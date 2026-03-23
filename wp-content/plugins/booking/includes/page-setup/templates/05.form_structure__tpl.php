<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// -------------------------------------------------------------------------------------------------------------
// == Main - Form Structure  ==
// -------------------------------------------------------------------------------------------------------------

/**
 * Template - Form Structure
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
function wpbc_stp_wiz__template__form_structure(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__form_structure">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__form_structure">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 0 auto;flex-flow: column;margin:0 0 10px;">
						<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Booking Form Structure', 'booking' ); ?></h1>
						<p class="wpbc_swp_section_header_description"><?php esc_html_e('Customize the structure of your booking form.','booking'); ?></p>
					</div>
					<div class="wpbc__field">
						<#
							var wpbc_template__timeline_steps = wp.template( 'wpbc_template__timeline_steps' );
						#>
						<div class="wpbc__container_place__steps_for_timeline">{{{  wpbc_template__timeline_steps( data.steps_is_done )  }}}</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		// == Main Section ==
		?>
		<div class="wpbc_ajx_page__section_main"><?php

			wpbc_maybe_show_warning_conflict__wordfence();

			?>
			<div class="wpbc_widgets">

				<div class="wpbc_widget             wpbc_widget_preview_booking_form">
					<div class="wpbc_widget_header">
						<span class="wpbc_widget_header_text"><?php
							esc_html_e( 'Preview', 'booking' );
							echo ' <i class="menu_icon icon-1x wpbc_icn_navigate_next"></i> ';
							esc_html_e( 'Booking Form', 'booking' );
						?></span>
						<?php wpbc_stp_wiz__ui__form_structure__mobile_buttons(); ?>
					</div>
					<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders">
						<div class="wpbc_center_preview" style="margin: 0 auto;">
							<# data.calendar_force_load = data.calendar_force_load.replace( /ajax_scrpt/gi, 'script' ); #>
							{{{data.calendar_force_load}}}
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
		// == Right Section ==
		?>
		<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_right 	wpbc_ajx_page__section_right">
			<div class="wpbc_widgets">
				<?php

				wpbc_stp_wiz__widget__form_structure__select_form_template();
				wpbc_stp_wiz__ui__form_structure__help();

				?>
			</div>
		</div>
		<?php //wpbc_stp_wiz__template__form_structure__bottom_buttons(); ?>
		<?php
		// == CSS to Hide original 'Steps as Dots' ==
		?>
		<style type="text/css">
			.wpbc__container_place__steps_for_timeline {
				display: none;
			}
			.wpbc__container_place__header__steps_for_timeline .wpbc__container_place__steps_for_timeline {
				display: flex;
			}
			.wpbc_setup_wizard_page_container .wpbc__container_place__steps_for_timeline{
				margin: 0 auto 10px;
			}
		</style>
	</script><?php
}


/**
 * Widget - Right Side - Select Form Template
 * @return void
 */
function wpbc_stp_wiz__widget__form_structure__select_form_template(){

	$booking_action = 'set_booking_form_template';
	$el_id          = 'ui_btn_cstm__' . $booking_action;
	?>
	<div class="wpbc_widget wpbc_widget_change_form_structure">
		<div class="wpbc_widget_header">
			<span class="wpbc_widget_header_text"><?php esc_html_e('Form Template','booking'); ?></span>
		</div>
		<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders">
			<div class="ui_container">

				<div class="ui_group    ui_group__change_form_structure"><?php
					/*
					?><div class="ui_element ui_nowrap0" style="flex: 1 1 100%;margin: 0;"><?php
						wpbc_flex_label(  array( 'id' => $el_id, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Form Template', 'booking' ) . ':</span>' )  );
					?></div><?php
					*/
					?><div class="ui_element ui_nowrap" style="flex: 1 1 100%;margin: 0;"><?php

						wpbc_stp_wiz__ui__form_structure__dropdown_form_template();

						// $is_apply_rotating_icon = false;
						// wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
						// wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
					?></div>
					<?php

					$params_button_save = array(
						'type'             => 'button' ,
						'title'            => '&nbsp;'. __( 'Apply Form', 'booking' ),  													// Title of the button
						'hint'             => array( 'title' => __( 'Click to load selected form', 'booking' ), 'position' => 'top' ),  	// Hint
						'link'             => 'javascript:void(0)',  																		// Direct link or skip  it
						'style'            => '',																							// Any CSS class here
						'mobile_show_text' => true,																							// Show  or hide text,  when viewing on Mobile devices (small window size).
						'icon' 			   => array( 'icon_font' => 'wpbc_icn_check _circle_outline', 'position'  => 'left', 'icon_img'  => '' ),
						'class'            => 'wpbc_ui_button _primary',  																	// ''  | 'wpbc_ui_button_primary'
						'attr'             => array( 'id' => $el_id . '_apply' ),
						//'action'         =>  "wpbc_ajx_booking__ui_click_save__set_booking_cost( {{data['parsed_fields']['booking_id']}}, this, '{$booking_action}', '{$el_id}' );" ,
						'action' 		   => " wpbc_ajx__setup_wizard_page__send_request_with_params( {								   																		
																							   'do_action': 'load_form_template',
																							   'ui_clicked_element_id': '{$el_id}_apply', 
																							   'step_data':{
																									'wpbc_swp_booking_form_template_pro': jQuery( '#ui_btn_cstm__set_booking_form_template_pro').val()
																								}								   																		   								   																		
																							});
									  wpbc_button_enable_loading_icon( this );
									  wpbc_admin_show_message_processing( '' );"
					);
					?><div class="ui_element ui_nowrap  " style="margin-left:auto;"><?php
						wpbc_flex_button( $params_button_save );
					?></div>
				</div><?php

				?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
					jQuery( document ).ready( function (){
						jQuery( '#ui_btn_cstm__set_booking_form_template_pro' ).off( 'change' );
						jQuery( '#ui_btn_cstm__set_booking_form_template_pro' ).on( 'change', function (){
							wpbc_blink_element( '#ui_btn_cstm__set_booking_form_template_apply', 3, 50 );
						});
					} );
				<?php if (0) { ?></script><?php } ?> #><?php

			?>
			</div>
		</div>
	</div>
	<?php
}


