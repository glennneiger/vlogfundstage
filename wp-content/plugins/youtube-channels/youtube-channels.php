<?php
/**
 * Plugin Name:  YouTube Channels
 * Plugin URI:   https://vlogfund.com/
 * Description:  Search YouTube Channels and Get Channel Logo
 * Version:      1.0
 * Author:       VlogFund
 * Author URI:   https://vlogfund.com/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  youtube-channels
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $wpdb;
if( !defined('YTC_PLUGIN_URL') ) : //Plugin URL
	define('YTC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;
if( !defined('YTC_PLUGIN_PATH') ) : //Plugin Path
	define('YTC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
endif;
/**
 * Plugin Activation 
 * 
 * Handles to run on plugin activation
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_plugin_activation() {
	//Plugin Activation	
}
register_activation_hook( __FILE__, 'ytc_plugin_activation' );
/**
 * Plugin Deactivation 
 * 
 * Handles to run on plugin deactivation
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_plugin_deactivation() {
	//Clear Cron Jobs on Deactivation of Plugin
	wp_clear_scheduled_hook('ytc_daily_update_channels');
}
register_activation_hook( __FILE__, 'ytc_plugin_deactivation' );
//Load Files
//Register Custom Post Types
//YouTube Channels
require_once( YTC_PLUGIN_PATH . 'includes/custom-posts/youtube-channels.php' );
//Common Functions
require_once( YTC_PLUGIN_PATH . 'includes/plugin-functions.php' );
//AJAX Callback
require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-ajax.php' );
//Class Shortcode
require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-shortcodes.php' );
//Class Admin
require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-admin.php' );