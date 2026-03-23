<?php /**
 * @version 1.1
 * @package Any
 * @category Toolbar. UI Elements for Admin Panel
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-05-07
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly

// ---------------------------------------------------------------------------------------------------------------------
//   T o o l b a r s
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show top toolbar on Booking Listing page
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 */
function wpbc_ajx_bookings_toolbar( $escaped_search_request_params ) {

    wpbc_clear_div();

    //  Toolbar ////////////////////////////////////////////////////////////////

	$default_param_values = wpbc_ajx_get__request_params__names_default( 'default' );


    ?><div id="toolbar_booking_listing" class="wpbc_ajx_toolbar" style="box-shadow: none;"><?php

		// -------------------------------------------------------------------------------------------------------------
		// Filters
		// -------------------------------------------------------------------------------------------------------------
		?><div><?php //Required for bottom border radius in container

			?><div class="ui_container    ui_container_toolbar		ui_container_filters    ui_container_filter_row_1" style="display: flex;background: transparent;padding: 6px 0;border: 0;"><?php

				?><div class="ui_group    ui_group__dates_status    ui_search_fields_group_1"><?php  						//	array( 'class' => 'group_nowrap' )	// Elements at Several or One Line

					wpbc_ajx__ui__booked_dates( $escaped_search_request_params, $default_param_values );

					wpbc_ajx__ui__booking_status( $escaped_search_request_params, $default_param_values );

				?></div><?php

				?><div class="ui_group    ui_group__keyword    ui_search_fields_group_2"><?php  							//	array( 'class' => 'group_nowrap' )	// Elements at Several or One Line

					wpbc_ajx__ui__booking_resources( $escaped_search_request_params, $default_param_values );

					wpbc_ajx_toolbar_keyword_search( $escaped_search_request_params, $default_param_values );

					wpbc_ajx_toolbar_clear_keyword_search( $escaped_search_request_params, $default_param_values );		// FixIn: 9.7.2.3.

					wpbc_ajx_toolbar_force_reload_button( $escaped_search_request_params, $default_param_values );

				?></div><?php

			?></div><?php


			?><div class="ui_container    ui_container_toolbar		ui_container_small    ui_container_filters    ui_container_filter_row_2" style="display: flex;background: transparent;padding: 6px 0;border: 0;"><?php

				?><div class="ui_group    ui_group__statuses    ui_search_fields_group_1"><?php  					//	array( 'class' => 'group_nowrap' )	// Elements at Several or One Line



					wpbc_ajx__ui__existing_or_trash( $escaped_search_request_params, $default_param_values );

					wpbc_ajx__ui__all_or_new( $escaped_search_request_params, $default_param_values );

					wpbc_ajx__ui__creation_date( $escaped_search_request_params, $default_param_values );

					wpbc_ajx__ui__payment_status( $escaped_search_request_params, $default_param_values );

					wpbc_ajx__ui__cost_min_max( $escaped_search_request_params, $default_param_values );

					wpbc_ajx_toolbar_reset_button( $escaped_search_request_params, $default_param_values );

				?></div><?php

			?></div><?php

		?></div><?php //Required for bottom border radius in container


	?></div><?php
}



// <editor-fold     defaultstate="collapsed"                        desc=" T o o l b a r       F i l t e r       B u t t o n s "  >

// ---------------------------------------------------------------------------------------------------------------------
/// 1st row
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Booked dates
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__booked_dates( $escaped_search_request_params, $defaults ){

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_event', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'hint' 	=> array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
						, 'attr'        => array()
					);

    $dates_interval = array(
                                1 => '1' . ' ' . __('day' ,'booking')
                              , 2 => '2' . ' ' . __('days' ,'booking')
                              , 3 => '3' . ' ' . __('days' ,'booking')
                              , 4 => '4' . ' ' . __('days' ,'booking')
                              , 5 => '5' . ' ' . __('days' ,'booking')
                              , 6 => '6' . ' ' . __('days' ,'booking')
                              , 7 => '1' . ' ' . __('week' ,'booking')
                              , 14 => '2' . ' ' . __('weeks' ,'booking')
                              , 30 => '1' . ' ' . __('month' ,'booking')
                              , 60 => '2' . ' ' . __('months' ,'booking')
                              , 90 => '3' . ' ' . __('months' ,'booking')
                              , 183 => '6' . ' ' . __('months' ,'booking')
                              , 365 => '1' . ' ' . __('Year' ,'booking')
                        );

	$request_input_el_default = array(
		'wh_booking_date'             => isset( $escaped_search_request_params['wh_booking_date'] ) ? $escaped_search_request_params['wh_booking_date'] : $defaults['wh_booking_date'],
		'ui_wh_booking_date_radio'    => isset( $escaped_search_request_params['ui_wh_booking_date_radio'] ) ? $escaped_search_request_params['ui_wh_booking_date_radio'] : $defaults['ui_wh_booking_date_radio'],
		'ui_wh_booking_date_next'     => isset( $escaped_search_request_params['ui_wh_booking_date_next'] ) ? $escaped_search_request_params['ui_wh_booking_date_next'] : $defaults['ui_wh_booking_date_next'],
		'ui_wh_booking_date_prior'    => isset( $escaped_search_request_params['ui_wh_booking_date_prior'] ) ? $escaped_search_request_params['ui_wh_booking_date_prior'] : $defaults['ui_wh_booking_date_prior'],
		'ui_wh_booking_date_checkin'  => isset( $escaped_search_request_params['ui_wh_booking_date_checkin'] ) ? $escaped_search_request_params['ui_wh_booking_date_checkin'] : $defaults['ui_wh_booking_date_checkin'],
		'ui_wh_booking_date_checkout' => isset( $escaped_search_request_params['ui_wh_booking_date_checkout'] ) ? $escaped_search_request_params['ui_wh_booking_date_checkout'] : $defaults['ui_wh_booking_date_checkout']
	);

	$options = array (
						// 'header2'   => array( 'type' => 'header', 'title' => __( 'Complex Days', 'booking' ) ),
						// 'disabled1' => array( 'type' => 'simple', 'value' => '19', 'title' => __( 'This is option was disabled', 'booking' ), 'disabled' => true ),
						//todo: 2023-06-10
					    'fixed' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ), 7 => array( 'value' ) ),												//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ), 'text2' => ' - ' ,7 => array( 'value' ) ),	//  1 => array( 'label', 'title' )  -->   $complex_option['input_options'][1]['label'][ 'title' ]
								'style' => 'min-width:290px',
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex:1 1 100%;margin-top:5px;display:none;">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_booking_date_radio_3' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Dates' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '6' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '6' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 4px 4px 0;flex: 0 1 90px;order: 2;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkin' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkin'
														, 'label'    => ''//__('Check-in' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-in' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkin'] 	// Some Value from optins array that selected by default
