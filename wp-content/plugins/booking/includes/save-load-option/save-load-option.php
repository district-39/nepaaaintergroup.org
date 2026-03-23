<?php
/**
 * General Option Loader/Saver (AJAX)
 *
 * - Save complex structures by posting RAW JSON (string) -> json_decode() -> array stored via update_option().
 * - Save simple scalars (e.g., "On"/"Off") as-is.
 * - Load returns stored value (array/scalar).
 * - Enqueues small JS/CSS that provide generic save/load helpers with busy (spinner) UI.
 *
 * file: ../includes/save-load-option/save-load-option.php
 *
 * Data attributes on clickable elements:
 *   Save:
 *     data-wpbc-u-save-name      — option key (required)
 *     data-wpbc-u-save-nonce     — nonce value (required for SAVE)
 *     data-wpbc-u-save-action    — nonce action (required for SAVE)
 *     data-wpbc-u-save-value     — RAW scalar to save (optional)
 *     data-wpbc-u-save-value-json— JSON string to save (optional)
 *     data-wpbc-u-save-fields    — CSV of selectors; values serialized with jQuery.param (optional)
 *     data-wpbc-u-busy-text      — custom text during AJAX (optional)
 *     data-wpbc-u-save-callback  — window function name to call on success (optional)
 *
 *   Load:
 *     data-wpbc-u-load-name      — option key (required)
 *     data-wpbc-u-busy-text      — custom text during AJAX (optional)
 *     data-wpbc-u-load-callback  — window function name to receive loaded value (optional)
 *
 * JS Events:
 *   jQuery(document)
 *     .on('wpbc:option:beforeSave', function (e, $el, payload) {})
 *     .on('wpbc:option:afterSave',  function (e, response) {})
 *     .on('wpbc:option:beforeLoad', function (e, $el, name) {})
 *     .on('wpbc:option:afterLoad',  function (e, response) {})
 *
 * @package   Booking Calendar
 * @author    wpdevelop
 * @since     11.0.0
 * @version   1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wpbc_option_saver_loader {

	private static $ajax_action_save = 'wpbc_ajax_option_save';
	private static $ajax_action_load = 'wpbc_ajax_option_load';
	private static $option_prefix    = '';
	private static $asset_version    = '1.0.1';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_ajax_handlers' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Register AJAX handlers (logged-in admin).
	 *
	 * @return void
	 */
	public static function register_ajax_handlers() {
		add_action( 'wp_ajax_' . self::$ajax_action_save, array( __CLASS__, 'handle_ajax_save' ) );
		add_action( 'wp_ajax_' . self::$ajax_action_load, array( __CLASS__, 'handle_ajax_load' ) );
	}

	/**
	 * Enqueue JS/CSS for admin pages.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {

		// Optional screen check.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			$ok     = apply_filters( 'wpbc_option_saver_loader_enqueue', true, $screen );
			if ( ! $ok ) {
				return;
			}
		}

		$base_url = plugins_url( '', defined( 'WPBC_FILE' ) ? WPBC_FILE : __FILE__ );
		$js_url   = $base_url . '/includes/save-load-option/_out/save-load-option.js';
		$css_url  = $base_url . '/includes/save-load-option/_out/save-load-option.css';

		wp_register_style( 'wpbc-save-load-option', $css_url, array(), self::$asset_version );
		wp_enqueue_style( 'wpbc-save-load-option' );

		wp_register_script( 'wpbc-save-load-option', $js_url, array( 'jquery' ), self::$asset_version, true );
		wp_enqueue_script( 'wpbc-save-load-option' );

		wp_localize_script(
			'wpbc-save-load-option',
			'wpbc_option_saver_loader_config',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'action_save' => self::$ajax_action_save,
				'action_load' => self::$ajax_action_load,
			)
		);
	}

	/**
	 * AJAX: Save option.
	 *
	 * Expected POST:
	 * - data_name     string  Option key.
	 * - data_value    string  RAW scalar | query-string | JSON string.
	 * - nonce_action  string  Nonce action name.
	 * - nonce         string  Nonce value.
	 *
	 * @return void
	 */
	public static function handle_ajax_save() {

		$capability = apply_filters( 'wpbc_option_saver_loader_cap_save', 'manage_options' );
		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to save settings.', 'booking' ) ) );
		}

		$data_name   = isset( $_POST['data_name'] ) ? sanitize_key( wp_unslash( $_POST['data_name'] ) ) : '';
		/* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */
		$data_raw    = isset( $_POST['data_value'] ) ? wp_unslash( $_POST['data_value'] ) : '';
		// Optional: split JSON object into multiple options.
		$data_mode   = isset( $_POST['data_mode'] ) ? sanitize_key( wp_unslash( $_POST['data_mode'] ) ) : '';
		$data_fields = isset( $_POST['data_fields'] ) ? sanitize_text_field( wp_unslash( $_POST['data_fields'] ) ) : '';

		$nonce_name  = isset( $_POST['nonce_action'] ) ? sanitize_key( wp_unslash( $_POST['nonce_action'] ) ) : '';
		$nonce_value = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( empty( $nonce_name ) || ! wp_verify_nonce( $nonce_value, $nonce_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'booking' ) ) );
		}

		if ( empty( $data_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing data name.', 'booking' ) ) );
		}

		$value_to_store = self::normalize_incoming_value( $data_raw );

		// Split mode: JSON object => multiple options saved separately.
		if ( 'split' === $data_mode && is_array( $value_to_store ) ) {

			$allowed_keys = array();
			if ( '' !== trim( $data_fields ) ) {
				$parts = explode( ',', (string) $data_fields );
				foreach ( $parts as $p ) {
					$k = sanitize_key( trim( (string) $p ) );
					if ( '' !== $k ) {
						$allowed_keys[ $k ] = true;
					}
				}
			}

			$saved = array();

			foreach ( $value_to_store as $k => $v ) {

				if ( ! is_scalar( $k ) ) {
					continue;
				}

				$opt_key = sanitize_key( (string) $k );
				if ( '' === $opt_key ) {
					continue;
				}

				// If allowlist provided, only save those keys.
				if ( ! empty( $allowed_keys ) && ! isset( $allowed_keys[ $opt_key ] ) ) {
					continue;
				}

				// Values: allow scalar or arrays (already sanitized by normalize_incoming_value()).
				$opt_val = $v;
				if ( is_scalar( $opt_val ) ) {
					$opt_val = sanitize_text_field( (string) $opt_val );
				} elseif ( is_array( $opt_val ) ) {
					$opt_val = self::sanitize_mixed_value( $opt_val );
				} else {
					$opt_val = '';
				}

				self::update_option( self::$option_prefix . $opt_key, $opt_val );
				$saved[ $opt_key ] = $opt_val;
			}

			if ( empty( $saved ) ) {
				wp_send_json_error( array( 'message' => __( 'Nothing to save.', 'booking' ) ) );
			}

			wp_send_json_success(
				array(
					'message' => __( 'Settings saved.', 'booking' ),
					'value'   => $saved,
					'mode'    => 'split',
				)
			);
		}

		// Default: store as a single option (scalar/array).
		self::update_option( self::$option_prefix . $data_name, $value_to_store );

		// Return stored value (useful for client callbacks / UI sync).
		wp_send_json_success(
			array(
				'message' => __( 'Settings saved.', 'booking' ),
				'value'   => $value_to_store,
			)
		);
	}

	/**
	 * AJAX: Load option.
	 *
	 * Expected GET:
	 * - data_name  string  Option key.
	 *
	 * @return void
	 */
	public static function handle_ajax_load() {

		$capability = apply_filters( 'wpbc_option_saver_loader_cap_load', 'manage_options' );
		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to load settings.', 'booking' ) ) );
		}

		/* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */
		$data_name = isset( $_GET['data_name'] ) ? sanitize_key( wp_unslash( $_GET['data_name'] ) ) : '';
		if ( empty( $data_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing data name.', 'booking' ) ) );
		}

		$option_key = self::$option_prefix . $data_name;
		$value      = self::get_option( $option_key, array() );

		wp_send_json_success( array( 'value' => $value ) );
	}

	/**
	 * Normalize payload: prefer JSON -> array; fallback to query-string -> array; else scalar string.
	 *
	 * @param string $data_raw Raw input.
	 * @return mixed
	 */
	private static function normalize_incoming_value( $data_raw ) {

		if ( ! is_string( $data_raw ) || '' === $data_raw ) {
			return '';
		}

		$maybe_json = trim( $data_raw );

		// JSON path.
		if (
			0 === strpos( $maybe_json, '{' ) || 0 === strpos( $maybe_json, '[' ) ||
			'null' === strtolower( $maybe_json ) || 'true' === strtolower( $maybe_json ) ||
			'false' === strtolower( $maybe_json ) || is_numeric( $maybe_json )
		) {
			$decoded = json_decode( $maybe_json, true );
			if ( null !== $decoded && JSON_ERROR_NONE === json_last_error() ) {
				return self::sanitize_mixed_value( $decoded );
			}
		}

		// Query-string path.
		if ( false !== strpos( $data_raw, '=' ) || false !== strpos( $data_raw, '&' ) || false !== strpos( strtolower( $data_raw ), '%5b' ) ) {
			$parsed = array();
			parse_str( $data_raw, $parsed ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return self::sanitize_kv_array_preserve_brackets( $parsed );
		}

		// Scalar.
		return sanitize_text_field( $data_raw );
	}

	/**
	 * Recursively sanitize mixed values.
	 *
	 * @param mixed $value Mixed value.
	 * @return mixed
	 */
	private static function sanitize_mixed_value( $value ) {

		if ( is_array( $value ) ) {
			$out = array();
			foreach ( $value as $k => $v ) {
				$kk         = is_string( $k ) ? preg_replace( '/[^a-zA-Z0-9_\-\[\]]/', '', $k ) : $k;
				$out[ $kk ] = self::sanitize_mixed_value( $v );
			}
			return $out;
		}

		if ( is_scalar( $value ) ) {
			return sanitize_text_field( (string) $value );
		}

		return '';
	}

	/**
	 * Sanitize arrays parsed from query-string, preserving bracket keys.
	 *
	 * @param array $parsed_data Parsed data.
	 * @return array
	 */
	private static function sanitize_kv_array_preserve_brackets( $parsed_data ) {

		$sanitized_data = array();

		if ( empty( $parsed_data ) || ! is_array( $parsed_data ) ) {
			return $sanitized_data;
		}

		foreach ( $parsed_data as $key => $val ) {
			$key = preg_replace( '/[^a-zA-Z0-9_\-\[\]]/', '', (string) $key );
			if ( is_array( $val ) ) {
				$sanitized_data[ $key ] = self::sanitize_mixed_value( $val );
			} else {
				$sanitized_data[ $key ] = sanitize_text_field( $val );
			}
		}

		return $sanitized_data;
	}

	/**
	 * Update option (Booking Calendar wrapper if present).
	 *
	 * @param string $option_key Key.
	 * @param mixed  $value      Value.
	 * @return void
	 */
	private static function update_option( $option_key, $value ) {
		if ( function_exists( 'update_bk_option' ) ) {
			update_bk_option( $option_key, $value );
		} else {
			update_option( $option_key, $value );
		}
	}

	/**
	 * Get option (Booking Calendar wrapper if present).
	 *
	 * @param string $option_key Key.
	 * @param mixed  $default    Default.
	 * @return mixed
	 */
	private static function get_option( $option_key, $default = false ) {
		if ( function_exists( 'get_bk_option' ) ) {
			$val = get_bk_option( $option_key );
			return ( null === $val ) ? $default : $val;
		}
		return get_option( $option_key, $default );
	}
}

