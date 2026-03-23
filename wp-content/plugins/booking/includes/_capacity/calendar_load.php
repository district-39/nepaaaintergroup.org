<?php
/**
 * @file: wp-content/plugins/booking/includes/_capacity/calendar_load.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly            // FixIn: 9.8.0.4.

/**
 * Define parameters for calendar -- required for JS calendar show
 *
 * @param array $params
 *
 * @return string  JS code body (no <script> wrapper)
 */
function wpbc__calendar__set_js_params__before_show( $params ) {

	$start_script_code = '';

	// Server Balancer.
	$balancer_max_threads = intval( get_bk_option( 'booking_load_balancer_max_threads' ) );
	$balancer_max_threads = ( empty( $balancer_max_threads ) ) ? 1 : $balancer_max_threads;
	$start_script_code .= " _wpbc.balancer__set_max_threads( " . $balancer_max_threads . " ); ";

	$resource_id = isset( $params['resource_id'] ) ? absint( $params['resource_id'] ) : 1;

	// Start Month (require [year,month]).
	$calendar_scroll_to = 'false';
	if (
		isset( $params['start_month_calendar'] )
		&& is_array( $params['start_month_calendar'] )
		&& ( count( $params['start_month_calendar'] ) > 1 )
	) {
		$y = intval( $params['start_month_calendar'][0] );
		$m = intval( $params['start_month_calendar'][1] );
		if ( ( $y > 0 ) && ( $m > 0 ) ) {
			$calendar_scroll_to = "[ " . (string) $y . "," . (string) $m . " ]";
		}
	}
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'calendar_scroll_to' , " . $calendar_scroll_to . " ); ";

	// Set Start / End Date in Calendar (JSON encode => safe quotes).
	$params['calendar_dates_start'] = ( empty( $params['calendar_dates_start'] ) ) ? '' : (string) $params['calendar_dates_start'];
	$params['calendar_dates_end']   = ( empty( $params['calendar_dates_end'] ) ) ? '' : (string) $params['calendar_dates_end'];

	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'calendar_dates_start' , " . wp_json_encode( $params['calendar_dates_start'] ) . " ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'calendar_dates_end'   , " . wp_json_encode( $params['calendar_dates_end'] ) . " ); ";

	if ( ( '' !== $params['calendar_dates_start'] ) || ( '' !== $params['calendar_dates_end'] ) ) {
		$start_script_code .= wpbc_get_localized_js__time_local( $params['calendar_dates_start'] );
	}

	// Max months to scroll.
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'booking_max_monthes_in_calendar' , " . wp_json_encode( (string) get_bk_option( 'booking_max_monthes_in_calendar' ) ) . " ); ";

	// Start of week day.
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'booking_start_day_weeek' , " . wp_json_encode( (string) get_bk_option( 'booking_start_day_weeek' ) ) . " ); ";

	// Number of visible months.
	$months_num = isset( $params['calendar_number_of_months'] ) ? (int) $params['calendar_number_of_months'] : 1;
	$months_num = max( 1, min( 36, $months_num ) );
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'calendar_number_of_months' , " . wp_json_encode( (string) $months_num ) . " ); ";

	// Days Selection.
	$days_selection_arr = wpbc__calendar__js_params__get_days_selection_arr();

	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'days_select_mode', " . wp_json_encode( (string) $days_selection_arr['days_select_mode'] ) . " ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'fixed__days_num', " . intval( $days_selection_arr['fixed__days_num'] ) . " ); ";

	// Keep legacy: these are stored as comma lists like "-1" or "0,1".
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'fixed__week_days__start',   [" . $days_selection_arr['fixed__week_days__start'] . "] ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'dynamic__days_min', " . intval( $days_selection_arr['dynamic__days_min'] ) . " ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'dynamic__days_max', " . intval( $days_selection_arr['dynamic__days_max'] ) . " ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'dynamic__days_specific',    [" . $days_selection_arr['dynamic__days_specific'] . "] ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'dynamic__week_days__start', [" . $days_selection_arr['dynamic__week_days__start'] . "] ); ";

	// Date / Time formats.
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'booking_date_format', " . wp_json_encode( (string) get_bk_option( 'booking_date_format' ) ) . " ); ";
	$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'booking_time_format', " . wp_json_encode( (string) get_bk_option( 'booking_time_format' ) ) . " ); ";

	// Capacity | availability.
	if ( class_exists( 'wpdev_bk_biz_l' ) ) {
		$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'is_parent_resource' , " . ( wpbc_get_child_resources_number( $resource_id ) ? 1 : 0 ) . " ); ";
		$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'booking_capacity_field' , " . wp_json_encode( (string) wpbc_get__booking_capacity_field__name() ) . " ); ";
		$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'booking_is_dissbale_booking_for_different_sub_resources' , " . wp_json_encode( (string) get_bk_option( 'booking_is_dissbale_booking_for_different_sub_resources' ) ) . " ); ";
	}
	if ( class_exists( 'wpdev_bk_biz_s' ) ) {
		$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . " , 'booking_recurrent_time' , " . wp_json_encode( (string) get_bk_option( 'booking_recurrent_time' ) ) . " ); ";
	}

	// Save initial values of days selection for later use in conditional day logic.
	$start_script_code .= " if ( 'function' === typeof ( wpbc__conditions__SAVE_INITIAL__days_selection_params__bm ) ) { wpbc__conditions__SAVE_INITIAL__days_selection_params__bm( " . $resource_id . " ); } ";

	if (
		( ! empty( $params['shortcode_options'] ) )
		&& ( function_exists( 'wpbc_parse_shortcode_option__set_days_selection_conditions' ) )
	) {
		$days_selection_conditions = wpbc_parse_shortcode_option__set_days_selection_conditions( $resource_id, $params['shortcode_options'] );

		if ( ! empty( $days_selection_conditions ) ) {
			$start_script_code .= " _wpbc.calendar__set_param_value( " . $resource_id . ", 'conditions', " . wp_json_encode( $days_selection_conditions['conditions'] ) . " ); ";
			$start_script_code .= " _wpbc.seasons__set( " . $resource_id . ", " . wp_json_encode( $days_selection_conditions['seasons'] ) . " ); ";
		}
	}

	return $start_script_code;
}


		/**
		 * Get days selection parameters,  which saved in database
		 *
		 * @return array
		 */
		function wpbc__calendar__js_params__get_days_selection_arr(){

			$specific_days_selection = ( function_exists( 'wpbc_get_specific_range_dates__as_comma_list' ) )
										? wpbc_get_specific_range_dates__as_comma_list( get_bk_option( 'booking_range_selection_days_specific_num_dynamic' ) )
										: '';
			$data_arr = array();
			// Modes :: Selection of Days
			$data_arr['days_select_mode'] = ( 'range' === get_bk_option('booking_type_of_day_selections') )
														? get_bk_option('booking_range_selection_type')
														: get_bk_option( 'booking_type_of_day_selections');
			$data_arr['fixed__days_num']            = intval( get_bk_option( 'booking_range_selection_days_count' ) );                 /* Number of days selection with 1 mouse click */
			$data_arr['fixed__week_days__start']    = get_bk_option( 'booking_range_start_day' );                                      /* { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat } */
			$data_arr['dynamic__days_min']          = intval( get_bk_option( 'booking_range_selection_days_count_dynamic' ) );         /* Min. Number of days selection with 2 mouse clicks */
			$data_arr['dynamic__days_max']          = intval( get_bk_option( 'booking_range_selection_days_max_count_dynamic' ) );     /* Max. Number of days selection with 2 mouse clicks */
			$data_arr['dynamic__days_specific']     = $specific_days_selection;                                                        /* Example: '5,7' */
			$data_arr['dynamic__week_days__start']  = get_bk_option( 'booking_range_start_day_dynamic' );                              /* { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat } */

			// Fix about possible issue of downgrade from  Paid to  free version  and try  to  use 'Range days' selection,  which  is not supports in that  version.      // FixIn: 10.4.0.4.
			if ( ( ! class_exists( 'wpdev_bk_biz_s' ) ) && ( in_array( $data_arr['days_select_mode'], array( 'dynamic', 'fixed' ) ) ) ) {
				$data_arr['days_select_mode'] = 'multiple';
			}

			return $data_arr;
		}



