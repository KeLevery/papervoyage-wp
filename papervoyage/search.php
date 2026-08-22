<?php
/**
 * 搜索结果页
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<span class="en-sub"><?php esc_html_e( 'Search Results', 'papervoyage' ); ?></span>
		<h1>
			<?php
			/* translators: %s: search query */
			printf( esc_html__( '「%s」的搜索结果', 'papervoyage' ), esc_html( get_search_query() ) );
			?>
		</h1>
		<p class="desc">
			<?php
			global $wp_query;
			/* translators: %d: result count */
			printf( esc_html__( '共找到 %d 篇相关文章。', 'papervoyage' ), (int) $wp_query->found_posts );
			?>
		</p>
	</header>

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
