<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
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
	exit; // Exit if accessed directly.
}

?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

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
								<?php /* translators: %s: Customer first name */ ?>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></span></p>
								<?php /* translators: %s: Store name */ ?>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php printf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'woocommerce' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></span></p>
								<?php /* translators: %s Customer username */ ?>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php printf( esc_html__( 'Username: %s', 'woocommerce' ), esc_html( $user_login ) ); ?></span></p>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php esc_html_e( 'If you didn\'t make this request, just ignore this email. If you\'d like to proceed:', 'woocommerce' ); ?></span></p>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif">
									<a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>"><?php // phpcs:ignore ?>
										<?php esc_html_e( 'Click here to reset your password', 'woocommerce' ); ?>
									</a>
								</span></p>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php esc_html_e( 'Thanks for reading.', 'woocommerce' ); ?></span></p>
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

<?php do_action( 'woocommerce_email_footer', $email ); ?>
