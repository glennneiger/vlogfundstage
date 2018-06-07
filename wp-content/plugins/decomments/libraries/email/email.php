<?php

class DECOM_Email {

	public function notyfy_post_comments( $comment ) {
		if ( is_array( $comment ) ) {
			$comment = (object) $comment;
		}
		self::remove_all_actions_and_filters_before_send_mail();
		update_comment_meta( $comment->comment_ID, 'decomments_comment_send_after_appoved', 1 );
		ignore_user_abort( true );
		set_time_limit( 3600 );
		$decomments_notifications = apply_filters( 'decomments_get_notifications', '' );
		$post                     = get_post( $comment->comment_post_ID );
		$blog_name                = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$post_title               = apply_filters( 'the_title', $post->post_title );
		$comment_author           = $comment->comment_author;
		$comment_text             = apply_filters( 'decomments_comment_text', $comment->comment_content );
		$comment_post_url         = get_permalink( $comment->comment_post_ID );
		$comment_link             = $comment_post_url . '#comment-' . $comment->comment_ID;
		$comment_creation_date    = date( "Y-m-d H:i:s" );
		$site_name                = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
		$mail_my_comment          = '';

		if ( strpos( $site_name, '/' ) ) {
			$site_name = explode( '/', $site_name );
			$site_name = $site_name[0];
		}

		$headers = "Content-type: text/html, charset=utf8 \r\n";
		$headers .= "From: " . $blog_name . " <noreply@" . $site_name . ">\r\n";

		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );


		// Send email to comment
		if ( $comment->comment_parent && $mail_my_comment = get_comment_meta( $comment->comment_parent, '_decom_subscriber', true ) ) {
			if ( $mail_my_comment != $comment->comment_author_email ) {
				$messageMyComment = $decomments_notifications['email']['new_comment_to_comment'];
				$messagesComment  = self::substitution( $messageMyComment['title'], $messageMyComment['text'], $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $mail_my_comment, $comment->comment_post_ID, $comment->comment_ID, 1 );
				wp_mail( $mail_my_comment, $messagesComment['subject'], $messagesComment['text'], $headers );
			}
		}


		// Send email to post
		if ( $emails_list_post = get_post_meta( $comment->comment_post_ID, '_decom_subscribers', true ) ) {
			$messagePostComment = $decomments_notifications['email']['new_post_comment'];
			foreach ( $emails_list_post as $email ) {
				$email = trim( $email );
				if ( $email == $comment->comment_author_email || $email == $mail_my_comment ) {
					continue;
				}
				if ( $email ) {
					$messagesPost = self::substitution( $messagePostComment['title'], $messagePostComment['text'], $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $email, $comment->comment_post_ID, 0, 2 );

					wp_mail( $email, $messagesPost['subject'], $messagesPost['text'], $headers );

				}
			}
		}
	}

	public static function unit_test_notyfy_post_comments( $comment ) {
		if ( is_array( $comment ) ) {
			$comment = (object) $comment;
		}
		update_comment_meta( $comment->comment_ID, 'decomments_comment_send_after_appoved', 1 );
		ignore_user_abort( true );
		set_time_limit( 3600 );
		$decomments_notifications = apply_filters( 'decomments_get_notifications', '' );
		$post                     = get_post( $comment->comment_post_ID );
		$blog_name                = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$post_title               = apply_filters( 'the_title', $post->post_title );
		$comment_author           = $comment->comment_author;
		$comment_text             = apply_filters( 'decomments_comment_text', $comment->comment_content );
		$comment_post_url         = get_permalink( $comment->comment_post_ID );
		$comment_link             = $comment_post_url . '#comment-' . $comment->comment_ID;
		$comment_creation_date    = date( "Y-m-d H:i:s" );
		$site_name                = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
		$mail_my_comment          = '';

		if ( strpos( $site_name, '/' ) ) {
			$site_name = explode( '/', $site_name );
			$site_name = $site_name[0];
		}

		$headers = "Content-type: text/html, charset=utf8 \r\n";
		$headers .= "From: " . $blog_name . " <noreply@" . $site_name . ">\r\n";

		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );


		// Send email to comment
//		if ( $comment->comment_parent && $mail_my_comment = get_comment_meta( $comment->comment_parent, '_decom_subscriber', true ) ) {

		if ( $mail_my_comment === $comment->comment_author_email ) {
			return;
		}
