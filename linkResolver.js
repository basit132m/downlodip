const axios = require('axios');

/**
 * Resolves an IP-specific download/redirect URL based on the campaign's resolver type.
 *
 * Supported resolver types:
 *
 *  1. "url_template"
 *     Replace {ip} in a URL template with the visitor's IP.
 *     config: { "template": "https://example.com/getlink?ip={ip}&token=abc" }
 *
 *  2. "api_fetch"
 *     POST or GET to an API that returns a JSON body containing the link.
 *     config: {
 *       "url": "https://api.example.com/generate",
 *       "method": "POST",           // optional, default GET
 *       "ip_field": "user_ip",      // field name to send the IP as
 *       "response_field": "link",   // JSON field to read the URL from
 *       "extra_params": {}          // optional extra fields merged into request
 *     }
 *
 *  3. "regex_scrape"
 *     Fetch a URL (with {ip} substituted) and extract a link using a regex.
 *     config: {
 *       "url": "https://example.com/page?ip={ip}",
 *       "regex": "href=\"(https://download\\.example\\.com/[^\"]+)\""
 *     }
 *
 *  4. "static"
 *     Always returns the same URL regardless of IP.
 *     Useful for campaigns where the link doesn't need IP binding.
 *     config: { "url": "https://example.com/static-link" }
 */
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

function resolveUrlTemplate(config, ip) {
  if (!config.template) throw new Error('url_template resolver requires config.template');
  const url = config.template.replace(/\{ip\}/g, encodeURIComponent(ip));
  return url;
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

  const data = response.data;
  const resolved = data[responseField];
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
  const regex = new RegExp(config.regex);
  const match = html.match(regex);

  if (!match || !match[1]) throw new Error('Regex did not match any URL in the response');
  return match[1];
}

module.exports = { resolveLink };
