<?php
/**
 * All Users Related Functions
 **/
if( !function_exists('vlog_user_last_login') ) :
//Record Users Last Login
function vlog_user_last_login( $user_login, $user ) {
	update_user_meta( $user->ID, 'last_login', time() );
}
add_action('wp_login', 'vlog_user_last_login', 10, 2);
endif; //Endif
if( !function_exists('vlog_user_custom_columns') ) :
//Show Users Last Login in Admin Column
function vlog_user_custom_columns( $column ) {
    $column['last_login'] = 'Last Login';
    return $column;
}
add_filter('manage_users_columns', 'vlog_user_custom_columns');
endif;
if( !function_exists('vlog_user_custom_columns_data') ) :
//Show Users Last Login in Admin Column
function vlog_user_custom_columns_data( $val, $column, $user_id ) {
    switch($column) :
        case 'last_login' :
			$last_login = get_user_meta( $user_id, 'last_login', time() );
            $val = !empty( $last_login )? date('d/m/Y', $last_login) : '&mdash;';
            break;
    endswitch;
    return $val;
}
add_filter('manage_users_custom_column', 'vlog_user_custom_columns_data', 10, 3);
endif;
if( !function_exists('vlog_disable_frontend_media_controls') ) :
//Disable Audio/Video Playlist
function vlog_disable_frontend_media_controls(){
	if( !is_admin() ) :
		return false;
	endif; //Endif
}
add_filter('media_library_show_audio_playlist', 'vlog_disable_frontend_media_controls');
add_filter('media_library_show_video_playlist', 'vlog_disable_frontend_media_controls');
endif;
if( !function_exists('vlog_disable_frontend_media_tabs') ) :
//Update Media Popup Strings
function vlog_disable_frontend_media_tabs($strings){
	if( !is_admin() ) :
		unset($strings['addMedia']);
		unset($strings['insertMediaTitle']);
		unset($strings['createGalleryTitle']);
		unset($strings['mediaLibraryTitle']);
		unset($strings['setFeaturedImageTitle']);
	endif; //Endif
	return $strings;
}
add_filter('media_view_strings', 'vlog_disable_frontend_media_tabs', 99);
endif;
if( !function_exists('vlog_change_media_default_active_tab') ) :
//Trigger Insert Media Tab
function vlog_change_media_default_active_tab(){ ?>
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$(document).on( 'click', '.insert-media', function(event){
				$('.media-menu .media-menu-item:last' ).trigger('click');
				$('.media-frame .media-frame-title h1').html('Insert from URL (YouTube, Twitter, Instagram...)');
				if( !$('.media-frame .media-frame-content p.media-info').length ){
					$('<p class="media-info">Here you can insert the link to your favorite YouTube Video, Twitter, or Instagram Post, It will render an embed on your campaign page when you save and preview the form.</p><p class="media-info">*Alternatively you can copy this shortcode [embed][/embed] add it to the editor and place your link inside it. This will render an embed as well when you save and preview your campaign.</p>').prependTo( $('.media-frame .media-frame-content') );
				}
			});
		});
	</script>
<?php }
add_action('wp_footer', 'vlog_change_media_default_active_tab');
endif;
if( !function_exists('vlog_allow_subscribers_to_upload_media') ) :
//Allow Subscribers to Upload Media
function vlog_allow_subscribers_to_upload_media(){
	if( current_user_can('subscriber') && !current_user_can('upload_files') ) :
		$subscriber = get_role('subscriber');
		$subscriber->add_cap('upload_files');
	endif; //Endif
}
add_action('init', 'vlog_allow_subscribers_to_upload_media');
endif;