//														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 0 4px 4px;flex: 0 1 90px;order: 4;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkout' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkout'
														, 'label'    => ''//__('Check-out' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-out' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkout']  		// Some Value from optins array that selected by default
//														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													// FixIn: 9.7.2.2.
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding:0;flex: 0 1 14px;order: 2;">' )
													, array(
															'type' => 'button'
														  , 'title' => ''					  // Title of the button
														  , 'hint' => ''
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "jQuery('#ui_wh_booking_date_checkin').val('');jQuery('#ui_wh_booking_date_checkout').val('');"
														  , 'class' => 'wpbc_button_as_icon'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => array( 'icon_font' => 'wpbc_icn_horizontal_rule', 'position' => 'left', 'icon_img' => '' )
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => 'padding: 0px;font-size: 20px;color: #555;'                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )

													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 0 4px 4px;flex: 0 1 35px;order: 5;">' )
													, array(
															'type' => 'button'
														  , 'title' => ''					  // Title of the button
														  , 'hint' => array( 'title' => __('Apply' ,'booking') , 'position' => 'top' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);".
																		"wpbc_ui_dropdown_apply_click( { 
														  												'dropdown_id'		 : 'wh_booking_date', 
														  												'dropdown_radio_name': 'ui_wh_booking_date_radio' 
																									} );"				// JavaScript code
														  , 'class' => 'wpbc_ui_button_primary'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => array( 'icon_font' => 'wpbc_icn_arrow_forward', 'position' => 'left', 'icon_img' => '' )
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => 'padding:5px 7px;'                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px;flex: 0 1 20px;margin-left: auto;margin-right:-5px;order: 6;">' )
													, array(
															'type' => 'button'
														  , 'title' => ''					  // Title of the button
														  , 'hint' => array( 'title' => __('Reset check in/out dates filter' ,'booking') , 'position' => 'top' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' =>"if ( ( jQuery('#ui_wh_booking_date_checkin').val() == '') && ( jQuery('#ui_wh_booking_date_checkout').val() == '') ) { wpbc_field_highlight( '#ui_wh_booking_date_checkin' ); wpbc_field_highlight( '#ui_wh_booking_date_checkout' );  return false; }"
															 		 . "jQuery('#ui_wh_booking_date_checkin').val('');jQuery('#ui_wh_booking_date_checkout').val('');"
														  , 'class' => 'wpbc_button_as_icon'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => array( 'icon_font' => 'wpbc_icn_close', 'position' => 'left', 'icon_img' => '' )
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => 'padding-left: 0;padding-right: 0;'                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )

											)
						),
						'divider4' => array( 'type' => 'html', 'html' => '<hr/>' ),


						'0' => __( 'Current dates', 'booking' ),
						'1' => __( 'Today', 'booking' ),
						'2' => __( 'Previous dates', 'booking' ),
						'3' => __( 'All dates', 'booking' ),

						'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),

						'9' => __( 'Today check in/out', 'booking' ),
						'10' => __( 'Check in - Today', 'booking' ),
						'11' => __( 'Check out - Today', 'booking' ),
						'7' => __( 'Check in - Tomorrow', 'booking' ),
						'8' => __( 'Check out - Tomorrow', 'booking' ),

						'divider2' => array( 'type' => 'html', 'html' => '<hr/>' ),

						// Next  [ '4', '10' ]		- radio button (if selected)  value '4'    and select-box with  selected value   '10'
					    'next' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								// recheck if LI selected: 	 $options['next']['selected_options_value'] == $params['default],  e.g. ~  [ '4', '10' ]
								'selected_options_value' => array(
																	1 => array( 'value' ),					//  $options['next']['input_options'][ 1 ]['value']				'4'
																	4 => array( 'value' ) 					//  $options['next']['input_options'][ 4 ]['value']				'10'
																),
								// Get selected Title, for dropdown if $options['next'] selected
								'selected_options_title' => array(
																	1 => array( 'label', 'title' ),			// $options['next']['input_options'][ 1 ]['label'][ 'title' ]		'Next'
																	'text1' => ': ',						// if key 'text1' not exist in ['input_options'], then it text	': '
																	4 => array( 'options', $request_input_el_default['ui_wh_booking_date_next'] )					// 	'10 days'
																),
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element">' )
													, array(	 														// Default options from simple input element, like: wpbc_flex_radio()
														  'type' => 'radio'
														, 'id'       => 'ui_wh_booking_date_radio_1' 					// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Next' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 												// CSS of select element
														, 'class'    => '' 												// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 										// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''												// aria-label parameter
														, 'value'    => '4'
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '4' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element">' )
													, array(
															'type' => 'select'
														  , 'attr' => array()
														  , 'name' => 'ui_wh_booking_date_next'
														  , 'id' => 'ui_wh_booking_date_next'
														  , 'options' => $dates_interval
														  , 'value' => $request_input_el_default[ 'ui_wh_booking_date_next']
														  , 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_1').prop('checked', true);"									// JavaScript code
														  //, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),

						// Prior  [ '5', '10' ]
						'prior' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'min-width: 244px;',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ) ),		//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array(    1 => array( 'label', 'title' )
																	, 'text1' => ': '
																	, 4 => array( 'options' , $request_input_el_default[ 'ui_wh_booking_date_prior'] )
																 ), 													//  1 => array( 'label', 'title' )  --> $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_booking_date_radio_2' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Prior' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '5' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '5' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element">' )
													, array(
															'type' => 'select'
														  , 'attr' => array()
														  , 'name' => 'ui_wh_booking_date_prior'
														  , 'id' => 'ui_wh_booking_date_prior'
														  , 'options' => $dates_interval
														  , 'value' => $request_input_el_default[ 'ui_wh_booking_date_prior']
														  , 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_2').prop('checked', true);"					// JavaScript code
														  //, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
							),
