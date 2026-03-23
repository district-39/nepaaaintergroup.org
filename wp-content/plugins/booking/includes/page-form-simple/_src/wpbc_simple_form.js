/**
 *  Add 'last_selected', 'current' CSS classes  on FOCUS to table rows
 */
( function( $ ){
	var controlled = false;
	var shifted = false;
	var hasFocus = false;

	$(document).on('keyup keydown', function(e){ shifted = e.shiftKey; controlled = e.ctrlKey || e.metaKey } );

	$('.wpbc_input_table').on( 'focus click', 'input', function( e ) {

			var this_closest_table = $(this).closest('table');
			var this_closest_row   = $(this).closest('tr');

			if ( ( e.type == 'focus' && hasFocus != this_closest_row.index() ) || ( e.type == 'click' && $(this).is(':focus') ) ) {

					hasFocus = this_closest_row.index();

					if ( ! shifted && ! controlled ) {
							$('tr', this_closest_table).removeClass('current').removeClass('last_selected');
							this_closest_row.addClass('current').addClass('last_selected');
					} else if ( shifted ) {
							$('tr', this_closest_table).removeClass('current');
							this_closest_row.addClass('selected_now').addClass('current');

							if ( $('tr.last_selected', this_closest_table).size() > 0 ) {
									if ( this_closest_row.index() > $('tr.last_selected', this_closest_table).index() ) {
											$('tr', this_closest_table).slice( $('tr.last_selected', this_closest_table).index(), this_closest_row.index() ).addClass('current');
									} else {
											$('tr', this_closest_table).slice( this_closest_row.index(), $('tr.last_selected', this_closest_table).index() + 1 ).addClass('current');
									}
							}

							$('tr', this_closest_table).removeClass('last_selected');
							this_closest_row.addClass('last_selected');
					} else {
							$('tr', this_closest_table).removeClass('last_selected');
							if ( controlled && $(this).closest('tr').is('.current') ) {
									this_closest_row.removeClass('current');
							} else {
									this_closest_row.addClass('current').addClass('last_selected');
							}
					}

					$('tr', this_closest_table).removeClass('selected_now');

			}
	}).on( 'blur', 'input', function( e ) {
			hasFocus = false;
	});

}( jQuery ) );


// Make Table sortable
function wpbc_make_table_sortable(){

	jQuery('.wpbc_input_table tbody th').css('cursor','move');

	jQuery('.wpbc_input_table tbody td.sort').css('cursor','move');

	jQuery('.wpbc_input_table.sortable tbody').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
// connectWith: ".wpbc_table_form_free tbody",					//// FixIn: 10.1.2.2.
// //axis:'y',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: '.wpbc_input_table .sort',
			start:function(event,ui){
					ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
					ui.item.removeAttr('style');
			}
	});
}


// Activate row delete
function wpbc_activate_table_row_delete( del_btn_css_class, is_confirm ){

	// Delete Row
	jQuery( del_btn_css_class ).on( 'click', function(){                   // FixIn: 8.7.11.12.

		if ( true === is_confirm ){
			if ( ! wpbc_are_you_sure( 'Do you really want to do this ?' ) ){
				return false;
			}
		}

		var $current = jQuery(this).closest('tr');
		if ( $current.size() > 0 ) {
			$current.each(function(){
					jQuery(this).remove();
			});
			return true;
		}

		return false;
	});

}


//////////////////////////////////////////////////////////
// Fields Generator Section
//////////////////////////////////////////////////////////


/**
 * Check  Name  in  "field form" about possible usage of this name and about  any Duplicates in Filds Table
 * @param {string} field_name
 */
