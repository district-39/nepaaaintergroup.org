"use strict";

/**
 *   Ajax   ----------------------------------------------------------------------------------------------------- */
//var is_this_action = false;
/**
 * Send Ajax action request,  like approving or cancellation
 *
 * @param action_param
 */
function wpbc_ajx_booking_ajax_action_request(action_param = {}) {
  console.groupCollapsed('WPBC_AJX_BOOKING_ACTIONS');
  console.log(' == Ajax Actions :: Params == ', action_param);
  //is_this_action = true;

  wpbc_admin_show_message_processing('');
  wpbc_booking_listing_reload_button__spin_start();

  // Get redefined Locale,  if action on single booking !
  if (undefined != action_param['booking_id'] && !Array.isArray(action_param['booking_id'])) {
    // Not array

    action_param['locale'] = wpbc_get_selected_locale(action_param['booking_id'], wpbc_ajx_booking_listing.get_secure_param('locale'));
  }
  var action_post_params = {
    action: 'WPBC_AJX_BOOKING_ACTIONS',
    nonce: wpbc_ajx_booking_listing.get_secure_param('nonce'),
    wpbc_ajx_user_id: undefined == action_param['user_id'] ? wpbc_ajx_booking_listing.get_secure_param('user_id') : action_param['user_id'],
    wpbc_ajx_locale: undefined == action_param['locale'] ? wpbc_ajx_booking_listing.get_secure_param('locale') : action_param['locale'],
    action_params: action_param
  };

  // It's required for CSV export - getting the same list  of bookings
  if (typeof action_param.search_params !== 'undefined') {
    action_post_params['search_params'] = action_param.search_params;
    delete action_post_params.action_params.search_params;
  }

  // Start Ajax
  jQuery.post(wpbc_url_ajax, action_post_params,
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    console.log(' == Ajax Actions :: Response WPBC_AJX_BOOKING_ACTIONS == ', response_data);
    console.groupEnd();

    // Probably Error
    if (typeof response_data !== 'object' || response_data === null) {
      jQuery('.wpbc_ajx_under_toolbar_row').hide(); // FixIn: 9.6.1.5.
      jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html('<div class="wpbc-settings-notice notice-warning" style="text-align:left">' + response_data + '</div>');
      return;
    }
    wpbc_booking_listing_reload_button__spin_pause();
    wpbc_admin_show_message(response_data['ajx_after_action_message'].replace(/\n/g, "<br />"), '1' == response_data['ajx_after_action_result'] ? 'success' : 'error', 'undefined' === typeof response_data['ajx_after_action_result_all_params_arr']['after_action_result_delay'] ? 10000 : response_data['ajx_after_action_result_all_params_arr']['after_action_result_delay']);

    // Success response
    if ('1' == response_data['ajx_after_action_result']) {
      var is_reload_ajax_listing = true;

      // After Google Calendar import show imported bookings and reload the page for toolbar parameters update
      if (false !== response_data['ajx_after_action_result_all_params_arr']['new_listing_params']) {
        wpbc_ajx_booking_send_search_request_with_params(response_data['ajx_after_action_result_all_params_arr']['new_listing_params']);
        var closed_timer = setTimeout(function () {
          if (wpbc_booking_listing_reload_button__is_spin()) {
            if (undefined != response_data['ajx_after_action_result_all_params_arr']['new_listing_params']['reload_url_params']) {
              document.location.href = response_data['ajx_after_action_result_all_params_arr']['new_listing_params']['reload_url_params'];
            } else {
              document.location.reload();
            }
          }
        }, 2000);
        is_reload_ajax_listing = false;
      }

      // Start download exported CSV file
      if (undefined != response_data['ajx_after_action_result_all_params_arr']['export_csv_url']) {
        wpbc_ajx_booking__export_csv_url__download(response_data['ajx_after_action_result_all_params_arr']['export_csv_url']);
        is_reload_ajax_listing = false;
      }
      if (is_reload_ajax_listing) {
        wpbc_ajx_booking__actual_listing__show(); //	Sending Ajax Request	-	with parameters that  we early  defined in "wpbc_ajx_booking_listing" Obj.
      }
    }

    // Remove spin icon from  button and Enable this button.
    wpbc_button__remove_spin(response_data['ajx_cleaned_params']['ui_clicked_element_id']);

    // Hide modals
    wpbc_popup_modals__hide();
    jQuery('#ajax_respond').html(response_data); // For ability to show response, add such DIV element to page
  }).fail(function (jqXHR, textStatus, errorThrown) {
    if (window.console && window.console.log) {
      console.log('Ajax_Error', jqXHR, textStatus, errorThrown);
    }
    jQuery('.wpbc_ajx_under_toolbar_row').hide(); // FixIn: 9.6.1.5.
    var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown;
    if (jqXHR.responseText) {
      error_message += jqXHR.responseText;
    }
    error_message = error_message.replace(/\n/g, "<br />");
    wpbc_ajx_booking_show_message(error_message);
  })
  // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
  ; // End Ajax
}

/**
 * Hide all open modal popups windows
 */
function wpbc_popup_modals__hide() {
  // Hide modals
  if ('function' === typeof jQuery('.wpbc_popup_modal').wpbc_my_modal) {
    jQuery('.wpbc_popup_modal').wpbc_my_modal('hide');
  }
}

/**
 *   Dates  Short <-> Wide    ----------------------------------------------------------------------------------- */

function wpbc_ajx_click_on_dates_short() {
  jQuery('#booking_dates_small,.booking_dates_full').hide();
  jQuery('#booking_dates_full,.booking_dates_small').show();
  wpbc_ajx_booking_send_search_request_with_params({
    'ui_usr__dates_short_wide': 'short'
  });
}
function wpbc_ajx_click_on_dates_wide() {
  jQuery('#booking_dates_full,.booking_dates_small').hide();
  jQuery('#booking_dates_small,.booking_dates_full').show();
  wpbc_ajx_booking_send_search_request_with_params({
    'ui_usr__dates_short_wide': 'wide'
  });
}
function wpbc_ajx_click_on_dates_toggle(this_date) {
  jQuery(this_date).parents('.wpbc_col_dates').find('.booking_dates_small').toggle();
  jQuery(this_date).parents('.wpbc_col_dates').find('.booking_dates_full').toggle();

  /*
  var visible_section = jQuery( this_date ).parents( '.booking_dates_expand_section' );
  visible_section.hide();
  if ( visible_section.hasClass( 'booking_dates_full' ) ){
  	visible_section.parents( '.wpbc_col_dates' ).find( '.booking_dates_small' ).show();
  } else {
  	visible_section.parents( '.wpbc_col_dates' ).find( '.booking_dates_full' ).show();
  }*/
  console.log('wpbc_ajx_click_on_dates_toggle', this_date);
}

/**
 *   Locale   --------------------------------------------------------------------------------------------------- */

/**
 * 	Select options in select boxes based on attribute "value_of_selected_option" and RED color and hint for LOCALE button   --  It's called from 	wpbc_ajx_booking_define_ui_hooks()  	each  time after Listing loading.
 */
function wpbc_ajx_booking__ui_define__locale() {
  jQuery('.wpbc__list__table select').each(function (index) {
    var selection = jQuery(this).attr("value_of_selected_option"); // Define selected select boxes

    if (undefined !== selection) {
      jQuery(this).find('option[value="' + selection + '"]').prop('selected', true);
      if ('' != selection && jQuery(this).hasClass('set_booking_locale_selectbox')) {
        // Locale

        var booking_locale_button = jQuery(this).parents('.ui_element_locale').find('.set_booking_locale_button');

        //booking_locale_button.css( 'color', '#db4800' );		// Set button  red
        booking_locale_button.addClass('wpbc_ui_red'); // Set button  red
        if ('function' === typeof wpbc_tippy) {
          booking_locale_button.get(0)._tippy.setContent(selection);
        }
      }
    }
  });
}

/**
 *   Remark   --------------------------------------------------------------------------------------------------- */

