<?php
/**
 * Front-End Shortcode Params Normalizer
 *
 * Goal:
 * - Normalize and sanitize shortcode attributes into a stable params array
 *   for WPBC_FE_Render::render_booking_form() and ::render_calendar_only().
 *
 * @file: includes/fontend/class-fe-shortcode-params.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end shortcode params: Parse -> Sanitize (single entry point).
 *
 * @since  11.0.x
 */
class WPBC_FE_Shortcode_Params {

	/**
	 * Get primary resource id from attrs:
	 *
	 * IMPORTANT: does NOT apply aggregate/agregate.
	 *
	 * @param array $attr - array with all parameters if shortcode.
	 * @param int   $default_id - default booking resource ID.
	 *
	 * @return int
	 */
	public static function get_from_attr__primary_resource_id( $attr, $default_id = 1 ) {

		$resource_id = intval( $default_id );
		$type_value  = null;

		if ( isset( $attr['type'] ) ) {
			$type_value = intval( $attr['type'] );
		}

		if ( isset( $attr['resource_id'] ) ) {
			$type_value = intval( $attr['resource_id'] );
		}

		if ( null !== $type_value ) {
			$resource_id = intval( $type_value );
		}

		return $resource_id;
	}

	/**
	 * Get resource id with aggregate/agregate support (legacy for [booking] / [bookingcalendar] / [bookingedit]).
	 *
	 * Sanitizes resource_id with support of:
	 * - aliases: resource_id -> type
	 * - aggregate/agregate
	 * - commas as separators
	 *
	 * Returns string (may include aggregate IDs "5;3;9") for renderer to split later.
	 *
	 * @param array $attr - array with all parameters if shortcode.
	 * @param int   $default_id - default booking resource ID.
	 *
	 * @return int|string  -> 5 | '5;9;10'
	 */
	public static function get_from_attr__resource_id_with_aggregate( $attr, $default_id = 1 ) {

		$resource_id = self::get_from_attr__primary_resource_id( $attr, $default_id );

		// Aggregate (two spellings).
		if ( isset( $attr['agregate'] ) && ( '' !== (string) $attr['agregate'] ) ) {
			$resource_id .= ';' . (string) $attr['agregate'];
		}
		if ( isset( $attr['aggregate'] ) && ( '' !== (string) $attr['aggregate'] ) ) {
			$resource_id .= ';' . (string) $attr['aggregate'];
		}

		// Normalize separators.
		$resource_id = str_replace( ',', ';', $resource_id );

		// Strict sanitize: digits and separators.
		$resource_id__or__csd = wpbc_clean_digit_or_csd( $resource_id );
		$resource_id          = str_replace( ',', ';', $resource_id__or__csd );

		return ( '' !== $resource_id ) ? $resource_id : (string) (int) $default_id;
	}

	/**
	 * Get calendar months count from shortcode attrs (nummonths) with fallback.
	 *
	 * @param array $attr - array with all parameters if shortcode.
	 * @param int   $default_count - default count of months.
	 *
	 * @return int
	 */
	public static function get_from_attr__months_count( $attr, $default_count = 1 ) {

		$cal_count = intval( $default_count );

		if ( isset( $attr['nummonths'] ) ) {
			$cal_count = intval( $attr['nummonths'] );
		}
		if ( isset( $attr['monthsnum'] ) ) {
			$cal_count = intval( $attr['monthsnum'] );
		}
		if ( isset( $attr['monthscount'] ) ) {
			$cal_count = intval( $attr['monthscount'] );
		}
		if ( isset( $attr['months_count'] ) ) {
			$cal_count = intval( $attr['months_count'] );
		}

		$cal_count = max( 1, min( 36, (int) $cal_count ) ); // choose your max.

		return $cal_count;
	}

	/**
	 * Parse startmonth into legacy format array( year, month ) or false.
	 * Accepts "YYYY-M" or "YYYY-MM".
	 *
	 * @param array       $attr        - array with all parameters if shortcode.
	 * @param false|mixed $default_val - false,  if not defined.
	 *
	 * @return false|mixed|string[]
	 */
	public static function get_from_attr__start_month( $attr, $default_val = false ) {

		$val = '';

		if ( isset( $attr['startmonth'] ) ) {
			$val = trim( (string) $attr['startmonth'] );
		}

		if ( '' === $val ) {
			return $default_val;
		}

		if ( ! preg_match( '/^(\d{4})-(\d{1,2})$/', $val, $m ) ) {
			return $default_val;
		}

		$year  = (int) $m[1];
		$month = (int) $m[2];

		if ( ( $year < 1900 ) || ( $year > 2100 ) ) {
			return $default_val;
		}
		if ( ( $month < 1 ) || ( $month > 12 ) ) {
			return $default_val;
		}

		return array( (string) $year, (string) $month );
	}

