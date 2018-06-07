<?php

/**
 * CRED Frontend File Ajax Upload Manager handles credfile, credimage, credaudio and credvideo files
 * through wp_ajax_[nopriv_] wp hook
 *
 * @since 1.9.3
 */
class CRED_Frontend_File_Ajax_Upload_Manager implements ICRED_Frontend_File_Ajax_Upload_Manager {

	const CRED_ACTION_AJAX_UPLOAD = 'cred_frontend_ajax_upload_submit';
	const CRED_AJAX_FILE_UPLOADER = 'cred-frontend-ajax-file-uploader';
	const CREDFILE = 'wpt-field-credfile';

	protected $php_file_upload_error_messages;
	protected $cred_file_upload_error_messages;
	protected $max_upload_size;
	protected $human_readable_max_upload_size;

	private static $instance;

	public function __construct() {
		$this->init();
		$this->prevent_possible_php_post_content_length_warning();
		$this->register_hooks();
	}

	protected function init() {
		$this->max_upload_size = wp_max_upload_size();
		$this->human_readable_max_upload_size = size_format( $this->max_upload_size, 2 );

		$this->php_file_upload_error_messages = array(
			0 => __( 'There is no error, the file uploaded with success', 'wp-cred' ),
			1 => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wp-cred' ),
			2 => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wp-cred' ),
			3 => __( 'The uploaded file was only partially uploaded', 'wp-cred' ),
			4 => __( 'No file was uploaded', 'wp-cred' ),
			6 => __( 'Missing a temporary folder', 'wp-cred' ),
			7 => __( 'Failed to write file to disk.', 'wp-cred' ),
			8 => __( 'A PHP extension stopped the file upload.', 'wp-cred' ),
		);

		$this->cred_file_upload_error_messages = array(
			1 => __( 'The file you uploaded it too large. You can upload files up to %s', 'wp-cred' ),
			4 => __( 'There was an error uploading your file.', 'wp-cred' ),
			5 => __( "The form you're submitting doesn't seem to belong to this page. Please reload the page and try again.", 'wp-cred' ),
			6 => __( 'The form you submitted has expired. Please refresh the page and try again.', 'wp-cred' ),
		);
	}

	// We need to handle a specific case:
	// When file upload size is higher than ini_get('post_max_size')
	// PHP execution shows
	// PHP Warning: POST Content-Length of XXXXXX bytes exceeds the limit of XXXXXX
	// on the screen before the WP execution
	// that breaks the json that contains the error message
	// and the user will always receive a generic Upload Error.
	// We can clean the screen
	// just in case we have a ajax, cred file upload
	// and the file size is not valid.
	protected function prevent_possible_php_post_content_length_warning() {
		if (
			cred_is_ajax_call()
			&& isset( $_GET['action'] )
			&& $_GET['action'] == self::CRED_ACTION_AJAX_UPLOAD
			&& isset( $_SERVER["CONTENT_LENGTH"] )
			&& $_SERVER["CONTENT_LENGTH"] > wp_max_upload_size()
		) {
			ob_end_clean();
		}
	}

