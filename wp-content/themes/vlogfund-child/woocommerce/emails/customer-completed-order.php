<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
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

//Get Purchased Campaign
$campaign = '';
foreach ( $order->get_items() as $item_id => $item ) :
	$product = $item->get_product();
	if( $product ) : 
		$campaign = $product->get_id(); 
		break; 
	endif;
endforeach;
$campaign_link = get_permalink($campaign);
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
								<h2 style="text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif">Thank you for your donation</span></h2>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?><br> your donation has been marked complete on our side.</span></p>
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
									//do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
								?>								
								<br>
								<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif">If we raise enough money then the collaboration happens and the funds get released to all the charities and people who really need it. Else we will refund the money back into your account. So please make sure to share the collab with your friends.</span></p>
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
					<tbody>
						<tr>
							<td><span></span></td>
						</tr>
					</tbody>
				</table>
			<!--
			<td class="mcnDividerBlockInner" style="padding: 18px;">
			<hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
			-->
			</td>
		</tr>
	</tbody>
</table>
<!--share-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnShareBlock" style="min-width:100%;">
	<tbody class="mcnShareBlockOuter">
		<tr>
			<td valign="top" style="padding:9px" class="mcnShareBlockInner">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnShareContentContainer" style="min-width:100%;">
					<tbody>
						<tr>
							<td align="center" style="padding-top:0; padding-left:9px; padding-bottom:0; padding-right:9px;">
								<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;" class="mcnShareContent">
									<tbody>
										<tr>
											<td align="center" valign="top" class="mcnShareContentItemContainer" style="padding-top:9px; padding-right:9px; padding-left:9px;">
												<table align="center" border="0" cellpadding="0" cellspacing="0">
													<tbody>
														<tr>
															<td align="left" valign="top">
																<!--[if mso]>
																<table align="center" border="0" cellspacing="0" cellpadding="0">
																<tr>
																<![endif]-->
																
																<!--[if mso]>
																<td align="center" valign="top">
																<![endif]-->
																<table align="left" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td valign="top" style="padding-right:9px; padding-bottom:9px;" class="mcnShareContentItemContainer">
																				<table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;background-color: #4267b2;border: 4px solid #4267b2;">
																					<tbody>
																						<tr>
																							<td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">
																								<table align="left" border="0" cellpadding="0" cellspacing="0" width="">
																									<tbody>
																										<tr>
																											<td align="center" valign="middle" width="24" class="mcnShareIconContent">
																												<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($campaign_link);?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/outline-light-facebook-48.png" style="display:block;" height="24" width="24" class=""></a>
																											</td>
																											<td align="left" valign="middle" class="mcnShareTextContent" style="padding-left:5px;">
																												<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($campaign_link);?>" target="" style="color: #FFFFFF;font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif;font-size: 12px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;">Share</a>
																											</td>
																										</tr>
																									</tbody>
																								</table>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
																</td>
																<![endif]-->
																
																<!--[if mso]>
																<td align="center" valign="top">
																<![endif]-->
																<table align="left" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td valign="top" style="padding-right:9px; padding-bottom:9px;" class="mcnShareContentItemContainer">
																				<table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;background-color: #1da1f2;border: 4px solid #1da1f2;">
																					<tbody>
																						<tr>
																							<td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">
																								<table align="left" border="0" cellpadding="0" cellspacing="0" width="">
																									<tbody>
																										<tr>
																											<td align="center" valign="middle" width="24" class="mcnShareIconContent">
																												<a href="http://twitter.com/intent/tweet?text=<?php echo get_the_title($campaign);?>: <?php echo urlencode($campaign_link);?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/outline-light-twitter-48.png" style="display:block;" height="24" width="24" class=""></a>
																											</td>
																											<td align="left" valign="middle" class="mcnShareTextContent" style="padding-left:5px;">
																												<a href="http://twitter.com/intent/tweet?text=<?php echo get_the_title($campaign);?>: <?php echo urlencode($campaign_link);?>" target="" style="color: #FFFFFF;font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif;font-size: 12px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;">Tweet</a>
																											</td>
																										</tr>
																									</tbody>
																								</table>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
																</td>
																<![endif]-->
																<!--[if mso]>
																<td align="center" valign="top">
																<![endif]-->
																<table align="left" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td valign="top" style="padding-right:9px; padding-bottom:9px;" class="mcnShareContentItemContainer">
																				<table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;background-color: #25D366;border: 4px solid #25D366;">
																					<tbody>
																						<tr>
																							<td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">
																								<table align="left" border="0" cellpadding="0" cellspacing="0" width="">
																									<tbody>
																										<tr>
																											<td align="center" valign="middle" width="24" class="mcnShareIconContent">
																												<a href="whatsapp://send?text=<?php echo get_the_title($campaign);?> <?php echo urlencode($campaign_link);?>" data-action="share/whatsapp/share" target="_blank"><img src="https://gallery.mailchimp.com/144e2b841120da1b2d05b4e05/images/36656994-960a-4e92-9c4b-37e1ac9c8a78.png" style="display:block;" height="24" width="24" class=""></a>
																											</td>
																											<td align="left" valign="middle" class="mcnShareTextContent" style="padding-left:5px;">
																												<a href="whatsapp://send?text=<?php echo get_the_title($campaign);?> <?php echo urlencode($campaign_link);?>" data-action="share/whatsapp/share" target="" style="color: #FFFFFF;font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif;font-size: 12px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;">Whatsapp</a>
																											</td>
																										</tr>
																									</tbody>
																								</table>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
																</td>
																<![endif]-->
																
																<!--[if mso]>
																<td align="center" valign="top">
																<![endif]-->
																<table align="left" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td valign="top" style="padding-right:9px; padding-bottom:9px;" class="mcnShareContentItemContainer">
																				<table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;background-color: #ff4500;border: 4px solid #ff4500;">
																					<tbody>
																						<tr>
																							<td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">
																								<table align="left" border="0" cellpadding="0" cellspacing="0" width="">
																									<tbody>
																										<tr>
																											<td align="center" valign="middle" width="24" class="mcnShareIconContent">
																												<a href="https://www.reddit.com/submit?url=<?php echo urlencode($campaign_link);?>" target="_blank"><img src="https://gallery.mailchimp.com/144e2b841120da1b2d05b4e05/images/762ef7ba-832e-45c2-a7c8-88f00844cdb9.png" style="display:block;" height="24" width="24" class=""></a>
																											</td>
																											<td align="left" valign="middle" class="mcnShareTextContent" style="padding-left:5px;">
																												<a href="https://www.reddit.com/submit?url=<?php echo urlencode($campaign_link);?>" target="" style="color: #FFFFFF;font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif;font-size: 12px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;">Reddit</a>
																											</td>
																										</tr>
																									</tbody>
																								</table>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																<!--[if mso]>
																</td>
																<![endif]-->
																
																<!--[if mso]>
																<td align="center" valign="top">
																<![endif]-->
																<table align="left" border="0" cellpadding="0" cellspacing="0">
																	<tbody>
																		<tr>
																			<td valign="top" style="padding-right:0; padding-bottom:9px;" class="mcnShareContentItemContainer">
																				<table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;background-color: #0084ff;border: 4px solid #0084ff;">
																					<tbody>
																						<tr>
																							<td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">
																								<table align="left" border="0" cellpadding="0" cellspacing="0" width="">
																									<tbody>
																										<tr>
																											<td align="center" valign="middle" width="24" class="mcnShareIconContent">
																												<a href="fb-messenger://share/?link=<?php echo urlencode($campaign_link);?>/&app_id=181038895828102" target="_blank"><img src="https://gallery.mailchimp.com/144e2b841120da1b2d05b4e05/images/7eed5e49-469c-4f2e-8260-9f1126defc03.png" style="display:block;" height="24" width="24" class=""></a>
																											</td>
																											<td align="left" valign="middle" class="mcnShareTextContent" style="padding-left:5px;">
																												<a href="fb-messenger://share/?link=<?php echo urlencode($campaign_link);?>/&app_id=181038895828102" target="" style="color: #FFFFFF;font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif;font-size: 12px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;">Messenger</a>
																											</td>
																										</tr>
																									</tbody>
																								</table>
																							</td>
																						</tr>
																					</tbody>
																				</table>
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
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!--share-->
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
								<!--<div style="text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><span style="font-size:12px">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span></span></div>-->
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
								<h1 class="null" style="text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif"><span style="font-size:14px"><strong>The Vlogfund Team!</strong></span></span></h1>
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
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
