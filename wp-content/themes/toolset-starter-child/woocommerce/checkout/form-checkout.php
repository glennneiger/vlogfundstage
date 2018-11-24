<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
f
do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>




<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
<div class="sfc-checkout-billing-container">
                <div class="sfc-checkout-billing">





    <div class="sfc-checkout-billing-item">

    <div class="sfc-checkout-billing-profile-wrapper">
                            <div class="sfc-checkout-billing-profile-background">
                                <?php if ( is_user_logged_in() ) {
                                echo '<h3><i class="fa fa-address-card-o"></i></h3>';
    }
else {
    	echo '<h3>Already have an account? <a href="#login" class="sf-checkout-login-link">Login</a></h3>';

 }

 ?>
        </div>
                            <!--<div class="sfc-checkout-billing-description"><h3>Billing Details</h3></div>-->
                            <div class="sfc-checkout-billing-input-fields">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>


                 </div>
                        </div><!--sfc-checkout-billing-profile-wrapper-->


    </div><!--item-->

    <div class="sfc-checkout-billing-item">

                        <div class="sfc-checkout-billing-profile-wrapper">
                            <div class="sfc-checkout-billing-profile-background"><h3><i class="fa fa-credit-card"></i></h3>
                            </div>

                            <!--<div class="sfc-checkout-billing-description"><h3>Payment Details</h3></div>-->
                            <div class="sfc-checkout-billing-input-fields">


	<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>




          <!--<div class="sf-cart-yt-video-thumbnails">
                            <div class="sf-cart-yt-video-thumbnail" data-id="<?php echo get_post_meta( $product_id, 'wpcf-youtube-video-id-collaborator-1', true ); ?>"></div>
                            <div class="sf-cart-yt-video-thumbnail" data-id="<?php echo get_post_meta( $product_id, 'wpcf-youtube-video-id-collaborator-2', true ); ?>"></div>
                                        </div>   -->






	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
        <span class="sfc-checkout-terms">By continuing, you agree to our <a href="/terms" target="_blank">Terms of Use</a>
            Our <a href="/terms" target="_blank">Privacy Policy</a>, and our <a href="/terms" target="_blank">Refund Policy</a></span>


                                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

                                </div>
                                 </div><!--sfc-checkout-billing-profile-wrapper-->

                         <div class="sfc-checkout-billing-profile-wrapper">
                             <div class="sfc-checkout-billing-sticky">
                            <div><h3><i class="fa fa-question"></i> Questions</h3></div>

                            <div class="contain">
                                <div class="sfc-checkout-billing-faq-accordion active to-animate">
                                    <h5>Is my online contribution safe? <i class="fa fa-angle-down"></i></h5>
                                    <div class="sfc-faq-body" style="display: block;">
                                        <p>All information during data transmission is encrypted.
Our website ensures to precede all requests via “https” rather than “http”, which provides an extra layer of security. This is also symbolized in the corner of the web browser with a padlock symbol. This verifies that the page requesting your credit card information is secure.
 </p>
                                        <p>Read more of our FAQ <a href="/faq" target="_blank">here</a></p>
                                    </div>
                                </div>

                                <div class="sfc-checkout-billing-faq-accordion active to-animate">
                                    <h5>Will I receive a receipt for my contribution? <i class="fa fa-angle-down"></i></h5>
                                    <div class="sfc-faq-body" style="display: block;">
                                        <p><em>Please note: Your credit card will be charged immediately if your transaction was successful you’ll receive an automatic confirmation email.</em><br> The email will be sent to the email address provided during the checkout process. Make sure to check your spam folder if you can’t find the e-mail after your transaction.
We will not be issuing any receipts. If you choose to deduct your donation, you can use the confirmation email we sent you if your transaction was successful. Often no additional receipt is required for donations of $250 or less. Please email us at …. and we will issue a receipt if your donation was greater than or equal to $250.
</p>
                                        <p>Read more of our FAQ <a href="/faq" target="_blank">here</a></p>
                                    </div>
                                </div>

                                <!--<div class="sfc-checkout-billing-faq-accordion active to-animate">
                                    <h5>Are my donations entitled to tax deductions? <i class="fa fa-angle-down"></i></h5>
                                    <div class="sfc-faq-body" style="display: block;">
                                        <p>Far far away, behind the word mountains, far from the Vokalia and
                                            Consonantia, there </p>
                                        <p>Read more of our FAQ <a href="/faq" target="_blank">here</a></p>
                                    </div>
                                </div>-->
                                <!--<img src="" class="sfc-checkout-billing-ssl" />-->
                            </div>
                        </div>
                    </div><!--item-->

                             </div><!--sfc-checkout-billing-profile-wrapper-->





                </div><!--checkout-billing-->
            </div><!--container-->
    </form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
