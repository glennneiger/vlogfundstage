<?php
/**
 * Plugin Name:  YouTube Channels
 * Plugin URI:   https://vlogfund.com/
 * Description:  Search YouTube Channels and Get Channel Logo
 * Version:      1.0
 * Author:       Vlogfund
 * Author URI:   https://vlogfund.com/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  youtube-channels
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !defined('YTC_PLUGIN_URL') ) :
	define('YTC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;
if( !defined('YTC_PLUGIN_PATH') ) :
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
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'yt_channels';
	$charset_collate = $wpdb->get_charset_collate();
	
    // Activation code here...
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		 `id` int(11) NOT NULL,
		 `channelid` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `logo` varchar(500) CHARACTER SET latin1 NOT NULL,
		 `name` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `country` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `subscribers` bigint(20) NOT NULL,
		 `views` bigint(20) NOT NULL,
		 `topics` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `bio` varchar(255) CHARACTER SET latin1 NOT NULL,
		 `twitter` varchar(255) NOT NULL,
		 `instagram` varchar(255) NOT NULL
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