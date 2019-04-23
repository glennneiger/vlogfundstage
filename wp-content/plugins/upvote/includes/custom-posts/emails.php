<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Post Type for Subscribers
 *
 * Handles to post type for subscribers
 *
 * @since Upvote 1.0
 **/
if( !function_exists('upvote_register_emails_post_type') ) :
/**
 * Post Type for Subscribers
 *
 * Handles to post type for subscribers
 *
 * @since Upvote 1.0
 **/
function upvote_register_emails_post_type(){

	//Emails Labels
	$emailslabels = array(
		'name'				=>	_x('Emails', 'post type general name', 'upvote'),
		'singular_name'		=>	_x('Email', 'post type singular name', 'upvote'),
		'menu_name'			=>	_x('Upvote', 'admin menu', 'upvote'),
		'name_admin_bar'	=>	_x('Email', 'add new on admin bar', 'upvote'),
		'add_new'			=>	_x('Add New', 'upvote-emails', 'upvote'),
		'add_new_item'		=>	__('Add New', 'upvote'),
		'new_item'			=>	__('New Email', 'upvote'),
		'edit_item'			=>	__('Edit Email', 'upvote'),
		'view_item'			=>	__('View Email', 'upvote'),
		'all_items'			=>	__('All Emails', 'upvote'),
		'search_items'		=>	__('Search Emails', 'upvote'),
		'parent_item_colon' =>	__('Parent Emails:', 'upvote'),
		'not_found'         =>	__('No emails found.', 'upvote'),
		'not_found_in_trash'=>	__('No emails found in Trash.', 'upvote')
	);
	//Emails Arguments
	$emailsargs = array(
		'labels'			=>	$emailslabels,
		'description'		=>	__( 'Description.', 'upvote' ),
		'public'            =>	true,
		'publicly_queryable'=>	false,
		'show_ui'           =>	true,
		'show_in_menu'      =>	true,
		'query_var'         =>	false,
		'rewrite'           =>	false,
		'capability_type'   =>	'post',
		'capabilities' 		=> 	array('create_posts' => 'do_not_allow'),
		'has_archive'       =>	false,
		'hierarchical'      =>	false,
		'menu_position'     =>	71,
		'menu_icon'			=>	'dashicons-thumbs-up',
		'supports'          =>	array( 'title' )
	);

	//Register Subscribers Post Type
	register_post_type( 'upvote-emails', $emailsargs );
	
}
add_action('init', 'upvote_register_emails_post_type');
endif;
if( !function_exists('upvote_emails_updated_messages') ) :
/**
 * Emails Update Messages
 * 
 * Handles to update messages for email
 *
 * @since Upvote 1.0
 **/
function upvote_emails_updated_messages( $messages ) {
	
  	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['upvote-emails'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Email updated.', 'upvote' ),
		2  => __( 'Custom field updated.', 'upvote' ),
		3  => __( 'Custom field deleted.', 'upvote' ),
		4  => __( 'Email updated.', 'upvote' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Email restored to revision from %s', 'upvote' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Email published.', 'upvote' ),
		7  => __( 'Email saved.', 'upvote' ),
		8  => __( 'Email submitted.', 'upvote' ),
		9  => sprintf(
			__( 'Email scheduled for: <strong>%1$s</strong>.', 'upvote' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'upvote' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Email draft updated.', 'upvote' )
	);

	if ( $post_type_object->publicly_queryable && 'upvote-emails' === $post_type ) {
		$permalink = get_permalink( $post->ID );
		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Email', 'upvote' ) );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;
		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Email', 'upvote' ) );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}
add_filter('post_updated_messages', 'upvote_emails_updated_messages');
endif;
if( !function_exists('upvote_emails_columns') ) :
/**
 * Emails Columns
 * 
 * Handles to emails column
 *
 * @since Upvote 1.0
 **/
function upvote_emails_columns($columns) {
    unset( $columns['date'] );
	$columns['name'] = __( 'Name', 'upvote' );
	$columns['product'] = __( 'Product', 'upvote' );
    $columns['date'] = __( 'Date', 'upvote' );
    return $columns;
}
add_filter( 'manage_upvote-emails_posts_columns', 'upvote_emails_columns' );
endif;
if( !function_exists('upvote_emails_column') ) :
/**
 * Emails Columns
 * 
 * Handles to emails column
 *
 * @since Upvote 1.0
 **/
function upvote_emails_column( $column, $post_id ) {
    switch($column) {
		case 'product' : //Product
			$product = get_post_meta( $post_id, '_upvote_product', true);
			if( !empty( $product ) ) : //Check Product Set
				printf('<a href="%1$s" target="_blank">%2$s</a>', get_permalink($product), get_the_title($product));
			else : //Else
            	echo '&mdash;'; 
			endif; //Endif
			break;
		case 'name' : //Username
			echo get_post_meta( $post_id, '_upvote_username', true) ? get_post_meta( $post_id, '_upvote_username', true) : '&mdash;';
			break;
	}
}
add_action( 'manage_upvote-emails_posts_custom_column' , 'upvote_emails_column', 10, 2 );
endif;