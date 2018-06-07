<?php

define( "PAD", "\t" );
define( "NL", "\r\n" );

if ( ! class_exists( "CredUserFormCreator", false ) ) {

	/**
	 * Description of CredUserFormCreator
	 *
	 * usage: CredUserFormCreator::cred_create_form('mycredform_name_post', 'new', 'subscriber');
	 *        CredUserFormCreator::cred_create_form('mycredform_name_page', 'edit', 'subscriber');
	 * to include:
	 * if (defined( 'CRED_CLASSES_PATH' )) {
	 * require_once CRED_CLASSES_PATH."/CredUserFormCreator.php";
	 * CredUserFormCreator::cred_create_form('test', 'new', 'subscriber');
	 * }
	 *
	 * @author Franko
	 */
	class CredUserFormCreator {

		/**
		 *
		 * stdClass Object
		 * (
		 * [form] => Array
		 * (
		 * [hide_comments] => 0
		 * [has_media_button] => 0
		 * [action_message] =>
		 * [type] => new
		 * [action] => form
		 * [redirect_delay] => 0
		 * )
		 *
		 * [post] => Array
		 * (
		 * [post_type] => post
		 * [post_status] => publish
		 * )
		 *
		 * )
		 *
		 *
		 * stdClass Object
		 * (
		 * [notifications] => Array
		 * (
		 * )
		 *
		 * [enable] => 1
		 * )
		 *
		 * @param type $name
		 * @param type $mode [new|edit]
		 * @param type $post_type
		 */
		public static $_created = array();

		/**
		 * @param $name
		 * @param $mode
		 * @param array $user_type
		 * @param bool $autogenerated_username
		 * @param bool $autogenerated_password
		 * @param bool $autogenerated_nickname
		 * @param string $post_type
		 *
		 * @return mixed
		 */
		public static function cred_create_form( $name, $mode, $user_type = array( 'subscriber' ), $autogenerated_username = true, $autogenerated_password = true, $autogenerated_nickname = true, $post_type = 'user' ) {
			$name = sanitize_text_field( $name );
			if ( empty( self::$_created ) && ! in_array( $name, self::$_created ) ) {
				self::$_created[] = $name;

				$form = get_page_by_title( html_entity_decode( $name ), OBJECT, CRED_USER_FORMS_CUSTOM_POST_NAME );
				if ( ! empty( $form ) ) {
					//TODO: give message? CRED form already exists
					return;
				}

				$model = CRED_Loader::get( 'MODEL/UserForms' );
				$fields_model = CRED_Loader::get( 'MODEL/UserFields' );
				$autogenerated_array = array(
					'username' => $autogenerated_username,
					'nickname' => $autogenerated_nickname,
					'password' => $autogenerated_password,
				);
				$fields_all = $fields_model->getFields( $autogenerated_array );

				if ( isset( $fields_all['extra_fields']['_featured_image'] ) ) {
					unset( $fields_all['extra_fields']['_featured_image'] );
				}

				$form_id = 1;
				$form_name = $name;
				$includeWPML = false;
				$includeRecaptcha = false;
				$counter = 0;

				if ( ! $includeRecaptcha ) {
					unset( $fields_all['extra_fields']['recaptcha'] );
				}

				$taxs_out = '';
				$parents_out = '';

				// add fields
				$groups_out = '';
				$out = '';
				if ( isset( $fields_all['user_fields'] ) && ! empty( $fields_all['user_fields'] ) ) {
					foreach ( $fields_all['user_fields'] as $f => $fields ) {
						if ( isset( $fields['cred_builtin'] ) && $fields['cred_builtin'] == 1 ) {
							continue;
						}
						if ( isset( $fields['id'] ) && $fields['id'] == 'user_url' ) {
							continue;
						}
						$counter++;
						$groups_out .= self::fieldOutput( $fields, $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
					}
				}

				if ( isset( $fields_all['custom_fields'] ) && ! empty( $fields_all['custom_fields'] ) ) {
					foreach ( $fields_all['custom_fields'] as $f => $fields ) {
						if ( isset( $fields['id'] ) && $fields['id'] == 'description' ) {
							continue;
						}
						$counter++;
						$groups_out .= self::fieldOutput( $fields, $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
					}
				}

				if ( isset( $fields_all['extra_fields'] ) && ! empty( $fields_all['extra_fields'] ) ) {
					foreach ( $fields_all['extra_fields'] as $f => $fields ) {
						$counter++;
						$groups_out .= self::fieldOutput( $fields, $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
					}
				}
				//TODO: check _credModel.get('[form][theme]') how to reproduce in PHP
				if ( 'minimal' == 'minimal' /* _credModel.get('[form][theme]') */ /* $('input[name="_cred[form][theme]"]:checked').val() */ ) // bypass script and other styles added to form, minimal
				{
					$out .= '[creduserform class=\'cred-user-form cred-keep-original\']' . NL . NL;
				} else {
					$out .= '[creduserform class=\'cred-user-form\']' . NL . NL;
				}
				if ( isset( $fields_all['form_fields']['form_messages'] ) ) {
					$out .= PAD . self::shortcode( $fields_all['form_fields']['form_messages'] ) . NL . NL;
				}
				if ( isset( $fields_all['user_fields']['post_title'] ) ) {
					$out .= self::fieldOutput( $fields_all['user_fields']['post_title'], $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
				}
				if ( isset( $fields_all['user_fields']['post_content']['supports'] ) && ! empty( $fields_all['user_fields']['post_content']['supports'] ) ) {
					$out .= self::fieldOutput( $fields_all['user_fields']['post_content'], $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
				}
				if ( isset( $fields_all['user_fields']['post_excerpt']['supports'] ) && ! empty( $fields_all['user_fields']['post_excerpt']['supports'] ) ) {
					$out .= self::fieldOutput( $fields_all['user_fields']['post_excerpt'], $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
				}
				if ( isset( $fields_all['extra_fields']['_featured_image']['supports'] ) && ! empty( $fields_all['extra_fields']['_featured_image']['supports'] ) ) {
					$out .= self::fieldOutput( $fields_all['extra_fields']['_featured_image'], $form_id, $form_name, $includeWPML, PAD ) . NL . NL;
				}

				$out .= $groups_out;
				if ( isset( $fields_all['taxonomies_count'] ) && intval( $fields_all['taxonomies_count'] ) > 0 ) {
					$out .= self::groupOutputContent( 'taxonomies', 'Taxonomies', $taxs_out, PAD ) . NL . NL;
				}
				if ( isset( $fields_all['parents_count'] ) && intval( $fields_all['parents_count'] ) > 0 ) {
					$out .= self::groupOutputContent( 'parents', 'Parents', $parents_out, PAD ) . NL . NL;
				}
				if ( isset( $fields_all['form_fields']['form_submit'] ) ) {
					$out .= PAD . self::shortcode( $fields_all['form_fields']['form_submit'] ) . NL . NL;
				}
				$out .= '[/creduserform]' . NL;

				$form = new stdClass;
				$form->ID = '';
				$form->post_title = $name;
				$form->post_content = $out;
				global $current_user;
				$form->post_author = $current_user->ID;
				$form->post_status = 'private';
				$form->comment_status = 'closed';
				$form->ping_status = 'closed';
				$form->post_type = CRED_USER_FORMS_CUSTOM_POST_NAME;
				$form->post_name = CRED_USER_FORMS_CUSTOM_POST_NAME;

				$fields = array();
				$fields['form_settings'] = new stdClass;
				$fields['form_settings']->form_type = $mode;
				$fields['form_settings']->form_action = 'form';
				$fields['form_settings']->form_action_page = '';
				$fields['form_settings']->redirect_delay = 0;

				$js_user_type = json_encode( $user_type );
				$fields['form_settings']->user_role = $js_user_type;

				$fields['form_settings']->autogenerate_username_scaffold = $autogenerated_username;
				$fields['form_settings']->autogenerate_password_scaffold = $autogenerated_password;

				$fields['form_settings']->message = '';
				$fields['form_settings']->hide_comments = 1;

				$fields['form_settings']->include_captcha_scaffold = 0;
				$fields['form_settings']->include_wpml_scaffold = 0;
				$fields['form_settings']->has_media_button = 0;
				$fields['form_settings']->post_type = $post_type;
				$fields['form_settings']->post_status = 'publish';
				$fields['form_settings']->cred_theme_css = 'minimal';

				$fields['wizard'] = -1;

				$fields['extra'] = new stdClass;
				$fields['extra']->css = '';
				$fields['extra']->js = '';

				$fields['extra']->messages = $model->getDefaultMessages();

				$res = $model->saveForm( $form, $fields );

				return $res;
			}
		}

		/**
		 * groupOutputContent
		 *
		 * @param type $slug
		 * @param type $group_name
		 * @param type $content
		 * @param string $pad
		 *
		 * @return type
		 */
		public static function groupOutputContent( $slug, $group_name, $content, $pad ) {
			$pad = '';
			$group_out = array();
			$lines = explode( NL, $content );
			for ( $i = 0; $i < count( $lines ); $i++ ) {
				$lines[ $i ] = $pad . PAD . $lines[ $i ];
			}
			$content = implode( NL, $lines );
			$group_out[] = $content;

			return implode( NL, $group_out );
		}

		/**
		 * array2Obj
		 *
		 * @param type $array
		 *
		 * @return \stdClass
		 */
		public static function array2Obj( $array ) {
			$object = new stdClass();
			if ( isset( $array ) && count( $array ) > 0 ) {
				foreach ( $array as $key => $value ) {
					$object->$key = $value;
				}
			}

			return $object;
		}

		/**
		 * taxOutput
		 *
		 * @param type $tax
		 * @param type $form_id
		 * @param type $form_name
		 * @param type $WPML
		 * @param string $pad
		 *
		 * @return type
		 */
		public static function taxOutput( $tax, $form_id, $form_name, $WPML, $pad ) {
			$WPML = $WPML || false;
			if ( ! isset( $pad ) ) {
				$pad = '';
			}

			$tax_out = array();
			$tax_out[] = $pad . '<div class="form-group">';

			if ( $WPML ) {
				$tax_out[] = $pad . PAD . '<label>[wpml-string context="cred-form-' . $form_name . '-' . $form_id . '" name="' . $tax->label . '"]' . $tax->label . '[/wpml-string]</label>';
			} else {
				$tax_out[] = $pad . PAD . '<label>' . $tax->label . '</label>';
			}
			$tax_out[] = $pad . PAD . self::shortcode( $tax );
			/**
			 * AUX:
			 * add_new_taxonomy true
			 * master_taxonomy "category"
			 * name "category_add_new"
			 */
			$tax_out[] = $pad . PAD . self::shortcode( $tax->aux );
			$tax_out[] = $pad . '</div>';

			return implode( NL, $tax_out );
		}

		/**
		 * groupOutput
		 *
		 * @param type $group
		 * @param type $fields
		 * @param type $conditions
		 * @param type $custom_fields
		 * @param type $form_id
		 * @param type $form_name
		 * @param type $WPML
		 * @param string $pad
		 *
		 * @return type
		 */
		public static function groupOutput( $group, $fields, $conditions, $custom_fields, $form_id, $form_name, $WPML, $pad ) {

			if ( ! isset( $pad ) ) {
				$pad = '';
			}
			$group_out = array();
			$group_conditional = false;
			$group_conditiona_string = '';
			if ( isset( $conditions[ $group ] ) ) {
				$group_conditional = true;
				$group_conditiona_string = $conditions[ $group ];
			}
			if ( $group_conditional ) {
				$group_out[] = $pad . '[cred_show_group if="' . $group_conditiona_string . '" mode=\'fade-slide\']';
			}
			for ( $ii = 0; $ii < count( $fields ); $ii++ ) {
				if ( isset( $custom_fields[ $fields[ $ii ] ] ) && isset( $custom_fields[ $fields[ $ii ] ]['_cred_ignore'] ) ) {
					continue;
				}
				$group_out[] = self::fieldOutput( $custom_fields[ $fields[ $ii ] ], $form_id, $form_name, $WPML, $pad ) . NL;
			}
			if ( $group_conditional ) {
				$group_out[] = $pad . '[/cred_show_group]';
			}

			return implode( NL, $group_out ) . NL;
		}

		/**
		 * fieldOutput
		 *
		 * @param type $field
		 * @param type $form_id
		 * @param type $form_name
		 * @param type $WPML
		 * @param string $pad
		 * @param type $extra
		 *
		 * @return type
		 */
		public static function fieldOutput( $field, $form_id, $form_name, $WPML, $pad, $extra = array() ) {
			$field = self::array2Obj( $field );

			if ( ! $pad || ! isset( $pad ) ) {
				$pad = '';
			}
			$field_out = array();
			$WPML = $WPML || false;

			if ( isset( $field ) ) {
				$field_out[] = $pad . '<div class="form-group">';
				if ( 'checkbox' != $field->type ) {
					if ( $WPML ) {
						$label_field = '[wpml-string context=\'cred-form-' . $form_name . '-' . $form_id . '\' name=\'' . $field->name . '\']' . $field->name . '[/wpml-string]';
					} else {
						$label_field = $field->name;
					}
					$field_out[] = $pad . PAD . '<label>' . $label_field . '</label>';
				}
				$args = (array) $field;

				if ( ! empty( $extra ) ) {
					$field_out[] = $pad . PAD . self::shortcode( $args, $extra );
				} else {
					$field_out[] = $pad . PAD . self::shortcode( $args );
				}

				$field_out[] = $pad . '</div>';
			}

			return implode( NL, $field_out );
		}

		/**
		 * shortcode
		 *
		 * @param type $field
		 * @param type $extra
		 *
		 * @return string
		 */
		public static function shortcode( $field, $extra = null ) {
			$field = self::array2Obj( $field );
			$extra = self::array2Obj( $extra );

			/* use underscores in shortcodes and not hyphens anymore, try to keep compatibility */
			$field_out = '';
			$post_type = '';
			$value = ' value=\'\'';
			if ( isset( $field ) && isset( $field->slug ) ) {
				if ( isset( $field->post_type ) ) {
					$post_type = ' post=\'' . $field->post_type . '\'';
				}
				if ( isset( $field->value ) ) {
					$value = ' value=\'' . $field->value . '\'';
				}
				// add url parameter
				if ( ! isset( $field->taxonomy ) && ! isset( $field->is_parent ) && 'form_messages' != $field->type ) {
					$value .= ' urlparam=\'\'';
				}

				if ( $field->type == 'image' || $field->type == 'file' ) {
					$max_width = ( isset( $extra ) && isset( $extra->max_width ) ) ? $extra->max_width : false;
					$max_height = ( isset( $extra ) && isset( $extra->max_height ) ) ? $extra->max_height : false;
					if ( isset( $max_width ) && ! empty( $max_width ) && $max_width !== false ) {
						$value .= ' max_width=\'' . $max_width . '\'';
					}
					if ( isset( $max_height ) && ! empty( $max_height ) && $max_height !== false ) {
						$value .= ' max_height=\'' . $max_height . '\'';
					}
				}
				if ( isset( $field->is_parent ) ) {
					$parent_order = ( isset( $extra ) && isset( $extra->parent_order ) ) ? $extra->parent_order : false;
					$parent_ordering = ( isset( $extra ) && isset( $extra->parent_ordering ) ) ? $extra->parent_ordering : false;
					$parent_results = ( isset( $extra ) && isset( $extra->parent_max_results ) ) ? $extra->parent_max_results : false;
					$required = ( isset( $extra ) && isset( $extra->required ) ) ? $extra->required : false;
					$no_parent_text = ( isset( $extra ) && isset( $extra->no_parent_text ) ) ? $extra->no_parent_text : false;
					$select_parent_text = ( isset( $extra ) && isset( $extra->select_parent_text ) ) ? $extra->select_parent_text : false;
					$validate_parent_text = ( isset( $extra ) && isset( $extra->validate_parent_text ) ) ? $extra->validate_parent_text : false;
					if ( isset( $parent_results ) && ! empty( $parent_results ) && $parent_results !== false ) {
						$value .= ' max_results=\'' . $parent_results . '\'';
					}
					if ( isset( $parent_order ) && ! empty( $parent_order ) && $parent_order !== false ) {
						$value .= ' order=\'' . $parent_order . '\'';
					}
					if ( isset( $parent_ordering ) && ! empty( $parent_ordering ) && $parent_ordering !== false ) {
						$value .= ' ordering=\'' . $parent_ordering . '\'';
					}
					if ( isset( $required ) && ! empty( $required ) && $required !== false ) {
						$value .= ' required=\'' . $required . '\'';
					}
					if ( isset( $select_parent_text ) && ! empty( $select_parent_text ) && $select_parent_text !== false ) {
						$value .= ' select_text=\'' . $select_parent_text . '\'';
					}
					if ( isset( $required ) && ! empty( $required ) && $validate_parent_text !== false ) {
						$value .= ' validate_text=\'' . $validate_parent_text . '\'';
					}
					if ( isset( $no_parent_text ) && ! empty( $no_parent_text ) && $no_parent_text !== false ) {
						$value .= ' no_parent_text=\'' . $no_parent_text . '\'';
					}
				}
				if ( $field->type == 'textfield' ||
					$field->type == 'textarea' ||
					$field->type == 'wysiwyg' ||
					$field->type == 'date' ||
					$field->type == 'phone' ||
					$field->type == 'url' ||
					$field->type == 'numeric' ||
					$field->type == 'email'
				) {
					$readonly = ( isset( $extra ) && isset( $extra->readonly ) ) ? $extra->readonly : false;
					$escape = ( isset( $extra ) && isset( $extra->escape ) ) ? $extra->escape : false;
					$placeholder = ( $extra && isset( $extra->placeholder ) ) ? $extra->placeholder : false;
					if ( isset( $readonly ) && ! empty( $readonly ) && $readonly !== false ) {
						$value .= ' readonly=\'' . $readonly . '\'';
					}
					if ( isset( $escape ) && ! empty( $escape ) && $escape !== false ) {
						$value .= ' escape=\'' . $escape . '\'';
					}
					if ( isset( $placeholder ) && ! empty( $placeholder ) && $placeholder !== false ) {
						$value .= ' placeholder=\'' . $placeholder . '\'';
					}
				}

				if ( $field->type == 'checkbox' ||
					$field->type == 'checkboxes' ||
					$field->type == 'radio' ||
					$field->type == 'form_messages' ||
					$field->type == 'audio' ||
					$field->type == 'video' ||
					$field->type == 'image' ||
					$field->type == 'file' ||
					$field->type == 'wysiwyg' ||
					$field->type == 'form_submit'
				) {
					if ( $field->type == 'form_messages' ) {
						$value .= " class='alert alert-warning'";
					}
					if ( $field->type == 'form_submit' ) {
						$value .= " class='btn btn-primary btn-lg'";
					}
				} else {
					$value .= " class='form-control'";
				}

				if ( $field->type != 'form_messages' ) {
					$value .= " output='bootstrap'";
				}

				$field_out = '[cred_field field=\'' . $field->slug . '\'' . $post_type . $value . ']';
			}
			if ( isset( $field ) && ( isset( $field->taxonomy ) || isset( $field->aux ) ) ) {
				if ( isset( $field->hierarchical ) ) {
					// Hierarchical taxonomy fields are displayed as default as checkboxes, which do no support a class attribute yet
					$field_out = '[cred_field field=\'' . $field->name . '\' display=\'checkbox\' output=\'bootstrap\']';
				} else {
					$field_out = '[cred_field field=\'' . $field->name . '\' class=\'form-control\' output=\'bootstrap\']';
				}
			}
			if ( isset( $field ) && isset( $field->popular ) ) {
				$field_out = '[cred_field field=\'' . $field->name . '\' taxonomy=\'' . $field->master_taxonomy . '\' type=\'show_popular\']';
			}
			if ( isset( $field ) && isset( $field->add_new_taxonomy ) ) {
				$field_out = '[cred_field field=\'' . $field->name . '\' taxonomy=\'' . $field->master_taxonomy . '\' type=\'add_new\']';
			}

			return $field_out;
		}

	}

}