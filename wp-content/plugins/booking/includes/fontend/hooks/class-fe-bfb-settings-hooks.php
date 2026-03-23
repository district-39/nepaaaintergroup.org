<?php
/**
 * BFB Settings -> Front-End Booking Form Post-Processing Hooks
 *
 * This file registers hook callbacks that apply **BFB form settings** (decoded JSON settings array) to the final booking form HTML that is rendered on the front-end.
 *
 * These hooks are executed inside `WPBC_FE_Form_Body_Renderer::render()` in this order:
 *
 * 1) `wpbc_booking_form__body_html__before_postprocess`        -->   raw **form body HTML only** (no injected calendar/captcha/extra calendars yet).
 *
 * 2) `wpbc_booking_form__html__before_wrapper`                 -->   composed **form HTML** where calendar/captcha/extra calendars already injected,
 *
 * 3) `wpbc_booking_form__wrapped_html__before_inline_scripts   -->   full **wrapped HTML** including the wrapper `<div class="wpbc_container wpbc_form ...">` and the `<form>` markup
 *
 * 4) `wpbc_booking_form__wrapped_html__after_inline_scripts`   -->   the final HTML after inline scripts are appended.
 *
 * Notes:
 * - `$bfb_settings` is expected to be an array like:
 *   [
 *     'options'       => [ 'booking_form_theme' => 'wpbc_theme_dark_1', ... ]
 *   ]
 *
 * @package Booking Calendar
 * @since   11.0.x
 * @file    ../includes/fontend/hooks/class-fe-bfb-settings-hooks.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ['options']['booking_form_layout_width'] - > Inject per-form CSS variables:  into the BFB form root wrapper.   On Step 1.
 *
 * Purpose:
 * - If the form body contains the BFB root element (class `wpbc_bfb_form`),
 *   inject CSS Custom Properties (variables) into its `style` attribute.
 *
 * This hook must run **before** legacy post-processing inserts calendars/captcha, because it targets the BFB body root and should not depend on wrapper existence.
 *
 * Expected `$bfb_settings` format:  - `$bfb_settings['css_vars']` is an associative array: `--var-name` => `value`.
 *
 * @since  11.0.x
 *
 * @param string $form_html               Raw booking form body HTML (no calendar/captcha injected yet).
 * @param array  $bfb_settings            Decoded BFB settings array (may be empty).
 * @param int    $resource_id             Booking resource ID.
 * @param string $custom_booking_form_name Booking form slug/name (legacy: "standard").
 *
 * @return string Filtered body HTML.
 */
function wpbc_booking_form__body_html__before_postprocess__apply_bfb_vars( $form_html, $bfb_settings, $resource_id, $custom_booking_form_name ) {

	$form_html    = (string) $form_html;
	$bfb_settings = is_array( $bfb_settings ) ? $bfb_settings : array();

	// If body has no BFB root, do nothing (fast path).
	if ( false === strpos( $form_html, 'wpbc_bfb_form' ) ) {
		return $form_html;
	}

	// Local helper (no global function redeclare risk).
	$sanitize_css_length = function( $v ) {
		$v = trim( (string) $v );
		if ( '' === $v ) {
			return '';
		}
		// Allow only: number + unit.
		return preg_match( '/^-?\d+(?:\.\d+)?(?:%|px|rem|em|vw|vh)$/', $v ) ? $v : '';
	};

	$width = '';
	if ( ! empty( $bfb_settings['options'] ) && is_array( $bfb_settings['options'] ) ) {
		$width = isset( $bfb_settings['options']['booking_form_layout_width'] )
			? $sanitize_css_length( $bfb_settings['options']['booking_form_layout_width'] )
			: '';
	}

	// If no valid width -> do nothing (keeps HTML unchanged).
	if ( '' === $width ) {
		return $form_html;
	}

	$css_vars = ( ! empty( $bfb_settings['css_vars'] ) && is_array( $bfb_settings['css_vars'] ) )
		? $bfb_settings['css_vars']
		: array();

	// Use a dedicated CSS var name.
	$css_vars['--wpbc-bfb-booking_form_layout_width'] = $width;

	return WPBC_FE_Form_Style_Injector::inject_css_vars_into_bfb_root( $form_html, $css_vars );
}
add_filter( 'wpbc_booking_form__body_html__before_postprocess', 'wpbc_booking_form__body_html__before_postprocess__apply_bfb_vars', 10, 4 );


