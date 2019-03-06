<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

	<?php if ( have_posts() ) : ?>
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
					<?php ytc_get_channels_list(); ?>
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
	else : //Else
		echo '<p>'.__('No channels found.','youtube-channels').'</p>';
	endif; ?>
	
<?php get_footer(); ?>