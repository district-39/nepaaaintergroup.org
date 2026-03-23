<?php
/**
 * @version     1.0
 * @package     Booking Calendar
 * @subpackage  Booking Data Parsing functions
 * @category    Functions
 *
 * @author      wpdevelop
 * @link        https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified    2024-05-14
 * @file        ../includes/_functions/booking_data__parse.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// =====================================================================================================================
// ==   B o o k i n g   F o r m s   P a r s i n g  ==
// =====================================================================================================================

/**
 * Parse  "text^firstname1^John~..."  ->  [ 'firstname':[name:"firstname", value:"John" ...] ... ]   -   Parse (DB) form_data and get   fields array values
 *
 * @param string $form_data   	- formatted booking form  data from database,  like this: 'text^selected_short_dates_hint15^21/02/2023 - 24/02/2023~text^days_number_hint15^4~text^cost_hint15^&amp;#36;400.00~text^deposit_hint15^&amp;#36;40.00~text^standard_bk_cost15^100~...'
 * @param int    $resource_id 	- ID of booking resource
 * @param array  $params 	  	- (optional)  default: array()		define here what field we need to get	array( 'get' => 'value' )
 *
 * @return array
 * 					[
 * 					      rangetime = [
 * 					                      type          = "selectbox-multiple"
 * 					                      original_name = "rangetime3[]"
 * 					                      name          = "rangetime"
 * 					                      value         = "12:00 - 14:00"
 * 					                  ]
 * 					      name = [
 * 					                      type = "text"
 * 					                      original_name = "name3"
 * 					                      name = "name"
 * 					                      value = "test 1"
 * 					              ]
 * 					      secondname = [...]
 * 					      email = [...]
 * 					 ]
 *
 *   Example 1:
 *						   $structured_booking_data_arr = wpbc_get_parsed_booking_data_arr( $this_booking->form, $resource_id, array( 'get' => 'value' ) );
 *         		output:    [  	"rangetime" : 	"12:00 - 14:00",
 *                              "name":			"John" ,
 * 								"secondname" : 	"Smith" , 		"email" : "test@wpbookingcalendar.com" , "visitors" : "1" ... ]
 *
 *   Example 2:
 *				 		   $structured_booking_data_arr = wpbc_get_parsed_booking_data_arr( $this_booking->form, $resource_id );
 *   		 	output:    [  rangetime = [
 * 					                      type          = "selectbox-multiple"
 * 					                      original_name = "rangetime3[]"
 * 					                      name          = "rangetime"
 * 					                      value         = "12:00 - 14:00"
 * 					                  ]
 * 					      		name = [
 * 					                      type = "text"
 * 					                      original_name = "name3"
 * 					                      name = "name"
 * 					                      value = "test 1"
 * 					              ]
 * 					      		secondname = [...]
 * 					      		email = [...]
 * 					 		]
*/
function wpbc_get_parsed_booking_data_arr( $form_data, $resource_id = 1, $params = array( ) ) {

	$booking_data_arr = array();

	if ( ! empty( $form_data ) ) {

		$fields_arr = explode( '~', $form_data );

		foreach ( $fields_arr as $field ) {

			if ( false === strpos( $field, '^' ) ) {
				break;            // FixIn: 9.8.10.2.
			}

			list( $field_type, $field_original_name, $field_value ) = explode( '^', $field );

			$field_name = $field_original_name;

			// Is this multi select in checkboxes: [checkbox* ConducenteExtra "Si" "No"]   =>   [... 6: "checkbox^ConducenteExtra4[]^Si", 7: "checkbox^ConducenteExtra4[]^" ... ]
			$is_multi_options = ( '[]' == substr( $field_name, - 2 ) );

			$minus_additional = ( '[]' == substr( $field_name, - 2 ) ) ? 2 : 0;
			$field_name       = ( $minus_additional > 0 ) ? substr( $field_name, 0, - $minus_additional ) : $field_name;


			$minus_additional = ( strval( $resource_id ) == substr( $field_name, - 1 * ( strlen( strval( $resource_id ) ) ) ) )
				? strlen( strval( $resource_id ) ) : 0;
			$field_name       = ( $minus_additional > 0 ) ? substr( $field_name, 0, - $minus_additional ) : $field_name;

			if ( ( 'checkbox' === $field_type ) && ( 'true' === $field_value ) ) {
				//$field_value = strtolower( __( 'Yes', 'booking' ) );
				$field_value = 'true';
			}
			if ( ( 'checkbox' === $field_type ) && ( 'false' === $field_value ) ) {
				//$field_value = strtolower( __( 'No', 'booking' ) );
				$field_value = 'false';
			}

			if ( ! isset( $booking_data_arr[ $field_name ] ) ) {
				$booking_data_arr[ $field_name ] = array(
															'type'          => $field_type,
															'original_name' => $field_original_name,
															'name'          => $field_name,
															'value'         => array( $field_value )
														);
			} else {
				// All values we save as array values,  for situations  of MULTI options:
				//   [checkbox* ConducenteExtra "Si" "No"]   =>   [... 6: "checkbox^ConducenteExtra4[]^Si", 7: "checkbox^ConducenteExtra4[]^" ... ]
				$booking_data_arr[ $field_name ]['value'][] = $field_value;
			}
		}

		// Convert arrays to string
		foreach ( $booking_data_arr as $field_name => $field_structure_arr ) {									// FixIn: 9.8.9.1.
			$field_structure_arr['value'] = array_filter( $field_structure_arr['value'], function ( $v ) {
																											return ( $v !== '' );	// Remove All '' entries
																										} );
			$booking_data_arr[ $field_name ]['value'] = implode( ', ', $field_structure_arr['value'] );
		}


		if ( isset( $params['get'] ) ) {
			/**
			 * Now get only values or other fields:  [
			 *                                                "rangetime" : "12:00 - 14:00",
			 *                                              "name":"test 1" ,
			 *                                              "secondname" : "test 1" ,
			 *                                              "email" : "test@wpbookingcalendar.com" ,
			 *                                              "visitors" : "1"
			 *                                        ...
			 *                                        ]
			 */

			foreach ( $booking_data_arr as $field_name => $field_structure_arr ) {

				if ( isset( $field_structure_arr[ $params['get'] ] ) ) {
					$booking_data_arr[ $field_name ] = $field_structure_arr[ $params['get'] ];
				} else {
					$booking_data_arr[ $field_name ] = '-';
				}
			}
		}
	}

	return $booking_data_arr;
}

