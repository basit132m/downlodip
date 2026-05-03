const axios = require('axios');
const { URL } = require('url');

const FLARESOLVERR = process.env.FLARESOLVERR_URL || 'http://localhost:8191/v1';

async function resolveLink(campaign, ip) {
  let config;
  try {
    config = typeof campaign.resolver_config === 'string'
      ? JSON.parse(campaign.resolver_config)
      : campaign.resolver_config;
  } catch {
    throw new Error('Invalid resolver_config JSON');
  }

  switch (campaign.resolver_type) {
    case 'follow_redirect': return resolveFollowRedirect(config, ip);
    case 'url_template':    return { url: await resolveUrlTemplate(config, ip), cookies: [], userAgent: null };
    case 'api_fetch':       return { url: await resolveApiFetch(config, ip), cookies: [], userAgent: null };
    case 'regex_scrape':    return { url: await resolveRegexScrape(config, ip), cookies: [], userAgent: null };
    case 'static':
      if (!config.url) throw new Error('static resolver requires config.url');
      return { url: config.url, cookies: [], userAgent: null };
    default:
      throw new Error(`Unknown resolver_type: ${campaign.resolver_type}`);
  }
}

/**
 * follow_redirect:
 * 1. Uses FlareSolverr to load the download page and get session cookies + hidden fields.
 * 2. POSTs to admin-ajax.php (same session) with action=k_get_download + hidden fields.
 * 3. If a token URL is returned, redirects user there.
 * 4. Falls back to returning the source URL so the user's own browser generates the token.
 */
async function resolveFollowRedirect(config, ip) {
  if (!config.url) throw new Error('follow_redirect resolver requires config.url');

  // Try server-side token generation via FlareSolverr session
  try {
    const result = await resolveViaFlareSolverr(config.url, ip);
    if (result) return result;
  } catch (err) {
    console.warn('[follow_redirect] FlareSolverr attempt failed:', err.message);
  }

  // Fallback: send the user to the source URL; their browser will generate the token
  console.log('[follow_redirect] Falling back to direct redirect for user-side token generation');
  return { url: config.url, cookies: [], userAgent: null };
}

