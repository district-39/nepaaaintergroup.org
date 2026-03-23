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
 * Show Link (usually  button)
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui_el__a( $args ) {

	$defaults = array(
		'title'           => '',
		'hint'            => '',
		// Example: array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' ).
		'font_icon'       => '',
		'position'        => '',
		'style'           => '',
		'container_class' => '',
		'container_style' => '',
		'attr'            => array(),
		'onclick'         => 'javascript:void(0)',
		'permalink'       => 'javascript:void(0)',
		'onmouseover'     => 'javascript:void(0)',
		'onmouseout'      => 'javascript:void(0)',
	);
	$params   = wp_parse_args( $args, $defaults );


	?><div class="wpbc_ui_el_container wpbc_ui_el__a <?php echo esc_attr( $params['container_class'] ); ?>" style="<?php echo esc_attr( $params['container_style'] ); ?>"><a
		onmouseover="<?php echo $params['onmouseover']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
		onmouseout="<?php echo $params['onmouseout']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
		onclick="<?php echo $params['onclick']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
		href="<?php echo $params['permalink'];  /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
		class="<?php echo esc_attr( ( ! empty( $params['hint'] ) ) ? 'tooltip_' . $params['hint']['position'] . ' ' : '' ); ?>"
		style="<?php echo esc_attr( $params['style'] ); ?>"
		<?php
		if ( ! empty( $params['hint'] ) ) {
			echo ' title="' . esc_attr( $params['hint']['title'] ) . '" ';
		}
		echo wpbc_get_custom_attr( $params );   /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
		?>
	>
	<?php
	if ( ! empty( $params['font_icon'] ) ) {
		// Font Icon.
		?><i class="menu_icon icon-1x <?php echo wp_kses_post( $params['font_icon'] ); ?>"></i><?php
	}
	if ( ! empty( $params['title'] ) ){
		?><span class="nav-tab-text"><?php echo wp_kses_post( $params['title'] ); ?></span><?php
	}
	?></a></div><?php
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