	/**
	 * Get calendar_dates_start usualy  in format '2027-01-01'
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - ''.
	 *
	 * @return string
	 */
	public static function get_from_attr__calendar_dates_start( $attr, $default_val = '' ) {

		$val = '';

		if ( isset( $attr['calendar_dates_start'] ) ) {
			$val = (string) wpbc_sanitize_date( $attr['calendar_dates_start'] );
		}

		if ( '' !== $val ) {
			return $val;
		}

		return (string) $default_val;
	}

	/**
	 * Get calendar_dates_end usualy  in format '2027-12-31'
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - ''.
	 *
	 * @return string
	 */
	public static function get_from_attr__calendar_dates_end( $attr, $default_val = '' ) {

		$val = '';

		if ( isset( $attr['calendar_dates_end'] ) ) {
			$val = (string) wpbc_sanitize_date( $attr['calendar_dates_end'] );
		}

		if ( '' !== $val ) {
			return $val;
		}

		return (string) $default_val;
	}


	/**
	 * Get selected_dates for Only 'booking form' without calendar - usualy  in format '24.12.2027, 25.12.2027';
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - ''.
	 *
	 * @return string
	 */
	public static function get_from_attr__selected_dates_without_calendar( $attr, $default_val = '' ) {

		$val = '';
		// '24.12.2027, 25.12.2027'
		if ( isset( $attr['selected_dates'] ) ) {
			$val = (string) wpbc_sanitize_csv_dates( $attr['selected_dates'] );
		}

		if ( '' !== $val ) {
			return $val;
		}

		return (string) $default_val;
	}

	/**
	 * Get options parameter from shortcode [booking ... options="..."] value (legacy key: 'options').
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - ''.
	 *
	 * @return string
	 */
	public static function get_from_attr__options( $attr, $default_val = '' ) {

		$options = '';

		if ( isset( $attr['options'] ) ) {
			$options = (string) $attr['options'];
			$options = sanitize_text_field( wp_unslash( $options ) );
		}

		if ( ! empty( $options ) ) {
			return $options;
		}

		return $default_val;
	}


	/**
	 * Get custom booking form name (form type) from shortcode attrs (key: form_type).
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - 'standard'.
	 *
	 * @return string
	 */
	public static function get_from_attr__custom_form( $attr, $default_val = 'standard' ) {

		$custom_form = '';

		if ( isset( $attr['form_type'] ) ) {
			$custom_form = sanitize_text_field( wp_unslash( $attr['form_type'] ) );
		}
		if ( isset( $attr['custom_form'] ) ) {
			$custom_form = sanitize_text_field( wp_unslash( $attr['custom_form'] ) );
		}
		if ( isset( $attr['booking_form'] ) ) {
			$custom_form = sanitize_text_field( wp_unslash( $attr['booking_form'] ) );
		}
		if ( isset( $attr['custom_booking_form'] ) ) {
			$custom_form = sanitize_text_field( wp_unslash( $attr['custom_booking_form'] ) );
		}

		if ( ! empty( $custom_form ) ) {
			return (string) $custom_form;
		}

		// Get Primary  booking resource ID from  shortcode attr.
		$resource_id = self::get_from_attr__primary_resource_id( $attr, WPBC_FE_Attr_Postprocessor::get_default_booking_resource_id() );
		// -------------------------------------------------------------------------------------------------------------
		// Maybe get "Default Custom Form" for specific booking resource, in  >= BM ,  if in shortcode 'custom_form' is EMPTY!
		// -------------------------------------------------------------------------------------------------------------
		$default_custom_form_name = apply_bk_filter( 'wpbc_get_default_custom_form', '', $resource_id );
		if ( ! empty( $default_custom_form_name ) ) {
			return (string) $default_custom_form_name;
		}

		return (string) $default_val;
	}


