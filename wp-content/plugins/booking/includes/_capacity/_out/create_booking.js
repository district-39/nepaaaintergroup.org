"use strict";

// ---------------------------------------------------------------------------------------------------------------------
// ==  A j a x    A d d    N e w    B o o k i n g  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Submit new booking
 *
 * @param params   =     {
                                'resource_id'        : resource_id,
                                'dates_ddmmyy_csv'   : document.getElementById( 'date_booking' + resource_id ).value,
                                'formdata'           : formdata,
                                'booking_hash'       : my_booking_hash,
                                'custom_form'        : my_booking_form,

                                'captcha_chalange'   : captcha_chalange,
                                'captcha_user_input' : user_captcha,

                                'is_emails_send'     : is_send_emeils,
                                'active_locale'      : wpdev_active_locale
						}
 *
 */
function wpbc_ajx_booking__create(params) {
  console.groupCollapsed('WPBC_AJX_BOOKING__CREATE');
  console.groupCollapsed('== Before Ajax Send ==');
  console.log(params);
  console.groupEnd();
  params = wpbc_captcha__simple__maybe_remove_in_ajx_params(params);

  // Trigger hook  before sending request  to  create the booking.
  jQuery('body').trigger('wpbc_before_booking_create', [params['resource_id'], params]);

  // Start Ajax.
  jQuery.post(wpbc_url_ajax, {
    action: 'WPBC_AJX_BOOKING__CREATE',
    wpbc_ajx_user_id: _wpbc.get_secure_param('user_id'),
    nonce: _wpbc.get_secure_param('nonce'),
    wpbc_ajx_locale: _wpbc.get_secure_param('locale'),
    calendar_request_params: params
    /**
     *  Usually  params = { 'resource_id'        : resource_id,
     *						'dates_ddmmyy_csv'   : document.getElementById( 'date_booking' + resource_id ).value,
     *						'formdata'           : formdata,
     *						'booking_hash'       : my_booking_hash,
     *						'custom_form'        : my_booking_form,
     *
     *						'captcha_chalange'   : captcha_chalange,
     *						'user_captcha'       : user_captcha,
     *
     *						'is_emails_send'     : is_send_emeils,
     *						'active_locale'      : wpdev_active_locale
     *				}
     */
  },
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    console.log(' == Response WPBC_AJX_BOOKING__CREATE == ');
    for (var obj_key in response_data) {
      console.groupCollapsed('==' + obj_key + '==');
      console.log(' : ' + obj_key + ' : ', response_data[obj_key]);
      console.groupEnd();
    }
    console.groupEnd();

    // <editor-fold     defaultstate="collapsed"     desc=" = Error Message! Server response with String.  ->  E_X_I_T  "  >
    // -------------------------------------------------------------------------------------------------
    // This section execute,  when server response with  String instead of Object -- Usually  it's because of mistake in code !
    // -------------------------------------------------------------------------------------------------
    if (typeof response_data !== 'object' || response_data === null) {
      var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(this.data);
      var jq_node = '#booking_form' + calendar_id;
      if ('' == response_data) {
        response_data = '<strong>' + 'Error! Server respond with empty string!' + '</strong> ';
      }
      // Show Message
      wpbc_front_end__show_message(response_data, {
        'type': 'error',
        'show_here': {
          'jq_node': jq_node,
          'where': 'after'
        },
        'is_append': true,
        'style': 'text-align:left;',
        'delay': 0
      });
      // Enable Submit | Hide spin loader
      wpbc_booking_form__on_response__ui_elements_enable(calendar_id);
      return;
    }
    // </editor-fold>

    // <editor-fold     defaultstate="collapsed"     desc="  ==  This section execute,  when we have KNOWN errors from Booking Calendar.  ->  E_X_I_T  "  >
    // -------------------------------------------------------------------------------------------------
    // This section execute,  when we have KNOWN errors from Booking Calendar
    // -------------------------------------------------------------------------------------------------

    if ('ok' != response_data['ajx_data']['status']) {
      switch (response_data['ajx_data']['status_error']) {
        case 'captcha_simple_wrong':
          wpbc_captcha__simple__update({
            'resource_id': response_data['resource_id'],
            'url': response_data['ajx_data']['captcha__simple']['url'],
            'challenge': response_data['ajx_data']['captcha__simple']['challenge'],
            'message': response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />")
          });
          break;
        case 'resource_id_incorrect':
          // Show Error Message - incorrect  booking resource ID during submit of booking.
          var message_id = wpbc_front_end__show_message(response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />"), {
            'type': 'undefined' !== typeof response_data['ajx_data']['ajx_after_action_message_status'] ? response_data['ajx_data']['ajx_after_action_message_status'] : 'warning',
            'delay': 0,
            'show_here': {
              'where': 'after',
              'jq_node': '#booking_form' + params['resource_id']
            }
          });
          break;
        case 'booking_can_not_save':
          // We can not save booking, because dates are booked or can not save in same booking resource all the dates
          var message_id = wpbc_front_end__show_message(response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />"), {
            'type': 'undefined' !== typeof response_data['ajx_data']['ajx_after_action_message_status'] ? response_data['ajx_data']['ajx_after_action_message_status'] : 'warning',
            'delay': 0,
            'show_here': {
              'where': 'after',
              'jq_node': '#booking_form' + params['resource_id']
            }
          });

          // Enable Submit | Hide spin loader
          wpbc_booking_form__on_response__ui_elements_enable(response_data['resource_id']);
          break;
        default:
          // <editor-fold     defaultstate="collapsed"                        desc=" = For debug only ? --  Show Message under the form = "  >
          // --------------------------------------------------------------------------------------------------------------------------------
          if ('undefined' !== typeof response_data['ajx_data']['ajx_after_action_message'] && '' != response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />")) {
            var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(this.data);
            var jq_node = '#booking_form' + calendar_id;
            var ajx_after_booking_message = response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />");
            console.log(ajx_after_booking_message);

            /**
             * // Show Message
            	var ajx_after_action_message_id = wpbc_front_end__show_message( ajx_after_booking_message,
            								{
            									'type' : ('undefined' !== typeof (response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ]))
            											? response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] : 'info',
            									'delay'    : 10000,
            									'show_here': {
            													'jq_node': jq_node,
            													'where'  : 'after'
            												 }
            								} );
             */
          }
        // </editor-fold>
      }

      // -------------------------------------------------------------------------------------------------
      // Reactivate calendar again ?
      // -------------------------------------------------------------------------------------------------
      // Enable Submit | Hide spin loader
      wpbc_booking_form__on_response__ui_elements_enable(response_data['resource_id']);

      // Unselect  dates
      wpbc_calendar__unselect_all_dates(response_data['resource_id']);

      // 'resource_id'    => $params['resource_id'],
      // 'booking_hash'   => $booking_hash,
      // 'request_uri'    => $_SERVER['REQUEST_URI'],                                            // Is it the same as window.location.href or
      // 'custom_form'    => $params['custom_form'],                                             // Optional.
      // 'aggregate_resource_id_str' => implode( ',', $params['aggregate_resource_id_arr'] )     // Optional. Resource ID   from  aggregate parameter in shortcode.

      // Load new data in calendar.
      wpbc_calendar__load_data__ajx({
        'resource_id': response_data['resource_id'] // It's from response ...AJX_BOOKING__CREATE of initial sent resource_id
        ,
        'booking_hash': response_data['ajx_cleaned_params']['booking_hash'] // ?? we can not use it,  because HASH chnaged in any  case!
        ,
        'request_uri': response_data['ajx_cleaned_params']['request_uri'],
        'custom_form': response_data['ajx_cleaned_params']['custom_form']
        // Aggregate booking resources,  if any ?
        ,
        'aggregate_resource_id_str': _wpbc.booking__get_param_value(response_data['resource_id'], 'aggregate_resource_id_arr').join(',')
      });
      // Exit
      return;
    }

    // </editor-fold>

    /*
    	// Show Calendar
    	wpbc_calendar__loading__stop( response_data[ 'resource_id' ] );
    
    	// -------------------------------------------------------------------------------------------------
    	// Bookings - Dates
    	_wpbc.bookings_in_calendar__set_dates(  response_data[ 'resource_id' ], response_data[ 'ajx_data' ]['dates']  );
    
    	// Bookings - Child or only single booking resource in dates
    	_wpbc.booking__set_param_value( response_data[ 'resource_id' ], 'resources_id_arr__in_dates', response_data[ 'ajx_data' ][ 'resources_id_arr__in_dates' ] );
    	// -------------------------------------------------------------------------------------------------
    
    	// Update calendar
    	wpbc_calendar__update_look( response_data[ 'resource_id' ] );
    */

    // Hide spin loader
    wpbc_booking_form__spin_loader__hide(response_data['resource_id']);

    // Hide booking form
    wpbc_booking_form__animated__hide(response_data['resource_id']);

    // Show Confirmation | Payment section
    wpbc_show_thank_you_message_after_booking(response_data);
    setTimeout(function () {
      wpbc_do_scroll('#wpbc_scroll_point_' + response_data['resource_id'], 10);
    }, 500);
  }).fail(
  // <editor-fold     defaultstate="collapsed"                        desc=" = This section execute,  when  NONCE field was not passed or some error happened at  server! = "  >
  function (jqXHR, textStatus, errorThrown) {
    if (window.console && window.console.log) {
      console.log('Ajax_Error', jqXHR, textStatus, errorThrown);
    }

    // -------------------------------------------------------------------------------------------------
    // This section execute,  when  NONCE field was not passed or some error happened at  server!
    // -------------------------------------------------------------------------------------------------

    // Get Content of Error Message
    var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown;
    if (jqXHR.status) {
      error_message += ' (<b>' + jqXHR.status + '</b>)';
      if (403 == jqXHR.status) {
        error_message += '<br> Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
        error_message += '<br> Otherwise, please check this <a style="font-weight: 600;" href="https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/?after_update=10.1.1">troubleshooting instruction</a>.<br>';
      }
    }
    if (jqXHR.responseText) {
      // Escape tags in Error message
      error_message += '<br><strong>Response</strong><div style="padding: 0 10px;margin: 0 0 10px;border-radius:3px; box-shadow:0px 0px 1px #a3a3a3;">' + jqXHR.responseText.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;") + '</div>';
    }
    error_message = error_message.replace(/\n/g, "<br />");
    var calendar_id = wpbc_get_resource_id__from_ajx_post_data_url(this.data);
    var jq_node = '#booking_form' + calendar_id;

    // Show Message
    wpbc_front_end__show_message(error_message, {
      'type': 'error',
      'show_here': {
        'jq_node': jq_node,
        'where': 'after'
      },
      'is_append': true,
      'style': 'text-align:left;',
      'delay': 0
    });
    // Enable Submit | Hide spin loader
    wpbc_booking_form__on_response__ui_elements_enable(calendar_id);
  }
  // </editor-fold>
  )
  // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
  ; // End Ajax

  return true;
}

// <editor-fold     defaultstate="collapsed"                        desc="  ==  CAPTCHA ==  "  >

/**
 * Update image in captcha and show warning message
 *
 * @param params
 *
 * Example of 'params' : {
 *							'resource_id': response_data[ 'resource_id' ],
 *							'url'        : response_data[ 'ajx_data' ][ 'captcha__simple' ][ 'url' ],
 *							'challenge'  : response_data[ 'ajx_data' ][ 'captcha__simple' ][ 'challenge' ],
 *							'message'    : response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" )
 *						}
 */
