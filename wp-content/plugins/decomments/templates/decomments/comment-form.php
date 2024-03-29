<?php
echo '<span id="decom_default_position_form_add" style="display: none"></span>';

if ( get_option( 'show_avatars' ) ) {
	$da_position = $settings['display_avatars_right'] ? ' decomments-avatar-right' : '';
} else {
	$da_position = ' no-avatar';
}

?>

<div id="decomments-form-add-comment" class="decomments-addform<?php echo $da_position; ?>" data-site-url="<?php echo site_url(); ?>">

	<div class="decomments-social-login-widget">
		<?php
		do_action( 'comment_form_top' );
		?>
	</div>

	<div class="decomments-addform-title">

		<h3><?php echo apply_filters( 'decomments_add_comment_title', esc_html__( 'Add comment', 'decomments' ) ); ?></h3>

	</div>

	<?php if ( ! is_user_logged_in() ) {
		echo $login_form;
	} ?>


	<div class="decomments-addform-head"<?php if ( is_user_logged_in() ) : echo ' data-full="short"'; endif; ?>>

		<?php if ( ! $decom_settings['disable_display_logo'] ) {
			if ( empty( $decom_settings['decom_your_affiliate_id'] ) ) {
				$link = "https://decomments.com";
			} else {
				$link = "https://decomments.com?ref=" . $decom_settings['decom_your_affiliate_id'];
			}
			?>
			<a href="<?php echo $link; ?>" class="decomments-affilate-link">
				<img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/logo.svg" width="78" height="21" />
			</a>
		<?php } ?>

		<?php if ( $decom_settings['output_subscription_rejoin'] || $decom_settings['output_subscription_comments'] ) { ?>
			<?php if ( is_user_logged_in() ) { ?>
				<a class="decomments-logout-link" href="<?php echo wp_logout_url( home_url() . $_SERVER['REQUEST_URI'] ); ?>"><?php _e( 'Log out', 'decomments' ); ?></a>
			<?php } ?>

			<nav class="decomments-subscribe-block">

				<span class="decomments-subscribe-show"><i class="decomments-icon-quick-contacts-mail"></i><?php _e( 'Subscribe', 'decomments' ); ?></span>

				<span class="decomments-subscribe-links">
					<?php $subcribe_block = '';

					if ( $decom_settings['output_subscription_rejoin'] ) {
						$active = $settings['mark_subscription_rejoin'] ? ' active' : '';
						$subcribe_block .= '<a class="decomments-checkbox' . $active . '" href="javascript:void(0)" name="subscribe_my_comment">' . __( 'Replies to my comments', 'decomments' ) . '</a>';
					}
					if ( $decom_settings['output_subscription_comments'] ) {
						$active                                                 = '';
						$decomments_current_commenter_is_subscribe_all_comments = decomments_is_user_subscribe_all_comments_by_post();

						if ( $decomments_current_commenter_is_subscribe_all_comments ) {
							$active = ' active';
						} else if ( isset( $settings['mark_subscription_comments'] ) && $settings['mark_subscription_comments'] ) {
							$active = ' active';
						}


						$subcribe_block .= '<a class="decomments-checkbox' . $active . '" href="javascript:void(0)" name="subscribe_all_comments">' . __( 'All comments', 'decomments' ) . '</a>';
					}
					echo $subcribe_block; ?>
				</span>
			</nav>


		<?php } ?>


		<nav class="descomments-form-nav">
			<a title="<?php _e( 'Add a quote', 'decomments' ) ?>" class="decomments-add-blockquote " onclick="decom.showModal(this,jQuery('.decomments-comment-section').attr('data-modal-quote')); return false;"><i class="decomments-icon-format-quote"></i></a>
			<a title="<?php _e( 'Add a picture', 'decomments' ) ?>" class="decomments-add-image " onclick="decom.showModal(this,jQuery('.decomments-comment-section').attr('data-modal-addimage')); return false;" data-width="500" data-height="120"><i class="decomments-icon-insert-photo"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/photo.svg" width="28" height="23" /></i></a>
		</nav>
	</div>


	<div class="decomments-addform-body"<?php if ( is_user_logged_in() ) : echo ' data-full="short"'; endif; ?>>

		<?php if ( is_user_logged_in() ) : echo $login_success; endif; ?>

		<input type="hidden" name="social_icon" id="decomments-social-icon" value="<?php echo $social_iсon ?>">

		<textarea rows="5" cols="30" class="decomments-editor"></textarea>

		<div class="decomments-commentform-message">
			<i class="decomments-icon-warning"></i>
			<span><?php _e( 'Sorry, you must be logged in to post a comment.', 'decomments' ); ?></span>
		</div>

		<span class="decomments-loading"><div class="loader-ball-scale">
				<div></div>
				<div></div>
				<div></div>
			</div></span>
		<div class="decomments-comment-form-custom-inputs">
			<?php
			do_action( 'decomments_form_input' );

			$decomments_google_captcha = do_shortcode( '[bws_google_captcha]' );
			if ( false === strpos( $decomments_google_captcha, '[bws_google_captcha]' ) ) {
				?>
				<div id="decomments-google-recapcha">
					<?php echo $decomments_google_captcha; ?>
				</div>
			<?php } ?>
		</div>
		<div class="decomments-comment-form-custom-inputs" style="display: none;">
			<?php echo apply_filters( 'decomments_form_block_for_hidden_inputs', '' ); ?>
		</div>
		<button class="decomments-button decomments-button-send"><?php esc_html_e( 'Submit', 'decomments' ) ?></button>
		<button class="decomments-button decomments-button-cancel"><?php esc_html_e( 'Cancel', 'decomments' ) ?></button>

	</div>

</div>

<div class="decomments-form-popup">


	<div id="decom_alert_void-block" class="decomments-popup-style" style="display:none;">
		<div class="decomments-popup-style">
			<div id="decom-alert-void-text" class="decom-popup-box decom-quote-box">
				<p></p>
			</div>
		</div>
	</div>
</div>