	/**
	 * Get status of form,  e.g.:  'published' | 'preview'  ->  from shortcode attrs (key: form_status).
	 *
	 * @param array  $attr        - array with all parameters if shortcode.
	 * @param string $default_val - 'published'.
	 *
	 * @return string
	 */
	public static function get_from_attr__form_status( $attr, $default_val = 'published' ) {

		$val = '';

		if ( isset( $attr['form_status'] ) ) {
			$val = sanitize_key( wp_unslash( $attr['form_status'] ) );
		}

		// Normalize synonyms (optional).
		if ( in_array( $val, array( 'publish', 'published' ), true ) ) {
			$val = 'published';
		}
		if ( in_array( $val, array( 'preview' ), true ) ) {
			$val = 'preview';
		}

		// Whitelist (keep tight to avoid future “draft” leakage on frontend).
		if ( ! in_array( $val, array( 'published', 'preview' ), true ) ) {
			$val = (string) $default_val;
		}

		return ( '' !== $val ) ? $val : (string) $default_val;
	}

}


/**
 * Post-processing helpers for normalized attributes.
 *
 * This class contains logic that operates on already-sanitized values:
 *  - aggregate resource splitting
 *  - resource validation
 *  - booking_hash resolution and parent-resource rewrite (legacy behavior)
 *  - date-range completion (calendar_dates_start/end)
 *
 * @since 11.0.x
 */
class WPBC_FE_Attr_Postprocessor {

	/**
	 * Get default Booking resource.
	 *
	 * @return int|mixed
	 */
	public static function get_default_booking_resource_id() {

		$resource_id = 1;

		$legacy = wpbc_get_legacy_booking_instance();

		if ( ( ! empty( $legacy ) ) && ( false !== $legacy->wpdev_bk_personal ) ) {
			$resource_id = (int) $legacy->wpdev_bk_personal->get_default_booking_resource_id();
		}

		return (int) $resource_id;
	}

	/**
	 * If resource_id contains aggregate IDs "5;3;9", return first + full list, e.g:   [ 'resource_id' => 5, 'aggregate_resource_id_arr' => [5,3,9] ]
	 *
	 * @param mixed $resource_id   - e.g. "5;3;9" or "5" or 5.
	 *
	 * @return array(
	 *    'resource_id' => string,
	 *    'aggregate_resource_id_arr' => array
	 * )
	 */
	public static function split_aggregate_resource_id( $resource_id ) {

		$aggregate_resource_id_arr = array();

		$resource_id = (string) $resource_id;

		$resource_id = str_replace( ',', ';', wpbc_clean_digit_or_csd( $resource_id ) );

		if ( false !== strpos( $resource_id, ';' ) ) {
			$aggregate_resource_id_arr = explode( ';', $resource_id );
			$resource_id               = $aggregate_resource_id_arr[0];
		}

		$aggregate_resource_id_arr = array_values( array_filter( array_map( 'absint', $aggregate_resource_id_arr ) ) );
		$aggregate_resource_id_arr = array_values( array_unique( $aggregate_resource_id_arr ) );

		return array(
			'resource_id'               => $resource_id,
			'aggregate_resource_id_arr' => $aggregate_resource_id_arr,
		);
	}

	/**
	 * Validate resource_id parameter and check if booking resource exists.
	 *
	 * @param int|string $resource_id ID of booking resource.
	 *
	 * @return true|string True if valid, otherwise error message.
	 */
	public static function validate_resource_id( $resource_id ) {

		$resource_id = absint( $resource_id );

		if ( 0 === $resource_id ) {
			return __( 'Booking resource type is not defined. This can be, when at the URL is wrong booking hash.', 'booking' );
		}

		// Validation should not echo. Rendering layer decides about output.
		$is_echo = false;

		$is_booking_resource_exist = apply_bk_filter( 'wpdev_is_booking_resource_exist', true, $resource_id, $is_echo );

		if ( ! $is_booking_resource_exist ) {
			return 'Booking resource does not exist. [ID=' . esc_attr( $resource_id ) . ']';
		}

		return true;
	}

	/**
	 * Hash resolution, e.g. $_GET['booking_hash'] for Booking Form rendering (calendar+form).  Keeps legacy messages and parent resource rewrite behavior.
	 *
	 * @param array  $params_arr array of parameters from shortcode.
	 * @param string $context    can be 'form'|'calendar'.
	 *
	 * @return array('ok' => bool, 'params_arr' => array, 'error_html' => string)
	 */
	public static function resolve_booking_hash_and_maybe_get_parent_resource_id( $params_arr, $context = 'form' ) {

		// Legacy behavior: ONLY checks key existence, not “non-empty”.
		if ( ! WPBC_GET_Request::has_non_empty_get( 'booking_hash' ) ) {
			return array(
				'ok'         => true,
				'params_arr' => $params_arr,
				'error_html' => '',
			);
		}

		// If booking_hash exists and this is calendar-only, show warning.
		if ( 'calendar' === $context ) {
			$is_error = '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>'
						. esc_html__( 'Oops!', 'booking' )
						. '</strong> '
						. esc_html__( 'We could not manage booking on availability calendar without booking form. The link you used may be incorrect.', 'booking' )
						. '</div></div></div>';

			return array(
				'ok'         => false,
				'params_arr' => $params_arr,
				'error_html' => $is_error,
			);
		}

		$get_booking_hash = WPBC_GET_Request::get_sanitized( 'booking_hash' );

		$my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $get_booking_hash );