//			$messageMyComment = $model_notifications->getNotificationLocale( 'new_comment_to_comment' );
		$messageMyComment = $decomments_notifications['email']['new_comment_to_comment'];
		$messagesComment  = self::substitution( $messageMyComment['title'], $messageMyComment['text'], $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $mail_my_comment, $comment->comment_post_ID, $comment->comment_ID, 1 );
//			wp_mail( $mail_my_comment, $messagesComment['subject'], nl2br( $messagesComment['text'] ), $headers );
		wp_mail( 'dim.romanenko@gmail.com', $messagesComment['subject'], $messagesComment['text'], $headers );
//		}

		// Send email to post
//		if ( $emails_list_post = get_post_meta( $comment->comment_post_ID, '_decom_subscribers', true ) ) {
//			$messagePostComment = $model_notifications->getNotificationLocale( 'new_post_comment' );
		$messagePostComment = $decomments_notifications['email']['new_post_comment'];
		$emails_list_post   = array( 'dim.romanenko@gmail.com' );
		foreach ( $emails_list_post as $email ) {
			if ( $email === $comment->comment_author_email || $email === $mail_my_comment ) {
				continue;
			}
			$messagesPost = self::substitution( $messagePostComment['title'], $messagePostComment['text'], $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $email, $comment->comment_post_ID, 0, 2 );

//				wp_mail( $email, $messagesPost['subject'], nl2br( $messagesPost['text'] ), $headers );
			wp_mail( $email, $messagesPost['subject'], $messagesPost['text'], $headers );

		}
