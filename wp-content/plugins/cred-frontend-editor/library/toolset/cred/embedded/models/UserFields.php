<?php

/**
 * Cred fields model gets custom fields for post types
 */
class CRED_User_Fields_Model extends CRED_Fields_Abstract_Model implements CRED_Singleton {

    public function __construct() {
        parent::__construct();
    }

	/**
	 * @param array $exclude_fields
	 *
	 * @return mixed
	 */
    public function getCustomFieldsList($exclude_fields = array()) {
        $exclude = array('_edit_last', '_edit_lock', '_wp_old_slug', '_thumbnail_id', '_wp_page_template');
        if ( !empty( $exclude_fields ) ) {
            $exclude = array_merge( $exclude, $exclude_fields );
        }

        $exclude = "'" . implode( "','", $exclude ) . "'"; //wrap in quotes
        $sql = $this->wpdb->prepare( 
			"SELECT pm.meta_key 
			FROM {$this->wpdb->usermeta} as pm INNER JOIN {$this->wpdb->users} as p
            ON pm.user_id = p.ID
            AND pm.meta_key NOT IN ({$exclude})
            AND pm.meta_key NOT LIKE %s 
            AND pm.meta_key NOT LIKE %s 
			GROUP BY pm.meta_key", 
			array( "wpcf-%", "\_%" ) 
		);

        $fields = $this->wpdb->get_col( $sql );

        return $fields;
    }

	/**
	 * @param $post_type
	 * @param array $exclude_fields
	 * @param bool $show_private
	 * @param $paged
	 * @param int $perpage
	 * @param string $orderby
	 * @param string $order
	 *
	 * @return mixed
	 */
    public function getPostTypeCustomFields($post_type, $exclude_fields = array(), $show_private = true, $paged, $perpage = 10, $orderby = 'meta_key', $order = 'asc') {
        /*
          TODO:
          make search incremental to avoid large data issues
         */

        //get custom fields not managed by Types
        //added not like wpcf-%
        $exclude = array('_edit_last', '_edit_lock', '_wp_old_slug', '_thumbnail_id', '_wp_page_template', 'first_name', 'last_name', 'nickname', 'description');
	    if ( ! empty( $exclude_fields ) ) {
		    $exclude = array_merge( $exclude, $exclude_fields );
	    }

	    $exclude = "'" . implode( "','", $exclude ) . "'"; //wrap in quotes

        if ( $paged < 0 ) {
	        if ( $show_private ) {
		        $sql = $this->wpdb->prepare( 
					"SELECT COUNT(DISTINCT(pm.meta_key)) 
					FROM {$this->wpdb->usermeta} as pm, {$this->wpdb->users} as p
					WHERE pm.user_id = p.ID
					AND pm.meta_key NOT IN ({$exclude})
					AND pm.meta_key NOT LIKE %s", 
					"wpcf-%" 
				);
	        } else {
		        $sql = $this->wpdb->prepare( 
					"SELECT COUNT(DISTINCT(pm.meta_key)) 
					FROM {$this->wpdb->usermeta} as pm, {$this->wpdb->users} as p
					WHERE pm.user_id = p.ID
					AND pm.meta_key NOT IN ({$exclude})
					AND pm.meta_key NOT LIKE %s 
					AND pm.meta_key NOT LIKE %s",
					array( "wpcf-%", "\_%" ) 
				);
	        }

            return $this->wpdb->get_var( $sql );
        }

        $paged = intval( $paged );
        $perpage = intval( $perpage );
        $paged--;
        $order = strtoupper( $order );
	    if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
		    $order = 'ASC';
	    }
	    if ( ! in_array( $orderby, array( 'meta_key' ) ) ) {
		    $orderby = 'meta_key';
	    }

