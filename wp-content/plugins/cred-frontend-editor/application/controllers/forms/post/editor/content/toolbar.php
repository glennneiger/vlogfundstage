<?php

namespace OTGS\Toolset\CRED\Controller\Forms\Post\Editor\Content;

use OTGS\Toolset\CRED\Controller\Forms\Post\Main as PostFormMain;
use OTGS\Toolset\CRED\Controller\FormEditorToolbar\Base;

use OTGS\Toolset\CRED\Model\Settings;

/**
 * Post form content editor toolbar controller.
 * 
 * @since 2.1
 */
class Toolbar extends Base {
	
	protected $editor_domain = 'post';
	protected $editor_target = 'content';
	
	/**
	 * Print the toolbar buttons.
	 *
	 * @since 2.1
	 */
	public function print_toolbar_buttons() {
		global $post_ID;

		$this->print_default_buttons();
		$this->print_generic_and_conditional_buttons();
		$this->print_media_button( $post_ID );
	}

	/**
	 * Print the toolbar buttons for the notification subject input.
	 * 
	 * @param string $editor_id
	 *
	 * @since 2.1
	 */
	public function print_notification_subject_toolbar_buttons( $editor_id ) {
		do_action(
			'wpv_action_wpv_generate_fields_and_views_button',
			$editor_id,
			array( 'output' => 'button' )
		);

		$placeholders_args = array(
            'editor_domain' => $this->editor_domain,
            'editor_target' => $editor_id,
			'slug' => 'notification-placeholders-subject',
			'label' => __( 'Placeholders', 'wp-cred' ),
            'icon' => '<i class="fa fa-database"></i>',
            'class' => 'js-cred-form-notification-placeholders',
            'data' => array( 'kind' => 'subject' )
		);
		$this->print_button( $placeholders_args );
	}
	
	/**
	 * Print the toolbar buttons for the notification body editor.
	 * 
	 * @param string $editor_id
	 *
	 * @since 2.1
	 */
	public function print_notification_body_toolbar_buttons( $editor_id ) {
		do_action(
			'wpv_action_wpv_generate_fields_and_views_button',
			$editor_id,
			array( 'output' => 'button' )
		);

		$placeholders_args = array(
            'editor_domain' => $this->editor_domain,
            'editor_target' => $editor_id,
			'slug' => 'notification-placeholders-body',
			'label' => __( 'Placeholders', 'wp-cred' ),
            'icon' => '<i class="fa fa-database"></i>',
            'class' => 'js-cred-form-notification-placeholders',
            'data' => array( 'kind' => 'body' )
		);
		$this->print_button( $placeholders_args );
	}

	/**
	 * Print the toolbar buttons for the message after submitting the form.
	 * 
	 * @param string $editor_id
	 *
	 * @since 2.1
	 */
	public function print_action_message_toolbar_buttons( $editor_id ) {
		do_action(
			'wpv_action_wpv_generate_fields_and_views_button',
			$editor_id,
			array( 'output' => 'button' )
		);

		global $post_ID;
		$this->print_media_button( $post_ID, $editor_id );
	}

