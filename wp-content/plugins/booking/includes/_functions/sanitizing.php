<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Security: Escaping & Sanitizing Functions
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
// ==  Security: Escaping & Sanitizing  ==
// =====================================================================================================================

/**
 * Check specific parameters in ARRAY and return cleaned params or default values
 *
 * @param array $request_params_values_arr        = / think like in $_REQUEST parameter /
 *                                      array(
												* 'page_num'         => 1,
												* 'page_items_count' => 10,
												* 'sort'             => 'rule_id',
												* 'sort_type'        => 'DESC',
												* 'status'           => '',
												* 'keyword'          => '',
												* 'create_date'	   => ''
										* )
 * @param array $params_rules                     = array(
 * 'page_num'          => array( 'validate' => 'd', 					'default' => 1 )
											* , 'page_items_count'  => array( 'validate' => 'd', 					'default' => 10 )
											* , 'sort'              => array( 'validate' => array( 'rule_id' ),	'default' => 'rule_id' )
											* , 'sort_type'         => array( 'validate' => array( 'ASC', 'DESC'),'default' => 'DESC' )
											* , 'status'            => array( 'validate' => 's', 					'default' => '' )
											* , 'keyword'           => array( 'validate' => 's', 					'default' => '' )
											* , 'create_date'       => array( 'validate' => 'date', 				'default' => '' )
										* )
 *
 *
 * 'd';                             // '1' | ''
 * 's';                             // string   !!! Clean 'LIKE' string for DB !!!
 * 'digit_or_csd';                  // '0' | '1,2,3' | ''
 * 'digit_or_date';                 // number | date 2016-07-20
 *
 * 'checked_skip_it'                // Skip  checking
 *  array( '0', 'trash', 'any');    // Elements only listed in array
 *
 *@return array $clean_params = Array	(
											* [page_num] => 3
											* [page_items_count] => 20
											* [sort] => booking_id
											* [sort_type] => DESC
											* [keyword] =>
											* [source] =>
											* [create_date] =>
										* )
 *
 *
    Example of Direct Clean Params:

				$request_params_ajx_booking  = array(
										  'page_num'          => array( 'validate' => 'd', 					'default' => 1 )
										, 'page_items_count'  => array( 'validate' => 'd', 					'default' => 10 )
										, 'sort'              => array( 'validate' => array( 'ajx_booking_id' ),	'default' => 'ajx_booking_id' )
										, 'sort_type'         => array( 'validate' => array( 'ASC', 'DESC'),'default' => 'DESC' )
										, 'status'            => array( 'validate' => 's', 					'default' => '' )
										, 'keyword'           => array( 'validate' => 's', 					'default' => '' )
										, 'ru_create_date'    => array( 'validate' => 'date', 				'default' => '' )
				);
				$request_params_values = array(                                                                         // Usually 		$request_params_values 	is  $_REQUEST
										'page_num'         => 1,
										'page_items_count' => 3,
										'sort'             => 'ajx_booking_id',
										'sort_type'        => 'DESC',
										'status'           => '',
										'keyword'          => '',
										'ru_create_date'   => ''
								);
				$request_params = wpbc_sanitize_params_in_arr( $request_params_values, $request_params_ajx_booking );
 */
