<?php
/**
 * Plugin Shortcodes
 *
 * Handles to all shortcode of plugin
 *
 * @since Upvote 1.0
 **/

if( !function_exists('upvote_button_shortcode') ) :
/**
 * Upvote Button Shortcode
 *
 * Handles to manage upvote button shortcode
 *
 * @since Upvote 1.0
 **/
//delete_post_meta( 1334, '_upvote_users');
function upvote_button_shortcode( $atts, $content = null ){

	global $user_ID;

	extract( shortcode_atts( array(
		'postid'	=> get_the_ID(),//Show count by default
        'show_count'=> 'yes',		//Show count by default
        'label' 	=> __('Vote Up','upvote') //Button label
    ), $atts ) );

	//Get vote count
	$vote_count = get_post_meta( $postid, '_upvote_count', true ) 	? get_post_meta( $postid, '_upvote_count', true ) : 0;
	//Get saved votes
	$vote_users = get_post_meta( $postid, '_upvote_users', true ) 	? get_post_meta( $postid, '_upvote_users', true ) : array();
	//Get User IPs
	$vote_ips 	= get_post_meta( $postid, '_upvote_ips', true )		? get_post_meta( $postid, '_upvote_ips', true )	: array();
	//Referral Active
	$referral_enable = get_post_meta($postid, 'wpcf-campaign_referral_enable', true);
	//Guest Vote
	$voted_guest = ( isset( $_COOKIE['_voted'] ) && !empty( $_COOKIE['_voted'] ) ) ? intval( $_COOKIE['_voted'] ) : 0;

	//Load Public Script
	wp_enqueue_script('upvote-public-script');

	//Button for Vote
	$content = '<div class="upvote-container-big">';
	if( !is_user_logged_in() && 
		( ( isset( $_GET['referral'] ) && !empty( $_GET['referral'] ) ) 
			|| !empty( $referral_enable )
			|| ( !empty( $voted_guest ) && $voted_guest >= UPVOTE_ALLOWED_VOTES_GUEST ) && !in_array( upvote_get_ip(), $vote_ips ) ) ) : //Check user is not logged in
		$content .= '<div class="upvote-progress-button">
						<a href="#register"><button class="upvote-btn" data-id="'.$postid.'">'.$label.'</button></a>
						<i class="upvote-progress-circle fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
					</div><!-- /progress-button -->';
	else : //Else
		if( ( is_user_logged_in() && in_array( $user_ID, $vote_users ) ) || in_array( upvote_get_ip(), $vote_ips ) ) : //Check user already voted or not
			$content .= '<div class="upvote-progress-button success-upvote">
							<button disabled="disabled">'.__('You already voted','upvote').'</button>
						 </div><!-- /progress-button -->';//Already voted
		elseif( ( $voted_guest < UPVOTE_ALLOWED_VOTES_GUEST ) && !in_array( upvote_get_ip(), $vote_ips ) ) : //Else
			$content .= '<div class="upvote-progress-button">
							<button class="upvote-btn vote-me" data-id="'.$postid.'">'.$label.'</button>
						</div><!-- /progress-button -->';
		endif; //Endif
	endif; //Endif
	//Voted Count
	if( $show_count == 'yes' ) :
		if( !is_user_logged_in() || ( $voted_guest >= UPVOTE_ALLOWED_VOTES_GUEST ) ) : //Check user is not logged in
			$content .= '<div class="upvote-count" data-id="'.$postid.'"><a href="#register">↑&nbsp;<span>'.$vote_count.'</span></a></div>';
		else :
			$content .= '<div class="upvote-count" data-id="'.$postid.'">↑&nbsp;<span>'.$vote_count.'</span></div>';
		endif;
	endif; //Endif
	$content .= '</div><!--/.upvote-container-->';
	//Return Content
	return $content;

}
add_shortcode('upvote_button', 'upvote_button_shortcode');
endif;
if( !function_exists('upvote_count_shortcode') ) :
/**
 * Upvote Count Shortcode
 *
 * Handles to manage upvote count shortcode
 *
 * @since Upvote 1.0
 **/