/**
 *  Convert  [ ... ]  -> "text^firstname1^John~..."   - encoded booking data string (Usually  for inserting into DB),  from  parsed booking data array
 *
 * @param array $booking_data_arr	- parsed booking data.
 * @param int $resource_id			- ID of booking resource  - In case,  if we need to  RE-UPDATE booking resource,  Otherwise skip  it
 *
 * @return string
 */
function wpbc_encode_booking_data_to_string( $booking_data_arr, $resource_id = 0 ) {

	$fields_arr = array();

	foreach ( $booking_data_arr as $fields ) {

		if ( ! empty( $resource_id ) ) {
			$fields_arr[] =         $fields['type']
							. '^' . $fields['name'] . $resource_id  . ( ( '[]' == substr( $fields['original_name'], - 2 ) ) ? '[]' : '' )
							. '^' . $fields['value'];
		} else {
			$fields_arr[] = $fields['type'] . '^' . $fields['original_name'] . '^' . $fields['value'];
		}

	}

	$form_data = implode( '~', $fields_arr );

	return $form_data;
}

// -------------------------------------------------------------------------------------------------------------

/**
 * Get Time fields  in booking form_data
 *
 * @param string $form_data   	- formatted booking form  data from database,  like this: 'text^selected_short_dates_hint15^21/02/2023 - 24/02/2023~text^days_number_hint15^4~text^cost_hint15^&amp;#36;400.00~text^deposit_hint15^&amp;#36;40.00~text^standard_bk_cost15^100~...'
 * @param int    $resource_id 	- ID of booking resource
 * @param array  $params 	  	- (optional)  default: array()		define here what field we need to get	array( 'get' => 'value' )
 *
 * @return array	[ "18:00:00", "20:00:00" ]		<- default
 *
 *   Example #1:          $times_his_arr = wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id);														--> [ "18:00:01", "20:00:02" ]
 *        same:           $times_his_arr = wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id, array( 'get' => 'times_his_arr' ) );					--> [ "18:00:01", "20:00:02" ]
 *   Example #2:    $time_as_seconds_arr = wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id, array( 'get' => 'time_as_seconds_arr' ) );			--> [ 64800, 72000 ]
 *   Example #4:    $booking_data_arr    = wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id, array( 'get' => 'structured_booking_data_arr' ) );	--> [ name = "John", secondname = "Smith", email = "john.smith@server.com", visitors = "2",... ]
 *
 *   Example #5:    $booking_data_arr    = wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id, array( 'get' => 'all' ) );
 *                    --> [
 *							  'structured_booking_data_arr' => [ name = "John", secondname = "Smith", email = "john.smith@server.com", visitors = "2",... ]
 *							  'time_as_seconds_arr' 		=> [ 64800, 72000 ]
 *							  'time_as_his_arr' 			=> [ "18:00:01", "20:00:02" ]
 * 						  ]
 */
function wpbc_get_times_his_arr__in_form_data( $form_data, $resource_id = 1, $params = array() ) {

	$defaults = array(
						'get' => 'times_his_arr'
					);
	$params   = wp_parse_args( $params, $defaults );


	// Get Time from  booking form
	$local_params = array();
	/**
	 * Get parsed booking form:                 = [ name = "John", secondname = "Smith", email = "john.smith@server.com", visitors = "2",... ]
	 */
	$local_params['structured_booking_data_arr'] = wpbc_get_parsed_booking_data_arr( $form_data, $resource_id, array( 'get' => 'value' ) );
	// $local_params['all_booking_data_arr']     = wpbc_get_parsed_booking_data_arr( $form_data, $resource_id );

	//  Important! : [ 64800, 72000 ]
	$local_params['time_as_seconds_arr'] = wpbc_get_in_booking_form__time_to_book_as_seconds_arr( $local_params['structured_booking_data_arr'] );

	// [ "18:00:00", "20:00:00" ]
	$time_as_seconds_arr    = $local_params['time_as_seconds_arr'];
	$time_as_seconds_arr[0] = ( 0 != $time_as_seconds_arr[0] ) ? $time_as_seconds_arr[0] + 1 : $time_as_seconds_arr[0];                 // set check  in time with  ended 1 second
	$time_as_seconds_arr[1] = ( ( 24 * 60 * 60 ) != $time_as_seconds_arr[1] ) ? $time_as_seconds_arr[1] + 2 : $time_as_seconds_arr[1];     // set check out time with  ended 2 seconds
	if ( ( 0 != $time_as_seconds_arr[0] ) && ( ( 24 * 60 * 60 ) == $time_as_seconds_arr[1] ) ) {
		//FixIn: 10.0.0.49  - in case if we have start time != 00:00  and end time as 24:00 then  set  end time as 23:59:52
		$time_as_seconds_arr[1] += - 8;
	}
	// [ '16:00:01', '18:00:02' ]
	$local_params['times_his_arr'] = array(
											  wpbc_transform__seconds__in__24_hours_his( $time_as_seconds_arr[0] ),
											  wpbc_transform__seconds__in__24_hours_his( $time_as_seconds_arr[1] )
											);
	if (
           ( isset( $params['get'] ) )
		&& ( isset( $local_params[ $params['get'] ] ) )
	) {
		return $local_params[ $params['get'] ];
	}

	return $local_params;
}