// ---------------------------------------------------------------------------------------------------------------------
//  ==    UI    for    T e m p l a t e s   ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Mobile buttons at  top right side of window
 * @return void
 */
function wpbc_stp_wiz__ui__form_structure__mobile_buttons(){
	?>
	<span class="wpbc_widget_header_settings_link">
		<div class="wpbc_section_preview_mobile_btn_bar">
			<a class="wpbc_preview_a active" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '100%', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
				<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-display _icn_computer"></i></a>
			<a class="wpbc_preview_a" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '750px', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
				<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-tablet _icn_tablet_mac"></i></a>
			<a class="wpbc_preview_a" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '350px', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
				<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-phone _icn_phone_iphone"></i>
			</a>
		</div>
	</span>
	<?php
}


/**
 * Select-box - booking_form_template
 *
 * @return void
 */
function wpbc_stp_wiz__ui__form_structure__dropdown_form_template(){

	$booking_action = 'set_booking_form_template_pro';
	$el_id = 'ui_btn_cstm__' . $booking_action ;

	// ---------------------------------------------------------------------------------------------------------
	//  Templates
	// ---------------------------------------------------------------------------------------------------------
	$templates = array(
		'optgroup_sf_free_s'     => array( 'optgroup' => true, 'close' => false, 'title' => '&nbsp;' . __( 'Simple Form', 'booking' ) . ' (' . __( 'Free Version', 'booking' ) . ')' ),
		'free|wizard_2columns'   => array( 'title' => __( 'Wizard (several steps)', 'booking' ) ),
		'free|wizard_services_a' => array( 'title' => __( 'Wizard (Steps Timline)', 'booking' ) . ' - ' . __( 'Service Duration', 'booking' ) ),
		'free|vertical'          => array( 'title' => __( 'Form under calendar', 'booking' ) ),
		'free|form_right'        => array( 'title' => __( 'Form at right side of calendar', 'booking' ) ),
		'free|form_center'       => array( 'title' => __( 'Form and calendar are centered', 'booking' ) ),
		'optgroup_sf_free_e'     => array( 'optgroup' => true, 'close' => true ),
	);
	// ---------------------------------------------------------------------------------------------------------
	$templates[ 'pro|optgroup_sf_s'] = array( 'optgroup' => true, 'close' => false, 'title' => '&nbsp;' . __( 'Advanced Form', 'booking' ) . ' (' . __( 'Pro Versions', 'booking' ) . ')'  );

	$templates[ 'pro|appointments_service_c' ] = array( 'title' => __( 'Service-Based Appointments', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );  // FixIn: 10.9.6.5.
	$templates[ 'pro|appointments30' ]         = array( 'title' => __( 'Time-Based Appointments', 'booking') .  ' ('.  ' 30 ' . __( 'minutes', 'booking' ) . ')', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
	$templates[ 'pro|wizard_no_times_a' ]      = array( 'title' => __( 'Wizard (several steps)', 'booking' ) . ' - ' . __('No times','booking'), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
	$templates[ 'pro|wizard_times30' ]         = array( 'title' => __( 'Wizard', 'booking') .  ' ('.__('Time slots', 'booking') . ' 30 ' . __( 'minutes', 'booking' ) . ')', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );

	/*
		$templates[ 'pro|wizard_times15' ] 		= array( 'title' => __('Wizard', 'booking') .  ' ('.__('Time slots', 'booking') . ' 15 ' . __( 'minutes', 'booking' ) . ' (AM/PM)' . ')', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
		$templates[ 'pro|wizard_times120' ] 	= array( 'title' => __('Wizard', 'booking') .  ' ('.__('Time slots', 'booking') .  ' 2 ' . __( 'hours', 'booking' ) . ')', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
		$templates[ 'pro|wizard_times60' ] 		= array( 'title' => __('Wizard', 'booking') .  ' ('.__('Time slots', 'booking') .  ' 1 ' . __( 'hour', 'booking' ) . ')', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) )  );
		$templates[ 'pro|wizard_times60_24h' ] 	= array( 'title' => __('Wizard', 'booking') .  ' ('.__('Time slots', 'booking') .  ' 1 ' . __( 'hour', 'booking' ) . ' (24H)', 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) )  );
	*/

	// if ( class_exists( 'wpdev_bk_biz_m' ) ) {
		$templates[ 'pro|timesweek'] = array( 'title' => __( 'Time slots for different weekdays', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_m' ) ) );
		$templates[ 'pro|hints']     = array( 'title' => __( 'Hints', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_m' ) ) );
		$templates[ 'pro|hints-dev'] = array( 'title' => __( 'Hints', 'booking' ) . ' [' . __( 'days', 'booking' ) . ']', 'disabled' => ( ! class_exists( 'wpdev_bk_biz_m' ) ) );
	// }

	$templates[ 'pro|optgroup_wiz_e'] = array( 'optgroup' => true, 'close' => true );
	// ---------------------------------------------------------------------------------------------------------
	$templates[ 'pro|optgroup_anf_s'] = array( 'optgroup' => true, 'close' => false, 'title'  => '&nbsp;' . __('Other' ,'booking') . ' (' . __( 'Pro Versions', 'booking' ) . ')'  );
	/*
		$templates['pro|standard']            = array( 'title' => __( 'Standard', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
		$templates['pro|2collumns']           = array( 'title' => __( 'Calendar next to form', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
	*/
	$templates['pro|fields2columns']      = array( 'title' => '2 ' . __( 'columns', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
	$templates['pro|fields3columns']      = array( 'title' => '3 ' . __( 'columns', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );
	$templates['pro|fields2columnstimes'] = array( 'title' => __( '2 columns with  times', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_personal' ) ) );

	// if ( class_exists( 'wpdev_bk_biz_s' ) ) {
		$templates[ 'pro|payment']           = array( 'title' => __( 'Payment', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|paymentUS']         = array( 'title' => __( 'Payment', 'booking' ) . ' (US)', 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );

		$templates[ 'pro|optgroup_sf_e']   = array( 'optgroup' => true, 'close' => true );
		// ---------------------------------------------------------------------------------------------------------
		$templates[ 'pro|optgroup_tf_s'] = array( 'optgroup' => true, 'close' => false, 'title'  => '&nbsp;' . __('Times Templates' ,'booking') . ' (' . __( 'Pro Versions', 'booking' ) . ')'  );

		$templates[ 'pro|times']        = array( 'title' => __( 'Time slots', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|times120']     = array( 'title' => __( 'Time slots', 'booking' ) . ' 2 ' . __( 'hours', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|times60']      = array( 'title' => __( 'Time slots', 'booking' ) . ' 1 ' . __( 'hour', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|times60_24h']  = array( 'title' => __( 'Time slots', 'booking' ) . ' 1 ' . __( 'hour', 'booking' ) . ' (24H)', 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|times30']      = array( 'title' => __( 'Time slots', 'booking' ) . ' 30 ' . __( 'minutes', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|times15']      = array( 'title' => __( 'Time slots', 'booking' ) . ' 15 ' . __( 'minutes', 'booking' ) . ' (AM/PM)', 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|endtime']      = array( 'title' => __( 'Start Time', 'booking' ) . ' / ' . __( 'End Time', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
		$templates[ 'pro|durationtime'] = array( 'title' => __( 'Start Time', 'booking' ) . ' / ' . __( 'Duration Time', 'booking' ), 'disabled' => ( ! class_exists( 'wpdev_bk_biz_s' ) ) );
	// }
	$templates['pro|optgroup_tf_e'] = array( 'optgroup' => true, 'close' => true );
	// ---------------------------------------------------------------------------------------------------------



	//	if (function_exists('wpbc_flex_ui__dropdown_list__form_templates__get_options')){   $templates = wpbc_flex_ui__dropdown_list__form_templates__get_options();  }

	$params_select = array(
						  'id'       => $el_id 				// HTML ID  of element
						, 'name'     => $booking_action
						, 'label'    => '' 																		//__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
						, 'style'    => 'height:22em;scrollbar-width: thin;' 									// CSS of select element
						, 'class'    => 'wpbc_radio__set_days_customize_plugin' 								// CSS Class of select element
						//, 'multiple' => true
						, 'disabled' => false
						, 'disabled_options' => array()     													// If some options disabled, then it has to list here
						, 'options'  => $templates
						//, 'attr'   => array( 'value_of_selected_option' => '{{selected_locale_value}}' )		// Any additional attributes, if this radio | checkbox element
						, 'attr'     => array( 'multiple' => 'multiple' )
						, 'onchange' =>  "if ( 'function' === typeof( wpbc_in_form__make_exclusive_selectbox ) ){  wpbc_in_form__make_exclusive_selectbox( this ); }"
						//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
//							, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
//							, 'onchange' => "wpbc_ajx_customize_plugin.search_set_param('customize_plugin__booking_skin', jQuery(this).val().replace( '" . WPBC_PLUGIN_URL . "', '') );"
//							, 'onchange' =>  "jQuery(this).hide();
//											 var jButton = jQuery('#button_locale_for_booking{{data[\'parsed_fields\'][\'booking_id\']}}');
//											 jButton.show();
//											 wpbc_button_enable_loading_icon( jButton.get(0) ); "
//											 . " wpbc_ajx_booking_ajax_action_request( {
//																						'booking_action' : '{$booking_action}',
//																						'booking_id'     : {{data[\'parsed_fields\'][\'booking_id\']}},
//																						'booking_meta_locale' : jQuery('#locale_for_booking{{data[\'parsed_fields\'][\'booking_id\']}} option:selected').val()
//																					} );"
	);
		wpbc_flex_select( $params_select );

	?>
	<#
		jQuery( '#<?php echo esc_attr( $el_id ); ?> option' ).prop('selected', false);
		if (
			   ( 'undefined' !== typeof( data.booking_wizard_data) )
			&& ( 'undefined' !== typeof( data.booking_wizard_data.save_and_continue__bookings_types ) )
			&& ( 'undefined' !== typeof( data.booking_wizard_data.save_and_continue__bookings_types.wpbc_swp_booking_types ) )
		){
			jQuery(document).ready(function(){
				if ( 'time_slots_appointments' === data.booking_wizard_data.save_and_continue__bookings_types.wpbc_swp_booking_types ) {
					if ( 'durationtime' === data.booking_wizard_data.save_and_continue__bookings_types.wpbc_swp_booking_appointments_type ){
						jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="free|wizard_services_a"]' ).prop('selected', true);
					} else {
						jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="free|wizard_2columns"]' ).prop('selected', true);
					}
				} else {
					jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="free|form_right"]' ).prop('selected', true);
				}
			});
		}
		if (
			   ( 'undefined' !== typeof( data.booking_wizard_data) )
			&& ( 'undefined' !== typeof( data.booking_wizard_data.load_form_template ) )
			&& ( 'undefined' !== typeof( data.booking_wizard_data.load_form_template.wpbc_swp_booking_form_template_pro ) )
		){
			jQuery(document).ready(function(){
				jQuery( '#<?php echo esc_attr( $el_id ); ?> option' ).prop('selected', false);
				jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="' + data.booking_wizard_data.load_form_template.wpbc_swp_booking_form_template_pro + '"]' ).prop( 'selected', true );
			});
		}
	#>
	<style tye="text/css">
		#<?php echo esc_attr( $el_id ); ?> optgroup {
			color: #b7b7b7;
			padding: 10px 0 0;
			font-style: normal;
		}
		#<?php echo esc_attr( $el_id ); ?> option {
			color: #1d2327;
			padding-top: 10px;
			padding-bottom: 10px;
		}
		#<?php echo esc_attr( $el_id ); ?> option:disabled{
			color: #ccc;
		}
		#<?php echo esc_attr( $el_id ); ?> option:first-child {
			margin-top: 10px;
		}
	</style>
	<?php
}


/**
 * Widget - Right Side -  "Customize fields in booking form"  -- HELP section
 * @return void
 */
function wpbc_stp_wiz__ui__form_structure__help(){

	// Help message
	?><div class="wpbc-settings-notice notice-warning notice-helpful-info" style="height: auto;font-size: 12px;margin: 0 0 1.5em;">
		<?php
	/* translators: 1: ... */
	echo wp_kses_post( sprintf( __(  'You can configure form fields later in %s', 'booking' ),
			'<a href="'. esc_attr( wpbc_get_settings_url() . '&tab=form' ).'">Settings > Booking Form</a>' ) ); ?>
	</div><?php
}


/**
 * Custom bottom Line:   Back | Next   buttons
 *
 * @return void
 */
function wpbc_stp_wiz__ui__form_structure__bottom_buttons(){

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
	</style><?php
}
