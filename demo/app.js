/* 纸旅 PaperVoyage · 应用逻辑（数据驱动渲染 + 真评论） */
(function(){
  'use strict';
  var A = window.PV_ARTICLES || [];
  var $ = function(s,c){return (c||document).querySelector(s)};
  var $$ = function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))};

  /* ---------- 首页 ---------- */
  function renderHome(){
    var byDate = A.slice().sort(function(x,y){ return y.date.localeCompare(x.date); });
    var featured = $('#home-featured');
    if(featured){
      var f = byDate.slice(0,2);
      featured.innerHTML = f.map(function(a){
        return '<a class="feature-card" href="article.html?slug='+a.slug+'">'+
          '<span class="thumb"><img src="'+a.cover+'" alt="'+pvEsc(a.title)+'"></span>'+
          '<span class="cap"><span class="cat">'+a.categoryCn+' · '+a.categoryEn+'</span>'+
          '<h3>'+pvEsc(a.title)+'</h3>'+
          '<span class="d">'+a.date+' · 约 '+a.readTime+' 分钟</span></span></a>';
      }).join('');
    }
    var latest = $('#home-latest');
    if(latest){
      latest.innerHTML = byDate.slice(2,10).map(function(a){
        return '<div class="post-item"><span class="temp-badge">'+a.temp+' °C</span>'+
          '<span class="t"><a href="article.html?slug='+a.slug+'">'+pvEsc(a.title)+'</a></span>'+
          '<span class="d">'+a.date+'</span></div>';
      }).join('');
    }
    var hot = $('#home-hot');
    if(hot){
      var sorted = A.slice().sort(function(x,y){return y.temp-x.temp;}).slice(0,6);
      hot.innerHTML = sorted.map(function(a,i){
        return '<li><span class="rank">'+String(i+1).padStart(2,'0')+'</span>'+
          '<span class="t"><a href="article.html?slug='+a.slug+'">'+pvEsc(a.title)+'</a></span>'+
          '<span class="temp-badge">'+a.temp+' °C</span></li>';
      }).join('');
    }
    var tags = $('#home-tags');
    if(tags){
      var tagCount = {};
      A.forEach(function(a){a.tags.forEach(function(t){tagCount[t]=(tagCount[t]||0)+1;});});
      tags.className = 'tag-cloud';
      var tagArr = Object.keys(tagCount).sort(function(x,y){return tagCount[y]-tagCount[x];}).slice(0,8);
      tags.innerHTML = tagArr.map(function(t){
        return '<a href="articles.html?tag='+encodeURIComponent(t)+'">'+pvEsc(t)+'<span class="n">'+tagCount[t]+'</span></a>';
      }).join('<span class="sep">·</span>');
    }
    var cats = $('#home-cats');
    if(cats){
      cats.innerHTML = window.PV_CATEGORIES.filter(function(c){
        return A.some(function(a){return a.category===c.slug;});
      }).slice(0,3).map(function(c){
        var posts = A.filter(function(a){return a.category===c.slug;}).sort(function(x,y){ return y.date.localeCompare(x.date); }).slice(0,4);
        var items = posts.map(function(a){
          return '<div class="post-item"><span class="temp-badge">'+a.temp+' °C</span>'+
            '<span class="t"><a href="article.html?slug='+a.slug+'">'+pvEsc(a.title)+'</a></span>'+
            '<span class="d">'+a.date+'</span></div>';
        }).join('');
        return '<div class="home-col reveal"><div class="sec-head"><span class="bar"></span>'+
          '<h2>'+c.cn+'</h2><span class="en-sub">'+c.en+'</span>'+
          '<a class="more-link" href="category-'+c.slug+'.html">More →</a></div>'+items+'</div>';
      }).join('');
    }
  }

  /* ---------- 列表页 ---------- */
  function renderList(){
    var flow = $('#list-flow');
    if(!flow) return;
    var cat = pvQuery('cat'), tag = pvQuery('tag'), q = pvQuery('q');
    var filtered = A.slice();
    var title = '全部文章', titleEn = 'All Articles', desc = '按时间倒序排列的全部文字。';
    if(cat){
      var c = window.PV_CATEGORIES.find(function(x){return x.slug===cat;});
      filtered = filtered.filter(function(a){return a.category===cat;});
      title = c ? c.cn : '栏目'; titleEn = c ? c.en : 'Category';
      desc = '「'+title+'」栏目下的全部文章。';
    } else if(tag){
      filtered = filtered.filter(function(a){return a.tags.indexOf(tag)>=0;});
      title = '# '+tag; titleEn = 'Tagged With';
      desc = '标签「'+tag+'」下的全部文章。';
    } else if(q){
      var ql = q.toLowerCase();
      filtered = filtered.filter(function(a){
        return a.title.toLowerCase().indexOf(ql)>=0 || a.excerpt.toLowerCase().indexOf(ql)>=0 ||
               a.content.toLowerCase().indexOf(ql)>=0;
      });
      title = '搜索：'+q; titleEn = 'Search Results';
      desc = filtered.length ? '找到 '+filtered.length+' 篇相关文章。' : '没有找到相关文章，换个关键词试试。';
    }
    filtered.sort(function(x,y){return y.date.localeCompare(x.date);});

    var head = $('#page-head');
    if(head){
      head.querySelector('.en-sub').textContent = titleEn;
      head.querySelector('h1').textContent = title;
      head.querySelector('.desc').textContent = desc;
    }
    document.title = title + ' · 纸旅 PaperVoyage';

    if(!filtered.length){
      flow.innerHTML = '<div class="post-card" style="padding:48px;text-align:center">'+
        '<span class="en-sub" style="display:block;margin-bottom:10px">Nothing Here Yet</span>'+
        '<h2 style="font-size:20px">这里还是一片空白</h2>'+
        '<p style="color:var(--muted);font-size:14px">没有找到相关内容，换个关键词试试？</p></div>';
      return;
    }
    flow.innerHTML = filtered.map(function(a){
      return '<article class="post-card">'+
        (a.cover?'<a class="card-thumb" href="article.html?slug='+a.slug+'"><img src="'+a.cover+'" alt=""></a>':'')+
        '<div class="card-body">'+
        '<div class="card-meta"><a class="cat-link" href="articles.html?cat='+a.category+'">'+a.categoryCn+'</a>'+
        '<time>'+a.date+'</time><span class="temp-badge">'+a.temp+' °C</span><span>约 '+a.readTime+' 分钟</span></div>'+
        '<h2><a href="article.html?slug='+a.slug+'">'+pvEsc(a.title)+'</a></h2>'+
        '<p class="excerpt">'+pvEsc(a.excerpt)+'</p>'+
        '<div class="card-foot">'+
          a.tags.map(function(t){return '<a class="tag-pill" href="articles.html?tag='+encodeURIComponent(t)+'"># '+pvEsc(t)+'</a>';}).join('')+
          '<a class="more-link" style="margin-left:auto" href="article.html?slug='+a.slug+'">阅读 →</a>'+
        '</div></div></article>';
    }).join('');
    // 触发 reveal
    if(window.themeReveal) window.themeReveal();
  }

  /* ---------- 文章详情页 ---------- */
  function renderArticle(){
    var mount = $('#article-mount');
    if(!mount) return;
    var slug = pvQuery('slug');
    var a = pvGet(slug);
    if(!a){
      mount.innerHTML = '<div class="error-404" style="padding:96px 0;text-align:center">'+
        '<div class="big" style="font-family:var(--font-mono);font-size:96px;color:var(--brand);line-height:1">404</div>'+
        '<h1 style="margin:16px 0">这篇文章不存在</h1>'+
        '<p style="color:var(--muted)">可能被风吹走了，或者从未被写过。</p>'+
        '<p style="margin-top:20px"><a class="more-link" href="articles.html">← 回到文章列表</a></p></div>';
      document.title = '404 · 纸旅 PaperVoyage';
      return;
    }
    document.title = a.title + ' · 纸旅 PaperVoyage';
    var prev = A[A.indexOf(a)+1] || null;
    var next = A[A.indexOf(a)-1] || null;

    mount.innerHTML =
      '<header class="single-head reveal">'+
        '<span class="en-sub">'+a.categoryEn+' · '+a.categoryCn+'</span>'+
        '<h1>'+pvEsc(a.title)+'</h1>'+
        '<div class="single-meta">'+
          '<time datetime="'+a.dateTime+'">'+a.date+'</time><span class="sep"></span>'+
          '<span>约 '+a.readTime+' 分钟</span><span class="sep"></span>'+
          '<span>'+a.words+' 字</span><span class="sep"></span>'+
          '<span class="temp-badge">'+a.temp+' °C</span><span class="sep"></span>'+
          '<a href="#comments"><span id="cmt-count">…</span> 条评论</a>'+
        '</div>'+
      '</header>'+
      '<div class="single-layout"><div class="single-body">'+
        (a.cover?'<figure class="single-thumb"><img src="'+a.cover+'" alt=""></figure>':'')+
        '<div class="entry-content">'+a.content+'</div>'+
        '<div class="entry-tags tag-pills">'+
          a.tags.map(function(t){return '<a class="tag-pill" href="articles.html?tag='+encodeURIComponent(t)+'"># '+pvEsc(t)+'</a>';}).join('')+
        '</div>'+
        shareBox(a)+
        postNav(prev,next)+
        commentsBox(a)+
      '</div>'+
      sidebarBox(a)+
      '</div>';

    renderComments(a);
    bindCommentForm(a);
    if(window.themeReveal) window.themeReveal();
  }

  function shareBox(a){
    var url = location.origin + location.pathname + '?slug=' + a.slug;
    var u = encodeURIComponent(url), t = encodeURIComponent(a.title);
    return '<div class="share-box">'+
      '<span class="en-sub">Share · 分享本文</span>'+
      '<a class="share-btn" href="https://service.weibo.com/share/share.php?url='+u+'&title='+t+'" target="_blank" rel="noopener"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 13.5c1.5-3 5-3.5 6-2"/></svg>微博</a>'+
      '<a class="share-btn" href="https://twitter.com/intent/tweet?url='+u+'&text='+t+'" target="_blank" rel="noopener"><svg viewBox="0 0 24 24"><path d="M4 4l16 16M20 4L4 20"/></svg>X / Twitter</a>'+
      '<a class="share-btn" href="#" data-copy="'+url+'"><svg viewBox="0 0 24 24"><rect x="4" y="8" width="16" height="12" rx="3"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/></svg>复制链接</a>'+
      '</div>';
  }
  function postNav(prev,next){
    return '<nav class="post-nav">'+
      (prev?'<a href="article.html?slug='+prev.slug+'"><span class="nav-label">← 上一篇 · Older</span><span class="nav-title">'+pvEsc(prev.title)+'</span></a>':'<span></span>')+
      (next?'<a class="nav-next" href="article.html?slug='+next.slug+'"><span class="nav-label">下一篇 · Newer →</span><span class="nav-title">'+pvEsc(next.title)+'</span></a>':'')+
      '</nav>';
  }
  function sidebarBox(a){
    var hot = A.slice().sort(function(x,y){return y.temp-x.temp;}).slice(0,4);
    return '<aside class="sidebar">'+
      '<section class="widget"><h3 class="widget-title">温度榜 <span class="en-sub" style="margin-left:8px">Hottest</span></h3><ol class="hot-list">'+
        hot.map(function(h,i){return '<li><span class="rank">'+String(i+1).padStart(2,'0')+'</span><span class="t"><a href="article.html?slug='+h.slug+'">'+pvEsc(h.title)+'</a></span><span class="temp-badge">'+h.temp+' °C</span></li>';}).join('')+
      '</ol></section>'+
      '<section class="widget"><h3 class="widget-title">同栏目</h3><ul>'+
        A.filter(function(x){return x.category===a.category && x.slug!==a.slug;}).slice(0,5).map(function(x){
          return '<li><a href="article.html?slug='+x.slug+'">'+pvEsc(x.title)+'</a><span class="n">'+x.date+'</span></li>';
        }).join('')+
      '</ul></section>'+
      '</aside>';
  }

  /* ---------- 评论（真·localStorage） ---------- */
  function commentsBox(a){
    return '<div id="comments" class="comments-area">'+
      '<h2 class="comments-title"><span class="bar" style="width:3px;height:18px;background:var(--brand);border-radius:2px"></span>'+
      '<span id="cmt-title-count">…</span> 条评论 <span class="en-sub" style="margin-left:8px">Neighborhood Voices</span></h2>'+
      '<ol class="comment-list" id="cmt-list"></ol>'+
      '<h3 class="comment-reply-title" style="margin-top:32px;font-size:16px">留下你的声音 <span class="en-sub" style="margin-left:8px">Leave a Whisper</span></h3>'+
      '<form class="comment-form" id="cmt-form">'+
        '<div class="row">'+
          '<div><label>昵称 *</label><input type="text" name="author" required></div>'+
          '<div><label>邮箱 *（不公开）</label><input type="email" name="email" required></div>'+
          '<div><label>站点（可选）</label><input type="text" name="site"></div>'+
        '</div>'+
        '<div><label>评论内容 *</label><textarea name="content" rows="5" placeholder="说点真实的——别客气，也别讨好。" required></textarea></div>'+
        '<p class="hint">邮箱地址不会被公开。友好交流，寒来暑往，人聚又散。评论保存在你的浏览器本地。</p>'+
        '<div><button class="submit" type="submit">发布评论</button></div>'+
      '</form>'+
    '</div>';
  }
  function avatarChar(name){ return (name||'?').trim().charAt(0).toUpperCase() || '?'; }
  function renderComments(a){
    var list = pvComments.get(a.slug);
    var el = $('#cmt-list');
    if(!list.length){
      el.innerHTML = '<li style="padding:24px 0;color:var(--muted);font-size:13px;border-bottom:1px solid var(--line-soft)">还没有评论。做第一个开口的人。</li>';
    } else {
      el.innerHTML = list.map(function(c){
        return '<li><article class="comment-body">'+
          '<span class="comment-avatar">'+pvEsc(avatarChar(c.author))+'</span>'+
          '<div class="comment-main">'+
            '<div class="comment-meta"><span class="comment-author">'+pvEsc(c.author)+'</span>'+
            '<a class="comment-date" href="#">'+c.date+'</a></div>'+
            '<div class="comment-content">'+pvEsc(c.content).replace(/\n/g,'<br>')+'</div>'+
            '<div><a class="comment-reply" href="#">回复</a></div>'+
          '</div></article></li>';
      }).join('');
    }
    var cc = $('#cmt-count'); if(cc) cc.textContent = list.length;
    var ct = $('#cmt-title-count'); if(ct) ct.textContent = list.length;
  }
  function bindCommentForm(a){
    var form = $('#cmt-form');
    if(!form) return;
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var author = form.author.value.trim();
      var content = form.content.value.trim();
      if(!author || !content){ return; }
      var now = new Date();
      var pad = function(n){return n<10?'0'+n:''+n;};
      var c = {
        author: author,
        email: form.email.value.trim(),
        site: form.site.value.trim(),
        content: content,
        date: now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())+' '+pad(now.getHours())+':'+pad(now.getMinutes())
      };
      pvComments.add(a.slug, c);
      renderComments(a);
      form.content.value = '';
      // 滚到评论顶
      var list = $('#cmt-list');
      if(list) list.scrollIntoView({behavior:'smooth', block:'start'});
    });
  }

  /* ---------- 归档页 ---------- */
  function renderArchive(){
    var mount = $('#archive-mount');
    if(!mount) return;
    var byYear = {};
    A.slice().sort(function(x,y){return y.date.localeCompare(x.date);}).forEach(function(a){
      var y = a.date.slice(0,4);
      (byYear[y] = byYear[y] || []).push(a);
    });
    var years = Object.keys(byYear).sort(function(x,y){return y.localeCompare(x);});
    mount.innerHTML = years.map(function(y){
      var items = byYear[y].map(function(a){
        return '<div class="post-item"><span class="temp-badge">'+a.temp+' °C</span>'+
          '<span class="t"><a href="article.html?slug='+a.slug+'">'+pvEsc(a.title)+'</a></span>'+
          '<span class="d">'+a.date+'</span></div>';
      }).join('');
      return '<section class="archive-year"><h2>'+y+'</h2>'+items+'</section>';
    }).join('');
    if(window.themeReveal) window.themeReveal();
  }

  /* ---------- 列表页筛选条 ---------- */
  function renderFilterBar(){
    var bar = $('#filter-bar');
    if(!bar) return;
    var cat = pvQuery('cat'), tag = pvQuery('tag');
    var cats = window.PV_CATEGORIES.filter(function(c){return A.some(function(a){return a.category===c.slug;});});
    var tagCount = {};
    A.forEach(function(a){a.tags.forEach(function(t){tagCount[t]=(tagCount[t]||0)+1;});});
    var tags = Object.keys(tagCount).sort(function(x,y){return tagCount[y]-tagCount[x];}).slice(0,14);
    bar.innerHTML =
      '<span class="en-sub">Filter by Category</span>'+
      '<div class="tag-pills" style="margin-bottom:14px">'+
        '<a class="tag-pill '+(cat||tag?'':'is-active')+'" href="articles.html">全部</a>'+
        cats.map(function(c){return '<a class="tag-pill '+(cat===c.slug?'is-active':'')+'" href="category-'+c.slug+'.html">'+c.cn+' <span class="n">'+A.filter(function(a){return a.category===c.slug;}).length+'</span></a>';}).join('')+
      '</div>'+
      '<span class="en-sub">Filter by Tag</span>'+
      '<div class="tag-pills">'+
        tags.map(function(t){return '<a class="tag-pill '+(tag===t?'is-active':'')+'" href="articles.html?tag='+encodeURIComponent(t)+'"># '+pvEsc(t)+' <span class="n">'+tagCount[t]+'</span></a>';}).join('')+
      '</div>';
  }

  /* ---------- 独立栏目页（每栏目一个 HTML 文件，视觉各异） ---------- */
  function renderCategoryPage(){
    var flow = $('#list-flow');
    if(!flow) return;
    var catSlug = document.body.dataset.cat;
    if(!catSlug) return;
    var c = window.PV_CATEGORIES.find(function(x){return x.slug===catSlug;});
    if(!c) return;
    var filtered = A.filter(function(a){return a.category===catSlug;})
      .sort(function(x,y){return y.date.localeCompare(x.date);});

    var head = $('#page-head');
    if(head){
      head.querySelector('.en-sub').textContent = c.en;
      head.querySelector('h1').textContent = c.cn;
      head.querySelector('.desc').textContent = '「'+c.cn+'」栏目下的全部 '+filtered.length+' 篇文章。';
    }
    document.title = c.cn + ' · ' + c.en + ' · 纸旅 PaperVoyage';

    /* 按栏目渲染不同风格的卡片 */
    flow.innerHTML = filtered.map(function(a, idx){
      var d = a.date.split('-');
      var link = 'article.html?slug='+a.slug;
      switch(catSlug){
        case 'albatross':
          /* 航海日志卡：左侧日期柱 + 右侧正文 */
          return '<article class="log-card"><a class="log-date" href="'+link+'"><span class="day">'+d[2]+'</span><span class="mon">'+d[1]+'月</span></a><div class="log-body"><h3><a href="'+link+'">'+pvEsc(a.title)+'</a></h3><p>'+pvEsc(a.excerpt)+'</p><div class="log-meta"><span class="temp-badge">'+a.temp+' °C</span><span>'+a.date+'</span><span>约 '+a.readTime+' 分钟</span></div></div></article>';
        case 'travels':
          /* 明信片卡：邮戳 + 正文 */
          return '<article class="postcard"><div class="stamp"><span class="big">'+d[2]+'</span>'+d[1]+'月</div><h3><a href="'+link+'">'+pvEsc(a.title)+'</a></h3><p>'+pvEsc(a.excerpt)+'</p><div class="pc-meta"><span>'+a.date+'</span> · <span>约 '+a.readTime+' 分钟</span> · <span class="temp-badge">'+a.temp+' °C</span></div></article>';
        case 'films':
          /* 宽银幕卡：21:9 缩略图 + 暗色正文 */
          return '<article class="screening"><a class="scr-thumb" href="'+link+'"><img src="'+a.cover+'" alt=""></a><div class="scr-body"><h3><a href="'+link+'" style="color:inherit">'+pvEsc(a.title)+'</a></h3><p>'+pvEsc(a.excerpt)+'</p><div class="scr-meta"><span class="runtime">'+a.readTime+' MIN</span><span>'+a.date+'</span><span>'+a.temp+' °C</span></div></div></article>';
        case 'essays':
          /* 手稿卡：无图，纯文字，大标题 */
          return '<article class="manuscript"><div class="ms-date">'+a.date+'</div><h3><a href="'+link+'">'+pvEsc(a.title)+'</a></h3><p class="ms-excerpt">'+pvEsc(a.excerpt)+'</p><div class="ms-foot"><span class="temp-badge">'+a.temp+' °C</span><span>约 '+a.readTime+' 分钟</span><span>'+a.words+' 字</span><a class="more-link" style="margin-left:auto" href="'+link+'">阅读全文 →</a></div></article>';
        case 'dreams':
          /* 梦境记录卡：漂浮半透明 + 梦境编号 */
          var dreamNum = String(idx+1).padStart(2,'0');
          return '<article class="dream-card"><div class="dream-num">梦境记录 #'+dreamNum+'</div><h3><a href="'+link+'" style="color:inherit">'+pvEsc(a.title)+'</a></h3><p>'+pvEsc(a.excerpt)+'</p><div class="dream-meta"><span>'+a.date+'</span> · <span>约 '+a.readTime+' 分钟</span> · <span class="temp-badge">'+a.temp+' °C</span></div></article>';
        default:
          return '<article class="post-card"><div class="card-body"><h2><a href="'+link+'">'+pvEsc(a.title)+'</a></h2></div></article>';
      }
    }).join('');
    if(window.themeReveal) window.themeReveal();
  }

  /* ---------- 启动 ---------- */
  function init(){
    renderHome();
    renderFilterBar();
    renderList();
    renderArticle();
    renderArchive();
    renderCategoryPage();
  }
  /* 暴露给 PJAX：内容替换后重跑渲染 */
  window.PVRender = init;
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
