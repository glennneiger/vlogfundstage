<?php
/**
 * Register Custom Post Type YouTube Channels
 *
 * Handles all register custom post type youtube channels
 *
 * @since YouTube Channels 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( !function_exists('ytc_register_youtube_channels_post_type') ) :
function ytc_register_youtube_channels_post_type(){

	//YouTube Channels
	$yt_channels_labels = array(
		'name'				=>	_x( 'YouTube Channels', 'post type general name', 'youtube-channels' ),
		'singular_name'		=>	_x( 'Channel', 'post type singular name', 'youtube-channels' ),
		'menu_name'			=>	_x( 'YouTube Channels', 'admin menu', 'youtube-channels' ),
		'name_admin_bar'	=>	_x( 'YouTube Channel', 'add new on admin bar', 'youtube-channels' ),
		'add_new'			=>	_x( 'Add New', 'youtube_channels', 'youtube-channels' ),
		'add_new_item'		=>	__( 'Add New Channel', 'youtube-channels' ),
		'new_item'			=>	__( 'New Channel', 'youtube-channels' ),
		'edit_item'			=>	__( 'Edit Channel', 'youtube-channels' ),
		'view_item'			=>	__( 'View Channel', 'youtube-channels' ),
		'all_items'			=>	__( 'All Channels', 'youtube-channels' ),
		'search_items'		=>	__( 'Search Channels', 'youtube-channels' ),
		'parent_item_colon'	=>	__( 'Parent Channels:', 'youtube-channels' ),
		'not_found'         =>	__( 'No channels found.', 'youtube-channels' ),
		'not_found_in_trash'=>	__( 'No channels found in Trash.', 'youtube-channels' )
	);

	//Post Type Arguments
	$yt_channels_args = array(
		'labels'			=>	$yt_channels_labels,
		'description'		=>	__( 'YouTube Channels.', 'youtube-channels' ),
		'public'           	=>	true,
		'publicly_queryable'=>	true,
		'show_ui'           =>	true,
		'show_in_menu'      =>	true,
		'query_var'         =>	true,
		'rewrite'           =>	array( 'slug' => 'youtube-channel', 'with_front' => false ),
		'capability_type'   =>	'post',
		'has_archive'       =>	false,
		'hierarchical'      =>	false,
		'menu_position'     =>	null,
		'menu_icon'			=>	'dashicons-video-alt3',
		'supports'          => array( 'title', 'editor' )
	);
	//Register Post Type
	register_post_type( 'youtube_channels', $yt_channels_args );

}
add_action( 'init', 'ytc_register_youtube_channels_post_type' );
endif;
if( !function_exists('ytc_channels_updated_messages') ) :
/**
 * YouTube Channels update messages.
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 **/
function ytc_channels_updated_messages( $messages ){

	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['youtube_channels'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Channel updated.', 'youtube-channels' ),
		2  => __( 'Custom field updated.', 'youtube-channels' ),
		3  => __( 'Custom field deleted.', 'youtube-channels' ),
		4  => __( 'Channel updated.', 'youtube-channels' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Channel restored to revision from %s', 'youtube-channels' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Channel published.', 'youtube-channels' ),
		7  => __( 'Channel saved.', 'youtube-channels' ),
		8  => __( 'Channel submitted.', 'youtube-channels' ),
		9  => sprintf(
			__( 'Channel scheduled for: <strong>%1$s</strong>.', 'youtube-channels' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'youtube-channels' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Channel draft updated.', 'youtube-channels' )
	);

	if ( $post_type_object->publicly_queryable && 'youtube_channels' === $post_type ) {

		$permalink = get_permalink( $post->ID );
		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Channel', 'youtube-channels' ) );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Channel', 'youtube-channels' ) );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}
add_filter( 'post_updated_messages', 'ytc_channels_updated_messages' );
endif;