// -------------------------------------------------------------------------------------------------------------

/**
 * Get readable booking form data.	 Escape values here, as well!
 *
 *        - 1.    <= BS : 'Booking form show' configuration    from standard form in versions up to  Business Small version ,
 *        - 2     >= BM : If form data has field of custom form, then from custom form configuration,
 *        - 3     >= BM : Otherwise if resource has default custom  booking form,  then  from  this default custom  booking form
 *        - 4      = MU :  specific form of specific WP User
 *        - 5   finally : simple standard form
 *
 * @param string $form_data
 * @param int $resource_id
 *
 * @return string
 */
function wpbc_get__booking_form_data__show( $form_data, $resource_id = 1 , $params = array() ) {

	$defaults = array(
					  'is_replace_unknown_shortcodes' => true,
					  'unknown_shortcodes_replace_by' => ''
				);
	$params   = wp_parse_args( $params, $defaults );

	$booking_form_show = wpbc_get__booking_form_data_configuration( $resource_id, $form_data, $params );

	$booking_data_arr  = wpbc_get_parsed_booking_data_arr( $form_data, $resource_id, array( 'get' => 'value' ) );

	foreach ( $booking_data_arr as $key_param => $value_param ) {                                  					// FixIn: 6.1.1.4.

		$value_param = esc_html( $value_param );																	//FixIn: 9.7.4.1	-	escape coded html/xss
		$value_param = esc_html( html_entity_decode( $value_param ) );
		$value_param = nl2br($value_param);                                             							// Add BR instead if /n elements		// FixIn: 9.7.4.2.
		$value_param = wpbc_string__escape__then_convert__n_amp__html( $value_param );

		$value_param = wpbc_replace__true_false__to__yes_no( $value_param );												// FixIn: 9.8.9.1.

		if (
				( gettype( $value_param ) != 'array' )
			 && ( gettype( $value_param ) != 'object' )
		) {
			$booking_form_show = str_replace( '[' . $key_param . ']', $value_param, $booking_form_show );
		}
	}

	if ($params['is_replace_unknown_shortcodes']) {
		// Remove all shortcodes, which is not replaced early.
		$booking_form_show = preg_replace( '/[\s]{0,}\[[a-zA-Z0-9.,-_]{0,}\][\s]{0,}/', $params['unknown_shortcodes_replace_by'], $booking_form_show );        // FixIn: 6.1.1.4.
	}

	$booking_form_show = str_replace( "&amp;", '&', $booking_form_show );											// FixIn: 7.1.2.12.



	return $booking_form_show;
}

/**
 * Get name of custom  booking form,  if it was used in form  data OR booking resource use this default custom form
 *
 * @param int $resource_id
 * @param string $form_data		Form  data here, required in >= BM, because such  form_data can contain fields about used custom booking form
 *
 * @return string
 */
function wpbc_get__custom_booking_form_name( $resource_id = 1, $form_data = '' ) {

	$my_booking_form_name = '';

	if ( class_exists( 'wpdev_bk_biz_m' ) ) {

		if ( false !== strpos( $form_data, 'wpbc_custom_booking_form' . $resource_id . '^' ) ) {                        // FixIn: 9.4.3.12.

			$custom_booking_form_name = substr( $form_data, strpos( $form_data, 'wpbc_custom_booking_form' . $resource_id . '^' ) + strlen( 'wpbc_custom_booking_form' . $resource_id . '^' ) );

			if ( false !== strpos( $custom_booking_form_name, '~' ) ) {
				$custom_booking_form_name = substr( $custom_booking_form_name, 0, strpos( $custom_booking_form_name, '~' ) );
				$my_booking_form_name     = $custom_booking_form_name;
			}

		} else {

			// BM :: Get default Custom Form  of Resource
			$my_booking_form_name = apply_bk_filter( 'wpbc_get_default_custom_form', 'standard', $resource_id );
		}

		if ( 'standard' == $my_booking_form_name ) {
			$my_booking_form_name = '';
		}
	}

	return $my_booking_form_name;
}

