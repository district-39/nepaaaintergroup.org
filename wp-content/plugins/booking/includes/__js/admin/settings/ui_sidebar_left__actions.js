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