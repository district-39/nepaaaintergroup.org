"use strict";

/**
 *  Add 'last_selected', 'current' CSS classes  on FOCUS to table rows
 */
(function ($) {
  var controlled = false;
  var shifted = false;
  var hasFocus = false;
  $(document).on('keyup keydown', function (e) {
    shifted = e.shiftKey;
    controlled = e.ctrlKey || e.metaKey;
  });
  $('.wpbc_input_table').on('focus click', 'input', function (e) {
    var this_closest_table = $(this).closest('table');
    var this_closest_row = $(this).closest('tr');
    if (e.type == 'focus' && hasFocus != this_closest_row.index() || e.type == 'click' && $(this).is(':focus')) {
      hasFocus = this_closest_row.index();
      if (!shifted && !controlled) {
        $('tr', this_closest_table).removeClass('current').removeClass('last_selected');
        this_closest_row.addClass('current').addClass('last_selected');
      } else if (shifted) {
        $('tr', this_closest_table).removeClass('current');
        this_closest_row.addClass('selected_now').addClass('current');
        if ($('tr.last_selected', this_closest_table).size() > 0) {
          if (this_closest_row.index() > $('tr.last_selected', this_closest_table).index()) {
            $('tr', this_closest_table).slice($('tr.last_selected', this_closest_table).index(), this_closest_row.index()).addClass('current');
          } else {
            $('tr', this_closest_table).slice(this_closest_row.index(), $('tr.last_selected', this_closest_table).index() + 1).addClass('current');
          }
        }
        $('tr', this_closest_table).removeClass('last_selected');
        this_closest_row.addClass('last_selected');
      } else {
        $('tr', this_closest_table).removeClass('last_selected');
        if (controlled && $(this).closest('tr').is('.current')) {
          this_closest_row.removeClass('current');
        } else {
          this_closest_row.addClass('current').addClass('last_selected');
        }
      }
      $('tr', this_closest_table).removeClass('selected_now');
    }
  }).on('blur', 'input', function (e) {
    hasFocus = false;
  });
})(jQuery);

// Make Table sortable
function wpbc_make_table_sortable() {
  jQuery('.wpbc_input_table tbody th').css('cursor', 'move');
  jQuery('.wpbc_input_table tbody td.sort').css('cursor', 'move');
  jQuery('.wpbc_input_table.sortable tbody').sortable({
    items: 'tr',
    cursor: 'move',
    axis: 'y',
    // connectWith: ".wpbc_table_form_free tbody",					//// FixIn: 10.1.2.2.
    // //axis:'y',
    scrollSensitivity: 40,
    forcePlaceholderSize: true,
    helper: 'clone',
    opacity: 0.65,
    placeholder: '.wpbc_input_table .sort',
    start: function (event, ui) {
      ui.item.css('background-color', '#f6f6f6');
    },
    stop: function (event, ui) {
      ui.item.removeAttr('style');
    }
  });
}

