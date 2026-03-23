<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-12
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Date | Time Formats,  Etc... ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Date | Time Formats ... - Step 02
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
function wpbc_stp_wiz__template__date_time_formats(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__date_time_formats">
	<div class="wpbc_page_main_section    wpbc_container    wpbc_form    wpbc_container_booking_form">
		<div class="wpbc__form__div wpbc_swp_section wpbc_swp_section__date_time_formats">
			<div class="wpbc__row">
				<div class="wpbc__field">
					<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Dates and times preferences', 'booking' ); ?></h1>
					<p class="wpbc_swp_section_header_description"><?php esc_html_e('Customize how dates and times are displayed.','booking'); ?></p>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<label><?php esc_html_e('Date Format','booking'); ?></label><br>
					<select name="wpbc_swp_date_format" >
						<?php
						$date_format = get_bk_option( 'booking_date_format' );
						$date_format_arr = array(
										'j M Y', 'F j, Y',
										'd/m/Y', 'd.m.Y', 'd-m-Y',
										'm/d/Y', 'm.d.Y', 'm-d-Y',
										'Y/m/d', 'Y.m.d', 'Y-m-d'
						);
						foreach ( $date_format_arr as $format ) {
							?><option <?php selected( $date_format, esc_attr( $format ) ); ?> value="<?php echo esc_attr( $format ); ?>"><?php echo esc_html( date_i18n( $format ) ); ?></option><?php
						}
					?>
					</select>
				</div>
				<div class="wpbc__field">
					<label><?php esc_html_e('Time Format','booking'); ?></label><br>
					<select name="wpbc_swp_time_format" >
						<?php
						$time_format = get_bk_option( 'booking_time_format' );
						$time_format_arr = array( 'g:i a', 'g:i A', 'H:i' );
						foreach ( $time_format_arr as $format ) {
							?><option <?php selected( $time_format, esc_attr( $format ) ); ?> value="<?php echo esc_attr( $format ); ?>"><?php echo esc_html( date_i18n( $format ) ); ?></option><?php
						}
					?>
					</select>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<label><?php esc_html_e('Start day of the week','booking'); ?></label><br>
					<?php
					$booking_start_day_weeek = get_bk_option( 'booking_start_day_weeek' );
					$booking_start_day_weeek_arr = array(
												  '0' => __('Sunday' ,'booking')
												, '1' => __('Monday' ,'booking')
												, '2' => __('Tuesday' ,'booking')
												, '3' => __('Wednesday' ,'booking')
												, '4' => __('Thursday' ,'booking')
												, '5' => __('Friday' ,'booking')
												, '6' => __('Saturday' ,'booking')
											)
					?>
					<select name="wpbc_swp_start_day_weeek">
						<?php
						foreach ( $booking_start_day_weeek_arr as $week_day_val => $week_day_title ) {
							?><option <?php selected( $booking_start_day_weeek, esc_attr( $week_day_val ) ); ?> value="<?php echo esc_attr( $week_day_val ); ?>"><?php echo esc_html( $week_day_title ); ?></option><?php
						}
						?>
					</select>
					<span style="font-size:12px;"><?php esc_html_e('Select which day the week starts on.','booking'); ?></span>
				</div>
				<div class="wpbc__field">
					<label><?php esc_html_e('Default view for dates in booking tables','booking'); ?></label><br>
					<?php
					$booking_start_day_weeek = get_bk_option( 'booking_date_view_type' );
					$booking_start_day_weeek_arr = array(
							                                 'short' => __('Short days view' ,'booking')
							                               , 'wide'  => __('Wide days view' ,'booking')
							                            );
					?>
					<select name="wpbc_swp_date_view_type"  onchange="javascript: jQuery( '.view_dates' ).hide(); jQuery( '.view_dates_' + jQuery( this ).val() ).show();">
						<?php
						foreach ( $booking_start_day_weeek_arr as $week_day_val => $week_day_title ) {
							?><option <?php selected( $booking_start_day_weeek, esc_attr( $week_day_val ) ); ?> value="<?php echo esc_attr( $week_day_val ); ?>"><?php echo esc_html( $week_day_title ); ?></option><?php
						}
						?>
					</select>
					<?php
					update_bk_option( 'booking_date_format', 'j M Y' );
					$dates_comma = wpbc_get_comma_seprated_dates_from_to_day( date_i18n( "d.m.Y", strtotime( 'now' ) ), date_i18n( "d.m.Y", strtotime( '+3 days' ) ) );
					?>
					<span style="font-size:12px;"><?php esc_html_e('Example','booking'); ?>: &nbsp; </span>
					<span style="font-size:12px;width:25em;<?php echo ( 'wide'  == $booking_start_day_weeek ) ? 'display:none' : ''; ?>" class="view_dates view_dates_short"><?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wpbc_get_dates_short_format( $dates_comma ); ?></span>
					<span style="font-size:12px;width:25em;<?php echo ( 'short' == $booking_start_day_weeek ) ? 'display:none' : ''; ?>" class="view_dates view_dates_wide"><?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wpbc_get_dates_comma_string_localized( $dates_comma ); ?></span>
					<?php update_bk_option( 'booking_date_format', $date_format ); ?>
				</div>
			</div>

			<div class="wpbc__spacer" style="width:100%;clear:both;height:40px;margin-bottom:5px;border-bottom:1px solid #ccc;"></div>
			<div class="wpbc__row wpbc_setup_wizard_page__section_footer">
				<div class="wpbc__field">
					<span style="font-size:11px;"><?php esc_html_e( 'You can always change this later', 'booking' ); ?>
						- <a href="<?php echo esc_url( wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_datestimes_tab' ); ?>"><?php
								echo esc_html__( 'Settings', 'booking' ) . ' > ' . esc_html__( 'Date / Time Formats', 'booking' );
				  ?></a></span>
				</div>
				<div class="wpbc__field wpbc_container wpbc_container_booking_form">
					<a     class="wpbc_button_light"  style="margin-left:auto;margin-right:10px;" tabindex="0"
						   id="btn__toolbar__buttons_prior"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																								'current_step': '{{data.steps[ data.current_step ].prior}}',
																								'do_action': 'none',
																								'ui_clicked_element_id': 'btn__toolbar__buttons_prior'
																							} );
									wpbc_button_enable_loading_icon( this );
									wpbc_admin_show_message_processing( '' );" ><i class="menu_icon icon-1x wpbc_icn_arrow_back_ios"></i><span>&nbsp;&nbsp;&nbsp;<?php esc_html_e('Go Back','booking'); ?></span></a>
					<a	  style=""
						   class="wpbc_button_light button-primary"
						   id="btn__toolbar__buttons_next"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																								'current_step': '{{data.steps[ data.current_step ].next}}',
																								   'do_action': '{{data.steps[ data.current_step ].do_action}}',
																								'ui_clicked_element_id': 'btn__toolbar__buttons_next',
																								'step_data':{
																												'wpbc_swp_date_format':     jQuery( '[name=\'wpbc_swp_date_format\']').val(),
																												'wpbc_swp_time_format': 	jQuery( '[name=\'wpbc_swp_time_format\']').val(),
																												'wpbc_swp_start_day_weeek': jQuery( '[name=\'wpbc_swp_start_day_weeek\']').val(),
																												'wpbc_swp_date_view_type':  jQuery( '[name=\'wpbc_swp_date_view_type\']').val()
																											}
																							} );
									wpbc_button_enable_loading_icon( this );
									wpbc_admin_show_message_processing( '' );" ><span><?php esc_html_e('Save and Continue','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
				</div>
			</div>
			<?php /**/ ?>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<p class="wpbc_exit_link_small">
						<a href="javascript:void(0)"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
						   title="<?php esc_attr_e('Exit and skip the setup wizard','booking'); ?>"
						><?php
							esc_html_e('Exit and skip the setup wizard','booking');
						?></a>
						<?php  ?>
						<a href="javascript:void(0)" class="wpbc_button_danger" style="margin: 25px 0 0;  font-size: 12px;"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'make_reset' } ); "
						   title="<?php esc_attr_e('Reset the Setup Wizard and start from beginning','booking'); ?>"
						><?php
							esc_html_e('Reset Wizard','booking');
						?></a>
						<?php  ?>
					</p>
				</div>
			</div>
			<?php /**/ ?>


		</div>
		<style type="text/css">
			.wpbc_ajx_page__container .wpbc_ajx_page__section_footer:not(.wpbc__row){ display: none;}
		</style>
	</div>
	</script><?php
}

