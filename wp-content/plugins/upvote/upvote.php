<?php
/**
 * Plugin Name: Upvote
 * Plugin URI: https://profiles.wordpress.org/krunalprajapati41/
 * Description: a plugin to vote
 * Version: 1.0
 * Author: Krunal Prajapati
 * Author URI: https://profiles.wordpress.org/krunalprajapati41/
 * License: GPL3
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !defined('UPVOTE_PLUGIN_URL') ) :
	define('UPVOTE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
endif;
if( !defined('UPVOTE_ALLOWED_VOTES_GUEST') ) :
	define('UPVOTE_ALLOWED_VOTES_GUEST', 1 );
endif;

/**
 * Plugin Activation 
 * 
 * Handles to run on plugin activation
 * 
 * @since Upvote 1.0
 **/
function upvote_plugin_activation() {

    // Activation code here...
}
register_activation_hook( __FILE__, 'upvote_plugin_activation' );
/**
 * Plugin Deactivation 
 * 
 * Handles to run on plugin deactivation
 * 
 * @since Upvote 1.0
 **/
function upvote_plugin_deactivation() {

    // Activation code here...
}
register_activation_hook( __FILE__, 'upvote_plugin_deactivation' );
/**
 * Plugin Shortcodes
 **/
require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php' );
/**
 * Plugin Public Functions
 **/
require_once( plugin_dir_path( __FILE__ ) . 'includes/script-public.php' );