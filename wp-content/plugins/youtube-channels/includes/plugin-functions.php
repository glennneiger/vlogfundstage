<?php
/**
 * Plugin Functions
 *
 * Handles all plugin common functions
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !function_exists('ytc_number_abbs') ) :
/**
 * Create Number with Short Abberivation
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_number_abbs( $num ){

	if( $num > 1000 ){
		$x = round($num);
		$x_number_format = number_format($x);
		$x_array = explode(',', $x_number_format);
		$x_parts = array('K', 'M', 'B', 'T');
		$x_count_parts = count($x_array) - 1;
		$x_display = $x;
		$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		$x_display .= $x_parts[$x_count_parts - 1];
		return $x_display;
	}
	return $num;
}
endif;
if( !function_exists('ytc_get_short_desc') ) :
/**
 * Filter Description
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_get_short_desc( $string ) {
	if( !empty( $string ) ){
		if( strlen( $string ) > 300 ){
			$string = substr($string, 0, 300).'....';
			$string = str_replace(array('\'', '\"'), array('',''), $string);
		}
		return $string = str_replace(array('\'', '\"'), array('',''), $string);	
	} else {
		return 'N/A';
	}
}
endif;
if( !function_exists('ytc_get_channel_short_title') ) :
/**
 * Make Title Short
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_get_channel_short_title( $string ){
	return strlen( $string ) > 14 ? substr( $string, 0, 14 ) . '...' : $string;
}
endif;
if( !function_exists('ytc_get_channels_count') ) :
/**
 * Get Channels Count
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_get_channels_count( $args = array() ){
	
	$query_args = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' );
	if( isset( $args['search'] ) && !empty( $args['search'] ) ) {
		$query_args['s'] = $args['search'];
	}
	$channels = new WP_Query( $query_args );
	$channels_count = $channels->found_posts;
	wp_reset_postdata();
	return $channels_count;	
}
endif;
if( !function_exists('ytc_channel_exists') ) :
/**
 * Check Channel Exists
 *
 * Handles to check channel is already exists or not
 *
 * @since YouTube Channels 1.0
 **/
function ytc_channel_exists( $id ) {
	$channels = get_posts( array( 'post_type' => 'youtube_channels', 'post_status' => 'any', 'posts_per_page' => -1, 'meta_key' => 'wpcf-channel_id', 'meta_value' => $id ) );
	return ( !empty( $channels ) && ( count( $channels ) > 0 ) ? true : false );
}
endif;
if( !function_exists('ytc_get_channels_list') ) :
/**
 * Get YouTube Channels Data
 *
 * Handles to get youtube channels data with HTML
 *
 * @since YouTube Channels 1.0
 **/
function ytc_get_channels_list( $args = array() ){
	
	$query_args = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 'orderby' => 'meta_value' );
	
	if( isset( $args['search'] ) && !empty( $args['search'] ) ) {
		$query_args['s'] = $args['search'];
	} elseif( isset( $args['channelid'] ) && !empty( $args['channelid'] ) ) { //Specific Channel
		$query_args['meta_query'] = array( array( 'key' => 'wpcf-channel_id', 'value' => esc_sql( $args['channelid'] ) ) );
	}
	
	//Offset
	if( isset( $args['offset'] ) && !empty( $args['offset'] ) ) {
		$query_args['offset'] = $args['offset'];
	}
	
	//Limit
	if( isset( $args['limit'] ) && !empty( $args['limit'] ) ) {
		$query_args['posts_per_page'] = $args['limit'];
	} else {
		$query_args['posts_per_page'] = 40;
	}
	
	//Order
	if( isset( $args['order'] ) && !empty( $args['order'] ) ) {
		$query_args['order'] = $args['order'];						
	} else {
		$query_args['order'] = 'ASC';
	}
	
	//Orderby
	if( isset( $args['orderby'] ) && !empty( $args['orderby'] ) ) {
		$query_args['meta_key'] = 'wpcf-channel_' . $args['orderby'];
	} else {
		$query_args['meta_key'] = 'wpcf-channel_subscribers';
	}
		
	//Get Channels
	$channels = new WP_Query( $query_args );
	
	if( $channels->have_posts() ) :
		
		while( $channels->have_posts() ) : $channels->the_post();
		
			ytc_get_channel_loop(); //Channel Loop
			
		endwhile; //Endwhile

	endif;

	//Reset Post Data
	wp_reset_postdata();
	
}
endif;
if( !function_exists('ytc_get_channel_loop') ) :
/**
 * Get Channel Loop Block
 *
 * Handles to show channel loop block
 *
 * @since YouTube Channels 1.0
 **/
