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


// ---------------------------------------------------------------------------------------------------------------------
// == Main - Calendar Availability  ==
// ---------------------------------------------------------------------------------------------------------------------

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
function wpbc_stp_wiz__template__cal_availability(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__cal_availability">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__cal_availability">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 0 auto;flex-flow: column;margin:0 0 10px;">
						<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'General Availability', 'booking' ); ?></h1>
						<p class="wpbc_swp_section_header_description"><?php esc_html_e('Define unavailable weekdays, add booking buffers, and customize unavailable day options.','booking'); ?></p>
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
		<div class="wpbc_ajx_page__section_main">
			<div class="wpbc_widgets">
				<div class="wpbc_widget             wpbc_widget_preview_booking_form">
					<div class="wpbc_widget_header">
						<span class="wpbc_widget_header_text"><?php esc_html_e( 'Preview', 'booking' ); echo ' <i class="menu_icon icon-1x wpbc_icn_navigate_next"></i> '; esc_html_e( 'Booking Form', 'booking' ); ?></span>
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
			<div class="wpbc_widgets"><?php

				wpbc_stp_wiz__widget__cal_availability__weekdays();

				wpbc_stp_wiz__widget__cal_availability__advanced_availability();

				wpbc_stp_wiz__ui__cal_availability__help();

			?>
			</div>
		</div>
		<?php //wpbc_template__stp_wiz__cal_availability__bottom_buttons(); ?>
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
 * Widget - Right Side - Weekdays Availability
 *
 * @return void
 */
function wpbc_stp_wiz__widget__cal_availability__weekdays(){

	?>
	<div class="wpbc_widget wpbc_widget__cal_availability__weekdays">
		<div class="wpbc_widget_header">
			<span class="wpbc_widget_header_text"><?php esc_html_e('Unavailable Weekdays','booking'); ?></span>
		</div>
		<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders">
			<div class="ui_container">
				<div class="ui_group    ui_group__cal_availability__weekdays"><?php

					wpbc_stp_wiz__ui__cal_availability__weekdays_toggles();

				?></div>
			</div>
		</div>
	</div>
	<?php
}


/**
 * Widget - Right Side -  Before/Afer Today Availability  |  Buffers
 *
 * @return void
 */
function wpbc_stp_wiz__widget__cal_availability__advanced_availability(){
    ?>
	<div class="wpbc_widget wpbc_widget__cal_availability__advanced_availability">
		<div class="wpbc_widget_header">
			<span class="wpbc_widget_header_text"><?php esc_html_e('Advanced availability', 'booking'); ?></span>
			<!--a href="https://wpbookingcalendar.com/features/#availability-from-today" target="_blank" class="wpbc_widget_header_settings_link"><span class="wpbc_pro_label">Pro</span></i></a-->
		</div>
		<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders" style="margin:0 0 0px;">

			<div class="ui_container    ui_container_toolbar		ui_container_small0">
				<div class="ui_group    ui_group__cal_availability__today_availability">
				<?php
					wpbc_stp_wiz__ui__cal_availability__unavailable_from_today();
				?>
				</div>
				<?php
						$is_blur = ( ! class_exists( 'wpdev_bk_biz_m' ) ) ? 'wpbc_blur' : '';
						if ( ! empty( $is_blur ) ) {
							?><div class="clear" style="width:101%;height:50px;"></div><?php
							wpbc_stp_wiz__ui__upgrade_note( 'biz_m', 'https://wpbookingcalendar.com/features/#availability-from-today' );
						}
				?>
				<div class="ui_group    ui_group__cal_availability__buffer_availability <?php echo esc_attr( $is_blur ); ?>">
				<?php
					wpbc_stp_wiz__ui__cal_availability__limit_available_from_today();

					wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings();
				?>
				</div>
			</div>

			<div class="ui_container    ui_container_toolbar		ui_container_small">
				<div class="ui_group    ui_group__cal_availability__reset" style="flex: 1 1 auto;"><?php

					?><div class="ui_element ui_nowrap0" style="margin-left: auto;"><?php
						wpbc_stp_wiz__ui__cal_availability__reset__btn();
					?></div><?php

					?>
				</div>
			</div>

		</div>
	</div>
	<?php
}




