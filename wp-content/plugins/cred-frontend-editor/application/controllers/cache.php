<?php

/**
 * Caching system for Toolset CRED_Cache
 *
 * Currently, used to invalidate the cache of the known, published CRED forms,
 * used by the Toolset CRED shortcode generator.
 *
 * @since 1.9.3
 */
class CRED_Cache {
	
	public function initialize() {
		
		add_action( 'save_post',	array( $this, 'delete_shortcodes_gui_transients_action' ), 10, 2 );
		add_action( 'delete_post',	array( $this, 'delete_shortcodes_gui_transients_action' ), 10 );
		
	}
	
	/**
	 * Invalidate cred_transient_published_*** cache when:
	 * 	creating, updating or deleting a post form
	 * 	creating, updating or deleting an user form
	 *
	 *
	 * @since 1.9.3
	 */
	
	function delete_shortcodes_gui_transients_action( $post_id, $post = null  ) {
		if ( is_null( $post ) ) {
			$post = get_post( $post_id );
			if ( is_null( $post ) ) {
				return;
			}
		}
		$slugs = array( 'cred-form', 'cred-user-form' );
		if ( ! in_array( $post->post_type, $slugs ) ) {
			return;
		}
		switch ( $post->post_type ) {
			case 'cred-form':
				delete_transient( 'cred_transient_published_post_forms' );
				break;
			case 'cred-user-form':
				delete_transient( 'cred_transient_published_user_forms' );
				break;
			
		}
	}
	
}