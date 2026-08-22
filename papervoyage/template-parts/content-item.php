<?php
/**
 * 紧凑文章条目（温度徽标 + 标题 + 等宽日期）
 *
 * @package PaperVoyage
 */
?>
<div class="post-item">
	<?php papervoyage_temp_badge(); ?>
	<span class="t"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
	<span class="d"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
</div>
