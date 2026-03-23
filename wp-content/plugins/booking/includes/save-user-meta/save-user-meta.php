<?php
/**
 * @version     1.0
 * @package     Booking Calendar
 * @subpackage  User_Custom_Data_Saver
 * @category    Functions
 * @author      wpdevelop
 * @link        https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com
 * @modified    2025-06-25
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

class WPBC_User_Custom_Data_Saver {

	private static $ajax_action = 'AJAX_SAVE_USER_META_DATA';
	private static $user_option_prefix = 'booking_custom_';

	/**
	 * Init hooks
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_ajax_handler' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Register AJAX handler
	 */
	public static function register_ajax_handler() {
		add_action( 'wp_ajax_' . self::$ajax_action, array( __CLASS__, 'handle_ajax_save' ) );
	}

	/**
	 * Enqueue JavaScript file and pass AJAX URL
	 */
	public static function enqueue_scripts() {

		$js_url = function_exists( 'wpbc_plugin_url' ) ? wpbc_plugin_url( '/includes/save-user-meta/_out/save-user-meta.js' ) : plugins_url( '_out/user-data-saver.js', WPBC_FILE );

		wp_register_script( 'wpbc-user-data-saver', $js_url, array( 'jquery' ), '1.1', true );
		wp_enqueue_script( 'wpbc-user-data-saver' );

		wp_localize_script( 'wpbc-user-data-saver', 'WPBC_UserDataSaver', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'action'   => self::$ajax_action,
		) );
	}

	/**
	 * Create nonce
	 */
	public static function create_nonce( $action_name ) {
		return wp_create_nonce( $action_name );
	}

	/**
	 * Render a save button or div with onclick and data attributes
	 */
	public static function render_save_button( $args = array() ) {
		$defaults = array(
			'label'        => __( 'Save', 'booking' ),
			'data_name'    => 'custom_data_block',
			'nonce_action' => 'wpbc_custom_data_nonce',
			'user_id'      => get_current_user_id(),
			'element'      => 'div',
			'id'           => '',
			'class'        => 'wpbc-save-button button button-primary',
			'data_fields'  => array(),
		);
		$args     = wp_parse_args( $args, $defaults );

		$tag       = $args['element'];
		$id_attr   = $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '';
		$nonce     = self::create_nonce( $args['nonce_action'] );
		$selectors = implode( ',', $args['data_fields'] );
		$onclick   = 'wpbc_save_custom_user_data_from_element(this);';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<' . $tag . $id_attr . ' class="' . esc_attr( $args['class'] ) . '"' . ' data-wpbc-u-save-name="' . esc_attr( $args['data_name'] ) . '"' . ' data-wpbc-u-save-nonce="' . esc_attr( $nonce ) . '"' . ' data-wpbc-u-save-user-id="' . esc_attr( $args['user_id'] ) . '"' . ' data-wpbc-u-save-action="' . esc_attr( $args['nonce_action'] ) . '"' . ' data-wpbc-u-save-fields="' . esc_attr( $selectors ) . '"' . ' onclick="' . esc_attr( $onclick ) . '"' . '>' . esc_html( $args['label'] ) . '</' . $tag . '>';
	}

	/**
	 * AJAX handler
	 */
	public static function handle_ajax_save() {
		$user_id    = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		$data_name  = isset( $_POST['data_name'] ) ? sanitize_key( wp_unslash( $_POST['data_name'] ) ) : '';
		$data_raw   = isset( $_POST['data_value'] ) ? wp_unslash( $_POST['data_value'] ) : '';    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce_name = isset( $_POST['nonce_action'] ) ? sanitize_key( wp_unslash( $_POST['nonce_action'] ) ) : '';
		$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( empty( $nonce_name ) || ! wp_verify_nonce( $nonce, $nonce_name ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce.' ) );
		}

		if ( empty( $user_id ) || empty( $data_name ) || empty( $data_raw ) ) {
			wp_send_json_error( array( 'message' => 'Missing required parameters.' ) );
		}
		// FixIn: 10.14.15.2.
		if ( wpbc_get_current_user_id() !== $user_id ) {
			wp_send_json_error( array( 'message' => 'Unauthorized: You can only modify your own settings.' ) );
		}
		parse_str( $data_raw, $parsed_data );

		$sanitized_data = array();
		foreach ( $parsed_data as $key => $val ) {

			// $key = sanitize_key( $key ); // This strips square brackets and digits for complex input keys (like rows[0][title]) //.

			$key = preg_replace( '/[^a-zA-Z0-9_\[\]]/', '', $key );  // This preserves structure like rows[0][title] (important for array-style inputs), but still sanitizes.

			$sanitized_data[ $key ] = is_array( $val ) ? array_map( 'sanitize_text_field', $val )
				: sanitize_text_field( $val );
		}

		update_user_option( $user_id, self::$user_option_prefix . $data_name, $sanitized_data );

		wp_send_json_success( array( 'message' => 'Settings saved successfully.' ) );
	}

	/**
	 * Get saved data
	 *
	 * Usage for simple values:
	 *
	 * 		$is_full_screen = WPBC_User_Custom_Data_Saver::get_user_data_value( wpbc_get_current_user_id(), $user_cust_option );
	 * 		$is_full_screen= ( isset( $is_full_screen_data['value'] ) && $is_full_screen_data['value'] === 'On' );
	 */
	public static function get_user_data( $user_id, $data_name ) {
		$option_key = self::$user_option_prefix . sanitize_key( $data_name );
		$data       = get_user_option( $option_key, $user_id );

		return is_array( $data ) ? $data : array();
	}

	/**
	 * Get simple saved value.
	 *
	 * @param $user_id
	 * @param $data_name
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public static function get_user_data_value( $user_id, $data_name, $key = 'value' ) {
		$data = self::get_user_data( $user_id, $data_name );

		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

// Register loading JavaScript and Ajax handlers. In initi used other init hooks, that is why we use 'plugins_loaded'.
add_action( 'plugins_loaded', array( 'WPBC_User_Custom_Data_Saver', 'init' ) );

/*
if ( 0 ) {

	?>
	Example #1:
	<input type="text" id="my_setting" value="Hello"/>
	<?php
	WPBC_User_Custom_Data_Saver::render_save_button( array(
		'label'        => 'Save Setting',
		'data_name'    => 'my_text_value',
		'nonce_action' => 'wpbc_text_value_nonce',
		'id'           => 'wpbc_save_text_btn',
		'data_fields'  => array( '#my_setting' ),
	) );
	?>

	Example #2:
	<input type="text" name="rows[0][title]" id="row_0_title" value="Title 1"/>
	<input type="text" name="rows[0][value]" id="row_0_value" value="100"/>
	<input type="text" name="rows[1][title]" id="row_1_title" value="Title 2"/>
	<input type="text" name="rows[1][value]" id="row_1_value" value="200"/>
	<?php
	WPBC_User_Custom_Data_Saver::render_save_button( array(
		'label'        => 'Save Table Rows',
		'data_name'    => 'my_object_array',
		'nonce_action' => 'wpbc_object_array_nonce',
		'id'           => 'wpbc_save_array_btn',
		'data_fields'  => array( '#row_0_title', '#row_0_value', '#row_1_title', '#row_1_value' ),
	) );
	?>

	Example #3:
	<?php
	$nonce_action = 'wpbc_manual_example_nonce';
	?>
	<input type="text" id="custom_note" value="My note"/>
	<div class="button button-primary"
		 onclick="wpbc_save_custom_user_data_from_element(this)"
		 data-wpbc-u-save-name="user_note"
		 data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
		 data-wpbc-u-save-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
		 data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ); ?>"
		 data-wpbc-u-save-fields="#custom_note">
		Save Note
	</div>

	Example #4:
	<?php
		$nonce_action = 'wpbc_array_rows_nonce';
	?>
	<input type="text" name="rows[0][title]" id="row_0_title" value="Title 1"/>
	<input type="text" name="rows[0][value]" id="row_0_value" value="100"/>
	<input type="text" name="rows[1][title]" id="row_1_title" value="Title 2"/>
	<input type="text" name="rows[1][value]" id="row_1_value" value="200"/>
	<a href="javascript:void(0);"
	   class="button button-primary"
	   onclick="wpbc_save_custom_user_data_from_element(this)"
	   data-wpbc-u-save-name="object_array_example"
	   data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
	   data-wpbc-u-save-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
	   data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ); ?>"
	   data-wpbc-u-save-fields="#row_0_title,#row_0_value,#row_1_title,#row_1_value">
		Save Rows
	</a>

	Example #5:
	<?php $nonce_action = 'wpbc_simple_value_nonce'; ?>
	<a href="javascript:void(0);"
	   class="button button-primary"
	   onclick="wpbc_save_custom_user_data_from_element(this)"
	   data-wpbc-u-save-name="simple_note"
	   data-wpbc-u-save-value="My saved string value"
	   data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
	   data-wpbc-u-save-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
	   data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ); ?>"
	>
	   Save Simple Note
	</a>

	Example #6:
	<?php $nonce_action = 'wpbc_simple_value_nonce'; ?>
	<a href="javascript:void(0);"
	   class="button button-primary"
	   onclick="wpbc_save_custom_user_data_from_element(this)"
	   data-wpbc-u-save-name="simple_note"
	   data-wpbc-u-save-value-json='{"title":"Hello","value":42}'
	   data-wpbc-u-save-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
	   data-wpbc-u-save-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
	   data-wpbc-u-save-action="<?php echo esc_attr( $nonce_action ); ?>"
	>
	   Save Simple Note
	</a>
 	<?php
}
*/