function wpbc_sanitize_params_in_arr( $request_params_values_arr, $params_rules ){

	$clean_params = array();

	foreach ( $params_rules as $request_key_name => $clean_type ) {

		if ( isset( $request_params_values_arr[ $request_key_name ] ) ) {
			$request_value_check = $request_params_values_arr[ $request_key_name ];
		} else {
			$request_value_check = false;
		}

		// If not defined in VALUES (think like in $_REQUEST parameter),  then  get  default value
		if ( false === $request_value_check ) {

			// D E F A U L T
			$clean_params[ $request_key_name ] = $params_rules[ $request_key_name ]['default'];

		} else {

			// C L E A N I N G
			$clean_type = $params_rules[ $request_key_name ]['validate'];

			// Check only values from this Array
			if ( is_array( $clean_type ) ) {

				$clean_type = array_map( 'strtolower', $clean_type );

				if ( ( isset( $request_value_check ) ) && ( ! in_array( strtolower( $request_value_check ), $clean_type ) ) ) {
					$clean_type          = 'checked_skip_it';
					$request_value_check = $params_rules[ $request_key_name ]['default'];                            //  Reset it, if value not in array And get default value
				} else {
					$clean_type = 'checked_skip_it';
				}
			}

			switch ( $clean_type ) {

				case 'checked_skip_it':
					$clean_params[ $request_key_name ] = $request_value_check;
					break;

				case 'date':													// Date
					$clean_params[ $request_key_name ] = wpbc_sanitize_date( $request_value_check );
					break;

				case 'csv_dates':													// CSV Dates: '11.11.2025, 12.11.2025, 13.11.2025'  or  '2024-02-06, 2024-02-10'
					$clean_params[ $request_key_name ] = wpbc_sanitize_csv_dates( $request_value_check );               // FixIn: 9.9.1.1.
					break;

				case 'digit_or_date':                                            // digit or Date
					$clean_params[ $request_key_name ] = wpbc_sanitize_digit_or_date( $request_value_check );
					break;

				case 'digit_or_csd':                                            // digit or comma separated digit
					$clean_params[ $request_key_name ] = wpbc_sanitize_digit_or_csd( $request_value_check );
					break;

				case 's':                                                       // string
					$clean_params[ $request_key_name ] = wpbc_sanitize_text( $request_value_check );
					break;

				case 'strong':                                                       // string
					$clean_params[ $request_key_name ] = wpbc_sanitize_text_strong( $request_value_check );
					break;

				case 'array':
					if ( is_array( $request_value_check ) ) {
						foreach ( $request_value_check as $check_arr_index => $check_arr_value ) {
							$request_value_check[ $check_arr_index ] = wpbc_sanitize_text( $check_arr_value );     // Check  each option as string
						}
						$clean_params[ $request_key_name ] = $request_value_check;

					} else {
						$clean_params[ $request_key_name ] = $params_rules[ $request_key_name ]['default'];
					}
					break;

				case 'digit_or_empty':                                                       // digit or ''
					if ( '' === $request_value_check) {
						$clean_params[ $request_key_name ] = '';
					} else {
						$clean_params[ $request_key_name ] = intval( $request_value_check );
					}
					break;

				case 'float_or_empty':                                                       // digit or ''
					if ( '' === $request_value_check) {
						$clean_params[ $request_key_name ] = '';
					} else {

						// In case if was entered 10,99 instead of 10.99
						$request_value_check = str_replace( ',', '.', $request_value_check );

						$clean_params[ $request_key_name ] = floatval( $request_value_check );
					}
					break;

				case 'f':                                                                   // float
					$clean_params[ $request_key_name ] = floatval( $request_value_check );
					break;

				case 'd':                                                                   // digit
					$clean_params[ $request_key_name ] = intval( $request_value_check );
					break;

				default:
					$clean_params[ $request_key_name ] = intval( $request_value_check );
					break;
			}
		}
	}
	return $clean_params;
}

/**
 * Check  parameter  if it number or comma separated list  of numbers
 *
 * @param string | array $value
 *
 * @return string | array
 *
 * Example:
					* wpbc_sanitize_digit_or_csd( '12,a,45,9' )                  => '12,0,45,9'
 * or
					* wpbc_sanitize_digit_or_csd( '10a' )                        => '10
 * or
					* wpbc_sanitize_digit_or_csd( array( '12,a,45,9', '10a' ) )  => array ( '12,0,45,9',  '10' )
 */
function wpbc_sanitize_digit_or_csd( $value ) {                                // FixIn: 6.2.1.4.

	if ( $value === '' ) {
		return $value;
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $key => $check_value ) {
			$value[ $key ] = wpbc_sanitize_digit_or_csd( $check_value );
		}

		return $value;
	}

	$value         = str_replace( ';', ',', $value );
	$array_of_nums = explode( ',', $value );


	$result = array();
	foreach ( $array_of_nums as $check_element ) {
		$result[] = intval( $check_element );
	}
	$result = implode( ',', $result );

	return $result;
}

