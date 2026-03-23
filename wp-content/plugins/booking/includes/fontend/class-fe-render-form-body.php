<?php
/**
 * Front-End Form Body Rendering
 *
 *  - Calendar markup (or hidden selected-dates field)
 *  - Form body source resolution (BFB / legacy / simple)
 *  - Legacy post-processing hooks (additional calendars, captcha, change-over times)
 *  - Legacy wrapper (<form> container + nonce + hidden fields)
 *  - Legacy inline scripts (duplicate-calendar warning, cost hints, autofill)
 *
 * @package Booking Calendar
 * @since   11.0.x
 * @file    ../includes/fontend/class-fe-render-form-body.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Booking Form Body Renderer.
 *
 * Orchestrates building of the booking form HTML while preserving legacy output and
 * filter/action execution order.
 *
 * @since 11.0.x
 */
class WPBC_FE_Form_Body_Renderer {

	/**
	 * Render booking form HTML (calendar + form body + wrapper + inline scripts),
	 * preserving legacy output and hook execution order.
	 *
	 * Expected flow:
	 *  1) Parse shortcode "options" into custom parameters (for BFB/simple form context).
	 *  2) Build calendar markup OR hidden textarea with preselected dates.
	 *  3) Resolve form body source:
	 *      - BFB source (if available) with filter override and shortcode-engine fallback.
	 *      - Legacy booking form (personal.php).
	 *      - Simple form fallback.
	 *  4) Post-process composed markup:
	 *      - Insert calendar ([calendar] placeholder or prepend).
	 *      - Replace additional calendars via legacy filter.
	 *      - Replace [captcha] placeholder.
	 *      - Append change-over times.
	 *  5) Wrap in legacy container + add nonce + hidden fields.
	 *  6) Append legacy inline scripts.
	 *
	 * @since 11.0.x
	 *
	 * @param array $args {
	 *     Optional. Arguments for rendering.
	 *
	 *     @type int           $resource_id                     Booking resource ID.
	 *     @type string        $custom_booking_form             Booking form slug/name (legacy: "standard").
	 *     @type string        $selected_dates_without_calendar Preselected dates string (legacy format).
	 *     @type int           $cal_count                       Number of months to show in calendar.
	 *     @type mixed         $bk_otions                       Shortcode options string (legacy: options="...") or array.
	 *     @type wpdev_booking $legacy_instance                 Legacy plugin booking object (Phase #1/compat).
	 * }
	 *
	 * @return string Rendered HTML (ready to echo).
	 */
	public static function render( $args ) {

		$defaults = array(
			'resource_id'                     => 1,
			'custom_booking_form'             => 'standard',
			'form_status'                     => 'published',
			'selected_dates_without_calendar' => '',
			'cal_count'                       => 1,
			'bk_otions'                       => '',
			'legacy_instance'                 => null,
		);

		$args = wp_parse_args( $args, $defaults );

		$resource_id                     = (int) $args['resource_id'];
		$custom_booking_form             = (string) $args['custom_booking_form'];
		$selected_dates_without_calendar = (string) $args['selected_dates_without_calendar'];
		$cal_count                       = (int) $args['cal_count'];
		$bk_otions                       = $args['bk_otions'];
		$legacy_instance                 = $args['legacy_instance'];
		$form_status                     = isset( $args['form_status'] ) ? (string) $args['form_status'] : 'published';

		$nl = '<div style="clear:both;height:10px;"></div>';

		$custom_params = WPBC_FE_Options_Parser::parse_for_parameter__in_shortcode_options( $bk_otions );

		$calendar_html = WPBC_FE_Calendar_Markup::build( $resource_id, $cal_count, $bk_otions, $selected_dates_without_calendar );

		$source_res = WPBC_FE_Form_Source::get_form_body_html( $resource_id, $custom_booking_form, $form_status, $custom_params, $legacy_instance );

		$form_html = $source_res['body_html'];

		// Preserve legacy: apply after-load filter ONLY for BFB and Simple form.
		if ( ! empty( $source_res['apply_after_load_filter'] ) ) {
			// Re-update other hints, such as availability times hint.
			$form_html = apply_filters( 'wpbc_booking_form_content__after_load', $form_html, $resource_id, $custom_booking_form );
		}


		// 1. Body HTML, before you inject calendar/captcha/extra calendars.  Postprocess Form Conent - regarding settings - e.g.:  $source_res['bfb_settings'] = [   options = [  booking_form_theme = "wpbc_theme_dark_1", .... ], ...
		$form_html = apply_filters( 'wpbc_booking_form__body_html__before_postprocess', $form_html, $source_res['bfb_settings'], $resource_id, $custom_booking_form );

		$form_html = WPBC_FE_Form_Postprocessor::apply( $form_html, $calendar_html, array(
			'resource_id'                     => $resource_id,
			'custom_booking_form'             => $custom_booking_form,
			'selected_dates_without_calendar' => $selected_dates_without_calendar,
			'cal_count'                       => $cal_count,
			'bk_otions'                       => $bk_otions,
			'custom_params'                   => $custom_params,
			'legacy_instance'                 => $legacy_instance,
			'nl'                              => $nl,
		) );

		// 2. Composed booking form HTML (calendar already inserted).    Postprocess Form Conent - regarding settings - e.g.:  $source_res['bfb_settings'] = [   options = [  booking_form_theme = "wpbc_theme_dark_1", .... ], ...
		$form_html = apply_filters( 'wpbc_booking_form__html__before_wrapper', $form_html, $source_res['bfb_settings'], $resource_id, $custom_booking_form );

		$wrapped = WPBC_FE_Form_Wrapper::wrap( $form_html, $resource_id );

		// 3. Postprocess Form Conent - regarding settings - e.g.:  $source_res['bfb_settings'] = [   options = [  booking_form_theme = "wpbc_theme_dark_1", .... ], ...
		$wrapped = apply_filters( 'wpbc_booking_form__wrapped_html__before_inline_scripts', $wrapped, $source_res['bfb_settings'], $resource_id, $custom_booking_form );

		$wrapped .= WPBC_FE_Inline_Scripts::collect( $resource_id, $selected_dates_without_calendar );

		// 4. Postprocess Form Conent - regarding settings - e.g.:  $source_res['bfb_settings'] = [   options = [  booking_form_theme = "wpbc_theme_dark_1", .... ], ...
		$wrapped = apply_filters( 'wpbc_booking_form__wrapped_html__after_inline_scripts', $wrapped, $source_res['bfb_settings'], $resource_id, $custom_booking_form );

		return $wrapped;
	}
}


