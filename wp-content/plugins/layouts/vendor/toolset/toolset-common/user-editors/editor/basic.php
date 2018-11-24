<?php

/**
 * Editor class for the Basic editor (CodeMirror).
 *
 * Handles all the functionality needed to allow the Basic editor (CodeMirror) to work with Content Template editing.
 *
 * @since 2.5.0
 */

class Toolset_User_Editors_Editor_Basic
	extends Toolset_User_Editors_Editor_Abstract {

	protected $id = 'basic';
	protected $name = 'HTML';

	public function initialize() {
		add_filter( 'wpv_filter_wpv_shortcodes_transform_format', array( $this, 'secure_shortcode_from_sanitization' ), 10, 2 );
	}

	public function required_plugin_active() {
		return true;
	}

	public function run() {

	}

	/**
	 * Transform all the shortcodes to the new format, using placeholders instead of brackets, whenever they are used
	 * for a Content Template that is built using a page builder.
	 *
	 * @param    string   $shortcode          The shortcode string.
	 * @param    int      $content_template   The current currently edited Content Template.
	 *
	 * @return   string   The transformed shortcode.
	 *
	 * @since 2.5.1
	 */
	public function secure_shortcode_from_sanitization( $shortcode, $content_template ) {
		if (
			null !== $content_template
			&& 'view-template' === $content_template->post_type
			&& in_array(
				get_post_meta(
					$content_template->ID,
					'_toolset_user_editors_editor_choice', true
				),
				array(
					Toolset_User_Editors_Editor_Screen_Divi_Backend::DIVI_SCREEN_ID,
					Toolset_User_Editors_Editor_Screen_Avada_Backend::AVADA_SCREEN_ID,
					Toolset_User_Editors_Editor_Screen_Visual_Composer_Backend::VC_SCREEN_ID,
					Toolset_User_Editors_Editor_Screen_Native_Backend::NATIVE_SCREEN_ID,
					Toolset_User_Editors_Editor_Screen_Beaver_Backend::BEAVER_SCREEN_ID,
				)
			)
		) {
			$shortcode = str_replace( '[', '{!{', $shortcode );
			$shortcode = str_replace( ']', '}!}', $shortcode );
		}
		return $shortcode;
	}
}