<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Calendar Scripts
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-07-19
 * @file: ../includes/_functions/calendar_scripts.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

// =====================================================================================================================
// ==  Calendar  functions  ==
// =====================================================================================================================
/**
 * Print the critical inline CSS exactly once per page.
 * - In Elementor-safe mode, collect CSS into WPBC_FE_Assets (head if possible, else footer).
 * - Legacy fallback: echo <style> in place.
 */
function wpbc_print_loader_inline_css_once() {

	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;

	$css = "
	/* Critical loader styles (scoped by class names) */
	.calendar_loader_frame {
		width: calc(341px * var(--wpbc-loader-cols, 1));
		max-width: 100%;
		height: 307px;
		display: flex;
		flex-flow: column nowrap;
		align-items: center;
		justify-content: center;
		border-radius: 5px;
		box-shadow: 0 0 2px #ccc;
		gap: 15px;
		/* Calendar variables (safe fallbacks) */
		color: var(--wpbc_cal-available-text-color, #2c3e50);
		background: rgb(from var(--wpbc_cal-available-day-color, #e6f2ff) r g b / var(--wpbc_cal-day-bg-color-opacity, 1));
		border: var(--wpbc_cal-day-cell-border-width, 1px) solid var(--wpbc_cal-available-day-color, #aacbeb);
	}
	.calendar_loader_text {
		font-size: 18px;
		text-align: center;
	}
	.calendar_loader_frame__progress_line_container {
		width: 50%;
		height: 3px;
		margin-top: 7px;
		overflow: hidden;
		background: #202020;
		border-radius: 30px;
	}
	.calendar_loader_frame__progress_line {
		width: 0%;
		height: 3px;
		background: #8ECE01;
		border-radius: 30px;
		animation: calendar_loader_bar_progress 3s infinite linear;
	}
	@keyframes calendar_loader_bar_progress { to { width: 100%; } }
	@media (prefers-reduced-motion: reduce) {
		.calendar_loader_frame__progress_line { animation: none; width: 50%; }
	}
	";

	// Elementor-safe path: attach CSS to an enqueued WPBC stylesheet.
	if ( class_exists( 'WPBC_FE_Assets' ) ) {

		// Ensure the target style is enqueued (safe-check anyway).
		if ( function_exists( 'wp_enqueue_style' ) ) {
			wp_enqueue_style( 'wpbc-ui-both', wpbc_plugin_url( '/css/wpbc_ui_both.css' ), array(), WP_BK_VERSION_NUM );                                                            // FixIn: 10.0.0.25.
		}
		// FixIn: 10.14.14.1.
		$added = WPBC_FE_Assets::add_inline_css_to_wp_style( 'wpbc-ui-both', $css, 'wpbc:calendar-loader-css' );

		if ( $added ) {
			return; // Do NOT print <style> inside content.
		}
	}

	// Legacy fallback (only when WP inline style cannot be attached).
	echo "\n" . '<style id="wpbc_calendar_loader_inline_css">' . "\n" . $css . "\n" . '</style>' . "\n";                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}



/**
 * Output a single calendar loader block (no inline <script>, includes inline CSS once).
 *
 * How it works:
 * - The external JS (enqueued here) auto-detects this block and manages the loader lifecycle.
 * - Multiple instances on the same page are supported, even with duplicate RIDs.
 * - Critical CSS is printed inline once, so loaders render correctly immediately.
 *
 * @param int $resource_id   Booking resource ID for this calendar.
 * @param int $months_number Number of months in calendar view (affects width).
 * @param int $grace_ms      Grace time in ms before showing helpful messages (default 8000).
 *
 * @return string HTML markup for the loader.
 */
function wpbc_get_calendar_loader_animation( $resource_id = 1, $months_number = 1, $grace_ms = 8000 ) {

	ob_start();

	$rid   = (int) $resource_id;
	$cols  = max( 1, (int) $months_number );
	$grace = max( 1, (int) $grace_ms );

	// Print the inline CSS once.
	wpbc_print_loader_inline_css_once();
	?>
	<div class="calendar_loader_frame calendar_loader_frame<?php echo esc_attr( $rid ); ?>"
		data-wpbc-rid="<?php echo esc_attr( $rid ); ?>"
		data-wpbc-grace="<?php echo esc_attr( $grace ); ?>"
		style="--wpbc-loader-cols: <?php echo esc_attr( $cols ); ?>;"
	>
		<div class="calendar_loader_text"><?php esc_html_e( 'Loading', 'booking' ); ?>...</div>
		<div class="calendar_loader_frame__progress_line_container">
			<div class="calendar_loader_frame__progress_line"></div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

// FixIn: 10.14.6.1.
/**
 * Keep Cloudflare Rocket Loader ON, but exclude key scripts it must not defer.
 * Prevents "jQuery is not defined" / "_wpbc is undefined" races.
 */
function wpbc_disable_cloudflare_on_calendar_script( $tag, $handle, $src = '' ) {

	// 1) Handles to exclude (extendable via filter)
	$exclude_handles = [
		// WordPress core libs:
		'jquery', 'jquery-core', 'jquery-migrate',
		'jquery-ui-core', 'jquery-ui-datepicker',

		// Booking Calendar (common handles; may vary by site):
		'wpbc_all',
		'wpbc-main-client',
		'wpbc-datepick',
		'wpbc-datepick-localize',
		'wpbc-times',
		'wpbc-time-selector',
		'wpbc-timeline-flex',
		'wpbc_capacity',
		'wpbc-popper',
		'wpbc-tipcy',
		'wpbc-imask',
	];

	$exclude_handles = apply_filters( 'wpbc_cloudflare_exclude_handles', $exclude_handles, $tag, $handle, $src );

	// 2) Decide whether to exclude this script
	$should_exclude = in_array( $handle, $exclude_handles, true );

	// 3) Fallbacks by src path (covers all Booking Calendar assets)
	if ( ! $should_exclude && is_string( $src ) ) {
		if ( strpos( $src, '/wp-content/plugins/booking/' ) !== false ) {
			$should_exclude = true;
		}
		if ( strpos( $src, '/wp-content/plugins/booking-calendar-com/' ) !== false ) {
			$should_exclude = true;
		}
	}

	if ( $should_exclude ) {
		// Add data-cfasync="false" once (prevents Rocket Loader rewrite)
		if ( strpos( $tag, 'data-cfasync=' ) === false ) {
			$tag = preg_replace( '/^<script\b/i', '<script data-cfasync="false"', $tag, 1 );
		}
		// Remove async (keep defer)
		$tag = preg_replace( '/\sasync(\s*=\s*([\'"][^\'"]*[\'"]|[^\s>]+))?/i', '', $tag );
	}

	return $tag;
}
// Use a later priority so we run after most tag modifiers.
add_filter( 'script_loader_tag', 'wpbc_disable_cloudflare_on_calendar_script', 100, 3 );



/**
 * Get HTML for the initilizing inline calendars
 *
 * @param integer $resource_id  - ID of booking resource.
 * @param integer $cal_count    - Number of months.
 * @param array   $bk_otions    - Options.
 *
 * @return string
 */
function wpbc_pre_get_calendar_html( $resource_id = 1, $cal_count = 1, $bk_otions = array() ) {

	/**
	 * SHORTCODE: [booking type=56 form_type='standard' nummonths=4 options='{calendar months_num_in_row=2 width=682px cell_height=48px}'] .
	 * OPTIONS:
	 * [months_num_in_row] => 2
	 * [width] => 341px                define: width: 100%; max-width:341px;
	 * [strong_width] => 341px     define: width:341px;
	 * [cell_height] => 48px
	 */
	$bk_otions = wpbc_parse_calendar_options( $bk_otions );

	$width             = '';
	$months_num_in_row = '';
	$cell_height       = '';

	if ( ! empty( $bk_otions ) ) {

		if ( isset( $bk_otions['months_num_in_row'] ) ) {
			$months_num_in_row = $bk_otions['months_num_in_row'];
		}

		if ( isset( $bk_otions['width'] ) ) {
			$width = 'width:100%;max-width:' . $bk_otions['width'] . ';';                                           // FixIn: 9.3.1.5.
		}
		if ( isset( $bk_otions['strong_width'] ) ) {
			$width .= 'width:' . $bk_otions['strong_width'] . ';';                                                  // FixIn: 9.3.1.6.
		}

		if ( isset( $bk_otions['cell_height'] ) ) {
			$cell_height = $bk_otions['cell_height'];
		}
		if ( isset( $bk_otions['strong_cell_height'] ) ) {                                                          // FixIn: 9.7.3.3.
			$cell_height = $bk_otions['strong_cell_height'] . '!important;';
		}
	}
	/* FixIn: 9.7.3.4 */

	if ( ! empty( $cell_height ) ) {
		// FixIn: 10.13.1.3.
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		$style = '<style type="text/css" rel="stylesheet" >.hasDatepick .datepick-inline .datepick-title-row th,.hasDatepick .datepick-inline .datepick-days-cell,.hasDatepick .datepick-inline .wpbc-cell-box{ max-height: ' . $cell_height . '; }</style>';  // FixIn: 10.12.4.2.
	} else {
		$style = '';
	}

	$booking_timeslot_day_bg_as_available = get_bk_option( 'booking_timeslot_day_bg_as_available' );

	$booking_timeslot_day_bg_as_available = ( 'On' === $booking_timeslot_day_bg_as_available ) ? ' wpbc_timeslot_day_bg_as_available' : '';

	$is_custom_width_css = ( empty( $width ) ) ? ' wpbc_no_custom_width ' : '';

	$calendar = $style .
				'<div class="wpbc_cal_container bk_calendar_frame' . $is_custom_width_css . ' months_num_in_row_' . $months_num_in_row . ' cal_month_num_' . $cal_count . $booking_timeslot_day_bg_as_available . '" style="' . $width . '">' .
				'<div id="calendar_booking' . $resource_id . '"  class="wpbc_calendar_id_' . $resource_id . '" >' .
				wpbc_get_calendar_loader_animation( $resource_id, $cal_count ) .
				'</div>' .
				'</div>';

	$booking_is_show_powered_by_notice = get_bk_option( 'booking_is_show_powered_by_notice' );
	if ( ( ! class_exists( 'wpdev_bk_personal' ) ) && ( 'On' === $booking_is_show_powered_by_notice ) ) {
		$calendar .= '<div style="font-size:7px;text-align:left;margin:0 0 10px;text-shadow: none;">Powered by <a href="https://wpbookingcalendar.com" style="font-size:7px;" target="_blank" title="Booking Calendar plugin for WordPress">Booking Calendar</a></div>';
	}

	$calendar .= '<textarea id="date_booking' . $resource_id . '" name="date_booking' . $resource_id . '" autocomplete="off" style="display:none;"></textarea>';   // Calendar code.

	$calendar .= wpbc_get_calendar_legend();                                                                            // FixIn: 9.4.3.6.

	$calendar_css_class_outer = 'wpbc_calendar_wraper';

	// FixIn: 7.0.1.24.
	$is_booking_change_over_days_triangles = get_bk_option( 'booking_change_over_days_triangles' );
	if ( 'Off' !== $is_booking_change_over_days_triangles ) {
		$calendar_css_class_outer .= ' wpbc_change_over_triangle';
	}

	// filenames,  such  as 'multidays.css'.
	$calendar_skin_name = basename( get_bk_option( 'booking_skin' ) );
	if ( wpbc_is_calendar_skin_legacy( $calendar_skin_name ) ) {
		$calendar_css_class_outer .= ' wpbc_calendar_skin_legacy';
	}

	$calendar = '<div class="' . esc_attr( $calendar_css_class_outer ) . '">' . $calendar . '</div>';

	return $calendar;
}

// >=BM - Get script for calendar activation required only  in the BM  version  for extra calendars.!
add_bk_filter( 'pre_get_calendar_html', 'wpbc_pre_get_calendar_html' );
