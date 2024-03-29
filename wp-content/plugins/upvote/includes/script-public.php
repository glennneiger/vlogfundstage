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
																'product_page' => is_singular('product') ? 1 : 0,
																'logged_in' => is_user_logged_in() ? 1 : 0 ) );
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

	global $user_ID, $user_email;
				
	$response = array();

	//Check post id set or not
	if( isset( $_POST['postid'] ) && !empty( $_POST['postid'] ) ) :

		$postid = $_POST['postid']; //Post ID
		//Referral Active
		$referral_enable= get_post_meta($postid, 'wpcf-campaign_referral_enable', true);
		//Get saved votes
		$votes 			= get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
		//Get saved votes
		$vote_users 	= get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
		//Get User IPs
		$vote_ips 		= get_post_meta( $postid, '_upvote_ips', true )	  ?	get_post_meta( $postid, '_upvote_ips', true ) 	: array();
		//Guest Vote
		$voted_guest 	= ( isset( $_COOKIE['_voted'] ) && !empty( $_COOKIE['_voted'] ) ) ? intval( $_COOKIE['_voted'] ) : 0;
		//Voted Posts
		$voted_posts 	= ( isset( $_COOKIE['_voted_posts'] ) && !empty( $_COOKIE['_voted_posts'] ) ) ? explode(',',$_COOKIE['_voted_posts'] ) : array();
		
		if( ( is_user_logged_in() && in_array( $user_ID, $vote_users ) )
			|| ( !is_user_logged_in() && !in_array( $postid, $voted_posts ) &&
			( !empty( $referral_enable ) || ( !empty( $voted_guest ) && $voted_guest >= UPVOTE_ALLOWED_VOTES_GUEST ) ) ) ) :
			$response['voted'] = 1; //Success
			$response['message'] = __('You already voted', 'upvote'); //Success
			$response['count'] = $votes; //Success
		else : //Else
			//Update Vote Count
			$vote_count = ( $votes + 1 );
			update_post_meta( $postid, '_upvote_count', $vote_count );
			//Update Ips for Users
			array_push( $vote_ips, upvote_get_ip() );
			update_post_meta( $postid, '_upvote_ips', $vote_ips );
			if( is_user_logged_in() ) : //User Logged In
				//Update User
				array_push( $vote_users, $user_ID );
				update_post_meta( $postid, '_upvote_users', $vote_users );
				if( !get_user_meta( $user_ID, '_upvote_for_'.$postid, true) ) :
					update_user_meta( $user_ID, '_upvote_for_'.$postid, 1); //Track User Voted for which Posts
				endif;
				//Hook for Upvote Done
				do_action('vlog_user_upvoted', $user_ID, $postid);
				$total_upvoted = 0;
				$usermetadata = get_user_meta( $user_ID );
				if( !empty( $usermetadata ) ) :
					foreach( $usermetadata as $key => $usermeta ) :
						if( stripos($key, '_upvote_for_') !== false ) :
							$total_upvoted++; 
						endif; //Endif	
					endforeach; ///Endforeach
				endif; //Endif
				include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');			
				$sub_to = array( VLOG_MAILCHIMP_VOTERS_GROUP => true );
				$interests = get_post_meta($postid, 'wpcf-campaign_mc_interests', true);
				if( !empty( $interests ) ) : //Check Campaign Have Interests of MC
					$int_to_sub = explode(',',$interests);
					foreach( $int_to_sub as $int ) :
						if( $int !== VLOG_MAILCHIMP_VOTERS_GROUP ) : //Check Voters Group not in Follow
							$sub_to[$int] = true;
						endif; //Endif
					endforeach; //Endforeach
				endif; //Endif				
				$MailChimp 	= new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
				$subscriber_hash = $MailChimp->subscriberHash($user_email);
				$mc_exist = $MailChimp->get('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array( 'interests' => $sub_to ) );    	
				if( $mc_exist['status'] != 404 ) : //Exist Then Update
					//Update Existing Users
					$result = $MailChimp->put('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array(
						'email_address' => $user_email,
						'merge_fields' => array( 'UNAME' => get_the_title( $postid ), 'UTOTAL' => $total_upvoted ),
						'status' => 'subscribed',
						'interests' => $sub_to
					));
				else :
					//Subscribe New Users
					$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_LIST.'/members', array(
						'email_address' => $user_email,
						'merge_fields' => array( 'UNAME' => get_the_title( $postid ), 'UTOTAL' => $total_upvoted ),
						'status' => 'subscribed',
						'interests' => $sub_to
					));
				endif;
			else :				
				$guest_votes = ( isset( $_COOKIE['_voted'] ) && !empty( $_COOKIE['_voted'] ) ) ? ( intval( $_COOKIE['_voted'] ) + 1 ) : 1;
				setcookie('_voted', $guest_votes, ( time() + (3600*24*365) ), '/');
				if( isset( $_COOKIE['_voted_posts'] ) && !empty( $_COOKIE['_voted_posts'] ) ) :
					$guest_voted = explode(',',$_COOKIE['_voted_posts']);
					if( !in_array($postid,$guest_voted) ) :
						array_push($guest_voted, $postid);			
					endif; //Endif
					$voted_posts = implode(',', $guest_voted);
					setcookie('_voted_posts', $voted_posts, ( time() + (3600*24*365) ), '/');
				else : //Else					
					setcookie('_voted_posts', $postid, ( time() + (3600*24*365) ), '/');
				endif; //Endif
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
if( !function_exists('upvote_guest_vote_user_login_save') ) :
/**
 * Guest Vote Move to Users When Login
 *
 * Handles to guest vote move to users when login
 *
 * @since Upvote 1.0
 **/
