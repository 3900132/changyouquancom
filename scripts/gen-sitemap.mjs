import fs from 'node:fs';
const BASE = 'https://changyouquan.com';
const LANGS = ['zh', 'zh-TW', 'en', 'es', 'fr', 'de', 'ja', 'ko'];
const PAGES = [['index', '/', 1.0, 'weekly'], ['help', '/help.html', 0.8, 'monthly'], ['about', '/about.html', 0.6, 'yearly']];
const url = (lang, p) => lang === 'zh' ? (p === '/' ? BASE + '/' : BASE + p) : BASE + '/' + lang + (p === '/' ? '/' : p);
const esc = s => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
const cluster = (p) => LANGS.map(l => `    <xhtml:link rel="alternate" hreflang="${esc(l)}" href="${url(l, p)}"/>`).join('\n')
  + `\n    <xhtml:link rel="alternate" hreflang="x-default" href="${url('zh', p)}"/>`;
let out = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">\n`;
for (const [page, path, pri, freq] of PAGES) {
  for (const lang of LANGS) {
    out += `  <url>\n    <loc>${url(lang, path)}</loc>\n    <lastmod>2026-09-21</lastmod>\n    <changefreq>${freq}</changefreq>\n    <priority>${lang === 'zh' ? pri : pri - 0.1}</priority>\n${cluster(path)}\n  </url>\n`;
  }
}
out += `</urlset>\n`;
fs.writeFileSync('E:/github/changyouquancom/sitemap.xml', out);
console.log('written, urls:', (out.match(/<loc>/g) || []).length);
