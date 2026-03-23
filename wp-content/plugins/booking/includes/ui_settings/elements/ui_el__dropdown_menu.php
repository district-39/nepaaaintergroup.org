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
 * Show Drop Down menu.
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__dropdown_menu( $args ) {

	$defaults = array(
		'title'           => '',
		'hint'            => '',    // Example : array ->  'title' => 'Manage bookings' , 'position' => 'top' .
		'font_icon'       => '',

		'title_html'      => '',    // Alternative to  'title'.
		'svg_icon'        => '',    // Alternative to 'font_icon'.
		'svg_icon_class'  => 'wpbc_svg_icon_class',
		'svg_icon_style'  => '',

		'position'        => '',
		'has_down_arrow'  => true,    // Show or Hide down arrow at  right side.
		'has_border'      => false,   // Show border,  like in selectbox.
		'style'           => '',
		'class'           => '',
		'container_class' => '',
		'container_style' => '',
		'attr'            => array(),
		'items'           => array(),
	);
	$params   = wp_parse_args( $args, $defaults );


	?><div class="wpbc_ui_el wpbc_ui_el_container wpbc_ui_el__dropdown <?php echo esc_attr( $params['container_class'] ); ?>" style="<?php echo esc_attr( $params['container_style'] ); ?>">
	<a href="javascript:void(0)"
		data-toggle="wpbc_dropdown"
		aria-expanded="true"
		class="ul_dropdown_menu_toggle
		<?php
		echo esc_attr( ( ! empty( $params['has_down_arrow'] ) ) ? ' has_down_arrow ' : '' );
		echo esc_attr( ( ! empty( $params['has_border'] ) ) ? ' has_border ' : '' );
		echo esc_attr( ( ! empty( $params['hint'] ) ) ? ' tooltip_' . $params['hint']['position'] . ' ' : '' );
		echo esc_attr( ' ' . $params['class'] . ' ' );
		?>
		"
		style="<?php echo esc_attr( $params['style'] ); ?>"
		<?php
		if ( ! empty( $params['hint'] ) ) {
			echo ' title="' . esc_attr( $params['hint']['title'] ) . '" ';
		}
		echo wpbc_get_custom_attr( $params );  /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
		?>
	>
	<?php

	if ( ! empty( $params['font_icon'] ) ) {
		// Font Icon.
		?><i class="menu_icon icon-1x <?php echo wp_kses_post( $params['font_icon'] ); ?>"></i><?php
	}
	if ( ! empty( $params['svg_icon'] ) ) {
		// SVG Icon.
		?><i class="menu_icon <?php echo esc_attr($params['svg_icon_class']); ?>" style="<?php echo esc_attr($params['svg_icon_style']); ?>;background-image: url('<?php echo esc_attr($params['svg_icon']); ?>');"></i><?php
	}
	if ( ! empty( $params['title'] ) ){
		?><span class="nav-tab-text"><?php echo wp_kses_post( $params['title'] ); ?></span><?php
	}
	if ( ! empty( $params['title_html'] ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $params['title_html'];
	}
	?></a><?php



	?><ul class="ul_dropdown_menu" role="menu" style="<?php echo( ( $params['position'] == 'right' ) ? 'right:0px; left:auto;' : '' ); ?>">
	<?php

	foreach ( $params['items'] as $items ) {

		if ( ( empty( $items['type'] ) ) && ( ! empty( $items['html'] ) ) ) {
			$items['type'] = 'html';
		}
		if ( empty( $items['type'] ) ) {
			continue;
		}
		switch ( $items['type'] ) {

			case 'divider':
				echo '<li class="divider' . esc_attr( ( ! empty( $items['class'] ) ) ? ' ' . $items['class'] : '' ) . '"></li>';
				break;

			case 'html':
				echo '<li ' . wpbc_get_custom_attr( $items ) . '>';                                             // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $items['html'];                                                                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</li>';
				break;

			case 'header':
				 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<li ' . wpbc_get_custom_attr( $items ) . ' class="dropdown-header' . esc_attr( ( ! empty( $items['class'] ) ) ? ' ' . $items['class'] : '' ) . '">';
				echo wp_kses_post( $items['title'] );
				echo '</li>';
				break;

			default:
				echo '<li>';
				echo '<a href="' . esc_attr( $items['url'] ) . '" ' . wpbc_get_custom_attr( $items ) . '>';     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wp_kses_post( $items['title'] );
				echo '</a>';
				echo '</li>';
				break;
		}
	}

	?></ul></div><?php
}

/**
 * Usage Example:
 *
		$el_arr = array(
			'title'     => __( 'Booking Calendar', 'booking' ),
			'font_icon' => 'wpbc-bi-calendar2-range',
			'position'  => 'left',
			'items'     => array(
				array(
					'type'  => 'link',
					'title' => __( 'FAQ', 'booking' ),
					'url'   => 'https://wpbookingcalendar.com/faq/',
				),
				array( 'type' => 'divider' ),
				array(
					'type'  => 'link',
					'title' => __( 'Support Forum', 'booking' ),
					'url'   => 'https://wpbookingcalendar.com/support/',
				),
				array(
					'type' => 'html',
					'html' => '<span class="wpbc_flex_settings_containers" style="flex-flow:row nowrap;display:flex;">
								<div id="wpbc_general_settings_calendar_tab" class="wpbc_settings_navigation_item wpbc_settings_navigation_item_active" style="min-width:350px;">
									<a class="" original-title="" onclick="javascript:wpbc_navigation_click_show_section(this,\'#wpbc_general_settings_calendar_metabox\' );" href="javascript:void(0);">
										<i class="menu_icon icon-1x wpbc-bi-calendar3-week"></i>
										<div class="wpbc_text_inside">
											<div class="wpbc_flex_settings_title"><div>Calendar Look</div><div>01</div></div>
											<div class="wpbc_flex_settings_description">Set Calendar Skin, Max Months to Scroll, Calendar Legend</div>
										</div>
									</a>
								</div>
								<div id="wpbc_general_settings_calendar_tab" class="wpbc_settings_navigation_item wpbc_settings_navigation_item_active" style="min-width:350px;">
									<a class="" original-title="" onclick="javascript:wpbc_navigation_click_show_section(this,\'#wpbc_general_settings_calendar_metabox\' );" href="javascript:void(0);">
										<i class="menu_icon icon-1x wpbc-bi-calendar3-week"></i>
										<div class="wpbc_text_inside">
											<div class="wpbc_flex_settings_title"><div>Days Selection</div><div>02</div></div>
											<div class="wpbc_flex_settings_description">Single Day, Multi Days or Range Days Selection (Min/Max days )</div>
										</div>
									</a>
								</div>
							</span>',
				),
				array(
					'type'  => 'header',
					'title' => __( 'Contact Support', 'booking' ),
				),
				array(
					'type'  => 'link',
					'title' => __( 'Contact Support', 'booking' ),
					'url'   => 'mailto:support@wpbookingcalendar.com',
					'attr'  => array( 'style' => 'font-weight: 600;' ),
				),
				array(
					'type'  => 'link',
					'title' => "What's New",
					'url'   => esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) ),
				),
				array(
					'type'  => 'link',
					'title' => __( 'About', 'booking' ),
					'url'   => 'https://wpbookingcalendar.com/',
				),
			),
		);
		wpbc_ui_el__dropdown_menu( $el_arr );
 */