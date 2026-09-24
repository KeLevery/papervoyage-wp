<?php
/**
 * 头部模板：顶部工具条 + 吸顶主导航 + 搜索浮层
 *
 * @package PaperVoyage
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
/* 暗色模式尽早应用 + 立即铺纸色底：文档初始绘制（CSS 到达前）不再是浏览器默认白底，消除跳转白屏 */
(function(){var d=document.documentElement,b='#f1f1f1';try{var t=localStorage.getItem('papervoyage-theme');if(t){d.setAttribute('data-theme',t);if(t==='dark')b='#1e2022';}else if(window.matchMedia('(prefers-color-scheme: dark)').matches){d.setAttribute('data-theme','dark');b='#1e2022';}}catch(e){}d.style.background=b;})();
</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 顶部工具条 -->
<div class="topbar">
	<div class="wrap">
		<span class="brandmark"><?php echo esc_html( get_theme_mod( 'brand_mark', 'PAPERVOYAGE' ) ); ?></span>
		<?php
		if ( has_nav_menu( 'topbar' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'topbar',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
		}
		?>
		<div class="topbar-right">
			<span class="mono-date" id="topbar-clock"></span>
		</div>
	</div>
</div>

<!-- 主导航（吸顶） -->
<header class="site-header">
	<div class="wrap">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<p class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php bloginfo( 'name' ); ?><span class="dot">.</span>
					</a>
				</p>
				<?php $en_desc = get_theme_mod( 'site_desc_en', 'The Beginning of Everything' ); ?>
				<?php if ( $en_desc ) : ?>
					<div class="site-desc"><?php echo esc_html( $en_desc ); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<nav class="main-navigation" id="site-navigation" aria-label="<?php esc_attr_e( '主导航', 'papervoyage' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'walker'         => new PaperVoyage_Nav_Walker(),
						'fallback_cb'    => false,
					)
				);
			} else {
				wp_page_menu(
					array(
						'menu_class' => 'menu',
						'container'  => false,
					)
				);
			}
			?>
		</nav>

		<div class="header-actions">
			<button class="icon-btn" id="search-toggle" aria-label="<?php esc_attr_e( '搜索', 'papervoyage' ); ?>">
				<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
			</button>
			<button class="icon-btn" id="theme-toggle" aria-label="<?php esc_attr_e( '切换明暗主题', 'papervoyage' ); ?>">
				<svg viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/></svg>
			</button>
			<button class="icon-btn menu-toggle" id="menu-toggle" aria-label="<?php esc_attr_e( '打开菜单', 'papervoyage' ); ?>" aria-expanded="false">
				<svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
		</div>
	</div>
</header>
<div class="nav-mask" id="nav-mask"></div>

<!-- 搜索浮层 -->
<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( '站内搜索', 'papervoyage' ); ?>">
	<button class="icon-btn search-close" id="search-close" aria-label="<?php esc_attr_e( '关闭搜索', 'papervoyage' ); ?>">✕</button>
	<div class="search-inner">
		<span class="en-sub"><?php esc_html_e( 'Search the Archive', 'papervoyage' ); ?></span>
		<?php get_search_form(); ?>
		<p class="search-hint"><?php esc_html_e( '输入关键词，回车开始搜索… 支持文章标题与正文。', 'papervoyage' ); ?></p>
	</div>
</div>

<main class="site-main" id="content">
