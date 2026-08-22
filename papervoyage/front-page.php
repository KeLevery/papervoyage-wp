<?php
/**
 * 首页模板：Hero + 波浪分隔 + 最新文章 + 栏目栅格
 *
 * @package PaperVoyage
 */

get_header();
?>

<!-- Hero -->
<section class="hero">
	<div class="wrap">
		<div class="hero-inner <?php echo get_theme_mod( 'hero_image' ) ? 'has-image' : ''; ?>"
			<?php if ( get_theme_mod( 'hero_image' ) ) : ?>
				style="background-image:url('<?php echo esc_url( get_theme_mod( 'hero_image' ) ); ?>')"
			<?php endif; ?>>

			<!-- 取景框直角 -->
			<div class="hero-corners" aria-hidden="true">
				<span class="c-tl"></span><span class="c-tr"></span><span class="c-bl"></span><span class="c-br"></span>
			</div>

			<!-- 弹幕气泡（取最新评论） -->
			<?php if ( get_theme_mod( 'hero_bubbles', true ) ) : ?>
				<div class="hero-bubbles" aria-hidden="true">
					<?php
					$bubble_comments = get_comments(
						array(
							'number' => 3,
							'status' => 'approve',
						)
					);
					$positions = array(
						array( 'top' => '14%', 'right' => '8%', 'delay' => '0s' ),
						array( 'top' => '38%', 'right' => '20%', 'delay' => '1.6s' ),
						array( 'top' => '22%', 'right' => '34%', 'delay' => '3.1s' ),
					);
					foreach ( $bubble_comments as $i => $bc ) :
						$pos = $positions[ $i % 3 ];
						?>
						<span class="hero-bubble" style="top:<?php echo esc_attr( $pos['top'] ); ?>;right:<?php echo esc_attr( $pos['right'] ); ?>;animation-delay:<?php echo esc_attr( $pos['delay'] ); ?>">
							<span class="who"><?php echo esc_html( $bc->comment_author ); ?></span><?php echo esc_html( wp_trim_words( $bc->comment_content, 12, '…' ) ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="hero-content">
				<span class="en-sub"><?php echo esc_html( get_theme_mod( 'hero_subtitle_en', 'PAPER · MINIMAL · POETRY' ) ); ?></span>
				<h1><?php echo esc_html( get_theme_mod( 'hero_title', __( '把日子过成一本杂志', 'papervoyage' ) ) ); ?></h1>
				<?php $quote = get_theme_mod( 'hero_quote' ); ?>
				<?php if ( $quote ) : ?>
					<p class="hero-quote">「<?php echo esc_html( $quote ); ?>」</p>
				<?php endif; ?>
			</div>
		</div>

		<!-- 四层波浪分隔 -->
		<div class="hero-waves" aria-hidden="true">
			<svg viewBox="0 0 1200 90" preserveAspectRatio="none">
				<g class="wave wf1"><path d="M0,50 C150,20 300,80 450,55 C600,30 750,75 900,50 C1050,25 1200,70 1350,50 C1500,30 1650,75 1800,55 C1950,35 2100,70 2250,50 L2250,90 L0,90 Z"/></g>
				<g class="wave w2 wf2"><path d="M0,60 C200,35 400,85 600,60 C800,40 1000,80 1200,60 C1400,40 1600,82 1800,60 C2000,42 2200,78 2400,60 L2400,90 L0,90 Z"/></g>
				<g class="wave w3 wf3"><path d="M0,68 C180,50 360,88 540,68 C720,52 900,86 1080,68 C1260,52 1440,86 1620,68 C1800,52 1980,84 2160,68 L2160,90 L0,90 Z"/></g>
				<path style="fill:var(--bg)" d="M0,78 C240,64 480,90 720,78 C960,66 1200,88 1440,78 C1680,68 1920,88 2160,78 L2160,90 L0,90 Z"/>
			</svg>
		</div>
	</div>
</section>

<!-- 最新文章 -->
<section class="home-section">
	<div class="wrap">
		<div class="sec-head reveal">
			<span class="bar"></span>
			<h2><?php esc_html_e( '最新文章', 'papervoyage' ); ?></h2>
			<span class="en-sub"><?php esc_html_e( 'Latest Articles', 'papervoyage' ); ?></span>
			<a class="more-link" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/?post_type=post' ) ); ?>"><?php esc_html_e( 'More →', 'papervoyage' ); ?></a>
		</div>

		<div class="home-grid">
			<!-- 左：特色文章（最近 2 篇带图） -->
			<div class="home-col reveal">
				<?php
				$featured = new WP_Query(
					array(
						'posts_per_page'      => 2,
						'ignore_sticky_posts' => true,
					)
				);
				while ( $featured->have_posts() ) :
					$featured->the_post();
					get_template_part( 'template-parts/content', 'feature' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<!-- 中：文章温度列表 -->
			<div class="home-col reveal">
				<?php
				$recent = new WP_Query(
					array(
						'posts_per_page'      => 8,
						'offset'              => 2,
						'ignore_sticky_posts' => true,
					)
				);
				while ( $recent->have_posts() ) :
					$recent->the_post();
					get_template_part( 'template-parts/content', 'item' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<!-- 右：标签 + 热榜 -->
			<div class="home-col reveal">
				<div class="sec-head" style="margin-bottom:12px">
					<span class="bar"></span>
					<h2 style="font-size:16px"><?php esc_html_e( '标签', 'papervoyage' ); ?></h2>
					<span class="en-sub"><?php esc_html_e( 'Tags', 'papervoyage' ); ?></span>
				</div>
				<div class="tag-cloud">
					<?php
					$tags = get_tags( array( 'number' => 8, 'orderby' => 'count', 'order' => 'DESC' ) );
					$tag_links = array();
					foreach ( $tags as $tag ) {
						$tag_links[] = '<a href="' . esc_url( get_tag_link( $tag ) ) . '">' . esc_html( $tag->name ) . '<span class="n">' . (int) $tag->count . '</span></a>';
					}
					echo implode( '<span class="sep">·</span>', $tag_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>

				<div class="sec-head" style="margin:28px 0 12px">
					<span class="bar"></span>
					<h2 style="font-size:16px"><?php esc_html_e( '本周热榜', 'papervoyage' ); ?></h2>
					<span class="en-sub"><?php esc_html_e( 'Trending', 'papervoyage' ); ?></span>
				</div>
				<?php papervoyage_hot_list( 6 ); ?>
			</div>
		</div>
	</div>
</section>

<!-- 栏目精选（按分类分区展示） -->
<section class="home-section">
	<div class="wrap">
		<div class="home-grid">
			<?php
			$cats = get_categories(
				array(
					'number'       => 3,
					'orderby'      => 'count',
					'order'        => 'DESC',
					'hide_empty'   => true,
				)
			);
			foreach ( $cats as $cat ) :
				$en = papervoyage_term_en_sub( $cat );
				?>
				<div class="home-col reveal">
					<div class="sec-head">
						<span class="bar"></span>
						<h2><?php echo esc_html( $cat->name ); ?></h2>
						<?php if ( $en ) : ?><span class="en-sub"><?php echo esc_html( $en ); ?></span><?php endif; ?>
						<a class="more-link" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php esc_html_e( 'More →', 'papervoyage' ); ?></a>
					</div>
					<?php
					$cat_posts = new WP_Query(
						array(
							'cat'                 => $cat->term_id,
							'posts_per_page'      => 5,
							'ignore_sticky_posts' => true,
						)
					);
					while ( $cat_posts->have_posts() ) :
						$cat_posts->the_post();
						get_template_part( 'template-parts/content', 'item' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