function upvote_count_shortcode( $atts, $content = null ){

	extract( shortcode_atts( array(
		'postid'=> get_the_ID(),	//Show count by default
    ), $atts ) );

	//Get vote count
	$count = get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;

	//Return count
	return '<span class="upvote-count-sc" data-id="'.$postid.'">'. $count .'</span>';
}
add_shortcode('upvote_count', 'upvote_count_shortcode');
endif;
if( !function_exists('upvote_icon_button_shortcode') ) :
/**
 * Upvote Button Shortcode
 *
 * Handles to manage upvote button shortcode
 *
 * @since Upvote 1.0
 **/
function upvote_icon_button_shortcode( $atts, $content = null ){

	global $user_ID;

	extract( shortcode_atts( array(
		'postid'	=> get_the_ID(),//Show count by default
    ), $atts ) );

	//Get vote count
	$vote_count = get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
	//Get saved votes
	$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();
	//Get User IPs
	$vote_ips 	= get_post_meta( $postid, '_upvote_ips', true )   ? get_post_meta( $postid, '_upvote_ips', true ) 	: array();
	//Guest Vote
	$voted_guest = ( isset( $_COOKIE['_voted'] ) && !empty( $_COOKIE['_voted'] ) ) ? intval( $_COOKIE['_voted'] ) : 0;

	//Load Public Script
	wp_enqueue_script('upvote-public-script');

	//Button for Vote
	$content = '<div class="upvote-container">';
	if( !is_user_logged_in() && ( ( !empty( $voted_guest ) && $voted_guest >= UPVOTE_ALLOWED_VOTES_GUEST ) && !in_array( upvote_get_ip(), $vote_ips ) ) ) : //Check user is not logged in
		$content .= '<div class="upvote-progress-button icon">
						<a href="#register"><button class="upvote-btn icon">↑&nbsp;<span>'.$vote_count.'</span></button></a>
						<i class="upvote-progress-circle fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
					</div><!-- /progress-button -->';
	else : //Else
		if( ( is_user_logged_in() && in_array( $user_ID, $vote_users ) ) || in_array( upvote_get_ip(), $vote_ips ) ) : //Check user already voted or not
			$content .= '<div class="upvote-progress-button icon success-upvote">
							<button disabled="disabled">↑&nbsp;<span>'.$vote_count.'</span></button>
						 </div><!-- /progress-button -->';//Already voted
		elseif( ( $voted_guest < UPVOTE_ALLOWED_VOTES_GUEST ) && !in_array( upvote_get_ip(), $vote_ips ) ) : //Else
			$content .= '<div class="upvote-progress-button icon">
							<button class="upvote-btn icon vote-me" data-id="'.$postid.'">↑&nbsp;<span>'.$vote_count.'</span></button>
						</div><!-- /progress-button -->';
		endif; //Endif
	endif; //Endif
	$content .= '</div><!--/.upvote-container-->';
	//Return Content
	return $content;

}
add_shortcode('upvote_icon_button', 'upvote_icon_button_shortcode');
endif;
if( !function_exists('upvote_progress_shortcode') ) :
/**
 * Upvote Progress Shortcode
 *
 * Handles to manage upvote progress shortcode
 *
 * @since Upvote 1.0
 **/
function upvote_progress_shortcode( $atts, $content = null ){

	extract( shortcode_atts( array(
		'postid'	=> get_the_ID(),//Show count by default
    ), $atts ) );

	//Get vote count
	$vote_count = get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
	$goal_count = get_post_meta( $postid, '_upvote_goal', true ) ? get_post_meta( $postid, '_upvote_goal', true ) : 1000;
	$vote_progress = ( number_format( ( $vote_count / $goal_count ) * 100, 2 ) );
	$vote_progress = ( $vote_progress > 100 ) ? 100 : $vote_progress; // Prevent more than 100%
	$content = '<div class="sf-milestone-progress-wrapper">
					<div class="sf-milestone-values sf-upvote-progress-label"><span>'.sprintf('<span class="upvote-count-sc" data-id="'.$postid.'">%1$s</span>↑ %2$s %3$s↑ %4$s', $vote_count, __('upvotes reached of','upvote'), $goal_count, __('goal','upvote') ).'</span></div>
					<div class="sf-milestone-progress">
						<div class="sf-milestone-container" style="width:'.$vote_progress.'%"><span>'.$vote_progress.'%</span></div>
					</div>
				</div>';
	return $content;
}
add_shortcode('upvote_progress','upvote_progress_shortcode');
endif;
