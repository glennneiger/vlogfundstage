<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="woocommerce-order">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

    <!--<?php
		$order = new WC_Order( $order->id );
		$items = $order->get_items();
		$product_id	= false;
		foreach ( $items as $item ) {
    		$product_id = $item['product_id'];
		}
		?>-->

    <?php $ordered_product = array_shift( $order->get_items() );
			$product = get_post( $ordered_product['product_id'] );
			$excerpt = $product->post_excerpt ? $product->post_excerpt : get_bloginfo('description'); ?>



<h2 style="text-align: center;">Donation complete </h2>

<div class="sfc-checkout-progress-bar">
    <div class="sfc-checkout-progress-bar-dot sfc-checkout-progress-bar-active">1</div>
    <div class="sfc-checkout-progress-bar-bar sfc-checkout-progress-bar-active"></div>

    <div class="sfc-checkout-progress-bar-dot sfc-checkout-progress-bar-active">2</div>
    <div class="sfc-checkout-progress-bar-bar sfc-checkout-progress-bar-active"></div>

    <div class="sfc-checkout-progress-bar-dot sfc-checkout-progress-bar-active">3</div>

</div>

<div class="sfc-checkout-progress-bar-titles">
    <div class="sfc-checkout-progress-bar-title">Choose a charity</div>
    <div class="sfc-checkout-progress-bar-title">Billing details</div>
    <div class="sfc-checkout-progress-bar-title">Thank you!</div>
</div>


<div class="sfc-checkout-thankyou">

    <div class="sfc-checkout-thankyou-header" style="max-width:500px;margin: auto;">
        <h1 class="sfc-checkout-thankyou-title">Thank you for your donation!</h1>

				<?php if( is_user_logged_in() ) { ?>
				<div class="sfc-thank-you-conf-msg"><span>If your donation was successful you will receive a <strong>confirmation email</strong> to the email address you provided.</span><br>
				Your donation will also show up in your personal account if you created an account with us.<br>
				<a href="<?php echo $order->get_view_order_url();?>" style="text-decoration: underline">Details</a>
				</div>
        <?php } else { ?>
        <div class="sfc-thank-you-conf-msg"><span><i class="fa fa-heart"></i> Thank you for being part of our community and bringing new ideas to life!<br> If your contribution was successful you will receive an immediate, automatic <strong>confirmation email</strong> to the email address you provided. </span> </div>
				<?php } ?>

    </div>




        <div class="sfc-checkout-thankyou-row sfc-campaign-archive-post">


					<span class="sfc-campaign-archive-post-status-donate"><span class="sfc-campaign-archive-post-status-dollar-sign">$</span></span>



								<div class="sfc-campaign-images">
								<div class="sfc-campaign-image sfc-campaign-single-image">
								 <img align="top" class="sfc-campaign-image-left"
											src="<?php echo get_post_meta( $product_id, 'wpcf-collaborator-1-image', true ); ?>"/>
						    </div>
						 <div class="sfc-campaign-image sfc-campaign-single-image">
								 <img align="top" class="sfc-campaign-image-right"
											src="<?php echo get_post_meta( $product_id, 'wpcf-collaborator-2-image', true ); ?>"/>
						 </div>
					 </div>

                <h3 class="sfc-campaign-archive-post-title" style="text-align:center;"><?php echo get_post_meta( $product_id, 'wpcf-collaborator-1', true ); ?> + <?php echo get_post_meta( $product_id, 'wpcf-collaborator-2', true ); ?></h3>




            <div class="sfc-checkout-thankyou-share">
               <p class="thank_you_share_title" style="text-align:center;">Share this collaboration now with your friends!</p>
					<!--Sharing buttons-->
					<ul class="sf-sharing-buttons-inline">
						<?php if( vlogref_is_referral_enable( $product->ID ) ) : //Permalink
							$permalink = do_shortcode( '[vlog_referral_url id="'.$product->ID.'"]' );
						else : //Else Normal Permalink
							$permalink = get_permalink( $product->ID );
						endif; //Endif ?>
						<li class="sf-sharing-button-facebook"><a id="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( $permalink );?>" target="_blank"><i id="facebook" class="fab fa-facebook"></i></a></li>
						<li class="sf-sharing-button-twitter"><a id="twitter" href="https://twitter.com/intent/tweet?text=<?php echo substr($excerpt, 0, 279);?>" target="_blank"><i id="twitter" class="fab fa-twitter"></i></a></li>
						<li class="sf-sharing-button-whatsapp"><a id="whatsapp" href="whatsapp://send?text=<?php echo $product->post_title . ' | ' . $excerpt . ' | ' . $permalink;?>" data-action="share/whatsapp/share" target="_blank"><i id="whatsapp" class="fab fa-whatsapp"></i></a></li>
						<li class="sf-sharing-button-reddit"><a id="reddit" href="http://www.reddit.com/submit?url=<?php echo $permalink;?>" target="_blank"><i id="reddit" class="fab fa-reddit"></i></a></li>
						<li class="sf-sharing-button-mail"><a id="mail" href="mailto:?subject= Let's make this YouTube collaboration happen&amp;body=Check out this YouTube Collaboration <?php echo $permalink;?>"><i id="mail" class="fa fa-envelope"></i></a></li>
						<li class="sf-sharing-button-messenger messenger-mobile"><a id="messenger" href="fb-messenger://share/?link=<?php echo urlencode( $permalink );?>&app_id=181038895828102" target="_blank"><i id="whatsapp" class="fab fa-facebook-messenger"></i></a></li>
						<li class="sf-sharing-button-messenger messenger-desktop"><a id="messenger" href="http://www.facebook.com/dialog/send?app_id=181038895828102&link=<?php echo urlencode( $permalink );?>&redirect_uri=<?php echo esc_url( $permalink ); ?>" target="_blank"><i id="whatsapp" class="fab fa-facebook-messenger"></i></a></li>
					</ul>
					<!--Sharing buttons-->

					<!--<h3 class="vf-referral-phase-title"><?php _e('Get your friends to donate with this unique URL:');?></h3>-->
					<input type="text" class="sf-mc-email vf-referral-url" value="<?php echo do_shortcode('[vlog_referral_url id="'.$product->ID.'"]');?>" style="background: #eee;" readonly="readonly">

            </div>


        </div>



		<?php $prizes = vlogref_donations_referral_prizes( $product->ID );
		if( vlogref_is_referral_enable( $product->ID ) && !empty( $prizes ) ) : //On-going ?>
		<div style="max-width:600px;margin:auto;padding:0 10px;">
		<h2 class="vf-ref-prize-title"><?php _e('Share this campaign to win awesome prizes');?></h2>
		<p class="vf-ref-prize-desc"><?php _e('The top 3 ambassadors, who bring in the most donation will win!');?></p>
			<div class="vf-ref-prizes">
				<?php $prize_counter = 1;
				foreach( $prizes as $prize ) : //List Prizes
					$prize_data = vlogref_donations_prize_details( $prize ); ?>
					<div class="vf-ref-prize-item prize-<?php echo $prize_counter;?>">
						<?php //Image of Prize
							echo !empty( $prize_data['img'] ) 	? '<img class="vf-ref-prize-img" src="'.esc_url($prize_data['img']).'" alt="'.$prize_data['title'].'"/>' : '<i class="fas fa-trophy"></i>';
							//Title of Prize
							echo !empty( $prize_data['title'] ) ? sprintf('<h4 class="vf-ref-prize-title"><strong>%1$s</strong></h4>', $prize_data['title'] ) : '';
							//Description of Prize
							//echo !empty( $prize_data['desc'] ) 	? sprintf('<p class="description">%1$s</p>', $prize_data['desc'] ) : '';
						?>
					</div><!--/.vf-referral-prize-->
				<?php $prize_counter++;
				endforeach; //Endforeach ?>
			</div><!--/.vf-ref-prizes-->
		</div>
		<?php endif; //Endif ?>

