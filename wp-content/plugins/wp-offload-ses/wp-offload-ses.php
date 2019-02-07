<?php
/*
Plugin Name: WP Offload SES
Description: Automatically send WordPress mail through Amazon SES (Simple Email Service).
Author: Delicious Brains
Version: 1.0.2
Author URI: https://deliciousbrains.com/
Network: True
Text Domain: wp-offload-ses
Domain Path: /languages/

// Copyright (c) 2018 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
*/

// Exit if accessed directly.
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Compatibility_Check;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['wposes_meta']['wp-offload-ses']['version'] = '1.0.2';

/**
 * Initiate the WP Offload SES plugin.
 */
function wp_offload_ses_init() {
	if ( class_exists( 'DeliciousBrains\WP_Offload_SES\WP_Offload_SES' ) ) {
		return;
	}

	/** @var WP_Offload_SES $wp_offload_ses */
	global $wp_offload_ses;

	/** @var Compatibility_Check $wposes_compat_check */
	global $wposes_compat_check;

	$abspath = dirname( __FILE__ );
	if ( WPMU_PLUGIN_DIR === $abspath ) {
		$abspath = $abspath . '/wp-offload-ses/';
	}

	require_once $abspath . '/classes/Compatibility-Check.php';
	$wposes_compat_check = new DeliciousBrains\WP_Offload_SES\Compatibility_Check( __FILE__ );

	if ( ! $wposes_compat_check->is_compatible() ) {
		return;
	}

	if ( $wposes_compat_check->is_plugin_active( 'wp-ses/wp-ses.php' ) ) {
		// Deactivate WP SES if activated.
		DeliciousBrains\WP_Offload_SES\Compatibility_Check::deactivate_other_instances( 'wp-offload-ses/wp-offload-ses.php' );
	}

	// Load autoloaders.
	require_once $abspath . '/vendor/Aws3/aws-autoloader.php';
	require_once $abspath . '/vendor/deliciousbrains/autoloader.php';
	require_once $abspath . '/classes/Autoloader.php';
	new DeliciousBrains\WP_Offload_SES\Autoloader( 'WP_Offload_SES', $abspath );

	// Kick off the plugin.
	$wp_offload_ses = new DeliciousBrains\WP_Offload_SES\WP_Offload_SES( __FILE__ );
}
add_action( 'init', 'wp_offload_ses_init', 1 );

/*
 * Override wp_mail if SES enabled
 *
 * We have to manually check if the sending option is enabled
 * as WP_Offload_SES is not available until the init hook.
 */
if ( ! function_exists( 'wp_mail' ) ) {
	$settings = get_site_option( 'wposes_settings' );
	if ( isset( $settings['send-via-ses'] ) && (bool) $settings['send-via-ses'] ) {
		/**
		 * Send mail via Amazon SES.
		 *
		 * @param string|array $to          Array or comma-separated list of email addresses.
		 * @param string       $subject     Email subject.
		 * @param string       $message     Email message.
		 * @param string|array $headers     Optional. Additional headers.
		 * @param string|array $attachments Optional. Files to attach.
		 *
		 * @return bool
		 */
		function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
			/** @var WP_Offload_SES $wp_offload_ses */
			global $wp_offload_ses;
			return $wp_offload_ses->mail_handler( $to, $subject, $message, $headers, $attachments );
		}
	}
} else {
	require_once dirname( __FILE__ ) . '/classes/Error.php';
	new DeliciousBrains\WP_Offload_SES\Error( DeliciousBrains\WP_Offload_SES\Error::$mail_function_exists, 'Mail function already overridden.' );
}
