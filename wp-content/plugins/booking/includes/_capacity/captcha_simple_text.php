<?php

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly            // FixIn: 9.8.0.4.

// ---------------------------------------------------------------------------------------------------------------------
// Main function  for       ajax_ WPBC_AJX_BOOKING__CREATE
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Check CAPTCHA during Ajax request from  booking form      and if it's incorrect,  then STOP execution  and send request  to  show warning
 *
 * @param $request_params           [ 'captcha_user_input'=> '...', 'captcha_chalange'=> '...']
 * @param $is_from_admin_panel      true | false
 * @param $original_ajx_search_params       usually $_REQUEST[ $request_prefix ]
 *
 * @return void                     wp_send_json() TO front-end  or  skip  and continue
 */
function wpbc_captcha__in_ajx__check( $request_params, $is_from_admin_panel , $original_ajx_search_params ) {

	if (
			( ( 'On' === get_bk_option( 'booking_is_use_captcha' ) ) || ( WPBC_NEW_FORM_BUILDER ) ) &&
			( ! $is_from_admin_panel ) &&
			( ( isset( $original_ajx_search_params['captcha_user_input'] ) ) && ( isset( $original_ajx_search_params['captcha_chalange'] ) ) )
	) {

		if ( ! wpbc_captcha__simple__is_ansfer_correct( $request_params['captcha_user_input'], $request_params['captcha_chalange'] ) ) {

			$captcha_arr = wpbc_captcha__simple__generate_new();

			$ajx_data_arr                                    = array();
			$ajx_data_arr['status']                          = 'error';
			$ajx_data_arr['status_error']                    = 'captcha_simple_wrong';
			$ajx_data_arr['captcha__simple']                 = $captcha_arr;
			$ajx_data_arr['ajx_after_action_message']        = __( 'The code you entered is incorrect', 'booking' );
			$ajx_data_arr['ajx_after_action_message_status'] = 'warning';

			wp_send_json(
				array(
					'ajx_data'           => $ajx_data_arr,
					'ajx_search_params'  => $original_ajx_search_params,
					'ajx_cleaned_params' => $request_params,
					'resource_id'        => $request_params['resource_id'],
				)
			);
			// After this page will die;.
		}
	}
}



// ---------------------------------------------------------------------------------------------------------------------
// CAPTCHA  Support
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Is entered CAPTCHA correct ?
 *
 * @param string $captcha_user_input		user  entrance
 * @param string $captcha_chalange			chalange
 *
 * @return bool
 */
function wpbc_captcha__simple__is_ansfer_correct( $captcha_user_input, $captcha_chalange ) {

	if ( ( empty( $captcha_user_input ) ) || ( empty( $captcha_chalange ) ) ) {
		return false;
	}

	$captcha_instance = new wpdevReallySimpleCaptcha();
	$correct = $captcha_instance->check( $captcha_chalange, $captcha_user_input );

	return $correct;
}


/**
 * Generate new CAPTCHA image and return  URL  to  this image and challenge code
 *
 * @return array		[
							'url'       => $captcha_url,
							'challenge' => $captcha_challenge
						]
 */
function wpbc_captcha__simple__generate_new() {

	$captcha_instance = new wpdevReallySimpleCaptcha();

	// Clean up dead files older than  2 minutes
	$captcha_instance->cleanup( 2 );                    // FixIn: 7.0.1.67.

	//$captcha_instance->img_size = array( 72, 24 );
	/* Background color of CAPTCHA image. RGB color 0-255 */
	//$captcha_instance->bg = array( 0, 0, 0 );//array( 255, 255, 255 );
	/* Foreground (character) color of CAPTCHA image. RGB color 0-255 */
	//$captcha_instance->fg = array( 255, 255, 255 );//array( 0, 0, 0 );
	/* Coordinates for a text in an image. I don't know the meaning. Just adjust. */
	//$captcha_instance->base = array( 6, 18 );
	/* Font size */
	//$captcha_instance->font_size = 14;
	/* Width of a character */
	//$captcha_instance->font_char_width = 15;
	/* Image type. 'png', 'gif' or 'jpeg' */
	//$captcha_instance->img_type = 'png';


	$word   = $captcha_instance->generate_random_word();
	$prefix = wp_rand();

	$captcha_instance->generate_image( $prefix, $word );

	$filename    = $prefix . '.png';

	$captcha_url       = WPBC_PLUGIN_URL . '/js/captcha/tmp/' . $filename;
	$captcha_challenge = substr( $filename, 0, strrpos( $filename, '.' ) );

	return array(
					'url'       => $captcha_url,
					'challenge' => $captcha_challenge
				);
}


/**
 * Generate initial HTML content for CAPTCHA in booking form
 *
 * @param $resource_id
 *
 * @return string|true
 */
function wpbc_captcha__simple__generate_html_content( $resource_id ) {

	if ( true !== wpbc_captcha__simple__is_installed() ) {
		return wpbc_captcha__simple__is_installed();
	}

	$captcha_arr = wpbc_captcha__simple__generate_new();

	$captcha_url       = $captcha_arr['url'];
	$captcha_challenge = $captcha_arr['challenge'];
	$html  = '<span class="wpbc_text_captcha_container wpdev-form-control-wrap ">';
	$html .=   '<input  autocomplete="off" type="hidden" name="wpdev_captcha_challenge_' . $resource_id . '"  id="wpdev_captcha_challenge_' . $resource_id . '" value="' . $captcha_challenge . '" />';
	$html .=   '<input  autocomplete="off" type="text" class="captachinput" value="" name="captcha_input' . $resource_id . '" id="captcha_input' . $resource_id . '" />';
	// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
	$html .=   '<img class="captcha_img"  id="captcha_img' . $resource_id . '" alt="To show CAPTCHA, please deactivate cache plugin or exclude this page from caching or disable CAPTCHA at WP Booking Calendar - Settings General page in Form Options section." src="' . $captcha_url . '" />';
	$html .= '</span>';

	return $html;
}


