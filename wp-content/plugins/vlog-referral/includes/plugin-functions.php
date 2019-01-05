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
if( !function_exists('vlogref_campaign_upvotes_goal') ) :
/**
 * Get Campaign Upvotes Goal
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_campaign_upvotes_goal($post_id){
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
	$status_label = '';
	$status_saved = get_post_meta($post_id, 'wpcf-campaign-status',true);
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
if( !function_exists('vlogref_user_position') ) :
/**
 * Get User Position
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_user_position($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$ranking = $wpdb->get_results( "SELECT COUNT(upvoted) AS upvotes, referred_by
									FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1
									GROUP BY referred_by ORDER BY upvotes DESC;", ARRAY_A );
	if( !empty( $ranking ) ) :
		$key = array_search($userid, array_column($ranking, 'referred_by'));
		return ($key+1);
	endif; //Endif
	return false;
}
endif;
if( !function_exists('vlogref_user_campaign_referral_count') ) :
/**
 * Get User Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_user_campaign_referral_count($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1 AND referred_by='$userid'" );	
	return $referrals;
}
endif;
if( !function_exists('vlogref_user_campaign_referred_signup_count') ) :
/**
 * Get User Referred Signup Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_user_campaign_referred_signup_count($campaign, $userid = 0){
	
	global $user_ID, $wpdb;
	$userid = !empty( $userid ) ? $userid : $user_ID; //User ID
	$table_name = VLOG_REFERRAL_TABLE;	
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='$campaign' AND referred_by='$userid';");
	return $referrals;
}
endif;
if( !function_exists('vlogref_campaign_total_referral_count') ) :
/**
 * Get Total Campaign Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_campaign_total_referral_count($campaign){
	
	global $user_ID, $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$referrals = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND upvoted=1 AND campaign='$campaign';");	
	return $referrals;
}
endif;
if( !function_exists('vlogref_get_campaign_referrals') ) :
/**
 * Get User Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_get_campaign_referrals( $args = array() ){
	global $wpdb;
	$table_name = VLOG_REFERRAL_TABLE;
	$campaign 	= $args['campaign'];
	$limit 		= isset( $args['limit'] ) && !empty( $args['limit'] ) ? $args['limit'] : 20;
	$offset 	= isset( $args['page'] ) && $args['page'] > 1 ? ( $limit * ($args['page']-1) ) : 0;
	$sql = "SELECT COUNT(upvoted) AS upvotes, referred_by, campaign
			FROM $table_name WHERE 1=1 AND campaign='$campaign' AND upvoted=1
			GROUP BY referred_by ORDER BY upvotes DESC
			LIMIT $offset,$limit";	
	$rankings = $wpdb->get_results($sql, ARRAY_A);	
	return $rankings;
}
endif;
if( !function_exists('vlogref_get_user_referrals') ) :
/**
 * Get User Referrals Count
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_get_user_referrals( $args = array() ){
	global $wpdb, $user_ID;
	$table_name = VLOG_REFERRAL_TABLE;
	$userid 	= isset( $args['user'] ) && !empty( $args['user'] ) ? $args['user'] : $user_ID;
	$limit 		= isset( $args['limit'] ) && !empty( $args['limit'] ) ? $args['limit'] : 20;
	$offset 	= isset( $args['page'] ) && $args['page'] > 1 ? ( $limit * ($args['page']-1) ) : 0;
	$sql = "SELECT COUNT(upvoted) AS upvotes,campaign,referred_by FROM $table_name
			WHERE 1=1 AND upvoted=1 AND referred_by='$userid'
			GROUP BY campaign ORDER BY upvotes DESC
			LIMIT $offset,$limit";	
	$referred = $wpdb->get_results($sql, ARRAY_A);
	return $referred;
}
endif;
if( !function_exists('vlogref_get_campaign_winners') ) :
/**
 * Campaign Winners
 *
 * Handles to get campaign winners
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_get_campaign_winners( $campaign ){
	$winners = get_post_meta($campaign, '_referral_winners', true) ? get_post_meta($campaign, '_referral_winners', true) : array();
	return $winners;	
}
endif; //Endif
if( !function_exists('vlogref_get_user_won_campaigns') ) :
/**
 * Get User Won Campaigns
 *
 * Handles to got user won campaings
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_get_user_won_campaigns( $userid ){
	$won = get_user_meta($userid, '_referral_won', true) ? get_user_meta($userid, '_referral_won', true) : array();
	return $won;
}
endif; //Endif
if( !function_exists('vlogref_referral_prizes') ) :
/**
 * Get Campaign Referral Prizes
 *
 * Handles to get campaign referral prizes
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_referral_prizes( $campaign ){
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
if( !function_exists('vlogref_get_prize_details') ) :
/**
 * Get Prize Data
 *
 * Handles to got user won campaings
 *
 * @since Vlog Referral 1.0
 **/
function vlogref_get_prize_details( $prizeid ){
	$prize_data = array();
	$prize_data['title'] 	= get_post_meta($prizeid, 'wpcf-ref_prize_title', true) ? get_post_meta($prizeid, 'wpcf-ref_prize_title', true) : '';
	$prize_data['desc'] 	= get_post_meta($prizeid, 'wpcf-ref_prize_desc', true)	? get_post_meta($prizeid, 'wpcf-ref_prize_desc', true) 	: '';
	$prize_data['img'] 		= get_post_meta($prizeid, 'wpcf-ref_prize_img', true) 	? get_post_meta($prizeid, 'wpcf-ref_prize_img', true) 	: '';
	return $prize_data;
}
endif; //Endif