/* //todo: 2023-06-10
						// Fixed [ '6', '', '2022-05-21']
					    'fixed' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ), 7 => array( 'value' ) ),												//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ), 'text2' => ' - ' ,7 => array( 'value' ) ),	//  1 => array( 'label', 'title' )  -->   $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex:1 1 100%;margin-top:5px;">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_booking_date_radio_3' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Dates' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '6' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '6' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 4px 4px 0;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkin' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkin'
														, 'label'    => ''//__('Check-in' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-in' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkin'] 	// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 0 4px 4px;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkout' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkout'
														, 'label'    => ''//__('Check-out' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-out' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkout']  		// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),
*/
						'divider3' => array( 'type' => 'html', 'html' => '<hr/>' ),

					 	// Buttons
					    'buttons1' =>  array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'justify-content: flex-end;',
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Apply', 'booking' )                     										// Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_apply_click( { 
														  												'dropdown_id'		 : 'wh_booking_date', 
														  												'dropdown_radio_name': 'ui_wh_booking_date_radio' 
																									} );"				// JavaScript code
														  , 'class' => 'wpbc_ui_button_primary'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0 0 0 1em;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Close', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_close_click( 'wh_booking_date' );"                    // JavaScript code
														  , 'class' => ''     				  // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),
				);

    $params = array(
					  'id'      => 'wh_booking_date'
					, 'default' => $request_input_el_default[ 'wh_booking_date' ]
                    , 'label' 	=> ''//__('Approve 1', 'booking') . ':'
                    , 'title' 	=> ''//__('Approve 2', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
					, 'align' 	=> 'left'
					, 'li_options' => $options
                );

	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 * Approved | Pending | All
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__booking_status( $escaped_search_request_params, $defaults ){

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_done_all', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
					);

    $params = array(
					  'id'      => 'wh_approved'
					, 'default' => isset( $escaped_search_request_params['wh_approved'] ) ? $escaped_search_request_params['wh_approved'] : $defaults['wh_approved']
                    , 'label' 	=> ''//__('Status', 'booking') . ':'
                    , 'title' 	=> __('Status', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by booking status' ,'booking') , 'position' => 'top' )
					, 'li_options' => array (
											'0' => __( 'Pending', 'booking' ),
											'1' => __( 'Approved', 'booking' ),
											'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),
											// 'header1' => array( 'type' => 'header', 'title' => __( 'Default', 'booking' ) ),
											'any' => array(
														'type'     => 'simple',
														'value'    => '',
														// 'disabled' => true,
														'title'    => __( 'Any', 'booking' )
											),
									 )
                );

	?><div class="ui_element ui_nowrap"><?php

		//wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 * Keywords
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
*/
function wpbc_ajx_toolbar_keyword_search( $escaped_search_request_params, $defaults ){

	$el_id = 'wpbc_search_field';

	$default_value = '';

	// Old way of searching booking ID
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( ! empty( $_REQUEST['wh_booking_id'] ) ) {
		$wh_booking_id = intval( $_REQUEST['wh_booking_id'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( $wh_booking_id > 0 ) {
			$_REQUEST['overwrite'] = 1;
			$_REQUEST['keyword']   = 'id:' . $wh_booking_id;
		}
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( ( ! empty( $_REQUEST['overwrite'] ) ) && ( ! empty( $_REQUEST['keyword'] ) ) ) {

		// Searching for booking(s)  from URL: http://beta/wp-admin/admin.php?page=wpbc&tab=vm_booking_listing&keyword=id:2&overwrite=1 .

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$default_value = wpbc_sanitize_text( $_REQUEST['keyword'] );

		?><script type="text/javascript">
			jQuery( document ).ready( function (){
				// setTimeout( function () {
				// 	wpbc_ajx_booking_listing.search_set_param( 'wh_booking_type', [0] );
				// }, 950 );
				wpbc_ajx_booking_searching_after_few_seconds( '#<?php echo esc_attr( $el_id ); ?>', 1000 ); 							// Immediate search after 0.5 second
			} );
		</script><?php
	}

	$params = array(
		'type'        => 'text',
		'id'          => $el_id,
		'name'        => $el_id,
		'label'       => '',
		'disabled'    => false,
		'class'       => '',
		'style'       => '',
		'placeholder' => __( 'Enter keyword to search...', 'booking' ),
		'attr'        => array(),
		'value'       => $default_value,
		'onfocus'     => '',
	);
	?><div class="ui_element"><?php
		wpbc_flex_text( $params );
	?></div><?php
}


// FixIn: 9.7.2.3.
function wpbc_ajx_toolbar_clear_keyword_search( $escaped_search_request_params, $defaults ){

    $params  =  array(
	    'type'             => 'button' ,
	    'title'            => '',//__( 'Reset', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
	    'hint'             => array( 'title' => __( 'Reset keyword text field', 'booking' ), 'position' => 'top' ),  	// Hint
	    'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
	    'action'           => "jQuery( '#wpbc_search_field').val(''); 
							   wpbc_ajx_booking_send_search_request_with_params( {
										'keyword'  : '',
										'page_num': 1
									} );
							       ",																					// JavaScript
	    'icon' 			   => array(
									'icon_font' => 'wpbc_icn_close', //'wpbc_icn_rotate_left',
									'position'  => 'left',
									'icon_img'  => ''
								),
	    'class'            => 'wpbc_button_as_icon',  																	// ''  | 'wpbc_ui_button_primary'
	    'style'            => '', 																						// Any CSS class here
	    'mobile_show_text' => true,																						// Show  or hide text,  when viewing on Mobile devices (small window size).
	    'attr'             => array()
	);

	?><div class="ui_element" style="flex: 0 1 auto;margin-left: -50px;z-index: 1;"><?php
		wpbc_flex_button( $params );
	?></div><?php
}

/**
 * Reset button - init default filter options
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx_toolbar_reset_button( $escaped_search_request_params, $defaults ){

    $params  =  array(
	    'type'             => 'button' ,
	    'title'            => __( 'Reset', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
	    'hint'             => array( 'title' => __( 'Reset search filter and user options to default values', 'booking' ) . '. ' . __( 'Show all bookings.', 'booking' ), 'position' => 'top' ),  	// Hint
	    'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
	    'action'           => "wpbc_ajx_booking_send_search_request_with_params( {
															'ui_reset': 'make_reset',
															'page_num': 1
										} );",																			// JavaScript
	    'icon' 			   => array(
									'icon_font' => 'wpbc_icn_settings_backup_restore', //'wpbc_icn_rotate_left',
									'position'  => 'left',
									'icon_img'  => ''
								),
	    'class'            => 'wpbc_ui_button',  																		// ''  | 'wpbc_ui_button_primary'
	    'style'            => '',// 'color: #ef6500;', 						//FixIn: 9.7.2.1								// Any CSS class here
	    'mobile_show_text' => !true,																						// Show  or hide text,  when viewing on Mobile devices (small window size).
	    'attr'             => array()
	);

	?><div class="ui_element" style="margin-left: auto;"><?php
		wpbc_flex_button( $params );
	?></div><?php
}

// ---------------------------------------------------------------------------------------------------------------------
/// 2nd row
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Booking resources
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__booking_resources( $escaped_search_request_params, $defaults ){

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		return false;
	}

	wpbc_bo_listing__resource_choosen( $escaped_search_request_params, $defaults );

	return; //TODO: 2025-04-29 relpace it.

	$params_button = array(
						'type'             => 'button' ,
						'title'            => '',//__( 'Reset', 'booking' ) . '&nbsp;&nbsp;',  							// Title of the button
						'hint'             => array( 'title' => __( 'Remove all booking resources', 'booking' ), 'position' => 'top' ),  	// Hint
						'link'             => 'javascript:void(0)',  													// Direct link or skip  it
						'action'           => "remove_all_options_from_choozen('#wh_booking_type');",					// JavaScript
						'icon' 			   => array(
													'icon_font' => 'wpbc_icn_close',
													'position'  => 'left',
													'icon_img'  => ''
												),
						'class'            => 'wpbc_ui_button',  														// ''  | 'wpbc_ui_button_primary'
						'style'            => '',																		// Any CSS class here
						'mobile_show_text' => true,																		// Show  or hide text,  when viewing on Mobile devices (small window size).
						'attr'             => array( 'id' => 'wpbc_booking_listing_reload_button')
					);

	/**
	 * result = {array} [12]
							 1 = {array} [10]
												  booking_type_id = "1"
												  title = "Standard"
												  users = "3"
												  import = null
												  export = null
												  cost = "25"
												  default_form = "owner-custom-form-1"
												  prioritet = "2"
												  parent = "0"
												  visitors = "2"
							 2 = {array} [10]
												  booking_type_id = "2"
												  title = "Apartment#1"   ...
	 */
	$resources_sql_arr = wpbc_ajx_get_all_booking_resources_arr();

	/**
	 * $resources_arr = array( 			 linear_resources = {array} [12] 			single_or_parent = {array} [5]				child = {array} [2]  )
	 *
		$resources_arr = {array} [3]
										 linear_resources = {array} [12]
																			  1 = {array} [12]
																							   booking_type_id = "1"
																							   title = "Standard"
																							   users = "3"
																							   import = null
																							   export = null
																							   cost = "25"
																							   default_form = "owner-custom-form-1"
																							   prioritet = "2"
																							   parent = "0"
																							   visitors = "2"
																							   id = "1"
																							   count = {int} 5
																			  5 = {array} [12]
																							   booking_type_id = "5"
																							   title = "Standard-1"
																							   users = "1"
																							   import = null
	 */
	$resources_arr     = wpbc_ajx_arrange_booking_resources_arr( $resources_sql_arr );
	$style = '';
	$select_box_options = array();            // FixIn: 4.3.2.1.
	if ( ! empty( $resources_arr ) ) {

		$linear_resources_arr = $resources_arr['linear_resources'];



		if ( count( $linear_resources_arr ) > 1 ) {

			$resources_id_arr   = array();
			foreach ( $linear_resources_arr as $bkr ) {
				$resources_id_arr[] = $bkr['id'];
			}

			$select_box_options[ /*implode( ',', $resources_id_arr )*/0 ] = array(
																			  'title' => __( 'All resources', 'booking' )
																			, 'attr'  => array( 'title' => '<strong>' . esc_html__( 'All resources', 'booking' ) . '</strong>' )
																			, 'style' => 'font-weight:600;'
																		);
		}

		foreach ( $linear_resources_arr as $bkr ) {

			$option_title = wpbc_lang( $bkr['title'] );

			if ( isset( $bkr['parent'] ) ) {
				if ( $bkr ['parent'] == 0 ) {
					$option_title = $option_title;
					$style = 'font-weight:600;';
				} else {
					$option_title = '&nbsp;&nbsp;&nbsp;' . $option_title;
					$style = 'font-weight:400;';
				}
			}
			$select_box_options[ $bkr ['id'] ] = array(
															  'title' => $option_title
															, 'attr'  => array( 'title' => $option_title )
															, 'style' => $style
														);
		}
	}



	$el_id = 'wh_booking_type';
 	$params_select = array(
						  'id'       => $el_id 												// HTML ID  of element
						, 'name'     => $el_id
		 				, 'label' => '' 	// __( 'Next Days', 'booking' )					// Label (optional)
						, 'style' => 'display:none;'                     								// CSS of select element
						, 'class' => 'chzn-select'                     								// CSS Class of select element
						, 'multiple' => true
    					, 'attr' => array( 'data-placeholder' => __( 'Select booking resources', 'booking' ) )			// Any additional attributes, if this radio | checkbox element
						, 'disabled' => false
		 				, 'disabled_options' => array()     								// If some options disabled, then it has to list here
						, 'options' => $select_box_options

						, 'value' => isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ]  : $defaults[ $el_id ]		// Some Value from options array that selected by default
						//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"							// JavaScript code
						//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"						// JavaScript code

					  );

	 // Booking resources
 	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_select( $params_select );

		wpbc_flex_button( $params_button );

	?></div><?php

	?><script type="text/javascript">
            function remove_all_options_from_choozen( selectbox_id ){
				jQuery( selectbox_id + ' option' ).prop( 'selected', false );    										// Disable selection in the real selectbox
				jQuery( selectbox_id ).trigger( 'chosen:updated' );            											// Remove all fields from the Choozen field	// FixIn: 8.7.9.9.
				jQuery( selectbox_id ).trigger( 'change' );
            }
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpbc_jq_ready_start();
			?>
			if ( 'function' === typeof( jQuery("#<?php echo esc_attr( $el_id ); ?>").chosen ) ) {

				jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).chosen( {no_results_text: "No results matched"} );

				jQuery("#<?php echo esc_attr( $el_id ); ?>").chosen().on('change', function(va){									// Catch any selections in the Choozen

					if ( jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).val() != null ){
						//So we are having aready values
						jQuery.each( jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).val(), function ( index, value ){

							if ( (value.indexOf( ',' ) > 0) || ('0' === value) ){ 															// Ok we are have array with  all booking resources ID

								// Disable selection in the real selectbox
								jQuery( '#<?php echo esc_attr( $el_id ); ?>' + ' option' ).removeAttr( 'selected' );

								// Select "All resources" option in real selectbox
								jQuery( '#<?php echo esc_attr( $el_id ); ?>' + ' option:first-child' ).prop( "selected", true );

								//Highlight options in chosen, before removing
								jQuery('#<?php echo esc_attr( $el_id ); ?>_chosen li.search-choice:not(:contains(' + '<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo html_entity_decode( esc_js( __( 'All resources', 'booking' ) ) ); ?>' + '))')
									.fadeOut(350).fadeIn(300)
									.fadeOut(350).fadeIn(400)
									.fadeOut(350).fadeIn(300)
									.fadeOut(350).fadeIn(400)
									.animate({opacity: 1}, 4000);

								// Update chosen LI choices, relative selected options in selectbox
								var all_resources_timer = setTimeout( function (){

									jQuery( '#<?php echo esc_attr( $el_id ); ?>' ).trigger( 'chosen:updated' );            			// Remove all fields from the Choozen field
								}, 2000 );

								var my_message = '<?php echo esc_js( __( 'Please note, its not possible to add new resources, if "All resources" option is selected. Please clear the selection, then add new resources.', 'booking' )  ); ?>';
								wpbc_admin_show_message( my_message, 'warning', 10000 );
							}
						} );
					}
				});

			} else {
				alert( 'WPBC Error. JavaScript library "chosen" was not defined.' );
			}
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpbc_jq_ready_end();
			?>
        </script><?php
}

