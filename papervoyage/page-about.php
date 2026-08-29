<?php
/**
 * Template Name: 关于我（全宽自由编辑）
 * Template Post Type: page
 *
 * 灵感源自 Demo 版本的专属「关于我」页面模板。
 * 允许在 WordPress 编辑器中 100% 自由排版与自定义文字、卡片和链接，
 * 同时解除普通单页 650px 限制，完美撑满 1200px 宽度，右侧不再留白。
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
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
</div>

<?php
get_footer();