function wpbc_captcha__simple__update(params) {
  document.getElementById('captcha_input' + params['resource_id']).value = '';
  document.getElementById('captcha_img' + params['resource_id']).src = params['url'];
  document.getElementById('wpdev_captcha_challenge_' + params['resource_id']).value = params['challenge'];

  // Show warning 		After CAPTCHA Img
  var message_id = wpbc_front_end__show_message__warning('#captcha_input' + params['resource_id'] + ' + img', params['message']);

  // Animate
  jQuery('#' + message_id + ', ' + '#captcha_input' + params['resource_id']).fadeOut(350).fadeIn(300).fadeOut(350).fadeIn(400).animate({
    opacity: 1
  }, 4000);
  // Focus text  field
  jQuery('#captcha_input' + params['resource_id']).trigger('focus'); // FixIn: 8.7.11.12.

  // Enable Submit | Hide spin loader
  wpbc_booking_form__on_response__ui_elements_enable(params['resource_id']);
}

/**
 * If the captcha elements not exist  in the booking form,  then  remove parameters relative captcha
 * @param params
 * @returns obj
 */
function wpbc_captcha__simple__maybe_remove_in_ajx_params(params) {
  if (!wpbc_captcha__simple__is_exist_in_form(params['resource_id'])) {
    delete params['captcha_chalange'];
    delete params['captcha_user_input'];
  }
  return params;
}

/**
 * Check if CAPTCHA exist in the booking form
 * @param resource_id
 * @returns {boolean}
 */
function wpbc_captcha__simple__is_exist_in_form(resource_id) {
  return 0 !== jQuery('#wpdev_captcha_challenge_' + resource_id).length || 0 !== jQuery('#captcha_input' + resource_id).length;
}

// </editor-fold>

// <editor-fold     defaultstate="collapsed"                        desc="  ==  Send Button | Form Spin Loader  ==  "  >

/**
 * Disable Send button  |  Show Spin Loader
 *
 * @param resource_id
 */
function wpbc_booking_form__on_submit__ui_elements_disable(resource_id) {
  // Disable Submit
  wpbc_booking_form__send_button__disable(resource_id);

  // Show Spin loader in booking form
  wpbc_booking_form__spin_loader__show(resource_id);
}

/**
 * Enable Send button  |   Hide Spin Loader
 *
 * @param resource_id
 */
function wpbc_booking_form__on_response__ui_elements_enable(resource_id) {
  // Enable Submit
  wpbc_booking_form__send_button__enable(resource_id);

  // Hide Spin loader in booking form
  wpbc_booking_form__spin_loader__hide(resource_id);
}

/**
 * Enable Submit button
 * @param resource_id
 */
function wpbc_booking_form__send_button__enable(resource_id) {
  // Activate Send button
  jQuery('#booking_form_div' + resource_id + ' input[type=button]').prop("disabled", false);
  jQuery('#booking_form_div' + resource_id + ' button').prop("disabled", false);
}

/**
 * Disable Submit button  and show  spin
 *
 * @param resource_id
 */
function wpbc_booking_form__send_button__disable(resource_id) {
  // Disable Send button
  jQuery('#booking_form_div' + resource_id + ' input[type=button]').prop("disabled", true);
  jQuery('#booking_form_div' + resource_id + ' button').prop("disabled", true);
}

/**
 * Disable 'This' button
 *
 * @param _this
 */
function wpbc_booking_form__this_button__disable(_this) {
  // Disable Send button
  jQuery(_this).prop("disabled", true);
}

/**
 * Show booking form  Spin Loader
 * @param resource_id
 */
function wpbc_booking_form__spin_loader__show(resource_id) {
  // Show Spin Loader
  jQuery('#booking_form' + resource_id).after('<div id="wpbc_booking_form_spin_loader' + resource_id + '" class="wpbc_booking_form_spin_loader" style="position: relative;"><div class="wpbc_spins_loader_wrapper"><div class="wpbc_spins_loader_mini"></div></div></div>');
}

/**
 * Remove / Hide booking form  Spin Loader
 * @param resource_id
 */
function wpbc_booking_form__spin_loader__hide(resource_id) {
  // Remove Spin Loader
  jQuery('#wpbc_booking_form_spin_loader' + resource_id).remove();
}

/**
 * Hide booking form wth animation
 *
 * @param resource_id
 */
function wpbc_booking_form__animated__hide(resource_id) {
  jQuery('#booking_form' + resource_id).hide();
}
// </editor-fold>

// <editor-fold     defaultstate="collapsed"                        desc="  ==  Mini Spin Loader  ==  "  >

/**
 *
 * @param parent_html_id
 */

/**
 * Show micro Spin Loader
 *
 * @param id						ID of Loader,  for later  hide it by  using 		wpbc__spin_loader__micro__hide( id ) OR wpbc__spin_loader__mini__hide( id )
 * @param jq_node_where_insert		such as '#estimate_booking_night_cost_hint10'   OR  '.estimate_booking_night_cost_hint10'
 */
function wpbc__spin_loader__micro__show__inside(id, jq_node_where_insert) {
  wpbc__spin_loader__mini__show(id, {
    'color': '#444',
    'show_here': {
      'where': 'inside',
      'jq_node': jq_node_where_insert
    },
    'style': 'position: relative;display: inline-flex;flex-flow: column nowrap;justify-content: center;align-items: center;margin: 7px 12px;',
    'class': 'wpbc_one_spin_loader_micro'
  });
}

/**
 * Remove spinner
 * @param id
 */
function wpbc__spin_loader__micro__hide(id) {
  wpbc__spin_loader__mini__hide(id);
}

/**
 * Show mini Spin Loader
 * @param parent_html_id
 */
function wpbc__spin_loader__mini__show(parent_html_id, params = {}) {
  var params_default = {
    'color': '#0071ce',
    'show_here': {
      'jq_node': '',
      // any jQuery node definition
      'where': 'after' // 'inside' | 'before' | 'after' | 'right' | 'left'
    },
    'style': 'position: relative;min-height: 2.8rem;',
    'class': 'wpbc_one_spin_loader_mini 0wpbc_spins_loader_mini'
  };
  for (var p_key in params) {
    params_default[p_key] = params[p_key];
  }
  params = params_default;
  if ('undefined' !== typeof params['color'] && '' != params['color']) {
    params['color'] = 'border-color:' + params['color'] + ';';
  }
  var spinner_html = '<div id="wpbc_mini_spin_loader' + parent_html_id + '" class="wpbc_booking_form_spin_loader" style="' + params['style'] + '"><div class="wpbc_spins_loader_wrapper"><div class="' + params['class'] + '" style="' + params['color'] + '"></div></div></div>';
  if ('' == params['show_here']['jq_node']) {
    params['show_here']['jq_node'] = '#' + parent_html_id;
  }

  // Show Spin Loader
  if ('after' == params['show_here']['where']) {
    jQuery(params['show_here']['jq_node']).after(spinner_html);
  } else {
    jQuery(params['show_here']['jq_node']).html(spinner_html);
  }
}

/**
 * Remove / Hide mini Spin Loader
 * @param parent_html_id
 */
function wpbc__spin_loader__mini__hide(parent_html_id) {
  // Remove Spin Loader
  jQuery('#wpbc_mini_spin_loader' + parent_html_id).remove();
}

// </editor-fold>

//TODO: what  about showing only  Thank you. message without payment forms.
/**
 * Show 'Thank you'. message and payment forms
 *
 * @param response_data
 */
