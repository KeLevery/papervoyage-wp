<?php
/**
 * 分类与标签筛选条（列表页 / 归档页）
 *
 * @package PaperVoyage
 */

$cats = get_categories( array( 'hide_empty' => true, 'number' => 12 ) );
$tags = get_tags( array( 'hide_empty' => true, 'number' => 16, 'orderby' => 'count', 'order' => 'DESC' ) );

$current_cat = is_category() ? get_queried_object_id() : 0;
$current_tag = is_tag() ? get_queried_object_id() : 0;

if ( ! $cats && ! $tags ) {
	return;
}
?>
<div class="filter-bar reveal">
	<?php if ( $cats ) : ?>
		<span class="en-sub"><?php esc_html_e( 'Filter by Category', 'papervoyage' ); ?></span>
		<div class="tag-pills" style="margin-bottom:14px">
			<?php
			$posts_page_id = (int) get_option( 'page_for_posts' );
			$all_posts_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
			?>
			<a class="tag-pill <?php echo ( ! is_category() && ! is_tag() ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $all_posts_url ); ?>">
				<?php esc_html_e( '全部', 'papervoyage' ); ?>
			</a>
			<?php foreach ( $cats as $cat ) : ?>
				<a class="tag-pill <?php echo $current_cat === $cat->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>">
					<?php echo esc_html( $cat->name ); ?> <span class="n"><?php echo (int) $cat->count; ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $tags ) : ?>
		<span class="en-sub"><?php esc_html_e( 'Filter by Tag', 'papervoyage' ); ?></span>
		<div class="tag-pills">
			<?php foreach ( $tags as $tag ) : ?>
				<a class="tag-pill <?php echo $current_tag === $tag->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">
					# <?php echo esc_html( $tag->name ); ?> <span class="n"><?php echo (int) $tag->count; ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
