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
		return String(s)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
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
		if (v === true) { return 'On'; }
		if (v === false) { return 'Off'; }
		var s = String(v || '').toLowerCase();
		if (s === 'on' || s === '1' || s === 'true' || s === 'yes') { return 'On'; }
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

		$el.addClass('wpbc-is-busy')
			.attr('aria-disabled', 'true')
			.prop('disabled', true);
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

		$el.removeClass('wpbc-is-busy')
			.removeAttr('aria-disabled')
			.prop('disabled', false);

		$el.removeData('wpbc-uix-busy')
			.removeData('wpbc-uix-original-html');
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
		var nonce        = $el.data('wpbc-u-save-nonce');
		var nonce_action = $el.data('wpbc-u-save-action');
		var data_name    = $el.data('wpbc-u-save-name');

		// Dynamic values MUST prefer attribute read (fresh), fallback to .data().
		var fields_raw   = $el.data('wpbc-u-save-fields') || '';
		var inline_value = wpbc_uix_read_attr_or_data($el, 'data-wpbc-u-save-value', 'wpbc-u-save-value');
		var json         = wpbc_uix_read_attr_or_data($el, 'data-wpbc-u-save-value-json', 'wpbc-u-save-value-json');
		var save_mode    = $el.data('wpbc-u-save-mode') || $el.attr('data-wpbc-u-save-mode') || '';

		// Optional: compute scalar from another control selector at click time.
		var value_from_selector = $el.data('wpbc-u-save-value-from') || $el.attr('data-wpbc-u-save-value-from');

		var cb_id = $el.data('wpbc-u-save-callback');
		var cb_fn = (cb_id && typeof w[cb_id] === 'function') ? w[cb_id] : null;

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

			var fields = String(fields_raw).split(',')
				.map(function (s) { return String(s || '').trim(); })
				.filter(Boolean);

			var data = {};

			fields.forEach(function (sel) {
				var $f = $(sel);
				if (!$f.length) { return; }

				// If selector points to a wrapper, try to locate a real control inside.
				var $control = $f.is('input,select,textarea') ? $f : $f.find('input,select,textarea').first();
				if (!$control.length) { return; }

				var key = $control.attr('name') || $control.attr('id');
				if (!key) { return; }

				data[key] = wpbc_uix_get_control_value($control);
			});

			payload = $.param(data);
		}
		else {
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

		$(document).trigger('wpbc:option:beforeSave', [ $el, payload ]);
		wpbc_uix_busy_on($el);

		$.ajax({
			url:  w.wpbc_option_saver_loader_config.ajax_url,
			type: 'POST',
			data: {
				action:       w.wpbc_option_saver_loader_config.action_save,
				nonce:        nonce,
				nonce_action: nonce_action,
				data_name:    data_name,
				data_value:   payload,

				// Optional: split JSON object into separate options server-side.
				data_mode:    save_mode,

				// Optional allowlist of keys for split mode (CSV).
				data_fields:  (save_mode === 'split' ? String(fields_raw || '') : '')
			}
		})
		.done(function (resp) {

			// NOTE: previously the code always showed "success" even on error.
			// Fixed: show success only when resp.success is true.

			if (resp && resp.success) {

				if (cb_fn) {
					try { cb_fn(resp); } catch (e) { console.error(e); }
				}

				var ok_message = (resp && resp.data && resp.data.message) ? resp.data.message : 'Saved';
				if (typeof w.wpbc_admin_show_message === 'function') {
					w.wpbc_admin_show_message(ok_message, 'success', 1000, false);
				}

			} else {

				var err_message = (resp && resp.data && resp.data.message) ? resp.data.message : 'Save error';
				console.error('WPBC | ' + err_message);

				if (typeof w.wpbc_admin_show_message === 'function') {
					w.wpbc_admin_show_message(err_message, 'error', 30000);
				}
			}

			$(document).trigger('wpbc:option:afterSave', [ resp ]);
		})
		.fail(function (xhr) {
			var feedback_message = 'WPBC | AJAX ' + xhr.status + ' ' + xhr.statusText;
			console.error(feedback_message);

			if (typeof w.wpbc_admin_show_message === 'function') {
				w.wpbc_admin_show_message(feedback_message, 'error', 30000);
			}

			$(document).trigger('wpbc:option:afterSave', [ { success: false, data: { message: xhr.statusText } } ]);
		})
		.always(function () {
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

		var $el  = $(el);
		var name = $el.data('wpbc-u-load-name') || $el.data('wpbc-u-save-name');

		var cb_id = $el.data('wpbc-u-load-callback');
		var cb_fn = (cb_id && typeof w[cb_id] === 'function') ? w[cb_id] : null;

		if (!name) {
			console.error('WPBC | missing data-wpbc-u-load-name');
			return;
		}

		$(document).trigger('wpbc:option:beforeLoad', [ $el, name ]);
		wpbc_uix_busy_on($el);

		$.ajax({
			url:  w.wpbc_option_saver_loader_config.ajax_url,
			type: 'GET',
			data: {
				action:    w.wpbc_option_saver_loader_config.action_load,
				data_name: name
			}
		})
		.done(function (resp) {
			if (resp && resp.success) {
				if (cb_fn) {
					try { cb_fn(resp.data && resp.data.value); } catch (e) { console.error(e); }
				}
			} else {
				console.error('WPBC | ' + (resp && resp.data && resp.data.message ? resp.data.message : 'Load error'));
			}
			$(document).trigger('wpbc:option:afterLoad', [ resp ]);
		})
		.fail(function (xhr) {
			console.error('WPBC | AJAX ' + xhr.status + ' ' + xhr.statusText);
			$(document).trigger('wpbc:option:afterLoad', [ { success: false, data: { message: xhr.statusText } } ]);
		})
		.always(function () {
			wpbc_uix_busy_off($el);
		});
	};

}(window, jQuery));
