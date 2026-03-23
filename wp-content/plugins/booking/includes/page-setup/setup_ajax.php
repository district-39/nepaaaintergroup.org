<?php /**
 * @version 1.0
 * @description Ajax and Requests Structure  for   WPBC_AJX__Setup__Ajax_Request
 * @category   Setup Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2023-06-23
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// =====================================================================================================================
// ==  Get RULES STRUCTURE  ==
// =====================================================================================================================

/**
* Get params names for escaping and/or default value of such  params
*
* @return array        array (  'resource_id'      => array( 'validate' => 'digit_or_csd',  	'default' => array( '1' ) )
*                             , ... )
*/
function wpbc_setup_wizard_page__request_rules_structure() {

	return array(
		'do_action' => array(
			'validate' => array(
								'none',
								'save_and_continue',
								'make_reset', 'skip_wizard',

								'save_and_continue__welcome',
								'save_and_continue__general_info',
								'save_and_continue__date_time_formats',
								'save_and_continue__bookings_types',
								'save_and_continue__form_structure',
								'load_form_template',
								'save_and_continue__cal_availability',
								'save_and_continue__color_theme',
								'save_and_continue__optional_other_settings',
								'save_and_continue__wizard_publish',
								'save_and_continue__get_started'
						),
			'default'  => 'none'
		),
		'current_step'          => array( 'validate' => 's', 'default' => '' ),
		'resource_id'           => array( 'validate' => 'd', 'default' => wpbc_get_default_resource() ),
		'ui_clicked_element_id' => array( 'validate' => 's', 'default' => '' )
		// 'calendar__booking_start_day_weeek' => array( 'validate' => array( '0', '1', '2', '3', '4', '5', '6' ), 'default'  => get_bk_option( 'booking_start_day_weeek' ) )
	);
}


	/**
	 * Get default params
	 *
	 * @return array        array (  'ui_wh_modification_date_radio' => 0
	 *                             , ... )
	 */
	function wpbc_setup_wizard_page__get__request_values__default(){

		$request_rules_structure = wpbc_setup_wizard_page__request_rules_structure();

		$default_params_arr = array();

		$structure_type = 'default';

		foreach ( $request_rules_structure as $key => $value ) {
			$default_params_arr[ $key ] = $value[ $structure_type ];
		}

		return $default_params_arr;
	}


// =====================================================================================================================
// ==  Get sanitised Request parameters  for  Ajax  ==
// =====================================================================================================================

/**
 * Get sanitised request parameters. |  01. -> Firstly check if user saved request params in user_meta DB.
 *                                   |  02. -> Otherwise check      $_REQUEST.
 *                                   |  03. -> Otherwise Get        default.
 *
 * @return array|false
 */