/**
 * Existing | Trash | Any
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__existing_or_trash( $escaped_search_request_params, $defaults ){

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_delete_outline', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
						, 'hint' 	=> array( 'title' => __('Show trashed or existing bookings' ,'booking') , 'position' => 'top' )
					);

	$el_id = 'wh_trash';
	$params = array(
					  'id'      => $el_id
					, 'default' => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
					, 'label' 	=> ''
					, 'title' 	=> ''	//__('Bookings', 'booking')
					, 'hint' 	=> array( 'title' => __('Show trashed or existing bookings' ,'booking') , 'position' => 'top' )
					, 'li_options' => array(
										  '0'        => __( 'Existing', 'booking' ),
										  'trash'    => __( 'In Trash / Rejected', 'booking' ),
										  'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),
										  'any'      => __( 'Any', 'booking' )
									  )
					//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( '#{$el_id}' ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
					//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() 		, 'in element:' , jQuery( this ) );"					// JavaScript code
					//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( { '{$el_id}': JSON.parse( jQuery( this ).val() )[0], 'page_num': 1 } );"
				);

	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 *  All bookings | New bookings	| Imported bookings	| Plugin bookings
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__all_or_new( $escaped_search_request_params, $defaults ){

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_visibility', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
						, 'hint' 	=> array( 'title' => __('Filter bookings by additional criteria' ,'booking') , 'position' => 'top' )
					);

		$el_id = 'wh_what_bookings';
		$params = array(
						  'id'      => $el_id
						, 'default' => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
						, 'label' 	=> ''
						, 'title' 	=> '' //__('Show', 'booking')
						, 'hint' 	=> array( 'title' => __('Filter bookings by additional criteria' ,'booking') , 'position' => 'top' )
						, 'li_options' => array(
											  'all' => array(
												  'type'  => 'simple',
												  'value' => 'any',
												  'title' => __( 'Any', 'booking' )
											  ),
											  'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),
											  'new'        	=> __( 'New', 'booking' ),
											  'imported' 	=> __( 'Imported', 'booking' ),
											  'in_plugin'   => __( 'Plugin bookings', 'booking' )
										  )
						//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( '#{$el_id}' ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
						//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() 		, 'in element:' , jQuery( this ) );"					// JavaScript code
						//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( { '{$el_id}': JSON.parse( jQuery( this ).val() )[0], 'page_num': 1 } );"
					);


	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 *  "Creation Date"   of bookings
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__creation_date( $escaped_search_request_params, $defaults ){

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_edit_calendar', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
						, 'hint' 	=> array( 'title' => __('Filter bookings by creation booking date' ,'booking') , 'position' => 'top' )
					);

	$dates_interval = array(
								1 => '1' . ' ' . __('day' ,'booking')
							  , 2 => '2' . ' ' . __('days' ,'booking')
							  , 3 => '3' . ' ' . __('days' ,'booking')
							  , 4 => '4' . ' ' . __('days' ,'booking')
							  , 5 => '5' . ' ' . __('days' ,'booking')
							  , 6 => '6' . ' ' . __('days' ,'booking')
							  , 7 => '1' . ' ' . __('week' ,'booking')
							  , 14 => '2' . ' ' . __('weeks' ,'booking')
							  , 30 => '1' . ' ' . __('month' ,'booking')
							  , 60 => '2' . ' ' . __('months' ,'booking')
							  , 90 => '3' . ' ' . __('months' ,'booking')
							  , 183 => '6' . ' ' . __('months' ,'booking')
							  , 365 => '1' . ' ' . __('Year' ,'booking')
						);

	$el_id = 'wh_modification_date';

	$request_input_el_default = array(
		$el_id             			  	   => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ],
		'ui_wh_modification_date_radio'    => isset( $escaped_search_request_params['ui_wh_modification_date_radio'] ) ? $escaped_search_request_params['ui_wh_modification_date_radio'] : $defaults['ui_wh_modification_date_radio'],
		'ui_wh_modification_date_prior'    => isset( $escaped_search_request_params['ui_wh_modification_date_prior'] ) ? $escaped_search_request_params['ui_wh_modification_date_prior'] : $defaults['ui_wh_modification_date_prior'],
		'ui_wh_modification_date_checkin'  => isset( $escaped_search_request_params['ui_wh_modification_date_checkin'] ) ? $escaped_search_request_params['ui_wh_modification_date_checkin'] : $defaults['ui_wh_modification_date_checkin'],
		'ui_wh_modification_date_checkout' => isset( $escaped_search_request_params['ui_wh_modification_date_checkout'] ) ? $escaped_search_request_params['ui_wh_modification_date_checkout'] : $defaults['ui_wh_modification_date_checkout']
	);

	$options = array (
						'1' => __( 'Today', 'booking' ),
						'3' => __( 'All dates', 'booking' ),

						'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),

						// Prior  [ '5', '10' ]
						'prior' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'min-width: 244px;',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ) ),		//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array(    1 => array( 'label', 'title' )
																	, 'text1' => ': '
																	, 4 => array( 'options' , $request_input_el_default[ 'ui_wh_modification_date_prior'] )
																 ), 													//  1 => array( 'label', 'title' )  --> $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_modification_date_radio_2' 		// HTML ID  of element
														, 'name'     => 'ui_wh_modification_date_radio'
														, 'label'    => array( 'title' => __('Prior' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '5' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_modification_date_radio' ] == '5' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element">' )
													, array(
															'type' => 'select'
														  , 'attr' => array()
														  , 'name' => 'ui_wh_modification_date_prior'
														  , 'id' => 'ui_wh_modification_date_prior'
														  , 'options' => $dates_interval
														  , 'value' => $request_input_el_default[ 'ui_wh_modification_date_prior']
														  , 'onfocus' =>  "jQuery('#ui_wh_modification_date_radio_2').prop('checked', true);"					// JavaScript code
														  //, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
							),

						// Fixed [ '6', '', '2022-05-21']
						'fixed' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ), 7 => array( 'value' ) ),												//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ), 'text2' => ' - ' ,7 => array( 'value' ) ),	//  1 => array( 'label', 'title' )  -->   $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex:1 1 100%;margin-top:5px;">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_modification_date_radio_3' 		// HTML ID  of element
														, 'name'     => 'ui_wh_modification_date_radio'
														, 'label'    => array( 'title' => __('Dates' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '6' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_modification_date_radio' ] == '6' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 4px 4px 0;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_modification_date_checkin' 		// HTML ID  of element
														, 'name'     => 'ui_wh_modification_date_checkin'
														, 'label'    => ''//__('Check-in' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('From' ,'booking')	// gmdate( 'Y-m-d' )
														, 'value'    => $request_input_el_default[ 'ui_wh_modification_date_checkin'] 	// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_modification_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 0 4px 4px;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_modification_date_checkout' 		// HTML ID  of element
														, 'name'     => 'ui_wh_modification_date_checkout'
														, 'label'    => ''//__('Check-out' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('To' ,'booking')		// gmdate( 'Y-m-d' )
														, 'value'    => $request_input_el_default[ 'ui_wh_modification_date_checkout']  		// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_modification_date_radio_3').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),

						'divider3' => array( 	'type'  => 'html', 		'html' => '<hr/>' ),

						// Buttons
						'buttons1' =>  array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'justify-content: flex-end;',
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Apply', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_apply_click( { 
																									'dropdown_id'		 : 'wh_modification_date', 
																									'dropdown_radio_name': 'ui_wh_modification_date_radio' 
																								} );"				// JavaScript code
														  , 'class' => 'wpbc_ui_button_primary'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0 0 0 1em;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Close', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_close_click( 'wh_modification_date' );"				// JavaScript code
														  , 'class' => ''     				  // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),
				);

	$params = array(
					  'id'      => $el_id
					, 'default' => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
					, 'label' 	=> ''
					, 'title' 	=> ''//__('Creation', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by creation booking date' ,'booking') , 'position' => 'top' )
					, 'li_options' => $options
					//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( '#{$el_id}' ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
					//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() 		, 'in element:' , jQuery( this ) );"					// JavaScript code
					//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( { '{$el_id}': JSON.parse( jQuery( this ).val() ), 'page_num': 1 "
					//						// Frontend selected elements (saving for future use, after F5)
					//						. " ,'ui_wh_modification_date_radio'   : jQuery( 'input[name=\"ui_wh_modification_date_radio\"]:checked' ).val()"
					//						. " ,'ui_wh_modification_date_prior'   : jQuery( '#ui_wh_modification_date_prior' ).val()"
					//						. " ,'ui_wh_modification_date_checkin' : jQuery( '#ui_wh_modification_date_checkin' ).val()"
					//						. " ,'ui_wh_modification_date_checkout': jQuery( '#ui_wh_modification_date_checkout' ).val()"
					//				."} );"
				);

	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 *  Payment Status
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__payment_status( $escaped_search_request_params, $defaults ){

	if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
		return false;
	}

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_payments', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
						, 'hint' 	=> array( 'title' => __('Filter bookings by payment status' ,'booking') , 'position' => 'top' )
					);

	$el_id = 'wh_pay_status';

	$request_input_el_default = array(
		$el_id             			 => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ],
		'ui_wh_pay_status_radio'     => isset( $escaped_search_request_params['ui_wh_pay_status_radio'] ) ?  $escaped_search_request_params['ui_wh_pay_status_radio']  : $defaults['ui_wh_pay_status_radio'],
		'ui_wh_pay_status_custom'   => isset( $escaped_search_request_params['ui_wh_pay_status_custom'] ) ?  $escaped_search_request_params['ui_wh_pay_status_custom']  : $defaults['ui_wh_pay_status_custom']
	);

	$options = array (
						'all'           => __( 'Any Status', 'booking' ),
						'divider0'      => array( 'type' => 'html', 'html' => '<hr/>' ),
						'group_ok'      => __( 'Paid OK', 'booking' ),
						'group_unknown' => __( 'Unknown Status', 'booking' ),
						'group_pending' => __( 'Not Completed', 'booking' ),
						'group_failed'  => __( 'Failed', 'booking' ),

						'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),

						// Fixed [ '6', '', '2022-05-21']
						'custom' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ) ),												//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ) ),	//  1 => array( 'label', 'title' )  -->   $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex:1 1 100%;margin-top:5px;">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_pay_status_radio_1' 		// HTML ID  of element
														, 'name'     => 'ui_wh_pay_status_radio'
														, 'label'    => array( 'title' => __('Custom' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => 'user_entered' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_pay_status_radio' ] == 'user_entered' ) ? true : false 				// Selected or not
														//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex:1 1 100%;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_pay_status_custom' 		// HTML ID  of element
														, 'name'     => 'ui_wh_pay_status_custom'
														, 'label'    => ''//__('Check-out' ,'booking')
														, 'style'    => 'max-width:100%;width:100%;' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Payment status' ,'booking')		// gmdate( 'Y-m-d' )
														, 'value'    => $request_input_el_default[ 'ui_wh_pay_status_custom']  		// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_pay_status_radio_1').prop('checked', true);"					// JavaScript code
														//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),


						// Buttons
						'buttons1' =>  array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'justify-content: flex-end;',
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Apply', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_apply_click( { 
																									'dropdown_id'		 : 'wh_pay_status', 
																									'dropdown_radio_name': 'ui_wh_pay_status_radio' 
																								} );"				// JavaScript code
														  , 'class' => 'wpbc_ui_button_primary'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0 0 0 1em;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Close', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_close_click( 'wh_pay_status' );"           // JavaScript code
														  , 'class' => ''     				  // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),
				);

	$params = array(
					  'id'      => $el_id
					, 'default' => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
					, 'label' 	=> ''
					, 'title' 	=> ''//__('Payment', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by payment status' ,'booking') , 'position' => 'top' )
					, 'li_options' => $options
					//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( '#{$el_id}' ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
					//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() 		, 'in element:' , jQuery( this ) );"					// JavaScript code
					///*, 'onchange' =>*/. "wpbc_ajx_booking_send_search_request_with_params( { '{$el_id}': JSON.parse( jQuery( this ).val() ), 'page_num': 1 "
					//						// Frontend selected elements (saving for future use, after F5)
					//						. " ,'ui_wh_pay_status_radio'  : ( undefined === jQuery( 'input[name=\"ui_wh_pay_status_radio\"]:checked' ).val() ) ? '' : jQuery( 'input[name=\"ui_wh_pay_status_radio\"]:checked' ).val()"
					//						. " ,'ui_wh_pay_status_custom' : jQuery( '#ui_wh_pay_status_custom' ).val()"
					//				."} );"
				);


	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php
}

