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

    <!--new-->

<h2 style="text-align: center;">contribution complete </h2>

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

    <div class="sfc-checkout-thankyou-header">
        <h1 class="sfc-checkout-thankyou-title">Thank you for your contribution!</h1>
    </div>

    <div class="sfc-checkout-thankyou-container">


        <div class="sfc-checkout-thankyou-row">


            <div class="sfc-checkout-thankyyou-cammpaign">





                <div class="sfc-checkout-thankyou-image-container">



                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink( $product_id );?>"
                       target="_blank">



                        <!--<div class="sfc-checkout-thankyou-img-left image-left-1"></div>
                        <div class="sfc-checkout-thankyou-img-right image-right-1"></div>--->

                       <div class="sf-campaign-popular-yt-video-thumbnails">
                            <div class="sf-campaign-popular-yt-video-thumbnail" data-id="<?php echo get_post_meta( $product_id, 'wpcf-youtube-video-id-collaborator-1', true ); ?>"></div>
                            <div class="sf-campaign-popular-yt-video-thumbnail" data-id="<?php echo get_post_meta( $product_id, 'wpcf-youtube-video-id-collaborator-2', true ); ?>"></div>
                                        </div>



                    </a>
                </div>




                <h2><?php echo get_post_meta( $product_id, 'wpcf-collaborator-1', true ); ?> + <?php echo get_post_meta( $product_id, 'wpcf-collaborator-2', true ); ?></h2>

            </div>


            <div class="sfc-checkout-thankyou-share">

							<!--Sharing buttons-->
							<ul class="sf-sharing-buttons-inline">
							<li class="sf-sharing-button-facebook"><a id="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink( $product->ID ) );?>" target="_blank"><i id="facebook" class="fab fa-facebook"></i></a></li>
							<li class="sf-sharing-button-twitter"><a id="twitter" href="https://twitter.com/intent/tweet?text=<?php echo substr($excerpt, 0, 279);?>" target="_blank"><i id="twitter" class="fab fa-twitter"></i></a></li>
							<li class="sf-sharing-button-google"><a id="google" href="https://plus.google.com/share?url=<?php echo urlencode( get_permalink( $product->ID ) );?>" target="_blank"><i id="google" class="fab fa-google"></i> </a></li>
							<li class="sf-sharing-button-whatsapp"><a id="whatsapp" href="whatsapp://send?text=<?php echo $product->post_title . ' | ' . $excerpt . ' | ' . get_permalink( $product->ID );?>" data-action="share/whatsapp/share" target="_blank"><i id="whatsapp" class="fab fa-whatsapp"></i></a></li>
							<li class="sf-sharing-button-reddit"><a id="reddit" href="http://www.reddit.com/submit?url=<?php echo get_permalink( $product_id );?>" target="_blank"><i id="reddit" class="fab fa-reddit"></i></a></li>
							<li class="sf-sharing-button-mail"><a id="mail" href="mailto:?subject= Let's make this YouTube collaboration happen&amp;body=Check out this YouTube Collaboration <?php echo get_permalink( $product_id );?>"><i id="mail" class="fa fa-envelope"></i></a></li>
							<li class="sf-sharing-button-messenger messenger-mobile"><a id="messenger" href="fb-messenger://share/?link=<?php echo urlencode( get_permalink( $product->ID ) );?>&app_id=181038895828102" target="_blank">
							<i id="whatsapp" class="fab fa-facebook-messenger"></i></a></li>
							<li class="sf-sharing-button-messenger messenger-desktop"><a id="messenger" href="http://www.facebook.com/dialog/send?app_id=181038895828102&link=<?php echo urlencode( get_permalink( $product->ID ) );?>&redirect_uri=<?php echo esc_url( get_permalink() ); ?>" target="_blank">
							<i id="whatsapp" class="fab fa-facebook-messenger"></i></a></li>
							</ul>
							<!--Sharing buttons-->


                <h3 class="thank_you_share_title"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink( $product_id );?>">Share this collaboration now with your friends!</a>
                </h3>


            </div>
        </div>
        <?php if( is_user_logged_in() ) { ?>
        <div><span class="fc-checkout-thank-you-confirmation-msg"><i class="fa fa-heart"></i> Thank you for being part of our community and bringing new ideas to life!<br> You’ll receive and automatic <strong>confirmation email</strong> if your contribution is successful.</span> </div><br>
        Your contribution will also show up in your personal account if you created an account with us.<br>
        <a href="<?php echo $order->get_view_order_url();?>" style="text-decoration: underline">Details</a>

        <?php } else { ?>
        <div><span class="fc-checkout-thank-you-confirmation-msg"><i class="fa fa-heart"></i> Thank you for being part of our community and bringing new ideas to life!<br> If your contribution was successful you will receive an immediate, automatic <strong>confirmation email</strong> to the email address you provided. </span> </div>
				<?php } ?>
    </div>
</div>

    <!--end-new-->


    <div class="sfc-campaign-widget-container">
        <div class="sfc-campaign-widget-heading sfc-campaign-widget-heading-controls">
                        <a href="/campaign-search/"><h2>Other likeminded people contributed to the following <b class="sfc-campaign-section-title">Collaborations</b></h2></a>
                        <div class="sfc-campaign-widget-heading-see-more">
                            <button class="sfc-campaign-widget-heading-see-more-button"><a href="/campaign-search/">See all</a></button>
                        </div>
                    </div>
    <?php echo do_shortcode ('[wpv-view name="campaign-search" view_display="layout" limit="4" orderby="field-total_sales" order="desc" orderby_second="post_date" order_second="desc" funding="2"]') ?>
    </div>







			<?php /*<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php _e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php _e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php _e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php _e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php _e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>

			</ul>*/?>

		<?php endif; ?>

		<?php //do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php //do_action( 'woocommerce_thankyou', $order->get_id() ); ?>


	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you very much. Your contribution has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>



<span class="hide"><a href="#share" class="thank-you-share">Share</a></span>


<!--Thank you share popup-->

<div id="share" class="sf-popup">
    <div class="sf-popup-container">
      <span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>
      <!--content-login--->
        <div class="sf-popup-content">
          <!---->
          <h2>Get the word out</h2>
            <h3>Share this collab with your friends</h3>
            <!--<p style="color:#999;">in additional contributions and engagements</p>-->

						<!--Sharing buttons-->
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
						<!--Sharing buttons-->
          <!---->
        </div>
    </div>
</div>
