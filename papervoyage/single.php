<?php
/**
 * 文章详情页：阅读体验优化排版 + 侧栏热榜
 *
 * @package PaperVoyage
 */

get_header();
?>

<div class="wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="single-head">
				<?php
				$cats = get_the_category();
				$en   = $cats ? papervoyage_term_en_sub( $cats[0] ) : '';
				?>
				<span class="en-sub"><?php echo esc_html( $en ? $en : __( 'A Paper Voyage', 'papervoyage' ) ); ?></span>
				<h1><?php the_title(); ?></h1>
				<?php papervoyage_post_meta(); ?>
			</header>

			<div class="single-layout">
				<div class="single-body">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="single-thumb">
							<?php the_post_thumbnail( 'full' ); ?>
						</figure>
					<?php endif; ?>

					<div class="entry-content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<div class="pagination">',
								'after'  => '</div>',
							)
						);
						?>
					</div>

					<?php $tags = get_the_tags(); ?>
					<?php if ( $tags ) : ?>
						<div class="entry-tags tag-pills">
							<?php foreach ( $tags as $tag ) : ?>
								<a class="tag-pill" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">
									# <?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php papervoyage_share_buttons(); ?>

					<nav class="post-nav" aria-label="<?php esc_attr_e( '文章导航', 'papervoyage' ); ?>">
						<?php
						$prev = get_previous_post();
						$next = get_next_post();
						?>
						<?php if ( $prev ) : ?>
							<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
								<span class="nav-label"><?php esc_html_e( '← 上一篇 · Older', 'papervoyage' ); ?></span>
								<span class="nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
							</a>
						<?php else : ?>
							<span></span>
						<?php endif; ?>
						<?php if ( $next ) : ?>
							<a class="nav-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
								<span class="nav-label"><?php esc_html_e( '下一篇 · Newer →', 'papervoyage' ); ?></span>
								<span class="nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
							</a>
						<?php endif; ?>
					</nav>

					<?php
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div>

				<?php get_sidebar(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
