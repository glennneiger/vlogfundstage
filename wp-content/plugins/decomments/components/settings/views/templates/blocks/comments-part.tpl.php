<header>
	<i class="icon-forum"></i>

	<h2><?php esc_html_e( 'Comments', DECOM_LANG_DOMAIN ); ?></h2>
</header>

<p>
	<input name="disable_display_logo" id="_disable_display_logo" type="checkbox" <?php checked( isset( $settings['disable_display_logo'] ) ? $settings['disable_display_logo'] : 0, 1 ); ?>/>
	<label for="_disable_display_logo">
		<?php echo isset( $setting_name['disable_display_logo'] ) ? $setting_name['disable_display_logo'] : ''; ?>
	</label>
	<br>
	<label for="decom_your_affiliate_id">
		https://decomments.com?ref=
		<input name="decom_your_affiliate_id" id="decom_your_affiliate_id" type="number" style="width:54px;"
			   value="<?php echo isset( $settings['decom_your_affiliate_id'] ) ? $settings['decom_your_affiliate_id'] : ''; ?>" placeholder="ID" /><br>
	</label>

</p>

<p>
	<input name="allow_quote_comments" id="_allow_quote_comments" type="checkbox" <?php checked( isset( $settings['allow_quote_comments'] ) ? $settings['allow_quote_comments'] : 0, 1 ); ?>/>
	<label for="_allow_quote_comments"><?php echo isset( $setting_name['allow_quote_comments'] ) ? $setting_name['allow_quote_comments'] : '' ?></label>
</p>

<p>
	<input name="decom_disable_replies" id="_decom_disable_replies" type="checkbox" <?php checked( isset( $settings['decom_disable_replies'] ) ? $settings['decom_disable_replies'] : 0, 1 ); ?>/>
	<label for="_decom_disable_replies"><?php echo isset( $setting_name['decom_disable_replies'] ) ? $setting_name['decom_disable_replies'] : ''; ?></label>
</p>

<p>
	<label for="_time_editing_deleting_comments"><?php echo isset( $setting_name['time_editing_deleting_comments'] ) ? $setting_name['time_editing_deleting_comments'] : ''; ?></label>
	<input class="easyui-numberspinner" data-options="min:1" name="time_editing_deleting_comments" id="_time_editing_deleting_comments" type="number" value="<?php echo isset( $settings['time_editing_deleting_comments'] ) ? $settings['time_editing_deleting_comments'] : ''; ?>" size="5" max="240" />
</p>

<p>
	<input name="comment_form_up" id="_comment_form_up" type="checkbox" <?php checked( isset( $settings['comment_form_up'] ) ? $settings['comment_form_up'] : 0, 1 ); ?>/>
	<label for="_comment_form_up"><?php echo isset( $setting_name['comment_form_up'] ) ? $setting_name['comment_form_up'] : ''; ?></label>
</p>

