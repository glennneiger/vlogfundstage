<?php
/**
 * Public Class
 *
 * Handles all public functions
 *
 * @since Vlog Referral 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( !class_exists('Vlogref_Public') ) :

class Vlogref_Public{
	
	//Construct which run class
	public function __construct(){
		//Add Styles/Scripts
		add_action('wp_enqueue_scripts', 	array($this, 'register_style_scripts'));
		//Add Referral Parameter to Permalink
		add_shortcode('vlog_referral_url', 	array($this, 'referral_url'));
		//Add Referral Data to User
		add_action('vlog_user_register', 	array($this, 'update_user_register_referral'), 10, 2);
		//When User Upvote
		add_action('vlog_user_upvoted', 	array($this, 'user_upvoted'), 10, 2);
		//Add Woocommerce Account Tab
		add_filter('woocommerce_account_menu_items', array($this, 'woo_account_tab_menu'), 99);
		//Add Woocommerce Endpoints
		add_action('init',					array($this, 'woo_account_endpoints'), 99 );
		//Add Woocommerce Endpoints Query Vars
		add_filter('query_vars', 			array($this, 'woo_account_endpoints_query_vars'), 0 );
		//Add Woocommerce Referral Tab Content
		add_action('woocommerce_account_my-referrals_endpoint', array( $this, 'woo_account_referrals_content') );
		//Redirect User to My Referrals when he manually change the campaign ID on URL
		add_action('wp', 					array($this, 'referral_redirect') );
	}
	/**
	 * Register Style / Scripts
	 **/
	public function register_style_scripts(){
		//Register Plugin Style
		wp_enqueue_style('vlog-referral-style', VLOGREF_PLUGIN_URL . '/assets/css/style.css', array());
	}
	/**
	 * Render Referral Permalink
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function referral_url( $atts, $content = null ){
		global $post, $user_ID;
		extract( shortcode_atts( array( 
			'id' => $post->ID 
		), $atts, 'vlog_referral_url' ) );
		if( ( is_singular('product') || get_query_var('my-referrals') ) && is_user_logged_in() && vlogref_is_referral_enable($id) ) :
			$url = get_permalink( $id );
			$referral = base64_encode( $id.'_'.$user_ID );
			$url = add_query_arg('referral', $referral, $url);
		endif; //Endif		
		return $url;
	}
	/**
	 * Update Referral Data
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function update_user_register_referral( $user_id, $postdata ){
		if( isset( $postdata['referral'] ) && !empty( $postdata['referral'] ) ) :
			$ref_data = explode('_', base64_decode( $postdata['referral'] ) );			
			if( isset( $ref_data[0] ) && !empty( $ref_data[0] ) ) : //Update Referred Campaign
				$update_args = array( 'user_id' => $user_id );
				$campaign_id = $update_args['campaign'] = $ref_data[0];				
				if( vlogref_is_referral_enable( $campaign_id ) ) : //Check Referral Enabled
					update_user_meta( $user_id, '_referred_campaign', $campaign_id );
					if( isset( $ref_data[1] ) && !empty( $ref_data[1] ) ) : //Update Referred User
						$referred_id = $update_args['referred_by'] = $ref_data[1];
						update_user_meta( $user_id, '_referred_by', $referred_id );
					endif; //Endif
					$this->insert_referral( $update_args );
				endif; //Endif
			endif; //Endif
		endif; //Endif
	}
	/**
	 * Update Referral User Data to Table
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function insert_referral( $args ){
		global $wpdb;
		$wpdb->insert( VLOG_REFERRAL_TABLE, $args );
	}
	/**
	 * User Upvoted Campaign
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function user_upvoted( $userid, $postid ){
		global $wpdb;
		$ref_id = $wpdb->get_var('SELECT id FROM '.VLOG_REFERRAL_TABLE.' WHERE 1=1 AND user_id="'.$userid.'" AND campaign="'.$postid.'";');
		if( !empty( $ref_id ) ) : //Update Existing Record
			$wpdb->update( VLOG_REFERRAL_TABLE, 
					array('upvoted' => 1, 'update_date' => date('Y-m-d H:i:s', current_time('timestamp') ) ),
					array('id' => $ref_id) );			
		endif; //Endif
		//When Campaign Reach to Upvote Goal Then Disable Referral
		/*$upvote_count	= vlogref_campaign_upvotes($postid);
		$upvote_goal 	= vlogref_campaign_upvotes_goal($postid);
		if( $upvote_count >= $upvote_goal ) :
			update_post_meta($postid, 'wpcf-campaign_referral_enable',0);
		endif; //Endif*/
	}
	
	/**
	 * Add New Tab to Woocommerce Account Page
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function woo_account_tab_menu( $items ){
		$old_items = $items;
		unset( $items['edit-account'] );
		$items['my-referrals'] = __('Referrals', 'vlog-referral');
		$items['edit-account'] = $old_items['edit-account'];
		return $items;
	}
	
	/**
	* Register Woocommerce New Endpoints
	**/
	public function woo_account_endpoints() {
		//Referrals Endpoint
		add_rewrite_endpoint( 'my-referrals', EP_ROOT | EP_PAGES );
	}
	/**
	* Add Query Var for Woocommerce Endpoints
	**/
	public function woo_account_endpoints_query_vars( $vars ) {
		$vars[] = 'my-referrals'; //Referrals	 
		return $vars;
	}
	/**
	* Add Woo Account Referral Tab Content
	**/
	public function woo_account_referrals_content() {
		global $user_ID, $wpdb;		
		if( get_query_var('my-referrals') ) : //Check Referal Details Page
			include_once( VLOGREF_PLUGIN_PATH . '/includes/public/my-referrals-campaign.php');
		else : //Listing of Referrals
			include_once( VLOGREF_PLUGIN_PATH . '/includes/public/my-referrals.php');
		endif; //Endif		
	}
	
	/**
	 * Redirect User When Campaign Disabled for Referral
	 **/
	public function referral_redirect(){
		if( $campaign = get_query_var('my-referrals') ) :			
			if( !vlogref_user_campaign_referred_signup_count($campaign) ) :
				wp_safe_redirect( wc_get_endpoint_url('my-referrals') );
				exit;
			endif;
		endif; //Endif
	}
}
//Run Class
$vlogref_public = new Vlogref_Public();
endif;