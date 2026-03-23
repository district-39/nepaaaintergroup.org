<?php
/**
 * Front-End Render (Booking form, Calendar)
 *
 * File: /wp-content/plugins/booking/includes/fontend/class-fe-render.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPBC_FE_Render {

	/**
	 * Echo or return helper (to preserve legacy API behavior).
	 *
	 * @param string $html - already escaped/sanitizing HTML content.
	 * @param bool   $is_echo - do we need to echo it?.
	 *
	 * @return string|void
	 */
	private static function echo_or_return( $html, $is_echo ) {

		if ( $is_echo ) {

			echo $html;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			return;
		}

		return $html;
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Render Booking Form (Calendar + Form).
	 *
	 * @param array $params_arr
	 *
	 * @return string|void
	 */
	public static function render_booking_form( $params_arr = array() ) {

		// Default parameters (keep identical to legacy).
		$default_params = array(
			'resource_id'                     => 1,
			'cal_count'                       => 1,
			'is_echo'                         => 1,
			'custom_booking_form'             => 'standard',
			'selected_dates_without_calendar' => '',
			'start_month_calendar'            => false,
			'bk_otions'                       => '',
			'calendar_dates_start'            => '',
			'calendar_dates_end'              => '',
			'form_status'                     => 'published',
		);
		$params_arr     = wp_parse_args( $params_arr, $default_params );
		$is_echo        = ( ! empty( $params_arr['is_echo'] ) );

		// ---------------------------------------------------------------------
		// Hash resolution, e.g. $_GET['booking_hash'] for Booking Form rendering.
		// ---------------------------------------------------------------------
		$hash_res = WPBC_FE_Attr_Postprocessor::resolve_booking_hash_and_maybe_get_parent_resource_id( $params_arr, 'form' );
		if ( ! $hash_res['ok'] ) {
			return self::echo_or_return( $hash_res['error_html'], $is_echo );
		}
		$params_arr = $hash_res['params_arr'];

		// ---------------------------------------------------------------------
		// Normalize aggregate resource IDs. - "5;3;9" -> [ 'resource_id' => 5, 'aggregate_resource_id_arr' => [5,3,9] ].
		// ---------------------------------------------------------------------
		$aggregate_resource_id_arr = array();
		$split                     = WPBC_FE_Attr_Postprocessor::split_aggregate_resource_id( $params_arr['resource_id'] );
		$params_arr['resource_id'] = $split['resource_id'];
		$aggregate_resource_id_arr = $split['aggregate_resource_id_arr'];

		// ---------------------------------------------------------------------
		// Validate resource_id parameter and check if booking resource exists.
		// ---------------------------------------------------------------------
		$resource_validation = WPBC_FE_Attr_Postprocessor::validate_resource_id( $params_arr['resource_id'] );
		if ( true !== $resource_validation ) {
			return self::echo_or_return( $resource_validation, $is_echo );
		}

		// ---------------------------------------------------------------------
		// == MU ==
		// ---------------------------------------------------------------------
		make_bk_action( 'check_multiuser_params_for_client_side', $params_arr['resource_id'] );

		// ---------------------------------------------------------------------
		// Normalize calendar_dates_start/end.
		// ---------------------------------------------------------------------
		$calendar_dates_range = WPBC_FE_Attr_Postprocessor::normalize_calendar_dates_range( $params_arr['calendar_dates_start'], $params_arr['calendar_dates_end'] );

		// ---------------------------------------------------------------------
		// Calendar load params (keep identical keys).
		// ---------------------------------------------------------------------
		$calendar_load_params = array(
			'resource_id'                     => $params_arr['resource_id'],
			'aggregate_resource_id_arr'       => $aggregate_resource_id_arr,
			'selected_dates_without_calendar' => $params_arr['selected_dates_without_calendar'],
			'calendar_number_of_months'       => $params_arr['cal_count'],
			'start_month_calendar'            => $params_arr['start_month_calendar'],
			'shortcode_options'               => $params_arr['bk_otions'],
			'custom_form'                     => $params_arr['custom_booking_form'],
			'calendar_dates_start'            => $calendar_dates_range['start'],
			'calendar_dates_end'              => $calendar_dates_range['end'],
		);

		$start_script_code = wpbc__calendar__load( $calendar_load_params );

		// Disable booked time slots when selected date is predefined (legacy behavior).
		if ( '' !== $params_arr['selected_dates_without_calendar'] ) {
			$assets_key = 'wpbc:disable_time_fields_in_booking_form:' . intval( $params_arr['resource_id'] );
			$start_script_code .= WPBC_FE_Assets::add_jq_ready_js( 'wpbc_disable_time_fields_in_booking_form(' . intval( $params_arr['resource_id'] ) . ')', $assets_key, 'footer' );
		}

		// ---------------------------------------------------------------------
		// For Phase #1 we still use legacy HTML builder for the form body. (Later phases will move it fully.)
		// ---------------------------------------------------------------------
		$legacy = wpbc_get_legacy_booking_instance();
		if ( ! $legacy ) {
			$err = '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>' . esc_html__( 'Oops!', 'booking' ) . '</strong> ' . esc_html__( 'Booking Calendar front-end renderer is not available. Please contact support.', 'booking' ) . '</div></div></div>';
			return self::echo_or_return( $err, $is_echo );
		}

		$form_html = WPBC_FE_Form_Body_Renderer::render(
			array(
				'resource_id'                     => $params_arr['resource_id'],
				'custom_booking_form'             => $params_arr['custom_booking_form'],
				'form_status'                     => $params_arr['form_status'],
				'selected_dates_without_calendar' => $params_arr['selected_dates_without_calendar'],
				'cal_count'                       => $params_arr['cal_count'],
				'bk_otions'                       => $params_arr['bk_otions'],
				'legacy_instance'                 => $legacy,
			)
		);

		$my_result = ' ' . $form_html . ' ' . $start_script_code;


		// Add DIV structure, where to show payment form.
		$my_result = apply_filters( 'wpdev_booking_form', $my_result, $params_arr['resource_id'] );                     // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		// == MU ==
		make_bk_action( 'finish_check_multiuser_params_for_client_side', $params_arr['resource_id'] );

		return self::echo_or_return( $my_result, $is_echo );
	}


	/**
	 * Render Availability Calendar Only.
	 *
	 * @param array $params_arr
	 *
	 * @return string|void
	 */
	public static function render_calendar_only( $params_arr = array() ) {

		$default_params = array(
			'resource_id'          => 1,
			'cal_count'            => 1,
			'is_echo'              => 1,
			'start_month_calendar' => false,
			'bk_otions'            => '',
			'calendar_dates_start' => '',
			'calendar_dates_end'   => '',
		);
		$params_arr     = wp_parse_args( $params_arr, $default_params );
		$is_echo        = ( ! empty( $params_arr['is_echo'] ) );

		// ---------------------------------------------------------------------
		// Hash resolution, e.g. $_GET['booking_hash'] for Booking Form rendering.
		// ---------------------------------------------------------------------
		$hash_res = WPBC_FE_Attr_Postprocessor::resolve_booking_hash_and_maybe_get_parent_resource_id( $params_arr, 'calendar' );
		if ( ! $hash_res['ok'] ) {
			return self::echo_or_return( $hash_res['error_html'], $is_echo );
		}
		$params_arr = $hash_res['params_arr'];

		// ---------------------------------------------------------------------
		// Normalize aggregate resource IDs. - "5;3;9" -> [ 'resource_id' => 5, 'aggregate_resource_id_arr' => [5,3,9] ].
		// ---------------------------------------------------------------------
		$aggregate_resource_id_arr = array();
		$split                     = WPBC_FE_Attr_Postprocessor::split_aggregate_resource_id( $params_arr['resource_id'] );
		$params_arr['resource_id'] = $split['resource_id'];
		$aggregate_resource_id_arr = $split['aggregate_resource_id_arr'];


		// ---------------------------------------------------------------------
		// Validate resource_id parameter and check if booking resource exists.
		// ---------------------------------------------------------------------
		$resource_validation = WPBC_FE_Attr_Postprocessor::validate_resource_id( $params_arr['resource_id'] );
		if ( true !== $resource_validation ) {
			return self::echo_or_return( $resource_validation, $is_echo );
		}

		// ---------------------------------------------------------------------
		// == MU ==
		// ---------------------------------------------------------------------
		make_bk_action( 'check_multiuser_params_for_client_side', $params_arr['resource_id'] );

		// ---------------------------------------------------------------------
		// Normalize calendar_dates_start/end.
		// ---------------------------------------------------------------------
		$calendar_dates_range = WPBC_FE_Attr_Postprocessor::normalize_calendar_dates_range( $params_arr['calendar_dates_start'], $params_arr['calendar_dates_end'] );

		$calendar_load_params = array(
			'resource_id'                     => $params_arr['resource_id'],
			'aggregate_resource_id_arr'       => $aggregate_resource_id_arr,
			'selected_dates_without_calendar' => '',
			'calendar_number_of_months'       => $params_arr['cal_count'],
			'start_month_calendar'            => $params_arr['start_month_calendar'],
			'shortcode_options'               => $params_arr['bk_otions'],
			'custom_form'                     => 'standard',                             // Because we show only  'AVAILABILITY CALENDAR' without the form,  at all.
			'calendar_dates_start'            => $calendar_dates_range['start'],
			'calendar_dates_end'              => $calendar_dates_range['end'],
		);
		$start_script_code    = wpbc__calendar__load( $calendar_load_params );


		$my_result  = '<div style="clear:both;height:10px;"></div>';
		$my_result .= wpbc_pre_get_calendar_html( $params_arr['resource_id'], $params_arr['cal_count'], $params_arr['bk_otions'] );
		$my_result .= ' ' . $start_script_code;

		// Keep legacy filter.
		$my_result = apply_filters( 'wpdev_booking_calendar', $my_result, $params_arr['resource_id'] );                 // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$booking_form_is_using_bs_css = get_bk_option( 'booking_form_is_using_bs_css' );
		$my_result                    = '<span ' . ( ( 'On' === $booking_form_is_using_bs_css ) ? 'class="wpdevelop"' : '' ) . '>' . $my_result . '</span>';

		// == MU ==
		make_bk_action( 'finish_check_multiuser_params_for_client_side', $params_arr['resource_id'] );

		return self::echo_or_return( $my_result, $is_echo );
	}

}


