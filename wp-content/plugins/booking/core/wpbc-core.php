<?php 
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Core Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */


if ( ! defined( 'ABSPATH' ) ) {        // Exit if accessed directly.
	exit;
}

// ---------------------------------------------------------------------------------------------------------------------
// Internal plugin action hooks system
// ---------------------------------------------------------------------------------------------------------------------
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
global $wpbc_bk_action, $wpbc_bk_filter;


function add_bk_filter( $filter_type, $filter ) {   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_filter;

	$args = array();
	if ( is_array( $filter ) && 1 == count( $filter ) && is_object( $filter[0] ) ) {
		$args[] =& $filter[0];
	} else {
		$args[] = $filter;
	}
	for ( $a = 2; $a < func_num_args(); $a++ ) {
		$args[] = func_get_arg( $a );
	}

	if ( is_array( $wpbc_bk_filter ) ) {
		if ( isset( $wpbc_bk_filter[ $filter_type ] ) ) {
			if ( is_array( $wpbc_bk_filter[ $filter_type ] ) ) {
				$wpbc_bk_filter[ $filter_type ][] = $args;
			} else {
				$wpbc_bk_filter[ $filter_type ] = array( $args );
			}
		} else {
			$wpbc_bk_filter[ $filter_type ] = array( $args );
		}
	} else {
		$wpbc_bk_filter = array( $filter_type => array( $args ) );
	}
}

function remove_bk_filter( $filter_type, $filter ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_filter;

	if ( isset( $wpbc_bk_filter[ $filter_type ] ) ) {
		for ( $i = 0; $i < count( $wpbc_bk_filter[ $filter_type ] ); $i++ ) {
			if ( $wpbc_bk_filter[ $filter_type ][ $i ][0] == $filter ) {
				$wpbc_bk_filter[ $filter_type ][ $i ] = null;

				return;
			}
		}
	}
}

function apply_bk_filter( $filter_type ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_filter;


	$args = array();
	for ( $a = 1; $a < func_num_args(); $a++ ) {
		$args[] = func_get_arg( $a );
	}

	if ( count( $args ) > 0 ) {
		$value = $args[0];
	} else {
		$value = false;
	}

	if ( is_array( $wpbc_bk_filter ) ) {
		if ( isset( $wpbc_bk_filter[ $filter_type ] ) ) {
			foreach ( $wpbc_bk_filter[ $filter_type ] as $filter ) {
				$filter_func = array_shift( $filter );
				$parameter   = $args;
				$value       = call_user_func_array( $filter_func, $parameter );
			}
		}
	}

	return $value;
}


function make_bk_action( $action_type ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_action;

	$args = array();
	for ( $a = 1; $a < func_num_args(); $a++ ) {
		$args[] = func_get_arg( $a );
	}

	if ( is_array( $wpbc_bk_action ) ) {
		if ( isset( $wpbc_bk_action[ $action_type ] ) ) {
			foreach ( $wpbc_bk_action[ $action_type ] as $action ) {
				$action_func = array_shift( $action );
				$parameter   = $action;
				call_user_func_array( $action_func, $args );
			}
		}
	}
}

function add_bk_action( $action_type, $action ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_action;

	$args = array();
	if ( is_array( $action ) && 1 == count( $action ) && is_object( $action[0] ) ) // array(&$this)
	{
		$args[] =& $action[0];
	} else {
		$args[] = $action;
	}
	for ( $a = 2; $a < func_num_args(); $a++ ) {
		$args[] = func_get_arg( $a );
	}

	if ( is_array( $wpbc_bk_action ) ) {
		if ( isset( $wpbc_bk_action[ $action_type ] ) ) {
			if ( is_array( $wpbc_bk_action[ $action_type ] ) ) {
				$wpbc_bk_action[ $action_type ][] = $args;
			} else {
				$wpbc_bk_action[ $action_type ] = array( $args );
			}
		} else {
			$wpbc_bk_action[ $action_type ] = array( $args );
		}
	} else {
		$wpbc_bk_action = array( $action_type => array( $args ) );
	}
}

