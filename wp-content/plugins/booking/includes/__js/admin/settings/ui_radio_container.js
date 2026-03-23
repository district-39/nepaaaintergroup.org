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