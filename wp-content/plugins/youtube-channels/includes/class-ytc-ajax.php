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
		add_action( 'wp_ajax_ytc_get_channels', 		array( $this, 'ytc_get_channels' ) );
		add_action( 'wp_ajax_nopriv_ytc_get_channels', 	array( $this, 'ytc_get_channels' ) );
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
		elseif( isset( $_POST['normal'] ) && !empty( $_POST['normal'] ) ) : //Normal Channels
			$args = array( 'orderby' => $_POST['sortby'], 'order' => $_POST['orderby'], 'limit' => 40, 'offset' => $_POST['offset'] );
			//Get Channels
			ytc_get_channels_list( $args );
		endif; //Endif		
		wp_die(); //To Through Proper Result
	}
	
	
	/**
	 * Get Specific Channel Data form ID
	 **/
	public function ytc_get_particual_channel(){

		if( isset( $_POST['id'] ) && !empty( $_POST['id'] ) ) : //Check Channel ID
			$channelid 	= $_POST['id'];
			$posturl 	= "https://www.googleapis.com/youtube/v3/search?key=".YTC_YOUTUBE_KEY."&channelId=$channelid&part=snippet,id&type=video&order=date&maxResults=5";
			$data 		= file_get_contents( $posturl, false);
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
		$table_name = $wpdb->prefix . 'yt_channels';
		
		if( isset( $_POST['id'] ) && !empty( $_POST['id'] ) ) {			
        	$id = $wpdb->_real_escape( $_POST['id'] );			
			if( empty( $id ) ){
				echo "<div class='alert alert-danger'><strong>Error!</strong> Please enter channelid!!</div>";
			} elseif( !empty( $id ) ){				
				if( ytc_channel_exists( $id ) ){
					echo "<div class='alert alert-danger'><strong>Error!</strong> Channel already exists!!</div>";
				} else {
					$posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id='.$id.'&key='.YTC_YOUTUBE_KEY;
					$data = file_get_contents( $posturl, false);
					$response = json_decode($data);
					if( !isset( $response->items[0]->snippet->title ) ){
						echo "<div class='alert alert-danger'><strong>Error!</strong> Wrong Channel Id!!</div>";
					} else {
						$insert_data = array();
						$insert_data['channelid'] = $id;
						$insert_data['name'] 		= isset( $response->items[0]->snippet->title )		? $response->items[0]->snippet->title : '';
						$insert_data['country'] 	= isset( $response->items[0]->snippet->country ) 	? $response->items[0]->snippet->country : 'N/A';
						$insert_data['subscribers'] = isset( $response->items[0]->statistics->subscriberCount ) ? $response->items[0]->statistics->subscriberCount : 0;
						$insert_data['views']		= isset( $response->items[0]->statistics->viewCount )		? $response->items[0]->statistics->viewCount : 0;
						//$insert_data['logo']		= isset( $response->items[0]->snippet->thumbnails->high->url ) ? $response->items[0]->snippet->thumbnails->high->url : '';						
						$wpdb->insert( $table_name, $insert_data );
						if( $wpdb->insert_id ){
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
		
		global $wpdb;
		
		//Check Search Not Empty
		if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ){
			$return_arr = array();
			$table_name = $wpdb->prefix . 'yt_channels';
			$results = $wpdb->get_results( "SELECT name,channelid FROM $table_name WHERE 1=1 AND name LIKE '%".esc_sql( $_GET['term'] )."%' LIMIT 10", ARRAY_A );
			if( !empty( $results ) ){
				foreach( $results as $row ) :
					$return_arr[] = array( 'value' => $row['name'], 'channelid' => $row['channelid'] );
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