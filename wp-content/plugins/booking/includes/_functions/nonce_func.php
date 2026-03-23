<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Nonce functions - excluding from  front-end
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-06-14
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// FixIn: 10.1.1.2.

// =====================================================================================================================
// ==  Nonce functions  ==
// =====================================================================================================================

/**
 * Is use nonce fields at Front-Edn side in king forms
 * If we do not use nonce,  then  it can  prevent issues on websites with  cache plugins regarding these Errors: Forbidden (403) Probably nonce for this page has been expired.
 *                                                                                                     or Error: Request do not pass security check! Please refresh the page and try one more time. Please check more here https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/
 * Find more information at these pages:    https://konstantin.blog/2012/nonces-on-the-front-end-is-a-bad-idea/
 *                                          https://contactform7.com/2017/08/18/contact-form-7-49/
 *                                          https://developer.wordpress.org/apis/security/nonces/
 *
 * @return bool
 */
function wpbc_is_use_nonce_at_front_end() {
	return( 'On' === get_bk_option( 'booking_is_nonce_at_front_end' ) );
}

// ---------------------------------------------------------------------------------------------------------------------
//    S u p p o r t    f u n c t i o n s    f o r     A j a x    ///////////////
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Verify the nonce in Admin Panel          - during Ajax request
 *
 * @param $action_check
 *
 * @return bool|void
 */
function wpbc_check_nonce_in_admin_panel( $action_check = 'wpbc_ajax_admin_nonce' ){
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $nonce = ( isset($_REQUEST['wpbc_nonce']) ) ? $_REQUEST['wpbc_nonce'] : '';

	if ( '' === $nonce ) return false;	// Its was request  from  some other plugin										// FixIn: 7.2.1.10.

    if ( ! wp_verify_nonce( $nonce, $action_check ) ) {                         										// This nonce is not valid.
        ?>
        <script type="text/javascript">
			if (jQuery("#ajax_respond").length > 0 ){
				jQuery( "#ajax_respond" ).after( "<div class='wpdevelop'><div style='margin: 0 0 40px;' class='wpbc-general-settings-notice wpbc-settings-notice notice-error 0alert 0alert-warning 0alert-danger'><?php

					echo '<strong>' . esc_js( __( 'Error' , 'booking') ) . '!</strong> ';

					/* translators: 1: ... */
					echo esc_js( sprintf( __( 'Probably nonce for this page has been expired. Please %1$sreload the page%2$s.', 'booking' ), "<a href='javascript:void(0)' onclick='javascript:location.reload();'>", "</a>" ) );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
					echo '<br/>' . sprintf( esc_js( __( 'Please check more here %s', 'booking' ) ), "<a href='https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/?update=" . esc_attr( WP_BK_VERSION_NUM ) . '&ver=' . wpbc_get_version_type__and_mu() . "'>FAQ</a>" );        // FixIn: 8.8.3.6.

					?></div></div>" );
			} else if (jQuery(".ajax_respond_insert").length > 0 ){
				jQuery( ".ajax_respond_insert" ).after( "<div class='wpdevelop'><div class='alert alert-warning alert-danger'><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
					printf( __( '%1$sError!%2$s Request do not pass security check! Please refresh the page and try one more time.', 'booking' ), '<strong>', '</strong>' );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
					echo '<br/>' . sprintf( __( 'Please check more here %s', 'booking' ), 'https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/?update=' . esc_attr( WP_BK_VERSION_NUM ) . '&ver=' . wpbc_get_version_type__and_mu() );        // FixIn: 8.8.3.6.
					?></div></div>" );
			}
			if ( jQuery( "#ajax_message" ).length ){
				jQuery( "#ajax_message" ).slideUp();
			}
        </script>
        <?php
        die;
    }
	return  true;																										// FixIn: 7.2.1.10.
}


// ---------------------------------------------------------------------------------------------------------------------
// For test only,  where Nonce time can  be adjusted
// ---------------------------------------------------------------------------------------------------------------------
// add_filter( 'nonce_life', 'wpbc_nonce_life_test' );
//function wpbc_nonce_life_test( $lifespan ) {
//	return 60;  //4 * HOUR_IN_SECONDS;
//}


function wpbc_do_not_cache() {

	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}

	if ( ! defined( 'DONOTCACHEDB' ) ) {
		define( 'DONOTCACHEDB', true );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}

	if ( ! defined( 'DONOTMINIFY' ) ) {
		define( 'DONOTMINIFY', true );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}

	if ( ! defined( 'DONOTCDN' ) ) {
		define( 'DONOTCDN', true );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}

	if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
		define( 'DONOTCACHEOBJECT', true );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}

	// Set the headers to prevent caching for the different browsers.
	nocache_headers();
}
//wpbc_do_not_cache();


