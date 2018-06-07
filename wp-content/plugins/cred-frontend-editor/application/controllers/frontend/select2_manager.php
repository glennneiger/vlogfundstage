<?php

/**
 * Class Select2 Frontend Manager will handle and replace all the select fields that have to become
 * select2 with ajax/not ajax feature
 *
 * @since 1.9.3
 */
class CRED_Frontend_Select2_Manager {

	const SELECT2_SHORTCODE_NEVER = 'never';

	const SELECT2_SHORTCODE_ALWAYS = 'always';

	const SELECT2_SHORTCODE_ONMANY = 'on_many';

	const SELECT2_PARENTS = 'select2_potential_parents';

	const SELECT2_POSTS = 'select2_potential_posts';

	private static $instance;

	/**
	 * Array that contains all the form fields select that have to be replaced by select2 Ajax
	 *
	 * @var array
	 */
	protected $select2_fields_list;

	protected function __construct() {
		$this->register_select2_wp_ajax_callbacks();

		if ( ! cred_is_ajax_call() ) {
			$this->select2_fields_list = array();
			//Once all fields are elaborated i can localize select2 scripts
			add_action( 'wp_footer', array( &$this, 'localize_frontend_select2_script' ) );
		}
	}

	/**
	 * Function to register the select2 main ajax callbacks
	 */
	protected function register_select2_wp_ajax_callbacks() {
		add_action( 'wp_ajax_' . self::SELECT2_PARENTS, array( $this, 'get_potential_parents' ) );
		add_action( 'wp_ajax_nopriv_' . self::SELECT2_PARENTS, array( $this, 'get_potential_parents' ) );
		add_action( 'wp_ajax_' . self::SELECT2_POSTS, array( $this, 'get_potential_posts' ) );
		add_action( 'wp_ajax_nopriv_' . self::SELECT2_POSTS, array( $this, 'get_potential_posts' ) );
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return array
	 */
	public function get_select2_fields_list() {
		return $this->select2_fields_list;
	}

	public function empty_select2_fields_list() {
		$this->select2_fields_list = array();
	}

	/**
	 * @param $html_form_id
	 * @param $field_name
	 * @param $select2_args {action,parameter,placeholder,other_args related to select2 options like multiple}
	 */
	public function register_field_to_select2_list( $html_form_id, $field_name, $select2_args ) {
		if ( ! isset( $this->select2_fields_list[ $html_form_id ] ) ) {
			$this->select2_fields_list[ $html_form_id ] = array();
		}
		if ( ! isset( $this->select2_fields_list[ $html_form_id ][ $field_name ] ) ) {
			$this->select2_fields_list[ $html_form_id ][ $field_name ] = array();
		}
		$this->select2_fields_list[ $html_form_id ][ $field_name ] = $select2_args;
	}

	/**
	 * @param string $html_form_id
	 * @param string $field_name
	 * @param int|string $value
	 * @param string $post_type
	 */
	public function set_current_value_to_registered_select2_field( $html_form_id, $field_name, $value, $post_type ) {
		if ( ! isset( $value )
			|| empty( $value ) ) {
			return;
		}
		if ( isset( $this->select2_fields_list[ $html_form_id ][ $field_name ] ) ) {
			$post = CRED_Field_Utils::get_instance()->get_parent_by_post_id( $value );
			if ( isset( $post )
				&& ! empty( $post ) ) {
				$this->select2_fields_list[ $html_form_id ][ $field_name ]['current_option'] = $this->get_option_value_by_post( $post );
			}
		}
	}


	/**
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	protected function get_option_value_by_post( $post ) {
		return array( 'value' => esc_attr( $post->ID ), 'text' => sanitize_text_field( $post->post_title ) );
	}

	/**
	 * Enqueue main file manager javascript with toolset_select2 lib dependency
	 */
	public function localize_frontend_select2_script() {
		wp_localize_script( 'cred-select2-frontend-js', 'cred_select2_frontend_settings',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'select2_fields_list' => $this->select2_fields_list,
			)
		);
	}

