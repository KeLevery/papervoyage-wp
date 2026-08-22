/**
 * 纸旅 PaperVoyage — 交互脚本
 * 移动导航 / 搜索浮层 / 明暗切换 / 滚动进场 / 回到顶部 / 复制分享 / 顶栏时钟
 */
(function () {
	'use strict';

	var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

	/* ---------- 移动导航 ---------- */
	var menuToggle = $('#menu-toggle');
	var nav = $('#site-navigation');
	var mask = $('#nav-mask');

	function closeNav() {
		if (!nav) return;
		nav.classList.remove('is-open');
		if (mask) mask.classList.remove('is-open');
		if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
		document.body.style.overflow = '';
	}

	if (menuToggle && nav) {
		menuToggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			if (mask) mask.classList.toggle('is-open', open);
			menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.style.overflow = open ? 'hidden' : '';
		});
		if (mask) mask.addEventListener('click', closeNav);
	}

	/* ---------- 搜索浮层 ---------- */
	var searchToggle = $('#search-toggle');
	var searchOverlay = $('#search-overlay');
	var searchClose = $('#search-close');

	function openSearch() {
		if (!searchOverlay) return;
		searchOverlay.classList.add('is-open');
		var field = $('.search-field', searchOverlay);
		if (field) setTimeout(function () { field.focus(); }, 120);
	}
	function closeSearch() {
		if (searchOverlay) searchOverlay.classList.remove('is-open');
	}

	if (searchToggle) searchToggle.addEventListener('click', openSearch);
	if (searchClose) searchClose.addEventListener('click', closeSearch);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeSearch(); closeNav(); }
		// Ctrl/Cmd + K 打开搜索
		if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
			e.preventDefault();
			openSearch();
		}
	});
	if (searchOverlay) {
		searchOverlay.addEventListener('click', function (e) {
			if (e.target === searchOverlay) closeSearch();
		});
	}

	/* ---------- 明暗主题切换 ---------- */
	var themeToggle = $('#theme-toggle');
	if (themeToggle) {
		themeToggle.addEventListener('click', function () {
			var html = document.documentElement;
			var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
			html.setAttribute('data-theme', next);
			try { localStorage.setItem('papervoyage-theme', next); } catch (e) {}
		});
	}

	/* ---------- 滚动进场（可重复调用：PJAX 替换内容后重新观察） ---------- */
	var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-in');
				io.unobserve(entry.target);
			}
		});
	}, { threshold: 0.08, rootMargin: '0px 0px 400px 0px' }) : null;
	function observeReveals() {
		$$('.reveal:not(.is-in)').forEach(function (el) {
			if (io) { io.observe(el); } else { el.classList.add('is-in'); }
		});
	}
	observeReveals();
	window.papervoyageReveal = observeReveals;
	/* 兜底：1.2s 后强制显示所有未入场元素，避免 IO 异常或无 JS 时内容不可见 */
	setTimeout(function () {
		$$('.reveal:not(.is-in)').forEach(function (el) { el.classList.add('is-in'); });
	}, 1200);

	/* ---------- 回到顶部 / 悬浮工具条 ---------- */
	var floatTools = $('#float-tools');
	var backToTop = $('#back-to-top');
	window.addEventListener('scroll', function () {
		if (floatTools) floatTools.classList.toggle('is-show', window.scrollY > 480);
	}, { passive: true });
	if (backToTop) {
		backToTop.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	/* ---------- 复制链接分享（事件委托：PJAX 替换 main 后新按钮依然生效） ---------- */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('[data-copy]') : null;
		if (!btn) return;
		e.preventDefault();
		var url = btn.getAttribute('data-copy');
		var done = function () {
			var hint = (window.papervoyageData && papervoyageData.copied) || '链接已复制';
			var old = btn.textContent;
			btn.textContent = hint;
			setTimeout(function () { btn.textContent = old; }, 1600);
		};
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).then(done).catch(done);
		} else {
			var ta = document.createElement('textarea');
			ta.value = url;
			document.body.appendChild(ta);
			ta.select();
			try { document.execCommand('copy'); } catch (err) {}
			document.body.removeChild(ta);
			done();
		}
	});

	/* ---------- 顶栏时钟 ---------- */
	var clock = $('#topbar-clock');
	if (clock) {
		var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
		var tick = function () {
			var d = new Date();
			clock.textContent = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
		};
		tick();
		setInterval(tick, 1000);
	}

	/* ---------- 跳转前先回顶部：pageswap 在旧页快照捕获前触发 ----------
	   提前 scrollTo 让新旧页面对齐滚动位置，交叉过渡不再整页上下跳动 */
	window.addEventListener('pageswap', function (e) {
		if (e.viewTransition && window.scrollY > 0) { window.scrollTo(0, 0); }
	});
})();