/**
 * Check  if captcha can  work  here
 *
 * @return string|true         if true then  can work,  Otherwise return  error message
 */
function wpbc_captcha__simple__is_installed() {

	// FixIn: 8.8.3.5.
	if ( function_exists( 'gd_info' ) ) {

		return  true;
		/*
		$gd_info = gd_info();
		if ( isset( $gd_info['GD Version'] ) ) {
			$gd_info = $gd_info['GD Version'];
		} else {
			$gd_info = wp_json_encode( $gd_info );
		}
		*/
	} else {
		return  '<strong>Error!</strong>  CAPTCHA requires the GD library activated in your PHP configuration.'
		       .'Please check more <a href="https://wpbookingcalendar.com/faq/captcha-showing-problems/">here</a>.';
	}


}


/**
 * Decide if CAPTCHA can be shown for current request/context.
 *
 * Rules:
 * - Must be enabled in settings (booking_is_use_captcha = On)
 * - Must not be a WP Admin page, except plugin settings form page
 *
 * @return bool
 */
function wpbc_booking_form_is_captcha_allowed_here() {

	if ( ( 'On' !== get_bk_option( 'booking_is_use_captcha' ) ) && ( ! WPBC_NEW_FORM_BUILDER ) ) {
		return false;
	}

	// In admin area we usually do not show CAPTCHA, except settings form page.
	// if ( is_admin() && ( ! wpbc_is_settings_form_page() ) ) { return false; }  //.

	// old URI-based logic for edge cases, keep it as extra guard:.
	$server_request_uri = ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';   // phpcs:ignore WordPress.Security.NonceVerification.Missing

	// Optional legacy-style admin URI check.
	$admin_uri = ltrim( str_replace( get_site_url( null, '', 'admin' ), '', admin_url( 'admin.php?' ) ), '/' );

	if ( ( '' !== $server_request_uri ) && ( false !== strpos( $server_request_uri, $admin_uri ) ) && ( ! wpbc_is_settings_form_page() ) ) {
		return false;
	}

	return true;
}


/**
 * Default CAPTCHA placeholder replacement for booking form HTML.
 *
 * Replaces [captcha] token with generated CAPTCHA markup (if enabled),
 * or removes the token when CAPTCHA is disabled/not allowed in current context.
 *
 * Add-ons can:
 * - Replace HTML entirely by filtering 'wpbc_booking_form_captcha_html'
 * - Or override this whole replacement by using a lower priority on
 *   'wpbc_booking_form_html__create_captha' / '__create_captcha'
 *
 * @param string $form_html    Booking form HTML.
 * @param int    $resource_id  Booking resource ID.
 *
 * @return string
 */
function wpbc_booking_form_html__create_captcha_default( $form_html, $resource_id ) {

	if ( false === strpos( $form_html, '[captcha]' ) ) {
		return $form_html;
	}

	$resource_id = (int) $resource_id;

	// If CAPTCHA is not allowed here, remove placeholder.
	if ( ! wpbc_booking_form_is_captcha_allowed_here() ) {
		return str_replace( '[captcha]', '', $form_html );
	}

	/**
	 * Filter: Provide CAPTCHA HTML for booking form.
	 *
	 * Return non-empty string to supply your own CAPTCHA markup (addons).
	 * Return empty string to let WPBC fallback to the built-in simple CAPTCHA.
	 *
	 * @param string $captcha_html  CAPTCHA HTML (empty by default).
	 * @param int    $resource_id   Booking resource ID.
	 * @param array  $args          Context args.
	 */
	$captcha_html = apply_filters( 'wpbc_booking_form_captcha_html', '', $resource_id,
		array(
			'context'     => 'booking_form',
			'placeholder' => '[captcha]',
		)
	);

	if ( '' === $captcha_html ) {
		// Core fallback (your existing generator).
		$captcha_html = wpbc_captcha__simple__generate_html_content( $resource_id );
	}

	return str_replace( '[captcha]', $captcha_html, $form_html );
}
add_filter( 'wpbc_booking_form_html__create_captcha', 'wpbc_booking_form_html__create_captcha_default', 10, 2 );


/**
 * How an add-on supplies a different CAPTCHA (example).
 * add_filter( 'wpbc_booking_form_captcha_html', 'my_addon_wpbc_captcha_html', 20, 3 );
 *
 *
 * Provide custom CAPTCHA HTML (e.g., reCAPTCHA/hCaptcha).
 *
 * @param string $captcha_html Current HTML (empty by default).
 * @param int    $resource_id  Booking resource ID.
 * @param array  $args         Context args.
 *
 * @return string
 *
 * function my_addon_wpbc_captcha_html( $captcha_html, $resource_id, $args ) {
 *
 * // Only replace on booking form rendering context.
 * if ( empty( $args['context'] ) || ( 'booking_form' !== $args['context'] ) ) {
 * return $captcha_html;
 * }
 *
 * // Return your custom markup here.
 * return '<div class="my-addon-captcha">...</div>';
 * }
 */