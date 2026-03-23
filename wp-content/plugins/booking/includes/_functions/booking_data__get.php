<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Booking details | replace / fields functions
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
// ==  Booking details | replace / fields functions  ==
// =====================================================================================================================

/**
 * Get booking resource title (translated/localized)
 *
 * @param $resource_id
 *
 * @return string
 */
function wpbc_get_resource_title( $resource_id = 1 ) {

	$resource_title = '';

	if ( function_exists( 'wpbc_db__get_resource_title' ) ) {

		$resource_title = wpbc_db__get_resource_title( $resource_id );

		if (! empty($resource_title)) {
			$resource_title = wpbc_lang( $resource_title );
		}
	}

	return $resource_title;
}


/**
 * Get parent booking resource title (translated/localized)
 *
 * @param $resource_id
 *
 * @return string
 */
function wpbc_get_parent_resource_title( $resource_id = 1 ) {                                                           // FixIn: 10.9.2.4.

	$resource_title = '';

	if ( ( function_exists( 'wpbc_is_this_child_resource' ) ) && ( wpbc_is_this_child_resource( $resource_id ) ) ) {
		$resource_id = wpbc_get_parent_resource( $resource_id );
	}

	if ( function_exists( 'wpbc_db__get_resource_title' ) ) {

		$resource_title = wpbc_db__get_resource_title( $resource_id );

		if (! empty($resource_title)) {
			$resource_title = wpbc_lang( $resource_title );
		}
	}

	return $resource_title;
}

/**
 * Check, if exist booking for this hash. If existed, get Email of this booking
 *
 * @param string $booking_hash		- booking hash.
 * @param string $booking_data_key	- booking field key - default 'email'.
 *
 * @return bool 					- booking data field
 */
function wpbc_get__booking_data_field__by_booking_hash( $booking_hash, $booking_data_key = 'email' ){								// FixIn: 8.1.3.5.

	$return_val = false;

	// $booking_hash = '0d55671fd055fd64423294f89d6b58e6';        	// debugging

	if ( ! empty( $booking_hash ) ) {

		$my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $booking_hash );

		if ( ! empty( $my_booking_id_type ) ) {

			list( $booking_id, $resource_id ) = $my_booking_id_type;

			$booking_data = wpbc_db_get_booking_details( $booking_id );

			if ( ! empty( $booking_data ) ) {

				$booking_details = wpbc_get_booking_different_params_arr( $booking_id, $booking_data->form, $resource_id );

				if ( isset( $booking_details[ $booking_data_key ] ) ) {

					$return_val = $booking_details[ $booking_data_key ];
				}
			}
		}
	}
	return $return_val;
}

/**
 * Get booking details object from DB
 *
 * @param $booking_id - int
 *
 * @return mixed - booking details or false if not found
 * Example:
 * stdClass Object
 * (
 * [booking_id] => 26
 * [trash] => 0
 * [sync_gid] =>
 * [is_new] => 0
 * [status] =>
 * [sort_date] => 2018-02-27 00:00:00
 * [modification_date] => 2018-02-18 12:49:30
 * [form] => text^selected_short_dates_hint3^02/27/2018 - 03/02/2018~text^days_number_hint3^4~text^cost_hint3^40.250&nbsp;&#36;~text^name3^Victoria~text^secondname3^vica~email^email3^vica@wpbookingcalendar.com~text^phone3^test booking ~selectbox-one^visitors3^1
 * [hash] => 0d55671fd055fd64423294f89d6b58e6
 * [booking_type] => 3
 * [remark] =>
 * [cost] => 40.25
 * [pay_status] => 151895097121.16
 * [pay_request] => 0
 * )
 */
function wpbc_db_get_booking_details( $booking_id ) {                                                                    // FixIn: 8.1.3.5.

	global $wpdb;

	$slct_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking WHERE booking_id = %d LIMIT 0,1", $booking_id );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$sql_results = $wpdb->get_row( $slct_sql );

	if ( ! empty( $sql_results ) ) {
		return $sql_results;
	} else {
		return false;
	}
}

/**
 * Get booking   D a t e s  from DB - array of objects
 *
 * @param $booking_id - int
 *
 * @return mixed - booking dates array or false if not found
 *
 */
