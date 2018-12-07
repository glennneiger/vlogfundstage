<?php
/**** Overall Shortcodes of Sites ****/
if( !function_exists('vlogfund_stay_in_loop_form') ) :
/**
 * Campaign Stay in Loop Subscribe MailChimp
 *
 * Handles to subscriber campaign stay in loop mailchimp
 **/
function vlogfund_stay_in_loop_form( $atts, $content = null ){
	
	$content = '<div class="sf-campaign-stay-loop-container">';
	$content .= '<form action="'.get_permalink().'" method="POST" id="campaign_stay_loop_form">';
	$content .= '<h3>Stay In The Loop</h3>';
	$content .= '<input type="email" name="csl_email" id="csl_email" placeholder="E-Mail" required/>';
	$content .= '<input type="submit" name="csl_subscribe" id="csl_subscribe" value="Subscribe"/>';
	$content .= '<input type="hidden" name="csl_campaign" id="csl_campaign" value="'.get_the_ID().'"/>';
	$content .= '<div class="sf-campaign-stay-loop-message"></div>';
	$content .= '</form>';
	$content .= '</div>';
	return $content;
}
add_shortcode('vlog_campaign_stay_loop', 'vlogfund_stay_in_loop_form');
endif;
if( !function_exists('vlog_campaign_stay_in_loop_subscribe_ajax_callback') ) :
/**
 * Stay in Loop AJAX Callback
 *
 * Handles to subscriber stay in loop ajax callback
 **/
function vlog_campaign_stay_in_loop_subscribe_ajax_callback( $atts, $content = null ){
	
	$response = array();
	if( isset( $_POST['email'] ) && !empty( $_POST['email'] ) && is_email( $_POST['email'] ) 
		&& isset( $_POST['campaign'] ) && !empty( $_POST['campaign'] ) ) :
		$interests = get_post_meta($_POST['campaign'], 'wpcf-campaign_mc_interests', true);
		$int_to_sub = explode(',',$interests);
		$sub_to = array();
		foreach( $int_to_sub as $int ) :
			$sub_to[$int] = true;
		endforeach; //Endforeach
		if( !empty( $sub_to ) ) :
			include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');
			$MailChimp = new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
			$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_CAMPAIGN_LIST.'/members', array(
				'email_address' => $_POST['email'],			
				'status' => 'subscribed',
				'interests' => $sub_to
			));
			if( isset( $result['status'] ) && $result['status'] == 400 ) :
				$response['error'] = 1;
			else :
				$response['success'] = 1;
			endif; //Endif
		endif;
	endif; //Endif
	wp_send_json( $response );
	wp_die(); //To Proper Output
}
add_action('wp_ajax_vlog_campaign_stay_in_loop', 		'vlog_campaign_stay_in_loop_subscribe_ajax_callback');
add_action('wp_ajax_nopriv_vlog_campaign_stay_in_loop', 'vlog_campaign_stay_in_loop_subscribe_ajax_callback');
endif;