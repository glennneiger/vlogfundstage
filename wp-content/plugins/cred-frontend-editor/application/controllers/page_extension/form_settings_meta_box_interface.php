<?php
/**
 * CRED Page Extension Form Settings Meta Box Interface
 *
 * @since 1.9.4
 */
interface CRED_Page_Extension_Form_Settings_Meta_Box_Interface {

	/**
	 * @param $form
	 * @param $args
	 */
	public function execute( $form, $args );
}