<?php
/**
 * Plugin Functions
 *
 * Handles to plugin all generic functions
 *
 * @since Vlog Referral 1.0
 **/ 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !function_exists('vlogref_is_referral_enable') ) :
/**
 * Check Referral Enable or Not
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_is_referral_enable($post_id = 0){
	$post_id = !empty( $post_id ) ? $post_id : get_the_ID();
	$ref_enabled = get_post_meta($post_id, 'wpcf-campaign_referral_enable', true);
	return !empty( $ref_enabled ) ? true : false;
}
endif;
if( !function_exists('base64url_encode') ) :
/**
 * Base 64 Encode in Proper Manner
 *
 * @since Vlog Referral 1.0
 **/
function base64url_encode( $data ){
	return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
}
endif;
if( !function_exists('base64url_decode') ) :
/**
 * Base 64 Decode in Proper Manner
 *
 * @since Vlog Referral 1.0
 **/
function base64url_decode( $data ){
	return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
}
endif;
if( !function_exists('vlogref_campaign_upvotes') ) :
/**
 * Get Campaign Upvotes
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_campaign_upvotes($post_id){
	return get_post_meta($post_id, '_upvote_count', true) ? get_post_meta($post_id, '_upvote_count', true) : 0;
}
endif;
if( !function_exists('vlogref_upvotes_campaign_goal') ) :
/**
 * Get Campaign Upvotes Goal
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_campaign_goal($post_id){
	return get_post_meta( $post_id, '_upvote_goal', true ) ? get_post_meta( $post_id, '_upvote_goal', true ) : 1000;
}
endif;
if( !function_exists('vlogref_campaign_status') ) :
/**
 * Get Campaign Status
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_campaign_status($post_id){
	$status = get_post_meta($post_id, 'wpcf-campaign-status',true);
	return $status;
}
endif;
if( !function_exists('vlogref_campaign_status_title') ) :
/**
 * Get Campaign Status Title
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_campaign_status_title($post_id){
	$status_label = '';
	$status_saved = vlogref_campaign_status($post_id);
	$toolset_object = get_option('wpcf-fields');
	$campaign_statues = $toolset_object['campaign-status']['data']['options'];
	foreach( $campaign_statues as $key => $status ) : //Status
		if( $status['value'] == $status_saved ) :
			$status_label = $status['title'];
		endif; //Endif
	endforeach;
	return $status_label;
}
endif;
if( !function_exists('vlogref_is_campaign_donation_enabled') ) :
/**
 * Get Campaign in Donation Phase
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_is_campaign_donation_enabled($post_id){
	$status = vlogref_campaign_status($post_id);
	return ( intval( $status ) == 2 ) ? true : false;
}
endif;
if( !function_exists('vlogref_upvotes_user_rank') ) :
/**
 * Get User Position
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_user_rank($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$ranking = $wpdb->get_results( "SELECT SUM(upvoted) AS upvotes, referred_by
									FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1
									GROUP BY referred_by ORDER BY upvotes DESC;", ARRAY_A );
	if( !empty( $ranking ) ) :
		$key = array_search($userid, array_column($ranking, 'referred_by'));
		return ($key+1);
	endif; //Endif
	return false;
}
endif;
if( !function_exists('vlogref_upvotes_user_referral_count') ) :
/**
 * Get User Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_user_referral_count($campaign, $userid = 0){	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1 AND referred_by='$userid';");
	return $referrals;
}
endif;
if( !function_exists('vlogref_upvotes_user_referred_signup_count') ) :
/**
 * Get User Referred Signup Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_user_referred_signup_count($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;	
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='$campaign' AND referred_by='$userid' AND registered='1';");
	return $referrals;
}
endif;
if( !function_exists('vlogref_upvotes_total_referred_count') ) :
/**
 * Get Total Campaign Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_total_referred_count($campaign){
	
	global $user_ID, $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND upvoted=1 AND campaign='$campaign';");	
	return $referrals;
}
endif;
if( !function_exists('vlogref_upvotes_get_campaign_referrals') ) :
/**
 * Get User Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_get_campaign_referrals( $args = array() ){
	global $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$campaign 	= $args['campaign'];
	$limit 		= isset( $args['limit'] ) 	&& !empty( $args['limit'] ) ? $args['limit'] : 0;
	$offset 	= isset( $args['page'] ) 	&& $args['page'] > 1 		? ( $limit * ($args['page']-1) ) : 0;
	$sql = "SELECT SUM(upvoted) AS upvotes, referred_by, campaign
			FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1
			GROUP BY referred_by ORDER BY upvotes DESC";
	if( !empty( $limit ) ) : //Check Limit Set
		$sql .= " LIMIT $offset,$limit";
	endif;	
	$rankings = $wpdb->get_results($sql, ARRAY_A);
	return $rankings;
}
endif;
if( !function_exists('vlogref_upvotes_get_user_referrals') ) :
/**
 * Get User Upvotes Referrals
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_get_user_referrals( $args = array() ){
	global $wpdb, $user_ID;
	$table_name = VLOG_REFERRAL_TABLE;
	$userid 	= isset( $args['user'] ) 	&& !empty( $args['user'] ) 	? $args['user'] 	: $user_ID;
	$limit 		= isset( $args['limit'] ) 	&& !empty( $args['limit'] ) ? $args['limit'] 	: 0;
	$offset 	= isset( $args['page'] ) 	&& $args['page'] > 1 		? ( $limit * ($args['page']-1) ) : 0;
	$sql = "SELECT SUM(upvoted) AS upvotes,SUM(amount) AS donation,campaign,referred_by
			FROM $table_name
			WHERE 1=1 AND referred_by='$userid' AND (upvoted=1 OR donated=1)
			GROUP BY campaign ORDER BY upvotes DESC,donation DESC";
	if( !empty( $limit ) ) : //Check Limit Set
		$sql .= " LIMIT $offset,$limit";
	endif;	
	$referred = $wpdb->get_results($sql, ARRAY_A);
	return $referred;
}
endif;
if( !function_exists('vlogref_upvotes_get_campaign_winners') ) :
/**
 * Campaign Winners
 *
 * Handles to get campaign winners
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_get_campaign_winners( $campaign ){
	$winners = get_post_meta($campaign, '_referral_winners', true) ? get_post_meta($campaign, '_referral_winners', true) : array();
	return $winners;	
}
endif; //Endif
if( !function_exists('vlogref_upvotes_get_user_won_campaigns') ) :
/**
 * Get User Won Campaigns
 *
 * Handles to got user won campaings
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_get_user_won_campaigns( $userid ){
	$won = get_user_meta($userid, '_referral_won', true) ? get_user_meta($userid, '_referral_won', true) : array();
	return $won;
}
endif; //Endif
if( !function_exists('vlogref_upvotes_referral_prizes') ) :
/**
 * Get Campaign Referral Prizes
 *
 * Handles to get campaign referral prizes
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_referral_prizes( $campaign ){
	$prizes = new WP_Query( array('post_type' => 'campaign_ref_prize', 'numberposts' => -1, 'order' => 'ASC', 'orderby' => 'ID', 'toolset_relationships' => array( 'role' => 'child', 'related_to' => $campaign, 'relationship' => 'campaign_ref_prize' ) ) );
	$prizes_list = array();
	if( $prizes->have_posts() ) :
		while( $prizes->have_posts() ) : $prizes->the_post();
			$prizes_list[] = get_the_ID();
		endwhile; //Endwhile
	endif; //Endif
	wp_reset_postdata();
	return $prizes_list;
}
endif;
if( !function_exists('vlogref_upvotes_prize_details') ) :
/**
 * Get Prize Data
 *
 * Handles to got user won campaings
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_upvotes_prize_details( $prizeid ){
	$prize_data = array();
	$prize_data['title'] 	= get_post_meta($prizeid, 'wpcf-ref_prize_title', true) ? get_post_meta($prizeid, 'wpcf-ref_prize_title', true) : '';
	$prize_data['desc'] 	= get_post_meta($prizeid, 'wpcf-ref_prize_desc', true)	? get_post_meta($prizeid, 'wpcf-ref_prize_desc', true) 	: '';
	$prize_data['img'] 		= get_post_meta($prizeid, 'wpcf-ref_prize_img', true) 	? get_post_meta($prizeid, 'wpcf-ref_prize_img', true) 	: '';
	return $prize_data;
}
endif; //Endif
if( !function_exists('vlogref_donations_campaign_get_total') ) :
/**
 * Get Total Donations of Campaign
 *
 * Handles to get total donations of campaign
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_get_campaign_total($campaign){
	global $wpdb;
	$total_donation = $wpdb->get_row( "SELECT SUM(order_item_meta__line_total.meta_value) as total_amount, COUNT(*) AS total_orders
			FROM {$wpdb->posts} AS posts
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__line_total ON (order_items.order_item_id = order_item_meta__line_total.order_item_id)
				AND (order_item_meta__line_total.meta_key = '_line_total')
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__product_id_array ON order_items.order_item_id = order_item_meta__product_id_array.order_item_id 
			WHERE posts.post_type IN ('shop_order')
			AND posts.post_status IN ('wc-completed') AND ( ( order_item_meta__product_id_array.meta_key IN ('_product_id','_variation_id') 
			AND order_item_meta__product_id_array.meta_value IN ('{$campaign}') ) );", ARRAY_A );
	return ( !empty( $total_donation ) ? $total_donation : array( 'total_orders' => 0, 'total_amount' => 0 ) );
}
endif;
if( !function_exists('vlogref_donations_get_campaign_referrals') ) :
/**
 * Get Campaign Donation
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_get_campaign_referrals( $args = array() ){
	global $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$campaign 	= $args['campaign'];
	$limit 		= isset( $args['limit'] ) && !empty( $args['limit'] ) ? $args['limit'] : 0;
	$offset 	= isset( $args['page'] ) && $args['page'] > 1 ? ( $limit * ($args['page']-1) ) : 0;
	$sql = "SELECT SUM(amount) AS donation, referred_by, campaign, COUNT(user_id) AS referred_users
			FROM $table_name WHERE 1=1 AND campaign='$campaign' AND donated=1
			GROUP BY referred_by ORDER BY donation DESC";
	if( !empty( $limit ) ) : //Check Limit Set
		$sql .= " LIMIT $offset,$limit";
	endif;
	$donations = $wpdb->get_results($sql,ARRAY_A);	
	return $donations;
}
endif;
if( !function_exists('vlogref_donations_get_campaign_winners') ) :
/**
 * Campaign Donation Winners
 *
 * Handles to get campaign winners
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_get_campaign_winners( $campaign ){
	$winners = get_post_meta($campaign, '_referral_donation_winners', true) ? get_post_meta($campaign, '_referral_donation_winners', true) : array();
	return $winners;	
}
endif; //Endif
if( !function_exists('vlogref_donations_get_user_won_campaigns') ) :
/**
 * Get User Won Donation Phase
 *
 * Handles to got user won donation
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_get_user_won_campaigns( $userid ){
	$won = get_user_meta($userid, '_referral_donation_won', true) ? get_user_meta($userid, '_referral_donation_won', true) : array();
	return $won;
}
endif; //Endif
if( !function_exists('vlogref_donations_referral_prizes') ) :
/**
 * Get Campaign Referral Donation Prizes
 *
 * Handles to get campaign referral donation prizes
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_referral_prizes( $campaign ){
	$prizes = new WP_Query( array('post_type' => 'campaign_don_prize', 'numberposts' => -1, 'order' => 'ASC', 'orderby' => 'ID', 'toolset_relationships' => array( 'role' => 'child', 'related_to' => $campaign, 'relationship' => 'campaign_don_prize' ) ) );
	$prizes_list = array();
	if( $prizes->have_posts() ) :
		while( $prizes->have_posts() ) : $prizes->the_post();
			$prizes_list[] = get_the_ID();
		endwhile; //Endwhile
	endif; //Endif
	wp_reset_postdata();
	return $prizes_list;
}
endif;
if( !function_exists('vlogref_donations_prize_details') ) :
/**
 * Get Donation Prize Data
 *
 * Handles to get donation prize data
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_prize_details( $prizeid ){
	$prize_data = array();
	$prize_data['title'] 	= get_post_meta($prizeid, 'wpcf-donation_prize_title', true)? get_post_meta($prizeid, 'wpcf-donation_prize_title', true): '';
	$prize_data['desc'] 	= get_post_meta($prizeid, 'wpcf-donation_prize_desc', true)	? get_post_meta($prizeid, 'wpcf-donation_prize_desc', true) : '';
	$prize_data['img'] 		= get_post_meta($prizeid, 'wpcf-donation_prize_img', true) 	? get_post_meta($prizeid, 'wpcf-donation_prize_img', true) 	: '';
	return $prize_data;
}
endif; //Endif
if( !function_exists('vlogref_donations_user_rank') ) :
/**
 * Donations User Rank
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_user_rank($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$ranking = $wpdb->get_results( "SELECT SUM(amount) AS donations, referred_by
									FROM $table_name WHERE 1=1 AND campaign='$campaign' AND donated=1
									GROUP BY referred_by 
									ORDER BY donations DESC;", ARRAY_A );
	if( !empty( $ranking ) ) :
		$key = array_search($userid, array_column($ranking, 'referred_by'));
		return ($key+1);
	endif; //Endif
	return false;
}
endif;
if( !function_exists('vlogref_donations_campaign_goal') ) :
/**
 * Get Campaign Donations Goal
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_campaign_goal($post_id){
	return get_post_meta( $post_id, '_donations_goal', true ) ? get_post_meta( $post_id, '_donations_goal', true ) : 10000;
}
endif;
if( !function_exists('vlogref_donations_total_referred_amount') ) :
/**
 * Get Total Campaign Donation Referred Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_total_referred_amount($campaign){	
	global $user_ID, $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var("SELECT SUM(amount) FROM $table_name WHERE 1=1 AND donated=1 AND campaign='$campaign';");	
	return $referrals;
}
endif;
if( !function_exists('vlogref_donations_user_referred_count') ) :
/**
 * Get User Referred Donations Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_user_referred_count($campaign, $userid = 0){	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;	
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='$campaign' AND referred_by='$userid' AND donated='1';");
	return $referrals;
}
endif;
if( !function_exists('vlogref_donations_user_referred_amount') ) :
/**
 * Get User Referrals Donations
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_donations_user_referred_amount($campaign, $userid = 0){	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var("SELECT SUM(amount) FROM $table_name WHERE 1=1 AND donated=1 AND campaign='$campaign' AND referred_by='$userid';");
	return $referrals;
}
endif;
if( !function_exists('vlogref_price') ) :
/**
 * Get Formatted Price
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_price( $price, $args = array() ){
	$args['decimals'] = isset( $args['decimals'] ) ? $args['decimals'] : 0;
	return wc_price($price, $args);
}
endif;