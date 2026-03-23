<?php /**
 * @version 1.0
 * @description Booking Hash Functions
 * @category  Booking Hash
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-08-05
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

//     H   A   S   H                                                                                                            // FixIn: 9.2.3.3.

/**
 * Get booking ID and resource ID  by booking HASH
 *
 * @param $booking_hash
 *
 * @return array|false		-   array( $booking_id, $resource_id )  |  false 		if not found
 */
function wpbc_hash__get_booking_id__resource_id( $booking_hash ) {

	if ( '' === $booking_hash ) {
		return false;
	}
	global $wpdb;

	if ( class_exists( 'wpdev_bk_personal' ) ) {

		$sql = $wpdb->prepare( "SELECT booking_id as id, booking_type as type FROM {$wpdb->prefix}booking as bk  WHERE  bk.hash = %s", $booking_hash );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql );

		if ( ( ! empty( $res ) ) && ( is_array( $res ) ) && ( isset( $res[0]->id ) ) && ( isset( $res[0]->type ) ) ) {          // FixIn: 8.1.2.13.
			return array( $res[0]->id, $res[0]->type );
		}
	} else {

		$sql = $wpdb->prepare( "SELECT booking_id as id FROM {$wpdb->prefix}booking as bk  WHERE  bk.hash = %s", $booking_hash );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql );

		if ( ( ! empty( $res ) ) && ( is_array( $res ) ) && ( isset( $res[0]->id ) ) ) {                                        // FixIn: 8.1.2.13.
			return array( $res[0]->id, 1 );
		}
	}

	return false;
}


/**
 * Get booking HASH and resource ID
 * by booking ID
 *
 * @param $booking_id
 *
 * @return array|false        -   array( $hash, $resource_id )  |  false        if not found
 */
function wpbc_hash__get_booking_hash__resource_id( $booking_id ) {

	if ( '' == $booking_id ) {
		return false;
	}
	global $wpdb;

	if ( class_exists( 'wpdev_bk_personal' ) ) {

		$sql = $wpdb->prepare( "SELECT hash, booking_type as type FROM {$wpdb->prefix}booking as bk  WHERE  bk.booking_id = %d", $booking_id );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql );

		if ( ( ! empty( $res ) ) && ( is_array( $res ) ) && ( isset( $res[0]->hash ) ) && ( isset( $res[0]->type ) ) ) {
			return array( $res[0]->hash, $res[0]->type );
		}
	} else {

		$sql = $wpdb->prepare( "SELECT hash FROM {$wpdb->prefix}booking as bk  WHERE  bk.booking_id = %d", $booking_id );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql );

		if ( ( ! empty( $res ) ) && ( is_array( $res ) ) && ( isset( $res[0]->hash ) ) ) {
			return array( $res[0]->hash, 1 );
		}
	}

	return false;
}


/**
 * Update booking hash to newly generated.    Run after creation/modification of booking in post request
 *
 * @param $booking_id
 * @param $resource_id
 *
 * @return void
 */