/**
 * Define content of remark "booking note" button and textarea.  -- It's called from 	wpbc_ajx_booking_define_ui_hooks()  	each  time after Listing loading.
 */
function wpbc_ajx_booking__ui_define__remark() {
  jQuery('.wpbc__list__table .ui_remark_section textarea').each(function (index) {
    var text_val = jQuery(this).val();
    if (undefined !== text_val && '' != text_val) {
      var remark_button = jQuery(this).parents('.ui_group').find('.set_booking_note_button');
      if (remark_button.length > 0) {
        remark_button.addClass('wpbc_ui_red'); // Set button  red
        if ('function' === typeof wpbc_tippy) {
          //remark_button.get( 0 )._tippy.allowHTML = true;
          //remark_button.get( 0 )._tippy.setContent( text_val.replace(/[\n\r]/g, '<br>') );

          remark_button.get(0)._tippy.setProps({
            allowHTML: true,
            content: text_val.replace(/[\n\r]/g, '<br>')
          });
        }
      }
    }
  });
}
function wpbc_ajx_booking__ui_click_save__change_resource(this_el, booking_action, el_id) {
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': booking_action,
    'booking_id': jQuery('#change_booking_resource__booking_id').val(),
    'selected_resource_id': jQuery('#change_booking_resource__resource_select').val(),
    'ui_clicked_element_id': el_id
  });
  wpbc_button_enable_loading_icon(this_el);
}
function wpbc_ajx_booking__ui_click_save__duplicate_booking(this_el, booking_action, el_id) {
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': booking_action,
    'booking_id': jQuery('#duplicate_booking_to_other_resource__booking_id').val(),
    'selected_resource_id': jQuery('#duplicate_booking_to_other_resource__resource_select').val(),
    'ui_clicked_element_id': el_id
  });
  wpbc_button_enable_loading_icon(this_el);
}

//TODO: delete

/**
 *   Change booking cost   -------------------------------------------------------------------------------------- */

function wpbc_ajx_booking__ui_click_save__set_booking_cost(booking_id, this_el, booking_action, el_id) {
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': booking_action,
    'booking_id': booking_id,
    'booking_cost': jQuery('#ui_btn_set_booking_cost' + booking_id + '_cost').val(),
    'ui_clicked_element_id': el_id + '_save'
  });
  wpbc_button_enable_loading_icon(this_el);
  jQuery('#' + el_id + '_cancel').hide();
  //wpbc_button_enable_loading_icon( jQuery( '#' + el_id + '_cancel').get(0) );
}
function wpbc_ajx_booking__ui_click_close__set_booking_cost() {
  // Hide all change  payment status for booking
  jQuery(".ui__set_booking_cost__section_in_booking").hide();
}

/**
 *   Send Payment request   -------------------------------------------------------------------------------------- */

function wpbc_ajx_booking__ui_click__send_payment_request() {
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': 'send_payment_request',
    'booking_id': jQuery('#wpbc_modal__payment_request__booking_id').val(),
    'reason_of_action': jQuery('#wpbc_modal__payment_request__reason_of_action').val(),
    'ui_clicked_element_id': 'wpbc_modal__payment_request__button_send'
  });
  wpbc_button_enable_loading_icon(jQuery('#wpbc_modal__payment_request__button_send').get(0));
}

/**
 *   Import Google Calendar  ------------------------------------------------------------------------------------ */

function wpbc_ajx_booking__ui_click__import_google_calendar() {
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': 'import_google_calendar',
    'ui_clicked_element_id': 'wpbc_modal__import_google_calendar__button_send',
    'booking_gcal_events_from': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_from option:selected').val(),
    'booking_gcal_events_from_offset': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_from_offset').val(),
    'booking_gcal_events_from_offset_type': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_from_offset_type option:selected').val(),
    'booking_gcal_events_until': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_until option:selected').val(),
    'booking_gcal_events_until_offset': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_until_offset').val(),
    'booking_gcal_events_until_offset_type': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_until_offset_type option:selected').val(),
    'booking_gcal_events_max': jQuery('#wpbc_modal__import_google_calendar__section #booking_gcal_events_max').val(),
    'booking_gcal_resource': jQuery('#wpbc_modal__import_google_calendar__section #wpbc_booking_resource option:selected').val()
  });
  wpbc_button_enable_loading_icon(jQuery('#wpbc_modal__import_google_calendar__section #wpbc_modal__import_google_calendar__button_send').get(0));
}

/**
 *   Export bookings to CSV  ------------------------------------------------------------------------------------ */
function wpbc_ajx_booking__ui_click__export_csv(params) {
  var selected_booking_id_arr = wpbc_get_selected_row_id();
  wpbc_ajx_booking_ajax_action_request({
    'booking_action': params['booking_action'],
    'ui_clicked_element_id': params['ui_clicked_element_id'],
    'export_type': params['export_type'],
    'csv_export_separator': params['csv_export_separator'],
    'csv_export_skip_fields': params['csv_export_skip_fields'],
    'booking_id': selected_booking_id_arr.join(','),
    'search_params': wpbc_ajx_booking_listing.search_get_all_params()
  });
  var this_el = jQuery('#' + params['ui_clicked_element_id']).get(0);
  wpbc_button_enable_loading_icon(this_el);
}

/**
 * Open URL in new tab - mainly  it's used for open CSV link  for downloaded exported bookings as CSV
 *
 * @param export_csv_url
 */
