<?php

class CRED_Field extends CRED_Field_Abstract {

	/**
	 * CRED_Field constructor.
	 *
	 * @param array $atts
	 * @param CRED_Form_Rendering $cred_form_rendering
	 * @param CRED_Helper $formHelper
	 * @param CRED_Form_Data $formData
	 * @param CRED_Translate_Field_Factory $translate_field_factory
	 */
	public function __construct( $atts, $cred_form_rendering, $formHelper, $formData, $translate_field_factory ) {
		parent::__construct( $atts, $cred_form_rendering, $formHelper, $formData, $translate_field_factory );
	}

	/**
	 * @return string
	 */
	public function get_field() {
		$formHelper = $this->_formHelper;
		$form = $this->_formData;
		$_fields = $form->getFields();
		$form_type = $_fields['form_settings']->form['type'];
		$post_type = $_fields['form_settings']->post['post_type'];

		$filtered_attributes = shortcode_atts( array(
			'class' => '',
			'post' => '',
			'field' => '',
			'value' => null,
			'urlparam' => '',
			'placeholder' => null,
			'escape' => false,
			'readonly' => false,
			'taxonomy' => null,
			'single_select' => null,
			'type' => null,
			'display' => null,
			'max_width' => null,
			'max_height' => null,
			'max_results' => null,
			'order' => null,
			'ordering' => null,
			'required' => false,
			//for parent select fields @deprecated since 1.9.1  use select_text
			'no_parent_text' => null,
			//Select default label for select/parent fields
			'select_text' => null,
			'use_select2' => null,
			'validate_text' => $formHelper->getLocalisedMessage( 'field_required' ),
			'show_popular' => false,
			'output' => '',
		), $this->_atts );

		$class = ( isset( $filtered_attributes['class'] ) ) ? $filtered_attributes['class'] : '';
		$post = ( isset( $filtered_attributes['post'] ) ) ? $filtered_attributes['post'] : '';
		$field = ( isset( $filtered_attributes['field'] ) ) ? $filtered_attributes['field'] : '';
		$value = ( isset( $filtered_attributes['value'] ) ) ? $filtered_attributes['value'] : false;
		$urlparam = ( isset( $filtered_attributes['urlparam'] ) ) ? $filtered_attributes['urlparam'] : '';
		$placeholder = ( isset( $filtered_attributes['placeholder'] ) ) ? $filtered_attributes['placeholder'] : null;
		$escape = ( isset( $filtered_attributes['escape'] ) ) ? $filtered_attributes['escape'] : false;
		$readonly = ( isset( $filtered_attributes['readonly'] ) ) ? $filtered_attributes['readonly'] : false;
		$taxonomy = ( isset( $filtered_attributes['taxonomy'] ) ) ? $filtered_attributes['taxonomy'] : null;
		$single_select = ( isset( $filtered_attributes['single_select'] ) ) ? $filtered_attributes['single_select'] : null;
		$type = ( isset( $filtered_attributes['type'] ) ) ? $filtered_attributes['type'] : null;
		$display = ( isset( $filtered_attributes['display'] ) ) ? $filtered_attributes['display'] : null;
		$max_width = ( isset( $filtered_attributes['max_width'] ) ) ? $filtered_attributes['max_width'] : null;
		$max_height = ( isset( $filtered_attributes['max_height'] ) ) ? $filtered_attributes['max_height'] : null;
		$max_results = ( isset( $filtered_attributes['max_results'] ) ) ? $filtered_attributes['max_results'] : null;
		$order = ( isset( $filtered_attributes['order'] ) ) ? $filtered_attributes['order'] : null;
		$ordering = ( isset( $filtered_attributes['ordering'] ) ) ? $filtered_attributes['ordering'] : null;
		$required = ( isset( $filtered_attributes['required'] ) ) ? $filtered_attributes['required'] : false;
		$no_parent_text = ( isset( $filtered_attributes['no_parent_text'] ) ) ? $filtered_attributes['no_parent_text'] : null;
		$select_text = ( isset( $filtered_attributes['select_text'] ) ) ? $filtered_attributes['select_text'] : null;
		$use_select2 = ( isset( $filtered_attributes['use_select2'] ) ) ? $filtered_attributes['use_select2'] : null;
		$validate_text = ( isset( $filtered_attributes['validate_text'] ) ) ? $filtered_attributes['validate_text'] : $formHelper->getLocalisedMessage( 'field_required' );
		$show_popular = ( isset( $filtered_attributes['show_popular'] ) ) ? $filtered_attributes['show_popular'] : false;
		$output = ( isset( $filtered_attributes['output'] ) ) ? $filtered_attributes['output'] : false;

		$field_object = false;
		$field_name = $field;

		/*
		 * result of this use fix_cred_field_shortcode_value_attribute_by_single_quote
		 */
		$value = str_replace( "@_cred_rsq_@", "'", $value );

		if ( $field == 'form_messages' ) {
			$post_not_saved_singular = str_replace( "%PROBLEMS_UL_LIST", "", $formHelper->getLocalisedMessage( 'post_not_saved_singular' ) );
			$post_not_saved_plural = str_replace( "%PROBLEMS_UL_LIST", "", $formHelper->getLocalisedMessage( 'post_not_saved_plural' ) );

			return '<div id="wpt-form-message-' . $form->getForm()->ID . '"
              data-message-single="' . esc_attr( $post_not_saved_singular ) . '"
              data-message-plural="' . esc_attr( $post_not_saved_plural ) . '"
              style="display:none;" class="wpt-top-form-error wpt-form-error alert alert-danger"></div><!CRED_ERROR_MESSAGE!>';
		}

		$escape = false;
		$readonly = (bool) ( strtoupper( $readonly ) === 'TRUE' );
		$required = (bool) ( strtoupper( $required ) === 'TRUE' );

		if ( ! $taxonomy ) {

			$translated_field = null;

			//Post Fields
			if (
				array_key_exists( 'post_fields', CRED_StaticClass::$out['fields'] ) &&
				is_array( CRED_StaticClass::$out['fields']['post_fields'] ) &&
				in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['post_fields'] ) )
			) {
				if ( $post != $post_type ) {
					return '';
				}

				$field = CRED_StaticClass::$out['fields']['post_fields'][ $field_name ];
				$name = $name_orig = $field['slug'];

				if ( ( ! isset( $placeholder )
						|| empty( $placeholder ) )
					&& isset( $field['data']['placeholder'] ) )
				{
					$placeholder = $field['data']['placeholder'];
				}

				if ( isset( $field['plugin_type_prefix'] ) ) {
					$name = $field['plugin_type_prefix'] . $name;
				}

				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				$additional_attributes = array(
					'class' => $class,
					'output' => $output,
					'preset_value' => $value,
					'urlparam' => $urlparam,
				);

				if ( in_array( $field['type'], array( 'credimage', 'image', 'file', 'credfile' ) ) ) {
					$additional_attributes['is_tax'] = false;
					$additional_attributes['max_width'] = $max_width;
					$additional_attributes['max_height'] = $max_height;
				} else {
					$additional_attributes['value_escape'] = $escape;
					$additional_attributes['make_readonly'] = $readonly;
					$additional_attributes['placeholder'] = $placeholder;
					$additional_attributes['select_text'] = $select_text;
				}

				//Do not delete this commented code, it is a new feature we will enable on the next future release
				//CRED_Select2_Utils::get_instance()->try_register_field_as_select2( $this->cred_form_rendering->html_form_id, $name, $field, $use_select2 );

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				/*
				 * check which fields are actually used in form
				 */
				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
				);

