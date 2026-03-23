<?php
/**
 * Front-End Shortcodes (Phase #1)
 *
 * File: /wp-content/plugins/booking/includes/fontend/class-fe-shortcodes.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPBC_FE_Shortcodes {

	/**
	 * Boot.
	 */
	public static function init() {

		// Register after legacy (legacy uses init priority 9999).
		add_action( 'init', array( __CLASS__, 'register_shortcodes' ), 10000 );
	}

	/**
	 * Register the shortcodes we move in Phase #1.
	 */
	public static function register_shortcodes() {

		add_shortcode( 'booking', array( __CLASS__, 'booking_shortcode' ) );
		add_shortcode( 'bookingcalendar', array( __CLASS__, 'booking_calendar_only_shortcode' ) );
		add_shortcode( 'bookingform', array( __CLASS__, 'bookingform_shortcode' ) );
	}

	// ---------------------------------------------------------------------
	// Helpers
	// ---------------------------------------------------------------------

	private static function force_attr_array( $attr ) {
		return is_array( $attr ) ? $attr : array();
	}

	private static function maybe_escape_shortcode_params( $attr ) {

		// This function sanitize each value in attr array  by  using WP function: sanitize_text_field().
		$attr = wpbc_escape_shortcode_params( $attr );

		return is_array( $attr ) ? $attr : array();
	}

	// ---------------------------------------------------------------------
	// Shortcodes
	// ---------------------------------------------------------------------

	/**
	 * Shortcode [booking ...]
	 *
	 * @param array $attr
	 *
	 * @return string
	 */
	public static function booking_shortcode( $attr ) {

		if ( wpbc_is_on_edit_page() ) {
			return wpbc_get_preview_for_shortcode( 'booking', $attr );
		}

		if ( WPBC_GET_Request::has_non_empty_get( 'booking_hash' ) ) {
			/* translators: 1: URL  to FAQ page. */
			return __( 'You need to use special shortcode [bookingedit] for booking editing.', 'booking' ) . ' ' . sprintf( __( 'Please check FAQ instruction how to configure it here %s', 'booking' ), '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/">https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/</a>' );
		}

		$attr = self::force_attr_array( $attr );
		$attr = self::maybe_escape_shortcode_params( $attr );

		// Parse, sanitize and normilize shortcode paramaters.
		$shortcode_params = array(
			'is_echo'                         => 0,
			'resource_id'                     => WPBC_FE_Shortcode_Params::get_from_attr__resource_id_with_aggregate( $attr, WPBC_FE_Attr_Postprocessor::get_default_booking_resource_id() ),
			'cal_count'                       => WPBC_FE_Shortcode_Params::get_from_attr__months_count( $attr, 1 ),
			'start_month_calendar'            => WPBC_FE_Shortcode_Params::get_from_attr__start_month( $attr, false ),// [ year, month ] | false.
			'calendar_dates_start'            => WPBC_FE_Shortcode_Params::get_from_attr__calendar_dates_start( $attr, '' ),
			'calendar_dates_end'              => WPBC_FE_Shortcode_Params::get_from_attr__calendar_dates_end( $attr, '' ),
			'selected_dates_without_calendar' => '',
			'custom_booking_form'             => WPBC_FE_Shortcode_Params::get_from_attr__custom_form( $attr, 'standard' ),
			'bk_otions'                       => WPBC_FE_Shortcode_Params::get_from_attr__options( $attr, '' ),
			'form_status'                     => WPBC_FE_Shortcode_Params::get_from_attr__form_status( $attr, 'published' ),
		);

		$shortcode_result = WPBC_FE_Render::render_booking_form( $shortcode_params );

		return $shortcode_result;
	}


	/**
	 * Shortcode [bookingcalendar ...]
	 *
	 * @param array $attr
	 *
	 * @return string
	 */
	public static function booking_calendar_only_shortcode( $attr ) {

		if ( wpbc_is_on_edit_page() ) {
			return wpbc_get_preview_for_shortcode( 'bookingcalendar', $attr );
		}

		if ( WPBC_GET_Request::has_non_empty_get( 'booking_hash' ) ) {
			/* translators: 1: URL  to FAQ page. */
			return __( 'You need to use special shortcode [bookingedit] for booking editing.', 'booking' ) . ' ' . sprintf( __( 'Please check FAQ instruction how to configure it here %s', 'booking' ), '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/">https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/</a>' );
		}

		$attr = self::force_attr_array( $attr );
		$attr = self::maybe_escape_shortcode_params( $attr );

		$resource_id = WPBC_FE_Shortcode_Params::get_from_attr__primary_resource_id( $attr, WPBC_FE_Attr_Postprocessor::get_default_booking_resource_id() );

		// Parse, sanitize and normilize shortcode paramaters.
		$shortcode_params = array(
			'is_echo'                         => 0,
			'resource_id'                     => WPBC_FE_Shortcode_Params::get_from_attr__resource_id_with_aggregate( $attr, WPBC_FE_Attr_Postprocessor::get_default_booking_resource_id() ),
			'cal_count'                       => WPBC_FE_Shortcode_Params::get_from_attr__months_count( $attr, 1 ),
			'start_month_calendar'            => WPBC_FE_Shortcode_Params::get_from_attr__start_month( $attr, false ),  // [ year, month ] | false.
			'calendar_dates_start'            => WPBC_FE_Shortcode_Params::get_from_attr__calendar_dates_start( $attr, '' ),
			'calendar_dates_end'              => WPBC_FE_Shortcode_Params::get_from_attr__calendar_dates_end( $attr, '' ),
			'bk_otions'                       => WPBC_FE_Shortcode_Params::get_from_attr__options( $attr, '' ),
			'selected_dates_without_calendar' => '',
			'custom_booking_form'             => '', // WPBC_FE_Shortcode_Params::get_from_attr__custom_form( $attr, 'standard' ), //.
		);

		$shortcode_calendar_html = WPBC_FE_Render::render_calendar_only( $shortcode_params );

		$shortcode_html = "<div class='wpbc_only_calendar wpbc_container'>" .
							"<div id='calendar_booking_unselectable" . esc_attr( intval( preg_replace( '/[^0-9].*$/', '', (string) $resource_id ) ) ) . "'></div>" .
							$shortcode_calendar_html .
						'</div>';

		return $shortcode_html;
	}


	/**
	 * Shortcode [bookingform ...]
	 *
	 * @param array $attr
	 *
	 * @return string
	 */
	public static function bookingform_shortcode( $attr ) {

		if ( wpbc_is_on_edit_page() ) {
			return wpbc_get_preview_for_shortcode( 'bookingform', $attr );
		}

		if ( WPBC_GET_Request::has_non_empty_get( 'booking_hash' ) ) {
			/* translators: 1: URL  to FAQ page. */
			return __( 'You need to use special shortcode [bookingedit] for booking editing.', 'booking' ) . ' ' . sprintf( __( 'Please check FAQ instruction how to configure it here %s', 'booking' ), '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/">https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/</a>' );
		}

		$attr = self::force_attr_array( $attr );
		$attr = self::maybe_escape_shortcode_params( $attr );

		// Parse, sanitize and normilize shortcode paramaters.
		$shortcode_params = array(
			'is_echo'                         => 0,
			'resource_id'                     => WPBC_FE_Shortcode_Params::get_from_attr__resource_id_with_aggregate( $attr, WPBC_FE_Attr_Postprocessor::get_default_booking_resource_id() ),
			'selected_dates_without_calendar' => WPBC_FE_Shortcode_Params::get_from_attr__selected_dates_without_calendar( $attr, '' ),
			'custom_booking_form'             => WPBC_FE_Shortcode_Params::get_from_attr__custom_form( $attr, 'standard' ),
			'bk_otions'                       => WPBC_FE_Shortcode_Params::get_from_attr__options( $attr, '' ),
			'cal_count'                       => 1,
			'start_month_calendar'            => false,
			'calendar_dates_start'            => '',
			'calendar_dates_end'              => '',
		);

		$shortcode_result = WPBC_FE_Render::render_booking_form( $shortcode_params );

		return $shortcode_result;
	}

}

WPBC_FE_Shortcodes::init();