function wpbc_check_typed_name( field_name ){

	// Set Name only Letters
	if (    ( jQuery('#' + field_name + '_name').val() != '' )
		 && ( ! jQuery('#' + field_name + '_name').is(':disabled') )
		){
		var p_name = jQuery('#' + field_name + '_name').val();
		p_name = p_name.replace(/[^A-Za-z0-9_-]*[0-9]*$/g,'').replace(/[^A-Za-z0-9_-]/g,'');
		p_name = p_name.toLowerCase();


		jQuery('input[name^=form_field_name]').each(function(){
			var text_value = jQuery(this).val();
			if( text_value == p_name ) {                            // error element with this name exist

				p_name +=  '_' + Math.round( new Date().getTime()  ) + '_rand';         //Add random sufix
			}
		});

		jQuery('#' + field_name + '_name').val( p_name );
	}
}


/** Reset to default values all Form  fields for creation new fields */
function wpbc_reset_all_forms(){

	jQuery('.wpbc_table_form_free tr').removeClass('highlight');
	jQuery('.wpbc_add_field_row').hide();
	jQuery('.wpbc_edit_field_row').hide();

	var field_type_array = [ 'text', 'textarea', 'select', 'selectbox', 'checkbox', 'rangetime', 'durationtime', 'starttime', 'endtime' ];						//FixIn: TimeFreeGenerator
	var field_type;

	jQuery( '.field_options_format').remove();

	for (var i = 0; i < field_type_array.length; i++) {
		field_type = field_type_array[i];

		if ( ! jQuery('#' + field_type + '_field_generator_name').is(':disabled') ){						//FixIn: TimeFreeGenerator
			jQuery( '#' + field_type + '_field_generator_active' ).prop( 'checked', true );
			jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked', false );
			jQuery( '#' + field_type + '_field_generator_label' ).val( '' );

			jQuery( '#' + field_type + '_field_generator_name' ).prop( 'disabled', false );
			jQuery( '#' + field_type + '_field_generator_name' ).val( '' );
			jQuery( '#' + field_type + '_field_generator_value' ).val( '' );
		}
	}
}


/**
 * Show selected Add New Field form, and reset fields in this form
 *
 * @param string selected_field_value
 */
function wpbc_show_fields_generator( selected_field_value ) {
	if (
		('text' === selected_field_value) ||
		('textarea' === selected_field_value) ||
		('select' === selected_field_value) ||
		('selectbox' === selected_field_value) ||
		('checkbox' === selected_field_value)
	) {
		if ('select' === selected_field_value){
		jQuery( '#' + 'selectbox' + '_field_generator_name' ).prop( 'disabled', false );
		}
		jQuery( '#' + selected_field_value + '_field_generator_name' ).prop( 'disabled', false );
	}

	wpbc_reset_all_forms();

	var time_fields_arr       = [ 'rangetime', 'durationtime', 'starttime', 'endtime' ];
	var time_fields_arr_count = time_fields_arr.length;
	for ( var i = 0; i < time_fields_arr_count; i++ ) {
		// time_fields_arr[i] = 'rangetime'.
		if ( 'edit_' + time_fields_arr[i] === selected_field_value ) {
			// This field already  exist  in the booking form,  and thats why  we can  not add a new field,  and instead of that  edit it.
			var range_time_edit_field = jQuery( '.wpbc_table_form_free :input[value="' + time_fields_arr[i] + '"]' );	// time_fields_arr[i] = 'rangetime'.
			var range_time_field_num  = 0;

			if ( range_time_edit_field.length > 0 ) {
				var range_time_edit_field_name = jQuery( range_time_edit_field.get( 0 ) ).attr( 'name' );
				range_time_edit_field_name     = range_time_edit_field_name.replaceAll( 'form_field_name[', '' ).replaceAll( ']', '' );
				range_time_field_num           = parseInt( range_time_edit_field_name );
				if ( range_time_field_num > 0 ) {
					wpbc_start_edit_form_field( range_time_field_num );
				}
			}
			if ( 0 === range_time_field_num ) {
				selected_field_value = time_fields_arr[i];	// time_fields_arr[i] = 'rangetime'.
			} else {
				return;
			}
		}

	}

	if (selected_field_value == 'selector_hint') {
		jQuery('.metabox_wpbc_form_field_free_generator').hide();
		jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show();						// FixIn: 8.7.11.7.
		jQuery( '#wpbc_settings__form_fields__toolbar').show();
	} else {
		jQuery('.metabox_wpbc_form_field_free_generator').show();
		jQuery('.wpbc_field_generator').hide();
		jQuery('.wpbc_field_generator_' + selected_field_value ).show();
		jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html( jQuery('#select_form_help_shortcode option:selected').text() );
		jQuery('.wpbc_add_field_row').show();
		jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').hide();						// FixIn: 8.7.11.7.
		jQuery( '#wpbc_settings__form_fields__toolbar').hide();
	}
}


