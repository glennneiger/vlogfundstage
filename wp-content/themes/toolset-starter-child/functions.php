<?php
//New changes nick 
//Override Woocommerce Functionality
require_once( get_theme_file_path('/inc/woocommerce.php') );
//Send Email Notification Functionality
require_once( get_theme_file_path('/inc/campaign-status-update.php') );
require_once( get_theme_file_path() . '/libs/custom-ajax-auth.php' );

if ( ! function_exists( 'ref_enqueue_main_stylesheet' ) ) {
	function ref_enqueue_main_stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'main', get_template_directory_uri() . '/style.css', array(), null );
			wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array(), null );
            wp_enqueue_style( 'owl-carousel', get_stylesheet_directory_uri() . '/css/owl.carousel.css', array(), null );

						wp_register_style( 'fontawesome5', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css' );
						wp_enqueue_style('fontawesome5');

						wp_register_style( 'jqueryui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
						wp_enqueue_style('jqueryui-css');

						wp_register_script( 'jqueryui-script', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js' );
					 wp_enqueue_script('jqueryui-script');


					 wp_register_script('validate-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js' );
				     wp_enqueue_script('validate-script');


            wp_register_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
            wp_enqueue_style('select2-style');

            wp_register_script( 'select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js' );
            wp_enqueue_script('select2-script');


             wp_register_style( 'toastr-style', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' );
            wp_enqueue_style('toastr-style');

             wp_register_script( 'toastr-script', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js' );
            wp_enqueue_script('toastr-script');


            wp_enqueue_script( 'owl-carousel', get_stylesheet_directory_uri() . '/js/owl.carousel.js', array(), null );

            wp_enqueue_script( 'js', get_stylesheet_directory_uri() . '/js.js', array(), null );

		}
		if ( is_page('about') && ! is_admin() ) {

			wp_enqueue_style( 'slick-style', get_stylesheet_directory_uri() . '/css/vendor/slick.css', array(), null );
			//wp_enqueue_style( 'animate-style', get_stylesheet_directory_uri() . '/css/vendor/animate.css', array(), null );
			wp_enqueue_style( 'responsive-style', get_stylesheet_directory_uri() . '/css/responsive.css', array(), null );
			wp_enqueue_style( 'normalize-style', get_stylesheet_directory_uri() . '/css/normalize.css', array(), null );

			wp_enqueue_script( 'modernizr-script', get_stylesheet_directory_uri() . '/js/vendor/modernizr.min.js', array(), null );
			wp_enqueue_script( 'waypoints-script', get_stylesheet_directory_uri() . '/js/vendor/waypoints.min.js', array(), null );

			wp_enqueue_script( 'slick-script', get_stylesheet_directory_uri() . '/js/vendor/slick.min.js', array(), null );

				wp_enqueue_script( 'general-script', get_stylesheet_directory_uri() . '/js/general.js', array(), null );

		}



	}
	add_action( 'wp_enqueue_scripts', 'ref_enqueue_main_stylesheet', 100 );
}





function sf_remove_scripts() {
    wp_dequeue_style( 'main' );
    wp_deregister_style( 'main' );

    wp_dequeue_style( 'theme' );
    wp_deregister_style( 'theme' );

    wp_dequeue_style( 'woocommerce-general' );
    wp_deregister_style( 'woocommerce-general' );

    wp_dequeue_style( 'ref_woocommerce' );
    wp_deregister_style( 'ref_woocommerce' );

     wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_deregister_style( 'woocommerce-smallscreen' );

     wp_dequeue_style( 'woocommerce-layout' );
    wp_deregister_style( 'woocommerce-layout' );

    wp_dequeue_style( 'woocommerce_views_onsale_badge' );
    wp_deregister_style( 'woocommerce_views_onsale_badge' );

    wp_dequeue_style( 'formidable' );
    wp_deregister_style( 'formidable' );

    wp_dequeue_style( 'open-sans' );
    wp_deregister_style( 'open-sans' );

    wp_dequeue_style( 'frm_fonts' );
    wp_deregister_style( 'frm_fonts' );

    wp_dequeue_style( 'menu-cells-front-end' );
    wp_deregister_style( 'menu-cells-front-end' );

    wp_dequeue_style( 'default-fonts' );
    wp_deregister_style( 'default-fonts' );

    wp_dequeue_style( 'post-views-counter-frontend' );
    wp_deregister_style( 'post-views-counter-frontend' );


     wp_dequeue_style( 'ddl-front-end' );
    wp_deregister_style( 'ddl-front-end' );

     wp_dequeue_style( 'views-pagination-style' );
    wp_deregister_style( 'views-pagination-style' );

     wp_dequeue_style( 'onthego-admin-styles' );
    wp_deregister_style( 'onthego-admin-styles' );

    wp_dequeue_style( 'photoswipe' );
    wp_deregister_style( 'photoswipe' );


    wp_dequeue_style( 'toolset-common' );
    wp_deregister_style( 'toolset-common' );

    wp_dequeue_style( 'woo-slg-public-style' );
    wp_deregister_style( 'woo-slg-public-style' );

    wp_dequeue_style( 'yoast-seo-adminbar' );
    wp_deregister_style( 'yoast-seo-adminbar' );


     wp_dequeue_style( 'font_awesome');
		 wp_deregister_style( 'font_awesome' );


    wp_dequeue_script( 'wpbootstrap_bootstrap_js' );





   // wp_dequeue_script( 'site' );
   // wp_deregister_script( 'site' );

    // Now register your styles and scripts here
}
add_action( 'wp_enqueue_scripts', 'sf_remove_scripts', 20 );









/**************************************************
 * Load custom cells types for Layouts plugin from the /dd-layouts-cells/ directory
 **************************************************/
if ( defined('WPDDL_VERSION') && !function_exists( 'include_ddl_layouts' ) ) {

	function include_ddl_layouts( $tpls_dir = '' ) {
		$dir_str = dirname( __FILE__ ) . $tpls_dir;
		$dir     = opendir( $dir_str );

		while ( ( $currentFile = readdir( $dir ) ) !== false ) {
			if ( is_file( $dir_str . $currentFile ) ) {
				include $dir_str . $currentFile;
			}
		}
		closedir( $dir );
	}

	include_ddl_layouts( '/dd-layouts-cells/' );
}




//general


//add code to footer




// Add lightbox to wordpress
/*add_action('wp_enqueue_scripts', 'add_thickbox');

function patch_thickbox() {
 if (is_admin()) return;
 wp_localize_script('thickbox', 'thickboxL10n', array(
			'next' => '>',
			'prev' => '<',
			'image' => 'Image',
			'of' => 'of',
			'close' => 'X',
			'noiframes' => 'Your Browser is too old',
			'l10n_print_after' => 'try{convertEntities(thickboxL10n);}catch(e){};'
	)
 );
}
add_action('wp_head', 'patch_thickbox');*/

/*function load_scripts2(){
    wp_enqueue_script('js', get_theme_file_uri('js.js'), array('jquery'), null, true );
	wp_localize_script('js', 'Vlogfund', array('ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http') ) ) );
}
add_action('wp_enqueue_scripts', 'load_scripts2');*/


//redirect default wp feed to feedburner

// replace the default posts feed with FeedBurner
function vf_custom_rss_feed( $output, $feed ) {
    if ( strpos( $output, 'comments' ) )
        return $output;

    return esc_url( 'http://feeds.feedburner.com/Vlogfund/' );
}
add_action( 'feed_link', 'vf_custom_rss_feed', 10, 2 );



/*******************************************************/
//comments
/*******************************************************/
//comments shortcode

function comments_template_shortcode() {
ob_start();
comments_template( '/comments_template.php' );
$cform = ob_get_contents();
ob_end_clean();
return $cform;
}

add_shortcode( 'comments_template', 'comments_template_shortcode' );

//remove last name

/*add_filter('decomments_get_name_is_email', function($name){
	list($name, $_) = explode('',$name);
	return $name;
},999);*/

/*******************************************************/
//campaign form && campaign edit form
/*******************************************************/

//combine fields into post title fields 98 && 216
add_action('cred_save_data', 'build_post_title', 10, 2);
function build_post_title($post_id, $form_data) {

if ( get_post_type( $post_id ) == 'product' ) {
if ($form_data['id']==98 || 216) {
$field1 = get_post_meta($post_id, 'wpcf-collaborator-1', true);
$field2 = get_post_meta($post_id, 'wpcf-collaborator-2', true);
$field3 = get_post_meta($post_id, 'wpcf-collaboration-type', true);

$post_title=$field1.' '.$field3.' '.$field2;
$slug = sanitize_title($post_title);
wp_update_post(array('ID'=>$post_id, 'post_title'=>$post_title,'post_name' => $slug));
}
}
}

//set post status 98 && 216

	function customize_cred_form_98( $post_id, $form_data ){

	    if ( isset( $_POST['post_status'] ) ) {

	        $updates = array(
	            'ID'            =>   $post_id,
	            'post_status'   =>   $_POST['post_status']
	            );
	        wp_update_post( $updates );
	    }
	}
	add_action( 'cred_submit_complete_98', 'customize_cred_form_98', 10, 2 );
    add_action( 'cred_submit_complete_216', 'customize_cred_form_98', 10, 2 );




//post content substitute 98 && 216

add_action('cred_save_data', 'my_save_content',10,2);

function my_save_content($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98 || 216)
    {
        if (isset($_POST['post_content_substitute']))
        {
            // add it to saved post meta
              $my_post = array(
                  'ID'           => $post_id,
                  'post_content' => $_POST['post_content_substitute']
              );

            // Update the post into the database
              wp_update_post( $my_post );
        }
    }
}


//copy post content substitute content to excerpt field 98 && 216
add_action('cred_save_data', 'my_save_excerpt',10,2);
function my_save_excerpt($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98 || 216)
    {
        if (isset($_POST['post_content_substitute']))
        {
            // add it to saved post meta
              $my_post = array(
                  'ID'           => $post_id,
                  'post_excerpt' => $_POST['post_content_substitute']
              );

            // Update the post into the database
              wp_update_post( $my_post );
        }
    }
}




