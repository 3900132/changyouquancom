var i18n = (function () {
  let strings = {};
  let fallbackStrings = {};
  let current = 'zh';
  const supported = ['zh', 'zh-TW', 'en', 'es', 'fr', 'de', 'ja', 'ko'];

  function normalizeLang(code) {
    const lc = (code || '').toLowerCase();
    if (lc.startsWith('zh-tw') || lc.includes('hant')) return 'zh-TW';
    if (lc.startsWith('zh')) return 'zh';
    if (lc.startsWith('ja')) return 'ja';
    if (lc.startsWith('ko')) return 'ko';
    if (lc.startsWith('es')) return 'es';
    if (lc.startsWith('fr')) return 'fr';
    if (lc.startsWith('de')) return 'de';
    return 'en';
  }

  async function load(lang) {
    const res = await fetch('/i18n/locales/' + lang + '.json', { cache: 'no-cache' });
    strings = await res.json();
  }

  async function loadFallback() {
    try {
      const res = await fetch('/i18n/locales/zh.json', { cache: 'no-cache' });
      fallbackStrings = await res.json();
    } catch (e) {
      fallbackStrings = {};
    }
  }

  function lookup(key) {
    return key.split('.').reduce((o, k) => (o && o[k] !== undefined ? o[k] : undefined), strings);
  }
  function t(key) {
    let val = lookup(key);
    if (typeof val === 'string') return val;
    if (fallbackStrings && typeof fallbackStrings === 'object') {
      val = key.split('.').reduce((o, k) => (o && o[k] !== undefined ? o[k] : undefined), fallbackStrings);
      if (typeof val === 'string') return val;
    }
    return key;
  }
  function getArray(key) {
    let val = lookup(key);
    if (!Array.isArray(val) && fallbackStrings) {
      val = key.split('.').reduce((o, k) => (o && o[k] !== undefined ? o[k] : undefined), fallbackStrings);
    }
    return Array.isArray(val) ? val : [];
  }

  const OG_LOCALES = { 'zh': 'zh_CN', 'zh-TW': 'zh_TW', 'en': 'en_US', 'es': 'es_ES', 'fr': 'fr_FR', 'de': 'de_DE', 'ja': 'ja_JP', 'ko': 'ko_KR' };

  // 多语言 SEO/GEO：按当前语言同步更新 head 中的 SEO 相关标签。
  // 说明：本站为纯静态单文件架构，各语言 URL（如 /en/）由服务端重写到同一 HTML，
  // 因此在客户端语言确定后动态本地化 canonical / OG / Twitter / JSON-LD 标签。
  function setMeta(selector, attr, value) {
    let el = document.head.querySelector(selector);
    if (!el) return;
    el.setAttribute(attr, value);
  }

  function updateSEO(lang) {
    // 剥离路径中已有的语言前缀，避免重复拼接
    let page = (window.location.pathname || '/').replace(/^\/(zh-TW|zh|en|es|fr|de|ja|ko)(?=\/)/, '');
    page = page.replace(/\/+index\.html$/, '/');
    const isHome = /\/$/.test(page) || page === '';
    const self = 'https://changyouquan.com' + (lang === 'zh'
      ? (isHome ? '/' : page)
      : (isHome ? '/' + lang + '/' : '/' + lang + page));
    const title = t('app.title');
    const desc = t('meta.description');

    document.documentElement.lang = lang;
    if (title && title !== 'app.title') document.title = title;
    setMeta('link[rel="canonical"]', 'href', self);
    setMeta('meta[property="og:url"]', 'content', self);
    setMeta('meta[property="og:locale"]', 'content', OG_LOCALES[lang] || 'zh_CN');
    if (title && title !== 'app.title') {
      setMeta('meta[property="og:title"]', 'content', title);
      setMeta('meta[name="twitter:title"]', 'content', title);
      setMeta('meta[name="twitter:description"]', 'content', desc);
    }
    setMeta('meta[property="og:description"]', 'content', desc);
    setMeta('meta[name="twitter:image"]', 'content', 'https://changyouquan.com/logo.png');

    // JSON-LD：更新 url 与 inLanguage
    try {
      document.querySelectorAll('script[type="application/ld+json"]').forEach((s) => {
        const data = JSON.parse(s.textContent);
        let changed = false;
        if (typeof data.url === 'string' && data.url.startsWith('https://changyouquan.com')) { data.url = self; changed = true; }
        if (Array.isArray(data.inLanguage)) { data.inLanguage = [lang]; changed = true; }
        else if (typeof data.inLanguage === 'string') { data.inLanguage = lang; changed = true; }
        if (data.isPartOf && typeof data.isPartOf.url === 'string') { data.isPartOf.url = 'https://changyouquan.com/'; changed = true; }
        if (changed) s.textContent = JSON.stringify(data, null, 2);
      });
    } catch (e) {}
  }

  function apply() {
    document.querySelectorAll('[data-i18n]').forEach((el) => {
      const key = el.getAttribute('data-i18n');
      const text = t(key);
      if (el.hasAttribute('data-i18n-html')) {
        el.innerHTML = text;
      } else if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.setAttribute('placeholder', text);
      } else {
        el.textContent = text;
      }
    });
    const title = t('app.title');
    if (title && title !== 'app.title') document.title = title;
    try {
      const desc = t('meta.description');
      if (desc && desc !== 'meta.description') {
        let md = document.querySelector('meta[name="description"]');
        if (!md) {
          md = document.createElement('meta');
          md.setAttribute('name', 'description');
          document.head.appendChild(md);
        }
        md.setAttribute('content', desc);
      }
    } catch(e){}
    document.documentElement.lang = current;
    const select = document.getElementById('langSelect');
    if (select) select.value = current;
    try { updateSEO(current); } catch (e) {}
  }

  async function setLanguage(lang) {
    if (!supported.includes(lang)) lang = 'en';
    current = lang;
    await load(lang);
    try {
      localStorage.setItem('lang', lang);
    } catch (e) {}
    apply();
  }

  async function init(opts) {
    const def = (opts && opts.defaultLang) ? opts.defaultLang : 'zh';
    let initial = null;
    try {
      const seg = (window.location.pathname || '/').split('/').filter(Boolean);
      const first = seg[0] || '';
      if (supported.includes(first)) initial = first;
    } catch (e) {}
    try {
      const saved = localStorage.getItem('lang');
      if (saved && supported.includes(saved)) initial = saved;
    } catch (e) {}
    if (!initial) {
      const nav = navigator.language || navigator.userLanguage || 'en';
      initial = normalizeLang(nav);
    }
    if (!supported.includes(initial)) initial = def;
    await loadFallback();
    await setLanguage(initial);
    const select = document.getElementById('langSelect');
    if (select) {
      select.addEventListener('change', (e) => setLanguage(e.target.value));
    }
  }

  return { t, setLanguage, init, getArray, updateSEO, get current() { return current; } };
})();