/**
 * Get configuration  of 'BOOKING FORM   F I E L D S   SHORTCODES'  from  -  Booking > Settings > Form page
 *
 *        - 1.    <= BS : 'Booking form show' configuration    from standard form in versions up to  Business Small version ,
 *        - 2     >= BM : If form data has field of custom form, then from custom form configuration,
 *        - 3     >= BM : Otherwise if resource has default custom  booking form,  then  from  this default custom  booking form
 *        - 4      = MU :  specific form of specific WP User
 *        - 5   finally : simple standard form
 *
 * @param int $resource_id
 * @param string $form_name						Name of custom booking form, required in >= BM
 *
 * @return string
 *
 *   Example 1:     	$booking_form = wpbc_get__booking_form_fields__configuration( 1 );						<-  Load STANDARD booking form
 *   Example 2:     	$booking_form = wpbc_get__booking_form_fields__configuration( 1 , 'standard' );			<-  Load STANDARD booking form
 *   Example 3:     	$booking_form = wpbc_get__booking_form_fields__configuration( 1 , 'my_custom_form' );	<-  Load CUSTOM  booking form  with name 'my_custom_form'
 *   Example 4:     	$booking_form = wpbc_get__booking_form_fields__configuration( 1 , '' );					<-  Load CUSTOM booking form,  which  is DEFAULT for RESOURCE
 *   Example 4:     	$booking_form = wpbc_get__booking_form_fields__configuration( 10 );						<-  If resource ID = 10  belong to REGULAR User,  then  load  booking form  for this REGULAR USER
 *
 */
function wpbc_get__booking_form_fields__configuration( $resource_id = 1, $form_name = 'standard' ) {

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		$booking_form_configuration = wpbc_simple_form__get_booking_form__as_shortcodes();
	} else {
		$booking_form_configuration = get_bk_option( 'booking_form' );

		if ( class_exists( 'wpdev_bk_biz_m' ) ) {

			if ( $form_name != 'standard' ) {

				$booking_form_configuration = apply_bk_filter( 'wpdev_get_booking_form', $booking_form_configuration, $form_name );
			}

			// Get default Custom Form  for resource,  if $form_name == ''		wpbc_get__booking_form_fields__configuration(1,'')
			if ( empty( $form_name ) ) {

				$resource_default_custom_form_name = apply_bk_filter( 'wpbc_get_default_custom_form', 'standard', $resource_id );

				$booking_form_configuration = apply_bk_filter( 'wpdev_get_booking_form', $booking_form_configuration, $resource_default_custom_form_name );
			}

			//MU :: if resource of "Regular User" - then  GET STANDARD user form ( if ( get_bk_option( 'booking_is_custom_forms_for_regular_users' ) !== 'On' ) )
			$booking_form_configuration = apply_bk_filter( 'wpbc_multiuser_get_booking_form_fields_configuration_of_regular_user', $booking_form_configuration, $resource_id, $form_name );    // FixIn: 8.1.3.19.
		}
	}

	// Language
	$booking_form_configuration = wpbc_lang( $booking_form_configuration );

	return $booking_form_configuration;
}


/**
 * Load BFB preview payload from transient (for booking created inside preview iframe).
 *
 * @param array $ctx
 * @return array|null
 */
function wpbc_bfb_preview__maybe_get_payload_from_context( $ctx ) {

	$ctx = ( is_array( $ctx ) ) ? $ctx : array();

	$user_id = isset( $ctx['user_id'] ) ? absint( $ctx['user_id'] ) : 0;
	$form_id = isset( $ctx['wpbc_bfb_preview_form_id'] ) ? absint( $ctx['wpbc_bfb_preview_form_id'] ) : 0;
	$token   = isset( $ctx['wpbc_bfb_preview_token'] ) ? sanitize_key( $ctx['wpbc_bfb_preview_token'] ) : '';
	$nonce   = isset( $ctx['wpbc_bfb_preview_nonce'] ) ? sanitize_text_field( $ctx['wpbc_bfb_preview_nonce'] ) : '';

	if ( $user_id <= 0 || $form_id <= 0 || '' === $token ) {
		return null;
	}

	// Optional hardening: verify nonce if present.
	if ( '' !== $nonce ) {
		if ( ! wp_verify_nonce( $nonce, 'wpbc_bfb_preview_' . $token ) ) {
			return null;
		}
	}

	// Optional hardening: capability check (preview is admin-only anyway).
	$cap = ( function_exists( 'wpbc_bfb_get_manage_cap' ) ) ? wpbc_bfb_get_manage_cap() : 'manage_options';
	if ( ! is_user_logged_in() || ! current_user_can( $cap ) ) {
		return null;
	}

	$transient_key = 'wpbc_bfb_preview_' . $user_id . '_' . $form_id . '_' . $token;

	$data = get_transient( $transient_key );
	if ( empty( $data ) || ! is_array( $data ) ) {
		return null;
	}

	return $data;
}


/**
 * Get configuration  of 'BOOKING FORM DATA'  from  -  Booking > Settings > Form page
 *
 *        - 1.    <= BS : 'Booking form show' configuration    from standard form in versions up to  Business Small version ,
 *        - 2     >= BM : If form data has field of custom form, then from custom form configuration,
 *        - 3     >= BM : Otherwise if resource has default custom  booking form,  then  from  this default custom  booking form
 *        - 4      = MU :  specific form of specific WP User
 *        - 5   finally : simple standard form
 *
 * @param int $resource_id
 * @param string $form_data		Form  data here, required in >= BM, because such  form_data can contain fields about used custom booking form
 *
 * @return string
 */
