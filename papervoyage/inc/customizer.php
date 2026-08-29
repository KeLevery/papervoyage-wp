<?php
/**
 * 自定义器：品牌色、Hero、社交链接等
 *
 * @package PaperVoyage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function papervoyage_customize_register( $wp_customize ) {

	/* ---------- 主题设置面板 ---------- */
	$wp_customize->add_panel(
		'papervoyage_panel',
		array(
			'title'    => __( '纸旅主题设置', 'papervoyage' ),
			'priority' => 30,
		)
	);

	/* ---------- 品牌 ---------- */
	$wp_customize->add_section(
		'papervoyage_brand',
		array(
			'title' => __( '品牌与配色', 'papervoyage' ),
			'panel' => 'papervoyage_panel',
		)
	);

	$wp_customize->add_setting(
		'accent_color',
		array(
			'default'           => '#ac706d',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'accent_color',
			array(
				'label'   => __( '品牌主色（豆沙红）', 'papervoyage' ),
				'section' => 'papervoyage_brand',
			)
		)
	);

	$wp_customize->add_setting(
		'brand_mark',
		array(
			'default'           => 'PAPERVOYAGE',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'brand_mark',
		array(
			'label'       => __( '顶部工具条品牌标记（英文）', 'papervoyage' ),
			'section'     => 'papervoyage_brand',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'site_desc_en',
		array(
			'default'           => 'The Beginning of Everything',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'site_desc_en',
		array(
			'label'   => __( '站点英文副标题', 'papervoyage' ),
			'section' => 'papervoyage_brand',
			'type'    => 'text',
		)
	);

	/* ---------- 首页 Hero ---------- */
	$wp_customize->add_section(
		'papervoyage_hero',
		array(
			'title' => __( '首页 Hero 区', 'papervoyage' ),
			'panel' => 'papervoyage_panel',
		)
	);

	$wp_customize->add_setting(
		'hero_image',
		array(
			'default'           => get_template_directory_uri() . '/assets/img/hero-default.jpg',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'hero_image',
			array(
				'label'       => __( 'Hero 头图（建议 1920×800，暖调摄影）', 'papervoyage' ),
				'section'     => 'papervoyage_hero',
			)
		)
	);

	$wp_customize->add_setting(
		'hero_title',
		array(
			'default'           => __( '把日子过成一本杂志', 'papervoyage' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'hero_title',
		array(
			'label'   => __( 'Hero 标题', 'papervoyage' ),
			'section' => 'papervoyage_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hero_subtitle_en',
		array(
			'default'           => 'PAPER · MINIMAL · POETRY',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'hero_subtitle_en',
		array(
			'label'   => __( 'Hero 英文副标题', 'papervoyage' ),
			'section' => 'papervoyage_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hero_quote',
		array(
			'default'           => __( '做自己，不随波逐流，不妥协。这世间，本就是寒来暑往，日出日落，人聚又散。', 'papervoyage' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'hero_quote',
		array(
			'label'   => __( '取景框引文（宋体呈现）', 'papervoyage' ),
			'section' => 'papervoyage_hero',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'hero_bubbles',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'hero_bubbles',
		array(
			'label'       => __( '显示弹幕气泡（取最新评论）', 'papervoyage' ),
			'section'     => 'papervoyage_hero',
			'type'        => 'checkbox',
		)
	);

	/* ---------- 分类归档设置 ---------- */
	$wp_customize->add_section(
		'papervoyage_category_options',
		array(
			'title' => __( '分类归档设置', 'papervoyage' ),
			'panel' => 'papervoyage_panel',
		)
	);

	$wp_customize->add_setting(
		'unify_category_cards',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'unify_category_cards',
		array(
			'label'       => __( '统一分类卡片为随笔（日志）格式', 'papervoyage' ),
			'description' => __( '开启后，各分类目录（杂文、游记等）文章列表均统一使用带左侧深色日期块的随笔卡片格式。', 'papervoyage' ),
			'section'     => 'papervoyage_category_options',
			'type'        => 'checkbox',
		)
	);

	/* ---------- 社交链接 ---------- */
	$wp_customize->add_section(
		'papervoyage_social',
		array(
			'title' => __( '社交链接', 'papervoyage' ),
			'panel' => 'papervoyage_panel',
		)
	);

	$socials = array(
		'weibo'   => __( '微博', 'papervoyage' ),
		'zhihu'   => __( '知乎', 'papervoyage' ),
		'github'  => 'GitHub',
		'bilibili' => __( '哔哩哔哩', 'papervoyage' ),
		'rss'     => 'RSS',
	);
	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting(
			"social_{$key}",
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			"social_{$key}",
			array(
				/* translators: %s: social name */
				'label'   => sprintf( __( '%s 链接', 'papervoyage' ), $label ),
				'section' => 'papervoyage_social',
				'type'    => 'url',
			)
		);
	}

	/* ---------- 关于我页面设置 ---------- */
	$wp_customize->add_section(
		'papervoyage_about',
		array(
			'title' => __( '关于我页面', 'papervoyage' ),
			'panel' => 'papervoyage_panel',
		)
	);

	$about_fields = array(
		'about_name'            => array( 'label' => __( '姓名 / 署名', 'papervoyage' ), 'default' => '陈纸旅 / Chen ZhiLv' ),
		'about_quote'           => array( 'label' => __( '个人引言金句', 'papervoyage' ), 'default' => '「写东西，是因为不写会生病。旅行，是因为不走去生病。摄影，是因为不拍会失去证据。」' ),
		'about_since'           => array( 'label' => __( '起步年份（Since）', 'papervoyage' ), 'default' => '2019.04' ),
		'about_now_in'          => array( 'label' => __( '常驻城市（Now In）', 'papervoyage' ), 'default' => '杭州' ),
		'about_camera'          => array( 'label' => __( '相机设备（Camera）', 'papervoyage' ), 'default' => 'Leica M6' ),
		'about_coffee'          => array( 'label' => __( '咖啡偏好（Coffee）', 'papervoyage' ), 'default' => '浅烘 · 果酸' ),
		'about_music'           => array( 'label' => __( '音乐偏好（Music）', 'papervoyage' ), 'default' => '坂本龙一' ),
		'about_signature'       => array( 'label' => __( '落款签名', 'papervoyage' ), 'default' => '纸旅，写于某个秋天的下午' ),
		'about_weibo_handle'    => array( 'label' => __( '微博账号名', 'papervoyage' ), 'default' => '@纸旅' ),
		'about_zhihu_handle'    => array( 'label' => __( '知乎账号名', 'papervoyage' ), 'default' => '@纸旅' ),
		'about_github_handle'   => array( 'label' => __( 'GitHub 账号名', 'papervoyage' ), 'default' => '@papervoyage' ),
		'about_bilibili_handle' => array( 'label' => __( 'B站账号名', 'papervoyage' ), 'default' => '@纸旅的旅行箱' ),
	);

	foreach ( $about_fields as $key => $meta ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $meta['default'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $meta['label'],
				'section' => 'papervoyage_about',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'papervoyage_customize_register' );

/**
 * 输出自定义品牌色（内联 CSS 变量覆盖）
 */
function papervoyage_custom_css() {
	$accent = get_theme_mod( 'accent_color', '#ac706d' );
	if ( '#ac706d' === strtolower( (string) $accent ) ) {
		return;
	}
	echo '<style>:root{--brand:' . esc_attr( $accent ) . ';}</style>' . "\n"; // phpcs:ignore
}
add_action( 'wp_head', 'papervoyage_custom_css', 20 );

/**
 * 获取已配置的社交链接
 */
function papervoyage_social_links() {
	$links = array();
	foreach ( array( 'weibo', 'zhihu', 'github', 'bilibili', 'rss' ) as $key ) {
		$url = get_theme_mod( "social_{$key}", '' );
		if ( $url ) {
			$links[ $key ] = $url;
		}
	}
	return $links;
}
