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
		$ytc_channels = get_posts(array( 'post_type' => 'youtube_channels', 'post_status' => 'any', 'posts_per_page' => -1,
									'meta_query' => array( array( 'key' => 'wpcf-channel_id', 'value' => '', 'compare' => '!=' ) ),
									'fields' => 'ids',
									'date_query' => array( 
											array( 'column' => 'post_modified', 
												'before' => array( 	
													'year' 	=> date('Y', strtotime( $date_before ) ), 
													'month' => date('m', strtotime( $date_before ) ), 
													'day' 	=> date('d', strtotime( $date_before ) ) 
												) 
									) ) ) );		
		$total_to_update = !empty( $ytc_channels ) ? count($ytc_channels) : 0;
		echo '<style type="text/css">';
		echo '.update-channel-progress-wrap{ width: 100%; background-color: #d3d3d3; margin-bottom:20px; display:none; }';
		echo '.update-channel-progress{ width:0%; height: 30px; background-color: green; }';
		echo '.result-count{ margin-bottom:20px; display:none; }';
		echo '</style>';
		echo '<div class="wrap">';
		echo '<h2>'.__('Update Channels','youtube-channels').'</h2><br>';
		if( !empty( $ytc_channels ) ) : //Check Channels to Updated 
			echo '<p><strong>'.__('Update may take some time. Please do not close your browser or refresh the page until the process is complete.', 'youtube-channels').'</strong></p>';			
			echo '<div class="update-channel-progress-wrap"><div class="update-channel-progress"></div></div>';
			echo '<div class="result-count">Updated <span class="updated">0</span> out of <span class="total">'.$total_to_update.'</span></div>';
			echo '<a href="#" class="button button-primary update-channels-btn">'.__('Update','youtube-channels').'</a>';
			echo '<p>Records will be updated which are updated on or before '.date('Y-m-d', strtotime('-4days')).'</p>';
			echo '<div class="update-channel-results"></div>';		
			echo '</div>';
		else : 
			echo '<p>'.__('All records are updated, no records to update as of now', 'youtube-channels').'</p>';
		endif; //Endif
		wp_reset_postdata(); //Reset Postdata
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
			$date_before 	= date('Y-m-d', strtotime('-4days'));
			$total_to_update= ( isset( $_POST['total'] ) && !empty( $_POST['total'] ) ) 	? intval( $_POST['total'] ) : 0;
			$paged 			= ( isset( $_POST['paged'] ) && !empty( $_POST['paged'] ) ) 	? $_POST['paged'] : 1;
			$updated 		= ( isset( $_POST['updated'] ) && !empty( $_POST['updated'] ) ) ? intval( $_POST['updated'] ) : 0;
			$response 		= array('updated' => 0);
			$ytc_args 		= array( 'post_type' => 'youtube_channels', 'post_status' => 'any', 'posts_per_page' => 40,
								'paged' => $paged, 'fields' => 'ids',
								'meta_query' => array( array( 'key' => 'wpcf-channel_id', 'value' => '', 'compare' => '!=' ) ),								
								'date_query' => array(
										array( 'column' => 'post_modified', 
											'before' => array( 	
												'year' => date('Y', strtotime( $date_before ) ), 
												'month' => date('m', strtotime( $date_before ) ), 
												'day' => date('d', strtotime( $date_before ) ) 
											) 
								) ) );
			$ytc_channels 	= get_posts( $ytc_args );
			$updated_channels = array();
			if( !empty( $ytc_channels ) ) : //Check Have Post
				$channels_list = array();
				foreach( $ytc_channels as $channel ) : //Loop to Collect Channels ID
					if( $channel_id = get_post_meta($channel, 'wpcf-channel_id', true) ) :
						$channels_list[$channel] = $channel_id;
					endif; //Endif
				endforeach;
				$channel_ids 	= implode(',', $channels_list );
				$use_yt_key 	= ytc_youtube_api_key(); //YTC_YOUTUBE_KEY; //
				$channel_url 	= 'https://www.googleapis.com/youtube/v3/channels?part=topicDetails,status,brandingSettings,contentDetails,contentOwnerDetails,localizations,snippet,statistics&key='.$use_yt_key.'&id='.$channel_ids;
				$channel_data 	= file_get_contents( $channel_url, false);
				$channel_results= json_decode( $channel_data );				
				if( !empty( $channel_results->items ) ) : //Update the Channel Data					
					foreach( $channel_results->items as $channel ) :
						$post_id = array_search($channel->id, $channels_list);						
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
							array_push($updated_channels, $post_id); //Add Updated Channel							
						endif;
					endforeach; //Endforeach
				endif; //Endif
				$not_updated = array_diff($ytc_channels, $updated_channels);
				if( !empty( $not_updated ) ) : //Check Not Updated
					foreach( $not_updated as $channel ) :
						$updated_post = wp_update_post( array('ID' => $channel ) );						
						$updated++;
					endforeach; //Endforeach
				endif; //Endif
			endif; //Endif			
			$response['left_update'] = ( intval( $total_to_update ) - $updated );
			$response['updated']= $updated;
			$response['success']= 1;
			$response['paged'] 	= ( $paged + 1 );
			//$response['not_updated'] = $not_updated;
			wp_send_json( $response );
			wp_die();
		endif; //Endif
	}
	
}
//Run Class
$ytc_admin = new YTC_Admin();
endif;