// =====================================================================================================================
// Exclude from  Minify  and caching from  different CACHE Plugins
// =====================================================================================================================

/**
 * Add exclusion  of minify JS files in 'WP-Optimize' plugin            // FixIn: 10.1.3.3.
 *
 *  Otherwise default rules:
 *  *plugins/booking*
 *  *jquery/jquery.min.js
 *  *jquery/jquery-migrate.min.js
 *
 * @param $excluded_filter_arr
 *
 * @return mixed
 */
function wpbc_exclude_for_wp_optimize( $excluded_filter_arr ) {

	if ( is_array( $excluded_filter_arr ) ) {
		$excluded_filter_arr[] = '*/plugins/booking*';
		$excluded_filter_arr[] = '*/jquery/jquery.min.js';
		$excluded_filter_arr[] = '*/jquery/jquery-migrate.min.js';
	}

	return $excluded_filter_arr;
}
add_filter( 'wp-optimize-minify-default-exclusions', 'wpbc_exclude_for_wp_optimize', 10, 1 );

// FixIn: 10.14.4.2. (loader made cache-proof)
function wpbc_get_exclude_scripts_arr_for_wp_rocket() {

	$plugin_path = wpbc_make_link_relative( WPBC_PLUGIN_URL );

	$exclusions = array();
	// Exclude jQuery
	$exclusions[] = '/jquery(-migrate)?-?([0-9.]+)?(.min|.slim|.slim.min)?.js(\?(.*))?( |\'|"|>)';

	// Exclude plugin  JS files
	$exclusions[] = $plugin_path . '/(.*)';

	/*
		$exclusions[] = '/wp-content/plugins/booking(.*)/_dist/all/_out/wpbc_all.js';
		$exclusions[] = '/wp-content/plugins/booking(.*)/js/datepick/jquery.datepick.wpbc.9.0.js';
		$exclusions[] = '/wp-content/plugins/booking(.*)/js/wpbc_time-selector.js';
		$exclusions[] = '/wp-content/plugins/booking(.*)/vendors/_custom/tippy.js';
		$exclusions[] = '/wp-content/plugins/booking(.*)/vendors/_custom/popper/popper.js';
		$exclusions[] = '/wp-content/plugins/booking(.*)/inc/js/biz_m.js';
	*/

	// Exclude  some important plugin  JS vars
	$exclusions[] = 'wpbc_init__head';
	$exclusions[] = 'wpbc_url_ajax';
	$exclusions[] = 'booking_max_monthes_in_calendar';
	$exclusions[] = 'wpbc_define_tippy_popover';
	$exclusions[] = 'flex_tl_table_loading';

	$exclusions[] = '(.*)_wpbc.(.*)';
	$exclusions[] = 'moveOptionalElementsToGarbage';
	$exclusions[] = ' wpbc_(.*)';


	// Old way to  exclude all
	$exclusions[] = '/wp-content/plugins/booking(.*)/(.*)';
	$exclusions[] = '(.*)wpbc(.*)';

	return $exclusions;
}

/**
 * Exclude JS scripts from Delay JS  in the WP Rocket plugin
 *
 * @param $exclusions
 *
 * @return mixed
 */
function wpbc_exclude_from_delay_for_wp_rocket( $other_exclusions ) {
 // return $exclusions;
	$wpbc_exclusions = wpbc_get_exclude_scripts_arr_for_wp_rocket();

	$exclusions = array_merge( $other_exclusions, $wpbc_exclusions );

	return $exclusions;
}
add_filter( 'rocket_delay_js_exclusions',   'wpbc_exclude_from_delay_for_wp_rocket' );

/**
 * Don't defer our plugin scripts
 */
add_filter( 'rocket_defer_js_exclusions',   'wpbc_exclude_from_delay_for_wp_rocket' );

/**
 * If WP Rocket is deferring inline JS, exclude our initializer by a unique key
 */
add_filter( 'rocket_defer_inline_exclusions',   'wpbc_exclude_from_delay_for_wp_rocket' );
