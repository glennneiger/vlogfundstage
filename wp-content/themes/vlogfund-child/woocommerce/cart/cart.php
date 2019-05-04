<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );  ?>

<form class="woocommerce-cart-form sfc-checkout-billing-container" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">

		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">



                        <td class="sfc-cart-item">




<div class="sfc-campaign-archive-post">
													<?php
														// @codingStandardsIgnoreLine
														echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
															'<a href="%s" class="remove" data-product_id="%s" data-product_sku="%s">&times;</a>',
															esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
															__( 'Remove this item', 'woocommerce' ),
															esc_attr( $product_id ),
															esc_attr( $_product->get_sku() )
														), $cart_item_key );
													?>

													<div class="sfc-campaign-images">
            <div class="sfc-campaign-image sfc-campaign-single-image">
              <img align="top" class="sfc-campaign-image-left" src="<?php echo get_post_meta( $product_id, 'wpcf-collaborator-1-image', true ); ?>">
            </div>
            <div class="sfc-campaign-image sfc-campaign-single-image">
              <img align="top" class="sfc-campaign-image-right" src="<?php echo get_post_meta( $product_id, 'wpcf-collaborator-2-image', true ); ?>">
            </div>
          </div>



                            	<?php
								if ( ! $product_permalink ) {
									echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
								} else {
									echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<h3 class="sfc-campaign-archive-post-title" href="%s">%s</h3>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
								}
								// Meta data
								echo wc_get_formatted_cart_item_data( $cart_item );
							?>

</div>



                        </td>

                        <!--new--->

                        <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                         <?php if( WC_Name_Your_Price_Helpers::is_nyp( $product_id ) /*|| WC_Name_Your_Price_Helpers::has_nyp( $product_id )*/ ){ ?>


							<h4 class="sfc-checkout-review-amount-title">Donation Amount</h4>
							<div class="sfc-campaign-choose-amount-wrapper">
			<input type="radio" class="product_custom_price" name="donation_amount_<?php echo $product_id;?>" id="donation_amount_<?php echo $product_id;?>_30" value="30" data-id="<?php echo $product_id;?>" <?php checked($_product->get_price(), 30);?>>
            <label for="donation_amount_<?php echo $product_id;?>_30" class="sfc-campaign-amount-button"><span><?php echo wc_price(30, array('decimals' => 0));?></span></label>
			
            <input type="radio" class="product_custom_price" name="donation_amount_<?php echo $product_id;?>" id="donation_amount_<?php echo $product_id;?>_20" value="20" data-id="<?php echo $product_id;?>" <?php checked($_product->get_price(), 20);?>>
            <label for="donation_amount_<?php echo $product_id;?>_20" class="sfc-campaign-amount-button"><span><?php echo wc_price(20, array('decimals' => 0));?></span></label>

            <input type="radio" class="product_custom_price" name="donation_amount_<?php echo $product_id;?>" id="donation_amount_<?php echo $product_id;?>_10" value="10" data-id="<?php echo $product_id;?>" <?php checked($_product->get_price(), 10);?>>
            <label for="donation_amount_<?php echo $product_id;?>_10" class="sfc-campaign-amount-button"><span><?php echo wc_price(10, array('decimals' => 0));?></span></label>

            <?php /*<input type="radio" class="product_custom_price" name="donation_amount_<?php echo $product_id;?>" id="donation_amount_<?php echo $product_id;?>_5" value="5" data-id="<?php echo $product_id;?>" <?php checked($_product->get_price(), 5);?>>
            <label for="donation_amount_<?php echo $product_id;?>_5" class="sfc-campaign-amount-button"><span><?php echo wc_price(5, array('decimals' => 0));?></span></label>*/?>

            <input type="radio" class="product_custom_price" name="donation_amount_<?php echo $product_id;?>" id="donation_amount_<?php echo $product_id;?>_3" value="3" data-id="<?php echo $product_id;?>" <?php checked($_product->get_price(), 3);?>>
            <label for="donation_amount_<?php echo $product_id;?>_3" class="sfc-campaign-amount-button"><span><?php echo wc_price(3, array('decimals' => 0));?></span></label>




                           <div class="sfc-checkout-billing-donation-amount">
                           <span class="product-custom-price-currency"><?php echo get_woocommerce_currency_symbol();?></span>
                           <input type="text" class="product_custom_price sfc-checkout-billing-donation-other" data-id="<?php echo $_product->get_id();?>" value="<?php echo $_product->get_price();?>" placeholder="other amount"/>



                           </div>
                           </div>
							 <?php } else {
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							} ?>

						</td>

                        <!--end-new--->


					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>



			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>


<?php do_action( 'woocommerce_after_cart' ); ?>
