<?php
/**** Overall Shortcodes of Sites ****/
if( !function_exists('vlogfund_organization_id_campaign') ) :
/**
 * Campaign Organization Parent ID
 *
 * Handles to show parent ID of campaign organization ID
 **/
function vlogfund_organization_id_campaign( $atts, $content = null ){

	$organization_id = '';
	
	if( isset( $_GET['post_id'] ) && !empty( $_GET['post_id'] ) ) :
		$organization_id = toolset_get_related_post($_GET['post_id'],'organization-campaign','parent');
	endif; //Endif

	return $organization_id;	
}
add_shortcode('vlog_org_id_campaign', 'vlogfund_organization_id_campaign');
endif;
if( !function_exists('vlogfund_stay_in_loop_form') ) :
/**
 * Campaign Stay in Loop Subscribe MailChimp
 *
 * Handles to subscriber campaign stay in loop mailchimp
 **/
function vlogfund_stay_in_loop_form( $atts, $content = null ){

	$content = '<div class="sf-campaign-stay-loop-container">';
	$content .= '<form action="'.get_permalink().'" method="POST" id="campaign_stay_loop_form" class="sfc-front-page-signup-controls validate">';
	//$content .= '<h3>Stay In The Loop</h3>';

	$content .= '<div class="sfc-signup-wrapper"><input type="email" name="csl_email" id="csl_email" class="sf-mc-email" placeholder="E-Mail" required/></div>';
	$content .= '<div class="sfc-signup-wrapper"><input type="submit" name="csl_subscribe" id="csl_subscribe" class="sf-mc-button" value="Subscribe"/></div>';
	$content .= '<input type="hidden" name="csl_campaign" id="csl_campaign" value="'.get_the_ID().'"/>';
	//$content .= '<div class="sf-campaign-stay-loop-message"></div>';
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
	if( isset( $_POST['csl_email'] ) && !empty( $_POST['csl_email'] ) && is_email( $_POST['csl_email'] )
		&& isset( $_POST['csl_campaign'] ) && !empty( $_POST['csl_campaign'] ) ) :
		$interests = get_post_meta($_POST['csl_campaign'], 'wpcf-campaign_mc_interests', true);
		$int_to_sub = explode(',',$interests);
		$sub_to = array();
		foreach( $int_to_sub as $int ) :
			$sub_to[$int] = true;
		endforeach; //Endforeach
		if( !empty( $sub_to ) ) :
			include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');
			$MailChimp = new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
			$subscriber_hash = $MailChimp->subscriberHash($_POST['csl_email']);
			$mc_exist = $MailChimp->get('lists/'.VLOG_MAILCHIMP_CAMPAIGN_LIST.'/members/'.$subscriber_hash, array(
							'email_address' => $_POST['csl_email'],
							'interests' => $sub_to
						));
			if( $mc_exist['status'] != 404 ) : //Exist Then Update
				//Update Existing Users
				$result = $MailChimp->put('lists/'.VLOG_MAILCHIMP_CAMPAIGN_LIST.'/members/'.$subscriber_hash, array(
					'email_address' => $_POST['csl_email'],
					'interests' => $sub_to
				));
			else :
				$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_CAMPAIGN_LIST.'/members', array(
					'email_address' => $_POST['csl_email'],
					'status' => 'subscribed',
					'interests' => $sub_to
				));
			endif;
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
