/* globals window, document */
/**
 * WPBC Slider Length Groups
 *
 * Universal, dependency-free controller that keeps a "length" control in sync:
 *  - number input  (data-wpbc_slider_len_value)
 *  - unit select   (data-wpbc_slider_len_unit)
 *  - range slider  (data-wpbc_slider_len_range)
 *  - writer input  (data-wpbc_slider_len_writer)  [optional but recommended]
 *
 * The "writer" stores the combined value like: "100%", "420px", "12.5rem".
 * When number/unit/slider change -> writer updates and emits 'input' (bubbles).
 * When writer is changed externally (apply-from-JSON, etc) -> UI updates.
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_slider_len_group"
 *       data-wpbc_slider_len_bounds_map='{"%":{"min":30,"max":100,"step":1},"px":{"min":300,"max":2000,"step":10}}'
 *       data-wpbc_slider_len_default_unit="%">
 *    <input type="number" data-wpbc_slider_len_value>
 *    <select data-wpbc_slider_len_unit>...</select>
 *    <input type="range" data-wpbc_slider_len_range>
 *    <input type="text" data-wpbc_slider_len_writer style="display:none;">
 *  </div>
 *
 * Performance notes:
 * - MutationObserver is DISABLED by default (prevents performance issues).
 * - If your UI re-renders and inserts new groups dynamically, call:
 *     WPBC_Slider_Len_AutoInit();  OR instance.refresh();
 *   Or enable observer via: new WPBC_Slider_Len_Groups(root, { enable_observer:true }).init();
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *
 * @version 2026-01-25
 * @since   2026-01-25
 * @file    ../includes/__js/admin/slider_groups/wpbc_len_groups.js
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

	function safe_json_parse(str) {
		try {
			return JSON.parse(str);
		} catch (e) {
			return null;
		}
	}

	function parse_len_combined(raw, default_unit) {
		var s = (raw == null) ? '' : String(raw).trim();
		if (!s) return { num: '', unit: default_unit || '%' };

		var m = s.match(/^\s*([\-]?\d+(?:\.\d+)?)\s*([a-z%]*)\s*$/i);
		if (!m) {
			// If it's not parseable, treat as number and keep default unit.
			return { num: s, unit: default_unit || '%' };
		}

		var num  = m[1] ? String(m[1]) : '';
		var unit = m[2] ? String(m[2]) : '';
		if (!unit) unit = default_unit || '%';

		return { num: num, unit: unit };
	}

	function build_combined(num, unit) {
		if (num == null || String(num).trim() === '') return '';
		return String(num) + String(unit || '');
	}

	function emit_input(el) {
		if (!el) return;
		el.dispatchEvent(new Event('input', { bubbles: true }));
	}

	// -------------------------------------------------------------------------------------------------
	// Controller
	// -------------------------------------------------------------------------------------------------
	class WPBC_Slider_Len_Groups {

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
				group_selector  : '.wpbc_slider_len_group',
				value_selector  : '[data-wpbc_slider_len_value]',
				unit_selector   : '[data-wpbc_slider_len_unit]',
				range_selector  : '[data-wpbc_slider_len_range]',
				writer_selector : '[data-wpbc_slider_len_writer]',

				default_unit    : '%',

				fallback_bounds : {
					'px' : { min: 0,   max: 512,  step: 1   },
					'%'  : { min: 0,   max: 100,  step: 1   },
					'rem': { min: 0,   max: 10,   step: 0.1 },
					'em' : { min: 0,   max: 10,   step: 0.1 }
				},

				// Disabled by default for performance.
				enable_observer     : false,
				observer_debounce_ms: 150
			}, opts || {});

			this._on_input  = this._on_input.bind(this);
			this._on_change = this._on_change.bind(this);

			this._bounds_cache = new WeakMap(); // group -> bounds_map_object
			this._observer     = null;
			this._refresh_tmr  = null;
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
				this._sync_group_from_writer(groups[i]);
				this._apply_bounds_for_current_unit(groups[i]);
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
				unit  : group.querySelector(this.opts.unit_selector),
				range : group.querySelector(this.opts.range_selector),
				writer: group.querySelector(this.opts.writer_selector)
			};
		}

		_get_default_unit(group) {
			var du = (group && group.getAttribute)
				? group.getAttribute('data-wpbc_slider_len_default_unit')
				: '';
			return du ? String(du) : this.opts.default_unit;
		}

		_get_bounds_map(group) {
			if (!group) return null;
			if (this._bounds_cache.has(group)) {
				return this._bounds_cache.get(group);
			}

			var raw = group.getAttribute('data-wpbc_slider_len_bounds_map');
			var map = raw ? safe_json_parse(raw) : null;
			if (!map || typeof map !== 'object') map = null;

			this._bounds_cache.set(group, map);
			return map;
		}

		_get_bounds_for_unit(group, unit) {
			var map = this._get_bounds_map(group);
			if (map && unit && map[unit]) {
				return map[unit];
			}
			return this.opts.fallback_bounds[unit] || this.opts.fallback_bounds['px'];
		}

		_apply_bounds(parts, bounds) {
			if (!parts || !bounds) return;

			var min  = (bounds.min  != null) ? Number(bounds.min)  : null;
			var max  = (bounds.max  != null) ? Number(bounds.max)  : null;
			var step = (bounds.step != null) ? Number(bounds.step) : null;

			if (parts.range) {
				if (!isNaN(min))  parts.range.min  = String(min);
				if (!isNaN(max))  parts.range.max  = String(max);
				if (!isNaN(step)) parts.range.step = String(step);
			}
			if (parts.num) {
				if (!isNaN(min))  parts.num.min  = String(min);
				if (!isNaN(max))  parts.num.max  = String(max);
				if (!isNaN(step)) parts.num.step = String(step);
			}
		}

		_apply_bounds_for_current_unit(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.unit) return;

			var unit = parts.unit.value || this._get_default_unit(group);
			var b    = this._get_bounds_for_unit(group, unit);

			this._apply_bounds(parts, b);

			// Clamp current value to new bounds.
			var v = parse_float(parts.num && parts.num.value ? parts.num.value : (parts.range ? parts.range.value : ''));
			if (v == null) return;

			var min = (b && b.min != null) ? Number(b.min) : null;
			var max = (b && b.max != null) ? Number(b.max) : null;
			v = clamp_num(v, isNaN(min) ? null : min, isNaN(max) ? null : max);

			if (parts.num)   parts.num.value   = String(v);
			if (parts.range) parts.range.value = String(v);

			this._write_combined(parts, String(v), unit, /*emit*/ false);
		}

		_write_combined(parts, num, unit, emit) {
			if (!parts) return;

			var combined = build_combined(num, unit);

			if (parts.writer) {
				// Avoid recursion: mark as internal write.
				parts.writer.__wpbc_slider_len_internal = true;
				parts.writer.value = combined;
				if (emit) emit_input(parts.writer);
				parts.writer.__wpbc_slider_len_internal = false;
			} else if (parts.num) {
				// If writer is missing, at least notify via number input.
				if (emit) emit_input(parts.num);
			}
		}

		_sync_group_from_writer(group) {
			var parts = this._get_parts(group);
			if (!parts || !parts.writer) return;

			var raw = String(parts.writer.value || '').trim();
			if (!raw) return;

			var du = this._get_default_unit(group);
			var p  = parse_len_combined(raw, du);

			if (parts.unit)  parts.unit.value  = p.unit;
			if (parts.num)   parts.num.value   = p.num;
			if (parts.range) parts.range.value = p.num;
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
				if (t.__wpbc_slider_len_internal) return;
				this._sync_group_from_writer(group);
				this._apply_bounds_for_current_unit(group);
				return;
			}

			// Slider moved -> update number + writer.
			if (t.matches && t.matches(this.opts.range_selector)) {
				if (parts.num) parts.num.value = t.value;

				var unit = (parts.unit && parts.unit.value) ? parts.unit.value : this._get_default_unit(group);
				this._write_combined(parts, t.value, unit, /*emit*/ true);
				return;
			}

			// Number typed -> update slider + writer (clamp if slider has bounds).
			if (t.matches && t.matches(this.opts.value_selector)) {
				var v = parse_float(t.value);

				if (v != null && parts.range) {
					var rmin = Number(parts.range.min);
					var rmax = Number(parts.range.max);
					v = clamp_num(v, isNaN(rmin) ? null : rmin, isNaN(rmax) ? null : rmax);

					parts.range.value = String(v);
					if (String(v) !== t.value) t.value = String(v);
				}

				var unit2 = (parts.unit && parts.unit.value) ? parts.unit.value : this._get_default_unit(group);
				this._write_combined(parts, t.value, unit2, /*emit*/ true);
			}
		}

		_on_change(ev) {
			var t = ev.target;
			if (!t) return;

			var group = this._find_group(t);
			if (!group) return;

			var parts = this._get_parts(group);
			if (!parts) return;

			// Unit changed -> update bounds + writer.
			if (t.matches && t.matches(this.opts.unit_selector)) {
				this._apply_bounds_for_current_unit(group);

				var num  = parts.num ? parts.num.value : (parts.range ? parts.range.value : '');
				var unit = t.value || this._get_default_unit(group);
				this._write_combined(parts, num, unit, /*emit*/ true);
			}
		}
	}

	// -------------------------------------------------------------------------------------------------
	// Auto-init
	// -------------------------------------------------------------------------------------------------
	function wpbc_slider_len_groups__auto_init() {
		var ROOT  = '.wpbc_slider_len_groups';
		var nodes = Array.prototype.slice.call(d.querySelectorAll(ROOT))
			.filter(function (n) { return !n.parentElement || !n.parentElement.closest(ROOT); });

		// If no explicit containers, install a single document-root instance.
		if (!nodes.length) {
			if (!d.__wpbc_slider_len_groups_global_instance) {
				d.__wpbc_slider_len_groups_global_instance = new WPBC_Slider_Len_Groups(d).init();
			}
			return;
		}

		nodes.forEach(function (node) {
			if (node.__wpbc_slider_len_groups_instance) return;
			node.__wpbc_slider_len_groups_instance = new WPBC_Slider_Len_Groups(node).init();
		});
	}

	// Export globals (manual control if needed).
	w.WPBC_Slider_Len_Groups   = WPBC_Slider_Len_Groups;
	w.WPBC_Slider_Len_AutoInit = wpbc_slider_len_groups__auto_init;

	// DOM-ready auto init.
	if (d.readyState === 'loading') {
		d.addEventListener('DOMContentLoaded', wpbc_slider_len_groups__auto_init, { once: true });
	} else {
		wpbc_slider_len_groups__auto_init();
	}

})(window, document);