function upvote_guest_vote_user_login_save( $username, $user ){	
	//Update Guest Voted to User
	upvote_transfer_guest_voted_to_user( $user->ID );
}
add_action('wp_login', 'upvote_guest_vote_user_login_save', 99, 2);
endif;
if( !function_exists('upvote_transfer_guest_voted_to_user') ) :
/**
 * Guest Vote Move to Users
 *
 * Handles to guest vote move to users
 *
 * @since Upvote 1.0
 **/
function upvote_transfer_guest_voted_to_user( $user_id ){
	//Check Voted Posts
	if( isset( $_COOKIE['_voted_posts'] ) && !empty( $_COOKIE['_voted_posts'] ) ) :
		$guest_voted = explode(',',$_COOKIE['_voted_posts']);		
		if( !empty( $guest_voted ) ) :			
			foreach( $guest_voted as $key => $postid ) :
				//Get Saved Votes
				$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
				if( !in_array( $user_id, $vote_users ) ) : //Check Users in Post
					array_push( $vote_users, $user_id );
					update_post_meta( $postid, '_upvote_users', $vote_users );
				endif; //Endif								
				if( !get_user_meta( $user_id, '_upvote_for_'.$postid, true) ) :
					update_user_meta( $user_id, '_upvote_for_'.$postid, 1); //Track User Voted for which Posts
					unset($guest_voted[$key]);
				endif; //Endif
			endforeach; //Endforeach
			//Hook for Upvote Done
			do_action('vlog_user_upvoted', $user_id, $postid);
			$voted_posts = !empty( $guest_voted ) ? implode(',', $guest_voted) : '';
			$vote_count = ( count( $guest_voted ) > 0 ) ? count( $guest_voted ) : '';
			setcookie('_voted_posts', $voted_posts, ( time() + (3600*24*365) ), '/');
			setcookie('_voted', $vote_count, ( time() + (3600*24*365) ), '/');
		endif; //Endif
	endif; //Endif
}
endif;
if( !function_exists('upvote_milestone_email_trigger') ) :
/**
 * Upvote Milestone Email Trigger
 *
 * Handles to upvote milestone email
 *
 * @since Upvote 1.0
 **/
function upvote_milestone_email_trigger($user_id, $postid){
	
	//Get saved votes
	$votes 		= get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
	//Get Milestone
	$vote_milestone = get_post_meta( $postid, 'wpcf-upvote_milestone', true ) ? get_post_meta( $postid, 'wpcf-upvote_milestone', true ) : 1000;
	
	if( intval($votes) >= intval($vote_milestone) ) : //Check Milestone Reach
		
		//Get Notified Users
		$notified_users = get_post_meta( $postid, '_upvote_milestone_notified', true ) ? get_post_meta( $postid, '_upvote_milestone_notified', true ) : array();
		//Get saved votes
		$vote_users 	= get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
		$campaign_data 	= get_post($postid); //Get Campaign Data
		$author_email 	= get_the_author_meta('user_email',$campaign_data->post_author);
		$campaign_title	= $campaign_data->post_title;
		
		//Voter Email
		ob_start();
		include_once( UPVOTE_PLUGIN_PATH . 'includes/email-milestone-voter.php' );
		$voter_body = ob_get_contents();
		ob_get_clean();
		add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
		
		//Search Values in Template and Replace
		$search_arr = array('%%POST_TITLE%%', '%%POST_LINK%%', '%%MILESTONE%%');
		$replace_arr = array( $campaign_title, get_permalink($postid), $vote_milestone );
		
		//Notify to Those User Which are Not Notified Before
		$get_notified = array_diff($vote_users, $notified_users);

		//Send Emails to Votes
		foreach( $get_notified as $user_id ) :
			$email = get_the_author_meta('user_email',$user_id);			
			if( $email !== $author_email ) : //Don't send it to Author
				$voter_body	= str_replace( $search_arr, $replace_arr, $voter_body );
				$sent = wp_mail( $email, $campaign_title . ' reached a Milestone', $voter_body ); //Send emails to customers
				if( $sent ) : //Check Email Sent
					array_push($notified_users, $user_id);
				endif; //Endif
			endif; //Endif
		endforeach;
		
		//Update Milestone Notified Users
		update_post_meta( $postid, '_upvote_milestone_notified', $notified_users );
		
		if( !get_post_meta($postid,'_upvote_milestone_author_notify',true) ) :
			ob_start();
			include_once( UPVOTE_PLUGIN_PATH . 'includes/email-milestone-author.php' );
			$author_body = ob_get_contents();
			ob_get_clean();
			//Send emails to author
			$author_body = str_replace( $search_arr, $replace_arr, $author_body );
			$author_notified = wp_mail( $author_email, 'Your campaign ' . $campaign_title .' reached it\'s Milestone', $author_body );
			if( $author_notified ) : //Check Author Notified
				update_post_meta( $postid, '_upvote_milestone_author_notify', true );
			endif; //Endif
		endif; //Endif
	endif; //Endif
}
add_action('vlog_user_upvoted', 'upvote_milestone_email_trigger',10,2);
endif;