function ytc_get_channel_loop( $post_id = 0 ) {
	
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$channel_id 		= get_post_meta( $post_id, 'wpcf-channel_id', true ); 		//Channel ID
	$channel_views 		= get_post_meta( $post_id, 'wpcf-channel_views', true ) 		? get_post_meta( $post_id, 'wpcf-channel_views', true ) : 'N/A'; 	//Views								
	$channel_subscribers= get_post_meta( $post_id, 'wpcf-channel_subscribers', true ) 	? get_post_meta( $post_id, 'wpcf-channel_subscribers', true ) : 'N/A' ; 	//Subscribers
	$channel_keywords 	= get_post_meta( $post_id, 'wpcf-channel_keywords', true ); //Keywords
	$channel_img 		= get_post_meta( $post_id, 'wpcf-channel_img', true ) ? get_post_meta( $post_id, 'wpcf-channel_img', true ) : YTC_PLUGIN_URL . 'assets/images/default.png'; 		//Image 
	$channel_gplus 		= get_post_meta( $post_id, 'wpcf-channel_gplus', true );	//Google+
	$channel_tw 		= get_post_meta( $post_id, 'wpcf-channel_tw', true ); 		//Twitter
	$channel_insta		= get_post_meta( $post_id, 'wpcf-channel_insta', true ); 	//Instagram
	$channel_fb 		= get_post_meta( $post_id, 'wpcf-channel_fb', true ); 		//Facebook
	$channel_web 		= get_post_meta( $post_id, 'wpcf-channel_web', true ); 		//Website
	$channel_snap 		= get_post_meta( $post_id, 'wpcf-channel_snap', true ); 	//Snapchat
	$channel_vk 		= get_post_meta( $post_id, 'wpcf-channel_vk', true ); 		//VK ?>
	
	<div class="col-lg-3 col-sm-6" id="<?php echo $channel_id; ?>">
		<div class="box grid recipes">
			<div class="by"><i class="fa fa-eye" aria-hidden="true"></i>
				<span id="<?php echo $channel_id; ?>-views" title="Total Views Count" style="color:white" class="details">
					<?php echo ytc_number_abbs( $channel_views ); ?>
				</span> <span id="<?php echo $channel_id; ?>-subs" title="Subscribers Count" class="fa-pull-right details"><i class="fa fa-users" aria-hidden="true"></i> <?php echo ( $channel_subscribers == 0 ) ? 'N/A' : ytc_number_abbs( $channel_subscribers ); ?></span>
			</div><!--/.by-->
			<a href="https://www.youtube.com/channel/<?php echo $channel_id; ?>" class="showinfoimg"><img class="b-lazy" id="<?php echo $channel_id; ?>-img" src="https://discoverbrands.co/public/img/loader.gif" data-src="<?php echo $channel_img; ?>" alt=""></a>
			<h2><a href="https://www.youtube.com/channel/<?php echo $channel_id; ?>" class="showinfo" data-gplus="<?php echo $channel_gplus; ?>" data-twitter="<?php echo $channel_tw; ?>" data-instagram="<?php echo $channel_insta; ?>" data-facebook="<?php echo $channel_fb; ?>" data-website="<?php echo $channel_web; ?>" data-snapchat="<?php echo $channel_snap; ?>" data-vk="<?php echo $channel_vk; ?>" data-channelid="<?php the_ID(); ?>" data-title="<?php echo get_the_title(); ?>" target="_blank">
				<?php echo ytc_get_channel_short_title( get_the_title() ); ?>
			</a></h2>
			<p><span class="details" title='<p><span style="text-decoration:underline">Channel Title:</span><br><?php echo ytc_get_short_desc( get_the_title() );  ?></p><p><span style="text-decoration:underline">Description:</span><br><?php echo ytc_get_short_desc( get_the_content() ); ?></p><p><span style="text-decoration:underline">Country:</span><br><?php echo ytc_get_short_desc( $channel_country ); ?></p><p><span style="text-decoration:underline">Keywords:</span><br><?php echo ytc_get_short_desc( $channel_keywords ); ?></p>'> <i class="fa fa-info-circle" style="color:#e13b2b;cursor:pointer"></i> More</span> - <span class="details" title="Total Videos Count"><i class="fa fa-video" style="color:#e13b2b"></i> <b><?php echo $channel_videos; ?></b></span></p><br>
		</div><!--/.box-->
	</div><!--/.col-lg-3-->
	
<?php }
endif;
if( !function_exists('ytc_cron_schedules') ) :
/**
 * Cron Schedule
 *
 * Handles to cron schedule
 *
 * @since YouTube Channels 1.0
 **/
