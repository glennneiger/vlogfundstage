<?php
/**
 * Shortcodes
 *
 * Handles all shortcodes of plugin
 *
 * @since YouTube Channels 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('YTC_Shortcodes') ) :

class YTC_Shortcodes{

	//Construct which run class
	function __construct(){

		//Enqueue Scripts
		add_action( 'wp_enqueue_scripts', array( $this,'register_scripts' ) );
		//Channels Shortcode
		add_shortcode( 'ytc_channels', array($this, 'channels_shortcode_callback') );
		//Channels Shortcode
		add_shortcode( 'ytc_channel_single', array($this, 'channels_single_shortcode_callback') );
		//Channels Meta Data
		add_action( 'wp_head', array($this, 'channel_meta_data') );
	}
	
	/**
	* Channels Meta Data
	*
	* @since YouTube Channels 1.0
	**/
	public function channel_meta_data(){
		
		//Check YouTube Channels Details Page
		if( !is_singular('youtube_channels') ) : return; endif;
		
		$desc = get_post_meta( get_the_ID(), 'wpcf-channel_description', true ); //Description
		$desc = !empty( $desc ) ? $desc : get_the_content();
	
		echo '<meta property="og:type" content="website"/>';
		echo '<meta property="og:url" content="'.get_permalink().'"/>';
		echo '<meta property="og:site_name" content="'.get_bloginfo('name').'"/>';
		if( $logo = get_post_meta( get_the_ID(), 'wpcf-channel_img', true ) ) : //Check Channel Logo
			$logo_sizes = getimagesize( $logo );
			echo '<meta property="og:image" content="'.$logo.'"/>';
			echo '<meta property="og:image:secure_url" content="'.$logo.'"/>';
			echo '<meta property="og:image:width" content="'.$logo_sizes[0].'"/>';
			echo '<meta property="og:image:height" content="'.$logo_sizes[1].'"/>';
			echo '<meta name="twitter:image" content="'.$logo.'"/>';
		endif; //Endif		
		echo '<meta property="fb:app_id" content="181038895828102"/>';
		echo '<meta property="og:title" content="'.get_the_title().'"/>';
		echo '<meta property="og:locale" content="'.get_locale().'"/>';
		echo '<meta property="og:description" content="'.substr($desc,0,299).'"/>';
		echo '<meta name="twitter:domain" content="'.get_bloginfo('url').'"/>';
		echo '<meta name="twitter:card" content="summary"/>';
		echo '<meta name="twitter:title" content="'.get_the_title().'"/>';
		echo '<meta name="twitter:description" content="'.substr($desc,0,199).'"/>';
		echo '<meta name="twitter:site" content="'.get_permalink().'"/>';
		if( $tw = get_post_meta(get_the_ID(), 'wpcf-channel_tw', true) ) : //Check Twitter			
			echo '<meta name="twitter:creator" content="'.ytc_find_twitter_username($tw).'"/>';
		endif; //Endif

	}

	/**
	* Enqueue All Scripts / Styles
	*
	* @since YouTube Channels 1.0
	**/
	public function register_scripts(){

		//Common Styles
		wp_register_style( 'ytc-styles', 			YTC_PLUGIN_URL . 'assets/css/styles.min.css', array(), null );
		
		if( is_singular('youtube_channels') ) { //Check Youtube Channel Page
			wp_register_style( 'ytc-detail-style',	YTC_PLUGIN_URL . 'assets/css/yt-detail.css', array(), null );
			wp_enqueue_style( 'ytc-detail-style' );
			wp_register_script( 'ytc-twitter-script', 	'https://platform.twitter.com/widgets.js', array(), null, true );
		}
		//App Style
		wp_register_style( 'ytc-app-style',			YTC_PLUGIN_URL . 'assets/css/app.css', array(), null );
		//BLazy
		wp_register_script( 'ytc-blazy-script', 	YTC_PLUGIN_URL . 'assets/js/blazy.min.js', array('jquery'), null, true );
		//Script for Public Function
		wp_register_script( 'ytc-app-script', 		YTC_PLUGIN_URL . 'assets/js/app.js', array('jquery'), null, true );
		wp_localize_script( 'ytc-app-script', 'YTC_Obj', array( 'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) ) );
	}

	/**
	* Channels Shortcode
	*
	* Handles to list all youtube channels with shortcode
	**/
	public function channels_shortcode_callback( $atts, $content = null ){
		
		extract( shortcode_atts( array(
			'showresults' => 1 //Display Results by Default
		), $atts, 'ytc_channels' ) );

		//Enqueue Scripts / Styles
		wp_enqueue_style( array( 'ytc-styles', 'ytc-app-style') );
		wp_enqueue_script( array('jquery', 'jquery-ui-core', 'ytc-blazy-script', 'ytc-app-script') );

		ob_start(); //Start Buffer ?>

		<div class="list ytc-container">
     		<div class="sfc-campaign-archive">
				<div class="fc-campaign-archive-container">
					<div class="sfc-campaign-archive-heading">
						<h1 class="sfc-campaign-archive-title">Discover your favorite YouTubers</h1>
					</div><!--/.row-->
					<form action="<?php the_permalink();?>" method="POST" id="ytc-search-form" class="sfc-campaign-archive-search">
						<div class="sfc-campaign-archive-search-fields">
							<div class="sfc-campaign-archive-search-item">
								<span class="fa fa-search sfc-campaign-search-icon"></span>
								<input id="ytc-search-input" name="q" type="search" class="form-control sfc-campaign-archive-search-input" placeholder="Search Channels..." value="<?php if( isset( $q ) ){ echo $q; } ?>">
								<button type="button" class="sfc-campaign-archive-reset-button wpv-reset-trigger js-wpv-reset-trigger"><i class="fas fa-times"></i></button>
							</div><!--/.form-group-->
							<div class="sfc-campaign-archive-search-item">
								<button type="submit" class="btn btn-block btn-primary sfc-youtube-channel-search-button">Search</button>
							</div><!--/.form-group-->
							<?php /*<div class="sfc-campaign-archive-search-item">
								<button class="btn btn-block btn-primary" id="showfilters">Show Filters</button>
							</div><!--/.form-group--> */?>
						</div><!--/.row-->

						<div class="row ytc-filters" id="filters" style="display:one">
							<div class="ytc-filter-group">
								<label for="ytc-sortBy">Sort by</label>
								<select name="sortBy" id="ytc-sortBy" class="form-control">
								   <option value="subscribers">Subscribers</option>
								   <option value="views">Views</option>
							   </select>
							</div><!--/.form-group-->
							<div class="ytc-filter-group">
								<label for="ytc-orderBy">Order By</label>
								<select name="orderBy" id="ytc-orderBy" class="form-control">
									<option value="desc">Descending</option>
								   <option value="asc">Ascending</option>
							   </select>
							</div><!--/.form-group-->
						</div><!--/.row-->
					</form>
				</div><!--/.container-->
			</div><!--/.sfc-campaign-archive-->
        	<div class="sfc-campaign-archive-container">
				<div class="row tags-container"><div class="tag"></div></div>
				<div id="ytc-searchloader"><div class="col-lg-12"><center><i class="fa fa-spinner fa-2x fa-spin"></i></center></div></div>
				<div id="ytc-creators-wrap"><strong class="count"><?php echo ytc_get_channels_count(); ?></strong> creators found </div>
				<div class="row sfc-campaign-archive-posts" id="ytc-channles-list">
					<?php if( $showresults ) : //Check Show Results
						ytc_get_channels_list();
					endif; //Edif ?>
					<div class="sfc-campaign-archive-post"></div>
					<div class="sfc-campaign-archive-post"></div>
					<div class="sfc-campaign-archive-post"></div>
				</div><!--/.row-->
			</div><!--/.sfc-campaign-archive-container-->
			<input type="hidden" id="ytc-page" value="1"/>
			<?php if( ytc_get_channels_count() > 40 ){ ?>
				<button id="ytc-loadmore" class="sfc-ytc-load-more-button">Load More</button>
			<?php } ?>
		</div><!--/.list-->

		<?php /*<!-- Channel Info Modal-->
			<!--<div class="modal" id="infomodal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header" style="background:#e13b2b;display:block">
							<p style="color:white;margin-bottom:0"><i class="fa fa-eye"></i> <span id="cviews"></span> <span class="float-right"> <i class="fa fa-users"></i> <span id="csubs"></span></span></p>
						</div>
						<div class="modal-body">
							<center>
								<div class="form-group"><h4 id="ctitle"></h4></div>
								<div class="form-group"><img id="cimg" width="150" class="img-fluid rounded-circle"/></div>
								<div class="form-group">
									<button data-title="Latest Videos" class="showvideos btn btn-danger"><i class="fa fa-video"></i></button>
									<button data-twitter="" data-title="Latest Tweets" class="showtweets btn btn-primary"><i class="fab fa-twitter"></i></button>
									<a href="" target="_blank" style="color:white" class="instagram btn instagram"><i class="fab fa-instagram"></i></a>
									<a id="facebook" href="" target="_blank" style="color:white;background:#3B5998" class="btn"><i class="fab fa-facebook"></i></a>
									<a id="snapchat" href="" target="_blank" style="color:black;background:#FFFC00;" class="btn"><i class="fab fa-snapchat-square"></i></a>
									<a id="website" href="" target="_blank" style="color:white" class="btn btn-success"><i class="fas fa-globe"></i></a>
									<a id="gplus" href="" target="_blank" style="color:white;background:#d34836" class="btn"><i class="fab fa-google-plus-square"></i></a>
									<a id="vk" href="" target="_blank" style="color:white;background:#4c75a3" class="btn"><i class="fab fa-vk"></i></a>
								</div>
								<div class="form-group"><h4 id="detailtitle">Latest Videos</h4></div>
								<p id="videoloader"><i class="fa fa-spinner fa-spin"></i></p>
								<div id="cvideos">
								   <div class="youtubes" data-embed="qs3t7zgKmAk">
										<div class="play-button"></div>
									</div>
								</div>
								<div id="tweets"></div>
							</center>
							<button type="button" class="close-modal" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>-->*/
		?>
		<?php $content = ob_get_contents(); //End Buffer
		ob_get_clean();
		return $content;
	}
	
	/**
	* Channels Shortcode
	*
	* Handles to list all youtube channels with shortcode
	**/
	public function channels_single_shortcode_callback( $atts, $content = null ){
		
		wp_enqueue_script( array('ytc-twitter-script') );
		
		ob_start(); //Start Buffer
		include_once( YTC_PLUGIN_PATH . '/includes/channels-single.php');
		$content = ob_get_contents(); //Get Content
		ob_get_clean(); //Clean Buffer
		return $content;
	}

}
//Run Class
$ytc_shortcodes = new YTC_Shortcodes();
endif;
