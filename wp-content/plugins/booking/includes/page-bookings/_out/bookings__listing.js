"use strict";

jQuery('body').on({
  'touchmove': function (e) {
    jQuery('.timespartly').each(function (index) {
      var td_el = jQuery(this).get(0);
      if (undefined != td_el._tippy) {
        var instance = td_el._tippy;
        instance.hide();
      }
    });
  }
});

/**
 * Request Object
 * Here we can  define Search parameters and Update it later,  when  some parameter was changed
 *
 */
var wpbc_ajx_booking_listing = function (obj, $) {
  // Secure parameters for Ajax	------------------------------------------------------------------------------------
  var p_secure = obj.security_obj = obj.security_obj || {
    user_id: 0,
    nonce: '',
    locale: ''
  };
  obj.set_secure_param = function (param_key, param_val) {
    p_secure[param_key] = param_val;
  };
  obj.get_secure_param = function (param_key) {
    return p_secure[param_key];
  };

  // Listing Search parameters	------------------------------------------------------------------------------------
  var p_listing = obj.search_request_obj = obj.search_request_obj || {
    sort: "booking_id",
    sort_type: "DESC",
    page_num: 1,
    page_items_count: 10,
    create_date: "",
    keyword: "",
    source: ""
  };
  obj.search_set_all_params = function (request_param_obj) {
    p_listing = request_param_obj;
  };
  obj.search_get_all_params = function () {
    return p_listing;
  };
  obj.search_get_param = function (param_key) {
    return p_listing[param_key];
  };
  obj.search_set_param = function (param_key, param_val) {
    // if ( Array.isArray( param_val ) ){
    // 	param_val = JSON.stringify( param_val );
    // }
    p_listing[param_key] = param_val;
  };
  obj.search_set_params_arr = function (params_arr) {
    _.each(params_arr, function (p_val, p_key, p_data) {
      // Define different Search  parameters for request
      this.search_set_param(p_key, p_val);
    });
  };

  // Other parameters 			------------------------------------------------------------------------------------
  var p_other = obj.other_obj = obj.other_obj || {};
  obj.set_other_param = function (param_key, param_val) {
    p_other[param_key] = param_val;
  };
  obj.get_other_param = function (param_key) {
    return p_other[param_key];
  };
  return obj;
}(wpbc_ajx_booking_listing || {}, jQuery);

/**
 *   Ajax  ------------------------------------------------------------------------------------------------------ */

/**
 * Send Ajax search request
 * for searching specific Keyword and other params
 */
function wpbc_ajx_booking_ajax_search_request() {
  console.groupCollapsed('AJX_BOOKING_LISTING');
  console.log(' == Before Ajax Send - search_get_all_params() == ', wpbc_ajx_booking_listing.search_get_all_params());
  wpbc_booking_listing_reload_button__spin_start();

  /*
  //FixIn: forVideo
  if ( ! is_this_action ){
  	//wpbc_ajx_booking__actual_listing__hide();
  	jQuery( wpbc_ajx_booking_listing.get_other_param( 'listing_container' ) ).html(
  		'<div style="width:100%;text-align: center;" id="wpbc_loading_section"><span class="wpbc_icn_autorenew wpbc_spin"></span></div>'
  		+ jQuery( wpbc_ajx_booking_listing.get_other_param( 'listing_container' ) ).html()
  	);
  	if ( 'function' === typeof (jQuery( '#wpbc_loading_section' ).wpbc_my_modal) ){			// FixIn: 9.0.1.5.
  		jQuery( '#wpbc_loading_section' ).wpbc_my_modal( 'show' );
  	} else {
  		alert( 'Warning! Booking Calendar. Its seems that  you have deactivated loading of Bootstrap JS files at Booking Settings General page in Advanced section.' )
  	}
  }
  is_this_action = false;
  */
  // Start Ajax
  jQuery.post(wpbc_url_ajax, {
    action: 'WPBC_AJX_BOOKING_LISTING',
    wpbc_ajx_user_id: wpbc_ajx_booking_listing.get_secure_param('user_id'),
    nonce: wpbc_ajx_booking_listing.get_secure_param('nonce'),
    wpbc_ajx_locale: wpbc_ajx_booking_listing.get_secure_param('locale'),
    search_params: wpbc_ajx_booking_listing.search_get_all_params()
  },
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    //FixIn: forVideo
    //jQuery( '#wpbc_loading_section' ).wpbc_my_modal( 'hide' );

    console.log(' == Response WPBC_AJX_BOOKING_LISTING == ', response_data);
    console.groupEnd();
    // Probably Error
    if (typeof response_data !== 'object' || response_data === null) {
      jQuery('.wpbc_ajx_under_toolbar_row').hide(); // FixIn: 9.6.1.5.
      jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html('<div class="wpbc-settings-notice notice-warning" style="text-align:left">' + response_data + '</div>');
      return;
    }

    // Reload page, after filter toolbar was reseted
    if (undefined != response_data['ajx_cleaned_params'] && 'reset_done' === response_data['ajx_cleaned_params']['ui_reset']) {
      window.location.href = response_data['ajx_cleaned_params']['ui_reset_url'];
      // location.reload();
      return;
    }

    // Show listing
    if (response_data['ajx_count'] > 0) {
      wpbc_ajx_booking_show_listing(response_data['ajx_items'], response_data['ajx_search_params'], response_data['ajx_booking_resources']);
      wpbc_pagination_echo(wpbc_ajx_booking_listing.get_other_param('pagination_container'), wpbc_ajx_booking_listing.get_other_param('pagination_container_header'), wpbc_ajx_booking_listing.get_other_param('pagination_container_footer'), {
        'page_active': response_data['ajx_search_params']['page_num'],
        'pages_count': Math.ceil(response_data['ajx_count'] / response_data['ajx_search_params']['page_items_count']),
        'page_items_count': response_data['ajx_search_params']['page_items_count'],
        'sort_type': response_data['ajx_search_params']['sort_type'],
        'total_count': response_data['ajx_count']
      });
      wpbc_ajx_booking_define_ui_hooks(); // Redefine Hooks, because we show new DOM elements
    } else {
      wpbc_ajx_booking__actual_listing__hide();
      jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html('<div class="wpbc-settings-notice0 notice-warning0" style="text-align:center;font-size: 15px;margin: 2em 0;">' + '<p><strong>No results found for current filter options...</strong></p>' + '<p><strong><a  href="javascript:void(0)" ' + ' onclick="javascript:wpbc_ajx_booking_send_search_request_with_params( {' + ' \'ui_reset\': \'make_reset\', ' + ' \'page_num\': 1 ' + '} );">Reset filters</a> to show all bookings.</strong></p>' + '</div>');
    }

    // Update new booking count
    if (undefined !== response_data['ajx_new_bookings_count']) {
      var ajx_new_bookings_count = parseInt(response_data['ajx_new_bookings_count']);
      if (ajx_new_bookings_count > 0) {
        jQuery('.wpbc_badge_count').show();
      }
      jQuery('.bk-update-count').html(ajx_new_bookings_count);
    }
    wpbc_booking_listing_reload_button__spin_pause();
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
 *   Views  ----------------------------------------------------------------------------------------------------- */

/**
 * Show Listing Table 		and define gMail checkbox hooks
 *
 * @param json_items_arr		- JSON object with Items
 * @param json_search_params	- JSON object with Search
 */
function wpbc_ajx_booking_show_listing(json_items_arr, json_search_params, json_booking_resources) {
  wpbc_ajx_define_templates__resource_manipulation(json_items_arr, json_search_params, json_booking_resources);

  //console.log( 'json_items_arr' , json_items_arr, json_search_params );
  jQuery('.wpbc_ajx_under_toolbar_row').css("display", "flex"); // FixIn: 9.6.1.5.
  var list_header_tpl = wp.template('wpbc_ajx_booking_list_header');
  var list_footer_tpl = wp.template('wpbc_ajx_booking_list_footer');
  var list_row_tpl = wp.template('wpbc_ajx_booking_list_row');

  // Header.
  jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html(list_header_tpl());
  // Send to template all request params: jQuery( wpbc_ajx_booking_listing.get_other_param( 'listing_container' ) ).html( list_header_tpl(wpbc_ajx_booking_listing.search_get_all_params()) );
  // Body.
  jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).append('<div class="wpbc_selectable_body"></div>');
  // Footer.
  jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).append(list_footer_tpl());

  // R o w s
  console.groupCollapsed('LISTING_ROWS'); // LISTING_ROWS
  _.each(json_items_arr, function (p_val, p_key, p_data) {
    if ('undefined' !== typeof json_search_params['keyword']) {
      // Parameter for marking keyword with different color in a list
      p_val['__search_request_keyword__'] = json_search_params['keyword'];
    } else {
      p_val['__search_request_keyword__'] = '';
    }
    p_val['booking_resources'] = json_booking_resources;
    jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container') + ' .wpbc_selectable_body').append(list_row_tpl(p_val));
  });
  console.groupEnd(); // LISTING_ROWS

  wpbc_define_gmail_checkbox_selection(jQuery); // Redefine Hooks for clicking at Checkboxes
}

/**
 * Define template for changing booking resources &  update it each time,  when  listing updating, useful  for showing actual  booking resources.
 *
 * @param json_items_arr		- JSON object with Items
 * @param json_search_params	- JSON object with Search
 * @param json_booking_resources	- JSON object with Resources
 */
