/* globals window, document */
/**
 * WPBC Slider Range Groups
 *
 * Universal, dependency-free controller that keeps a "range + number" pair in sync:
 *  - number input  (data-wpbc_slider_range_value)
 *  - range slider  (data-wpbc_slider_range_range)
 *  - writer input  (data-wpbc_slider_range_writer) [optional]
 *
 * If writer exists: number/slider update writer and emit 'input' on writer (bubbles).
 * If writer is missing: emits 'input' on the number input.
 * If writer changes externally: updates number/slider.
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_slider_range_group">
 *    <input type="number" data-wpbc_slider_range_value>
 *    <input type="range"  data-wpbc_slider_range_range>
 *    <!-- optional -->
 *    <input type="text" data-wpbc_slider_range_writer style="display:none;">
 *  </div>
 *
 * Performance notes:
 * - MutationObserver is DISABLED by default.
 * - If your UI re-renders and inserts new groups dynamically, call:
 *     WPBC_Slider_Range_AutoInit(); OR instance.refresh();
 *   Or enable observer via: new WPBC_Slider_Range_Groups(root, { enable_observer:true }).init();
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *
 * @version 2026-01-25
 * @since   2026-01-25
 * @file    ../includes/__js/admin/slider_groups/wpbc_range_groups.js
 */