	protected function register_hooks() {
		add_action( 'wp_ajax_' . self::CRED_ACTION_AJAX_UPLOAD, array( $this, 'upload' ) );
		add_action( 'wp_ajax_nopriv_' . self::CRED_ACTION_AJAX_UPLOAD, array( $this, 'upload' ) );
	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Enqueue all assets needed for the frontend file upload field.
	 *
	 * @param $is_progress_bar_disabled
	 *
	 * @since 1.9.3
	 */
	public function enqueue_file_upload_assets( $is_progress_bar_disabled ) {
		wp_register_script( self::CREDFILE, CRED_Asset_Manager::get_instance()->get_asset_url( 'js/credfile.js' ), array( 'wptoolset-forms' ), WPTOOLSET_FORMS_VERSION, true );
		wp_enqueue_script( self::CREDFILE );

		if ( $is_progress_bar_disabled ) {
			// Nothing else is needed
			return;
		}

		$base_url = CRED_Asset_Manager::get_instance()->get_asset_url( 'js/jquery_upload' );

		wp_enqueue_style( 'progress_bar-style', "$base_url/progress_bar.css" );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( 'load-image-all-script', "$base_url/load-image.all.min.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-iframe-transport-script', "$base_url/jquery.iframe-transport.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-script', "$base_url/jquery.fileupload.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-process-script', "$base_url/jquery.fileupload-process.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-image-script', "$base_url/jquery.fileupload-image.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-audio-script', "$base_url/jquery.fileupload-audio.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-video-script', "$base_url/jquery.fileupload-video.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-validate-script', "$base_url/jquery.fileupload-validate.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-ui-script', "$base_url/jquery.fileupload-ui.js", array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-fileupload-jquery-ui-script', "$base_url/jquery.fileupload-jquery-ui.js", array( 'jquery' ), '', true );

		wp_enqueue_script( self::CRED_AJAX_FILE_UPLOADER, "$base_url/file_upload.js", array( 'jquery' ) );
		wp_localize_script( self::CRED_AJAX_FILE_UPLOADER, 'settings',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'action' => self::CRED_ACTION_AJAX_UPLOAD,
				'media_settings' => Toolset_Utils::get_image_sizes( 'thumbnail' ),
				'delete_confirm_text' => __( 'Are you sure to delete this file ?', 'wp-cred' ),
				'delete_alert_text' => __( 'Generic Error in deleting file', 'wp-cred' ),
				'delete_text' => __( 'delete', 'wp-cred' ),
				'failed_upload_too_large_file_alert_text' => __( 'The file you uploaded it too large. You can upload files up to ', 'wp-cred' ),
				'failed_upload_generic_alert_text' => __( 'There was an error uploading your file.', 'wp-cred' ),
				'max_upload_size' => $this->max_upload_size,
				'human_readable_max_upload_size' => $this->human_readable_max_upload_size,
				'nonce' => wp_create_nonce( 'ajax_nonce' ),
			)
		);
	}