	/**
	 * Complete shared data to be used in the toolbar script.
	 *
	 * @return array
	 * 
	 * @since 2.1
	 */
	protected function get_script_localization() {
		$origin = admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' )  );
		$query_args['toolset_force_one_query_arg'] = 'toolset';
		$ajaxurl = esc_url( add_query_arg(
			$query_args,
			$origin
		) );

		$cred_ajax = \CRED_Ajax::get_instance();

		$i18n_shared = $this->get_shared_script_localization();

		$i18n = array(
			'messages' => array(
				'selection_missing' => __( 'You need to select a post type first', 'wp-cred' )
			),
			'data' => array(
				'ajaxurl' => $ajaxurl,
				'requestObjectFields' => array(
					'action' => $cred_ajax->get_action_js_name( \CRED_Ajax::CALLBACK_GET_POST_TYPE_FIELDS ),
					'nonce' => wp_create_nonce( \CRED_Ajax::CALLBACK_GET_POST_TYPE_FIELDS )
				),
				'shortcodes' => array(
					'form_container' => PostFormMain::SHORTCODE_NAME_FORM_CONTAINER
				),
				'fields' => array(
					'labels' => array(
						'basic' => __( 'Standard WordPress fields', 'wp-cred' ),
						'taxonomy' => __( 'Taxonomies', 'wp-cred' ),
						'meta' => __( 'Custom fields', 'wp-cred' ),
						'roles' => __( 'Related posts', 'wp-cred' ),
						'legacyParent' => __( 'Parent post', 'wp-cred' ),
						'hierarchicalParent' => __( 'Hierarchical parent post', 'wp-cred' ),
						'relationship' => __( 'Relationships', 'wp-cred' )
					),
					'fields' => array(
						'formElements' => array(
							'form_container' => array(
								'label' => __( 'Form container', 'wp-cred' ),
								'shortcode' => PostFormMain::SHORTCODE_NAME_FORM_CONTAINER,
								'requiredItem' => true,
								'attributes' => array(),
								'options' => array()
							)
						)
					)
				),
				'scaffold' => array(
					'fields' => array(
						'formElements' => array(
							'feedback' => array(
								'label' => __( 'Form messages', 'wp-cred' ),
								'shortcode' => PostFormMain::SHORTCODE_NAME_FORM_FIELD,
								'attributes' => array(
									'field' => 'form_messages'
								),
								'options' => array(
									'class' => array(
										'label' => __( 'Additional classnames', 'wp-cred' ),
										'type'  => 'text',
										'defaultForceValue' => 'alert alert-warning'
									)
								),
								'location' => 'top'
							)
						)
					)
				),
				'placeholders' => array(
					'%%POST_ID%%' => array(
						'label' => __( 'Post ID', 'wp-cred' ),
						'placeholder' => '%%POST_ID%%'
					),
					'%%POST_TITLE%%' => array(
						'label' => __( 'Post title', 'wp-cred' ),
						'placeholder' => '%%POST_TITLE%%'
					),
					'%%POST_LINK%%' => array(
						'label' => __( 'Post link', 'wp-cred' ),
						'placeholder' => '%%POST_LINK%%',
						'target__not_in' => array( 'subject' )
					),
					'%%POST_PARENT_TITLE%%' => array(
						'label' => __( 'Post parent title', 'wp-cred' ),
						'placeholder' => '%%POST_PARENT_TITLE%%',
						'type__in' => array( 'form_submit' )
					),
					'%%POST_PARENT_LINK%%' => array(
						'label' => __( 'Post parent link', 'wp-cred' ),
						'placeholder' => '%%POST_PARENT_LINK%%',
						'target__not_in' => array( 'subject' ),
						'type__in' => array( 'form_submit' )
					),
					'%%POST_ADMIN_LINK%%' => array(
						'label' => __( 'Post admin link', 'wp-cred' ),
						'placeholder' => '%%POST_ADMIN_LINK%%',
						'target__not_in' => array( 'subject' )
					),
					'%%USER_LOGIN_NAME%%' => array(
						'label' => __( '(Logged in user) User login name', 'wp-cred' ),
						'placeholder' => '%%USER_LOGIN_NAME%%'
					),
					'%%USER_DISPLAY_NAME%%' => array(
						'label' => __( '(Logged in user) User display name', 'wp-cred' ),
						'placeholder' => '%%USER_DISPLAY_NAME%%'
					),
					'%%FORM_NAME%%' => array(
						'label' => __( 'Form name', 'wp-cred' ),
						'placeholder' => '%%FORM_NAME%%'
					),
					'%%FORM_DATA%%' => array(
						'label' => __( 'Form data', 'wp-cred' ),
						'placeholder' => '%%FORM_DATA%%',
						'target__not_in' => array( 'subject' ),
						'type__in' => array( 'form_submit' )
					),
					'%%DATE_TIME%%' => array(
						'label' => __( 'Date/Time', 'wp-cred' ),
						'placeholder' => '%%DATE_TIME%%'
					),
					'%%EXPIRATION_DATE%%' => array(
						'label' => __( 'Expiration date', 'wp-cred' ),
						'placeholder' => '%%EXPIRATION_DATE%%'
					)
				)
			)
		);

		$blocked_reCaptcha = true;
		$cred_settings = Settings::get_instance()->get_settings();//\CRED_Loader::get( 'MODEL/Settings' )->getSettings();
		if (
			isset( $cred_settings[ 'recaptcha' ][ 'public_key' ] )
			&& isset( $cred_settings[ 'recaptcha' ][ 'private_key' ] )
			&& ! empty( $cred_settings[ 'recaptcha' ][ 'public_key' ] )
			&& ! empty( $cred_settings[ 'recaptcha' ][ 'private_key' ] ) 
		) {
			$blocked_reCaptcha = false;
		}

		$reCaptcha_settings_url = admin_url( 'admin.php' );
		$reCaptcha_settings_url = add_query_arg(
			array( 'page' => 'toolset-settings', 'tab' => 'forms' ),
			$reCaptcha_settings_url
		);

		$i18n['data']['scaffold']['fields']['formElements']['recaptcha'] = array(
			'label' => __( 'reCaptcha', 'wp-cred' ),
			'shortcode' => PostFormMain::SHORTCODE_NAME_FORM_FIELD,
			'blockedItem' => $blocked_reCaptcha,
			'blockedReason' => __( 'You need an API key to use the reCaptcha field', 'wp-cred' ),
			'blockedLink' => $reCaptcha_settings_url,
			'attributes' => array(
				'field' => 'recaptcha',
				'class' => 'form-control',
				'output' => 'bootstrap'
			),
			'location' => 'bottom'
		);

		$i18n['data']['scaffold']['fields']['formElements']['submit'] = array(
			'label' => __( 'Submit button', 'wp-cred' ),
			'shortcode' => PostFormMain::SHORTCODE_NAME_FORM_FIELD,
			'requiredItem' => true,
			'attributes' => array(
				'field' => 'form_submit',
				'output' => 'bootstrap'
			),
			'options' => array(
				'value' => array(
					'label' => __( 'Button label', 'wp-cred' ),
					'type'  => 'text',
					'defaultForceValue' => __( 'Submit', 'wp-cred' )
				),
				'class' => array(
					'label' => __( 'Additional classnames', 'wp-cred' ),
					'type'  => 'text',
					'defaultForceValue' => 'btn btn-primary btn-lg'
				)
			),
			'location' => 'bottom'
		);

		return array_merge( $i18n_shared, $i18n );
	}
	
}