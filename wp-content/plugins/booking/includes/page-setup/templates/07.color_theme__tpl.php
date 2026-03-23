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
// == Main - Color Theme  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Template - Color Theme
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
function wpbc_stp_wiz__template__color_theme(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__color_theme">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__color_theme">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 0 auto;flex-flow: column;margin:0 0 10px;">
						<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Calendar Appearance', 'booking' ); ?></h1>
						<p class="wpbc_swp_section_header_description"><?php esc_html_e('Select a color theme for your booking form that matches the look of your website.','booking'); ?></p>
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

				wpbc_stp_wiz__widget__color_theme__skins();

			?>
			</div>
		</div>
		<?php

		wpbc_stp_wiz__ui__color_theme__bottom_buttons();

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
 * Custom bottom Line:   Back | Next   buttons
 *
 * @return void
 */
function wpbc_stp_wiz__ui__color_theme__bottom_buttons(){

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
																												'booking_form_theme': 			jQuery( '[name=\'set_form_color_theme\']').val(),
																												'booking_skin': 				jQuery( '[name=\'set_calendar_skins\'] option:selected').val(),
																												'booking_timeslot_picker_skin': jQuery( '[name=\'set_time_picker_skin\']').val()
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
		.wpbc_ajx_page__container .wpbc_ajx_page__section_footer:not(.wpbc_ajx_page__section_footer__internal){ display: none;}
	</style><?php
}



/**
 * Widget - Right Side -  Set Calendar / TimePicker   Skin  and  Form Color Theme
 *
 * @return void
 */
function wpbc_stp_wiz__widget__color_theme__skins( $params = array() ) {

	$defaults = array(
		'is_use_js_for_templates' => true,
	);
	$params   = wp_parse_args( $params, $defaults );
    ?>
	<div class="wpbc_widget wpbc_widget__color_theme__skins">
		<div class="wpbc_widget_header">
			<span class="wpbc_widget_header_text"><?php esc_html_e('Appearance', 'booking'); ?></span>
			<!--a href="https://wpbookingcalendar.com/features/#availability-from-today" target="_blank" class="wpbc_widget_header_settings_link"><span class="wpbc_pro_label">Pro</span></i></a-->
		</div>
		<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders" style="margin:0 0 0px;">

			<div class="ui_container    ui_container_toolbar		ui_container_small0">
				<div class="ui_group    ui_group__color_theme__today_availability">
				<?php

					wpbc_stp_wiz__ui__color_theme__form_color_theme( $params );

					wpbc_stp_wiz__ui__color_theme__calendar_skins( $params );

					wpbc_stp_wiz__ui__color_theme__timepicker_skins( $params );
				?>
				</div>
			</div>

		</div>
	</div>
	<?php
}



// ---------------------------------------------------------------------------------------------------------------------
//  ==   T e m p l a t e s    UI   ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 *  Calendar Skins  - DropDown List
 */
function wpbc_stp_wiz__ui__color_theme__calendar_skins( $params = array() ){

	$defaults = array(
		'is_use_js_for_templates' => true,
	);
	$params   = wp_parse_args( $params, $defaults );


	$booking_action = 'set_calendar_skins';

	$el_id = 'ui_btn_cstm__' . $booking_action ;

	//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}

	?><div class="ui_element ui_nowrap0"><?php

		wpbc_flex_label( array(  'id' 	  => $el_id
								, 'label' => '<div class="" style="font-weight:400;">'
												. 	__( 'Calendar Skin', 'booking' ) . ': '
											. '</div>'
						) );
	?></div><?php

	?><div class="ui_element ui_nowrap"><?php

		$transformed_cal_arr = wpbc_get_calendar_skin_options_with_legacy_sections();

		$params_select = array(
			'id'               => $el_id,  // HTML ID  of element.
			'name'             => $booking_action,
			'label'            => '',   // __( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking') .
			'style'            => '',   // C S S of select element.
			'class'            => 'wpbc_radio__set_days_customize_plugin',  // CSS Class of select element.
			'disabled'         => false,
			'disabled_options' => array(),  // If some options disabled, then it has to list here.
			'options'          => $transformed_cal_arr,
		);

		wpbc_flex_select( $params_select );

		$is_apply_rotating_icon = false;
		wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
		wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
	?></div><?php

	// Set checked specific OPTION depends on last action from  user
	if ( $params['is_use_js_for_templates'] ) {
		echo '<# ';
		if ( 0 ) { ?><script type="text/javascript"><?php } ?>

		// Set selected option  in dropdown list based on  data. value
		jQuery( document ).ready( function (){

			jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value$="' + data.ui.booking_skin + '"]' ).prop( 'selected', true );

			/**
			 * Change calendar skin view
			 */
			jQuery( '.wpbc_radio__set_days_customize_plugin' ).off('change');
			jQuery( '.wpbc_radio__set_days_customize_plugin' ).on('change', function ( event, resource_id, inst ){
				wpbc__calendar__change_skin( jQuery( this ).val() );
			});


			//wpbc__css__change_skin( '<?php //echo esc_url( WPBC_PLUGIN_URL ); ?>//' + data.ajx_cleaned_params.customize_plugin__time_picker_skin  , 'wpbc-time_picker-skin-css' );
			/**
			 * Change Time Picker Skin
			 */
			// jQuery( '.wpbc_radio__set_time_picker_skin' ).on('change', function ( event, resource_id, inst ){
			// 	wpbc__css__change_skin( jQuery( this ).val() , 'wpbc-time_picker-skin-css' );
			// });
		} );
		<?php echo '#> '; if (0) { ?></script><?php }
	}
}