/**
 * Get configuration  of 'BOOKING FORM DATA'  from  -  Booking > Settings > Form page
 *
 *        - 1.    <= BS : 'Booking form show' configuration    from standard form in versions up to  Business Small version ,
 *        - 2     >= BM : If form data has field of custom form, then from custom form configuration,
 *        - 3     >= BM : Otherwise if resource has default custom  booking form,  then  from  this default custom  booking form
 *        - 4      = MU :  specific form of specific WP User
 *        - 5   finally : simple standard form
 *
 * @param int    $resource_id
 * @param string $form_data  Form data here, required in >= BM.
 *
 * @return string
 */
function wpbc_get__booking_form_data_configuration( $resource_id = 1, $form_data = '', $params = array() ) {

	// FixIn: 2026-02-05 - allow preview booking submissions to use preview "content_form".
	$ctx = wp_parse_args( ( is_array( $params ) ? $params : array() ), wpbc_get_request_form_context() );

	$form_status = ( isset( $ctx['form_status'] ) ) ? sanitize_key( $ctx['form_status'] ) : 'published';

	if ( 'preview' === $form_status ) {

		$payload = wpbc_bfb_preview__maybe_get_payload_from_context( $ctx );

		// content_form is exactly the "Booking form show" template exported for preview session.
		if ( ! empty( $payload ) && isset( $payload['content_form'] ) && ( '' !== (string) $payload['content_form'] ) ) {
			return (string) $payload['content_form'];
		}
	}

	$resource_id          = (int) $resource_id;
	$form_data            = (string) $form_data;
	$my_booking_form_name = 'standard';

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {

		$booking_form_show = wpbc_simple_form__get_form_show__as_shortcodes();
		$booking_form_show = wpbc_bf__replace_custom_html_shortcodes( $booking_form_show );

		// Try BFB (if available) even in this branch (safe no-op if not loaded).
		$booking_form_show = WPBC_BFB_Booking_Data_Content_Resolver::maybe_override_booking_form_show_by_bfb( $booking_form_show, $resource_id, $my_booking_form_name, $form_data, $ctx );

	} else {

		$booking_form_show = get_bk_option( 'booking_form_show' );
		$booking_form_show = wpbc_bf__replace_custom_html_shortcodes( $booking_form_show );

		if ( class_exists( 'wpdev_bk_biz_m' ) ) {

			if ( false !== strpos( $form_data, 'wpbc_custom_booking_form' . $resource_id . '^' ) ) { // FixIn: 9.4.3.12.

				$custom_booking_form_name = substr(
					$form_data,
					strpos( $form_data, 'wpbc_custom_booking_form' . $resource_id . '^' ) + strlen( 'wpbc_custom_booking_form' . $resource_id . '^' )
				);

				if ( false !== strpos( $custom_booking_form_name, '~' ) ) {
					$custom_booking_form_name = substr( $custom_booking_form_name, 0, strpos( $custom_booking_form_name, '~' ) );
				}

				$booking_form_show    = apply_bk_filter( 'wpdev_get_booking_form_content', $booking_form_show, $custom_booking_form_name );
				$my_booking_form_name = $custom_booking_form_name;

			} else {

				// BM :: Get default Custom Form of Resource.
				$my_booking_form_name = apply_bk_filter( 'wpbc_get_default_custom_form', 'standard', $resource_id );
				if ( ( $my_booking_form_name != 'standard' ) && ( ! empty( $my_booking_form_name ) ) ) {
					$booking_form_show = apply_bk_filter( 'wpdev_get_booking_form_content', $booking_form_show, $my_booking_form_name );
				}
			}

			// MU :: if resource of "Regular User" - then GET STANDARD user form.
			$booking_form_show = apply_bk_filter( 'wpbc_multiuser_get_booking_form_show_of_regular_user', $booking_form_show, $resource_id, $my_booking_form_name ); // FixIn: 8.1.3.19.

			/**
			 * IMPORTANT:
			 * Try BFB DB "content_form" (resolver-driven) BEFORE MU regular-user override,
			 * so MU filter can still enforce standard form when required.
			 */
			$booking_form_show = WPBC_BFB_Booking_Data_Content_Resolver::maybe_override_booking_form_show_by_bfb( $booking_form_show, $resource_id, $my_booking_form_name, $form_data, $ctx );

		} else {

			// Try BFB also for non-BM paid branches (safe).
			$booking_form_show = WPBC_BFB_Booking_Data_Content_Resolver::maybe_override_booking_form_show_by_bfb( $booking_form_show, $resource_id, $my_booking_form_name, $form_data, $ctx );
		}
	}

	// Language.
	$booking_form_show = wpbc_lang( $booking_form_show );

	return $booking_form_show;
}


// -------------------------------------------------------------------------------------------------------------

/**
 * Replace resource_ID of booking  in 'form_data'
 * Useful, when we need to save booking from one resource into another.
 *
 * @param $booking__form_data__str		'selectbox-multiple^rangetime2[]^18:00 - 20:00~checkbox^fee2[]^true~text^name2^John~text^secondname2^Smith...'
 * @param $new_resource_id				10
 * @param $old_resource_id				2
 *
 * @return string						'selectbox-multiple^rangetime10[]^18:00 - 20:00~checkbox^fee10[]^true~text^name10^John~text^secondname10^Smith...'
 */