function ytc_cron_schedules($schedules){
    if( !isset( $schedules['ytc_2min'] ) ):
        $schedules['ytc_2min'] = array(
            'interval' 	=> 2 * 60, //Every 2 minutes
            'display'	=> __('Once every 2 minutes')
		);		
    endif; //Endif
	if( !isset( $schedules['ytc_1min'] ) ):
		$schedules['ytc_1min'] = array(
            'interval' 	=> 60, //Every minutes
            'display'	=> __('Every minutes')
		);
	endif; //Endif
	return $schedules;
}
add_filter('cron_schedules','ytc_cron_schedules');
endif;
if( !function_exists('ytc_register_schedule_events') ) :
/**
 * Register Schedule Event
 *
 * Handles to register schedule events for cron job
 * 
 * @since YouTube Channels
 **/
function ytc_register_schedule_events(){
	//Daily Update the YouTube Channels	
	/*if( !wp_next_scheduled( 'ytc_daily_update_channels' ) ) :
		$ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
		//wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'ytc_2min', 'ytc_daily_update_channels' );
		//wp_schedule_event( time(), 'ytc_2min', 'ytc_daily_update_channels' );
		wp_schedule_event( time(), 'ytc_1min', 'ytc_daily_update_channels' );
    endif;*/
}
//add_action('init', 'ytc_register_schedule_events');
endif;

if( !function_exists('ytc_daily_update_channels_data') ) :
/**
 * Update Chanels Daily
 * 
 * Handles to update channels daily
 *
 * @since YouTube Channels 1.0
 **/