	public function upload() {
		$current_user = wp_get_current_user();
		$is_current_user_admin = ( user_can( $current_user, 'administrator' ) );

		$response = new CRED_Frontend_File_Ajax_Upload_Response();
		$data = array();

		//checking nonce
		if (
			isset( $_REQUEST['nonce'] )
			&& check_ajax_referer( 'ajax_nonce', 'nonce', false )
		) {
			//checking delete action
			if (
				isset( $_POST['action'] )
				&& $_POST['action'] == 'delete'
				&& isset( $_POST['file'] )
			) {
				$file = esc_url_raw( $_POST['file'] );
				$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : - 1;

				$data = array( 'result' => true );
				$local_file = cred_get_local( $file );
				$attachments = get_children(
					array(
						'post_parent' => $id,
						'post_type' => 'attachment',
					)
				);
				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment ) {
						$attach_file = strtolower( basename( $attachment->guid ) );
						$my_local_file = strtolower( basename( $local_file ) );
						if ( $attach_file == $my_local_file ) {
							wp_delete_attachment( $attachment->ID );
						}
					}
				}

			} else {

				//checking parent post_id
				if ( isset( $_GET['id'] ) ) {
					$post_id = (int) $_GET['id'];
					$post_type = get_post_type( $post_id );

					//checking formid
					if ( isset( $_GET['formid'] ) ) {
						$form_id = (int) $_GET['formid'];
						$form = get_post( $form_id );
						$form_type = $form->post_type;

						$is_user_form = ( $form_type == CRED_USER_FORMS_CUSTOM_POST_NAME );
						if ( $is_user_form ) {
							//for user forms attached parent post is always -1
							$post_id = - 1;
						}

						$form_slug = $form->post_name;

						$this_form = array(
							'id' => $form_id,
							'post_type' => $post_type,
							'form_type' => $form_type,
						);

						$files = array();
						$previews = array();
						$upload_overrides = array( 'test_form' => false );
						if ( ! empty( $_FILES ) ) {

							//controls file error_codes
							foreach ( $_FILES as $uploaded_file ) {
								$error_code = is_array( $uploaded_file['error'] ) ? reset($uploaded_file['error']) : $uploaded_file['error'];
								if ( $error_code != 0 ) {
									//shows php error messages only for admin users
									$error_message = $is_current_user_admin ? $this->php_file_upload_error_messages[ $error_code ] : $this->cred_file_upload_error_messages[4];
									$data = array( 'message' => $error_message );
									break;
								}
							}

							//There are no errors
							if ( empty( $data ) ) {

								$fields = array();
								foreach ( $_FILES as $field_name => $field_value ) {
									$fields[ $field_name ]['field_data'] = $field_value;
								}

								$errors = array();

								list( $fields, $errors ) = apply_filters( 'cred_form_ajax_upload_validate_' . $form_slug, array( $fields, $errors ), $this_form );
								list( $fields, $errors ) = apply_filters( 'cred_form_ajax_upload_validate_' . $form_id, array( $fields, $errors ), $this_form );
								list( $fields, $errors ) = apply_filters( 'cred_form_ajax_upload_validate', array( $fields, $errors ), $this_form );

								if ( ! empty( $errors ) ) {
									//even data is an array of array, we can set 1 field error only at time
									foreach ( $errors as $field_name => $error_text ) {
										$data = array( 'message' => $field_name . ': ' . $error_text );
									}
									$response->json_send_ajax_error( $data );
								} else {
									foreach ( $_FILES as $file ) {
										//For repetitive
										foreach ( $file as &$f ) {
											if ( is_array( $f ) ) {
												foreach ( $f as $p ) {
													$f = $p;
													break;
												}
											}
										}

										$upload_result = wp_handle_upload( $file, $upload_overrides );
										if ( ! isset( $upload_result['error'] ) ) {

											$base_name = basename( $upload_result['file'] );
											$attachment = array(
												'post_mime_type' => $upload_result['type'],
												'post_title' => $base_name,
												'post_content' => '',
												'post_status' => 'inherit',
												'post_parent' => $post_id,
												'post_type' => 'attachment',
												'guid' => $upload_result['url'],
											);
											$attached_id = wp_insert_attachment( $attachment, $upload_result['file'] );
											$attached_data = wp_generate_attachment_metadata( $attached_id, $upload_result['file'] );
											wp_update_attachment_metadata( $attached_id, $attached_data );

											//Fixing S3 Amazon rewriting compatibility
											if ( wp_attachment_is_image( $attached_id ) ) {
												$rewrite_url = wp_get_attachment_image_src( $attached_id, 'full' );
												$rewrite_url_preview = wp_get_attachment_image_src( $attached_id );
												$attached_data = wp_generate_attachment_metadata( $attached_id, $rewrite_url );
											} else {
												$rewrite_url = wp_get_attachment_url( $attached_id );
											}

											if ( isset( $rewrite_url ) ) {
												$files[] = ( is_array( $rewrite_url ) && isset( $rewrite_url[0] ) ) ? $rewrite_url[0] : $rewrite_url; //$res['url'];
												$attaches[] = $attached_id;
												if ( isset( $rewrite_url_preview ) ) {
													$previews[] = ( is_array( $rewrite_url_preview ) && isset( $rewrite_url_preview[0] ) ) ? $rewrite_url_preview[0] : $rewrite_url_preview;
												}
											} else {
												$files[] = $upload_result['url'];
												$attaches[] = $attached_id;
											}

											//upload success
											$data = array(
												'files' => $files,
												'attaches' => $attaches,
												'previews' => $previews,
												'delete_nonce' => time(),
											);
											$response->json_send_ajax_success( $data );
										} else {
											//very last priority. This case should never happen because we already checked error_code above.
											$data = array( 'message' => $this->cred_file_upload_error_messages[4] );
										}
									}
								}
							}

						} else {
							$data = array( 'message' => sprintf( $this->cred_file_upload_error_messages[1], $this->human_readable_max_upload_size ) );
						}

					} else {
						$data = array( 'message' => $this->cred_file_upload_error_messages[5] );
					}

				} else {
					$data = array( 'message' => $this->cred_file_upload_error_messages[5] );
				}
			}

		} else {
			$data = array( 'message' => $this->cred_file_upload_error_messages[6] );
		}

		// we cannot use wp_send_ajax_success or wp_send_ajax_error
		// because of how is handling response file_upload js lib
		$response->json_send_ajax_error( $data );
	}

}