<?php
/**
 * 无内容提示
 *
 * @package PaperVoyage
 */
?>
<div class="post-card" style="padding:48px;text-align:center">
	<span class="en-sub" style="display:block;margin-bottom:10px"><?php esc_html_e( 'Nothing Here Yet', 'papervoyage' ); ?></span>
	<h2 style="font-size:20px"><?php esc_html_e( '这里还是一片空白', 'papervoyage' ); ?></h2>
	<p style="color:var(--muted);font-size:14px"><?php esc_html_e( '没有找到相关内容，换个关键词试试？', 'papervoyage' ); ?></p>
	<div style="max-width:380px;margin:20px auto 0">
		<?php get_search_form(); ?>
	</div>
</div>
