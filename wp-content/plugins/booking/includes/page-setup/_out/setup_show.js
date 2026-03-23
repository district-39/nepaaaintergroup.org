"use strict";

/**
 * Parameters usually  defined in   Ajax Response or Front-End 	for  == _wpbc_settings.get_all_params__setup_wizard():
 *
 * In 	Front-End side as  JavaScript 		::		wpbc_ajx__setup_wizard_page__send_request_with_params( {  'current_step': 'optional_other_settings', 'do_action': 'none', 'ui_clicked_element_id': 'btn__toolbar__buttons_prior'  } );
 *
 * After Ajax response in setup_ajax.js  as ::		_wpbc_settings.set_params_arr__setup_wizard( response_data[ 'ajx_data' ] );
 *
 */

// =====================================================================================================================
// ==  Set Request  for  Ajax  ==
// =====================================================================================================================
/**
 * Send Ajax Request 	after 	Updating Request Parameters
 *
 * @param params_arr
 *
 * 		Example 1:
 *
 * 			wpbc_ajx__setup_wizard_page__send_request_with_params( {
 *											'page_num': page_number
 *										} );
 * 		Example 2:
 *
 * 			wpbc_ajx__setup_wizard_page__send_request_with_params( {
 *											'current_step': '{{data.steps[ data.current_step ].prior}}',
 *											'do_action': 'none',
 *											'ui_clicked_element_id': 'btn__toolbar__buttons_prior'
 *										} );
 *
 */
function wpbc_ajx__setup_wizard_page__send_request_with_params(params_arr) {
  // Define Params Array 	to 	Request
  _wpbc_settings.set_params_arr__setup_wizard(params_arr);

  // Send Ajax Request
  wpbc_ajx__setup_wizard_page__send_request();
}
// Example 1:  wpbc_ajx__setup_wizard_page__send_request_with_params( {  'page_num': page_number  } );
// Example 2:  wpbc_ajx__setup_wizard_page__send_request_with_params( {  'current_step': 'optional_other_settings', 'do_action': 'none', 'ui_clicked_element_id': 'btn__toolbar__buttons_prior'  } );

// =====================================================================================================================
// == Show / Hide  Content ==
// =====================================================================================================================
/**
 * Show Main Content	...	_wpbc_settings.get_all_params__setup_wizard()  	-	must  be defined!
 */
function wpbc_setup_wizard_page__show_content() {
  var wpbc_template__stp_wiz__main_content = wp.template('wpbc_template__stp_wiz__main_content');
  jQuery(_wpbc_settings.get_param__other('container__main_content')).html(wpbc_template__stp_wiz__main_content(_wpbc_settings.get_all_params__setup_wizard()));

  // Hide 'Processing' Notice
  jQuery('.wpbc_processing.wpbc_spin').parent().parent().parent().parent('[id^="wpbc_notice_"]').hide();

  //var header_menu_text = ' Step ' + wpbc_setup_wizard_page__get_actual_step_number() + ' / ' + wpbc_setup_wizard_page__get_steps_count();
  //jQuery( '.wpbc_header_menu_tabs .nav-tab-active .nav-tab-text').html( header_menu_text );
  //
  //jQuery( '.wpbc_navigation_menu_left_item ' ).removeClass( 'wpbc_active' );
  //jQuery( '#' + _wpbc_settings.get_param__setup_wizard( 'current_step' ) ).addClass( 'wpbc_active' );

  // Recheck Full Screen  mode,  by  removing top tab
  wpbc_check_full_screen_mode();

  // Scroll to top
  // wpbc_scroll_to(  '.wpbc_page_top__header_tabs' );
  wpbc_scroll_to('.wpbc__container_place__steps_for_timeline');
}

/**
 * Hide Main Content
 */
function wpbc_setup_wizard_page__hide_content() {
  jQuery(_wpbc_settings.get_param__other('container__main_content')).html('');
}

/**
 * Update Plugin  menu progress   -> Progress line at  "Left Main Menu"
 */