function wpbc_setup_wizard_page__get_cleaned_params__saved_request_default(){

	// User Specific Experience with Setup -> saved to user meta_table.
	// E.g. next  time user  open the page with  saved own settings
	$user_request = new WPBC_AJX__REQUEST( array(
											   'db_option_name'          => 'booking_setup_wizard_page_request_params',
											   'user_id'                 => wpbc_get_current_user_id(),
											   'request_rules_structure' => wpbc_setup_wizard_page__request_rules_structure()
											)
					);

	// -----------------------------------------------------------------------------------------------------------------
	// Get saved from DB
	// -----------------------------------------------------------------------------------------------------------------
	$escaped_request_params_arr = $user_request->get_sanitized__saved__user_request_params();


	// -----------------------------------------------------------------------------------------------------------------
	// Get $_REQUEST or Default      ::       This request was not saved before, then get sanitized direct parameters   , such as: 	$_REQUEST['resource_id']
	// -----------------------------------------------------------------------------------------------------------------
	if ( false === $escaped_request_params_arr ) {
		$request_prefix = false;
		$escaped_request_params_arr = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );
	}

	// -----------------------------------------------------------------------------------------------------------------
	// ==  O V E R R I D E    - DB params  by  the params from  REQUEST!  ==
	// -----------------------------------------------------------------------------------------------------------------
	$request_key = 'current_step';
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_REQUEST[ $request_key ] ) ) {

		 // Get SANITIZED REQUEST parameters together with default values
		$request_prefix = false;
		$url_request_params_arr = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 		// Direct: 	$_REQUEST['resource_id']

		// Now get only SANITIZED values that exist in REQUEST
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$url_request_params_only_arr = array_intersect_key( $url_request_params_arr, $_REQUEST );

		// And now override our DB  $escaped_request_params_arr  by  SANITIZED $_REQUEST values
		$escaped_request_params_arr  = wp_parse_args( $url_request_params_only_arr, $escaped_request_params_arr );
	}
	// ---------------------------------------------------------------------------------------------------------

	//	//MU
	//	if ( class_exists( 'wpdev_bk_multiuser' ) ) {
	//
	//		// Check if this MU user activated or super-admin,  otherwise show warning
	//		if ( ! wpbc_is_mu_user_can_be_here('activated_user') )
	//			return  false;
	//
	//		// Check if this MU user owner of this resource or super-admin,  otherwise show warning
	//		if ( ! wpbc_is_mu_user_can_be_here( 'resource_owner', $escaped_request_params_arr['resource_id'] ) ) {
	//			$default_values = $user_request->get_request_rules__default();
	//			$escaped_request_params_arr['resource_id'] = $default_values['resource_id'];
	//		}
	//	}

    return $escaped_request_params_arr;
}


// =====================================================================================================================
// ==  A J A X  ==
// =====================================================================================================================

class WPBC_AJX__Setup_Wizard__Ajax_Request {


	/**
	 * Define HOOKs for start  loading Ajax
	 */
	public function define_ajax_hook(){

		// Ajax Handlers.		Note. "locale_for_ajax" rechecked in wpbc-ajax.php
		add_action( 'wp_ajax_'		     . 'WPBC_AJX_SETUP_WIZARD_PAGE', array( $this, 'ajax_' . 'WPBC_AJX_SETUP_WIZARD_PAGE' ) );	    // Admin & Client (logged in usres)

		// Ajax Handlers for actions
		// add_action( 'wp_ajax_nopriv_' . 'WPBC_AJX_BOOKING_LISTING', array( $this, 'ajax_' . 'WPBC_AJX_BOOKING_LISTING' ) );	    // Client         (not logged in)
	}


