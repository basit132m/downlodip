async function quickCreate() {
  const name = document.getElementById('q-name').value.trim();
  const url  = document.getElementById('q-url').value.trim();
  const use_lander = document.getElementById('q-lander').checked;

  if (!name) { alert('Please enter a link name'); return; }
  if (!url)  { alert('Please enter a destination URL'); return; }
  try { new URL(url); } catch { alert('Please enter a valid URL (include https://)'); return; }

  const res = await fetch('/admin/api/campaigns', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      name,
      resolver_type: 'static',
      resolver_config: JSON.stringify({ url }),
      use_lander,
      lander_title: name,
      lander_description: 'Click the button below to get your download link.',
    }),
  });

  if (!res.ok) {
    const err = await res.json();
    alert(err.error || 'Failed to create link');
    return;
  }

  const campaign = await res.json();
  const shareLink = `${location.origin}/r/${campaign.slug}`;

  document.getElementById('quick-link-url').textContent = shareLink;
  document.getElementById('quick-result').classList.remove('hidden');
  document.getElementById('q-name').value = '';
  document.getElementById('q-url').value = '';
  document.getElementById('q-lander').checked = false;

  loadCampaigns();
}

function copyQuickLink() {
  const url = document.getElementById('quick-link-url').textContent;
  navigator.clipboard.writeText(url).then(() => {
    const btn = document.querySelector('#quick-result .btn-ghost');
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 1500);
  });
}

async function loadCampaigns() {
  const res = await fetch('/admin/api/campaigns');
  if (!res.ok) { document.getElementById('campaigns-list').innerHTML = '<p class="muted">Failed to load.</p>'; return; }
  renderCampaigns(await res.json());
}

function renderCampaigns(campaigns) {
  const el = document.getElementById('campaigns-list');
  if (!campaigns.length) { el.innerHTML = '<p class="muted">No links yet. Create one above.</p>'; return; }

  const rows = campaigns.map(c => {
    const link = `${location.origin}/r/${c.slug}`;
    return `<tr>
      <td><strong>${esc(c.name)}</strong></td>
      <td><span class="slug-badge">${esc(c.slug)}</span></td>
      <td class="dest-url">${esc(destUrl(c))}</td>
      <td>${c.use_lander ? '<span class="tag tag-green">Lander</span>' : '<span class="tag tag-gray">Direct</span>'}</td>
      <td><span class="copy-link" onclick="copyText('${esc(link)}')" title="Click to copy">${esc(link)}</span></td>
      <td><div class="actions">
        <button class="btn-ghost" onclick="showStats(${c.id}, '${esc(c.name)}')">Stats</button>
        <button class="btn-danger" onclick="deleteCampaign(${c.id})">Delete</button>
      </div></td>
    </tr>`;
  }).join('');

  el.innerHTML = `<table class="campaign-table">
    <thead><tr><th>Name</th><th>Slug</th><th>Destination</th><th>Mode</th><th>Share Link</th><th>Actions</th></tr></thead>
    <tbody>${rows}</tbody>
  </table>`;
}

function destUrl(c) {
  try { const cfg = JSON.parse(c.resolver_config); return cfg.url || cfg.template || c.resolver_type; }
  catch { return c.resolver_type; }
}

async function deleteCampaign(id) {
  if (!confirm('Delete this link? All visit stats will be lost.')) return;
  await fetch(`/admin/api/campaigns/${id}`, { method: 'DELETE' });
  loadCampaigns();
}

async function showStats(id, name) {
  const stats = await (await fetch(`/admin/api/campaigns/${id}/stats`)).json();
  document.getElementById('stats-title').textContent = `Stats — ${name}`;
  const rows = stats.recent.map(v => `<tr><td>${esc(v.ip)}</td><td>${esc(v.resolved_url || '—')}</td><td>${esc(v.visited_at)}</td></tr>`).join('');
  document.getElementById('stats-content').innerHTML = `
    <div class="stats-numbers">
      <div class="stat-box"><div class="val">${stats.total}</div><div class="lbl">Total Visits</div></div>
      <div class="stat-box"><div class="val">${stats.resolved}</div><div class="lbl">Redirected</div></div>
    </div>
    ${rows ? `<table class="visit-table"><thead><tr><th>IP</th><th>Destination</th><th>Time</th></tr></thead><tbody>${rows}</tbody></table>` : '<p class="muted">No visits yet.</p>'}`;
  document.getElementById('stats-modal').classList.remove('hidden');
}

function closeStats() { document.getElementById('stats-modal').classList.add('hidden'); }

function copyText(url) {
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
