<?php
/**
 * Admin Class
 *
 * Handles all admin functions
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Admin') ) :

class YTC_Admin{
	
	//Construct which run class
	public function __construct(){		
		//Admin Menu
		add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
		//Enqueue Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		//Admin AJAX
		add_action( 'wp_ajax_ytc_update_channels', array( $this, 'ytc_update_channels' ) );
	}
	
	/**
	 * Enqueue All Scripts / Styles
	 *
	 * @since YouTube Channels 1.0
	 **/
	public function register_scripts( $hook ){
		if( $hook != 'youtube_channels_page_update-channels ' ) : 
			//Script for Admin Function
			wp_enqueue_script( 'ytc-admin-script', YTC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), null, true );
			wp_localize_script( 'ytc-admin-script', 'YTC_Admin_Obj', array( 'secure'  => wp_create_nonce( 'ytc-secure-code' ) ) );
		endif; //Endif
	}
	
	/**
     * Register submenu
	 *
     * @since YouTube Channels 1.0
     */
    public function register_sub_menu() {
		add_submenu_page( 
            'edit.php?post_type=youtube_channels', 
			__('Update Channels','youtube-channels'), 
			__('Update Channels', 'youtube-channels'),
			'manage_options',
			'update-channels',
			array( $this, 'update_channels_submenu_callback' )
        );
    }
	
	/**
	 * Update Channels Submenu Page
	 *
	 * @since YouTube Channels 1.0
	 **/
	public function update_channels_submenu_callback(){
		
		global $wpdb;
		$date_before = date('Y-m-d', strtotime('-4days'));
		$total_to_update = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE 1=1 AND post_status = 'publish' AND post_type = 'youtube_channels' AND post_modified <= '$date_before';" );
		$total_to_update = !empty( $total_to_update ) ? $total_to_update : 0;
		
		echo '<div class="wrap">';
		echo '<h2>'.__('Update Channels','youtube-channels').'</h2><br>';
		echo '<p><strong>'.__('Update may take some time. Please do not close your browser or refresh the page until the process is complete.', 'youtube-channels').'</strong></p>';
		echo '<style type="text/css">';
		echo '.update-channel-progress-wrap{ width: 100%; background-color: #d3d3d3; margin-bottom:20px; display:none; }';
		echo '.update-channel-progress{ width:0%; height: 30px; background-color: green; }';
		echo '.result-count{ margin-bottom:20px; display:none; }';
		echo '</style>';
		echo '<div class="update-channel-progress-wrap"><div class="update-channel-progress"></div></div>';
		echo '<div class="result-count">Updated <span class="updated">0</span> out of <span class="total">'.$total_to_update.'</span></div>';
		echo '<a href="#" class="button button-primary update-channels-btn">'.__('Update','youtube-channels').'</a>';
		echo '<p>Records will be updated which are updated on or before '.date('Y-m-d', strtotime('-4days')).'</p>';
		echo '<div class="update-channel-results"></div>';		
		echo '</div>';
	}
	
	/**
	 * AJAX Callback For Update Channels
	 *
	 * @since YouTube Channels 1.0
	 **/
	public function ytc_update_channels(){
		global $wpdb;		
		if( ! check_ajax_referer( 'ytc-secure-code', 'secure' ) ) :
			wp_send_json_error( 'Invalid security token.' );
			wp_die(); //To Proper Output
		else : //Else Process Update
			$response = array('updated' => 0);
			$date_before = date('Y-m-d', strtotime('-4days'));
			$total_to_update = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE 1=1 AND post_modified <= '$date_before';" );
			$query = "SELECT m1.meta_value FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta AS m1 ON (m1.post_id = $wpdb->posts.ID)
					WHERE 1=1 AND $wpdb->posts.post_type = 'youtube_channels'
					AND m1.meta_key = 'wpcf-channel_id' AND $wpdb->posts.post_status = 'publish'
					AND $wpdb->posts.post_modified <= '$date_before' LIMIT 120;";
			$all_channels = $wpdb->get_col( $query );
			$updated = 0;
			if( !empty( $all_channels ) ) : //Check Channel Data
				$channel_slots = array_chunk( $all_channels, 40 );				
				foreach( $channel_slots as $channels_list ) :			
					$channel_ids = implode(',', $channels_list );					
					$use_yt_key = ytc_youtube_api_key(); //YTC_YOUTUBE_KEY; //
					$channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=topicDetails,status,brandingSettings,contentDetails,contentOwnerDetails,localizations,snippet,statistics&key='.$use_yt_key.'&id='.$channel_ids;
					$channel_data = file_get_contents( $channel_url, false);
					$channel_results = json_decode( $channel_data );					
					if( !empty( $channel_results->items ) ) : //Update the Channel Data
						foreach( $channel_results->items as $channel ) :
							$post_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 AND meta_key = 'wpcf-channel_id' AND meta_value = '".$channel->id."';" );
							if( !empty( $post_id ) ) :
								$updated_post = wp_update_post( array(
									'ID' => $post_id,
									'post_title'   => $channel->snippet->title,
								) );						
								 //Update Related Details
								update_post_meta( $post_id, 'wpcf-channel_views', 		$channel->statistics->viewCount );
								update_post_meta( $post_id, 'wpcf-channel_subscribers', $channel->statistics->subscriberCount );
								update_post_meta( $post_id, 'wpcf-channel_keywords', 	$channel->brandingSettings->keywords );
								update_post_meta( $post_id, 'wpcf-channel_img', 		$channel->snippet->thumbnails->medium->url );
								update_post_meta( $post_id, 'wpcf-channel_banner', 		$channel->brandingSettings->image->bannerImageUrl );
								update_post_meta( $post_id, 'wpcf-channel_videos', 		$channel->statistics->videoCount );
								update_post_meta( $post_id, 'wpcf-channel_description', $channel->snippet->description );
								
								$updated++;							
							endif;
						endforeach; //Endforeach
					endif; //Endif
				endforeach;				
			endif; //Endif
			$response['left_update'] = ( $total_to_update - $updated );
			$response['updated'] = $updated;
			$response['success'] = 1;
			wp_send_json( $response );
			wp_die();
		endif; //Endif
	}
	
}
//Run Class
$ytc_admin = new YTC_Admin();
endif;