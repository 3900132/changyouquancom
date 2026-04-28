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

  return { t, setLanguage, init, getArray, get current() { return current; } };
})();
