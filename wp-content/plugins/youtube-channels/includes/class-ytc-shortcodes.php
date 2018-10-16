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
	}

	/**
	* Enqueue All Scripts / Styles
	*
	* @since YouTube Channels 1.0
	**/
	public function register_scripts(){

		//Bootstrap Style
		//wp_register_style( 'ytc-bootstrap-style',	YTC_PLUGIN_URL . 'assets/css/bootstrap.min.css', array(), null );
		//Common Styles
		wp_register_style( 'ytc-styles', 			YTC_PLUGIN_URL . 'assets/css/styles.min.css', array(), null );
		//App Style
		wp_register_style( 'ytc-app-style',			YTC_PLUGIN_URL . 'assets/css/app.css', array(), null );
		//BLazy
		wp_register_script( 'ytc-blazy-script', 	YTC_PLUGIN_URL . 'assets/js/blazy.min.js', array('jquery'), null, true );
		//Bootstrap Script
		//wp_register_script( 'ytc-bootstrap-script',	YTC_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), null, true );
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
		wp_enqueue_style( array( 'ytc-bootstrap-style', 'ytc-styles', 'ytc-app-style') );
		wp_enqueue_script( array('jquery', 'jquery-ui-core', 'ytc-blazy-script', 'ytc-bootstrap-script', 'ytc-app-script') );

		$q 		= isset( $_GET['q'] ) ? $_GET['q'] : ''; //Search Query
		$sortby	= isset( $_GET['sortBy'] ) 	? $_GET['sortBy'] 	: 'subscribers'; //Orderby
		$orderby= isset( $_GET['orderBy'] ) ? $_GET['orderBy'] 	: 'desc'; //Order

		ob_start(); //Start Buffer ?>

		<div class="list ytc-container">

     <div class="sfc-campaign-archive">
			<div class="fc-campaign-archive-container">
                <div class="sfc-campaign-archive-heading">
                    <h1 class="sfc-campaign-archive-title">Discover your favorite YouTubers</h1>
                </div><!--/.row-->


				<form action="<?php the_permalink();?>" method="GET" id="ytc-search-form" class="sfc-campaign-archive-search">
					<div class="sfc-campaign-archive-search-fields">


							<div class="sfc-campaign-archive-search-item">
								<span class="fa fa-search sfc-campaign-search-icon"></span>
								<input id="ytc-search-input" name="q" type="search" class="form-control sfc-campaign-archive-search-input" placeholder="Search Channels..." value="<?php if( isset( $q ) ){ echo $q; } ?>">
							</div><!--/.form-group-->



							<div class="sfc-campaign-archive-search-item">
								<button type="submit" class="btn btn-block btn-primary sfc-youtube-channel-search-button">Search</button>
							</div><!--/.form-group-->



							<!--<div class="sfc-campaign-archive-search-item">
								<button class="btn btn-block btn-primary" id="showfilters">Show Filters</button>
							</div>--><!--/.form-group-->


					</div><!--/.row-->


					<div class="row ytc-filters" id="filters" style="display:one">

							<div class="ytc-filter-group">
								<label for="ytc-sortBy">Sort by</label>
								<select onchange="this.form.submit()" name="sortBy" id="ytc-sortBy" class="form-control" id="">
								   <option value="subscribers" <?php selected( 'subscribers', $sortby );?>>Subscribers</option>
								   <option value="views" <?php selected( 'views', $sortby );?>>Views</option>
							   </select>
							</div><!--/.form-group-->


							<div class="ytc-filter-group">
								<label for="ytc-orderBy">Order By</label>
								<select onchange="this.form.submit()" name="orderBy" id="ytc-orderBy" class="form-control" id="">
								   <option value="asc" <?php selected( 'asc', $orderby );?>>Ascending</option>
								   <option value="desc" <?php selected( 'desc', $orderby );?>>Descending</option>
							   </select>
							</div><!--/.form-group-->

					</div><!--/.row-->



				</form>

				</div><!--/.container-->
			</div>

				<div class="row tags-container"><div class="tag"></div></div>
				<div id="ytc-searchloader" style="display:none"><div class="col-lg-12"><center><i class="fa fa-spinner fa-2x fa-spin"></i></center></div></div>
        <div class="sfc-campaign-archive-container">

					<?php if( !empty( $q ) ){ ?>
<span><?php echo ytc_get_channels_count( array( 'search' => $q ) ); ?> creators found for keyword: <b><?php echo $q; ?></b></span>
					<?php } else { ?>
						<span><?php echo ytc_get_channels_count(); ?> creators found </span>
					<?php } ?>


				<div class="row sfc-campaign-archive-posts" id="ytc-channles-list">
					<?php if( $showresults ) : //Check Show Results

						ytc_get_channels_list( array( 'search' => $q, 'order' => $orderby, 'orderby' => $sortby ) );


					endif; //Edif ?>
				</div><!--/.row-->
			</div>
				<?php if( ytc_get_channels_count( array( 'search' => $q ) ) > 40 ){ ?>


							<button id="ytc-loadmore" class="sfc-ytc-load-more-button">Load More</button>


				<?php } ?>

		</div><!--/.list-->

		<!-- Channel Info Modal-->
		<div class="modal" id="infomodal">
			<div class="modal-dialog">
				<div class="modal-content">
					<!-- Modal Header -->
					<div class="modal-header" style="background:#e13b2b;display:block">
						<p style="color:white;margin-bottom:0"><i class="fa fa-eye"></i> <span id="cviews"></span> <span class="float-right"> <i class="fa fa-users"></i> <span id="csubs"></span></span></p>
					</div><!--/.modal-header-->
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
                            </div><!--/.form-group-->
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

					</div><!--/.modal-body-->
				</div><!--/.modal-content-->
			</div><!--/.modal-dialog-->
		</div><!--/#infomodal-->
		<?php $content = ob_get_contents(); //End Buffer
		ob_get_clean();
		return $content;
	}

}
//Run Class
$ytc_shortcodes = new YTC_Shortcodes();
endif;
