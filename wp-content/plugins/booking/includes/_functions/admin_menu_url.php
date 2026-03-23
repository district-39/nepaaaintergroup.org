<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Admin Menu Functions
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-03
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// =====================================================================================================================
// ==  URL  functions  ==
// =====================================================================================================================

/**
 * Get absolute URL to  relative plugin path.
 *  Possibly to load minified version of file,  if its exist
 * @param string $path    - path
 * @return string
 */
function wpbc_plugin_url( $path ) {

	return trailingslashit( WPBC_PLUGIN_URL ) . ltrim( $path, '/\\' );

}

// Set URL from absolute to relative (starting from /)
function wpbc_set_relative_url( $url ){

	$url = esc_url_raw($url);

	$url_path = wp_parse_url($url,  PHP_URL_PATH);
	$url_path =  ( empty($url_path) ? $url : $url_path );

	$url =  trim($url_path, '/');
	return  '/' . $url;
}

/**
 * Get Relative URL
 *
 * @param $maybe_absolute_link
 *
 * @return string
 */
function wpbc_make_link_relative( $maybe_absolute_link ) {

	if ( $maybe_absolute_link == get_option( 'siteurl' ) ) {
		$maybe_absolute_link = '/';
	}

	$maybe_absolute_link = '/' . trim( wp_make_link_relative( $maybe_absolute_link ), '/' );

	return $maybe_absolute_link;
}

/**
 * Get  Absolute URL  (check for languages)
 *
 * @param $maybe_relative_link
 *
 * @return string
 */
function wpbc_make_link_absolute( $maybe_relative_link ){

	if ( ( $maybe_relative_link != home_url() ) && ( strpos( $maybe_relative_link, 'http' ) !== 0 ) ) {

		$maybe_relative_link = wpbc_lang( $maybe_relative_link );           // FixIn: 8.4.5.1.

		$maybe_relative_link = home_url() . '/' . trim( wp_make_link_relative( $maybe_relative_link ), '/' );        // FixIn: 7.0.1.20.
	}

	return esc_js( $maybe_relative_link );
}

/**
 * Redirect browser to a specific page
 *
 * @param string $url - URL of page to redirect
 */
function wpbc_redirect( $url ) {

	$url = wpbc_make_link_absolute( $url );

	$url = html_entity_decode( esc_url( $url ) );

	echo '<script type="text/javascript">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo 'window.location.href="' . $url . '";';
	echo '</script>';
	echo '<noscript>';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
	echo '</noscript>';
}


// =====================================================================================================================
// ==  Admin Menu Pages and URLs  ==
// =====================================================================================================================

/**
 * Check  if we edit or create a new page in   WordPress ?
 * @return bool
 */
function wpbc_is_on_edit_page() {

	// FixIn: 9.9.0.39.

	// Elementor.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( ( ! empty( $_REQUEST['action'] ) ) && ( ( 'elementor' === $_REQUEST['action'] ) || ( 'elementor_ajax' === $_REQUEST['action'] ) ) ) {
		return false;
	}

	if ( ( ! empty( $GLOBALS['pagenow'] ) ) && ( is_admin() ) ) {
		if (
			( 'post.php' === $GLOBALS['pagenow'] )                       // Edit - Post / Page.
			|| ( 'post-new.php' === $GLOBALS['pagenow'] )                // Add New - Post / Page.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			// || ( ( 'admin-ajax.php' === $GLOBALS['pagenow'] ) && ( ! empty( $_REQUEST['action'] ) ) && ( 'elementor_ajax' === $_REQUEST['action'] ) )   // Elementor Edit page - Ajax.
		) {
			return true;
		}
	}

	return false;
}

