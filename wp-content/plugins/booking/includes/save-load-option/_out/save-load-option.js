"use strict";

/**
 * General Option Loader/Saver (client)
 *
 * - Provides:
 *     window.wpbc_save_option_from_element(el)
 *     window.wpbc_load_option_from_element(el)
 * - Busy UI (spinner + disabled)
 * - JSON path: send raw JSON string untouched.
 * - RAW scalar path: send as-is.
 * - Fields path: serialize to query-string via jQuery.param.
 *
 * IMPORTANT:
 * - jQuery .data() is cached. If some code updates data-* attributes via setAttribute(),
 *   reading via $el.data(...) can return stale values.
 * - Therefore, this module prefers reading via $el.attr('data-...') for dynamic keys
 *   (value/value-json), and falls back to $el.data(...) when attribute is missing.
 *
 * file: ../includes/save-load-option/_out/save-load-option.js
 *
 * Events:
 *   $(document).on('wpbc:option:beforeSave', (e, $el, payload) => {})
 *   $(document).on('wpbc:option:afterSave',  (e, response) => {})
 *   $(document).on('wpbc:option:beforeLoad', (e, $el, name) => {})
 *   $(document).on('wpbc:option:afterLoad',  (e, response) => {})
 */
(function (w, $) {
  'use strict';

  /**
   * Escape for safe HTML injection (small helper).
   *
   * @param {string} s
   * @returns {string}
   */
  function wpbc_uix_escape_html(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  /**
   * Read a value from data-* attribute first (fresh), then fallback to jQuery .data() cache.
   *
   * @param {jQuery} $el
   * @param {string} attr_name      Example: 'data-wpbc-u-save-value'
   * @param {string} data_key       Example: 'wpbc-u-save-value'
   * @returns {*}
   */
  function wpbc_uix_read_attr_or_data($el, attr_name, data_key) {
    var v = $el.attr(attr_name);
    if (typeof v !== 'undefined') {
      return v;
    }
    return $el.data(data_key);
  }

  /**
   * Turn "On"/"Off" like values into consistent "On"/"Off".
   * (Used for checkbox/toggle serialization.)
   *
   * @param {*} v
   * @returns {string}
   */
  function wpbc_uix_to_on_off(v) {
    if (v === true) {
      return 'On';
    }
    if (v === false) {
      return 'Off';
    }
    var s = String(v || '').toLowerCase();
    if (s === 'on' || s === '1' || s === 'true' || s === 'yes') {
      return 'On';
    }
    return 'Off';
  }

  /**
   * Get a useful value from an input/select/textarea element.
   * - checkbox => 'On'/'Off'
   * - radio    => value of checked in group (if possible), else ''
   * - others   => .val()
   *
   * @param {jQuery} $control
   * @returns {string}
   */
  function wpbc_uix_get_control_value($control) {
    if (!$control || !$control.length) {
      return '';
    }

    // checkbox/toggle.
    if ($control.is(':checkbox')) {
      return $control.is(':checked') ? 'On' : 'Off';
    }

    // radio group.
    if ($control.is(':radio')) {
      var name = $control.attr('name');
      if (name) {
        var $checked = $('input[type="radio"][name="' + name + '"]:checked');
        return $checked.length ? String($checked.val()) : '';
      }
      return $control.is(':checked') ? String($control.val()) : '';
    }

    // select/text/textarea/etc.
    return String($control.val() == null ? '' : $control.val());
  }

  /**
   * Busy ON UI for a clickable element.
   *
   * @param {jQuery} $el
   * @returns {void}
   */
  function wpbc_uix_busy_on($el) {
    if (!$el || !$el.length || $el.data('wpbc-uix-busy')) {
      return;
    }
    $el.data('wpbc-uix-busy', 1);
    $el.data('wpbc-uix-original-html', $el.html());
    var busy_text = $el.data('wpbc-u-busy-text');
    var spinner = '<span class="wpbc_icn_rotate_right wpbc_spin wpbc_ajax_icon wpbc_processing wpbc_icn_autorenew" aria-hidden="true"></span>';
    if (typeof busy_text === 'string' && busy_text.length) {
      $el.html(wpbc_uix_escape_html(busy_text) + ' ' + spinner);
    } else {
      $el.append(spinner);
    }
    $el.addClass('wpbc-is-busy').attr('aria-disabled', 'true').prop('disabled', true);
  }

  /**
   * Busy OFF UI for a clickable element.
   *
   * @param {jQuery} $el
   * @returns {void}
   */
  function wpbc_uix_busy_off($el) {
    if (!$el || !$el.length || !$el.data('wpbc-uix-busy')) {
      return;
    }
    var original = $el.data('wpbc-uix-original-html');
    if (typeof original === 'string') {
      $el.html(original);
    }
    $el.removeClass('wpbc-is-busy').removeAttr('aria-disabled').prop('disabled', false);
    $el.removeData('wpbc-uix-busy').removeData('wpbc-uix-original-html');
  }

  /**
   * Save Option - send ajax request to save data.
   *
   * Data attributes:
   *     data-wpbc-u-save-name       — option key (required)
   *     data-wpbc-u-save-nonce      — nonce value (required)
   *     data-wpbc-u-save-action     — nonce action (required)
   *     data-wpbc-u-save-value      — RAW scalar to save (optional)  (dynamic: read via attr first)
   *     data-wpbc-u-save-value-json — JSON string to save (optional) (dynamic: read via attr first)
   *     data-wpbc-u-save-fields     — CSV selectors; values serialized with jQuery.param (optional). Optional allowlist of keys for split mode (CSV).
   *     data-wpbc-u-save-mode       - Optional.: 'split' | ''  --  Optional: split JSON object into separate options server-side.
   *     data-wpbc-u-save-value-from — OPTIONAL selector to read scalar from (checkbox => On/Off)
   *     data-wpbc-u-busy-text       — custom text during AJAX (optional)
   *     data-wpbc-u-save-callback   — window function name to call on success (optional)
   *
   * @param {HTMLElement} el element with data attributes.
   * @returns {void}
   */
  w.wpbc_save_option_from_element = function (el) {
    if (!w.wpbc_option_saver_loader_config) {
      console.error('WPBC | config missing');
      return;
    }
    var $el = $(el);

    // Static values can be read from .data().
    var nonce = $el.data('wpbc-u-save-nonce');
    var nonce_action = $el.data('wpbc-u-save-action');
    var data_name = $el.data('wpbc-u-save-name');

    // Dynamic values MUST prefer attribute read (fresh), fallback to .data().
    var fields_raw = $el.data('wpbc-u-save-fields') || '';
    var inline_value = wpbc_uix_read_attr_or_data($el, 'data-wpbc-u-save-value', 'wpbc-u-save-value');
    var json = wpbc_uix_read_attr_or_data($el, 'data-wpbc-u-save-value-json', 'wpbc-u-save-value-json');
    var save_mode = $el.data('wpbc-u-save-mode') || $el.attr('data-wpbc-u-save-mode') || '';

    // Optional: compute scalar from another control selector at click time.
    var value_from_selector = $el.data('wpbc-u-save-value-from') || $el.attr('data-wpbc-u-save-value-from');
    var cb_id = $el.data('wpbc-u-save-callback');
    var cb_fn = cb_id && typeof w[cb_id] === 'function' ? w[cb_id] : null;
    if (!nonce || !nonce_action || !data_name) {
      console.error('WPBC | missing nonce/action/name');
      return;
    }
    var payload = '';

    // 1) JSON path.
    if (typeof json === 'string' && json.trim() !== '') {
      payload = json.trim();
    }
    // 2) Scalar computed from selector (checkbox => On/Off).
    else if (value_from_selector) {
      var $src = $(value_from_selector);
      var $control = $src.is('input,select,textarea') ? $src : $src.find('input,select,textarea').first();
      payload = wpbc_uix_get_control_value($control);
    }
    // 3) RAW scalar path.
    else if (typeof inline_value !== 'undefined' && inline_value !== null) {
      payload = String(inline_value);
    }
    // 4) Fields path (query-string).
    else if (fields_raw) {
      var fields = String(fields_raw).split(',').map(function (s) {
        return String(s || '').trim();
      }).filter(Boolean);
      var data = {};
      fields.forEach(function (sel) {
        var $f = $(sel);
        if (!$f.length) {
          return;
        }

        // If selector points to a wrapper, try to locate a real control inside.
        var $control = $f.is('input,select,textarea') ? $f : $f.find('input,select,textarea').first();
        if (!$control.length) {
          return;
        }
        var key = $control.attr('name') || $control.attr('id');
        if (!key) {
          return;
        }
        data[key] = wpbc_uix_get_control_value($control);
      });
      payload = $.param(data);
    } else {
      console.error('WPBC | provide value, value-from selector, json, or fields');
      return;
    }

    // Sync jQuery cache for the scalar value (helps other code that still reads .data()).
    // If payload looks like a simple scalar (not JSON, not query-string), keep it aligned.
    if (typeof payload === 'string' && payload.indexOf('=') === -1 && payload.indexOf('&') === -1) {
      try {
        $el.data('wpbc-u-save-value', payload);
      } catch (e) {}
    }
    $(document).trigger('wpbc:option:beforeSave', [$el, payload]);
    wpbc_uix_busy_on($el);
    $.ajax({
      url: w.wpbc_option_saver_loader_config.ajax_url,
      type: 'POST',
      data: {
        action: w.wpbc_option_saver_loader_config.action_save,
        nonce: nonce,
        nonce_action: nonce_action,
        data_name: data_name,
        data_value: payload,
        // Optional: split JSON object into separate options server-side.
        data_mode: save_mode,
        // Optional allowlist of keys for split mode (CSV).
        data_fields: save_mode === 'split' ? String(fields_raw || '') : ''
      }
    }).done(function (resp) {
      // NOTE: previously the code always showed "success" even on error.
      // Fixed: show success only when resp.success is true.

      if (resp && resp.success) {
        if (cb_fn) {
          try {
            cb_fn(resp);
          } catch (e) {
            console.error(e);
          }
        }
        var ok_message = resp && resp.data && resp.data.message ? resp.data.message : 'Saved';
        if (typeof w.wpbc_admin_show_message === 'function') {
          w.wpbc_admin_show_message(ok_message, 'success', 1000, false);
        }
      } else {
        var err_message = resp && resp.data && resp.data.message ? resp.data.message : 'Save error';
        console.error('WPBC | ' + err_message);
        if (typeof w.wpbc_admin_show_message === 'function') {
          w.wpbc_admin_show_message(err_message, 'error', 30000);
        }
      }
      $(document).trigger('wpbc:option:afterSave', [resp]);
    }).fail(function (xhr) {
      var feedback_message = 'WPBC | AJAX ' + xhr.status + ' ' + xhr.statusText;
      console.error(feedback_message);
      if (typeof w.wpbc_admin_show_message === 'function') {
        w.wpbc_admin_show_message(feedback_message, 'error', 30000);
      }
      $(document).trigger('wpbc:option:afterSave', [{
        success: false,
        data: {
          message: xhr.statusText
        }
      }]);
    }).always(function () {
      wpbc_uix_busy_off($el);
    });
  };

  /**
   * Load option value via AJAX.
   *
   * @param {HTMLElement} el element with data attributes.
   * @returns {void}
   */
  w.wpbc_load_option_from_element = function (el) {
    if (!w.wpbc_option_saver_loader_config) {
      console.error('WPBC | config missing');
      return;
    }
    var $el = $(el);
    var name = $el.data('wpbc-u-load-name') || $el.data('wpbc-u-save-name');
    var cb_id = $el.data('wpbc-u-load-callback');
    var cb_fn = cb_id && typeof w[cb_id] === 'function' ? w[cb_id] : null;
    if (!name) {
      console.error('WPBC | missing data-wpbc-u-load-name');
      return;
    }
    $(document).trigger('wpbc:option:beforeLoad', [$el, name]);
    wpbc_uix_busy_on($el);
    $.ajax({
      url: w.wpbc_option_saver_loader_config.ajax_url,
      type: 'GET',
      data: {
        action: w.wpbc_option_saver_loader_config.action_load,
        data_name: name
      }
    }).done(function (resp) {
      if (resp && resp.success) {
        if (cb_fn) {
          try {
            cb_fn(resp.data && resp.data.value);
          } catch (e) {
            console.error(e);
          }
        }
      } else {
        console.error('WPBC | ' + (resp && resp.data && resp.data.message ? resp.data.message : 'Load error'));
      }
      $(document).trigger('wpbc:option:afterLoad', [resp]);
    }).fail(function (xhr) {
      console.error('WPBC | AJAX ' + xhr.status + ' ' + xhr.statusText);
      $(document).trigger('wpbc:option:afterLoad', [{
        success: false,
        data: {
          message: xhr.statusText
        }
      }]);
    }).always(function () {
      wpbc_uix_busy_off($el);
    });
  };
})(window, jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvc2F2ZS1sb2FkLW9wdGlvbi9fb3V0L3NhdmUtbG9hZC1vcHRpb24uanMiLCJuYW1lcyI6WyJ3IiwiJCIsIndwYmNfdWl4X2VzY2FwZV9odG1sIiwicyIsIlN0cmluZyIsInJlcGxhY2UiLCJ3cGJjX3VpeF9yZWFkX2F0dHJfb3JfZGF0YSIsIiRlbCIsImF0dHJfbmFtZSIsImRhdGFfa2V5IiwidiIsImF0dHIiLCJkYXRhIiwid3BiY191aXhfdG9fb25fb2ZmIiwidG9Mb3dlckNhc2UiLCJ3cGJjX3VpeF9nZXRfY29udHJvbF92YWx1ZSIsIiRjb250cm9sIiwibGVuZ3RoIiwiaXMiLCJuYW1lIiwiJGNoZWNrZWQiLCJ2YWwiLCJ3cGJjX3VpeF9idXN5X29uIiwiaHRtbCIsImJ1c3lfdGV4dCIsInNwaW5uZXIiLCJhcHBlbmQiLCJhZGRDbGFzcyIsInByb3AiLCJ3cGJjX3VpeF9idXN5X29mZiIsIm9yaWdpbmFsIiwicmVtb3ZlQ2xhc3MiLCJyZW1vdmVBdHRyIiwicmVtb3ZlRGF0YSIsIndwYmNfc2F2ZV9vcHRpb25fZnJvbV9lbGVtZW50IiwiZWwiLCJ3cGJjX29wdGlvbl9zYXZlcl9sb2FkZXJfY29uZmlnIiwiY29uc29sZSIsImVycm9yIiwibm9uY2UiLCJub25jZV9hY3Rpb24iLCJkYXRhX25hbWUiLCJmaWVsZHNfcmF3IiwiaW5saW5lX3ZhbHVlIiwianNvbiIsInNhdmVfbW9kZSIsInZhbHVlX2Zyb21fc2VsZWN0b3IiLCJjYl9pZCIsImNiX2ZuIiwicGF5bG9hZCIsInRyaW0iLCIkc3JjIiwiZmluZCIsImZpcnN0IiwiZmllbGRzIiwic3BsaXQiLCJtYXAiLCJmaWx0ZXIiLCJCb29sZWFuIiwiZm9yRWFjaCIsInNlbCIsIiRmIiwia2V5IiwicGFyYW0iLCJpbmRleE9mIiwiZSIsImRvY3VtZW50IiwidHJpZ2dlciIsImFqYXgiLCJ1cmwiLCJhamF4X3VybCIsInR5cGUiLCJhY3Rpb24iLCJhY3Rpb25fc2F2ZSIsImRhdGFfdmFsdWUiLCJkYXRhX21vZGUiLCJkYXRhX2ZpZWxkcyIsImRvbmUiLCJyZXNwIiwic3VjY2VzcyIsIm9rX21lc3NhZ2UiLCJtZXNzYWdlIiwid3BiY19hZG1pbl9zaG93X21lc3NhZ2UiLCJlcnJfbWVzc2FnZSIsImZhaWwiLCJ4aHIiLCJmZWVkYmFja19tZXNzYWdlIiwic3RhdHVzIiwic3RhdHVzVGV4dCIsImFsd2F5cyIsIndwYmNfbG9hZF9vcHRpb25fZnJvbV9lbGVtZW50IiwiYWN0aW9uX2xvYWQiLCJ2YWx1ZSIsIndpbmRvdyIsImpRdWVyeSJdLCJzb3VyY2VzIjpbImluY2x1ZGVzL3NhdmUtbG9hZC1vcHRpb24vX3NyYy9zYXZlLWxvYWQtb3B0aW9uLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBHZW5lcmFsIE9wdGlvbiBMb2FkZXIvU2F2ZXIgKGNsaWVudClcclxuICpcclxuICogLSBQcm92aWRlczpcclxuICogICAgIHdpbmRvdy53cGJjX3NhdmVfb3B0aW9uX2Zyb21fZWxlbWVudChlbClcclxuICogICAgIHdpbmRvdy53cGJjX2xvYWRfb3B0aW9uX2Zyb21fZWxlbWVudChlbClcclxuICogLSBCdXN5IFVJIChzcGlubmVyICsgZGlzYWJsZWQpXHJcbiAqIC0gSlNPTiBwYXRoOiBzZW5kIHJhdyBKU09OIHN0cmluZyB1bnRvdWNoZWQuXHJcbiAqIC0gUkFXIHNjYWxhciBwYXRoOiBzZW5kIGFzLWlzLlxyXG4gKiAtIEZpZWxkcyBwYXRoOiBzZXJpYWxpemUgdG8gcXVlcnktc3RyaW5nIHZpYSBqUXVlcnkucGFyYW0uXHJcbiAqXHJcbiAqIElNUE9SVEFOVDpcclxuICogLSBqUXVlcnkgLmRhdGEoKSBpcyBjYWNoZWQuIElmIHNvbWUgY29kZSB1cGRhdGVzIGRhdGEtKiBhdHRyaWJ1dGVzIHZpYSBzZXRBdHRyaWJ1dGUoKSxcclxuICogICByZWFkaW5nIHZpYSAkZWwuZGF0YSguLi4pIGNhbiByZXR1cm4gc3RhbGUgdmFsdWVzLlxyXG4gKiAtIFRoZXJlZm9yZSwgdGhpcyBtb2R1bGUgcHJlZmVycyByZWFkaW5nIHZpYSAkZWwuYXR0cignZGF0YS0uLi4nKSBmb3IgZHluYW1pYyBrZXlzXHJcbiAqICAgKHZhbHVlL3ZhbHVlLWpzb24pLCBhbmQgZmFsbHMgYmFjayB0byAkZWwuZGF0YSguLi4pIHdoZW4gYXR0cmlidXRlIGlzIG1pc3NpbmcuXHJcbiAqXHJcbiAqIGZpbGU6IC4uL2luY2x1ZGVzL3NhdmUtbG9hZC1vcHRpb24vX291dC9zYXZlLWxvYWQtb3B0aW9uLmpzXHJcbiAqXHJcbiAqIEV2ZW50czpcclxuICogICAkKGRvY3VtZW50KS5vbignd3BiYzpvcHRpb246YmVmb3JlU2F2ZScsIChlLCAkZWwsIHBheWxvYWQpID0+IHt9KVxyXG4gKiAgICQoZG9jdW1lbnQpLm9uKCd3cGJjOm9wdGlvbjphZnRlclNhdmUnLCAgKGUsIHJlc3BvbnNlKSA9PiB7fSlcclxuICogICAkKGRvY3VtZW50KS5vbignd3BiYzpvcHRpb246YmVmb3JlTG9hZCcsIChlLCAkZWwsIG5hbWUpID0+IHt9KVxyXG4gKiAgICQoZG9jdW1lbnQpLm9uKCd3cGJjOm9wdGlvbjphZnRlckxvYWQnLCAgKGUsIHJlc3BvbnNlKSA9PiB7fSlcclxuICovXHJcbihmdW5jdGlvbiAodywgJCkge1xyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0LyoqXHJcblx0ICogRXNjYXBlIGZvciBzYWZlIEhUTUwgaW5qZWN0aW9uIChzbWFsbCBoZWxwZXIpLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IHNcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfdWl4X2VzY2FwZV9odG1sKHMpIHtcclxuXHRcdHJldHVybiBTdHJpbmcocylcclxuXHRcdFx0LnJlcGxhY2UoLyYvZywgJyZhbXA7JylcclxuXHRcdFx0LnJlcGxhY2UoLzwvZywgJyZsdDsnKVxyXG5cdFx0XHQucmVwbGFjZSgvPi9nLCAnJmd0OycpXHJcblx0XHRcdC5yZXBsYWNlKC9cIi9nLCAnJnF1b3Q7JylcclxuXHRcdFx0LnJlcGxhY2UoLycvZywgJyYjMDM5OycpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogUmVhZCBhIHZhbHVlIGZyb20gZGF0YS0qIGF0dHJpYnV0ZSBmaXJzdCAoZnJlc2gpLCB0aGVuIGZhbGxiYWNrIHRvIGpRdWVyeSAuZGF0YSgpIGNhY2hlLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICRlbFxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBhdHRyX25hbWUgICAgICBFeGFtcGxlOiAnZGF0YS13cGJjLXUtc2F2ZS12YWx1ZSdcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gZGF0YV9rZXkgICAgICAgRXhhbXBsZTogJ3dwYmMtdS1zYXZlLXZhbHVlJ1xyXG5cdCAqIEByZXR1cm5zIHsqfVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfdWl4X3JlYWRfYXR0cl9vcl9kYXRhKCRlbCwgYXR0cl9uYW1lLCBkYXRhX2tleSkge1xyXG5cdFx0dmFyIHYgPSAkZWwuYXR0cihhdHRyX25hbWUpO1xyXG5cdFx0aWYgKHR5cGVvZiB2ICE9PSAndW5kZWZpbmVkJykge1xyXG5cdFx0XHRyZXR1cm4gdjtcclxuXHRcdH1cclxuXHRcdHJldHVybiAkZWwuZGF0YShkYXRhX2tleSk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBUdXJuIFwiT25cIi9cIk9mZlwiIGxpa2UgdmFsdWVzIGludG8gY29uc2lzdGVudCBcIk9uXCIvXCJPZmZcIi5cclxuXHQgKiAoVXNlZCBmb3IgY2hlY2tib3gvdG9nZ2xlIHNlcmlhbGl6YXRpb24uKVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHsqfSB2XHJcblx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX3VpeF90b19vbl9vZmYodikge1xyXG5cdFx0aWYgKHYgPT09IHRydWUpIHsgcmV0dXJuICdPbic7IH1cclxuXHRcdGlmICh2ID09PSBmYWxzZSkgeyByZXR1cm4gJ09mZic7IH1cclxuXHRcdHZhciBzID0gU3RyaW5nKHYgfHwgJycpLnRvTG93ZXJDYXNlKCk7XHJcblx0XHRpZiAocyA9PT0gJ29uJyB8fCBzID09PSAnMScgfHwgcyA9PT0gJ3RydWUnIHx8IHMgPT09ICd5ZXMnKSB7IHJldHVybiAnT24nOyB9XHJcblx0XHRyZXR1cm4gJ09mZic7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYSB1c2VmdWwgdmFsdWUgZnJvbSBhbiBpbnB1dC9zZWxlY3QvdGV4dGFyZWEgZWxlbWVudC5cclxuXHQgKiAtIGNoZWNrYm94ID0+ICdPbicvJ09mZidcclxuXHQgKiAtIHJhZGlvICAgID0+IHZhbHVlIG9mIGNoZWNrZWQgaW4gZ3JvdXAgKGlmIHBvc3NpYmxlKSwgZWxzZSAnJ1xyXG5cdCAqIC0gb3RoZXJzICAgPT4gLnZhbCgpXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJGNvbnRyb2xcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfdWl4X2dldF9jb250cm9sX3ZhbHVlKCRjb250cm9sKSB7XHJcblxyXG5cdFx0aWYgKCEkY29udHJvbCB8fCAhJGNvbnRyb2wubGVuZ3RoKSB7XHJcblx0XHRcdHJldHVybiAnJztcclxuXHRcdH1cclxuXHJcblx0XHQvLyBjaGVja2JveC90b2dnbGUuXHJcblx0XHRpZiAoJGNvbnRyb2wuaXMoJzpjaGVja2JveCcpKSB7XHJcblx0XHRcdHJldHVybiAkY29udHJvbC5pcygnOmNoZWNrZWQnKSA/ICdPbicgOiAnT2ZmJztcclxuXHRcdH1cclxuXHJcblx0XHQvLyByYWRpbyBncm91cC5cclxuXHRcdGlmICgkY29udHJvbC5pcygnOnJhZGlvJykpIHtcclxuXHRcdFx0dmFyIG5hbWUgPSAkY29udHJvbC5hdHRyKCduYW1lJyk7XHJcblx0XHRcdGlmIChuYW1lKSB7XHJcblx0XHRcdFx0dmFyICRjaGVja2VkID0gJCgnaW5wdXRbdHlwZT1cInJhZGlvXCJdW25hbWU9XCInICsgbmFtZSArICdcIl06Y2hlY2tlZCcpO1xyXG5cdFx0XHRcdHJldHVybiAkY2hlY2tlZC5sZW5ndGggPyBTdHJpbmcoJGNoZWNrZWQudmFsKCkpIDogJyc7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuICRjb250cm9sLmlzKCc6Y2hlY2tlZCcpID8gU3RyaW5nKCRjb250cm9sLnZhbCgpKSA6ICcnO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIHNlbGVjdC90ZXh0L3RleHRhcmVhL2V0Yy5cclxuXHRcdHJldHVybiBTdHJpbmcoJGNvbnRyb2wudmFsKCkgPT0gbnVsbCA/ICcnIDogJGNvbnRyb2wudmFsKCkpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogQnVzeSBPTiBVSSBmb3IgYSBjbGlja2FibGUgZWxlbWVudC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5fSAkZWxcclxuXHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX3VpeF9idXN5X29uKCRlbCkge1xyXG5cdFx0aWYgKCEkZWwgfHwgISRlbC5sZW5ndGggfHwgJGVsLmRhdGEoJ3dwYmMtdWl4LWJ1c3knKSkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblxyXG5cdFx0JGVsLmRhdGEoJ3dwYmMtdWl4LWJ1c3knLCAxKTtcclxuXHRcdCRlbC5kYXRhKCd3cGJjLXVpeC1vcmlnaW5hbC1odG1sJywgJGVsLmh0bWwoKSk7XHJcblxyXG5cdFx0dmFyIGJ1c3lfdGV4dCA9ICRlbC5kYXRhKCd3cGJjLXUtYnVzeS10ZXh0Jyk7XHJcblx0XHR2YXIgc3Bpbm5lciA9ICc8c3BhbiBjbGFzcz1cIndwYmNfaWNuX3JvdGF0ZV9yaWdodCB3cGJjX3NwaW4gd3BiY19hamF4X2ljb24gd3BiY19wcm9jZXNzaW5nIHdwYmNfaWNuX2F1dG9yZW5ld1wiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPjwvc3Bhbj4nO1xyXG5cclxuXHRcdGlmICh0eXBlb2YgYnVzeV90ZXh0ID09PSAnc3RyaW5nJyAmJiBidXN5X3RleHQubGVuZ3RoKSB7XHJcblx0XHRcdCRlbC5odG1sKHdwYmNfdWl4X2VzY2FwZV9odG1sKGJ1c3lfdGV4dCkgKyAnICcgKyBzcGlubmVyKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdCRlbC5hcHBlbmQoc3Bpbm5lcik7XHJcblx0XHR9XHJcblxyXG5cdFx0JGVsLmFkZENsYXNzKCd3cGJjLWlzLWJ1c3knKVxyXG5cdFx0XHQuYXR0cignYXJpYS1kaXNhYmxlZCcsICd0cnVlJylcclxuXHRcdFx0LnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBCdXN5IE9GRiBVSSBmb3IgYSBjbGlja2FibGUgZWxlbWVudC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5fSAkZWxcclxuXHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX3VpeF9idXN5X29mZigkZWwpIHtcclxuXHRcdGlmICghJGVsIHx8ICEkZWwubGVuZ3RoIHx8ICEkZWwuZGF0YSgnd3BiYy11aXgtYnVzeScpKSB7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgb3JpZ2luYWwgPSAkZWwuZGF0YSgnd3BiYy11aXgtb3JpZ2luYWwtaHRtbCcpO1xyXG5cdFx0aWYgKHR5cGVvZiBvcmlnaW5hbCA9PT0gJ3N0cmluZycpIHtcclxuXHRcdFx0JGVsLmh0bWwob3JpZ2luYWwpO1xyXG5cdFx0fVxyXG5cclxuXHRcdCRlbC5yZW1vdmVDbGFzcygnd3BiYy1pcy1idXN5JylcclxuXHRcdFx0LnJlbW92ZUF0dHIoJ2FyaWEtZGlzYWJsZWQnKVxyXG5cdFx0XHQucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XHJcblxyXG5cdFx0JGVsLnJlbW92ZURhdGEoJ3dwYmMtdWl4LWJ1c3knKVxyXG5cdFx0XHQucmVtb3ZlRGF0YSgnd3BiYy11aXgtb3JpZ2luYWwtaHRtbCcpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogU2F2ZSBPcHRpb24gLSBzZW5kIGFqYXggcmVxdWVzdCB0byBzYXZlIGRhdGEuXHJcblx0ICpcclxuXHQgKiBEYXRhIGF0dHJpYnV0ZXM6XHJcblx0ICogICAgIGRhdGEtd3BiYy11LXNhdmUtbmFtZSAgICAgICDigJQgb3B0aW9uIGtleSAocmVxdWlyZWQpXHJcblx0ICogICAgIGRhdGEtd3BiYy11LXNhdmUtbm9uY2UgICAgICDigJQgbm9uY2UgdmFsdWUgKHJlcXVpcmVkKVxyXG5cdCAqICAgICBkYXRhLXdwYmMtdS1zYXZlLWFjdGlvbiAgICAg4oCUIG5vbmNlIGFjdGlvbiAocmVxdWlyZWQpXHJcblx0ICogICAgIGRhdGEtd3BiYy11LXNhdmUtdmFsdWUgICAgICDigJQgUkFXIHNjYWxhciB0byBzYXZlIChvcHRpb25hbCkgIChkeW5hbWljOiByZWFkIHZpYSBhdHRyIGZpcnN0KVxyXG5cdCAqICAgICBkYXRhLXdwYmMtdS1zYXZlLXZhbHVlLWpzb24g4oCUIEpTT04gc3RyaW5nIHRvIHNhdmUgKG9wdGlvbmFsKSAoZHluYW1pYzogcmVhZCB2aWEgYXR0ciBmaXJzdClcclxuXHQgKiAgICAgZGF0YS13cGJjLXUtc2F2ZS1maWVsZHMgICAgIOKAlCBDU1Ygc2VsZWN0b3JzOyB2YWx1ZXMgc2VyaWFsaXplZCB3aXRoIGpRdWVyeS5wYXJhbSAob3B0aW9uYWwpLiBPcHRpb25hbCBhbGxvd2xpc3Qgb2Yga2V5cyBmb3Igc3BsaXQgbW9kZSAoQ1NWKS5cclxuXHQgKiAgICAgZGF0YS13cGJjLXUtc2F2ZS1tb2RlICAgICAgIC0gT3B0aW9uYWwuOiAnc3BsaXQnIHwgJycgIC0tICBPcHRpb25hbDogc3BsaXQgSlNPTiBvYmplY3QgaW50byBzZXBhcmF0ZSBvcHRpb25zIHNlcnZlci1zaWRlLlxyXG5cdCAqICAgICBkYXRhLXdwYmMtdS1zYXZlLXZhbHVlLWZyb20g4oCUIE9QVElPTkFMIHNlbGVjdG9yIHRvIHJlYWQgc2NhbGFyIGZyb20gKGNoZWNrYm94ID0+IE9uL09mZilcclxuXHQgKiAgICAgZGF0YS13cGJjLXUtYnVzeS10ZXh0ICAgICAgIOKAlCBjdXN0b20gdGV4dCBkdXJpbmcgQUpBWCAob3B0aW9uYWwpXHJcblx0ICogICAgIGRhdGEtd3BiYy11LXNhdmUtY2FsbGJhY2sgICDigJQgd2luZG93IGZ1bmN0aW9uIG5hbWUgdG8gY2FsbCBvbiBzdWNjZXNzIChvcHRpb25hbClcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsIGVsZW1lbnQgd2l0aCBkYXRhIGF0dHJpYnV0ZXMuXHJcblx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0ICovXHJcblx0dy53cGJjX3NhdmVfb3B0aW9uX2Zyb21fZWxlbWVudCA9IGZ1bmN0aW9uIChlbCkge1xyXG5cclxuXHRcdGlmICghdy53cGJjX29wdGlvbl9zYXZlcl9sb2FkZXJfY29uZmlnKSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoJ1dQQkMgfCBjb25maWcgbWlzc2luZycpO1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyICRlbCA9ICQoZWwpO1xyXG5cclxuXHRcdC8vIFN0YXRpYyB2YWx1ZXMgY2FuIGJlIHJlYWQgZnJvbSAuZGF0YSgpLlxyXG5cdFx0dmFyIG5vbmNlICAgICAgICA9ICRlbC5kYXRhKCd3cGJjLXUtc2F2ZS1ub25jZScpO1xyXG5cdFx0dmFyIG5vbmNlX2FjdGlvbiA9ICRlbC5kYXRhKCd3cGJjLXUtc2F2ZS1hY3Rpb24nKTtcclxuXHRcdHZhciBkYXRhX25hbWUgICAgPSAkZWwuZGF0YSgnd3BiYy11LXNhdmUtbmFtZScpO1xyXG5cclxuXHRcdC8vIER5bmFtaWMgdmFsdWVzIE1VU1QgcHJlZmVyIGF0dHJpYnV0ZSByZWFkIChmcmVzaCksIGZhbGxiYWNrIHRvIC5kYXRhKCkuXHJcblx0XHR2YXIgZmllbGRzX3JhdyAgID0gJGVsLmRhdGEoJ3dwYmMtdS1zYXZlLWZpZWxkcycpIHx8ICcnO1xyXG5cdFx0dmFyIGlubGluZV92YWx1ZSA9IHdwYmNfdWl4X3JlYWRfYXR0cl9vcl9kYXRhKCRlbCwgJ2RhdGEtd3BiYy11LXNhdmUtdmFsdWUnLCAnd3BiYy11LXNhdmUtdmFsdWUnKTtcclxuXHRcdHZhciBqc29uICAgICAgICAgPSB3cGJjX3VpeF9yZWFkX2F0dHJfb3JfZGF0YSgkZWwsICdkYXRhLXdwYmMtdS1zYXZlLXZhbHVlLWpzb24nLCAnd3BiYy11LXNhdmUtdmFsdWUtanNvbicpO1xyXG5cdFx0dmFyIHNhdmVfbW9kZSAgICA9ICRlbC5kYXRhKCd3cGJjLXUtc2F2ZS1tb2RlJykgfHwgJGVsLmF0dHIoJ2RhdGEtd3BiYy11LXNhdmUtbW9kZScpIHx8ICcnO1xyXG5cclxuXHRcdC8vIE9wdGlvbmFsOiBjb21wdXRlIHNjYWxhciBmcm9tIGFub3RoZXIgY29udHJvbCBzZWxlY3RvciBhdCBjbGljayB0aW1lLlxyXG5cdFx0dmFyIHZhbHVlX2Zyb21fc2VsZWN0b3IgPSAkZWwuZGF0YSgnd3BiYy11LXNhdmUtdmFsdWUtZnJvbScpIHx8ICRlbC5hdHRyKCdkYXRhLXdwYmMtdS1zYXZlLXZhbHVlLWZyb20nKTtcclxuXHJcblx0XHR2YXIgY2JfaWQgPSAkZWwuZGF0YSgnd3BiYy11LXNhdmUtY2FsbGJhY2snKTtcclxuXHRcdHZhciBjYl9mbiA9IChjYl9pZCAmJiB0eXBlb2Ygd1tjYl9pZF0gPT09ICdmdW5jdGlvbicpID8gd1tjYl9pZF0gOiBudWxsO1xyXG5cclxuXHRcdGlmICghbm9uY2UgfHwgIW5vbmNlX2FjdGlvbiB8fCAhZGF0YV9uYW1lKSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoJ1dQQkMgfCBtaXNzaW5nIG5vbmNlL2FjdGlvbi9uYW1lJyk7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgcGF5bG9hZCA9ICcnO1xyXG5cclxuXHRcdC8vIDEpIEpTT04gcGF0aC5cclxuXHRcdGlmICh0eXBlb2YganNvbiA9PT0gJ3N0cmluZycgJiYganNvbi50cmltKCkgIT09ICcnKSB7XHJcblx0XHRcdHBheWxvYWQgPSBqc29uLnRyaW0oKTtcclxuXHRcdH1cclxuXHRcdC8vIDIpIFNjYWxhciBjb21wdXRlZCBmcm9tIHNlbGVjdG9yIChjaGVja2JveCA9PiBPbi9PZmYpLlxyXG5cdFx0ZWxzZSBpZiAodmFsdWVfZnJvbV9zZWxlY3Rvcikge1xyXG5cdFx0XHR2YXIgJHNyYyA9ICQodmFsdWVfZnJvbV9zZWxlY3Rvcik7XHJcblx0XHRcdHZhciAkY29udHJvbCA9ICRzcmMuaXMoJ2lucHV0LHNlbGVjdCx0ZXh0YXJlYScpID8gJHNyYyA6ICRzcmMuZmluZCgnaW5wdXQsc2VsZWN0LHRleHRhcmVhJykuZmlyc3QoKTtcclxuXHRcdFx0cGF5bG9hZCA9IHdwYmNfdWl4X2dldF9jb250cm9sX3ZhbHVlKCRjb250cm9sKTtcclxuXHRcdH1cclxuXHRcdC8vIDMpIFJBVyBzY2FsYXIgcGF0aC5cclxuXHRcdGVsc2UgaWYgKHR5cGVvZiBpbmxpbmVfdmFsdWUgIT09ICd1bmRlZmluZWQnICYmIGlubGluZV92YWx1ZSAhPT0gbnVsbCkge1xyXG5cdFx0XHRwYXlsb2FkID0gU3RyaW5nKGlubGluZV92YWx1ZSk7XHJcblx0XHR9XHJcblx0XHQvLyA0KSBGaWVsZHMgcGF0aCAocXVlcnktc3RyaW5nKS5cclxuXHRcdGVsc2UgaWYgKGZpZWxkc19yYXcpIHtcclxuXHJcblx0XHRcdHZhciBmaWVsZHMgPSBTdHJpbmcoZmllbGRzX3Jhdykuc3BsaXQoJywnKVxyXG5cdFx0XHRcdC5tYXAoZnVuY3Rpb24gKHMpIHsgcmV0dXJuIFN0cmluZyhzIHx8ICcnKS50cmltKCk7IH0pXHJcblx0XHRcdFx0LmZpbHRlcihCb29sZWFuKTtcclxuXHJcblx0XHRcdHZhciBkYXRhID0ge307XHJcblxyXG5cdFx0XHRmaWVsZHMuZm9yRWFjaChmdW5jdGlvbiAoc2VsKSB7XHJcblx0XHRcdFx0dmFyICRmID0gJChzZWwpO1xyXG5cdFx0XHRcdGlmICghJGYubGVuZ3RoKSB7IHJldHVybjsgfVxyXG5cclxuXHRcdFx0XHQvLyBJZiBzZWxlY3RvciBwb2ludHMgdG8gYSB3cmFwcGVyLCB0cnkgdG8gbG9jYXRlIGEgcmVhbCBjb250cm9sIGluc2lkZS5cclxuXHRcdFx0XHR2YXIgJGNvbnRyb2wgPSAkZi5pcygnaW5wdXQsc2VsZWN0LHRleHRhcmVhJykgPyAkZiA6ICRmLmZpbmQoJ2lucHV0LHNlbGVjdCx0ZXh0YXJlYScpLmZpcnN0KCk7XHJcblx0XHRcdFx0aWYgKCEkY29udHJvbC5sZW5ndGgpIHsgcmV0dXJuOyB9XHJcblxyXG5cdFx0XHRcdHZhciBrZXkgPSAkY29udHJvbC5hdHRyKCduYW1lJykgfHwgJGNvbnRyb2wuYXR0cignaWQnKTtcclxuXHRcdFx0XHRpZiAoIWtleSkgeyByZXR1cm47IH1cclxuXHJcblx0XHRcdFx0ZGF0YVtrZXldID0gd3BiY191aXhfZ2V0X2NvbnRyb2xfdmFsdWUoJGNvbnRyb2wpO1xyXG5cdFx0XHR9KTtcclxuXHJcblx0XHRcdHBheWxvYWQgPSAkLnBhcmFtKGRhdGEpO1xyXG5cdFx0fVxyXG5cdFx0ZWxzZSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoJ1dQQkMgfCBwcm92aWRlIHZhbHVlLCB2YWx1ZS1mcm9tIHNlbGVjdG9yLCBqc29uLCBvciBmaWVsZHMnKTtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN5bmMgalF1ZXJ5IGNhY2hlIGZvciB0aGUgc2NhbGFyIHZhbHVlIChoZWxwcyBvdGhlciBjb2RlIHRoYXQgc3RpbGwgcmVhZHMgLmRhdGEoKSkuXHJcblx0XHQvLyBJZiBwYXlsb2FkIGxvb2tzIGxpa2UgYSBzaW1wbGUgc2NhbGFyIChub3QgSlNPTiwgbm90IHF1ZXJ5LXN0cmluZyksIGtlZXAgaXQgYWxpZ25lZC5cclxuXHRcdGlmICh0eXBlb2YgcGF5bG9hZCA9PT0gJ3N0cmluZycgJiYgcGF5bG9hZC5pbmRleE9mKCc9JykgPT09IC0xICYmIHBheWxvYWQuaW5kZXhPZignJicpID09PSAtMSkge1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdCRlbC5kYXRhKCd3cGJjLXUtc2F2ZS12YWx1ZScsIHBheWxvYWQpO1xyXG5cdFx0XHR9IGNhdGNoIChlKSB7fVxyXG5cdFx0fVxyXG5cclxuXHRcdCQoZG9jdW1lbnQpLnRyaWdnZXIoJ3dwYmM6b3B0aW9uOmJlZm9yZVNhdmUnLCBbICRlbCwgcGF5bG9hZCBdKTtcclxuXHRcdHdwYmNfdWl4X2J1c3lfb24oJGVsKTtcclxuXHJcblx0XHQkLmFqYXgoe1xyXG5cdFx0XHR1cmw6ICB3LndwYmNfb3B0aW9uX3NhdmVyX2xvYWRlcl9jb25maWcuYWpheF91cmwsXHJcblx0XHRcdHR5cGU6ICdQT1NUJyxcclxuXHRcdFx0ZGF0YToge1xyXG5cdFx0XHRcdGFjdGlvbjogICAgICAgdy53cGJjX29wdGlvbl9zYXZlcl9sb2FkZXJfY29uZmlnLmFjdGlvbl9zYXZlLFxyXG5cdFx0XHRcdG5vbmNlOiAgICAgICAgbm9uY2UsXHJcblx0XHRcdFx0bm9uY2VfYWN0aW9uOiBub25jZV9hY3Rpb24sXHJcblx0XHRcdFx0ZGF0YV9uYW1lOiAgICBkYXRhX25hbWUsXHJcblx0XHRcdFx0ZGF0YV92YWx1ZTogICBwYXlsb2FkLFxyXG5cclxuXHRcdFx0XHQvLyBPcHRpb25hbDogc3BsaXQgSlNPTiBvYmplY3QgaW50byBzZXBhcmF0ZSBvcHRpb25zIHNlcnZlci1zaWRlLlxyXG5cdFx0XHRcdGRhdGFfbW9kZTogICAgc2F2ZV9tb2RlLFxyXG5cclxuXHRcdFx0XHQvLyBPcHRpb25hbCBhbGxvd2xpc3Qgb2Yga2V5cyBmb3Igc3BsaXQgbW9kZSAoQ1NWKS5cclxuXHRcdFx0XHRkYXRhX2ZpZWxkczogIChzYXZlX21vZGUgPT09ICdzcGxpdCcgPyBTdHJpbmcoZmllbGRzX3JhdyB8fCAnJykgOiAnJylcclxuXHRcdFx0fVxyXG5cdFx0fSlcclxuXHRcdC5kb25lKGZ1bmN0aW9uIChyZXNwKSB7XHJcblxyXG5cdFx0XHQvLyBOT1RFOiBwcmV2aW91c2x5IHRoZSBjb2RlIGFsd2F5cyBzaG93ZWQgXCJzdWNjZXNzXCIgZXZlbiBvbiBlcnJvci5cclxuXHRcdFx0Ly8gRml4ZWQ6IHNob3cgc3VjY2VzcyBvbmx5IHdoZW4gcmVzcC5zdWNjZXNzIGlzIHRydWUuXHJcblxyXG5cdFx0XHRpZiAocmVzcCAmJiByZXNwLnN1Y2Nlc3MpIHtcclxuXHJcblx0XHRcdFx0aWYgKGNiX2ZuKSB7XHJcblx0XHRcdFx0XHR0cnkgeyBjYl9mbihyZXNwKTsgfSBjYXRjaCAoZSkgeyBjb25zb2xlLmVycm9yKGUpOyB9XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR2YXIgb2tfbWVzc2FnZSA9IChyZXNwICYmIHJlc3AuZGF0YSAmJiByZXNwLmRhdGEubWVzc2FnZSkgPyByZXNwLmRhdGEubWVzc2FnZSA6ICdTYXZlZCc7XHJcblx0XHRcdFx0aWYgKHR5cGVvZiB3LndwYmNfYWRtaW5fc2hvd19tZXNzYWdlID09PSAnZnVuY3Rpb24nKSB7XHJcblx0XHRcdFx0XHR3LndwYmNfYWRtaW5fc2hvd19tZXNzYWdlKG9rX21lc3NhZ2UsICdzdWNjZXNzJywgMTAwMCwgZmFsc2UpO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHRcdHZhciBlcnJfbWVzc2FnZSA9IChyZXNwICYmIHJlc3AuZGF0YSAmJiByZXNwLmRhdGEubWVzc2FnZSkgPyByZXNwLmRhdGEubWVzc2FnZSA6ICdTYXZlIGVycm9yJztcclxuXHRcdFx0XHRjb25zb2xlLmVycm9yKCdXUEJDIHwgJyArIGVycl9tZXNzYWdlKTtcclxuXHJcblx0XHRcdFx0aWYgKHR5cGVvZiB3LndwYmNfYWRtaW5fc2hvd19tZXNzYWdlID09PSAnZnVuY3Rpb24nKSB7XHJcblx0XHRcdFx0XHR3LndwYmNfYWRtaW5fc2hvd19tZXNzYWdlKGVycl9tZXNzYWdlLCAnZXJyb3InLCAzMDAwMCk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQkKGRvY3VtZW50KS50cmlnZ2VyKCd3cGJjOm9wdGlvbjphZnRlclNhdmUnLCBbIHJlc3AgXSk7XHJcblx0XHR9KVxyXG5cdFx0LmZhaWwoZnVuY3Rpb24gKHhocikge1xyXG5cdFx0XHR2YXIgZmVlZGJhY2tfbWVzc2FnZSA9ICdXUEJDIHwgQUpBWCAnICsgeGhyLnN0YXR1cyArICcgJyArIHhoci5zdGF0dXNUZXh0O1xyXG5cdFx0XHRjb25zb2xlLmVycm9yKGZlZWRiYWNrX21lc3NhZ2UpO1xyXG5cclxuXHRcdFx0aWYgKHR5cGVvZiB3LndwYmNfYWRtaW5fc2hvd19tZXNzYWdlID09PSAnZnVuY3Rpb24nKSB7XHJcblx0XHRcdFx0dy53cGJjX2FkbWluX3Nob3dfbWVzc2FnZShmZWVkYmFja19tZXNzYWdlLCAnZXJyb3InLCAzMDAwMCk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdCQoZG9jdW1lbnQpLnRyaWdnZXIoJ3dwYmM6b3B0aW9uOmFmdGVyU2F2ZScsIFsgeyBzdWNjZXNzOiBmYWxzZSwgZGF0YTogeyBtZXNzYWdlOiB4aHIuc3RhdHVzVGV4dCB9IH0gXSk7XHJcblx0XHR9KVxyXG5cdFx0LmFsd2F5cyhmdW5jdGlvbiAoKSB7XHJcblx0XHRcdHdwYmNfdWl4X2J1c3lfb2ZmKCRlbCk7XHJcblx0XHR9KTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBMb2FkIG9wdGlvbiB2YWx1ZSB2aWEgQUpBWC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsIGVsZW1lbnQgd2l0aCBkYXRhIGF0dHJpYnV0ZXMuXHJcblx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0ICovXHJcblx0dy53cGJjX2xvYWRfb3B0aW9uX2Zyb21fZWxlbWVudCA9IGZ1bmN0aW9uIChlbCkge1xyXG5cclxuXHRcdGlmICghdy53cGJjX29wdGlvbl9zYXZlcl9sb2FkZXJfY29uZmlnKSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoJ1dQQkMgfCBjb25maWcgbWlzc2luZycpO1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyICRlbCAgPSAkKGVsKTtcclxuXHRcdHZhciBuYW1lID0gJGVsLmRhdGEoJ3dwYmMtdS1sb2FkLW5hbWUnKSB8fCAkZWwuZGF0YSgnd3BiYy11LXNhdmUtbmFtZScpO1xyXG5cclxuXHRcdHZhciBjYl9pZCA9ICRlbC5kYXRhKCd3cGJjLXUtbG9hZC1jYWxsYmFjaycpO1xyXG5cdFx0dmFyIGNiX2ZuID0gKGNiX2lkICYmIHR5cGVvZiB3W2NiX2lkXSA9PT0gJ2Z1bmN0aW9uJykgPyB3W2NiX2lkXSA6IG51bGw7XHJcblxyXG5cdFx0aWYgKCFuYW1lKSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoJ1dQQkMgfCBtaXNzaW5nIGRhdGEtd3BiYy11LWxvYWQtbmFtZScpO1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblxyXG5cdFx0JChkb2N1bWVudCkudHJpZ2dlcignd3BiYzpvcHRpb246YmVmb3JlTG9hZCcsIFsgJGVsLCBuYW1lIF0pO1xyXG5cdFx0d3BiY191aXhfYnVzeV9vbigkZWwpO1xyXG5cclxuXHRcdCQuYWpheCh7XHJcblx0XHRcdHVybDogIHcud3BiY19vcHRpb25fc2F2ZXJfbG9hZGVyX2NvbmZpZy5hamF4X3VybCxcclxuXHRcdFx0dHlwZTogJ0dFVCcsXHJcblx0XHRcdGRhdGE6IHtcclxuXHRcdFx0XHRhY3Rpb246ICAgIHcud3BiY19vcHRpb25fc2F2ZXJfbG9hZGVyX2NvbmZpZy5hY3Rpb25fbG9hZCxcclxuXHRcdFx0XHRkYXRhX25hbWU6IG5hbWVcclxuXHRcdFx0fVxyXG5cdFx0fSlcclxuXHRcdC5kb25lKGZ1bmN0aW9uIChyZXNwKSB7XHJcblx0XHRcdGlmIChyZXNwICYmIHJlc3Auc3VjY2Vzcykge1xyXG5cdFx0XHRcdGlmIChjYl9mbikge1xyXG5cdFx0XHRcdFx0dHJ5IHsgY2JfZm4ocmVzcC5kYXRhICYmIHJlc3AuZGF0YS52YWx1ZSk7IH0gY2F0Y2ggKGUpIHsgY29uc29sZS5lcnJvcihlKTsgfVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRjb25zb2xlLmVycm9yKCdXUEJDIHwgJyArIChyZXNwICYmIHJlc3AuZGF0YSAmJiByZXNwLmRhdGEubWVzc2FnZSA/IHJlc3AuZGF0YS5tZXNzYWdlIDogJ0xvYWQgZXJyb3InKSk7XHJcblx0XHRcdH1cclxuXHRcdFx0JChkb2N1bWVudCkudHJpZ2dlcignd3BiYzpvcHRpb246YWZ0ZXJMb2FkJywgWyByZXNwIF0pO1xyXG5cdFx0fSlcclxuXHRcdC5mYWlsKGZ1bmN0aW9uICh4aHIpIHtcclxuXHRcdFx0Y29uc29sZS5lcnJvcignV1BCQyB8IEFKQVggJyArIHhoci5zdGF0dXMgKyAnICcgKyB4aHIuc3RhdHVzVGV4dCk7XHJcblx0XHRcdCQoZG9jdW1lbnQpLnRyaWdnZXIoJ3dwYmM6b3B0aW9uOmFmdGVyTG9hZCcsIFsgeyBzdWNjZXNzOiBmYWxzZSwgZGF0YTogeyBtZXNzYWdlOiB4aHIuc3RhdHVzVGV4dCB9IH0gXSk7XHJcblx0XHR9KVxyXG5cdFx0LmFsd2F5cyhmdW5jdGlvbiAoKSB7XHJcblx0XHRcdHdwYmNfdWl4X2J1c3lfb2ZmKCRlbCk7XHJcblx0XHR9KTtcclxuXHR9O1xyXG5cclxufSh3aW5kb3csIGpRdWVyeSkpO1xyXG4iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQyxXQUFVQSxDQUFDLEVBQUVDLENBQUMsRUFBRTtFQUNoQixZQUFZOztFQUVaO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtFQUNDLFNBQVNDLG9CQUFvQkEsQ0FBQ0MsQ0FBQyxFQUFFO0lBQ2hDLE9BQU9DLE1BQU0sQ0FBQ0QsQ0FBQyxDQUFDLENBQ2RFLE9BQU8sQ0FBQyxJQUFJLEVBQUUsT0FBTyxDQUFDLENBQ3RCQSxPQUFPLENBQUMsSUFBSSxFQUFFLE1BQU0sQ0FBQyxDQUNyQkEsT0FBTyxDQUFDLElBQUksRUFBRSxNQUFNLENBQUMsQ0FDckJBLE9BQU8sQ0FBQyxJQUFJLEVBQUUsUUFBUSxDQUFDLENB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
