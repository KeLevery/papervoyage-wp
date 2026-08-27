<?php
/**
 * 评论区模板（定制样式）
 *
 * @package PaperVoyage
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<span class="bar"></span>
			<?php
			printf(
				/* translators: %d: comment count */
				esc_html( _n( '%d 条评论', '%d 条评论', get_comments_number(), 'papervoyage' ) ),
				(int) get_comments_number()
			);
			?>
			<span class="en-sub"><?php esc_html_e( 'Neighborhood Voices', 'papervoyage' ); ?></span>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'callback'    => 'papervoyage_comment_callback',
					'style'       => 'ol',
					'avatar_size' => 40,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => __( '← 更早的评论', 'papervoyage' ),
				'next_text' => __( '更晚的评论 →', 'papervoyage' ),
			)
		);
		?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php esc_html_e( '评论已关闭。', 'papervoyage' ); ?></p>
	<?php endif; ?>

	<?php
	/* 未登录时的「昵称 / 邮箱 / 站点」三字段 —— CSS 里排成三列网格 */
	$pv_commenter   = wp_get_current_commenter();
	$pv_user        = wp_get_current_user();
	$pv_req_mark    = ' <span class="required-mark" aria-hidden="true">*</span>';

	$pv_fields = array(
		'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( '昵称', 'papervoyage' ) . $pv_req_mark . '</label>' .
			'<input id="author" name="author" type="text" placeholder="' . esc_attr__( '怎么称呼你', 'papervoyage' ) . '" value="' . esc_attr( $pv_commenter['comment_author'] ) . '" size="30" maxlength="245" autocomplete="name" required></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( '邮箱（不公开）', 'papervoyage' ) . $pv_req_mark . '</label>' .
			'<input id="email" name="email" type="email" placeholder="' . esc_attr__( '用于回复通知', 'papervoyage' ) . '" value="' . esc_attr( $pv_commenter['comment_author_email'] ) . '" size="30" maxlength="100" autocomplete="email" required></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( '站点（可选）', 'papervoyage' ) . '</label>' .
			'<input id="url" name="url" type="url" placeholder="' . esc_attr__( 'https://', 'papervoyage' ) . '" value="' . esc_attr( $pv_commenter['comment_author_url'] ) . '" size="30" maxlength="200" autocomplete="url"></p>',
		'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . checked( $pv_commenter['comment_author_email'] ? 1 : 0, 1, false ) . '>' .
			'<label for="wp-comment-cookies-consent">' . esc_html__( '记住我的昵称与邮箱', 'papervoyage' ) . '</label></p>',
	);

	comment_form(
		array(
			'title_reply'          => __( '留下你的声音', 'papervoyage' ) . ' <span class="en-sub">' . __( 'Leave a Whisper', 'papervoyage' ) . '</span>',
			'title_reply_before'   => '<h3 class="comment-reply-title">',
			'title_reply_after'    => '</h3>',
			'fields'               => $pv_fields,
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( '评论内容', 'papervoyage' ) . $pv_req_mark . '</label><textarea id="comment" name="comment" rows="5" maxlength="2000" placeholder="' . esc_attr__( '说点真实的——别客气，也别讨好。', 'papervoyage' ) . '" required></textarea></p>',
			'label_submit'         => __( '发布评论', 'papervoyage' ),
			'class_submit'         => 'submit',
			'logged_in_as'         => '<p class="comment-form-logged-in">' . sprintf(
				/* translators: 1: profile URL, 2: display name, 3: logout URL */
				__( '以 <a href="%1$s">%2$s</a> 的身份发言 · <a href="%3$s">注销</a>', 'papervoyage' ),
				esc_url( admin_url( 'profile.php' ) ),
				esc_html( $pv_user->display_name ),
				esc_url( wp_logout_url( get_permalink() ) )
			) . '</p>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( '邮箱地址不会被公开。友好交流，寒来暑往，人聚又散。', 'papervoyage' ) . '</p>',
			'comment_notes_after'  => '',
		)
	);
	?>

</div>
