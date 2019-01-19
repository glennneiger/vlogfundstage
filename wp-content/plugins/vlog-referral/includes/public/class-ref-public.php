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
		//Add Referral to Login Users
		add_action('vlog_user_login', 		array($this, 'update_user_register_referral'), 10, 2);		
		//When User Upvote
		add_action('vlog_user_upvoted', 	array($this, 'referral_user_upvoted'), 10, 2);
		//Add Woocommerce Account Tab
		add_filter('woocommerce_account_menu_items', array($this, 'woo_account_tab_menu'), 99);
		//Add Woocommerce Endpoints
		add_action('init',					array($this, 'woo_account_endpoints') );
		//Add Woocommerce Endpoints Query Vars
		add_filter('query_vars', 			array($this, 'woo_account_endpoints_query_vars'), 0 );
		//Add Woocommerce Referral Tab Content
		add_action('woocommerce_account_my-referrals_endpoint', array( $this, 'woo_account_referrals_content') );
		//Redirect User to My Referrals when he manually change the campaign ID on URL
		add_action('wp', 					array($this, 'referral_redirect') );
		
		//Donation Phase
		//Update Add to Cart Redirect
		add_action('init',					array($this, 'woo_set_donate_referral_to_session'));
		//Order Create
		add_action('woocommerce_checkout_order_processed',	array($this, 'woo_add_donate_referral_to_order'), 99, 3);
		//Order Complete Hook to Update Referrals
		add_action('woocommerce_order_status_changed',		array($this, 'woo_insert_donate_referral'), 99, 4);
		
	}
	public function referral_tabs_rewrite_rules($rules){
		$newrules = array();
    	$newrules[ 'my-referrals/?$/view/?$' ] = 'index.php?pagename=my-referrals&view=$matches[2]';
    	return $newrules + $rules;
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
		$url = get_permalink( $id );
		if( ( is_singular('product') || get_query_var('my-referrals') ) && is_user_logged_in() && vlogref_is_referral_enable($id) ) :
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
		global $wpdb;
		if( isset( $postdata['referral'] ) && !empty( $postdata['referral'] ) ) :
			$ref_data = explode('_', base64_decode( $postdata['referral'] ) );			
			if( isset( $ref_data[0] ) && !empty( $ref_data[0] ) ) : //Update Referred Campaign
				$update_args = array( 'user_id' => $user_id, 'registered' => 1 );
				$campaign_id = $update_args['campaign'] = $ref_data[0];				
				if( vlogref_is_referral_enable( $campaign_id ) ) : //Check Referral Enabled					
					$referred_id = $update_args['referred_by'] = $ref_data[1];
					update_user_meta( $user_id, '_referred_campaign', $campaign_id );					
					if( isset( $ref_data[1] ) && !empty( $ref_data[1] ) ) : //Update Referred User						
						update_user_meta( $user_id, '_referred_by', $referred_id );
					endif; //Endif
					if( !vlogref_is_campaign_donation_enabled($campaign_id) ) : //Check Campaign Not In Donation Phase
						$ref_id = $wpdb->get_var('SELECT id FROM '.VLOG_REFERRAL_TABLE.' WHERE 1=1 AND user_id="'.$user_id.'" AND campaign="'.$campaign_id.'";');
						if( empty( $ref_id ) ) : //Check User Exists
							$this->insert_referral( $update_args );
						endif; //Endif
					endif; //Endif
				endif; //Endif
			endif; //Endif
		endif; //Endif
	}
	/**
	 * User Upvoted Campaign
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function referral_user_upvoted( $userid, $postid ){
		global $wpdb;
		$ref_id = $wpdb->get_var('SELECT id FROM '.VLOG_REFERRAL_TABLE.' WHERE 1=1 AND user_id="'.$userid.'" AND campaign="'.$postid.'";');
		if( !empty( $ref_id ) ) : //Update Existing Record
			$wpdb->update( VLOG_REFERRAL_TABLE, 
					array('upvoted' => 1, 'update_date' => date('Y-m-d H:i:s', current_time('timestamp') ) ),
					array('id' => $ref_id) );
		endif; //Endif
		//When Campaign Reach to Upvote Goal Then Disable Referral
		/*$upvote_count	= vlogref_campaign_upvotes($postid);
		$upvote_goal 	= vlogref_upvotes_campaign_goal($postid);
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
	public function woo_account_endpoints(){
		//Referrals Endpoint
		add_rewrite_endpoint( 'my-referrals', 	EP_ROOT | EP_PAGES );
	}
	/**
	* Add Query Var for Woocommerce Endpoints
	**/
	public function woo_account_endpoints_query_vars( $vars ){
		$vars[] = 'my-referrals'; 	//Referrals
		$vars[] = 'view'; 	//View
		return $vars;
	}
	/**
	* Add Woo Account Referral Tab Content
	**/
	public function woo_account_referrals_content(){
		global $user_ID, $wpdb;
		$campaign = get_query_var('my-referrals');		
		if( !empty( $campaign ) ) : //Check Referal Details Page
			if( vlogref_upvotes_user_referred_signup_count( $campaign ) && vlogref_donations_user_referred_count( $campaign ) ) :
				$upvotes_active 	= ( !isset( $_GET['view'] ) || $_GET['view'] == 'upvotes' ) ? ' is-active' : '';
				$donations_active 	= ( isset( $_GET['view'] ) && $_GET['view'] == 'donations' ) ? ' is-active' : '';
				echo '<nav class="woocommerce-MyAccount-navigation vf-referral-navigation"><ul>';
				echo '<li class="woocommerce-MyAccount-navigation-link'.$upvotes_active.'"><a href="'.add_query_arg('view', 'upvotes').'">'.__('Upvotes','vlog-referral').'</a></li>';
				echo '<li class="woocommerce-MyAccount-navigation-link'.$donations_active.'"><a href="'.add_query_arg('view', 'donations').'">'.__('Donations','vlog-referral').'</a></li>';
				echo '</ul></nav>';
			endif; //Endif
			if( isset( $_GET['view'] ) && $_GET['view'] == 'donations' && vlogref_donations_user_referred_count( $campaign ) ) : //Check View and Campaign Status
				include_once( VLOGREF_PLUGIN_PATH . '/includes/public/my-campaign-donations.php');
			else : //Else
				include_once( VLOGREF_PLUGIN_PATH . '/includes/public/my-campaign-upvotes.php');
			endif; //Endif
		else : //Listing of Referrals
			include_once( VLOGREF_PLUGIN_PATH . '/includes/public/my-referrals.php');
		endif; //Endif
	}
	
	/**
	 * Redirect User When Campaign Disabled for Referral
	 **/
	public function referral_redirect(){
		if( $campaign = get_query_var('my-referrals') ) :			
			if( !vlogref_upvotes_user_referred_signup_count( $campaign ) && !vlogref_donations_user_referred_count( $campaign ) ) :
				wp_safe_redirect( wc_get_endpoint_url('my-referrals') );
				exit;
			endif;
		endif; //Endif
	}
	/**
	 * Insert Donate Referral User Data to Table
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function insert_referral( $args ){
		global $wpdb;
		return $wpdb->insert( VLOG_REFERRAL_TABLE, $args );
	}	
	/**
	 * Save Referral Details to Session
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function woo_set_donate_referral_to_session(){
		if( !session_id() ) :
			session_start();
		endif; //Endif		
		if( isset( $_GET['referral'] ) && !empty( $_GET['referral'] ) ) :
			$ref_data = explode('_', base64_decode( $_GET['referral'] ) );
			if( isset( $ref_data[0] ) && !empty( $ref_data[0] ) ) : //Update Referred Campaign
				if( vlogref_is_campaign_donation_enabled($ref_data[0]) ) : //Check Campaign Status is Donation
					$_SESSION['referral'] = $_GET['referral'];
				endif; //Endif
			endif; //Endif
		endif; //Endif
	}
	/**
	 * Add Referral Data to Order
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function woo_add_donate_referral_to_order( $order_id, $posted_data, $order ){
		global $wpdb;
		if( isset( $_SESSION['referral'] ) && !empty( $_SESSION['referral'] ) ) :			
			$ref_data = explode('_', base64_decode( $_SESSION['referral'] ) );
			if( isset( $ref_data[0] ) && !empty( $ref_data[0] ) ) : //Update Referred Campaign
				$campaign_id 	= intval( $ref_data[0] );
				$donation_enable= get_post_meta($campaign_id, '_nyp',true);
				if( vlogref_is_referral_enable( $campaign_id ) 
					&& vlogref_is_campaign_donation_enabled($campaign_id) 
					&& $donation_enable == 'yes' && $ref_data[1] != $order->get_user_id() ) : //Check Referral Enabled / Contribute Status / Donation Enabled					
					foreach( $order->get_items() as $item ) : //Loop to Check Referred Campaign Exist
						if( $item->get_product_id() == $campaign_id ) : //Check Referred Campaign Exist
							update_post_meta($order_id, '_referred_by',	$ref_data[1]); //Referred By User
							update_post_meta($order_id, '_referred_for',$campaign_id); //Referred For Campaign
							unset( $_SESSION['referral'] ); //Remove From Session
						endif; //Endif
					endforeach; //Endforeach					
				endif; //Endif
			endif; //Endif
		endif; //Endif
	}
	/**
	 * Check Donate Referral Data
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function woo_insert_donate_referral( $order_id, $from_status, $to_status, $order ){
		global $wpdb;
		//Check Status Update to Completed
		if( $from_status == $to_status || $to_status != 'completed' ) :
			return;
		endif; //Endif
		
		$referred_by 	= get_post_meta($order_id, '_referred_by', true); 	//Get Referred By
		$referred_for 	= get_post_meta($order_id, '_referred_for', true); 	//Get Referred for
		//Check Order is Referred or not
		if( empty( $referred_by ) || empty( $referred_for ) ) :
			return;
		endif; //Endif
		
		//Check Status Update to Completed
		if( $from_status != $to_status && $to_status == 'completed' ) :
			$table_name = VLOG_REFERRAL_TABLE;
			$ref_args = array( 'user_id' 	=> $order->get_user_id(),
								'order_id' 	=> $order_id, 
								'referred_by'=> $referred_by, 
								'campaign' 	=> $referred_for, 
								'amount' 	=> $order->get_total(),
								'donated'	=> 1, 
								'update_date'=> date('Y-m-d H:i:s', current_time('timestamp') ) );
			$ref_id = $wpdb->get_var("SELECT id FROM $table_name WHERE 1=1 AND referred_by='$referred_by' AND campaign='$referred_for' AND order_id=0 AND user_id='".$order->get_user_id()."' AND amount=0 AND donated=0;");
			if( !empty( $ref_id ) ) : //If Exists Then Update
				$wpdb->update( VLOG_REFERRAL_TABLE, $ref_args, array('id' => $ref_id) );
			else : //Else
				//Add Donate Data to Table
				$this->insert_referral( $ref_args );				
			endif;
		endif; //Endif
	}
}
//Run Class
$vlogref_public = new Vlogref_Public();
endif;