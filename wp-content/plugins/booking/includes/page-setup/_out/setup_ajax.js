"use strict";

// =====================================================================================================================
// == Ajax ==
// =====================================================================================================================
function wpbc_ajx__setup_wizard_page__send_request() {
  console.groupCollapsed('WPBC_AJX_SETUP_WIZARD_PAGE');
  console.log(' == Before Ajax Send - search_get_all_params() == ', _wpbc_settings.get_all_params__setup_wizard());

  // It can start 'icon spinning' on top menu bar at 'active menu item'.
  wpbc_setup_wizard_page_reload_button__spin_start();

  // Clear some parameters, which can make issue with blocking requests.
  wpbc_ajx__setup_wizard_page__do_request_clean();

  // Start Ajax
  jQuery.post(wpbc_url_ajax, {
    action: 'WPBC_AJX_SETUP_WIZARD_PAGE',
    wpbc_ajx_user_id: _wpbc_settings.get_param__secure('user_id'),
    nonce: _wpbc_settings.get_param__secure('nonce'),
    wpbc_ajx_locale: _wpbc_settings.get_param__secure('locale'),
    all_ajx_params: _wpbc_settings.get_all_params__setup_wizard()
  },
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    console.log(' == Response WPBC_AJX_SETUP_WIZARD_PAGE == ', response_data);
    console.groupEnd();

    // -------------------------------------------------------------------------------------------------
    // Probably Error
    // -------------------------------------------------------------------------------------------------
    if (typeof response_data !== 'object' || response_data === null) {
      wpbc_setup_wizard_page__hide_content();
      wpbc_setup_wizard_page__show_message(response_data);
      return;
    }

    // -------------------------------------------------------------------------------------------------
    // Reset Done - Reload page, after filter toolbar has been reset
    // -------------------------------------------------------------------------------------------------
    if (undefined != response_data['ajx_cleaned_params'] && 'reset_done' === response_data['ajx_cleaned_params']['do_action']) {
      location.reload();
      return;
    }

    // Define Front-End side JS vars from  Ajax
    _wpbc_settings.set_params_arr__setup_wizard(response_data['ajx_data']);

    // Update Menu statuses: Top Black UI and in Left Main menu
    wpbc_setup_wizard_page__update_steps_status(response_data['ajx_data']['steps_is_done']);
    if (wpbc_setup_wizard_page__is_all_steps_completed()) {
      if (undefined != response_data['ajx_data']['redirect_url']) {
        window.location.href = response_data['ajx_data']['redirect_url'];
        return;
      }
    }

    // -> Progress line at  "Left Main Menu"
    wpbc_setup_wizard_page__update_plugin_menu_progress(response_data['ajx_data']['plugin_menu__setup_progress']);

    // -------------------------------------------------------------------------------------------------
    // Show Main Content
    // -------------------------------------------------------------------------------------------------
    wpbc_setup_wizard_page__show_content();

    // -------------------------------------------------------------------------------------------------
    // Redefine Hooks, because we show new DOM elements
    // -------------------------------------------------------------------------------------------------
    wpbc_setup_wizard_page__define_ui_hooks();

    // Show Messages
    if ('' !== response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />")) {
      wpbc_admin_show_message(response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />"), '1' == response_data['ajx_data']['ajx_after_action_result'] ? 'success' : 'error', 10000);
    }

    // It can STOP 'icon spinning' on top menu bar at 'active menu item'
    wpbc_setup_wizard_page_reload_button__spin_pause();

    // Remove spin from "button with icon", that was clicked and Enable this button.
    wpbc_button__remove_spin(response_data['ajx_cleaned_params']['ui_clicked_element_id']);
    jQuery('#ajax_respond').html(response_data); // For ability to show response, add such DIV element to page
  }).fail(function (jqXHR, textStatus, errorThrown) {
    if (window.console && window.console.log) {
      console.log('Ajax_Error', jqXHR, textStatus, errorThrown);
    }
    var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown;
    if (jqXHR.status) {
      error_message += ' (<b>' + jqXHR.status + '</b>)';
      if (403 == jqXHR.status) {
        error_message += ' Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
      }
    }
    if (jqXHR.responseText) {
      error_message += ' ' + jqXHR.responseText;
    }
    error_message = error_message.replace(/\n/g, "<br />");

    // Hide Content
    wpbc_setup_wizard_page__hide_content();

    // Show Error Message
    wpbc_setup_wizard_page__show_message(error_message);
  })
  // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
  ; // End Ajax
}

