<?php
/**
 * 404 页面
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<div class="error-404">
		<div class="big">404</div>
		<span class="en-sub" style="display:block;margin:12px 0"><?php esc_html_e( 'Lost in the Paper Sea', 'papervoyage' ); ?></span>
		<h1 style="font-size:24px"><?php esc_html_e( '这一页被风吹走了', 'papervoyage' ); ?></h1>
		<p style="color:var(--muted)"><?php esc_html_e( '你要找的页面不存在，或者已被归档进了另一个梦境。', 'papervoyage' ); ?></p>
		<div style="max-width:420px;margin:24px auto 0">
			<?php get_search_form(); ?>
		</div>
		<p style="margin-top:24px"><a class="more-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← 回到首页', 'papervoyage' ); ?></a></p>
	</div>
</div>

<?php
get_footer();
