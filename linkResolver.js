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
    case 'follow_redirect':
      if (!config.url) throw new Error('follow_redirect resolver requires config.url');
      // The user's own browser must visit this URL — their real residential IP causes
      // the target site to generate a fresh IP-specific download token automatically.
      // Server-side generation is impossible: romsfun.com blocks all datacenter IPs at
      // the PHP level, and TCP source IPs cannot be spoofed regardless of tool used.
      return { url: config.url, cookies: [], userAgent: null };

    case 'url_template':
      if (!config.template) throw new Error('url_template resolver requires config.template');
      return { url: config.template.replace(/\{ip\}/g, encodeURIComponent(ip)), cookies: [], userAgent: null };

    case 'api_fetch': {
      if (!config.url) throw new Error('api_fetch resolver requires config.url');
      const ipField = config.ip_field || 'ip';
      const responseField = config.response_field || 'url';
      const method = (config.method || 'GET').toUpperCase();
      const extra = config.extra_params || {};
      const params = { [ipField]: ip, ...extra };
      const url = config.url.replace(/\{ip\}/g, encodeURIComponent(ip));
      const response = method === 'POST'
        ? await axios.post(url, params, { timeout: 10000 })
        : await axios.get(url, { params, timeout: 10000 });
      const resolved = response.data[responseField];
      if (!resolved) throw new Error(`Response field "${responseField}" not found in API response`);
      return { url: resolved, cookies: [], userAgent: null };
    }

    case 'regex_scrape': {
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
      return { url: match[1], cookies: [], userAgent: null };
    }

    case 'static':
      if (!config.url) throw new Error('static resolver requires config.url');
      return { url: config.url, cookies: [], userAgent: null };

    default:
      throw new Error(`Unknown resolver_type: ${campaign.resolver_type}`);
  }
}

module.exports = { resolveLink };
