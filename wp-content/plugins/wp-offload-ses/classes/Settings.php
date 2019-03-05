<?php
/**
 * Handle settings for WP Offload SES
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

/**
 * Class Settings
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * The settings key used in the database.
	 *
	 * @var string
	 */
	private $settings_key;

	/**
	 * The settings constant used in defines.
	 *
	 * @var string
	 */
	private $settings_constant;

	/**
	 * The settings array.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Settings that have been defined via constants.
	 *
	 * @var array
	 */
	private $defined_settings;

	/**
	 * Construct the Settings class.
	 *
	 * @param string $settings_key      The settings key used in the database.
	 * @param string $settings_constant The settings constant used in defines.
	 */
	public function __construct( $settings_key, $settings_constant ) {
		$this->settings_key      = $settings_key;
		$this->settings_constant = $settings_constant;
	}

	/**
	 * Get the plugin's settings array
	 *
	 * @param bool $force Get the settings fresh.
	 *
	 * @return array
	 */
	public function get_settings( $force = false) {
		if ( is_null( $this->settings ) || $force ) {
			$this->settings = $this->filter_settings( get_site_option( $this->settings_key ) );
		}

		return $this->settings;
	}

	/**
	 * Get all settings that have been defined via constant for the plugin
	 *
	 * @param bool $force Get the settings fresh.
	 *
	 * @return array
	 */
	public function get_defined_settings( $force = false ) {
		if ( ! defined( $this->settings_constant ) ) {
			$this->defined_settings = array();
			return $this->defined_settings;
		}

		if ( is_null( $this->defined_settings ) || $force ) {
			$this->defined_settings = array();
			$unserialized           = maybe_unserialize( constant( $this->settings_constant ) );
			$unserialized           = is_array( $unserialized ) ? $unserialized : array();

			foreach ( $unserialized as $key => $value ) {
				if ( ! in_array( $key, $this->get_settings_whitelist() ) ) {
					continue;
				}

				if ( is_bool( $value ) || is_null( $value ) ) {
					$value = (int) $value;
				}

				if ( is_numeric( $value ) ) {
					$value = strval( $value );
				} else {
					$value = $this->sanitize_setting( $key, $value );
				}

				$this->defined_settings[ $key ] = $value;
			}

			$this->listen_for_settings_constant_changes();

			// Normalize the defined settings before saving, so we can detect when a real change happens.
			ksort( $this->defined_settings );
			update_site_option( 'wposes_constant_' . $this->settings_constant, $this->defined_settings );
		}

		return $this->defined_settings;
	}

	/**
	 * Gets a single setting that has been defined in the plugin settings constant
	 *
	 * @param string $key     The key of the setting to get.
	 * @param mixed  $default Default to use if not found.
	 *
	 * @return mixed
	 */
	public function get_defined_setting( $key, $default = '' ) {
		$defined_settings = $this->get_defined_settings();
		$setting          = isset( $defined_settings[ $key ] ) ? $defined_settings[ $key ] : $default;

		return $setting;
	}

	/**
	 * Subscribe to changes of the site option used to store the constant-defined settings.
	 */
	protected function listen_for_settings_constant_changes() {
		if ( ! has_action( 'update_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
			$this,
			'settings_constant_changed',
		) ) ) {
			add_action( 'add_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
				$this,
				'settings_constant_added',
			), 10, 3 );
			add_action( 'update_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
				$this,
				'settings_constant_changed',
			), 10, 4 );
		}
	}

	/**
	 * Translate a settings constant option addition into a change.
	 *
	 * @param string $option     Name of the option.
	 * @param mixed  $value      Value the option is being initialized with.
	 * @param int    $network_id ID of the network.
	 */
	public function settings_constant_added( $option, $value, $network_id ) {
		$db_settings = get_site_option( $this->settings_key, array() );
		$this->settings_constant_changed( $option, $value, $db_settings, $network_id );
	}

	/**
	 * Callback for announcing when settings-defined values change.
	 *
	 * @param string $option       Name of the option.
	 * @param mixed  $new_settings Current value of the option.
	 * @param mixed  $old_settings Old value of the option.
	 * @param int    $network_id   ID of the network.
	 */
	public function settings_constant_changed( $option, $new_settings, $old_settings, $network_id ) {
		$old_settings = $old_settings ?: array();

		foreach ( $this->get_settings_whitelist() as $setting ) {
			$old_value = isset( $old_settings[ $setting ] ) ? $old_settings[ $setting ] : null;
			$new_value = isset( $new_settings[ $setting ] ) ? $new_settings[ $setting ] : null;

			if ( $old_value !== $new_value ) {
				/**
				 * Setting-specific hook for setting change.
				 *
				 * @param mixed  $new_value
				 * @param mixed  $old_value
				 * @param string $setting
				 */
				do_action( 'wposes_constant_' . $this->settings_constant . '_changed_' . $setting, $new_value, $old_value, $setting );

				/**
				 * Generic hook for setting change.
				 *
				 * @param mixed  $new_value
				 * @param mixed  $old_value
				 * @param string $setting
				 */
				do_action( 'wposes_constant_' . $this->settings_constant . '_changed', $new_value, $old_value, $setting );
			}
		}
	}

	/**
	 * Filter the plugin settings array
	 *
	 * @param array $settings The settings to filter.
	 *
	 * @return array $settings
	 */
	public function filter_settings( $settings ) {
		$defined_settings = $this->get_defined_settings();
		// Bail early if there are no defined settings.
		if ( empty( $defined_settings ) ) {
			return $settings;
		}

		foreach ( $defined_settings as $key => $value ) {
			$settings[ $key ] = $value;
		}

		return $settings;
	}

	/**
	 * Get the whitelisted settings for the plugin.
	 *
	 * @param array $settings_whitelist Default whitelist.
	 *
	 * @return array
	 */
	public function get_settings_whitelist( $settings_whitelist = array() ) {
		$settings_whitelist = array(
			'send-via-ses',
			'region',
			'default-email',
			'default-email-name',
			'reply-to',
			'return-path',
			'log-duration',
			'completed-setup',
			'enable-open-tracking',
			'enable-click-tracking',
		);
		return apply_filters( 'wposes_settings_whitelist', $settings_whitelist );
	}

	/**
	 * List of settings that should skip full sanitize.
	 *
	 * @param array $skip_sanitize_settings Settings to skip santitization.
	 *
	 * @return array
	 */
	public function get_skip_sanitize_settings( $skip_sanitize_settings = array() ) {
		return apply_filters( 'wposes_skip_sanitize_settings', $skip_sanitize_settings );
	}

	/**
	 * Sanitize a setting value, maybe.
	 *
	 * @param string $key   Setting to sanitize.
	 * @param mixed  $value Value of setting to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_setting( $key, $value ) {
		$skip_sanitize = $this->get_skip_sanitize_settings();
		if ( in_array( $key, $skip_sanitize ) ) {
			$value = wp_strip_all_tags( $value );
		} else {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Get a specific setting
	 *
	 * @param string $key     The key of the setting to get.
	 * @param string $default The default value if not found.
	 *
	 * @return string
	 */
	public function get_setting( $key, $default = '' ) {
		$this->get_settings();
		if ( isset( $this->settings[ $key ] ) ) {
			$setting = $this->settings[ $key ];
		} else {
			$setting = $default;
		}

		return apply_filters( 'wposes_get_setting', $setting, $key );
	}

	/**
	 * Gets arguements used to render a setting view.
	 *
	 * @param string $key Key of the setting.
	 *
	 * @return array
	 */
	public function get_setting_args( $key ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$is_defined = $this->get_defined_setting( $key, false );

		$args = array(
			'key'           => $key,
			'disabled'      => false,
			'disabled_attr' => '',
			'tr_class'      => str_replace( '_', '-', $wp_offload_ses->get_plugin_prefix() . '-' . $key . '-container' ),
			'setting_msg'   => '',
			'is_defined'    => false,
		);

		if ( false !== $is_defined ) {
			$args['is_defined']    = true;
			$args['disabled']      = true;
			$args['disabled_attr'] = 'disabled="disabled"';
			$args['tr_class']      .= ' wposes-defined-setting';
			$args['setting_msg']   = '<span class="wposes-defined-in-config">' . __( 'defined in wp-config.php', 'wp-offload-ses' ) . '</span>';
		}

		return $args;
	}

	/**
	 * Delete a setting
	 *
	 * @param string $key The key of the setting to delete.
	 */
	public function remove_setting( $key ) {
		$this->get_settings();
		if ( isset( $this->settings[ $key ] ) ) {
			unset( $this->settings[ $key ] );
		}
	}

	/**
	 * Set a setting
	 *
	 * @param string $key   The key of the setting to set.
	 * @param mixed  $value The value of the setting to set.
	 */
	public function set_setting( $key, $value ) {
		$this->get_settings();

		$this->settings[ $key ] = $value;
	}

	/**
	 * Bulk set the settings array
	 *
	 * @param array $settings The settings to set.
	 */
	public function set_settings( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Save the settings to the database
	 */
	public function save_settings() {
		if ( is_array( $this->settings ) ) {
			ksort( $this->settings );
		}

		$this->update_site_option( $this->settings_key, $this->settings );
	}

	/**
	 * Update site option.
	 *
	 * @param string $option   The key of the option to update.
	 * @param mixed  $value    The value of the option.
	 * @param bool   $autoload If it should autoload.
	 *
	 * @return bool
	 */
	public function update_site_option( $option, $value, $autoload = true ) {
		if ( is_multisite() ) {
			return update_site_option( $option, $value );
		}

		return update_option( $option, $value, $autoload );
	}

}