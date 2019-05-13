<?php
/**
 * Plugin Name:  Vlog Betterplace
 * Plugin URI:   https://vlogfund.com/
 * Description:  Import/Update Betterplace API Data
 * Version:      1.0
 * Author:       Krunal Prajapati
 * Author URI:   https://profiles.wordpress.org/krunalprajapati41 
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  vlog-betterplace
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $wpdb;
global $wpdb;
if( !defined('VLOGBET_PLUGIN_URL') ) : //Plugin URL
	define('VLOGBET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;
if( !defined('VLOGBET_PLUGIN_PATH') ) : //Plugin Path
	define('VLOGBET_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
endif;
if( !defined('VLOGBET_BETTERPLACE_ORG_API_URL') ) : //Betterplace Organization API URL
	define('VLOGBET_BETTERPLACE_ORG_API_URL', 'https://api.betterplace.org/de/api_v4/organisations.json');
endif;
/**
 * Plugin Activation 
 * 
 * Handles to run on plugin activation
 * 
 * @since Vlog Betterplace 1.0
 **/
function vlogbet_plugin_activation() {
	//Plugin Activation	
}
register_activation_hook( __FILE__, 'vlogbet_plugin_activation' );
/**
 * Plugin Deactivation 
 * 
 * Handles to run on plugin deactivation
 * 
 * @since Vlog Betterplace 1.0
 **/
function vlogbet_plugin_deactivation() {	
}
register_activation_hook( __FILE__, 'vlogbet_plugin_deactivation' );
//Admin Functions
require_once( VLOGBET_PLUGIN_PATH . 'includes/class-bet-admin.php' );