/**
 * Calendar markup builder for booking form composition.
 *
 * If no preselected dates are provided, returns full calendar markup via legacy generator.
 * If preselected dates are provided, returns a hidden textarea for legacy JS to consume.
 *
 * @since 11.0.x
 */
class WPBC_FE_Calendar_Markup {

	/**
	 * Build calendar HTML (real calendar markup or hidden textarea with preselected dates).
	 *
	 * @param int    $resource_id
	 * @param int    $cal_count
	 * @param mixed  $bk_otions
	 * @param string $selected_dates_without_calendar
	 *
	 * @param int    $resource_id                     Booking resource ID.
	 * @param int    $cal_count                       Number of months to show.
	 * @param mixed  $bk_otions                       Shortcode options parameter (legacy).
	 * @param string $selected_dates_without_calendar Preselected dates (legacy string format).
	 *
	 * @return string Calendar HTML snippet.
	 */
	public static function build( $resource_id, $cal_count, $bk_otions, $selected_dates_without_calendar ) {

		$resource_id                     = (int) $resource_id;
		$cal_count                       = (int) $cal_count;
		$selected_dates_without_calendar = (string) $selected_dates_without_calendar;

		if ( '' === $selected_dates_without_calendar ) {
			// Get HTML  with  [calendar] shortcode and Styles for calendar. Get legend html. //TODO: extract CSS Styles separately !!!!
			return wpbc_pre_get_calendar_html( $resource_id, $cal_count, $bk_otions );
		}

		return '<textarea rows="3" cols="50" id="date_booking' . $resource_id . '" name="date_booking' . $resource_id . '" autocomplete="off" style="display:none;">' .
				esc_textarea( $selected_dates_without_calendar ) .
			'</textarea>';
	}
}

