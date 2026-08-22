<?php
/**
 * 侧栏：温度热榜（内置）+ 小工具区域
 *
 * @package PaperVoyage
 */
?>
<aside class="sidebar" aria-label="<?php esc_attr_e( '侧栏', 'papervoyage' ); ?>">

	<!-- 内置：温度热榜 -->
	<section class="widget">
		<h3 class="widget-title"><?php esc_html_e( '温度榜', 'papervoyage' ); ?><span class="en-sub" style="margin-left:8px"><?php esc_html_e( 'Hottest', 'papervoyage' ); ?></span></h3>
		<?php papervoyage_hot_list( 5 ); ?>
	</section>

	<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php else : ?>
		<!-- 默认小工具：分类与搜索 -->
		<section class="widget">
			<h3 class="widget-title"><?php esc_html_e( '栏目', 'papervoyage' ); ?></h3>
			<ul>
				<?php
				wp_list_categories(
					array(
						'title_li' => '',
						'show_count' => true,
					)
				);
				?>
			</ul>
		</section>
		<section class="widget">
			<h3 class="widget-title"><?php esc_html_e( '搜索', 'papervoyage' ); ?></h3>
			<?php get_search_form(); ?>
		</section>
	<?php endif; ?>
</aside>
