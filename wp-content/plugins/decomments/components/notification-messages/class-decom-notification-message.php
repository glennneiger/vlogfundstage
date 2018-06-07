<?php

/**
 * Nitification Message
 */
class DECOM_Notification_Message {

	private static $table_notification;
	private static $table_notification_labels;
	private static $table_notification_languages;
	private static $table_notification_values;
	private static $bundle_path;
	private static $bundle_url;
	public static $defaultLocale;
	public static $notifications_option = 'decomments_notifications';

	public static function init() {
		global $wpdb;
		self::$bundle_path                  = dirname( __FILE__ );
		self::$bundle_url                   = str_replace( ABSPATH, site_url() . '/', self::$bundle_path ) . '/';
		self::$table_notification           = $wpdb->prefix . 'decom_notifications';
		self::$table_notification_labels    = $wpdb->prefix . 'decom_notifications_labels';
		self::$table_notification_languages = $wpdb->prefix . 'decom_notifications_languages';
		self::$table_notification_values    = $wpdb->prefix . 'decom_notifications_values';
		self::$defaultLocale                = array( 'en_US' );

		add_action( 'decomments_activation', __CLASS__ . '::activate', 10 );
		add_filter( 'decomments_get_notifications', __CLASS__ . '::get_notifications', 10 );
		add_filter( 'comment_notification_recipients', __CLASS__ . '::disable_comment_notification_recipients', 10, 2 );
		add_action( 'decomments_settings_blocks_notification_messages', __CLASS__ . '::setting_block', 10 );
		add_action( 'wp_ajax_decom_edit_settings', __CLASS__ . '::save_notification', 10 );
		add_action( 'admin_print_scripts', __CLASS__ . '::assets' );

	}

	public static function activate() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS " . self::$table_notification . ";";
		$wpdb->query( $sql );
		$sql = "DROP TABLE IF EXISTS " . self::$table_notification_labels . ";";
		$wpdb->query( $sql );
		$sql = "DROP TABLE IF EXISTS " . self::$table_notification_languages . ";";
		$wpdb->query( $sql );
		$sql = "DROP TABLE IF EXISTS " . self::$table_notification_values . ";";
		$wpdb->query( $sql );
	}

	public static function assets() {
		wp_enqueue_style( 'decomments-settings-notification-style', self::$bundle_url . 'assets/css/decom-notification.css' );
	}

	public static function setting_block() {

		$decomments_notifications = self::get_notifications();
		$notifications            = $decomments_notifications['email'];
		include_once self::$bundle_path . '/views/html-settings-notification-messages.php';
	}

	public static function save_notification() {
		if ( isset( $_POST['decomments_notifications'] ) ) {
			$locale                          = get_locale();
			$decomments_notifications        = self::get_default_notifications();
			$decomments_notifications_single = $_POST['decomments_notifications'];

			$decomments_notifications[ $locale ] = $decomments_notifications_single;

			update_option( self::$notifications_option, json_encode( $decomments_notifications ) );
		}
	}

	private static function get_default_notifications() {

		/** ************* BEGIN ru_RU *************** */

		$default_params['ru_RU']['email']['new_post_comment']['title'] = "Новый комментарий к посту ''%COMMENTED_POST_TITLE%''";
		$default_params['ru_RU']['email']['new_post_comment']['text']  = "Новый комментарий к посту ''%COMMENTED_POST_TITLE%''
Автор: %COMMENT_AUTHOR%
Комментарий:
%COMMENT_TEXT%

Все комментарии к посту Вы можете увидеть здесь:
%COMMENT_LINK%";

		$default_params['ru_RU']['email']['new_comment_to_comment']['title'] = "Новый комментарий к комментарию. Пост ''%COMMENTED_POST_TITLE%''";
		$default_params['ru_RU']['email']['new_comment_to_comment']['text']  = "Новый комментарий к комментарию. Пост ''%COMMENTED_POST_TITLE%''
Автор: %COMMENT_AUTHOR%
Комментарий:
%COMMENT_TEXT%

Все комментарии к посту Вы можете увидеть здесь:
%COMMENT_LINK%";

		/** ************* END ru_RU *************** */


		/** ************* BEGIN en_US *************** */

		$default_params['en_US']['email']['new_post_comment']['title'] = "New comment on post ''%COMMENTED_POST_TITLE%''";
		$default_params['en_US']['email']['new_post_comment']['text']  = "New comment on post ''%COMMENTED_POST_TITLE%''
Author: %COMMENT_AUTHOR%
Comment:
%COMMENT_TEXT%

You can see all comments on this post here:
Permalink: %COMMENT_LINK%";

		$default_params['en_US']['email']['new_comment_to_comment']['title'] = "New comment on comment. ''%COMMENTED_POST_TITLE%'' post";
		$default_params['en_US']['email']['new_comment_to_comment']['text']  = "New comment on comment. ''%COMMENTED_POST_TITLE%'' post
Author: %COMMENT_AUTHOR%
Comment:
%COMMENT_TEXT%

You can see all comments on this post here:
Permalink: %COMMENT_LINK%";

		/** ************* END en_US *************** */


		/** ************* BEGIN uk *************** */

		$default_params['uk']['email']['new_post_comment']['title'] = "Новий коментар до посту ''%COMMENTED_POST_TITLE%''";
		$default_params['uk']['email']['new_post_comment']['text']  = "Новий коментар до посту ''%COMMENTED_POST_TITLE%''
Автор: %COMMENT_AUTHOR%
Коментар:
%COMMENT_TEXT%

Всі коментарі до посту Ви можете побачити тут:
%COMMENT_LINK%";

		$default_params['uk']['email']['new_comment_to_comment']['title'] = "Новий коментар до коментаря. Пост ''%COMMENTED_POST_TITLE%''";
		$default_params['uk']['email']['new_comment_to_comment']['text']  = "Новий коментар до коментаря. Пост ''%COMMENTED_POST_TITLE%''
Автор: %COMMENT_AUTHOR%
Коментар:
%COMMENT_TEXT%

Всі коментарі до посту Ви можете побачити тут:
%COMMENT_LINK%";

		$default_params['uk_UA']['email']['new_post_comment']['title'] = $default_params['uk']['email']['new_post_comment']['title'];
		$default_params['uk_UA']['email']['new_post_comment']['text']  = $default_params['uk']['email']['new_post_comment']['text'];

		$default_params['uk_UA']['email']['new_comment_to_comment']['title'] = $default_params['uk']['email']['new_comment_to_comment']['title'];
		$default_params['uk_UA']['email']['new_comment_to_comment']['text']  = $default_params['uk']['email']['new_comment_to_comment']['text'];

		/** ************* END uk *************** */

		return $default_params;
	}

	public static function get_notifications( $args = array() ) {
		$locale                   = get_locale();
		$decomments_notifications = get_option( self::$notifications_option );
		$decomments_notifications = json_decode( $decomments_notifications, true );

		//			$decomments_notifications = json_decode( stripcslashes( $decomments_notifications ), true );

		if ( isset( $decomments_notifications[ $locale ] ) ) {
			$decomments_notifications = $decomments_notifications[ $locale ];
		} else {
			$decomments_notifications = self::get_default_notifications();
			if ( isset( $decomments_notifications[ $locale ] ) ) {
				$decomments_notifications = $decomments_notifications[ $locale ];
			} else {
				$decomments_notifications = $decomments_notifications['en_US'];
			}
		}

		return $decomments_notifications;
	}

	public static function disable_comment_notification_recipients( $emails, $comment_ID ) {
		return array();
	}

}

DECOM_Notification_Message::init();