/**
 * Return the booking form body HTML, choosing BFB source if available, else legacy.
 * Does NOT insert calendar, captcha, additional calendars, wrapper, nonce, etc.
 *
 * @since 11.0.x
 *
 * @param int            $resource_id         Booking resource ID.
 * @param string         $custom_booking_form Booking form slug/name.
 * @param array          $custom_params       Parsed custom params from shortcode options ("{parameter ...}").
 * @param wpdev_booking  $legacy_instance     Legacy booking object (optional).
 *
 * @return array {
 *    Result array.
 *
 *    @type string $body_html               Booking form body HTML.
 *    @type bool   $apply_after_load_filter Whether to apply 'wpbc_booking_form_content__after_load'.
 * }
 */
class WPBC_FE_Form_Source {


	/**
	 * Return the booking form body HTML, choosing BFB source if available, else legacy.
	 *
	 * @param int           $resource_id
	 * @param string        $custom_booking_form
	 * @param string        $form_status         'published'|'preview'
	 * @param array         $custom_params
	 * @param wpdev_booking $legacy_instance
	 *
	 * @return array
	 */
	public static function get_form_body_html( $resource_id, $custom_booking_form, $form_status, $custom_params, $legacy_instance ) {

		$resource_id         = (int) $resource_id;
		$custom_booking_form = (string) $custom_booking_form;
		$form_status         = (string) $form_status;
		$custom_params       = ( is_array( $custom_params ) ) ? $custom_params : array();

		$req = array(
			'resource_id'     => $resource_id,
			'form_slug'       => $custom_booking_form,
			'form_status'     => $form_status,
			'custom_params'   => $custom_params,
			'legacy_instance' => $legacy_instance,
		);

		/**
		 * $resolved = array(   'engine'                  : 'bfb_db'|'legacy'|'simple'
		 *                      'apply_after_load_filter' : bool
		 *                      'bfb_loader_args'         : array()
		 *                      'fallback_chain'          : array()
		 */
		$resolved = WPBC_FE_Form_Source_Resolver::resolve( $req );

		// -----------------------------------------------------------------
		// BFB DB engine (only if resolver decided it).
		// -----------------------------------------------------------------
		if ( ( isset( $resolved['engine'] ) ) && ( 'bfb_db' === $resolved['engine'] ) && function_exists( 'wpbc_bfb_get_booking_form_pair' ) ) {

			$booking_form_html__arr = self::wpbc_bfb__get_booking_form_html__arr( $req, $resolved );

			if ( null !== $booking_form_html__arr ) {
				return $booking_form_html__arr;
			}
		}

		// -----------------------------------------------------------------
		// Legacy engine (unchanged output).
		// -----------------------------------------------------------------
		if ( ( ! empty( $legacy_instance ) )
			 && ( false !== $legacy_instance->wpdev_bk_personal )
			 // && ( 'On' != get_bk_option( 'booking_is_use_simple_booking_form' ) )  // Important! in paid versions, if edited in "Simple mode" the compiled form, still saved as "Advanced form" content, so we need to get it from there!
		) {
			return array(
				'body_html'               => $legacy_instance->wpdev_bk_personal->get_booking_form( $resource_id, $custom_booking_form, $custom_params ),
				'apply_after_load_filter' => false,
				'bfb_settings'            => array(),
			);
		}

		// -----------------------------------------------------------------
		// Simple fallback. - ONLY  for the Booking Calendar Free version !
		// -----------------------------------------------------------------
		return array(
			'body_html'               => wpbc_simple_form__get_booking_form__as_html( $resource_id, $custom_booking_form, $custom_params ),
			'apply_after_load_filter' => true,
			'bfb_settings'            => array(),
		);
	}