/**
 *  Time Picker Skins  - DropDown List
 */
function wpbc_stp_wiz__ui__color_theme__timepicker_skins( $params = array() ){

	$defaults = array(
		'is_use_js_for_templates' => true,
	);
	$params   = wp_parse_args( $params, $defaults );


	$booking_action = 'set_time_picker_skin';

	$el_id = 'ui_btn_cstm__' . $booking_action ;

	//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}

	?><div class="ui_element ui_nowrap0"><?php

		wpbc_flex_label( array(  'id' 	  => $el_id
								, 'label' => '<div class="" style="font-weight:400;">'
												. 	__( 'Time Picker Skin', 'booking' ) . ': '
											. '</div>'
						) );
	?></div><?php

	?><div class="ui_element ui_nowrap"><?php



		//  Timepicker Skin  /////////////////////////////////////////////////////
        $time_pickers_options  = array();

        // Skins in the Custom User folder (need to create it manually):    http://example.com/wp-content/uploads/wpbc_skins/ ( This folder do not owerwrited during update of plugin )
        $upload_dir = wp_upload_dir();
	    // FixIn: 8.9.4.8.
		$files_in_folder = wpbc_dir_list( array(  WPBC_PLUGIN_DIR . '/css/time_picker_skins/', $upload_dir['basedir'].'/wpbc_time_picker_skins/' ) );  // Folders where to look about Time Picker skins

        foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';

            //FixIn: 8.9.4.8    // FixIn: 9.1.2.10.
			$skin_file[1] = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL , $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for calendar skin
            $time_pickers_options[ WPBC_PLUGIN_URL . $skin_file[1] ] = $skin_file[2];
        }

		$params_select = array(
							  'id'       => $el_id 				// HTML ID  of element
							, 'name'     => $booking_action
							, 'label' => '' //__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
							, 'style'    => '' 					// CSS of select element
									, 'class'    => 'wpbc_radio__set_time_picker_skin' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $time_pickers_options
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

		$is_apply_rotating_icon = false;
		wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
		wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
	?></div><?php

	// Set checked specific OPTION depends on last action from  user
	if ( $params['is_use_js_for_templates'] ) {
		echo '<# ';
		if ( 0 ) { ?><script type="text/javascript"><?php } ?>

		// Set selected option  in dropdown list based on  data. value
		jQuery( document ).ready( function (){

			jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value$="' + data.ui.booking_timeslot_picker_skin + '"]' ).prop( 'selected', true );

			/**
			 * Change Time Picker Skin
			 */
			jQuery( '.wpbc_radio__set_time_picker_skin' ).off('change');
			jQuery( '.wpbc_radio__set_time_picker_skin' ).on('change', function ( event, resource_id, inst ){
				wpbc__css__change_skin( jQuery( this ).val() , 'wpbc-time_picker-skin-css' );
			});

			// // Set selected option  in dropdown list based on  data. value
			//jQuery( '#ui_btn_cstm__set_time_picker_skin option[value="<?php //echo esc_url( WPBC_PLUGIN_URL ); ?>//' + data.ajx_cleaned_params.customize_plugin__time_picker_skin + '"]' ).prop( 'selected', true );
			//wpbc__css__change_skin( '<?php //echo esc_url( WPBC_PLUGIN_URL ); ?>//' + data.ajx_cleaned_params.customize_plugin__time_picker_skin  , 'wpbc-time_picker-skin-css' );

			/**
			 * Change Time Picker Skin
			 */
			// jQuery( '.wpbc_radio__set_time_picker_skin' ).on('change', function ( event, resource_id, inst ){
			// 	wpbc__css__change_skin( jQuery( this ).val() , 'wpbc-time_picker-skin-css' );
			// });


		} );

		<?php
		echo ' #>';
		if (0) { ?></script><?php }
	}
}


/**
 *  Time Picker Skins  - DropDown List
 */