/** Hide all Add New Field forms, and reset fields in these forms*/
function wpbc_hide_fields_generators() {
	wpbc_reset_all_forms();
	jQuery('.metabox_wpbc_form_field_free_generator').hide();
	jQuery('#select_form_help_shortcode>option:eq(0)').attr('selected', true);

	jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show();						// FixIn: 8.7.11.7.
	jQuery( '#wpbc_settings__form_fields__toolbar').show();
}


/**
 * Add New Row with new Field to Table and Submit Saving changes.
 *
 * @param {string} field_name
 * @param {string} field_type
 */
function wpbc_add_field ( field_name, field_type ) {

	// FixIn: TimeFreeGenerator.
	var time_fields_arr = [ 'rangetime' ]; //, 'durationtime', 'starttime', 'endtime' ]; .
	var time_fields_arr_count = time_fields_arr.length;
	for ( var i = 0; i < time_fields_arr_count; i++ ) {
		// time_fields_arr[i] = 'rangetime'.
		if ( time_fields_arr[i] + '_field_generator' === field_name ) {
			var replaced_result = wpbc_get_saved_value_from_timeslots_table();
			if ( false === replaced_result ){
				wpbc_hide_fields_generators();
				//TOO: Show warning at  the top of page,  about error during saving timeslots
				console.log( 'error during parsing timeslots tbale and savig it.' )
				return;
			}
		}
	}

	if ( jQuery('#' + field_name + '_name').val() != '' ) {

		wpbc_check_typed_name( field_name );

		var row_num = jQuery('.wpbc_table_form_free tbody tr').length + Math.round( new Date().getTime()  ) ;

		var row_active = 'Off';
		var row_active_checked = '';
		if ( jQuery('#' + field_name + '_active').is( ":checked" ) ) {
			row_active = 'On';
			row_active_checked = ' checked="checked" ';
		}

		var row_required = 'Off';
		var row_required_checked = '';
		if ( jQuery('#' + field_name + '_required').is( ":checked" ) ) {
			row_required = 'On';
			row_required_checked = ' checked="checked" ';
		}


		var row;
		row = '<tr class="account ui-sortable-handle">';

		////////////////////////////////////////////////////////////
		row += '<td class="sort" style="cursor: move;"><span class="wpbc_icn_drag_indicator" aria-hidden="true"></span></td>';

		row += '<td class="field_active">';
		row +=      '<input type="checkbox" name="form_field_active['+ row_num +']" value="' + row_active + '" ' + row_active_checked + ' autocomplete="off" />';
		row += '</td>';

		////////////////////////////////////////////////////////////
		row += '<td class="field_label">';

		//row +=      '<legend class="screen-reader-text"><span>' + jQuery('#' + field_name + '_label').val() + '</span></legend>';

		row +=      '<input type="text" name="form_field_label['+ row_num +']" value="'
							+ jQuery('#' + field_name + '_label').val() + '" placeholder="'
							+ jQuery('#' + field_name + '_label').val() + '" class="regular-text" autocomplete="off" />';

		row +=        		'<div class="field_type_name_description">';
		//row +=        			'<?php echo esc_js( __( 'Type', 'booking' ) ); ?>: <div class="field_type_name_value">' +field_type+ '</div>';
		row +=        			'Type: <div class="field_type_name_value">' +field_type+ '</div>';
		row +=        			'<span class="field_type_name_separator">|</span>';
		//row +=        			'<?php echo esc_js( __( 'Name', 'booking' ) ); ?>: <div class="field_type_name_value">' + jQuery('#' + field_name + '_name').val() + '</div>';
		row +=        			'Name: <div class="field_type_name_value">' + jQuery('#' + field_name + '_name').val() + '</div>';
		row +=        		'</div>';

		row +=        '<input type="hidden" value="' + ( ( 'select' == field_type ) ? 'selectbox' : field_type )  +  '"  name="form_field_type[' + row_num + ']" autocomplete="off" />';
		row +=        '<input type="hidden" value="' + jQuery('#' + field_name + '_name').val() + '"  name="form_field_name[' + row_num + ']" autocomplete="off" />';
		row +=        '<input type="hidden" value="' + ((jQuery( '#' + field_name + '_value' ).length) ? jQuery( '#' + field_name + '_value' ).val() : '') + '"  name="form_field_value[' + row_num + ']" autocomplete="off" />';

		row += '</td>';

		////////////////////////////////////////////////////////////
		row += '<td class="field_required">';

		// FixIn:  TimeFreeGenerator.
		if (
			('rangetime' === field_name) ||
			('durationtime' === field_name) ||
			('starttime' === field_name) ||
			('endtime' === field_name)
		) {
			row += '<input type="checkbox" disabled="DISABLED" name="form_field_required[' + row_num + ']" value="On"  checked="checked"  autocomplete="off" />';
		} else {
			row += '<input type="checkbox" name="form_field_required[' + row_num + ']" value="' + row_required + '" ' + row_required_checked + ' autocomplete="off" />';
		}

		row += '</td>';

		////////////////////////////////////////////////////////////
		// row += '<td class="field_options">';
		// row +=        '<input type="text" disabled="DISABLED" value="' + field_type + ' | ' + jQuery('#' + field_name + '_name').val() + '"  autocomplete="off" />';
		// row += '</td>';

		////////////////////////////////////////////////////////////
		row += '<td class="field_options">';

		//row +=      '<a href="javascript:void(0)" class="tooltip_top button-secondary button" title="<?php echo esc_js( __('Edit' ,'booking') ) ; ?>"><i class="wpbc_icn_draw"></i></a>';
		//row +=      '<a href="javascript:void(0)" class="tooltip_top button-secondary button delete_bk_link" title="<?php echo esc_js( __('Remove' ,'booking') ) ; ?>"><i class="wpbc_icn_close"></i></a>';

		row += '</td>';
		////////////////////////////////////////////////////////////
		row += '</tr>';

		jQuery('.wpbc_table_form_free tbody').append( row );

		wpbc_hide_fields_generators();

		document.forms['wpbc_form_field_free'].submit();            //Submit form

	} else {
		wpbc_field_highlight( '#' + field_name + '_name' );
	}
}


