/**
 * Booking Actions in Booking Listing.
 *
 * @version     1.0
 * @package     Booking Calendar
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com
 * @modified    2025-04-08
 */

/**
 * Check if we can open modal.
 *
 * @param html_id      ID of modal window, e.g.: '#wpbc_modal__payment_status_edit__section'
 *
 * @returns {boolean}
 */
function wpbc_is_modal_accessible( html_id ) {
	if ( 'function' !== typeof (jQuery( html_id ).wpbc_my_modal) ) {
		alert( 'Warning! wpbc_my_modal module has not found. Please, recheck about any conflicts by deactivating other plugins.' );
		return false;
	}
	return true;
}


// ---------------------------------------------------------------------------------------------------------------------
// == Actions, while cliking on option dropdown ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Change payment Cost.
 *
 * @param booking_id			- ID of booking.
 * @param cost	                - payment cost.
 */
function wpbc_boo_listing__click__set_booking_cost( booking_id, cost ) {

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__booking_cost_edit__section' ) ) {
		return false;
	}

	// Set booking cost.
	jQuery( '#wpbc_modal__booking_cost_edit__value' ).val( cost );

	// Set booking ID.
	jQuery( '#wpbc_modal__booking_cost_edit__booking_id' ).val( booking_id );

	// ID title.
	jQuery( '.wpbc_modal__booking_cost_edit__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__booking_cost_edit__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__booking_cost_edit__value' ).trigger( 'focus' );
}

/**
 * Change payment Status.
 *
 * @param booking_id			- ID of booking.
 * @param selected_pay_status	- payment status.
 */
function wpbc_boo_listing__click__set_payment_status( booking_id, selected_pay_status ) {

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__payment_status_edit__section' ) ) {
		return false;
	}

	var jSelect = jQuery( '#wpbc_modal__payment_status_edit__value' );

	// Select Status.
	if ( ( ! isNaN( parseFloat( selected_pay_status ) )) || ('' === selected_pay_status) ) {		// Is it float - then  it's unknown.
		jSelect.find( 'option[value="1"]' ).prop( 'selected', true );								// Unknown  value is '1' in select box.
	} else {
		jSelect.find( 'option[value="' + selected_pay_status + '"]' ).prop( 'selected', true );		// Otherwise known payment status.
	}
	// Set booking ID.
	jQuery( '#wpbc_modal__payment_status_edit__booking_id' ).val( booking_id );

	// ID title.
	jQuery( '.wpbc_modal__payment_status_edit__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__payment_status_edit__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__payment_status_edit__value' ).trigger( 'focus' );
}

/**
 * Send payment request
 *
 * @param booking_id
 * @param visitorbookingpayurl
 * @param cost
 * @returns {boolean}
 */
function wpbc_boo_listing__click__send_payment_request( booking_id, visitorbookingpayurl, cost ){

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__send_payment_request__section' ) ) {
		return false;
	}

	// Set booking cost.
	jQuery( '#wpbc_modal__send_payment_request__url' ).val( visitorbookingpayurl );

	// Set booking ID.
	jQuery( '#wpbc_modal__send_payment_request__booking_id' ).val( booking_id );

	// ID title.
	jQuery( '.wpbc_modal__send_payment_request__booking_id' ).html( 'ID: ' + booking_id );

	// Cost.
	jQuery( '.wpbc_modal__send_payment_request__cost' ).html( cost );

	// Show Modal.
	jQuery( '#wpbc_modal__send_payment_request__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__send_payment_request__value' ).trigger( 'focus' );

}

/**
 * Save Notes
 *
 * @param booking_id
 * @param note_text
 * @returns {boolean}
 */
function wpbc_boo_listing__click__set_booking_note( booking_id, note_text ){

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__set_booking_note__section' ) ) {
		return false;
	}

	// Set Note.
	jQuery( '#wpbc_modal__set_booking_note__value' ).val( note_text );

	// Set booking ID.
	jQuery( '#wpbc_modal__set_booking_note__booking_id' ).val( booking_id );

	// ID title.
	jQuery( '.wpbc_modal__set_booking_note__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__set_booking_note__section' ).wpbc_my_modal( 'show' );

	// Set focus to input. // jQuery( '#wpbc_modal__set_booking_note__value' ).trigger( 'focus' ); .
	jQuery( '#wpbc_modal__set_booking_note__value' ).scrollTop( 0 );

}

/**
 * Change Resource for Booking
 *
 * @param booking_id			- ID of booking.
 * @param resource_id           - ID of booking resource.
 */