	/**
	 * Get BFB booking form  content array. Helper function.
	 *
	 * @param array $req       - array.
	 * @param array $resolved  - array.
	 *
	 * @return array|null
	 */
	public static function wpbc_bfb__get_booking_form_html__arr( $req, $resolved ) {

		$custom_params       = $req ['custom_params'];
		$form_status         = $req ['form_status'];
		$custom_booking_form = $req['form_slug'];
		$resource_id         = $req ['resource_id'];
		$legacy_instance     = $req ['legacy_instance'];

		$bfb_loader_args = ( isset( $resolved['bfb_loader_args'] ) && is_array( $resolved['bfb_loader_args'] ) ) ? $resolved['bfb_loader_args'] : array();


		// Keep old behavior: allow explicit form_id/status overrides from options only if present.
		if ( ! empty( $custom_params['bfb_form_id'] ) && empty( $bfb_loader_args['form_id'] ) ) {
			$bfb_loader_args['form_id'] = (int) $custom_params['bfb_form_id'];
		}

		$bfb_settings  = array();
		$bfb_source    = trim( '' );
		$settings_json = trim( '' );

		$bfb_pair = wpbc_bfb_get_booking_form_pair( $bfb_loader_args );

		if ( is_array( $bfb_pair ) ) {
			$bfb_source    = isset( $bfb_pair['form'] ) ? (string) $bfb_pair['form'] : '';
			$settings_json = isset( $bfb_pair['settings_json'] ) ? (string) $bfb_pair['settings_json'] : '';
		}

		if ( '' !== $settings_json ) {
			$decoded = json_decode( $settings_json, true );
			if ( is_array( $decoded ) ) {
				$bfb_settings = $decoded;
			}
		}

		// Allow loader to return either string OR array with settings.
		if ( is_array( $bfb_source ) ) {

			// Common possible keys (support multiple formats, future-proof).
			if ( ! empty( $bfb_source['settings'] ) && is_array( $bfb_source['settings'] ) ) {
				$bfb_settings = $bfb_source['settings'];
			} elseif ( ! empty( $bfb_source['settings_json'] ) && is_string( $bfb_source['settings_json'] ) ) {
				$decoded = json_decode( $bfb_source['settings_json'], true );
				if ( is_array( $decoded ) ) {
					$bfb_settings = $decoded;
				}
			}

			// Source itself.
			if ( isset( $bfb_source['advanced_form'] ) ) {
				$bfb_source = (string) $bfb_source['advanced_form'];
			} elseif ( isset( $bfb_source['source'] ) ) {
				$bfb_source = (string) $bfb_source['source'];
			} else {
				$bfb_source = '';
			}
		}

		// Optional fallback hook if you keep loader returning string for now.
		if ( empty( $bfb_settings ) ) {
			$bfb_settings = apply_filters( 'wpbc_bfb_form_settings_for_render', array(), $bfb_loader_args, $resource_id, $custom_booking_form, $custom_params );
		}


		if ( '' !== trim( (string) $bfb_source ) ) {

			// Give addons a chance to fully handle rendering.
			$bfb_form_html = apply_filters( 'wpbc_bfb_render_booking_form_source', '', $bfb_source, $resource_id, $custom_booking_form, $custom_params );

			if ( '' === $bfb_form_html ) {

				$render_args = array_merge( $custom_params, array( 'booking_type' => (int) $resource_id ) );

				if ( ( ! empty( $legacy_instance ) ) && ( ! empty( $legacy_instance->wpdev_bk_personal ) ) ) {
					$render_args['owner'] = $legacy_instance->wpdev_bk_personal;
				}

				$bfb_source = wpbc_lang( $bfb_source );

				// Replace custom params in the source (legacy behavior).
				if ( ! empty( $custom_params ) ) {
					foreach ( $custom_params as $custom_params_key => $custom_params_value ) {
						$bfb_source = str_replace( $custom_params_key, $custom_params_value, $bfb_source );
					}
				}

				$bfb_source    = wpbc_bf__replace_custom_html_shortcodes( $bfb_source );
				$bfb_form_html = wpbc_render_booking_form_shortcodes( $bfb_source, $render_args );
			}

			if ( '' !== $bfb_form_html ) {
				return array(
					'body_html'               => $bfb_form_html,
					'apply_after_load_filter' => ! empty( $resolved['apply_after_load_filter'] ),
					'bfb_settings'            => $bfb_settings,
				);
			}
		}

		// If BFB resolution succeeded but returned empty output, fall through to legacy.
		return null;
	}
}

