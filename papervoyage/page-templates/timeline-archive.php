<?php
/**
 * Template Name: 全站归档（时间线）
 * Template Post Type: page
 *
 * 按年份分组的归档时间线页面。
 * 使用方法：新建页面 → 页面属性 → 模板选择「全站归档（时间线）」。
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
		<header class="page-head">
			<span class="en-sub"><?php esc_html_e( 'The Archive of Everything', 'papervoyage' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( get_the_content() ) : ?>
				<div class="desc entry-content" style="font-size:14px"><?php the_content(); ?></div>
			<?php endif; ?>
		</header>
	<?php endwhile; ?>

	<div class="content-with-sidebar">
		<div class="archive-timeline reveal">
			<?php
			$all_posts = new WP_Query(
				array(
					'posts_per_page'      => -1,
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'orderby'             => 'date',
					'order'               => 'DESC',
				)
			);

			$by_year = array();
			if ( $all_posts->have_posts() ) {
				while ( $all_posts->have_posts() ) {
					$all_posts->the_post();
					$year = get_the_date( 'Y' );
					$by_year[ $year ][] = get_post();
				}
				wp_reset_postdata();
			}

			if ( ! empty( $by_year ) ) :
				global $post;
				foreach ( $by_year as $year => $year_posts ) :
					?>
					<section class="archive-year">
						<h2><?php echo esc_html( $year ); ?></h2>
						<?php
						foreach ( $year_posts as $post ) :
							setup_postdata( $post );
							get_template_part( 'template-parts/content', 'item' );
						endforeach;
						wp_reset_postdata();
						?>
					</section>
				<?php endforeach; ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