function remove_bk_action( $action_type, $action ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $wpbc_bk_action;

	$elements_count = count( $wpbc_bk_action[ $action_type ] );
	if ( isset( $wpbc_bk_action[ $action_type ] ) ) {
		for ( $i = 0; $i < $elements_count; $i++ ) {
			if ( $wpbc_bk_action[ $action_type ][ $i ][0] == $action ) {
				$wpbc_bk_action[ $action_type ][ $i ] = null;

				return;
			}
		}
	}
}


function get_bk_option( $option, $default = false ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

	$u_value = apply_bk_filter( 'wpdev_bk_get_option', 'no-values', $option, $default );
	if ( 'no-values' !== $u_value ) {
		return $u_value;
	}
	return get_option( $option, $default );
}

function update_bk_option( $option, $newvalue ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

	$u_value = apply_bk_filter( 'wpdev_bk_update_option', 'no-values', $option, $newvalue );
	if ( 'no-values' !== $u_value ) {
		return $u_value;
	}
	return update_option( $option, $newvalue );
}

function delete_bk_option( $option ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

	$u_value = apply_bk_filter( 'wpdev_bk_delete_option', 'no-values', $option );
	if ( 'no-values' !== $u_value ) {
		return $u_value;
	}
	return delete_option( $option );
}

function add_bk_option( $option, $value = '', $deprecated = '', $autoload = null ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

	$u_value = apply_bk_filter( 'wpdev_bk_add_option', 'no-values', $option, $value, $deprecated, $autoload );
	if ( 'no-values' !== $u_value ) {
		return $u_value;
	}
	return add_option( $option, $value, '', $autoload );
}


////////////////////////////////////////////////////////////////////////////////
//  Booking Meta Options    -       'booking_options'   IN 'wp_booking' TABLE
////////////////////////////////////////////////////////////////////////////////

/**
 * Update Booking Meta Option
 *
 * @param int $booking_id       ID of the booking
 * @param array $option_arr     Associative array for saving into booking options
 *                              Example 1:  saving 'locale' => 'de_DE':     [ 'locale' => 'de_DE' ],
 *                              Example 2:                                  [ 'paid' => '100.00', 'balance' => '90.00' ]
 *
 * @return array|object|stdClass[]|null
 */
function wpbc_save_booking_meta_option( $booking_id, $option_arr ) {

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$select_result = $wpdb->get_results( $wpdb->prepare( "SELECT booking_options FROM {$wpdb->prefix}booking WHERE booking_id = %d", (int) $booking_id ) );

	if ( ! empty( $select_result ) ) {

		$select_result = $select_result[0]->booking_options;

		if ( is_null( $select_result ) ) {      // NULL -> option was not defined yet for this booking row.
			$exist_booking_option_arr = array();
		} else {
			$exist_booking_option_arr = maybe_unserialize( $select_result );
		}
	} else {
		return false;                           // No booking with such ID.
	}

	// Merge exist booking option  array  with  new data.
	$new_booking_option_arr = array_merge(
		$exist_booking_option_arr,
		$option_arr
	);

	$serialized_booking_option_arr = maybe_serialize( $new_booking_option_arr );

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	if ( false === $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET booking_options = %s WHERE booking_id = %d", $serialized_booking_option_arr, (int) $booking_id ) ) ) {
		debuge_error( 'Error saving to DB', __FILE__, __LINE__ );
		return false;
	}

	return true;
}


/**
 * Get Booking Meta Option
 *
 * @param int   $booking_id       ID of the booking
 * @param false $option_name      name of booking option  or false (can  be skipped)  to  return  array  of options
 *
 * @return array|mixed|string
 */
function wpbc_get_booking_meta_option( $booking_id, $option_name = false ) {

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$select_result = $wpdb->get_results( $wpdb->prepare( "SELECT booking_options FROM {$wpdb->prefix}booking WHERE booking_id = %d", (int) $booking_id ) );

	$exist_booking_option_arr = array();

	if ( ! empty( $select_result ) ) {

		$select_result = $select_result[0]->booking_options;

		if ( ! is_null( $select_result ) ) {      // NULL -> chekc if option was not defined for this booking row.

			$exist_booking_option_arr = maybe_unserialize( $select_result );

			if (
				( ! empty( $option_name ) ) &&
				( ! empty( $exist_booking_option_arr[ $option_name ] ) )
			) {
				return $exist_booking_option_arr[ $option_name ];
			}
		}
	}

	return $exist_booking_option_arr;
}