function wpbc_get__form_data__with_replaced_id( $booking__form_data__str, $new_resource_id, $old_resource_id ) {

	$all_booking_data_arr = wpbc_get_parsed_booking_data_arr( $booking__form_data__str, $old_resource_id );

	$new__form_data__str  = wpbc_encode_booking_data_to_string( $all_booking_data_arr, $new_resource_id );

	return $new__form_data__str;
}

/**
 * Get booking form pair (fields + show template) for listing/inspection purposes,
 * but allow BFB resolver to override legacy options.
 *
 * @param int    $resource_id
 * @param string $form_name
 * @param array  $ctx
 *
 * @return array { form:string, content:string }
 */
function wpbc_get__booking_form_pair__for_field_names_listing( $resource_id = 1, $form_name = 'standard', $ctx = array() ) {

	$pair = array(
		'form'    => wpbc_bf__replace_custom_html_shortcodes( get_bk_option( 'booking_form' ) ),
		'content' => wpbc_bf__replace_custom_html_shortcodes( get_bk_option( 'booking_form_show' ) ),
	);

	// Let BFB override both "form" and "content" if available.
	if ( class_exists( 'WPBC_BFB_Booking_Data_Content_Resolver' ) ) {

		// Recommended: add this method to the resolver (see section 3 below).
		if ( method_exists( 'WPBC_BFB_Booking_Data_Content_Resolver', 'maybe_override_booking_form_pair_by_bfb' ) ) {
			$pair = WPBC_BFB_Booking_Data_Content_Resolver::maybe_override_booking_form_pair_by_bfb(
				$pair,
				(int) $resource_id,
				(string) $form_name,
				'',       // no form_data here
				(is_array( $ctx ) ? $ctx : array())
			);
		} else {
			// Fallback: at least keep content resolved by BFB (you already have this method).
			if ( method_exists( 'WPBC_BFB_Booking_Data_Content_Resolver', 'maybe_override_booking_form_show_by_bfb' ) ) {
				$pair['content'] = WPBC_BFB_Booking_Data_Content_Resolver::maybe_override_booking_form_show_by_bfb(
					$pair['content'],
					(int) $resource_id,
					(string) $form_name,
					'',
					(is_array( $ctx ) ? $ctx : array())
				);
			}
		}
	}

	// Normalize keys.
	if ( ! isset( $pair['form'] ) ) {
		$pair['form'] = '';
	}
	if ( ! isset( $pair['content'] ) ) {
		$pair['content'] = '';
	}

	$pair['form']    = (string) $pair['form'];
	$pair['content'] = (string) $pair['content'];

	return $pair;
}

/**
 * Get arr   of all Fields Names 	from  all booking forms  (including custom)
 *
 * @return array   = [
						 0: [  name = "standard",  num =  8,  listing = [ ... ]    ],
						 1: [
							  name = "minimal"
							  num = 7
							  listing = [
										   labels = [
														0 = " adults"
														1 = " children"
														2 = " infants"
														3 = " gender"
														4 = " full_name"
														5 = " email"
														6 = " phone"
										   fields = {array[7]}
														0 = " adults"
														1 = " children"
														2 = " infants"
														3 = " gender"
														4 = " full_name"
														5 = " email"
														6 = " phone"
										   fields_type = {array[7]}
														0 = "select"
														1 = "select"
														2 = "select"
														3 = "radio"
														4 = "text"
														5 = "email"
														6 = "text"
										]
							]
						 2: [], ...
 * 					 ]
 */
function wpbc_get__in_all_forms__field_names_arr() {

	$booking_form_fields_arr   = array();
	$ctx = wpbc_get_request_form_context();

	$ctx['form_status'] = 'published';

	$pair = wpbc_get__booking_form_pair__for_field_names_listing( 1, 'standard', $ctx );

	$booking_form_fields_arr[] = array(
		'name'    => 'standard',
		'form'    => $pair['form'],
		'content' => $pair['content'],
	);

	/**
	 * Get custom booking form configurations: [
	 *                                            [ name = "minimal",
	 *                                              form = "[calendar]...",
	 *                                              content = "<div class="payment-content-form"> [name] ..."
	 * 											  ],
	 * 											  ...
	 * 										   ]
	 */
	$is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
	if ( ( $is_can ) || ( get_bk_option( 'booking_is_custom_forms_for_regular_users' ) === 'On' ) ) {
		$booking_forms_extended = get_bk_option( 'booking_forms_extended' );
		$booking_forms_extended = maybe_unserialize( $booking_forms_extended );
		if (  false !== $booking_forms_extended ) {
			foreach ( $booking_forms_extended as $form_extended ) {
				$booking_form_fields_arr[] = $form_extended;
			}
		}
	}

	foreach ( $booking_form_fields_arr as $form_key => $booking_form_element ) {

		$booking_form = $booking_form_element['form'];

		$types = 'text[*]?|email[*]?|time[*]?|textarea[*]?|select[*]?|selectbox[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|file[*]?|quiz';
		$regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
		$regex2 = '%\[\s*(country[*]?|starttime[*]?|endtime[*]?)(\s*[a-zA-Z]*[0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
		$fields_count = preg_match_all($regex, $booking_form, $fields_matches) ;
		$fields_count2 = preg_match_all($regex2, $booking_form, $fields_matches2) ;

		//Gathering Together 2 arrays $fields_matches  and $fields_matches2
		foreach ($fields_matches2 as $key => $value) {
			if ($key == 2) $value = $fields_matches2[1];
			foreach ($value as $v) {
				$fields_matches[$key][count($fields_matches[$key])]  = $v;
			}
		}
		$fields_count += $fields_count2;

		$booking_form_fields_arr[ $form_key ]['num']     = $fields_count;
		$booking_form_fields_arr[ $form_key ]['listing'] = array();                //$fields_matches;

		$fields_matches[1] = array_map( 'trim', $fields_matches[1] );
		$fields_matches[2] = array_map( 'trim', $fields_matches[2] );

		$booking_form_fields_arr[ $form_key ]['listing']['labels'] = array_map( 'ucfirst', $fields_matches[2] );
		$booking_form_fields_arr[ $form_key ]['listing']['fields'] = $fields_matches[2] ;

		foreach ( $fields_matches[1] as $key_fm => $value_fm ) {
			$fields_matches[1][ $key_fm ] = trim( str_replace( '*', '', $value_fm ) );
		}

		$booking_form_fields_arr[ $form_key ]['listing']['fields_type'] = $fields_matches[1];

		// Reset
		unset( $booking_form_fields_arr[ $form_key ]['form'] );
		unset( $booking_form_fields_arr[ $form_key ]['content'] );
	}

	return $booking_form_fields_arr;
}

