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

	$query_args = array( 'post_type' => 'youtube_channels', 'post_status' => 'publish', 'orderby' => 'meta_value_num', 'page' => $args['page'] );

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

	<div class="col-lg-3 col-sm-6 sfc-campaign-archive-post" id="<?php echo $channel_id; ?>">
		<div class="box grid recipes">
			<div class="by"><i class="fa fa-eye" aria-hidden="true"></i>
				<span id="<?php echo $channel_id; ?>-views" style="color:white">
					<?php echo ytc_number_abbs( $channel_views ); ?>
				</span> <span id="<?php echo $channel_id; ?>-subs" class="fa-pull-right"><i class="fa fa-users" aria-hidden="true"></i> <?php echo ( $channel_subscribers == 0 ) ? 'N/A' : ytc_number_abbs( $channel_subscribers ); ?></span>
			</div><!--/.by-->
			<a href="<?php the_permalink(); ?>" class="showinfoimg"><img class="b-lazy" id="<?php echo $channel_id; ?>-img" src="https://discoverbrands.co/public/img/loader.gif" data-src="<?php echo $channel_img; ?>" alt=""></a>
			<h2><a href="<?php the_permalink(); ?>" class="showinfo" data-gplus="<?php echo $channel_gplus; ?>" data-twitter="<?php echo $channel_tw; ?>" data-instagram="<?php echo $channel_insta; ?>" data-facebook="<?php echo $channel_fb; ?>" data-website="<?php echo $channel_web; ?>" data-snapchat="<?php echo $channel_snap; ?>" data-vk="<?php echo $channel_vk; ?>" data-channelid="<?php the_ID(); ?>" data-title="<?php echo get_the_title(); ?>" target="_blank">
				<?php echo ytc_get_channel_short_title( get_the_title() ); ?>
			</a></h2>
		</div><!--/.box-->
	</div><!--/.col-lg-3-->
<?php }

endif;
