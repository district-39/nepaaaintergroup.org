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