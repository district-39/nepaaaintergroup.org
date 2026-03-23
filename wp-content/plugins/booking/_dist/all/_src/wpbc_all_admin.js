
/**
 * Blink specific HTML element to set attention to this element.
 *
 * @param {string} element_to_blink		  - class or id of element: '.wpbc_widget_available_unavailable'
 * @param {int} how_many_times			  - 4
 * @param {int} how_long_to_blink		  - 350
 */
function wpbc_blink_element( element_to_blink, how_many_times = 4, how_long_to_blink = 350 ){

	for ( let i = 0; i < how_many_times; i++ ){
		jQuery( element_to_blink ).fadeOut( how_long_to_blink ).fadeIn( how_long_to_blink );
	}
    jQuery( element_to_blink ).animate( {opacity: 1}, 500 );
}

/**
 *   Support Functions - Spin Icon in Buttons  ------------------------------------------------------------------ */

/**
 * Remove spin icon from  button and Enable this button.
 *
 * @param button_clicked_element_id		- HTML ID attribute of this button
 * @return string						- CSS classes that was previously in button icon
 */
function wpbc_button__remove_spin(button_clicked_element_id) {

	var previos_classes = '';
	if (
		(undefined != button_clicked_element_id)
		&& ('' != button_clicked_element_id)
	) {
		var jElement = jQuery( '#' + button_clicked_element_id );
		if ( jElement.length ) {
			previos_classes = wpbc_button_disable_loading_icon( jElement.get( 0 ) );
		}
	}

	return previos_classes;
}


/**
 * Show Loading (rotating arrow) icon for button that has been clicked
 *
 * @param this_button		- this object of specific button
 * @return string			- CSS classes that was previously in button icon
 */
function wpbc_button_enable_loading_icon(this_button) {

	var jButton         = jQuery( this_button );
	var jIcon           = jButton.find( 'i' );
	var previos_classes = jIcon.attr( 'class' );

	jIcon.removeClass().addClass( 'menu_icon icon-1x wpbc_icn_rotate_right wpbc_spin' );	// Set Rotate icon.
	// jIcon.addClass( 'wpbc_animation_pause' );												// Pause animation.
	// jIcon.addClass( 'wpbc_ui_red' );														// Set icon color red.

	jIcon.attr( 'wpbc_previous_class', previos_classes )

	jButton.addClass( 'disabled' );															// Disable button
	// We need to  set  here attr instead of prop, because for A elements,  attribute 'disabled' do  not added with jButton.prop( "disabled", true );.

	jButton.attr( 'wpbc_previous_onclick', jButton.attr( 'onclick' ) );		// Save this value.
	jButton.attr( 'onclick', '' );											// Disable actions "on click".

	return previos_classes;
}


/**
 * Hide Loading (rotating arrow) icon for button that was clicked and show previous icon and enable button
 *
 * @param this_button		- this object of specific button
 * @return string			- CSS classes that was previously in button icon
 */
function wpbc_button_disable_loading_icon(this_button) {

	var jButton = jQuery( this_button );
	var jIcon   = jButton.find( 'i' );

	var previos_classes = jIcon.attr( 'wpbc_previous_class' );
	if (
		(undefined != previos_classes)
		&& ('' != previos_classes)
	) {
		jIcon.removeClass().addClass( previos_classes );
	}

	jButton.removeClass( 'disabled' );															// Remove Disable button.

	var previous_onclick = jButton.attr( 'wpbc_previous_onclick' )
	if (
		(undefined != previous_onclick)
		&& ('' != previous_onclick)
	) {
		jButton.attr( 'onclick', previous_onclick );
	}

	return previos_classes;
}

/**
 * On selection  of radio button, adjust attributes of radio container
 *
 * @param _this
 */
function wpbc_ui_el__radio_container_selection(_this) {

	if ( jQuery( _this ).is( ':checked' ) ) {
		jQuery( _this ).parents( '.wpbc_ui_radio_section' ).find( '.wpbc_ui_radio_container' ).removeAttr( 'data-selected' );
		jQuery( _this ).parents( '.wpbc_ui_radio_container:not(.disabled)' ).attr( 'data-selected', true );
	}

	if ( jQuery( _this ).is( ':disabled' ) ) {
		jQuery( _this ).parents( '.wpbc_ui_radio_container' ).addClass( 'disabled' );
	}
}

/**
 * On click on Radio Container, we will  select  the  radio button    and then adjust attributes of radio container
 *
 * @param _this
 */
function wpbc_ui_el__radio_container_click(_this) {

	if ( jQuery( _this ).hasClass( 'disabled' ) ) {
		return false;
	}

	var j_radio = jQuery( _this ).find( 'input[type=radio]:not(.wpbc-form-radio-internal)' );
	if ( j_radio.length ) {
		j_radio.prop( 'checked', true ).trigger( 'change' );
	}

}
"use strict";
// =====================================================================================================================
// == Full Screen  -  support functions   ==
// =====================================================================================================================

/**
 * Check Full  screen mode,  by  removing top tab
 */
function wpbc_check_full_screen_mode(){
	if ( jQuery( 'body' ).hasClass( 'wpbc_admin_full_screen' ) ) {
		jQuery( 'html' ).removeClass( 'wp-toolbar' );
	} else {
		jQuery( 'html' ).addClass( 'wp-toolbar' );
	}
	wpbc_check_buttons_max_min_in_full_screen_mode();
}

function wpbc_check_buttons_max_min_in_full_screen_mode() {
	if ( jQuery( 'body' ).hasClass( 'wpbc_admin_full_screen' ) ) {
		jQuery( '.wpbc_ui__top_nav__btn_full_screen'   ).addClass(    'wpbc_ui__hide' );
		jQuery( '.wpbc_ui__top_nav__btn_normal_screen' ).removeClass( 'wpbc_ui__hide' );
	} else {
		jQuery( '.wpbc_ui__top_nav__btn_full_screen'   ).removeClass( 'wpbc_ui__hide' );
		jQuery( '.wpbc_ui__top_nav__btn_normal_screen' ).addClass(    'wpbc_ui__hide' );
	}
}

jQuery( document ).ready( function () {
	wpbc_check_full_screen_mode();
} );
/**
 * Checkbox Selection functions for Listing.
 */

/**
 * Selections of several  checkboxes like in gMail with shift :)
 * Need to  have this structure:
 * .wpbc_selectable_table
 *      .wpbc_selectable_head
 *              .check-column
 *                  :checkbox
 *      .wpbc_selectable_body
 *          .wpbc_row
 *              .check-column
 *                  :checkbox
 *      .wpbc_selectable_foot
 *              .check-column
 *                  :checkbox
 */
function wpbc_define_gmail_checkbox_selection( $ ){

	var checks, first, last, checked, sliced, lastClicked = false;

	// Check all checkboxes.
	$( '.wpbc_selectable_body' ).find( '.check-column' ).find( ':checkbox' ).on(
		'click',
		function (e) {
			if ( 'undefined' == e.shiftKey ) {
				return true;
			}
			if ( e.shiftKey ) {
				if ( ! lastClicked ) {
					return true;
				}
				checks  = $( lastClicked ).closest( '.wpbc_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' );
				first   = checks.index( lastClicked );
				last    = checks.index( this );
				checked = $( this ).prop( 'checked' );
				if ( 0 < first && 0 < last && first != last ) {
					sliced = (last > first) ? checks.slice( first, last ) : checks.slice( last, first );
					sliced.prop(
						'checked',
						function () {
							if ( $( this ).closest( '.wpbc_row' ).is( ':visible' ) ) {
								return checked;
							}
							return false;
						}
					).trigger( 'change' );
				}
			}
			lastClicked = this;

			// toggle "check all" checkboxes.
			var unchecked = $( this ).closest( '.wpbc_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' ).not( ':checked' );
			$( this ).closest( '.wpbc_selectable_table' ).children( '.wpbc_selectable_head, .wpbc_selectable_foot' ).find( ':checkbox' ).prop(
				'checked',
				function () {
					return (0 === unchecked.length);
				}
			).trigger( 'change' );

			return true;
		}
	);

	// Head || Foot clicking to  select / deselect ALL.
	$( '.wpbc_selectable_head, .wpbc_selectable_foot' ).find( '.check-column :checkbox' ).on(
		'click',
		function (event) {
			var $this          = $( this ),
				$table         = $this.closest( '.wpbc_selectable_table' ),
				controlChecked = $this.prop( 'checked' ),
				toggle         = event.shiftKey || $this.data( 'wp-toggle' );

			$table.children( '.wpbc_selectable_body' ).filter( ':visible' )
				.find( '.check-column' ).find( ':checkbox' )
				.prop(
					'checked',
					function () {
						if ( $( this ).is( ':hidden,:disabled' ) ) {
							return false;
						}
						if ( toggle ) {
							return ! $( this ).prop( 'checked' );
						} else if ( controlChecked ) {
							return true;
						}
						return false;
					}
				).trigger( 'change' );

			$table.children( '.wpbc_selectable_head,  .wpbc_selectable_foot' ).filter( ':visible' )
				.find( '.check-column' ).find( ':checkbox' )
				.prop(
					'checked',
					function () {
						if ( toggle ) {
							return false;
						} else if ( controlChecked ) {
							return true;
						}
						return false;
					}
				);
		}
	);


	// Visually  show selected border.
	$( '.wpbc_selectable_body' ).find( '.check-column :checkbox' ).on(
		'change',
		function (event) {
			if ( jQuery( this ).is( ':checked' ) ) {
				jQuery( this ).closest( '.wpbc_list_row' ).addClass( 'row_selected_color' );
			} else {
				jQuery( this ).closest( '.wpbc_list_row' ).removeClass( 'row_selected_color' );
			}

			// Disable text selection while pressing 'shift'.
			document.getSelection().removeAllRanges();

			// Show or hide buttons on Actions toolbar  at  Booking Listing  page,  if we have some selected bookings.
			wpbc_show_hide_action_buttons_for_selected_bookings();
		}
	);

	wpbc_show_hide_action_buttons_for_selected_bookings();
}


/**
 * Get ID array  of selected elements
 */
function wpbc_get_selected_row_id() {

	var $table      = jQuery( '.wpbc__wrap__booking_listing .wpbc_selectable_table' );
	var checkboxes  = $table.children( '.wpbc_selectable_body' ).filter( ':visible' ).find( '.check-column' ).find( ':checkbox' );
	var selected_id = [];

	jQuery.each(
		checkboxes,
		function (key, checkbox) {
			if ( jQuery( checkbox ).is( ':checked' ) ) {
				var element_id = wpbc_get_row_id_from_element( checkbox );
				selected_id.push( element_id );
			}
		}
	);

	return selected_id;
}


/**
 * Get ID of row,  based on clciked element
 *
 * @param this_inbound_element  - ususlly  this
 * @returns {number}
 */
function wpbc_get_row_id_from_element(this_inbound_element) {

	var element_id = jQuery( this_inbound_element ).closest( '.wpbc_listing_usual_row' ).attr( 'id' );

	element_id = parseInt( element_id.replace( 'row_id_', '' ) );

	return element_id;
}


/**
 * == Booking Listing == Show or hide buttons on Actions toolbar  at    page,  if we have some selected bookings.
 */
function wpbc_show_hide_action_buttons_for_selected_bookings(){

	var selected_rows_arr = wpbc_get_selected_row_id();

	if ( selected_rows_arr.length > 0 ) {
		jQuery( '.hide_button_if_no_selection' ).show();
	} else {
		jQuery( '.hide_button_if_no_selection' ).hide();
	}
}
"use strict";
// =====================================================================================================================
// == Left Bar  -  expand / colapse functions   ==
// =====================================================================================================================

/**
 * Expand Vertical Left Bar.
 */
function wpbc_admin_ui__sidebar_left__do_max() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min max compact none' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'max' );
	jQuery( '.wpbc_ui__top_nav__btn_open_left_vertical_nav' ).addClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_left_vertical_nav' ).removeClass( 'wpbc_ui__hide' );

	jQuery( '.wp-admin' ).removeClass( 'wpbc_page_wrapper_left_min wpbc_page_wrapper_left_max wpbc_page_wrapper_left_compact wpbc_page_wrapper_left_none' );
	jQuery( '.wp-admin' ).addClass( 'wpbc_page_wrapper_left_max' );
}

/**
 * Hide Vertical Left Bar.
 */
function wpbc_admin_ui__sidebar_left__do_min() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min max compact none' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'min' );
	jQuery( '.wpbc_ui__top_nav__btn_open_left_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_left_vertical_nav' ).addClass( 'wpbc_ui__hide' );

	jQuery( '.wp-admin' ).removeClass( 'wpbc_page_wrapper_left_min wpbc_page_wrapper_left_max wpbc_page_wrapper_left_compact wpbc_page_wrapper_left_none' );
	jQuery( '.wp-admin' ).addClass( 'wpbc_page_wrapper_left_min' );
}

/**
 * Colapse Vertical Left Bar.
 */
function wpbc_admin_ui__sidebar_left__do_compact() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min max compact none' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'compact' );
	jQuery( '.wpbc_ui__top_nav__btn_open_left_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_left_vertical_nav' ).addClass( 'wpbc_ui__hide' );

	jQuery( '.wp-admin' ).removeClass( 'wpbc_page_wrapper_left_min wpbc_page_wrapper_left_max wpbc_page_wrapper_left_compact wpbc_page_wrapper_left_none' );
	jQuery( '.wp-admin' ).addClass( 'wpbc_page_wrapper_left_compact' );
}

/**
 * Completely Hide Vertical Left Bar.
 */
function wpbc_admin_ui__sidebar_left__do_hide() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min max compact none' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'none' );
	jQuery( '.wpbc_ui__top_nav__btn_open_left_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_left_vertical_nav' ).addClass( 'wpbc_ui__hide' );
	// Hide top "Menu" button with divider.
	jQuery( '.wpbc_ui__top_nav__btn_show_left_vertical_nav,.wpbc_ui__top_nav__btn_show_left_vertical_nav_divider' ).addClass( 'wpbc_ui__hide' );

	jQuery( '.wp-admin' ).removeClass( 'wpbc_page_wrapper_left_min wpbc_page_wrapper_left_max wpbc_page_wrapper_left_compact wpbc_page_wrapper_left_none' );
	jQuery( '.wp-admin' ).addClass( 'wpbc_page_wrapper_left_none' );
}

/**
 * Action on click "Go Back" - show root menu
 * or some other section in left sidebar.
 *
 * @param string menu_to_show - menu slug.
 */
function wpbc_admin_ui__sidebar_left__show_section( menu_to_show ) {
	jQuery( '.wpbc_ui_el__vert_left_bar__section' ).addClass( 'wpbc_ui__hide' )
	jQuery( '.wpbc_ui_el__vert_left_bar__section_' + menu_to_show ).removeClass( 'wpbc_ui__hide' );
}

// =====================================================================================================================
// == Right Side Bar  -  expand / colapse functions   ==
// =====================================================================================================================

/**
 * Expand Vertical Right Bar.
 */
function wpbc_admin_ui__sidebar_right__do_max() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min_right max_right compact_right none_right' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'max_right' );
	jQuery( '.wpbc_ui__top_nav__btn_open_right_vertical_nav' ).addClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_right_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
}

/**
 * Hide Vertical Right Bar.
 */
function wpbc_admin_ui__sidebar_right__do_min() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min_right max_right compact_right none_right' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'min_right' );
	jQuery( '.wpbc_ui__top_nav__btn_open_right_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_right_vertical_nav' ).addClass( 'wpbc_ui__hide' );
}

/**
 * Colapse Vertical Right Bar.
 */
function wpbc_admin_ui__sidebar_right__do_compact() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min_right max_right compact_right none_right' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'compact_right' );
	jQuery( '.wpbc_ui__top_nav__btn_open_right_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_right_vertical_nav' ).addClass( 'wpbc_ui__hide' );
}

/**
 * Completely Hide Vertical Right Bar.
 */
function wpbc_admin_ui__sidebar_right__do_hide() {
	jQuery( '.wpbc_settings_page_wrapper' ).removeClass( 'min_right max_right compact_right none_right' );
	jQuery( '.wpbc_settings_page_wrapper' ).addClass( 'none_right' );
	jQuery( '.wpbc_ui__top_nav__btn_open_right_vertical_nav' ).removeClass( 'wpbc_ui__hide' );
	jQuery( '.wpbc_ui__top_nav__btn_hide_right_vertical_nav' ).addClass( 'wpbc_ui__hide' );
	// Hide top "Menu" button with divider.
	jQuery( '.wpbc_ui__top_nav__btn_show_right_vertical_nav,.wpbc_ui__top_nav__btn_show_right_vertical_nav_divider' ).addClass( 'wpbc_ui__hide' );
}

/**
 * Action on click "Go Back" - show root menu
 * or some other section in right sidebar.
 *
 * @param string menu_to_show - menu slug.
 */
function wpbc_admin_ui__sidebar_right__show_section( menu_to_show ) {
	jQuery( '.wpbc_ui_el__vert_right_bar__section' ).addClass( 'wpbc_ui__hide' )
	jQuery( '.wpbc_ui_el__vert_right_bar__section_' + menu_to_show ).removeClass( 'wpbc_ui__hide' );
}

// =====================================================================================================================
// == End Right Side Bar  section   ==
// =====================================================================================================================

/**
 * Get anchor(s) array  from  URL.
 * Doc: https://developer.mozilla.org/en-US/docs/Web/API/Location
 *
 * @returns {*[]}
 */
function wpbc_url_get_anchors_arr() {
	var hashes            = window.location.hash.replace( '%23', '#' );
	var hashes_arr        = hashes.split( '#' );
	var result            = [];
	var hashes_arr_length = hashes_arr.length;

	for ( var i = 0; i < hashes_arr_length; i++ ) {
		if ( hashes_arr[i].length > 0 ) {
			result.push( hashes_arr[i] );
		}
	}
	return result;
}

/**
 * Auto Expand Settings section based on URL anchor, after  page loaded.
 */
jQuery( document ).ready( function () { wpbc_admin_ui__do_expand_section(); setTimeout( 'wpbc_admin_ui__do_expand_section', 10 ); } );
jQuery( document ).ready( function () { wpbc_admin_ui__do_expand_section(); setTimeout( 'wpbc_admin_ui__do_expand_section', 150 ); } );

/**
 * Expand section in  General Settings page and select Menu item.
 */
function wpbc_admin_ui__do_expand_section() {

	// window.location.hash  = #section_id  /  doc: https://developer.mozilla.org/en-US/docs/Web/API/Location .
	var anchors_arr        = wpbc_url_get_anchors_arr();
	var anchors_arr_length = anchors_arr.length;

	if ( anchors_arr_length > 0 ) {
		var one_anchor_prop_value = anchors_arr[0].split( 'do_expand__' );
		if ( one_anchor_prop_value.length > 1 ) {

			// 'wpbc_general_settings_calendar_metabox'
			var section_to_show    = one_anchor_prop_value[1];
			var section_id_to_show = '#' + section_to_show;


			// -- Remove selected background in all left  menu  items ---------------------------------------------------
			jQuery( '.wpbc_ui_el__vert_nav_item ' ).removeClass( 'active' );
			// Set left menu selected.
			jQuery( '.do_expand__' + section_to_show + '_link' ).addClass( 'active' );
			var selected_title = jQuery( '.do_expand__' + section_to_show + '_link a .wpbc_ui_el__vert_nav_title ' ).text();

			// Expand section, if it colapsed.
			if ( ! jQuery( '.do_expand__' + section_to_show + '_link' ).parents( '.wpbc_ui_el__level__folder' ).hasClass( 'expanded' ) ) {
				jQuery( '.wpbc_ui_el__level__folder' ).removeClass( 'expanded' );
				jQuery( '.do_expand__' + section_to_show + '_link' ).parents( '.wpbc_ui_el__level__folder' ).addClass( 'expanded' );
			}

			// -- Expand section ---------------------------------------------------------------------------------------
			var container_to_hide_class = '.postbox';
			// Hide sections '.postbox' in admin page and show specific one.
			jQuery( '.wpbc_admin_page ' + container_to_hide_class ).hide();
			jQuery( '.wpbc_container_always_hide__on_left_nav_click' ).hide();
			jQuery( section_id_to_show ).show();

			// Show all other sections,  if provided in URL: ..?page=wpbc-settings#do_expand__wpbc_general_settings_capacity_metabox#wpbc_general_settings_capacity_upgrade_metabox .
			for ( let i = 1; i < anchors_arr_length; i++ ) {
				jQuery( '#' + anchors_arr[i] ).show();
			}

			if ( false ) {
				var targetOffset = wpbc_scroll_to( section_id_to_show );
			}

			// -- Set Value to Input about selected Nav element  ---------------------------------------------------------------       // FixIn: 9.8.6.1.
			var section_id_tab = section_id_to_show.substring( 0, section_id_to_show.length - 8 ) + '_tab';
			if ( container_to_hide_class == section_id_to_show ) {
				section_id_tab = '#wpbc_general_settings_all_tab'
			}
			if ( '#wpbc_general_settings_capacity_metabox,#wpbc_general_settings_capacity_upgrade_metabox' == section_id_to_show ) {
				section_id_tab = '#wpbc_general_settings_capacity_tab'
			}
			jQuery( '#form_visible_section' ).val( section_id_tab );
		}

		// Like blinking some elements.
		wpbc_admin_ui__do__anchor__another_actions();
	}
}

function wpbc_admin_ui__is_in_mobile_screen_size() {
	return wpbc_admin_ui__is_in_this_screen_size( 605 );
}

function wpbc_admin_ui__is_in_this_screen_size(size) {
	return (window.screen.width <= size);
}

/**
 * Open settings page  |  Expand section  |  Select Menu item.
 */
function wpbc_admin_ui__do__open_url__expand_section(url, section_id) {

	// window.location.href = url + '&do_expand=' + section_id + '#do_expand__' + section_id; //.
	window.location.href = url + '#do_expand__' + section_id;

	if ( wpbc_admin_ui__is_in_mobile_screen_size() ) {
		wpbc_admin_ui__sidebar_left__do_min();
	}

	wpbc_admin_ui__do_expand_section();
}


/**
 * Check  for Other actions:  Like blinking some elements in settings page. E.g. Days selection  or  change-over days.
 */
function wpbc_admin_ui__do__anchor__another_actions() {

	var anchors_arr        = wpbc_url_get_anchors_arr();
	var anchors_arr_length = anchors_arr.length;

	// Other actions:  Like blinking some elements.
	for ( var i = 0; i < anchors_arr_length; i++ ) {

		var this_anchor = anchors_arr[i];

		var this_anchor_prop_value = this_anchor.split( 'do_other_actions__' );

		if ( this_anchor_prop_value.length > 1 ) {

			var section_action = this_anchor_prop_value[1];

			switch ( section_action ) {

				case 'blink_day_selections':
					// wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a', '#wpbc_general_settings_calendar_metabox', 'Days Selection' );.
					wpbc_blink_element( '.wpbc_tr_set_gen_booking_type_of_day_selections', 4, 350 );
						wpbc_scroll_to( '.wpbc_tr_set_gen_booking_type_of_day_selections' );
					break;

				case 'blink_change_over_days':
					// wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a', '#wpbc_general_settings_calendar_metabox', 'Changeover Days' );.
					wpbc_blink_element( '.wpbc_tr_set_gen_booking_range_selection_time_is_active', 4, 350 );
						wpbc_scroll_to( '.wpbc_tr_set_gen_booking_range_selection_time_is_active' );
					break;

				case 'blink_captcha':
					wpbc_blink_element( '.wpbc_tr_set_gen_booking_is_use_captcha', 4, 350 );
						wpbc_scroll_to( '.wpbc_tr_set_gen_booking_is_use_captcha' );
					break;

				default:
			}
		}
	}
}
/**
 * Copy txt to clipbrd from Text fields.
 *
 * @param html_element_id  - e.g. 'data_field'
 * @returns {boolean}
 */
function wpbc_copy_text_to_clipbrd_from_element( html_element_id ) {
	// Get the text field.
	var copyText = document.getElementById( html_element_id );

	// Select the text field.
	copyText.select();
	copyText.setSelectionRange( 0, 99999 ); // For mobile devices.

	// Copy the text inside the text field.
	var is_copied = wpbc_copy_text_to_clipbrd( copyText.value );
	if ( ! is_copied ) {
		console.error( 'Oops, unable to copy', copyText.value );
	}
	return is_copied;
}

/**
 * Copy txt to clipbrd.
 *
 * @param text
 * @returns {boolean}
 */
function wpbc_copy_text_to_clipbrd(text) {

	if ( ! navigator.clipboard ) {
		return wpbc_fallback_copy_text_to_clipbrd( text );
	}

	navigator.clipboard.writeText( text ).then(
		function () {
			// console.log( 'Async: Copying to clipboard was successful!' );.
			return  true;
		},
		function (err) {
			// console.error( 'Async: Could not copy text: ', err );.
			return  false;
		}
	);
}

/**
 * Copy txt to clipbrd - depricated method.
 *
 * @param text
 * @returns {boolean}
 */
function wpbc_fallback_copy_text_to_clipbrd( text ) {

	// -----------------------------------------------------------------------------------------------------------------
	// var textArea   = document.createElement( "textarea" );
	// textArea.value = text;
	//
	// // Avoid scrolling to bottom.
	// textArea.style.top      = "0";
	// textArea.style.left     = "0";
	// textArea.style.position = "fixed";
	// textArea.style.zIndex   = "999999999";
	// document.body.appendChild( textArea );
	// textArea.focus();
	// textArea.select();

	// -----------------------------------------------------------------------------------------------------------------
	// Now get it as HTML  (original here https://stackoverflow.com/questions/34191780/javascript-copy-string-to-clipboard-as-text-html ).

	// [1] - Create container for the HTML.
	var container       = document.createElement( 'div' );
	container.innerHTML = text;

	// [2] - Hide element.
	container.style.position      = 'fixed';
	container.style.pointerEvents = 'none';
	container.style.opacity       = 0;

	// Detect all style sheets of the page.
	var activeSheets = Array.prototype.slice.call( document.styleSheets ).filter(
		function (sheet) {
			return ! sheet.disabled;
		}
	);

	// [3] - Mount the container to the DOM to make `contentWindow` available.
	document.body.appendChild( container );

	// [4] - Copy to clipboard.
	window.getSelection().removeAllRanges();

	var range = document.createRange();
	range.selectNode( container );
	window.getSelection().addRange( range );
	// -----------------------------------------------------------------------------------------------------------------

	var result = false;

	try {
		result = document.execCommand( 'copy' );
		// console.log( 'Fallback: Copying text command was ' + msg ); //.
	} catch ( err ) {
		// console.error( 'Fallback: Oops, unable to copy', err ); //.
	}
	// document.body.removeChild( textArea ); //.

	// [5.4] - Enable CSS.
	var activeSheets_length = activeSheets.length;
	for ( var i = 0; i < activeSheets_length; i++ ) {
		activeSheets[i].disabled = false;
	}

	// [6] - Remove the container
	document.body.removeChild( container );

	return  result;
}
/**
 * WPBC Collapsible Groups
 *
 * Universal, dependency-free controller for expanding/collapsing grouped sections in right-side panels (Inspector/Library/Form Settings, or any other WPBC page).
 *
 * 		=== How to use it (quick) ? ===
 *
 *		-- 1. Markup (independent mode: multiple open allowed) --
 *			<div class="wpbc_collapsible">
 *			  <section class="wpbc_ui__collapsible_group is-open">
 *				<button type="button" class="group__header"><h3>General</h3></button>
 *				<div class="group__fields">…</div>
 *			  </section>
 *			  <section class="wpbc_ui__collapsible_group">
 *				<button type="button" class="group__header"><h3>Advanced</h3></button>
 *				<div class="group__fields">…</div>
 *			  </section>
 *			</div>
 *
 *		-- 2. Exclusive/accordion mode (one open at a time) --
 *			<div class="wpbc_collapsible wpbc_collapsible--exclusive">…</div>
 *
 *		-- 3. Auto-init --
 *			The script auto-initializes on DOMContentLoaded. No extra code needed.
 *
 *		-- 4. Programmatic control (optional)
 *			const root = document.querySelector('#wpbc_bfb__inspector');
 *			const api  = root.__wpbc_collapsible_instance; // set by auto-init
 *
 *			api.open_by_heading('Validation'); // open by heading text
 *			api.open_by_index(0);              // open the first group
 *
 *		-- 5.Listen to events (e.g., to persist “open group” state) --
 *			root.addEventListener('wpbc:collapsible:open',  (e) => { console.log(  e.detail.group ); });
 *			root.addEventListener('wpbc:collapsible:close', (e) => { console.log(  e.detail.group ); });
 *
 *
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_collapsible [wpbc_collapsible--exclusive]">
 *    <section class="wpbc_ui__collapsible_group [is-open]">
 *      <button type="button" class="group__header"> ... </button>
 *      <div class="group__fields"> ... </div>
 *    </section>
 *    ... more <section> ...
 *  </div>
 *
 * Notes:
 *  - Add `is-open` to any section you want initially expanded.
 *  - Add `wpbc_collapsible--exclusive` to the container for "open one at a time" behavior.
 *  - Works with your existing BFB markup (classes used there are the defaults).
 *
 * Accessibility:
 *  - Sets aria-expanded on .group__header
 *  - Sets aria-hidden + [hidden] on .group__fields
 *  - ArrowUp/ArrowDown move focus between headers; Enter/Space toggles
 *
 * Events (bubbles from the <section>):
 *  - 'wpbc:collapsible:open'  (detail: { group, root, instance })
 *  - 'wpbc:collapsible:close' (detail: { group, root, instance })
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *  - expand(group, [exclusive]), collapse(group), toggle(group)
 *  - open_by_index(index), open_by_heading(text)
 *  - is_exclusive(), is_open(group)
 *
 * @version 2025-08-26
 * @since 2025-08-26
 */