function wpbc_boo_listing__click__change_booking_resource( booking_id, resource_id ){

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__change_booking_resource__section' ) ) {
		return false;
	}

	// Select booking resource  that belong to  booking.
	jQuery( '#wpbc_modal__change_booking_resource__resource_id' ).val( resource_id ).trigger( 'change' );

	// Set booking ID.
	jQuery( '#wpbc_modal__change_booking_resource__booking_id' ).val( booking_id );
	// ID title.
	jQuery( '.wpbc_modal__change_booking_resource__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__change_booking_resource__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__change_booking_resource__resource_id' ).focus();
}

/**
 * Duplicate Booking into another resource.
 *
 * @param booking_id			- ID of booking.
 * @param resource_id           - ID of booking resource.
 */
function wpbc_boo_listing__click__duplicate_booking_to_other_resource( booking_id, resource_id ){

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__duplicate_booking_to_other_resource__section' ) ) {
		return false;
	}

	// Select booking resource  that belong to  booking.
	jQuery( '#wpbc_modal__duplicate_booking_to_other_resource__resource_id' ).val( resource_id ).trigger( 'change' );

	// Set booking ID.
	jQuery( '#wpbc_modal__duplicate_booking_to_other_resource__booking_id' ).val( booking_id );
	// ID title.
	jQuery( '.wpbc_modal__duplicate_booking_to_other_resource__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__duplicate_booking_to_other_resource__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__duplicate_booking_to_other_resource__resource_id' ).focus();
}

/**
 * Change Locale of Booking.
 *
 * @param booking_id			- ID of booking.
 * @param resource_id           - ID of booking resource.
 */
function wpbc_boo_listing__click__set_booking_locale( booking_id, selected_locale_value ){

	if ( ! wpbc_is_modal_accessible( '#wpbc_modal__set_booking_locale__section' ) ) {
		return false;
	}

	// Select booking Locale  that belong to  booking.
	jQuery( '#wpbc_modal__set_booking_locale' ).val( selected_locale_value ).trigger( 'change' );

	// var jSelect = jQuery( '#set_booking_locale__resource_select' );
	// jSelect.find( 'option[value="' + resource_id + '"]' ).prop( 'selected', true );		// Otherwise known payment status.

	// Set booking ID.
	jQuery( '#wpbc_modal__set_booking_locale__booking_id' ).val( booking_id );
	// ID title.
	jQuery( '.wpbc_modal__set_booking_locale__booking_id' ).html( 'ID: ' + booking_id );

	// Show Modal.
	jQuery( '#wpbc_modal__set_booking_locale__section' ).wpbc_my_modal( 'show' );

	// Set focus to input.
	jQuery( '#wpbc_modal__set_booking_locale' ).focus();
}


// ---------------------------------------------------------------------------------------------------------------------
// == Filter Toolbar ==
// ---------------------------------------------------------------------------------------------------------------------
/**
 * == "Sort By" Button ==
 * This function update Title in Dropdown menu.
 * It executed, after receving Ajax response.
 * And based on parameters of this response, we get option title from dropdown list options and show it in toggle title.
 */
function wpbc_boo_listing__init_hook__sort_by() {

	var el_id = 'wh_sort';

	var parameter_value = wpbc_ajx_booking_listing.search_get_param( el_id );

	var j_option_link = jQuery( '.ul_dropdown_menu_li__' + el_id + '__' + parameter_value );
	if ( j_option_link.length ) {
		jQuery( '.ul_dropdown_menu__' + el_id + ' .ul_dropdown_menu_toggle .selected_value' ).html( j_option_link.html() );
	} else {
		jQuery( '.ul_dropdown_menu__' + el_id + ' .ul_dropdown_menu_toggle .selected_value' ).html( '---' );
	}
}

// ---------------------------------------------------------------------------------------------------------------------
// == Listing Header Table ==
// ---------------------------------------------------------------------------------------------------------------------
/**
 * == "Expand All Rows" Button ==
 */
function wpbc_boo_listing__click__expand_all_rows() {
	jQuery( '.wpbc_row_wrap' ).removeClass( 'max_height_a' );
	jQuery( '.wpbc_row_wrap .wpbc_icn_expand_less' ).show();
	jQuery( '.wpbc_row_wrap .wpbc_icn_expand_more' ).hide();
	jQuery( '.wpbc_btn_expand_colapse_all' ).toggle();
}


/**
 * == "Colpase All Rows" Button ==
 */
function wpbc_boo_listing__click__colapse_all_rows() {
	jQuery( '.wpbc_row_wrap' ).addClass( 'max_height_a' );
	jQuery( '.wpbc_row_wrap .wpbc_icn_expand_less' ).hide();
	jQuery( '.wpbc_row_wrap .wpbc_icn_expand_more' ).show();
	jQuery( '.wpbc_btn_expand_colapse_all' ).toggle();
}