/**
 *  Costs 	Min - Max
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx__ui__cost_min_max( $escaped_search_request_params, $defaults ){

	if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
		return false;
	}

	$el_id = 'wh_cost';

	$params = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => '<span class="" style="font-weight:600;">' . esc_html__( 'Cost', 'booking' ) . ' <em style="color:#888;">(' . esc_html__( 'min-max', 'booking' ) . '):</em></span>'
							, 'style'    => 'max-width: 69px;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
							, 'placeholder' => '0'
							, 'value'    => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
							//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
			);
	?><div class="ui_element" style="margin-right: 5px;"><?php

	wpbc_flex_text( $params );

	?></div><?php


	$el_id = 'wh_cost2';

	$params = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => '<span class="" style="font-weight:600;"> &dash; </span>'
							, 'style'    => 'max-width: 69px;' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
							, 'placeholder' => '10000'
							, 'value'    => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ]
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
							//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
			);
	?><div class="ui_element"><?php

	wpbc_flex_text( $params );

	?></div><?php

}

/**
 * Reload button - force loading of ajax data
 *
 * @param $escaped_search_request_params   	-	escaped search request parameters array
 * @param $defaults							-   default parameters values
 */
function wpbc_ajx_toolbar_force_reload_button( $escaped_search_request_params, $defaults ){

    $params  =  array(
	    'type'             => 'button' ,
	    'title'            => '',//__( 'Reset', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
	    'hint'             => array( 'title' => __( 'Reload bookings listing', 'booking' ), 'position' => 'top' ),  	// Hint
	    'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
	    'action'           => "wpbc_ajx_booking_send_search_request_with_params( { } );",	// Some JavaScript
	    'icon' 			   => array(
									'icon_font' => 'wpbc_icn_refresh wpbc_spin', //'wpbc_icn_rotate_left',
									'position'  => 'left',
									'icon_img'  => ''
								),
	    'class'            => 'wpbc_ui_button wpbc_ui_button_primary0',  																// ''  | 'wpbc_ui_button_primary'
	    'style'            => '',																						// Any CSS class here
	    'mobile_show_text' => true,																					// Show  or hide text,  when viewing on Mobile devices (small window size).
	    'attr'             => array( 'id' => 'wpbc_booking_listing_reload_button')
	);

	?><div class="ui_element" style="flex: 0 1 auto;"><?php
		wpbc_flex_button( $params );
	?></div><?php
}