function wpbc_ajx_define_templates__resource_manipulation(json_items_arr, json_search_params, json_booking_resources) {
  // -------------------------------------------------------------------------------------------------------------
  // New. 2025-04-21.
  // -------------------------------------------------------------------------------------------------------------
  // Change booking resource in Modal.
  var modal__change_booking_resource = wp.template('wpbc_ajx__modal__change_booking_resource');
  jQuery('#section_in_in_modal__change_booking_resource').html(modal__change_booking_resource({
    'ajx_search_params': json_search_params,
    'ajx_booking_resources': json_booking_resources
  }));

  // Duplicate booking into another resource in Modal. New. 2025-04-21.
  var modal__duplicate_booking_to_other_resource = wp.template('wpbc_ajx__modal__duplicate_booking_to_other_resource');
  jQuery('#section_in_in_modal__duplicate_booking_to_other_resource').html(modal__duplicate_booking_to_other_resource({
    'ajx_search_params': json_search_params,
    'ajx_booking_resources': json_booking_resources
  }));
  // -------------------------------------------------------------------------------------------------------------
}

/**
 * Show just message instead of listing and hide pagination
 */
function wpbc_ajx_booking_show_message(message) {
  wpbc_ajx_booking__actual_listing__hide();
  jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html('<div class="wpbc-settings-notice notice-warning" style="text-align:left">' + message + '</div>');
}

/**
 *   H o o k s  -  its Action/Times when need to re-Render Views  ----------------------------------------------- */

/**
 * Send Ajax Search Request after Updating search request parameters
 *
 * @param params_arr
 */
function wpbc_ajx_booking_send_search_request_with_params(params_arr) {
  // Define different Search  parameters for request
  _.each(params_arr, function (p_val, p_key, p_data) {
    //console.log( 'Request for: ', p_key, p_val );
    wpbc_ajx_booking_listing.search_set_param(p_key, p_val);
  });

  // Send Ajax Request
  wpbc_ajx_booking_ajax_search_request();
}

/**
 * Search request for "Page Number"
 * @param page_number	int
 */
function wpbc_ajx_booking_pagination_click(page_number) {
  wpbc_ajx_booking_send_search_request_with_params({
    'page_num': page_number
  });
}

/**
 *   Keyword Searching  ----------------------------------------------------------------------------------------- */

/**
 * Search request for "Keyword", also set current page to  1
 *
 * @param element_id	-	HTML ID  of element,  where was entered keyword
 */
function wpbc_ajx_booking_send_search_request_for_keyword(element_id) {
  // We need to Reset page_num to 1 with each new search, because we can be at page #4,  but after  new search  we can  have totally  only  1 page
  wpbc_ajx_booking_send_search_request_with_params({
    'keyword': jQuery(element_id).val(),
    'page_num': 1
  });
}

/**
 * Send search request after few seconds (usually after 1,5 sec)
 * Closure function. Its useful,  for do  not send too many Ajax requests, when someone make fast typing.
 */
var wpbc_ajx_booking_searching_after_few_seconds = function () {
  var closed_timer = 0;
  return function (element_id, timer_delay) {
    // Get default value of "timer_delay",  if parameter was not passed into the function.
    timer_delay = typeof timer_delay !== 'undefined' ? timer_delay : 1500;
    clearTimeout(closed_timer); // Clear previous timer

    // Start new Timer
    closed_timer = setTimeout(wpbc_ajx_booking_send_search_request_for_keyword.bind(null, element_id), timer_delay);
  };
}();

/**
 *   Define Dynamic Hooks  (like pagination click, which renew each time with new listing showing)  ------------- */

/**
 * Define HTML ui Hooks: on KeyUp | Change | -> Sort Order & Number Items / Page
 * We are hcnaged it each  time, when showing new listing, because DOM elements chnaged
 */
function wpbc_ajx_booking_define_ui_hooks() {
  if ('function' === typeof wpbc_define_tippy_tooltips) {
    wpbc_define_tippy_tooltips('.wpbc__list__table ');
  }
  wpbc_ajx_booking__ui_define__locale();
  wpbc_ajx_booking__ui_define__remark();
  wpbc_boo_listing__init_hook__sort_by();

  // Items Per Page.
  jQuery('.wpbc_items_per_page').on('change', function (event) {
    wpbc_ajx_booking_send_search_request_with_params({
      'page_items_count': jQuery(this).val(),
      'page_num': 1
    });
  });

  // Sorting.
  jQuery('.wpbc_items_sort_type').on('change', function (event) {
    wpbc_ajx_booking_send_search_request_with_params({
      'sort_type': jQuery(this).val()
    });
  });
}

/**
 *   Show / Hide Listing  --------------------------------------------------------------------------------------- */

/**
 *  Show Listing Table 	- 	Sending Ajax Request	-	with parameters that  we early  defined in "wpbc_ajx_booking_listing" Obj.
 */
function wpbc_ajx_booking__actual_listing__show() {
  wpbc_ajx_booking_ajax_search_request(); // Send Ajax Request	-	with parameters that  we early  defined in "wpbc_ajx_booking_listing" Obj.
}

/**
 * Hide Listing Table ( and Pagination )
 */
function wpbc_ajx_booking__actual_listing__hide() {
  jQuery('.wpbc_ajx_under_toolbar_row').hide(); // FixIn: 9.6.1.5.
  jQuery(wpbc_ajx_booking_listing.get_other_param('listing_container')).html('');
  jQuery(wpbc_ajx_booking_listing.get_other_param('pagination_container')).html('');
}

/**
 *   Support functions for Content Template data  --------------------------------------------------------------- */

/**
 * Highlight strings,
 * by inserting <span class="fieldvalue name fieldsearchvalue">...</span> html  elements into the string.
 * @param {string} booking_details 	- Source string
 * @param {string} booking_keyword	- Keyword to highlight
 * @returns {string}
 */
function wpbc_get_highlighted_search_keyword(booking_details, booking_keyword) {
  booking_keyword = booking_keyword.trim().toLowerCase();
  if (0 == booking_keyword.length) {
    return booking_details;
  }

  // Highlight substring withing HTML tags in "Content of booking fields data" -- e.g. starting from  >  and ending with <
  let keywordRegex = new RegExp(`fieldvalue[^<>]*>([^<]*${booking_keyword}[^<]*)`, 'gim');

  //let matches = [...booking_details.toLowerCase().matchAll( keywordRegex )];
  let matches = booking_details.toLowerCase().matchAll(keywordRegex);
  matches = Array.from(matches);
  let strings_arr = [];
  let pos_previous = 0;
  let search_pos_start;
  let search_pos_end;
  for (const match of matches) {
    search_pos_start = match.index + match[0].toLowerCase().indexOf('>', 0) + 1;
    strings_arr.push(booking_details.substr(pos_previous, search_pos_start - pos_previous));
    search_pos_end = booking_details.toLowerCase().indexOf('<', search_pos_start);
    strings_arr.push('<span class="fieldvalue name fieldsearchvalue">' + booking_details.substr(search_pos_start, search_pos_end - search_pos_start) + '</span>');
    pos_previous = search_pos_end;
  }
  strings_arr.push(booking_details.substr(pos_previous, booking_details.length - pos_previous));
  return strings_arr.join('');
}

/**
 * Convert special HTML characters   from:	 &amp; 	-> 	&
 *
 * @param text
 * @returns {*}
 */
function wpbc_decode_HTML_entities(text) {
  var textArea = document.createElement('textarea');
  textArea.innerHTML = text;
  return textArea.value;
}

/**
 * Convert TO special HTML characters   from:	 & 	-> 	&amp;
 *
 * @param text
 * @returns {*}
 */
function wpbc_encode_HTML_entities(text) {
  var textArea = document.createElement('textarea');
  textArea.innerText = text;
  return textArea.innerHTML;
}

/**
 *   Support Functions - Spin Icon in Buttons  ------------------------------------------------------------------ */

/**
 * Spin button in Filter toolbar  -  Start
 */
