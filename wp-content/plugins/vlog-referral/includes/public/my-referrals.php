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
	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php _e('Referred Campaign','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php _e('Online Since','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Total Upvotes','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Total Referred Upvotes','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php _e('Your Referred Upvotes','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php _e('Your Position','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="nobr"><?php _e('Views','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Status','vlog-referral');?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php _e('Action','vlog-referral');?></span></th>
			</tr>
		</thead>
		<tbody class="wpv-loop js-wpv-loop">
			<?php if( $referred = vlogref_get_user_referrals( array( 'page' => $page ) ) ) : //Check Referred Has Data
				foreach( $referred as $camp ) :
					$campaign 		= $camp['campaign'];
					$userid 		= $camp['referred_by'];
					$total_referred = vlogref_campaign_total_referral_count($campaign);
					$user_referred 	= vlogref_user_campaign_referral_count($campaign, $userid);
					$user_position 	= vlogref_user_position($campaign);
					$views 			= get_post_meta($campaign,'wpcf-campaign-view-count', true); 
					$winner			= get_post_meta($campaign, '_referral_winner', true);						
					$winner_str 	= ( intval( $winner ) === $user_ID ) ? '<span class="c-owner" style="font-size: 10px; border: 1px solid brown; padding: 2px;">'.__('You won').'</span>' : '';
					$total_upvotes 	= vlogref_campaign_upvotes($campaign);
					$campaign_goal 	= vlogref_campaign_upvotes_goal($campaign);
					$campaign_status= vlogref_campaign_status($campaign); ?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php _e('Referred Campaign','vlog-referral');?>"><?php printf('<a href="%1$s" target="_blank">%2$s</a> %3$s', get_permalink($campaign), get_the_title($campaign), $winner_str);?></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php _e('Online Since','vlog-referral');?>"><time><?php echo get_the_date('d/m/y', $campaign);?></time></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Total Upvotes','vlog-referral');?>"><span><strong>↑</strong> <span class="upvote-count-sc"><?php echo $total_upvotes.'/'.$campaign_goal;?></span></span></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Total Referred Upvotes','vlog-referral');?>"><span><strong>↑</strong> <span class="upvote-count-sc"><?php echo $total_referred.'/'.$campaign_goal;?></span></span></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php _e('Your Referred Upvotes','vlog-referral');?>"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"></span><?php echo $user_referred.'/'.$campaign_goal;?></span></span></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php _e('Your Position','vlog-referral');?>"><span><i class="fas fa-users __web-inspector-hide-shortcut__" aria-hidden="true"></i> <?php echo '#'.$user_position;?></span></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php _e('Views','vlog-referral');?>"><span><i class="fas fa-signal" aria-hidden="true"></i> <?php echo $views;?></span></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php _e('Campaign Status','vlog-referral');?>"><a href="'.get_permalink($campaign).'" target="_blank"><span class="woocommerce-button button view <?php echo strtolower($campaign_status).' sfc-my-campaigns-status-'.strtolower($campaign_status);?>"><?php echo $campaign_status;?></span></a></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php _e('Actions','vlog-referral');?>"><?php printf('<a href="%1$s">%2$s</a>', wc_get_endpoint_url('my-referrals').$campaign, __('View','vlog-referral'));?></td>
					</tr>
			<?php endforeach; //Endforeach						
			else : //Else ?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
					<td colspan="9"><?php _e('No referrals found, start referring to your friend and stand a chance to win prize.','vlog-referral');?></td>
				</tr>
		<?php endif; //Endif ?>
		</tbody>
	</table>
<?php include_once( VLOGREF_PLUGIN_PATH . '/includes/public/class-ref-paginator.php');
	$vloref_paginator  = new Vlogref_Paginator( "SELECT COUNT(upvoted) AS upvotes FROM ".VLOG_REFERRAL_TABLE." WHERE 1=1 AND upvoted=1 AND referred_by='$user_ID' GROUP BY campaign ORDER BY upvotes DESC;" );
	echo $vloref_paginator->createLinks();