function ytc_daily_update_channels_data() {
	
	global $wpdb;
	$youtube_keys = array('AIzaSyDRp8PJ-exVLhq2hrELXXh3ukgmCxpXQqE', 'AIzaSyA8zsv8cUPn5RFl-FPQDzt98_YVoetvzpM', 'AIzaSyDNWCjklIla_ozAj4GeZ7N3RI_ZTeiwjks');	
	/*$query = "SELECT m1.meta_value FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta AS m1 ON (m1.post_id = $wpdb->posts.ID)
			WHERE 1=1 AND $wpdb->posts.post_type = 'youtube_channels'
			AND m1.meta_key = 'wpcf-channel_id' ORDER BY $wpdb->posts.post_modified ASC;";*/
	$query = "SELECT m1.meta_value FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta AS m1 ON (m1.post_id = $wpdb->posts.ID)
			WHERE 1=1 AND $wpdb->posts.post_type = 'youtube_channels'
			AND m1.meta_key = 'wpcf-channel_id'
			AND $wpdb->posts.post_modified < DATE_SUB( NOW(), INTERVAL 96 HOUR ) ORDER BY $wpdb->posts.post_modified ASC LIMIT 150;";
	$all_channels = $wpdb->get_col( $query );
	$updated = $not_updated = array();
	if( !empty( $all_channels ) ) : //Check Channel Data
		$channel_slots = array_chunk( $all_channels, 15 );
		$counter = 1;
		foreach( $channel_slots as $channels_list ) :			
			$channel_ids = implode(',', $channels_list );
			$yt_rand_key = array_rand( $youtube_keys );
			$use_yt_key = $youtube_keys[$yt_rand_key]; //YTC_YOUTUBE_KEY; //
			$channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=topicDetails,status,brandingSettings,contentDetails,contentOwnerDetails,localizations,snippet,statistics,topicDetails&key='.$use_yt_key.'&id='.$channel_ids;
			$channel_data = file_get_contents( $channel_url, false);
			$channel_results = json_decode( $channel_data );
			if( !empty( $channel_results->items ) ) : //Update the Channel Data
				foreach( $channel_results->items as $channel ) :
					$post_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 AND meta_key = 'wpcf-channel_id' AND meta_value = '".$channel->id."';" );
					if( !empty( $post_id ) ) :
						$updated_post = wp_update_post( array(
							'ID' => $post_id,
							'post_title'   => $channel->snippet->title,
							'post_content' => $channel->snippet->description,
						) );						
						 //Update Related Details
						update_post_meta( $post_id, 'wpcf-channel_views', 		$channel->statistics->viewCount );
						update_post_meta( $post_id, 'wpcf-channel_subscribers', $channel->statistics->subscriberCount );
						update_post_meta( $post_id, 'wpcf-channel_keywords', 	$channel->brandingSettings->keywords );
						update_post_meta( $post_id, 'wpcf-channel_img', 		$channel->snippet->thumbnails->medium->url );
						update_post_meta( $post_id, 'wpcf-channel_videos', 		$channel->statistics->videoCount );
						$counter++;
					endif;					
				endforeach; //Endforeach
			endif; //Endif			
		endforeach;
	endif; //Endif
	/*ob_start();
	echo '<pre>';
	//print_r($all_channels);
	//print_r($updated);
	print_r($not_updated);
	echo '</pre>';
	$content = ob_get_contents();
	ob_get_clean();
	wp_mail('wptestworld@gmail.com', 'Cron Updated Records - ' . date('Y-m-d H:i:s') . ' - ' . $counter, 'Cron Updated Records - ' . $counter . "\n\r\n\r" . $content);*/
}
add_action('ytc_daily_update_channels', 'ytc_daily_update_channels_data');
//add_action('init', 'ytc_daily_update_channels_data');
endif;
/*function just_check_query_time(){
	global $wpdb;
	echo $query = "SELECT m1.meta_value FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta AS m1 ON (m1.post_id = $wpdb->posts.ID)
			WHERE 1=1 AND $wpdb->posts.post_type = 'youtube_channels'
			AND m1.meta_key = 'wpcf-channel_id'
			AND $wpdb->posts.post_modified < ( now() - INTERVAL 1 DAY ) ORDER BY $wpdb->posts.post_modified ASC LIMIT 160;";
	$all_channels = $wpdb->get_col( $query );
	$channel_slots = array_chunk( $all_channels, 40 );
	$counter = 0;
	$updated = array();
	foreach( $channel_slots as $channels_list ) :
		$channel_ids = implode(',', $channels_list );
		$channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=topicDetails,status,brandingSettings,contentDetails,contentOwnerDetails,localizations,snippet,statistics,topicDetails&id='.$channel_ids.'&key='.YTC_YOUTUBE_KEY;
		$channel_data = file_get_contents( $channel_url, false);
		$channel_results = json_decode( $channel_data );
		if( !empty( $channel_results->items ) ) : //Update the Channel Data
			foreach( $channel_results->items as $channel ) :
				$post_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 AND meta_key = 'wpcf-channel_id' AND meta_value='".$channel->id."';" );
				if( !empty( $post_id ) ) :
					$updated_post = wp_update_post( array(
						'ID' => $post_id,
						'post_title'   => $channel->snippet->title,
						'post_content' => $channel->snippet->description,
					) );						
					 //Update Related Details
					update_post_meta( $post_id, 'wpcf-channel_views', 		$channel->statistics->viewCount );
					update_post_meta( $post_id, 'wpcf-channel_subscribers', $channel->statistics->subscriberCount );
					update_post_meta( $post_id, 'wpcf-channel_keywords', 	$channel->brandingSettings->keywords );
					update_post_meta( $post_id, 'wpcf-channel_img', 		$channel->snippet->thumbnails->medium->url );
					update_post_meta( $post_id, 'wpcf-channel_videos', 		$channel->statistics->videoCount );					
				endif;				
				$counter++;
			endforeach; //Endforeach
		endif; //Endif		
	endforeach;	
}
add_action('wp', 'just_check_query_time');*/