// ---------------------------------------------------------------------------------------------------------------------
// == File  /collapsible_groups.js == Time point: 2025-08-26 14:13
// ---------------------------------------------------------------------------------------------------------------------
(function (w, d) {
	'use strict';

	class WPBC_Collapsible_Groups {

		/**
		 * Create a collapsible controller for a container.
		 *
		 * @param {HTMLElement|string} root_el
		 *        The container element (or CSS selector) that wraps collapsible groups.
		 *        The container usually has the class `.wpbc_collapsible`.
		 * @param {Object} [opts={}]
		 * @param {string}  [opts.group_selector='.wpbc_ui__collapsible_group']
		 *        Selector for each collapsible group inside the container.
		 * @param {string}  [opts.header_selector='.group__header']
		 *        Selector for the clickable header inside a group.
		 * @param {string}  [opts.fields_selector='.group__fields']
		 *        Selector for the content/panel element inside a group.
		 * @param {string}  [opts.open_class='is-open']
		 *        Class name that indicates the group is open.
		 * @param {boolean} [opts.exclusive=false]
		 *        If true, only one group can be open at a time in this container.
		 *
		 * @constructor
		 * @since 2025-08-26
		 */
		constructor(root_el, opts = {}) {
			this.root = (typeof root_el === 'string') ? d.querySelector( root_el ) : root_el;
			this.opts = Object.assign( {
				group_selector : '.wpbc_ui__collapsible_group',
				header_selector: '.group__header',
				fields_selector: '.group__fields,.group__content',
				open_class     : 'is-open',
				exclusive      : false
			}, opts );

			// Bound handlers (for add/removeEventListener symmetry).
			/** @private */
			this._on_click = this._on_click.bind( this );
			/** @private */
			this._on_keydown = this._on_keydown.bind( this );

			/** @type {HTMLElement[]} @private */
			this._groups = [];
			/** @type {MutationObserver|null} @private */
			this._observer = null;
		}

		/**
		 * Initialize the controller: cache groups, attach listeners, set ARIA,
		 * and start observing DOM changes inside the container.
		 *
		 * @returns {WPBC_Collapsible_Groups} The instance (chainable).
		 * @listens click
		 * @listens keydown
		 * @since 2025-08-26
		 */
		init() {
			if ( !this.root ) {
				return this;
			}
			this._groups = Array.prototype.slice.call(
				this.root.querySelectorAll( this.opts.group_selector )
			);
			this.root.addEventListener( 'click', this._on_click, false );
			this.root.addEventListener( 'keydown', this._on_keydown, false );

			// Observe dynamic inserts/removals (Inspector re-renders).
			this._observer = new MutationObserver( () => {
				this.refresh();
			} );
			this._observer.observe( this.root, { childList: true, subtree: true } );

			this._sync_all_aria();
			return this;
		}

		/**
		 * Tear down the controller: detach listeners, stop the observer,
		 * and drop internal references.
		 *
		 * @returns {void}
		 * @since 2025-08-26
		 */
		destroy() {
			if ( !this.root ) {
				return;
			}
			this.root.removeEventListener( 'click', this._on_click, false );
			this.root.removeEventListener( 'keydown', this._on_keydown, false );
			if ( this._observer ) {
				this._observer.disconnect();
				this._observer = null;
			}
			this._groups = [];
		}

		/**
		 * Re-scan the DOM for current groups and re-apply ARIA to all of them.
		 * Useful after dynamic (re)renders.
		 *
		 * @returns {void}
		 * @since 2025-08-26
		 */
		refresh() {
			if ( !this.root ) {
				return;
			}
			this._groups = Array.prototype.slice.call(
				this.root.querySelectorAll( this.opts.group_selector )
			);
			this._sync_all_aria();
		}

		/**
		 * Check whether the container is in exclusive (accordion) mode.
		 *
		 * Order of precedence:
		 *  1) Explicit option `opts.exclusive`
		 *  2) Container has class `.wpbc_collapsible--exclusive`
		 *  3) Container matches `[data-wpbc-accordion="exclusive"]`
		 *
		 * @returns {boolean} True if exclusive mode is active.
		 * @since 2025-08-26
		 */
		is_exclusive() {
			return !!(
				this.opts.exclusive ||
				this.root.classList.contains( 'wpbc_collapsible--exclusive' ) ||
				this.root.matches( '[data-wpbc-accordion="exclusive"]' )
			);
		}

		/**
		 * Determine whether a specific group is open.
		 *
		 * @param {HTMLElement} group The group element to test.
		 * @returns {boolean} True if the group is currently open.
		 * @since 2025-08-26
		 */
		is_open(group) {
			return group.classList.contains( this.opts.open_class );
		}

		/**
		 * Open a group. Honors exclusive mode by collapsing all sibling groups
		 * (queried from the live DOM at call-time).
		 *
		 * @param {HTMLElement} group The group element to open.
		 * @param {boolean} [exclusive]
		 *        If provided, overrides container mode for this action only.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:open
		 * @since 2025-08-26
		 */
		expand(group, exclusive) {
			if ( !group ) {
				return;
			}
			const do_exclusive = (typeof exclusive === 'boolean') ? exclusive : this.is_exclusive();
			if ( do_exclusive ) {
				// Always use the live DOM, not the cached list.
				Array.prototype.forEach.call(
					this.root.querySelectorAll( this.opts.group_selector ),
					(g) => {
						if ( g !== group ) {
							this._set_open( g, false );
						}
					}
				);
			}
			this._set_open( group, true );
		}

		/**
		 * Close a group.
		 *
		 * @param {HTMLElement} group The group element to close.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:close
		 * @since 2025-08-26
		 */
		collapse(group) {
			if ( !group ) {
				return;
			}
			this._set_open( group, false );
		}

		/**
		 * Toggle a group's open/closed state.
		 *
		 * @param {HTMLElement} group The group element to toggle.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		toggle(group) {
			if ( !group ) {
				return;
			}
			this[this.is_open( group ) ? 'collapse' : 'expand']( group );
		}

		/**
		 * Open a group by its index within the container (0-based).
		 *
		 * @param {number} index Zero-based index of the group.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		open_by_index(index) {
			const group = this._groups[index];
			if ( group ) {
				this.expand( group );
			}
		}

		/**
		 * Open a group by matching text contained within the <h3> inside the header.
		 * The comparison is case-insensitive and substring-based.
		 *
		 * @param {string} text Text to match against the heading contents.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		open_by_heading(text) {
			if ( !text ) {
				return;
			}
			const t     = String( text ).toLowerCase();
			const match = this._groups.find( (g) => {
				const h = g.querySelector( this.opts.header_selector + ' h3' );
				return h && h.textContent.toLowerCase().indexOf( t ) !== -1;
			} );
			if ( match ) {
				this.expand( match );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Internal
		// -------------------------------------------------------------------------------------------------------------

		/**
		 * Delegated click handler for headers.
		 *
		 * @private
		 * @param {MouseEvent} ev The click event.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_on_click(ev) {
			const btn = ev.target.closest( this.opts.header_selector );
			if ( !btn || !this.root.contains( btn ) ) {
				return;
			}
			ev.preventDefault();
			ev.stopPropagation();
			const group = btn.closest( this.opts.group_selector );
			if ( group ) {
				this.toggle( group );
			}
		}

		/**
		 * Keyboard handler for header interactions and roving focus:
		 *  - Enter/Space toggles the active group.
		 *  - ArrowUp/ArrowDown moves focus between group headers.
		 *
		 * @private
		 * @param {KeyboardEvent} ev The keyboard event.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_on_keydown(ev) {
			const btn = ev.target.closest( this.opts.header_selector );
			if ( !btn ) {
				return;
			}

			const key = ev.key;

			// Toggle on Enter / Space.
			if ( key === 'Enter' || key === ' ' ) {
				ev.preventDefault();
				const group = btn.closest( this.opts.group_selector );
				if ( group ) {
					this.toggle( group );
				}
				return;
			}

			// Move focus with ArrowUp/ArrowDown between headers in this container.
			if ( key === 'ArrowUp' || key === 'ArrowDown' ) {
				ev.preventDefault();
				const headers = Array.prototype.map.call(
					this.root.querySelectorAll( this.opts.group_selector ),
					(g) => g.querySelector( this.opts.header_selector )
				).filter( Boolean );
				const idx     = headers.indexOf( btn );
				if ( idx !== -1 ) {
					const next_idx = (key === 'ArrowDown')
						? Math.min( headers.length - 1, idx + 1 )
						: Math.max( 0, idx - 1 );
					headers[next_idx].focus();
				}
			}
		}

		/**
		 * Apply ARIA synchronization to all known groups based on their open state.
		 *
		 * @private
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_sync_all_aria() {
			this._groups.forEach( (g) => this._sync_group_aria( g ) );
		}

		/**
		 * Sync ARIA attributes and visibility on a single group.
		 *
		 * @private
		 * @param {HTMLElement} group The group element to sync.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_sync_group_aria(group) {
			const is_open = this.is_open( group );
			const header  = group.querySelector( this.opts.header_selector );
			// Only direct children that match.
			const panels = Array.prototype.filter.call( group.children, (el) => el.matches( this.opts.fields_selector ) );

			// Header ARIA.
			if ( header ) {
				header.setAttribute( 'role', 'button' );
				header.setAttribute( 'aria-expanded', is_open ? 'true' : 'false' );

				if ( panels.length ) {
					// Ensure each panel has an id; then wire aria-controls with space-separated ids.
					const ids = panels.map( (p) => {
						if ( !p.id ) p.id = this._generate_id( 'wpbc_collapsible_panel' );
						return p.id;
					} );
					header.setAttribute( 'aria-controls', ids.join( ' ' ) );
				}
			}

			// (3) Panels ARIA + visibility.
			panels.forEach( (p) => {
				p.hidden = !is_open;                            // actual visibility.
				p.setAttribute( 'aria-hidden', is_open ? 'false' : 'true' ); // ARIA.
			} );
		}

		/**
		 * Internal state change: set a group's open/closed state, sync ARIA,
		 * manage focus on collapse, and emit a custom event.
		 *
		 * @private
		 * @param {HTMLElement} group The group element to mutate.
		 * @param {boolean} open Whether the group should be open.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:open
		 * @fires CustomEvent#wpbc:collapsible:close
		 * @since 2025-08-26
		 */
		_set_open(group, open) {
			if ( !open && group.contains( document.activeElement ) ) {
				const header = group.querySelector( this.opts.header_selector );
				header && header.focus();
			}
			group.classList.toggle( this.opts.open_class, open );
			this._sync_group_aria( group );
			const ev_name = open ? 'wpbc:collapsible:open' : 'wpbc:collapsible:close';
			group.dispatchEvent( new CustomEvent( ev_name, {
				bubbles: true,
				detail : { group, root: this.root, instance: this }
			} ) );
		}

		/**
		 * Generate a unique DOM id with the specified prefix.
		 *
		 * @private
		 * @param {string} prefix The id prefix to use.
		 * @returns {string} A unique element id not present in the document.
		 * @since 2025-08-26
		 */
		_generate_id(prefix) {
			let i = 1;
			let id;
			do {
				id = prefix + '_' + (i++);
			}
			while ( d.getElementById( id ) );
			return id;
		}
	}

	/**
	 * Auto-initialize collapsible controllers on the page.
	 * Finds top-level `.wpbc_collapsible` containers (ignoring nested ones),
	 * and instantiates {@link WPBC_Collapsible_Groups} on each.
	 *
	 * @function WPBC_Collapsible_AutoInit
	 * @returns {void}
	 * @since 2025-08-26
	 * @example
	 * // Runs automatically on DOMContentLoaded; can also be called manually:
	 * WPBC_Collapsible_AutoInit();
	 */
	function wpbc_collapsible__auto_init() {
		var ROOT  = '.wpbc_collapsible';
		var nodes = Array.prototype.slice.call( d.querySelectorAll( ROOT ) )
			.filter( function (n) {
				return !n.parentElement || !n.parentElement.closest( ROOT );
			} );

		nodes.forEach( function (node) {
			if ( node.__wpbc_collapsible_instance ) {
				return;
			}
			var exclusive = node.classList.contains( 'wpbc_collapsible--exclusive' ) || node.matches( '[data-wpbc-accordion="exclusive"]' );

			node.__wpbc_collapsible_instance = new WPBC_Collapsible_Groups( node, { exclusive } ).init();
		} );
	}

	// Export to global for manual control if needed.
	w.WPBC_Collapsible_Groups   = WPBC_Collapsible_Groups;
	w.WPBC_Collapsible_AutoInit = wpbc_collapsible__auto_init;

	// DOM-ready auto init.
	if ( d.readyState === 'loading' ) {
		d.addEventListener( 'DOMContentLoaded', wpbc_collapsible__auto_init, { once: true } );
	} else {
		wpbc_collapsible__auto_init();
	}
})( window, document );

/* globals window, document */
/**
 * WPBC Slider Length Groups
 *
 * Universal, dependency-free controller that keeps a "length" control in sync:
 *  - number input  (data-wpbc_slider_len_value)
 *  - unit select   (data-wpbc_slider_len_unit)
 *  - range slider  (data-wpbc_slider_len_range)
 *  - writer input  (data-wpbc_slider_len_writer)  [optional but recommended]
 *
 * The "writer" stores the combined value like: "100%", "420px", "12.5rem".
 * When number/unit/slider change -> writer updates and emits 'input' (bubbles).
 * When writer is changed externally (apply-from-JSON, etc) -> UI updates.
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_slider_len_group"
 *       data-wpbc_slider_len_bounds_map='{"%":{"min":30,"max":100,"step":1},"px":{"min":300,"max":2000,"step":10}}'
 *       data-wpbc_slider_len_default_unit="%">
 *    <input type="number" data-wpbc_slider_len_value>
 *    <select data-wpbc_slider_len_unit>...</select>
 *    <input type="range" data-wpbc_slider_len_range>
 *    <input type="text" data-wpbc_slider_len_writer style="display:none;">
 *  </div>
 *
 * Performance notes:
 * - MutationObserver is DISABLED by default (prevents performance issues).
 * - If your UI re-renders and inserts new groups dynamically, call:
 *     WPBC_Slider_Len_AutoInit();  OR instance.refresh();
 *   Or enable observer via: new WPBC_Slider_Len_Groups(root, { enable_observer:true }).init();
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *
 * @version 2026-01-25
 * @since   2026-01-25
 * @file    ../includes/__js/admin/slider_groups/wpbc_len_groups.js
 */
(function (w, d) {
	'use strict';

	// -------------------------------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------------------------------
	function clamp_num(v, min, max) {
		if (typeof min === 'number' && !isNaN(min)) v = Math.max(min, v);
		if (typeof max === 'number' && !isNaN(max)) v = Math.min(max, v);
		return v;
	}

	function parse_float(v) {
		var n = parseFloat(v);
		return isNaN(n) ? null : n;
	}

	function safe_json_parse(str) {
		try {
			return JSON.parse(str);
		} catch (e) {
			return null;
		}
	}

	function parse_len_combined(raw, default_unit) {
		var s = (raw == null) ? '' : String(raw).trim();
		if (!s) return { num: '', unit: default_unit || '%' };

		var m = s.match(/^\s*([\-]?\d+(?:\.\d+)?)\s*([a-z%]*)\s*$/i);
		if (!m) {
			// If it's not parseable, treat as number and keep default unit.
			return { num: s, unit: default_unit || '%' };
		}

		var num  = m[1] ? String(m[1]) : '';
		var unit = m[2] ? String(m[2]) : '';
		if (!unit) unit = default_unit || '%';

		return { num: num, unit: unit };
	}

	function build_combined(num, unit) {
		if (num == null || String(num).trim() === '') return '';
		return String(num) + String(unit || '');
	}

	function emit_input(el) {
		if (!el) return;
		el.dispatchEvent(new Event('input', { bubbles: true }));
	}

	// -------------------------------------------------------------------------------------------------
	// Controller
	// -------------------------------------------------------------------------------------------------
	class WPBC_Slider_Len_Groups {

		/**
		 * @param {HTMLElement|string} root_el Container (or selector). If omitted, uses document.
		 * @param {Object} [opts={}]
		 */
		constructor(root_el, opts) {
			this.root = root_el
				? ((typeof root_el === 'string') ? d.querySelector(root_el) : root_el)
				: d;

			this.opts = Object.assign({
				// Strict selectors (NO backward compatibility).
				group_selector  : '.wpbc_slider_len_group',
				value_selector  : '[data-wpbc_slider_len_value]',
				unit_selector   : '[data-wpbc_slider_len_unit]',
				range_selector  : '[data-wpbc_slider_len_range]',
				writer_selector : '[data-wpbc_slider_len_writer]',

				default_unit    : '%',

				fallback_bounds : {
					'px' : { min: 0,   max: 512,  step: 1   },
					'%'  : { min: 0,   max: 100,  step: 1   },
					'rem': { min: 0,   max: 10,   step: 0.1 },
					'em' : { min: 0,   max: 10,   step: 0.1 }
				},

				// Disabled by default for performance.
				enable_observer     : false,
				observer_debounce_ms: 150
			}, opts || {});

			this._on_input  = this._on_input.bind(this);
			this._on_change = this._on_change.bind(this);

			this._bounds_cache = new WeakMap(); // group -> bounds_map_object
			this._observer     = null;
			this._refresh_tmr  = null;
		}

		init() {
			if (!this.root) return this;

			this.root.addEventListener('input',  this._on_input,  true);
			this.root.addEventListener('change', this._on_change, true);

			if (this.opts.enable_observer && w.MutationObserver) {
				this._observer = new MutationObserver(() => { this._debounced_refresh(); });
				this._observer.observe(this.root === d ? d.documentElement : this.root, { childList: true, subtree: true });
			}

			this.refresh();
			return this;
		}

		destroy() {
			if (!this.root) return;

			this.root.removeEventListener('input',  this._on_input,  true);
			this.root.removeEventListener('change', this._on_change, true);

			if (this._observer) {
				this._observer.disconnect();
				this._observer = null;
			}

			if (this._refresh_tmr) {
				clearTimeout(this._refresh_tmr);
				this._refresh_tmr = null;
			}
		}

		refresh() {
			if (!this.root) return;

			var scope  = (this.root === d ? d : this.root);
			var groups = Array.prototype.slice.call(scope.querySelectorAll(this.opts.group_selector));

			for (var i = 0; i < groups.length; i++) {
				this._sync_group_from_writer(groups[i]);
				this._apply_bounds_for_current_unit(groups[i]);
			}
		}

		// -------------------------------------------------------------------------------------------------
		// Internal
		// -------------------------------------------------------------------------------------------------
		_debounced_refresh() {
			if (this._refresh_tmr) clearTimeout(this._refresh_tmr);
			this._refresh_tmr = setTimeout(() => {
				this._refresh_tmr = null;
				this.refresh();
			}, Number(this.opts.observer_debounce_ms) || 0);
		}

		_find_group(el) {
			return (el && el.closest) ? el.closest(this.opts.group_selector) : null;
		}

		_get_parts(group) {
			if (!group) return null;
			return {
				group : group,
				num   : group.querySelector(this.opts.value_selector),
				unit  : group.querySelector(this.opts.unit_selector),
				range : group.querySelector(this.opts.range_selector),
				writer: group.querySelector(this.opts.writer_selector)
			};
		}

		_get_default_unit(group) {
			var du = (group && group.getAttribute)
				? group.getAttribute('data-wpbc_slider_len_default_unit')
				: '';
			return du ? String(du) : this.opts.default_unit;
		}

		_get_bounds_map(group) {
			if (!group) return null;
			if (this._bounds_cache.has(group)) {
				return this._bounds_cache.get(group);
			}

			var raw = group.getAttribute('data-wpbc_slider_len_bounds_map');
			var map = raw ? safe_json_parse(raw) : null;
			if (!map || typeof map !== 'object') map = null;

			this._bounds_cache.set(group, map);
			return map;
		}

		_get_bounds_for_unit(group, unit) {
			var map = this._get_bounds_map(group);
			if (map && unit && map[unit]) {
				return map[unit];
			}
			return this.opts.fallback_bounds[unit] || this.opts.fallback_bounds['px'];
		}

		_apply_bounds(parts, bounds) {
			if (!parts || !bounds) return;

			var min  = (bounds.min  != null) ? Number(bounds.min)  : null;
			var max  = (bounds.max  != null) ? Number(bounds.max)  : null;
			var step = (bounds.step != null) ? Number(bounds.step) : null;

			if (parts.range) {
				if (!isNaN(min))  parts.range.min  = String(min);
				if (!isNaN(max))  parts.range.max  = String(max);
				if (!isNaN(step)) parts.range.step = String(step);
			}
			if (parts.num) {
				if (!isNaN(min))  parts.num.min  = String(min);
				if (!isNaN(max))  parts.num.max  = String(max);
				if (!isNaN(step)) parts.num.step = String(step);
			}
		}

		_apply_bounds_for_current_unit(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.unit) return;

			var unit = parts.unit.value || this._get_default_unit(group);
			var b    = this._get_bounds_for_unit(group, unit);

			this._apply_bounds(parts, b);

			// Clamp current value to new bounds.
			var v = parse_float(parts.num && parts.num.value ? parts.num.value : (parts.range ? parts.range.value : ''));
			if (v == null) return;

			var min = (b && b.min != null) ? Number(b.min) : null;
			var max = (b && b.max != null) ? Number(b.max) : null;
			v = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

			if (parts.num)   parts.num.value   = String(v);
			if (parts.range) parts.range.value = String(v);

			this._write_combined(parts, String(v), unit, /*emit*/ false);
		}

		_write_combined(parts, num, unit, emit) {
			if (!parts) return;

			var combined = build_combined(num, unit);

			if (parts.writer) {
				// Avoid recursion: mark as internal write.
				parts.writer.__wpbc_slider_len_internal = true;
				parts.writer.value = combined;
				if (emit) emit_input(parts.writer);
				parts.writer.__wpbc_slider_len_internal = false;
			} else if (parts.num) {
				// If writer is missing, at least notify via number input.
				if (emit) emit_input(parts.num);
			}
		}

		_sync_group_from_writer(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.writer) return;

			var raw = String(parts.writer.value || '').trim();
			if (!raw) return;

			var du = this._get_default_unit(group);
			var p  = parse_len_combined(raw, du);

			if (parts.unit)  parts.unit.value  = p.unit;
			if (parts.num)   parts.num.value   = p.num;
			if (parts.range) parts.range.value = p.num;
		}

		_on_input(ev) {
			var t = ev.target;
			if (!t) return;

			var group = this._find_group(t);
			if (!group) return;

			var parts = this._get_parts(group);
			if (!parts) return;

			// Writer changed externally -> update UI.
			if (parts.writer && t === parts.writer) {
				if (t.__wpbc_slider_len_internal) return;
				this._sync_group_from_writer(group);
				this._apply_bounds_for_current_unit(group);
				return;
			}

			// Slider moved -> update number + writer.
			if (t.matches && t.matches(this.opts.range_selector)) {
				if (parts.num) parts.num.value = t.value;

				var unit = (parts.unit && parts.unit.value) ? parts.unit.value : this._get_default_unit(group);
				this._write_combined(parts, t.value, unit, /*emit*/ true);
				return;
			}

			// Number typed -> update slider + writer (clamp if slider has bounds).
			if (t.matches && t.matches(this.opts.value_selector)) {
				var v = parse_float(t.value);

				if (v != null && parts.range) {
					var rmin = Number(parts.range.min);
					var rmax = Number(parts.range.max);
					v = clamp_num(v, isNaN(rmin) ? null : rmin, isNaN(rmax) ? null : rmax);

					parts.range.value = String(v);
					if (String(v) !== t.value) t.value = String(v);
				}

				var unit2 = (parts.unit && parts.unit.value) ? parts.unit.value : this._get_default_unit(group);
				this._write_combined(parts, t.value, unit2, /*emit*/ true);
			}
		}

		_on_change(ev) {
			var t = ev.target;
			if (!t) return;

			var group = this._find_group(t);
			if (!group) return;

			var parts = this._get_parts(group);
			if (!parts) return;

			// Unit changed -> update bounds + writer.
			if (t.matches && t.matches(this.opts.unit_selector)) {
				this._apply_bounds_for_current_unit(group);

				var num  = parts.num ? parts.num.value : (parts.range ? parts.range.value : '');
				var unit = t.value || this._get_default_unit(group);
				this._write_combined(parts, num, unit, /*emit*/ true);
			}
		}
	}

	// -------------------------------------------------------------------------------------------------
	// Auto-init
	// -------------------------------------------------------------------------------------------------
	function wpbc_slider_len_groups__auto_init() {
		var ROOT  = '.wpbc_slider_len_groups';
		var nodes = Array.prototype.slice.call(d.querySelectorAll(ROOT))
			.filter(function (n) { return !n.parentElement || !n.parentElement.closest(ROOT); });

		// If no explicit containers, install a single document-root instance.
		if (!nodes.length) {
			if (!d.__wpbc_slider_len_groups_global_instance) {
				d.__wpbc_slider_len_groups_global_instance = new WPBC_Slider_Len_Groups(d).init();
			}
			return;
		}

		nodes.forEach(function (node) {
			if (node.__wpbc_slider_len_groups_instance) return;
			node.__wpbc_slider_len_groups_instance = new WPBC_Slider_Len_Groups(node).init();
		});
	}

	// Export globals (manual control if needed).
	w.WPBC_Slider_Len_Groups   = WPBC_Slider_Len_Groups;
	w.WPBC_Slider_Len_AutoInit = wpbc_slider_len_groups__auto_init;

	// DOM-ready auto init.
	if (d.readyState === 'loading') {
		d.addEventListener('DOMContentLoaded', wpbc_slider_len_groups__auto_init, { once: true });
	} else {
		wpbc_slider_len_groups__auto_init();
	}

})(window, document);

/* globals window, document */
/**
 * WPBC Slider Range Groups
 *
 * Universal, dependency-free controller that keeps a "range + number" pair in sync:
 *  - number input  (data-wpbc_slider_range_value)
 *  - range slider  (data-wpbc_slider_range_range)
 *  - writer input  (data-wpbc_slider_range_writer) [optional]
 *
 * If writer exists: number/slider update writer and emit 'input' on writer (bubbles).
 * If writer is missing: emits 'input' on the number input.
 * If writer changes externally: updates number/slider.
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_slider_range_group">
 *    <input type="number" data-wpbc_slider_range_value>
 *    <input type="range"  data-wpbc_slider_range_range>
 *    <!-- optional -->
 *    <input type="text" data-wpbc_slider_range_writer style="display:none;">
 *  </div>
 *
 * Performance notes:
 * - MutationObserver is DISABLED by default.
 * - If your UI re-renders and inserts new groups dynamically, call:
 *     WPBC_Slider_Range_AutoInit(); OR instance.refresh();
 *   Or enable observer via: new WPBC_Slider_Range_Groups(root, { enable_observer:true }).init();
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *
 * @version 2026-01-25
 * @since   2026-01-25
 * @file    ../includes/__js/admin/slider_groups/wpbc_range_groups.js
 */
(function (w, d) {
	'use strict';

	// -------------------------------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------------------------------
	function clamp_num(v, min, max) {
		if (typeof min === 'number' && !isNaN(min)) v = Math.max(min, v);
		if (typeof max === 'number' && !isNaN(max)) v = Math.min(max, v);
		return v;
	}

	function parse_float(v) {
		var n = parseFloat(v);
		return isNaN(n) ? null : n;
	}

	function emit_input(el) {
		if (!el) return;
		el.dispatchEvent(new Event('input', { bubbles: true }));
	}

	// -------------------------------------------------------------------------------------------------
	// Controller
	// -------------------------------------------------------------------------------------------------
	class WPBC_Slider_Range_Groups {

		/**
		 * @param {HTMLElement|string} root_el Container (or selector). If omitted, uses document.
		 * @param {Object} [opts={}]
		 */
		constructor(root_el, opts) {
			this.root = root_el
				? ((typeof root_el === 'string') ? d.querySelector(root_el) : root_el)
				: d;

			this.opts = Object.assign({
				// Strict selectors (NO backward compatibility).
				group_selector  : '.wpbc_slider_range_group',
				value_selector  : '[data-wpbc_slider_range_value]',
				range_selector  : '[data-wpbc_slider_range_range]',
				writer_selector : '[data-wpbc_slider_range_writer]',

				// Disabled by default for performance.
				enable_observer     : false,
				observer_debounce_ms: 150
			}, opts || {});

			this._on_input  = this._on_input.bind(this);
			this._on_change = this._on_change.bind(this);

			this._observer    = null;
			this._refresh_tmr = null;
		}

		init() {
			if (!this.root) return this;

			this.root.addEventListener('input',  this._on_input,  true);
			this.root.addEventListener('change', this._on_change, true);

			if (this.opts.enable_observer && w.MutationObserver) {
				this._observer = new MutationObserver(() => { this._debounced_refresh(); });
				this._observer.observe(this.root === d ? d.documentElement : this.root, { childList: true, subtree: true });
			}

			this.refresh();
			return this;
		}

		destroy() {
			if (!this.root) return;

			this.root.removeEventListener('input',  this._on_input,  true);
			this.root.removeEventListener('change', this._on_change, true);

			if (this._observer) {
				this._observer.disconnect();
				this._observer = null;
			}

			if (this._refresh_tmr) {
				clearTimeout(this._refresh_tmr);
				this._refresh_tmr = null;
			}
		}

		refresh() {
			if (!this.root) return;

			var scope  = (this.root === d ? d : this.root);
			var groups = Array.prototype.slice.call(scope.querySelectorAll(this.opts.group_selector));

			for (var i = 0; i < groups.length; i++) {
				this._sync_from_writer(groups[i]);
				this._clamp_to_range(groups[i]);
			}
		}

		// -------------------------------------------------------------------------------------------------
		// Internal
		// -------------------------------------------------------------------------------------------------
		_debounced_refresh() {
			if (this._refresh_tmr) clearTimeout(this._refresh_tmr);
			this._refresh_tmr = setTimeout(() => {
				this._refresh_tmr = null;
				this.refresh();
			}, Number(this.opts.observer_debounce_ms) || 0);
		}

		_find_group(el) {
			return (el && el.closest) ? el.closest(this.opts.group_selector) : null;
		}

		_get_parts(group) {
			if (!group) return null;
			return {
				group : group,
				num   : group.querySelector(this.opts.value_selector),
				range : group.querySelector(this.opts.range_selector),
				writer: group.querySelector(this.opts.writer_selector)
			};
		}

		_write(parts, value, emit) {
			if (!parts) return;

			if (parts.writer) {
				parts.writer.__wpbc_slider_range_internal = true;
				parts.writer.value = String(value);
				if (emit) emit_input(parts.writer);
				parts.writer.__wpbc_slider_range_internal = false;
			} else if (parts.num) {
				// If writer is missing, at least notify via number input.
				if (emit) emit_input(parts.num);
			}
		}

		_sync_from_writer(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.writer) return;

			var raw = String(parts.writer.value || '').trim();
			if (!raw) return;

			if (parts.num)   parts.num.value   = raw;
			if (parts.range) parts.range.value = raw;
		}

		_clamp_to_range(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.range || !parts.num) return;

			var v = parse_float(parts.num.value);
			if (v == null) return;

			var min = Number(parts.range.min);
			var max = Number(parts.range.max);
			var vv  = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

			if (String(vv) !== parts.num.value) parts.num.value = String(vv);
			parts.range.value = String(vv);
		}

		_on_input(ev) {
			var t = ev.target;
			if (!t) return;

			var group = this._find_group(t);
			if (!group) return;

			var parts = this._get_parts(group);
			if (!parts) return;

			// Writer changed externally -> update UI.
			if (parts.writer && t === parts.writer) {
				if (t.__wpbc_slider_range_internal) return;
				this._sync_from_writer(group);
				this._clamp_to_range(group);
				return;
			}

			// Range moved -> update number + writer.
			if (t.matches && t.matches(this.opts.range_selector)) {
				if (parts.num) parts.num.value = t.value;
				this._write(parts, t.value, /*emit*/ true);
				return;
			}

			// Number typed -> update range + writer (clamp by slider bounds).
			if (t.matches && t.matches(this.opts.value_selector)) {
				if (parts.range) {
					var v = parse_float(t.value);
					if (v != null) {
						var min = Number(parts.range.min);
						var max = Number(parts.range.max);
						v = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

						parts.range.value = String(v);
						if (String(v) !== t.value) t.value = String(v);
					}
				}
				this._write(parts, t.value, /*emit*/ true);
			}
		}

		_on_change(ev) {
			// No special "change" handling needed currently; kept for symmetry/future.
		}
	}

	// -------------------------------------------------------------------------------------------------
	// Auto-init
	// -------------------------------------------------------------------------------------------------
	function wpbc_slider_range_groups__auto_init() {
		var ROOT  = '.wpbc_slider_range_groups';
		var nodes = Array.prototype.slice.call(d.querySelectorAll(ROOT))
			.filter(function (n) { return !n.parentElement || !n.parentElement.closest(ROOT); });

		if (!nodes.length) {
			if (!d.__wpbc_slider_range_groups_global_instance) {
				d.__wpbc_slider_range_groups_global_instance = new WPBC_Slider_Range_Groups(d).init();
			}
			return;
		}

		nodes.forEach(function (node) {
			if (node.__wpbc_slider_range_groups_instance) return;
			node.__wpbc_slider_range_groups_instance = new WPBC_Slider_Range_Groups(node).init();
		});
	}

	// Export globals.
	w.WPBC_Slider_Range_Groups   = WPBC_Slider_Range_Groups;
	w.WPBC_Slider_Range_AutoInit = wpbc_slider_range_groups__auto_init;

	if (d.readyState === 'loading') {
		d.addEventListener('DOMContentLoaded', wpbc_slider_range_groups__auto_init, { once: true });
	} else {
		wpbc_slider_range_groups__auto_init();
	}

})(window, document);

/**
 * Booking Calendar — Generic UI Tabs Utility (JS)
 *
 * Purpose: Lightweight, dependency-free tabs controller for any small tab group in admin UIs.
 * - Auto-initializes groups marked with data-wpbc-tabs.
 * - Assigns ARIA roles and toggles aria-selected/aria-hidden/tabindex.
 * - Supports keyboard navigation (Left/Right/Home/End).
 * - Public API: window.wpbc_ui_tabs.{init_on, init_group, set_active}
 * - Emits 'wpbc:tabs:change' on the group root when the active tab changes.
 *
 * Markup contract:
 * - Root:   [data-wpbc-tabs]
 * - Tabs:   [data-wpbc-tab-key="K"]
 * - Panels: [data-wpbc-tab-panel="K"]
 *
 * @package   Booking Calendar
 * @subpackage Admin\UI
 * @since     11.0.0
 * @version   1.0.0
 * @see       /includes/__js/admin/ui_tabs/ui_tabs.js
 *
 *
 * How it works:
 * - Root node must have [data-wpbc-tabs] attribute (any value).
 * - Tab buttons must carry [data-wpbc-tab-key="..."] (unique per group).
 * - Panels must carry [data-wpbc-tab-panel="..."] with matching keys.
 * - Adds WAI-ARIA roles and aria-selected/hidden wiring.
 *
 * <div data-wpbc-tabs="column-styles" data-wpbc-tab-active="1"    class="wpbc_ui_tabs_root" >
 *    <!-- Top Tabs -->
 *    <div data-wpbc-tablist="" role="tablist"                    class=" wpbc_ui_el__horis_top_bar__wrapper" >
 *        <div class="wpbc_ui_el__horis_top_bar__content">
 *            <h2 class="wpbc_ui_el__horis_nav_label">Column:</h2>
 *
 *            <div class="wpbc_ui_el__horis_nav_item wpbc_ui_el__horis_nav_item__1">
 *                <a
 *                    data-wpbc-tab-key="1"
 *                    aria-selected="true" role="tab" tabindex="0" aria-controls="wpbc_tab_panel_col_1"
 *
 *                        href="javascript:void(0);"
 *                        class="wpbc_ui_el__horis_nav_item__a wpbc_ui_el__horis_nav_item__single"
 *                        id="wpbc_tab_col_1"
 *                        title="Column 1"
 *                ><span class="wpbc_ui_el__horis_nav_title">Title 1</span></a>
 *            </div>
 *            ...
 *        </div>
 *    </div>
 *    <!-- Tabs Content -->
 *    <div class="wpbc_tab__panel group__fields" data-wpbc-tab-panel="1" id="wpbc_tab_panel_col_1" role="tabpanel" aria-labelledby="wpbc_tab_col_1">
 *        ...
 *    </div>
 *    ...
 * </div>
 *
 * Public API:
 *   - wpbc_ui_tabs.init_on(root_or_selector)   // find and init groups within a container
 *   - wpbc_ui_tabs.init_group(root_el)         // init a single group root
 *   - wpbc_ui_tabs.set_active(root_el, key)    // programmatically change active tab
 *
 * Events:
 *   - Dispatches CustomEvent 'wpbc:tabs:change' on root when tab changes:
 *       detail: { active_key: '2', prev_key: '1' }
 *
 * Switch a local (generic) tabs group to tab 3:     var group = document.querySelector('[data-wpbc-tabs="column-styles"]'); if ( group ) { wpbc_ui_tabs.set_active(group, '3'); }
 */