/**
 * Booking form post-processor (legacy compatibility).
 *
 * Applies legacy transformations and hooks to the combined form markup:
 *  - Inserts calendar markup (replaces [calendar] or prepends).
 *  - Replaces additional calendars via legacy filter.
 *  - Replaces [captcha] placeholder if present.
 *  - Appends change-over times.
 *
 * @since 11.0.x
 */
class WPBC_FE_Form_Postprocessor {

	/**
	 * Apply legacy postprocessing to the composed form markup.
	 *
	 * @since 11.0.x
	 *
	 * @param string $form_html     Booking form body HTML.
	 * @param string $calendar_html Calendar HTML snippet.
	 * @param array  $ctx {
	 *     Context data used during post-processing.
	 *
	 *     @type int           $resource_id                     Booking resource ID.
	 *     @type string        $custom_booking_form             Booking form slug/name.
	 *     @type string        $selected_dates_without_calendar Preselected dates (legacy string).
	 *     @type int           $cal_count                       Number of months shown.
	 *     @type mixed         $bk_otions                       Shortcode options.
	 *     @type wpdev_booking $legacy_instance                 Legacy booking instance (optional).
	 *     @type string        $nl                              Legacy separator markup.
	 * }
	 *
	 * @return string Post-processed HTML.
	 */
	public static function apply( $form_html, $calendar_html, $ctx ) {

		$form_html     = (string) $form_html;
		$calendar_html = (string) $calendar_html;

		$resource_id                     = (int) $ctx['resource_id'];
		$custom_booking_form             = (string) $ctx['custom_booking_form'];
		$selected_dates_without_calendar = (string) $ctx['selected_dates_without_calendar'];
		$cal_count                       = (int) $ctx['cal_count'];
		$bk_otions                       = $ctx['bk_otions'];
		$legacy_instance                 = $ctx['legacy_instance'];
		$nl                              = (string) $ctx['nl'];

		// Insert calendar into form.
		if ( false !== strpos( $form_html, '[calendar]' ) ) {
			$form_html = str_replace( '[calendar]', $calendar_html, $form_html );
		} else {
			$form_html = '<div class="booking_form_div">' . $calendar_html . '</div>' . $nl . $form_html;
		}

		// Replace additional calendars.
		$form_html = apply_bk_filter(
			'wpdev_check_for_additional_calendars_in_form',
			$form_html,
			$resource_id,
			array(
				'booking_form'   => $custom_booking_form,
				'selected_dates' => $selected_dates_without_calendar,
				'cal_count'      => $cal_count,
				'otions'         => $bk_otions,
			)
		);

		// Captcha replacement.
		$form_html = apply_filters( 'wpbc_booking_form_html__create_captcha', $form_html, $resource_id );

		// Change-over times injection.
		$form_html = apply_filters( 'wpbc_booking_form_html__update__append_change_over_times', $form_html, $resource_id );

		return $form_html;
	}
}

/**
 * Booking form wrapper (legacy HTML structure).
 *
 * Responsible for building the outer <div> container and <form> tag, including:
 *  - ajax response containers
 *  - anchor link (bklnk)
 *  - hidden bk_type field
 *  - nonce field
 *  - "garbage" container used by legacy JS
 *
 * @since 11.0.x
 */
class WPBC_FE_Form_Wrapper {