<p>
	<span class="dsp-label"><?php echo isset( $setting_name['follow'] ) ? $setting_name['follow'] : ''; ?></span>
	<span class="dsp-fieldset">
							<input name="follow" id="_follow" type="radio" value="dofollow" <?php checked( isset( $settings['follow'] ) ? $settings['follow'] : '', 'dofollow' ); ?>/>
							<label for="_follow"><?php _e( 'dofollow', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="follow" id="_nofollow" type="radio" value="nofollow" <?php checked( isset( $settings['follow'] ) ? $settings['follow'] : '', 'nofollow' ); ?>/>
							<label for="_nofollow"><?php _e( 'nofollow', DECOM_LANG_DOMAIN ); ?></label>
						</span>
</p>

<p>
	<input name="allocate_comments_author_post"
		   id="_allocate_comments_author_post"
		   type="checkbox" <?php checked( isset( $settings['allocate_comments_author_post'] ) ? $settings['allocate_comments_author_post'] : 0, 1 ); ?>/>
	<label for="_allocate_comments_author_post">
		<?php echo isset( $setting_name['allocate_comments_author_post'] ) ? $setting_name['allocate_comments_author_post'] : ''; ?>
	</label>
</p>

<p>
	<label for="decomments_main_color_theme"><?php echo isset( $setting_name['decomments_main_color_theme'] ) ? $setting_name['decomments_main_color_theme'] : ''; ?></label>

	<input type="text" name="decomments_main_color_theme" id="decomments-settings-color-field" value="<?php echo isset( $settings['decomments_main_color_theme'] ) ? $settings['decomments_main_color_theme'] : ''; ?>" />
</p>

<p>
	<input name="allow_html_in_comments" id="_allow_html_in_comments" type="checkbox" <?php checked( isset( $settings['allow_html_in_comments'] ) ? $settings['allow_html_in_comments'] : 0, 1 ); ?>/>
	<label for="_allow_html_in_comments"><?php echo isset( $setting_name['allow_html_in_comments'] ) ? $setting_name['allow_html_in_comments'] : ''; ?></label>
</p>

<p>
	<input name="output_numbers_comments" id="_output_numbers_comments" type="checkbox" <?php checked( isset( $settings['output_numbers_comments'] ) ? $settings['output_numbers_comments'] : 0, 1 ); ?>/>
	<label for="_output_numbers_comments"><?php echo isset( $setting_name['output_numbers_comments'] ) ? $setting_name['output_numbers_comments'] : ''; ?></label>
</p>

<p>
	<input name="output_total_number_comments_top" id="_output_total_number_comments_top" type="checkbox" <?php checked( isset( $settings['output_total_number_comments_top'] ) ? $settings['output_total_number_comments_top'] : 0, 1 ); ?>/>
	<label for="_output_total_number_comments_top"><?php echo isset( $setting_name['output_total_number_comments_top'] ) ? $setting_name['output_total_number_comments_top'] : ''; ?></label>
</p>

<p>
	<input id="_enable_embed_links" name="enable_embed_links" type="checkbox" <?php echo ( isset( $settings['enable_embed_links'] ) && ! empty( $settings['enable_embed_links'] ) ) ? 'checked' : ''; ?>/>
	<label for="_enable_embed_links"><?php echo isset( $setting_name['enable_embed_links'] ) ? $setting_name['enable_embed_links'] : ''; ?></label>

	<span id="max_embed_links_count" class="show-hidden">
							<label for="_max_embed_links_count"><?php echo '=> ' . isset( $setting_name['max_embed_links_count'] ) ? $setting_name['max_embed_links_count'] : ''; ?></label>
							<input class="easyui-numberspinner" data-options="min:1" name="max_embed_links_count" id="_max_embed_links_count" type="number" value="<?php echo isset( $settings['max_embed_links_count'] ) ? $settings['max_embed_links_count'] : ''; ?>" size="5" />
						</span>
</p>

<p>
	<input name="enable_field_website" id="_enable_field_website" type="checkbox" <?php echo $settings['enable_field_website'] ? 'checked' : ''; ?>/>
	<label for="_enable_field_website"><?php echo $setting_name['enable_field_website']; ?></label>
</p>


<!--<h4><i class="icon-cloud-upload"></i> <?php /*esc_html_e( 'File Upload', DECOM_LANG_DOMAIN ); */ ?></h4>-->

<p>
	<label for="_max_size_uploaded_images"><?php echo isset( $setting_name['max_size_uploaded_images'] ) ? $setting_name['max_size_uploaded_images'] : ''; ?></label>
	<input class="easyui-numberspinner" name="max_size_uploaded_images" id="_max_size_uploaded_images" type="number" value="<?php echo isset( $settings['max_size_uploaded_images'] ) ? $settings['max_size_uploaded_images'] : ''; ?>" size="5" max="99" /> MB
</p>

<p>
	<span class="dsp-label"><?php echo isset( $setting_name['deco_comments_paginate'] ) ? $setting_name['deco_comments_paginate'] : ''; ?></span>
	<span class="dsp-fieldset">
							<input name="deco_ajax_navy" id="_deco_show_more_comments_onebutton" type="radio" value="deco_show_more_comments_onebutton" <?php checked( isset( $settings['deco_ajax_navy'] ) ? $settings['deco_ajax_navy'] : '', 'deco_show_more_comments_onebutton' ); ?>/>
							<label for="_deco_show_more_comments_onebutton"><?php _e( '"Show more" button', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="deco_ajax_navy" id="_deco_show_more_comments_prevnext" type="radio" value="deco_show_more_comments_prevnext" <?php checked( isset( $settings['deco_ajax_navy'] ) ? $settings['deco_ajax_navy'] : '', 'deco_show_more_comments_prevnext' ); ?>/>
							<label for="_deco_show_more_comments_prevnext"><?php _e( 'Previous/next pages', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="deco_ajax_navy" id="_deco_show_more_comments_lazy" type="radio" value="deco_show_more_comments_lazy" <?php checked( isset( $settings['deco_ajax_navy'] ) ? $settings['deco_ajax_navy'] : '', 'deco_show_more_comments_lazy' ); ?>/>
							<label for="_deco_show_more_comments_lazy"><?php _e( 'Infinite scroll (lazy load)', DECOM_LANG_DOMAIN ); ?></label>
						</span>
</p>