// Activate row delete
function wpbc_activate_table_row_delete(del_btn_css_class, is_confirm) {
  // Delete Row
  jQuery(del_btn_css_class).on('click', function () {
    // FixIn: 8.7.11.12.

    if (true === is_confirm) {
      if (!wpbc_are_you_sure('Do you really want to do this ?')) {
        return false;
      }
    }
    var $current = jQuery(this).closest('tr');
    if ($current.size() > 0) {
      $current.each(function () {
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
function wpbc_check_typed_name(field_name) {
  // Set Name only Letters
  if (jQuery('#' + field_name + '_name').val() != '' && !jQuery('#' + field_name + '_name').is(':disabled')) {
    var p_name = jQuery('#' + field_name + '_name').val();
    p_name = p_name.replace(/[^A-Za-z0-9_-]*[0-9]*$/g, '').replace(/[^A-Za-z0-9_-]/g, '');
    p_name = p_name.toLowerCase();
    jQuery('input[name^=form_field_name]').each(function () {
      var text_value = jQuery(this).val();
      if (text_value == p_name) {
        // error element with this name exist

        p_name += '_' + Math.round(new Date().getTime()) + '_rand'; //Add random sufix
      }
    });
    jQuery('#' + field_name + '_name').val(p_name);
  }
}

/** Reset to default values all Form  fields for creation new fields */
function wpbc_reset_all_forms() {
  jQuery('.wpbc_table_form_free tr').removeClass('highlight');
  jQuery('.wpbc_add_field_row').hide();
  jQuery('.wpbc_edit_field_row').hide();
  var field_type_array = ['text', 'textarea', 'select', 'selectbox', 'checkbox', 'rangetime', 'durationtime', 'starttime', 'endtime']; //FixIn: TimeFreeGenerator
  var field_type;
  jQuery('.field_options_format').remove();
  for (var i = 0; i < field_type_array.length; i++) {
    field_type = field_type_array[i];
    if (!jQuery('#' + field_type + '_field_generator_name').is(':disabled')) {
      //FixIn: TimeFreeGenerator
      jQuery('#' + field_type + '_field_generator_active').prop('checked', true);
      jQuery('#' + field_type + '_field_generator_required').prop('checked', false);
      jQuery('#' + field_type + '_field_generator_label').val('');
      jQuery('#' + field_type + '_field_generator_name').prop('disabled', false);
      jQuery('#' + field_type + '_field_generator_name').val('');
      jQuery('#' + field_type + '_field_generator_value').val('');
    }
  }
}

/**
 * Show selected Add New Field form, and reset fields in this form
 *
 * @param string selected_field_value
 */
function wpbc_show_fields_generator(selected_field_value) {
  if ('text' === selected_field_value || 'textarea' === selected_field_value || 'select' === selected_field_value || 'selectbox' === selected_field_value || 'checkbox' === selected_field_value) {
    if ('select' === selected_field_value) {
      jQuery('#' + 'selectbox' + '_field_generator_name').prop('disabled', false);
    }
    jQuery('#' + selected_field_value + '_field_generator_name').prop('disabled', false);
  }
  wpbc_reset_all_forms();
  var time_fields_arr = ['rangetime', 'durationtime', 'starttime', 'endtime'];
  var time_fields_arr_count = time_fields_arr.length;
  for (var i = 0; i < time_fields_arr_count; i++) {
    // time_fields_arr[i] = 'rangetime'.
    if ('edit_' + time_fields_arr[i] === selected_field_value) {
      // This field already  exist  in the booking form,  and thats why  we can  not add a new field,  and instead of that  edit it.
      var range_time_edit_field = jQuery('.wpbc_table_form_free :input[value="' + time_fields_arr[i] + '"]'); // time_fields_arr[i] = 'rangetime'.
      var range_time_field_num = 0;
      if (range_time_edit_field.length > 0) {
        var range_time_edit_field_name = jQuery(range_time_edit_field.get(0)).attr('name');
        range_time_edit_field_name = range_time_edit_field_name.replaceAll('form_field_name[', '').replaceAll(']', '');
        range_time_field_num = parseInt(range_time_edit_field_name);
        if (range_time_field_num > 0) {
          wpbc_start_edit_form_field(range_time_field_num);
        }
      }
      if (0 === range_time_field_num) {
        selected_field_value = time_fields_arr[i]; // time_fields_arr[i] = 'rangetime'.
      } else {
        return;
      }
    }
  }
  if (selected_field_value == 'selector_hint') {
    jQuery('.metabox_wpbc_form_field_free_generator').hide();
    jQuery('#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show(); // FixIn: 8.7.11.7.
    jQuery('#wpbc_settings__form_fields__toolbar').show();
  } else {
    jQuery('.metabox_wpbc_form_field_free_generator').show();
    jQuery('.wpbc_field_generator').hide();
    jQuery('.wpbc_field_generator_' + selected_field_value).show();
    jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html(jQuery('#select_form_help_shortcode option:selected').text());
    jQuery('.wpbc_add_field_row').show();
    jQuery('#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').hide(); // FixIn: 8.7.11.7.
    jQuery('#wpbc_settings__form_fields__toolbar').hide();
  }
}

/** Hide all Add New Field forms, and reset fields in these forms*/
function wpbc_hide_fields_generators() {
  wpbc_reset_all_forms();
  jQuery('.metabox_wpbc_form_field_free_generator').hide();
  jQuery('#select_form_help_shortcode>option:eq(0)').attr('selected', true);
  jQuery('#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show(); // FixIn: 8.7.11.7.
  jQuery('#wpbc_settings__form_fields__toolbar').show();
}

/**
 * Add New Row with new Field to Table and Submit Saving changes.
 *
 * @param {string} field_name
 * @param {string} field_type
 */
function wpbc_add_field(field_name, field_type) {
  // FixIn: TimeFreeGenerator.
  var time_fields_arr = ['rangetime']; //, 'durationtime', 'starttime', 'endtime' ]; .
  var time_fields_arr_count = time_fields_arr.length;
  for (var i = 0; i < time_fields_arr_count; i++) {
    // time_fields_arr[i] = 'rangetime'.
    if (time_fields_arr[i] + '_field_generator' === field_name) {
      var replaced_result = wpbc_get_saved_value_from_timeslots_table();
      if (false === replaced_result) {
        wpbc_hide_fields_generators();
        //TOO: Show warning at  the top of page,  about error during saving timeslots
        console.log('error during parsing timeslots tbale and savig it.');
        return;
      }
    }
  }
  if (jQuery('#' + field_name + '_name').val() != '') {
    wpbc_check_typed_name(field_name);
    var row_num = jQuery('.wpbc_table_form_free tbody tr').length + Math.round(new Date().getTime());
    var row_active = 'Off';
    var row_active_checked = '';
    if (jQuery('#' + field_name + '_active').is(":checked")) {
      row_active = 'On';
      row_active_checked = ' checked="checked" ';
    }
    var row_required = 'Off';
    var row_required_checked = '';
    if (jQuery('#' + field_name + '_required').is(":checked")) {
      row_required = 'On';
      row_required_checked = ' checked="checked" ';
    }
    var row;
    row = '<tr class="account ui-sortable-handle">';

    ////////////////////////////////////////////////////////////
    row += '<td class="sort" style="cursor: move;"><span class="wpbc_icn_drag_indicator" aria-hidden="true"></span></td>';
    row += '<td class="field_active">';
    row += '<input type="checkbox" name="form_field_active[' + row_num + ']" value="' + row_active + '" ' + row_active_checked + ' autocomplete="off" />';
    row += '</td>';

    ////////////////////////////////////////////////////////////
    row += '<td class="field_label">';

    //row +=      '<legend class="screen-reader-text"><span>' + jQuery('#' + field_name + '_label').val() + '</span></legend>';

    row += '<input type="text" name="form_field_label[' + row_num + ']" value="' + jQuery('#' + field_name + '_label').val() + '" placeholder="' + jQuery('#' + field_name + '_label').val() + '" class="regular-text" autocomplete="off" />';
    row += '<div class="field_type_name_description">';
    //row +=        			'<?php echo esc_js( __( 'Type', 'booking' ) ); ?>: <div class="field_type_name_value">' +field_type+ '</div>';
    row += 'Type: <div class="field_type_name_value">' + field_type + '</div>';
    row += '<span class="field_type_name_separator">|</span>';
    //row +=        			'<?php echo esc_js( __( 'Name', 'booking' ) ); ?>: <div class="field_type_name_value">' + jQuery('#' + field_name + '_name').val() + '</div>';
    row += 'Name: <div class="field_type_name_value">' + jQuery('#' + field_name + '_name').val() + '</div>';
    row += '</div>';
    row += '<input type="hidden" value="' + ('select' == field_type ? 'selectbox' : field_type) + '"  name="form_field_type[' + row_num + ']" autocomplete="off" />';
    row += '<input type="hidden" value="' + jQuery('#' + field_name + '_name').val() + '"  name="form_field_name[' + row_num + ']" autocomplete="off" />';
    row += '<input type="hidden" value="' + (jQuery('#' + field_name + '_value').length ? jQuery('#' + field_name + '_value').val() : '') + '"  name="form_field_value[' + row_num + ']" autocomplete="off" />';
    row += '</td>';

    ////////////////////////////////////////////////////////////
    row += '<td class="field_required">';

    // FixIn:  TimeFreeGenerator.
    if ('rangetime' === field_name || 'durationtime' === field_name || 'starttime' === field_name || 'endtime' === field_name) {
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
    jQuery('.wpbc_table_form_free tbody').append(row);
    wpbc_hide_fields_generators();
    document.forms['wpbc_form_field_free'].submit(); //Submit form
  } else {
    wpbc_field_highlight('#' + field_name + '_name');
  }
}

/**
 * Prepare Edit section for editing specific field.
 * @param row_number
 */
function wpbc_start_edit_form_field(row_number) {
  wpbc_reset_all_forms(); // Reset Fields in all generator rows (text,select,...) to init (empty) values
  jQuery('.wpbc_edit_field_row').show(); // Show row with edit btn

  jQuery('.wpbc_table_form_free tr').removeClass('highlight');
  jQuery('input[name="form_field_name[' + row_number + ']"]').closest('tr').addClass('highlight'); //Highlight row

  // Get exist data from EXIST fields Table
  var field_active = jQuery('input[name="form_field_active[' + row_number + ']"]').is(":checked");
  var field_required = jQuery('input[name="form_field_required[' + row_number + ']"]').is(":checked");
  var field_label = jQuery('input[name="form_field_label[' + row_number + ']"]').val();
  var field_value = jQuery('input[name="form_field_value[' + row_number + ']"]').val();
  var field_name = jQuery('input[name="form_field_name[' + row_number + ']"]').val();
  var field_type = jQuery('input[name="form_field_type[' + row_number + ']"]').val();
  //console.log( 'field_active, field_required, field_label, field_value, field_name, field_type', field_active, field_required, field_label, field_value, field_name, field_type );

  jQuery('.metabox_wpbc_form_field_free_generator').show(); // Show Generator section
  jQuery('.wpbc_field_generator').hide(); // Hide inside of generator sub section  relative to fields types

  // FixIn: TimeFreeGenerator	- Exception - field with  name 'rangetime, have type 'rangetype' in Generator BUT, it have to  be saved as 'select' type'.
  if ('rangetime' == field_name) {
    /**
     *  Field 'rangetime_field_generator' have DIV section, which have CSS class 'wpbc_field_generator_rangetime',
     *  but its also  defined with  type 'select'  for adding this field via    javascript:wpbc_add_field ( 'rangetime_field_generator', 'select' );
     */

    field_type = 'rangetime';

    /**
     * During editing 'field_required' == false,  because this field does not exist  in the Table with exist fields,  but we need to  set it to  true and disabled.
     */
  }
  jQuery('.wpbc_field_generator_' + field_type).show(); // Show specific generator sub section  relative to selected Field Type.
  jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html('Edit: ' + field_name);
  jQuery('#' + field_type + '_field_generator_active').prop('checked', field_active);
  jQuery('#' + field_type + '_field_generator_required').prop('checked', field_required);
  jQuery('#' + field_type + '_field_generator_label').val(field_label);
  jQuery('#' + field_type + '_field_generator_name').val(field_name);
  jQuery('#' + field_type + '_field_generator_value').val(field_value);
  jQuery('#' + field_type + '_field_generator_name').prop('disabled', true);
  jQuery('.field_options_format').remove();

  // FixIn: TimeFreeGenerator.
  if ('rangetime' === field_name || 'durationtime' === field_name || 'starttime' === field_name || 'endtime' === field_name) {
    jQuery('#' + field_type + '_field_generator_required').prop('checked', true).prop('disabled', true); // Set Disabled and Checked -- Required field.
    if ('durationtime' === field_name) {
      jQuery('#' + field_type + '_field_generator_value').after('<div class="wpbc-settings-notice notice-info notice-helpful-info field_options_format" style="margin: 30px 0;">' + 'The option format is "Title@@Time Duration," where "Title" is usually the service name and "Time Duration" is defined in the format HH:MM (HH = hours from 00 to 23, MM = minutes from 00 to 59).' + '</div>');
    }
    if ('starttime' === field_name) {
      jQuery('#' + field_type + '_field_generator_value').after('<div class="wpbc-settings-notice notice-info notice-helpful-info field_options_format" style="margin: 30px 0;">' + 'The option format is "Title@@Time," where "Title" is any text (typically the time in AM/PM format) and "Time" is defined in the 24-hour format (HH:MM), where HH = hours (00 to 23) and MM = minutes (00 to 59).' + '</div>');
    }
  }
  if ('rangetime' === field_name) {
    wpbc_check_typed_values(field_name + '_field_generator'); // Update Options and Titles for TimeSlots.
    wpbc_timeslots_table__fill_rows();
  }
  jQuery('#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').hide(); // FixIn: 8.7.11.7.
  jQuery('#wpbc_settings__form_fields__toolbar').hide();
  wpbc_scroll_to('#wpbc_form_field_free_generator_metabox');
}

/**
 * Prepare fields data, and submit Edited field by clicking "Save changes" btn.
 *
 * @param field_name
 * @param field_type
 */
function wpbc_finish_edit_form_field(field_name, field_type) {
  // FixIn: TimeFreeGenerator.
  var time_fields_arr = ['rangetime']; // 'durationtime', 'starttime', 'endtime' ];
  var time_fields_arr_count = time_fields_arr.length;
  for (var i = 0; i < time_fields_arr_count; i++) {
    // time_fields_arr[i] = 'rangetime'.
    if (time_fields_arr[i] + '_field_generator' === field_name) {
      var replaced_result = wpbc_get_saved_value_from_timeslots_table();
      if (false === replaced_result) {
        wpbc_hide_fields_generators();
        // TODO: Show warning at  the top of page,  about error during saving timeslots.
        console.log('error during parsing timeslots table and savig it.');
        return;
      }
    }
  }

  // Get Values in  Edit Form ////////////////////////////////////

  //0: var field_type
  //1:
  var row_active = 'Off';
  var row_active_checked = false;
  if (jQuery('#' + field_name + '_active').is(":checked")) {
    row_active = 'On';
    row_active_checked = true;
  }
  //2:
  var row_required = 'Off';
  var row_required_checked = false;
  if (jQuery('#' + field_name + '_required').is(":checked")) {
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
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').prop('checked', row_active_checked);
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').val(row_active);
  //2:
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').prop('checked', row_required_checked);
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').val(row_required);
  //3:
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_label]').val(row_label);
  //                //4:
  //                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_name]').val( row_name );
  //                //0:
  //                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_type]').val( field_type );
  //5:
  jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_value]').val(row_value);
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
function wpbc_check_typed_values(field_name) {
  var t_options_titles_arr = wpbc_get_titles_options_from_values('#' + field_name + '_value');
  if (false !== t_options_titles_arr) {
    var t_options = t_options_titles_arr[0].join("\n");
    var t_titles = t_options_titles_arr[1].join("\n");
    jQuery('#' + field_name + '_options_options').val(t_options);
    jQuery('#' + field_name + '_options_titles').val(t_titles);
  }
}

/**
 * Get array  with  Options and Titles from  Values,  if in values was defined constrution  like this 			' Option @@ Title '
 * @param field_id string
 * @returns array | false
 */
function wpbc_get_titles_options_from_values(field_id) {
  if (jQuery(field_id).val() != '' && !jQuery(field_id).is(':disabled')) {
    var tslots = jQuery(field_id).val();
    tslots = tslots.split('\n');
    var t_options = [];
    var t_titles = [];
    var slot_t = '';
    if (typeof tslots !== 'undefined' && tslots.length > 0) {
      for (var i = 0; i < tslots.length; i++) {
        slot_t = tslots[i].split('@@');
        if (slot_t.length > 1) {
          t_options.push(slot_t[1].trim());
          t_titles.push(slot_t[0].trim());
        } else {
          t_options.push(slot_t[0].trim());
          t_titles.push('');
        }
      }
    }
    var t_options_titles_arr = [];
    t_options_titles_arr.push(t_options);
    t_options_titles_arr.push(t_titles);
    return t_options_titles_arr;
  }
  return false;
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1mb3JtLXNpbXBsZS9fb3V0L3dwYmNfc2ltcGxlX2Zvcm0uanMiLCJuYW1lcyI6WyIkIiwiY29udHJvbGxlZCIsInNoaWZ0ZWQiLCJoYXNGb2N1cyIsImRvY3VtZW50Iiwib24iLCJlIiwic2hpZnRLZXkiLCJjdHJsS2V5IiwibWV0YUtleSIsInRoaXNfY2xvc2VzdF90YWJsZSIsImNsb3Nlc3QiLCJ0aGlzX2Nsb3Nlc3Rfcm93IiwidHlwZSIsImluZGV4IiwiaXMiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwic2l6ZSIsInNsaWNlIiwialF1ZXJ5Iiwid3BiY19tYWtlX3RhYmxlX3NvcnRhYmxlIiwiY3NzIiwic29ydGFibGUiLCJpdGVtcyIsImN1cnNvciIsImF4aXMiLCJzY3JvbGxTZW5zaXRpdml0eSIsImZvcmNlUGxhY2Vob2xkZXJTaXplIiwiaGVscGVyIiwib3BhY2l0eSIsInBsYWNlaG9sZGVyIiwic3RhcnQiLCJldmVudCIsInVpIiwiaXRlbSIsInN0b3AiLCJyZW1vdmVBdHRyIiwid3BiY19hY3RpdmF0ZV90YWJsZV9yb3dfZGVsZXRlIiwiZGVsX2J0bl9jc3NfY2xhc3MiLCJpc19jb25maXJtIiwid3BiY19hcmVfeW91X3N1cmUiLCIkY3VycmVudCIsImVhY2giLCJyZW1vdmUiLCJ3cGJjX2NoZWNrX3R5cGVkX25hbWUiLCJmaWVsZF9uYW1lIiwidmFsIiwicF9uYW1lIiwicmVwbGFjZSIsInRvTG93ZXJDYXNlIiwidGV4dF92YWx1ZSIsIk1hdGgiLCJyb3VuZCIsIkRhdGUiLCJnZXRUaW1lIiwid3BiY19yZXNldF9hbGxfZm9ybXMiLCJoaWRlIiwiZmllbGRfdHlwZV9hcnJheSIsImZpZWxkX3R5cGUiLCJpIiwibGVuZ3RoIiwicHJvcCIsIndwYmNfc2hvd19maWVsZHNfZ2VuZXJhdG9yIiwic2VsZWN0ZWRfZmllbGRfdmFsdWUiLCJ0aW1lX2ZpZWxkc19hcnIiLCJ0aW1lX2ZpZWxkc19hcnJfY291bnQiLCJyYW5nZV90aW1lX2VkaXRfZmllbGQiLCJyYW5nZV90aW1lX2ZpZWxkX251bSIsInJhbmdlX3RpbWVfZWRpdF9maWVsZF9uYW1lIiwiZ2V0IiwiYXR0ciIsInJlcGxhY2VBbGwiLCJwYXJzZUludCIsIndwYmNfc3RhcnRfZWRpdF9mb3JtX2ZpZWxkIiwic2hvdyIsImh0bWwiLCJ0ZXh0Iiwid3BiY19oaWRlX2ZpZWxkc19nZW5lcmF0b3JzIiwid3BiY19hZGRfZmllbGQiLCJyZXBsYWNlZF9yZXN1bHQiLCJ3cGJjX2dldF9zYXZlZF92YWx1ZV9mcm9tX3RpbWVzbG90c190YWJsZSIsImNvbnNvbGUiLCJsb2ciLCJyb3dfbnVtIiwicm93X2FjdGl2ZSIsInJvd19hY3RpdmVfY2hlY2tlZCIsInJvd19yZXF1aXJlZCIsInJvd19yZXF1aXJlZF9jaGVja2VkIiwicm93IiwiYXBwZW5kIiwiZm9ybXMiLCJzdWJtaXQiLCJ3cGJjX2ZpZWxkX2hpZ2hsaWdodCIsInJvd19udW1iZXIiLCJmaWVsZF9hY3RpdmUiLCJmaWVsZF9yZXF1aXJlZCIsImZpZWxkX2xhYmVsIiwiZmllbGRfdmFsdWUiLCJhZnRlciIsIndwYmNfY2hlY2tfdHlwZWRfdmFsdWVzIiwid3BiY190aW1lc2xvdHNfdGFibGVfX2ZpbGxfcm93cyIsIndwYmNfc2Nyb2xsX3RvIiwid3BiY19maW5pc2hfZWRpdF9mb3JtX2ZpZWxkIiwicm93X2xhYmVsIiwicm93X25hbWUiLCJyb3dfdmFsdWUiLCJ0X29wdGlvbnNfdGl0bGVzX2FyciIsIndwYmNfZ2V0X3RpdGxlc19vcHRpb25zX2Zyb21fdmFsdWVzIiwidF9vcHRpb25zIiwiam9pbiIsInRfdGl0bGVzIiwiZmllbGRfaWQiLCJ0c2xvdHMiLCJzcGxpdCIsInNsb3RfdCIsInB1c2giLCJ0cmltIl0sInNvdXJjZXMiOlsiaW5jbHVkZXMvcGFnZS1mb3JtLXNpbXBsZS9fc3JjL3dwYmNfc2ltcGxlX2Zvcm0uanMiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqICBBZGQgJ2xhc3Rfc2VsZWN0ZWQnLCAnY3VycmVudCcgQ1NTIGNsYXNzZXMgIG9uIEZPQ1VTIHRvIHRhYmxlIHJvd3NcclxuICovXHJcbiggZnVuY3Rpb24oICQgKXtcclxuXHR2YXIgY29udHJvbGxlZCA9IGZhbHNlO1xyXG5cdHZhciBzaGlmdGVkID0gZmFsc2U7XHJcblx0dmFyIGhhc0ZvY3VzID0gZmFsc2U7XHJcblxyXG5cdCQoZG9jdW1lbnQpLm9uKCdrZXl1cCBrZXlkb3duJywgZnVuY3Rpb24oZSl7IHNoaWZ0ZWQgPSBlLnNoaWZ0S2V5OyBjb250cm9sbGVkID0gZS5jdHJsS2V5IHx8IGUubWV0YUtleSB9ICk7XHJcblxyXG5cdCQoJy53cGJjX2lucHV0X3RhYmxlJykub24oICdmb2N1cyBjbGljaycsICdpbnB1dCcsIGZ1bmN0aW9uKCBlICkge1xyXG5cclxuXHRcdFx0dmFyIHRoaXNfY2xvc2VzdF90YWJsZSA9ICQodGhpcykuY2xvc2VzdCgndGFibGUnKTtcclxuXHRcdFx0dmFyIHRoaXNfY2xvc2VzdF9yb3cgICA9ICQodGhpcykuY2xvc2VzdCgndHInKTtcclxuXHJcblx0XHRcdGlmICggKCBlLnR5cGUgPT0gJ2ZvY3VzJyAmJiBoYXNGb2N1cyAhPSB0aGlzX2Nsb3Nlc3Rfcm93LmluZGV4KCkgKSB8fCAoIGUudHlwZSA9PSAnY2xpY2snICYmICQodGhpcykuaXMoJzpmb2N1cycpICkgKSB7XHJcblxyXG5cdFx0XHRcdFx0aGFzRm9jdXMgPSB0aGlzX2Nsb3Nlc3Rfcm93LmluZGV4KCk7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCAhIHNoaWZ0ZWQgJiYgISBjb250cm9sbGVkICkge1xyXG5cdFx0XHRcdFx0XHRcdCQoJ3RyJywgdGhpc19jbG9zZXN0X3RhYmxlKS5yZW1vdmVDbGFzcygnY3VycmVudCcpLnJlbW92ZUNsYXNzKCdsYXN0X3NlbGVjdGVkJyk7XHJcblx0XHRcdFx0XHRcdFx0dGhpc19jbG9zZXN0X3Jvdy5hZGRDbGFzcygnY3VycmVudCcpLmFkZENsYXNzKCdsYXN0X3NlbGVjdGVkJyk7XHJcblx0XHRcdFx0XHR9IGVsc2UgaWYgKCBzaGlmdGVkICkge1xyXG5cdFx0XHRcdFx0XHRcdCQoJ3RyJywgdGhpc19jbG9zZXN0X3RhYmxlKS5yZW1vdmVDbGFzcygnY3VycmVudCcpO1xyXG5cdFx0XHRcdFx0XHRcdHRoaXNfY2xvc2VzdF9yb3cuYWRkQ2xhc3MoJ3NlbGVjdGVkX25vdycpLmFkZENsYXNzKCdjdXJyZW50Jyk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdGlmICggJCgndHIubGFzdF9zZWxlY3RlZCcsIHRoaXNfY2xvc2VzdF90YWJsZSkuc2l6ZSgpID4gMCApIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0aWYgKCB0aGlzX2Nsb3Nlc3Rfcm93LmluZGV4KCkgPiAkKCd0ci5sYXN0X3NlbGVjdGVkJywgdGhpc19jbG9zZXN0X3RhYmxlKS5pbmRleCgpICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0JCgndHInLCB0aGlzX2Nsb3Nlc3RfdGFibGUpLnNsaWNlKCAkKCd0ci5sYXN0X3NlbGVjdGVkJywgdGhpc19jbG9zZXN0X3RhYmxlKS5pbmRleCgpLCB0aGlzX2Nsb3Nlc3Rfcm93LmluZGV4KCkgKS5hZGRDbGFzcygnY3VycmVudCcpO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0JCgndHInLCB0aGlzX2Nsb3Nlc3RfdGFibGUpLnNsaWNlKCB0aGlzX2Nsb3Nlc3Rfcm93LmluZGV4KCksICQoJ3RyLmxhc3Rfc2VsZWN0ZWQnLCB0aGlzX2Nsb3Nlc3RfdGFibGUpLmluZGV4KCkgKyAxICkuYWRkQ2xhc3MoJ2N1cnJlbnQnKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRcdFx0JCgndHInLCB0aGlzX2Nsb3Nlc3RfdGFibGUpLnJlbW92ZUNsYXNzKCdsYXN0X3NlbGVjdGVkJyk7XHJcblx0XHRcdFx0XHRcdFx0dGhpc19jbG9zZXN0X3Jvdy5hZGRDbGFzcygnbGFzdF9zZWxlY3RlZCcpO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0XHQkKCd0cicsIHRoaXNfY2xvc2VzdF90YWJsZSkucmVtb3ZlQ2xhc3MoJ2xhc3Rfc2VsZWN0ZWQnKTtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGNvbnRyb2xsZWQgJiYgJCh0aGlzKS5jbG9zZXN0KCd0cicpLmlzKCcuY3VycmVudCcpICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHR0aGlzX2Nsb3Nlc3Rfcm93LnJlbW92ZUNsYXNzKCdjdXJyZW50Jyk7XHJcblx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0dGhpc19jbG9zZXN0X3Jvdy5hZGRDbGFzcygnY3VycmVudCcpLmFkZENsYXNzKCdsYXN0X3NlbGVjdGVkJyk7XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdCQoJ3RyJywgdGhpc19jbG9zZXN0X3RhYmxlKS5yZW1vdmVDbGFzcygnc2VsZWN0ZWRfbm93Jyk7XHJcblxyXG5cdFx0XHR9XHJcblx0fSkub24oICdibHVyJywgJ2lucHV0JywgZnVuY3Rpb24oIGUgKSB7XHJcblx0XHRcdGhhc0ZvY3VzID0gZmFsc2U7XHJcblx0fSk7XHJcblxyXG59KCBqUXVlcnkgKSApO1xyXG5cclxuXHJcbi8vIE1ha2UgVGFibGUgc29ydGFibGVcclxuZnVuY3Rpb24gd3BiY19tYWtlX3RhYmxlX3NvcnRhYmxlKCl7XHJcblxyXG5cdGpRdWVyeSgnLndwYmNfaW5wdXRfdGFibGUgdGJvZHkgdGgnKS5jc3MoJ2N1cnNvcicsJ21vdmUnKTtcclxuXHJcblx0alF1ZXJ5KCcud3BiY19pbnB1dF90YWJsZSB0Ym9keSB0ZC5zb3J0JykuY3NzKCdjdXJzb3InLCdtb3ZlJyk7XHJcblxyXG5cdGpRdWVyeSgnLndwYmNfaW5wdXRfdGFibGUuc29ydGFibGUgdGJvZHknKS5zb3J0YWJsZSh7XHJcblx0XHRcdGl0ZW1zOid0cicsXHJcblx0XHRcdGN1cnNvcjonbW92ZScsXHJcblx0XHRcdGF4aXM6J3knLFxyXG4vLyBjb25uZWN0V2l0aDogXCIud3BiY190YWJsZV9mb3JtX2ZyZWUgdGJvZHlcIixcdFx0XHRcdFx0Ly8vLyBGaXhJbjogMTAuMS4yLjIuXHJcbi8vIC8vYXhpczoneScsXHJcblx0XHRcdHNjcm9sbFNlbnNpdGl2aXR5OjQwLFxyXG5cdFx0XHRmb3JjZVBsYWNlaG9sZGVyU2l6ZTogdHJ1ZSxcclxuXHRcdFx0aGVscGVyOiAnY2xvbmUnLFxyXG5cdFx0XHRvcGFjaXR5OiAwLjY1LFxyXG5cdFx0XHRwbGFjZWhvbGRlcjogJy53cGJjX2lucHV0X3RhYmxlIC5zb3J0JyxcclxuXHRcdFx0c3RhcnQ6ZnVuY3Rpb24oZXZlbnQsdWkpe1xyXG5cdFx0XHRcdFx0dWkuaXRlbS5jc3MoJ2JhY2tncm91bmQtY29sb3InLCcjZjZmNmY2Jyk7XHJcblx0XHRcdH0sXHJcblx0XHRcdHN0b3A6ZnVuY3Rpb24oZXZlbnQsdWkpe1xyXG5cdFx0XHRcdFx0dWkuaXRlbS5yZW1vdmVBdHRyKCdzdHlsZScpO1xyXG5cdFx0XHR9XHJcblx0fSk7XHJcbn1cclxuXHJcblxyXG4vLyBBY3RpdmF0ZSByb3cgZGVsZXRlXHJcbmZ1bmN0aW9uIHdwYmNfYWN0aXZhdGVfdGFibGVfcm93X2RlbGV0ZSggZGVsX2J0bl9jc3NfY2xhc3MsIGlzX2NvbmZpcm0gKXtcclxuXHJcblx0Ly8gRGVsZXRlIFJvd1xyXG5cdGpRdWVyeSggZGVsX2J0bl9jc3NfY2xhc3MgKS5vbiggJ2NsaWNrJywgZnVuY3Rpb24oKXsgICAgICAgICAgICAgICAgICAgLy8gRml4SW46IDguNy4xMS4xMi5cclxuXHJcblx0XHRpZiAoIHRydWUgPT09IGlzX2NvbmZpcm0gKXtcclxuXHRcdFx0aWYgKCAhIHdwYmNfYXJlX3lvdV9zdXJlKCAnRG8geW91IHJlYWxseSB3YW50IHRvIGRvIHRoaXMgPycgKSApe1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHZhciAkY3VycmVudCA9IGpRdWVyeSh0aGlzKS5jbG9zZXN0KCd0cicpO1xyXG5cdFx0aWYgKCAkY3VycmVudC5zaXplKCkgPiAwICkge1xyXG5cdFx0XHQkY3VycmVudC5lYWNoKGZ1bmN0aW9uKCl7XHJcblx0XHRcdFx0XHRqUXVlcnkodGhpcykucmVtb3ZlKCk7XHJcblx0XHRcdH0pO1xyXG5cdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fSk7XHJcblxyXG59XHJcblxyXG5cclxuLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vL1xyXG4vLyBGaWVsZHMgR2VuZXJhdG9yIFNlY3Rpb25cclxuLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vL1xyXG5cclxuXHJcbi8qKlxyXG4gKiBDaGVjayAgTmFtZSAgaW4gIFwiZmllbGQgZm9ybVwiIGFib3V0IHBvc3NpYmxlIHVzYWdlIG9mIHRoaXMgbmFtZSBhbmQgYWJvdXQgIGFueSBEdXBsaWNhdGVzIGluIEZpbGRzIFRhYmxlXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBmaWVsZF9uYW1lXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NoZWNrX3R5cGVkX25hbWUoIGZpZWxkX25hbWUgKXtcclxuXHJcblx0Ly8gU2V0IE5hbWUgb25seSBMZXR0ZXJzXHJcblx0aWYgKCAgICAoIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJykudmFsKCkgIT0gJycgKVxyXG5cdFx0ICYmICggISBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfbmFtZScpLmlzKCc6ZGlzYWJsZWQnKSApXHJcblx0XHQpe1xyXG5cdFx0dmFyIHBfbmFtZSA9IGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJykudmFsKCk7XHJcblx0XHRwX25hbWUgPSBwX25hbWUucmVwbGFjZSgvW15BLVphLXowLTlfLV0qWzAtOV0qJC9nLCcnKS5yZXBsYWNlKC9bXkEtWmEtejAtOV8tXS9nLCcnKTtcclxuXHRcdHBfbmFtZSA9IHBfbmFtZS50b0xvd2VyQ2FzZSgpO1xyXG5cclxuXHJcblx0XHRqUXVlcnkoJ2lucHV0W25hbWVePWZvcm1fZmllbGRfbmFtZV0nKS5lYWNoKGZ1bmN0aW9uKCl7XHJcblx0XHRcdHZhciB0ZXh0X3ZhbHVlID0galF1ZXJ5KHRoaXMpLnZhbCgpO1xyXG5cdFx0XHRpZiggdGV4dF92YWx1ZSA9PSBwX25hbWUgKSB7ICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGVycm9yIGVsZW1lbnQgd2l0aCB0aGlzIG5hbWUgZXhpc3RcclxuXHJcblx0XHRcdFx0cF9uYW1lICs9ICAnXycgKyBNYXRoLnJvdW5kKCBuZXcgRGF0ZSgpLmdldFRpbWUoKSAgKSArICdfcmFuZCc7ICAgICAgICAgLy9BZGQgcmFuZG9tIHN1Zml4XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cclxuXHRcdGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJykudmFsKCBwX25hbWUgKTtcclxuXHR9XHJcbn1cclxuXHJcblxyXG4vKiogUmVzZXQgdG8gZGVmYXVsdCB2YWx1ZXMgYWxsIEZvcm0gIGZpZWxkcyBmb3IgY3JlYXRpb24gbmV3IGZpZWxkcyAqL1xyXG5mdW5jdGlvbiB3cGJjX3Jlc2V0X2FsbF9mb3Jtcygpe1xyXG5cclxuXHRqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0cicpLnJlbW92ZUNsYXNzKCdoaWdobGlnaHQnKTtcclxuXHRqUXVlcnkoJy53cGJjX2FkZF9maWVsZF9yb3cnKS5oaWRlKCk7XHJcblx0alF1ZXJ5KCcud3BiY19lZGl0X2ZpZWxkX3JvdycpLmhpZGUoKTtcclxuXHJcblx0dmFyIGZpZWxkX3R5cGVfYXJyYXkgPSBbICd0ZXh0JywgJ3RleHRhcmVhJywgJ3NlbGVjdCcsICdzZWxlY3Rib3gnLCAnY2hlY2tib3gnLCAncmFuZ2V0aW1lJywgJ2R1cmF0aW9udGltZScsICdzdGFydHRpbWUnLCAnZW5kdGltZScgXTtcdFx0XHRcdFx0XHQvL0ZpeEluOiBUaW1lRnJlZUdlbmVyYXRvclxyXG5cdHZhciBmaWVsZF90eXBlO1xyXG5cclxuXHRqUXVlcnkoICcuZmllbGRfb3B0aW9uc19mb3JtYXQnKS5yZW1vdmUoKTtcclxuXHJcblx0Zm9yICh2YXIgaSA9IDA7IGkgPCBmaWVsZF90eXBlX2FycmF5Lmxlbmd0aDsgaSsrKSB7XHJcblx0XHRmaWVsZF90eXBlID0gZmllbGRfdHlwZV9hcnJheVtpXTtcclxuXHJcblx0XHRpZiAoICEgalF1ZXJ5KCcjJyArIGZpZWxkX3R5cGUgKyAnX2ZpZWxkX2dlbmVyYXRvcl9uYW1lJykuaXMoJzpkaXNhYmxlZCcpICl7XHRcdFx0XHRcdFx0Ly9GaXhJbjogVGltZUZyZWVHZW5lcmF0b3JcclxuXHRcdFx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfYWN0aXZlJyApLnByb3AoICdjaGVja2VkJywgdHJ1ZSApO1xyXG5cdFx0XHRqUXVlcnkoICcjJyArIGZpZWxkX3R5cGUgKyAnX2ZpZWxkX2dlbmVyYXRvcl9yZXF1aXJlZCcgKS5wcm9wKCAnY2hlY2tlZCcsIGZhbHNlICk7XHJcblx0XHRcdGpRdWVyeSggJyMnICsgZmllbGRfdHlwZSArICdfZmllbGRfZ2VuZXJhdG9yX2xhYmVsJyApLnZhbCggJycgKTtcclxuXHJcblx0XHRcdGpRdWVyeSggJyMnICsgZmllbGRfdHlwZSArICdfZmllbGRfZ2VuZXJhdG9yX25hbWUnICkucHJvcCggJ2Rpc2FibGVkJywgZmFsc2UgKTtcclxuXHRcdFx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfbmFtZScgKS52YWwoICcnICk7XHJcblx0XHRcdGpRdWVyeSggJyMnICsgZmllbGRfdHlwZSArICdfZmllbGRfZ2VuZXJhdG9yX3ZhbHVlJyApLnZhbCggJycgKTtcclxuXHRcdH1cclxuXHR9XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogU2hvdyBzZWxlY3RlZCBBZGQgTmV3IEZpZWxkIGZvcm0sIGFuZCByZXNldCBmaWVsZHMgaW4gdGhpcyBmb3JtXHJcbiAqXHJcbiAqIEBwYXJhbSBzdHJpbmcgc2VsZWN0ZWRfZmllbGRfdmFsdWVcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc2hvd19maWVsZHNfZ2VuZXJhdG9yKCBzZWxlY3RlZF9maWVsZF92YWx1ZSApIHtcclxuXHRpZiAoXHJcblx0XHQoJ3RleHQnID09PSBzZWxlY3RlZF9maWVsZF92YWx1ZSkgfHxcclxuXHRcdCgndGV4dGFyZWEnID09PSBzZWxlY3RlZF9maWVsZF92YWx1ZSkgfHxcclxuXHRcdCgnc2VsZWN0JyA9PT0gc2VsZWN0ZWRfZmllbGRfdmFsdWUpIHx8XHJcblx0XHQoJ3NlbGVjdGJveCcgPT09IHNlbGVjdGVkX2ZpZWxkX3ZhbHVlKSB8fFxyXG5cdFx0KCdjaGVja2JveCcgPT09IHNlbGVjdGVkX2ZpZWxkX3ZhbHVlKVxyXG5cdCkge1xyXG5cdFx0aWYgKCdzZWxlY3QnID09PSBzZWxlY3RlZF9maWVsZF92YWx1ZSl7XHJcblx0XHRqUXVlcnkoICcjJyArICdzZWxlY3Rib3gnICsgJ19maWVsZF9nZW5lcmF0b3JfbmFtZScgKS5wcm9wKCAnZGlzYWJsZWQnLCBmYWxzZSApO1xyXG5cdFx0fVxyXG5cdFx0alF1ZXJ5KCAnIycgKyBzZWxlY3RlZF9maWVsZF92YWx1ZSArICdfZmllbGRfZ2VuZXJhdG9yX25hbWUnICkucHJvcCggJ2Rpc2FibGVkJywgZmFsc2UgKTtcclxuXHR9XHJcblxyXG5cdHdwYmNfcmVzZXRfYWxsX2Zvcm1zKCk7XHJcblxyXG5cdHZhciB0aW1lX2ZpZWxkc19hcnIgICAgICAgPSBbICdyYW5nZXRpbWUnLCAnZHVyYXRpb250aW1lJywgJ3N0YXJ0dGltZScsICdlbmR0aW1lJyBdO1xyXG5cdHZhciB0aW1lX2ZpZWxkc19hcnJfY291bnQgPSB0aW1lX2ZpZWxkc19hcnIubGVuZ3RoO1xyXG5cdGZvciAoIHZhciBpID0gMDsgaSA8IHRpbWVfZmllbGRzX2Fycl9jb3VudDsgaSsrICkge1xyXG5cdFx0Ly8gdGltZV9maWVsZHNfYXJyW2ldID0gJ3JhbmdldGltZScuXHJcblx0XHRpZiAoICdlZGl0XycgKyB0aW1lX2ZpZWxkc19hcnJbaV0gPT09IHNlbGVjdGVkX2ZpZWxkX3ZhbHVlICkge1xyXG5cdFx0XHQvLyBUaGlzIGZpZWxkIGFscmVhZHkgIGV4aXN0ICBpbiB0aGUgYm9va2luZyBmb3JtLCAgYW5kIHRoYXRzIHdoeSAgd2UgY2FuICBub3QgYWRkIGEgbmV3IGZpZWxkLCAgYW5kIGluc3RlYWQgb2YgdGhhdCAgZWRpdCBpdC5cclxuXHRcdFx0dmFyIHJhbmdlX3RpbWVfZWRpdF9maWVsZCA9IGpRdWVyeSggJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSA6aW5wdXRbdmFsdWU9XCInICsgdGltZV9maWVsZHNfYXJyW2ldICsgJ1wiXScgKTtcdC8vIHRpbWVfZmllbGRzX2FycltpXSA9ICdyYW5nZXRpbWUnLlxyXG5cdFx0XHR2YXIgcmFuZ2VfdGltZV9maWVsZF9udW0gID0gMDtcclxuXHJcblx0XHRcdGlmICggcmFuZ2VfdGltZV9lZGl0X2ZpZWxkLmxlbmd0aCA+IDAgKSB7XHJcblx0XHRcdFx0dmFyIHJhbmdlX3RpbWVfZWRpdF9maWVsZF9uYW1lID0galF1ZXJ5KCByYW5nZV90aW1lX2VkaXRfZmllbGQuZ2V0KCAwICkgKS5hdHRyKCAnbmFtZScgKTtcclxuXHRcdFx0XHRyYW5nZV90aW1lX2VkaXRfZmllbGRfbmFtZSAgICAgPSByYW5nZV90aW1lX2VkaXRfZmllbGRfbmFtZS5yZXBsYWNlQWxsKCAnZm9ybV9maWVsZF9uYW1lWycsICcnICkucmVwbGFjZUFsbCggJ10nLCAnJyApO1xyXG5cdFx0XHRcdHJhbmdlX3RpbWVfZmllbGRfbnVtICAgICAgICAgICA9IHBhcnNlSW50KCByYW5nZV90aW1lX2VkaXRfZmllbGRfbmFtZSApO1xyXG5cdFx0XHRcdGlmICggcmFuZ2VfdGltZV9maWVsZF9udW0gPiAwICkge1xyXG5cdFx0XHRcdFx0d3BiY19zdGFydF9lZGl0X2Zvcm1fZmllbGQoIHJhbmdlX3RpbWVfZmllbGRfbnVtICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggMCA9PT0gcmFuZ2VfdGltZV9maWVsZF9udW0gKSB7XHJcblx0XHRcdFx0c2VsZWN0ZWRfZmllbGRfdmFsdWUgPSB0aW1lX2ZpZWxkc19hcnJbaV07XHQvLyB0aW1lX2ZpZWxkc19hcnJbaV0gPSAncmFuZ2V0aW1lJy5cclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0fVxyXG5cclxuXHRpZiAoc2VsZWN0ZWRfZmllbGRfdmFsdWUgPT0gJ3NlbGVjdG9yX2hpbnQnKSB7XHJcblx0XHRqUXVlcnkoJy5tZXRhYm94X3dwYmNfZm9ybV9maWVsZF9mcmVlX2dlbmVyYXRvcicpLmhpZGUoKTtcclxuXHRcdGpRdWVyeSggJyN3cGJjX2Zvcm1fZmllbGRfZnJlZSBpbnB1dC53cGJjX3N1Ym1pdF9idXR0b25bdHlwZT1cInN1Ym1pdFwiXSxpbnB1dC53cGJjX3N1Ym1pdF9idXR0b25bdHlwZT1cImJ1dHRvblwiXScpLnNob3coKTtcdFx0XHRcdFx0XHQvLyBGaXhJbjogOC43LjExLjcuXHJcblx0XHRqUXVlcnkoICcjd3BiY19zZXR0aW5nc19fZm9ybV9maWVsZHNfX3Rvb2xiYXInKS5zaG93KCk7XHJcblx0fSBlbHNlIHtcclxuXHRcdGpRdWVyeSgnLm1ldGFib3hfd3BiY19mb3JtX2ZpZWxkX2ZyZWVfZ2VuZXJhdG9yJykuc2hvdygpO1xyXG5cdFx0alF1ZXJ5KCcud3BiY19maWVsZF9nZW5lcmF0b3InKS5oaWRlKCk7XHJcblx0XHRqUXVlcnkoJy53cGJjX2ZpZWxkX2dlbmVyYXRvcl8nICsgc2VsZWN0ZWRfZmllbGRfdmFsdWUgKS5zaG93KCk7XHJcblx0XHRqUXVlcnkoJyN3cGJjX2Zvcm1fZmllbGRfZnJlZV9nZW5lcmF0b3JfbWV0YWJveCBoMy5obmRsZSBzcGFuJykuaHRtbCggalF1ZXJ5KCcjc2VsZWN0X2Zvcm1faGVscF9zaG9ydGNvZGUgb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpICk7XHJcblx0XHRqUXVlcnkoJy53cGJjX2FkZF9maWVsZF9yb3cnKS5zaG93KCk7XHJcblx0XHRqUXVlcnkoICcjd3BiY19mb3JtX2ZpZWxkX2ZyZWUgaW5wdXQud3BiY19zdWJtaXRfYnV0dG9uW3R5cGU9XCJzdWJtaXRcIl0saW5wdXQud3BiY19zdWJtaXRfYnV0dG9uW3R5cGU9XCJidXR0b25cIl0nKS5oaWRlKCk7XHRcdFx0XHRcdFx0Ly8gRml4SW46IDguNy4xMS43LlxyXG5cdFx0alF1ZXJ5KCAnI3dwYmNfc2V0dGluZ3NfX2Zvcm1fZmllbGRzX190b29sYmFyJykuaGlkZSgpO1xyXG5cdH1cclxufVxyXG5cclxuXHJcbi8qKiBIaWRlIGFsbCBBZGQgTmV3IEZpZWxkIGZvcm1zLCBhbmQgcmVzZXQgZmllbGRzIGluIHRoZXNlIGZvcm1zKi9cclxuZnVuY3Rpb24gd3BiY19oaWRlX2ZpZWxkc19nZW5lcmF0b3JzKCkge1xyXG5cdHdwYmNfcmVzZXRfYWxsX2Zvcm1zKCk7XHJcblx0alF1ZXJ5KCcubWV0YWJveF93cGJjX2Zvcm1fZmllbGRfZnJlZV9nZW5lcmF0b3InKS5oaWRlKCk7XHJcblx0alF1ZXJ5KCcjc2VsZWN0X2Zvcm1faGVscF9zaG9ydGNvZGU+b3B0aW9uOmVxKDApJykuYXR0cignc2VsZWN0ZWQnLCB0cnVlKTtcclxuXHJcblx0alF1ZXJ5KCAnI3dwYmNfZm9ybV9maWVsZF9mcmVlIGlucHV0LndwYmNfc3VibWl0X2J1dHRvblt0eXBlPVwic3VibWl0XCJdLGlucHV0LndwYmNfc3VibWl0X2J1dHRvblt0eXBlPVwiYnV0dG9uXCJdJykuc2hvdygpO1x0XHRcdFx0XHRcdC8vIEZpeEluOiA4LjcuMTEuNy5cclxuXHRqUXVlcnkoICcjd3BiY19zZXR0aW5nc19fZm9ybV9maWVsZHNfX3Rvb2xiYXInKS5zaG93KCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogQWRkIE5ldyBSb3cgd2l0aCBuZXcgRmllbGQgdG8gVGFibGUgYW5kIFN1Ym1pdCBTYXZpbmcgY2hhbmdlcy5cclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IGZpZWxkX25hbWVcclxuICogQHBhcmFtIHtzdHJpbmd9IGZpZWxkX3R5cGVcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWRkX2ZpZWxkICggZmllbGRfbmFtZSwgZmllbGRfdHlwZSApIHtcclxuXHJcblx0Ly8gRml4SW46IFRpbWVGcmVlR2VuZXJhdG9yLlxyXG5cdHZhciB0aW1lX2ZpZWxkc19hcnIgPSBbICdyYW5nZXRpbWUnIF07IC8vLCAnZHVyYXRpb250aW1lJywgJ3N0YXJ0dGltZScsICdlbmR0aW1lJyBdOyAuXHJcblx0dmFyIHRpbWVfZmllbGRzX2Fycl9jb3VudCA9IHRpbWVfZmllbGRzX2Fyci5sZW5ndGg7XHJcblx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGltZV9maWVsZHNfYXJyX2NvdW50OyBpKysgKSB7XHJcblx0XHQvLyB0aW1lX2ZpZWxkc19hcnJbaV0gPSAncmFuZ2V0aW1lJy5cclxuXHRcdGlmICggdGltZV9maWVsZHNfYXJyW2ldICsgJ19maWVsZF9nZW5lcmF0b3InID09PSBmaWVsZF9uYW1lICkge1xyXG5cdFx0XHR2YXIgcmVwbGFjZWRfcmVzdWx0ID0gd3BiY19nZXRfc2F2ZWRfdmFsdWVfZnJvbV90aW1lc2xvdHNfdGFibGUoKTtcclxuXHRcdFx0aWYgKCBmYWxzZSA9PT0gcmVwbGFjZWRfcmVzdWx0ICl7XHJcblx0XHRcdFx0d3BiY19oaWRlX2ZpZWxkc19nZW5lcmF0b3JzKCk7XHJcblx0XHRcdFx0Ly9UT086IFNob3cgd2FybmluZyBhdCAgdGhlIHRvcCBvZiBwYWdlLCAgYWJvdXQgZXJyb3IgZHVyaW5nIHNhdmluZyB0aW1lc2xvdHNcclxuXHRcdFx0XHRjb25zb2xlLmxvZyggJ2Vycm9yIGR1cmluZyBwYXJzaW5nIHRpbWVzbG90cyB0YmFsZSBhbmQgc2F2aWcgaXQuJyApXHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHRpZiAoIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJykudmFsKCkgIT0gJycgKSB7XHJcblxyXG5cdFx0d3BiY19jaGVja190eXBlZF9uYW1lKCBmaWVsZF9uYW1lICk7XHJcblxyXG5cdFx0dmFyIHJvd19udW0gPSBqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0Ym9keSB0cicpLmxlbmd0aCArIE1hdGgucm91bmQoIG5ldyBEYXRlKCkuZ2V0VGltZSgpICApIDtcclxuXHJcblx0XHR2YXIgcm93X2FjdGl2ZSA9ICdPZmYnO1xyXG5cdFx0dmFyIHJvd19hY3RpdmVfY2hlY2tlZCA9ICcnO1xyXG5cdFx0aWYgKCBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfYWN0aXZlJykuaXMoIFwiOmNoZWNrZWRcIiApICkge1xyXG5cdFx0XHRyb3dfYWN0aXZlID0gJ09uJztcclxuXHRcdFx0cm93X2FjdGl2ZV9jaGVja2VkID0gJyBjaGVja2VkPVwiY2hlY2tlZFwiICc7XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIHJvd19yZXF1aXJlZCA9ICdPZmYnO1xyXG5cdFx0dmFyIHJvd19yZXF1aXJlZF9jaGVja2VkID0gJyc7XHJcblx0XHRpZiAoIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19yZXF1aXJlZCcpLmlzKCBcIjpjaGVja2VkXCIgKSApIHtcclxuXHRcdFx0cm93X3JlcXVpcmVkID0gJ09uJztcclxuXHRcdFx0cm93X3JlcXVpcmVkX2NoZWNrZWQgPSAnIGNoZWNrZWQ9XCJjaGVja2VkXCIgJztcclxuXHRcdH1cclxuXHJcblxyXG5cdFx0dmFyIHJvdztcclxuXHRcdHJvdyA9ICc8dHIgY2xhc3M9XCJhY2NvdW50IHVpLXNvcnRhYmxlLWhhbmRsZVwiPic7XHJcblxyXG5cdFx0Ly8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHRyb3cgKz0gJzx0ZCBjbGFzcz1cInNvcnRcIiBzdHlsZT1cImN1cnNvcjogbW92ZTtcIj48c3BhbiBjbGFzcz1cIndwYmNfaWNuX2RyYWdfaW5kaWNhdG9yXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCI+PC9zcGFuPjwvdGQ+JztcclxuXHJcblx0XHRyb3cgKz0gJzx0ZCBjbGFzcz1cImZpZWxkX2FjdGl2ZVwiPic7XHJcblx0XHRyb3cgKz0gICAgICAnPGlucHV0IHR5cGU9XCJjaGVja2JveFwiIG5hbWU9XCJmb3JtX2ZpZWxkX2FjdGl2ZVsnKyByb3dfbnVtICsnXVwiIHZhbHVlPVwiJyArIHJvd19hY3RpdmUgKyAnXCIgJyArIHJvd19hY3RpdmVfY2hlY2tlZCArICcgYXV0b2NvbXBsZXRlPVwib2ZmXCIgLz4nO1xyXG5cdFx0cm93ICs9ICc8L3RkPic7XHJcblxyXG5cdFx0Ly8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHRyb3cgKz0gJzx0ZCBjbGFzcz1cImZpZWxkX2xhYmVsXCI+JztcclxuXHJcblx0XHQvL3JvdyArPSAgICAgICc8bGVnZW5kIGNsYXNzPVwic2NyZWVuLXJlYWRlci10ZXh0XCI+PHNwYW4+JyArIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19sYWJlbCcpLnZhbCgpICsgJzwvc3Bhbj48L2xlZ2VuZD4nO1xyXG5cclxuXHRcdHJvdyArPSAgICAgICc8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiZm9ybV9maWVsZF9sYWJlbFsnKyByb3dfbnVtICsnXVwiIHZhbHVlPVwiJ1xyXG5cdFx0XHRcdFx0XHRcdCsgalF1ZXJ5KCcjJyArIGZpZWxkX25hbWUgKyAnX2xhYmVsJykudmFsKCkgKyAnXCIgcGxhY2Vob2xkZXI9XCInXHJcblx0XHRcdFx0XHRcdFx0KyBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfbGFiZWwnKS52YWwoKSArICdcIiBjbGFzcz1cInJlZ3VsYXItdGV4dFwiIGF1dG9jb21wbGV0ZT1cIm9mZlwiIC8+JztcclxuXHJcblx0XHRyb3cgKz0gICAgICAgIFx0XHQnPGRpdiBjbGFzcz1cImZpZWxkX3R5cGVfbmFtZV9kZXNjcmlwdGlvblwiPic7XHJcblx0XHQvL3JvdyArPSAgICAgICAgXHRcdFx0Jzw/cGhwIGVjaG8gZXNjX2pzKCBfXyggJ1R5cGUnLCAnYm9va2luZycgKSApOyA/PjogPGRpdiBjbGFzcz1cImZpZWxkX3R5cGVfbmFtZV92YWx1ZVwiPicgK2ZpZWxkX3R5cGUrICc8L2Rpdj4nO1xyXG5cdFx0cm93ICs9ICAgICAgICBcdFx0XHQnVHlwZTogPGRpdiBjbGFzcz1cImZpZWxkX3R5cGVfbmFtZV92YWx1ZVwiPicgK2ZpZWxkX3R5cGUrICc8L2Rpdj4nO1xyXG5cdFx0cm93ICs9ICAgICAgICBcdFx0XHQnPHNwYW4gY2xhc3M9XCJmaWVsZF90eXBlX25hbWVfc2VwYXJhdG9yXCI+fDwvc3Bhbj4nO1xyXG5cdFx0Ly9yb3cgKz0gICAgICAgIFx0XHRcdCc8P3BocCBlY2hvIGVzY19qcyggX18oICdOYW1lJywgJ2Jvb2tpbmcnICkgKTsgPz46IDxkaXYgY2xhc3M9XCJmaWVsZF90eXBlX25hbWVfdmFsdWVcIj4nICsgalF1ZXJ5KCcjJyArIGZpZWxkX25hbWUgKyAnX25hbWUnKS52YWwoKSArICc8L2Rpdj4nO1xyXG5cdFx0cm93ICs9ICAgICAgICBcdFx0XHQnTmFtZTogPGRpdiBjbGFzcz1cImZpZWxkX3R5cGVfbmFtZV92YWx1ZVwiPicgKyBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfbmFtZScpLnZhbCgpICsgJzwvZGl2Pic7XHJcblx0XHRyb3cgKz0gICAgICAgIFx0XHQnPC9kaXY+JztcclxuXHJcblx0XHRyb3cgKz0gICAgICAgICc8aW5wdXQgdHlwZT1cImhpZGRlblwiIHZhbHVlPVwiJyArICggKCAnc2VsZWN0JyA9PSBmaWVsZF90eXBlICkgPyAnc2VsZWN0Ym94JyA6IGZpZWxkX3R5cGUgKSAgKyAgJ1wiICBuYW1lPVwiZm9ybV9maWVsZF90eXBlWycgKyByb3dfbnVtICsgJ11cIiBhdXRvY29tcGxldGU9XCJvZmZcIiAvPic7XHJcblx0XHRyb3cgKz0gICAgICAgICc8aW5wdXQgdHlwZT1cImhpZGRlblwiIHZhbHVlPVwiJyArIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJykudmFsKCkgKyAnXCIgIG5hbWU9XCJmb3JtX2ZpZWxkX25hbWVbJyArIHJvd19udW0gKyAnXVwiIGF1dG9jb21wbGV0ZT1cIm9mZlwiIC8+JztcclxuXHRcdHJvdyArPSAgICAgICAgJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgdmFsdWU9XCInICsgKChqUXVlcnkoICcjJyArIGZpZWxkX25hbWUgKyAnX3ZhbHVlJyApLmxlbmd0aCkgPyBqUXVlcnkoICcjJyArIGZpZWxkX25hbWUgKyAnX3ZhbHVlJyApLnZhbCgpIDogJycpICsgJ1wiICBuYW1lPVwiZm9ybV9maWVsZF92YWx1ZVsnICsgcm93X251bSArICddXCIgYXV0b2NvbXBsZXRlPVwib2ZmXCIgLz4nO1xyXG5cclxuXHRcdHJvdyArPSAnPC90ZD4nO1xyXG5cclxuXHRcdC8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vL1xyXG5cdFx0cm93ICs9ICc8dGQgY2xhc3M9XCJmaWVsZF9yZXF1aXJlZFwiPic7XHJcblxyXG5cdFx0Ly8gRml4SW46ICBUaW1lRnJlZUdlbmVyYXRvci5cclxuXHRcdGlmIChcclxuXHRcdFx0KCdyYW5nZXRpbWUnID09PSBmaWVsZF9uYW1lKSB8fFxyXG5cdFx0XHQoJ2R1cmF0aW9udGltZScgPT09IGZpZWxkX25hbWUpIHx8XHJcblx0XHRcdCgnc3RhcnR0aW1lJyA9PT0gZmllbGRfbmFtZSkgfHxcclxuXHRcdFx0KCdlbmR0aW1lJyA9PT0gZmllbGRfbmFtZSlcclxuXHRcdCkge1xyXG5cdFx0XHRyb3cgKz0gJzxpbnB1dCB0eXBlPVwiY2hlY2tib3hcIiBkaXNhYmxlZD1cIkRJU0FCTEVEXCIgbmFtZT1cImZvcm1fZmllbGRfcmVxdWlyZWRbJyArIHJvd19udW0gKyAnXVwiIHZhbHVlPVwiT25cIiAgY2hlY2tlZD1cImNoZWNrZWRcIiAgYXV0b2NvbXBsZXRlPVwib2ZmXCIgLz4nO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0cm93ICs9ICc8aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgbmFtZT1cImZvcm1fZmllbGRfcmVxdWlyZWRbJyArIHJvd19udW0gKyAnXVwiIHZhbHVlPVwiJyArIHJvd19yZXF1aXJlZCArICdcIiAnICsgcm93X3JlcXVpcmVkX2NoZWNrZWQgKyAnIGF1dG9jb21wbGV0ZT1cIm9mZlwiIC8+JztcclxuXHRcdH1cclxuXHJcblx0XHRyb3cgKz0gJzwvdGQ+JztcclxuXHJcblx0XHQvLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cclxuXHRcdC8vIHJvdyArPSAnPHRkIGNsYXNzPVwiZmllbGRfb3B0aW9uc1wiPic7XHJcblx0XHQvLyByb3cgKz0gICAgICAgICc8aW5wdXQgdHlwZT1cInRleHRcIiBkaXNhYmxlZD1cIkRJU0FCTEVEXCIgdmFsdWU9XCInICsgZmllbGRfdHlwZSArICcgfCAnICsgalF1ZXJ5KCcjJyArIGZpZWxkX25hbWUgKyAnX25hbWUnKS52YWwoKSArICdcIiAgYXV0b2NvbXBsZXRlPVwib2ZmXCIgLz4nO1xyXG5cdFx0Ly8gcm93ICs9ICc8L3RkPic7XHJcblxyXG5cdFx0Ly8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHRyb3cgKz0gJzx0ZCBjbGFzcz1cImZpZWxkX29wdGlvbnNcIj4nO1xyXG5cclxuXHRcdC8vcm93ICs9ICAgICAgJzxhIGhyZWY9XCJqYXZhc2NyaXB0OnZvaWQoMClcIiBjbGFzcz1cInRvb2x0aXBfdG9wIGJ1dHRvbi1zZWNvbmRhcnkgYnV0dG9uXCIgdGl0bGU9XCI8P3BocCBlY2hvIGVzY19qcyggX18oJ0VkaXQnICwnYm9va2luZycpICkgOyA/PlwiPjxpIGNsYXNzPVwid3BiY19pY25fZHJhd1wiPjwvaT48L2E+JztcclxuXHRcdC8vcm93ICs9ICAgICAgJzxhIGhyZWY9XCJqYXZhc2NyaXB0OnZvaWQoMClcIiBjbGFzcz1cInRvb2x0aXBfdG9wIGJ1dHRvbi1zZWNvbmRhcnkgYnV0dG9uIGRlbGV0ZV9ia19saW5rXCIgdGl0bGU9XCI8P3BocCBlY2hvIGVzY19qcyggX18oJ1JlbW92ZScgLCdib29raW5nJykgKSA7ID8+XCI+PGkgY2xhc3M9XCJ3cGJjX2ljbl9jbG9zZVwiPjwvaT48L2E+JztcclxuXHJcblx0XHRyb3cgKz0gJzwvdGQ+JztcclxuXHRcdC8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vL1xyXG5cdFx0cm93ICs9ICc8L3RyPic7XHJcblxyXG5cdFx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdGJvZHknKS5hcHBlbmQoIHJvdyApO1xyXG5cclxuXHRcdHdwYmNfaGlkZV9maWVsZHNfZ2VuZXJhdG9ycygpO1xyXG5cclxuXHRcdGRvY3VtZW50LmZvcm1zWyd3cGJjX2Zvcm1fZmllbGRfZnJlZSddLnN1Ym1pdCgpOyAgICAgICAgICAgIC8vU3VibWl0IGZvcm1cclxuXHJcblx0fSBlbHNlIHtcclxuXHRcdHdwYmNfZmllbGRfaGlnaGxpZ2h0KCAnIycgKyBmaWVsZF9uYW1lICsgJ19uYW1lJyApO1xyXG5cdH1cclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBQcmVwYXJlIEVkaXQgc2VjdGlvbiBmb3IgZWRpdGluZyBzcGVjaWZpYyBmaWVsZC5cclxuICogQHBhcmFtIHJvd19udW1iZXJcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfc3RhcnRfZWRpdF9mb3JtX2ZpZWxkKCByb3dfbnVtYmVyICkge1xyXG5cclxuXHR3cGJjX3Jlc2V0X2FsbF9mb3JtcygpO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFJlc2V0IEZpZWxkcyBpbiBhbGwgZ2VuZXJhdG9yIHJvd3MgKHRleHQsc2VsZWN0LC4uLikgdG8gaW5pdCAoZW1wdHkpIHZhbHVlc1xyXG5cdGpRdWVyeSgnLndwYmNfZWRpdF9maWVsZF9yb3cnKS5zaG93KCk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFNob3cgcm93IHdpdGggZWRpdCBidG5cclxuXHJcblx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdHInKS5yZW1vdmVDbGFzcygnaGlnaGxpZ2h0Jyk7XHJcblx0alF1ZXJ5KCdpbnB1dFtuYW1lPVwiZm9ybV9maWVsZF9uYW1lWycrcm93X251bWJlcisnXVwiXScpLmNsb3Nlc3QoJ3RyJykuYWRkQ2xhc3MoJ2hpZ2hsaWdodCcpO1x0XHRcdC8vSGlnaGxpZ2h0IHJvd1xyXG5cclxuXHQvLyBHZXQgZXhpc3QgZGF0YSBmcm9tIEVYSVNUIGZpZWxkcyBUYWJsZVxyXG5cdHZhciBmaWVsZF9hY3RpdmUgPSBqUXVlcnkoJ2lucHV0W25hbWU9XCJmb3JtX2ZpZWxkX2FjdGl2ZVsnK3Jvd19udW1iZXIrJ11cIl0nKS5pcyggXCI6Y2hlY2tlZFwiICk7XHJcblx0dmFyIGZpZWxkX3JlcXVpcmVkID0galF1ZXJ5KCdpbnB1dFtuYW1lPVwiZm9ybV9maWVsZF9yZXF1aXJlZFsnK3Jvd19udW1iZXIrJ11cIl0nKS5pcyggXCI6Y2hlY2tlZFwiICk7XHJcblx0dmFyIGZpZWxkX2xhYmVsID0galF1ZXJ5KCdpbnB1dFtuYW1lPVwiZm9ybV9maWVsZF9sYWJlbFsnK3Jvd19udW1iZXIrJ11cIl0nKS52YWwoKTtcclxuXHR2YXIgZmllbGRfdmFsdWUgPSBqUXVlcnkoJ2lucHV0W25hbWU9XCJmb3JtX2ZpZWxkX3ZhbHVlWycrcm93X251bWJlcisnXVwiXScpLnZhbCgpO1xyXG5cdHZhciBmaWVsZF9uYW1lID0galF1ZXJ5KCdpbnB1dFtuYW1lPVwiZm9ybV9maWVsZF9uYW1lWycrcm93X251bWJlcisnXVwiXScpLnZhbCgpO1xyXG5cdHZhciBmaWVsZF90eXBlID0galF1ZXJ5KCdpbnB1dFtuYW1lPVwiZm9ybV9maWVsZF90eXBlWycrcm93X251bWJlcisnXVwiXScpLnZhbCgpO1xyXG4vL2NvbnNvbGUubG9nKCAnZmllbGRfYWN0aXZlLCBmaWVsZF9yZXF1aXJlZCwgZmllbGRfbGFiZWwsIGZpZWxkX3ZhbHVlLCBmaWVsZF9uYW1lLCBmaWVsZF90eXBlJywgZmllbGRfYWN0aXZlLCBmaWVsZF9yZXF1aXJlZCwgZmllbGRfbGFiZWwsIGZpZWxkX3ZhbHVlLCBmaWVsZF9uYW1lLCBmaWVsZF90eXBlICk7XHJcblxyXG5cdGpRdWVyeSgnLm1ldGFib3hfd3BiY19mb3JtX2ZpZWxkX2ZyZWVfZ2VuZXJhdG9yJykuc2hvdygpO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFNob3cgR2VuZXJhdG9yIHNlY3Rpb25cclxuXHRqUXVlcnkoJy53cGJjX2ZpZWxkX2dlbmVyYXRvcicpLmhpZGUoKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gSGlkZSBpbnNpZGUgb2YgZ2VuZXJhdG9yIHN1YiBzZWN0aW9uICByZWxhdGl2ZSB0byBmaWVsZHMgdHlwZXNcclxuXHJcblxyXG5cdC8vIEZpeEluOiBUaW1lRnJlZUdlbmVyYXRvclx0LSBFeGNlcHRpb24gLSBmaWVsZCB3aXRoICBuYW1lICdyYW5nZXRpbWUsIGhhdmUgdHlwZSAncmFuZ2V0eXBlJyBpbiBHZW5lcmF0b3IgQlVULCBpdCBoYXZlIHRvICBiZSBzYXZlZCBhcyAnc2VsZWN0JyB0eXBlJy5cclxuXHRpZiAoICdyYW5nZXRpbWUnID09IGZpZWxkX25hbWUgKSB7XHJcblx0XHQvKipcclxuXHRcdCAqICBGaWVsZCAncmFuZ2V0aW1lX2ZpZWxkX2dlbmVyYXRvcicgaGF2ZSBESVYgc2VjdGlvbiwgd2hpY2ggaGF2ZSBDU1MgY2xhc3MgJ3dwYmNfZmllbGRfZ2VuZXJhdG9yX3JhbmdldGltZScsXHJcblx0XHQgKiAgYnV0IGl0cyBhbHNvICBkZWZpbmVkIHdpdGggIHR5cGUgJ3NlbGVjdCcgIGZvciBhZGRpbmcgdGhpcyBmaWVsZCB2aWEgICAgamF2YXNjcmlwdDp3cGJjX2FkZF9maWVsZCAoICdyYW5nZXRpbWVfZmllbGRfZ2VuZXJhdG9yJywgJ3NlbGVjdCcgKTtcclxuXHRcdCAqL1xyXG5cclxuXHRcdGZpZWxkX3R5cGUgPSAncmFuZ2V0aW1lJztcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIER1cmluZyBlZGl0aW5nICdmaWVsZF9yZXF1aXJlZCcgPT0gZmFsc2UsICBiZWNhdXNlIHRoaXMgZmllbGQgZG9lcyBub3QgZXhpc3QgIGluIHRoZSBUYWJsZSB3aXRoIGV4aXN0IGZpZWxkcywgIGJ1dCB3ZSBuZWVkIHRvICBzZXQgaXQgdG8gIHRydWUgYW5kIGRpc2FibGVkLlxyXG5cdFx0ICovXHJcblx0fVxyXG5cclxuXHRqUXVlcnkoICcud3BiY19maWVsZF9nZW5lcmF0b3JfJyArIGZpZWxkX3R5cGUgKS5zaG93KCk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgICAgICAgICAgICAvLyBTaG93IHNwZWNpZmljIGdlbmVyYXRvciBzdWIgc2VjdGlvbiAgcmVsYXRpdmUgdG8gc2VsZWN0ZWQgRmllbGQgVHlwZS5cclxuXHRqUXVlcnkoICcjd3BiY19mb3JtX2ZpZWxkX2ZyZWVfZ2VuZXJhdG9yX21ldGFib3ggaDMuaG5kbGUgc3BhbicgKS5odG1sKCAnRWRpdDogJyArIGZpZWxkX25hbWUgKTtcclxuXHJcblxyXG5cdGpRdWVyeSggJyMnICsgZmllbGRfdHlwZSArICdfZmllbGRfZ2VuZXJhdG9yX2FjdGl2ZScgKS5wcm9wKCAnY2hlY2tlZCcsIGZpZWxkX2FjdGl2ZSApO1xyXG5cdGpRdWVyeSggJyMnICsgZmllbGRfdHlwZSArICdfZmllbGRfZ2VuZXJhdG9yX3JlcXVpcmVkJyApLnByb3AoICdjaGVja2VkJywgZmllbGRfcmVxdWlyZWQgKTtcclxuXHRqUXVlcnkoICcjJyArIGZpZWxkX3R5cGUgKyAnX2ZpZWxkX2dlbmVyYXRvcl9sYWJlbCcgKS52YWwoIGZpZWxkX2xhYmVsICk7XHJcblx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfbmFtZScgKS52YWwoIGZpZWxkX25hbWUgKTtcclxuXHRqUXVlcnkoICcjJyArIGZpZWxkX3R5cGUgKyAnX2ZpZWxkX2dlbmVyYXRvcl92YWx1ZScgKS52YWwoIGZpZWxkX3ZhbHVlICk7XHJcblx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfbmFtZScgKS5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlICk7XHJcblxyXG5cdGpRdWVyeSggJy5maWVsZF9vcHRpb25zX2Zvcm1hdCcpLnJlbW92ZSgpO1xyXG5cclxuXHQvLyBGaXhJbjogVGltZUZyZWVHZW5lcmF0b3IuXHJcblx0aWYgKCAoJ3JhbmdldGltZScgPT09IGZpZWxkX25hbWUpIHx8XHJcblx0XHQoJ2R1cmF0aW9udGltZScgPT09IGZpZWxkX25hbWUpIHx8XHJcblx0XHQoJ3N0YXJ0dGltZScgPT09IGZpZWxkX25hbWUpIHx8XHJcblx0XHQoJ2VuZHRpbWUnID09PSBmaWVsZF9uYW1lKVxyXG5cdCkge1xyXG5cdFx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfcmVxdWlyZWQnICkucHJvcCggJ2NoZWNrZWQnLCB0cnVlICkucHJvcCggJ2Rpc2FibGVkJywgdHJ1ZSApO1x0XHQvLyBTZXQgRGlzYWJsZWQgYW5kIENoZWNrZWQgLS0gUmVxdWlyZWQgZmllbGQuXHJcblx0XHRpZiAoICdkdXJhdGlvbnRpbWUnID09PSBmaWVsZF9uYW1lICkge1xyXG5cdFx0XHRqUXVlcnkoICcjJyArIGZpZWxkX3R5cGUgKyAnX2ZpZWxkX2dlbmVyYXRvcl92YWx1ZScgKS5hZnRlcihcclxuXHRcdFx0XHQnPGRpdiBjbGFzcz1cIndwYmMtc2V0dGluZ3Mtbm90aWNlIG5vdGljZS1pbmZvIG5vdGljZS1oZWxwZnVsLWluZm8gZmllbGRfb3B0aW9uc19mb3JtYXRcIiBzdHlsZT1cIm1hcmdpbjogMzBweCAwO1wiPicrXHJcblx0XHRcdFx0J1RoZSBvcHRpb24gZm9ybWF0IGlzIFwiVGl0bGVAQFRpbWUgRHVyYXRpb24sXCIgd2hlcmUgXCJUaXRsZVwiIGlzIHVzdWFsbHkgdGhlIHNlcnZpY2UgbmFtZSBhbmQgXCJUaW1lIER1cmF0aW9uXCIgaXMgZGVmaW5lZCBpbiB0aGUgZm9ybWF0IEhIOk1NIChISCA9IGhvdXJzIGZyb20gMDAgdG8gMjMsIE1NID0gbWludXRlcyBmcm9tIDAwIHRvIDU5KS4nXHJcblx0XHRcdFx0KyAnPC9kaXY+J1xyXG5cdFx0XHQpO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCAnc3RhcnR0aW1lJyA9PT0gZmllbGRfbmFtZSApIHtcclxuXHRcdFx0alF1ZXJ5KCAnIycgKyBmaWVsZF90eXBlICsgJ19maWVsZF9nZW5lcmF0b3JfdmFsdWUnICkuYWZ0ZXIoXHJcblx0XHRcdFx0JzxkaXYgY2xhc3M9XCJ3cGJjLXNldHRpbmdzLW5vdGljZSBub3RpY2UtaW5mbyBub3RpY2UtaGVscGZ1bC1pbmZvIGZpZWxkX29wdGlvbnNfZm9ybWF0XCIgc3R5bGU9XCJtYXJnaW46IDMwcHggMDtcIj4nK1xyXG5cdFx0XHRcdCdUaGUgb3B0aW9uIGZvcm1hdCBpcyBcIlRpdGxlQEBUaW1lLFwiIHdoZXJlIFwiVGl0bGVcIiBpcyBhbnkgdGV4dCAodHlwaWNhbGx5IHRoZSB0aW1lIGluIEFNL1BNIGZvcm1hdCkgYW5kIFwiVGltZVwiIGlzIGRlZmluZWQgaW4gdGhlIDI0LWhvdXIgZm9ybWF0IChISDpNTSksIHdoZXJlIEhIID0gaG91cnMgKDAwIHRvIDIzKSBhbmQgTU0gPSBtaW51dGVzICgwMCB0byA1OSkuJ1xyXG5cdFx0XHRcdCsgJzwvZGl2PidcclxuXHRcdFx0KTtcclxuXHRcdH1cclxuXHR9XHJcblx0aWYgKCAncmFuZ2V0aW1lJyA9PT0gZmllbGRfbmFtZSApIHtcclxuXHRcdHdwYmNfY2hlY2tfdHlwZWRfdmFsdWVzKCBmaWVsZF9uYW1lICsgJ19maWVsZF9nZW5lcmF0b3InICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFVwZGF0ZSBPcHRpb25zIGFuZCBUaXRsZXMgZm9yIFRpbWVTbG90cy5cclxuXHRcdHdwYmNfdGltZXNsb3RzX3RhYmxlX19maWxsX3Jvd3MoKTtcclxuXHR9XHJcblxyXG5cdGpRdWVyeSggJyN3cGJjX2Zvcm1fZmllbGRfZnJlZSBpbnB1dC53cGJjX3N1Ym1pdF9idXR0b25bdHlwZT1cInN1Ym1pdFwiXSxpbnB1dC53cGJjX3N1Ym1pdF9idXR0b25bdHlwZT1cImJ1dHRvblwiXScgKS5oaWRlKCk7XHRcdFx0XHRcdFx0Ly8gRml4SW46IDguNy4xMS43LlxyXG5cdGpRdWVyeSggJyN3cGJjX3NldHRpbmdzX19mb3JtX2ZpZWxkc19fdG9vbGJhcicgKS5oaWRlKCk7XHJcblxyXG5cdHdwYmNfc2Nyb2xsX3RvKCAnI3dwYmNfZm9ybV9maWVsZF9mcmVlX2dlbmVyYXRvcl9tZXRhYm94JyApO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFByZXBhcmUgZmllbGRzIGRhdGEsIGFuZCBzdWJtaXQgRWRpdGVkIGZpZWxkIGJ5IGNsaWNraW5nIFwiU2F2ZSBjaGFuZ2VzXCIgYnRuLlxyXG4gKlxyXG4gKiBAcGFyYW0gZmllbGRfbmFtZVxyXG4gKiBAcGFyYW0gZmllbGRfdHlwZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19maW5pc2hfZWRpdF9mb3JtX2ZpZWxkKCBmaWVsZF9uYW1lLCBmaWVsZF90eXBlICkge1xyXG5cclxuXHQvLyBGaXhJbjogVGltZUZyZWVHZW5lcmF0b3IuXHJcblx0dmFyIHRpbWVfZmllbGRzX2FyciAgICAgICA9IFsgJ3JhbmdldGltZScsIF07IC8vICdkdXJhdGlvbnRpbWUnLCAnc3RhcnR0aW1lJywgJ2VuZHRpbWUnIF07XHJcblx0dmFyIHRpbWVfZmllbGRzX2Fycl9jb3VudCA9IHRpbWVfZmllbGRzX2Fyci5sZW5ndGg7XHJcblx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGltZV9maWVsZHNfYXJyX2NvdW50OyBpKysgKSB7XHJcblx0XHQvLyB0aW1lX2ZpZWxkc19hcnJbaV0gPSAncmFuZ2V0aW1lJy5cclxuXHRcdGlmICggdGltZV9maWVsZHNfYXJyW2ldICsgJ19maWVsZF9nZW5lcmF0b3InID09PSBmaWVsZF9uYW1lICkge1xyXG5cdFx0XHR2YXIgcmVwbGFjZWRfcmVzdWx0ID0gd3BiY19nZXRfc2F2ZWRfdmFsdWVfZnJvbV90aW1lc2xvdHNfdGFibGUoKTtcclxuXHRcdFx0aWYgKCBmYWxzZSA9PT0gcmVwbGFjZWRfcmVzdWx0ICkge1xyXG5cdFx0XHRcdHdwYmNfaGlkZV9maWVsZHNfZ2VuZXJhdG9ycygpO1xyXG5cdFx0XHRcdC8vIFRPRE86IFNob3cgd2FybmluZyBhdCAgdGhlIHRvcCBvZiBwYWdlLCAgYWJvdXQgZXJyb3IgZHVyaW5nIHNhdmluZyB0aW1lc2xvdHMuXHJcblx0XHRcdFx0Y29uc29sZS5sb2coICdlcnJvciBkdXJpbmcgcGFyc2luZyB0aW1lc2xvdHMgdGFibGUgYW5kIHNhdmlnIGl0LicgKVxyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxuXHJcblxyXG5cdC8vIEdldCBWYWx1ZXMgaW4gIEVkaXQgRm9ybSAvLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cclxuXHJcblx0Ly8wOiB2YXIgZmllbGRfdHlwZVxyXG5cdC8vMTpcclxuXHR2YXIgcm93X2FjdGl2ZSA9ICdPZmYnO1xyXG5cdHZhciByb3dfYWN0aXZlX2NoZWNrZWQgPSBmYWxzZTtcclxuXHRpZiAoIGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19hY3RpdmUnKS5pcyggXCI6Y2hlY2tlZFwiICkgKSB7XHJcblx0XHRyb3dfYWN0aXZlID0gJ09uJztcclxuXHRcdHJvd19hY3RpdmVfY2hlY2tlZCA9IHRydWU7XHJcblx0fVxyXG5cdC8vMjpcclxuXHR2YXIgcm93X3JlcXVpcmVkID0gJ09mZic7XHJcblx0dmFyIHJvd19yZXF1aXJlZF9jaGVja2VkID0gZmFsc2U7XHJcblx0aWYgKCBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfcmVxdWlyZWQnKS5pcyggXCI6Y2hlY2tlZFwiICkgKSB7XHJcblx0XHRyb3dfcmVxdWlyZWQgPSAnT24nO1xyXG5cdFx0cm93X3JlcXVpcmVkX2NoZWNrZWQgPSB0cnVlO1xyXG5cdH1cclxuXHQvLzM6XHJcblx0dmFyIHJvd19sYWJlbCA9IGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19sYWJlbCcpLnZhbCgpO1xyXG5cdC8vNDpcclxuXHR2YXIgcm93X25hbWUgPSBqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfbmFtZScpLnZhbCgpO1xyXG5cdC8vNTpcclxuXHR2YXIgcm93X3ZhbHVlID0galF1ZXJ5KCcjJyArIGZpZWxkX25hbWUgKyAnX3ZhbHVlJykudmFsKCk7XHJcblxyXG5cdC8vIFNldCAgdmFsdWVzIHRvICB0aGUgUk9XIGluIEZpZWxkcyBUYWJsZSAvLy8vLy8vLy8vLy8vLy8vLy8vLy9cclxuXHQvLzE6XHJcblx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdHIuaGlnaGxpZ2h0IGlucHV0W25hbWVePWZvcm1fZmllbGRfYWN0aXZlXScpLnByb3AoICdjaGVja2VkJywgcm93X2FjdGl2ZV9jaGVja2VkICk7XHJcblx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdHIuaGlnaGxpZ2h0IGlucHV0W25hbWVePWZvcm1fZmllbGRfYWN0aXZlXScpLnZhbCggcm93X2FjdGl2ZSApO1xyXG5cdC8vMjpcclxuXHRqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0ci5oaWdobGlnaHQgaW5wdXRbbmFtZV49Zm9ybV9maWVsZF9yZXF1aXJlZF0nKS5wcm9wKCAnY2hlY2tlZCcsIHJvd19yZXF1aXJlZF9jaGVja2VkICk7XHJcblx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdHIuaGlnaGxpZ2h0IGlucHV0W25hbWVePWZvcm1fZmllbGRfcmVxdWlyZWRdJykudmFsKCByb3dfcmVxdWlyZWQgKTtcclxuXHQvLzM6XHJcblx0alF1ZXJ5KCcud3BiY190YWJsZV9mb3JtX2ZyZWUgdHIuaGlnaGxpZ2h0IGlucHV0W25hbWVePWZvcm1fZmllbGRfbGFiZWxdJykudmFsKCByb3dfbGFiZWwgKTtcclxuLy8gICAgICAgICAgICAgICAgLy80OlxyXG4vLyAgICAgICAgICAgICAgICBqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0ci5oaWdobGlnaHQgaW5wdXRbbmFtZV49Zm9ybV9maWVsZF9uYW1lXScpLnZhbCggcm93X25hbWUgKTtcclxuLy8gICAgICAgICAgICAgICAgLy8wOlxyXG4vLyAgICAgICAgICAgICAgICBqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0ci5oaWdobGlnaHQgaW5wdXRbbmFtZV49Zm9ybV9maWVsZF90eXBlXScpLnZhbCggZmllbGRfdHlwZSApO1xyXG5cdC8vNTpcclxuXHRqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0ci5oaWdobGlnaHQgaW5wdXRbbmFtZV49Zm9ybV9maWVsZF92YWx1ZV0nKS52YWwoIHJvd192YWx1ZSApO1xyXG4vLyAgICAgICAgICAgICAgICAvLyBPcHRpb25zIGZpZWxkOlxyXG4vLyAgICAgICAgICAgICAgICBqUXVlcnkoJy53cGJjX3RhYmxlX2Zvcm1fZnJlZSB0ci5oaWdobGlnaHQgdGQuZmllbGRfb3B0aW9ucyBpbnB1dDpkaXNhYmxlZCcpLnZhbCggZmllbGRfdHlwZSArICd8JyArICByb3dfbmFtZSApO1xyXG5cclxuXHJcblx0Ly8gSGlkZSBnZW5lcmF0b3JzIGFuZCBSZXNldCBmb3JtcyAgYW5kIERpc2FibGUgaGlnaGxpZ2h0aW5nXHJcblx0d3BiY19oaWRlX2ZpZWxkc19nZW5lcmF0b3JzKCk7XHJcblxyXG5cdC8vIFN1Ym1pdCBmb3JtXHJcblx0ZG9jdW1lbnQuZm9ybXNbJ3dwYmNfZm9ybV9maWVsZF9mcmVlJ10uc3VibWl0KCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogQ2hlY2sgIFZhbHVlIGFuZCBwYXJzZSBpdCB0byBPcHRpb25zIGFuZCBUaXRsZXNcclxuICogQHBhcmFtIHtzdHJpbmd9IGZpZWxkX25hbWVcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY2hlY2tfdHlwZWRfdmFsdWVzKCBmaWVsZF9uYW1lICl7XHJcblxyXG5cdHZhciB0X29wdGlvbnNfdGl0bGVzX2FyciA9IHdwYmNfZ2V0X3RpdGxlc19vcHRpb25zX2Zyb21fdmFsdWVzKCAnIycgKyBmaWVsZF9uYW1lICsgJ192YWx1ZScgKTtcclxuXHJcblx0aWYgKCBmYWxzZSAhPT0gdF9vcHRpb25zX3RpdGxlc19hcnIgKSB7XHJcblxyXG5cdFx0dmFyIHRfb3B0aW9ucyA9IHRfb3B0aW9uc190aXRsZXNfYXJyWzBdLmpvaW4oIFwiXFxuXCIgKTtcclxuXHRcdHZhciB0X3RpdGxlcyAgPSB0X29wdGlvbnNfdGl0bGVzX2FyclsxXS5qb2luKCBcIlxcblwiICk7XHJcblx0XHRqUXVlcnkoJyMnICsgZmllbGRfbmFtZSArICdfb3B0aW9uc19vcHRpb25zJykudmFsKCB0X29wdGlvbnMgKTtcclxuXHRcdGpRdWVyeSgnIycgKyBmaWVsZF9uYW1lICsgJ19vcHRpb25zX3RpdGxlcycpLnZhbCggdF90aXRsZXMgKTtcclxuXHJcblx0fVxyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIEdldCBhcnJheSAgd2l0aCAgT3B0aW9ucyBhbmQgVGl0bGVzIGZyb20gIFZhbHVlcywgIGlmIGluIHZhbHVlcyB3YXMgZGVmaW5lZCBjb25zdHJ1dGlvbiAgbGlrZSB0aGlzIFx0XHRcdCcgT3B0aW9uIEBAIFRpdGxlICdcclxuICogQHBhcmFtIGZpZWxkX2lkIHN0cmluZ1xyXG4gKiBAcmV0dXJucyBhcnJheSB8IGZhbHNlXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2dldF90aXRsZXNfb3B0aW9uc19mcm9tX3ZhbHVlcyggZmllbGRfaWQgKXtcclxuXHRpZiAoICAgICggalF1ZXJ5KCBmaWVsZF9pZCApLnZhbCgpICE9ICcnIClcclxuXHRcdCAmJiAoICEgalF1ZXJ5KCBmaWVsZF9pZCApLmlzKCc6ZGlzYWJsZWQnKSApXHJcblx0XHQpe1xyXG5cclxuXHRcdHZhciB0c2xvdHMgPSBqUXVlcnkoIGZpZWxkX2lkICkudmFsKCk7XHJcblx0XHR0c2xvdHMgPSB0c2xvdHMuc3BsaXQoJ1xcbicpO1xyXG5cdFx0dmFyIHRfb3B0aW9ucyA9IFtdO1xyXG5cdFx0dmFyIHRfdGl0bGVzICA9IFtdO1xyXG5cdFx0dmFyIHNsb3RfdCA9ICcnO1xyXG5cclxuXHRcdGlmICggKCB0eXBlb2YgdHNsb3RzICE9PSAndW5kZWZpbmVkJyApICYmICggdHNsb3RzLmxlbmd0aCA+IDAgKSApe1xyXG5cclxuXHRcdFx0Zm9yICggdmFyIGk9MDsgaSA8IHRzbG90cy5sZW5ndGg7IGkrKyApIHtcclxuXHJcblx0XHRcdFx0c2xvdF90ID0gdHNsb3RzWyBpIF0uc3BsaXQoICdAQCcgKTtcclxuXHJcblx0XHRcdFx0aWYgKCBzbG90X3QubGVuZ3RoID4gMSApe1xyXG5cdFx0XHRcdFx0dF9vcHRpb25zLnB1c2goIHNsb3RfdFsgMSBdLnRyaW0oKSApO1xyXG5cdFx0XHRcdFx0dF90aXRsZXMucHVzaCggIHNsb3RfdFsgMCBdLnRyaW0oKSApO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHR0X29wdGlvbnMucHVzaCggc2xvdF90WyAwIF0udHJpbSgpICk7XHJcblx0XHRcdFx0XHR0X3RpdGxlcy5wdXNoKCAgJycgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHR9XHJcblx0XHR2YXIgdF9vcHRpb25zX3RpdGxlc19hcnIgPSBbXTtcclxuXHRcdHRfb3B0aW9uc190aXRsZXNfYXJyLnB1c2goIHRfb3B0aW9ucyApO1xyXG5cdFx0dF9vcHRpb25zX3RpdGxlc19hcnIucHVzaCggdF90aXRsZXMgKTtcclxuXHJcblx0XHRyZXR1cm4gdF9vcHRpb25zX3RpdGxlc19hcnI7XHJcblx0fVxyXG5cdHJldHVybiBmYWxzZTtcclxufVxyXG4iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNB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
