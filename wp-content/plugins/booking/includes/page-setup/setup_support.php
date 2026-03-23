<?php /**
 * @version 1.0
 * @description Support functions for the Setup Wizard Page
 * @category    Setup Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-06
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * Do we need to start 'Setup Wizard' ?
 *
 * @return bool
 */
function wpbc_setup_wizard_page__is_need_start(){

	if ( wpbc_setup_wizard_page__is_all_steps_completed() ) {
		return false;        // Wizard Completed!
	}

	// Some steps were not completed or wizard was not started at all

	$days_num_ago = wpbc_how_old_in_days();

	// No bookings, so maybe need to start
	if ( - 1 === $days_num_ago ) {
		return true;
	}

	// If older than 3 days ago,  then no need to  start !
	if (  $days_num_ago < 3 ){
		return true;
	}

	// No need to  start,  because this installation  already OLD
	return false;
}

/**
 * Do we need to Continue 'Setup Wizard' or all steps already  completed ?
 *
 * @return bool
 */
function wpbc_setup_wizard_page__is_all_steps_completed() {

	$setup_steps = new WPBC_SETUP_WIZARD_STEPS();

	$is_all_steps_completed =  $setup_steps->db__is_all_steps_completed();

	return $is_all_steps_completed;
}

/**
 * Is user  can  access Wizard Setup  page ?
 * @return bool
 */
function wpbc_is_user_can_access_wizard_page() {
	$is_user_can_access_wizard_page = true;
	if ( class_exists( 'wpdev_bk_multiuser' ) ) {

		$real_current_user_id = get_current_user_id();                                                                  // Is current user suer booking admin  and if this user was simulated log in
		$is_user_super_admin  = apply_bk_filter( 'is_user_super_admin', $real_current_user_id );
		if ( ! $is_user_super_admin ) {
			$is_user_can_access_wizard_page = false;
		}
	}

	return $is_user_can_access_wizard_page;
}


// =====================================================================================================================
// ==  Shortcodes Content  ==
// =====================================================================================================================

/**
 *  Get "Booking Form" Shortcode Content
 *
 * @param $resource_id
 *
 * @return false|string
 */