/**
 * Prepare Edit section for editing specific field.
 * @param row_number
 */
function wpbc_start_edit_form_field( row_number ) {

	wpbc_reset_all_forms();																					// Reset Fields in all generator rows (text,select,...) to init (empty) values
	jQuery('.wpbc_edit_field_row').show();																	// Show row with edit btn

	jQuery('.wpbc_table_form_free tr').removeClass('highlight');
	jQuery('input[name="form_field_name['+row_number+']"]').closest('tr').addClass('highlight');			//Highlight row

	// Get exist data from EXIST fields Table
	var field_active = jQuery('input[name="form_field_active['+row_number+']"]').is( ":checked" );
	var field_required = jQuery('input[name="form_field_required['+row_number+']"]').is( ":checked" );
	var field_label = jQuery('input[name="form_field_label['+row_number+']"]').val();
	var field_value = jQuery('input[name="form_field_value['+row_number+']"]').val();
	var field_name = jQuery('input[name="form_field_name['+row_number+']"]').val();
	var field_type = jQuery('input[name="form_field_type['+row_number+']"]').val();
//console.log( 'field_active, field_required, field_label, field_value, field_name, field_type', field_active, field_required, field_label, field_value, field_name, field_type );

	jQuery('.metabox_wpbc_form_field_free_generator').show();												// Show Generator section
	jQuery('.wpbc_field_generator').hide();																	// Hide inside of generator sub section  relative to fields types


	// FixIn: TimeFreeGenerator	- Exception - field with  name 'rangetime, have type 'rangetype' in Generator BUT, it have to  be saved as 'select' type'.
	if ( 'rangetime' == field_name ) {
		/**
		 *  Field 'rangetime_field_generator' have DIV section, which have CSS class 'wpbc_field_generator_rangetime',
		 *  but its also  defined with  type 'select'  for adding this field via    javascript:wpbc_add_field ( 'rangetime_field_generator', 'select' );
		 */

		field_type = 'rangetime';

		/**
		 * During editing 'field_required' == false,  because this field does not exist  in the Table with exist fields,  but we need to  set it to  true and disabled.
		 */
	}

	jQuery( '.wpbc_field_generator_' + field_type ).show();													            // Show specific generator sub section  relative to selected Field Type.
	jQuery( '#wpbc_form_field_free_generator_metabox h3.hndle span' ).html( 'Edit: ' + field_name );


	jQuery( '#' + field_type + '_field_generator_active' ).prop( 'checked', field_active );
	jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked', field_required );
	jQuery( '#' + field_type + '_field_generator_label' ).val( field_label );
	jQuery( '#' + field_type + '_field_generator_name' ).val( field_name );
	jQuery( '#' + field_type + '_field_generator_value' ).val( field_value );
	jQuery( '#' + field_type + '_field_generator_name' ).prop( 'disabled', true );

	jQuery( '.field_options_format').remove();

	// FixIn: TimeFreeGenerator.
	if ( ('rangetime' === field_name) ||
		('durationtime' === field_name) ||
		('starttime' === field_name) ||
		('endtime' === field_name)
	) {
		jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked', true ).prop( 'disabled', true );		// Set Disabled and Checked -- Required field.
		if ( 'durationtime' === field_name ) {
			jQuery( '#' + field_type + '_field_generator_value' ).after(
				'<div class="wpbc-settings-notice notice-info notice-helpful-info field_options_format" style="margin: 30px 0;">'+
				'The option format is "Title@@Time Duration," where "Title" is usually the service name and "Time Duration" is defined in the format HH:MM (HH = hours from 00 to 23, MM = minutes from 00 to 59).'
				+ '</div>'
			);
		}
		if ( 'starttime' === field_name ) {
			jQuery( '#' + field_type + '_field_generator_value' ).after(
				'<div class="wpbc-settings-notice notice-info notice-helpful-info field_options_format" style="margin: 30px 0;">'+
				'The option format is "Title@@Time," where "Title" is any text (typically the time in AM/PM format) and "Time" is defined in the 24-hour format (HH:MM), where HH = hours (00 to 23) and MM = minutes (00 to 59).'
				+ '</div>'
			);
		}
	}
	if ( 'rangetime' === field_name ) {
		wpbc_check_typed_values( field_name + '_field_generator' );														// Update Options and Titles for TimeSlots.
		wpbc_timeslots_table__fill_rows();
	}

	jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]' ).hide();						// FixIn: 8.7.11.7.
	jQuery( '#wpbc_settings__form_fields__toolbar' ).hide();

	wpbc_scroll_to( '#wpbc_form_field_free_generator_metabox' );
}