/**
 * Get JavaScript for Calendar Loading:
 *  1) Show Calendar
 *  2) Send Ajax to load bookings / availability / data for calendar
 *
 * @param array $params
 *
 * @return string  Either '' (when collected to footer) OR legacy <script>...</script> fallback.
 */
function wpbc__calendar__load( $params = array() ) {

	$defaults = array(
		'resource_id'                     => '1',
		'aggregate_resource_id_arr'       => array(),
		'selected_dates_without_calendar' => '',
		'calendar_number_of_months'       => 1,
		'start_month_calendar'            => false,
		'shortcode_options'               => '',
		'custom_form'                     => 'standard',
		'calendar_dates_start'            => '',
		'calendar_dates_end'              => '',
	);
	$params = wp_parse_args( $params, $defaults );

	// Resource ID.
	$params['resource_id'] = absint( $params['resource_id'] );

	// Build JS BODY (no <script>, no ready wrapper).
	$js_body = '';

	$js_body .= wpbc__calendar__set_js_params__before_show( $params );

	// Show calendar.
	$js_body .= " wpbc_calendar_show(" . wp_json_encode( (string) $params['resource_id'] ) . "); ";

	// Security params.
	$js_body .= " _wpbc.set_secure_param('nonce', " . wp_json_encode( wp_create_nonce( 'wpbc_calendar_load_ajx' . '_wpbcnonce' ) ) . "); ";
	$js_body .= " _wpbc.set_secure_param('user_id', " . wp_json_encode( (string) wpbc_get_current_user_id() ) . "); ";
	$js_body .= " _wpbc.set_secure_param('locale', " . wp_json_encode( (string) get_user_locale() ) . "); ";

	// booking_hash (keep legacy GET behavior, sanitized).
	$get_booking_hash = '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_GET['booking_hash'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw_hash = wp_unslash( $_GET['booking_hash'] );
		if ( ! is_array( $raw_hash ) ) {
			$get_booking_hash = sanitize_text_field( (string) $raw_hash );
		}
	}

	// Aggregate type.
	$aggregate_type = 'all';
	if (
		( ! empty( $params['shortcode_options'] ) )
		&& function_exists( 'wpbc_parse_calendar_options__aggregate_param' )
	) {
		$aggregate_type_res = wpbc_parse_calendar_options__aggregate_param( $params['shortcode_options'] );
		if ( ( ! empty( $aggregate_type_res ) ) && ( ! empty( $aggregate_type_res['type'] ) ) ) {
			$aggregate_type = (string) $aggregate_type_res['type'];
		}
	}

	// request_uri captured on front-end (legacy rationale).
	$server_request_uri = '';
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$server_request_uri = sanitize_text_field( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) );
	}

	$params_for_request = array(
		'resource_id'               => $params['resource_id'],
		'booking_hash'              => $get_booking_hash,
		'request_uri'               => $server_request_uri,
		'custom_form'               => (string) $params['custom_form'],
		'aggregate_resource_id_str' => implode( ',', (array) $params['aggregate_resource_id_arr'] ),
		'aggregate_type'            => (string) $aggregate_type,
	);

	// Send Ajax request to load bookings.
	$js_body .= " wpbc_calendar__load_data__ajx(" . wp_json_encode( $params_for_request ) . "); ";

	// ---------------------------------------------------------------------
	// Elementor-safe path: add inline via WP scripts (footer-safe, not in content). // FixIn: 10.14.13.2.
	// ---------------------------------------------------------------------
	$can_use_wp_inline = ( ! wp_doing_ajax() );

	if ( $can_use_wp_inline && class_exists( 'WPBC_FE_Assets' ) ) {

		$assets_key = 'wpbc:cal-load:' . intval( $params['resource_id'] ) . ':' . md5( wp_json_encode( $params_for_request ) );

		// Try after main client script first (best), fallback to wpbc_all if needed.
		$added = WPBC_FE_Assets::add_jq_ready_js_to_wp_script( 'wpbc-main-client', $js_body, $assets_key );
		if ( ! $added ) {
			$added = WPBC_FE_Assets::add_jq_ready_js_to_wp_script( 'wpbc_all', $js_body, $assets_key );
		}

		if ( $added ) {
			return ''; // Important: nothing inline in the content.
		}
	}


	// ---------------------------------------------------------------------
	// Legacy fallback: return inline <script>...</script>
	// (works in rare contexts without wp_footer / without assets manager).
	// ---------------------------------------------------------------------
	$start_script_code  = '<script type="text/javascript"> ' . wpbc_jq_ready_start();
	$start_script_code .= $js_body;
	$start_script_code .= wpbc_jq_ready_end() . '</script>';

	return $start_script_code;
}



