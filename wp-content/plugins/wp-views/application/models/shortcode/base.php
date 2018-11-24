<?php
/**
 * Class WPV_Shortcode_Base
 *
 * @since 2.6.4
 */
abstract class WPV_Shortcode_Base implements WPV_Shortcode_Interface {
	abstract function get_value( $atts, $content );

	/**
	 * Gets the post, either the one with ID equal to the ID given as a parameter or its translation.
	 *
	 * @param $item_id
	 *
	 * @return array|null|WP_Post
	 */
	protected function get_post( $item_id ) {
		$item = get_post( $item_id );

		if ( null === $item ) {
			return null;
		}

		// Adjust for WPML support
		// If WPML is enabled, $item_id should contain the right ID for the current post in the current language
		// However, if using the id attribute, we might need to adjust it to the translated post for the given ID
		$item_id = apply_filters( 'translate_object_id', $item_id, $item->post_type, true, null );

		if ( $item_id !== $item->ID ) {
			$item = get_post( $item_id );
		}

		return $item;
	}
}
