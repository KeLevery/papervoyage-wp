/* 纸旅 PaperVoyage · PJAX 无感切换
   拦截站内链接/GET 表单 → fetch 目标页 → 只替换 <main> 内容，
   header/导航/footer 不重渲染、CSS/JS 不重载、全程无白屏。
   失败或特殊场景自动退回整页跳转（渐进增强，不影响 SEO 与无 JS 访问）。 */
(function () {
  'use strict';
  if (!window.fetch || !window.history || !window.history.pushState || !window.DOMParser) return;

  var main = document.querySelector('main');
  if (!main) return;

  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var absUrl = function (u) { try { return new URL(u, location.href); } catch (e) { return null; } };

  var cache = new Map();          // path+search -> {title, bodyAttrs, mainHTML}
  var CACHE_MAX = 10;
  var busy = false;
  var currentKey = location.pathname + location.search;

  /* 换页前收起浮层（搜索遮罩 / 移动端导航抽屉）：它们在 main 之外，
     不随内容替换消失，残留会遮挡新页面并锁住滚动 */
  function dismissOverlays() {
    var ov = document.getElementById('search-overlay');
    if (ov) ov.classList.remove('is-open');
    var nav = document.getElementById('site-navigation');
    if (nav) nav.classList.remove('is-open');
    var mask = document.getElementById('nav-mask');
    if (mask) mask.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  function navigable(url) {
    return url &&
      url.origin === location.origin &&
      !/^(mailto|tel|javascript):/i.test(url.protocol) &&
      url.pathname.indexOf('/wp-admin') === -1 &&
      url.pathname.indexOf('/wp-login') === -1;
  }

  /* ---------- body 属性（class + data-*）搬运：栏目视觉由 cat-{slug} 类驱动 ---------- */
  function collectBodyAttrs(body) {
    var attrs = { className: body.className || '' };
    Array.prototype.forEach.call(body.attributes, function (a) {
      if (a.name.indexOf('data-') === 0) attrs[a.name] = a.value;
    });
    return attrs;
  }
  function applyBodyAttrs(attrs) {
    document.body.className = attrs.className;
    Array.prototype.slice.call(document.body.attributes).forEach(function (a) {
      if (a.name.indexOf('data-') === 0) document.body.removeAttribute(a.name);
    });
    Object.keys(attrs).forEach(function (k) {
      if (k !== 'className') document.body.setAttribute(k, attrs[k]);
    });
  }

  /* ---------- 导航高亮随 URL 迁移 ---------- */
  function updateNav(url) {
    var here = url.pathname.replace(/\/+$/, '');
    $$('.main-navigation a').forEach(function (a) {
      var u = absUrl(a.getAttribute('href') || '');
      if (!u) return;
      var there = u.pathname.replace(/\/+$/, '');
      var hit = there !== '' && there !== '/' && (here === there || here.indexOf(there + '/') === 0);
      a.classList.toggle('active', hit);
      if (a.parentElement) a.parentElement.classList.toggle('current-menu-item', hit);
    });
  }

  /* ---------- 拉取与解析 ---------- */
  function fetchDoc(href) {
    return fetch(href, { credentials: 'same-origin', headers: { 'X-PJAX': 'true' } })
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); });
  }
  function parseDoc(html) {
    var doc = new DOMParser().parseFromString(html, 'text/html');
    var mainEl = doc.querySelector('main');
    if (!mainEl) throw new Error('no <main> in response');
    return {
      title: doc.title,
      bodyAttrs: collectBodyAttrs(doc.body),
      mainHTML: mainEl.innerHTML,
      pageScripts: collectPageScripts(doc, mainEl)
    };
  }

  /* main 之外、body 之内的内联脚本（如留言墙页面逻辑）：
     外链脚本与 speculationrules 跳过，避免重复初始化全局模块 */
  function collectPageScripts(doc, mainEl) {
    var out = [];
    Array.prototype.slice.call(doc.body.querySelectorAll('script')).forEach(function (s) {
      if (mainEl.contains(s) || s.src) return;
      var type = (s.getAttribute('type') || 'text/javascript').toLowerCase();
      if (type !== 'text/javascript' && type !== 'application/javascript' && type !== 'module' && type !== '') return;
      out.push(s.textContent);
    });
    return out;
  }

  /* innerHTML 注入的 <script> 不会执行，逐个重建以恢复页面级脚本 */
  function runScripts(scope) {
    $$('script', scope).forEach(function (old) {
      var s = document.createElement('script');
      Array.prototype.forEach.call(old.attributes, function (a) { s.setAttribute(a.name, a.value); });
      s.text = old.text;
      old.parentNode.replaceChild(s, old);
    });
  }

  /* ---------- 内容替换 + 页面级 JS 重跑 ---------- */
  function swap(parsed, href, scrollTop, hash) {
    document.title = parsed.title;
    applyBodyAttrs(parsed.bodyAttrs);
    main.innerHTML = parsed.mainHTML;
    runScripts(main);
    updateNav(absUrl(href));
    if (window.PVRender) window.PVRender();       /* demo 数据渲染 */
    if (window.themeReveal) window.themeReveal();  /* 进场动画重新观察 */
    (parsed.pageScripts || []).forEach(function (code) { /* 页面级内联脚本重跑 */
      var s = document.createElement('script');
      s.text = code;
      document.body.appendChild(s);
    });
    if (hash) {
      var target = document.getElementById(hash.slice(1)) || main.querySelector(hash);
      if (target) { target.scrollIntoView(); return; }
    }
    window.scrollTo(0, typeof scrollTop === 'number' ? scrollTop : 0);
  }

  function transitionTo(parsed, href, scrollTop, hash) {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var enter = function () {
      if (reduce) { swap(parsed, href, scrollTop, hash); return; }
      main.classList.add('pjax-enter');
      swap(parsed, href, scrollTop, hash);
      void main.offsetHeight; /* 强制回流，让起始态先生效 */
      requestAnimationFrame(function () { main.classList.remove('pjax-enter'); });
    };
    if (reduce) { enter(); return; }
    main.classList.add('pjax-exit');
    setTimeout(function () {
      main.classList.remove('pjax-exit');
      enter();
    }, 160);
  }

  function navigate(href, push, scrollTop) {
    var url = absUrl(href);
    if (!url || !navigable(url)) { location.href = href; return; }
    if (busy) return;
    var key = url.pathname + url.search;
    if (!push && key === currentKey) return; /* 仅 hash 变化 */
    busy = true;
    dismissOverlays();
    var done = function (parsed) {
      try {
        if (push) {
          history.replaceState({ pv: true, scroll: window.scrollY }, '', location.href);
          history.pushState({ pv: true, scroll: 0 }, '', url.href);
        }
        currentKey = key;
        transitionTo(parsed, url.href, scrollTop, url.hash);
      } finally {
        busy = false; /* 页面级渲染抛异常也不能卡死后续导航 */
      }
    };
    var cached = cache.get(key);
    if (cached) { done(cached); return; }
    fetchDoc(url.href).then(function (html) {
      var parsed = parseDoc(html);
      cache.set(key, parsed);
      if (cache.size > CACHE_MAX) cache.delete(cache.keys().next().value);
      done(parsed);
    }).catch(function () {
      busy = false;
      location.href = url.href; /* 兜底：整页跳转 */
    });
  }

  /* ---------- 拦截：链接点击 ---------- */
  document.addEventListener('click', function (e) {
    if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var a = e.target.closest ? e.target.closest('a') : null;
    if (!a || a.target === '_blank' || a.hasAttribute('download')) return;
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || /^(mailto|tel|javascript):/i.test(href)) return;
    var url = absUrl(a.href);
    if (!url || !navigable(url)) return;
    /* 同页锚点走默认行为 */
    if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
    e.preventDefault();
    navigate(url.href, true);
  });

  /* ---------- 拦截：GET 表单（站内搜索） ---------- */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || (form.method || 'get').toLowerCase() !== 'get') return;
    var action = absUrl(form.getAttribute('action') || location.href);
    if (!action || !navigable(action)) return;
    e.preventDefault();
    var qs = new URLSearchParams();
    new FormData(form).forEach(function (value, key) {
      if (value) qs.append(key, value);
    });
    var href = action.origin + action.pathname + (qs.toString() ? '?' + qs.toString() : '');
    if (href !== location.href) navigate(href, true);
  });

  /* ---------- 前进/后退 ---------- */
  history.scrollRestoration = 'manual';
  history.replaceState({ pv: true, scroll: 0 }, '', location.href);
  window.addEventListener('popstate', function (e) {
    var url = absUrl(location.href);
    if (!navigable(url)) { location.reload(); return; }
    navigate(location.href, false, (e.state && typeof e.state.scroll === 'number') ? e.state.scroll : 0);
  });
})();