add_action( 'plugins_loaded', array( 'wpbc_option_saver_loader', 'init' ) );


/**
 * == Usage examples ==
 *
 * 1) Save RAW scalar (On/Off).
 *

<?php
$opt_name     = 'booking_timeslot_picker';
$nonce_action = 'wpbc_nonce_' . $opt_name;
?>
<a  href="javascript:void(0);"
	class="button button-secondary"
	onclick="(function(btn){var $=jQuery, $chk=$('.js-toggle-timeslot-picker').first(); $(btn).data('wpbc-u-save-value',$chk.is(':checked')?'On':'Off'); wpbc_save_option_from_element(btn);})(this)"
	data-wpbc-u-save-name="<?php echo esc_attr( $opt_name ); ?>"
	data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
	data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ); ?>"
	data-wpbc-u-busy-text="<?php esc_attr_e( 'Saving…', 'booking' ); ?>">
	<?php esc_html_e( 'Save Toggle', 'booking' ); ?>
</a>

 *
 * 2) Save complex structure (RAW JSON)
 *

<?php
$opt_name     = 'wpbc_bfb_form_structure';
$nonce_action = 'wpbc_nonce_' . $opt_name;
?>
<a  href="javascript:void(0);"
	class="button button-primary"
	onclick="(function(btn){var s=window.wpbc_bfb && window.wpbc_bfb.get_structure ? window.wpbc_bfb.get_structure() : []; jQuery(btn).data('wpbc-u-save-value-json', JSON.stringify(s)); wpbc_save_option_from_element(btn);})(this)"
	data-wpbc-u-save-name="<?php echo esc_attr( $opt_name ); ?>"
	data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
	data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ) ; ?>"
	data-wpbc-u-busy-text="<?php esc_attr_e( 'Saving…', 'booking' ); ?>">
	<?php esc_html_e( 'Save Form Structure', 'booking' ); ?>
</a>

 *
 * 3) Load option and apply
 *

<a  href="javascript:void(0);"
	class="button"
	onclick="wpbc_load_option_from_element(this)"
	data-wpbc-u-load-name="wpbc_bfb_form_structure"
	data-wpbc-u-load-callback="wpbc_bfb__on_structure_loaded"
	data-wpbc-u-busy-text="<?php esc_attr_e( 'Loading…', 'booking' ); ?>">
	<?php esc_html_e( 'Load Form Structure', 'booking' ); ?>
</a>
<script>
function wpbc_bfb__on_structure_loaded(val){
	try {
		if ( typeof val === 'string' ) { val = JSON.parse(val); }
		if ( window.wpbc_bfb && typeof window.wpbc_bfb.load_saved_structure === 'function' ) {
			window.wpbc_bfb.load_saved_structure( val || [] );
		}
	} catch(e){ console.error(e); }
}
</script>
 *
 */