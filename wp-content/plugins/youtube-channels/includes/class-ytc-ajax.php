<?php
/**
 * AJAX Callbacks
 *
 * Handles all ajax callbacks requests
 *
 * @since YouTube Channels 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Shortcodes') ) :

class YTC_Ajax_Callbacks{

	//Construct which run class
	function __construct(){
		//Searched Channels
		add_action( 'wp_ajax_ytc_search_channels', 			array( $this, 'ytc_search_channels' ) );
		add_action( 'wp_ajax_nopriv_ytc_search_channels', 	array( $this, 'ytc_search_channels' ) );
		//Searched Channels
		add_action( 'wp_ajax_ytc_get_channels', 			array( $this, 'ytc_get_channels' ) );
		add_action( 'wp_ajax_nopriv_ytc_get_channels', 		array( $this, 'ytc_get_channels' ) );
		//Load more Channels
		add_action( 'wp_ajax_ytc_loadmore_channels', 		array( $this, 'ytc_loadmore_channels' ) );
		add_action( 'wp_ajax_nopriv_ytc_loadmore_channels', array( $this, 'ytc_loadmore_channels' ) );
		//Autocomplete Channels
		add_action( 'wp_ajax_ytc_search_autocomplete',			array( $this, 'ytc_search_autocomplete' ) );
		add_action( 'wp_ajax_nopriv_ytc_search_autocomplete',	array( $this, 'ytc_search_autocomplete' ) );
		//Get Individual Channel
		add_action( 'wp_ajax_ytc_get_channel',			array( $this, 'ytc_get_particual_channel' ) );
		add_action( 'wp_ajax_nopriv_ytc_get_channel',	array( $this, 'ytc_get_particual_channel' ) );
		//Add New Channel
		add_action( 'wp_ajax_ytc_add_channel',			array( $this, 'ytc_add_channel' ) );
		add_action( 'wp_ajax_nopriv_ytc_add_channel',	array( $this, 'ytc_add_channel' ) );
	}
	
	/**
	 * Get Channels Data
	 **/
	public function ytc_get_channels(){
		
		//Search
		if( isset( $_POST['term'] ) && !empty( $_POST['term'] ) ) :
			ytc_get_channels_list( array( 'search' => $_POST['term'] ) );
		elseif( isset( $_POST['channelid'] ) && !empty( $_POST['channelid'] ) ) : //Choose From Autocomplete
			ytc_get_channels_list( array( 'channelid' => $_POST['channelid'] ) );
		endif; //Endif		
		wp_die(); //To Through Proper Result
	}

	/**
	 * Get Search Channels Data
	 **/
	public function ytc_search_channels(){

		$response = array();
		$query_args = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 
						'orderby' => 'meta_value_num', 'order' => $_POST['order'], 'paged' => 1, 'posts_per_page' => 40 );

		//Orderby
		if( isset( $_POST['sort'] ) && !empty( $_POST['sort'] ) ) :
			$query_args['meta_key'] = 'wpcf-channel_' . $_POST['sort'];
		else :
			$query_args['meta_key'] = 'wpcf-channel_subscribers';
		endif;
		//Check Search
		if( isset( $_POST['search'] ) && !empty( $_POST['search'] ) ) :
			$query_args['s'] = $_POST['search'];
		elseif( isset( $_POST['channelid'] ) && !empty( $_POST['channelid'] ) ) :
			$query_args['meta_query'] = array( array( 'key' => 'wpcf-channel_id', 'value' => esc_sql( $_POST['channelid'] ) ) );
		endif;

		ob_start(); //Start Output
		
		//Get Channels
		$channels = new WP_Query( $query_args );
		
		if( $channels->have_posts() ) :
	
			while( $channels->have_posts() ) : $channels->the_post();
	
				ytc_get_channel_loop(); //Channel Loop
	
			endwhile; //Endwhile
	
		endif;
		
		$response['html'] = ob_get_contents();
		ob_get_clean();
		$response['html'] .= '<div class="sfc-campaign-archive-post"></div><div class="sfc-campaign-archive-post"></div><div class="sfc-campaign-archive-post"></div>';				
		$response['found_posts'] = $channels->found_posts;
		
		//Reset Post Data
		wp_reset_postdata();
		wp_send_json( $response );	
		wp_die(); //To Through Proper Result
	}

	/**
	 * Load More Channels
	 **/
	public function ytc_loadmore_channels(){
		
		$response = array();
		$paged = isset( $_POST['paged'] ) && !empty( $_POST['paged'] ) ? ( intval( $_POST['paged'] ) + 1 ) : 1;
		$query_args = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 'posts_per_page' => 40,
						'orderby' => 'meta_value_num', 'order' => $_POST['orderby'], 'paged' => $paged  );
		//Orderby
		if( isset( $_POST['orderby'] ) && !empty( $_POST['orderby'] ) ) {
			$query_args['meta_key'] = 'wpcf-channel_' . $_POST['orderby'];
		} else {
			$query_args['meta_key'] = 'wpcf-channel_subscribers';
		}
		if( isset( $_POST['search'] ) && !empty( $_POST['search'] ) ) {
			$query_args['s'] = $_POST['search'];
		}

		ob_start(); //Start Output

		//Get Channels
		$channels = new WP_Query( $query_args );
	
		if( $channels->have_posts() ) :
	
			while( $channels->have_posts() ) : $channels->the_post();
	
				ytc_get_channel_loop(); //Channel Loop
	
			endwhile; //Endwhile
	
		endif; //Endif
		
		$response['html'] = ob_get_contents();
		ob_get_clean();
		$response['more_page'] = ( $channels->max_num_pages > $paged ) ? 1 : 0; //More Pages
		$response['paged'] = $paged;
		
		//Reset Post Data
		wp_reset_postdata();
		wp_send_json( $response );	
		wp_die(); //To Through Proper Result
	}

	/**
	 * Get Specific Channel Data form ID
	 **/
	public function ytc_get_particual_channel(){

		if( isset( $_POST['id'] ) && !empty( $_POST['id'] ) ) : //Check Channel ID
			$channelid 	= get_post_meta( $_POST['id'], 'wpcf-channel_id', true);
			$posturl 	= "https://www.googleapis.com/youtube/v3/search?key=".YTC_YOUTUBE_KEY."&channelId=$channelid&part=snippet,id&type=video&order=date&maxResults=5";
			$data 		= file_get_contents( $posturl, false );
			$response 	= json_decode( $data );
			$html 	= '';
			foreach( $response->items as $item ){
				$html .= '<div class="youtubes" data-embed="'.$item->id->videoId.'">
								<div class="play-button"></div>
								<img class="thumbs" src="https://img.youtube.com/vi/'.$item->id->videoId.'/sddefault.jpg">
							</div>';
			}
			echo $html;
		endif;
		wp_die(); //To Through Proper Result
	}

	/**
	 * Add New Channel
	 **/
	public function ytc_add_channel(){

		global $wpdb;

		if( isset( $_POST['channel_id'] ) && !empty( $_POST['channel_id'] ) ) {
        	$channel_id = $wpdb->_real_escape( $_POST['channel_id'] );
			if( empty( $channel_id ) ){
				echo "<div class='alert alert-danger'><strong>Error!</strong> Please enter channelid!!</div>";
			} elseif( !empty( $channel_id ) ){
				if( ytc_channel_exists( $channel_id ) ){
					echo "<div class='alert alert-danger'><strong>Error!</strong> Channel already exists!!</div>";
				} else {
					$posturl 	= 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id='.$channel_id.'&key='.YTC_YOUTUBE_KEY;
					$data 		= file_get_contents( $posturl, false);
					$response 	= json_decode( $data );
					if( !isset( $response->items[0]->snippet->title ) ){
						echo "<div class='alert alert-danger'><strong>Error!</strong> Wrong Channel Id!!</div>";
					} else {
						//Insert Channel Data
						$insert_data = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish',
											'post_title' 	=> ( isset( $response->items[0]->snippet->title ) ? $response->items[0]->snippet->title : '' ),
											'post_content' 	=> ( isset( $response->items[0]->snippet->description ) ? $response->items[0]->snippet->description : '' ) );
						$inserted_id = wp_insert_post( $insert_data );
						if( !empty( $inserted_id ) ){
							update_post_meta( $inserted_id, 'wpcf-channel_id', $channel_id );
							update_post_meta( $inserted_id, 'wpcf-channel_img', 		( isset( $response->items[0]->snippet->thumbnails->medium->url ) ? $response->items[0]->snippet->thumbnails->medium->url : '' ) );
							update_post_meta( $inserted_id, 'wpcf-channel_country', 	( isset( $response->items[0]->snippet->country ) 			? $response->items[0]->snippet->country : 'N/A' ) );
							update_post_meta( $inserted_id, 'wpcf-channel_subscribers', ( isset( $response->items[0]->statistics->subscriberCount ) ? $response->items[0]->statistics->subscriberCount : 0 ) );
							update_post_meta( $inserted_id, 'wpcf-channel_views', 		( isset( $response->items[0]->statistics->viewCount )		? $response->items[0]->statistics->viewCount : 0 ) );
							update_post_meta( $inserted_id, 'wpcf-channel_videos', 		( isset( $response->items[0]->statistics->videoCount )		? $response->items[0]->statistics->videoCount : 0 ) );
							update_post_meta( $inserted_id, 'wpcf-channel_keywords',	( isset( $response->items[0]->brandingSettings->channel->keywords )		? $response->items[0]->brandingSettings->channel->keywords : '' ) );
							update_post_meta( $inserted_id, 'wpcf-channel_web',			( isset( $response->items[0]->invideoPromotion->items->id->websiteUrl )	? $response->items[0]->invideoPromotion->items->id->websiteUrl : '' ) );
							echo "<div class='alert alert-success'><strong>Success!</strong> Channel Added Successfuly!!</div>";
						} else {
							echo "<div class='alert alert-danger'><strong>Error!</strong> Something went wrong try again!!</div>";
						}
					}
				}
			}
		} else {
			echo "<div class='alert alert-danger'><strong>Error!</strong> Please enter channelid!!</div>";
		}
		wp_die(); //To Through Proper Result
	}

	/**
	* Autocomplete Search Channels
	**/
	public function ytc_search_autocomplete(){

		//Check Search Not Empty
		if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ){
			$return_arr = array();
			$channels = get_posts( array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 'posts_per_page' => 10, 's' => $_GET['term'] ) );
			if( !empty( $channels ) ){
				foreach( $channels as $channel ) :
					$channel_id = get_post_meta( $channel->ID, 'wpcf-channel_id', true ); 		//Channel ID
					$return_arr[] = array( 'value' => $channel->post_title, 'channelid' => $channel_id );
				endforeach; //Endforeach
			}
			//Send Response
			wp_send_json( $return_arr );
		}
		wp_die(); //To Through Proper Result
	}
}
//Run Class
$ytc_ajax = new YTC_Ajax_Callbacks();
endif;
