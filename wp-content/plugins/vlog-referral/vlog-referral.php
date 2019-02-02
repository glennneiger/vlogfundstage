<?php
/**
 * Plugin Name:  Vlog Referral
 * Plugin URI:   https://vlogfund.com/
 * Description:  Refer users and most referred upvoted user stand a chance to win prizes
 * Version:      1.0
 * Author:       Krunal Prajapati
 * Author URI:   https://profiles.wordpress.org/krunalprajapati41 
 * Text Domain:  vlog-referral
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $wpdb;
if( !defined('VLOGREF_PLUGIN_URL') ) : //Plugin URL
	define('VLOGREF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;
if( !defined('VLOGREF_PLUGIN_PATH') ) : //Plugin Path
	define('VLOGREF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
endif;
if( !defined('VLOG_REFERRAL_TABLE') ) : //Referral Table Name
	define('VLOG_REFERRAL_TABLE', $wpdb->prefix . 'vlog_referral' );
endif;
/**
 * Plugin Activation 
 * 
 * Handles to run on plugin activation
 * 
 * @since Vlog Referral 1.0
 **/
function vlogref_plugin_activation(){
	//Plugin Activation	
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	//Load Upgrade
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$referral_table = $wpdb->prefix.'vlog_referral';
	$sql = "CREATE TABLE IF NOT EXISTS $referral_table (
		 `id` int(11) NOT NULL AUTO_INCREMENT,
		 `user_id` int(11) NOT NULL,
		 `campaign` int(11) NOT NULL,
		 `referred_by` int(11) NOT NULL,
		 `registered` tinyint(1) DEFAULT NULL,
		 `upvoted` tinyint(1) NOT NULL DEFAULT '0',
		 `order_id` int(11) NOT NULL DEFAULT '0',
		 `amount` float NOT NULL DEFAULT '0',
		 `donated` tinyint(1) NOT NULL DEFAULT '0',
		 `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		 `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		 PRIMARY KEY (`id`)
		) $charset_collate;";
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'vlogref_plugin_activation' );
/**
 * Plugin Deactivation 
 * 
 * Handles to run on plugin deactivation
 * 
 * @since Vlog Referral 1.0
 **/
function vlogref_plugin_deactivation(){
	//Plugin Deactivation
}
register_activation_hook( __FILE__, 'vlogref_plugin_deactivation' );
if( !function_exists('vlogref_plugins_loaded') ) : 
/**
 * Load Plugin After All Plugin
 *
 * Handles to load plugin after all plugins loaded
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_plugins_loaded(){
	//Plugin Functions
	require_once VLOGREF_PLUGIN_PATH . 'includes/plugin-functions.php';
	//Admin Functions
	require_once VLOGREF_PLUGIN_PATH . 'includes/admin/class-ref-admin.php';
	//Public Functions
	require_once VLOGREF_PLUGIN_PATH . 'includes/public/class-ref-public.php';
}
add_action('plugins_loaded', 'vlogref_plugins_loaded');
endif; //Endif