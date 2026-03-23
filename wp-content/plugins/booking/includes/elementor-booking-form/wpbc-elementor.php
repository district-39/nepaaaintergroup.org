<?php
/**
 * includes/elementor-booking-form/wpbc-elementor.php
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// ---------------------------------------------------------------------------------------------------------------------
// == Add Widgets ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Define Booking Calendar Widget.
 *
 * @param $widgets_manager
 *
 * @return void
 */
function wpbc_elementor__register_widget__booking_form( $widgets_manager ) {

	require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/elementor-widget-booking.php';

	$widgets_manager->register( new \WPBC_Elementor_WPBC_Booking_Form_1() );
}
add_action( 'elementor/widgets/register', 'wpbc_elementor__register_widget__booking_form' );

/**
 * Add custom category  for the Elementor Widgets.
 *
 * @param object $elements_manager - Elementor manager.
 *
 * @return void
 */
function wpbc_elementor__add_widget_categories( $elements_manager ) {

	$elements_manager->add_category( 'wpbc', [
			'title' => 'Booking Calendar',
			'icon'  => 'wpbc_logo_in_elementor',
		] );
}
add_action( 'elementor/elements/categories_registered', 'wpbc_elementor__add_widget_categories' );


// ---------------------------------------------------------------------------------------------------------------------
// == JS / CSS ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Load CSS,  where defined SVG icon
 * @return void
 */
function wpbc_elementor__enqueue_editor_styles() {

	wp_enqueue_style( 'elementor-wpbc-editor', wpbc_plugin_url( '/includes/elementor-booking-form/_out/wpbc-elementor-editor.css' ), array(), WP_BK_VERSION_NUM );
}
add_action( 'elementor/editor/after_enqueue_styles', 'wpbc_elementor__enqueue_editor_styles' );

/**
 *
 * function my_plugin_register_editor_styles() {
 *   wp_register_style( 'elementor-wpbc-client-pages', wpbc_plugin_url( '/css/client.css' ), array(), WP_BK_VERSION_NUM );
 * }
 * add_action( 'elementor/editor/after_register_styles', 'my_plugin_register_editor_styles' );
 */

/**
 * Load  JAVASCRIPT files.
 *
 * @return void
 */
function wpbc_elementor__enqueue_save_calendar_skin() {

	wp_enqueue_script( 'wpbc-elementor-save-skin', wpbc_plugin_url( '/includes/elementor-booking-form/_out/wpbc-elementor.js' ), array('jquery', 'elementor-editor' ), WP_BK_VERSION_NUM, true );

	wp_localize_script( 'wpbc-elementor-save-skin', 'WPBC_Ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'wpbc_save_skin_nonce' ),
	) );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'wpbc_elementor__enqueue_save_calendar_skin' );


// ---------------------------------------------------------------------------------------------------------------------
// == Controls ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Register Booking Resource Selection Control.
 *
 * Include control file and register control class.
 *
 * @since 1.0.0
 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
 * @return void
 */
function wpbc_elementor__register_resource_selection_control( $controls_manager ) {

	if ( class_exists( 'wpdev_bk_personal' ) ) {
		require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/controls/elementor_resource_selection.php';

		$controls_manager->register( new \WPBC_Elementor_Booking_Resource_Selection_Control() );
	}
}
add_action( 'elementor/controls/register', 'wpbc_elementor__register_resource_selection_control' );

/**
 * Register Booking Custom_Form Selection Control.
 *
 * Include control file and register control class.
 *
 * @since 1.0.0
 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
 * @return void
 */
function wpbc_elementor__register_custom_form_selection_control( $controls_manager ) {

	if ( wpbc_is_custom_forms_enabled() ) {

		require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/controls/elementor_custom_form.php';

		$controls_manager->register( new \WPBC_Elementor_Custom_Form_Selection_Control() );
	}
}
add_action( 'elementor/controls/register', 'wpbc_elementor__register_custom_form_selection_control' );

/**
 * Register Calendar Skin Selection Control.
 *
 * Include control file and register control class.
 *
 * @since 1.0.0
 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
 * @return void
 */
function wpbc_elementor__register_calendar_skin_selection_control( $controls_manager ) {

	require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/controls/elementor_calendar_skin_selection.php';

	$controls_manager->register( new \WPBC_Elementor_Calendar_Skin_Selection_Control() );
}
add_action( 'elementor/controls/register', 'wpbc_elementor__register_calendar_skin_selection_control' );

/**
 * Register Go Button
 *
 * Include control file and register control class.
 *
 * @since 1.0.0
 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
 * @return void
 */
function wpbc_elementor__register_go_button_control( $controls_manager ) {

	require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/controls/elementor_go_button.php';

	$controls_manager->register( new \WPBC_Elementor_Go_Button_Control() );
}
add_action( 'elementor/controls/register', 'wpbc_elementor__register_go_button_control' );


// ---------------------------------------------------------------------------------------------------------------------
// == Ajax ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Ajax Callback.
 *
 * @return void
 */