function wpbc_show_thank_you_message_after_booking(response_data) {
  if ('undefined' !== typeof response_data['ajx_confirmation']['ty_is_redirect'] && 'undefined' !== typeof response_data['ajx_confirmation']['ty_url'] && 'page' == response_data['ajx_confirmation']['ty_is_redirect'] && '' != response_data['ajx_confirmation']['ty_url']) {
    jQuery('body').trigger('wpbc_booking_created', [response_data['resource_id'], response_data]); // FixIn: 10.0.0.30.
    window.location.href = response_data['ajx_confirmation']['ty_url'];
    return;
  }
  var resource_id = response_data['resource_id'];
  var confirm_content = '';
  if ('undefined' === typeof response_data['ajx_confirmation']['ty_message']) {
    response_data['ajx_confirmation']['ty_message'] = '';
  }
  if ('undefined' === typeof response_data['ajx_confirmation']['ty_payment_payment_description']) {
    response_data['ajx_confirmation']['ty_payment_payment_description'] = '';
  }
  if ('undefined' === typeof response_data['ajx_confirmation']['payment_cost']) {
    response_data['ajx_confirmation']['payment_cost'] = '';
  }
  if ('undefined' === typeof response_data['ajx_confirmation']['ty_payment_gateways']) {
    response_data['ajx_confirmation']['ty_payment_gateways'] = '';
  }
  var ty_message_hide = '' == response_data['ajx_confirmation']['ty_message'] ? 'wpbc_ty_hide' : '';
  var ty_payment_payment_description_hide = '' == response_data['ajx_confirmation']['ty_payment_payment_description'].replace(/\\n/g, '') ? 'wpbc_ty_hide' : '';
  var ty_booking_costs_hide = '' == response_data['ajx_confirmation']['payment_cost'] ? 'wpbc_ty_hide' : '';
  var ty_payment_gateways_hide = '' == response_data['ajx_confirmation']['ty_payment_gateways'].replace(/\\n/g, '') ? 'wpbc_ty_hide' : '';
  if ('wpbc_ty_hide' != ty_payment_gateways_hide) {
    jQuery('.wpbc_ty__content_text.wpbc_ty__content_gateways').html(''); // Reset  all  other possible gateways before showing new one.
  }
  confirm_content += `<div id="wpbc_scroll_point_${resource_id}"></div>`;
  confirm_content += `  <div class="wpbc_after_booking_thank_you_section">`;
  confirm_content += `    <div class="wpbc_ty__message ${ty_message_hide}">${response_data['ajx_confirmation']['ty_message']}</div>`;
  confirm_content += `    <div class="wpbc_ty__container">`;
  if ('' !== response_data['ajx_confirmation']['ty_message_booking_id']) {
    confirm_content += `      <div class="wpbc_ty__header">${response_data['ajx_confirmation']['ty_message_booking_id']}</div>`;
  }
  confirm_content += `      <div class="wpbc_ty__content">`;
  confirm_content += `        <div class="wpbc_ty__content_text wpbc_ty__payment_description ${ty_payment_payment_description_hide}">${response_data['ajx_confirmation']['ty_payment_payment_description'].replace(/\\n/g, '')}</div>`;
  if ('' !== response_data['ajx_confirmation']['ty_customer_details']) {
    confirm_content += `      	<div class="wpbc_ty__content_text wpbc_cols_2">${response_data['ajx_confirmation']['ty_customer_details']}</div>`;
  }
  if ('' !== response_data['ajx_confirmation']['ty_booking_details']) {
    confirm_content += `      	<div class="wpbc_ty__content_text wpbc_cols_2">${response_data['ajx_confirmation']['ty_booking_details']}</div>`;
  }
  confirm_content += `        <div class="wpbc_ty__content_text wpbc_ty__content_costs ${ty_booking_costs_hide}">${response_data['ajx_confirmation']['ty_booking_costs']}</div>`;
  confirm_content += `        <div class="wpbc_ty__content_text wpbc_ty__content_gateways ${ty_payment_gateways_hide}">${response_data['ajx_confirmation']['ty_payment_gateways'].replace(/\\n/g, '').replace(/ajax_script/gi, 'script')}</div>`;
  confirm_content += `      </div>`;
  confirm_content += `    </div>`;
  confirm_content += `</div>`;
  jQuery('#booking_form' + resource_id).after(confirm_content);

  //FixIn: 10.0.0.30		// event name			// Resource ID	-	'1'
  jQuery('body').trigger('wpbc_booking_created', [resource_id, response_data]);
  // To catch this event: jQuery( 'body' ).on('wpbc_booking_created', function( event, resource_id, params ) { console.log( event, resource_id, params ); } );
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvX2NhcGFjaXR5L19vdXQvY3JlYXRlX2Jvb2tpbmcuanMiLCJuYW1lcyI6WyJ3cGJjX2FqeF9ib29raW5nX19jcmVhdGUiLCJwYXJhbXMiLCJjb25zb2xlIiwiZ3JvdXBDb2xsYXBzZWQiLCJsb2ciLCJncm91cEVuZCIsIndwYmNfY2FwdGNoYV9fc2ltcGxlX19tYXliZV9yZW1vdmVfaW5fYWp4X3BhcmFtcyIsImpRdWVyeSIsInRyaWdnZXIiLCJwb3N0Iiwid3BiY191cmxfYWpheCIsImFjdGlvbiIsIndwYmNfYWp4X3VzZXJfaWQiLCJfd3BiYyIsImdldF9zZWN1cmVfcGFyYW0iLCJub25jZSIsIndwYmNfYWp4X2xvY2FsZSIsImNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zIiwicmVzcG9uc2VfZGF0YSIsInRleHRTdGF0dXMiLCJqcVhIUiIsIm9ial9rZXkiLCJjYWxlbmRhcl9pZCIsIndwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsIiwiZGF0YSIsImpxX25vZGUiLCJ3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlIiwid3BiY19ib29raW5nX2Zvcm1fX29uX3Jlc3BvbnNlX191aV9lbGVtZW50c19lbmFibGUiLCJ3cGJjX2NhcHRjaGFfX3NpbXBsZV9fdXBkYXRlIiwicmVwbGFjZSIsIm1lc3NhZ2VfaWQiLCJhanhfYWZ0ZXJfYm9va2luZ19tZXNzYWdlIiwid3BiY19jYWxlbmRhcl9fdW5zZWxlY3RfYWxsX2RhdGVzIiwid3BiY19jYWxlbmRhcl9fbG9hZF9kYXRhX19hangiLCJib29raW5nX19nZXRfcGFyYW1fdmFsdWUiLCJqb2luIiwid3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19oaWRlIiwid3BiY19ib29raW5nX2Zvcm1fX2FuaW1hdGVkX19oaWRlIiwid3BiY19zaG93X3RoYW5rX3lvdV9tZXNzYWdlX2FmdGVyX2Jvb2tpbmciLCJzZXRUaW1lb3V0Iiwid3BiY19kb19zY3JvbGwiLCJmYWlsIiwiZXJyb3JUaHJvd24iLCJ3aW5kb3ciLCJlcnJvcl9tZXNzYWdlIiwic3RhdHVzIiwicmVzcG9uc2VUZXh0IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsInZhbHVlIiwic3JjIiwid3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fd2FybmluZyIsImZhZGVPdXQiLCJmYWRlSW4iLCJhbmltYXRlIiwib3BhY2l0eSIsIndwYmNfY2FwdGNoYV9fc2ltcGxlX19pc19leGlzdF9pbl9mb3JtIiwicmVzb3VyY2VfaWQiLCJsZW5ndGgiLCJ3cGJjX2Jvb2tpbmdfZm9ybV9fb25fc3VibWl0X191aV9lbGVtZW50c19kaXNhYmxlIiwid3BiY19ib29raW5nX2Zvcm1fX3NlbmRfYnV0dG9uX19kaXNhYmxlIiwid3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19zaG93Iiwid3BiY19ib29raW5nX2Zvcm1fX3NlbmRfYnV0dG9uX19lbmFibGUiLCJwcm9wIiwid3BiY19ib29raW5nX2Zvcm1fX3RoaXNfYnV0dG9uX19kaXNhYmxlIiwiX3RoaXMiLCJhZnRlciIsInJlbW92ZSIsImhpZGUiLCJ3cGJjX19zcGluX2xvYWRlcl9fbWljcm9fX3Nob3dfX2luc2lkZSIsImlkIiwianFfbm9kZV93aGVyZV9pbnNlcnQiLCJ3cGJjX19zcGluX2xvYWRlcl9fbWluaV9fc2hvdyIsIndwYmNfX3NwaW5fbG9hZGVyX19taWNyb19faGlkZSIsIndwYmNfX3NwaW5fbG9hZGVyX19taW5pX19oaWRlIiwicGFyZW50X2h0bWxfaWQiLCJwYXJhbXNfZGVmYXVsdCIsInBfa2V5Iiwic3Bpbm5lcl9odG1sIiwiaHRtbCIsImxvY2F0aW9uIiwiaHJlZiIsImNvbmZpcm1fY29udGVudCIsInR5X21lc3NhZ2VfaGlkZSIsInR5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbl9oaWRlIiwidHlfYm9va2luZ19jb3N0c19oaWRlIiwidHlfcGF5bWVudF9nYXRld2F5c19oaWRlIl0sInNvdXJjZXMiOlsiaW5jbHVkZXMvX2NhcGFjaXR5L19zcmMvY3JlYXRlX2Jvb2tpbmcuanMiXSwic291cmNlc0NvbnRlbnQiOlsiXCJ1c2Ugc3RyaWN0XCI7XHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gIEEgaiBhIHggICAgQSBkIGQgICAgTiBlIHcgICAgQiBvIG8gayBpIG4gZyAgPT1cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cclxuLyoqXHJcbiAqIFN1Ym1pdCBuZXcgYm9va2luZ1xyXG4gKlxyXG4gKiBAcGFyYW0gcGFyYW1zICAgPSAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdyZXNvdXJjZV9pZCcgICAgICAgIDogcmVzb3VyY2VfaWQsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2RhdGVzX2RkbW15eV9jc3YnICAgOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2RhdGVfYm9va2luZycgKyByZXNvdXJjZV9pZCApLnZhbHVlLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdmb3JtZGF0YScgICAgICAgICAgIDogZm9ybWRhdGEsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2Jvb2tpbmdfaGFzaCcgICAgICAgOiBteV9ib29raW5nX2hhc2gsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2N1c3RvbV9mb3JtJyAgICAgICAgOiBteV9ib29raW5nX2Zvcm0sXHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdjYXB0Y2hhX2NoYWxhbmdlJyAgIDogY2FwdGNoYV9jaGFsYW5nZSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnY2FwdGNoYV91c2VyX2lucHV0JyA6IHVzZXJfY2FwdGNoYSxcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2lzX2VtYWlsc19zZW5kJyAgICAgOiBpc19zZW5kX2VtZWlscyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnYWN0aXZlX2xvY2FsZScgICAgICA6IHdwZGV2X2FjdGl2ZV9sb2NhbGVcclxuXHRcdFx0XHRcdFx0fVxyXG4gKlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19fY3JlYXRlKCBwYXJhbXMgKXtcclxuXHJcblx0Y29uc29sZS5ncm91cENvbGxhcHNlZCggJ1dQQkNfQUpYX0JPT0tJTkdfX0NSRUFURScgKTtcclxuXHRjb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnPT0gQmVmb3JlIEFqYXggU2VuZCA9PScgKTtcclxuXHRjb25zb2xlLmxvZyggcGFyYW1zICk7XHJcblx0Y29uc29sZS5ncm91cEVuZCgpO1xyXG5cclxuXHRwYXJhbXMgPSB3cGJjX2NhcHRjaGFfX3NpbXBsZV9fbWF5YmVfcmVtb3ZlX2luX2FqeF9wYXJhbXMoIHBhcmFtcyApO1xyXG5cclxuXHQvLyBUcmlnZ2VyIGhvb2sgIGJlZm9yZSBzZW5kaW5nIHJlcXVlc3QgIHRvICBjcmVhdGUgdGhlIGJvb2tpbmcuXHJcblx0alF1ZXJ5KCAnYm9keScgKS50cmlnZ2VyKCAnd3BiY19iZWZvcmVfYm9va2luZ19jcmVhdGUnLCBbIHBhcmFtc1sncmVzb3VyY2VfaWQnXSwgcGFyYW1zIF0gKTtcclxuXHJcblx0Ly8gU3RhcnQgQWpheC5cclxuXHRqUXVlcnkucG9zdCggd3BiY191cmxfYWpheCxcclxuXHRcdHtcclxuXHRcdFx0YWN0aW9uICAgICAgICAgICAgICAgICA6ICdXUEJDX0FKWF9CT09LSU5HX19DUkVBVEUnLFxyXG5cdFx0XHR3cGJjX2FqeF91c2VyX2lkICAgICAgIDogX3dwYmMuZ2V0X3NlY3VyZV9wYXJhbSggJ3VzZXJfaWQnICksXHJcblx0XHRcdG5vbmNlICAgICAgICAgICAgICAgICAgOiBfd3BiYy5nZXRfc2VjdXJlX3BhcmFtKCAnbm9uY2UnICksXHJcblx0XHRcdHdwYmNfYWp4X2xvY2FsZSAgICAgICAgOiBfd3BiYy5nZXRfc2VjdXJlX3BhcmFtKCAnbG9jYWxlJyApLFxyXG5cdFx0XHRjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtczogcGFyYW1zLFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogIFVzdWFsbHkgIHBhcmFtcyA9IHsgJ3Jlc291cmNlX2lkJyAgICAgICAgOiByZXNvdXJjZV9pZCxcclxuXHRcdFx0ICpcdFx0XHRcdFx0XHQnZGF0ZXNfZGRtbXl5X2NzdicgICA6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICkudmFsdWUsXHJcblx0XHRcdCAqXHRcdFx0XHRcdFx0J2Zvcm1kYXRhJyAgICAgICAgICAgOiBmb3JtZGF0YSxcclxuXHRcdFx0ICpcdFx0XHRcdFx0XHQnYm9va2luZ19oYXNoJyAgICAgICA6IG15X2Jvb2tpbmdfaGFzaCxcclxuXHRcdFx0ICpcdFx0XHRcdFx0XHQnY3VzdG9tX2Zvcm0nICAgICAgICA6IG15X2Jvb2tpbmdfZm9ybSxcclxuXHRcdFx0ICpcclxuXHRcdFx0ICpcdFx0XHRcdFx0XHQnY2FwdGNoYV9jaGFsYW5nZScgICA6IGNhcHRjaGFfY2hhbGFuZ2UsXHJcblx0XHRcdCAqXHRcdFx0XHRcdFx0J3VzZXJfY2FwdGNoYScgICAgICAgOiB1c2VyX2NhcHRjaGEsXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqXHRcdFx0XHRcdFx0J2lzX2VtYWlsc19zZW5kJyAgICAgOiBpc19zZW5kX2VtZWlscyxcclxuXHRcdFx0ICpcdFx0XHRcdFx0XHQnYWN0aXZlX2xvY2FsZScgICAgICA6IHdwZGV2X2FjdGl2ZV9sb2NhbGVcclxuXHRcdFx0ICpcdFx0XHRcdH1cclxuXHRcdFx0ICovXHJcblx0XHR9LFxyXG5cclxuXHRcdFx0XHQvKipcclxuXHRcdFx0XHQgKiBTIHUgYyBjIGUgcyBzXHJcblx0XHRcdFx0ICpcclxuXHRcdFx0XHQgKiBAcGFyYW0gcmVzcG9uc2VfZGF0YVx0XHQtXHRpdHMgb2JqZWN0IHJldHVybmVkIGZyb20gIEFqYXggLSBjbGFzcy1saXZlLXNlYXJjZy5waHBcclxuXHRcdFx0XHQgKiBAcGFyYW0gdGV4dFN0YXR1c1x0XHQtXHQnc3VjY2VzcydcclxuXHRcdFx0XHQgKiBAcGFyYW0ganFYSFJcdFx0XHRcdC1cdE9iamVjdFxyXG5cdFx0XHRcdCAqL1xyXG5cdFx0XHRcdGZ1bmN0aW9uICggcmVzcG9uc2VfZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7XHJcbmNvbnNvbGUubG9nKCAnID09IFJlc3BvbnNlIFdQQkNfQUpYX0JPT0tJTkdfX0NSRUFURSA9PSAnICk7XHJcbmZvciAoIHZhciBvYmpfa2V5IGluIHJlc3BvbnNlX2RhdGEgKXtcclxuXHRjb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnPT0nICsgb2JqX2tleSArICc9PScgKTtcclxuXHRjb25zb2xlLmxvZyggJyA6ICcgKyBvYmpfa2V5ICsgJyA6ICcsIHJlc3BvbnNlX2RhdGFbIG9ial9rZXkgXSApO1xyXG5cdGNvbnNvbGUuZ3JvdXBFbmQoKTtcclxufVxyXG5jb25zb2xlLmdyb3VwRW5kKCk7XHJcblxyXG5cclxuXHRcdFx0XHRcdC8vIDxlZGl0b3ItZm9sZCAgICAgZGVmYXVsdHN0YXRlPVwiY29sbGFwc2VkXCIgICAgIGRlc2M9XCIgPSBFcnJvciBNZXNzYWdlISBTZXJ2ZXIgcmVzcG9uc2Ugd2l0aCBTdHJpbmcuICAtPiAgRV9YX0lfVCAgXCIgID5cclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRcdC8vIFRoaXMgc2VjdGlvbiBleGVjdXRlLCAgd2hlbiBzZXJ2ZXIgcmVzcG9uc2Ugd2l0aCAgU3RyaW5nIGluc3RlYWQgb2YgT2JqZWN0IC0tIFVzdWFsbHkgIGl0J3MgYmVjYXVzZSBvZiBtaXN0YWtlIGluIGNvZGUgIVxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0aWYgKCAodHlwZW9mIHJlc3BvbnNlX2RhdGEgIT09ICdvYmplY3QnKSB8fCAocmVzcG9uc2VfZGF0YSA9PT0gbnVsbCkgKXtcclxuXHJcblx0XHRcdFx0XHRcdHZhciBjYWxlbmRhcl9pZCA9IHdwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsKCB0aGlzLmRhdGEgKTtcclxuXHRcdFx0XHRcdFx0dmFyIGpxX25vZGUgPSAnI2Jvb2tpbmdfZm9ybScgKyBjYWxlbmRhcl9pZDtcclxuXHJcblx0XHRcdFx0XHRcdGlmICggJycgPT0gcmVzcG9uc2VfZGF0YSApe1xyXG5cdFx0XHRcdFx0XHRcdHJlc3BvbnNlX2RhdGEgPSAnPHN0cm9uZz4nICsgJ0Vycm9yISBTZXJ2ZXIgcmVzcG9uZCB3aXRoIGVtcHR5IHN0cmluZyEnICsgJzwvc3Ryb25nPiAnIDtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHQvLyBTaG93IE1lc3NhZ2VcclxuXHRcdFx0XHRcdFx0d3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZSggcmVzcG9uc2VfZGF0YSAsIHsgJ3R5cGUnICAgICA6ICdlcnJvcicsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7J2pxX25vZGUnOiBqcV9ub2RlLCAnd2hlcmUnOiAnYWZ0ZXInfSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2lzX2FwcGVuZCc6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzdHlsZScgICAgOiAndGV4dC1hbGlnbjpsZWZ0OycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgOiAwXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0XHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19vbl9yZXNwb25zZV9fdWlfZWxlbWVudHNfZW5hYmxlKCBjYWxlbmRhcl9pZCApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHQvLyA8L2VkaXRvci1mb2xkPlxyXG5cclxuXHJcblx0XHRcdFx0XHQvLyA8ZWRpdG9yLWZvbGQgICAgIGRlZmF1bHRzdGF0ZT1cImNvbGxhcHNlZFwiICAgICBkZXNjPVwiICA9PSAgVGhpcyBzZWN0aW9uIGV4ZWN1dGUsICB3aGVuIHdlIGhhdmUgS05PV04gZXJyb3JzIGZyb20gQm9va2luZyBDYWxlbmRhci4gIC0+ICBFX1hfSV9UICBcIiAgPlxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0Ly8gVGhpcyBzZWN0aW9uIGV4ZWN1dGUsICB3aGVuIHdlIGhhdmUgS05PV04gZXJyb3JzIGZyb20gQm9va2luZyBDYWxlbmRhclxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRcdFx0XHRcdGlmICggJ29rJyAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdzdGF0dXMnIF0gKSB7XHJcblxyXG5cdFx0XHRcdFx0XHRzd2l0Y2ggKCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdzdGF0dXNfZXJyb3InIF0gKXtcclxuXHJcblx0XHRcdFx0XHRcdFx0Y2FzZSAnY2FwdGNoYV9zaW1wbGVfd3JvbmcnOlxyXG5cdFx0XHRcdFx0XHRcdFx0d3BiY19jYXB0Y2hhX19zaW1wbGVfX3VwZGF0ZSgge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdyZXNvdXJjZV9pZCc6IHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndXJsJyAgICAgICAgOiByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdjYXB0Y2hhX19zaW1wbGUnIF1bICd1cmwnIF0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2NoYWxsZW5nZScgIDogcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnY2FwdGNoYV9fc2ltcGxlJyBdWyAnY2hhbGxlbmdlJyBdLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdtZXNzYWdlJyAgICA6IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdFx0XHRcdGNhc2UgJ3Jlc291cmNlX2lkX2luY29ycmVjdCc6XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBTaG93IEVycm9yIE1lc3NhZ2UgLSBpbmNvcnJlY3QgIGJvb2tpbmcgcmVzb3VyY2UgSUQgZHVyaW5nIHN1Ym1pdCBvZiBib29raW5nLlxyXG5cdFx0XHRcdFx0XHRcdFx0dmFyIG1lc3NhZ2VfaWQgPSB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnIDogKCd1bmRlZmluZWQnICE9PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0pKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdD8gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX3N0YXR1cycgXSA6ICd3YXJuaW5nJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgOiAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZSc6IHsgJ3doZXJlJzogJ2FmdGVyJywgJ2pxX25vZGUnOiAnI2Jvb2tpbmdfZm9ybScgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdFx0XHRcdGNhc2UgJ2Jvb2tpbmdfY2FuX25vdF9zYXZlJzpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFdlIGNhbiBub3Qgc2F2ZSBib29raW5nLCBiZWNhdXNlIGRhdGVzIGFyZSBib29rZWQgb3IgY2FuIG5vdCBzYXZlIGluIHNhbWUgYm9va2luZyByZXNvdXJjZSBhbGwgdGhlIGRhdGVzXHJcblx0XHRcdFx0XHRcdFx0XHR2YXIgbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndHlwZScgOiAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAocmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX3N0YXR1cycgXSkpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PyByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2Vfc3RhdHVzJyBdIDogJ3dhcm5pbmcnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICA6IDAsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJzogeyAnd2hlcmUnOiAnYWZ0ZXInLCAnanFfbm9kZSc6ICcjYm9va2luZ19mb3JtJyArIHBhcmFtc1sgJ3Jlc291cmNlX2lkJyBdIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gRW5hYmxlIFN1Ym1pdCB8IEhpZGUgc3BpbiBsb2FkZXJcclxuXHRcdFx0XHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19vbl9yZXNwb25zZV9fdWlfZWxlbWVudHNfZW5hYmxlKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRicmVhaztcclxuXHJcblxyXG5cdFx0XHRcdFx0XHRcdGRlZmF1bHQ6XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gPGVkaXRvci1mb2xkICAgICBkZWZhdWx0c3RhdGU9XCJjb2xsYXBzZWRcIiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2M9XCIgPSBGb3IgZGVidWcgb25seSA/IC0tICBTaG93IE1lc3NhZ2UgdW5kZXIgdGhlIGZvcm0gPSBcIiAgPlxyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0XHRcdFx0XHRcdGlmIChcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQoICd1bmRlZmluZWQnICE9PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXSkgKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHQgJiYgKCAnJyAhPSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICkgKVxyXG5cdFx0XHRcdFx0XHRcdFx0KXtcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdHZhciBjYWxlbmRhcl9pZCA9IHdwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsKCB0aGlzLmRhdGEgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIGpxX25vZGUgPSAnI2Jvb2tpbmdfZm9ybScgKyBjYWxlbmRhcl9pZDtcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdHZhciBhanhfYWZ0ZXJfYm9va2luZ19tZXNzYWdlID0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApO1xyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0Y29uc29sZS5sb2coIGFqeF9hZnRlcl9ib29raW5nX21lc3NhZ2UgKTtcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdC8qKlxyXG5cdFx0XHRcdFx0XHRcdFx0XHQgKiAvLyBTaG93IE1lc3NhZ2VcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR2YXIgYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX2lkID0gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZSggYWp4X2FmdGVyX2Jvb2tpbmdfbWVzc2FnZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnIDogKCd1bmRlZmluZWQnICE9PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0pKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PyByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2Vfc3RhdHVzJyBdIDogJ2luZm8nLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMTAwMDAsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2pxX25vZGUnOiBqcV9ub2RlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aGVyZScgIDogJ2FmdGVyJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgfVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQgKi9cclxuXHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdC8vIDwvZWRpdG9yLWZvbGQ+XHJcblx0XHRcdFx0XHRcdH1cclxuXHJcblxyXG5cdFx0XHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0XHRcdC8vIFJlYWN0aXZhdGUgY2FsZW5kYXIgYWdhaW4gP1xyXG5cdFx0XHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0XHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19vbl9yZXNwb25zZV9fdWlfZWxlbWVudHNfZW5hYmxlKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRcdC8vIFVuc2VsZWN0ICBkYXRlc1xyXG5cdFx0XHRcdFx0XHR3cGJjX2NhbGVuZGFyX191bnNlbGVjdF9hbGxfZGF0ZXMoIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSApO1xyXG5cclxuXHRcdFx0XHRcdFx0Ly8gJ3Jlc291cmNlX2lkJyAgICA9PiAkcGFyYW1zWydyZXNvdXJjZV9pZCddLFxyXG5cdFx0XHRcdFx0XHQvLyAnYm9va2luZ19oYXNoJyAgID0+ICRib29raW5nX2hhc2gsXHJcblx0XHRcdFx0XHRcdC8vICdyZXF1ZXN0X3VyaScgICAgPT4gJF9TRVJWRVJbJ1JFUVVFU1RfVVJJJ10sICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBJcyBpdCB0aGUgc2FtZSBhcyB3aW5kb3cubG9jYXRpb24uaHJlZiBvclxyXG5cdFx0XHRcdFx0XHQvLyAnY3VzdG9tX2Zvcm0nICAgID0+ICRwYXJhbXNbJ2N1c3RvbV9mb3JtJ10sICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gT3B0aW9uYWwuXHJcblx0XHRcdFx0XHRcdC8vICdhZ2dyZWdhdGVfcmVzb3VyY2VfaWRfc3RyJyA9PiBpbXBsb2RlKCAnLCcsICRwYXJhbXNbJ2FnZ3JlZ2F0ZV9yZXNvdXJjZV9pZF9hcnInXSApICAgICAvLyBPcHRpb25hbC4gUmVzb3VyY2UgSUQgICBmcm9tICBhZ2dyZWdhdGUgcGFyYW1ldGVyIGluIHNob3J0Y29kZS5cclxuXHJcblx0XHRcdFx0XHRcdC8vIExvYWQgbmV3IGRhdGEgaW4gY2FsZW5kYXIuXHJcblx0XHRcdFx0XHRcdHdwYmNfY2FsZW5kYXJfX2xvYWRfZGF0YV9fYWp4KCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgICdyZXNvdXJjZV9pZCcgOiByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF1cdFx0XHRcdFx0XHRcdC8vIEl0J3MgZnJvbSByZXNwb25zZSAuLi5BSlhfQk9PS0lOR19fQ1JFQVRFIG9mIGluaXRpYWwgc2VudCByZXNvdXJjZV9pZFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAnYm9va2luZ19oYXNoJzogcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXVsnYm9va2luZ19oYXNoJ10gXHQvLyA/PyB3ZSBjYW4gbm90IHVzZSBpdCwgIGJlY2F1c2UgSEFTSCBjaG5hZ2VkIGluIGFueSAgY2FzZSFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ3JlcXVlc3RfdXJpJyA6IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bJ3JlcXVlc3RfdXJpJ11cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgJ2N1c3RvbV9mb3JtJyA6IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bJ2N1c3RvbV9mb3JtJ11cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBBZ2dyZWdhdGUgYm9va2luZyByZXNvdXJjZXMsICBpZiBhbnkgP1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAnYWdncmVnYXRlX3Jlc291cmNlX2lkX3N0cicgOiBfd3BiYy5ib29raW5nX19nZXRfcGFyYW1fdmFsdWUoIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSwgJ2FnZ3JlZ2F0ZV9yZXNvdXJjZV9pZF9hcnInICkuam9pbignLCcpXHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRcdFx0Ly8gRXhpdFxyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gPC9lZGl0b3ItZm9sZD5cclxuXHJcblxyXG4vKlxyXG5cdC8vIFNob3cgQ2FsZW5kYXJcclxuXHR3cGJjX2NhbGVuZGFyX19sb2FkaW5nX19zdG9wKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEJvb2tpbmdzIC0gRGF0ZXNcclxuXHRfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fc2V0X2RhdGVzKCAgcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdLCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bJ2RhdGVzJ10gICk7XHJcblxyXG5cdC8vIEJvb2tpbmdzIC0gQ2hpbGQgb3Igb25seSBzaW5nbGUgYm9va2luZyByZXNvdXJjZSBpbiBkYXRlc1xyXG5cdF93cGJjLmJvb2tpbmdfX3NldF9wYXJhbV92YWx1ZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdLCAncmVzb3VyY2VzX2lkX2Fycl9faW5fZGF0ZXMnLCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdyZXNvdXJjZXNfaWRfYXJyX19pbl9kYXRlcycgXSApO1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0Ly8gVXBkYXRlIGNhbGVuZGFyXHJcblx0d3BiY19jYWxlbmRhcl9fdXBkYXRlX2xvb2soIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSApO1xyXG4qL1xyXG5cclxuXHRcdFx0XHRcdC8vIEhpZGUgc3BpbiBsb2FkZXJcclxuXHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19zcGluX2xvYWRlcl9faGlkZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gSGlkZSBib29raW5nIGZvcm1cclxuXHRcdFx0XHRcdHdwYmNfYm9va2luZ19mb3JtX19hbmltYXRlZF9faGlkZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gU2hvdyBDb25maXJtYXRpb24gfCBQYXltZW50IHNlY3Rpb25cclxuXHRcdFx0XHRcdHdwYmNfc2hvd190aGFua195b3VfbWVzc2FnZV9hZnRlcl9ib29raW5nKCByZXNwb25zZV9kYXRhICk7XHJcblxyXG5cdFx0XHRcdFx0c2V0VGltZW91dCggZnVuY3Rpb24gKCl7XHJcblx0XHRcdFx0XHRcdHdwYmNfZG9fc2Nyb2xsKCAnI3dwYmNfc2Nyb2xsX3BvaW50XycgKyByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0sIDEwICk7XHJcblx0XHRcdFx0XHR9LCA1MDAgKTtcclxuXHJcblxyXG5cclxuXHRcdFx0XHR9XHJcblx0XHRcdCAgKS5mYWlsKFxyXG5cdFx0XHRcdCAgLy8gPGVkaXRvci1mb2xkICAgICBkZWZhdWx0c3RhdGU9XCJjb2xsYXBzZWRcIiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2M9XCIgPSBUaGlzIHNlY3Rpb24gZXhlY3V0ZSwgIHdoZW4gIE5PTkNFIGZpZWxkIHdhcyBub3QgcGFzc2VkIG9yIHNvbWUgZXJyb3IgaGFwcGVuZWQgYXQgIHNlcnZlciEgPSBcIiAgPlxyXG5cdFx0XHRcdCAgZnVuY3Rpb24gKCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKSB7ICAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnQWpheF9FcnJvcicsIGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93biApOyB9XHJcblxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0Ly8gVGhpcyBzZWN0aW9uIGV4ZWN1dGUsICB3aGVuICBOT05DRSBmaWVsZCB3YXMgbm90IHBhc3NlZCBvciBzb21lIGVycm9yIGhhcHBlbmVkIGF0ICBzZXJ2ZXIhXHJcblx0XHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdFx0XHRcdFx0Ly8gR2V0IENvbnRlbnQgb2YgRXJyb3IgTWVzc2FnZVxyXG5cdFx0XHRcdFx0dmFyIGVycm9yX21lc3NhZ2UgPSAnPHN0cm9uZz4nICsgJ0Vycm9yIScgKyAnPC9zdHJvbmc+ICcgKyBlcnJvclRocm93biA7XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgKDxiPicgKyBqcVhIUi5zdGF0dXMgKyAnPC9iPiknO1xyXG5cdFx0XHRcdFx0XHRpZiAoNDAzID09IGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0gJzxicj4gUHJvYmFibHkgbm9uY2UgZm9yIHRoaXMgcGFnZSBoYXMgYmVlbiBleHBpcmVkLiBQbGVhc2UgPGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiIG9uY2xpY2s9XCJqYXZhc2NyaXB0OmxvY2F0aW9uLnJlbG9hZCgpO1wiPnJlbG9hZCB0aGUgcGFnZTwvYT4uJztcclxuXHRcdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICc8YnI+IE90aGVyd2lzZSwgcGxlYXNlIGNoZWNrIHRoaXMgPGEgc3R5bGU9XCJmb250LXdlaWdodDogNjAwO1wiIGhyZWY9XCJodHRwczovL3dwYm9va2luZ2NhbGVuZGFyLmNvbS9mYXEvcmVxdWVzdC1kby1ub3QtcGFzcy1zZWN1cml0eS1jaGVjay8/YWZ0ZXJfdXBkYXRlPTEwLjEuMVwiPnRyb3VibGVzaG9vdGluZyBpbnN0cnVjdGlvbjwvYT4uPGJyPidcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCBqcVhIUi5yZXNwb25zZVRleHQgKXtcclxuXHRcdFx0XHRcdFx0Ly8gRXNjYXBlIHRhZ3MgaW4gRXJyb3IgbWVzc2FnZVxyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICc8YnI+PHN0cm9uZz5SZXNwb25zZTwvc3Ryb25nPjxkaXYgc3R5bGU9XCJwYWRkaW5nOiAwIDEwcHg7bWFyZ2luOiAwIDAgMTBweDtib3JkZXItcmFkaXVzOjNweDsgYm94LXNoYWRvdzowcHggMHB4IDFweCAjYTNhM2EzO1wiPicgKyBqcVhIUi5yZXNwb25zZVRleHQucmVwbGFjZSgvJi9nLCBcIiZhbXA7XCIpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0IC5yZXBsYWNlKC88L2csIFwiJmx0O1wiKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAucmVwbGFjZSgvPi9nLCBcIiZndDtcIilcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgLnJlcGxhY2UoL1wiL2csIFwiJnF1b3Q7XCIpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0IC5yZXBsYWNlKC8nL2csIFwiJiMzOTtcIilcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQrJzwvZGl2Pic7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlID0gZXJyb3JfbWVzc2FnZS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKTtcclxuXHJcblx0XHRcdFx0XHR2YXIgY2FsZW5kYXJfaWQgPSB3cGJjX2dldF9yZXNvdXJjZV9pZF9fZnJvbV9hanhfcG9zdF9kYXRhX3VybCggdGhpcy5kYXRhICk7XHJcblx0XHRcdFx0XHR2YXIganFfbm9kZSA9ICcjYm9va2luZ19mb3JtJyArIGNhbGVuZGFyX2lkO1xyXG5cclxuXHRcdFx0XHRcdC8vIFNob3cgTWVzc2FnZVxyXG5cdFx0XHRcdFx0d3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZSggZXJyb3JfbWVzc2FnZSAsIHsgJ3R5cGUnICAgICA6ICdlcnJvcicsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJzogeydqcV9ub2RlJzoganFfbm9kZSwgJ3doZXJlJzogJ2FmdGVyJ30sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnaXNfYXBwZW5kJzogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzdHlsZScgICAgOiAndGV4dC1hbGlnbjpsZWZ0OycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHRcdFx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggY2FsZW5kYXJfaWQgKTtcclxuXHRcdFx0ICBcdCB9XHJcblx0XHRcdFx0IC8vIDwvZWRpdG9yLWZvbGQ+XHJcblx0XHRcdCAgKVxyXG5cdCAgICAgICAgICAvLyAuZG9uZSggICBmdW5jdGlvbiAoIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnc2Vjb25kIHN1Y2Nlc3MnLCBkYXRhLCB0ZXh0U3RhdHVzLCBqcVhIUiApOyB9ICAgIH0pXHJcblx0XHRcdCAgLy8gLmFsd2F5cyggZnVuY3Rpb24gKCBkYXRhX2pxWEhSLCB0ZXh0U3RhdHVzLCBqcVhIUl9lcnJvclRocm93biApIHsgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ2Fsd2F5cyBmaW5pc2hlZCcsIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICk7IH0gICAgIH0pXHJcblx0XHRcdCAgOyAgLy8gRW5kIEFqYXhcclxuXHJcblx0cmV0dXJuIHRydWU7XHJcbn1cclxuXHJcblxyXG5cdC8vIDxlZGl0b3ItZm9sZCAgICAgZGVmYXVsdHN0YXRlPVwiY29sbGFwc2VkXCIgICAgICAgICAgICAgICAgICAgICAgICBkZXNjPVwiICA9PSAgQ0FQVENIQSA9PSAgXCIgID5cclxuXHJcblx0LyoqXHJcblx0ICogVXBkYXRlIGltYWdlIGluIGNhcHRjaGEgYW5kIHNob3cgd2FybmluZyBtZXNzYWdlXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gcGFyYW1zXHJcblx0ICpcclxuXHQgKiBFeGFtcGxlIG9mICdwYXJhbXMnIDoge1xyXG5cdCAqXHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnOiByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0sXHJcblx0ICpcdFx0XHRcdFx0XHRcdCd1cmwnICAgICAgICA6IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2NhcHRjaGFfX3NpbXBsZScgXVsgJ3VybCcgXSxcclxuXHQgKlx0XHRcdFx0XHRcdFx0J2NoYWxsZW5nZScgIDogcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnY2FwdGNoYV9fc2ltcGxlJyBdWyAnY2hhbGxlbmdlJyBdLFxyXG5cdCAqXHRcdFx0XHRcdFx0XHQnbWVzc2FnZScgICAgOiByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiIClcclxuXHQgKlx0XHRcdFx0XHRcdH1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhcHRjaGFfX3NpbXBsZV9fdXBkYXRlKCBwYXJhbXMgKXtcclxuXHJcblx0XHRkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2NhcHRjaGFfaW5wdXQnICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKS52YWx1ZSA9ICcnO1xyXG5cdFx0ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYXB0Y2hhX2ltZycgKyBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApLnNyYyA9IHBhcmFtc1sgJ3VybCcgXTtcclxuXHRcdGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BkZXZfY2FwdGNoYV9jaGFsbGVuZ2VfJyArIHBhcmFtc1sgJ3Jlc291cmNlX2lkJyBdICkudmFsdWUgPSBwYXJhbXNbICdjaGFsbGVuZ2UnIF07XHJcblxyXG5cdFx0Ly8gU2hvdyB3YXJuaW5nIFx0XHRBZnRlciBDQVBUQ0hBIEltZ1xyXG5cdFx0dmFyIG1lc3NhZ2VfaWQgPSB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlX193YXJuaW5nKCAnI2NhcHRjaGFfaW5wdXQnICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKyAnICsgaW1nJywgcGFyYW1zWyAnbWVzc2FnZScgXSApO1xyXG5cclxuXHRcdC8vIEFuaW1hdGVcclxuXHRcdGpRdWVyeSggJyMnICsgbWVzc2FnZV9pZCArICcsICcgKyAnI2NhcHRjaGFfaW5wdXQnICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKS5mYWRlT3V0KCAzNTAgKS5mYWRlSW4oIDMwMCApLmZhZGVPdXQoIDM1MCApLmZhZGVJbiggNDAwICkuYW5pbWF0ZSgge29wYWNpdHk6IDF9LCA0MDAwICk7XHJcblx0XHQvLyBGb2N1cyB0ZXh0ICBmaWVsZFxyXG5cdFx0alF1ZXJ5KCAnI2NhcHRjaGFfaW5wdXQnICsgcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKS50cmlnZ2VyKCAnZm9jdXMnICk7ICAgIFx0XHRcdFx0XHRcdFx0XHRcdC8vIEZpeEluOiA4LjcuMTEuMTIuXHJcblxyXG5cclxuXHRcdC8vIEVuYWJsZSBTdWJtaXQgfCBIaWRlIHNwaW4gbG9hZGVyXHJcblx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fb25fcmVzcG9uc2VfX3VpX2VsZW1lbnRzX2VuYWJsZSggcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBJZiB0aGUgY2FwdGNoYSBlbGVtZW50cyBub3QgZXhpc3QgIGluIHRoZSBib29raW5nIGZvcm0sICB0aGVuICByZW1vdmUgcGFyYW1ldGVycyByZWxhdGl2ZSBjYXB0Y2hhXHJcblx0ICogQHBhcmFtIHBhcmFtc1xyXG5cdCAqIEByZXR1cm5zIG9ialxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FwdGNoYV9fc2ltcGxlX19tYXliZV9yZW1vdmVfaW5fYWp4X3BhcmFtcyggcGFyYW1zICl7XHJcblxyXG5cdFx0aWYgKCAhIHdwYmNfY2FwdGNoYV9fc2ltcGxlX19pc19leGlzdF9pbl9mb3JtKCBwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSApICl7XHJcblx0XHRcdGRlbGV0ZSBwYXJhbXNbICdjYXB0Y2hhX2NoYWxhbmdlJyBdO1xyXG5cdFx0XHRkZWxldGUgcGFyYW1zWyAnY2FwdGNoYV91c2VyX2lucHV0JyBdO1xyXG5cdFx0fVxyXG5cdFx0cmV0dXJuIHBhcmFtcztcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBDaGVjayBpZiBDQVBUQ0hBIGV4aXN0IGluIHRoZSBib29raW5nIGZvcm1cclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhcHRjaGFfX3NpbXBsZV9faXNfZXhpc3RfaW5fZm9ybSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRyZXR1cm4gKFxyXG5cdFx0XHRcdFx0XHQoMCAhPT0galF1ZXJ5KCAnI3dwZGV2X2NhcHRjaGFfY2hhbGxlbmdlXycgKyByZXNvdXJjZV9pZCApLmxlbmd0aClcclxuXHRcdFx0XHRcdCB8fCAoMCAhPT0galF1ZXJ5KCAnI2NhcHRjaGFfaW5wdXQnICsgcmVzb3VyY2VfaWQgKS5sZW5ndGgpXHJcblx0XHRcdFx0KTtcclxuXHR9XHJcblxyXG5cdC8vIDwvZWRpdG9yLWZvbGQ+XHJcblxyXG5cclxuXHQvLyA8ZWRpdG9yLWZvbGQgICAgIGRlZmF1bHRzdGF0ZT1cImNvbGxhcHNlZFwiICAgICAgICAgICAgICAgICAgICAgICAgZGVzYz1cIiAgPT0gIFNlbmQgQnV0dG9uIHwgRm9ybSBTcGluIExvYWRlciAgPT0gIFwiICA+XHJcblxyXG5cdC8qKlxyXG5cdCAqIERpc2FibGUgU2VuZCBidXR0b24gIHwgIFNob3cgU3BpbiBMb2FkZXJcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19vbl9zdWJtaXRfX3VpX2VsZW1lbnRzX2Rpc2FibGUoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdFx0Ly8gRGlzYWJsZSBTdWJtaXRcclxuXHRcdHdwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZGlzYWJsZSggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHQvLyBTaG93IFNwaW4gbG9hZGVyIGluIGJvb2tpbmcgZm9ybVxyXG5cdFx0d3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19zaG93KCByZXNvdXJjZV9pZCApO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogRW5hYmxlIFNlbmQgYnV0dG9uICB8ICAgSGlkZSBTcGluIExvYWRlclxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX29uX3Jlc3BvbnNlX191aV9lbGVtZW50c19lbmFibGUocmVzb3VyY2VfaWQpe1xyXG5cclxuXHRcdC8vIEVuYWJsZSBTdWJtaXRcclxuXHRcdHdwYmNfYm9va2luZ19mb3JtX19zZW5kX2J1dHRvbl9fZW5hYmxlKCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdC8vIEhpZGUgU3BpbiBsb2FkZXIgaW4gYm9va2luZyBmb3JtXHJcblx0XHR3cGJjX2Jvb2tpbmdfZm9ybV9fc3Bpbl9sb2FkZXJfX2hpZGUoIHJlc291cmNlX2lkICk7XHJcblx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRW5hYmxlIFN1Ym1pdCBidXR0b25cclxuXHRcdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiB3cGJjX2Jvb2tpbmdfZm9ybV9fc2VuZF9idXR0b25fX2VuYWJsZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdC8vIEFjdGl2YXRlIFNlbmQgYnV0dG9uXHJcblx0XHRcdGpRdWVyeSggJyNib29raW5nX2Zvcm1fZGl2JyArIHJlc291cmNlX2lkICsgJyBpbnB1dFt0eXBlPWJ1dHRvbl0nICkucHJvcCggXCJkaXNhYmxlZFwiLCBmYWxzZSApO1xyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgYnV0dG9uJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgZmFsc2UgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIERpc2FibGUgU3VibWl0IGJ1dHRvbiAgYW5kIHNob3cgIHNwaW5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX3NlbmRfYnV0dG9uX19kaXNhYmxlKCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdFx0Ly8gRGlzYWJsZSBTZW5kIGJ1dHRvblxyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgaW5wdXRbdHlwZT1idXR0b25dJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgdHJ1ZSApO1xyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCArICcgYnV0dG9uJyApLnByb3AoIFwiZGlzYWJsZWRcIiwgdHJ1ZSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRGlzYWJsZSAnVGhpcycgYnV0dG9uXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIF90aGlzXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX190aGlzX2J1dHRvbl9fZGlzYWJsZSggX3RoaXMgKXtcclxuXHJcblx0XHRcdC8vIERpc2FibGUgU2VuZCBidXR0b25cclxuXHRcdFx0alF1ZXJ5KCBfdGhpcyApLnByb3AoIFwiZGlzYWJsZWRcIiwgdHJ1ZSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2hvdyBib29raW5nIGZvcm0gIFNwaW4gTG9hZGVyXHJcblx0XHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fX3NwaW5fbG9hZGVyX19zaG93KCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdFx0Ly8gU2hvdyBTcGluIExvYWRlclxyXG5cdFx0XHRqUXVlcnkoICcjYm9va2luZ19mb3JtJyArIHJlc291cmNlX2lkICkuYWZ0ZXIoXHJcblx0XHRcdFx0JzxkaXYgaWQ9XCJ3cGJjX2Jvb2tpbmdfZm9ybV9zcGluX2xvYWRlcicgKyByZXNvdXJjZV9pZCArICdcIiBjbGFzcz1cIndwYmNfYm9va2luZ19mb3JtX3NwaW5fbG9hZGVyXCIgc3R5bGU9XCJwb3NpdGlvbjogcmVsYXRpdmU7XCI+PGRpdiBjbGFzcz1cIndwYmNfc3BpbnNfbG9hZGVyX3dyYXBwZXJcIj48ZGl2IGNsYXNzPVwid3BiY19zcGluc19sb2FkZXJfbWluaVwiPjwvZGl2PjwvZGl2PjwvZGl2PidcclxuXHRcdFx0KTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFJlbW92ZSAvIEhpZGUgYm9va2luZyBmb3JtICBTcGluIExvYWRlclxyXG5cdFx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19zcGluX2xvYWRlcl9faGlkZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdC8vIFJlbW92ZSBTcGluIExvYWRlclxyXG5cdFx0XHRqUXVlcnkoICcjd3BiY19ib29raW5nX2Zvcm1fc3Bpbl9sb2FkZXInICsgcmVzb3VyY2VfaWQgKS5yZW1vdmUoKTtcclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBIaWRlIGJvb2tpbmcgZm9ybSB3dGggYW5pbWF0aW9uXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYm9va2luZ19mb3JtX19hbmltYXRlZF9faGlkZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRcdGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5oaWRlKCk7XHJcblx0XHR9XHJcblx0Ly8gPC9lZGl0b3ItZm9sZD5cclxuXHJcblxyXG5cdC8vIDxlZGl0b3ItZm9sZCAgICAgZGVmYXVsdHN0YXRlPVwiY29sbGFwc2VkXCIgICAgICAgICAgICAgICAgICAgICAgICBkZXNjPVwiICA9PSAgTWluaSBTcGluIExvYWRlciAgPT0gIFwiICA+XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHBhcmVudF9odG1sX2lkXHJcblx0XHQgKi9cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3cgbWljcm8gU3BpbiBMb2FkZXJcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gaWRcdFx0XHRcdFx0XHRJRCBvZiBMb2FkZXIsICBmb3IgbGF0ZXIgIGhpZGUgaXQgYnkgIHVzaW5nIFx0XHR3cGJjX19zcGluX2xvYWRlcl9fbWljcm9fX2hpZGUoIGlkICkgT1Igd3BiY19fc3Bpbl9sb2FkZXJfX21pbmlfX2hpZGUoIGlkIClcclxuXHRcdCAqIEBwYXJhbSBqcV9ub2RlX3doZXJlX2luc2VydFx0XHRzdWNoIGFzICcjZXN0aW1hdGVfYm9va2luZ19uaWdodF9jb3N0X2hpbnQxMCcgICBPUiAgJy5lc3RpbWF0ZV9ib29raW5nX25pZ2h0X2Nvc3RfaGludDEwJ1xyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiB3cGJjX19zcGluX2xvYWRlcl9fbWljcm9fX3Nob3dfX2luc2lkZSggaWQgLCBqcV9ub2RlX3doZXJlX2luc2VydCApe1xyXG5cclxuXHRcdFx0XHR3cGJjX19zcGluX2xvYWRlcl9fbWluaV9fc2hvdyggaWQsIHtcclxuXHRcdFx0XHRcdCdjb2xvcicgIDogJyM0NDQnLFxyXG5cdFx0XHRcdFx0J3Nob3dfaGVyZSc6IHtcclxuXHRcdFx0XHRcdFx0J3doZXJlJyAgOiAnaW5zaWRlJyxcclxuXHRcdFx0XHRcdFx0J2pxX25vZGUnOiBqcV9ub2RlX3doZXJlX2luc2VydFxyXG5cdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdCdzdHlsZScgICAgOiAncG9zaXRpb246IHJlbGF0aXZlO2Rpc3BsYXk6IGlubGluZS1mbGV4O2ZsZXgtZmxvdzogY29sdW1uIG5vd3JhcDtqdXN0aWZ5LWNvbnRlbnQ6IGNlbnRlcjthbGlnbi1pdGVtczogY2VudGVyO21hcmdpbjogN3B4IDEycHg7JyxcclxuXHRcdFx0XHRcdCdjbGFzcycgICAgOiAnd3BiY19vbmVfc3Bpbl9sb2FkZXJfbWljcm8nXHJcblx0XHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogUmVtb3ZlIHNwaW5uZXJcclxuXHRcdCAqIEBwYXJhbSBpZFxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiB3cGJjX19zcGluX2xvYWRlcl9fbWljcm9fX2hpZGUoIGlkICl7XHJcblx0XHQgICAgd3BiY19fc3Bpbl9sb2FkZXJfX21pbmlfX2hpZGUoIGlkICk7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2hvdyBtaW5pIFNwaW4gTG9hZGVyXHJcblx0XHQgKiBAcGFyYW0gcGFyZW50X2h0bWxfaWRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19fc3Bpbl9sb2FkZXJfX21pbmlfX3Nob3coIHBhcmVudF9odG1sX2lkICwgcGFyYW1zID0ge30gKXtcclxuXHJcblx0XHRcdHZhciBwYXJhbXNfZGVmYXVsdCA9IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0J2NvbG9yJyAgICA6ICcjMDA3MWNlJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZSc6IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQnanFfbm9kZSc6ICcnLFx0XHRcdFx0XHQvLyBhbnkgalF1ZXJ5IG5vZGUgZGVmaW5pdGlvblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCd3aGVyZScgIDogJ2FmdGVyJ1x0XHRcdFx0Ly8gJ2luc2lkZScgfCAnYmVmb3JlJyB8ICdhZnRlcicgfCAncmlnaHQnIHwgJ2xlZnQnXHJcblx0XHRcdFx0XHRcdFx0XHRcdH0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdCdzdHlsZScgICAgOiAncG9zaXRpb246IHJlbGF0aXZlO21pbi1oZWlnaHQ6IDIuOHJlbTsnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHQnY2xhc3MnICAgIDogJ3dwYmNfb25lX3NwaW5fbG9hZGVyX21pbmkgMHdwYmNfc3BpbnNfbG9hZGVyX21pbmknXHJcblx0XHRcdFx0XHRcdFx0XHR9O1xyXG5cdFx0XHRmb3IgKCB2YXIgcF9rZXkgaW4gcGFyYW1zICl7XHJcblx0XHRcdFx0cGFyYW1zX2RlZmF1bHRbIHBfa2V5IF0gPSBwYXJhbXNbIHBfa2V5IF07XHJcblx0XHRcdH1cclxuXHRcdFx0cGFyYW1zID0gcGFyYW1zX2RlZmF1bHQ7XHJcblxyXG5cdFx0XHRpZiAoICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChwYXJhbXNbJ2NvbG9yJ10pKSAmJiAoJycgIT0gcGFyYW1zWydjb2xvciddKSApe1xyXG5cdFx0XHRcdHBhcmFtc1snY29sb3InXSA9ICdib3JkZXItY29sb3I6JyArIHBhcmFtc1snY29sb3InXSArICc7JztcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIHNwaW5uZXJfaHRtbCA9ICc8ZGl2IGlkPVwid3BiY19taW5pX3NwaW5fbG9hZGVyJyArIHBhcmVudF9odG1sX2lkICsgJ1wiIGNsYXNzPVwid3BiY19ib29raW5nX2Zvcm1fc3Bpbl9sb2FkZXJcIiBzdHlsZT1cIicgKyBwYXJhbXNbICdzdHlsZScgXSArICdcIj48ZGl2IGNsYXNzPVwid3BiY19zcGluc19sb2FkZXJfd3JhcHBlclwiPjxkaXYgY2xhc3M9XCInICsgcGFyYW1zWyAnY2xhc3MnIF0gKyAnXCIgc3R5bGU9XCInICsgcGFyYW1zWyAnY29sb3InIF0gKyAnXCI+PC9kaXY+PC9kaXY+PC9kaXY+JztcclxuXHJcblx0XHRcdGlmICggJycgPT0gcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApe1xyXG5cdFx0XHRcdHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gPSAnIycgKyBwYXJlbnRfaHRtbF9pZDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gU2hvdyBTcGluIExvYWRlclxyXG5cdFx0XHRpZiAoICdhZnRlcicgPT0gcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnd2hlcmUnIF0gKXtcclxuXHRcdFx0XHRqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5hZnRlciggc3Bpbm5lcl9odG1sICk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0alF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkuaHRtbCggc3Bpbm5lcl9odG1sICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFJlbW92ZSAvIEhpZGUgbWluaSBTcGluIExvYWRlclxyXG5cdFx0ICogQHBhcmFtIHBhcmVudF9odG1sX2lkXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfX3NwaW5fbG9hZGVyX19taW5pX19oaWRlKCBwYXJlbnRfaHRtbF9pZCApe1xyXG5cclxuXHRcdFx0Ly8gUmVtb3ZlIFNwaW4gTG9hZGVyXHJcblx0XHRcdGpRdWVyeSggJyN3cGJjX21pbmlfc3Bpbl9sb2FkZXInICsgcGFyZW50X2h0bWxfaWQgKS5yZW1vdmUoKTtcclxuXHRcdH1cclxuXHJcblx0Ly8gPC9lZGl0b3ItZm9sZD5cclxuXHJcbi8vVE9ETzogd2hhdCAgYWJvdXQgc2hvd2luZyBvbmx5ICBUaGFuayB5b3UuIG1lc3NhZ2Ugd2l0aG91dCBwYXltZW50IGZvcm1zLlxyXG4vKipcclxuICogU2hvdyAnVGhhbmsgeW91Jy4gbWVzc2FnZSBhbmQgcGF5bWVudCBmb3Jtc1xyXG4gKlxyXG4gKiBAcGFyYW0gcmVzcG9uc2VfZGF0YVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zaG93X3RoYW5rX3lvdV9tZXNzYWdlX2FmdGVyX2Jvb2tpbmcoIHJlc3BvbnNlX2RhdGEgKXtcclxuXHJcblx0aWYgKFxyXG4gXHRcdCAgICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X2lzX3JlZGlyZWN0JyBdKSlcclxuXHRcdCYmICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3VybCcgXSkpXHJcblx0XHQmJiAoJ3BhZ2UnID09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfaXNfcmVkaXJlY3QnIF0pXHJcblx0XHQmJiAoJycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV91cmwnIF0pXHJcblx0KXtcclxuXHRcdGpRdWVyeSggJ2JvZHknICkudHJpZ2dlciggJ3dwYmNfYm9va2luZ19jcmVhdGVkJywgWyByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gLCByZXNwb25zZV9kYXRhIF0gKTtcdFx0XHQvLyBGaXhJbjogMTAuMC4wLjMwLlxyXG5cdFx0d2luZG93LmxvY2F0aW9uLmhyZWYgPSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3VybCcgXTtcclxuXHRcdHJldHVybjtcclxuXHR9XHJcblxyXG5cdHZhciByZXNvdXJjZV9pZCA9IHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXVxyXG5cdHZhciBjb25maXJtX2NvbnRlbnQgPScnO1xyXG5cclxuXHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfbWVzc2FnZScgXSkgKXtcclxuXHRcdFx0XHRcdCAgXHRcdFx0IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfbWVzc2FnZScgXSA9ICcnO1xyXG5cdH1cclxuXHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfcGF5bWVudF9wYXltZW50X2Rlc2NyaXB0aW9uJyBdICkgKXtcclxuXHRcdCBcdFx0XHQgIFx0XHRcdCByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbicgXSA9ICcnO1xyXG5cdH1cclxuXHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAncGF5bWVudF9jb3N0JyBdICkgKXtcclxuXHRcdFx0XHRcdCAgXHRcdFx0IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAncGF5bWVudF9jb3N0JyBdID0gJyc7XHJcblx0fVxyXG5cdGlmICggJ3VuZGVmaW5lZCcgPT09IHR5cGVvZiAocmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9wYXltZW50X2dhdGV3YXlzJyBdICkgKXtcclxuXHRcdFx0XHRcdCAgXHRcdFx0IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfcGF5bWVudF9nYXRld2F5cycgXSA9ICcnO1xyXG5cdH1cclxuXHR2YXIgdHlfbWVzc2FnZV9oaWRlIFx0XHRcdFx0XHRcdD0gKCcnID09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfbWVzc2FnZScgXSkgPyAnd3BiY190eV9oaWRlJyA6ICcnO1xyXG5cdHZhciB0eV9wYXltZW50X3BheW1lbnRfZGVzY3JpcHRpb25faGlkZSBcdD0gKCcnID09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfcGF5bWVudF9wYXltZW50X2Rlc2NyaXB0aW9uJyBdLnJlcGxhY2UoIC9cXFxcbi9nLCAnJyApKSA/ICd3cGJjX3R5X2hpZGUnIDogJyc7XHJcblx0dmFyIHR5X2Jvb2tpbmdfY29zdHNfaGlkZSBcdFx0XHRcdD0gKCcnID09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAncGF5bWVudF9jb3N0JyBdKSA/ICd3cGJjX3R5X2hpZGUnIDogJyc7XHJcblx0dmFyIHR5X3BheW1lbnRfZ2F0ZXdheXNfaGlkZSBcdFx0XHQ9ICgnJyA9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfZ2F0ZXdheXMnIF0ucmVwbGFjZSggL1xcXFxuL2csICcnICkpID8gJ3dwYmNfdHlfaGlkZScgOiAnJztcclxuXHJcblx0aWYgKCAnd3BiY190eV9oaWRlJyAhPSB0eV9wYXltZW50X2dhdGV3YXlzX2hpZGUgKXtcclxuXHRcdGpRdWVyeSggJy53cGJjX3R5X19jb250ZW50X3RleHQud3BiY190eV9fY29udGVudF9nYXRld2F5cycgKS5odG1sKCAnJyApO1x0Ly8gUmVzZXQgIGFsbCAgb3RoZXIgcG9zc2libGUgZ2F0ZXdheXMgYmVmb3JlIHNob3dpbmcgbmV3IG9uZS5cclxuXHR9XHJcblxyXG5cdGNvbmZpcm1fY29udGVudCArPSBgPGRpdiBpZD1cIndwYmNfc2Nyb2xsX3BvaW50XyR7cmVzb3VyY2VfaWR9XCI+PC9kaXY+YDtcclxuXHRjb25maXJtX2NvbnRlbnQgKz0gYCAgPGRpdiBjbGFzcz1cIndwYmNfYWZ0ZXJfYm9va2luZ190aGFua195b3Vfc2VjdGlvblwiPmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgPGRpdiBjbGFzcz1cIndwYmNfdHlfX21lc3NhZ2UgJHt0eV9tZXNzYWdlX2hpZGV9XCI+JHtyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X21lc3NhZ2UnIF19PC9kaXY+YDtcclxuICAgIGNvbmZpcm1fY29udGVudCArPSBgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250YWluZXJcIj5gO1xyXG5cdGlmICggJycgIT09IHJlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfbWVzc2FnZV9ib29raW5nX2lkJyBdICl7XHJcblx0XHRjb25maXJtX2NvbnRlbnQgKz0gYCAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19oZWFkZXJcIj4ke3Jlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfbWVzc2FnZV9ib29raW5nX2lkJyBdfTwvZGl2PmA7XHJcblx0fVxyXG4gICAgY29uZmlybV9jb250ZW50ICs9IGAgICAgICA8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGVudFwiPmA7XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY190eV9fcGF5bWVudF9kZXNjcmlwdGlvbiAke3R5X3BheW1lbnRfcGF5bWVudF9kZXNjcmlwdGlvbl9oaWRlfVwiPiR7cmVzcG9uc2VfZGF0YVsgJ2FqeF9jb25maXJtYXRpb24nIF1bICd0eV9wYXltZW50X3BheW1lbnRfZGVzY3JpcHRpb24nIF0ucmVwbGFjZSggL1xcXFxuL2csICcnICl9PC9kaXY+YDtcclxuXHRpZiAoICcnICE9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X2N1c3RvbWVyX2RldGFpbHMnIF0gKXtcclxuXHRcdGNvbmZpcm1fY29udGVudCArPSBgICAgICAgXHQ8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGVudF90ZXh0IHdwYmNfY29sc18yXCI+JHtyZXNwb25zZV9kYXRhWydhanhfY29uZmlybWF0aW9uJ11bJ3R5X2N1c3RvbWVyX2RldGFpbHMnXX08L2Rpdj5gO1xyXG5cdH1cclxuXHRpZiAoICcnICE9PSByZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X2Jvb2tpbmdfZGV0YWlscycgXSApe1xyXG5cdFx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgICBcdDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY19jb2xzXzJcIj4ke3Jlc3BvbnNlX2RhdGFbJ2FqeF9jb25maXJtYXRpb24nXVsndHlfYm9va2luZ19kZXRhaWxzJ119PC9kaXY+YDtcclxuXHR9XHJcblx0Y29uZmlybV9jb250ZW50ICs9IGAgICAgICAgIDxkaXYgY2xhc3M9XCJ3cGJjX3R5X19jb250ZW50X3RleHQgd3BiY190eV9fY29udGVudF9jb3N0cyAke3R5X2Jvb2tpbmdfY29zdHNfaGlkZX1cIj4ke3Jlc3BvbnNlX2RhdGFbICdhanhfY29uZmlybWF0aW9uJyBdWyAndHlfYm9va2luZ19jb3N0cycgXX08L2Rpdj5gO1xyXG5cdGNvbmZpcm1fY29udGVudCArPSBgICAgICAgICA8ZGl2IGNsYXNzPVwid3BiY190eV9fY29udGVudF90ZXh0IHdwYmNfdHlfX2NvbnRlbnRfZ2F0ZXdheXMgJHt0eV9wYXltZW50X2dhdGV3YXlzX2hpZGV9XCI+JHtyZXNwb25zZV9kYXRhWyAnYWp4X2NvbmZpcm1hdGlvbicgXVsgJ3R5X3BheW1lbnRfZ2F0ZXdheXMnIF0ucmVwbGFjZSggL1xcXFxuL2csICcnICkucmVwbGFjZSggL2FqYXhfc2NyaXB0L2dpLCAnc2NyaXB0JyApfTwvZGl2PmA7XHJcbiAgICBjb25maXJtX2NvbnRlbnQgKz0gYCAgICAgIDwvZGl2PmA7XHJcbiAgICBjb25maXJtX2NvbnRlbnQgKz0gYCAgICA8L2Rpdj5gO1xyXG5cdGNvbmZpcm1fY29udGVudCArPSBgPC9kaXY+YDtcclxuXHJcbiBcdGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKS5hZnRlciggY29uZmlybV9jb250ZW50ICk7XHJcblxyXG5cclxuXHQvL0ZpeEluOiAxMC4wLjAuMzBcdFx0Ly8gZXZlbnQgbmFtZVx0XHRcdC8vIFJlc291cmNlIElEXHQtXHQnMSdcclxuXHRqUXVlcnkoICdib2R5JyApLnRyaWdnZXIoICd3cGJjX2Jvb2tpbmdfY3JlYXRlZCcsIFsgcmVzb3VyY2VfaWQgLCByZXNwb25zZV9kYXRhIF0gKTtcclxuXHQvLyBUbyBjYXRjaCB0aGlzIGV2ZW50OiBqUXVlcnkoICdib2R5JyApLm9uKCd3cGJjX2Jvb2tpbmdfY3JlYXRlZCcsIGZ1bmN0aW9uKCBldmVudCwgcmVzb3VyY2VfaWQsIHBhcmFtcyApIHsgY29uc29sZS5sb2coIGV2ZW50LCByZXNvdXJjZV9pZCwgcGFyYW1zICk7IH0gKTtcclxufVxyXG4iXSwibWFwcGluZ3MiOiJBQUFBLFlBQVk7O0FBRVo7QUFDQTtBQUNBOztBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLFNBQVNBLHdCQUF3QkEsQ0FBRUMsTUFBTSxFQUFFO0VBRTFDQyxPQUFPLENBQUNDLGNBQWMsQ0FBRSwwQkFBMkIsQ0FBQztFQUNwREQsT0FBTyxDQUFDQyxjQUFjLENBQUUsd0JBQXlCLENBQUM7RUFDbERELE9BQU8sQ0FBQ0UsR0FBRyxDQUFFSCxNQUFPLENBQUM7RUFDckJDLE9BQU8sQ0FBQ0csUUFBUSxDQUFDLENBQUM7RUFFbEJKLE1BQU0sR0FBR0ssZ0RBQWdELENBQUVMLE1BQU8sQ0FBQzs7RUFFbkU7RUFDQU0sTUFBTSxDQUFFLE1BQU8sQ0FBQyxDQUFDQyxPQUFPLENBQUUsNEJBQTRCLEVBQUUsQ0FBRVAsTUFBTSxDQUFDLGFBQWEsQ0FBQyxFQUFFQSxNQUFNLENBQUcsQ0FBQzs7RUFFM0Y7RUFDQU0sTUFBTSxDQUFDRSxJQUFJLENBQUVDLGFBQWEsRUFDekI7SUFDQ0MsTUFBTSxFQUFtQiwwQkFBMEI7SUFDbkRDLGdCQUFnQixFQUFTQyxLQUFLLENBQUNDLGdCQUFnQixDQUFFLFNBQVUsQ0FBQztJQUM1REMsS0FBSyxFQUFvQkYsS0FBSyxDQUFDQyxnQkFBZ0IsQ0FBRSxPQUFRLENBQUM7SUFDMURFLGVBQWUsRUFBVUgsS0FBSyxDQUFDQyxnQkFBZ0IsQ0FBRSxRQUFTLENBQUM7SUFDM0RHLHVCQUF1QixFQUFFaEI7SUFDekI7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtFQUNFLENBQUM7RUFFQztBQUNKO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtFQUNJLFVBQVdp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Q25JZ0UsZUFBZSxJQUFJLHNDQUFzQztFQUM1RCxJQUFLLEVBQUUsS0FBS2hFLGFBQWEsQ0FBRSxrQkFBa0IsQ0FBRSxDQUFFLHVCQUF1QixDQUFFLEVBQUU7SUFDM0VnRSxlQUFlLElBQUksc0NBQXNDaEUsYUFBYSxDQUFFLGtCQUFrQixDQUFFLENBQUUsdUJBQXVCLENBQUUsUUFBUTtFQUNoSTtFQUNHZ0UsZUFBZSxJQUFJLHNDQUFzQztFQUM1REEsZUFBZSxJQUFJLDBFQUEwRUUsbUNBQW1DLEtBQUtsRSxhQUFhLENBQUUsa0JBQWtCLENBQUUsQ0FBRSxnQ0FBZ0MsQ0FBRSxDQUFDVyxPQUFPLENBQUUsTUFBTSxFQUFFLEVBQUcsQ0FBQyxRQUFRO0VBQzFPLElBQUssRUFBRSxLQUFLWCxhQUFhLENBQUUsa0JBQWtCLENBQUUsQ0FBRSxxQkFBcUIsQ0FBRSxFQUFFO0lBQ3pFZ0UsZUFBZSxJQUFJLHlEQUF5RGhFLGFBQWEsQ0FBQyxrQkFBa0IsQ0FBQyxDQUFDLHFCQUFxQixDQUFDLFFBQVE7RUFDN0k7RUFDQSxJQUFLLEVBQUUsS0FBS0EsYUFBYSxDQUFFLGtCQUFrQixDQUFFLENBQUUsb0JBQW9CLENBQUUsRUFBRTtJQUN4RWdFLGVBQWUsSUFBSSx5REFBeURoRSxhQUFhLENBQUMsa0JBQWtCLENBQUMsQ0FBQyxvQkFBb0IsQ0FBQyxRQUFRO0VBQzVJO0VBQ0FnRSxlQUFlLElBQUksb0VBQW9FRyxxQkFBcUIsS0FBS25FLGFBQWEsQ0FBRSxrQkFBa0IsQ0FBRSxDQUFFLGtCQUFrQixDQUFFLFFBQVE7RUFDbExnRSxlQUFlLElBQUksdUVBQXVFSSx3QkFBd0IsS0FBS3BFLGFBQWEsQ0FBRSxrQkFBa0IsQ0FBRSxDQUFFLHFCQUFxQixDQUFFLENBQUNXLE9BQU8sQ0FBRSxNQUFNLEVBQUUsRUFBRyxDQUFDLENBQUNBLE9BQU8sQ0FBRSxlQUFlLEVBQUUsUUFBUyxDQUFDLFFBQVE7RUFDblBxRCxlQUFlLElBQUksY0FBYztFQUNqQ0EsZUFBZSxJQUFJLFlBQVk7RUFDbENBLGVBQWUsSUFBSSxRQUFRO0VBRTFCM0UsTUFBTSxDQUFFLGVBQWUsR0FBR2tELFdBQVksQ0FBQyxDQUFDUyxLQUFLLENBQUVnQixlQUFnQixDQUFDOztFQUdqRTtFQUNBM0UsTUFBTSxDQUFFLE1BQU8sQ0FBQyxDQUFDQyxPQUFPLENBQUUsc0JBQXNCLEVBQUUsQ0FBRWlELFdBQVcsRUFBR3ZDLGFBQWEsQ0FBRyxDQUFDO0VBQ25GO0FBQ0QiLCJpZ25vcmVMaXN0IjpbXX0=