/**
 * ['options'][booking_form_theme] -> Apply a "theme" CSS class to the main booking form wrapper container.   On Step 3.
 *
 * Purpose:
 * - Add `$bfb_settings['options']['booking_form_theme']` as an extra class to the FIRST wrapper container:
 *   `<div class="... wpbc_container wpbc_form ...">`
 *
 * Why here:
 * - The wrapper container does not exist until `WPBC_FE_Form_Wrapper::wrap()` runs, therefore this must be executed on the wrapped HTML stage.
 *
 * Potential limitations:
 * - This implementation supports a **single** class value. If you plan to support multiple
 *   classes in one setting (space-separated), you should split and sanitize each class.
 *
 * @since  11.0.x
 *
 * @param string $wrapped_html            Fully wrapped booking form HTML (before inline scripts appended).
 * @param array  $bfb_settings            Decoded BFB settings array (may be empty).
 * @param int    $resource_id             Booking resource ID.
 * @param string $custom_booking_form_name Booking form slug/name (legacy: "standard").
 *
 * @return string Filtered wrapped HTML.
 */
function wpbc_booking_form__wrapped_html__before_inline_scripts__apply_theme_class( $wrapped_html, $bfb_settings, $resource_id, $custom_booking_form_name ) {

	$wrapped_html = (string) $wrapped_html;
	$bfb_settings = is_array( $bfb_settings ) ? $bfb_settings : array();

	if ( empty( $bfb_settings['options']['booking_form_theme'] ) ) {
		return $wrapped_html;
	}

	// Get theme class(es).
	$raw_theme     = trim( (string) $bfb_settings['options']['booking_form_theme'] );
	$theme_class_arr = array(); // token => true .
	$parts = preg_split( '/\s+/', $raw_theme, - 1, PREG_SPLIT_NO_EMPTY );
	foreach ( $parts as $part ) {
		$tok = sanitize_html_class( (string) $part );
		if ( '' === $tok ) { continue; }
		$theme_class_arr[ $tok ] = true;
	}
	$theme_class_arr = array_keys( $theme_class_arr );
	if ( empty( $theme_class_arr ) ) {
		return $wrapped_html;
	}


	/**
	 * Find the first wrapper container:
	 * <div class="... wpbc_container wpbc_form ...">
	 *
	 * BUG CHECK:
	 * - The pattern is "tempered" to find the class attribute containing both tokens.
	 * - It does not require order (wpbc_container can appear before/after wpbc_form).
	 * - It matches the opening <div ...> tag only.
	 */
	$pattern = '/<div\b[^>]*\bclass\s*=\s*(["\'])(?:(?!\1).)*\bwpbc_container\b(?:(?!\1).)*\bwpbc_form\b(?:(?!\1).)*\1[^>]*>/i';

	return preg_replace_callback( $pattern, function ( $m ) use ( $theme_class_arr ) {

		$tag = $m[0];

		if ( preg_match( '/\bclass\s*=\s*(["\'])(.*?)\1/i', $tag, $cm ) ) {

			$quote   = $cm[1];
			$classes = (string) $cm[2];

			// Optional but recommended: remove any existing wpbc_theme_* to avoid conflicts.
			$classes = preg_replace( '/\bwpbc_theme_[A-Za-z0-9_-]+\b/', '', $classes );
			$classes = trim( preg_replace( '/\s+/', ' ', $classes ) );

			// Add missing theme tokens.
			foreach ( $theme_class_arr as $tok ) {
				if ( false === strpos( ' ' . $classes . ' ', ' ' . $tok . ' ' ) ) {
					$classes = trim( $classes . ' ' . $tok );
				}
			}

			$tag = preg_replace( '/\bclass\s*=\s*(["\'])(.*?)\1/i', 'class=' . $quote . esc_attr( $classes ) . $quote, $tag, 1 );
		}

		return $tag;
	}, $wrapped_html, 1 );

}
add_filter( 'wpbc_booking_form__wrapped_html__before_inline_scripts', 'wpbc_booking_form__wrapped_html__before_inline_scripts__apply_theme_class', 10, 4 );