(function (w, d) {
	'use strict';

	// -------------------------------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------------------------------
	function clamp_num(v, min, max) {
		if (typeof min === 'number' && !isNaN(min)) v = Math.max(min, v);
		if (typeof max === 'number' && !isNaN(max)) v = Math.min(max, v);
		return v;
	}

	function parse_float(v) {
		var n = parseFloat(v);
		return isNaN(n) ? null : n;
	}

	function emit_input(el) {
		if (!el) return;
		el.dispatchEvent(new Event('input', { bubbles: true }));
	}

	// -------------------------------------------------------------------------------------------------
	// Controller
	// -------------------------------------------------------------------------------------------------
	class WPBC_Slider_Range_Groups {

		/**
		 * @param {HTMLElement|string} root_el Container (or selector). If omitted, uses document.
		 * @param {Object} [opts={}]
		 */
		constructor(root_el, opts) {
			this.root = root_el
				? ((typeof root_el === 'string') ? d.querySelector(root_el) : root_el)
				: d;

			this.opts = Object.assign({
				// Strict selectors (NO backward compatibility).
				group_selector  : '.wpbc_slider_range_group',
				value_selector  : '[data-wpbc_slider_range_value]',
				range_selector  : '[data-wpbc_slider_range_range]',
				writer_selector : '[data-wpbc_slider_range_writer]',

				// Disabled by default for performance.
				enable_observer     : false,
				observer_debounce_ms: 150
			}, opts || {});

			this._on_input  = this._on_input.bind(this);
			this._on_change = this._on_change.bind(this);

			this._observer    = null;
			this._refresh_tmr = null;
		}

		init() {
			if (!this.root) return this;

			this.root.addEventListener('input',  this._on_input,  true);
			this.root.addEventListener('change', this._on_change, true);

			if (this.opts.enable_observer && w.MutationObserver) {
				this._observer = new MutationObserver(() => { this._debounced_refresh(); });
				this._observer.observe(this.root === d ? d.documentElement : this.root, { childList: true, subtree: true });
			}

			this.refresh();
			return this;
		}

		destroy() {
			if (!this.root) return;

			this.root.removeEventListener('input',  this._on_input,  true);
			this.root.removeEventListener('change', this._on_change, true);

			if (this._observer) {
				this._observer.disconnect();
				this._observer = null;
			}

			if (this._refresh_tmr) {
				clearTimeout(this._refresh_tmr);
				this._refresh_tmr = null;
			}
		}

		refresh() {
			if (!this.root) return;

			var scope  = (this.root === d ? d : this.root);
			var groups = Array.prototype.slice.call(scope.querySelectorAll(this.opts.group_selector));

			for (var i = 0; i < groups.length; i++) {
				this._sync_from_writer(groups[i]);
				this._clamp_to_range(groups[i]);
			}
		}

		// -------------------------------------------------------------------------------------------------
		// Internal
		// -------------------------------------------------------------------------------------------------
		_debounced_refresh() {
			if (this._refresh_tmr) clearTimeout(this._refresh_tmr);
			this._refresh_tmr = setTimeout(() => {
				this._refresh_tmr = null;
				this.refresh();
			}, Number(this.opts.observer_debounce_ms) || 0);
		}

		_find_group(el) {
			return (el && el.closest) ? el.closest(this.opts.group_selector) : null;
		}

		_get_parts(group) {
			if (!group) return null;
			return {
				group : group,
				num   : group.querySelector(this.opts.value_selector),
				range : group.querySelector(this.opts.range_selector),
				writer: group.querySelector(this.opts.writer_selector)
			};
		}

		_write(parts, value, emit) {
			if (!parts) return;

			if (parts.writer) {
				parts.writer.__wpbc_slider_range_internal = true;
				parts.writer.value = String(value);
				if (emit) emit_input(parts.writer);
				parts.writer.__wpbc_slider_range_internal = false;
			} else if (parts.num) {
				// If writer is missing, at least notify via number input.
				if (emit) emit_input(parts.num);
			}
		}

		_sync_from_writer(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.writer) return;

			var raw = String(parts.writer.value || '').trim();
			if (!raw) return;

			if (parts.num)   parts.num.value   = raw;
			if (parts.range) parts.range.value = raw;
		}

		_clamp_to_range(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.range || !parts.num) return;

			var v = parse_float(parts.num.value);
			if (v == null) return;

			var min = Number(parts.range.min);
			var max = Number(parts.range.max);
			var vv  = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

			if (String(vv) !== parts.num.value) parts.num.value = String(vv);
			parts.range.value = String(vv);
		}

		_on_input(ev) {
			var t = ev.target;
			if (!t) return;

			var group = this._find_group(t);
			if (!group) return;

			var parts = this._get_parts(group);
			if (!parts) return;

			// Writer changed externally -> update UI.
			if (parts.writer && t === parts.writer) {
				if (t.__wpbc_slider_range_internal) return;
				this._sync_from_writer(group);
				this._clamp_to_range(group);
				return;
			}

			// Range moved -> update number + writer.
			if (t.matches && t.matches(this.opts.range_selector)) {
				if (parts.num) parts.num.value = t.value;
				this._write(parts, t.value, /*emit*/ true);
				return;
			}

			// Number typed -> update range + writer (clamp by slider bounds).
			if (t.matches && t.matches(this.opts.value_selector)) {
				if (parts.range) {
					var v = parse_float(t.value);
					if (v != null) {
						var min = Number(parts.range.min);
						var max = Number(parts.range.max);
						v = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

						parts.range.value = String(v);
						if (String(v) !== t.value) t.value = String(v);
					}
				}
				this._write(parts, t.value, /*emit*/ true);
			}
		}

		_on_change(ev) {
			// No special "change" handling needed currently; kept for symmetry/future.
		}
	}

	// -------------------------------------------------------------------------------------------------
	// Auto-init
	// -------------------------------------------------------------------------------------------------
	function wpbc_slider_range_groups__auto_init() {
		var ROOT  = '.wpbc_slider_range_groups';
		var nodes = Array.prototype.slice.call(d.querySelectorAll(ROOT))
			.filter(function (n) { return !n.parentElement || !n.parentElement.closest(ROOT); });

		if (!nodes.length) {
			if (!d.__wpbc_slider_range_groups_global_instance) {
				d.__wpbc_slider_range_groups_global_instance = new WPBC_Slider_Range_Groups(d).init();
			}
			return;
		}

		nodes.forEach(function (node) {
			if (node.__wpbc_slider_range_groups_instance) return;
			node.__wpbc_slider_range_groups_instance = new WPBC_Slider_Range_Groups(node).init();
		});
	}

	// Export globals.
	w.WPBC_Slider_Range_Groups   = WPBC_Slider_Range_Groups;
	w.WPBC_Slider_Range_AutoInit = wpbc_slider_range_groups__auto_init;

	if (d.readyState === 'loading') {
		d.addEventListener('DOMContentLoaded', wpbc_slider_range_groups__auto_init, { once: true });
	} else {
		wpbc_slider_range_groups__auto_init();
	}

})(window, document);