function wpbc_setup_wizard_page__get_shortcode_html( $resource_id = 1 , $is_show_only_calendar = false ) {

	ob_start();

	$localized_js_vars_script = wpbc_get_localized_js_vars();

	if ( $is_show_only_calendar ) {
		// Center calendar.
		?><style type="text/css">
			.wpbc_calendar_wraper {
			  display: flex;
			  flex-flow: column nowrap;
			  align-items: center;
			  justify-content: flex-start;
			}
			.wpbc_calendar_wraper div {
				margin-bottom: 10px;
			}
		</style>
		<div class="wpbc_shortcode_container"><?php
			echo do_shortcode( '[bookingcalendar resource_id=' . $resource_id . ']' );
		?></div><?php

		// If we use the [bookingcalendar] shortcode,  then  we remove this tag,  for ability to  select  dates in calendar.
		?><script tye="text/javascript">
			jQuery(document).ready(function(){
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $localized_js_vars_script;
				?>
				jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function( event, resource_id ) {
					jQuery( '#calendar_booking_unselectable' + resource_id ).remove();
				} );
			});
		</script><?php

	} else {

		// Help message
		?><div class="wpbc-settings-notice notice-warning notice-helpful-info" style="padding: 8px 20px;font-size: 14px;margin: 0 auto;max-width: Min(54em,100%);">
			<strong><?php esc_html_e('Note','booking'); ?>: </strong>
			<?php
				esc_html_e( 'This is a preview of your booking form.', 'booking' ); echo ' ';
				esc_html_e( 'You can adjust settings in the widgets on the right side of the page.', 'booking' );
				//echo '<a href="'. esc_attr( wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_availability_tab' ).'">Settings > Availability</a>';
			?>
		</div><?php

		// If we use the [bookingcalendar] shortcode,  then  we remove this tag,  for ability to  select  dates in calendar.
		?><script type="text/javascript">
			jQuery( document ).ready( function () {
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $localized_js_vars_script;
				?>
				_wpbc.set_other_param( 'calendars__on_this_page', [] );
				<?php if ( 'On' === get_bk_option( 'booking_timeslot_picker') ) { ?>
				wpbc_hook__init_timeselector();
				<?php } ?>
				wpbc_hook__init_booking_form_wizard_buttons();
			  	// wpbc__calendar__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL . get_bk_option( 'booking_skin' ) ); ?>' );
			  	// wpbc__css__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL . get_bk_option( 'booking_timeslot_picker_skin' ) ); ?>' );
				<?php if ('wpbc_theme_dark_1' === get_bk_option( 'booking_form_theme' ) ){  ?>
				jQuery( '.wpbc_widget_preview_booking_form .wpbc_center_preview,.wpbc_widget_preview_booking_form .wpbc_container.wpbc_container_booking_form,.wpbc_widget_preview_booking_form .wpbc_widget_content' ).addClass( 'wpbc_theme_dark_1' );
				<?php } ?>
				if ( 'function' === typeof( wpbc_update_capacity_hint ) ) {
					jQuery( '.booking_form_div' ).on( 'wpbc_booking_date_or_option_selected', function ( event, resource_id ) {
						wpbc_update_capacity_hint( resource_id );
					} );
				}
				//jQuery( 'body' ).on( 'wpbc_datepick_inline_calendar_loaded', function ( event, resource_id, jCalContainer, inst ) { });
				// jQuery( 'body' ).off( 'wpbc_calendar_ajx__loaded_data' );
				// jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function ( event, loaded_resource_id ){
				// 	// wpbc_blink_element( '.wpbc_widget_change_form_structure', 3, 220 );
				//  	wpbc_blink_element( '#ui_btn_cstm__set_booking_form_template_pro', 2, 220 );
				//  	wpbc_blink_element( '#ui_btn_cstm__set_booking_form_template_apply', 4, 220 );
				// });

				// jQuery( 'body' ).on( 'wpbc_calendar_ajx__before_loaded_data', function( event, resource_id ) {
				// 	wpbc_calendar__loading__stop( resource_id );
				// } );
			});
			//if ( 'undefined' !== typeof _wpbc ) {
			//	<?php
    		//	echo "_wpbc.set_other_param( 'availability__week_days_unavailable', ["
            //                                                        . ( ( get_bk_option( 'booking_unavailable_day0') == 'On' ) ? '0,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day1') == 'On' ) ? '1,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day2') == 'On' ) ? '2,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day3') == 'On' ) ? '3,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day4') == 'On' ) ? '4,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day5') == 'On' ) ? '5,' : '' )
			//	                                                    . ( ( get_bk_option( 'booking_unavailable_day6') == 'On' ) ? '6,' : '' )
			//	                                                    . "999] ); ";
			//	 ?>
			//}
		</script><?php

		?><div class="wpbc_shortcode_container"><?php
			echo do_shortcode( '[booking resource_id=' . $resource_id . ']' );
		?></div><?php


	}

	return  ob_get_clean();
}



//TODO: Left Menu - TEMP . Delete it ?
function wpbc_setup_wizard_page__get_left_navigation_menu_arr(){

   	$navigation_menu_arr = array();

	$navigation_menu_arr['general_info'] = array(
												'title'  => __( 'General Info', 'booking' ),
												'icon'   => 'wpbc_icn_check wpbc_icn_dashboard0  wpbc_icn_task_alt0  0wpbc_icn_circle_outline',
												'class'  => '',
												//'style'  => 'cursor:not-allowed;',
												//'a_style'  => 'pointer-events: none;',
												'action' => "wpbc_ajx__setup_wizard_page__send_request_with_params( { 'current_step':'general_info' } );"
											   ,'right_icon'   => array(
														'icon' 	 => '',
														'text' 	 => __('Done', 'booking'),
														'action' => "console.log( this );",
													)

											);
	$navigation_menu_arr['optional_other_settings'] = array(
												'title'  => __( 'Bookings Type', 'booking' ),
												'icon'   => 'wpbc-bi-calendar2-range 0wpbc_icn_radio_button_checked',
												'class'  => '',
												//'a_style'  => 'color: var(--wpbc_settings__nav_menu_left__active_border_color);',
												'action' => "wpbc_ajx__setup_wizard_page__send_request_with_params( { 'current_step':'optional_other_settings' } );"
											);
	$navigation_menu_arr['next_step'] = array(
												'title'  => '',//__( 'Bookings Type', 'booking' ),
												'icon'   => 'wpbc-bi-three-dots 0wpbc_icn_radio_button_unchecked',
												'class'  => '',
												'a_style'  => 'pointer-events: none;cursor:not-allowed;',
												'action' => "wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );"
											);
//			$navigation_menu_arr['booking_notification'] = array(
//														'title'  => __( 'Publishing', 'booking' ),
//														'icon'   => 'wpbc-bi-calendar2-range',
//														'class'  => 'wpbc_top_border',
//														'action' => "wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );",
//																		'right_icon'   => array(
//																'icon' 	 => 'wpbc_icn_navigate_next expand_more',
//																'action' => "console.log( this );",
//															)
//													);
//			$navigation_menu_arr['booking_form'] = array(
//														'title'  => __( 'Booking Form', 'booking' ),
//														'icon'   => 'wpbc_icn_dashboard',
//														'class'  => 'wpbc_sub_option',
//														'action' => "wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );"
//													);

	return $navigation_menu_arr;
}


