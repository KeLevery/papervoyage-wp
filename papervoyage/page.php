<?php
/**
 * 独立页面模板（关于我等）
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
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="single-head">
				<span class="en-sub"><?php echo esc_html( get_post_meta( get_the_ID(), '_papervoyage_en_sub', true ) ?: 'Page' ); ?></span>
				<h1><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="single-thumb">
					<?php the_post_thumbnail( 'full' ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
