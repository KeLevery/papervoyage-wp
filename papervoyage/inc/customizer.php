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
