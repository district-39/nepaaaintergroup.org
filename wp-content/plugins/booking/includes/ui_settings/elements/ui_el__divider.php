<?php
/**
 * Admin Panel UI - Parts
 *
 * @version  1.2
 * @package  Any
 * @category Page Structure in Admin Panel
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-02-15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Vertical Divider
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__divider_vertical( $args = array() ) {

	$defaults = array(
		'style'           => '',
		'container_class' => '',
		'container_style' => '',
		'attr'            => array(),
		'class'           => 'wpbc_ui_el__vertical_line',
	);
	$params   = wp_parse_args( $args, $defaults );

	?><div class="wpbc_ui_el_container wpbc_ui_el__divider <?php echo esc_attr( $params['container_class'] ); ?>"
		   style="<?php echo esc_attr( $params['container_style'] ); ?>"><div class="wpbc_ui_el <?php echo esc_attr( $params['class'] ); ?>"
		 style="<?php echo esc_attr( $params['style'] ); ?>"
		<?php echo wpbc_get_custom_attr( $params );   /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	></div></div><?php
}


/**
 * Horizontal Divider
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__divider_horizontal( $args = array() ) {

	$defaults = array(
		'style'           => '',
		'container_class' => '',
		'container_style' => '',
		'attr'            => array(),
		'class'           => 'wpbc_ui_el__horisontal_line',
	);
	$params   = wp_parse_args( $args, $defaults );

	wpbc_ui_el__divider_vertical( $params );
}


/**
 * Horizontal Space
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__space_horizontal( $args = array() ) {
	?>
	<div class="wpbc_ui_el__divider">
		<div class="wpbc_ui_el__vertical_space"></div>
	</div>
	<?php
}


/**
 * Vertical Space
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__space_vertical( $args = array() ) {
	?>
	<div class="wpbc_ui_el__divider">
		<div class="wpbc_ui_el__horisontal_space"></div>
	</div>
	<?php
}