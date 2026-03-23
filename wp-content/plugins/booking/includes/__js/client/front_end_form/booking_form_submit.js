// == Submit Booking Data ==============================================================================================
// Refactored (safe), with new wpbc_* names.
// Backward-compatible wrappers for legacy function names are included at the bottom.
// @file: includes/__js/client/front_end_form/booking_form_submit.js

/**
 * Check fields at form and then send request (legacy: mybooking_submit).
 *
 * @param {HTMLFormElement} submit_form
 * @param {number|string}   resource_id
 * @param {string}          wpdev_active_locale
 *
 * @return {false|undefined} Legacy behavior: returns false in some cases, otherwise undefined.
 */
function wpbc_booking_form_submit( submit_form, resource_id, wpdev_active_locale ) {

	resource_id = parseInt( resource_id, 10 );

	// Safety guard (legacy code assumed valid form).
	if ( ! submit_form || ! submit_form.elements ) {
		/* eslint-disable no-console */
		console.error( 'WPBC: Invalid submit form in wpbc_booking_form_submit().' );
		/* eslint-enable no-console */
		return false;
	}

	// -------------------------------------------------------------------------
	// External hook: allow pause submit on confirmation/summary step.
	// -------------------------------------------------------------------------
	var target_elm = jQuery( '.booking_form_div' ).trigger( 'booking_form_submit_click', [ resource_id, submit_form, wpdev_active_locale ] ); // FixIn: 8.8.3.13.

	if (
		( jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).length > 0 ) &&
		( 'pause_submit' === jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).val() )
	) {
		return false;
	}

	// FixIn: 8.4.0.2.
	var is_error = wpbc_check_errors_in_booking_form( resource_id );
	if ( is_error ) {
		return false;
	}

	// -------------------------------------------------------------------------
	// Show message if no selected days in Calendar(s).
	// -------------------------------------------------------------------------
	var date_input = document.getElementById( 'date_booking' + resource_id );
	var date_value = ( date_input ) ? date_input.value : '';

	if ( '' === date_value ) {

		var arr_of_selected_additional_calendars = wpbc_get_arr_of_selected_additional_calendars( resource_id ); // FixIn: 8.5.2.26.

		if ( ! arr_of_selected_additional_calendars || ( arr_of_selected_additional_calendars.length === 0 ) ) {
			wpbc_front_end__show_message__error_under_element(
				'#booking_form_div' + resource_id + ' .bk_calendar_frame',
				_wpbc.get_message( 'message_check_no_selected_dates' ),
				3000
			);
			return;
		}
	}

	// -------------------------------------------------------------------------
	// FixIn: 6.1.1.3. Time selection availability checks.
	// -------------------------------------------------------------------------
	if ( typeof wpbc_is_this_time_selection_not_available === 'function' ) {

		if ( '' === date_value ) { // Primary calendar not selected.

			var additional_calendars_el = document.getElementById( 'additional_calendars' + resource_id );

			if ( additional_calendars_el !== null ) { // Checking additional calendars.

				var id_additional_str = additional_calendars_el.value;
				var id_additional_arr = id_additional_str.split( ',' );
				var is_times_dates_ok = false;

				for ( var ia = 0; ia < id_additional_arr.length; ia++ ) {

					var add_id = id_additional_arr[ ia ];

					var add_date_el = document.getElementById( 'date_booking' + add_id );
					var add_date_val = ( add_date_el ) ? add_date_el.value : '';

					if (
						( '' !== add_date_val ) &&
						( ! wpbc_is_this_time_selection_not_available( add_id, submit_form.elements ) )
					) {
						is_times_dates_ok = true;
					}
				}

				if ( ! is_times_dates_ok ) {
					return;
				}
			}

		} else { // Primary calendar selected.

			if ( wpbc_is_this_time_selection_not_available( resource_id, submit_form.elements ) ) {
				return;
			}
		}
	}

	// -------------------------------------------------------------------------
	// Serialize form (legacy format).
	// -------------------------------------------------------------------------
	var count    = submit_form.elements.length;
	var formdata = '';
	var inp_value;
	var inp_title_value;
	var element;
	var el_type;

	// Helper: legacy escaping for the serialized value.
	function wpbc_escape_serialized_value( val ) {

		val = ( val == null ) ? '' : String( val );

		// Replace registered characters.
		val = val.replace( new RegExp( '\\^', 'g' ), '&#94;' );
		val = val.replace( new RegExp( '~', 'g' ), '&#126;' );

		// Replace quotes.
		val = val.replace( /"/g, '&#34;' );
		val = val.replace( /'/g, '&#39;' );

		return val;
	}

	// Helper: determine UI type for title extraction (legacy logic).
	function wpbc_get_input_element_type( el ) {

		if ( ! el || ! el.tagName ) {
			return '';
		}

		var tag = String( el.tagName ).toLowerCase();

		if ( 'input' === tag ) {
			return ( el.type ) ? String( el.type ).toLowerCase() : 'text';
		}

		// Legacy used "select" string here.
		if ( 'select' === tag ) {
			return 'select';
		}

		return tag;
	}

	for ( var i = 0; i < count; i++ ) { // FixIn: 9.1.5.1.

		element = submit_form.elements[ i ];

		if ( ! element ) {
			continue;
		}

		if ( jQuery( element ).closest( '.booking_form_garbage' ).length ) {
			continue; // Skip elements from garbage. FixIn: 7.1.2.14.
		}

		if (
			( element.type !== 'button' ) &&
			( element.type !== 'hidden' ) &&
			( element.name !== ( 'date_booking' + resource_id ) )
			// && ( jQuery( element ).is( ':visible' ) ) //FixIn: 7.2.1.12.2
		) {

			// -------------------------------------------------------------
			// Get element value.
			// -------------------------------------------------------------
			if ( element.type === 'checkbox' ) {

				if ( element.value === '' ) {
					inp_value = element.checked;
				} else {
					inp_value = ( element.checked ) ? element.value : '';
				}

			} else if ( element.type === 'radio' ) {

				if ( element.checked ) {
					inp_value = element.value;
				} else {

					// Required radio: show warning if none checked.
					// FixIn: 7.0.1.62.
					if (
						( element.className.indexOf( 'wpdev-validates-as-required' ) !== -1 ) &&
						( jQuery( element ).is( ':visible' ) ) && // FixIn: 7.2.1.12.2.
						( ! jQuery( ':radio[name="' + element.name + '"]', submit_form ).is( ':checked' ) )
					) {
						wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_radio_box' ) ); // FixIn: 8.5.1.3.
						return;
					}

					// Skip storing empty radio options.
					continue;
				}

			} else {
				inp_value = element.value;
			}

			inp_title_value = '';

			// -------------------------------------------------------------
			// Get human-friendly title value (legacy behavior).
			// -------------------------------------------------------------
			var input_element_type = wpbc_get_input_element_type( element );

			switch ( input_element_type ) {

				case 'text':
				case 'email':
					inp_title_value = inp_value;
					break;

				case 'select':
					inp_title_value = jQuery( element ).find( 'option:selected' ).text();
					break;

				case 'radio':
				case 'checkbox':
					if ( jQuery( element ).is( ':checked' ) ) {
						var label_element = jQuery( element ).parents( '.wpdev-list-item' ).find( '.wpdev-list-item-label' );
						if ( label_element.length ) {
							inp_title_value = label_element.html();
						}
					}
					break;

				default:
					inp_title_value = inp_value;
			}

			// -------------------------------------------------------------
			// Multiple select value extraction.
			// -------------------------------------------------------------
			if ( ( element.type === 'selectbox-multiple' ) || ( element.type === 'select-multiple' ) ) {
				inp_value = jQuery( '[name="' + element.name + '"]' ).val();
				if ( ( inp_value === null ) || ( String( inp_value ) === '' ) ) {
					inp_value = '';
				}
			}

			// -------------------------------------------------------------
			// Make validation only for visible elements.
			// -------------------------------------------------------------
			if ( jQuery( element ).is( ':visible' ) ) { // FixIn: 7.2.1.12.2.

				// Recheck max available visitors selection.
				if ( typeof wpbc__is_less_than_required__of_max_available_slots__bl === 'function' ) {
					if ( wpbc__is_less_than_required__of_max_available_slots__bl( resource_id, element ) ) {
						return;
					}
				}

				// Required fields.
				if ( element.className.indexOf( 'wpdev-validates-as-required' ) !== -1 ) {

					if ( ( element.type === 'checkbox' ) && ( element.checked === false ) ) {

						if ( ! jQuery( ':checkbox[name="' + element.name + '"]', submit_form ).is( ':checked' ) ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_check_box' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}

					if ( element.type === 'radio' ) {

						if ( ! jQuery( ':radio[name="' + element.name + '"]', submit_form ).is( ':checked' ) ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_radio_box' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}

					if ( ( element.type !== 'checkbox' ) && ( element.type !== 'radio' ) && ( '' === wpbc_trim( inp_value ) ) ) {
						wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required' ) ); // FixIn: 8.5.1.3.
						return;
					}
				}

				// Email format validation.
				if ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) {

					inp_value = String( inp_value ).replace( /^\s+|\s+$/gm, '' ); // Trim white space. FixIn: 5.4.5.
					var reg_email = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;

					if ( inp_value !== '' ) {
						if ( reg_email.test( inp_value ) === false ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_email' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}
				}

				// Same email field validation (verification field).
				if ( ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) && ( element.className.indexOf( 'same_as_' ) !== -1 ) ) {

					var primary_email_name = element.className.match( /same_as_([^\s])+/gi );

					if ( primary_email_name !== null ) {

						primary_email_name = primary_email_name[ 0 ].substr( 8 );

						if ( jQuery( '[name="' + primary_email_name + resource_id + '"]' ).length > 0 ) {

							if ( jQuery( '[name="' + primary_email_name + resource_id + '"]' ).val() !== inp_value ) {
								wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_same_email' ) ); // FixIn: 8.5.1.3.
								return;
							}
						}
					}

					// Skip one loop for the email verification field.
					continue; // FixIn: 8.1.2.15.
				}
			}

			// -------------------------------------------------------------
			// Get Form Data (legacy format).
			// -------------------------------------------------------------
			if ( element.name !== ( 'captcha_input' + resource_id ) ) {

				if ( formdata !== '' ) {
					formdata += '~';
				}

				el_type = element.type;

				if ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) {
					el_type = 'email';
				}
				if ( element.className.indexOf( 'wpdev-validates-as-coupon' ) !== -1 ) {
					el_type = 'coupon';
				}

				inp_value = wpbc_escape_serialized_value( inp_value );

				if ( el_type === 'select-one' ) {
					el_type = 'selectbox-one';
				}
				if ( el_type === 'select-multiple' ) {
					el_type = 'selectbox-multiple';
				}

				formdata += el_type + '^' + element.name + '^' + inp_value;

				// Add title/label value (legacy).
				var clean_field_name = String( element.name );

				// BUGFIX: replaceAll(RegExp) is not supported in older browsers.
				// Keep legacy intent: remove [] suffix occurrences.
				clean_field_name = clean_field_name.replace( /\[\]/gi, '' );

				var resource_id_str = String( resource_id );

				// Legacy assumed suffix ends with resource_id, make it safe.
				if (
					( clean_field_name.length >= resource_id_str.length ) &&
					( clean_field_name.substr( clean_field_name.length - resource_id_str.length ) === resource_id_str )
				) {
					clean_field_name = clean_field_name.substr( 0, clean_field_name.length - resource_id_str.length );
				}

				formdata += '~' + el_type + '^' + clean_field_name + '_val' + resource_id + '^' + inp_title_value;
			}
		}
	}

	// TODO: here was function for 'Check if visitor finish dates selection.

	// Captcha verify.
	var captcha = document.getElementById( 'wpdev_captcha_challenge_' + resource_id );

	if ( captcha !== null ) {
		wpbc_form_submit_send( resource_id, formdata, captcha.value, document.getElementById( 'captcha_input' + resource_id ).value, wpdev_active_locale );
	} else {
		wpbc_form_submit_send( resource_id, formdata, '', '', wpdev_active_locale );
	}

	return;
}


/**
 * Gathering params for sending Ajax request and then send it (legacy: form_submit_send).
 *
 * @param {number|string} resource_id
 * @param {string}        formdata
 * @param {string}        captcha_chalange
 * @param {string}        user_captcha
 * @param {string}        wpdev_active_locale
 *
 * @return {undefined} Legacy behavior.
 */
function wpbc_form_submit_send( resource_id, formdata, captcha_chalange, user_captcha, wpdev_active_locale ) {

	resource_id = parseInt( resource_id, 10 );

	var my_booking_form = '';
	var booking_form_type_el = document.getElementById( 'booking_form_type' + resource_id );
	if ( booking_form_type_el !== null ) {
		my_booking_form = booking_form_type_el.value;
	}

	var my_booking_hash = '';
	if ( _wpbc.get_other_param( 'this_page_booking_hash' ) !== '' ) {
		my_booking_hash = _wpbc.get_other_param( 'this_page_booking_hash' );
	}

	var is_send_emeils = 1;
	if ( jQuery( '#is_send_email_for_pending' ).length ) { // FixIn: 8.7.9.5.

		is_send_emeils = jQuery( '#is_send_email_for_pending' ).is( ':checked' );

		if ( false === is_send_emeils ) {
			is_send_emeils = 0;
		} else {
			is_send_emeils = 1;
		}
	}

	var date_el = document.getElementById( 'date_booking' + resource_id );
	var date_value = ( date_el ) ? date_el.value : '';

	if ( '' !== date_value ) { // FixIn: 6.1.1.3.
		wpbc_send_ajax_submit( resource_id, formdata, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale );
	} else {
		jQuery( '#booking_form_div' + resource_id ).hide();
		jQuery( '#submiting' + resource_id ).hide();
	}

	// -------------------------------------------------------------------------
	// Additional calendars submit.
	// -------------------------------------------------------------------------
	var additional_calendars_el = document.getElementById( 'additional_calendars' + resource_id );
	if ( additional_calendars_el === null ) {
		return;
	}

	var id_additional_str = additional_calendars_el.value;
	var id_additional_arr = id_additional_str.split( ',' );

	// FixIn: 10.9.4.1.
	for ( var ia = 0; ia < id_additional_arr.length; ia++ ) {
		id_additional_arr[ ia ] = parseInt( id_additional_arr[ ia ], 10 );
	}

	if ( ! jQuery( '#booking_form_div' + resource_id ).is( ':visible' ) ) {
		wpbc_booking_form__spin_loader__show( resource_id ); // Show Spinner
	}

	// Helper: rewrite field name suffix from resource_id -> id_additional.
	function wpbc_rewrite_field_name_suffix( field_name, old_id, new_id ) {

		field_name = String( field_name );

		var old_id_str = String( old_id );
		var new_id_str = String( new_id );

		// Handle fields with [].
		if (
			( field_name.length >= ( old_id_str.length + 2 ) ) &&
			( field_name.substr( field_name.length - ( old_id_str.length + 2 ) ) === ( old_id_str + '[]' ) )
		) {
			return field_name.substr( 0, field_name.length - ( old_id_str.length + 2 ) ) + new_id_str + '[]';
		}

		// Handle fields without [].
		if (
			( field_name.length >= old_id_str.length ) &&
			( field_name.substr( field_name.length - old_id_str.length ) === old_id_str )
		) {
			return field_name.substr( 0, field_name.length - old_id_str.length ) + new_id_str;
		}

		// Fallback: return unchanged (safer than breaking name).
		return field_name;
	}

	for ( ia = 0; ia < id_additional_arr.length; ia++ ) {

		var id_additional = id_additional_arr[ ia ];

		// FixIn: 10.9.4.1.
		if ( id_additional <= 0 ) {
			continue;
		}

		// Rebuild formdata for each additional calendar (legacy behavior).
		var formdata_additional_arr = String( formdata ).split( '~' );
		var formdata_additional = '';

		for ( var j = 0; j < formdata_additional_arr.length; j++ ) {

			var my_form_field = formdata_additional_arr[ j ].split( '^' );

			if ( formdata_additional !== '' ) {
				formdata_additional += '~';
			}

			// Safety: ensure we have at least type ^ name ^ value.
			if ( my_form_field.length < 3 ) {
				formdata_additional += formdata_additional_arr[ j ];
				continue;
			}

			my_form_field[ 1 ] = wpbc_rewrite_field_name_suffix( my_form_field[ 1 ], resource_id, id_additional );
			formdata_additional += my_form_field[ 0 ] + '^' + my_form_field[ 1 ] + '^' + my_form_field[ 2 ];
		}

		// If payment form for main booking resource is showing, append for additional calendars.
		if ( jQuery( '#gateway_payment_forms' + resource_id ).length > 0 ) {
			jQuery( '#gateway_payment_forms' + resource_id ).after( '<div id="gateway_payment_forms' + id_additional + '"></div>' );
			jQuery( '#gateway_payment_forms' + resource_id ).after( '<div id="ajax_respond_insert' + id_additional + '" style="display:none;"></div>' );
		}

		// FixIn: 8.5.2.17.
		wpbc_send_ajax_submit( id_additional, formdata_additional, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale );
	}
}


/**
 * Send Ajax submit (legacy: send_ajax_submit).
 *
 * @param {number|string} resource_id
 * @param {string}        formdata
 * @param {string}        captcha_chalange
 * @param {string}        user_captcha
 * @param {number}        is_send_emeils
 * @param {string}        my_booking_hash
 * @param {string}        my_booking_form
 * @param {string}        wpdev_active_locale
 *
 * @return {undefined} Legacy behavior.
 */
function wpbc_send_ajax_submit(resource_id, formdata, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale) {

	resource_id = parseInt( resource_id, 10 );

	// Disable Submit | Show spin loader.
	wpbc_booking_form__on_submit__ui_elements_disable( resource_id );

	// FixIn: 2026-02-05 - pass preview context to booking create Ajax.
	var form_status  = wpbc__get_form_status_for_submit( resource_id );
	var preview_args = (form_status === 'preview') ? wpbc__get_bfb_preview_args_from_location() : null;

	var request_params = {
		'resource_id'              : resource_id,
		'dates_ddmmyy_csv'         : document.getElementById( 'date_booking' + resource_id ).value,
		'formdata'                 : formdata,
		'booking_hash'             : my_booking_hash,
		'custom_form'              : my_booking_form,
		'aggregate_resource_id_arr': ( ( null !== _wpbc.booking__get_param_value( resource_id, 'aggregate_resource_id_arr' ) ) ? _wpbc.booking__get_param_value( resource_id, 'aggregate_resource_id_arr' ).join( ',' ) : '' ),
		'captcha_chalange'         : captcha_chalange,
		'captcha_user_input'       : user_captcha,
		'is_emails_send'           : is_send_emeils,
		'active_locale'            : wpdev_active_locale,
		'form_status'              : form_status
	};

	// If preview, pass session identifiers so PHP can load transient snapshot.
	if ( preview_args && preview_args.token && preview_args.form_id ) {
		request_params['wpbc_bfb_preview']         = 1;
		request_params['wpbc_bfb_preview_token']   = preview_args.token;
		request_params['wpbc_bfb_preview_form_id'] = preview_args.form_id;
		request_params['wpbc_bfb_preview_nonce']   = preview_args.nonce; // note: URL param is `nonce`.
	}

	var is_exit = wpbc_ajx_booking__create( request_params );

	if ( true === is_exit ) {
		return;
	}
}



// == Helper Functions =================================================================================================

/**
 * Parse query string into {key:value} (old-browser safe).
 *
 * @param {string} qs
 * @return {Object}
 */
function wpbc__parse_query_string(qs) {
	var out = {};
	qs      = (qs || '');
	qs      = qs.replace( /^\?/, '' );
	if ( ! qs ) {
		return out;
	}

	var parts = qs.split( '&' );
	for ( var i = 0; i < parts.length; i++ ) {
		var kv = parts[i].split( '=' );
		var k  = decodeURIComponent( kv[0] || '' );
		if ( ! k ) {
			continue;
		}
		var v  = decodeURIComponent( kv.slice( 1 ).join( '=' ) || '' );
		out[k] = v;
	}
	return out;
}

/**
 * Detect preview args from current URL (iframe URL).
 *
 * @return {Object|null} { token, form_id, nonce } or null
 */
function wpbc__get_bfb_preview_args_from_location() {
	try {
		var p = wpbc__parse_query_string( (window.location && window.location.search) ? window.location.search : '' );

		if ( ! p.wpbc_bfb_preview || (p.wpbc_bfb_preview === '0') ) {
			return null;
		}

		if ( ! p.wpbc_bfb_preview_token || ! p.wpbc_bfb_preview_form_id ) {
			return null;
		}

		return {
			token  : String( p.wpbc_bfb_preview_token ),
			form_id: parseInt( p.wpbc_bfb_preview_form_id, 10 ) || 0,
			nonce  : (p.nonce) ? String( p.nonce ) : ''
		};
	} catch ( e ) {
		return null;
	}
}

/**
 * Resolve form status for submit.
 *
 * Priority:
 * 1) shortcode param exposed via _wpbc.booking__get_param_value(..., 'form_status')
 * 2) detect preview URL args
 * 3) fallback: published
 *
 * @param {number} resource_id
 * @return {string} 'preview'|'published'
 */
function wpbc__get_form_status_for_submit(resource_id) {

	var status = '';

	try {
		if ( (typeof _wpbc !== 'undefined') && _wpbc.booking__get_param_value ) {
			status = _wpbc.booking__get_param_value( resource_id, 'form_status' );
		}
	} catch ( e ) {}

	status = (status == null) ? '' : String( status );
	status = status.toLowerCase();

	// URL-based detection for preview iframe.
	var preview_args = wpbc__get_bfb_preview_args_from_location();
	if ( preview_args ) {
		return 'preview';
	}

	return (status === 'preview') ? 'preview' : 'published';
}



// == Backward-compatible wrappers (keep old global names working 100% as before). =====================================
function mybooking_submit( submit_form, resource_id, wpdev_active_locale ) {
	return wpbc_booking_form_submit( submit_form, resource_id, wpdev_active_locale );
}