	/**
	 * Ajax - Get Listing Data and Response to JS script
	 */
	public function ajax_WPBC_AJX_SETUP_WIZARD_PAGE() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['all_ajx_params'] ) || empty( $_POST['all_ajx_params'] ) ) { exit; }

		// -------------------------------------------------------------------------------------------------------------
		// ==  Security  ==         ->   in Ajax Post:   'nonce': wpbc_ajx_booking_listing.get_secure_param( 'nonce' )
		// -------------------------------------------------------------------------------------------------------------
		$action_name    = 'wpbc_setup_wizard_page_ajx' . '_wpbcnonce';
		$nonce_post_key = 'nonce';
		$result_check   = check_ajax_referer( $action_name, $nonce_post_key );

		$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) )  ?  intval( $_REQUEST['wpbc_ajx_user_id'] )  :  wpbc_get_current_user_id();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

		// -------------------------------------------------------------------------------------------------------------
		// ==  Request  ==          ->  $_REQUEST['all_ajx_params']['page_num'],   $_REQUEST['all_ajx_params']['page_items_count'], ...
		// -------------------------------------------------------------------------------------------------------------
		$user_request = new WPBC_AJX__REQUEST( array(
												   'db_option_name'          => 'booking_setup_wizard_page_request_params',
												   'user_id'                 => $user_id,
												   'request_rules_structure' => wpbc_setup_wizard_page__request_rules_structure()
												)
						);
		//--------------------------------------------------------------------------------------------------------------
		// If in Ajax:   all_ajx_params: _wpbc_settings.get_all_..()  ->  Use prefix "all_ajx_params"       THEN        Sanitize required REQUEST params
		//--------------------------------------------------------------------------------------------------------------
		$request_prefix = 'all_ajx_params';
		$cleaned_request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		// NOT Direct: 	$_REQUEST['all_ajx_params']['resource_id']
		//--------------------------------------------------------------------------------------------------------------

		$cleaned_data = array();
		$setup_steps  = new WPBC_SETUP_WIZARD_STEPS();

		$data_arr = array();
		$data_arr['ajx_after_action_message'] = '';
		$data_arr['ajx_after_action_result']  = 1;                  // Message Type:   ? '1' => 'success' : 'error'
		//--------------------------------------------------------------------------------------------------------------
		// Steps
		//--------------------------------------------------------------------------------------------------------------
		$data_arr['current_step']  = ( ! empty( $cleaned_request_params['current_step'] )
										? $cleaned_request_params['current_step']
										: $setup_steps->get_active_step_name() );       // e.g. 'general_info' or 'optional_other_settings'
		$data_arr['steps'] = $setup_steps->get_steps_arr();

		// -------------------------------------------------------------------------------------------------------------
		// Get Wizard history
		// -------------------------------------------------------------------------------------------------------------
		$booking_wizard_data_arr = get_bk_option( 'booking_wizard_data' );
		$booking_wizard_data_arr = ( empty( $booking_wizard_data_arr ) ) ? array() : $booking_wizard_data_arr;


		// =============================================================================================================
		// ==  Do Action  ==
		// =============================================================================================================
		switch ( $cleaned_request_params['do_action'] ) {

			// ---------------------------------------------------------------------------------------------------------
			// ==  RESET  ==
			// ---------------------------------------------------------------------------------------------------------
			case 'make_reset':

				$is_reseted = $user_request->user_request_params__db_delete();											// Delete from DB

				$cleaned_request_params['do_action'] = $is_reseted ? 'reset_done' : 'reset_error';

				$cleaned_request_params = wpbc_setup_wizard_page__get__request_values__default();

				$data_arr['ajx_after_action_message'] = __( 'Start Setup from Beginning', 'booking' );

				$data_arr['current_step'] = 'welcome';

				update_bk_option( 'booking_wizard_data', array() );

				$setup_steps->db__set_all_steps_as( false );        // Clear All Steps      Mark as Undone
				break;

			case 'skip_wizard':

				$data_arr['current_step'] = 'welcome';
				// $data_arr['redirect_url'] = wpbc_get_settings_url();
				$data_arr['redirect_url'] = wpbc_get_bookings_url();

				$setup_steps->db__set_all_steps_as( true );        // Mark All Steps as Done

				break;

			case 'save_and_continue__welcome':

				$setup_steps->db__set_step_as_completed( 'welcome' );
				break;

			case 'save_and_continue__general_info':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__general_info__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if ( 'On' === $cleaned_data['wpbc_swp_accept_send'] ) {
						//wpbc_setup_feedback__send_email( $cleaned_data );		// FixIn: 10.7.1.3.
						update_bk_option( 'booking_feedback__send_email', $cleaned_data );
					} else {
						delete_bk_option( 'booking_feedback__send_email' );
					}

					wpbc_setup__update__general_info( $cleaned_data );
				}

				$setup_steps->db__set_step_as_completed( 'general_info' );
				break;

			case 'save_and_continue__date_time_formats':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__date_time_formats__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__date_time_formats( $cleaned_data );
				}

				$setup_steps->db__set_step_as_completed( 'date_time_formats' );
				break;

			case 'save_and_continue__bookings_types':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__bookings_types__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__bookings_types( $cleaned_data );

					// FixIn: 10.7.1.3.
					$cleaned_data_booking_feedback_arr = get_bk_option( 'booking_feedback__send_email' );
					if (! empty($cleaned_data_booking_feedback_arr)){
						if ( 'On' === $cleaned_data_booking_feedback_arr['wpbc_swp_accept_send'] ) {
							$cleaned_data_booking_feedback_arr = array_merge( $cleaned_data_booking_feedback_arr , array( 'type' => 'Type: ' . $cleaned_data ['wpbc_swp_booking_types'] ) );
							if ( 'time_slots_appointments' === $cleaned_data ['wpbc_swp_booking_types'] ) {
								$cleaned_data_booking_feedback_arr = array_merge( $cleaned_data_booking_feedback_arr, array( 'appointments_type' => 'Appointment: ' . $cleaned_data ['wpbc_swp_booking_appointments_type'] ) );
							}
							wpbc_setup_feedback__send_email( $cleaned_data_booking_feedback_arr );
							update_bk_option( 'booking_feedback__after_send', $cleaned_data_booking_feedback_arr );
							delete_bk_option( 'booking_feedback__send_email' );
						}
					}

					// -------------------------------------------------------------------------------------------------
					// Save selected option  at the next  step  for paid versions
					// -------------------------------------------------------------------------------------------------
					$booking_wizard_data_arr[ 'load_form_template' ] = array();
					if ( class_exists( 'wpdev_bk_personal' ) ) {
						if ( 'full_days_bookings' === $cleaned_data['wpbc_swp_booking_types'] ) {
							$booking_wizard_data_arr['load_form_template'] ['wpbc_swp_booking_form_template_pro'] = 'pro|hints-dev';
						}
						if ( 'time_slots_appointments' === $cleaned_data['wpbc_swp_booking_types'] ) {
							if ( 'durationtime' === $cleaned_data['wpbc_swp_booking_appointments_type'] ) {
								$booking_wizard_data_arr['load_form_template'] ['wpbc_swp_booking_form_template_pro'] = 'pro|appointments_service_c';    // FixIn: 10.7.1.4.
							} else {
								$booking_wizard_data_arr['load_form_template'] ['wpbc_swp_booking_form_template_pro'] = 'pro|appointments30';    // FixIn: 10.7.1.4.
							}
						}
						if ( 'changeover_multi_dates_bookings' === $cleaned_data['wpbc_swp_booking_types'] ) {
							$booking_wizard_data_arr['load_form_template'] ['wpbc_swp_booking_form_template_pro'] = 'pro|wizard';
						}
					}
			        // -------------------------------------------------------------------------------------------------
				}

				$setup_steps->db__set_step_as_completed( 'bookings_types' );
				break;

			case 'save_and_continue__form_structure':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__form_structure__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__form_structure( $cleaned_data );
				}

				$setup_steps->db__set_step_as_completed( 'form_structure' );
				break;

			case 'load_form_template':

				if (
						( 'form_structure' === $data_arr['current_step'] )
				     && ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) )
				){
					$cleaned_data = wpbc_template__form_structure__action_validate_data( $_POST['all_ajx_params']['step_data'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__form_structure( $cleaned_data );
				}
				if (
						( 'cal_availability' === $data_arr['current_step'] )
				     && ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) )
				){
					$cleaned_data = wpbc_template__cal_availability__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__cal_availability( $cleaned_data );
				}
				if (
						( 'color_theme' === $data_arr['current_step'] )
				     && ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) )
				){
					$cleaned_data = wpbc_template__color_theme__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__color_theme( $cleaned_data );
				}

				break;

			case 'save_and_continue__cal_availability':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__cal_availability__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__cal_availability( $cleaned_data );
				}

				$setup_steps->db__set_step_as_completed( 'cal_availability' );
				break;

			case 'save_and_continue__color_theme':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__color_theme__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__color_theme( $cleaned_data );
				}

				$setup_steps->db__set_step_as_completed( 'color_theme' );
				break;

			case 'save_and_continue__optional_other_settings':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__optional_other_settings__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__optional_other_settings( $cleaned_data );
				}
				$setup_steps->db__set_step_as_completed( 'optional_other_settings' );
				break;

			case 'save_and_continue__wizard_publish':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__wizard_publish__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__wizard_publish( $cleaned_data );
				}
				$setup_steps->db__set_step_as_completed( 'wizard_publish' );
				break;

			case 'save_and_continue__get_started':

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
					$cleaned_data = wpbc_template__get_started__action_validate_data( $_POST['all_ajx_params']['step_data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wpbc_setup__update__get_started( $cleaned_data );
				}
				$setup_steps->db__set_step_as_completed( 'get_started' );
				break;


			default:
				// Default
		}

		//--------------------------------------------------------------------------------------------------------------
		// Other
		//--------------------------------------------------------------------------------------------------------------
		$data_arr['steps_is_done']               = $setup_steps->db__get_steps_is_done();
		$data_arr['left_navigation']             = wpbc_setup_wizard_page__get_left_navigation_menu_arr();
		$data_arr['plugin_menu__setup_progress'] = $setup_steps->get_plugin_menu_title__setup_progress();

		//--------------------------------------------------------------------------------------------------------------
		// Load Calendar depend on Step
		//--------------------------------------------------------------------------------------------------------------
		$data_arr['ui'] = array();

		switch ( $data_arr['current_step'] ) {

			case 'form_structure':
				$data_arr['calendar_force_load'] = '';

				if ( 'save_and_continue__bookings_types' === $cleaned_request_params['do_action'] ) {
					// We need to  reload the calendar skins,  because at  the previous step 'Booking Types' we updated the calendar skins relative to  selected options
					ob_start();
					?>
					<script type="text/javascript">
						jQuery( document ).ready( function () {
							wpbc__calendar__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL  . get_bk_option( 'booking_skin' ) ); ?>' );
							wpbc__css__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL . get_bk_option( 'booking_timeslot_picker_skin' ) ); ?>' );
						} );
					</script><?php
					$data_arr['calendar_force_load'] .= ob_get_clean();
				}
				$data_arr['calendar_force_load'] .= wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );
				break;

			case 'cal_availability':

				$data_arr['calendar_force_load'] = wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );

				// -----------------------------------------------------------------------------------------------------
				// ==  UNAVAILABLE  WeekDays  ==
				// -----------------------------------------------------------------------------------------------------
				$data_arr['ui']['booking_unavailable_day'] = array();
				for ( $wdi = 0; $wdi < 7; $wdi ++ ) {
					if ( get_bk_option( 'booking_unavailable_day' . $wdi ) == 'On' ) {
						$data_arr['ui']['booking_unavailable_day'][] = $wdi;
					}
				}

				/**
				 * $unavailable_from_today_arr = [
				 *                    booking_unavailable_days_num_from_today = "540m"
				 *                    booking_unavailable_days_num_from_today__hint = ": 2025-01-25 15:19 - 2025-01-26 00:18:42"
				 *                    booking_available_days_num_from_today = "0"
				 *                    booking_available_days_num_from_today__hint = ": 26 Jan, 2025 - ..."
				 *                  ]
				 */
				$unavailable_from_today_arr = wpbc_get_unavailable_from_today_hints_arr();

				$data_arr['ui']['booking_unavailable_days_num_from_today']       = $unavailable_from_today_arr['booking_unavailable_days_num_from_today'];
				$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = $unavailable_from_today_arr['booking_unavailable_days_num_from_today__hint'];
				$data_arr['ui']['booking_available_days_num_from_today']         = $unavailable_from_today_arr['booking_available_days_num_from_today'];
				$data_arr['ui']['booking_available_days_num_from_today__hint']   = $unavailable_from_today_arr['booking_available_days_num_from_today__hint'];
