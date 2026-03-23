<?php /**
 * @version 1.0
 * @description AJX_Bookings
 * @category  AJX_Bookings Actions
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-06-10
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Main Ajax handler
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Ajax Actions - Get Listing Data and Response to JS script
 */
function wpbc_ajax_WPBC_AJX_BOOKING_ACTIONS() {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( ! isset( $_POST['action_params'] ) || empty( $_POST['action_params'] ) ) {
		exit;
	}

	$ajax_errors = new WPBC_AJAX_ERROR_CATCHING();

	// Security  -----------------------------------------------------------------------------------------------    	// in Ajax Post:   'nonce': wpbc_ajx_booking_listing.get_secure_param( 'nonce' ),
	$action_name    = 'wpbc_ajx_booking_listing_ajx' . '_wpbcnonce';
	$nonce_post_key = 'nonce';
	$result_check   = check_ajax_referer( $action_name, $nonce_post_key );

	$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) )  ?  intval( $_REQUEST['wpbc_ajx_user_id'] )  :  wpbc_get_current_user_id();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

	// Get clean Parameters for SQL  ---------------------------------------------------------------------------
	$request_rules_structure = array(
						'booking_action' =>  array( 'validate' => array(
																			'set_booking_locale',
																			'set_booking_pending' ,
																			'set_booking_approved',
																			'move_booking_to_trash',
																			'restore_booking_from_trash',
																			'delete_booking_completely',
																			'set_booking_as_read',
																			'set_booking_as_unread',
																			'empty_trash',
																			'set_booking_note',
																			'change_booking_resource',
																			'duplicate_booking_to_other_resource',
																			'set_payment_status',
																			'set_booking_cost',
																			'send_payment_request',
																			'import_google_calendar',
																			'export_csv',
																			'feedback_01'

														), 'default' => '' ),
						'booking_id'              => array( 'validate' => 'digit_or_csd', 'default' => 0 ),
						'selected_resource_id'    => array( 'validate' => 'd', 'default' => 0 ),
						'booking_meta_locale'     => array( 'validate' => 's', 'default' => '' ),
						'reason_of_action'        => array( 'validate' => 's', 'default' => '' ),
						'remark'                  => array( 'validate' => 's', 'default' => '' ),
						'ui_clicked_element_id'   => array( 'validate' => 's', 'default' => '' ),
						'selected_payment_status' => array( 'validate' => 's', 'default' => '' ),
						'booking_cost' 			  => array( 'validate' => 's', 'default' => '' ),

						'export_type' 			  => array( 'validate' => array( 'csv_all', 'csv_page' ), 'default' => 'csv_page' ),
						'csv_export_separator'    => array( 'validate' => array( 'semicolon', 'comma' ), 'default' => ';' ),
						'csv_export_skip_fields'  => array( 'validate' => 's', 'default' => '' ),

						'booking_gcal_events_from'              => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_from_offset'       => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_from_offset_type'  => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_until'             => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_until_offset'      => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_until_offset_type' => array( 'validate' => 's', 'default' => '' ),
						'booking_gcal_events_max'               => array( 'validate' => 'd', 'default' => 25 ),
						'booking_gcal_resource'                 => array( 'validate' => 's', 'default' => '' ),

						'feedback__note'                  => array( 'validate' => 's', 'default' => '' ),
						'feedback_stars'                  => array( 'validate' => 'd', 'default' => 0 )
				);

	$user_request = new WPBC_AJX__REQUEST( array(
											   'db_option_name'          => 'booking_listing_request_params',
											   'user_id'                 => wpbc_get_current_user_id(),
											   'request_rules_structure' => $request_rules_structure
											)
					);
	$request_prefix = 'action_params';
	$request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 			// NOT Direct: 	$_REQUEST['action_params']['resource_id']





	$action_result = array();

	switch ( $request_params['booking_action'] ) {

	    case 'set_booking_locale':
			$action_result = wpbc_booking_do_action__set_booking_locale( $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'booking_locale' => $request_params['booking_meta_locale']
																			  )
																		);
	        break;

	    case 'set_booking_pending':        																				// Pending
		    $action_result = wpbc_booking_do_action__set_booking_approved_or_pending(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'reason_of_action' => $request_params['reason_of_action'],
																				'is_approve' 	   => '0'
																			  )
																		);
	        break;

	    case 'set_booking_approved':																					// Approve
		    $action_result = wpbc_booking_do_action__set_booking_approved_or_pending(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'reason_of_action' => $request_params['reason_of_action'],
																				'is_approve' 	   => '1'
																			  )
																		);
	        break;

	    case 'move_booking_to_trash':																					// Approve
		    $action_result = wpbc_booking_do_action__trash_booking_or_restore(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'reason_of_action' => $request_params['reason_of_action'],
																				'is_trash' 		   => '1'
																			  )
																		);
	        break;

		case 'restore_booking_from_trash':
		    $action_result = wpbc_booking_do_action__trash_booking_or_restore(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'reason_of_action' => $request_params['reason_of_action'],
																				'is_trash' 		   => '0'
																			  )
																		);
	        break;

		case 'delete_booking_completely':
		    $action_result = wpbc_booking_do_action__delete_booking_completely(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'reason_of_action' => $request_params['reason_of_action'],
																			  )
																		);
	        break;

		case 'set_booking_as_read':
		    $action_result = wpbc_booking_do_action__set_booking_as_read_unread(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'is_new' 		   => '0'
																			  )
																		);
	        break;

		case 'set_booking_as_unread':
		    $action_result = wpbc_booking_do_action__set_booking_as_read_unread(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'is_new' 		   => '1'
																			  )
																		);
	        break;

		case 'empty_trash':
		    $action_result = wpbc_booking_do_action__empty_trash( array(
																	'user_id'          => $user_id
															    ) );
	        break;

		case 'set_booking_note':
		    $action_result = wpbc_booking_do_action__set_booking_note(    $request_params['booking_id']
																			, array(
																				'user_id' => $user_id,
																				'remark'  => $request_params['remark'],
																			  )
																		);
			break;

	    case 'change_booking_resource':        																				// Pending
		    $action_result = wpbc_booking_do_action__change_booking_resource( $request_params['booking_id']
																			, $request_params['selected_resource_id']
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
	        break;
		case 'duplicate_booking_to_other_resource':
		    $action_result = wpbc_booking_do_action__duplicate_booking_to_other_resource( $request_params['booking_id']
																			, $request_params['selected_resource_id']
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
	        break;
		case 'set_payment_status':
		    $action_result = wpbc_booking_do_action__set_payment_status( $request_params['booking_id']
																			, $request_params['selected_payment_status']
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
	        break;
		case 'set_booking_cost':
		    $action_result = wpbc_booking_do_action__set_booking_cost( $request_params['booking_id']
																			, $request_params['booking_cost']
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
	        break;
		case 'send_payment_request':
		    $action_result = wpbc_booking_do_action__send_payment_request( $request_params['booking_id']
																			, $request_params['reason_of_action']
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
	        break;

		case 'import_google_calendar':
		    $action_result = wpbc_booking_do_action__import_google_calendar(  $request_params
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
			break;

		case 'export_csv':

		    $action_result = wpbc_booking_do_action__export_csv(  $request_params
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
			break;

		case 'feedback_01':

		    $action_result = wpbc_booking_do_action__feedback_01(  $request_params
																			, array(
																				'user_id'          => $user_id
																			  )
																		);
			break;

	    default:
	}


	$defaults = array(
					    'new_listing_params'   => false		// required for Import Google Calendar bookings
                  	  , 'after_action_result'  => false
					  /* translators: 1: ... */
					  , 'after_action_message' => sprintf( __( 'No actions %s has been processed.', 'booking')
														 , ' <strong>' . $request_params['booking_action'] . '</strong> ' )
			);
	$action_result = wp_parse_args( $action_result, $defaults );

	// Check if there were some errors  --------------------------------------------------------------------------------
	$error_messages = $ajax_errors->get_error_messages();
	if ( ! empty( $error_messages ) ) {
		$action_result['after_action_message'] .= $error_messages;
	}

	// Hook for other integrations
	do_action( 'wpbc_' . $request_params['booking_action'], $request_params, $action_result );							// FixIn: 9.5.3.3.

	//------------------------------------------------------------------------------------------------------------------
	// Send JSON. Its will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) )		Pass JS OBJ: response_data in "jQuery.post( " function on success.
	wp_send_json( array(
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'ajx_action_params'                      => $_REQUEST['action_params'], // Do not clean input parameters.
		'ajx_cleaned_params'                     => $request_params, // Cleaned input parameters.
		'ajx_after_action_message'               => $action_result['after_action_message'], // Message to  show.
		'ajx_after_action_result'                => (int) $action_result['after_action_result'], // Result key 0 | 1.
		'ajx_after_action_result_all_params_arr' => $action_result, // All result parameters.
	) );
}


// <editor-fold     defaultstate="collapsed"                        desc="  ==  Ajax Actions for bookings  ==  "  >

	/**
	 * Action: set booking as Locale
	 *
	 * @param int $booking_id				ID of booking			10
	 * @param $params
	 *
	 * @return array
	 */
	function wpbc_booking_do_action__set_booking_locale( $booking_id, $params ){

		$booking_locale = $params['booking_locale'];

		// LOG ---------------------------------------------------------------------------------------------------------
		$curr_user = get_user_by( 'id', (int) $params['user_id'] );
		wpbc_db__add_log_info( 		$booking_id,
								/* translators: 1: ... */
								sprintf( __( 'Booking locale changed to %s by:', 'booking' ), $booking_locale )
								. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
						 );

		// Action  ---------------------------------------------------------------------------------------------------------
		$option_arr = array( 'booking_meta_locale' => $booking_locale );

		$result = wpbc_save_booking_meta_option( $booking_id, $option_arr );

		if ( $result ) {
			$after_action_result  = true;
			$after_action_message = ( ( false === strpos( $booking_id, ',' ) )
										/* translators: 1: ... */
										? sprintf( __( 'Booking has been changed locale to %s', 'booking' ), "<strong>{$booking_locale}</strong>" )
										/* translators: 1: ... */
										: sprintf( __( 'Bookings have been changed locale to %s', 'booking' ), "<strong>{$booking_locale}</strong>" )
									)
									. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id . '</strong> )</span>';
		} else {
			$after_action_result  = false;
			$after_action_message = ( ( false === strpos( $booking_id, ',' ) )
										/* translators: 1: ... */
										? sprintf( __( 'Booking has NOT been changed locale to %s', 'booking' ), "<strong>{$booking_locale}</strong>" )
										/* translators: 1: ... */
										: sprintf( __( 'Bookings have NOT been changed locale to %s', 'booking' ), "<strong>{$booking_locale}</strong>" )
									)
									. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id . '</strong> )</span>';
		}

		return array(
					'after_action_result' => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action: Get Google Calendar Link
	 *
	 * @param $booking_data					array(
														'form_data'   => array( ... )
														'form_show'   => ....
														'dates_short' => array( ... )
												)
	 *
	 * @return string	URL  of encoded link for adding to  Google Calendar
	 */
	function wpbc_booking_do_action__get_google_calendar_link( $booking_data ){

		$params = array();
		$params['timezone'] = get_bk_option('booking_gcal_timezone');

		$booking_gcal_events_form_fields = maybe_unserialize( get_bk_option( 'booking_gcal_events_form_fields') );			// array( [title]=>text^name,  [description]=>textarea^details,  [where]=>text^	)

		// Fields ----------------------------------------------------------------------------------------------------------
		$fields = array( 'title' => '', 'description' => '', 'where' => '' );

		foreach ( $fields as $key_name => $field_value ) {

			if ( ! empty( $booking_gcal_events_form_fields[ $key_name ] ) ) {

				$field_name = explode( '^', $booking_gcal_events_form_fields[ $key_name ] );

				$field_name = $field_name[ ( count( $field_name ) - 1 ) ];                                                  // FixIn: 8.7.7.6.

				if ( 'description' === $key_name ) {                                                                	// FixIn: 8.1.3.2.

					if ( isset( $booking_data['form_show'] ) ) {                                                    	// FixIn: 8.7.3.14.
																															// FixIn: 8.7.11.4.
							$fields[ $key_name ] = $booking_data['form_show'];
							$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
							$fields[ $key_name ] = urlencode($fields[ $key_name ]);
							$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
							$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
					}

				} else if ( ( ! empty( $field_name ) )
					 && ( ! empty( $booking_data['form_data'] ) )
					 && ( ! empty( $booking_data['form_data'][ $field_name ] ) )
				) {
					// FixIn: 8.7.11.4.
					$fields[ $key_name ] = $booking_data['form_data'][ $field_name ];
					$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
					/**
					 *  // Convert here from  usual  symbols to URL symbols https://www.url-encode-decode.com/
						$fields[ $key_name ] = str_replace(    array( '%','#', '+', '&' )
															 , array( '%25','%23', '%2B', '%26')
															 , $fields[ $key_name ]
												);
					 */
					$fields[ $key_name ] = urlencode($fields[ $key_name ]);
					$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
					$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
				}
			}
		}

		// Dates -----------------------------------------------------------------------------------------------------------
		$check_in_timestamp = $check_out_timestamp = '';
		if ( ! empty( $booking_data[ 'dates_short' ] ) ) {

			/**
			 * All day events, you can use 20161208/20161209 - note that the old google documentation gets it wrong.
			 * You must use the following date as the end date for a one day all day event,
			 * or +1 day to whatever you want the end date to be.
			 */

			$check_in_timestamp  = strtotime( $booking_data[ 'dates_short' ][ 0 ], current_time( 'timestamp' ) );
			$check_out_timestamp = strtotime( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], current_time( 'timestamp' ) );

			if ( trim( substr( $booking_data[ 'dates_short' ][ 0 ], 11 ) ) == '00:00:00' ) {
				if ( trim( substr( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], 11 ) ) == '00:00:00' ) {
					$check_in_timestamp = gmdate( "Ymd", $check_in_timestamp );                			// All day
				} else {
					$check_in_timestamp = gmdate( "Ymd\T000000", $check_in_timestamp );                	// All day starting on 00:00:00	// FixIn: 10.0.0.28.
				}
			} else {
				$check_in_timestamp = gmdate( "Ymd\THis", $check_in_timestamp );			//$check_in_timestamp = gmdate( "Ymd\THis\Z", $check_in_timestamp );
			}

			if ( trim( substr( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], 11 ) ) == '00:00:00' ) {
				$check_out_timestamp = strtotime( '+1 day', $check_out_timestamp );
				$check_out_timestamp = gmdate( "Ymd", $check_out_timestamp );				// All day
			} else {
				$check_out_timestamp = gmdate( "Ymd\THis", $check_out_timestamp );		//$check_out_timestamp = gmdate( "Ymd\THis\Z", $check_out_timestamp );
			}
		}

		// Link  -----------------------------------------------------------------------------------------------------------
		/**
		 * Convert an ISO date/time to a UNIX timestamp
		function iso_to_ts( $iso ) {
			sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );
			return mktime( $hour, $minute, $second, $month, $day, $year );
		 20140127T224000Z
		 gmdate("Ymd\THis\Z", time());
		*/

		/**
		 * 	action:		action=TEMPLATE
						A default required parameter.

	src:
		Example: src=default%40gmail.com
		Format: src=text
		This is not covered by Google help but is an optional parameter in order to add an event to a shared calendar rather than a user's default.

	text:
		Example: text=Garden%20Waste%20Collection
		Format: text=text
		This is a required parameter giving the event title.

	dates:
		Example: dates=20090621T063000Z/20090621T080000Z (i.e. an event on 21 June 2009 from 7.30am to 9.0am British Summer Time (=GMT+1)).
		Format: dates=YYYYMMDDToHHMMSSZ/YYYYMMDDToHHMMSSZ
		This required parameter gives the start and end dates and times (in Greenwich Mean Time) for the event.

	location:
		Example: location=Home
		Format: location=text
		The obvious location field.

	trp:
		Example: trp=false
		Format: trp=true/false
		Show event as busy (true) or available (false)

	sprop:
		Example: sprop=http%3A%2F%2Fwww.me.org
		Example: sprop=name:Home%20Page
		Format: sprop=website and/or sprop=name:website_name
		 */

		//$link_add2gcal  = 'http://www.google.com/calendar/event?action=TEMPLATE';
		//$link_add2gcal .= '&text=' . $fields['title'];
		$link_add2gcal = 'https://calendar.google.com/calendar/r/eventedit?';												// FixIn: 8.7.3.10.
		$link_add2gcal .= 'text=' . $fields['title'];																		// FixIn: 8.7.11.4.
		$link_add2gcal .= '&dates=' . $check_in_timestamp . '/' . $check_out_timestamp;					//$link_add2gcal .= '&dates=[start-custom format='Ymd\\THi00\\Z']/[end-custom format='Ymd\\THi00\\Z']';
		$link_add2gcal .= '&details='  . ( ( 'On' !== get_bk_option( 'booking_g_cal_export_no_data' ) ) ? $fields['description'] : '' );    //FixIn: 10.3.0.1                                                             // FixIn: 8.7.11.4.
		$link_add2gcal .= '&location=' . ( ( 'On' !== get_bk_option( 'booking_g_cal_export_no_data' ) ) ? $fields['where'] : '' );                                                                    // FixIn: 8.7.11.4.
		$link_add2gcal .= '&trp=false';
		if ( ! empty( $params['timezone'] ) ) {
			$link_add2gcal .= '&ctz=' . str_replace( '%', '%25', $params['timezone'] );                   					//FixIn: 8.7.3.10		//TimeZone
		}
		//$link_add2gcal .= '&sprop=';
		//$link_add2gcal .= '&sprop=name:';

		return $link_add2gcal;
	}

	/**
	 * Action: move booking to  Trash | Restore
	 *
	 * @param $booking_id_arr	array or int	of booking ID
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1, 'reason_of_action' => 'Because...', 'is_trash' => '1' )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__trash_booking_or_restore( $booking_id_arr, $params ){

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_is_csd = join( ',',  $booking_id_arr  );
		$booking_is_csd = wpbc_clean_digit_or_csd( $booking_is_csd );


		// Get reason of action --------------------------------------------------------------------------------------------
		$action_reason = $params['reason_of_action'];	// stripslashes( $params['reason_of_action'] ); // translate words like don\'t to don't

		// Get reason of action --------------------------------------------------------------------------------------------
		$is_trash_or_restore = $params['is_trash'];

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );


		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_is_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;
			if ( $is_trash_or_restore == '1' ) {
				$after_action_message = '<strong style="font-size: 1.4em;">' . count( explode( ',', $booking_is_csd ) ) .'</strong> '
										. ( ( false === strpos( $booking_is_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'Booking has been %1$s trashed %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been %1$s trashed %2$s', 'booking' ) , '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_is_csd . '</strong> )</span>';
			} else {
				$after_action_message = '<strong style="font-size: 1.4em;">' . count( explode( ',', $booking_is_csd ) ) .'</strong> '
										. ( ( false === strpos( $booking_is_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'Booking has been set as %1$s restored %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been set as %1$s restored %2$s', 'booking' ), '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_is_csd . '</strong> )</span>';
			}
			//    SQL    ---------------------------------------------------------------------------------------------------
			global $wpdb;
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$prepared_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET trash = %s WHERE booking_id IN ({$booking_is_csd})", $is_trash_or_restore );  // FixIn: 10.12.1.5.
			if ( $is_trash_or_restore == '1' ) {
				// Trash
				$my_trash_date = wpbc_datetime_localized( gmdate( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' );
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$prepared_sql  = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET  trash = %s, is_trash = %s WHERE booking_id IN ({$booking_is_csd})", $is_trash_or_restore, $my_trash_date );  // FixIn: 10.12.1.5.
			} else {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$prepared_sql  = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET  trash = %s, is_trash = NULL WHERE booking_id IN ({$booking_is_csd})", $is_trash_or_restore );  // FixIn: 10.12.1.5.
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

			// Update the Hash and Cost of the booking
			$bk_id_arr = explode(',', $booking_is_csd );																	// FixIn: 8.6.1.11.
			foreach ( $bk_id_arr as $bk_id ) {
				wpbc_hash__update_booking_hash( $bk_id );
			}

			// LOG ---------------------------------------------------------------------------------------------------------
			$curr_user = get_user_by( 'id', (int) $params['user_id'] );
			wpbc_db__add_log_info( 		explode( ',', $booking_is_csd ),
									( ( $is_trash_or_restore == '1' ) ? __( 'Trashed by:', 'booking' ) : __( 'Restored by:', 'booking' ) )
									. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
							 );

			// ::  ?  ::   Update 'is_new' status of bookings in Database
			// wpbc_db_update_number_new_bookings( explode( ',', $booking_is_csd ), '0', $params['user_id'] );

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__trash', $booking_is_csd, $is_trash_or_restore );                                 		// FixIn: 8.7.6.2.

			// Emails ------------------------------------------------------------------------------------------------------
			if ( ! empty( $is_send_emeils ) ) {

				if ( $is_trash_or_restore == '1' ) {
					wpbc_send_email_trash( $booking_is_csd, $is_send_emeils, $action_reason );
				} else {
					//wpbc_send_email_approved( $booking_is_csd, $is_send_emeils, $action_reason );
					//wpbc_send_email_deny( 	$booking_is_csd, $is_send_emeils, $action_reason );
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action: set booking as 	Approved | Pending
	 *
	 * @param $booking_id_arr	array or int	of booking ID
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1, 'reason_of_action' => 'Because...', 'is_approve' => '1' )
	 *
	 * @return array
	 *
	 * Example:
	 * 			Approved:
								$action_result = wpbc_booking_do_action__set_booking_approved_or_pending(    $request_params['booking_id']
																				 , array(
																					 'user_id'          => $user_id,
																					 'reason_of_action' => $request_params['reason_of_action'],
																					 'is_approve' => '1'
																				   )
																			 );
	 * 			Pending:
								$action_result = wpbc_booking_do_action__set_booking_approved_or_pending(    $request_params['booking_id']
																				 , array(
																					 'user_id'          => $user_id,
																					 'reason_of_action' => $request_params['reason_of_action'],
																					 'is_approve' => '0'
																				   )
																			 );
	 */
	function wpbc_booking_do_action__set_booking_approved_or_pending( $booking_id_arr, $params ){

		/**
		 *
		 * For creation  of new bookings or editing use something like this:
		 *
		  // Be sure to  make load in Booking Listing  this:  wp_enqueue_script( 'wpbc-main-client', wpbc_plugin_url( '/js/client.js' ), array( 'wpbc-datepick' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
				new wpdev_booking(); // Define ability to  work  with  shortcodes
				$return_sh  = do_shortcode('[booking type=1 nummonths=2]');
				wp_send_json($return_sh); // Send calendar
		*/

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );


		// Get reason of action --------------------------------------------------------------------------------------------
		$action_reason = $params['reason_of_action'];	// stripslashes( $params['reason_of_action'] ); // translate words like don\'t to don't

		// Get reason of action --------------------------------------------------------------------------------------------
		$is_approve_or_pending = $params['is_approve'];

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );


		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;
			if ( $is_approve_or_pending == '1' ) {
				$after_action_message = '<strong style="font-size: 1.4em;">' . count( explode( ',', $booking_id_csd ) ) .'</strong> '
										. ( ( false === strpos( $booking_id_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'Booking has been %1$s approved %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been %1$s approved %2$s', 'booking' ) , '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>';
			} else {
				$after_action_message = '<strong style="font-size: 1.4em;">' . count( explode( ',', $booking_id_csd ) ) .'</strong> '
										. ( ( false === strpos( $booking_id_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'Booking has been set as %1$s pending %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been set as %1$s pending %2$s', 'booking' ), '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>';
			}
			//    SQL    ---------------------------------------------------------------------------------------------------
			global $wpdb;
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$prepared_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}bookingdates SET approved = %s WHERE booking_id IN ({$booking_id_csd})", $is_approve_or_pending );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

			// LOG ---------------------------------------------------------------------------------------------------------
			$curr_user = get_user_by( 'id', (int) $params['user_id'] );
			wpbc_db__add_log_info( 		explode( ',', $booking_id_csd ),
									( ( $is_approve_or_pending == '1' ) ? __( 'Approved by:', 'booking' ) : __( 'Declined by:', 'booking' ) )
									. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
							 );

			// Update 'is_new' status of bookings in Database
			wpbc_db_update_number_new_bookings( explode( ',', $booking_id_csd ), '0', $params['user_id'] );

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__approved', $booking_id_csd, $is_approve_or_pending );                                 // FixIn: 8.7.6.1.

			// Emails ------------------------------------------------------------------------------------------------------
			if ( ! empty( $is_send_emeils ) ) {

				if ( $is_approve_or_pending == '1' ) {
					wpbc_send_email_approved( $booking_id_csd, $is_send_emeils, $action_reason );
				} else {
					wpbc_send_email_deny( $booking_id_csd, $is_send_emeils, $action_reason );
				}
			}

			// Cancel  other pending bookings for the same date	------------------------------------------------------------
			if ( $is_approve_or_pending == '1' ) {
				$all_bk_id_what_canceled = apply_bk_filter( 'cancel_pending_same_resource_bookings_for_specific_dates', false, $booking_id_csd );
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action:  booking 	Completely Delete
	 *
	 * @param $booking_id_arr	array or int	of booking ID
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1, 'reason_of_action' => 'Because...' )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__delete_booking_completely( $booking_id_arr, $params ){

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_is_csd = join( ',',  $booking_id_arr  );
		$booking_is_csd = wpbc_clean_digit_or_csd( $booking_is_csd );


		// Get reason of action --------------------------------------------------------------------------------------------
		$action_reason = $params['reason_of_action'];	// stripslashes( $params['reason_of_action'] ); // translate words like don\'t to don't

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );


		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_is_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;
			$bookings_count = '<strong style="font-size: 1.4em;">' . count( explode( ',', $booking_is_csd ) ) .'</strong> ';
			$after_action_message = $bookings_count . ' '
								  . ( ( false === strpos( $booking_is_csd, ',' ) )
									  	/* translators: 1: ... */
									  	? strtolower( sprintf( __( 'Booking has been %1$s deleted %2$s', 'booking' ), '<strong>', '</strong>' ) )
										/* translators: 1: ... */
										: strtolower( sprintf( __( 'Bookings have been %1$s deleted %2$s', 'booking' ) , '<strong>', '</strong>' ) )
									)
								 . ' <span style="font-size:0.9em;">( ID = <strong>' . implode( ', ', explode( ',', $booking_is_csd ) ) . '</strong> )</span>';

			// LOG ---------------------------------------------------------------------------------------------------------
			$curr_user = get_user_by( 'id', (int) $params['user_id'] );
			wpbc_db__add_log_info( 		explode( ',', $booking_is_csd ),
									__( 'Deleted by:', 'booking' )
									. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
							 );

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__delete', $booking_is_csd );                                 						// FixIn: 8.7.6.3.

			// Emails ------------------------------------------------------------------------------------------------------
			if ( ! empty( $is_send_emeils ) ) {

				wpbc_send_email_deleted( $booking_is_csd, $is_send_emeils, $action_reason );
			}

			//    SQL    ---------------------------------------------------------------------------------------------------
			global $wpdb;

			// Dates
			$prepared_sql = "DELETE FROM {$wpdb->prefix}bookingdates WHERE booking_id IN ({$booking_is_csd})";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during deleting dates in DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}
			// Bookings
			$prepared_sql = "DELETE FROM {$wpdb->prefix}booking WHERE booking_id IN ({$booking_is_csd})";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during deleting bookings in DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action: set booking as 	Read (Old) | Unread (New)
	 *
	 * @param $booking_id_arr	array or int	of booking ID
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1, 'is_new' => '1' )
	 *
	 * @return array
	 *
	 * Example:
		    $action_result = wpbc_booking_do_action__set_booking_as_read_unread(    $request_params['booking_id']
																			, array(
																				'user_id'          => $user_id,
																				'is_new' 		   => '0'
																			  )
																		);

	 */
	function wpbc_booking_do_action__set_booking_as_read_unread( $booking_id_arr, $params ){

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );

		// Get reason of action --------------------------------------------------------------------------------------------
		$is_new = $params['is_new'];

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;
			if ( $is_new == '1' ) {
				$after_action_message =  ( ( false === strpos( $booking_id_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'Booking has been set as %1$s new %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been set as %1$s new %2$s', 'booking' ) , '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . (( '-1' == $booking_id_csd ) ? __( 'all', 'booking' ) : $booking_id_csd) . '</strong> )</span>';
			} else {
				$after_action_message = ( ( false === strpos( $booking_id_csd, ',' ) )
											/* translators: 1: ... */
											? sprintf( __( 'The booking has been marked as %1$s read %2$s', 'booking' ), '<strong>', '</strong>' )
											/* translators: 1: ... */
											: sprintf( __( 'Bookings have been marked as %1$s read %2$s', 'booking' ), '<strong>', '</strong>' )
										)
										. ' <span style="font-size:0.9em;">( ID = <strong>' . (( '-1' == $booking_id_csd ) ? __( 'all', 'booking' ) : $booking_id_csd) . '</strong> )</span>';
			}
			//    SQL    ---------------------------------------------------------------------------------------------------
			global $wpdb;
			if ('-1' == $booking_id_csd ){
				$prepared_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET is_new = %s", $is_new );  // FixIn: 10.12.1.5.

				$prepared_sql .= " WHERE  ( 1 = 1 ) ";
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$prepared_sql = apply_bk_filter('update_where_sql_for_getting_bookings_in_multiuser', $prepared_sql );
			} else {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$prepared_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET is_new = %s WHERE booking_id IN ({$booking_id_csd})", $is_new );  // FixIn: 10.12.1.5.
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

			// LOG ---------------------------------------------------------------------------------------------------------
			$curr_user = get_user_by( 'id', (int) $params['user_id'] );
			wpbc_db__add_log_info( 		explode( ',', $booking_id_csd ),
									( ( $is_new == '1' ) ? __( 'Set as unread by:', 'booking' ) : __( 'Set as read by:', 'booking' ) )
									. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
							 );

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__changed_new_status', $booking_id_csd, $is_new );                                 // FixIn: 8.7.6.1.

			// Emails ------------------------------------------------------------------------------------------------------
			if ( ! empty( $is_send_emeils ) ) {

				if ( $is_new == '1' ) {
					//wpbc_send_email_approved( $booking_id_csd, $is_send_emeils, $action_reason );
				} else {
					//wpbc_send_email_deny( $booking_id_csd, $is_send_emeils, $action_reason );
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action:  Emty Trash
	 *
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__empty_trash( $params ){

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// Get bookings ID to delete -------------------------------------------------------------------------------
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.trash = 1";  // FixIn: 10.12.1.5.

		$sql = apply_bk_filter( 'update_where_sql_for_getting_bookings_in_multiuser', $sql, $params['user_id'] );                    // Get booking resources of this user only: $user_id

		$max_bookings_to_erase = 1000;
		$sql .= " LIMIT 0, " . $max_bookings_to_erase;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$bookings_in_trash = $wpdb->get_results( $sql );

		$bookings_id_in_trash_arr = array();

		foreach ( $bookings_in_trash as $booking_obj ) {
			$bookings_id_in_trash_arr[] = $booking_obj->booking_id;
		}
		$booking_is_csd = implode( ',', $bookings_id_in_trash_arr );

		// Empty trash  ------------------------------------------------------------------------------------------------

		if ( empty( $booking_is_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings in trash to erase.', 'booking' );

		} else {
			$after_action_result   = true;
			if ( count( $bookings_id_in_trash_arr ) < $max_bookings_to_erase ) {
				$after_action_message = sprintf( __( 'Trash has been erased.', 'booking' ) , '<strong>', '</strong>' )
										. ' <span style="font-size:0.9em;">( ID = <strong>' . substr( $booking_is_csd, 0 , 500 ) . '</strong> )</span>';
			} else {
				/* translators: 1: ... */
				$after_action_message = sprintf( __( 'From trash has been erased %s bookings.', 'booking' ) , '<strong> ' . count( $bookings_id_in_trash_arr ) . ' </strong> ' ) ;
			}

			// LOG -----------------------------------------------------------------------------------------------------
			//			$curr_user = get_user_by( 'id', (int) $params['user_id'] );
			//			wpbc_db__add_log_info( 		explode( ',', $booking_is_csd ),
			//									__( 'Deleted by:', 'booking' )
			//									. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
			//							 );

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__empty_trash' );                                 											// FixIn: 8.7.6.3.

			// Emails ------------------------------------------------------------------------------------------------------
			if ( ! empty( $is_send_emeils ) ) {

				// wpbc_send_email_deleted( $booking_is_csd, $is_send_emeils, __( 'Empty Trash', 'booking' )  );
			}

			//    SQL    ---------------------------------------------------------------------------------------------------

			// Dates
			$prepared_sql = "DELETE FROM {$wpdb->prefix}bookingdates WHERE booking_id IN ({$booking_is_csd})";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during deleting dates in DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}
			// Bookings
			$prepared_sql = "DELETE FROM {$wpdb->prefix}booking WHERE booking_id IN ({$booking_is_csd})";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during deleting bookings in DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action: save booking   Remark
	 *
	 * @param $booking_id_arr	array or int	of booking ID
	 * @param $params			array			array of parameters: 		array(	'user_id' => 1, 'remark' => 'Because...')
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__set_booking_note( $booking_id_arr, $params ){

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );

		// Get remark --------------------------------------------------------------------------------------------------
		$remark_text = $params['remark'];	// stripslashes( $params['reason_of_action'] ); // translate words like don\'t to don't

		// Is send email  for this action ------------------------------------------------------------------------------
		// $is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );


		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;
			$after_action_message = __( 'Note has been saved', 'booking' )
									. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>';
			//    SQL    ---------------------------------------------------------------------------------------------------
			global $wpdb;
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$prepared_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET remark = %s WHERE booking_id IN ({$booking_id_csd})", $remark_text );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$after_action_result = $wpdb->query( $prepared_sql );
			if ( false === $after_action_result ) {
				$after_action_message = 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
			} else {
				$after_action_result = true;
			}

			// LOG ---------------------------------------------------------------------------------------------------------
			if (0) {
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $booking_id_csd ),
											 __( 'Note saved by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );
			}

			// Just  action  hook  for some other addons
			do_action( 'wpbc_booking_action__note_saved', $booking_id_csd, $remark_text );

			// Emails ------------------------------------------------------------------------------------------------------
			if ( 0 ) {
				if ( ! empty( $is_send_emeils ) ) {
					wpbc_send_email_approved( $booking_id_csd, $is_send_emeils, $remark_text );
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}

	/**
	 * Action: change Booking Resource
	 *
	 * @param $booking_id_arr			array or int	of booking ID
	 * @param $selected_resource_id	 	int				of booking resource
	 * @param $params					array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__change_booking_resource( $booking_id_arr, $selected_resource_id, $params ) {

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );

		// ID of booking resource ------------------------------------------------------------------------------------------
		$selected_resource_id = intval($selected_resource_id);

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;

			$after_action_message = ( ( false === strpos( $booking_id_csd, ',' ) )
										/* translators: 1: ... */
										? sprintf( __( 'Booking has been changed %1$s booking resource %2$s', 'booking' ), '<strong>', '</strong>' )
										/* translators: 1: ... */
										: sprintf( __( 'Bookings have been changed %1$s booking resource %2$s', 'booking' ), '<strong>', '</strong>' )
									)
									. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>';


			$work_booking_id_arr = explode( ',', $booking_id_csd );

			foreach ( $work_booking_id_arr as $selected_booking_id ) {

				//    SQL    ---------------------------------------------------------------------------------------------------
				list( $after_action_result, $after_action_message, $formdata_new ) = wpbc__sql__change_booking_resource_for_booking( $selected_booking_id, $selected_resource_id );

				if ( $after_action_result ) {
					// LOG ---------------------------------------------------------------------------------------------------------
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $selected_booking_id ),
											__( 'Booking resource changed by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );

					// Just  action  hook  for some other addons
					do_action( 'wpbc_booking_action__change_booking_resource', $selected_booking_id, $selected_resource_id );

					// Emails ------------------------------------------------------------------------------------------------------
					if ( ! empty( $is_send_emeils ) ) {
						wpbc_send_email_modified( $selected_booking_id, $selected_resource_id, $formdata_new );
					}
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);

	}

		/**
		 * Change booking resource for specific booking -- SQL manipulation
		 * @param $selected_booking_id
		 * @param $selected_resource_id
		 *
		 * @return array
		 */
		function wpbc__sql__change_booking_resource_for_booking( $selected_booking_id, $selected_resource_id ){

			global $wpdb;

			$booking_id   = intval( $selected_booking_id );
			$resource_id  = intval( $selected_resource_id );
			$db_form_data_new = '';

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 1.Get dates of SOURCE booking
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ( 1 ) {
				// 1.1.Get booking data of SOURCE booking
				$sql              = $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}booking as bk WHERE booking_id = %d ", $booking_id );

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$res = $wpdb->get_row( $sql );

				$db_form_data_old = $res->form;
				$resource_id_old  = $res->booking_type;

				// 1.2. Get dates of SOURCE booking
				$sql                      = $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}bookingdates as dt WHERE booking_id = %d ORDER BY booking_date ASC ", $booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$old_resource_dates_array = $wpdb->get_results( $sql );
			}


			//TODO: 2023-10-03 14:49  this is new workflow have to be:
			if (0){
				$booking_p = array(
								  'dates'	 => TRANSFORM_IT( $old_resource_dates_array ) // ->  array( '2017-06-23 14:00:01', '2017-06-24 00:00:00', '2017-06-25', '2017-06-26 12:00:02' )
								, 'resource_id' => $resource_id
							    , 'params'      => array()			// 'is_use_booking_recurrent_time' => false )		// Commented,  because get from  Booking > Settings General page
						   );
				$is_dates_booked = wpbc_api_is_dates_booked( $booking_p[ 'dates' ], $booking_p[ 'resource_id' ], $booking_p[ 'params' ] );
			}

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 2. Get bookings of selected booking resource - checking if some dates there is booked or not
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$exist_dates_results = wpbc_get_booking_dates_in_resource_for_folowing_dates( $old_resource_dates_array , $resource_id );


			if ( ( count( $exist_dates_results ) == 3 ) && ( false === $exist_dates_results[0] ) ){
				// // ERROR :: number of check in/dates does not equal  to  number of check  out dates.
				return $exist_dates_results;
			}

			if ( get_bk_option('booking_change_resource_skip_checking') === 'On' ) {									// FixIn: 8.4.5.4.
				$is_date_time_booked = false;
			} else {
				$is_date_time_booked = wpbc_check_dates_intersections( $old_resource_dates_array, $exist_dates_results );
			}

			if (  $is_date_time_booked ) {

				$after_action_result  = false;
				$after_action_message = __( 'Warning! The resource was not changed. Current dates are already booked there.', 'booking' );

			} else {		// Possible to change

				$db_form_data_new = wpbc_update_resource_id_in_dbformatted_booking_data( $db_form_data_old, $resource_id_old, $resource_id );

				// Update // FixIn: 10.12.1.5.
				$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET form=%s, booking_type=%d WHERE booking_id=%d;"
													, $db_form_data_new, $resource_id, $booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				if ( false === $wpdb->query( $update_sql ) ) {
					$after_action_result  = false;
					$after_action_message = get_debuge_error( 'Error during updating booking reource type in BD', __FILE__, __LINE__ );

					return array( $after_action_result, $after_action_message, $db_form_data_new );
				}


				if ( class_exists( 'wpdev_bk_biz_l' ) ) {
					$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}bookingdates SET type_id=NULL WHERE booking_id=%d ", $booking_id );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
					if ( false === $wpdb->query( $update_sql ) ) {
						$after_action_result  = false;
						$after_action_message = get_debuge_error( 'Error during updating dates type in BD', __FILE__, __LINE__ );

						return array( $after_action_result, $after_action_message, $db_form_data_new );
					}
				}

				$booking_resources_arr = wpbc_ajx_get_all_booking_resources_arr();
				$after_action_result  = true;
				/* translators: 1: ... */
				$after_action_message = sprintf( __( 'Booking %1$s has been changed booking resource from %2$s to %3$s', 'booking' )
												, '<strong>[ID=' . $booking_id . ']</strong>'
												, '<strong>' . wpbc_lang( $booking_resources_arr[ $resource_id_old ]['title'] ) . '</strong>'
												, '<strong>' . wpbc_lang( $booking_resources_arr[ $resource_id ]['title'] ) . '</strong>'
											);

				// Everything Cool :) - booking resource changed
			}

			return array( $after_action_result, $after_action_message , $db_form_data_new );
		}

			/**
			 * Get booking dates (bookings obj) of selected booking resource - checking if some dates there is booked or not
			 *
			 * @param $old_resource_dates_array		array  of OBJ with  ..->booking_date
			 * @param int $resource_id				Resource ID,  where we get dates.
			 *
			 * @return array		array  of booking OBJ with  ..->booking_date
			 */
			function wpbc_get_booking_dates_in_resource_for_folowing_dates( $old_resource_dates_array , $resource_id ){

				global $wpdb;

				$dates_sql_between   = '';
				$check_in_dates_arr  = array();
				$check_out_dates_arr = array();

				if ( 'On' === get_bk_option( 'booking_recurrent_time' ) ) {    // If we are using  recurrent time slots ?

					foreach ( $old_resource_dates_array as $k => $v ) {

						if ( ':02' == substr( $v->booking_date, - 3 ) ) {
							$check_out_dates_arr[] = $v->booking_date;
						}
						if ( ':01' == substr( $v->booking_date, - 3 ) ) {
							$check_in_dates_arr[] = $v->booking_date;
						}
					}

					if ( count( $check_out_dates_arr ) == count( $check_in_dates_arr ) ) {
						$dates_sql_between_arr = array();
						foreach ( $check_in_dates_arr as $k => $v ) {

							$dates_sql_between_arr [] = ' ( dt.booking_date BETWEEN "' . $check_in_dates_arr[ $k ] . '" AND "' . $check_out_dates_arr[ $k ] . '" ) ';
						}
						$dates_sql_between = implode( 'OR', $dates_sql_between_arr );

						//TODO: remove (),  if only 1 element in array

					} else {
						// ERROR :: number of check in/dates does not equal  to  number of check  out dates.
						$after_action_result  = false;
						$after_action_message = '<strong>Error</strong>. Number of check in times of booking dates does not equal to number of check out days.'
												. '<br>Check  in dates: ' . implode( ', ', $check_in_dates_arr )
												. '<br>Check out dates: ' . implode( ', ', $check_out_dates_arr );

						return array( $after_action_result, $after_action_message, '' );
					}
				}

				if ( '' == $dates_sql_between ) {

					$temp_check_in  = $old_resource_dates_array[0]->booking_date;
					$temp_check_out = $old_resource_dates_array[ ( count( $old_resource_dates_array ) - 1 ) ]->booking_date;

					if ( ':02' != substr( $temp_check_out, - 3 ) ) {
						$temp_check_out = gmdate( 'Y-m-d H:i:s', strtotime( '+1 day -1 second', strtotime( $temp_check_out ) ) );
					}

					$dates_sql_between .= ' dt.booking_date BETWEEN "' . $temp_check_in . '" AND "' . $temp_check_out . '" ';
				}


				$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk
												 INNER JOIN {$wpdb->prefix}bookingdates as dt
												 ON    bk.booking_id = dt.booking_id
										  WHERE  bk.trash != 1 AND bk.booking_type = %d", $resource_id );
				$sql .= " 						 AND ( {$dates_sql_between} ) ";

				// In BL version its does not check for booking belonging to several booking resources
				if ( class_exists( 'wpdev_bk_biz_l' ) ) {
					$sql .= " OR  bk.booking_id IN ( "
							. " SELECT DISTINCT booking_id FROM {$wpdb->prefix}bookingdates as dtt 
									WHERE  dtt.type_id = {$resource_id} "
							//."AND DATE(dt.booking_date) IN ( {$dates_string} )"
							. " AND ( " . str_replace( 'dt.', 'dtt.', $dates_sql_between ) . " ) "
							. ") ";
				}
				$sql .= "   ORDER BY bk.booking_id DESC, dt.booking_date ASC ";

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$exist_dates_results = $wpdb->get_results( $sql );

				// We have found only  intersected dates. Now we need to get all  dates from such  bookings,  for having correct  "Start  and end time times for the booking"
				// and does not add some "start  time at the begining of the day inside of wpbc_check_dates_intersections( ) function
				if ( count( $exist_dates_results ) > 0 ) {

					// Get ID of all  bookings that  inside of this interval
					$my_booking_id_arr = [];
					foreach ( $exist_dates_results as $key => $booking_obj ) {
						$my_booking_id_arr[] = $booking_obj->booking_id;
					}
					// Get all dates of such  bookings (not only  intersected!
					$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk
													 INNER JOIN {$wpdb->prefix}bookingdates as dt
													 ON   bk.booking_id = dt.booking_id 
											  WHERE  bk.booking_id IN (" . implode( ',', $my_booking_id_arr ) . ") AND bk.trash != 1 AND bk.booking_type = %d", $resource_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					$sql .= "   ORDER BY bk.booking_id DESC, dt.booking_date ASC ";

					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
					$exist_dates_results = $wpdb->get_results( $sql );
				}

				return $exist_dates_results;
			}

			/**
			 * Update "Content of booking fields data" from  old booking resource id SUFFIX to  new booking Resource ID
			 *
			 * @param string $db_form_data_old
			 * @param int $resource_id_old
			 * @param int $resource_id_new
			 *
			 * @return void
			 */
			function wpbc_update_resource_id_in_dbformatted_booking_data( $db_form_data_old, $resource_id_old, $resource_id_new ) {

				$db_form_data_new = array();

				$form_data_arr = explode( '~', $db_form_data_old );
				$fields_count  = count( $form_data_arr );

				for ( $i = 0; $i < $fields_count; $i ++ ) {

					list( $type, $element_name, $value ) = explode( '^', $form_data_arr[ $i ] );

					if ( substr( $element_name, - 2 ) == '[]' ) {
						$element_name = str_replace( '[]', '', $element_name );
					}

					$element_name = substr( $element_name, 0, - 1 * strlen( $resource_id_old ) ) . $resource_id_new;  	// Change resource ID in field

					$db_form_data_new[] = $type . '^' . $element_name . '^' . $value;
				}

				return implode( '~', $db_form_data_new );
			}

	/**
	 * Action: Duplicate Booking into other Resource
	 *
	 * @param $booking_id_arr			array or int	of booking ID
	 * @param $selected_resource_id	 	int				of booking resource
	 * @param $params					array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__duplicate_booking_to_other_resource( $booking_id_arr, $selected_resource_id, $params ) {

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );

		// ID of booking resource ------------------------------------------------------------------------------------------
		$selected_resource_id = intval($selected_resource_id);

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result   = true;

			$after_action_message = ( ( false === strpos( $booking_id_csd, ',' ) )
										/* translators: 1: ... */
										? sprintf( __( 'Booking has been %1$s duplicated %2$s', 'booking' ), '<strong>', '</strong>' )
										/* translators: 1: ... */
										: sprintf( __( 'Bookings have been %1$s duplicated %2$s', 'booking' ), '<strong>', '</strong>' )
									)
									. ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>';


			$work_booking_id_arr = explode( ',', $booking_id_csd );

			foreach ( $work_booking_id_arr as $selected_booking_id ) {

				//    SQL    ---------------------------------------------------------------------------------------------------
				list( $after_action_result, $after_action_message, $formdata_new ) = wpbc__sql__duplicate_booking_to_other_resource_for_booking( $selected_booking_id, $selected_resource_id, $params );

				if ( $after_action_result ) {
					// LOG ---------------------------------------------------------------------------------------------------------
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $selected_booking_id ),
											__( 'Booking resource changed by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );

					// Just  action  hook  for some other addons
					do_action( 'wpbc_booking_action__duplicate_booking_to_other_resource', $selected_booking_id, $selected_resource_id );

					// Emails ------------------------------------------------------------------------------------------------------
					if ( ! empty( $is_send_emeils ) ) {
						// 	We are sending emails about the new booking,  if we have created it.
						//	wpbc_send_email_modified( $selected_booking_id, $selected_resource_id, $formdata_new );
					}
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);

	}

		/**
		 * Duplicate booking in another resource -- SQL manipulation
		 * @param $selected_booking_id
		 * @param $selected_resource_id
		 *
		 * @return array
		 */
		function wpbc__sql__duplicate_booking_to_other_resource_for_booking( $selected_booking_id, $selected_resource_id, $params ){
			global $wpdb;

			$booking_id   = intval( $selected_booking_id );
			$resource_id  = intval( $selected_resource_id );
			$db_form_data_new = '';
			$booking_resources_arr = wpbc_ajx_get_all_booking_resources_arr();

			$is_send_emails = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 1.Get dates of SOURCE booking
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ( 1 ) {
				// 1.1.Get booking data of SOURCE booking
				$sql              = $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}booking as bk WHERE booking_id = %d ", $booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$res = $wpdb->get_row( $sql );
				$db_form_data_old = $res->form;
				$resource_id_old  = $res->booking_type;

				// 1.2. Get dates of SOURCE booking
				$sql                      = $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}bookingdates as dt WHERE booking_id = %d ORDER BY booking_date ASC ", $booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$old_resource_dates_array = $wpdb->get_results( $sql );
			}

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 2. Get bookings of selected booking resource - checking if some dates there is booked or not
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$exist_dates_results = wpbc_get_booking_dates_in_resource_for_folowing_dates( $old_resource_dates_array , $resource_id );

			if ( ( count( $exist_dates_results ) == 3 ) && ( false === $exist_dates_results[0] ) ){
				// // ERROR :: number of check in/dates does not equal  to  number of check  out dates.
				return $exist_dates_results;
			}

			if ( get_bk_option('booking_change_resource_skip_checking') === 'On' ) {									// FixIn: 8.4.5.4.
				$is_date_time_booked = false;
			} else {
				$is_date_time_booked = wpbc_check_dates_intersections( $old_resource_dates_array, $exist_dates_results );
			}

			if (  $is_date_time_booked ) {

				$after_action_result  = false;
				$after_action_message = '<strong>' . esc_html__( 'Warning', 'booking' ) . '!</strong> '
										/* translators: 1: ... */
										. sprintf( __( 'Booking %1$s has not been duplicated in booking resource %2$s. Current dates are already booked there.' , 'booking' )
														, '<strong style="font-size:0.9em;">[ID=' . $booking_id . ']</strong>'
														, '<strong>' . wpbc_lang( $booking_resources_arr[ $resource_id ]['title'] ) . '</strong>'
											   );

			} else {		// Possible to change

				$db_form_data_new = wpbc_update_resource_id_in_dbformatted_booking_data( $db_form_data_old, $resource_id_old, $resource_id );
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Recommended
				$wpdev_active_locale = isset( $_REQUEST['wpbc_ajx_locale'] ) ?  sanitize_text_field( $_REQUEST['wpbc_ajx_locale'] ) : 'en_US';


				// Change dates from DateObj( $selected_date->booking_date = '2015-10-17' ) 	to    array ( '17.10.2015' )
				$my_dates_for_sql = array();
				foreach ($old_resource_dates_array as $selected_date) {
					$selected_date = explode( '-', $selected_date->booking_date );
					$my_dates_for_sql[]  = sprintf( "%02d.%02d.%04d", $selected_date[2], $selected_date[1], $selected_date[0] );
				}
				$my_dates_for_sql = implode( ', ', $my_dates_for_sql );

				// Create a new booking
				$request_save_params = array(
											 'resource_id'         => $resource_id,					// 2
											 'dates_ddmmyy_csv'    => $my_dates_for_sql,			// '04.10.2023, 05.10.2023, 06.10.2023'
											 'form_data'           => $db_form_data_new,			// 'text^cost_hint2^150.00฿~selectbox-multiple^rangetime2[]^14:00...'
											 'booking_hash'        => '',							// 'sdfsf34534rf'
											 'custom_form'         => '',							// 'custom_form_name'
											 'is_emails_send'       => $is_send_emails,				// 0 | 1
										 	 'is_show_payment_form' => 0							// 0 | 1
												// 'request_uri'          	=> $_SERVER['HTTP_REFERER']
												// 'user_id' 				=> wpbc_get_current_user_id()
												// 'sync_gid' 				=> ''
												// 'is_approve_booking'   	=> 0
										);
				$booking_save_arr = wpbc_booking_save( $request_save_params );

				if ( 'ok' === $booking_save_arr['ajx_data']['status'] ) {												// Everything Cool :) - booking has been duplicated

					$booking_id_new = $booking_save_arr['booking_id'];
					$after_action_result  = true;
					/* translators: 1: ... */
					$after_action_message = sprintf( __( 'Booking %1$s has been duplicated in booking resource %2$s. New booking %3$s.', 'booking' )
														, '<strong style="font-size:0.9em;">[ID=' . $booking_id . ']</strong>'
														, '<strong>' . wpbc_lang( $booking_resources_arr[ $resource_id ]['title'] ) . '</strong>'
														, '<strong style="font-size:0.9em;">[ID=' . $booking_id_new . ']</strong>'
											   );
				} else {																								// Error
					$after_action_result  = false;
					$after_action_message = $booking_save_arr['ajx_data']['ajx_after_action_message'];
				}
			}

			return array( $after_action_result, $after_action_message , $db_form_data_new );
		}

	/**
	 * Action: Set payment status for the booking
	 *
	 * @param $booking_id_arr			array or int	of booking ID
	 * @param $selected_payment_status	string			payment status of booking
	 * @param $params					array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__set_payment_status( $booking_id_arr, $selected_payment_status, $params ) {

		global $wpdb;

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );


		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result  = true;
			$after_action_message = '';

			$work_booking_id_arr = explode( ',', $booking_id_csd );

			foreach ( $work_booking_id_arr as $selected_booking_id ) {

				//    SQL    ---------------------------------------------------------------------------------------------------
				$sql       = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.booking_id= %d ", $selected_booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$result_bk = $wpdb->get_results( $sql );

				$old_pay_status = '';

				if ( ( 0 == count( $result_bk ) ) ) {
					// Error
					$after_action_result = false;
					/* translators: 1: ... */
					$after_action_message .= sprintf(	 __( 'There is no booking %s', 'booking' )
														 , ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>'
													);
				} else {
					$old_pay_status = $result_bk[0]->pay_status;
				}
				// FixIn: 10.12.1.5.
				$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET pay_status= %s WHERE booking_id= %d ", $selected_payment_status, $selected_booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				if ( false === $wpdb->query( $update_sql ) ) {
					// Error
					$after_action_result = false;
					$after_action_message .= 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
				} else {
					// Success
					/* translators: 1: ... */
					$after_action_message .= sprintf( __( 'Payment status for Booking %1$s has been updated from %2$s to %3$s', 'booking' )
														, ' <span style="font-size:0.9em;">( ID = <strong>' . $selected_booking_id . '</strong> )</span>'
														, '<strong>"' . wpbc__format__get_payment_status_title( $old_pay_status ) . '"</strong>'
														, '<strong>"' . wpbc__format__get_payment_status_title( $selected_payment_status ) . '"</strong>'
											 		) . '<br/>';
				}


				if ( $after_action_result ) {
					// LOG ---------------------------------------------------------------------------------------------------------
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $selected_booking_id ),
											$after_action_message
											. '. ' . __( 'Changed by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );

					// Just  action  hook  for some other addons
					do_action( 'wpbc_booking_action__set_payment_status', $selected_booking_id, $selected_payment_status , $old_pay_status );

					// Emails ------------------------------------------------------------------------------------------------------
					if ( ! empty( $is_send_emeils ) ) {
						// 	We are sending emails about the new booking,  if we have created it.
						//	wpbc_send_email_modified( $selected_booking_id, $selected_resource_id, $formdata_new );
					}
				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);

	}

		/**
		 * Get title of the payment status
		 *
		 * @param $payment_status_key
		 *
		 * @return string
		 */
		function wpbc__format__get_payment_status_title( $payment_status_key ){

			$selected_payment_status_text = $payment_status_key;

			$select_box_options = get_payment_status_titles();
			$select_box_options = array_flip( $select_box_options );

			if ( ! empty( $select_box_options[ $payment_status_key ] ) ) {
				$selected_payment_status_text = $select_box_options[ $payment_status_key ];
			}

			return $selected_payment_status_text;
		}


	/**
	 * Action: Set Booking Cost
	 *
	 * @param $booking_id_arr			array or int	of booking ID
	 * @param $booking_cost				string			booking cost
	 * @param $params					array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 *
	 * Example:
	 */
	function wpbc_booking_do_action__set_booking_cost( $booking_id_arr, $booking_cost, $params ) {

		global $wpdb;

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );


		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		$booking_cost = str_replace(',', '.', $booking_cost);
		$booking_cost = floatval(  $booking_cost );


		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result  = true;
			$after_action_message = '';

			$work_booking_id_arr = explode( ',', $booking_id_csd );

			foreach ( $work_booking_id_arr as $selected_booking_id ) {

				//    SQL    ---------------------------------------------------------------------------------------------------
				$sql       = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.booking_id= %d ", $selected_booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$result_bk = $wpdb->get_results( $sql );

				$old_booking_cost = '';

				if ( ( 0 == count( $result_bk ) ) ) {
					// Error
					$after_action_result = false;
					/* translators: 1: ... */
					$after_action_message .= sprintf(	 __( 'There is no booking %s', 'booking' )
														 , ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>'
													);
				} else {
					$old_booking_cost = $result_bk[0]->cost;
				}

				$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET cost = %f WHERE booking_id = %d ", $booking_cost, $selected_booking_id );  // FixIn: 10.12.1.5.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				if ( false === $wpdb->query( $update_sql ) ) {
					// Error
					$after_action_result = false;
					$after_action_message .= 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
				} else {
					// Success
					/* translators: 1: ... */
					$after_action_message .= sprintf( __( 'Cost for Booking %1$s has been updated from %2$s to %3$s', 'booking' )
														, ' <span style="font-size:0.9em;">( ID = <strong>' . $selected_booking_id . '</strong> )</span>'
														, '<strong>"' . wpbc__format__get_payment_status_title( $old_booking_cost ) . '"</strong>'
														, '<strong>"' . wpbc__format__get_payment_status_title( $booking_cost ) . '"</strong>'
											 		) . '<br/>';
				}


				if ( $after_action_result ) {
					// LOG ---------------------------------------------------------------------------------------------------------
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $selected_booking_id ),
											$after_action_message
											. '. ' . __( 'Changed by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );

					// Just  action  hook  for some other addons
					do_action( 'wpbc_booking_action__set_booking_cost', $selected_booking_id, $booking_cost , $old_booking_cost );

					// Emails ------------------------------------------------------------------------------------------------------
					if ( ! empty( $is_send_emeils ) ) {

						if ( get_bk_option( 'booking_send_email_on_cost_change' ) == 'On' ) {                                    // FixIn: 8.1.3.30.
							$booking_data = apply_bk_filter( 'wpbc_get_booking_data', $selected_booking_id );
							wpbc_send_email_modified( $selected_booking_id, $booking_data['type'], $booking_data['form'] );
						}
					}

				}
			}
		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}


	/**
	 * Action: Send Payment request
	 *
	 * @param $booking_id_arr			array or int	of booking ID
	 * @param $booking_cost				string			booking cost
	 * @param $params					array			array of parameters: 		array(	'user_id' => 1 )
	 *
	 * @return array
	 */
	function wpbc_booking_do_action__send_payment_request($booking_id_arr, $reason_of_action, $params ) {
		global $wpdb;

		make_bk_action('check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		// Get ID list of bookings,  like  '1' or '3,7,9'	----------------------------------------------------------------
		if ( ! is_array( $booking_id_arr ) ) {
			$booking_id_arr = array( $booking_id_arr );
		}
		$booking_id_csd = join( ',',  $booking_id_arr  );
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );


		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		if ( empty( $booking_id_csd ) ) {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been selected. Please select one or more bookings.', 'booking' );

		} else {
			$after_action_result  = true;
			$after_action_message = '';

			$work_booking_id_arr = explode( ',', $booking_id_csd );

			foreach ( $work_booking_id_arr as $selected_booking_id ) {

				//    SQL    ---------------------------------------------------------------------------------------------------
				$sql       = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.booking_id= %d ", $selected_booking_id );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$result_bk = $wpdb->get_results( $sql );

				$old_booking_cost = '';

				if ( ( 0 == count( $result_bk ) ) ) {
					// Error
					$after_action_result = false;
					/* translators: 1: ... */
					$after_action_message .= sprintf(	 __( 'There is no booking %s', 'booking' )
														 , ' <span style="font-size:0.9em;">( ID = <strong>' . $booking_id_csd . '</strong> )</span>'
													);
				} else {
					$old_booking_cost = $result_bk[0]->cost;

					$is_email_payment_request_adress = get_bk_option( 'booking_is_email_payment_request_adress' );
					if ( 'Off' != $is_email_payment_request_adress ) {

						$reason_of_action = htmlspecialchars( str_replace( '\"', '"', $reason_of_action ) );
						$reason_of_action = str_replace( "\'", "'", $reason_of_action );

						foreach ( $result_bk as $res ) {

							$is_send = wpbc_send_email_payment_request( $res->booking_id, $res->booking_type, $res->form, $reason_of_action );

							if ( $is_send ) {
								// Update Payment request number
								$pay_request_numer = $res->pay_request + 1;
								// FixIn: 10.12.1.5.
								$update_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}booking SET pay_request= %d WHERE booking_id= %d ", $pay_request_numer, $res->booking_id );
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
								if ( false === $wpdb->query( $update_sql ) ) {
									// Error
									$after_action_result = false;
									$after_action_message .= 'Error during updating to DB. File:' . __FILE__ . ' on line: ' . __LINE__;
								} else {
									// Success
									/* translators: 1: ... */
									$after_action_message .= sprintf( __( 'Payment request for Booking %1$s has been sent. Cost for payment: %2$s', 'booking' )
																		, ' <span style="font-size:0.9em;">( ID = <strong>' . $selected_booking_id . '</strong> )</span>'
																		, '<strong>"' . wpbc__format__get_payment_status_title( $old_booking_cost ) . '"</strong>'
																	) . '<br/>';
								}
							}
						}
					}
				}

				if ( $after_action_result ) {
					// LOG ---------------------------------------------------------------------------------------------------------
					$curr_user = get_user_by( 'id', (int) $params['user_id'] );
					wpbc_db__add_log_info( 		explode( ',', $selected_booking_id ),
											$after_action_message
											. '. ' . __( 'Send by:', 'booking' )
											. ' ' . $curr_user->first_name . ' ' . $curr_user->last_name . ' (' . $curr_user->user_email . ')'
									 );

					// Just  action  hook  for some other addons
					do_action( 'wpbc_booking_action__send_payment_request', $selected_booking_id, $reason_of_action , $old_booking_cost );

					// Emails ------------------------------------------------------------------------------------------------------
					/*
					if ( ! empty( $is_send_emeils ) ) {

						if ( get_bk_option( 'booking_send_email_on_cost_change' ) == 'On' ) {
							$booking_data = apply_bk_filter( 'wpbc_get_booking_data', $selected_booking_id );
							wpbc_send_email_modified( $selected_booking_id, $booking_data['type'], $booking_data['form'] );
						}
					}
					*/
				}
			}

		}

		return array(
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}


	function wpbc_booking_do_action__import_google_calendar( $request_params, $params ) {

		$return_array = array();

		$user_bk_id = (int) $params['user_id'];

		global $wpdb;

		$wpbc_Google_Calendar = new WPBC_Google_Calendar();

		$wpbc_Google_Calendar->setSilent();

		$wpbc_Google_Calendar->set_timezone( get_bk_option('booking_gcal_timezone') );

		$wpbc_Google_Calendar->set_events_from_with_array( array(
																	$request_params['booking_gcal_events_from'],
																	$request_params['booking_gcal_events_from_offset'],
																	$request_params['booking_gcal_events_from_offset_type']
																) );

		$wpbc_Google_Calendar->set_events_until_with_array( array(
																	$request_params['booking_gcal_events_until'],
																	$request_params['booking_gcal_events_until_offset'],
																	$request_params['booking_gcal_events_until_offset_type']
																) );

		$wpbc_Google_Calendar->set_events_max( $request_params['booking_gcal_events_max']  );

		if ( ( isset( $request_params['booking_gcal_resource'] ) ) && ( empty( $request_params['booking_gcal_resource'] ) ) ) {

			$wpbc_Google_Calendar->setUrl( get_bk_option( 'booking_gcal_feed') );
			$import_result = $wpbc_Google_Calendar->run();

		} else {

			if ( $request_params['booking_gcal_resource'] != 'all' ) {                             // One resource

				$wpbc_booking_resource_id = intval( $request_params['booking_gcal_resource'] );

				$wpbc_Google_Calendar->setResource( $wpbc_booking_resource_id );

				$wpbc_booking_resource_feed = get_booking_resource_attr( $wpbc_booking_resource_id );
				$wpbc_booking_resource_feed = $wpbc_booking_resource_feed->import;
				$wpbc_Google_Calendar->setUrl( $wpbc_booking_resource_feed );

				$import_result = $wpbc_Google_Calendar->run();
			} else {                                                                // All  resources

				$where = '';                                                        // Where for the different situation: BL and MU

				if ( class_exists( 'wpdev_bk_multiuser' ) ) {                       // MultiUser - only specific booking resources for specific Regular User in Admin panel.

					$is_user_super_admin = apply_bk_filter( 'is_user_super_admin', $user_bk_id );

					if ( ! $is_user_super_admin ) {
						$where .= ' WHERE users = ' . intval( $user_bk_id ) . ' ';
					}
				}

				$my_sql = "SELECT booking_type_id, import FROM {$wpdb->prefix}bookingtypes {$where}";
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$types_list = $wpdb->get_results( $my_sql );

				foreach ($types_list as $wpbc_booking_resource) {
					$wpbc_booking_resource_id   = $wpbc_booking_resource->booking_type_id;
					$wpbc_booking_resource_feed = $wpbc_booking_resource->import;
					if ( ( ! empty( $wpbc_booking_resource_feed ) ) && ( $wpbc_booking_resource_feed != null ) && ( $wpbc_booking_resource_feed != '/' ) ) {

						$wpbc_Google_Calendar->setUrl( $wpbc_booking_resource_feed );
						$wpbc_Google_Calendar->setResource( $wpbc_booking_resource_id );
						$import_result = $wpbc_Google_Calendar->run();
					}
				}
			}
		}


		if ( ( isset( $import_result ) ) && ( false != $import_result ) ) {
			$after_action_result  = true;
			/* translators: 1: ... */
			$after_action_message = sprintf(  __( '%s new bookings have been imported', 'booking' )
											, ' <span style="font-size:1em;"> <strong>' . count( $wpbc_Google_Calendar->events ) . '</strong> </span>'
											) ;
			if ( 0 != count( $wpbc_Google_Calendar->events ) ) {
				$after_action_message .= '<br/><br/>' . esc_html__( 'The filter settings have been updated to reflect these imported bookings. The page will be reloaded.', 'booking' );
			}

			if ( ! empty( $wpbc_Google_Calendar->getErrorMessage() ) ) {
				$after_action_message .= '<div style="max-width:60rem;margin:2em 10px;">' . $wpbc_Google_Calendar->getErrorMessage() . '</div>';
				$return_array['after_action_result_delay'] = 100000000000000;
			}

		} else {
			$after_action_result  = false;
			$after_action_message = __( 'No bookings have been imported.', 'booking' )
									. '<br/><br/>'
			                        . sprintf( __( 'Please configure settings for import Google Calendar events', 'booking' ), '<b>', ',</b>' )
                           				. ' <a href="' . esc_url( wpbc_get_settings_url() . '&tab=sync&subtab=gcal' ). '">' . esc_html__('here' ,'booking') . '.</a>'
									. '<br/><br/>'
									. $wpbc_Google_Calendar->getErrorMessage();
			$return_array['after_action_result_delay'] = 100000000000000;
		}


		if ( 0 == count( $wpbc_Google_Calendar->events ) ) {
			$new_listing_params = false;
		} else {
			$new_listing_params = array(
				"sort"                             => "booking_id",
				"sort_type"                        => "DESC",
				"page_num"                         => 1,
				"page_items_count"                 => "50",
				"create_date"                      => "",
				"keyword"                          => "",
				"source"                           => "",
				"wh_booking_type"                  => array( "0" ),
				"wh_approved"                      => "",
				"wh_booking_date"                  => array( "3" ),
				"ui_wh_booking_date_radio"         => 0,
				"ui_wh_booking_date_next"          => 1,
				"ui_wh_booking_date_prior"         => 1,
				"ui_wh_booking_date_checkin"       => "",
				"ui_wh_booking_date_checkout"      => "",
				"wh_what_bookings"                 => "imported",
				"wh_modification_date"             => array( "1" ),
				"ui_wh_modification_date_prior"    => "1",
				"ui_wh_modification_date_checkin"  => "",
				"ui_wh_modification_date_checkout" => "",
				"wh_pay_status"                    => array( "all" ),
				"ui_wh_pay_status_radio"           => "",
				"ui_wh_pay_status_custom"          => "",
				"wh_cost"                          => "",
				"wh_cost2"                         => "",
				"wh_sort"                          => "booking_id__desc",
				"wh_trash"                         => "any",
				'reload_url_params'				   => htmlspecialchars_decode( esc_url( wpbc_get_bookings_url() . '&tab=vm_booking_listing' ) )
			);
		}


		$return_array['after_action_result']  = $after_action_result;
		$return_array['after_action_message'] = $after_action_message;
		$return_array['new_listing_params']   = $new_listing_params;

		return $return_array;
	}


	function wpbc_booking_do_action__export_csv( $request_params, $params ){

		global $wpdb;

		make_bk_action( 'check_multiuser_params_for_client_side_by_user_id', $params['user_id'] );

		$booking_id_csd = $request_params['booking_id'];
		$booking_id_csd = wpbc_clean_digit_or_csd( $booking_id_csd );
		if ( empty( $booking_id_csd ) ) {
			// Export all  bookings (no selected bookings was in the listing
		}

		$export_type = $request_params['export_type']; // csv_page | csv_all

		// Is send email  for this action ----------------------------------------------------------------------------------
		$is_send_emeils = wpbc_ajx__user_request_option__is_send_emails( $params['user_id']  );

		// -----------------------------------------------------------------------------------------------------------------

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['search_params'] ) || empty( $_POST['search_params'] ) ) {

			$after_action_result  = false;
			$after_action_message = __('Error', 'booking')  . '! ' . 'Search parameters for CSV generating are empty.';

		} else {


			$user_request = new WPBC_AJX__REQUEST( array(
													   'db_option_name'          => 'booking_listing_request_params',
													   'user_id'                 => wpbc_get_current_user_id(),
													   'request_rules_structure' => wpbc_ajx_get__request_params__names_default()
													)
							);
			$request_prefix = 'search_params';
			$request_params_for_listing = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 		// NOT Direct: 	$_REQUEST['search_params']['resource_id']



			// $data_arr = wpbc_ajx_get_booking_data_arr( $request_params_for_listing );

			$export_csv_url = wpbc_csv_get_url_export( $request_params_for_listing );

			$after_action_result  = true;
			$after_action_message = __('Processing' ,'booking') . '... ';
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Recommended
		$wpbc_ajx_locale  = ( isset( $_REQUEST['wpbc_ajx_locale'] ) )  ? sanitize_text_field( $_REQUEST['wpbc_ajx_locale'] )  : 'en_US';

		return array(

					'export_csv_url' => $export_csv_url
										. '&export_type=' . $export_type
										. '&selected_id=' . $booking_id_csd
										. '&csv_export_separator='   . $request_params['csv_export_separator']
										. '&csv_export_skip_fields=' . $request_params['csv_export_skip_fields']
										. '&wpbc_ajx_locale=' . $wpbc_ajx_locale
										. '&wpbc_ajx_locale_reload=force' ,
					'after_action_result'  => $after_action_result,
					'after_action_message' => $after_action_message
				);
	}
// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc="  ==  Button Actions Templates for UI  ==  "  >

	/**
	 * Show Read booking Button
	 */
	function wpbc_for_booking_template__action_read(){

		$booking_action = 'set_booking_as_read';

		if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
			return false;
		}

		$params = array(
			'type'             => 'button',
			'title'            => '',
			'hint'             => array(
											'title'    => __( 'New booking', 'booking' ),
											'position' => 'top'
										),
			'link'             => 'javascript:void(0)',
			'action'           => "	wpbc_ajx_booking_ajax_action_request( { 
																			'booking_action' : '{$booking_action}', 
																			'booking_id'     : {{data['parsed_fields']['booking_id']}}  
																		} ); 
									wpbc_button_enable_loading_icon( this ); ",
			'icon'             => array(
											'icon_font' => 'wpbc_icn_fiber_manual_record',// 'wpbc_icn_visibility',
											'position'  => 'left',
											'icon_img'  => ''
										),
			'class'            => 'wpbc_is_new_button',
			'style'            => '',
			'mobile_show_text' => true,
			'attr'             => array()
		);

		?><# if ( '1' == data.parsed_fields.is_new ) { #><?php
			wpbc_flex_button( $params );
		?><# } #><?php
	}

// </editor-fold>



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Support functions
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Catch some errors, if such errors exist during our Ajx request
 */
