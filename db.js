const initSqlJs = require('sql.js');
const fs = require('fs');
const path = require('path');
const { nanoid } = require('nanoid');

const DB_PATH = process.env.DB_PATH || path.join(__dirname, 'data.db');

let db;

const ready = initSqlJs().then(SQL => {
  if (fs.existsSync(DB_PATH)) {
    db = new SQL.Database(fs.readFileSync(DB_PATH));
  } else {
    db = new SQL.Database();
  }

  db.run(`
    CREATE TABLE IF NOT EXISTS campaigns (
      id          INTEGER PRIMARY KEY AUTOINCREMENT,
      slug        TEXT    NOT NULL UNIQUE,
      name        TEXT    NOT NULL,
      resolver_type   TEXT NOT NULL,
      resolver_config TEXT NOT NULL,
      use_lander  INTEGER NOT NULL DEFAULT 0,
      lander_title       TEXT DEFAULT '',
      lander_description TEXT DEFAULT '',
      created_at  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%SZ','now'))
    );
    CREATE TABLE IF NOT EXISTS visits (
      id          INTEGER PRIMARY KEY AUTOINCREMENT,
      campaign_id INTEGER NOT NULL,
      ip          TEXT NOT NULL,
      user_agent  TEXT,
      resolved_url TEXT,
      visited_at  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%SZ','now')),
      FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
    );
  `);

  persist();
});

function persist() {
  const data = db.export();
  fs.writeFileSync(DB_PATH, Buffer.from(data));
}

function run(sql, params = []) {
  db.run(sql, params);
  const rowid = getLastRowid();
  persist();
  return rowid;
}

function getLastRowid() {
  const stmt = db.prepare('SELECT last_insert_rowid() as id');
  stmt.step();
  const id = stmt.getAsObject().id;
  stmt.free();
  return id;
}

function get(sql, params = []) {
  const stmt = db.prepare(sql);
  stmt.bind(params);
  const found = stmt.step() ? stmt.getAsObject() : null;
  stmt.free();
  return found;
}

function all(sql, params = []) {
  const results = [];
  const stmt = db.prepare(sql);
  stmt.bind(params);
  while (stmt.step()) results.push(stmt.getAsObject());
  stmt.free();
  return results;
}

function createCampaign(data) {
  const slug = nanoid(8);
  const rowid = run(
    `INSERT INTO campaigns (slug, name, resolver_type, resolver_config, use_lander, lander_title, lander_description)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [slug, data.name, data.resolver_type, data.resolver_config,
     data.use_lander ? 1 : 0, data.lander_title || '', data.lander_description || '']
  );
  return getCampaignById(rowid);
}

function getCampaign(slug) {
  return get('SELECT * FROM campaigns WHERE slug = ?', [slug]);
}

function getCampaignById(id) {
  return get('SELECT * FROM campaigns WHERE id = ?', [id]);
}

function getAllCampaigns() {
  return all('SELECT * FROM campaigns ORDER BY id DESC');
}

function updateCampaign(id, data) {
  const allowed = ['name', 'resolver_type', 'resolver_config', 'use_lander', 'lander_title', 'lander_description'];
  const fields = Object.keys(data).filter(k => allowed.includes(k));
  if (!fields.length) return getCampaignById(id);
  const set = fields.map(f => `${f} = ?`).join(', ');
  const values = fields.map(f => data[f]);
  run(`UPDATE campaigns SET ${set} WHERE id = ?`, [...values, id]);
  return getCampaignById(id);
}

function deleteCampaign(id) {
  run('DELETE FROM visits WHERE campaign_id = ?', [id]);
  run('DELETE FROM campaigns WHERE id = ?', [id]);
}

function logVisit(campaignId, ip, userAgent) {
  run('INSERT INTO visits (campaign_id, ip, user_agent) VALUES (?, ?, ?)', [campaignId, ip, userAgent]);
}

function markResolved(campaignId, ip, resolvedUrl) {
  const visit = get(
    'SELECT id FROM visits WHERE campaign_id = ? AND ip = ? AND resolved_url IS NULL ORDER BY id DESC LIMIT 1',
    [campaignId, ip]
  );
  if (visit) run('UPDATE visits SET resolved_url = ? WHERE id = ?', [resolvedUrl, visit.id]);
}

function getStats(campaignId) {
  const total    = (get('SELECT COUNT(*) as c FROM visits WHERE campaign_id = ?', [campaignId]) || {}).c || 0;
  const resolved = (get('SELECT COUNT(*) as c FROM visits WHERE campaign_id = ? AND resolved_url IS NOT NULL', [campaignId]) || {}).c || 0;
  const recent   = all(
    'SELECT ip, user_agent, resolved_url, visited_at FROM visits WHERE campaign_id = ? ORDER BY id DESC LIMIT 50',
    [campaignId]
  );
  return { total, resolved, recent };
}

module.exports = {
  ready,
  createCampaign, getCampaign, getCampaignById, getAllCampaigns,
  updateCampaign, deleteCampaign, logVisit, markResolved, getStats,
};
