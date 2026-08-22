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
	<header class="page-head">
		<span class="en-sub"><?php esc_html_e( 'The Archive of Everything', 'papervoyage' ); ?></span>
		<h1><?php the_title(); ?></h1>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php if ( get_the_content() ) : ?>
				<div class="desc entry-content" style="font-size:14px"><?php the_content(); ?></div>
			<?php endif; ?>
		<?php endwhile; endif; ?>
	</header>

	<div class="content-with-sidebar">
		<div class="archive-timeline reveal">
			<?php
			global $wpdb;
			$years = $wpdb->get_col(
				"SELECT DISTINCT YEAR(post_date) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC"
			);

			foreach ( $years as $year ) :
				$posts = new WP_Query(
					array(
						'posts_per_page'      => -1,
						'year'                => (int) $year,
						'ignore_sticky_posts' => true,
					)
				);
				?>
				<section class="archive-year">
					<h2><?php echo esc_html( $year ); ?></h2>
					<?php
					while ( $posts->have_posts() ) :
						$posts->the_post();
						get_template_part( 'template-parts/content', 'item' );
					endwhile;
					wp_reset_postdata();
					?>
				</section>
			<?php endforeach; ?>

			<?php if ( empty( $years ) ) : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
