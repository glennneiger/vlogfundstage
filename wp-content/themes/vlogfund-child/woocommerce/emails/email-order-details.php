<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); 

$organization = get_post_meta($order->get_id(), 'billing_cause', true); ?>

<p style="font-size: 18px !important; text-align: center;"><strong style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif">Contribution #<?php echo $order->get_order_number();?> (<?php echo wc_format_datetime( $order->get_date_created() );?>)</strong></p>
<div>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family:open sans,helvetica neue,helvetica,arial,sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" style="text-align: left;"><?php esc_html_e('Campaign', 'woocommerce');?></th>
				<th class="td" style="text-align: left;"><?php esc_html_e('Amount', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				echo wc_get_email_order_items( $order, array( // WPCS: XSS ok.
					'show_sku'      => $sent_to_admin,
					'show_image'    => false,
					'image_size'    => array( 32, 32 ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
				) );
			?>
			<tr>
				<th class="td" style="text-align: left;"><?php esc_html_e('Payment Method', 'woocommerce');?></th>
				<td class="td" style="text-align: left;"><?php echo $order->get_payment_method();?></td>
			</tr>
			<tr>
				<th class="td" style="text-align: left;"><?php esc_html_e('Charity', 'woocommerce'); ?></th>
				<td class="td" style="text-align: left;"><?php echo $organization ? get_the_title($organization) : '&mdash;';?></td>
			</tr>
	  </tbody>
	</table>
</div>
<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
