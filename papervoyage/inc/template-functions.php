<?php
/**
 * 模板辅助函数：温度计数、阅读时长、社交分享、热榜、菜单 Walker 等
 *
 * @package PaperVoyage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================
   温度 °C（浏览量）系统
   ============================================================ */

/**
 * 获取文章温度（浏览量）
 */
function papervoyage_get_views( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$views   = (int) get_post_meta( $post_id, '_papervoyage_views', true );
	// 保底温度：让旧文章也有"余温"
	$base = (int) get_post_meta( $post_id, '_papervoyage_views_base', true );
	if ( ! $base ) {
		$base = 40 + ( $post_id % 120 );
		update_post_meta( $post_id, '_papervoyage_views_base', $base );
	}
	return $base + $views;
}

/**
 * 输出温度徽标
 */
function papervoyage_temp_badge( $post_id = null, $echo = true ) {
	$temp  = papervoyage_get_views( $post_id );
	$badge = '<span class="temp-badge" title="' . esc_attr__( '文章温度（浏览热度）', 'papervoyage' ) . '">' . esc_html( $temp ) . ' °C</span>';
	if ( $echo ) {
		echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return $badge;
}

/**
 * 文章页计数（每会话每篇仅计一次）
 */
function papervoyage_count_view() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}
	$counted = isset( $_COOKIE['papervoyage_viewed'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['papervoyage_viewed'] ) ) ) : array();
	if ( in_array( (string) $post_id, $counted, true ) ) {
		return;
	}
	$views = (int) get_post_meta( $post_id, '_papervoyage_views', true );
	update_post_meta( $post_id, '_papervoyage_views', $views + 1 );
	$counted[] = (string) $post_id;
	if ( ! headers_sent() ) {
		setcookie( 'papervoyage_viewed', implode( ',', array_slice( $counted, -50 ) ), time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}
}
add_action( 'wp', 'papervoyage_count_view' );

/* ============================================================
   阅读时长 / 字数
   ============================================================ */

function papervoyage_word_count( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() );
	$content = wp_strip_all_tags( strip_shortcodes( $content ) );
	// 中文字符 + 英文单词混合计数
	$chinese = preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $content );
	$english = str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $content ) );
	return $chinese + $english;
}

function papervoyage_reading_time( $post_id = null ) {
	$words   = papervoyage_word_count( $post_id );
	$minutes = max( 1, (int) ceil( $words / 400 ) );
	/* translators: %d: minutes */
	return sprintf( __( '约 %d 分钟', 'papervoyage' ), $minutes );
}

/* ============================================================
   主导航 Walker：中文名 + 英文副标题（菜单描述字段）
   ============================================================ */

/* 常用栏目中文 → 英文副标题兜底映射（菜单项未填「描述」时使用） */
function papervoyage_nav_en_fallback( $title ) {
	$map = array(
		'初页'     => 'First Page',
		'初頁'     => 'First Page',
		'首页'     => 'First Page',
		'信天翁'   => 'Albatross',
		'随笔'     => 'Essays',
		'雜文'     => 'Essays',
		'杂文'     => 'Notes',
		'游记'     => 'Travels',
		'遊記'     => 'Travels',
		'影视'     => 'Films',
		'影視'     => 'Films',
		'梦境'     => 'Dreams',
		'夢境'     => 'Dreams',
		'归档'     => 'Archive',
		'歸檔'     => 'Archive',
		'关于'     => 'About',
		'关于我'   => 'About',
		'留言'     => 'Guestbook',
		'留言板'   => 'Guestbook',
		'街坊·留言' => 'Guestbook',
	);
	$key = trim( (string) $title );
	return $map[ $key ] ?? '';
}

