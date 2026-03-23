(function( w ) {

	'use strict';

	if ( ! w.WPBC_FE ) {
		w.WPBC_FE = {};
	}

	/**
	 * Auto-fill booking form fields (text/email) based on input "name" patterns.
	 *
	 * Form ID format: booking_form{resource_id}
	 * Skips date field: date_booking{resource_id}
	 *
	 * @param {number} resource_id Booking resource ID.
	 * @param {Object} fill_values Values to inject (strings).
	 *
	 * @return {boolean} True if form found and processed, false otherwise.
	 */
	w.WPBC_FE.autofill_booking_form_fields = function( resource_id, fill_values ) {

		resource_id  = parseInt( resource_id, 10 ) || 0;
		fill_values  = fill_values || {};

		var form_id   = 'booking_form' + resource_id;
		var date_name = 'date_booking' + resource_id;

		var submit_form = document.getElementById( form_id );

		if ( ! submit_form ) {
			/* eslint-disable no-console */
			console.error( 'WPBC: No booking form: ' + form_id );
			/* eslint-enable no-console */
			return false;
		}

		// Keep same regex rules and priority order as legacy inline JS.
		var rules = array_rules( fill_values );

		var elements = submit_form.elements || [];
		var count    = elements.length;
		var el;
		var i;
		var j;

		for ( i = 0; i < count; i++ ) {

			el = elements[ i ];

			if ( ! el || ! el.name ) {
				continue;
			}

			// Only text/email inputs.
			if ( ( el.type !== 'text' ) && ( el.type !== 'email' ) ) {
				continue;
			}

			// Skip date field.
			if ( el.name === date_name ) {
				continue;
			}

			// Fill only empty values (legacy behavior: == "").
			if ( el.value !== '' ) {
				continue;
			}

			for ( j = 0; j < rules.length; j++ ) {

				if ( rules[ j ].re.test( el.name ) ) {

					if ( rules[ j ].val !== '' ) {
						el.value = rules[ j ].val;
					}

					break; // Stop at first matching rule (priority).
				}
			}
		}

		return true;
	};

	/**
	 * Build rules array for autofill.
	 *
	 * @param {Object} fill_values Values to inject.
	 *
	 * @return {Array} Rules list.
	 */
	function array_rules( fill_values ) {

		// Normalize to strings (prevent "undefined" in fields).
		var nickname  = ( fill_values.nickname != null ) ? String( fill_values.nickname ) : '';
		var last_name = ( fill_values.last_name != null ) ? String( fill_values.last_name ) : '';
		var first_name = ( fill_values.first_name != null ) ? String( fill_values.first_name ) : '';
		var email     = ( fill_values.email != null ) ? String( fill_values.email ) : '';
		var phone     = ( fill_values.phone != null ) ? String( fill_values.phone ) : '';
		var nb_enfant = ( fill_values.nb_enfant != null ) ? String( fill_values.nb_enfant ) : '';
		var url       = ( fill_values.url != null ) ? String( fill_values.url ) : '';

		return [
			{ re: /^([A-Za-z0-9_\-\.])*(nickname){1}([A-Za-z0-9_\-\.])*$/, val: nickname },
			{ re: /^([A-Za-z0-9_\-\.])*(last|second){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/, val: last_name },
			{ re: /^name([0-9_\-\.])*$/, val: first_name },
			{ re: /^([A-Za-z0-9_\-\.])*(first|my){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/, val: first_name },
			{ re: /^(e)?([_\-\.])?mail([0-9_\-\.]*)$/, val: email },
			{ re: /^([A-Za-z0-9_\-\.])*(phone|fone){1}([A-Za-z0-9_\-\.])*$/, val: phone },
			{ re: /^(e)?([_\-\.])?nb_enfant([0-9_\-\.]*)$/, val: nb_enfant },
			{ re: /^([A-Za-z0-9_\-\.])*(URL|site|web|WEB){1}([A-Za-z0-9_\-\.])*$/, val: url }
		];
	}

})( window );