// </editor-fold>



// ---------------------------------------------------------------------------------------------------------------------
//  JS & CSS Loading
// ---------------------------------------------------------------------------------------------------------------------

/**
 * CSS files loading
 *
 * @param string $where_to_load
 */
function wpbc_ajx_toolbar_enqueue_css_files( $where_to_load ) {

	if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

		wp_enqueue_style( 'wpbc-flex-toolbar', wpbc_plugin_url( '/includes/_toolbar_ui/_src/toolbar_ui.css' ), array(), WP_BK_VERSION_NUM );
	}
}
add_action( 'wpbc_enqueue_css_files', 'wpbc_ajx_toolbar_enqueue_css_files', 50 );


/**
 * JS files loading
 *
 * @param string $where_to_load
 */
function wpbc_ajx_toolbar_enqueue_js_files( $where_to_load ) {

	$in_footer = true;

	if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

		wp_enqueue_script( 'wpbc-flex-toolbar-ui', wpbc_plugin_url( '/includes/_toolbar_ui/_out/toolbar_ui.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );

		/**
		 *  wp_localize_script( 'wpbc_all', 'wpbc_live_request_obj'
								, array(
										'contacts'  => '',
										'reminders' => ''
									)
			);
		*/
	}
}
add_action( 'wpbc_enqueue_js_files', 'wpbc_ajx_toolbar_enqueue_js_files', 50 );


// 2024-08-13