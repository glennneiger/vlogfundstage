<?php
/**** Override Woocommerce Functionality ****/
if( !function_exists('vlogfund_woocommerce_override_checkout_fields') ) :
/**
 * Override Checkout Fields
 **/
function vlogfund_woocommerce_override_checkout_fields( $fields ) {

	//Remove Phone Required
	$fields['billing']['billing_phone']['required'] = false;

	//Billing Fields
	unset($fields['billing']['billing_address_1'],$fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_city'],$fields['billing']['billing_state']);
	unset($fields['billing']['billing_postcode'],$fields['billing']['billing_company']);

	//Shipping Fields
	unset($fields['shipping']['shipping_first_name'],$fields['shipping']['shipping_last_name']);
	unset($fields['shipping']['shipping_company'],$fields['shipping']['shipping_country']);
	unset($fields['shipping']['shipping_address_1'],$fields['shipping']['shipping_address_2']);
	unset($fields['shipping']['shipping_state'],$fields['shipping']['shipping_postcode']);
	unset($fields['shipping']['shipping_city']);
	//Order Fields
	unset($fields['order']['order_comments']);
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'vlogfund_woocommerce_override_checkout_fields' );
endif;
if( !function_exists('vlogfund_woocommerce_add_checkout_organization_fields') ) :
/**
 * Add Organization Fields
 **/
function vlogfund_woocommerce_add_checkout_organization_fields($checkout){

	if( !vlogfund_smile_mode_on() ) return;  //Don't show organization

	$organization_options = array();
	$organization = get_posts( array('post_type' => 'organization', 'posts_per_page' => -1, 'post_status' => 'publish', 'fields' => 'ids') );
	if( !empty( $organization ) ) :
		$chosen_org = isset( $_COOKIE['vlogfundorg'] ) ? $_COOKIE['vlogfundorg'] : '';
		foreach( $organization as $key => $org_id ) :
			$chosen_org = ( $key == 0 && empty( $chosen_org ) ) ? $org_id : $chosen_org;
			$organization_options[$org_id] = get_the_title($org_id) . '&nbsp;';
		endforeach;
		//echo $checkout->get_value('billing_cause');
		echo '<div class="organizations-wrapper">';
		echo '<span class="sfc-checkout-org-not-selected"> Your contribution will go to: <a href="/checkout-choose-organization"><i class="fa fa-pencil"></i></a></span>';
		woocommerce_form_field('billing_cause', array(
			'type' => 'radio',
			'options' => $organization_options,
			'input_class' => array('organization-input'),
			'class' => array('organization'),
			'label_class' => 'label-cause',
			'required' => true,
			'default' => ''
		), $chosen_org ); // $checkout->get_value('billing_cause')
		echo '</div><!--/.organizations-wrapper-->';
	endif; //Endif
}
add_action('woocommerce_before_checkout_billing_form','vlogfund_woocommerce_add_checkout_organization_fields');
add_filter('woocommerce_enable_order_notes_field', '__return_false');
endif;
if( !function_exists('vlogfund_woocommerce_checkout_process') ) :
/**
 * Checkout Process
 **/
function vlogfund_woocommerce_checkout_process(){
	if( !vlogfund_smile_mode_on() ) return;  //Don't show organization
	//If user not choose organization
	if ( !isset( $_POST['billing_cause'] ) || empty( $_POST['billing_cause'] ) ) :
		wc_add_notice( __('Please select a cause.') , 'error' );
	endif;
}
add_action('woocommerce_checkout_process', 'vlogfund_woocommerce_checkout_process');
endif;
if( !function_exists('vlogfund_woocommerce_checkout_update_fields') ) :
/**
 * Update Checkout Fields
 **/
function vlogfund_woocommerce_checkout_update_fields( $order_id ){
	if( !vlogfund_smile_mode_on() ) return;  //Don't show organization
	if( !empty( $_POST['billing_cause'] ) ) :
		update_post_meta($order_id, 'billing_cause', sanitize_text_field( $_POST['billing_cause'] ) );
	endif;
}
add_action('woocommerce_checkout_update_order_meta', 'vlogfund_woocommerce_checkout_update_fields');
endif;
if( !function_exists('vlogfun_woocommerce_order_details') ) :
/**
 * Show Custom Data on Order Edit
 **/
function vlogfun_woocommerce_order_details( $order ){
	$organization = get_post_meta($order->get_id(), 'billing_cause', true);
	$all_organizations = get_posts( array('post_type' => 'organization', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids') ); ?>
        <p class="form-field form-field-wide">
            <label for="billing_cause"><?php _e('Cause');?></label>
            <select name="billing_cause" id="billing_cause" class="wc-enhanced-select">
            		<option value="">---Choose Organization---</option>
            		<?php foreach( $all_organizations as $org ) : //Loop to List Organization ?>
                    	<option value="<?php echo $org;?>" <?php selected($organization,$org);?>><?php echo get_the_title($org);?></option>
                    <?php endforeach; //Endforeach ?>
            </select>
        </p>
<?php
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'vlogfun_woocommerce_order_details' );
endif;
if( !function_exists('vlogfund_save_billing_cause_admin') ) :
/**
 * Save Billing Cause from Admin
 **/
function vlogfund_save_billing_cause_admin( $post_id ){
	$organization = get_post_meta($post_id, 'billing_cause', true);
	$order = new WC_Order( $post_id );
	if( $organization !== $_POST['billing_cause'] ) :
    	update_post_meta( $post_id, 'billing_cause', wc_clean( $_POST['billing_cause'] ) );
		$current_user = wp_get_current_user();
		$order->add_order_note( sprintf('Organization updated %1$s to %2$s by %3$s', get_the_title($organization), get_the_title($_POST['billing_cause']), $current_user->user_login) );
	endif;
}
add_action( 'woocommerce_process_shop_order_meta', 'vlogfund_save_billing_cause_admin', 45 );
endif;
if( !function_exists('vlogfund_woocommerce_view_order_cause') ) :
/**
 * View Order Cause
 **/
function vlogfund_woocommerce_view_order_cause( $order ){
	if( $organization = get_post_meta($order->get_id(), 'billing_cause', true) ) : ?>
        <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
            <td class="woocommerce-table__product-name organization-name"><strong><?php _e('Cause')?></strong></td>
            <td><a href="<?php echo get_permalink($organization);?>"><strong><?php echo get_the_title($organization);?></strong></a></td>
        </tr>
<?php endif; //Endif
}
add_action('woocommerce_order_items_table', 'vlogfund_woocommerce_view_order_cause');
endif;
if( !function_exists('vlogfund_woocommerce_email_order_cause') ) :
/**
 * Order Email Cause
 **/
function vlogfund_woocommerce_email_order_cause( $order ) {
	if( $cause = get_post_meta( $order->id, 'billing_cause', true ) ) : ?>
    	<div style="margin-bottom:10px;">
        	<h3><?php _e('Your contribution will go towards the following cause');?></h3>
        	<a href="<?php echo get_permalink($cause);?>" style="margin-bottom:10px;"><?php echo get_the_title($cause);?></a>
		</div>
<?php endif;
    //return $fields;
}
add_action( 'woocommerce_email_after_order_table', 'vlogfund_woocommerce_email_order_cause', 10, 3 );
endif;
if( !function_exists('vlogfund_wc_stripe_payment_metadata') ) :
/**
 * Stripe Payment Metadata
 **/
function vlogfund_wc_stripe_payment_metadata( $metadata, $order ){
	if( $cause = get_post_meta( $order->id, 'billing_cause', true ) && vlogfund_smile_mode_on() ) :
		$metadata['billing_cause'] = get_the_title($cause);
	endif;
	return $metadata;
}
add_filter('wc_stripe_payment_metadata', 'vlogfund_wc_stripe_payment_metadata', 10, 2);
endif;

if( !function_exists('vlogfund_get_customer_emails_for_product') ) :
/**
 * Get Customer Emails for Product ID
 **/
function vlogfund_get_customer_emails_for_product( $product_id ){

	global $wpdb;

    $customers = $wpdb->get_col("
        SELECT usermeta.meta_value
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
		LEFT JOIN {$wpdb->postmeta} AS postmeta ON postmeta.post_id = posts.ID
		LEFT JOIN {$wpdb->usermeta} AS usermeta ON usermeta.user_id = postmeta.meta_value
        WHERE posts.post_type = 'shop_order'
        AND order_items.order_item_type = 'line_item'
        AND order_item_meta.meta_key = '_product_id'
        AND order_item_meta.meta_value = '$product_id'
		AND postmeta.meta_key = '_customer_user'
		AND usermeta.meta_key = 'billing_email' GROUP BY usermeta.meta_value");

    return $customers;
}
endif;
if( !function_exists('vlogfund_update_product_sales_customer') ) :
/**
 * Update Product Sales Customer / Sales
 **/
function vlogfund_update_product_sales_customer( $order_id, $from_status, $to_status, $order ){

	//Check New Status to Completed from pending, processing, on-hold, cancelled etc
	if( $to_status == 'completed' && in_array( $from_status, array('pending', 'processing', 'on-hold', 'cancelled', 'refunded', 'failed') ) ) : //Check Status to Complete
		foreach( $order->get_items() as $key => $order_product ) :
			$total_sales 	= get_post_meta($order_product['product_id'], '_product_total_sales', true);
			$total_customers= get_post_meta($order_product['product_id'], '_product_total_customers', true);
			$total_sales 	= !empty( $total_sales ) 	? $total_sales : 0;
			$total_customers= !empty( $total_customers )? $total_customers : 0;
			$updated_sales = ( $total_sales + $order_product['total'] );
			//Increse Total Sales
			update_post_meta( $order_product['product_id'], '_product_total_sales', $updated_sales );
			//Increse Total Customers
			update_post_meta( $order_product['product_id'], '_product_total_customers', ( $total_customers + 1 ) );
			//Update Campaign Milestone
			$milestones = array(10000,100000,500000,1000000);
			$milestone_abbrs = array('first','second','third', 'final');
			foreach( $milestones as $key => $milestone ) {
				$getalreadyupdated = new WP_Query( array('post_type' => 'publish', 'post_type' => 'update', 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_milestone_updates', 'value' => $milestone), array( 'key' => '_wpcf_belongs_product_id', 'value' => $order_product['product_id'] ) ) ) );
				if( !$getalreadyupdated->have_posts() && $updated_sales >= $milestone ){
					// Insert the post into the database
					$update_post_name = sprintf('%1$s %2$s %3$s', get_the_title( $order_product['product_id'] ), '-reached-', $milestone );
					$update_id = wp_insert_post( array('post_title' => wp_strip_all_tags( $milestone ), 'post_name' => $update_post_name, 'post_status' => 'publish','post_type' => 'update') );
					update_post_meta($update_id, '_wpcf_belongs_product_id', $order_product['product_id']);
					update_post_meta($update_id, '_milestone_updates', $milestone);
					$purchased_customers = vlogfund_get_customer_emails_for_product($order_product['product_id']);

					if( !empty( $purchased_customers ) ) :
						$campaign_data = get_post($order_product['product_id']);
						$author_email = get_the_author_meta('user_email',$campaign_data->post_author);
						ob_start();
						include_once( get_theme_file_path('/inc/email-milestone-reached.php') );
						$body = ob_get_contents();
						ob_get_clean();
						add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
						$campaign_title = get_the_title($order_product['product_id']);
						$backers_btn_label = 'Visit the campaign';
						$product_link = get_permalink($order_product['product_id']);
						$author_btn_label = 'Visit your campaign to post an update';
						end($milestones);
						if( key($milestones) == $key ) :
							$next_milestone = 'Campaign '.$campaign_title.' reached it’s last milestone<br>';
							$next_milestone .= 'Thank you for helping raise $1 Million<br>';
							$next_milestone .= 'We’ll reach out to you once the collaboration goes live';
						else :
							$next_milestone = 'Next Milestone: <strong>$'.number_format($milestones[$key+1], 2, ',', '.').'</strong>';
						endif;
						$body = str_replace( array('%%POST_TITLE%%', '%%MILESTONE_REACHED%%', '%%NEXT_MILESTONE%%'),
											array( $campaign_title, number_format($milestone,2, ',', '.'), $next_milestone ),
											$body );
						foreach( $purchased_customers as $email ) :
							$backerbody = str_replace( array('%%POST_LINK%%', '%%VISIT_CAMPAIGN_LABEL%%'),
											array( $product_link, $backers_btn_label ), $body);
							wp_mail( $email,
									 $campaign_title . ' reached a new Milestone',
									 $backerbody ); //Send emails to customers
						endforeach;
						//Send emails to author
						$authorbody = str_replace( array('%%POST_LINK%%', '%%VISIT_CAMPAIGN_LABEL%%'),
											array( home_url('/update-form/?parent_product_id='.$order_product['product_id']), $author_btn_label ), $body);
						wp_mail( $author_email,
								'Your campaign ' . $campaign_title .' reached it\'s '. $milestone_abbrs[$key] . ' Milestone',
								$authorbody );
					endif;
				}
				wp_reset_postdata();
			}
			endforeach;
	//Check Old Status from Completed to pending, processing, on-hold, cancelled etc
	elseif( $from_status == 'completed' && in_array( $to_status, array('pending', 'processing', 'on-hold', 'cancelled', 'refunded', 'failed') ) ) : //Check Status
		foreach( $order->get_items() as $key => $order_product ) :
			$total_sales 	= get_post_meta($order_product['product_id'], '_product_total_sales', true);
			$total_customers= get_post_meta($order_product['product_id'], '_product_total_customers', true);
			$updated_sales = !empty( $total_sales ) && ( $total_sales > 0 ) ? ( $total_sales - $order_product['total'] ) : 0;
			$updated_customer = !empty( $total_customers ) && ( $total_customers > 0 ) ? ( $total_customers - 1 ) : 0;
			//Reduce Total Sales
			update_post_meta( $order_product['product_id'], '_product_total_sales', $updated_sales );
			//Reduce Total Customers
			update_post_meta( $order_product['product_id'], '_product_total_customers', $updated_customer );
		endforeach;
	endif;

}
add_action('woocommerce_order_status_changed', 'vlogfund_update_product_sales_customer', 10, 4);
endif;
if( !function_exists('vlogfund_woocommerce_social_login_subscribe_mailchimp') ) :
/**
 * Woocommerce Social Login Subscribe to Mailchimp
 **/
function vlogfund_woocommerce_social_login_subscribe_mailchimp( $userdata ){
	include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');
	$MailChimp = new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
	$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_LIST.'/members', array(
		'email_address' => $userdata['user_email'],
		'merge_fields' => array( 'FNAME' => $userdata['first_name'], 'LNAME' => $userdata['last_name'] ),
		'status' => 'subscribed'
	));
	return $userdata;
}
add_filter('wc_social_login_facebook_new_user_data', 'vlogfund_woocommerce_social_login_subscribe_mailchimp', 10 );
add_filter('wc_social_login_google_new_user_data', 'vlogfund_woocommerce_social_login_subscribe_mailchimp', 10 );
endif;
if( !function_exists('vlogfund_redirect_for_smile_mode') ) :
/**
 * Redirect to checkout page when smile mode and user add product to cart
 **/
function vlogfund_redirect_for_smile_mode( $url ) {
	if( !vlogfund_smile_mode_on() ) :
	    $url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
	endif;
    return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'vlogfund_redirect_for_smile_mode' );
endif;
if( !function_exists('vlogfund_redirect_for_smile_mode_cart') ) :
/**
 * Redirect to checkout page when smile mode and someone hits cart page
 **/
function vlogfund_redirect_for_smile_mode_cart(){
	if( is_page( get_option('woocommerce_cart_page_id') ) && !vlogfund_smile_mode_on() ) :
		wp_safe_redirect( get_permalink( get_option( 'woocommerce_checkout_page_id' ) ) );
        die;
	endif; //Endif
}
add_action( 'template_redirect', 'vlogfund_redirect_for_smile_mode_cart' );
endif;
if( !function_exists('vlogfund_empty_cart_on_logout') ) :
/**
 * Clear cart when user logged out
 **/
function vlogfund_empty_cart_on_logout() {
 	WC()->cart->empty_cart();
}
add_action( 'wp_logout', 'vlogfund_empty_cart_on_logout');
endif;
if( !function_exists('vlogfund_woocommerce_register_post_type_product_labels') ) :
/**
 * Update Woocommerce Product Labels
 **/
function vlogfund_woocommerce_register_post_type_product_labels( $args ){
	$args['labels'] = array(
		'name'                  => __( 'Campaigns', 'woocommerce' ),
		'singular_name'         => __( 'Campaign', 'woocommerce' ),
		'all_items'             => __( 'All Campaigns', 'woocommerce' ),
		'menu_name'             => _x( 'Campaigns', 'Admin menu name', 'woocommerce' ),
		'add_new'               => __( 'Add New', 'woocommerce' ),
		'add_new_item'          => __( 'Add new campaign', 'woocommerce' ),
		'edit'                  => __( 'Edit', 'woocommerce' ),
		'edit_item'             => __( 'Edit Campaign', 'woocommerce' ),
		'new_item'              => __( 'New Campaign', 'woocommerce' ),
		'view'                  => __( 'View Campaign', 'woocommerce' ),
		'view_item'             => __( 'View Campaign', 'woocommerce' ),
		'search_items'          => __( 'Search campaigns', 'woocommerce' ),
		'not_found'             => __( 'No campaigns found', 'woocommerce' ),
		'not_found_in_trash'    => __( 'No campaigns found in trash', 'woocommerce' ),
		'parent'                => __( 'Parent campaign', 'woocommerce' ),
		'featured_image'        => __( 'Campaign image', 'woocommerce' ),
		'set_featured_image'    => __( 'Set campaign image', 'woocommerce' ),
		'remove_featured_image' => __( 'Remove campaign image', 'woocommerce' ),
		'use_featured_image'    => __( 'Use as campaign image', 'woocommerce' ),
		'insert_into_item'      => __( 'Insert into campaign', 'woocommerce' ),
		'uploaded_to_this_item' => __( 'Uploaded to this campaign', 'woocommerce' ),
		'filter_items_list'     => __( 'Filter campaigns', 'woocommerce' ),
		'items_list_navigation' => __( 'Campaigns navigation', 'woocommerce' ),
		'items_list'            => __( 'Campaigns list', 'woocommerce' )
	);
	return $args;
}
add_filter('woocommerce_register_post_type_product', 'vlogfund_woocommerce_register_post_type_product_labels', 99);
endif;
if( !function_exists('vlogfund_woocommerce_register_post_type_shop_order_labels') ) :
/**
 * Update Woocommerce Shop Orders Labels
 **/
function vlogfund_woocommerce_register_post_type_shop_order_labels( $args ){
	$args['labels'] = array(
		'name'                  => __( 'Contributions', 'woocommerce' ),
		'singular_name'         => _x( 'Contribution', 'shop_order post type singular name', 'woocommerce' ),
		'add_new'               => __( 'Add Contribution', 'woocommerce' ),
		'add_new_item'          => __( 'Add new contribution', 'woocommerce' ),
		'edit'                  => __( 'Edit', 'woocommerce' ),
		'edit_item'             => __( 'Edit Contribution', 'woocommerce' ),
		'new_item'              => __( 'New Contribution', 'woocommerce' ),
		'view'                  => __( 'View Contribution', 'woocommerce' ),
		'view_item'             => __( 'View Contribution', 'woocommerce' ),
		'search_items'          => __( 'Search contributions', 'woocommerce' ),
		'not_found'             => __( 'No contributions found', 'woocommerce' ),
		'not_found_in_trash'    => __( 'No contributions found in trash', 'woocommerce' ),
		'parent'                => __( 'Parent Contributions', 'woocommerce' ),
		'menu_name'             => _x( 'Contributions', 'Admin menu name', 'woocommerce' ),
		'filter_items_list'     => __( 'Filter Contributions', 'woocommerce' ),
		'items_list_navigation' => __( 'Contributions navigation', 'woocommerce' ),
		'items_list'            => __( 'Contributions list', 'woocommerce' )
	);
	return $args;
}
add_filter('woocommerce_register_post_type_shop_order', 'vlogfund_woocommerce_register_post_type_shop_order_labels', 99);
endif;
if( !function_exists('vlogfund_woocommerce_product_columns') ) :
/**
* Add Woocommerce Product Columns
**/
function vlogfund_woocommerce_product_columns( $columns ) {

	unset($columns['sku'],$columns['wpseo-score'],$columns['wpseo-score-readability'],
		$columns['product_tag'], $columns['product_type'], $columns['thumb'],
		$columns['wpseo-links'], $columns['wpseo-linked'], $columns['product_cat'], $columns['featured'], $columns['price'],
		$columns['date'], $columns['author'], $columns['post_views'], $columns['wpseo-title'],
		$columns['wpseo-metadesc'], $columns['wpseo-focuskw']);

	//$columns['featured'] = __('Featured');
	$columns['date'] = __('Date');
	$columns['author'] = __('Author');
	$columns['post_views'] = '<span class="dash-icon dashicons dashicons-chart-bar" title="Post Views"></span>';
	$columns['wpseo-title'] = __('SEO Title');
	$columns['wpseo-metadesc'] = __('Meta Desc.');
	$columns['wpseo-focuskw'] = __('Focus KW');
	$columns['upvotes'] = __( 'Upvotes' );
	$columns['total_sales'] = __('Total Sales');
	$columns['nyp'] = __( 'NYP' );
	$columns['campaign-status'] = __( 'Status' );
	return $columns;
}
add_filter( 'manage_product_posts_columns', 'vlogfund_woocommerce_product_columns', 99 );
endif;
if( !function_exists('vlogfund_woocommerce_product_columns_data') ) :
/**
* Product Columns Data
**/
function vlogfund_woocommerce_product_columns_data( $column, $post_id ) {

	switch( $column ) :
		case 'nyp' : //NYP
			echo get_post_meta($post_id, '_nyp',true) ? get_post_meta($post_id, '_nyp',true) : '&mdash;';
			break;
		case 'campaign-status' : //Campaign Status
			$campaign_status = '&mdash;';
			if( $saved_status = get_post_meta($post_id, 'wpcf-campaign-status',true) ) :
				$toolset_object = get_option('wpcf-fields');
				$campaign_statues = $toolset_object['campaign-status']['data']['options'];
				if( !empty( $campaign_statues ) && is_array( $campaign_statues ) ) :
					foreach( $campaign_statues as $key => $status ) : //Status
						if( $status['value'] == $saved_status ) :
							$campaign_status = $status['title'];
						endif; //Endif
					endforeach;
				endif; //Endif
			endif; //Endif
			echo $campaign_status;
			break;
		case 'total_sales' : //Total Sales
			echo get_post_meta($post_id, '_product_total_sales',true) ? get_post_meta($post_id, '_product_total_sales',true) : '&mdash;';
			break;
		case 'upvotes' : //Total Sales
			echo get_post_meta($post_id, '_upvote_count',true) ? get_post_meta($post_id, '_upvote_count',true) : '&mdash;';
			break;
	endswitch;
}
add_action( 'manage_product_posts_custom_column', 'vlogfund_woocommerce_product_columns_data', 99, 2);
endif;
if( !function_exists('vlogfund_woocommerce_shop_order_columns') ) :
/**
* Add Woocommerce Donations Columns
**/
function vlogfund_woocommerce_shop_order_columns( $columns ) {
	unset($columns['shipping_address'],$columns['order_total'],$columns['wc_actions']);
	$columns['billing_cause'] = __('Billing Cause');
	$columns['order_total'] = __('Total');
	$columns['wc_actions'] = __('Actions');
	return $columns;
}
add_filter( 'manage_shop_order_posts_columns', 'vlogfund_woocommerce_shop_order_columns', 99 );
endif;
if( !function_exists('vlogfund_woocommerce_shop_order_columns_data') ) :
/**
* Donations Columns Data
**/
function vlogfund_woocommerce_shop_order_columns_data( $column, $post_id ) {

	switch( $column ) :
		case 'billing_cause' : //Billing Cause
			$cause = get_post_meta($post_id, 'billing_cause',true);
			echo  $cause ? get_the_title( $cause ) : '&mdash;';
			break;
	endswitch;
}
add_action( 'manage_shop_order_posts_custom_column', 'vlogfund_woocommerce_shop_order_columns_data', 99, 2);
endif;
if( !function_exists('vlogfund_update_columns') ) :
/**
* Add Updates Columns
**/
function vlogfund_update_columns( $columns ) {
	unset($columns['wpseo-links'], $columns['wpseo-linked'], $columns['wpseo-score'],
	$columns['wpseo-score-readability'], $columns['date']);
	$columns['update_author'] = __('Update Author');
	$columns['related_campaign'] = __('Related Campaign');
	$columns['date'] = __('Date');
	return $columns;
}
add_filter( 'manage_update_posts_columns', 'vlogfund_update_columns', 99 );
endif;
if( !function_exists('vlogfund_update_columns_data') ) :
/**
* Updates Columns Data
**/
function vlogfund_update_columns_data( $column, $post_id ) {

	switch( $column ) :
		case 'update_author' : //Author
			$author = get_the_author();
			echo  $author ? $author : '&mdash;';
			break;
		case 'related_campaign' : //Related Campaign
			$related_campaign = get_post_meta($post_id, '_wpcf_belongs_product_id',true);
			echo  $related_campaign ? '<a href="'.get_permalink($related_campaign).'" target="_blank">'.get_the_title( $related_campaign ).'</a>' : '&mdash;';
			break;
	endswitch;
}
add_action( 'manage_update_posts_custom_column', 'vlogfund_update_columns_data', 99, 2);
endif;
//Change Category Label
if( !function_exists('vlogfund_update_category_tax_label') ) :
function vlogfund_update_category_tax_label( $args ){
	$args['label'] = $args['labels']['name'] = __( 'Campaign categories', 'woocommerce' );
	return $args;
}
add_filter('woocommerce_taxonomy_args_product_cat', 'vlogfund_update_category_tax_label', 99);
endif;
//Change Tags Label
if( !function_exists('vlogfund_update_tags_tax_label') ) :
function vlogfund_update_tags_tax_label( $args ){
	$args['label'] = $args['labels']['name'] = __( 'Campaign tags', 'woocommerce' );
	return $args;
}
add_filter('woocommerce_taxonomy_args_product_tag', 'vlogfund_update_tags_tax_label', 99);
endif;
//Change Metabox Label
if( !function_exists('vlogfund_update_metabox_titles') ) :
function vlogfund_update_metabox_titles() {
    global $wp_meta_boxes; // array of defined meta boxes
 	$wp_meta_boxes['product']['normal']['default']['postexcerpt']['title'] = __('Campaign short description', 'woocommerce');
	$wp_meta_boxes['product']['side']['low']['woocommerce-product-images']['title'] = __('Campaign gallery', 'woocommerce');
	$wp_meta_boxes['product']['normal']['high']['woocommerce-product-data']['title'] = __('Campaign data','woocommerce');
}
add_action('add_meta_boxes', 'vlogfund_update_metabox_titles', 99);
endif;
//Change Selector
if( !function_exists('vlogfund_product_type_selector') ) :
function vlogfund_product_type_selector( $args ){
	return array('simple'   => __( 'Simple campaign', 'woocommerce' ));
}
add_filter('product_type_selector', 'vlogfund_product_type_selector', 99);
endif;
//Remove Metabox Tabs
if( !function_exists('vlogfund_remove_admin_meta_tabs') ) :
function vlogfund_remove_admin_meta_tabs($tabs){
    unset( $tabs['inventory'], $tabs['shipping'], $tabs['attribute'], $tabs['variations'] );
	$tabs['linked_product']['label'] = __('Linked Campaign');
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'vlogfund_remove_admin_meta_tabs', 99, 1);
endif;
//Update Product Type Options
if( !function_exists('vlogfund_nyp_options_update') ) :
function vlogfund_nyp_options_update( $options ){
	$options['nyp']['label'] = __('Accept Donations');
	return $options;
}
add_filter( 'product_type_options', 'vlogfund_nyp_options_update', 99 );
endif;
//Remove Submenu Page
if( !function_exists('vlogfund_wc_remove_submenu_page') ) :
function vlogfund_wc_remove_submenu_page() {
	remove_submenu_page( 'edit.php?post_type=product', 'product_attributes' ); //Product Attributes Page
}
add_action( 'admin_menu', 'vlogfund_wc_remove_submenu_page', 99 );
endif;

//Add Author Email to Product Edit Screen
if( !function_exists('vlogfund_wc_author_email_show') ) :
function vlogfund_wc_author_email_show( $output ){
	global $pagenow, $post_type, $post;
	if( $pagenow == 'post.php' && $post_type == 'product' ) :
		$output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		$get_userdata = get_userdata( $post->post_author );
		$output .= sprintf('<a href="mailto:%1$s">%1$s</a>', antispambot( $get_userdata->user_email ) );
	endif;
	return $output;
}
add_filter( 'wp_dropdown_users', 'vlogfund_wc_author_email_show', 99 );
endif;

//Submit Youtube Campaign Create Subscribe to Mailchimp
if( !function_exists('vlogfund_wc_subscribe_mailchimp_campaign') ) :
function vlogfund_wc_subscribe_mailchimp_campaign( $post_id, $form_data ){
	if( $form_data['post_type'] == 'product' ) :
		$postdata 	= get_post( $post_id );
		$userdata 	= get_userdata( $postdata->post_author );
		if( !empty( $userdata ) ) :
			$user_posts = new WP_Query( array('posts_per_page' => -1, 'post_type' => 'product', 'post_status' => 'any', 'fields' => 'ids', 'author' => $userdata->ID ) );
			$first_name = get_user_meta($userdata->ID, 'first_name', true);
			$last_name 	= get_user_meta($userdata->ID, 'last_name', true);
			include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');
			$MailChimp 	= new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
			$subscriber_hash = $MailChimp->subscriberHash($userdata->user_email);			
			$mc_exist = $MailChimp->get('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array( 'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP ) ) );
			if( $mc_exist['status'] != 404 ) : //Exist Then Update
				//Update Existing Users
				$result = $MailChimp->put('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array(
					'email_address' => $userdata->user_email,
					'merge_fields' => array( 'FNAME' => $first_name, 'LNAME' => $last_name, 'CAMPAIGN' => $post->post_title, 'STATUS' => $postdata->post_status, 'COUNT' => $user_posts->found_posts ),
					'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP => true )
				));
			else :
				//Subscribe New Users
				$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_LIST.'/members', array(
					'email_address' => $userdata->user_email,
					'merge_fields' => array( 'FNAME' => $first_name, 'LNAME' => $last_name, 'CAMPAIGN' => $postdata->post_title, 'STATUS' => $postdata->post_status, 'COUNT' => count( $user_posts->found_posts ) ),
					'status' => 'subscribed',
					'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP => true )
				));
			endif;
			wp_reset_postdata();
		endif; //Endif
	endif;	//Endif
}
add_action('cred_submit_complete_98',	'vlogfund_wc_subscribe_mailchimp_campaign', 99,2);
add_action('cred_submit_complete_216',	'vlogfund_wc_subscribe_mailchimp_campaign', 99,2);
endif;

//Submit Youtube Campaign Subscribe to Mailchimp When Updating from Admin
if( !function_exists('vlogfund_wc_subscribe_mailchimp_campaign_admin') ) :
function vlogfund_wc_subscribe_mailchimp_campaign_admin( $post_id, $post ){

	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) || $post->post_type !== 'product' ) :
		return;
	endif; //Endif
	$userdata 	= get_userdata( $post->post_author );
	if( !empty( $userdata ) ) :
		$first_name = get_user_meta($userdata->ID, 'first_name', true);
		$last_name 	= get_user_meta($userdata->ID, 'last_name', true);
		$all_status = array('0' => 'Neutral', '1' => 'Vote', '2' => 'Contribute', '3' => 'Declined', '4' => 'Success', '5' => 'Draft', '6' => 'Pending');
		$saved_status = isset( $_POST['wpcf']['campaign-status'] ) ? $_POST['wpcf']['campaign-status'] : 0;
		include_once get_theme_file_path('/inc/mailchimp/mailchimp.php');
		$MailChimp 	= new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
		$subscriber_hash = $MailChimp->subscriberHash($userdata->user_email);
		$mc_exist = $MailChimp->get('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array( 'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP ) ) );    	
		if( $mc_exist['status'] != 404 ) : //Exist Then Update
			//Update Existing Users
			$result = $MailChimp->put('lists/'.VLOG_MAILCHIMP_LIST.'/members/'.$subscriber_hash, array(
				'email_address' => $userdata->user_email,
				'merge_fields' => array( 'FNAME' => $first_name, 'LNAME' => $last_name, 'CAMPAIGN' => $post->post_title, 'STATUS' => $all_status[$saved_status] ),
				'status' => 'subscribed',
				'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP => true )
			));
		else :
			//Subscribe New Users
			$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_LIST.'/members', array(
				'email_address' => $userdata->user_email,
				'merge_fields' => array( 'FNAME' => $first_name, 'LNAME' => $last_name, 'CAMPAIGN' => $post->post_title, 'STATUS' => $all_status[$saved_status] ),
				'status' => 'subscribed',
				'interests' => array( VLOG_MAILCHIMP_CREATORS_GROUP => true )
			));
		endif;

	endif; //Endif
}
add_action('save_post',	'vlogfund_wc_subscribe_mailchimp_campaign_admin', 30, 2);
endif;
