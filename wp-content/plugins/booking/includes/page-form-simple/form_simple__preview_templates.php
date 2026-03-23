<?php
/**
 * @version 1.0
 * @package     Form Templates
 * @category    WP Booking Calendar > Settings > Booking Form page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-06-02
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// <editor-fold     defaultstate="collapsed"                        desc=" T e m p l a t e s   U n d e r s c o r e"  >

	/**
	 * Templates at footer of page
	 *
	 * @param $page string
	 */
	function wpbc_hook_settings_page_footer_templates_simple_form( $page ){

		if ( ( 'form_field_free_settings' === $page ) || ( 'color_themes_settings' === $page ) ) {

			wpbc_template__form_simple__change_calendar_skin();

			wpbc_template__form_simple__change_time_picker();
		}

	}
	add_action('wpbc_hook_settings_page_footer', 'wpbc_hook_settings_page_footer_templates_simple_form');



		/**
		 * Tpl - Calendar - Skin
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
		function wpbc_template__form_simple__change_calendar_skin(){

		    ?><script type="text/html" id="tmpl-wpbc_ajx_widget_change_calendar_skin">
				<div class="ui_group    ui_group__change_calendar_skin"><?php

					//	Calendar  skin
					?><div class="ui_element ui_nowrap0"><?php
							$booking_action = 'set_calendar_skin';

							$el_id = 'ui_btn_cstm__' . $booking_action ;

							wpbc_flex_label(
												array(
													  'id' 	  => $el_id
													, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Calendar Skin', 'booking' ) . ':</span>'
												)
										   );
					?></div><?php
					?><div class="ui_element ui_nowrap"><?php
						wpbc_smpl_form__ui__calendar_skin_dropdown();
						$is_apply_rotating_icon = false;
						wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
						wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
					?></div><?php

					$upload_dir              = wp_upload_dir();
					$custom_user_skin_folder = $upload_dir['basedir'];
					$custom_user_skin_url    = $upload_dir['baseurl'];

					// Set checked specific OPTION depends on last action from  user
					?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>
						jQuery( document ).ready( function (){
							/* Set INITIAL selected option  in dropdown list based on  data. value 	//FixIn: 10.3.0.5 */
							if (jQuery( '#ui_btn_cstm__set_calendar_skin option[value="<?php echo esc_url( WPBC_PLUGIN_URL ); ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin + '"]' ).length){
								jQuery( '#ui_btn_cstm__set_calendar_skin option[value="<?php echo esc_url( WPBC_PLUGIN_URL ); ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin + '"]' ).prop( 'selected', true );
								wpbc__calendar__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin  );
							}
							/* Set INITIAL selected option  if selected CUSTOM Calendar skin 		//FixIn: 10.3.0.5 */
							if (jQuery( '#ui_btn_cstm__set_calendar_skin option[value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $custom_user_skin_url; ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin + '"]' ).length){
								jQuery( '#ui_btn_cstm__set_calendar_skin option[value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $custom_user_skin_url; ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin + '"]' ).prop( 'selected', true );
								wpbc__calendar__change_skin( '<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo $custom_user_skin_url; ?>' + data.ajx_cleaned_params.customize_plugin__booking_skin  );
							}
							/**
							 * Change calendar skin view
							 */
							jQuery( '.wpbc_radio__set_days_customize_plugin' ).on('change', function ( event, resource_id, inst ){
								wpbc__calendar__change_skin( jQuery( this ).val() );
							});
						} );

					<?php if (0) { ?></script><?php } ?> #><?php

					//	Calendar  Visible Months
					//wpbc_ajx_cstm__ui__template__visible_months();
				?>
				</div>
			</script><?php
		}


		/**
		 * Tpl - Time Picker - Skin
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
		function wpbc_template__form_simple__change_time_picker(){

		    ?><script type="text/html" id="tmpl-wpbc_ajx_widget_change_time_picker">
				<div class="ui_group    ui_group__change_time_picker"><?php

					//	Calendar  skin
					?><div class="ui_element ui_nowrap0"><?php
							$booking_action = 'set_time_picker_skin';

							$el_id = 'ui_btn_cstm__' . $booking_action ;

							wpbc_flex_label(
												array(
													  'id' 	  => $el_id
													, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Time Picker Skin', 'booking' ) . ':</span>'
												)
										   );
					?></div><?php
					?><div class="ui_element ui_nowrap"><?php
						wpbc_smpl_form__ui__time_picker_dropdown();
						$is_apply_rotating_icon = false;
						wpbc_smpl_form__ui__selectbox_prior_btn( $el_id, $is_apply_rotating_icon );
						wpbc_smpl_form__ui__selectbox_next_btn(  $el_id, $is_apply_rotating_icon );
					?></div><?php

					// Set checked specific OPTION depends on last action from  user
					?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>

						jQuery( document ).ready( function (){
							// Set selected option  in dropdown list based on  data. value
							jQuery( '#ui_btn_cstm__set_time_picker_skin option[value="<?php echo esc_url( WPBC_PLUGIN_URL ); ?>' + data.ajx_cleaned_params.customize_plugin__time_picker_skin + '"]' ).prop( 'selected', true );
							wpbc__css__change_skin( '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>' + data.ajx_cleaned_params.customize_plugin__time_picker_skin  , 'wpbc-time_picker-skin-css' );

							/**
							 * Change Time Picker Skin
							 */
							jQuery( '.wpbc_radio__set_time_picker_skin' ).on('change', function ( event, resource_id, inst ){
								wpbc__css__change_skin( jQuery( this ).val() , 'wpbc-time_picker-skin-css' );
							});



						} );

					<?php if (0) { ?></script><?php } ?> #><?php

				?>
				</div>
			</script><?php
		}


// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc=" ==  Calendar Skin UI  == "  >

/**
 * Select-box - Calendar skins
 *
 * @return void
 */
function wpbc_smpl_form__ui__calendar_skin_dropdown(){

		$booking_action = 'set_calendar_skin';

		$el_id = 'ui_btn_cstm__' . $booking_action ;

		//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}


		$transformed_cal_arr = wpbc_get_calendar_skin_options_with_legacy_sections();

		$params_select = array(
							  'id'       => $el_id 				// HTML ID  of element
							, 'name'     => $booking_action
							, 'label' => '' //__( 'Select the skin of the booking calendar', 'booking' )//__('Calendar Skin', 'booking')
							, 'style'    => '' 					// CSS of select element
									, 'class'    => 'wpbc_radio__set_days_customize_plugin' 					// CSS Class of select element
							//, 'multiple' => true
							//, 'attr' => array( 'value_of_selected_option' => '{{selected_locale_value}}' )			// Any additional attributes, if this radio | checkbox element
							, 'disabled' => false
							, 'disabled_options' => array()     								// If some options disabled, then it has to list here
							, 'options' => $transformed_cal_arr
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
}

// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc=" ==  Time Picker  Skin UI  == "  >

/**
 * Select-box - Time Picker
 *
 * @return void
 */
function wpbc_smpl_form__ui__time_picker_dropdown(){

////, 'title'       => __('Time Picker Skin', 'booking')
//, 'description' => __('Select the skin of the time picker' ,'booking')

		$booking_action = 'set_time_picker_skin';
		$el_id = 'ui_btn_cstm__' . $booking_action ;

		//if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) { 	return false; 	}


        //  Calendar Skin  /////////////////////////////////////////////////////
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
}

// </editor-fold>