// <editor-fold     defaultstate="collapsed"                        desc=" ==  UI  Elements for  Availability    == "  >

	/**
	 *  Range Days 	Start week days*
	 */
	function wpbc_stp_wiz__ui__cal_availability__weekdays_toggles(){

		$booking_action = 'set_cal_availability';

		$el_id = 'ui_btn_cstm__' . $booking_action . '_weekdays';

		?><div class="ui_element ui_element_micro ui_nowrap0" style="flex:1 1 100%;"><?php

			wpbc_flex_label( array(  'id' 	  => $el_id
									, 'style'=>'height: auto;line-height: 1.75em;'
									, 'label' => '<span class="" style="font-weight:400;">' .
												 __( 'Select weekdays to be marked as unavailable in calendars.', 'booking' ).
												 '</span>'
							) );
		?></div><?php

		$week_days_arr = array(
							  1 => __( 'Mo', 'booking' )
							, 2 => __( 'Tu', 'booking' )
							, 3 => __( 'We', 'booking' )
							, 4 => __( 'Th', 'booking' )
							, 5 => __( 'Fr', 'booking' )
							, 6 => __( 'Sa', 'booking' )
							, 0 => __( 'Su', 'booking' )
						);

		foreach ( $week_days_arr as $day_key => $day_title ) {

			$params_checkbox = array(
									  'id'       => $el_id . '_'.$day_key 		// HTML ID  of element
									, 'name'     => $el_id . '_'.$day_key
									, 'label'    => array( 'title' => $day_title , 'position' => 'right' )           // FixIn: 9.6.1.5.
									, 'style'    => '' 					// CSS of select element
									, 'class'    => '' 					// CSS Class of select element
									, 'disabled' => false
									, 'attr'     => array() 							// Any  additional attributes, if this radio | checkbox element
									, 'legend'   => ''									// aria-label parameter
									, 'value'    => $day_key 							// Some Value from optins array that selected by default
									, 'selected' => false								// Selected or not
									//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
									//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__send_emails': (jQuery( this ).is(':checked') ? 'send' : 'not_send') } );"					// JavaScript code
									//	, 'onchange' => "
									//					 var sel_arr_el = _wpbc.get_other_param('availability__week_days_unavailable');
									//					 //console.log( '1:sel_arr_el', sel_arr_el , jQuery( this ).is(':checked') , '".$day_key."' );
									//					 if ( jQuery( this ).is(':checked') ) {
									//						sel_arr_el = _.uniq( [".$day_key."].concat( sel_arr_el ) ).sort().join( ',');
									//					 } else {
									//						sel_arr_el = _.uniq( _.without( sel_arr_el, ".$day_key." ) ).sort().join( ',');
									//					 }
									//					 // console.log( '2:sel_arr_el', sel_arr_el );
									//					 wpbc_ajx__setup_wizard_page__send_request_with_params( {
									//															   'do_action': 'load_form_template',
									//															   'step_data':{
									//																	'wpbc_swp_cal_availability__weekdays': sel_arr_el
									//																}
									//															});
									//					 wpbc_button_enable_loading_icon( this );
									//					 wpbc_admin_show_message_processing( '' );"
								);
			?><div class="ui_element ui_element_micro"><?php
				wpbc_flex_toggle( $params_checkbox );
			?></div><?php

			// Set checked specific Radio button,  depends on  last action  from  user
			?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
				jQuery( document ).ready( function (){
					//if   ( -1 !== _wpbc.get_other_param('availability__week_days_unavailable').indexOf( parseInt(<?php echo esc_attr( $day_key ); ?>) ) ) {
					if ( wpbc_in_array( data.ui.booking_unavailable_day, parseInt( <?php echo esc_attr( $day_key ); ?> ) ) ){
						jQuery( '#<?php echo esc_attr( $el_id . '_'.$day_key ); ?>' ).prop( 'checked', true );//.trigger( 'change' );
					} else {
						jQuery( '#<?php echo esc_attr( $el_id . '_'.$day_key ); ?>' ).prop( 'checked', false );//.trigger( 'change' );
					}
					jQuery( '#<?php echo esc_attr( $el_id . '_'.$day_key ); ?>' ).off( 'change' );
					jQuery( '#<?php echo esc_attr( $el_id . '_'.$day_key ); ?>' ).on( 'change', function (){
						wpbc_blink_element( '#<?php echo esc_attr( $el_id . '_apply' ); ?>', 2, 50 );
					});
				} );
			<?php if (0) { ?></script><?php } ?> #><?php
		}

		$params_button_save = array(
			'type'             => 'button' ,
			'title'            => '&nbsp;' . __( 'Apply Availability', 'booking' ),  													// Title of the button
			'hint'             => array( 'title' => __( 'Click to load selected form', 'booking' ), 'position' => 'top' ),  	// Hint
			'link'             => 'javascript:void(0)',  																		// Direct link or skip  it
			'style'            => '',																							// Any CSS class here
			'mobile_show_text' => true,																							// Show  or hide text,  when viewing on Mobile devices (small window size).
			'icon' 			   => array( 'icon_font' => 'wpbc_icn_check _circle_outline', 'position'  => 'left', 'icon_img'  => '' ),
			'class'            => 'wpbc_ui_button _primary',  																	// ''  | 'wpbc_ui_button_primary'
			'attr'             => array( 'id' => $el_id . '_apply' ),
			'action' 		   => " var sel_arr_el = []; /* _wpbc.get_other_param('availability__week_days_unavailable'); */
									for (var i=0; i < 7; i++) {			 			
										sel_arr_el = ( jQuery( '#".$el_id ."_' + i ).is(':checked') ) 
														? _.uniq( [ i ].concat( sel_arr_el ) ).sort() 
														: _.uniq( _.without( sel_arr_el, i ) ).sort();
									}
									sel_arr_el.push(999);
									sel_arr_el = sel_arr_el.join( ',');
									wpbc_ajx__setup_wizard_page__send_request_with_params({   
																							   'do_action': 'load_form_template',
																							   'step_data':{
																											'wpbc_swp_cal_availability__weekdays': sel_arr_el
																										}
																						 });
									wpbc_button_enable_loading_icon( this );
									wpbc_admin_show_message_processing( '' );"
		);
		?><div style="flex:1 1 100%;height:0;"></div><?php
		?><div class="ui_element ui_nowrap" style="margin-left:auto;"><?php
			wpbc_flex_button( $params_button_save );
		?></div><?php

		?><div style="flex:1 1 100%;height:0;border-top:1px solid #dfdfdf;margin: 10px 0;"></div><?php

			wpbc_flex_label( array(  'id' 	  => $el_id
									, 'style'=>'height: auto;line-height: 1.75em;font-size:10px;'
									, 'label' => '<span class="" style="font-weight:400;">' .
												 __( 'This setting will override all other availability settings.', 'booking' ).
												 '</span>'
							) );

	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 *  Unavailable - from Today
	 */
	function wpbc_stp_wiz__ui__cal_availability__unavailable_from_today(){

		$booking_action = 'set_calendar_unavailable_from_today';

		$el_id = 'ui_btn_cstm__' . $booking_action ;

		//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}

		?><div class="ui_element ui_nowrap0"><?php

			wpbc_flex_label( array(  'id' 	  => $el_id
									, 'style' => 'height:auto;'
									, 'label' => '<div class="" style="font-weight:400;">'
													. 	__( 'Unavailable days from today', 'booking' ) . ': '
													. '<div style="line-height: 1.5;"><code id="' . $el_id . '_hint" 
															style="font-weight: 600;font-size: 10px;padding: 3px 5px;color: #626262;border-radius:2px;background: #f9f2f4;"></code></div>'
			                                    . '</div>'
							) );
		?></div><?php

		?><div class="ui_element ui_nowrap"><?php

			//  Options
			$dropdown_options = range( 0, 31 );

			// FixIn: 10.8.1.4.
			$field_options = array();

			$field_options[0] = ' - ';
			foreach ( range( 5, 55 , 5 ) as $extra_num) {                                           // Each 5 minutes
				$field_options[ $extra_num . 'm' ] = $extra_num . ' ' . __( 'minutes', 'booking' );
			}
			$field_options[ '60' . 'm' ] =  '1 ' . __( 'hour', 'booking' );
			foreach ( range( 65, 115 , 5 ) as $extra_num) {                                         // 1 hour + Each 5 minutes
				$field_options[ $extra_num . 'm' ] =  '1 ' . __( 'hour', 'booking' ) . ' ' . ($extra_num - 60 ) . ' ' . __( 'minutes', 'booking' );
			}
			foreach ( range( 120, 1380 , 60 ) as $extra_num) {                                      // Each Hour based on minutes
				$field_options[ $extra_num . 'm' ] = ($extra_num / 60) . ' ' . __( 'hours', 'booking' );
			}
			$field_options[ 1 ] = 1  . ' ' . __( 'day', 'booking' );
			for ( $ii = 2; $ii < 92; $ii ++ ) {
				$field_options[ $ii ] = $ii . ' ' . __( 'days', 'booking' );
			}
			$dropdown_options = $field_options;


			$params_select = array(
								  'id'       => $el_id 				// HTML ID  of element
								, 'name'     => $booking_action
								, 'label' => '' 				//__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
								, 'style'    => '' 					// CSS of select element
								, 'class'    => '' 					// CSS Class of select element
								//, 'multiple' => true
								//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
								, 'disabled' => false
								, 'disabled_options' => array()     								// If some options disabled, then it has to list here
								, 'options' => $dropdown_options
								//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
								//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
								, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																								   'do_action': 'load_form_template',
																								   'step_data':{
																										'booking_unavailable_days_num_from_today': jQuery(this).val()
																									}
																							 });
												wpbc_button_enable_loading_icon( this );
												wpbc_admin_show_message_processing( '' );"

							  );
			wpbc_flex_select( $params_select );


			$is_apply_rotating_icon = false;
			wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
			wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
		?></div><?php

		// Set checked specific OPTION depends on last action from  user
		?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
			jQuery( document ).ready( function (){
				// Set selected option  in dropdown list based on  data. value
				jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="' + data.ui.booking_unavailable_days_num_from_today + '"]' ).prop( 'selected', true );

				jQuery( '#<?php echo esc_attr( $el_id ); ?>_hint' ).html( '<span style="color: #cc3a5f;text-transform: uppercase;"><?php echo esc_js( __('Unavailable', 'booking' ) ); ?></span>'
															+ data.ui.booking_unavailable_days_num_from_today__hint );
			} );

		<?php if (0) { ?></script><?php } ?> #><?php

	}

	/**
	 *  Limit Available - from Today
	 */
	function wpbc_stp_wiz__ui__cal_availability__limit_available_from_today(){

		$booking_action = 'set_calendar_limit_available_from_today';

		$el_id = 'ui_btn_cstm__' . $booking_action ;

		//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}

		?><div class="ui_element ui_nowrap0"><?php

			wpbc_flex_label( array(  'id' 	  => $el_id
									, 'style' => 'height:auto;'
									, 'label' => '<div class="" style="font-weight:400;">'
													. 	__( 'Limit available days from today', 'booking' ) . ': '
													. '<div><code id="' . $el_id . '_hint" 
																  style="font-weight: 600;font-size: 10px;padding: 3px 5px;color: #626262;border-radius:2px;background: #f9f2f4;"></code></div>'
			                                    . '</div>'
							) );
		?></div><?php

		?><div class="ui_element ui_nowrap"><?php

			//  Options
			$dropdown_options = array( '' => ' - ' );
			//foreach ( range( 365, 1, -1 ) as $value ) {
			foreach ( range( 1,365 ) as $value ) {
				$dropdown_options[ $value ] = $value;
			}

			$params_select = array(
								  'id'       => $el_id 				// HTML ID  of element
								, 'name'     => $booking_action
								, 'label' => '' 				//__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
								, 'style'    => '' 					// CSS of select element
								, 'class'    => '' 					// CSS Class of select element
								//, 'multiple' => true
								//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
								, 'disabled' => false
								, 'disabled_options' => array()     								// If some options disabled, then it has to list here
								, 'options' => $dropdown_options
								//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
								//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
								, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																								   'do_action': 'load_form_template',
																								   'step_data':{
																										'booking_available_days_num_from_today': jQuery(this).val()
																									}
																							 });
												wpbc_button_enable_loading_icon( this );
												wpbc_admin_show_message_processing( '' );"

							  );
			wpbc_flex_select( $params_select );


			$is_apply_rotating_icon = false;
			wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
			wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );

		?></div><?php

		// Set checked specific OPTION depends on last action from  user
		?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
			jQuery( document ).ready( function (){
				// Set selected option  in dropdown list based on  data. value
				jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="' + data.ui.booking_available_days_num_from_today + '"]' ).prop( 'selected', true );

				jQuery( '#<?php echo esc_attr( $el_id ); ?>_hint' ).html(  '<span style="color: #50be31;text-transform: uppercase;"><?php echo esc_js( __('Available', 'booking' ) ); ?></span>'
															+ data.ui.booking_available_days_num_from_today__hint );
			} );

		<?php if (0) { ?></script><?php } ?> #><?php

	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Radio  buttons  - Unavailable time buffers: Before|After  -  Business Medium or higher versions
	 *
	 * @return void
	 */
	function wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings(){

		$booking_action = 'set_calendar_unavailable_before_after_bookings';

		$el_id = 'ui_btn_cstm__' . $booking_action ;

		//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}
		?><div class="clear" style="height: 1px;width: 100%;border-top: 1px solid #ccc;margin: 15px 0 10px;"></div><?php
		?><div class="ui_element ui_nowrap0"><?php

			wpbc_flex_label( array(  'id' 	  => $el_id
									, 'style' => 'height: auto;'
									, 'label' => '<span class="" style="font-weight:400;">'
													. __('Unavailable time before / after booking' ,'booking')
												 .':</span>'

							) );
		?></div><?php

			$el_value = '';
		?><div class="ui_element"><?php
			?><span class="wpbc_ui_control wpbc_ui_button <?php echo esc_attr( $el_id. '_' . $el_value . '__outer_button' ); ?>" style="padding-right: 8px;"><?php
				$params_radio = array(
								  'id'       => $el_id . '_' . $el_value			// HTML ID  of element
								, 'name'     => $booking_action
								, 'label'    => array(    'title' => ucfirst( __( 'None', 'booking' ) )
														, 'position' => 'right' )
								, 'style'    => 'margin:1px 0 0;' 					// CSS of select element
										, 'class'    => 'wpbc_radio__set_days_availability' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => $el_value 		// Some Value from options array that selected by default
										//, 'selected' => true
										//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
									);
				wpbc_flex_radio( $params_radio );

			?></span><?php
		?></div><?php


			$el_value = 'm';
		?><div class="ui_element"><?php
			?><span class="wpbc_ui_control wpbc_ui_button <?php echo esc_attr( $el_id. '_' . $el_value . '__outer_button' ); ?>" style="padding-right: 8px;"><?php
				$params_radio = array(
								  'id'       => $el_id . '_' . $el_value			// HTML ID  of element
								, 'name'     => $booking_action
								, 'label'    => array(    'title' => ucfirst( __( 'minutes', 'booking' ) ) . ' / ' . ucfirst( __( 'hours', 'booking' ) )
														, 'position' => 'right' )
								, 'style'    => 'margin:1px 0 0;' 					// CSS of select element
										, 'class'    => 'wpbc_radio__set_days_availability' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => $el_value 		// Some Value from options array that selected by default
										//, 'selected' => false
										//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
									);
				wpbc_flex_radio( $params_radio );

			?></span><?php
		?></div><?php


		?><div class="ui_element_sub_section ui_element_sub_section_m" ><?php

			wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings_minutes();

		?></div><?php



			$el_value = 'd';
		?><div class="ui_element"><?php
			?><span class="wpbc_ui_control wpbc_ui_button <?php echo esc_attr( $el_id. '_' . $el_value . '__outer_button' ); ?>" style="padding-right: 8px;"><?php
				$params_radio = array(
								  'id'       => $el_id . '_' . $el_value			// HTML ID  of element
								, 'name'     => $booking_action
								, 'label'    => array( 	  'title' => ucfirst( __( 'day(s)', 'booking' ) )
														, 'position' => 'right' )
								, 'style'    => 'margin:1px 0 0;' 					// CSS of select element
										, 'class'    => 'wpbc_radio__set_days_availability' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => $el_value 		// Some Value from options array that selected by default
										//, 'selected' => false
										//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
									);
				wpbc_flex_radio( $params_radio );

			?></span><?php
		?></div><?php


		?><div class="ui_element_sub_section ui_element_sub_section_d" ><?php

			wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings_days();

		?></div><?php

		?><div class="clear" style="height: 1px;width: 100%;border-top: 1px solid #ccc;margin: 15px 0 10px;"></div><?php

			// Set checked specific Radio button,  depends on  last action  from  user
			?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>

				jQuery( document ).ready( function (){

					<?php foreach ( array( '', 'm', 'd' ) as $item_val) { ?>

						// Change and send Ajax
						jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_<?php echo esc_attr( $item_val ); ?>' ).on( 'change', function ( event ){
							<?php // It's required for not send request second time !  ?>
							jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_<?php echo esc_attr( $item_val ); ?>' ).off( 'change' );
							//if ( '' === '<?php echo esc_attr( $item_val ); ?>' ) {
								wpbc_ajx__setup_wizard_page__send_request_with_params( {
									'do_action': 'load_form_template',
									'step_data': {
										'booking_unavailable_extra_in_out': '<?php echo esc_attr( $item_val ); ?>'
									}
								} );
								wpbc_button_enable_loading_icon( this );
								wpbc_admin_show_message_processing( '' );
							//}
							jQuery( '.ui_element_sub_section_d,.ui_element_sub_section_m').hide();
							if ('m' == '<?php echo esc_attr( $item_val ); ?>') {
								jQuery( '.ui_element_sub_section_m').show();
							}
							if ('d' == '<?php echo esc_attr( $item_val ); ?>') {
								jQuery( '.ui_element_sub_section_d').show();
							}
							return false;
						} );
						<?php // Helper,  if we click on button  side,  and not at  radio button or label,  then  make radio checked. ?>
						jQuery(     '.ui_btn_cstm__set_calendar_unavailable_before_after_bookings_<?php echo esc_attr( $item_val ); ?>__outer_button' ).on( 'click', function (){
							jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_<?php echo esc_attr( $item_val ); ?>' ).prop( "checked", true ).trigger('change');
						} );

					<?php } ?>

					// Set checked or not, specific radio buttons
					jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_' ).prop( 'checked', true );
					jQuery( '.ui_element_sub_section_d,.ui_element_sub_section_m').hide();
					if ( 'm' == data.ui.booking_unavailable_extra_in_out ){
						jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_m' ).prop( 'checked', true );
						jQuery( '.ui_element_sub_section_m').show();
					}
					if ( 'd' == data.ui.booking_unavailable_extra_in_out ){
						jQuery( '#ui_btn_cstm__set_calendar_unavailable_before_after_bookings_d' ).prop( 'checked', true );
						jQuery( '.ui_element_sub_section_d').show();
					}
					<?php
					// Show possible selection in paid versions,  while using lower version.
					if ( ! class_exists( 'wpdev_bk_biz_m' ) ) { ?>
						jQuery( '.ui_element_sub_section_m').show();
						jQuery( '.ui_element_sub_section_d').show();
					<?php } ?>
				} );

			<?php if (0) { ?></script><?php } ?> #><?php
	}

	function wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings_minutes(){

		$booking_action = 'booking_unavailable_extra_minutes';
		$el_id = 'ui_btn_cstm__' . $booking_action;

		//  Options
	   $extra_time = array();
		$extra_time[''] = ' - ';
		foreach ( range( 5, 55 , 5 ) as $extra_num) {                                           // Each 5 minutes
			$extra_time[ $extra_num . 'm' ] = $extra_num . ' ' . __( 'minutes', 'booking' );
		}
		$extra_time[ '60' . 'm' ] =  '1 ' . __( 'hour', 'booking' );
		foreach ( range( 65, 115 , 5 ) as $extra_num) {                                         // 1 hour + Each 5 minutes
			$extra_time[ $extra_num . 'm' ] =  '1 ' . __( 'hour', 'booking' ) . ' ' . ($extra_num - 60 ) . ' ' . __( 'minutes', 'booking' );
		}

		foreach ( range( 120, 1380 , 60 ) as $extra_num) {                                      // Each Hour based on minutes
			$extra_time[ $extra_num . 'm' ] = ($extra_num / 60) . ' ' . __( 'hours', 'booking' );
		}


		$params_select = array(
							  'id'       => $el_id  . '_in'				// HTML ID  of element
							, 'name'     => $el_id  . '_in'
							, 'label'    => __( 'Before booking', 'booking' )//'<span class="" style="font-weight:600;">' . esc_html__( 'Days', 'booking' ) . ' <em style="color:#888;">(' . __( 'min-max', 'booking' ) . '):</em></span>'
							, 'style'    => 'max-width: 100%;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $extra_time
							//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
							//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
							, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																							   'do_action': 'load_form_template',
																							   'step_data':{
																							   		'booking_unavailable_extra_in_out': jQuery( '[name=\"set_calendar_unavailable_before_after_bookings\"]:checked').val(),
																									'booking_unavailable_extra_minutes_in': jQuery(this).val()
																								}
																						 });
											wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );"
						  );
		?><div class="ui_element ui_element_micro"><?php
			wpbc_flex_select( $params_select );
		?></div><?php


		$params_select = array(
							  'id'       => $el_id  . '_out' 				// HTML ID  of element
							, 'name'     => $el_id  . '_out'
							, 'label'    => __( 'After booking', 'booking' )//'<span class="" style="font-weight:600;">' . esc_html__( 'Days', 'booking' ) . ' <em style="color:#888;">(' . __( 'min-max', 'booking' ) . '):</em></span>'
							, 'style'    => 'max-width: 100%;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $extra_time
							//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
							//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
							, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																							   'do_action': 'load_form_template',
																							   'step_data':{
																							   		'booking_unavailable_extra_in_out': jQuery( '[name=\"set_calendar_unavailable_before_after_bookings\"]:checked').val(),
																									'booking_unavailable_extra_minutes_out': jQuery(this).val()
																								}
																						 });
											wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );"
						  );
		?><div class="ui_element ui_element_micro"><?php
			wpbc_flex_select( $params_select );
		?></div><?php


		// Set checked specific OPTION depends on last action from  user
		?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
			jQuery( document ).ready( function (){
				// Set selected option  in dropdown list based on  data. value
				jQuery( '#<?php echo esc_attr( $el_id . '_in' ); ?> option[value="' + data.ui.booking_unavailable_extra_minutes_in + '"]' ).prop( 'selected', true );
				jQuery( '#<?php echo esc_attr( $el_id . '_out' ); ?> option[value="' + data.ui.booking_unavailable_extra_minutes_out + '"]' ).prop( 'selected', true );
			} );

		<?php if (0) { ?></script><?php } ?> #><?php
	}

	function wpbc_stp_wiz__ui__cal_availability__unavailable_before_after_bookings_days(){

		$booking_action = 'booking_unavailable_extra_days';
		$el_id = 'ui_btn_cstm__' . $booking_action;

		//  Options
		$extra_time = array();
		$extra_time[''] = ' - ';
		foreach ( range( 1, 30 , 1 ) as $extra_num) {                                           // Each Day
			$extra_time[ $extra_num . 'd' ] = $extra_num . ' ' . __( 'day(s)', 'booking' );
		}


		$params_select = array(
							  'id'       => $el_id  . '_in'				// HTML ID  of element
							, 'name'     => $el_id  . '_in'
							, 'label'    => __( 'Before booking', 'booking' )//'<span class="" style="font-weight:600;">' . esc_html__( 'Days', 'booking' ) . ' <em style="color:#888;">(' . __( 'min-max', 'booking' ) . '):</em></span>'
							, 'style'    => 'max-width: 100%;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $extra_time
							//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
							//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
							, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																							   'do_action': 'load_form_template',
																							   'step_data':{
																							   		'booking_unavailable_extra_in_out': jQuery( '[name=\"set_calendar_unavailable_before_after_bookings\"]:checked').val(),
																									'booking_unavailable_extra_days_in': jQuery(this).val()
																								}
																						 });
											wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );"
						  );
		?><div class="ui_element ui_element_micro"><?php
			wpbc_flex_select( $params_select );
		?></div><?php


		$params_select = array(
							  'id'       => $el_id  . '_out' 				// HTML ID  of element
							, 'name'     => $el_id  . '_out'
							, 'label'    => __( 'After booking', 'booking' )//'<span class="" style="font-weight:600;">' . esc_html__( 'Days', 'booking' ) . ' <em style="color:#888;">(' . __( 'min-max', 'booking' ) . '):</em></span>'
							, 'style'    => 'max-width: 100%;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $extra_time
							//, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
							//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
							, 'onchange' => "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																							   'do_action': 'load_form_template',
																							   'step_data':{
																							   		'booking_unavailable_extra_in_out': jQuery( '[name=\"set_calendar_unavailable_before_after_bookings\"]:checked').val(),
																									'booking_unavailable_extra_days_out': jQuery(this).val()
																								}
																						 });
											wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );"
						  );
		?><div class="ui_element ui_element_micro"><?php
			wpbc_flex_select( $params_select );
		?></div><?php


		// Set checked specific OPTION depends on last action from  user
		?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
			jQuery( document ).ready( function (){
				// Set selected option  in dropdown list based on  data. value
				jQuery( '#<?php echo esc_attr( $el_id . '_in' ); ?> option[value="' + data.ui.booking_unavailable_extra_days_in + '"]' ).prop( 'selected', true );
				jQuery( '#<?php echo esc_attr( $el_id . '_out' ); ?> option[value="' + data.ui.booking_unavailable_extra_days_out + '"]' ).prop( 'selected', true );
			} );

		<?php if (0) { ?></script><?php } ?> #><?php
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Button -  Reset Calendar Availability
	 * @return void
	 */
	function wpbc_stp_wiz__ui__cal_availability__reset__btn(){

		$params  =  array(
						'type'             => 'button' ,
						'title'            => __( 'Reset availability', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
						'hint'             => '',//array( 'title' => __( 'Reset selected options to default values', 'booking' ), 'position' => 'top' ),  	// Hint
						'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
						'action'           =>  "wpbc_ajx__setup_wizard_page__send_request_with_params({   
																						   'do_action': 'load_form_template',
																						   'step_data':{
																								'wpbc_swp_cal_availability__weekdays': '999',
																								'booking_unavailable_days_num_from_today': 0,
																								'booking_available_days_num_from_today':  '',
																								'booking_unavailable_extra_in_out':  '',
																								'booking_unavailable_extra_minutes_in':  '',
																								'booking_unavailable_extra_minutes_out':  '',
																								'booking_unavailable_extra_days_in':  '',
																								'booking_unavailable_extra_days_out':  ''
																							}
																					 });
												wpbc_button_enable_loading_icon( this );
												wpbc_admin_show_message_processing( '' );",																			// JavaScript
						'icon' 			   => array(
													'icon_font' => 'wpbc_icn_close', //'wpbc_icn_rotate_left',  wpbc_icn_settings_backup_restore
													'position'  => 'left',
													'icon_img'  => ''
												),
						'class'            => 'wpbc_ui_button_danger',  																// ''  | 'wpbc_ui_button_primary'
						'style'            => '',																						// Any CSS class here
						'mobile_show_text' => true,																						// Show  or hide text,  when viewing on Mobile devices (small window size).
						'attr'             => array( 'id' => 'btn__status_bar__reset' )
				);

		wpbc_flex_button( $params );
	}


	/**
	 * Widget - Right Side -  "Customize calendar availability"  -- HELP section
	 * @return void
	 */
	function wpbc_stp_wiz__ui__cal_availability__help(){

		// Help message
		?><div class="wpbc-settings-notice notice-warning notice-helpful-info" style="height: auto;font-size: 12px;margin: 0 0 1.5em;">
			<?php
				esc_html_e( 'You can always change this later', 'booking' );
				echo ' - ';
				echo '<a href="'. esc_attr( wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_availability_tab' ).'">Settings > Availability</a>';
			?>
		</div><?php

		wpbc_maybe_show_warning_conflict__wordfence('margin: 20px 0;');
	}


// </editor-fold>


// ---------------------------------------------------------------------------------------------------------------------
//  ==   T e m p l a t e s    UI   ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Upgrade Note with link to  specific feature.
 *
 * @param $version	- 'free' | 'personal' | 'biz_s' | 'biz_m' | 'biz_l' | 'multiuser';			it's from wpbc_get_version_type__and_mu();

 * @param $url		- full URL
 *
 * @return void
 *
 *             Example:		wpbc_ajx_cstm__ui__upgrade_note( 'biz_s', 'https://wpbookingcalendar.com/features/#range-days-selection' );
 */
function wpbc_stp_wiz__ui__upgrade_note( $version, $url ){

	$ver_title = $version;    // wpbc_get_plugin_version_type();
	$ver_title = str_replace( '_m', ' Medium', $ver_title );
	$ver_title = str_replace( '_l', ' Large', $ver_title );
	$ver_title = str_replace( '_s', ' Small', $ver_title );
	$ver_title = str_replace( 'biz', 'Business', $ver_title );
	$ver_title = ucwords( $ver_title );

    ?>
	<div class="ui_group    ui_group__upgrade">
		<div class="wpbc_upgrade_note">
			This <a target="_blank" href="<?php echo esc_url( $url ); ?>">feature</a> requires the
			<a target="_blank" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $ver_title ); ?></a>
			<?php if ( 'multiuser' !== $version ) { ?>
				or higher versions
			<?php } ?>
		</div>
	</div>
	<?php
}