/**
 * Clean some parameters,  does not required for request
 */
function wpbc_ajx__setup_wizard_page__do_request_clean() {
  // We donot require the 'calendar_force_load' parameter  with  all html and scripts  content at  server side. This content generated at server side.
  // It is also can be the reason of blocking request, because of script tags.
  _wpbc_settings.set_param__setup_wizard('calendar_force_load', '');
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1zZXR1cC9fb3V0L3NldHVwX2FqYXguanMiLCJuYW1lcyI6WyJ3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdCIsImNvbnNvbGUiLCJncm91cENvbGxhcHNlZCIsImxvZyIsIl93cGJjX3NldHRpbmdzIiwiZ2V0X2FsbF9wYXJhbXNfX3NldHVwX3dpemFyZCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCIsIndwYmNfYWp4X19zZXR1cF93aXphcmRfcGFnZV9fZG9fcmVxdWVzdF9jbGVhbiIsImpRdWVyeSIsInBvc3QiLCJ3cGJjX3VybF9hamF4IiwiYWN0aW9uIiwid3BiY19hanhfdXNlcl9pZCIsImdldF9wYXJhbV9fc2VjdXJlIiwibm9uY2UiLCJ3cGJjX2FqeF9sb2NhbGUiLCJhbGxfYWp4X3BhcmFtcyIsInJlc3BvbnNlX2RhdGEiLCJ0ZXh0U3RhdHVzIiwianFYSFIiLCJncm91cEVuZCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2hpZGVfY29udGVudCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3Nob3dfbWVzc2FnZSIsInVuZGVmaW5lZCIsImxvY2F0aW9uIiwicmVsb2FkIiwic2V0X3BhcmFtc19hcnJfX3NldHVwX3dpemFyZCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9zdGVwc19zdGF0dXMiLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX19pc19hbGxfc3RlcHNfY29tcGxldGVkIiwid2luZG93IiwiaHJlZiIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9wbHVnaW5fbWVudV9wcm9ncmVzcyIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3Nob3dfY29udGVudCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2RlZmluZV91aV9ob29rcyIsInJlcGxhY2UiLCJ3cGJjX2FkbWluX3Nob3dfbWVzc2FnZSIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSIsIndwYmNfYnV0dG9uX19yZW1vdmVfc3BpbiIsImh0bWwiLCJmYWlsIiwiZXJyb3JUaHJvd24iLCJlcnJvcl9tZXNzYWdlIiwic3RhdHVzIiwicmVzcG9uc2VUZXh0Iiwic2V0X3BhcmFtX19zZXR1cF93aXphcmQiXSwic291cmNlcyI6WyJpbmNsdWRlcy9wYWdlLXNldHVwL19zcmMvc2V0dXBfYWpheC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbi8vID09IEFqYXggPT1cclxuLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblxyXG5mdW5jdGlvbiB3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdCgpe1xyXG5cclxuY29uc29sZS5ncm91cENvbGxhcHNlZCggJ1dQQkNfQUpYX1NFVFVQX1dJWkFSRF9QQUdFJyApOyBjb25zb2xlLmxvZyggJyA9PSBCZWZvcmUgQWpheCBTZW5kIC0gc2VhcmNoX2dldF9hbGxfcGFyYW1zKCkgPT0gJyAsIF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKSApO1xyXG5cclxuXHQvLyBJdCBjYW4gc3RhcnQgJ2ljb24gc3Bpbm5pbmcnIG9uIHRvcCBtZW51IGJhciBhdCAnYWN0aXZlIG1lbnUgaXRlbScuXHJcblx0d3BiY19zZXR1cF93aXphcmRfcGFnZV9yZWxvYWRfYnV0dG9uX19zcGluX3N0YXJ0KCk7XHJcblxyXG5cdC8vIENsZWFyIHNvbWUgcGFyYW1ldGVycywgd2hpY2ggY2FuIG1ha2UgaXNzdWUgd2l0aCBibG9ja2luZyByZXF1ZXN0cy5cclxuXHR3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX2RvX3JlcXVlc3RfY2xlYW4oKTtcclxuXHJcblx0Ly8gU3RhcnQgQWpheFxyXG5cdGpRdWVyeS5wb3N0KCB3cGJjX3VybF9hamF4LFxyXG5cdFx0XHR7XHJcblx0XHRcdFx0YWN0aW9uICAgICAgICAgIDogJ1dQQkNfQUpYX1NFVFVQX1dJWkFSRF9QQUdFJyxcclxuXHRcdFx0XHR3cGJjX2FqeF91c2VyX2lkOiBfd3BiY19zZXR0aW5ncy5nZXRfcGFyYW1fX3NlY3VyZSggJ3VzZXJfaWQnICksXHJcblx0XHRcdFx0bm9uY2UgICAgICAgICAgIDogX3dwYmNfc2V0dGluZ3MuZ2V0X3BhcmFtX19zZWN1cmUoICdub25jZScgKSxcclxuXHRcdFx0XHR3cGJjX2FqeF9sb2NhbGUgOiBfd3BiY19zZXR0aW5ncy5nZXRfcGFyYW1fX3NlY3VyZSggJ2xvY2FsZScgKSxcclxuXHJcblx0XHRcdFx0YWxsX2FqeF9wYXJhbXMgIDogX3dwYmNfc2V0dGluZ3MuZ2V0X2FsbF9wYXJhbXNfX3NldHVwX3dpemFyZCgpXHJcblx0XHRcdH0sXHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBTIHUgYyBjIGUgcyBzXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSByZXNwb25zZV9kYXRhXHRcdC1cdGl0cyBvYmplY3QgcmV0dXJuZWQgZnJvbSAgQWpheCAtIGNsYXNzLWxpdmUtc2VhcmNnLnBocFxyXG5cdFx0XHQgKiBAcGFyYW0gdGV4dFN0YXR1c1x0XHQtXHQnc3VjY2VzcydcclxuXHRcdFx0ICogQHBhcmFtIGpxWEhSXHRcdFx0XHQtXHRPYmplY3RcclxuXHRcdFx0ICovXHJcblx0XHRcdGZ1bmN0aW9uICggcmVzcG9uc2VfZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7XHJcblxyXG5jb25zb2xlLmxvZyggJyA9PSBSZXNwb25zZSBXUEJDX0FKWF9TRVRVUF9XSVpBUkRfUEFHRSA9PSAnLCByZXNwb25zZV9kYXRhICk7IGNvbnNvbGUuZ3JvdXBFbmQoKTtcclxuXHJcblx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdC8vIFByb2JhYmx5IEVycm9yXHJcblx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdGlmICggKHR5cGVvZiByZXNwb25zZV9kYXRhICE9PSAnb2JqZWN0JykgfHwgKHJlc3BvbnNlX2RhdGEgPT09IG51bGwpICl7XHJcblxyXG5cdFx0XHRcdFx0d3BiY19zZXR1cF93aXphcmRfcGFnZV9faGlkZV9jb250ZW50KCk7XHJcblx0XHRcdFx0XHR3cGJjX3NldHVwX3dpemFyZF9wYWdlX19zaG93X21lc3NhZ2UoIHJlc3BvbnNlX2RhdGEgKTtcclxuXHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0Ly8gUmVzZXQgRG9uZSAtIFJlbG9hZCBwYWdlLCBhZnRlciBmaWx0ZXIgdG9vbGJhciBoYXMgYmVlbiByZXNldFxyXG5cdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRpZiAoICAoIHVuZGVmaW5lZCAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdICkgJiYgKCAncmVzZXRfZG9uZScgPT09IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bICdkb19hY3Rpb24nIF0gKSAgKXtcclxuXHRcdFx0XHRcdGxvY2F0aW9uLnJlbG9hZCgpO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gRGVmaW5lIEZyb250LUVuZCBzaWRlIEpTIHZhcnMgZnJvbSAgQWpheFxyXG5cdFx0XHRcdF93cGJjX3NldHRpbmdzLnNldF9wYXJhbXNfYXJyX19zZXR1cF93aXphcmQoIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXSApO1xyXG5cclxuXHRcdFx0XHQvLyBVcGRhdGUgTWVudSBzdGF0dXNlczogVG9wIEJsYWNrIFVJIGFuZCBpbiBMZWZ0IE1haW4gbWVudVxyXG5cdFx0XHRcdHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9zdGVwc19zdGF0dXMoIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsnc3RlcHNfaXNfZG9uZSddICk7XHJcblxyXG5cdFx0XHRcdGlmICggd3BiY19zZXR1cF93aXphcmRfcGFnZV9faXNfYWxsX3N0ZXBzX2NvbXBsZXRlZCgpICkge1xyXG5cdFx0XHRcdFx0aWYgKHVuZGVmaW5lZCAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdyZWRpcmVjdF91cmwnIF0pe1xyXG5cdFx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ3JlZGlyZWN0X3VybCcgXTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblxyXG5cdFx0XHRcdC8vIC0+IFByb2dyZXNzIGxpbmUgYXQgIFwiTGVmdCBNYWluIE1lbnVcIlxyXG5cdFx0XHRcdHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9wbHVnaW5fbWVudV9wcm9ncmVzcyggcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWydwbHVnaW5fbWVudV9fc2V0dXBfcHJvZ3Jlc3MnXSApO1xyXG5cclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0Ly8gU2hvdyBNYWluIENvbnRlbnRcclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0d3BiY19zZXR1cF93aXphcmRfcGFnZV9fc2hvd19jb250ZW50KCk7XHJcblxyXG5cdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHQvLyBSZWRlZmluZSBIb29rcywgYmVjYXVzZSB3ZSBzaG93IG5ldyBET00gZWxlbWVudHNcclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0d3BiY19zZXR1cF93aXphcmRfcGFnZV9fZGVmaW5lX3VpX2hvb2tzKCk7XHJcblxyXG5cdFx0XHRcdC8vIFNob3cgTWVzc2FnZXNcclxuXHRcdFx0XHRpZiAoICcnICE9PSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICkgKXtcclxuXHRcdFx0XHRcdHdwYmNfYWRtaW5fc2hvd19tZXNzYWdlKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsICggJzEnID09IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fcmVzdWx0JyBdICkgPyAnc3VjY2VzcycgOiAnZXJyb3InXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgMTAwMDBcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyBJdCBjYW4gU1RPUCAnaWNvbiBzcGlubmluZycgb24gdG9wIG1lbnUgYmFyIGF0ICdhY3RpdmUgbWVudSBpdGVtJ1xyXG5cdFx0XHRcdHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSgpO1xyXG5cclxuXHRcdFx0XHQvLyBSZW1vdmUgc3BpbiBmcm9tIFwiYnV0dG9uIHdpdGggaWNvblwiLCB0aGF0IHdhcyBjbGlja2VkIGFuZCBFbmFibGUgdGhpcyBidXR0b24uXHJcblx0XHRcdFx0d3BiY19idXR0b25fX3JlbW92ZV9zcGluKCByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWyAndWlfY2xpY2tlZF9lbGVtZW50X2lkJyBdIClcclxuXHJcblx0XHRcdFx0alF1ZXJ5KCAnI2FqYXhfcmVzcG9uZCcgKS5odG1sKCByZXNwb25zZV9kYXRhICk7XHRcdC8vIEZvciBhYmlsaXR5IHRvIHNob3cgcmVzcG9uc2UsIGFkZCBzdWNoIERJViBlbGVtZW50IHRvIHBhZ2VcclxuXHRcdFx0fVxyXG5cdFx0ICApLmZhaWwoIGZ1bmN0aW9uICgganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duICkgeyAgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ0FqYXhfRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKTsgfVxyXG5cclxuXHRcdFx0XHR2YXIgZXJyb3JfbWVzc2FnZSA9ICc8c3Ryb25nPicgKyAnRXJyb3IhJyArICc8L3N0cm9uZz4gJyArIGVycm9yVGhyb3duIDtcclxuXHRcdFx0XHRpZiAoIGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnICg8Yj4nICsganFYSFIuc3RhdHVzICsgJzwvYj4pJztcclxuXHRcdFx0XHRcdGlmICg0MDMgPT0ganFYSFIuc3RhdHVzICl7XHJcblx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0gJyBQcm9iYWJseSBub25jZSBmb3IgdGhpcyBwYWdlIGhhcyBiZWVuIGV4cGlyZWQuIFBsZWFzZSA8YSBocmVmPVwiamF2YXNjcmlwdDp2b2lkKDApXCIgb25jbGljaz1cImphdmFzY3JpcHQ6bG9jYXRpb24ucmVsb2FkKCk7XCI+cmVsb2FkIHRoZSBwYWdlPC9hPi4nO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRpZiAoIGpxWEhSLnJlc3BvbnNlVGV4dCApe1xyXG5cdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnICcgKyBqcVhIUi5yZXNwb25zZVRleHQ7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGVycm9yX21lc3NhZ2UgPSBlcnJvcl9tZXNzYWdlLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApO1xyXG5cclxuXHRcdFx0XHQvLyBIaWRlIENvbnRlbnRcclxuXHRcdFx0XHR3cGJjX3NldHVwX3dpemFyZF9wYWdlX19oaWRlX2NvbnRlbnQoKTtcclxuXHJcblx0XHRcdFx0Ly8gU2hvdyBFcnJvciBNZXNzYWdlXHJcblx0XHRcdFx0d3BiY19zZXR1cF93aXphcmRfcGFnZV9fc2hvd19tZXNzYWdlKCBlcnJvcl9tZXNzYWdlICk7XHJcblx0XHQgIH0pXHJcblx0XHQgIC8vIC5kb25lKCAgIGZ1bmN0aW9uICggZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdzZWNvbmQgc3VjY2VzcycsIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICk7IH0gICAgfSlcclxuXHRcdCAgLy8gLmFsd2F5cyggZnVuY3Rpb24gKCBkYXRhX2pxWEhSLCB0ZXh0U3RhdHVzLCBqcVhIUl9lcnJvclRocm93biApIHsgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ2Fsd2F5cyBmaW5pc2hlZCcsIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICk7IH0gICAgIH0pXHJcblx0XHQgIDsgIC8vIEVuZCBBamF4XHJcblxyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIENsZWFuIHNvbWUgcGFyYW1ldGVycywgIGRvZXMgbm90IHJlcXVpcmVkIGZvciByZXF1ZXN0XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX2RvX3JlcXVlc3RfY2xlYW4oKSB7XHJcblx0Ly8gV2UgZG9ub3QgcmVxdWlyZSB0aGUgJ2NhbGVuZGFyX2ZvcmNlX2xvYWQnIHBhcmFtZXRlciAgd2l0aCAgYWxsIGh0bWwgYW5kIHNjcmlwdHMgIGNvbnRlbnQgYXQgIHNlcnZlciBzaWRlLiBUaGlzIGNvbnRlbnQgZ2VuZXJhdGVkIGF0IHNlcnZlciBzaWRlLlxyXG5cdC8vIEl0IGlzIGFsc28gY2FuIGJlIHRoZSByZWFzb24gb2YgYmxvY2tpbmcgcmVxdWVzdCwgYmVjYXVzZSBvZiBzY3JpcHQgdGFncy5cclxuXHRfd3BiY19zZXR0aW5ncy5zZXRfcGFyYW1fX3NldHVwX3dpemFyZCgnY2FsZW5kYXJfZm9yY2VfbG9hZCcsICcnKTtcclxu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