/**
 * Check  about Valid date or number, like 2016-07-20 and return this date or number
 *
 * @param string $value
 *
 * @return string | int  '2022-05-31' or 5  or ''
 */
function wpbc_sanitize_digit_or_date( $value ) {                               // FixIn: 6.2.1.4.

	if ( $value === '' ) return $value;

	if ( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) ) {

		return $value;                                                      // Date is valid in format: 2016-07-20
	} else {
		return intval( $value );
	}

}

/**
 * Check about Valid date, like 2016-07-20 and return this date or ''
 *
 * @param string $value
 *
 * @return string '2022-05-31' or ''
 */
function wpbc_sanitize_date( $value ) {                               // FixIn: 6.2.1.4.

	if ( $value === '' ) return $value;

	if ( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) ) {

		return $value;                                                      // Date is valid in format: 2016-07-20
	} else {
		return '';
	}

}

/**
 * Check about Valid date, like '31.05.2022 and return this date or ''
 *
 * @param string $value
 *
 * @return string '31.05.2022' or ''
 */
function wpbc_sanitize_date_dmy( $value ) {                                                                             // FixIn: 9.9.1.1.

	if ( $value === '' ) return $value;

	if ( preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]).(0[1-9]|1[0-2]).[0-9]{4}$/", $value ) ) {

		return $value;                                                      // Date is valid in format: 31.05.2022
	} else {
		return '';
	}

}

/**
 * Check about Valid date(s), like CSV Dates:  such  as: '11.11.2025, 12.11.2025, 13.11.2025'  or  '2024-02-06, 2024-02-10'    and then return them sanitized dates or ''
 *
 * @param string $value
 *
 * @return string '2022-05-31' or ''
 */
function wpbc_sanitize_csv_dates( $value ) {                                                                            // FixIn: 9.9.1.1.

	if ( '' === $value ) { return $value; }

	$value         = str_replace( ';', ',', $value );
	$array_of_nums = explode( ',', $value );

	$result = array();

	foreach ( $array_of_nums as $single_date ) {

		$single_date = trim( $single_date );

		// Check  for date '2024-02-06
		$date_ymd = wpbc_sanitize_date( $single_date );

		if ( '' !== $date_ymd ) {
			$result[] = $date_ymd;
		} else {

			// Otherwise check  for date: '06.02.2024'
			$date_dmy = wpbc_sanitize_date_dmy( $single_date );
			if ( '' !== $date_dmy ) {
				$result[] = $date_dmy;
			}
		}
	}

	$result = implode( ',', $result );

	return $result;
}

/**
 * Escape string from SQL for the HTML form field
 *
 * @param string $value
 *
 * @return string
 *
 * Used: esc_sql function.
 *
 * https://codex.wordpress.org/Function_Reference/esc_sql
 * Note: Be careful to use this function correctly. It will only escape values to be used in strings in the query.
 * That is, it only provides escaping for values that will be within quotes in the SQL (as in field = '{$escaped_value}').
 * If your value is not going to be within quotes, your code will still be vulnerable to SQL injection.
 * For example, this is vulnerable, because the escaped value is not surrounded by quotes in the SQL query:
 * ORDER BY {$escaped_value}. As such, this function does not escape unquoted numeric values, field names, or SQL keywords.
 *
 */
function wpbc_sanitize_text( $value ){

	$value_trimmed = trim( stripslashes( $value ) );                    // \' becomes ' and so on

	$esc_sql_value = sanitize_textarea_field( $value_trimmed );         // preserves new lines (\n) and other whitespace
	//$esc_sql_value = sanitize_text_field( $value_trimmed );           // remove new lines (\n) and other whitespace

	//global $wpdb;
	//$value = trim( $wpdb->prepare( "'%s'",  $esc_sql_value ) , "'" );
	//$esc_sql_value = trim( stripslashes( $esc_sql_value ) );

	return $esc_sql_value;
}

