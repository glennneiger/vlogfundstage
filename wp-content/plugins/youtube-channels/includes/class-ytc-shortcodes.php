<?php
/**
 * Shortcodes
 *
 * Handles all shortcodes of plugin
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Shortcodes') ) :

class YTC_Shortcodes{
	
	//Construct which run class
	function __construct(){		
	
		//Channels Shortcode
		add_shortcode( 'ytc_channels', array($this, 'channels_shortcode_callback') );
		
	}
	
	/**
	* Channels Shortcode
	* 
	* Handles to list all youtube channels with shortcode
	**/
	public function channels_shortcode_callback( $atts, $content = null ){
		extract( shortcode_atts( array(
			//Attributes Here
		), $atts, 'ytc_channels' ) );
	}
	
}
//Run Class
$ytc_shortcodes = new YTC_Shortcodes();
endif;