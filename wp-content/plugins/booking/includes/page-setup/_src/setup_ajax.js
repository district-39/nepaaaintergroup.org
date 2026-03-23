"use strict";
// =====================================================================================================================
// == Ajax ==
// =====================================================================================================================

function wpbc_ajx__setup_wizard_page__send_request(){

console.groupCollapsed( 'WPBC_AJX_SETUP_WIZARD_PAGE' ); console.log( ' == Before Ajax Send - search_get_all_params() == ' , _wpbc_settings.get_all_params__setup_wizard() );

	// It can start 'icon spinning' on top menu bar at 'active menu item'.
	wpbc_setup_wizard_page_reload_button__spin_start();

	// Clear some parameters, which can make issue with blocking requests.
	wpbc_ajx__setup_wizard_page__do_request_clean();

	// Start Ajax
	jQuery.post( wpbc_url_ajax,
			{
				action          : 'WPBC_AJX_SETUP_WIZARD_PAGE',
				wpbc_ajx_user_id: _wpbc_settings.get_param__secure( 'user_id' ),
				nonce           : _wpbc_settings.get_param__secure( 'nonce' ),
				wpbc_ajx_locale : _wpbc_settings.get_param__secure( 'locale' ),

				all_ajx_params  : _wpbc_settings.get_all_params__setup_wizard()
			},
			/**
			 * S u c c e s s
			 *
			 * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
			 * @param textStatus		-	'success'
			 * @param jqXHR				-	Object
			 */
			function ( response_data, textStatus, jqXHR ) {

console.log( ' == Response WPBC_AJX_SETUP_WIZARD_PAGE == ', response_data ); console.groupEnd();

				// -------------------------------------------------------------------------------------------------
				// Probably Error
				// -------------------------------------------------------------------------------------------------
				if ( (typeof response_data !== 'object') || (response_data === null) ){

					wpbc_setup_wizard_page__hide_content();
					wpbc_setup_wizard_page__show_message( response_data );

					return;
				}

				// -------------------------------------------------------------------------------------------------
				// Reset Done - Reload page, after filter toolbar has been reset
				// -------------------------------------------------------------------------------------------------
				if (  ( undefined != response_data[ 'ajx_cleaned_params' ] ) && ( 'reset_done' === response_data[ 'ajx_cleaned_params' ][ 'do_action' ] )  ){
					location.reload();
					return;
				}

				// Define Front-End side JS vars from  Ajax
				_wpbc_settings.set_params_arr__setup_wizard( response_data[ 'ajx_data' ] );

				// Update Menu statuses: Top Black UI and in Left Main menu
				wpbc_setup_wizard_page__update_steps_status( response_data[ 'ajx_data' ]['steps_is_done'] );

				if ( wpbc_setup_wizard_page__is_all_steps_completed() ) {
					if (undefined != response_data[ 'ajx_data' ][ 'redirect_url' ]){
						window.location.href = response_data[ 'ajx_data' ][ 'redirect_url' ];
						return;
					}
				}


				// -> Progress line at  "Left Main Menu"
				wpbc_setup_wizard_page__update_plugin_menu_progress( response_data[ 'ajx_data' ]['plugin_menu__setup_progress'] );

				// -------------------------------------------------------------------------------------------------
				// Show Main Content
				// -------------------------------------------------------------------------------------------------
				wpbc_setup_wizard_page__show_content();

				// -------------------------------------------------------------------------------------------------
				// Redefine Hooks, because we show new DOM elements
				// -------------------------------------------------------------------------------------------------
				wpbc_setup_wizard_page__define_ui_hooks();

				// Show Messages
				if ( '' !== response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" ) ){
					wpbc_admin_show_message(
												  response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" )
												, ( '1' == response_data[ 'ajx_data' ][ 'ajx_after_action_result' ] ) ? 'success' : 'error'
												, 10000
											);
				}

				// It can STOP 'icon spinning' on top menu bar at 'active menu item'
				wpbc_setup_wizard_page_reload_button__spin_pause();

				// Remove spin from "button with icon", that was clicked and Enable this button.
				wpbc_button__remove_spin( response_data[ 'ajx_cleaned_params' ][ 'ui_clicked_element_id' ] )

				jQuery( '#ajax_respond' ).html( response_data );		// For ability to show response, add such DIV element to page
			}
		  ).fail( function ( jqXHR, textStatus, errorThrown ) {    if ( window.console && window.console.log ){ console.log( 'Ajax_Error', jqXHR, textStatus, errorThrown ); }

				var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown ;
				if ( jqXHR.status ){
					error_message += ' (<b>' + jqXHR.status + '</b>)';
					if (403 == jqXHR.status ){
						error_message += ' Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
					}
				}
				if ( jqXHR.responseText ){
					error_message += ' ' + jqXHR.responseText;
				}
				error_message = error_message.replace( /\n/g, "<br />" );

				// Hide Content
				wpbc_setup_wizard_page__hide_content();

				// Show Error Message
				wpbc_setup_wizard_page__show_message( error_message );
		  })
		  // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
		  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
		  ;  // End Ajax

}


/**
 * Clean some parameters,  does not required for request
 */
function wpbc_ajx__setup_wizard_page__do_request_clean() {
	// We donot require the 'calendar_force_load' parameter  with  all html and scripts  content at  server side. This content generated at server side.
	// It is also can be the reason of blocking request, because of script tags.
	_wpbc_settings.set_param__setup_wizard('calendar_force_load', '');
}