//		}
	}

	public static function substitution( $message_title, $message_text, $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $email = '', $post_id = 0, $id = 0, $type = 0 ) {

		global $wp_rewrite;
		$message_title = stripslashes( $message_title );
		$message_text  = stripslashes( $message_text );

		$message['text']    = str_replace( "\r", "<br/>", $message_text );
		$message['text']    = str_replace( "\n", "<br/>", $message['text'] );
		$post_title         = str_replace( array( "&#39;", "'" ), "'", $post_title );
		$post_title         = str_replace( array( "&rsquo;" ), "’", $post_title );
		$post_title         = htmlspecialchars_decode( $post_title );
		$message['subject'] = preg_replace( '/%COMMENTED_POST_TITLE%/', $post_title, $message_title );
		$message_title      = $message['subject'];
		$message['text']    = preg_replace( '/%COMMENTED_POST_TITLE%/', '<strong>' . $post_title . '</strong>', $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_TEXT%/', '<strong>' . $comment_text . '</strong>', $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_AUTHOR%/', '<strong>' . $comment_author . '</strong>', $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_LINK%/', $comment_link, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENTED_POST_URL%/', $comment_post_url, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_CREATION_DATE%/', '<strong>' . $comment_creation_date . '</strong>', $message['text'] );

		if ( $wp_rewrite->using_permalinks() ) {
			$pref = '?';
		} else {
			$pref = '&';
		}

		if ( $type === 1 ) {
			$link = "{$pref}decomments_unsubscribe=" . md5( $email . 'decomments_unsubscribe_reply_comment' . $post_id . $id ) . '&_uid=' . $id;
		} else {
			$link = "{$pref}decomments_unsubscribe=" . md5( $email . 'decomments_unsubscribe_all_comments' . $post_id ) . '&_uid=' . $post_id;
		}

		$unsubscribe = '<strong>' . __( 'Unsubscribe', 'decomments' ) . '</strong><br>
' . __( 'To unsubscribe, just click', 'decomments' ) . ' <a href="' . $comment_post_url . $link . '" style="color: #222222; font-family: \'Helvetica\', \'Arial\', sans-serif;">' . __( 'here', 'decomments' ) . '</a>.';


		ob_start();
		include_once DECOM_Loader_MVC::getPathTheme() . 'parts/email.php';
		$html_message    = ob_get_clean();
		$message['text'] = $html_message;

		return $message;
	}

	public function notyfy_post_comment_admin( $comment_ID ) {
		$comment = get_comment( $comment_ID );
		self::remove_all_actions_and_filters_before_send_mail();
		ignore_user_abort( true );
		set_time_limit( 3600 );
		$decomments_notifications = apply_filters( 'decomments_get_notifications', '' );
		$post                     = get_post( $comment->comment_post_ID );
		$blog_name                = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$post_title               = apply_filters( 'the_title', $post->post_title );
		$comment_author           = $comment->comment_author;
		$comment_text             = apply_filters( 'decomments_comment_text', $comment->comment_content );
		$comment_post_url         = get_permalink( $comment->comment_post_ID );
		$comment_link             = $comment_post_url . '#comment-' . $comment->comment_ID;
		$comment_creation_date    = date( "Y-m-d H:i:s" );
		$site_name                = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );

		if ( strpos( $site_name, '/' ) ) {
			$site_name = explode( '/', $site_name );
			$site_name = $site_name[0];
		}

		$headers = "From: " . $blog_name . " <noreply@" . $site_name . ">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";


		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );

		$admin_email = get_option( 'admin_email' );

		$messageData = $decomments_notifications['email']['new_post_comment'];

		$messagesComment = self::substitution_admin(
			$messageData['title'],
			$messageData['text'],
			$post_title,
			$comment_author,
			$comment_text,
			$comment_link,
			$comment_post_url,
			$comment_creation_date,
			$post
		);

		wp_mail(
			$admin_email,
			__( 'New comment on your site', 'decomments' ),
			$messagesComment['text'],
			$headers
		);
	}

	public static function substitution_admin( $message_title, $message_text, $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $post ) {

		$message_title                   = stripslashes( $message_title );
		$message_text                    = stripslashes( $message_text );
		$admin_link_all_comments_by_post = admin_url( 'edit-comments.php?p=' . $post->ID );
		$message['text']                 = str_replace( "\r", "<br/>", $message_text );
		$message['text']                 = str_replace( "\n", "<br/>", $message['text'] );
		$post_title                      = str_replace( array( "&#39;", "'" ), "'", $post_title );
		$post_title                      = str_replace( array( "&rsquo;" ), "’", $post_title );
		$post_title                      = htmlspecialchars_decode( $post_title );
		$message['text']                 = preg_replace( '/%COMMENTED_POST_TITLE%/', '<strong>' . $post_title . '</strong>', $message['text'] );
		$message['text']                 = preg_replace( '/%COMMENT_TEXT%/', '<strong>' . $comment_text . '</strong>', $message['text'] );
		$message['text']                 = preg_replace( '/%COMMENT_AUTHOR%/', '<strong>' . $comment_author . '</strong>', $message['text'] );
		$message['text']                 = preg_replace( '/%COMMENT_LINK%/', $admin_link_all_comments_by_post, $message['text'] );
		$message['text']                 = preg_replace( '/%COMMENTED_POST_URL%/', $comment_post_url, $message['text'] );
		$message['text']                 = preg_replace( '/%COMMENT_CREATION_DATE%/', '<strong>' . $comment_creation_date . '</strong>', $message['text'] );


		ob_start();
		include_once DECOM_COMPONENTS_PATH . '/comments/views/admin-email.php';
		$message['text'] = ob_get_clean();

		return $message;
	}

	private static function remove_all_actions_and_filters_before_send_mail() {
		remove_all_actions( 'wp_mail', 999 );
		remove_all_filters( 'wp_mail', 999 );

		remove_all_actions( 'wp_mail_from', 999 );
		remove_all_filters( 'wp_mail_from', 999 );

		remove_all_actions( 'wp_mail_from_name', 999 );
		remove_all_filters( 'wp_mail_from_name', 999 );

		remove_all_actions( 'wp_mail_content_type', 999 );
		remove_all_filters( 'wp_mail_content_type', 999 );

		remove_all_actions( 'wp_mail_charset', 999 );
		remove_all_filters( 'wp_mail_charset', 999 );

		remove_all_actions( 'comment_form', 999 );
		remove_all_actions( 'phpmailer_init', 999 );
		remove_all_actions( 'wp_mail_failed', 999 );
		remove_all_actions( 'transition_comment_status', 999 );
		remove_all_filters( 'akismet_comment_nonce', 999 );

		remove_all_filters( 'comment_moderation_recipients', 999 );
		remove_all_filters( 'pre_comment_approved', 999 );
		remove_all_filters( 'akismet_get_api_key', 999 );
		remove_all_filters( 'akismet_spam_count_incr', 999 );
		remove_all_filters( 'akismet_ua', 999 );
		remove_all_actions( 'akismet_submit_nonspam_comment', 999 );

	}

}