function wpbc_save_calendar_skin_callback() {

	check_ajax_referer( 'wpbc_save_skin_nonce' );
	$selected_calendar_skin = ( ! empty( $_POST['skin'] ) ) ? sanitize_text_field( wp_unslash( $_POST['skin'] ) ) : '';
	$html_id                = ( ! empty( $_POST['html_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['html_id'] ) ) : '';
	$calendar_skin_to_save  = '';

	if ( ! empty( $selected_calendar_skin ) ) {

		// FixIn: 10.3.0.5.
		$upload_dir              = wp_upload_dir();
		$custom_user_skin_folder = $upload_dir['basedir'];
		$custom_user_skin_url    = $upload_dir['baseurl'];

		$calendar_skin_to_save = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL, $custom_user_skin_folder, $custom_user_skin_url ), '', $selected_calendar_skin );

		// Check if this skin exist in the plugin  folder.
		if (
			( file_exists( WPBC_PLUGIN_DIR . $calendar_skin_to_save ) ) ||
			( file_exists( $custom_user_skin_folder . $calendar_skin_to_save ) )
		) {
			update_bk_option( 'booking_skin', $calendar_skin_to_save );
		}
	}


	if ( empty( $calendar_skin_to_save ) ) {
		wp_send_json_error( 'No skin provided.' );
	}

	wp_send_json_success(
		array(
			'html_id'   => $html_id,
			'url_full'  => $selected_calendar_skin,
			'url_saved' => $calendar_skin_to_save,
		)
	);
}
add_action('wp_ajax_wpbc_save_calendar_skin', 'wpbc_save_calendar_skin_callback');


// ---------------------------------------------------------------------------------------------------------------------
// == Warning ==
// ---------------------------------------------------------------------------------------------------------------------
function wpbc_elementor_warn_text_widget_shortcodes( $widget_content, $widget ) {

	if ( ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
		return $widget_content;
	}

	// Only Text Editor widget sanitizes with wp_kses_post().
	if ( 'text-editor' !== $widget->get_name() ) {
		return $widget_content;
	}

	$settings = array();
	if ( method_exists( $widget, 'get_settings_for_display' ) ) {
		$settings = $widget->get_settings_for_display();
	}

	$raw = ( isset( $settings['editor'] ) ) ? (string) $settings['editor'] : '';
	if ( '' === $raw ) {
		return $widget_content;
	}

	// Detect Booking Calendar shortcodes in the raw editor content.
	if ( ! preg_match( '/\[(booking|bookingcalendar|bookingform|bookingselect|bookingtimeline)\b/i', $raw ) ) {
		return $widget_content;
	}

	// Show warning to admins and in Elementor editor mode.
	$show_warning = false;

	if ( function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) ) {
		$show_warning = true;
	}

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->editor ) ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$show_warning = true;
		}
	}

	if ( ! $show_warning ) {
		// For visitors, either return original (even if broken),
		// or replace shortcode area with a softer public message.
		return $widget_content;
	}

	// Prepend warning before the sanitized output.
	$widget_content = wpbc_elementor_build_text_widget_warning() . $widget_content;

	return $widget_content;
}
add_filter( 'elementor/widget/render_content', 'wpbc_elementor_warn_text_widget_shortcodes', 20, 2 );


/**
 * Build Elementor Text Editor widget warning for Booking Calendar shortcodes.
 *
 * @param string $faq_url
 *
 * @return string HTML
 */
function wpbc_elementor_build_text_widget_warning( $faq_url = '' ) {

	$faq_url = ( '' !== $faq_url ) ? $faq_url : 'https://wpbookingcalendar.com/faq/elementor-3-33-5-booking-calendar/';

	$warning  = '<div class="wpbc-elementor-warning" role="alert" style="border:1px solid #d63638;padding:12px;margin:10px 0;background:#fff;">';
	$warning .= '<strong>' . esc_html__( 'Booking Calendar Notice', 'booking' ) . '</strong>';

	$warning .= '<p style="margin:8px 0 0 0;">' .
		esc_html( 'This Booking Calendar shortcode is placed inside an Elementor Text Editor widget.' ) .
		'</p>';

	$warning .= '<p style="margin:8px 0 0 0;">' .
		esc_html( 'Since Elementor 3.33.5, the Elementor Text Editor widget can strip the booking form markup, which prevents the calendar and form from working correctly.' ) .
		'</p>';

	$warning .= '<p style="margin:8px 0 0 0;">' . esc_html__( 'Please use one of these instead:', 'booking' ) . '</p>';

	$warning .= '<ul style="margin:8px 0 0 18px;">';
	$warning .= '<li>' . esc_html( 'Elementor Shortcode widget' ) . '</li>';
	$warning .= '<li>' . esc_html( 'The native Booking Calendar Elementor widget' ) . '</li>';
	$warning .= '<li>' .
		esc_html__( 'More details in the FAQ:', 'booking' ) . ' ' .
		'<a href="' . esc_url( $faq_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $faq_url ) . '</a>' .
		'</li>';
	$warning .= '</ul>';

	$warning .= '</div>';

	return $warning;
}


add_action( 'elementor/frontend/after_register_scripts', function() {
	wp_register_script( 'wpbc-elementor-frontend-bridge', wpbc_plugin_url( '/includes/elementor-booking-form/_out/wpbc-elementor-frontend-bridge.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, true );
} );

add_action( 'elementor/frontend/after_register_styles', function() {
		wp_register_style( 'wpbc-elementor-frontend-bridge', wpbc_plugin_url( '/includes/elementor-booking-form/_out/wpbc-elementor-frontend-bridge.css' ), array(), WP_BK_VERSION_NUM );
} );

add_filter( 'wpbc_force_client_assets', function( $force ) {

	if ( $force ) {
		return true;
	}

	// If Elementor widget exists on the page, Elementor enqueues the bridge handle.
	if ( wp_script_is( 'wpbc-elementor-frontend-bridge', 'enqueued' ) || wp_style_is( 'wpbc-elementor-frontend-bridge', 'enqueued' ) ) {
		return true;
	}

	return false;
} );
