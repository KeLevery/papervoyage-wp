<?php
/**
 * 列表页文章卡片
 *
 * @package PaperVoyage
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card-thumb" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'papervoyage-card' ); ?>
		</a>
	<?php endif; ?>
	<div class="card-body">
		<div class="card-meta">
			<?php papervoyage_temp_badge(); ?>
			<?php
			$cats = get_the_category();
			if ( $cats ) :
				?>
				<a class="cat-link" href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></time>
			<span><?php echo esc_html( papervoyage_reading_time() ); ?></span>
		</div>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<div class="card-foot">
			<a class="more-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More →', 'papervoyage' ); ?></a>
		</div>
	</div>
</article>
