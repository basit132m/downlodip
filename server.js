require('dotenv').config();
const express = require('express');
const rateLimit = require('express-rate-limit');
const path = require('path');
const db = require('./db');
const { resolveLink } = require('./linkResolver');

const app = express();
const PORT = process.env.PORT || 3000;
const ADMIN_SECRET = process.env.ADMIN_SECRET || 'changeme';

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));
app.set('trust proxy', 1);

const redirectLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 20,
  standardHeaders: true,
  legacyHeaders: false,
});

function getClientIp(req) {
  const forwarded = req.headers['x-forwarded-for'];
  if (forwarded) return forwarded.split(',')[0].trim();
  return req.ip || req.connection.remoteAddress;
}

function adminAuth(req, res, next) {
  const cookie = req.headers.cookie || '';
  const token = cookie.split(';').map(c => c.trim()).find(c => c.startsWith('admin_token='));
  if (token && token.split('=')[1] === ADMIN_SECRET) return next();
  const header = req.headers['x-admin-secret'];
  if (header === ADMIN_SECRET) return next();
  return res.status(401).json({ error: 'Unauthorized' });
}

function adminPageAuth(req, res, next) {
  const cookie = req.headers.cookie || '';
  const token = cookie.split(';').map(c => c.trim()).find(c => c.startsWith('admin_token='));
  if (token && token.split('=')[1] === ADMIN_SECRET) return next();
  return res.redirect('/admin/login');
}

app.get('/r/:slug', redirectLimiter, async (req, res) => {
  const { slug } = req.params;
  const campaign = db.getCampaign(slug);
  if (!campaign) return res.status(404).render('404');

  const ip = getClientIp(req);
  db.logVisit(campaign.id, ip, req.headers['user-agent'] || '');

  if (campaign.use_lander) {
    return res.render('lander', { campaign, slug });
  }

  try {
    const resolvedUrl = await resolveLink(campaign, ip);
    db.markResolved(campaign.id, ip, resolvedUrl);
    return res.redirect(302, resolvedUrl);
  } catch (err) {
    console.error('Resolution failed:', err.message);
    return res.render('lander', { campaign, slug, error: 'Link generation failed, please try again.' });
  }
});

app.post('/r/:slug/resolve', redirectLimiter, async (req, res) => {
  const { slug } = req.params;
  const campaign = db.getCampaign(slug);
  if (!campaign) return res.status(404).json({ error: 'Not found' });

  const ip = getClientIp(req);
  try {
    const resolvedUrl = await resolveLink(campaign, ip);
    db.markResolved(campaign.id, ip, resolvedUrl);
    return res.json({ url: resolvedUrl });
  } catch (err) {
    console.error('Resolution failed:', err.message);
    return res.status(500).json({ error: 'Could not generate link. Try again.' });
  }
});

app.get('/admin/login', (req, res) => res.render('login', { error: null }));

app.post('/admin/login', (req, res) => {
  const { secret } = req.body;
  if (secret === ADMIN_SECRET) {
    res.setHeader('Set-Cookie', `admin_token=${ADMIN_SECRET}; HttpOnly; Path=/; SameSite=Strict; Max-Age=86400`);
    return res.redirect('/admin');
  }
  res.render('login', { error: 'Wrong password. Try again.' });
});

app.get('/admin/logout', (req, res) => {
  res.setHeader('Set-Cookie', 'admin_token=; HttpOnly; Path=/; Max-Age=0');
  res.redirect('/admin/login');
});

app.get('/admin', adminPageAuth, (req, res) => res.render('admin'));

app.get('/admin/api/campaigns', adminAuth, (req, res) => {
  res.json(db.getAllCampaigns());
});

app.post('/admin/api/campaigns', adminAuth, (req, res) => {
  const { name, resolver_type, resolver_config, use_lander, lander_title, lander_description } = req.body;
  if (!name || !resolver_type || !resolver_config) {
    return res.status(400).json({ error: 'name, resolver_type, and resolver_config are required' });
  }
  const campaign = db.createCampaign({
    name,
    resolver_type,
    resolver_config: typeof resolver_config === 'string' ? resolver_config : JSON.stringify(resolver_config),
    use_lander: use_lander ? 1 : 0,
    lander_title: lander_title || name,
    lander_description: lander_description || '',
  });
  res.json(campaign);
});

app.put('/admin/api/campaigns/:id', adminAuth, (req, res) => {
  const campaign = db.updateCampaign(req.params.id, req.body);
  if (!campaign) return res.status(404).json({ error: 'Not found' });
  res.json(campaign);
});

app.delete('/admin/api/campaigns/:id', adminAuth, (req, res) => {
  db.deleteCampaign(req.params.id);
  res.json({ ok: true });
});

app.get('/admin/api/campaigns/:id/stats', adminAuth, (req, res) => {
  res.json(db.getStats(req.params.id));
});

db.ready.then(() => {
  app.listen(PORT, () => {
    console.log(`Downlodip running on http://localhost:${PORT}`);
    console.log(`Admin: http://localhost:${PORT}/admin`);
  });
}).catch(err => { console.error('DB init failed:', err); process.exit(1); });