if(0){
				// -----------------------------------------------------------------------------------------------------
				// ==  UNAVAILABLE  Today days  ==
				// -----------------------------------------------------------------------------------------------------
				// FixIn: 10.8.1.4.
				$last_unavailable_date = '';
				if ( 'm' === substr( get_bk_option( 'booking_unavailable_days_num_from_today' ), - 1 ) ) {
					// -------------------------------------------------------------------------------------------------
					// == Minutes ==
					// -------------------------------------------------------------------------------------------------
					$data_arr['ui']['booking_unavailable_days_num_from_today'] = intval( get_bk_option( 'booking_unavailable_days_num_from_today' ) );

					// Hints.
					$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = ': <span style="text-transform: lowercase;font-size:0.9em;">'
																													. __( 'None', 'booking' ) . '</span>';
					if ( ! empty( $data_arr['ui']['booking_unavailable_days_num_from_today'] ) ) {

						$start_date_unix         = strtotime( 'now' );
						$todate_with_wp_timezone = wpbc_datetime_localized__use_wp_timezone( gmdate( 'Y-m-d H:i:s', $start_date_unix ), 'Y-m-d H:i' );

						$start_date_unix           = strtotime( '+' . ( intval( $data_arr['ui']['booking_unavailable_days_num_from_today'] ) - 1 ) . ' minutes' );
						$max_date_with_wp_timezone = wpbc_datetime_localized__use_wp_timezone( gmdate( 'Y-m-d H:i:s', $start_date_unix ), 'Y-m-d H:i:s' );

						$last_unavailable_date = $max_date_with_wp_timezone;

						$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = ': ' . $todate_with_wp_timezone . ' - ' . $max_date_with_wp_timezone;        // FixIn: 10.9.4.2.
					}
					$data_arr['ui']['booking_unavailable_days_num_from_today'] .= 'm';
				} else {
					// -------------------------------------------------------------------------------------------------
					// == Days ==
					// -------------------------------------------------------------------------------------------------
					$data_arr['ui']['booking_unavailable_days_num_from_today'] = intval( get_bk_option( 'booking_unavailable_days_num_from_today' ) );

					// Hints.
					$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = ': <span style="text-transform: lowercase;font-size:0.9em;">' . __( 'None', 'booking' ) . '</span>';

					if ( 1 === $data_arr['ui']['booking_unavailable_days_num_from_today'] ) {
						$last_unavailable_date = wp_date( 'Y-m-d 00:00:00' );
						$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = ': ' . wp_date( 'd M', strtotime( $last_unavailable_date ) );
					}
					if ( $data_arr['ui']['booking_unavailable_days_num_from_today'] > 1 ) {
						$last_unavailable_date = wp_date( 'Y-m-d 00:00:00', strtotime( '+' . ( $data_arr['ui']['booking_unavailable_days_num_from_today'] - 1 ) . ' days' ) );
						$data_arr['ui']['booking_unavailable_days_num_from_today__hint'] = ': ' . wp_date( 'd M' ) . ' - ' . wp_date( 'd M', strtotime( $last_unavailable_date ) );
					}
				}

				// -----------------------------------------------------------------------------------------------------
				// ==  AVAILABLE  Today days  ==
				// -----------------------------------------------------------------------------------------------------
				// if ( class_exists( 'wpdev_bk_biz_m' ) ) { .
				$data_arr['ui']['booking_available_days_num_from_today'] = esc_js( get_bk_option( 'booking_available_days_num_from_today' ) );

				// Hints.
				// $start_available_date = ( '' === $last_unavailable_date ) ? wp_date( 'Y-m-d 00:00:00' ) : wp_date( 'Y-m-d 00:00:00', strtotime( '+1 day', strtotime( $last_unavailable_date ) ) );
				if ( '' === $last_unavailable_date ) {
					$start_date_unix         = strtotime( 'now' );
					$todate_with_wp_timezone = wpbc_datetime_localized__use_wp_timezone( gmdate( 'Y-m-d H:i:s', $start_date_unix ), 'Y-m-d 00:00:00' );
					$start_available_date    = $todate_with_wp_timezone;
				} else {
					$start_available_date = $last_unavailable_date;
					// We use here with  no WP timezone, because timezone already  applied to $last_unavailable_date.
					$start_available_date = wpbc_datetime_localized__no_wp_timezone( strtotime( $last_unavailable_date ), 'Y-m-d 00:00:00' );
				}


				if ( empty( $data_arr['ui']['booking_available_days_num_from_today'] ) ) {
					$last_available_date = '';
				} else {
					// $last_available_date = wp_date( 'Y-m-d 00:00:00', strtotime( '+' . ( $data_arr['ui']['booking_available_days_num_from_today'] ) . ' days' ) );
					// FixIn: 10.9.6.3.
					$start_date_unix = strtotime( '+' . ( intval( $data_arr['ui']['booking_available_days_num_from_today'] ) - 1 ) . ' days' );
					$last_available_date = wpbc_datetime_localized__use_wp_timezone( gmdate( 'Y-m-d H:i:s', $start_date_unix ), 'Y-m-d 00:00:00' );
				}

				if ( ! empty( $data_arr['ui']['booking_available_days_num_from_today'] ) ) {

					if ( strtotime( $start_available_date ) < strtotime( $last_available_date ) ) {

						$data_arr['ui']['booking_available_days_num_from_today__hint'] = ': ' . wp_date( 'd M, Y', strtotime( $start_available_date ) ) . ' - ' . wp_date( 'd M, Y', strtotime( $last_available_date ) );
					} else if ( strtotime( $start_available_date ) == strtotime( $last_available_date ) ) {
						$data_arr['ui']['booking_available_days_num_from_today__hint'] = ': ' . wp_date( 'd M, Y', strtotime( $start_available_date ) );
					} else {
						$data_arr['ui']['booking_available_days_num_from_today__hint'] = ': <span style="text-transform: uppercase;font-size:1.1em;">' . esc_html__( 'None', 'booking' ) . '</span><br>' .
																						 ' <span style="text-transform: lowercase;font-size:0.9em;color:#cc3a5f;">' .
																						 'Start available' . ': ' . wp_date( 'd M, Y', strtotime( $start_available_date ) ) . '<br>' .
																						 'Last available' . ': ' . wp_date( 'd M, Y', strtotime( $last_available_date ) ) . '</span>';
					}
				} else {
					$data_arr['ui']['booking_available_days_num_from_today__hint'] = ': ' . wp_date( 'd M, Y', strtotime( $start_available_date ) ) . ' - ...';
				}
}

				$data_arr['ui']['booking_unavailable_extra_in_out']      = get_bk_option( 'booking_unavailable_extra_in_out' );
				$data_arr['ui']['booking_unavailable_extra_minutes_in']  = get_bk_option( 'booking_unavailable_extra_minutes_in' );
				$data_arr['ui']['booking_unavailable_extra_minutes_out'] = get_bk_option( 'booking_unavailable_extra_minutes_out' );
				$data_arr['ui']['booking_unavailable_extra_days_in']     = get_bk_option( 'booking_unavailable_extra_days_in' );
				$data_arr['ui']['booking_unavailable_extra_days_out']    = get_bk_option( 'booking_unavailable_extra_days_out' );

				break;

			case 'color_theme':

				$data_arr['calendar_force_load'] = wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );
				$data_arr['ui']['booking_form_theme']           = get_bk_option( 'booking_form_theme' );
				$data_arr['ui']['booking_skin']                 = get_bk_option( 'booking_skin' );
				$data_arr['ui']['booking_timeslot_picker_skin'] = get_bk_option( 'booking_timeslot_picker_skin' );
				break;

			case 'optional_other_settings':
				 $data_arr['calendar_force_load'] = '';//wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );
				break;

			case 'wizard_publish':
				 $data_arr['calendar_force_load'] = '';//wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );
				break;

			case 'get_started':
				 $data_arr['calendar_force_load'] = '';//wpbc_setup_wizard_page__get_shortcode_html( $cleaned_request_params['resource_id'] );
				break;

			default:
				$data_arr['calendar_force_load'] = '';
		}


		// -------------------------------------------------------------------------------------------------------------
		// Save Wizard history
		// -------------------------------------------------------------------------------------------------------------
		// Save to DB
		$booking_wizard_data_arr[ $cleaned_request_params['do_action'] ] = $cleaned_data;
		update_bk_option( 'booking_wizard_data', $booking_wizard_data_arr );
		// Ajax Transfer
		$data_arr['booking_wizard_data'] = $booking_wizard_data_arr;
		// -------------------------------------------------------------------------------------------------------------