/**
 * Escape string from SQL for the HTML form field
 *
 * @param string $value
 *
 * @return string
 *
 * Used: esc_sql function.
 *
 * https://codex.wordpress.org/Function_Reference/esc_sql
 * Note: Be careful to use this function correctly. It will only escape values to be used in strings in the query.
 * That is, it only provides escaping for values that will be within quotes in the SQL (as in field = '{$escaped_value}').
 * If your value is not going to be within quotes, your code will still be vulnerable to SQL injection.
 * For example, this is vulnerable, because the escaped value is not surrounded by quotes in the SQL query:
 * ORDER BY {$escaped_value}. As such, this function does not escape unquoted numeric values, field names, or SQL keywords.
 *
 */
function wpbc_sanitize_text_strong( $value ){

	$value_trimmed = trim( stripslashes( $value ) );                    // \' becomes ' and so on

	//$esc_sql_value = sanitize_text_field( $value_trimmed );           // remove new lines (\n) and other whitespace
	$esc_sql_value = sanitize_textarea_field( $value_trimmed );         // preserves new lines (\n) and other whitespace

	// clean any tags
    $esc_sql_value = preg_replace( '/<[^>]*>/', '', $esc_sql_value );
    $esc_sql_value = str_replace( '<', ' ', $esc_sql_value );
    $esc_sql_value = str_replace( '>', ' ', $esc_sql_value );
    $esc_sql_value = wp_strip_all_tags( $esc_sql_value );

	//FixIn: 9.7.4.1	-	escape coded html/xss							// Escape any XSS injection
	// If we have field converted to 'Unicode Hex Character Code', then  we make HTML decode firstly (html_entity_decode) and then make sanitizing
    $esc_sql_value = sanitize_textarea_field( html_entity_decode( $esc_sql_value ) );

	// $esc_sql_value = str_replace('%', '&#37;', $esc_sql_value );            // clean any % from the form, because otherwise, there is problems with SQL prepare function
	// $esc_sql_value = str_replace('_', '&#95;', $esc_sql_value );            // clean any _

	// $esc_sql_value = str_replace('^', '&#94;', $esc_sql_value );            // clean any ^  caret symbols
	// $esc_sql_value = str_replace('~', '&#126;', $esc_sql_value );           // clean any ~ equivalency sign - tilde

	return $esc_sql_value;
}


// ---------------------------------------------------------------------------------------------------------------------
// Other Sanitize functions
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Sanitize $_GET, $_POST, $_REQUEST text parameters									    				    // FixIn: 10.0.0.12.
 *
 * @param $value
 * @param $keep_newlines bool
 *
 * @return string
 */
function wpbc_clean_text_value( $value , $keep_newlines = false  ){

	if ( $keep_newlines ) {
		$value_cleaned = sanitize_textarea_field( $value );
	} else {
		$value_cleaned = sanitize_text_field( $value );
	}

	return $value_cleaned;
}


// check $value for injection here
function wpbc_clean_parameter( $value, $is_escape_sql = true ) {

	$value = preg_replace( '/<[^>]*>/', '', $value );                       // clean any tags
	$value = str_replace( '<', ' ', $value );
	$value = str_replace( '>', ' ', $value );
	$value = wp_strip_all_tags( $value );

	//FixIn: 9.7.4.1	-	escape coded html/xss							// Escape any XSS injection
	$value = sanitize_textarea_field( $value );
	$value = sanitize_textarea_field( html_entity_decode( $value ) );		// If we have field converted to 'Unicode Hex Character Code', then  we make HTML decode firstly (html_entity_decode) and then make sanitizing

	if ( $is_escape_sql ) {
		$value = esc_sql( $value );			// Clean SQL injection					// FixIn: 9.7.4.2.
	}

	$value = esc_textarea( $value );																				// FixIn: 7.1.1.2.

	return $value;
}


/**
 * Check parameter  if it number or comma separated list  of numbers
 *
 * @param $value
 * @return array|string
 *
 * Example:
 *					wpbc_clean_digit_or_csd( '12,a,45,9' )                  => '12,0,45,9'
 * or
 *					wpbc_clean_digit_or_csd( '10a' )                        => '10
 * or
 *					wpbc_clean_digit_or_csd( array( '12,a,45,9', '10a' ) )  => array ( '12,0,45,9',  '10' )
 */
