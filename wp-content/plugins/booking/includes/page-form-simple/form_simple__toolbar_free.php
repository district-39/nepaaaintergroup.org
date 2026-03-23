<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Simple Booking Form  -- Toolbar
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-08-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


function wpbc_simple_form__toolbar_free() {


	if ( function_exists( 'wpbc_flex_toolbar__booking_form__top_tabs__ps' ) ) {
		wpbc_flex_toolbar__booking_form__top_tabs__ps();
	} else {

		wpbc_bs_toolbar_tabs_html_container_start();
			wpbc_bs_display_tab(   array(
												'title'         => __('Form Fields Setup', 'booking')
												, 'hint' 		=> array( 'title' => __('Customize booking form in a simple setup' ,'booking') , 'position' => 'bottom' )
												, 'onclick'     =>    ""
//												, 'onclick'     =>    "jQuery( '#wpbc_form_field_free' ).show();"
//																	. "jQuery( '.wpbc_center_preview' ).hide();"
//																	. "jQuery('.wpbc_container_hide__on_left_nav_click .nav-tab').removeClass('nav-tab-active');"
//																	. "jQuery(this).addClass('nav-tab-active');"
//																	. "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
//																	. "jQuery('.nav-tab-active i').addClass('icon-white');"
												, 'attr' => array( 'style' => 'order:0;' )			// In paid version  set it as second tab
												, 'font_icon'   => 'wpbc_icn_format_align_left'
												, 'default'     => true
								) );
		wpbc_bs_toolbar_tabs_html_container_end();
	}

		// css class: wpbc_flex_toolbar_container

	?><div class="wpbc_ajx_toolbar wpbc_no_borders0"><?php
		?><div class="ui_container    ui_container_toolbar		ui_container_small    ui_container_custom_forms_selection    ui_container_actions_row_1"><?php

			// Reset	- Form Template -
			?><div class="ui_group ui_group___add_new_field"><?php

				?><div class="ui_element"><?php
					wpbc_flex_ui__simple_form__select_field();
				?></div><?php

				?><div class="ui_element"><?php
					wpbc_flex_ui__simple_form__add_field_button();
				?></div><?php

			?></div><?php

			// Select	- Custom Forms -
			if ( function_exists( 'wpbc_flex_toolbar__custom_forms' ) ) {
				?><div class="ui_group"><div class="ui_element"><div class="wpbc_ui_separtor" style="margin-left: 8px;"></div></div></div><?php
				wpbc_flex_toolbar__custom_forms();
			}

			// Reset	- Form Template -
			?><div class="ui_group ui_group___reset_form_templates" style="margin-left:auto;"><?php

				?><div class="ui_element"><?php
					wpbc_flex_ui__simple_form__reset_form_template__button();
				?></div><?php

			?></div><?php


		?></div><?php
	?></div><?php

}