class WPBC_AJAX_ERROR_CATCHING{

	public $errors_arr;

	function __construct() {

		$this->errors_arr = array();

		// Catch Error messages
		add_action( 'wp_error_added', array( $this, 'wpbc_wp_error_added' ), 10, 4 );

		if ( ! defined( 'WPBC_AJAX_ERROR_CATCH' ) ) { define( 'WPBC_AJAX_ERROR_CATCH', true ); }		// Check somewhere to not show error messages:  if ( ( defined( 'WPBC_AJAX_ERROR_CATCH' ) ) && (  WPBC_AJAX_ERROR_CATCH ) ) { return  false; }
	}

	/**
	 * Catch Error messages
	 *
	 * @param $code
	 * @param $message
	 * @param $data
	 * @param $this_link
	 *
	 * @return void
	 */
	function wpbc_wp_error_added( $code, $message, $data, $this_link ){

		$this->errors_arr[] = array(
			'code'      => $code,
			'message'   => $message,
			'data'      => $data,
			'this_link' => $this_link
		) ;
	}

	/**
	 * Get summary  of all  errors
	 * @return string
	 */
	function get_error_messages(){

		$error_message = array();

		foreach ( $this->errors_arr as $error ) {
			$error_message[] = 'Error: ' . $error['code'] . '. ' . $error['message'];
		}
		$error_message = implode( '<br/>', $error_message );

		if ( ! empty( $error_message ) ) {
			$error_message =   '<div class="wpbc_ajx_errors">'
							   	.'<br/><strong>' . esc_html__( 'Some errors were encountered.', 'booking' ) . '</strong><br/>'
							   	. $error_message
							 . '</div>';
		}
		return $error_message;
	}
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Check what user can  do
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Check permission of this user on specific actions,  like approving or deleting of bookings
 *
 * @param string  	$action		like 'set_pnding'
 * @param int 		$user_id	like 1
 *
 * @return bool
 */
function wpbc_is_user_can( $action, $user_id = 0 ){

	if ( 0 == $user_id ) {
		$user_id = wpbc_get_current_user_id();
	}
	$user       = get_userdata( $user_id );
	$user_roles = (array) ( empty( $user ) ? array() : $user->roles );        // Standard WordPress Roles:  'administrator' | 'editor' | 'author' | 'contributor' | 'subscriber'


	// Get here list  of actions that  user can do
    switch ( $action ) {
        case 'set_booking_pending':
            break;
        case 'set_booking_approved':
            break;
		case 'move_booking_to_trash':
			break;
		case 'restore_booking_from_trash':
			break;
		case 'delete_booking_completely':
			// if ( ! in_array( 'administrator', $user_roles ) ) { return false; }
			break;
		case 'booking_add_google_calendar':
			break;
		case 'set_booking_locale':
			break;
		case 'set_booking_as_read':
			break;
		case 'set_booking_as_unread':
			break;
		case 'empty_trash':
			break;
		case 'set_booking_note':
			break;
		case 'edit_booking':
			break;
		case 'change_booking_resource':
			break;
		case 'duplicate_booking_to_other_resource':
			break;
		case 'set_payment_status':
			break;
		case 'set_booking_cost':
			break;
		case 'send_payment_request':
			break;
		case 'import_google_calendar':
			break;
		case 'export_csv':
			break;
        default:
           // Default
    }

	return true;
}