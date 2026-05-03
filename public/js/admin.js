const RESOLVER_HINTS = {
  url_template: `{"template":"https://example.com/dl?ip={ip}&token=YOUR_TOKEN"}`,
  api_fetch: `{"url":"https://api.example.com/link","method":"POST","ip_field":"user_ip","response_field":"download_url"}`,
  regex_scrape: `{"url":"https://example.com/page?ip={ip}","regex":"href=\\"(https://dl\\.example\\.com/[^\\"]+)\\""}`,
  static: `{"url":"https://example.com/static-download-link"}`,
};

function updateResolverHelp() {
  const type = document.getElementById('f-type').value;
  document.getElementById('resolver-hint').textContent = '— example config shown as placeholder';
  document.getElementById('f-config').placeholder = RESOLVER_HINTS[type] || '{}';
}

document.getElementById('f-lander').addEventListener('change', function () {
  document.getElementById('lander-fields').style.display = this.checked ? 'grid' : 'none';
});

updateResolverHelp();

function secret() { return document.getElementById('admin-secret').value.trim(); }
function headers() { return { 'Content-Type': 'application/json', 'x-admin-secret': secret() }; }

async function loadCampaigns() {
  const res = await fetch('/admin/api/campaigns', { headers: headers() });
  if (!res.ok) { alert('Wrong secret or server error'); return; }
  renderCampaigns(await res.json());
}

function renderCampaigns(campaigns) {
  const el = document.getElementById('campaigns-list');
  if (!campaigns.length) { el.innerHTML = '<p class="muted">No campaigns yet. Create one above.</p>'; return; }
  const rows = campaigns.map(c => {
    const link = `${location.origin}/r/${c.slug}`;
    return `<tr>
      <td><strong>${esc(c.name)}</strong></td>
      <td><span class="slug-badge">${esc(c.slug)}</span></td>
      <td>${tagForType(c.resolver_type)}</td>
      <td>${c.use_lander ? '<span class="tag tag-green">Lander</span>' : '<span class="tag tag-gray">Direct</span>'}</td>
      <td><span class="copy-link" onclick="copyLink('${esc(link)}')" title="Click to copy">${esc(link)}</span></td>
      <td><div class="actions">
        <button class="btn-ghost" onclick="showStats(${c.id}, '${esc(c.name)}')">Stats</button>
        <button class="btn-danger" onclick="deleteCampaign(${c.id})">Delete</button>
      </div></td>
    </tr>`;
  }).join('');
  el.innerHTML = `<table class="campaign-table"><thead><tr><th>Name</th><th>Slug</th><th>Type</th><th>Mode</th><th>Link</th><th>Actions</th></tr></thead><tbody>${rows}</tbody></table>`;
}

function tagForType(type) {
  const map = { url_template: 'tag-blue', api_fetch: 'tag-yellow', regex_scrape: 'tag-yellow', static: 'tag-gray' };
  const cls = map[type] || 'tag-gray';
  const label = { url_template: 'URL Template', api_fetch: 'API Fetch', regex_scrape: 'Regex Scrape', static: 'Static' };
  return `<span class="tag ${cls}">${label[type] || esc(type)}</span>`;
}

async function createCampaign() {
  const name = document.getElementById('f-name').value.trim();
  const resolver_type = document.getElementById('f-type').value;
  const configRaw = document.getElementById('f-config').value.trim();
  const use_lander = document.getElementById('f-lander').checked;
  const lander_title = document.getElementById('f-lander-title').value.trim();
  const lander_description = document.getElementById('f-lander-desc').value.trim();
  if (!name) { alert('Campaign name is required'); return; }
  if (!configRaw) { alert('Resolver config is required'); return; }
  try { JSON.parse(configRaw); } catch { alert('Resolver config must be valid JSON'); return; }
  const res = await fetch('/admin/api/campaigns', {
    method: 'POST', headers: headers(),
    body: JSON.stringify({ name, resolver_type, resolver_config: configRaw, use_lander, lander_title, lander_description }),
  });
  if (!res.ok) { alert((await res.json()).error || 'Failed'); return; }
  document.getElementById('f-name').value = '';
  document.getElementById('f-config').value = '';
  document.getElementById('f-lander').checked = false;
  document.getElementById('lander-fields').style.display = 'none';
  loadCampaigns();
}

async function deleteCampaign(id) {
  if (!confirm('Delete this campaign? All visit stats will be lost.')) return;
  await fetch(`/admin/api/campaigns/${id}`, { method: 'DELETE', headers: headers() });
  loadCampaigns();
}

async function showStats(id, name) {
  const stats = await (await fetch(`/admin/api/campaigns/${id}/stats`, { headers: headers() })).json();
  document.getElementById('stats-title').textContent = `Stats — ${name}`;
  const rows = stats.recent.map(v => `<tr><td>${esc(v.ip)}</td><td>${esc(v.resolved_url || '—')}</td><td>${esc(v.visited_at)}</td></tr>`).join('');
  document.getElementById('stats-content').innerHTML = `
    <div class="stats-numbers">
      <div class="stat-box"><div class="val">${stats.total}</div><div class="lbl">Total Visits</div></div>
      <div class="stat-box"><div class="val">${stats.resolved}</div><div class="lbl">Links Resolved</div></div>
      <div class="stat-box"><div class="val">${stats.total ? Math.round(stats.resolved/stats.total*100) : 0}%</div><div class="lbl">Success Rate</div></div>
    </div>
    ${rows ? `<table class="visit-table"><thead><tr><th>IP</th><th>Resolved URL</th><th>Time</th></tr></thead><tbody>${rows}</tbody></table>` : '<p class="muted">No visits yet.</p>'}`;
  document.getElementById('stats-modal').classList.remove('hidden');
}

function closeStats() { document.getElementById('stats-modal').classList.add('hidden'); }

function copyLink(url) {
  navigator.clipboard.writeText(url).then(() => {
    const el = document.querySelector(`.copy-link[onclick*="${url.slice(-8)}"]`);
    if (el) { const orig = el.textContent; el.textContent = 'Copied!'; setTimeout(() => el.textContent = orig, 1500); }
  });
}

function esc(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('stats-modal').addEventListener('click', function(e) { if (e.target === this) closeStats(); });
loadCampaigns();