	/**
	 * Wrap the form HTML into legacy outer container + add hidden fields + nonce + garbage.
	 *
	 * @param string $form_html
	 * @param int    $resource_id
	 *
	 * @param string $form_html   Post-processed booking form HTML (already contains calendar markup).
	 * @param int    $resource_id Booking resource ID.
	 *
	 * @return string Wrapped HTML.
	 */
	public static function wrap( $form_html, $resource_id ) {

		$resource_id = (int) $resource_id;
		$wpbc_nonce  = wp_nonce_field( 'CALCULATE_THE_COST', ( 'wpbc_nonceCALCULATE_THE_COST' . $resource_id ), true, false );

		$booking_form_is_using_bs_css = get_bk_option( 'booking_form_is_using_bs_css' );
		$booking_form_format_type     = get_bk_option( 'booking_form_format_type' );
		$booking_form_theme           = get_bk_option( 'booking_form_theme' );

		$form_container_random_id = 'form_id' . ( time() * wp_rand( 0, 1000 ) );
		$form_container_css       = 'wpbc_container wpbc_form wpbc_container_booking_form ' . ( ( 'On' === wpbc_is_this_demo() ) ? ' wpbc_demo_site ' : '' ) .
									esc_attr( $booking_form_theme ) . ( ( 'On' === $booking_form_is_using_bs_css ) ? ' wpdevelop ' : '' );


		$return_form = '<div id="' . esc_attr( $form_container_random_id ) . '" class="' . esc_attr( $form_container_css ) . '" >' .
						'<form id="booking_form' . intval( $resource_id ) . '" class="booking_form ' . esc_attr( $booking_form_format_type ) . '" method="post" action="">' .
							'<div id="ajax_respond_insert' . intval( $resource_id ) . '" class="ajax_respond_insert" style="display:none;"></div>' .
							'<a name="bklnk' . $resource_id . '" id="bklnk' . $resource_id . '"></a>' .
							'<div id="booking_form_div' . $resource_id . '" class="booking_form_div">' .
								$form_html .
								'<input id="bk_type' . $resource_id . '" name="bk_type' . $resource_id . '" class="" type="hidden" value="' . $resource_id . '" />' .
							'</div>' .
							'<div id="submiting' . $resource_id . '"></div>' .
							'<div class="form_bk_messages" id="form_bk_messages' . $resource_id . '" ></div>' .
							$wpbc_nonce .
						'</form>' .
					'</div>' .
					'<div id="booking_form_garbage' . intval( $resource_id ) . '" class="booking_form_garbage"></div>';

		return $return_form;
	}
}


/**
 * Inline scripts builder (legacy compatibility).
 *
 * Generates and appends legacy inline JavaScript required by booking form UX:
 *  - Duplicate-calendar warning in console when the same resource calendar is rendered multiple times.
 *  - Cost hint display trigger when selected dates are predefined.
 *  - Autofill for logged-in users (legacy behavior).
 *
 * @since 11.0.x
 */
class WPBC_FE_Inline_Scripts {

