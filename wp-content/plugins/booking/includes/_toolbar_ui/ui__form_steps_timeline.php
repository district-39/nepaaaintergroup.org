<?php
/**
 * Templates functions for showing Steps Timeline in Booking form  and in Wizard.
 *
 * @version  1.0
 * @package  Booking Calendar
 * @category Templates Fucntions
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-01-28
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                             // Exit, if accessed directly.
}

// FixIn: 10.9.6.6.

// ---------------------------------------------------------------------------------------------------------------------
// Steps Timeline in Booking form
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get html for the 'Steps Timeline'
 *
 * @param int $steps_count        - total number of steps.
 * @param int $actual_step_number - active step.
 *
 * @return string
 */
function wpbc_ui__steps_timeline__get_html( $steps_count, $actual_step_number = 1 ) {

	ob_start();

	?>
	<div class="wpbc_steps_for_timeline_container">
		<div class="wpbc_steps_for_timeline">
		<?php

		$css_class_for_step = '';
		$css_class_for_line = '';
		$is_line_exist      = false;

		for ( $i = 1; $i <= $steps_count; $i++ ) {

			$is_line_exist = ( $i > 1 ) ? true : false;

			if ( $actual_step_number > $i ) {
				$css_class_for_step = ' wpbc_steps_for_timeline_step_completed';
				$css_class_for_line = 'wpbc_steps_for_timeline_line_active';
			} elseif ( $actual_step_number === $i ) {
				$css_class_for_step = ' wpbc_steps_for_timeline_step_active';
				$css_class_for_line = 'wpbc_steps_for_timeline_line_active';
			} else {
				$css_class_for_step = '';
				$css_class_for_line = '';
			}

			if ( $is_line_exist ) {
				echo "<div class='wpbc_steps_for_timeline_step_line " . esc_attr( $css_class_for_line ) . "' ></div >";
			}
			echo "<div class='wpbc_steps_for_timeline_step " . esc_attr( $css_class_for_step ) . "'>";
			echo wpbc_ui__steps_timeline__get_icons();   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}
		?>
		</div>
	</div>
	<?php

	$html = ob_get_clean();

	return $html;
}


/**
 * Get icons for the 'Steps Timeline'
 *
 * @return false|string
 */
function wpbc_ui__steps_timeline__get_icons() {

	ob_start();
	?>
	<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon icon-success" data-icon="check" data-prefix="fas" focusable="false" aria-hidden="true" width="10" height="10">
		<path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>
	</svg>
	<svg viewBox="0 0 352 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon icon-failed" data-icon="times" data-prefix="fas" focusable="false" aria-hidden="true" width="8" height="11">
		<path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path>
	</svg>
	<?php

	return ob_get_clean();
}


/**
 * Update [steps_timeline] shortcode
 *
 * @param string $form_content             - Form Content.
 * @param int    $resource_id              - Resource ID.
 * @param string $custom_booking_form_name - Custom booking form name ?.
 *
 * @return string
 */
function wpbc_update_bookingform_content__steps_timeline( $form_content, $resource_id, $custom_booking_form_name = '' ) {

	//	$hint_html = wpbc_ui__steps_timeline__get_html( 3, 2 );
	//	$form_content = str_replace( '[steps_timeline]', $hint_html, $form_content );

	/**
	 * Example: array(
	 *                    content    => "<span class="wpbc_top_news_dismiss">[wpbc_dismiss6764]</span>"
	 *                    shortcodes => array(
	 *                                         'wpbc_dismiss6764' => array(
	 *                                                                        shortcode => "[wpbc_dismiss6764]",
	 *                                                                        params => array( id => "wpbc_top_news__offer_2023_04_21" )
	 *                                                                        shortcode_original => "[wpbc_dismiss id="wpbc_top_news__offer_2023_04_21"]"
	 *                                                                    )
	 *                                        )
	 *                )
	 */

	$form_content   = str_replace( 'steps_timline', 'steps_timeline', $form_content );
	$shortcodes_arr = wpbc_get_shortcodes_in_text__as_unique_replace( $form_content, array( 'steps_timeline' ) );
	$string         = $shortcodes_arr['content'];


	foreach ( $shortcodes_arr['shortcodes'] as $shortcode_text_to_replace => $shortcode_params_arr ) {
		$steps_count = 1;
		$step_number = 1;

		if ( isset( $shortcode_params_arr['params']['steps_count'] ) ) {
			$steps_count = intval( $shortcode_params_arr['params']['steps_count'] );
		}
		if ( isset( $shortcode_params_arr['params']['active_step'] ) ) {
			$step_number = intval( $shortcode_params_arr['params']['active_step'] );
		}
		$new_html = '<span class="wpbc_steps_for_timeline__'.esc_attr($shortcode_text_to_replace).'">';
		$new_html .= wpbc_ui__steps_timeline__get_html( $steps_count, $step_number );
		$new_html .= '</span>';
		if ( isset( $shortcode_params_arr['params']['color'] ) ) {
			$color = esc_attr( $shortcode_params_arr['params']['color'] );
			$new_html .= '<style type="text/css"> .booking_form_div .wpbc_steps_for_timeline__'.esc_attr($shortcode_text_to_replace).' .wpbc_steps_for_timeline_container { --wpbc_steps_for_timeline_active_color: ' . $color . '; } </style>';
		}
		$string = str_replace( '[' . $shortcode_text_to_replace . ']', $new_html, $string );
	}

	return $string;
}

add_filter( 'wpbc_booking_form_content__after_load', 'wpbc_update_bookingform_content__steps_timeline', 10, 3 );
