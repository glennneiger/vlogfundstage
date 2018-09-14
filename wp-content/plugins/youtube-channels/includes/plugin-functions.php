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
if( !function_exists('ytc_update_subscribers') ) :
/**
 * Update Subscribers of Channels
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_update_subscribers($channelid, $subs){
	global $wpdb;
	$wpdb->update( $wpdb->prefix . 'yt_channels', array( 'subscribers' => $subs ), array( 'channelid' => $channelid ) );
}
endif;
if( !function_exists('ytc_update_views') ) :
/**
 * Update Views of Channels
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_update_views($channelid, $views){
	global $wpdb;
	$wpdb->update( $wpdb->prefix . 'yt_channels', array( 'views' => $views ), array( 'channelid' => $channelid ) );    
}
endif;
if( !function_exists('ytc_get_channel_socials') ) :
/**
 * Get Socials of Channel
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_get_channel_socials($channelid){
	global $wpdb;
	$socials = $wpdb->get_row( $wpdb->prepare( "SELECT instagram,twitter,facebook,website,gplus,snapchat,vk FROM ".$wpdb->prefix . "yt_channels WHERE 1=1 AND channelid='%s'", esc_sql( $channelid ) ), ARRAY_A );
	return $socials;
}
endif;
if( !function_exists('ytc_get_channels_count') ) :
/**
 * Get Channels Count
 * 
 * @since YouTube Channels 1.0
 **/
function ytc_get_channels_count( $args = array() ){
	
	global $wpdb;
	$query = 'SELECT COUNT(*) FROM '.$wpdb->prefix . 'yt_channels'.' WHERE 1=1';
	if( isset( $args['search'] ) ) {
		$query .= ' AND name LIKE "%'.esc_sql( $args['search'] ).'%"';
	}
	$channels_count = $wpdb->get_var( $query );
	return $channels_count;	
}
endif;
if( !function_exists('ytc_get_channel_ids') ) :
/**
 * Get YouTube Channel IDs
 *
 * Handles to get youtube channel IDs
 *
 * @since YouTube Channels 1.0
 **/
