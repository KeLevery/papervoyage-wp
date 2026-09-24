<?php
/**
 * 归档模板（分类 / 标签 / 日期 / 作者）
 *
 * @package PaperVoyage
 */

get_header();

$term        = get_queried_object();
$en_subtitle = ( $term instanceof WP_Term ) ? papervoyage_term_en_sub( $term ) : '';
$default_en  = is_category() ? 'Category' : ( is_tag() ? 'Tagged With' : 'Archive' );
?>

<div class="wrap">
	<header class="page-head">
		<span class="en-sub"><?php echo esc_html( $en_subtitle ? $en_subtitle : $default_en ); ?></span>
		<h1><?php echo esc_html( get_the_archive_title() ); ?></h1>
		<?php
		$desc = get_the_archive_description();
		if ( $term instanceof WP_Term && ! empty( $term->description ) ) {
			$lines = array_values( array_filter( array_map( 'trim', explode( "\n", $term->description ) ) ) );
			if ( ! empty( $lines ) ) {
				if ( preg_match( '/[\x{4e00}-\x{9fff}]/u', $lines[0] ) ) {
					$desc = implode( "<br>", $lines );
				} elseif ( count( $lines ) > 1 ) {
					$desc = implode( "<br>", array_slice( $lines, 1 ) );
				}
			}
		}
		if ( $desc ) :
			?>
			<p class="desc"><?php echo wp_kses_post( $desc ); ?></p>
		<?php endif; ?>
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