/**
 * ['options']['booking_type_of_day_selections'] -> Set per-form day selection mode for calendar init.
 *
 * Uses legacy JS global: bk_days_selection_mode = 'single'|'multiple'
 *
 * @param string $wrapped_html
 * @param array  $bfb_settings
 * @param int    $resource_id
 * @param string $custom_booking_form_name
 *
 * @return string
 */
function wpbc_booking_form__wrapped_html__before_inline_scripts__apply_day_selection_mode( $wrapped_html, $bfb_settings, $resource_id, $custom_booking_form_name ) {

	$wrapped_html = (string) $wrapped_html;
	$bfb_settings = is_array( $bfb_settings ) ? $bfb_settings : array();

	$mode = '';
	if ( ! empty( $bfb_settings['options'] ) && is_array( $bfb_settings['options'] ) ) {
		if ( isset( $bfb_settings['options']['booking_type_of_day_selections'] ) ) {
			$mode = (string) $bfb_settings['options']['booking_type_of_day_selections'];
		}
	}

	if ( ( 'single' !== $mode ) && ( 'multiple' !== $mode ) ) {
		return $wrapped_html;
	}

	$rid = (int) $resource_id;
	$fn  = ( 'single' === $mode ) ? 'wpbc_cal_ready_days_select__single' : 'wpbc_cal_ready_days_select__multiple';

	$js_body  = '(function(){';
	$js_body .= 'var rid=' . $rid . ';';
	$js_body .= 'var tries=0;';
	$js_body .= 'function run(){';
	$js_body .= '  if (typeof window.' . $fn . ' === "function") { window.' . $fn . '(rid); return; }';
	$js_body .= '  if (tries++ < 40) { setTimeout(run,50); }';
	$js_body .= '}';
	$js_body .= 'run();';
	$js_body .= '}());';

	$can_use_wp_inline = ( ! wp_doing_ajax() );

	// Elementor safe adding inline scripts after loaded of 'wpbc_all' script.
	if ( $can_use_wp_inline && class_exists( 'WPBC_FE_Assets' ) && function_exists( 'wp_script_is' ) ) {

		// 1) Ensure the handle exists in WP_Scripts *now* (register if needed).
		if ( ! wp_script_is( 'wpbc_all', 'registered' ) && function_exists( 'wp_register_script' ) ) {
			wp_enqueue_script( 'wpbc_all', wpbc_plugin_url( '/_dist/all/_out/wpbc_all.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
		}

		// 2) Enqueue it (safe even if it will be enqueued later anyway).
		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'wpbc_all' );
		}

		$assets_key = 'wpbc:day-select:' . $rid . ':' . md5( (string) $custom_booking_form_name . ':' . $mode );

		$added = WPBC_FE_Assets::add_jq_ready_js_to_wp_script( 'wpbc_all', $js_body, $assets_key );

		if ( $added ) {
			return $wrapped_html; // no <script> printed in content.
		}
	}

	// Fallback only if WP inline could not be attached.
	$wrapped_html .= "\n" . '<script type="text/javascript">' . "\n" . $js_body . "\n" . '</script>' . "\n";
	return $wrapped_html;
}
add_filter( 'wpbc_booking_form__wrapped_html__before_inline_scripts', 'wpbc_booking_form__wrapped_html__before_inline_scripts__apply_day_selection_mode', 9, 4 );
