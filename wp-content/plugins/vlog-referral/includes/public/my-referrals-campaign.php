<?php
/**
 * Show Referred Campaign Details
 *
 * Handles to show referred campaign details
 *
 * @since Vlog Referral 1.0
 **/ 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	global $wpdb;
	$userdata = get_currentuserinfo();
	$campaign = get_query_var('my-referrals');
	$page 	= ( isset( $_GET['pg'] ) && !empty( $_GET['pg'] ) ) ? $_GET['pg'] : 1; 	
	$winners	= vlogref_get_campaign_winners($campaign);
	$ref_prizes = vlogref_referral_prizes( $campaign );	
	
	if( !empty( $winners ) && !array_key_exists($userdata->ID, $winners) ) : //Loser ?>
		<div class="sfc-campaign-status-notification sfc-campaign-status-notification-pending vf-referral-notice">
		  	<div class="sfc-campaign-status-notification-item">
				<div><i class="fas fa-check-circle"></i> <?php _e('The first phase has been completed.','vlog-referral');?></div><br />
			</div><!--/.sfc-campaign-status-notification-item-->
		</div><!--/.sfc-campaign-status-notification-->
	<?php elseif( !empty( $winners ) && array_key_exists($userdata->ID, $winners) ) : //Winner ?>
		<div class="sfc-campaign-status-notification sfc-campaign-status-notification-pending vf-referral-notice">
		  	<div class="sfc-campaign-status-notification-item">
				<div><i class="fas fa-check-circle"></i> <?php _e('You won! Thank you for refering this collaboration.','vlog-referral');?></div><br />
			</div>		
		</div>
	<?php endif; //Endif ?>
	
	<div class="vf-referral">
    	<div class="vf-referral-col">
			<h1 class="vf-referral-headline"><?php _e('Refer your friends - Win awesome prizes','vlog-referral');?></h1>
      		<div class="vf-referral-current">
				<?php $avatar = get_avatar($userdata->ID, 92, '', $userdata->user_login, array('height' => 72, 'width' => 92, 'class' => 'vf-ambassador-avatar'));
				if( !empty( $avatar ) ) : //Chekc Avatar
					echo $avatar;
				else : //else ?>
					<img class="vf-ambassador-avatar" src="/wp-content/uploads/2018/06/image-placeholder.png" alt="<?php echo $userdata->user_login;?>"/>
				<?php endif; //Endif ?>
				<br><h3 class="vf-ambassador-name"><?php echo $userdata->user_login;?></h3>
				<div class="vf-ambassador-stats">
					<div class="vf-abassador-stat"><?php echo vlogref_user_campaign_referral_count($campaign);?><br><span><?php _e('Your Referrals','vlog-referral');?></span></div>
					<div class="vf-abassador-stat">#<?php echo vlogref_user_position($campaign, $userdata->ID);?><br><span><?php _e('Current Position','vlog-referral');?></span></div>
					<div class="vf-abassador-stat"><?php echo vlogref_campaign_upvotes_goal($campaign);?> <br><span><?php _e('Goal','vlog-referral');?></span></div>
        		</div><!--/.vf-ambassador-stats-->
			</div><!--/.vf-referral-current-->
			<div class="vf-referral-ranking">
				<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
					<thead>
						<tr>
						  <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php _e('Position', 'vlog-referral');?></span></th>
						  <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php _e('Name', 'vlog-referral');?></span></th>
						  <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Referred Upvotes', 'vlog-referral');?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php if( $rankings = vlogref_get_campaign_referrals( array( 'campaign' => $campaign, 'page' => $page ) ) ) : //Chek Ranking Not Empty							
							foreach( $rankings as $rank ) : //Loop to List Referrals
								$referred_by = get_userdata($rank['referred_by']);								
								$winner_str = '';
								if( array_key_exists($referred_by->ID, $winners) && $referred_by->ID !== $userdata->ID ) : //Check Winner
									$winner_str = ' <span class="vf-creator">'. __('Won','vlog-referral').'</span>';
								elseif( array_key_exists($referred_by->ID, $winners) && $referred_by->ID === $userdata->ID ) :
									$winner_str = ' <span class="vf-creator">'. __('You won','vlog-referral').'</span>';
								endif; //Endif ?>
								<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
									<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php _e('Position', 'vlog-referral');?>">
										<?php echo '#'.vlogref_user_position($campaign, $referred_by->ID) . $winner_str;?>
									</td>
									<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php _e('Name', 'vlog-referral');?>">
										<time><?php echo ucfirst($referred_by->user_login);?></time>
									</td>
									<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Upvotes', 'vlog-referral');?>">
										<span><strong>↑</strong><span class="upvote-count-sc"> <?php echo $rank['upvotes'];?></span></span>
									</td>
								</tr>
						<?php endforeach; //Endforeach
						else : //Else ?>
							<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
								<td colspan="3" class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"><?php _e('No records found.', 'vlog-referral');?></td>
							</tr>
						<?php endif; //Endif ?>							
					</tbody>
				</table>		
				<?php include_once( VLOGREF_PLUGIN_PATH . '/includes/public/class-ref-paginator.php');
					$vlogref_campaign_paginator  = new Vlogref_Paginator( "SELECT COUNT(upvoted) AS upvotes FROM ".VLOG_REFERRAL_TABLE." WHERE 1=1 AND campaign='$campaign' AND upvoted=1 GROUP BY referred_by ORDER BY upvotes DESC;" );
					echo $vlogref_campaign_paginator->createLinks(); 
				?>				
			</div><!--/.vf-referral-ranking-->
		</div><!--/.vf-referral-col-->		
		<div class="vf-referral-col">
			<?php echo do_shortcode('[wpv-view name="campaign-search" view_display="layout" ids="'.$campaign.'"]'); ?>      		
			<?php if( empty( $winners ) && vlogref_is_referral_enable( $campaign ) ) : //Check Campaign Active ?>
				<h3 style="margin:30px 0 0 0;"><?php _e('Get your friends to upvote with this unique URL:','vlog-referral');?></h3><br>
				<input type="text" class="sf-mc-email" value="<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" style="background: #eee;"><br>
				<ul class="sf-sharing-buttons-inline">
					<li class="sf-sharing-button-facebook">
						<a id="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" target="_blank"><i id="facebook" class="fab fa-facebook"></i> Facebook</a>
					</li>
					<li class="sf-sharing-button-twitter">
						<a id="twitter" href="https://twitter.com/intent/tweet?text=<?php echo get_the_title($campaign) .' '. do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" target="_blank"><i id="twitter" class="fab fa-twitter"></i> Twitter</a>
					</li>
					<li class="sf-sharing-button-whatsapp">
						<a id="whatsapp" href="whatsapp://send?text=<?php echo get_the_title($campaign) .' '. do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" data-action="share/whatsapp/share" target="_blank"><i id="whatsapp" class="fab fa-whatsapp"></i> Whatsapp</a>
					</li>
					<li class="sf-sharing-button-reddit">
						<a id="reddit" href="http://www.reddit.com/submit?url=<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" target="_blank"><i id="reddit" class="fab fa-reddit"></i> Reddit</a>
					</li>
					<li class="sf-sharing-button-messenger messenger-mobile">
						<a id="messenger" href="fb-messenger://share/?link=<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>&app_id=181038895828102" target="_blank"><i id="whatsapp" class="fab fa-facebook-messenger"></i> Messenger</a>
					</li>
					<li class="sf-sharing-button-messenger messenger-desktop">
						<a id="messenger" href="http://www.facebook.com/dialog/send?app_id=181038895828102&link=<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>&redirect_uri=<?php echo do_shortcode('[vlog_referral_url id="'.$campaign.'"]');?>" target="_blank"><i id="whatsapp" class="fab fa-facebook-messenger"></i> Messenger</a>
					</li>
				</ul>
			<?php endif; //Endif
			if( !empty( $winners ) ) : //Check Winner Declare ?>
				<h3 style="margin: 30px 0 0 0;"><?php _e('The first phase of this campaign has been completed','vlog-referral');?></h3><br>
			<?php endif; //Endif ?>
				<br>
				<div id="referral-prizes">
					<?php if( !empty( $winners ) && array_key_exists($userdata->ID, $winners) ) : //Current User Winner ?>
						<h3><?php _e('You won the following prize','vlog-referral');?></h3>
						<?php $price_data = vlogref_get_prize_details( $winners[$userdata->ID] ); ?>
						<div class="vf-referral-prize prize-1"> <i class="fas fa-trophy"></i> <span><strong><?php echo $price_data['title'];?></strong></span> </div>
					<?php endif; //Endif ?>
					<?php if( empty( $winner ) && vlogref_is_referral_enable( $campaign ) ) : //Loser ?>
						<h3><?php _e('The prizes','vlog-referral');?></h3>
						<div class="vf-referral-prize prize-1"> <i class="fas fa-trophy"></i> <span><strong><?php _e('The Top spot','vlog-referral');?></strong> <?php _e('receives a goodie box!','vlog-referral');?></span></div>
						<div class="vf-referral-prize prize prize-2"> <i class="fas fa-trophy"></i> <span><strong><?php _e('Top 5 referrers','vlog-referral');?></strong> <?php _e('receive a hoodie.','vlog-referral');?></span></div>
						<div class="vf-referral-prize prize prize-3"> <i class="fas fa-trophy"></i> <span><strong><?php _e('Refer 5 friends','vlog-referral');?></strong> <?php _e('to receive an official t-shirt.','vlog-referral');?></span></div>
						<div class="vf-referral-prize prize mbn prize-4"> <i class="fas fa-heart"></i> <span><strong><?php _e('Karma','vlog-referral');?></strong> <?php _e('for helping us spread the word and making collabs come true!','vlog-referral');?></span></div>
					<?php endif; //Endif ?>
				</div><!--/referral-prizes-->			
		</div><!--/.vf-referral-col-->
	</div><!--/.vf-referral-->