	/**
	 * Collect legacy inline scripts into WPBC_FE_Assets (Elementor-safe).
	 *
	 * @param int    $resource_id
	 * @param string $selected_dates_without_calendar
	 *
	 * @return string Inline scripts HTML.
	 */
	public static function collect( $resource_id, $selected_dates_without_calendar ) {

		$html = '';

		if ( ! class_exists( 'WPBC_FE_Assets' ) ) {
			return $html;
		}

		$resource_id = (int) $resource_id;

		$selected_dates_without_calendar = (string) $selected_dates_without_calendar;

		// 1) Duplicate-calendar warning / or cost-hint trigger (mutually exclusive in legacy).
		if ( '' === $selected_dates_without_calendar ) {

			$warn = sprintf( 'Warning! The booking calendar for this resource with the ID = %d is already on the page. Find more details here: https://wpbookingcalendar.com/faq/why-the-booking-calendar-widget-not-show-on-page/', (int) $resource_id );

			$js_body = ''
				. 'jQuery(".widget_wpdev_booking .booking_form.form-horizontal").removeClass("form-horizontal");'
				. "\n" . 'var calendars__on_this_page_list = _wpbc.get_other_param("calendars__on_this_page") || [];'
				. "\n" . 'var visible_calendars_count = calendars__on_this_page_list.length;'
				. "\n" . 'if (visible_calendars_count !== null) {'
				. "\n" . '  for (var i=0; i<visible_calendars_count; i++) {'
				. "\n" . '    if (_wpbc.get_other_param("calendars__on_this_page")[i] === ' . intval( $resource_id ) . ') {'
				. "\n" . '      console.log("%c%s", "color: #e77; font-weight:bold", ' . wp_json_encode( $warn ) . ');'
				. "\n" . '    }'
				. "\n" . '  }'
				. "\n" . '  _wpbc.get_other_param("calendars__on_this_page")[visible_calendars_count] = ' . intval( $resource_id ) . ';'
				. "\n" . '}';

			$html .= WPBC_FE_Assets::add_jq_ready_js( $js_body, 'wpbc:dup-calendar-warn:' . intval( $resource_id ), 'footer' );

		} else {

			$js_body = ''
				. 'if (typeof(wpbc_show_cost_hints_after_few_seconds) === "function") {'
				. '  wpbc_show_cost_hints_after_few_seconds(' . intval( $resource_id ) . ');'
				. '} else if (typeof(showCostHintInsideBkForm) === "function") { showCostHintInsideBkForm(' . intval( $resource_id ) . '); }';   // Legacy Fallback.

			$html .= WPBC_FE_Assets::add_jq_ready_js( $js_body, 'wpbc:cost-hint:' . intval( $resource_id ), 'footer' );
		}

		// 2) Autofill (legacy conditions preserved).
		$autofill_js = self::build_autofill_js_body( $resource_id );
		if ( '' !== $autofill_js ) {
			$html .= WPBC_FE_Assets::add_jq_ready_js( $autofill_js, 'wpbc:autofill:' . intval( $resource_id ), 'footer' );
		}

		return $html;
	}


	/**
	 * Build ONLY the JS BODY for legacy autofill (no <script>, no ready wrapper).
	 *
	 * Now calls the shared JS function:
	 *   window.WPBC_FE.autofill_booking_form_fields( resource_id, fill_values )
	 *
	 * @param int $resource_id Booking resource ID.
	 *
	 * @return string JS body (or empty string if not applicable).
	 */
	private static function build_autofill_js_body( $resource_id ) {

		$resource_id = (int) $resource_id;

		if ( WPBC_GET_Request::has_non_empty_get( 'booking_hash' ) ) {
			return '';
		}

		$is_use_auto_fill_for_logged = get_bk_option( 'booking_is_use_autofill_4_logged_user' );
		if ( 'On' !== $is_use_auto_fill_for_logged ) {
			return '';
		}

		$curr_user = wpbc_get_current_user();
		if ( empty( $curr_user ) || ( (int) $curr_user->ID <= 0 ) ) {
			return '';
		}

		$user_nick_name = get_user_meta( $curr_user->ID, 'nickname' );
		$user_nick_name = ( empty( $user_nick_name ) ) ? '' : $user_nick_name[0];

		$fill_values = array(
			'nickname'   => (string) $user_nick_name,
			'last_name'  => (string) $curr_user->last_name,
			'first_name' => (string) $curr_user->first_name,
			'email'      => (string) $curr_user->user_email,
			'phone'      => (string) $curr_user->phone_number,
			'nb_enfant'  => (string) $curr_user->nb_enfant,
			'url'        => (string) $curr_user->user_url,
		);

		// IMPORTANT: return body only (no <script>, no ready wrapper).
		$js_body = 'if ( window.WPBC_FE && ( typeof window.WPBC_FE.autofill_booking_form_fields === "function" ) ) {';
		$js_body .= 'window.WPBC_FE.autofill_booking_form_fields(' . (int) $resource_id . ', ' . wp_json_encode( $fill_values ) . ');';
		$js_body .= '}';

		return $js_body;
	}
}

class WPBC_FE_Form_Style_Injector {

