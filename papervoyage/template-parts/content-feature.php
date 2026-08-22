<?php
/**
 * 特色文章卡片（封面大图 + 底部蒙版标题）
 *
 * @package PaperVoyage
 */
?>
<a class="feature-card" href="<?php the_permalink(); ?>" id="post-<?php the_ID(); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<span class="thumb"><?php the_post_thumbnail( 'papervoyage-card' ); ?></span>
	<?php else : ?>
		<?php
		// 无特色图片时使用主题内置胶片感兜底图（按文章 ID 交替）
		$fallback = ( get_the_ID() % 2 )
			? '/assets/img/card-essay.jpg'
			: '/assets/img/card-film.jpg';
		?>
		<span class="thumb"><img src="<?php echo esc_url( get_template_directory_uri() . $fallback ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"></span>
	<?php endif; ?>
	<span class="cap">
		<?php
		$cats = get_the_category();
		if ( $cats ) :
			?>
			<span class="cat"><?php echo esc_html( $cats[0]->name ); ?></span>
		<?php endif; ?>
		<h3><?php the_title(); ?></h3>
		<span class="d"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?> · <?php echo esc_html( papervoyage_reading_time() ); ?></span>
	</span>
</a>