function wpbc_stp_wiz__ui__color_theme__form_color_theme( $params = array() ){

	$defaults = array(
		'is_use_js_for_templates' => true,
	);
	$params   = wp_parse_args( $params, $defaults );

	$booking_action = 'set_form_color_theme';

	$el_id = 'ui_btn_cstm__' . $booking_action ;

	//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}

	?><div class="ui_element ui_nowrap0"><?php

		wpbc_flex_label( array(  'id' 	  => $el_id
								, 'label' => '<div class="" style="font-weight:400;">'
												. 	__( 'Color Theme', 'booking' ) . ': '
											. '</div>'
						) );
	?></div><?php

	?><div class="ui_element ui_nowrap"><?php



		//  Timepicker Skin  /////////////////////////////////////////////////////
        $time_pickers_options  =array(
									''                  => __( 'Light', 'booking' ),
									'wpbc_theme_dark_1' => __( 'Dark', 'booking' )
								);

		$params_select = array(
							  'id'       => $el_id 				// HTML ID  of element
							, 'name'     => $booking_action
							, 'label' => '' //__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
							, 'style'    => '' 					// CSS of select element
									, 'class'    => 'wpbc_radio__set_form_color_theme' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $time_pickers_options
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

		$is_apply_rotating_icon = false;
		wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
		wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
	?></div><?php

	// Set checked specific OPTION depends on last action from  user
	if ( $params['is_use_js_for_templates'] ) {
		echo '<# ';
		if ( 0 ) { ?><script type="text/javascript"><?php } ?>

		// Set selected option  in dropdown list based on  data. value
		jQuery( document ).ready( function (){

			jQuery( '#<?php echo esc_attr( $el_id ); ?> option[value="' + data.ui.booking_form_theme + '"]' ).prop( 'selected', true );
			jQuery( '.wpbc_widget_preview_booking_form .wpbc_center_preview,.wpbc_widget_preview_booking_form .wpbc_container.wpbc_container_booking_form,.wpbc_widget_preview_booking_form .wpbc_widget_content' ).addClass( jQuery( '#<?php echo esc_attr( $el_id ); ?> option:selected').val() );

			/**
			 * Change Time Picker Skin
			 */
			jQuery( '.wpbc_radio__set_form_color_theme' ).off('change');
			jQuery( '.wpbc_radio__set_form_color_theme' ).on('change', function ( event, resource_id, inst ){
				wpbc_on_change__form_color_theme( this );
			});

			function wpbc_on_change__form_color_theme( _this ){
				var wpbc_cal_dark_skin_path;
				if ( 'wpbc_theme_dark_1' == jQuery( _this ).val() ){
					jQuery( '.wpbc_widget_preview_booking_form .wpbc_center_preview,.wpbc_widget_preview_booking_form .wpbc_container.wpbc_container_booking_form,.wpbc_widget_preview_booking_form .wpbc_widget_content' ).addClass( 'wpbc_theme_dark_1' );


					wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/skins/24_9__dark_1.css';
					jQuery( '#ui_btn_cstm__set_calendar_skins' ).find( 'option' ).prop( 'selected', false );
					jQuery( '#ui_btn_cstm__set_calendar_skins' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
					wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/time_picker_skins/black.css';
					jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option' ).prop( 'selected', false );
					jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
				} else {
					jQuery( '.wpbc_widget_preview_booking_form .wpbc_center_preview,.wpbc_widget_preview_booking_form .wpbc_container.wpbc_container_booking_form,.wpbc_widget_preview_booking_form .wpbc_widget_content' ).removeClass( 'wpbc_theme_dark_1' );
					wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/skins/25_5__square_1.css';
					jQuery( '#ui_btn_cstm__set_calendar_skins' ).find( 'option' ).prop( 'selected', false );
					jQuery( '#ui_btn_cstm__set_calendar_skins' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
					wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/time_picker_skins/light__24_8.css';
					jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option' ).prop( 'selected', false );
					jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
				}
			}

			// // Set selected option  in dropdown list based on  data. value
			//jQuery( '#ui_btn_cstm__set_time_picker_skin option[value="<?php //echo esc_url( WPBC_PLUGIN_URL ); ?>//' + data.ajx_cleaned_params.customize_plugin__time_picker_skin + '"]' ).prop( 'selected', true );
			//wpbc__css__change_skin( '<?php //echo esc_url( WPBC_PLUGIN_URL ); ?>//' + data.ajx_cleaned_params.customize_plugin__time_picker_skin  , 'wpbc-time_picker-skin-css' );

			/**
			 * Change Time Picker Skin
			 */
			// jQuery( '.wpbc_radio__set_time_picker_skin' ).on('change', function ( event, resource_id, inst ){
			// 	wpbc__css__change_skin( jQuery( this ).val() , 'wpbc-time_picker-skin-css' );
			// });
		} );
		<?php
		echo ' #>';
		if (0) { ?></script><?php }
	}
}