/**
 * Try to get legacy wpdev_booking instance (for Phase #1 fallback calls).
 *
 * @return wpdev_booking|null
 */
function wpbc_get_legacy_booking_instance() {

	if ( ! function_exists( 'WPBC' ) ) {
		return null;
	}

	$wpbc_instance = WPBC();
	if ( ! $wpbc_instance ) {
		return null;
	}

	if ( empty( $wpbc_instance->booking_obj ) && class_exists( 'wpdev_booking' ) ) {
		// Lazy boot as a last resort (avoid doing this if you don’t want any boot during true WPBC ajax).
		$wpbc_instance->booking_obj = new wpdev_booking();
	}

	return ( $wpbc_instance->booking_obj instanceof wpdev_booking ) ? $wpbc_instance->booking_obj : null;
}



//TODO: replace evrywhere wpbc_get_rendered_booking_form_html() to  some simpler call.

/**
 * Get HTML of Rendered Booking From - Legacy  type of calling render.
 *
 * @param $resource_id
 * @param $cal_count
 * @param $is_echo
 * @param $my_booking_form
 * @param $my_selected_dates_without_calendar
 * @param $start_month_calendar
 * @param $bk_otions
 *
 * @return string|null
 */
function wpbc_get_rendered_booking_form_html( $resource_id = 1, $cal_count = 1, $is_echo = 1, $my_booking_form = 'standard', $my_selected_dates_without_calendar = '', $start_month_calendar = false, $bk_otions = array() ) {

	return WPBC_FE_Render::render_booking_form(
		array(
			'resource_id'                     => $resource_id,
			'cal_count'                       => $cal_count,
			'is_echo'                         => $is_echo,
			'custom_booking_form'             => $my_booking_form,
			'selected_dates_without_calendar' => $my_selected_dates_without_calendar,
			'start_month_calendar'            => $start_month_calendar,
			'bk_otions'                       => $bk_otions,
		)
	);
}

/**
 * Get booking form rendered HTML content without echo.
 *
 * @param $resource_id
 * @param $cal_count
 * @param $my_booking_form
 * @param $my_selected_dates_without_calendar
 * @param $start_month_calendar
 * @param $bk_otions
 *
 * @return string|null
 */
function wpbc_get_rendered_booking_form_html__noecho( $resource_id = 1, $cal_count = 1, $my_booking_form = 'standard', $my_selected_dates_without_calendar = '', $start_month_calendar = false, $bk_otions = array() ) {

	$my_result = WPBC_FE_Render::render_booking_form(
		array(
			'resource_id'                     => $resource_id,
			'cal_count'                       => $cal_count,
			'is_echo'                         => 0,
			'custom_booking_form'             => $my_booking_form,
			'selected_dates_without_calendar' => $my_selected_dates_without_calendar,
			'start_month_calendar'            => $start_month_calendar,
			'bk_otions'                       => $bk_otions,
		)
	);
	return $my_result;
}

/**
 * This hook exists in personal.php  file for the [bookingselect ..]
 */
add_bk_filter( 'wpdevbk_get_booking_form', 'wpbc_get_rendered_booking_form_html__noecho' );