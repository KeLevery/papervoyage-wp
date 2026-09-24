/* 纸旅 PaperVoyage · demo 交互脚本 */
(function(){
  'use strict';
  var $=function(s,c){return (c||document).querySelector(s)};
  var $$=function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))};

  /* 移动导航 */
  var menuToggle=$('#menu-toggle');
  var nav=$('#site-navigation');
  var mask=$('#nav-mask');
  if(menuToggle&&nav){
    menuToggle.addEventListener('click',function(){
      var open=nav.classList.toggle('is-open');
      if(mask)mask.classList.toggle('is-open',open);
      menuToggle.setAttribute('aria-expanded',open?'true':'false');
      document.body.style.overflow=open?'hidden':'';
    });
    if(mask)mask.addEventListener('click',function(){nav.classList.remove('is-open');mask.classList.remove('is-open');document.body.style.overflow='';});
  }

  /* 搜索浮层 */
  var ov=$('#search-overlay');
  if($('#search-toggle')) $('#search-toggle').addEventListener('click',function(){ov.classList.add('is-open');setTimeout(function(){$('input',ov).focus()},120)});
  if($('#search-close')) $('#search-close').addEventListener('click',function(){ov.classList.remove('is-open')});
  if(ov) ov.addEventListener('click',function(e){if(e.target===ov)ov.classList.remove('is-open')});
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){if(ov)ov.classList.remove('is-open');if(nav)nav.classList.remove('is-open');if(mask)mask.classList.remove('is-open');document.body.style.overflow='';}
    if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){e.preventDefault();if(ov){ov.classList.add('is-open');setTimeout(function(){$('input',ov).focus()},120)}}
  });

  /* 明暗切换 */
  var themeToggle=$('#theme-toggle');
  if(themeToggle) themeToggle.addEventListener('click',function(){
    var h=document.documentElement;
    var n=h.getAttribute('data-theme')==='dark'?'light':'dark';
    h.setAttribute('data-theme',n);
    h.style.background=n==='dark'?'#1e2022':'#f1f1f1';
    try{localStorage.setItem('papervoyage-theme',n)}catch(e){}
  });

  /* 进场动画 */
  var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function(es){es.forEach(function(en){if(en.isIntersecting){en.target.classList.add('is-in');io.unobserve(en.target)}})},{threshold:.08,rootMargin:'0px 0px 400px 0px'}) : null;
  function observeReveals(){
    $$('.reveal:not(.is-in)').forEach(function(el){ if(io) io.observe(el); else el.classList.add('is-in'); });
  }
  observeReveals();
  /* 暴露给 app.js 动态渲染后调用 */
  window.themeReveal = observeReveals;
  /* 安全兜底：1.2s 后所有未入场元素强制显示 */
  setTimeout(function(){$$('.reveal:not(.is-in)').forEach(function(el){el.classList.add('is-in')})},1200);

  /* 回到顶部 */
  var ft=$('#float-tools');
  window.addEventListener('scroll',function(){if(ft)ft.classList.toggle('is-show',window.scrollY>480)},{passive:true});
  if($('#back-to-top')) $('#back-to-top').addEventListener('click',function(){window.scrollTo({top:0,behavior:'smooth'})});

  /* 复制链接（事件委托：PJAX 替换 main 后新按钮依然生效） */
  document.addEventListener('click', function(e){
    var btn = e.target.closest ? e.target.closest('[data-copy]') : null;
    if(!btn || btn.dataset.copying) return;
    e.preventDefault();
    btn.dataset.copying = 'true';
    var url=btn.getAttribute('data-copy');
    var origHTML=btn.innerHTML;
    var done=function(){
      var svg=btn.querySelector('svg');
      btn.innerHTML=(svg?svg.outerHTML+' ':'')+'链接已复制';
      setTimeout(function(){btn.innerHTML=origHTML;delete btn.dataset.copying;},1500);
    };
    if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(url).then(done).catch(done)}
    else{var ta=document.createElement('textarea');ta.value=url;document.body.appendChild(ta);ta.select();try{document.execCommand('copy')}catch(err){}document.body.removeChild(ta);done()}
  });

  /* 顶栏时钟 */
  var clock=$('#topbar-clock');
  if(clock){
    var pad=function(n){return n<10?'0'+n:''+n};
    var tick=function(){var d=new Date();clock.textContent=d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate())+' '+pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds())};
    tick();setInterval(tick,1000);
  }

  /* 跳转前先回顶部：pageswap 在旧页快照捕获前触发，
     提前 scrollTo 让新旧页面对齐滚动位置，交叉过渡不再整页上下跳动 */
  window.addEventListener('pageswap', function(e){
    if (e.viewTransition && window.scrollY > 0) window.scrollTo(0, 0);
  });
})();
