<?php
/**
 * Settings - Notification messages
 *
 * @var string $view
 * @var object $addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="easyui-tabs" data-options="tools:'#decom-tools-save'" style="margin-right:14px;">
	<div style="padding:20px;">
		<div id="decom-supported-shortcodes" class="blogdescription">
			<div id="decom_shortcodes" class="decom-description">
				<table>
					<tr>
						<th colspan="2" style=""><?php esc_html_e( 'Supported shortcodes', 'decomments-admin' ); ?></php></th>
					</tr>
					<tr class="odd">
						<td>%COMMENT_AUTHOR%</td>
						<td><?php esc_html_e( "Comment's author", 'decomments-admin' ) ?></td>
					</tr>
					<tr>
						<td>%COMMENT_CREATION_DATE%</td>
						<td><?php esc_html_e( 'Comment creation date', 'decomments-admin' ) ?></td>
					</tr>
					<tr class="odd">
						<td>%COMMENT_TEXT%</td>
						<td><?php esc_html_e( 'Comment text', 'decomments-admin' ) ?></td>
					</tr>
					<tr>
						<td>%COMMENT_LINK%</td>
						<td><?php esc_html_e( 'Comment link', 'decomments-admin' ) ?></td>
					</tr>
					<tr class="odd">
						<td>%COMMENTED_POST_TITLE%</td>
						<td><?php esc_html_e( 'Post title to which was added a comment', 'decomments-admin' ) ?></td>
					</tr>
					<tr>
						<td>%COMMENTED_POST_URL%</td>
						<td><?php esc_html_e( 'Post link', 'decomments-admin' ) ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="decom-notifikation"><?php esc_html_e( 'New comment on post', 'decomments-admin' ); ?></div>
		<table class="decom-block-lang">
			<tbody>
			<tr>
				<td>
					<div class="decom-input-lang">
						<input class="easyui-validatebox" required="true" type="text" name="decomments_notifications[email][new_post_comment][title]" id="new_post_comment" value="<?php echo stripslashes( $notifications['new_post_comment']['title'] ); ?>" size="84">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<textarea class="easyui-validatebox" required="true" cols="87" rows="7" name="decomments_notifications[email][new_post_comment][text]"><?php echo stripslashes( $notifications['new_post_comment']['text'] ); ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="decom-notifikation"><?php esc_html_e( 'New comment on comment', 'decomments-admin' ); ?></div>
		<table class="decom-block-lang">
			<tbody>
			<tr>
				<td>
					<div class="decom-input-lang">
						<input class="easyui-validatebox" required="true" type="text" name="decomments_notifications[email][new_comment_to_comment][title]" id="new_comment_to_comment" value="<?php echo stripslashes( $notifications['new_comment_to_comment']['title'] ); ?>" size="84">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<textarea class="easyui-validatebox" required="true" cols="87" rows="7" name="decomments_notifications[email][new_comment_to_comment][text]"><?php echo stripslashes( $notifications['new_comment_to_comment']['text'] ); ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>