				//Custom Fields
			} elseif (
				array_key_exists( 'custom_fields', CRED_StaticClass::$out['fields'] )
				&& is_array( CRED_StaticClass::$out['fields']['custom_fields'] )
				&& in_array( strtolower( $field_name ), array_keys( CRED_StaticClass::$out['fields']['custom_fields'] ) )
			) {
				if ( $post != $post_type ) {
					return '';
				}

				$field = CRED_StaticClass::$out['fields']['custom_fields'][ $field_name ];

				$name = $name_orig = $field['slug'];

				if ( isset( $field['plugin_type_prefix'] ) ) {
					$name = $field['plugin_type_prefix'] . $name;
				}
				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				$additional_attributes = array(
					'class' => $class,
					'output' => $output,
					'preset_value' => $value,
					'urlparam' => $urlparam,
				);

				if ( in_array( $field['type'], array( 'credimage', 'image', 'file', 'credfile' ) ) ) {
					$additional_attributes['is_tax'] = false;
					$additional_attributes['max_width'] = $max_width;
					$additional_attributes['max_height'] = $max_height;
				} else {
					$additional_attributes['value_escape'] = $escape;
					$additional_attributes['make_readonly'] = $readonly;
					$additional_attributes['placeholder'] = $placeholder;
				}

				//Do not delete this commented code, it is a new feature we will enable on the next future release
				//CRED_Select2_Utils::get_instance()->try_register_field_as_select2( $this->cred_form_rendering->html_form_id, $name, $field, $use_select2 );

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				/*
				 * check which fields are actually used in form
				 */
				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
				);

				//Parents Fields
			} elseif (
				array_key_exists( 'parents', CRED_StaticClass::$out['fields'] )
				&& is_array( CRED_StaticClass::$out['fields']['parents'] )
				&& in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['parents'] ) )
			) {
				$field = CRED_StaticClass::$out['fields']['parents'][ $field_name ];
				$name = $name_orig = $field_name;
				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				/*
				 * Try to get placeholder by shortcode select_text attribute
				 * or by description if exists
				 */
				$field['placeholder'] = ( isset( $select_text ) ) ? $select_text : ( isset( $field['description'] ) ? $field['description'] : "" );

				$field['wpml_context'] = $form->getForm()->post_type . '-' . $form->getForm()->post_title . '-' . $form->getForm()->ID;

				$field['data']['validate'] = array();
				if ( $required ) {
					$field['data']['validate'] = array(
						'required' => array( 'message' => $validate_text, 'active' => 1 ),
					);
				}

				//Manage parent select field with select2
				$potential_parents = CRED_Select2_Utils::get_instance()->try_register_parent_as_select2( $this->cred_form_rendering->html_form_id, $field_name, $field, $max_results, $use_select2 );

				$field['data']['options'] = array();
				$default_option = null;
				/*
				 * enable setting parent form url param ([cred_child_link_form])
				 */
				if ( array_key_exists( 'parent_' . $field['data']['post_type'] . '_id', $_GET ) ) {
					$default_option = (int) $_GET[ 'parent_' . $field['data']['post_type'] . '_id' ];
				}

				foreach ( $potential_parents as $ii => $option ) {
					$option_id = (string) ( $option->ID );
					$field['data']['options'][ $option_id ] = array(
						'title' => $option->post_title,
						'value' => $option_id,
						'display_value' => $option_id,
					);
				}
				$field['data']['options']['default'] = $default_option;

				$additional_attributes = array(
					'preset_value' => $value,
					'urlparam' => $urlparam,
					'make_readonly' => $readonly,
					'max_width' => $max_width,
					'max_height' => $max_height,
					'class' => $class,
					'output' => $output,
					'select_text' => $select_text
				);

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				/*
				 * We need to register the current value of the select field
				 * that could have a default value from $_GET post_[parent]_id
				 */
				if ( null != $default_option
					&& get_post_type( $default_option ) !== $field['data']['post_type']
				) {
					$default_option = null;
					$html_form_id = $this->cred_form_rendering->html_form_id;
					$this->expected_parent_post_type = $field['data']['post_type'];

					add_action( 'cred_before_html_form_' . $html_form_id, array( $this, 'add_default_parent_post_type_top_error_message' ) );
				}

				$current_value = isset( $default_option ) && ( ! isset( $field_object['value'] ) || empty( $field_object['value'] ) ) ? $default_option : $field_object['value'];
				CRED_Select2_Utils::get_instance()->set_current_value_to_registered_select2_field( $this->cred_form_rendering->html_form_id, $field_name, $current_value, $field['data']['post_type'] );

				/*
				 * check which fields are actually used in form
				 */
				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
				);

				//Form Fields/User Fields
			} elseif (
				( array_key_exists( 'form_fields', CRED_StaticClass::$out['fields'] )
					&& is_array( CRED_StaticClass::$out['fields']['form_fields'] )
					&& in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['form_fields'] ) ) )
				|| ( array_key_exists( 'user_fields', CRED_StaticClass::$out['fields'] )
					&& is_array( CRED_StaticClass::$out['fields']['user_fields'] )
					&& in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['user_fields'] ) ) )
			) {
				$field = CRED_StaticClass::$out['fields']['form_fields'][ $field_name ];
				$name = $name_orig = $field_name;
				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				$additional_attributes = array(
					'preset_value' => $value,
					'urlparam' => $urlparam,
					'make_readonly' => $readonly,
					'max_width' => $max_width,
					'max_height' => $max_height,
					'class' => $class,
					'output' => $output,
					'placeholder' => $placeholder,
				);

				//Do not delete this commented code, it is a new feature we will enable on the next future release
				//CRED_Select2_Utils::get_instance()->try_register_field_as_select2( $this->cred_form_rendering->html_form_id, $name, $field, $use_select2 );

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				if ( $form_type == 'edit'
					&& ( $translated_field['name'] == 'user_pass' ||
						$translated_field['name'] == 'user_pass2' )
				) {
					if ( isset( $translated_field['data']['validate'] )
						&& isset( $translated_field['data']['validate']['required'] )
					) {
						unset( $translated_field['data']['validate']['required'] );
					}
				}

				// check which fields are actually used in form
				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
				);

				//Extra Fields
			} elseif (
				array_key_exists( 'extra_fields', CRED_StaticClass::$out['fields'] )
				&& is_array( CRED_StaticClass::$out['fields']['extra_fields'] )
				&& in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['extra_fields'] ) )
			) {
				$field = CRED_StaticClass::$out['fields']['extra_fields'][ $field_name ];
				$name = $name_orig = $field['slug'];
				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				$additional_attributes = array(
					'preset_value' => $value,
					'urlparam' => $urlparam,
					'make_readonly' => $readonly,
					'max_width' => $max_width,
					'max_height' => $max_height,
					'class' => $class,
					'output' => $output,
					'placeholder' => $placeholder,
				);

				//Do not delete this commented code, it is a new feature we will enable on the next future release
				//CRED_Select2_Utils::get_instance()->try_register_field_as_select2( $this->cred_form_rendering->html_form_id, $name, $field, $use_select2 );

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				// check which fields are actually used in form
				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
				);

				//Taxonomy Fields
			} elseif (
				array_key_exists( 'taxonomies', CRED_StaticClass::$out['fields'] )
				&& is_array( CRED_StaticClass::$out['fields']['taxonomies'] )
				&& in_array( $field_name, array_keys( CRED_StaticClass::$out['fields']['taxonomies'] ) )
			) {
				$field = CRED_StaticClass::$out['fields']['taxonomies'][ $field_name ];
				$name = $name_orig = $field['name'];
				// check which fields are actually used in form
				$field['form_html_id'] = $this->_translate_field_factory->get_html_form_field_id( $field );

				$single_select = ( $single_select === 'true' );
				$additional_attributes = array(
					'preset_value' => $display,
					'is_tax' => true,
					'single_select' => $single_select,
					'show_popular' => $show_popular,
					'placeholder' => $placeholder,
					'class' => $class,
					'output' => $output,
				);

				$field_object = $this->_translate_field_factory->cred_translate_field( $name, $field, $additional_attributes );

				CRED_StaticClass::$out['form_fields'][ $name_orig ] = $this->get_uniformed_field($field, $field_object);
				CRED_StaticClass::$out['form_fields_info'][ $name_orig ] = array(
					'type' => $field['type'],
					'repetitive' => ( isset( $field['data']['repetitive'] ) && $field['data']['repetitive'] ),
					'plugin_type' => ( isset( $field['plugin_type'] ) ) ? $field['plugin_type'] : '',
					'name' => $name,
					'display' => $value,
				);
			}

			if ( $field_object ) {
				return $this->cred_form_rendering->renderField( $field_object );
			} elseif ( current_user_can( 'manage_options' ) ) {
				return sprintf(
					'<p class="alert">%s</p>', sprintf(
						__( 'There is a problem with %s field. Please check CRED form.', 'wp-cred' ), $field
					)
				);
			}

			//is Taxonomy
		} else {

			//Taxonomy Fields
			if (
				array_key_exists( 'taxonomies', CRED_StaticClass::$out['fields'] ) &&
				is_array( CRED_StaticClass::$out['fields']['taxonomies'] ) &&
				in_array( $taxonomy, array_keys( CRED_StaticClass::$out['fields']['taxonomies'] ) ) &&
				in_array( $type, array( 'show_popular', 'add_new' ) )
			) {
				if ( // auxilliary field type matches taxonomy type
					( $type == 'show_popular' && ! CRED_StaticClass::$out['fields']['taxonomies'][ $taxonomy ]['hierarchical'] ) ||
					( $type == 'add_new' && CRED_StaticClass::$out['fields']['taxonomies'][ $taxonomy ]['hierarchical'] )
				) {
					// add a placeholder for the 'show_popular' or 'add_new' buttons.
					// the real buttons will be copied to this position via js
					// added data-label text from value shortcode attribute
					switch ( $type ) {
						case 'show_popular':
							return '<div class="js-taxonomy-button-placeholder" data-taxonomy="' . esc_attr( $taxonomy ) . '" data-label="' . esc_attr( $value ) . '" style="display:none"></div>';
						case 'add_new':
							return '<div class="js-taxonomy-hierarchical-button-placeholder" data-taxonomy="' . esc_attr( $taxonomy ) . '" data-label="' . esc_attr( $value ) . '" style="display:none"></div>';
					}
				}
			}

		}

		return '';
	}

	/**
	 * Private function used to get a uniformed array object with standard key values
	 *
	 * @param $field
	 * @param $field_object
	 *
	 * @return array
	 *
	 * @since 1.9.3
	 */
	private function get_uniformed_field( $field, $field_object ) {
		$uniformed_field_array = array(
			'name' => $field['name'],
			'type' => $field['type'],
			'id' => isset( $field['id'] ) ? $field['id'] : $field['name'],
			'slug' => isset( $field['slug'] ) ? $field['slug'] : $field['name'],
			'title' => isset( $field_object['title'] ) ? $field_object['title'] : $field['name'],
			'label' => isset( $field_object['title'] ) ? $field_object['title'] : $field['name'],
			'value' => isset( $field_object['value'] ) ? $field_object['value'] : '',
			'attr' => $field_object['attr'],
			'data' => $field_object['data'],
			'form_html_id' => $field['form_html_id'],
		);
		if ( isset( $field_object['attr'] ) ) {
			$uniformed_field_array['attr'] = $field_object['attr'];
		}
		if ( isset( $field_object['data'] ) ) {
			$uniformed_field_array['data'] = $field_object['data'];
		}

		return $uniformed_field_array;
	}

	/**
	 * Print a specific alert message when parent post_id has wrong post_type
	 *
	 * @since 1.9.4
	 */
	public function add_default_parent_post_type_top_error_message() {
		echo "<div class='alert alert-danger'>" . ( isset( $this->expected_parent_post_type ) && ! empty( $this->expected_parent_post_type ) ? __( sprintf( 'Could not set the parent post because it has the wrong type. The parent for this post should be of type %s.', $this->expected_parent_post_type ), 'wp-cred' ) : __( 'Could not set the parent post because it has the wrong type.', 'wp-cred' ) ) . "</div>";
	}

}
