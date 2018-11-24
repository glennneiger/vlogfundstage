<?php

/**
 *
 *
 */
require_once 'class.textfield.php';

class WPToolset_Field_Skype extends WPToolset_Field_Textfield {

	protected $_defaults = array(
		'skypename' => '',
		'action' => 'chat',
		'color' => 'blue',
		'size' => 32,
	);

	public function init() {
		$this->set_placeholder_as_attribute();
	}

	public function metaform() {
		$value = wp_parse_args( $this->getValue(), $this->_defaults );
		$attributes = $this->getAttr();
		$shortcode_class = array_key_exists( 'class', $attributes ) ? $attributes['class'] : "";
		$attributes['class'] = "js-wpt-skypename js-wpt-cond-trigger regular-text {$shortcode_class}"; // What is this js-wpt-cond-trigger classname for?

		$wpml_action = $this->getWPMLAction();

		$form = array();
		$form[] = array(
			'#type' => 'textfield',
			'#title' => $this->getTitle(),
			'#description' => $this->getDescription(),
			'#name' => $this->getName() . "[skypename]",
			'#attributes' => array(),
			'#value' => $value['skypename'],
			'#validate' => $this->getValidationData(),
			'#attributes' => $attributes,
			'#repetitive' => $this->isRepetitive(),
			'wpml_action' => $wpml_action,
		);

		/**
		 * action
		 */
		$form[] = array(
			'#type' => 'hidden',
			'#value' => $value['action'],
			'#name' => $this->getName() . '[action]',
			'#attributes' => array( 'class' => 'js-wpt-skype-action' ),
		);

		/**
		 * color
		 */
		$form[] = array(
			'#type' => 'hidden',
			'#value' => $value['color'],
			'#name' => $this->getName() . '[color]',
			'#attributes' => array( 'class' => 'js-wpt-skype-color' ),
		);

		/**
		 * size
		 */
		$form[] = array(
			'#type' => 'hidden',
			'#value' => $value['size'],
			'#name' => $this->getName() . '[size]',
			'#attributes' => array( 'class' => 'js-wpt-skype-size' ),
		);

		if ( ! Toolset_Utils::is_real_admin() ) {
			return $form;
		}

		$wpcf_wpml_condition = defined( 'WPML_TM_VERSION' ) &&
			intval( $wpml_action ) === 1 &&
			function_exists( 'wpcf_wpml_post_is_original' ) &&
			function_exists( 'wpcf_wpml_have_original' ) &&
			! wpcf_wpml_post_is_original() &&
			wpcf_wpml_have_original();

		if (
			Toolset_Utils::is_real_admin() &&
			$wpcf_wpml_condition
		) {
			$button_element['#attributes']['disabled'] = 'disabled';
		}

		foreach ( $value as $key => $val ) {
			$button_element['#attributes'][ 'data-' . esc_attr( $key ) ] = $val;
		}
		$form[] = $button_element;

		return $form;
	}

	/**
	 * No edit dialog anymore, just keeping this to prevent any fatal error as it's public
	 *
	 * @deprecated 3.1
	 */
	public function editButtonTemplate() {
		echo '';
	}

	public function editform( $config = null ) {

	}

	public function mediaEditor() {
		return array();
	}

}
