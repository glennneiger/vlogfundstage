<?php

/**
 * Shortcode generator for Toolset CRED
 *
 * @since 1.9.3
 */
class CRED_Shortcode_Generator extends Toolset_Shortcode_Generator {
	
	public $admin_bar_item_registered	= false;
	
	public $footer_dialog_needed		= false;
	public $dialog_groups				= array();
	
	public $footer_dialogs				= '';

	public function initialize() {
		
		/**
		 * ---------------------
		 * Admin Bar
		 * ---------------------
		 */
		// Register the Fields and Views item in the backend Admin Bar
		$this->admin_bar_item_registered = false;
		add_filter( 'toolset_shortcode_generator_register_item', array( $this, 'register_cred_shortcode_generator' ), 50 );
		
		/**
		 * ---------------------
		 * CRED button and dialogs
		 * ---------------------
		 */
		// Initialize dialog groups and the action to register them
		$this->dialog_groups = array();
		add_action( 'cred_action_collect_shortcode_groups', array( $this, 'register_builtin_groups' ), 1 );
		add_action( 'cred_action_collect_shortcode_groups', array( $this, 'register_extra_groups' ), 1 );
		add_action( 'cred_action_register_shortcode_group', array( $this, 'register_shortcode_group' ), 10, 2 );
		
		// Fields and Views button in native editors plus on demand:
		// - From media_buttons actions, for posts, taxonomy or users depending on the current edit page
		// - From Toolset arbitrary editor toolbars, for posts
		add_action( 'media_buttons', array( $this, 'generate_cred_button' ) );
		add_action( 'toolset_action_toolset_editor_toolbar_add_buttons', array( $this, 'generate_cred_custom_button' ), 10, 2 );
		
		add_filter( 'cred_filter_add_cred_button', array( $this, 'unhook_cred_button'), 10, 2 );
		
		// Track whether dialogs re needed and have been rendered in the footer
		$this->footer_dialogs					= '';
		
		// Generate and print the shortcodes dialogs in the footer,
		// both in frotend and backend, as long as there is anything to print.
		// Do it as late as possible because page builders tend to register their templates,
		// including native WP editors, hence shortcode buttons, in wp_footer:10.
		// Also, because this way we can extend the dialog groups for almost the whole page request.
		add_action( 'wp_footer', array( $this, 'render_footer_dialogs' ), PHP_INT_MAX );
		add_action( 'admin_footer', array( $this, 'render_footer_dialogs' ), PHP_INT_MAX );
		
		/**
		 * ---------------------
		 * Assets
		 * ---------------------
		 */
		// Register shortcodes dialogs assets
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
		
		// Ensure that shortcodes dialogs assets re enqueued
		// both when using the Admin Bar item and when a Fields and Views button is on the page.
		add_action( 'cred_action_enforce_shortcode_assets', array( $this, 'enforce_shortcode_assets' ) );
		
		/**
		 * ---------------------
		 * Compatibility
		 * ---------------------
		 */
		//add_filter( 'gform_noconflict_scripts',	array( $this, 'gform_noconflict_scripts' ) );
		//add_filter( 'gform_noconflict_styles',	array( $this, 'gform_noconflict_styles' ) );
		
	}
	
	/**
	 * Register the CRED shortcode generator in the Toolset shortcodes admin bar entry.
	 *
	 * Hooked into the toolset_shortcode_generator_register_item filter.
	 * 
	 * @since 1.9.3
	 */
	public function register_cred_shortcode_generator( $registered_sections ) {
		$this->admin_bar_item_registered = true;
		$this->footer_dialog_needed = true;
		$this->enforce_shortcode_assets();
		$registered_sections[ 'cred' ] = array(
			'id'		=> 'CRED',
			'title'		=> __( 'CRED forms', 'wpv-views' ),
			'href'		=> '#cred_shortcodes',
			'parent'	=> 'toolset-shortcodes',
			'meta'		=> 'js-cred-shortcode-generator-node'
		);
		return $registered_sections;
	}
	
