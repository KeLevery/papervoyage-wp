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
			<span class="bar" style="width:3px;height:18px;background:var(--brand);border-radius:2px"></span>
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
		<p class="no-comments" style="color:var(--muted);font-size:13px"><?php esc_html_e( '评论已关闭。', 'papervoyage' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'         => __( '留下你的声音', 'papervoyage' ) . ' <span class="en-sub" style="margin-left:8px">' . __( 'Leave a Whisper', 'papervoyage' ) . '</span>',
			'title_reply_before'  => '<h3 class="comment-reply-title">',
			'title_reply_after'   => '</h3>',
			'comment_field'       => '<p class="comment-form-comment"><label for="comment">' . __( '评论内容', 'papervoyage' ) . '</label><textarea id="comment" name="comment" rows="5" maxlength="2000" required></textarea></p>',
			'label_submit'        => __( '发布评论', 'papervoyage' ),
			'comment_notes_before' => '<p class="comment-notes" style="font-size:12px;color:var(--muted)">' . __( '邮箱地址不会被公开。友好交流，寒来暑往，人聚又散。', 'papervoyage' ) . '</p>',
		)
	);
	?>

</div>