	/**
	 * Inject CSS variables into the FIRST <div ... class="... wpbc_bfb_form ..."> tag.
	 *
	 * @param string $html
	 * @param array  $css_vars  Map: '--var-name' => 'value'
	 *
	 * @return string
	 */
	public static function inject_css_vars_into_bfb_root( $html, $css_vars ) {

		$html     = (string) $html;
		$css_vars = is_array( $css_vars ) ? $css_vars : array();

		if ( empty( $css_vars ) ) {
			return $html;
		}
		if ( false === strpos( $html, 'wpbc_bfb_form' ) ) {
			return $html;
		}

		$style_append = self::build_css_vars_style_fragment( $css_vars );
		if ( '' === $style_append ) {
			return $html;
		}

		// Match FIRST <div ... class="... wpbc_bfb_form ..."> tag.
		$pattern = '/<div\b[^>]*\bclass\s*=\s*(["\'])(?:(?!\1).)*\bwpbc_bfb_form\b(?:(?!\1).)*\1[^>]*>/i';

		$result = preg_replace_callback(
			$pattern,
			function( $m ) use ( $style_append ) {

				$tag = $m[0];

				// If style already exists: append.
				if ( preg_match( '/\bstyle\s*=\s*(["\'])(.*?)\1/i', $tag, $sm ) ) {

					$quote    = $sm[1];
					$existing = (string) $sm[2];

					// Avoid double-escaping if the tag already contains entities.
					$existing = html_entity_decode( $existing, ENT_QUOTES, 'UTF-8' );

					$merged = trim( $existing );
					if ( ( '' !== $merged ) && ( ';' !== substr( $merged, -1 ) ) ) {
						$merged .= ';';
					}
					$merged .= $style_append;

					$tag = preg_replace(
						'/\bstyle\s*=\s*(["\'])(.*?)\1/i',
						'style=' . $quote . esc_attr( $merged ) . $quote,
						$tag,
						1
					);

					return $tag;
				}

				// No style attr: add it.
				$tag = rtrim( $tag, '>' ) . ' style="' . esc_attr( $style_append ) . '">';

				return $tag;
			},
			$html,
			1
		);

		// preg_replace_callback can return null on regex error.
		return ( null === $result ) ? $html : $result;
	}

	/**
	 * Build CSS vars fragment: "--a:1;--b:2;"
	 *
	 * @param array $css_vars
	 *
	 * @return string
	 */
	private static function build_css_vars_style_fragment( $css_vars ) {

		$out = '';

		foreach ( $css_vars as $name => $value ) {

			$name  = self::sanitize_css_var_name( $name );
			$value = self::sanitize_css_var_value( $value );

			// Allow "0" but skip empty.
			if ( '' === $name || '' === $value ) {
				continue;
			}

			$out .= $name . ':' . $value . ';';
		}

		return $out;
	}

	private static function sanitize_css_var_name( $name ) {

		$name = is_scalar( $name ) ? trim( (string) $name ) : '';
		if ( '' === $name ) {
			return '';
		}

		// Keep it strict: only CSS custom properties.
		if ( ! preg_match( '/^--[a-z0-9\-_]+$/i', $name ) ) {
			return '';
		}

		// Optional: enforce prefix to avoid abusing other vars (case-insensitive).
		$lower = strtolower( $name );
		if ( 0 !== strpos( $lower, '--wpbc-' ) && 0 !== strpos( $lower, '--wpbc_bfb-' ) ) {
			return '';
		}

		return $name;
	}

	private static function sanitize_css_var_value( $value ) {

		$value = is_scalar( $value ) ? trim( (string) $value ) : '';
		if ( '' === $value ) {
			return '';
		}

		/**
		 * IMPORTANT:
		 * Disallow characters that can break out of "--var:value;" into extra declarations:
		 * - ';' would start a new declaration
		 * - '{' '}' can start blocks
		 * Also strip quotes/newlines/< > to keep inline style safe.
		 */
		$value = str_replace(
			array( ';', '{', '}', '"', "'", '<', '>', "\n", "\r", "\0" ),
			'',
			$value
		);

		return trim( $value );
	}
}
