<?php
/**
 * 纸旅 PaperVoyage 主题功能文件
 *
 * @package PaperVoyage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAPERVOYAGE_VERSION', '1.2.0' );
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
		'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700&family=Noto+Serif+SC:wght@400;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'papervoyage-style', get_stylesheet_uri(), array( 'papervoyage-fonts' ), PAPERVOYAGE_VERSION );

	wp_enqueue_script( 'papervoyage-main', PAPERVOYAGE_URI . '/assets/js/main.js', array(), PAPERVOYAGE_VERSION, true );
	wp_localize_script(
		'papervoyage-main',
		'papervoyageData',
		array(
			'searchHint' => __( '输入关键词，回车开始搜索…', 'papervoyage' ),
			'copied'     => __( '链接已复制', 'papervoyage' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'papervoyage_assets' );

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
