<?php /**
 * @version 1.0
 * @package Booking Calendar  -   JavaScript
 * @category Define JavaScript variables
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 19.10.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly.

/**
 * Set local today  date ( can depend  from  the parameters in shortcode:  [booking resource_id=1 calendar_dates_start='2025-01-01' calendar_dates_end='2025-12-31'] )
 *
 * @param $start_it - default 'now',  but you can pass date: '2024-07-26'  (usually  from  the past to  set  ability to  select  the time slots in the past).
 *
 * @return string
 */
function wpbc_get_localized_js__time_local( $start_it = 'now' ) {

	$script = '';
	// FixIn: 10.8.1.4.
	$gmt_time = gmdate( 'Y-m-d H:i:s', strtotime( $start_it ) );

	if ( ( isset( $_REQUEST['allow_past'] ) ) || ( wpbc_is_new_booking_page() && ( isset( $_GET['booking_hash'] ) ) ) ) {           // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing                                                                                 // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		// Get time in past. //FixIn: 10.10.3.2.
		$gmt_time = gmdate( 'Y-m-d H:i:s', strtotime( '-' . intval( wpbc_get_max_visible_days_in_calendar() ) . ' days' ) );        // - 365 days or more
	}

	$gmt_csv   = wpbc_datetime_localized__no_wp_timezone( $gmt_time, 'Y,m,d,H,i' );
	$local_csv = wpbc_datetime_localized__use_wp_timezone( $gmt_time, 'Y,m,d,H,i' );

	$gmt_arr   = wpbc_csv_numbers_to_int_array( $gmt_csv );
	$local_arr = wpbc_csv_numbers_to_int_array( $local_csv );

	$script    .= "_wpbc.set_other_param('time_gmt_arr', " . wp_json_encode( $gmt_arr ) . " ); ";
	$script    .= "_wpbc.set_other_param('time_local_arr', " . wp_json_encode( $local_arr ) . " ); ";

//	$script .= "_wpbc.set_other_param( 'time_gmt_arr', [" . wpbc_set_csd_as_int( wpbc_datetime_localized__no_wp_timezone( $gmt_time, 'Y,m,d,H,i' ) ) . ']  ); ';
//	$script .= "_wpbc.set_other_param( 'time_local_arr', [" . wpbc_set_csd_as_int( wpbc_datetime_localized__use_wp_timezone( $gmt_time, 'Y,m,d,H,i' ) ) . ']  ); ';

	$unavailable_time_from_today = get_bk_option( 'booking_unavailable_days_num_from_today' );

	if ( ! empty( $unavailable_time_from_today ) ) {
		if ( 'm' === substr( $unavailable_time_from_today, - 1 ) ) {

			// Get time ONLY, if it is not in past. //FixIn: 10.10.3.2.
			if ( ( ! isset( $_REQUEST['allow_past'] ) ) && ( ! ( wpbc_is_new_booking_page() && ( isset( $_GET['booking_hash'] ) ) ) ) ) {   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				$gmt_time = gmdate( 'Y-m-d H:i:s', strtotime( '+' . ( intval( $unavailable_time_from_today ) - 1 ) . ' minutes' ) );
			}

			// Local timezone unavailable time.                                                                         // FixIn: 10.9.6.3.
			$local_unavailable_datetime_ymdhis = wpbc_datetime_localized__use_wp_timezone( $gmt_time, 'Y-m-d H:i:s' );             // Local unavailable.
			// Local timezone Now time.
			$local_now_datetime_ymdhis = wpbc_datetime_localized__use_wp_timezone( strtotime( $start_it ), 'Y-m-d H:i:s' );            // Local Now.
			// Local timezone Today midnight time.
			$local_now_datetime_ymd000 = wpbc_datetime_localized__use_wp_timezone( strtotime( $start_it ), 'Y-m-d 00:00:00' );         // Local Midnight.

			// Check time from local MIDNIGHT to Unavailable.
			$days_number_shift           = intval( ( strtotime( $local_unavailable_datetime_ymdhis ) - strtotime( $local_now_datetime_ymd000 ) ) / 86400 );
			$unavailable_time_from_today = $days_number_shift;
			// $unavailable_time_from_today = '0'; // FixIn: 10.9.2.6.
		}
	} else {
		$unavailable_time_from_today = '0';
	}

	$today_local_csv = wpbc_datetime_localized__use_wp_timezone( $gmt_time, 'Y,m,d,H,i' );
	$today_arr       = wpbc_csv_numbers_to_int_array( $today_local_csv );

	$script .= "_wpbc.set_other_param( 'today_arr', " . wp_json_encode( $today_arr ) . " ); ";

	// FixIn: 10.8.1.4.
	$script .= "_wpbc.set_other_param( 'availability__unavailable_from_today', '" . esc_js( $unavailable_time_from_today ) . "' ); ";    //Default: 0 '           Old JS: block_some_dates_from_today'		_wpbc.get_other_param( 'availability__unavailable_from_today' )

	return $script;
}