class PaperVoyage_Nav_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// 检查是否为首页链接：在首页时确保具有高亮 class
		$is_home_current = false;
		if ( is_front_page() || is_home() ) {
			$item_url = untrailingslashit( (string) $item->url );
			$home_url = untrailingslashit( home_url( '/' ) );
			$site_url = untrailingslashit( site_url( '/' ) );
			if ( $item_url === $home_url || $item_url === $site_url || $item->url === '/' || '' === $item_url || in_array( 'menu-item-home', $classes, true ) ) {
				$is_home_current = true;
				if ( ! in_array( 'current-menu-item', $classes, true ) ) {
					$classes[] = 'current-menu-item';
				}
				if ( ! in_array( 'current_page_item', $classes, true ) ) {
					$classes[] = 'current_page_item';
				}
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$output     .= '<li class="' . esc_attr( $class_names ) . '">';

		$is_current = $is_home_current
			|| in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current-category', $classes, true )
			|| in_array( 'current-menu-parent', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true );

		$atts = array(
			'href'  => ! empty( $item->url ) ? $item->url : '',
			'class' => $is_current ? 'active' : '',
		);
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$en    = '';
		if ( 0 === $depth ) {
			/* 优先用菜单项「描述」字段；未填时用内置映射兜底 */
			$en_text = ! empty( $item->description ) ? $item->description : papervoyage_nav_en_fallback( $title );
			$en      = $en_text ? '<span class="en">' . esc_html( $en_text ) . '</span>' : '';
		}

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '><span class="zh">' . esc_html( $title ) . '</span>' . $en . '</a>';
		$item_output .= $args->after ?? '';
		$output      .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/**
 * 确保在首页时，指向首页的菜单项必定包含 current-menu-item / current_page_item
 */
function papervoyage_nav_menu_home_class( $classes, $item ) {
	if ( is_front_page() || is_home() ) {
		$item_url = untrailingslashit( (string) $item->url );
		$home_url = untrailingslashit( home_url( '/' ) );
		$site_url = untrailingslashit( site_url( '/' ) );
		if ( $item_url === $home_url || $item_url === $site_url || $item->url === '/' || '' === $item_url || in_array( 'menu-item-home', $classes, true ) ) {
			if ( ! in_array( 'current-menu-item', $classes, true ) ) {
				$classes[] = 'current-menu-item';
			}
			if ( ! in_array( 'current_page_item', $classes, true ) ) {
				$classes[] = 'current_page_item';
			}
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'papervoyage_nav_menu_home_class', 10, 2 );

/* ============================================================
   社交分享
   ============================================================ */

function papervoyage_share_buttons( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$url     = rawurlencode( get_permalink( $post_id ) );
	$title   = rawurlencode( get_the_title( $post_id ) );

	$links = array(
		'weibo'  => array(
			'label' => __( '微博', 'papervoyage' ),
			'url'   => "https://service.weibo.com/share/share.php?url={$url}&title={$title}",
			'icon'  => '<circle cx="12" cy="12" r="9"/><path d="M9 13.5c1.5-3 5-3.5 6-2"/>',
		),
		'x'      => array(
			'label' => 'X / Twitter',
			'url'   => "https://twitter.com/intent/tweet?url={$url}&text={$title}",
			'icon'  => '<path d="M4 4l16 16M20 4L4 20"/>',
		),
		'wechat' => array(
			'label' => __( '微信（复制链接）', 'papervoyage' ),
			'url'   => '#copy',
			'icon'  => '<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>',
		),
	);
	?>
	<div class="share-box">
		<span class="en-sub"><?php esc_html_e( 'Share · 分享本文', 'papervoyage' ); ?></span>
		<?php foreach ( $links as $key => $link ) : ?>
			<a class="share-btn share-<?php echo esc_attr( $key ); ?>"
			   href="<?php echo esc_url( $link['url'] ); ?>"
			   <?php echo '#copy' !== $link['url'] ? 'target="_blank" rel="noopener noreferrer"' : 'data-copy="' . esc_url( get_permalink( $post_id ) ) . '"'; ?>>
				<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $link['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
				<?php echo esc_html( $link['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/* ============================================================
   热榜（温度榜）
   ============================================================ */

function papervoyage_hot_list( $count = 5 ) {
	$posts = get_posts(
		array(
			'numberposts' => 40,
			'post_status' => 'publish',
		)
	);
	usort(
		$posts,
		function ( $a, $b ) {
			return papervoyage_get_views( $b->ID ) <=> papervoyage_get_views( $a->ID );
		}
	);
	$posts = array_slice( $posts, 0, $count );
	if ( ! $posts ) {
		return;
	}
	echo '<ol class="hot-list">';
	$i = 1;
	foreach ( $posts as $p ) {
		printf(
			'<li><span class="rank">%02d</span><span class="t"><a href="%s">%s</a></span>%s</li>',
			$i++,
			esc_url( get_permalink( $p ) ),
			esc_html( get_the_title( $p ) ),
			papervoyage_temp_badge( $p->ID, false )
		);
	}
	echo '</ol>';
}

/* ============================================================
   评论回调（定制评论区样式）
   ============================================================ */

function papervoyage_comment_callback( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent', $comment ); ?>>
		<article class="comment-body">
			<?php echo get_avatar( $comment, 40 ); ?>
			<div class="comment-main">
				<div class="comment-meta">
					<cite class="comment-author"><?php comment_author_link( $comment ); ?><span class="says"><?php esc_html_e( '说：', 'papervoyage' ); ?></span></cite>
					<a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>"><?php comment_date( 'Y-m-d H:i', $comment ); ?></time>
					</a>
					<?php edit_comment_link( __( '编辑', 'papervoyage' ), '<span class="edit-link">', '</span>' ); ?>
				</div>
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( '你的评论正在等待审核。', 'papervoyage' ); ?></p>
				<?php endif; ?>
				<div class="comment-content"><?php comment_text(); ?></div>
				<div class="reply">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'reply_text' => __( '回复', 'papervoyage' ),
							)
						)
					);
					?>
				</div>
			</div>
		</article>
	<?php
	// 注意：不输出闭合标签，Walker 会自动处理 end-callback
}

/* ============================================================
   文章 Meta 行
   ============================================================ */

function papervoyage_post_meta() {
	?>
	<div class="single-meta">
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y-m-d H:i' ) ); ?></time>
		<span class="sep"></span>
		<span><?php echo esc_html( papervoyage_reading_time() ); ?></span>
		<span class="sep"></span>
		<span>
			<?php
			/* translators: %d: word count */
			printf( esc_html__( '%d 字', 'papervoyage' ), (int) papervoyage_word_count() );
			?>
		</span>
		<span class="sep"></span>
		<?php papervoyage_temp_badge(); ?>
		<?php if ( comments_open() || get_comments_number() ) : ?>
			<span class="sep"></span>
			<a href="#comments">
				<?php
				/* translators: %d: comment count */
				printf( esc_html__( '%d 条评论', 'papervoyage' ), (int) get_comments_number() );
				?>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/* ============================================================
   栏目（分类）副标题：取分类描述的英文行
   分类描述写法约定：第一行为英文副标题，其余为中文简介。
   ============================================================ */

function papervoyage_term_en_sub( $term = null ) {
	$term  = $term ? get_term( $term ) : get_queried_object();
	if ( ! $term || is_wp_error( $term ) || empty( $term->description ) ) {
		return '';
	}
	$lines = array_values( array_filter( array_map( 'trim', explode( "\n", $term->description ) ) ) );
	return isset( $lines[0] ) ? $lines[0] : '';
}

/* ============================================================
   栏目专属视觉：Hero + 卡片渲染（5 套独立设计）
   ============================================================ */

/**
 * 栏目 Hero 区——按 slug 输出完全不同的视觉
 * 支持通过分类 meta 字段 cat_hero_image 设置自定义背景图
 */
function papervoyage_category_hero( $slug, $cn, $en, $desc = '' ) {
	// 拼音别名 → 栏目风格映射（兼容中文分类的拼音 slug，杂文统一为随笔风格）
	$alias = array(
		'suibi'    => 'albatross',
		'youji'    => 'travels',
		'yingshi'  => 'films',
		'zaowen'   => 'albatross',
		'zawen'    => 'albatross',
		'essays'   => 'albatross',
		'mengjing' => 'dreams',
	);
	$style_key = $alias[ $slug ] ?? $slug;

	// 读取分类 Hero 图片；未上传时用主题自带默认大图，保证 Hero 视觉不缺席
	$cat = get_queried_object();
	$hero_img = $cat ? get_term_meta( $cat->term_id, 'cat_hero_image', true ) : '';
	if ( ! $hero_img ) {
		$hero_img = get_template_directory_uri() . '/assets/img/hero-default.jpg';
	}
	$hero_style = ' style="background-image:url(\'' . esc_url( $hero_img ) . '\')"';

	switch ( $style_key ) {
		case 'albatross':
		case 'essays':
			$default_quote = ( 'essays' === $slug || 'zawen' === $slug || 'zaowen' === $slug )
				? '写东西，是因为不写会生病。这些是没生病的证据。'
				: '它们可以连续飞行几千公里不落地，但一旦起飞，就几乎不再回头。';
			$hero_quote = ! empty( $desc ) ? $desc : $default_quote;
			$default_en = ( 'essays' === $slug || 'zawen' === $slug || 'zaowen' === $slug ) ? 'Notes' : 'The Wandering Albatross';
			$hero_en = ! empty( $en ) ? $en : $default_en;
			?>
			<div class="cat-hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="wind-lines" aria-hidden="true"><svg viewBox="0 0 1200 340" preserveAspectRatio="none">
					<path d="M0,80 Q300,40 600,90 T1200,70" fill="none" stroke="rgba(180,200,215,.25)" stroke-width="1"/>
					<path d="M0,140 Q300,100 600,150 T1200,130" fill="none" stroke="rgba(180,200,215,.18)" stroke-width="1"/>
					<path d="M0,210 Q300,170 600,220 T1200,200" fill="none" stroke="rgba(180,200,215,.12)" stroke-width="1"/>
				</svg></div>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $hero_en ); ?></span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p><?php echo esc_html( $hero_quote ); ?></p>
				</div>
			</div>
			<?php
			break;
		case 'travels':
			?>
			<div class="cat-hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="route-dashed" aria-hidden="true"></div>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $en ? $en : 'Notes on the Road' ); ?></span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p>散步不是赶路，是把自己暂时交给一座城市，让它带你走。</p>
				</div>
			</div>
			<?php
			break;
		case 'films':
			?>
			<div class="cat-hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $en ? $en : 'Films' ); ?> · 影視</span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p>在黑暗里坐两小时，借别人的眼睛看一遍世界。</p>
				</div>
			</div>
			<div class="filmstrip" aria-hidden="true"></div>
			<?php
			break;
		case 'essays':
			?>
			<div class="cat-hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $en ? $en : 'Essays' ); ?> · 雜文</span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p>写东西，是因为不写会生病。这些是没生病的证据。</p>
				</div>
			</div>
			<?php
			break;
		case 'dreams':
			?>
			<div class="cat-hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="mist" aria-hidden="true">
					<span style="top:20%;left:15%;animation-delay:0s"></span>
					<span style="top:50%;left:40%;animation-delay:1.5s"></span>
					<span style="top:30%;left:70%;animation-delay:3s"></span>
					<span style="top:70%;left:25%;animation-delay:4.5s"></span>
					<span style="top:60%;left:80%;animation-delay:6s"></span>
				</div>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $en ? $en : 'Reality Is But a Dream' ); ?></span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p>庄周梦蝶，还是蝶梦庄周？这些是醒来后还记得的部分。</p>
				</div>
			</div>
			<?php
			break;
		default:
			?>
			<div class="cat-hero cat-hero-default"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="cat-hero-inner">
					<span class="en-sub"><?php echo esc_html( $en ? $en : 'Category' ); ?></span>
					<h1><?php echo esc_html( $cn ); ?></h1>
					<p><?php echo esc_html( $cat && $cat->description ? $cat->description : '「' . $cn . '」栏目下的全部文章。' ); ?></p>
				</div>
			</div>
			<?php
	}
}

/**
 * 栏目文章卡——按 slug 输出完全不同的卡片模板
 */
function papervoyage_category_card( $slug ) {
	$d = explode( '-', get_the_date( 'Y-m-d' ) );
	$link = get_permalink();
	$title = get_the_title();
	$excerpt = wp_trim_words( get_the_excerpt(), 30, '…' );
	$temp = papervoyage_get_views();
	$read = papervoyage_reading_time();
	$words = papervoyage_word_count();
	$thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'papervoyage-card' ) : '';

	// 拼音别名 → 栏目卡片风格映射（杂文统一使用随笔风格）
	$alias = array(
		'suibi'    => 'albatross',
		'youji'    => 'travels',
		'yingshi'  => 'films',
		'zaowen'   => 'albatross',
		'zawen'    => 'albatross',
		'essays'   => 'albatross',
		'mengjing' => 'dreams',
	);
	$slug = $alias[ $slug ] ?? $slug;

	// 后台开启「统一分类卡片为随笔/日志卡片样式」或当前为随笔/杂文时，输出日志卡片样式
	$force_log_card = get_theme_mod( 'unify_category_cards', true );
	if ( $force_log_card || 'albatross' === $slug || 'essays' === $slug ) {
		?>
		<article class="log-card">
			<a class="log-date" href="<?php echo esc_url( $link ); ?>">
				<span class="day"><?php echo esc_html( $d[2] ); ?></span>
				<span class="mon"><?php echo esc_html( $d[1] ); ?>月</span>
			</a>
			<div class="log-body">
				<h3><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
				<p><?php echo esc_html( $excerpt ); ?></p>
				<div class="log-meta">
					<?php papervoyage_temp_badge(); ?>
					<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
					<span><?php echo esc_html( $read ); ?></span>
				</div>
			</div>
		</article>
		<?php
		return;
	}

	switch ( $slug ) {
		case 'albatross':
			?>
			<article class="log-card">
				<a class="log-date" href="<?php echo esc_url( $link ); ?>">
					<span class="day"><?php echo esc_html( $d[2] ); ?></span>
					<span class="mon"><?php echo esc_html( $d[1] ); ?>月</span>
				</a>
				<div class="log-body">
					<h3><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
					<p><?php echo esc_html( $excerpt ); ?></p>
					<div class="log-meta">
						<?php papervoyage_temp_badge(); ?>
						<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
						<span><?php echo esc_html( $read ); ?></span>
					</div>
				</div>
			</article>
			<?php
			break;
		case 'travels':
			?>
			<article class="postcard">
				<div class="stamp"><span class="big"><?php echo esc_html( $d[2] ); ?></span><?php echo esc_html( $d[1] ); ?>月</div>
				<h3><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
				<p><?php echo esc_html( $excerpt ); ?></p>
				<div class="pc-meta">
					<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span> ·
					<span><?php echo esc_html( $read ); ?></span> ·
					<?php papervoyage_temp_badge(); ?>
				</div>
			</article>
			<?php
			break;
		case 'films':
			?>
			<article class="screening">
				<?php if ( $thumb ) : ?>
					<a class="scr-thumb" href="<?php echo esc_url( $link ); ?>">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>">
					</a>
				<?php endif; ?>
				<div class="scr-body">
					<h3><a href="<?php echo esc_url( $link ); ?>" style="color:inherit"><?php echo esc_html( $title ); ?></a></h3>
					<p><?php echo esc_html( $excerpt ); ?></p>
					<div class="scr-meta">
						<span class="runtime"><?php echo esc_html( $read ); ?> MIN</span>
						<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
						<span><?php echo esc_html( $temp ); ?> °C</span>
					</div>
				</div>
			</article>
			<?php
			break;
		case 'essays':
			?>
			<article class="manuscript">
				<div class="ms-date"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></div>
				<h3><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
				<p class="ms-excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<div class="ms-foot">
					<?php papervoyage_temp_badge(); ?>
					<span><?php echo esc_html( $read ); ?></span>
					<span><?php echo esc_html( $words ); ?> 字</span>
					<a class="more-link" style="margin-left:auto" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( '阅读全文 →', 'papervoyage' ); ?></a>
				</div>
			</article>
			<?php
			break;
		case 'dreams':
			static $dream_idx = 0;
			$dream_idx++;
			?>
			<article class="dream-card">
				<div class="dream-num"><?php printf( esc_html__( '梦境记录 #%02d', 'papervoyage' ), $dream_idx ); ?></div>
				<h3><a href="<?php echo esc_url( $link ); ?>" style="color:inherit"><?php echo esc_html( $title ); ?></a></h3>
				<p><?php echo esc_html( $excerpt ); ?></p>
				<div class="dream-meta">
					<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span> ·
					<span><?php echo esc_html( $read ); ?></span> ·
					<?php papervoyage_temp_badge(); ?>
				</div>
			</article>
			<?php
			break;
		default:
			$d = explode( '-', get_the_date( 'Y-m-d' ) );
			?>
			<article class="log-card">
				<a class="log-date" href="<?php echo esc_url( get_permalink() ); ?>">
					<span class="day"><?php echo esc_html( $d[2] ); ?></span>
					<span class="mon"><?php echo esc_html( $d[1] ); ?>月</span>
				</a>
				<div class="log-body">
					<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
					<div class="log-meta">
						<?php papervoyage_temp_badge(); ?>
						<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
						<span><?php echo esc_html( papervoyage_reading_time() ); ?></span>
					</div>
				</div>
			</article>
			<?php
	}
}

/**
 * body_class 过滤器：分类页加 cat-{slug} 类
 */
function papervoyage_category_body_class( $classes ) {
	if ( is_category() ) {
		$cat = get_queried_object();
		if ( $cat && $cat->slug ) {
			$classes[] = 'cat-' . $cat->slug;
			// 拼音别名也映射到对应栏目风格类，让专属 CSS 生效（杂文统一映射为 albatross 随笔风格）
			$alias = array(
				'suibi'    => 'albatross',
				'youji'    => 'travels',
				'yingshi'  => 'films',
				'zaowen'   => 'albatross',
				'zawen'    => 'albatross',
				'essays'   => 'albatross',
				'mengjing' => 'dreams',
			);
			if ( isset( $alias[ $cat->slug ] ) ) {
				$style_class = 'cat-' . $alias[ $cat->slug ];
				if ( ! in_array( $style_class, $classes, true ) ) {
					$classes[] = $style_class;
				}
			}
		}
	}
	return $classes;
}
add_filter( 'body_class', 'papervoyage_category_body_class' );
