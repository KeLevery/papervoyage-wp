<?php
/**
 * Template Name: 关于我（Demo 风格 / 全宽自由编辑）
 * Template Post Type: page
 *
 * 灵感源自 Demo 版本的专属「关于我」页面模板。
 * 顶部显示 Hero 封面大图与引言金句；
 * 正文支持双栏排版：左栏自述文字与签名，右栏展示 6 格数据徽章与社交链接；
 * 若在编辑器中使用原生 Columns 块排版，亦可自适应撑满 1200px 宽度。
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		$en_sub = get_post_meta( get_the_ID(), '_papervoyage_en_sub', true );
		if ( ! $en_sub ) {
			$en_sub = 'About · 關於我';
		}
		?>
		<header class="page-head reveal">
			<span class="en-sub"><?php echo esc_html( $en_sub ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="about-hero reveal" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>')">
				<div class="hero-content">
					<span class="en-sub" style="color:rgba(255,255,255,.75)"><?php esc_html_e( 'Who I Am · 我是', 'papervoyage' ); ?></span>
					<h1><?php echo esc_html( get_theme_mod( 'about_name', get_bloginfo( 'name' ) ) ); ?></h1>
					<?php
					$hero_quote = get_theme_mod( 'about_quote', '' );
					if ( ! $hero_quote ) {
						$hero_quote = get_theme_mod( 'hero_quote', '' );
					}
					if ( $hero_quote ) :
					?>
						<p class="hero-quote"><?php echo esc_html( $hero_quote ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<div style="height:36px"></div>
		<?php endif; ?>

		<div class="entry-content about-page-content reveal">
			<?php
			$raw_content = get_the_content();
			// 如果编辑器中已包含 Gutenberg 多列块，直接渲染让用户自由排版
			$has_columns = ( false !== strpos( $raw_content, 'wp-block-columns' ) || false !== strpos( $raw_content, 'about-grid' ) );

			if ( $has_columns ) :
				the_content();
			else :
				// 默认采用 Demo 同款双栏布局：左自述+签名，右6格数据徽章+社交链接
				?>
				<div class="about-grid">
					<div class="about-bio">
						<?php if ( trim( $raw_content ) ) : ?>
							<?php the_content(); ?>
						<?php else : ?>
							<p><?php esc_html_e( '你好，欢迎来到我的个人博客。这里记录着我走过的路、读过的书与看过的世界。', 'papervoyage' ); ?></p>
							<p><?php esc_html_e( '写东西，是因为不写会生病。摄影，是因为不拍会失去证据。如果你恰好也觉得还行，那就是最好的相遇。', 'papervoyage' ); ?></p>
						<?php endif; ?>

						<?php
						$signature = get_theme_mod( 'about_signature', '纸旅，写于某个秋天的下午' );
						if ( $signature ) :
							?>
							<p class="sig"><?php echo esc_html( $signature ); ?></p>
						<?php endif; ?>
					</div>

					<div class="about-side">
						<div class="about-meta">
							<?php
							$post_count = (int) wp_count_posts()->publish;
							$since      = get_theme_mod( 'about_since', '2019.04' );
							$now_in     = get_theme_mod( 'about_now_in', '杭州' );
							$camera     = get_theme_mod( 'about_camera', 'Leica M6' );
							$coffee     = get_theme_mod( 'about_coffee', '浅烘 · 果酸' );
							$music      = get_theme_mod( 'about_music', '坂本龙一' );
							?>
							<div class="cell"><span class="lab">Written</span><span class="val"><?php echo esc_html( $post_count ); ?> 篇</span></div>
							<?php if ( $since ) : ?><div class="cell"><span class="lab">Since</span><span class="val"><?php echo esc_html( $since ); ?></span></div><?php endif; ?>
							<?php if ( $now_in ) : ?><div class="cell"><span class="lab">Now In</span><span class="val"><?php echo esc_html( $now_in ); ?></span></div><?php endif; ?>
							<?php if ( $camera ) : ?><div class="cell"><span class="lab">Camera</span><span class="val"><?php echo esc_html( $camera ); ?></span></div><?php endif; ?>
							<?php if ( $coffee ) : ?><div class="cell"><span class="lab">Coffee</span><span class="val"><?php echo esc_html( $coffee ); ?></span></div><?php endif; ?>
							<?php if ( $music ) : ?><div class="cell"><span class="lab">Music</span><span class="val"><?php echo esc_html( $music ); ?></span></div><?php endif; ?>
						</div>

						<?php
						$social_items = array(
							'weibo'    => array( 'label' => __( '微博', 'papervoyage' ), 'handle' => get_theme_mod( 'about_weibo_handle', '@纸旅' ) ),
							'zhihu'    => array( 'label' => __( '知乎', 'papervoyage' ), 'handle' => get_theme_mod( 'about_zhihu_handle', '@纸旅' ) ),
							'github'   => array( 'label' => 'GitHub', 'handle' => get_theme_mod( 'about_github_handle', '@papervoyage' ) ),
							'bilibili' => array( 'label' => __( '哔哩哔哩', 'papervoyage' ), 'handle' => get_theme_mod( 'about_bilibili_handle', '@纸旅的旅行箱' ) ),
						);
						$configured_socials = papervoyage_social_links();
						$has_handles        = false;
						foreach ( $social_items as $item ) {
							if ( ! empty( $item['handle'] ) ) {
								$has_handles = true;
								break;
							}
						}
						if ( $has_handles ) :
							?>
							<div class="widget" style="margin-top:24px">
								<h3 class="widget-title"><?php esc_html_e( '街坊', 'papervoyage' ); ?> <span class="en-sub" style="margin-left:8px">Find me</span></h3>
								<ul>
									<?php foreach ( $social_items as $s_key => $s_data ) : ?>
										<?php if ( ! empty( $s_data['handle'] ) ) : ?>
											<?php $s_url = $configured_socials[ $s_key ] ?? ''; ?>
											<li>
												<?php if ( $s_url ) : ?>
													<a href="<?php echo esc_url( $s_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $s_data['label'] ); ?></a>
												<?php else : ?>
													<span><?php echo esc_html( $s_data['label'] ); ?></span>
												<?php endif; ?>
												<span class="n"><?php echo esc_html( $s_data['handle'] ); ?></span>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>

<?php
get_footer();