	    if ( $show_private ) {
		    $sql = $this->wpdb->prepare( 
				"SELECT DISTINCT(pm.meta_key) 
				FROM {$this->wpdb->usermeta} as pm, {$this->wpdb->users} as p
				WHERE pm.user_id = p.ID
				AND pm.meta_key NOT IN ({$exclude})
				AND pm.meta_key NOT LIKE %s 
				ORDER BY pm.{$orderby} {$order}
				LIMIT " . ( $paged * $perpage ) . ", " . $perpage, 
				"wpcf-%" 
			);
	    } else {
		    $sql = $this->wpdb->prepare( 
				"SELECT DISTINCT(pm.meta_key) 
				FROM {$this->wpdb->usermeta} as pm, {$this->wpdb->users} as p
				WHERE pm.user_id = p.ID
				AND pm.meta_key NOT IN ({$exclude})
				AND pm.meta_key NOT LIKE %s 
				AND pm.meta_key NOT LIKE %s
				ORDER BY pm.{$orderby} {$order}
				LIMIT " . ( $paged * $perpage ) . ", " . $perpage, 
				array( "wpcf-%", "\_%" )
			);
	    }

        $fields = $this->wpdb->get_col( $sql );

        return $fields;
    }

	/**
	 * Function responsible to create correct User Fields array structure for CRED
	 * getting all the Types Fields and Types Controlled ones
	 * NOTE: Types controlled fields do not have prefix 'wpcf-'
	 *
	 * @param string $role
	 * @param string $type_form
	 *
	 * @return array
	 */
    public function getCustomFields($role = "", $type_form = "") {
        $fields = array();
        //return get_option('wpcf-usermeta', false);
        $isTypesActive = defined( 'WPCF_VERSION' );
        if ( $isTypesActive ) {

            if ( defined( 'WPCF_EMBEDDED_ABSPATH' ) ) {
                require_once WPCF_EMBEDDED_ABSPATH . '/includes/usermeta-post.php';
                $_type_fields_groups = wpcf_admin_usermeta_get_groups_fields();
            }

            //Getting all types user meta fields
            if ( isset( $_type_fields_groups ) && !empty( $_type_fields_groups ) ) {
                foreach ( $_type_fields_groups as $n => $usermeta_field ) {
                    if ( $usermeta_field['is_active'] == 1 ) {
                        if (
                                (
                                isset( $usermeta_field['_wp_types_group_showfor'] ) &&
                                (
                                (is_array( $usermeta_field['_wp_types_group_showfor'] ) && in_array( $role, $usermeta_field['_wp_types_group_showfor'] )) ||
                                (is_string( $usermeta_field['_wp_types_group_showfor'] ) && 'all' == $usermeta_field['_wp_types_group_showfor'])
                                )
                                ) || (empty( $role ) || $type_form == 'edit') ) {
                            $fields = array_merge( $fields, $usermeta_field['fields'] );
                        }
                    }
                }
            }

            $fields = (isset( $fields ) && !empty( $fields )) ? $fields : array();

            $_all_option_wpcf_fields = get_option( 'wpcf-usermeta', false );
            if ( !empty( $_all_option_wpcf_fields ) ) {
                foreach ( $_all_option_wpcf_fields as $key => $usermeta_field ) {
                    if ( isset( $usermeta_field['data']['controlled'] ) && $usermeta_field['data']['controlled'] == 1 ) {
                        //use slug because of key sensisitive
                        $fields = array_merge( $fields, array( $usermeta_field['slug'] => $usermeta_field) );
                    }
                }
            }

            $plugin = 'types';
            if ( !empty( $fields ) ) {
                foreach ( $fields as $key => $field ) {
                    $fields[$key]['post_labels'] = "$key";
                    $fields[$key]['post_type'] = 'user';
                    $fields[$key]['plugin_type'] = $plugin;
	                $is_types_controlled = ( isset( $field['data']['controlled'] ) && (bool) $field['data']['controlled'] );
                    /*
                     * Types Controlled Fields do not have prefix wpcf-
                     */
	                $fields[ $key ]['plugin_type_prefix'] = ( ! $is_types_controlled ) ? 'wpcf-' : '';
                }
            }
        }

        $fields = (isset( $fields ) && !empty( $fields )) ? $fields : array();

        $all_custom_field_names = $this->getCustomFieldsList();
        $custom_field_names = array_diff( $all_custom_field_names, array('first_name', 'last_name', 'nickname', 'description') );
        $_all_option_custom_cred_fields = get_option( self::CUSTOM_FIELDS_OPTION, false );

        if ( isset( $_all_option_custom_cred_fields ) && isset( $_all_option_custom_cred_fields['cred-user-form'] ) ) {
            foreach ( $_all_option_custom_cred_fields['cred-user-form'] as $key => $usermeta_field ) {
                if ( !in_array( $key, $custom_field_names ) ) {
                    continue;
                }

                if ( isset( $usermeta_field['cred_custom'] ) && $usermeta_field['cred_custom'] == 1 ) {
                    $newf = array();
                    //use slug because of key sensisitive
                    $newf[$usermeta_field['slug']] = $usermeta_field;
                    $fields = array_merge( $fields, $newf );
                }
            }
        }

        $plugin = 'cred';
        if ( !empty( $fields ) ) {
            foreach ( $fields as $key => $field ) {
                if ( isset( $fields[$key]['plugin_type'] ) &&
                        $fields[$key]['plugin_type'] == 'types' ) {
                    continue;
                }

                if ( isset( $fields[$key]['_cred_ignore'] ) && $fields[$key]['_cred_ignore'] == 1 ) {
                    continue;
                }

                $fields[$key]['post_labels'] = "$key";
                $fields[$key]['post_type'] = 'user';
                $fields[$key]['plugin_type'] = $plugin;
                $fields[$key]['plugin_type_prefix'] = ''; //'cred-';
                $fields[$key]['meta_key'] = $fields[$key]['plugin_type_prefix'] . $key;
            }
        }

        $usermetas = $all_custom_field_names;

        if ( !empty( $usermetas ) ) {
            //$usermetas = json_decode(json_encode($usermetas), true);
            $to_include = array('first_name', 'last_name', 'description');

            foreach ( $usermetas as $n => $meta ) {
                $metas['meta_key'] = $meta;
                if ( !in_array( $metas['meta_key'], $to_include ) ) {
                    continue;
                }

                if ( !isset( $fields[$metas['meta_key']] ) ) {
                    $fields[$metas['meta_key']] = array();
                }

                $fields[$metas['meta_key']]['id'] = $metas['meta_key'];
                $fields[$metas['meta_key']]['slug'] = $metas['meta_key'];
                $fields[$metas['meta_key']]['type'] = ($metas['meta_key'] == 'description') ? 'wysiwyg' : 'textfield';
                $fields[$metas['meta_key']]['name'] = ($metas['meta_key'] == 'description') ? 'Biographical Info' : ucwords( str_replace( "_", " ", $metas['meta_key'] ) );
                $fields[$metas['meta_key']]['data'] = '';
                $fields[$metas['meta_key']]['meta_key'] = $metas['meta_key'];
                $fields[$metas['meta_key']]['post_type'] = 'user';
                $fields[$metas['meta_key']]['meta_type'] = 'usermeta';
                $fields[$metas['meta_key']]['post_labels'] = $fields[$metas['meta_key']]['name'];
                $fields[$metas['meta_key']]['plugin_type'] = "";
                $fields[$metas['meta_key']]['plugin_type_prefix'] = "";
            }
        }

        return $fields;
    }

	/**
	 * Function that gets all the custom fields related to User Forms by CRED and Types
	 *
	 * @param array $autogenerate
	 * @param string $role
	 * @param string $type_form
	 * @param bool $add_default
	 * @param null|callable $localized_message_callback     Function used to translate using extra message by message_id like 'field_required' or similar. Usually the function used is CRED_Form_Builder_Helper::getLocalisedMessage($id) the $id that is a string (ex.'field_required') and returns a string translated
	 *
	 * @return array
	 */
    public function getFields($autogenerate = array('username' => true, 'nickname' => true, 'password' => true), $role = "", $type_form = "", $add_default = true, $localized_message_callback = null) {
        // ALL FIELDS
        $fields_all = array();

        // fetch custom fields for post type even if not created by types or default
        $groups = array();
        $groups_conditions = array();

        $fields = $this->getCustomFields( $role, $type_form );

        //Check this because is working fine in post without it
        foreach ( $fields as $k => &$field ) {
            if ( isset( $field['_cred_ignore'] ) && $field['_cred_ignore'] == 1 ) {
                unset( $fields[$k] );
                continue;
            }
            if ( isset( $field['plugin_type'] ) && $field['plugin_type'] == 'types' ) {
                if ( isset( $field['data']['validate']['required']['message'] ) ) {
                    $mym = $field['data']['validate']['required']['message'];
                    if ( $localized_message_callback ) {
                        $mym = call_user_func( $localized_message_callback, 'field_required' );
                    }
                    $field['data']['validate']['required']['message'] = $mym;
                }
                if ( isset( $field['data']['validate']['number']['message'] ) ) {
                    $mym = $field['data']['validate']['number']['message'];
                    if ( $localized_message_callback ) {
                        $mym = call_user_func( $localized_message_callback, 'enter_valid_number' );
                    }
                    $field['data']['validate']['number']['message'] = $mym;
                }
                if ( isset( $field['data']['validate']['url']['message'] ) ) {
                    $mym = $field['data']['validate']['url']['message'];
                    if ( $localized_message_callback ) {
                        $mym = call_user_func( $localized_message_callback, 'enter_valid_url' );
                    }
                    $field['data']['validate']['url']['message'] = $mym;
                }
            }
        }

        //#########################################################################################

        $user_fields = array();

        if ( $add_default ) {

            if ( $localized_message_callback ) {
                $required_message = call_user_func( $localized_message_callback, 'field_required' );
                $equalto_message = call_user_func( $localized_message_callback, 'passwords_do_not_match' );
            } else {
                $required_message = __( 'This field is required', 'wp-cred' );
                $equalto_message = __( 'Passwords do not match', 'wp-cred' );
            }

            $expression_user = isset( $autogenerate['username'] ) && ( (bool) $autogenerate['username'] !== true || $autogenerate['username'] === 'false');
            $expression_nick = isset( $autogenerate['nickname'] ) && ( (bool) $autogenerate['nickname'] !== true || $autogenerate['nickname'] === 'false');
            $expression_pawwsd = isset( $autogenerate['password'] ) && ( (bool) $autogenerate['password'] !== true || $autogenerate['password'] === 'false');

            if ( $expression_user === true ) {
                $user_fields['user_login'] = array('post_type' => 'user', 'post_labels' => __( 'Username', 'wp-cred' ), 'id' => 'user_login', 'wp_default' => true, 'slug' => 'user_login', 'type' => 'textfield', 'name' => __( 'Username', 'wp-cred' ), 'description' => 'Username', 'data' => array('repetitive' => 0, 'validate' => array('required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
            }

            if ( $expression_nick === true ) {
                $user_fields['nickname'] = array('post_type' => 'user', 'post_labels' => __( 'Nickname', 'wp-cred' ), 'id' => 'nickname', 'wp_default' => true, 'slug' => 'nickname', 'type' => 'textfield', 'name' => __( 'Nickname', 'wp-cred' ), 'description' => 'Nickname', 'data' => array('repetitive' => 0, 'validate' => array('required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
            }

            if ( $expression_pawwsd === true ) {
                $user_fields['user_pass'] = array('post_type' => 'user', 'post_labels' => __( 'Password', 'wp-cred' ), 'id' => 'user_pass', 'wp_default' => true, 'slug' => 'user_pass', 'type' => 'password', 'name' => __( 'Password', 'wp-cred' ), 'description' => 'Password', 'data' => array('repetitive' => 0, 'validate' => array('required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
                $user_fields['user_pass2'] = array('post_type' => 'user', 'post_labels' => __( 'Repeat Password', 'wp-cred' ), 'id' => 'user_pass2', 'wp_default' => true, 'slug' => 'user_pass2', 'type' => 'password', 'name' => __( 'Repeat Password', 'wp-cred' ), 'description' => 'Repeat Password', 'data' => array('repetitive' => 0, 'validate' => array('equalto' => array('active' => 1, 'args' => array('$value' => 'user_pass'), 'message' => $equalto_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
            }

            $mail_localized_message = __( 'Please enter a valid email address', 'wp-cred' );
            if ( $localized_message_callback ) {
                $mail_localized_message = call_user_func( $localized_message_callback, 'enter_valid_email' );
            }
            $user_fields['user_email'] = array('post_type' => 'user', 'post_labels' => __( 'Email', 'wp-cred' ), 'id' => 'user_email', 'wp_default' => true, 'slug' => 'user_email', 'type' => 'email', 'name' => __( 'Email', 'wp-cred' ), 'description' => 'Email', 'data' => array('repetitive' => 0, 'validate' => array('email' => array('active' => 1, 'message' => $mail_localized_message), 'required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
            $user_fields['user_url'] = array('post_type' => 'user', 'post_labels' => __( 'Website', 'wp-cred' ), 'id' => 'user_url', 'wp_default' => true, 'slug' => 'user_url', 'type' => 'textfield', 'name' => __( 'Website', 'wp-cred' ), 'description' => 'Url', 'data' => array(/* 'repetitive' => 0, 'validate' => array ( 'required' => array ( 'active' => 1, 'value' => true, 'message' => __('This field is required','wp-cred') ) ), 'conditional_display' => array ( ), 'disabled_by_type' => 0 */));
        }

        $parents = array();

        // EXTRA FIELDS
        $extra_fields = array();
        $extra_fields['recaptcha'] = array('id' => 're_captcha', 'slug' => 'recaptcha', 'name' => esc_js( __( 'reCaptcha', 'wp-cred' ) ), 'type' => 'recaptcha', 'cred_builtin' => true, 'description' => esc_js( __( 'Adds Image Captcha to your forms to prevent automatic submision by bots', 'wp-cred' ) ));
        $setts = CRED_Loader::get( 'MODEL/Settings' )->getSettings();
        if (
                !isset( $setts['recaptcha']['public_key'] ) ||
                !isset( $setts['recaptcha']['private_key'] ) ||
                empty( $setts['recaptcha']['public_key'] ) ||
                empty( $setts['recaptcha']['private_key'] )
        ) {
            // no keys set for API
            $extra_fields['recaptcha']['disabled'] = true;
            $extra_fields['recaptcha']['disabled_reason'] = sprintf( '<a href="%s" target="_blank">%s</a> %s', CRED_CRED::$settingsPage, __( 'Get and Enter your API keys', 'wp-cred' ), esc_js( __( 'to use the Captcha field.', 'wp-cred' ) ) );
        }

        // featured image field
        $extra_fields['_featured_image'] = array('id' => '_featured_image', 'slug' => '_featured_image', 'name' => esc_js( __( 'Featured Image', 'wp-cred' ) ), 'type' => 'image', 'cred_builtin' => true, 'description' => 'Featured Image');
        $extra_fields['_featured_image']['supports'] = false;

        // BASIC FORM FIELDS
        $form_fields = array();
        $form_fields['form'] = array('id' => 'creduserform', 'name' => esc_js( __( 'User Form Container', 'wp-cred' ) ), 'slug' => 'creduserform', 'type' => 'creduserform', 'cred_builtin' => true, 'description' => esc_js( __( 'User Form (required)', 'wp-cred', 'wp-cred' ) ));
        //$form_fields['form_end']=array('id'=>'form_end','name'=>'Form End','slug'=>'form_end','type'=>'form_end','cred_builtin'=>true,'description'=>__('End of Form'));
        $form_fields['form_submit'] = array('value' => __( 'Submit', 'wp-cred' ), 'id' => 'form_submit', 'name' => esc_js( __( 'Form Submit', 'wp-cred' ) ), 'slug' => 'form_submit', 'type' => 'form_submit', 'cred_builtin' => true, 'description' => esc_js( __( 'Form Submit Button', 'wp-cred' ) ));
        $form_fields['form_messages'] = array('value' => '', 'id' => 'form_messages', 'name' => esc_js( __( 'Form Messages', 'wp-cred' ) ), 'slug' => 'form_messages', 'type' => 'form_messages', 'cred_builtin' => true, 'description' => esc_js( __( 'Form Messages Container', 'wp-cred' ) ));
        $form_fields['user_login'] = array('post_type' => 'user', 'post_labels' => __( 'Username', 'wp-cred' ), 'id' => 'user_login', 'wp_default' => true, 'slug' => 'user_login', 'type' => 'textfield', 'name' => __( 'Username', 'wp-cred' ), 'description' => 'Username', 'data' => array('repetitive' => 0, 'validate' => array('required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
        //nickname is required
        $form_fields['nickname'] = array('post_type' => 'user', 'post_labels' => __( 'Nickname', 'wp-cred' ), 'id' => 'nickname', 'wp_default' => true, 'slug' => 'nickname', 'type' => 'textfield', 'name' => __( 'Nickname', 'wp-cred' ), 'description' => 'Nickname', 'data' => array(/* 'repetitive' => 0, 'validate' => array ( 'required' => array ( 'active' => 1, 'value' => true, 'message' => __('This field is required','wp-cred') ) ), 'conditional_display' => array ( ), 'disabled_by_type' => 0 */));
        $form_fields['user_pass'] = array('post_type' => 'user', 'post_labels' => __( 'Password', 'wp-cred' ), 'id' => 'user_pass', 'wp_default' => true, 'slug' => 'user_pass', 'type' => 'password', 'name' => __( 'Password', 'wp-cred' ), 'description' => 'Password', 'data' => array('repetitive' => 0, 'validate' => array('required' => array('active' => 1, 'value' => true, 'message' => $required_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));
        $form_fields['user_pass2'] = array('post_type' => 'user', 'post_labels' => __( 'Repeat Password', 'wp-cred' ), 'id' => 'user_pass2', 'wp_default' => true, 'slug' => 'user_pass2', 'type' => 'password', 'name' => __( 'Repeat Password', 'wp-cred' ), 'description' => 'Repeat Password', 'data' => array('repetitive' => 0, 'validate' => array('equalto' => array('active' => 1, 'args' => array('$value', 'user_pass'), 'message' => $equalto_message)), 'conditional_display' => array(), 'disabled_by_type' => 0));

        // TAXONOMIES FIELDS
        $taxonomies = array();

        $form_fields = array_merge( $user_fields, $form_fields );

        $fields_all['groups'] = $groups;
        $fields_all['groups_conditions'] = $groups_conditions;
        $fields_all['form_fields'] = $form_fields;
        $fields_all['user_fields'] = $user_fields;
        $fields_all['custom_fields'] = $fields;
        $fields_all['taxonomies'] = $taxonomies;
        $fields_all['parents'] = $parents;
        $fields_all['extra_fields'] = $extra_fields;
        $fields_all['form_fields_count'] = count( $form_fields );
        $fields_all['user_fields_count'] = count( $user_fields );
        $fields_all['custom_fields_count'] = count( $fields );
        $fields_all['taxonomies_count'] = count( $taxonomies );
        $fields_all['parents_count'] = count( $parents );
        $fields_all['extra_fields_count'] = count( $extra_fields );

        return $fields_all;
    }

	/**
	 * @return mixed
	 */
    public function getAllFields() {
        return get_option( 'wpcf-fields' );
    }

	/**
	 * @param $text
	 * @param int $limit
	 *
	 * @return mixed
	 */
    public function suggestUserByName($text, $limit = 20) {
        $fm = CRED_Loader::get( 'MODEL/UserForms' );
        return $fm->getUsers( $text, $limit );
    }

}