/**
 * Get URL to specific Admin Menu page
 *
 * @param string $menu_type         -   { booking | add | resources | settings }
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_menu_url( $menu_type, $is_absolute_url = true, $is_old = true) {

	$is_old = false;

	switch ( $menu_type) {

		case 'booking':                                                     // Bookings.
		case 'bookings':
		case 'booking-listing':
		case 'bookings-listing':
		case 'listing':
		case 'overview':
		case 'calendar-overview':
		case 'timeline':
			$link = 'wpbc';
			break;

		case 'add':                                                         // Add New Booking.
		case 'add-bookings':
		case 'add-booking':
		case 'new':
		case 'new-bookings':
		case 'new-booking':
			$link = 'wpbc-new';
			break;

		case 'availability':
			$link = 'wpbc-availability';
			break;

		case 'price':
			$link = 'wpbc-prices';
			break;

		case 'resources':                                                   // Resources.
		case 'booking-resources':
			$link = 'wpbc-resources';
			break;

		case 'settings':                                                    // Settings.
		case 'options':
			$link = 'wpbc-settings';
			break;

		case 'setup':                                                    	// Setup.
			$link = 'wpbc-setup';
			break;

		default:                                                            // Bookings.
			$link = 'wpbc';
			break;
	}

	if ( $is_absolute_url ) {
		$link = admin_url( 'admin.php' ) . '?page=' . $link ;
	}

	return $link;
}


// ---------------------------------------------------------------------------------------------------------------------
// ==  URL of Admin Menu pages  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get URL of Booking Listing or Calendar Overview page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_bookings_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'booking', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Availability page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_availability_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'availability', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Availability page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_price_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'price', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Add booking page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_new_booking_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'add', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Resources page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_resources_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'resources', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Settings page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_settings_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'settings', $is_absolute_url, $is_old );
}

/**
 * Get URL of Booking > Setup page
 *
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true }
 * @return string                   - URL  to  menu
 */
function wpbc_get_setup_wizard_page_url( $is_absolute_url = true, $is_old = true ) {
	return wpbc_get_menu_url( 'setup', $is_absolute_url, $is_old );
}


// -----------------------------------------------------------------------------------------------------------------
// ==  Is this specific admin page ?  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Check if this Booking Listing or Calendar Overview page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_this_plugin_page( $server_param = 'REQUEST_URI' ) {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc' ) !== false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking Listing or Calendar Overview page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_bookings_page( $server_param = 'REQUEST_URI' ) {
	// Old.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'wpdev-booking.phpwpdev-booking' ) !== false ) && ( strpos( $_SERVER[ $server_param ], 'wpdev-booking.phpwpdev-booking-reservation' ) === false ) ) {
		return true;
	}
	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc' ) !== false ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-' ) === false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking > Add booking page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_new_booking_page( $server_param = 'REQUEST_URI' ) {
	// Old.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'wpdev-booking.phpwpdev-booking-reservation' ) !== false ) ) {
		return true;
	}
	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-new' ) !== false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this WP Booking Calendar > Settings > Booking Form page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_settings_form_page( $server_param = 'REQUEST_URI' ) {
	// Regular  user overwrite settings.

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-settings' ) !== false ) && ( ( strpos( $_SERVER[ $server_param ], '&tab=form' ) !== false ) || ( ( class_exists( 'wpdev_bk_multiuser' ) ) && ( ! empty( $_REQUEST['tab'] ) ) && ( 'form' === $_REQUEST['tab'] ) ) ) ) {
		return true;
	}

	return false;
}


/**
 * Check if this WP Booking Calendar > Settings > Booking Form Builder (BFB) page.
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_settings_bfb_page( $server_param = 'REQUEST_URI' ) {
	// Regular  user overwrite settings.

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-settings' ) !== false ) && ( ( strpos( $_SERVER[ $server_param ], '&tab=builder_booking_form' ) !== false ) || ( ( class_exists( 'wpdev_bk_multiuser' ) ) && ( ! empty( $_REQUEST['tab'] ) ) && ( 'builder_booking_form' === $_REQUEST['tab'] ) ) ) ) {
		return true;
	}

	return false;
}


/**
 * Check if this WP Booking Calendar > Settings > Booking Form page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_settings_color_themes_page( $server_param = 'REQUEST_URI' ) {
	// Regular  user overwrite settings.

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( false !== strpos( $_SERVER[ $server_param ], 'page=wpbc-settings' ) ) && ( false !== strpos( $_SERVER[ $server_param ], '&tab=color_themes' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking > Availability page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_availability_page( $server_param = 'REQUEST_URI' ) {

	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-availability' ) !== false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking > Availability page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_builder_booking_form_page( $server_param = 'REQUEST_URI' ) {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-settings' ) !== false ) && ( strpos( $_SERVER[ $server_param ], 'tab=builder_booking_form' ) !== false ) ) {
		return true;
	}

	return false;
}


/**
 * Check if this Booking > Setup page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_setup_wizard_page( $server_param = 'REQUEST_URI' ) {                                            // FixIn: 9.8.0.1.

	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-setup' ) !== false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking > Resources page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_resources_page( $server_param = 'REQUEST_URI' ) {

	// Old.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'wpdev-booking.phpwpdev-booking-resources' ) !== false ) ) {
		return true;
	}
	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-resources' ) !== false ) ) {
		return true;
	}

	return false;
}

/**
 * Check if this Booking > Settings page
 *
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 *
 * @return boolean true | false
 */
