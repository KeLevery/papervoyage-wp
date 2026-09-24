<?php
/**
 * Template Name: 留言墙（Guestbook）
 * Template Post Type: page
 *
 * 全站留言墙页面。评论通过 WordPress 原生评论系统存储。
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="page-head reveal">
			<span class="en-sub"><?php esc_html_e( 'Neighborhood Voices · 街坊留言', 'papervoyage' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( get_the_content() ) : ?>
				<div class="desc entry-content" style="font-size:14px;max-width:640px"><?php the_content(); ?></div>
			<?php endif; ?>
		</header>

		<div class="content-with-sidebar">
			<div>
				<?php
				// 使用 WordPress 评论系统作为留言墙
				comments_template( '', true );
				?>
			</div>

			<aside class="sidebar">
				<section class="widget">
					<h3 class="widget-title"><?php esc_html_e( '关于这里', 'papervoyage' ); ?></h3>
					<p style="font-size:13px;line-height:1.9;color:var(--muted)"><?php esc_html_e( '留言墙是我最初做博客时保留的功能。十多年过去，它变成了一个很私人的角落——有些人不读文章也会来这里留一句晚安。', 'papervoyage' ); ?></p>
				</section>
				<?php if ( is_active_sidebar( 'sidebar' ) ) dynamic_sidebar( 'sidebar' ); ?>
			</aside>
		</div>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