(function ( w ) {
	'use strict';

	if ( w.wpbc_ui_tabs ) {
		return;
	}

	/**
	 * Internal: toggle active state.
	 *
	 * @param {HTMLElement} root_el
	 * @param {string}      key
	 * @param {boolean}     should_emit
	 */
	function set_active_internal( root_el, key, should_emit ) {
		var tab_btns = root_el.querySelectorAll( '[data-wpbc-tab-key]' );
		var panels   = root_el.querySelectorAll( '[data-wpbc-tab-panel]' );

		var prev_key = root_el.getAttribute( 'data-wpbc-tab-active' ) || null;
		if ( String( prev_key ) === String( key ) ) {
			return;
		}

		// Buttons: aria + class
		for ( var i = 0; i < tab_btns.length; i++ ) {
			var btn   = tab_btns[i];
			var b_key = btn.getAttribute( 'data-wpbc-tab-key' );
			var is_on = String( b_key ) === String( key );

			btn.setAttribute( 'role', 'tab' );
			btn.setAttribute( 'aria-selected', is_on ? 'true' : 'false' );
			btn.setAttribute( 'tabindex', is_on ? '0' : '-1' );

			if ( is_on ) {
				btn.classList.add( 'active' );
			} else {
				btn.classList.remove( 'active' );
			}
		}

		// Panels: aria + visibility
		for ( var j = 0; j < panels.length; j++ ) {
			var pn   = panels[j];
			var pkey = pn.getAttribute( 'data-wpbc-tab-panel' );
			var show = String( pkey ) === String( key );

			pn.setAttribute( 'role', 'tabpanel' );
			pn.setAttribute( 'aria-hidden', show ? 'false' : 'true' );
			if ( show ) {
				pn.removeAttribute( 'hidden' );
			} else {
				pn.setAttribute( 'hidden', '' );
			}
		}

		root_el.setAttribute( 'data-wpbc-tab-active', String( key ) );

		if ( should_emit ) {
			try {
				var ev = new w.CustomEvent( 'wpbc:tabs:change', {
					bubbles : true,
					detail  : { active_key : String( key ), prev_key : prev_key }
				} );
				root_el.dispatchEvent( ev );
			} catch ( _e ) {}
		}
	}

	/**
	 * Internal: get ordered keys from buttons.
	 *
	 * @param {HTMLElement} root_el
	 * @returns {string[]}
	 */
	function get_keys( root_el ) {
		var list = [];
		var btns = root_el.querySelectorAll( '[data-wpbc-tab-key]' );
		for ( var i = 0; i < btns.length; i++ ) {
			var k = btns[i].getAttribute( 'data-wpbc-tab-key' );
			if ( k != null && k !== '' ) {
				list.push( String( k ) );
			}
		}
		return list;
	}

	/**
	 * Internal: move focus between tabs using keyboard.
	 *
	 * @param {HTMLElement} root_el
	 * @param {number}      dir  +1 (next) / -1 (prev)
	 */
	function focus_relative( root_el, dir ) {
		var keys    = get_keys( root_el );
		var current = root_el.getAttribute( 'data-wpbc-tab-active' ) || keys[0] || null;
		var idx     = Math.max( 0, keys.indexOf( String( current ) ) );
		var next    = keys[ ( idx + ( dir > 0 ? 1 : keys.length - 1 ) ) % keys.length ];

		var next_btn = root_el.querySelector( '[data-wpbc-tab-key="' + next + '"]' );
		if ( next_btn ) {
			next_btn.focus();
			set_active_internal( root_el, next, true );
		}
	}

	/**
	 * Initialize a single tabs group root.
	 *
	 * @param {HTMLElement} root_el
	 */
	function init_group( root_el ) {
		if ( ! root_el || root_el.__wpbc_tabs_inited ) {
			return;
		}
		root_el.__wpbc_tabs_inited = true;

		// Roles
		var tablist = root_el.querySelector( '[data-wpbc-tablist]' ) || root_el;
		tablist.setAttribute( 'role', 'tablist' );

		// Default active: from attribute or first button
		var keys = get_keys( root_el );
		var def  = root_el.getAttribute( 'data-wpbc-tab-active' ) || ( keys[0] || '1' );
		set_active_internal( root_el, def, false );

		// Clicks
		root_el.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest ? e.target.closest( '[data-wpbc-tab-key]' ) : null;
			if ( ! btn || ! root_el.contains( btn ) ) {
				return;
			}
			e.preventDefault();
			var key = btn.getAttribute( 'data-wpbc-tab-key' );
			if ( key != null ) {
				set_active_internal( root_el, key, true );
			}
		}, true );

		// Keyboard (Left/Right/Home/End)
		root_el.addEventListener( 'keydown', function ( e ) {
			var tgt = e.target;
			if ( ! tgt || ! tgt.hasAttribute || ! tgt.hasAttribute( 'data-wpbc-tab-key' ) ) {
				return;
			}
			switch ( e.key ) {
			case 'ArrowLeft':
				e.preventDefault(); focus_relative( root_el, -1 ); break;
			case 'ArrowRight':
				e.preventDefault(); focus_relative( root_el, +1 ); break;
			case 'Home':
				e.preventDefault(); set_active_internal( root_el, ( get_keys( root_el )[0] || '1' ), true ); break;
			case 'End':
				e.preventDefault(); var ks = get_keys( root_el ); set_active_internal( root_el, ( ks[ ks.length - 1 ] || '1' ), true ); break;
			}
		}, true );
	}

	/**
	 * Initialize all groups within a container (or document).
	 *
	 * @param {HTMLElement|string|null} container
	 */
	function init_on( container ) {
		var ctx = container ? ( typeof container === 'string' ? document.querySelector( container ) : container ) : document;
		if ( ! ctx ) {
			return;
		}
		var groups = ctx.querySelectorAll( '[data-wpbc-tabs]' );
		for ( var i = 0; i < groups.length; i++ ) {
			init_group( groups[i] );
		}
	}

	/**
	 * Programmatically set active tab by key.
	 *
	 * @param {HTMLElement} root_el
	 * @param {string|number} key
	 */
	function set_active( root_el, key ) {
		if ( root_el && root_el.hasAttribute && root_el.hasAttribute( 'data-wpbc-tabs' ) ) {
			set_active_internal( root_el, String( key ), true );
		}
	}

	// Public API (snake_case)
	w.wpbc_ui_tabs = {
		init_on    : init_on,
		init_group : init_group,
		set_active : set_active
	};

	// Auto-init on DOM ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { init_on( document ); } );
	} else {
		init_on( document );
	}

})( window );

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInVpX2VsZW1lbnRzLmpzIiwidWlfbG9hZGluZ19zcGluLmpzIiwidWlfcmFkaW9fY29udGFpbmVyLmpzIiwidWlfZnVsbF9zY3JlZW5fbW9kZS5qcyIsImdtYWlsX2NoZWNrYm94X3NlbGVjdGlvbi5qcyIsImJvb2tpbmdzX2NoZWNrYm94X3NlbGVjdGlvbi5qcyIsInVpX3NpZGViYXJfbGVmdF9fYWN0aW9ucy5qcyIsImNvcHlfdGV4dF90b19jbGlwYnJkLmpzIiwiY29sbGFwc2libGVfZ3JvdXBzLmpzIiwid3BiY19sZW5fZ3JvdXBzLmpzIiwid3BiY19yYW5nZV9ncm91cHMuanMiLCJ1aV90YWJzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDZkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3JGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2pDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM3QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNqSUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3BEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzdSQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3JIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbGdCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdFpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNyUkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6IndwYmNfYWxsX2FkbWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiXHJcbi8qKlxyXG4gKiBCbGluayBzcGVjaWZpYyBIVE1MIGVsZW1lbnQgdG8gc2V0IGF0dGVudGlvbiB0byB0aGlzIGVsZW1lbnQuXHJcbiAqXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBlbGVtZW50X3RvX2JsaW5rXHRcdCAgLSBjbGFzcyBvciBpZCBvZiBlbGVtZW50OiAnLndwYmNfd2lkZ2V0X2F2YWlsYWJsZV91bmF2YWlsYWJsZSdcclxuICogQHBhcmFtIHtpbnR9IGhvd19tYW55X3RpbWVzXHRcdFx0ICAtIDRcclxuICogQHBhcmFtIHtpbnR9IGhvd19sb25nX3RvX2JsaW5rXHRcdCAgLSAzNTBcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYmxpbmtfZWxlbWVudCggZWxlbWVudF90b19ibGluaywgaG93X21hbnlfdGltZXMgPSA0LCBob3dfbG9uZ190b19ibGluayA9IDM1MCApe1xyXG5cclxuXHRmb3IgKCBsZXQgaSA9IDA7IGkgPCBob3dfbWFueV90aW1lczsgaSsrICl7XHJcblx0XHRqUXVlcnkoIGVsZW1lbnRfdG9fYmxpbmsgKS5mYWRlT3V0KCBob3dfbG9uZ190b19ibGluayApLmZhZGVJbiggaG93X2xvbmdfdG9fYmxpbmsgKTtcclxuXHR9XHJcbiAgICBqUXVlcnkoIGVsZW1lbnRfdG9fYmxpbmsgKS5hbmltYXRlKCB7b3BhY2l0eTogMX0sIDUwMCApO1xyXG59XHJcbiIsIi8qKlxyXG4gKiAgIFN1cHBvcnQgRnVuY3Rpb25zIC0gU3BpbiBJY29uIGluIEJ1dHRvbnMgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFJlbW92ZSBzcGluIGljb24gZnJvbSAgYnV0dG9uIGFuZCBFbmFibGUgdGhpcyBidXR0b24uXHJcbiAqXHJcbiAqIEBwYXJhbSBidXR0b25fY2xpY2tlZF9lbGVtZW50X2lkXHRcdC0gSFRNTCBJRCBhdHRyaWJ1dGUgb2YgdGhpcyBidXR0b25cclxuICogQHJldHVybiBzdHJpbmdcdFx0XHRcdFx0XHQtIENTUyBjbGFzc2VzIHRoYXQgd2FzIHByZXZpb3VzbHkgaW4gYnV0dG9uIGljb25cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYnV0dG9uX19yZW1vdmVfc3BpbihidXR0b25fY2xpY2tlZF9lbGVtZW50X2lkKSB7XHJcblxyXG5cdHZhciBwcmV2aW9zX2NsYXNzZXMgPSAnJztcclxuXHRpZiAoXHJcblx0XHQodW5kZWZpbmVkICE9IGJ1dHRvbl9jbGlja2VkX2VsZW1lbnRfaWQpXHJcblx0XHQmJiAoJycgIT0gYnV0dG9uX2NsaWNrZWRfZWxlbWVudF9pZClcclxuXHQpIHtcclxuXHRcdHZhciBqRWxlbWVudCA9IGpRdWVyeSggJyMnICsgYnV0dG9uX2NsaWNrZWRfZWxlbWVudF9pZCApO1xyXG5cdFx0aWYgKCBqRWxlbWVudC5sZW5ndGggKSB7XHJcblx0XHRcdHByZXZpb3NfY2xhc3NlcyA9IHdwYmNfYnV0dG9uX2Rpc2FibGVfbG9hZGluZ19pY29uKCBqRWxlbWVudC5nZXQoIDAgKSApO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0cmV0dXJuIHByZXZpb3NfY2xhc3NlcztcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBTaG93IExvYWRpbmcgKHJvdGF0aW5nIGFycm93KSBpY29uIGZvciBidXR0b24gdGhhdCBoYXMgYmVlbiBjbGlja2VkXHJcbiAqXHJcbiAqIEBwYXJhbSB0aGlzX2J1dHRvblx0XHQtIHRoaXMgb2JqZWN0IG9mIHNwZWNpZmljIGJ1dHRvblxyXG4gKiBAcmV0dXJuIHN0cmluZ1x0XHRcdC0gQ1NTIGNsYXNzZXMgdGhhdCB3YXMgcHJldmlvdXNseSBpbiBidXR0b24gaWNvblxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19idXR0b25fZW5hYmxlX2xvYWRpbmdfaWNvbih0aGlzX2J1dHRvbikge1xyXG5cclxuXHR2YXIgakJ1dHRvbiAgICAgICAgID0galF1ZXJ5KCB0aGlzX2J1dHRvbiApO1xyXG5cdHZhciBqSWNvbiAgICAgICAgICAgPSBqQnV0dG9uLmZpbmQoICdpJyApO1xyXG5cdHZhciBwcmV2aW9zX2NsYXNzZXMgPSBqSWNvbi5hdHRyKCAnY2xhc3MnICk7XHJcblxyXG5cdGpJY29uLnJlbW92ZUNsYXNzKCkuYWRkQ2xhc3MoICdtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9yb3RhdGVfcmlnaHQgd3BiY19zcGluJyApO1x0Ly8gU2V0IFJvdGF0ZSBpY29uLlxyXG5cdC8vIGpJY29uLmFkZENsYXNzKCAnd3BiY19hbmltYXRpb25fcGF1c2UnICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gUGF1c2UgYW5pbWF0aW9uLlxyXG5cdC8vIGpJY29uLmFkZENsYXNzKCAnd3BiY191aV9yZWQnICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFNldCBpY29uIGNvbG9yIHJlZC5cclxuXHJcblx0akljb24uYXR0ciggJ3dwYmNfcHJldmlvdXNfY2xhc3MnLCBwcmV2aW9zX2NsYXNzZXMgKVxyXG5cclxuXHRqQnV0dG9uLmFkZENsYXNzKCAnZGlzYWJsZWQnICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRGlzYWJsZSBidXR0b25cclxuXHQvLyBXZSBuZWVkIHRvICBzZXQgIGhlcmUgYXR0ciBpbnN0ZWFkIG9mIHByb3AsIGJlY2F1c2UgZm9yIEEgZWxlbWVudHMsICBhdHRyaWJ1dGUgJ2Rpc2FibGVkJyBkbyAgbm90IGFkZGVkIHdpdGggakJ1dHRvbi5wcm9wKCBcImRpc2FibGVkXCIsIHRydWUgKTsuXHJcblxyXG5cdGpCdXR0b24uYXR0ciggJ3dwYmNfcHJldmlvdXNfb25jbGljaycsIGpCdXR0b24uYXR0ciggJ29uY2xpY2snICkgKTtcdFx0Ly8gU2F2ZSB0aGlzIHZhbHVlLlxyXG5cdGpCdXR0b24uYXR0ciggJ29uY2xpY2snLCAnJyApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBEaXNhYmxlIGFjdGlvbnMgXCJvbiBjbGlja1wiLlxyXG5cclxuXHRyZXR1cm4gcHJldmlvc19jbGFzc2VzO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIEhpZGUgTG9hZGluZyAocm90YXRpbmcgYXJyb3cpIGljb24gZm9yIGJ1dHRvbiB0aGF0IHdhcyBjbGlja2VkIGFuZCBzaG93IHByZXZpb3VzIGljb24gYW5kIGVuYWJsZSBidXR0b25cclxuICpcclxuICogQHBhcmFtIHRoaXNfYnV0dG9uXHRcdC0gdGhpcyBvYmplY3Qgb2Ygc3BlY2lmaWMgYnV0dG9uXHJcbiAqIEByZXR1cm4gc3RyaW5nXHRcdFx0LSBDU1MgY2xhc3NlcyB0aGF0IHdhcyBwcmV2aW91c2x5IGluIGJ1dHRvbiBpY29uXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2J1dHRvbl9kaXNhYmxlX2xvYWRpbmdfaWNvbih0aGlzX2J1dHRvbikge1xyXG5cclxuXHR2YXIgakJ1dHRvbiA9IGpRdWVyeSggdGhpc19idXR0b24gKTtcclxuXHR2YXIgakljb24gICA9IGpCdXR0b24uZmluZCggJ2knICk7XHJcblxyXG5cdHZhciBwcmV2aW9zX2NsYXNzZXMgPSBqSWNvbi5hdHRyKCAnd3BiY19wcmV2aW91c19jbGFzcycgKTtcclxuXHRpZiAoXHJcblx0XHQodW5kZWZpbmVkICE9IHByZXZpb3NfY2xhc3NlcylcclxuXHRcdCYmICgnJyAhPSBwcmV2aW9zX2NsYXNzZXMpXHJcblx0KSB7XHJcblx0XHRqSWNvbi5yZW1vdmVDbGFzcygpLmFkZENsYXNzKCBwcmV2aW9zX2NsYXNzZXMgKTtcclxuXHR9XHJcblxyXG5cdGpCdXR0b24ucmVtb3ZlQ2xhc3MoICdkaXNhYmxlZCcgKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBSZW1vdmUgRGlzYWJsZSBidXR0b24uXHJcblxyXG5cdHZhciBwcmV2aW91c19vbmNsaWNrID0gakJ1dHRvbi5hdHRyKCAnd3BiY19wcmV2aW91c19vbmNsaWNrJyApXHJcblx0aWYgKFxyXG5cdFx0KHVuZGVmaW5lZCAhPSBwcmV2aW91c19vbmNsaWNrKVxyXG5cdFx0JiYgKCcnICE9IHByZXZpb3VzX29uY2xpY2spXHJcblx0KSB7XHJcblx0XHRqQnV0dG9uLmF0dHIoICdvbmNsaWNrJywgcHJldmlvdXNfb25jbGljayApO1xyXG5cdH1cclxuXHJcblx0cmV0dXJuIHByZXZpb3NfY2xhc3NlcztcclxufVxyXG4iLCIvKipcclxuICogT24gc2VsZWN0aW9uICBvZiByYWRpbyBidXR0b24sIGFkanVzdCBhdHRyaWJ1dGVzIG9mIHJhZGlvIGNvbnRhaW5lclxyXG4gKlxyXG4gKiBAcGFyYW0gX3RoaXNcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfdWlfZWxfX3JhZGlvX2NvbnRhaW5lcl9zZWxlY3Rpb24oX3RoaXMpIHtcclxuXHJcblx0aWYgKCBqUXVlcnkoIF90aGlzICkuaXMoICc6Y2hlY2tlZCcgKSApIHtcclxuXHRcdGpRdWVyeSggX3RoaXMgKS5wYXJlbnRzKCAnLndwYmNfdWlfcmFkaW9fc2VjdGlvbicgKS5maW5kKCAnLndwYmNfdWlfcmFkaW9fY29udGFpbmVyJyApLnJlbW92ZUF0dHIoICdkYXRhLXNlbGVjdGVkJyApO1xyXG5cdFx0alF1ZXJ5KCBfdGhpcyApLnBhcmVudHMoICcud3BiY191aV9yYWRpb19jb250YWluZXI6bm90KC5kaXNhYmxlZCknICkuYXR0ciggJ2RhdGEtc2VsZWN0ZWQnLCB0cnVlICk7XHJcblx0fVxyXG5cclxuXHRpZiAoIGpRdWVyeSggX3RoaXMgKS5pcyggJzpkaXNhYmxlZCcgKSApIHtcclxuXHRcdGpRdWVyeSggX3RoaXMgKS5wYXJlbnRzKCAnLndwYmNfdWlfcmFkaW9fY29udGFpbmVyJyApLmFkZENsYXNzKCAnZGlzYWJsZWQnICk7XHJcblx0fVxyXG59XHJcblxyXG4vKipcclxuICogT24gY2xpY2sgb24gUmFkaW8gQ29udGFpbmVyLCB3ZSB3aWxsICBzZWxlY3QgIHRoZSAgcmFkaW8gYnV0dG9uICAgIGFuZCB0aGVuIGFkanVzdCBhdHRyaWJ1dGVzIG9mIHJhZGlvIGNvbnRhaW5lclxyXG4gKlxyXG4gKiBAcGFyYW0gX3RoaXNcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfdWlfZWxfX3JhZGlvX2NvbnRhaW5lcl9jbGljayhfdGhpcykge1xyXG5cclxuXHRpZiAoIGpRdWVyeSggX3RoaXMgKS5oYXNDbGFzcyggJ2Rpc2FibGVkJyApICkge1xyXG5cdFx0cmV0dXJuIGZhbHNlO1xyXG5cdH1cclxuXHJcblx0dmFyIGpfcmFkaW8gPSBqUXVlcnkoIF90aGlzICkuZmluZCggJ2lucHV0W3R5cGU9cmFkaW9dOm5vdCgud3BiYy1mb3JtLXJhZGlvLWludGVybmFsKScgKTtcclxuXHRpZiAoIGpfcmFkaW8ubGVuZ3RoICkge1xyXG5cdFx0al9yYWRpby5wcm9wKCAnY2hlY2tlZCcsIHRydWUgKS50cmlnZ2VyKCAnY2hhbmdlJyApO1xyXG5cdH1cclxuXHJcbn0iLCJcInVzZSBzdHJpY3RcIjtcclxuLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbi8vID09IEZ1bGwgU2NyZWVuICAtICBzdXBwb3J0IGZ1bmN0aW9ucyAgID09XHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG5cclxuLyoqXHJcbiAqIENoZWNrIEZ1bGwgIHNjcmVlbiBtb2RlLCAgYnkgIHJlbW92aW5nIHRvcCB0YWJcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY2hlY2tfZnVsbF9zY3JlZW5fbW9kZSgpe1xyXG5cdGlmICggalF1ZXJ5KCAnYm9keScgKS5oYXNDbGFzcyggJ3dwYmNfYWRtaW5fZnVsbF9zY3JlZW4nICkgKSB7XHJcblx0XHRqUXVlcnkoICdodG1sJyApLnJlbW92ZUNsYXNzKCAnd3AtdG9vbGJhcicgKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0alF1ZXJ5KCAnaHRtbCcgKS5hZGRDbGFzcyggJ3dwLXRvb2xiYXInICk7XHJcblx0fVxyXG5cdHdwYmNfY2hlY2tfYnV0dG9uc19tYXhfbWluX2luX2Z1bGxfc2NyZWVuX21vZGUoKTtcclxufVxyXG5cclxuZnVuY3Rpb24gd3BiY19jaGVja19idXR0b25zX21heF9taW5faW5fZnVsbF9zY3JlZW5fbW9kZSgpIHtcclxuXHRpZiAoIGpRdWVyeSggJ2JvZHknICkuaGFzQ2xhc3MoICd3cGJjX2FkbWluX2Z1bGxfc2NyZWVuJyApICkge1xyXG5cdFx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9mdWxsX3NjcmVlbicgICApLmFkZENsYXNzKCAgICAnd3BiY191aV9faGlkZScgKTtcclxuXHRcdGpRdWVyeSggJy53cGJjX3VpX190b3BfbmF2X19idG5fbm9ybWFsX3NjcmVlbicgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0fSBlbHNlIHtcclxuXHRcdGpRdWVyeSggJy53cGJjX3VpX190b3BfbmF2X19idG5fZnVsbF9zY3JlZW4nICAgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0XHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX25vcm1hbF9zY3JlZW4nICkuYWRkQ2xhc3MoICAgICd3cGJjX3VpX19oaWRlJyApO1xyXG5cdH1cclxufVxyXG5cclxualF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKSB7XHJcblx0d3BiY19jaGVja19mdWxsX3NjcmVlbl9tb2RlKCk7XHJcbn0gKTsiLCIvKipcclxuICogQ2hlY2tib3ggU2VsZWN0aW9uIGZ1bmN0aW9ucyBmb3IgTGlzdGluZy5cclxuICovXHJcblxyXG4vKipcclxuICogU2VsZWN0aW9ucyBvZiBzZXZlcmFsICBjaGVja2JveGVzIGxpa2UgaW4gZ01haWwgd2l0aCBzaGlmdCA6KVxyXG4gKiBOZWVkIHRvICBoYXZlIHRoaXMgc3RydWN0dXJlOlxyXG4gKiAud3BiY19zZWxlY3RhYmxlX3RhYmxlXHJcbiAqICAgICAgLndwYmNfc2VsZWN0YWJsZV9oZWFkXHJcbiAqICAgICAgICAgICAgICAuY2hlY2stY29sdW1uXHJcbiAqICAgICAgICAgICAgICAgICAgOmNoZWNrYm94XHJcbiAqICAgICAgLndwYmNfc2VsZWN0YWJsZV9ib2R5XHJcbiAqICAgICAgICAgIC53cGJjX3Jvd1xyXG4gKiAgICAgICAgICAgICAgLmNoZWNrLWNvbHVtblxyXG4gKiAgICAgICAgICAgICAgICAgIDpjaGVja2JveFxyXG4gKiAgICAgIC53cGJjX3NlbGVjdGFibGVfZm9vdFxyXG4gKiAgICAgICAgICAgICAgLmNoZWNrLWNvbHVtblxyXG4gKiAgICAgICAgICAgICAgICAgIDpjaGVja2JveFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19kZWZpbmVfZ21haWxfY2hlY2tib3hfc2VsZWN0aW9uKCAkICl7XHJcblxyXG5cdHZhciBjaGVja3MsIGZpcnN0LCBsYXN0LCBjaGVja2VkLCBzbGljZWQsIGxhc3RDbGlja2VkID0gZmFsc2U7XHJcblxyXG5cdC8vIENoZWNrIGFsbCBjaGVja2JveGVzLlxyXG5cdCQoICcud3BiY19zZWxlY3RhYmxlX2JvZHknICkuZmluZCggJy5jaGVjay1jb2x1bW4nICkuZmluZCggJzpjaGVja2JveCcgKS5vbihcclxuXHRcdCdjbGljaycsXHJcblx0XHRmdW5jdGlvbiAoZSkge1xyXG5cdFx0XHRpZiAoICd1bmRlZmluZWQnID09IGUuc2hpZnRLZXkgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBlLnNoaWZ0S2V5ICkge1xyXG5cdFx0XHRcdGlmICggISBsYXN0Q2xpY2tlZCApIHtcclxuXHRcdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRjaGVja3MgID0gJCggbGFzdENsaWNrZWQgKS5jbG9zZXN0KCAnLndwYmNfc2VsZWN0YWJsZV9ib2R5JyApLmZpbmQoICc6Y2hlY2tib3gnICkuZmlsdGVyKCAnOnZpc2libGU6ZW5hYmxlZCcgKTtcclxuXHRcdFx0XHRmaXJzdCAgID0gY2hlY2tzLmluZGV4KCBsYXN0Q2xpY2tlZCApO1xyXG5cdFx0XHRcdGxhc3QgICAgPSBjaGVja3MuaW5kZXgoIHRoaXMgKTtcclxuXHRcdFx0XHRjaGVja2VkID0gJCggdGhpcyApLnByb3AoICdjaGVja2VkJyApO1xyXG5cdFx0XHRcdGlmICggMCA8IGZpcnN0ICYmIDAgPCBsYXN0ICYmIGZpcnN0ICE9IGxhc3QgKSB7XHJcblx0XHRcdFx0XHRzbGljZWQgPSAobGFzdCA+IGZpcnN0KSA/IGNoZWNrcy5zbGljZSggZmlyc3QsIGxhc3QgKSA6IGNoZWNrcy5zbGljZSggbGFzdCwgZmlyc3QgKTtcclxuXHRcdFx0XHRcdHNsaWNlZC5wcm9wKFxyXG5cdFx0XHRcdFx0XHQnY2hlY2tlZCcsXHJcblx0XHRcdFx0XHRcdGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0XHRpZiAoICQoIHRoaXMgKS5jbG9zZXN0KCAnLndwYmNfcm93JyApLmlzKCAnOnZpc2libGUnICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0XHRyZXR1cm4gY2hlY2tlZDtcclxuXHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHQpLnRyaWdnZXIoICdjaGFuZ2UnICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdGxhc3RDbGlja2VkID0gdGhpcztcclxuXHJcblx0XHRcdC8vIHRvZ2dsZSBcImNoZWNrIGFsbFwiIGNoZWNrYm94ZXMuXHJcblx0XHRcdHZhciB1bmNoZWNrZWQgPSAkKCB0aGlzICkuY2xvc2VzdCggJy53cGJjX3NlbGVjdGFibGVfYm9keScgKS5maW5kKCAnOmNoZWNrYm94JyApLmZpbHRlciggJzp2aXNpYmxlOmVuYWJsZWQnICkubm90KCAnOmNoZWNrZWQnICk7XHJcblx0XHRcdCQoIHRoaXMgKS5jbG9zZXN0KCAnLndwYmNfc2VsZWN0YWJsZV90YWJsZScgKS5jaGlsZHJlbiggJy53cGJjX3NlbGVjdGFibGVfaGVhZCwgLndwYmNfc2VsZWN0YWJsZV9mb290JyApLmZpbmQoICc6Y2hlY2tib3gnICkucHJvcChcclxuXHRcdFx0XHQnY2hlY2tlZCcsXHJcblx0XHRcdFx0ZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0cmV0dXJuICgwID09PSB1bmNoZWNrZWQubGVuZ3RoKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdCkudHJpZ2dlciggJ2NoYW5nZScgKTtcclxuXHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0fVxyXG5cdCk7XHJcblxyXG5cdC8vIEhlYWQgfHwgRm9vdCBjbGlja2luZyB0byAgc2VsZWN0IC8gZGVzZWxlY3QgQUxMLlxyXG5cdCQoICcud3BiY19zZWxlY3RhYmxlX2hlYWQsIC53cGJjX3NlbGVjdGFibGVfZm9vdCcgKS5maW5kKCAnLmNoZWNrLWNvbHVtbiA6Y2hlY2tib3gnICkub24oXHJcblx0XHQnY2xpY2snLFxyXG5cdFx0ZnVuY3Rpb24gKGV2ZW50KSB7XHJcblx0XHRcdHZhciAkdGhpcyAgICAgICAgICA9ICQoIHRoaXMgKSxcclxuXHRcdFx0XHQkdGFibGUgICAgICAgICA9ICR0aGlzLmNsb3Nlc3QoICcud3BiY19zZWxlY3RhYmxlX3RhYmxlJyApLFxyXG5cdFx0XHRcdGNvbnRyb2xDaGVja2VkID0gJHRoaXMucHJvcCggJ2NoZWNrZWQnICksXHJcblx0XHRcdFx0dG9nZ2xlICAgICAgICAgPSBldmVudC5zaGlmdEtleSB8fCAkdGhpcy5kYXRhKCAnd3AtdG9nZ2xlJyApO1xyXG5cclxuXHRcdFx0JHRhYmxlLmNoaWxkcmVuKCAnLndwYmNfc2VsZWN0YWJsZV9ib2R5JyApLmZpbHRlciggJzp2aXNpYmxlJyApXHJcblx0XHRcdFx0LmZpbmQoICcuY2hlY2stY29sdW1uJyApLmZpbmQoICc6Y2hlY2tib3gnIClcclxuXHRcdFx0XHQucHJvcChcclxuXHRcdFx0XHRcdCdjaGVja2VkJyxcclxuXHRcdFx0XHRcdGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0aWYgKCAkKCB0aGlzICkuaXMoICc6aGlkZGVuLDpkaXNhYmxlZCcgKSApIHtcclxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0aWYgKCB0b2dnbGUgKSB7XHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuICEgJCggdGhpcyApLnByb3AoICdjaGVja2VkJyApO1xyXG5cdFx0XHRcdFx0XHR9IGVsc2UgaWYgKCBjb250cm9sQ2hlY2tlZCApIHtcclxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0KS50cmlnZ2VyKCAnY2hhbmdlJyApO1xyXG5cclxuXHRcdFx0JHRhYmxlLmNoaWxkcmVuKCAnLndwYmNfc2VsZWN0YWJsZV9oZWFkLCAgLndwYmNfc2VsZWN0YWJsZV9mb290JyApLmZpbHRlciggJzp2aXNpYmxlJyApXHJcblx0XHRcdFx0LmZpbmQoICcuY2hlY2stY29sdW1uJyApLmZpbmQoICc6Y2hlY2tib3gnIClcclxuXHRcdFx0XHQucHJvcChcclxuXHRcdFx0XHRcdCdjaGVja2VkJyxcclxuXHRcdFx0XHRcdGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0aWYgKCB0b2dnbGUgKSB7XHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHRcdFx0XHR9IGVsc2UgaWYgKCBjb250cm9sQ2hlY2tlZCApIHtcclxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0KTtcclxuXHRcdH1cclxuXHQpO1xyXG5cclxuXHJcblx0Ly8gVmlzdWFsbHkgIHNob3cgc2VsZWN0ZWQgYm9yZGVyLlxyXG5cdCQoICcud3BiY19zZWxlY3RhYmxlX2JvZHknICkuZmluZCggJy5jaGVjay1jb2x1bW4gOmNoZWNrYm94JyApLm9uKFxyXG5cdFx0J2NoYW5nZScsXHJcblx0XHRmdW5jdGlvbiAoZXZlbnQpIHtcclxuXHRcdFx0aWYgKCBqUXVlcnkoIHRoaXMgKS5pcyggJzpjaGVja2VkJyApICkge1xyXG5cdFx0XHRcdGpRdWVyeSggdGhpcyApLmNsb3Nlc3QoICcud3BiY19saXN0X3JvdycgKS5hZGRDbGFzcyggJ3Jvd19zZWxlY3RlZF9jb2xvcicgKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRqUXVlcnkoIHRoaXMgKS5jbG9zZXN0KCAnLndwYmNfbGlzdF9yb3cnICkucmVtb3ZlQ2xhc3MoICdyb3dfc2VsZWN0ZWRfY29sb3InICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIERpc2FibGUgdGV4dCBzZWxlY3Rpb24gd2hpbGUgcHJlc3NpbmcgJ3NoaWZ0Jy5cclxuXHRcdFx0ZG9jdW1lbnQuZ2V0U2VsZWN0aW9uKCkucmVtb3ZlQWxsUmFuZ2VzKCk7XHJcblxyXG5cdFx0XHQvLyBTaG93IG9yIGhpZGUgYnV0dG9ucyBvbiBBY3Rpb25zIHRvb2xiYXIgIGF0ICBCb29raW5nIExpc3RpbmcgIHBhZ2UsICBpZiB3ZSBoYXZlIHNvbWUgc2VsZWN0ZWQgYm9va2luZ3MuXHJcblx0XHRcdHdwYmNfc2hvd19oaWRlX2FjdGlvbl9idXR0b25zX2Zvcl9zZWxlY3RlZF9ib29raW5ncygpO1xyXG5cdFx0fVxyXG5cdCk7XHJcblxyXG5cdHdwYmNfc2hvd19oaWRlX2FjdGlvbl9idXR0b25zX2Zvcl9zZWxlY3RlZF9ib29raW5ncygpO1xyXG59XHJcbiIsIlxyXG4vKipcclxuICogR2V0IElEIGFycmF5ICBvZiBzZWxlY3RlZCBlbGVtZW50c1xyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19nZXRfc2VsZWN0ZWRfcm93X2lkKCkge1xyXG5cclxuXHR2YXIgJHRhYmxlICAgICAgPSBqUXVlcnkoICcud3BiY19fd3JhcF9fYm9va2luZ19saXN0aW5nIC53cGJjX3NlbGVjdGFibGVfdGFibGUnICk7XHJcblx0dmFyIGNoZWNrYm94ZXMgID0gJHRhYmxlLmNoaWxkcmVuKCAnLndwYmNfc2VsZWN0YWJsZV9ib2R5JyApLmZpbHRlciggJzp2aXNpYmxlJyApLmZpbmQoICcuY2hlY2stY29sdW1uJyApLmZpbmQoICc6Y2hlY2tib3gnICk7XHJcblx0dmFyIHNlbGVjdGVkX2lkID0gW107XHJcblxyXG5cdGpRdWVyeS5lYWNoKFxyXG5cdFx0Y2hlY2tib3hlcyxcclxuXHRcdGZ1bmN0aW9uIChrZXksIGNoZWNrYm94KSB7XHJcblx0XHRcdGlmICggalF1ZXJ5KCBjaGVja2JveCApLmlzKCAnOmNoZWNrZWQnICkgKSB7XHJcblx0XHRcdFx0dmFyIGVsZW1lbnRfaWQgPSB3cGJjX2dldF9yb3dfaWRfZnJvbV9lbGVtZW50KCBjaGVja2JveCApO1xyXG5cdFx0XHRcdHNlbGVjdGVkX2lkLnB1c2goIGVsZW1lbnRfaWQgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdCk7XHJcblxyXG5cdHJldHVybiBzZWxlY3RlZF9pZDtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBHZXQgSUQgb2Ygcm93LCAgYmFzZWQgb24gY2xjaWtlZCBlbGVtZW50XHJcbiAqXHJcbiAqIEBwYXJhbSB0aGlzX2luYm91bmRfZWxlbWVudCAgLSB1c3VzbGx5ICB0aGlzXHJcbiAqIEByZXR1cm5zIHtudW1iZXJ9XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2dldF9yb3dfaWRfZnJvbV9lbGVtZW50KHRoaXNfaW5ib3VuZF9lbGVtZW50KSB7XHJcblxyXG5cdHZhciBlbGVtZW50X2lkID0galF1ZXJ5KCB0aGlzX2luYm91bmRfZWxlbWVudCApLmNsb3Nlc3QoICcud3BiY19saXN0aW5nX3VzdWFsX3JvdycgKS5hdHRyKCAnaWQnICk7XHJcblxyXG5cdGVsZW1lbnRfaWQgPSBwYXJzZUludCggZWxlbWVudF9pZC5yZXBsYWNlKCAncm93X2lkXycsICcnICkgKTtcclxuXHJcblx0cmV0dXJuIGVsZW1lbnRfaWQ7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogPT0gQm9va2luZyBMaXN0aW5nID09IFNob3cgb3IgaGlkZSBidXR0b25zIG9uIEFjdGlvbnMgdG9vbGJhciAgYXQgICAgcGFnZSwgIGlmIHdlIGhhdmUgc29tZSBzZWxlY3RlZCBib29raW5ncy5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2hvd19oaWRlX2FjdGlvbl9idXR0b25zX2Zvcl9zZWxlY3RlZF9ib29raW5ncygpe1xyXG5cclxuXHR2YXIgc2VsZWN0ZWRfcm93c19hcnIgPSB3cGJjX2dldF9zZWxlY3RlZF9yb3dfaWQoKTtcclxuXHJcblx0aWYgKCBzZWxlY3RlZF9yb3dzX2Fyci5sZW5ndGggPiAwICkge1xyXG5cdFx0alF1ZXJ5KCAnLmhpZGVfYnV0dG9uX2lmX25vX3NlbGVjdGlvbicgKS5zaG93KCk7XHJcblx0fSBlbHNlIHtcclxuXHRcdGpRdWVyeSggJy5oaWRlX2J1dHRvbl9pZl9ub19zZWxlY3Rpb24nICkuaGlkZSgpO1xyXG5cdH1cclxufSIsIlwidXNlIHN0cmljdFwiO1xyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLy8gPT0gTGVmdCBCYXIgIC0gIGV4cGFuZCAvIGNvbGFwc2UgZnVuY3Rpb25zICAgPT1cclxuLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblxyXG4vKipcclxuICogRXhwYW5kIFZlcnRpY2FsIExlZnQgQmFyLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hZG1pbl91aV9fc2lkZWJhcl9sZWZ0X19kb19tYXgoKSB7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLnJlbW92ZUNsYXNzKCAnbWluIG1heCBjb21wYWN0IG5vbmUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLmFkZENsYXNzKCAnbWF4JyApO1xyXG5cdGpRdWVyeSggJy53cGJjX3VpX190b3BfbmF2X19idG5fb3Blbl9sZWZ0X3ZlcnRpY2FsX25hdicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9oaWRlX2xlZnRfdmVydGljYWxfbmF2JyApLnJlbW92ZUNsYXNzKCAnd3BiY191aV9faGlkZScgKTtcclxuXHJcblx0alF1ZXJ5KCAnLndwLWFkbWluJyApLnJlbW92ZUNsYXNzKCAnd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9taW4gd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9tYXggd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9jb21wYWN0IHdwYmNfcGFnZV93cmFwcGVyX2xlZnRfbm9uZScgKTtcclxuXHRqUXVlcnkoICcud3AtYWRtaW4nICkuYWRkQ2xhc3MoICd3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X21heCcgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEhpZGUgVmVydGljYWwgTGVmdCBCYXIuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FkbWluX3VpX19zaWRlYmFyX2xlZnRfX2RvX21pbigpIHtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkucmVtb3ZlQ2xhc3MoICdtaW4gbWF4IGNvbXBhY3Qgbm9uZScgKTtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkuYWRkQ2xhc3MoICdtaW4nICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9vcGVuX2xlZnRfdmVydGljYWxfbmF2JyApLnJlbW92ZUNsYXNzKCAnd3BiY191aV9faGlkZScgKTtcclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX2hpZGVfbGVmdF92ZXJ0aWNhbF9uYXYnICkuYWRkQ2xhc3MoICd3cGJjX3VpX19oaWRlJyApO1xyXG5cclxuXHRqUXVlcnkoICcud3AtYWRtaW4nICkucmVtb3ZlQ2xhc3MoICd3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X21pbiB3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X21heCB3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X2NvbXBhY3Qgd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9ub25lJyApO1xyXG5cdGpRdWVyeSggJy53cC1hZG1pbicgKS5hZGRDbGFzcyggJ3dwYmNfcGFnZV93cmFwcGVyX2xlZnRfbWluJyApO1xyXG59XHJcblxyXG4vKipcclxuICogQ29sYXBzZSBWZXJ0aWNhbCBMZWZ0IEJhci5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX3NpZGViYXJfbGVmdF9fZG9fY29tcGFjdCgpIHtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkucmVtb3ZlQ2xhc3MoICdtaW4gbWF4IGNvbXBhY3Qgbm9uZScgKTtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkuYWRkQ2xhc3MoICdjb21wYWN0JyApO1xyXG5cdGpRdWVyeSggJy53cGJjX3VpX190b3BfbmF2X19idG5fb3Blbl9sZWZ0X3ZlcnRpY2FsX25hdicgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9oaWRlX2xlZnRfdmVydGljYWxfbmF2JyApLmFkZENsYXNzKCAnd3BiY191aV9faGlkZScgKTtcclxuXHJcblx0alF1ZXJ5KCAnLndwLWFkbWluJyApLnJlbW92ZUNsYXNzKCAnd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9taW4gd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9tYXggd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9jb21wYWN0IHdwYmNfcGFnZV93cmFwcGVyX2xlZnRfbm9uZScgKTtcclxuXHRqUXVlcnkoICcud3AtYWRtaW4nICkuYWRkQ2xhc3MoICd3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X2NvbXBhY3QnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDb21wbGV0ZWx5IEhpZGUgVmVydGljYWwgTGVmdCBCYXIuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FkbWluX3VpX19zaWRlYmFyX2xlZnRfX2RvX2hpZGUoKSB7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLnJlbW92ZUNsYXNzKCAnbWluIG1heCBjb21wYWN0IG5vbmUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLmFkZENsYXNzKCAnbm9uZScgKTtcclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX29wZW5fbGVmdF92ZXJ0aWNhbF9uYXYnICkucmVtb3ZlQ2xhc3MoICd3cGJjX3VpX19oaWRlJyApO1xyXG5cdGpRdWVyeSggJy53cGJjX3VpX190b3BfbmF2X19idG5faGlkZV9sZWZ0X3ZlcnRpY2FsX25hdicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0Ly8gSGlkZSB0b3AgXCJNZW51XCIgYnV0dG9uIHdpdGggZGl2aWRlci5cclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX3Nob3dfbGVmdF92ZXJ0aWNhbF9uYXYsLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9zaG93X2xlZnRfdmVydGljYWxfbmF2X2RpdmlkZXInICkuYWRkQ2xhc3MoICd3cGJjX3VpX19oaWRlJyApO1xyXG5cclxuXHRqUXVlcnkoICcud3AtYWRtaW4nICkucmVtb3ZlQ2xhc3MoICd3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X21pbiB3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X21heCB3cGJjX3BhZ2Vfd3JhcHBlcl9sZWZ0X2NvbXBhY3Qgd3BiY19wYWdlX3dyYXBwZXJfbGVmdF9ub25lJyApO1xyXG5cdGpRdWVyeSggJy53cC1hZG1pbicgKS5hZGRDbGFzcyggJ3dwYmNfcGFnZV93cmFwcGVyX2xlZnRfbm9uZScgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIEFjdGlvbiBvbiBjbGljayBcIkdvIEJhY2tcIiAtIHNob3cgcm9vdCBtZW51XHJcbiAqIG9yIHNvbWUgb3RoZXIgc2VjdGlvbiBpbiBsZWZ0IHNpZGViYXIuXHJcbiAqXHJcbiAqIEBwYXJhbSBzdHJpbmcgbWVudV90b19zaG93IC0gbWVudSBzbHVnLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hZG1pbl91aV9fc2lkZWJhcl9sZWZ0X19zaG93X3NlY3Rpb24oIG1lbnVfdG9fc2hvdyApIHtcclxuXHRqUXVlcnkoICcud3BiY191aV9lbF9fdmVydF9sZWZ0X2Jhcl9fc2VjdGlvbicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnIClcclxuXHRqUXVlcnkoICcud3BiY191aV9lbF9fdmVydF9sZWZ0X2Jhcl9fc2VjdGlvbl8nICsgbWVudV90b19zaG93ICkucmVtb3ZlQ2xhc3MoICd3cGJjX3VpX19oaWRlJyApO1xyXG59XHJcblxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLy8gPT0gUmlnaHQgU2lkZSBCYXIgIC0gIGV4cGFuZCAvIGNvbGFwc2UgZnVuY3Rpb25zICAgPT1cclxuLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblxyXG4vKipcclxuICogRXhwYW5kIFZlcnRpY2FsIFJpZ2h0IEJhci5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX3NpZGViYXJfcmlnaHRfX2RvX21heCgpIHtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkucmVtb3ZlQ2xhc3MoICdtaW5fcmlnaHQgbWF4X3JpZ2h0IGNvbXBhY3RfcmlnaHQgbm9uZV9yaWdodCcgKTtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkuYWRkQ2xhc3MoICdtYXhfcmlnaHQnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9vcGVuX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9oaWRlX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBIaWRlIFZlcnRpY2FsIFJpZ2h0IEJhci5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX3NpZGViYXJfcmlnaHRfX2RvX21pbigpIHtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkucmVtb3ZlQ2xhc3MoICdtaW5fcmlnaHQgbWF4X3JpZ2h0IGNvbXBhY3RfcmlnaHQgbm9uZV9yaWdodCcgKTtcclxuXHRqUXVlcnkoICcud3BiY19zZXR0aW5nc19wYWdlX3dyYXBwZXInICkuYWRkQ2xhc3MoICdtaW5fcmlnaHQnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9vcGVuX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9oaWRlX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDb2xhcHNlIFZlcnRpY2FsIFJpZ2h0IEJhci5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX3NpZGViYXJfcmlnaHRfX2RvX2NvbXBhY3QoKSB7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLnJlbW92ZUNsYXNzKCAnbWluX3JpZ2h0IG1heF9yaWdodCBjb21wYWN0X3JpZ2h0IG5vbmVfcmlnaHQnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfc2V0dGluZ3NfcGFnZV93cmFwcGVyJyApLmFkZENsYXNzKCAnY29tcGFjdF9yaWdodCcgKTtcclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX29wZW5fcmlnaHRfdmVydGljYWxfbmF2JyApLnJlbW92ZUNsYXNzKCAnd3BiY191aV9faGlkZScgKTtcclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX2hpZGVfcmlnaHRfdmVydGljYWxfbmF2JyApLmFkZENsYXNzKCAnd3BiY191aV9faGlkZScgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIENvbXBsZXRlbHkgSGlkZSBWZXJ0aWNhbCBSaWdodCBCYXIuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FkbWluX3VpX19zaWRlYmFyX3JpZ2h0X19kb19oaWRlKCkge1xyXG5cdGpRdWVyeSggJy53cGJjX3NldHRpbmdzX3BhZ2Vfd3JhcHBlcicgKS5yZW1vdmVDbGFzcyggJ21pbl9yaWdodCBtYXhfcmlnaHQgY29tcGFjdF9yaWdodCBub25lX3JpZ2h0JyApO1xyXG5cdGpRdWVyeSggJy53cGJjX3NldHRpbmdzX3BhZ2Vfd3JhcHBlcicgKS5hZGRDbGFzcyggJ25vbmVfcmlnaHQnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9vcGVuX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfX3RvcF9uYXZfX2J0bl9oaWRlX3JpZ2h0X3ZlcnRpY2FsX25hdicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcblx0Ly8gSGlkZSB0b3AgXCJNZW51XCIgYnV0dG9uIHdpdGggZGl2aWRlci5cclxuXHRqUXVlcnkoICcud3BiY191aV9fdG9wX25hdl9fYnRuX3Nob3dfcmlnaHRfdmVydGljYWxfbmF2LC53cGJjX3VpX190b3BfbmF2X19idG5fc2hvd19yaWdodF92ZXJ0aWNhbF9uYXZfZGl2aWRlcicgKS5hZGRDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBBY3Rpb24gb24gY2xpY2sgXCJHbyBCYWNrXCIgLSBzaG93IHJvb3QgbWVudVxyXG4gKiBvciBzb21lIG90aGVyIHNlY3Rpb24gaW4gcmlnaHQgc2lkZWJhci5cclxuICpcclxuICogQHBhcmFtIHN0cmluZyBtZW51X3RvX3Nob3cgLSBtZW51IHNsdWcuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FkbWluX3VpX19zaWRlYmFyX3JpZ2h0X19zaG93X3NlY3Rpb24oIG1lbnVfdG9fc2hvdyApIHtcclxuXHRqUXVlcnkoICcud3BiY191aV9lbF9fdmVydF9yaWdodF9iYXJfX3NlY3Rpb24nICkuYWRkQ2xhc3MoICd3cGJjX3VpX19oaWRlJyApXHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfZWxfX3ZlcnRfcmlnaHRfYmFyX19zZWN0aW9uXycgKyBtZW51X3RvX3Nob3cgKS5yZW1vdmVDbGFzcyggJ3dwYmNfdWlfX2hpZGUnICk7XHJcbn1cclxuXHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4vLyA9PSBFbmQgUmlnaHQgU2lkZSBCYXIgIHNlY3Rpb24gICA9PVxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuXHJcbi8qKlxyXG4gKiBHZXQgYW5jaG9yKHMpIGFycmF5ICBmcm9tICBVUkwuXHJcbiAqIERvYzogaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvQVBJL0xvY2F0aW9uXHJcbiAqXHJcbiAqIEByZXR1cm5zIHsqW119XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX3VybF9nZXRfYW5jaG9yc19hcnIoKSB7XHJcblx0dmFyIGhhc2hlcyAgICAgICAgICAgID0gd2luZG93LmxvY2F0aW9uLmhhc2gucmVwbGFjZSggJyUyMycsICcjJyApO1xyXG5cdHZhciBoYXNoZXNfYXJyICAgICAgICA9IGhhc2hlcy5zcGxpdCggJyMnICk7XHJcblx0dmFyIHJlc3VsdCAgICAgICAgICAgID0gW107XHJcblx0dmFyIGhhc2hlc19hcnJfbGVuZ3RoID0gaGFzaGVzX2Fyci5sZW5ndGg7XHJcblxyXG5cdGZvciAoIHZhciBpID0gMDsgaSA8IGhhc2hlc19hcnJfbGVuZ3RoOyBpKysgKSB7XHJcblx0XHRpZiAoIGhhc2hlc19hcnJbaV0ubGVuZ3RoID4gMCApIHtcclxuXHRcdFx0cmVzdWx0LnB1c2goIGhhc2hlc19hcnJbaV0gKTtcclxuXHRcdH1cclxuXHR9XHJcblx0cmV0dXJuIHJlc3VsdDtcclxufVxyXG5cclxuLyoqXHJcbiAqIEF1dG8gRXhwYW5kIFNldHRpbmdzIHNlY3Rpb24gYmFzZWQgb24gVVJMIGFuY2hvciwgYWZ0ZXIgIHBhZ2UgbG9hZGVkLlxyXG4gKi9cclxualF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKSB7IHdwYmNfYWRtaW5fdWlfX2RvX2V4cGFuZF9zZWN0aW9uKCk7IHNldFRpbWVvdXQoICd3cGJjX2FkbWluX3VpX19kb19leHBhbmRfc2VjdGlvbicsIDEwICk7IH0gKTtcclxualF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKSB7IHdwYmNfYWRtaW5fdWlfX2RvX2V4cGFuZF9zZWN0aW9uKCk7IHNldFRpbWVvdXQoICd3cGJjX2FkbWluX3VpX19kb19leHBhbmRfc2VjdGlvbicsIDE1MCApOyB9ICk7XHJcblxyXG4vKipcclxuICogRXhwYW5kIHNlY3Rpb24gaW4gIEdlbmVyYWwgU2V0dGluZ3MgcGFnZSBhbmQgc2VsZWN0IE1lbnUgaXRlbS5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX2RvX2V4cGFuZF9zZWN0aW9uKCkge1xyXG5cclxuXHQvLyB3aW5kb3cubG9jYXRpb24uaGFzaCAgPSAjc2VjdGlvbl9pZCAgLyAgZG9jOiBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvTG9jYXRpb24gLlxyXG5cdHZhciBhbmNob3JzX2FyciAgICAgICAgPSB3cGJjX3VybF9nZXRfYW5jaG9yc19hcnIoKTtcclxuXHR2YXIgYW5jaG9yc19hcnJfbGVuZ3RoID0gYW5jaG9yc19hcnIubGVuZ3RoO1xyXG5cclxuXHRpZiAoIGFuY2hvcnNfYXJyX2xlbmd0aCA+IDAgKSB7XHJcblx0XHR2YXIgb25lX2FuY2hvcl9wcm9wX3ZhbHVlID0gYW5jaG9yc19hcnJbMF0uc3BsaXQoICdkb19leHBhbmRfXycgKTtcclxuXHRcdGlmICggb25lX2FuY2hvcl9wcm9wX3ZhbHVlLmxlbmd0aCA+IDEgKSB7XHJcblxyXG5cdFx0XHQvLyAnd3BiY19nZW5lcmFsX3NldHRpbmdzX2NhbGVuZGFyX21ldGFib3gnXHJcblx0XHRcdHZhciBzZWN0aW9uX3RvX3Nob3cgICAgPSBvbmVfYW5jaG9yX3Byb3BfdmFsdWVbMV07XHJcblx0XHRcdHZhciBzZWN0aW9uX2lkX3RvX3Nob3cgPSAnIycgKyBzZWN0aW9uX3RvX3Nob3c7XHJcblxyXG5cclxuXHRcdFx0Ly8gLS0gUmVtb3ZlIHNlbGVjdGVkIGJhY2tncm91bmQgaW4gYWxsIGxlZnQgIG1lbnUgIGl0ZW1zIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRqUXVlcnkoICcud3BiY191aV9lbF9fdmVydF9uYXZfaXRlbSAnICkucmVtb3ZlQ2xhc3MoICdhY3RpdmUnICk7XHJcblx0XHRcdC8vIFNldCBsZWZ0IG1lbnUgc2VsZWN0ZWQuXHJcblx0XHRcdGpRdWVyeSggJy5kb19leHBhbmRfXycgKyBzZWN0aW9uX3RvX3Nob3cgKyAnX2xpbmsnICkuYWRkQ2xhc3MoICdhY3RpdmUnICk7XHJcblx0XHRcdHZhciBzZWxlY3RlZF90aXRsZSA9IGpRdWVyeSggJy5kb19leHBhbmRfXycgKyBzZWN0aW9uX3RvX3Nob3cgKyAnX2xpbmsgYSAud3BiY191aV9lbF9fdmVydF9uYXZfdGl0bGUgJyApLnRleHQoKTtcclxuXHJcblx0XHRcdC8vIEV4cGFuZCBzZWN0aW9uLCBpZiBpdCBjb2xhcHNlZC5cclxuXHRcdFx0aWYgKCAhIGpRdWVyeSggJy5kb19leHBhbmRfXycgKyBzZWN0aW9uX3RvX3Nob3cgKyAnX2xpbmsnICkucGFyZW50cyggJy53cGJjX3VpX2VsX19sZXZlbF9fZm9sZGVyJyApLmhhc0NsYXNzKCAnZXhwYW5kZWQnICkgKSB7XHJcblx0XHRcdFx0alF1ZXJ5KCAnLndwYmNfdWlfZWxfX2xldmVsX19mb2xkZXInICkucmVtb3ZlQ2xhc3MoICdleHBhbmRlZCcgKTtcclxuXHRcdFx0XHRqUXVlcnkoICcuZG9fZXhwYW5kX18nICsgc2VjdGlvbl90b19zaG93ICsgJ19saW5rJyApLnBhcmVudHMoICcud3BiY191aV9lbF9fbGV2ZWxfX2ZvbGRlcicgKS5hZGRDbGFzcyggJ2V4cGFuZGVkJyApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyAtLSBFeHBhbmQgc2VjdGlvbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0dmFyIGNvbnRhaW5lcl90b19oaWRlX2NsYXNzID0gJy5wb3N0Ym94JztcclxuXHRcdFx0Ly8gSGlkZSBzZWN0aW9ucyAnLnBvc3Rib3gnIGluIGFkbWluIHBhZ2UgYW5kIHNob3cgc3BlY2lmaWMgb25lLlxyXG5cdFx0XHRqUXVlcnkoICcud3BiY19hZG1pbl9wYWdlICcgKyBjb250YWluZXJfdG9faGlkZV9jbGFzcyApLmhpZGUoKTtcclxuXHRcdFx0alF1ZXJ5KCAnLndwYmNfY29udGFpbmVyX2Fsd2F5c19oaWRlX19vbl9sZWZ0X25hdl9jbGljaycgKS5oaWRlKCk7XHJcblx0XHRcdGpRdWVyeSggc2VjdGlvbl9pZF90b19zaG93ICkuc2hvdygpO1xyXG5cclxuXHRcdFx0Ly8gU2hvdyBhbGwgb3RoZXIgc2VjdGlvbnMsICBpZiBwcm92aWRlZCBpbiBVUkw6IC4uP3BhZ2U9d3BiYy1zZXR0aW5ncyNkb19leHBhbmRfX3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYXBhY2l0eV9tZXRhYm94I3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYXBhY2l0eV91cGdyYWRlX21ldGFib3ggLlxyXG5cdFx0XHRmb3IgKCBsZXQgaSA9IDE7IGkgPCBhbmNob3JzX2Fycl9sZW5ndGg7IGkrKyApIHtcclxuXHRcdFx0XHRqUXVlcnkoICcjJyArIGFuY2hvcnNfYXJyW2ldICkuc2hvdygpO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIGZhbHNlICkge1xyXG5cdFx0XHRcdHZhciB0YXJnZXRPZmZzZXQgPSB3cGJjX3Njcm9sbF90byggc2VjdGlvbl9pZF90b19zaG93ICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIC0tIFNldCBWYWx1ZSB0byBJbnB1dCBhYm91dCBzZWxlY3RlZCBOYXYgZWxlbWVudCAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICAgICAgIC8vIEZpeEluOiA5LjguNi4xLlxyXG5cdFx0XHR2YXIgc2VjdGlvbl9pZF90YWIgPSBzZWN0aW9uX2lkX3RvX3Nob3cuc3Vic3RyaW5nKCAwLCBzZWN0aW9uX2lkX3RvX3Nob3cubGVuZ3RoIC0gOCApICsgJ190YWInO1xyXG5cdFx0XHRpZiAoIGNvbnRhaW5lcl90b19oaWRlX2NsYXNzID09IHNlY3Rpb25faWRfdG9fc2hvdyApIHtcclxuXHRcdFx0XHRzZWN0aW9uX2lkX3RhYiA9ICcjd3BiY19nZW5lcmFsX3NldHRpbmdzX2FsbF90YWInXHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCAnI3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYXBhY2l0eV9tZXRhYm94LCN3cGJjX2dlbmVyYWxfc2V0dGluZ3NfY2FwYWNpdHlfdXBncmFkZV9tZXRhYm94JyA9PSBzZWN0aW9uX2lkX3RvX3Nob3cgKSB7XHJcblx0XHRcdFx0c2VjdGlvbl9pZF90YWIgPSAnI3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYXBhY2l0eV90YWInXHJcblx0XHRcdH1cclxuXHRcdFx0alF1ZXJ5KCAnI2Zvcm1fdmlzaWJsZV9zZWN0aW9uJyApLnZhbCggc2VjdGlvbl9pZF90YWIgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBMaWtlIGJsaW5raW5nIHNvbWUgZWxlbWVudHMuXHJcblx0XHR3cGJjX2FkbWluX3VpX19kb19fYW5jaG9yX19hbm90aGVyX2FjdGlvbnMoKTtcclxuXHR9XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX2lzX2luX21vYmlsZV9zY3JlZW5fc2l6ZSgpIHtcclxuXHRyZXR1cm4gd3BiY19hZG1pbl91aV9faXNfaW5fdGhpc19zY3JlZW5fc2l6ZSggNjA1ICk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX2lzX2luX3RoaXNfc2NyZWVuX3NpemUoc2l6ZSkge1xyXG5cdHJldHVybiAod2luZG93LnNjcmVlbi53aWR0aCA8PSBzaXplKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIE9wZW4gc2V0dGluZ3MgcGFnZSAgfCAgRXhwYW5kIHNlY3Rpb24gIHwgIFNlbGVjdCBNZW51IGl0ZW0uXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FkbWluX3VpX19kb19fb3Blbl91cmxfX2V4cGFuZF9zZWN0aW9uKHVybCwgc2VjdGlvbl9pZCkge1xyXG5cclxuXHQvLyB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHVybCArICcmZG9fZXhwYW5kPScgKyBzZWN0aW9uX2lkICsgJyNkb19leHBhbmRfXycgKyBzZWN0aW9uX2lkOyAvLy5cclxuXHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHVybCArICcjZG9fZXhwYW5kX18nICsgc2VjdGlvbl9pZDtcclxuXHJcblx0aWYgKCB3cGJjX2FkbWluX3VpX19pc19pbl9tb2JpbGVfc2NyZWVuX3NpemUoKSApIHtcclxuXHRcdHdwYmNfYWRtaW5fdWlfX3NpZGViYXJfbGVmdF9fZG9fbWluKCk7XHJcblx0fVxyXG5cclxuXHR3cGJjX2FkbWluX3VpX19kb19leHBhbmRfc2VjdGlvbigpO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIENoZWNrICBmb3IgT3RoZXIgYWN0aW9uczogIExpa2UgYmxpbmtpbmcgc29tZSBlbGVtZW50cyBpbiBzZXR0aW5ncyBwYWdlLiBFLmcuIERheXMgc2VsZWN0aW9uICBvciAgY2hhbmdlLW92ZXIgZGF5cy5cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRtaW5fdWlfX2RvX19hbmNob3JfX2Fub3RoZXJfYWN0aW9ucygpIHtcclxuXHJcblx0dmFyIGFuY2hvcnNfYXJyICAgICAgICA9IHdwYmNfdXJsX2dldF9hbmNob3JzX2FycigpO1xyXG5cdHZhciBhbmNob3JzX2Fycl9sZW5ndGggPSBhbmNob3JzX2Fyci5sZW5ndGg7XHJcblxyXG5cdC8vIE90aGVyIGFjdGlvbnM6ICBMaWtlIGJsaW5raW5nIHNvbWUgZWxlbWVudHMuXHJcblx0Zm9yICggdmFyIGkgPSAwOyBpIDwgYW5jaG9yc19hcnJfbGVuZ3RoOyBpKysgKSB7XHJcblxyXG5cdFx0dmFyIHRoaXNfYW5jaG9yID0gYW5jaG9yc19hcnJbaV07XHJcblxyXG5cdFx0dmFyIHRoaXNfYW5jaG9yX3Byb3BfdmFsdWUgPSB0aGlzX2FuY2hvci5zcGxpdCggJ2RvX290aGVyX2FjdGlvbnNfXycgKTtcclxuXHJcblx0XHRpZiAoIHRoaXNfYW5jaG9yX3Byb3BfdmFsdWUubGVuZ3RoID4gMSApIHtcclxuXHJcblx0XHRcdHZhciBzZWN0aW9uX2FjdGlvbiA9IHRoaXNfYW5jaG9yX3Byb3BfdmFsdWVbMV07XHJcblxyXG5cdFx0XHRzd2l0Y2ggKCBzZWN0aW9uX2FjdGlvbiApIHtcclxuXHJcblx0XHRcdFx0Y2FzZSAnYmxpbmtfZGF5X3NlbGVjdGlvbnMnOlxyXG5cdFx0XHRcdFx0Ly8gd3BiY191aV9zZXR0aW5nc19fcGFuZWxfX2NsaWNrKCAnI3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYWxlbmRhcl90YWIgYScsICcjd3BiY19nZW5lcmFsX3NldHRpbmdzX2NhbGVuZGFyX21ldGFib3gnLCAnRGF5cyBTZWxlY3Rpb24nICk7LlxyXG5cdFx0XHRcdFx0d3BiY19ibGlua19lbGVtZW50KCAnLndwYmNfdHJfc2V0X2dlbl9ib29raW5nX3R5cGVfb2ZfZGF5X3NlbGVjdGlvbnMnLCA0LCAzNTAgKTtcclxuXHRcdFx0XHRcdFx0d3BiY19zY3JvbGxfdG8oICcud3BiY190cl9zZXRfZ2VuX2Jvb2tpbmdfdHlwZV9vZl9kYXlfc2VsZWN0aW9ucycgKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRjYXNlICdibGlua19jaGFuZ2Vfb3Zlcl9kYXlzJzpcclxuXHRcdFx0XHRcdC8vIHdwYmNfdWlfc2V0dGluZ3NfX3BhbmVsX19jbGljayggJyN3cGJjX2dlbmVyYWxfc2V0dGluZ3NfY2FsZW5kYXJfdGFiIGEnLCAnI3dwYmNfZ2VuZXJhbF9zZXR0aW5nc19jYWxlbmRhcl9tZXRhYm94JywgJ0NoYW5nZW92ZXIgRGF5cycgKTsuXHJcblx0XHRcdFx0XHR3cGJjX2JsaW5rX2VsZW1lbnQoICcud3BiY190cl9zZXRfZ2VuX2Jvb2tpbmdfcmFuZ2Vfc2VsZWN0aW9uX3RpbWVfaXNfYWN0aXZlJywgNCwgMzUwICk7XHJcblx0XHRcdFx0XHRcdHdwYmNfc2Nyb2xsX3RvKCAnLndwYmNfdHJfc2V0X2dlbl9ib29raW5nX3JhbmdlX3NlbGVjdGlvbl90aW1lX2lzX2FjdGl2ZScgKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRjYXNlICdibGlua19jYXB0Y2hhJzpcclxuXHRcdFx0XHRcdHdwYmNfYmxpbmtfZWxlbWVudCggJy53cGJjX3RyX3NldF9nZW5fYm9va2luZ19pc191c2VfY2FwdGNoYScsIDQsIDM1MCApO1xyXG5cdFx0XHRcdFx0XHR3cGJjX3Njcm9sbF90byggJy53cGJjX3RyX3NldF9nZW5fYm9va2luZ19pc191c2VfY2FwdGNoYScgKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRkZWZhdWx0OlxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG59IiwiLyoqXHJcbiAqIENvcHkgdHh0IHRvIGNsaXBicmQgZnJvbSBUZXh0IGZpZWxkcy5cclxuICpcclxuICogQHBhcmFtIGh0bWxfZWxlbWVudF9pZCAgLSBlLmcuICdkYXRhX2ZpZWxkJ1xyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY29weV90ZXh0X3RvX2NsaXBicmRfZnJvbV9lbGVtZW50KCBodG1sX2VsZW1lbnRfaWQgKSB7XHJcblx0Ly8gR2V0IHRoZSB0ZXh0IGZpZWxkLlxyXG5cdHZhciBjb3B5VGV4dCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBodG1sX2VsZW1lbnRfaWQgKTtcclxuXHJcblx0Ly8gU2VsZWN0IHRoZSB0ZXh0IGZpZWxkLlxyXG5cdGNvcHlUZXh0LnNlbGVjdCgpO1xyXG5cdGNvcHlUZXh0LnNldFNlbGVjdGlvblJhbmdlKCAwLCA5OTk5OSApOyAvLyBGb3IgbW9iaWxlIGRldmljZXMuXHJcblxyXG5cdC8vIENvcHkgdGhlIHRleHQgaW5zaWRlIHRoZSB0ZXh0IGZpZWxkLlxyXG5cdHZhciBpc19jb3BpZWQgPSB3cGJjX2NvcHlfdGV4dF90b19jbGlwYnJkKCBjb3B5VGV4dC52YWx1ZSApO1xyXG5cdGlmICggISBpc19jb3BpZWQgKSB7XHJcblx0XHRjb25zb2xlLmVycm9yKCAnT29wcywgdW5hYmxlIHRvIGNvcHknLCBjb3B5VGV4dC52YWx1ZSApO1xyXG5cdH1cclxuXHRyZXR1cm4gaXNfY29waWVkO1xyXG59XHJcblxyXG4vKipcclxuICogQ29weSB0eHQgdG8gY2xpcGJyZC5cclxuICpcclxuICogQHBhcmFtIHRleHRcclxuICogQHJldHVybnMge2Jvb2xlYW59XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NvcHlfdGV4dF90b19jbGlwYnJkKHRleHQpIHtcclxuXHJcblx0aWYgKCAhIG5hdmlnYXRvci5jbGlwYm9hcmQgKSB7XHJcblx0XHRyZXR1cm4gd3BiY19mYWxsYmFja19jb3B5X3RleHRfdG9fY2xpcGJyZCggdGV4dCApO1xyXG5cdH1cclxuXHJcblx0bmF2aWdhdG9yLmNsaXBib2FyZC53cml0ZVRleHQoIHRleHQgKS50aGVuKFxyXG5cdFx0ZnVuY3Rpb24gKCkge1xyXG5cdFx0XHQvLyBjb25zb2xlLmxvZyggJ0FzeW5jOiBDb3B5aW5nIHRvIGNsaXBib2FyZCB3YXMgc3VjY2Vzc2Z1bCEnICk7LlxyXG5cdFx0XHRyZXR1cm4gIHRydWU7XHJcblx0XHR9LFxyXG5cdFx0ZnVuY3Rpb24gKGVycikge1xyXG5cdFx0XHQvLyBjb25zb2xlLmVycm9yKCAnQXN5bmM6IENvdWxkIG5vdCBjb3B5IHRleHQ6ICcsIGVyciApOy5cclxuXHRcdFx0cmV0dXJuICBmYWxzZTtcclxuXHRcdH1cclxuXHQpO1xyXG59XHJcblxyXG4vKipcclxuICogQ29weSB0eHQgdG8gY2xpcGJyZCAtIGRlcHJpY2F0ZWQgbWV0aG9kLlxyXG4gKlxyXG4gKiBAcGFyYW0gdGV4dFxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfZmFsbGJhY2tfY29weV90ZXh0X3RvX2NsaXBicmQoIHRleHQgKSB7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gdmFyIHRleHRBcmVhICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCBcInRleHRhcmVhXCIgKTtcclxuXHQvLyB0ZXh0QXJlYS52YWx1ZSA9IHRleHQ7XHJcblx0Ly9cclxuXHQvLyAvLyBBdm9pZCBzY3JvbGxpbmcgdG8gYm90dG9tLlxyXG5cdC8vIHRleHRBcmVhLnN0eWxlLnRvcCAgICAgID0gXCIwXCI7XHJcblx0Ly8gdGV4dEFyZWEuc3R5bGUubGVmdCAgICAgPSBcIjBcIjtcclxuXHQvLyB0ZXh0QXJlYS5zdHlsZS5wb3NpdGlvbiA9IFwiZml4ZWRcIjtcclxuXHQvLyB0ZXh0QXJlYS5zdHlsZS56SW5kZXggICA9IFwiOTk5OTk5OTk5XCI7XHJcblx0Ly8gZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZCggdGV4dEFyZWEgKTtcclxuXHQvLyB0ZXh0QXJlYS5mb2N1cygpO1xyXG5cdC8vIHRleHRBcmVhLnNlbGVjdCgpO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIE5vdyBnZXQgaXQgYXMgSFRNTCAgKG9yaWdpbmFsIGhlcmUgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS9xdWVzdGlvbnMvMzQxOTE3ODAvamF2YXNjcmlwdC1jb3B5LXN0cmluZy10by1jbGlwYm9hcmQtYXMtdGV4dC1odG1sICkuXHJcblxyXG5cdC8vIFsxXSAtIENyZWF0ZSBjb250YWluZXIgZm9yIHRoZSBIVE1MLlxyXG5cdHZhciBjb250YWluZXIgICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xyXG5cdGNvbnRhaW5lci5pbm5lckhUTUwgPSB0ZXh0O1xyXG5cclxuXHQvLyBbMl0gLSBIaWRlIGVsZW1lbnQuXHJcblx0Y29udGFpbmVyLnN0eWxlLnBvc2l0aW9uICAgICAgPSAnZml4ZWQnO1xyXG5cdGNvbnRhaW5lci5zdHlsZS5wb2ludGVyRXZlbnRzID0gJ25vbmUnO1xyXG5cdGNvbnRhaW5lci5zdHlsZS5vcGFjaXR5ICAgICAgID0gMDtcclxuXHJcblx0Ly8gRGV0ZWN0IGFsbCBzdHlsZSBzaGVldHMgb2YgdGhlIHBhZ2UuXHJcblx0dmFyIGFjdGl2ZVNoZWV0cyA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKCBkb2N1bWVudC5zdHlsZVNoZWV0cyApLmZpbHRlcihcclxuXHRcdGZ1bmN0aW9uIChzaGVldCkge1xyXG5cdFx0XHRyZXR1cm4gISBzaGVldC5kaXNhYmxlZDtcclxuXHRcdH1cclxuXHQpO1xyXG5cclxuXHQvLyBbM10gLSBNb3VudCB0aGUgY29udGFpbmVyIHRvIHRoZSBET00gdG8gbWFrZSBgY29udGVudFdpbmRvd2AgYXZhaWxhYmxlLlxyXG5cdGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQoIGNvbnRhaW5lciApO1xyXG5cclxuXHQvLyBbNF0gLSBDb3B5IHRvIGNsaXBib2FyZC5cclxuXHR3aW5kb3cuZ2V0U2VsZWN0aW9uKCkucmVtb3ZlQWxsUmFuZ2VzKCk7XHJcblxyXG5cdHZhciByYW5nZSA9IGRvY3VtZW50LmNyZWF0ZVJhbmdlKCk7XHJcblx0cmFuZ2Uuc2VsZWN0Tm9kZSggY29udGFpbmVyICk7XHJcblx0d2luZG93LmdldFNlbGVjdGlvbigpLmFkZFJhbmdlKCByYW5nZSApO1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdHZhciByZXN1bHQgPSBmYWxzZTtcclxuXHJcblx0dHJ5IHtcclxuXHRcdHJlc3VsdCA9IGRvY3VtZW50LmV4ZWNDb21tYW5kKCAnY29weScgKTtcclxuXHRcdC8vIGNvbnNvbGUubG9nKCAnRmFsbGJhY2s6IENvcHlpbmcgdGV4dCBjb21tYW5kIHdhcyAnICsgbXNnICk7IC8vLlxyXG5cdH0gY2F0Y2ggKCBlcnIgKSB7XHJcblx0XHQvLyBjb25zb2xlLmVycm9yKCAnRmFsbGJhY2s6IE9vcHMsIHVuYWJsZSB0byBjb3B5JywgZXJyICk7IC8vLlxyXG5cdH1cclxuXHQvLyBkb2N1bWVudC5ib2R5LnJlbW92ZUNoaWxkKCB0ZXh0QXJlYSApOyAvLy5cclxuXHJcblx0Ly8gWzUuNF0gLSBFbmFibGUgQ1NTLlxyXG5cdHZhciBhY3RpdmVTaGVldHNfbGVuZ3RoID0gYWN0aXZlU2hlZXRzLmxlbmd0aDtcclxuXHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBhY3RpdmVTaGVldHNfbGVuZ3RoOyBpKysgKSB7XHJcblx0XHRhY3RpdmVTaGVldHNbaV0uZGlzYWJsZWQgPSBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8vIFs2XSAtIFJlbW92ZSB0aGUgY29udGFpbmVyXHJcblx0ZG9jdW1lbnQuYm9keS5yZW1vdmVDaGlsZCggY29udGFpbmVyICk7XHJcblxyXG5cdHJldHVybiAgcmVzdWx0O1xyXG59IiwiLyoqXHJcbiAqIFdQQkMgQ29sbGFwc2libGUgR3JvdXBzXHJcbiAqXHJcbiAqIFVuaXZlcnNhbCwgZGVwZW5kZW5jeS1mcmVlIGNvbnRyb2xsZXIgZm9yIGV4cGFuZGluZy9jb2xsYXBzaW5nIGdyb3VwZWQgc2VjdGlvbnMgaW4gcmlnaHQtc2lkZSBwYW5lbHMgKEluc3BlY3Rvci9MaWJyYXJ5L0Zvcm0gU2V0dGluZ3MsIG9yIGFueSBvdGhlciBXUEJDIHBhZ2UpLlxyXG4gKlxyXG4gKiBcdFx0PT09IEhvdyB0byB1c2UgaXQgKHF1aWNrKSA/ID09PVxyXG4gKlxyXG4gKlx0XHQtLSAxLiBNYXJrdXAgKGluZGVwZW5kZW50IG1vZGU6IG11bHRpcGxlIG9wZW4gYWxsb3dlZCkgLS1cclxuICpcdFx0XHQ8ZGl2IGNsYXNzPVwid3BiY19jb2xsYXBzaWJsZVwiPlxyXG4gKlx0XHRcdCAgPHNlY3Rpb24gY2xhc3M9XCJ3cGJjX3VpX19jb2xsYXBzaWJsZV9ncm91cCBpcy1vcGVuXCI+XHJcbiAqXHRcdFx0XHQ8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImdyb3VwX19oZWFkZXJcIj48aDM+R2VuZXJhbDwvaDM+PC9idXR0b24+XHJcbiAqXHRcdFx0XHQ8ZGl2IGNsYXNzPVwiZ3JvdXBfX2ZpZWxkc1wiPuKApjwvZGl2PlxyXG4gKlx0XHRcdCAgPC9zZWN0aW9uPlxyXG4gKlx0XHRcdCAgPHNlY3Rpb24gY2xhc3M9XCJ3cGJjX3VpX19jb2xsYXBzaWJsZV9ncm91cFwiPlxyXG4gKlx0XHRcdFx0PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJncm91cF9faGVhZGVyXCI+PGgzPkFkdmFuY2VkPC9oMz48L2J1dHRvbj5cclxuICpcdFx0XHRcdDxkaXYgY2xhc3M9XCJncm91cF9fZmllbGRzXCI+4oCmPC9kaXY+XHJcbiAqXHRcdFx0ICA8L3NlY3Rpb24+XHJcbiAqXHRcdFx0PC9kaXY+XHJcbiAqXHJcbiAqXHRcdC0tIDIuIEV4Y2x1c2l2ZS9hY2NvcmRpb24gbW9kZSAob25lIG9wZW4gYXQgYSB0aW1lKSAtLVxyXG4gKlx0XHRcdDxkaXYgY2xhc3M9XCJ3cGJjX2NvbGxhcHNpYmxlIHdwYmNfY29sbGFwc2libGUtLWV4Y2x1c2l2ZVwiPuKApjwvZGl2PlxyXG4gKlxyXG4gKlx0XHQtLSAzLiBBdXRvLWluaXQgLS1cclxuICpcdFx0XHRUaGUgc2NyaXB0IGF1dG8taW5pdGlhbGl6ZXMgb24gRE9NQ29udGVudExvYWRlZC4gTm8gZXh0cmEgY29kZSBuZWVkZWQuXHJcbiAqXHJcbiAqXHRcdC0tIDQuIFByb2dyYW1tYXRpYyBjb250cm9sIChvcHRpb25hbClcclxuICpcdFx0XHRjb25zdCByb290ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3dwYmNfYmZiX19pbnNwZWN0b3InKTtcclxuICpcdFx0XHRjb25zdCBhcGkgID0gcm9vdC5fX3dwYmNfY29sbGFwc2libGVfaW5zdGFuY2U7IC8vIHNldCBieSBhdXRvLWluaXRcclxuICpcclxuICpcdFx0XHRhcGkub3Blbl9ieV9oZWFkaW5nKCdWYWxpZGF0aW9uJyk7IC8vIG9wZW4gYnkgaGVhZGluZyB0ZXh0XHJcbiAqXHRcdFx0YXBpLm9wZW5fYnlfaW5kZXgoMCk7ICAgICAgICAgICAgICAvLyBvcGVuIHRoZSBmaXJzdCBncm91cFxyXG4gKlxyXG4gKlx0XHQtLSA1Lkxpc3RlbiB0byBldmVudHMgKGUuZy4sIHRvIHBlcnNpc3Qg4oCcb3BlbiBncm91cOKAnSBzdGF0ZSkgLS1cclxuICpcdFx0XHRyb290LmFkZEV2ZW50TGlzdGVuZXIoJ3dwYmM6Y29sbGFwc2libGU6b3BlbicsICAoZSkgPT4geyBjb25zb2xlLmxvZyggIGUuZGV0YWlsLmdyb3VwICk7IH0pO1xyXG4gKlx0XHRcdHJvb3QuYWRkRXZlbnRMaXN0ZW5lcignd3BiYzpjb2xsYXBzaWJsZTpjbG9zZScsIChlKSA9PiB7IGNvbnNvbGUubG9nKCAgZS5kZXRhaWwuZ3JvdXAgKTsgfSk7XHJcbiAqXHJcbiAqXHJcbiAqXHJcbiAqIE1hcmt1cCBleHBlY3RhdGlvbnMgKG1pbmltYWwpOlxyXG4gKiAgPGRpdiBjbGFzcz1cIndwYmNfY29sbGFwc2libGUgW3dwYmNfY29sbGFwc2libGUtLWV4Y2x1c2l2ZV1cIj5cclxuICogICAgPHNlY3Rpb24gY2xhc3M9XCJ3cGJjX3VpX19jb2xsYXBzaWJsZV9ncm91cCBbaXMtb3Blbl1cIj5cclxuICogICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImdyb3VwX19oZWFkZXJcIj4gLi4uIDwvYnV0dG9uPlxyXG4gKiAgICAgIDxkaXYgY2xhc3M9XCJncm91cF9fZmllbGRzXCI+IC4uLiA8L2Rpdj5cclxuICogICAgPC9zZWN0aW9uPlxyXG4gKiAgICAuLi4gbW9yZSA8c2VjdGlvbj4gLi4uXHJcbiAqICA8L2Rpdj5cclxuICpcclxuICogTm90ZXM6XHJcbiAqICAtIEFkZCBgaXMtb3BlbmAgdG8gYW55IHNlY3Rpb24geW91IHdhbnQgaW5pdGlhbGx5IGV4cGFuZGVkLlxyXG4gKiAgLSBBZGQgYHdwYmNfY29sbGFwc2libGUtLWV4Y2x1c2l2ZWAgdG8gdGhlIGNvbnRhaW5lciBmb3IgXCJvcGVuIG9uZSBhdCBhIHRpbWVcIiBiZWhhdmlvci5cclxuICogIC0gV29ya3Mgd2l0aCB5b3VyIGV4aXN0aW5nIEJGQiBtYXJrdXAgKGNsYXNzZXMgdXNlZCB0aGVyZSBhcmUgdGhlIGRlZmF1bHRzKS5cclxuICpcclxuICogQWNjZXNzaWJpbGl0eTpcclxuICogIC0gU2V0cyBhcmlhLWV4cGFuZGVkIG9uIC5ncm91cF9faGVhZGVyXHJcbiAqICAtIFNldHMgYXJpYS1oaWRkZW4gKyBbaGlkZGVuXSBvbiAuZ3JvdXBfX2ZpZWxkc1xyXG4gKiAgLSBBcnJvd1VwL0Fycm93RG93biBtb3ZlIGZvY3VzIGJldHdlZW4gaGVhZGVyczsgRW50ZXIvU3BhY2UgdG9nZ2xlc1xyXG4gKlxyXG4gKiBFdmVudHMgKGJ1YmJsZXMgZnJvbSB0aGUgPHNlY3Rpb24+KTpcclxuICogIC0gJ3dwYmM6Y29sbGFwc2libGU6b3BlbicgIChkZXRhaWw6IHsgZ3JvdXAsIHJvb3QsIGluc3RhbmNlIH0pXHJcbiAqICAtICd3cGJjOmNvbGxhcHNpYmxlOmNsb3NlJyAoZGV0YWlsOiB7IGdyb3VwLCByb290LCBpbnN0YW5jZSB9KVxyXG4gKlxyXG4gKiBQdWJsaWMgQVBJIChpbnN0YW5jZSBtZXRob2RzKTpcclxuICogIC0gaW5pdCgpLCBkZXN0cm95KCksIHJlZnJlc2goKVxyXG4gKiAgLSBleHBhbmQoZ3JvdXAsIFtleGNsdXNpdmVdKSwgY29sbGFwc2UoZ3JvdXApLCB0b2dnbGUoZ3JvdXApXHJcbiAqICAtIG9wZW5fYnlfaW5kZXgoaW5kZXgpLCBvcGVuX2J5X2hlYWRpbmcodGV4dClcclxuICogIC0gaXNfZXhjbHVzaXZlKCksIGlzX29wZW4oZ3JvdXApXHJcbiAqXHJcbiAqIEB2ZXJzaW9uIDIwMjUtMDgtMjZcclxuICogQHNpbmNlIDIwMjUtMDgtMjZcclxuICovXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vLyA9PSBGaWxlICAvY29sbGFwc2libGVfZ3JvdXBzLmpzID09IFRpbWUgcG9pbnQ6IDIwMjUtMDgtMjYgMTQ6MTNcclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbihmdW5jdGlvbiAodywgZCkge1xyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0Y2xhc3MgV1BCQ19Db2xsYXBzaWJsZV9Hcm91cHMge1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ3JlYXRlIGEgY29sbGFwc2libGUgY29udHJvbGxlciBmb3IgYSBjb250YWluZXIuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudHxzdHJpbmd9IHJvb3RfZWxcclxuXHRcdCAqICAgICAgICBUaGUgY29udGFpbmVyIGVsZW1lbnQgKG9yIENTUyBzZWxlY3RvcikgdGhhdCB3cmFwcyBjb2xsYXBzaWJsZSBncm91cHMuXHJcblx0XHQgKiAgICAgICAgVGhlIGNvbnRhaW5lciB1c3VhbGx5IGhhcyB0aGUgY2xhc3MgYC53cGJjX2NvbGxhcHNpYmxlYC5cclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0cz17fV1cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSAgW29wdHMuZ3JvdXBfc2VsZWN0b3I9Jy53cGJjX3VpX19jb2xsYXBzaWJsZV9ncm91cCddXHJcblx0XHQgKiAgICAgICAgU2VsZWN0b3IgZm9yIGVhY2ggY29sbGFwc2libGUgZ3JvdXAgaW5zaWRlIHRoZSBjb250YWluZXIuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gIFtvcHRzLmhlYWRlcl9zZWxlY3Rvcj0nLmdyb3VwX19oZWFkZXInXVxyXG5cdFx0ICogICAgICAgIFNlbGVjdG9yIGZvciB0aGUgY2xpY2thYmxlIGhlYWRlciBpbnNpZGUgYSBncm91cC5cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSAgW29wdHMuZmllbGRzX3NlbGVjdG9yPScuZ3JvdXBfX2ZpZWxkcyddXHJcblx0XHQgKiAgICAgICAgU2VsZWN0b3IgZm9yIHRoZSBjb250ZW50L3BhbmVsIGVsZW1lbnQgaW5zaWRlIGEgZ3JvdXAuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gIFtvcHRzLm9wZW5fY2xhc3M9J2lzLW9wZW4nXVxyXG5cdFx0ICogICAgICAgIENsYXNzIG5hbWUgdGhhdCBpbmRpY2F0ZXMgdGhlIGdyb3VwIGlzIG9wZW4uXHJcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IFtvcHRzLmV4Y2x1c2l2ZT1mYWxzZV1cclxuXHRcdCAqICAgICAgICBJZiB0cnVlLCBvbmx5IG9uZSBncm91cCBjYW4gYmUgb3BlbiBhdCBhIHRpbWUgaW4gdGhpcyBjb250YWluZXIuXHJcblx0XHQgKlxyXG5cdFx0ICogQGNvbnN0cnVjdG9yXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRjb25zdHJ1Y3Rvcihyb290X2VsLCBvcHRzID0ge30pIHtcclxuXHRcdFx0dGhpcy5yb290ID0gKHR5cGVvZiByb290X2VsID09PSAnc3RyaW5nJykgPyBkLnF1ZXJ5U2VsZWN0b3IoIHJvb3RfZWwgKSA6IHJvb3RfZWw7XHJcblx0XHRcdHRoaXMub3B0cyA9IE9iamVjdC5hc3NpZ24oIHtcclxuXHRcdFx0XHRncm91cF9zZWxlY3RvciA6ICcud3BiY191aV9fY29sbGFwc2libGVfZ3JvdXAnLFxyXG5cdFx0XHRcdGhlYWRlcl9zZWxlY3RvcjogJy5ncm91cF9faGVhZGVyJyxcclxuXHRcdFx0XHRmaWVsZHNfc2VsZWN0b3I6ICcuZ3JvdXBfX2ZpZWxkcywuZ3JvdXBfX2NvbnRlbnQnLFxyXG5cdFx0XHRcdG9wZW5fY2xhc3MgICAgIDogJ2lzLW9wZW4nLFxyXG5cdFx0XHRcdGV4Y2x1c2l2ZSAgICAgIDogZmFsc2VcclxuXHRcdFx0fSwgb3B0cyApO1xyXG5cclxuXHRcdFx0Ly8gQm91bmQgaGFuZGxlcnMgKGZvciBhZGQvcmVtb3ZlRXZlbnRMaXN0ZW5lciBzeW1tZXRyeSkuXHJcblx0XHRcdC8qKiBAcHJpdmF0ZSAqL1xyXG5cdFx0XHR0aGlzLl9vbl9jbGljayA9IHRoaXMuX29uX2NsaWNrLmJpbmQoIHRoaXMgKTtcclxuXHRcdFx0LyoqIEBwcml2YXRlICovXHJcblx0XHRcdHRoaXMuX29uX2tleWRvd24gPSB0aGlzLl9vbl9rZXlkb3duLmJpbmQoIHRoaXMgKTtcclxuXHJcblx0XHRcdC8qKiBAdHlwZSB7SFRNTEVsZW1lbnRbXX0gQHByaXZhdGUgKi9cclxuXHRcdFx0dGhpcy5fZ3JvdXBzID0gW107XHJcblx0XHRcdC8qKiBAdHlwZSB7TXV0YXRpb25PYnNlcnZlcnxudWxsfSBAcHJpdmF0ZSAqL1xyXG5cdFx0XHR0aGlzLl9vYnNlcnZlciA9IG51bGw7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBJbml0aWFsaXplIHRoZSBjb250cm9sbGVyOiBjYWNoZSBncm91cHMsIGF0dGFjaCBsaXN0ZW5lcnMsIHNldCBBUklBLFxyXG5cdFx0ICogYW5kIHN0YXJ0IG9ic2VydmluZyBET00gY2hhbmdlcyBpbnNpZGUgdGhlIGNvbnRhaW5lci5cclxuXHRcdCAqXHJcblx0XHQgKiBAcmV0dXJucyB7V1BCQ19Db2xsYXBzaWJsZV9Hcm91cHN9IFRoZSBpbnN0YW5jZSAoY2hhaW5hYmxlKS5cclxuXHRcdCAqIEBsaXN0ZW5zIGNsaWNrXHJcblx0XHQgKiBAbGlzdGVucyBrZXlkb3duXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRpbml0KCkge1xyXG5cdFx0XHRpZiAoICF0aGlzLnJvb3QgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpcy5fZ3JvdXBzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoXHJcblx0XHRcdFx0dGhpcy5yb290LnF1ZXJ5U2VsZWN0b3JBbGwoIHRoaXMub3B0cy5ncm91cF9zZWxlY3RvciApXHJcblx0XHRcdCk7XHJcblx0XHRcdHRoaXMucm9vdC5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCB0aGlzLl9vbl9jbGljaywgZmFsc2UgKTtcclxuXHRcdFx0dGhpcy5yb290LmFkZEV2ZW50TGlzdGVuZXIoICdrZXlkb3duJywgdGhpcy5fb25fa2V5ZG93biwgZmFsc2UgKTtcclxuXHJcblx0XHRcdC8vIE9ic2VydmUgZHluYW1pYyBpbnNlcnRzL3JlbW92YWxzIChJbnNwZWN0b3IgcmUtcmVuZGVycykuXHJcblx0XHRcdHRoaXMuX29ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIoICgpID0+IHtcclxuXHRcdFx0XHR0aGlzLnJlZnJlc2goKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHR0aGlzLl9vYnNlcnZlci5vYnNlcnZlKCB0aGlzLnJvb3QsIHsgY2hpbGRMaXN0OiB0cnVlLCBzdWJ0cmVlOiB0cnVlIH0gKTtcclxuXHJcblx0XHRcdHRoaXMuX3N5bmNfYWxsX2FyaWEoKTtcclxuXHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBUZWFyIGRvd24gdGhlIGNvbnRyb2xsZXI6IGRldGFjaCBsaXN0ZW5lcnMsIHN0b3AgdGhlIG9ic2VydmVyLFxyXG5cdFx0ICogYW5kIGRyb3AgaW50ZXJuYWwgcmVmZXJlbmNlcy5cclxuXHRcdCAqXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdGRlc3Ryb3koKSB7XHJcblx0XHRcdGlmICggIXRoaXMucm9vdCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpcy5yb290LnJlbW92ZUV2ZW50TGlzdGVuZXIoICdjbGljaycsIHRoaXMuX29uX2NsaWNrLCBmYWxzZSApO1xyXG5cdFx0XHR0aGlzLnJvb3QucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ2tleWRvd24nLCB0aGlzLl9vbl9rZXlkb3duLCBmYWxzZSApO1xyXG5cdFx0XHRpZiAoIHRoaXMuX29ic2VydmVyICkge1xyXG5cdFx0XHRcdHRoaXMuX29ic2VydmVyLmRpc2Nvbm5lY3QoKTtcclxuXHRcdFx0XHR0aGlzLl9vYnNlcnZlciA9IG51bGw7XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpcy5fZ3JvdXBzID0gW107XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZS1zY2FuIHRoZSBET00gZm9yIGN1cnJlbnQgZ3JvdXBzIGFuZCByZS1hcHBseSBBUklBIHRvIGFsbCBvZiB0aGVtLlxyXG5cdFx0ICogVXNlZnVsIGFmdGVyIGR5bmFtaWMgKHJlKXJlbmRlcnMuXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRyZWZyZXNoKCkge1xyXG5cdFx0XHRpZiAoICF0aGlzLnJvb3QgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdHRoaXMuX2dyb3VwcyA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKFxyXG5cdFx0XHRcdHRoaXMucm9vdC5xdWVyeVNlbGVjdG9yQWxsKCB0aGlzLm9wdHMuZ3JvdXBfc2VsZWN0b3IgKVxyXG5cdFx0XHQpO1xyXG5cdFx0XHR0aGlzLl9zeW5jX2FsbF9hcmlhKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDaGVjayB3aGV0aGVyIHRoZSBjb250YWluZXIgaXMgaW4gZXhjbHVzaXZlIChhY2NvcmRpb24pIG1vZGUuXHJcblx0XHQgKlxyXG5cdFx0ICogT3JkZXIgb2YgcHJlY2VkZW5jZTpcclxuXHRcdCAqICAxKSBFeHBsaWNpdCBvcHRpb24gYG9wdHMuZXhjbHVzaXZlYFxyXG5cdFx0ICogIDIpIENvbnRhaW5lciBoYXMgY2xhc3MgYC53cGJjX2NvbGxhcHNpYmxlLS1leGNsdXNpdmVgXHJcblx0XHQgKiAgMykgQ29udGFpbmVyIG1hdGNoZXMgYFtkYXRhLXdwYmMtYWNjb3JkaW9uPVwiZXhjbHVzaXZlXCJdYFxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm5zIHtib29sZWFufSBUcnVlIGlmIGV4Y2x1c2l2ZSBtb2RlIGlzIGFjdGl2ZS5cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdGlzX2V4Y2x1c2l2ZSgpIHtcclxuXHRcdFx0cmV0dXJuICEhKFxyXG5cdFx0XHRcdHRoaXMub3B0cy5leGNsdXNpdmUgfHxcclxuXHRcdFx0XHR0aGlzLnJvb3QuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19jb2xsYXBzaWJsZS0tZXhjbHVzaXZlJyApIHx8XHJcblx0XHRcdFx0dGhpcy5yb290Lm1hdGNoZXMoICdbZGF0YS13cGJjLWFjY29yZGlvbj1cImV4Y2x1c2l2ZVwiXScgKVxyXG5cdFx0XHQpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRGV0ZXJtaW5lIHdoZXRoZXIgYSBzcGVjaWZpYyBncm91cCBpcyBvcGVuLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGdyb3VwIFRoZSBncm91cCBlbGVtZW50IHRvIHRlc3QuXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn0gVHJ1ZSBpZiB0aGUgZ3JvdXAgaXMgY3VycmVudGx5IG9wZW4uXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRpc19vcGVuKGdyb3VwKSB7XHJcblx0XHRcdHJldHVybiBncm91cC5jbGFzc0xpc3QuY29udGFpbnMoIHRoaXMub3B0cy5vcGVuX2NsYXNzICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBPcGVuIGEgZ3JvdXAuIEhvbm9ycyBleGNsdXNpdmUgbW9kZSBieSBjb2xsYXBzaW5nIGFsbCBzaWJsaW5nIGdyb3Vwc1xyXG5cdFx0ICogKHF1ZXJpZWQgZnJvbSB0aGUgbGl2ZSBET00gYXQgY2FsbC10aW1lKS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBncm91cCBUaGUgZ3JvdXAgZWxlbWVudCB0byBvcGVuLlxyXG5cdFx0ICogQHBhcmFtIHtib29sZWFufSBbZXhjbHVzaXZlXVxyXG5cdFx0ICogICAgICAgIElmIHByb3ZpZGVkLCBvdmVycmlkZXMgY29udGFpbmVyIG1vZGUgZm9yIHRoaXMgYWN0aW9uIG9ubHkuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBmaXJlcyBDdXN0b21FdmVudCN3cGJjOmNvbGxhcHNpYmxlOm9wZW5cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdGV4cGFuZChncm91cCwgZXhjbHVzaXZlKSB7XHJcblx0XHRcdGlmICggIWdyb3VwICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCBkb19leGNsdXNpdmUgPSAodHlwZW9mIGV4Y2x1c2l2ZSA9PT0gJ2Jvb2xlYW4nKSA/IGV4Y2x1c2l2ZSA6IHRoaXMuaXNfZXhjbHVzaXZlKCk7XHJcblx0XHRcdGlmICggZG9fZXhjbHVzaXZlICkge1xyXG5cdFx0XHRcdC8vIEFsd2F5cyB1c2UgdGhlIGxpdmUgRE9NLCBub3QgdGhlIGNhY2hlZCBsaXN0LlxyXG5cdFx0XHRcdEFycmF5LnByb3RvdHlwZS5mb3JFYWNoLmNhbGwoXHJcblx0XHRcdFx0XHR0aGlzLnJvb3QucXVlcnlTZWxlY3RvckFsbCggdGhpcy5vcHRzLmdyb3VwX3NlbGVjdG9yICksXHJcblx0XHRcdFx0XHQoZykgPT4ge1xyXG5cdFx0XHRcdFx0XHRpZiAoIGcgIT09IGdyb3VwICkge1xyXG5cdFx0XHRcdFx0XHRcdHRoaXMuX3NldF9vcGVuKCBnLCBmYWxzZSApO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0KTtcclxuXHRcdFx0fVxyXG5cdFx0XHR0aGlzLl9zZXRfb3BlbiggZ3JvdXAsIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIENsb3NlIGEgZ3JvdXAuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZ3JvdXAgVGhlIGdyb3VwIGVsZW1lbnQgdG8gY2xvc2UuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBmaXJlcyBDdXN0b21FdmVudCN3cGJjOmNvbGxhcHNpYmxlOmNsb3NlXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRjb2xsYXBzZShncm91cCkge1xyXG5cdFx0XHRpZiAoICFncm91cCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpcy5fc2V0X29wZW4oIGdyb3VwLCBmYWxzZSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVG9nZ2xlIGEgZ3JvdXAncyBvcGVuL2Nsb3NlZCBzdGF0ZS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBncm91cCBUaGUgZ3JvdXAgZWxlbWVudCB0byB0b2dnbGUuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdHRvZ2dsZShncm91cCkge1xyXG5cdFx0XHRpZiAoICFncm91cCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpc1t0aGlzLmlzX29wZW4oIGdyb3VwICkgPyAnY29sbGFwc2UnIDogJ2V4cGFuZCddKCBncm91cCApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogT3BlbiBhIGdyb3VwIGJ5IGl0cyBpbmRleCB3aXRoaW4gdGhlIGNvbnRhaW5lciAoMC1iYXNlZCkuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IGluZGV4IFplcm8tYmFzZWQgaW5kZXggb2YgdGhlIGdyb3VwLlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRvcGVuX2J5X2luZGV4KGluZGV4KSB7XHJcblx0XHRcdGNvbnN0IGdyb3VwID0gdGhpcy5fZ3JvdXBzW2luZGV4XTtcclxuXHRcdFx0aWYgKCBncm91cCApIHtcclxuXHRcdFx0XHR0aGlzLmV4cGFuZCggZ3JvdXAgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogT3BlbiBhIGdyb3VwIGJ5IG1hdGNoaW5nIHRleHQgY29udGFpbmVkIHdpdGhpbiB0aGUgPGgzPiBpbnNpZGUgdGhlIGhlYWRlci5cclxuXHRcdCAqIFRoZSBjb21wYXJpc29uIGlzIGNhc2UtaW5zZW5zaXRpdmUgYW5kIHN1YnN0cmluZy1iYXNlZC5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gdGV4dCBUZXh0IHRvIG1hdGNoIGFnYWluc3QgdGhlIGhlYWRpbmcgY29udGVudHMuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdG9wZW5fYnlfaGVhZGluZyh0ZXh0KSB7XHJcblx0XHRcdGlmICggIXRleHQgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdGNvbnN0IHQgICAgID0gU3RyaW5nKCB0ZXh0ICkudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0Y29uc3QgbWF0Y2ggPSB0aGlzLl9ncm91cHMuZmluZCggKGcpID0+IHtcclxuXHRcdFx0XHRjb25zdCBoID0gZy5xdWVyeVNlbGVjdG9yKCB0aGlzLm9wdHMuaGVhZGVyX3NlbGVjdG9yICsgJyBoMycgKTtcclxuXHRcdFx0XHRyZXR1cm4gaCAmJiBoLnRleHRDb250ZW50LnRvTG93ZXJDYXNlKCkuaW5kZXhPZiggdCApICE9PSAtMTtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHRpZiAoIG1hdGNoICkge1xyXG5cdFx0XHRcdHRoaXMuZXhwYW5kKCBtYXRjaCApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gSW50ZXJuYWxcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIERlbGVnYXRlZCBjbGljayBoYW5kbGVyIGZvciBoZWFkZXJzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKiBAcGFyYW0ge01vdXNlRXZlbnR9IGV2IFRoZSBjbGljayBldmVudC5cclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICogQHNpbmNlIDIwMjUtMDgtMjZcclxuXHRcdCAqL1xyXG5cdFx0X29uX2NsaWNrKGV2KSB7XHJcblx0XHRcdGNvbnN0IGJ0biA9IGV2LnRhcmdldC5jbG9zZXN0KCB0aGlzLm9wdHMuaGVhZGVyX3NlbGVjdG9yICk7XHJcblx0XHRcdGlmICggIWJ0biB8fCAhdGhpcy5yb290LmNvbnRhaW5zKCBidG4gKSApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0ZXYucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0ZXYuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblx0XHRcdGNvbnN0IGdyb3VwID0gYnRuLmNsb3Nlc3QoIHRoaXMub3B0cy5ncm91cF9zZWxlY3RvciApO1xyXG5cdFx0XHRpZiAoIGdyb3VwICkge1xyXG5cdFx0XHRcdHRoaXMudG9nZ2xlKCBncm91cCApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBLZXlib2FyZCBoYW5kbGVyIGZvciBoZWFkZXIgaW50ZXJhY3Rpb25zIGFuZCByb3ZpbmcgZm9jdXM6XHJcblx0XHQgKiAgLSBFbnRlci9TcGFjZSB0b2dnbGVzIHRoZSBhY3RpdmUgZ3JvdXAuXHJcblx0XHQgKiAgLSBBcnJvd1VwL0Fycm93RG93biBtb3ZlcyBmb2N1cyBiZXR3ZWVuIGdyb3VwIGhlYWRlcnMuXHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqIEBwYXJhbSB7S2V5Ym9hcmRFdmVudH0gZXYgVGhlIGtleWJvYXJkIGV2ZW50LlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRfb25fa2V5ZG93bihldikge1xyXG5cdFx0XHRjb25zdCBidG4gPSBldi50YXJnZXQuY2xvc2VzdCggdGhpcy5vcHRzLmhlYWRlcl9zZWxlY3RvciApO1xyXG5cdFx0XHRpZiAoICFidG4gKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCBrZXkgPSBldi5rZXk7XHJcblxyXG5cdFx0XHQvLyBUb2dnbGUgb24gRW50ZXIgLyBTcGFjZS5cclxuXHRcdFx0aWYgKCBrZXkgPT09ICdFbnRlcicgfHwga2V5ID09PSAnICcgKSB7XHJcblx0XHRcdFx0ZXYucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0XHRjb25zdCBncm91cCA9IGJ0bi5jbG9zZXN0KCB0aGlzLm9wdHMuZ3JvdXBfc2VsZWN0b3IgKTtcclxuXHRcdFx0XHRpZiAoIGdyb3VwICkge1xyXG5cdFx0XHRcdFx0dGhpcy50b2dnbGUoIGdyb3VwICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTW92ZSBmb2N1cyB3aXRoIEFycm93VXAvQXJyb3dEb3duIGJldHdlZW4gaGVhZGVycyBpbiB0aGlzIGNvbnRhaW5lci5cclxuXHRcdFx0aWYgKCBrZXkgPT09ICdBcnJvd1VwJyB8fCBrZXkgPT09ICdBcnJvd0Rvd24nICkge1xyXG5cdFx0XHRcdGV2LnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0Y29uc3QgaGVhZGVycyA9IEFycmF5LnByb3RvdHlwZS5tYXAuY2FsbChcclxuXHRcdFx0XHRcdHRoaXMucm9vdC5xdWVyeVNlbGVjdG9yQWxsKCB0aGlzLm9wdHMuZ3JvdXBfc2VsZWN0b3IgKSxcclxuXHRcdFx0XHRcdChnKSA9PiBnLnF1ZXJ5U2VsZWN0b3IoIHRoaXMub3B0cy5oZWFkZXJfc2VsZWN0b3IgKVxyXG5cdFx0XHRcdCkuZmlsdGVyKCBCb29sZWFuICk7XHJcblx0XHRcdFx0Y29uc3QgaWR4ICAgICA9IGhlYWRlcnMuaW5kZXhPZiggYnRuICk7XHJcblx0XHRcdFx0aWYgKCBpZHggIT09IC0xICkge1xyXG5cdFx0XHRcdFx0Y29uc3QgbmV4dF9pZHggPSAoa2V5ID09PSAnQXJyb3dEb3duJylcclxuXHRcdFx0XHRcdFx0PyBNYXRoLm1pbiggaGVhZGVycy5sZW5ndGggLSAxLCBpZHggKyAxIClcclxuXHRcdFx0XHRcdFx0OiBNYXRoLm1heCggMCwgaWR4IC0gMSApO1xyXG5cdFx0XHRcdFx0aGVhZGVyc1tuZXh0X2lkeF0uZm9jdXMoKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEFwcGx5IEFSSUEgc3luY2hyb25pemF0aW9uIHRvIGFsbCBrbm93biBncm91cHMgYmFzZWQgb24gdGhlaXIgb3BlbiBzdGF0ZS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRfc3luY19hbGxfYXJpYSgpIHtcclxuXHRcdFx0dGhpcy5fZ3JvdXBzLmZvckVhY2goIChnKSA9PiB0aGlzLl9zeW5jX2dyb3VwX2FyaWEoIGcgKSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU3luYyBBUklBIGF0dHJpYnV0ZXMgYW5kIHZpc2liaWxpdHkgb24gYSBzaW5nbGUgZ3JvdXAuXHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGdyb3VwIFRoZSBncm91cCBlbGVtZW50IHRvIHN5bmMuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBzaW5jZSAyMDI1LTA4LTI2XHJcblx0XHQgKi9cclxuXHRcdF9zeW5jX2dyb3VwX2FyaWEoZ3JvdXApIHtcclxuXHRcdFx0Y29uc3QgaXNfb3BlbiA9IHRoaXMuaXNfb3BlbiggZ3JvdXAgKTtcclxuXHRcdFx0Y29uc3QgaGVhZGVyICA9IGdyb3VwLnF1ZXJ5U2VsZWN0b3IoIHRoaXMub3B0cy5oZWFkZXJfc2VsZWN0b3IgKTtcclxuXHRcdFx0Ly8gT25seSBkaXJlY3QgY2hpbGRyZW4gdGhhdCBtYXRjaC5cclxuXHRcdFx0Y29uc3QgcGFuZWxzID0gQXJyYXkucHJvdG90eXBlLmZpbHRlci5jYWxsKCBncm91cC5jaGlsZHJlbiwgKGVsKSA9PiBlbC5tYXRjaGVzKCB0aGlzLm9wdHMuZmllbGRzX3NlbGVjdG9yICkgKTtcclxuXHJcblx0XHRcdC8vIEhlYWRlciBBUklBLlxyXG5cdFx0XHRpZiAoIGhlYWRlciApIHtcclxuXHRcdFx0XHRoZWFkZXIuc2V0QXR0cmlidXRlKCAncm9sZScsICdidXR0b24nICk7XHJcblx0XHRcdFx0aGVhZGVyLnNldEF0dHJpYnV0ZSggJ2FyaWEtZXhwYW5kZWQnLCBpc19vcGVuID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cclxuXHRcdFx0XHRpZiAoIHBhbmVscy5sZW5ndGggKSB7XHJcblx0XHRcdFx0XHQvLyBFbnN1cmUgZWFjaCBwYW5lbCBoYXMgYW4gaWQ7IHRoZW4gd2lyZSBhcmlhLWNvbnRyb2xzIHdpdGggc3BhY2Utc2VwYXJhdGVkIGlkcy5cclxuXHRcdFx0XHRcdGNvbnN0IGlkcyA9IHBhbmVscy5tYXAoIChwKSA9PiB7XHJcblx0XHRcdFx0XHRcdGlmICggIXAuaWQgKSBwLmlkID0gdGhpcy5fZ2VuZXJhdGVfaWQoICd3cGJjX2NvbGxhcHNpYmxlX3BhbmVsJyApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gcC5pZDtcclxuXHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRcdGhlYWRlci5zZXRBdHRyaWJ1dGUoICdhcmlhLWNvbnRyb2xzJywgaWRzLmpvaW4oICcgJyApICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyAoMykgUGFuZWxzIEFSSUEgKyB2aXNpYmlsaXR5LlxyXG5cdFx0XHRwYW5lbHMuZm9yRWFjaCggKHApID0+IHtcclxuXHRcdFx0XHRwLmhpZGRlbiA9ICFpc19vcGVuOyAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBhY3R1YWwgdmlzaWJpbGl0eS5cclxuXHRcdFx0XHRwLnNldEF0dHJpYnV0ZSggJ2FyaWEtaGlkZGVuJywgaXNfb3BlbiA/ICdmYWxzZScgOiAndHJ1ZScgKTsgLy8gQVJJQS5cclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSW50ZXJuYWwgc3RhdGUgY2hhbmdlOiBzZXQgYSBncm91cCdzIG9wZW4vY2xvc2VkIHN0YXRlLCBzeW5jIEFSSUEsXHJcblx0XHQgKiBtYW5hZ2UgZm9jdXMgb24gY29sbGFwc2UsIGFuZCBlbWl0IGEgY3VzdG9tIGV2ZW50LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBncm91cCBUaGUgZ3JvdXAgZWxlbWVudCB0byBtdXRhdGUuXHJcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IG9wZW4gV2hldGhlciB0aGUgZ3JvdXAgc2hvdWxkIGJlIG9wZW4uXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqIEBmaXJlcyBDdXN0b21FdmVudCN3cGJjOmNvbGxhcHNpYmxlOm9wZW5cclxuXHRcdCAqIEBmaXJlcyBDdXN0b21FdmVudCN3cGJjOmNvbGxhcHNpYmxlOmNsb3NlXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRfc2V0X29wZW4oZ3JvdXAsIG9wZW4pIHtcclxuXHRcdFx0aWYgKCAhb3BlbiAmJiBncm91cC5jb250YWlucyggZG9jdW1lbnQuYWN0aXZlRWxlbWVudCApICkge1xyXG5cdFx0XHRcdGNvbnN0IGhlYWRlciA9IGdyb3VwLnF1ZXJ5U2VsZWN0b3IoIHRoaXMub3B0cy5oZWFkZXJfc2VsZWN0b3IgKTtcclxuXHRcdFx0XHRoZWFkZXIgJiYgaGVhZGVyLmZvY3VzKCk7XHJcblx0XHRcdH1cclxuXHRcdFx0Z3JvdXAuY2xhc3NMaXN0LnRvZ2dsZSggdGhpcy5vcHRzLm9wZW5fY2xhc3MsIG9wZW4gKTtcclxuXHRcdFx0dGhpcy5fc3luY19ncm91cF9hcmlhKCBncm91cCApO1xyXG5cdFx0XHRjb25zdCBldl9uYW1lID0gb3BlbiA/ICd3cGJjOmNvbGxhcHNpYmxlOm9wZW4nIDogJ3dwYmM6Y29sbGFwc2libGU6Y2xvc2UnO1xyXG5cdFx0XHRncm91cC5kaXNwYXRjaEV2ZW50KCBuZXcgQ3VzdG9tRXZlbnQoIGV2X25hbWUsIHtcclxuXHRcdFx0XHRidWJibGVzOiB0cnVlLFxyXG5cdFx0XHRcdGRldGFpbCA6IHsgZ3JvdXAsIHJvb3Q6IHRoaXMucm9vdCwgaW5zdGFuY2U6IHRoaXMgfVxyXG5cdFx0XHR9ICkgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdlbmVyYXRlIGEgdW5pcXVlIERPTSBpZCB3aXRoIHRoZSBzcGVjaWZpZWQgcHJlZml4LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4IFRoZSBpZCBwcmVmaXggdG8gdXNlLlxyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ30gQSB1bmlxdWUgZWxlbWVudCBpZCBub3QgcHJlc2VudCBpbiB0aGUgZG9jdW1lbnQuXHJcblx0XHQgKiBAc2luY2UgMjAyNS0wOC0yNlxyXG5cdFx0ICovXHJcblx0XHRfZ2VuZXJhdGVfaWQocHJlZml4KSB7XHJcblx0XHRcdGxldCBpID0gMTtcclxuXHRcdFx0bGV0IGlkO1xyXG5cdFx0XHRkbyB7XHJcblx0XHRcdFx0aWQgPSBwcmVmaXggKyAnXycgKyAoaSsrKTtcclxuXHRcdFx0fVxyXG5cdFx0XHR3aGlsZSAoIGQuZ2V0RWxlbWVudEJ5SWQoIGlkICkgKTtcclxuXHRcdFx0cmV0dXJuIGlkO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogQXV0by1pbml0aWFsaXplIGNvbGxhcHNpYmxlIGNvbnRyb2xsZXJzIG9uIHRoZSBwYWdlLlxyXG5cdCAqIEZpbmRzIHRvcC1sZXZlbCBgLndwYmNfY29sbGFwc2libGVgIGNvbnRhaW5lcnMgKGlnbm9yaW5nIG5lc3RlZCBvbmVzKSxcclxuXHQgKiBhbmQgaW5zdGFudGlhdGVzIHtAbGluayBXUEJDX0NvbGxhcHNpYmxlX0dyb3Vwc30gb24gZWFjaC5cclxuXHQgKlxyXG5cdCAqIEBmdW5jdGlvbiBXUEJDX0NvbGxhcHNpYmxlX0F1dG9Jbml0XHJcblx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0ICogQHNpbmNlIDIwMjUtMDgtMjZcclxuXHQgKiBAZXhhbXBsZVxyXG5cdCAqIC8vIFJ1bnMgYXV0b21hdGljYWxseSBvbiBET01Db250ZW50TG9hZGVkOyBjYW4gYWxzbyBiZSBjYWxsZWQgbWFudWFsbHk6XHJcblx0ICogV1BCQ19Db2xsYXBzaWJsZV9BdXRvSW5pdCgpO1xyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY29sbGFwc2libGVfX2F1dG9faW5pdCgpIHtcclxuXHRcdHZhciBST09UICA9ICcud3BiY19jb2xsYXBzaWJsZSc7XHJcblx0XHR2YXIgbm9kZXMgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbCggZC5xdWVyeVNlbGVjdG9yQWxsKCBST09UICkgKVxyXG5cdFx0XHQuZmlsdGVyKCBmdW5jdGlvbiAobikge1xyXG5cdFx0XHRcdHJldHVybiAhbi5wYXJlbnRFbGVtZW50IHx8ICFuLnBhcmVudEVsZW1lbnQuY2xvc2VzdCggUk9PVCApO1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0bm9kZXMuZm9yRWFjaCggZnVuY3Rpb24gKG5vZGUpIHtcclxuXHRcdFx0aWYgKCBub2RlLl9fd3BiY19jb2xsYXBzaWJsZV9pbnN0YW5jZSApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0dmFyIGV4Y2x1c2l2ZSA9IG5vZGUuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19jb2xsYXBzaWJsZS0tZXhjbHVzaXZlJyApIHx8IG5vZGUubWF0Y2hlcyggJ1tkYXRhLXdwYmMtYWNjb3JkaW9uPVwiZXhjbHVzaXZlXCJdJyApO1xyXG5cclxuXHRcdFx0bm9kZS5fX3dwYmNfY29sbGFwc2libGVfaW5zdGFuY2UgPSBuZXcgV1BCQ19Db2xsYXBzaWJsZV9Hcm91cHMoIG5vZGUsIHsgZXhjbHVzaXZlIH0gKS5pbml0KCk7XHJcblx0XHR9ICk7XHJcblx0fVxyXG5cclxuXHQvLyBFeHBvcnQgdG8gZ2xvYmFsIGZvciBtYW51YWwgY29udHJvbCBpZiBuZWVkZWQuXHJcblx0dy5XUEJDX0NvbGxhcHNpYmxlX0dyb3VwcyAgID0gV1BCQ19Db2xsYXBzaWJsZV9Hcm91cHM7XHJcblx0dy5XUEJDX0NvbGxhcHNpYmxlX0F1dG9Jbml0ID0gd3BiY19jb2xsYXBzaWJsZV9fYXV0b19pbml0O1xyXG5cclxuXHQvLyBET00tcmVhZHkgYXV0byBpbml0LlxyXG5cdGlmICggZC5yZWFkeVN0YXRlID09PSAnbG9hZGluZycgKSB7XHJcblx0XHRkLmFkZEV2ZW50TGlzdGVuZXIoICdET01Db250ZW50TG9hZGVkJywgd3BiY19jb2xsYXBzaWJsZV9fYXV0b19pbml0LCB7IG9uY2U6IHRydWUgfSApO1xyXG5cdH0gZWxzZSB7XHJcblx0XHR3cGJjX2NvbGxhcHNpYmxlX19hdXRvX2luaXQoKTtcclxuXHR9XHJcbn0pKCB3aW5kb3csIGRvY3VtZW50ICk7XHJcbiIsIi8qIGdsb2JhbHMgd2luZG93LCBkb2N1bWVudCAqL1xyXG4vKipcclxuICogV1BCQyBTbGlkZXIgTGVuZ3RoIEdyb3Vwc1xyXG4gKlxyXG4gKiBVbml2ZXJzYWwsIGRlcGVuZGVuY3ktZnJlZSBjb250cm9sbGVyIHRoYXQga2VlcHMgYSBcImxlbmd0aFwiIGNvbnRyb2wgaW4gc3luYzpcclxuICogIC0gbnVtYmVyIGlucHV0ICAoZGF0YS13cGJjX3NsaWRlcl9sZW5fdmFsdWUpXHJcbiAqICAtIHVuaXQgc2VsZWN0ICAgKGRhdGEtd3BiY19zbGlkZXJfbGVuX3VuaXQpXHJcbiAqICAtIHJhbmdlIHNsaWRlciAgKGRhdGEtd3BiY19zbGlkZXJfbGVuX3JhbmdlKVxyXG4gKiAgLSB3cml0ZXIgaW5wdXQgIChkYXRhLXdwYmNfc2xpZGVyX2xlbl93cml0ZXIpICBbb3B0aW9uYWwgYnV0IHJlY29tbWVuZGVkXVxyXG4gKlxyXG4gKiBUaGUgXCJ3cml0ZXJcIiBzdG9yZXMgdGhlIGNvbWJpbmVkIHZhbHVlIGxpa2U6IFwiMTAwJVwiLCBcIjQyMHB4XCIsIFwiMTIuNXJlbVwiLlxyXG4gKiBXaGVuIG51bWJlci91bml0L3NsaWRlciBjaGFuZ2UgLT4gd3JpdGVyIHVwZGF0ZXMgYW5kIGVtaXRzICdpbnB1dCcgKGJ1YmJsZXMpLlxyXG4gKiBXaGVuIHdyaXRlciBpcyBjaGFuZ2VkIGV4dGVybmFsbHkgKGFwcGx5LWZyb20tSlNPTiwgZXRjKSAtPiBVSSB1cGRhdGVzLlxyXG4gKlxyXG4gKiBNYXJrdXAgZXhwZWN0YXRpb25zIChtaW5pbWFsKTpcclxuICogIDxkaXYgY2xhc3M9XCJ3cGJjX3NsaWRlcl9sZW5fZ3JvdXBcIlxyXG4gKiAgICAgICBkYXRhLXdwYmNfc2xpZGVyX2xlbl9ib3VuZHNfbWFwPSd7XCIlXCI6e1wibWluXCI6MzAsXCJtYXhcIjoxMDAsXCJzdGVwXCI6MX0sXCJweFwiOntcIm1pblwiOjMwMCxcIm1heFwiOjIwMDAsXCJzdGVwXCI6MTB9fSdcclxuICogICAgICAgZGF0YS13cGJjX3NsaWRlcl9sZW5fZGVmYXVsdF91bml0PVwiJVwiPlxyXG4gKiAgICA8aW5wdXQgdHlwZT1cIm51bWJlclwiIGRhdGEtd3BiY19zbGlkZXJfbGVuX3ZhbHVlPlxyXG4gKiAgICA8c2VsZWN0IGRhdGEtd3BiY19zbGlkZXJfbGVuX3VuaXQ+Li4uPC9zZWxlY3Q+XHJcbiAqICAgIDxpbnB1dCB0eXBlPVwicmFuZ2VcIiBkYXRhLXdwYmNfc2xpZGVyX2xlbl9yYW5nZT5cclxuICogICAgPGlucHV0IHR5cGU9XCJ0ZXh0XCIgZGF0YS13cGJjX3NsaWRlcl9sZW5fd3JpdGVyIHN0eWxlPVwiZGlzcGxheTpub25lO1wiPlxyXG4gKiAgPC9kaXY+XHJcbiAqXHJcbiAqIFBlcmZvcm1hbmNlIG5vdGVzOlxyXG4gKiAtIE11dGF0aW9uT2JzZXJ2ZXIgaXMgRElTQUJMRUQgYnkgZGVmYXVsdCAocHJldmVudHMgcGVyZm9ybWFuY2UgaXNzdWVzKS5cclxuICogLSBJZiB5b3VyIFVJIHJlLXJlbmRlcnMgYW5kIGluc2VydHMgbmV3IGdyb3VwcyBkeW5hbWljYWxseSwgY2FsbDpcclxuICogICAgIFdQQkNfU2xpZGVyX0xlbl9BdXRvSW5pdCgpOyAgT1IgaW5zdGFuY2UucmVmcmVzaCgpO1xyXG4gKiAgIE9yIGVuYWJsZSBvYnNlcnZlciB2aWE6IG5ldyBXUEJDX1NsaWRlcl9MZW5fR3JvdXBzKHJvb3QsIHsgZW5hYmxlX29ic2VydmVyOnRydWUgfSkuaW5pdCgpO1xyXG4gKlxyXG4gKiBQdWJsaWMgQVBJIChpbnN0YW5jZSBtZXRob2RzKTpcclxuICogIC0gaW5pdCgpLCBkZXN0cm95KCksIHJlZnJlc2goKVxyXG4gKlxyXG4gKiBAdmVyc2lvbiAyMDI2LTAxLTI1XHJcbiAqIEBzaW5jZSAgIDIwMjYtMDEtMjVcclxuICogQGZpbGUgICAgLi4vaW5jbHVkZXMvX19qcy9hZG1pbi9zbGlkZXJfZ3JvdXBzL3dwYmNfbGVuX2dyb3Vwcy5qc1xyXG4gKi9cclxuKGZ1bmN0aW9uICh3LCBkKSB7XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSGVscGVyc1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRmdW5jdGlvbiBjbGFtcF9udW0odiwgbWluLCBtYXgpIHtcclxuXHRcdGlmICh0eXBlb2YgbWluID09PSAnbnVtYmVyJyAmJiAhaXNOYU4obWluKSkgdiA9IE1hdGgubWF4KG1pbiwgdik7XHJcblx0XHRpZiAodHlwZW9mIG1heCA9PT0gJ251bWJlcicgJiYgIWlzTmFOKG1heCkpIHYgPSBNYXRoLm1pbihtYXgsIHYpO1xyXG5cdFx0cmV0dXJuIHY7XHJcblx0fVxyXG5cclxuXHRmdW5jdGlvbiBwYXJzZV9mbG9hdCh2KSB7XHJcblx0XHR2YXIgbiA9IHBhcnNlRmxvYXQodik7XHJcblx0XHRyZXR1cm4gaXNOYU4obikgPyBudWxsIDogbjtcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIHNhZmVfanNvbl9wYXJzZShzdHIpIHtcclxuXHRcdHRyeSB7XHJcblx0XHRcdHJldHVybiBKU09OLnBhcnNlKHN0cik7XHJcblx0XHR9IGNhdGNoIChlKSB7XHJcblx0XHRcdHJldHVybiBudWxsO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gcGFyc2VfbGVuX2NvbWJpbmVkKHJhdywgZGVmYXVsdF91bml0KSB7XHJcblx0XHR2YXIgcyA9IChyYXcgPT0gbnVsbCkgPyAnJyA6IFN0cmluZyhyYXcpLnRyaW0oKTtcclxuXHRcdGlmICghcykgcmV0dXJuIHsgbnVtOiAnJywgdW5pdDogZGVmYXVsdF91bml0IHx8ICclJyB9O1xyXG5cclxuXHRcdHZhciBtID0gcy5tYXRjaCgvXlxccyooW1xcLV0/XFxkKyg/OlxcLlxcZCspPylcXHMqKFthLXolXSopXFxzKiQvaSk7XHJcblx0XHRpZiAoIW0pIHtcclxuXHRcdFx0Ly8gSWYgaXQncyBub3QgcGFyc2VhYmxlLCB0cmVhdCBhcyBudW1iZXIgYW5kIGtlZXAgZGVmYXVsdCB1bml0LlxyXG5cdFx0XHRyZXR1cm4geyBudW06IHMsIHVuaXQ6IGRlZmF1bHRfdW5pdCB8fCAnJScgfTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgbnVtICA9IG1bMV0gPyBTdHJpbmcobVsxXSkgOiAnJztcclxuXHRcdHZhciB1bml0ID0gbVsyXSA/IFN0cmluZyhtWzJdKSA6ICcnO1xyXG5cdFx0aWYgKCF1bml0KSB1bml0ID0gZGVmYXVsdF91bml0IHx8ICclJztcclxuXHJcblx0XHRyZXR1cm4geyBudW06IG51bSwgdW5pdDogdW5pdCB9O1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gYnVpbGRfY29tYmluZWQobnVtLCB1bml0KSB7XHJcblx0XHRpZiAobnVtID09IG51bGwgfHwgU3RyaW5nKG51bSkudHJpbSgpID09PSAnJykgcmV0dXJuICcnO1xyXG5cdFx0cmV0dXJuIFN0cmluZyhudW0pICsgU3RyaW5nKHVuaXQgfHwgJycpO1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gZW1pdF9pbnB1dChlbCkge1xyXG5cdFx0aWYgKCFlbCkgcmV0dXJuO1xyXG5cdFx0ZWwuZGlzcGF0Y2hFdmVudChuZXcgRXZlbnQoJ2lucHV0JywgeyBidWJibGVzOiB0cnVlIH0pKTtcclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBDb250cm9sbGVyXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGNsYXNzIFdQQkNfU2xpZGVyX0xlbl9Hcm91cHMge1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudHxzdHJpbmd9IHJvb3RfZWwgQ29udGFpbmVyIChvciBzZWxlY3RvcikuIElmIG9taXR0ZWQsIHVzZXMgZG9jdW1lbnQuXHJcblx0XHQgKiBAcGFyYW0ge09iamVjdH0gW29wdHM9e31dXHJcblx0XHQgKi9cclxuXHRcdGNvbnN0cnVjdG9yKHJvb3RfZWwsIG9wdHMpIHtcclxuXHRcdFx0dGhpcy5yb290ID0gcm9vdF9lbFxyXG5cdFx0XHRcdD8gKCh0eXBlb2Ygcm9vdF9lbCA9PT0gJ3N0cmluZycpID8gZC5xdWVyeVNlbGVjdG9yKHJvb3RfZWwpIDogcm9vdF9lbClcclxuXHRcdFx0XHQ6IGQ7XHJcblxyXG5cdFx0XHR0aGlzLm9wdHMgPSBPYmplY3QuYXNzaWduKHtcclxuXHRcdFx0XHQvLyBTdHJpY3Qgc2VsZWN0b3JzIChOTyBiYWNrd2FyZCBjb21wYXRpYmlsaXR5KS5cclxuXHRcdFx0XHRncm91cF9zZWxlY3RvciAgOiAnLndwYmNfc2xpZGVyX2xlbl9ncm91cCcsXHJcblx0XHRcdFx0dmFsdWVfc2VsZWN0b3IgIDogJ1tkYXRhLXdwYmNfc2xpZGVyX2xlbl92YWx1ZV0nLFxyXG5cdFx0XHRcdHVuaXRfc2VsZWN0b3IgICA6ICdbZGF0YS13cGJjX3NsaWRlcl9sZW5fdW5pdF0nLFxyXG5cdFx0XHRcdHJhbmdlX3NlbGVjdG9yICA6ICdbZGF0YS13cGJjX3NsaWRlcl9sZW5fcmFuZ2VdJyxcclxuXHRcdFx0XHR3cml0ZXJfc2VsZWN0b3IgOiAnW2RhdGEtd3BiY19zbGlkZXJfbGVuX3dyaXRlcl0nLFxyXG5cclxuXHRcdFx0XHRkZWZhdWx0X3VuaXQgICAgOiAnJScsXHJcblxyXG5cdFx0XHRcdGZhbGxiYWNrX2JvdW5kcyA6IHtcclxuXHRcdFx0XHRcdCdweCcgOiB7IG1pbjogMCwgICBtYXg6IDUxMiwgIHN0ZXA6IDEgICB9LFxyXG5cdFx0XHRcdFx0JyUnICA6IHsgbWluOiAwLCAgIG1heDogMTAwLCAgc3RlcDogMSAgIH0sXHJcblx0XHRcdFx0XHQncmVtJzogeyBtaW46IDAsICAgbWF4OiAxMCwgICBzdGVwOiAwLjEgfSxcclxuXHRcdFx0XHRcdCdlbScgOiB7IG1pbjogMCwgICBtYXg6IDEwLCAgIHN0ZXA6IDAuMSB9XHJcblx0XHRcdFx0fSxcclxuXHJcblx0XHRcdFx0Ly8gRGlzYWJsZWQgYnkgZGVmYXVsdCBmb3IgcGVyZm9ybWFuY2UuXHJcblx0XHRcdFx0ZW5hYmxlX29ic2VydmVyICAgICA6IGZhbHNlLFxyXG5cdFx0XHRcdG9ic2VydmVyX2RlYm91bmNlX21zOiAxNTBcclxuXHRcdFx0fSwgb3B0cyB8fCB7fSk7XHJcblxyXG5cdFx0XHR0aGlzLl9vbl9pbnB1dCAgPSB0aGlzLl9vbl9pbnB1dC5iaW5kKHRoaXMpO1xyXG5cdFx0XHR0aGlzLl9vbl9jaGFuZ2UgPSB0aGlzLl9vbl9jaGFuZ2UuYmluZCh0aGlzKTtcclxuXHJcblx0XHRcdHRoaXMuX2JvdW5kc19jYWNoZSA9IG5ldyBXZWFrTWFwKCk7IC8vIGdyb3VwIC0+IGJvdW5kc19tYXBfb2JqZWN0XHJcblx0XHRcdHRoaXMuX29ic2VydmVyICAgICA9IG51bGw7XHJcblx0XHRcdHRoaXMuX3JlZnJlc2hfdG1yICA9IG51bGw7XHJcblx0XHR9XHJcblxyXG5cdFx0aW5pdCgpIHtcclxuXHRcdFx0aWYgKCF0aGlzLnJvb3QpIHJldHVybiB0aGlzO1xyXG5cclxuXHRcdFx0dGhpcy5yb290LmFkZEV2ZW50TGlzdGVuZXIoJ2lucHV0JywgIHRoaXMuX29uX2lucHV0LCAgdHJ1ZSk7XHJcblx0XHRcdHRoaXMucm9vdC5hZGRFdmVudExpc3RlbmVyKCdjaGFuZ2UnLCB0aGlzLl9vbl9jaGFuZ2UsIHRydWUpO1xyXG5cclxuXHRcdFx0aWYgKHRoaXMub3B0cy5lbmFibGVfb2JzZXJ2ZXIgJiYgdy5NdXRhdGlvbk9ic2VydmVyKSB7XHJcblx0XHRcdFx0dGhpcy5fb2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlcigoKSA9PiB7IHRoaXMuX2RlYm91bmNlZF9yZWZyZXNoKCk7IH0pO1xyXG5cdFx0XHRcdHRoaXMuX29ic2VydmVyLm9ic2VydmUodGhpcy5yb290ID09PSBkID8gZC5kb2N1bWVudEVsZW1lbnQgOiB0aGlzLnJvb3QsIHsgY2hpbGRMaXN0OiB0cnVlLCBzdWJ0cmVlOiB0cnVlIH0pO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR0aGlzLnJlZnJlc2goKTtcclxuXHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHR9XHJcblxyXG5cdFx0ZGVzdHJveSgpIHtcclxuXHRcdFx0aWYgKCF0aGlzLnJvb3QpIHJldHVybjtcclxuXHJcblx0XHRcdHRoaXMucm9vdC5yZW1vdmVFdmVudExpc3RlbmVyKCdpbnB1dCcsICB0aGlzLl9vbl9pbnB1dCwgIHRydWUpO1xyXG5cdFx0XHR0aGlzLnJvb3QucmVtb3ZlRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgdGhpcy5fb25fY2hhbmdlLCB0cnVlKTtcclxuXHJcblx0XHRcdGlmICh0aGlzLl9vYnNlcnZlcikge1xyXG5cdFx0XHRcdHRoaXMuX29ic2VydmVyLmRpc2Nvbm5lY3QoKTtcclxuXHRcdFx0XHR0aGlzLl9vYnNlcnZlciA9IG51bGw7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICh0aGlzLl9yZWZyZXNoX3Rtcikge1xyXG5cdFx0XHRcdGNsZWFyVGltZW91dCh0aGlzLl9yZWZyZXNoX3Rtcik7XHJcblx0XHRcdFx0dGhpcy5fcmVmcmVzaF90bXIgPSBudWxsO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmVmcmVzaCgpIHtcclxuXHRcdFx0aWYgKCF0aGlzLnJvb3QpIHJldHVybjtcclxuXHJcblx0XHRcdHZhciBzY29wZSAgPSAodGhpcy5yb290ID09PSBkID8gZCA6IHRoaXMucm9vdCk7XHJcblx0XHRcdHZhciBncm91cHMgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChzY29wZS5xdWVyeVNlbGVjdG9yQWxsKHRoaXMub3B0cy5ncm91cF9zZWxlY3RvcikpO1xyXG5cclxuXHRcdFx0Zm9yICh2YXIgaSA9IDA7IGkgPCBncm91cHMubGVuZ3RoOyBpKyspIHtcclxuXHRcdFx0XHR0aGlzLl9zeW5jX2dyb3VwX2Zyb21fd3JpdGVyKGdyb3Vwc1tpXSk7XHJcblx0XHRcdFx0dGhpcy5fYXBwbHlfYm91bmRzX2Zvcl9jdXJyZW50X3VuaXQoZ3JvdXBzW2ldKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIEludGVybmFsXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRfZGVib3VuY2VkX3JlZnJlc2goKSB7XHJcblx0XHRcdGlmICh0aGlzLl9yZWZyZXNoX3RtcikgY2xlYXJUaW1lb3V0KHRoaXMuX3JlZnJlc2hfdG1yKTtcclxuXHRcdFx0dGhpcy5fcmVmcmVzaF90bXIgPSBzZXRUaW1lb3V0KCgpID0+IHtcclxuXHRcdFx0XHR0aGlzLl9yZWZyZXNoX3RtciA9IG51bGw7XHJcblx0XHRcdFx0dGhpcy5yZWZyZXNoKCk7XHJcblx0XHRcdH0sIE51bWJlcih0aGlzLm9wdHMub2JzZXJ2ZXJfZGVib3VuY2VfbXMpIHx8IDApO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9maW5kX2dyb3VwKGVsKSB7XHJcblx0XHRcdHJldHVybiAoZWwgJiYgZWwuY2xvc2VzdCkgPyBlbC5jbG9zZXN0KHRoaXMub3B0cy5ncm91cF9zZWxlY3RvcikgOiBudWxsO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9nZXRfcGFydHMoZ3JvdXApIHtcclxuXHRcdFx0aWYgKCFncm91cCkgcmV0dXJuIG51bGw7XHJcblx0XHRcdHJldHVybiB7XHJcblx0XHRcdFx0Z3JvdXAgOiBncm91cCxcclxuXHRcdFx0XHRudW0gICA6IGdyb3VwLnF1ZXJ5U2VsZWN0b3IodGhpcy5vcHRzLnZhbHVlX3NlbGVjdG9yKSxcclxuXHRcdFx0XHR1bml0ICA6IGdyb3VwLnF1ZXJ5U2VsZWN0b3IodGhpcy5vcHRzLnVuaXRfc2VsZWN0b3IpLFxyXG5cdFx0XHRcdHJhbmdlIDogZ3JvdXAucXVlcnlTZWxlY3Rvcih0aGlzLm9wdHMucmFuZ2Vfc2VsZWN0b3IpLFxyXG5cdFx0XHRcdHdyaXRlcjogZ3JvdXAucXVlcnlTZWxlY3Rvcih0aGlzLm9wdHMud3JpdGVyX3NlbGVjdG9yKVxyXG5cdFx0XHR9O1xyXG5cdFx0fVxyXG5cclxuXHRcdF9nZXRfZGVmYXVsdF91bml0KGdyb3VwKSB7XHJcblx0XHRcdHZhciBkdSA9IChncm91cCAmJiBncm91cC5nZXRBdHRyaWJ1dGUpXHJcblx0XHRcdFx0PyBncm91cC5nZXRBdHRyaWJ1dGUoJ2RhdGEtd3BiY19zbGlkZXJfbGVuX2RlZmF1bHRfdW5pdCcpXHJcblx0XHRcdFx0OiAnJztcclxuXHRcdFx0cmV0dXJuIGR1ID8gU3RyaW5nKGR1KSA6IHRoaXMub3B0cy5kZWZhdWx0X3VuaXQ7XHJcblx0XHR9XHJcblxyXG5cdFx0X2dldF9ib3VuZHNfbWFwKGdyb3VwKSB7XHJcblx0XHRcdGlmICghZ3JvdXApIHJldHVybiBudWxsO1xyXG5cdFx0XHRpZiAodGhpcy5fYm91bmRzX2NhY2hlLmhhcyhncm91cCkpIHtcclxuXHRcdFx0XHRyZXR1cm4gdGhpcy5fYm91bmRzX2NhY2hlLmdldChncm91cCk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciByYXcgPSBncm91cC5nZXRBdHRyaWJ1dGUoJ2RhdGEtd3BiY19zbGlkZXJfbGVuX2JvdW5kc19tYXAnKTtcclxuXHRcdFx0dmFyIG1hcCA9IHJhdyA/IHNhZmVfanNvbl9wYXJzZShyYXcpIDogbnVsbDtcclxuXHRcdFx0aWYgKCFtYXAgfHwgdHlwZW9mIG1hcCAhPT0gJ29iamVjdCcpIG1hcCA9IG51bGw7XHJcblxyXG5cdFx0XHR0aGlzLl9ib3VuZHNfY2FjaGUuc2V0KGdyb3VwLCBtYXApO1xyXG5cdFx0XHRyZXR1cm4gbWFwO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9nZXRfYm91bmRzX2Zvcl91bml0KGdyb3VwLCB1bml0KSB7XHJcblx0XHRcdHZhciBtYXAgPSB0aGlzLl9nZXRfYm91bmRzX21hcChncm91cCk7XHJcblx0XHRcdGlmIChtYXAgJiYgdW5pdCAmJiBtYXBbdW5pdF0pIHtcclxuXHRcdFx0XHRyZXR1cm4gbWFwW3VuaXRdO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiB0aGlzLm9wdHMuZmFsbGJhY2tfYm91bmRzW3VuaXRdIHx8IHRoaXMub3B0cy5mYWxsYmFja19ib3VuZHNbJ3B4J107XHJcblx0XHR9XHJcblxyXG5cdFx0X2FwcGx5X2JvdW5kcyhwYXJ0cywgYm91bmRzKSB7XHJcblx0XHRcdGlmICghcGFydHMgfHwgIWJvdW5kcykgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIG1pbiAgPSAoYm91bmRzLm1pbiAgIT0gbnVsbCkgPyBOdW1iZXIoYm91bmRzLm1pbikgIDogbnVsbDtcclxuXHRcdFx0dmFyIG1heCAgPSAoYm91bmRzLm1heCAgIT0gbnVsbCkgPyBOdW1iZXIoYm91bmRzLm1heCkgIDogbnVsbDtcclxuXHRcdFx0dmFyIHN0ZXAgPSAoYm91bmRzLnN0ZXAgIT0gbnVsbCkgPyBOdW1iZXIoYm91bmRzLnN0ZXApIDogbnVsbDtcclxuXHJcblx0XHRcdGlmIChwYXJ0cy5yYW5nZSkge1xyXG5cdFx0XHRcdGlmICghaXNOYU4obWluKSkgIHBhcnRzLnJhbmdlLm1pbiAgPSBTdHJpbmcobWluKTtcclxuXHRcdFx0XHRpZiAoIWlzTmFOKG1heCkpICBwYXJ0cy5yYW5nZS5tYXggID0gU3RyaW5nKG1heCk7XHJcblx0XHRcdFx0aWYgKCFpc05hTihzdGVwKSkgcGFydHMucmFuZ2Uuc3RlcCA9IFN0cmluZyhzdGVwKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAocGFydHMubnVtKSB7XHJcblx0XHRcdFx0aWYgKCFpc05hTihtaW4pKSAgcGFydHMubnVtLm1pbiAgPSBTdHJpbmcobWluKTtcclxuXHRcdFx0XHRpZiAoIWlzTmFOKG1heCkpICBwYXJ0cy5udW0ubWF4ICA9IFN0cmluZyhtYXgpO1xyXG5cdFx0XHRcdGlmICghaXNOYU4oc3RlcCkpIHBhcnRzLm51bS5zdGVwID0gU3RyaW5nKHN0ZXApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0X2FwcGx5X2JvdW5kc19mb3JfY3VycmVudF91bml0KGdyb3VwKSB7XHJcblx0XHRcdHZhciBwYXJ0cyA9IHRoaXMuX2dldF9wYXJ0cyhncm91cCk7XHJcblx0XHRcdGlmICghcGFydHMgfHwgIXBhcnRzLnVuaXQpIHJldHVybjtcclxuXHJcblx0XHRcdHZhciB1bml0ID0gcGFydHMudW5pdC52YWx1ZSB8fCB0aGlzLl9nZXRfZGVmYXVsdF91bml0KGdyb3VwKTtcclxuXHRcdFx0dmFyIGIgICAgPSB0aGlzLl9nZXRfYm91bmRzX2Zvcl91bml0KGdyb3VwLCB1bml0KTtcclxuXHJcblx0XHRcdHRoaXMuX2FwcGx5X2JvdW5kcyhwYXJ0cywgYik7XHJcblxyXG5cdFx0XHQvLyBDbGFtcCBjdXJyZW50IHZhbHVlIHRvIG5ldyBib3VuZHMuXHJcblx0XHRcdHZhciB2ID0gcGFyc2VfZmxvYXQocGFydHMubnVtICYmIHBhcnRzLm51bS52YWx1ZSA/IHBhcnRzLm51bS52YWx1ZSA6IChwYXJ0cy5yYW5nZSA/IHBhcnRzLnJhbmdlLnZhbHVlIDogJycpKTtcclxuXHRcdFx0aWYgKHYgPT0gbnVsbCkgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIG1pbiA9IChiICYmIGIubWluICE9IG51bGwpID8gTnVtYmVyKGIubWluKSA6IG51bGw7XHJcblx0XHRcdHZhciBtYXggPSAoYiAmJiBiLm1heCAhPSBudWxsKSA/IE51bWJlcihiLm1heCkgOiBudWxsO1xyXG5cdFx0XHR2ID0gY2xhbXBfbnVtKHYsIGlzTmFOKG1pbikgPyBudWxsIDogbWluLCBpc05hTihtYXgpID8gbnVsbCA6IG1heCk7XHJcblxyXG5cdFx0XHRpZiAocGFydHMubnVtKSAgIHBhcnRzLm51bS52YWx1ZSAgID0gU3RyaW5nKHYpO1xyXG5cdFx0XHRpZiAocGFydHMucmFuZ2UpIHBhcnRzLnJhbmdlLnZhbHVlID0gU3RyaW5nKHYpO1xyXG5cclxuXHRcdFx0dGhpcy5fd3JpdGVfY29tYmluZWQocGFydHMsIFN0cmluZyh2KSwgdW5pdCwgLyplbWl0Ki8gZmFsc2UpO1xyXG5cdFx0fVxyXG5cclxuXHRcdF93cml0ZV9jb21iaW5lZChwYXJ0cywgbnVtLCB1bml0LCBlbWl0KSB7XHJcblx0XHRcdGlmICghcGFydHMpIHJldHVybjtcclxuXHJcblx0XHRcdHZhciBjb21iaW5lZCA9IGJ1aWxkX2NvbWJpbmVkKG51bSwgdW5pdCk7XHJcblxyXG5cdFx0XHRpZiAocGFydHMud3JpdGVyKSB7XHJcblx0XHRcdFx0Ly8gQXZvaWQgcmVjdXJzaW9uOiBtYXJrIGFzIGludGVybmFsIHdyaXRlLlxyXG5cdFx0XHRcdHBhcnRzLndyaXRlci5fX3dwYmNfc2xpZGVyX2xlbl9pbnRlcm5hbCA9IHRydWU7XHJcblx0XHRcdFx0cGFydHMud3JpdGVyLnZhbHVlID0gY29tYmluZWQ7XHJcblx0XHRcdFx0aWYgKGVtaXQpIGVtaXRfaW5wdXQocGFydHMud3JpdGVyKTtcclxuXHRcdFx0XHRwYXJ0cy53cml0ZXIuX193cGJjX3NsaWRlcl9sZW5faW50ZXJuYWwgPSBmYWxzZTtcclxuXHRcdFx0fSBlbHNlIGlmIChwYXJ0cy5udW0pIHtcclxuXHRcdFx0XHQvLyBJZiB3cml0ZXIgaXMgbWlzc2luZywgYXQgbGVhc3Qgbm90aWZ5IHZpYSBudW1iZXIgaW5wdXQuXHJcblx0XHRcdFx0aWYgKGVtaXQpIGVtaXRfaW5wdXQocGFydHMubnVtKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdF9zeW5jX2dyb3VwX2Zyb21fd3JpdGVyKGdyb3VwKSB7XHJcblx0XHRcdHZhciBwYXJ0cyA9IHRoaXMuX2dldF9wYXJ0cyhncm91cCk7XHJcblx0XHRcdGlmICghcGFydHMgfHwgIXBhcnRzLndyaXRlcikgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIHJhdyA9IFN0cmluZyhwYXJ0cy53cml0ZXIudmFsdWUgfHwgJycpLnRyaW0oKTtcclxuXHRcdFx0aWYgKCFyYXcpIHJldHVybjtcclxuXHJcblx0XHRcdHZhciBkdSA9IHRoaXMuX2dldF9kZWZhdWx0X3VuaXQoZ3JvdXApO1xyXG5cdFx0XHR2YXIgcCAgPSBwYXJzZV9sZW5fY29tYmluZWQocmF3LCBkdSk7XHJcblxyXG5cdFx0XHRpZiAocGFydHMudW5pdCkgIHBhcnRzLnVuaXQudmFsdWUgID0gcC51bml0O1xyXG5cdFx0XHRpZiAocGFydHMubnVtKSAgIHBhcnRzLm51bS52YWx1ZSAgID0gcC5udW07XHJcblx0XHRcdGlmIChwYXJ0cy5yYW5nZSkgcGFydHMucmFuZ2UudmFsdWUgPSBwLm51bTtcclxuXHRcdH1cclxuXHJcblx0XHRfb25faW5wdXQoZXYpIHtcclxuXHRcdFx0dmFyIHQgPSBldi50YXJnZXQ7XHJcblx0XHRcdGlmICghdCkgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIGdyb3VwID0gdGhpcy5fZmluZF9ncm91cCh0KTtcclxuXHRcdFx0aWYgKCFncm91cCkgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIHBhcnRzID0gdGhpcy5fZ2V0X3BhcnRzKGdyb3VwKTtcclxuXHRcdFx0aWYgKCFwYXJ0cykgcmV0dXJuO1xyXG5cclxuXHRcdFx0Ly8gV3JpdGVyIGNoYW5nZWQgZXh0ZXJuYWxseSAtPiB1cGRhdGUgVUkuXHJcblx0XHRcdGlmIChwYXJ0cy53cml0ZXIgJiYgdCA9PT0gcGFydHMud3JpdGVyKSB7XHJcblx0XHRcdFx0aWYgKHQuX193cGJjX3NsaWRlcl9sZW5faW50ZXJuYWwpIHJldHVybjtcclxuXHRcdFx0XHR0aGlzLl9zeW5jX2dyb3VwX2Zyb21fd3JpdGVyKGdyb3VwKTtcclxuXHRcdFx0XHR0aGlzLl9hcHBseV9ib3VuZHNfZm9yX2N1cnJlbnRfdW5pdChncm91cCk7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBTbGlkZXIgbW92ZWQgLT4gdXBkYXRlIG51bWJlciArIHdyaXRlci5cclxuXHRcdFx0aWYgKHQubWF0Y2hlcyAmJiB0Lm1hdGNoZXModGhpcy5vcHRzLnJhbmdlX3NlbGVjdG9yKSkge1xyXG5cdFx0XHRcdGlmIChwYXJ0cy5udW0pIHBhcnRzLm51bS52YWx1ZSA9IHQudmFsdWU7XHJcblxyXG5cdFx0XHRcdHZhciB1bml0ID0gKHBhcnRzLnVuaXQgJiYgcGFydHMudW5pdC52YWx1ZSkgPyBwYXJ0cy51bml0LnZhbHVlIDogdGhpcy5fZ2V0X2RlZmF1bHRfdW5pdChncm91cCk7XHJcblx0XHRcdFx0dGhpcy5fd3JpdGVfY29tYmluZWQocGFydHMsIHQudmFsdWUsIHVuaXQsIC8qZW1pdCovIHRydWUpO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTnVtYmVyIHR5cGVkIC0+IHVwZGF0ZSBzbGlkZXIgKyB3cml0ZXIgKGNsYW1wIGlmIHNsaWRlciBoYXMgYm91bmRzKS5cclxuXHRcdFx0aWYgKHQubWF0Y2hlcyAmJiB0Lm1hdGNoZXModGhpcy5vcHRzLnZhbHVlX3NlbGVjdG9yKSkge1xyXG5cdFx0XHRcdHZhciB2ID0gcGFyc2VfZmxvYXQodC52YWx1ZSk7XHJcblxyXG5cdFx0XHRcdGlmICh2ICE9IG51bGwgJiYgcGFydHMucmFuZ2UpIHtcclxuXHRcdFx0XHRcdHZhciBybWluID0gTnVtYmVyKHBhcnRzLnJhbmdlLm1pbik7XHJcblx0XHRcdFx0XHR2YXIgcm1heCA9IE51bWJlcihwYXJ0cy5yYW5nZS5tYXgpO1xyXG5cdFx0XHRcdFx0diA9IGNsYW1wX251bSh2LCBpc05hTihybWluKSA/IG51bGwgOiBybWluLCBpc05hTihybWF4KSA/IG51bGwgOiBybWF4KTtcclxuXHJcblx0XHRcdFx0XHRwYXJ0cy5yYW5nZS52YWx1ZSA9IFN0cmluZyh2KTtcclxuXHRcdFx0XHRcdGlmIChTdHJpbmcodikgIT09IHQudmFsdWUpIHQudmFsdWUgPSBTdHJpbmcodik7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR2YXIgdW5pdDIgPSAocGFydHMudW5pdCAmJiBwYXJ0cy51bml0LnZhbHVlKSA/IHBhcnRzLnVuaXQudmFsdWUgOiB0aGlzLl9nZXRfZGVmYXVsdF91bml0KGdyb3VwKTtcclxuXHRcdFx0XHR0aGlzLl93cml0ZV9jb21iaW5lZChwYXJ0cywgdC52YWx1ZSwgdW5pdDIsIC8qZW1pdCovIHRydWUpO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0X29uX2NoYW5nZShldikge1xyXG5cdFx0XHR2YXIgdCA9IGV2LnRhcmdldDtcclxuXHRcdFx0aWYgKCF0KSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgZ3JvdXAgPSB0aGlzLl9maW5kX2dyb3VwKHQpO1xyXG5cdFx0XHRpZiAoIWdyb3VwKSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgcGFydHMgPSB0aGlzLl9nZXRfcGFydHMoZ3JvdXApO1xyXG5cdFx0XHRpZiAoIXBhcnRzKSByZXR1cm47XHJcblxyXG5cdFx0XHQvLyBVbml0IGNoYW5nZWQgLT4gdXBkYXRlIGJvdW5kcyArIHdyaXRlci5cclxuXHRcdFx0aWYgKHQubWF0Y2hlcyAmJiB0Lm1hdGNoZXModGhpcy5vcHRzLnVuaXRfc2VsZWN0b3IpKSB7XHJcblx0XHRcdFx0dGhpcy5fYXBwbHlfYm91bmRzX2Zvcl9jdXJyZW50X3VuaXQoZ3JvdXApO1xyXG5cclxuXHRcdFx0XHR2YXIgbnVtICA9IHBhcnRzLm51bSA/IHBhcnRzLm51bS52YWx1ZSA6IChwYXJ0cy5yYW5nZSA/IHBhcnRzLnJhbmdlLnZhbHVlIDogJycpO1xyXG5cdFx0XHRcdHZhciB1bml0ID0gdC52YWx1ZSB8fCB0aGlzLl9nZXRfZGVmYXVsdF91bml0KGdyb3VwKTtcclxuXHRcdFx0XHR0aGlzLl93cml0ZV9jb21iaW5lZChwYXJ0cywgbnVtLCB1bml0LCAvKmVtaXQqLyB0cnVlKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEF1dG8taW5pdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRmdW5jdGlvbiB3cGJjX3NsaWRlcl9sZW5fZ3JvdXBzX19hdXRvX2luaXQoKSB7XHJcblx0XHR2YXIgUk9PVCAgPSAnLndwYmNfc2xpZGVyX2xlbl9ncm91cHMnO1xyXG5cdFx0dmFyIG5vZGVzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoZC5xdWVyeVNlbGVjdG9yQWxsKFJPT1QpKVxyXG5cdFx0XHQuZmlsdGVyKGZ1bmN0aW9uIChuKSB7IHJldHVybiAhbi5wYXJlbnRFbGVtZW50IHx8ICFuLnBhcmVudEVsZW1lbnQuY2xvc2VzdChST09UKTsgfSk7XHJcblxyXG5cdFx0Ly8gSWYgbm8gZXhwbGljaXQgY29udGFpbmVycywgaW5zdGFsbCBhIHNpbmdsZSBkb2N1bWVudC1yb290IGluc3RhbmNlLlxyXG5cdFx0aWYgKCFub2Rlcy5sZW5ndGgpIHtcclxuXHRcdFx0aWYgKCFkLl9fd3BiY19zbGlkZXJfbGVuX2dyb3Vwc19nbG9iYWxfaW5zdGFuY2UpIHtcclxuXHRcdFx0XHRkLl9fd3BiY19zbGlkZXJfbGVuX2dyb3Vwc19nbG9iYWxfaW5zdGFuY2UgPSBuZXcgV1BCQ19TbGlkZXJfTGVuX0dyb3VwcyhkKS5pbml0KCk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdG5vZGVzLmZvckVhY2goZnVuY3Rpb24gKG5vZGUpIHtcclxuXHRcdFx0aWYgKG5vZGUuX193cGJjX3NsaWRlcl9sZW5fZ3JvdXBzX2luc3RhbmNlKSByZXR1cm47XHJcblx0XHRcdG5vZGUuX193cGJjX3NsaWRlcl9sZW5fZ3JvdXBzX2luc3RhbmNlID0gbmV3IFdQQkNfU2xpZGVyX0xlbl9Hcm91cHMobm9kZSkuaW5pdCgpO1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cclxuXHQvLyBFeHBvcnQgZ2xvYmFscyAobWFudWFsIGNvbnRyb2wgaWYgbmVlZGVkKS5cclxuXHR3LldQQkNfU2xpZGVyX0xlbl9Hcm91cHMgICA9IFdQQkNfU2xpZGVyX0xlbl9Hcm91cHM7XHJcblx0dy5XUEJDX1NsaWRlcl9MZW5fQXV0b0luaXQgPSB3cGJjX3NsaWRlcl9sZW5fZ3JvdXBzX19hdXRvX2luaXQ7XHJcblxyXG5cdC8vIERPTS1yZWFkeSBhdXRvIGluaXQuXHJcblx0aWYgKGQucmVhZHlTdGF0ZSA9PT0gJ2xvYWRpbmcnKSB7XHJcblx0XHRkLmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCB3cGJjX3NsaWRlcl9sZW5fZ3JvdXBzX19hdXRvX2luaXQsIHsgb25jZTogdHJ1ZSB9KTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0d3BiY19zbGlkZXJfbGVuX2dyb3Vwc19fYXV0b19pbml0KCk7XHJcblx0fVxyXG5cclxufSkod2luZG93LCBkb2N1bWVudCk7XHJcbiIsIi8qIGdsb2JhbHMgd2luZG93LCBkb2N1bWVudCAqL1xyXG4vKipcclxuICogV1BCQyBTbGlkZXIgUmFuZ2UgR3JvdXBzXHJcbiAqXHJcbiAqIFVuaXZlcnNhbCwgZGVwZW5kZW5jeS1mcmVlIGNvbnRyb2xsZXIgdGhhdCBrZWVwcyBhIFwicmFuZ2UgKyBudW1iZXJcIiBwYWlyIGluIHN5bmM6XHJcbiAqICAtIG51bWJlciBpbnB1dCAgKGRhdGEtd3BiY19zbGlkZXJfcmFuZ2VfdmFsdWUpXHJcbiAqICAtIHJhbmdlIHNsaWRlciAgKGRhdGEtd3BiY19zbGlkZXJfcmFuZ2VfcmFuZ2UpXHJcbiAqICAtIHdyaXRlciBpbnB1dCAgKGRhdGEtd3BiY19zbGlkZXJfcmFuZ2Vfd3JpdGVyKSBbb3B0aW9uYWxdXHJcbiAqXHJcbiAqIElmIHdyaXRlciBleGlzdHM6IG51bWJlci9zbGlkZXIgdXBkYXRlIHdyaXRlciBhbmQgZW1pdCAnaW5wdXQnIG9uIHdyaXRlciAoYnViYmxlcykuXHJcbiAqIElmIHdyaXRlciBpcyBtaXNzaW5nOiBlbWl0cyAnaW5wdXQnIG9uIHRoZSBudW1iZXIgaW5wdXQuXHJcbiAqIElmIHdyaXRlciBjaGFuZ2VzIGV4dGVybmFsbHk6IHVwZGF0ZXMgbnVtYmVyL3NsaWRlci5cclxuICpcclxuICogTWFya3VwIGV4cGVjdGF0aW9ucyAobWluaW1hbCk6XHJcbiAqICA8ZGl2IGNsYXNzPVwid3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBcIj5cclxuICogICAgPGlucHV0IHR5cGU9XCJudW1iZXJcIiBkYXRhLXdwYmNfc2xpZGVyX3JhbmdlX3ZhbHVlPlxyXG4gKiAgICA8aW5wdXQgdHlwZT1cInJhbmdlXCIgIGRhdGEtd3BiY19zbGlkZXJfcmFuZ2VfcmFuZ2U+XHJcbiAqICAgIDwhLS0gb3B0aW9uYWwgLS0+XHJcbiAqICAgIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtd3BiY19zbGlkZXJfcmFuZ2Vfd3JpdGVyIHN0eWxlPVwiZGlzcGxheTpub25lO1wiPlxyXG4gKiAgPC9kaXY+XHJcbiAqXHJcbiAqIFBlcmZvcm1hbmNlIG5vdGVzOlxyXG4gKiAtIE11dGF0aW9uT2JzZXJ2ZXIgaXMgRElTQUJMRUQgYnkgZGVmYXVsdC5cclxuICogLSBJZiB5b3VyIFVJIHJlLXJlbmRlcnMgYW5kIGluc2VydHMgbmV3IGdyb3VwcyBkeW5hbWljYWxseSwgY2FsbDpcclxuICogICAgIFdQQkNfU2xpZGVyX1JhbmdlX0F1dG9Jbml0KCk7IE9SIGluc3RhbmNlLnJlZnJlc2goKTtcclxuICogICBPciBlbmFibGUgb2JzZXJ2ZXIgdmlhOiBuZXcgV1BCQ19TbGlkZXJfUmFuZ2VfR3JvdXBzKHJvb3QsIHsgZW5hYmxlX29ic2VydmVyOnRydWUgfSkuaW5pdCgpO1xyXG4gKlxyXG4gKiBQdWJsaWMgQVBJIChpbnN0YW5jZSBtZXRob2RzKTpcclxuICogIC0gaW5pdCgpLCBkZXN0cm95KCksIHJlZnJlc2goKVxyXG4gKlxyXG4gKiBAdmVyc2lvbiAyMDI2LTAxLTI1XHJcbiAqIEBzaW5jZSAgIDIwMjYtMDEtMjVcclxuICogQGZpbGUgICAgLi4vaW5jbHVkZXMvX19qcy9hZG1pbi9zbGlkZXJfZ3JvdXBzL3dwYmNfcmFuZ2VfZ3JvdXBzLmpzXHJcbiAqL1xyXG4oZnVuY3Rpb24gKHcsIGQpIHtcclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBIZWxwZXJzXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGZ1bmN0aW9uIGNsYW1wX251bSh2LCBtaW4sIG1heCkge1xyXG5cdFx0aWYgKHR5cGVvZiBtaW4gPT09ICdudW1iZXInICYmICFpc05hTihtaW4pKSB2ID0gTWF0aC5tYXgobWluLCB2KTtcclxuXHRcdGlmICh0eXBlb2YgbWF4ID09PSAnbnVtYmVyJyAmJiAhaXNOYU4obWF4KSkgdiA9IE1hdGgubWluKG1heCwgdik7XHJcblx0XHRyZXR1cm4gdjtcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIHBhcnNlX2Zsb2F0KHYpIHtcclxuXHRcdHZhciBuID0gcGFyc2VGbG9hdCh2KTtcclxuXHRcdHJldHVybiBpc05hTihuKSA/IG51bGwgOiBuO1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gZW1pdF9pbnB1dChlbCkge1xyXG5cdFx0aWYgKCFlbCkgcmV0dXJuO1xyXG5cdFx0ZWwuZGlzcGF0Y2hFdmVudChuZXcgRXZlbnQoJ2lucHV0JywgeyBidWJibGVzOiB0cnVlIH0pKTtcclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBDb250cm9sbGVyXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGNsYXNzIFdQQkNfU2xpZGVyX1JhbmdlX0dyb3VwcyB7XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fHN0cmluZ30gcm9vdF9lbCBDb250YWluZXIgKG9yIHNlbGVjdG9yKS4gSWYgb21pdHRlZCwgdXNlcyBkb2N1bWVudC5cclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBbb3B0cz17fV1cclxuXHRcdCAqL1xyXG5cdFx0Y29uc3RydWN0b3Iocm9vdF9lbCwgb3B0cykge1xyXG5cdFx0XHR0aGlzLnJvb3QgPSByb290X2VsXHJcblx0XHRcdFx0PyAoKHR5cGVvZiByb290X2VsID09PSAnc3RyaW5nJykgPyBkLnF1ZXJ5U2VsZWN0b3Iocm9vdF9lbCkgOiByb290X2VsKVxyXG5cdFx0XHRcdDogZDtcclxuXHJcblx0XHRcdHRoaXMub3B0cyA9IE9iamVjdC5hc3NpZ24oe1xyXG5cdFx0XHRcdC8vIFN0cmljdCBzZWxlY3RvcnMgKE5PIGJhY2t3YXJkIGNvbXBhdGliaWxpdHkpLlxyXG5cdFx0XHRcdGdyb3VwX3NlbGVjdG9yICA6ICcud3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXAnLFxyXG5cdFx0XHRcdHZhbHVlX3NlbGVjdG9yICA6ICdbZGF0YS13cGJjX3NsaWRlcl9yYW5nZV92YWx1ZV0nLFxyXG5cdFx0XHRcdHJhbmdlX3NlbGVjdG9yICA6ICdbZGF0YS13cGJjX3NsaWRlcl9yYW5nZV9yYW5nZV0nLFxyXG5cdFx0XHRcdHdyaXRlcl9zZWxlY3RvciA6ICdbZGF0YS13cGJjX3NsaWRlcl9yYW5nZV93cml0ZXJdJyxcclxuXHJcblx0XHRcdFx0Ly8gRGlzYWJsZWQgYnkgZGVmYXVsdCBmb3IgcGVyZm9ybWFuY2UuXHJcblx0XHRcdFx0ZW5hYmxlX29ic2VydmVyICAgICA6IGZhbHNlLFxyXG5cdFx0XHRcdG9ic2VydmVyX2RlYm91bmNlX21zOiAxNTBcclxuXHRcdFx0fSwgb3B0cyB8fCB7fSk7XHJcblxyXG5cdFx0XHR0aGlzLl9vbl9pbnB1dCAgPSB0aGlzLl9vbl9pbnB1dC5iaW5kKHRoaXMpO1xyXG5cdFx0XHR0aGlzLl9vbl9jaGFuZ2UgPSB0aGlzLl9vbl9jaGFuZ2UuYmluZCh0aGlzKTtcclxuXHJcblx0XHRcdHRoaXMuX29ic2VydmVyICAgID0gbnVsbDtcclxuXHRcdFx0dGhpcy5fcmVmcmVzaF90bXIgPSBudWxsO1xyXG5cdFx0fVxyXG5cclxuXHRcdGluaXQoKSB7XHJcblx0XHRcdGlmICghdGhpcy5yb290KSByZXR1cm4gdGhpcztcclxuXHJcblx0XHRcdHRoaXMucm9vdC5hZGRFdmVudExpc3RlbmVyKCdpbnB1dCcsICB0aGlzLl9vbl9pbnB1dCwgIHRydWUpO1xyXG5cdFx0XHR0aGlzLnJvb3QuYWRkRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgdGhpcy5fb25fY2hhbmdlLCB0cnVlKTtcclxuXHJcblx0XHRcdGlmICh0aGlzLm9wdHMuZW5hYmxlX29ic2VydmVyICYmIHcuTXV0YXRpb25PYnNlcnZlcikge1xyXG5cdFx0XHRcdHRoaXMuX29ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIoKCkgPT4geyB0aGlzLl9kZWJvdW5jZWRfcmVmcmVzaCgpOyB9KTtcclxuXHRcdFx0XHR0aGlzLl9vYnNlcnZlci5vYnNlcnZlKHRoaXMucm9vdCA9PT0gZCA/IGQuZG9jdW1lbnRFbGVtZW50IDogdGhpcy5yb290LCB7IGNoaWxkTGlzdDogdHJ1ZSwgc3VidHJlZTogdHJ1ZSB9KTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dGhpcy5yZWZyZXNoKCk7XHJcblx0XHRcdHJldHVybiB0aGlzO1xyXG5cdFx0fVxyXG5cclxuXHRcdGRlc3Ryb3koKSB7XHJcblx0XHRcdGlmICghdGhpcy5yb290KSByZXR1cm47XHJcblxyXG5cdFx0XHR0aGlzLnJvb3QucmVtb3ZlRXZlbnRMaXN0ZW5lcignaW5wdXQnLCAgdGhpcy5fb25faW5wdXQsICB0cnVlKTtcclxuXHRcdFx0dGhpcy5yb290LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2NoYW5nZScsIHRoaXMuX29uX2NoYW5nZSwgdHJ1ZSk7XHJcblxyXG5cdFx0XHRpZiAodGhpcy5fb2JzZXJ2ZXIpIHtcclxuXHRcdFx0XHR0aGlzLl9vYnNlcnZlci5kaXNjb25uZWN0KCk7XHJcblx0XHRcdFx0dGhpcy5fb2JzZXJ2ZXIgPSBudWxsO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAodGhpcy5fcmVmcmVzaF90bXIpIHtcclxuXHRcdFx0XHRjbGVhclRpbWVvdXQodGhpcy5fcmVmcmVzaF90bXIpO1xyXG5cdFx0XHRcdHRoaXMuX3JlZnJlc2hfdG1yID0gbnVsbDtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHJlZnJlc2goKSB7XHJcblx0XHRcdGlmICghdGhpcy5yb290KSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgc2NvcGUgID0gKHRoaXMucm9vdCA9PT0gZCA/IGQgOiB0aGlzLnJvb3QpO1xyXG5cdFx0XHR2YXIgZ3JvdXBzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoc2NvcGUucXVlcnlTZWxlY3RvckFsbCh0aGlzLm9wdHMuZ3JvdXBfc2VsZWN0b3IpKTtcclxuXHJcblx0XHRcdGZvciAodmFyIGkgPSAwOyBpIDwgZ3JvdXBzLmxlbmd0aDsgaSsrKSB7XHJcblx0XHRcdFx0dGhpcy5fc3luY19mcm9tX3dyaXRlcihncm91cHNbaV0pO1xyXG5cdFx0XHRcdHRoaXMuX2NsYW1wX3RvX3JhbmdlKGdyb3Vwc1tpXSk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBJbnRlcm5hbFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0X2RlYm91bmNlZF9yZWZyZXNoKCkge1xyXG5cdFx0XHRpZiAodGhpcy5fcmVmcmVzaF90bXIpIGNsZWFyVGltZW91dCh0aGlzLl9yZWZyZXNoX3Rtcik7XHJcblx0XHRcdHRoaXMuX3JlZnJlc2hfdG1yID0gc2V0VGltZW91dCgoKSA9PiB7XHJcblx0XHRcdFx0dGhpcy5fcmVmcmVzaF90bXIgPSBudWxsO1xyXG5cdFx0XHRcdHRoaXMucmVmcmVzaCgpO1xyXG5cdFx0XHR9LCBOdW1iZXIodGhpcy5vcHRzLm9ic2VydmVyX2RlYm91bmNlX21zKSB8fCAwKTtcclxuXHRcdH1cclxuXHJcblx0XHRfZmluZF9ncm91cChlbCkge1xyXG5cdFx0XHRyZXR1cm4gKGVsICYmIGVsLmNsb3Nlc3QpID8gZWwuY2xvc2VzdCh0aGlzLm9wdHMuZ3JvdXBfc2VsZWN0b3IpIDogbnVsbDtcclxuXHRcdH1cclxuXHJcblx0XHRfZ2V0X3BhcnRzKGdyb3VwKSB7XHJcblx0XHRcdGlmICghZ3JvdXApIHJldHVybiBudWxsO1xyXG5cdFx0XHRyZXR1cm4ge1xyXG5cdFx0XHRcdGdyb3VwIDogZ3JvdXAsXHJcblx0XHRcdFx0bnVtICAgOiBncm91cC5xdWVyeVNlbGVjdG9yKHRoaXMub3B0cy52YWx1ZV9zZWxlY3RvciksXHJcblx0XHRcdFx0cmFuZ2UgOiBncm91cC5xdWVyeVNlbGVjdG9yKHRoaXMub3B0cy5yYW5nZV9zZWxlY3RvciksXHJcblx0XHRcdFx0d3JpdGVyOiBncm91cC5xdWVyeVNlbGVjdG9yKHRoaXMub3B0cy53cml0ZXJfc2VsZWN0b3IpXHJcblx0XHRcdH07XHJcblx0XHR9XHJcblxyXG5cdFx0X3dyaXRlKHBhcnRzLCB2YWx1ZSwgZW1pdCkge1xyXG5cdFx0XHRpZiAoIXBhcnRzKSByZXR1cm47XHJcblxyXG5cdFx0XHRpZiAocGFydHMud3JpdGVyKSB7XHJcblx0XHRcdFx0cGFydHMud3JpdGVyLl9fd3BiY19zbGlkZXJfcmFuZ2VfaW50ZXJuYWwgPSB0cnVlO1xyXG5cdFx0XHRcdHBhcnRzLndyaXRlci52YWx1ZSA9IFN0cmluZyh2YWx1ZSk7XHJcblx0XHRcdFx0aWYgKGVtaXQpIGVtaXRfaW5wdXQocGFydHMud3JpdGVyKTtcclxuXHRcdFx0XHRwYXJ0cy53cml0ZXIuX193cGJjX3NsaWRlcl9yYW5nZV9pbnRlcm5hbCA9IGZhbHNlO1xyXG5cdFx0XHR9IGVsc2UgaWYgKHBhcnRzLm51bSkge1xyXG5cdFx0XHRcdC8vIElmIHdyaXRlciBpcyBtaXNzaW5nLCBhdCBsZWFzdCBub3RpZnkgdmlhIG51bWJlciBpbnB1dC5cclxuXHRcdFx0XHRpZiAoZW1pdCkgZW1pdF9pbnB1dChwYXJ0cy5udW0pO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0X3N5bmNfZnJvbV93cml0ZXIoZ3JvdXApIHtcclxuXHRcdFx0dmFyIHBhcnRzID0gdGhpcy5fZ2V0X3BhcnRzKGdyb3VwKTtcclxuXHRcdFx0aWYgKCFwYXJ0cyB8fCAhcGFydHMud3JpdGVyKSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgcmF3ID0gU3RyaW5nKHBhcnRzLndyaXRlci52YWx1ZSB8fCAnJykudHJpbSgpO1xyXG5cdFx0XHRpZiAoIXJhdykgcmV0dXJuO1xyXG5cclxuXHRcdFx0aWYgKHBhcnRzLm51bSkgICBwYXJ0cy5udW0udmFsdWUgICA9IHJhdztcclxuXHRcdFx0aWYgKHBhcnRzLnJhbmdlKSBwYXJ0cy5yYW5nZS52YWx1ZSA9IHJhdztcclxuXHRcdH1cclxuXHJcblx0XHRfY2xhbXBfdG9fcmFuZ2UoZ3JvdXApIHtcclxuXHRcdFx0dmFyIHBhcnRzID0gdGhpcy5fZ2V0X3BhcnRzKGdyb3VwKTtcclxuXHRcdFx0aWYgKCFwYXJ0cyB8fCAhcGFydHMucmFuZ2UgfHwgIXBhcnRzLm51bSkgcmV0dXJuO1xyXG5cclxuXHRcdFx0dmFyIHYgPSBwYXJzZV9mbG9hdChwYXJ0cy5udW0udmFsdWUpO1xyXG5cdFx0XHRpZiAodiA9PSBudWxsKSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgbWluID0gTnVtYmVyKHBhcnRzLnJhbmdlLm1pbik7XHJcblx0XHRcdHZhciBtYXggPSBOdW1iZXIocGFydHMucmFuZ2UubWF4KTtcclxuXHRcdFx0dmFyIHZ2ICA9IGNsYW1wX251bSh2LCBpc05hTihtaW4pID8gbnVsbCA6IG1pbiwgaXNOYU4obWF4KSA/IG51bGwgOiBtYXgpO1xyXG5cclxuXHRcdFx0aWYgKFN0cmluZyh2dikgIT09IHBhcnRzLm51bS52YWx1ZSkgcGFydHMubnVtLnZhbHVlID0gU3RyaW5nKHZ2KTtcclxuXHRcdFx0cGFydHMucmFuZ2UudmFsdWUgPSBTdHJpbmcodnYpO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9vbl9pbnB1dChldikge1xyXG5cdFx0XHR2YXIgdCA9IGV2LnRhcmdldDtcclxuXHRcdFx0aWYgKCF0KSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgZ3JvdXAgPSB0aGlzLl9maW5kX2dyb3VwKHQpO1xyXG5cdFx0XHRpZiAoIWdyb3VwKSByZXR1cm47XHJcblxyXG5cdFx0XHR2YXIgcGFydHMgPSB0aGlzLl9nZXRfcGFydHMoZ3JvdXApO1xyXG5cdFx0XHRpZiAoIXBhcnRzKSByZXR1cm47XHJcblxyXG5cdFx0XHQvLyBXcml0ZXIgY2hhbmdlZCBleHRlcm5hbGx5IC0+IHVwZGF0ZSBVSS5cclxuXHRcdFx0aWYgKHBhcnRzLndyaXRlciAmJiB0ID09PSBwYXJ0cy53cml0ZXIpIHtcclxuXHRcdFx0XHRpZiAodC5fX3dwYmNfc2xpZGVyX3JhbmdlX2ludGVybmFsKSByZXR1cm47XHJcblx0XHRcdFx0dGhpcy5fc3luY19mcm9tX3dyaXRlcihncm91cCk7XHJcblx0XHRcdFx0dGhpcy5fY2xhbXBfdG9fcmFuZ2UoZ3JvdXApO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gUmFuZ2UgbW92ZWQgLT4gdXBkYXRlIG51bWJlciArIHdyaXRlci5cclxuXHRcdFx0aWYgKHQubWF0Y2hlcyAmJiB0Lm1hdGNoZXModGhpcy5vcHRzLnJhbmdlX3NlbGVjdG9yKSkge1xyXG5cdFx0XHRcdGlmIChwYXJ0cy5udW0pIHBhcnRzLm51bS52YWx1ZSA9IHQudmFsdWU7XHJcblx0XHRcdFx0dGhpcy5fd3JpdGUocGFydHMsIHQudmFsdWUsIC8qZW1pdCovIHRydWUpO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTnVtYmVyIHR5cGVkIC0+IHVwZGF0ZSByYW5nZSArIHdyaXRlciAoY2xhbXAgYnkgc2xpZGVyIGJvdW5kcykuXHJcblx0XHRcdGlmICh0Lm1hdGNoZXMgJiYgdC5tYXRjaGVzKHRoaXMub3B0cy52YWx1ZV9zZWxlY3RvcikpIHtcclxuXHRcdFx0XHRpZiAocGFydHMucmFuZ2UpIHtcclxuXHRcdFx0XHRcdHZhciB2ID0gcGFyc2VfZmxvYXQodC52YWx1ZSk7XHJcblx0XHRcdFx0XHRpZiAodiAhPSBudWxsKSB7XHJcblx0XHRcdFx0XHRcdHZhciBtaW4gPSBOdW1iZXIocGFydHMucmFuZ2UubWluKTtcclxuXHRcdFx0XHRcdFx0dmFyIG1heCA9IE51bWJlcihwYXJ0cy5yYW5nZS5tYXgpO1xyXG5cdFx0XHRcdFx0XHR2ID0gY2xhbXBfbnVtKHYsIGlzTmFOKG1pbikgPyBudWxsIDogbWluLCBpc05hTihtYXgpID8gbnVsbCA6IG1heCk7XHJcblxyXG5cdFx0XHRcdFx0XHRwYXJ0cy5yYW5nZS52YWx1ZSA9IFN0cmluZyh2KTtcclxuXHRcdFx0XHRcdFx0aWYgKFN0cmluZyh2KSAhPT0gdC52YWx1ZSkgdC52YWx1ZSA9IFN0cmluZyh2KTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0dGhpcy5fd3JpdGUocGFydHMsIHQudmFsdWUsIC8qZW1pdCovIHRydWUpO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0X29uX2NoYW5nZShldikge1xyXG5cdFx0XHQvLyBObyBzcGVjaWFsIFwiY2hhbmdlXCIgaGFuZGxpbmcgbmVlZGVkIGN1cnJlbnRseTsga2VwdCBmb3Igc3ltbWV0cnkvZnV0dXJlLlxyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEF1dG8taW5pdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRmdW5jdGlvbiB3cGJjX3NsaWRlcl9yYW5nZV9ncm91cHNfX2F1dG9faW5pdCgpIHtcclxuXHRcdHZhciBST09UICA9ICcud3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzJztcclxuXHRcdHZhciBub2RlcyA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGQucXVlcnlTZWxlY3RvckFsbChST09UKSlcclxuXHRcdFx0LmZpbHRlcihmdW5jdGlvbiAobikgeyByZXR1cm4gIW4ucGFyZW50RWxlbWVudCB8fCAhbi5wYXJlbnRFbGVtZW50LmNsb3Nlc3QoUk9PVCk7IH0pO1xyXG5cclxuXHRcdGlmICghbm9kZXMubGVuZ3RoKSB7XHJcblx0XHRcdGlmICghZC5fX3dwYmNfc2xpZGVyX3JhbmdlX2dyb3Vwc19nbG9iYWxfaW5zdGFuY2UpIHtcclxuXHRcdFx0XHRkLl9fd3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzX2dsb2JhbF9pbnN0YW5jZSA9IG5ldyBXUEJDX1NsaWRlcl9SYW5nZV9Hcm91cHMoZCkuaW5pdCgpO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHJcblx0XHRub2Rlcy5mb3JFYWNoKGZ1bmN0aW9uIChub2RlKSB7XHJcblx0XHRcdGlmIChub2RlLl9fd3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzX2luc3RhbmNlKSByZXR1cm47XHJcblx0XHRcdG5vZGUuX193cGJjX3NsaWRlcl9yYW5nZV9ncm91cHNfaW5zdGFuY2UgPSBuZXcgV1BCQ19TbGlkZXJfUmFuZ2VfR3JvdXBzKG5vZGUpLmluaXQoKTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHJcblx0Ly8gRXhwb3J0IGdsb2JhbHMuXHJcblx0dy5XUEJDX1NsaWRlcl9SYW5nZV9Hcm91cHMgICA9IFdQQkNfU2xpZGVyX1JhbmdlX0dyb3VwcztcclxuXHR3LldQQkNfU2xpZGVyX1JhbmdlX0F1dG9Jbml0ID0gd3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzX19hdXRvX2luaXQ7XHJcblxyXG5cdGlmIChkLnJlYWR5U3RhdGUgPT09ICdsb2FkaW5nJykge1xyXG5cdFx0ZC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgd3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzX19hdXRvX2luaXQsIHsgb25jZTogdHJ1ZSB9KTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0d3BiY19zbGlkZXJfcmFuZ2VfZ3JvdXBzX19hdXRvX2luaXQoKTtcclxuXHR9XHJcblxyXG59KSh3aW5kb3csIGRvY3VtZW50KTtcclxuIiwiLyoqXHJcbiAqIEJvb2tpbmcgQ2FsZW5kYXIg4oCUIEdlbmVyaWMgVUkgVGFicyBVdGlsaXR5IChKUylcclxuICpcclxuICogUHVycG9zZTogTGlnaHR3ZWlnaHQsIGRlcGVuZGVuY3ktZnJlZSB0YWJzIGNvbnRyb2xsZXIgZm9yIGFueSBzbWFsbCB0YWIgZ3JvdXAgaW4gYWRtaW4gVUlzLlxyXG4gKiAtIEF1dG8taW5pdGlhbGl6ZXMgZ3JvdXBzIG1hcmtlZCB3aXRoIGRhdGEtd3BiYy10YWJzLlxyXG4gKiAtIEFzc2lnbnMgQVJJQSByb2xlcyBhbmQgdG9nZ2xlcyBhcmlhLXNlbGVjdGVkL2FyaWEtaGlkZGVuL3RhYmluZGV4LlxyXG4gKiAtIFN1cHBvcnRzIGtleWJvYXJkIG5hdmlnYXRpb24gKExlZnQvUmlnaHQvSG9tZS9FbmQpLlxyXG4gKiAtIFB1YmxpYyBBUEk6IHdpbmRvdy53cGJjX3VpX3RhYnMue2luaXRfb24sIGluaXRfZ3JvdXAsIHNldF9hY3RpdmV9XHJcbiAqIC0gRW1pdHMgJ3dwYmM6dGFiczpjaGFuZ2UnIG9uIHRoZSBncm91cCByb290IHdoZW4gdGhlIGFjdGl2ZSB0YWIgY2hhbmdlcy5cclxuICpcclxuICogTWFya3VwIGNvbnRyYWN0OlxyXG4gKiAtIFJvb3Q6ICAgW2RhdGEtd3BiYy10YWJzXVxyXG4gKiAtIFRhYnM6ICAgW2RhdGEtd3BiYy10YWIta2V5PVwiS1wiXVxyXG4gKiAtIFBhbmVsczogW2RhdGEtd3BiYy10YWItcGFuZWw9XCJLXCJdXHJcbiAqXHJcbiAqIEBwYWNrYWdlICAgQm9va2luZyBDYWxlbmRhclxyXG4gKiBAc3VicGFja2FnZSBBZG1pblxcVUlcclxuICogQHNpbmNlICAgICAxMS4wLjBcclxuICogQHZlcnNpb24gICAxLjAuMFxyXG4gKiBAc2VlICAgICAgIC9pbmNsdWRlcy9fX2pzL2FkbWluL3VpX3RhYnMvdWlfdGFicy5qc1xyXG4gKlxyXG4gKlxyXG4gKiBIb3cgaXQgd29ya3M6XHJcbiAqIC0gUm9vdCBub2RlIG11c3QgaGF2ZSBbZGF0YS13cGJjLXRhYnNdIGF0dHJpYnV0ZSAoYW55IHZhbHVlKS5cclxuICogLSBUYWIgYnV0dG9ucyBtdXN0IGNhcnJ5IFtkYXRhLXdwYmMtdGFiLWtleT1cIi4uLlwiXSAodW5pcXVlIHBlciBncm91cCkuXHJcbiAqIC0gUGFuZWxzIG11c3QgY2FycnkgW2RhdGEtd3BiYy10YWItcGFuZWw9XCIuLi5cIl0gd2l0aCBtYXRjaGluZyBrZXlzLlxyXG4gKiAtIEFkZHMgV0FJLUFSSUEgcm9sZXMgYW5kIGFyaWEtc2VsZWN0ZWQvaGlkZGVuIHdpcmluZy5cclxuICpcclxuICogPGRpdiBkYXRhLXdwYmMtdGFicz1cImNvbHVtbi1zdHlsZXNcIiBkYXRhLXdwYmMtdGFiLWFjdGl2ZT1cIjFcIiAgICBjbGFzcz1cIndwYmNfdWlfdGFic19yb290XCIgPlxyXG4gKiAgICA8IS0tIFRvcCBUYWJzIC0tPlxyXG4gKiAgICA8ZGl2IGRhdGEtd3BiYy10YWJsaXN0PVwiXCIgcm9sZT1cInRhYmxpc3RcIiAgICAgICAgICAgICAgICAgICAgY2xhc3M9XCIgd3BiY191aV9lbF9faG9yaXNfdG9wX2Jhcl9fd3JhcHBlclwiID5cclxuICogICAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3VpX2VsX19ob3Jpc190b3BfYmFyX19jb250ZW50XCI+XHJcbiAqICAgICAgICAgICAgPGgyIGNsYXNzPVwid3BiY191aV9lbF9faG9yaXNfbmF2X2xhYmVsXCI+Q29sdW1uOjwvaDI+XHJcbiAqXHJcbiAqICAgICAgICAgICAgPGRpdiBjbGFzcz1cIndwYmNfdWlfZWxfX2hvcmlzX25hdl9pdGVtIHdwYmNfdWlfZWxfX2hvcmlzX25hdl9pdGVtX18xXCI+XHJcbiAqICAgICAgICAgICAgICAgIDxhXHJcbiAqICAgICAgICAgICAgICAgICAgICBkYXRhLXdwYmMtdGFiLWtleT1cIjFcIlxyXG4gKiAgICAgICAgICAgICAgICAgICAgYXJpYS1zZWxlY3RlZD1cInRydWVcIiByb2xlPVwidGFiXCIgdGFiaW5kZXg9XCIwXCIgYXJpYS1jb250cm9scz1cIndwYmNfdGFiX3BhbmVsX2NvbF8xXCJcclxuICpcclxuICogICAgICAgICAgICAgICAgICAgICAgICBocmVmPVwiamF2YXNjcmlwdDp2b2lkKDApO1wiXHJcbiAqICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3M9XCJ3cGJjX3VpX2VsX19ob3Jpc19uYXZfaXRlbV9fYSB3cGJjX3VpX2VsX19ob3Jpc19uYXZfaXRlbV9fc2luZ2xlXCJcclxuICogICAgICAgICAgICAgICAgICAgICAgICBpZD1cIndwYmNfdGFiX2NvbF8xXCJcclxuICogICAgICAgICAgICAgICAgICAgICAgICB0aXRsZT1cIkNvbHVtbiAxXCJcclxuICogICAgICAgICAgICAgICAgPjxzcGFuIGNsYXNzPVwid3BiY191aV9lbF9faG9yaXNfbmF2X3RpdGxlXCI+VGl0bGUgMTwvc3Bhbj48L2E+XHJcbiAqICAgICAgICAgICAgPC9kaXY+XHJcbiAqICAgICAgICAgICAgLi4uXHJcbiAqICAgICAgICA8L2Rpdj5cclxuICogICAgPC9kaXY+XHJcbiAqICAgIDwhLS0gVGFicyBDb250ZW50IC0tPlxyXG4gKiAgICA8ZGl2IGNsYXNzPVwid3BiY190YWJfX3BhbmVsIGdyb3VwX19maWVsZHNcIiBkYXRhLXdwYmMtdGFiLXBhbmVsPVwiMVwiIGlkPVwid3BiY190YWJfcGFuZWxfY29sXzFcIiByb2xlPVwidGFicGFuZWxcIiBhcmlhLWxhYmVsbGVkYnk9XCJ3cGJjX3RhYl9jb2xfMVwiPlxyXG4gKiAgICAgICAgLi4uXHJcbiAqICAgIDwvZGl2PlxyXG4gKiAgICAuLi5cclxuICogPC9kaXY+XHJcbiAqXHJcbiAqIFB1YmxpYyBBUEk6XHJcbiAqICAgLSB3cGJjX3VpX3RhYnMuaW5pdF9vbihyb290X29yX3NlbGVjdG9yKSAgIC8vIGZpbmQgYW5kIGluaXQgZ3JvdXBzIHdpdGhpbiBhIGNvbnRhaW5lclxyXG4gKiAgIC0gd3BiY191aV90YWJzLmluaXRfZ3JvdXAocm9vdF9lbCkgICAgICAgICAvLyBpbml0IGEgc2luZ2xlIGdyb3VwIHJvb3RcclxuICogICAtIHdwYmNfdWlfdGFicy5zZXRfYWN0aXZlKHJvb3RfZWwsIGtleSkgICAgLy8gcHJvZ3JhbW1hdGljYWxseSBjaGFuZ2UgYWN0aXZlIHRhYlxyXG4gKlxyXG4gKiBFdmVudHM6XHJcbiAqICAgLSBEaXNwYXRjaGVzIEN1c3RvbUV2ZW50ICd3cGJjOnRhYnM6Y2hhbmdlJyBvbiByb290IHdoZW4gdGFiIGNoYW5nZXM6XHJcbiAqICAgICAgIGRldGFpbDogeyBhY3RpdmVfa2V5OiAnMicsIHByZXZfa2V5OiAnMScgfVxyXG4gKlxyXG4gKiBTd2l0Y2ggYSBsb2NhbCAoZ2VuZXJpYykgdGFicyBncm91cCB0byB0YWIgMzogICAgIHZhciBncm91cCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ1tkYXRhLXdwYmMtdGFicz1cImNvbHVtbi1zdHlsZXNcIl0nKTsgaWYgKCBncm91cCApIHsgd3BiY191aV90YWJzLnNldF9hY3RpdmUoZ3JvdXAsICczJyk7IH1cclxuICovXHJcbihmdW5jdGlvbiAoIHcgKSB7XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHRpZiAoIHcud3BiY191aV90YWJzICkge1xyXG5cdFx0cmV0dXJuO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogSW50ZXJuYWw6IHRvZ2dsZSBhY3RpdmUgc3RhdGUuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSByb290X2VsXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9ICAgICAga2V5XHJcblx0ICogQHBhcmFtIHtib29sZWFufSAgICAgc2hvdWxkX2VtaXRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBzZXRfYWN0aXZlX2ludGVybmFsKCByb290X2VsLCBrZXksIHNob3VsZF9lbWl0ICkge1xyXG5cdFx0dmFyIHRhYl9idG5zID0gcm9vdF9lbC5xdWVyeVNlbGVjdG9yQWxsKCAnW2RhdGEtd3BiYy10YWIta2V5XScgKTtcclxuXHRcdHZhciBwYW5lbHMgICA9IHJvb3RfZWwucXVlcnlTZWxlY3RvckFsbCggJ1tkYXRhLXdwYmMtdGFiLXBhbmVsXScgKTtcclxuXHJcblx0XHR2YXIgcHJldl9rZXkgPSByb290X2VsLmdldEF0dHJpYnV0ZSggJ2RhdGEtd3BiYy10YWItYWN0aXZlJyApIHx8IG51bGw7XHJcblx0XHRpZiAoIFN0cmluZyggcHJldl9rZXkgKSA9PT0gU3RyaW5nKCBrZXkgKSApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIEJ1dHRvbnM6IGFyaWEgKyBjbGFzc1xyXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGFiX2J0bnMubGVuZ3RoOyBpKysgKSB7XHJcblx0XHRcdHZhciBidG4gICA9IHRhYl9idG5zW2ldO1xyXG5cdFx0XHR2YXIgYl9rZXkgPSBidG4uZ2V0QXR0cmlidXRlKCAnZGF0YS13cGJjLXRhYi1rZXknICk7XHJcblx0XHRcdHZhciBpc19vbiA9IFN0cmluZyggYl9rZXkgKSA9PT0gU3RyaW5nKCBrZXkgKTtcclxuXHJcblx0XHRcdGJ0bi5zZXRBdHRyaWJ1dGUoICdyb2xlJywgJ3RhYicgKTtcclxuXHRcdFx0YnRuLnNldEF0dHJpYnV0ZSggJ2FyaWEtc2VsZWN0ZWQnLCBpc19vbiA/ICd0cnVlJyA6ICdmYWxzZScgKTtcclxuXHRcdFx0YnRuLnNldEF0dHJpYnV0ZSggJ3RhYmluZGV4JywgaXNfb24gPyAnMCcgOiAnLTEnICk7XHJcblxyXG5cdFx0XHRpZiAoIGlzX29uICkge1xyXG5cdFx0XHRcdGJ0bi5jbGFzc0xpc3QuYWRkKCAnYWN0aXZlJyApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGJ0bi5jbGFzc0xpc3QucmVtb3ZlKCAnYWN0aXZlJyApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gUGFuZWxzOiBhcmlhICsgdmlzaWJpbGl0eVxyXG5cdFx0Zm9yICggdmFyIGogPSAwOyBqIDwgcGFuZWxzLmxlbmd0aDsgaisrICkge1xyXG5cdFx0XHR2YXIgcG4gICA9IHBhbmVsc1tqXTtcclxuXHRcdFx0dmFyIHBrZXkgPSBwbi5nZXRBdHRyaWJ1dGUoICdkYXRhLXdwYmMtdGFiLXBhbmVsJyApO1xyXG5cdFx0XHR2YXIgc2hvdyA9IFN0cmluZyggcGtleSApID09PSBTdHJpbmcoIGtleSApO1xyXG5cclxuXHRcdFx0cG4uc2V0QXR0cmlidXRlKCAncm9sZScsICd0YWJwYW5lbCcgKTtcclxuXHRcdFx0cG4uc2V0QXR0cmlidXRlKCAnYXJpYS1oaWRkZW4nLCBzaG93ID8gJ2ZhbHNlJyA6ICd0cnVlJyApO1xyXG5cdFx0XHRpZiAoIHNob3cgKSB7XHJcblx0XHRcdFx0cG4ucmVtb3ZlQXR0cmlidXRlKCAnaGlkZGVuJyApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHBuLnNldEF0dHJpYnV0ZSggJ2hpZGRlbicsICcnICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRyb290X2VsLnNldEF0dHJpYnV0ZSggJ2RhdGEtd3BiYy10YWItYWN0aXZlJywgU3RyaW5nKCBrZXkgKSApO1xyXG5cclxuXHRcdGlmICggc2hvdWxkX2VtaXQgKSB7XHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0dmFyIGV2ID0gbmV3IHcuQ3VzdG9tRXZlbnQoICd3cGJjOnRhYnM6Y2hhbmdlJywge1xyXG5cdFx0XHRcdFx0YnViYmxlcyA6IHRydWUsXHJcblx0XHRcdFx0XHRkZXRhaWwgIDogeyBhY3RpdmVfa2V5IDogU3RyaW5nKCBrZXkgKSwgcHJldl9rZXkgOiBwcmV2X2tleSB9XHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdHJvb3RfZWwuZGlzcGF0Y2hFdmVudCggZXYgKTtcclxuXHRcdFx0fSBjYXRjaCAoIF9lICkge31cclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIEludGVybmFsOiBnZXQgb3JkZXJlZCBrZXlzIGZyb20gYnV0dG9ucy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvb3RfZWxcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nW119XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gZ2V0X2tleXMoIHJvb3RfZWwgKSB7XHJcblx0XHR2YXIgbGlzdCA9IFtdO1xyXG5cdFx0dmFyIGJ0bnMgPSByb290X2VsLnF1ZXJ5U2VsZWN0b3JBbGwoICdbZGF0YS13cGJjLXRhYi1rZXldJyApO1xyXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgYnRucy5sZW5ndGg7IGkrKyApIHtcclxuXHRcdFx0dmFyIGsgPSBidG5zW2ldLmdldEF0dHJpYnV0ZSggJ2RhdGEtd3BiYy10YWIta2V5JyApO1xyXG5cdFx0XHRpZiAoIGsgIT0gbnVsbCAmJiBrICE9PSAnJyApIHtcclxuXHRcdFx0XHRsaXN0LnB1c2goIFN0cmluZyggayApICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHRcdHJldHVybiBsaXN0O1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogSW50ZXJuYWw6IG1vdmUgZm9jdXMgYmV0d2VlbiB0YWJzIHVzaW5nIGtleWJvYXJkLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm9vdF9lbFxyXG5cdCAqIEBwYXJhbSB7bnVtYmVyfSAgICAgIGRpciAgKzEgKG5leHQpIC8gLTEgKHByZXYpXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gZm9jdXNfcmVsYXRpdmUoIHJvb3RfZWwsIGRpciApIHtcclxuXHRcdHZhciBrZXlzICAgID0gZ2V0X2tleXMoIHJvb3RfZWwgKTtcclxuXHRcdHZhciBjdXJyZW50ID0gcm9vdF9lbC5nZXRBdHRyaWJ1dGUoICdkYXRhLXdwYmMtdGFiLWFjdGl2ZScgKSB8fCBrZXlzWzBdIHx8IG51bGw7XHJcblx0XHR2YXIgaWR4ICAgICA9IE1hdGgubWF4KCAwLCBrZXlzLmluZGV4T2YoIFN0cmluZyggY3VycmVudCApICkgKTtcclxuXHRcdHZhciBuZXh0ICAgID0ga2V5c1sgKCBpZHggKyAoIGRpciA+IDAgPyAxIDoga2V5cy5sZW5ndGggLSAxICkgKSAlIGtleXMubGVuZ3RoIF07XHJcblxyXG5cdFx0dmFyIG5leHRfYnRuID0gcm9vdF9lbC5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtd3BiYy10YWIta2V5PVwiJyArIG5leHQgKyAnXCJdJyApO1xyXG5cdFx0aWYgKCBuZXh0X2J0biApIHtcclxuXHRcdFx0bmV4dF9idG4uZm9jdXMoKTtcclxuXHRcdFx0c2V0X2FjdGl2ZV9pbnRlcm5hbCggcm9vdF9lbCwgbmV4dCwgdHJ1ZSApO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogSW5pdGlhbGl6ZSBhIHNpbmdsZSB0YWJzIGdyb3VwIHJvb3QuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSByb290X2VsXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gaW5pdF9ncm91cCggcm9vdF9lbCApIHtcclxuXHRcdGlmICggISByb290X2VsIHx8IHJvb3RfZWwuX193cGJjX3RhYnNfaW5pdGVkICkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHRyb290X2VsLl9fd3BiY190YWJzX2luaXRlZCA9IHRydWU7XHJcblxyXG5cdFx0Ly8gUm9sZXNcclxuXHRcdHZhciB0YWJsaXN0ID0gcm9vdF9lbC5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtd3BiYy10YWJsaXN0XScgKSB8fCByb290X2VsO1xyXG5cdFx0dGFibGlzdC5zZXRBdHRyaWJ1dGUoICdyb2xlJywgJ3RhYmxpc3QnICk7XHJcblxyXG5cdFx0Ly8gRGVmYXVsdCBhY3RpdmU6IGZyb20gYXR0cmlidXRlIG9yIGZpcnN0IGJ1dHRvblxyXG5cdFx0dmFyIGtleXMgPSBnZXRfa2V5cyggcm9vdF9lbCApO1xyXG5cdFx0dmFyIGRlZiAgPSByb290X2VsLmdldEF0dHJpYnV0ZSggJ2RhdGEtd3BiYy10YWItYWN0aXZlJyApIHx8ICgga2V5c1swXSB8fCAnMScgKTtcclxuXHRcdHNldF9hY3RpdmVfaW50ZXJuYWwoIHJvb3RfZWwsIGRlZiwgZmFsc2UgKTtcclxuXHJcblx0XHQvLyBDbGlja3NcclxuXHRcdHJvb3RfZWwuYWRkRXZlbnRMaXN0ZW5lciggJ2NsaWNrJywgZnVuY3Rpb24gKCBlICkge1xyXG5cdFx0XHR2YXIgYnRuID0gZS50YXJnZXQuY2xvc2VzdCA/IGUudGFyZ2V0LmNsb3Nlc3QoICdbZGF0YS13cGJjLXRhYi1rZXldJyApIDogbnVsbDtcclxuXHRcdFx0aWYgKCAhIGJ0biB8fCAhIHJvb3RfZWwuY29udGFpbnMoIGJ0biApICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdHZhciBrZXkgPSBidG4uZ2V0QXR0cmlidXRlKCAnZGF0YS13cGJjLXRhYi1rZXknICk7XHJcblx0XHRcdGlmICgga2V5ICE9IG51bGwgKSB7XHJcblx0XHRcdFx0c2V0X2FjdGl2ZV9pbnRlcm5hbCggcm9vdF9lbCwga2V5LCB0cnVlICk7XHJcblx0XHRcdH1cclxuXHRcdH0sIHRydWUgKTtcclxuXHJcblx0XHQvLyBLZXlib2FyZCAoTGVmdC9SaWdodC9Ib21lL0VuZClcclxuXHRcdHJvb3RfZWwuYWRkRXZlbnRMaXN0ZW5lciggJ2tleWRvd24nLCBmdW5jdGlvbiAoIGUgKSB7XHJcblx0XHRcdHZhciB0Z3QgPSBlLnRhcmdldDtcclxuXHRcdFx0aWYgKCAhIHRndCB8fCAhIHRndC5oYXNBdHRyaWJ1dGUgfHwgISB0Z3QuaGFzQXR0cmlidXRlKCAnZGF0YS13cGJjLXRhYi1rZXknICkgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdHN3aXRjaCAoIGUua2V5ICkge1xyXG5cdFx0XHRjYXNlICdBcnJvd0xlZnQnOlxyXG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTsgZm9jdXNfcmVsYXRpdmUoIHJvb3RfZWwsIC0xICk7IGJyZWFrO1xyXG5cdFx0XHRjYXNlICdBcnJvd1JpZ2h0JzpcclxuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7IGZvY3VzX3JlbGF0aXZlKCByb290X2VsLCArMSApOyBicmVhaztcclxuXHRcdFx0Y2FzZSAnSG9tZSc6XHJcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpOyBzZXRfYWN0aXZlX2ludGVybmFsKCByb290X2VsLCAoIGdldF9rZXlzKCByb290X2VsIClbMF0gfHwgJzEnICksIHRydWUgKTsgYnJlYWs7XHJcblx0XHRcdGNhc2UgJ0VuZCc6XHJcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpOyB2YXIga3MgPSBnZXRfa2V5cyggcm9vdF9lbCApOyBzZXRfYWN0aXZlX2ludGVybmFsKCByb290X2VsLCAoIGtzWyBrcy5sZW5ndGggLSAxIF0gfHwgJzEnICksIHRydWUgKTsgYnJlYWs7XHJcblx0XHRcdH1cclxuXHRcdH0sIHRydWUgKTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIEluaXRpYWxpemUgYWxsIGdyb3VwcyB3aXRoaW4gYSBjb250YWluZXIgKG9yIGRvY3VtZW50KS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR8c3RyaW5nfG51bGx9IGNvbnRhaW5lclxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIGluaXRfb24oIGNvbnRhaW5lciApIHtcclxuXHRcdHZhciBjdHggPSBjb250YWluZXIgPyAoIHR5cGVvZiBjb250YWluZXIgPT09ICdzdHJpbmcnID8gZG9jdW1lbnQucXVlcnlTZWxlY3RvciggY29udGFpbmVyICkgOiBjb250YWluZXIgKSA6IGRvY3VtZW50O1xyXG5cdFx0aWYgKCAhIGN0eCApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cdFx0dmFyIGdyb3VwcyA9IGN0eC5xdWVyeVNlbGVjdG9yQWxsKCAnW2RhdGEtd3BiYy10YWJzXScgKTtcclxuXHRcdGZvciAoIHZhciBpID0gMDsgaSA8IGdyb3Vwcy5sZW5ndGg7IGkrKyApIHtcclxuXHRcdFx0aW5pdF9ncm91cCggZ3JvdXBzW2ldICk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBQcm9ncmFtbWF0aWNhbGx5IHNldCBhY3RpdmUgdGFiIGJ5IGtleS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvb3RfZWxcclxuXHQgKiBAcGFyYW0ge3N0cmluZ3xudW1iZXJ9IGtleVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHNldF9hY3RpdmUoIHJvb3RfZWwsIGtleSApIHtcclxuXHRcdGlmICggcm9vdF9lbCAmJiByb290X2VsLmhhc0F0dHJpYnV0ZSAmJiByb290X2VsLmhhc0F0dHJpYnV0ZSggJ2RhdGEtd3BiYy10YWJzJyApICkge1xyXG5cdFx0XHRzZXRfYWN0aXZlX2ludGVybmFsKCByb290X2VsLCBTdHJpbmcoIGtleSApLCB0cnVlICk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyBQdWJsaWMgQVBJIChzbmFrZV9jYXNlKVxyXG5cdHcud3BiY191aV90YWJzID0ge1xyXG5cdFx0aW5pdF9vbiAgICA6IGluaXRfb24sXHJcblx0XHRpbml0X2dyb3VwIDogaW5pdF9ncm91cCxcclxuXHRcdHNldF9hY3RpdmUgOiBzZXRfYWN0aXZlXHJcblx0fTtcclxuXHJcblx0Ly8gQXV0by1pbml0IG9uIERPTSByZWFkeVxyXG5cdGlmICggZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gJ2xvYWRpbmcnICkge1xyXG5cdFx0ZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7IGluaXRfb24oIGRvY3VtZW50ICk7IH0gKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0aW5pdF9vbiggZG9jdW1lbnQgKTtcclxuXHR9XHJcblxyXG59KSggd2luZG93ICk7XHJcbiJdfQ==
