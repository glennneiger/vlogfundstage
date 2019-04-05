<?php
/**
 * Admin cancelled order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-cancelled-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
*/
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
	<tbody class="mcnTextBlockOuter">
		<tr>
			<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
				<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
				<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
					<tbody>
						<tr>
							<td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
								<h2 style="text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php echo $email->get_subject();?></span></h2>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php echo $email->get_heading();?></span></p>
								<?php /* translators: %1$s: Customer full name. %2$s: Order numer */ ?>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php printf( esc_html__( 'Alas. Just to let you know &mdash; %1$s has cancelled order #%2$s:', 'woocommerce' ), esc_html( $order->get_formatted_billing_full_name() ), esc_html( $order->get_order_number() ) ); ?></span></p>
								<br>
								<?php
								
								/*
								 * @hooked WC_Emails::order_details() Shows the order details table.
								 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
								 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
								 * @since 2.5.0
								 */
								do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
								
								/*
								 * @hooked WC_Emails::order_meta() Shows order meta data.
								 */
								do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
								
								/*
								 * @hooked WC_Emails::customer_details() Shows customer details
								 * @hooked WC_Emails::email_address() Shows email address
								 */
								do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
								?>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php esc_html_e( 'Thanks for reading.', 'woocommerce' ); ?></span></p>
								<br>
							</td>
						</tr>
					</tbody>
				</table>
				<!--[if mso]>
				</td>
				<![endif]-->
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
			</td>
		</tr>
	</tbody>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
	<tbody class="mcnDividerBlockOuter">
		<tr>
			<td class="mcnDividerBlockInner" style="min-width: 100%; padding: 18px 18px 0px;">
				<table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;">
					<tbody><tr><td><span></span></td></tr></tbody>
				</table>
			<!--
			<td class="mcnDividerBlockInner" style="padding: 18px;">
			<hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
			-->
			</td>
		</tr>
	</tbody>
</table>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