	/**
	 * Register all the dedicated shortcodes assets:
	 * - Shortcodes GUI script.
	 *
	 * @todo Move the assets registration to here
	 *
	 * @since 1.9.3
	 */	
	public function register_assets() {
		$toolset_assets_manager = Toolset_Assets_Manager::get_instance();
		
		$toolset_assets_manager->register_script(
			'cred-shortcode',
			CRED_ABSURL . '/public/js/cred_shortcode.js',
			array( 'toolset-shortcode' ),
			TOOLSET_COMMON_VERSION,
			true
		);
		
		global $pagenow;
		$cred_shortcode_i18n = array(
			'action'	=> array(
				'insert' => __( 'Insert shortcode', 'wp-cred' ),
				'create' => __( 'Create shortcode', 'wp-cred' ),
				'update' => __( 'Update shortcode', 'wp-cred' ),
				'close' => __( 'Close', 'wp-cred' ),
				'cancel' => __( 'Cancel', 'wp-cred' ),
				'back' => __( 'Back', 'wp-cred' ),
				'previous' => __( 'Previous', 'wp-cred' ),
				'next' => __( 'Next', 'wp-cred' ),
				'save' => __( 'Save settings', 'wp-cred' ),
				'loading' => __( 'Loading...', 'wp-cred' ),
			),
			'title' => array(
				'dialog' => __( 'CRED shortcodes', 'wp-cred' ),
				'generated' => __( 'Generated shortcode', 'wp-cred' ),
				'button' => __( 'CRED', 'wp-cred' ),
			),
			'validation' => array(
				'mandatory'		=> __( 'This option is mandatory ', 'wp-cred' ),
				'number'		=> __( 'Please enter a valid number', 'wp-cred' ),
				'numberlist'	=> __( 'Please enter a valid comma separated number list', 'wp-cred' ),
				'url'			=> __( 'Please enter a valid URL', 'wp-cred' ),
				
			),
			'ajaxurl'									=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' )  ),
			'pagenow'									=> $pagenow
		);
		$toolset_assets_manager->localize_script( 
			'cred-shortcode', 
			'cred_shortcode_i18n', 
			$cred_shortcode_i18n 
		);
	}
	
	/**
	 * Enforce some assets that need to be in the frontend header, like styles, 
	 * when we detect that we are on a page that needs them.
	 * Basically, this involves frontend page builders, detected by their own methods.
	 * Also enforces the generation of the CRED dialog, just in case, in the footer.
	 *
	 * @uses is_frontend_editor_page which is a parent method.
	 *
	 * @since 1.9.3
	 */
	public function frontend_enqueue_assets() {
		// Enqueue on the frontend pages that we know it is needed, maybe on users frontend editors only
		
		if ( $this->is_frontend_editor_page() ) {
			$this->footer_dialog_needed = true;
			$this->enforce_shortcode_assets();
			
		}
		
	}
	
	/**
	 * Enforce some assets that need to be in the backend header, like styles, 
	 * when we detect that we are on a page that needs them.
	 * Also enforces the generation of the CRED dialog, just in case, in the footer.
	 *
	 * Note that we enforce the shortcode assets in all known admin editor pages.
	 *
	 * @uses is_admin_editor_page which is a parent method.
	 *
	 * @since 1.9.3
	 */
	public function admin_enqueue_assets( $hook ) {
		if ( $this->is_admin_editor_page() ) {
			$this->footer_dialog_needed = true;
			$this->enforce_shortcode_assets();
		}
	}
	
	/**
	 * Enfoces the shortcodes assets when loaded at a late time.
	 * Note that there should be no problem with scripts, 
	 * although styles might not be correctly enqueued.
	 *
	 * @usage do_action( 'cred_action_enforce_shortcode_assets' );
	 *
	 * @since 1.9.3
	 */	
	public function enforce_shortcode_assets() {
		
		do_action( 'toolset_enqueue_scripts', array( 'cred-shortcode' ) );
		do_action( 'toolset_enqueue_styles', array( 'toolset-common', 'toolset-dialogs-overrides-css', 'toolset-select2-css' ) );
		do_action( 'otg_action_otg_enforce_styles' );
		
	}
	
	public function unhook_cred_button( $status, $editor ) {
		
		// first determine what is the situation
		
		$is_cred_form_page = false;
		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();
			$is_cred_form_page = ( 
				$editor == 'credformactionmessage' 
				|| ( isset( $current_screen ) && in_array( $current_screen->id, array( 'cred-form', 'cred-user-form' ) ) )
			);
		}
		
		$is_gravity_forms_page = ( 'gf_edit_forms' === toolset_getget( 'page' ) );
		$is_e_popotheme_settings_page = ( 
			'_options' === toolset_getget( 'page' ) 
			&& in_array( $editor, array( 'custom_copyright', 'custom_power' ) ) 
		);
		$is_elementor_page_builder = ( 'elementor' === toolset_getget( 'action' ) );

