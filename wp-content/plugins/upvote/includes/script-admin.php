<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Admin Script Functions
 *
 * Handles to admin script functions
 *
 * @since Upvote 1.0
 **/
if( !function_exists('upvote_register_admin_menu_page') ) : 
/**
 * Register Admin Menu Page
 *
 * Handles to register admin menu page
 *
 * @since Upvote 1.0
 **/
function upvote_register_admin_menu_page() {
	//Add Menu Page
    add_submenu_page('edit.php?post_type=upvote-emails',
        __( 'Upvote Options', 'upvote' ),
        __('Options', 'upvote'),
        'manage_options',
        'upvote-options',
        'upvote_admin_menu_page'
    );
}
add_action( 'admin_menu', 'upvote_register_admin_menu_page' );
endif;
if( !function_exists('upvote_admin_menu_page') ) :
/**
 * Admin Menu Page Options
 *
 * Handles to add admin menu page options
 *
 * @since Upvote 1.0
 **/
function upvote_admin_menu_page(){
	
	//Plugin Options
	include_once( UPVOTE_PLUGIN_PATH . 'includes/options.php' );
	
}
endif;
if( !function_exists('upvote_register_plugin_settings') ) :
/**
 * Setting Options
 *
 * Handles to add setting options
 *
 * @since Upvote 1.0
 **/
function upvote_register_plugin_settings() {
	//Register Upvote Settings
    register_setting( 'upvote_plugin_options', 'upvote_options', 'upvote_options_validate' ); 
} 
add_action( 'admin_init', 'upvote_register_plugin_settings' );
endif;
if( !function_exists('upvote_options_validate') ) :
/**
 * Validate Options
 *
 * Handles to validate options
 *
 * @since Upvote 1.0
 **/
function upvote_options_validate( $input ){
	$upvote_options = upvote_get_option();
	//Guest Voting
	$input['enable_guest'] 		 	= isset( $input['enable_guest'] )			? 1 : 0;
	//Email Optin
	$input['enable_email_optin'] 	= isset( $input['enable_email_optin'] )		? 1 : 0;
	//Email Popup Trigger
	$input['email_popup_trigger']	= !empty( $input['email_popup_trigger'] )	? $input['email_popup_trigger'] : 3;
	//Email Optin Title
	$input['email_optin_title']		= !empty( $input['email_optin_title'] )		? $input['email_optin_title'] : __('Vote now and receive regular updates','upvote');
	//Email Optin Name Label
	$input['email_optin_name_label']= !empty( $input['email_optin_name_label'] )? $input['email_optin_name_label'] : __('Name','upvote');
	//Email Optin Name Label
	$input['email_optin_email_label']= !empty( $input['email_optin_email_label'] )	? $input['email_optin_email_label'] : __('Email','upvote');
	if( stripos($input['mailchimp_api_key'],'*') !== false ) :
		$input['mailchimp_api_key'] = $upvote_options['mailchimp_api_key'];
	endif;
	return $input; //Return Options
}
endif;
if( !function_exists('upvote_get_option') ) :
/**
 * Get Options
 *
 * Handles to get options
 *
 * @since Upvote 1.0
 **/
function upvote_get_option() {
	return get_option('upvote_options') ? get_option('upvote_options') : array(); 
}
endif;
if( !function_exists('upvote_obfuscate_string') ) :
/**
 * Replace String with Star
 *
 * Handles to replace string with star
 *
 * @since Upvote 1.0
 **/
function upvote_obfuscate_string($string){
    $length = strlen($string);
    $obfuscated_length = ceil($length / 2);
    $string = str_repeat('*', $obfuscated_length) . substr($string, $obfuscated_length);
    return $string;
}
endif;