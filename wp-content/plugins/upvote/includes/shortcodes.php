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
	$vote_count = get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
	//Get saved votes
	$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();

	//Load Public Script
	wp_enqueue_script('upvote-public-script');

	//Button for Vote
	$content = '<div class="upvote-container-big">';
	if( !is_user_logged_in() ) : //Check user is not logged in
		$content .= '<div class="upvote-progress-button">
						<a href="#login"><button class="upvote-btn" data-id="'.$postid.'">'.$label.'</button></a>
						<i class="upvote-progress-circle fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
					</div><!-- /progress-button -->';
	else : //Else
		if( in_array( $user_ID, $vote_users ) ) : //Check user already voted or not
			$content .= '<div class="upvote-progress-button success-upvote">
							<button disabled="disabled">'.__('You already voted','upvote').'</button>
						 </div><!-- /progress-button -->';//Already voted
		else : //Else
			$content .= '<div class="upvote-progress-button">
							<button class="upvote-btn vote-me" data-id="'.$postid.'">'.$label.'</button>
						</div><!-- /progress-button -->';
		endif; //Endif
	endif; //Endif
	//Voted Count
	if( $show_count == 'yes' ) :
		$content .= '<div class="upvote-count" data-id="'.$postid.'">+&nbsp;<span>'.$vote_count.'</span></div>';
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
	return '<span data-id="'.$postid.'">'. $count .'</span>';
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
        'show_count'=> 'yes',		//Show count by default
        'icon' 		=> 'fa-thumbs-o-up',
		'icon_fill' => 'fa-thumbs-up'//Button label
    ), $atts ) );

	//Get vote count
	$vote_count = get_post_meta( $postid, '_upvote_count', true ) ? get_post_meta( $postid, '_upvote_count', true ) : 0;
	//Get saved votes
	$vote_users = get_post_meta( $postid, '_upvote_users', true ) ? get_post_meta( $postid, '_upvote_users', true ) : array();

	//Load Public Script
	wp_enqueue_script('upvote-public-script');

	//Button for Vote
	$content = '<div class="upvote-container">';
	if( !is_user_logged_in() ) : //Check user is not logged in
		$content .= '<div class="upvote-progress-button icon">
						<a href="#login"><button class="upvote-btn icon">↑&nbsp;<span>'.$vote_count.'</span></button></a>
						<i class="upvote-progress-circle fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
					</div><!-- /progress-button -->';
	else : //Else
		if( in_array( $user_ID, $vote_users ) ) : //Check user already voted or not
			$content .= '<div class="upvote-progress-button icon success-upvote">
							<button disabled="disabled">↑&nbsp;<span>'.$vote_count.'</span></button>
						 </div><!-- /progress-button -->';//Already voted
		else : //Else
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