// ---------------------------------------------------------------------------------------------------------------------
//  UI Elements.
// ---------------------------------------------------------------------------------------------------------------------
function wpbc_flex_ui__simple_form__select_field(){

	$id = 'select_form_help_shortcode';

	$templates = array();

	$templates['selector_hint'] = array(
		'title'    => __( 'Select', 'booking' ) . ' ' . __( 'Form Field', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => 'font-weight: 400;border-bottom:1px dashed #ccc;color:#ccc;',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['text']          = array(
		'title'    => __( 'Text', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['select']        = array(
		'title'    => __( 'Dropdown', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['textarea']      = array(
		'title'    => __( 'Textarea', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['checkbox']      = array(
		'title'    => __( 'Checkbox', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);

	$templates['optgroup_af_s'] = array(
		'optgroup' => true,
		'close'    => false,
		'title'    => '&nbsp;' . __( 'Times Fields', 'booking' ),
	);

	$templates['rangetime']    = array(
		'title'    => __( 'Time Slots', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['durationtime'] = array(
		'title'    => __( 'Duration Time', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);
	$templates['starttime']    = array(
		'title'    => __( 'Start Time', 'booking' ),
		'id'       => '',
		'name'     => '',
		'style'    => '',
		'class'    => '',
		'disabled' => false,
		'selected' => false,
		'attr'     => array(),
	);

	// If time slot field already  exist,  then  re-update it to Edit Field.
	$templates = apply_filters( 'wpbc_form_gen_free_fields_selection', $templates, wpbc_simple_form__db__get_visual_form_structure() );

	$templates['optgroup_af_e'] = array( 'optgroup' => true, 'close' => true );


	$params_select = array(
		'id'       => $id,  // HTML ID  of element.
		'name'     => $id,
		'options'  => $templates,
		'onchange' => "wpbc_show_fields_generator( this.options[this.selectedIndex].value );",
		// 'label' => __('Booking Form', 'booking') . ':'							// Label (optional).
		//	, 'style' => ''                     								// CSS of select element.
		//	, 'class' => ''                     								// CSS Class of select element.
		//	, 'multiple' => false  //.
		//	, 'attr' => array()                 								// Any additional attributes, if this radio | checkbox element.
		//	, 'disabled' => false  //.
		//	, 'disabled_options' => array( 2, 30 )     							// If some options disabled, then it has to list here.
		//	, 'value' => ( isset( $escaped_search_request_params[ 'next_days' ] ) ) ? $escaped_search_request_params[ 'next_days' ] : '183'	// Some Value from options array that selected by default.
		//	, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code.
	);

	wpbc_flex_select( $params_select );
}


function wpbc_flex_ui__simple_form__add_field_button() {

	$id = 'simple_form__add_field_button';

	$el_id = 'ui_btn_' . $id;

	$params = array(
		'type'             => 'button',
		'title'            => __( 'Add New Field', 'booking' ),
		'hint'             => array(
			'title'    => __( 'Select a field type from the drop-down list on the left.', 'booking' ),
			'position' => 'bottom',
		),
		'link'             => 'javascript:void(0)', // Direct link or skip  it.
		'action'           => "if ( 'selector_hint'===jQuery( '#select_form_help_shortcode').val() ) { " .
							  "wpbc_field_highlight( '#select_form_help_shortcode' ); " .
							  "alert( '" . esc_js( esc_attr( __( 'Select a field type from the drop-down list on the left.', 'booking' ) ) ) . "' ); " .
							  "} else { " .
							  "wpbc_show_fields_generator( jQuery( '#select_form_help_shortcode').val() );" .
							  "}",
		'icon'             => array(
			'icon_font' => 'wpbc_icn_add',
			'position'  => 'right',
			'icon_img'  => '',
		),
		'class'            => 'wpbc_ui_button wpbc_bs_button_green',  // 'wpbc_ui_button_primary'.
		'style'            => '', // Any CSS class here.
		'mobile_show_text' => true, // Show  or hide text,  when viewing on Mobile devices (small window size).
		'attr'             => array( 'id' => $el_id ),
	);

	wpbc_flex_button( $params );
}


/**
 * Button for Reset to default booking form
 * (import form  fields  from OLD  free version
 */
function wpbc_flex_ui__simple_form__reset_form_template__button() {

	$id = 'reset_form_template__button';

	$el_id = 'ui_btn_' . $id;

	$params = array(
		'type'             => 'button',
		'title'            => __( 'Reset', 'booking' ) . '&nbsp;&nbsp;', // Title of the button.
		'hint'             => array(
			'title'    => __( 'Reset booking form fields', 'booking' ),
			'position' => 'bottom',
		),
		'link'             => 'javascript:void(0)', // Direct link or skip  it.
		'action'           => "if ( wpbc_are_you_sure('" .
							  esc_js( __( 'Do you really want to do this ?', 'booking' ) ) .
							  "') ) {" .
							  "var selected_val = 'standard';" .
							  "jQuery('#reset_to_default_form').val( selected_val );jQuery('#wpbc_form_field_free').trigger( 'submit' );" .
							  "}",
		'icon'             => array(
			'icon_font' => 'wpbc_icn_rotate_left wpbc_icn_settings_backup_restore0 wpbc_icn_system_update_alt0',
			'position'  => 'right',
			'icon_img'  => '',
		),
		'class'            => 'wpbc_ui_button wpbc_ui_button_danger', // 'wpbc_ui_button_primary'.
		'style'            => '', // Any CSS class here.
		'mobile_show_text' => true, // Show  or hide text,  when viewing on Mobile devices (small window size).
		'attr'             => array( 'id' => $el_id ),
	);

	wpbc_flex_button( $params );
}