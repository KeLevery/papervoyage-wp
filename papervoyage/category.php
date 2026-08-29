<?php
/**
 * 分类归档模板（5 个栏目各自独立的视觉风格）
 *
 * 通过 body_class 加 cat-{slug} 类触发各自专属 CSS。
 * 每个栏目有独立的 hero 区和文章卡渲染模板。
 *
 * @package PaperVoyage
 */

get_header();

$cat = get_queried_object();
$slug = $cat ? $cat->slug : '';
$cn = $cat ? $cat->name : '';
$en = papervoyage_term_en_sub( $cat );
$desc_lines = array();
if ( $cat && $cat->description ) {
	$desc_lines = array_values( array_filter( array_map( 'trim', explode( "\n", $cat->description ) ) ));
}
$desc = isset( $desc_lines[1] ) ? $desc_lines[1] : ( $cn ? '「' . $cn . '」栏目下的全部文章。' : '' );
?>

<div class="wrap">

		<!-- 栏目专属 Hero -->
		<?php papervoyage_category_hero( $slug, $cn, $en, $desc ); ?>

		<div style="height:40px"></div>

		<div class="content-with-sidebar">
			<div id="list-flow">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php papervoyage_category_card( $slug ); ?>
					<?php endwhile; ?>

					<div class="pagination">
						<?php
						echo paginate_links( array(
							'prev_text' => '←',
							'next_text' => '→',
							'mid_size'  => 2,
						) );
						?>
					</div>

				<?php else : ?>
					<div class="post-card" style="padding:48px;text-align:center">
						<span class="en-sub" style="display:block;margin-bottom:10px">Nothing Here Yet</span>
						<h2 style="font-size:20px">这里还是一片空白</h2>
						<p style="color:var(--muted);font-size:14px">这个栏目还没有文章。</p>
					</div>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>

<?php get_footer(); ?>