/**
 * Show Warning Conflict with "Wordfence" plugin
 *
 * @return void
 */
function wpbc_maybe_show_warning_conflict__wordfence( $style = '' ) {
	if ( class_exists( 'wordfence' ) ) {

		$is_panel_visible = wpbc_is_dismissed_panel_visible( 'wpbc_show_warning_wordfence' );        // FixIn: 9.9.0.8.
		if ( $is_panel_visible ) {
			?><div id="wpbc_show_warning_wordfence" class="wpbc-settings-notice notice-error notice-helpful-info0"
				   style="max-width: Min(450px, 100%);margin: auto;padding: 4px 15px 7px 20px;font-size: 14px;line-height: 28px;margin-bottom: 25px; display: flex;flex-flow:row nowrap;justify-content: space-between;align-items: flex-start;border-left-color: #e2892b;<?php echo esc_attr( $style ); ?>"
			   >
				<div>
				   <span style="margin: 0 5px 0 0;color: #e2892b;" ><i class="menu_icon icon-1x wpbc_icn_warning_amber"></i></span>
					<?php
					echo '<strong>' . esc_html__('Important!' ,'booking') . '</strong> ' ;
					// FixIn: 10.9.1.1.
					/* translators: 1: ... */
					echo wp_kses_post( sprintf( __( 'We detect that you use %s plugin.', 'booking' ), '<strong>Wordfence</strong>' ) );

					echo '<br>';

					/* translators: 1: ... */
					echo wp_kses_post( sprintf( __( 'If you encounter any issues, follow this %1$stroubleshooting instruction%2$s.', 'booking' ),
							'<a href="https://wpbookingcalendar.com/faq/setup-wizard-on-step-4-keeps-going-to-blank-page/" target="_blank" style="font-weight:600;text-underline-offset: 3px;text-decoration-thickness: 0px;text-decoration-style: dashed;">', '</a>' ) );
				?>
				</div>
				<div><?php
				ob_start();
				$is_panel_visible = wpbc_is_dismissed( 'wpbc_show_warning_wordfence', array(
												'title' => '<i class="menu_icon icon-1x wpbc_icn_close"></i> ',
												'hint'  => __( 'Dismiss', 'booking' ),
												'class' => 'wpbc_panel_get_started_dismiss',
												'css'   => 'background: #fff;border-radius: 7px;'
											));

				$dismiss_x_button = ob_get_clean();

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_replace__js_scripts__to__tpl_scripts( $dismiss_x_button );

			?></div>
			</div><?php
		}



	}
}


/**
 * Escape scripts from the Booking Calendar shortcode content for showing in Wizard templates.
 *
 * @param string $calendar_html_content  - usualy it is the content of [booking ...] shortcode.
 *
 * @return mixed
 */
function wpbc_clean_calendar_loading_scripts( $calendar_html_content ) {

	// Prevent of any intention usage od this tag.
	$calendar_html_content = str_replace( 'ajax_scrpt', '', $calendar_html_content );

	$pattern               = '/<script\s*(type=[\'"]+text\/javascript[\'"]+)?\s*>/i';
	$calendar_html_content = preg_replace( $pattern, '<ajax_scrpt>', $calendar_html_content );

	$pattern               = '/<\/script>/i';
	$calendar_html_content = preg_replace( $pattern, '</ajax_scrpt>', $calendar_html_content );

	return $calendar_html_content;
}
