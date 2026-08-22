<?php
/**
 * 主模板（文章列表页 / 兜底模板）
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<span class="en-sub"><?php esc_html_e( 'All Articles', 'papervoyage' ); ?></span>
		<h1><?php esc_html_e( '全部文章', 'papervoyage' ); ?></h1>
		<p class="desc"><?php esc_html_e( '按时间倒序排列的全部文字。', 'papervoyage' ); ?></p>
	</header>

	<?php get_template_part( 'template-parts/filter', 'bar' ); ?>

	<div class="content-with-sidebar">
		<div class="post-flow">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;

				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '←',
						'next_text' => '→',
					)
				);
				?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
