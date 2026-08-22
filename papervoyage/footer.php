<?php
/**
 * 页脚模板 + 右侧悬浮工具条
 *
 * @package PaperVoyage
 */
?>
</main><!-- .site-main -->

<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-brand">
				<p class="site-title"><?php bloginfo( 'name' ); ?><span style="color:var(--brand)">.</span></p>
				<span class="en-sub"><?php echo esc_html( get_theme_mod( 'site_desc_en', 'The Beginning of Everything' ) ); ?></span>
				<p style="margin-top:10px"><?php bloginfo( 'description' ); ?></p>
				<?php
				$socials = papervoyage_social_links();
				if ( $socials ) :
					$labels = array(
						'weibo'    => __( '微博', 'papervoyage' ),
						'zhihu'    => __( '知乎', 'papervoyage' ),
						'github'   => 'GitHub',
						'bilibili' => __( '哔哩哔哩', 'papervoyage' ),
						'rss'      => 'RSS',
					);
					?>
					<p class="footer-social">
						<?php foreach ( $socials as $key => $url ) : ?>
							<a class="more-link" style="margin-right:16px" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $labels[ $key ] ); ?></a>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
			</div>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( "footer-{$i}" ) ) : ?>
					<div class="footer-col">
						<?php dynamic_sidebar( "footer-{$i}" ); ?>
					</div>
				<?php endif; ?>
			<?php endfor; ?>
		</div>

		<div class="footer-bottom">
			<span class="en-sub"><?php echo esc_html( get_theme_mod( 'brand_mark', 'PAPERVOYAGE' ) ); ?> · Paper × Minimal × Poetry</span>
			<span>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> ·
				<?php esc_html_e( '由 纸旅 PaperVoyage 主题驱动', 'papervoyage' ); ?>
			</span>
		</div>
	</div>
</footer>

<!-- 右侧悬浮工具条 -->
<div class="float-tools" id="float-tools">
	<button class="icon-btn" id="back-to-top" aria-label="<?php esc_attr_e( '回到顶部', 'papervoyage' ); ?>">
		<svg viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
	</button>
</div>

<?php wp_footer(); ?>
</body>
</html>