function wpbc_ajx_booking__export_csv_url__download(export_csv_url) {
  //var selected_booking_id_arr = wpbc_get_selected_row_id();

  document.location.href = export_csv_url; // + '&selected_id=' + selected_booking_id_arr.join(',');

  // It's open additional dialog for asking opening ulr in new tab
  // window.open( export_csv_url, '_blank').focus();
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1ib29raW5ncy9fb3V0L2Jvb2tpbmdzX19hY3Rpb25zLmpzIiwibmFtZXMiOlsid3BiY19hanhfYm9va2luZ19hamF4X2FjdGlvbl9yZXF1ZXN0IiwiYWN0aW9uX3BhcmFtIiwiY29uc29sZSIsImdyb3VwQ29sbGFwc2VkIiwibG9nIiwid3BiY19hZG1pbl9zaG93X21lc3NhZ2VfcHJvY2Vzc2luZyIsIndwYmNfYm9va2luZ19saXN0aW5nX3JlbG9hZF9idXR0b25fX3NwaW5fc3RhcnQiLCJ1bmRlZmluZWQiLCJBcnJheSIsImlzQXJyYXkiLCJ3cGJjX2dldF9zZWxlY3RlZF9sb2NhbGUiLCJ3cGJjX2FqeF9ib29raW5nX2xpc3RpbmciLCJnZXRfc2VjdXJlX3BhcmFtIiwiYWN0aW9uX3Bvc3RfcGFyYW1zIiwiYWN0aW9uIiwibm9uY2UiLCJ3cGJjX2FqeF91c2VyX2lkIiwid3BiY19hanhfbG9jYWxlIiwiYWN0aW9uX3BhcmFtcyIsInNlYXJjaF9wYXJhbXMiLCJqUXVlcnkiLCJwb3N0Iiwid3BiY191cmxfYWpheCIsInJlc3BvbnNlX2RhdGEiLCJ0ZXh0U3RhdHVzIiwianFYSFIiLCJncm91cEVuZCIsImhpZGUiLCJnZXRfb3RoZXJfcGFyYW0iLCJodG1sIiwid3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSIsIndwYmNfYWRtaW5fc2hvd19tZXNzYWdlIiwicmVwbGFjZSIsImlzX3JlbG9hZF9hamF4X2xpc3RpbmciLCJ3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMiLCJjbG9zZWRfdGltZXIiLCJzZXRUaW1lb3V0Iiwid3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9faXNfc3BpbiIsImRvY3VtZW50IiwibG9jYXRpb24iLCJocmVmIiwicmVsb2FkIiwid3BiY19hanhfYm9va2luZ19fZXhwb3J0X2Nzdl91cmxfX2Rvd25sb2FkIiwid3BiY19hanhfYm9va2luZ19fYWN0dWFsX2xpc3RpbmdfX3Nob3ciLCJ3cGJjX2J1dHRvbl9fcmVtb3ZlX3NwaW4iLCJ3cGJjX3BvcHVwX21vZGFsc19faGlkZSIsImZhaWwiLCJlcnJvclRocm93biIsIndpbmRvdyIsImVycm9yX21lc3NhZ2UiLCJyZXNwb25zZVRleHQiLCJ3cGJjX2FqeF9ib29raW5nX3Nob3dfbWVzc2FnZSIsIndwYmNfbXlfbW9kYWwiLCJ3cGJjX2FqeF9jbGlja19vbl9kYXRlc19zaG9ydCIsInNob3ciLCJ3cGJjX2FqeF9jbGlja19vbl9kYXRlc193aWRlIiwid3BiY19hanhfY2xpY2tfb25fZGF0ZXNfdG9nZ2xlIiwidGhpc19kYXRlIiwicGFyZW50cyIsImZpbmQiLCJ0b2dnbGUiLCJ3cGJjX2FqeF9ib29raW5nX191aV9kZWZpbmVfX2xvY2FsZSIsImVhY2giLCJpbmRleCIsInNlbGVjdGlvbiIsImF0dHIiLCJwcm9wIiwiaGFzQ2xhc3MiLCJib29raW5nX2xvY2FsZV9idXR0b24iLCJhZGRDbGFzcyIsIndwYmNfdGlwcHkiLCJnZXQiLCJfdGlwcHkiLCJzZXRDb250ZW50Iiwid3BiY19hanhfYm9va2luZ19fdWlfZGVmaW5lX19yZW1hcmsiLCJ0ZXh0X3ZhbCIsInZhbCIsInJlbWFya19idXR0b24iLCJsZW5ndGgiLCJzZXRQcm9wcyIsImFsbG93SFRNTCIsImNvbnRlbnQiLCJ3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19zYXZlX19jaGFuZ2VfcmVzb3VyY2UiLCJ0aGlzX2VsIiwiYm9va2luZ19hY3Rpb24iLCJlbF9pZCIsIndwYmNfYnV0dG9uX2VuYWJsZV9sb2FkaW5nX2ljb24iLCJ3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19zYXZlX19kdXBsaWNhdGVfYm9va2luZyIsIndwYmNfYWp4X2Jvb2tpbmdfX3VpX2NsaWNrX3NhdmVfX3NldF9ib29raW5nX2Nvc3QiLCJib29raW5nX2lkIiwid3BiY19hanhfYm9va2luZ19fdWlfY2xpY2tfY2xvc2VfX3NldF9ib29raW5nX2Nvc3QiLCJ3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19fc2VuZF9wYXltZW50X3JlcXVlc3QiLCJ3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19faW1wb3J0X2dvb2dsZV9jYWxlbmRhciIsIndwYmNfYWp4X2Jvb2tpbmdfX3VpX2NsaWNrX19leHBvcnRfY3N2IiwicGFyYW1zIiwic2VsZWN0ZWRfYm9va2luZ19pZF9hcnIiLCJ3cGJjX2dldF9zZWxlY3RlZF9yb3dfaWQiLCJqb2luIiwic2VhcmNoX2dldF9hbGxfcGFyYW1zIiwiZXhwb3J0X2Nzdl91cmwiXSwic291cmNlcyI6WyJpbmNsdWRlcy9wYWdlLWJvb2tpbmdzL19zcmMvYm9va2luZ3NfX2FjdGlvbnMuanMiXSwic291cmNlc0NvbnRlbnQiOlsiXCJ1c2Ugc3RyaWN0XCI7XHJcblxyXG4vKipcclxuICogICBBamF4ICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuLy92YXIgaXNfdGhpc19hY3Rpb24gPSBmYWxzZTtcclxuLyoqXHJcbiAqIFNlbmQgQWpheCBhY3Rpb24gcmVxdWVzdCwgIGxpa2UgYXBwcm92aW5nIG9yIGNhbmNlbGxhdGlvblxyXG4gKlxyXG4gKiBAcGFyYW0gYWN0aW9uX3BhcmFtXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX2FqYXhfYWN0aW9uX3JlcXVlc3QoIGFjdGlvbl9wYXJhbSA9IHt9ICl7XHJcblxyXG5jb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnV1BCQ19BSlhfQk9PS0lOR19BQ1RJT05TJyApOyBjb25zb2xlLmxvZyggJyA9PSBBamF4IEFjdGlvbnMgOjogUGFyYW1zID09ICcsIGFjdGlvbl9wYXJhbSApO1xyXG4vL2lzX3RoaXNfYWN0aW9uID0gdHJ1ZTtcclxuXHJcblx0d3BiY19hZG1pbl9zaG93X21lc3NhZ2VfcHJvY2Vzc2luZyggJycgKTtcclxuXHJcblx0d3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCgpO1xyXG5cclxuXHQvLyBHZXQgcmVkZWZpbmVkIExvY2FsZSwgIGlmIGFjdGlvbiBvbiBzaW5nbGUgYm9va2luZyAhXHJcblx0aWYgKCAgKCB1bmRlZmluZWQgIT0gYWN0aW9uX3BhcmFtWyAnYm9va2luZ19pZCcgXSApICYmICggISBBcnJheS5pc0FycmF5KCBhY3Rpb25fcGFyYW1bICdib29raW5nX2lkJyBdICkgKSApe1x0XHRcdFx0Ly8gTm90IGFycmF5XHJcblxyXG5cdFx0YWN0aW9uX3BhcmFtWyAnbG9jYWxlJyBdID0gd3BiY19nZXRfc2VsZWN0ZWRfbG9jYWxlKCBhY3Rpb25fcGFyYW1bICdib29raW5nX2lkJyBdLCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X3NlY3VyZV9wYXJhbSggJ2xvY2FsZScgKSApO1xyXG5cdH1cclxuXHJcblx0dmFyIGFjdGlvbl9wb3N0X3BhcmFtcyA9IHtcclxuXHRcdFx0XHRcdFx0XHRcdGFjdGlvbiAgICAgICAgICA6ICdXUEJDX0FKWF9CT09LSU5HX0FDVElPTlMnLFxyXG5cdFx0XHRcdFx0XHRcdFx0bm9uY2UgICAgICAgICAgIDogd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9zZWN1cmVfcGFyYW0oICdub25jZScgKSxcclxuXHRcdFx0XHRcdFx0XHRcdHdwYmNfYWp4X3VzZXJfaWQ6ICggKCB1bmRlZmluZWQgPT0gYWN0aW9uX3BhcmFtWyAndXNlcl9pZCcgXSApID8gd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9zZWN1cmVfcGFyYW0oICd1c2VyX2lkJyApIDogYWN0aW9uX3BhcmFtWyAndXNlcl9pZCcgXSApLFxyXG5cdFx0XHRcdFx0XHRcdFx0d3BiY19hanhfbG9jYWxlOiAgKCAoIHVuZGVmaW5lZCA9PSBhY3Rpb25fcGFyYW1bICdsb2NhbGUnIF0gKSAgPyB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X3NlY3VyZV9wYXJhbSggJ2xvY2FsZScgKSAgOiBhY3Rpb25fcGFyYW1bICdsb2NhbGUnIF0gKSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRhY3Rpb25fcGFyYW1zXHQ6IGFjdGlvbl9wYXJhbVxyXG5cdFx0XHRcdFx0XHRcdH07XHJcblxyXG5cdC8vIEl0J3MgcmVxdWlyZWQgZm9yIENTViBleHBvcnQgLSBnZXR0aW5nIHRoZSBzYW1lIGxpc3QgIG9mIGJvb2tpbmdzXHJcblx0aWYgKCB0eXBlb2YgYWN0aW9uX3BhcmFtLnNlYXJjaF9wYXJhbXMgIT09ICd1bmRlZmluZWQnICl7XHJcblx0XHRhY3Rpb25fcG9zdF9wYXJhbXNbICdzZWFyY2hfcGFyYW1zJyBdID0gYWN0aW9uX3BhcmFtLnNlYXJjaF9wYXJhbXM7XHJcblx0XHRkZWxldGUgYWN0aW9uX3Bvc3RfcGFyYW1zLmFjdGlvbl9wYXJhbXMuc2VhcmNoX3BhcmFtcztcclxuXHR9XHJcblxyXG5cdC8vIFN0YXJ0IEFqYXhcclxuXHRqUXVlcnkucG9zdCggd3BiY191cmxfYWpheCAsXHJcblxyXG5cdFx0XHRcdGFjdGlvbl9wb3N0X3BhcmFtcyAsXHJcblxyXG5cdFx0XHRcdC8qKlxyXG5cdFx0XHRcdCAqIFMgdSBjIGMgZSBzIHNcclxuXHRcdFx0XHQgKlxyXG5cdFx0XHRcdCAqIEBwYXJhbSByZXNwb25zZV9kYXRhXHRcdC1cdGl0cyBvYmplY3QgcmV0dXJuZWQgZnJvbSAgQWpheCAtIGNsYXNzLWxpdmUtc2VhcmNnLnBocFxyXG5cdFx0XHRcdCAqIEBwYXJhbSB0ZXh0U3RhdHVzXHRcdC1cdCdzdWNjZXNzJ1xyXG5cdFx0XHRcdCAqIEBwYXJhbSBqcVhIUlx0XHRcdFx0LVx0T2JqZWN0XHJcblx0XHRcdFx0ICovXHJcblx0XHRcdFx0ZnVuY3Rpb24gKCByZXNwb25zZV9kYXRhLCB0ZXh0U3RhdHVzLCBqcVhIUiApIHtcclxuXHJcbmNvbnNvbGUubG9nKCAnID09IEFqYXggQWN0aW9ucyA6OiBSZXNwb25zZSBXUEJDX0FKWF9CT09LSU5HX0FDVElPTlMgPT0gJywgcmVzcG9uc2VfZGF0YSApOyBjb25zb2xlLmdyb3VwRW5kKCk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gUHJvYmFibHkgRXJyb3JcclxuXHRcdFx0XHRcdGlmICggKHR5cGVvZiByZXNwb25zZV9kYXRhICE9PSAnb2JqZWN0JykgfHwgKHJlc3BvbnNlX2RhdGEgPT09IG51bGwpICl7XHJcblx0XHRcdFx0XHRcdGpRdWVyeSggJy53cGJjX2FqeF91bmRlcl90b29sYmFyX3JvdycgKS5oaWRlKCk7XHQgXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDkuNi4xLjUuXHJcblx0XHRcdFx0XHRcdGpRdWVyeSggd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9vdGhlcl9wYXJhbSggJ2xpc3RpbmdfY29udGFpbmVyJyApICkuaHRtbChcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwid3BiYy1zZXR0aW5ncy1ub3RpY2Ugbm90aWNlLXdhcm5pbmdcIiBzdHlsZT1cInRleHQtYWxpZ246bGVmdFwiPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRyZXNwb25zZV9kYXRhICtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfbGlzdGluZ19yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlKCk7XHJcblxyXG5cdFx0XHRcdFx0d3BiY19hZG1pbl9zaG93X21lc3NhZ2UoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgcmVzcG9uc2VfZGF0YVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICggJzEnID09IHJlc3BvbnNlX2RhdGFbICdhanhfYWZ0ZXJfYWN0aW9uX3Jlc3VsdCcgXSApID8gJ3N1Y2Nlc3MnIDogJ2Vycm9yJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICggKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mKHJlc3BvbnNlX2RhdGFbICdhanhfYWZ0ZXJfYWN0aW9uX3Jlc3VsdF9hbGxfcGFyYW1zX2FycicgXVsgJ2FmdGVyX2FjdGlvbl9yZXN1bHRfZGVsYXknIF0pIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ/IDEwMDAwXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0OiByZXNwb25zZV9kYXRhWyAnYWp4X2FmdGVyX2FjdGlvbl9yZXN1bHRfYWxsX3BhcmFtc19hcnInIF1bICdhZnRlcl9hY3Rpb25fcmVzdWx0X2RlbGF5JyBdIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gU3VjY2VzcyByZXNwb25zZVxyXG5cdFx0XHRcdFx0aWYgKCAnMScgPT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9hZnRlcl9hY3Rpb25fcmVzdWx0JyBdICl7XHJcblxyXG5cdFx0XHRcdFx0XHR2YXIgaXNfcmVsb2FkX2FqYXhfbGlzdGluZyA9IHRydWU7XHJcblxyXG5cdFx0XHRcdFx0XHQvLyBBZnRlciBHb29nbGUgQ2FsZW5kYXIgaW1wb3J0IHNob3cgaW1wb3J0ZWQgYm9va2luZ3MgYW5kIHJlbG9hZCB0aGUgcGFnZSBmb3IgdG9vbGJhciBwYXJhbWV0ZXJzIHVwZGF0ZVxyXG5cdFx0XHRcdFx0XHRpZiAoIGZhbHNlICE9PSByZXNwb25zZV9kYXRhWyAnYWp4X2FmdGVyX2FjdGlvbl9yZXN1bHRfYWxsX3BhcmFtc19hcnInIF1bICduZXdfbGlzdGluZ19wYXJhbXMnIF0gKXtcclxuXHJcblx0XHRcdFx0XHRcdFx0d3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zKCByZXNwb25zZV9kYXRhWyAnYWp4X2FmdGVyX2FjdGlvbl9yZXN1bHRfYWxsX3BhcmFtc19hcnInIF1bICduZXdfbGlzdGluZ19wYXJhbXMnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRcdFx0dmFyIGNsb3NlZF90aW1lciA9IHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpe1xyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0aWYgKCB3cGJjX2Jvb2tpbmdfbGlzdGluZ19yZWxvYWRfYnV0dG9uX19pc19zcGluKCkgKXtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRpZiAoIHVuZGVmaW5lZCAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2FmdGVyX2FjdGlvbl9yZXN1bHRfYWxsX3BhcmFtc19hcnInIF1bICduZXdfbGlzdGluZ19wYXJhbXMnIF1bICdyZWxvYWRfdXJsX3BhcmFtcycgXSApe1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZG9jdW1lbnQubG9jYXRpb24uaHJlZiA9IHJlc3BvbnNlX2RhdGFbICdhanhfYWZ0ZXJfYWN0aW9uX3Jlc3VsdF9hbGxfcGFyYW1zX2FycicgXVsgJ25ld19saXN0aW5nX3BhcmFtcycgXVsgJ3JlbG9hZF91cmxfcGFyYW1zJyBdO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRkb2N1bWVudC5sb2NhdGlvbi5yZWxvYWQoKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAyMDAwICk7XHJcblx0XHRcdFx0XHRcdFx0aXNfcmVsb2FkX2FqYXhfbGlzdGluZyA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0XHQvLyBTdGFydCBkb3dubG9hZCBleHBvcnRlZCBDU1YgZmlsZVxyXG5cdFx0XHRcdFx0XHRpZiAoIHVuZGVmaW5lZCAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2FmdGVyX2FjdGlvbl9yZXN1bHRfYWxsX3BhcmFtc19hcnInIF1bICdleHBvcnRfY3N2X3VybCcgXSApe1xyXG5cdFx0XHRcdFx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfX2V4cG9ydF9jc3ZfdXJsX19kb3dubG9hZCggcmVzcG9uc2VfZGF0YVsgJ2FqeF9hZnRlcl9hY3Rpb25fcmVzdWx0X2FsbF9wYXJhbXNfYXJyJyBdWyAnZXhwb3J0X2Nzdl91cmwnIF0gKTtcclxuXHRcdFx0XHRcdFx0XHRpc19yZWxvYWRfYWpheF9saXN0aW5nID0gZmFsc2U7XHJcblx0XHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRcdGlmICggaXNfcmVsb2FkX2FqYXhfbGlzdGluZyApe1xyXG5cdFx0XHRcdFx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfX2FjdHVhbF9saXN0aW5nX19zaG93KCk7XHQvL1x0U2VuZGluZyBBamF4IFJlcXVlc3RcdC1cdHdpdGggcGFyYW1ldGVycyB0aGF0ICB3ZSBlYXJseSAgZGVmaW5lZCBpbiBcIndwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZ1wiIE9iai5cclxuXHRcdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBSZW1vdmUgc3BpbiBpY29uIGZyb20gIGJ1dHRvbiBhbmQgRW5hYmxlIHRoaXMgYnV0dG9uLlxyXG5cdFx0XHRcdFx0d3BiY19idXR0b25fX3JlbW92ZV9zcGluKCByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWyAndWlfY2xpY2tlZF9lbGVtZW50X2lkJyBdIClcclxuXHJcblx0XHRcdFx0XHQvLyBIaWRlIG1vZGFsc1xyXG5cdFx0XHRcdFx0d3BiY19wb3B1cF9tb2RhbHNfX2hpZGUoKTtcclxuXHJcblx0XHRcdFx0XHRqUXVlcnkoICcjYWpheF9yZXNwb25kJyApLmh0bWwoIHJlc3BvbnNlX2RhdGEgKTtcdFx0Ly8gRm9yIGFiaWxpdHkgdG8gc2hvdyByZXNwb25zZSwgYWRkIHN1Y2ggRElWIGVsZW1lbnQgdG8gcGFnZVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0ICApLmZhaWwoIGZ1bmN0aW9uICgganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duICkgeyAgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ0FqYXhfRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKTsgfVxyXG5cdFx0XHRcdFx0alF1ZXJ5KCAnLndwYmNfYWp4X3VuZGVyX3Rvb2xiYXJfcm93JyApLmhpZGUoKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDkuNi4xLjUuXHJcblx0XHRcdFx0XHR2YXIgZXJyb3JfbWVzc2FnZSA9ICc8c3Ryb25nPicgKyAnRXJyb3IhJyArICc8L3N0cm9uZz4gJyArIGVycm9yVGhyb3duIDtcclxuXHRcdFx0XHRcdGlmICgganFYSFIucmVzcG9uc2VUZXh0ICl7XHJcblx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0ganFYSFIucmVzcG9uc2VUZXh0O1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSA9IGVycm9yX21lc3NhZ2UucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICk7XHJcblxyXG5cdFx0XHRcdFx0d3BiY19hanhfYm9va2luZ19zaG93X21lc3NhZ2UoIGVycm9yX21lc3NhZ2UgKTtcclxuXHRcdFx0ICB9KVxyXG5cdCAgICAgICAgICAvLyAuZG9uZSggICBmdW5jdGlvbiAoIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnc2Vjb25kIHN1Y2Nlc3MnLCBkYXRhLCB0ZXh0U3RhdHVzLCBqcVhIUiApOyB9ICAgIH0pXHJcblx0XHRcdCAgLy8gLmFsd2F5cyggZnVuY3Rpb24gKCBkYXRhX2pxWEhSLCB0ZXh0U3RhdHVzLCBqcVhIUl9lcnJvclRocm93biApIHsgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ2Fsd2F5cyBmaW5pc2hlZCcsIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICk7IH0gICAgIH0pXHJcblx0XHRcdCAgOyAgLy8gRW5kIEFqYXhcclxufVxyXG5cclxuXHJcblxyXG4vKipcclxuICogSGlkZSBhbGwgb3BlbiBtb2RhbCBwb3B1cHMgd2luZG93c1xyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19wb3B1cF9tb2RhbHNfX2hpZGUoKXtcclxuXHJcblx0Ly8gSGlkZSBtb2RhbHNcclxuXHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAoalF1ZXJ5KCAnLndwYmNfcG9wdXBfbW9kYWwnICkud3BiY19teV9tb2RhbCkgKXtcclxuXHRcdGpRdWVyeSggJy53cGJjX3BvcHVwX21vZGFsJyApLndwYmNfbXlfbW9kYWwoICdoaWRlJyApO1xyXG5cdH1cclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIERhdGVzICBTaG9ydCA8LT4gV2lkZSAgICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuZnVuY3Rpb24gd3BiY19hanhfY2xpY2tfb25fZGF0ZXNfc2hvcnQoKXtcclxuXHRqUXVlcnkoICcjYm9va2luZ19kYXRlc19zbWFsbCwuYm9va2luZ19kYXRlc19mdWxsJyApLmhpZGUoKTtcclxuXHRqUXVlcnkoICcjYm9va2luZ19kYXRlc19mdWxsLC5ib29raW5nX2RhdGVzX3NtYWxsJyApLnNob3coKTtcclxuXHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHsndWlfdXNyX19kYXRlc19zaG9ydF93aWRlJzogJ3Nob3J0J30gKTtcclxufVxyXG5cclxuZnVuY3Rpb24gd3BiY19hanhfY2xpY2tfb25fZGF0ZXNfd2lkZSgpe1xyXG5cdGpRdWVyeSggJyNib29raW5nX2RhdGVzX2Z1bGwsLmJvb2tpbmdfZGF0ZXNfc21hbGwnICkuaGlkZSgpO1xyXG5cdGpRdWVyeSggJyNib29raW5nX2RhdGVzX3NtYWxsLC5ib29raW5nX2RhdGVzX2Z1bGwnICkuc2hvdygpO1xyXG5cdHdwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcyggeyd1aV91c3JfX2RhdGVzX3Nob3J0X3dpZGUnOiAnd2lkZSd9ICk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2NsaWNrX29uX2RhdGVzX3RvZ2dsZSh0aGlzX2RhdGUpe1xyXG5cclxuXHRqUXVlcnkoIHRoaXNfZGF0ZSApLnBhcmVudHMoICcud3BiY19jb2xfZGF0ZXMnICkuZmluZCggJy5ib29raW5nX2RhdGVzX3NtYWxsJyApLnRvZ2dsZSgpO1xyXG5cdGpRdWVyeSggdGhpc19kYXRlICkucGFyZW50cyggJy53cGJjX2NvbF9kYXRlcycgKS5maW5kKCAnLmJvb2tpbmdfZGF0ZXNfZnVsbCcgKS50b2dnbGUoKTtcclxuXHJcblx0LypcclxuXHR2YXIgdmlzaWJsZV9zZWN0aW9uID0galF1ZXJ5KCB0aGlzX2RhdGUgKS5wYXJlbnRzKCAnLmJvb2tpbmdfZGF0ZXNfZXhwYW5kX3NlY3Rpb24nICk7XHJcblx0dmlzaWJsZV9zZWN0aW9uLmhpZGUoKTtcclxuXHRpZiAoIHZpc2libGVfc2VjdGlvbi5oYXNDbGFzcyggJ2Jvb2tpbmdfZGF0ZXNfZnVsbCcgKSApe1xyXG5cdFx0dmlzaWJsZV9zZWN0aW9uLnBhcmVudHMoICcud3BiY19jb2xfZGF0ZXMnICkuZmluZCggJy5ib29raW5nX2RhdGVzX3NtYWxsJyApLnNob3coKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0dmlzaWJsZV9zZWN0aW9uLnBhcmVudHMoICcud3BiY19jb2xfZGF0ZXMnICkuZmluZCggJy5ib29raW5nX2RhdGVzX2Z1bGwnICkuc2hvdygpO1xyXG5cdH0qL1xyXG5cdGNvbnNvbGUubG9nKCAnd3BiY19hanhfY2xpY2tfb25fZGF0ZXNfdG9nZ2xlJywgdGhpc19kYXRlICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiAgIExvY2FsZSAgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFx0U2VsZWN0IG9wdGlvbnMgaW4gc2VsZWN0IGJveGVzIGJhc2VkIG9uIGF0dHJpYnV0ZSBcInZhbHVlX29mX3NlbGVjdGVkX29wdGlvblwiIGFuZCBSRUQgY29sb3IgYW5kIGhpbnQgZm9yIExPQ0FMRSBidXR0b24gICAtLSAgSXQncyBjYWxsZWQgZnJvbSBcdHdwYmNfYWp4X2Jvb2tpbmdfZGVmaW5lX3VpX2hvb2tzKCkgIFx0ZWFjaCAgdGltZSBhZnRlciBMaXN0aW5nIGxvYWRpbmcuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9kZWZpbmVfX2xvY2FsZSgpe1xyXG5cclxuXHRqUXVlcnkoICcud3BiY19fbGlzdF9fdGFibGUgc2VsZWN0JyApLmVhY2goIGZ1bmN0aW9uICggaW5kZXggKXtcclxuXHJcblx0XHR2YXIgc2VsZWN0aW9uID0galF1ZXJ5KCB0aGlzICkuYXR0ciggXCJ2YWx1ZV9vZl9zZWxlY3RlZF9vcHRpb25cIiApO1x0XHRcdC8vIERlZmluZSBzZWxlY3RlZCBzZWxlY3QgYm94ZXNcclxuXHJcblx0XHRpZiAoIHVuZGVmaW5lZCAhPT0gc2VsZWN0aW9uICl7XHJcblx0XHRcdGpRdWVyeSggdGhpcyApLmZpbmQoICdvcHRpb25bdmFsdWU9XCInICsgc2VsZWN0aW9uICsgJ1wiXScgKS5wcm9wKCAnc2VsZWN0ZWQnLCB0cnVlICk7XHJcblxyXG5cdFx0XHRpZiAoICgnJyAhPSBzZWxlY3Rpb24pICYmIChqUXVlcnkoIHRoaXMgKS5oYXNDbGFzcyggJ3NldF9ib29raW5nX2xvY2FsZV9zZWxlY3Rib3gnICkpICl7XHRcdFx0XHRcdFx0XHRcdC8vIExvY2FsZVxyXG5cclxuXHRcdFx0XHR2YXIgYm9va2luZ19sb2NhbGVfYnV0dG9uID0galF1ZXJ5KCB0aGlzICkucGFyZW50cyggJy51aV9lbGVtZW50X2xvY2FsZScgKS5maW5kKCAnLnNldF9ib29raW5nX2xvY2FsZV9idXR0b24nIClcclxuXHJcblx0XHRcdFx0Ly9ib29raW5nX2xvY2FsZV9idXR0b24uY3NzKCAnY29sb3InLCAnI2RiNDgwMCcgKTtcdFx0Ly8gU2V0IGJ1dHRvbiAgcmVkXHJcblx0XHRcdFx0Ym9va2luZ19sb2NhbGVfYnV0dG9uLmFkZENsYXNzKCAnd3BiY191aV9yZWQnICk7XHRcdC8vIFNldCBidXR0b24gIHJlZFxyXG5cdFx0XHRcdCBpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiggd3BiY190aXBweSApICl7XHJcblx0XHRcdFx0XHRib29raW5nX2xvY2FsZV9idXR0b24uZ2V0KDApLl90aXBweS5zZXRDb250ZW50KCBzZWxlY3Rpb24gKTtcclxuXHRcdFx0XHQgfVxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fSApO1xyXG59XHJcblxyXG4vKipcclxuICogICBSZW1hcmsgICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBEZWZpbmUgY29udGVudCBvZiByZW1hcmsgXCJib29raW5nIG5vdGVcIiBidXR0b24gYW5kIHRleHRhcmVhLiAgLS0gSXQncyBjYWxsZWQgZnJvbSBcdHdwYmNfYWp4X2Jvb2tpbmdfZGVmaW5lX3VpX2hvb2tzKCkgIFx0ZWFjaCAgdGltZSBhZnRlciBMaXN0aW5nIGxvYWRpbmcuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9kZWZpbmVfX3JlbWFyaygpe1xyXG5cclxuXHRqUXVlcnkoICcud3BiY19fbGlzdF9fdGFibGUgLnVpX3JlbWFya19zZWN0aW9uIHRleHRhcmVhJyApLmVhY2goIGZ1bmN0aW9uICggaW5kZXggKXtcclxuXHRcdHZhciB0ZXh0X3ZhbCA9IGpRdWVyeSggdGhpcyApLnZhbCgpO1xyXG5cdFx0aWYgKCAodW5kZWZpbmVkICE9PSB0ZXh0X3ZhbCkgJiYgKCcnICE9IHRleHRfdmFsKSApe1xyXG5cclxuXHRcdFx0dmFyIHJlbWFya19idXR0b24gPSBqUXVlcnkoIHRoaXMgKS5wYXJlbnRzKCAnLnVpX2dyb3VwJyApLmZpbmQoICcuc2V0X2Jvb2tpbmdfbm90ZV9idXR0b24nICk7XHJcblxyXG5cdFx0XHRpZiAoIHJlbWFya19idXR0b24ubGVuZ3RoID4gMCApe1xyXG5cclxuXHRcdFx0XHRyZW1hcmtfYnV0dG9uLmFkZENsYXNzKCAnd3BiY191aV9yZWQnICk7XHRcdC8vIFNldCBidXR0b24gIHJlZFxyXG5cdFx0XHRcdGlmICggJ2Z1bmN0aW9uJyA9PT0gdHlwZW9mICh3cGJjX3RpcHB5KSApe1xyXG5cdFx0XHRcdFx0Ly9yZW1hcmtfYnV0dG9uLmdldCggMCApLl90aXBweS5hbGxvd0hUTUwgPSB0cnVlO1xyXG5cdFx0XHRcdFx0Ly9yZW1hcmtfYnV0dG9uLmdldCggMCApLl90aXBweS5zZXRDb250ZW50KCB0ZXh0X3ZhbC5yZXBsYWNlKC9bXFxuXFxyXS9nLCAnPGJyPicpICk7XHJcblxyXG5cdFx0XHRcdFx0cmVtYXJrX2J1dHRvbi5nZXQoIDAgKS5fdGlwcHkuc2V0UHJvcHMoIHtcclxuXHRcdFx0XHRcdFx0YWxsb3dIVE1MOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRjb250ZW50ICA6IHRleHRfdmFsLnJlcGxhY2UoIC9bXFxuXFxyXS9nLCAnPGJyPicgKVxyXG5cdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH0gKTtcclxufVxyXG5cclxuXHJcblxyXG5cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19fdWlfY2xpY2tfc2F2ZV9fY2hhbmdlX3Jlc291cmNlKCB0aGlzX2VsLCBib29raW5nX2FjdGlvbiwgZWxfaWQgKXtcclxuXHJcblx0d3BiY19hanhfYm9va2luZ19hamF4X2FjdGlvbl9yZXF1ZXN0KCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2luZ19hY3Rpb24nICAgICAgIDogYm9va2luZ19hY3Rpb24sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2luZ19pZCcgICAgICAgICAgIDogalF1ZXJ5KCAnI2NoYW5nZV9ib29raW5nX3Jlc291cmNlX19ib29raW5nX2lkJyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3NlbGVjdGVkX3Jlc291cmNlX2lkJyA6IGpRdWVyeSggJyNjaGFuZ2VfYm9va2luZ19yZXNvdXJjZV9fcmVzb3VyY2Vfc2VsZWN0JyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VpX2NsaWNrZWRfZWxlbWVudF9pZCc6IGVsX2lkXHJcblx0fSApO1xyXG5cclxuXHR3cGJjX2J1dHRvbl9lbmFibGVfbG9hZGluZ19pY29uKCB0aGlzX2VsICk7XHJcblxyXG5cclxufVxyXG5cclxuXHJcblxyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19zYXZlX19kdXBsaWNhdGVfYm9va2luZyggdGhpc19lbCwgYm9va2luZ19hY3Rpb24sIGVsX2lkICl7XHJcblxyXG5cdHdwYmNfYWp4X2Jvb2tpbmdfYWpheF9hY3Rpb25fcmVxdWVzdCgge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2Jvb2tpbmdfYWN0aW9uJyAgICAgICA6IGJvb2tpbmdfYWN0aW9uLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2Jvb2tpbmdfaWQnICAgICAgICAgICA6IGpRdWVyeSggJyNkdXBsaWNhdGVfYm9va2luZ190b19vdGhlcl9yZXNvdXJjZV9fYm9va2luZ19pZCcgKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RlZF9yZXNvdXJjZV9pZCcgOiBqUXVlcnkoICcjZHVwbGljYXRlX2Jvb2tpbmdfdG9fb3RoZXJfcmVzb3VyY2VfX3Jlc291cmNlX3NlbGVjdCcgKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV9jbGlja2VkX2VsZW1lbnRfaWQnOiBlbF9pZFxyXG5cdH0gKTtcclxuXHJcblx0d3BiY19idXR0b25fZW5hYmxlX2xvYWRpbmdfaWNvbiggdGhpc19lbCApO1xyXG5cclxuXHJcbn1cclxuXHJcblxyXG5cclxuLy9UT0RPOiBkZWxldGVcclxuXHJcblxyXG4vKipcclxuICogICBDaGFuZ2UgYm9va2luZyBjb3N0ICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2Jvb2tpbmdfX3VpX2NsaWNrX3NhdmVfX3NldF9ib29raW5nX2Nvc3QoIGJvb2tpbmdfaWQsIHRoaXNfZWwsIGJvb2tpbmdfYWN0aW9uLCBlbF9pZCApe1xyXG5cclxuXHR3cGJjX2FqeF9ib29raW5nX2FqYXhfYWN0aW9uX3JlcXVlc3QoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdib29raW5nX2FjdGlvbicgICAgICAgOiBib29raW5nX2FjdGlvbixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdib29raW5nX2lkJyAgICAgICAgICAgOiBib29raW5nX2lkLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2Jvb2tpbmdfY29zdCcgXHRcdCAgIDogalF1ZXJ5KCAnI3VpX2J0bl9zZXRfYm9va2luZ19jb3N0JyArIGJvb2tpbmdfaWQgKyAnX2Nvc3QnKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV9jbGlja2VkX2VsZW1lbnRfaWQnOiBlbF9pZCArICdfc2F2ZSdcclxuXHR9ICk7XHJcblxyXG5cdHdwYmNfYnV0dG9uX2VuYWJsZV9sb2FkaW5nX2ljb24oIHRoaXNfZWwgKTtcclxuXHJcblx0alF1ZXJ5KCAnIycgKyBlbF9pZCArICdfY2FuY2VsJykuaGlkZSgpO1xyXG5cdC8vd3BiY19idXR0b25fZW5hYmxlX2xvYWRpbmdfaWNvbiggalF1ZXJ5KCAnIycgKyBlbF9pZCArICdfY2FuY2VsJykuZ2V0KDApICk7XHJcblxyXG59XHJcblxyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19jbG9zZV9fc2V0X2Jvb2tpbmdfY29zdCgpe1xyXG5cdC8vIEhpZGUgYWxsIGNoYW5nZSAgcGF5bWVudCBzdGF0dXMgZm9yIGJvb2tpbmdcclxuXHRqUXVlcnkoXCIudWlfX3NldF9ib29raW5nX2Nvc3RfX3NlY3Rpb25faW5fYm9va2luZ1wiKS5oaWRlKCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogICBTZW5kIFBheW1lbnQgcmVxdWVzdCAgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19fc2VuZF9wYXltZW50X3JlcXVlc3QoKXtcclxuXHJcblx0d3BiY19hanhfYm9va2luZ19hamF4X2FjdGlvbl9yZXF1ZXN0KCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2luZ19hY3Rpb24nICAgICAgIDogJ3NlbmRfcGF5bWVudF9yZXF1ZXN0JyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdib29raW5nX2lkJyAgICAgICAgICAgOiBqUXVlcnkoICcjd3BiY19tb2RhbF9fcGF5bWVudF9yZXF1ZXN0X19ib29raW5nX2lkJykudmFsKCksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVhc29uX29mX2FjdGlvbicgXHQgICA6IGpRdWVyeSggJyN3cGJjX21vZGFsX19wYXltZW50X3JlcXVlc3RfX3JlYXNvbl9vZl9hY3Rpb24nKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV9jbGlja2VkX2VsZW1lbnRfaWQnOiAnd3BiY19tb2RhbF9fcGF5bWVudF9yZXF1ZXN0X19idXR0b25fc2VuZCdcclxuXHR9ICk7XHJcblx0d3BiY19idXR0b25fZW5hYmxlX2xvYWRpbmdfaWNvbiggalF1ZXJ5KCAnI3dwYmNfbW9kYWxfX3BheW1lbnRfcmVxdWVzdF9fYnV0dG9uX3NlbmQnICkuZ2V0KCAwICkgKTtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIEltcG9ydCBHb29nbGUgQ2FsZW5kYXIgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19fdWlfY2xpY2tfX2ltcG9ydF9nb29nbGVfY2FsZW5kYXIoKXtcclxuXHJcblx0d3BiY19hanhfYm9va2luZ19hamF4X2FjdGlvbl9yZXF1ZXN0KCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2luZ19hY3Rpb24nICAgICAgIDogJ2ltcG9ydF9nb29nbGVfY2FsZW5kYXInLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VpX2NsaWNrZWRfZWxlbWVudF9pZCc6ICd3cGJjX21vZGFsX19pbXBvcnRfZ29vZ2xlX2NhbGVuZGFyX19idXR0b25fc2VuZCdcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICdib29raW5nX2djYWxfZXZlbnRzX2Zyb20nIDogXHRcdFx0XHRqUXVlcnkoICcjd3BiY19tb2RhbF9faW1wb3J0X2dvb2dsZV9jYWxlbmRhcl9fc2VjdGlvbiAjYm9va2luZ19nY2FsX2V2ZW50c19mcm9tIG9wdGlvbjpzZWxlY3RlZCcpLnZhbCgpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICdib29raW5nX2djYWxfZXZlbnRzX2Zyb21fb2Zmc2V0JyA6IFx0XHRqUXVlcnkoICcjd3BiY19tb2RhbF9faW1wb3J0X2dvb2dsZV9jYWxlbmRhcl9fc2VjdGlvbiAjYm9va2luZ19nY2FsX2V2ZW50c19mcm9tX29mZnNldCcgKS52YWwoKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAnYm9va2luZ19nY2FsX2V2ZW50c19mcm9tX29mZnNldF90eXBlJyA6IFx0alF1ZXJ5KCAnI3dwYmNfbW9kYWxfX2ltcG9ydF9nb29nbGVfY2FsZW5kYXJfX3NlY3Rpb24gI2Jvb2tpbmdfZ2NhbF9ldmVudHNfZnJvbV9vZmZzZXRfdHlwZSBvcHRpb246c2VsZWN0ZWQnKS52YWwoKVxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2Jvb2tpbmdfZ2NhbF9ldmVudHNfdW50aWwnIDogXHRcdFx0alF1ZXJ5KCAnI3dwYmNfbW9kYWxfX2ltcG9ydF9nb29nbGVfY2FsZW5kYXJfX3NlY3Rpb24gI2Jvb2tpbmdfZ2NhbF9ldmVudHNfdW50aWwgb3B0aW9uOnNlbGVjdGVkJykudmFsKClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2Jvb2tpbmdfZ2NhbF9ldmVudHNfdW50aWxfb2Zmc2V0JyA6IFx0XHRqUXVlcnkoICcjd3BiY19tb2RhbF9faW1wb3J0X2dvb2dsZV9jYWxlbmRhcl9fc2VjdGlvbiAjYm9va2luZ19nY2FsX2V2ZW50c191bnRpbF9vZmZzZXQnICkudmFsKClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2Jvb2tpbmdfZ2NhbF9ldmVudHNfdW50aWxfb2Zmc2V0X3R5cGUnIDogalF1ZXJ5KCAnI3dwYmNfbW9kYWxfX2ltcG9ydF9nb29nbGVfY2FsZW5kYXJfX3NlY3Rpb24gI2Jvb2tpbmdfZ2NhbF9ldmVudHNfdW50aWxfb2Zmc2V0X3R5cGUgb3B0aW9uOnNlbGVjdGVkJykudmFsKClcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICdib29raW5nX2djYWxfZXZlbnRzX21heCcgOiBcdGpRdWVyeSggJyN3cGJjX21vZGFsX19pbXBvcnRfZ29vZ2xlX2NhbGVuZGFyX19zZWN0aW9uICNib29raW5nX2djYWxfZXZlbnRzX21heCcgKS52YWwoKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAnYm9va2luZ19nY2FsX3Jlc291cmNlJyA6IFx0alF1ZXJ5KCAnI3dwYmNfbW9kYWxfX2ltcG9ydF9nb29nbGVfY2FsZW5kYXJfX3NlY3Rpb24gI3dwYmNfYm9va2luZ19yZXNvdXJjZSBvcHRpb246c2VsZWN0ZWQnKS52YWwoKVxyXG5cdH0gKTtcclxuXHR3cGJjX2J1dHRvbl9lbmFibGVfbG9hZGluZ19pY29uKCBqUXVlcnkoICcjd3BiY19tb2RhbF9faW1wb3J0X2dvb2dsZV9jYWxlbmRhcl9fc2VjdGlvbiAjd3BiY19tb2RhbF9faW1wb3J0X2dvb2dsZV9jYWxlbmRhcl9fYnV0dG9uX3NlbmQnICkuZ2V0KCAwICkgKTtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIEV4cG9ydCBib29raW5ncyB0byBDU1YgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX191aV9jbGlja19fZXhwb3J0X2NzdiggcGFyYW1zICl7XHJcblxyXG5cdHZhciBzZWxlY3RlZF9ib29raW5nX2lkX2FyciA9IHdwYmNfZ2V0X3NlbGVjdGVkX3Jvd19pZCgpO1xyXG5cclxuXHR3cGJjX2FqeF9ib29raW5nX2FqYXhfYWN0aW9uX3JlcXVlc3QoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdib29raW5nX2FjdGlvbicgICAgICAgIDogcGFyYW1zWyAnYm9va2luZ19hY3Rpb24nIF0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndWlfY2xpY2tlZF9lbGVtZW50X2lkJyA6IHBhcmFtc1sgJ3VpX2NsaWNrZWRfZWxlbWVudF9pZCcgXSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZXhwb3J0X3R5cGUnICAgICAgICAgICA6IHBhcmFtc1sgJ2V4cG9ydF90eXBlJyBdLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2Nzdl9leHBvcnRfc2VwYXJhdG9yJyAgOiBwYXJhbXNbICdjc3ZfZXhwb3J0X3NlcGFyYXRvcicgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjc3ZfZXhwb3J0X3NraXBfZmllbGRzJzogcGFyYW1zWyAnY3N2X2V4cG9ydF9za2lwX2ZpZWxkcycgXSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2luZ19pZCdcdDogc2VsZWN0ZWRfYm9va2luZ19pZF9hcnIuam9pbignLCcpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3NlYXJjaF9wYXJhbXMnIDogd3BiY19hanhfYm9va2luZ19saXN0aW5nLnNlYXJjaF9nZXRfYWxsX3BhcmFtcygpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cclxuXHR2YXIgdGhpc19lbCA9IGpRdWVyeSggJyMnICsgcGFyYW1zWyAndWlfY2xpY2tlZF9lbGVtZW50X2lkJyBdICkuZ2V0KCAwIClcclxuXHJcblx0d3BiY19idXR0b25fZW5hYmxlX2xvYWRpbmdfaWNvbiggdGhpc19lbCApO1xyXG59XHJcblxyXG4vKipcclxuICogT3BlbiBVUkwgaW4gbmV3IHRhYiAtIG1haW5seSAgaXQncyB1c2VkIGZvciBvcGVuIENTViBsaW5rICBmb3IgZG93bmxvYWRlZCBleHBvcnRlZCBib29raW5ncyBhcyBDU1ZcclxuICpcclxuICogQHBhcmFtIGV4cG9ydF9jc3ZfdXJsXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX19leHBvcnRfY3N2X3VybF9fZG93bmxvYWQoIGV4cG9ydF9jc3ZfdXJsICl7XHJcblxyXG5cdC8vdmFyIHNlbGVjdGVkX2Jvb2tpbmdfaWRfYXJyID0gd3BiY19nZXRfc2VsZWN0ZWRfcm93X2lkKCk7XHJcblxyXG5cdGRvY3VtZW50LmxvY2F0aW9uLmhyZWYgPSBleHBvcnRfY3N2X3VybDsvLyArICcmc2VsZWN0ZWRfaWQ9JyArIHNlbGVjdGVkX2Jvb2tpbmdfaWRfYXJyLmpvaW4oJywnKTtcclxuXHJcblx0Ly8gSXQncyBvcGVuIGFkZGl0aW9uYWwgZGlhbG9nIGZvciBhc2tpbmcgb3BlbmluZyB1bHIgaW4gbmV3IHRhYlxyXG5cdC8vIHdpbmRvdy5vcGVuKCBleHBvcnRfY3N2X3VybCwgJ19ibGFuaycpLmZvY3VzKCk7XHJcbn0iXSwibWFwcGluZ3MiOiJBQUFBLFlBQVk7O0FBRVo7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNBLG9DQUFvQ0EsQ0FBRUMsWUFBWSxHQUFHLENBQUMsQ0FBQyxFQUFFO0VBRWxFQyxPQUFPLENBQUNDLGNBQWMsQ0FBRSwwQkFBMkIsQ0FBQztFQUFFRCxPQUFPLENBQUNFLEdBQUcsQ0FBRSxnQ0FBZ0MsRUFBRUgsWUFBYSxDQUFDO0VBQ25IOztFQUVDSSxrQ0FBa0MsQ0FBRSxFQUFHLENBQUM7RUFFeENDLDhDQUE4QyxDQUFDLENBQUM7O0VBRWhEO0VBQ0EsSUFBUUMsU0FBUyxJQUFJTixZQUFZLENBQUUsWUFBWSxDQUFFLElBQVEsQ0FBRU8sS0FBSyxDQUFDQyxPQUFPLENBQUVSLFlBQVksQ0FBRSxZQUFZLENBQUcsQ0FBRyxFQUFFO0lBQUs7O0lBRWhIQSxZQUFZLENBQUUsUUFBUSxDQUFFLEdBQUdTLHdCQUF3QixDQUFFVCxZQUFZLENBQUUsWUFBWSxDQUFFLEVBQUVVLHdCQUF3QixDQUFDQyxnQkFBZ0IsQ0FBRSxRQUFTLENBQUUsQ0FBQztFQUMzSTtFQUVBLElBQUlDLGtCQUFrQixHQUFHO0lBQ2xCQyxNQUFNLEVBQVksMEJBQTBCO0lBQzVDQyxLQUFLLEVBQWFKLHdCQUF3QixDQUFDQyxnQkFBZ0IsQ0FBRSxPQUFRLENBQUM7SUFDdEVJLGdCQUFnQixFQUFNVCxTQUFTLElBQUlOLFlBQVksQ0FBRSxTQUFTLENBQUUsR0FBS1Usd0JBQXdCLENBQUNDLGdCQUFnQixDQUFFLFNBQVUsQ0FBQyxHQUFHWCxZ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
