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
							'email_address' => $_POST['csl_email'], 'interests' => $sub_to
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
//Milestone Shortcode
if( !function_exists('vlogfund_campaign_milestone_shortcode') ) :
function vlogfund_campaign_milestone_shortcode( $atts, $content = null ){
	extract( shortcode_atts( array(
		'product_id' => get_the_ID()
	), $atts, 'vlogfund_campaign_milestone' ) );
	//Total Sales
	$total_sales = get_post_meta($product_id,'_product_total_sales',true) ? get_post_meta($product_id,'_product_total_sales',true) : 0;
	//Milestones
	if( $camp_milestones = get_post_meta($product_id, 'wpcf-donation_milestones', true) ) :
		$milestones = explode(',',trim($camp_milestones));
		array_push($milestones,0);
		sort($milestones);
	else : //Else Default Milestones
		$milestones = array(0,1000,5000,10000,50000,100000,150000,200000,250000,500000,1000000);
	endif;
	$content = '';
	//$total_sales = 1000000; //Testing Value
	end($milestones);
	$final_milestone = $milestones[key($milestones)];
	if( $total_sales >= $final_milestone ) :
		$milestone_percent = ( number_format( ( $total_sales / $final_milestone ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:100%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">Campaign Goal Reached</span><span class="end">$'.$final_milestone.'</span></div>
				</div>';
	else : //Else
		foreach( $milestones as $key => $milestone ) :
			$milestone = trim($milestone);
			//Not Reach to First Milestone
			$next_milestone = $milestones[$key+1];
			if( $total_sales >= $milestone && $total_sales < $next_milestone ) :
				$milestone_percent = ( number_format( ( $total_sales / $next_milestone ) * 100, 2 ) );
				$content .= '<div class="sf-milestone-progress-wrapper">
								<div class="sf-milestone-progress">
									<div class="sf-milestone-container" style="width:'.( $milestone_percent > 100 ? 100 : $milestone_percent ) .'%"><span>'.$milestone_percent.'%</span></div>
								</div>
								<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$'.$next_milestone.'</span></div>
								<div class="sf-milestone-txt-content">
									<span class="sf-milestone-txt">'.__('Milestone').' <strong>'.($key+1).'</strong></span>';
									if( isset( $milestones[$key+2] ) ) : //Check Exist
										$content .= '<span class="sf-next-milestone-txt">'.__('Next Milestone: $').$milestones[$key+2].'</span>';
									endif;
				$content .= '</div>
							</div>';
				break;
			endif; //Endif
		endforeach;
	endif; //endif
	return $content;
}
add_shortcode('vlogfund_campaign_milestone', 'vlogfund_campaign_milestone_shortcode');
endif;
