<?php
/**
 * List User Referrals
 *
 * Handles to list user referrals
 *
 * @since Vlog Referral 1.0
 **/ 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	global $user_ID, $wpdb; 
	$page 	= ( isset( $_GET['pg'] ) && !empty( $_GET['pg'] ) ) ? $_GET['pg'] : 1;  ?>
	<div class="vf-campaign-referrals-container">
		<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table vf-my-referrals">			
			<?php if( $referred = vlogref_upvotes_get_user_referrals( array( 'page' => $page, 'limit' => 20 ) ) ) : //Check Referred Has Data ?>
				<thead>
					<tr>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php _e('Referred Campaign','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php _e('Online Since','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Total','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Total Referred','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php _e('Your Referrals','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php _e('Your Position','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="nobr"><?php _e('Views','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Status','vlog-referral');?></span></th>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Action','vlog-referral');?></span></th>
					</tr>
				</thead>
				<tbody class="wpv-loop js-wpv-loop">
					<?php foreach( $referred as $camp ) :
						$campaign 		= $camp['campaign'];
						$userid 		= $camp['referred_by'];
						$campaign_status = vlogref_campaign_status($campaign);
						$campaign_status_title = vlogref_campaign_status_title($campaign);
						$views 			= get_post_meta($campaign,'wpcf-campaign-view-count', true); 
						if( intval( $campaign_status ) == 2 ) :
							$total_donation	= vlogref_donations_get_campaign_total($campaign);
							$total			= vlogref_price($total_donation['total_amount']);
							$total_referred = vlogref_price(vlogref_donations_total_referred_amount($campaign));
							$user_referred 	= vlogref_price($camp['donation']);//vlogref_upvotes_user_referral_count($campaign, $userid);
							$user_position 	= vlogref_donations_user_rank($campaign);
							$campaign_goal 	= vlogref_price(vlogref_donations_campaign_goal($campaign));
							$winner			= vlogref_donations_get_campaign_winners($campaign);
							$total 			= '<span><span class="upvote-count-sc">'.$total.' / '.$campaign_goal.'</span></span>';
							$total_referred = '<span><span class="upvote-count-sc">'.$total_referred.' / '.$campaign_goal.'</span></span>';
							$view_link		= add_query_arg( 'view', 'donations', wc_get_endpoint_url('my-referrals').$campaign);
						else : //Else
							$total 			= vlogref_campaign_upvotes($campaign);
							$total_referred = vlogref_upvotes_total_referred_count($campaign);
							$user_referred 	= $camp['upvotes'];//vlogref_upvotes_user_referral_count($campaign, $userid);
							$user_position 	= vlogref_upvotes_user_rank($campaign);
							$campaign_goal 	= vlogref_upvotes_campaign_goal($campaign);
							$winner			= vlogref_upvotes_get_campaign_winners($campaign);
							//$winner_str 	= ( intval( $winner ) === $user_ID ) ? '<span class="c-owner" style="font-size: 10px; border: 1px solid brown; padding: 2px;">'.__('You won').'</span>' : '';
							$total 			= '<span><strong>↑</strong> <span class="upvote-count-sc">'.$total.' / '.$campaign_goal.'</span></span>';							
							$total_referred = '<span><strong>↑</strong> <span class="upvote-count-sc">'.$total_referred.' / '.$campaign_goal.'</span></span>';
							$view_link		= add_query_arg( 'view', 'upvotes', wc_get_endpoint_url('my-referrals').$campaign);
						endif; //Endif ?>
						<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php _e('Referred Campaign','vlog-referral');?>"><?php printf('<a href="%1$s" target="_blank">%2$s</a> %3$s', get_permalink($campaign), get_the_title($campaign), $winner_str);?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php _e('Online Since','vlog-referral');?>"><time><?php echo get_the_date('d/m/y', $campaign);?></time></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Total Upvotes','vlog-referral');?>"><?php echo $total;?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Total Referred Upvotes','vlog-referral');?>"><?php echo $total_referred;?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php _e('Your Referred Upvotes','vlog-referral');?>"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span><?php echo $user_referred.' / '.$campaign_goal;?></span></span></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php _e('Your Position','vlog-referral');?>"><span><i class="fas fa-users __web-inspector-hide-shortcut__" aria-hidden="true"></i> <?php echo '#'.$user_position;?></span></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php _e('Views','vlog-referral');?>"><span><i class="fas fa-signal" aria-hidden="true"></i> <?php echo $views;?></span></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Campaign Status','vlog-referral');?>"><span class="woocommerce-button button view <?php echo strtolower($campaign_status_title).' sfc-my-campaigns-status-'.strtolower($campaign_status_title);?>"><?php echo $campaign_status_title;?></span></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php _e('Actions','vlog-referral');?>"><?php printf('<a href="%1$s">%2$s</a>', $view_link, __('View','vlog-referral'));?></td>
						</tr>
					<?php endforeach; //Endforeach ?>
				</tbody>
			<?php else : //Else ?>
				<tbody class="wpv-loop js-wpv-loop">
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
						<td><?php _e('You\'ve not referred anyone yet, start referring to your friend and stand a chance to win surprising gifts.','vlog-referral');?></td>
					</tr>
				</tbody>
			<?php endif; //Endif ?>			
		</table>
	</div><!--/.vf-campaign-referrals-container-->
<?php include_once( VLOGREF_PLUGIN_PATH . '/includes/public/class-ref-paginator.php');
	$vloref_paginator  = new Vlogref_Paginator( "SELECT COUNT(upvoted) AS upvotes FROM ".VLOG_REFERRAL_TABLE." WHERE 1=1 AND upvoted=1 AND referred_by='$user_ID' GROUP BY campaign ORDER BY upvotes DESC;" );
	echo $vloref_paginator->createLinks();