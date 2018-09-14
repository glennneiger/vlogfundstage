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
if( !defined('YTC_YOUTUBE_KEY') ) : //YouTube Key
	define('YTC_YOUTUBE_KEY', 'AIzaSyA7dxlViSTWdJGzgq-EhRcdiRKTU-FS2xA' );
endif;
/**
 * Plugin Activation 
 * 
 * Handles to run on plugin activation
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_plugin_activation() {
	global $wpdb;
	

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'yt_channels';
    // Activation code here...
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (		  
		 `id` int(11) NOT NULL AUTO_INCREMENT,
		 `channelid` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `logo` varchar(500) CHARACTER SET latin1 NOT NULL,
		 `name` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `country` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `subscribers` bigint(20) NOT NULL,
		 `views` bigint(20) NOT NULL,
		 `topics` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `bio` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `twitter` varchar(255) NOT NULL,
		 `instagram` varchar(255) NOT NULL,
		 `facebook` varchar(255) NOT NULL,
		 `gplus` varchar(255) NOT NULL,
		 `website` varchar(255) NOT NULL,
		 `snapchat` varchar(255) NOT NULL,
		 `vk` varchar(255) NOT NULL,
		 PRIMARY KEY (`id`)
		) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql ); //Run the Table Query
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

    // Activation code here...
}
register_activation_hook( __FILE__, 'ytc_plugin_deactivation' );
//Load Files
//Common Functions
require_once( YTC_PLUGIN_PATH . 'includes/plugin-functions.php' );
//AJAX Callback
require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-ajax.php' );
//Class Admin
//require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-admin.php' );
//Class Shortcode
require_once( YTC_PLUGIN_PATH . 'includes/class-ytc-shortcodes.php' );