function wpbc_clean_digit_or_csd( $value ) {

	if ( '' === $value ) {
		return $value;
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $key => $check_value ) {
			$value[ $key ] = wpbc_clean_digit_or_csd( $check_value );
		}
		return $value;
	}

	$value = str_replace( ';', ',', $value );

	$array_of_nums = explode( ',', $value );

	$result = array();
	foreach ( $array_of_nums as $check_element ) {
		$result[] = intval( $check_element );                        // FixIn: 8.0.2.10.
	}
	$result = implode( ',', $result );

	return $result;
}


/**
 * Cehck  about Valid date,  like 2016-07-20 or digit
 *
 * @param string $value
 * @return string or int
 */
function wpbc_clean_digit_or_date( $value ) {                               // FixIn: 6.2.1.4.

	if ( $value === '' ) return $value;

	if ( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) ) {

		return $value;                                                      // Date is valid in format: 2016-07-20
	} else {
		return intval( $value );
	}

}


/**
 * Check about Valid dat in format '2024-05-08' otherwise return ''
 *
 * @param string $value
 *
 * @return string date or '' if date was not valie
 */
function wpbc_clean_date( $value ) {

	if (
			( ! empty( $value ) )
		 && ( preg_match( "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) )
	){
		return $value;       	// Date is valid in format: 2024-05-08
	}

	return '';					// Date not Valid
}


/**
 * Escape any XSS injection from  values in booking form
 *
 * @param array $structured_booking_data_arr    [...]
 *
 * @return array                                [...]
 */
function wpbc_escape_any_xss_in_arr( $structured_booking_data_arr ) {

	foreach ( $structured_booking_data_arr as $field_name => $field_value ) {

		if ( is_array( $field_value ) ) {

			$structured_booking_data_arr[ $field_name ] = wpbc_escape_any_xss_in_arr( $field_value );

		} else {
			$is_escape_sql = false;	// Do not replace %
			$field_value_cleaned = wpbc_escape_any_xss_in_string( $field_value, $is_escape_sql );
			$structured_booking_data_arr[ $field_name ] = $field_value_cleaned;
		}
	}

	return $structured_booking_data_arr;
}


/**
 * Escape any XSS injection from  string values
 *
 * @param string $field_value
 *
 * @return string
 */
function wpbc_escape_any_xss_in_string( $field_value, $is_escape_sql = true ) {

	$field_value_cleaned = wpbc_clean_parameter( $field_value, $is_escape_sql );
	$field_value_cleaned = str_replace( '%', '&#37;', $field_value_cleaned );                                   // clean % in form, because can be problems with SQL prepare function

	return $field_value_cleaned;
}


function wpbc_esc_like( $value_trimmed ) {

	global $wpdb;
	if ( method_exists( $wpdb ,'esc_like' ) )
		return $wpdb->esc_like( $value_trimmed );                           // Its require minimum WP 4.0.0
	else
		return addcslashes( $value_trimmed, '_%\\' );                       // Direct implementation  from $wpdb->esc_like(
}

/**
 * Escape single quote from '  to  &#039;
 *
 * @param string $value  - String to escape.
 *
 * @return array|string|string[]
 */
function wpbc_esc_single_quote( $value ) {

	$safe_text     = wp_check_invalid_utf8( $value );
	$escaped_value = str_replace( "'", '&#039;', $safe_text );

	return $escaped_value;
}

/**
 * Sanitize term to Slug format (no spaces, lowercase).
 * urldecode - reverse munging of UTF8 characters.
 *
 * @param mixed $value
 * @return string
 */
function wpbc_get_slug_format( $value ) {
	return  urldecode( sanitize_title( $value ) );
}


/**
 * Clean user string for using in SQL LIKE statement - append to  LIKE sql
 *
 * @param string $value - to clean
 * @return string       - escaped
 *                                  Exmaple:
 *                                              $search_escaped_like_title = wpbc_clean_like_string_for_append_in_sql_for_db( $input_var );
 *
 *                                              $where_sql = " WHERE title LIKE ". $search_escaped_like_title ." ";
 */
function wpbc_clean_like_string_for_append_in_sql_for_db( $value ) {
	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );
	$wild          = '%';
	$like          = $wild . wpbc_esc_like( $value_trimmed ) . $wild;
	/* phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder */
	$sql = $wpdb->prepare( "'%s'", $like );

	return $sql;


/* Help:
	 * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
 *
 * Example Prepared Statement:
 *
 *     $wild = '%';
 *     $find = 'only 43% of planets';
 *     $like = $wild . wpbc_esc_like( $find ) . $wild;
 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
 *
 * Example Escape Chain:
 *
 *     $sql  = esc_sql( wpbc_esc_like( $input ) );
 */

}


/**
 * Clean string for using in SQL LIKE requests inside single quotes:    WHERE title LIKE '%". $escaped_search_title ."%'
 *  Replaced _ to \_     % to \%      \   to   \\
 * @param string $value - to clean
 * @return string       - escaped
 *                                  Exmaple:
 *                                              $search_escaped_like_title = wpbc_clean_like_string_for_db( $input_var );
 *
 *                                              $where_sql = " WHERE title LIKE '%". $search_escaped_like_title ."%' ";
 *
 *                                  Important! Use SINGLE quotes after in SQL query:  LIKE '%".$data."%'
 */
function wpbc_clean_like_string_for_db( $value ){

	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );

	$value_trimmed = wpbc_esc_like( $value_trimmed );

	/* phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder */
	$value = trim( $wpdb->prepare( "'%s'", $value_trimmed ), "'" );

	return $value;

/* Help:
	 * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
 *
 * Example Prepared Statement:
 *
 *     $wild = '%';
 *     $find = 'only 43% of planets';
 *     $like = $wild . wpbc_esc_like( $find ) . $wild;
 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
 *
 * Example Escape Chain:
 *
 *     $sql  = esc_sql( wpbc_esc_like( $input ) );
 */
}