// -------------------------------------------------------------------------------------------------------------

/**
 * Get arr with booking form fields values,  after parsing form_data. 		Avoid to  use this function  in a future.
 *
 * @param $formdata
 * @param $bktype
 * @param $booking_form_show
 * @param $extended_params
 *
 * @return array
 */
function wpbc__legacy__get_form_content_arr ( $formdata, $bktype =-1, $booking_form_show ='', $extended_params = array() ) {

	if ( $bktype == -1 ) {
		$bktype = ( function_exists( 'get__default_type' ) ) ? get__default_type() : 1;
	}

	if ( $booking_form_show === '' ) {

		$booking_form_show = wpbc_get__booking_form_data_configuration( $bktype, $formdata );
	}

	$formdata_array = explode('~',$formdata);
	$formdata_array_count = count($formdata_array);
	$email_adress='';
	$name_of_person = '';
	$coupon_code = '';
	$secondname_of_person = '';
	$visitors_count = 1;
	$select_box_selected_items = array();
	$check_box_selected_items = array();
	$all_fields_array = array();
	$all_fields_array_without_types = array();
	$checkbox_value=array();

	for ( $i=0 ; $i < $formdata_array_count ; $i++) {

		if ( empty( $formdata_array[$i] ) ) {
			continue;
		}

		$elemnts = explode('^',$formdata_array[$i]);

		$type = $elemnts[0];
		$element_name = $elemnts[1];
		$value = $elemnts[2];

		//FixIn: 9.7.4.1	-	escape coded html/xss
		$value = esc_html( $value );
		$value = esc_html( html_entity_decode( $value ) );
		$value = nl2br($value);                                             // Add BR instead if /n elements		// FixIn: 9.7.4.2.
				// Escaping for timeline popovers and for other places
		$value = wpbc_string__escape__then_convert__n_amp__html( $value );

		$count_pos = strlen( $bktype );

		$type_name = $elemnts[1];
		$type_name = str_replace('[]','',$type_name);
		if ($bktype == substr( $type_name,  -1*$count_pos ) ) $type_name = substr( $type_name, 0, -1*$count_pos ); // $type_name = str_replace($bktype,'',$elemnts[1]);

		if ( ( ($type_name == 'email') || ($type == 'email')  ) && ( empty($email_adress) )   )    $email_adress = $value;  // FixIn: 6.0.1.9.
		if ( ($type_name == 'coupon') || ($type == 'coupon')  )             $coupon_code = $value;
		if ( ($type_name == 'name') || ($type == 'name')  )                 $name_of_person = $value;
		if ( ($type_name == 'secondname') || ($type == 'secondname')  )     $secondname_of_person = $value;
		if ( ($type_name == 'visitors') || ($type == 'visitors')  )         $visitors_count = $value;


		if ($type == 'checkbox') {
			if ($value == 'true') {
				$value = strtolower( __( 'yes', 'booking' ) );
			}

			if ($value == 'false') {
				$value = strtolower( __( 'no', 'booking' ) );
			}

			if  ( $value !='' )
				if ( ( isset($checkbox_value[ str_replace('[]','',(string) $element_name) ]) ) && ( is_array($checkbox_value[ str_replace('[]','',(string) $element_name) ]) ) ) {
					$checkbox_value[ str_replace('[]','',(string) $element_name) ][] = $value;
				} else {
					$checkbox_value[ str_replace('[]','',(string) $element_name) ] = array($value);
				}

			$value = '['. $type_name .']';                                  // FixIn: 6.1.1.14.
		}

		if ( ( $type == 'select-one') || ( $type == 'selectbox-one') || ( $type == 'select-multiple' ) || ( $type == 'selectbox-multiple' )  || ( $type == 'radio' ) ) { // add all select box selected items to return array
			$select_box_selected_items[$type_name] = $value;
		}

		if ( ($type == 'checkbox') && (isset($checkbox_value)) ) {
			if (isset(  $checkbox_value[ str_replace('[]','',(string) $element_name) ] )) {
				if (is_array(  $checkbox_value[ str_replace('[]','',(string) $element_name) ] ))
					$current_checkbox_value = implode(', ', $checkbox_value[ str_replace('[]','',(string) $element_name) ] );
				else
					$current_checkbox_value = $checkbox_value[ str_replace('[]','',(string) $element_name) ] ;
			} else {
				$current_checkbox_value = '';
			}
			$all_fields_array[ str_replace('[]','',(string) $element_name) ] = $current_checkbox_value;
			$all_fields_array_without_types[ substr(   str_replace('[]','',(string) $element_name), 0 , -1*strlen( $bktype ) )  ] = $current_checkbox_value;

			$check_box_selected_items[$type_name] = $current_checkbox_value;
		} else {

			// FixIn: 8.4.2.11.
			$all_fields_array_without_types[ substr(   str_replace('[]','',(string) $element_name), 0 , -1*strlen( $bktype ) )   ] = $value;
			/**
			   ['_all_']        => $all_fields_array,        CONVERT to  " AM/PM "
			   ['_all_fields_'] => $all_fields_array_without_types => in " 24 hour " format - for ability correct  calculate Booking > Resources > Advanced cost page.
			 */
			if ( ( $type_name == 'rangetime' ) || ( $type == 'rangetime' ) ) {
				$value = wpbc_time_slot_in_format(  $value );
			}
			$all_fields_array[ str_replace('[]','',(string) $element_name) ] = $value;
			// FixIn: 8.4.2.11.

		}
		$is_skip_replace = false;                                           // FixIn: 7.0.1.45.
		if ( ( $type == 'radio' ) && empty( $value ) )
				$is_skip_replace = true;
		if ( ! $is_skip_replace ) {
			$booking_form_show = str_replace( '[' . $type_name . ']', $value, $booking_form_show );
		}
	}

	if (! isset($all_fields_array_without_types[ 'booking_resource_id'  ])) $all_fields_array_without_types[ 'booking_resource_id'  ] = $bktype;
	if (! isset($all_fields_array_without_types[ 'resource_id'  ]))         $all_fields_array_without_types[ 'resource_id'  ] = $bktype;
	if (! isset($all_fields_array_without_types[ 'type_id'  ]))             $all_fields_array_without_types[ 'type_id'  ] = $bktype;

	if (! isset($all_fields_array_without_types[ 'type'  ]))                $all_fields_array_without_types[ 'type'  ] = $bktype;
	if (! isset($all_fields_array_without_types[ 'resource'  ]))            $all_fields_array_without_types[ 'resource'  ] = $bktype;

	foreach ($extended_params as $key_param=>$value_param) {
		if (! isset($all_fields_array_without_types[  $key_param  ]))            $all_fields_array_without_types[ $key_param  ] = $value_param;
	}

	foreach ( $all_fields_array_without_types as $key_param=>$value_param) {                                  // FixIn: 6.1.1.4.
		if (   ( gettype ( $value_param ) != 'array' )
			&& ( gettype ( $value_param ) != 'object' )
			) {
			$booking_form_show = str_replace( '['. $key_param .']', $value_param ,$booking_form_show);

			$all_fields_array_without_types[ $key_param ] = str_replace( "&amp;", '&', $value_param );					// FixIn: 7.1.2.12.
		}


	}
	// Remove all shortcodes, which is not replaced early.
	$booking_form_show = preg_replace ('/[\s]{0,}\[[a-zA-Z0-9.,-_]{0,}\][\s]{0,}/', '', $booking_form_show);  // FixIn: 6.1.1.4.

	$booking_form_show = str_replace( "&amp;", '&', $booking_form_show );											// FixIn: 7.1.2.12.

	$return_array = array(
							'content'      => $booking_form_show,
							'email'        => $email_adress,
							'name'         => $name_of_person,
							'secondname'   => $secondname_of_person,
							'visitors'     => $visitors_count,
							'coupon'       => $coupon_code,
							'_all_'        => $all_fields_array,
							'_all_fields_' => $all_fields_array_without_types
						);

	foreach ( $select_box_selected_items as $key => $value ) {
		if ( ! isset( $return_array[ $key ] ) ) {
			$return_array[ $key ] = $value;
		}
	}
	foreach ( $check_box_selected_items as $key => $value ) {
		if ( ! isset( $return_array[ $key ] ) ) {
			$return_array[ $key ] = $value;
		}
	}

	return $return_array;
}


/**
 * Check whether custom booking forms are enabled for the current user.
 *
 * This function determines if the current user is allowed to access custom booking forms
 * based on plugin version and user permissions. It checks:
 *   - If the business version of the plugin is active (`wpdev_bk_biz_m` class exists).
 *   - If the user is a super admin (via `multiuser_is_user_can_be_here` filter).
 *   - If regular users are allowed to use custom forms (via `booking_is_custom_forms_for_regular_users` option).
 *
 * @return bool True if custom forms are enabled for the current user; false otherwise.
 */
function wpbc_is_custom_forms_enabled() {

	if ( ! class_exists( 'wpdev_bk_biz_m' ) ) {
		return false;
	}

	$is_super_admin       = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
	$custom_forms_allowed = ( 'On' === get_bk_option( 'booking_is_custom_forms_for_regular_users' ) );

	return $is_super_admin || $custom_forms_allowed;
}