//validate form characters 98 && 216

add_filter('cred_form_validate','my_validation',10,2);
function my_validation($field_data, $form_data){
    //field data are field values and errors
    list($fields,$errors)=$field_data;

    //validate if specific form
    if ($form_data['id']==98 || 216){

if( ($_POST['post_status']!='draft') ) {
        //check my_field value
        if (strlen(trim($fields['post_content_substitute']['value'])) < 250 ){
            //set error message for my_field
            $errors['post_content_substitute']='Please enter at least 250 characters or save your collaboration as a draft';
        }
    }
}
    //return result
    return array($fields,$errors);
}



//get youtube video ID

/*add_action('cred_save_data', 'get_yt_id_1_data_action',10,2);
function get_yt_id_1_data_action($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98 || 216)
    {
        if (isset($_POST['wpcf-youtube-video-collaborator-1']))
        {
            $link = $_POST['wpcf-youtube-video-collaborator-1'];
            $video_id = explode("?v=", $link); // For videos like http://www.youtube.com/watch?v=...
            if (empty($video_id[1]))
                $video_id = explode("/v/", $link); // For videos like http://www.youtube.com/watch/v/..

            $video_id = explode("&", $video_id[1]); // Deleting any other params
            $video_id = $video_id[0]; // here is your required video ID

            // add it to saved post meta
            add_post_meta($post_id, 'wpcf-youtube-video-id-collaborator-1', $video_id, true);
        }
    }
}



add_action('cred_save_data', 'get_yt_id_2_data_action',10,2);
function get_yt_id_2_data_action($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98 || 216)
    {
        if (isset($_POST['wpcf-youtube-video-collaborator-2']))
        {
            $link = $_POST['wpcf-youtube-video-collaborator-2'];
            $video_id = explode("?v=", $link); // For videos like http://www.youtube.com/watch?v=...
            if (empty($video_id[1]))
                $video_id = explode("/v/", $link); // For videos like http://www.youtube.com/watch/v/..

            $video_id = explode("&", $video_id[1]); // Deleting any other params
            $video_id = $video_id[0]; // here is your required video ID

            // add it to saved post meta
            add_post_meta($post_id, 'wpcf-youtube-video-id-collaborator-2', $video_id, true);
        }
    }
}*/



add_action('cred_save_data', 'cred_format_youtube_video_id',10,2);
function cred_format_youtube_video_id($post_id, $form_data)
{
    // if a specific form
    $forms = array(98,216);
    if (in_array($form_data['id'], $forms))
    {
        if (isset($_POST['wpcf-youtube-video-collaborator-1']))
        {
            $link = $_POST['wpcf-youtube-video-collaborator-1'];
            $video_id = explode("?v=", $link); // http://www.youtube.com/watch?v=...
            if (empty($video_id[1])) {
              $video_id = explode("/v/", $link); // http://www.youtube.com/watch/v/..
            }
            if (empty($video_id[1])) {
              $video_id = explode("/youtu.be/", $link); // https://youtu.be/...
            }

            $video_id = explode("&", $video_id[1]); // Deleting any other params
            $video_id = $video_id[0]; // here is your required video ID

            // update post meta with video ID
            update_post_meta($post_id, 'wpcf-youtube-video-id-collaborator-1', $video_id);
        }
    }
}

add_action('cred_save_data', 'cred_format_youtube_video_id2',10,2);
function cred_format_youtube_video_id2($post_id, $form_data)
{
    // if a specific form
    $forms = array(98,216);
    if (in_array($form_data['id'], $forms))
    {
        if (isset($_POST['wpcf-youtube-video-collaborator-2']))
        {
            $link = $_POST['wpcf-youtube-video-collaborator-2'];
            $video_id = explode("?v=", $link); // http://www.youtube.com/watch?v=...
            if (empty($video_id[1])) {
              $video_id = explode("/v/", $link); // http://www.youtube.com/watch/v/..
            }
            if (empty($video_id[1])) {
              $video_id = explode("/youtu.be/", $link); // https://youtu.be/...
            }

            $video_id = explode("&", $video_id[1]); // Deleting any other params
            $video_id = $video_id[0]; // here is your required video ID

            // update post meta with video ID
            update_post_meta($post_id, 'wpcf-youtube-video-id-collaborator-2', $video_id);
        }
    }
}




//campaign categories

/*add_action('cred_save_data', 'save_alt_product_cat',100,2);
function save_alt_product_cat($post_id, $form_data) {
  $forms = array( 98, 216 );
  if( in_array( $form_data['id'], $forms)) {
    $taxonomy = 'product_cat';
    $term = isset($_POST['alt_product_cat']) ? $_POST['alt_product_cat'] : '';
    if( $term != '' ) {
      wp_set_object_terms( $post_id, $term, $taxonomy, true );
    }
  }
}*/



//validate campaign category
/*add_filter('cred_form_validate','validate_product_cat_and_alt_cat',10,2);
function validate_product_cat_and_alt_cat($error_fields, $form_data)
{
    //field data are field values and errors
    list($fields,$errors)=$error_fields;
    //uncomment this if you want to print the field values
    //print_r($fields);
    //validate if specific form
    $forms = array( 98, 216 );
    if( in_array( $form_data['id'], $forms ) ) {
        //check selected product_cats
        if ($fields['product_cat']['value']==null)
        {
            //set error message for my_field
            $errors['product_cat']='You must select at least one Category';
        }
    }
    //return result
    return array($fields,$errors);
}*/



