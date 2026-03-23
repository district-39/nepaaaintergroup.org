<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  DB - checking if table, field or index exists
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
// ==  DB - checking if table, field or index exists  ==
// =====================================================================================================================

/**
 * Check if table exist
 *
 * @param string $tablename
 *
 * @return 0|1
 * @global       $wpdb
 */
function wpbc_is_table_exists( $tablename ) {

	global $wpdb;

	if (
		( ( ! empty( $wpdb->prefix ) ) && ( strpos( $tablename, $wpdb->prefix ) === false ) )
		|| ( '_' == $wpdb->prefix )                                                                                    // FixIn: 8.7.3.16.
	) {
		$tablename = $wpdb->prefix . $tablename;
	}

	if ( 0 ) {
		$sql_check_table = $wpdb->prepare( "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_SCHEMA = '{$wpdb->dbname}') AND (TABLE_NAME = %s);", $tablename );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql_check_table );

		return count( $res );

	} else {

		$sql_check_table = $wpdb->prepare( "SHOW TABLES LIKE %s", $tablename );                                    //FixIn: 5.4.3
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql_check_table );

		return count( $res );
	}

}




/**
 * Check if table exist
 *
 * @global  $wpdb
 * @param string $tablename
 * @param  $fieldname
 * @return 0|1
 */
function wpbc_is_field_in_table_exists( $tablename, $fieldname ) {


	global $wpdb;

	if (
		( ( ! empty( $wpdb->prefix ) ) && ( strpos( $tablename, $wpdb->prefix ) === false ) )
		|| ( '_' == $wpdb->prefix )                                                                                    // FixIn: 8.7.3.16.
	) {
		$tablename = $wpdb->prefix . $tablename;
	}

	if ( 0 ) {

		$sql_check_table = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='{$tablename}' AND TABLE_SCHEMA='{$wpdb->dbname}' ";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql_check_table );

		foreach ( $res as $fld ) {
			if ( $fieldname === $fld->COLUMN_NAME ) {
				return 1;
			}
		}

	} else {

		$sql_check_table = "SHOW COLUMNS FROM {$tablename}";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$res = $wpdb->get_results( $sql_check_table );

		foreach ( $res as $fld ) {
			if ( $fld->Field == $fieldname ) {
				return 1;
			}
		}
	}

	return 0;
}


/**
 * Check if index exist
 *
 * @param string $tablename
 * @param        $fieldindex
 *
 * @return 0|1
 * @global       $wpdb
 */
function wpbc_is_index_in_table_exists( $tablename, $fieldindex ) {
	global $wpdb;
	if ( ( ! empty( $wpdb->prefix ) ) && ( strpos( $tablename, $wpdb->prefix ) === false ) ) {
		$tablename = $wpdb->prefix . $tablename;
	}
	/* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
	$sql_check_table = $wpdb->prepare( "SHOW INDEX FROM {$tablename} WHERE Key_name = %s", $fieldindex );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$res = $wpdb->get_results( $sql_check_table );
	if ( count( $res ) > 0 ) {
		return 1;
	} else {
		return 0;
	}
}