		// and after that, decide what to do
		if ( 
			$is_cred_form_page 
			|| $is_gravity_forms_page 
			|| $is_e_popotheme_settings_page 
			|| $is_elementor_page_builder 
		) {
			return false;
		}
		
		return $status;
	}
	
	/**
	 * Generates the CRED button on native editors, using the media_buttons action.
	 * and also on demand using a custom action.
	 *
	 * @param $editor		string
	 * @param $args			array
	 *     output	string	'span'|'button'. Defaults to 'span'.
	 *
	 * @since 1.9.3
	 */
	function generate_cred_button( $editor, $args = array() ) {
		
		if ( 
			empty( $args ) 
			&& (
				! apply_filters( 'toolset_editor_add_form_buttons', true ) 
				/**
				 * cred_filter_add_cred_button
				 *
				 * Public filter to disable the Fields and Views button on native WordPress editors.
				 *
				 * @since 2.3.0
				 */
				|| ! apply_filters( 'cred_filter_add_cred_button', true, $editor )
			)
		) {
			// Disable the CRED button just on native WP Editors
			return;
		}
		
		$this->footer_dialog_needed = true;
		$this->enforce_shortcode_assets();
		
		$defaults = array(
			'output'	=> 'span',
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$button			= '';
		$button_label	= __( 'CRED Forms', 'wp-cred' );
		
		switch ( $args['output'] ) {
			case 'button':
				$button = '<button'
					. ' class="button-secondary js-cred-in-toolbar"'
					. ' data-editor="' . esc_attr( $editor ) . '">'
					. '<i class="icon-cred-logo ont-icon-18"></i>'
					. '<span class="button-label">'. esc_html( $button_label ) . '</span>'
					. '</button>';
				break;
			case 'span':
			default:
				$button = '<span'
				. ' class="button js-cred-in-toolbar"'
				. ' data-editor="' . esc_attr( $editor ) . '">'
				. '<i class="icon-cred-logo fa fa-cred-custom ont-icon-18 ont-color-gray"></i>'
				. '<span class="button-label">' . esc_html( $button_label ) . '</span>'
				. '</span>';
				break;
		}
		
		do_action( 'cred_action_enforce_shortcode_assets' );
		
		echo $button;
		
	}
	
	/**
	 * Generate a Fields and Views button for custom editor toolbars, inside a <li></li> HTML tag.
	 *
	 * @param $editor	string	The editor ID.
	 * @param $source	string	The Toolset plugin originting the call.
	 *
	 * Hooked into the toolset_action_toolset_editor_toolbar_add_buttons action.
	 *
	 * @since 1.9.3
	 */
	public function generate_cred_custom_button( $editor, $source = '' ) {
		
		if ( 
			'wpv_filter_meta_html_content' == $editor 
			&& 'views' == $source 
		) {
			return;
		}
		
		$args = array(
			'output'	=> 'button',
		);
		echo '<li>';
		$this->generate_cred_button( $editor, $args );
		echo '</li>';
		
	}
	
	public function get_post_form_shortcode_callback( $form_candidate, $form_type ) {
		$args = array(
			'shortcode' => 'cred_form',
			'target' => 'post',
			'type' => $form_type
		);
		return $this->get_form_shortcode_callback( $form_candidate, $args );
	}
	
	public function get_user_form_shortcode_callback( $form_candidate, $form_type ) {
		$args = array(
			'shortcode' => 'cred_user_form',
			'target' => 'user',
			'type' => $form_type
		);
		return $this->get_form_shortcode_callback( $form_candidate, $args );
	}
	
	public function get_form_shortcode_callback( $form_candidate, $args ) {
		return ( 'edit' === $args['type'] ) 
		? "Toolset.CRED.shortcodeGUI.shortcodeDialogOpen({ shortcode: '" . esc_js( $args['shortcode'] ) . "', title: '" . esc_js( $form_candidate->post_title ) . "', parameters: { form: '" . esc_js( $form_candidate->post_name ) . "' } })" 
		: "Toolset.CRED.shortcodeGUI.shortcodeDoAction({ shortcode: '" . esc_js( $args['shortcode'] ) . "', parameters: { form: '" . esc_js( $form_candidate->post_name ) . "' } })";
	}
	
	public function register_builtin_groups() {
		
		global $wpdb;
		
		$post_forms = get_transient( 'cred_transient_published_post_forms' );
		
		$form_groups = array(
			'new-post'	=> array(
				'name'		=> __( 'New Post Forms', 'wp-cred' ),
				'fields'	=> array()
			),
			'edit-post'	=> array(
				'name'		=> __( 'Edit Post Forms', 'wp-cred' ),
				'fields'	=> array()
			)
		);
		
		if ( $post_forms === false ) {
			
			$post_forms_transient_to_update = array(
				'new'	=> array(),
				'edit'	=> array()
			);
			
			$post_forms_available = $wpdb->get_results(
				"SELECT ID, post_title, post_name FROM {$wpdb->posts}
				WHERE post_type='cred-form'
				AND post_status in ('private')"
			);
			
			foreach ( $post_forms_available as $post_form_candidate ) {
				$current_form_type = 'new';
				$form_settings = (array) get_post_meta( $post_form_candidate->ID, '_cred_form_settings', true );
				if (
					! is_array( $form_settings ) 
					|| empty( $form_settings ) 
					|| ! array_key_exists( 'form', $form_settings ) 
					|| ! array_key_exists( 'type', $form_settings['form'] )
				) {
					continue;
				}
				$current_form_type = $form_settings['form']['type'];
				$post_forms_transient_to_update[ $current_form_type ][] = $post_form_candidate;
				$form_groups[ $current_form_type . '-post' ]['fields'][ $post_form_candidate->post_name ] = array(
					'name'		=> $post_form_candidate->post_title,
					'shortcode'	=> 'cred_form form="' . esc_html( $post_form_candidate->post_name ) . '"',
					'callback'	=> $this->get_post_form_shortcode_callback( $post_form_candidate, $current_form_type )
				);
			}
			
			set_transient( 'cred_transient_published_post_forms', $post_forms_transient_to_update, WEEK_IN_SECONDS );
			
		} else {
			$post_forms_types = array( 'new', 'edit' );
			foreach ( $post_forms_types as $forms_type ) {
				if ( 
					isset( $post_forms[ $forms_type ] )
					&& is_array( $post_forms[ $forms_type ] )
				) {
					foreach ( $post_forms[ $forms_type ] as $post_form_candidate ) {
						$form_groups[ $forms_type . '-post' ]['fields'][ $post_form_candidate->post_name ] = array(
							'name'		=> $post_form_candidate->post_title,
							'shortcode'	=> 'cred_form form="' . esc_html( $post_form_candidate->post_name ) . '"',
							'callback'	=> $this->get_post_form_shortcode_callback( $post_form_candidate, $forms_type )
						);
					}
				}
			}
		}
		
		foreach ( $form_groups as $form_group_candidate_id => $form_group_candidate_data ) {
			if ( count( $form_group_candidate_data['fields'] ) > 0 ) {
				$this->register_shortcode_group( $form_group_candidate_id, $form_group_candidate_data );
			}
		}
		
		$user_forms = get_transient( 'cred_transient_published_user_forms' );
		
		$user_form_groups = array(
			'new-user'	=> array(
				'name'		=> __( 'New User Forms', 'wp-cred' ),
				'fields'	=> array()
			),
			'edit-user'	=> array(
				'name'		=> __( 'Edit User Forms', 'wp-cred' ),
				'fields'	=> array()
			)
		);
		
		if ( $user_forms === false ) {
			
			$user_forms_transient_to_update = array(
				'new'	=> array(),
				'edit'	=> array()
			);
			
			$user_forms_available = $wpdb->get_results(
				"SELECT ID, post_title, post_name FROM {$wpdb->posts}
				WHERE post_type='cred-user-form'
				AND post_status in ('private')"
			);
			
			foreach ( $user_forms_available as $user_form_candidate ) {
				$current_form_type = 'new';
				$form_settings = (array) get_post_meta( $user_form_candidate->ID, '_cred_form_settings', true );
				if (
					! is_array( $form_settings ) 
					|| empty( $form_settings ) 
					|| ! array_key_exists( 'form', $form_settings ) 
					|| ! array_key_exists( 'type', $form_settings['form'] )
				) {
					continue;
				}
				$current_form_type = $form_settings['form']['type'];
				$user_forms_transient_to_update[ $current_form_type ][] = $user_form_candidate;
				$user_form_groups[ $current_form_type . '-user' ]['fields'][ $user_form_candidate->post_name ] = array(
					'name'		=> $user_form_candidate->post_title,
					'shortcode'	=> 'cred_user_form form="' . esc_html( $user_form_candidate->post_name ) . '"',
					'callback'	=> $this->get_user_form_shortcode_callback( $user_form_candidate, $current_form_type )
				);
			}
			
			set_transient( 'cred_transient_published_user_forms', $user_forms_transient_to_update, WEEK_IN_SECONDS );
			
		} else {
			$user_forms_types = array( 'new', 'edit' );
			foreach ( $user_forms_types as $forms_type ) {
				if ( 
					isset( $user_forms[ $forms_type ] )
					&& is_array( $user_forms[ $forms_type ] )
				) {
					foreach ( $user_forms[ $forms_type ] as $user_form_candidate ) {
						$user_form_groups[ $forms_type . '-user' ]['fields'][ $user_form_candidate->post_name ] = array(
							'name'		=> $user_form_candidate->post_title,
							'shortcode'	=> 'cred_user_form form="' . esc_html( $user_form_candidate->post_name ) . '"',
							'callback'	=> $this->get_user_form_shortcode_callback( $user_form_candidate, $forms_type )
						);
					}
				}
			}
		}
		
		foreach ( $user_form_groups as $form_group_candidate_id => $form_group_candidate_data ) {
			if ( count( $form_group_candidate_data['fields'] ) > 0 ) {
				$this->register_shortcode_group( $form_group_candidate_id, $form_group_candidate_data );
			}
		}
		
	}
	
	public function get_shortcode_callback( $shortcode_slug, $shortcode_title ) {
		return "Toolset.CRED.shortcodeGUI.shortcodeDialogOpen({ shortcode: '" . esc_js( $shortcode_slug ) . "', title: '" . esc_js( $shortcode_title ) . "' })";
	}
	
	public function register_extra_groups() {
		
		$group_id	= 'cred-extra';
		$group_data	= array(
			'name'		=> __( 'Other CRED actions', 'wp-cred' ),
			'fields'	=> array()
		);
		
		$shortcodes = array(
			'cred_delete_post_link'		=> __( 'Delete Post Link', 'wp-cred' ),
			'cred_child_link_form'		=> __( 'Create Child Post Link', 'wp-cred' )
		);
		
		foreach ( $shortcodes as $shortcode_slug => $shortcode_title ) {
			$group_data['fields'][ $shortcode_slug ] = array(
				'name'		=> $shortcode_title,
				'shortcode'	=> $shortcode_slug,
				'callback'	=> $this->get_shortcode_callback( $shortcode_slug, $shortcode_title )
			);
		}
		
		$this->register_shortcode_group( $group_id, $group_data );
		
	}
	
	/**
	 * Register a CRED dialog group with its fields.
	 *
	 * @param $group_id		string 	The group unique ID.
	 * @param $group_data	array	The group data:
	 *     name		string	The group name that will be used over the group fields.
	 *     fields	array	Optional. The group fields. Leave blank or empty to just pre-register the group.
	 *         array(
	 *             field_key => array(
	 *                 shortcode	string	The shortcode that this item will insert.
	 *                 name			string	The button label for this item.
	 *                 callback		string	The JS callback to execute when this item is clicked.
	 *             )
	 *         )
	 *
	 * @usage do_action( 'cred_action_register_shortcode_group', $group_id, $group_data );
	 *
	 * @since 1.9.3
	 */
	public function register_shortcode_group( $group_id = '', $group_data = array() ) {
		
		$group_id = sanitize_text_field( $group_id );
		
		if ( empty( $group_id ) ) {
			return;
		}
		
		$group_data['fields'] = ( isset( $group_data['fields'] ) && is_array( $group_data['fields'] ) ) ? $group_data['fields'] : array();
		
		$dialog_groups = $this->dialog_groups;
		
		if ( isset( $dialog_groups[ $group_id ] ) ) {
			
			// Extending an already registered group, which should have a name already.
			if ( ! array_key_exists( 'name', $dialog_groups[ $group_id ] ) ) {
				return;
			}
			foreach( $group_data['fields'] as $field_key => $field_data ) {
				$dialog_groups[ $group_id ]['fields'][ $field_key ] = $field_data;
			}
			
		} else {
			
			// Registering a new group, the group name is mandatory
			if ( ! array_key_exists( 'name', $group_data ) ) {
				return;
			}
			$dialog_groups[ $group_id ]['name']		= $group_data['name'];
			$dialog_groups[ $group_id ]['fields']	= $group_data['fields'];
		
		}
		$this->dialog_groups = $dialog_groups;
		
	}
	
	public function generate_shortcodes_dialog() {
		
		$dialog_links = array();
		$dialog_content = '';
		
		foreach ( $this->dialog_groups as $group_id => $group_data ) {
			
			if ( empty( $group_data['fields'] ) ) {
				continue;
			}
			
			$dialog_links[] = '<li data-id="' . md5( $group_id ) . '" class="editor-addon-top-link" data-editor_addon_target="editor-addon-link-' . md5( $group_id ) . '">' . esc_html( $group_data['name'] ) . ' </li>';

			$dialog_content .= '<div class="group"><h4 data-id="' . md5( $group_id ) . '" class="group-title  editor-addon-link-' . md5( $group_id ) . '-target">' . esc_html( $group_data['name'] ) . "</h4>";
			$dialog_content .= "\n";
			$dialog_content .= '<ul class="toolset-shortcode-gui-group-list cred-shortcode-gui-group-list js-cred-shortcode-gui-group-list">';
			$dialog_content .= "\n";
			foreach ( $group_data['fields'] as $group_data_field_key => $group_data_field_data ) {
				if (
					! isset( $group_data_field_data['callback'] ) 
					|| empty( $group_data_field_data['callback'] )
				) {
					$dialog_content .= sprintf(
						'<li class="item"><button class="button button-secondary button-small js-cred-shortcode-gui-no-attributes" data-shortcode="%s" >%s</button></li>',
						'[' . esc_attr( $group_data_field_data['shortcode'] ) . ']',
						esc_html( $group_data_field_data['name'] )
					);
				} else {
					$dialog_content .= sprintf(
						'<li class="item"><button class="button button-secondary button-small js-cred-shortcode-gui" onclick="%s; return false;">%s</button></li>', 
						$group_data_field_data['callback'], 
						esc_html( $group_data_field_data['name'] )
					);
				}
				$dialog_content .= "\n";
			}
			$dialog_content .= '</ul>';
			$dialog_content .= "\n";
			$dialog_content .= '</div>';
		}

		$direct_links = implode( '', $dialog_links );

		// add search box
		$searchbar = '<div class="cred-shortcode-gui-dialog-searchbar toolset-shortcode-gui-dialog-searchbar">';
		$searchbar .=   '<label for="cred-shortcode-gui-dialog-searchbar-input-for-cred">' . __( 'Search', 'wp-cred' ) . ': </label>';
		$searchbar .=   '<input id="cred-shortcode-gui-dialog-searchbar-input-for-cred" type="text" class="cred-shortcode-gui-dialog-sarch-field js-cred-shortcode-gui-dialog-sarch-field" onkeyup="wpv_on_search_filter(this)" />';
		$searchbar .= '</div>';

		// generate output content
		$out = '
		<div id="js-cred-shortcode-gui-dialog-container-main" class="toolset-shortcode-gui-dialog cred-shortcode-gui-dialog">'
			. "\n"
			. '<div class="cred-shortcode-gui-dialog-content js-cred-shortcode-gui-dialog-content">'
					. "\n"
					. $searchbar
					. "\n"
					//. '<div class="direct-links-desc"><ul class="direct-links"><li class="direct-links-label">' . __( 'Jump to:', 'wp-cred' ) . '</li>' . $direct_links . '</ul></div>'
					//. "\n"
					. $dialog_content
					. '
			</div>
		</div>';
		
		$this->footer_dialogs .= $out;
		
	}
	
	function render_footer_dialogs() {
		
		if ( ! $this->footer_dialog_needed ) {
			return;
		}
		
		do_action( 'cred_action_collect_shortcode_groups' );
		$this->generate_shortcodes_dialog();
		
		$footer_dialogs = $this->footer_dialogs;
		if ( '' != $footer_dialogs ) {
			?>
			<div class="js-cred-footer-dialogs" style="display:none">
				<?php 
				echo $footer_dialogs; 
				?>
			</div>
			<?php
			$this->render_dialog_templates();
		}
		
	}
	
	public function render_dialog_templates() {
		do_action( 'toolset_action_require_shortcodes_templates' );
		?>	
		<script type="text/html" id="tmpl-cred-post-edit-form-template">
			<#
			data = _.extend( 
				data, 
				{
					attributes: {
						postSelection: {
							header: '<?php _e( 'Post selection', 'wp-cred' ); ?>',
							fields: {
								post: { 
									label: '<?php _e( 'Post to edit', 'wp-cred' ); ?>',
									type: 'post',
									defaultValue: 'current'
								} 
							}
						}
					}
				}
			);
			#>
			<# print( data.templates.dialog( data ) ); #>
		</script>
		<script type="text/html" id="tmpl-cred-user-edit-form-template">
			<#
			data = _.extend( 
				data, 
				{
					attributes: {
						userSelection: {
							header: '<?php _e( 'User selection', 'wp-cred' ); ?>',
							fields: {
								user: { 
									label: '<?php _e( 'User to edit', 'wp-cred' ); ?>',
									type: 'user',
									defaultValue: 'current'
								} 
							}
						}
					}
				}
			);
			#>
			<# print( data.templates.dialog( data ) ); #>
		</script>
		<script type="text/html" id="tmpl-cred-delete-post-link-template">
			<#
			data = _.extend( 
				data, 
				{
					attributes: {
						options: {
							header: '<?php _e( 'Options', 'wp-cred' ); ?>',
							fields: {
								action: { 
									label: '<?php _e( 'Action to perform', 'wp-cred' ); ?>',
									type: 'radio',
									options: {
										trash: '<?php _e( 'Trash the post', 'wp-cred' ); ?>',
										delete: '<?php _e( 'Delete the post', 'wp-cred' ); ?>'
									},
									defaultForceValue: 'trash'
								},
								text: { 
									label: '<?php _e( 'Link text', 'wp-cred' ); ?>',
									type: 'text',
									required: true,
									defaultForceValue: '<?php _e( 'Delete %TITLE%', 'wp-cred' ); ?>'
								},
								redirect: {
									label: '<?php _e( 'Action after deleting the post', 'wp-cred' ); ?> ',
									type: 'radio',
									options: {
										'': '<?php _e( 'Do nothing', 'wp-cred' ); ?>',
										'reload': '<?php _e( 'Reload the current page', 'wp-cred' ); ?>',
										toolsetCombo: '<?php _e( 'Redirect to another page', 'wp-cred' ); ?>'
									},
									default: ''
								},
								'toolsetCombo:redirect': {
									type: 'ajaxSelect2',
									action: 'toolset_select2_suggest_posts_by_title',
									nonce: '',
									placeholder: '<?php _e( 'Search for a page to redirect to', 'wp-cred' ); ?>',
									hidden: true
								},
							}
						},
						messageOptions: {
							header: '<?php _e( 'Confirmation messages', 'wp-cred' ); ?>',
							fields: {
								message_show: {
									label: '<?php _e( 'Message before deleting', 'wp-cred' ); ?>',
									type: 'radio',
									options: {
										'0': '<?php _e( 'Do not show a message before deleting the post', 'wp-cred' ); ?>',
										toolsetCombo: '<?php _e( 'Show a message before deleting the post', 'wp-cred' ); ?>'
									},
									defaultValue: 'toolsetCombo'
								},
								'toolsetCombo:message_show': {
									type: 'text',
									defaultForceValue: '<?php _e( 'Are you sure you want to delete this post?', 'wp-cred' ); ?>'
								},
								message_after: {
									label: '<?php _e( 'Message after deleting', 'wp-cred' ); ?>',
									type: 'radio',
									options: {
										'': '<?php _e( 'Do not show a message after deleting the post', 'wp-cred' ); ?>',
										toolsetCombo: '<?php _e( 'Show a message after deleting the post', 'wp-cred' ); ?>'
									},
									defaultValue: ''
								},
								'toolsetCombo:message_after': {
									type: 'text',
									defaultForceValue: '<?php _e( 'Post deleted', 'wp-cred' ); ?>',
									hidden: true
								}
							}
						},
						styleOptions: {
							header: '<?php _e( 'Style options', 'wp-cred' ); ?>',
							fields: {
								class: { 
									label: '<?php _e( 'Class', 'wp-cred' ); ?>',
									type: 'text',
									description: '<?php _e( 'Space-separated list of classnames that will be added to the anchor HTML tag.', 'wp-cred' ); ?>'
								},
								style: { 
									label: '<?php _e( 'Style', 'wp-cred' ); ?>',
									type: 'text',
									description: '<?php _e( 'Inline styles that will be added to the anchor HTML tag.', 'wp-cred' ); ?>'
								}
							}
						},
						postSelection: {
							header: '<?php _e( 'Post selection', 'wp-cred' ); ?>',
							fields: {
								post: { 
									label: '<?php _e( 'Post to delete', 'wp-cred' ); ?>',
									type: 'post',
									defaultValue: 'current'
								} 
							}
						}
					}
				}
			);
			#>
			<# print( data.templates.dialog( data ) ); #>
		</script>
		<script type="text/html" id="tmpl-cred-create-child-post-link-template">
			<#
			data = _.extend( 
				data, 
				{
					attributes: {
						options: {
							header: '<?php _e( 'Options', 'wp-cred' ); ?>',
							fields: {
								form: { 
									label: '<?php _e( 'Page that contains the form', 'wp-cred' ); ?>',
									type: 'ajaxSelect2',
									action: 'toolset_select2_suggest_posts_by_title',
									nonce: '',
									placeholder: '<?php _e( 'Search for a target page', 'wp-cred' ); ?>',
									required: true
								},
								'parent_id': { 
									label: '<?php _e( 'Parent post', 'wp-cred' ); ?>',
									type: 'radio',
									options: {
										'': '<?php _e( 'The form includes the parent selector', 'wp-cred' ); ?>',
										'-1': '<?php _e( 'Set the parent according to the currently displayed content', 'wp-cred' ); ?>',
										toolsetCombo: '<?php _e( 'Choose a specific parent', 'wp-cred' ); ?>'
									},
									defaultValue: ''
								},
								'toolsetCombo:parent_id': {
									type: 'ajaxSelect2',
									action: 'toolset_select2_suggest_posts_by_title',
									nonce: '',
									placeholder: '<?php _e( 'Search for a parent post', 'wp-cred' ); ?>',
									hidden: true
								},
								text: { 
									label: '<?php _e( 'Link text', 'wp-cred' ); ?>',
									type: 'text',
									required: true,
									defaultForceValue: '<?php _e( 'Create new', 'wp-cred' ); ?>'
								},
								target: {
									label: '<?php _e( 'Open link in', 'wp-cred' ); ?>',
									type: 'select',
									options: {
										'_self': '<?php _e( 'Current window', 'wp-cred' ); ?>',
										'_top': '<?php _e( 'Parent window', 'wp-cred' ); ?>',
										'_blank': '<?php _e( 'New window', 'wp-cred' ); ?>'
									}
								}
							}
						},
						styleOptions: {
							header: '<?php _e( 'Style options', 'wp-cred' ); ?>',
							fields: {
								class: { 
									label: '<?php _e( 'Class', 'wp-cred' ); ?>',
									type: 'text',
									description: '<?php _e( 'Space-separated list of classnames that will be added to the anchor HTML tag.', 'wp-cred' ); ?>'
								},
								style: { 
									label: '<?php _e( 'Style', 'wp-cred' ); ?>',
									type: 'text',
									description: '<?php _e( 'Inline styles that will be added to the anchor HTML tag.', 'wp-cred' ); ?>'
								}
							}
						}
					}
				}
			);
			#>
			<# print( data.templates.dialog( data ) ); #>
		</script>
		<?php
	}
	
	/**
	 * ====================================
	 * Compatibility
	 * ====================================
	 */
	
	/**
	 * Gravity Forms compatibility.
	 *
	 * GF removes all assets from its admin pages, and offers a series of hooks to add your own to its whitelist.
	 * Those two callbacks are hooked to these filters.
	 *
	 * @param array $required_objects
	 *
	 * @return array
	 *
	 * @note This is not used because we disable CRED buttons in Gravity Forms notifications and confirmations.
	 *       Why would anyone want to put a CRED form inside those elements, anyway?
	 *
	 * @since 1.9.3
	 */
	/*
	function gform_noconflict_scripts( $required_objects ) {
		$required_objects[] = 'cred-shortcode';
		return $required_objects;
	}
	function gform_noconflict_styles( $required_objects ) {
		$required_objects[] = 'toolset-common';
		$required_objects[] = 'toolset-dialogs-overrides-css';
		$required_objects[] = 'onthego-admin-styles';
		return $required_objects;
	}
	*/

}