function wpbc_hash__update_booking_hash( $booking_id, $resource_id = '1' ) {
	global $wpdb;
	// FixIn: 10.12.1.5.
	$update_sql = $wpdb->prepare(
		"UPDATE {$wpdb->prefix}booking SET hash = MD5(%s) WHERE booking_id = %d"
		, time() . '_' . wp_rand( 1000, 1000000 )
		, $booking_id
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	if ( false === $wpdb->query( $update_sql ) ) {
		?>
		<script type="text/javascript"> document.getElementById('submiting<?php echo esc_attr( $resource_id ); ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error( 'Error during updating hash in BD', __FILE__, __LINE__ ); ?></div>'; </script> <?php
		die();
	}
}


/**
 * Get JavaScript for start dates selection  in calendar after 1.5 second
 *
 * @param array $to_select__dates_sql_arr
 * @param int 	$resource_id
 *
 * @return string	-	 JavaScript code as text
 */
function wpbc_get_dates_selection_js_code( $to_select__dates_sql_arr, $resource_id ){

	$dates_selection_js_code  = '<script type="text/javascript"> ' . wpbc_jq_ready_start();                                 				// FixIn: 10.1.3.7.

	// FixIn: 10.0.0.50.
	$dates_selection_js_code .= ' var select_dates_in_calendar_id = ' . intval( $resource_id ) . ';';
	$dates_selection_js_code .= " jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function ( event, loaded_resource_id ){ ";		// Fire on all booking dates loaded
	$dates_selection_js_code .= " 	if ( loaded_resource_id == select_dates_in_calendar_id ){ ";

		$string__dates_sql_arr = array_map( function ( $value ) {
												$value = explode( ' ', $value );
												$new_value = "'" . $value[0] . "'";
												return $new_value;
											}, $to_select__dates_sql_arr );
		$string__dates_sql_arr = array_unique($string__dates_sql_arr);
		$string__dates_sql_str = implode( ',', $string__dates_sql_arr );

	$dates_selection_js_code .= " 		setTimeout( function (){ wpbc_auto_select_dates_in_calendar( select_dates_in_calendar_id, [" . $string__dates_sql_str . "] ); }, 500 );";
	$dates_selection_js_code .= " 	} ";
	$dates_selection_js_code .= " } ); ";

	$dates_selection_js_code .= wpbc_jq_ready_end() . '</script>';                                                          				// FixIn: 10.1.3.7.

	return $dates_selection_js_code;
}


/**
 * Get booking ID,  from  the $_GET['booking_hash'] in url
 *
 * @param $booking_hash		if default false,  then  get  hash  from $_GET['booking_hash'] Otherwise get  it from  this parameter
 *
 * @return int|string	ID of booking or '', if not found
 */
function wpbc_get_booking_id__from_hash_in_url( $booking_hash = false ){

	$result_arr =  wpbc_get_booking_arr__from_hash_in_url( $booking_hash );

	return $result_arr['booking_id'];
}


/**
 * Get booking ID,  from  the $_GET['booking_hash'] in url
 *
 * @param $booking_hash		if default false,  then  get  hash  from $_GET['booking_hash'] Otherwise get  it from  this parameter
 *
 * @return int|string	ID of ooking resource or '', if not found
 */
function wpbc_get_resource_id__from_hash_in_url( $booking_hash = false ){

	$result_arr = wpbc_get_booking_arr__from_hash_in_url( $booking_hash );

	return $result_arr['resource_id'];
}



/**
 * Get booking data ['booking_id'  => 2453, 'resource_id' => 4 ],  from  the   URL   -- $_GET['booking_hash']
 *
 * @param $booking_hash		if default false,  then  get  hash  from $_REQUEST['booking_hash'] 		Otherwise get  it from  this parameter
 *
 * @return array        if not found then 	['booking_id'  => '',   'resource_id' => '']
 * 						otherwise  			['booking_id'  => 2453, 'resource_id' => 4 ]
 */
function wpbc_get_booking_arr__from_hash_in_url( $booking_hash = false ){

	if ( empty( $booking_hash ) ) {
		$booking_hash = ( isset( $_REQUEST['booking_hash'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['booking_hash'] ) ) : ''; /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */
	}

	$booking_id  = '';
	$resource_id = '';

	if ( ! empty( $booking_hash ) ) {
		$my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $booking_hash );

		if (false !== $my_booking_id_type ){
			list( $booking_id, $resource_id ) = $my_booking_id_type;
		}
	}

	$result = array(
					'booking_id'  => $booking_id,
					'resource_id' => $resource_id
				);

	return $result;

}

// FixIn: 10.10.1.1.
/**
 * Change hash of booking after approval / pending / trash / restore   booking(s)
 *
 * @param integer|string $booking_id_csd        - ID(s) of booking(s): integer or comma seperated integer string.
 * @param bool           $is_approve_or_pending - Status of the action: approved or pending | trashed or restored.
 *
 * @return void
 */
function wpbc_hook__change_hash__afteraction( $booking_id_csd, $is_approve_or_pending ) {

	$is_change_hash_after_approvement = get_bk_option( 'booking_is_change_hash_after_approvement' );

	if ( 'Off' !== $is_change_hash_after_approvement ) {

		if ( is_numeric( $booking_id_csd ) ) {
			wpbc_hash__update_booking_hash( intval( $booking_id_csd ) );
		} else {
			$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );
			$booking_id_arr = explode( ',', $booking_id_csd );
			foreach ( $booking_id_arr as $booking_id ) {
				wpbc_hash__update_booking_hash( (int) $booking_id );
			}
		}
	}
}
add_action( 'wpbc_booking_approved', 'wpbc_hook__change_hash__afteraction', 10, 2 );
add_action( 'wpbc_booking_action__approved', 'wpbc_hook__change_hash__afteraction', 10, 2 );
add_action( 'wpbc_booking_trash', 'wpbc_hook__change_hash__afteraction', 10, 2 );
add_action( 'wpbc_booking_action__trash', 'wpbc_hook__change_hash__afteraction', 10, 2 );