//TODO: delete this ?
if(0){

		$data_arr['customize_steps'] = array();
		$data_arr['customize_steps']['action']    = 'none';

		// Actions =================================================================================================

		if ( 'save_calendar_additional' == $cleaned_request_params['do_action'] ) {

			$is_updated = update_bk_option( 'booking_max_monthes_in_calendar',  $cleaned_request_params['calendar__booking_max_monthes_in_calendar'] );
			$is_updated = update_bk_option( 'booking_start_day_weeek',          $cleaned_request_params['calendar__booking_start_day_weeek'] );
		}

        //----------------------------------------------------------------------------------------------------------

		// Get booking resources (sql)
		$resources_arr = wpbc_ajx_get_all_booking_resources_arr();          /**
																			 * Array (   [0] => Array (     [booking_type_id] => 1
																											[title] => Standard
																											[users] => 1
																											[import] =>
																											[export] =>
																											[cost] => 25
																											[default_form] => standard
																											[prioritet] => 0
																											[parent] => 0
																											[visitors] => 2
																				), ...                  */

		$resources_arr_sorted = wpbc_ajx_get_sorted_booking_resources_arr( $resources_arr );

		$data_arr['ajx_booking_resources'] = $resources_arr_sorted;
}


		// -------------------------------------------------------------------------------------------------------------
		// Save Status of Wizard for specific user
		// -------------------------------------------------------------------------------------------------------------
		if ( 'make_reset' !== $cleaned_request_params['do_action'] ) {

			$request_params_to_save = $cleaned_request_params;

			// Do not safe such elements
			unset( $request_params_to_save['ui_clicked_element_id'] );
			unset( $request_params_to_save['do_action'] );
			unset( $request_params_to_save['calendar_force_load'] );
			unset( $request_params_to_save['plugin_menu__setup_progress'] );

			$is_success_update = $user_request->user_request_params__db_save( $request_params_to_save );					// Save to DB		// - $cleaned_request_params - serialized here automatically
		}

		if ( ! empty( $data_arr['calendar_force_load'] ) ) {
			$data_arr['calendar_force_load'] = wpbc_clean_calendar_loading_scripts( $data_arr['calendar_force_load'] );
		}
		// -------------------------------------------------------------------------------------------------------------
		// Send JSON.   It will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) )		Pass JS OBJ: response_data in "jQuery.post( " function on success.
		// -------------------------------------------------------------------------------------------------------------
		wp_send_json( array(
							'ajx_data'              => $data_arr,
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							'ajx_all_ajx_params'    => $_REQUEST[ $request_prefix ],			 					    // $_REQUEST[ 'all_ajx_params' ]
							'ajx_cleaned_params'    => $cleaned_request_params
						) );
	}

}

/**
 * Just for loading CSS and  JavaScript files
 */
if ( true ) {
	$wpbc_setup_wizard_page_loading = new WPBC_AJX__Setup_Wizard__Ajax_Request;
	$wpbc_setup_wizard_page_loading->define_ajax_hook();
}