async function resolveViaFlareSolverr(pageUrl, ip) {
  const parsedPage = new URL(pageUrl);
  const ajaxUrl = `${parsedPage.protocol}//${parsedPage.host}/wp-admin/admin-ajax.php`;

  // Step 1: Load the page through FlareSolverr to get cf_clearance + page HTML
  console.log(`[FlareSolverr] Loading page: ${pageUrl}`);
  const pageResp = await axios.post(FLARESOLVERR, {
    cmd: 'request.get',
    url: pageUrl,
    maxTimeout: 30000,
  }, { timeout: 35000 });

  if (pageResp.data.status !== 'ok') {
    throw new Error(`FlareSolverr page load failed: ${pageResp.data.message}`);
  }

  const html = pageResp.data.solution.response;
  const cookies = pageResp.data.solution.cookies || [];
  const userAgent = pageResp.data.solution.userAgent;

  console.log(`[FlareSolverr] Got ${cookies.length} cookies, UA: ${userAgent ? userAgent.slice(0, 40) : 'none'}`);

  // Step 2: Extract hidden fields from the page HTML
  const postIdMatch = html.match(/name=["']post_id["'][^>]*value=["'](\d+)["']/i)
    || html.match(/value=["'](\d+)["'][^>]*name=["']post_id["']/i);
  const refererMatch = html.match(/name=["']_wp_http_referer["'][^>]*value=["']([^"']+)["']/i)
    || html.match(/value=["']([^"']+)["'][^>]*name=["']_wp_http_referer["']/i);

  const postId = postIdMatch ? postIdMatch[1] : null;
  const wpReferer = refererMatch ? refererMatch[1] : parsedPage.pathname;

  console.log(`[FlareSolverr] Extracted post_id=${postId}, _wp_http_referer=${wpReferer}`);

  // Step 3: Check if the fresh token was already injected into the page HTML
  // (some pages render the token server-side after JS execution)
  const tokenInPage = scrapeDownloadUrl(html);
  if (tokenInPage) {
    console.log(`[FlareSolverr] Found token URL in page HTML: ${tokenInPage}`);
    return { url: tokenInPage, cookies, userAgent };
  }

  // Step 4: POST to admin-ajax.php using the FlareSolverr session cookies
  const cookieHeader = cookies.map(c => `${c.name}=${c.value}`).join('; ');
  const postBody = new URLSearchParams({ action: 'k_get_download' });
  if (postId) postBody.set('post_id', postId);
  if (wpReferer) postBody.set('_wp_http_referer', wpReferer);

  console.log(`[FlareSolverr] POSTing to ${ajaxUrl} with body: ${postBody.toString()}`);

  const ajaxResp = await axios.post(ajaxUrl, postBody.toString(), {
    timeout: 15000,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      'User-Agent': userAgent || 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
      'Referer': pageUrl,
      'Origin': `${parsedPage.protocol}//${parsedPage.host}`,
      'X-Requested-With': 'XMLHttpRequest',
      'Cookie': cookieHeader,
    },
  });

  console.log(`[FlareSolverr] admin-ajax.php response:`, JSON.stringify(ajaxResp.data).slice(0, 300));

  // Response should contain the download link HTML or a URL
  const ajaxText = typeof ajaxResp.data === 'string' ? ajaxResp.data : JSON.stringify(ajaxResp.data);
  const tokenUrl = scrapeDownloadUrl(ajaxText);
  if (tokenUrl) {
    console.log(`[FlareSolverr] Extracted token URL: ${tokenUrl}`);
    return { url: tokenUrl, cookies, userAgent };
  }

  // Try parsing as JSON with a url/link field
  if (typeof ajaxResp.data === 'object' && (ajaxResp.data.url || ajaxResp.data.link || ajaxResp.data.data)) {
    const url = ajaxResp.data.url || ajaxResp.data.link || ajaxResp.data.data;
    if (typeof url === 'string' && url.startsWith('http')) {
      console.log(`[FlareSolverr] Got URL from JSON response: ${url}`);
      return { url, cookies, userAgent };
    }
  }

  console.warn('[FlareSolverr] Could not extract a download URL from admin-ajax.php response');
  return null;
}


// Matches direct file download URLs (.zip, .iso, .rar, etc.) with optional token
const DOWNLOAD_PATTERNS = [
  /https?:\/\/[^\s"'<>]+\.(?:zip|iso|rar|7z|exe|pkg|xci|nsp|apk|bin|img)(?:\?[^\s"'<>]*)?/gi,
];

function scrapeDownloadUrl(html, customPattern) {
  if (customPattern) {
    const m = html.match(new RegExp(customPattern));
    if (m) return m[1] || m[0];
  }
  for (const pattern of DOWNLOAD_PATTERNS) {
    pattern.lastIndex = 0;
    const m = pattern.exec(html);
    if (m) return m[0].replace(/['">\s]+$/, '');
  }
  return null;
}

function resolveUrlTemplate(config, ip) {
  if (!config.template) throw new Error('url_template resolver requires config.template');
  return config.template.replace(/\{ip\}/g, encodeURIComponent(ip));
}

async function resolveApiFetch(config, ip) {
  if (!config.url) throw new Error('api_fetch resolver requires config.url');
  const ipField = config.ip_field || 'ip';
  const responseField = config.response_field || 'url';
  const method = (config.method || 'GET').toUpperCase();
  const extra = config.extra_params || {};
  const params = { [ipField]: ip, ...extra };
  const url = config.url.replace(/\{ip\}/g, encodeURIComponent(ip));
  let response;
  if (method === 'POST') {
    response = await axios.post(url, params, { timeout: 10000 });
  } else {
    response = await axios.get(url, { params, timeout: 10000 });
  }
  const resolved = response.data[responseField];
  if (!resolved) throw new Error(`Response field "${responseField}" not found in API response`);
  return resolved;
}

async function resolveRegexScrape(config, ip) {
  if (!config.url || !config.regex) throw new Error('regex_scrape resolver requires config.url and config.regex');
  const url = config.url.replace(/\{ip\}/g, encodeURIComponent(ip));
  const response = await axios.get(url, {
    timeout: 12000,
    headers: {
      'X-Forwarded-For': ip,
      'X-Real-IP': ip,
      'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    },
  });
  const html = typeof response.data === 'string' ? response.data : JSON.stringify(response.data);
  const match = html.match(new RegExp(config.regex));
  if (!match || !match[1]) throw new Error('Regex did not match any URL in the response');
  return match[1];
}

module.exports = { resolveLink };
