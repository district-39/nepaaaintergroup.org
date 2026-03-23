<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-09
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Main - Booking Types ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Booking Types - Step 03
 *
 * 	Help Tips:
 *
 *		<script type="text/html" id="tmpl-template_name_a">
 * 			Escaped:  	 {{data.test_key}}
 * 			HTML:  		{{{data.test_key}}}
 * 			JS: 	  	<# if (true) { alert( 1 ); } #>
 * 		</script>
 *
 * 		var template__var = wp.template( 'template_name_a' );
 *
 * 		jQuery( '.content' ).html( template__var( { 'test_key' => '<strong>Data</strong>' } ) );
 *
 * @return void
 */

function wpbc_stp_wiz__template__bookings_types(){

 ?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__bookings_types">
	<div class="wpbc_page_main_section    wpbc_container    wpbc_form    wpbc_container_booking_form">
		<div class="wpbc__form__div wpbc_swp_section wpbc_swp_section__bookings_types">
			<div class="wpbc__row">
				<div class="wpbc__field">
					<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Select Your Booking Type', 'booking' ); ?></h1>
					<p class="wpbc_swp_section_header_description"><?php esc_html_e('This will help customize your experience.','booking'); ?></p>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">

					<div class="wpbc_ui_radio_section wpbc_ui_radio_section_as_row">
						<?php
							$params_radio = array(
								  'id'       => 'wpbc_swp_booking_types__full_days_bookings' 				// HTML ID  of element
								, 'name'     => 'wpbc_swp_booking_types'
								, 'value'    => 						'full_days_bookings' 				// Some Value from options array that selected by default
								, 'label'    => array( 'title' => __('Full-Day Bookings','booking') )
								, 'text_description'  => __('Manage bookings for entire days, whether it\'s for a single day or multiple days. No time selection is required.','booking')
								, 'label_after_right' => ''
								, 'footer_text' 	  => ''
								, 'style'    => '' 																		// CSS of select element
								, 'class'    => '' 																		// CSS Class of select element
								, 'disabled' => false
								, 'attr'     => array() 																// Any  additional attributes, if this radio | checkbox element
								, 'legend'   => ''																		// aria-label parameter
								, 'selected' => true 																	// Selected or not
								, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
								, 'onchange' => "jQuery('.wpbc_in_radio_container_selectbox').hide(); "//"console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"							// JavaScript code
							);
							wpbc_flex_radio_container( $params_radio );

							$params_radio = array(
								  'id'       => 'wpbc_swp_booking_types__time_slots_appointments' 				// HTML ID  of element
								, 'name'     => 'wpbc_swp_booking_types'
								, 'value'    => 						'time_slots_appointments' 				// Some Value from options array that selected by default
								, 'label'    => array( 'title' => __('Time-Based Appointments','booking') )
								, 'text_description'  => __('Manage bookings for specific times on selected dates. Allow clients to choose from available time slots in the booking form.','booking')
								, 'label_after_right' => ''
								, 'footer_text' 	  => ''
								, 'style'    => '' 																		// CSS of select element
								, 'class'    => '' 																		// CSS Class of select element
								, 'disabled' => false
								, 'attr'     => array() 																// Any  additional attributes, if this radio | checkbox element
								, 'legend'   => ''																		// aria-label parameter
								, 'selected' => false 																	// Selected or not
								, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
								, 'onchange' => "jQuery('.wpbc_in_radio_container_selectbox').hide(); if ( 'time_slots_appointments' === jQuery( this ).val() ) {jQuery('.wpbc_ui_booking_timeslot_picker__get_on_off__div').show();}"//"console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"							// JavaScript code
								, 'bottom_html' => '<div class="wpbc_in_radio_container_selectbox wpbc_ui_booking_timeslot_picker__get_on_off__div" style="display: none">'
								                   . wpbc_stp_wiz__ui__bookings_types__appointments_type_selectbox__get()
								                   . wpbc_stp_wiz__ui__bookings_types__timeslot_picker_selectbox__get()
											.'</div>'
							);
							wpbc_flex_radio_container( $params_radio );

							$params_radio = array(
								  'id'       => 'wpbc_swp_booking_types__changeover_multi_dates_bookings' 				// HTML ID  of element
								, 'name'     => 'wpbc_swp_booking_types'
								, 'value'    => 						'changeover_multi_dates_bookings' 				// Some Value from options array that selected by default
								, 'label'    => array( 'title' => __('Changeover Multi-Date Bookings','booking') )
								, 'text_description'  => __('Manage multi-day bookings with specific check-in and check-out days, clearly marked with diagonal or vertical lines. Ideal for bookings that require split days.','booking')
								, 'label_after_right' => '<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank"><strong class="wpbc_ui_radio_text_right"><span class="wpbc_pro_label">Pro</span></strong></a>'
								/* translators: 1: ... */
								, 'footer_text' 	  => sprintf( __( 'Find more information about this feature on %1$sthis page%2$s.', 'booking' ), '<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank">','</a>')
								, 'style'    => '' 																		// CSS of select element
								, 'class'    => '' 																		// CSS Class of select element
								, 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) )
								, 'attr'     => array() 																// Any  additional attributes, if this radio | checkbox element
								, 'legend'   => ''																		// aria-label parameter
								, 'selected' => false 																	// Selected or not
								, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
								, 'onchange' => "jQuery('.wpbc_in_radio_container_selectbox').hide(); if ( 'changeover_multi_dates_bookings' === jQuery( this ).val() ) {jQuery('.wpbc_ui_booking_change_over__get_on_off__div').show();}"//"console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"							// JavaScript code
								, 'bottom_html' => '<div class="wpbc_in_radio_container_selectbox wpbc_ui_booking_change_over__get_on_off__div" style="display: none">'
								                   . wpbc_stp_wiz__ui__bookings_types__change_over_selectbox__get()
											.'</div>'
							);
							wpbc_flex_radio_container( $params_radio );
						?>
					</div>
					<?php

					// Help message
					?><div class="wpbc-settings-notice notice-warning notice-helpful-info" style="height: auto;font-size: 12px;margin: 20px 20px 5px 2px;">
						<?php
						 	echo '<strong>' . esc_html__('Note!' ,'booking') . '</strong> ' .
								 esc_html__( 'This step will update the booking form configuration.', 'booking' ) . ' ';
							echo esc_html__( 'You can always change this later', 'booking' ) . '. ';
							/* translators: 1: ... */
							echo wp_kses_post( sprintf( __( 'Configure it at the %s page.', 'booking' ),
							'<a href="'. esc_attr( wpbc_get_settings_url() . '&tab=form' ).'">Settings > Booking Form</a>',
							'<a href="https://wpbookingcalendar.com/faq/no-emails/">', '</a>' ) );
							echo ' ';
						?>
					</div><?php

					wpbc_maybe_show_warning_conflict__wordfence('margin: 20px 20px 5px 2px;');

				?>
				</div>
			</div>
		</div>
	</div>
	<?php
	// -----------------------------------------------------------------------------------------------------------------
	// Buttons
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_footer		wpbc_ajx_page__section_footer wpbc_ajx_page__section_footer__internal">
		<div class="wpbc__container_place__footer_buttons 		wpbc_container    wpbc_form    wpbc_container_booking_form">

			<div class="wpbc__form__div">
				<hr>
				<div class="wpbc__row">
					<?php /** ?>
					<div class="wpbc__field">
						<input type="button" value="<?php esc_attr_e('Reset Wizard and Start from Beginning','booking'); ?>"
							   class="wpbc_button_light wpbc_button_danger tooltip_top "  style=""
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params(  { 'do_action': 'make_reset' }  ); ">
					</div>
					<?php /**/ ?>
					<div class="wpbc__field">
						<#  if ( '' != data['steps'][ data['current_step'] ]['prior'] ) { #>
						<a     class="wpbc_button_light"  style="margin-left:auto;margin-right:10px;" tabindex="0"
							   id="btn__toolbar__buttons_prior"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																									'current_step': '{{data.steps[ data.current_step ].prior}}',
																									'do_action': 'none',
																									'ui_clicked_element_id': 'btn__toolbar__buttons_prior'
																								} );
										wpbc_button_enable_loading_icon( this );
										wpbc_admin_show_message_processing( '' );" ><i class="menu_icon icon-1x wpbc_icn_arrow_back_ios"></i><span>&nbsp;&nbsp;&nbsp;<?php esc_html_e('Go Back','booking'); ?></span></a>

						<# } else { #>
							<span style="margin-left:auto;"></span>
						<# } #>
						<a	   class="wpbc_button_light button-primary" tabindex="0"
							   id="btn__toolbar__buttons_next"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																									'current_step': '{{data.steps[ data.current_step ].next}}',
																									   'do_action': '{{data.steps[ data.current_step ].do_action}}',
																									'ui_clicked_element_id': 'btn__toolbar__buttons_next',
																									   'step_data':{
																												'wpbc_swp_booking_types': jQuery( '[name=\'wpbc_swp_booking_types\']:checked').val(),
																												'wpbc_swp_booking_timeslot_picker': jQuery( '[name=\'wpbc_swp_booking_timeslot_picker\']').val(),
																												'wpbc_swp_booking_appointments_type': jQuery( '[name=\'wpbc_swp_booking_appointments_type\']:checked').val(),
																												'wpbc_swp_booking_change_over_days_triangles': jQuery( '[name=\'wpbc_swp_booking_change_over_days_triangles\']').val()
																											}

																								} );

										wpbc_button_enable_loading_icon( this );
										wpbc_admin_show_message_processing( '' );" ><span><?php esc_html_e('Save and Continue','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
					</div>
				</div>
				<div class="wpbc__row">
					<div class="wpbc__field">
						<p class="wpbc_exit_link_small">
							<a href="javascript:void(0)" tabindex="-1"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
							   title="<?php esc_attr_e('Exit and skip the setup wizard','booking'); ?>"
							><?php
								esc_html_e('Exit and skip the setup wizard','booking');
							?></a>
							<?php  ?>
							<a href="javascript:void(0)" class="wpbc_button_danger" style="margin: 25px 0 0;  font-size: 12px;" tabindex="-1"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'make_reset' } ); "
							   title="<?php esc_attr_e('Start Setup from Beginning','booking'); ?>"
							><?php
								esc_html_e('Reset Wizard','booking');
							?></a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	// -----------------------------------------------------------------------------------------------------------------
	// End Buttons
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<style type="text/css">
		.wpbc_setup_wizard_page_container .wpbc_swp_section__bookings_types {max-width: 440px}
		.wpbc_ajx_page__container .wpbc_ajx_page__section_footer:not(.wpbc_ajx_page__section_footer__internal){ display: none;}
	</style>
	</script><?php
}




