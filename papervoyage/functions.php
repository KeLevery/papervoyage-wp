<?php
/**
 * 纸旅 PaperVoyage 主题功能文件
 *
 * @package PaperVoyage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAPERVOYAGE_VERSION', '1.4.0' );
define( 'PAPERVOYAGE_DIR', get_template_directory() );
define( 'PAPERVOYAGE_URI', get_template_directory_uri() );

/**
 * 主题初始化
 */
function papervoyage_setup() {
	load_theme_textdomain( 'papervoyage', PAPERVOYAGE_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// 特色图片尺寸
	set_post_thumbnail_size( 1200, 520, true );
	add_image_size( 'papervoyage-card', 800, 450, true );

	// 菜单
	register_nav_menus(
		array(
			'topbar'  => __( '顶部工具条菜单', 'papervoyage' ),
			'primary' => __( '主导航菜单', 'papervoyage' ),
			'footer'  => __( '页脚菜单', 'papervoyage' ),
		)
	);

	// 启用菜单项「描述」字段（用作英文副标题）
	add_filter( 'wp_setup_nav_menu_item', 'papervoyage_enable_menu_description' );
}
add_action( 'after_setup_theme', 'papervoyage_setup' );

function papervoyage_enable_menu_description( $menu_item ) {
	if ( isset( $menu_item->description ) ) {
		$menu_item->description = apply_filters( 'nav_menu_description', trim( $menu_item->description ) );
	}
	return $menu_item;
}

/**
 * 资源加载
 */
function papervoyage_assets() {
	// Google Fonts：中文黑体 + 宋体（引文）
	wp_enqueue_style(
		'papervoyage-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700&family=Noto+Serif+SC:wght@400;600&display=optional',
		array(),
		null
	);

	wp_enqueue_style( 'papervoyage-style', get_stylesheet_uri(), array( 'papervoyage-fonts' ), PAPERVOYAGE_VERSION );

	wp_enqueue_script( 'papervoyage-main', PAPERVOYAGE_URI . '/assets/js/main.js', array(), PAPERVOYAGE_VERSION, true );
	wp_localize_script(
		'papervoyage-main',
		'papervoyageData',
		array(
			'searchHint'       => __( '输入关键词，回车开始搜索…', 'papervoyage' ),
			'copied'           => __( '链接已复制', 'papervoyage' ),
			'commentReplySrc'  => site_url( '/wp-includes/js/comment-reply.min.js' ),
		)
	);
	wp_enqueue_script( 'papervoyage-pjax', PAPERVOYAGE_URI . '/assets/js/pjax.js', array( 'papervoyage-main' ), PAPERVOYAGE_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'papervoyage_assets' );

/**
 * 资源提示：字体域名 preconnect，缩短字体首用等待
 */
function papervoyage_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'papervoyage_resource_hints', 10, 2 );

/**
 * 布局稳定性（CLS）：移除前台 emoji 检测脚本。
 * twemoji 会把正文里的 emoji 文字异步替换为图片，造成加载后布局偏移。
 */
function papervoyage_disable_emoji_scripts() {
	if ( is_admin() ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
}
add_action( 'init', 'papervoyage_disable_emoji_scripts' );

/**
 * 首屏 Hero 图预加载，避免大图晚到造成首屏视觉闪变
 */
function papervoyage_preload_assets() {
	$hero = get_theme_mod( 'hero_image' );
	if ( $hero && is_front_page() ) {
		echo '<link rel="preload" as="image" href="' . esc_url( $hero ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'papervoyage_preload_assets', 2 );

/**
 * Speculation Rules：悬停时预取站内链接的 HTML（Chrome 支持，其余浏览器忽略）。
 * 点击时页面几乎即时渲染，消除多页应用跳转时的空白窗口。
 * 只用 prefetch 不用 prerender：避免页面在后台提前渲染、进场动画被吞掉。
 */
function papervoyage_speculation_rules() {
	if ( is_admin() ) {
		return;
	}
	echo '<script type="speculationrules">{"prefetch":[{"source":"document","where":{"href_matches":"/*"},"eagerness":"conservative"}]}</script>' . "\n";
}
add_action( 'wp_head', 'papervoyage_speculation_rules', 2 );

/**
 * 小工具区域
 */
function papervoyage_widgets_init() {
	$areas = array(
		'sidebar'    => array( __( '文章页侧栏', 'papervoyage' ), __( '显示在文章详情页与列表页右侧。', 'papervoyage' ) ),
		'footer-1'   => array( __( '页脚栏目一', 'papervoyage' ), '' ),
		'footer-2'   => array( __( '页脚栏目二', 'papervoyage' ), '' ),
		'footer-3'   => array( __( '页脚栏目三', 'papervoyage' ), '' ),
	);
	foreach ( $areas as $id => $area ) {
		register_sidebar(
			array(
				'name'          => $area[0],
				'id'            => $id,
				'description'   => $area[1],
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'papervoyage_widgets_init' );

/**
 * 摘要设置
 */
function papervoyage_excerpt_length() {
	return 48;
}
add_filter( 'excerpt_length', 'papervoyage_excerpt_length' );

function papervoyage_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'papervoyage_excerpt_more' );

/**
 * 标签云：限顶 8、等大 12px、纸面内联风
 */
function papervoyage_tag_cloud_args( $args ) {
	$args['number']    = 8;
	$args['smallest']  = 12;
	$args['largest']   = 12;
	$args['unit']      = 'px';
	$args['format']    = 'flat';
	$args['separator'] = ' · ';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'papervoyage_tag_cloud_args' );

/**
 * 加载辅助模块
 */
require PAPERVOYAGE_DIR . '/inc/template-functions.php';
require PAPERVOYAGE_DIR . '/inc/customizer.php';

/* ============================================================
   分类目录 Hero 图片上传框
   ============================================================ */

/**
 * 添加分类页面 — Hero 图片上传框
 */
function papervoyage_category_hero_field_add() {
	?>
	<div class="form-field">
		<label for="cat_hero_image"><?php esc_html_e( '栏目 Hero 图片', 'papervoyage' ); ?></label>
		<input type="hidden" id="cat_hero_image" name="cat_hero_image" value="">
		<div class="cat-hero-preview" style="margin-top:8px;max-width:400px;display:none">
			<img src="" alt="" style="width:100%;border-radius:8px;border:1px solid #ddd">
		</div>
		<button type="button" class="button cat-hero-upload-btn" style="margin-top:8px"><?php esc_html_e( '选择图片', 'papervoyage' ); ?></button>
		<button type="button" class="button cat-hero-remove-btn" style="margin-top:8px;display:none"><?php esc_html_e( '移除图片', 'papervoyage' ); ?></button>
		<p class="description"><?php esc_html_e( '上传一张大图作为该栏目页的 Hero 背景图（建议 1600×600 以上）。', 'papervoyage' ); ?></p>
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'papervoyage_category_hero_field_add' );

/**
 * 编辑分类页面 — Hero 图片上传框
 */
function papervoyage_category_hero_field_edit( $term ) {
	$hero_url = get_term_meta( $term->term_id, 'cat_hero_image', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="cat_hero_image"><?php esc_html_e( '栏目 Hero 图片', 'papervoyage' ); ?></label></th>
		<td>
			<input type="hidden" id="cat_hero_image" name="cat_hero_image" value="<?php echo esc_attr( $hero_url ); ?>">
			<div class="cat-hero-preview" style="margin-top:8px;max-width:400px;<?php echo $hero_url ? '' : 'display:none'; ?>">
				<img src="<?php echo esc_url( $hero_url ); ?>" alt="" style="width:100%;border-radius:8px;border:1px solid #ddd">
			</div>
			<button type="button" class="button cat-hero-upload-btn" style="margin-top:8px"><?php esc_html_e( '选择图片', 'papervoyage' ); ?></button>
			<?php if ( $hero_url ) : ?>
				<button type="button" class="button cat-hero-remove-btn" style="margin-top:8px"><?php esc_html_e( '移除图片', 'papervoyage' ); ?></button>
			<?php endif; ?>
			<p class="description"><?php esc_html_e( '上传一张大图作为该栏目页的 Hero 背景图（建议 1600×600 以上）。', 'papervoyage' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'papervoyage_category_hero_field_edit' );

/**
 * 保存分类 Hero 图片
 */
function papervoyage_save_category_hero_image( $term_id ) {
	if ( isset( $_POST['cat_hero_image'] ) ) {
		$url = esc_url_raw( wp_unslash( $_POST['cat_hero_image'] ) );
		update_term_meta( $term_id, 'cat_hero_image', $url );
	}
}
add_action( 'created_category', 'papervoyage_save_category_hero_image' );
add_action( 'edited_category', 'papervoyage_save_category_hero_image' );

/**
 * 分类编辑页 — 媒体上传器 JS
 */
function papervoyage_category_hero_uploader_js( $hook ) {
	if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	?>
	<script>
	jQuery(function($){
		var frame;
		$('.cat-hero-upload-btn').on('click', function(e){
			e.preventDefault();
			if(frame){ frame.open(); return; }
			frame = wp.media({ title: '<?php echo esc_js( __( '选择 Hero 图片', 'papervoyage' ) ); ?>', library:{type:'image'}, multiple:false });
			frame.on('select', function(){
				var url = frame.state().get('selection').first().toJSON().url;
				$('#cat_hero_image').val(url);
				$('.cat-hero-preview img').attr('src', url);
				$('.cat-hero-preview').show();
				$('.cat-hero-remove-btn').show();
			});
			frame.open();
		});
		$('.cat-hero-remove-btn').on('click', function(e){
			e.preventDefault();
			$('#cat_hero_image').val('');
			$('.cat-hero-preview').hide();
			$(this).hide();
		});
	});
	</script>
	<?php
}
add_action( 'admin_enqueue_scripts', 'papervoyage_category_hero_uploader_js' );
