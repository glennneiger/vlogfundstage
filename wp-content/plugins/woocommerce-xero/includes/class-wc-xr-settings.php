<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Settings {

	const OPTION_PREFIX = 'wc_xero_';

	/**
	 * Option key name postfix for upload info.
	 *
	 * @since 1.7.13
	 */
	const UPLOAD_INFO_POSTFIX = '_upload_info';

	// Settings defaults
	private $settings = array();
	private $override = array();

	private $key_file_delete_result = array();

	public function __construct( $override = null ) {

		add_filter( 'option_page_capability_woocommerce_xero', array( $this, 'add_save_capability' ) );

		if ( $override !== null ) {
			$this->override = $override;
		}

		// Set the settings
		$this->settings = array(

			// API keys
			'consumer_key'        => array(
				'title'       => __( 'Consumer Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.', 'wc-xero' ),
			),
			'consumer_secret'     => array(
				'title'       => __( 'Consumer Secret', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.', 'wc-xero' ),
			),
			// SSH key files
			'public_key_content'  => array(
				'title'       => __( 'Public Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'key_file',
				'key_type'    => 'public',
				'file_ext'    => '.cer',
				'description' => __(  'Public key file created to authenticate this site with Xero.', 'wc-xero' ),
			),
			'private_key_content' => array(
				'title'       => __( 'Private Key', 'wc-xero' ),
				'default'     => '',
				'type'        => 'key_file',
				'key_type'    => 'private',
				'file_ext'    => '.pem',
				'description' => __(  'Private key file created to authenticate this site with Xero.', 'wc-xero' ),
			),
			// Invoice Prefix
			'invoice_prefix'      => array(
				'title'       => __( 'Invoice Prefix', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Allow you to prefix all your invoices.', 'wc-xero' ),
			),
			// Accounts
			'sales_account'       => array(
				'title'       => __( 'Sales Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Code for Xero account to track sales.', 'wc-xero' ),
			),
			'shipping_account'    => array(
				'title'       => __( 'Shipping Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Code for Xero account to track shipping charges.', 'wc-xero' ),
			),
			'fees_account'        => array(
				'title'       => __( 'Fees Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Code for Xero account to allow fees.', 'wc-xero' ),
			),
			'payment_account'     => array(
				'title'       => __( 'Payment Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Code for Xero account to track payments received.', 'wc-xero' ),
			),
			'rounding_account'    => array(
				'title'       => __( 'Rounding Account', 'wc-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __(  'Code for Xero account to allow an adjustment entry for rounding.', 'wc-xero' ),
			),
			// Misc settings
			'send_invoices'       => array(
				'title'       => __( 'Send Invoices', 'wc-xero' ),
				'default'     => 'manual',
				'type'        => 'select',
				'description' => __(  'Send Invoices manually (from the order\'s action menu), on creation (when the order is created), or on completion (when order status is changed to completed).', 'wc-xero' ),
				'options'     => array(
					'manual'             => __( 'Manually', 'wc-xero' ),
					'creation'           => __( 'On Order Creation', 'wc-xero' ),
					'payment_completion' => __( 'On Payment Completion', 'wc-xero' ),	
					'on'                 => __( 'On Order Completion', 'wc-xero' ),
				),
			),
			'send_payments'       => array(
				'title'       => __( 'Send Payments', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'select',
				'description' => __(  'Send Payments manually or automatically when order is completed. This may need to be turned off if you sync via a separate integration such as PayPal.', 'wc-xero' ),
				'options'     => array(
					'manual' => __( 'Manually', 'wc-xero' ),
					'payment_completion' => __( 'On Payment Completion', 'wc-xero' ),
					'on'  => __( 'On Order Completion', 'wc-xero' ),
				),
			),
			'treat_shipping_as'   => array(
				'title'       => __( 'Treat Shipping As', 'wc-xero' ),
				'default'     => 'expense',
				'type'        => 'select',
				'description' => __(  'Set this to correspond to your Xero shipping account\'s type.', 'wc-xero' ),
				'options'     => array(
					'income'  => __( 'Income / Revenue / Sales', 'wc-xero' ),
					'expense' => __( 'Expense', 'wc-xero' ),
				),
			),
			'four_decimals'       => array(
				'title'       => __( 'Four Decimal Places', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => __(  'Use four decimal places for unit prices instead of two.', 'wc-xero' ),
			),
			'export_zero_amount'  => array(
				'title'       => __( 'Orders with zero total', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => __(  'Export orders with zero total.', 'wc-xero' ),
			),
			'send_inventory'      => array(
				'title'       => __( 'Send Inventory Items', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => __(  'Send Item Code field with invoices. If this is enabled then each product must have a SKU defined and be setup as an <a href="https://help.xero.com/us/#Settings_PriceList" target="_blank">inventory item</a> in Xero.', 'wc-xero' ),
			),
			'debug'               => array(
				'title'       => __( 'Debug', 'wc-xero' ),
				'default'     => 'off',
				'type'        => 'checkbox',
				'description' => __(  'Enable logging.  Log file is located at: /wc-logs/', 'wc-xero' ),
			),
		);

		$this->migrate_existing_keys();
		$this->key_file_delete_result = $this->delete_old_key_file();
	}

	/**
	 * Migrate key file(s) contents to option database.
	 *
	 * Copies key content into DB from the files pointed to by
	 * 'private_key' and 'public_key'. Key content is placed in
	 * new option entries: 'private_key_content' and 'public_key_content'.
	 * 'private_key' and 'public_key' keys will be deleted only
	 * if they do not point to a valid key file. Key files
	 * are deleted in {@see delete_old_key_files()}.
	 *
	 * @since 1.7.13
	 *
	 * @return void
	 */
	public function migrate_existing_keys() {
		foreach ( array( 'private_key', 'public_key' ) as $key_postfix ) {
			$old_key_name = self::OPTION_PREFIX . $key_postfix;
			$content_key_name = $old_key_name . '_content';
			$new_key_content = get_option( $content_key_name );
			$key_file_path = get_option( $old_key_name );

			if ( false !== $key_file_path ) {
				if ( file_exists( $key_file_path ) ) {
					// If new {@since 1.7.13} key has been set yet.
					if ( false === $new_key_content ) {
						$key_file_content = $this->read_key_file( $key_file_path );
						if ( ! empty( $key_file_content ) && empty( $new_key_content ) ) {
							// Save key content from file to db.
							update_option( $content_key_name, $key_file_content );
							$this->set_upload_info( $content_key_name, basename( $key_file_path ) );
						}
					}
				} else {
				    // Just delete the option, since the file it's pointing to does not exist.
					delete_option( $old_key_name );
				}
			}
		}
	}

	/**
	 * Saves upload info to options db.
	 *
	 * Saves key file name and upload timestamp to options db so it can be reported back to user.
	 *
	 * @since 1.7.13
	 *
	 * @param string $content_key_name key name where content of key is saved (e.g. 'wc_private_key_content').
	 * @param string $filename         filename of uploaded file.
	 *
	 * @return void
	 */
	public function set_upload_info( $content_key_name, $filename ) {
		$upload_info_key = $content_key_name . self::UPLOAD_INFO_POSTFIX;

		update_option( $upload_info_key, array(
			'upload_timestamp' => current_time( 'timestamp' ),
			'upload_filename' => $filename,
		) );
	}

	/**
	 * Get formatted string for upload info.
	 *
	 * Returns a human-readable string for what filename was uploaded and when.
	 *
	 * @since 1.7.13
	 *
	 * @param string $content_key_name key name where content of key is saved (e.g. 'wc_private_key_content').
	 *
	 * @return string Filename and upload datetime.
	 */
	public function get_upload_info_string( $content_key_name ) {
		$upload_info_key = $content_key_name . self::UPLOAD_INFO_POSTFIX;
		$upload_info = get_option( $upload_info_key );

		if ( ! empty ( $upload_info ) ) {
			$format = get_option( 'time_format' ) . ', ' . get_option( 'date_format' );
			$upload_date_time =  date_i18n( $format, $upload_info['upload_timestamp'] );
			return sprintf( __( 'Using %s uploaded at %s', 'wc-xero' ), $upload_info['upload_filename'], $upload_date_time );
		}
		return '';
	}

	/**
	 * Handle delete requests from Delete <filename> button push.
	 *
	 * Deletes key file. Once key file is deleted, the option
	 * field will be cleaned up in {@see migrate_existing_keys}.
	 *
	 * @see key_migration_notice()
	 * @since 1.7.13
	 *
	 * @return array {
	 *     Key file deletion result.
	 *
	 *     @type string $result   Result of delete ('error' / 'success').
	 *     @type string $key_file Full file path of key file to be deleted.
	 * }
	 */
	public function delete_old_key_file() {
		$key_file_path = '';
		$result = '';
		if ( isset( $_POST['delete_key_file'] ) && current_user_can( 'manage_options' ) ) {
			$key_name = sanitize_text_field( $_POST['delete_key_file'] );
			$key_file_path = get_option( $key_name );
			if ( ! empty( $key_file_path ) ) {
				$delete_result = unlink( $key_file_path );
				if ( false === $delete_result ) {
					$result = 'error';
				} else {
					$result = 'success';
				}
			}
		}
		return array(
			'result'   => $result,
			'key_file' => $key_file_path,
		);
	}

	/**
	 * Reads key file contents.
	 *
	 * @since 1.7.13
	 *
	 * @param string Path to key file.
	 * @return string Key content.
	 */
	public function read_key_file( $file_path ) {
		$fp = fopen( $file_path,'r' );
		$file_contents = fread( $fp, 8192 );
		fclose( $fp );
		return $file_contents;
	}

	/**
	 * Adds manage_woocommerce capability to settings so that
	 * any roles with this capabilitity will be able to save the settings
	 */
	public function add_save_capability( $capability ) {
		return 'manage_woocommerce';
	}

	/**
	 * Setup the required settings hooks
	 */
	public function setup_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_notices', array( $this, 'key_migration_notice' ) );
	}

	/**
	 * Get an option
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_option( $key ) {

		if ( isset( $this->override[ $key ] ) ) {
			return $this->override[ $key ];
		}

		return get_option( self::OPTION_PREFIX . $key, $this->settings[ $key ]['default'] );
	}

	/**
	 * settings_init()
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {

		// Add section
		add_settings_section( 'wc_xero_settings', __( 'Xero Settings', 'wc-xero' ), array(
			$this,
			'settings_intro'
		), 'woocommerce_xero' );

		// Add setting fields
		foreach ( $this->settings as $key => $option ) {

			// Add setting fields
			add_settings_field( self::OPTION_PREFIX . $key, $option['title'], array(
				$this,
				'input_' . $option['type']
			), 'woocommerce_xero', 'wc_xero_settings', array( 'key' => $key, 'option' => $option ) );

			if ( 'key_file' === $option['type'] ) {
				add_filter( 'pre_update_option_' . self::OPTION_PREFIX . $key , array( $this, 'handle_key_file_upload' ), 10, 3 );
			}

			// Register setting
			register_setting( 'woocommerce_xero', self::OPTION_PREFIX . $key );

		}

	}

	/**
	 * Add menu item
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$sub_menu_page = add_submenu_page( 'woocommerce', __( 'Xero', 'wc-xero' ), __( 'Xero', 'wc-xero' ), 'manage_woocommerce', 'woocommerce_xero', array(
			$this,
			'options_page'
		) );

		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_style' ) );
	}

	public function enqueue_style() {
		global $woocommerce;
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
	}

	/**
	 * The options page
	 */
	public function options_page() {
		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" enctype="multipart/form-data" action="options.php">
				<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
				<h2><?php _e( 'Xero for WooCommerce', 'wc-xero' ); ?></h2>

				<?php
				if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'true' ) ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'wc-xero' ) . '</strong></p></div>';

				} else if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'false' ) ) {
					echo '<div id="message" class="error fade"><p><strong>' . __( 'There was an error saving your settings.', 'wc-xero' ) . '</strong></p></div>';
				}
				?>

				<?php settings_fields( 'woocommerce_xero' ); ?>
				<?php do_settings_sections( 'woocommerce_xero' ); ?>
				<p class="submit"><input type="submit" class="button-primary" value="Save"/></p>
			</form>
		</div>
	<?php
	}

	/**
	 * Settings intro
	 */
	public function settings_intro() {
		echo '<p>' . __( 'Settings for your Xero account including security keys and default account numbers.<br/> <strong>All</strong> text fields are required for the integration to work properly.', 'wc-xero' ) . '</p>';
	}

	/**
	 * Key file input field.
	 *
	 * Generates file input for key file upload.
	 *
	 * @param $args
	 */
	public function input_key_file( $args ) {
		$input_name = self::OPTION_PREFIX . $args['key'] ;
		$file_ext = $args['option']['file_ext'];

		// File input field.
		echo '<input type="file" name="' .  esc_attr( $input_name ) . '" id="' . esc_attr( $input_name ) . '" accept="' . esc_attr( $file_ext ) . '"/>';

		echo '<p class="description">' . esc_html( $args['option']['description'] ) . '</p>';

		$key_content = $this->get_option( $args['key'] );
		if ( ! empty( $key_content ) ) {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #4AB915; color: #fff; font-weight: bold;">' . esc_html( $this->get_upload_info_string( $input_name ) ) . '</span></p>';
		} else {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #bc0b0b; color: #fff; font-weight: bold;">' . __( 'Key not set', 'wc-xero' ) . '</span></p>';
		}
	}

	/**
	 * Text setting field
	 *
	 * @param array $args
	 */
	public function input_text( $args ) {
		echo '<input type="text" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" value="' . $this->get_option( $args['key'] ) . '" />';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

	/**
	 * Checkbox setting field
	 *
	 * @param array $args
	 */
	public function input_checkbox( $args ) {
		echo '<input type="checkbox" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" ' . checked( 'on', $this->get_option( $args['key'] ), false ) . ' /> ';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

	/**
	 * Drop down setting field
	 *
	 * @param array $args
	 */
	public function input_select( $args ) {
		$option = $this->get_option( $args['key'] );

		$name = esc_attr( self::OPTION_PREFIX . $args['key'] );
		$id = esc_attr( self::OPTION_PREFIX . $args['key'] );
		echo "<select name='$name' id='$id'>";

		foreach( $args['option']['options'] as $key => $value ) {
			$selected = selected( $option, $key, false );
			$text = esc_html( $value );
			$val = esc_attr( $key );
			echo "<option value='$val' $selected>$text</option>";
		}

		echo '</select>';
		echo '<p class="description">' . esc_html( $args['option']['description'] ) . '</p>';
	}

	/**
	 * Handle key file upload.
	 *
	 * Retrieves key content from user uploaded as string
	 * and returns it so that's stored as the option value
	 * instead of just the file name. This should be hooked
	 * into the 'pre_update_option_' hook for 'key_file'
	 * inputs so key data can be retrieved.
	 *
	 * @since 1.7.13
	 *
	 * @param string $new_value filename of uploaded file
	 * @param string $old_value original value of key.
	 * @param string $option_name full name of option.
	 *
	 * @return string key content to directly store in option db.
	 */
	public function handle_key_file_upload( $new_value, $old_value, $option_name ) {
		if ( ! isset( $_FILES[ $option_name ] ) ) {
			return $old_value;
		}

		$valid_filetypes = array(
			'txt' => 'text/plain',
			'pem' => 'text/plain',
			'cer' => 'text/plain',
			'pub' => 'text/plain',
			'ppk' => 'text/plain',
		);
		$overrides       = array(
			'test_form' => false,
			'mimes'     => $valid_filetypes,
		);
		$import          = $_FILES[ $option_name ];
		$upload          = wp_handle_upload( $import, $overrides );

		if ( isset( $upload['error'] ) ) {
			return $old_value;
		}

		$key_content = $this->read_key_file( $upload['file'] );
		if ( empty( $key_content ) ) {
			return $old_value;
		}

		$this->set_upload_info( $option_name, $import['name'] );
		return $key_content;
	}

	/**
	 * Notify user is key file(s) still exists.
	 *
	 * @since 1.7.13
	 *
	 * @return void
	 */
	public function key_migration_notice() {
		if ( current_user_can( 'manage_options' ) ) {
			foreach ( array( 'wc_xero_public_key', 'wc_xero_private_key' ) as $key_name ) {
				$key_file_path = esc_html( get_option( $key_name ) );
				if ( false !== $key_file_path && file_exists( $key_file_path ) ) {
					$filename = basename( $key_file_path );
					?>
					<div class="notice notice-warning">
						<p><?php echo esc_html( sprintf( __( 'Xero has securely saved the contents of key file %s to the database and no longer requires it.', 'wc-xero' ), $key_file_path ) ); ?></p>
						<form method="post">
							<button type="submit" name="delete_key_file" value="<?php echo esc_attr( $key_name ); ?>">
								<?php echo esc_html( sprintf( __( 'Delete %s', 'wc-xero' ), $filename ) ); ?>
							</button>
						</form>
					</div>
					<?php
				}
			}

			// Show file deletion result if file was deleted.
			if ( ! empty( $this->key_file_delete_result['result'] ) ) {
				$key_file_path = $this->key_file_delete_result['key_file'];
				if ( 'error' === $this->key_file_delete_result['result'] ) {
					?>
					<div class="error">
						<p>
							<?php echo esc_html( sprintf( __( 'Xero could not delete %s. Check permissions and try again.', 'wc-xero' ), $key_file_path ) ); ?>
						</p>
					</div>
					<?php
				} else if ( 'success' === $this->key_file_delete_result['result'] ) {
					?>
					<div class="updated">
						<p>
							<?php echo esc_html( sprintf( __( 'Xero successfully deleted %s.', 'wc-xero' ), $key_file_path ) ); ?>
						</p>
					</div>
					<?php
				}
			}
		}
    }

}
