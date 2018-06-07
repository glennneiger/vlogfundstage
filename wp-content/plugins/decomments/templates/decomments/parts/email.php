<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8' />
	<title> <?php echo strtolower( $_SERVER['SERVER_NAME'] ) . ' - ' . $message_title; ?></title>
</head>
<body style="-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;margin: 0;padding: 0;font-family: 'Helvetica', 'Arial', sans-serif;color: #222222;font-weight: normal;text-align: left;line-height: 19px;font-size: 14px;width: 100% !important; background-color: #f7f7f7;">
<table class="body" style="border-spacing: 0;border-collapse: collapse;padding: 0;vertical-align: top;text-align: left;height: 100%;width: 100%; max-width: 600px;color: #222222;font-family: 'Helvetica', 'Arial', sans-serif;font-weight: normal;margin: 0;line-height: 19px;font-size: 14px;margin: 0 auto;margin-bottom: 20px !important;">
	<style type="text/css">
		[class="decomments_header"] a {
			display:         block;
			text-align:      right;
			font-family:     'Helvetica', 'Arial', sans-serif;
			font-size:       25px;
			font-weight:     700;
			color:           #ffffff;
			text-decoration: none;
		}
	</style>
	<tbody>
	<tr style="padding: 0;vertical-align: top;text-align: left;">
		<td class="center" align="center" valign="top" style="word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: center;color: #222222;font-family: 'Helvetica', 'Arial', sans-serif;font-weight: normal;margin: 0;line-height: 19px;font-size: 14px;border-collapse: collapse !important;">
			<center style="width: 100%;min-width: 0 !important; border: 1px solid #eeeeee; box-shadow: 0 1px 3px #eeeeee; background-color: #ffffff; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px;">
				<table class="decomments_header" style="width: 100%; text-align: right; background-color: #35283a; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px;">
					<tbody>
					<tr>
						<td style="width: 30%; vertical-align: center; padding: 20px 5px 20px 15px; text-align: left;">
							<a href="https://decomments.com/" style="text-decoration: none !important; text-align: left !important;">
								<img src="https://decomments.com/wp-content/themes/decomments/assets/images/logo.png" width="120px" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;max-width: 100%;border: none;">
							</a>
						</td>
						<td style="width: 70%; vertical-align: center; padding: 20px 15px 20px 5px; text-align: right; font-family: 'Helvetica', 'Arial', sans-serif; font-size: 25px; font-weight: 700; color: #ffffff; text-decoration: none;">
						</td>
					</tr>
					</tbody>
				</table>
				<?php /*
				<div>
					<?php echo $message['text']; ?>
					<br/>
					<?php echo $unsubscribe; ?>

				</div>
 */ ?>


				<table style="width: 100%; padding: 40px 12px;">
					<tbody>
					<tr>
						<td colspan="2" style="font-family: 'Helvetica', 'Arial', sans-serif; font-size: 18px; text-align: left;">
							<?php echo $message['text']; ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width: 100%; vertical-align: center;">
							<br/>
							<?php echo $unsubscribe; ?>
						</td>
					</tr>
					</tbody>
				</table>
 
				<table style="width: 100%; background-color: #35283a; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px;">
					<tbody>
					<tr>
						<td style="width: 100%; vertical-align: center; padding: 20px 15px 20px 15px; text-align: center;">
							<a href="https://decomments.com/" style="text-decoration: none !important;">
								<img src="https://decomments.com/wp-content/themes/decomments/assets/images/logo.png" width="120px" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;max-width: 100%;border: none;">
							</a>
						</td>
					</tr>
					</tbody>
				</table>
			</center>
		</td>
	</tr>
	</tbody>
</table>
</body>
</html>