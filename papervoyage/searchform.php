<?php
/**
 * 搜索表单
 *
 * @package PaperVoyage
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="search-field-<?php echo esc_attr( wp_unique_id() ); ?>"><?php esc_html_e( '搜索：', 'papervoyage' ); ?></label>
	<input type="search" class="search-field" placeholder="<?php esc_attr_e( '搜索文章…', 'papervoyage' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off">
	<button type="submit" class="search-submit"><?php esc_html_e( '搜索', 'papervoyage' ); ?></button>
</form>