//copy campaign channel_logo_url_n to wpcf-collaborator-n-image field 98
add_action('cred_save_data', 'my_save_collaborator_1_image',10,2);
function my_save_collaborator_1_image($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98)
    {
        if (isset($_POST['channel_logo_url_1']))
        {
            // add it to saved post meta
            add_post_meta($post_id, 'wpcf-collaborator-1-image', $_POST['channel_logo_url_1'], true);
        }
    }
}

add_action('cred_save_data', 'my_save_collaborator_2_image',10,2);
function my_save_collaborator_2_image($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98)
    {
        if (isset($_POST['channel_logo_url_2']))
        {
            // add it to saved post meta
            add_post_meta($post_id, 'wpcf-collaborator-2-image', $_POST['channel_logo_url_2'], true);
        }
    }
}



//update channel logo

add_action('cred_save_data', 'my_save_collaborator_1_image_update', 10, 2);
function my_save_collaborator_1_image_update($post_id, $form_data) {

if ( get_post_type( $post_id ) == 'product' ) {
if ($form_data['id']==216) {
if (isset($_POST['channel_logo_url_1'])) {

update_post_meta($post_id, 'wpcf-collaborator-1-image', $_POST['channel_logo_url_1'], true);
}
}
}
}

add_action('cred_save_data', 'my_save_collaborator_2_image_update', 10, 2);
function my_save_collaborator_2_image_update($post_id, $form_data) {

if ( get_post_type( $post_id ) == 'product' ) {
if ($form_data['id']==216) {
if (isset($_POST['channel_logo_url_2'])) {
update_post_meta($post_id, 'wpcf-collaborator-2-image', $_POST['channel_logo_url_2'], true);
}
}
}
}





/**user redirect**/

add_action( 'template_redirect', 'redirect_to_specific_page' );

function redirect_to_specific_page() {

if ( is_page('campaign-form-get-started') && is_user_logged_in() ) {

wp_redirect( '/campaign-form', 301 );
  exit;
} elseif ( is_page('campaign-form') && ! is_user_logged_in()) {
	wp_redirect( '/campaign-form-get-started', 301 );
	  exit;
}
}




/*add_action( 'admin_init', 'redirect_non_logged_users_to_specific_page' );

function redirect_non_logged_users_to_specific_page() {

if ( !is_user_logged_in() && is_page('add page slug or i.d here') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php' ) {

wp_redirect( 'http://www.example.dev/page/' );
    exit;
}*/
/*******************************************************/
//update form && update edit form
/*******************************************************/

//Redirect to campaign after update submission

add_filter('cred_success_redirect_928', 'redirect_to_parent_1', 10, 3);
function redirect_to_parent_1($url, $post_id, $thisform) {
$parent_id = get_post_meta( $post_id, '_wpcf_belongs_product_id', true);
return get_permalink( $parent_id );
}

add_filter('cred_success_redirect_931', 'redirect_to_parent_2', 10, 3);
function redirect_to_parent_2($url, $post_id, $thisform) {
$parent_id = get_post_meta( $post_id, '_wpcf_belongs_product_id', true);
return get_permalink( $parent_id );
}

//Do not display CRED edit forms if the post is published
//Status by ID in URL
add_shortcode( 'status-by-id-in-url', 'status_by_id_in_url_shortcode' );
function status_by_id_in_url_shortcode( $atts ) {
  $a = shortcode_atts( array(
    'param' => 'post_id'
  ), $atts );
  $id = $_GET[$a['param']];
  $status = get_post_status($id);

  return $status;
}

/*******************************************************/
//campaign
/*******************************************************/

