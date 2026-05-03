const https = require('https');
const http = require('http');
const { URL } = require('url');
const axios = require('axios');

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

/**
 * follow_redirect:
 * 1. Follow HTTP redirects with visitor's IP spoofed in headers.
 * 2. If the final page is HTML (no more redirects), scrape it for a
 *    download URL (.zip / .iso / .rar / .7z / .exe with optional ?token=...).
 * This handles sites like romsfun that embed an IP-locked token URL
 * in the page HTML behind a JS timer.
 */
async function resolveFollowRedirect(config, ip) {
  if (!config.url) throw new Error('follow_redirect resolver requires config.url');

  const BROWSER_HEADERS = {
    'X-Forwarded-For': ip,
    'X-Real-IP': ip,
    'CF-Connecting-IP': ip,
    'True-Client-IP': ip,
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language': 'en-US,en;q=0.5',
  };

  // Step 1: follow HTTP redirects manually
  const MAX_REDIRECTS = 12;
  let currentUrl = config.url;
  let lastBody = null;
  let lastStatus = null;

  for (let i = 0; i < MAX_REDIRECTS; i++) {
    const parsed = new URL(currentUrl);
    const client = parsed.protocol === 'https:' ? https : http;

    const { location, status, body } = await new Promise((resolve, reject) => {
      const options = {
        hostname: parsed.hostname,
        port: parsed.port || (parsed.protocol === 'https:' ? 443 : 80),
        path: parsed.pathname + parsed.search,
        method: 'GET',
        headers: { ...BROWSER_HEADERS, 'Referer': `${parsed.protocol}//${parsed.hostname}/` },
      };

      let rawBody = '';
      const req = client.request(options, (res) => {
        res.setEncoding('utf8');
        res.on('data', chunk => { if (rawBody.length < 500000) rawBody += chunk; });
        res.on('end', () => {
          if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
            const loc = res.headers.location;
            const next = loc.startsWith('http') ? loc : new URL(loc, currentUrl).href;
            resolve({ location: next, status: res.statusCode, body: null });
          } else {
            resolve({ location: null, status: res.statusCode, body: rawBody });
          }
        });
      });

      req.on('error', reject);
      req.setTimeout(15000, () => { req.destroy(); reject(new Error('Request timed out')); });
      req.end();
    });

    lastStatus = status;
    if (!location) { lastBody = body; break; }
    currentUrl = location;
  }

  // Step 2: if we landed on an HTML page, scrape the download URL from it
  if (lastBody) {
    const downloadUrl = scrapeDownloadUrl(lastBody, config.scrape_pattern);
    if (downloadUrl) return downloadUrl;
    // If nothing found, fall back to returning the final page URL
    // (at least the user lands on the right page)
  }

  return currentUrl;
}

// Common file extensions and token patterns to look for
const DOWNLOAD_PATTERNS = [
  // href or src pointing to a file with optional query string
  /https?:\/\/[^\s"'<>]+\.(?:zip|iso|rar|7z|exe|pkg|xci|nsp|apk|bin|img)[^\s"'<>]*/gi,
];

function scrapeDownloadUrl(html, customPattern) {
  // Try custom pattern first if provided
  if (customPattern) {
    const m = html.match(new RegExp(customPattern));
    if (m) return m[1] || m[0];
  }

  // Try each built-in pattern
  for (const pattern of DOWNLOAD_PATTERNS) {
    pattern.lastIndex = 0;
    const m = pattern.exec(html);
    if (m) return m[0].replace(/['">\s]+$/, ''); // trim trailing junk
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
