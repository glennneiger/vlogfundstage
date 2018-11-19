<?php
/**
 * Public Script Functions
 *
 * Handles to public script functions
 *
 * @since Upvote 1.0
 **/
if( !function_exists('upvote_get_ip') ) :
/**
 * Get User IP Address
 *
 * Handles to get user IP addres
 * 
 * @since Upvote 1.0
 **/
function upvote_get_ip() {

   if( isset( $_SERVER ) ) {	     	
		if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
		   	$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	}  else {
		if( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$realip = getenv( 'HTTP_CLIENT_IP' );
		} else {
			$realip = getenv( 'REMOTE_ADDR' );
		}
	}
	return $realip;
}
endif;
if( ! function_exists('upvote_enqueue_scripts') ) :
/**
 * Enqueue Scripts/Styles
 *
 * Handles to enqueue scripts/styles
 *
 * @since Upvote 1.0
 **/
function upvote_enqueue_scripts(){
	
	//Core jQuery
	wp_enqueue_script( array('jquery') );
	
	//Register Style for Upvote Public
	wp_enqueue_style( 'upvote-public-style', 	UPVOTE_PLUGIN_URL . 'css/style-public.css', array(), null );
	//Register Script for Upvote Public
	wp_register_script( 'upvote-public-script', UPVOTE_PLUGIN_URL . 'js/script-public.js', array('jquery'), null, true );
	//Localize Script
	wp_localize_script( 'upvote-public-script', 'Upvote', array( 'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
																'loaderurl' => UPVOTE_PLUGIN_URL . '/images/ajax-spinner.gif',
																'product_page' => is_singular('product') ? 1 : 0 ) );
}
add_action('wp_enqueue_scripts', 'upvote_enqueue_scripts');
endif;
if( ! function_exists('upvote_update_vote_ajax_callback') ) :
/**
 * AJAX Callback to Vote
 *
 * Handles to AJAX callback to vote
 *
 * @since Upvote 1.0
 **/
function upvote_update_vote_ajax_callback(){

	global $user_ID;
				
	$response = array();

	//Check post id set or not
	if( isset( $_POST['postid'] ) && !empty( $_POST['postid'] ) ) :

		$postid = $_POST['postid']; //Post ID
		//Get saved votes
		$votes 		= get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
		//Get saved votes
		$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
		//Get User IPs
		$vote_ips 	= get_post_meta( $postid, '_upvote_ips', true )	  ?	get_post_meta( $postid, '_upvote_ips', true ) 	: array();
		
		if( ( is_user_logged_in() && in_array( $user_ID, $vote_users ) ) || in_array( upvote_get_ip(), $vote_ips ) ) : //Check already voted or not
			$response['voted'] = 1; //Success
			$response['message'] = __('You already voted', 'upvote'); //Success
		else : //Else
			//Update Vote Count
			$vote_count = ( $votes + 1 );
			update_post_meta( $postid, '_upvote_count', $vote_count );				
			if( is_user_logged_in() ) : //User Logged In
				//Update User
				array_push( $vote_users, $user_ID );
				update_post_meta( $postid, '_upvote_users', $vote_users );
				if( !get_user_meta( $user_ID, '_upvote_for_'.$postid, true) ) :
					update_user_meta( $user_ID, '_upvote_for_'.$postid, 1); //Track User Voted for which Posts
				endif;
			else :
				//Update Ips for Guest
				array_push( $vote_ips, upvote_get_ip() );
				update_post_meta( $postid, '_upvote_ips', $vote_ips );
				$guest_votes = ( isset( $_COOKIE['_voted'] ) && !empty( $_COOKIE['_voted'] ) ) ? ( intval( $_COOKIE['_voted'] ) + 1 ) : 1;
				setcookie('_voted', $guest_votes, ( time() + (3600*24*365) ), '/');
				if( $guest_votes >= UPVOTE_ALLOWED_VOTES_GUEST ) : //Disable Guest
					$response['guest_limit_reached'] = 1;
				endif;
			endif; //Endif
			$response['count'] 	 = $vote_count; //Count
			$response['success'] = 1; //Success
			$response['message'] = __('Thank you for voting', 'upvote'); //Success
		endif; //Endif
	endif; //Endif
	echo json_encode( $response ); //Response
	wp_die(); //Exit for better output
}
add_action('wp_ajax_upvote_update_vote', 		'upvote_update_vote_ajax_callback');
add_action('wp_ajax_nopriv_upvote_update_vote', 'upvote_update_vote_ajax_callback');
endif;