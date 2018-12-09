<?php
/**
 * Youtube Single Shortcode
 *
 * Handles to render youtube channels single page
 **/
	$countries_obj = new WC_Countries();
	$countries = $countries_obj->__get('countries');
	$subscribers = get_post_meta(get_the_ID(), 'wpcf-channel_subscribers', true);
	$subscribers = !empty( $subscribers ) ? ytc_number_abbs( $subscribers ) : 0;
	$views = get_post_meta(get_the_ID(), 'wpcf-channel_views', true);
	$views = !empty( $views ) ? ytc_number_abbs( $views ) : 0;

	$tw 	= get_post_meta(get_the_ID(), 'wpcf-channel_tw', true);
	$insta 	= get_post_meta(get_the_ID(), 'wpcf-channel_insta', true);
	$fb 	= get_post_meta(get_the_ID(), 'wpcf-channel_fb', true);
	$gplus 	= get_post_meta(get_the_ID(), 'wpcf-channel_gplus', true);
	$web 	= get_post_meta(get_the_ID(), 'wpcf-channel_web', true);
	$snap 	= get_post_meta(get_the_ID(), 'wpcf-channel_snap', true);
	$vk 	= get_post_meta(get_the_ID(), 'wpcf-channel_vk', true);
	$channel_id = get_post_meta(get_the_ID(), 'wpcf-channel_id', true);
	$age 	= get_post_meta(get_the_ID(), 'wpcf-channel_dob', true);
	$gender = get_post_meta(get_the_ID(), 'wpcf-channel_gender', true);
	if( !empty( $age ) ) :
		$cdate = new DateTime(date('Y-m-d', current_time('timestamp')));
		$bdate = new DateTime(date('Y-m-d', $age));
		$diff = $bdate->diff($cdate);
		$age = $diff->y;
	endif; //Endif
	if( $banner = get_post_meta(get_the_ID(), 'wpcf-channel_banner', true) ) : //Check Banner ?>
		<img class="channel-banner-img" src="<?php echo $banner;?>" alt="<?php the_title();?>"/>
	<?php else : //Default Logo ?>
		<div class="channel-banner-placeholder"></div>
	<?php endif; //Endif ?>

	<div class="channel-details-wrapper">
		<div class="container-main">
			<div class="channel-profile-box channel-section" style="max-width:600px;">
				<div class="channel-content">
					<figure class="channel-logo">
						<?php if( $logo = get_post_meta( get_the_ID(), 'wpcf-channel_img', true ) ) : //Check Channel Logo ?>
							<img src="<?php echo $logo;?>" alt="<?php the_title();?>">
						<?php else : //Default Logo ?>
							<img src="<?php echo YTC_PLUGIN_URL;?>/assets/images/default.png" alt="<?php the_title();?>">
						<?php endif; //Endif ?>
					</figure><!--/.channel-logo-->
					<?php the_title('<h3 class="channel-name">','</h3>'); //Title of Channel ?>
					<?php if( $country = get_post_meta( get_the_ID(), 'wpcf-channel_country', true ) ) : //Check Channel Country ?>
						<strong class="country-name"><?php echo $countries[$country];?></strong><!--/.country-name-->
					<?php endif; //Endif ?>
					<?php if( $desc = get_post_meta( get_the_ID(), 'wpcf-channel_description', true ) ) : //Check Channel Description
						echo wpautop( $desc, false );
					endif; //Endif
					if( !empty( $age ) || !empty( $gender ) ) : //Check Gender/Age ?>
						<div class="channel-inforow">
							<?php echo !empty( $age ) 	? sprintf('%1$s: %2$s %3$s', 'Age', $age, ( ( $age > 1 ) ? 'years' : 'year' ) ) : ''; ?>
							<?php echo !empty( $gender )? sprintf('<span>%1$s: %2$s</span>', 'Gender', $gender )  : ''; ?>
						</div><!--/.channel-inforow-->
					<?php endif; //Endif ?>
					<div class="channel-statistics">
						<div class="statistic-col">
							<i class="subscribe-icon"></i>
							<?php printf('<span><strong>%1$s</strong>%2$s</span>', $subscribers, 'Subscribers');?>
						</div><!--/.statistic-col-->
						<div class="statistic-col">
							<i class="view-icon"></i>
							<?php printf('<span><strong>%1$s</strong>%2$s</span>', $views, 'Views'); ?>
						</div><!--/.statistic-col-->
					</div><!--/.channel-statistics-->
					<?php if( !empty( $fb ) || !empty( $tw ) ) : //Check Social ?>
						<div class="social-icons">
							  <a href="https://www.youtube.com/channel/<?php echo $channel_id;?>" class="vf-social-icon-yt" target="_blank"><i class="fab fa-youtube"></i></a>
							<?php if( !empty( $fb ) ) : //Check Facebook ?>
								<a href="<?php echo esc_url($fb);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/fb.png" alt="Facebook"></a>
							<?php endif; //Endif
							if( !empty( $tw ) ) : //Check Twitter ?>
								<a href="<?php echo esc_url($tw);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/tw.png" alt="Twitter"></a>
							<?php endif; //Endif
							if( !empty( $insta ) ) : //Check Instagram ?>
								<a href="<?php echo esc_url($insta);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/insta.png" alt="Instagram"></a>
							<?php endif; //Endif
							if( !empty( $gplus ) ) : //Check Google+ ?>
								<a href="<?php echo esc_url($gplus);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/gplus.png" alt="Google+"></a>
							<?php endif; //Endif
							if( !empty( $snap ) ) : //Check SnapChat ?>
								<a href="<?php echo esc_url($snap);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/snap.png" alt="SnapChat"></a>
							<?php endif; //Endif
							if( !empty( $vk ) ) : //Check VK ?>
								<a href="<?php echo esc_url($vk);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/vk.png" alt="VK"></a>
							<?php endif; //Endif
							if( !empty( $web ) ) : //Check Web ?>
								<a href="<?php echo esc_url($web);?>" target="_blank"><img src="<?php echo YTC_PLUGIN_URL;?>assets/images/web.png" alt="Web"></a>
							<?php endif; //Endif ?>
						</div><!--/.social-icons-->
					<?php endif; //Endif ?>
				</div><!--/.channel-content-->
				<?php if( $keywords = get_post_meta(get_the_ID(), 'wpcf-channel_keywords', true) ) : ?>
					<div class="keywords-row">
						<strong>Keywords</strong>
						<?php echo $keywords; ?>
					</div><!--/.keywords-row-->
				<?php endif; //Endif ?>
			</div><!--/.channel-profile-box-->

			<div class="latest-videos-section channel-section">
				<div class="latest-videos channel-section-item">
					<div class="latest-vidoes-section-headline">
						<h2>Latest Videos</h2>
						<a href="https://www.youtube.com/channel/<?php echo $channel_id;?>" class="vf-link-to-videos" target="_blank">All Videos</a>
					</div>
					<?php if( $latest_videos = ytc_get_channel_latest_videos( $channel_id ) ) : ?>
						<div class="grid-cols2 mobile-scroll-row">
							<?php foreach( $latest_videos as $video ) : //Video List ?>
								<div class="grid-col mobile-scroll-row-item">
									<div class="channel-video-box">
										<div class="channel-video">
											<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video['id'];?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
										</div><!--/.channel-video-->
										<h3><?php echo $video['title'];?></h3>
									</div><!--/.channel-video-box-->
								</div><!--/.grid-col-->
							<?php endforeach; //Endforeach ?>
						</div><!--/.grid-cols-->
					<?php endif; //Endif ?>
				</div><!--/.latest-videos-->
				<?php if( !empty( $tw ) ) : //Check Twitter ?>
					<div class="latest-tweets channel-section-item">
						<h2>Latest Tweets</h2>
						<div class="tweets-list">
							<div class="grid-cols2 grid-cols2-sm">
								<div class="grid-col">
									<div class="tweet-box"><a class="twitter-timeline" data-height="600" data-width="500" href="<?php echo esc_url( $tw );?>"></a></div><!--/.tweet-box-->
								</div><!--/.grid-col-->
							</div><!--/.grid-cols-->
						</div><!--/.tweets-list-->
					</div><!--/.latest-tweets-section-->
				<?php endif; //Endif ?>
			</div><!--/.latest-videos-section-->
			<div class="related-collaborations-section channel-section">

				<?php if( $related_collabs = toolset_get_related_posts( get_the_ID(), 'channel-campaign', 'parent') ) : //Check Related Campaign
					echo '<h2>Collaborations</h2>';
					echo do_shortcode('[wpv-view name="campaign-search" view_display="layout" limit="4" ids="'.implode(',', $related_collabs).'"]');
				else : //Else
					//echo do_shortcode('[wpv-view name="campaign-search" view_display="layout" limit="3"]');
				endif; //Endif ?>
			</div><!--/.related-collaborations-section-->

			<div class="sf-blog-banner" style="background: url(https://2iktwd2ubfm82gjo2r3hm8g6-wpengine.netdna-ssl.com/wp-content/uploads/2018/11/vf-blog-banner-bg.jpg);padding:80px 15px;">
				<h2>Make YouTube Collaborations Come True</h2>
				<a href="/youtube-collaborations"><button class="sf-get-started">Let's get it</button></a>
			</div><!--/.sf-blog-banner-->

			<?php  if( $related_posts = toolset_get_related_posts( get_the_ID(), 'channel-post', 'parent') ) : //Check Blog Post Related
				$rba_big = array_shift( $related_posts ); ?>
				<div class="related-blog-section channel-section" style="margin-top: 40px;">
					<h2>Related Articles</h2>
					<div class="grid-cols2 grid-cols2-sm cols-parent">
						<?php if( !empty( $rba_big ) ) : //Check Big Article ?>
							<div class="grid-col">
								<div class="related-blog-col featured">
									<a href="<?php echo get_permalink( $rba_big );?>">
										<figure>
											<?php if( $video_id = get_post_meta($rba_big, 'wpcf-post-youtube-video-id', true) ) : //Check Video ID ?>
												<img src="https://img.youtube.com/vi/<?php echo $video_id;?>/sddefault.jpg" alt="<?php echo get_the_title( $rba_big );?>">
											<?php elseif( $rba_big_thumb = get_the_post_thumbnail_url( $rba_big, 'medium_large') ) : //Check Thumbnail ?>
												<img src="<?php echo $rba_big_thumb;?>" alt="<?php echo get_the_title( $rba_big );?>">
											<?php else : //Else ?>
												<img src="<?php echo YTC_PLUGIN_URL;?>assets/images/630x450.jpg" alt="<?php echo get_the_title( $rba_big );?>">
											<?php endif; //Endif ?>
										</figure>
										<h4><?php echo get_the_title( $rba_big );?></h4>
									</a>
									<?php if( $rba_category = get_the_category( $rba_big ) ) : //Check Category
											echo '<a class="related-blog-category" href="' . esc_url( get_category_link( $rba_category[0]->term_id ) ) . '">' . esc_html( $rba_category[0]->name ) . '</a>';
									endif; //Endif ?>
								</div><!--/.related-blog-col-->
							</div><!--/.grid-col-->
						<?php endif; //Endif
						if( !empty( $related_posts ) ) : //Regular Posts ?>
							<div class="grid-col">
								<div class="grid-cols2 grid-cols2-sm">
									<?php foreach( $related_posts as $rbapost ) : //Loop to List Posts ?>
										<div class="grid-col">
											<div class="related-blog-col">
												<a href="<?php echo get_permalink( $rbapost );?>">
													<figure>
														<?php if( $video_id = get_post_meta($rbapost, 'wpcf-post-youtube-video-id', true) ) : //Check Video ID ?>
															<img src="https://img.youtube.com/vi/<?php echo $video_id;?>/hqdefault.jpg" alt="<?php echo get_the_title( $rbapost );?>">
														<?php elseif( $rbapost_thumb = get_the_post_thumbnail_url( $rbapost, 'product-thumbnail') ) : //Check Thumbnail ?>
															<img src="<?php echo $rbapost_thumb;?>" alt="<?php echo get_the_title( $rbapost );?>">
														<?php else : //Else ?>
															<img src="<?php echo YTC_PLUGIN_URL;?>assets/images/305x215.jpg" alt="<?php echo get_the_title( $rbapost );?>">
														<?php endif; //Endif ?>
													</figure>
													<h4><?php echo get_the_title( $rbapost );?></h4>
												</a>
												<?php if( $rba_category = get_the_category( $rbapost ) ) : //Check Category
														echo '<a class="related-blog-category" href="' . esc_url( get_category_link( $rba_category[0]->term_id ) ) . '">' . esc_html( $rba_category[0]->name ) . '</a>';
												endif; //Endif ?>
											</div><!--/.related-blog-col-->
										</div><!--/.grid-col-->
									<?php endforeach; //Endforeach ?>
								</div><!--/.grid-cols2-->
							</div><!--/.grid-col-->
						<?php endif; //Endif ?>
					</div><!--/.grid-cols-->
				</div><!--/.related-blog-section-->
			<?php else : //Else
				$related_posts = get_posts( array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 5, 'orderby' => 'rand') );
				$rba_big = array_shift( $related_posts ); ?>
				<div class="related-blog-section channel-section">
					<h2>Discover articles from our Blog</h2>
					<div class="grid-cols2 grid-cols2-sm cols-parent">
						<?php if( !empty( $rba_big ) ) : //Check Big Article ?>
							<div class="grid-col">
								<div class="related-blog-col featured">
									<a href="<?php echo get_permalink( $rba_big->ID );?>">
										<figure>
											<?php if( $video_id = get_post_meta($rba_big->ID, 'wpcf-post-youtube-video-id', true) ) : //Check Video ID ?>
												<img src="https://img.youtube.com/vi/<?php echo $video_id;?>/sddefault.jpg" alt="<?php echo get_the_title( $rba_big->ID );?>">
											<?php elseif( $rba_big_thumb = get_the_post_thumbnail_url( $rba_big->ID, 'medium_large') ) : //Check Thumbnail ?>
												<img src="<?php echo $rba_big_thumb;?>" alt="<?php echo get_the_title( $rba_big->ID );?>">
											<?php else : //Else ?>
												<img src="<?php echo YTC_PLUGIN_URL;?>assets/images/630x450.jpg" alt="<?php echo get_the_title( $rba_big->ID );?>">
											<?php endif; //Endif ?>
										</figure>
										<h4><?php echo get_the_title( $rba_big->ID );?></h4>
									</a>
									<?php if( $rba_category = get_the_category( $rba_big->ID ) ) : //Check Category
											echo '<a class="related-blog-category" href="' . esc_url( get_category_link( $rba_category[0]->term_id ) ) . '">' . esc_html( $rba_category[0]->name ) . '</a>';
									endif; //Endif ?>
								</div><!--/.related-blog-col-->
							</div><!--/.grid-col-->
						<?php endif; //Endif
						if( !empty( $related_posts ) ) : //Regular Posts ?>
							<div class="grid-col">
								<div class="grid-cols2 grid-cols2-sm">
									<?php foreach( $related_posts as $rbapost ) : //Loop to List Posts ?>
										<div class="grid-col">
											<div class="related-blog-col">
												<a href="<?php echo get_permalink( $rbapost->ID );?>">
													<figure>
														<?php if( $video_id = get_post_meta($rbapost->ID, 'wpcf-post-youtube-video-id', true) ) : //Check Video ID ?>
															<img src="https://img.youtube.com/vi/<?php echo $video_id;?>/hqdefault.jpg" alt="<?php echo get_the_title( $rbapost->ID );?>">
														<?php elseif( $rbapost_thumb = get_the_post_thumbnail_url( $rbapost->ID, 'product-thumbnail') ) : //Check Thumbnail ?>
															<img src="<?php echo $rbapost_thumb;?>" alt="<?php echo get_the_title( $rbapost->ID );?>">
														<?php else : //Else ?>
															<img src="<?php echo YTC_PLUGIN_URL;?>assets/images/305x215.jpg" alt="<?php echo get_the_title( $rbapost->ID );?>">
														<?php endif; //Endif ?>
													</figure>
													<h4><?php echo get_the_title( $rbapost->ID );?></h4>
												</a>
												<?php if( $rba_category = get_the_category( $rbapost->ID ) ) : //Check Category
													echo '<a class="related-blog-category" href="' . esc_url( get_category_link( $rba_category[0]->term_id ) ) . '">' . esc_html( $rba_category[0]->name ) . '</a>';
												endif; //Endif ?>
											</div><!--/.related-blog-col-->
										</div><!--/.grid-col-->
									<?php endforeach; //Endforeach ?>
								</div><!--/.grid-cols2-->
							</div><!--/.grid-col-->
						<?php endif; //Endif ?>
					</div><!--/.grid-cols-->
				</div><!--/.related-blog-section-->
			<?php endif; //Endif ?>
		</div><!--/.container-main-->
	</div><!--/.channel-details-wrapper-->