/**
 * Get script string for localised variables.
 *
 * @return string
 */
function wpbc_get_localized_js_vars() {

	// $script  = 'var1 = '. wp_json_encode('var1') .'; ';      // JSON.parse(wpbc_global_var);  //.

	$script  = '';
	$script .= "_wpbc.set_other_param( 'locale_active', '" . esc_js( wpbc_get_maybe_reloaded_booking_locale() ) . "' ); ";

	$script .= wpbc_get_localized_js__time_local();

	$script .= "_wpbc.set_other_param( 'url_plugin', '" . esc_url( plugins_url( '', WPBC_FILE ) ) . "' ); ";

	$get_booking_hash = ( ( isset( $_GET['booking_hash'] ) ) ? sanitize_text_field( wp_unslash( $_GET['booking_hash'] ) ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */

	$script .= "_wpbc.set_other_param( 'this_page_booking_hash', '" . esc_js( $get_booking_hash ) . "'  ); ";


	$script .= "_wpbc.set_other_param( 'calendars__on_this_page', [] ); ";

	$script .= "_wpbc.set_other_param( 'calendars__first_day', '"   . intval( get_bk_option( 'booking_start_day_weeek' ) )   . "' ); ";                             //."\n";
	$script .= "_wpbc.set_other_param( 'calendars__max_monthes_in_calendar', '"   . esc_js( get_bk_option( 'booking_max_monthes_in_calendar' ) )   . "' ); ";
	if ( class_exists( 'wpdev_bk_biz_m' ) ) {
		$script .= "_wpbc.set_other_param( 'availability__available_from_today', '" . esc_js( get_bk_option( 'booking_available_days_num_from_today' ) ) . "' ); ";    //Default '' - all days. Old JS: 'wpbc_available_days_num_from_today'
	}
    $script .= "_wpbc.set_other_param( 'availability__week_days_unavailable', ["
                                                                    . ( ( get_bk_option( 'booking_unavailable_day0') == 'On' ) ? '0,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day1') == 'On' ) ? '1,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day2') == 'On' ) ? '2,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day3') == 'On' ) ? '3,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day4') == 'On' ) ? '4,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day5') == 'On' ) ? '5,' : '' )
				                                                    . ( ( get_bk_option( 'booking_unavailable_day6') == 'On' ) ? '6,' : '' )
				                                                    . "999] ); ";                                            // 999 - blank day, which will not impact  to the checking of the week days. Required for correct creation of this array.
	// General  days selection.
	$days_selection_arr = wpbc__calendar__js_params__get_days_selection_arr();

	$script .= "_wpbc.set_other_param( 'calendars__days_select_mode', '" . $days_selection_arr['days_select_mode'] . "' ); ";
	$script .= "_wpbc.set_other_param( 'calendars__fixed__days_num', " . $days_selection_arr['fixed__days_num'] . " ); ";
	$script .= "_wpbc.set_other_param( 'calendars__fixed__week_days__start',   [" . $days_selection_arr['fixed__week_days__start'] . "] ); ";
	$script .= "_wpbc.set_other_param( 'calendars__dynamic__days_min', " . $days_selection_arr['dynamic__days_min'] . " ); ";
	$script .= "_wpbc.set_other_param( 'calendars__dynamic__days_max', " . $days_selection_arr['dynamic__days_max'] . " ); ";
	$script .= "_wpbc.set_other_param( 'calendars__dynamic__days_specific',    [" . $days_selection_arr['dynamic__days_specific'] . "] ); ";
	$script .= "_wpbc.set_other_param( 'calendars__dynamic__week_days__start', [" . $days_selection_arr['dynamic__week_days__start'] . "] ); ";

	// FixIn: 10.3.0.9.
	if ( false !== strpos( get_bk_option( 'booking_skin' ), 'light__24_8' ) ) {
		$script .= "_wpbc.set_other_param( 'calendars__days_selection__middle_days_opacity', '0.5' ); ";
	} else {
		$script .= "_wpbc.set_other_param( 'calendars__days_selection__middle_days_opacity', '0.75' ); ";
	}


	// Defined in  BS.
	$script .= "_wpbc.set_other_param( 'is_enabled_booking_recurrent_time',  " . ( ( get_bk_option( 'booking_recurrent_time' ) !== 'On' ) ? 'false' : 'true' ) . " ); ";
	$script .= "_wpbc.set_other_param( 'is_allow_several_months_on_mobile',  " . ( ( get_bk_option( 'booking_calendar_allow_several_months_on_mobile' ) !== 'On' ) ? 'false' : 'true' ) . " ); ";
	$script .= "_wpbc.set_other_param( 'is_enabled_change_over',  "            . (
																						(
																							( function_exists( 'wpbc_is_booking_used_check_in_out_time' ) )
																					     && ( wpbc_is_booking_used_check_in_out_time() )
																						) ? 'true' : 'false'
																				  ) . " ); ";

	$script .= "_wpbc.set_other_param( 'is_enabled_booking_timeslot_picker',  " . ( ( 'On' === get_bk_option( 'booking_timeslot_picker' ) ) ? 'true' : 'false' ) . " ); ";

	if ( class_exists( 'wpdev_bk_biz_l' ) ) {
		$script .= "_wpbc.set_other_param( 'is_enabled_booking_search_results_days_select', '" . esc_js( get_bk_option( 'booking_search_results_days_select' ) ) . "' ); ";
	}

	$script .= "_wpbc.set_other_param( 'update', '" . esc_attr( WP_BK_VERSION_NUM ) . "' ); ";
	$script .= "_wpbc.set_other_param( 'version', '" . wpbc_get_version_type__and_mu() . "' ); ";

	// Warning Messages.
	$script .= "_wpbc.set_message( 'message_dates_times_unavailable', "        . wp_json_encode( __( 'These dates and times in this calendar are already booked or unavailable.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_choose_alternative_dates', "       . wp_json_encode( __( 'Please choose alternative date(s), times, or adjust the number of slots booked.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_cannot_save_in_one_resource', "    . wp_json_encode( __( 'It is not possible to store this sequence of the dates into the one same resource.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_required', "                 . wp_json_encode( __( 'This field is required', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_required_for_check_box', "   . wp_json_encode( __( 'This checkbox must be checked', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_required_for_radio_box', "   . wp_json_encode( __( 'At least one option must be selected', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_email', "                    . wp_json_encode( __( 'Incorrect email address', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_same_email', "               . wp_json_encode( __( 'Your emails do not match', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_check_no_selected_dates', "        . wp_json_encode( __( 'Please, select booking date(s) at Calendar.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_processing', "                     . wp_json_encode( __( 'Processing', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_deleting', "                       . wp_json_encode( __( 'Deleting', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_updating', "                       . wp_json_encode( __( 'Updating', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_saving', "                         . wp_json_encode( __( 'Saving', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_error_check_in_out_time', "        . wp_json_encode( __( 'Error! Please reset your check-in/check-out dates above.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_error_start_time', "               . wp_json_encode( __( 'Start Time is invalid. The date or time may be booked, or already in the past! Please choose another date or time.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_error_end_time', "                 . wp_json_encode( __( 'End Time is invalid. The date or time may be booked, or already in the past. The End Time may also be earlier that the start time, if only 1 day was selected! Please choose another date or time.', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_error_range_time', "               . wp_json_encode( __( 'The time(s) may be booked, or already in the past!', 'booking' ) ) . " ); ";
	$script .= "_wpbc.set_message( 'message_error_duration_time', "            . wp_json_encode( __( 'The time(s) may be booked, or already in the past!', 'booking' ) ) . " ); ";

	$script .= "console.log( '== WPBC VARS " . esc_attr( WP_BK_VERSION_NUM ) . ' [' . wpbc_get_version_type__and_mu() . "] LOADED ==' );";
	return $script;
}


// FixIn: 10.14.4.2. (loader made cache-proof)
/**
 * Print inline JS for Booking Calendar that initializes safely even with cache plugins (delay/defer/on-interaction).
 *
 * @param string $where_to_load 'both' by default.
 *
 * @return void
 */
function wpbc_localize_js_vars( $where_to_load = 'both' ) {

	// Allow disabling, if needed.
	if ( apply_filters( 'wpbc_disable_inline_bootstrap', false, $where_to_load ) ) {
		return;
	}

	// 1) BEFORE: essentials used by inlines or deferred scripts.  - Define both a global var and window property for maximum compatibility.
	$script_before = 'var wpbc_url_ajax = ' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ';' . 'window.wpbc_url_ajax = wpbc_url_ajax;';
	wp_add_inline_script( 'wpbc_all', $script_before, 'before' );

	// 2) Existing payload (calls to _wpbc.set_other_param, _wpbc.set_message, …).
	$payload_js = wpbc_get_localized_js_vars();

	// 3) Robust bootstrap: runs payload only when _wpbc is ready.
	$max_wait_ms      = (int) apply_filters( 'wpbc_js_bootstrap_max_wait_ms', 10000 );        // 10s cap
	$poll_interval_ms = (int) apply_filters( 'wpbc_js_bootstrap_poll_interval_ms', 50 );      // 50ms
	$custom_events    = (array) apply_filters( 'wpbc_js_bootstrap_ready_events', array( 'wpbc-ready', 'wpbc:ready', 'wpbc_ready', 'wpbcReady' ) );
	$user_events      = (array) apply_filters( 'wpbc_js_bootstrap_user_events', array( 'click', 'mousemove', 'touchstart', 'keydown', 'scroll' ) );

	$boot_js = '(function(){' . "\n" .
				'"use strict";' . "\n" .
				'function wpbc_init__head(){' . $payload_js . '}' . "\n" .
				'(function(){' . "\n" .
				'  if (window.__wpbc_boot_done__ === true) return;' . "\n" .
				// global guard across duplicates.
				'  var started = false;' . "\n" .
				'  function run_once(){' . "\n" .
				'    if (started || window.__wpbc_boot_done__ === true) return true;' . "\n" .
				'    started = true;' . "\n" .
				'    try { wpbc_init__head(); window.__wpbc_boot_done__ = true; }' . "\n" .
				'    catch(e){ started = false; try{console.error("WPBC init failed:", e);}catch(_){} }' . "\n" .
				'    return (window.__wpbc_boot_done__ === true);' . "\n" .
				'  }' . "\n" .
				// Fast path.
				'  function is_ready(){ return !!(window._wpbc && typeof window._wpbc.set_other_param === "function"); }' . "\n" .
				'  if ( is_ready() && run_once() ) return;' . "\n" .
				// Polling fallback (covers delayed/deferred/on-interaction loaders).
				'  var waited = 0, max_ms = ' . $max_wait_ms . ', step = ' . $poll_interval_ms . ';' . "\n" .
				'  var timer = setInterval(function(){' . "\n" .
				'    if ( is_ready() && run_once() ) { clearInterval(timer); return; }' . "\n" .
				'    waited += step;' . "\n" .
				'    if ( waited >= max_ms ) {' . "\n" .
				'      clearInterval(timer);' . "\n" .
				'      // Switch to slow polling (1s) so we still init even without user interaction later.' . "\n" .
				'      var slow = setInterval(function(){ if ( is_ready() && run_once() ) clearInterval(slow); }, 1000);' . "\n" .
				'      try{console.warn("WPBC: _wpbc not detected within " + max_ms + "ms; using slow polling.");}catch(_){}' . "\n" .
				'    }' . "\n" .
				'  }, step);' . "\n" .
				// Listen for your custom ready events (emit from core once _wpbc is ready).
				'  var evs = ' . wp_json_encode( array_values( $custom_events ) ) . ';' . "\n" .
				'  evs.forEach(function(name){' . "\n" .
				'    document.addEventListener(name, function onready(){ if (is_ready() && run_once()) document.removeEventListener(name, onready); });' . "\n" .
				'  });' . "\n" .
				// Standard lifecycle events (covers DOMContentLoaded + load).
				'  if (document.readyState === "loading") {' . "\n" .
				'    document.addEventListener("DOMContentLoaded", function(){ if (is_ready()) run_once(); }, { once:true });' . "\n" .
				'  }' . "\n" .
				'  window.addEventListener("load", function(){ if (is_ready()) run_once(); }, { once:true });' . "\n" .
				'  window.addEventListener("pageshow", function(){ if (is_ready()) run_once(); }, { once:true });' . "\n" .
				'  document.addEventListener("visibilitychange", function(){ if (!document.hidden && is_ready()) run_once(); });' . "\n" .
				// User interaction hooks — some cache plugins load delayed JS only after interaction.
				'  var ui = ' . wp_json_encode( array_values( $user_events ) ) . ';' . "\n" .
				'  var ui_bailed = false;' . "\n" .
				'  function on_ui(){ if (ui_bailed) return; if (is_ready() && run_once()){ ui_bailed = true; ui.forEach(function(t){ document.removeEventListener(t, on_ui, true); }); } }' . "\n" .
				'  ui.forEach(function(t){ document.addEventListener(t, on_ui, true); });' . "\n" .
				'})();' . "\n" .
				'})();';
	wp_add_inline_script( 'wpbc_all', $boot_js );
}
add_action( 'wpbc_enqueue_js_files', 'wpbc_localize_js_vars', 51 );