/**
 * Escape string from SQL for the HTML form field
 *
 * @param string $value
 * @return string
 *
 * Used: esc_sql function.
 *
 * https://codex.wordpress.org/Function_Reference/esc_sql
 * Note: Be careful to use this function correctly. It will only escape values to be used in strings in the query.
 * That is, it only provides escaping for values that will be within quotes in the SQL (as in field = '{$escaped_value}').
 * If your value is not going to be within quotes, your code will still be vulnerable to SQL injection.
 * For example, this is vulnerable, because the escaped value is not surrounded by quotes in the SQL query:
 * ORDER BY {$escaped_value}. As such, this function does not escape unquoted numeric values, field names, or SQL keywords.
 *
 */
function wpbc_clean_string_for_form( $value ){

	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );

	//FixIn: 8.0.2.10		//Fix for update of WP 4.8.3
	if ( method_exists( $wpdb, 'remove_placeholder_escape' ) )
		$esc_sql_value =  $wpdb->remove_placeholder_escape( esc_sql( $value_trimmed ) );
	else
		$esc_sql_value =  esc_sql(  $value_trimmed );

	//$value = trim( $wpdb->prepare( "'%s'",  $esc_sql_value ) , "'" );

	$esc_sql_value = trim( stripslashes( $esc_sql_value ) );

	return $esc_sql_value;

}


/**
 * Escape shortcode parameters
 *
 * @param array $attr
 *
 * @return array
 */
function wpbc_escape_shortcode_params( $attr ) {

	if ( is_array( $attr ) ) {

		$scaped_attr = array();

		foreach ( $attr as $attr_key => $attr_val ) {
			$attr_key = sanitize_text_field( $attr_key );        // FixIn: 10.11.2.1.
			$attr_val = sanitize_text_field( $attr_val );        // FixIn: 10.11.2.1.

			$scaped_attr[ $attr_key ] = $attr_val;
		}

		return $scaped_attr;
	}

	if ( is_string( $attr ) ) {

		$scaped_attr = sanitize_text_field( $attr );        // FixIn: 10.11.2.1.

		return $scaped_attr;
	}

	return $attr;
}