	/**
	 * WP_Ajax Callback to get parents elements with Join WPML translations
	 */
	public function get_potential_parents() {
		if ( ! isset( $_POST['parameter'] ) ) {
			$data = array(
				'type' => 'parameter',
				'message' => __( 'Wrong or missing post_type.', 'wpv-views' ),
			);
			wp_send_json_error( $data );
		}

		$query = cred_wrap_esc_like( $_POST['q'] );
		$post_type = sanitize_text_field( $_POST['parameter'] );
		$wpml_context = sanitize_text_field( $_POST['wpml_context'] );
		$wpml_name = sanitize_text_field( $_POST['wpml_name'] );

		/**
		 * cred_select2_ajax_get_potential_parents_query_limit
		 *
		 * Filter used to handle the limit of get potential parents query
		 * during an ajax call by a select2 ajax component
		 *
		 * @param int $limit
		 *
		 * @since 1.9.4
		 */
		$limit = apply_filters( 'cred_select2_ajax_get_potential_parents_query_limit', 20 );

		$potential_parents = CRED_Field_Utils::get_instance()->get_potential_parents( $post_type, $wpml_name, $wpml_context, $limit, $query );

		$this->print_select2_json_success_output( $potential_parents );
	}

	/**
	 * Get array of WP_Objects list and print select2 compatible output
	 *
	 * @param WP_Post[] $wp_items
	 */
	protected function print_select2_json_success_output( $wp_items ) {
		$output_results = array();
		if ( is_array( $wp_items ) && ! empty( $wp_items ) ) {
			foreach ( $wp_items as $item ) {
				$output_results[] = array(
					'text' => $item->post_title,
					'id' => $item->ID,
				);
			}
		}

		wp_send_json_success( $output_results );
	}

	/**
	 * WP_Ajax Callback to get posts elements
	 */
	public function get_potential_posts() {
		if ( ! isset( $_POST['parameter'] ) ) {
			$data = array(
				'type' => 'parameter',
				'message' => __( 'Wrong or missing post_type.', 'wpv-views' ),
			);
			wp_send_json_error( $data );
		}

		$query = sanitize_text_field( $_POST['q'] );
		$post_type = sanitize_text_field( $_POST['parameter'] );

		/**
		 * cred_select2_ajax_get_potential_posts_query_limit
		 *
		 * Filter used to handle the limit of get potential parents query
		 * during an ajax call by a select2 ajax component
		 *
		 * @param int $limit
		 *
		 * @since 1.9.4
		 */
		$limit = apply_filters( 'cred_select2_ajax_get_potential_posts_query_limit', 20 );

		$potential_posts = CRED_Field_Utils::get_instance()->get_potential_posts( $post_type, $limit, $query );

		$this->print_select2_json_success_output( $potential_posts );
	}

	/**
	 * Core Method that elaborate the decision to use select2 or not
	 * depending by the shortcode attribute {always,never,on_many} and if there are many options
	 *
	 * @param string $shortcode use_select2 field shortcode: {always,never,on_many}
	 * @param bool $many_options
	 *
	 * @return bool
	 */
	public function use_select2( $shortcode, $many_options = true ) {
		//Default behavior when attribute use_select2 is not present in the shortcode is intended as null
		if ( ! isset( $shortcode )
			&& $many_options ) {
			return true;
		}

		if ( $shortcode == self::SELECT2_SHORTCODE_NEVER ) {
			return false;
		}

		if ( $shortcode == self::SELECT2_SHORTCODE_ALWAYS ) {
			return true;
		}

		return ( $many_options );
	}

	/**
	 * @param string $field_type
	 *
	 * @return bool
	 */
	public function is_valid_field_type_for_select2( $field_type ) {
		return ( $field_type === 'select' || $field_type === 'multiselect' );
	}
}