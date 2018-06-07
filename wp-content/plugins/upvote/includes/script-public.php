<?php
/**
 * Public Script Functions
 *
 * Handles to public script functions
 *
 * @since Upvote 1.0
 **/
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
																'loaderurl' => UPVOTE_PLUGIN_URL . '/images/ajax-spinner.gif' ) );
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
	if( isset( $_POST['postid'] ) && !empty( $_POST['postid'] ) && !empty( $user_ID ) ) :

		$postid = $_POST['postid']; //Post ID
		//Get saved votes
		$votes 		= get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
		//Get saved votes
		$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
		if( in_array( $user_ID, $vote_users ) ) : //Check already voted or not
			$response['voted'] = 1; //Success
			$response['message'] = __('You already voted', 'upvote'); //Success
		else : //Else
			//Update Vote Count
			$vote_count = ( $votes + 1 );
			update_post_meta( $postid, '_upvote_count', $vote_count );
			//Update User
			array_push( $vote_users, $user_ID );
			update_post_meta( $postid, '_upvote_users', $vote_users );
			if( !get_user_meta( $user_ID, '_upvote_for_'.$postid, true) ) :
				update_user_meta( $user_ID, '_upvote_for_'.$postid, 1); //Track User Voted for which Posts
			endif;
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