/**
 * Prepare fields data, and submit Edited field by clicking "Save changes" btn.
 *
 * @param field_name
 * @param field_type
 */
function wpbc_finish_edit_form_field( field_name, field_type ) {

	// FixIn: TimeFreeGenerator.
	var time_fields_arr       = [ 'rangetime', ]; // 'durationtime', 'starttime', 'endtime' ];
	var time_fields_arr_count = time_fields_arr.length;
	for ( var i = 0; i < time_fields_arr_count; i++ ) {
		// time_fields_arr[i] = 'rangetime'.
		if ( time_fields_arr[i] + '_field_generator' === field_name ) {
			var replaced_result = wpbc_get_saved_value_from_timeslots_table();
			if ( false === replaced_result ) {
				wpbc_hide_fields_generators();
				// TODO: Show warning at  the top of page,  about error during saving timeslots.
				console.log( 'error during parsing timeslots table and savig it.' )
				return;
			}
		}
	}


	// Get Values in  Edit Form ////////////////////////////////////

	//0: var field_type
	//1:
	var row_active = 'Off';
	var row_active_checked = false;
	if ( jQuery('#' + field_name + '_active').is( ":checked" ) ) {
		row_active = 'On';
		row_active_checked = true;
	}
	//2:
	var row_required = 'Off';
	var row_required_checked = false;
	if ( jQuery('#' + field_name + '_required').is( ":checked" ) ) {
		row_required = 'On';
		row_required_checked = true;
	}
	//3:
	var row_label = jQuery('#' + field_name + '_label').val();
	//4:
	var row_name = jQuery('#' + field_name + '_name').val();
	//5:
	var row_value = jQuery('#' + field_name + '_value').val();

	// Set  values to  the ROW in Fields Table /////////////////////
	//1:
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').prop( 'checked', row_active_checked );
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').val( row_active );
	//2:
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').prop( 'checked', row_required_checked );
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').val( row_required );
	//3:
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_label]').val( row_label );
//                //4:
//                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_name]').val( row_name );
//                //0:
//                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_type]').val( field_type );
	//5:
	jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_value]').val( row_value );
