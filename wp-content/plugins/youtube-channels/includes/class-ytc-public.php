<?php
/**
 * Script Public
 *
 * Handles all public functions
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Public') ) :

	class YTC_Public{
		
		//Construct which run class
		function __construct(){		
			
			//Enqueue Scripts
			add_action( 'wp_enqueue_scripts', array( $this,'register_scripts' ) );		
		}
		
		/**
		* Enqueue All Scripts / Styles
		*
		* @since YouTube Channels 1.0
		**/
		public function register_scripts(){
			
			//Range Style
			wp_register_style( 'ytc-jquery-ui-style', 	'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), null );			
			//Bootstrap Style
			wp_register_style( 'ytc-bootstrap-style',	YTC_PLUGIN_URL . 'assets/css/bootstrap.min.css', array(), null );
			//Common Styles
			wp_register_style( 'ytc-styles', 			YTC_PLUGIN_URL . 'assets/css/styles.min.css', array(), null );
			//Select2 Style
			wp_register_style( 'ytc-select2-style',		YTC_PLUGIN_URL . 'assets/css/select2.min.css', array(), null );
			//App Style
			wp_register_style( 'ytc-app-style',			YTC_PLUGIN_URL . 'assets/css/app.css', array(), null );
			//Tooltipster Style
			wp_register_style( 'ytc-tooltipster-style',	YTC_PLUGIN_URL . 'assets/css/tooltipster.bundle.css', array(), null );
			//Range Style
			wp_register_style( 'ytc-range-style', 		YTC_PLUGIN_URL . 'assets/js/jquery.range.css', array(), null );
			
			//Core jQuery
			//wp_enqueue_script( array('jquery-ui-core') );
			//Popper Script
			wp_register_script( 'ytc-popper-script', 	YTC_PLUGIN_URL . 'assets/js/popper.min.js', array('jquery'), null, true );
			//Bootstrap Script
			wp_register_script( 'ytc-bootstrap-script',	YTC_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), null, true );
			//Select2 Script
			wp_register_script( 'ytc-select2-script',	YTC_PLUGIN_URL . 'assets/js/select2.min.js', array('jquery'), null, true );
			//Scripts
			wp_register_script( 'ytc-scripts',			YTC_PLUGIN_URL . 'assets/js/scripts.js', array('jquery'), null, true );
			//Tooltipster Script
			wp_register_script( 'ytc-tooltipster-script',YTC_PLUGIN_URL . 'assets/js/tooltipster.bundle.js', array('jquery'), null, true );
			//Script for Public Function
			wp_register_script( 'ytc-app-script', 		YTC_PLUGIN_URL . 'assets/js/app.js', array('jquery'), null, true );		
			wp_localize_script( 'ytc-app-script', 'YTC_Obj', array( 'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) ) );
		}
		
	}
	//Run Class
	$ytc_public = new YTC_Public();
endif;