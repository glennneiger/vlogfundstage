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
	$total_sales = vlogfund_get_product_sales($product_id);
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
						<div class="sf-milestone-values"><span class="start">Campaign Goal Reached</span><span class="end">'.wc_price($final_milestone).'</span></div>
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
								</div>';
								if( $final_milestone != $next_milestone ) : //Check Final and Current Milestone not same
									$content .= '<div class="sf-milestone-values">';
										if( $key == 0 ) : //First Milestone
											$content .= '<span class="start">'.__('First Milestone').'</span>';
										else : //Next Milestone
											$content .= '<span class="start">'.__('Next Milestone').'</span>';
										endif;									
										$content .= '<span class="end">'.wc_price($next_milestone).'</span>';
									$content .= '</div>';
								endif;
				$content .= '<div class="sf-milestone-values">';
					$content .= '<span class="start"><strong>'.__('Goal').'</strong></span><span class="end">'.wc_price($final_milestone).'</span>';
				$content .= '</div>';
				$content .= '</div>';
				break;
			endif; //Endif
		endforeach;
	endif; //endif
	return $content;
}
add_shortcode('vlogfund_campaign_milestone', 'vlogfund_campaign_milestone_shortcode');
endif;
if( !function_exists('vlogfund_comments_template_shortcode') ) :
/**
 * Comments shortcode
 **/
function comments_template_shortcode() {
	ob_start();
	comments_template( '/comments_template.php' );
	$cform = ob_get_contents();
	ob_end_clean();
	return $cform;
}
add_shortcode( 'comments_template', 'comments_template_shortcode' );
endif;
if( !function_exists('showparentpostid_func') ) :
/**
 * Get the ID of the current post which is being edited
 **/
function showparentpostid_func(){
	$product_id = get_post( get_the_ID() ); // Get ID of current product
  	$auction_parent_page_id = toolset_get_related_post( // Get ID of parent auction page
    	$product_id,
    	'organization-campaign', //slug of relationship
    	'parent'
	);
	return $auction_parent_page_id;
}
add_shortcode('showparentpostid', 'showparentpostid_func');
endif;
if( !function_exists('status_by_id_in_url_shortcode') ) :
/**
 * Do not display CRED edit forms if the post is published
 * Status by ID in URL
 **/
function status_by_id_in_url_shortcode( $atts ) {
  $a = shortcode_atts( array(
    'param' => 'post_id'
  ), $atts );
  $id = $_GET[$a['param']];
  $status = get_post_status($id);
  return $status;
}
add_shortcode('status-by-id-in-url', 'status_by_id_in_url_shortcode');
endif;
if( !function_exists('get_product_total_sales') ) :
/**
 * Get Total Spent on Particular Product
 **/
function get_product_total_sales( $atts, $content = null ){
	global $wpdb;
	extract( shortcode_atts( array(
		'productid' => get_the_ID(),
	), $atts, 'product_total_sales' ) );
	$total_sales = vlogfund_get_product_sales( $productid );
	return wc_price( $total_sales );
}
add_shortcode('product_total_sales', 'get_product_total_sales');
endif;
if( !function_exists('get_product_total_customers') ) :
/**
 * Get the total customers
 **/
function get_product_total_customers($atts, $content = null){

	global $wpdb;

	extract( shortcode_atts( array(
		'productid' => get_the_ID(),
	), $atts, 'product_total_customers' ) );

	$total_customers = $wpdb->get_var( "SELECT COUNT(orders.ID) as total_customers FROM {$wpdb->prefix}woocommerce_order_itemmeta order_itemmeta
							INNER JOIN {$wpdb->prefix}woocommerce_order_items order_items
							ON order_itemmeta.order_item_id = order_items.order_item_id
							INNER JOIN $wpdb->posts orders
							ON order_items.order_id = orders.ID
							WHERE order_itemmeta.meta_key = '_product_id'
							AND order_itemmeta.meta_value IN ( $productid )
							AND orders.post_status IN ( 'wc-completed' ) AND orders.post_type IN ('shop_order')
							ORDER BY orders.ID DESC" );
	return $total_customers;
}
add_shortcode('product_total_customers', 'get_product_total_customers');
endif;
if( !function_exists('format_completed_date_func') ) :
/**
 * Get the total customers
 **/
function format_completed_date_func($atts) {
	$a = shortcode_atts( array(
		'format' => 'm d Y',
		'orderid' => 0
	), $atts );

	$completedDate = get_post_meta( $a['orderid'], '_completed_date', true );
	if ( !$completedDate )
		return;
	$time = strtotime($completedDate);
	$date = date($a['format'], $time);
	return $date;
}
add_shortcode('format_completed_date', 'format_completed_date_func');
endif;
if( !function_exists('get_cart_count') ) :
/**
 * Add Shortcode [cart_count]
 **/
function get_cart_count(){
	if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    	global $woocommerce;
    	return $woocommerce->cart->cart_contents_count;
    }
}
add_shortcode('cart_count', 'get_cart_count');
endif;
if( !function_exists('my_form_shortcode') ) :
/**
 * Login Form
 **/
function my_form_shortcode() {
    ob_start();
    get_template_part('login_form');
    return ob_get_clean();
}
add_shortcode('my_form_shortcode', 'my_form_shortcode');
endif;
if( !function_exists('registration_form_shortcode') ) :
/**
 * Registration Form
 **/
function registration_form_shortcode() {
    ob_start();
    get_template_part('registration_form');
    return ob_get_clean();
}
add_shortcode('registration_form_shortcode', 'registration_form_shortcode' );
endif;
if( !function_exists('incrementor') ) :
/**
 * Incrementor
 **/
function incrementor() {
	static $i = 1;
	return $i ++;
}
add_shortcode('incrementor', 'incrementor');
endif;
if( !function_exists('post_count') ) :
/**
 * Organization Post Count
 * Get post count for cpt
 **/
function post_count() {
    $count_posts = wp_count_posts('organization');
    $published_posts = $count_posts->publish;
    return $published_posts . ' ';
}
add_shortcode('postcount', 'post_count');
endif;
if( !function_exists('connections') ) :
/**
 * Register connections shortcode
 *
 * @att (string) relationship : post relationship slug
 * @return count of connected posts
 */
function connections( $atts, $content ){
    // provide defaults
    $atts = shortcode_atts(
        array( 'relationship' => '' ),
        $atts
    );
    global $post;
    $count = 0;
    $relationship = toolset_get_relationship( $atts['relationship'] );
    if ( $relationship ) {
        $parent = $relationship['roles']['parent']['types'][0];
        $child = $relationship['roles']['child']['types'][0];
        $type = $post->post_type;
        $origin = ( $parent == $type ) ? 'parent' : 'child';
        // Get connected posts
        $connections = toolset_get_related_posts( $post->ID, $atts['relationship'], $origin, 9999, 0, array(), 'post_id', 'other', null, 'ASC', true, $count );
    }

    return $count;
}
add_shortcode( 'connections', 'connections');
endif;
if( !function_exists('vlogfund_currency_symbol_shortcode') ) :
/**
 * Currency Symbol
 **/
function vlogfund_currency_symbol_shortcode($atts,$content){
	return get_woocommerce_currency_symbol();
}
add_shortcode('vlogfund_currency_symbol', 'vlogfund_currency_symbol_shortcode');
endif;