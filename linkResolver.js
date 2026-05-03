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
    case 'url_template':    return resolveUrlTemplate(config, ip);
    case 'api_fetch':       return resolveApiFetch(config, ip);
    case 'regex_scrape':    return resolveRegexScrape(config, ip);
    case 'static':
      if (!config.url) throw new Error('static resolver requires config.url');
      return config.url;
    default:
      throw new Error(`Unknown resolver_type: ${campaign.resolver_type}`);
  }
}

async function resolveFollowRedirect(config, ip) {
  if (!config.url) throw new Error('follow_redirect resolver requires config.url');

  let html = null;
  let finalUrl = config.url;

  // --- Try FlareSolverr first ---
  try {
    const response = await axios.post(FLARESOLVERR, {
      cmd: 'request.get',
      url: config.url,
      maxTimeout: 60000,
      headers: {
        'X-Forwarded-For': ip,
        'X-Real-IP': ip,
      },
    }, { timeout: 70000 });

    if (response.data && response.data.solution) {
      html = response.data.solution.response;
      finalUrl = response.data.solution.url || config.url;
      console.log(`[FlareSolverr] Fetched ${finalUrl} for IP ${ip}`);
    }
  } catch (err) {
    console.warn(`[FlareSolverr] Failed: ${err.message} — falling back to plain fetch`);
  }

  // --- Fallback: plain HTTP with IP headers ---
  if (!html) {
    try {
      const res = await axios.get(config.url, {
        timeout: 15000,
        maxRedirects: 10,
        headers: {
          'X-Forwarded-For': ip,
          'X-Real-IP': ip,
          'CF-Connecting-IP': ip,
          'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
          'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
      });
      html = typeof res.data === 'string' ? res.data : JSON.stringify(res.data);
      finalUrl = res.request?.res?.responseUrl || config.url;
    } catch (err) {
      console.warn(`[Plain fetch] Failed: ${err.message}`);
    }
  }

  // --- Scrape download URL from HTML ---
  if (html) {
    const downloadUrl = scrapeDownloadUrl(html, config.scrape_pattern);
    if (downloadUrl) {
      console.log(`[Scraper] Found download URL: ${downloadUrl}`);
      return downloadUrl;
    }
    console.warn('[Scraper] No download URL found in HTML');
  }

  return finalUrl;
}

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
    if (m) return m[0].replace(/['">,\s]+$/, '');
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