</div>


		<?php endif; ?>




	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you very much. Your contribution has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>



<!-- <span class="hide"><a href="#share" class="thank-you-share">Share</a></span> -->


<!--Thank you share popup-->

<!-- <div id="share" class="sf-popup">
    <div class="sf-popup-container">
      <span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>

        <div class="sf-popup-content">

          <h2>Get the word out</h2>
            <h3>Share this collab with your friends</h3>


						<ul class="sf-sharing-buttons-inline">
						<li class="sf-sharing-button-facebook"><a id="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink( $product->ID ) );?>" target="_blank"><i id="facebook" class="fab fa-facebook"></i> Facebook</a></li>
						<li class="sf-sharing-button-twitter"><a id="twitter" href="https://twitter.com/intent/tweet?text=<?php echo substr($excerpt, 0, 279);?>" target="_blank"><i id="twitter" class="fab fa-twitter"></i> Twitter</a></li>
						<li class="sf-sharing-button-google"><a id="google" href="https://plus.google.com/share?url=<?php echo urlencode( get_permalink( $product->ID ) );?>" target="_blank"><i id="google" class="fab fa-google"></i> Google</a></li>
						<li class="sf-sharing-button-whatsapp"><a id="whatsapp" href="whatsapp://send?text=<?php echo $product->post_title . ' | ' . $excerpt . ' | ' . get_permalink( $product->ID );?>" data-action="share/whatsapp/share" target="_blank"><i id="whatsapp" class="fab fa-whatsapp"></i> Whatsapp</a></li>
						<li class="sf-sharing-button-reddit"><a id="reddit" href="http://www.reddit.com/submit?url=<?php echo get_permalink( $product_id );?>" target="_blank"><i id="reddit" class="fab fa-reddit"></i> Reddit</a></li>
						<li class="sf-sharing-button-mail"><a id="mail" href="mailto:?subject= Let's make this YouTube collaboration happen&amp;body=Check out this YouTube Collaboration <?php echo get_permalink( $product_id );?>"><i id="mail" class="fa fa-envelope"></i> Mail</a></li>
						<li class="sf-sharing-button-messenger messenger-mobile"><a id="messenger" href="fb-messenger://share/?link=<?php echo urlencode( get_permalink( $product->ID ) );?>&app_id=181038895828102" target="_blank">
						<i id="whatsapp" class="fab fa-facebook-messenger"></i> Messenger</a></li>
						<li class="sf-sharing-button-messenger messenger-desktop"><a id="messenger" href="http://www.facebook.com/dialog/send?app_id=181038895828102&link=<?php echo urlencode( get_permalink( $product->ID ) );?>&redirect_uri=<?php echo esc_url( get_permalink() ); ?>" target="_blank">
						<i id="whatsapp" class="fab fa-facebook-messenger"></i> Messenger</a></li>
						</ul>


        </div>
    </div>
</div> -->
