const axios = require('axios');
const https = require('https');
const http = require('http');
const { URL } = require('url');

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
    case 'follow_redirect':
      return resolveFollowRedirect(config, ip);
    case 'url_template':
      return resolveUrlTemplate(config, ip);
    case 'api_fetch':
      return resolveApiFetch(config, ip);
    case 'regex_scrape':
      return resolveRegexScrape(config, ip);
    case 'static':
      if (!config.url) throw new Error('static resolver requires config.url');
      return config.url;
    default:
      throw new Error(`Unknown resolver_type: ${campaign.resolver_type}`);
  }
}

/**
 * follow_redirect: Visit the source URL with the visitor's IP in headers,
 * follow all redirects manually, and return the final URL.
 * This makes romsfun (and similar sites) generate an IP-specific token
 * for the visitor's IP rather than our server's IP.
 */
async function resolveFollowRedirect(config, ip) {
  if (!config.url) throw new Error('follow_redirect resolver requires config.url');

  const MAX_REDIRECTS = 12;
  let currentUrl = config.url;

  for (let i = 0; i < MAX_REDIRECTS; i++) {
    const parsed = new URL(currentUrl);
    const client = parsed.protocol === 'https:' ? https : http;

    const location = await new Promise((resolve, reject) => {
      const options = {
        hostname: parsed.hostname,
        port: parsed.port || (parsed.protocol === 'https:' ? 443 : 80),
        path: parsed.pathname + parsed.search,
        method: 'GET',
        headers: {
          'X-Forwarded-For': ip,
          'X-Real-IP': ip,
          'CF-Connecting-IP': ip,
          'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
          'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Accept-Language': 'en-US,en;q=0.5',
          'Referer': `${parsed.protocol}//${parsed.hostname}/`,
        },
      };

      const req = client.request(options, (res) => {
        res.resume(); // drain body
        if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
          const loc = res.headers.location;
          // Resolve relative redirects
          const next = loc.startsWith('http') ? loc : new URL(loc, currentUrl).href;
          resolve(next);
        } else {
          resolve(null); // no more redirects
        }
      });

      req.on('error', reject);
      req.setTimeout(12000, () => { req.destroy(); reject(new Error('Request timed out')); });
      req.end();
    });

    if (!location) break; // reached final URL
    currentUrl = location;
  }

  return currentUrl;
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
    timeout: 10000,
    headers: { 'User-Agent': 'Mozilla/5.0 (compatible; Googlebot/2.1)' },
  });
  const html = typeof response.data === 'string' ? response.data : JSON.stringify(response.data);
  const match = html.match(new RegExp(config.regex));
  if (!match || !match[1]) throw new Error('Regex did not match any URL in the response');
  return match[1];
}

module.exports = { resolveLink };
