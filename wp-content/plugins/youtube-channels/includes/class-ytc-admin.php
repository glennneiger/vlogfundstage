<?php
/**
 * Script Admin
 *
 * Handles all admin functions
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Admin') ) :

	class YTC_Admin{

		//Construct which run class
		function __construct(){
			
			//Add Menu Page
			add_action( 'admin_menu', array( $this, 'ytc_add_menu_pages' ) );
			//Admin Init for Import
			add_action( 'admin_init', array( $this, 'ytc_import_channels_submit' ) );
		}
		/**
		 * Add Menu Pages
		 **/
		public function ytc_add_menu_pages(){
			//Add Main Menu Page
			add_menu_page(
				__( 'YouTube Channels Options', 'youtube-channels' ),
				__( 'YouTube Channels', 'youtube-channels' ),
				'manage_options',
				'ytc-channels',
				'',//array( $this, 'ytc_add_menu_pages_callback' ),
				'dashicons-video-alt3'
			);
			
			//Add Import Menu Page
			add_submenu_page(
				'ytc-channels',
				__('Import Channels', 'youtube-channels'),
				__('Import Channels', 'youtube-channels'),
				'manage_options',
				'ytc-channels',
				array( $this, 'ytc_add_menu_pages_callback' )
			);			
		}
		/**
		 * Add Admin Pages
		 **/
		public function ytc_add_menu_pages_callback(){
			
			//Admin Import Page
			include_once( YTC_PLUGIN_PATH . 'includes/import.php');
			
		}		
	}
	//Run Class
	$ytc_admin = new YTC_Admin();
endif;