//                // Options field:
//                jQuery('.wpbc_table_form_free tr.highlight td.field_options input:disabled').val( field_type + '|' +  row_name );


	// Hide generators and Reset forms  and Disable highlighting
	wpbc_hide_fields_generators();

	// Submit form
	document.forms['wpbc_form_field_free'].submit();
}


/**
 * Check  Value and parse it to Options and Titles
 * @param {string} field_name
 */
function wpbc_check_typed_values( field_name ){

	var t_options_titles_arr = wpbc_get_titles_options_from_values( '#' + field_name + '_value' );

	if ( false !== t_options_titles_arr ) {

		var t_options = t_options_titles_arr[0].join( "\n" );
		var t_titles  = t_options_titles_arr[1].join( "\n" );
		jQuery('#' + field_name + '_options_options').val( t_options );
		jQuery('#' + field_name + '_options_titles').val( t_titles );

	}
}


/**
 * Get array  with  Options and Titles from  Values,  if in values was defined constrution  like this 			' Option @@ Title '
 * @param field_id string
 * @returns array | false
 */
function wpbc_get_titles_options_from_values( field_id ){
	if (    ( jQuery( field_id ).val() != '' )
		 && ( ! jQuery( field_id ).is(':disabled') )
		){

		var tslots = jQuery( field_id ).val();
		tslots = tslots.split('\n');
		var t_options = [];
		var t_titles  = [];
		var slot_t = '';

		if ( ( typeof tslots !== 'undefined' ) && ( tslots.length > 0 ) ){

			for ( var i=0; i < tslots.length; i++ ) {

				slot_t = tslots[ i ].split( '@@' );

				if ( slot_t.length > 1 ){
					t_options.push( slot_t[ 1 ].trim() );
					t_titles.push(  slot_t[ 0 ].trim() );
				} else {
					t_options.push( slot_t[ 0 ].trim() );
					t_titles.push(  '' );
				}
			}

		}
		var t_options_titles_arr = [];
		t_options_titles_arr.push( t_options );
		t_options_titles_arr.push( t_titles );

		return t_options_titles_arr;
	}
	return false;
}