function wpbc_setup_wizard_page__update_plugin_menu_progress(plugin_menu__setup_progress__html) {
  if ('undefined' != typeof plugin_menu__setup_progress__html) {
    jQuery('.setup_wizard_page_container').parent().html(plugin_menu__setup_progress__html);
  }
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  Steps Number Functions ==
// 					Gets data in   			_wpbc_settings.get_all_params__setup_wizard().steps
// 					which  defined in   	setup_ajax.php     															Ajax
// 					as 						$data_arr ['steps'] =  new WPBC_SETUP_WIZARD_STEPS();  $this->get_steps_arr();  			from 		setup_steps.php		structure.
// ---------------------------------------------------------------------------------------------------------------------

function wpbc_setup_wizard_page__get_steps_count() {
  var params_arr = _wpbc_settings.get_all_params__setup_wizard().steps;
  var steps_count = 0;
  _.each(params_arr, function (p_val, p_key, p_data) {
    steps_count++;
  });
  return steps_count;
}
function wpbc_setup_wizard_page__get_actual_step_number() {
  var params_arr = _wpbc_settings.get_all_params__setup_wizard().steps;
  var steps_finished = 1;
  _.each(params_arr, function (p_val, p_key, p_data) {
    if (p_val.is_done) {
      steps_finished++;
    }
  });
  return steps_finished;
}
function wpbc_setup_wizard_page__update_steps_status(steps_is_done_arr) {
  var params_arr = _wpbc_settings.get_all_params__setup_wizard().steps;
  _.each(steps_is_done_arr, function (p_val, p_key, p_data) {
    if ("undefined" !== typeof params_arr[p_key]) {
      params_arr[p_key].is_done = true === steps_is_done_arr[p_key];
    }
  });
  return params_arr;
}
function wpbc_setup_wizard_page__is_all_steps_completed() {
  var params_arr = _wpbc_settings.get_all_params__setup_wizard().steps;
  var status = true;
  _.each(params_arr, function (p_val, p_key, p_data) {
    if (!p_val.is_done) {
      status = false;
    }
  });
  return status;
}

/**
 * Define UI hooks for elements, after showing in Ajax.
 *
 * Because each  time,  when  we show content in Ajax, all Hooks needs re-defined.
 */
function wpbc_setup_wizard_page__define_ui_hooks() {
  // -----------------------------------------------------------------------------------------------------------------
  // Tooltips
  if ('function' === typeof wpbc_define_tippy_tooltips) {
    var parent_css_class = _wpbc_settings.get_param__other('container__main_content') + ' ';
    wpbc_define_tippy_tooltips(parent_css_class);
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Change Radio Containers
  jQuery('.wpbc_ui_radio_choice_input').on('change', function (event) {
    wpbc_ui_el__radio_container_selection(this);

    //wpbc_ajx__setup_wizard_page__send_request_with_params( {   'page_items_count': jQuery( this ).val(),   'page_num': 1   } );
  });
  jQuery('.wpbc_ui_radio_choice_input').each(function (index) {
    wpbc_ui_el__radio_container_selection(this);
  });

  // Define ability to click on Radio Containers (not only radio-buttons)
  jQuery('.wpbc_ui_radio_container').on('click', function (event) {
    wpbc_ui_el__radio_container_click(this);
  });

  // -----------------------------------------------------------------------------------------------------------------
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  M e s s a g e  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show message in content
 *
 * @param message				Message HTML
 * @param params = {
 *                   ['type']				'warning' | 'info' | 'error' | 'success'		default: 'warning'
 *                   ['container']			'.wpbc_ajx_cstm__section_left'		default: _wpbc_settings.get_param__other( 'container__main_content')
 *                   ['is_append']			true | false						default: true
 *				   }
 * Example:
 * 			var html_id = wpbc_setup_wizard_page__show_message( 'You can test days selection in calendar', 'info', '.wpbc_ajx_cstm__section_left', true );
 *
 *
 * @returns string  - HTML ID
 */
function wpbc_setup_wizard_page__show_message(message, params = {}) {
  var params_default = {
    'type': 'warning',
    'container': _wpbc_settings.get_param__other('container__main_content'),
    'is_append': true,
    'style': 'text-align:left;',
    'delay': 0
  };
  _.each(params, function (p_val, p_key, p_data) {
    params_default[p_key] = p_val;
  });
  params = params_default;
  var unique_div_id = new Date();
  unique_div_id = 'wpbc_notice_' + unique_div_id.getTime();
  var alert_class = 'notice ';
  if (params['type'] == 'error') {
    alert_class += 'notice-error ';
    message = '<i style="margin-right: 0.5em;color: #d63638;" class="menu_icon icon-1x wpbc_icn_report_gmailerrorred"></i>' + message;
  }
  if (params['type'] == 'warning') {
    alert_class += 'notice-warning ';
    message = '<i style="margin-right: 0.5em;color: #e9aa04;" class="menu_icon icon-1x wpbc_icn_warning"></i>' + message;
  }
  if (params['type'] == 'info') {
    alert_class += 'notice-info ';
  }
  if (params['type'] == 'success') {
    alert_class += 'notice-info alert-success updated ';
    message = '<i style="margin-right: 0.5em;color: #64aa45;" class="menu_icon icon-1x wpbc_icn_done_outline"></i>' + message;
  }
  message = '<div id="' + unique_div_id + '" class="wpbc-settings-notice ' + alert_class + '" style="' + params['style'] + '">' + message + '</div>';
  if (params['is_append']) {
    jQuery(params['container']).append(message);
  } else {
    jQuery(params['container']).html(message);
  }
  params['delay'] = parseInt(params['delay']);
  if (params['delay'] > 0) {
    var closed_timer = setTimeout(function () {
      jQuery('#' + unique_div_id).fadeOut(1500);
    }, params['delay']);
  }
  return unique_div_id;
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  Support Functions - Spin Icon in Top Bar Menu -> '  Initial Setup'  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Spin button in Filter toolbar  -  Start
 */
function wpbc_setup_wizard_page_reload_button__spin_start() {
  return false; // Currently  disabled,  maybe activate it for some other element.
  jQuery('#wpbc_initial_setup_top_menu_item .menu_icon.wpbc_spin').removeClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  Pause
 */
function wpbc_setup_wizard_page_reload_button__spin_pause() {
  jQuery('#wpbc_initial_setup_top_menu_item .menu_icon.wpbc_spin').addClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  is Spinning ?
 *
 * @returns {boolean}
 */
function wpbc_setup_wizard_page_reload_button__is_spin() {
  if (jQuery('#wpbc_initial_setup_top_menu_item .menu_icon.wpbc_spin').hasClass('wpbc_animation_pause')) {
    return true;
  } else {
    return false;
  }
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1zZXR1cC9fb3V0L3NldHVwX3Nob3cuanMiLCJuYW1lcyI6WyJ3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcyIsInBhcmFtc19hcnIiLCJfd3BiY19zZXR0aW5ncyIsInNldF9wYXJhbXNfYXJyX19zZXR1cF93aXphcmQiLCJ3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdCIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3Nob3dfY29udGVudCIsIndwYmNfdGVtcGxhdGVfX3N0cF93aXpfX21haW5fY29udGVudCIsIndwIiwidGVtcGxhdGUiLCJqUXVlcnkiLCJnZXRfcGFyYW1fX290aGVyIiwiaHRtbCIsImdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQiLCJwYXJlbnQiLCJoaWRlIiwid3BiY19jaGVja19mdWxsX3NjcmVlbl9tb2RlIiwid3BiY19zY3JvbGxfdG8iLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX19oaWRlX2NvbnRlbnQiLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX191cGRhdGVfcGx1Z2luX21lbnVfcHJvZ3Jlc3MiLCJwbHVnaW5fbWVudV9fc2V0dXBfcHJvZ3Jlc3NfX2h0bWwiLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX19nZXRfc3RlcHNfY291bnQiLCJzdGVwcyIsInN0ZXBzX2NvdW50IiwiXyIsImVhY2giLCJwX3ZhbCIsInBfa2V5IiwicF9kYXRhIiwid3BiY19zZXR1cF93aXphcmRfcGFnZV9fZ2V0X2FjdHVhbF9zdGVwX251bWJlciIsInN0ZXBzX2ZpbmlzaGVkIiwiaXNfZG9uZSIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9zdGVwc19zdGF0dXMiLCJzdGVwc19pc19kb25lX2FyciIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2lzX2FsbF9zdGVwc19jb21wbGV0ZWQiLCJzdGF0dXMiLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX19kZWZpbmVfdWlfaG9va3MiLCJ3cGJjX2RlZmluZV90aXBweV90b29sdGlwcyIsInBhcmVudF9jc3NfY2xhc3MiLCJvbiIsImV2ZW50Iiwid3BiY191aV9lbF9fcmFkaW9fY29udGFpbmVyX3NlbGVjdGlvbiIsImluZGV4Iiwid3BiY191aV9lbF9fcmFkaW9fY29udGFpbmVyX2NsaWNrIiwid3BiY19zZXR1cF93aXphcmRfcGFnZV9fc2hvd19tZXNzYWdlIiwibWVzc2FnZSIsInBhcmFtcyIsInBhcmFtc19kZWZhdWx0IiwidW5pcXVlX2Rpdl9pZCIsIkRhdGUiLCJnZXRUaW1lIiwiYWxlcnRfY2xhc3MiLCJhcHBlbmQiLCJwYXJzZUludCIsImNsb3NlZF90aW1lciIsInNldFRpbWVvdXQiLCJmYWRlT3V0Iiwid3BiY19zZXR1cF93aXphcmRfcGFnZV9yZWxvYWRfYnV0dG9uX19zcGluX3N0YXJ0IiwicmVtb3ZlQ2xhc3MiLCJ3cGJjX3NldHVwX3dpemFyZF9wYWdlX3JlbG9hZF9idXR0b25fX3NwaW5fcGF1c2UiLCJhZGRDbGFzcyIsIndwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9faXNfc3BpbiIsImhhc0NsYXNzIl0sInNvdXJjZXMiOlsiaW5jbHVkZXMvcGFnZS1zZXR1cC9fc3JjL3NldHVwX3Nob3cuanMiXSwic291cmNlc0NvbnRlbnQiOlsiXCJ1c2Ugc3RyaWN0XCI7XHJcblxyXG4vKipcclxuICogUGFyYW1ldGVycyB1c3VhbGx5ICBkZWZpbmVkIGluICAgQWpheCBSZXNwb25zZSBvciBGcm9udC1FbmQgXHRmb3IgID09IF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKTpcclxuICpcclxuICogSW4gXHRGcm9udC1FbmQgc2lkZSBhcyAgSmF2YVNjcmlwdCBcdFx0OjpcdFx0d3BiY19hanhfX3NldHVwX3dpemFyZF9wYWdlX19zZW5kX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHsgICdjdXJyZW50X3N0ZXAnOiAnb3B0aW9uYWxfb3RoZXJfc2V0dGluZ3MnLCAnZG9fYWN0aW9uJzogJ25vbmUnLCAndWlfY2xpY2tlZF9lbGVtZW50X2lkJzogJ2J0bl9fdG9vbGJhcl9fYnV0dG9uc19wcmlvcicgIH0gKTtcclxuICpcclxuICogQWZ0ZXIgQWpheCByZXNwb25zZSBpbiBzZXR1cF9hamF4LmpzICBhcyA6Olx0XHRfd3BiY19zZXR0aW5ncy5zZXRfcGFyYW1zX2Fycl9fc2V0dXBfd2l6YXJkKCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF0gKTtcclxuICpcclxuICovXHJcblxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLy8gPT0gIFNldCBSZXF1ZXN0ICBmb3IgIEFqYXggID09XHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4vKipcclxuICogU2VuZCBBamF4IFJlcXVlc3QgXHRhZnRlciBcdFVwZGF0aW5nIFJlcXVlc3QgUGFyYW1ldGVyc1xyXG4gKlxyXG4gKiBAcGFyYW0gcGFyYW1zX2FyclxyXG4gKlxyXG4gKiBcdFx0RXhhbXBsZSAxOlxyXG4gKlxyXG4gKiBcdFx0XHR3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG4gKlx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncGFnZV9udW0nOiBwYWdlX251bWJlclxyXG4gKlx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG4gKiBcdFx0RXhhbXBsZSAyOlxyXG4gKlxyXG4gKiBcdFx0XHR3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG4gKlx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnY3VycmVudF9zdGVwJzogJ3t7ZGF0YS5zdGVwc1sgZGF0YS5jdXJyZW50X3N0ZXAgXS5wcmlvcn19JyxcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2RvX2FjdGlvbic6ICdub25lJyxcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VpX2NsaWNrZWRfZWxlbWVudF9pZCc6ICdidG5fX3Rvb2xiYXJfX2J1dHRvbnNfcHJpb3InXHJcbiAqXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcbiAqXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcyAoIHBhcmFtc19hcnIgKXtcclxuXHJcblx0Ly8gRGVmaW5lIFBhcmFtcyBBcnJheSBcdHRvIFx0UmVxdWVzdFxyXG5cdF93cGJjX3NldHRpbmdzLnNldF9wYXJhbXNfYXJyX19zZXR1cF93aXphcmQoIHBhcmFtc19hcnIgKTtcclxuXHJcblx0Ly8gU2VuZCBBamF4IFJlcXVlc3RcclxuXHR3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdCgpO1xyXG59XHJcbi8vIEV4YW1wbGUgMTogIHdwYmNfYWp4X19zZXR1cF93aXphcmRfcGFnZV9fc2VuZF9yZXF1ZXN0X3dpdGhfcGFyYW1zKCB7ICAncGFnZV9udW0nOiBwYWdlX251bWJlciAgfSApO1xyXG4vLyBFeGFtcGxlIDI6ICB3cGJjX2FqeF9fc2V0dXBfd2l6YXJkX3BhZ2VfX3NlbmRfcmVxdWVzdF93aXRoX3BhcmFtcyggeyAgJ2N1cnJlbnRfc3RlcCc6ICdvcHRpb25hbF9vdGhlcl9zZXR0aW5ncycsICdkb19hY3Rpb24nOiAnbm9uZScsICd1aV9jbGlja2VkX2VsZW1lbnRfaWQnOiAnYnRuX190b29sYmFyX19idXR0b25zX3ByaW9yJyAgfSApO1xyXG5cclxuXHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4vLyA9PSBTaG93IC8gSGlkZSAgQ29udGVudCA9PVxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLyoqXHJcbiAqIFNob3cgTWFpbiBDb250ZW50XHQuLi5cdF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKSAgXHQtXHRtdXN0ICBiZSBkZWZpbmVkIVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zZXR1cF93aXphcmRfcGFnZV9fc2hvd19jb250ZW50KCkge1xyXG5cclxuXHR2YXIgd3BiY190ZW1wbGF0ZV9fc3RwX3dpel9fbWFpbl9jb250ZW50ID0gd3AudGVtcGxhdGUoICd3cGJjX3RlbXBsYXRlX19zdHBfd2l6X19tYWluX2NvbnRlbnQnICk7XHJcblxyXG5cdGpRdWVyeSggX3dwYmNfc2V0dGluZ3MuZ2V0X3BhcmFtX19vdGhlciggJ2NvbnRhaW5lcl9fbWFpbl9jb250ZW50JyApICkuaHRtbCggICB3cGJjX3RlbXBsYXRlX19zdHBfd2l6X19tYWluX2NvbnRlbnQoIF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKSApICAgKTtcclxuXHJcblx0Ly8gSGlkZSAnUHJvY2Vzc2luZycgTm90aWNlXHJcblx0alF1ZXJ5KCAnLndwYmNfcHJvY2Vzc2luZy53cGJjX3NwaW4nKS5wYXJlbnQoKS5wYXJlbnQoKS5wYXJlbnQoKS5wYXJlbnQoICdbaWRePVwid3BiY19ub3RpY2VfXCJdJyApLmhpZGUoKTtcclxuXHJcblx0Ly92YXIgaGVhZGVyX21lbnVfdGV4dCA9ICcgU3RlcCAnICsgd3BiY19zZXR1cF93aXphcmRfcGFnZV9fZ2V0X2FjdHVhbF9zdGVwX251bWJlcigpICsgJyAvICcgKyB3cGJjX3NldHVwX3dpemFyZF9wYWdlX19nZXRfc3RlcHNfY291bnQoKTtcclxuXHQvL2pRdWVyeSggJy53cGJjX2hlYWRlcl9tZW51X3RhYnMgLm5hdi10YWItYWN0aXZlIC5uYXYtdGFiLXRleHQnKS5odG1sKCBoZWFkZXJfbWVudV90ZXh0ICk7XHJcblx0Ly9cclxuXHQvL2pRdWVyeSggJy53cGJjX25hdmlnYXRpb25fbWVudV9sZWZ0X2l0ZW0gJyApLnJlbW92ZUNsYXNzKCAnd3BiY19hY3RpdmUnICk7XHJcblx0Ly9qUXVlcnkoICcjJyArIF93cGJjX3NldHRpbmdzLmdldF9wYXJhbV9fc2V0dXBfd2l6YXJkKCAnY3VycmVudF9zdGVwJyApICkuYWRkQ2xhc3MoICd3cGJjX2FjdGl2ZScgKTtcclxuXHJcblx0Ly8gUmVjaGVjayBGdWxsIFNjcmVlbiAgbW9kZSwgIGJ5ICByZW1vdmluZyB0b3AgdGFiXHJcblx0d3BiY19jaGVja19mdWxsX3NjcmVlbl9tb2RlKCk7XHJcblxyXG5cdC8vIFNjcm9sbCB0byB0b3BcclxuXHQvLyB3cGJjX3Njcm9sbF90byggICcud3BiY19wYWdlX3RvcF9faGVhZGVyX3RhYnMnICk7XHJcblx0d3BiY19zY3JvbGxfdG8oICAnLndwYmNfX2NvbnRhaW5lcl9wbGFjZV9fc3RlcHNfZm9yX3RpbWVsaW5lJyApO1xyXG59XHJcblxyXG4vKipcclxuICogSGlkZSBNYWluIENvbnRlbnRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2hpZGVfY29udGVudCgpe1xyXG5cclxuXHRqUXVlcnkoIF93cGJjX3NldHRpbmdzLmdldF9wYXJhbV9fb3RoZXIoICdjb250YWluZXJfX21haW5fY29udGVudCcgKSApLmh0bWwoICAnJyApO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFVwZGF0ZSBQbHVnaW4gIG1lbnUgcHJvZ3Jlc3MgICAtPiBQcm9ncmVzcyBsaW5lIGF0ICBcIkxlZnQgTWFpbiBNZW51XCJcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9wbHVnaW5fbWVudV9wcm9ncmVzcyggcGx1Z2luX21lbnVfX3NldHVwX3Byb2dyZXNzX19odG1sICl7XHJcblx0aWYgKCAndW5kZWZpbmVkJyAhPSB0eXBlb2YgKHBsdWdpbl9tZW51X19zZXR1cF9wcm9ncmVzc19faHRtbCkgKXtcclxuXHRcdGpRdWVyeSggJy5zZXR1cF93aXphcmRfcGFnZV9jb250YWluZXInICkucGFyZW50KCkuaHRtbCggcGx1Z2luX21lbnVfX3NldHVwX3Byb2dyZXNzX19odG1sICk7XHJcblx0fVxyXG59XHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gIFN0ZXBzIE51bWJlciBGdW5jdGlvbnMgPT1cclxuLy8gXHRcdFx0XHRcdEdldHMgZGF0YSBpbiAgIFx0XHRcdF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKS5zdGVwc1xyXG4vLyBcdFx0XHRcdFx0d2hpY2ggIGRlZmluZWQgaW4gICBcdHNldHVwX2FqYXgucGhwICAgICBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRBamF4XHJcbi8vIFx0XHRcdFx0XHRhcyBcdFx0XHRcdFx0XHQkZGF0YV9hcnIgWydzdGVwcyddID0gIG5ldyBXUEJDX1NFVFVQX1dJWkFSRF9TVEVQUygpOyAgJHRoaXMtPmdldF9zdGVwc19hcnIoKTsgIFx0XHRcdGZyb20gXHRcdHNldHVwX3N0ZXBzLnBocFx0XHRzdHJ1Y3R1cmUuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuZnVuY3Rpb24gd3BiY19zZXR1cF93aXphcmRfcGFnZV9fZ2V0X3N0ZXBzX2NvdW50KCkge1xyXG5cclxuXHR2YXIgcGFyYW1zX2FyciA9IF93cGJjX3NldHRpbmdzLmdldF9hbGxfcGFyYW1zX19zZXR1cF93aXphcmQoKS5zdGVwc1xyXG5cdHZhciBzdGVwc19jb3VudCA9IDBcclxuXHRfLmVhY2goIHBhcmFtc19hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKSB7XHJcblx0XHRzdGVwc19jb3VudCsrO1xyXG5cdH0gKTtcclxuXHRyZXR1cm4gc3RlcHNfY291bnQ7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2dldF9hY3R1YWxfc3RlcF9udW1iZXIoKSB7XHJcblxyXG5cdHZhciBwYXJhbXNfYXJyID0gX3dwYmNfc2V0dGluZ3MuZ2V0X2FsbF9wYXJhbXNfX3NldHVwX3dpemFyZCgpLnN0ZXBzXHJcblx0dmFyIHN0ZXBzX2ZpbmlzaGVkID0gMVxyXG5cdF8uZWFjaCggcGFyYW1zX2FyciwgZnVuY3Rpb24gKCBwX3ZhbCwgcF9rZXksIHBfZGF0YSApIHtcclxuXHRcdGlmICggcF92YWwuaXNfZG9uZSApe1xyXG5cdFx0XHRzdGVwc19maW5pc2hlZCsrO1xyXG5cdFx0fVxyXG5cdH0gKTtcclxuXHRyZXR1cm4gc3RlcHNfZmluaXNoZWQ7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX3VwZGF0ZV9zdGVwc19zdGF0dXMoIHN0ZXBzX2lzX2RvbmVfYXJyICl7XHJcblxyXG5cdHZhciBwYXJhbXNfYXJyID0gX3dwYmNfc2V0dGluZ3MuZ2V0X2FsbF9wYXJhbXNfX3NldHVwX3dpemFyZCgpLnN0ZXBzXHJcblxyXG5cdF8uZWFjaCggc3RlcHNfaXNfZG9uZV9hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKSB7XHJcblx0XHRpZiAoIFwidW5kZWZpbmVkXCIgIT09IHR5cGVvZiAoIHBhcmFtc19hcnJbIHBfa2V5IF0gKSApIHtcclxuXHRcdFx0cGFyYW1zX2FyclsgcF9rZXkgXS5pc19kb25lID0gKHRydWUgPT09IHN0ZXBzX2lzX2RvbmVfYXJyWyBwX2tleSBdKTtcclxuXHRcdH1cclxuXHR9ICk7XHJcblxyXG5cdHJldHVybiBwYXJhbXNfYXJyO1xyXG5cclxufVxyXG5cclxuXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfX2lzX2FsbF9zdGVwc19jb21wbGV0ZWQoKXtcclxuXHJcblx0dmFyIHBhcmFtc19hcnIgPSBfd3BiY19zZXR0aW5ncy5nZXRfYWxsX3BhcmFtc19fc2V0dXBfd2l6YXJkKCkuc3RlcHNcclxuXHR2YXIgc3RhdHVzID0gdHJ1ZTtcclxuXHJcblx0Xy5lYWNoKCBwYXJhbXNfYXJyLCBmdW5jdGlvbiAoIHBfdmFsLCBwX2tleSwgcF9kYXRhICkge1xyXG5cdFx0aWYgKCAhIHBfdmFsLmlzX2RvbmUgKXtcclxuXHRcdFx0c3RhdHVzID0gZmFsc2U7XHJcblx0XHR9XHJcblx0fSApO1xyXG5cclxuXHRyZXR1cm4gc3RhdHVzO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIERlZmluZSBVSSBob29rcyBmb3IgZWxlbWVudHMsIGFmdGVyIHNob3dpbmcgaW4gQWpheC5cclxuICpcclxuICogQmVjYXVzZSBlYWNoICB0aW1lLCAgd2hlbiAgd2Ugc2hvdyBjb250ZW50IGluIEFqYXgsIGFsbCBIb29rcyBuZWVkcyByZS1kZWZpbmVkLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zZXR1cF93aXphcmRfcGFnZV9fZGVmaW5lX3VpX2hvb2tzKCl7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVG9vbHRpcHNcclxuXHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiggd3BiY19kZWZpbmVfdGlwcHlfdG9vbHRpcHMgKSApIHtcclxuXHRcdHZhciBwYXJlbnRfY3NzX2NsYXNzID0gIF93cGJjX3NldHRpbmdzLmdldF9wYXJhbV9fb3RoZXIoICdjb250YWluZXJfX21haW5fY29udGVudCcgKSAgKyAnICdcclxuXHRcdHdwYmNfZGVmaW5lX3RpcHB5X3Rvb2x0aXBzKCBwYXJlbnRfY3NzX2NsYXNzICk7XHJcblx0fVxyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIENoYW5nZSBSYWRpbyBDb250YWluZXJzXHJcblx0alF1ZXJ5KCAnLndwYmNfdWlfcmFkaW9fY2hvaWNlX2lucHV0JyApLm9uKCAnY2hhbmdlJywgZnVuY3Rpb24oIGV2ZW50ICl7XHJcblxyXG5cdFx0d3BiY191aV9lbF9fcmFkaW9fY29udGFpbmVyX3NlbGVjdGlvbiggdGhpcyApO1xyXG5cclxuXHRcdC8vd3BiY19hanhfX3NldHVwX3dpemFyZF9wYWdlX19zZW5kX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHsgICAncGFnZV9pdGVtc19jb3VudCc6IGpRdWVyeSggdGhpcyApLnZhbCgpLCAgICdwYWdlX251bSc6IDEgICB9ICk7XHJcblx0fSApO1xyXG5cclxuXHRqUXVlcnkoICcud3BiY191aV9yYWRpb19jaG9pY2VfaW5wdXQnICkuZWFjaChmdW5jdGlvbiAoaW5kZXggKXtcclxuXHRcdHdwYmNfdWlfZWxfX3JhZGlvX2NvbnRhaW5lcl9zZWxlY3Rpb24oIHRoaXMgKTtcclxuXHR9KTtcclxuXHJcblx0Ly8gRGVmaW5lIGFiaWxpdHkgdG8gY2xpY2sgb24gUmFkaW8gQ29udGFpbmVycyAobm90IG9ubHkgcmFkaW8tYnV0dG9ucylcclxuXHRqUXVlcnkoICcud3BiY191aV9yYWRpb19jb250YWluZXInICkub24oICdjbGljaycsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cdFx0d3BiY191aV9lbF9fcmFkaW9fY29udGFpbmVyX2NsaWNrKCB0aGlzICk7XHJcblx0fSApO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHJcbn1cclxuXHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gIE0gZSBzIHMgYSBnIGUgID09XHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuLyoqXHJcbiAqIFNob3cgbWVzc2FnZSBpbiBjb250ZW50XHJcbiAqXHJcbiAqIEBwYXJhbSBtZXNzYWdlXHRcdFx0XHRNZXNzYWdlIEhUTUxcclxuICogQHBhcmFtIHBhcmFtcyA9IHtcclxuICogICAgICAgICAgICAgICAgICAgWyd0eXBlJ11cdFx0XHRcdCd3YXJuaW5nJyB8ICdpbmZvJyB8ICdlcnJvcicgfCAnc3VjY2VzcydcdFx0ZGVmYXVsdDogJ3dhcm5pbmcnXHJcbiAqICAgICAgICAgICAgICAgICAgIFsnY29udGFpbmVyJ11cdFx0XHQnLndwYmNfYWp4X2NzdG1fX3NlY3Rpb25fbGVmdCdcdFx0ZGVmYXVsdDogX3dwYmNfc2V0dGluZ3MuZ2V0X3BhcmFtX19vdGhlciggJ2NvbnRhaW5lcl9fbWFpbl9jb250ZW50JylcclxuICogICAgICAgICAgICAgICAgICAgWydpc19hcHBlbmQnXVx0XHRcdHRydWUgfCBmYWxzZVx0XHRcdFx0XHRcdGRlZmF1bHQ6IHRydWVcclxuICpcdFx0XHRcdCAgIH1cclxuICogRXhhbXBsZTpcclxuICogXHRcdFx0dmFyIGh0bWxfaWQgPSB3cGJjX3NldHVwX3dpemFyZF9wYWdlX19zaG93X21lc3NhZ2UoICdZb3UgY2FuIHRlc3QgZGF5cyBzZWxlY3Rpb24gaW4gY2FsZW5kYXInLCAnaW5mbycsICcud3BiY19hanhfY3N0bV9fc2VjdGlvbl9sZWZ0JywgdHJ1ZSApO1xyXG4gKlxyXG4gKlxyXG4gKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zZXR1cF93aXphcmRfcGFnZV9fc2hvd19tZXNzYWdlKCBtZXNzYWdlLCBwYXJhbXMgPSB7fSApe1xyXG5cclxuXHR2YXIgcGFyYW1zX2RlZmF1bHQgPSB7XHJcblx0XHRcdFx0XHRcdFx0XHQndHlwZScgICAgIDogJ3dhcm5pbmcnLFxyXG5cdFx0XHRcdFx0XHRcdFx0J2NvbnRhaW5lcic6IF93cGJjX3NldHRpbmdzLmdldF9wYXJhbV9fb3RoZXIoICdjb250YWluZXJfX21haW5fY29udGVudCcpLFxyXG5cdFx0XHRcdFx0XHRcdFx0J2lzX2FwcGVuZCc6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHQnc3R5bGUnICAgIDogJ3RleHQtYWxpZ246bGVmdDsnLFxyXG5cdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICA6IDBcclxuXHRcdFx0XHRcdFx0XHR9O1xyXG5cdF8uZWFjaCggcGFyYW1zLCBmdW5jdGlvbiAoIHBfdmFsLCBwX2tleSwgcF9kYXRhICl7XHJcblx0XHRwYXJhbXNfZGVmYXVsdFsgcF9rZXkgXSA9IHBfdmFsO1xyXG5cdH0gKTtcclxuXHRwYXJhbXMgPSBwYXJhbXNfZGVmYXVsdDtcclxuXHJcbiAgICB2YXIgdW5pcXVlX2Rpdl9pZCA9IG5ldyBEYXRlKCk7XHJcbiAgICB1bmlxdWVfZGl2X2lkID0gJ3dwYmNfbm90aWNlXycgKyB1bmlxdWVfZGl2X2lkLmdldFRpbWUoKTtcclxuXHJcblx0dmFyIGFsZXJ0X2NsYXNzID0gJ25vdGljZSAnO1xyXG5cdGlmICggcGFyYW1zWyd0eXBlJ10gPT0gJ2Vycm9yJyApe1xyXG5cdFx0YWxlcnRfY2xhc3MgKz0gJ25vdGljZS1lcnJvciAnO1xyXG5cdFx0bWVzc2FnZSA9ICc8aSBzdHlsZT1cIm1hcmdpbi1yaWdodDogMC41ZW07Y29sb3I6ICNkNjM2Mzg7XCIgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9yZXBvcnRfZ21haWxlcnJvcnJlZFwiPjwvaT4nICsgbWVzc2FnZTtcclxuXHR9XHJcblx0aWYgKCBwYXJhbXNbJ3R5cGUnXSA9PSAnd2FybmluZycgKXtcclxuXHRcdGFsZXJ0X2NsYXNzICs9ICdub3RpY2Utd2FybmluZyAnO1xyXG5cdFx0bWVzc2FnZSA9ICc8aSBzdHlsZT1cIm1hcmdpbi1yaWdodDogMC41ZW07Y29sb3I6ICNlOWFhMDQ7XCIgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl93YXJuaW5nXCI+PC9pPicgKyBtZXNzYWdlO1xyXG5cdH1cclxuXHRpZiAoIHBhcmFtc1sndHlwZSddID09ICdpbmZvJyApe1xyXG5cdFx0YWxlcnRfY2xhc3MgKz0gJ25vdGljZS1pbmZvICc7XHJcblx0fVxyXG5cdGlmICggcGFyYW1zWyd0eXBlJ10gPT0gJ3N1Y2Nlc3MnICl7XHJcblx0XHRhbGVydF9jbGFzcyArPSAnbm90aWNlLWluZm8gYWxlcnQtc3VjY2VzcyB1cGRhdGVkICc7XHJcblx0XHRtZXNzYWdlID0gJzxpIHN0eWxlPVwibWFyZ2luLXJpZ2h0OiAwLjVlbTtjb2xvcjogIzY0YWE0NTtcIiBjbGFzcz1cIm1lbnVfaWNvbiBpY29uLTF4IHdwYmNfaWNuX2RvbmVfb3V0bGluZVwiPjwvaT4nICsgbWVzc2FnZTtcclxuXHR9XHJcblxyXG5cdG1lc3NhZ2UgPSAnPGRpdiBpZD1cIicgKyB1bmlxdWVfZGl2X2lkICsgJ1wiIGNsYXNzPVwid3BiYy1zZXR0aW5ncy1ub3RpY2UgJyArIGFsZXJ0X2NsYXNzICsgJ1wiIHN0eWxlPVwiJyArIHBhcmFtc1sgJ3N0eWxlJyBdICsgJ1wiPicgKyBtZXNzYWdlICsgJzwvZGl2Pic7XHJcblxyXG5cdGlmICggcGFyYW1zWydpc19hcHBlbmQnXSApe1xyXG5cdFx0alF1ZXJ5KCBwYXJhbXNbJ2NvbnRhaW5lciddICkuYXBwZW5kKCBtZXNzYWdlICk7XHJcblx0fSBlbHNlIHtcclxuXHRcdGpRdWVyeSggcGFyYW1zWydjb250YWluZXInXSApLmh0bWwoIG1lc3NhZ2UgKTtcclxuXHR9XHJcblxyXG5cdHBhcmFtc1snZGVsYXknXSA9IHBhcnNlSW50KCBwYXJhbXNbJ2RlbGF5J10gKTtcclxuXHRpZiAoIHBhcmFtc1snZGVsYXknXSA+IDAgKXtcclxuXHJcblx0XHR2YXIgY2xvc2VkX3RpbWVyID0gc2V0VGltZW91dCggZnVuY3Rpb24gKCl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0alF1ZXJ5KCAnIycgKyB1bmlxdWVfZGl2X2lkICkuZmFkZU91dCggMTUwMCApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgcGFyYW1zWyAnZGVsYXknIF1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCApO1xyXG5cdH1cclxuXHRyZXR1cm4gdW5pcXVlX2Rpdl9pZDtcclxufVxyXG5cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vLyA9PSAgU3VwcG9ydCBGdW5jdGlvbnMgLSBTcGluIEljb24gaW4gVG9wIEJhciBNZW51IC0+ICcgIEluaXRpYWwgU2V0dXAnICA9PVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcbi8qKlxyXG4gKiBTcGluIGJ1dHRvbiBpbiBGaWx0ZXIgdG9vbGJhciAgLSAgU3RhcnRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCgpe1xyXG5cdHJldHVybiBmYWxzZTsgLy8gQ3VycmVudGx5ICBkaXNhYmxlZCwgIG1heWJlIGFjdGl2YXRlIGl0IGZvciBzb21lIG90aGVyIGVsZW1lbnQuXHJcblx0alF1ZXJ5KCAnI3dwYmNfaW5pdGlhbF9zZXR1cF90b3BfbWVudV9pdGVtIC5tZW51X2ljb24ud3BiY19zcGluJykucmVtb3ZlQ2xhc3MoICd3cGJjX2FuaW1hdGlvbl9wYXVzZScgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFNwaW4gYnV0dG9uIGluIEZpbHRlciB0b29sYmFyICAtICBQYXVzZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zZXR1cF93aXphcmRfcGFnZV9yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlKCl7XHJcblx0alF1ZXJ5KCAnI3dwYmNfaW5pdGlhbF9zZXR1cF90b3BfbWVudV9pdGVtIC5tZW51X2ljb24ud3BiY19zcGluJyApLmFkZENsYXNzKCAnd3BiY19hbmltYXRpb25fcGF1c2UnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTcGluIGJ1dHRvbiBpbiBGaWx0ZXIgdG9vbGJhciAgLSAgaXMgU3Bpbm5pbmcgP1xyXG4gKlxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2V0dXBfd2l6YXJkX3BhZ2VfcmVsb2FkX2J1dHRvbl9faXNfc3Bpbigpe1xyXG4gICAgaWYgKCBqUXVlcnkoICcjd3BiY19pbml0aWFsX3NldHVwX3RvcF9tZW51X2l0ZW0gLm1lbnVfaWNvbi53cGJjX3NwaW4nICkuaGFzQ2xhc3MoICd3cGJjX2FuaW1hdGlvbl9wYXVzZScgKSApe1xyXG5cdFx0cmV0dXJuIHRydWU7XHJcblx0fSBlbHNlIHtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcbn1cclxuIl0sIm1hcHBpbmdzIjoiQUFBQSxZQUFZOztBQUVaO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxTQUFTQSxxREFBcURBLENBQUdDLFVBQVUsRUFBRTtFQUU1RTtFQUNBQyxjQUFjLENBQUNDLDRCQUE0QixDQUFFRixVQUFXLENBQUM7O0VBRXpEO0VBQ0FHLHlDQUF5QyxDQUFDLENBQUM7QUFDNUM7QUFDQTtBQUNBOztBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNDLG9DQUFvQ0EsQ0FBQSxFQUFHO0VBRS9DLElBQUlDLG9DQUFvQyxHQUFHQyxFQUFFLENBQUNDLFFBQVEsQ0FBRSxzQ0FBdUMsQ0FBQztFQUVoR0MsTUFBTSxDQUFFUCxjQUFjLENBQUNRLGdCQUFnQixDQUFFLHlCQUEwQixDQUFFLENBQUMsQ0FBQ0MsSUFBSSxDQUFJTCxvQ0FBb0MsQ0FBRUosY0FBYyxDQUFDVSw0QkFBNEIsQ0FBQyxDQUFFLENBQUksQ0FBQzs7RUFFeEs7RUFDQUgsTUFBTSxDQUFFLDRCQUE0QixDQUFDLENBQUNJLE1BQU0sQ0FBQyxDQUFDLENBQUNBLE1BQU0sQ0FBQyxDQUFDLENBQUNBLE1BQU0sQ0FBQyxDQUFDLENBQUNBLE1BQU0sQ0FBRSxzQkFBdUIsQ0FBQyxDQUFDQyxJQUFJLENBQUMsQ0FBQzs7RUFFeEc7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7RUFFQTtFQUNBQywyQkFBMkIsQ0FBQyxDQUFDOztFQUU3QjtFQUNB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