function wpbc_is_settings_page( $server_param = 'REQUEST_URI' ) {

	// Old.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'wpdev-booking.phpwpdev-booking-option' ) !== false ) ) {
		return true;
	}
	// New.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( is_admin() ) && ( strpos( $_SERVER[ $server_param ], 'page=wpbc-settings' ) !== false ) ) {
		return true;
	}

	return false;
}


// -----------------------------------------------------------------------------------------------------------------
// ==  support  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Transform the REQUEST parameters (GET and POST) into URL
 *
 * @param $page_param
 * @param $exclude_params
 * @param $only_these_parameters
 * @param $is_escape_url
 * @param $only_get
 *
 * @return string|null
 */
function wpbc_get_params_in_url( $page_param , $exclude_params = array(), $only_these_parameters = false, $is_escape_url = true, $only_get = false ){			//FixIn: 8.0.1.101     //Fix: $is_escape_url = false

	$exclude_params[] = 'page';
	$exclude_params[] = 'post_type';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_GET['page'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$page_param = $_GET['page'];
	}
	$get_paramaters = array( 'page' => $page_param );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$check_params = ( $only_get ) ? $_GET : $_REQUEST;


	foreach ( $check_params as $prm_key => $prm_value ) {

		// Skip  parameters arrays,  like $_GET['rvaluation_to'] = Array ( [0] => 6,  [1] => 14,  [2] => 14 )
		if (
			   (  is_string( $prm_value ) )
			|| ( is_numeric( $prm_value ) )
		){

			// Check  about Too long parameters,  if it exists  then  reset it.
			if ( strlen( $prm_value ) > 1000 ) {
				$prm_value = '';
			}

			if ( ! in_array( $prm_key, $exclude_params ) ) {
				if ( ( $only_these_parameters === false ) || ( in_array( $prm_key, $only_these_parameters ) ) ) {
					$get_paramaters[ $prm_key ] = $prm_value;
				}
			}

		}
	}

	$url = admin_url( add_query_arg(  $get_paramaters , 'admin.php' ) );

	if ( $is_escape_url ) {
		$url = esc_url_raw( $url );                                                                                     // FixIn: 8.1.1.7.
	}

	return $url;
}


function wpbc_left_vertival_nav__get_tab_url( $page_tag, $tab_name, $subtab_name = false, $tags = array( 'tab' => 'tab', 'subtab' => 'subtab' ) ) {

	if ( false === $subtab_name ) {
		return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $tags['tab'] => $tab_name, ), 'admin.php' ) ) );
	} else {
		return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $tags['tab'] => $tab_name, $tags['subtab'] => $subtab_name, ), 'admin.php' ) ) );
	}
}

/**
 * Get  back  URL  for going from  edit season filter or rate to  specfic main  page.
 *
 * @return string|null
 */
function wpbc_get_back_button_url__for_seasons() {

	$page_params_arr = array(
		'page'     => 'wpbc-availability',
		'page_num' => 1,
	);

	if ( ! empty( $_REQUEST['page'] ) ) {                                                       // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$page_params_arr['page'] = sanitize_text_field( wp_unslash( $_REQUEST['page'] ) );      // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	}
	if ( ! empty( $_REQUEST['tab'] ) ) {                                                       // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$page_params_arr['tab'] = sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) );       // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	}
	if ( ! empty( $_REQUEST['page_num'] ) ) {                                                  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$page_params_arr['page_num'] = intval( $_REQUEST['page_num'] );                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	}

	$back_url = admin_url(
		add_query_arg(
			$page_params_arr,
			'admin.php'
		)
	);
	return $back_url;
}