/**
 * Response to Ajax request,  about loading calendar data
 *
 * @return void
 */
function ajax_WPBC_AJX_CALENDAR_LOAD() {   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

	// Security  ------------------------------------------------------------------------------------------------------ // in Ajax Post:   'nonce': _wpbc.get_secure_param( 'nonce' ),
	$action_name    = 'wpbc_calendar_load_ajx' . '_wpbcnonce';
	$nonce_post_key = 'nonce';
	if ( wpbc_is_use_nonce_at_front_end() ) {           // FixIn: 10.1.1.2.
		$result_check = check_ajax_referer( $action_name, $nonce_post_key );
	}

	$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) )  ?  intval( $_REQUEST['wpbc_ajx_user_id'] )  :  wpbc_get_current_user_id();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

	/**
	 * SQL  ---------------------------------------------------------------------------
	 *
	 * in Ajax Post:  'search_params': _wpbc.search_get_all_params()
	 *
	 * Use prefix "search_params", if Ajax sent -    $_REQUEST['search_params']['page_num'],  $_REQUEST['search_params']['page_items_count'],  ...
	 */

	/**
	 * Using this class here only  for escaping variables
	 */
	$user_request = new WPBC_AJX__REQUEST( array(
											   'db_option_name'          => 'booking__wpbc_calendar_load__request_params',
											   'user_id'                 => $user_id,
											   'request_rules_structure' => array(
	                                'resource_id'    => array( 'validate' => 'd', 'default' => 1 ),                     // 'digit_or_csd'
	                                'booking_hash'   => array( 'validate' => 's', 'default' => '' ),
	                                'request_uri'    => array( 'validate' => 's', 'default' => '' ),
									'custom_form'    => array( 'validate' => 's', 'default' => 'standard' ),
									'aggregate_resource_id_str' => array( 'validate' => 'digit_or_csd', 'default' => '' ),        // Comma separated string of resource ID,  which was used in 'aggregate' parameter.
									'aggregate_type'            => array( 'validate' => 's', 'default' => 'all' ),                 //  'all' | 'bookings_only'   // FixIn: 9.8.15.10.
									'dates_to_check'            => array( 'validate' => 'array', 'default' => '' ),                 //  'all' | 'bookings_only'   // FixIn: 9.8.15.10.
																				 )
											)
					);
	$request_prefix = 'calendar_request_params';
	$request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 		    // NOT Direct: 	$_REQUEST['search_params']['resource_id']


	// Do Job  here !! -------------------------------------------------------------------------------------------------

	make_bk_action( 'check_pending_unpaid_bookings__do_auto_cancel', $request_params['resource_id'] );

	$skip_booking_id = wpbc_get_booking_id__from_hash_in_url( $request_params['booking_hash'] );                        // Booking ID  ( like 1101 )  to skip in calendar
	$max_days_count  = wpbc_get_max_visible_days_in_calendar();                          // 365  - number of visible days
	// Resource ID from  aggregate parameter  in booking shortcode!
	$aggregate_resource_id_arr = explode( ',', $request_params['aggregate_resource_id_str'] );
	$aggregate_resource_id_arr = array_filter( $aggregate_resource_id_arr );                                            // All entries of array equal to FALSE (0, '', '0' ) will be removed.
	$aggregate_resource_id_arr = array_unique( $aggregate_resource_id_arr );                                            // Remove duplicates    // FixIn: 9.8.15.10.
	if ( ( $resource_id_key = array_search( $request_params['resource_id'], $aggregate_resource_id_arr ) ) !== false ) {
		unset( $aggregate_resource_id_arr[ $resource_id_key ] );                                                        // Remove source booking resource  from  aggregate ARR. // FixIn: 9.8.15.10.
	}
	$aggregate_resource_id_arr = array_values($aggregate_resource_id_arr);                                              // Reset  keys

	$availability_per_days__params = array(
											'resource_id'     => $request_params['resource_id'],
											'max_days_count'  => $max_days_count,
											'skip_booking_id' => $skip_booking_id,
											'request_uri'     => $request_params['request_uri'],                        // It different in Ajax requests than $server_request_uri . It's used for change-over days to detect for exception at specific pages
											'custom_form'     => $request_params['custom_form']
											, 'additional_bk_types' => $aggregate_resource_id_arr                       // It is array  of booking resources from aggregate parameter()                                 // arrays | CSD | int       // OPTIONAL
											, 'aggregate_type' => $request_params['aggregate_type']                     // It is string: 'all' | 'bookings_only'                     // OPTIONAL
										//	, 'as_single_resource'  => true                                             // get dates as for 'single resource' or 'parent' resource including bookings in all 'child booking resources'
										//	, 'max_days_count'      => wpbc_get_max_visible_days_in_calendar()          // 365
//	, 'timeslots_to_check_intersect' => $timeslots_to_check_intersect  //TODO 2023-10-25: delete it, because we get it in wpbc_get_availability_per_days_arr()  //array( '12:20 - 12:55', '13:00 - 14:00' )
								);

	if ( ! empty( $request_params['dates_to_check'] ) ) {
		$availability_per_days__params['dates_to_check'] = $request_params['dates_to_check'];  // Usually  if defined it is: [ '2025-01-01', '2025-12-31' ],  because of shortcode [booking resource_id=4 calendar_dates_start='2025-01-01' calendar_dates_end='2025-12-31' ...]
	}

	// If in Booking > Add booking page we use in URL .../wp-admin/admin.php?page=wpbc-new&booking_type=2&as_single_resource=1   then get  availability  only for single resource.
	if (
			( ! empty( $request_params['request_uri'] ) )
	     && ( strpos( $request_params['request_uri'], 'as_single_resource=1' ) !== false )
	){
		$availability_per_days__params['as_single_resource'] = true;
	}

	/**
	 * Tricks:
	 *          if ( strpos( $request_params['request_uri'],'page=wpbc-new' ) !== false ) { $availability_per_days__params['is_days_always_available'] =  true; }
	 *
	 *          // Set  dates in calendar always available only  for specific resources with specific ID
	 *          if ( in_array( $request_params['resource_id'], array( 12, 15, 17 ) ) ) { $availability_per_days__params['is_days_always_available'] =  true; }
	 */

	// Get unavailable dates for calendar
	// $availability_per_days__params['dates_to_check'] = array( '2025-01-01', '2025-12-31' );  // FixIn: 10.13.1.4.
	$availability_per_days_arr = wpbc_get_availability_per_days_arr( $availability_per_days__params );


	$ajx_data_arr = array( /**
							 * Send JSON. It will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) ) .
							 * Pass JS OBJ: response_data in "jQuery.post( " function on success.
							 */
						   'ajx_data'           => $availability_per_days_arr,
						   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						   'ajx_search_params'  => $_REQUEST[ $request_prefix ],
						   'ajx_cleaned_params' => $request_params,
						   'resource_id'        => $request_params['resource_id'],
				);

	// FixIn: 10.12.4.6.
	if ( ! in_array( $request_params['resource_id'], $availability_per_days_arr['resources_id_arr__in_dates'] ) ) {
		$ajx_data_arr['ajx_data']['ajx_after_action_message'] = 'Wrong ID of booking resource. Probably calendar (booking resource) with ID = ' . intval( $request_params['resource_id'] ) . ' not exists. Please check resource_id parameter in the Booking Calendar shortcode.';
	}



																		// Show message under calendar,  if needed.
																		//$availability_per_days_arr['ajx_after_action_message'] = 'Ta Da :))';
																		//$availability_per_days_arr['ajx_after_action_message_status']  = 'warning';

	// Ajax ------------------------------------------------------------------------------------------------------------
	wp_send_json( $ajx_data_arr );

}

if (  is_admin() && ( defined( 'DOING_AJAX' ) ) && ( DOING_AJAX )  ) {
	add_action( 'wp_ajax_nopriv_' . 'WPBC_AJX_CALENDAR_LOAD', 'ajax_' . 'WPBC_AJX_CALENDAR_LOAD' );                     // Client         (not logged in)
	add_action( 'wp_ajax_'        . 'WPBC_AJX_CALENDAR_LOAD', 'ajax_' . 'WPBC_AJX_CALENDAR_LOAD' );                     // Logged In users  (or admin panel)
}