		$html_error_message = '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>'
							  . esc_html__( 'Oops!', 'booking' )
							  . '</strong> '
							  . esc_html__( 'We could not find your booking. The link you used may be incorrect or has expired. If you need assistance, please contact our support team.', 'booking' )
							  . '</div></div></div>';

		$is_error           = false;

		if ( false !== $my_booking_id_type ) {
			list( $my_edited_bk_id, $my_boook_type ) = $my_booking_id_type;

			if ( empty( $my_boook_type ) ) {
				$is_error = $html_error_message;
			}
		} else {
			$is_error = $html_error_message;
		}

		if ( false !== $is_error ) {
			return array(
				'ok'         => false,
				'params_arr' => $params_arr,
				'error_html' => $is_error,
			);
		}

		if ( 'form' === $context ) {

			// Parent resource rewrite for child booking resources (legacy behavior).
			if ( ( ! WPBC_GET_Request::has_get( 'booking_pay' ) ) && function_exists( 'wpbc_is_this_child_resource' ) && wpbc_is_this_child_resource( $my_boook_type ) ) {
				$bk_parent_br_id           = absint( wpbc_get_parent_resource( $my_boook_type ) );
				$params_arr['resource_id'] = $bk_parent_br_id;
			}
		}

		return array(
			'ok'         => true,
			'params_arr' => $params_arr,
			'error_html' => '',
		);
	}

	/**
	 * Normalize calendar_dates_start/end:
	 * If only one boundary is set, compute the other based on max visible days.
	 *
	 * @param string $start - start date.
	 * @param string $end  - end date.
	 *
	 * @return array('start' => string, 'end' => string)
	 */
	public static function normalize_calendar_dates_range( $start, $end ) {

		$start = (string) $start;
		$end   = (string) $end;

		$days = (int) wpbc_get_max_visible_days_in_calendar();

		$tz = wp_timezone(); // WordPress site timezone (DateTimeZone).

		if ( ( '' !== $start ) && ( '' === $end ) ) {
			$dt = DateTimeImmutable::createFromFormat( 'Y-m-d', $start, $tz );
			if ( $dt ) {
				$end = $dt->modify( '+' . $days . ' days' )->format( 'Y-m-d' );
			}
		}

		if ( ( '' === $start ) && ( '' !== $end ) ) {
			$dt = DateTimeImmutable::createFromFormat( 'Y-m-d', $end, $tz );
			if ( $dt ) {
				$start = $dt->modify( '-' . $days . ' days' )->format( 'Y-m-d' );
			}
		}

		return array(
			'start' => $start,
			'end'   => $end,
		);
	}

}


/**
 * Shortcode "options" parameter parser for legacy "{parameter ...}" syntax.
 *
 * Converts options string like:
 *  options='{parameter name="my_param" value="value"},{parameter name="other_param" value="other value"}'
 *
 * Into a sanitized associative array:
 *  array(
 *    'my_param'    => 'value',
 *    'other_param' => 'other value',
 *  )
 *
 * Used by booking form shortcodes like:
 *  [text some_field_name "my_param"]
 *
 * @since 11.0.x
 */
class WPBC_FE_Options_Parser {