function wpbc_booking_listing_reload_button__spin_start() {
  jQuery('#wpbc_booking_listing_reload_button .menu_icon.wpbc_spin').removeClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  Pause
 */
function wpbc_booking_listing_reload_button__spin_pause() {
  jQuery('#wpbc_booking_listing_reload_button .menu_icon.wpbc_spin').addClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  is Spinning ?
 *
 * @returns {boolean}
 */
function wpbc_booking_listing_reload_button__is_spin() {
  if (jQuery('#wpbc_booking_listing_reload_button .menu_icon.wpbc_spin').hasClass('wpbc_animation_pause')) {
    return true;
  } else {
    return false;
  }
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1ib29raW5ncy9fb3V0L2Jvb2tpbmdzX19saXN0aW5nLmpzIiwibmFtZXMiOlsialF1ZXJ5Iiwib24iLCJ0b3VjaG1vdmUiLCJlIiwiZWFjaCIsImluZGV4IiwidGRfZWwiLCJnZXQiLCJ1bmRlZmluZWQiLCJfdGlwcHkiLCJpbnN0YW5jZSIsImhpZGUiLCJ3cGJjX2FqeF9ib29raW5nX2xpc3RpbmciLCJvYmoiLCIkIiwicF9zZWN1cmUiLCJzZWN1cml0eV9vYmoiLCJ1c2VyX2lkIiwibm9uY2UiLCJsb2NhbGUiLCJzZXRfc2VjdXJlX3BhcmFtIiwicGFyYW1fa2V5IiwicGFyYW1fdmFsIiwiZ2V0X3NlY3VyZV9wYXJhbSIsInBfbGlzdGluZyIsInNlYXJjaF9yZXF1ZXN0X29iaiIsInNvcnQiLCJzb3J0X3R5cGUiLCJwYWdlX251bSIsInBhZ2VfaXRlbXNfY291bnQiLCJjcmVhdGVfZGF0ZSIsImtleXdvcmQiLCJzb3VyY2UiLCJzZWFyY2hfc2V0X2FsbF9wYXJhbXMiLCJyZXF1ZXN0X3BhcmFtX29iaiIsInNlYXJjaF9nZXRfYWxsX3BhcmFtcyIsInNlYXJjaF9nZXRfcGFyYW0iLCJzZWFyY2hfc2V0X3BhcmFtIiwic2VhcmNoX3NldF9wYXJhbXNfYXJyIiwicGFyYW1zX2FyciIsIl8iLCJwX3ZhbCIsInBfa2V5IiwicF9kYXRhIiwicF9vdGhlciIsIm90aGVyX29iaiIsInNldF9vdGhlcl9wYXJhbSIsImdldF9vdGhlcl9wYXJhbSIsIndwYmNfYWp4X2Jvb2tpbmdfYWpheF9zZWFyY2hfcmVxdWVzdCIsImNvbnNvbGUiLCJncm91cENvbGxhcHNlZCIsImxvZyIsIndwYmNfYm9va2luZ19saXN0aW5nX3JlbG9hZF9idXR0b25fX3NwaW5fc3RhcnQiLCJwb3N0Iiwid3BiY191cmxfYWpheCIsImFjdGlvbiIsIndwYmNfYWp4X3VzZXJfaWQiLCJ3cGJjX2FqeF9sb2NhbGUiLCJzZWFyY2hfcGFyYW1zIiwicmVzcG9uc2VfZGF0YSIsInRleHRTdGF0dXMiLCJqcVhIUiIsImdyb3VwRW5kIiwiaHRtbCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsIndwYmNfYWp4X2Jvb2tpbmdfc2hvd19saXN0aW5nIiwid3BiY19wYWdpbmF0aW9uX2VjaG8iLCJNYXRoIiwiY2VpbCIsIndwYmNfYWp4X2Jvb2tpbmdfZGVmaW5lX3VpX2hvb2tzIiwid3BiY19hanhfYm9va2luZ19fYWN0dWFsX2xpc3RpbmdfX2hpZGUiLCJhanhfbmV3X2Jvb2tpbmdzX2NvdW50IiwicGFyc2VJbnQiLCJzaG93Iiwid3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSIsImZhaWwiLCJlcnJvclRocm93biIsImVycm9yX21lc3NhZ2UiLCJyZXNwb25zZVRleHQiLCJyZXBsYWNlIiwid3BiY19hanhfYm9va2luZ19zaG93X21lc3NhZ2UiLCJqc29uX2l0ZW1zX2FyciIsImpzb25fc2VhcmNoX3BhcmFtcyIsImpzb25fYm9va2luZ19yZXNvdXJjZXMiLCJ3cGJjX2FqeF9kZWZpbmVfdGVtcGxhdGVzX19yZXNvdXJjZV9tYW5pcHVsYXRpb24iLCJjc3MiLCJsaXN0X2hlYWRlcl90cGwiLCJ3cCIsInRlbXBsYXRlIiwibGlzdF9mb290ZXJfdHBsIiwibGlzdF9yb3dfdHBsIiwiYXBwZW5kIiwid3BiY19kZWZpbmVfZ21haWxfY2hlY2tib3hfc2VsZWN0aW9uIiwibW9kYWxfX2NoYW5nZV9ib29raW5nX3Jlc291cmNlIiwibW9kYWxfX2R1cGxpY2F0ZV9ib29raW5nX3RvX290aGVyX3Jlc291cmNlIiwibWVzc2FnZSIsIndwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcyIsIndwYmNfYWp4X2Jvb2tpbmdfcGFnaW5hdGlvbl9jbGljayIsInBhZ2VfbnVtYmVyIiwid3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X2Zvcl9rZXl3b3JkIiwiZWxlbWVudF9pZCIsInZhbCIsIndwYmNfYWp4X2Jvb2tpbmdfc2VhcmNoaW5nX2FmdGVyX2Zld19zZWNvbmRzIiwiY2xvc2VkX3RpbWVyIiwidGltZXJfZGVsYXkiLCJjbGVhclRpbWVvdXQiLCJzZXRUaW1lb3V0IiwiYmluZCIsIndwYmNfZGVmaW5lX3RpcHB5X3Rvb2x0aXBzIiwid3BiY19hanhfYm9va2luZ19fdWlfZGVmaW5lX19sb2NhbGUiLCJ3cGJjX2FqeF9ib29raW5nX191aV9kZWZpbmVfX3JlbWFyayIsIndwYmNfYm9vX2xpc3RpbmdfX2luaXRfaG9va19fc29ydF9ieSIsImV2ZW50Iiwid3BiY19hanhfYm9va2luZ19fYWN0dWFsX2xpc3RpbmdfX3Nob3ciLCJ3cGJjX2dldF9oaWdobGlnaHRlZF9zZWFyY2hfa2V5d29yZCIsImJvb2tpbmdfZGV0YWlscyIsImJvb2tpbmdfa2V5d29yZCIsInRyaW0iLCJ0b0xvd2VyQ2FzZSIsImxlbmd0aCIsImtleXdvcmRSZWdleCIsIlJlZ0V4cCIsIm1hdGNoZXMiLCJtYXRjaEFsbCIsIkFycmF5IiwiZnJvbSIsInN0cmluZ3NfYXJyIiwicG9zX3ByZXZpb3VzIiwic2VhcmNoX3Bvc19zdGFydCIsInNlYXJjaF9wb3NfZW5kIiwibWF0Y2giLCJpbmRleE9mIiwicHVzaCIsInN1YnN0ciIsImpvaW4iLCJ3cGJjX2RlY29kZV9IVE1MX2VudGl0aWVzIiwidGV4dCIsInRleHRBcmVhIiwiZG9jdW1lbnQiLCJjcmVhdGVFbGVtZW50IiwiaW5uZXJIVE1MIiwidmFsdWUiLCJ3cGJjX2VuY29kZV9IVE1MX2VudGl0aWVzIiwiaW5uZXJUZXh0IiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIndwYmNfYm9va2luZ19saXN0aW5nX3JlbG9hZF9idXR0b25fX2lzX3NwaW4iLCJoYXNDbGFzcyJdLCJzb3VyY2VzIjpbImluY2x1ZGVzL3BhZ2UtYm9va2luZ3MvX3NyYy9ib29raW5nc19fbGlzdGluZy5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuXHJcbmpRdWVyeSgnYm9keScpLm9uKHtcclxuICAgICd0b3VjaG1vdmUnOiBmdW5jdGlvbihlKSB7XHJcblxyXG5cdFx0alF1ZXJ5KCAnLnRpbWVzcGFydGx5JyApLmVhY2goIGZ1bmN0aW9uICggaW5kZXggKXtcclxuXHJcblx0XHRcdHZhciB0ZF9lbCA9IGpRdWVyeSggdGhpcyApLmdldCggMCApO1xyXG5cclxuXHRcdFx0aWYgKCAodW5kZWZpbmVkICE9IHRkX2VsLl90aXBweSkgKXtcclxuXHJcblx0XHRcdFx0dmFyIGluc3RhbmNlID0gdGRfZWwuX3RpcHB5O1xyXG5cdFx0XHRcdGluc3RhbmNlLmhpZGUoKTtcclxuXHRcdFx0fVxyXG5cdFx0fSApO1xyXG5cdH1cclxufSk7XHJcblxyXG4vKipcclxuICogUmVxdWVzdCBPYmplY3RcclxuICogSGVyZSB3ZSBjYW4gIGRlZmluZSBTZWFyY2ggcGFyYW1ldGVycyBhbmQgVXBkYXRlIGl0IGxhdGVyLCAgd2hlbiAgc29tZSBwYXJhbWV0ZXIgd2FzIGNoYW5nZWRcclxuICpcclxuICovXHJcbnZhciB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcgPSAoZnVuY3Rpb24gKCBvYmosICQpIHtcclxuXHJcblx0Ly8gU2VjdXJlIHBhcmFtZXRlcnMgZm9yIEFqYXhcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX3NlY3VyZSA9IG9iai5zZWN1cml0eV9vYmogPSBvYmouc2VjdXJpdHlfb2JqIHx8IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0dXNlcl9pZDogMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bm9uY2UgIDogJycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdGxvY2FsZSA6ICcnXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH07XHJcblxyXG5cdG9iai5zZXRfc2VjdXJlX3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXksIHBhcmFtX3ZhbCApIHtcclxuXHRcdHBfc2VjdXJlWyBwYXJhbV9rZXkgXSA9IHBhcmFtX3ZhbDtcclxuXHR9O1xyXG5cclxuXHRvYmouZ2V0X3NlY3VyZV9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5ICkge1xyXG5cdFx0cmV0dXJuIHBfc2VjdXJlWyBwYXJhbV9rZXkgXTtcclxuXHR9O1xyXG5cclxuXHJcblx0Ly8gTGlzdGluZyBTZWFyY2ggcGFyYW1ldGVyc1x0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfbGlzdGluZyA9IG9iai5zZWFyY2hfcmVxdWVzdF9vYmogPSBvYmouc2VhcmNoX3JlcXVlc3Rfb2JqIHx8IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0c29ydCAgICAgICAgICAgIDogXCJib29raW5nX2lkXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHNvcnRfdHlwZSAgICAgICA6IFwiREVTQ1wiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRwYWdlX251bSAgICAgICAgOiAxLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRwYWdlX2l0ZW1zX2NvdW50OiAxMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y3JlYXRlX2RhdGUgICAgIDogXCJcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0a2V5d29yZCAgICAgICAgIDogXCJcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0c291cmNlICAgICAgICAgIDogXCJcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fTtcclxuXHJcblx0b2JqLnNlYXJjaF9zZXRfYWxsX3BhcmFtcyA9IGZ1bmN0aW9uICggcmVxdWVzdF9wYXJhbV9vYmogKSB7XHJcblx0XHRwX2xpc3RpbmcgPSByZXF1ZXN0X3BhcmFtX29iajtcclxuXHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX2dldF9hbGxfcGFyYW1zID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0cmV0dXJuIHBfbGlzdGluZztcclxuXHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX2dldF9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5ICkge1xyXG5cdFx0cmV0dXJuIHBfbGlzdGluZ1sgcGFyYW1fa2V5IF07XHJcblx0fTtcclxuXHJcblx0b2JqLnNlYXJjaF9zZXRfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0Ly8gaWYgKCBBcnJheS5pc0FycmF5KCBwYXJhbV92YWwgKSApe1xyXG5cdFx0Ly8gXHRwYXJhbV92YWwgPSBKU09OLnN0cmluZ2lmeSggcGFyYW1fdmFsICk7XHJcblx0XHQvLyB9XHJcblx0XHRwX2xpc3RpbmdbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblxyXG5cdG9iai5zZWFyY2hfc2V0X3BhcmFtc19hcnIgPSBmdW5jdGlvbiggcGFyYW1zX2FyciApe1xyXG5cdFx0Xy5lYWNoKCBwYXJhbXNfYXJyLCBmdW5jdGlvbiAoIHBfdmFsLCBwX2tleSwgcF9kYXRhICl7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRGVmaW5lIGRpZmZlcmVudCBTZWFyY2ggIHBhcmFtZXRlcnMgZm9yIHJlcXVlc3RcclxuXHRcdFx0dGhpcy5zZWFyY2hfc2V0X3BhcmFtKCBwX2tleSwgcF92YWwgKTtcclxuXHRcdH0gKTtcclxuXHR9XHJcblxyXG5cclxuXHQvLyBPdGhlciBwYXJhbWV0ZXJzIFx0XHRcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX290aGVyID0gb2JqLm90aGVyX29iaiA9IG9iai5vdGhlcl9vYmogfHwgeyB9O1xyXG5cclxuXHRvYmouc2V0X290aGVyX3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXksIHBhcmFtX3ZhbCApIHtcclxuXHRcdHBfb3RoZXJbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblxyXG5cdG9iai5nZXRfb3RoZXJfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSApIHtcclxuXHRcdHJldHVybiBwX290aGVyWyBwYXJhbV9rZXkgXTtcclxuXHR9O1xyXG5cclxuXHJcblx0cmV0dXJuIG9iajtcclxufSggd3BiY19hanhfYm9va2luZ19saXN0aW5nIHx8IHt9LCBqUXVlcnkgKSk7XHJcblxyXG5cclxuLyoqXHJcbiAqICAgQWpheCAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2VuZCBBamF4IHNlYXJjaCByZXF1ZXN0XHJcbiAqIGZvciBzZWFyY2hpbmcgc3BlY2lmaWMgS2V5d29yZCBhbmQgb3RoZXIgcGFyYW1zXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX2FqYXhfc2VhcmNoX3JlcXVlc3QoKXtcclxuXHJcbmNvbnNvbGUuZ3JvdXBDb2xsYXBzZWQoJ0FKWF9CT09LSU5HX0xJU1RJTkcnKTsgY29uc29sZS5sb2coICcgPT0gQmVmb3JlIEFqYXggU2VuZCAtIHNlYXJjaF9nZXRfYWxsX3BhcmFtcygpID09ICcgLCB3cGJjX2FqeF9ib29raW5nX2xpc3Rpbmcuc2VhcmNoX2dldF9hbGxfcGFyYW1zKCkgKTtcclxuXHJcblx0d3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCgpO1xyXG5cclxuLypcclxuLy9GaXhJbjogZm9yVmlkZW9cclxuaWYgKCAhIGlzX3RoaXNfYWN0aW9uICl7XHJcblx0Ly93cGJjX2FqeF9ib29raW5nX19hY3R1YWxfbGlzdGluZ19faGlkZSgpO1xyXG5cdGpRdWVyeSggd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9vdGhlcl9wYXJhbSggJ2xpc3RpbmdfY29udGFpbmVyJyApICkuaHRtbChcclxuXHRcdCc8ZGl2IHN0eWxlPVwid2lkdGg6MTAwJTt0ZXh0LWFsaWduOiBjZW50ZXI7XCIgaWQ9XCJ3cGJjX2xvYWRpbmdfc2VjdGlvblwiPjxzcGFuIGNsYXNzPVwid3BiY19pY25fYXV0b3JlbmV3IHdwYmNfc3BpblwiPjwvc3Bhbj48L2Rpdj4nXHJcblx0XHQrIGpRdWVyeSggd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9vdGhlcl9wYXJhbSggJ2xpc3RpbmdfY29udGFpbmVyJyApICkuaHRtbCgpXHJcblx0KTtcclxuXHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAoalF1ZXJ5KCAnI3dwYmNfbG9hZGluZ19zZWN0aW9uJyApLndwYmNfbXlfbW9kYWwpICl7XHRcdFx0Ly8gRml4SW46IDkuMC4xLjUuXHJcblx0XHRqUXVlcnkoICcjd3BiY19sb2FkaW5nX3NlY3Rpb24nICkud3BiY19teV9tb2RhbCggJ3Nob3cnICk7XHJcblx0fSBlbHNlIHtcclxuXHRcdGFsZXJ0KCAnV2FybmluZyEgQm9va2luZyBDYWxlbmRhci4gSXRzIHNlZW1zIHRoYXQgIHlvdSBoYXZlIGRlYWN0aXZhdGVkIGxvYWRpbmcgb2YgQm9vdHN0cmFwIEpTIGZpbGVzIGF0IEJvb2tpbmcgU2V0dGluZ3MgR2VuZXJhbCBwYWdlIGluIEFkdmFuY2VkIHNlY3Rpb24uJyApXHJcblx0fVxyXG59XHJcbmlzX3RoaXNfYWN0aW9uID0gZmFsc2U7XHJcbiovXHJcblx0Ly8gU3RhcnQgQWpheFxyXG5cdGpRdWVyeS5wb3N0KCB3cGJjX3VybF9hamF4LFxyXG5cdFx0XHRcdHtcclxuXHRcdFx0XHRcdGFjdGlvbiAgICAgICAgICA6ICdXUEJDX0FKWF9CT09LSU5HX0xJU1RJTkcnLFxyXG5cdFx0XHRcdFx0d3BiY19hanhfdXNlcl9pZDogd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9zZWN1cmVfcGFyYW0oICd1c2VyX2lkJyApLFxyXG5cdFx0XHRcdFx0bm9uY2UgICAgICAgICAgIDogd3BiY19hanhfYm9va2luZ19saXN0aW5nLmdldF9zZWN1cmVfcGFyYW0oICdub25jZScgKSxcclxuXHRcdFx0XHRcdHdwYmNfYWp4X2xvY2FsZSA6IHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfc2VjdXJlX3BhcmFtKCAnbG9jYWxlJyApLFxyXG5cclxuXHRcdFx0XHRcdHNlYXJjaF9wYXJhbXNcdDogd3BiY19hanhfYm9va2luZ19saXN0aW5nLnNlYXJjaF9nZXRfYWxsX3BhcmFtcygpXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHQvKipcclxuXHRcdFx0XHQgKiBTIHUgYyBjIGUgcyBzXHJcblx0XHRcdFx0ICpcclxuXHRcdFx0XHQgKiBAcGFyYW0gcmVzcG9uc2VfZGF0YVx0XHQtXHRpdHMgb2JqZWN0IHJldHVybmVkIGZyb20gIEFqYXggLSBjbGFzcy1saXZlLXNlYXJjZy5waHBcclxuXHRcdFx0XHQgKiBAcGFyYW0gdGV4dFN0YXR1c1x0XHQtXHQnc3VjY2VzcydcclxuXHRcdFx0XHQgKiBAcGFyYW0ganFYSFJcdFx0XHRcdC1cdE9iamVjdFxyXG5cdFx0XHRcdCAqL1xyXG5cdFx0XHRcdGZ1bmN0aW9uICggcmVzcG9uc2VfZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7XHJcbi8vRml4SW46IGZvclZpZGVvXHJcbi8valF1ZXJ5KCAnI3dwYmNfbG9hZGluZ19zZWN0aW9uJyApLndwYmNfbXlfbW9kYWwoICdoaWRlJyApO1xyXG5cclxuY29uc29sZS5sb2coICcgPT0gUmVzcG9uc2UgV1BCQ19BSlhfQk9PS0lOR19MSVNUSU5HID09ICcsIHJlc3BvbnNlX2RhdGEgKTsgY29uc29sZS5ncm91cEVuZCgpO1xyXG5cdFx0XHRcdFx0Ly8gUHJvYmFibHkgRXJyb3JcclxuXHRcdFx0XHRcdGlmICggKHR5cGVvZiByZXNwb25zZV9kYXRhICE9PSAnb2JqZWN0JykgfHwgKHJlc3BvbnNlX2RhdGEgPT09IG51bGwpICl7XHJcblx0XHRcdFx0XHRcdGpRdWVyeSggJy53cGJjX2FqeF91bmRlcl90b29sYmFyX3JvdycgKS5oaWRlKCk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS42LjEuNS5cclxuXHRcdFx0XHRcdFx0alF1ZXJ5KCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5odG1sKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJ3cGJjLXNldHRpbmdzLW5vdGljZSBub3RpY2Utd2FybmluZ1wiIHN0eWxlPVwidGV4dC1hbGlnbjpsZWZ0XCI+JyArXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHJlc3BvbnNlX2RhdGEgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JzwvZGl2PidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFJlbG9hZCBwYWdlLCBhZnRlciBmaWx0ZXIgdG9vbGJhciB3YXMgcmVzZXRlZFxyXG5cdFx0XHRcdFx0aWYgKCAgICAgICAoICAgICB1bmRlZmluZWQgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXSlcclxuXHRcdFx0XHRcdFx0XHQmJiAoICdyZXNldF9kb25lJyA9PT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXVsgJ3VpX3Jlc2V0JyBdKVxyXG5cdFx0XHRcdFx0KXtcclxuXHRcdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uLmhyZWYgPSByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWyd1aV9yZXNldF91cmwnXTtcclxuXHRcdFx0XHRcdFx0Ly8gbG9jYXRpb24ucmVsb2FkKCk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBTaG93IGxpc3RpbmdcclxuXHRcdFx0XHRcdGlmICggcmVzcG9uc2VfZGF0YVsgJ2FqeF9jb3VudCcgXSA+IDAgKXtcclxuXHJcblx0XHRcdFx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfc2hvd19saXN0aW5nKCByZXNwb25zZV9kYXRhWyAnYWp4X2l0ZW1zJyBdLCByZXNwb25zZV9kYXRhWyAnYWp4X3NlYXJjaF9wYXJhbXMnIF0sIHJlc3BvbnNlX2RhdGFbICdhanhfYm9va2luZ19yZXNvdXJjZXMnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRcdHdwYmNfcGFnaW5hdGlvbl9lY2hvKFxyXG5cdFx0XHRcdFx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdwYWdpbmF0aW9uX2NvbnRhaW5lcicgKSxcclxuXHRcdFx0XHRcdFx0XHR3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAncGFnaW5hdGlvbl9jb250YWluZXJfaGVhZGVyJyApLFxyXG5cdFx0XHRcdFx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdwYWdpbmF0aW9uX2NvbnRhaW5lcl9mb290ZXInICksXHJcblx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0J3BhZ2VfYWN0aXZlJzogcmVzcG9uc2VfZGF0YVsgJ2FqeF9zZWFyY2hfcGFyYW1zJyBdWyAncGFnZV9udW0nIF0sXHJcblx0XHRcdFx0XHRcdFx0XHQncGFnZXNfY291bnQnOiBNYXRoLmNlaWwoIHJlc3BvbnNlX2RhdGFbICdhanhfY291bnQnIF0gLyByZXNwb25zZV9kYXRhWyAnYWp4X3NlYXJjaF9wYXJhbXMnIF1bICdwYWdlX2l0ZW1zX2NvdW50JyBdICksXHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0J3BhZ2VfaXRlbXNfY291bnQnOiByZXNwb25zZV9kYXRhWyAnYWp4X3NlYXJjaF9wYXJhbXMnIF1bICdwYWdlX2l0ZW1zX2NvdW50JyBdLFxyXG5cdFx0XHRcdFx0XHRcdFx0J3NvcnRfdHlwZScgICAgICAgOiByZXNwb25zZV9kYXRhWyAnYWp4X3NlYXJjaF9wYXJhbXMnIF1bICdzb3J0X3R5cGUnIF0sXHJcblx0XHRcdFx0XHRcdFx0XHQndG90YWxfY291bnQnICAgICA6IHJlc3BvbnNlX2RhdGFbICdhanhfY291bnQnIF0sXHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0XHR3cGJjX2FqeF9ib29raW5nX2RlZmluZV91aV9ob29rcygpO1x0XHRcdFx0XHRcdC8vIFJlZGVmaW5lIEhvb2tzLCBiZWNhdXNlIHdlIHNob3cgbmV3IERPTSBlbGVtZW50c1xyXG5cclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHRcdFx0XHR3cGJjX2FqeF9ib29raW5nX19hY3R1YWxfbGlzdGluZ19faGlkZSgpO1xyXG5cdFx0XHRcdFx0XHRqUXVlcnkoIHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmh0bWwoXHJcblx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJ3cGJjLXNldHRpbmdzLW5vdGljZTAgbm90aWNlLXdhcm5pbmcwXCIgc3R5bGU9XCJ0ZXh0LWFsaWduOmNlbnRlcjtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAyZW0gMDtcIj4nICtcclxuXHRcdFx0XHRcdFx0XHRcdCc8cD48c3Ryb25nPk5vIHJlc3VsdHMgZm91bmQgZm9yIGN1cnJlbnQgZmlsdGVyIG9wdGlvbnMuLi48L3N0cm9uZz48L3A+JyArXHJcblx0XHRcdFx0XHRcdFx0XHQnPHA+PHN0cm9uZz48YSAgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiICcgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnIG9uY2xpY2s9XCJqYXZhc2NyaXB0OndwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcyggeycgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCcgXFwndWlfcmVzZXRcXCc6IFxcJ21ha2VfcmVzZXRcXCcsICcgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCcgXFwncGFnZV9udW1cXCc6IDEgJyArXHJcblx0XHRcdFx0XHRcdFx0XHRcdCd9ICk7XCI+UmVzZXQgZmlsdGVyczwvYT4gdG8gc2hvdyBhbGwgYm9va2luZ3MuPC9zdHJvbmc+PC9wPicgK1xyXG5cdFx0XHRcdFx0XHRcdCc8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdCk7XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gVXBkYXRlIG5ldyBib29raW5nIGNvdW50XHJcblx0XHRcdFx0XHRpZiAoIHVuZGVmaW5lZCAhPT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9uZXdfYm9va2luZ3NfY291bnQnIF0gKXtcclxuXHRcdFx0XHRcdFx0dmFyIGFqeF9uZXdfYm9va2luZ3NfY291bnQgPSBwYXJzZUludCggcmVzcG9uc2VfZGF0YVsgJ2FqeF9uZXdfYm9va2luZ3NfY291bnQnIF0gKVxyXG5cdFx0XHRcdFx0XHRpZiAoYWp4X25ld19ib29raW5nc19jb3VudD4wKXtcclxuXHRcdFx0XHRcdFx0XHRqUXVlcnkoICcud3BiY19iYWRnZV9jb3VudCcgKS5zaG93KCk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0alF1ZXJ5KCAnLmJrLXVwZGF0ZS1jb3VudCcgKS5odG1sKCBhanhfbmV3X2Jvb2tpbmdzX2NvdW50ICk7XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0d3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9fc3Bpbl9wYXVzZSgpO1xyXG5cclxuXHRcdFx0XHRcdGpRdWVyeSggJyNhamF4X3Jlc3BvbmQnICkuaHRtbCggcmVzcG9uc2VfZGF0YSApO1x0XHQvLyBGb3IgYWJpbGl0eSB0byBzaG93IHJlc3BvbnNlLCBhZGQgc3VjaCBESVYgZWxlbWVudCB0byBwYWdlXHJcblx0XHRcdFx0fVxyXG5cdFx0XHQgICkuZmFpbCggZnVuY3Rpb24gKCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKSB7ICAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnQWpheF9FcnJvcicsIGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93biApOyB9XHJcblx0XHRcdFx0XHRqUXVlcnkoICcud3BiY19hanhfdW5kZXJfdG9vbGJhcl9yb3cnICkuaGlkZSgpO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS42LjEuNS5cclxuXHRcdFx0XHRcdHZhciBlcnJvcl9tZXNzYWdlID0gJzxzdHJvbmc+JyArICdFcnJvciEnICsgJzwvc3Ryb25nPiAnICsgZXJyb3JUaHJvd24gO1xyXG5cdFx0XHRcdFx0aWYgKCBqcVhIUi5yZXNwb25zZVRleHQgKXtcclxuXHRcdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSBqcVhIUi5yZXNwb25zZVRleHQ7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlID0gZXJyb3JfbWVzc2FnZS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKTtcclxuXHJcblx0XHRcdFx0XHR3cGJjX2FqeF9ib29raW5nX3Nob3dfbWVzc2FnZSggZXJyb3JfbWVzc2FnZSApO1xyXG5cdFx0XHQgIH0pXHJcblx0ICAgICAgICAgIC8vIC5kb25lKCAgIGZ1bmN0aW9uICggZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdzZWNvbmQgc3VjY2VzcycsIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICk7IH0gICAgfSlcclxuXHRcdFx0ICAvLyAuYWx3YXlzKCBmdW5jdGlvbiAoIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnYWx3YXlzIGZpbmlzaGVkJywgZGF0YV9qcVhIUiwgdGV4dFN0YXR1cywganFYSFJfZXJyb3JUaHJvd24gKTsgfSAgICAgfSlcclxuXHRcdFx0ICA7ICAvLyBFbmQgQWpheFxyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqICAgVmlld3MgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2hvdyBMaXN0aW5nIFRhYmxlIFx0XHRhbmQgZGVmaW5lIGdNYWlsIGNoZWNrYm94IGhvb2tzXHJcbiAqXHJcbiAqIEBwYXJhbSBqc29uX2l0ZW1zX2Fyclx0XHQtIEpTT04gb2JqZWN0IHdpdGggSXRlbXNcclxuICogQHBhcmFtIGpzb25fc2VhcmNoX3BhcmFtc1x0LSBKU09OIG9iamVjdCB3aXRoIFNlYXJjaFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19zaG93X2xpc3RpbmcoIGpzb25faXRlbXNfYXJyLCBqc29uX3NlYXJjaF9wYXJhbXMsIGpzb25fYm9va2luZ19yZXNvdXJjZXMgKXtcclxuXHJcblx0d3BiY19hanhfZGVmaW5lX3RlbXBsYXRlc19fcmVzb3VyY2VfbWFuaXB1bGF0aW9uKCBqc29uX2l0ZW1zX2FyciwganNvbl9zZWFyY2hfcGFyYW1zLCBqc29uX2Jvb2tpbmdfcmVzb3VyY2VzICk7XHJcblxyXG4vL2NvbnNvbGUubG9nKCAnanNvbl9pdGVtc19hcnInICwganNvbl9pdGVtc19hcnIsIGpzb25fc2VhcmNoX3BhcmFtcyApO1xyXG5cdGpRdWVyeSggJy53cGJjX2FqeF91bmRlcl90b29sYmFyX3JvdycgKS5jc3MoIFwiZGlzcGxheVwiLCBcImZsZXhcIiApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDkuNi4xLjUuXHJcblx0dmFyIGxpc3RfaGVhZGVyX3RwbCA9IHdwLnRlbXBsYXRlKCAnd3BiY19hanhfYm9va2luZ19saXN0X2hlYWRlcicgKTtcclxuXHR2YXIgbGlzdF9mb290ZXJfdHBsID0gd3AudGVtcGxhdGUoICd3cGJjX2FqeF9ib29raW5nX2xpc3RfZm9vdGVyJyApO1xyXG5cdHZhciBsaXN0X3Jvd190cGwgICAgPSB3cC50ZW1wbGF0ZSggJ3dwYmNfYWp4X2Jvb2tpbmdfbGlzdF9yb3cnICk7XHJcblxyXG5cclxuXHQvLyBIZWFkZXIuXHJcblx0alF1ZXJ5KCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5odG1sKCBsaXN0X2hlYWRlcl90cGwoKSApO1xyXG5cdC8vIFNlbmQgdG8gdGVtcGxhdGUgYWxsIHJlcXVlc3QgcGFyYW1zOiBqUXVlcnkoIHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmh0bWwoIGxpc3RfaGVhZGVyX3RwbCh3cGJjX2FqeF9ib29raW5nX2xpc3Rpbmcuc2VhcmNoX2dldF9hbGxfcGFyYW1zKCkpICk7XHJcblx0Ly8gQm9keS5cclxuXHRqUXVlcnkoIHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmFwcGVuZCggJzxkaXYgY2xhc3M9XCJ3cGJjX3NlbGVjdGFibGVfYm9keVwiPjwvZGl2PicgKTtcclxuXHQvLyBGb290ZXIuXHJcblx0alF1ZXJ5KCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5hcHBlbmQoIGxpc3RfZm9vdGVyX3RwbCgpICk7XHJcblxyXG5cdC8vIFIgbyB3IHNcclxuY29uc29sZS5ncm91cENvbGxhcHNlZCggJ0xJU1RJTkdfUk9XUycgKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gTElTVElOR19ST1dTXHJcblx0Xy5lYWNoKCBqc29uX2l0ZW1zX2FyciwgZnVuY3Rpb24gKCBwX3ZhbCwgcF9rZXksIHBfZGF0YSApe1xyXG5cdFx0aWYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIGpzb25fc2VhcmNoX3BhcmFtc1sgJ2tleXdvcmQnIF0gKXtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFBhcmFtZXRlciBmb3IgbWFya2luZyBrZXl3b3JkIHdpdGggZGlmZmVyZW50IGNvbG9yIGluIGEgbGlzdFxyXG5cdFx0XHRwX3ZhbFsgJ19fc2VhcmNoX3JlcXVlc3Rfa2V5d29yZF9fJyBdID0ganNvbl9zZWFyY2hfcGFyYW1zWyAna2V5d29yZCcgXTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHBfdmFsWyAnX19zZWFyY2hfcmVxdWVzdF9rZXl3b3JkX18nIF0gPSAnJztcclxuXHRcdH1cclxuXHRcdHBfdmFsWyAnYm9va2luZ19yZXNvdXJjZXMnIF0gPSBqc29uX2Jvb2tpbmdfcmVzb3VyY2VzO1xyXG5cdFx0alF1ZXJ5KCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKyAnIC53cGJjX3NlbGVjdGFibGVfYm9keScgKS5hcHBlbmQoIGxpc3Rfcm93X3RwbCggcF92YWwgKSApO1xyXG5cdH0gKTtcclxuY29uc29sZS5ncm91cEVuZCgpOyBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIExJU1RJTkdfUk9XU1xyXG5cclxuXHR3cGJjX2RlZmluZV9nbWFpbF9jaGVja2JveF9zZWxlY3Rpb24oIGpRdWVyeSApO1x0XHRcdFx0XHRcdC8vIFJlZGVmaW5lIEhvb2tzIGZvciBjbGlja2luZyBhdCBDaGVja2JveGVzXHJcbn1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSB0ZW1wbGF0ZSBmb3IgY2hhbmdpbmcgYm9va2luZyByZXNvdXJjZXMgJiAgdXBkYXRlIGl0IGVhY2ggdGltZSwgIHdoZW4gIGxpc3RpbmcgdXBkYXRpbmcsIHVzZWZ1bCAgZm9yIHNob3dpbmcgYWN0dWFsICBib29raW5nIHJlc291cmNlcy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBqc29uX2l0ZW1zX2Fyclx0XHQtIEpTT04gb2JqZWN0IHdpdGggSXRlbXNcclxuXHQgKiBAcGFyYW0ganNvbl9zZWFyY2hfcGFyYW1zXHQtIEpTT04gb2JqZWN0IHdpdGggU2VhcmNoXHJcblx0ICogQHBhcmFtIGpzb25fYm9va2luZ19yZXNvdXJjZXNcdC0gSlNPTiBvYmplY3Qgd2l0aCBSZXNvdXJjZXNcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2FqeF9kZWZpbmVfdGVtcGxhdGVzX19yZXNvdXJjZV9tYW5pcHVsYXRpb24oIGpzb25faXRlbXNfYXJyLCBqc29uX3NlYXJjaF9wYXJhbXMsIGpzb25fYm9va2luZ19yZXNvdXJjZXMgKXtcclxuXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBOZXcuIDIwMjUtMDQtMjEuXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBDaGFuZ2UgYm9va2luZyByZXNvdXJjZSBpbiBNb2RhbC5cclxuXHRcdHZhciBtb2RhbF9fY2hhbmdlX2Jvb2tpbmdfcmVzb3VyY2UgPSB3cC50ZW1wbGF0ZSggJ3dwYmNfYWp4X19tb2RhbF9fY2hhbmdlX2Jvb2tpbmdfcmVzb3VyY2UnICk7XHJcblxyXG5cdFx0alF1ZXJ5KCAnI3NlY3Rpb25faW5faW5fbW9kYWxfX2NoYW5nZV9ib29raW5nX3Jlc291cmNlJyApLmh0bWwoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bW9kYWxfX2NoYW5nZV9ib29raW5nX3Jlc291cmNlKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X3NlYXJjaF9wYXJhbXMnICAgIDoganNvbl9zZWFyY2hfcGFyYW1zLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9ib29raW5nX3Jlc291cmNlcyc6IGpzb25fYm9va2luZ19yZXNvdXJjZXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9IClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcblxyXG5cdFx0Ly8gRHVwbGljYXRlIGJvb2tpbmcgaW50byBhbm90aGVyIHJlc291cmNlIGluIE1vZGFsLiBOZXcuIDIwMjUtMDQtMjEuXHJcblx0XHR2YXIgbW9kYWxfX2R1cGxpY2F0ZV9ib29raW5nX3RvX290aGVyX3Jlc291cmNlID0gd3AudGVtcGxhdGUoICd3cGJjX2FqeF9fbW9kYWxfX2R1cGxpY2F0ZV9ib29raW5nX3RvX290aGVyX3Jlc291cmNlJyApO1xyXG5cclxuXHRcdGpRdWVyeSggJyNzZWN0aW9uX2luX2luX21vZGFsX19kdXBsaWNhdGVfYm9va2luZ190b19vdGhlcl9yZXNvdXJjZScgKS5odG1sKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG1vZGFsX19kdXBsaWNhdGVfYm9va2luZ190b19vdGhlcl9yZXNvdXJjZSgge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9zZWFyY2hfcGFyYW1zJyAgICA6IGpzb25fc2VhcmNoX3BhcmFtcyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdhanhfYm9va2luZ19yZXNvdXJjZXMnOiBqc29uX2Jvb2tpbmdfcmVzb3VyY2VzXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHR9XHJcblxyXG5cclxuLyoqXHJcbiAqIFNob3cganVzdCBtZXNzYWdlIGluc3RlYWQgb2YgbGlzdGluZyBhbmQgaGlkZSBwYWdpbmF0aW9uXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX3Nob3dfbWVzc2FnZSggbWVzc2FnZSApe1xyXG5cclxuXHR3cGJjX2FqeF9ib29raW5nX19hY3R1YWxfbGlzdGluZ19faGlkZSgpO1xyXG5cclxuXHRqUXVlcnkoIHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmh0bWwoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwid3BiYy1zZXR0aW5ncy1ub3RpY2Ugbm90aWNlLXdhcm5pbmdcIiBzdHlsZT1cInRleHQtYWxpZ246bGVmdFwiPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG1lc3NhZ2UgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnPC9kaXY+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogICBIIG8gbyBrIHMgIC0gIGl0cyBBY3Rpb24vVGltZXMgd2hlbiBuZWVkIHRvIHJlLVJlbmRlciBWaWV3cyAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBTZW5kIEFqYXggU2VhcmNoIFJlcXVlc3QgYWZ0ZXIgVXBkYXRpbmcgc2VhcmNoIHJlcXVlc3QgcGFyYW1ldGVyc1xyXG4gKlxyXG4gKiBAcGFyYW0gcGFyYW1zX2FyclxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zICggcGFyYW1zX2FyciApe1xyXG5cclxuXHQvLyBEZWZpbmUgZGlmZmVyZW50IFNlYXJjaCAgcGFyYW1ldGVycyBmb3IgcmVxdWVzdFxyXG5cdF8uZWFjaCggcGFyYW1zX2FyciwgZnVuY3Rpb24gKCBwX3ZhbCwgcF9rZXksIHBfZGF0YSApIHtcclxuXHRcdC8vY29uc29sZS5sb2coICdSZXF1ZXN0IGZvcjogJywgcF9rZXksIHBfdmFsICk7XHJcblx0XHR3cGJjX2FqeF9ib29raW5nX2xpc3Rpbmcuc2VhcmNoX3NldF9wYXJhbSggcF9rZXksIHBfdmFsICk7XHJcblx0fSk7XHJcblxyXG5cdC8vIFNlbmQgQWpheCBSZXF1ZXN0XHJcblx0d3BiY19hanhfYm9va2luZ19hamF4X3NlYXJjaF9yZXF1ZXN0KCk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTZWFyY2ggcmVxdWVzdCBmb3IgXCJQYWdlIE51bWJlclwiXHJcbiAqIEBwYXJhbSBwYWdlX251bWJlclx0aW50XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX3BhZ2luYXRpb25fY2xpY2soIHBhZ2VfbnVtYmVyICl7XHJcblxyXG5cdHdwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IHBhZ2VfbnVtYmVyXHJcblx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIEtleXdvcmQgU2VhcmNoaW5nICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFNlYXJjaCByZXF1ZXN0IGZvciBcIktleXdvcmRcIiwgYWxzbyBzZXQgY3VycmVudCBwYWdlIHRvICAxXHJcbiAqXHJcbiAqIEBwYXJhbSBlbGVtZW50X2lkXHQtXHRIVE1MIElEICBvZiBlbGVtZW50LCAgd2hlcmUgd2FzIGVudGVyZWQga2V5d29yZFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X2Zvcl9rZXl3b3JkKCBlbGVtZW50X2lkICkge1xyXG5cclxuXHQvLyBXZSBuZWVkIHRvIFJlc2V0IHBhZ2VfbnVtIHRvIDEgd2l0aCBlYWNoIG5ldyBzZWFyY2gsIGJlY2F1c2Ugd2UgY2FuIGJlIGF0IHBhZ2UgIzQsICBidXQgYWZ0ZXIgIG5ldyBzZWFyY2ggIHdlIGNhbiAgaGF2ZSB0b3RhbGx5ICBvbmx5ICAxIHBhZ2VcclxuXHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdrZXl3b3JkJyAgOiBqUXVlcnkoIGVsZW1lbnRfaWQgKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcbn1cclxuXHJcblx0LyoqXHJcblx0ICogU2VuZCBzZWFyY2ggcmVxdWVzdCBhZnRlciBmZXcgc2Vjb25kcyAodXN1YWxseSBhZnRlciAxLDUgc2VjKVxyXG5cdCAqIENsb3N1cmUgZnVuY3Rpb24uIEl0cyB1c2VmdWwsICBmb3IgZG8gIG5vdCBzZW5kIHRvbyBtYW55IEFqYXggcmVxdWVzdHMsIHdoZW4gc29tZW9uZSBtYWtlIGZhc3QgdHlwaW5nLlxyXG5cdCAqL1xyXG5cdHZhciB3cGJjX2FqeF9ib29raW5nX3NlYXJjaGluZ19hZnRlcl9mZXdfc2Vjb25kcyA9IGZ1bmN0aW9uICgpe1xyXG5cclxuXHRcdHZhciBjbG9zZWRfdGltZXIgPSAwO1xyXG5cclxuXHRcdHJldHVybiBmdW5jdGlvbiAoIGVsZW1lbnRfaWQsIHRpbWVyX2RlbGF5ICl7XHJcblxyXG5cdFx0XHQvLyBHZXQgZGVmYXVsdCB2YWx1ZSBvZiBcInRpbWVyX2RlbGF5XCIsICBpZiBwYXJhbWV0ZXIgd2FzIG5vdCBwYXNzZWQgaW50byB0aGUgZnVuY3Rpb24uXHJcblx0XHRcdHRpbWVyX2RlbGF5ID0gdHlwZW9mIHRpbWVyX2RlbGF5ICE9PSAndW5kZWZpbmVkJyA/IHRpbWVyX2RlbGF5IDogMTUwMDtcclxuXHJcblx0XHRcdGNsZWFyVGltZW91dCggY2xvc2VkX3RpbWVyICk7XHRcdC8vIENsZWFyIHByZXZpb3VzIHRpbWVyXHJcblxyXG5cdFx0XHQvLyBTdGFydCBuZXcgVGltZXJcclxuXHRcdFx0Y2xvc2VkX3RpbWVyID0gc2V0VGltZW91dCggd3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X2Zvcl9rZXl3b3JkLmJpbmQoICBudWxsLCBlbGVtZW50X2lkICksIHRpbWVyX2RlbGF5ICk7XHJcblx0XHR9XHJcblx0fSgpO1xyXG5cclxuXHJcbi8qKlxyXG4gKiAgIERlZmluZSBEeW5hbWljIEhvb2tzICAobGlrZSBwYWdpbmF0aW9uIGNsaWNrLCB3aGljaCByZW5ldyBlYWNoIHRpbWUgd2l0aCBuZXcgbGlzdGluZyBzaG93aW5nKSAgLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuXHQvKipcclxuXHQgKiBEZWZpbmUgSFRNTCB1aSBIb29rczogb24gS2V5VXAgfCBDaGFuZ2UgfCAtPiBTb3J0IE9yZGVyICYgTnVtYmVyIEl0ZW1zIC8gUGFnZVxyXG5cdCAqIFdlIGFyZSBoY25hZ2VkIGl0IGVhY2ggIHRpbWUsIHdoZW4gc2hvd2luZyBuZXcgbGlzdGluZywgYmVjYXVzZSBET00gZWxlbWVudHMgY2huYWdlZFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfYWp4X2Jvb2tpbmdfZGVmaW5lX3VpX2hvb2tzKCkge1xyXG5cclxuXHRcdGlmICggJ2Z1bmN0aW9uJyA9PT0gdHlwZW9mICh3cGJjX2RlZmluZV90aXBweV90b29sdGlwcykgKSB7XHJcblx0XHRcdHdwYmNfZGVmaW5lX3RpcHB5X3Rvb2x0aXBzKCAnLndwYmNfX2xpc3RfX3RhYmxlICcgKTtcclxuXHRcdH1cclxuXHJcblx0XHR3cGJjX2FqeF9ib29raW5nX191aV9kZWZpbmVfX2xvY2FsZSgpO1xyXG5cdFx0d3BiY19hanhfYm9va2luZ19fdWlfZGVmaW5lX19yZW1hcmsoKTtcclxuXHJcblx0XHR3cGJjX2Jvb19saXN0aW5nX19pbml0X2hvb2tfX3NvcnRfYnkoKTtcclxuXHJcblx0XHQvLyBJdGVtcyBQZXIgUGFnZS5cclxuXHRcdGpRdWVyeSggJy53cGJjX2l0ZW1zX3Blcl9wYWdlJyApLm9uKFxyXG5cdFx0XHQnY2hhbmdlJyxcclxuXHRcdFx0ZnVuY3Rpb24gKGV2ZW50KSB7XHJcblx0XHRcdFx0d3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zKFxyXG5cdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHQncGFnZV9pdGVtc19jb3VudCc6IGpRdWVyeSggdGhpcyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHQncGFnZV9udW0nICAgICAgICA6IDFcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHQpO1xyXG5cdFx0XHR9XHJcblx0XHQpO1xyXG5cclxuXHRcdC8vIFNvcnRpbmcuXHJcblx0XHRqUXVlcnkoICcud3BiY19pdGVtc19zb3J0X3R5cGUnICkub24oXHJcblx0XHRcdCdjaGFuZ2UnLFxyXG5cdFx0XHRmdW5jdGlvbiAoZXZlbnQpIHtcclxuXHRcdFx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHsgJ3NvcnRfdHlwZSc6IGpRdWVyeSggdGhpcyApLnZhbCgpIH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0KTtcclxuXHR9XHJcblxyXG5cclxuLyoqXHJcbiAqICAgU2hvdyAvIEhpZGUgTGlzdGluZyAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogIFNob3cgTGlzdGluZyBUYWJsZSBcdC0gXHRTZW5kaW5nIEFqYXggUmVxdWVzdFx0LVx0d2l0aCBwYXJhbWV0ZXJzIHRoYXQgIHdlIGVhcmx5ICBkZWZpbmVkIGluIFwid3BiY19hanhfYm9va2luZ19saXN0aW5nXCIgT2JqLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYm9va2luZ19fYWN0dWFsX2xpc3RpbmdfX3Nob3coKXtcclxuXHJcblx0d3BiY19hanhfYm9va2luZ19hamF4X3NlYXJjaF9yZXF1ZXN0KCk7XHRcdFx0Ly8gU2VuZCBBamF4IFJlcXVlc3RcdC1cdHdpdGggcGFyYW1ldGVycyB0aGF0ICB3ZSBlYXJseSAgZGVmaW5lZCBpbiBcIndwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZ1wiIE9iai5cclxufVxyXG5cclxuLyoqXHJcbiAqIEhpZGUgTGlzdGluZyBUYWJsZSAoIGFuZCBQYWdpbmF0aW9uIClcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2Jvb2tpbmdfX2FjdHVhbF9saXN0aW5nX19oaWRlKCl7XHJcblx0alF1ZXJ5KCAnLndwYmNfYWp4X3VuZGVyX3Rvb2xiYXJfcm93JyApLmhpZGUoKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS42LjEuNS5cclxuXHRqUXVlcnkoIHdwYmNfYWp4X2Jvb2tpbmdfbGlzdGluZy5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSAgICApLmh0bWwoICcnICk7XHJcblx0alF1ZXJ5KCB3cGJjX2FqeF9ib29raW5nX2xpc3RpbmcuZ2V0X290aGVyX3BhcmFtKCAncGFnaW5hdGlvbl9jb250YWluZXInICkgKS5odG1sKCAnJyApO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqICAgU3VwcG9ydCBmdW5jdGlvbnMgZm9yIENvbnRlbnQgVGVtcGxhdGUgZGF0YSAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogSGlnaGxpZ2h0IHN0cmluZ3MsXHJcbiAqIGJ5IGluc2VydGluZyA8c3BhbiBjbGFzcz1cImZpZWxkdmFsdWUgbmFtZSBmaWVsZHNlYXJjaHZhbHVlXCI+Li4uPC9zcGFuPiBodG1sICBlbGVtZW50cyBpbnRvIHRoZSBzdHJpbmcuXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBib29raW5nX2RldGFpbHMgXHQtIFNvdXJjZSBzdHJpbmdcclxuICogQHBhcmFtIHtzdHJpbmd9IGJvb2tpbmdfa2V5d29yZFx0LSBLZXl3b3JkIHRvIGhpZ2hsaWdodFxyXG4gKiBAcmV0dXJucyB7c3RyaW5nfVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19nZXRfaGlnaGxpZ2h0ZWRfc2VhcmNoX2tleXdvcmQoIGJvb2tpbmdfZGV0YWlscywgYm9va2luZ19rZXl3b3JkICl7XHJcblxyXG5cdGJvb2tpbmdfa2V5d29yZCA9IGJvb2tpbmdfa2V5d29yZC50cmltKCkudG9Mb3dlckNhc2UoKTtcclxuXHRpZiAoIDAgPT0gYm9va2luZ19rZXl3b3JkLmxlbmd0aCApe1xyXG5cdFx0cmV0dXJuIGJvb2tpbmdfZGV0YWlscztcclxuXHR9XHJcblxyXG5cdC8vIEhpZ2hsaWdodCBzdWJzdHJpbmcgd2l0aGluZyBIVE1MIHRhZ3MgaW4gXCJDb250ZW50IG9mIGJvb2tpbmcgZmllbGRzIGRhdGFcIiAtLSBlLmcuIHN0YXJ0aW5nIGZyb20gID4gIGFuZCBlbmRpbmcgd2l0aCA8XHJcblx0bGV0IGtleXdvcmRSZWdleCA9IG5ldyBSZWdFeHAoIGBmaWVsZHZhbHVlW148Pl0qPihbXjxdKiR7Ym9va2luZ19rZXl3b3JkfVtePF0qKWAsICdnaW0nICk7XHJcblxyXG5cdC8vbGV0IG1hdGNoZXMgPSBbLi4uYm9va2luZ19kZXRhaWxzLnRvTG93ZXJDYXNlKCkubWF0Y2hBbGwoIGtleXdvcmRSZWdleCApXTtcclxuXHRsZXQgbWF0Y2hlcyA9IGJvb2tpbmdfZGV0YWlscy50b0xvd2VyQ2FzZSgpLm1hdGNoQWxsKCBrZXl3b3JkUmVnZXggKTtcclxuXHRcdG1hdGNoZXMgPSBBcnJheS5mcm9tKCBtYXRjaGVzICk7XHJcblxyXG5cdGxldCBzdHJpbmdzX2FyciA9IFtdO1xyXG5cdGxldCBwb3NfcHJldmlvdXMgPSAwO1xyXG5cdGxldCBzZWFyY2hfcG9zX3N0YXJ0O1xyXG5cdGxldCBzZWFyY2hfcG9zX2VuZDtcclxuXHJcblx0Zm9yICggY29uc3QgbWF0Y2ggb2YgbWF0Y2hlcyApe1xyXG5cclxuXHRcdHNlYXJjaF9wb3Nfc3RhcnQgPSBtYXRjaC5pbmRleCArIG1hdGNoWyAwIF0udG9Mb3dlckNhc2UoKS5pbmRleE9mKCAnPicsIDAgKSArIDEgO1xyXG5cclxuXHRcdHN0cmluZ3NfYXJyLnB1c2goIGJvb2tpbmdfZGV0YWlscy5zdWJzdHIoIHBvc19wcmV2aW91cywgKHNlYXJjaF9wb3Nfc3RhcnQgLSBwb3NfcHJldmlvdXMpICkgKTtcclxuXHJcblx0XHRzZWFyY2hfcG9zX2VuZCA9IGJvb2tpbmdfZGV0YWlscy50b0xvd2VyQ2FzZSgpLmluZGV4T2YoICc8Jywgc2VhcmNoX3Bvc19zdGFydCApO1xyXG5cclxuXHRcdHN0cmluZ3NfYXJyLnB1c2goICc8c3BhbiBjbGFzcz1cImZpZWxkdmFsdWUgbmFtZSBmaWVsZHNlYXJjaHZhbHVlXCI+JyArIGJvb2tpbmdfZGV0YWlscy5zdWJzdHIoIHNlYXJjaF9wb3Nfc3RhcnQsIChzZWFyY2hfcG9zX2VuZCAtIHNlYXJjaF9wb3Nfc3RhcnQpICkgKyAnPC9zcGFuPicgKTtcclxuXHJcblx0XHRwb3NfcHJldmlvdXMgPSBzZWFyY2hfcG9zX2VuZDtcclxuXHR9XHJcblxyXG5cdHN0cmluZ3NfYXJyLnB1c2goIGJvb2tpbmdfZGV0YWlscy5zdWJzdHIoIHBvc19wcmV2aW91cywgKGJvb2tpbmdfZGV0YWlscy5sZW5ndGggLSBwb3NfcHJldmlvdXMpICkgKTtcclxuXHJcblx0cmV0dXJuIHN0cmluZ3NfYXJyLmpvaW4oICcnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDb252ZXJ0IHNwZWNpYWwgSFRNTCBjaGFyYWN0ZXJzICAgZnJvbTpcdCAmYW1wOyBcdC0+IFx0JlxyXG4gKlxyXG4gKiBAcGFyYW0gdGV4dFxyXG4gKiBAcmV0dXJucyB7Kn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfZGVjb2RlX0hUTUxfZW50aXRpZXMoIHRleHQgKXtcclxuXHR2YXIgdGV4dEFyZWEgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAndGV4dGFyZWEnICk7XHJcblx0dGV4dEFyZWEuaW5uZXJIVE1MID0gdGV4dDtcclxuXHRyZXR1cm4gdGV4dEFyZWEudmFsdWU7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDb252ZXJ0IFRPIHNwZWNpYWwgSFRNTCBjaGFyYWN0ZXJzICAgZnJvbTpcdCAmIFx0LT4gXHQmYW1wO1xyXG4gKlxyXG4gKiBAcGFyYW0gdGV4dFxyXG4gKiBAcmV0dXJucyB7Kn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfZW5jb2RlX0hUTUxfZW50aXRpZXModGV4dCkge1xyXG4gIHZhciB0ZXh0QXJlYSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3RleHRhcmVhJyk7XHJcbiAgdGV4dEFyZWEuaW5uZXJUZXh0ID0gdGV4dDtcclxuICByZXR1cm4gdGV4dEFyZWEuaW5uZXJIVE1MO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqICAgU3VwcG9ydCBGdW5jdGlvbnMgLSBTcGluIEljb24gaW4gQnV0dG9ucyAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU3BpbiBidXR0b24gaW4gRmlsdGVyIHRvb2xiYXIgIC0gIFN0YXJ0XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2Jvb2tpbmdfbGlzdGluZ19yZWxvYWRfYnV0dG9uX19zcGluX3N0YXJ0KCl7XHJcblx0alF1ZXJ5KCAnI3dwYmNfYm9va2luZ19saXN0aW5nX3JlbG9hZF9idXR0b24gLm1lbnVfaWNvbi53cGJjX3NwaW4nKS5yZW1vdmVDbGFzcyggJ3dwYmNfYW5pbWF0aW9uX3BhdXNlJyApO1xyXG59XHJcblxyXG4vKipcclxuICogU3BpbiBidXR0b24gaW4gRmlsdGVyIHRvb2xiYXIgIC0gIFBhdXNlXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2Jvb2tpbmdfbGlzdGluZ19yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlKCl7XHJcblx0alF1ZXJ5KCAnI3dwYmNfYm9va2luZ19saXN0aW5nX3JlbG9hZF9idXR0b24gLm1lbnVfaWNvbi53cGJjX3NwaW4nICkuYWRkQ2xhc3MoICd3cGJjX2FuaW1hdGlvbl9wYXVzZScgKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIFNwaW4gYnV0dG9uIGluIEZpbHRlciB0b29sYmFyICAtICBpcyBTcGlubmluZyA/XHJcbiAqXHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbl9faXNfc3Bpbigpe1xyXG4gICAgaWYgKCBqUXVlcnkoICcjd3BiY19ib29raW5nX2xpc3RpbmdfcmVsb2FkX2J1dHRvbiAubWVudV9pY29uLndwYmNfc3BpbicgKS5oYXNDbGFzcyggJ3dwYmNfYW5pbWF0aW9uX3BhdXNlJyApICl7XHJcblx0XHRyZXR1cm4gdHJ1ZTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0cmV0dXJuIGZhbHNlO1xy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
