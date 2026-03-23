
// Hint labels inside of input boxes.
jQuery( document ).ready(
	function () {

		jQuery( 'div.inside_hint' ).on(
			'click',
			function () {
				jQuery( this ).css( 'visibility', 'hidden' ).siblings( '.has-inside-hint' ).trigger( 'focus' );   // FixIn: 8.7.11.12.
			}
		);

		jQuery( 'input.has-inside-hint' ).on(
			'blur',
			function () {
				if ( '' === this.value ) {
					jQuery( this ).siblings( '.inside_hint' ).css( 'visibility', '' );  // FixIn: 8.7.11.12.
				}
			}
		).on(
			'focus',
			function () {
				jQuery( this ).siblings( '.inside_hint' ).css( 'visibility', 'hidden' );  // FixIn: 8.7.11.12.
			}
		);

		jQuery( '.booking_form_div input[type=button]' ).prop( "disabled", false );
	}
);


// FixIn: 8.4.0.2.
/**
 * Check errors in booking form  fields, and show warnings if some errors exist.
 * Check  errors,  like not selected dates or not filled requred form  fields, or not correct entering email or phone
 * fields,  etc...
 *
 * @param bk_type  int (ID of booking resource)
 */
function wpbc_check_errors_in_booking_form( bk_type ) {

	var is_error_in_field = false;  // By default, all  is good - no error.

	var my_form = jQuery( '#booking_form' + bk_type );

    if ( my_form.length ) {

        var fields_with_errors_arr = [];

        // Pseudo-selector that get form elements <input , <textarea , <select, <button...
        my_form.find( ':input' ).each( function( index, el ) {

            // Skip some elements
            var skip_elements = [ 'hidden', 'button' ];

            if (  -1 == skip_elements.indexOf( jQuery( el ).attr( 'type' ) )  ){

				// Check Calendar Dates Selection
                if ( ( 'date_booking' + bk_type ) == jQuery( el ).attr( 'name' ) ) {

                    // Show Warning only  if the calendar visible ( we are at step with  calendar)
                    if (
                            (  ( jQuery( '#calendar_booking' + bk_type ).is( ':visible' )  ) && ( '' == jQuery( el ).val() )  )
                         && ( wpbc_get_arr_of_selected_additional_calendars( bk_type ).length == 0 )                    // FixIn: 8.5.2.26.
                    ){            // FixIn: 8.4.4.5.

                        var notice_message_id = wpbc_front_end__show_message__error_under_element( '#booking_form_div' + bk_type + ' .bk_calendar_frame', _wpbc.get_message( 'message_check_no_selected_dates' ) , 3000 );

						//wpbc_do_scroll('#calendar_booking' + bk_type);            // Scroll to the calendar    		// FixIn: 8.5.1.3.
						is_error_in_field = true;    // Error
                    }
                }

                // Check only visible elements at this step
                if ( jQuery( el ).is( ':visible' )  ){
// console.log( '|id, type, val, visible|::', jQuery( el ).attr( 'name' ), '|' + jQuery( el ).attr( 'type' ) + '|', jQuery( el ).val(), jQuery( el ).is( ':visible' ) );

					// Is Required
					if ( jQuery( el ).hasClass( 'wpdev-validates-as-required' ) ){

						// Checkboxes
						if ( 'checkbox' == jQuery( el ).attr( 'type' ) ){

                            if (
                                    ( ! jQuery( el ).is( ':checked' ))
                                 && ( ! jQuery( ':checkbox[name="' + el.name + '"]', my_form ).is( ":checked" ) )       // FixIn: 8.5.2.12.
                            ){
                                var checkbox_parent_element;

                                if ( jQuery( el ).parents( '.wpdev-form-control-wrap' ).length > 0 ){

                                    checkbox_parent_element = jQuery( el ).parents( '.wpdev-form-control-wrap' );

                                } else if ( jQuery( el ).parents( '.controls' ).length > 0 ){

                                    checkbox_parent_element = jQuery( el ).parents( '.controls' );

                                } else {

                                    checkbox_parent_element = jQuery( el );
                                }
                                var notice_message_id = wpbc_front_end__show_message__warning( checkbox_parent_element, _wpbc.get_message( 'message_check_required_for_check_box' ) );

                                fields_with_errors_arr.push( el );
								is_error_in_field = true;    // Error
							}

							// Radio boxes
						} else if ( 'radio' == jQuery( el ).attr( 'type' ) ){

							if ( !jQuery( ':radio[name="' + jQuery( el ).attr( 'name' ) + '"]', my_form ).is( ':checked' ) ){
                                var notice_message_id = wpbc_front_end__show_message__warning( jQuery( el ).parents('.wpdev-form-control-wrap'), _wpbc.get_message( 'message_check_required_for_radio_box' ) );
                                fields_with_errors_arr.push( el );
								is_error_in_field = true;    // Error
							}

							// Other elements
						} else {

							var inp_value = jQuery( el ).val();

                            if ( '' === wpbc_trim( inp_value ) ){                                                       //FixIn: 8.8.1.3        // FixIn: 8.7.11.12.

                                var notice_message_id = wpbc_front_end__show_message__warning( el, _wpbc.get_message( 'message_check_required' ) );

                                fields_with_errors_arr.push( el );
								is_error_in_field = true;    // Error
							}
						}
					}

					// Validate Email
					if ( jQuery( el ).hasClass( 'wpdev-validates-as-email' ) ){
						var inp_value = jQuery( el ).val();
						inp_value = inp_value.replace( /^\s+|\s+$/gm, '' );                // Trim  white space //FixIn: 5.4.5
						var reg = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;
						if ( (inp_value != '') && (reg.test( inp_value ) == false) ){

                            var notice_message_id = wpbc_front_end__show_message__warning( el, _wpbc.get_message( 'message_check_email' ) );
                            fields_with_errors_arr.push( el );
							is_error_in_field = true;    // Error
						}
					}

					// Validate For digit entering - for example for - Phone
					// <p>Digit Field:<br />[text* dig_field class:validate_as_digit] </p>
					// <p>Phone:<br />[text* phone class:validate_digit_8] </p>

					var classList = jQuery( el ).attr( 'class' );

					if ( classList ){

						classList = classList.split( /\s+/ );

                        jQuery.each( classList, function ( cl_index, cl_item ){

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "Date"   [CSS class - 'validate_as_digit']
                            if ( 'validate_as_date' === cl_item ) {

                                // Valid values: 09-25-2018, 09/25/2018, 09-25-2018,  31-9-1918  ---   m/d/Y, m.d.Y, m-d-Y, d/m/Y, d.m.Y, d-m-Y
                                var regex = new RegExp( '^[0-3]?\\d{1}[\\/\\.\\-]+[0-3]?\\d{1}[\\/\\.\\-]+[0-2]+\\d{3}$' );       // Check for Date 09/25/2018
                                var message_verif_phone = 'This field must be valid date like this ' + '09/25/2018';
                                var inp_value = jQuery( el ).val();

                                if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                    wpbc_front_end__show_message__warning( el, message_verif_phone );
                                    fields_with_errors_arr.push( el );
                                    is_error_in_field = true;    // Error
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "DIGIT"   [CSS class - 'validate_as_digit']
                            if ( 'validate_as_digit' === cl_item ) {

                                var regex = new RegExp( '^[0-9]+\\.?[0-9]*$' );       // Check for digits
                                var message_verif_phone = 'This field must contain only digits';
                                var inp_value = jQuery( el ).val();

                                if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                    wpbc_front_end__show_message__warning( el, message_verif_phone );
                                    fields_with_errors_arr.push( el );
                                    is_error_in_field = true;    // Error
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "Phone" number or any other valid number wth specific number of digits [CSS class - 'validate_digit_8' || 'validate_digit_10' ]
                            var is_validate_digit = cl_item.substring( 0, 15 );

                            // Check  if class start  with 'validate_digit_'
                            if ( 'validate_digit_' === is_validate_digit ){

                                // Get  number of digit in class: validate_digit_8 => 8 or validate_digit_10 => 10
                                var digits_to_check = parseInt( cl_item.substring( 15 ) );

                                // Check  about any errors in
                                if ( !isNaN( digits_to_check ) ){

                                    var regex = new RegExp( '^\\d{' + digits_to_check + '}$' );       // We was valid it as parseInt - only integer variable - digits_to_check
                                    var message_verif_phone = 'This field must contain ' + digits_to_check + ' digits';
                                    var inp_value = jQuery( el ).val();

									if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                        wpbc_front_end__show_message__warning( el, message_verif_phone );
                                        fields_with_errors_arr.push( el );
                                        is_error_in_field = true;    // Error
                                    }
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                        });
    				}
                }
			}
        } );

        if ( fields_with_errors_arr.length > 0 ){
            jQuery( fields_with_errors_arr[ 0 ] ).trigger( 'focus' );    // FixIn: 9.3.1.9.
        }
	}

    return is_error_in_field;
}


// FixIn: 8.6.1.15.
/**
 * Go to next  specific step in Wizard style booking form, with
 * check all required elements specific step, otherwise show warning message!
 *
 * @param el
 * @param step_num
 * @returns {boolean}
 */
function wpbc_wizard_step(el, step_num, step_from) {
	var br_id = jQuery( el ).closest( 'form' ).find( 'input[name^="bk_type"]' ).val();

	// FixIn: 8.8.1.5.
	if ( (undefined == step_from) || (step_num > step_from) ) {
		if ( 1 != step_num ) {                                                                       					// FixIn: 8.7.7.8.
			var is_error = wpbc_check_errors_in_booking_form( br_id );
			if ( is_error ) {
				return false;
			}
		}
	}

	if ( wpbc_is_some_elements_visible( br_id, [ 'rangetime', 'durationtime', 'starttime', 'endtime' ] ) ) {
		if ( wpbc_is_this_time_selection_not_available( br_id, document.getElementById( 'booking_form' + br_id ) ) ) {
			return false;
		}
	}

	if ( br_id != undefined ) {
		jQuery( "#booking_form" + br_id + " .wpbc_wizard_step" ).css( { "display": "none" } ).removeClass( 'wpbc_wizard_step_hidden' );
		jQuery( "#booking_form" + br_id + " .wpbc_wizard_step" + step_num ).css( { "display": "block" } );
		return jQuery( "#booking_form" + br_id + " .wpbc_wizard_step" + step_num );
	}
}


/**
 *  Init Buttons in Booking form Wizard
 */
function wpbc_hook__init_booking_form_wizard_buttons() {

	// CSS classes in Wizard Next / Prior links can  be like this:  <a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">Step 1</a>   |  <a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2">Step 2</a> .

	jQuery( '.wpbc_wizard_step_button' ).attr(
		{
			href: 'javascript:void(0)',
		}
	);

	jQuery( '.wpbc_wizard_step_button' ).on(
		'click',
		function (event) {
			var found_steps_arr = jQuery( this ).attr( 'class' ).match( /wpbc\_wizard\_step\_([\d]+)([\s'"]+|$)/ );

			if ( (null !== found_steps_arr) && (found_steps_arr.length > 2) ) {
				var step = parseInt( found_steps_arr[1] );
				if ( step > 0 ) {
					var jq_step_element;
					// Check actual step in booking form for getting step_from number.
					var this_formsteps_arr = jQuery( this ).parents( '.wpbc_wizard_step' ).attr( 'class' ).match( /wpbc\_wizard\_step([\d]+)([\s'"]+|$)/ );
					if ( (null !== this_formsteps_arr) && (this_formsteps_arr.length > 2) ) {
						var step_from   = parseInt( this_formsteps_arr[1] );
						jq_step_element = wpbc_wizard_step( this, step, step_from );
					} else {
						jq_step_element = wpbc_wizard_step( this, step );
					}
					// Do Scroll.
					if ( false !== jq_step_element ) {
						wpbc_do_scroll( jq_step_element ); // wpbc_do_scroll( jQuery('.wpbc_wizard_step:visible') );.
					}
				}
			}
		}
	);
}

// FixIn: 10.1.3.2.
jQuery( document ).ready( function (){
    wpbc_hook__init_booking_form_wizard_buttons();
} );


// FixIn: 8.6.1.15.
/**
 * Check if at least  one element from  array  of  elements names in booking form  visible  or not.
 * Usage Example:   if ( wpbc_is_some_elements_visible( br_id, ['rangetime', 'durationtime', 'starttime', 'endtime'] )
 * ){ ... }
 *
 * @param bk_type
 * @param elements_names
 * @returns {boolean}
 */
function wpbc_is_some_elements_visible( bk_type, elements_names ){

    var is_some_elements_visible = false;

    var my_form = jQuery( '#booking_form' + bk_type );

    if ( my_form.length ){

        // Pseudo-selector that get form elements <input , <textarea , <select, <button...
        my_form.find( ':input' ).each( function ( index, el ){

            // Skip some elements
            var skip_elements = ['hidden', 'button'];

            if ( -1 == skip_elements.indexOf( jQuery( el ).attr( 'type' ) ) ){

                for ( var ei = 0; ei < ( elements_names.length - 1) ; ei++ ){

                    // Check Calendar Dates Selection
                    if ( (elements_names[ ei ] + bk_type) == jQuery( el ).attr( 'name' ) ){

                        if ( jQuery( el ).is( ':visible' ) ){
                            is_some_elements_visible = true;
                        }
                    }
                }
            }
        } );
    }
    return is_some_elements_visible;
}

// == Days Selections - support functions ==============================================================================

/**
 * Get first day of selection
 *
 * @param dates
 * @returns {string|*}
 */
function get_first_day_of_selection(dates) {

    // Multiple days selections
    if ( dates.indexOf( ',' ) != -1 ){
        var dates_array = dates.split( /,\s*/ );
        var length = dates_array.length;
        var element = null;
        var new_dates_array = [];

        for ( var i = 0; i < length; i++ ){

            element = dates_array[ i ].split( /\./ );

            new_dates_array[ new_dates_array.length ] = element[ 2 ] + '.' + element[ 1 ] + '.' + element[ 0 ];       //2013.12.20
        }
        new_dates_array.sort();

        element = new_dates_array[ 0 ].split( /\./ );

        return element[ 2 ] + '.' + element[ 1 ] + '.' + element[ 0 ];                    //20.12.2013
    }

    // Range days selection
    if ( dates.indexOf( ' - ' ) != -1 ){
        var start_end_date = dates.split( " - " );
        return start_end_date[ 0 ];
    }

    // Single day selection
    return dates;                                                               //20.12.2013
}

// Get last day of selection
function get_last_day_of_selection(dates) {

    // Multiple days selections
    if ( dates.indexOf(',') != -1 ){
        var dates_array =dates.split(/,\s*/);
        var length = dates_array.length;
        var element = null;
        var new_dates_array = [];

        for (var i = 0; i < length; i++) {

          element = dates_array[i].split(/\./);

          new_dates_array[new_dates_array.length] = element[2]+'.' + element[1]+'.' + element[0];       //2013.12.20
        }
        new_dates_array.sort();

        element = new_dates_array[(new_dates_array.length-1)].split(/\./);

        return element[2]+'.' + element[1]+'.' + element[0];                    //20.12.2013
    }

    // Range days selection
    if ( dates.indexOf(' - ') != -1 ){
        var start_end_date = dates.split(" - ");
        return start_end_date[(start_end_date.length-1)];
    }

    // Single day selection
    return dates;                                                               //20.12.2013
}


/**
 * Check ID of selected additional calendars
 *
 * @param int bk_type
 * @returns array
 */
function wpbc_get_arr_of_selected_additional_calendars( bk_type ){                                                      // FixIn: 8.5.2.26.

    var selected_additionl_calendars = [];

    // Checking according additional calendars
    if ( document.getElementById( 'additional_calendars' + bk_type ) != null ){

        var id_additional_str = document.getElementById( 'additional_calendars' + bk_type ).value;
        var id_additional_arr = id_additional_str.split( ',' );

        var is_all_additional_days_unselected = true;

        for ( var ia = 0; ia < id_additional_arr.length; ia++ ){
            if ( document.getElementById( 'date_booking' + id_additional_arr[ ia ] ).value != '' ){
                selected_additionl_calendars.push( id_additional_arr[ ia ] );
            }
        }
    }
    return selected_additionl_calendars;
}

// ---------------------------------------------------------------------------------------------------------------------
// Elementor Ready Widget Update.
// ---------------------------------------------------------------------------------------------------------------------
jQuery( function ($) {
	// FixIn: 10.14.15.1.
	if ( (window.elementorFrontend) && ('undefined' !== typeof (elementorFrontend.hooks)) ) {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/wpbc_widget_booking_form_1.default', function ($scope) {
			// Simulate DOM ready,  after  updating Elementor Widget.
			jQuery( document ).trigger( 'wpbc_elementor_ready' );
		} );

		// Catch all widget re-renders.
		// elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ($scope) {
		// 	console.log( 'Some widget was re-rendered:', $scope );
		// } );
	}
} );

// Elementor widget  reinit. So  we need to reinit all "jQuery( document ).ready( ...) " again with custom 'wpbc_elementor_ready' event.
jQuery( document ).on( 'wpbc_elementor_ready', function () {

	wpbc_hook__init_booking_form_wizard_buttons();

	if ( 'function' === typeof (wpbc_hook__init_timeselector) ) {
		wpbc_hook__init_timeselector();
	}

	if ( 'function' === typeof (wpbc_update_capacity_hint) ) {
		jQuery( '.booking_form_div' ).on( 'wpbc_booking_date_or_option_selected', function (event, resource_id) {
			wpbc_update_capacity_hint( resource_id );
		} );
	}

	// TODO:
	// <?php if ('wpbc_theme_dark_1' === get_bk_option( 'booking_form_theme' ) ){  ?>
	// 	jQuery( '.wpbc_widget_preview_booking_form .wpbc_center_preview,.wpbc_widget_preview_booking_form .wpbc_container.wpbc_container_booking_form,.wpbc_widget_preview_booking_form .wpbc_widget_content' ).addClass( 'wpbc_theme_dark_1' );
	// <?php } ?>

} );