	/**
	 * Parse shortcode options parameter. e.g. - {parameter name="..." value="..."}
	 *
	 *        Paramaters defined in the Booking Calendar shortcode,  like this:
	 *              options='{parameter name="my_param" value="value"},{parameter name="other_param" value="other value"}'
	 *        Usage in booking form:
	 *              [text some_field_name "my_param"] and [text other_field_name "other_param"]
	 *
	 * Returns sanitized associative array:
	 *  array( 'param_name' => 'param_val', 'bfb_form_id' => '12', ... )
	 *
	 * @param mixed $option_param_value - shortcode options parameter value, e.g.: '{parameter name="my_param" value="value"},{parameter name="other_param" value="other value"}'.
	 *
	 * @return array  - founded options: [ 'my_param' => 'value', 'other_param' => 'other value'' ]
	 */
	public static function parse_for_parameter__in_shortcode_options( $option_param_value ) {

		$custom_params = array();

		$raw = is_scalar( $option_param_value ) ? (string) $option_param_value : '';
		$raw = trim( $raw );

		if ( '' === $raw ) {
			return $custom_params;
		}

		// Find {parameter ...} blocks.
		if ( ! preg_match_all( '/\{\s*parameter\b([^}]*)\}/i', $raw, $blocks ) ) {
			return $custom_params;
		}

		foreach ( $blocks[1] as $attrs ) {

			$name  = '';
			$value = '';

			if ( preg_match( '/\bname\s*=\s*([\'"])(.*?)\1/i', $attrs, $m1 ) ) {
				$name = (string) $m1[2];
			}
			if ( preg_match( '/\bvalue\s*=\s*([\'"])(.*?)\1/i', $attrs, $m2 ) ) {
				$value = (string) $m2[2];
			}

			if ( ( '' !== $name ) && ( '' !== $value ) ) {
				$custom_params[ sanitize_text_field( (string) $name ) ] = sanitize_text_field( (string) $value );
			}
		}

		return $custom_params;
	}
}


/**
 * Front-End Request Helper - get sanitized $_GET params.
 *
 * Purpose:
 * - Centralize access to request (mostly $_GET) values used by front-end rendering.
 * - Perform basic sanitization/unslash in one place.
 * - Keep other classes "pure" (no direct $_GET usage).
 *
 * @package Booking Calendar
 * @since   11.0.x
 */
class WPBC_GET_Request {

	/**
	 * Does request contain given GET key?
	 *
	 * @param string $key - Get key slug.
	 *
	 * @return bool
	 */
	public static function has_get( $key ) {

		$key = is_scalar( $key ) ? (string) $key : '';
		$key = trim( $key );

		if ( '' === $key ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		return isset( $_GET[ $key ] );
	}

	/**
	 * Does request contain given non EMPTY - GET key?
	 *
	 * @param string $key - Get key slug.
	 *
	 * @return bool
	 */
	public static function has_non_empty_get( $key ) {
		return ( '' !== trim( self::get_sanitized( $key ) ) );
	}

	/**
	 * Get sanitized scalar value from $_GET (unslash + sanitize).
	 *
	 * @param string $key
	 * @param string $default_val
	 * @param string $sanitize_type 'text' | 'key'
	 *
	 * @return string
	 */
	public static function get_sanitized( $key, $default_val = '', $sanitize_type = 'text' ) {

		$key = is_scalar( $key ) ? (string) $key : '';
		$key = trim( $key );

		if ( '' === $key ) {
			return (string) $default_val;
		}

		if ( ! self::has_get( $key ) ) {
			return (string) $default_val;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw = wp_unslash( $_GET[ $key ] );

		// If attacker sends ?x[]=1 we must NOT accept "Array" as a valid value.
		if ( is_array( $raw ) || is_null( $raw ) ) {
			return (string) $default_val;
		}

		$raw = (string) $raw;

		if ( 'key' === $sanitize_type ) {
			$val = sanitize_key( $raw );

			return ( '' !== $val ) ? $val : (string) $default_val;
		}

		return sanitize_text_field( $raw );
	}
}


/**
 * Check if we enabled BFB in Settings, e.g.: 		WPBC_Frontend_Settings::is_bfb_enabled()
 */
class WPBC_Frontend_Settings {

	/**
	 * Is the BFB feature compiled/available in this build.
	 *
	 * @return bool
	 */
	public static function is_bfb_feature_available() {
		return ( defined( 'WPBC_NEW_FORM_BUILDER' ) && WPBC_NEW_FORM_BUILDER );
	}

	/**
	 * Is BFB enabled in settings.
	 *
	 * @param WPBC_Frontend_Context|null $ctx Optional, for future use (preview context etc).
	 *
	 * @return bool
	 */
	public static function is_bfb_enabled( $ctx = null ) {

		if ( ! self::is_bfb_feature_available() ) {
			return false;
		}

		$is_enabled = ( 'On' === get_bk_option( 'booking_use_bfb_form' ) );

		/**
		 * Allow forcing enable/disable externally if needed.
		 *
		 * @param bool                 $is_enabled
		 * @param WPBC_Frontend_Context|null $ctx
		 */
		$is_enabled = (bool) apply_filters( 'wpbc_frontend_is_bfb_enabled', $is_enabled, $ctx );

		return $is_enabled;
	}
}