function wpbc_db_get_booking_dates( $booking_id ) {

	global $wpdb;

	$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}bookingdates as dt WHERE dt.booking_id = %d ", $booking_id );

	if ( class_exists( 'wpdev_bk_biz_l' ) ) {
		$sql .= " ORDER BY booking_id, type_id, booking_date ";
	} else {
		$sql .= " ORDER BY booking_id, booking_date ";
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$sql_results = $wpdb->get_results( $sql );

	if ( ! empty( $sql_results ) ) {
		return $sql_results;
	} else {
		return false;
	}
}

/**
 * Get booking modification  date from  DB
 *
 * @param $booking_id
 *
 * @return string
 */
function wpbc_db_get_booking_modification_date( $booking_id ) {
	// FixIn: 8.0.1.7.
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$modification_date = ' ' . $wpdb->get_var( $wpdb->prepare( "SELECT modification_date FROM {$wpdb->prefix}booking  WHERE booking_id = %d ", $booking_id ) );

	return $modification_date;
}

/**
 * Get user IP
 *
 * @return mixed|string
 */
function wpbc_get_user_ip() {

	if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$userIP = ( ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) ? sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$userIP = ( ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ? sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
		$userIP = ( ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) ? sanitize_text_field( $_SERVER['HTTP_X_FORWARDED'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
		$userIP = ( ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) ? sanitize_text_field( $_SERVER['HTTP_FORWARDED_FOR'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
		$userIP = ( ( isset( $_SERVER['HTTP_FORWARDED'] ) ) ? sanitize_text_field( $_SERVER['HTTP_FORWARDED'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$userIP = ( ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} else {
		$userIP = "";
	}
	return $userIP;
}

/**
 * Get IP of Server
 *
 * @return string
 */
function wpbc_get_server_ip() {																					// FixIn: 9.8.14.3.

	$ip_address = '';
	if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
		$ip_address = ( ( isset( $_SERVER['SERVER_ADDR'] ) ) ? sanitize_text_field( $_SERVER['SERVER_ADDR'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	} else if ( array_key_exists( 'LOCAL_ADDR', $_SERVER ) ) {
		$ip_address = ( ( isset( $_SERVER['LOCAL_ADDR'] ) ) ? sanitize_text_field( $_SERVER['LOCAL_ADDR'] ) : '' );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	}

	return $ip_address;
}

/**
 * Get different Booking Fields as array for   replace
 *
 * @param int    $booking_id 		- ID of booking                                       // 999
 * @param string $formdata   		- booking form data content                  // selectbox-one^rangetime4^10:00 - 12:00~text^name4^Jo~text^secondname4^Smith~email^email4^smith@wpbookingcalendar.com~...
 * @param int $booking_resource_id 	- booking resource type                      // 4
 *
 * @return array
 *
 *   Example:
 *              [
 * 					[booking_id] => 26
 * 					[id] => 26
 * 					[days_input_format] => 01.03.2018,02.03.2018,27.02.2018,28.02.2018
 * 					[days_only_sql] => 2018-02-27,2018-02-28,2018-03-01,2018-03-02
 * 					[dates_sql] => 2018-02-27 00:00:00, 2018-02-28 00:00:00, 2018-03-01 00:00:00, 2018-03-02 00:00:00
 * 					[check_in_date_sql] => 2018-02-27 00:00:00
 * 					[check_out_date_sql] =>  2018-03-02 00:00:00
 * 					[dates] => 02/27/2018 - 03/02/2018
 * 					[check_in_date] => 02/27/2018
 * 					[check_out_date] => 03/02/2018
 * 					[check_out_plus1day] => 03/03/2018
 * 					[dates_count] => 4
 * 					[days_count] => 4
 * 					[nights_count] => 3
 * 					[check_in_date_hint] => 02/27/2018
 * 					[check_out_date_hint] => 03/02/2018
 * 					[start_time_hint] => 00:00
 * 					[end_time_hint] => 00:00
 * 					[selected_dates_hint] => 02/27/2018, 02/28/2018, 03/01/2018, 03/02/2018
 * 					[selected_timedates_hint] => 02/27/2018, 02/28/2018, 03/01/2018, 03/02/2018
 * 					[selected_short_dates_hint] => 02/27/2018 - 03/02/2018
 * 					[selected_short_timedates_hint] => 02/27/2018 - 03/02/2018
 * 					[days_number_hint] => 4
 * 					[nights_number_hint] => 3
 * 					[siteurl] => http://beta
 * 					[resource_title] => Apartment A
 * 					[bookingtype] => Apartment A
 * 					[remote_ip] => 127.0.0.1
 * 					[user_agent] => Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0
 * 					[request_url] => http://beta/wp-admin/post.php?post=1473&action=edit
 * 					[current_date] => 02/18/2018
 * 					[current_time] => 14:11
 * 					[cost_hint] => 40.250 $
 * 					[name] => Victoria
 * 					[secondname] => vica
 * 					[email] => vica@wpbookingcalendar.com
 * 					[phone] => test booking
 * 					[visitors] => 1
 * 					[booking_resource_id] => 3
 * 					[resource_id] => 3
 * 					[type_id] => 3
 * 					[type] => 3
 * 					[resource] => 3
 * 					[content] => '........'
 * 					[moderatelink] => http://beta/wp-admin/admin.php?page=wpbc&tab=vm_booking_listing&wh_booking_id=26
 * 					[visitorbookingediturl] => http://beta/?booking_hash=0d55671fd055fd64423294f89d6b58e6
 * 					[visitorbookingcancelurl] => http://beta/?booking_hash=0d55671fd055fd64423294f89d6b58e6&booking_cancel=1
 * 					[visitorbookingpayurl] => http://beta/?booking_hash=0d55671fd055fd64423294f89d6b58e6&booking_pay=1
 * 					[bookinghash] => 0d55671fd055fd64423294f89d6b58e6
 * 					[db_cost] => 40.25
 * 					[db_cost_hint] => 40.250 $
 * 					[modification_date] =>  2018-02-18 12:49:30
 * 					[modification_year] => 2018
 * 					[modification_month] => 02
 * 					[modification_day] => 18
 * 					[modification_hour] => 12
 * 					[modification_minutes] => 49
 * 					[modification_seconds] => 30
 *        		]
 */
function wpbc_get_booking_different_params_arr( $booking_id, $formdata, $booking_resource_id = 1 ) {

	$replace = array();

	// Resources ///////////////////////////////////////////////////////////////
	$bk_title = wpbc_get_resource_title( $booking_resource_id );


	////////////////////////////////////////////////////////////////////////////
	// Dates Dif. Formats
	////////////////////////////////////////////////////////////////////////////

	// -> '2023-10-09 12:00:01, 2023-10-09 20:00:02'
	$sql_dates_format = wpbc_db__get_sql_dates__in_booking__as_str( $booking_id );       // 2016-08-03 16:00:01, 2016-08-03 18:00:02

	$sql_dates_only = explode(',',$sql_dates_format);
	$sql_days_only_array = array();
	$days_as_in_form_array = array();
	foreach ( $sql_dates_only as $sql_day_only ) {
		$sql_days_only_array[] = trim( substr($sql_day_only, 0, 11 ) );
		$days_as_in_form_array[] = date_i18n( "d.m.Y", strtotime( trim( substr($sql_day_only, 0, 11 ) ) ) );
	}
	$sql_days_only_array = array_unique( $sql_days_only_array );
	sort( $sql_days_only_array );
	$sql_days_only = implode( ',', $sql_days_only_array );

	$days_as_in_form_array = array_unique( $days_as_in_form_array );
	sort( $days_as_in_form_array );
	$days_as_in_form = implode( ',', $days_as_in_form_array );

	$sql_days_only_with_full_times = array();
	foreach ( $sql_days_only_array as $sql_day ) {
		$sql_days_only_with_full_times[] = $sql_day . ' 00:00:00';
	}
	$sql_days_only_with_full_times = implode(',', $sql_days_only_with_full_times );


	if ( get_bk_option( 'booking_date_view_type' ) == 'short' ) {
		$formated_booking_dates = wpbc_get_dates_short_format( $sql_dates_format );
	} else {
		$formated_booking_dates = wpbc_get_dates_comma_string_localized( $sql_dates_format );
	}

	$sql_dates_format_check_in_out = explode(',', $sql_dates_format );

	$my_check_in_date  = wpbc_get_dates_comma_string_localized( $sql_dates_format_check_in_out[0] );
	$my_check_out_date = wpbc_get_dates_comma_string_localized( $sql_dates_format_check_in_out[ count( $sql_dates_format_check_in_out ) - 1 ] );

	$my_check_out_plus1day = wpbc_datetime_localized( gmdate(  'Y-m-d H:i:s'
														, strtotime(  $sql_dates_format_check_in_out[ count( $sql_dates_format_check_in_out ) - 1 ]
																	. " +1 day" )
											) );

	$date_format = get_bk_option( 'booking_date_format');
	$check_in_date_hint  = wpbc_date_localized( $sql_days_only_array[0] );
	$check_out_date_hint = wpbc_date_localized( $sql_days_only_array[ ( count( $sql_days_only_array ) - 1  ) ] );

	// FixIn: 9.7.3.16.
	$cancel_date_hint = wpbc_datetime_localized( gmdate(  'Y-m-d H:i:s'
												  , strtotime( '-14 days', strtotime( $sql_days_only_array[0] ) )
											) );
	// FixIn: 10.0.0.31.
	$pre_checkin_date_hint = wpbc_datetime_localized( gmdate( 'Y-m-d H:i:s'
												, strtotime( '-' . intval( get_bk_option( 'booking_number_for_pre_checkin_date_hint' ) ) . ' days', strtotime( $sql_days_only_array[0] ) )
											) );

	// Booking Times -------------------------------------------------------------------------------------------
	$start_end_time = wpbc_get_times_in_form( $formdata, $booking_resource_id ); // false ||

	if ( $start_end_time !== false ) {
		$start_time = $start_end_time[0];                                       // array('00','00','01');
		$end_time   = $start_end_time[1];                                       // array('00','00','01');
	} else {
		$start_time = array('00','00','00');
		$end_time   = array('00','00','00');
	}

//TODO: continue here with  replacing date_i18n to wpbc_loc_ ...

	$start_time_hint = wpbc_time_localized( implode( ':', $start_time ) );
	$end_time_hint   = wpbc_time_localized( implode( ':', $end_time   ) );

	// FixIn: 9.5.1.3.
	$check_in_date_sql  = wpbc_date_localized( $sql_days_only_array[0], 'Y-m-d' );
	$check_out_date_sql = wpbc_date_localized( $sql_days_only_array[ ( count( $sql_days_only_array ) - 1  ) ], 'Y-m-d' );

	$start_time_sql = wpbc_time_localized( implode( ':', $start_time ), 'H:i' );
	$end_time_sql   = wpbc_time_localized( implode( ':', $end_time )  , 'H:i' );

	////////////////////////////////////////////////////////////////////////////


	// Other ///////////////////////////////////////////////////////////////////
	$replace[ 'booking_id' ]    = $booking_id;
	$replace[ 'id' ]            = $replace[ 'booking_id' ];

	$replace[ 'days_input_format' ] = $days_as_in_form;                         // 15.11.2023,16.11.2023,17.11.2023
	$replace[ 'days_only_sql' ]     = $sql_days_only;                           // 2023-11-15,2023-11-16,2023-11-17
	$replace[ 'dates_sql' ]         = $sql_dates_format;                        // 2016-07-28 16:00:01, 2016-07-28 18:00:02
	$replace[ 'check_in_date_sql' ] = $sql_dates_format_check_in_out[0];        // 2016-07-28 16:00:01
	$replace[ 'check_out_date_sql' ] = $sql_dates_format_check_in_out[ count( $sql_dates_format_check_in_out ) - 1 ];       // 2016-07-28 18:00:02
	$replace[ 'dates' ]             = $formated_booking_dates;                  // July 28, 2016 16:00 - July 28, 2016 18:00
	$replace[ 'check_in_date' ]     = $my_check_in_date;                        // July 28, 2016 16:00
	$replace[ 'check_out_date' ]    = $my_check_out_date;                       // July 28, 2016 18:00
	$replace[ 'check_out_plus1day'] = $my_check_out_plus1day;                   // July 29, 2016 18:00
	$replace[ 'dates_count' ]       = count( $sql_days_only_array );            // 1
	$replace[ 'days_count' ]        = count( $sql_days_only_array );            // 1
	$replace[ 'nights_count' ]      = ( $replace[ 'days_count' ] > 1 ) ? ( $replace[ 'days_count' ] - 1 ) : $replace[ 'days_count' ];       // 1

	// FixIn: 9.7.3.16.
	$replace[ 'cancel_date_hint' ]      = $cancel_date_hint;                      // 11/11/2013
	// FixIn: 10.0.0.31.
	$replace[ 'pre_checkin_date_hint' ] = $pre_checkin_date_hint;                 // 11/11/2013

	$replace[ 'check_in_date_hint' ]  = $check_in_date_hint;                    // 11/25/2013
	$replace[ 'check_out_date_hint' ] = $check_out_date_hint;                   // 11/27/2013
	$replace[ 'start_time_hint' ]   = $start_time_hint;                         // 10:00
	$replace[ 'end_time_hint' ]     = $end_time_hint;                           // 12:00

	// FixIn: 9.5.1.3.
	$replace['check_in_date_hint_sql']  = $check_in_date_sql;                    	// 2023-03-04
	$replace['check_out_date_hint_sql'] = $check_out_date_sql;                   	// 2023-03-12
	$replace['start_time_hint_sql']     = $start_time_sql;                         	// 10:00
	$replace['end_time_hint_sql']       = $end_time_sql;                           	// 12:00

$replace['selected_dates_hint']       = wpbc_get_dates_comma_string_localized( $sql_days_only_with_full_times );             // 11/25/2013, 11/26/2013, 11/27/2013
	$replace['selected_timedates_hint']   = wpbc_get_dates_comma_string_localized( $sql_dates_format );              		// 11/25/2013 10:00, 11/26/2013, 11/27/2013 12:00
$replace['selected_short_dates_hint']     =     wpbc_get_dates_short_format( $sql_days_only_with_full_times );      // 11/25/2013 - 11/27/2013
	$replace['selected_short_timedates_hint'] = wpbc_get_dates_short_format( $sql_dates_format );       		    // 11/25/2013 10:00 - 11/27/2013 12:00
	$replace[ 'days_number_hint' ]   = $replace[ 'days_count' ];                // 3
	$replace[ 'nights_number_hint' ] = $replace[ 'nights_count' ];              // 2
	$replace[ 'siteurl' ]       = htmlspecialchars_decode( '<a href="' . esc_url( home_url() ) . '">' . home_url() . '</a>' );
	$replace[ 'resource_title'] = wpbc_lang( $bk_title );
	$replace[ 'bookingtype' ]   = $replace[ 'resource_title'];
	$replace[ 'remote_ip'     ] = wpbc_get_user_ip();          													// The IP address from which the user is viewing the current page.
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$replace[ 'user_agent'    ] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';  	// Contents of the User-Agent: header from the current request, if there is one.
	$server_http_referer_uri = ( ( isset( $_SERVER['HTTP_REFERER'] ) ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
	$replace[ 'request_url'   ] = $server_http_referer_uri;        	// The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
	$replace[ 'current_date' ]  = date_i18n( get_bk_option( 'booking_date_format' ) );
	$replace[ 'current_time' ]  = date_i18n( get_bk_option( 'booking_time_format' ) );


	// Form Fields /////////////////////////////////////////////////////////////
	$booking_form_show_array = wpbc__legacy__get_form_content_arr( $formdata, $booking_resource_id, '', $replace );    // We use here $replace array,  becaise in "Content of booking filds data" form  can  be shortcodes from above definition

	foreach ( $booking_form_show_array['_all_fields_'] as $shortcode_name => $shortcode_value ) {

		if ( ! isset( $replace[ $shortcode_name ] ) )
			$replace[ $shortcode_name ] = $shortcode_value;
	}
	$replace[ 'content' ]       = $booking_form_show_array['content'];

	// Links ///////////////////////////////////////////////////////////////////
	$replace[ 'moderatelink' ]  = htmlspecialchars_decode(
														//    '<a href="' .
															esc_url( wpbc_get_bookings_url() . '&tab=vm_booking_listing&wh_booking_id=' . $booking_id )
														//    . '">' . esc_html__('here', 'booking') . '</a>'
														);
	$replace[ 'visitorbookingediturl' ]     = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingediturl]', $booking_id );
	$replace[ 'visitorbookingslisting' ]     = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingslisting]', $booking_id );	//FixIn: 8.1.3.5.1
	$replace[ 'visitorbookingcancelurl' ]   = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingcancelurl]', $booking_id );
	$replace[ 'visitorbookingpayurl' ]      = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingpayurl]', $booking_id );
	$replace[ 'bookinghash' ]               = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[bookinghash]', $booking_id );

	// Cost ////////////////////////////////////////////////////////////////////
	$replace[ 'db_cost' ]        = apply_bk_filter( 'get_booking_cost_from_db', '', $booking_id );
	$replace[ 'db_cost_hint' ]   = wpbc_get_cost_with_currency_for_user( $replace[ 'db_cost' ], $booking_resource_id );

	////////////////////////////////////////////////////////////////////////////

	// FixIn: 8.0.1.7.
	$modification_date = wpbc_db_get_booking_modification_date( $booking_id );

	// This date $values in GMT date/Time format. So  we need to switch  to  WordPress locale with TIME  sum of actual  GMT date/time value + shift  of timezone from WordPress.
	$is_add_wp_timezone = true;
	$modification_date = wpbc_datetime_localized( trim( $modification_date ), 'Y-m-d H:i:s', $is_add_wp_timezone );
	$replace['modification_date'] = ' ' . $modification_date;
	$modification_date = explode( ' ', $modification_date );
	list( $replace['modification_year'], $replace['modification_month'], $replace['modification_day'] ) = explode( '-', $modification_date[0] );
	list( $replace['modification_hour'], $replace['modification_minutes'], $replace['modification_seconds'] ) = explode( ':', $modification_date[1] );

	// FixIn: 10.0.0.34.
	if ( ! empty( $replace['creation_date'] ) ) {

		// This date $values in GMT date/Time format. So  we need to switch  to  WordPress locale with TIME  sum of actual  GMT date/time value + shift  of timezone from WordPress.
		$creation_date = wpbc_datetime_localized( trim( $replace['creation_date'] ), 'Y-m-d H:i:s', $is_add_wp_timezone );

		$replace['creation_date'] = ' ' . $creation_date;
		$creation_date = explode( ' ', $creation_date );
		list( $replace['creation_year'], $replace['creation_month'], $replace['creation_day'] ) 	  = explode( '-', $creation_date[0] );
		list( $replace['creation_hour'], $replace['creation_minutes'], $replace['creation_seconds'] ) = explode( ':', $creation_date[1] );
	}

	return $replace;
}

/**
 * Get additional parameters to the replace array  for specific booking
 *
 * @param $replace
 * @param $booking_id
 * @param $bktype
 * @param $formdata
 *
 * @return mixed
 */
function wpbc_replace_params_for_booking_func( $replace, $booking_id, $bktype, $formdata ){
	/*
		$modification_date = wpbc_db_get_booking_modification_date( $booking_id );

		// This date $values in GMT date/Time format. So  we need to switch  to  WordPress locale with TIME  sum of actual  GMT date/time value + shift  of timezone from WordPress.
		$is_add_wp_timezone = true;
		$modification_date = wpbc_datetime_localized( trim( $modification_date ), 'Y-m-d H:i:s', $is_add_wp_timezone );

		$replace['modification_date'] = ' ' . $modification_date;

		$modification_date = explode( ' ', $modification_date );
		list( $replace['modification_year'], $replace['modification_month'], $replace['modification_day'] ) = explode( '-', $modification_date[0] );
		list( $replace['modification_hour'], $replace['modification_minutes'], $replace['modification_seconds'] ) = explode( ':', $modification_date[1] );
	*/

	// FixIn: 8.4.2.11.
	if ( isset( $replace['rangetime'] ) ) {
		$replace['rangetime'] = wpbc_time_slot_in_format( $replace['rangetime'] );
	}
	if ( isset( $replace['starttime'] ) ) {
		$replace['starttime'] = wpbc_time_in_format( $replace['starttime'] );
	}
	if ( isset( $replace['endtime'] ) ) {
		$replace['endtime'] = wpbc_time_in_format( $replace['endtime'] );
	}

	// FixIn: 8.2.1.25.
	$booking_data = wpbc_db_get_booking_details( $booking_id );

	if ( ! empty( $booking_data ) ) {
		foreach ( $booking_data as $booking_key => $booking_data ) {
			if ( ! isset( $replace[ $booking_key ] ) ) {
				$replace[ $booking_key ] = $booking_data;
			}
		}
	}

	// Set dates and times in correct format ---------------------------------------------------------------

	$is_add_wp_timezone = true;

	if ( ! empty( $replace['modification_date'] ) ) {

		// This date $values in GMT date/Time format. So  we need to switch  to  WordPress locale with TIME  sum of actual  GMT date/time value + shift  of timezone from WordPress.
		$modification_date = wpbc_datetime_localized( trim( $replace['modification_date'] ), 'Y-m-d H:i:s', $is_add_wp_timezone );

		$replace['modification_date'] = ' ' . $modification_date;
		$modification_date = explode( ' ', $modification_date );
		list( $replace['modification_year'], $replace['modification_month'], $replace['modification_day'] ) 	  = explode( '-', $modification_date[0] );
		list( $replace['modification_hour'], $replace['modification_minutes'], $replace['modification_seconds'] ) = explode( ':', $modification_date[1] );
	}
	// FixIn: 10.0.0.34.
	if ( ! empty( $replace['creation_date'] ) ) {

		// This date $values in GMT date/Time format. So  we need to switch  to  WordPress locale with TIME  sum of actual  GMT date/time value + shift  of timezone from WordPress.
		$creation_date = wpbc_datetime_localized( trim( $replace['creation_date'] ), 'Y-m-d H:i:s', $is_add_wp_timezone );

		$replace['creation_date'] = ' ' . $creation_date;
		$creation_date = explode( ' ', $creation_date );
		list( $replace['creation_year'], $replace['creation_month'], $replace['creation_day'] ) 	  = explode( '-', $creation_date[0] );
		list( $replace['creation_hour'], $replace['creation_minutes'], $replace['creation_seconds'] ) = explode( ':', $creation_date[1] );
	}

	return $replace;
}
add_filter( 'wpbc_replace_params_for_booking', 'wpbc_replace_params_for_booking_func', 10, 4 );


/**
	 * Replace shortcodes in string
 *
 * @param string $subject 					- string to  manipulate
 * @param array $replace_array 				- array with  values to  replace                 // array( [booking_id] => 9, [id] => 9, [dates] => July 3, 2016 14:00 - July 4, 2016 16:00, .... )
 * @param mixed $replace_unknown_shortcodes - replace unknown params, if false, then  no replace unknown params
 * @return string
 */
function wpbc_replace_booking_shortcodes( $subject, $replace_array , $replace_unknown_shortcodes = ' ' ) {

	$defaults = array(
		  'ip'              => wpbc_get_user_ip()
		, 'blogname'        => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
		, 'siteurl'         => get_site_url()
	);

	$replace = wp_parse_args( $replace_array, $defaults );

	foreach ( $replace as $replace_shortcode => $replace_value ) {

		if ( is_null( $replace_value ) ) {
			$replace_value = '';
		};

		$subject = str_replace( array(   '[' . $replace_shortcode . ']'
									   , '{' . $replace_shortcode . '}' )
								, $replace_value
								, $subject );
	}

	// Remove all shortcodes, which is not replaced early.
	if ( $replace_unknown_shortcodes !== false )
		$subject = preg_replace( '/[\s]{0,}[\[\{]{1}[a-zA-Z0-9.,-_]{0,}[\]\}]{1}[\s]{0,}/', $replace_unknown_shortcodes, $subject );


	return $subject;
}