function ytc_get_channel_ids( $args = array() ) {
	
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'yt_channels';
	
	$offset = ( isset( $args['offset'] ) && !empty( $args['offset'] ) ) ? $args['offset'] : 0;
	$limit 	= ( isset( $args['limit'] ) && !empty( $args['limit'] ) ) 	? $args['limit'] : 40;	
	$order 	= ( isset( $args['order'] ) && !empty( $args['order'] ) ) 	? $args['order'] : 'desc';
	$orderby= ( isset( $args['orderby'] ) && !empty( $args['orderby'] ) ) ? $args['orderby'] : 'subscribers';
	
	$query = "SELECT channelid FROM $table_name WHERE 1=1";
	
	if( isset( $args['search'] ) && !empty( $args['search'] ) ) {
		$query .= " AND name LIKE '%".esc_sql( $args['search'] )."%'";
	} elseif( isset( $args['channelid'] ) && !empty( $args['channelid'] ) ) { //Specific Channel
		$query .= " AND channelid='".esc_sql( $args['channelid'] )."'";
	}
	
	//Orderby
	$query .= " ORDER BY ".$orderby." ".strtoupper( $order );
	//Limit and Offset
	$query .= " LIMIT $offset, $limit";
	
	//Get Channels
	$results = $wpdb->get_col( $query );
	
	return $results;
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
	global $wpdb;
	$table_name = $wpdb->prefix . 'yt_channels';
	$channel_exists = $wpdb->get_row( $wpdb->prepare( "SELECT channelid FROM $table_name WHERE 1=1 AND channelid='%s'", $id ) );
	return ( !empty( $channel_exists ) ? true : false );
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
	
	$ord = isset( $args['order'] ) ? $args['order'] : 'desc';
	$sort = isset( $args['orderby'] ) ? $args['orderby'] : 'subscribers';
		
	//Get Channels
	$results = ytc_get_channel_ids( $args );
	
	if( !empty( $results ) ){

		$ids = implode(',', $results);
		$posturl = 'https://www.googleapis.com/youtube/v3/channels?part=topicDetails,status,brandingSettings,contentDetails,contentOwnerDetails,localizations,snippet,statistics,topicDetails&order=viewCount&id='.$ids.'&key='.YTC_YOUTUBE_KEY;
		$data = file_get_contents( $posturl, false);
		$response = json_decode($data);		
		$i = 0;
		$channeldata = array();
		foreach( $response->items as $item ){
			$channeldata[$i]['id'] 			=	$item->id;
			$channeldata[$i]['title'] 		=	$item->snippet->title;
			$channeldata[$i]['subscribers'] =	$item->statistics->subscriberCount != 0 ? $item->statistics->subscriberCount: 0;
			$channeldata[$i]['views'] 		=	$item->statistics->viewCount;
			$channeldata[$i]['image'] 		=	$item->snippet->thumbnails->high->url;
			$channeldata[$i]['country'] 	=	isset( $item->snippet->country ) ? $item->snippet->country : 'N/A';
			$channeldata[$i]['description'] =	$item->snippet->description;
			$channeldata[$i]['videos'] 		=	$item->statistics->videoCount;
			$channeldata[$i]['keywords']	=	isset( $item->brandingSettings->channel->keywords ) ? $item->brandingSettings->channel->keywords : 'N/A';
			$socialmedia = ytc_get_channel_socials( $item->id );
			$channeldata[$i]['instagram']	=	$socialmedia['instagram'];
			$channeldata[$i]['twitter'] 	=	$socialmedia['twitter'];
			$channeldata[$i]['facebook']	=	$socialmedia['facebook'];
			$channeldata[$i]['website'] 	=	$socialmedia['website'];
			$channeldata[$i]['gplus'] 		=	$socialmedia['gplus'];
			$channeldata[$i]['snapchat']	=	$socialmedia['snapchat'];
			$channeldata[$i]['vk'] 			=	$socialmedia['vk'];
			ytc_update_subscribers( $item->id, $item->statistics->subscriberCount );
			ytc_update_views( $item->id, $item->statistics->viewCount );
			$i++;
		}
		
		if( $sort == 'views' ){
			if( $ord == 'asc' ){
				usort( $channeldata, function($item1, $item2) {
					return $item1['views'] <=> $item2['views'];
				});
			} else {
				usort($channeldata, function($item1, $item2) {
					return $item2['views'] <=> $item1['views'];
				});
			}
		} elseif( $sort == 'subscribers' ){
			if( $ord == 'asc' ){
				usort($channeldata, function($item1, $item2) {
					return $item1['subscribers'] <=> $item2['subscribers'];
				});
			}else{
				usort($channeldata, function($item1, $item2) {
					return $item2['subscribers'] <=> $item1['subscribers'];
				});
			}
		}		
		
		for( $i = 0; $i < count( $channeldata ); $i++ ) {
			//Get Channel Loop Block
			ytc_get_channel_loop( $channeldata[$i] );
		}
	} else { 
		echo '<div class="ytc-no-channels"><p>No channels found.</p></div>'; 
	}
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
function ytc_get_channel_loop( $channeldata ) { ?>
	
	<div class="col-lg-3 col-sm-6" id="<?php echo $channeldata['id']; ?>">
		<div class="box grid recipes">
			<div class="by"><i class="fa fa-eye" aria-hidden="true"></i>
				<span id="<?php echo $channeldata['id']; ?>-views" title="Total Views Count" style="color:white" class="details">
					<?php echo ytc_number_abbs( $channeldata['views'] ); ?>
				</span> <span id="<?php echo $channeldata['id']; ?>-subs" title="Subscribers Count" class="fa-pull-right details"><i class="fa fa-users" aria-hidden="true"></i> <?php echo ( $channeldata['subscribers'] == 0 ) ? 'N/A' : ytc_number_abbs( $channeldata['subscribers'] ); ?></span>
			</div><!--/.by-->
			<a href="#" class="showinfoimg"><img class="b-lazy" id="<?php echo $channeldata['id']; ?>-img" src="https://discoverbrands.co/public/img/loader.gif" data-src="<?php echo $channeldata['image'] ?>" alt=""></a>
			<h2><a class="showinfo" data-gplus="<?php echo $channeldata['gplus']; ?>" data-twitter="<?php echo $channeldata['twitter']; ?>" data-instagram="<?php echo $channeldata['instagram']; ?>" data-facebook="<?php echo $channeldata['facebook']; ?>" data-website="<?php echo $channeldata['website']; ?>" data-snapchat="<?php echo $channeldata['snapchat']; ?>" data-vk="<?php echo $channeldata['vk']; ?>" data-channelid="<?php echo $channeldata['id']; ?>" data-title="<?php echo $channeldata['title']; ?>" target="_blank" href="https://www.youtube.com/channel/<?php echo $channeldata['id']; ?>">
				<?php echo ytc_get_channel_short_title( $channeldata['title'] ); ?>
			</a></h2>
			<p><span class="details" title='<p><span style="text-decoration:underline">Channel Title:</span><br><?php echo ytc_get_short_desc( $channeldata['title'] );  ?></p><p><span style="text-decoration:underline">Description:</span><br><?php echo ytc_get_short_desc($channeldata['description']); ?></p><p><span style="text-decoration:underline">Country:</span><br><?php echo ytc_get_short_desc( $channeldata['country'] ); ?></p><p><span style="text-decoration:underline">Keywords:</span><br><?php echo ytc_get_short_desc( $channeldata['keywords'] ); ?></p>'> <i class="fa fa-info-circle" style="color:#e13b2b;cursor:pointer"></i> More</span> - <span class="details" title="Total Videos Count"><i class="fa fa-video" style="color:#e13b2b"></i> <b><?php echo $channeldata['videos']; ?></b></span></p><br>
		</div><!--/.box-->
	</div><!--/.col-lg-3-->
	
<?php }
endif;