//Get Total Spent on Particular Product
function get_product_total_sales( $atts, $content = null ) {

	global $wpdb;

	extract( shortcode_atts( array(
		'productid' => get_the_ID(),
	), $atts, 'product_total_sales' ) );

	$total_sales = $wpdb->get_var( "SELECT SUM( order_item_meta__line_total.meta_value) as order_item_amount
		FROM {$wpdb->posts} AS posts
		INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
		INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__line_total ON (order_items.order_item_id = order_item_meta__line_total.order_item_id)
			AND (order_item_meta__line_total.meta_key = '_line_total')
		INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__product_id_array ON order_items.order_item_id = order_item_meta__product_id_array.order_item_id
		WHERE posts.post_type IN ( 'shop_order' )
		AND posts.post_status IN ( 'wc-completed' ) AND ( ( order_item_meta__product_id_array.meta_key IN ('_product_id','_variation_id')
		AND order_item_meta__product_id_array.meta_value IN ('{$productid}') ) );" );

	return wc_price( $total_sales );
}
add_shortcode('product_total_sales', 'get_product_total_sales');


//Get the total customers
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


//increase post meta
/*function update_product_sales_customer( $order_id, $from_status, $to_status, $order ){

	if( $to_status == 'completed' && in_array( $from_status, array('pending', 'processing', 'on-hold', 'cancelled', 'refunded', 'failed') ) ) : //Check Status to Complete
		foreach( $order->get_items() as $key => $order_product ) :
			$total_sales 	= get_post_meta($order_product['product_id'], '_product_total_sales', true);
			$total_customers= get_post_meta($order_product['product_id'], '_product_total_customers', true);
			$total_sales 	= !empty( $total_sales ) 	? $total_sales : 0;
			$total_customers= !empty( $total_customers )? $total_customers : 0;
			//Increse Total Sales
			update_post_meta( $order_product['product_id'], '_product_total_sales', ( $total_sales + $order_product['total'] ) );
			//Increse Total Customers
			update_post_meta( $order_product['product_id'], '_product_total_customers', ( $total_customers + 1 ) );
		endforeach;
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
add_action('woocommerce_order_status_changed', 'update_product_sales_customer', 10, 4);*/






//Milestone Shortcode
if( !function_exists('vlogfund_campaign_milestone_shortcode') ) :
function vlogfund_campaign_milestone_shortcode( $atts, $content = null ){
	extract( shortcode_atts( array(
		'product_id' => get_the_ID()
	), $atts, 'vlogfund_campaign_milestone' ) );
	//Total Sales
	$total_sales = get_post_meta($product_id,'_product_total_sales',true) ? get_post_meta($product_id,'_product_total_sales',true) : 0;
	$content = '';
	//$total_sales = 1100250; //Testing Value
	if( $total_sales <= 10000 ) : //Milestone 1 $10 000
		$milestone_percent = ( number_format( ( $total_sales / 10000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$10 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>1</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $100 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 10000 && $total_sales <= 100000 ) : //Milestone 2 $100 000
		$milestone_percent = ( number_format( ( $total_sales / 100000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$100 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>2</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $500 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 100000 && $total_sales <= 500000 ) : //Milestone 3 $500 000
		$milestone_percent = ( number_format( ( $total_sales / 500000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$500 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>3</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $1 000 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 500000 && $total_sales <= 1000000 ) : //Milestone 4 $1 000 000
		$milestone_percent = ( number_format( ( $total_sales / 1000000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$1 000 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>4</strong></span>
						</div>
					</div>';
	elseif( $total_sales > 1000000 ) : //All Milestone Over
		$milestone_percent = ( number_format( ( $total_sales / 1000000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:100%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">Campaign Goal Reached</span><span class="end">$1 000 000</span></div>
					</div>';
	endif;
	return $content;
}
add_shortcode('vlogfund_campaign_milestone', 'vlogfund_campaign_milestone_shortcode');
endif;






//Set minimum product price
/*function set_minimum_suggested_price( $price ) {
	return 3;
}
add_filter('woocommerce_raw_suggested_price', 'set_minimum_suggested_price');*/




//add meta descriptions to product pages

/*function doctype_opengraph($output) {
    if ( is_product() ) {
    return $output . '
    xmlns:og="http://opengraphprotocol.org/schema/"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
}
add_filter('language_attributes', 'doctype_opengraph');*/


//add custom meta dscripitons to campaign pages
function fb_opengraph() {
    global $post;

    if( get_post_type( get_the_ID() ) == 'product' ) {


        $yt_thumbnail_url_1 = 'https://i.ytimg.com/vi/';
        $yt_thumbnail_url_2 = '/hqdefault.jpg';


        if($excerpt = $post->post_excerpt) {
            $excerpt = strip_tags($post->post_excerpt);
            $excerpt = str_replace("", "'", $excerpt);
        } else {
            $excerpt = get_bloginfo('description');
        }
        ?>
<meta property="og:type" content="website"/>
<meta property="og:url" content="<?php echo the_permalink(); ?>"/>
<meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
<meta property="og:image" content="<?php echo do_shortcode ($yt_thumbnail_url_1.( '[types field="youtube-video-id-collaborator-1" output="raw"][/types]' ).$yt_thumbnail_url_2); ?>"/>
<meta property="og:image:secure_url" content="<?php echo do_shortcode ($yt_thumbnail_url_1.( '[types field="youtube-video-id-collaborator-1" output="raw"][/types]' ).$yt_thumbnail_url_2); ?>"/>
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image" content="<?php echo do_shortcode ($yt_thumbnail_url_1.( '[types field="youtube-video-id-collaborator-2" output="raw"][/types]' ).$yt_thumbnail_url_2); ?>"/>
<meta property="og:image:secure_url" content="<?php echo do_shortcode ($yt_thumbnail_url_1.( '[types field="youtube-video-id-collaborator-2" output="raw"][/types]' ).$yt_thumbnail_url_2); ?>"/>
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="fb:app_id" content="181038895828102"/>
<meta name="twitter:domain" content="<?php echo get_site_url() ?>">
<meta name="twitter:image" content="<?php echo do_shortcode ($yt_thumbnail_url_1.( '[types field="youtube-video-id-collaborator-1" output="raw"][/types]' ).$yt_thumbnail_url_2); ?>"/>
<?php
    } else {
        return;
    }
}
add_action('wp_head', 'fb_opengraph', 5);


//yoast seo remove meta tags from product pages

function remove_yoast_meta_desc_specific_page ( $myfilter ) {
    if( get_post_type( get_the_ID() ) == 'product' ) {
        return false;
    }
    return $myfilter;
}
//add_filter( 'wpseo_metadesc', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_title', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_robots', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_canonical', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_metakeywords', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_locale', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_opengraph_title', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_opengraph_desc', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_opengraph_url', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_opengraph_type', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_opengraph_site_name', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_opengraph_admin', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_opengraph_author_facebook', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_opengraph_show_publish_date', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_title', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_description', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_card_type', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_site', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_image', 'remove_yoast_meta_desc_specific_page' );
//add_filter( 'wpseo_twitter_creator_account', 'remove_yoast_meta_desc_specific_page' );
add_filter( 'wpseo_json_ld_output', 'remove_yoast_meta_desc_specific_page' );



//https://gist.github.com/amboutwe/811e92b11e5277977047d44ea81ee9d4
add_action('wp_head', 'remove_all_wpseo_og', 1);
function remove_all_wpseo_og() {
    if ( is_page(270)) { //is_single also works
  remove_action( 'wpseo_head', array( $GLOBALS['wpseo_og'], 'opengraph' ), 30 );
    }
}

/*function fb_opengraph_thank_you_page() {
    global $post, $product;

    if(is_single(270)) {

    }
}*/




//Single Post Meta Tags for YouTube Image
function single_post_meta_tags(){
	global $post;
	if( is_singular('post') && !has_post_thumbnail() && ( $youtubeid = types_render_field('post-youtube-video-id', array('id' => get_the_ID() ) ) ) ) : ?>
        <meta property="og:image" content="https://i.ytimg.com/vi/<?php echo $youtubeid;?>/hqdefault.jpg"/>
        <meta name="twitter:image" content="https://i.ytimg.com/vi/<?php echo $youtubeid;?>/hqdefault.jpg"/>
<?php endif; //Endif
}
add_action('wp_head', 'single_post_meta_tags', 5);










//Add product author to products


add_action('init', 'function_to_add_author_woocommerce', 999 );

function function_to_add_author_woocommerce() {
  add_post_type_support( 'product', 'author' );
}


//format _completed_date hidden custom field orders

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

add_shortcode( 'format_completed_date', 'format_completed_date_func');

/*******************************************************/
//campaign search
/*******************************************************/

//campaign popularity/view sorting

add_action( 'pvc_after_count_visit', 'update_toolset_view_count' );
function update_toolset_view_count ( $post_id ) {
  $post_type = 'product';
  // only update the post view count for products
  if ($post_type == get_post_type($post_id)) {
    $view_count = get_post_meta($post_id, 'wpcf-campaign-view-count', true);
    $view_count = $view_count ? $view_count : 0;
    // update and increment the view count for this product
    update_post_meta($post_id, 'wpcf-campaign-view-count', (intval($view_count) + 1));
  }
}




/*******************************************************/
//account
/*******************************************************/


function my_custom_endpoints1() {
    add_rewrite_endpoint( 'my-campaigns', EP_ROOT | EP_PAGES );

}

add_action( 'init', 'my_custom_endpoints1' );



/*function my_custom_endpoints2() {
    add_rewrite_endpoint( 'settings', EP_ROOT | EP_PAGES );

}

add_action( 'init', 'my_custom_endpoints2' );*/




function my_custom_query_vars1( $vars ) {
    $vars[] = 'my-campaigns';


    return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars1', 0 );


/*function my_custom_query_vars2( $vars ) {
    $vars[] = 'settings';


    return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars2', 0 );*/



function my_custom_flush_rewrite_rules() {
    flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'my_custom_flush_rewrite_rules' );




function my_custom_my_account_menu_items( $items ) {

    $items = array(

    'my-campaigns' => 'My Campaigns',
		'orders'            => __( 'Backed Campaigns', 'woocommerce' ),
		'edit-account' => __('Account', 'woocommerce')
        //'settings'  => __( 'Account Settings', 'woocommerce' ),

    );

    return $items;

}

add_filter( 'woocommerce_account_menu_items', 'my_custom_my_account_menu_items' );





function my_custom_endpoint_content1() {


echo do_shortcode ('[wpv-post-body view_template="account-my-campaigns"]');
}

add_action( 'woocommerce_account_my-campaigns_endpoint', 'my_custom_endpoint_content1' );




/*function my_custom_endpoint_content2() {


echo do_shortcode ('[wpv-post-body view_template="acccount-settings"]');
}

add_action( 'woocommerce_account_settings_endpoint', 'my_custom_endpoint_content2' );*/





/*******************************************************/
//checkout
/*******************************************************/

// go straight to checkout
//http://yoursite.com/checkout/?add-to-cart=[product-id]
/*add_filter('woocommerce_add_to_cart_redirect', 'themeprefix_add_to_cart_redirect');
function themeprefix_add_to_cart_redirect() {
 global $woocommerce;
 $checkout_url = $woocommerce->cart->get_checkout_url();
 return $checkout_url;
}*/

// or
/*add_filter ('add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout() {
return WC()->cart->get_checkout_url();
}*/

//Pre-populate Woocommerce checkout fields
add_filter('woocommerce_checkout_get_value', function($input, $key ) {
	global $current_user;
	switch ($key) :
		case 'billing_first_name':
		case 'shipping_first_name':
			return $current_user->first_name;
		break;

		case 'billing_last_name':
		case 'shipping_last_name':
			return $current_user->last_name;
		break;
		case 'billing_email':
			return $current_user->user_email;
		break;
		case 'billing_phone':
			return $current_user->phone;
		break;
	endswitch;
}, 10, 2);



/*Password Strength*/
function sf_remove_password_strength() {
 if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
 wp_dequeue_script( 'wc-password-strength-meter' );
 }
}
add_action( 'wp_print_scripts', 'sf_remove_password_strength', 100 );

// WOOCOMMERCE REDIRECT LOOP WHEN CART AND CHECKOUT ON THE SAME PAGE
//https://etzelstorfer.com/en/woocommerce-redirect-loop-when-cart-and-checkout-on-the-same-page/

/*
add_action('template_redirect', 'redirection_function');

function redirection_function(){
    global $woocommerce;

    if( is_checkout() && 0 == sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count) && !isset($_GET['key']) ) {
        wp_redirect( home_url() );
        exit;
    }
}
*/




// Add Shortcode [cart_count]
function get_cart_count() {
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    global $woocommerce;
    return $woocommerce->cart->cart_contents_count;
    }
}
add_shortcode( 'cart_count', 'get_cart_count' );








//change donation amount

function register_session(){
  if( !session_id() ){
	session_start();
  }
}
add_action('init', 'register_session');

function return_custom_price($price, $product) {

	if( is_admin() ) return $price;
	$product_id = $product->get_id();
	if( isset( $_SESSION['woo_user_price_'.$product->id] ) && !empty( $_SESSION['woo_user_price_'.$product->id] ) ) {
		return $_SESSION['woo_user_price_'.$product->id];
	} else {
    	return $price;
	}
}
add_filter('woocommerce_product_get_price', 'return_custom_price', 10, 2);

function remove_custom_price_when_item_removed( $cart_item_key, $cart ) {

	$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
	//Remove Changed Price When Product Removed from Cart
	unset($_SESSION['woo_user_price_'.$product_id]);

};
add_action( 'woocommerce_remove_cart_item', 'remove_custom_price_when_item_removed', 10, 2 );

function reset_all_product_prices(){
	foreach( $_SESSION as $key => $value ){
        if( strpos($key, 'woo_user_price_') === 0){
        	unset($_SESSION[$key]); //add this line
        }
    }
}
add_action('woocommerce_new_order', 'reset_all_product_prices', 10, 1);

function update_product_price_on_cart(){

	if( !empty($_POST['products']) ) {
		$products = is_array( $_POST['products'] ) ? $_POST['products'] : array();
		foreach( $products as $product_id => $product_price ) :
			$_SESSION['woo_user_price_'.$product_id] = $product_price;
		endforeach;
		echo '1';
	} else {
		echo '0';
	}
	wp_die(); //For Proper Output
}
add_action('wp_ajax_update_product_price_on_cart', 			'update_product_price_on_cart');
add_action('wp_ajax_nopriv_update_product_price_on_cart', 	'update_product_price_on_cart');


function load_scripts2(){
    wp_enqueue_script('js', get_theme_file_uri('js.js'), array('jquery'), null, true );
	wp_localize_script('js', 'Vlogfund', array('ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http') ) ) );
}
add_action('wp_enqueue_scripts', 'load_scripts2');



//remove quantity field

function wc_remove_all_quantity_fields( $return, $product ) {
    return true;
}
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );



//allow only one product in cart

/*function check_if_cart_has_product( $valid, $product_id, $quantity ) {

            if(!empty(WC()->cart->get_cart()) && $valid){
                foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                    $_product = $values['data'];

                    if( $product_id == $_product->get_id() ) {
                        unset(WC()->cart->cart_contents[$cart_item_key]);
                    }
                }
            }

            return $valid;

        }
        add_filter( 'woocommerce_add_to_cart_validation', 'check_if_cart_has_product', 10, 3 );*/



/*add_filter( 'woocommerce_add_cart_item_data', 'woo_custom_add_to_cart' );

function woo_custom_add_to_cart( $cart_item_data ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}*/


//Allow only 1 product in cart
function only_one_product_allow_in_cart( $passed, $added_product_id ) {

	global $woocommerce;

	// empty cart: new item will replace previous
	$woocommerce->cart->empty_cart();

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'only_one_product_allow_in_cart', 99, 2 );


/*******************************************************/
/**login**/
/*******************************************************/



/*add_action( 'init', 'blockusers_init' );
function blockusers_init() {
if ( is_admin() && ! current_user_can('shop_manager') && ! current_user_can('author') && ! current_user_can( 'administrator' ) &&
! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
wp_redirect( home_url() );
exit;
}
}*/





add_action('init','custom_login');
function custom_login(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow && !is_user_logged_in()) {
  wp_redirect('/#login');
  exit();
 }
    else if( 'wp-login.php' == $pagenow && is_user_logged_in()) {
       wp_redirect('/');
    }
}



add_filter('woocommerce_login_redirect', 'bryce_wc_login_redirect');
function bryce_wc_login_redirect( $redirect ) {

    if ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) || current_user_can( 'author' ) || current_user_can( 'editor' )  ) {
     $redirect = '/wp-admin';

	 } else if ( current_user_can( 'subscriber' ) || current_user_can( 'customer' ) ) {
       $redirect = '/account/my-campaigns';
    }
     return $redirect;
}



//woocommerce social login

//Update User Role to "Subscriber" when created from Woocommerce Social Login Plugin
function wp_social_login_change_user_role( $userdata ) {
	$userdata['role'] = 'subscriber';
	return $userdata;
}
add_action( 'wc_social_login_facebook_new_user_data', 	'wp_social_login_change_user_role', 20); //For Facebook
add_action( 'wc_social_login_google_new_user_data', 	'wp_social_login_change_user_role', 20); //For Google





//login form

function my_form_shortcode() {
    ob_start();
    get_template_part('login_form');
    return ob_get_clean();
}
add_shortcode( 'my_form_shortcode', 'my_form_shortcode' );


/*function ajax_login_init(){

    wp_enqueue_script('js', get_theme_file_uri('js.js'), array('jquery'), null, true );


    //Global $wp;

    wp_localize_script( 'js', 'ajax_login_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http') ),
        'redirecturl' => get_permalink(), //$_SERVER[‘REQUEST_URI’], //home_url() also works //home_url(add_query_arg(array(),$wp->request)),
				//'redirecturl' => add_query_arg ('welc_back', '1', get_permalink ()) ;
        'loadingmessage' => __('Sending user info, please wait...')

    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}


function ajax_login(){

    // First check the nonce, if it fails the function will break
    //check_ajax_referer( 'ajax-login-nonce', 'security_login' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    //$user_signon = wp_signon( $info, false ); //can be set to true or replace by $user_signon = wp_signon($info, is_ssl() ? true : false); or $user_signon = wp_signon($info); the function will decide for you
    $user_signon = wp_signon($info, is_ssl() ? true : false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username/email or password.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }

    die();
}*/


//Registration Form

function registration_form_shortcode() {
    ob_start();
    get_template_part('registration_form');
    return ob_get_clean();
}
add_shortcode( 'registration_form_shortcode', 'registration_form_shortcode' );


//Login and logout message



/* Custom Changes */
function login_footer_add_goodbye_message() {
	if( ( isset( $_GET['loggedout'] ) && $_GET['loggedout'] == "true" ) || ( isset($_GET['bye'] ) && $_GET['bye'] == 1 ) ) { ?>

        <script type="text/javascript">
		//Logout trigger
		jQuery(document).ready(function(e) {
        	toastr.success('', 'Goodbye!');
        });
		</script>
		<?php
	}
}
add_action( 'login_footer', 'login_footer_add_goodbye_message', 20 );
add_action( 'wp_footer', 'login_footer_add_goodbye_message', 20 );




//welcome message
function wp_footer_add_welcome_message_new() {
	if( ! isset( $_GET['welc'] ) || ! $_GET['welc'] )
		return;
	?>
	<script type="text/javascript">
	//Catch login actions
	jQuery(document).ready(function(e) {
		toastr.success('', 'Welcome');
	});
        /*jQuery(document).on('upvote', function(event, data, form){
		if( data.success == 1 ){
			toastr.success('', 'Thank you for voting');
		}
	});*/
	</script>
	<?php
}
add_action( 'wp_footer', 'wp_footer_add_welcome_message_new', 20 );
//add_action( 'admin_footer', 'wp_footer_add_welcome_message_new', 20 );

//welcome back message
function wp_footer_add_welcome_back_message_new() {
	if( ! isset( $_GET['welc_back'] ) || ! $_GET['welc_back'] )
		return;
	?>
	<script type="text/javascript">
	//Catch login actions
	jQuery(document).ready(function(e) {
		toastr.success('', 'Welcome');
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'wp_footer_add_welcome_back_message_new', 20 );


//welcome message
function add_welcome_message_register_redirect( $redirect_to, $request, $user ) {
	return add_query_arg( 'welc', 1, $redirect_to );
}
add_filter( 'login_redirect', 'add_welcome_message_register_redirect', 100, 3 );



//welcome back message
function add_welcome_back_message_login_redirect( $redirect_to, $request, $user ) {
	return add_query_arg( 'welc_back', 1, $redirect_to );
}
add_filter( 'login_redirect', 'add_welcome_back_message_login_redirect', 100, 3 );







function add_welcome_message_logout_redirect( $logout_url, $redirect ) {
    $redirect	= add_query_arg( 'bye', 1, $redirect );
	return add_query_arg( 'redirect_to', $redirect, $logout_url );
}
add_filter( 'logout_url', 'add_welcome_message_logout_redirect', 100, 2 );






















/** blog **/


add_shortcode('incrementor', 'incrementor');
function incrementor() {
static $i = 1;
return $i ++;
}


add_action( 'pvc_after_count_visit', 'update_toolset_view_count2' );
function update_toolset_view_count2 ( $post_id ) {
  $post_type = 'post';
  // only update the post view count for products
  if ($post_type == get_post_type($post_id)) {
    $view_count = get_post_meta($post_id, 'wpcf-post-view-count', true);
    $view_count = $view_count ? $view_count : 0;
    // update and increment the view count for this product
    update_post_meta($post_id, 'wpcf-post-view-count', (intval($view_count) + 1));
  }
}



/*add_action('cred_save_data', 'get_yt_id_3_data_action',10,2);
function get_yt_id_3_data_action($post_id, $form_data)
{
    // if a specific form
    if ($form_data['id']==98 || 216)
    {
        if (isset($_POST['wpcf-']))
        {
            $link = $_POST['wpcf-'];
            $video_id = explode("?v=", $link); // For videos like http://www.youtube.com/watch?v=...
            if (empty($video_id[1]))
                $video_id = explode("/v/", $link); // For videos like http://www.youtube.com/watch/v/..

            $video_id = explode("&", $video_id[1]); // Deleting any other params
            $video_id = $video_id[0]; // here is your required video ID

            // add it to saved post meta
            add_post_meta($post_id, 'wpcf-id', $video_id, true);
        }
    }
}*/






/*******************************************************/
/**Emails**/
/*******************************************************/

// Function to change email address

function wpb_sender_email( $original_email_address ) {
    return 'noreply@vlogfund.com';
}

// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return 'Vlogfund';
}

// Hooking up our functions to WordPress filters
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );















/*******************************************************/
/** admin **/
/*******************************************************/


// add GTM to admin

function add_gtm() {

    echo '<script type="text/javascript">';
    echo '(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,"script","dataLayer","GTM-PZJ9CCT");';
    echo '</script>';
}

add_filter('admin_head', 'add_gtm');





// unregister all widgets
 function unregister_default_widgets() {
     unregister_widget('WP_Widget_Pages');
     unregister_widget('WP_Widget_Calendar');
     unregister_widget('WP_Widget_Archives');
     unregister_widget('WP_Widget_Links');
     unregister_widget('WP_Widget_Meta');
     unregister_widget('WP_Widget_Search');
     unregister_widget('WP_Widget_Text');
     unregister_widget('WP_Widget_Categories');
     unregister_widget('WP_Widget_Recent_Posts');
     unregister_widget('WP_Widget_Recent_Comments');
     unregister_widget('WP_Widget_RSS');
     unregister_widget('WP_Widget_Tag_Cloud');
     unregister_widget('WP_Nav_Menu_Widget');
 }
 add_action('widgets_init', 'unregister_default_widgets', 11);


// remove ajax dynamic css

add_action( 'after_setup_theme', 'layout2job_func', 99 );
function layout2job_func(){
    remove_action( 'wp_enqueue_scripts', 'ref_enqueue_customizer_css', 100 );
    remove_action( 'wp_ajax_ref_dynamic_css', 'ref_dynamic_css' );
    remove_action( 'wp_ajax_nopriv_ref_dynamic_css', 'ref_dynamic_css' );
}




// Custom WordPress Footer
function remove_footer_admin () {
	$site_title = get_bloginfo( 'name' );
	echo '&copy; 2018 - ' . $site_title;
}
add_filter('admin_footer_text', 'remove_footer_admin');

// remove wp logo
add_action('admin_bar_menu', 'remove_wp_logo', 999);
function remove_wp_logo( $wp_admin_bar ) {
$wp_admin_bar->remove_node('wp-logo');
}

function my_footer_shh() {
    remove_filter( 'update_footer', 'core_update_footer' );
}
add_action( 'admin_menu', 'my_footer_shh' );




// Remove dashboard widgets
function remove_dashboard_meta() {
	if ( ! current_user_can( 'update_core' ) ) {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
        remove_meta_box( 'wpe_dify_news_feed', 'dashboard', 'normal');
        remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
        remove_meta_box( 'pvc_dashboard', 'dashboard', 'normal');
        remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal');

	}
}
add_action( 'admin_init', 'remove_dashboard_meta' );


//Remove Admin Bar Links
function remove_admin_bar_links() {
    global $wp_admin_bar;
    //$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    //$wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    //$wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    //$wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    //$wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
   // $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    //$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
    $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
    $wp_admin_bar->remove_menu('view-store');        // Remove the store site link
    $wp_admin_bar->remove_menu('edit');              // Edit page
    $wp_admin_bar->remove_menu('ddl-front-end-editor'); // Frontend Editor
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
    $wp_admin_bar->remove_menu('new-content');      // Remove the content link
    //$wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
    $wp_admin_bar->remove_menu('toolset-shortcodes'); //
		$wp_admin_bar->remove_menu('wpseo-menu'); //

}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );




//Capabilities for shop managers

function wpexplorer_remove_menus0() {

if ( ! current_user_can( 'administrator' ) ) {
	      remove_menu_page( 'tools.php' );
          remove_menu_page( 'tools' );
          remove_menu_page( 'edit.php?post_type=page' );
          remove_menu_page( 'page' );
    }
}
add_action( 'admin_menu', 'wpexplorer_remove_menus0' );


//Capabilities for authors

function wpexplorer_remove_menus1() {

if ( ! current_user_can( 'administrator' ) && ! current_user_can( 'shop_manager' )  ) {
	  remove_menu_page( 'edit.php?post_type=product' );
    remove_menu_page( 'product' );
    remove_menu_page( 'edit.php?post_type=organization' );
		remove_menu_page( 'edit.php?post_type=update' );
    remove_menu_page( 'organization' );
    remove_menu_page( 'tools.php' );
		remove_menu_page( 'edit-comments.php' );
    remove_menu_page( 'tools' );
    }
}
add_action( 'admin_menu', 'wpexplorer_remove_menus1' );



//remove admin bar
/*add_action('set_current_user', 'remove_admin_bar');
function remove_admin_bar() {
if ( ! current_user_can('shop_manager') && ! current_user_can('author') && ! current_user_can( 'administrator' ) ) {
  show_admin_bar(false);
}
}

add_action('set_current_user', 'cc_hide_admin_bar');
add_filter(‘show_admin_bar’, ‘__return_false’);
function cc_hide_admin_bar() {
  if (!current_user_can('edit_posts')) {
    show_admin_bar(false);
  }
}*/

add_action( 'after_setup_theme', 'my_website_remove_admin_bar' );
add_action('set_current_user', 'my_website_remove_admin_bar');
function my_website_remove_admin_bar() {
   if ( ! current_user_can( 'administrator' ) )
      show_admin_bar( false );

}






// Remove Woocommerce Submenu Items
function wooninja_remove_items() {
 $remove = array( 'wc-settings', 'wc-status', 'wc-addons','th_checkout_field_editor_pro', );
  foreach ( $remove as $submenu_slug ) {
   if ( ! current_user_can( 'update_core' ) ) {
    remove_submenu_page( 'woocommerce', $submenu_slug );
   }
  }
}
add_action( 'admin_menu', 'wooninja_remove_items', 99, 0 );


/*Rename Woocommerce Admin Menu*/
add_action( 'admin_menu', 'rename_woocoomerce', 999 );

function rename_woocoomerce()
{
    global $menu;

    // Pinpoint menu item
    $woo = recursive_array_search_php_91365( 'WooCommerce', $menu );

    // Validate
    if( !$woo )
        return;

    $menu[$woo][0] = 'Fundraising';
}

function recursive_array_search_php_91365( $needle, $haystack )
{
    foreach( $haystack as $key => $value )
    {
        $current_key = $key;
        if(
            $needle === $value
            OR (
                is_array( $value )
                && recursive_array_search_php_91365( $needle, $value ) !== false
            )
        )
        {
            return $current_key;
        }
    }
    return false;
}



//Change product custom post type name
/*add_filter( 'woocommerce_register_post_type_product', 'custom_post_type_label_woo' );

function custom_post_type_label_woo( $args ){
    $labels = array(
        'menu_name'          => _x( 'Campaigns', 'Admin menu name', 'woocommerce' ),
    );

    $args['labels'] = $labels;
    return $args;
}


//Change order custom post type name
add_filter( 'woocommerce_register_post_type_shop_order', 'custom_post_type_label_woo2' );

function custom_post_type_label_woo2( $args ){
    $labels = array(
        'menu_name'          => _x( 'Contributions', 'Admin menu name', 'woocommerce' ),
    );

    $args['labels'] = $labels;
    return $args;
}*/





add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    body, td, textarea, input, select {
      font-family: -apple-system,BlinkMacSystemFont,SegoeUI, "Helvetica Neue", Helvetica, sans-serif;
      font-size: 12px;
    }
    span.js-wpv-fields-and-views-in-toolbar, span.js-cred-in-toolbar, a.js-layout-private-add-new-top, span.js-wpcf-access-editor-button {
    display: none!important;
    }
		table.wp-list-table .column-price {
		width: 10%;
}
  </style>';
}




	// add new custom widget
	add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

	function my_custom_dashboard_widgets() {
	global $wp_meta_boxes;

	wp_add_dashboard_widget('custom_help_widget', 'Quick Links', 'custom_dashboard_help');
	}

	function custom_dashboard_help() {
	echo '
	<ul>
	<li><a href="https://www.freelancer.com/dashboard/" target="_blank">Freelancer</a></li>
	<li><a href="https://trends.google.com/trends/home/e/US" target="_blank">Google Trends</a></li>
	<li><a href="https://adwords.google.com/KeywordPlanner" target="_blank">Keyword Planner</a></li>
    <li><a href="https://yoast.com/suggest" target="Yoast Google Suggest"></a></li>
	</ul>
	';
	}


/*******************************************************/
/** Security **/
/*******************************************************/
//Remove wordpress version
remove_action('wp_head', 'wp_generator');



/*******************************************************/
/** dataLayer **/
/*******************************************************/
//Pass post status to tag manager
function vlogfund_datalayer_post_status( $dataLayer ) {
	global $post;
	if( is_singular() ) :
		$dataLayer['postStatus'] = $post->post_status;
	endif;
	return $dataLayer;
}
add_filter( 'gtm4wp_compile_datalayer', 'vlogfund_datalayer_post_status' );

//Push featured image to dataLayer

function vlogfund_datalayer_post_featured_image( $dataLayer, $post = null, $size = 'post-thumbnail' ) {
	global $post;
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	$yt_thumbnail_url_1 = 'i.ytimg.com/vi/';
	$yt_thumbnail_url_2 = '/hqdefault.jpg';

	if( get_post_type( get_the_ID() ) == 'post' && ( $post_thumbnail_id ) ) :
		$dataLayer['postFeaturedImage'] = wp_get_attachment_image_url( $post_thumbnail_id, $size );

	elseif ( get_post_type( get_the_ID() ) == 'post' &&  (! $post_thumbnail_id ) ):
        $dataLayer['postFeaturedImage'] = do_shortcode ($yt_thumbnail_url_1.( '[types field="post-youtube-video-id" output="raw"][/types]' ).$yt_thumbnail_url_2);

	endif;
	return $dataLayer;
}
add_filter( 'gtm4wp_compile_datalayer', 'vlogfund_datalayer_post_featured_image' );


//Push post excerpt to dataLayer

function vlogfund_datalayer_post_excerpt( $dataLayer ) {

	global $post;

	if( is_singular() ) :
		$dataLayer['postExcerpt'] = wp_trim_words( get_the_content($post ), 25 );

	endif;
	return $dataLayer;
}
add_filter( 'gtm4wp_compile_datalayer', 'vlogfund_datalayer_post_excerpt' );

//Push post modified data to dataLayer

function vlogfund_datalayer_post_modified( $dataLayer ) {

	global $post;

	if( is_singular() ) :
		$dataLayer['postModified'] = get_the_modified_date( );

	endif;
	return $dataLayer;
}
add_filter( 'gtm4wp_compile_datalayer', 'vlogfund_datalayer_post_modified' );



//Push Youtube channel logo to dataLayer

function vlogfund_datalayer_campaign_infos ( $dataLayer ) {
	global $post;

		if( get_post_type( get_the_ID() ) == 'product' ) :
		$dataLayer['collaborator1'] = do_shortcode ( '[types field="collaborator-1" output="raw"][/types]' );
		$dataLayer['collaborator2'] = do_shortcode ( '[types field="collaborator-2" output="raw"][/types]' );
		$dataLayer['collaborator1Image'] = do_shortcode ( '[types field="collaborator-1-image" output="raw"][/types]' );
    $dataLayer['collaborator2Image'] = do_shortcode ( '[types field="collaborator-2-image" output="raw"][/types]' );
		$dataLayer['collaborator1YouTubeVideo'] = do_shortcode ( '[types field="youtube-video-collaborator-1" output="raw"][/types]' );
    $dataLayer['collaborator2YouTubeVideo'] = do_shortcode ( '[types field="youtube-video-collaborator-2" output="raw"][/types]' );
		$dataLayer['collaborator1Quote'] = do_shortcode ( '[types field="collaborator-1-quote" output="raw"][/types]' );
    $dataLayer['collaborator2Quote'] = do_shortcode ( '[types field="collaborator-2-quote" output="raw"][/types]' );
	  $dataLayer['collaborationType'] = do_shortcode ( '[types field="collaboration-type" output="raw"][/types]' );

	endif;
	return $dataLayer;
}
add_filter( 'gtm4wp_compile_datalayer', 'vlogfund_datalayer_campaign_infos' );







//Organization Post Count
// Get post count for cpt
add_shortcode('postcount', 'post_count');
function post_count() {
    $count_posts = wp_count_posts('organization');
    $published_posts = $count_posts->publish;
    return $published_posts . ' ';
}

//Milestone Shortcode
if( !function_exists('vlogfund_campaign_milestone_shortcode') ) :
function vlogfund_campaign_milestone_shortcode( $atts, $content = null ){
	extract( shortcode_atts( array(
		'product_id' => get_the_ID()
	), $atts, 'vlogfund_campaign_milestone' ) );
	//Total Sales
	$total_sales = get_post_meta($product_id,'_product_total_sales',true) ? get_post_meta($product_id,'_product_total_sales',true) : 0;
	$content = '';
	//$total_sales = 1100250; //Testing Value
	if( $total_sales <= 10000 ) : //Milestone 1 $10 000
		$milestone_percent = ( number_format( ( $total_sales / 10000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$10 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>1</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $100 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 10000 && $total_sales <= 100000 ) : //Milestone 2 $100 000
		$milestone_percent = ( number_format( ( $total_sales / 100000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$100 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>2</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $500 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 100000 && $total_sales <= 500000 ) : //Milestone 3 $500 000
		$milestone_percent = ( number_format( ( $total_sales / 500000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$500 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>3</strong></span>
							<span class="sf-next-milestone-txt">'.__('Next Milestone: $1 000 000').'</span>
						</div>
					</div>';
	elseif( $total_sales > 500000 && $total_sales <= 1000000 ) : //Milestone 4 $1 000 000
		$milestone_percent = ( number_format( ( $total_sales / 1000000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:'.$milestone_percent.'%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">$'.$total_sales.'</span><span class="end">$1 000 000</span></div>
						<div class="sf-milestone-txt-content">
							<span class="sf-milestone-txt">'.__('Milestone').' <strong>4</strong></span>
						</div>
					</div>';
	elseif( $total_sales > 1000000 ) : //All Milestone Over
		$milestone_percent = ( number_format( ( $total_sales / 1000000 ) * 100, 2 ) );
		$content .= '<div class="sf-milestone-progress-wrapper">
						<div class="sf-milestone-progress">
							<div class="sf-milestone-container" style="width:100%"><span>'.$milestone_percent.'%</span></div>
						</div>
						<div class="sf-milestone-values"><span class="start">Campaign Goal Reached</span><span class="end">$1 000 000</span></div>
					</div>';
	endif;
	return $content;
}
add_shortcode('vlogfund_campaign_milestone', 'vlogfund_campaign_milestone_shortcode');
endif;

//Check Smile Mode
if( !function_exists('vlogfund_smile_mode_on') ) :
function vlogfund_smile_mode_on(){
	if( isset( $_COOKIE['vlogfundsmilemode'] ) && !empty( $_COOKIE['vlogfundsmilemode'] ) ) :
		return 1;
	else :
		return 0;
	endif;
}
endif;
//Change Brand Color for smile Mode
if( !function_exists('vlogfund_smile_mode_theme') ) :
function vlogfund_smile_mode_theme(){
	if( vlogfund_smile_mode_on() ) : ?>
    	<style type="text/css">
			:root {	--brand-color: #dc2c5d !important; }
		</style>
	<?php endif;
}
add_action('wp_head','vlogfund_smile_mode_theme');
endif;



/** admin text and visual editor fix **/

// paste everything below this line in your WP functions.php file below everything else.
// always backup your data

function allow_all_tinymce_elements_attributes( $init ) {

    // Allow all elements and all attributes
    $ext = '*[*]';

    // Add to extended_valid_elements if it already exists
    if ( isset( $init['extended_valid_elements'] ) ) {
        $init['extended_valid_elements'] .= ',' . $ext;
    } else {
        $init['extended_valid_elements'] = $ext;
    }

    // return value
    return $init;
}
add_filter('tiny_mce_before_init', 'allow_all_tinymce_elements_attributes');



/*******************************************************/
/** amp **/
/*******************************************************/

/*add_action( 'amp_post_template_css', function( $amp_template ) {
    ?>
    nav.amp-wp-title-bar {background: #000;}
    ul.jp-amp-list {
        list-style: none;
        display: inline;
    }

    ul.jp-amp-list li {
        display: inline;
        margin: 0 8px;
    }

		.amp-wp-byline span.amp-wp-author {
			display: none;
		}

		footer.amp-wp-footer div p {
			display: none;
		}
    <?php
});*/




/*add_action( 'pre_amp_render_post', function () {
    add_filter( 'the_content', function( $content ){
        if ( has_post_thumbnail() ) {
            $image = sprintf( '<p class="jp-amp-featured-image">%s</p>', get_the_post_thumbnail() );
            $content = $image . $content;
        }
        return $content;
    }, 3 );
});*/


/*add_action( 'pre_amp_render_post', function () {
    add_filter( 'the_content', function( $content ){
        $menu_name = 'amp';
        $menu = wp_get_nav_menu_object( $menu_name );
        if ( ! empty( $menu ) ) {
            $menu_items = wp_get_nav_menu_items( $menu->term_id );
            $menu_list = sprintf( '<br /><ul id="%s" class="jp-amp-list">Menu: ', esc_attr( 'amp-jp-menu-' . $menu_name ) );
            foreach ( $menu_items as $key => $menu_item ) {
                $menu_list .= sprintf( '<li><a href="%s">%s</a></li>', amp_get_permalink( $menu_item->object_id ), esc_html( $menu_item->title ) );
            }
            $menu_list .= '</ul>';
            $content .= $menu_list;
        }
        return $content;
    }, 1000 );
});



add_action( 'pre_amp_render_post', function () {
    add_filter( 'the_content', function( $content ){
        $post = get_post();
        if( is_object( $post ) ){
            $twitter = add_query_arg( array(
                'url' => urlencode( get_permalink( $post->ID ) ),
                'status' => urlencode( $post->post_title )
            ),'https://twitter.com/share' );
            $facebook = add_query_arg( array(
                    'u' => urlencode( get_permalink( $post->ID ) )
                ), 'https://www.facebook.com/sharer/sharer.php'
            );
        }
        $share = sprintf( '<hr /><ul id="amp-jp-share" class="jp-amp-list">Share: <li id="twitter-share"><a href="%s" title="Share on Twitter">Twitter</a></li><li id="facebook-share"><a href="%s" title="Share on Facebook">Facebook</a></ul>', esc_url_raw( $twitter ), esc_url_raw( $facebook ) );
        $content  .= $share;
        return $content;
    }, 1000 );
});*/



add_filter( 'amp_post_template_file', 'amp_set_cutome_style_path', 10, 3 );  // Setting custom stylesheet

function amp_set_cutome_style_path( $file, $type, $post ) {

if ( 'style' === $type ) {

    $file = dirname( __FILE__ ) . '/amp/style.php';

}
  return $file;
}




add_filter( 'amp_post_template_data', 'xyz_amp_set_custom_site_icon_url' ); //Changes site icon

function xyz_amp_set_custom_site_icon_url( $data ) {

    $data[ 'site_icon_url' ] = get_stylesheet_directory_uri().'/images/vf-logo.png';

return $data;

}




add_filter( 'amp_post_template_file', 'set_my_amp_custom_template', 10, 3 ); //Setting custom template for amp page

function set_my_amp_custom_template( $file, $type, $post ) {

if ( 'single' === $type ) {

    $file = dirname( __FILE__ ) . '/amp/vf-single.php';
} if ( 'footer' === $type ) {

    $file = dirname( __FILE__ ) . '/amp/vf-footer.php';
}

  return $file;
}



add_action( 'amp_post_template_head', 'amp_add_google_analytics' );  // Adding google Analytic script to head tag of Amp page

function  amp_add_google_analytics( $amp_template ) { ?>

     <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>

<?php }


/**
 * Add related posts to AMP amp_post_article_footer_meta
 */
/*function my_amp_post_article_footer_meta( $parts ) {

    $index = 1;

    array_splice( $parts, $index, 0, array( 'my-related-posts' ) );

    return $parts;
}
add_filter( 'amp_post_article_footer_meta', 'my_amp_post_article_footer_meta' );

/**
 * Designate the template file for related posts
 */
/*function my_amp_related_posts_path( $file, $template_type, $post ) {

    if ( 'my-related-posts' === $template_type ) {
        $file = get_stylesheet_directory() . '/amp/related-posts.php';
    }
    return $file;
}
add_filter( 'amp_post_template_file', 'my_amp_related_posts_path', 10, 3 );