/**
 * Get toggle  On | Off  Selector  for 'Time picker for time slots'
 */
function wpbc_stp_wiz__ui__bookings_types__timeslot_picker_selectbox__get() {

	$booking_timeslot_picker = get_bk_option('booking_timeslot_picker');

	ob_start();
	?><div class="booking_timeslot_picker__get_on_off" style="max-width: 90%;margin: 0 auto;padding-bottom: 16px;">
		<label style="margin: 0 0 8px;"><?php esc_html_e('Show time slots as','booking'); ?></label>
		<select name="wpbc_swp_booking_timeslot_picker" >
			<option <?php selected($booking_timeslot_picker,'On'); ?> value="On"><?php esc_html_e('Time Picker','booking'); ?></option>
			<option <?php selected($booking_timeslot_picker,'Off'); ?> value="Off"><?php esc_html_e('Drop-down list','booking'); ?></option>
		</select>
	</div><?php
	$toggle_box_html = ob_get_clean();

	return $toggle_box_html;
}


/**
 * Get toggle  On | Off  Selector  for 'Appointments_type'
 */
function wpbc_stp_wiz__ui__bookings_types__appointments_type_selectbox__get() {

	ob_start();
	?>
	<div class="booking_appointments_type__get_on_off" style="max-width: 90%;margin: 0 auto;padding-bottom: 16px;">
		<label style="margin: 0 0 8px;"><?php esc_html_e( 'Choose how to display appointment availability', 'booking' ); ?></label>
		<?php /* ?>
		<select name="wpbc_swp_booking_appointments_type">
			<option selected="SELECTED" value="rangetime"><?php esc_html_e( 'Based on Time Slots', 'booking' ); ?></option>
			<option value="durationtime"><?php esc_html_e( 'Based on Service Duration', 'booking' ); ?></option>
		</select>
		<?php */ ?>
		<label class="wpbc-form-radio" title="<?php esc_attr_e( 'Based on fixed Time Slots', 'booking' ); ?>">
			<input type="radio" name="wpbc_swp_booking_appointments_type" value="rangetime" class="wpbc-form-radio-internal" style="" id="wpbc_swp_booking_appointments_type_rangetime" autocomplete="off">&nbsp;
			<?php esc_html_e( 'Based on fixed Time Slots', 'booking' ); ?>
		</label>
		<label class="wpbc-form-radio" title="<?php esc_attr_e( 'Based on service Duration and Start', 'booking' ); ?>">
			<input type="radio" name="wpbc_swp_booking_appointments_type" value="durationtime" checked="checked" class="wpbc-form-radio-internal" style="" id="wpbc_swp_booking_appointments_type_durationtime" autocomplete="off">&nbsp;
			<?php esc_html_e( 'Based on service Duration and Start Time', 'booking' ); ?>
		</label>
		<!--span class="description" style="display: none;"><strong>Note.</strong> This feature is applying only for bookings for specific timeslots, or if activated check in/out time option.</span-->
	</div>
	<?php

	$toggle_box_html = ob_get_clean();

	return $toggle_box_html;
}



/**
 * Get toggle  On | Off  Selector  for 'Time picker for time slots'
 */
function wpbc_stp_wiz__ui__bookings_types__change_over_selectbox__get() {

	$booking_change_over_days_triangles = get_bk_option('booking_change_over_days_triangles');

	ob_start();
	?><div class="wpbc_ui_booking_change_over__get_on_off__div" style="max-width: 90%;margin: 0 auto;padding-bottom: 16px;">
		<label style="margin: 0 0 8px;"><?php esc_html_e('Show change over days as','booking'); ?></label>
		<select name="wpbc_swp_booking_change_over_days_triangles" >
			<option <?php selected($booking_change_over_days_triangles,'On'); ?> value="On"><?php esc_html_e('Triangles','booking'); ?></option>
			<option <?php selected($booking_change_over_days_triangles,'Off'); ?> value="Off"><?php esc_html_e('Vertical Lines','booking'); ?></option>
		</select>
	</div><?php
	$toggle_box_html = ob_get_clean();

	return $toggle_box_html;
}
