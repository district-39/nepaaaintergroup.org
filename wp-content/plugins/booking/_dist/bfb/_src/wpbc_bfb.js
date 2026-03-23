// ---------------------------------------------------------------------------------------------------------------------
// == File  /includes/page-form-builder/_out/core/bfb-core.js == | 2025-09-10 15:47
// ---------------------------------------------------------------------------------------------------------------------
(function ( w ) {
	'use strict';

	// Single global namespace (idempotent & load-order safe).
	const Core = ( w.WPBC_BFB_Core = w.WPBC_BFB_Core || {} );
	const UI   = ( Core.UI = Core.UI || {} );

	/**
	 * Core sanitize/escape/normalize helpers.
	 * All methods use snake_case; camelCase aliases are provided for backwards compatibility.
	 */
	Core.WPBC_BFB_Sanitize = class {

		/**
		 * Escape text for safe use in CSS selectors.
		 * @param {string} s - raw selector fragment
		 * @returns {string}
		 */
		static esc_css(s) {
			return (w.CSS && w.CSS.escape) ? w.CSS.escape( String( s ) ) : String( s ).replace( /([^\w-])/g, '\\$1' );
		}

		/**
		 * Escape a value for attribute selectors, e.g. [data-id="<value>"].
		 * @param {string} v
		 * @returns {string}
		 */
		static esc_attr_value_for_selector(v) {
			return String( v )
				.replace( /\\/g, '\\\\' )
				.replace( /"/g, '\\"' )
				.replace( /\n/g, '\\A ' )
				.replace( /\]/g, '\\]' );
		}

		/**
		 * Sanitize into a broadly compatible HTML id: letters, digits, - _ : . ; must start with a letter.
		 * @param {string} v
		 * @returns {string}
		 */
		static sanitize_html_id(v) {
			let s = (v == null ? '' : String( v )).trim();
			s     = s
				.replace( /\s+/g, '-' )
				.replace( /[^A-Za-z0-9\-_\:.]/g, '-' )
				.replace( /-+/g, '-' )
				.replace( /^[-_.:]+|[-_.:]+$/g, '' );
			if ( !s ) return 'field';
			if ( !/^[A-Za-z]/.test( s ) ) s = 'f-' + s;
			return s;
		}

		/**
		 * Sanitize into a safe HTML name token: letters, digits, _ -
		 * Must start with a letter; no dots/brackets/spaces.
		 * @param {string} v
		 * @returns {string}
		 */
		static sanitize_html_name(v) {

			let s = (v == null ? '' : String( v )).trim();

			s = s.replace( /\s+/g, '_' ).replace( /[^A-Za-z0-9_-]/g, '_' ).replace( /_+/g, '_' );

			if ( ! s ) {
				s = 'field';
			}
			if ( ! /^[A-Za-z]/.test( s ) ) {
				s = 'f_' + s;
			}
			return s;
		}

		/**
		 * Escape for HTML text/attributes (not URLs).
		 * @param {any} v
		 * @returns {string}
		 */
		static escape_html(v) {
			if ( v == null ) {
				return '';
			}
			return String( v )
				.replace( /&/g, '&amp;' )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#039;' )
				.replace( /</g, '&lt;' )
				.replace( />/g, '&gt;' );
		}

		/**
		 * Escape minimal set for attribute-safety without slugging.
		 * Keeps original human text; escapes &, <, >, " and ' only.
		 * @param {string} s
		 * @returns {string}
		 */
		static escape_value_for_attr(s) {
			return String( s == null ? '' : s )
				.replace( /&/g, '&amp;' )
				.replace( /</g, '&lt;' )
				.replace( />/g, '&gt;' )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#39;' );
		}

		/**
		 * Sanitize a space-separated CSS class list.
		 * @param {any} v
		 * @returns {string}
		 */
		static sanitize_css_classlist(v) {
			if ( v == null ) return '';
			return String( v ).replace( /[^\w\- ]+/g, ' ' ).replace( /\s+/g, ' ' ).trim();
		}
// == NEW ==
		/**
		 * Turn an arbitrary value into a conservative "token" (underscores, hyphens allowed).
		 * Useful for shortcode tokens, ids in plain text, etc.
		 * @param {any} v
		 * @returns {string}
		 */
		static to_token(v) {
			return String( v ?? '' )
				.trim()
				.replace( /\s+/g, '_' )
				.replace( /[^A-Za-z0-9_\-]/g, '' );
		}

		/**
		 * Convert to kebab-case (letters, digits, hyphens).
		 * @param {any} v
		 * @returns {string}
		 */
		static to_kebab(v) {
			return String( v ?? '' )
				.trim()
				.replace( /[_\s]+/g, '-' )
				.replace( /[^A-Za-z0-9-]/g, '' )
				.replace( /-+/g, '-' )
				.toLowerCase();
		}

		/**
		 * Truthy normalization for form-like inputs: true, 'true', 1, '1', 'yes', 'on'.
		 * @param {any} v
		 * @returns {boolean}
		 */
		static is_truthy(v) {
			if ( typeof v === 'boolean' ) return v;
			const s = String( v ?? '' ).trim().toLowerCase();
			return s === 'true' || s === '1' || s === 'yes' || s === 'on';
		}

		/**
		 * Coerce to boolean with an optional default for empty values.
		 * @param {any} v
		 * @param {boolean} [def=false]
		 * @returns {boolean}
		 */
		static coerce_boolean(v, def = false) {
			if ( v == null || v === '' ) return def;
			return this.is_truthy( v );
		}

		/**
		 * Parse a "percent-like" value ('33'|'33%'|33) with fallback.
		 * @param {string|number|null|undefined} v
		 * @param {number} fallback_value
		 * @returns {number}
		 */
		static parse_percent(v, fallback_value) {
			if ( v == null ) {
				return fallback_value;
			}
			const s = String( v ).trim();
			const n = parseFloat( s.replace( /%/g, '' ) );
			return Number.isFinite( n ) ? n : fallback_value;
		}

		/**
		 * Clamp a number to the [min, max] range.
		 * @param {number} n
		 * @param {number} min
		 * @param {number} max
		 * @returns {number}
		 */
		static clamp(n, min, max) {
			return Math.max( min, Math.min( max, n ) );
		}

		/**
		 * Escape a value for inclusion inside a quoted HTML attribute (double quotes).
		 * Replaces newlines with spaces and double quotes with single quotes.
		 * @param {any} v
		 * @returns {string}
		 */
		static escape_for_attr_quoted(v) {
			if ( v == null ) return '';
			return String( v ).replace( /\r?\n/g, ' ' ).replace( /"/g, '\'' );
		}

		/**
		 * Escape for shortcode-like tokens where double quotes and newlines should be neutralized.
		 * @param {any} v
		 * @returns {string}
		 */
		static escape_for_shortcode(v) {
			return String( v ?? '' ).replace( /"/g, '\\"' ).replace( /\r?\n/g, ' ' );
		}

		/**
		 * JSON.parse with fallback (no throw).
		 * @param {string} s
		 * @param {any} [fallback=null]
		 * @returns {any}
		 */
		static safe_json_parse(s, fallback = null) {
			try {
				return JSON.parse( s );
			} catch ( _ ) {
				return fallback;
			}
		}

		/**
		 * Stringify data-* attribute value safely (objects -> JSON, others -> String).
		 * @param {any} v
		 * @returns {string}
		 */
		static stringify_data_value(v) {
			if ( typeof v === 'object' && v !== null ) {
				try {
					return JSON.stringify( v );
				} catch {
					console.error( 'WPBC: stringify_data_value' );
					return '';
				}
			}
			return String( v );
		}

		// -------------------------------------------------------------------------------------------------------------
		// Strict value guards for CSS lengths and hex colors (defense-in-depth).
		// -------------------------------------------------------------------------------------------------------------
		/**
		 * Sanitize a CSS length. Allows: px, %, rem, em (lower/upper).
		 * Returns fallback if invalid.
		 * @param {any} v
		 * @param {string} [fallback='100%']
		 * @returns {string}
		 */
		static sanitize_css_len(v, fallback = '100%') {
			const s = String( v ?? '' ).trim();
			const m = s.match( /^(-?\d+(?:\.\d+)?)(px|%|rem|em)$/i );
			return m ? m[0] : String( fallback );
		}

		/**
		 * Sanitize a hex color. Allows #rgb or #rrggbb (case-insensitive).
		 * Returns fallback if invalid.
		 * @param {any} v
		 * @param {string} [fallback='#e0e0e0']
		 * @returns {string}
		 */
		static sanitize_hex_color(v, fallback = '#e0e0e0') {
			const s = String( v ?? '' ).trim();
			return /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test( s ) ? s : String( fallback );
		}

	}

	/**
	 * WPBC ID / Name service. Generates, sanitizes, and ensures uniqueness for field ids/names/html_ids within the canvas.
	 */
	Core.WPBC_BFB_IdService = class  {

		/**
		 * Constructor. Set root container of the form pages.
		 *
		 * @param {HTMLElement} pages_container - Root container of the form pages.
		 */
		constructor( pages_container ) {
			this.pages_container = pages_container;
		}

		/**
		 * Ensure a unique **internal** field id (stored in data-id) within the canvas.
		 * Starts from a desired id (already sanitized or not) and appends suffixes if needed.
		 *
		 * @param {string} baseId - Desired id.
		 * @returns {string} Unique id.
		 */
		ensure_unique_field_id(baseId, currentEl = null) {
			const base    = Core.WPBC_BFB_Sanitize.sanitize_html_id( baseId );
			let id        = base || 'field';
			const esc     = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );
			const escUid  = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );
			const notSelf = currentEl?.dataset?.uid ? `:not([data-uid="${escUid( currentEl.dataset.uid )}"])` : '';
			while ( this.pages_container?.querySelector(
				`.wpbc_bfb__panel--preview .wpbc_bfb__field${notSelf}[data-id="${esc(id)}"], .wpbc_bfb__panel--preview .wpbc_bfb__section${notSelf}[data-id="${esc(id)}"]`
			) ) {
				// Excludes self by data-uid .
				const found = this.pages_container.querySelector( `.wpbc_bfb__panel--preview .wpbc_bfb__field[data-id="${esc( id )}"], .wpbc_bfb__panel--preview .wpbc_bfb__section[data-id="${esc( id )}"]` );
				if ( found && currentEl && found === currentEl ) {
					break;
				}
				id = `${base || 'field'}-${Math.random().toString( 36 ).slice( 2, 5 )}`;
			}
			return id;
		}

		/**
		 * Ensure a unique HTML name across the form.
		 *
		 * @param {string} base - Desired base name (un/sanitized).
		 * @param {HTMLElement|null} currentEl - If provided, ignore conflicts with this element.
		 * @returns {string} Unique name.
		 */
		ensure_unique_field_name(base, currentEl = null) {
			let name      = base || 'field';
			const esc     = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );
			const escUid  = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );
			// Exclude the current field *and any DOM mirrors of it* (same data-uid)
			const uid     = currentEl?.dataset?.uid;
			const notSelf = uid ? `:not([data-uid="${escUid( uid )}"])` : '';
			while ( true ) {
				const selector = `.wpbc_bfb__panel--preview .wpbc_bfb__field${notSelf}[data-name="${esc( name )}"]`;
				const clashes  = this.pages_container?.querySelectorAll( selector ) || [];
				if ( clashes.length === 0 ) break;           // nobody else uses this name
				const m = name.match( /-(\d+)$/ );
				name    = m ? name.replace( /-\d+$/, '-' + (Number( m[1] ) + 1) ) : `${base}-2`;
			}
			return name;
		}

		/**
		 * Set field's INTERNAL id (data-id) on an element. Ensures uniqueness and optionally asks caller to refresh preview.
		 *
		 * @param {HTMLElement} field_el - Field element in the canvas.
		 * @param {string} newIdRaw - Desired id (un/sanitized).
		 * @param {boolean} [renderPreview=false] - Caller can decide to re-render preview.
		 * @returns {string} Applied unique id.
		 */
		set_field_id( field_el, newIdRaw, renderPreview = false ) {
			const desired = Core.WPBC_BFB_Sanitize.sanitize_html_id( newIdRaw );
			const unique  = this.ensure_unique_field_id( desired, field_el );
			field_el.setAttribute( 'data-id', unique );
			if ( renderPreview ) {
				// Caller decides if / when to render.
			}
			return unique;
		}

		/**
		 * Set field's REQUIRED HTML name (data-name). Ensures sanitized + unique per form.
		 * Falls back to sanitized internal id if user provides empty value.
		 *
		 * @param {HTMLElement} field_el - Field element in the canvas.
		 * @param {string} newNameRaw - Desired name (un/sanitized).
		 * @param {boolean} [renderPreview=false] - Caller can decide to re-render preview.
		 * @returns {string} Applied unique name.
		 */
		set_field_name( field_el, newNameRaw, renderPreview = false ) {
			const raw  = (newNameRaw == null ? '' : String( newNameRaw )).trim();
			const base = raw
				? Core.WPBC_BFB_Sanitize.sanitize_html_name( raw )
				: Core.WPBC_BFB_Sanitize.sanitize_html_name( field_el.getAttribute( 'data-id' ) || 'field' );

			const unique = this.ensure_unique_field_name( base, field_el );
			field_el.setAttribute( 'data-name', unique );
			if ( renderPreview ) {
				// Caller decides if / when to render.
			}
			return unique;
		}

		/**
		 * Set field's OPTIONAL public HTML id (data-html_id). Empty value removes the attribute.
		 * Ensures sanitization + uniqueness among other declared HTML ids.
		 *
		 * @param {HTMLElement} field_el - Field element in the canvas.
		 * @param {string} newHtmlIdRaw - Desired html_id (optional).
		 * @param {boolean} [renderPreview=false] - Caller can decide to re-render preview.
		 * @returns {string} The applied html_id or empty string if removed.
		 */
		set_field_html_id( field_el, newHtmlIdRaw, renderPreview = false ) {
			const raw = (newHtmlIdRaw == null ? '' : String( newHtmlIdRaw )).trim();

			if ( raw === '' ) {
				field_el.removeAttribute( 'data-html_id' );
				if ( renderPreview ) {
					// Caller decides if / when to render.
				}
				return '';
			}

			const desired = Core.WPBC_BFB_Sanitize.sanitize_html_id( raw );
			let htmlId    = desired;
			const esc     = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );
			const escUid  = (v) => Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( v );

			while ( true ) {

				const uid     = field_el?.dataset?.uid;
				const notSelf = uid ? `:not([data-uid="${escUid( uid )}"])` : '';

				const clashInCanvas = this.pages_container?.querySelector(
					`.wpbc_bfb__panel--preview .wpbc_bfb__field${notSelf}[data-html_id="${esc( htmlId )}"],` +
					`.wpbc_bfb__panel--preview .wpbc_bfb__section${notSelf}[data-html_id="${esc( htmlId )}"]`
				);
				const domClash = document.getElementById( htmlId );

				// Allow when the only "clash" is inside this same field (e.g., the input you just rendered)
				const domClashIsSelf = domClash === field_el || (domClash && field_el.contains( domClash ));

				if ( !clashInCanvas && (!domClash || domClashIsSelf) ) {
					break;
				}

				const m = htmlId.match( /-(\d+)$/ );
				htmlId  = m ? htmlId.replace( /-\d+$/, '-' + (Number( m[1] ) + 1) ) : `${desired}-2`;
			}

			field_el.setAttribute( 'data-html_id', htmlId );
			if ( renderPreview ) {
				// Caller decides if / when to render.
			}
			return htmlId;
		}
	};

	/**
	 * WPBC Layout service. Encapsulates column width math with gap handling, presets, and utilities.
	 */
	Core.WPBC_BFB_LayoutService = class  {

		/**
		 * Constructor. Set options with gap between columns (%).
		 *
		 * @param {{ col_gap_percent?: number }} [opts] - Options with gap between columns (%).
		 */
		constructor( opts = {} ) {
			this.col_gap_percent = Number.isFinite( +opts.col_gap_percent ) ? +opts.col_gap_percent : 3;
		}

		/**
		 * Compute normalized flex-basis values for a row, respecting column gaps.
		 * Returns bases that sum to available = 100 - (n-1)*gap.
		 *
		 * @param {HTMLElement} row_el - Row element containing .wpbc_bfb__column children.
		 * @param {number} [gap_percent=this.col_gap_percent] - Gap percent between columns.
		 * @returns {{available:number,bases:number[]}} Available space and basis values.
		 */
		compute_effective_bases_from_row( row_el, gap_percent = this.col_gap_percent ) {
			const cols = Array.from( row_el?.querySelectorAll( ':scope > .wpbc_bfb__column' ) || [] );
			const n    = cols.length || 1;

			const raw = cols.map( ( col ) => {
				const w = col.style.flexBasis || '';
				const p = Core.WPBC_BFB_Sanitize.parse_percent( w, NaN );
				return Number.isFinite( p ) ? p : (100 / n);
			} );

			const sum_raw    = raw.reduce( ( a, b ) => a + b, 0 ) || 100;
			const gp         = Number.isFinite( +gap_percent ) ? +gap_percent : 3;
			const total_gaps = Math.max( 0, n - 1 ) * gp;
			const available  = Math.max( 0, 100 - total_gaps );
			const scale      = available / sum_raw;

			return {
				available,
				bases: raw.map( ( p ) => Math.max( 0, p * scale ) )
			};
		}

		/**
		 * Apply computed bases to the row's columns (sets flex-basis %).
		 *
		 * @param {HTMLElement} row_el - Row element.
		 * @param {number[]} bases - Array of basis values (percent of full 100).
		 * @returns {void}
		 */
		apply_bases_to_row( row_el, bases ) {
			const cols = Array.from( row_el?.querySelectorAll( ':scope > .wpbc_bfb__column' ) || [] );
			cols.forEach( ( col, i ) => {
				const p             = bases[i] ?? 0;
				col.style.flexBasis = `${p}%`;
			} );
		}

		/**
		 * Distribute columns evenly, respecting gap.
		 *
		 * @param {HTMLElement} row_el - Row element.
		 * @param {number} [gap_percent=this.col_gap_percent] - Gap percent.
		 * @returns {void}
		 */
		set_equal_bases( row_el, gap_percent = this.col_gap_percent ) {
			const cols       = Array.from( row_el?.querySelectorAll( ':scope > .wpbc_bfb__column' ) || [] );
			const n          = cols.length || 1;
			const gp         = Number.isFinite( +gap_percent ) ? +gap_percent : 3;
			const total_gaps = Math.max( 0, n - 1 ) * gp;
			const available  = Math.max( 0, 100 - total_gaps );
			const each       = available / n;
			this.apply_bases_to_row( row_el, Array( n ).fill( each ) );
		}

		/**
		 * Apply a preset of relative weights to a row/section.
		 *
		 * @param {HTMLElement} sectionOrRow - .wpbc_bfb__section or its child .wpbc_bfb__row.
		 * @param {number[]} weights - Relative weights (e.g., [1,3,1]).
		 * @param {number} [gap_percent=this.col_gap_percent] - Gap percent.
		 * @returns {void}
		 */
		apply_layout_preset( sectionOrRow, weights, gap_percent = this.col_gap_percent ) {
			const row = sectionOrRow?.classList?.contains( 'wpbc_bfb__row' )
				? sectionOrRow
				: sectionOrRow?.querySelector( ':scope > .wpbc_bfb__row' );

			if ( ! row ) {
				return;
			}

			const cols = Array.from( row.querySelectorAll( ':scope > .wpbc_bfb__column' ) || [] );
			const n    = cols.length || 1;

			if ( ! Array.isArray( weights ) || weights.length !== n ) {
				this.set_equal_bases( row, gap_percent );
				return;
			}

			const sum       = weights.reduce( ( a, b ) => a + Math.max( 0, Number( b ) || 0 ), 0 ) || 1;
			const gp        = Number.isFinite( +gap_percent ) ? +gap_percent : 3;
			const available = Math.max( 0, 100 - Math.max( 0, n - 1 ) * gp );
			const bases     = weights.map( ( w ) => Math.max( 0, (Number( w ) || 0) / sum * available ) );

			this.apply_bases_to_row( row, bases );
		}

		/**
		 * Build preset weight lists for a given column count.
		 *
		 * @param {number} n - Column count.
		 * @returns {number[][]} List of weight arrays.
		 */
		build_presets_for_columns( n ) {
			switch ( n ) {
				case 1:
					return [ [ 1 ] ];
				case 2:
					return [ [ 1, 2 ], [ 2, 1 ], [ 1, 3 ], [ 3, 1 ] ];
				case 3:
					return [ [ 1, 3, 1 ], [ 1, 2, 1 ], [ 2, 1, 1 ], [ 1, 1, 2 ] ];
				case 4:
					return [ [ 1, 2, 2, 1 ], [ 2, 1, 1, 1 ], [ 1, 1, 1, 2 ] ];
				default:
					return [ Array( n ).fill( 1 ) ];
			}
		}

		/**
		 * Format a human-readable label like "50%/25%/25%" from weights.
		 *
		 * @param {number[]} weights - Weight list.
		 * @returns {string} Label string.
		 */
		format_preset_label( weights ) {
			const sum = weights.reduce( ( a, b ) => a + (Number( b ) || 0), 0 ) || 1;
			return weights.map( ( w ) => Math.round( ((Number( w ) || 0) / sum) * 100 ) ).join( '%/' ) + '%';
		}

		/**
		 * Parse comma/space separated weights into numbers.
		 *
		 * @param {string} input - User input like "20,60,20".
		 * @returns {number[]} Parsed weights.
		 */
		parse_weights( input ) {
			if ( ! input ) {
				return [];
			}
			return String( input )
				.replace( /[^\d,.\s]/g, '' )
				.split( /[\s,]+/ )
				.map( ( s ) => parseFloat( s ) )
				.filter( ( n ) => Number.isFinite( n ) && n >= 0 );
		}
	};

	/**
	 * WPBC Usage Limit service.
	 * Counts field usage by key, compares to palette limits, and updates palette UI.
	 */
	Core.WPBC_BFB_UsageLimitService = class  {

		/**
		 * Constructor. Set pages_container and palette_ul.
		 *
		 * @param {HTMLElement} pages_container - Canvas root that holds placed fields.
		 * @param {HTMLElement[]|null} palette_uls?:   Palettes UL with .wpbc_bfb__field items (may be null).
		 */
		constructor(pages_container, palette_uls) {
			this.pages_container = pages_container;
			// Normalize to an array; we’ll still be robust if none provided.
			this.palette_uls     = Array.isArray( palette_uls ) ? palette_uls : (palette_uls ? [ palette_uls ] : []);
		}


		/**
		 * Parse usage limit from raw dataset value. Missing/invalid -> Infinity.
		 *
		 * @param {string|number|null|undefined} raw - Raw attribute value.
		 * @returns {number} Limit number or Infinity.
		 */
		static parse_usage_limit( raw ) {
			if ( raw == null ) {
				return Infinity;
			}
			const n = parseInt( raw, 10 );
			return Number.isFinite( n ) ? n : Infinity;
		}

		/**
		 * Count how many instances exist per usage_key in the canvas.
		 *
		 * @returns {Record<string, number>} Map of usage_key -> count.
		 */
		count_usage_by_key() {
			const used = {};
			const all  = this.pages_container?.querySelectorAll( '.wpbc_bfb__panel--preview .wpbc_bfb__field:not(.is-invalid)' ) || [];
			all.forEach( ( el ) => {
				const key = el.dataset.usage_key || el.dataset.type || el.dataset.id;
				if ( ! key ) {
					return;
				}
				used[key] = (used[key] || 0) + 1;
			} );
			return used;
		}

		/**
		 * Return palette limit for a given usage key (id of the palette item).
		 *
		 * @param {string} key - Usage key.
		 * @returns {number} Limit value or Infinity.
		 */
		get_limit_for_key(key) {
			if ( ! key ) {
				return Infinity;
			}
			// Query across all palettes present now (stored + any newly added in DOM).
			const roots            = this.palette_uls?.length ? this.palette_uls : document.querySelectorAll( '.wpbc_bfb__panel_field_types__ul' );
			const allPaletteFields = Array.from( roots ).flatMap( r => Array.from( r.querySelectorAll( '.wpbc_bfb__field' ) ) );
			let limit              = Infinity;

			allPaletteFields.forEach( (el) => {
				if ( el.dataset.id === key ) {
					const n = Core.WPBC_BFB_UsageLimitService.parse_usage_limit( el.dataset.usagenumber );
					// Choose the smallest finite limit (safest if palettes disagree).
					if ( n < limit ) {
						limit = n;
					}
				}
			} );

			return limit;
		}


		/**
		 * Disable/enable palette items based on current usage counts and limits.
		 *
		 * @returns {void}
		 */
		update_palette_ui() {
			// Always compute usage from the canvas:
			const usage = this.count_usage_by_key();

			// Update all palettes currently in DOM (not just the initially captured ones)
			const palettes = document.querySelectorAll( '.wpbc_bfb__panel_field_types__ul' );

			palettes.forEach( (pal) => {
				pal.querySelectorAll( '.wpbc_bfb__field' ).forEach( (panel_field) => {
					const paletteId   = panel_field.dataset.id;
					const raw_limit   = panel_field.dataset.usagenumber;
					const perElLimit  = Core.WPBC_BFB_UsageLimitService.parse_usage_limit( raw_limit );
					// Effective limit across all palettes is the global limit for this key.
					const globalLimit = this.get_limit_for_key( paletteId );
					const limit       = Number.isFinite( globalLimit ) ? globalLimit : perElLimit; // prefer global min

					const current = usage[paletteId] || 0;
					const disable = Number.isFinite( limit ) && current >= limit;

					panel_field.style.pointerEvents = disable ? 'none' : '';
					panel_field.style.opacity       = disable ? '0.4' : '';
					panel_field.setAttribute( 'aria-disabled', disable ? 'true' : 'false' );
					if ( disable ) {
						panel_field.setAttribute( 'tabindex', '-1' );
					} else {
						panel_field.removeAttribute( 'tabindex' );
					}
				} );
			} );
		}


		/**
		 * Return how many valid instances with this usage key exist in the canvas.
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @returns {number} Count of existing non-invalid instances.
		 */
		count_for_key( key ) {
			if ( ! key ) {
				return 0;
			}
			return ( this.pages_container?.querySelectorAll(
                `.wpbc_bfb__panel--preview .wpbc_bfb__field[data-usage_key="${Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( key )}"]:not(.is-invalid), 
                 .wpbc_bfb__panel--preview .wpbc_bfb__field[data-type="${Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( key )}"]:not(.is-invalid)`
			) || [] ).length;
		}

		/**
		 * Alias for limit lookup (readability).
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @returns {number} Limit value or Infinity.
		 */
		limit_for_key( key ) {
			return this.get_limit_for_key( key );
		}

		/**
		 * Remaining slots for this key (Infinity if unlimited).
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @returns {number} Remaining count (>= 0) or Infinity.
		 */
		remaining_for_key( key ) {
			const limit = this.limit_for_key( key );
			if ( limit === Infinity ) {
				return Infinity;
			}
			const used = this.count_for_key( key );
			return Math.max( 0, limit - used );
		}

		/**
		 * True if you can add `delta` more items for this key.
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @param {number} [delta=1] - How many items you intend to add.
		 * @returns {boolean} Whether adding is allowed.
		 */
		can_add( key, delta = 1 ) {
			const rem = this.remaining_for_key( key );
			return ( rem === Infinity ) ? true : ( rem >= delta );
		}

		/**
		 * UI-facing gate: alert when exceeded. Returns boolean allowed/blocked.
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @param {{label?: string, delta?: number}} [opts={}] - Optional UI info.
		 * @returns {boolean} True if allowed, false if blocked.
		 */
		gate_or_alert( key, { label = key, delta = 1 } = {} ) {
			if ( this.can_add( key, delta ) ) {
				return true;
			}
			const limit = this.limit_for_key( key );
			alert( `Only ${limit} instance${limit > 1 ? 's' : ''} of "${label}" allowed.` );
			return false;
		}

		/**
		 * Backward-compatible alias used elsewhere in the codebase.  - Check whether another instance with the given usage key can be added.
		 *
		 * @param {string} key - Usage key of a palette item.
		 * @returns {boolean} Whether adding one more is allowed.
		 */
		is_usage_ok( key ) {
			return this.can_add( key, 1 );
		}

	};

	/**
	 * Constant event names for the builder.
	 */
	Core.WPBC_BFB_Events = Object.freeze({
		SELECT            : 'wpbc:bfb:select',
		CLEAR_SELECTION   : 'wpbc:bfb:clear-selection',
		FIELD_ADD         : 'wpbc:bfb:field:add',
		FIELD_REMOVE      : 'wpbc:bfb:field:remove',
		STRUCTURE_CHANGE  : 'wpbc:bfb:structure:change',
		STRUCTURE_LOADED  : 'wpbc:bfb:structure:loaded'
	});

	/**
	 * Lightweight event bus that emits to both the pages container and document.
	 */
	Core.WPBC_BFB_EventBus =  class {
		/**
		 * @param {HTMLElement} scope_el - Element to dispatch bubbled events from.
		 */
		constructor( scope_el ) {
			this.scope_el = scope_el;
		}

		/**
		 * Emit a DOM CustomEvent with payload.
		 *
		 * @param {string} type - Event type (use Core.WPBC_BFB_Events. when possible).
		 * @param {Object} [detail={}] - Arbitrary serializable payload.
		 * @returns {void}
		 */
		emit( type, detail = {} ) {
			if ( ! this.scope_el ) {
				return;
			}
			this.scope_el.dispatchEvent( new CustomEvent( type, { detail: { ...detail }, bubbles: true } ) );
		}

		/**
		 * Subscribe to an event on document.
		 *
		 * @param {string} type - Event type.
		 * @param {(ev:CustomEvent)=>void} handler - Handler function.
		 * @returns {void}
		 */
		on( type, handler ) {
			document.addEventListener( type, handler );
		}

		/**
		 * Unsubscribe from an event on document.
		 *
		 * @param {string} type - Event type.
		 * @param {(ev:CustomEvent)=>void} handler - Handler function.
		 * @returns {void}
		 */
		off( type, handler ) {
			document.removeEventListener( type, handler );
		}
	};

	/**
	 * SortableJS manager: single point for consistent DnD config.
	 */
	Core.WPBC_BFB_SortableManager = class  {

		/**
		 * @param {WPBC_Form_Builder} builder - The active builder instance.
		 * @param {{ groupName?: string, animation?: number, ghostClass?: string, chosenClass?: string, dragClass?: string }} [opts={}] - Visual/behavior options.
		 */
		constructor( builder, opts = {} ) {
			this.builder = builder;
			const gid = this.builder?.instance_id || Math.random().toString( 36 ).slice( 2, 8 );
			this.opts = {
				// groupName  : 'form',
				groupName: `form-${gid}`,
				animation  : 150,
				ghostClass : 'wpbc_bfb__drag-ghost',
				chosenClass: 'wpbc_bfb__highlight',
				dragClass  : 'wpbc_bfb__drag-active',
				...opts
			};
			/** @type {Set<HTMLElement>} */
			this._containers = new Set();
		}

		/**
		 * Tag the drag mirror (element under cursor) with role: 'palette' | 'canvas'.
		 * Works with Sortable's fallback mirror (.sortable-fallback / .sortable-drag) and with your dragClass (.wpbc_bfb__drag-active).
		 */
		_tag_drag_mirror( evt ) {
			const fromPalette = this.builder?.palette_uls?.includes?.( evt.from );
			const role        = fromPalette ? 'palette' : 'canvas';
			// Wait a tick so the mirror exists.  - The window.requestAnimationFrame() method tells the browser you wish to perform an animation.
			requestAnimationFrame( () => {
				const mirror = document.querySelector( '.sortable-fallback, .sortable-drag, .' + this.opts.dragClass );
				if ( mirror ) {
					mirror.setAttribute( 'data-drag-role', role );
				}
			} );
		}

		_toggle_dnd_root_flags( active, from_palette = false ) {

			// set to root element of an HTML document, which is the <html>.
			const root = document.documentElement;
			if ( active ) {
				root.classList.add( 'wpbc_bfb__dnd-active' );
				if ( from_palette ) {
					root.classList.add( 'wpbc_bfb__drag-from-palette' );
				}
			} else {
				root.classList.remove( 'wpbc_bfb__dnd-active', 'wpbc_bfb__drag-from-palette' );
			}
		}


		/**
		 * Ensure Sortable is attached to a container with role 'palette' or 'canvas'.
		 *
		 *  -- Handle selectors: handle:  '.section-drag-handle, .wpbc_bfb__drag-handle, .wpbc_bfb__drag-anywhere, [data-draggable="true"]'
		 *  -- Draggable gate: draggable: '.wpbc_bfb__field:not([data-draggable="false"]), .wpbc_bfb__section'
		 *  -- Filter (overlay-safe):     ignore everything in overlay except the handle -  '.wpbc_bfb__overlay-controls *:not(.wpbc_bfb__drag-handle):not(.section-drag-handle):not(.wpbc_icn_drag_indicator)'
		 *  -- No-drag wrapper:           use .wpbc_bfb__no-drag-zone inside renderers for inputs/widgets.
		 *  -- Focus guard (optional):    flip [data-draggable] on focusin/focusout to prevent accidental drags while typing.
		 *
		 * @param {HTMLElement} container - The element to enhance with Sortable.
		 * @param {'palette'|'canvas'} role - Behavior profile to apply.
		 * @param {{ onAdd?: Function }} [handlers={}] - Optional handlers.
		 * @returns {void}
		 */
		ensure( container, role, handlers = {} ) {
			if ( ! container || typeof Sortable === 'undefined' ) {
				return;
			}
			if ( Sortable.get?.( container ) ) {
				return;
			}

			const common = {
				animation  : this.opts.animation,
				ghostClass : this.opts.ghostClass,
				chosenClass: this.opts.chosenClass,
				dragClass  : this.opts.dragClass,
				// == Element under the cursor  == Ensure we drag a real DOM mirror you can style via CSS (cross-browser).
				forceFallback    : true,
				fallbackOnBody   : true,
				fallbackTolerance: 6,
				// Add body/html flags so you can style differently when dragging from palette.
				onStart: (evt) => {
					this.builder?._add_dragging_class?.();

					const fromPalette = this.builder?.palette_uls?.includes?.( evt.from );
					this._toggle_dnd_root_flags( true, fromPalette );  // set to root HTML document: html.wpbc_bfb__dnd-active.wpbc_bfb__drag-from-palette .

					this._tag_drag_mirror( evt );                      // Add 'data-drag-role' attribute to  element under cursor.
				},
				onEnd  : () => {
					setTimeout( () => { this.builder._remove_dragging_class(); }, 50 );
					this._toggle_dnd_root_flags( false );
				}
			};

			if ( role === 'palette' ) {
				Sortable.create( container, {
					...common,
					group   : { name: this.opts.groupName, pull: 'clone', put: false },
					sort    : false
				} );
				this._containers.add( container );
				return;
			}

			// role === 'canvas'.
			Sortable.create( container, {
				...common,
				group    : {
					name: this.opts.groupName,
					pull: true,
					put : (to, from, draggedEl) => {
						return draggedEl.classList.contains( 'wpbc_bfb__field' ) ||
							   draggedEl.classList.contains( 'wpbc_bfb__section' );
					}
				},
				// ---------- DnD Handlers --------------                // Grab anywhere on fields that opt-in with the class or attribute.  - Sections still require their dedicated handle.
				handle   : '.section-drag-handle, .wpbc_bfb__drag-handle, .wpbc_bfb__drag-anywhere, [data-draggable="true"]',
				draggable: '.wpbc_bfb__field:not([data-draggable="false"]), .wpbc_bfb__section',                        // Per-field opt-out with [data-draggable="false"] (e.g., while editing).
				// ---------- Filters - No DnD ----------                // Declarative “no-drag zones”: anything inside these wrappers won’t start a drag.
				filter: [
					'.wpbc_bfb__no-drag-zone',
					'.wpbc_bfb__no-drag-zone *',
					'.wpbc_bfb__column-resizer',  // Ignore the resizer rails during DnD (prevents edge “snap”).
					                              // In the overlay toolbar, block everything EXCEPT the drag handle (and its icon).
					'.wpbc_bfb__overlay-controls *:not(.wpbc_bfb__drag-handle):not(.section-drag-handle):not(.wpbc_icn_drag_indicator)'
				].join( ',' ),
				preventOnFilter  : false,
					// ---------- anti-jitter tuning ----------
				direction            : 'vertical',           // columns are vertical lists.
				invertSwap           : true,                 // use swap on inverted overlap.
				swapThreshold        : 0.65,                 // be less eager to swap.
				invertedSwapThreshold: 0.85,                 // require deeper overlap when inverted.
				emptyInsertThreshold : 24,                   // don’t jump into empty containers too early.
				dragoverBubble       : false,                // keep dragover local.
				fallbackOnBody       : true,                 // more stable positioning.
				fallbackTolerance    : 6,                    // Reduce micro-moves when the mouse shakes a bit (esp. on touchpads).
				scroll               : true,
				scrollSensitivity    : 40,
				scrollSpeed          : 10,
				/**
				 * Enter/leave hysteresis for cross-column moves.    Only allow dropping into `to` when the pointer is well inside it.
				 */
				onMove: (evt, originalEvent) => {
					const { to, from } = evt;
					if ( !to || !from ) return true;

					// Only gate columns (not page containers), and only for cross-column moves in the same row
					const isColumn = to.classList?.contains( 'wpbc_bfb__column' );
					if ( !isColumn ) return true;

					const fromRow = from.closest( '.wpbc_bfb__row' );
					const toRow   = to.closest( '.wpbc_bfb__row' );
					if ( fromRow && toRow && fromRow !== toRow ) return true;

					const rect = to.getBoundingClientRect();
					const evtX = (originalEvent.touches?.[0]?.clientX) ?? originalEvent.clientX;
					const evtY = (originalEvent.touches?.[0]?.clientY) ?? originalEvent.clientY;

					// --- Edge fence (like you had), but clamped for tiny columns
					const paddingX = Core.WPBC_BFB_Sanitize.clamp( rect.width * 0.20, 12, 36 );
					const paddingY = Core.WPBC_BFB_Sanitize.clamp( rect.height * 0.10, 6, 16 );

					// Looser Y if the column is visually tiny/empty
					const isVisuallyEmpty = to.childElementCount === 0 || rect.height < 64;
					const innerTop        = rect.top + (isVisuallyEmpty ? 4 : paddingY);
					const innerBottom     = rect.bottom - (isVisuallyEmpty ? 4 : paddingY);
					const innerLeft       = rect.left + paddingX;
					const innerRight      = rect.right - paddingX;

					const insideX = evtX > innerLeft && evtX < innerRight;
					const insideY = evtY > innerTop && evtY < innerBottom;
					if ( !(insideX && insideY) ) return false;   // stay in current column until well inside new one

					// --- Sticky target commit distance: only switch if we’re clearly inside the new column
					const ds = this._dragState;
					if ( ds ) {
						if ( ds.stickyTo && ds.stickyTo !== to ) {
							// require a deeper penetration to switch columns
							const commitX = Core.WPBC_BFB_Sanitize.clamp( rect.width * 0.25, 18, 40 );   // 25% or 18–40px
							const commitY = Core.WPBC_BFB_Sanitize.clamp( rect.height * 0.15, 10, 28 );  // 15% or 10–28px

							const deepInside =
									  (evtX > rect.left + commitX && evtX < rect.right - commitX) &&
									  (evtY > rect.top + commitY && evtY < rect.bottom - commitY);

							if ( !deepInside ) return false;
						}
						// We accept the new target now.
						ds.stickyTo     = to;
						ds.lastSwitchTs = performance.now();
					}

					return true;
				},
				onStart: (evt) => {
					this.builder?._add_dragging_class?.();
					// Match the flags we set in common so CSS stays consistent on canvas drags too.
					const fromPalette = this.builder?.palette_uls?.includes?.( evt.from );
					this._toggle_dnd_root_flags( true, fromPalette );          // set to root HTML document: html.wpbc_bfb__dnd-active.wpbc_bfb__drag-from-palette .
					this._tag_drag_mirror( evt );                             // Tag the mirror under cursor.
					this._dragState = { stickyTo: null, lastSwitchTs: 0 };    // per-drag state.
				},
				onEnd  : () => {
					setTimeout( () => { this.builder._remove_dragging_class(); }, 50 );
					this._toggle_dnd_root_flags( false );                    // set to root HTML document without these classes: html.wpbc_bfb__dnd-active.wpbc_bfb__drag-from-palette .
					this._dragState = null;
				},
				// ----------------------------------------
				// onAdd: handlers.onAdd || this.builder.handle_on_add.bind( this.builder )
				onAdd: (evt) => {
					if ( this._on_add_section( evt ) ) {
						return;
					}
					// Fallback: original handler for normal fields.
					(handlers.onAdd || this.builder.handle_on_add.bind( this.builder ))( evt );
				},
				onUpdate: () => {
					this.builder.bus?.emit?.( Core.WPBC_BFB_Events.STRUCTURE_CHANGE, { reason: 'sort-update' } );
				}
			} );

			this._containers.add( container );
		}

		/**
		 * Handle adding/moving sections via Sortable onAdd.
		 * Returns true if handled (i.e., it was a section), false to let the default field handler run.
		 *
		 * - Palette -> canvas: remove the placeholder clone and build a fresh section via add_section()
		 * - Canvas -> canvas: keep the moved DOM (and its children), just re-wire overlays/sortables/metadata
		 *
		 * @param {Sortable.SortableEvent} evt
		 * @returns {boolean}
		 */
		_on_add_section(evt) {

			const item = evt.item;
			if ( ! item ) {
				return false;
			}

			// Identify sections both from palette items (li clones) and real canvas nodes.
			const data      = Core.WPBC_Form_Builder_Helper.get_all_data_attributes( item );
			const isSection = item.classList.contains( 'wpbc_bfb__section' ) || (data?.type || item.dataset?.type) === 'section';

			if ( ! isSection ) {
				return false;
			}

			const fromPalette = this.builder?.palette_uls?.includes?.( evt.from ) === true;

			if ( ! fromPalette ) {
				// Canvas -> canvas move: DO NOT rebuild/remove; preserve children.
				this.builder.add_overlay_toolbar?.( item );                       // ensure overlay exists
				this.builder.pages_sections?.init_all_nested_sortables?.( item ); // ensure inner sortables

				// Ensure metadata present/updated
				item.dataset.type    = 'section';
				const cols           = item.querySelectorAll( ':scope > .wpbc_bfb__row > .wpbc_bfb__column' ).length || 1;
				item.dataset.columns = String( cols );

				// Select & notify subscribers (layout/min guards, etc.)
				this.builder.select_field?.( item );
				this.builder.bus?.emit?.( Core.WPBC_BFB_Events.STRUCTURE_CHANGE, { el: item, reason: 'section-move' } );
				this.builder.usage?.update_palette_ui?.();
				return true; // handled.
			}

			// Palette -> canvas: build a brand-new section using the same path as the dropdown/menu
			const to   = evt.to?.closest?.( '.wpbc_bfb__column, .wpbc_bfb__form_preview_section_container' ) || evt.to;
			const cols = parseInt( data?.columns || item.dataset.columns || 1, 10 ) || 1;

			// Remove the palette clone placeholder.
			item.parentNode && item.parentNode.removeChild( item );

			// Create the real section.
			this.builder.pages_sections.add_section( to, cols );

			// Insert at the precise drop index.
			const section = to.lastElementChild; // add_section appends to end.
			if ( evt.newIndex != null && evt.newIndex < to.children.length - 1 ) {
				const ref = to.children[evt.newIndex] || null;
				to.insertBefore( section, ref );
			}

			// Finalize: overlay, selection, events, usage refresh.
			this.builder.add_overlay_toolbar?.( section );
			this.builder.select_field?.( section );
			this.builder.bus?.emit?.( Core.WPBC_BFB_Events.FIELD_ADD, {
				el : section,
				id : section.dataset.id,
				uid: section.dataset.uid
			} );
			this.builder.usage?.update_palette_ui?.();

			return true;
		}

		/**
		 * Destroy all Sortable instances created by this manager.
		 *
		 * @returns {void}
		 */
		destroyAll() {
			this._containers.forEach( ( el ) => {
				const inst = Sortable.get?.( el );
				if ( inst ) {
					inst.destroy();
				}
			} );
			this._containers.clear();
		}
	};

	/**
	 * Small DOM contract and renderer helper
	 *
	 * @type {Readonly<{
	 *                  SELECTORS: {pagePanel: string, field: string, validField: string, section: string, column: string, row: string, overlay: string},
	 *                  CLASSES: {selected: string},
	 *        	        ATTR: {id: string, name: string, htmlId: string, usageKey: string, uid: string}}
	 *        >}
	 */
	Core.WPBC_BFB_DOM = Object.freeze( {
		SELECTORS: {
			pagePanel : '.wpbc_bfb__panel--preview',
			field     : '.wpbc_bfb__field',
			validField: '.wpbc_bfb__field:not(.is-invalid)',
			section   : '.wpbc_bfb__section',
			column    : '.wpbc_bfb__column',
			row       : '.wpbc_bfb__row',
			overlay   : '.wpbc_bfb__overlay-controls'
		},
		CLASSES  : {
			selected: 'is-selected'
		},
		ATTR     : {
			id      : 'data-id',
			name    : 'data-name',
			htmlId  : 'data-html_id',
			usageKey: 'data-usage_key',
			uid     : 'data-uid'
		}
	} );

	Core.WPBC_Form_Builder_Helper = class {

		/**
		 * Create an HTML element.
		 *
		 * @param {string} tag - HTML tag name.
		 * @param {string} [class_name=''] - Optional CSS class name.
		 * @param {string} [inner_html=''] - Optional innerHTML.
		 * @returns {HTMLElement} Created element.
		 */
		static create_element( tag, class_name = '', inner_html = '' ) {
			const el = document.createElement( tag );
			if ( class_name ) {
				el.className = class_name;
			}
			if ( inner_html ) {
				el.innerHTML = inner_html;
			}
			return el;
		}

		/**
		 * Set multiple `data-*` attributes on a given element.
		 *
		 * @param {HTMLElement} el - Target element.
		 * @param {Object} data_obj - Key-value pairs for data attributes.
		 * @returns {void}
		 */
		static set_data_attributes( el, data_obj ) {
			Object.entries( data_obj ).forEach( ( [ key, val ] ) => {
				// Previously: 2025-09-01 17:09:
				// const value = (typeof val === 'object') ? JSON.stringify( val ) : val;
				//New:
				let value;
				if ( typeof val === 'object' && val !== null ) {
					try {
						value = JSON.stringify( val );
					} catch {
						value = '';
					}
				} else {
					value = val;
				}

				el.setAttribute( 'data-' + key, value );
			} );
		}

		/**
		 * Get all `data-*` attributes from an element and parse JSON where possible.
		 *
		 * @param {HTMLElement} el - Element to extract data from.
		 * @returns {Object} Parsed key-value map of data attributes.
		 */
		static get_all_data_attributes( el ) {
			const data = {};

			if ( ! el || ! el.attributes ) {
				return data;
			}

			Array.from( el.attributes ).forEach(
				( attr ) => {
					if ( attr.name.startsWith( 'data-' ) ) {
						const key = attr.name.replace( /^data-/, '' );
						try {
							data[key] = JSON.parse( attr.value );
						} catch ( e ) {
							data[key] = attr.value;
						}
					}
				}
			);

			// Only default the label if it's truly absent (undefined/null), not when it's an empty string.
			const hasExplicitLabel = Object.prototype.hasOwnProperty.call( data, 'label' );
			if ( ! hasExplicitLabel && data.id ) {
				data.label = data.id.charAt( 0 ).toUpperCase() + data.id.slice( 1 );
			}

			return data;
		}

		/**
		 * Render a simple label + type preview (used for unknown or fallback fields).
		 *
		 * @param {Object} field_data - Field data object.
		 * @returns {string} HTML content.
		 */
		static render_field_inner_html( field_data ) {
			// Make the fallback preview respect an empty label.
			const hasLabel = Object.prototype.hasOwnProperty.call( field_data, 'label' );
			const label    = hasLabel ? String( field_data.label ) : String( field_data.id || '(no label)' );

			const type        = String( field_data.type || 'unknown' );
			const is_required = field_data.required === true || field_data.required === 'true' || field_data.required === 1 || field_data.required === '1';

			const wrapper = document.createElement( 'div' );

			const spanLabel       = document.createElement( 'span' );
			spanLabel.className   = 'wpbc_bfb__field-label';
			spanLabel.textContent = label + (is_required ? ' *' : '');
			wrapper.appendChild( spanLabel );

			const spanType       = document.createElement( 'span' );
			spanType.className   = 'wpbc_bfb__field-type';
			spanType.textContent = type;
			wrapper.appendChild( spanType );

			return wrapper.innerHTML;
		}

		/**
		 * Debounce a function.
		 *
		 * @param {Function} fn - Function to debounce.
		 * @param {number} wait - Delay in ms.
		 * @returns {Function} Debounced function.
		 */
		static debounce( fn, wait = 120 ) {
			let t = null;
			return function debounced( ...args ) {
				if ( t ) {
					clearTimeout( t );
				}
				t = setTimeout( () => fn.apply( this, args ), wait );
			};
		}

	};

	// Renderer registry. Allows late registration and avoids tight coupling to a global map.
	Core.WPBC_BFB_Field_Renderer_Registry = (function () {
		const map = new Map();
		return {
			register( type, ClassRef ) {
				map.set( String( type ), ClassRef );
			},
			get( type ) {
				return map.get( String( type ) );
			}
		};
	})();

}( window ));
// ---------------------------------------------------------------------------------------------------------------------
// == File  /includes/page-form-builder/_out/core/bfb-fields.js == | 2025-09-10 15:47
// ---------------------------------------------------------------------------------------------------------------------
(function ( w ) {
	'use strict';

	// Single global namespace (idempotent & load-order safe).
	const Core = ( w.WPBC_BFB_Core = w.WPBC_BFB_Core || {} );
	const UI   = ( Core.UI = Core.UI || {} );

	/**
	 * Base class for field renderers (static-only contract).
	 * ================================================================================================================
	 * Contract exposed to the builder (static methods on the CLASS itself):
	 *   - render(el, data, ctx)              // REQUIRED
	 *   - on_field_drop(data, el, meta)      // OPTIONAL (default provided)
	 *
	 * Helpers for subclasses:
	 *   - get_defaults()     -> per-field defaults (MUST override in subclass to set type/label)
	 *   - normalize_data(d)  -> shallow merge with defaults
	 *   - get_template(id)   -> per-id cached wp.template compiler
	 *
	 * Subclass usage:
	 *   class WPBC_BFB_Field_Text extends Core.WPBC_BFB_Field_Base { static get_defaults(){ ... } }
	 *   WPBC_BFB_Field_Text.template_id = 'wpbc-bfb-field-text';
	 * ================================================================================================================
	 */
	Core.WPBC_BFB_Field_Base = class {

		/**
		 * Default field data (generic baseline).
		 * Subclasses MUST override to provide { type, label } appropriate for the field.
		 * @returns {Object}
		 */
		static get_defaults() {
			return {
				type        : 'field',
				label       : 'Field',
				name        : 'field',
				html_id     : '',
				placeholder : '',
				required    : false,
				minlength   : '',
				maxlength   : '',
				pattern     : '',
				cssclass    : '',
				help        : ''
			};
		}

		/**
		 * Shallow-merge incoming data with defaults.
		 * @param {Object} data
		 * @returns {Object}
		 */
		static normalize_data( data ) {
			var d        = data || {};
			var defaults = this.get_defaults();
			var out      = {};
			var k;

			for ( k in defaults ) {
				if ( Object.prototype.hasOwnProperty.call( defaults, k ) ) {
					out[k] = defaults[k];
				}
			}
			for ( k in d ) {
				if ( Object.prototype.hasOwnProperty.call( d, k ) ) {
					out[k] = d[k];
				}
			}
			return out;
		}

		/**
		 * Compile and cache a wp.template by id (per-id cache).
		 * @param {string} template_id
		 * @returns {Function|null}
		 */
		static get_template(template_id) {

			// Accept either "wpbc-bfb-field-text" or "tmpl-wpbc-bfb-field-text".
			if ( ! template_id || ! window.wp || ! wp.template ) {
				return null;
			}
			const domId = template_id.startsWith( 'tmpl-' ) ? template_id : ('tmpl-' + template_id);
			if ( ! document.getElementById( domId ) ) {
				return null;
			}

			if ( ! Core.__bfb_tpl_cache_map ) {
				Core.__bfb_tpl_cache_map = {};
			}

			// Normalize id for the compiler & cache. // wp.template expects id WITHOUT the "tmpl-" prefix !
			const key = template_id.replace( /^tmpl-/, '' );
			if ( Core.__bfb_tpl_cache_map[key] ) {
				return Core.__bfb_tpl_cache_map[key];
			}

			const compiler = wp.template( key );     // <-- normalized id here
			if ( compiler ) {
				Core.__bfb_tpl_cache_map[key] = compiler;
			}

			return compiler;
		}

		/**
		 * REQUIRED: render preview into host element (full redraw; idempotent).
		 * Subclasses should set static `template_id` to a valid wp.template id.
		 * @param {HTMLElement} el
		 * @param {Object}      data
		 * @param {{mode?:string,builder?:any,tpl?:Function,sanit?:any}} ctx
		 * @returns {void}
		 */
		static render( el, data, ctx ) {
			if ( ! el ) {
				return;
			}

			var compile = this.get_template( this.template_id );
			var d       = this.normalize_data( data );

			var s = (ctx && ctx.sanit) ? ctx.sanit : Core.WPBC_BFB_Sanitize;

			// Sanitize critical attributes before templating.
			if ( s ) {
				d.html_id = d.html_id ? s.sanitize_html_id( String( d.html_id ) ) : '';
				d.name    = s.sanitize_html_name( String( d.name || d.id || 'field' ) );
			} else {
				d.html_id = d.html_id ? String( d.html_id ) : '';
				d.name    = String( d.name || d.id || 'field' );
			}

			// Fall back to generic preview if template not available.
			if ( compile ) {
				el.innerHTML = compile( d );

				// After render, set attribute values via DOM so quotes/newlines are handled correctly.
				const input = el.querySelector( 'input, textarea, select' );
				if ( input ) {
					if ( d.placeholder != null ) input.setAttribute( 'placeholder', String( d.placeholder ) );
					if ( d.title != null ) input.setAttribute( 'title', String( d.title ) );
				}

			} else {
				el.innerHTML = Core.WPBC_Form_Builder_Helper.render_field_inner_html( d );
			}

			el.dataset.type = d.type || 'field';
			el.setAttribute( 'data-label', (d.label != null ? String( d.label ) : '') ); // allow "".
		}


		/**
		 * OPTIONAL hook executed after field is dropped/loaded/preview.
		 * Default extended:
		 * - On first drop: stamp default label (existing behavior) and mark field as "fresh" for auto-name.
		 * - On load: mark as loaded so later label edits do not rename the saved name.
		 */
		static on_field_drop(data, el, meta) {

			const context = (meta && meta.context) ? String( meta.context ) : '';

			// -----------------------------------------------------------------------------------------
			// NEW: Seed default "help" (and keep it in Structure) for all field packs that define it.
			// This fixes the mismatch where:
			//   - UI shows default help via normalize_data() / templates
			//   - but get_structure() / exporters see `help` as undefined/empty.
			//
			// Behavior:
			//   - Runs ONLY on initial drop (context === 'drop').
			//   - If get_defaults() exposes a non-empty "help", and data.help is
			//     missing / null / empty string -> we persist the default into `data`
			//     and notify Structure so exports see it.
			//   - On "load" we do nothing, so existing forms where user *cleared*
			//     help will not be overridden.
			// -----------------------------------------------------------------------------------------
			if ( context === 'drop' && data ) {
				try {
					const defs = (typeof this.get_defaults === 'function') ? this.get_defaults() : null;
					if ( defs && Object.prototype.hasOwnProperty.call( defs, 'help' ) ) {
						const current    = Object.prototype.hasOwnProperty.call( data, 'help' ) ? data.help : undefined;
						const hasValue   = (current !== undefined && current !== null && String( current ) !== '');
						const defaultVal = defs.help;

						if ( ! hasValue && defaultVal != null && String( defaultVal ) !== '' ) {
							// 1) persist into data object (used by Structure).
							data.help = defaultVal;

							// 2) mirror into dataset (for any DOM-based consumers).
							if ( el ) {
								el.dataset.help = String( defaultVal );

								// 3) notify Structure / listeners (if available).
								try {
									Core.Structure?.update_field_prop?.( el, 'help', defaultVal );
									el.dispatchEvent(
										new CustomEvent( 'wpbc_bfb_field_data_changed', { bubbles: true, detail : { key: 'help', value: defaultVal } } )
									);
								} catch ( _inner ) {}
							}
						}
					}
				} catch ( _e ) {}
			}
			// -----------------------------------------------------------------------------------------

			if ( context === 'drop' && !Object.prototype.hasOwnProperty.call( data, 'label' ) ) {
				const defs = this.get_defaults();
				data.label = defs.label || 'Field';
				el.setAttribute( 'data-label', data.label );
			}
			// Mark provenance flags.
			if ( context === 'drop' ) {
				el.dataset.fresh      = '1';   // can auto-name on first label edit.
				el.dataset.autoname   = '1';
				el.dataset.was_loaded = '0';
				// Seed a provisional unique name immediately.
				try {
					const b = meta?.builder;
					if ( b?.id && (!el.hasAttribute( 'data-name' ) || !el.getAttribute( 'data-name' )) ) {
						const S    = Core.WPBC_BFB_Sanitize;
						const base = S.sanitize_html_name( el.getAttribute( 'data-id' ) || data?.id || data?.type || 'field' );
						const uniq = b.id.ensure_unique_field_name( base, el );
						el.setAttribute( 'data-name', uniq );
						el.dataset.name_user_touched = '0';
					}
				} catch ( _ ) {}

			} else if ( context === 'load' ) {
				el.dataset.fresh      = '0';
				el.dataset.autoname   = '0';
				el.dataset.was_loaded = '1';   // never rename names for loaded fields.
			}
		}

		// --- Auto Rename "Fresh" field,  on entering the new Label ---

		/**
		 * Create a conservative field "name" from a human label.
		 * Uses the same constraints as sanitize_html_name (letters/digits/_- and leading letter).
		 */
		static name_from_label(label) {
			const s = Core.WPBC_BFB_Sanitize.sanitize_html_name( String( label ?? '' ) );
			return s.toLowerCase() || 'field';
		}

		/**
		 * Auto-fill data-name from label ONLY for freshly dropped fields that were not edited yet.
		 * - Never runs for sections.
		 * - Never runs for loaded/existing fields.
		 * - Stops as soon as user edits the Name manually.
		 *
		 * @param {WPBC_Form_Builder} builder
		 * @param {HTMLElement} el  - .wpbc_bfb__field element
		 * @param {string} labelVal
		 */
		static maybe_autoname_from_label(builder, el, labelVal) {
			if ( !builder || !el ) return;
			if ( el.classList.contains( 'wpbc_bfb__section' ) ) return;

			const allowAuto = el.dataset.autoname === '1';

			const userTouched = el.dataset.name_user_touched === '1';
			const isLoaded    = el.dataset.was_loaded === '1';

			if ( !allowAuto || userTouched || isLoaded ) return;

			// Only override placeholder-y names
			const S = Core.WPBC_BFB_Sanitize;

			const base   = this.name_from_label( labelVal );
			const unique = builder.id.ensure_unique_field_name( base, el );
			el.setAttribute( 'data-name', unique );

			const ins      = document.getElementById( 'wpbc_bfb__inspector' );
			const nameCtrl = ins?.querySelector( '[data-inspector-key="name"]' );
			if ( nameCtrl && 'value' in nameCtrl && nameCtrl.value !== unique ) nameCtrl.value = unique;
		}


	};

	/**
	 * Select_Base (shared base for select-like packs)
	 *
	 * @type {Core.WPBC_BFB_Select_Base}
	 */
	Core.WPBC_BFB_Select_Base = class extends Core.WPBC_BFB_Field_Base {

		static template_id            = null;                 // main preview template id
		static option_row_template_id = 'wpbc-bfb-inspector-select-option-row'; // row tpl id
		static kind                   = 'select';
		static __root_wired           = false;
		static __root_node            = null;

		// Single source of selectors used by the inspector UI.
		static ui = {
			list   : '.wpbc_bfb__options_list',
			holder : '.wpbc_bfb__options_state[data-inspector-key="options"]',
			row    : '.wpbc_bfb__options_row',
			label  : '.wpbc_bfb__opt-label',
			value  : '.wpbc_bfb__opt-value',
			toggle : '.wpbc_bfb__opt-selected-chk',
			add_btn: '.js-add-option',

			drag_handle      : '.wpbc_bfb__drag-handle',
			multiple_chk     : '.js-opt-multiple[data-inspector-key="multiple"]',
			default_text     : '.js-default-value[data-inspector-key="default_value"]',
			placeholder_input: '.js-placeholder[data-inspector-key="placeholder"]',
			placeholder_note : '.js-placeholder-note',
			size_input       : '.inspector__input[data-inspector-key="size"]',

			// Dropdown menu integration.
			menu_root  : '.wpbc_ui_el__dropdown',
			menu_toggle: '[data-toggle="wpbc_dropdown"]',
			menu_action: '.ul_dropdown_menu_li_action[data-action]',
			// Value-differs toggle.
			value_differs_chk: '.js-value-differs[data-inspector-key="value_differs"]',
		};

		/**
		 * Build option value from label.
		 * - If `differs === true` -> generate token (slug-like machine value).
		 * - If `differs === false` -> keep human text; escape only dangerous chars.
		 * @param {string} label
		 * @param {boolean} differs
		 * @returns {string}
		 */
		static build_value_from_label(label, differs) {
			const S = Core.WPBC_BFB_Sanitize;
			if ( differs ) {
				return (S && typeof S.to_token === 'function')
					? S.to_token( String( label || '' ) )
					: String( label || '' ).trim().toLowerCase().replace( /\s+/g, '_' ).replace( /[^\w-]/g, '' );
			}
			// single-input mode: keep human text; template will escape safely.
			return String( label == null ? '' : label );
		}

		/**
		 * Is the “value differs from label” toggle enabled?
		 * @param {HTMLElement} panel
		 * @returns {boolean}
		 */
		static is_value_differs_enabled(panel) {
			const chk = panel?.querySelector( this.ui.value_differs_chk );
			return !!(chk && chk.checked);
		}

		/**
		 * Ensure visibility/enabled state of Value inputs based on the toggle.
		 * When disabled -> hide Value inputs and keep them mirrored from Label.
		 * @param {HTMLElement} panel
		 * @returns {void}
		 */
		static sync_value_inputs_visibility(panel) {
			const differs = this.is_value_differs_enabled( panel );
			const rows    = panel?.querySelectorAll( this.ui.row ) || [];

			for ( let i = 0; i < rows.length; i++ ) {
				const r      = rows[i];
				const lbl_in = r.querySelector( this.ui.label );
				const val_in = r.querySelector( this.ui.value );
				if ( !val_in ) continue;

				if ( differs ) {
					// Re-enable & show value input
					val_in.removeAttribute( 'disabled' );
					val_in.style.display = '';

					// If we have a cached custom value and the row wasn't edited while OFF, restore it
					const hasCache   = !!val_in.dataset.cached_value;
					const userEdited = r.dataset.value_user_touched === '1';

					if ( hasCache && !userEdited ) {
						val_in.value = val_in.dataset.cached_value;
					} else if ( !hasCache ) {
						// No cache: if value is just a mirrored label, offer a tokenized default
						const lbl      = lbl_in ? lbl_in.value : '';
						const mirrored = this.build_value_from_label( lbl, /*differs=*/false );
						if ( val_in.value === mirrored ) {
							val_in.value = this.build_value_from_label( lbl, /*differs=*/true );
						}
					}
				} else {
					// ON -> OFF: cache once, then mirror
					if ( !val_in.dataset.cached_value ) {
						val_in.dataset.cached_value = val_in.value || '';
					}
					const lbl    = lbl_in ? lbl_in.value : '';
					val_in.value = this.build_value_from_label( lbl, /*differs=*/false );

					val_in.setAttribute( 'disabled', 'disabled' );
					val_in.style.display = 'none';
					// NOTE: do NOT mark as user_touched here
				}
			}
		}


		/**
		 * Return whether this row’s value has been edited by user.
		 * @param {HTMLElement} row
		 * @returns {boolean}
		 */
		static is_row_value_user_touched(row) {
			return row?.dataset?.value_user_touched === '1';
		}

		/**
		 * Mark this row’s value as edited by user.
		 * @param {HTMLElement} row
		 */
		static mark_row_value_user_touched(row) {
			if ( row ) row.dataset.value_user_touched = '1';
		}

		/**
		 * Initialize “freshness” flags on a row (value untouched).
		 * Call on creation/append of rows.
		 * @param {HTMLElement} row
		 */
		static init_row_fresh_flags(row) {
			if ( row ) {
				if ( !row.dataset.value_user_touched ) {
					row.dataset.value_user_touched = '0';
				}
			}
		}

		// ---- defaults (packs can override) ----
		static get_defaults() {
			return {
				type         : this.kind,
				label        : 'Select',
				name         : '',
				html_id      : '',
				placeholder  : '--- Select ---',
				required     : false,
				multiple     : false,
				size         : null,
				cssclass     : '',
				help         : '',
				default_value: '',
				options      : [
					{ label: 'Option 1', value: 'Option 1', selected: false },
					{ label: 'Option 2', value: 'Option 2', selected: false },
					{ label: 'Option 3', value: 'Option 3', selected: false },
					{ label: 'Option 4', value: 'Option 4', selected: false }
				],
				min_width    : '240px'
			};
		}

		// ---- preview render (idempotent) ----
		static render(el, data, ctx) {
			if ( !el ) return;

			const d = this.normalize_data( data );

			if ( d.min_width != null ) {
				el.dataset.min_width = String( d.min_width );
				try {
					el.style.setProperty( '--wpbc-col-min', String( d.min_width ) );
				} catch ( _ ) {
				}
			}
			if ( d.html_id != null ) el.dataset.html_id = String( d.html_id || '' );
			if ( d.cssclass != null ) el.dataset.cssclass = String( d.cssclass || '' );
			if ( d.placeholder != null ) el.dataset.placeholder = String( d.placeholder || '' );

			const tpl = this.get_template( this.template_id );
			if ( typeof tpl !== 'function' ) {
				el.innerHTML = '<div class="wpbc_bfb__error" role="alert">Template not found: ' + this.template_id + '.</div>';
				return;
			}

			try {
				el.innerHTML = tpl( d );
			} catch ( e ) {
				window._wpbc?.dev?.error?.( 'Select_Base.render', e );
				el.innerHTML = '<div class="wpbc_bfb__error" role="alert">Error rendering field preview.</div>';
				return;
			}

			el.dataset.type = d.type || this.kind;
			el.setAttribute( 'data-label', (d.label != null ? String( d.label ) : '') );

			try {
				Core.UI?.WPBC_BFB_Overlay?.ensure?.( ctx?.builder, el );
			} catch ( _ ) {
			}

			if ( !el.dataset.options && Array.isArray( d.options ) && d.options.length ) {
				try {
					el.dataset.options = JSON.stringify( d.options );
				} catch ( _ ) {
				}
			}
		}

		// ---- drop seeding (options + placeholder) ----
		static on_field_drop(data, el, meta) {
			try {
				super.on_field_drop?.( data, el, meta );
			} catch ( _ ) {
			}

			const is_drop = (meta && meta.context === 'drop');

			if ( is_drop ) {
				if ( !Array.isArray( data.options ) || !data.options.length ) {
					const opts   = (this.get_defaults().options || []).map( (o) => ({
						label   : o.label,
						value   : o.value,
						selected: !!o.selected
					}) );
					data.options = opts;
					try {
						el.dataset.options = JSON.stringify( opts );
						el.dispatchEvent( new CustomEvent( 'wpbc_bfb_field_data_changed', { bubbles: true,
							detail                                                                 : {
								key  : 'options',
								value: opts
							}
						} ) );
						Core.Structure?.update_field_prop?.( el, 'options', opts );
					} catch ( _ ) {
					}
				}

				const ph = (data.placeholder ?? '').toString().trim();
				if ( !ph ) {
					const dflt       = this.get_defaults().placeholder || '--- Select ---';
					data.placeholder = dflt;
					try {
						el.dataset.placeholder = String( dflt );
						el.dispatchEvent( new CustomEvent( 'wpbc_bfb_field_data_changed', { bubbles: true,
							detail                                                                 : {
								key  : 'placeholder',
								value: dflt
							}
						} ) );
						Core.Structure?.update_field_prop?.( el, 'placeholder', dflt );
					} catch ( _ ) {
					}
				}
			}
		}

		// ==============================
		// Inspector helpers (snake_case)
		// ==============================
		static get_panel_root(el) {
			return el?.closest?.( '.wpbc_bfb__inspector__body' ) || el?.closest?.( '.wpbc_bfb__inspector' ) || null;
		}

		static get_list(panel) {
			return panel ? panel.querySelector( this.ui.list ) : null;
		}

		static get_holder(panel) {
			return panel ? panel.querySelector( this.ui.holder ) : null;
		}

		static make_uid() {
			return 'wpbc_ins_auto_opt_' + Math.random().toString( 36 ).slice( 2, 10 );
		}

		static append_row(panel, data) {
			const list = this.get_list( panel );
			if ( !list ) return;

			const idx  = list.children.length;
			const rowd = Object.assign( { label: '', value: '', selected: false, index: idx }, (data || {}) );
			if ( !rowd.uid ) rowd.uid = this.make_uid();

			const tpl_id = this.option_row_template_id;
			const tpl    = (window.wp && wp.template) ? wp.template( tpl_id ) : null;
			const html   = tpl ? tpl( rowd ) : null;

			// In append_row() -> fallback HTML.
			const wrap     = document.createElement( 'div' );
			wrap.innerHTML = html || (
				'<div class="wpbc_bfb__options_row" data-index="' + (rowd.index || 0) + '">' +
					'<span class="wpbc_bfb__drag-handle"><span class="wpbc_icn_drag_indicator"></span></span>' +
					'<input type="text" class="wpbc_bfb__opt-label" placeholder="Label" value="' + (rowd.label || '') + '">' +
					'<input type="text" class="wpbc_bfb__opt-value" placeholder="Value" value="' + (rowd.value || '') + '">' +
					'<div class="wpbc_bfb__opt-selected">' +
						'<div class="inspector__control wpbc_ui__toggle">' +
							'<input type="checkbox" class="wpbc_bfb__opt-selected-chk inspector__input" id="' + rowd.uid + '" role="switch" ' + (rowd.selected ? 'checked aria-checked="true"' : 'aria-checked="false"') + '>' +
							'<label class="wpbc_ui__toggle_icon_radio" for="' + rowd.uid + '"></label>' +
							'<label class="wpbc_ui__toggle_label" for="' + rowd.uid + '">Default</label>' +
						'</div>' +
					'</div>' +
					// 3-dot dropdown (uses existing plugin dropdown JS).
					'<div class="wpbc_ui_el wpbc_ui_el_container wpbc_ui_el__dropdown">' +
						'<a href="javascript:void(0)" data-toggle="wpbc_dropdown" aria-expanded="false" class="ul_dropdown_menu_toggle">' +
							'<i class="menu_icon icon-1x wpbc_icn_more_vert"></i>' +
						'</a>' +
						'<ul class="ul_dropdown_menu" role="menu" style="right:0px; left:auto;">' +
							'<li>' +
								'<a class="ul_dropdown_menu_li_action" data-action="add_after" href="javascript:void(0)">' +
									'Add New' +
									'<i class="menu_icon icon-1x wpbc_icn_add_circle"></i>' +
								'</a>' +
							'</li>' +
							'<li>' +
								'<a class="ul_dropdown_menu_li_action" data-action="duplicate" href="javascript:void(0)">' +
									'Duplicate' +
									'<i class="menu_icon icon-1x wpbc_icn_content_copy"></i>' +
								'</a>' +
							'</li>' +
							'<li class="divider"></li>' +
							'<li>' +
								'<a class="ul_dropdown_menu_li_action" data-action="remove" href="javascript:void(0)">' +
									'Remove' +
									'<i class="menu_icon icon-1x wpbc_icn_delete_outline"></i>' +
								'</a>' +
							'</li>' +
						'</ul>' +
					'</div>' +
				'</div>'
			);

			const node = wrap.firstElementChild;
			 if (! node) {
				 return;
			 }
			// pre-hide Value input if toggle is OFF **before** appending.
			const differs = this.is_value_differs_enabled( panel );
			const valIn   = node.querySelector( this.ui.value );
			const lblIn   = node.querySelector( this.ui.label );

			if ( !differs && valIn ) {
				if ( !valIn.dataset.cached_value ) {
					valIn.dataset.cached_value = valIn.value || '';
				}
				if ( lblIn ) valIn.value = this.build_value_from_label( lblIn.value, false );
				valIn.setAttribute( 'disabled', 'disabled' );
				valIn.style.display = 'none';
			}


			this.init_row_fresh_flags( node );
			list.appendChild( node );

			// Keep your existing post-append sync as a safety net
			this.sync_value_inputs_visibility( panel );
		}

		static close_dropdown(anchor_el) {
			try {
				var root = anchor_el?.closest?.( this.ui.menu_root );
				if ( root ) {
					// If your dropdown toggler toggles a class like 'open', close it.
					root.classList.remove( 'open' );
					// Or if it relies on aria-expanded on the toggle.
					var t = root.querySelector( this.ui.menu_toggle );
					if ( t ) {
						t.setAttribute( 'aria-expanded', 'false' );
					}
				}
			} catch ( _ ) { }
		}

		static insert_after(new_node, ref_node) {
			if ( ref_node?.parentNode ) {
				if ( ref_node.nextSibling ) {
					ref_node.parentNode.insertBefore( new_node, ref_node.nextSibling );
				} else {
					ref_node.parentNode.appendChild( new_node );
				}
			}
		}

		static commit_options(panel) {
			const list   = this.get_list( panel );
			const holder = this.get_holder( panel );
			if ( !list || !holder ) return;

			const differs = this.is_value_differs_enabled( panel );

			const rows    = list.querySelectorAll( this.ui.row );
			const options = [];
			for ( let i = 0; i < rows.length; i++ ) {
				const r      = rows[i];
				const lbl_in = r.querySelector( this.ui.label );
				const val_in = r.querySelector( this.ui.value );
				const chk    = r.querySelector( this.ui.toggle );

				const lbl = (lbl_in && lbl_in.value) || '';
				let val   = (val_in && val_in.value) || '';

				// If single-input mode -> hard mirror to label.
				if ( ! differs ) {
					// single-input mode: mirror Label, minimal escaping (no slug).
					val = this.build_value_from_label( lbl, /*differs=*/false );
					if ( val_in ) {
						val_in.value = val;   // keep hidden input in sync for any previews/debug.
					}
				}

				const sel = !!(chk && chk.checked);
				options.push( { label: lbl, value: val, selected: sel } );
			}

			try {
				holder.value = JSON.stringify( options );
				holder.dispatchEvent( new Event( 'input', { bubbles: true } ) );
				holder.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				panel.dispatchEvent( new CustomEvent( 'wpbc_bfb_field_data_changed', {
					bubbles: true, detail: {
						key: 'options', value: options
					}
				} ) );
			} catch ( _ ) {
			}

			this.sync_default_value_lock( panel );
			this.sync_placeholder_lock( panel );

			// Mirror to the selected field element so canvas/export sees current options immediately.
			const field = panel.__selectbase_field
				|| document.querySelector( '.wpbc_bfb__field.is-selected, .wpbc_bfb__field--selected' );
			if ( field ) {
				try {
					field.dataset.options = JSON.stringify( options );
				} catch ( _ ) {
				}
				Core.Structure?.update_field_prop?.( field, 'options', options );
				field.dispatchEvent( new CustomEvent( 'wpbc_bfb_field_data_changed', {
					bubbles: true, detail: { key: 'options', value: options }
				} ) );
			}
		}


		static ensure_sortable(panel) {
			const list = this.get_list( panel );
			if ( !list || list.dataset.sortable_init === '1' ) return;
			if ( window.Sortable?.create ) {
				try {
					window.Sortable.create( list, {
						handle   : this.ui.drag_handle,
						animation: 120,
						onSort   : () => this.commit_options( panel )
					} );
					list.dataset.sortable_init = '1';
				} catch ( e ) {
					window._wpbc?.dev?.error?.( 'Select_Base.ensure_sortable', e );
				}
			}
		}

		static rebuild_if_empty(panel) {
			const list   = this.get_list( panel );
			const holder = this.get_holder( panel );
			if ( !list || !holder || list.children.length ) return;

			let data = [];
			try {
				data = JSON.parse( holder.value || '[]' );
			} catch ( _ ) {
				data = [];
			}

			if ( !Array.isArray( data ) || !data.length ) {
				data = (this.get_defaults().options || []).slice( 0 );
				try {
					holder.value = JSON.stringify( data );
					holder.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					holder.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				} catch ( _ ) {
				}
			}

			for ( let i = 0; i < data.length; i++ ) {
				this.append_row( panel, {
					label   : data[i]?.label || '',
					value   : data[i]?.value || '',
					selected: !!data[i]?.selected,
					index   : i,
					uid     : this.make_uid()
				} );
			}

			this.sync_default_value_lock( panel );
			this.sync_placeholder_lock( panel );
			this.sync_value_inputs_visibility( panel );
		}

		static has_row_defaults(panel) {
			const checks = panel?.querySelectorAll( this.ui.toggle );
			if ( !checks?.length ) return false;
			for ( let i = 0; i < checks.length; i++ ) if ( checks[i].checked ) return true;
			return false;
		}

		static is_multiple_enabled(panel) {
			const chk = panel?.querySelector( this.ui.multiple_chk );
			return !!(chk && chk.checked);
		}

		static has_text_default_value(panel) {
			const dv = panel?.querySelector( this.ui.default_text );
			return !!(dv && String( dv.value || '' ).trim().length);
		}

		static sync_default_value_lock(panel) {
			const input = panel?.querySelector( this.ui.default_text );
			const note  = panel?.querySelector( '.js-default-value-note' );
			if ( !input ) return;

			const lock     = this.has_row_defaults( panel );
			input.disabled = !!lock;
			if ( lock ) {
				input.setAttribute( 'aria-disabled', 'true' );
				if ( note ) note.style.display = '';
			} else {
				input.removeAttribute( 'aria-disabled' );
				if ( note ) note.style.display = 'none';
			}
		}

		static sync_placeholder_lock(panel) {
			const input = panel?.querySelector( this.ui.placeholder_input );
			const note  = panel?.querySelector( this.ui.placeholder_note );

			// NEW: compute multiple and toggle row visibility
			const isMultiple     = this.is_multiple_enabled( panel );
			const placeholderRow = input?.closest( '.inspector__row' ) || null;
			const sizeInput      = panel?.querySelector( this.ui.size_input ) || null;
			const sizeRow        = sizeInput?.closest( '.inspector__row' ) || null;

			// Show placeholder only for single-select; show size only for multiple
			if ( placeholderRow ) placeholderRow.style.display = isMultiple ? 'none' : '';
			if ( sizeRow ) sizeRow.style.display = isMultiple ? '' : 'none';

			// Existing behavior (keep as-is)
			if ( !input ) return;

			const lock = isMultiple || this.has_row_defaults( panel ) || this.has_text_default_value( panel );
			if ( note && !note.id ) note.id = 'wpbc_placeholder_note_' + Math.random().toString( 36 ).slice( 2, 10 );

			input.disabled = !!lock;
			if ( lock ) {
				input.setAttribute( 'aria-disabled', 'true' );
				if ( note ) {
					note.style.display = '';
					input.setAttribute( 'aria-describedby', note.id );
				}
			} else {
				input.removeAttribute( 'aria-disabled' );
				input.removeAttribute( 'aria-describedby' );
				if ( note ) note.style.display = 'none';
			}
		}

		static enforce_single_default(panel, clicked) {
			if ( this.is_multiple_enabled( panel ) ) return;

			const checks = panel?.querySelectorAll( this.ui.toggle );
			if ( !checks?.length ) return;

			if ( clicked && clicked.checked ) {
				for ( let i = 0; i < checks.length; i++ ) if ( checks[i] !== clicked ) {
					checks[i].checked = false;
					checks[i].setAttribute( 'aria-checked', 'false' );
				}
				clicked.setAttribute( 'aria-checked', 'true' );
				return;
			}

			let kept = false;
			for ( let j = 0; j < checks.length; j++ ) if ( checks[j].checked ) {
				if ( !kept ) {
					kept = true;
				} else {
					checks[j].checked = false;
					checks[j].setAttribute( 'aria-checked', 'false' );
				}
			}

			this.sync_default_value_lock( panel );
			this.sync_placeholder_lock( panel );
		}

		// ---- one-time bootstrap of a panel ----
		static bootstrap_panel(panel) {
			if ( !panel ) return;
			if ( !panel.querySelector( '.wpbc_bfb__options_editor' ) ) return; // only select-like UIs
			if ( panel.dataset.selectbase_bootstrapped === '1' ) {
				this.ensure_sortable( panel );
				return;
			}

			this.rebuild_if_empty( panel );
			this.ensure_sortable( panel );
			panel.dataset.selectbase_bootstrapped = '1';

			this.sync_default_value_lock( panel );
			this.sync_placeholder_lock( panel );
			this.sync_value_inputs_visibility( panel );
		}

		// ---- hook into inspector lifecycle (fires ONCE) ----
		static wire_once() {
			if ( Core.__selectbase_wired ) return;
			Core.__selectbase_wired = true;

			const on_ready_or_render = (ev) => {
				const panel = ev?.detail?.panel;
				const field = ev?.detail?.el || ev?.detail?.field || null;
				if ( !panel ) return;
				if ( field ) panel.__selectbase_field = field;
				this.bootstrap_panel( panel );
				// If the inspector root was remounted, ensure root listeners are (re)bound.
				this.wire_root_listeners();
			};

			document.addEventListener( 'wpbc_bfb_inspector_ready', on_ready_or_render );
			document.addEventListener( 'wpbc_bfb_inspector_render', on_ready_or_render );

			this.wire_root_listeners();
		}

		static wire_root_listeners() {

			// If already wired AND the stored root is still in the DOM, bail out.
			if ( this.__root_wired && this.__root_node?.isConnected ) return;

			const root = document.getElementById( 'wpbc_bfb__inspector' );
			if ( !root ) {
				// Root missing (e.g., SPA re-render) — clear flags so we can wire later.
				this.__root_wired = false;
				this.__root_node  = null;
				return;
			}

			this.__root_node                   = root;
			this.__root_wired                  = true;
			root.dataset.selectbase_root_wired = '1';

			const get_panel = (target) =>
				target?.closest?.( '.wpbc_bfb__inspector__body' ) ||
				root.querySelector( '.wpbc_bfb__inspector__body' ) || null;

			// Click handlers: add / delete / duplicate
			root.addEventListener( 'click', (e) => {
				const panel = get_panel( e.target );
				if ( !panel ) return;

				this.bootstrap_panel( panel );

				const ui = this.ui;

				// Existing "Add option" button (top toolbar)
				const add = e.target.closest?.( ui.add_btn );
				if ( add ) {
					this.append_row( panel, { label: '', value: '', selected: false } );
					this.commit_options( panel );
					this.sync_value_inputs_visibility( panel );
					return;
				}

				// Dropdown menu actions.
				const menu_action = e.target.closest?.( ui.menu_action );
				if ( menu_action ) {
					e.preventDefault();
					e.stopPropagation();

					const action = (menu_action.getAttribute( 'data-action' ) || '').toLowerCase();
					const row    = menu_action.closest?.( ui.row );

					if ( !row ) {
						this.close_dropdown( menu_action );
						return;
					}

					if ( 'add_after' === action ) {
						// Add empty row after current
						const prev_count = this.get_list( panel )?.children.length || 0;
						this.append_row( panel, { label: '', value: '', selected: false } );
						// Move the newly added last row just after current row to preserve "add after"
						const list = this.get_list( panel );
						if ( list && list.lastElementChild && list.lastElementChild !== row ) {
							this.insert_after( list.lastElementChild, row );
						}
						this.commit_options( panel );
						this.sync_value_inputs_visibility( panel );
					} else if ( 'duplicate' === action ) {
						const lbl = (row.querySelector( ui.label ) || {}).value || '';
						const val = (row.querySelector( ui.value ) || {}).value || '';
						const sel = !!((row.querySelector( ui.toggle ) || {}).checked);
						this.append_row( panel, { label: lbl, value: val, selected: sel, uid: this.make_uid() } );
						// Place the new row right after the current.
						const list = this.get_list( panel );

						if ( list && list.lastElementChild && list.lastElementChild !== row ) {
							this.insert_after( list.lastElementChild, row );
						}
						this.enforce_single_default( panel, null );
						this.commit_options( panel );
						this.sync_value_inputs_visibility( panel );
					} else if ( 'remove' === action ) {
						if ( row && row.parentNode ) row.parentNode.removeChild( row );
						this.commit_options( panel );
						this.sync_value_inputs_visibility( panel );
					}

					this.close_dropdown( menu_action );
					return;
				}

			}, true );


			// Input delegation.
			root.addEventListener( 'input', (e) => {
				const panel = get_panel( e.target );
				if ( ! panel ) {
					return;
				}
				const ui                = this.ui;
				const is_label_or_value = e.target.classList?.contains( 'wpbc_bfb__opt-label' ) || e.target.classList?.contains( 'wpbc_bfb__opt-value' );
				const is_toggle         = e.target.classList?.contains( 'wpbc_bfb__opt-selected-chk' );
				const is_multiple       = e.target.matches?.( ui.multiple_chk );
				const is_default_text   = e.target.matches?.( ui.default_text );
				const is_value_differs  = e.target.matches?.( ui.value_differs_chk );

				// Handle "value differs" toggle live
				if ( is_value_differs ) {
					this.sync_value_inputs_visibility( panel );
					this.commit_options( panel );
					return;
				}

				// Track when the user edits VALUE explicitly
				if ( e.target.classList?.contains( 'wpbc_bfb__opt-value' ) ) {
					const row = e.target.closest( this.ui.row );
					this.mark_row_value_user_touched( row );
					// Keep the cache updated so toggling OFF/ON later restores the latest custom value
					e.target.dataset.cached_value = e.target.value || '';
				}

				// Auto-fill VALUE from LABEL if value is fresh (and differs is ON); if differs is OFF, we mirror anyway in commit
				if ( e.target.classList?.contains( 'wpbc_bfb__opt-label' ) ) {
					const row     = e.target.closest( ui.row );
					const val_in  = row?.querySelector( ui.value );
					const differs = this.is_value_differs_enabled( panel );

					if ( val_in ) {
						if ( !differs ) {
							// single-input mode: mirror human label with minimal escaping
							val_in.value = this.build_value_from_label( e.target.value, false );
						} else if ( !this.is_row_value_user_touched( row ) ) {
							// separate-value mode, only while fresh
							val_in.value = this.build_value_from_label( e.target.value, true );
						}
					}
				}


				if ( is_label_or_value || is_toggle || is_multiple ) {
					if ( is_toggle ) e.target.setAttribute( 'aria-checked', e.target.checked ? 'true' : 'false' );
					if ( is_toggle || is_multiple ) this.enforce_single_default( panel, is_toggle ? e.target : null );
					this.commit_options( panel );
				}

				if ( is_default_text ) {
					this.sync_default_value_lock( panel );
					this.sync_placeholder_lock( panel );
					const holder = this.get_holder( panel );
					if ( holder ) {
						holder.dispatchEvent( new Event( 'input', { bubbles: true } ) );
						holder.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					}
				}
			}, true );


			// Change delegation
			root.addEventListener( 'change', (e) => {
				const panel = get_panel( e.target );
				if ( !panel ) return;

				const ui        = this.ui;
				const is_toggle = e.target.classList?.contains( 'wpbc_bfb__opt-selected-chk' );
				const is_multi  = e.target.matches?.( ui.multiple_chk );
				if ( !is_toggle && !is_multi ) return;

				if ( is_toggle ) e.target.setAttribute( 'aria-checked', e.target.checked ? 'true' : 'false' );
				this.enforce_single_default( panel, is_toggle ? e.target : null );
				this.commit_options( panel );
			}, true );

			// Lazy bootstrap
			root.addEventListener( 'mouseenter', (e) => {
				const panel = get_panel( e.target );
				if ( panel && e.target?.closest?.( this.ui.list ) ) this.bootstrap_panel( panel );
			}, true );

			root.addEventListener( 'mousedown', (e) => {
				const panel = get_panel( e.target );
				if ( panel && e.target?.closest?.( this.ui.drag_handle ) ) this.bootstrap_panel( panel );
			}, true );
		}

	};

	try { Core.WPBC_BFB_Select_Base.wire_once(); } catch (_) {}
	// Try immediately (if root is already in DOM), then again on DOMContentLoaded.
	Core.WPBC_BFB_Select_Base.wire_root_listeners();

	document.addEventListener('DOMContentLoaded', () => { Core.WPBC_BFB_Select_Base.wire_root_listeners();  });

}( window ));
// ---------------------------------------------------------------------------------------------------------------------
// == File  /includes/page-form-builder/_out/core/bfb-ui.js == | 2025-09-10 15:47
// ---------------------------------------------------------------------------------------------------------------------
(function (w, d) {
	'use strict';

	// Single global namespace (idempotent & load-order safe).
	const Core = (w.WPBC_BFB_Core = w.WPBC_BFB_Core || {});
	const UI   = (Core.UI = Core.UI || {});

	// --- Highlight Element,  like Generator brn  -  Tiny UI helpers ------------------------------------
	UI._pulse_timers = UI._pulse_timers || new Map(); // el -> timer_id
	UI._pulse_meta   = UI._pulse_meta   || new Map(); // el -> { token, last_ts, debounce_id, color_set }
	// Pulse tuning (milliseconds).
	UI.PULSE_THROTTLE_MS  = Number.isFinite( UI.PULSE_THROTTLE_MS ) ? UI.PULSE_THROTTLE_MS : 500;
	UI.PULSE_DEBOUNCE_MS  = Number.isFinite( UI.PULSE_DEBOUNCE_MS ) ? UI.PULSE_DEBOUNCE_MS : 750;

	// Debounce STRUCTURE_CHANGE for continuous inspector controls (sliders / scrubbing).
	// Tune: 180..350 is usually a sweet spot.
	UI.STRUCTURE_CHANGE_DEBOUNCE_MS = Number.isFinite( UI.STRUCTURE_CHANGE_DEBOUNCE_MS ) ? UI.STRUCTURE_CHANGE_DEBOUNCE_MS : 180;
	// Change this to tune speed: 50..120 ms is a good range. Can be configured in <div data-len-group data-len-throttle="180">...</div>.
	UI.VALUE_SLIDER_THROTTLE_MS = Number.isFinite( UI.VALUE_SLIDER_THROTTLE_MS ) ? UI.VALUE_SLIDER_THROTTLE_MS : 120;

	/**
	 * Cancel any running pulse sequence for an element.
	 * Uses token invalidation so already-scheduled callbacks become no-ops.
	 *
	 * @param {HTMLElement} el
	 */
	UI.cancel_pulse = function (el) {
		if ( !el ) { return; }
		try {
			clearTimeout( UI._pulse_timers.get( el ) );
		} catch ( _ ) {}
		UI._pulse_timers.delete( el );

		var meta = UI._pulse_meta.get( el ) || {};
		meta.token = (Number.isFinite( meta.token ) ? meta.token : 0) + 1;
		meta.color_set = false;
		try { el.classList.remove( 'wpbc_bfb__scroll-pulse', 'wpbc_bfb__highlight-pulse' ); } catch ( _ ) {}
		try { el.style.removeProperty( '--wpbc-bfb-pulse-color' ); } catch ( _ ) {}
		UI._pulse_meta.set( el, meta );
		try { clearTimeout( meta.debounce_id ); } catch ( _ ) {}
		meta.debounce_id = 0;
	};

	/**
	 * Force-restart a CSS animation on a class.
	 * @param {HTMLElement} el
	 * @param {string} cls
	 */
	UI._restart_css_animation = function (el, cls) {
		if ( ! el ) { return; }
		try {
			el.classList.remove( cls );
		} catch ( _ ) {}
		// Force reflow so the next add() retriggers the keyframes.
		void el.offsetWidth;
		try {
			el.classList.add( cls );
		} catch ( _ ) {}
	};

	/**
		Single pulse (back-compat).
		@param {HTMLElement} el
		@param {number} dur_ms
	 */
	UI.pulse_once = function (el, dur_ms) {
		if ( ! el ) { return; }
		var cls = 'wpbc_bfb__scroll-pulse';
		var ms  = Number.isFinite( dur_ms ) ? dur_ms : 700;

		UI.cancel_pulse( el );

		var meta  = UI._pulse_meta.get( el ) || {};
		var token = (Number.isFinite( meta.token ) ? meta.token : 0) + 1;
		meta.token = token;
		UI._pulse_meta.set( el, meta );

		UI._restart_css_animation( el, cls );
		var t = setTimeout( function () {
			// ignore if a newer pulse started.
			var m = UI._pulse_meta.get( el ) || {};
			if ( m.token !== token ) { return; }
			try {
				el.classList.remove( cls );
			} catch ( _ ) {}
			UI._pulse_timers.delete( el );
		}, ms );
		UI._pulse_timers.set( el, t );
	};

	/**
		Multi-blink sequence with optional per-call color override.
		@param {HTMLElement} el
		@param {number} [times=3]
		@param {number} [on_ms=280]
		@param {number} [off_ms=180]
		@param {string} [hex_color] Optional CSS color (e.g. '#ff4d4f' or 'rgb(...)').
	 */
	UI.pulse_sequence = function (el, times, on_ms, off_ms, hex_color) {
		if ( !el || !d.body.contains( el ) ) {
			return;
		}
		var cls   = 'wpbc_bfb__highlight-pulse';
		var count = Number.isFinite( times ) ? times : 2;
		var on    = Number.isFinite( on_ms ) ? on_ms : 280;
		var off   = Number.isFinite( off_ms ) ? off_ms : 180;

		// Throttle: avoid reflow spam if called repeatedly while typing/dragging.
		var meta = UI._pulse_meta.get( el ) || {};
		var now  = Date.now();
		var throttle_ms = Number.isFinite( UI.PULSE_THROTTLE_MS ) ? UI.PULSE_THROTTLE_MS : 120;
		if ( Number.isFinite( meta.last_ts ) && (now - meta.last_ts) < throttle_ms ) {
			return;
		}
		meta.last_ts = now;

		// cancel any running pulse and reset class (token invalidation).
		UI.cancel_pulse( el );

		// new token for this run
		var token = (Number.isFinite( meta.token ) ? meta.token : 0) + 1;
		meta.token = token;

		var have_color = !!hex_color && typeof hex_color === 'string';
		if ( have_color ) {
			try {
				el.style.setProperty( '--wpbc-bfb-pulse-color', hex_color );
			} catch ( _ ) {}
			meta.color_set = true;
		}
		UI._pulse_meta.set( el, meta );

		var i = 0;
		(function tick() {
			var m = UI._pulse_meta.get( el ) || {};
			if ( m.token !== token ) {
				// canceled/replaced
				return;
			}
			if ( i >= count ) {
				UI._pulse_timers.delete( el );
				if ( have_color ) {
					try {
						el.style.removeProperty( '--wpbc-bfb-pulse-color' );
					} catch ( _ ) {}
				}
				return;
			}
			UI._restart_css_animation( el, cls );
			UI._pulse_timers.set( el, setTimeout( function () {     // ON -> OFF
				var m2 = UI._pulse_meta.get( el ) || {};
				if ( m2.token !== token ) { return; }
				try {
					el.classList.remove( cls );
				} catch ( _ ) {
				}
				UI._pulse_timers.set( el, setTimeout( function () { // OFF gap -> next
					var m3 = UI._pulse_meta.get( el ) || {};
					if ( m3.token !== token ) { return; }
					i++;
					tick();
				}, off ) );
			}, on ) );
		})();
	};


	/**
	 * Debounced query + pulse.
	 * Useful for `input` events (sliders / typing) to avoid forced reflow spam.
	 *
	 * @param {HTMLElement|string} root_or_selector
	 * @param {string} selector
	 * @param {number} wait_ms
	 * @param {number} [a]
	 * @param {number} [b]
	 * @param {number} [c]
	 * @param {string} [color]
	 */
	UI.pulse_query_debounced = function (root_or_selector, selector, wait_ms, a, b, c, color) {
		var root = (typeof root_or_selector === 'string') ? d : (root_or_selector || d);
		var sel  = (typeof root_or_selector === 'string') ? root_or_selector : selector;
		if ( !sel ) { return; }
		var el = root.querySelector( sel );
		if ( !el ) { return; }

		var def_ms = Number.isFinite( UI.PULSE_DEBOUNCE_MS ) ? UI.PULSE_DEBOUNCE_MS : 120;
		var ms     = Number.isFinite( wait_ms ) ? wait_ms : def_ms;
		var meta = UI._pulse_meta.get( el ) || {};
		try { clearTimeout( meta.debounce_id ); } catch ( _ ) {}
		meta.debounce_id = setTimeout( function () {
			UI.pulse_sequence( el, a, b, c, color );
		}, ms );
		UI._pulse_meta.set( el, meta );
	};

	/**
		Query + pulse:
		(BC) If only 3rd arg is a number and no 4th/5th -> single long pulse.
		Otherwise -> strong sequence (defaults 3×280/180).
		Optional 6th arg: color.
		@param {HTMLElement|string} root_or_selector
		@param {string} [selector]
		@param {number} [a]
		@param {number} [b]

		@param {number} [c]

		@param {string} [color]
	 */
	UI.pulse_query = function (root_or_selector, selector, a, b, c, color) {
		var root = (typeof root_or_selector === 'string') ? d : (root_or_selector || d);
		var sel  = (typeof root_or_selector === 'string') ? root_or_selector : selector;
		if ( !sel ) {
			return;
		}

		var el = root.querySelector( sel );
		if ( !el ) {
			return;
		}

// Back-compat: UI.pulseQuery(root, sel, dur_ms)
		if ( Number.isFinite( a ) && b === undefined && c === undefined ) {
			return UI.pulse_once( el, a );
		}
// New: sequence; params optional; supports optional color.
		UI.pulse_sequence( el, a, b, c, color );
	};

	/**
	Convenience helper (snake_case) to call a strong pulse with options.

	@param {HTMLElement} el

	@param {Object} [opts]

	@param {number} [opts.times=3]

	@param {number} [opts.on_ms=280]

	@param {number} [opts.off_ms=180]

	@param {string} [opts.color]
	 */
	UI.pulse_sequence_strong = function (el, opts) {
		opts = opts || {};
		UI.pulse_sequence(
			el,
			Number.isFinite( opts.times ) ? opts.times : 3,
			Number.isFinite( opts.on_ms ) ? opts.on_ms : 280,
			Number.isFinite( opts.off_ms ) ? opts.off_ms : 180,
			opts.color
		);
	};


	/**
	 * Base class for BFB modules.
	 */
	UI.WPBC_BFB_Module = class {
		/** @param {WPBC_Form_Builder} builder */
		constructor(builder) {
			this.builder = builder;
		}

		/** Initialize the module. */
		init() {
		}

		/** Cleanup the module. */
		destroy() {
		}
	};

	/**
	 * Central overlay/controls manager for fields/sections.
	 * Pure UI composition; all actions route back into the builder instance.
	 */
	UI.WPBC_BFB_Overlay = class {

		/**
		 * Ensure an overlay exists and is wired up on the element.
		 * @param {WPBC_Form_Builder} builder
		 * @param {HTMLElement} el - field or section element
		 */
		static ensure(builder, el) {

			if ( !el ) {
				return;
			}
			const isSection = el.classList.contains( 'wpbc_bfb__section' );

			// let overlay = el.querySelector( Core.WPBC_BFB_DOM.SELECTORS.overlay );
			let overlay = el.querySelector( `:scope > ${Core.WPBC_BFB_DOM.SELECTORS.overlay}` );
			if ( !overlay ) {
				overlay = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__overlay-controls' );
				el.prepend( overlay );
			}

			// Drag handle.
			if ( !overlay.querySelector( '.wpbc_bfb__drag-handle' ) ) {
				const dragClass = isSection ? 'wpbc_bfb__drag-handle section-drag-handle' : 'wpbc_bfb__drag-handle';
				overlay.appendChild(
					Core.WPBC_Form_Builder_Helper.create_element( 'span', dragClass, '<span class="wpbc_icn_drag_indicator"></span>' )
				);
			}

			// SETTINGS button (shown for both fields & sections).
			if ( !overlay.querySelector( '.wpbc_bfb__settings-btn' ) ) {
				const settings_btn   = Core.WPBC_Form_Builder_Helper.create_element( 'button', 'wpbc_bfb__settings-btn', '<i class="menu_icon icon-1x wpbc_icn_settings"></i>' );
				settings_btn.type    = 'button';
				settings_btn.title   = 'Open settings';
				settings_btn.onclick = (e) => {
					e.preventDefault();
					// Select THIS element and scroll it into view.
					builder.select_field( el, { scrollIntoView: true } );

					// Auto-open Inspector from the overlay “Settings” button.
					document.dispatchEvent( new CustomEvent( 'wpbc_bfb:show_panel', {
						detail: {
							panel_id: 'wpbc_bfb__inspector',
							tab_id  : 'wpbc_tab_inspector'
						}
					} ) );

					// Try to bring the inspector into view / focus first input.
					const ins = document.getElementById( 'wpbc_bfb__inspector' );
					if ( ins ) {
						ins.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
						// Focus first interactive control (best-effort).
						setTimeout( () => {
							const focusable = ins.querySelector( 'input,select,textarea,button,[contenteditable],[tabindex]:not([tabindex="-1"])' );
							focusable?.focus?.();
						}, 260 );
					}
				};

				overlay.appendChild( settings_btn );
			}

			overlay.setAttribute( 'role', 'toolbar' );
			overlay.setAttribute( 'aria-label', el.classList.contains( 'wpbc_bfb__section' ) ? 'Section tools' : 'Field tools' );

			return overlay;
		}
	};

	/**
	 * WPBC Layout Chips helper - visual layout picker (chips), e.g., "50%/50%", to a section overlay.
	 *
	 * Renders Equal/Presets/Custom chips into a host container and wires them to apply the layout.
	 */
	UI.WPBC_BFB_Layout_Chips = class {

		/** Read per-column min (px) from CSS var set by the guard. */
		static _get_col_min_px(col) {
			const v = getComputedStyle( col ).getPropertyValue( '--wpbc-col-min' ) || '0';
			const n = parseFloat( v );
			return Number.isFinite( n ) ? Math.max( 0, n ) : 0;
		}

		/**
		 * Turn raw weights (e.g. [1,1], [2,1,1]) into effective "available-%" bases that
		 * (a) sum to the row's available %, and (b) meet every column's min px.
		 * Returns an array of bases (numbers) or null if impossible to satisfy mins.
		 */
		static _fit_weights_respecting_min(builder, row, weights) {
			const cols = Array.from( row.querySelectorAll( ':scope > .wpbc_bfb__column' ) );
			const n    = cols.length;
			if ( !n ) return null;
			if ( !Array.isArray( weights ) || weights.length !== n ) return null;

			// available % after gaps (from LayoutService)
			const gp       = builder.col_gap_percent;
			const eff      = builder.layout.compute_effective_bases_from_row( row, gp );
			const availPct = eff.available;               // e.g. 94 if 2 cols and 3% gap
			const rowPx    = row.getBoundingClientRect().width;
			const availPx  = rowPx * (availPct / 100);

			// collect minima in % of "available"
			const minPct = cols.map( (c) => {
				const minPx = UI.WPBC_BFB_Layout_Chips._get_col_min_px( c );
				if ( availPx <= 0 ) return 0;
				return (minPx / availPx) * availPct;
			} );

			// If mins alone don't fit, bail.
			const sumMin = minPct.reduce( (a, b) => a + b, 0 );
			if ( sumMin > availPct - 1e-6 ) {
				return null; // impossible to respect mins; don't apply preset
			}

			// Target percentages from weights, normalized to availPct.
			const wSum      = weights.reduce( (a, w) => a + (Number( w ) || 0), 0 ) || n;
			const targetPct = weights.map( (w) => ((Number( w ) || 0) / wSum) * availPct );

			// Lock columns that would be below min, then distribute the remainder
			// across the remaining columns proportionally to their targetPct.
			const locked  = new Array( n ).fill( false );
			let lockedSum = 0;
			for ( let i = 0; i < n; i++ ) {
				if ( targetPct[i] < minPct[i] ) {
					locked[i] = true;
					lockedSum += minPct[i];
				}
			}

			let remaining     = availPct - lockedSum;
			const freeIdx     = [];
			let freeTargetSum = 0;
			for ( let i = 0; i < n; i++ ) {
				if ( !locked[i] ) {
					freeIdx.push( i );
					freeTargetSum += targetPct[i];
				}
			}

			const result = new Array( n ).fill( 0 );
			// Seed locked with their minima.
			for ( let i = 0; i < n; i++ ) {
				if ( locked[i] ) result[i] = minPct[i];
			}

			if ( freeIdx.length === 0 ) {
				// everything locked exactly at min; any leftover (shouldn't happen)
				// would be ignored to keep simplicity and stability.
				return result;
			}

			if ( remaining <= 0 ) {
				// nothing left to distribute; keep exactly mins on locked,
				// nothing for free (degenerate but consistent)
				return result;
			}

			if ( freeTargetSum <= 0 ) {
				// distribute equally among free columns
				const each = remaining / freeIdx.length;
				freeIdx.forEach( (i) => (result[i] = each) );
				return result;
			}

			// Distribute remaining proportionally to free columns' targetPct
			freeIdx.forEach( (i) => {
				result[i] = remaining * (targetPct[i] / freeTargetSum);
			} );
			return result;
		}

		/** Apply a preset but guard it by minima; returns true if applied, false if skipped. */
		static _apply_preset_with_min_guard(builder, section_el, weights) {
			const row = section_el.querySelector( ':scope > .wpbc_bfb__row' );
			if ( !row ) return false;

			const fitted = UI.WPBC_BFB_Layout_Chips._fit_weights_respecting_min( builder, row, weights );
			if ( !fitted ) {
				builder?._announce?.( 'Not enough space for this layout because of fields’ minimum widths.' );
				return false;
			}

			// `fitted` already sums to the row’s available %, so we can apply bases directly.
			builder.layout.apply_bases_to_row( row, fitted );
			return true;
		}


		/**
		 * Build and append layout chips for a section.
		 *
		 * @param {WPBC_Form_Builder} builder - The form builder instance.
		 * @param {HTMLElement} section_el - The .wpbc_bfb__section element.
		 * @param {HTMLElement} host_el - Container where chips should be rendered.
		 * @returns {void}
		 */
		static render_for_section(builder, section_el, host_el) {

			if ( !builder || !section_el || !host_el ) {
				return;
			}

			const row = section_el.querySelector( ':scope > .wpbc_bfb__row' );
			if ( !row ) {
				return;
			}

			const cols = row.querySelectorAll( ':scope > .wpbc_bfb__column' ).length || 1;

			// Clear host.
			host_el.innerHTML = '';

			// Equal chip.
			host_el.appendChild(
				UI.WPBC_BFB_Layout_Chips._make_chip( builder, section_el, Array( cols ).fill( 1 ), 'Equal' )
			);

			// Presets based on column count.
			const presets = builder.layout.build_presets_for_columns( cols );
			presets.forEach( (weights) => {
				host_el.appendChild(
					UI.WPBC_BFB_Layout_Chips._make_chip( builder, section_el, weights, null )
				);
			} );

			// Custom chip.
			const customBtn       = document.createElement( 'button' );
			customBtn.type        = 'button';
			customBtn.className   = 'wpbc_bfb__layout_chip';
			customBtn.textContent = 'Custom…';
			customBtn.title       = `Enter ${cols} percentages`;
			customBtn.addEventListener( 'click', () => {
				const example = (cols === 2) ? '50,50' : (cols === 3 ? '20,60,20' : '25,25,25,25');
				const text    = prompt( `Enter ${cols} percentages (comma or space separated):`, example );
				if ( text == null ) return;
				const weights = builder.layout.parse_weights( text );
				if ( weights.length !== cols ) {
					alert( `Please enter exactly ${cols} numbers.` );
					return;
				}
				// OLD:
				// builder.layout.apply_layout_preset( section_el, weights, builder.col_gap_percent );
				// Guarded apply:.
				if ( !UI.WPBC_BFB_Layout_Chips._apply_preset_with_min_guard( builder, section_el, weights ) ) {
					return;
				}
				host_el.querySelectorAll( '.wpbc_bfb__layout_chip' ).forEach( c => c.classList.remove( 'is-active' ) );
				customBtn.classList.add( 'is-active' );
			} );
			host_el.appendChild( customBtn );
		}

		/**
		 * Create a single layout chip button.
		 *
		 * @private
		 * @param {WPBC_Form_Builder} builder
		 * @param {HTMLElement} section_el
		 * @param {number[]} weights
		 * @param {string|null} label
		 * @returns {HTMLButtonElement}
		 */
		static _make_chip(builder, section_el, weights, label = null) {

			const btn     = document.createElement( 'button' );
			btn.type      = 'button';
			btn.className = 'wpbc_bfb__layout_chip';

			const title = label || builder.layout.format_preset_label( weights );
			btn.title   = title;

			// Visual miniature.
			const vis     = document.createElement( 'div' );
			vis.className = 'wpbc_bfb__layout_chip-vis';
			const sum     = weights.reduce( (a, b) => a + (Number( b ) || 0), 0 ) || 1;
			weights.forEach( (w) => {
				const bar      = document.createElement( 'span' );
				bar.style.flex = `0 0 calc( ${((Number( w ) || 0) / sum * 100).toFixed( 3 )}% - 1.5px )`;
				vis.appendChild( bar );
			} );
			btn.appendChild( vis );

			const txt       = document.createElement( 'span' );
			txt.className   = 'wpbc_bfb__layout_chip-label';
			txt.textContent = label || builder.layout.format_preset_label( weights );
			btn.appendChild( txt );

			btn.addEventListener( 'click', () => {
				// OLD:
				// builder.layout.apply_layout_preset( section_el, weights, builder.col_gap_percent );

				// NEW:
				if ( !UI.WPBC_BFB_Layout_Chips._apply_preset_with_min_guard( builder, section_el, weights ) ) {
					return; // do not toggle active if we didn't change layout
				}

				btn.parentElement?.querySelectorAll( '.wpbc_bfb__layout_chip' ).forEach( c => c.classList.remove( 'is-active' ) );
				btn.classList.add( 'is-active' );
			} );

			return btn;
		}
	};

	/**
	 * Selection controller for fields and announcements.
	 */
	UI.WPBC_BFB_Selection_Controller = class extends UI.WPBC_BFB_Module {

		init() {

			this._selected_uid              = null;
			this.builder.select_field       = this.select_field.bind( this );
			this.builder.get_selected_field = this.get_selected_field.bind( this );
			this._on_clear                  = this.on_clear.bind( this );

			// Centralized delete command used by keyboard + inspector + overlay.
			this.builder.delete_item = (el) => {
				if ( !el ) {
					return null;
				}
				const b        = this.builder;
				const neighbor = b._find_neighbor_selectable?.( el ) || null;
				el.remove();
				// Use local Core constants (not a global) to avoid ReferenceErrors.
				b.bus?.emit?.( Core.WPBC_BFB_Events.FIELD_REMOVE, { el, id: el?.dataset?.id, uid: el?.dataset?.uid } );
				b.usage?.update_palette_ui?.();
				// Notify generic structure listeners, too:
				b.bus?.emit?.( Core.WPBC_BFB_Events.STRUCTURE_CHANGE, { reason: 'delete', el } );
				// Defer selection a tick so the DOM is fully settled before Inspector hydrates.
				requestAnimationFrame( () => {
					// This calls inspector.bind_to_field() and opens the Inspector panel.
					b.select_field?.( neighbor || null, { scrollIntoView: !!neighbor } );
				} );
				return neighbor;
			};
			this.builder.bus.on( Core.WPBC_BFB_Events.CLEAR_SELECTION, this._on_clear );
			this.builder.bus.on( Core.WPBC_BFB_Events.STRUCTURE_LOADED, this._on_clear );
			// delegated click selection (capture ensures we win before bubbling to containers).
			this._on_canvas_click = this._handle_canvas_click.bind( this );
			this.builder.pages_container.addEventListener( 'click', this._on_canvas_click, true );
		}

		destroy() {
			this.builder.bus.off( Core.WPBC_BFB_Events.CLEAR_SELECTION, this._on_clear );

			if ( this._on_canvas_click ) {
				this.builder.pages_container.removeEventListener( 'click', this._on_canvas_click, true );
				this._on_canvas_click = null;
			}
		}

		/**
		 * Delegated canvas click -> select closest field/section (inner beats outer).
		 * @private
		 * @param {MouseEvent} e
		 */
		_handle_canvas_click(e) {
			const root = this.builder.pages_container;
			if ( !root ) return;

			// Ignore clicks on controls/handles/resizers, etc.
			const IGNORE = [
				'.wpbc_bfb__overlay-controls',
				'.wpbc_bfb__layout_picker',
				'.wpbc_bfb__drag-handle',
				'.wpbc_bfb__field-remove-btn',
				'.wpbc_bfb__field-move-up',
				'.wpbc_bfb__field-move-down',
				'.wpbc_bfb__column-resizer'
			].join( ',' );

			if ( e.target.closest( IGNORE ) ) {
				return; // let those controls do their own thing.
			}

			// Find the closest selectable (field OR section) from the click target.
			let hit = e.target.closest?.(
				`${Core.WPBC_BFB_DOM.SELECTORS.validField}, ${Core.WPBC_BFB_DOM.SELECTORS.section}, .wpbc_bfb__column`
			);

			if ( !hit || !root.contains( hit ) ) {
				this.select_field( null );           // Clear selection on blank click.
				return;                              // Empty space is handled elsewhere.
			}

			// NEW: if user clicked a COLUMN -> remember tab key on its SECTION, but still select the section.
			let preselect_tab_key = null;
			if ( hit.classList.contains( 'wpbc_bfb__column' ) ) {
				const row  = hit.closest( '.wpbc_bfb__row' );
				const cols = row ? Array.from( row.querySelectorAll( ':scope > .wpbc_bfb__column' ) ) : [];
				const idx  = Math.max( 0, cols.indexOf( hit ) );
				const sec  = hit.closest( '.wpbc_bfb__section' );
				if ( sec ) {
					preselect_tab_key = String( idx + 1 );              // tabs are 1-based in ui-column-styles.js
					// Hint for the renderer (it reads this BEFORE rendering and restores the tab).
					sec.dataset.col_styles_active_tab = preselect_tab_key;
					// promote selection to the section (same UX as before).
					hit                               = sec;
					// NEW: visually mark which column is being edited
					if ( UI && UI.WPBC_BFB_Column_Styles && UI.WPBC_BFB_Column_Styles.set_selected_col_flag ) {
						UI.WPBC_BFB_Column_Styles.set_selected_col_flag( sec, preselect_tab_key );
					}
				}
			}

			// Select and stop bubbling so outer containers don’t reselect a parent.
			this.select_field( hit );
			e.stopPropagation();

			// Also set the tab after the inspector renders (works even if it was already open).
			if ( preselect_tab_key ) {
				(window.requestAnimationFrame || setTimeout)( function () {
					try {
						const ins  = document.getElementById( 'wpbc_bfb__inspector' );
						const tabs = ins && ins.querySelector( '[data-bfb-slot="column_styles"] [data-wpbc-tabs]' );
						if ( tabs && window.wpbc_ui_tabs && typeof window.wpbc_ui_tabs.set_active === 'function' ) {
							window.wpbc_ui_tabs.set_active( tabs, preselect_tab_key );
						}
					} catch ( _e ) {
					}
				}, 0 );

				// Politely ask the Inspector to focus/open the "Column Styles" group and tab.
				try {
					document.dispatchEvent( new CustomEvent( 'wpbc_bfb:inspector_focus', {
						detail: {
							group  : 'column_styles',
							tab_key: preselect_tab_key
						}
					} ) );
				} catch ( _e ) {
				}
			}
		}


		/**
		 * Select a field element or clear selection.
		 *
		 * @param {HTMLElement|null} field_el
		 * @param {{scrollIntoView?: boolean}} [opts = {}]
		 */
		select_field(field_el, { scrollIntoView = false } = {}) {
			const root   = this.builder.pages_container;
			const prevEl = this.get_selected_field?.() || null;   // the one we’re leaving.

			// Ignore elements not in the canvas.
			if ( field_el && !root.contains( field_el ) ) {
				field_el = null; // treat as "no selection".
			}

			// NEW: if we are leaving a section, clear its column highlight
			if (
				prevEl && prevEl !== field_el &&
				prevEl.classList?.contains( 'wpbc_bfb__section' ) &&
				UI?.WPBC_BFB_Column_Styles?.clear_selected_col_flag
			) {
				UI.WPBC_BFB_Column_Styles.clear_selected_col_flag( prevEl );
			}

			// If we're leaving a field, permanently stop auto-name for it.
			if ( prevEl && prevEl !== field_el && prevEl.classList?.contains( 'wpbc_bfb__field' ) ) {
				prevEl.dataset.autoname = '0';
				prevEl.dataset.fresh    = '0';
			}

			root.querySelectorAll( '.is-selected' ).forEach( (n) => {
				n.classList.remove( 'is-selected' );
			} );
			if ( !field_el ) {
				const prev         = this._selected_uid || null;
				this._selected_uid = null;
				this.builder.inspector?.clear?.();
				root.classList.remove( 'has-selection' );
				this.builder.bus.emit( Core.WPBC_BFB_Events.CLEAR_SELECTION, { prev_uid: prev, source: 'builder' } );

				// Auto-open "Add Fields" when nothing is selected.
				document.dispatchEvent( new CustomEvent( 'wpbc_bfb:show_panel', {
					detail: {
						panel_id: 'wpbc_bfb__palette_add_new',
						tab_id  : 'wpbc_tab_library'
					}
				} ) );

				return;
			}
			field_el.classList.add( 'is-selected' );
			this._selected_uid = field_el.getAttribute( 'data-uid' ) || null;

			// Fallback: ensure sections announce themselves as type="section".
			if ( field_el.classList.contains( 'wpbc_bfb__section' ) && !field_el.dataset.type ) {
				field_el.dataset.type = 'section';
			}

			if ( scrollIntoView ) {
				field_el.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
			this.builder.inspector?.bind_to_field?.( field_el );

			// Fallback: ensure inspector enhancers (incl. ValueSlider) run every bind.
			try {
				const ins = document.getElementById( 'wpbc_bfb__inspector' )
					|| document.querySelector( '.wpbc_bfb__inspector' );
				if ( ins ) {
					UI.InspectorEnhancers?.scan?.( ins );              // runs all enhancers
					UI.WPBC_BFB_ValueSlider?.init_on?.( ins );         // extra belt-and-suspenders
				}
			} catch ( _ ) {
			}

			// NEW: when selecting a section, reflect its active tab as the highlighted column.
			if ( field_el.classList.contains( 'wpbc_bfb__section' ) &&
				UI?.WPBC_BFB_Column_Styles?.set_selected_col_flag ) {
				var k = (field_el.dataset && field_el.dataset.col_styles_active_tab)
					? field_el.dataset.col_styles_active_tab : '1';
				UI.WPBC_BFB_Column_Styles.set_selected_col_flag( field_el, k );
			}

			// Keep sections & fields in the same flow:
			// 1) Generic hydrator for simple dataset-backed controls.
			if ( field_el ) {
				UI.WPBC_BFB_Inspector_Bridge._generic_hydrate_controls?.( this.builder, field_el );
				UI.WPBC_BFB_Inspector_Bridge._hydrate_special_controls?.( this.builder, field_el );
			}

			// Auto-open Inspector when a user selects a field/section .
			document.dispatchEvent( new CustomEvent( 'wpbc_bfb:show_panel', {
				detail: {
					panel_id: 'wpbc_bfb__inspector',
					tab_id  : 'wpbc_tab_inspector'
				}
			} ) );

			root.classList.add( 'has-selection' );
			this.builder.bus.emit( Core.WPBC_BFB_Events.SELECT, { uid: this._selected_uid, el: field_el } );
			const label = field_el?.querySelector( '.wpbc_bfb__field-label' )?.textContent || (field_el.classList.contains( 'wpbc_bfb__section' ) ? 'section' : '') || field_el?.dataset?.id || 'item';
			this.builder._announce( 'Selected ' + label + '.' );
		}

		/** @returns {HTMLElement|null} */
		get_selected_field() {
			if ( !this._selected_uid ) {
				return null;
			}
			const esc_attr = Core.WPBC_BFB_Sanitize.esc_attr_value_for_selector( this._selected_uid );
			return this.builder.pages_container.querySelector( `.wpbc_bfb__field[data-uid="${esc_attr}"], .wpbc_bfb__section[data-uid="${esc_attr}"]` );
		}

		/** @param {CustomEvent} ev */
		on_clear(ev) {
			const src = ev?.detail?.source ?? ev?.source;
			if ( src !== 'builder' ) {
				this.select_field( null );
			}
		}

	};

	/**
	 * Bridges the builder with the Inspector and sanitizes id/name edits.
	 */
	UI.WPBC_BFB_Inspector_Bridge = class extends UI.WPBC_BFB_Module {

		init() {
			this._attach_inspector();
			this._bind_id_sanitizer();
			this._open_inspector_after_field_added();
			this._bind_focus_shortcuts();
		}

		_attach_inspector() {
			const b      = this.builder;
			const attach = () => {
				if ( typeof window.WPBC_BFB_Inspector === 'function' ) {
					b.inspector = new WPBC_BFB_Inspector( document.getElementById( 'wpbc_bfb__inspector' ), b );
					this._bind_id_sanitizer();
					document.removeEventListener( 'wpbc_bfb_inspector_ready', attach );
				}
			};
			// Ensure we bind after late ready as well.
			if ( typeof window.WPBC_BFB_Inspector === 'function' ) {
				attach();
			} else {
				b.inspector = {
					bind_to_field() {
					}, clear() {
					}
				};
				document.addEventListener( 'wpbc_bfb_inspector_ready', attach );
				setTimeout( attach, 0 );
			}
		}

		/**
		 * Listen for "focus" hints from the canvas and open the right group/tab.
		 * - Supports: group === 'column_styles'
		 * - Also scrolls the group into view.
		 */
		_bind_focus_shortcuts() {
			/** @param {CustomEvent} e */
			const on_focus = (e) => {
				try {
					const grp_key = e && e.detail && e.detail.group;
					const tab_key = e && e.detail && e.detail.tab_key;
					if ( !grp_key ) {
						return;
					}

					const ins = document.getElementById( 'wpbc_bfb__inspector' ) || document.querySelector( '.wpbc_bfb__inspector' );
					if ( !ins ) {
						return;
					}

					if ( grp_key === 'column_styles' ) {
						// Find the Column Styles slot/group.
						const slot = ins.querySelector( '[data-bfb-slot="column_styles"]' )
							|| ins.querySelector( '[data-inspector-group-key="column_styles"]' );
						if ( slot ) {
							// Open collapsible container if present.
							const group_wrap = slot.closest( '.inspector__group' ) || slot.closest( '[data-inspector-group]' );
							if ( group_wrap && !group_wrap.classList.contains( 'is-open' ) ) {
								group_wrap.classList.add( 'is-open' );
								// Mirror ARIA state if your header uses aria-expanded.
								const header_btn = group_wrap.querySelector( '[aria-expanded]' );
								if ( header_btn ) {
									header_btn.setAttribute( 'aria-expanded', 'true' );
								}
							}

							// Optional: set the requested tab key if tabs exist in this group.
							if ( tab_key ) {
								const tabs = slot.querySelector( '[data-wpbc-tabs]' );
								if ( tabs && window.wpbc_ui_tabs && typeof window.wpbc_ui_tabs.set_active === 'function' ) {
									window.wpbc_ui_tabs.set_active( tabs, String( tab_key ) );
								}
							}

							// Bring into view for convenience.
							try {
								slot.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
							} catch ( _e ) {
							}
						}
					}
				} catch ( _e ) {
				}
			};

			this._on_inspector_focus = on_focus;
			document.addEventListener( 'wpbc_bfb:inspector_focus', on_focus, true );
		}

		destroy() {
			try {
				if ( this._on_inspector_focus ) {
					document.removeEventListener( 'wpbc_bfb:inspector_focus', this._on_inspector_focus, true );
					this._on_inspector_focus = null;
				}
			} catch ( _e ) {
			}
		}


		/**
		 * Hydrate inspector inputs for "special" keys that we handle explicitly.
		 * Works for both fields and sections.
		 * @param {WPBC_Form_Builder} builder
		 * @param {HTMLElement} sel
		 */
		static _hydrate_special_controls(builder, sel) {
			const ins = document.getElementById( 'wpbc_bfb__inspector' );
			if ( !ins || !sel ) return;

			const setVal = (key, val) => {
				const ctrl = ins.querySelector( `[data-inspector-key="${key}"]` );
				if ( ctrl && 'value' in ctrl ) ctrl.value = String( val ?? '' );
			};

			// Internal id / name / public html_id.
			setVal( 'id', sel.getAttribute( 'data-id' ) || '' );
			setVal( 'name', sel.getAttribute( 'data-name' ) || '' );
			setVal( 'html_id', sel.getAttribute( 'data-html_id' ) || '' );

			// Section-only extras are harmless to set for fields (controls may not exist).
			setVal( 'cssclass', sel.getAttribute( 'data-cssclass' ) || '' );
			setVal( 'label', sel.getAttribute( 'data-label' ) || '' );
		}


		/**
		 * Hydrate inspector inputs that declare a generic dataset mapping via
		 * [data-inspector-key] but do NOT declare a custom value_from adapter.
		 * This makes sections follow the same data flow as fields with almost no glue.
		 *
		 * @param {WPBC_Form_Builder} builder
		 * @param {HTMLElement} sel - currently selected field/section
		 */
		static _generic_hydrate_controls(builder, sel) {
			const ins = document.getElementById( 'wpbc_bfb__inspector' );
			if ( !ins || !sel ) return;

			const SKIP = /^(id|name|html_id|cssclass|label)$/; // handled by _hydrate_special_controls

			// NEW: read schema for the selected element’s type.
			const schemas     = window.WPBC_BFB_Schemas || {};
			const typeKey     = (sel.dataset && sel.dataset.type) || '';
			const schemaEntry = schemas[typeKey] || null;
			const propsSchema = (schemaEntry && schemaEntry.schema && schemaEntry.schema.props) ? schemaEntry.schema.props : {};
			const hasOwn      = Function.prototype.call.bind( Object.prototype.hasOwnProperty );
			const getDefault  = (key) => {
				const meta = propsSchema[key];
				return (meta && hasOwn( meta, 'default' )) ? meta.default : undefined;
			};

			ins.querySelectorAll( '[data-inspector-key]' ).forEach( (ctrl) => {
				const key = String( ctrl.dataset?.inspectorKey || '' ).toLowerCase();
				if ( !key || SKIP.test( key ) ) return;

				// Element-level lock.
				const dl = (ctrl.dataset?.locked || '').trim().toLowerCase();
				if ( dl === '1' || dl === 'true' || dl === 'yes' ) return;

				// Respect explicit adapters.
				if ( ctrl.dataset?.value_from || ctrl.dataset?.valueFrom ) return;

				const raw      = sel.dataset ? sel.dataset[key] : undefined;
				const hasRaw   = sel.dataset ? hasOwn( sel.dataset, key ) : false;
				const defValue = getDefault( key );

				// Best-effort control typing with schema default fallback when value is absent.

				if ( ctrl instanceof HTMLInputElement && (ctrl.type === 'checkbox' || ctrl.type === 'radio') ) {
					// If dataset is missing the key entirely -> use schema default (boolean).
					if ( !hasRaw ) {
						ctrl.checked = !!defValue;
					} else {
						ctrl.checked = Core.WPBC_BFB_Sanitize.coerce_boolean( raw, !!defValue );
					}
				} else if ( 'value' in ctrl ) {
					if ( hasRaw ) {
						ctrl.value = (raw != null) ? String( raw ) : '';
					} else {
						ctrl.value = (defValue == null) ? '' : String( defValue );
					}
				}
			} );
		}

		_bind_id_sanitizer() {
			const b   = this.builder;
			const ins = document.getElementById( 'wpbc_bfb__inspector' );
			if ( ! ins ) {
				return;
			}
			if ( ins.__wpbc_bfb_id_sanitizer_bound ) {
				return;
			}
			ins.__wpbc_bfb_id_sanitizer_bound = true;

			const handler = (e) => {

				const t = e.target;
				if ( !t || !('value' in t) ) {
					return;
				}
				const key       = (t.dataset?.inspectorKey || '').toLowerCase();
				const sel       = b.get_selected_field?.();
				const isSection = sel?.classList?.contains( 'wpbc_bfb__section' );
				if ( !sel ) return;

				// Unified emitter that always includes the element reference.
				const EV              = Core.WPBC_BFB_Events;
				// STRUCTURE_CHANGE can be "expensive" because other listeners may trigger full canvas refresh.
				// Debounce only continuous controls (e.g. value slider scrubbing) on the INPUT phase.
				const ensure_sc_debounce_state = () => {
					if ( b.__wpbc_bfb_sc_debounce_state ) {
						return b.__wpbc_bfb_sc_debounce_state;
					}
					b.__wpbc_bfb_sc_debounce_state = { timer_id: 0, pending_payload: null };
					return b.__wpbc_bfb_sc_debounce_state;
				};

				const cancel_sc_debounced_emit = () => {
					const st = b.__wpbc_bfb_sc_debounce_state;
					if ( !st ) return;
					try { clearTimeout( st.timer_id ); } catch ( _ ) {}
					st.timer_id        = 0;
					st.pending_payload = null;
				};

				const bus_emit_change = (reason, extra = {}) => {
					// If we’re committing something (change/blur/etc), drop any pending "input" emit.
					cancel_sc_debounced_emit();
					b.bus?.emit?.( EV.STRUCTURE_CHANGE, { reason, el: sel, ...extra } );
				};

				const bus_emit_change_debounced = (reason, extra = {}, wait_ms) => {
					const st = ensure_sc_debounce_state();
					const ms = Number.isFinite( wait_ms )
						? wait_ms
						: (Number.isFinite( UI.STRUCTURE_CHANGE_DEBOUNCE_MS ) ? UI.STRUCTURE_CHANGE_DEBOUNCE_MS : 240);

					// Capture the CURRENT selected element into the payload now (stable ref).
					st.pending_payload = { reason, el: sel, ...extra, debounced: true };

					try { clearTimeout( st.timer_id ); } catch ( _ ) {}
					st.timer_id = setTimeout( function () {
						st.timer_id = 0;
						const payload = st.pending_payload;
						st.pending_payload = null;
						if ( payload ) {
							b.bus?.emit?.( EV.STRUCTURE_CHANGE, payload );
						}
					}, ms );
				};

				// ---- FIELD/SECTION: internal id ----
				if ( key === 'id' ) {
					const unique = b.id.set_field_id( sel, t.value );
					if ( b.preview_mode && !isSection ) {
						b.render_preview( sel );
					}
					if ( t.value !== unique ) {
						t.value = unique;
					}
					bus_emit_change( 'id-change' );
					return;
				}

				// ---- FIELD/SECTION: public HTML id ----
				if ( key === 'html_id' ) {
					const applied = b.id.set_field_html_id( sel, t.value );
					// For sections, also set the real DOM id so anchors/CSS can target it.
					if ( isSection ) {
						sel.id = applied || '';
					} else if ( b.preview_mode ) {
						b.render_preview( sel );
					}
					if ( t.value !== applied ) {
						t.value = applied;
					}
					bus_emit_change( 'html-id-change' );
					return;
				}

				// ---- FIELDS ONLY: name ----
				if ( key === 'name' && !isSection ) {

					// Live typing: sanitize only (NO uniqueness yet) to avoid "-2" spam
					if ( e.type === 'input' ) {
						const before    = t.value;
						const sanitized = Core.WPBC_BFB_Sanitize.sanitize_html_name( before );
						if ( before !== sanitized ) {
							// optional: preserve caret to avoid jump
							const selStart = t.selectionStart, selEnd = t.selectionEnd;
							t.value        = sanitized;
							try {
								t.setSelectionRange( selStart, selEnd );
							} catch ( _ ) {
							}
						}
						return; // uniqueness on change/blur
					}

					// Commit (change/blur)
					const raw = String( t.value ?? '' ).trim();

					if ( !raw ) {
						// RESEED: keep name non-empty and provisional (autoname stays ON)
						const S    = Core.WPBC_BFB_Sanitize;
						const base = S.sanitize_html_name( sel.getAttribute( 'data-id' ) || sel.dataset.id || sel.dataset.type || 'field' );
						const uniq = b.id.ensure_unique_field_name( base, sel );

						sel.setAttribute( 'data-name', uniq );
						sel.dataset.autoname          = '1';
						sel.dataset.name_user_touched = '0';

						// Keep DOM in sync if we’re not re-rendering
						if ( !b.preview_mode ) {
							const ctrl = sel.querySelector( 'input,textarea,select' );
							if ( ctrl ) ctrl.setAttribute( 'name', uniq );
						} else {
							b.render_preview( sel );
						}

						if ( t.value !== uniq ) t.value = uniq;
						bus_emit_change( 'name-reseed' );
						return;
					}

					// Non-empty commit: user takes control; disable autoname going forward
					sel.dataset.name_user_touched = '1';
					sel.dataset.autoname          = '0';

					const sanitized = Core.WPBC_BFB_Sanitize.sanitize_html_name( raw );
					const unique    = b.id.set_field_name( sel, sanitized );

					if ( !b.preview_mode ) {
						const ctrl = sel.querySelector( 'input,textarea,select' );
						if ( ctrl ) ctrl.setAttribute( 'name', unique );
					} else {
						b.render_preview( sel );
					}

					if ( t.value !== unique ) t.value = unique;
					bus_emit_change( 'name-change' );
					return;
				}

				// ---- SECTIONS & FIELDS: cssclass (live apply; no re-render) ----
				if ( key === 'cssclass' ) {
					const next       = Core.WPBC_BFB_Sanitize.sanitize_css_classlist( t.value || '' );
					const desiredArr = next.split( /\s+/ ).filter( Boolean );
					const desiredSet = new Set( desiredArr );

					// Core classes are never touched.
					const isCore = (cls) => cls === 'is-selected' || cls.startsWith( 'wpbc_' );

					// Snapshot before mutating (DOMTokenList is live).
					const beforeClasses = Array.from( sel.classList );
					const customBefore  = beforeClasses.filter( (c) => !isCore( c ) );

					// Remove stray non-core classes not in desired.
					customBefore.forEach( (c) => {
						if ( !desiredSet.has( c ) ) sel.classList.remove( c );
					} );

					// Add missing desired classes in one go.
					const missing = desiredArr.filter( (c) => !customBefore.includes( c ) );
					if ( missing.length ) sel.classList.add( ...missing );

					// Keep dataset in sync (avoid useless attribute writes).
					if ( sel.getAttribute( 'data-cssclass' ) !== next ) {
						sel.setAttribute( 'data-cssclass', next );
					}

					// Emit only if something actually changed.
					const afterClasses = Array.from( sel.classList );
					const changed      = afterClasses.length !== beforeClasses.length || beforeClasses.some( (c, i) => c !== afterClasses[i] );

					const detail = { key: 'cssclass', phase: e.type };
					if ( isSection ) {
						bus_emit_change( 'cssclass-change', detail );
					} else {
						bus_emit_change( 'prop-change', detail );
					}
					return;
				}


				// ---- SECTIONS: label ----
				if ( isSection && key === 'label' ) {
					const val = String( t.value ?? '' );
					sel.setAttribute( 'data-label', val );
					bus_emit_change( 'label-change' );
					return;
				}

				// ---- FIELDS: label (auto-name while typing; freeze on commit) ----
				if ( !isSection && key === 'label' ) {
					const val         = String( t.value ?? '' );
					sel.dataset.label = val;

					// while typing, allow auto-name (if flags permit)
					try {
						Core.WPBC_BFB_Field_Base.maybe_autoname_from_label( b, sel, val );
					} catch ( _ ) {
					}

					// if user committed the label (blur/change), freeze future auto-name
					if ( e.type !== 'input' ) {
						sel.dataset.autoname = '0';   // stop future label->name sync
						sel.dataset.fresh    = '0';   // also kill the "fresh" escape hatch
					}

					// Optional UI nicety: disable Name when auto is ON, enable when OFF
					const ins      = document.getElementById( 'wpbc_bfb__inspector' );
					const nameCtrl = ins?.querySelector( '[data-inspector-key="name"]' );
					if ( nameCtrl ) {
						const autoActive =
								  (sel.dataset.autoname ?? '1') !== '0' &&
								  sel.dataset.name_user_touched !== '1' &&
								  sel.dataset.was_loaded !== '1';
						nameCtrl.toggleAttribute( 'disabled', autoActive );
						if ( autoActive && !nameCtrl.placeholder ) {
							nameCtrl.placeholder = b?.i18n?.auto_from_label ?? 'auto — from label';
						}
						if ( !autoActive && nameCtrl.placeholder === (b?.i18n?.auto_from_label ?? 'auto — from label') ) {
							nameCtrl.placeholder = '';
						}
					}

					// Always re-render the preview so label changes are visible immediately.
					b.render_preview( sel );
					bus_emit_change( 'label-change' );
					return;
				}


				// ---- DEFAULT (GENERIC): dataset writer for both fields & sections ----
				// Any inspector control with [data-inspector-key] that doesn't have a custom
				// adapter/value_from will simply read/write sel.dataset[key].
				if ( key ) {

					const selfLocked = /^(1|true|yes)$/i.test( (t.dataset?.locked || '').trim() );
					if ( selfLocked ) {
						return;
					}

					// Skip keys we handled above to avoid double work.
					if ( key === 'id' || key === 'name' || key === 'html_id' || key === 'cssclass' || key === 'label' ) {
						return;
					}
					let nextVal = '';
					if ( t instanceof HTMLInputElement && (t.type === 'checkbox' || t.type === 'radio') ) {
						nextVal = t.checked ? '1' : '';
					} else if ( 'value' in t ) {
						nextVal = String( t.value ?? '' );
					}
					// Persist to dataset.
					if ( sel?.dataset ) sel.dataset[key] = nextVal;

					// Generator controls are "UI inputs" — avoid STRUCTURE_CHANGE spam while dragging/typing.
					const is_gen_key = (key.indexOf( 'gen_' ) === 0);

					// Re-render on visual keys so preview stays in sync (calendar label/help, etc.).
					const visualKeys = new Set( [ 'help', 'placeholder', 'min_width', 'cssclass' ] );
					if ( !isSection && (visualKeys.has( key ) || key.startsWith( 'ui_' )) ) {
						// Light heuristic: only re-render on commit for heavy inputs; live for short ones is fine.
						if ( e.type === 'change' || key === 'help' || key === 'placeholder' ) {
							b.render_preview( sel );
						}
					}

					if ( !(is_gen_key && e.type === 'input') ) {
						// Debounce continuous value slider input events to avoid full-canvas refresh spam.
						// We detect the slider group via [data-len-group] wrapper.
						const is_len_group_ctrl = !!(t && t.closest && t.closest( '[data-len-group]' ));

						if ( is_len_group_ctrl && e.type === 'input' ) {
							bus_emit_change_debounced( 'prop-change', { key, phase: e.type } );
						} else {
							bus_emit_change( 'prop-change', { key, phase: e.type } );
						}
					}
					return;
				}
			};

			ins.addEventListener( 'change', handler, true );
			// reflect instantly while typing as well.
			ins.addEventListener( 'input', handler, true );
		}

		/**
		 * Open Inspector after a field is added.
		 * @private
		 */
		_open_inspector_after_field_added() {
			const EV = Core.WPBC_BFB_Events;
			this.builder?.bus?.on?.( EV.FIELD_ADD, (e) => {
				const el = e?.detail?.el || null;
				if ( el && this.builder?.select_field ) {
					this.builder.select_field( el, { scrollIntoView: true } );
				}
				// Show Inspector Palette.
				document.dispatchEvent( new CustomEvent( 'wpbc_bfb:show_panel', {
					detail: {
						panel_id: 'wpbc_bfb__inspector',
						tab_id  : 'wpbc_tab_inspector'
					}
				} ) );
			} );
		}
	};

	/**
	 * Keyboard shortcuts for selection, deletion, and movement.
	 */
	UI.WPBC_BFB_Keyboard_Controller = class extends UI.WPBC_BFB_Module {
		init() {
			this._on_key = this.on_key.bind( this );
			document.addEventListener( 'keydown', this._on_key, true );
		}

		destroy() {
			document.removeEventListener( 'keydown', this._on_key, true );
		}

		/** @param {KeyboardEvent} e */
		on_key(e) {
			const b         = this.builder;
			const is_typing = this._is_typing_anywhere();
			if ( e.key === 'Escape' ) {
				if ( is_typing ) {
					return;
				}
				this.builder.bus.emit( Core.WPBC_BFB_Events.CLEAR_SELECTION, { source: 'esc' } );
				return;
			}
			const selected = b.get_selected_field?.();
			if ( !selected || is_typing ) {
				return;
			}
			if ( e.key === 'Delete' || e.key === 'Backspace' ) {
				e.preventDefault();
				b.delete_item?.( selected );
				return;
			}
			if ( (e.altKey || e.ctrlKey || e.metaKey) && (e.key === 'ArrowUp' || e.key === 'ArrowDown') && !e.shiftKey ) {
				e.preventDefault();
				const dir = (e.key === 'ArrowUp') ? 'up' : 'down';
				b.move_item?.( selected, dir );
				return;
			}
			if ( e.key === 'Enter' ) {
				e.preventDefault();
				b.select_field( selected, { scrollIntoView: true } );
			}
		}

		/** @returns {boolean} */
		_is_typing_anywhere() {
			const a   = document.activeElement;
			const tag = a?.tagName;
			if ( tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (a?.isContentEditable === true) ) {
				return true;
			}
			const ins = document.getElementById( 'wpbc_bfb__inspector' );
			return !!(ins && a && ins.contains( a ));
		}
	};

	/**
	 * Column resize logic for section rows.
	 */
	UI.WPBC_BFB_Resize_Controller = class extends UI.WPBC_BFB_Module {
		init() {
			this.builder.init_resize_handler = this.handle_resize.bind( this );
		}

		/**
		 * read the CSS var (kept local so it doesn’t depend on the Min-Width module)
		 *
		 * @param col
		 * @returns {number|number}
		 * @private
		 */
		_get_col_min_px(col) {
			const v = getComputedStyle( col ).getPropertyValue( '--wpbc-col-min' ) || '0';
			const n = parseFloat( v );
			return Number.isFinite( n ) ? Math.max( 0, n ) : 0;
		}

		/** @param {MouseEvent} e */
		handle_resize(e) {
			const b = this.builder;
			e.preventDefault();
			if ( e.button !== 0 ) return;

			const resizer   = e.currentTarget;
			const row_el    = resizer.parentElement;
			const cols      = Array.from( row_el.querySelectorAll( ':scope > .wpbc_bfb__column' ) );
			const left_col  = resizer?.previousElementSibling;
			const right_col = resizer?.nextElementSibling;
			if ( !left_col || !right_col || !left_col.classList.contains( 'wpbc_bfb__column' ) || !right_col.classList.contains( 'wpbc_bfb__column' ) ) return;

			const left_index  = cols.indexOf( left_col );
			const right_index = cols.indexOf( right_col );
			if ( left_index === -1 || right_index !== left_index + 1 ) return;

			const start_x        = e.clientX;
			const left_start_px  = left_col.getBoundingClientRect().width;
			const right_start_px = right_col.getBoundingClientRect().width;
			const pair_px        = Math.max( 0, left_start_px + right_start_px );

			const gp         = b.col_gap_percent;
			const computed   = b.layout.compute_effective_bases_from_row( row_el, gp );
			const available  = computed.available;                 // % of the “full 100” after gaps
			const bases      = computed.bases.slice( 0 );            // current effective %
			const pair_avail = bases[left_index] + bases[right_index];

			// Bail if we can’t compute sane deltas.
			if (!pair_px || !Number.isFinite(pair_avail) || pair_avail <= 0) return;

			// --- MIN CLAMPS (pixels) -------------------------------------------------
			const pctToPx       = (pct) => (pair_px * (pct / pair_avail)); // pair-local percent -> px
			const genericMinPct = Math.min( 0.1, available );                  // original 0.1% floor (in “available %” space)
			const genericMinPx  = pctToPx( genericMinPct );

			const leftMinPx  = Math.max( this._get_col_min_px( left_col ), genericMinPx );
			const rightMinPx = Math.max( this._get_col_min_px( right_col ), genericMinPx );

			// freeze text selection + cursor
			const prev_user_select         = document.body.style.userSelect;
			document.body.style.userSelect = 'none';
			row_el.style.cursor            = 'col-resize';

			const on_mouse_move = (ev) => {
				if ( !pair_px ) return;

				// work in pixels, clamp by each side’s min
				const delta_px   = ev.clientX - start_x;
				let newLeftPx    = left_start_px + delta_px;
				newLeftPx        = Math.max( leftMinPx, Math.min( pair_px - rightMinPx, newLeftPx ) );
				const newRightPx = pair_px - newLeftPx;

				// translate back to pair-local percentages
				const newLeftPct      = (newLeftPx / pair_px) * pair_avail;
				const newBases        = bases.slice( 0 );
				newBases[left_index]  = newLeftPct;
				newBases[right_index] = pair_avail - newLeftPct;

				b.layout.apply_bases_to_row( row_el, newBases );
			};

			const on_mouse_up = () => {
				document.removeEventListener( 'mousemove', on_mouse_move );
				document.removeEventListener( 'mouseup', on_mouse_up );
				window.removeEventListener( 'mouseup', on_mouse_up );
				document.removeEventListener( 'mouseleave', on_mouse_up );
				document.body.style.userSelect = prev_user_select || '';
				row_el.style.cursor            = '';

				// normalize to the row’s available % again
				const normalized = b.layout.compute_effective_bases_from_row( row_el, gp );
				b.layout.apply_bases_to_row( row_el, normalized.bases );
			};

			document.addEventListener( 'mousemove', on_mouse_move );
			document.addEventListener( 'mouseup', on_mouse_up );
			window.addEventListener( 'mouseup', on_mouse_up );
			document.addEventListener( 'mouseleave', on_mouse_up );
		}

	};

	/**
	 * Page and section creation, rebuilding, and nested Sortable setup.
	 */
	UI.WPBC_BFB_Pages_Sections = class extends UI.WPBC_BFB_Module {

		init() {
			this.builder.add_page                  = (opts) => this.add_page( opts );
			this.builder.add_section               = (container, cols) => this.add_section( container, cols );
			this.builder.rebuild_section           = (section_data, container) => this.rebuild_section( section_data, container );
			this.builder.init_all_nested_sortables = (el) => this.init_all_nested_sortables( el );
			this.builder.init_section_sortable     = (el) => this.init_section_sortable( el );
			this.builder.pages_sections            = this;
		}

		/**
		 * Give every field/section in a cloned subtree a fresh data-uid so
		 * uniqueness checks don't exclude their originals.
		 */
		_retag_uids_in_subtree(root) {
			const b = this.builder;
			if ( !root ) return;
			const nodes = [];
			if ( root.classList?.contains( 'wpbc_bfb__section' ) || root.classList?.contains( 'wpbc_bfb__field' ) ) {
				nodes.push( root );
			}
			nodes.push( ...root.querySelectorAll( '.wpbc_bfb__section, .wpbc_bfb__field' ) );
			nodes.forEach( (el) => {
				const prefix   = el.classList.contains( 'wpbc_bfb__section' ) ? 's' : 'f';
				el.dataset.uid = `${prefix}-${++b._uid_counter}-${Date.now()}-${Math.random().toString( 36 ).slice( 2, 7 )}`;
			} );
		}

		/**
		 * Bump "foo", "foo-2", "foo-3", ...
		 */
		_make_unique(base, taken) {
			const s = Core.WPBC_BFB_Sanitize;
			let v   = String( base || '' );
			if ( !v ) v = 'field';
			const m  = v.match( /-(\d+)$/ );
			let n    = m ? (parseInt( m[1], 10 ) || 1) : 1;
			let stem = m ? v.replace( /-\d+$/, '' ) : v;
			while ( taken.has( v ) ) {
				n = Math.max( 2, n + 1 );
				v = `${stem}-${n}`;
			}
			taken.add( v );
			return v;
		}

		/**
		 * Strict, one-pass de-duplication for a newly-inserted subtree.
		 * - Ensures unique data-id (internal), data-name (fields), data-html_id (public)
		 * - Also updates DOM: <section id>, <input id>, <label for>, and input[name].
		 */
		_dedupe_subtree_strict(root) {
			const b = this.builder;
			const s = Core.WPBC_BFB_Sanitize;
			if ( !root || !b?.pages_container ) return;

			// 1) Build "taken" sets from outside the subtree.
			const takenDataId   = new Set();
			const takenDataName = new Set();
			const takenHtmlId   = new Set();
			const takenDomId    = new Set();

			// All fields/sections outside root
			b.pages_container.querySelectorAll( '.wpbc_bfb__field, .wpbc_bfb__section' ).forEach( el => {
				if ( root.contains( el ) ) return;
				const did  = el.getAttribute( 'data-id' );
				const dnam = el.getAttribute( 'data-name' );
				const hid  = el.getAttribute( 'data-html_id' );
				if ( did ) takenDataId.add( did );
				if ( dnam ) takenDataName.add( dnam );
				if ( hid ) takenHtmlId.add( hid );
			} );

			// All DOM ids outside root (labels, inputs, anything)
			document.querySelectorAll( '[id]' ).forEach( el => {
				if ( root.contains( el ) ) return;
				if ( el.id ) takenDomId.add( el.id );
			} );

			const nodes = [];
			if ( root.classList?.contains( 'wpbc_bfb__section' ) || root.classList?.contains( 'wpbc_bfb__field' ) ) {
				nodes.push( root );
			}
			nodes.push( ...root.querySelectorAll( '.wpbc_bfb__section, .wpbc_bfb__field' ) );

			// 2) Walk the subtree and fix collisions deterministically.
			nodes.forEach( el => {
				const isField   = el.classList.contains( 'wpbc_bfb__field' );
				const isSection = el.classList.contains( 'wpbc_bfb__section' );

				// INTERNAL data-id
				{
					const raw  = el.getAttribute( 'data-id' ) || '';
					const base = s.sanitize_html_id( raw ) || (isSection ? 'section' : 'field');
					const uniq = this._make_unique( base, takenDataId );
					if ( uniq !== raw ) el.setAttribute( 'data-id', uniq );
				}

				// HTML name (fields only)
				if ( isField ) {
					const raw = el.getAttribute( 'data-name' ) || '';
					if ( raw ) {
						const base = s.sanitize_html_name( raw );
						const uniq = this._make_unique( base, takenDataName );
						if ( uniq !== raw ) {
							el.setAttribute( 'data-name', uniq );
							// Update inner control immediately
							const input = el.querySelector( 'input, textarea, select' );
							if ( input ) input.setAttribute( 'name', uniq );
						}
					}
				}

				// Public HTML id (fields + sections)
				{
					const raw = el.getAttribute( 'data-html_id' ) || '';
					if ( raw ) {
						const base          = s.sanitize_html_id( raw );
						// Reserve against BOTH known data-html_id and real DOM ids.
						const combinedTaken = new Set( [ ...takenHtmlId, ...takenDomId ] );
						let candidate       = this._make_unique( base, combinedTaken );
						// Record into the real sets so future checks see the reservation.
						takenHtmlId.add( candidate );
						takenDomId.add( candidate );

						if ( candidate !== raw ) el.setAttribute( 'data-html_id', candidate );

						// Reflect to DOM immediately
						if ( isSection ) {
							el.id = candidate || '';
						} else {
							const input = el.querySelector( 'input, textarea, select' );
							const label = el.querySelector( 'label.wpbc_bfb__field-label' );
							if ( input ) input.id = candidate || '';
							if ( label ) label.htmlFor = candidate || '';
						}
					} else if ( isSection ) {
						// Ensure no stale DOM id if data-html_id was cleared
						el.removeAttribute( 'id' );
					}
				}
			} );
		}

		_make_add_columns_control(page_el, section_container, insert_pos = 'bottom') {

			// Accept insert_pos ('top'|'bottom'), default 'bottom'.

			const tpl = document.getElementById( 'wpbc_bfb__add_columns_template' );
			if ( !tpl ) {
				return null;
			}

			// Clone *contents* (not the id), unhide, and add a page-scoped class.
			const src = (tpl.content && tpl.content.firstElementChild) ? tpl.content.firstElementChild : tpl.firstElementChild;
			if ( !src ) {
				return null;
			}

			const clone = src.cloneNode( true );
			clone.removeAttribute( 'hidden' );
			if ( clone.id ) {
				clone.removeAttribute( 'id' );
			}
			clone.querySelectorAll( '[id]' ).forEach( n => n.removeAttribute( 'id' ) );

			// Mark where this control inserts sections.
			clone.dataset.insert = insert_pos; // 'top' | 'bottom'

			// // Optional UI hint for users (keeps existing markup intact).
			// const hint = clone.querySelector( '.nav-tab-text .selected_value' );
			// if ( hint ) {
			// 	hint.textContent = (insert_pos === 'top') ? ' (add at top)' : ' (add at bottom)';
			// }

			// Click on options - add section with N columns.
			clone.addEventListener( 'click', (e) => {
				const a = e.target.closest( '.ul_dropdown_menu_li_action_add_sections' );
				if ( !a ) {
					return;
				}
				e.preventDefault();

				// Read N either from data-cols or fallback to parsing text like "3 Columns".
				let cols = parseInt( a.dataset.cols || (a.textContent.match( /\b(\d+)\s*Column/i )?.[1] ?? '1'), 10 );
				cols     = Math.max( 1, Math.min( 4, cols ) );

				// NEW: honor the control's insertion position
				this.add_section( section_container, cols, insert_pos );

				// Reflect last choice (unchanged)
				const val = clone.querySelector( '.selected_value' );
				if ( val ) {
					val.textContent = ` (${cols})`;
				}
			} );

			return clone;
		}

		/**
		 * @param {{scroll?: boolean}} [opts = {}]
		 * @returns {HTMLElement}
		 */
		add_page({ scroll = true } = {}) {
			const b       = this.builder;
			const page_el = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__panel wpbc_bfb__panel--preview  wpbc_bfb_form wpbc_container wpbc_form wpbc_container_booking_form' );
			page_el.setAttribute( 'data-page', ++b.page_counter );

			// "Page 1 | X" - Render page Title with Remove X button.
			const controls_html = UI.render_wp_template( 'wpbc-bfb-tpl-page-remove', { page_number: b.page_counter } );
			page_el.innerHTML   = controls_html + '<div class="wpbc_bfb__form_preview_section_container wpbc_wizard__border_container"></div>';

			b.pages_container.appendChild( page_el );
			if ( scroll ) {
				page_el.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}

			const section_container         = page_el.querySelector( '.wpbc_bfb__form_preview_section_container' );
			const section_count_on_add_page = 2;
			this.init_section_sortable( section_container );
			this.add_section( section_container, section_count_on_add_page );

			// Dropdown control cloned from the hidden template.
			const controls_host_top = page_el.querySelector( '.wpbc_bfb__controls' );
			const ctrl_top          = this._make_add_columns_control( page_el, section_container, 'top' );
			if ( ctrl_top ) {
				controls_host_top.appendChild( ctrl_top );
			}
			// Bottom control bar after the section container.
			const controls_host_bottom = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__controls wpbc_bfb__controls--bottom' );
			section_container.after( controls_host_bottom );
			const ctrl_bottom = this._make_add_columns_control( page_el, section_container, 'bottom' );
			if ( ctrl_bottom ) {
				controls_host_bottom.appendChild( ctrl_bottom );
			}

			return page_el;
		}

		/**
		 * @param {HTMLElement} container
		 * @param {number}      cols
		 * @param {'top'|'bottom'} [insert_pos='bottom']  // NEW
		 */
		add_section(container, cols, insert_pos = 'bottom') {
			const b = this.builder;
			cols    = Math.max( 1, parseInt( cols, 10 ) || 1 );

			const section = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__section' );
			section.setAttribute( 'data-id', `section-${++b.section_counter}-${Date.now()}` );
			section.setAttribute( 'data-uid', `s-${++b._uid_counter}-${Date.now()}-${Math.random().toString( 36 ).slice( 2, 7 )}` );
			section.setAttribute( 'data-type', 'section' );
			section.setAttribute( 'data-label', 'Section' );
			section.setAttribute( 'data-columns', String( cols ) );
			// Do not persist or seed per-column styles by default (opt-in via inspector).

			const row = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__row wpbc__row' );
			for ( let i = 0; i < cols; i++ ) {
				const col           = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__column wpbc__field' );
				col.style.flexBasis = (100 / cols) + '%';
				// No default CSS vars here; real columns remain unaffected until user activates styles.
				b.init_sortable?.( col );
				row.appendChild( col );
				if ( i < cols - 1 ) {
					const resizer = Core.WPBC_Form_Builder_Helper.create_element( 'div', 'wpbc_bfb__column-resizer' );
					resizer.addEventListener( 'mousedown', b.init_resize_handler );
					row.appendChild( resizer );
				}
			}
			section.appendChild( row );
			b.layout.set_equal_bases( row, b.col_gap_percent );
			b.add_overlay_toolbar( section );
			section.setAttribute( 'tabindex', '0' );
			this.init_all_nested_sortables( section );

			// Insertion policy: top | bottom.
			if ( insert_pos === 'top' && container.firstElementChild ) {
				container.insertBefore( section, container.firstElementChild );
			} else {
				container.appendChild( section );
			}
		}

		/**
		 * @param {Object} section_data
		 * @param {HTMLElement} container
		 * @returns {HTMLElement} The rebuilt section element.
		 */
		rebuild_section(section_data, container) {
			const b         = this.builder;
			const cols_data = Array.isArray( section_data?.columns ) ? section_data.columns : [];
			this.add_section( container, cols_data.length || 1 );
			const section = container.lastElementChild;
			if ( !section.dataset.uid ) {
				section.setAttribute( 'data-uid', `s-${++b._uid_counter}-${Date.now()}-${Math.random().toString( 36 ).slice( 2, 7 )}` );
			}
			section.setAttribute( 'data-id', section_data?.id || `section-${++b.section_counter}-${Date.now()}` );
			section.setAttribute( 'data-type', 'section' );
			section.setAttribute( 'data-label', section_data?.label || 'Section' );
			section.setAttribute( 'data-columns', String( (section_data?.columns || []).length || 1 ) );
			// Persisted attributes
			if ( section_data?.html_id ) {
				section.setAttribute( 'data-html_id', String( section_data.html_id ) );
				// give the container a real id so anchors/CSS can target it
				section.id = String( section_data.html_id );
			}

			// NEW: restore persisted per-column styles (raw JSON string).
			if ( section_data?.col_styles != null ) {
				const json = String( section_data.col_styles );
				section.setAttribute( 'data-col_styles', json );
				try {
					section.dataset.col_styles = json;
				} catch ( _e ) {
				}
			}
			// (No render_preview() call here on purpose: sections’ builder DOM uses .wpbc_bfb__row/.wpbc_bfb__column.)


			if ( section_data?.cssclass ) {
				section.setAttribute( 'data-cssclass', String( section_data.cssclass ) );
				// keep core classes, then add custom class(es)
				String( section_data.cssclass ).split( /\s+/ ).filter( Boolean ).forEach( cls => section.classList.add( cls ) );
			}

			const row = section.querySelector( '.wpbc_bfb__row' );
			// Delegate parsing + activation + application to the Column Styles service.
			try {
				const json = section.getAttribute( 'data-col_styles' )
					|| (section.dataset ? (section.dataset.col_styles || '') : '');
				const arr  = UI.WPBC_BFB_Column_Styles.parse_col_styles( json );
				UI.WPBC_BFB_Column_Styles.apply( section, arr );
			} catch ( _e ) {
			}

			cols_data.forEach( (col_data, index) => {
				const columns_only  = row.querySelectorAll( ':scope > .wpbc_bfb__column' );
				const col           = columns_only[index];
				col.style.flexBasis = col_data.width || '100%';
				(col_data.items || []).forEach( (item) => {
					if ( !item || !item.type ) {
						return;
					}
					if ( item.type === 'field' ) {
						const el = b.build_field( item.data );
						if ( el ) {
							col.appendChild( el );
							b.trigger_field_drop_callback( el, 'load' );
						}
						return;
					}
					if ( item.type === 'section' ) {
						this.rebuild_section( item.data, col );
					}
				} );
			} );
			const computed = b.layout.compute_effective_bases_from_row( row, b.col_gap_percent );
			b.layout.apply_bases_to_row( row, computed.bases );
			this.init_all_nested_sortables( section );

			// NEW: retag UIDs first (so uniqueness checks don't exclude originals), then dedupe all keys.
			this._retag_uids_in_subtree( section );
			this._dedupe_subtree_strict( section );
			return section;
		}

		/** @param {HTMLElement} container */
		init_all_nested_sortables(container) {
			const b = this.builder;
			if ( container.classList.contains( 'wpbc_bfb__form_preview_section_container' ) ) {
				this.init_section_sortable( container );
			}
			container.querySelectorAll( '.wpbc_bfb__section' ).forEach( (section) => {
				section.querySelectorAll( '.wpbc_bfb__column' ).forEach( (col) => {
					this.init_section_sortable( col );
				} );
			} );
		}

		/** @param {HTMLElement} container */
		init_section_sortable(container) {
			const b = this.builder;
			if ( !container ) {
				return;
			}
			const is_column    = container.classList.contains( 'wpbc_bfb__column' );
			const is_top_level = container.classList.contains( 'wpbc_bfb__form_preview_section_container' );
			if ( !is_column && !is_top_level ) {
				return;
			}
			b.init_sortable?.( container );
		}
	};

	/**
	 * Serialization and deserialization of pages/sections/fields.
	 */
	UI.WPBC_BFB_Structure_IO = class extends UI.WPBC_BFB_Module {
		init() {
			this.builder.get_structure        = () => this.serialize();
			this.builder.load_saved_structure = (s, opts) => this.deserialize( s, opts );
		}

		/** @returns {Array} */
		serialize() {
			const b = this.builder;
			this._normalize_ids();
			this._normalize_names();
			const pages = [];
			b.pages_container.querySelectorAll( '.wpbc_bfb__panel--preview' ).forEach( (page_el, page_index) => {
				const container = page_el.querySelector( '.wpbc_bfb__form_preview_section_container' );
				const content   = [];
				if ( !container ) {
					pages.push( { page: page_index + 1, content } );
					return;
				}
				container.querySelectorAll( ':scope > *' ).forEach( (child) => {
					if ( child.classList.contains( 'wpbc_bfb__section' ) ) {
						content.push( { type: 'section', data: this.serialize_section( child ) } );
						return;
					}
					if ( child.classList.contains( 'wpbc_bfb__field' ) ) {
						if ( child.classList.contains( 'is-invalid' ) ) {
							return;
						}
						const f_data = Core.WPBC_Form_Builder_Helper.get_all_data_attributes( child );
						// Drop ephemeral/editor-only flags
						[ 'uid', 'fresh', 'autoname', 'was_loaded', 'name_user_touched' ]
							.forEach( k => {
								if ( k in f_data ) delete f_data[k];
							} );
						content.push( { type: 'field', data: f_data } );
					}
				} );
				pages.push( { page: page_index + 1, content } );
			} );
			return pages;
		}

		/**
		 * @param {HTMLElement} section_el
		 * @returns {{id:string,label:string,html_id:string,cssclass:string,col_styles:string,columns:Array}}
		 */
		serialize_section(section_el) {
			const row = section_el.querySelector( ':scope > .wpbc_bfb__row' );

			// NEW: read per-column styles from dataset/attributes (underscore & hyphen)
			var col_styles_raw =
					section_el.getAttribute( 'data-col_styles' ) ||
					(section_el.dataset ? (section_el.dataset.col_styles) : '') ||
					'';

			const base = {
				id        : section_el.dataset.id,
				label     : section_el.dataset.label || '',
				html_id   : section_el.dataset.html_id || '',
				cssclass  : section_el.dataset.cssclass || '',
				col_styles: String( col_styles_raw )        // <-- NEW: keep as raw JSON string
			};

			if ( !row ) {
				return Object.assign( {}, base, { columns: [] } );
			}

			const columns = [];
			row.querySelectorAll( ':scope > .wpbc_bfb__column' ).forEach( function (col) {
				const width = col.style.flexBasis || '100%';
				const items = [];
				Array.prototype.forEach.call( col.children, function (child) {
					if ( child.classList.contains( 'wpbc_bfb__section' ) ) {
						items.push( { type: 'section', data: this.serialize_section( child ) } );
						return;
					}
					if ( child.classList.contains( 'wpbc_bfb__field' ) ) {
						if ( child.classList.contains( 'is-invalid' ) ) {
							return;
						}
						const f_data = Core.WPBC_Form_Builder_Helper.get_all_data_attributes( child );
						[ 'uid', 'fresh', 'autoname', 'was_loaded', 'name_user_touched' ].forEach( function (k) {
							if ( k in f_data ) {
								delete f_data[k];
							}
						} );
						items.push( { type: 'field', data: f_data } );
					}
				}.bind( this ) );
				columns.push( { width: width, items: items } );
			}.bind( this ) );

			// Clamp persisted col_styles to the actual number of columns on Save.
			try {
				const colCount = columns.length;
				const raw      = String( col_styles_raw || '' ).trim();

				if ( raw ) {
					let arr = [];
					try {
						const parsed = JSON.parse( raw );
						arr          = Array.isArray( parsed ) ? parsed : (parsed && Array.isArray( parsed.columns ) ? parsed.columns : []);
					} catch ( _e ) {
						arr = [];
					}

					if ( colCount <= 0 ) {
						base.col_styles = '[]';
					} else {
						if ( arr.length > colCount ) arr.length = colCount;
						while ( arr.length < colCount ) arr.push( {} );
						base.col_styles = JSON.stringify( arr );
					}
				} else {
					base.col_styles = '';
				}
			} catch ( _e ) {
			}

			return Object.assign( {}, base, { columns: columns } );
		}

		/**
		 * @param {Array} structure
		 * @param {{deferIfTyping?: boolean}} [opts = {}]
		 */
		deserialize(structure, { deferIfTyping = true } = {}) {
			const b = this.builder;
			if ( deferIfTyping && this._is_typing_in_inspector() ) {
				clearTimeout( this._defer_timer );
				this._defer_timer = setTimeout( () => {
					this.deserialize( structure, { deferIfTyping: false } );
				}, 150 );
				return;
			}
			b.pages_container.innerHTML = '';
			b.page_counter              = 0;
			(structure || []).forEach( (page_data) => {
				const page_el               = b.pages_sections.add_page( { scroll: false } );
				const section_container     = page_el.querySelector( '.wpbc_bfb__form_preview_section_container' );
				section_container.innerHTML = '';
				b.init_section_sortable?.( section_container );
				(page_data.content || []).forEach( (item) => {
					if ( item.type === 'section' ) {
						// Now returns the element; attributes (incl. col_styles) are applied inside rebuild.
						b.pages_sections.rebuild_section( item.data, section_container );
						return;
					}
					if ( item.type === 'field' ) {
						const el = b.build_field( item.data );
						if ( el ) {
							section_container.appendChild( el );
							b.trigger_field_drop_callback( el, 'load' );
						}
					}
				} );
			} );
			b.usage?.update_palette_ui?.();
			b.bus.emit( Core.WPBC_BFB_Events.STRUCTURE_LOADED, { structure } );
		}

		_normalize_ids() {
			const b = this.builder;
			b.pages_container.querySelectorAll( '.wpbc_bfb__panel--preview .wpbc_bfb__field:not(.is-invalid)' ).forEach( (el) => {
				const data = Core.WPBC_Form_Builder_Helper.get_all_data_attributes( el );
				const want = Core.WPBC_BFB_Sanitize.sanitize_html_id( data.id || '' ) || 'field';
				const uniq = b.id.ensure_unique_field_id( want, el );
				if ( data.id !== uniq ) {
					el.setAttribute( 'data-id', uniq );
					if ( b.preview_mode ) {
						b.render_preview( el );
					}
				}
			} );
		}

		_normalize_names() {
			const b = this.builder;
			b.pages_container.querySelectorAll( '.wpbc_bfb__panel--preview .wpbc_bfb__field:not(.is-invalid)' ).forEach( (el) => {
				const data = Core.WPBC_Form_Builder_Helper.get_all_data_attributes( el );
				const base = Core.WPBC_BFB_Sanitize.sanitize_html_name( (data.name != null) ? data.name : data.id ) || 'field';
				const uniq = b.id.ensure_unique_field_name( base, el );
				if ( data.name !== uniq ) {
					el.setAttribute( 'data-name', uniq );
					if ( b.preview_mode ) {
						b.render_preview( el );
					}
				}
			} );
		}

		/** @returns {boolean} */
		_is_typing_in_inspector() {
			const ins = document.getElementById( 'wpbc_bfb__inspector' );
			return !!(ins && document.activeElement && ins.contains( document.activeElement ));
		}
	};

	/**
	 * Minimal, standalone guard that enforces per-column min widths based on fields' data-min_width.
	 *
	 * @type {UI.WPBC_BFB_Min_Width_Guard}
	 */
	UI.WPBC_BFB_Min_Width_Guard = class extends UI.WPBC_BFB_Module {

		constructor(builder) {
			super( builder );
			this._on_field_add        = this._on_field_add.bind( this );
			this._on_field_remove     = this._on_field_remove.bind( this );
			this._on_structure_loaded = this._on_structure_loaded.bind( this );
			this._on_structure_change = this._on_structure_change.bind( this );
			this._on_window_resize    = this._on_window_resize.bind( this );

			this._pending_rows = new Set();
			this._pending_all  = false;
			this._raf_id       = 0;
		}

		init() {
			const EV = Core.WPBC_BFB_Events;
			this.builder?.bus?.on?.( EV.FIELD_ADD, this._on_field_add );
			this.builder?.bus?.on?.( EV.FIELD_REMOVE, this._on_field_remove );
			this.builder?.bus?.on?.( EV.STRUCTURE_LOADED, this._on_structure_loaded );
			// Refresh selectively on structure change (NOT on every prop input).
			this.builder?.bus?.on?.( EV.STRUCTURE_CHANGE, this._on_structure_change );

			window.addEventListener( 'resize', this._on_window_resize, { passive: true } );
			this._schedule_refresh_all();
		}

		destroy() {
			const EV = Core.WPBC_BFB_Events;
			this.builder?.bus?.off?.( EV.FIELD_ADD, this._on_field_add );
			this.builder?.bus?.off?.( EV.FIELD_REMOVE, this._on_field_remove );
			this.builder?.bus?.off?.( EV.STRUCTURE_LOADED, this._on_structure_loaded );
			this.builder?.bus?.off?.( EV.STRUCTURE_CHANGE, this._on_structure_change );
			window.removeEventListener( 'resize', this._on_window_resize );
		}

		_on_field_add(e) {
			this._schedule_refresh_all();
			// if you really want to be minimal work here, keep your row-only version.
		}

		_on_field_remove(e) {
			const src_el = e?.detail?.el || null;
			const row    = (src_el && src_el.closest) ? src_el.closest( '.wpbc_bfb__row' ) : null;
			if ( row ) {
				this._schedule_refresh_row( row );
			} else {
				this._schedule_refresh_all();
			}
		}

		_on_structure_loaded() {
			this._schedule_refresh_all();
		}

		_on_structure_change(e) {
			const reason = e?.detail?.reason || '';
			const key    = e?.detail?.key || '';

			// Ignore noisy prop changes that don't affect min widths.
			if ( reason === 'prop-change' && key !== 'min_width' ) {
				return;
			}

			const el  = e?.detail?.el || null;
			const row = el?.closest?.( '.wpbc_bfb__row' ) || null;
			if ( row ) {
				this._schedule_refresh_row( row );
			} else {
				this._schedule_refresh_all();
			}
		}

		_on_window_resize() {
			this._schedule_refresh_all();
		}

		_schedule_refresh_row(row_el) {
			if ( !row_el ) return;
			this._pending_rows.add( row_el );
			this._kick_raf();
		}

		_schedule_refresh_all() {
			this._pending_all = true;
			this._pending_rows.clear();
			this._kick_raf();
		}

		_kick_raf() {
			if ( this._raf_id ) return;
			this._raf_id = (window.requestAnimationFrame || setTimeout)( () => {
				this._raf_id = 0;
				if ( this._pending_all ) {
					this._pending_all = false;
					this.refresh_all();
					return;
				}
				const rows = Array.from( this._pending_rows );
				this._pending_rows.clear();
				rows.forEach( (r) => this.refresh_row( r ) );
			}, 0 );
		}


		refresh_all() {
			this.builder?.pages_container
				?.querySelectorAll?.( '.wpbc_bfb__row' )
				?.forEach?.( (row) => this.refresh_row( row ) );
		}

		refresh_row(row_el) {
			if ( !row_el ) return;

			const cols = row_el.querySelectorAll( ':scope > .wpbc_bfb__column' );

			// 1) Recalculate each column’s required min px and write it to the CSS var.
			cols.forEach( (col) => this.apply_col_min( col ) );

			// 2) Enforce it at the CSS level right away so layout can’t render narrower.
			cols.forEach( (col) => {
				const px           = parseFloat( getComputedStyle( col ).getPropertyValue( '--wpbc-col-min' ) || '0' ) || 0;
				col.style.minWidth = px > 0 ? Math.round( px ) + 'px' : '';
			} );

			// 3) Normalize current bases so the row respects all mins without overflow.
			try {
				const b   = this.builder;
				const gp  = b.col_gap_percent;
				const eff = b.layout.compute_effective_bases_from_row( row_el, gp );  // { bases, available }
				// Re-fit *current* bases against mins (same algorithm layout chips use).
				const fitted = UI.WPBC_BFB_Layout_Chips._fit_weights_respecting_min( b, row_el, eff.bases );
				if ( Array.isArray( fitted ) ) {
					const changed = fitted.some( (v, i) => Math.abs( v - eff.bases[i] ) > 0.01 );
					if ( changed ) {
						b.layout.apply_bases_to_row( row_el, fitted );
					}
				}
			} catch ( e ) {
				w._wpbc?.dev?.error?.( 'WPBC_BFB_Min_Width_Guard - refresh_row', e );
			}
		}

		apply_col_min(col_el) {
			if ( !col_el ) return;
			let max_px    = 0;
			const colRect = col_el.getBoundingClientRect();
			col_el.querySelectorAll( ':scope > .wpbc_bfb__field' ).forEach( (field) => {
				const raw = field.getAttribute( 'data-min_width' );
				let px    = 0;
				if ( raw ) {
					const s = String( raw ).trim().toLowerCase();
					if ( s.endsWith( '%' ) ) {
						const n = parseFloat( s );
						if ( Number.isFinite( n ) && colRect.width > 0 ) {
							px = (n / 100) * colRect.width;
						} else {
							px = 0;
						}
					} else {
						px = this.parse_len_px( s );
					}
				} else {
					const cs = getComputedStyle( field );
					px       = parseFloat( cs.minWidth || '0' ) || 0;
				}
				if ( px > max_px ) max_px = px;
			} );
			col_el.style.setProperty( '--wpbc-col-min', max_px > 0 ? Math.round( max_px ) + 'px' : '0px' );
		}

		parse_len_px(value) {
			if ( value == null ) return 0;
			const s = String( value ).trim().toLowerCase();
			if ( s === '' ) return 0;
			if ( s.endsWith( 'px' ) ) {
				const n = parseFloat( s );
				return Number.isFinite( n ) ? n : 0;
			}
			if ( s.endsWith( 'rem' ) || s.endsWith( 'em' ) ) {
				const n    = parseFloat( s );
				const base = parseFloat( getComputedStyle( document.documentElement ).fontSize ) || 16;
				return Number.isFinite( n ) ? n * base : 0;
			}
			const n = parseFloat( s );
			return Number.isFinite( n ) ? n : 0;
		}
	};

	/**
	 * WPBC_BFB_Toggle_Normalizer
	 *
	 * Converts plain checkboxes into toggle UI:
	 * <div class="inspector__control wpbc_ui__toggle">
	 *   <input type="checkbox" id="{unique}" data-inspector-key="..." class="inspector__input" role="switch" aria-checked="true|false">
	 *   <label class="wpbc_ui__toggle_icon"  for="{unique}"></label>
	 *   <label class="wpbc_ui__toggle_label" for="{unique}">Label</label>
	 * </div>
	 *
	 * - Skips inputs already inside `.wpbc_ui__toggle`.
	 * - Reuses an existing <label for="..."> text if present; otherwise falls back to nearby labels or attributes.
	 * - Auto-generates a unique id when absent.
	 */
	UI.WPBC_BFB_Toggle_Normalizer = class {

		/**
		 * Upgrade all raw checkboxes in a container to toggles.
		 * @param {HTMLElement} root_el
		 */
		static upgrade_checkboxes_in(root_el) {

			if ( !root_el || !root_el.querySelectorAll ) {
				return;
			}

			var inputs = root_el.querySelectorAll( 'input[type="checkbox"]' );
			if ( !inputs.length ) {
				return;
			}

			Array.prototype.forEach.call( inputs, function (input) {

				// 1) Skip if already inside toggle wrapper.
				if ( input.closest( '.wpbc_ui__toggle' ) ) {
					return;
				}
				// Skip rows / where input checkbox explicitly marked with  attribute 'data-wpbc-ui-no-toggle'.
				if ( input.hasAttribute( 'data-wpbc-ui-no-toggle' ) ) {
					return;
				}

				// 2) Ensure unique id; prefer existing.
				var input_id = input.getAttribute( 'id' );
				if ( !input_id ) {
					var key  = (input.dataset && input.dataset.inspectorKey) ? String( input.dataset.inspectorKey ) : 'opt';
					input_id = UI.WPBC_BFB_Toggle_Normalizer.generate_unique_id( 'wpbc_ins_auto_' + key + '_' );
					input.setAttribute( 'id', input_id );
				}

				// 3) Find best label text.
				var label_text = UI.WPBC_BFB_Toggle_Normalizer.resolve_label_text( root_el, input, input_id );

				// 4) Build the toggle wrapper.
				var wrapper       = document.createElement( 'div' );
				wrapper.className = 'inspector__control wpbc_ui__toggle';

				// Keep original input; just move it into wrapper.
				input.classList.add( 'inspector__input' );
				input.setAttribute( 'role', 'switch' );
				input.setAttribute( 'aria-checked', input.checked ? 'true' : 'false' );

				var icon_label       = document.createElement( 'label' );
				icon_label.className = 'wpbc_ui__toggle_icon';
				icon_label.setAttribute( 'for', input_id );

				var text_label       = document.createElement( 'label' );
				text_label.className = 'wpbc_ui__toggle_label';
				text_label.setAttribute( 'for', input_id );
				text_label.appendChild( document.createTextNode( label_text ) );

				// 5) Insert wrapper into DOM near the input.
				//    Preferred: replace the original labeled row if it matches typical inspector layout.
				var replaced = UI.WPBC_BFB_Toggle_Normalizer.try_replace_known_row( input, wrapper, label_text );

				if ( !replaced ) {
					if ( !input.parentNode ) return; // NEW guard
					// Fallback: just wrap the input in place and append labels.
					input.parentNode.insertBefore( wrapper, input );
					wrapper.appendChild( input );
					wrapper.appendChild( icon_label );
					wrapper.appendChild( text_label );
				}

				// 6) ARIA sync on change.
				input.addEventListener( 'change', function () {
					input.setAttribute( 'aria-checked', input.checked ? 'true' : 'false' );
				} );
			} );
		}

		/**
		 * Generate a unique id with a given prefix.
		 * @param {string} prefix
		 * @returns {string}
		 */
		static generate_unique_id(prefix) {
			var base = String( prefix || 'wpbc_ins_auto_' );
			var uid  = Math.random().toString( 36 ).slice( 2, 8 );
			var id   = base + uid;
			// Minimal collision guard in the current document scope.
			while ( document.getElementById( id ) ) {
				uid = Math.random().toString( 36 ).slice( 2, 8 );
				id  = base + uid;
			}
			return id;
		}

		/**
		 * Resolve the best human label for an input.
		 * Priority:
		 *  1) <label for="{id}">text</label>
		 *  2) nearest sibling/parent .inspector__label text
		 *  3) input.getAttribute('aria-label') || data-label || data-inspector-key || name || 'Option'
		 * @param {HTMLElement} root_el
		 * @param {HTMLInputElement} input
		 * @param {string} input_id
		 * @returns {string}
		 */
		static resolve_label_text(root_el, input, input_id) {
			// for= association
			if ( input_id ) {
				var assoc = root_el.querySelector( 'label[for="' + UI.WPBC_BFB_Toggle_Normalizer.css_escape( input_id ) + '"]' );
				if ( assoc && assoc.textContent ) {
					var txt = assoc.textContent.trim();
					// Remove the old label from DOM; its text will be used by toggle.
					assoc.parentNode && assoc.parentNode.removeChild( assoc );
					if ( txt ) {
						return txt;
					}
				}
			}

			// nearby inspector label
			var near_label = input.closest( '.inspector__row' );
			if ( near_label ) {
				var il = near_label.querySelector( '.inspector__label' );
				if ( il && il.textContent ) {
					var t2 = il.textContent.trim();
					// If this row had the standard label+control, drop the old text label to avoid duplicates.
					il.parentNode && il.parentNode.removeChild( il );
					if ( t2 ) {
						return t2;
					}
				}
			}

			// fallbacks
			var aria = input.getAttribute( 'aria-label' );
			if ( aria ) {
				return aria;
			}
			if ( input.dataset && input.dataset.label ) {
				return String( input.dataset.label );
			}
			if ( input.dataset && input.dataset.inspectorKey ) {
				return String( input.dataset.inspectorKey );
			}
			if ( input.name ) {
				return String( input.name );
			}
			return 'Option';
		}

		/**
		 * Try to replace a known inspector row pattern with a toggle wrapper.
		 * Patterns:
		 *  <div.inspector__row>
		 *    <label.inspector__label>Text</label>
		 *    <div.inspector__control> [input[type=checkbox]] </div>
		 *  </div>
		 *
		 * @param {HTMLInputElement} input
		 * @param {HTMLElement} wrapper
		 * @returns {boolean} replaced
		 */
		static try_replace_known_row(input, wrapper, label_text) {
			var row       = input.closest( '.inspector__row' );
			var ctrl_wrap = input.parentElement;

			if ( row && ctrl_wrap && ctrl_wrap.classList.contains( 'inspector__control' ) ) {
				// Clear control wrap and reinsert toggle structure.
				while ( ctrl_wrap.firstChild ) {
					ctrl_wrap.removeChild( ctrl_wrap.firstChild );
				}
				row.classList.add( 'inspector__row--toggle' );

				ctrl_wrap.classList.add( 'wpbc_ui__toggle' );
				ctrl_wrap.appendChild( input );

				var input_id       = input.getAttribute( 'id' );
				var icon_lbl       = document.createElement( 'label' );
				icon_lbl.className = 'wpbc_ui__toggle_icon';
				icon_lbl.setAttribute( 'for', input_id );

				var text_lbl       = document.createElement( 'label' );
				text_lbl.className = 'wpbc_ui__toggle_label';
				text_lbl.setAttribute( 'for', input_id );
				if ( label_text ) {
					text_lbl.appendChild( document.createTextNode( label_text ) );
				}
				// If the row previously had a .inspector__label (we removed it in resolve_label_text),
				// we intentionally do NOT recreate it; the toggle text label becomes the visible one.
				// The text content is already resolved in resolve_label_text() and set below by caller.

				ctrl_wrap.appendChild( icon_lbl );
				ctrl_wrap.appendChild( text_lbl );
				return true;
			}

			// Not a known pattern; caller will wrap in place.
			return false;
		}

		/**
		 * CSS.escape polyfill for selectors.
		 * @param {string} s
		 * @returns {string}
		 */
		static css_escape(s) {
			s = String( s );
			if ( window.CSS && typeof window.CSS.escape === 'function' ) {
				return window.CSS.escape( s );
			}
			return s.replace( /([^\w-])/g, '\\$1' );
		}
	};

	/**
	 * Apply all UI normalizers/enhancers to a container (post-render).
	 * Keep this file small and add more normalizers later in one place.
	 *
	 * @param {HTMLElement} root
	 */
	UI.apply_post_render = function (root) {
		if ( !root ) {
			return;
		}
		try {
			UI.WPBC_BFB_ValueSlider?.init_on?.( root );
		} catch ( e ) { /* noop */
		}
		try {
			var T = UI.WPBC_BFB_Toggle_Normalizer;
			if ( T && typeof T.upgrade_checkboxes_in === 'function' ) {
				T.upgrade_checkboxes_in( root );
			}
		} catch ( e ) {
			w._wpbc?.dev?.error?.( 'apply_post_render.toggle', e );
		}

		// Accessibility: keep aria-checked in sync for all toggles inside root.
		try {
			root.querySelectorAll( '.wpbc_ui__toggle input[type="checkbox"]' ).forEach( function (cb) {
				if ( cb.__wpbc_aria_hooked ) {
					return;
				}
				cb.__wpbc_aria_hooked = true;
				cb.setAttribute( 'aria-checked', cb.checked ? 'true' : 'false' );
				// Delegate ‘change’ just once per render – native delegation still works fine for your logic.
				cb.addEventListener( 'change', () => {
					cb.setAttribute( 'aria-checked', cb.checked ? 'true' : 'false' );
				}, { passive: true } );
			} );
		} catch ( e ) {
			w._wpbc?.dev?.error?.( 'apply_post_render.aria', e );
		}
	};

	UI.InspectorEnhancers = UI.InspectorEnhancers || (function () {
		var regs = [];

		function register(name, selector, init, destroy) {
			regs.push( { name, selector, init, destroy } );
		}

		function scan(root) {
			if ( !root ) return;
			regs.forEach( function (r) {
				root.querySelectorAll( r.selector ).forEach( function (node) {
					node.__wpbc_eh = node.__wpbc_eh || {};
					if ( node.__wpbc_eh[r.name] ) return;
					try {
						r.init && r.init( node, root );
						node.__wpbc_eh[r.name] = true;
					} catch ( _e ) {
					}
				} );
			} );
		}

		function destroy(root) {
			if ( !root ) return;
			regs.forEach( function (r) {
				root.querySelectorAll( r.selector ).forEach( function (node) {
					try {
						r.destroy && r.destroy( node, root );
					} catch ( _e ) {
					}
					if ( node.__wpbc_eh ) delete node.__wpbc_eh[r.name];
				} );
			} );
		}

		return { register, scan, destroy };
	})();

	UI.WPBC_BFB_ValueSlider = {
		init_on(root) {
			var groups = (root.nodeType === 1 ? [ root ] : []).concat( [].slice.call( root.querySelectorAll?.( '[data-len-group]' ) || [] ) );
			groups.forEach( function (g) {
				if ( !g.matches || !g.matches( '[data-len-group]' ) ) return;
				if ( g.__wpbc_len_wired ) return;

				var number = g.querySelector( '[data-len-value]' );
				var range  = g.querySelector( '[data-len-range]' );
				var unit   = g.querySelector( '[data-len-unit]' );

				if ( !number || !range ) return;

				// Mirror constraints if missing on the range.
				[ 'min', 'max', 'step' ].forEach( function (a) {
					if ( !range.hasAttribute( a ) && number.hasAttribute( a ) ) {
						range.setAttribute( a, number.getAttribute( a ) );
					}
				} );


				function sync_range_from_number() {
					if ( range.value !== number.value ) {
						range.value = number.value;
					}
				}

				function dispatch_input(el) {
					try { el.dispatchEvent( new Event( 'input', { bubbles: true } ) ); } catch ( _e ) {}
				}
				function dispatch_change(el) {
					try { el.dispatchEvent( new Event( 'change', { bubbles: true } ) ); } catch ( _e ) {}
				}

				// Throttle range->number syncing (time-based).
				var timer_id       = 0;
				var pending_val    = null;
				var pending_change = false;
				var last_flush_ts  = 0;

				// Change this to tune speed: 50..120 ms is a good range.
				var min_interval_ms = parseInt( g.dataset.lenThrottle || UI.VALUE_SLIDER_THROTTLE_MS, 10 );
				min_interval_ms = Number.isFinite( min_interval_ms ) ? Math.max( 0, min_interval_ms ) : 120;

				function flush_range_to_number() {
					timer_id = 0;

					if ( pending_val == null ) {
						return;
					}

					var next    = String( pending_val );
					pending_val = null;

					if ( number.value !== next ) {
						number.value = next;
						// IMPORTANT: only 'input' while dragging.
						dispatch_input( number );
					}

					if ( pending_change ) {
						pending_change = false;
						dispatch_change( number );
					}

					last_flush_ts = Date.now();
				}

				function schedule_range_to_number(val, emit_change) {
					pending_val = val;
					if ( emit_change ) {
						pending_change = true;
					}

					// If commit requested, flush immediately.
					if ( pending_change ) {
						if ( timer_id ) {
							clearTimeout( timer_id );
							timer_id = 0;
						}
						flush_range_to_number();
						return;
					}

					var now   = Date.now();
					var delta = now - last_flush_ts;

					// If enough time passed, flush immediately; else schedule.
					if ( delta >= min_interval_ms ) {
						flush_range_to_number();
						return;
					}

					if ( timer_id ) {
						return;
					}

					timer_id = setTimeout( flush_range_to_number, Math.max( 0, min_interval_ms - delta ) );
				}

				function on_number_input() {
					sync_range_from_number();
				}

				function on_number_change() {
					sync_range_from_number();
				}

				function on_range_input() {
					schedule_range_to_number( range.value, false );
				}

				function on_range_change() {
					schedule_range_to_number( range.value, true );
				}

				number.addEventListener( 'input',  on_number_input );
				number.addEventListener( 'change', on_number_change );
				range.addEventListener( 'input',  on_range_input );
				range.addEventListener( 'change', on_range_change );

				if ( unit ) {
					unit.addEventListener( 'change', function () {
						// We just nudge the number so upstream handlers re-run.
						try {
							number.dispatchEvent( new Event( 'input', { bubbles: true } ) );
						} catch ( _e ) {
						}
					} );
				}

				// Initial sync
				sync_range_from_number();

				g.__wpbc_len_wired = {
					destroy() {
						number.removeEventListener( 'input',  on_number_input );
						number.removeEventListener( 'change', on_number_change );
						range.removeEventListener( 'input',  on_range_input );
						range.removeEventListener( 'change', on_range_change );
					}
				};
			} );
		},
		destroy_on(root) {
			var groups = (root && root.nodeType === 1 ? [ root ] : []).concat(
				[].slice.call( root.querySelectorAll?.( '[data-len-group]' ) || [] )
			);
			groups.forEach( function (g) {
				if ( !g.matches || !g.matches( '[data-len-group]' ) ) return;
				try {
					g.__wpbc_len_wired && g.__wpbc_len_wired.destroy && g.__wpbc_len_wired.destroy();
				} catch ( _e ) {
				}
				delete g.__wpbc_len_wired;
			} );
		}
	};

	// Register with the global enhancers hub.
	UI.InspectorEnhancers && UI.InspectorEnhancers.register(
		'value-slider',
		'[data-len-group]',
		function (el, _root) {
			UI.WPBC_BFB_ValueSlider.init_on( el );
		},
		function (el, _root) {
			UI.WPBC_BFB_ValueSlider.destroy_on( el );
		}
	);

	// Single, load-order-safe patch so enhancers auto-run on every bind.
	(function patchInspectorEnhancers() {
		function applyPatch() {
			var Inspector = w.WPBC_BFB_Inspector;
			if ( !Inspector || Inspector.__wpbc_enhancers_patched ) return false;
			Inspector.__wpbc_enhancers_patched = true;
			var orig                           = Inspector.prototype.bind_to_field;
			Inspector.prototype.bind_to_field  = function (el) {
				orig.call( this, el );
				try {
					var ins = this.panel
						|| document.getElementById( 'wpbc_bfb__inspector' )
						|| document.querySelector( '.wpbc_bfb__inspector' );
					UI.InspectorEnhancers && UI.InspectorEnhancers.scan( ins );
				} catch ( _e ) {
				}
			};
			// Initial scan if the DOM is already present.
			try {
				var insEl = document.getElementById( 'wpbc_bfb__inspector' )
					|| document.querySelector( '.wpbc_bfb__inspector' );
				UI.InspectorEnhancers && UI.InspectorEnhancers.scan( insEl );
			} catch ( _e ) {
			}
			return true;
		}

		// Try now; if Inspector isn’t defined yet, patch when it becomes ready.
		if ( !applyPatch() ) {
			document.addEventListener(
				'wpbc_bfb_inspector_ready',
				function () {
					applyPatch();
				},
				{ once: true }
			);
		}
	})();

}( window, document ));
// ---------------------------------------------------------------------------------------------------------------------
// == File  /includes/page-form-builder/_out/core/bfb-inspector.js == Time point: 2025-09-06 14:08
// ---------------------------------------------------------------------------------------------------------------------
(function (w) {
	'use strict';

	// 1) Actions registry.

	/** @type {Record<string, (ctx: InspectorActionContext) => void>} */
	const __INSPECTOR_ACTIONS_MAP__ = Object.create( null );

	// Built-ins.
	__INSPECTOR_ACTIONS_MAP__['deselect'] = ({ builder }) => {
		builder?.select_field?.( null );
	};

	__INSPECTOR_ACTIONS_MAP__['scrollto'] = ({ builder, el }) => {
		if ( !el || !document.body.contains( el ) ) return;
		builder?.select_field?.( el, { scrollIntoView: true } );
		el.classList.add( 'wpbc_bfb__scroll-pulse' );
		setTimeout( () => el.classList.remove( 'wpbc_bfb__scroll-pulse' ), 700 );
	};

	__INSPECTOR_ACTIONS_MAP__['move-up'] = ({ builder, el }) => {
		if ( !el ) return;
		builder?.move_item?.( el, 'up' );
		// Scroll after the DOM has settled.
		requestAnimationFrame(() => __INSPECTOR_ACTIONS_MAP__['scrollto']({ builder, el }));
	};

	__INSPECTOR_ACTIONS_MAP__['move-down'] = ({ builder, el }) => {
		if ( !el ) return;
		builder?.move_item?.( el, 'down' );
		// Scroll after the DOM has settled.
		requestAnimationFrame(() => __INSPECTOR_ACTIONS_MAP__['scrollto']({ builder, el }));
	};

	__INSPECTOR_ACTIONS_MAP__['delete'] = ({ builder, el, confirm = w.confirm }) => {
		if ( !el ) return;
		const is_field = el.classList.contains( 'wpbc_bfb__field' );
		const label    = is_field
			? (el.querySelector( '.wpbc_bfb__field-label' )?.textContent || el.dataset?.id || 'field')
			: (el.dataset?.id || 'section');

		UI.Modal_Confirm_Delete.open( label, () => {
			// Central command will remove, emit events, and reselect neighbor (which re-binds Inspector).
			builder?.delete_item?.( el );
		} );

	};

	__INSPECTOR_ACTIONS_MAP__['duplicate'] = ({ builder, el }) => {
		if ( !el ) return;
		const clone = builder?.duplicate_item?.( el );
		if ( clone ) builder?.select_field?.( clone, { scrollIntoView: true } );
	};

	// Public API.
	w.WPBC_BFB_Inspector_Actions = {
		run(name, ctx) {
			const fn = __INSPECTOR_ACTIONS_MAP__[name];
			if ( typeof fn === 'function' ) fn( ctx );
			else console.warn( 'WPBC. Inspector action not found:', name );
		},
		register(name, handler) {
			if ( !name || typeof handler !== 'function' ) {
				throw new Error( 'register(name, handler): invalid arguments' );
			}
			__INSPECTOR_ACTIONS_MAP__[name] = handler;
		},
		has(name) {
			return typeof __INSPECTOR_ACTIONS_MAP__[name] === 'function';
		}
	};

	// 2) Inspector Factory.

	var UI = (w.WPBC_BFB_Core.UI = w.WPBC_BFB_Core.UI || {});

	// Global Hybrid++ registries (keep public).
	w.wpbc_bfb_inspector_factory_slots      = w.wpbc_bfb_inspector_factory_slots || {};
	w.wpbc_bfb_inspector_factory_value_from = w.wpbc_bfb_inspector_factory_value_from || {};

	// Define Factory only if missing (no early return for the whole bundle).
	// always define/replace Factory
	{

		/**
		 * Utility: create element with attributes and children.
		 *
		 * @param {string} tag
		 * @param {Object=} attrs
		 * @param {(Node|string|Array<Node|string>)=} children
		 * @returns {HTMLElement}
		 */
		function el(tag, attrs, children) {
			var node = document.createElement( tag );
			if ( attrs ) {
				Object.keys( attrs ).forEach( function (k) {
					var v = attrs[k];
					if ( v == null ) return;
					if ( k === 'class' ) {
						node.className = v;
						return;
					}
					if ( k === 'dataset' ) {
						Object.keys( v ).forEach( function (dk) {
							node.dataset[dk] = String( v[dk] );
						} );
						return;
					}
					if ( k === 'checked' && typeof v === 'boolean' ) {
						if ( v ) node.setAttribute( 'checked', 'checked' );
						return;
					}
					if ( k === 'disabled' && typeof v === 'boolean' ) {
						if ( v ) node.setAttribute( 'disabled', 'disabled' );
						return;
					}
					// normalize boolean attributes to strings.
					if ( typeof v === 'boolean' ) {
						node.setAttribute( k, v ? 'true' : 'false' );
						return;
					}
					node.setAttribute( k, String( v ) );
				} );
			}
			if ( children ) {
				(Array.isArray( children ) ? children : [ children ]).forEach( function (c) {
					if ( c == null ) return;
					node.appendChild( (typeof c === 'string') ? document.createTextNode( c ) : c );
				} );
			}
			return node;
		}

		/**
		 * Build a toggle control row (checkbox rendered as toggle).
		 *
		 * Structure:
		 * <div class="inspector__row inspector__row--toggle">
		 *   <div class="inspector__control wpbc_ui__toggle">
		 *     <input type="checkbox" id="ID" data-inspector-key="KEY" class="inspector__input" checked>
		 *     <label class="wpbc_ui__toggle_icon"  for="ID"></label>
		 *     <label class="wpbc_ui__toggle_label" for="ID">Label text</label>
		 *   </div>
		 * </div>
		 *
		 * @param {string} input_id
		 * @param {string} key
		 * @param {boolean} checked
		 * @param {string} label_text
		 * @returns {HTMLElement}
		 */
		function build_toggle_row( input_id, key, checked, label_text ) {

			var row_el    = el( 'div', { 'class': 'inspector__row inspector__row--toggle' } );
			var ctrl_wrap = el( 'div', { 'class': 'inspector__control wpbc_ui__toggle' } );

			var input_el = el( 'input', {
				id                  : input_id,
				type                : 'checkbox',
				'data-inspector-key': key,
				'class'             : 'inspector__input',
				checked             : !!checked,
				role                : 'switch',
				'aria-checked'      : !!checked
			} );
			var icon_lbl = el( 'label', { 'class': 'wpbc_ui__toggle_icon', 'for': input_id } );
			var text_lbl = el( 'label', { 'class': 'wpbc_ui__toggle_label', 'for': input_id }, label_text || '' );

			ctrl_wrap.appendChild( input_el );
			ctrl_wrap.appendChild( icon_lbl );
			ctrl_wrap.appendChild( text_lbl );

			row_el.appendChild( ctrl_wrap );
			return row_el;
		}

		/**
	 * Utility: choose initial value from data or schema default.
	 */
		function get_initial_value(key, data, props_schema) {
			if ( data && Object.prototype.hasOwnProperty.call( data, key ) ) return data[key];
			var meta = props_schema && props_schema[key];
			return (meta && Object.prototype.hasOwnProperty.call( meta, 'default' )) ? meta.default : '';
		}

		/**
	 * Utility: coerce value by schema type.
	 */


		function coerce_by_type(value, type) {
			switch ( type ) {
				case 'number':
				case 'int':
				case 'float':
					if ( value === '' || value == null ) {
						return '';
					}
					var n = Number( value );
					return isNaN( n ) ? '' : n;
				case 'boolean':
					return !!value;
				case 'array':
					return Array.isArray( value ) ? value : [];
				default:
					return (value == null) ? '' : String( value );
			}
		}

		/**
	 * Normalize <select> options (array of {value,label} or map {value:label}).
	 */
		function normalize_select_options(options) {
			if ( Array.isArray( options ) ) {
				return options.map( function (o) {
					if ( typeof o === 'object' && o && 'value' in o ) {
						return { value: String( o.value ), label: String( o.label || o.value ) };
					}
					return { value: String( o ), label: String( o ) };
				} );
			}
			if ( options && typeof options === 'object' ) {
				return Object.keys( options ).map( function (k) {
					return { value: String( k ), label: String( options[k] ) };
				} );
			}
			return [];
		}

		/** Parse a CSS length like "120px" or "80%" into { value:number, unit:string }. */
		function parse_len(value, fallback_unit) {
			value = (value == null) ? '' : String( value ).trim();
			var m = value.match( /^(-?\d+(?:\.\d+)?)(px|%|rem|em)$/i );
			if ( m ) {
				return { value: parseFloat( m[1] ), unit: m[2].toLowerCase() };
			}
			// plain number -> assume fallback unit
			if ( value !== '' && !isNaN( Number( value ) ) ) {
				return { value: Number( value ), unit: (fallback_unit || 'px') };
			}
			return { value: 0, unit: (fallback_unit || 'px') };
		}

		/** Clamp helper. */
		function clamp_num(v, min, max) {
			if ( typeof v !== 'number' || isNaN( v ) ) return (min != null ? min : 0);
			if ( min != null && v < min ) v = min;
			if ( max != null && v > max ) v = max;
			return v;
		}

		// Initialize Coloris pickers in a given root.
		// Relies on Coloris being enqueued (see bfb-bootstrap.php).
		function init_coloris_pickers(root) {
			if ( !root || !w.Coloris ) return;
			// Mark inputs we want Coloris to handle.
			var inputs = root.querySelectorAll( 'input[data-inspector-type="color"]' );
			if ( !inputs.length ) return;

			// Add a stable class for Coloris targeting; avoid double-initializing.
			inputs.forEach( function (input) {
				if ( input.classList.contains( 'wpbc_bfb_coloris' ) ) return;
				input.classList.add( 'wpbc_bfb_coloris' );
			} );

			// Create/refresh a Coloris instance bound to these inputs.
			// Keep HEX output to match schema defaults (e.g., "#e0e0e0").
			try {
				w.Coloris( {
					el       : '.wpbc_bfb_coloris',
					alpha    : false,
					format   : 'hex',
					themeMode: 'auto'
				} );
				// Coloris already dispatches 'input' events on value changes.
			} catch ( e ) {
				// Non-fatal: if Coloris throws (rare), the text input still works.
				console.warn( 'WPBC Inspector: Coloris init failed:', e );
			}
		}

		/**
		 * Build: slider + number in one row (writes to a single data key).
		 * Control meta: { type:'range_number', key, label, min, max, step }
		 */
		function build_range_number_row(input_id, key, label_text, value, meta) {
			var row_el   = el('div', { 'class': 'inspector__row' });
			var label_el = el('label', { 'for': input_id, 'class': 'inspector__label' }, label_text || key || '');
			var ctrl     = el('div', { 'class': 'inspector__control' });

			var min  = (meta && meta.min != null)  ? meta.min  : 0;
			var max  = (meta && meta.max != null)  ? meta.max  : 100;
			var step = (meta && meta.step != null) ? meta.step : 1;

			var group = el('div', { 'class': 'wpbc_len_group wpbc_inline_inputs', 'data-len-group': key });

			var range = el('input', {
				type : 'range',
				'class': 'inspector__input',
				'data-len-range': '',
				min  : String(min),
				max  : String(max),
				step : String(step),
				value: String(value == null || value === '' ? min : value)
			});

			var num = el('input', {
				id   : input_id,
				type : 'number',
				'class': 'inspector__input inspector__w_30',
				'data-len-value': '',
				'data-inspector-key': key,
				min  : String(min),
				max  : String(max),
				step : String(step),
				value: (value == null || value === '') ? String(min) : String(value)
			});

			group.appendChild(range);
			group.appendChild(num);
			ctrl.appendChild(group);
			row_el.appendChild(label_el);
			row_el.appendChild(ctrl);
			return row_el;
		}

		/**
		 * Build: (number + unit) + slider, writing a *single* combined string to `key`.
		 * Control meta:
		 * {
		 *   type:'len', key, label, units:['px','%','rem','em'],
		 *   slider: { px:{min:0,max:512,step:1}, '%':{min:0,max:100,step:1}, rem:{min:0,max:10,step:0.1}, em:{...} },
		 *   fallback_unit:'px'
		 * }
		 */
		function build_len_compound_row(control, props_schema, data, uid) {
			var key        = control.key;
			var label_text = control.label || key || '';
			var def_str    = get_initial_value( key, data, props_schema );
			var fallback_u = control.fallback_unit || 'px';
			var parsed     = parse_len( def_str, fallback_u );

			var row   = el( 'div', { 'class': 'inspector__row' } );
			var label = el( 'label', { 'class': 'inspector__label' }, label_text );
			var ctrl  = el( 'div', { 'class': 'inspector__control' } );

			var units      = Array.isArray( control.units ) && control.units.length ? control.units : [ 'px', '%', 'rem', 'em' ];
			var slider_map = control.slider || {
				'px' : { min: 0, max: 512, step: 1 },
				'%'  : { min: 0, max: 100, step: 1 },
				'rem': { min: 0, max: 10, step: 0.1 },
				'em' : { min: 0, max: 10, step: 0.1 }
			};

			// Host with a hidden input that carries data-inspector-key to reuse the standard handler.
			var group = el( 'div', { 'class': 'wpbc_len_group', 'data-len-group': key } );

			var inline = el( 'div', { 'class': 'wpbc_inline_inputs' } );

			var num = el( 'input', {
				type            : 'number',
				'class'         : 'inspector__input',
				'data-len-value': '',
				min             : '0',
				step            : 'any',
				value           : String( parsed.value )
			} );

			var sel = el( 'select', { 'class': 'inspector__input', 'data-len-unit': '' } );
			units.forEach( function (u) {
				var opt = el( 'option', { value: u }, u );
				if ( u === parsed.unit ) opt.setAttribute( 'selected', 'selected' );
				sel.appendChild( opt );
			} );

			inline.appendChild( num );
			inline.appendChild( sel );

			// Slider (unit-aware)
			var current = slider_map[parsed.unit] || slider_map[units[0]];
			var range   = el( 'input', {
				type            : 'range',
				'class'         : 'inspector__input',
				'data-len-range': '',
				min             : String( current.min ),
				max             : String( current.max ),
				step            : String( current.step ),
				value           : String( clamp_num( parsed.value, current.min, current.max ) )
			} );

			// Hidden writer input that the default Inspector handler will catch.
			var hidden = el( 'input', {
				type                : 'text',
				'class'             : 'inspector__input',
				style               : 'display:none',
				'aria-hidden'       : 'true',
				tabindex            : '-1',
				id                  : 'wpbc_ins_' + key + '_' + uid + '_len_hidden',
				'data-inspector-key': key,
				value               : (String( parsed.value ) + parsed.unit)
			} );

			group.appendChild( inline );
			group.appendChild( range );
			group.appendChild( hidden );

			ctrl.appendChild( group );
			row.appendChild( label );
			row.appendChild( ctrl );
			return row;
		}

		/**
		 * Wire syncing for any .wpbc_len_group inside a given root (panel).
		 * - range ⇄ number sync
		 * - unit switches update slider bounds
		 * - hidden writer (if present) gets updated and emits 'input'
		 */
		function wire_len_group(root) {
			if ( !root ) return;

			function find_group(el) {
				return el && el.closest && el.closest( '.wpbc_len_group' );
			}

			root.addEventListener( 'input', function (e) {
				var t = e.target;
				// Slider moved -> update number (and writer/hidden)
				if ( t && t.hasAttribute( 'data-len-range' ) ) {
					var g = find_group( t );
					if ( !g ) return;
					var num = g.querySelector( '[data-len-value]' );
					if ( num ) {
						num.value = t.value;
					}
					var writer = g.querySelector( '[data-inspector-key]' );
					if ( writer && writer.type === 'text' ) {
						var unit     = g.querySelector( '[data-len-unit]' );
						unit         = unit ? unit.value : 'px';
						writer.value = String( t.value ) + String( unit );
						// trigger standard inspector handler:
						writer.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					} else {
						// Plain range_number case (number has data-inspector-key) -> fire input on number
						if ( num && num.hasAttribute( 'data-inspector-key' ) ) {
							num.dispatchEvent( new Event( 'input', { bubbles: true } ) );
						}
					}
				}

				// Number typed -> update slider and writer/hidden
				if ( t && t.hasAttribute( 'data-len-value' ) ) {
					var g = find_group( t );
					if ( !g ) return;
					var r = g.querySelector( '[data-len-range]' );
					if ( r ) {
						// clamp within slider bounds if present
						var min = Number( r.min );
						var max = Number( r.max );
						var v   = Number( t.value );
						if ( !isNaN( v ) ) {
							v       = clamp_num( v, isNaN( min ) ? undefined : min, isNaN( max ) ? undefined : max );
							r.value = String( v );
							if ( String( v ) !== t.value ) t.value = String( v );
						}
					}
					var writer = g.querySelector( '[data-inspector-key]' );
					if ( writer && writer.type === 'text' ) {
						var unit     = g.querySelector( '[data-len-unit]' );
						unit         = unit ? unit.value : 'px';
						writer.value = String( t.value || 0 ) + String( unit );
						writer.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					}
					// else: number itself likely carries data-inspector-key (range_number); default handler will run.
				}
			}, true );

			root.addEventListener( 'change', function (e) {
				var t = e.target;
				// Unit changed -> update slider limits and writer/hidden
				if ( t && t.hasAttribute( 'data-len-unit' ) ) {
					var g = find_group( t );
					if ( !g ) return;

					// Find the control meta via a data attribute on group if provided
					// (Factory path sets nothing here; we re-derive from current slider bounds.)
					var r      = g.querySelector( '[data-len-range]' );
					var num    = g.querySelector( '[data-len-value]' );
					var writer = g.querySelector( '[data-inspector-key]' );
					var unit   = t.value || 'px';

					// Adjust slider bounds heuristically (match Factory defaults)
					var bounds_by_unit = {
						'px' : { min: 0, max: 512, step: 1 },
						'%'  : { min: 0, max: 100, step: 1 },
						'rem': { min: 0, max: 10, step: 0.1 },
						'em' : { min: 0, max: 10, step: 0.1 }
					};
					if ( r ) {
						var b  = bounds_by_unit[unit] || bounds_by_unit['px'];
						r.min  = String( b.min );
						r.max  = String( b.max );
						r.step = String( b.step );
						// clamp to new bounds
						var v  = Number( num && num.value ? num.value : r.value );
						if ( !isNaN( v ) ) {
							v       = clamp_num( v, b.min, b.max );
							r.value = String( v );
							if ( num ) num.value = String( v );
						}
					}
					if ( writer && writer.type === 'text' ) {
						var v        = num && num.value ? num.value : (r ? r.value : '0');
						writer.value = String( v ) + String( unit );
						writer.dispatchEvent( new Event( 'input', { bubbles: true } ) );
					}
				}
			}, true );
		}

		// =============================================================================================================
		// ==  C O N T R O L  ==
		// =============================================================================================================

		/**
	 * Schema > Inspector > Control Element, e.g. Input!  Build a single control row:
	 * <div class="inspector__row">
	 *   <label class="inspector__label" for="...">Label</label>
	 *   <div class="inspector__control"><input|textarea|select class="inspector__input" ...></div>
	 * </div>
	 *
	 * @param {Object} control           - schema control meta ({type,key,label,...})
	 * @param {Object} props_schema      - schema.props
	 * @param {Object} data              - current element data-* map
	 * @param {string} uid               - unique suffix for input ids
	 * @param {Object} ctx               - { el, builder, type, data }
	 * @returns {HTMLElement}
	 */
		function build_control(control, props_schema, data, uid, ctx) {
			var type = control.type;
			var key  = control.key;

			var label_text = control.label || key || '';
			var prop_meta  = (key ? (props_schema[key] || { type: 'string' }) : { type: 'string' });
			var value      = coerce_by_type( get_initial_value( key, data, props_schema ), prop_meta.type );
		// Allow value_from override (computed at render-time).
		if ( control && control.value_from && w.wpbc_bfb_inspector_factory_value_from[control.value_from] ) {
				try {
					var computed = w.wpbc_bfb_inspector_factory_value_from[control.value_from]( ctx || {} );
					value        = coerce_by_type( computed, prop_meta.type );
				} catch ( e ) {
					console.warn( 'value_from failed for', control.value_from, e );
				}
			}

			var input_id = 'wpbc_ins_' + key + '_' + uid;

			var row_el    = el( 'div', { 'class': 'inspector__row' } );
			var label_el  = el( 'label', { 'for': input_id, 'class': 'inspector__label' }, label_text );
			var ctrl_wrap = el( 'div', { 'class': 'inspector__control' } );

			var field_el;

		// --- slot host (named UI injection) -----------------------------------
		if ( type === 'slot' && control.slot ) {
			// add a marker class for the layout chips row
			var classes = 'inspector__row inspector__row--slot';
			if ( control.slot === 'layout_chips' ) classes += ' inspector__row--layout-chips';

			var slot_row = el( 'div', { 'class': classes } );

			if ( label_text ) slot_row.appendChild( el( 'label', { 'class': 'inspector__label' }, label_text ) );

			// add a data attribute on the host so both CSS and the safety-net can target it
			var host_attrs = { 'class': 'inspector__control' };
			if ( control.slot === 'layout_chips' ) host_attrs['data-bfb-slot'] = 'layout_chips';

			var slot_host = el( 'div', host_attrs );
			slot_row.appendChild( slot_host );

			var slot_fn = w.wpbc_bfb_inspector_factory_slots[control.slot];
			if ( typeof slot_fn === 'function' ) {
				setTimeout( function () {
					try {
						slot_fn( slot_host, ctx || {} );
					} catch ( e ) {
						console.warn( 'slot "' + control.slot + '" failed:', e );
					}
				}, 0 );
			} else {
				slot_host.appendChild( el( 'div', { 'class': 'wpbc_bfb__slot__missing' }, '[slot: ' + control.slot + ']' ) );
			}
			return slot_row;
		}


			if ( type === 'textarea' ) {
				field_el = el( 'textarea', {
					id                  : input_id,
					'data-inspector-key': key,
					rows                : control.rows || 3,
					'class'             : 'inspector__input'
				}, (value == null ? '' : String( value )) );
			} else if ( type === 'select' ) {
				field_el = el( 'select', {
					id                  : input_id,
					'data-inspector-key': key,
					'class'             : 'inspector__input'
				} );
				normalize_select_options( control.options || [] ).forEach( function (opt) {
					var opt_el = el( 'option', { value: opt.value }, opt.label );
					if ( String( value ) === opt.value ) opt_el.setAttribute( 'selected', 'selected' );
					field_el.appendChild( opt_el );
				} );
			} else if ( type === 'checkbox' ) {
				// field_el = el( 'input', { id: input_id, type: 'checkbox', 'data-inspector-key': key, checked: !!value, 'class': 'inspector__input' } ); //.

				// Render as toggle UI instead of label-left + checkbox.  Note: we return the full toggle row here and skip the default row/label flow below.
				return build_toggle_row( input_id, key, !!value, label_text );

			} else if ( type === 'range_number' ) {
				// --- new: slider + number (single key).
				var rn_id  = 'wpbc_ins_' + key + '_' + uid;
				var rn_val = value; // from get_initial_value/prop_meta already.
				return build_range_number_row( rn_id, key, label_text, rn_val, control );

			} else if ( type === 'len' ) {
				// --- new: length compound (value+unit+slider -> writes a single string key).
				return build_len_compound_row( control, props_schema, data, uid );

			} else if ( type === 'color' ) {
				// Color picker (Coloris). Store as string (e.g., "#e0e0e0").
				field_el = el( 'input', {
					id                   : input_id,
					type                 : 'text',
					'data-inspector-key' : key,
					'data-inspector-type': 'color',
					'data-coloris'       : '',
					'class'              : 'inspector__input',
					'data-default-color' : ( value != null && value !== '' ? String(value) : (control.placeholder || '') )
				} );
				if ( value !== '' ) {
					field_el.value = String( value );
				}
			} else {
				// text/number default.
				var attrs = {
					id                  : input_id,
					type                : (type === 'number') ? 'number' : 'text',
					'data-inspector-key': key,
					'class'             : 'inspector__input'
				};
			// number constraints (schema or control)
				if ( type === 'number' ) {
					if ( Object.prototype.hasOwnProperty.call( prop_meta, 'min' ) ) attrs.min = prop_meta.min;
					if ( Object.prototype.hasOwnProperty.call( prop_meta, 'max' ) ) attrs.max = prop_meta.max;
					if ( Object.prototype.hasOwnProperty.call( prop_meta, 'step' ) ) attrs.step = prop_meta.step;
					if ( Object.prototype.hasOwnProperty.call( control, 'min' ) ) attrs.min = control.min;
					if ( Object.prototype.hasOwnProperty.call( control, 'max' ) ) attrs.max = control.max;
					if ( Object.prototype.hasOwnProperty.call( control, 'step' ) ) attrs.step = control.step;
				}
				field_el = el( 'input', attrs );
				if ( value !== '' ) field_el.value = String( value );
			}

			ctrl_wrap.appendChild( field_el );
			row_el.appendChild( label_el );
			row_el.appendChild( ctrl_wrap );
			return row_el;
		}

		/**
		 * Schema > Inspector > Groups! Build an inspector group (collapsible).
		 * Structure:
		 * <section class="wpbc_bfb__inspector__group wpbc_ui__collapsible_group is-open" data-group="...">
		 *   <button type="button" class="group__header" role="button" aria-expanded="true" aria-controls="wpbc_collapsible_panel_X">
		 *     <h3>Group Title</h3>
		 *     <i class="wpbc_ui_el__vert_menu_root_section_icon menu_icon icon-1x wpbc-bi-chevron-right"></i>
		 *   </button>
		 *   <div class="group__fields" id="wpbc_collapsible_panel_X" aria-hidden="false"> …rows… </div>
		 * </section>
		 *
		 * @param {Object} group
		 * @param {Object} props_schema
		 * @param {Object} data
		 * @param {string} uid
		 * @param {Object} ctx
		 * @returns {HTMLElement}
		 */
		function build_group(group, props_schema, data, uid, ctx) {
			var is_open  = !!group.open;
			var panel_id = 'wpbc_collapsible_panel_' + uid + '_' + (group.key || 'g');

			var section = el( 'section', {
				'class'     : 'wpbc_bfb__inspector__group wpbc_ui__collapsible_group' + (is_open ? ' is-open' : ''),
				'data-group': group.key || ''
			} );

			var header_btn = el( 'button', {
				type           : 'button',
				'class'        : 'group__header',
				role           : 'button',
				'aria-expanded': is_open ? 'true' : 'false',
				'aria-controls': panel_id
			}, [
				el( 'h3', null, group.title || group.label || group.key || '' ),
				el( 'i', { 'class': 'wpbc_ui_el__vert_menu_root_section_icon menu_icon icon-1x wpbc-bi-chevron-right' } )
			] );

			var fields = el( 'div', {
				'class'      : 'group__fields',
				id           : panel_id,
				'aria-hidden': is_open ? 'false' : 'true'
			} );

			function asArray(x) {
				if ( Array.isArray( x ) ) return x;
				if ( x && typeof x === 'object' ) return Object.values( x );
				return x != null ? [ x ] : [];
			}

			asArray( group.controls ).forEach( function (control) {
				fields.appendChild( build_control( control, props_schema, data, uid, ctx ) );
			} );

			section.appendChild( header_btn );
			section.appendChild( fields );
			return section;
		}

		/**
		 * Schema > Inspector > Header! Build inspector header with action buttons wired to existing data-action handlers.
		 *
		 * @param {Array<string>} header_actions
		 * @param {string}        title_text
		 * @returns {HTMLElement}
		 */
		function build_header(inspector_ui, title_fallback, schema_for_type) {

			inspector_ui      = inspector_ui || {};
			schema_for_type   = schema_for_type || {};
			var variant       = inspector_ui.header_variant || 'minimal';
			var headerActions = inspector_ui.header_actions
				|| schema_for_type.header_actions
				|| [ 'deselect', 'scrollto', 'move-up', 'move-down', 'duplicate', 'delete' ];

			var title       = inspector_ui.title || title_fallback || '';
			var description = inspector_ui.description || '';

			// helper to create a button for either header style
			function actionBtn(act, minimal) {
				if ( minimal ) {
					return el( 'button', { type: 'button', 'class': 'button-link', 'data-action': act }, '' );
				}
				// toolbar variant (rich)
				var iconMap = {
					'deselect' : 'wpbc_icn_remove_done',
					'scrollto' : 'wpbc_icn_ads_click filter_center_focus',
					'move-up'  : 'wpbc_icn_arrow_upward',
					'move-down': 'wpbc_icn_arrow_downward',
					'duplicate': 'wpbc_icn_content_copy',
					'delete'   : 'wpbc_icn_delete_outline'
				};
				var classes = 'button button-secondary wpbc_ui_control wpbc_ui_button';
				if ( act === 'delete' ) classes += ' wpbc_ui_button_danger button-link-delete';

				var btn = el( 'button', {
					type         : 'button',
					'class'      : classes,
					'data-action': act,
					'aria-label' : act.replace( /-/g, ' ' )
				} );

				if ( act === 'delete' ) {
					btn.appendChild( el( 'span', { 'class': 'in-button-text' }, 'Delete' ) );
					btn.appendChild( document.createTextNode( ' ' ) ); // minor spacing before icon
				}
				btn.appendChild( el( 'i', { 'class': 'menu_icon icon-1x ' + (iconMap[act] || '') } ) );
				return btn;
			}

			// === minimal header (existing look; default) ===
			if ( variant !== 'toolbar' ) {
				var header = el( 'header', { 'class': 'wpbc_bfb__inspector__header' } );
				header.appendChild( el( 'h3', null, title || '' ) );

				var actions = el( 'div', { 'class': 'wpbc_bfb__inspector__header_actions' } );
				headerActions.forEach( function (act) {
					actions.appendChild( actionBtn( act, /*minimal*/true ) );
				} );
				header.appendChild( actions );
				return header;
			}

			// === toolbar header (rich title/desc + grouped buttons) ===
			var root = el( 'div', { 'class': 'wpbc_bfb__inspector__head' } );
			var wrap = el( 'div', { 'class': 'header_container' } );
			var left = el( 'div', { 'class': 'header_title_content' } );
			var h3   = el( 'h3', { 'class': 'title' }, title || '' );
			left.appendChild( h3 );
			if ( description ) {
				left.appendChild( el( 'div', { 'class': 'desc' }, description ) );
			}

			var right = el( 'div', { 'class': 'actions wpbc_ajx_toolbar wpbc_no_borders' } );
			var uiC   = el( 'div', { 'class': 'ui_container ui_container_small' } );
			var uiG   = el( 'div', { 'class': 'ui_group' } );

			// Split into visual groups: first 2, next 2, then the rest.
			var g1 = el( 'div', { 'class': 'ui_element' } );
			var g2 = el( 'div', { 'class': 'ui_element' } );
			var g3 = el( 'div', { 'class': 'ui_element' } );

			headerActions.slice( 0, 2 ).forEach( function (act) {
				g1.appendChild( actionBtn( act, false ) );
			} );
			headerActions.slice( 2, 4 ).forEach( function (act) {
				g2.appendChild( actionBtn( act, false ) );
			} );
			headerActions.slice( 4 ).forEach( function (act) {
				g3.appendChild( actionBtn( act, false ) );
			} );

			uiG.appendChild( g1 );
			uiG.appendChild( g2 );
			uiG.appendChild( g3 );
			uiC.appendChild( uiG );
			right.appendChild( uiC );

			wrap.appendChild( left );
			wrap.appendChild( right );
			root.appendChild( wrap );

			return root;
		}


		function factory_render(panel_el, schema_for_type, data, opts) {
			if ( !panel_el ) return panel_el;

			schema_for_type  = schema_for_type || {};
			var props_schema = (schema_for_type.schema && schema_for_type.schema.props) ? schema_for_type.schema.props : {};
			var inspector_ui = (schema_for_type.inspector_ui || {});
			var groups       = inspector_ui.groups || [];

			var header_actions = inspector_ui.header_actions || schema_for_type.header_actions || [];
			var title_text     = (opts && opts.title) || inspector_ui.title || schema_for_type.label || (data && data.label) || '';

		// Prepare rendering context for slots/value_from, etc.
			var ctx = {
				el     : opts && opts.el || null,
				builder: opts && opts.builder || null,
				type   : opts && opts.type || null,
				data   : data || {}
			};

			// clear panel.
			while ( panel_el.firstChild ) panel_el.removeChild( panel_el.firstChild );

			var uid = Math.random().toString( 36 ).slice( 2, 8 );

			// header.
			panel_el.appendChild( build_header( inspector_ui, title_text, schema_for_type ) );


			// groups.
			groups.forEach( function (g) {
				panel_el.appendChild( build_group( g, props_schema, data || {}, uid, ctx ) );
			} );

			// ARIA sync for toggles created here (ensure aria-checked matches state).
			try {
				// Centralized UI normalizers (toggles + A11y): handled in Core.
				UI.apply_post_render( panel_el );
				try {
					wire_len_group( panel_el );
					// Initialize Coloris on color inputs rendered in this panel.
					init_coloris_pickers( panel_el );
				} catch ( _ ) { }
			} catch ( _ ) { }

			return panel_el;
		}

		UI.WPBC_BFB_Inspector_Factory = { render: factory_render };   // overwrite/refresh

		// ---- Built-in slot + value_from for Sections ----

		function slot_layout_chips(host, ctx) {
			try {
				var L = w.WPBC_BFB_Core &&  w.WPBC_BFB_Core.UI && w.WPBC_BFB_Core.UI.WPBC_BFB_Layout_Chips;
				if ( L && typeof L.render_for_section === 'function' ) {
					L.render_for_section( ctx.builder, ctx.el, host );
				} else {
					host.appendChild( document.createTextNode( '[layout_chips not available]' ) );
				}
			} catch ( e ) {
				console.warn( 'wpbc_bfb_slot_layout_chips failed:', e );
			}
		}

		w.wpbc_bfb_inspector_factory_slots.layout_chips = slot_layout_chips;

		function value_from_compute_section_columns(ctx) {
			try {
				var row = ctx && ctx.el && ctx.el.querySelector && ctx.el.querySelector( ':scope > .wpbc_bfb__row' );
				if ( !row ) return 1;
				var n = row.querySelectorAll( ':scope > .wpbc_bfb__column' ).length || 1;
				if ( n < 1 ) n = 1;
				if ( n > 4 ) n = 4;
				return n;
			} catch ( _ ) {
				return 1;
			}
		}

		w.wpbc_bfb_inspector_factory_value_from.compute_section_columns = value_from_compute_section_columns;
	}

	// 3) Inspector class.

	class WPBC_BFB_Inspector {

		constructor(panel_el, builder) {
			this.panel         = panel_el || this._create_fallback_panel();
			this.builder       = builder;
			this.selected_el   = null;
			this._render_timer = null;

			this._on_delegated_input  = (e) => this._apply_control_from_event( e );
			this._on_delegated_change = (e) => this._apply_control_from_event( e );
			this.panel.addEventListener( 'input', this._on_delegated_input, true );
			this.panel.addEventListener( 'change', this._on_delegated_change, true );

			this._on_delegated_click = (e) => {
				const btn = e.target.closest( '[data-action]' );
				if ( !btn || !this.panel.contains( btn ) ) return;
				e.preventDefault();
				e.stopPropagation();

				const action = btn.getAttribute( 'data-action' );
				const el     = this.selected_el;
				if ( !el ) return;

				w.WPBC_BFB_Inspector_Actions?.run( action, {
					builder: this.builder,
					el,
					panel  : this.panel,
					event  : e
				} );

				if ( action === 'delete' ) this.clear();
			};
			this.panel.addEventListener( 'click', this._on_delegated_click );
		}

		_post_render_ui() {
			try {
				var UI = w.WPBC_BFB_Core && w.WPBC_BFB_Core.UI;
				if ( UI && typeof UI.apply_post_render === 'function' ) {
					UI.apply_post_render( this.panel );
				}
				// NEW: wire slider/number/unit syncing for length & range_number groups.
				try {
					wire_len_group( this.panel );
					init_coloris_pickers( this.panel );
				} catch ( _ ) {
				}
			} catch ( e ) {
				_wpbc?.dev?.error?.( 'inspector._post_render_ui', e );
			}
		}


		_apply_control_from_event(e) {
			if ( !this.panel.contains( e.target ) ) return;

			const t   = /** @type {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} */ (e.target);
			const key = t?.dataset?.inspectorKey;
			if ( !key ) return;

			const el = this.selected_el;
			if ( !el || !document.body.contains( el ) ) return;

			let v;
			if ( t instanceof HTMLInputElement && t.type === 'checkbox' ) {
				v = !!t.checked;
				t.setAttribute( 'aria-checked', v ? 'true' : 'false' );         // Keep ARIA state in sync for toggles (schema and template paths).
			} else if ( t instanceof HTMLInputElement && t.type === 'number' ) {
				v = (t.value === '' ? '' : Number( t.value ));
			} else {
				v = t.value;
			}

			if ( key === 'id' ) {
				const unique = this.builder?.id?.set_field_id?.( el, v );
				if ( unique != null && t.value !== unique ) t.value = unique;

			} else if ( key === 'name' ) {
				const unique = this.builder?.id?.set_field_name?.( el, v );
				if ( unique != null && t.value !== unique ) t.value = unique;

			} else if ( key === 'html_id' ) {
				const applied = this.builder?.id?.set_field_html_id?.( el, v );
				if ( applied != null && t.value !== applied ) t.value = applied;

			} else if ( key === 'columns' && el.classList.contains( 'wpbc_bfb__section' ) ) {
				const v_int = parseInt( String( v ), 10 );
				if ( Number.isFinite( v_int ) ) {
					const clamped = w.WPBC_BFB_Core.WPBC_BFB_Sanitize.clamp( v_int, 1, 4 );
					this.builder?.set_section_columns?.( el, clamped );
					if ( String( clamped ) !== t.value ) t.value = String( clamped );
				}

			} else {
				if ( t instanceof HTMLInputElement && t.type === 'checkbox' ) {
					el.setAttribute( 'data-' + key, String( !!v ) );
				} else if ( t instanceof HTMLInputElement && t.type === 'number' ) {
					if ( t.value === '' || !Number.isFinite( v ) ) {
						el.removeAttribute( 'data-' + key );
					} else {
						el.setAttribute( 'data-' + key, String( v ) );
					}
				} else if ( v == null ) {
					el.removeAttribute( 'data-' + key );
				} else {
					el.setAttribute( 'data-' + key, (typeof v === 'object') ? JSON.stringify( v ) : String( v ) );
				}
			}

			// Update preview/overlay
			if ( el.classList.contains( 'wpbc_bfb__field' ) ) {
				if ( this.builder?.preview_mode ) this.builder.render_preview( el );
				else this.builder.add_overlay_toolbar( el );
			} else {
				this.builder.add_overlay_toolbar( el );
			}

			if ( this._needs_rerender( el, key, e ) ) {
				this._schedule_render_preserving_focus( 0 );
			}
		}

		_needs_rerender(el, key, _e) {
			if ( el.classList.contains( 'wpbc_bfb__section' ) && key === 'columns' ) return true;
			return false;
		}

		bind_to_field(field_el) {
			this.selected_el = field_el;
			this.render();
		}

		clear() {
			this.selected_el = null;
			if ( this._render_timer ) {
				clearTimeout( this._render_timer );
				this._render_timer = null;
			}
			// Also clear the section-cols hint on empty state.
			this.panel.removeAttribute('data-bfb-section-cols');
			this.panel.innerHTML = '<div class="wpbc_bfb__inspector__empty">Select a field to edit its options.</div>';
		}

		_schedule_render_preserving_focus(delay = 200) {
			const active    = /** @type {HTMLInputElement|HTMLTextAreaElement|HTMLElement|null} */ (document.activeElement);
			const activeKey = active?.dataset?.inspectorKey || null;
			let selStart    = null, selEnd = null;

			if ( active && 'selectionStart' in active && 'selectionEnd' in active ) {
				// @ts-ignore
				selStart = active.selectionStart;
				// @ts-ignore
				selEnd   = active.selectionEnd;
			}

			if ( this._render_timer ) clearTimeout( this._render_timer );
			this._render_timer = /** @type {unknown} */ (setTimeout( () => {
				this.render();
				if ( activeKey ) {
					const next = /** @type {HTMLInputElement|HTMLTextAreaElement|HTMLElement|null} */ (
						this.panel.querySelector( `[data-inspector-key="${activeKey}"]` )
					);
					if ( next ) {
						next.focus();
						try {
							if ( selStart != null && selEnd != null && typeof next.setSelectionRange === 'function' ) {
								// @ts-ignore
								next.setSelectionRange( selStart, selEnd );
							}
						} catch( e ){ _wpbc?.dev?.error( '_render_timer', e ); }
					}
				}
			}, delay ));
		}

		render() {

			const el = this.selected_el;
			if ( !el || !document.body.contains( el ) ) return this.clear();

			// Reset section-cols hint unless we set it later for a section.
			this.panel.removeAttribute( 'data-bfb-section-cols' );

			const prev_scroll = this.panel.scrollTop;

			// Section
			if ( el.classList.contains( 'wpbc_bfb__section' ) ) {
				let tpl = null;
				try {
					tpl = (w.wp && wp.template && document.getElementById( 'tmpl-wpbc-bfb-inspector-section' )) ? wp.template( 'wpbc-bfb-inspector-section' ) : null;
				} catch ( _ ) {
					tpl = null;
				}

				if ( tpl ) {
					this.panel.innerHTML = tpl( {} );
					this._enforce_default_group_open();
					this._set_panel_section_cols( el );
					this._post_render_ui();
					this.panel.scrollTop = prev_scroll;
					return;
				}

				const Factory = w.WPBC_BFB_Core.UI && w.WPBC_BFB_Core.UI.WPBC_BFB_Inspector_Factory;
				const schemas = w.WPBC_BFB_Schemas || {};
				const entry   = schemas['section'] || null;
				if ( entry && Factory ) {
					this.panel.innerHTML = '';
					Factory.render(
						this.panel,
						entry,
						{},
						{ el, builder: this.builder, type: 'section', title: entry.label || 'Section' }
					);
					this._enforce_default_group_open();

					// --- Safety net: if for any reason the slot didn’t render chips, inject them now.
					try {
						const hasSlotHost =
								  this.panel.querySelector( '[data-bfb-slot="layout_chips"]' ) ||
								  this.panel.querySelector( '.inspector__row--layout-chips .wpbc_bfb__layout_chips' ) ||
								  this.panel.querySelector( '#wpbc_bfb__layout_chips_host' );

						const hasChips =
								  !!this.panel.querySelector( '.wpbc_bfb__layout_chip' );

						if ( !hasChips ) {
							// Create a host if missing and render chips into it.
							const host = (function ensureHost(root) {
								let h =
										root.querySelector( '[data-bfb-slot="layout_chips"]' ) ||
										root.querySelector( '.inspector__row--layout-chips .wpbc_bfb__layout_chips' ) ||
										root.querySelector( '#wpbc_bfb__layout_chips_host' );
								if ( h ) return h;
								// Fallback host inside (or after) the “layout” group
								const fields    =
										  root.querySelector( '.wpbc_bfb__inspector__group[data-group="layout"] .group__fields' ) ||
										  root.querySelector( '.group__fields' ) || root;
								const row       = document.createElement( 'div' );
								row.className   = 'inspector__row inspector__row--layout-chips';
								const lab       = document.createElement( 'label' );
								lab.className   = 'inspector__label';
								lab.textContent = 'Layout';
								const ctl       = document.createElement( 'div' );
								ctl.className   = 'inspector__control';
								h               = document.createElement( 'div' );
								h.className     = 'wpbc_bfb__layout_chips';
								h.setAttribute( 'data-bfb-slot', 'layout_chips' );
								ctl.appendChild( h );
								row.appendChild( lab );
								row.appendChild( ctl );
								fields.appendChild( row );
								return h;
							})( this.panel );

							const L = (w.WPBC_BFB_Core && w.WPBC_BFB_Core.UI && w.WPBC_BFB_Core.UI.WPBC_BFB_Layout_Chips) ;
							if ( L && typeof L.render_for_section === 'function' ) {
								host.innerHTML = '';
								L.render_for_section( this.builder, el, host );
							}
						}
					} catch( e ){ _wpbc?.dev?.error( 'WPBC_BFB_Inspector - render', e ); }

					this._set_panel_section_cols( el );
					this.panel.scrollTop = prev_scroll;
					return;
				}

				this.panel.innerHTML = '<div class="wpbc_bfb__inspector__empty">Select a field to edit its options.</div>';
				return;
			}

			// Field
			if ( !el.classList.contains( 'wpbc_bfb__field' ) ) return this.clear();

			const data = w.WPBC_BFB_Core.WPBC_Form_Builder_Helper.get_all_data_attributes( el );
			const type = data.type || 'text';

			function _get_tpl(id) {
				if ( !w.wp || !wp.template ) return null;
				if ( !document.getElementById( 'tmpl-' + id ) ) return null;
				try {
					return wp.template( id );
				} catch ( e ) {
					return null;
				}
			}

			const tpl_id      = `wpbc-bfb-inspector-${type}`;
			const tpl         = _get_tpl( tpl_id );
			const generic_tpl = _get_tpl( 'wpbc-bfb-inspector-generic' );

			const schemas         = w.WPBC_BFB_Schemas || {};
			const schema_for_type = schemas[type] || null;
			const Factory         = w.WPBC_BFB_Core.UI && w.WPBC_BFB_Core.UI.WPBC_BFB_Inspector_Factory;

			if ( tpl ) {
				// NEW: merge schema defaults so missing keys (esp. booleans) honor defaults on first paint
				const hasOwn = Function.call.bind( Object.prototype.hasOwnProperty );
				const props  = (schema_for_type && schema_for_type.schema && schema_for_type.schema.props) ? schema_for_type.schema.props : {};
				const merged = { ...data };
				if ( props ) {
					Object.keys( props ).forEach( (k) => {
						const meta = props[k] || {};
						if ( !hasOwn( data, k ) || data[k] === '' ) {
							if ( hasOwn( meta, 'default' ) ) {
								// Coerce booleans to a real boolean; leave others as-is
								merged[k] = (meta.type === 'boolean') ? !!meta.default : meta.default;
							}
						} else if ( meta.type === 'boolean' ) {
							// Normalize truthy strings into booleans for templates that check on truthiness
							const v   = data[k];
							merged[k] = (v === true || v === 'true' || v === 1 || v === '1');
						}
					} );
				}
				this.panel.innerHTML = tpl( merged );

				this._post_render_ui();
			} else if ( schema_for_type && Factory ) {
				this.panel.innerHTML = '';
				Factory.render(
					this.panel,
					schema_for_type,
					{ ...data },
					{ el, builder: this.builder, type, title: data.label || '' }
				);
				// Ensure toggle normalizers and slider/number/unit wiring are attached.
				this._post_render_ui();
			} else if ( generic_tpl ) {
				this.panel.innerHTML = generic_tpl( { ...data } );
				this._post_render_ui();
			} else {

				const msg            = `There are no Inspector wp.template "${tpl_id}" or Schema for this "${String( type || '' )}" element.`;
				this.panel.innerHTML = '';
				const div            = document.createElement( 'div' );
				div.className        = 'wpbc_bfb__inspector__empty';
				div.textContent      = msg; // safe.
				this.panel.appendChild( div );
			}

			this._enforce_default_group_open();
			this.panel.scrollTop = prev_scroll;
		}

		_enforce_default_group_open() {
			const groups = Array.from( this.panel.querySelectorAll( '.wpbc_bfb__inspector__group' ) );
			if ( !groups.length ) return;

			let found = false;
			groups.forEach( (g) => {
				if ( !found && g.classList.contains( 'is-open' ) ) {
					found = true;
				} else {
					if ( g.classList.contains( 'is-open' ) ) {
						g.classList.remove( 'is-open' );
						g.dispatchEvent( new Event( 'wpbc:collapsible:close', { bubbles: true } ) );
					} else {
						g.classList.remove( 'is-open' );
					}
				}
			} );

			if ( !found ) {
				groups[0].classList.add( 'is-open' );
				groups[0].dispatchEvent( new Event( 'wpbc:collapsible:open', { bubbles: true } ) );
			}
		}

		/**
		 * Set data-bfb-section-cols on the inspector panel based on the current section.
		 * Uses the registered compute fn if available; falls back to direct DOM.
		 * @param {HTMLElement} sectionEl
		 */
		_set_panel_section_cols(sectionEl) {
			try {
				// Prefer the already-registered value_from helper if present.
				var compute = w.wpbc_bfb_inspector_factory_value_from && w.wpbc_bfb_inspector_factory_value_from.compute_section_columns;

				var cols = 1;
				if ( typeof compute === 'function' ) {
					cols = compute( { el: sectionEl } ) || 1;
				} else {
					// Fallback: compute directly from the DOM.
					var row = sectionEl && sectionEl.querySelector( ':scope > .wpbc_bfb__row' );
					cols    = row ? (row.querySelectorAll( ':scope > .wpbc_bfb__column' ).length || 1) : 1;
					if ( cols < 1 ) cols = 1;
					if ( cols > 4 ) cols = 4;
				}
				this.panel.setAttribute( 'data-bfb-section-cols', String( cols ) );
			} catch ( _ ) {
			}
		}


		_create_fallback_panel() {
			const p     = document.createElement( 'div' );
			p.id        = 'wpbc_bfb__inspector';
			p.className = 'wpbc_bfb__inspector';
			document.body.appendChild( p );
			return /** @type {HTMLDivElement} */ (p);
		}
	}

	// Export class + ready signal.
	w.WPBC_BFB_Inspector = WPBC_BFB_Inspector;
	document.dispatchEvent( new Event( 'wpbc_bfb_inspector_ready' ) );

})( window );

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJmYi1jb3JlLmpzIiwiYmZiLWZpZWxkcy5qcyIsImJmYi11aS5qcyIsImJmYi1pbnNwZWN0b3IuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3AwQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2htQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDL3dGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoid3BiY19iZmIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gRmlsZSAgL2luY2x1ZGVzL3BhZ2UtZm9ybS1idWlsZGVyL19vdXQvY29yZS9iZmItY29yZS5qcyA9PSB8IDIwMjUtMDktMTAgMTU6NDdcclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbihmdW5jdGlvbiAoIHcgKSB7XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyBTaW5nbGUgZ2xvYmFsIG5hbWVzcGFjZSAoaWRlbXBvdGVudCAmIGxvYWQtb3JkZXIgc2FmZSkuXHJcblx0Y29uc3QgQ29yZSA9ICggdy5XUEJDX0JGQl9Db3JlID0gdy5XUEJDX0JGQl9Db3JlIHx8IHt9ICk7XHJcblx0Y29uc3QgVUkgICA9ICggQ29yZS5VSSA9IENvcmUuVUkgfHwge30gKTtcclxuXHJcblx0LyoqXHJcblx0ICogQ29yZSBzYW5pdGl6ZS9lc2NhcGUvbm9ybWFsaXplIGhlbHBlcnMuXHJcblx0ICogQWxsIG1ldGhvZHMgdXNlIHNuYWtlX2Nhc2U7IGNhbWVsQ2FzZSBhbGlhc2VzIGFyZSBwcm92aWRlZCBmb3IgYmFja3dhcmRzIGNvbXBhdGliaWxpdHkuXHJcblx0ICovXHJcblx0Q29yZS5XUEJDX0JGQl9TYW5pdGl6ZSA9IGNsYXNzIHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVzY2FwZSB0ZXh0IGZvciBzYWZlIHVzZSBpbiBDU1Mgc2VsZWN0b3JzLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHMgLSByYXcgc2VsZWN0b3IgZnJhZ21lbnRcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBlc2NfY3NzKHMpIHtcclxuXHRcdFx0cmV0dXJuICh3LkNTUyAmJiB3LkNTUy5lc2NhcGUpID8gdy5DU1MuZXNjYXBlKCBTdHJpbmcoIHMgKSApIDogU3RyaW5nKCBzICkucmVwbGFjZSggLyhbXlxcdy1dKS9nLCAnXFxcXCQxJyApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRXNjYXBlIGEgdmFsdWUgZm9yIGF0dHJpYnV0ZSBzZWxlY3RvcnMsIGUuZy4gW2RhdGEtaWQ9XCI8dmFsdWU+XCJdLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHZcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBlc2NfYXR0cl92YWx1ZV9mb3Jfc2VsZWN0b3Iodikge1xyXG5cdFx0XHRyZXR1cm4gU3RyaW5nKCB2IClcclxuXHRcdFx0XHQucmVwbGFjZSggL1xcXFwvZywgJ1xcXFxcXFxcJyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC9cIi9nLCAnXFxcXFwiJyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC9cXG4vZywgJ1xcXFxBICcgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvXFxdL2csICdcXFxcXScgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNhbml0aXplIGludG8gYSBicm9hZGx5IGNvbXBhdGlibGUgSFRNTCBpZDogbGV0dGVycywgZGlnaXRzLCAtIF8gOiAuIDsgbXVzdCBzdGFydCB3aXRoIGEgbGV0dGVyLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHZcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBzYW5pdGl6ZV9odG1sX2lkKHYpIHtcclxuXHRcdFx0bGV0IHMgPSAodiA9PSBudWxsID8gJycgOiBTdHJpbmcoIHYgKSkudHJpbSgpO1xyXG5cdFx0XHRzICAgICA9IHNcclxuXHRcdFx0XHQucmVwbGFjZSggL1xccysvZywgJy0nIClcclxuXHRcdFx0XHQucmVwbGFjZSggL1teQS1aYS16MC05XFwtX1xcOi5dL2csICctJyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC8tKy9nLCAnLScgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvXlstXy46XSt8Wy1fLjpdKyQvZywgJycgKTtcclxuXHRcdFx0aWYgKCAhcyApIHJldHVybiAnZmllbGQnO1xyXG5cdFx0XHRpZiAoICEvXltBLVphLXpdLy50ZXN0KCBzICkgKSBzID0gJ2YtJyArIHM7XHJcblx0XHRcdHJldHVybiBzO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2FuaXRpemUgaW50byBhIHNhZmUgSFRNTCBuYW1lIHRva2VuOiBsZXR0ZXJzLCBkaWdpdHMsIF8gLVxyXG5cdFx0ICogTXVzdCBzdGFydCB3aXRoIGEgbGV0dGVyOyBubyBkb3RzL2JyYWNrZXRzL3NwYWNlcy5cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc2FuaXRpemVfaHRtbF9uYW1lKHYpIHtcclxuXHJcblx0XHRcdGxldCBzID0gKHYgPT0gbnVsbCA/ICcnIDogU3RyaW5nKCB2ICkpLnRyaW0oKTtcclxuXHJcblx0XHRcdHMgPSBzLnJlcGxhY2UoIC9cXHMrL2csICdfJyApLnJlcGxhY2UoIC9bXkEtWmEtejAtOV8tXS9nLCAnXycgKS5yZXBsYWNlKCAvXysvZywgJ18nICk7XHJcblxyXG5cdFx0XHRpZiAoICEgcyApIHtcclxuXHRcdFx0XHRzID0gJ2ZpZWxkJztcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoICEgL15bQS1aYS16XS8udGVzdCggcyApICkge1xyXG5cdFx0XHRcdHMgPSAnZl8nICsgcztcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gcztcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVzY2FwZSBmb3IgSFRNTCB0ZXh0L2F0dHJpYnV0ZXMgKG5vdCBVUkxzKS5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgZXNjYXBlX2h0bWwodikge1xyXG5cdFx0XHRpZiAoIHYgPT0gbnVsbCApIHtcclxuXHRcdFx0XHRyZXR1cm4gJyc7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIFN0cmluZyggdiApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC8mL2csICcmYW1wOycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvXCIvZywgJyZxdW90OycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvJy9nLCAnJiMwMzk7JyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC88L2csICcmbHQ7JyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC8+L2csICcmZ3Q7JyApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRXNjYXBlIG1pbmltYWwgc2V0IGZvciBhdHRyaWJ1dGUtc2FmZXR5IHdpdGhvdXQgc2x1Z2dpbmcuXHJcblx0XHQgKiBLZWVwcyBvcmlnaW5hbCBodW1hbiB0ZXh0OyBlc2NhcGVzICYsIDwsID4sIFwiIGFuZCAnIG9ubHkuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gc1xyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGVzY2FwZV92YWx1ZV9mb3JfYXR0cihzKSB7XHJcblx0XHRcdHJldHVybiBTdHJpbmcoIHMgPT0gbnVsbCA/ICcnIDogcyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC8mL2csICcmYW1wOycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvPC9nLCAnJmx0OycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvPi9nLCAnJmd0OycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvXCIvZywgJyZxdW90OycgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvJy9nLCAnJiMzOTsnICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTYW5pdGl6ZSBhIHNwYWNlLXNlcGFyYXRlZCBDU1MgY2xhc3MgbGlzdC5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc2FuaXRpemVfY3NzX2NsYXNzbGlzdCh2KSB7XHJcblx0XHRcdGlmICggdiA9PSBudWxsICkgcmV0dXJuICcnO1xyXG5cdFx0XHRyZXR1cm4gU3RyaW5nKCB2ICkucmVwbGFjZSggL1teXFx3XFwtIF0rL2csICcgJyApLnJlcGxhY2UoIC9cXHMrL2csICcgJyApLnRyaW0oKTtcclxuXHRcdH1cclxuLy8gPT0gTkVXID09XHJcblx0XHQvKipcclxuXHRcdCAqIFR1cm4gYW4gYXJiaXRyYXJ5IHZhbHVlIGludG8gYSBjb25zZXJ2YXRpdmUgXCJ0b2tlblwiICh1bmRlcnNjb3JlcywgaHlwaGVucyBhbGxvd2VkKS5cclxuXHRcdCAqIFVzZWZ1bCBmb3Igc2hvcnRjb2RlIHRva2VucywgaWRzIGluIHBsYWluIHRleHQsIGV0Yy5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgdG9fdG9rZW4odikge1xyXG5cdFx0XHRyZXR1cm4gU3RyaW5nKCB2ID8/ICcnIClcclxuXHRcdFx0XHQudHJpbSgpXHJcblx0XHRcdFx0LnJlcGxhY2UoIC9cXHMrL2csICdfJyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC9bXkEtWmEtejAtOV9cXC1dL2csICcnICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDb252ZXJ0IHRvIGtlYmFiLWNhc2UgKGxldHRlcnMsIGRpZ2l0cywgaHlwaGVucykuXHJcblx0XHQgKiBAcGFyYW0ge2FueX0gdlxyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHRvX2tlYmFiKHYpIHtcclxuXHRcdFx0cmV0dXJuIFN0cmluZyggdiA/PyAnJyApXHJcblx0XHRcdFx0LnRyaW0oKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvW19cXHNdKy9nLCAnLScgKVxyXG5cdFx0XHRcdC5yZXBsYWNlKCAvW15BLVphLXowLTktXS9nLCAnJyApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC8tKy9nLCAnLScgKVxyXG5cdFx0XHRcdC50b0xvd2VyQ2FzZSgpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVHJ1dGh5IG5vcm1hbGl6YXRpb24gZm9yIGZvcm0tbGlrZSBpbnB1dHM6IHRydWUsICd0cnVlJywgMSwgJzEnLCAneWVzJywgJ29uJy5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGlzX3RydXRoeSh2KSB7XHJcblx0XHRcdGlmICggdHlwZW9mIHYgPT09ICdib29sZWFuJyApIHJldHVybiB2O1xyXG5cdFx0XHRjb25zdCBzID0gU3RyaW5nKCB2ID8/ICcnICkudHJpbSgpLnRvTG93ZXJDYXNlKCk7XHJcblx0XHRcdHJldHVybiBzID09PSAndHJ1ZScgfHwgcyA9PT0gJzEnIHx8IHMgPT09ICd5ZXMnIHx8IHMgPT09ICdvbic7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDb2VyY2UgdG8gYm9vbGVhbiB3aXRoIGFuIG9wdGlvbmFsIGRlZmF1bHQgZm9yIGVtcHR5IHZhbHVlcy5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IFtkZWY9ZmFsc2VdXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGNvZXJjZV9ib29sZWFuKHYsIGRlZiA9IGZhbHNlKSB7XHJcblx0XHRcdGlmICggdiA9PSBudWxsIHx8IHYgPT09ICcnICkgcmV0dXJuIGRlZjtcclxuXHRcdFx0cmV0dXJuIHRoaXMuaXNfdHJ1dGh5KCB2ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBQYXJzZSBhIFwicGVyY2VudC1saWtlXCIgdmFsdWUgKCczMyd8JzMzJSd8MzMpIHdpdGggZmFsbGJhY2suXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ3xudW1iZXJ8bnVsbHx1bmRlZmluZWR9IHZcclxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSBmYWxsYmFja192YWx1ZVxyXG5cdFx0ICogQHJldHVybnMge251bWJlcn1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHBhcnNlX3BlcmNlbnQodiwgZmFsbGJhY2tfdmFsdWUpIHtcclxuXHRcdFx0aWYgKCB2ID09IG51bGwgKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbGxiYWNrX3ZhbHVlO1xyXG5cdFx0XHR9XHJcblx0XHRcdGNvbnN0IHMgPSBTdHJpbmcoIHYgKS50cmltKCk7XHJcblx0XHRcdGNvbnN0IG4gPSBwYXJzZUZsb2F0KCBzLnJlcGxhY2UoIC8lL2csICcnICkgKTtcclxuXHRcdFx0cmV0dXJuIE51bWJlci5pc0Zpbml0ZSggbiApID8gbiA6IGZhbGxiYWNrX3ZhbHVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ2xhbXAgYSBudW1iZXIgdG8gdGhlIFttaW4sIG1heF0gcmFuZ2UuXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcn0gblxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IG1pblxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IG1heFxyXG5cdFx0ICogQHJldHVybnMge251bWJlcn1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGNsYW1wKG4sIG1pbiwgbWF4KSB7XHJcblx0XHRcdHJldHVybiBNYXRoLm1heCggbWluLCBNYXRoLm1pbiggbWF4LCBuICkgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVzY2FwZSBhIHZhbHVlIGZvciBpbmNsdXNpb24gaW5zaWRlIGEgcXVvdGVkIEhUTUwgYXR0cmlidXRlIChkb3VibGUgcXVvdGVzKS5cclxuXHRcdCAqIFJlcGxhY2VzIG5ld2xpbmVzIHdpdGggc3BhY2VzIGFuZCBkb3VibGUgcXVvdGVzIHdpdGggc2luZ2xlIHF1b3Rlcy5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgZXNjYXBlX2Zvcl9hdHRyX3F1b3RlZCh2KSB7XHJcblx0XHRcdGlmICggdiA9PSBudWxsICkgcmV0dXJuICcnO1xyXG5cdFx0XHRyZXR1cm4gU3RyaW5nKCB2ICkucmVwbGFjZSggL1xccj9cXG4vZywgJyAnICkucmVwbGFjZSggL1wiL2csICdcXCcnICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBFc2NhcGUgZm9yIHNob3J0Y29kZS1saWtlIHRva2VucyB3aGVyZSBkb3VibGUgcXVvdGVzIGFuZCBuZXdsaW5lcyBzaG91bGQgYmUgbmV1dHJhbGl6ZWQuXHJcblx0XHQgKiBAcGFyYW0ge2FueX0gdlxyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGVzY2FwZV9mb3Jfc2hvcnRjb2RlKHYpIHtcclxuXHRcdFx0cmV0dXJuIFN0cmluZyggdiA/PyAnJyApLnJlcGxhY2UoIC9cIi9nLCAnXFxcXFwiJyApLnJlcGxhY2UoIC9cXHI/XFxuL2csICcgJyApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSlNPTi5wYXJzZSB3aXRoIGZhbGxiYWNrIChubyB0aHJvdykuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gc1xyXG5cdFx0ICogQHBhcmFtIHthbnl9IFtmYWxsYmFjaz1udWxsXVxyXG5cdFx0ICogQHJldHVybnMge2FueX1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHNhZmVfanNvbl9wYXJzZShzLCBmYWxsYmFjayA9IG51bGwpIHtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRyZXR1cm4gSlNPTi5wYXJzZSggcyApO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRyZXR1cm4gZmFsbGJhY2s7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFN0cmluZ2lmeSBkYXRhLSogYXR0cmlidXRlIHZhbHVlIHNhZmVseSAob2JqZWN0cyAtPiBKU09OLCBvdGhlcnMgLT4gU3RyaW5nKS5cclxuXHRcdCAqIEBwYXJhbSB7YW55fSB2XHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc3RyaW5naWZ5X2RhdGFfdmFsdWUodikge1xyXG5cdFx0XHRpZiAoIHR5cGVvZiB2ID09PSAnb2JqZWN0JyAmJiB2ICE9PSBudWxsICkge1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gSlNPTi5zdHJpbmdpZnkoIHYgKTtcclxuXHRcdFx0XHR9IGNhdGNoIHtcclxuXHRcdFx0XHRcdGNvbnNvbGUuZXJyb3IoICdXUEJDOiBzdHJpbmdpZnlfZGF0YV92YWx1ZScgKTtcclxuXHRcdFx0XHRcdHJldHVybiAnJztcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIFN0cmluZyggdiApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFN0cmljdCB2YWx1ZSBndWFyZHMgZm9yIENTUyBsZW5ndGhzIGFuZCBoZXggY29sb3JzIChkZWZlbnNlLWluLWRlcHRoKS5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8qKlxyXG5cdFx0ICogU2FuaXRpemUgYSBDU1MgbGVuZ3RoLiBBbGxvd3M6IHB4LCAlLCByZW0sIGVtIChsb3dlci91cHBlcikuXHJcblx0XHQgKiBSZXR1cm5zIGZhbGxiYWNrIGlmIGludmFsaWQuXHJcblx0XHQgKiBAcGFyYW0ge2FueX0gdlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IFtmYWxsYmFjaz0nMTAwJSddXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc2FuaXRpemVfY3NzX2xlbih2LCBmYWxsYmFjayA9ICcxMDAlJykge1xyXG5cdFx0XHRjb25zdCBzID0gU3RyaW5nKCB2ID8/ICcnICkudHJpbSgpO1xyXG5cdFx0XHRjb25zdCBtID0gcy5tYXRjaCggL14oLT9cXGQrKD86XFwuXFxkKyk/KShweHwlfHJlbXxlbSkkL2kgKTtcclxuXHRcdFx0cmV0dXJuIG0gPyBtWzBdIDogU3RyaW5nKCBmYWxsYmFjayApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2FuaXRpemUgYSBoZXggY29sb3IuIEFsbG93cyAjcmdiIG9yICNycmdnYmIgKGNhc2UtaW5zZW5zaXRpdmUpLlxyXG5cdFx0ICogUmV0dXJucyBmYWxsYmFjayBpZiBpbnZhbGlkLlxyXG5cdFx0ICogQHBhcmFtIHthbnl9IHZcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBbZmFsbGJhY2s9JyNlMGUwZTAnXVxyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHNhbml0aXplX2hleF9jb2xvcih2LCBmYWxsYmFjayA9ICcjZTBlMGUwJykge1xyXG5cdFx0XHRjb25zdCBzID0gU3RyaW5nKCB2ID8/ICcnICkudHJpbSgpO1xyXG5cdFx0XHRyZXR1cm4gL14jKD86WzAtOWEtZl17M318WzAtOWEtZl17Nn0pJC9pLnRlc3QoIHMgKSA/IHMgOiBTdHJpbmcoIGZhbGxiYWNrICk7XHJcblx0XHR9XHJcblxyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogV1BCQyBJRCAvIE5hbWUgc2VydmljZS4gR2VuZXJhdGVzLCBzYW5pdGl6ZXMsIGFuZCBlbnN1cmVzIHVuaXF1ZW5lc3MgZm9yIGZpZWxkIGlkcy9uYW1lcy9odG1sX2lkcyB3aXRoaW4gdGhlIGNhbnZhcy5cclxuXHQgKi9cclxuXHRDb3JlLldQQkNfQkZCX0lkU2VydmljZSA9IGNsYXNzICB7XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDb25zdHJ1Y3Rvci4gU2V0IHJvb3QgY29udGFpbmVyIG9mIHRoZSBmb3JtIHBhZ2VzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHBhZ2VzX2NvbnRhaW5lciAtIFJvb3QgY29udGFpbmVyIG9mIHRoZSBmb3JtIHBhZ2VzLlxyXG5cdFx0ICovXHJcblx0XHRjb25zdHJ1Y3RvciggcGFnZXNfY29udGFpbmVyICkge1xyXG5cdFx0XHR0aGlzLnBhZ2VzX2NvbnRhaW5lciA9IHBhZ2VzX2NvbnRhaW5lcjtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVuc3VyZSBhIHVuaXF1ZSAqKmludGVybmFsKiogZmllbGQgaWQgKHN0b3JlZCBpbiBkYXRhLWlkKSB3aXRoaW4gdGhlIGNhbnZhcy5cclxuXHRcdCAqIFN0YXJ0cyBmcm9tIGEgZGVzaXJlZCBpZCAoYWxyZWFkeSBzYW5pdGl6ZWQgb3Igbm90KSBhbmQgYXBwZW5kcyBzdWZmaXhlcyBpZiBuZWVkZWQuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGJhc2VJZCAtIERlc2lyZWQgaWQuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBVbmlxdWUgaWQuXHJcblx0XHQgKi9cclxuXHRcdGVuc3VyZV91bmlxdWVfZmllbGRfaWQoYmFzZUlkLCBjdXJyZW50RWwgPSBudWxsKSB7XHJcblx0XHRcdGNvbnN0IGJhc2UgICAgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLnNhbml0aXplX2h0bWxfaWQoIGJhc2VJZCApO1xyXG5cdFx0XHRsZXQgaWQgICAgICAgID0gYmFzZSB8fCAnZmllbGQnO1xyXG5cdFx0XHRjb25zdCBlc2MgICAgID0gKHYpID0+IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuZXNjX2F0dHJfdmFsdWVfZm9yX3NlbGVjdG9yKCB2ICk7XHJcblx0XHRcdGNvbnN0IGVzY1VpZCAgPSAodikgPT4gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5lc2NfYXR0cl92YWx1ZV9mb3Jfc2VsZWN0b3IoIHYgKTtcclxuXHRcdFx0Y29uc3Qgbm90U2VsZiA9IGN1cnJlbnRFbD8uZGF0YXNldD8udWlkID8gYDpub3QoW2RhdGEtdWlkPVwiJHtlc2NVaWQoIGN1cnJlbnRFbC5kYXRhc2V0LnVpZCApfVwiXSlgIDogJyc7XHJcblx0XHRcdHdoaWxlICggdGhpcy5wYWdlc19jb250YWluZXI/LnF1ZXJ5U2VsZWN0b3IoXHJcblx0XHRcdFx0YC53cGJjX2JmYl9fcGFuZWwtLXByZXZpZXcgLndwYmNfYmZiX19maWVsZCR7bm90U2VsZn1bZGF0YS1pZD1cIiR7ZXNjKGlkKX1cIl0sIC53cGJjX2JmYl9fcGFuZWwtLXByZXZpZXcgLndwYmNfYmZiX19zZWN0aW9uJHtub3RTZWxmfVtkYXRhLWlkPVwiJHtlc2MoaWQpfVwiXWBcclxuXHRcdFx0KSApIHtcclxuXHRcdFx0XHQvLyBFeGNsdWRlcyBzZWxmIGJ5IGRhdGEtdWlkIC5cclxuXHRcdFx0XHRjb25zdCBmb3VuZCA9IHRoaXMucGFnZXNfY29udGFpbmVyLnF1ZXJ5U2VsZWN0b3IoIGAud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGRbZGF0YS1pZD1cIiR7ZXNjKCBpZCApfVwiXSwgLndwYmNfYmZiX19wYW5lbC0tcHJldmlldyAud3BiY19iZmJfX3NlY3Rpb25bZGF0YS1pZD1cIiR7ZXNjKCBpZCApfVwiXWAgKTtcclxuXHRcdFx0XHRpZiAoIGZvdW5kICYmIGN1cnJlbnRFbCAmJiBmb3VuZCA9PT0gY3VycmVudEVsICkge1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGlkID0gYCR7YmFzZSB8fCAnZmllbGQnfS0ke01hdGgucmFuZG9tKCkudG9TdHJpbmcoIDM2ICkuc2xpY2UoIDIsIDUgKX1gO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiBpZDtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVuc3VyZSBhIHVuaXF1ZSBIVE1MIG5hbWUgYWNyb3NzIHRoZSBmb3JtLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBiYXNlIC0gRGVzaXJlZCBiYXNlIG5hbWUgKHVuL3Nhbml0aXplZCkuXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fG51bGx9IGN1cnJlbnRFbCAtIElmIHByb3ZpZGVkLCBpZ25vcmUgY29uZmxpY3RzIHdpdGggdGhpcyBlbGVtZW50LlxyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ30gVW5pcXVlIG5hbWUuXHJcblx0XHQgKi9cclxuXHRcdGVuc3VyZV91bmlxdWVfZmllbGRfbmFtZShiYXNlLCBjdXJyZW50RWwgPSBudWxsKSB7XHJcblx0XHRcdGxldCBuYW1lICAgICAgPSBiYXNlIHx8ICdmaWVsZCc7XHJcblx0XHRcdGNvbnN0IGVzYyAgICAgPSAodikgPT4gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5lc2NfYXR0cl92YWx1ZV9mb3Jfc2VsZWN0b3IoIHYgKTtcclxuXHRcdFx0Y29uc3QgZXNjVWlkICA9ICh2KSA9PiBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmVzY19hdHRyX3ZhbHVlX2Zvcl9zZWxlY3RvciggdiApO1xyXG5cdFx0XHQvLyBFeGNsdWRlIHRoZSBjdXJyZW50IGZpZWxkICphbmQgYW55IERPTSBtaXJyb3JzIG9mIGl0KiAoc2FtZSBkYXRhLXVpZClcclxuXHRcdFx0Y29uc3QgdWlkICAgICA9IGN1cnJlbnRFbD8uZGF0YXNldD8udWlkO1xyXG5cdFx0XHRjb25zdCBub3RTZWxmID0gdWlkID8gYDpub3QoW2RhdGEtdWlkPVwiJHtlc2NVaWQoIHVpZCApfVwiXSlgIDogJyc7XHJcblx0XHRcdHdoaWxlICggdHJ1ZSApIHtcclxuXHRcdFx0XHRjb25zdCBzZWxlY3RvciA9IGAud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGQke25vdFNlbGZ9W2RhdGEtbmFtZT1cIiR7ZXNjKCBuYW1lICl9XCJdYDtcclxuXHRcdFx0XHRjb25zdCBjbGFzaGVzICA9IHRoaXMucGFnZXNfY29udGFpbmVyPy5xdWVyeVNlbGVjdG9yQWxsKCBzZWxlY3RvciApIHx8IFtdO1xyXG5cdFx0XHRcdGlmICggY2xhc2hlcy5sZW5ndGggPT09IDAgKSBicmVhazsgICAgICAgICAgIC8vIG5vYm9keSBlbHNlIHVzZXMgdGhpcyBuYW1lXHJcblx0XHRcdFx0Y29uc3QgbSA9IG5hbWUubWF0Y2goIC8tKFxcZCspJC8gKTtcclxuXHRcdFx0XHRuYW1lICAgID0gbSA/IG5hbWUucmVwbGFjZSggLy1cXGQrJC8sICctJyArIChOdW1iZXIoIG1bMV0gKSArIDEpICkgOiBgJHtiYXNlfS0yYDtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gbmFtZTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNldCBmaWVsZCdzIElOVEVSTkFMIGlkIChkYXRhLWlkKSBvbiBhbiBlbGVtZW50LiBFbnN1cmVzIHVuaXF1ZW5lc3MgYW5kIG9wdGlvbmFsbHkgYXNrcyBjYWxsZXIgdG8gcmVmcmVzaCBwcmV2aWV3LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGZpZWxkX2VsIC0gRmllbGQgZWxlbWVudCBpbiB0aGUgY2FudmFzLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IG5ld0lkUmF3IC0gRGVzaXJlZCBpZCAodW4vc2FuaXRpemVkKS5cclxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gW3JlbmRlclByZXZpZXc9ZmFsc2VdIC0gQ2FsbGVyIGNhbiBkZWNpZGUgdG8gcmUtcmVuZGVyIHByZXZpZXcuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBBcHBsaWVkIHVuaXF1ZSBpZC5cclxuXHRcdCAqL1xyXG5cdFx0c2V0X2ZpZWxkX2lkKCBmaWVsZF9lbCwgbmV3SWRSYXcsIHJlbmRlclByZXZpZXcgPSBmYWxzZSApIHtcclxuXHRcdFx0Y29uc3QgZGVzaXJlZCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuc2FuaXRpemVfaHRtbF9pZCggbmV3SWRSYXcgKTtcclxuXHRcdFx0Y29uc3QgdW5pcXVlICA9IHRoaXMuZW5zdXJlX3VuaXF1ZV9maWVsZF9pZCggZGVzaXJlZCwgZmllbGRfZWwgKTtcclxuXHRcdFx0ZmllbGRfZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1pZCcsIHVuaXF1ZSApO1xyXG5cdFx0XHRpZiAoIHJlbmRlclByZXZpZXcgKSB7XHJcblx0XHRcdFx0Ly8gQ2FsbGVyIGRlY2lkZXMgaWYgLyB3aGVuIHRvIHJlbmRlci5cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gdW5pcXVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2V0IGZpZWxkJ3MgUkVRVUlSRUQgSFRNTCBuYW1lIChkYXRhLW5hbWUpLiBFbnN1cmVzIHNhbml0aXplZCArIHVuaXF1ZSBwZXIgZm9ybS5cclxuXHRcdCAqIEZhbGxzIGJhY2sgdG8gc2FuaXRpemVkIGludGVybmFsIGlkIGlmIHVzZXIgcHJvdmlkZXMgZW1wdHkgdmFsdWUuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZmllbGRfZWwgLSBGaWVsZCBlbGVtZW50IGluIHRoZSBjYW52YXMuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gbmV3TmFtZVJhdyAtIERlc2lyZWQgbmFtZSAodW4vc2FuaXRpemVkKS5cclxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gW3JlbmRlclByZXZpZXc9ZmFsc2VdIC0gQ2FsbGVyIGNhbiBkZWNpZGUgdG8gcmUtcmVuZGVyIHByZXZpZXcuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBBcHBsaWVkIHVuaXF1ZSBuYW1lLlxyXG5cdFx0ICovXHJcblx0XHRzZXRfZmllbGRfbmFtZSggZmllbGRfZWwsIG5ld05hbWVSYXcsIHJlbmRlclByZXZpZXcgPSBmYWxzZSApIHtcclxuXHRcdFx0Y29uc3QgcmF3ICA9IChuZXdOYW1lUmF3ID09IG51bGwgPyAnJyA6IFN0cmluZyggbmV3TmFtZVJhdyApKS50cmltKCk7XHJcblx0XHRcdGNvbnN0IGJhc2UgPSByYXdcclxuXHRcdFx0XHQ/IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuc2FuaXRpemVfaHRtbF9uYW1lKCByYXcgKVxyXG5cdFx0XHRcdDogQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5zYW5pdGl6ZV9odG1sX25hbWUoIGZpZWxkX2VsLmdldEF0dHJpYnV0ZSggJ2RhdGEtaWQnICkgfHwgJ2ZpZWxkJyApO1xyXG5cclxuXHRcdFx0Y29uc3QgdW5pcXVlID0gdGhpcy5lbnN1cmVfdW5pcXVlX2ZpZWxkX25hbWUoIGJhc2UsIGZpZWxkX2VsICk7XHJcblx0XHRcdGZpZWxkX2VsLnNldEF0dHJpYnV0ZSggJ2RhdGEtbmFtZScsIHVuaXF1ZSApO1xyXG5cdFx0XHRpZiAoIHJlbmRlclByZXZpZXcgKSB7XHJcblx0XHRcdFx0Ly8gQ2FsbGVyIGRlY2lkZXMgaWYgLyB3aGVuIHRvIHJlbmRlci5cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gdW5pcXVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2V0IGZpZWxkJ3MgT1BUSU9OQUwgcHVibGljIEhUTUwgaWQgKGRhdGEtaHRtbF9pZCkuIEVtcHR5IHZhbHVlIHJlbW92ZXMgdGhlIGF0dHJpYnV0ZS5cclxuXHRcdCAqIEVuc3VyZXMgc2FuaXRpemF0aW9uICsgdW5pcXVlbmVzcyBhbW9uZyBvdGhlciBkZWNsYXJlZCBIVE1MIGlkcy5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBmaWVsZF9lbCAtIEZpZWxkIGVsZW1lbnQgaW4gdGhlIGNhbnZhcy5cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBuZXdIdG1sSWRSYXcgLSBEZXNpcmVkIGh0bWxfaWQgKG9wdGlvbmFsKS5cclxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gW3JlbmRlclByZXZpZXc9ZmFsc2VdIC0gQ2FsbGVyIGNhbiBkZWNpZGUgdG8gcmUtcmVuZGVyIHByZXZpZXcuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBUaGUgYXBwbGllZCBodG1sX2lkIG9yIGVtcHR5IHN0cmluZyBpZiByZW1vdmVkLlxyXG5cdFx0ICovXHJcblx0XHRzZXRfZmllbGRfaHRtbF9pZCggZmllbGRfZWwsIG5ld0h0bWxJZFJhdywgcmVuZGVyUHJldmlldyA9IGZhbHNlICkge1xyXG5cdFx0XHRjb25zdCByYXcgPSAobmV3SHRtbElkUmF3ID09IG51bGwgPyAnJyA6IFN0cmluZyggbmV3SHRtbElkUmF3ICkpLnRyaW0oKTtcclxuXHJcblx0XHRcdGlmICggcmF3ID09PSAnJyApIHtcclxuXHRcdFx0XHRmaWVsZF9lbC5yZW1vdmVBdHRyaWJ1dGUoICdkYXRhLWh0bWxfaWQnICk7XHJcblx0XHRcdFx0aWYgKCByZW5kZXJQcmV2aWV3ICkge1xyXG5cdFx0XHRcdFx0Ly8gQ2FsbGVyIGRlY2lkZXMgaWYgLyB3aGVuIHRvIHJlbmRlci5cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0cmV0dXJuICcnO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCBkZXNpcmVkID0gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5zYW5pdGl6ZV9odG1sX2lkKCByYXcgKTtcclxuXHRcdFx0bGV0IGh0bWxJZCAgICA9IGRlc2lyZWQ7XHJcblx0XHRcdGNvbnN0IGVzYyAgICAgPSAodikgPT4gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5lc2NfYXR0cl92YWx1ZV9mb3Jfc2VsZWN0b3IoIHYgKTtcclxuXHRcdFx0Y29uc3QgZXNjVWlkICA9ICh2KSA9PiBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmVzY19hdHRyX3ZhbHVlX2Zvcl9zZWxlY3RvciggdiApO1xyXG5cclxuXHRcdFx0d2hpbGUgKCB0cnVlICkge1xyXG5cclxuXHRcdFx0XHRjb25zdCB1aWQgICAgID0gZmllbGRfZWw/LmRhdGFzZXQ/LnVpZDtcclxuXHRcdFx0XHRjb25zdCBub3RTZWxmID0gdWlkID8gYDpub3QoW2RhdGEtdWlkPVwiJHtlc2NVaWQoIHVpZCApfVwiXSlgIDogJyc7XHJcblxyXG5cdFx0XHRcdGNvbnN0IGNsYXNoSW5DYW52YXMgPSB0aGlzLnBhZ2VzX2NvbnRhaW5lcj8ucXVlcnlTZWxlY3RvcihcclxuXHRcdFx0XHRcdGAud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGQke25vdFNlbGZ9W2RhdGEtaHRtbF9pZD1cIiR7ZXNjKCBodG1sSWQgKX1cIl0sYCArXHJcblx0XHRcdFx0XHRgLndwYmNfYmZiX19wYW5lbC0tcHJldmlldyAud3BiY19iZmJfX3NlY3Rpb24ke25vdFNlbGZ9W2RhdGEtaHRtbF9pZD1cIiR7ZXNjKCBodG1sSWQgKX1cIl1gXHJcblx0XHRcdFx0KTtcclxuXHRcdFx0XHRjb25zdCBkb21DbGFzaCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBodG1sSWQgKTtcclxuXHJcblx0XHRcdFx0Ly8gQWxsb3cgd2hlbiB0aGUgb25seSBcImNsYXNoXCIgaXMgaW5zaWRlIHRoaXMgc2FtZSBmaWVsZCAoZS5nLiwgdGhlIGlucHV0IHlvdSBqdXN0IHJlbmRlcmVkKVxyXG5cdFx0XHRcdGNvbnN0IGRvbUNsYXNoSXNTZWxmID0gZG9tQ2xhc2ggPT09IGZpZWxkX2VsIHx8IChkb21DbGFzaCAmJiBmaWVsZF9lbC5jb250YWlucyggZG9tQ2xhc2ggKSk7XHJcblxyXG5cdFx0XHRcdGlmICggIWNsYXNoSW5DYW52YXMgJiYgKCFkb21DbGFzaCB8fCBkb21DbGFzaElzU2VsZikgKSB7XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGNvbnN0IG0gPSBodG1sSWQubWF0Y2goIC8tKFxcZCspJC8gKTtcclxuXHRcdFx0XHRodG1sSWQgID0gbSA/IGh0bWxJZC5yZXBsYWNlKCAvLVxcZCskLywgJy0nICsgKE51bWJlciggbVsxXSApICsgMSkgKSA6IGAke2Rlc2lyZWR9LTJgO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRmaWVsZF9lbC5zZXRBdHRyaWJ1dGUoICdkYXRhLWh0bWxfaWQnLCBodG1sSWQgKTtcclxuXHRcdFx0aWYgKCByZW5kZXJQcmV2aWV3ICkge1xyXG5cdFx0XHRcdC8vIENhbGxlciBkZWNpZGVzIGlmIC8gd2hlbiB0byByZW5kZXIuXHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIGh0bWxJZDtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBXUEJDIExheW91dCBzZXJ2aWNlLiBFbmNhcHN1bGF0ZXMgY29sdW1uIHdpZHRoIG1hdGggd2l0aCBnYXAgaGFuZGxpbmcsIHByZXNldHMsIGFuZCB1dGlsaXRpZXMuXHJcblx0ICovXHJcblx0Q29yZS5XUEJDX0JGQl9MYXlvdXRTZXJ2aWNlID0gY2xhc3MgIHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIENvbnN0cnVjdG9yLiBTZXQgb3B0aW9ucyB3aXRoIGdhcCBiZXR3ZWVuIGNvbHVtbnMgKCUpLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7eyBjb2xfZ2FwX3BlcmNlbnQ/OiBudW1iZXIgfX0gW29wdHNdIC0gT3B0aW9ucyB3aXRoIGdhcCBiZXR3ZWVuIGNvbHVtbnMgKCUpLlxyXG5cdFx0ICovXHJcblx0XHRjb25zdHJ1Y3Rvciggb3B0cyA9IHt9ICkge1xyXG5cdFx0XHR0aGlzLmNvbF9nYXBfcGVyY2VudCA9IE51bWJlci5pc0Zpbml0ZSggK29wdHMuY29sX2dhcF9wZXJjZW50ICkgPyArb3B0cy5jb2xfZ2FwX3BlcmNlbnQgOiAzO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ29tcHV0ZSBub3JtYWxpemVkIGZsZXgtYmFzaXMgdmFsdWVzIGZvciBhIHJvdywgcmVzcGVjdGluZyBjb2x1bW4gZ2Fwcy5cclxuXHRcdCAqIFJldHVybnMgYmFzZXMgdGhhdCBzdW0gdG8gYXZhaWxhYmxlID0gMTAwIC0gKG4tMSkqZ2FwLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvd19lbCAtIFJvdyBlbGVtZW50IGNvbnRhaW5pbmcgLndwYmNfYmZiX19jb2x1bW4gY2hpbGRyZW4uXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcn0gW2dhcF9wZXJjZW50PXRoaXMuY29sX2dhcF9wZXJjZW50XSAtIEdhcCBwZXJjZW50IGJldHdlZW4gY29sdW1ucy5cclxuXHRcdCAqIEByZXR1cm5zIHt7YXZhaWxhYmxlOm51bWJlcixiYXNlczpudW1iZXJbXX19IEF2YWlsYWJsZSBzcGFjZSBhbmQgYmFzaXMgdmFsdWVzLlxyXG5cdFx0ICovXHJcblx0XHRjb21wdXRlX2VmZmVjdGl2ZV9iYXNlc19mcm9tX3Jvdyggcm93X2VsLCBnYXBfcGVyY2VudCA9IHRoaXMuY29sX2dhcF9wZXJjZW50ICkge1xyXG5cdFx0XHRjb25zdCBjb2xzID0gQXJyYXkuZnJvbSggcm93X2VsPy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkgfHwgW10gKTtcclxuXHRcdFx0Y29uc3QgbiAgICA9IGNvbHMubGVuZ3RoIHx8IDE7XHJcblxyXG5cdFx0XHRjb25zdCByYXcgPSBjb2xzLm1hcCggKCBjb2wgKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgdyA9IGNvbC5zdHlsZS5mbGV4QmFzaXMgfHwgJyc7XHJcblx0XHRcdFx0Y29uc3QgcCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUucGFyc2VfcGVyY2VudCggdywgTmFOICk7XHJcblx0XHRcdFx0cmV0dXJuIE51bWJlci5pc0Zpbml0ZSggcCApID8gcCA6ICgxMDAgLyBuKTtcclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0Y29uc3Qgc3VtX3JhdyAgICA9IHJhdy5yZWR1Y2UoICggYSwgYiApID0+IGEgKyBiLCAwICkgfHwgMTAwO1xyXG5cdFx0XHRjb25zdCBncCAgICAgICAgID0gTnVtYmVyLmlzRmluaXRlKCArZ2FwX3BlcmNlbnQgKSA/ICtnYXBfcGVyY2VudCA6IDM7XHJcblx0XHRcdGNvbnN0IHRvdGFsX2dhcHMgPSBNYXRoLm1heCggMCwgbiAtIDEgKSAqIGdwO1xyXG5cdFx0XHRjb25zdCBhdmFpbGFibGUgID0gTWF0aC5tYXgoIDAsIDEwMCAtIHRvdGFsX2dhcHMgKTtcclxuXHRcdFx0Y29uc3Qgc2NhbGUgICAgICA9IGF2YWlsYWJsZSAvIHN1bV9yYXc7XHJcblxyXG5cdFx0XHRyZXR1cm4ge1xyXG5cdFx0XHRcdGF2YWlsYWJsZSxcclxuXHRcdFx0XHRiYXNlczogcmF3Lm1hcCggKCBwICkgPT4gTWF0aC5tYXgoIDAsIHAgKiBzY2FsZSApIClcclxuXHRcdFx0fTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEFwcGx5IGNvbXB1dGVkIGJhc2VzIHRvIHRoZSByb3cncyBjb2x1bW5zIChzZXRzIGZsZXgtYmFzaXMgJSkuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm93X2VsIC0gUm93IGVsZW1lbnQuXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcltdfSBiYXNlcyAtIEFycmF5IG9mIGJhc2lzIHZhbHVlcyAocGVyY2VudCBvZiBmdWxsIDEwMCkuXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqL1xyXG5cdFx0YXBwbHlfYmFzZXNfdG9fcm93KCByb3dfZWwsIGJhc2VzICkge1xyXG5cdFx0XHRjb25zdCBjb2xzID0gQXJyYXkuZnJvbSggcm93X2VsPy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkgfHwgW10gKTtcclxuXHRcdFx0Y29scy5mb3JFYWNoKCAoIGNvbCwgaSApID0+IHtcclxuXHRcdFx0XHRjb25zdCBwICAgICAgICAgICAgID0gYmFzZXNbaV0gPz8gMDtcclxuXHRcdFx0XHRjb2wuc3R5bGUuZmxleEJhc2lzID0gYCR7cH0lYDtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRGlzdHJpYnV0ZSBjb2x1bW5zIGV2ZW5seSwgcmVzcGVjdGluZyBnYXAuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm93X2VsIC0gUm93IGVsZW1lbnQuXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcn0gW2dhcF9wZXJjZW50PXRoaXMuY29sX2dhcF9wZXJjZW50XSAtIEdhcCBwZXJjZW50LlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKi9cclxuXHRcdHNldF9lcXVhbF9iYXNlcyggcm93X2VsLCBnYXBfcGVyY2VudCA9IHRoaXMuY29sX2dhcF9wZXJjZW50ICkge1xyXG5cdFx0XHRjb25zdCBjb2xzICAgICAgID0gQXJyYXkuZnJvbSggcm93X2VsPy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkgfHwgW10gKTtcclxuXHRcdFx0Y29uc3QgbiAgICAgICAgICA9IGNvbHMubGVuZ3RoIHx8IDE7XHJcblx0XHRcdGNvbnN0IGdwICAgICAgICAgPSBOdW1iZXIuaXNGaW5pdGUoICtnYXBfcGVyY2VudCApID8gK2dhcF9wZXJjZW50IDogMztcclxuXHRcdFx0Y29uc3QgdG90YWxfZ2FwcyA9IE1hdGgubWF4KCAwLCBuIC0gMSApICogZ3A7XHJcblx0XHRcdGNvbnN0IGF2YWlsYWJsZSAgPSBNYXRoLm1heCggMCwgMTAwIC0gdG90YWxfZ2FwcyApO1xyXG5cdFx0XHRjb25zdCBlYWNoICAgICAgID0gYXZhaWxhYmxlIC8gbjtcclxuXHRcdFx0dGhpcy5hcHBseV9iYXNlc190b19yb3coIHJvd19lbCwgQXJyYXkoIG4gKS5maWxsKCBlYWNoICkgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEFwcGx5IGEgcHJlc2V0IG9mIHJlbGF0aXZlIHdlaWdodHMgdG8gYSByb3cvc2VjdGlvbi5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBzZWN0aW9uT3JSb3cgLSAud3BiY19iZmJfX3NlY3Rpb24gb3IgaXRzIGNoaWxkIC53cGJjX2JmYl9fcm93LlxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJbXX0gd2VpZ2h0cyAtIFJlbGF0aXZlIHdlaWdodHMgKGUuZy4sIFsxLDMsMV0pLlxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IFtnYXBfcGVyY2VudD10aGlzLmNvbF9nYXBfcGVyY2VudF0gLSBHYXAgcGVyY2VudC5cclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHRhcHBseV9sYXlvdXRfcHJlc2V0KCBzZWN0aW9uT3JSb3csIHdlaWdodHMsIGdhcF9wZXJjZW50ID0gdGhpcy5jb2xfZ2FwX3BlcmNlbnQgKSB7XHJcblx0XHRcdGNvbnN0IHJvdyA9IHNlY3Rpb25PclJvdz8uY2xhc3NMaXN0Py5jb250YWlucyggJ3dwYmNfYmZiX19yb3cnIClcclxuXHRcdFx0XHQ/IHNlY3Rpb25PclJvd1xyXG5cdFx0XHRcdDogc2VjdGlvbk9yUm93Py5xdWVyeVNlbGVjdG9yKCAnOnNjb3BlID4gLndwYmNfYmZiX19yb3cnICk7XHJcblxyXG5cdFx0XHRpZiAoICEgcm93ICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgY29scyA9IEFycmF5LmZyb20oIHJvdy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkgfHwgW10gKTtcclxuXHRcdFx0Y29uc3QgbiAgICA9IGNvbHMubGVuZ3RoIHx8IDE7XHJcblxyXG5cdFx0XHRpZiAoICEgQXJyYXkuaXNBcnJheSggd2VpZ2h0cyApIHx8IHdlaWdodHMubGVuZ3RoICE9PSBuICkge1xyXG5cdFx0XHRcdHRoaXMuc2V0X2VxdWFsX2Jhc2VzKCByb3csIGdhcF9wZXJjZW50ICk7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCBzdW0gICAgICAgPSB3ZWlnaHRzLnJlZHVjZSggKCBhLCBiICkgPT4gYSArIE1hdGgubWF4KCAwLCBOdW1iZXIoIGIgKSB8fCAwICksIDAgKSB8fCAxO1xyXG5cdFx0XHRjb25zdCBncCAgICAgICAgPSBOdW1iZXIuaXNGaW5pdGUoICtnYXBfcGVyY2VudCApID8gK2dhcF9wZXJjZW50IDogMztcclxuXHRcdFx0Y29uc3QgYXZhaWxhYmxlID0gTWF0aC5tYXgoIDAsIDEwMCAtIE1hdGgubWF4KCAwLCBuIC0gMSApICogZ3AgKTtcclxuXHRcdFx0Y29uc3QgYmFzZXMgICAgID0gd2VpZ2h0cy5tYXAoICggdyApID0+IE1hdGgubWF4KCAwLCAoTnVtYmVyKCB3ICkgfHwgMCkgLyBzdW0gKiBhdmFpbGFibGUgKSApO1xyXG5cclxuXHRcdFx0dGhpcy5hcHBseV9iYXNlc190b19yb3coIHJvdywgYmFzZXMgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEJ1aWxkIHByZXNldCB3ZWlnaHQgbGlzdHMgZm9yIGEgZ2l2ZW4gY29sdW1uIGNvdW50LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSBuIC0gQ29sdW1uIGNvdW50LlxyXG5cdFx0ICogQHJldHVybnMge251bWJlcltdW119IExpc3Qgb2Ygd2VpZ2h0IGFycmF5cy5cclxuXHRcdCAqL1xyXG5cdFx0YnVpbGRfcHJlc2V0c19mb3JfY29sdW1ucyggbiApIHtcclxuXHRcdFx0c3dpdGNoICggbiApIHtcclxuXHRcdFx0XHRjYXNlIDE6XHJcblx0XHRcdFx0XHRyZXR1cm4gWyBbIDEgXSBdO1xyXG5cdFx0XHRcdGNhc2UgMjpcclxuXHRcdFx0XHRcdHJldHVybiBbIFsgMSwgMiBdLCBbIDIsIDEgXSwgWyAxLCAzIF0sIFsgMywgMSBdIF07XHJcblx0XHRcdFx0Y2FzZSAzOlxyXG5cdFx0XHRcdFx0cmV0dXJuIFsgWyAxLCAzLCAxIF0sIFsgMSwgMiwgMSBdLCBbIDIsIDEsIDEgXSwgWyAxLCAxLCAyIF0gXTtcclxuXHRcdFx0XHRjYXNlIDQ6XHJcblx0XHRcdFx0XHRyZXR1cm4gWyBbIDEsIDIsIDIsIDEgXSwgWyAyLCAxLCAxLCAxIF0sIFsgMSwgMSwgMSwgMiBdIF07XHJcblx0XHRcdFx0ZGVmYXVsdDpcclxuXHRcdFx0XHRcdHJldHVybiBbIEFycmF5KCBuICkuZmlsbCggMSApIF07XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEZvcm1hdCBhIGh1bWFuLXJlYWRhYmxlIGxhYmVsIGxpa2UgXCI1MCUvMjUlLzI1JVwiIGZyb20gd2VpZ2h0cy5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcltdfSB3ZWlnaHRzIC0gV2VpZ2h0IGxpc3QuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBMYWJlbCBzdHJpbmcuXHJcblx0XHQgKi9cclxuXHRcdGZvcm1hdF9wcmVzZXRfbGFiZWwoIHdlaWdodHMgKSB7XHJcblx0XHRcdGNvbnN0IHN1bSA9IHdlaWdodHMucmVkdWNlKCAoIGEsIGIgKSA9PiBhICsgKE51bWJlciggYiApIHx8IDApLCAwICkgfHwgMTtcclxuXHRcdFx0cmV0dXJuIHdlaWdodHMubWFwKCAoIHcgKSA9PiBNYXRoLnJvdW5kKCAoKE51bWJlciggdyApIHx8IDApIC8gc3VtKSAqIDEwMCApICkuam9pbiggJyUvJyApICsgJyUnO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogUGFyc2UgY29tbWEvc3BhY2Ugc2VwYXJhdGVkIHdlaWdodHMgaW50byBudW1iZXJzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBpbnB1dCAtIFVzZXIgaW5wdXQgbGlrZSBcIjIwLDYwLDIwXCIuXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyW119IFBhcnNlZCB3ZWlnaHRzLlxyXG5cdFx0ICovXHJcblx0XHRwYXJzZV93ZWlnaHRzKCBpbnB1dCApIHtcclxuXHRcdFx0aWYgKCAhIGlucHV0ICkge1xyXG5cdFx0XHRcdHJldHVybiBbXTtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gU3RyaW5nKCBpbnB1dCApXHJcblx0XHRcdFx0LnJlcGxhY2UoIC9bXlxcZCwuXFxzXS9nLCAnJyApXHJcblx0XHRcdFx0LnNwbGl0KCAvW1xccyxdKy8gKVxyXG5cdFx0XHRcdC5tYXAoICggcyApID0+IHBhcnNlRmxvYXQoIHMgKSApXHJcblx0XHRcdFx0LmZpbHRlciggKCBuICkgPT4gTnVtYmVyLmlzRmluaXRlKCBuICkgJiYgbiA+PSAwICk7XHJcblx0XHR9XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogV1BCQyBVc2FnZSBMaW1pdCBzZXJ2aWNlLlxyXG5cdCAqIENvdW50cyBmaWVsZCB1c2FnZSBieSBrZXksIGNvbXBhcmVzIHRvIHBhbGV0dGUgbGltaXRzLCBhbmQgdXBkYXRlcyBwYWxldHRlIFVJLlxyXG5cdCAqL1xyXG5cdENvcmUuV1BCQ19CRkJfVXNhZ2VMaW1pdFNlcnZpY2UgPSBjbGFzcyAge1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ29uc3RydWN0b3IuIFNldCBwYWdlc19jb250YWluZXIgYW5kIHBhbGV0dGVfdWwuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcGFnZXNfY29udGFpbmVyIC0gQ2FudmFzIHJvb3QgdGhhdCBob2xkcyBwbGFjZWQgZmllbGRzLlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudFtdfG51bGx9IHBhbGV0dGVfdWxzPzogICBQYWxldHRlcyBVTCB3aXRoIC53cGJjX2JmYl9fZmllbGQgaXRlbXMgKG1heSBiZSBudWxsKS5cclxuXHRcdCAqL1xyXG5cdFx0Y29uc3RydWN0b3IocGFnZXNfY29udGFpbmVyLCBwYWxldHRlX3Vscykge1xyXG5cdFx0XHR0aGlzLnBhZ2VzX2NvbnRhaW5lciA9IHBhZ2VzX2NvbnRhaW5lcjtcclxuXHRcdFx0Ly8gTm9ybWFsaXplIHRvIGFuIGFycmF5OyB3ZeKAmWxsIHN0aWxsIGJlIHJvYnVzdCBpZiBub25lIHByb3ZpZGVkLlxyXG5cdFx0XHR0aGlzLnBhbGV0dGVfdWxzICAgICA9IEFycmF5LmlzQXJyYXkoIHBhbGV0dGVfdWxzICkgPyBwYWxldHRlX3VscyA6IChwYWxldHRlX3VscyA/IFsgcGFsZXR0ZV91bHMgXSA6IFtdKTtcclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBQYXJzZSB1c2FnZSBsaW1pdCBmcm9tIHJhdyBkYXRhc2V0IHZhbHVlLiBNaXNzaW5nL2ludmFsaWQgLT4gSW5maW5pdHkuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd8bnVtYmVyfG51bGx8dW5kZWZpbmVkfSByYXcgLSBSYXcgYXR0cmlidXRlIHZhbHVlLlxyXG5cdFx0ICogQHJldHVybnMge251bWJlcn0gTGltaXQgbnVtYmVyIG9yIEluZmluaXR5LlxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgcGFyc2VfdXNhZ2VfbGltaXQoIHJhdyApIHtcclxuXHRcdFx0aWYgKCByYXcgPT0gbnVsbCApIHtcclxuXHRcdFx0XHRyZXR1cm4gSW5maW5pdHk7XHJcblx0XHRcdH1cclxuXHRcdFx0Y29uc3QgbiA9IHBhcnNlSW50KCByYXcsIDEwICk7XHJcblx0XHRcdHJldHVybiBOdW1iZXIuaXNGaW5pdGUoIG4gKSA/IG4gOiBJbmZpbml0eTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIENvdW50IGhvdyBtYW55IGluc3RhbmNlcyBleGlzdCBwZXIgdXNhZ2Vfa2V5IGluIHRoZSBjYW52YXMuXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMge1JlY29yZDxzdHJpbmcsIG51bWJlcj59IE1hcCBvZiB1c2FnZV9rZXkgLT4gY291bnQuXHJcblx0XHQgKi9cclxuXHRcdGNvdW50X3VzYWdlX2J5X2tleSgpIHtcclxuXHRcdFx0Y29uc3QgdXNlZCA9IHt9O1xyXG5cdFx0XHRjb25zdCBhbGwgID0gdGhpcy5wYWdlc19jb250YWluZXI/LnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGQ6bm90KC5pcy1pbnZhbGlkKScgKSB8fCBbXTtcclxuXHRcdFx0YWxsLmZvckVhY2goICggZWwgKSA9PiB7XHJcblx0XHRcdFx0Y29uc3Qga2V5ID0gZWwuZGF0YXNldC51c2FnZV9rZXkgfHwgZWwuZGF0YXNldC50eXBlIHx8IGVsLmRhdGFzZXQuaWQ7XHJcblx0XHRcdFx0aWYgKCAhIGtleSApIHtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0dXNlZFtrZXldID0gKHVzZWRba2V5XSB8fCAwKSArIDE7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0cmV0dXJuIHVzZWQ7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZXR1cm4gcGFsZXR0ZSBsaW1pdCBmb3IgYSBnaXZlbiB1c2FnZSBrZXkgKGlkIG9mIHRoZSBwYWxldHRlIGl0ZW0pLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgLSBVc2FnZSBrZXkuXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyfSBMaW1pdCB2YWx1ZSBvciBJbmZpbml0eS5cclxuXHRcdCAqL1xyXG5cdFx0Z2V0X2xpbWl0X2Zvcl9rZXkoa2V5KSB7XHJcblx0XHRcdGlmICggISBrZXkgKSB7XHJcblx0XHRcdFx0cmV0dXJuIEluZmluaXR5O1xyXG5cdFx0XHR9XHJcblx0XHRcdC8vIFF1ZXJ5IGFjcm9zcyBhbGwgcGFsZXR0ZXMgcHJlc2VudCBub3cgKHN0b3JlZCArIGFueSBuZXdseSBhZGRlZCBpbiBET00pLlxyXG5cdFx0XHRjb25zdCByb290cyAgICAgICAgICAgID0gdGhpcy5wYWxldHRlX3Vscz8ubGVuZ3RoID8gdGhpcy5wYWxldHRlX3VscyA6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3BhbmVsX2ZpZWxkX3R5cGVzX191bCcgKTtcclxuXHRcdFx0Y29uc3QgYWxsUGFsZXR0ZUZpZWxkcyA9IEFycmF5LmZyb20oIHJvb3RzICkuZmxhdE1hcCggciA9PiBBcnJheS5mcm9tKCByLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX2ZpZWxkJyApICkgKTtcclxuXHRcdFx0bGV0IGxpbWl0ICAgICAgICAgICAgICA9IEluZmluaXR5O1xyXG5cclxuXHRcdFx0YWxsUGFsZXR0ZUZpZWxkcy5mb3JFYWNoKCAoZWwpID0+IHtcclxuXHRcdFx0XHRpZiAoIGVsLmRhdGFzZXQuaWQgPT09IGtleSApIHtcclxuXHRcdFx0XHRcdGNvbnN0IG4gPSBDb3JlLldQQkNfQkZCX1VzYWdlTGltaXRTZXJ2aWNlLnBhcnNlX3VzYWdlX2xpbWl0KCBlbC5kYXRhc2V0LnVzYWdlbnVtYmVyICk7XHJcblx0XHRcdFx0XHQvLyBDaG9vc2UgdGhlIHNtYWxsZXN0IGZpbml0ZSBsaW1pdCAoc2FmZXN0IGlmIHBhbGV0dGVzIGRpc2FncmVlKS5cclxuXHRcdFx0XHRcdGlmICggbiA8IGxpbWl0ICkge1xyXG5cdFx0XHRcdFx0XHRsaW1pdCA9IG47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRyZXR1cm4gbGltaXQ7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRGlzYWJsZS9lbmFibGUgcGFsZXR0ZSBpdGVtcyBiYXNlZCBvbiBjdXJyZW50IHVzYWdlIGNvdW50cyBhbmQgbGltaXRzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHR1cGRhdGVfcGFsZXR0ZV91aSgpIHtcclxuXHRcdFx0Ly8gQWx3YXlzIGNvbXB1dGUgdXNhZ2UgZnJvbSB0aGUgY2FudmFzOlxyXG5cdFx0XHRjb25zdCB1c2FnZSA9IHRoaXMuY291bnRfdXNhZ2VfYnlfa2V5KCk7XHJcblxyXG5cdFx0XHQvLyBVcGRhdGUgYWxsIHBhbGV0dGVzIGN1cnJlbnRseSBpbiBET00gKG5vdCBqdXN0IHRoZSBpbml0aWFsbHkgY2FwdHVyZWQgb25lcylcclxuXHRcdFx0Y29uc3QgcGFsZXR0ZXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCAnLndwYmNfYmZiX19wYW5lbF9maWVsZF90eXBlc19fdWwnICk7XHJcblxyXG5cdFx0XHRwYWxldHRlcy5mb3JFYWNoKCAocGFsKSA9PiB7XHJcblx0XHRcdFx0cGFsLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX2ZpZWxkJyApLmZvckVhY2goIChwYW5lbF9maWVsZCkgPT4ge1xyXG5cdFx0XHRcdFx0Y29uc3QgcGFsZXR0ZUlkICAgPSBwYW5lbF9maWVsZC5kYXRhc2V0LmlkO1xyXG5cdFx0XHRcdFx0Y29uc3QgcmF3X2xpbWl0ICAgPSBwYW5lbF9maWVsZC5kYXRhc2V0LnVzYWdlbnVtYmVyO1xyXG5cdFx0XHRcdFx0Y29uc3QgcGVyRWxMaW1pdCAgPSBDb3JlLldQQkNfQkZCX1VzYWdlTGltaXRTZXJ2aWNlLnBhcnNlX3VzYWdlX2xpbWl0KCByYXdfbGltaXQgKTtcclxuXHRcdFx0XHRcdC8vIEVmZmVjdGl2ZSBsaW1pdCBhY3Jvc3MgYWxsIHBhbGV0dGVzIGlzIHRoZSBnbG9iYWwgbGltaXQgZm9yIHRoaXMga2V5LlxyXG5cdFx0XHRcdFx0Y29uc3QgZ2xvYmFsTGltaXQgPSB0aGlzLmdldF9saW1pdF9mb3Jfa2V5KCBwYWxldHRlSWQgKTtcclxuXHRcdFx0XHRcdGNvbnN0IGxpbWl0ICAgICAgID0gTnVtYmVyLmlzRmluaXRlKCBnbG9iYWxMaW1pdCApID8gZ2xvYmFsTGltaXQgOiBwZXJFbExpbWl0OyAvLyBwcmVmZXIgZ2xvYmFsIG1pblxyXG5cclxuXHRcdFx0XHRcdGNvbnN0IGN1cnJlbnQgPSB1c2FnZVtwYWxldHRlSWRdIHx8IDA7XHJcblx0XHRcdFx0XHRjb25zdCBkaXNhYmxlID0gTnVtYmVyLmlzRmluaXRlKCBsaW1pdCApICYmIGN1cnJlbnQgPj0gbGltaXQ7XHJcblxyXG5cdFx0XHRcdFx0cGFuZWxfZmllbGQuc3R5bGUucG9pbnRlckV2ZW50cyA9IGRpc2FibGUgPyAnbm9uZScgOiAnJztcclxuXHRcdFx0XHRcdHBhbmVsX2ZpZWxkLnN0eWxlLm9wYWNpdHkgICAgICAgPSBkaXNhYmxlID8gJzAuNCcgOiAnJztcclxuXHRcdFx0XHRcdHBhbmVsX2ZpZWxkLnNldEF0dHJpYnV0ZSggJ2FyaWEtZGlzYWJsZWQnLCBkaXNhYmxlID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cdFx0XHRcdFx0aWYgKCBkaXNhYmxlICkge1xyXG5cdFx0XHRcdFx0XHRwYW5lbF9maWVsZC5zZXRBdHRyaWJ1dGUoICd0YWJpbmRleCcsICctMScgKTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdHBhbmVsX2ZpZWxkLnJlbW92ZUF0dHJpYnV0ZSggJ3RhYmluZGV4JyApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFJldHVybiBob3cgbWFueSB2YWxpZCBpbnN0YW5jZXMgd2l0aCB0aGlzIHVzYWdlIGtleSBleGlzdCBpbiB0aGUgY2FudmFzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgLSBVc2FnZSBrZXkgb2YgYSBwYWxldHRlIGl0ZW0uXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyfSBDb3VudCBvZiBleGlzdGluZyBub24taW52YWxpZCBpbnN0YW5jZXMuXHJcblx0XHQgKi9cclxuXHRcdGNvdW50X2Zvcl9rZXkoIGtleSApIHtcclxuXHRcdFx0aWYgKCAhIGtleSApIHtcclxuXHRcdFx0XHRyZXR1cm4gMDtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gKCB0aGlzLnBhZ2VzX2NvbnRhaW5lcj8ucXVlcnlTZWxlY3RvckFsbChcclxuICAgICAgICAgICAgICAgIGAud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGRbZGF0YS11c2FnZV9rZXk9XCIke0NvcmUuV1BCQ19CRkJfU2FuaXRpemUuZXNjX2F0dHJfdmFsdWVfZm9yX3NlbGVjdG9yKCBrZXkgKX1cIl06bm90KC5pcy1pbnZhbGlkKSwgXHJcbiAgICAgICAgICAgICAgICAgLndwYmNfYmZiX19wYW5lbC0tcHJldmlldyAud3BiY19iZmJfX2ZpZWxkW2RhdGEtdHlwZT1cIiR7Q29yZS5XUEJDX0JGQl9TYW5pdGl6ZS5lc2NfYXR0cl92YWx1ZV9mb3Jfc2VsZWN0b3IoIGtleSApfVwiXTpub3QoLmlzLWludmFsaWQpYFxyXG5cdFx0XHQpIHx8IFtdICkubGVuZ3RoO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQWxpYXMgZm9yIGxpbWl0IGxvb2t1cCAocmVhZGFiaWxpdHkpLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgLSBVc2FnZSBrZXkgb2YgYSBwYWxldHRlIGl0ZW0uXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyfSBMaW1pdCB2YWx1ZSBvciBJbmZpbml0eS5cclxuXHRcdCAqL1xyXG5cdFx0bGltaXRfZm9yX2tleSgga2V5ICkge1xyXG5cdFx0XHRyZXR1cm4gdGhpcy5nZXRfbGltaXRfZm9yX2tleSgga2V5ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZW1haW5pbmcgc2xvdHMgZm9yIHRoaXMga2V5IChJbmZpbml0eSBpZiB1bmxpbWl0ZWQpLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgLSBVc2FnZSBrZXkgb2YgYSBwYWxldHRlIGl0ZW0uXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyfSBSZW1haW5pbmcgY291bnQgKD49IDApIG9yIEluZmluaXR5LlxyXG5cdFx0ICovXHJcblx0XHRyZW1haW5pbmdfZm9yX2tleSgga2V5ICkge1xyXG5cdFx0XHRjb25zdCBsaW1pdCA9IHRoaXMubGltaXRfZm9yX2tleSgga2V5ICk7XHJcblx0XHRcdGlmICggbGltaXQgPT09IEluZmluaXR5ICkge1xyXG5cdFx0XHRcdHJldHVybiBJbmZpbml0eTtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCB1c2VkID0gdGhpcy5jb3VudF9mb3Jfa2V5KCBrZXkgKTtcclxuXHRcdFx0cmV0dXJuIE1hdGgubWF4KCAwLCBsaW1pdCAtIHVzZWQgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFRydWUgaWYgeW91IGNhbiBhZGQgYGRlbHRhYCBtb3JlIGl0ZW1zIGZvciB0aGlzIGtleS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30ga2V5IC0gVXNhZ2Uga2V5IG9mIGEgcGFsZXR0ZSBpdGVtLlxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9IFtkZWx0YT0xXSAtIEhvdyBtYW55IGl0ZW1zIHlvdSBpbnRlbmQgdG8gYWRkLlxyXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59IFdoZXRoZXIgYWRkaW5nIGlzIGFsbG93ZWQuXHJcblx0XHQgKi9cclxuXHRcdGNhbl9hZGQoIGtleSwgZGVsdGEgPSAxICkge1xyXG5cdFx0XHRjb25zdCByZW0gPSB0aGlzLnJlbWFpbmluZ19mb3Jfa2V5KCBrZXkgKTtcclxuXHRcdFx0cmV0dXJuICggcmVtID09PSBJbmZpbml0eSApID8gdHJ1ZSA6ICggcmVtID49IGRlbHRhICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBVSS1mYWNpbmcgZ2F0ZTogYWxlcnQgd2hlbiBleGNlZWRlZC4gUmV0dXJucyBib29sZWFuIGFsbG93ZWQvYmxvY2tlZC5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30ga2V5IC0gVXNhZ2Uga2V5IG9mIGEgcGFsZXR0ZSBpdGVtLlxyXG5cdFx0ICogQHBhcmFtIHt7bGFiZWw/OiBzdHJpbmcsIGRlbHRhPzogbnVtYmVyfX0gW29wdHM9e31dIC0gT3B0aW9uYWwgVUkgaW5mby5cclxuXHRcdCAqIEByZXR1cm5zIHtib29sZWFufSBUcnVlIGlmIGFsbG93ZWQsIGZhbHNlIGlmIGJsb2NrZWQuXHJcblx0XHQgKi9cclxuXHRcdGdhdGVfb3JfYWxlcnQoIGtleSwgeyBsYWJlbCA9IGtleSwgZGVsdGEgPSAxIH0gPSB7fSApIHtcclxuXHRcdFx0aWYgKCB0aGlzLmNhbl9hZGQoIGtleSwgZGVsdGEgKSApIHtcclxuXHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCBsaW1pdCA9IHRoaXMubGltaXRfZm9yX2tleSgga2V5ICk7XHJcblx0XHRcdGFsZXJ0KCBgT25seSAke2xpbWl0fSBpbnN0YW5jZSR7bGltaXQgPiAxID8gJ3MnIDogJyd9IG9mIFwiJHtsYWJlbH1cIiBhbGxvd2VkLmAgKTtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQmFja3dhcmQtY29tcGF0aWJsZSBhbGlhcyB1c2VkIGVsc2V3aGVyZSBpbiB0aGUgY29kZWJhc2UuICAtIENoZWNrIHdoZXRoZXIgYW5vdGhlciBpbnN0YW5jZSB3aXRoIHRoZSBnaXZlbiB1c2FnZSBrZXkgY2FuIGJlIGFkZGVkLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgLSBVc2FnZSBrZXkgb2YgYSBwYWxldHRlIGl0ZW0uXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn0gV2hldGhlciBhZGRpbmcgb25lIG1vcmUgaXMgYWxsb3dlZC5cclxuXHRcdCAqL1xyXG5cdFx0aXNfdXNhZ2Vfb2soIGtleSApIHtcclxuXHRcdFx0cmV0dXJuIHRoaXMuY2FuX2FkZCgga2V5LCAxICk7XHJcblx0XHR9XHJcblxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIENvbnN0YW50IGV2ZW50IG5hbWVzIGZvciB0aGUgYnVpbGRlci5cclxuXHQgKi9cclxuXHRDb3JlLldQQkNfQkZCX0V2ZW50cyA9IE9iamVjdC5mcmVlemUoe1xyXG5cdFx0U0VMRUNUICAgICAgICAgICAgOiAnd3BiYzpiZmI6c2VsZWN0JyxcclxuXHRcdENMRUFSX1NFTEVDVElPTiAgIDogJ3dwYmM6YmZiOmNsZWFyLXNlbGVjdGlvbicsXHJcblx0XHRGSUVMRF9BREQgICAgICAgICA6ICd3cGJjOmJmYjpmaWVsZDphZGQnLFxyXG5cdFx0RklFTERfUkVNT1ZFICAgICAgOiAnd3BiYzpiZmI6ZmllbGQ6cmVtb3ZlJyxcclxuXHRcdFNUUlVDVFVSRV9DSEFOR0UgIDogJ3dwYmM6YmZiOnN0cnVjdHVyZTpjaGFuZ2UnLFxyXG5cdFx0U1RSVUNUVVJFX0xPQURFRCAgOiAnd3BiYzpiZmI6c3RydWN0dXJlOmxvYWRlZCdcclxuXHR9KTtcclxuXHJcblx0LyoqXHJcblx0ICogTGlnaHR3ZWlnaHQgZXZlbnQgYnVzIHRoYXQgZW1pdHMgdG8gYm90aCB0aGUgcGFnZXMgY29udGFpbmVyIGFuZCBkb2N1bWVudC5cclxuXHQgKi9cclxuXHRDb3JlLldQQkNfQkZCX0V2ZW50QnVzID0gIGNsYXNzIHtcclxuXHRcdC8qKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gc2NvcGVfZWwgLSBFbGVtZW50IHRvIGRpc3BhdGNoIGJ1YmJsZWQgZXZlbnRzIGZyb20uXHJcblx0XHQgKi9cclxuXHRcdGNvbnN0cnVjdG9yKCBzY29wZV9lbCApIHtcclxuXHRcdFx0dGhpcy5zY29wZV9lbCA9IHNjb3BlX2VsO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRW1pdCBhIERPTSBDdXN0b21FdmVudCB3aXRoIHBheWxvYWQuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHR5cGUgLSBFdmVudCB0eXBlICh1c2UgQ29yZS5XUEJDX0JGQl9FdmVudHMuIHdoZW4gcG9zc2libGUpLlxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IFtkZXRhaWw9e31dIC0gQXJiaXRyYXJ5IHNlcmlhbGl6YWJsZSBwYXlsb2FkLlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKi9cclxuXHRcdGVtaXQoIHR5cGUsIGRldGFpbCA9IHt9ICkge1xyXG5cdFx0XHRpZiAoICEgdGhpcy5zY29wZV9lbCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0dGhpcy5zY29wZV9lbC5kaXNwYXRjaEV2ZW50KCBuZXcgQ3VzdG9tRXZlbnQoIHR5cGUsIHsgZGV0YWlsOiB7IC4uLmRldGFpbCB9LCBidWJibGVzOiB0cnVlIH0gKSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU3Vic2NyaWJlIHRvIGFuIGV2ZW50IG9uIGRvY3VtZW50LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIC0gRXZlbnQgdHlwZS5cclxuXHRcdCAqIEBwYXJhbSB7KGV2OkN1c3RvbUV2ZW50KT0+dm9pZH0gaGFuZGxlciAtIEhhbmRsZXIgZnVuY3Rpb24uXHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqL1xyXG5cdFx0b24oIHR5cGUsIGhhbmRsZXIgKSB7XHJcblx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoIHR5cGUsIGhhbmRsZXIgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFVuc3Vic2NyaWJlIGZyb20gYW4gZXZlbnQgb24gZG9jdW1lbnQuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHR5cGUgLSBFdmVudCB0eXBlLlxyXG5cdFx0ICogQHBhcmFtIHsoZXY6Q3VzdG9tRXZlbnQpPT52b2lkfSBoYW5kbGVyIC0gSGFuZGxlciBmdW5jdGlvbi5cclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHRvZmYoIHR5cGUsIGhhbmRsZXIgKSB7XHJcblx0XHRcdGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoIHR5cGUsIGhhbmRsZXIgKTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBTb3J0YWJsZUpTIG1hbmFnZXI6IHNpbmdsZSBwb2ludCBmb3IgY29uc2lzdGVudCBEbkQgY29uZmlnLlxyXG5cdCAqL1xyXG5cdENvcmUuV1BCQ19CRkJfU29ydGFibGVNYW5hZ2VyID0gY2xhc3MgIHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEBwYXJhbSB7V1BCQ19Gb3JtX0J1aWxkZXJ9IGJ1aWxkZXIgLSBUaGUgYWN0aXZlIGJ1aWxkZXIgaW5zdGFuY2UuXHJcblx0XHQgKiBAcGFyYW0ge3sgZ3JvdXBOYW1lPzogc3RyaW5nLCBhbmltYXRpb24/OiBudW1iZXIsIGdob3N0Q2xhc3M/OiBzdHJpbmcsIGNob3NlbkNsYXNzPzogc3RyaW5nLCBkcmFnQ2xhc3M/OiBzdHJpbmcgfX0gW29wdHM9e31dIC0gVmlzdWFsL2JlaGF2aW9yIG9wdGlvbnMuXHJcblx0XHQgKi9cclxuXHRcdGNvbnN0cnVjdG9yKCBidWlsZGVyLCBvcHRzID0ge30gKSB7XHJcblx0XHRcdHRoaXMuYnVpbGRlciA9IGJ1aWxkZXI7XHJcblx0XHRcdGNvbnN0IGdpZCA9IHRoaXMuYnVpbGRlcj8uaW5zdGFuY2VfaWQgfHwgTWF0aC5yYW5kb20oKS50b1N0cmluZyggMzYgKS5zbGljZSggMiwgOCApO1xyXG5cdFx0XHR0aGlzLm9wdHMgPSB7XHJcblx0XHRcdFx0Ly8gZ3JvdXBOYW1lICA6ICdmb3JtJyxcclxuXHRcdFx0XHRncm91cE5hbWU6IGBmb3JtLSR7Z2lkfWAsXHJcblx0XHRcdFx0YW5pbWF0aW9uICA6IDE1MCxcclxuXHRcdFx0XHRnaG9zdENsYXNzIDogJ3dwYmNfYmZiX19kcmFnLWdob3N0JyxcclxuXHRcdFx0XHRjaG9zZW5DbGFzczogJ3dwYmNfYmZiX19oaWdobGlnaHQnLFxyXG5cdFx0XHRcdGRyYWdDbGFzcyAgOiAnd3BiY19iZmJfX2RyYWctYWN0aXZlJyxcclxuXHRcdFx0XHQuLi5vcHRzXHJcblx0XHRcdH07XHJcblx0XHRcdC8qKiBAdHlwZSB7U2V0PEhUTUxFbGVtZW50Pn0gKi9cclxuXHRcdFx0dGhpcy5fY29udGFpbmVycyA9IG5ldyBTZXQoKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFRhZyB0aGUgZHJhZyBtaXJyb3IgKGVsZW1lbnQgdW5kZXIgY3Vyc29yKSB3aXRoIHJvbGU6ICdwYWxldHRlJyB8ICdjYW52YXMnLlxyXG5cdFx0ICogV29ya3Mgd2l0aCBTb3J0YWJsZSdzIGZhbGxiYWNrIG1pcnJvciAoLnNvcnRhYmxlLWZhbGxiYWNrIC8gLnNvcnRhYmxlLWRyYWcpIGFuZCB3aXRoIHlvdXIgZHJhZ0NsYXNzICgud3BiY19iZmJfX2RyYWctYWN0aXZlKS5cclxuXHRcdCAqL1xyXG5cdFx0X3RhZ19kcmFnX21pcnJvciggZXZ0ICkge1xyXG5cdFx0XHRjb25zdCBmcm9tUGFsZXR0ZSA9IHRoaXMuYnVpbGRlcj8ucGFsZXR0ZV91bHM/LmluY2x1ZGVzPy4oIGV2dC5mcm9tICk7XHJcblx0XHRcdGNvbnN0IHJvbGUgICAgICAgID0gZnJvbVBhbGV0dGUgPyAncGFsZXR0ZScgOiAnY2FudmFzJztcclxuXHRcdFx0Ly8gV2FpdCBhIHRpY2sgc28gdGhlIG1pcnJvciBleGlzdHMuICAtIFRoZSB3aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lKCkgbWV0aG9kIHRlbGxzIHRoZSBicm93c2VyIHlvdSB3aXNoIHRvIHBlcmZvcm0gYW4gYW5pbWF0aW9uLlxyXG5cdFx0XHRyZXF1ZXN0QW5pbWF0aW9uRnJhbWUoICgpID0+IHtcclxuXHRcdFx0XHRjb25zdCBtaXJyb3IgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCAnLnNvcnRhYmxlLWZhbGxiYWNrLCAuc29ydGFibGUtZHJhZywgLicgKyB0aGlzLm9wdHMuZHJhZ0NsYXNzICk7XHJcblx0XHRcdFx0aWYgKCBtaXJyb3IgKSB7XHJcblx0XHRcdFx0XHRtaXJyb3Iuc2V0QXR0cmlidXRlKCAnZGF0YS1kcmFnLXJvbGUnLCByb2xlICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0X3RvZ2dsZV9kbmRfcm9vdF9mbGFncyggYWN0aXZlLCBmcm9tX3BhbGV0dGUgPSBmYWxzZSApIHtcclxuXHJcblx0XHRcdC8vIHNldCB0byByb290IGVsZW1lbnQgb2YgYW4gSFRNTCBkb2N1bWVudCwgd2hpY2ggaXMgdGhlIDxodG1sPi5cclxuXHRcdFx0Y29uc3Qgcm9vdCA9IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcclxuXHRcdFx0aWYgKCBhY3RpdmUgKSB7XHJcblx0XHRcdFx0cm9vdC5jbGFzc0xpc3QuYWRkKCAnd3BiY19iZmJfX2RuZC1hY3RpdmUnICk7XHJcblx0XHRcdFx0aWYgKCBmcm9tX3BhbGV0dGUgKSB7XHJcblx0XHRcdFx0XHRyb290LmNsYXNzTGlzdC5hZGQoICd3cGJjX2JmYl9fZHJhZy1mcm9tLXBhbGV0dGUnICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHJvb3QuY2xhc3NMaXN0LnJlbW92ZSggJ3dwYmNfYmZiX19kbmQtYWN0aXZlJywgJ3dwYmNfYmZiX19kcmFnLWZyb20tcGFsZXR0ZScgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVuc3VyZSBTb3J0YWJsZSBpcyBhdHRhY2hlZCB0byBhIGNvbnRhaW5lciB3aXRoIHJvbGUgJ3BhbGV0dGUnIG9yICdjYW52YXMnLlxyXG5cdFx0ICpcclxuXHRcdCAqICAtLSBIYW5kbGUgc2VsZWN0b3JzOiBoYW5kbGU6ICAnLnNlY3Rpb24tZHJhZy1oYW5kbGUsIC53cGJjX2JmYl9fZHJhZy1oYW5kbGUsIC53cGJjX2JmYl9fZHJhZy1hbnl3aGVyZSwgW2RhdGEtZHJhZ2dhYmxlPVwidHJ1ZVwiXSdcclxuXHRcdCAqICAtLSBEcmFnZ2FibGUgZ2F0ZTogZHJhZ2dhYmxlOiAnLndwYmNfYmZiX19maWVsZDpub3QoW2RhdGEtZHJhZ2dhYmxlPVwiZmFsc2VcIl0pLCAud3BiY19iZmJfX3NlY3Rpb24nXHJcblx0XHQgKiAgLS0gRmlsdGVyIChvdmVybGF5LXNhZmUpOiAgICAgaWdub3JlIGV2ZXJ5dGhpbmcgaW4gb3ZlcmxheSBleGNlcHQgdGhlIGhhbmRsZSAtICAnLndwYmNfYmZiX19vdmVybGF5LWNvbnRyb2xzICo6bm90KC53cGJjX2JmYl9fZHJhZy1oYW5kbGUpOm5vdCguc2VjdGlvbi1kcmFnLWhhbmRsZSk6bm90KC53cGJjX2ljbl9kcmFnX2luZGljYXRvciknXHJcblx0XHQgKiAgLS0gTm8tZHJhZyB3cmFwcGVyOiAgICAgICAgICAgdXNlIC53cGJjX2JmYl9fbm8tZHJhZy16b25lIGluc2lkZSByZW5kZXJlcnMgZm9yIGlucHV0cy93aWRnZXRzLlxyXG5cdFx0ICogIC0tIEZvY3VzIGd1YXJkIChvcHRpb25hbCk6ICAgIGZsaXAgW2RhdGEtZHJhZ2dhYmxlXSBvbiBmb2N1c2luL2ZvY3Vzb3V0IHRvIHByZXZlbnQgYWNjaWRlbnRhbCBkcmFncyB3aGlsZSB0eXBpbmcuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gY29udGFpbmVyIC0gVGhlIGVsZW1lbnQgdG8gZW5oYW5jZSB3aXRoIFNvcnRhYmxlLlxyXG5cdFx0ICogQHBhcmFtIHsncGFsZXR0ZSd8J2NhbnZhcyd9IHJvbGUgLSBCZWhhdmlvciBwcm9maWxlIHRvIGFwcGx5LlxyXG5cdFx0ICogQHBhcmFtIHt7IG9uQWRkPzogRnVuY3Rpb24gfX0gW2hhbmRsZXJzPXt9XSAtIE9wdGlvbmFsIGhhbmRsZXJzLlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKi9cclxuXHRcdGVuc3VyZSggY29udGFpbmVyLCByb2xlLCBoYW5kbGVycyA9IHt9ICkge1xyXG5cdFx0XHRpZiAoICEgY29udGFpbmVyIHx8IHR5cGVvZiBTb3J0YWJsZSA9PT0gJ3VuZGVmaW5lZCcgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggU29ydGFibGUuZ2V0Py4oIGNvbnRhaW5lciApICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgY29tbW9uID0ge1xyXG5cdFx0XHRcdGFuaW1hdGlvbiAgOiB0aGlzLm9wdHMuYW5pbWF0aW9uLFxyXG5cdFx0XHRcdGdob3N0Q2xhc3MgOiB0aGlzLm9wdHMuZ2hvc3RDbGFzcyxcclxuXHRcdFx0XHRjaG9zZW5DbGFzczogdGhpcy5vcHRzLmNob3NlbkNsYXNzLFxyXG5cdFx0XHRcdGRyYWdDbGFzcyAgOiB0aGlzLm9wdHMuZHJhZ0NsYXNzLFxyXG5cdFx0XHRcdC8vID09IEVsZW1lbnQgdW5kZXIgdGhlIGN1cnNvciAgPT0gRW5zdXJlIHdlIGRyYWcgYSByZWFsIERPTSBtaXJyb3IgeW91IGNhbiBzdHlsZSB2aWEgQ1NTIChjcm9zcy1icm93c2VyKS5cclxuXHRcdFx0XHRmb3JjZUZhbGxiYWNrICAgIDogdHJ1ZSxcclxuXHRcdFx0XHRmYWxsYmFja09uQm9keSAgIDogdHJ1ZSxcclxuXHRcdFx0XHRmYWxsYmFja1RvbGVyYW5jZTogNixcclxuXHRcdFx0XHQvLyBBZGQgYm9keS9odG1sIGZsYWdzIHNvIHlvdSBjYW4gc3R5bGUgZGlmZmVyZW50bHkgd2hlbiBkcmFnZ2luZyBmcm9tIHBhbGV0dGUuXHJcblx0XHRcdFx0b25TdGFydDogKGV2dCkgPT4ge1xyXG5cdFx0XHRcdFx0dGhpcy5idWlsZGVyPy5fYWRkX2RyYWdnaW5nX2NsYXNzPy4oKTtcclxuXHJcblx0XHRcdFx0XHRjb25zdCBmcm9tUGFsZXR0ZSA9IHRoaXMuYnVpbGRlcj8ucGFsZXR0ZV91bHM/LmluY2x1ZGVzPy4oIGV2dC5mcm9tICk7XHJcblx0XHRcdFx0XHR0aGlzLl90b2dnbGVfZG5kX3Jvb3RfZmxhZ3MoIHRydWUsIGZyb21QYWxldHRlICk7ICAvLyBzZXQgdG8gcm9vdCBIVE1MIGRvY3VtZW50OiBodG1sLndwYmNfYmZiX19kbmQtYWN0aXZlLndwYmNfYmZiX19kcmFnLWZyb20tcGFsZXR0ZSAuXHJcblxyXG5cdFx0XHRcdFx0dGhpcy5fdGFnX2RyYWdfbWlycm9yKCBldnQgKTsgICAgICAgICAgICAgICAgICAgICAgLy8gQWRkICdkYXRhLWRyYWctcm9sZScgYXR0cmlidXRlIHRvICBlbGVtZW50IHVuZGVyIGN1cnNvci5cclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdG9uRW5kICA6ICgpID0+IHtcclxuXHRcdFx0XHRcdHNldFRpbWVvdXQoICgpID0+IHsgdGhpcy5idWlsZGVyLl9yZW1vdmVfZHJhZ2dpbmdfY2xhc3MoKTsgfSwgNTAgKTtcclxuXHRcdFx0XHRcdHRoaXMuX3RvZ2dsZV9kbmRfcm9vdF9mbGFncyggZmFsc2UgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHRpZiAoIHJvbGUgPT09ICdwYWxldHRlJyApIHtcclxuXHRcdFx0XHRTb3J0YWJsZS5jcmVhdGUoIGNvbnRhaW5lciwge1xyXG5cdFx0XHRcdFx0Li4uY29tbW9uLFxyXG5cdFx0XHRcdFx0Z3JvdXAgICA6IHsgbmFtZTogdGhpcy5vcHRzLmdyb3VwTmFtZSwgcHVsbDogJ2Nsb25lJywgcHV0OiBmYWxzZSB9LFxyXG5cdFx0XHRcdFx0c29ydCAgICA6IGZhbHNlXHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdHRoaXMuX2NvbnRhaW5lcnMuYWRkKCBjb250YWluZXIgKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIHJvbGUgPT09ICdjYW52YXMnLlxyXG5cdFx0XHRTb3J0YWJsZS5jcmVhdGUoIGNvbnRhaW5lciwge1xyXG5cdFx0XHRcdC4uLmNvbW1vbixcclxuXHRcdFx0XHRncm91cCAgICA6IHtcclxuXHRcdFx0XHRcdG5hbWU6IHRoaXMub3B0cy5ncm91cE5hbWUsXHJcblx0XHRcdFx0XHRwdWxsOiB0cnVlLFxyXG5cdFx0XHRcdFx0cHV0IDogKHRvLCBmcm9tLCBkcmFnZ2VkRWwpID0+IHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIGRyYWdnZWRFbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fZmllbGQnICkgfHxcclxuXHRcdFx0XHRcdFx0XHQgICBkcmFnZ2VkRWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tIERuRCBIYW5kbGVycyAtLS0tLS0tLS0tLS0tLSAgICAgICAgICAgICAgICAvLyBHcmFiIGFueXdoZXJlIG9uIGZpZWxkcyB0aGF0IG9wdC1pbiB3aXRoIHRoZSBjbGFzcyBvciBhdHRyaWJ1dGUuICAtIFNlY3Rpb25zIHN0aWxsIHJlcXVpcmUgdGhlaXIgZGVkaWNhdGVkIGhhbmRsZS5cclxuXHRcdFx0XHRoYW5kbGUgICA6ICcuc2VjdGlvbi1kcmFnLWhhbmRsZSwgLndwYmNfYmZiX19kcmFnLWhhbmRsZSwgLndwYmNfYmZiX19kcmFnLWFueXdoZXJlLCBbZGF0YS1kcmFnZ2FibGU9XCJ0cnVlXCJdJyxcclxuXHRcdFx0XHRkcmFnZ2FibGU6ICcud3BiY19iZmJfX2ZpZWxkOm5vdChbZGF0YS1kcmFnZ2FibGU9XCJmYWxzZVwiXSksIC53cGJjX2JmYl9fc2VjdGlvbicsICAgICAgICAgICAgICAgICAgICAgICAgLy8gUGVyLWZpZWxkIG9wdC1vdXQgd2l0aCBbZGF0YS1kcmFnZ2FibGU9XCJmYWxzZVwiXSAoZS5nLiwgd2hpbGUgZWRpdGluZykuXHJcblx0XHRcdFx0Ly8gLS0tLS0tLS0tLSBGaWx0ZXJzIC0gTm8gRG5EIC0tLS0tLS0tLS0gICAgICAgICAgICAgICAgLy8gRGVjbGFyYXRpdmUg4oCcbm8tZHJhZyB6b25lc+KAnTogYW55dGhpbmcgaW5zaWRlIHRoZXNlIHdyYXBwZXJzIHdvbuKAmXQgc3RhcnQgYSBkcmFnLlxyXG5cdFx0XHRcdGZpbHRlcjogW1xyXG5cdFx0XHRcdFx0Jy53cGJjX2JmYl9fbm8tZHJhZy16b25lJyxcclxuXHRcdFx0XHRcdCcud3BiY19iZmJfX25vLWRyYWctem9uZSAqJyxcclxuXHRcdFx0XHRcdCcud3BiY19iZmJfX2NvbHVtbi1yZXNpemVyJywgIC8vIElnbm9yZSB0aGUgcmVzaXplciByYWlscyBkdXJpbmcgRG5EIChwcmV2ZW50cyBlZGdlIOKAnHNuYXDigJ0pLlxyXG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gSW4gdGhlIG92ZXJsYXkgdG9vbGJhciwgYmxvY2sgZXZlcnl0aGluZyBFWENFUFQgdGhlIGRyYWcgaGFuZGxlIChhbmQgaXRzIGljb24pLlxyXG5cdFx0XHRcdFx0Jy53cGJjX2JmYl9fb3ZlcmxheS1jb250cm9scyAqOm5vdCgud3BiY19iZmJfX2RyYWctaGFuZGxlKTpub3QoLnNlY3Rpb24tZHJhZy1oYW5kbGUpOm5vdCgud3BiY19pY25fZHJhZ19pbmRpY2F0b3IpJ1xyXG5cdFx0XHRcdF0uam9pbiggJywnICksXHJcblx0XHRcdFx0cHJldmVudE9uRmlsdGVyICA6IGZhbHNlLFxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLSBhbnRpLWppdHRlciB0dW5pbmcgLS0tLS0tLS0tLVxyXG5cdFx0XHRcdGRpcmVjdGlvbiAgICAgICAgICAgIDogJ3ZlcnRpY2FsJywgICAgICAgICAgIC8vIGNvbHVtbnMgYXJlIHZlcnRpY2FsIGxpc3RzLlxyXG5cdFx0XHRcdGludmVydFN3YXAgICAgICAgICAgIDogdHJ1ZSwgICAgICAgICAgICAgICAgIC8vIHVzZSBzd2FwIG9uIGludmVydGVkIG92ZXJsYXAuXHJcblx0XHRcdFx0c3dhcFRocmVzaG9sZCAgICAgICAgOiAwLjY1LCAgICAgICAgICAgICAgICAgLy8gYmUgbGVzcyBlYWdlciB0byBzd2FwLlxyXG5cdFx0XHRcdGludmVydGVkU3dhcFRocmVzaG9sZDogMC44NSwgICAgICAgICAgICAgICAgIC8vIHJlcXVpcmUgZGVlcGVyIG92ZXJsYXAgd2hlbiBpbnZlcnRlZC5cclxuXHRcdFx0XHRlbXB0eUluc2VydFRocmVzaG9sZCA6IDI0LCAgICAgICAgICAgICAgICAgICAvLyBkb27igJl0IGp1bXAgaW50byBlbXB0eSBjb250YWluZXJzIHRvbyBlYXJseS5cclxuXHRcdFx0XHRkcmFnb3ZlckJ1YmJsZSAgICAgICA6IGZhbHNlLCAgICAgICAgICAgICAgICAvLyBrZWVwIGRyYWdvdmVyIGxvY2FsLlxyXG5cdFx0XHRcdGZhbGxiYWNrT25Cb2R5ICAgICAgIDogdHJ1ZSwgICAgICAgICAgICAgICAgIC8vIG1vcmUgc3RhYmxlIHBvc2l0aW9uaW5nLlxyXG5cdFx0XHRcdGZhbGxiYWNrVG9sZXJhbmNlICAgIDogNiwgICAgICAgICAgICAgICAgICAgIC8vIFJlZHVjZSBtaWNyby1tb3ZlcyB3aGVuIHRoZSBtb3VzZSBzaGFrZXMgYSBiaXQgKGVzcC4gb24gdG91Y2hwYWRzKS5cclxuXHRcdFx0XHRzY3JvbGwgICAgICAgICAgICAgICA6IHRydWUsXHJcblx0XHRcdFx0c2Nyb2xsU2Vuc2l0aXZpdHkgICAgOiA0MCxcclxuXHRcdFx0XHRzY3JvbGxTcGVlZCAgICAgICAgICA6IDEwLFxyXG5cdFx0XHRcdC8qKlxyXG5cdFx0XHRcdCAqIEVudGVyL2xlYXZlIGh5c3RlcmVzaXMgZm9yIGNyb3NzLWNvbHVtbiBtb3Zlcy4gICAgT25seSBhbGxvdyBkcm9wcGluZyBpbnRvIGB0b2Agd2hlbiB0aGUgcG9pbnRlciBpcyB3ZWxsIGluc2lkZSBpdC5cclxuXHRcdFx0XHQgKi9cclxuXHRcdFx0XHRvbk1vdmU6IChldnQsIG9yaWdpbmFsRXZlbnQpID0+IHtcclxuXHRcdFx0XHRcdGNvbnN0IHsgdG8sIGZyb20gfSA9IGV2dDtcclxuXHRcdFx0XHRcdGlmICggIXRvIHx8ICFmcm9tICkgcmV0dXJuIHRydWU7XHJcblxyXG5cdFx0XHRcdFx0Ly8gT25seSBnYXRlIGNvbHVtbnMgKG5vdCBwYWdlIGNvbnRhaW5lcnMpLCBhbmQgb25seSBmb3IgY3Jvc3MtY29sdW1uIG1vdmVzIGluIHRoZSBzYW1lIHJvd1xyXG5cdFx0XHRcdFx0Y29uc3QgaXNDb2x1bW4gPSB0by5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX2NvbHVtbicgKTtcclxuXHRcdFx0XHRcdGlmICggIWlzQ29sdW1uICkgcmV0dXJuIHRydWU7XHJcblxyXG5cdFx0XHRcdFx0Y29uc3QgZnJvbVJvdyA9IGZyb20uY2xvc2VzdCggJy53cGJjX2JmYl9fcm93JyApO1xyXG5cdFx0XHRcdFx0Y29uc3QgdG9Sb3cgICA9IHRvLmNsb3Nlc3QoICcud3BiY19iZmJfX3JvdycgKTtcclxuXHRcdFx0XHRcdGlmICggZnJvbVJvdyAmJiB0b1JvdyAmJiBmcm9tUm93ICE9PSB0b1JvdyApIHJldHVybiB0cnVlO1xyXG5cclxuXHRcdFx0XHRcdGNvbnN0IHJlY3QgPSB0by5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcclxuXHRcdFx0XHRcdGNvbnN0IGV2dFggPSAob3JpZ2luYWxFdmVudC50b3VjaGVzPy5bMF0/LmNsaWVudFgpID8/IG9yaWdpbmFsRXZlbnQuY2xpZW50WDtcclxuXHRcdFx0XHRcdGNvbnN0IGV2dFkgPSAob3JpZ2luYWxFdmVudC50b3VjaGVzPy5bMF0/LmNsaWVudFkpID8/IG9yaWdpbmFsRXZlbnQuY2xpZW50WTtcclxuXHJcblx0XHRcdFx0XHQvLyAtLS0gRWRnZSBmZW5jZSAobGlrZSB5b3UgaGFkKSwgYnV0IGNsYW1wZWQgZm9yIHRpbnkgY29sdW1uc1xyXG5cdFx0XHRcdFx0Y29uc3QgcGFkZGluZ1ggPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmNsYW1wKCByZWN0LndpZHRoICogMC4yMCwgMTIsIDM2ICk7XHJcblx0XHRcdFx0XHRjb25zdCBwYWRkaW5nWSA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuY2xhbXAoIHJlY3QuaGVpZ2h0ICogMC4xMCwgNiwgMTYgKTtcclxuXHJcblx0XHRcdFx0XHQvLyBMb29zZXIgWSBpZiB0aGUgY29sdW1uIGlzIHZpc3VhbGx5IHRpbnkvZW1wdHlcclxuXHRcdFx0XHRcdGNvbnN0IGlzVmlzdWFsbHlFbXB0eSA9IHRvLmNoaWxkRWxlbWVudENvdW50ID09PSAwIHx8IHJlY3QuaGVpZ2h0IDwgNjQ7XHJcblx0XHRcdFx0XHRjb25zdCBpbm5lclRvcCAgICAgICAgPSByZWN0LnRvcCArIChpc1Zpc3VhbGx5RW1wdHkgPyA0IDogcGFkZGluZ1kpO1xyXG5cdFx0XHRcdFx0Y29uc3QgaW5uZXJCb3R0b20gICAgID0gcmVjdC5ib3R0b20gLSAoaXNWaXN1YWxseUVtcHR5ID8gNCA6IHBhZGRpbmdZKTtcclxuXHRcdFx0XHRcdGNvbnN0IGlubmVyTGVmdCAgICAgICA9IHJlY3QubGVmdCArIHBhZGRpbmdYO1xyXG5cdFx0XHRcdFx0Y29uc3QgaW5uZXJSaWdodCAgICAgID0gcmVjdC5yaWdodCAtIHBhZGRpbmdYO1xyXG5cclxuXHRcdFx0XHRcdGNvbnN0IGluc2lkZVggPSBldnRYID4gaW5uZXJMZWZ0ICYmIGV2dFggPCBpbm5lclJpZ2h0O1xyXG5cdFx0XHRcdFx0Y29uc3QgaW5zaWRlWSA9IGV2dFkgPiBpbm5lclRvcCAmJiBldnRZIDwgaW5uZXJCb3R0b207XHJcblx0XHRcdFx0XHRpZiAoICEoaW5zaWRlWCAmJiBpbnNpZGVZKSApIHJldHVybiBmYWxzZTsgICAvLyBzdGF5IGluIGN1cnJlbnQgY29sdW1uIHVudGlsIHdlbGwgaW5zaWRlIG5ldyBvbmVcclxuXHJcblx0XHRcdFx0XHQvLyAtLS0gU3RpY2t5IHRhcmdldCBjb21taXQgZGlzdGFuY2U6IG9ubHkgc3dpdGNoIGlmIHdl4oCZcmUgY2xlYXJseSBpbnNpZGUgdGhlIG5ldyBjb2x1bW5cclxuXHRcdFx0XHRcdGNvbnN0IGRzID0gdGhpcy5fZHJhZ1N0YXRlO1xyXG5cdFx0XHRcdFx0aWYgKCBkcyApIHtcclxuXHRcdFx0XHRcdFx0aWYgKCBkcy5zdGlja3lUbyAmJiBkcy5zdGlja3lUbyAhPT0gdG8gKSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gcmVxdWlyZSBhIGRlZXBlciBwZW5ldHJhdGlvbiB0byBzd2l0Y2ggY29sdW1uc1xyXG5cdFx0XHRcdFx0XHRcdGNvbnN0IGNvbW1pdFggPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmNsYW1wKCByZWN0LndpZHRoICogMC4yNSwgMTgsIDQwICk7ICAgLy8gMjUlIG9yIDE44oCTNDBweFxyXG5cdFx0XHRcdFx0XHRcdGNvbnN0IGNvbW1pdFkgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmNsYW1wKCByZWN0LmhlaWdodCAqIDAuMTUsIDEwLCAyOCApOyAgLy8gMTUlIG9yIDEw4oCTMjhweFxyXG5cclxuXHRcdFx0XHRcdFx0XHRjb25zdCBkZWVwSW5zaWRlID1cclxuXHRcdFx0XHRcdFx0XHRcdFx0ICAoZXZ0WCA+IHJlY3QubGVmdCArIGNvbW1pdFggJiYgZXZ0WCA8IHJlY3QucmlnaHQgLSBjb21taXRYKSAmJlxyXG5cdFx0XHRcdFx0XHRcdFx0XHQgIChldnRZID4gcmVjdC50b3AgKyBjb21taXRZICYmIGV2dFkgPCByZWN0LmJvdHRvbSAtIGNvbW1pdFkpO1xyXG5cclxuXHRcdFx0XHRcdFx0XHRpZiAoICFkZWVwSW5zaWRlICkgcmV0dXJuIGZhbHNlO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdC8vIFdlIGFjY2VwdCB0aGUgbmV3IHRhcmdldCBub3cuXHJcblx0XHRcdFx0XHRcdGRzLnN0aWNreVRvICAgICA9IHRvO1xyXG5cdFx0XHRcdFx0XHRkcy5sYXN0U3dpdGNoVHMgPSBwZXJmb3JtYW5jZS5ub3coKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdG9uU3RhcnQ6IChldnQpID0+IHtcclxuXHRcdFx0XHRcdHRoaXMuYnVpbGRlcj8uX2FkZF9kcmFnZ2luZ19jbGFzcz8uKCk7XHJcblx0XHRcdFx0XHQvLyBNYXRjaCB0aGUgZmxhZ3Mgd2Ugc2V0IGluIGNvbW1vbiBzbyBDU1Mgc3RheXMgY29uc2lzdGVudCBvbiBjYW52YXMgZHJhZ3MgdG9vLlxyXG5cdFx0XHRcdFx0Y29uc3QgZnJvbVBhbGV0dGUgPSB0aGlzLmJ1aWxkZXI/LnBhbGV0dGVfdWxzPy5pbmNsdWRlcz8uKCBldnQuZnJvbSApO1xyXG5cdFx0XHRcdFx0dGhpcy5fdG9nZ2xlX2RuZF9yb290X2ZsYWdzKCB0cnVlLCBmcm9tUGFsZXR0ZSApOyAgICAgICAgICAvLyBzZXQgdG8gcm9vdCBIVE1MIGRvY3VtZW50OiBodG1sLndwYmNfYmZiX19kbmQtYWN0aXZlLndwYmNfYmZiX19kcmFnLWZyb20tcGFsZXR0ZSAuXHJcblx0XHRcdFx0XHR0aGlzLl90YWdfZHJhZ19taXJyb3IoIGV2dCApOyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gVGFnIHRoZSBtaXJyb3IgdW5kZXIgY3Vyc29yLlxyXG5cdFx0XHRcdFx0dGhpcy5fZHJhZ1N0YXRlID0geyBzdGlja3lUbzogbnVsbCwgbGFzdFN3aXRjaFRzOiAwIH07ICAgIC8vIHBlci1kcmFnIHN0YXRlLlxyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0b25FbmQgIDogKCkgPT4ge1xyXG5cdFx0XHRcdFx0c2V0VGltZW91dCggKCkgPT4geyB0aGlzLmJ1aWxkZXIuX3JlbW92ZV9kcmFnZ2luZ19jbGFzcygpOyB9LCA1MCApO1xyXG5cdFx0XHRcdFx0dGhpcy5fdG9nZ2xlX2RuZF9yb290X2ZsYWdzKCBmYWxzZSApOyAgICAgICAgICAgICAgICAgICAgLy8gc2V0IHRvIHJvb3QgSFRNTCBkb2N1bWVudCB3aXRob3V0IHRoZXNlIGNsYXNzZXM6IGh0bWwud3BiY19iZmJfX2RuZC1hY3RpdmUud3BiY19iZmJfX2RyYWctZnJvbS1wYWxldHRlIC5cclxuXHRcdFx0XHRcdHRoaXMuX2RyYWdTdGF0ZSA9IG51bGw7XHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdFx0Ly8gb25BZGQ6IGhhbmRsZXJzLm9uQWRkIHx8IHRoaXMuYnVpbGRlci5oYW5kbGVfb25fYWRkLmJpbmQoIHRoaXMuYnVpbGRlciApXHJcblx0XHRcdFx0b25BZGQ6IChldnQpID0+IHtcclxuXHRcdFx0XHRcdGlmICggdGhpcy5fb25fYWRkX3NlY3Rpb24oIGV2dCApICkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHQvLyBGYWxsYmFjazogb3JpZ2luYWwgaGFuZGxlciBmb3Igbm9ybWFsIGZpZWxkcy5cclxuXHRcdFx0XHRcdChoYW5kbGVycy5vbkFkZCB8fCB0aGlzLmJ1aWxkZXIuaGFuZGxlX29uX2FkZC5iaW5kKCB0aGlzLmJ1aWxkZXIgKSkoIGV2dCApO1xyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0b25VcGRhdGU6ICgpID0+IHtcclxuXHRcdFx0XHRcdHRoaXMuYnVpbGRlci5idXM/LmVtaXQ/LiggQ29yZS5XUEJDX0JGQl9FdmVudHMuU1RSVUNUVVJFX0NIQU5HRSwgeyByZWFzb246ICdzb3J0LXVwZGF0ZScgfSApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0dGhpcy5fY29udGFpbmVycy5hZGQoIGNvbnRhaW5lciApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSGFuZGxlIGFkZGluZy9tb3Zpbmcgc2VjdGlvbnMgdmlhIFNvcnRhYmxlIG9uQWRkLlxyXG5cdFx0ICogUmV0dXJucyB0cnVlIGlmIGhhbmRsZWQgKGkuZS4sIGl0IHdhcyBhIHNlY3Rpb24pLCBmYWxzZSB0byBsZXQgdGhlIGRlZmF1bHQgZmllbGQgaGFuZGxlciBydW4uXHJcblx0XHQgKlxyXG5cdFx0ICogLSBQYWxldHRlIC0+IGNhbnZhczogcmVtb3ZlIHRoZSBwbGFjZWhvbGRlciBjbG9uZSBhbmQgYnVpbGQgYSBmcmVzaCBzZWN0aW9uIHZpYSBhZGRfc2VjdGlvbigpXHJcblx0XHQgKiAtIENhbnZhcyAtPiBjYW52YXM6IGtlZXAgdGhlIG1vdmVkIERPTSAoYW5kIGl0cyBjaGlsZHJlbiksIGp1c3QgcmUtd2lyZSBvdmVybGF5cy9zb3J0YWJsZXMvbWV0YWRhdGFcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge1NvcnRhYmxlLlNvcnRhYmxlRXZlbnR9IGV2dFxyXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0XHQgKi9cclxuXHRcdF9vbl9hZGRfc2VjdGlvbihldnQpIHtcclxuXHJcblx0XHRcdGNvbnN0IGl0ZW0gPSBldnQuaXRlbTtcclxuXHRcdFx0aWYgKCAhIGl0ZW0gKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBJZGVudGlmeSBzZWN0aW9ucyBib3RoIGZyb20gcGFsZXR0ZSBpdGVtcyAobGkgY2xvbmVzKSBhbmQgcmVhbCBjYW52YXMgbm9kZXMuXHJcblx0XHRcdGNvbnN0IGRhdGEgICAgICA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLmdldF9hbGxfZGF0YV9hdHRyaWJ1dGVzKCBpdGVtICk7XHJcblx0XHRcdGNvbnN0IGlzU2VjdGlvbiA9IGl0ZW0uY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgfHwgKGRhdGE/LnR5cGUgfHwgaXRlbS5kYXRhc2V0Py50eXBlKSA9PT0gJ3NlY3Rpb24nO1xyXG5cclxuXHRcdFx0aWYgKCAhIGlzU2VjdGlvbiApIHtcclxuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGNvbnN0IGZyb21QYWxldHRlID0gdGhpcy5idWlsZGVyPy5wYWxldHRlX3Vscz8uaW5jbHVkZXM/LiggZXZ0LmZyb20gKSA9PT0gdHJ1ZTtcclxuXHJcblx0XHRcdGlmICggISBmcm9tUGFsZXR0ZSApIHtcclxuXHRcdFx0XHQvLyBDYW52YXMgLT4gY2FudmFzIG1vdmU6IERPIE5PVCByZWJ1aWxkL3JlbW92ZTsgcHJlc2VydmUgY2hpbGRyZW4uXHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLmFkZF9vdmVybGF5X3Rvb2xiYXI/LiggaXRlbSApOyAgICAgICAgICAgICAgICAgICAgICAgLy8gZW5zdXJlIG92ZXJsYXkgZXhpc3RzXHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLnBhZ2VzX3NlY3Rpb25zPy5pbml0X2FsbF9uZXN0ZWRfc29ydGFibGVzPy4oIGl0ZW0gKTsgLy8gZW5zdXJlIGlubmVyIHNvcnRhYmxlc1xyXG5cclxuXHRcdFx0XHQvLyBFbnN1cmUgbWV0YWRhdGEgcHJlc2VudC91cGRhdGVkXHJcblx0XHRcdFx0aXRlbS5kYXRhc2V0LnR5cGUgICAgPSAnc2VjdGlvbic7XHJcblx0XHRcdFx0Y29uc3QgY29scyAgICAgICAgICAgPSBpdGVtLnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX3JvdyA+IC53cGJjX2JmYl9fY29sdW1uJyApLmxlbmd0aCB8fCAxO1xyXG5cdFx0XHRcdGl0ZW0uZGF0YXNldC5jb2x1bW5zID0gU3RyaW5nKCBjb2xzICk7XHJcblxyXG5cdFx0XHRcdC8vIFNlbGVjdCAmIG5vdGlmeSBzdWJzY3JpYmVycyAobGF5b3V0L21pbiBndWFyZHMsIGV0Yy4pXHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLnNlbGVjdF9maWVsZD8uKCBpdGVtICk7XHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLmJ1cz8uZW1pdD8uKCBDb3JlLldQQkNfQkZCX0V2ZW50cy5TVFJVQ1RVUkVfQ0hBTkdFLCB7IGVsOiBpdGVtLCByZWFzb246ICdzZWN0aW9uLW1vdmUnIH0gKTtcclxuXHRcdFx0XHR0aGlzLmJ1aWxkZXIudXNhZ2U/LnVwZGF0ZV9wYWxldHRlX3VpPy4oKTtcclxuXHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gaGFuZGxlZC5cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gUGFsZXR0ZSAtPiBjYW52YXM6IGJ1aWxkIGEgYnJhbmQtbmV3IHNlY3Rpb24gdXNpbmcgdGhlIHNhbWUgcGF0aCBhcyB0aGUgZHJvcGRvd24vbWVudVxyXG5cdFx0XHRjb25zdCB0byAgID0gZXZ0LnRvPy5jbG9zZXN0Py4oICcud3BiY19iZmJfX2NvbHVtbiwgLndwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXInICkgfHwgZXZ0LnRvO1xyXG5cdFx0XHRjb25zdCBjb2xzID0gcGFyc2VJbnQoIGRhdGE/LmNvbHVtbnMgfHwgaXRlbS5kYXRhc2V0LmNvbHVtbnMgfHwgMSwgMTAgKSB8fCAxO1xyXG5cclxuXHRcdFx0Ly8gUmVtb3ZlIHRoZSBwYWxldHRlIGNsb25lIHBsYWNlaG9sZGVyLlxyXG5cdFx0XHRpdGVtLnBhcmVudE5vZGUgJiYgaXRlbS5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKCBpdGVtICk7XHJcblxyXG5cdFx0XHQvLyBDcmVhdGUgdGhlIHJlYWwgc2VjdGlvbi5cclxuXHRcdFx0dGhpcy5idWlsZGVyLnBhZ2VzX3NlY3Rpb25zLmFkZF9zZWN0aW9uKCB0bywgY29scyApO1xyXG5cclxuXHRcdFx0Ly8gSW5zZXJ0IGF0IHRoZSBwcmVjaXNlIGRyb3AgaW5kZXguXHJcblx0XHRcdGNvbnN0IHNlY3Rpb24gPSB0by5sYXN0RWxlbWVudENoaWxkOyAvLyBhZGRfc2VjdGlvbiBhcHBlbmRzIHRvIGVuZC5cclxuXHRcdFx0aWYgKCBldnQubmV3SW5kZXggIT0gbnVsbCAmJiBldnQubmV3SW5kZXggPCB0by5jaGlsZHJlbi5sZW5ndGggLSAxICkge1xyXG5cdFx0XHRcdGNvbnN0IHJlZiA9IHRvLmNoaWxkcmVuW2V2dC5uZXdJbmRleF0gfHwgbnVsbDtcclxuXHRcdFx0XHR0by5pbnNlcnRCZWZvcmUoIHNlY3Rpb24sIHJlZiApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBGaW5hbGl6ZTogb3ZlcmxheSwgc2VsZWN0aW9uLCBldmVudHMsIHVzYWdlIHJlZnJlc2guXHJcblx0XHRcdHRoaXMuYnVpbGRlci5hZGRfb3ZlcmxheV90b29sYmFyPy4oIHNlY3Rpb24gKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyLnNlbGVjdF9maWVsZD8uKCBzZWN0aW9uICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlci5idXM/LmVtaXQ/LiggQ29yZS5XUEJDX0JGQl9FdmVudHMuRklFTERfQURELCB7XHJcblx0XHRcdFx0ZWwgOiBzZWN0aW9uLFxyXG5cdFx0XHRcdGlkIDogc2VjdGlvbi5kYXRhc2V0LmlkLFxyXG5cdFx0XHRcdHVpZDogc2VjdGlvbi5kYXRhc2V0LnVpZFxyXG5cdFx0XHR9ICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlci51c2FnZT8udXBkYXRlX3BhbGV0dGVfdWk/LigpO1xyXG5cclxuXHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBEZXN0cm95IGFsbCBTb3J0YWJsZSBpbnN0YW5jZXMgY3JlYXRlZCBieSB0aGlzIG1hbmFnZXIuXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMge3ZvaWR9XHJcblx0XHQgKi9cclxuXHRcdGRlc3Ryb3lBbGwoKSB7XHJcblx0XHRcdHRoaXMuX2NvbnRhaW5lcnMuZm9yRWFjaCggKCBlbCApID0+IHtcclxuXHRcdFx0XHRjb25zdCBpbnN0ID0gU29ydGFibGUuZ2V0Py4oIGVsICk7XHJcblx0XHRcdFx0aWYgKCBpbnN0ICkge1xyXG5cdFx0XHRcdFx0aW5zdC5kZXN0cm95KCk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9ICk7XHJcblx0XHRcdHRoaXMuX2NvbnRhaW5lcnMuY2xlYXIoKTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBTbWFsbCBET00gY29udHJhY3QgYW5kIHJlbmRlcmVyIGhlbHBlclxyXG5cdCAqXHJcblx0ICogQHR5cGUge1JlYWRvbmx5PHtcclxuXHQgKiAgICAgICAgICAgICAgICAgIFNFTEVDVE9SUzoge3BhZ2VQYW5lbDogc3RyaW5nLCBmaWVsZDogc3RyaW5nLCB2YWxpZEZpZWxkOiBzdHJpbmcsIHNlY3Rpb246IHN0cmluZywgY29sdW1uOiBzdHJpbmcsIHJvdzogc3RyaW5nLCBvdmVybGF5OiBzdHJpbmd9LFxyXG5cdCAqICAgICAgICAgICAgICAgICAgQ0xBU1NFUzoge3NlbGVjdGVkOiBzdHJpbmd9LFxyXG5cdCAqICAgICAgICBcdCAgICAgICAgQVRUUjoge2lkOiBzdHJpbmcsIG5hbWU6IHN0cmluZywgaHRtbElkOiBzdHJpbmcsIHVzYWdlS2V5OiBzdHJpbmcsIHVpZDogc3RyaW5nfX1cclxuXHQgKiAgICAgICAgPn1cclxuXHQgKi9cclxuXHRDb3JlLldQQkNfQkZCX0RPTSA9IE9iamVjdC5mcmVlemUoIHtcclxuXHRcdFNFTEVDVE9SUzoge1xyXG5cdFx0XHRwYWdlUGFuZWwgOiAnLndwYmNfYmZiX19wYW5lbC0tcHJldmlldycsXHJcblx0XHRcdGZpZWxkICAgICA6ICcud3BiY19iZmJfX2ZpZWxkJyxcclxuXHRcdFx0dmFsaWRGaWVsZDogJy53cGJjX2JmYl9fZmllbGQ6bm90KC5pcy1pbnZhbGlkKScsXHJcblx0XHRcdHNlY3Rpb24gICA6ICcud3BiY19iZmJfX3NlY3Rpb24nLFxyXG5cdFx0XHRjb2x1bW4gICAgOiAnLndwYmNfYmZiX19jb2x1bW4nLFxyXG5cdFx0XHRyb3cgICAgICAgOiAnLndwYmNfYmZiX19yb3cnLFxyXG5cdFx0XHRvdmVybGF5ICAgOiAnLndwYmNfYmZiX19vdmVybGF5LWNvbnRyb2xzJ1xyXG5cdFx0fSxcclxuXHRcdENMQVNTRVMgIDoge1xyXG5cdFx0XHRzZWxlY3RlZDogJ2lzLXNlbGVjdGVkJ1xyXG5cdFx0fSxcclxuXHRcdEFUVFIgICAgIDoge1xyXG5cdFx0XHRpZCAgICAgIDogJ2RhdGEtaWQnLFxyXG5cdFx0XHRuYW1lICAgIDogJ2RhdGEtbmFtZScsXHJcblx0XHRcdGh0bWxJZCAgOiAnZGF0YS1odG1sX2lkJyxcclxuXHRcdFx0dXNhZ2VLZXk6ICdkYXRhLXVzYWdlX2tleScsXHJcblx0XHRcdHVpZCAgICAgOiAnZGF0YS11aWQnXHJcblx0XHR9XHJcblx0fSApO1xyXG5cclxuXHRDb3JlLldQQkNfRm9ybV9CdWlsZGVyX0hlbHBlciA9IGNsYXNzIHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIENyZWF0ZSBhbiBIVE1MIGVsZW1lbnQuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHRhZyAtIEhUTUwgdGFnIG5hbWUuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gW2NsYXNzX25hbWU9JyddIC0gT3B0aW9uYWwgQ1NTIGNsYXNzIG5hbWUuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gW2lubmVyX2h0bWw9JyddIC0gT3B0aW9uYWwgaW5uZXJIVE1MLlxyXG5cdFx0ICogQHJldHVybnMge0hUTUxFbGVtZW50fSBDcmVhdGVkIGVsZW1lbnQuXHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBjcmVhdGVfZWxlbWVudCggdGFnLCBjbGFzc19uYW1lID0gJycsIGlubmVyX2h0bWwgPSAnJyApIHtcclxuXHRcdFx0Y29uc3QgZWwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCB0YWcgKTtcclxuXHRcdFx0aWYgKCBjbGFzc19uYW1lICkge1xyXG5cdFx0XHRcdGVsLmNsYXNzTmFtZSA9IGNsYXNzX25hbWU7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBpbm5lcl9odG1sICkge1xyXG5cdFx0XHRcdGVsLmlubmVySFRNTCA9IGlubmVyX2h0bWw7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIGVsO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2V0IG11bHRpcGxlIGBkYXRhLSpgIGF0dHJpYnV0ZXMgb24gYSBnaXZlbiBlbGVtZW50LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsIC0gVGFyZ2V0IGVsZW1lbnQuXHJcblx0XHQgKiBAcGFyYW0ge09iamVjdH0gZGF0YV9vYmogLSBLZXktdmFsdWUgcGFpcnMgZm9yIGRhdGEgYXR0cmlidXRlcy5cclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc2V0X2RhdGFfYXR0cmlidXRlcyggZWwsIGRhdGFfb2JqICkge1xyXG5cdFx0XHRPYmplY3QuZW50cmllcyggZGF0YV9vYmogKS5mb3JFYWNoKCAoIFsga2V5LCB2YWwgXSApID0+IHtcclxuXHRcdFx0XHQvLyBQcmV2aW91c2x5OiAyMDI1LTA5LTAxIDE3OjA5OlxyXG5cdFx0XHRcdC8vIGNvbnN0IHZhbHVlID0gKHR5cGVvZiB2YWwgPT09ICdvYmplY3QnKSA/IEpTT04uc3RyaW5naWZ5KCB2YWwgKSA6IHZhbDtcclxuXHRcdFx0XHQvL05ldzpcclxuXHRcdFx0XHRsZXQgdmFsdWU7XHJcblx0XHRcdFx0aWYgKCB0eXBlb2YgdmFsID09PSAnb2JqZWN0JyAmJiB2YWwgIT09IG51bGwgKSB7XHJcblx0XHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0XHR2YWx1ZSA9IEpTT04uc3RyaW5naWZ5KCB2YWwgKTtcclxuXHRcdFx0XHRcdH0gY2F0Y2gge1xyXG5cdFx0XHRcdFx0XHR2YWx1ZSA9ICcnO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHR2YWx1ZSA9IHZhbDtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGVsLnNldEF0dHJpYnV0ZSggJ2RhdGEtJyArIGtleSwgdmFsdWUgKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogR2V0IGFsbCBgZGF0YS0qYCBhdHRyaWJ1dGVzIGZyb20gYW4gZWxlbWVudCBhbmQgcGFyc2UgSlNPTiB3aGVyZSBwb3NzaWJsZS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbCAtIEVsZW1lbnQgdG8gZXh0cmFjdCBkYXRhIGZyb20uXHJcblx0XHQgKiBAcmV0dXJucyB7T2JqZWN0fSBQYXJzZWQga2V5LXZhbHVlIG1hcCBvZiBkYXRhIGF0dHJpYnV0ZXMuXHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBnZXRfYWxsX2RhdGFfYXR0cmlidXRlcyggZWwgKSB7XHJcblx0XHRcdGNvbnN0IGRhdGEgPSB7fTtcclxuXHJcblx0XHRcdGlmICggISBlbCB8fCAhIGVsLmF0dHJpYnV0ZXMgKSB7XHJcblx0XHRcdFx0cmV0dXJuIGRhdGE7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdEFycmF5LmZyb20oIGVsLmF0dHJpYnV0ZXMgKS5mb3JFYWNoKFxyXG5cdFx0XHRcdCggYXR0ciApID0+IHtcclxuXHRcdFx0XHRcdGlmICggYXR0ci5uYW1lLnN0YXJ0c1dpdGgoICdkYXRhLScgKSApIHtcclxuXHRcdFx0XHRcdFx0Y29uc3Qga2V5ID0gYXR0ci5uYW1lLnJlcGxhY2UoIC9eZGF0YS0vLCAnJyApO1xyXG5cdFx0XHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0XHRcdGRhdGFba2V5XSA9IEpTT04ucGFyc2UoIGF0dHIudmFsdWUgKTtcclxuXHRcdFx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHRcdFx0ZGF0YVtrZXldID0gYXR0ci52YWx1ZTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0KTtcclxuXHJcblx0XHRcdC8vIE9ubHkgZGVmYXVsdCB0aGUgbGFiZWwgaWYgaXQncyB0cnVseSBhYnNlbnQgKHVuZGVmaW5lZC9udWxsKSwgbm90IHdoZW4gaXQncyBhbiBlbXB0eSBzdHJpbmcuXHJcblx0XHRcdGNvbnN0IGhhc0V4cGxpY2l0TGFiZWwgPSBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoIGRhdGEsICdsYWJlbCcgKTtcclxuXHRcdFx0aWYgKCAhIGhhc0V4cGxpY2l0TGFiZWwgJiYgZGF0YS5pZCApIHtcclxuXHRcdFx0XHRkYXRhLmxhYmVsID0gZGF0YS5pZC5jaGFyQXQoIDAgKS50b1VwcGVyQ2FzZSgpICsgZGF0YS5pZC5zbGljZSggMSApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gZGF0YTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFJlbmRlciBhIHNpbXBsZSBsYWJlbCArIHR5cGUgcHJldmlldyAodXNlZCBmb3IgdW5rbm93biBvciBmYWxsYmFjayBmaWVsZHMpLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBmaWVsZF9kYXRhIC0gRmllbGQgZGF0YSBvYmplY3QuXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfSBIVE1MIGNvbnRlbnQuXHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyByZW5kZXJfZmllbGRfaW5uZXJfaHRtbCggZmllbGRfZGF0YSApIHtcclxuXHRcdFx0Ly8gTWFrZSB0aGUgZmFsbGJhY2sgcHJldmlldyByZXNwZWN0IGFuIGVtcHR5IGxhYmVsLlxyXG5cdFx0XHRjb25zdCBoYXNMYWJlbCA9IE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggZmllbGRfZGF0YSwgJ2xhYmVsJyApO1xyXG5cdFx0XHRjb25zdCBsYWJlbCAgICA9IGhhc0xhYmVsID8gU3RyaW5nKCBmaWVsZF9kYXRhLmxhYmVsICkgOiBTdHJpbmcoIGZpZWxkX2RhdGEuaWQgfHwgJyhubyBsYWJlbCknICk7XHJcblxyXG5cdFx0XHRjb25zdCB0eXBlICAgICAgICA9IFN0cmluZyggZmllbGRfZGF0YS50eXBlIHx8ICd1bmtub3duJyApO1xyXG5cdFx0XHRjb25zdCBpc19yZXF1aXJlZCA9IGZpZWxkX2RhdGEucmVxdWlyZWQgPT09IHRydWUgfHwgZmllbGRfZGF0YS5yZXF1aXJlZCA9PT0gJ3RydWUnIHx8IGZpZWxkX2RhdGEucmVxdWlyZWQgPT09IDEgfHwgZmllbGRfZGF0YS5yZXF1aXJlZCA9PT0gJzEnO1xyXG5cclxuXHRcdFx0Y29uc3Qgd3JhcHBlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdkaXYnICk7XHJcblxyXG5cdFx0XHRjb25zdCBzcGFuTGFiZWwgICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnc3BhbicgKTtcclxuXHRcdFx0c3BhbkxhYmVsLmNsYXNzTmFtZSAgID0gJ3dwYmNfYmZiX19maWVsZC1sYWJlbCc7XHJcblx0XHRcdHNwYW5MYWJlbC50ZXh0Q29udGVudCA9IGxhYmVsICsgKGlzX3JlcXVpcmVkID8gJyAqJyA6ICcnKTtcclxuXHRcdFx0d3JhcHBlci5hcHBlbmRDaGlsZCggc3BhbkxhYmVsICk7XHJcblxyXG5cdFx0XHRjb25zdCBzcGFuVHlwZSAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdzcGFuJyApO1xyXG5cdFx0XHRzcGFuVHlwZS5jbGFzc05hbWUgICA9ICd3cGJjX2JmYl9fZmllbGQtdHlwZSc7XHJcblx0XHRcdHNwYW5UeXBlLnRleHRDb250ZW50ID0gdHlwZTtcclxuXHRcdFx0d3JhcHBlci5hcHBlbmRDaGlsZCggc3BhblR5cGUgKTtcclxuXHJcblx0XHRcdHJldHVybiB3cmFwcGVyLmlubmVySFRNTDtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIERlYm91bmNlIGEgZnVuY3Rpb24uXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtGdW5jdGlvbn0gZm4gLSBGdW5jdGlvbiB0byBkZWJvdW5jZS5cclxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSB3YWl0IC0gRGVsYXkgaW4gbXMuXHJcblx0XHQgKiBAcmV0dXJucyB7RnVuY3Rpb259IERlYm91bmNlZCBmdW5jdGlvbi5cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGRlYm91bmNlKCBmbiwgd2FpdCA9IDEyMCApIHtcclxuXHRcdFx0bGV0IHQgPSBudWxsO1xyXG5cdFx0XHRyZXR1cm4gZnVuY3Rpb24gZGVib3VuY2VkKCAuLi5hcmdzICkge1xyXG5cdFx0XHRcdGlmICggdCApIHtcclxuXHRcdFx0XHRcdGNsZWFyVGltZW91dCggdCApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHR0ID0gc2V0VGltZW91dCggKCkgPT4gZm4uYXBwbHkoIHRoaXMsIGFyZ3MgKSwgd2FpdCApO1xyXG5cdFx0XHR9O1xyXG5cdFx0fVxyXG5cclxuXHR9O1xyXG5cclxuXHQvLyBSZW5kZXJlciByZWdpc3RyeS4gQWxsb3dzIGxhdGUgcmVnaXN0cmF0aW9uIGFuZCBhdm9pZHMgdGlnaHQgY291cGxpbmcgdG8gYSBnbG9iYWwgbWFwLlxyXG5cdENvcmUuV1BCQ19CRkJfRmllbGRfUmVuZGVyZXJfUmVnaXN0cnkgPSAoZnVuY3Rpb24gKCkge1xyXG5cdFx0Y29uc3QgbWFwID0gbmV3IE1hcCgpO1xyXG5cdFx0cmV0dXJuIHtcclxuXHRcdFx0cmVnaXN0ZXIoIHR5cGUsIENsYXNzUmVmICkge1xyXG5cdFx0XHRcdG1hcC5zZXQoIFN0cmluZyggdHlwZSApLCBDbGFzc1JlZiApO1xyXG5cdFx0XHR9LFxyXG5cdFx0XHRnZXQoIHR5cGUgKSB7XHJcblx0XHRcdFx0cmV0dXJuIG1hcC5nZXQoIFN0cmluZyggdHlwZSApICk7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0fSkoKTtcclxuXHJcbn0oIHdpbmRvdyApKTsiLCIvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gRmlsZSAgL2luY2x1ZGVzL3BhZ2UtZm9ybS1idWlsZGVyL19vdXQvY29yZS9iZmItZmllbGRzLmpzID09IHwgMjAyNS0wOS0xMCAxNTo0N1xyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuKGZ1bmN0aW9uICggdyApIHtcclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdC8vIFNpbmdsZSBnbG9iYWwgbmFtZXNwYWNlIChpZGVtcG90ZW50ICYgbG9hZC1vcmRlciBzYWZlKS5cclxuXHRjb25zdCBDb3JlID0gKCB3LldQQkNfQkZCX0NvcmUgPSB3LldQQkNfQkZCX0NvcmUgfHwge30gKTtcclxuXHRjb25zdCBVSSAgID0gKCBDb3JlLlVJID0gQ29yZS5VSSB8fCB7fSApO1xyXG5cclxuXHQvKipcclxuXHQgKiBCYXNlIGNsYXNzIGZvciBmaWVsZCByZW5kZXJlcnMgKHN0YXRpYy1vbmx5IGNvbnRyYWN0KS5cclxuXHQgKiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblx0ICogQ29udHJhY3QgZXhwb3NlZCB0byB0aGUgYnVpbGRlciAoc3RhdGljIG1ldGhvZHMgb24gdGhlIENMQVNTIGl0c2VsZik6XHJcblx0ICogICAtIHJlbmRlcihlbCwgZGF0YSwgY3R4KSAgICAgICAgICAgICAgLy8gUkVRVUlSRURcclxuXHQgKiAgIC0gb25fZmllbGRfZHJvcChkYXRhLCBlbCwgbWV0YSkgICAgICAvLyBPUFRJT05BTCAoZGVmYXVsdCBwcm92aWRlZClcclxuXHQgKlxyXG5cdCAqIEhlbHBlcnMgZm9yIHN1YmNsYXNzZXM6XHJcblx0ICogICAtIGdldF9kZWZhdWx0cygpICAgICAtPiBwZXItZmllbGQgZGVmYXVsdHMgKE1VU1Qgb3ZlcnJpZGUgaW4gc3ViY2xhc3MgdG8gc2V0IHR5cGUvbGFiZWwpXHJcblx0ICogICAtIG5vcm1hbGl6ZV9kYXRhKGQpICAtPiBzaGFsbG93IG1lcmdlIHdpdGggZGVmYXVsdHNcclxuXHQgKiAgIC0gZ2V0X3RlbXBsYXRlKGlkKSAgIC0+IHBlci1pZCBjYWNoZWQgd3AudGVtcGxhdGUgY29tcGlsZXJcclxuXHQgKlxyXG5cdCAqIFN1YmNsYXNzIHVzYWdlOlxyXG5cdCAqICAgY2xhc3MgV1BCQ19CRkJfRmllbGRfVGV4dCBleHRlbmRzIENvcmUuV1BCQ19CRkJfRmllbGRfQmFzZSB7IHN0YXRpYyBnZXRfZGVmYXVsdHMoKXsgLi4uIH0gfVxyXG5cdCAqICAgV1BCQ19CRkJfRmllbGRfVGV4dC50ZW1wbGF0ZV9pZCA9ICd3cGJjLWJmYi1maWVsZC10ZXh0JztcclxuXHQgKiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblx0ICovXHJcblx0Q29yZS5XUEJDX0JGQl9GaWVsZF9CYXNlID0gY2xhc3Mge1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRGVmYXVsdCBmaWVsZCBkYXRhIChnZW5lcmljIGJhc2VsaW5lKS5cclxuXHRcdCAqIFN1YmNsYXNzZXMgTVVTVCBvdmVycmlkZSB0byBwcm92aWRlIHsgdHlwZSwgbGFiZWwgfSBhcHByb3ByaWF0ZSBmb3IgdGhlIGZpZWxkLlxyXG5cdFx0ICogQHJldHVybnMge09iamVjdH1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGdldF9kZWZhdWx0cygpIHtcclxuXHRcdFx0cmV0dXJuIHtcclxuXHRcdFx0XHR0eXBlICAgICAgICA6ICdmaWVsZCcsXHJcblx0XHRcdFx0bGFiZWwgICAgICAgOiAnRmllbGQnLFxyXG5cdFx0XHRcdG5hbWUgICAgICAgIDogJ2ZpZWxkJyxcclxuXHRcdFx0XHRodG1sX2lkICAgICA6ICcnLFxyXG5cdFx0XHRcdHBsYWNlaG9sZGVyIDogJycsXHJcblx0XHRcdFx0cmVxdWlyZWQgICAgOiBmYWxzZSxcclxuXHRcdFx0XHRtaW5sZW5ndGggICA6ICcnLFxyXG5cdFx0XHRcdG1heGxlbmd0aCAgIDogJycsXHJcblx0XHRcdFx0cGF0dGVybiAgICAgOiAnJyxcclxuXHRcdFx0XHRjc3NjbGFzcyAgICA6ICcnLFxyXG5cdFx0XHRcdGhlbHAgICAgICAgIDogJydcclxuXHRcdFx0fTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNoYWxsb3ctbWVyZ2UgaW5jb21pbmcgZGF0YSB3aXRoIGRlZmF1bHRzLlxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IGRhdGFcclxuXHRcdCAqIEByZXR1cm5zIHtPYmplY3R9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBub3JtYWxpemVfZGF0YSggZGF0YSApIHtcclxuXHRcdFx0dmFyIGQgICAgICAgID0gZGF0YSB8fCB7fTtcclxuXHRcdFx0dmFyIGRlZmF1bHRzID0gdGhpcy5nZXRfZGVmYXVsdHMoKTtcclxuXHRcdFx0dmFyIG91dCAgICAgID0ge307XHJcblx0XHRcdHZhciBrO1xyXG5cclxuXHRcdFx0Zm9yICggayBpbiBkZWZhdWx0cyApIHtcclxuXHRcdFx0XHRpZiAoIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggZGVmYXVsdHMsIGsgKSApIHtcclxuXHRcdFx0XHRcdG91dFtrXSA9IGRlZmF1bHRzW2tdO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRmb3IgKCBrIGluIGQgKSB7XHJcblx0XHRcdFx0aWYgKCBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoIGQsIGsgKSApIHtcclxuXHRcdFx0XHRcdG91dFtrXSA9IGRba107XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiBvdXQ7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDb21waWxlIGFuZCBjYWNoZSBhIHdwLnRlbXBsYXRlIGJ5IGlkIChwZXItaWQgY2FjaGUpLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHRlbXBsYXRlX2lkXHJcblx0XHQgKiBAcmV0dXJucyB7RnVuY3Rpb258bnVsbH1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGdldF90ZW1wbGF0ZSh0ZW1wbGF0ZV9pZCkge1xyXG5cclxuXHRcdFx0Ly8gQWNjZXB0IGVpdGhlciBcIndwYmMtYmZiLWZpZWxkLXRleHRcIiBvciBcInRtcGwtd3BiYy1iZmItZmllbGQtdGV4dFwiLlxyXG5cdFx0XHRpZiAoICEgdGVtcGxhdGVfaWQgfHwgISB3aW5kb3cud3AgfHwgISB3cC50ZW1wbGF0ZSApIHtcclxuXHRcdFx0XHRyZXR1cm4gbnVsbDtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCBkb21JZCA9IHRlbXBsYXRlX2lkLnN0YXJ0c1dpdGgoICd0bXBsLScgKSA/IHRlbXBsYXRlX2lkIDogKCd0bXBsLScgKyB0ZW1wbGF0ZV9pZCk7XHJcblx0XHRcdGlmICggISBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggZG9tSWQgKSApIHtcclxuXHRcdFx0XHRyZXR1cm4gbnVsbDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCAhIENvcmUuX19iZmJfdHBsX2NhY2hlX21hcCApIHtcclxuXHRcdFx0XHRDb3JlLl9fYmZiX3RwbF9jYWNoZV9tYXAgPSB7fTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTm9ybWFsaXplIGlkIGZvciB0aGUgY29tcGlsZXIgJiBjYWNoZS4gLy8gd3AudGVtcGxhdGUgZXhwZWN0cyBpZCBXSVRIT1VUIHRoZSBcInRtcGwtXCIgcHJlZml4ICFcclxuXHRcdFx0Y29uc3Qga2V5ID0gdGVtcGxhdGVfaWQucmVwbGFjZSggL150bXBsLS8sICcnICk7XHJcblx0XHRcdGlmICggQ29yZS5fX2JmYl90cGxfY2FjaGVfbWFwW2tleV0gKSB7XHJcblx0XHRcdFx0cmV0dXJuIENvcmUuX19iZmJfdHBsX2NhY2hlX21hcFtrZXldO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCBjb21waWxlciA9IHdwLnRlbXBsYXRlKCBrZXkgKTsgICAgIC8vIDwtLSBub3JtYWxpemVkIGlkIGhlcmVcclxuXHRcdFx0aWYgKCBjb21waWxlciApIHtcclxuXHRcdFx0XHRDb3JlLl9fYmZiX3RwbF9jYWNoZV9tYXBba2V5XSA9IGNvbXBpbGVyO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gY29tcGlsZXI7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSRVFVSVJFRDogcmVuZGVyIHByZXZpZXcgaW50byBob3N0IGVsZW1lbnQgKGZ1bGwgcmVkcmF3OyBpZGVtcG90ZW50KS5cclxuXHRcdCAqIFN1YmNsYXNzZXMgc2hvdWxkIHNldCBzdGF0aWMgYHRlbXBsYXRlX2lkYCB0byBhIHZhbGlkIHdwLnRlbXBsYXRlIGlkLlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZWxcclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAgICAgIGRhdGFcclxuXHRcdCAqIEBwYXJhbSB7e21vZGU/OnN0cmluZyxidWlsZGVyPzphbnksdHBsPzpGdW5jdGlvbixzYW5pdD86YW55fX0gY3R4XHJcblx0XHQgKiBAcmV0dXJucyB7dm9pZH1cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHJlbmRlciggZWwsIGRhdGEsIGN0eCApIHtcclxuXHRcdFx0aWYgKCAhIGVsICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIGNvbXBpbGUgPSB0aGlzLmdldF90ZW1wbGF0ZSggdGhpcy50ZW1wbGF0ZV9pZCApO1xyXG5cdFx0XHR2YXIgZCAgICAgICA9IHRoaXMubm9ybWFsaXplX2RhdGEoIGRhdGEgKTtcclxuXHJcblx0XHRcdHZhciBzID0gKGN0eCAmJiBjdHguc2FuaXQpID8gY3R4LnNhbml0IDogQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZTtcclxuXHJcblx0XHRcdC8vIFNhbml0aXplIGNyaXRpY2FsIGF0dHJpYnV0ZXMgYmVmb3JlIHRlbXBsYXRpbmcuXHJcblx0XHRcdGlmICggcyApIHtcclxuXHRcdFx0XHRkLmh0bWxfaWQgPSBkLmh0bWxfaWQgPyBzLnNhbml0aXplX2h0bWxfaWQoIFN0cmluZyggZC5odG1sX2lkICkgKSA6ICcnO1xyXG5cdFx0XHRcdGQubmFtZSAgICA9IHMuc2FuaXRpemVfaHRtbF9uYW1lKCBTdHJpbmcoIGQubmFtZSB8fCBkLmlkIHx8ICdmaWVsZCcgKSApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGQuaHRtbF9pZCA9IGQuaHRtbF9pZCA/IFN0cmluZyggZC5odG1sX2lkICkgOiAnJztcclxuXHRcdFx0XHRkLm5hbWUgICAgPSBTdHJpbmcoIGQubmFtZSB8fCBkLmlkIHx8ICdmaWVsZCcgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gRmFsbCBiYWNrIHRvIGdlbmVyaWMgcHJldmlldyBpZiB0ZW1wbGF0ZSBub3QgYXZhaWxhYmxlLlxyXG5cdFx0XHRpZiAoIGNvbXBpbGUgKSB7XHJcblx0XHRcdFx0ZWwuaW5uZXJIVE1MID0gY29tcGlsZSggZCApO1xyXG5cclxuXHRcdFx0XHQvLyBBZnRlciByZW5kZXIsIHNldCBhdHRyaWJ1dGUgdmFsdWVzIHZpYSBET00gc28gcXVvdGVzL25ld2xpbmVzIGFyZSBoYW5kbGVkIGNvcnJlY3RseS5cclxuXHRcdFx0XHRjb25zdCBpbnB1dCA9IGVsLnF1ZXJ5U2VsZWN0b3IoICdpbnB1dCwgdGV4dGFyZWEsIHNlbGVjdCcgKTtcclxuXHRcdFx0XHRpZiAoIGlucHV0ICkge1xyXG5cdFx0XHRcdFx0aWYgKCBkLnBsYWNlaG9sZGVyICE9IG51bGwgKSBpbnB1dC5zZXRBdHRyaWJ1dGUoICdwbGFjZWhvbGRlcicsIFN0cmluZyggZC5wbGFjZWhvbGRlciApICk7XHJcblx0XHRcdFx0XHRpZiAoIGQudGl0bGUgIT0gbnVsbCApIGlucHV0LnNldEF0dHJpYnV0ZSggJ3RpdGxlJywgU3RyaW5nKCBkLnRpdGxlICkgKTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGVsLmlubmVySFRNTCA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLnJlbmRlcl9maWVsZF9pbm5lcl9odG1sKCBkICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGVsLmRhdGFzZXQudHlwZSA9IGQudHlwZSB8fCAnZmllbGQnO1xyXG5cdFx0XHRlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLWxhYmVsJywgKGQubGFiZWwgIT0gbnVsbCA/IFN0cmluZyggZC5sYWJlbCApIDogJycpICk7IC8vIGFsbG93IFwiXCIuXHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogT1BUSU9OQUwgaG9vayBleGVjdXRlZCBhZnRlciBmaWVsZCBpcyBkcm9wcGVkL2xvYWRlZC9wcmV2aWV3LlxyXG5cdFx0ICogRGVmYXVsdCBleHRlbmRlZDpcclxuXHRcdCAqIC0gT24gZmlyc3QgZHJvcDogc3RhbXAgZGVmYXVsdCBsYWJlbCAoZXhpc3RpbmcgYmVoYXZpb3IpIGFuZCBtYXJrIGZpZWxkIGFzIFwiZnJlc2hcIiBmb3IgYXV0by1uYW1lLlxyXG5cdFx0ICogLSBPbiBsb2FkOiBtYXJrIGFzIGxvYWRlZCBzbyBsYXRlciBsYWJlbCBlZGl0cyBkbyBub3QgcmVuYW1lIHRoZSBzYXZlZCBuYW1lLlxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgb25fZmllbGRfZHJvcChkYXRhLCBlbCwgbWV0YSkge1xyXG5cclxuXHRcdFx0Y29uc3QgY29udGV4dCA9IChtZXRhICYmIG1ldGEuY29udGV4dCkgPyBTdHJpbmcoIG1ldGEuY29udGV4dCApIDogJyc7XHJcblxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHQvLyBORVc6IFNlZWQgZGVmYXVsdCBcImhlbHBcIiAoYW5kIGtlZXAgaXQgaW4gU3RydWN0dXJlKSBmb3IgYWxsIGZpZWxkIHBhY2tzIHRoYXQgZGVmaW5lIGl0LlxyXG5cdFx0XHQvLyBUaGlzIGZpeGVzIHRoZSBtaXNtYXRjaCB3aGVyZTpcclxuXHRcdFx0Ly8gICAtIFVJIHNob3dzIGRlZmF1bHQgaGVscCB2aWEgbm9ybWFsaXplX2RhdGEoKSAvIHRlbXBsYXRlc1xyXG5cdFx0XHQvLyAgIC0gYnV0IGdldF9zdHJ1Y3R1cmUoKSAvIGV4cG9ydGVycyBzZWUgYGhlbHBgIGFzIHVuZGVmaW5lZC9lbXB0eS5cclxuXHRcdFx0Ly9cclxuXHRcdFx0Ly8gQmVoYXZpb3I6XHJcblx0XHRcdC8vICAgLSBSdW5zIE9OTFkgb24gaW5pdGlhbCBkcm9wIChjb250ZXh0ID09PSAnZHJvcCcpLlxyXG5cdFx0XHQvLyAgIC0gSWYgZ2V0X2RlZmF1bHRzKCkgZXhwb3NlcyBhIG5vbi1lbXB0eSBcImhlbHBcIiwgYW5kIGRhdGEuaGVscCBpc1xyXG5cdFx0XHQvLyAgICAgbWlzc2luZyAvIG51bGwgLyBlbXB0eSBzdHJpbmcgLT4gd2UgcGVyc2lzdCB0aGUgZGVmYXVsdCBpbnRvIGBkYXRhYFxyXG5cdFx0XHQvLyAgICAgYW5kIG5vdGlmeSBTdHJ1Y3R1cmUgc28gZXhwb3J0cyBzZWUgaXQuXHJcblx0XHRcdC8vICAgLSBPbiBcImxvYWRcIiB3ZSBkbyBub3RoaW5nLCBzbyBleGlzdGluZyBmb3JtcyB3aGVyZSB1c2VyICpjbGVhcmVkKlxyXG5cdFx0XHQvLyAgICAgaGVscCB3aWxsIG5vdCBiZSBvdmVycmlkZGVuLlxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRpZiAoIGNvbnRleHQgPT09ICdkcm9wJyAmJiBkYXRhICkge1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRjb25zdCBkZWZzID0gKHR5cGVvZiB0aGlzLmdldF9kZWZhdWx0cyA9PT0gJ2Z1bmN0aW9uJykgPyB0aGlzLmdldF9kZWZhdWx0cygpIDogbnVsbDtcclxuXHRcdFx0XHRcdGlmICggZGVmcyAmJiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoIGRlZnMsICdoZWxwJyApICkge1xyXG5cdFx0XHRcdFx0XHRjb25zdCBjdXJyZW50ICAgID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKCBkYXRhLCAnaGVscCcgKSA/IGRhdGEuaGVscCA6IHVuZGVmaW5lZDtcclxuXHRcdFx0XHRcdFx0Y29uc3QgaGFzVmFsdWUgICA9IChjdXJyZW50ICE9PSB1bmRlZmluZWQgJiYgY3VycmVudCAhPT0gbnVsbCAmJiBTdHJpbmcoIGN1cnJlbnQgKSAhPT0gJycpO1xyXG5cdFx0XHRcdFx0XHRjb25zdCBkZWZhdWx0VmFsID0gZGVmcy5oZWxwO1xyXG5cclxuXHRcdFx0XHRcdFx0aWYgKCAhIGhhc1ZhbHVlICYmIGRlZmF1bHRWYWwgIT0gbnVsbCAmJiBTdHJpbmcoIGRlZmF1bHRWYWwgKSAhPT0gJycgKSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gMSkgcGVyc2lzdCBpbnRvIGRhdGEgb2JqZWN0ICh1c2VkIGJ5IFN0cnVjdHVyZSkuXHJcblx0XHRcdFx0XHRcdFx0ZGF0YS5oZWxwID0gZGVmYXVsdFZhbDtcclxuXHJcblx0XHRcdFx0XHRcdFx0Ly8gMikgbWlycm9yIGludG8gZGF0YXNldCAoZm9yIGFueSBET00tYmFzZWQgY29uc3VtZXJzKS5cclxuXHRcdFx0XHRcdFx0XHRpZiAoIGVsICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0ZWwuZGF0YXNldC5oZWxwID0gU3RyaW5nKCBkZWZhdWx0VmFsICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gMykgbm90aWZ5IFN0cnVjdHVyZSAvIGxpc3RlbmVycyAoaWYgYXZhaWxhYmxlKS5cclxuXHRcdFx0XHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdENvcmUuU3RydWN0dXJlPy51cGRhdGVfZmllbGRfcHJvcD8uKCBlbCwgJ2hlbHAnLCBkZWZhdWx0VmFsICk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdGVsLmRpc3BhdGNoRXZlbnQoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0bmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmJfZmllbGRfZGF0YV9jaGFuZ2VkJywgeyBidWJibGVzOiB0cnVlLCBkZXRhaWwgOiB7IGtleTogJ2hlbHAnLCB2YWx1ZTogZGVmYXVsdFZhbCB9IH0gKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0XHRcdFx0fSBjYXRjaCAoIF9pbm5lciApIHt9XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSBjYXRjaCAoIF9lICkge31cclxuXHRcdFx0fVxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRcdFx0aWYgKCBjb250ZXh0ID09PSAnZHJvcCcgJiYgIU9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggZGF0YSwgJ2xhYmVsJyApICkge1xyXG5cdFx0XHRcdGNvbnN0IGRlZnMgPSB0aGlzLmdldF9kZWZhdWx0cygpO1xyXG5cdFx0XHRcdGRhdGEubGFiZWwgPSBkZWZzLmxhYmVsIHx8ICdGaWVsZCc7XHJcblx0XHRcdFx0ZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1sYWJlbCcsIGRhdGEubGFiZWwgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHQvLyBNYXJrIHByb3ZlbmFuY2UgZmxhZ3MuXHJcblx0XHRcdGlmICggY29udGV4dCA9PT0gJ2Ryb3AnICkge1xyXG5cdFx0XHRcdGVsLmRhdGFzZXQuZnJlc2ggICAgICA9ICcxJzsgICAvLyBjYW4gYXV0by1uYW1lIG9uIGZpcnN0IGxhYmVsIGVkaXQuXHJcblx0XHRcdFx0ZWwuZGF0YXNldC5hdXRvbmFtZSAgID0gJzEnO1xyXG5cdFx0XHRcdGVsLmRhdGFzZXQud2FzX2xvYWRlZCA9ICcwJztcclxuXHRcdFx0XHQvLyBTZWVkIGEgcHJvdmlzaW9uYWwgdW5pcXVlIG5hbWUgaW1tZWRpYXRlbHkuXHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdGNvbnN0IGIgPSBtZXRhPy5idWlsZGVyO1xyXG5cdFx0XHRcdFx0aWYgKCBiPy5pZCAmJiAoIWVsLmhhc0F0dHJpYnV0ZSggJ2RhdGEtbmFtZScgKSB8fCAhZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1uYW1lJyApKSApIHtcclxuXHRcdFx0XHRcdFx0Y29uc3QgUyAgICA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemU7XHJcblx0XHRcdFx0XHRcdGNvbnN0IGJhc2UgPSBTLnNhbml0aXplX2h0bWxfbmFtZSggZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1pZCcgKSB8fCBkYXRhPy5pZCB8fCBkYXRhPy50eXBlIHx8ICdmaWVsZCcgKTtcclxuXHRcdFx0XHRcdFx0Y29uc3QgdW5pcSA9IGIuaWQuZW5zdXJlX3VuaXF1ZV9maWVsZF9uYW1lKCBiYXNlLCBlbCApO1xyXG5cdFx0XHRcdFx0XHRlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLW5hbWUnLCB1bmlxICk7XHJcblx0XHRcdFx0XHRcdGVsLmRhdGFzZXQubmFtZV91c2VyX3RvdWNoZWQgPSAnMCc7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSBjYXRjaCAoIF8gKSB7fVxyXG5cclxuXHRcdFx0fSBlbHNlIGlmICggY29udGV4dCA9PT0gJ2xvYWQnICkge1xyXG5cdFx0XHRcdGVsLmRhdGFzZXQuZnJlc2ggICAgICA9ICcwJztcclxuXHRcdFx0XHRlbC5kYXRhc2V0LmF1dG9uYW1lICAgPSAnMCc7XHJcblx0XHRcdFx0ZWwuZGF0YXNldC53YXNfbG9hZGVkID0gJzEnOyAgIC8vIG5ldmVyIHJlbmFtZSBuYW1lcyBmb3IgbG9hZGVkIGZpZWxkcy5cclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLSBBdXRvIFJlbmFtZSBcIkZyZXNoXCIgZmllbGQsICBvbiBlbnRlcmluZyB0aGUgbmV3IExhYmVsIC0tLVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ3JlYXRlIGEgY29uc2VydmF0aXZlIGZpZWxkIFwibmFtZVwiIGZyb20gYSBodW1hbiBsYWJlbC5cclxuXHRcdCAqIFVzZXMgdGhlIHNhbWUgY29uc3RyYWludHMgYXMgc2FuaXRpemVfaHRtbF9uYW1lIChsZXR0ZXJzL2RpZ2l0cy9fLSBhbmQgbGVhZGluZyBsZXR0ZXIpLlxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgbmFtZV9mcm9tX2xhYmVsKGxhYmVsKSB7XHJcblx0XHRcdGNvbnN0IHMgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLnNhbml0aXplX2h0bWxfbmFtZSggU3RyaW5nKCBsYWJlbCA/PyAnJyApICk7XHJcblx0XHRcdHJldHVybiBzLnRvTG93ZXJDYXNlKCkgfHwgJ2ZpZWxkJztcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEF1dG8tZmlsbCBkYXRhLW5hbWUgZnJvbSBsYWJlbCBPTkxZIGZvciBmcmVzaGx5IGRyb3BwZWQgZmllbGRzIHRoYXQgd2VyZSBub3QgZWRpdGVkIHlldC5cclxuXHRcdCAqIC0gTmV2ZXIgcnVucyBmb3Igc2VjdGlvbnMuXHJcblx0XHQgKiAtIE5ldmVyIHJ1bnMgZm9yIGxvYWRlZC9leGlzdGluZyBmaWVsZHMuXHJcblx0XHQgKiAtIFN0b3BzIGFzIHNvb24gYXMgdXNlciBlZGl0cyB0aGUgTmFtZSBtYW51YWxseS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge1dQQkNfRm9ybV9CdWlsZGVyfSBidWlsZGVyXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbCAgLSAud3BiY19iZmJfX2ZpZWxkIGVsZW1lbnRcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFZhbFxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgbWF5YmVfYXV0b25hbWVfZnJvbV9sYWJlbChidWlsZGVyLCBlbCwgbGFiZWxWYWwpIHtcclxuXHRcdFx0aWYgKCAhYnVpbGRlciB8fCAhZWwgKSByZXR1cm47XHJcblx0XHRcdGlmICggZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgKSByZXR1cm47XHJcblxyXG5cdFx0XHRjb25zdCBhbGxvd0F1dG8gPSBlbC5kYXRhc2V0LmF1dG9uYW1lID09PSAnMSc7XHJcblxyXG5cdFx0XHRjb25zdCB1c2VyVG91Y2hlZCA9IGVsLmRhdGFzZXQubmFtZV91c2VyX3RvdWNoZWQgPT09ICcxJztcclxuXHRcdFx0Y29uc3QgaXNMb2FkZWQgICAgPSBlbC5kYXRhc2V0Lndhc19sb2FkZWQgPT09ICcxJztcclxuXHJcblx0XHRcdGlmICggIWFsbG93QXV0byB8fCB1c2VyVG91Y2hlZCB8fCBpc0xvYWRlZCApIHJldHVybjtcclxuXHJcblx0XHRcdC8vIE9ubHkgb3ZlcnJpZGUgcGxhY2Vob2xkZXIteSBuYW1lc1xyXG5cdFx0XHRjb25zdCBTID0gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZTtcclxuXHJcblx0XHRcdGNvbnN0IGJhc2UgICA9IHRoaXMubmFtZV9mcm9tX2xhYmVsKCBsYWJlbFZhbCApO1xyXG5cdFx0XHRjb25zdCB1bmlxdWUgPSBidWlsZGVyLmlkLmVuc3VyZV91bmlxdWVfZmllbGRfbmFtZSggYmFzZSwgZWwgKTtcclxuXHRcdFx0ZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1uYW1lJywgdW5pcXVlICk7XHJcblxyXG5cdFx0XHRjb25zdCBpbnMgICAgICA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0Y29uc3QgbmFtZUN0cmwgPSBpbnM/LnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1pbnNwZWN0b3Ita2V5PVwibmFtZVwiXScgKTtcclxuXHRcdFx0aWYgKCBuYW1lQ3RybCAmJiAndmFsdWUnIGluIG5hbWVDdHJsICYmIG5hbWVDdHJsLnZhbHVlICE9PSB1bmlxdWUgKSBuYW1lQ3RybC52YWx1ZSA9IHVuaXF1ZTtcclxuXHRcdH1cclxuXHJcblxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFNlbGVjdF9CYXNlIChzaGFyZWQgYmFzZSBmb3Igc2VsZWN0LWxpa2UgcGFja3MpXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7Q29yZS5XUEJDX0JGQl9TZWxlY3RfQmFzZX1cclxuXHQgKi9cclxuXHRDb3JlLldQQkNfQkZCX1NlbGVjdF9CYXNlID0gY2xhc3MgZXh0ZW5kcyBDb3JlLldQQkNfQkZCX0ZpZWxkX0Jhc2Uge1xyXG5cclxuXHRcdHN0YXRpYyB0ZW1wbGF0ZV9pZCAgICAgICAgICAgID0gbnVsbDsgICAgICAgICAgICAgICAgIC8vIG1haW4gcHJldmlldyB0ZW1wbGF0ZSBpZFxyXG5cdFx0c3RhdGljIG9wdGlvbl9yb3dfdGVtcGxhdGVfaWQgPSAnd3BiYy1iZmItaW5zcGVjdG9yLXNlbGVjdC1vcHRpb24tcm93JzsgLy8gcm93IHRwbCBpZFxyXG5cdFx0c3RhdGljIGtpbmQgICAgICAgICAgICAgICAgICAgPSAnc2VsZWN0JztcclxuXHRcdHN0YXRpYyBfX3Jvb3Rfd2lyZWQgICAgICAgICAgID0gZmFsc2U7XHJcblx0XHRzdGF0aWMgX19yb290X25vZGUgICAgICAgICAgICA9IG51bGw7XHJcblxyXG5cdFx0Ly8gU2luZ2xlIHNvdXJjZSBvZiBzZWxlY3RvcnMgdXNlZCBieSB0aGUgaW5zcGVjdG9yIFVJLlxyXG5cdFx0c3RhdGljIHVpID0ge1xyXG5cdFx0XHRsaXN0ICAgOiAnLndwYmNfYmZiX19vcHRpb25zX2xpc3QnLFxyXG5cdFx0XHRob2xkZXIgOiAnLndwYmNfYmZiX19vcHRpb25zX3N0YXRlW2RhdGEtaW5zcGVjdG9yLWtleT1cIm9wdGlvbnNcIl0nLFxyXG5cdFx0XHRyb3cgICAgOiAnLndwYmNfYmZiX19vcHRpb25zX3JvdycsXHJcblx0XHRcdGxhYmVsICA6ICcud3BiY19iZmJfX29wdC1sYWJlbCcsXHJcblx0XHRcdHZhbHVlICA6ICcud3BiY19iZmJfX29wdC12YWx1ZScsXHJcblx0XHRcdHRvZ2dsZSA6ICcud3BiY19iZmJfX29wdC1zZWxlY3RlZC1jaGsnLFxyXG5cdFx0XHRhZGRfYnRuOiAnLmpzLWFkZC1vcHRpb24nLFxyXG5cclxuXHRcdFx0ZHJhZ19oYW5kbGUgICAgICA6ICcud3BiY19iZmJfX2RyYWctaGFuZGxlJyxcclxuXHRcdFx0bXVsdGlwbGVfY2hrICAgICA6ICcuanMtb3B0LW11bHRpcGxlW2RhdGEtaW5zcGVjdG9yLWtleT1cIm11bHRpcGxlXCJdJyxcclxuXHRcdFx0ZGVmYXVsdF90ZXh0ICAgICA6ICcuanMtZGVmYXVsdC12YWx1ZVtkYXRhLWluc3BlY3Rvci1rZXk9XCJkZWZhdWx0X3ZhbHVlXCJdJyxcclxuXHRcdFx0cGxhY2Vob2xkZXJfaW5wdXQ6ICcuanMtcGxhY2Vob2xkZXJbZGF0YS1pbnNwZWN0b3Ita2V5PVwicGxhY2Vob2xkZXJcIl0nLFxyXG5cdFx0XHRwbGFjZWhvbGRlcl9ub3RlIDogJy5qcy1wbGFjZWhvbGRlci1ub3RlJyxcclxuXHRcdFx0c2l6ZV9pbnB1dCAgICAgICA6ICcuaW5zcGVjdG9yX19pbnB1dFtkYXRhLWluc3BlY3Rvci1rZXk9XCJzaXplXCJdJyxcclxuXHJcblx0XHRcdC8vIERyb3Bkb3duIG1lbnUgaW50ZWdyYXRpb24uXHJcblx0XHRcdG1lbnVfcm9vdCAgOiAnLndwYmNfdWlfZWxfX2Ryb3Bkb3duJyxcclxuXHRcdFx0bWVudV90b2dnbGU6ICdbZGF0YS10b2dnbGU9XCJ3cGJjX2Ryb3Bkb3duXCJdJyxcclxuXHRcdFx0bWVudV9hY3Rpb246ICcudWxfZHJvcGRvd25fbWVudV9saV9hY3Rpb25bZGF0YS1hY3Rpb25dJyxcclxuXHRcdFx0Ly8gVmFsdWUtZGlmZmVycyB0b2dnbGUuXHJcblx0XHRcdHZhbHVlX2RpZmZlcnNfY2hrOiAnLmpzLXZhbHVlLWRpZmZlcnNbZGF0YS1pbnNwZWN0b3Ita2V5PVwidmFsdWVfZGlmZmVyc1wiXScsXHJcblx0XHR9O1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQnVpbGQgb3B0aW9uIHZhbHVlIGZyb20gbGFiZWwuXHJcblx0XHQgKiAtIElmIGBkaWZmZXJzID09PSB0cnVlYCAtPiBnZW5lcmF0ZSB0b2tlbiAoc2x1Zy1saWtlIG1hY2hpbmUgdmFsdWUpLlxyXG5cdFx0ICogLSBJZiBgZGlmZmVycyA9PT0gZmFsc2VgIC0+IGtlZXAgaHVtYW4gdGV4dDsgZXNjYXBlIG9ubHkgZGFuZ2Vyb3VzIGNoYXJzLlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGxhYmVsXHJcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IGRpZmZlcnNcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBidWlsZF92YWx1ZV9mcm9tX2xhYmVsKGxhYmVsLCBkaWZmZXJzKSB7XHJcblx0XHRcdGNvbnN0IFMgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplO1xyXG5cdFx0XHRpZiAoIGRpZmZlcnMgKSB7XHJcblx0XHRcdFx0cmV0dXJuIChTICYmIHR5cGVvZiBTLnRvX3Rva2VuID09PSAnZnVuY3Rpb24nKVxyXG5cdFx0XHRcdFx0PyBTLnRvX3Rva2VuKCBTdHJpbmcoIGxhYmVsIHx8ICcnICkgKVxyXG5cdFx0XHRcdFx0OiBTdHJpbmcoIGxhYmVsIHx8ICcnICkudHJpbSgpLnRvTG93ZXJDYXNlKCkucmVwbGFjZSggL1xccysvZywgJ18nICkucmVwbGFjZSggL1teXFx3LV0vZywgJycgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHQvLyBzaW5nbGUtaW5wdXQgbW9kZToga2VlcCBodW1hbiB0ZXh0OyB0ZW1wbGF0ZSB3aWxsIGVzY2FwZSBzYWZlbHkuXHJcblx0XHRcdHJldHVybiBTdHJpbmcoIGxhYmVsID09IG51bGwgPyAnJyA6IGxhYmVsICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBJcyB0aGUg4oCcdmFsdWUgZGlmZmVycyBmcm9tIGxhYmVs4oCdIHRvZ2dsZSBlbmFibGVkP1xyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcGFuZWxcclxuXHRcdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgaXNfdmFsdWVfZGlmZmVyc19lbmFibGVkKHBhbmVsKSB7XHJcblx0XHRcdGNvbnN0IGNoayA9IHBhbmVsPy5xdWVyeVNlbGVjdG9yKCB0aGlzLnVpLnZhbHVlX2RpZmZlcnNfY2hrICk7XHJcblx0XHRcdHJldHVybiAhIShjaGsgJiYgY2hrLmNoZWNrZWQpO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRW5zdXJlIHZpc2liaWxpdHkvZW5hYmxlZCBzdGF0ZSBvZiBWYWx1ZSBpbnB1dHMgYmFzZWQgb24gdGhlIHRvZ2dsZS5cclxuXHRcdCAqIFdoZW4gZGlzYWJsZWQgLT4gaGlkZSBWYWx1ZSBpbnB1dHMgYW5kIGtlZXAgdGhlbSBtaXJyb3JlZCBmcm9tIExhYmVsLlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcGFuZWxcclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgc3luY192YWx1ZV9pbnB1dHNfdmlzaWJpbGl0eShwYW5lbCkge1xyXG5cdFx0XHRjb25zdCBkaWZmZXJzID0gdGhpcy5pc192YWx1ZV9kaWZmZXJzX2VuYWJsZWQoIHBhbmVsICk7XHJcblx0XHRcdGNvbnN0IHJvd3MgICAgPSBwYW5lbD8ucXVlcnlTZWxlY3RvckFsbCggdGhpcy51aS5yb3cgKSB8fCBbXTtcclxuXHJcblx0XHRcdGZvciAoIGxldCBpID0gMDsgaSA8IHJvd3MubGVuZ3RoOyBpKysgKSB7XHJcblx0XHRcdFx0Y29uc3QgciAgICAgID0gcm93c1tpXTtcclxuXHRcdFx0XHRjb25zdCBsYmxfaW4gPSByLnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkubGFiZWwgKTtcclxuXHRcdFx0XHRjb25zdCB2YWxfaW4gPSByLnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkudmFsdWUgKTtcclxuXHRcdFx0XHRpZiAoICF2YWxfaW4gKSBjb250aW51ZTtcclxuXHJcblx0XHRcdFx0aWYgKCBkaWZmZXJzICkge1xyXG5cdFx0XHRcdFx0Ly8gUmUtZW5hYmxlICYgc2hvdyB2YWx1ZSBpbnB1dFxyXG5cdFx0XHRcdFx0dmFsX2luLnJlbW92ZUF0dHJpYnV0ZSggJ2Rpc2FibGVkJyApO1xyXG5cdFx0XHRcdFx0dmFsX2luLnN0eWxlLmRpc3BsYXkgPSAnJztcclxuXHJcblx0XHRcdFx0XHQvLyBJZiB3ZSBoYXZlIGEgY2FjaGVkIGN1c3RvbSB2YWx1ZSBhbmQgdGhlIHJvdyB3YXNuJ3QgZWRpdGVkIHdoaWxlIE9GRiwgcmVzdG9yZSBpdFxyXG5cdFx0XHRcdFx0Y29uc3QgaGFzQ2FjaGUgICA9ICEhdmFsX2luLmRhdGFzZXQuY2FjaGVkX3ZhbHVlO1xyXG5cdFx0XHRcdFx0Y29uc3QgdXNlckVkaXRlZCA9IHIuZGF0YXNldC52YWx1ZV91c2VyX3RvdWNoZWQgPT09ICcxJztcclxuXHJcblx0XHRcdFx0XHRpZiAoIGhhc0NhY2hlICYmICF1c2VyRWRpdGVkICkge1xyXG5cdFx0XHRcdFx0XHR2YWxfaW4udmFsdWUgPSB2YWxfaW4uZGF0YXNldC5jYWNoZWRfdmFsdWU7XHJcblx0XHRcdFx0XHR9IGVsc2UgaWYgKCAhaGFzQ2FjaGUgKSB7XHJcblx0XHRcdFx0XHRcdC8vIE5vIGNhY2hlOiBpZiB2YWx1ZSBpcyBqdXN0IGEgbWlycm9yZWQgbGFiZWwsIG9mZmVyIGEgdG9rZW5pemVkIGRlZmF1bHRcclxuXHRcdFx0XHRcdFx0Y29uc3QgbGJsICAgICAgPSBsYmxfaW4gPyBsYmxfaW4udmFsdWUgOiAnJztcclxuXHRcdFx0XHRcdFx0Y29uc3QgbWlycm9yZWQgPSB0aGlzLmJ1aWxkX3ZhbHVlX2Zyb21fbGFiZWwoIGxibCwgLypkaWZmZXJzPSovZmFsc2UgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCB2YWxfaW4udmFsdWUgPT09IG1pcnJvcmVkICkge1xyXG5cdFx0XHRcdFx0XHRcdHZhbF9pbi52YWx1ZSA9IHRoaXMuYnVpbGRfdmFsdWVfZnJvbV9sYWJlbCggbGJsLCAvKmRpZmZlcnM9Ki90cnVlICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0Ly8gT04gLT4gT0ZGOiBjYWNoZSBvbmNlLCB0aGVuIG1pcnJvclxyXG5cdFx0XHRcdFx0aWYgKCAhdmFsX2luLmRhdGFzZXQuY2FjaGVkX3ZhbHVlICkge1xyXG5cdFx0XHRcdFx0XHR2YWxfaW4uZGF0YXNldC5jYWNoZWRfdmFsdWUgPSB2YWxfaW4udmFsdWUgfHwgJyc7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRjb25zdCBsYmwgICAgPSBsYmxfaW4gPyBsYmxfaW4udmFsdWUgOiAnJztcclxuXHRcdFx0XHRcdHZhbF9pbi52YWx1ZSA9IHRoaXMuYnVpbGRfdmFsdWVfZnJvbV9sYWJlbCggbGJsLCAvKmRpZmZlcnM9Ki9mYWxzZSApO1xyXG5cclxuXHRcdFx0XHRcdHZhbF9pbi5zZXRBdHRyaWJ1dGUoICdkaXNhYmxlZCcsICdkaXNhYmxlZCcgKTtcclxuXHRcdFx0XHRcdHZhbF9pbi5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xyXG5cdFx0XHRcdFx0Ly8gTk9URTogZG8gTk9UIG1hcmsgYXMgdXNlcl90b3VjaGVkIGhlcmVcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZXR1cm4gd2hldGhlciB0aGlzIHJvd+KAmXMgdmFsdWUgaGFzIGJlZW4gZWRpdGVkIGJ5IHVzZXIuXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSByb3dcclxuXHRcdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgaXNfcm93X3ZhbHVlX3VzZXJfdG91Y2hlZChyb3cpIHtcclxuXHRcdFx0cmV0dXJuIHJvdz8uZGF0YXNldD8udmFsdWVfdXNlcl90b3VjaGVkID09PSAnMSc7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBNYXJrIHRoaXMgcm934oCZcyB2YWx1ZSBhcyBlZGl0ZWQgYnkgdXNlci5cclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvd1xyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgbWFya19yb3dfdmFsdWVfdXNlcl90b3VjaGVkKHJvdykge1xyXG5cdFx0XHRpZiAoIHJvdyApIHJvdy5kYXRhc2V0LnZhbHVlX3VzZXJfdG91Y2hlZCA9ICcxJztcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEluaXRpYWxpemUg4oCcZnJlc2huZXNz4oCdIGZsYWdzIG9uIGEgcm93ICh2YWx1ZSB1bnRvdWNoZWQpLlxyXG5cdFx0ICogQ2FsbCBvbiBjcmVhdGlvbi9hcHBlbmQgb2Ygcm93cy5cclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvd1xyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgaW5pdF9yb3dfZnJlc2hfZmxhZ3Mocm93KSB7XHJcblx0XHRcdGlmICggcm93ICkge1xyXG5cdFx0XHRcdGlmICggIXJvdy5kYXRhc2V0LnZhbHVlX3VzZXJfdG91Y2hlZCApIHtcclxuXHRcdFx0XHRcdHJvdy5kYXRhc2V0LnZhbHVlX3VzZXJfdG91Y2hlZCA9ICcwJztcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyAtLS0tIGRlZmF1bHRzIChwYWNrcyBjYW4gb3ZlcnJpZGUpIC0tLS1cclxuXHRcdHN0YXRpYyBnZXRfZGVmYXVsdHMoKSB7XHJcblx0XHRcdHJldHVybiB7XHJcblx0XHRcdFx0dHlwZSAgICAgICAgIDogdGhpcy5raW5kLFxyXG5cdFx0XHRcdGxhYmVsICAgICAgICA6ICdTZWxlY3QnLFxyXG5cdFx0XHRcdG5hbWUgICAgICAgICA6ICcnLFxyXG5cdFx0XHRcdGh0bWxfaWQgICAgICA6ICcnLFxyXG5cdFx0XHRcdHBsYWNlaG9sZGVyICA6ICctLS0gU2VsZWN0IC0tLScsXHJcblx0XHRcdFx0cmVxdWlyZWQgICAgIDogZmFsc2UsXHJcblx0XHRcdFx0bXVsdGlwbGUgICAgIDogZmFsc2UsXHJcblx0XHRcdFx0c2l6ZSAgICAgICAgIDogbnVsbCxcclxuXHRcdFx0XHRjc3NjbGFzcyAgICAgOiAnJyxcclxuXHRcdFx0XHRoZWxwICAgICAgICAgOiAnJyxcclxuXHRcdFx0XHRkZWZhdWx0X3ZhbHVlOiAnJyxcclxuXHRcdFx0XHRvcHRpb25zICAgICAgOiBbXHJcblx0XHRcdFx0XHR7IGxhYmVsOiAnT3B0aW9uIDEnLCB2YWx1ZTogJ09wdGlvbiAxJywgc2VsZWN0ZWQ6IGZhbHNlIH0sXHJcblx0XHRcdFx0XHR7IGxhYmVsOiAnT3B0aW9uIDInLCB2YWx1ZTogJ09wdGlvbiAyJywgc2VsZWN0ZWQ6IGZhbHNlIH0sXHJcblx0XHRcdFx0XHR7IGxhYmVsOiAnT3B0aW9uIDMnLCB2YWx1ZTogJ09wdGlvbiAzJywgc2VsZWN0ZWQ6IGZhbHNlIH0sXHJcblx0XHRcdFx0XHR7IGxhYmVsOiAnT3B0aW9uIDQnLCB2YWx1ZTogJ09wdGlvbiA0Jywgc2VsZWN0ZWQ6IGZhbHNlIH1cclxuXHRcdFx0XHRdLFxyXG5cdFx0XHRcdG1pbl93aWR0aCAgICA6ICcyNDBweCdcclxuXHRcdFx0fTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyAtLS0tIHByZXZpZXcgcmVuZGVyIChpZGVtcG90ZW50KSAtLS0tXHJcblx0XHRzdGF0aWMgcmVuZGVyKGVsLCBkYXRhLCBjdHgpIHtcclxuXHRcdFx0aWYgKCAhZWwgKSByZXR1cm47XHJcblxyXG5cdFx0XHRjb25zdCBkID0gdGhpcy5ub3JtYWxpemVfZGF0YSggZGF0YSApO1xyXG5cclxuXHRcdFx0aWYgKCBkLm1pbl93aWR0aCAhPSBudWxsICkge1xyXG5cdFx0XHRcdGVsLmRhdGFzZXQubWluX3dpZHRoID0gU3RyaW5nKCBkLm1pbl93aWR0aCApO1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRlbC5zdHlsZS5zZXRQcm9wZXJ0eSggJy0td3BiYy1jb2wtbWluJywgU3RyaW5nKCBkLm1pbl93aWR0aCApICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggZC5odG1sX2lkICE9IG51bGwgKSBlbC5kYXRhc2V0Lmh0bWxfaWQgPSBTdHJpbmcoIGQuaHRtbF9pZCB8fCAnJyApO1xyXG5cdFx0XHRpZiAoIGQuY3NzY2xhc3MgIT0gbnVsbCApIGVsLmRhdGFzZXQuY3NzY2xhc3MgPSBTdHJpbmcoIGQuY3NzY2xhc3MgfHwgJycgKTtcclxuXHRcdFx0aWYgKCBkLnBsYWNlaG9sZGVyICE9IG51bGwgKSBlbC5kYXRhc2V0LnBsYWNlaG9sZGVyID0gU3RyaW5nKCBkLnBsYWNlaG9sZGVyIHx8ICcnICk7XHJcblxyXG5cdFx0XHRjb25zdCB0cGwgPSB0aGlzLmdldF90ZW1wbGF0ZSggdGhpcy50ZW1wbGF0ZV9pZCApO1xyXG5cdFx0XHRpZiAoIHR5cGVvZiB0cGwgIT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0ZWwuaW5uZXJIVE1MID0gJzxkaXYgY2xhc3M9XCJ3cGJjX2JmYl9fZXJyb3JcIiByb2xlPVwiYWxlcnRcIj5UZW1wbGF0ZSBub3QgZm91bmQ6ICcgKyB0aGlzLnRlbXBsYXRlX2lkICsgJy48L2Rpdj4nO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRlbC5pbm5lckhUTUwgPSB0cGwoIGQgKTtcclxuXHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0d2luZG93Ll93cGJjPy5kZXY/LmVycm9yPy4oICdTZWxlY3RfQmFzZS5yZW5kZXInLCBlICk7XHJcblx0XHRcdFx0ZWwuaW5uZXJIVE1MID0gJzxkaXYgY2xhc3M9XCJ3cGJjX2JmYl9fZXJyb3JcIiByb2xlPVwiYWxlcnRcIj5FcnJvciByZW5kZXJpbmcgZmllbGQgcHJldmlldy48L2Rpdj4nO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0ZWwuZGF0YXNldC50eXBlID0gZC50eXBlIHx8IHRoaXMua2luZDtcclxuXHRcdFx0ZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1sYWJlbCcsIChkLmxhYmVsICE9IG51bGwgPyBTdHJpbmcoIGQubGFiZWwgKSA6ICcnKSApO1xyXG5cclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRDb3JlLlVJPy5XUEJDX0JGQl9PdmVybGF5Py5lbnN1cmU/LiggY3R4Py5idWlsZGVyLCBlbCApO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCAhZWwuZGF0YXNldC5vcHRpb25zICYmIEFycmF5LmlzQXJyYXkoIGQub3B0aW9ucyApICYmIGQub3B0aW9ucy5sZW5ndGggKSB7XHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdGVsLmRhdGFzZXQub3B0aW9ucyA9IEpTT04uc3RyaW5naWZ5KCBkLm9wdGlvbnMgKTtcclxuXHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyAtLS0tIGRyb3Agc2VlZGluZyAob3B0aW9ucyArIHBsYWNlaG9sZGVyKSAtLS0tXHJcblx0XHRzdGF0aWMgb25fZmllbGRfZHJvcChkYXRhLCBlbCwgbWV0YSkge1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdHN1cGVyLm9uX2ZpZWxkX2Ryb3A/LiggZGF0YSwgZWwsIG1ldGEgKTtcclxuXHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGNvbnN0IGlzX2Ryb3AgPSAobWV0YSAmJiBtZXRhLmNvbnRleHQgPT09ICdkcm9wJyk7XHJcblxyXG5cdFx0XHRpZiAoIGlzX2Ryb3AgKSB7XHJcblx0XHRcdFx0aWYgKCAhQXJyYXkuaXNBcnJheSggZGF0YS5vcHRpb25zICkgfHwgIWRhdGEub3B0aW9ucy5sZW5ndGggKSB7XHJcblx0XHRcdFx0XHRjb25zdCBvcHRzICAgPSAodGhpcy5nZXRfZGVmYXVsdHMoKS5vcHRpb25zIHx8IFtdKS5tYXAoIChvKSA9PiAoe1xyXG5cdFx0XHRcdFx0XHRsYWJlbCAgIDogby5sYWJlbCxcclxuXHRcdFx0XHRcdFx0dmFsdWUgICA6IG8udmFsdWUsXHJcblx0XHRcdFx0XHRcdHNlbGVjdGVkOiAhIW8uc2VsZWN0ZWRcclxuXHRcdFx0XHRcdH0pICk7XHJcblx0XHRcdFx0XHRkYXRhLm9wdGlvbnMgPSBvcHRzO1xyXG5cdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0ZWwuZGF0YXNldC5vcHRpb25zID0gSlNPTi5zdHJpbmdpZnkoIG9wdHMgKTtcclxuXHRcdFx0XHRcdFx0ZWwuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmJfZmllbGRfZGF0YV9jaGFuZ2VkJywgeyBidWJibGVzOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdGRldGFpbCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRrZXkgIDogJ29wdGlvbnMnLFxyXG5cdFx0XHRcdFx0XHRcdFx0dmFsdWU6IG9wdHNcclxuXHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdH0gKSApO1xyXG5cdFx0XHRcdFx0XHRDb3JlLlN0cnVjdHVyZT8udXBkYXRlX2ZpZWxkX3Byb3A/LiggZWwsICdvcHRpb25zJywgb3B0cyApO1xyXG5cdFx0XHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRjb25zdCBwaCA9IChkYXRhLnBsYWNlaG9sZGVyID8/ICcnKS50b1N0cmluZygpLnRyaW0oKTtcclxuXHRcdFx0XHRpZiAoICFwaCApIHtcclxuXHRcdFx0XHRcdGNvbnN0IGRmbHQgICAgICAgPSB0aGlzLmdldF9kZWZhdWx0cygpLnBsYWNlaG9sZGVyIHx8ICctLS0gU2VsZWN0IC0tLSc7XHJcblx0XHRcdFx0XHRkYXRhLnBsYWNlaG9sZGVyID0gZGZsdDtcclxuXHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdGVsLmRhdGFzZXQucGxhY2Vob2xkZXIgPSBTdHJpbmcoIGRmbHQgKTtcclxuXHRcdFx0XHRcdFx0ZWwuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmJfZmllbGRfZGF0YV9jaGFuZ2VkJywgeyBidWJibGVzOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdGRldGFpbCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRrZXkgIDogJ3BsYWNlaG9sZGVyJyxcclxuXHRcdFx0XHRcdFx0XHRcdHZhbHVlOiBkZmx0XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHR9ICkgKTtcclxuXHRcdFx0XHRcdFx0Q29yZS5TdHJ1Y3R1cmU/LnVwZGF0ZV9maWVsZF9wcm9wPy4oIGVsLCAncGxhY2Vob2xkZXInLCBkZmx0ICk7XHJcblx0XHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuXHRcdC8vIEluc3BlY3RvciBoZWxwZXJzIChzbmFrZV9jYXNlKVxyXG5cdFx0Ly8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblx0XHRzdGF0aWMgZ2V0X3BhbmVsX3Jvb3QoZWwpIHtcclxuXHRcdFx0cmV0dXJuIGVsPy5jbG9zZXN0Py4oICcud3BiY19iZmJfX2luc3BlY3Rvcl9fYm9keScgKSB8fCBlbD8uY2xvc2VzdD8uKCAnLndwYmNfYmZiX19pbnNwZWN0b3InICkgfHwgbnVsbDtcclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgZ2V0X2xpc3QocGFuZWwpIHtcclxuXHRcdFx0cmV0dXJuIHBhbmVsID8gcGFuZWwucXVlcnlTZWxlY3RvciggdGhpcy51aS5saXN0ICkgOiBudWxsO1xyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyBnZXRfaG9sZGVyKHBhbmVsKSB7XHJcblx0XHRcdHJldHVybiBwYW5lbCA/IHBhbmVsLnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkuaG9sZGVyICkgOiBudWxsO1xyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyBtYWtlX3VpZCgpIHtcclxuXHRcdFx0cmV0dXJuICd3cGJjX2luc19hdXRvX29wdF8nICsgTWF0aC5yYW5kb20oKS50b1N0cmluZyggMzYgKS5zbGljZSggMiwgMTAgKTtcclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgYXBwZW5kX3JvdyhwYW5lbCwgZGF0YSkge1xyXG5cdFx0XHRjb25zdCBsaXN0ID0gdGhpcy5nZXRfbGlzdCggcGFuZWwgKTtcclxuXHRcdFx0aWYgKCAhbGlzdCApIHJldHVybjtcclxuXHJcblx0XHRcdGNvbnN0IGlkeCAgPSBsaXN0LmNoaWxkcmVuLmxlbmd0aDtcclxuXHRcdFx0Y29uc3Qgcm93ZCA9IE9iamVjdC5hc3NpZ24oIHsgbGFiZWw6ICcnLCB2YWx1ZTogJycsIHNlbGVjdGVkOiBmYWxzZSwgaW5kZXg6IGlkeCB9LCAoZGF0YSB8fCB7fSkgKTtcclxuXHRcdFx0aWYgKCAhcm93ZC51aWQgKSByb3dkLnVpZCA9IHRoaXMubWFrZV91aWQoKTtcclxuXHJcblx0XHRcdGNvbnN0IHRwbF9pZCA9IHRoaXMub3B0aW9uX3Jvd190ZW1wbGF0ZV9pZDtcclxuXHRcdFx0Y29uc3QgdHBsICAgID0gKHdpbmRvdy53cCAmJiB3cC50ZW1wbGF0ZSkgPyB3cC50ZW1wbGF0ZSggdHBsX2lkICkgOiBudWxsO1xyXG5cdFx0XHRjb25zdCBodG1sICAgPSB0cGwgPyB0cGwoIHJvd2QgKSA6IG51bGw7XHJcblxyXG5cdFx0XHQvLyBJbiBhcHBlbmRfcm93KCkgLT4gZmFsbGJhY2sgSFRNTC5cclxuXHRcdFx0Y29uc3Qgd3JhcCAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xyXG5cdFx0XHR3cmFwLmlubmVySFRNTCA9IGh0bWwgfHwgKFxyXG5cdFx0XHRcdCc8ZGl2IGNsYXNzPVwid3BiY19iZmJfX29wdGlvbnNfcm93XCIgZGF0YS1pbmRleD1cIicgKyAocm93ZC5pbmRleCB8fCAwKSArICdcIj4nICtcclxuXHRcdFx0XHRcdCc8c3BhbiBjbGFzcz1cIndwYmNfYmZiX19kcmFnLWhhbmRsZVwiPjxzcGFuIGNsYXNzPVwid3BiY19pY25fZHJhZ19pbmRpY2F0b3JcIj48L3NwYW4+PC9zcGFuPicgK1xyXG5cdFx0XHRcdFx0JzxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwid3BiY19iZmJfX29wdC1sYWJlbFwiIHBsYWNlaG9sZGVyPVwiTGFiZWxcIiB2YWx1ZT1cIicgKyAocm93ZC5sYWJlbCB8fCAnJykgKyAnXCI+JyArXHJcblx0XHRcdFx0XHQnPGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJ3cGJjX2JmYl9fb3B0LXZhbHVlXCIgcGxhY2Vob2xkZXI9XCJWYWx1ZVwiIHZhbHVlPVwiJyArIChyb3dkLnZhbHVlIHx8ICcnKSArICdcIj4nICtcclxuXHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwid3BiY19iZmJfX29wdC1zZWxlY3RlZFwiPicgK1xyXG5cdFx0XHRcdFx0XHQnPGRpdiBjbGFzcz1cImluc3BlY3Rvcl9fY29udHJvbCB3cGJjX3VpX190b2dnbGVcIj4nICtcclxuXHRcdFx0XHRcdFx0XHQnPGlucHV0IHR5cGU9XCJjaGVja2JveFwiIGNsYXNzPVwid3BiY19iZmJfX29wdC1zZWxlY3RlZC1jaGsgaW5zcGVjdG9yX19pbnB1dFwiIGlkPVwiJyArIHJvd2QudWlkICsgJ1wiIHJvbGU9XCJzd2l0Y2hcIiAnICsgKHJvd2Quc2VsZWN0ZWQgPyAnY2hlY2tlZCBhcmlhLWNoZWNrZWQ9XCJ0cnVlXCInIDogJ2FyaWEtY2hlY2tlZD1cImZhbHNlXCInKSArICc+JyArXHJcblx0XHRcdFx0XHRcdFx0JzxsYWJlbCBjbGFzcz1cIndwYmNfdWlfX3RvZ2dsZV9pY29uX3JhZGlvXCIgZm9yPVwiJyArIHJvd2QudWlkICsgJ1wiPjwvbGFiZWw+JyArXHJcblx0XHRcdFx0XHRcdFx0JzxsYWJlbCBjbGFzcz1cIndwYmNfdWlfX3RvZ2dsZV9sYWJlbFwiIGZvcj1cIicgKyByb3dkLnVpZCArICdcIj5EZWZhdWx0PC9sYWJlbD4nICtcclxuXHRcdFx0XHRcdFx0JzwvZGl2PicgK1xyXG5cdFx0XHRcdFx0JzwvZGl2PicgK1xyXG5cdFx0XHRcdFx0Ly8gMy1kb3QgZHJvcGRvd24gKHVzZXMgZXhpc3RpbmcgcGx1Z2luIGRyb3Bkb3duIEpTKS5cclxuXHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwid3BiY191aV9lbCB3cGJjX3VpX2VsX2NvbnRhaW5lciB3cGJjX3VpX2VsX19kcm9wZG93blwiPicgK1xyXG5cdFx0XHRcdFx0XHQnPGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiIGRhdGEtdG9nZ2xlPVwid3BiY19kcm9wZG93blwiIGFyaWEtZXhwYW5kZWQ9XCJmYWxzZVwiIGNsYXNzPVwidWxfZHJvcGRvd25fbWVudV90b2dnbGVcIj4nICtcclxuXHRcdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9tb3JlX3ZlcnRcIj48L2k+JyArXHJcblx0XHRcdFx0XHRcdCc8L2E+JyArXHJcblx0XHRcdFx0XHRcdCc8dWwgY2xhc3M9XCJ1bF9kcm9wZG93bl9tZW51XCIgcm9sZT1cIm1lbnVcIiBzdHlsZT1cInJpZ2h0OjBweDsgbGVmdDphdXRvO1wiPicgK1xyXG5cdFx0XHRcdFx0XHRcdCc8bGk+JyArXHJcblx0XHRcdFx0XHRcdFx0XHQnPGEgY2xhc3M9XCJ1bF9kcm9wZG93bl9tZW51X2xpX2FjdGlvblwiIGRhdGEtYWN0aW9uPVwiYWRkX2FmdGVyXCIgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnQWRkIE5ldycgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9hZGRfY2lyY2xlXCI+PC9pPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0JzwvYT4nICtcclxuXHRcdFx0XHRcdFx0XHQnPC9saT4nICtcclxuXHRcdFx0XHRcdFx0XHQnPGxpPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0JzxhIGNsYXNzPVwidWxfZHJvcGRvd25fbWVudV9saV9hY3Rpb25cIiBkYXRhLWFjdGlvbj1cImR1cGxpY2F0ZVwiIGhyZWY9XCJqYXZhc2NyaXB0OnZvaWQoMClcIj4nICtcclxuXHRcdFx0XHRcdFx0XHRcdFx0J0R1cGxpY2F0ZScgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9jb250ZW50X2NvcHlcIj48L2k+JyArXHJcblx0XHRcdFx0XHRcdFx0XHQnPC9hPicgK1xyXG5cdFx0XHRcdFx0XHRcdCc8L2xpPicgK1xyXG5cdFx0XHRcdFx0XHRcdCc8bGkgY2xhc3M9XCJkaXZpZGVyXCI+PC9saT4nICtcclxuXHRcdFx0XHRcdFx0XHQnPGxpPicgK1xyXG5cdFx0XHRcdFx0XHRcdFx0JzxhIGNsYXNzPVwidWxfZHJvcGRvd25fbWVudV9saV9hY3Rpb25cIiBkYXRhLWFjdGlvbj1cInJlbW92ZVwiIGhyZWY9XCJqYXZhc2NyaXB0OnZvaWQoMClcIj4nICtcclxuXHRcdFx0XHRcdFx0XHRcdFx0J1JlbW92ZScgK1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9kZWxldGVfb3V0bGluZVwiPjwvaT4nICtcclxuXHRcdFx0XHRcdFx0XHRcdCc8L2E+JyArXHJcblx0XHRcdFx0XHRcdFx0JzwvbGk+JyArXHJcblx0XHRcdFx0XHRcdCc8L3VsPicgK1xyXG5cdFx0XHRcdFx0JzwvZGl2PicgK1xyXG5cdFx0XHRcdCc8L2Rpdj4nXHJcblx0XHRcdCk7XHJcblxyXG5cdFx0XHRjb25zdCBub2RlID0gd3JhcC5maXJzdEVsZW1lbnRDaGlsZDtcclxuXHRcdFx0IGlmICghIG5vZGUpIHtcclxuXHRcdFx0XHQgcmV0dXJuO1xyXG5cdFx0XHQgfVxyXG5cdFx0XHQvLyBwcmUtaGlkZSBWYWx1ZSBpbnB1dCBpZiB0b2dnbGUgaXMgT0ZGICoqYmVmb3JlKiogYXBwZW5kaW5nLlxyXG5cdFx0XHRjb25zdCBkaWZmZXJzID0gdGhpcy5pc192YWx1ZV9kaWZmZXJzX2VuYWJsZWQoIHBhbmVsICk7XHJcblx0XHRcdGNvbnN0IHZhbEluICAgPSBub2RlLnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkudmFsdWUgKTtcclxuXHRcdFx0Y29uc3QgbGJsSW4gICA9IG5vZGUucXVlcnlTZWxlY3RvciggdGhpcy51aS5sYWJlbCApO1xyXG5cclxuXHRcdFx0aWYgKCAhZGlmZmVycyAmJiB2YWxJbiApIHtcclxuXHRcdFx0XHRpZiAoICF2YWxJbi5kYXRhc2V0LmNhY2hlZF92YWx1ZSApIHtcclxuXHRcdFx0XHRcdHZhbEluLmRhdGFzZXQuY2FjaGVkX3ZhbHVlID0gdmFsSW4udmFsdWUgfHwgJyc7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGlmICggbGJsSW4gKSB2YWxJbi52YWx1ZSA9IHRoaXMuYnVpbGRfdmFsdWVfZnJvbV9sYWJlbCggbGJsSW4udmFsdWUsIGZhbHNlICk7XHJcblx0XHRcdFx0dmFsSW4uc2V0QXR0cmlidXRlKCAnZGlzYWJsZWQnLCAnZGlzYWJsZWQnICk7XHJcblx0XHRcdFx0dmFsSW4uc3R5bGUuZGlzcGxheSA9ICdub25lJztcclxuXHRcdFx0fVxyXG5cclxuXHJcblx0XHRcdHRoaXMuaW5pdF9yb3dfZnJlc2hfZmxhZ3MoIG5vZGUgKTtcclxuXHRcdFx0bGlzdC5hcHBlbmRDaGlsZCggbm9kZSApO1xyXG5cclxuXHRcdFx0Ly8gS2VlcCB5b3VyIGV4aXN0aW5nIHBvc3QtYXBwZW5kIHN5bmMgYXMgYSBzYWZldHkgbmV0XHJcblx0XHRcdHRoaXMuc3luY192YWx1ZV9pbnB1dHNfdmlzaWJpbGl0eSggcGFuZWwgKTtcclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgY2xvc2VfZHJvcGRvd24oYW5jaG9yX2VsKSB7XHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0dmFyIHJvb3QgPSBhbmNob3JfZWw/LmNsb3Nlc3Q/LiggdGhpcy51aS5tZW51X3Jvb3QgKTtcclxuXHRcdFx0XHRpZiAoIHJvb3QgKSB7XHJcblx0XHRcdFx0XHQvLyBJZiB5b3VyIGRyb3Bkb3duIHRvZ2dsZXIgdG9nZ2xlcyBhIGNsYXNzIGxpa2UgJ29wZW4nLCBjbG9zZSBpdC5cclxuXHRcdFx0XHRcdHJvb3QuY2xhc3NMaXN0LnJlbW92ZSggJ29wZW4nICk7XHJcblx0XHRcdFx0XHQvLyBPciBpZiBpdCByZWxpZXMgb24gYXJpYS1leHBhbmRlZCBvbiB0aGUgdG9nZ2xlLlxyXG5cdFx0XHRcdFx0dmFyIHQgPSByb290LnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkubWVudV90b2dnbGUgKTtcclxuXHRcdFx0XHRcdGlmICggdCApIHtcclxuXHRcdFx0XHRcdFx0dC5zZXRBdHRyaWJ1dGUoICdhcmlhLWV4cGFuZGVkJywgJ2ZhbHNlJyApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBjYXRjaCAoIF8gKSB7IH1cclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgaW5zZXJ0X2FmdGVyKG5ld19ub2RlLCByZWZfbm9kZSkge1xyXG5cdFx0XHRpZiAoIHJlZl9ub2RlPy5wYXJlbnROb2RlICkge1xyXG5cdFx0XHRcdGlmICggcmVmX25vZGUubmV4dFNpYmxpbmcgKSB7XHJcblx0XHRcdFx0XHRyZWZfbm9kZS5wYXJlbnROb2RlLmluc2VydEJlZm9yZSggbmV3X25vZGUsIHJlZl9ub2RlLm5leHRTaWJsaW5nICk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdHJlZl9ub2RlLnBhcmVudE5vZGUuYXBwZW5kQ2hpbGQoIG5ld19ub2RlICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0c3RhdGljIGNvbW1pdF9vcHRpb25zKHBhbmVsKSB7XHJcblx0XHRcdGNvbnN0IGxpc3QgICA9IHRoaXMuZ2V0X2xpc3QoIHBhbmVsICk7XHJcblx0XHRcdGNvbnN0IGhvbGRlciA9IHRoaXMuZ2V0X2hvbGRlciggcGFuZWwgKTtcclxuXHRcdFx0aWYgKCAhbGlzdCB8fCAhaG9sZGVyICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgZGlmZmVycyA9IHRoaXMuaXNfdmFsdWVfZGlmZmVyc19lbmFibGVkKCBwYW5lbCApO1xyXG5cclxuXHRcdFx0Y29uc3Qgcm93cyAgICA9IGxpc3QucXVlcnlTZWxlY3RvckFsbCggdGhpcy51aS5yb3cgKTtcclxuXHRcdFx0Y29uc3Qgb3B0aW9ucyA9IFtdO1xyXG5cdFx0XHRmb3IgKCBsZXQgaSA9IDA7IGkgPCByb3dzLmxlbmd0aDsgaSsrICkge1xyXG5cdFx0XHRcdGNvbnN0IHIgICAgICA9IHJvd3NbaV07XHJcblx0XHRcdFx0Y29uc3QgbGJsX2luID0gci5xdWVyeVNlbGVjdG9yKCB0aGlzLnVpLmxhYmVsICk7XHJcblx0XHRcdFx0Y29uc3QgdmFsX2luID0gci5xdWVyeVNlbGVjdG9yKCB0aGlzLnVpLnZhbHVlICk7XHJcblx0XHRcdFx0Y29uc3QgY2hrICAgID0gci5xdWVyeVNlbGVjdG9yKCB0aGlzLnVpLnRvZ2dsZSApO1xyXG5cclxuXHRcdFx0XHRjb25zdCBsYmwgPSAobGJsX2luICYmIGxibF9pbi52YWx1ZSkgfHwgJyc7XHJcblx0XHRcdFx0bGV0IHZhbCAgID0gKHZhbF9pbiAmJiB2YWxfaW4udmFsdWUpIHx8ICcnO1xyXG5cclxuXHRcdFx0XHQvLyBJZiBzaW5nbGUtaW5wdXQgbW9kZSAtPiBoYXJkIG1pcnJvciB0byBsYWJlbC5cclxuXHRcdFx0XHRpZiAoICEgZGlmZmVycyApIHtcclxuXHRcdFx0XHRcdC8vIHNpbmdsZS1pbnB1dCBtb2RlOiBtaXJyb3IgTGFiZWwsIG1pbmltYWwgZXNjYXBpbmcgKG5vIHNsdWcpLlxyXG5cdFx0XHRcdFx0dmFsID0gdGhpcy5idWlsZF92YWx1ZV9mcm9tX2xhYmVsKCBsYmwsIC8qZGlmZmVycz0qL2ZhbHNlICk7XHJcblx0XHRcdFx0XHRpZiAoIHZhbF9pbiApIHtcclxuXHRcdFx0XHRcdFx0dmFsX2luLnZhbHVlID0gdmFsOyAgIC8vIGtlZXAgaGlkZGVuIGlucHV0IGluIHN5bmMgZm9yIGFueSBwcmV2aWV3cy9kZWJ1Zy5cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGNvbnN0IHNlbCA9ICEhKGNoayAmJiBjaGsuY2hlY2tlZCk7XHJcblx0XHRcdFx0b3B0aW9ucy5wdXNoKCB7IGxhYmVsOiBsYmwsIHZhbHVlOiB2YWwsIHNlbGVjdGVkOiBzZWwgfSApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGhvbGRlci52YWx1ZSA9IEpTT04uc3RyaW5naWZ5KCBvcHRpb25zICk7XHJcblx0XHRcdFx0aG9sZGVyLmRpc3BhdGNoRXZlbnQoIG5ldyBFdmVudCggJ2lucHV0JywgeyBidWJibGVzOiB0cnVlIH0gKSApO1xyXG5cdFx0XHRcdGhvbGRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdjaGFuZ2UnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0cGFuZWwuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmJfZmllbGRfZGF0YV9jaGFuZ2VkJywge1xyXG5cdFx0XHRcdFx0YnViYmxlczogdHJ1ZSwgZGV0YWlsOiB7XHJcblx0XHRcdFx0XHRcdGtleTogJ29wdGlvbnMnLCB2YWx1ZTogb3B0aW9uc1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gKSApO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dGhpcy5zeW5jX2RlZmF1bHRfdmFsdWVfbG9jayggcGFuZWwgKTtcclxuXHRcdFx0dGhpcy5zeW5jX3BsYWNlaG9sZGVyX2xvY2soIHBhbmVsICk7XHJcblxyXG5cdFx0XHQvLyBNaXJyb3IgdG8gdGhlIHNlbGVjdGVkIGZpZWxkIGVsZW1lbnQgc28gY2FudmFzL2V4cG9ydCBzZWVzIGN1cnJlbnQgb3B0aW9ucyBpbW1lZGlhdGVseS5cclxuXHRcdFx0Y29uc3QgZmllbGQgPSBwYW5lbC5fX3NlbGVjdGJhc2VfZmllbGRcclxuXHRcdFx0XHR8fCBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19maWVsZC5pcy1zZWxlY3RlZCwgLndwYmNfYmZiX19maWVsZC0tc2VsZWN0ZWQnICk7XHJcblx0XHRcdGlmICggZmllbGQgKSB7XHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdGZpZWxkLmRhdGFzZXQub3B0aW9ucyA9IEpTT04uc3RyaW5naWZ5KCBvcHRpb25zICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdENvcmUuU3RydWN0dXJlPy51cGRhdGVfZmllbGRfcHJvcD8uKCBmaWVsZCwgJ29wdGlvbnMnLCBvcHRpb25zICk7XHJcblx0XHRcdFx0ZmllbGQuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmJfZmllbGRfZGF0YV9jaGFuZ2VkJywge1xyXG5cdFx0XHRcdFx0YnViYmxlczogdHJ1ZSwgZGV0YWlsOiB7IGtleTogJ29wdGlvbnMnLCB2YWx1ZTogb3B0aW9ucyB9XHJcblx0XHRcdFx0fSApICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblxyXG5cdFx0c3RhdGljIGVuc3VyZV9zb3J0YWJsZShwYW5lbCkge1xyXG5cdFx0XHRjb25zdCBsaXN0ID0gdGhpcy5nZXRfbGlzdCggcGFuZWwgKTtcclxuXHRcdFx0aWYgKCAhbGlzdCB8fCBsaXN0LmRhdGFzZXQuc29ydGFibGVfaW5pdCA9PT0gJzEnICkgcmV0dXJuO1xyXG5cdFx0XHRpZiAoIHdpbmRvdy5Tb3J0YWJsZT8uY3JlYXRlICkge1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHR3aW5kb3cuU29ydGFibGUuY3JlYXRlKCBsaXN0LCB7XHJcblx0XHRcdFx0XHRcdGhhbmRsZSAgIDogdGhpcy51aS5kcmFnX2hhbmRsZSxcclxuXHRcdFx0XHRcdFx0YW5pbWF0aW9uOiAxMjAsXHJcblx0XHRcdFx0XHRcdG9uU29ydCAgIDogKCkgPT4gdGhpcy5jb21taXRfb3B0aW9ucyggcGFuZWwgKVxyXG5cdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0bGlzdC5kYXRhc2V0LnNvcnRhYmxlX2luaXQgPSAnMSc7XHJcblx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHR3aW5kb3cuX3dwYmM/LmRldj8uZXJyb3I/LiggJ1NlbGVjdF9CYXNlLmVuc3VyZV9zb3J0YWJsZScsIGUgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgcmVidWlsZF9pZl9lbXB0eShwYW5lbCkge1xyXG5cdFx0XHRjb25zdCBsaXN0ICAgPSB0aGlzLmdldF9saXN0KCBwYW5lbCApO1xyXG5cdFx0XHRjb25zdCBob2xkZXIgPSB0aGlzLmdldF9ob2xkZXIoIHBhbmVsICk7XHJcblx0XHRcdGlmICggIWxpc3QgfHwgIWhvbGRlciB8fCBsaXN0LmNoaWxkcmVuLmxlbmd0aCApIHJldHVybjtcclxuXHJcblx0XHRcdGxldCBkYXRhID0gW107XHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0ZGF0YSA9IEpTT04ucGFyc2UoIGhvbGRlci52YWx1ZSB8fCAnW10nICk7XHJcblx0XHRcdH0gY2F0Y2ggKCBfICkge1xyXG5cdFx0XHRcdGRhdGEgPSBbXTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCAhQXJyYXkuaXNBcnJheSggZGF0YSApIHx8ICFkYXRhLmxlbmd0aCApIHtcclxuXHRcdFx0XHRkYXRhID0gKHRoaXMuZ2V0X2RlZmF1bHRzKCkub3B0aW9ucyB8fCBbXSkuc2xpY2UoIDAgKTtcclxuXHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0aG9sZGVyLnZhbHVlID0gSlNPTi5zdHJpbmdpZnkoIGRhdGEgKTtcclxuXHRcdFx0XHRcdGhvbGRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdpbnB1dCcsIHsgYnViYmxlczogdHJ1ZSB9ICkgKTtcclxuXHRcdFx0XHRcdGhvbGRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdjaGFuZ2UnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRmb3IgKCBsZXQgaSA9IDA7IGkgPCBkYXRhLmxlbmd0aDsgaSsrICkge1xyXG5cdFx0XHRcdHRoaXMuYXBwZW5kX3JvdyggcGFuZWwsIHtcclxuXHRcdFx0XHRcdGxhYmVsICAgOiBkYXRhW2ldPy5sYWJlbCB8fCAnJyxcclxuXHRcdFx0XHRcdHZhbHVlICAgOiBkYXRhW2ldPy52YWx1ZSB8fCAnJyxcclxuXHRcdFx0XHRcdHNlbGVjdGVkOiAhIWRhdGFbaV0/LnNlbGVjdGVkLFxyXG5cdFx0XHRcdFx0aW5kZXggICA6IGksXHJcblx0XHRcdFx0XHR1aWQgICAgIDogdGhpcy5tYWtlX3VpZCgpXHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR0aGlzLnN5bmNfZGVmYXVsdF92YWx1ZV9sb2NrKCBwYW5lbCApO1xyXG5cdFx0XHR0aGlzLnN5bmNfcGxhY2Vob2xkZXJfbG9jayggcGFuZWwgKTtcclxuXHRcdFx0dGhpcy5zeW5jX3ZhbHVlX2lucHV0c192aXNpYmlsaXR5KCBwYW5lbCApO1xyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyBoYXNfcm93X2RlZmF1bHRzKHBhbmVsKSB7XHJcblx0XHRcdGNvbnN0IGNoZWNrcyA9IHBhbmVsPy5xdWVyeVNlbGVjdG9yQWxsKCB0aGlzLnVpLnRvZ2dsZSApO1xyXG5cdFx0XHRpZiAoICFjaGVja3M/Lmxlbmd0aCApIHJldHVybiBmYWxzZTtcclxuXHRcdFx0Zm9yICggbGV0IGkgPSAwOyBpIDwgY2hlY2tzLmxlbmd0aDsgaSsrICkgaWYgKCBjaGVja3NbaV0uY2hlY2tlZCApIHJldHVybiB0cnVlO1xyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0c3RhdGljIGlzX211bHRpcGxlX2VuYWJsZWQocGFuZWwpIHtcclxuXHRcdFx0Y29uc3QgY2hrID0gcGFuZWw/LnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkubXVsdGlwbGVfY2hrICk7XHJcblx0XHRcdHJldHVybiAhIShjaGsgJiYgY2hrLmNoZWNrZWQpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyBoYXNfdGV4dF9kZWZhdWx0X3ZhbHVlKHBhbmVsKSB7XHJcblx0XHRcdGNvbnN0IGR2ID0gcGFuZWw/LnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkuZGVmYXVsdF90ZXh0ICk7XHJcblx0XHRcdHJldHVybiAhIShkdiAmJiBTdHJpbmcoIGR2LnZhbHVlIHx8ICcnICkudHJpbSgpLmxlbmd0aCk7XHJcblx0XHR9XHJcblxyXG5cdFx0c3RhdGljIHN5bmNfZGVmYXVsdF92YWx1ZV9sb2NrKHBhbmVsKSB7XHJcblx0XHRcdGNvbnN0IGlucHV0ID0gcGFuZWw/LnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkuZGVmYXVsdF90ZXh0ICk7XHJcblx0XHRcdGNvbnN0IG5vdGUgID0gcGFuZWw/LnF1ZXJ5U2VsZWN0b3IoICcuanMtZGVmYXVsdC12YWx1ZS1ub3RlJyApO1xyXG5cdFx0XHRpZiAoICFpbnB1dCApIHJldHVybjtcclxuXHJcblx0XHRcdGNvbnN0IGxvY2sgICAgID0gdGhpcy5oYXNfcm93X2RlZmF1bHRzKCBwYW5lbCApO1xyXG5cdFx0XHRpbnB1dC5kaXNhYmxlZCA9ICEhbG9jaztcclxuXHRcdFx0aWYgKCBsb2NrICkge1xyXG5cdFx0XHRcdGlucHV0LnNldEF0dHJpYnV0ZSggJ2FyaWEtZGlzYWJsZWQnLCAndHJ1ZScgKTtcclxuXHRcdFx0XHRpZiAoIG5vdGUgKSBub3RlLnN0eWxlLmRpc3BsYXkgPSAnJztcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRpbnB1dC5yZW1vdmVBdHRyaWJ1dGUoICdhcmlhLWRpc2FibGVkJyApO1xyXG5cdFx0XHRcdGlmICggbm90ZSApIG5vdGUuc3R5bGUuZGlzcGxheSA9ICdub25lJztcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyBzeW5jX3BsYWNlaG9sZGVyX2xvY2socGFuZWwpIHtcclxuXHRcdFx0Y29uc3QgaW5wdXQgPSBwYW5lbD8ucXVlcnlTZWxlY3RvciggdGhpcy51aS5wbGFjZWhvbGRlcl9pbnB1dCApO1xyXG5cdFx0XHRjb25zdCBub3RlICA9IHBhbmVsPy5xdWVyeVNlbGVjdG9yKCB0aGlzLnVpLnBsYWNlaG9sZGVyX25vdGUgKTtcclxuXHJcblx0XHRcdC8vIE5FVzogY29tcHV0ZSBtdWx0aXBsZSBhbmQgdG9nZ2xlIHJvdyB2aXNpYmlsaXR5XHJcblx0XHRcdGNvbnN0IGlzTXVsdGlwbGUgICAgID0gdGhpcy5pc19tdWx0aXBsZV9lbmFibGVkKCBwYW5lbCApO1xyXG5cdFx0XHRjb25zdCBwbGFjZWhvbGRlclJvdyA9IGlucHV0Py5jbG9zZXN0KCAnLmluc3BlY3Rvcl9fcm93JyApIHx8IG51bGw7XHJcblx0XHRcdGNvbnN0IHNpemVJbnB1dCAgICAgID0gcGFuZWw/LnF1ZXJ5U2VsZWN0b3IoIHRoaXMudWkuc2l6ZV9pbnB1dCApIHx8IG51bGw7XHJcblx0XHRcdGNvbnN0IHNpemVSb3cgICAgICAgID0gc2l6ZUlucHV0Py5jbG9zZXN0KCAnLmluc3BlY3Rvcl9fcm93JyApIHx8IG51bGw7XHJcblxyXG5cdFx0XHQvLyBTaG93IHBsYWNlaG9sZGVyIG9ubHkgZm9yIHNpbmdsZS1zZWxlY3Q7IHNob3cgc2l6ZSBvbmx5IGZvciBtdWx0aXBsZVxyXG5cdFx0XHRpZiAoIHBsYWNlaG9sZGVyUm93ICkgcGxhY2Vob2xkZXJSb3cuc3R5bGUuZGlzcGxheSA9IGlzTXVsdGlwbGUgPyAnbm9uZScgOiAnJztcclxuXHRcdFx0aWYgKCBzaXplUm93ICkgc2l6ZVJvdy5zdHlsZS5kaXNwbGF5ID0gaXNNdWx0aXBsZSA/ICcnIDogJ25vbmUnO1xyXG5cclxuXHRcdFx0Ly8gRXhpc3RpbmcgYmVoYXZpb3IgKGtlZXAgYXMtaXMpXHJcblx0XHRcdGlmICggIWlucHV0ICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgbG9jayA9IGlzTXVsdGlwbGUgfHwgdGhpcy5oYXNfcm93X2RlZmF1bHRzKCBwYW5lbCApIHx8IHRoaXMuaGFzX3RleHRfZGVmYXVsdF92YWx1ZSggcGFuZWwgKTtcclxuXHRcdFx0aWYgKCBub3RlICYmICFub3RlLmlkICkgbm90ZS5pZCA9ICd3cGJjX3BsYWNlaG9sZGVyX25vdGVfJyArIE1hdGgucmFuZG9tKCkudG9TdHJpbmcoIDM2ICkuc2xpY2UoIDIsIDEwICk7XHJcblxyXG5cdFx0XHRpbnB1dC5kaXNhYmxlZCA9ICEhbG9jaztcclxuXHRcdFx0aWYgKCBsb2NrICkge1xyXG5cdFx0XHRcdGlucHV0LnNldEF0dHJpYnV0ZSggJ2FyaWEtZGlzYWJsZWQnLCAndHJ1ZScgKTtcclxuXHRcdFx0XHRpZiAoIG5vdGUgKSB7XHJcblx0XHRcdFx0XHRub3RlLnN0eWxlLmRpc3BsYXkgPSAnJztcclxuXHRcdFx0XHRcdGlucHV0LnNldEF0dHJpYnV0ZSggJ2FyaWEtZGVzY3JpYmVkYnknLCBub3RlLmlkICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGlucHV0LnJlbW92ZUF0dHJpYnV0ZSggJ2FyaWEtZGlzYWJsZWQnICk7XHJcblx0XHRcdFx0aW5wdXQucmVtb3ZlQXR0cmlidXRlKCAnYXJpYS1kZXNjcmliZWRieScgKTtcclxuXHRcdFx0XHRpZiAoIG5vdGUgKSBub3RlLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRzdGF0aWMgZW5mb3JjZV9zaW5nbGVfZGVmYXVsdChwYW5lbCwgY2xpY2tlZCkge1xyXG5cdFx0XHRpZiAoIHRoaXMuaXNfbXVsdGlwbGVfZW5hYmxlZCggcGFuZWwgKSApIHJldHVybjtcclxuXHJcblx0XHRcdGNvbnN0IGNoZWNrcyA9IHBhbmVsPy5xdWVyeVNlbGVjdG9yQWxsKCB0aGlzLnVpLnRvZ2dsZSApO1xyXG5cdFx0XHRpZiAoICFjaGVja3M/Lmxlbmd0aCApIHJldHVybjtcclxuXHJcblx0XHRcdGlmICggY2xpY2tlZCAmJiBjbGlja2VkLmNoZWNrZWQgKSB7XHJcblx0XHRcdFx0Zm9yICggbGV0IGkgPSAwOyBpIDwgY2hlY2tzLmxlbmd0aDsgaSsrICkgaWYgKCBjaGVja3NbaV0gIT09IGNsaWNrZWQgKSB7XHJcblx0XHRcdFx0XHRjaGVja3NbaV0uY2hlY2tlZCA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0Y2hlY2tzW2ldLnNldEF0dHJpYnV0ZSggJ2FyaWEtY2hlY2tlZCcsICdmYWxzZScgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Y2xpY2tlZC5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCAndHJ1ZScgKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGxldCBrZXB0ID0gZmFsc2U7XHJcblx0XHRcdGZvciAoIGxldCBqID0gMDsgaiA8IGNoZWNrcy5sZW5ndGg7IGorKyApIGlmICggY2hlY2tzW2pdLmNoZWNrZWQgKSB7XHJcblx0XHRcdFx0aWYgKCAha2VwdCApIHtcclxuXHRcdFx0XHRcdGtlcHQgPSB0cnVlO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRjaGVja3Nbal0uY2hlY2tlZCA9IGZhbHNlO1xyXG5cdFx0XHRcdFx0Y2hlY2tzW2pdLnNldEF0dHJpYnV0ZSggJ2FyaWEtY2hlY2tlZCcsICdmYWxzZScgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHRoaXMuc3luY19kZWZhdWx0X3ZhbHVlX2xvY2soIHBhbmVsICk7XHJcblx0XHRcdHRoaXMuc3luY19wbGFjZWhvbGRlcl9sb2NrKCBwYW5lbCApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0gb25lLXRpbWUgYm9vdHN0cmFwIG9mIGEgcGFuZWwgLS0tLVxyXG5cdFx0c3RhdGljIGJvb3RzdHJhcF9wYW5lbChwYW5lbCkge1xyXG5cdFx0XHRpZiAoICFwYW5lbCApIHJldHVybjtcclxuXHRcdFx0aWYgKCAhcGFuZWwucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fb3B0aW9uc19lZGl0b3InICkgKSByZXR1cm47IC8vIG9ubHkgc2VsZWN0LWxpa2UgVUlzXHJcblx0XHRcdGlmICggcGFuZWwuZGF0YXNldC5zZWxlY3RiYXNlX2Jvb3RzdHJhcHBlZCA9PT0gJzEnICkge1xyXG5cdFx0XHRcdHRoaXMuZW5zdXJlX3NvcnRhYmxlKCBwYW5lbCApO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dGhpcy5yZWJ1aWxkX2lmX2VtcHR5KCBwYW5lbCApO1xyXG5cdFx0XHR0aGlzLmVuc3VyZV9zb3J0YWJsZSggcGFuZWwgKTtcclxuXHRcdFx0cGFuZWwuZGF0YXNldC5zZWxlY3RiYXNlX2Jvb3RzdHJhcHBlZCA9ICcxJztcclxuXHJcblx0XHRcdHRoaXMuc3luY19kZWZhdWx0X3ZhbHVlX2xvY2soIHBhbmVsICk7XHJcblx0XHRcdHRoaXMuc3luY19wbGFjZWhvbGRlcl9sb2NrKCBwYW5lbCApO1xyXG5cdFx0XHR0aGlzLnN5bmNfdmFsdWVfaW5wdXRzX3Zpc2liaWxpdHkoIHBhbmVsICk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gLS0tLSBob29rIGludG8gaW5zcGVjdG9yIGxpZmVjeWNsZSAoZmlyZXMgT05DRSkgLS0tLVxyXG5cdFx0c3RhdGljIHdpcmVfb25jZSgpIHtcclxuXHRcdFx0aWYgKCBDb3JlLl9fc2VsZWN0YmFzZV93aXJlZCApIHJldHVybjtcclxuXHRcdFx0Q29yZS5fX3NlbGVjdGJhc2Vfd2lyZWQgPSB0cnVlO1xyXG5cclxuXHRcdFx0Y29uc3Qgb25fcmVhZHlfb3JfcmVuZGVyID0gKGV2KSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgcGFuZWwgPSBldj8uZGV0YWlsPy5wYW5lbDtcclxuXHRcdFx0XHRjb25zdCBmaWVsZCA9IGV2Py5kZXRhaWw/LmVsIHx8IGV2Py5kZXRhaWw/LmZpZWxkIHx8IG51bGw7XHJcblx0XHRcdFx0aWYgKCAhcGFuZWwgKSByZXR1cm47XHJcblx0XHRcdFx0aWYgKCBmaWVsZCApIHBhbmVsLl9fc2VsZWN0YmFzZV9maWVsZCA9IGZpZWxkO1xyXG5cdFx0XHRcdHRoaXMuYm9vdHN0cmFwX3BhbmVsKCBwYW5lbCApO1xyXG5cdFx0XHRcdC8vIElmIHRoZSBpbnNwZWN0b3Igcm9vdCB3YXMgcmVtb3VudGVkLCBlbnN1cmUgcm9vdCBsaXN0ZW5lcnMgYXJlIChyZSlib3VuZC5cclxuXHRcdFx0XHR0aGlzLndpcmVfcm9vdF9saXN0ZW5lcnMoKTtcclxuXHRcdFx0fTtcclxuXHJcblx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICd3cGJjX2JmYl9pbnNwZWN0b3JfcmVhZHknLCBvbl9yZWFkeV9vcl9yZW5kZXIgKTtcclxuXHRcdFx0ZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ3dwYmNfYmZiX2luc3BlY3Rvcl9yZW5kZXInLCBvbl9yZWFkeV9vcl9yZW5kZXIgKTtcclxuXHJcblx0XHRcdHRoaXMud2lyZV9yb290X2xpc3RlbmVycygpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHN0YXRpYyB3aXJlX3Jvb3RfbGlzdGVuZXJzKCkge1xyXG5cclxuXHRcdFx0Ly8gSWYgYWxyZWFkeSB3aXJlZCBBTkQgdGhlIHN0b3JlZCByb290IGlzIHN0aWxsIGluIHRoZSBET00sIGJhaWwgb3V0LlxyXG5cdFx0XHRpZiAoIHRoaXMuX19yb290X3dpcmVkICYmIHRoaXMuX19yb290X25vZGU/LmlzQ29ubmVjdGVkICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3Qgcm9vdCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0aWYgKCAhcm9vdCApIHtcclxuXHRcdFx0XHQvLyBSb290IG1pc3NpbmcgKGUuZy4sIFNQQSByZS1yZW5kZXIpIOKAlCBjbGVhciBmbGFncyBzbyB3ZSBjYW4gd2lyZSBsYXRlci5cclxuXHRcdFx0XHR0aGlzLl9fcm9vdF93aXJlZCA9IGZhbHNlO1xyXG5cdFx0XHRcdHRoaXMuX19yb290X25vZGUgID0gbnVsbDtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHRoaXMuX19yb290X25vZGUgICAgICAgICAgICAgICAgICAgPSByb290O1xyXG5cdFx0XHR0aGlzLl9fcm9vdF93aXJlZCAgICAgICAgICAgICAgICAgID0gdHJ1ZTtcclxuXHRcdFx0cm9vdC5kYXRhc2V0LnNlbGVjdGJhc2Vfcm9vdF93aXJlZCA9ICcxJztcclxuXHJcblx0XHRcdGNvbnN0IGdldF9wYW5lbCA9ICh0YXJnZXQpID0+XHJcblx0XHRcdFx0dGFyZ2V0Py5jbG9zZXN0Py4oICcud3BiY19iZmJfX2luc3BlY3Rvcl9fYm9keScgKSB8fFxyXG5cdFx0XHRcdHJvb3QucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9faW5zcGVjdG9yX19ib2R5JyApIHx8IG51bGw7XHJcblxyXG5cdFx0XHQvLyBDbGljayBoYW5kbGVyczogYWRkIC8gZGVsZXRlIC8gZHVwbGljYXRlXHJcblx0XHRcdHJvb3QuYWRkRXZlbnRMaXN0ZW5lciggJ2NsaWNrJywgKGUpID0+IHtcclxuXHRcdFx0XHRjb25zdCBwYW5lbCA9IGdldF9wYW5lbCggZS50YXJnZXQgKTtcclxuXHRcdFx0XHRpZiAoICFwYW5lbCApIHJldHVybjtcclxuXHJcblx0XHRcdFx0dGhpcy5ib290c3RyYXBfcGFuZWwoIHBhbmVsICk7XHJcblxyXG5cdFx0XHRcdGNvbnN0IHVpID0gdGhpcy51aTtcclxuXHJcblx0XHRcdFx0Ly8gRXhpc3RpbmcgXCJBZGQgb3B0aW9uXCIgYnV0dG9uICh0b3AgdG9vbGJhcilcclxuXHRcdFx0XHRjb25zdCBhZGQgPSBlLnRhcmdldC5jbG9zZXN0Py4oIHVpLmFkZF9idG4gKTtcclxuXHRcdFx0XHRpZiAoIGFkZCApIHtcclxuXHRcdFx0XHRcdHRoaXMuYXBwZW5kX3JvdyggcGFuZWwsIHsgbGFiZWw6ICcnLCB2YWx1ZTogJycsIHNlbGVjdGVkOiBmYWxzZSB9ICk7XHJcblx0XHRcdFx0XHR0aGlzLmNvbW1pdF9vcHRpb25zKCBwYW5lbCApO1xyXG5cdFx0XHRcdFx0dGhpcy5zeW5jX3ZhbHVlX2lucHV0c192aXNpYmlsaXR5KCBwYW5lbCApO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gRHJvcGRvd24gbWVudSBhY3Rpb25zLlxyXG5cdFx0XHRcdGNvbnN0IG1lbnVfYWN0aW9uID0gZS50YXJnZXQuY2xvc2VzdD8uKCB1aS5tZW51X2FjdGlvbiApO1xyXG5cdFx0XHRcdGlmICggbWVudV9hY3Rpb24gKSB7XHJcblx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG5cclxuXHRcdFx0XHRcdGNvbnN0IGFjdGlvbiA9IChtZW51X2FjdGlvbi5nZXRBdHRyaWJ1dGUoICdkYXRhLWFjdGlvbicgKSB8fCAnJykudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHRcdGNvbnN0IHJvdyAgICA9IG1lbnVfYWN0aW9uLmNsb3Nlc3Q/LiggdWkucm93ICk7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCAhcm93ICkge1xyXG5cdFx0XHRcdFx0XHR0aGlzLmNsb3NlX2Ryb3Bkb3duKCBtZW51X2FjdGlvbiApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0aWYgKCAnYWRkX2FmdGVyJyA9PT0gYWN0aW9uICkge1xyXG5cdFx0XHRcdFx0XHQvLyBBZGQgZW1wdHkgcm93IGFmdGVyIGN1cnJlbnRcclxuXHRcdFx0XHRcdFx0Y29uc3QgcHJldl9jb3VudCA9IHRoaXMuZ2V0X2xpc3QoIHBhbmVsICk/LmNoaWxkcmVuLmxlbmd0aCB8fCAwO1xyXG5cdFx0XHRcdFx0XHR0aGlzLmFwcGVuZF9yb3coIHBhbmVsLCB7IGxhYmVsOiAnJywgdmFsdWU6ICcnLCBzZWxlY3RlZDogZmFsc2UgfSApO1xyXG5cdFx0XHRcdFx0XHQvLyBNb3ZlIHRoZSBuZXdseSBhZGRlZCBsYXN0IHJvdyBqdXN0IGFmdGVyIGN1cnJlbnQgcm93IHRvIHByZXNlcnZlIFwiYWRkIGFmdGVyXCJcclxuXHRcdFx0XHRcdFx0Y29uc3QgbGlzdCA9IHRoaXMuZ2V0X2xpc3QoIHBhbmVsICk7XHJcblx0XHRcdFx0XHRcdGlmICggbGlzdCAmJiBsaXN0Lmxhc3RFbGVtZW50Q2hpbGQgJiYgbGlzdC5sYXN0RWxlbWVudENoaWxkICE9PSByb3cgKSB7XHJcblx0XHRcdFx0XHRcdFx0dGhpcy5pbnNlcnRfYWZ0ZXIoIGxpc3QubGFzdEVsZW1lbnRDaGlsZCwgcm93ICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0dGhpcy5jb21taXRfb3B0aW9ucyggcGFuZWwgKTtcclxuXHRcdFx0XHRcdFx0dGhpcy5zeW5jX3ZhbHVlX2lucHV0c192aXNpYmlsaXR5KCBwYW5lbCApO1xyXG5cdFx0XHRcdFx0fSBlbHNlIGlmICggJ2R1cGxpY2F0ZScgPT09IGFjdGlvbiApIHtcclxuXHRcdFx0XHRcdFx0Y29uc3QgbGJsID0gKHJvdy5xdWVyeVNlbGVjdG9yKCB1aS5sYWJlbCApIHx8IHt9KS52YWx1ZSB8fCAnJztcclxuXHRcdFx0XHRcdFx0Y29uc3QgdmFsID0gKHJvdy5xdWVyeVNlbGVjdG9yKCB1aS52YWx1ZSApIHx8IHt9KS52YWx1ZSB8fCAnJztcclxuXHRcdFx0XHRcdFx0Y29uc3Qgc2VsID0gISEoKHJvdy5xdWVyeVNlbGVjdG9yKCB1aS50b2dnbGUgKSB8fCB7fSkuY2hlY2tlZCk7XHJcblx0XHRcdFx0XHRcdHRoaXMuYXBwZW5kX3JvdyggcGFuZWwsIHsgbGFiZWw6IGxibCwgdmFsdWU6IHZhbCwgc2VsZWN0ZWQ6IHNlbCwgdWlkOiB0aGlzLm1ha2VfdWlkKCkgfSApO1xyXG5cdFx0XHRcdFx0XHQvLyBQbGFjZSB0aGUgbmV3IHJvdyByaWdodCBhZnRlciB0aGUgY3VycmVudC5cclxuXHRcdFx0XHRcdFx0Y29uc3QgbGlzdCA9IHRoaXMuZ2V0X2xpc3QoIHBhbmVsICk7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoIGxpc3QgJiYgbGlzdC5sYXN0RWxlbWVudENoaWxkICYmIGxpc3QubGFzdEVsZW1lbnRDaGlsZCAhPT0gcm93ICkge1xyXG5cdFx0XHRcdFx0XHRcdHRoaXMuaW5zZXJ0X2FmdGVyKCBsaXN0Lmxhc3RFbGVtZW50Q2hpbGQsIHJvdyApO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdHRoaXMuZW5mb3JjZV9zaW5nbGVfZGVmYXVsdCggcGFuZWwsIG51bGwgKTtcclxuXHRcdFx0XHRcdFx0dGhpcy5jb21taXRfb3B0aW9ucyggcGFuZWwgKTtcclxuXHRcdFx0XHRcdFx0dGhpcy5zeW5jX3ZhbHVlX2lucHV0c192aXNpYmlsaXR5KCBwYW5lbCApO1xyXG5cdFx0XHRcdFx0fSBlbHNlIGlmICggJ3JlbW92ZScgPT09IGFjdGlvbiApIHtcclxuXHRcdFx0XHRcdFx0aWYgKCByb3cgJiYgcm93LnBhcmVudE5vZGUgKSByb3cucGFyZW50Tm9kZS5yZW1vdmVDaGlsZCggcm93ICk7XHJcblx0XHRcdFx0XHRcdHRoaXMuY29tbWl0X29wdGlvbnMoIHBhbmVsICk7XHJcblx0XHRcdFx0XHRcdHRoaXMuc3luY192YWx1ZV9pbnB1dHNfdmlzaWJpbGl0eSggcGFuZWwgKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHR0aGlzLmNsb3NlX2Ryb3Bkb3duKCBtZW51X2FjdGlvbiApO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdH0sIHRydWUgKTtcclxuXHJcblxyXG5cdFx0XHQvLyBJbnB1dCBkZWxlZ2F0aW9uLlxyXG5cdFx0XHRyb290LmFkZEV2ZW50TGlzdGVuZXIoICdpbnB1dCcsIChlKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgcGFuZWwgPSBnZXRfcGFuZWwoIGUudGFyZ2V0ICk7XHJcblx0XHRcdFx0aWYgKCAhIHBhbmVsICkge1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRjb25zdCB1aSAgICAgICAgICAgICAgICA9IHRoaXMudWk7XHJcblx0XHRcdFx0Y29uc3QgaXNfbGFiZWxfb3JfdmFsdWUgPSBlLnRhcmdldC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX29wdC1sYWJlbCcgKSB8fCBlLnRhcmdldC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX29wdC12YWx1ZScgKTtcclxuXHRcdFx0XHRjb25zdCBpc190b2dnbGUgICAgICAgICA9IGUudGFyZ2V0LmNsYXNzTGlzdD8uY29udGFpbnMoICd3cGJjX2JmYl9fb3B0LXNlbGVjdGVkLWNoaycgKTtcclxuXHRcdFx0XHRjb25zdCBpc19tdWx0aXBsZSAgICAgICA9IGUudGFyZ2V0Lm1hdGNoZXM/LiggdWkubXVsdGlwbGVfY2hrICk7XHJcblx0XHRcdFx0Y29uc3QgaXNfZGVmYXVsdF90ZXh0ICAgPSBlLnRhcmdldC5tYXRjaGVzPy4oIHVpLmRlZmF1bHRfdGV4dCApO1xyXG5cdFx0XHRcdGNvbnN0IGlzX3ZhbHVlX2RpZmZlcnMgID0gZS50YXJnZXQubWF0Y2hlcz8uKCB1aS52YWx1ZV9kaWZmZXJzX2NoayApO1xyXG5cclxuXHRcdFx0XHQvLyBIYW5kbGUgXCJ2YWx1ZSBkaWZmZXJzXCIgdG9nZ2xlIGxpdmVcclxuXHRcdFx0XHRpZiAoIGlzX3ZhbHVlX2RpZmZlcnMgKSB7XHJcblx0XHRcdFx0XHR0aGlzLnN5bmNfdmFsdWVfaW5wdXRzX3Zpc2liaWxpdHkoIHBhbmVsICk7XHJcblx0XHRcdFx0XHR0aGlzLmNvbW1pdF9vcHRpb25zKCBwYW5lbCApO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gVHJhY2sgd2hlbiB0aGUgdXNlciBlZGl0cyBWQUxVRSBleHBsaWNpdGx5XHJcblx0XHRcdFx0aWYgKCBlLnRhcmdldC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX29wdC12YWx1ZScgKSApIHtcclxuXHRcdFx0XHRcdGNvbnN0IHJvdyA9IGUudGFyZ2V0LmNsb3Nlc3QoIHRoaXMudWkucm93ICk7XHJcblx0XHRcdFx0XHR0aGlzLm1hcmtfcm93X3ZhbHVlX3VzZXJfdG91Y2hlZCggcm93ICk7XHJcblx0XHRcdFx0XHQvLyBLZWVwIHRoZSBjYWNoZSB1cGRhdGVkIHNvIHRvZ2dsaW5nIE9GRi9PTiBsYXRlciByZXN0b3JlcyB0aGUgbGF0ZXN0IGN1c3RvbSB2YWx1ZVxyXG5cdFx0XHRcdFx0ZS50YXJnZXQuZGF0YXNldC5jYWNoZWRfdmFsdWUgPSBlLnRhcmdldC52YWx1ZSB8fCAnJztcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIEF1dG8tZmlsbCBWQUxVRSBmcm9tIExBQkVMIGlmIHZhbHVlIGlzIGZyZXNoIChhbmQgZGlmZmVycyBpcyBPTik7IGlmIGRpZmZlcnMgaXMgT0ZGLCB3ZSBtaXJyb3IgYW55d2F5IGluIGNvbW1pdFxyXG5cdFx0XHRcdGlmICggZS50YXJnZXQuY2xhc3NMaXN0Py5jb250YWlucyggJ3dwYmNfYmZiX19vcHQtbGFiZWwnICkgKSB7XHJcblx0XHRcdFx0XHRjb25zdCByb3cgICAgID0gZS50YXJnZXQuY2xvc2VzdCggdWkucm93ICk7XHJcblx0XHRcdFx0XHRjb25zdCB2YWxfaW4gID0gcm93Py5xdWVyeVNlbGVjdG9yKCB1aS52YWx1ZSApO1xyXG5cdFx0XHRcdFx0Y29uc3QgZGlmZmVycyA9IHRoaXMuaXNfdmFsdWVfZGlmZmVyc19lbmFibGVkKCBwYW5lbCApO1xyXG5cclxuXHRcdFx0XHRcdGlmICggdmFsX2luICkge1xyXG5cdFx0XHRcdFx0XHRpZiAoICFkaWZmZXJzICkge1xyXG5cdFx0XHRcdFx0XHRcdC8vIHNpbmdsZS1pbnB1dCBtb2RlOiBtaXJyb3IgaHVtYW4gbGFiZWwgd2l0aCBtaW5pbWFsIGVzY2FwaW5nXHJcblx0XHRcdFx0XHRcdFx0dmFsX2luLnZhbHVlID0gdGhpcy5idWlsZF92YWx1ZV9mcm9tX2xhYmVsKCBlLnRhcmdldC52YWx1ZSwgZmFsc2UgKTtcclxuXHRcdFx0XHRcdFx0fSBlbHNlIGlmICggIXRoaXMuaXNfcm93X3ZhbHVlX3VzZXJfdG91Y2hlZCggcm93ICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gc2VwYXJhdGUtdmFsdWUgbW9kZSwgb25seSB3aGlsZSBmcmVzaFxyXG5cdFx0XHRcdFx0XHRcdHZhbF9pbi52YWx1ZSA9IHRoaXMuYnVpbGRfdmFsdWVfZnJvbV9sYWJlbCggZS50YXJnZXQudmFsdWUsIHRydWUgKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblxyXG5cdFx0XHRcdGlmICggaXNfbGFiZWxfb3JfdmFsdWUgfHwgaXNfdG9nZ2xlIHx8IGlzX211bHRpcGxlICkge1xyXG5cdFx0XHRcdFx0aWYgKCBpc190b2dnbGUgKSBlLnRhcmdldC5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCBlLnRhcmdldC5jaGVja2VkID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cdFx0XHRcdFx0aWYgKCBpc190b2dnbGUgfHwgaXNfbXVsdGlwbGUgKSB0aGlzLmVuZm9yY2Vfc2luZ2xlX2RlZmF1bHQoIHBhbmVsLCBpc190b2dnbGUgPyBlLnRhcmdldCA6IG51bGwgKTtcclxuXHRcdFx0XHRcdHRoaXMuY29tbWl0X29wdGlvbnMoIHBhbmVsICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpZiAoIGlzX2RlZmF1bHRfdGV4dCApIHtcclxuXHRcdFx0XHRcdHRoaXMuc3luY19kZWZhdWx0X3ZhbHVlX2xvY2soIHBhbmVsICk7XHJcblx0XHRcdFx0XHR0aGlzLnN5bmNfcGxhY2Vob2xkZXJfbG9jayggcGFuZWwgKTtcclxuXHRcdFx0XHRcdGNvbnN0IGhvbGRlciA9IHRoaXMuZ2V0X2hvbGRlciggcGFuZWwgKTtcclxuXHRcdFx0XHRcdGlmICggaG9sZGVyICkge1xyXG5cdFx0XHRcdFx0XHRob2xkZXIuZGlzcGF0Y2hFdmVudCggbmV3IEV2ZW50KCAnaW5wdXQnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0XHRcdGhvbGRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdjaGFuZ2UnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9LCB0cnVlICk7XHJcblxyXG5cclxuXHRcdFx0Ly8gQ2hhbmdlIGRlbGVnYXRpb25cclxuXHRcdFx0cm9vdC5hZGRFdmVudExpc3RlbmVyKCAnY2hhbmdlJywgKGUpID0+IHtcclxuXHRcdFx0XHRjb25zdCBwYW5lbCA9IGdldF9wYW5lbCggZS50YXJnZXQgKTtcclxuXHRcdFx0XHRpZiAoICFwYW5lbCApIHJldHVybjtcclxuXHJcblx0XHRcdFx0Y29uc3QgdWkgICAgICAgID0gdGhpcy51aTtcclxuXHRcdFx0XHRjb25zdCBpc190b2dnbGUgPSBlLnRhcmdldC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX29wdC1zZWxlY3RlZC1jaGsnICk7XHJcblx0XHRcdFx0Y29uc3QgaXNfbXVsdGkgID0gZS50YXJnZXQubWF0Y2hlcz8uKCB1aS5tdWx0aXBsZV9jaGsgKTtcclxuXHRcdFx0XHRpZiAoICFpc190b2dnbGUgJiYgIWlzX211bHRpICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0XHRpZiAoIGlzX3RvZ2dsZSApIGUudGFyZ2V0LnNldEF0dHJpYnV0ZSggJ2FyaWEtY2hlY2tlZCcsIGUudGFyZ2V0LmNoZWNrZWQgPyAndHJ1ZScgOiAnZmFsc2UnICk7XHJcblx0XHRcdFx0dGhpcy5lbmZvcmNlX3NpbmdsZV9kZWZhdWx0KCBwYW5lbCwgaXNfdG9nZ2xlID8gZS50YXJnZXQgOiBudWxsICk7XHJcblx0XHRcdFx0dGhpcy5jb21taXRfb3B0aW9ucyggcGFuZWwgKTtcclxuXHRcdFx0fSwgdHJ1ZSApO1xyXG5cclxuXHRcdFx0Ly8gTGF6eSBib290c3RyYXBcclxuXHRcdFx0cm9vdC5hZGRFdmVudExpc3RlbmVyKCAnbW91c2VlbnRlcicsIChlKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgcGFuZWwgPSBnZXRfcGFuZWwoIGUudGFyZ2V0ICk7XHJcblx0XHRcdFx0aWYgKCBwYW5lbCAmJiBlLnRhcmdldD8uY2xvc2VzdD8uKCB0aGlzLnVpLmxpc3QgKSApIHRoaXMuYm9vdHN0cmFwX3BhbmVsKCBwYW5lbCApO1xyXG5cdFx0XHR9LCB0cnVlICk7XHJcblxyXG5cdFx0XHRyb290LmFkZEV2ZW50TGlzdGVuZXIoICdtb3VzZWRvd24nLCAoZSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IHBhbmVsID0gZ2V0X3BhbmVsKCBlLnRhcmdldCApO1xyXG5cdFx0XHRcdGlmICggcGFuZWwgJiYgZS50YXJnZXQ/LmNsb3Nlc3Q/LiggdGhpcy51aS5kcmFnX2hhbmRsZSApICkgdGhpcy5ib290c3RyYXBfcGFuZWwoIHBhbmVsICk7XHJcblx0XHRcdH0sIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0fTtcclxuXHJcblx0dHJ5IHsgQ29yZS5XUEJDX0JGQl9TZWxlY3RfQmFzZS53aXJlX29uY2UoKTsgfSBjYXRjaCAoXykge31cclxuXHQvLyBUcnkgaW1tZWRpYXRlbHkgKGlmIHJvb3QgaXMgYWxyZWFkeSBpbiBET00pLCB0aGVuIGFnYWluIG9uIERPTUNvbnRlbnRMb2FkZWQuXHJcblx0Q29yZS5XUEJDX0JGQl9TZWxlY3RfQmFzZS53aXJlX3Jvb3RfbGlzdGVuZXJzKCk7XHJcblxyXG5cdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCAoKSA9PiB7IENvcmUuV1BCQ19CRkJfU2VsZWN0X0Jhc2Uud2lyZV9yb290X2xpc3RlbmVycygpOyAgfSk7XHJcblxyXG59KCB3aW5kb3cgKSk7IiwiLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbi8vID09IEZpbGUgIC9pbmNsdWRlcy9wYWdlLWZvcm0tYnVpbGRlci9fb3V0L2NvcmUvYmZiLXVpLmpzID09IHwgMjAyNS0wOS0xMCAxNTo0N1xyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuKGZ1bmN0aW9uICh3LCBkKSB7XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyBTaW5nbGUgZ2xvYmFsIG5hbWVzcGFjZSAoaWRlbXBvdGVudCAmIGxvYWQtb3JkZXIgc2FmZSkuXHJcblx0Y29uc3QgQ29yZSA9ICh3LldQQkNfQkZCX0NvcmUgPSB3LldQQkNfQkZCX0NvcmUgfHwge30pO1xyXG5cdGNvbnN0IFVJICAgPSAoQ29yZS5VSSA9IENvcmUuVUkgfHwge30pO1xyXG5cclxuXHQvLyAtLS0gSGlnaGxpZ2h0IEVsZW1lbnQsICBsaWtlIEdlbmVyYXRvciBicm4gIC0gIFRpbnkgVUkgaGVscGVycyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRVSS5fcHVsc2VfdGltZXJzID0gVUkuX3B1bHNlX3RpbWVycyB8fCBuZXcgTWFwKCk7IC8vIGVsIC0+IHRpbWVyX2lkXHJcblx0VUkuX3B1bHNlX21ldGEgICA9IFVJLl9wdWxzZV9tZXRhICAgfHwgbmV3IE1hcCgpOyAvLyBlbCAtPiB7IHRva2VuLCBsYXN0X3RzLCBkZWJvdW5jZV9pZCwgY29sb3Jfc2V0IH1cclxuXHQvLyBQdWxzZSB0dW5pbmcgKG1pbGxpc2Vjb25kcykuXHJcblx0VUkuUFVMU0VfVEhST1RUTEVfTVMgID0gTnVtYmVyLmlzRmluaXRlKCBVSS5QVUxTRV9USFJPVFRMRV9NUyApID8gVUkuUFVMU0VfVEhST1RUTEVfTVMgOiA1MDA7XHJcblx0VUkuUFVMU0VfREVCT1VOQ0VfTVMgID0gTnVtYmVyLmlzRmluaXRlKCBVSS5QVUxTRV9ERUJPVU5DRV9NUyApID8gVUkuUFVMU0VfREVCT1VOQ0VfTVMgOiA3NTA7XHJcblxyXG5cdC8vIERlYm91bmNlIFNUUlVDVFVSRV9DSEFOR0UgZm9yIGNvbnRpbnVvdXMgaW5zcGVjdG9yIGNvbnRyb2xzIChzbGlkZXJzIC8gc2NydWJiaW5nKS5cclxuXHQvLyBUdW5lOiAxODAuLjM1MCBpcyB1c3VhbGx5IGEgc3dlZXQgc3BvdC5cclxuXHRVSS5TVFJVQ1RVUkVfQ0hBTkdFX0RFQk9VTkNFX01TID0gTnVtYmVyLmlzRmluaXRlKCBVSS5TVFJVQ1RVUkVfQ0hBTkdFX0RFQk9VTkNFX01TICkgPyBVSS5TVFJVQ1RVUkVfQ0hBTkdFX0RFQk9VTkNFX01TIDogMTgwO1xyXG5cdC8vIENoYW5nZSB0aGlzIHRvIHR1bmUgc3BlZWQ6IDUwLi4xMjAgbXMgaXMgYSBnb29kIHJhbmdlLiBDYW4gYmUgY29uZmlndXJlZCBpbiA8ZGl2IGRhdGEtbGVuLWdyb3VwIGRhdGEtbGVuLXRocm90dGxlPVwiMTgwXCI+Li4uPC9kaXY+LlxyXG5cdFVJLlZBTFVFX1NMSURFUl9USFJPVFRMRV9NUyA9IE51bWJlci5pc0Zpbml0ZSggVUkuVkFMVUVfU0xJREVSX1RIUk9UVExFX01TICkgPyBVSS5WQUxVRV9TTElERVJfVEhST1RUTEVfTVMgOiAxMjA7XHJcblxyXG5cdC8qKlxyXG5cdCAqIENhbmNlbCBhbnkgcnVubmluZyBwdWxzZSBzZXF1ZW5jZSBmb3IgYW4gZWxlbWVudC5cclxuXHQgKiBVc2VzIHRva2VuIGludmFsaWRhdGlvbiBzbyBhbHJlYWR5LXNjaGVkdWxlZCBjYWxsYmFja3MgYmVjb21lIG5vLW9wcy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsXHJcblx0ICovXHJcblx0VUkuY2FuY2VsX3B1bHNlID0gZnVuY3Rpb24gKGVsKSB7XHJcblx0XHRpZiAoICFlbCApIHsgcmV0dXJuOyB9XHJcblx0XHR0cnkge1xyXG5cdFx0XHRjbGVhclRpbWVvdXQoIFVJLl9wdWxzZV90aW1lcnMuZ2V0KCBlbCApICk7XHJcblx0XHR9IGNhdGNoICggXyApIHt9XHJcblx0XHRVSS5fcHVsc2VfdGltZXJzLmRlbGV0ZSggZWwgKTtcclxuXHJcblx0XHR2YXIgbWV0YSA9IFVJLl9wdWxzZV9tZXRhLmdldCggZWwgKSB8fCB7fTtcclxuXHRcdG1ldGEudG9rZW4gPSAoTnVtYmVyLmlzRmluaXRlKCBtZXRhLnRva2VuICkgPyBtZXRhLnRva2VuIDogMCkgKyAxO1xyXG5cdFx0bWV0YS5jb2xvcl9zZXQgPSBmYWxzZTtcclxuXHRcdHRyeSB7IGVsLmNsYXNzTGlzdC5yZW1vdmUoICd3cGJjX2JmYl9fc2Nyb2xsLXB1bHNlJywgJ3dwYmNfYmZiX19oaWdobGlnaHQtcHVsc2UnICk7IH0gY2F0Y2ggKCBfICkge31cclxuXHRcdHRyeSB7IGVsLnN0eWxlLnJlbW92ZVByb3BlcnR5KCAnLS13cGJjLWJmYi1wdWxzZS1jb2xvcicgKTsgfSBjYXRjaCAoIF8gKSB7fVxyXG5cdFx0VUkuX3B1bHNlX21ldGEuc2V0KCBlbCwgbWV0YSApO1xyXG5cdFx0dHJ5IHsgY2xlYXJUaW1lb3V0KCBtZXRhLmRlYm91bmNlX2lkICk7IH0gY2F0Y2ggKCBfICkge31cclxuXHRcdG1ldGEuZGVib3VuY2VfaWQgPSAwO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEZvcmNlLXJlc3RhcnQgYSBDU1MgYW5pbWF0aW9uIG9uIGEgY2xhc3MuXHJcblx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZWxcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gY2xzXHJcblx0ICovXHJcblx0VUkuX3Jlc3RhcnRfY3NzX2FuaW1hdGlvbiA9IGZ1bmN0aW9uIChlbCwgY2xzKSB7XHJcblx0XHRpZiAoICEgZWwgKSB7IHJldHVybjsgfVxyXG5cdFx0dHJ5IHtcclxuXHRcdFx0ZWwuY2xhc3NMaXN0LnJlbW92ZSggY2xzICk7XHJcblx0XHR9IGNhdGNoICggXyApIHt9XHJcblx0XHQvLyBGb3JjZSByZWZsb3cgc28gdGhlIG5leHQgYWRkKCkgcmV0cmlnZ2VycyB0aGUga2V5ZnJhbWVzLlxyXG5cdFx0dm9pZCBlbC5vZmZzZXRXaWR0aDtcclxuXHRcdHRyeSB7XHJcblx0XHRcdGVsLmNsYXNzTGlzdC5hZGQoIGNscyApO1xyXG5cdFx0fSBjYXRjaCAoIF8gKSB7fVxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdFx0U2luZ2xlIHB1bHNlIChiYWNrLWNvbXBhdCkuXHJcblx0XHRAcGFyYW0ge0hUTUxFbGVtZW50fSBlbFxyXG5cdFx0QHBhcmFtIHtudW1iZXJ9IGR1cl9tc1xyXG5cdCAqL1xyXG5cdFVJLnB1bHNlX29uY2UgPSBmdW5jdGlvbiAoZWwsIGR1cl9tcykge1xyXG5cdFx0aWYgKCAhIGVsICkgeyByZXR1cm47IH1cclxuXHRcdHZhciBjbHMgPSAnd3BiY19iZmJfX3Njcm9sbC1wdWxzZSc7XHJcblx0XHR2YXIgbXMgID0gTnVtYmVyLmlzRmluaXRlKCBkdXJfbXMgKSA/IGR1cl9tcyA6IDcwMDtcclxuXHJcblx0XHRVSS5jYW5jZWxfcHVsc2UoIGVsICk7XHJcblxyXG5cdFx0dmFyIG1ldGEgID0gVUkuX3B1bHNlX21ldGEuZ2V0KCBlbCApIHx8IHt9O1xyXG5cdFx0dmFyIHRva2VuID0gKE51bWJlci5pc0Zpbml0ZSggbWV0YS50b2tlbiApID8gbWV0YS50b2tlbiA6IDApICsgMTtcclxuXHRcdG1ldGEudG9rZW4gPSB0b2tlbjtcclxuXHRcdFVJLl9wdWxzZV9tZXRhLnNldCggZWwsIG1ldGEgKTtcclxuXHJcblx0XHRVSS5fcmVzdGFydF9jc3NfYW5pbWF0aW9uKCBlbCwgY2xzICk7XHJcblx0XHR2YXIgdCA9IHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0Ly8gaWdub3JlIGlmIGEgbmV3ZXIgcHVsc2Ugc3RhcnRlZC5cclxuXHRcdFx0dmFyIG0gPSBVSS5fcHVsc2VfbWV0YS5nZXQoIGVsICkgfHwge307XHJcblx0XHRcdGlmICggbS50b2tlbiAhPT0gdG9rZW4gKSB7IHJldHVybjsgfVxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGVsLmNsYXNzTGlzdC5yZW1vdmUoIGNscyApO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHt9XHJcblx0XHRcdFVJLl9wdWxzZV90aW1lcnMuZGVsZXRlKCBlbCApO1xyXG5cdFx0fSwgbXMgKTtcclxuXHRcdFVJLl9wdWxzZV90aW1lcnMuc2V0KCBlbCwgdCApO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdFx0TXVsdGktYmxpbmsgc2VxdWVuY2Ugd2l0aCBvcHRpb25hbCBwZXItY2FsbCBjb2xvciBvdmVycmlkZS5cclxuXHRcdEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsXHJcblx0XHRAcGFyYW0ge251bWJlcn0gW3RpbWVzPTNdXHJcblx0XHRAcGFyYW0ge251bWJlcn0gW29uX21zPTI4MF1cclxuXHRcdEBwYXJhbSB7bnVtYmVyfSBbb2ZmX21zPTE4MF1cclxuXHRcdEBwYXJhbSB7c3RyaW5nfSBbaGV4X2NvbG9yXSBPcHRpb25hbCBDU1MgY29sb3IgKGUuZy4gJyNmZjRkNGYnIG9yICdyZ2IoLi4uKScpLlxyXG5cdCAqL1xyXG5cdFVJLnB1bHNlX3NlcXVlbmNlID0gZnVuY3Rpb24gKGVsLCB0aW1lcywgb25fbXMsIG9mZl9tcywgaGV4X2NvbG9yKSB7XHJcblx0XHRpZiAoICFlbCB8fCAhZC5ib2R5LmNvbnRhaW5zKCBlbCApICkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHR2YXIgY2xzICAgPSAnd3BiY19iZmJfX2hpZ2hsaWdodC1wdWxzZSc7XHJcblx0XHR2YXIgY291bnQgPSBOdW1iZXIuaXNGaW5pdGUoIHRpbWVzICkgPyB0aW1lcyA6IDI7XHJcblx0XHR2YXIgb24gICAgPSBOdW1iZXIuaXNGaW5pdGUoIG9uX21zICkgPyBvbl9tcyA6IDI4MDtcclxuXHRcdHZhciBvZmYgICA9IE51bWJlci5pc0Zpbml0ZSggb2ZmX21zICkgPyBvZmZfbXMgOiAxODA7XHJcblxyXG5cdFx0Ly8gVGhyb3R0bGU6IGF2b2lkIHJlZmxvdyBzcGFtIGlmIGNhbGxlZCByZXBlYXRlZGx5IHdoaWxlIHR5cGluZy9kcmFnZ2luZy5cclxuXHRcdHZhciBtZXRhID0gVUkuX3B1bHNlX21ldGEuZ2V0KCBlbCApIHx8IHt9O1xyXG5cdFx0dmFyIG5vdyAgPSBEYXRlLm5vdygpO1xyXG5cdFx0dmFyIHRocm90dGxlX21zID0gTnVtYmVyLmlzRmluaXRlKCBVSS5QVUxTRV9USFJPVFRMRV9NUyApID8gVUkuUFVMU0VfVEhST1RUTEVfTVMgOiAxMjA7XHJcblx0XHRpZiAoIE51bWJlci5pc0Zpbml0ZSggbWV0YS5sYXN0X3RzICkgJiYgKG5vdyAtIG1ldGEubGFzdF90cykgPCB0aHJvdHRsZV9tcyApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cdFx0bWV0YS5sYXN0X3RzID0gbm93O1xyXG5cclxuXHRcdC8vIGNhbmNlbCBhbnkgcnVubmluZyBwdWxzZSBhbmQgcmVzZXQgY2xhc3MgKHRva2VuIGludmFsaWRhdGlvbikuXHJcblx0XHRVSS5jYW5jZWxfcHVsc2UoIGVsICk7XHJcblxyXG5cdFx0Ly8gbmV3IHRva2VuIGZvciB0aGlzIHJ1blxyXG5cdFx0dmFyIHRva2VuID0gKE51bWJlci5pc0Zpbml0ZSggbWV0YS50b2tlbiApID8gbWV0YS50b2tlbiA6IDApICsgMTtcclxuXHRcdG1ldGEudG9rZW4gPSB0b2tlbjtcclxuXHJcblx0XHR2YXIgaGF2ZV9jb2xvciA9ICEhaGV4X2NvbG9yICYmIHR5cGVvZiBoZXhfY29sb3IgPT09ICdzdHJpbmcnO1xyXG5cdFx0aWYgKCBoYXZlX2NvbG9yICkge1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGVsLnN0eWxlLnNldFByb3BlcnR5KCAnLS13cGJjLWJmYi1wdWxzZS1jb2xvcicsIGhleF9jb2xvciApO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHt9XHJcblx0XHRcdG1ldGEuY29sb3Jfc2V0ID0gdHJ1ZTtcclxuXHRcdH1cclxuXHRcdFVJLl9wdWxzZV9tZXRhLnNldCggZWwsIG1ldGEgKTtcclxuXHJcblx0XHR2YXIgaSA9IDA7XHJcblx0XHQoZnVuY3Rpb24gdGljaygpIHtcclxuXHRcdFx0dmFyIG0gPSBVSS5fcHVsc2VfbWV0YS5nZXQoIGVsICkgfHwge307XHJcblx0XHRcdGlmICggbS50b2tlbiAhPT0gdG9rZW4gKSB7XHJcblx0XHRcdFx0Ly8gY2FuY2VsZWQvcmVwbGFjZWRcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBpID49IGNvdW50ICkge1xyXG5cdFx0XHRcdFVJLl9wdWxzZV90aW1lcnMuZGVsZXRlKCBlbCApO1xyXG5cdFx0XHRcdGlmICggaGF2ZV9jb2xvciApIHtcclxuXHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdGVsLnN0eWxlLnJlbW92ZVByb3BlcnR5KCAnLS13cGJjLWJmYi1wdWxzZS1jb2xvcicgKTtcclxuXHRcdFx0XHRcdH0gY2F0Y2ggKCBfICkge31cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdFVJLl9yZXN0YXJ0X2Nzc19hbmltYXRpb24oIGVsLCBjbHMgKTtcclxuXHRcdFx0VUkuX3B1bHNlX3RpbWVycy5zZXQoIGVsLCBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKSB7ICAgICAvLyBPTiAtPiBPRkZcclxuXHRcdFx0XHR2YXIgbTIgPSBVSS5fcHVsc2VfbWV0YS5nZXQoIGVsICkgfHwge307XHJcblx0XHRcdFx0aWYgKCBtMi50b2tlbiAhPT0gdG9rZW4gKSB7IHJldHVybjsgfVxyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRlbC5jbGFzc0xpc3QucmVtb3ZlKCBjbHMgKTtcclxuXHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0VUkuX3B1bHNlX3RpbWVycy5zZXQoIGVsLCBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKSB7IC8vIE9GRiBnYXAgLT4gbmV4dFxyXG5cdFx0XHRcdFx0dmFyIG0zID0gVUkuX3B1bHNlX21ldGEuZ2V0KCBlbCApIHx8IHt9O1xyXG5cdFx0XHRcdFx0aWYgKCBtMy50b2tlbiAhPT0gdG9rZW4gKSB7IHJldHVybjsgfVxyXG5cdFx0XHRcdFx0aSsrO1xyXG5cdFx0XHRcdFx0dGljaygpO1xyXG5cdFx0XHRcdH0sIG9mZiApICk7XHJcblx0XHRcdH0sIG9uICkgKTtcclxuXHRcdH0pKCk7XHJcblx0fTtcclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIERlYm91bmNlZCBxdWVyeSArIHB1bHNlLlxyXG5cdCAqIFVzZWZ1bCBmb3IgYGlucHV0YCBldmVudHMgKHNsaWRlcnMgLyB0eXBpbmcpIHRvIGF2b2lkIGZvcmNlZCByZWZsb3cgc3BhbS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR8c3RyaW5nfSByb290X29yX3NlbGVjdG9yXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IHNlbGVjdG9yXHJcblx0ICogQHBhcmFtIHtudW1iZXJ9IHdhaXRfbXNcclxuXHQgKiBAcGFyYW0ge251bWJlcn0gW2FdXHJcblx0ICogQHBhcmFtIHtudW1iZXJ9IFtiXVxyXG5cdCAqIEBwYXJhbSB7bnVtYmVyfSBbY11cclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gW2NvbG9yXVxyXG5cdCAqL1xyXG5cdFVJLnB1bHNlX3F1ZXJ5X2RlYm91bmNlZCA9IGZ1bmN0aW9uIChyb290X29yX3NlbGVjdG9yLCBzZWxlY3Rvciwgd2FpdF9tcywgYSwgYiwgYywgY29sb3IpIHtcclxuXHRcdHZhciByb290ID0gKHR5cGVvZiByb290X29yX3NlbGVjdG9yID09PSAnc3RyaW5nJykgPyBkIDogKHJvb3Rfb3Jfc2VsZWN0b3IgfHwgZCk7XHJcblx0XHR2YXIgc2VsICA9ICh0eXBlb2Ygcm9vdF9vcl9zZWxlY3RvciA9PT0gJ3N0cmluZycpID8gcm9vdF9vcl9zZWxlY3RvciA6IHNlbGVjdG9yO1xyXG5cdFx0aWYgKCAhc2VsICkgeyByZXR1cm47IH1cclxuXHRcdHZhciBlbCA9IHJvb3QucXVlcnlTZWxlY3Rvciggc2VsICk7XHJcblx0XHRpZiAoICFlbCApIHsgcmV0dXJuOyB9XHJcblxyXG5cdFx0dmFyIGRlZl9tcyA9IE51bWJlci5pc0Zpbml0ZSggVUkuUFVMU0VfREVCT1VOQ0VfTVMgKSA/IFVJLlBVTFNFX0RFQk9VTkNFX01TIDogMTIwO1xyXG5cdFx0dmFyIG1zICAgICA9IE51bWJlci5pc0Zpbml0ZSggd2FpdF9tcyApID8gd2FpdF9tcyA6IGRlZl9tcztcclxuXHRcdHZhciBtZXRhID0gVUkuX3B1bHNlX21ldGEuZ2V0KCBlbCApIHx8IHt9O1xyXG5cdFx0dHJ5IHsgY2xlYXJUaW1lb3V0KCBtZXRhLmRlYm91bmNlX2lkICk7IH0gY2F0Y2ggKCBfICkge31cclxuXHRcdG1ldGEuZGVib3VuY2VfaWQgPSBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFVJLnB1bHNlX3NlcXVlbmNlKCBlbCwgYSwgYiwgYywgY29sb3IgKTtcclxuXHRcdH0sIG1zICk7XHJcblx0XHRVSS5fcHVsc2VfbWV0YS5zZXQoIGVsLCBtZXRhICk7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0XHRRdWVyeSArIHB1bHNlOlxyXG5cdFx0KEJDKSBJZiBvbmx5IDNyZCBhcmcgaXMgYSBudW1iZXIgYW5kIG5vIDR0aC81dGggLT4gc2luZ2xlIGxvbmcgcHVsc2UuXHJcblx0XHRPdGhlcndpc2UgLT4gc3Ryb25nIHNlcXVlbmNlIChkZWZhdWx0cyAzw5cyODAvMTgwKS5cclxuXHRcdE9wdGlvbmFsIDZ0aCBhcmc6IGNvbG9yLlxyXG5cdFx0QHBhcmFtIHtIVE1MRWxlbWVudHxzdHJpbmd9IHJvb3Rfb3Jfc2VsZWN0b3JcclxuXHRcdEBwYXJhbSB7c3RyaW5nfSBbc2VsZWN0b3JdXHJcblx0XHRAcGFyYW0ge251bWJlcn0gW2FdXHJcblx0XHRAcGFyYW0ge251bWJlcn0gW2JdXHJcblxyXG5cdFx0QHBhcmFtIHtudW1iZXJ9IFtjXVxyXG5cclxuXHRcdEBwYXJhbSB7c3RyaW5nfSBbY29sb3JdXHJcblx0ICovXHJcblx0VUkucHVsc2VfcXVlcnkgPSBmdW5jdGlvbiAocm9vdF9vcl9zZWxlY3Rvciwgc2VsZWN0b3IsIGEsIGIsIGMsIGNvbG9yKSB7XHJcblx0XHR2YXIgcm9vdCA9ICh0eXBlb2Ygcm9vdF9vcl9zZWxlY3RvciA9PT0gJ3N0cmluZycpID8gZCA6IChyb290X29yX3NlbGVjdG9yIHx8IGQpO1xyXG5cdFx0dmFyIHNlbCAgPSAodHlwZW9mIHJvb3Rfb3Jfc2VsZWN0b3IgPT09ICdzdHJpbmcnKSA/IHJvb3Rfb3Jfc2VsZWN0b3IgOiBzZWxlY3RvcjtcclxuXHRcdGlmICggIXNlbCApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdHZhciBlbCA9IHJvb3QucXVlcnlTZWxlY3Rvciggc2VsICk7XHJcblx0XHRpZiAoICFlbCApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuLy8gQmFjay1jb21wYXQ6IFVJLnB1bHNlUXVlcnkocm9vdCwgc2VsLCBkdXJfbXMpXHJcblx0XHRpZiAoIE51bWJlci5pc0Zpbml0ZSggYSApICYmIGIgPT09IHVuZGVmaW5lZCAmJiBjID09PSB1bmRlZmluZWQgKSB7XHJcblx0XHRcdHJldHVybiBVSS5wdWxzZV9vbmNlKCBlbCwgYSApO1xyXG5cdFx0fVxyXG4vLyBOZXc6IHNlcXVlbmNlOyBwYXJhbXMgb3B0aW9uYWw7IHN1cHBvcnRzIG9wdGlvbmFsIGNvbG9yLlxyXG5cdFx0VUkucHVsc2Vfc2VxdWVuY2UoIGVsLCBhLCBiLCBjLCBjb2xvciApO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdENvbnZlbmllbmNlIGhlbHBlciAoc25ha2VfY2FzZSkgdG8gY2FsbCBhIHN0cm9uZyBwdWxzZSB3aXRoIG9wdGlvbnMuXHJcblxyXG5cdEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsXHJcblxyXG5cdEBwYXJhbSB7T2JqZWN0fSBbb3B0c11cclxuXHJcblx0QHBhcmFtIHtudW1iZXJ9IFtvcHRzLnRpbWVzPTNdXHJcblxyXG5cdEBwYXJhbSB7bnVtYmVyfSBbb3B0cy5vbl9tcz0yODBdXHJcblxyXG5cdEBwYXJhbSB7bnVtYmVyfSBbb3B0cy5vZmZfbXM9MTgwXVxyXG5cclxuXHRAcGFyYW0ge3N0cmluZ30gW29wdHMuY29sb3JdXHJcblx0ICovXHJcblx0VUkucHVsc2Vfc2VxdWVuY2Vfc3Ryb25nID0gZnVuY3Rpb24gKGVsLCBvcHRzKSB7XHJcblx0XHRvcHRzID0gb3B0cyB8fCB7fTtcclxuXHRcdFVJLnB1bHNlX3NlcXVlbmNlKFxyXG5cdFx0XHRlbCxcclxuXHRcdFx0TnVtYmVyLmlzRmluaXRlKCBvcHRzLnRpbWVzICkgPyBvcHRzLnRpbWVzIDogMyxcclxuXHRcdFx0TnVtYmVyLmlzRmluaXRlKCBvcHRzLm9uX21zICkgPyBvcHRzLm9uX21zIDogMjgwLFxyXG5cdFx0XHROdW1iZXIuaXNGaW5pdGUoIG9wdHMub2ZmX21zICkgPyBvcHRzLm9mZl9tcyA6IDE4MCxcclxuXHRcdFx0b3B0cy5jb2xvclxyXG5cdFx0KTtcclxuXHR9O1xyXG5cclxuXHJcblx0LyoqXHJcblx0ICogQmFzZSBjbGFzcyBmb3IgQkZCIG1vZHVsZXMuXHJcblx0ICovXHJcblx0VUkuV1BCQ19CRkJfTW9kdWxlID0gY2xhc3Mge1xyXG5cdFx0LyoqIEBwYXJhbSB7V1BCQ19Gb3JtX0J1aWxkZXJ9IGJ1aWxkZXIgKi9cclxuXHRcdGNvbnN0cnVjdG9yKGJ1aWxkZXIpIHtcclxuXHRcdFx0dGhpcy5idWlsZGVyID0gYnVpbGRlcjtcclxuXHRcdH1cclxuXHJcblx0XHQvKiogSW5pdGlhbGl6ZSB0aGUgbW9kdWxlLiAqL1xyXG5cdFx0aW5pdCgpIHtcclxuXHRcdH1cclxuXHJcblx0XHQvKiogQ2xlYW51cCB0aGUgbW9kdWxlLiAqL1xyXG5cdFx0ZGVzdHJveSgpIHtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBDZW50cmFsIG92ZXJsYXkvY29udHJvbHMgbWFuYWdlciBmb3IgZmllbGRzL3NlY3Rpb25zLlxyXG5cdCAqIFB1cmUgVUkgY29tcG9zaXRpb247IGFsbCBhY3Rpb25zIHJvdXRlIGJhY2sgaW50byB0aGUgYnVpbGRlciBpbnN0YW5jZS5cclxuXHQgKi9cclxuXHRVSS5XUEJDX0JGQl9PdmVybGF5ID0gY2xhc3Mge1xyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogRW5zdXJlIGFuIG92ZXJsYXkgZXhpc3RzIGFuZCBpcyB3aXJlZCB1cCBvbiB0aGUgZWxlbWVudC5cclxuXHRcdCAqIEBwYXJhbSB7V1BCQ19Gb3JtX0J1aWxkZXJ9IGJ1aWxkZXJcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGVsIC0gZmllbGQgb3Igc2VjdGlvbiBlbGVtZW50XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBlbnN1cmUoYnVpbGRlciwgZWwpIHtcclxuXHJcblx0XHRcdGlmICggIWVsICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCBpc1NlY3Rpb24gPSBlbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fc2VjdGlvbicgKTtcclxuXHJcblx0XHRcdC8vIGxldCBvdmVybGF5ID0gZWwucXVlcnlTZWxlY3RvciggQ29yZS5XUEJDX0JGQl9ET00uU0VMRUNUT1JTLm92ZXJsYXkgKTtcclxuXHRcdFx0bGV0IG92ZXJsYXkgPSBlbC5xdWVyeVNlbGVjdG9yKCBgOnNjb3BlID4gJHtDb3JlLldQQkNfQkZCX0RPTS5TRUxFQ1RPUlMub3ZlcmxheX1gICk7XHJcblx0XHRcdGlmICggIW92ZXJsYXkgKSB7XHJcblx0XHRcdFx0b3ZlcmxheSA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLmNyZWF0ZV9lbGVtZW50KCAnZGl2JywgJ3dwYmNfYmZiX19vdmVybGF5LWNvbnRyb2xzJyApO1xyXG5cdFx0XHRcdGVsLnByZXBlbmQoIG92ZXJsYXkgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gRHJhZyBoYW5kbGUuXHJcblx0XHRcdGlmICggIW92ZXJsYXkucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fZHJhZy1oYW5kbGUnICkgKSB7XHJcblx0XHRcdFx0Y29uc3QgZHJhZ0NsYXNzID0gaXNTZWN0aW9uID8gJ3dwYmNfYmZiX19kcmFnLWhhbmRsZSBzZWN0aW9uLWRyYWctaGFuZGxlJyA6ICd3cGJjX2JmYl9fZHJhZy1oYW5kbGUnO1xyXG5cdFx0XHRcdG92ZXJsYXkuYXBwZW5kQ2hpbGQoXHJcblx0XHRcdFx0XHRDb3JlLldQQkNfRm9ybV9CdWlsZGVyX0hlbHBlci5jcmVhdGVfZWxlbWVudCggJ3NwYW4nLCBkcmFnQ2xhc3MsICc8c3BhbiBjbGFzcz1cIndwYmNfaWNuX2RyYWdfaW5kaWNhdG9yXCI+PC9zcGFuPicgKVxyXG5cdFx0XHRcdCk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIFNFVFRJTkdTIGJ1dHRvbiAoc2hvd24gZm9yIGJvdGggZmllbGRzICYgc2VjdGlvbnMpLlxyXG5cdFx0XHRpZiAoICFvdmVybGF5LnF1ZXJ5U2VsZWN0b3IoICcud3BiY19iZmJfX3NldHRpbmdzLWJ0bicgKSApIHtcclxuXHRcdFx0XHRjb25zdCBzZXR0aW5nc19idG4gICA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLmNyZWF0ZV9lbGVtZW50KCAnYnV0dG9uJywgJ3dwYmNfYmZiX19zZXR0aW5ncy1idG4nLCAnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9zZXR0aW5nc1wiPjwvaT4nICk7XHJcblx0XHRcdFx0c2V0dGluZ3NfYnRuLnR5cGUgICAgPSAnYnV0dG9uJztcclxuXHRcdFx0XHRzZXR0aW5nc19idG4udGl0bGUgICA9ICdPcGVuIHNldHRpbmdzJztcclxuXHRcdFx0XHRzZXR0aW5nc19idG4ub25jbGljayA9IChlKSA9PiB7XHJcblx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0XHQvLyBTZWxlY3QgVEhJUyBlbGVtZW50IGFuZCBzY3JvbGwgaXQgaW50byB2aWV3LlxyXG5cdFx0XHRcdFx0YnVpbGRlci5zZWxlY3RfZmllbGQoIGVsLCB7IHNjcm9sbEludG9WaWV3OiB0cnVlIH0gKTtcclxuXHJcblx0XHRcdFx0XHQvLyBBdXRvLW9wZW4gSW5zcGVjdG9yIGZyb20gdGhlIG92ZXJsYXkg4oCcU2V0dGluZ3PigJ0gYnV0dG9uLlxyXG5cdFx0XHRcdFx0ZG9jdW1lbnQuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmI6c2hvd19wYW5lbCcsIHtcclxuXHRcdFx0XHRcdFx0ZGV0YWlsOiB7XHJcblx0XHRcdFx0XHRcdFx0cGFuZWxfaWQ6ICd3cGJjX2JmYl9faW5zcGVjdG9yJyxcclxuXHRcdFx0XHRcdFx0XHR0YWJfaWQgIDogJ3dwYmNfdGFiX2luc3BlY3RvcidcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSApICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gVHJ5IHRvIGJyaW5nIHRoZSBpbnNwZWN0b3IgaW50byB2aWV3IC8gZm9jdXMgZmlyc3QgaW5wdXQuXHJcblx0XHRcdFx0XHRjb25zdCBpbnMgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwYmNfYmZiX19pbnNwZWN0b3InICk7XHJcblx0XHRcdFx0XHRpZiAoIGlucyApIHtcclxuXHRcdFx0XHRcdFx0aW5zLnNjcm9sbEludG9WaWV3KCB7IGJlaGF2aW9yOiAnc21vb3RoJywgYmxvY2s6ICduZWFyZXN0JyB9ICk7XHJcblx0XHRcdFx0XHRcdC8vIEZvY3VzIGZpcnN0IGludGVyYWN0aXZlIGNvbnRyb2wgKGJlc3QtZWZmb3J0KS5cclxuXHRcdFx0XHRcdFx0c2V0VGltZW91dCggKCkgPT4ge1xyXG5cdFx0XHRcdFx0XHRcdGNvbnN0IGZvY3VzYWJsZSA9IGlucy5xdWVyeVNlbGVjdG9yKCAnaW5wdXQsc2VsZWN0LHRleHRhcmVhLGJ1dHRvbixbY29udGVudGVkaXRhYmxlXSxbdGFiaW5kZXhdOm5vdChbdGFiaW5kZXg9XCItMVwiXSknICk7XHJcblx0XHRcdFx0XHRcdFx0Zm9jdXNhYmxlPy5mb2N1cz8uKCk7XHJcblx0XHRcdFx0XHRcdH0sIDI2MCApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH07XHJcblxyXG5cdFx0XHRcdG92ZXJsYXkuYXBwZW5kQ2hpbGQoIHNldHRpbmdzX2J0biApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRvdmVybGF5LnNldEF0dHJpYnV0ZSggJ3JvbGUnLCAndG9vbGJhcicgKTtcclxuXHRcdFx0b3ZlcmxheS5zZXRBdHRyaWJ1dGUoICdhcmlhLWxhYmVsJywgZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgPyAnU2VjdGlvbiB0b29scycgOiAnRmllbGQgdG9vbHMnICk7XHJcblxyXG5cdFx0XHRyZXR1cm4gb3ZlcmxheTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBXUEJDIExheW91dCBDaGlwcyBoZWxwZXIgLSB2aXN1YWwgbGF5b3V0IHBpY2tlciAoY2hpcHMpLCBlLmcuLCBcIjUwJS81MCVcIiwgdG8gYSBzZWN0aW9uIG92ZXJsYXkuXHJcblx0ICpcclxuXHQgKiBSZW5kZXJzIEVxdWFsL1ByZXNldHMvQ3VzdG9tIGNoaXBzIGludG8gYSBob3N0IGNvbnRhaW5lciBhbmQgd2lyZXMgdGhlbSB0byBhcHBseSB0aGUgbGF5b3V0LlxyXG5cdCAqL1xyXG5cdFVJLldQQkNfQkZCX0xheW91dF9DaGlwcyA9IGNsYXNzIHtcclxuXHJcblx0XHQvKiogUmVhZCBwZXItY29sdW1uIG1pbiAocHgpIGZyb20gQ1NTIHZhciBzZXQgYnkgdGhlIGd1YXJkLiAqL1xyXG5cdFx0c3RhdGljIF9nZXRfY29sX21pbl9weChjb2wpIHtcclxuXHRcdFx0Y29uc3QgdiA9IGdldENvbXB1dGVkU3R5bGUoIGNvbCApLmdldFByb3BlcnR5VmFsdWUoICctLXdwYmMtY29sLW1pbicgKSB8fCAnMCc7XHJcblx0XHRcdGNvbnN0IG4gPSBwYXJzZUZsb2F0KCB2ICk7XHJcblx0XHRcdHJldHVybiBOdW1iZXIuaXNGaW5pdGUoIG4gKSA/IE1hdGgubWF4KCAwLCBuICkgOiAwO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogVHVybiByYXcgd2VpZ2h0cyAoZS5nLiBbMSwxXSwgWzIsMSwxXSkgaW50byBlZmZlY3RpdmUgXCJhdmFpbGFibGUtJVwiIGJhc2VzIHRoYXRcclxuXHRcdCAqIChhKSBzdW0gdG8gdGhlIHJvdydzIGF2YWlsYWJsZSAlLCBhbmQgKGIpIG1lZXQgZXZlcnkgY29sdW1uJ3MgbWluIHB4LlxyXG5cdFx0ICogUmV0dXJucyBhbiBhcnJheSBvZiBiYXNlcyAobnVtYmVycykgb3IgbnVsbCBpZiBpbXBvc3NpYmxlIHRvIHNhdGlzZnkgbWlucy5cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIF9maXRfd2VpZ2h0c19yZXNwZWN0aW5nX21pbihidWlsZGVyLCByb3csIHdlaWdodHMpIHtcclxuXHRcdFx0Y29uc3QgY29scyA9IEFycmF5LmZyb20oIHJvdy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkgKTtcclxuXHRcdFx0Y29uc3QgbiAgICA9IGNvbHMubGVuZ3RoO1xyXG5cdFx0XHRpZiAoICFuICkgcmV0dXJuIG51bGw7XHJcblx0XHRcdGlmICggIUFycmF5LmlzQXJyYXkoIHdlaWdodHMgKSB8fCB3ZWlnaHRzLmxlbmd0aCAhPT0gbiApIHJldHVybiBudWxsO1xyXG5cclxuXHRcdFx0Ly8gYXZhaWxhYmxlICUgYWZ0ZXIgZ2FwcyAoZnJvbSBMYXlvdXRTZXJ2aWNlKVxyXG5cdFx0XHRjb25zdCBncCAgICAgICA9IGJ1aWxkZXIuY29sX2dhcF9wZXJjZW50O1xyXG5cdFx0XHRjb25zdCBlZmYgICAgICA9IGJ1aWxkZXIubGF5b3V0LmNvbXB1dGVfZWZmZWN0aXZlX2Jhc2VzX2Zyb21fcm93KCByb3csIGdwICk7XHJcblx0XHRcdGNvbnN0IGF2YWlsUGN0ID0gZWZmLmF2YWlsYWJsZTsgICAgICAgICAgICAgICAvLyBlLmcuIDk0IGlmIDIgY29scyBhbmQgMyUgZ2FwXHJcblx0XHRcdGNvbnN0IHJvd1B4ICAgID0gcm93LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLndpZHRoO1xyXG5cdFx0XHRjb25zdCBhdmFpbFB4ICA9IHJvd1B4ICogKGF2YWlsUGN0IC8gMTAwKTtcclxuXHJcblx0XHRcdC8vIGNvbGxlY3QgbWluaW1hIGluICUgb2YgXCJhdmFpbGFibGVcIlxyXG5cdFx0XHRjb25zdCBtaW5QY3QgPSBjb2xzLm1hcCggKGMpID0+IHtcclxuXHRcdFx0XHRjb25zdCBtaW5QeCA9IFVJLldQQkNfQkZCX0xheW91dF9DaGlwcy5fZ2V0X2NvbF9taW5fcHgoIGMgKTtcclxuXHRcdFx0XHRpZiAoIGF2YWlsUHggPD0gMCApIHJldHVybiAwO1xyXG5cdFx0XHRcdHJldHVybiAobWluUHggLyBhdmFpbFB4KSAqIGF2YWlsUGN0O1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHQvLyBJZiBtaW5zIGFsb25lIGRvbid0IGZpdCwgYmFpbC5cclxuXHRcdFx0Y29uc3Qgc3VtTWluID0gbWluUGN0LnJlZHVjZSggKGEsIGIpID0+IGEgKyBiLCAwICk7XHJcblx0XHRcdGlmICggc3VtTWluID4gYXZhaWxQY3QgLSAxZS02ICkge1xyXG5cdFx0XHRcdHJldHVybiBudWxsOyAvLyBpbXBvc3NpYmxlIHRvIHJlc3BlY3QgbWluczsgZG9uJ3QgYXBwbHkgcHJlc2V0XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIFRhcmdldCBwZXJjZW50YWdlcyBmcm9tIHdlaWdodHMsIG5vcm1hbGl6ZWQgdG8gYXZhaWxQY3QuXHJcblx0XHRcdGNvbnN0IHdTdW0gICAgICA9IHdlaWdodHMucmVkdWNlKCAoYSwgdykgPT4gYSArIChOdW1iZXIoIHcgKSB8fCAwKSwgMCApIHx8IG47XHJcblx0XHRcdGNvbnN0IHRhcmdldFBjdCA9IHdlaWdodHMubWFwKCAodykgPT4gKChOdW1iZXIoIHcgKSB8fCAwKSAvIHdTdW0pICogYXZhaWxQY3QgKTtcclxuXHJcblx0XHRcdC8vIExvY2sgY29sdW1ucyB0aGF0IHdvdWxkIGJlIGJlbG93IG1pbiwgdGhlbiBkaXN0cmlidXRlIHRoZSByZW1haW5kZXJcclxuXHRcdFx0Ly8gYWNyb3NzIHRoZSByZW1haW5pbmcgY29sdW1ucyBwcm9wb3J0aW9uYWxseSB0byB0aGVpciB0YXJnZXRQY3QuXHJcblx0XHRcdGNvbnN0IGxvY2tlZCAgPSBuZXcgQXJyYXkoIG4gKS5maWxsKCBmYWxzZSApO1xyXG5cdFx0XHRsZXQgbG9ja2VkU3VtID0gMDtcclxuXHRcdFx0Zm9yICggbGV0IGkgPSAwOyBpIDwgbjsgaSsrICkge1xyXG5cdFx0XHRcdGlmICggdGFyZ2V0UGN0W2ldIDwgbWluUGN0W2ldICkge1xyXG5cdFx0XHRcdFx0bG9ja2VkW2ldID0gdHJ1ZTtcclxuXHRcdFx0XHRcdGxvY2tlZFN1bSArPSBtaW5QY3RbaV07XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRsZXQgcmVtYWluaW5nICAgICA9IGF2YWlsUGN0IC0gbG9ja2VkU3VtO1xyXG5cdFx0XHRjb25zdCBmcmVlSWR4ICAgICA9IFtdO1xyXG5cdFx0XHRsZXQgZnJlZVRhcmdldFN1bSA9IDA7XHJcblx0XHRcdGZvciAoIGxldCBpID0gMDsgaSA8IG47IGkrKyApIHtcclxuXHRcdFx0XHRpZiAoICFsb2NrZWRbaV0gKSB7XHJcblx0XHRcdFx0XHRmcmVlSWR4LnB1c2goIGkgKTtcclxuXHRcdFx0XHRcdGZyZWVUYXJnZXRTdW0gKz0gdGFyZ2V0UGN0W2ldO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgcmVzdWx0ID0gbmV3IEFycmF5KCBuICkuZmlsbCggMCApO1xyXG5cdFx0XHQvLyBTZWVkIGxvY2tlZCB3aXRoIHRoZWlyIG1pbmltYS5cclxuXHRcdFx0Zm9yICggbGV0IGkgPSAwOyBpIDwgbjsgaSsrICkge1xyXG5cdFx0XHRcdGlmICggbG9ja2VkW2ldICkgcmVzdWx0W2ldID0gbWluUGN0W2ldO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIGZyZWVJZHgubGVuZ3RoID09PSAwICkge1xyXG5cdFx0XHRcdC8vIGV2ZXJ5dGhpbmcgbG9ja2VkIGV4YWN0bHkgYXQgbWluOyBhbnkgbGVmdG92ZXIgKHNob3VsZG4ndCBoYXBwZW4pXHJcblx0XHRcdFx0Ly8gd291bGQgYmUgaWdub3JlZCB0byBrZWVwIHNpbXBsaWNpdHkgYW5kIHN0YWJpbGl0eS5cclxuXHRcdFx0XHRyZXR1cm4gcmVzdWx0O1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIHJlbWFpbmluZyA8PSAwICkge1xyXG5cdFx0XHRcdC8vIG5vdGhpbmcgbGVmdCB0byBkaXN0cmlidXRlOyBrZWVwIGV4YWN0bHkgbWlucyBvbiBsb2NrZWQsXHJcblx0XHRcdFx0Ly8gbm90aGluZyBmb3IgZnJlZSAoZGVnZW5lcmF0ZSBidXQgY29uc2lzdGVudClcclxuXHRcdFx0XHRyZXR1cm4gcmVzdWx0O1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIGZyZWVUYXJnZXRTdW0gPD0gMCApIHtcclxuXHRcdFx0XHQvLyBkaXN0cmlidXRlIGVxdWFsbHkgYW1vbmcgZnJlZSBjb2x1bW5zXHJcblx0XHRcdFx0Y29uc3QgZWFjaCA9IHJlbWFpbmluZyAvIGZyZWVJZHgubGVuZ3RoO1xyXG5cdFx0XHRcdGZyZWVJZHguZm9yRWFjaCggKGkpID0+IChyZXN1bHRbaV0gPSBlYWNoKSApO1xyXG5cdFx0XHRcdHJldHVybiByZXN1bHQ7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIERpc3RyaWJ1dGUgcmVtYWluaW5nIHByb3BvcnRpb25hbGx5IHRvIGZyZWUgY29sdW1ucycgdGFyZ2V0UGN0XHJcblx0XHRcdGZyZWVJZHguZm9yRWFjaCggKGkpID0+IHtcclxuXHRcdFx0XHRyZXN1bHRbaV0gPSByZW1haW5pbmcgKiAodGFyZ2V0UGN0W2ldIC8gZnJlZVRhcmdldFN1bSk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0cmV0dXJuIHJlc3VsdDtcclxuXHRcdH1cclxuXHJcblx0XHQvKiogQXBwbHkgYSBwcmVzZXQgYnV0IGd1YXJkIGl0IGJ5IG1pbmltYTsgcmV0dXJucyB0cnVlIGlmIGFwcGxpZWQsIGZhbHNlIGlmIHNraXBwZWQuICovXHJcblx0XHRzdGF0aWMgX2FwcGx5X3ByZXNldF93aXRoX21pbl9ndWFyZChidWlsZGVyLCBzZWN0aW9uX2VsLCB3ZWlnaHRzKSB7XHJcblx0XHRcdGNvbnN0IHJvdyA9IHNlY3Rpb25fZWwucXVlcnlTZWxlY3RvciggJzpzY29wZSA+IC53cGJjX2JmYl9fcm93JyApO1xyXG5cdFx0XHRpZiAoICFyb3cgKSByZXR1cm4gZmFsc2U7XHJcblxyXG5cdFx0XHRjb25zdCBmaXR0ZWQgPSBVSS5XUEJDX0JGQl9MYXlvdXRfQ2hpcHMuX2ZpdF93ZWlnaHRzX3Jlc3BlY3RpbmdfbWluKCBidWlsZGVyLCByb3csIHdlaWdodHMgKTtcclxuXHRcdFx0aWYgKCAhZml0dGVkICkge1xyXG5cdFx0XHRcdGJ1aWxkZXI/Ll9hbm5vdW5jZT8uKCAnTm90IGVub3VnaCBzcGFjZSBmb3IgdGhpcyBsYXlvdXQgYmVjYXVzZSBvZiBmaWVsZHPigJkgbWluaW11bSB3aWR0aHMuJyApO1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gYGZpdHRlZGAgYWxyZWFkeSBzdW1zIHRvIHRoZSByb3figJlzIGF2YWlsYWJsZSAlLCBzbyB3ZSBjYW4gYXBwbHkgYmFzZXMgZGlyZWN0bHkuXHJcblx0XHRcdGJ1aWxkZXIubGF5b3V0LmFwcGx5X2Jhc2VzX3RvX3Jvdyggcm93LCBmaXR0ZWQgKTtcclxuXHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQnVpbGQgYW5kIGFwcGVuZCBsYXlvdXQgY2hpcHMgZm9yIGEgc2VjdGlvbi5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge1dQQkNfRm9ybV9CdWlsZGVyfSBidWlsZGVyIC0gVGhlIGZvcm0gYnVpbGRlciBpbnN0YW5jZS5cclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHNlY3Rpb25fZWwgLSBUaGUgLndwYmNfYmZiX19zZWN0aW9uIGVsZW1lbnQuXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBob3N0X2VsIC0gQ29udGFpbmVyIHdoZXJlIGNoaXBzIHNob3VsZCBiZSByZW5kZXJlZC5cclxuXHRcdCAqIEByZXR1cm5zIHt2b2lkfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgcmVuZGVyX2Zvcl9zZWN0aW9uKGJ1aWxkZXIsIHNlY3Rpb25fZWwsIGhvc3RfZWwpIHtcclxuXHJcblx0XHRcdGlmICggIWJ1aWxkZXIgfHwgIXNlY3Rpb25fZWwgfHwgIWhvc3RfZWwgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCByb3cgPSBzZWN0aW9uX2VsLnF1ZXJ5U2VsZWN0b3IoICc6c2NvcGUgPiAud3BiY19iZmJfX3JvdycgKTtcclxuXHRcdFx0aWYgKCAhcm93ICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgY29scyA9IHJvdy5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICkubGVuZ3RoIHx8IDE7XHJcblxyXG5cdFx0XHQvLyBDbGVhciBob3N0LlxyXG5cdFx0XHRob3N0X2VsLmlubmVySFRNTCA9ICcnO1xyXG5cclxuXHRcdFx0Ly8gRXF1YWwgY2hpcC5cclxuXHRcdFx0aG9zdF9lbC5hcHBlbmRDaGlsZChcclxuXHRcdFx0XHRVSS5XUEJDX0JGQl9MYXlvdXRfQ2hpcHMuX21ha2VfY2hpcCggYnVpbGRlciwgc2VjdGlvbl9lbCwgQXJyYXkoIGNvbHMgKS5maWxsKCAxICksICdFcXVhbCcgKVxyXG5cdFx0XHQpO1xyXG5cclxuXHRcdFx0Ly8gUHJlc2V0cyBiYXNlZCBvbiBjb2x1bW4gY291bnQuXHJcblx0XHRcdGNvbnN0IHByZXNldHMgPSBidWlsZGVyLmxheW91dC5idWlsZF9wcmVzZXRzX2Zvcl9jb2x1bW5zKCBjb2xzICk7XHJcblx0XHRcdHByZXNldHMuZm9yRWFjaCggKHdlaWdodHMpID0+IHtcclxuXHRcdFx0XHRob3N0X2VsLmFwcGVuZENoaWxkKFxyXG5cdFx0XHRcdFx0VUkuV1BCQ19CRkJfTGF5b3V0X0NoaXBzLl9tYWtlX2NoaXAoIGJ1aWxkZXIsIHNlY3Rpb25fZWwsIHdlaWdodHMsIG51bGwgKVxyXG5cdFx0XHRcdCk7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIEN1c3RvbSBjaGlwLlxyXG5cdFx0XHRjb25zdCBjdXN0b21CdG4gICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnYnV0dG9uJyApO1xyXG5cdFx0XHRjdXN0b21CdG4udHlwZSAgICAgICAgPSAnYnV0dG9uJztcclxuXHRcdFx0Y3VzdG9tQnRuLmNsYXNzTmFtZSAgID0gJ3dwYmNfYmZiX19sYXlvdXRfY2hpcCc7XHJcblx0XHRcdGN1c3RvbUJ0bi50ZXh0Q29udGVudCA9ICdDdXN0b23igKYnO1xyXG5cdFx0XHRjdXN0b21CdG4udGl0bGUgICAgICAgPSBgRW50ZXIgJHtjb2xzfSBwZXJjZW50YWdlc2A7XHJcblx0XHRcdGN1c3RvbUJ0bi5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCAoKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgZXhhbXBsZSA9IChjb2xzID09PSAyKSA/ICc1MCw1MCcgOiAoY29scyA9PT0gMyA/ICcyMCw2MCwyMCcgOiAnMjUsMjUsMjUsMjUnKTtcclxuXHRcdFx0XHRjb25zdCB0ZXh0ICAgID0gcHJvbXB0KCBgRW50ZXIgJHtjb2xzfSBwZXJjZW50YWdlcyAoY29tbWEgb3Igc3BhY2Ugc2VwYXJhdGVkKTpgLCBleGFtcGxlICk7XHJcblx0XHRcdFx0aWYgKCB0ZXh0ID09IG51bGwgKSByZXR1cm47XHJcblx0XHRcdFx0Y29uc3Qgd2VpZ2h0cyA9IGJ1aWxkZXIubGF5b3V0LnBhcnNlX3dlaWdodHMoIHRleHQgKTtcclxuXHRcdFx0XHRpZiAoIHdlaWdodHMubGVuZ3RoICE9PSBjb2xzICkge1xyXG5cdFx0XHRcdFx0YWxlcnQoIGBQbGVhc2UgZW50ZXIgZXhhY3RseSAke2NvbHN9IG51bWJlcnMuYCApO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHQvLyBPTEQ6XHJcblx0XHRcdFx0Ly8gYnVpbGRlci5sYXlvdXQuYXBwbHlfbGF5b3V0X3ByZXNldCggc2VjdGlvbl9lbCwgd2VpZ2h0cywgYnVpbGRlci5jb2xfZ2FwX3BlcmNlbnQgKTtcclxuXHRcdFx0XHQvLyBHdWFyZGVkIGFwcGx5Oi5cclxuXHRcdFx0XHRpZiAoICFVSS5XUEJDX0JGQl9MYXlvdXRfQ2hpcHMuX2FwcGx5X3ByZXNldF93aXRoX21pbl9ndWFyZCggYnVpbGRlciwgc2VjdGlvbl9lbCwgd2VpZ2h0cyApICkge1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRob3N0X2VsLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX2xheW91dF9jaGlwJyApLmZvckVhY2goIGMgPT4gYy5jbGFzc0xpc3QucmVtb3ZlKCAnaXMtYWN0aXZlJyApICk7XHJcblx0XHRcdFx0Y3VzdG9tQnRuLmNsYXNzTGlzdC5hZGQoICdpcy1hY3RpdmUnICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0aG9zdF9lbC5hcHBlbmRDaGlsZCggY3VzdG9tQnRuICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDcmVhdGUgYSBzaW5nbGUgbGF5b3V0IGNoaXAgYnV0dG9uLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKiBAcGFyYW0ge1dQQkNfRm9ybV9CdWlsZGVyfSBidWlsZGVyXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBzZWN0aW9uX2VsXHJcblx0XHQgKiBAcGFyYW0ge251bWJlcltdfSB3ZWlnaHRzXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ3xudWxsfSBsYWJlbFxyXG5cdFx0ICogQHJldHVybnMge0hUTUxCdXR0b25FbGVtZW50fVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgX21ha2VfY2hpcChidWlsZGVyLCBzZWN0aW9uX2VsLCB3ZWlnaHRzLCBsYWJlbCA9IG51bGwpIHtcclxuXHJcblx0XHRcdGNvbnN0IGJ0biAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnYnV0dG9uJyApO1xyXG5cdFx0XHRidG4udHlwZSAgICAgID0gJ2J1dHRvbic7XHJcblx0XHRcdGJ0bi5jbGFzc05hbWUgPSAnd3BiY19iZmJfX2xheW91dF9jaGlwJztcclxuXHJcblx0XHRcdGNvbnN0IHRpdGxlID0gbGFiZWwgfHwgYnVpbGRlci5sYXlvdXQuZm9ybWF0X3ByZXNldF9sYWJlbCggd2VpZ2h0cyApO1xyXG5cdFx0XHRidG4udGl0bGUgICA9IHRpdGxlO1xyXG5cclxuXHRcdFx0Ly8gVmlzdWFsIG1pbmlhdHVyZS5cclxuXHRcdFx0Y29uc3QgdmlzICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdkaXYnICk7XHJcblx0XHRcdHZpcy5jbGFzc05hbWUgPSAnd3BiY19iZmJfX2xheW91dF9jaGlwLXZpcyc7XHJcblx0XHRcdGNvbnN0IHN1bSAgICAgPSB3ZWlnaHRzLnJlZHVjZSggKGEsIGIpID0+IGEgKyAoTnVtYmVyKCBiICkgfHwgMCksIDAgKSB8fCAxO1xyXG5cdFx0XHR3ZWlnaHRzLmZvckVhY2goICh3KSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgYmFyICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnc3BhbicgKTtcclxuXHRcdFx0XHRiYXIuc3R5bGUuZmxleCA9IGAwIDAgY2FsYyggJHsoKE51bWJlciggdyApIHx8IDApIC8gc3VtICogMTAwKS50b0ZpeGVkKCAzICl9JSAtIDEuNXB4IClgO1xyXG5cdFx0XHRcdHZpcy5hcHBlbmRDaGlsZCggYmFyICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0YnRuLmFwcGVuZENoaWxkKCB2aXMgKTtcclxuXHJcblx0XHRcdGNvbnN0IHR4dCAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdzcGFuJyApO1xyXG5cdFx0XHR0eHQuY2xhc3NOYW1lICAgPSAnd3BiY19iZmJfX2xheW91dF9jaGlwLWxhYmVsJztcclxuXHRcdFx0dHh0LnRleHRDb250ZW50ID0gbGFiZWwgfHwgYnVpbGRlci5sYXlvdXQuZm9ybWF0X3ByZXNldF9sYWJlbCggd2VpZ2h0cyApO1xyXG5cdFx0XHRidG4uYXBwZW5kQ2hpbGQoIHR4dCApO1xyXG5cclxuXHRcdFx0YnRuLmFkZEV2ZW50TGlzdGVuZXIoICdjbGljaycsICgpID0+IHtcclxuXHRcdFx0XHQvLyBPTEQ6XHJcblx0XHRcdFx0Ly8gYnVpbGRlci5sYXlvdXQuYXBwbHlfbGF5b3V0X3ByZXNldCggc2VjdGlvbl9lbCwgd2VpZ2h0cywgYnVpbGRlci5jb2xfZ2FwX3BlcmNlbnQgKTtcclxuXHJcblx0XHRcdFx0Ly8gTkVXOlxyXG5cdFx0XHRcdGlmICggIVVJLldQQkNfQkZCX0xheW91dF9DaGlwcy5fYXBwbHlfcHJlc2V0X3dpdGhfbWluX2d1YXJkKCBidWlsZGVyLCBzZWN0aW9uX2VsLCB3ZWlnaHRzICkgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm47IC8vIGRvIG5vdCB0b2dnbGUgYWN0aXZlIGlmIHdlIGRpZG4ndCBjaGFuZ2UgbGF5b3V0XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRidG4ucGFyZW50RWxlbWVudD8ucXVlcnlTZWxlY3RvckFsbCggJy53cGJjX2JmYl9fbGF5b3V0X2NoaXAnICkuZm9yRWFjaCggYyA9PiBjLmNsYXNzTGlzdC5yZW1vdmUoICdpcy1hY3RpdmUnICkgKTtcclxuXHRcdFx0XHRidG4uY2xhc3NMaXN0LmFkZCggJ2lzLWFjdGl2ZScgKTtcclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0cmV0dXJuIGJ0bjtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBTZWxlY3Rpb24gY29udHJvbGxlciBmb3IgZmllbGRzIGFuZCBhbm5vdW5jZW1lbnRzLlxyXG5cdCAqL1xyXG5cdFVJLldQQkNfQkZCX1NlbGVjdGlvbl9Db250cm9sbGVyID0gY2xhc3MgZXh0ZW5kcyBVSS5XUEJDX0JGQl9Nb2R1bGUge1xyXG5cclxuXHRcdGluaXQoKSB7XHJcblxyXG5cdFx0XHR0aGlzLl9zZWxlY3RlZF91aWQgICAgICAgICAgICAgID0gbnVsbDtcclxuXHRcdFx0dGhpcy5idWlsZGVyLnNlbGVjdF9maWVsZCAgICAgICA9IHRoaXMuc2VsZWN0X2ZpZWxkLmJpbmQoIHRoaXMgKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyLmdldF9zZWxlY3RlZF9maWVsZCA9IHRoaXMuZ2V0X3NlbGVjdGVkX2ZpZWxkLmJpbmQoIHRoaXMgKTtcclxuXHRcdFx0dGhpcy5fb25fY2xlYXIgICAgICAgICAgICAgICAgICA9IHRoaXMub25fY2xlYXIuYmluZCggdGhpcyApO1xyXG5cclxuXHRcdFx0Ly8gQ2VudHJhbGl6ZWQgZGVsZXRlIGNvbW1hbmQgdXNlZCBieSBrZXlib2FyZCArIGluc3BlY3RvciArIG92ZXJsYXkuXHJcblx0XHRcdHRoaXMuYnVpbGRlci5kZWxldGVfaXRlbSA9IChlbCkgPT4ge1xyXG5cdFx0XHRcdGlmICggIWVsICkge1xyXG5cdFx0XHRcdFx0cmV0dXJuIG51bGw7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGNvbnN0IGIgICAgICAgID0gdGhpcy5idWlsZGVyO1xyXG5cdFx0XHRcdGNvbnN0IG5laWdoYm9yID0gYi5fZmluZF9uZWlnaGJvcl9zZWxlY3RhYmxlPy4oIGVsICkgfHwgbnVsbDtcclxuXHRcdFx0XHRlbC5yZW1vdmUoKTtcclxuXHRcdFx0XHQvLyBVc2UgbG9jYWwgQ29yZSBjb25zdGFudHMgKG5vdCBhIGdsb2JhbCkgdG8gYXZvaWQgUmVmZXJlbmNlRXJyb3JzLlxyXG5cdFx0XHRcdGIuYnVzPy5lbWl0Py4oIENvcmUuV1BCQ19CRkJfRXZlbnRzLkZJRUxEX1JFTU9WRSwgeyBlbCwgaWQ6IGVsPy5kYXRhc2V0Py5pZCwgdWlkOiBlbD8uZGF0YXNldD8udWlkIH0gKTtcclxuXHRcdFx0XHRiLnVzYWdlPy51cGRhdGVfcGFsZXR0ZV91aT8uKCk7XHJcblx0XHRcdFx0Ly8gTm90aWZ5IGdlbmVyaWMgc3RydWN0dXJlIGxpc3RlbmVycywgdG9vOlxyXG5cdFx0XHRcdGIuYnVzPy5lbWl0Py4oIENvcmUuV1BCQ19CRkJfRXZlbnRzLlNUUlVDVFVSRV9DSEFOR0UsIHsgcmVhc29uOiAnZGVsZXRlJywgZWwgfSApO1xyXG5cdFx0XHRcdC8vIERlZmVyIHNlbGVjdGlvbiBhIHRpY2sgc28gdGhlIERPTSBpcyBmdWxseSBzZXR0bGVkIGJlZm9yZSBJbnNwZWN0b3IgaHlkcmF0ZXMuXHJcblx0XHRcdFx0cmVxdWVzdEFuaW1hdGlvbkZyYW1lKCAoKSA9PiB7XHJcblx0XHRcdFx0XHQvLyBUaGlzIGNhbGxzIGluc3BlY3Rvci5iaW5kX3RvX2ZpZWxkKCkgYW5kIG9wZW5zIHRoZSBJbnNwZWN0b3IgcGFuZWwuXHJcblx0XHRcdFx0XHRiLnNlbGVjdF9maWVsZD8uKCBuZWlnaGJvciB8fCBudWxsLCB7IHNjcm9sbEludG9WaWV3OiAhIW5laWdoYm9yIH0gKTtcclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0cmV0dXJuIG5laWdoYm9yO1xyXG5cdFx0XHR9O1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuYnVzLm9uKCBDb3JlLldQQkNfQkZCX0V2ZW50cy5DTEVBUl9TRUxFQ1RJT04sIHRoaXMuX29uX2NsZWFyICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlci5idXMub24oIENvcmUuV1BCQ19CRkJfRXZlbnRzLlNUUlVDVFVSRV9MT0FERUQsIHRoaXMuX29uX2NsZWFyICk7XHJcblx0XHRcdC8vIGRlbGVnYXRlZCBjbGljayBzZWxlY3Rpb24gKGNhcHR1cmUgZW5zdXJlcyB3ZSB3aW4gYmVmb3JlIGJ1YmJsaW5nIHRvIGNvbnRhaW5lcnMpLlxyXG5cdFx0XHR0aGlzLl9vbl9jYW52YXNfY2xpY2sgPSB0aGlzLl9oYW5kbGVfY2FudmFzX2NsaWNrLmJpbmQoIHRoaXMgKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyLnBhZ2VzX2NvbnRhaW5lci5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCB0aGlzLl9vbl9jYW52YXNfY2xpY2ssIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0XHRkZXN0cm95KCkge1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuYnVzLm9mZiggQ29yZS5XUEJDX0JGQl9FdmVudHMuQ0xFQVJfU0VMRUNUSU9OLCB0aGlzLl9vbl9jbGVhciApO1xyXG5cclxuXHRcdFx0aWYgKCB0aGlzLl9vbl9jYW52YXNfY2xpY2sgKSB7XHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLnBhZ2VzX2NvbnRhaW5lci5yZW1vdmVFdmVudExpc3RlbmVyKCAnY2xpY2snLCB0aGlzLl9vbl9jYW52YXNfY2xpY2ssIHRydWUgKTtcclxuXHRcdFx0XHR0aGlzLl9vbl9jYW52YXNfY2xpY2sgPSBudWxsO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBEZWxlZ2F0ZWQgY2FudmFzIGNsaWNrIC0+IHNlbGVjdCBjbG9zZXN0IGZpZWxkL3NlY3Rpb24gKGlubmVyIGJlYXRzIG91dGVyKS5cclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKiBAcGFyYW0ge01vdXNlRXZlbnR9IGVcclxuXHRcdCAqL1xyXG5cdFx0X2hhbmRsZV9jYW52YXNfY2xpY2soZSkge1xyXG5cdFx0XHRjb25zdCByb290ID0gdGhpcy5idWlsZGVyLnBhZ2VzX2NvbnRhaW5lcjtcclxuXHRcdFx0aWYgKCAhcm9vdCApIHJldHVybjtcclxuXHJcblx0XHRcdC8vIElnbm9yZSBjbGlja3Mgb24gY29udHJvbHMvaGFuZGxlcy9yZXNpemVycywgZXRjLlxyXG5cdFx0XHRjb25zdCBJR05PUkUgPSBbXHJcblx0XHRcdFx0Jy53cGJjX2JmYl9fb3ZlcmxheS1jb250cm9scycsXHJcblx0XHRcdFx0Jy53cGJjX2JmYl9fbGF5b3V0X3BpY2tlcicsXHJcblx0XHRcdFx0Jy53cGJjX2JmYl9fZHJhZy1oYW5kbGUnLFxyXG5cdFx0XHRcdCcud3BiY19iZmJfX2ZpZWxkLXJlbW92ZS1idG4nLFxyXG5cdFx0XHRcdCcud3BiY19iZmJfX2ZpZWxkLW1vdmUtdXAnLFxyXG5cdFx0XHRcdCcud3BiY19iZmJfX2ZpZWxkLW1vdmUtZG93bicsXHJcblx0XHRcdFx0Jy53cGJjX2JmYl9fY29sdW1uLXJlc2l6ZXInXHJcblx0XHRcdF0uam9pbiggJywnICk7XHJcblxyXG5cdFx0XHRpZiAoIGUudGFyZ2V0LmNsb3Nlc3QoIElHTk9SRSApICkge1xyXG5cdFx0XHRcdHJldHVybjsgLy8gbGV0IHRob3NlIGNvbnRyb2xzIGRvIHRoZWlyIG93biB0aGluZy5cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gRmluZCB0aGUgY2xvc2VzdCBzZWxlY3RhYmxlIChmaWVsZCBPUiBzZWN0aW9uKSBmcm9tIHRoZSBjbGljayB0YXJnZXQuXHJcblx0XHRcdGxldCBoaXQgPSBlLnRhcmdldC5jbG9zZXN0Py4oXHJcblx0XHRcdFx0YCR7Q29yZS5XUEJDX0JGQl9ET00uU0VMRUNUT1JTLnZhbGlkRmllbGR9LCAke0NvcmUuV1BCQ19CRkJfRE9NLlNFTEVDVE9SUy5zZWN0aW9ufSwgLndwYmNfYmZiX19jb2x1bW5gXHJcblx0XHRcdCk7XHJcblxyXG5cdFx0XHRpZiAoICFoaXQgfHwgIXJvb3QuY29udGFpbnMoIGhpdCApICkge1xyXG5cdFx0XHRcdHRoaXMuc2VsZWN0X2ZpZWxkKCBudWxsICk7ICAgICAgICAgICAvLyBDbGVhciBzZWxlY3Rpb24gb24gYmxhbmsgY2xpY2suXHJcblx0XHRcdFx0cmV0dXJuOyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIEVtcHR5IHNwYWNlIGlzIGhhbmRsZWQgZWxzZXdoZXJlLlxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBORVc6IGlmIHVzZXIgY2xpY2tlZCBhIENPTFVNTiAtPiByZW1lbWJlciB0YWIga2V5IG9uIGl0cyBTRUNUSU9OLCBidXQgc3RpbGwgc2VsZWN0IHRoZSBzZWN0aW9uLlxyXG5cdFx0XHRsZXQgcHJlc2VsZWN0X3RhYl9rZXkgPSBudWxsO1xyXG5cdFx0XHRpZiAoIGhpdC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fY29sdW1uJyApICkge1xyXG5cdFx0XHRcdGNvbnN0IHJvdyAgPSBoaXQuY2xvc2VzdCggJy53cGJjX2JmYl9fcm93JyApO1xyXG5cdFx0XHRcdGNvbnN0IGNvbHMgPSByb3cgPyBBcnJheS5mcm9tKCByb3cucXVlcnlTZWxlY3RvckFsbCggJzpzY29wZSA+IC53cGJjX2JmYl9fY29sdW1uJyApICkgOiBbXTtcclxuXHRcdFx0XHRjb25zdCBpZHggID0gTWF0aC5tYXgoIDAsIGNvbHMuaW5kZXhPZiggaGl0ICkgKTtcclxuXHRcdFx0XHRjb25zdCBzZWMgID0gaGl0LmNsb3Nlc3QoICcud3BiY19iZmJfX3NlY3Rpb24nICk7XHJcblx0XHRcdFx0aWYgKCBzZWMgKSB7XHJcblx0XHRcdFx0XHRwcmVzZWxlY3RfdGFiX2tleSA9IFN0cmluZyggaWR4ICsgMSApOyAgICAgICAgICAgICAgLy8gdGFicyBhcmUgMS1iYXNlZCBpbiB1aS1jb2x1bW4tc3R5bGVzLmpzXHJcblx0XHRcdFx0XHQvLyBIaW50IGZvciB0aGUgcmVuZGVyZXIgKGl0IHJlYWRzIHRoaXMgQkVGT1JFIHJlbmRlcmluZyBhbmQgcmVzdG9yZXMgdGhlIHRhYikuXHJcblx0XHRcdFx0XHRzZWMuZGF0YXNldC5jb2xfc3R5bGVzX2FjdGl2ZV90YWIgPSBwcmVzZWxlY3RfdGFiX2tleTtcclxuXHRcdFx0XHRcdC8vIHByb21vdGUgc2VsZWN0aW9uIHRvIHRoZSBzZWN0aW9uIChzYW1lIFVYIGFzIGJlZm9yZSkuXHJcblx0XHRcdFx0XHRoaXQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPSBzZWM7XHJcblx0XHRcdFx0XHQvLyBORVc6IHZpc3VhbGx5IG1hcmsgd2hpY2ggY29sdW1uIGlzIGJlaW5nIGVkaXRlZFxyXG5cdFx0XHRcdFx0aWYgKCBVSSAmJiBVSS5XUEJDX0JGQl9Db2x1bW5fU3R5bGVzICYmIFVJLldQQkNfQkZCX0NvbHVtbl9TdHlsZXMuc2V0X3NlbGVjdGVkX2NvbF9mbGFnICkge1xyXG5cdFx0XHRcdFx0XHRVSS5XUEJDX0JGQl9Db2x1bW5fU3R5bGVzLnNldF9zZWxlY3RlZF9jb2xfZmxhZyggc2VjLCBwcmVzZWxlY3RfdGFiX2tleSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gU2VsZWN0IGFuZCBzdG9wIGJ1YmJsaW5nIHNvIG91dGVyIGNvbnRhaW5lcnMgZG9u4oCZdCByZXNlbGVjdCBhIHBhcmVudC5cclxuXHRcdFx0dGhpcy5zZWxlY3RfZmllbGQoIGhpdCApO1xyXG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG5cclxuXHRcdFx0Ly8gQWxzbyBzZXQgdGhlIHRhYiBhZnRlciB0aGUgaW5zcGVjdG9yIHJlbmRlcnMgKHdvcmtzIGV2ZW4gaWYgaXQgd2FzIGFscmVhZHkgb3BlbikuXHJcblx0XHRcdGlmICggcHJlc2VsZWN0X3RhYl9rZXkgKSB7XHJcblx0XHRcdFx0KHdpbmRvdy5yZXF1ZXN0QW5pbWF0aW9uRnJhbWUgfHwgc2V0VGltZW91dCkoIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IGlucyAgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwYmNfYmZiX19pbnNwZWN0b3InICk7XHJcblx0XHRcdFx0XHRcdGNvbnN0IHRhYnMgPSBpbnMgJiYgaW5zLnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1iZmItc2xvdD1cImNvbHVtbl9zdHlsZXNcIl0gW2RhdGEtd3BiYy10YWJzXScgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCB0YWJzICYmIHdpbmRvdy53cGJjX3VpX3RhYnMgJiYgdHlwZW9mIHdpbmRvdy53cGJjX3VpX3RhYnMuc2V0X2FjdGl2ZSA9PT0gJ2Z1bmN0aW9uJyApIHtcclxuXHRcdFx0XHRcdFx0XHR3aW5kb3cud3BiY191aV90YWJzLnNldF9hY3RpdmUoIHRhYnMsIHByZXNlbGVjdF90YWJfa2V5ICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9LCAwICk7XHJcblxyXG5cdFx0XHRcdC8vIFBvbGl0ZWx5IGFzayB0aGUgSW5zcGVjdG9yIHRvIGZvY3VzL29wZW4gdGhlIFwiQ29sdW1uIFN0eWxlc1wiIGdyb3VwIGFuZCB0YWIuXHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdGRvY3VtZW50LmRpc3BhdGNoRXZlbnQoIG5ldyBDdXN0b21FdmVudCggJ3dwYmNfYmZiOmluc3BlY3Rvcl9mb2N1cycsIHtcclxuXHRcdFx0XHRcdFx0ZGV0YWlsOiB7XHJcblx0XHRcdFx0XHRcdFx0Z3JvdXAgIDogJ2NvbHVtbl9zdHlsZXMnLFxyXG5cdFx0XHRcdFx0XHRcdHRhYl9rZXk6IHByZXNlbGVjdF90YWJfa2V5XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH0gKSApO1xyXG5cdFx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTZWxlY3QgYSBmaWVsZCBlbGVtZW50IG9yIGNsZWFyIHNlbGVjdGlvbi5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fG51bGx9IGZpZWxkX2VsXHJcblx0XHQgKiBAcGFyYW0ge3tzY3JvbGxJbnRvVmlldz86IGJvb2xlYW59fSBbb3B0cyA9IHt9XVxyXG5cdFx0ICovXHJcblx0XHRzZWxlY3RfZmllbGQoZmllbGRfZWwsIHsgc2Nyb2xsSW50b1ZpZXcgPSBmYWxzZSB9ID0ge30pIHtcclxuXHRcdFx0Y29uc3Qgcm9vdCAgID0gdGhpcy5idWlsZGVyLnBhZ2VzX2NvbnRhaW5lcjtcclxuXHRcdFx0Y29uc3QgcHJldkVsID0gdGhpcy5nZXRfc2VsZWN0ZWRfZmllbGQ/LigpIHx8IG51bGw7ICAgLy8gdGhlIG9uZSB3ZeKAmXJlIGxlYXZpbmcuXHJcblxyXG5cdFx0XHQvLyBJZ25vcmUgZWxlbWVudHMgbm90IGluIHRoZSBjYW52YXMuXHJcblx0XHRcdGlmICggZmllbGRfZWwgJiYgIXJvb3QuY29udGFpbnMoIGZpZWxkX2VsICkgKSB7XHJcblx0XHRcdFx0ZmllbGRfZWwgPSBudWxsOyAvLyB0cmVhdCBhcyBcIm5vIHNlbGVjdGlvblwiLlxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBORVc6IGlmIHdlIGFyZSBsZWF2aW5nIGEgc2VjdGlvbiwgY2xlYXIgaXRzIGNvbHVtbiBoaWdobGlnaHRcclxuXHRcdFx0aWYgKFxyXG5cdFx0XHRcdHByZXZFbCAmJiBwcmV2RWwgIT09IGZpZWxkX2VsICYmXHJcblx0XHRcdFx0cHJldkVsLmNsYXNzTGlzdD8uY29udGFpbnMoICd3cGJjX2JmYl9fc2VjdGlvbicgKSAmJlxyXG5cdFx0XHRcdFVJPy5XUEJDX0JGQl9Db2x1bW5fU3R5bGVzPy5jbGVhcl9zZWxlY3RlZF9jb2xfZmxhZ1xyXG5cdFx0XHQpIHtcclxuXHRcdFx0XHRVSS5XUEJDX0JGQl9Db2x1bW5fU3R5bGVzLmNsZWFyX3NlbGVjdGVkX2NvbF9mbGFnKCBwcmV2RWwgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gSWYgd2UncmUgbGVhdmluZyBhIGZpZWxkLCBwZXJtYW5lbnRseSBzdG9wIGF1dG8tbmFtZSBmb3IgaXQuXHJcblx0XHRcdGlmICggcHJldkVsICYmIHByZXZFbCAhPT0gZmllbGRfZWwgJiYgcHJldkVsLmNsYXNzTGlzdD8uY29udGFpbnMoICd3cGJjX2JmYl9fZmllbGQnICkgKSB7XHJcblx0XHRcdFx0cHJldkVsLmRhdGFzZXQuYXV0b25hbWUgPSAnMCc7XHJcblx0XHRcdFx0cHJldkVsLmRhdGFzZXQuZnJlc2ggICAgPSAnMCc7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHJvb3QucXVlcnlTZWxlY3RvckFsbCggJy5pcy1zZWxlY3RlZCcgKS5mb3JFYWNoKCAobikgPT4ge1xyXG5cdFx0XHRcdG4uY2xhc3NMaXN0LnJlbW92ZSggJ2lzLXNlbGVjdGVkJyApO1xyXG5cdFx0XHR9ICk7XHJcblx0XHRcdGlmICggIWZpZWxkX2VsICkge1xyXG5cdFx0XHRcdGNvbnN0IHByZXYgICAgICAgICA9IHRoaXMuX3NlbGVjdGVkX3VpZCB8fCBudWxsO1xyXG5cdFx0XHRcdHRoaXMuX3NlbGVjdGVkX3VpZCA9IG51bGw7XHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLmluc3BlY3Rvcj8uY2xlYXI/LigpO1xyXG5cdFx0XHRcdHJvb3QuY2xhc3NMaXN0LnJlbW92ZSggJ2hhcy1zZWxlY3Rpb24nICk7XHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLmJ1cy5lbWl0KCBDb3JlLldQQkNfQkZCX0V2ZW50cy5DTEVBUl9TRUxFQ1RJT04sIHsgcHJldl91aWQ6IHByZXYsIHNvdXJjZTogJ2J1aWxkZXInIH0gKTtcclxuXHJcblx0XHRcdFx0Ly8gQXV0by1vcGVuIFwiQWRkIEZpZWxkc1wiIHdoZW4gbm90aGluZyBpcyBzZWxlY3RlZC5cclxuXHRcdFx0XHRkb2N1bWVudC5kaXNwYXRjaEV2ZW50KCBuZXcgQ3VzdG9tRXZlbnQoICd3cGJjX2JmYjpzaG93X3BhbmVsJywge1xyXG5cdFx0XHRcdFx0ZGV0YWlsOiB7XHJcblx0XHRcdFx0XHRcdHBhbmVsX2lkOiAnd3BiY19iZmJfX3BhbGV0dGVfYWRkX25ldycsXHJcblx0XHRcdFx0XHRcdHRhYl9pZCAgOiAnd3BiY190YWJfbGlicmFyeSdcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9ICkgKTtcclxuXHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdGZpZWxkX2VsLmNsYXNzTGlzdC5hZGQoICdpcy1zZWxlY3RlZCcgKTtcclxuXHRcdFx0dGhpcy5fc2VsZWN0ZWRfdWlkID0gZmllbGRfZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS11aWQnICkgfHwgbnVsbDtcclxuXHJcblx0XHRcdC8vIEZhbGxiYWNrOiBlbnN1cmUgc2VjdGlvbnMgYW5ub3VuY2UgdGhlbXNlbHZlcyBhcyB0eXBlPVwic2VjdGlvblwiLlxyXG5cdFx0XHRpZiAoIGZpZWxkX2VsLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19zZWN0aW9uJyApICYmICFmaWVsZF9lbC5kYXRhc2V0LnR5cGUgKSB7XHJcblx0XHRcdFx0ZmllbGRfZWwuZGF0YXNldC50eXBlID0gJ3NlY3Rpb24nO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIHNjcm9sbEludG9WaWV3ICkge1xyXG5cdFx0XHRcdGZpZWxkX2VsLnNjcm9sbEludG9WaWV3KCB7IGJlaGF2aW9yOiAnc21vb3RoJywgYmxvY2s6ICdjZW50ZXInIH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuaW5zcGVjdG9yPy5iaW5kX3RvX2ZpZWxkPy4oIGZpZWxkX2VsICk7XHJcblxyXG5cdFx0XHQvLyBGYWxsYmFjazogZW5zdXJlIGluc3BlY3RvciBlbmhhbmNlcnMgKGluY2wuIFZhbHVlU2xpZGVyKSBydW4gZXZlcnkgYmluZC5cclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRjb25zdCBpbnMgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwYmNfYmZiX19pbnNwZWN0b3InIClcclxuXHRcdFx0XHRcdHx8IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcud3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0XHRpZiAoIGlucyApIHtcclxuXHRcdFx0XHRcdFVJLkluc3BlY3RvckVuaGFuY2Vycz8uc2Nhbj8uKCBpbnMgKTsgICAgICAgICAgICAgIC8vIHJ1bnMgYWxsIGVuaGFuY2Vyc1xyXG5cdFx0XHRcdFx0VUkuV1BCQ19CRkJfVmFsdWVTbGlkZXI/LmluaXRfb24/LiggaW5zICk7ICAgICAgICAgLy8gZXh0cmEgYmVsdC1hbmQtc3VzcGVuZGVyc1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBjYXRjaCAoIF8gKSB7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIE5FVzogd2hlbiBzZWxlY3RpbmcgYSBzZWN0aW9uLCByZWZsZWN0IGl0cyBhY3RpdmUgdGFiIGFzIHRoZSBoaWdobGlnaHRlZCBjb2x1bW4uXHJcblx0XHRcdGlmICggZmllbGRfZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgJiZcclxuXHRcdFx0XHRVST8uV1BCQ19CRkJfQ29sdW1uX1N0eWxlcz8uc2V0X3NlbGVjdGVkX2NvbF9mbGFnICkge1xyXG5cdFx0XHRcdHZhciBrID0gKGZpZWxkX2VsLmRhdGFzZXQgJiYgZmllbGRfZWwuZGF0YXNldC5jb2xfc3R5bGVzX2FjdGl2ZV90YWIpXHJcblx0XHRcdFx0XHQ/IGZpZWxkX2VsLmRhdGFzZXQuY29sX3N0eWxlc19hY3RpdmVfdGFiIDogJzEnO1xyXG5cdFx0XHRcdFVJLldQQkNfQkZCX0NvbHVtbl9TdHlsZXMuc2V0X3NlbGVjdGVkX2NvbF9mbGFnKCBmaWVsZF9lbCwgayApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBLZWVwIHNlY3Rpb25zICYgZmllbGRzIGluIHRoZSBzYW1lIGZsb3c6XHJcblx0XHRcdC8vIDEpIEdlbmVyaWMgaHlkcmF0b3IgZm9yIHNpbXBsZSBkYXRhc2V0LWJhY2tlZCBjb250cm9scy5cclxuXHRcdFx0aWYgKCBmaWVsZF9lbCApIHtcclxuXHRcdFx0XHRVSS5XUEJDX0JGQl9JbnNwZWN0b3JfQnJpZGdlLl9nZW5lcmljX2h5ZHJhdGVfY29udHJvbHM/LiggdGhpcy5idWlsZGVyLCBmaWVsZF9lbCApO1xyXG5cdFx0XHRcdFVJLldQQkNfQkZCX0luc3BlY3Rvcl9CcmlkZ2UuX2h5ZHJhdGVfc3BlY2lhbF9jb250cm9scz8uKCB0aGlzLmJ1aWxkZXIsIGZpZWxkX2VsICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIEF1dG8tb3BlbiBJbnNwZWN0b3Igd2hlbiBhIHVzZXIgc2VsZWN0cyBhIGZpZWxkL3NlY3Rpb24gLlxyXG5cdFx0XHRkb2N1bWVudC5kaXNwYXRjaEV2ZW50KCBuZXcgQ3VzdG9tRXZlbnQoICd3cGJjX2JmYjpzaG93X3BhbmVsJywge1xyXG5cdFx0XHRcdGRldGFpbDoge1xyXG5cdFx0XHRcdFx0cGFuZWxfaWQ6ICd3cGJjX2JmYl9faW5zcGVjdG9yJyxcclxuXHRcdFx0XHRcdHRhYl9pZCAgOiAnd3BiY190YWJfaW5zcGVjdG9yJ1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSApICk7XHJcblxyXG5cdFx0XHRyb290LmNsYXNzTGlzdC5hZGQoICdoYXMtc2VsZWN0aW9uJyApO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuYnVzLmVtaXQoIENvcmUuV1BCQ19CRkJfRXZlbnRzLlNFTEVDVCwgeyB1aWQ6IHRoaXMuX3NlbGVjdGVkX3VpZCwgZWw6IGZpZWxkX2VsIH0gKTtcclxuXHRcdFx0Y29uc3QgbGFiZWwgPSBmaWVsZF9lbD8ucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fZmllbGQtbGFiZWwnICk/LnRleHRDb250ZW50IHx8IChmaWVsZF9lbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fc2VjdGlvbicgKSA/ICdzZWN0aW9uJyA6ICcnKSB8fCBmaWVsZF9lbD8uZGF0YXNldD8uaWQgfHwgJ2l0ZW0nO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuX2Fubm91bmNlKCAnU2VsZWN0ZWQgJyArIGxhYmVsICsgJy4nICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqIEByZXR1cm5zIHtIVE1MRWxlbWVudHxudWxsfSAqL1xyXG5cdFx0Z2V0X3NlbGVjdGVkX2ZpZWxkKCkge1xyXG5cdFx0XHRpZiAoICF0aGlzLl9zZWxlY3RlZF91aWQgKSB7XHJcblx0XHRcdFx0cmV0dXJuIG51bGw7XHJcblx0XHRcdH1cclxuXHRcdFx0Y29uc3QgZXNjX2F0dHIgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLmVzY19hdHRyX3ZhbHVlX2Zvcl9zZWxlY3RvciggdGhpcy5fc2VsZWN0ZWRfdWlkICk7XHJcblx0XHRcdHJldHVybiB0aGlzLmJ1aWxkZXIucGFnZXNfY29udGFpbmVyLnF1ZXJ5U2VsZWN0b3IoIGAud3BiY19iZmJfX2ZpZWxkW2RhdGEtdWlkPVwiJHtlc2NfYXR0cn1cIl0sIC53cGJjX2JmYl9fc2VjdGlvbltkYXRhLXVpZD1cIiR7ZXNjX2F0dHJ9XCJdYCApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcGFyYW0ge0N1c3RvbUV2ZW50fSBldiAqL1xyXG5cdFx0b25fY2xlYXIoZXYpIHtcclxuXHRcdFx0Y29uc3Qgc3JjID0gZXY/LmRldGFpbD8uc291cmNlID8/IGV2Py5zb3VyY2U7XHJcblx0XHRcdGlmICggc3JjICE9PSAnYnVpbGRlcicgKSB7XHJcblx0XHRcdFx0dGhpcy5zZWxlY3RfZmllbGQoIG51bGwgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBCcmlkZ2VzIHRoZSBidWlsZGVyIHdpdGggdGhlIEluc3BlY3RvciBhbmQgc2FuaXRpemVzIGlkL25hbWUgZWRpdHMuXHJcblx0ICovXHJcblx0VUkuV1BCQ19CRkJfSW5zcGVjdG9yX0JyaWRnZSA9IGNsYXNzIGV4dGVuZHMgVUkuV1BCQ19CRkJfTW9kdWxlIHtcclxuXHJcblx0XHRpbml0KCkge1xyXG5cdFx0XHR0aGlzLl9hdHRhY2hfaW5zcGVjdG9yKCk7XHJcblx0XHRcdHRoaXMuX2JpbmRfaWRfc2FuaXRpemVyKCk7XHJcblx0XHRcdHRoaXMuX29wZW5faW5zcGVjdG9yX2FmdGVyX2ZpZWxkX2FkZGVkKCk7XHJcblx0XHRcdHRoaXMuX2JpbmRfZm9jdXNfc2hvcnRjdXRzKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0X2F0dGFjaF9pbnNwZWN0b3IoKSB7XHJcblx0XHRcdGNvbnN0IGIgICAgICA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0Y29uc3QgYXR0YWNoID0gKCkgPT4ge1xyXG5cdFx0XHRcdGlmICggdHlwZW9mIHdpbmRvdy5XUEJDX0JGQl9JbnNwZWN0b3IgPT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0XHRiLmluc3BlY3RvciA9IG5ldyBXUEJDX0JGQl9JbnNwZWN0b3IoIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKSwgYiApO1xyXG5cdFx0XHRcdFx0dGhpcy5fYmluZF9pZF9zYW5pdGl6ZXIoKTtcclxuXHRcdFx0XHRcdGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoICd3cGJjX2JmYl9pbnNwZWN0b3JfcmVhZHknLCBhdHRhY2ggKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH07XHJcblx0XHRcdC8vIEVuc3VyZSB3ZSBiaW5kIGFmdGVyIGxhdGUgcmVhZHkgYXMgd2VsbC5cclxuXHRcdFx0aWYgKCB0eXBlb2Ygd2luZG93LldQQkNfQkZCX0luc3BlY3RvciA9PT0gJ2Z1bmN0aW9uJyApIHtcclxuXHRcdFx0XHRhdHRhY2goKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRiLmluc3BlY3RvciA9IHtcclxuXHRcdFx0XHRcdGJpbmRfdG9fZmllbGQoKSB7XHJcblx0XHRcdFx0XHR9LCBjbGVhcigpIHtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9O1xyXG5cdFx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICd3cGJjX2JmYl9pbnNwZWN0b3JfcmVhZHknLCBhdHRhY2ggKTtcclxuXHRcdFx0XHRzZXRUaW1lb3V0KCBhdHRhY2gsIDAgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogTGlzdGVuIGZvciBcImZvY3VzXCIgaGludHMgZnJvbSB0aGUgY2FudmFzIGFuZCBvcGVuIHRoZSByaWdodCBncm91cC90YWIuXHJcblx0XHQgKiAtIFN1cHBvcnRzOiBncm91cCA9PT0gJ2NvbHVtbl9zdHlsZXMnXHJcblx0XHQgKiAtIEFsc28gc2Nyb2xscyB0aGUgZ3JvdXAgaW50byB2aWV3LlxyXG5cdFx0ICovXHJcblx0XHRfYmluZF9mb2N1c19zaG9ydGN1dHMoKSB7XHJcblx0XHRcdC8qKiBAcGFyYW0ge0N1c3RvbUV2ZW50fSBlICovXHJcblx0XHRcdGNvbnN0IG9uX2ZvY3VzID0gKGUpID0+IHtcclxuXHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0Y29uc3QgZ3JwX2tleSA9IGUgJiYgZS5kZXRhaWwgJiYgZS5kZXRhaWwuZ3JvdXA7XHJcblx0XHRcdFx0XHRjb25zdCB0YWJfa2V5ID0gZSAmJiBlLmRldGFpbCAmJiBlLmRldGFpbC50YWJfa2V5O1xyXG5cdFx0XHRcdFx0aWYgKCAhZ3JwX2tleSApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdGNvbnN0IGlucyA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKSB8fCBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19pbnNwZWN0b3InICk7XHJcblx0XHRcdFx0XHRpZiAoICFpbnMgKSB7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoIGdycF9rZXkgPT09ICdjb2x1bW5fc3R5bGVzJyApIHtcclxuXHRcdFx0XHRcdFx0Ly8gRmluZCB0aGUgQ29sdW1uIFN0eWxlcyBzbG90L2dyb3VwLlxyXG5cdFx0XHRcdFx0XHRjb25zdCBzbG90ID0gaW5zLnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1iZmItc2xvdD1cImNvbHVtbl9zdHlsZXNcIl0nIClcclxuXHRcdFx0XHRcdFx0XHR8fCBpbnMucXVlcnlTZWxlY3RvciggJ1tkYXRhLWluc3BlY3Rvci1ncm91cC1rZXk9XCJjb2x1bW5fc3R5bGVzXCJdJyApO1xyXG5cdFx0XHRcdFx0XHRpZiAoIHNsb3QgKSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gT3BlbiBjb2xsYXBzaWJsZSBjb250YWluZXIgaWYgcHJlc2VudC5cclxuXHRcdFx0XHRcdFx0XHRjb25zdCBncm91cF93cmFwID0gc2xvdC5jbG9zZXN0KCAnLmluc3BlY3Rvcl9fZ3JvdXAnICkgfHwgc2xvdC5jbG9zZXN0KCAnW2RhdGEtaW5zcGVjdG9yLWdyb3VwXScgKTtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGdyb3VwX3dyYXAgJiYgIWdyb3VwX3dyYXAuY2xhc3NMaXN0LmNvbnRhaW5zKCAnaXMtb3BlbicgKSApIHtcclxuXHRcdFx0XHRcdFx0XHRcdGdyb3VwX3dyYXAuY2xhc3NMaXN0LmFkZCggJ2lzLW9wZW4nICk7XHJcblx0XHRcdFx0XHRcdFx0XHQvLyBNaXJyb3IgQVJJQSBzdGF0ZSBpZiB5b3VyIGhlYWRlciB1c2VzIGFyaWEtZXhwYW5kZWQuXHJcblx0XHRcdFx0XHRcdFx0XHRjb25zdCBoZWFkZXJfYnRuID0gZ3JvdXBfd3JhcC5xdWVyeVNlbGVjdG9yKCAnW2FyaWEtZXhwYW5kZWRdJyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0aWYgKCBoZWFkZXJfYnRuICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRoZWFkZXJfYnRuLnNldEF0dHJpYnV0ZSggJ2FyaWEtZXhwYW5kZWQnLCAndHJ1ZScgKTtcclxuXHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0XHRcdC8vIE9wdGlvbmFsOiBzZXQgdGhlIHJlcXVlc3RlZCB0YWIga2V5IGlmIHRhYnMgZXhpc3QgaW4gdGhpcyBncm91cC5cclxuXHRcdFx0XHRcdFx0XHRpZiAoIHRhYl9rZXkgKSB7XHJcblx0XHRcdFx0XHRcdFx0XHRjb25zdCB0YWJzID0gc2xvdC5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtd3BiYy10YWJzXScgKTtcclxuXHRcdFx0XHRcdFx0XHRcdGlmICggdGFicyAmJiB3aW5kb3cud3BiY191aV90YWJzICYmIHR5cGVvZiB3aW5kb3cud3BiY191aV90YWJzLnNldF9hY3RpdmUgPT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdHdpbmRvdy53cGJjX3VpX3RhYnMuc2V0X2FjdGl2ZSggdGFicywgU3RyaW5nKCB0YWJfa2V5ICkgKTtcclxuXHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0XHRcdC8vIEJyaW5nIGludG8gdmlldyBmb3IgY29udmVuaWVuY2UuXHJcblx0XHRcdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0XHRcdHNsb3Quc2Nyb2xsSW50b1ZpZXcoIHsgYmVoYXZpb3I6ICdzbW9vdGgnLCBibG9jazogJ25lYXJlc3QnIH0gKTtcclxuXHRcdFx0XHRcdFx0XHR9IGNhdGNoICggX2UgKSB7XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fTtcclxuXHJcblx0XHRcdHRoaXMuX29uX2luc3BlY3Rvcl9mb2N1cyA9IG9uX2ZvY3VzO1xyXG5cdFx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnd3BiY19iZmI6aW5zcGVjdG9yX2ZvY3VzJywgb25fZm9jdXMsIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0XHRkZXN0cm95KCkge1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGlmICggdGhpcy5fb25faW5zcGVjdG9yX2ZvY3VzICkge1xyXG5cdFx0XHRcdFx0ZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ3dwYmNfYmZiOmluc3BlY3Rvcl9mb2N1cycsIHRoaXMuX29uX2luc3BlY3Rvcl9mb2N1cywgdHJ1ZSApO1xyXG5cdFx0XHRcdFx0dGhpcy5fb25faW5zcGVjdG9yX2ZvY3VzID0gbnVsbDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEh5ZHJhdGUgaW5zcGVjdG9yIGlucHV0cyBmb3IgXCJzcGVjaWFsXCIga2V5cyB0aGF0IHdlIGhhbmRsZSBleHBsaWNpdGx5LlxyXG5cdFx0ICogV29ya3MgZm9yIGJvdGggZmllbGRzIGFuZCBzZWN0aW9ucy5cclxuXHRcdCAqIEBwYXJhbSB7V1BCQ19Gb3JtX0J1aWxkZXJ9IGJ1aWxkZXJcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHNlbFxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgX2h5ZHJhdGVfc3BlY2lhbF9jb250cm9scyhidWlsZGVyLCBzZWwpIHtcclxuXHRcdFx0Y29uc3QgaW5zID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGJjX2JmYl9faW5zcGVjdG9yJyApO1xyXG5cdFx0XHRpZiAoICFpbnMgfHwgIXNlbCApIHJldHVybjtcclxuXHJcblx0XHRcdGNvbnN0IHNldFZhbCA9IChrZXksIHZhbCkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IGN0cmwgPSBpbnMucXVlcnlTZWxlY3RvciggYFtkYXRhLWluc3BlY3Rvci1rZXk9XCIke2tleX1cIl1gICk7XHJcblx0XHRcdFx0aWYgKCBjdHJsICYmICd2YWx1ZScgaW4gY3RybCApIGN0cmwudmFsdWUgPSBTdHJpbmcoIHZhbCA/PyAnJyApO1xyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0Ly8gSW50ZXJuYWwgaWQgLyBuYW1lIC8gcHVibGljIGh0bWxfaWQuXHJcblx0XHRcdHNldFZhbCggJ2lkJywgc2VsLmdldEF0dHJpYnV0ZSggJ2RhdGEtaWQnICkgfHwgJycgKTtcclxuXHRcdFx0c2V0VmFsKCAnbmFtZScsIHNlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLW5hbWUnICkgfHwgJycgKTtcclxuXHRcdFx0c2V0VmFsKCAnaHRtbF9pZCcsIHNlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWh0bWxfaWQnICkgfHwgJycgKTtcclxuXHJcblx0XHRcdC8vIFNlY3Rpb24tb25seSBleHRyYXMgYXJlIGhhcm1sZXNzIHRvIHNldCBmb3IgZmllbGRzIChjb250cm9scyBtYXkgbm90IGV4aXN0KS5cclxuXHRcdFx0c2V0VmFsKCAnY3NzY2xhc3MnLCBzZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1jc3NjbGFzcycgKSB8fCAnJyApO1xyXG5cdFx0XHRzZXRWYWwoICdsYWJlbCcsIHNlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWxhYmVsJyApIHx8ICcnICk7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSHlkcmF0ZSBpbnNwZWN0b3IgaW5wdXRzIHRoYXQgZGVjbGFyZSBhIGdlbmVyaWMgZGF0YXNldCBtYXBwaW5nIHZpYVxyXG5cdFx0ICogW2RhdGEtaW5zcGVjdG9yLWtleV0gYnV0IGRvIE5PVCBkZWNsYXJlIGEgY3VzdG9tIHZhbHVlX2Zyb20gYWRhcHRlci5cclxuXHRcdCAqIFRoaXMgbWFrZXMgc2VjdGlvbnMgZm9sbG93IHRoZSBzYW1lIGRhdGEgZmxvdyBhcyBmaWVsZHMgd2l0aCBhbG1vc3Qgbm8gZ2x1ZS5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge1dQQkNfRm9ybV9CdWlsZGVyfSBidWlsZGVyXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBzZWwgLSBjdXJyZW50bHkgc2VsZWN0ZWQgZmllbGQvc2VjdGlvblxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgX2dlbmVyaWNfaHlkcmF0ZV9jb250cm9scyhidWlsZGVyLCBzZWwpIHtcclxuXHRcdFx0Y29uc3QgaW5zID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGJjX2JmYl9faW5zcGVjdG9yJyApO1xyXG5cdFx0XHRpZiAoICFpbnMgfHwgIXNlbCApIHJldHVybjtcclxuXHJcblx0XHRcdGNvbnN0IFNLSVAgPSAvXihpZHxuYW1lfGh0bWxfaWR8Y3NzY2xhc3N8bGFiZWwpJC87IC8vIGhhbmRsZWQgYnkgX2h5ZHJhdGVfc3BlY2lhbF9jb250cm9sc1xyXG5cclxuXHRcdFx0Ly8gTkVXOiByZWFkIHNjaGVtYSBmb3IgdGhlIHNlbGVjdGVkIGVsZW1lbnTigJlzIHR5cGUuXHJcblx0XHRcdGNvbnN0IHNjaGVtYXMgICAgID0gd2luZG93LldQQkNfQkZCX1NjaGVtYXMgfHwge307XHJcblx0XHRcdGNvbnN0IHR5cGVLZXkgICAgID0gKHNlbC5kYXRhc2V0ICYmIHNlbC5kYXRhc2V0LnR5cGUpIHx8ICcnO1xyXG5cdFx0XHRjb25zdCBzY2hlbWFFbnRyeSA9IHNjaGVtYXNbdHlwZUtleV0gfHwgbnVsbDtcclxuXHRcdFx0Y29uc3QgcHJvcHNTY2hlbWEgPSAoc2NoZW1hRW50cnkgJiYgc2NoZW1hRW50cnkuc2NoZW1hICYmIHNjaGVtYUVudHJ5LnNjaGVtYS5wcm9wcykgPyBzY2hlbWFFbnRyeS5zY2hlbWEucHJvcHMgOiB7fTtcclxuXHRcdFx0Y29uc3QgaGFzT3duICAgICAgPSBGdW5jdGlvbi5wcm90b3R5cGUuY2FsbC5iaW5kKCBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5ICk7XHJcblx0XHRcdGNvbnN0IGdldERlZmF1bHQgID0gKGtleSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IG1ldGEgPSBwcm9wc1NjaGVtYVtrZXldO1xyXG5cdFx0XHRcdHJldHVybiAobWV0YSAmJiBoYXNPd24oIG1ldGEsICdkZWZhdWx0JyApKSA/IG1ldGEuZGVmYXVsdCA6IHVuZGVmaW5lZDtcclxuXHRcdFx0fTtcclxuXHJcblx0XHRcdGlucy5xdWVyeVNlbGVjdG9yQWxsKCAnW2RhdGEtaW5zcGVjdG9yLWtleV0nICkuZm9yRWFjaCggKGN0cmwpID0+IHtcclxuXHRcdFx0XHRjb25zdCBrZXkgPSBTdHJpbmcoIGN0cmwuZGF0YXNldD8uaW5zcGVjdG9yS2V5IHx8ICcnICkudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHRpZiAoICFrZXkgfHwgU0tJUC50ZXN0KCBrZXkgKSApIHJldHVybjtcclxuXHJcblx0XHRcdFx0Ly8gRWxlbWVudC1sZXZlbCBsb2NrLlxyXG5cdFx0XHRcdGNvbnN0IGRsID0gKGN0cmwuZGF0YXNldD8ubG9ja2VkIHx8ICcnKS50cmltKCkudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHRpZiAoIGRsID09PSAnMScgfHwgZGwgPT09ICd0cnVlJyB8fCBkbCA9PT0gJ3llcycgKSByZXR1cm47XHJcblxyXG5cdFx0XHRcdC8vIFJlc3BlY3QgZXhwbGljaXQgYWRhcHRlcnMuXHJcblx0XHRcdFx0aWYgKCBjdHJsLmRhdGFzZXQ/LnZhbHVlX2Zyb20gfHwgY3RybC5kYXRhc2V0Py52YWx1ZUZyb20gKSByZXR1cm47XHJcblxyXG5cdFx0XHRcdGNvbnN0IHJhdyAgICAgID0gc2VsLmRhdGFzZXQgPyBzZWwuZGF0YXNldFtrZXldIDogdW5kZWZpbmVkO1xyXG5cdFx0XHRcdGNvbnN0IGhhc1JhdyAgID0gc2VsLmRhdGFzZXQgPyBoYXNPd24oIHNlbC5kYXRhc2V0LCBrZXkgKSA6IGZhbHNlO1xyXG5cdFx0XHRcdGNvbnN0IGRlZlZhbHVlID0gZ2V0RGVmYXVsdCgga2V5ICk7XHJcblxyXG5cdFx0XHRcdC8vIEJlc3QtZWZmb3J0IGNvbnRyb2wgdHlwaW5nIHdpdGggc2NoZW1hIGRlZmF1bHQgZmFsbGJhY2sgd2hlbiB2YWx1ZSBpcyBhYnNlbnQuXHJcblxyXG5cdFx0XHRcdGlmICggY3RybCBpbnN0YW5jZW9mIEhUTUxJbnB1dEVsZW1lbnQgJiYgKGN0cmwudHlwZSA9PT0gJ2NoZWNrYm94JyB8fCBjdHJsLnR5cGUgPT09ICdyYWRpbycpICkge1xyXG5cdFx0XHRcdFx0Ly8gSWYgZGF0YXNldCBpcyBtaXNzaW5nIHRoZSBrZXkgZW50aXJlbHkgLT4gdXNlIHNjaGVtYSBkZWZhdWx0IChib29sZWFuKS5cclxuXHRcdFx0XHRcdGlmICggIWhhc1JhdyApIHtcclxuXHRcdFx0XHRcdFx0Y3RybC5jaGVja2VkID0gISFkZWZWYWx1ZTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGN0cmwuY2hlY2tlZCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuY29lcmNlX2Jvb2xlYW4oIHJhdywgISFkZWZWYWx1ZSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gZWxzZSBpZiAoICd2YWx1ZScgaW4gY3RybCApIHtcclxuXHRcdFx0XHRcdGlmICggaGFzUmF3ICkge1xyXG5cdFx0XHRcdFx0XHRjdHJsLnZhbHVlID0gKHJhdyAhPSBudWxsKSA/IFN0cmluZyggcmF3ICkgOiAnJztcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGN0cmwudmFsdWUgPSAoZGVmVmFsdWUgPT0gbnVsbCkgPyAnJyA6IFN0cmluZyggZGVmVmFsdWUgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHRfYmluZF9pZF9zYW5pdGl6ZXIoKSB7XHJcblx0XHRcdGNvbnN0IGIgICA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0Y29uc3QgaW5zID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGJjX2JmYl9faW5zcGVjdG9yJyApO1xyXG5cdFx0XHRpZiAoICEgaW5zICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoIGlucy5fX3dwYmNfYmZiX2lkX3Nhbml0aXplcl9ib3VuZCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0aW5zLl9fd3BiY19iZmJfaWRfc2FuaXRpemVyX2JvdW5kID0gdHJ1ZTtcclxuXHJcblx0XHRcdGNvbnN0IGhhbmRsZXIgPSAoZSkgPT4ge1xyXG5cclxuXHRcdFx0XHRjb25zdCB0ID0gZS50YXJnZXQ7XHJcblx0XHRcdFx0aWYgKCAhdCB8fCAhKCd2YWx1ZScgaW4gdCkgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGNvbnN0IGtleSAgICAgICA9ICh0LmRhdGFzZXQ/Lmluc3BlY3RvcktleSB8fCAnJykudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHRjb25zdCBzZWwgICAgICAgPSBiLmdldF9zZWxlY3RlZF9maWVsZD8uKCk7XHJcblx0XHRcdFx0Y29uc3QgaXNTZWN0aW9uID0gc2VsPy5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICk7XHJcblx0XHRcdFx0aWYgKCAhc2VsICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0XHQvLyBVbmlmaWVkIGVtaXR0ZXIgdGhhdCBhbHdheXMgaW5jbHVkZXMgdGhlIGVsZW1lbnQgcmVmZXJlbmNlLlxyXG5cdFx0XHRcdGNvbnN0IEVWICAgICAgICAgICAgICA9IENvcmUuV1BCQ19CRkJfRXZlbnRzO1xyXG5cdFx0XHRcdC8vIFNUUlVDVFVSRV9DSEFOR0UgY2FuIGJlIFwiZXhwZW5zaXZlXCIgYmVjYXVzZSBvdGhlciBsaXN0ZW5lcnMgbWF5IHRyaWdnZXIgZnVsbCBjYW52YXMgcmVmcmVzaC5cclxuXHRcdFx0XHQvLyBEZWJvdW5jZSBvbmx5IGNvbnRpbnVvdXMgY29udHJvbHMgKGUuZy4gdmFsdWUgc2xpZGVyIHNjcnViYmluZykgb24gdGhlIElOUFVUIHBoYXNlLlxyXG5cdFx0XHRcdGNvbnN0IGVuc3VyZV9zY19kZWJvdW5jZV9zdGF0ZSA9ICgpID0+IHtcclxuXHRcdFx0XHRcdGlmICggYi5fX3dwYmNfYmZiX3NjX2RlYm91bmNlX3N0YXRlICkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gYi5fX3dwYmNfYmZiX3NjX2RlYm91bmNlX3N0YXRlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0Yi5fX3dwYmNfYmZiX3NjX2RlYm91bmNlX3N0YXRlID0geyB0aW1lcl9pZDogMCwgcGVuZGluZ19wYXlsb2FkOiBudWxsIH07XHJcblx0XHRcdFx0XHRyZXR1cm4gYi5fX3dwYmNfYmZiX3NjX2RlYm91bmNlX3N0YXRlO1xyXG5cdFx0XHRcdH07XHJcblxyXG5cdFx0XHRcdGNvbnN0IGNhbmNlbF9zY19kZWJvdW5jZWRfZW1pdCA9ICgpID0+IHtcclxuXHRcdFx0XHRcdGNvbnN0IHN0ID0gYi5fX3dwYmNfYmZiX3NjX2RlYm91bmNlX3N0YXRlO1xyXG5cdFx0XHRcdFx0aWYgKCAhc3QgKSByZXR1cm47XHJcblx0XHRcdFx0XHR0cnkgeyBjbGVhclRpbWVvdXQoIHN0LnRpbWVyX2lkICk7IH0gY2F0Y2ggKCBfICkge31cclxuXHRcdFx0XHRcdHN0LnRpbWVyX2lkICAgICAgICA9IDA7XHJcblx0XHRcdFx0XHRzdC5wZW5kaW5nX3BheWxvYWQgPSBudWxsO1xyXG5cdFx0XHRcdH07XHJcblxyXG5cdFx0XHRcdGNvbnN0IGJ1c19lbWl0X2NoYW5nZSA9IChyZWFzb24sIGV4dHJhID0ge30pID0+IHtcclxuXHRcdFx0XHRcdC8vIElmIHdl4oCZcmUgY29tbWl0dGluZyBzb21ldGhpbmcgKGNoYW5nZS9ibHVyL2V0YyksIGRyb3AgYW55IHBlbmRpbmcgXCJpbnB1dFwiIGVtaXQuXHJcblx0XHRcdFx0XHRjYW5jZWxfc2NfZGVib3VuY2VkX2VtaXQoKTtcclxuXHRcdFx0XHRcdGIuYnVzPy5lbWl0Py4oIEVWLlNUUlVDVFVSRV9DSEFOR0UsIHsgcmVhc29uLCBlbDogc2VsLCAuLi5leHRyYSB9ICk7XHJcblx0XHRcdFx0fTtcclxuXHJcblx0XHRcdFx0Y29uc3QgYnVzX2VtaXRfY2hhbmdlX2RlYm91bmNlZCA9IChyZWFzb24sIGV4dHJhID0ge30sIHdhaXRfbXMpID0+IHtcclxuXHRcdFx0XHRcdGNvbnN0IHN0ID0gZW5zdXJlX3NjX2RlYm91bmNlX3N0YXRlKCk7XHJcblx0XHRcdFx0XHRjb25zdCBtcyA9IE51bWJlci5pc0Zpbml0ZSggd2FpdF9tcyApXHJcblx0XHRcdFx0XHRcdD8gd2FpdF9tc1xyXG5cdFx0XHRcdFx0XHQ6IChOdW1iZXIuaXNGaW5pdGUoIFVJLlNUUlVDVFVSRV9DSEFOR0VfREVCT1VOQ0VfTVMgKSA/IFVJLlNUUlVDVFVSRV9DSEFOR0VfREVCT1VOQ0VfTVMgOiAyNDApO1xyXG5cclxuXHRcdFx0XHRcdC8vIENhcHR1cmUgdGhlIENVUlJFTlQgc2VsZWN0ZWQgZWxlbWVudCBpbnRvIHRoZSBwYXlsb2FkIG5vdyAoc3RhYmxlIHJlZikuXHJcblx0XHRcdFx0XHRzdC5wZW5kaW5nX3BheWxvYWQgPSB7IHJlYXNvbiwgZWw6IHNlbCwgLi4uZXh0cmEsIGRlYm91bmNlZDogdHJ1ZSB9O1xyXG5cclxuXHRcdFx0XHRcdHRyeSB7IGNsZWFyVGltZW91dCggc3QudGltZXJfaWQgKTsgfSBjYXRjaCAoIF8gKSB7fVxyXG5cdFx0XHRcdFx0c3QudGltZXJfaWQgPSBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0XHRcdHN0LnRpbWVyX2lkID0gMDtcclxuXHRcdFx0XHRcdFx0Y29uc3QgcGF5bG9hZCA9IHN0LnBlbmRpbmdfcGF5bG9hZDtcclxuXHRcdFx0XHRcdFx0c3QucGVuZGluZ19wYXlsb2FkID0gbnVsbDtcclxuXHRcdFx0XHRcdFx0aWYgKCBwYXlsb2FkICkge1xyXG5cdFx0XHRcdFx0XHRcdGIuYnVzPy5lbWl0Py4oIEVWLlNUUlVDVFVSRV9DSEFOR0UsIHBheWxvYWQgKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSwgbXMgKTtcclxuXHRcdFx0XHR9O1xyXG5cclxuXHRcdFx0XHQvLyAtLS0tIEZJRUxEL1NFQ1RJT046IGludGVybmFsIGlkIC0tLS1cclxuXHRcdFx0XHRpZiAoIGtleSA9PT0gJ2lkJyApIHtcclxuXHRcdFx0XHRcdGNvbnN0IHVuaXF1ZSA9IGIuaWQuc2V0X2ZpZWxkX2lkKCBzZWwsIHQudmFsdWUgKTtcclxuXHRcdFx0XHRcdGlmICggYi5wcmV2aWV3X21vZGUgJiYgIWlzU2VjdGlvbiApIHtcclxuXHRcdFx0XHRcdFx0Yi5yZW5kZXJfcHJldmlldyggc2VsICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIHQudmFsdWUgIT09IHVuaXF1ZSApIHtcclxuXHRcdFx0XHRcdFx0dC52YWx1ZSA9IHVuaXF1ZTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGJ1c19lbWl0X2NoYW5nZSggJ2lkLWNoYW5nZScgKTtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIC0tLS0gRklFTEQvU0VDVElPTjogcHVibGljIEhUTUwgaWQgLS0tLVxyXG5cdFx0XHRcdGlmICgga2V5ID09PSAnaHRtbF9pZCcgKSB7XHJcblx0XHRcdFx0XHRjb25zdCBhcHBsaWVkID0gYi5pZC5zZXRfZmllbGRfaHRtbF9pZCggc2VsLCB0LnZhbHVlICk7XHJcblx0XHRcdFx0XHQvLyBGb3Igc2VjdGlvbnMsIGFsc28gc2V0IHRoZSByZWFsIERPTSBpZCBzbyBhbmNob3JzL0NTUyBjYW4gdGFyZ2V0IGl0LlxyXG5cdFx0XHRcdFx0aWYgKCBpc1NlY3Rpb24gKSB7XHJcblx0XHRcdFx0XHRcdHNlbC5pZCA9IGFwcGxpZWQgfHwgJyc7XHJcblx0XHRcdFx0XHR9IGVsc2UgaWYgKCBiLnByZXZpZXdfbW9kZSApIHtcclxuXHRcdFx0XHRcdFx0Yi5yZW5kZXJfcHJldmlldyggc2VsICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIHQudmFsdWUgIT09IGFwcGxpZWQgKSB7XHJcblx0XHRcdFx0XHRcdHQudmFsdWUgPSBhcHBsaWVkO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0YnVzX2VtaXRfY2hhbmdlKCAnaHRtbC1pZC1jaGFuZ2UnICk7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyAtLS0tIEZJRUxEUyBPTkxZOiBuYW1lIC0tLS1cclxuXHRcdFx0XHRpZiAoIGtleSA9PT0gJ25hbWUnICYmICFpc1NlY3Rpb24gKSB7XHJcblxyXG5cdFx0XHRcdFx0Ly8gTGl2ZSB0eXBpbmc6IHNhbml0aXplIG9ubHkgKE5PIHVuaXF1ZW5lc3MgeWV0KSB0byBhdm9pZCBcIi0yXCIgc3BhbVxyXG5cdFx0XHRcdFx0aWYgKCBlLnR5cGUgPT09ICdpbnB1dCcgKSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IGJlZm9yZSAgICA9IHQudmFsdWU7XHJcblx0XHRcdFx0XHRcdGNvbnN0IHNhbml0aXplZCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuc2FuaXRpemVfaHRtbF9uYW1lKCBiZWZvcmUgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCBiZWZvcmUgIT09IHNhbml0aXplZCApIHtcclxuXHRcdFx0XHRcdFx0XHQvLyBvcHRpb25hbDogcHJlc2VydmUgY2FyZXQgdG8gYXZvaWQganVtcFxyXG5cdFx0XHRcdFx0XHRcdGNvbnN0IHNlbFN0YXJ0ID0gdC5zZWxlY3Rpb25TdGFydCwgc2VsRW5kID0gdC5zZWxlY3Rpb25FbmQ7XHJcblx0XHRcdFx0XHRcdFx0dC52YWx1ZSAgICAgICAgPSBzYW5pdGl6ZWQ7XHJcblx0XHRcdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0XHRcdHQuc2V0U2VsZWN0aW9uUmFuZ2UoIHNlbFN0YXJ0LCBzZWxFbmQgKTtcclxuXHRcdFx0XHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0cmV0dXJuOyAvLyB1bmlxdWVuZXNzIG9uIGNoYW5nZS9ibHVyXHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gQ29tbWl0IChjaGFuZ2UvYmx1cilcclxuXHRcdFx0XHRcdGNvbnN0IHJhdyA9IFN0cmluZyggdC52YWx1ZSA/PyAnJyApLnRyaW0oKTtcclxuXHJcblx0XHRcdFx0XHRpZiAoICFyYXcgKSB7XHJcblx0XHRcdFx0XHRcdC8vIFJFU0VFRDoga2VlcCBuYW1lIG5vbi1lbXB0eSBhbmQgcHJvdmlzaW9uYWwgKGF1dG9uYW1lIHN0YXlzIE9OKVxyXG5cdFx0XHRcdFx0XHRjb25zdCBTICAgID0gQ29yZS5XUEJDX0JGQl9TYW5pdGl6ZTtcclxuXHRcdFx0XHRcdFx0Y29uc3QgYmFzZSA9IFMuc2FuaXRpemVfaHRtbF9uYW1lKCBzZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1pZCcgKSB8fCBzZWwuZGF0YXNldC5pZCB8fCBzZWwuZGF0YXNldC50eXBlIHx8ICdmaWVsZCcgKTtcclxuXHRcdFx0XHRcdFx0Y29uc3QgdW5pcSA9IGIuaWQuZW5zdXJlX3VuaXF1ZV9maWVsZF9uYW1lKCBiYXNlLCBzZWwgKTtcclxuXHJcblx0XHRcdFx0XHRcdHNlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLW5hbWUnLCB1bmlxICk7XHJcblx0XHRcdFx0XHRcdHNlbC5kYXRhc2V0LmF1dG9uYW1lICAgICAgICAgID0gJzEnO1xyXG5cdFx0XHRcdFx0XHRzZWwuZGF0YXNldC5uYW1lX3VzZXJfdG91Y2hlZCA9ICcwJztcclxuXHJcblx0XHRcdFx0XHRcdC8vIEtlZXAgRE9NIGluIHN5bmMgaWYgd2XigJlyZSBub3QgcmUtcmVuZGVyaW5nXHJcblx0XHRcdFx0XHRcdGlmICggIWIucHJldmlld19tb2RlICkge1xyXG5cdFx0XHRcdFx0XHRcdGNvbnN0IGN0cmwgPSBzZWwucXVlcnlTZWxlY3RvciggJ2lucHV0LHRleHRhcmVhLHNlbGVjdCcgKTtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGN0cmwgKSBjdHJsLnNldEF0dHJpYnV0ZSggJ25hbWUnLCB1bmlxICk7XHJcblx0XHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdFx0Yi5yZW5kZXJfcHJldmlldyggc2VsICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRcdGlmICggdC52YWx1ZSAhPT0gdW5pcSApIHQudmFsdWUgPSB1bmlxO1xyXG5cdFx0XHRcdFx0XHRidXNfZW1pdF9jaGFuZ2UoICduYW1lLXJlc2VlZCcgKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIE5vbi1lbXB0eSBjb21taXQ6IHVzZXIgdGFrZXMgY29udHJvbDsgZGlzYWJsZSBhdXRvbmFtZSBnb2luZyBmb3J3YXJkXHJcblx0XHRcdFx0XHRzZWwuZGF0YXNldC5uYW1lX3VzZXJfdG91Y2hlZCA9ICcxJztcclxuXHRcdFx0XHRcdHNlbC5kYXRhc2V0LmF1dG9uYW1lICAgICAgICAgID0gJzAnO1xyXG5cclxuXHRcdFx0XHRcdGNvbnN0IHNhbml0aXplZCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuc2FuaXRpemVfaHRtbF9uYW1lKCByYXcgKTtcclxuXHRcdFx0XHRcdGNvbnN0IHVuaXF1ZSAgICA9IGIuaWQuc2V0X2ZpZWxkX25hbWUoIHNlbCwgc2FuaXRpemVkICk7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCAhYi5wcmV2aWV3X21vZGUgKSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IGN0cmwgPSBzZWwucXVlcnlTZWxlY3RvciggJ2lucHV0LHRleHRhcmVhLHNlbGVjdCcgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCBjdHJsICkgY3RybC5zZXRBdHRyaWJ1dGUoICduYW1lJywgdW5pcXVlICk7XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRiLnJlbmRlcl9wcmV2aWV3KCBzZWwgKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoIHQudmFsdWUgIT09IHVuaXF1ZSApIHQudmFsdWUgPSB1bmlxdWU7XHJcblx0XHRcdFx0XHRidXNfZW1pdF9jaGFuZ2UoICduYW1lLWNoYW5nZScgKTtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIC0tLS0gU0VDVElPTlMgJiBGSUVMRFM6IGNzc2NsYXNzIChsaXZlIGFwcGx5OyBubyByZS1yZW5kZXIpIC0tLS1cclxuXHRcdFx0XHRpZiAoIGtleSA9PT0gJ2Nzc2NsYXNzJyApIHtcclxuXHRcdFx0XHRcdGNvbnN0IG5leHQgICAgICAgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLnNhbml0aXplX2Nzc19jbGFzc2xpc3QoIHQudmFsdWUgfHwgJycgKTtcclxuXHRcdFx0XHRcdGNvbnN0IGRlc2lyZWRBcnIgPSBuZXh0LnNwbGl0KCAvXFxzKy8gKS5maWx0ZXIoIEJvb2xlYW4gKTtcclxuXHRcdFx0XHRcdGNvbnN0IGRlc2lyZWRTZXQgPSBuZXcgU2V0KCBkZXNpcmVkQXJyICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gQ29yZSBjbGFzc2VzIGFyZSBuZXZlciB0b3VjaGVkLlxyXG5cdFx0XHRcdFx0Y29uc3QgaXNDb3JlID0gKGNscykgPT4gY2xzID09PSAnaXMtc2VsZWN0ZWQnIHx8IGNscy5zdGFydHNXaXRoKCAnd3BiY18nICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gU25hcHNob3QgYmVmb3JlIG11dGF0aW5nIChET01Ub2tlbkxpc3QgaXMgbGl2ZSkuXHJcblx0XHRcdFx0XHRjb25zdCBiZWZvcmVDbGFzc2VzID0gQXJyYXkuZnJvbSggc2VsLmNsYXNzTGlzdCApO1xyXG5cdFx0XHRcdFx0Y29uc3QgY3VzdG9tQmVmb3JlICA9IGJlZm9yZUNsYXNzZXMuZmlsdGVyKCAoYykgPT4gIWlzQ29yZSggYyApICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gUmVtb3ZlIHN0cmF5IG5vbi1jb3JlIGNsYXNzZXMgbm90IGluIGRlc2lyZWQuXHJcblx0XHRcdFx0XHRjdXN0b21CZWZvcmUuZm9yRWFjaCggKGMpID0+IHtcclxuXHRcdFx0XHRcdFx0aWYgKCAhZGVzaXJlZFNldC5oYXMoIGMgKSApIHNlbC5jbGFzc0xpc3QucmVtb3ZlKCBjICk7XHJcblx0XHRcdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gQWRkIG1pc3NpbmcgZGVzaXJlZCBjbGFzc2VzIGluIG9uZSBnby5cclxuXHRcdFx0XHRcdGNvbnN0IG1pc3NpbmcgPSBkZXNpcmVkQXJyLmZpbHRlciggKGMpID0+ICFjdXN0b21CZWZvcmUuaW5jbHVkZXMoIGMgKSApO1xyXG5cdFx0XHRcdFx0aWYgKCBtaXNzaW5nLmxlbmd0aCApIHNlbC5jbGFzc0xpc3QuYWRkKCAuLi5taXNzaW5nICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gS2VlcCBkYXRhc2V0IGluIHN5bmMgKGF2b2lkIHVzZWxlc3MgYXR0cmlidXRlIHdyaXRlcykuXHJcblx0XHRcdFx0XHRpZiAoIHNlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWNzc2NsYXNzJyApICE9PSBuZXh0ICkge1xyXG5cdFx0XHRcdFx0XHRzZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1jc3NjbGFzcycsIG5leHQgKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBFbWl0IG9ubHkgaWYgc29tZXRoaW5nIGFjdHVhbGx5IGNoYW5nZWQuXHJcblx0XHRcdFx0XHRjb25zdCBhZnRlckNsYXNzZXMgPSBBcnJheS5mcm9tKCBzZWwuY2xhc3NMaXN0ICk7XHJcblx0XHRcdFx0XHRjb25zdCBjaGFuZ2VkICAgICAgPSBhZnRlckNsYXNzZXMubGVuZ3RoICE9PSBiZWZvcmVDbGFzc2VzLmxlbmd0aCB8fCBiZWZvcmVDbGFzc2VzLnNvbWUoIChjLCBpKSA9PiBjICE9PSBhZnRlckNsYXNzZXNbaV0gKTtcclxuXHJcblx0XHRcdFx0XHRjb25zdCBkZXRhaWwgPSB7IGtleTogJ2Nzc2NsYXNzJywgcGhhc2U6IGUudHlwZSB9O1xyXG5cdFx0XHRcdFx0aWYgKCBpc1NlY3Rpb24gKSB7XHJcblx0XHRcdFx0XHRcdGJ1c19lbWl0X2NoYW5nZSggJ2Nzc2NsYXNzLWNoYW5nZScsIGRldGFpbCApO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0YnVzX2VtaXRfY2hhbmdlKCAncHJvcC1jaGFuZ2UnLCBkZXRhaWwgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblxyXG5cclxuXHRcdFx0XHQvLyAtLS0tIFNFQ1RJT05TOiBsYWJlbCAtLS0tXHJcblx0XHRcdFx0aWYgKCBpc1NlY3Rpb24gJiYga2V5ID09PSAnbGFiZWwnICkge1xyXG5cdFx0XHRcdFx0Y29uc3QgdmFsID0gU3RyaW5nKCB0LnZhbHVlID8/ICcnICk7XHJcblx0XHRcdFx0XHRzZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1sYWJlbCcsIHZhbCApO1xyXG5cdFx0XHRcdFx0YnVzX2VtaXRfY2hhbmdlKCAnbGFiZWwtY2hhbmdlJyApO1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gLS0tLSBGSUVMRFM6IGxhYmVsIChhdXRvLW5hbWUgd2hpbGUgdHlwaW5nOyBmcmVlemUgb24gY29tbWl0KSAtLS0tXHJcblx0XHRcdFx0aWYgKCAhaXNTZWN0aW9uICYmIGtleSA9PT0gJ2xhYmVsJyApIHtcclxuXHRcdFx0XHRcdGNvbnN0IHZhbCAgICAgICAgID0gU3RyaW5nKCB0LnZhbHVlID8/ICcnICk7XHJcblx0XHRcdFx0XHRzZWwuZGF0YXNldC5sYWJlbCA9IHZhbDtcclxuXHJcblx0XHRcdFx0XHQvLyB3aGlsZSB0eXBpbmcsIGFsbG93IGF1dG8tbmFtZSAoaWYgZmxhZ3MgcGVybWl0KVxyXG5cdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0Q29yZS5XUEJDX0JGQl9GaWVsZF9CYXNlLm1heWJlX2F1dG9uYW1lX2Zyb21fbGFiZWwoIGIsIHNlbCwgdmFsICk7XHJcblx0XHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBpZiB1c2VyIGNvbW1pdHRlZCB0aGUgbGFiZWwgKGJsdXIvY2hhbmdlKSwgZnJlZXplIGZ1dHVyZSBhdXRvLW5hbWVcclxuXHRcdFx0XHRcdGlmICggZS50eXBlICE9PSAnaW5wdXQnICkge1xyXG5cdFx0XHRcdFx0XHRzZWwuZGF0YXNldC5hdXRvbmFtZSA9ICcwJzsgICAvLyBzdG9wIGZ1dHVyZSBsYWJlbC0+bmFtZSBzeW5jXHJcblx0XHRcdFx0XHRcdHNlbC5kYXRhc2V0LmZyZXNoICAgID0gJzAnOyAgIC8vIGFsc28ga2lsbCB0aGUgXCJmcmVzaFwiIGVzY2FwZSBoYXRjaFxyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIE9wdGlvbmFsIFVJIG5pY2V0eTogZGlzYWJsZSBOYW1lIHdoZW4gYXV0byBpcyBPTiwgZW5hYmxlIHdoZW4gT0ZGXHJcblx0XHRcdFx0XHRjb25zdCBpbnMgICAgICA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0XHRcdGNvbnN0IG5hbWVDdHJsID0gaW5zPy5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtaW5zcGVjdG9yLWtleT1cIm5hbWVcIl0nICk7XHJcblx0XHRcdFx0XHRpZiAoIG5hbWVDdHJsICkge1xyXG5cdFx0XHRcdFx0XHRjb25zdCBhdXRvQWN0aXZlID1cclxuXHRcdFx0XHRcdFx0XHRcdCAgKHNlbC5kYXRhc2V0LmF1dG9uYW1lID8/ICcxJykgIT09ICcwJyAmJlxyXG5cdFx0XHRcdFx0XHRcdFx0ICBzZWwuZGF0YXNldC5uYW1lX3VzZXJfdG91Y2hlZCAhPT0gJzEnICYmXHJcblx0XHRcdFx0XHRcdFx0XHQgIHNlbC5kYXRhc2V0Lndhc19sb2FkZWQgIT09ICcxJztcclxuXHRcdFx0XHRcdFx0bmFtZUN0cmwudG9nZ2xlQXR0cmlidXRlKCAnZGlzYWJsZWQnLCBhdXRvQWN0aXZlICk7XHJcblx0XHRcdFx0XHRcdGlmICggYXV0b0FjdGl2ZSAmJiAhbmFtZUN0cmwucGxhY2Vob2xkZXIgKSB7XHJcblx0XHRcdFx0XHRcdFx0bmFtZUN0cmwucGxhY2Vob2xkZXIgPSBiPy5pMThuPy5hdXRvX2Zyb21fbGFiZWwgPz8gJ2F1dG8g4oCUIGZyb20gbGFiZWwnO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdGlmICggIWF1dG9BY3RpdmUgJiYgbmFtZUN0cmwucGxhY2Vob2xkZXIgPT09IChiPy5pMThuPy5hdXRvX2Zyb21fbGFiZWwgPz8gJ2F1dG8g4oCUIGZyb20gbGFiZWwnKSApIHtcclxuXHRcdFx0XHRcdFx0XHRuYW1lQ3RybC5wbGFjZWhvbGRlciA9ICcnO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gQWx3YXlzIHJlLXJlbmRlciB0aGUgcHJldmlldyBzbyBsYWJlbCBjaGFuZ2VzIGFyZSB2aXNpYmxlIGltbWVkaWF0ZWx5LlxyXG5cdFx0XHRcdFx0Yi5yZW5kZXJfcHJldmlldyggc2VsICk7XHJcblx0XHRcdFx0XHRidXNfZW1pdF9jaGFuZ2UoICdsYWJlbC1jaGFuZ2UnICk7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cclxuXHJcblx0XHRcdFx0Ly8gLS0tLSBERUZBVUxUIChHRU5FUklDKTogZGF0YXNldCB3cml0ZXIgZm9yIGJvdGggZmllbGRzICYgc2VjdGlvbnMgLS0tLVxyXG5cdFx0XHRcdC8vIEFueSBpbnNwZWN0b3IgY29udHJvbCB3aXRoIFtkYXRhLWluc3BlY3Rvci1rZXldIHRoYXQgZG9lc24ndCBoYXZlIGEgY3VzdG9tXHJcblx0XHRcdFx0Ly8gYWRhcHRlci92YWx1ZV9mcm9tIHdpbGwgc2ltcGx5IHJlYWQvd3JpdGUgc2VsLmRhdGFzZXRba2V5XS5cclxuXHRcdFx0XHRpZiAoIGtleSApIHtcclxuXHJcblx0XHRcdFx0XHRjb25zdCBzZWxmTG9ja2VkID0gL14oMXx0cnVlfHllcykkL2kudGVzdCggKHQuZGF0YXNldD8ubG9ja2VkIHx8ICcnKS50cmltKCkgKTtcclxuXHRcdFx0XHRcdGlmICggc2VsZkxvY2tlZCApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFNraXAga2V5cyB3ZSBoYW5kbGVkIGFib3ZlIHRvIGF2b2lkIGRvdWJsZSB3b3JrLlxyXG5cdFx0XHRcdFx0aWYgKCBrZXkgPT09ICdpZCcgfHwga2V5ID09PSAnbmFtZScgfHwga2V5ID09PSAnaHRtbF9pZCcgfHwga2V5ID09PSAnY3NzY2xhc3MnIHx8IGtleSA9PT0gJ2xhYmVsJyApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0bGV0IG5leHRWYWwgPSAnJztcclxuXHRcdFx0XHRcdGlmICggdCBpbnN0YW5jZW9mIEhUTUxJbnB1dEVsZW1lbnQgJiYgKHQudHlwZSA9PT0gJ2NoZWNrYm94JyB8fCB0LnR5cGUgPT09ICdyYWRpbycpICkge1xyXG5cdFx0XHRcdFx0XHRuZXh0VmFsID0gdC5jaGVja2VkID8gJzEnIDogJyc7XHJcblx0XHRcdFx0XHR9IGVsc2UgaWYgKCAndmFsdWUnIGluIHQgKSB7XHJcblx0XHRcdFx0XHRcdG5leHRWYWwgPSBTdHJpbmcoIHQudmFsdWUgPz8gJycgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdC8vIFBlcnNpc3QgdG8gZGF0YXNldC5cclxuXHRcdFx0XHRcdGlmICggc2VsPy5kYXRhc2V0ICkgc2VsLmRhdGFzZXRba2V5XSA9IG5leHRWYWw7XHJcblxyXG5cdFx0XHRcdFx0Ly8gR2VuZXJhdG9yIGNvbnRyb2xzIGFyZSBcIlVJIGlucHV0c1wiIOKAlCBhdm9pZCBTVFJVQ1RVUkVfQ0hBTkdFIHNwYW0gd2hpbGUgZHJhZ2dpbmcvdHlwaW5nLlxyXG5cdFx0XHRcdFx0Y29uc3QgaXNfZ2VuX2tleSA9IChrZXkuaW5kZXhPZiggJ2dlbl8nICkgPT09IDApO1xyXG5cclxuXHRcdFx0XHRcdC8vIFJlLXJlbmRlciBvbiB2aXN1YWwga2V5cyBzbyBwcmV2aWV3IHN0YXlzIGluIHN5bmMgKGNhbGVuZGFyIGxhYmVsL2hlbHAsIGV0Yy4pLlxyXG5cdFx0XHRcdFx0Y29uc3QgdmlzdWFsS2V5cyA9IG5ldyBTZXQoIFsgJ2hlbHAnLCAncGxhY2Vob2xkZXInLCAnbWluX3dpZHRoJywgJ2Nzc2NsYXNzJyBdICk7XHJcblx0XHRcdFx0XHRpZiAoICFpc1NlY3Rpb24gJiYgKHZpc3VhbEtleXMuaGFzKCBrZXkgKSB8fCBrZXkuc3RhcnRzV2l0aCggJ3VpXycgKSkgKSB7XHJcblx0XHRcdFx0XHRcdC8vIExpZ2h0IGhldXJpc3RpYzogb25seSByZS1yZW5kZXIgb24gY29tbWl0IGZvciBoZWF2eSBpbnB1dHM7IGxpdmUgZm9yIHNob3J0IG9uZXMgaXMgZmluZS5cclxuXHRcdFx0XHRcdFx0aWYgKCBlLnR5cGUgPT09ICdjaGFuZ2UnIHx8IGtleSA9PT0gJ2hlbHAnIHx8IGtleSA9PT0gJ3BsYWNlaG9sZGVyJyApIHtcclxuXHRcdFx0XHRcdFx0XHRiLnJlbmRlcl9wcmV2aWV3KCBzZWwgKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdGlmICggIShpc19nZW5fa2V5ICYmIGUudHlwZSA9PT0gJ2lucHV0JykgKSB7XHJcblx0XHRcdFx0XHRcdC8vIERlYm91bmNlIGNvbnRpbnVvdXMgdmFsdWUgc2xpZGVyIGlucHV0IGV2ZW50cyB0byBhdm9pZCBmdWxsLWNhbnZhcyByZWZyZXNoIHNwYW0uXHJcblx0XHRcdFx0XHRcdC8vIFdlIGRldGVjdCB0aGUgc2xpZGVyIGdyb3VwIHZpYSBbZGF0YS1sZW4tZ3JvdXBdIHdyYXBwZXIuXHJcblx0XHRcdFx0XHRcdGNvbnN0IGlzX2xlbl9ncm91cF9jdHJsID0gISEodCAmJiB0LmNsb3Nlc3QgJiYgdC5jbG9zZXN0KCAnW2RhdGEtbGVuLWdyb3VwXScgKSk7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoIGlzX2xlbl9ncm91cF9jdHJsICYmIGUudHlwZSA9PT0gJ2lucHV0JyApIHtcclxuXHRcdFx0XHRcdFx0XHRidXNfZW1pdF9jaGFuZ2VfZGVib3VuY2VkKCAncHJvcC1jaGFuZ2UnLCB7IGtleSwgcGhhc2U6IGUudHlwZSB9ICk7XHJcblx0XHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdFx0YnVzX2VtaXRfY2hhbmdlKCAncHJvcC1jaGFuZ2UnLCB7IGtleSwgcGhhc2U6IGUudHlwZSB9ICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHRpbnMuYWRkRXZlbnRMaXN0ZW5lciggJ2NoYW5nZScsIGhhbmRsZXIsIHRydWUgKTtcclxuXHRcdFx0Ly8gcmVmbGVjdCBpbnN0YW50bHkgd2hpbGUgdHlwaW5nIGFzIHdlbGwuXHJcblx0XHRcdGlucy5hZGRFdmVudExpc3RlbmVyKCAnaW5wdXQnLCBoYW5kbGVyLCB0cnVlICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBPcGVuIEluc3BlY3RvciBhZnRlciBhIGZpZWxkIGlzIGFkZGVkLlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0X29wZW5faW5zcGVjdG9yX2FmdGVyX2ZpZWxkX2FkZGVkKCkge1xyXG5cdFx0XHRjb25zdCBFViA9IENvcmUuV1BCQ19CRkJfRXZlbnRzO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXI/LmJ1cz8ub24/LiggRVYuRklFTERfQURELCAoZSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IGVsID0gZT8uZGV0YWlsPy5lbCB8fCBudWxsO1xyXG5cdFx0XHRcdGlmICggZWwgJiYgdGhpcy5idWlsZGVyPy5zZWxlY3RfZmllbGQgKSB7XHJcblx0XHRcdFx0XHR0aGlzLmJ1aWxkZXIuc2VsZWN0X2ZpZWxkKCBlbCwgeyBzY3JvbGxJbnRvVmlldzogdHJ1ZSB9ICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdC8vIFNob3cgSW5zcGVjdG9yIFBhbGV0dGUuXHJcblx0XHRcdFx0ZG9jdW1lbnQuZGlzcGF0Y2hFdmVudCggbmV3IEN1c3RvbUV2ZW50KCAnd3BiY19iZmI6c2hvd19wYW5lbCcsIHtcclxuXHRcdFx0XHRcdGRldGFpbDoge1xyXG5cdFx0XHRcdFx0XHRwYW5lbF9pZDogJ3dwYmNfYmZiX19pbnNwZWN0b3InLFxyXG5cdFx0XHRcdFx0XHR0YWJfaWQgIDogJ3dwYmNfdGFiX2luc3BlY3RvcidcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9ICkgKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEtleWJvYXJkIHNob3J0Y3V0cyBmb3Igc2VsZWN0aW9uLCBkZWxldGlvbiwgYW5kIG1vdmVtZW50LlxyXG5cdCAqL1xyXG5cdFVJLldQQkNfQkZCX0tleWJvYXJkX0NvbnRyb2xsZXIgPSBjbGFzcyBleHRlbmRzIFVJLldQQkNfQkZCX01vZHVsZSB7XHJcblx0XHRpbml0KCkge1xyXG5cdFx0XHR0aGlzLl9vbl9rZXkgPSB0aGlzLm9uX2tleS5iaW5kKCB0aGlzICk7XHJcblx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICdrZXlkb3duJywgdGhpcy5fb25fa2V5LCB0cnVlICk7XHJcblx0XHR9XHJcblxyXG5cdFx0ZGVzdHJveSgpIHtcclxuXHRcdFx0ZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ2tleWRvd24nLCB0aGlzLl9vbl9rZXksIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKiogQHBhcmFtIHtLZXlib2FyZEV2ZW50fSBlICovXHJcblx0XHRvbl9rZXkoZSkge1xyXG5cdFx0XHRjb25zdCBiICAgICAgICAgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdGNvbnN0IGlzX3R5cGluZyA9IHRoaXMuX2lzX3R5cGluZ19hbnl3aGVyZSgpO1xyXG5cdFx0XHRpZiAoIGUua2V5ID09PSAnRXNjYXBlJyApIHtcclxuXHRcdFx0XHRpZiAoIGlzX3R5cGluZyApIHtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0dGhpcy5idWlsZGVyLmJ1cy5lbWl0KCBDb3JlLldQQkNfQkZCX0V2ZW50cy5DTEVBUl9TRUxFQ1RJT04sIHsgc291cmNlOiAnZXNjJyB9ICk7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdGNvbnN0IHNlbGVjdGVkID0gYi5nZXRfc2VsZWN0ZWRfZmllbGQ/LigpO1xyXG5cdFx0XHRpZiAoICFzZWxlY3RlZCB8fCBpc190eXBpbmcgKSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggZS5rZXkgPT09ICdEZWxldGUnIHx8IGUua2V5ID09PSAnQmFja3NwYWNlJyApIHtcclxuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0Yi5kZWxldGVfaXRlbT8uKCBzZWxlY3RlZCApO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoIChlLmFsdEtleSB8fCBlLmN0cmxLZXkgfHwgZS5tZXRhS2V5KSAmJiAoZS5rZXkgPT09ICdBcnJvd1VwJyB8fCBlLmtleSA9PT0gJ0Fycm93RG93bicpICYmICFlLnNoaWZ0S2V5ICkge1xyXG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0XHRjb25zdCBkaXIgPSAoZS5rZXkgPT09ICdBcnJvd1VwJykgPyAndXAnIDogJ2Rvd24nO1xyXG5cdFx0XHRcdGIubW92ZV9pdGVtPy4oIHNlbGVjdGVkLCBkaXIgKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBlLmtleSA9PT0gJ0VudGVyJyApIHtcclxuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0Yi5zZWxlY3RfZmllbGQoIHNlbGVjdGVkLCB7IHNjcm9sbEludG9WaWV3OiB0cnVlIH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcmV0dXJucyB7Ym9vbGVhbn0gKi9cclxuXHRcdF9pc190eXBpbmdfYW55d2hlcmUoKSB7XHJcblx0XHRcdGNvbnN0IGEgICA9IGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQ7XHJcblx0XHRcdGNvbnN0IHRhZyA9IGE/LnRhZ05hbWU7XHJcblx0XHRcdGlmICggdGFnID09PSAnSU5QVVQnIHx8IHRhZyA9PT0gJ1RFWFRBUkVBJyB8fCB0YWcgPT09ICdTRUxFQ1QnIHx8IChhPy5pc0NvbnRlbnRFZGl0YWJsZSA9PT0gdHJ1ZSkgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0Y29uc3QgaW5zID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGJjX2JmYl9faW5zcGVjdG9yJyApO1xyXG5cdFx0XHRyZXR1cm4gISEoaW5zICYmIGEgJiYgaW5zLmNvbnRhaW5zKCBhICkpO1xyXG5cdFx0fVxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIENvbHVtbiByZXNpemUgbG9naWMgZm9yIHNlY3Rpb24gcm93cy5cclxuXHQgKi9cclxuXHRVSS5XUEJDX0JGQl9SZXNpemVfQ29udHJvbGxlciA9IGNsYXNzIGV4dGVuZHMgVUkuV1BCQ19CRkJfTW9kdWxlIHtcclxuXHRcdGluaXQoKSB7XHJcblx0XHRcdHRoaXMuYnVpbGRlci5pbml0X3Jlc2l6ZV9oYW5kbGVyID0gdGhpcy5oYW5kbGVfcmVzaXplLmJpbmQoIHRoaXMgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIHJlYWQgdGhlIENTUyB2YXIgKGtlcHQgbG9jYWwgc28gaXQgZG9lc27igJl0IGRlcGVuZCBvbiB0aGUgTWluLVdpZHRoIG1vZHVsZSlcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gY29sXHJcblx0XHQgKiBAcmV0dXJucyB7bnVtYmVyfG51bWJlcn1cclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKi9cclxuXHRcdF9nZXRfY29sX21pbl9weChjb2wpIHtcclxuXHRcdFx0Y29uc3QgdiA9IGdldENvbXB1dGVkU3R5bGUoIGNvbCApLmdldFByb3BlcnR5VmFsdWUoICctLXdwYmMtY29sLW1pbicgKSB8fCAnMCc7XHJcblx0XHRcdGNvbnN0IG4gPSBwYXJzZUZsb2F0KCB2ICk7XHJcblx0XHRcdHJldHVybiBOdW1iZXIuaXNGaW5pdGUoIG4gKSA/IE1hdGgubWF4KCAwLCBuICkgOiAwO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcGFyYW0ge01vdXNlRXZlbnR9IGUgKi9cclxuXHRcdGhhbmRsZV9yZXNpemUoZSkge1xyXG5cdFx0XHRjb25zdCBiID0gdGhpcy5idWlsZGVyO1xyXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdGlmICggZS5idXR0b24gIT09IDAgKSByZXR1cm47XHJcblxyXG5cdFx0XHRjb25zdCByZXNpemVyICAgPSBlLmN1cnJlbnRUYXJnZXQ7XHJcblx0XHRcdGNvbnN0IHJvd19lbCAgICA9IHJlc2l6ZXIucGFyZW50RWxlbWVudDtcclxuXHRcdFx0Y29uc3QgY29scyAgICAgID0gQXJyYXkuZnJvbSggcm93X2VsLnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2NvbHVtbicgKSApO1xyXG5cdFx0XHRjb25zdCBsZWZ0X2NvbCAgPSByZXNpemVyPy5wcmV2aW91c0VsZW1lbnRTaWJsaW5nO1xyXG5cdFx0XHRjb25zdCByaWdodF9jb2wgPSByZXNpemVyPy5uZXh0RWxlbWVudFNpYmxpbmc7XHJcblx0XHRcdGlmICggIWxlZnRfY29sIHx8ICFyaWdodF9jb2wgfHwgIWxlZnRfY29sLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19jb2x1bW4nICkgfHwgIXJpZ2h0X2NvbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fY29sdW1uJyApICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgbGVmdF9pbmRleCAgPSBjb2xzLmluZGV4T2YoIGxlZnRfY29sICk7XHJcblx0XHRcdGNvbnN0IHJpZ2h0X2luZGV4ID0gY29scy5pbmRleE9mKCByaWdodF9jb2wgKTtcclxuXHRcdFx0aWYgKCBsZWZ0X2luZGV4ID09PSAtMSB8fCByaWdodF9pbmRleCAhPT0gbGVmdF9pbmRleCArIDEgKSByZXR1cm47XHJcblxyXG5cdFx0XHRjb25zdCBzdGFydF94ICAgICAgICA9IGUuY2xpZW50WDtcclxuXHRcdFx0Y29uc3QgbGVmdF9zdGFydF9weCAgPSBsZWZ0X2NvbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS53aWR0aDtcclxuXHRcdFx0Y29uc3QgcmlnaHRfc3RhcnRfcHggPSByaWdodF9jb2wuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkud2lkdGg7XHJcblx0XHRcdGNvbnN0IHBhaXJfcHggICAgICAgID0gTWF0aC5tYXgoIDAsIGxlZnRfc3RhcnRfcHggKyByaWdodF9zdGFydF9weCApO1xyXG5cclxuXHRcdFx0Y29uc3QgZ3AgICAgICAgICA9IGIuY29sX2dhcF9wZXJjZW50O1xyXG5cdFx0XHRjb25zdCBjb21wdXRlZCAgID0gYi5sYXlvdXQuY29tcHV0ZV9lZmZlY3RpdmVfYmFzZXNfZnJvbV9yb3coIHJvd19lbCwgZ3AgKTtcclxuXHRcdFx0Y29uc3QgYXZhaWxhYmxlICA9IGNvbXB1dGVkLmF2YWlsYWJsZTsgICAgICAgICAgICAgICAgIC8vICUgb2YgdGhlIOKAnGZ1bGwgMTAw4oCdIGFmdGVyIGdhcHNcclxuXHRcdFx0Y29uc3QgYmFzZXMgICAgICA9IGNvbXB1dGVkLmJhc2VzLnNsaWNlKCAwICk7ICAgICAgICAgICAgLy8gY3VycmVudCBlZmZlY3RpdmUgJVxyXG5cdFx0XHRjb25zdCBwYWlyX2F2YWlsID0gYmFzZXNbbGVmdF9pbmRleF0gKyBiYXNlc1tyaWdodF9pbmRleF07XHJcblxyXG5cdFx0XHQvLyBCYWlsIGlmIHdlIGNhbuKAmXQgY29tcHV0ZSBzYW5lIGRlbHRhcy5cclxuXHRcdFx0aWYgKCFwYWlyX3B4IHx8ICFOdW1iZXIuaXNGaW5pdGUocGFpcl9hdmFpbCkgfHwgcGFpcl9hdmFpbCA8PSAwKSByZXR1cm47XHJcblxyXG5cdFx0XHQvLyAtLS0gTUlOIENMQU1QUyAocGl4ZWxzKSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdGNvbnN0IHBjdFRvUHggICAgICAgPSAocGN0KSA9PiAocGFpcl9weCAqIChwY3QgLyBwYWlyX2F2YWlsKSk7IC8vIHBhaXItbG9jYWwgcGVyY2VudCAtPiBweFxyXG5cdFx0XHRjb25zdCBnZW5lcmljTWluUGN0ID0gTWF0aC5taW4oIDAuMSwgYXZhaWxhYmxlICk7ICAgICAgICAgICAgICAgICAgLy8gb3JpZ2luYWwgMC4xJSBmbG9vciAoaW4g4oCcYXZhaWxhYmxlICXigJ0gc3BhY2UpXHJcblx0XHRcdGNvbnN0IGdlbmVyaWNNaW5QeCAgPSBwY3RUb1B4KCBnZW5lcmljTWluUGN0ICk7XHJcblxyXG5cdFx0XHRjb25zdCBsZWZ0TWluUHggID0gTWF0aC5tYXgoIHRoaXMuX2dldF9jb2xfbWluX3B4KCBsZWZ0X2NvbCApLCBnZW5lcmljTWluUHggKTtcclxuXHRcdFx0Y29uc3QgcmlnaHRNaW5QeCA9IE1hdGgubWF4KCB0aGlzLl9nZXRfY29sX21pbl9weCggcmlnaHRfY29sICksIGdlbmVyaWNNaW5QeCApO1xyXG5cclxuXHRcdFx0Ly8gZnJlZXplIHRleHQgc2VsZWN0aW9uICsgY3Vyc29yXHJcblx0XHRcdGNvbnN0IHByZXZfdXNlcl9zZWxlY3QgICAgICAgICA9IGRvY3VtZW50LmJvZHkuc3R5bGUudXNlclNlbGVjdDtcclxuXHRcdFx0ZG9jdW1lbnQuYm9keS5zdHlsZS51c2VyU2VsZWN0ID0gJ25vbmUnO1xyXG5cdFx0XHRyb3dfZWwuc3R5bGUuY3Vyc29yICAgICAgICAgICAgPSAnY29sLXJlc2l6ZSc7XHJcblxyXG5cdFx0XHRjb25zdCBvbl9tb3VzZV9tb3ZlID0gKGV2KSA9PiB7XHJcblx0XHRcdFx0aWYgKCAhcGFpcl9weCApIHJldHVybjtcclxuXHJcblx0XHRcdFx0Ly8gd29yayBpbiBwaXhlbHMsIGNsYW1wIGJ5IGVhY2ggc2lkZeKAmXMgbWluXHJcblx0XHRcdFx0Y29uc3QgZGVsdGFfcHggICA9IGV2LmNsaWVudFggLSBzdGFydF94O1xyXG5cdFx0XHRcdGxldCBuZXdMZWZ0UHggICAgPSBsZWZ0X3N0YXJ0X3B4ICsgZGVsdGFfcHg7XHJcblx0XHRcdFx0bmV3TGVmdFB4ICAgICAgICA9IE1hdGgubWF4KCBsZWZ0TWluUHgsIE1hdGgubWluKCBwYWlyX3B4IC0gcmlnaHRNaW5QeCwgbmV3TGVmdFB4ICkgKTtcclxuXHRcdFx0XHRjb25zdCBuZXdSaWdodFB4ID0gcGFpcl9weCAtIG5ld0xlZnRQeDtcclxuXHJcblx0XHRcdFx0Ly8gdHJhbnNsYXRlIGJhY2sgdG8gcGFpci1sb2NhbCBwZXJjZW50YWdlc1xyXG5cdFx0XHRcdGNvbnN0IG5ld0xlZnRQY3QgICAgICA9IChuZXdMZWZ0UHggLyBwYWlyX3B4KSAqIHBhaXJfYXZhaWw7XHJcblx0XHRcdFx0Y29uc3QgbmV3QmFzZXMgICAgICAgID0gYmFzZXMuc2xpY2UoIDAgKTtcclxuXHRcdFx0XHRuZXdCYXNlc1tsZWZ0X2luZGV4XSAgPSBuZXdMZWZ0UGN0O1xyXG5cdFx0XHRcdG5ld0Jhc2VzW3JpZ2h0X2luZGV4XSA9IHBhaXJfYXZhaWwgLSBuZXdMZWZ0UGN0O1xyXG5cclxuXHRcdFx0XHRiLmxheW91dC5hcHBseV9iYXNlc190b19yb3coIHJvd19lbCwgbmV3QmFzZXMgKTtcclxuXHRcdFx0fTtcclxuXHJcblx0XHRcdGNvbnN0IG9uX21vdXNlX3VwID0gKCkgPT4ge1xyXG5cdFx0XHRcdGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoICdtb3VzZW1vdmUnLCBvbl9tb3VzZV9tb3ZlICk7XHJcblx0XHRcdFx0ZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ21vdXNldXAnLCBvbl9tb3VzZV91cCApO1xyXG5cdFx0XHRcdHdpbmRvdy5yZW1vdmVFdmVudExpc3RlbmVyKCAnbW91c2V1cCcsIG9uX21vdXNlX3VwICk7XHJcblx0XHRcdFx0ZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ21vdXNlbGVhdmUnLCBvbl9tb3VzZV91cCApO1xyXG5cdFx0XHRcdGRvY3VtZW50LmJvZHkuc3R5bGUudXNlclNlbGVjdCA9IHByZXZfdXNlcl9zZWxlY3QgfHwgJyc7XHJcblx0XHRcdFx0cm93X2VsLnN0eWxlLmN1cnNvciAgICAgICAgICAgID0gJyc7XHJcblxyXG5cdFx0XHRcdC8vIG5vcm1hbGl6ZSB0byB0aGUgcm934oCZcyBhdmFpbGFibGUgJSBhZ2FpblxyXG5cdFx0XHRcdGNvbnN0IG5vcm1hbGl6ZWQgPSBiLmxheW91dC5jb21wdXRlX2VmZmVjdGl2ZV9iYXNlc19mcm9tX3Jvdyggcm93X2VsLCBncCApO1xyXG5cdFx0XHRcdGIubGF5b3V0LmFwcGx5X2Jhc2VzX3RvX3Jvdyggcm93X2VsLCBub3JtYWxpemVkLmJhc2VzICk7XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnbW91c2Vtb3ZlJywgb25fbW91c2VfbW92ZSApO1xyXG5cdFx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnbW91c2V1cCcsIG9uX21vdXNlX3VwICk7XHJcblx0XHRcdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCAnbW91c2V1cCcsIG9uX21vdXNlX3VwICk7XHJcblx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICdtb3VzZWxlYXZlJywgb25fbW91c2VfdXAgKTtcclxuXHRcdH1cclxuXHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogUGFnZSBhbmQgc2VjdGlvbiBjcmVhdGlvbiwgcmVidWlsZGluZywgYW5kIG5lc3RlZCBTb3J0YWJsZSBzZXR1cC5cclxuXHQgKi9cclxuXHRVSS5XUEJDX0JGQl9QYWdlc19TZWN0aW9ucyA9IGNsYXNzIGV4dGVuZHMgVUkuV1BCQ19CRkJfTW9kdWxlIHtcclxuXHJcblx0XHRpbml0KCkge1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuYWRkX3BhZ2UgICAgICAgICAgICAgICAgICA9IChvcHRzKSA9PiB0aGlzLmFkZF9wYWdlKCBvcHRzICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlci5hZGRfc2VjdGlvbiAgICAgICAgICAgICAgID0gKGNvbnRhaW5lciwgY29scykgPT4gdGhpcy5hZGRfc2VjdGlvbiggY29udGFpbmVyLCBjb2xzICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlci5yZWJ1aWxkX3NlY3Rpb24gICAgICAgICAgID0gKHNlY3Rpb25fZGF0YSwgY29udGFpbmVyKSA9PiB0aGlzLnJlYnVpbGRfc2VjdGlvbiggc2VjdGlvbl9kYXRhLCBjb250YWluZXIgKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyLmluaXRfYWxsX25lc3RlZF9zb3J0YWJsZXMgPSAoZWwpID0+IHRoaXMuaW5pdF9hbGxfbmVzdGVkX3NvcnRhYmxlcyggZWwgKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyLmluaXRfc2VjdGlvbl9zb3J0YWJsZSAgICAgPSAoZWwpID0+IHRoaXMuaW5pdF9zZWN0aW9uX3NvcnRhYmxlKCBlbCApO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIucGFnZXNfc2VjdGlvbnMgICAgICAgICAgICA9IHRoaXM7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHaXZlIGV2ZXJ5IGZpZWxkL3NlY3Rpb24gaW4gYSBjbG9uZWQgc3VidHJlZSBhIGZyZXNoIGRhdGEtdWlkIHNvXHJcblx0XHQgKiB1bmlxdWVuZXNzIGNoZWNrcyBkb24ndCBleGNsdWRlIHRoZWlyIG9yaWdpbmFscy5cclxuXHRcdCAqL1xyXG5cdFx0X3JldGFnX3VpZHNfaW5fc3VidHJlZShyb290KSB7XHJcblx0XHRcdGNvbnN0IGIgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdGlmICggIXJvb3QgKSByZXR1cm47XHJcblx0XHRcdGNvbnN0IG5vZGVzID0gW107XHJcblx0XHRcdGlmICggcm9vdC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgfHwgcm9vdC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX2ZpZWxkJyApICkge1xyXG5cdFx0XHRcdG5vZGVzLnB1c2goIHJvb3QgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRub2Rlcy5wdXNoKCAuLi5yb290LnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3NlY3Rpb24sIC53cGJjX2JmYl9fZmllbGQnICkgKTtcclxuXHRcdFx0bm9kZXMuZm9yRWFjaCggKGVsKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgcHJlZml4ICAgPSBlbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fc2VjdGlvbicgKSA/ICdzJyA6ICdmJztcclxuXHRcdFx0XHRlbC5kYXRhc2V0LnVpZCA9IGAke3ByZWZpeH0tJHsrK2IuX3VpZF9jb3VudGVyfS0ke0RhdGUubm93KCl9LSR7TWF0aC5yYW5kb20oKS50b1N0cmluZyggMzYgKS5zbGljZSggMiwgNyApfWA7XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEJ1bXAgXCJmb29cIiwgXCJmb28tMlwiLCBcImZvby0zXCIsIC4uLlxyXG5cdFx0ICovXHJcblx0XHRfbWFrZV91bmlxdWUoYmFzZSwgdGFrZW4pIHtcclxuXHRcdFx0Y29uc3QgcyA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemU7XHJcblx0XHRcdGxldCB2ICAgPSBTdHJpbmcoIGJhc2UgfHwgJycgKTtcclxuXHRcdFx0aWYgKCAhdiApIHYgPSAnZmllbGQnO1xyXG5cdFx0XHRjb25zdCBtICA9IHYubWF0Y2goIC8tKFxcZCspJC8gKTtcclxuXHRcdFx0bGV0IG4gICAgPSBtID8gKHBhcnNlSW50KCBtWzFdLCAxMCApIHx8IDEpIDogMTtcclxuXHRcdFx0bGV0IHN0ZW0gPSBtID8gdi5yZXBsYWNlKCAvLVxcZCskLywgJycgKSA6IHY7XHJcblx0XHRcdHdoaWxlICggdGFrZW4uaGFzKCB2ICkgKSB7XHJcblx0XHRcdFx0biA9IE1hdGgubWF4KCAyLCBuICsgMSApO1xyXG5cdFx0XHRcdHYgPSBgJHtzdGVtfS0ke259YDtcclxuXHRcdFx0fVxyXG5cdFx0XHR0YWtlbi5hZGQoIHYgKTtcclxuXHRcdFx0cmV0dXJuIHY7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTdHJpY3QsIG9uZS1wYXNzIGRlLWR1cGxpY2F0aW9uIGZvciBhIG5ld2x5LWluc2VydGVkIHN1YnRyZWUuXHJcblx0XHQgKiAtIEVuc3VyZXMgdW5pcXVlIGRhdGEtaWQgKGludGVybmFsKSwgZGF0YS1uYW1lIChmaWVsZHMpLCBkYXRhLWh0bWxfaWQgKHB1YmxpYylcclxuXHRcdCAqIC0gQWxzbyB1cGRhdGVzIERPTTogPHNlY3Rpb24gaWQ+LCA8aW5wdXQgaWQ+LCA8bGFiZWwgZm9yPiwgYW5kIGlucHV0W25hbWVdLlxyXG5cdFx0ICovXHJcblx0XHRfZGVkdXBlX3N1YnRyZWVfc3RyaWN0KHJvb3QpIHtcclxuXHRcdFx0Y29uc3QgYiA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0Y29uc3QgcyA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemU7XHJcblx0XHRcdGlmICggIXJvb3QgfHwgIWI/LnBhZ2VzX2NvbnRhaW5lciApIHJldHVybjtcclxuXHJcblx0XHRcdC8vIDEpIEJ1aWxkIFwidGFrZW5cIiBzZXRzIGZyb20gb3V0c2lkZSB0aGUgc3VidHJlZS5cclxuXHRcdFx0Y29uc3QgdGFrZW5EYXRhSWQgICA9IG5ldyBTZXQoKTtcclxuXHRcdFx0Y29uc3QgdGFrZW5EYXRhTmFtZSA9IG5ldyBTZXQoKTtcclxuXHRcdFx0Y29uc3QgdGFrZW5IdG1sSWQgICA9IG5ldyBTZXQoKTtcclxuXHRcdFx0Y29uc3QgdGFrZW5Eb21JZCAgICA9IG5ldyBTZXQoKTtcclxuXHJcblx0XHRcdC8vIEFsbCBmaWVsZHMvc2VjdGlvbnMgb3V0c2lkZSByb290XHJcblx0XHRcdGIucGFnZXNfY29udGFpbmVyLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX2ZpZWxkLCAud3BiY19iZmJfX3NlY3Rpb24nICkuZm9yRWFjaCggZWwgPT4ge1xyXG5cdFx0XHRcdGlmICggcm9vdC5jb250YWlucyggZWwgKSApIHJldHVybjtcclxuXHRcdFx0XHRjb25zdCBkaWQgID0gZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1pZCcgKTtcclxuXHRcdFx0XHRjb25zdCBkbmFtID0gZWwuZ2V0QXR0cmlidXRlKCAnZGF0YS1uYW1lJyApO1xyXG5cdFx0XHRcdGNvbnN0IGhpZCAgPSBlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWh0bWxfaWQnICk7XHJcblx0XHRcdFx0aWYgKCBkaWQgKSB0YWtlbkRhdGFJZC5hZGQoIGRpZCApO1xyXG5cdFx0XHRcdGlmICggZG5hbSApIHRha2VuRGF0YU5hbWUuYWRkKCBkbmFtICk7XHJcblx0XHRcdFx0aWYgKCBoaWQgKSB0YWtlbkh0bWxJZC5hZGQoIGhpZCApO1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHQvLyBBbGwgRE9NIGlkcyBvdXRzaWRlIHJvb3QgKGxhYmVscywgaW5wdXRzLCBhbnl0aGluZylcclxuXHRcdFx0ZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCggJ1tpZF0nICkuZm9yRWFjaCggZWwgPT4ge1xyXG5cdFx0XHRcdGlmICggcm9vdC5jb250YWlucyggZWwgKSApIHJldHVybjtcclxuXHRcdFx0XHRpZiAoIGVsLmlkICkgdGFrZW5Eb21JZC5hZGQoIGVsLmlkICk7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdGNvbnN0IG5vZGVzID0gW107XHJcblx0XHRcdGlmICggcm9vdC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgfHwgcm9vdC5jbGFzc0xpc3Q/LmNvbnRhaW5zKCAnd3BiY19iZmJfX2ZpZWxkJyApICkge1xyXG5cdFx0XHRcdG5vZGVzLnB1c2goIHJvb3QgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRub2Rlcy5wdXNoKCAuLi5yb290LnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3NlY3Rpb24sIC53cGJjX2JmYl9fZmllbGQnICkgKTtcclxuXHJcblx0XHRcdC8vIDIpIFdhbGsgdGhlIHN1YnRyZWUgYW5kIGZpeCBjb2xsaXNpb25zIGRldGVybWluaXN0aWNhbGx5LlxyXG5cdFx0XHRub2Rlcy5mb3JFYWNoKCBlbCA9PiB7XHJcblx0XHRcdFx0Y29uc3QgaXNGaWVsZCAgID0gZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX2ZpZWxkJyApO1xyXG5cdFx0XHRcdGNvbnN0IGlzU2VjdGlvbiA9IGVsLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19zZWN0aW9uJyApO1xyXG5cclxuXHRcdFx0XHQvLyBJTlRFUk5BTCBkYXRhLWlkXHJcblx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0Y29uc3QgcmF3ICA9IGVsLmdldEF0dHJpYnV0ZSggJ2RhdGEtaWQnICkgfHwgJyc7XHJcblx0XHRcdFx0XHRjb25zdCBiYXNlID0gcy5zYW5pdGl6ZV9odG1sX2lkKCByYXcgKSB8fCAoaXNTZWN0aW9uID8gJ3NlY3Rpb24nIDogJ2ZpZWxkJyk7XHJcblx0XHRcdFx0XHRjb25zdCB1bmlxID0gdGhpcy5fbWFrZV91bmlxdWUoIGJhc2UsIHRha2VuRGF0YUlkICk7XHJcblx0XHRcdFx0XHRpZiAoIHVuaXEgIT09IHJhdyApIGVsLnNldEF0dHJpYnV0ZSggJ2RhdGEtaWQnLCB1bmlxICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyBIVE1MIG5hbWUgKGZpZWxkcyBvbmx5KVxyXG5cdFx0XHRcdGlmICggaXNGaWVsZCApIHtcclxuXHRcdFx0XHRcdGNvbnN0IHJhdyA9IGVsLmdldEF0dHJpYnV0ZSggJ2RhdGEtbmFtZScgKSB8fCAnJztcclxuXHRcdFx0XHRcdGlmICggcmF3ICkge1xyXG5cdFx0XHRcdFx0XHRjb25zdCBiYXNlID0gcy5zYW5pdGl6ZV9odG1sX25hbWUoIHJhdyApO1xyXG5cdFx0XHRcdFx0XHRjb25zdCB1bmlxID0gdGhpcy5fbWFrZV91bmlxdWUoIGJhc2UsIHRha2VuRGF0YU5hbWUgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCB1bmlxICE9PSByYXcgKSB7XHJcblx0XHRcdFx0XHRcdFx0ZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1uYW1lJywgdW5pcSApO1xyXG5cdFx0XHRcdFx0XHRcdC8vIFVwZGF0ZSBpbm5lciBjb250cm9sIGltbWVkaWF0ZWx5XHJcblx0XHRcdFx0XHRcdFx0Y29uc3QgaW5wdXQgPSBlbC5xdWVyeVNlbGVjdG9yKCAnaW5wdXQsIHRleHRhcmVhLCBzZWxlY3QnICk7XHJcblx0XHRcdFx0XHRcdFx0aWYgKCBpbnB1dCApIGlucHV0LnNldEF0dHJpYnV0ZSggJ25hbWUnLCB1bmlxICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIFB1YmxpYyBIVE1MIGlkIChmaWVsZHMgKyBzZWN0aW9ucylcclxuXHRcdFx0XHR7XHJcblx0XHRcdFx0XHRjb25zdCByYXcgPSBlbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWh0bWxfaWQnICkgfHwgJyc7XHJcblx0XHRcdFx0XHRpZiAoIHJhdyApIHtcclxuXHRcdFx0XHRcdFx0Y29uc3QgYmFzZSAgICAgICAgICA9IHMuc2FuaXRpemVfaHRtbF9pZCggcmF3ICk7XHJcblx0XHRcdFx0XHRcdC8vIFJlc2VydmUgYWdhaW5zdCBCT1RIIGtub3duIGRhdGEtaHRtbF9pZCBhbmQgcmVhbCBET00gaWRzLlxyXG5cdFx0XHRcdFx0XHRjb25zdCBjb21iaW5lZFRha2VuID0gbmV3IFNldCggWyAuLi50YWtlbkh0bWxJZCwgLi4udGFrZW5Eb21JZCBdICk7XHJcblx0XHRcdFx0XHRcdGxldCBjYW5kaWRhdGUgICAgICAgPSB0aGlzLl9tYWtlX3VuaXF1ZSggYmFzZSwgY29tYmluZWRUYWtlbiApO1xyXG5cdFx0XHRcdFx0XHQvLyBSZWNvcmQgaW50byB0aGUgcmVhbCBzZXRzIHNvIGZ1dHVyZSBjaGVja3Mgc2VlIHRoZSByZXNlcnZhdGlvbi5cclxuXHRcdFx0XHRcdFx0dGFrZW5IdG1sSWQuYWRkKCBjYW5kaWRhdGUgKTtcclxuXHRcdFx0XHRcdFx0dGFrZW5Eb21JZC5hZGQoIGNhbmRpZGF0ZSApO1xyXG5cclxuXHRcdFx0XHRcdFx0aWYgKCBjYW5kaWRhdGUgIT09IHJhdyApIGVsLnNldEF0dHJpYnV0ZSggJ2RhdGEtaHRtbF9pZCcsIGNhbmRpZGF0ZSApO1xyXG5cclxuXHRcdFx0XHRcdFx0Ly8gUmVmbGVjdCB0byBET00gaW1tZWRpYXRlbHlcclxuXHRcdFx0XHRcdFx0aWYgKCBpc1NlY3Rpb24gKSB7XHJcblx0XHRcdFx0XHRcdFx0ZWwuaWQgPSBjYW5kaWRhdGUgfHwgJyc7XHJcblx0XHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdFx0Y29uc3QgaW5wdXQgPSBlbC5xdWVyeVNlbGVjdG9yKCAnaW5wdXQsIHRleHRhcmVhLCBzZWxlY3QnICk7XHJcblx0XHRcdFx0XHRcdFx0Y29uc3QgbGFiZWwgPSBlbC5xdWVyeVNlbGVjdG9yKCAnbGFiZWwud3BiY19iZmJfX2ZpZWxkLWxhYmVsJyApO1xyXG5cdFx0XHRcdFx0XHRcdGlmICggaW5wdXQgKSBpbnB1dC5pZCA9IGNhbmRpZGF0ZSB8fCAnJztcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGxhYmVsICkgbGFiZWwuaHRtbEZvciA9IGNhbmRpZGF0ZSB8fCAnJztcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSBlbHNlIGlmICggaXNTZWN0aW9uICkge1xyXG5cdFx0XHRcdFx0XHQvLyBFbnN1cmUgbm8gc3RhbGUgRE9NIGlkIGlmIGRhdGEtaHRtbF9pZCB3YXMgY2xlYXJlZFxyXG5cdFx0XHRcdFx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICdpZCcgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHRfbWFrZV9hZGRfY29sdW1uc19jb250cm9sKHBhZ2VfZWwsIHNlY3Rpb25fY29udGFpbmVyLCBpbnNlcnRfcG9zID0gJ2JvdHRvbScpIHtcclxuXHJcblx0XHRcdC8vIEFjY2VwdCBpbnNlcnRfcG9zICgndG9wJ3wnYm90dG9tJyksIGRlZmF1bHQgJ2JvdHRvbScuXHJcblxyXG5cdFx0XHRjb25zdCB0cGwgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwYmNfYmZiX19hZGRfY29sdW1uc190ZW1wbGF0ZScgKTtcclxuXHRcdFx0aWYgKCAhdHBsICkge1xyXG5cdFx0XHRcdHJldHVybiBudWxsO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBDbG9uZSAqY29udGVudHMqIChub3QgdGhlIGlkKSwgdW5oaWRlLCBhbmQgYWRkIGEgcGFnZS1zY29wZWQgY2xhc3MuXHJcblx0XHRcdGNvbnN0IHNyYyA9ICh0cGwuY29udGVudCAmJiB0cGwuY29udGVudC5maXJzdEVsZW1lbnRDaGlsZCkgPyB0cGwuY29udGVudC5maXJzdEVsZW1lbnRDaGlsZCA6IHRwbC5maXJzdEVsZW1lbnRDaGlsZDtcclxuXHRcdFx0aWYgKCAhc3JjICkge1xyXG5cdFx0XHRcdHJldHVybiBudWxsO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRjb25zdCBjbG9uZSA9IHNyYy5jbG9uZU5vZGUoIHRydWUgKTtcclxuXHRcdFx0Y2xvbmUucmVtb3ZlQXR0cmlidXRlKCAnaGlkZGVuJyApO1xyXG5cdFx0XHRpZiAoIGNsb25lLmlkICkge1xyXG5cdFx0XHRcdGNsb25lLnJlbW92ZUF0dHJpYnV0ZSggJ2lkJyApO1xyXG5cdFx0XHR9XHJcblx0XHRcdGNsb25lLnF1ZXJ5U2VsZWN0b3JBbGwoICdbaWRdJyApLmZvckVhY2goIG4gPT4gbi5yZW1vdmVBdHRyaWJ1dGUoICdpZCcgKSApO1xyXG5cclxuXHRcdFx0Ly8gTWFyayB3aGVyZSB0aGlzIGNvbnRyb2wgaW5zZXJ0cyBzZWN0aW9ucy5cclxuXHRcdFx0Y2xvbmUuZGF0YXNldC5pbnNlcnQgPSBpbnNlcnRfcG9zOyAvLyAndG9wJyB8ICdib3R0b20nXHJcblxyXG5cdFx0XHQvLyAvLyBPcHRpb25hbCBVSSBoaW50IGZvciB1c2VycyAoa2VlcHMgZXhpc3RpbmcgbWFya3VwIGludGFjdCkuXHJcblx0XHRcdC8vIGNvbnN0IGhpbnQgPSBjbG9uZS5xdWVyeVNlbGVjdG9yKCAnLm5hdi10YWItdGV4dCAuc2VsZWN0ZWRfdmFsdWUnICk7XHJcblx0XHRcdC8vIGlmICggaGludCApIHtcclxuXHRcdFx0Ly8gXHRoaW50LnRleHRDb250ZW50ID0gKGluc2VydF9wb3MgPT09ICd0b3AnKSA/ICcgKGFkZCBhdCB0b3ApJyA6ICcgKGFkZCBhdCBib3R0b20pJztcclxuXHRcdFx0Ly8gfVxyXG5cclxuXHRcdFx0Ly8gQ2xpY2sgb24gb3B0aW9ucyAtIGFkZCBzZWN0aW9uIHdpdGggTiBjb2x1bW5zLlxyXG5cdFx0XHRjbG9uZS5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCAoZSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IGEgPSBlLnRhcmdldC5jbG9zZXN0KCAnLnVsX2Ryb3Bkb3duX21lbnVfbGlfYWN0aW9uX2FkZF9zZWN0aW9ucycgKTtcclxuXHRcdFx0XHRpZiAoICFhICkge1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG5cdFx0XHRcdC8vIFJlYWQgTiBlaXRoZXIgZnJvbSBkYXRhLWNvbHMgb3IgZmFsbGJhY2sgdG8gcGFyc2luZyB0ZXh0IGxpa2UgXCIzIENvbHVtbnNcIi5cclxuXHRcdFx0XHRsZXQgY29scyA9IHBhcnNlSW50KCBhLmRhdGFzZXQuY29scyB8fCAoYS50ZXh0Q29udGVudC5tYXRjaCggL1xcYihcXGQrKVxccypDb2x1bW4vaSApPy5bMV0gPz8gJzEnKSwgMTAgKTtcclxuXHRcdFx0XHRjb2xzICAgICA9IE1hdGgubWF4KCAxLCBNYXRoLm1pbiggNCwgY29scyApICk7XHJcblxyXG5cdFx0XHRcdC8vIE5FVzogaG9ub3IgdGhlIGNvbnRyb2wncyBpbnNlcnRpb24gcG9zaXRpb25cclxuXHRcdFx0XHR0aGlzLmFkZF9zZWN0aW9uKCBzZWN0aW9uX2NvbnRhaW5lciwgY29scywgaW5zZXJ0X3BvcyApO1xyXG5cclxuXHRcdFx0XHQvLyBSZWZsZWN0IGxhc3QgY2hvaWNlICh1bmNoYW5nZWQpXHJcblx0XHRcdFx0Y29uc3QgdmFsID0gY2xvbmUucXVlcnlTZWxlY3RvciggJy5zZWxlY3RlZF92YWx1ZScgKTtcclxuXHRcdFx0XHRpZiAoIHZhbCApIHtcclxuXHRcdFx0XHRcdHZhbC50ZXh0Q29udGVudCA9IGAgKCR7Y29sc30pYDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdHJldHVybiBjbG9uZTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEBwYXJhbSB7e3Njcm9sbD86IGJvb2xlYW59fSBbb3B0cyA9IHt9XVxyXG5cdFx0ICogQHJldHVybnMge0hUTUxFbGVtZW50fVxyXG5cdFx0ICovXHJcblx0XHRhZGRfcGFnZSh7IHNjcm9sbCA9IHRydWUgfSA9IHt9KSB7XHJcblx0XHRcdGNvbnN0IGIgICAgICAgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdGNvbnN0IHBhZ2VfZWwgPSBDb3JlLldQQkNfRm9ybV9CdWlsZGVyX0hlbHBlci5jcmVhdGVfZWxlbWVudCggJ2RpdicsICd3cGJjX2JmYl9fcGFuZWwgd3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3ICB3cGJjX2JmYl9mb3JtIHdwYmNfY29udGFpbmVyIHdwYmNfZm9ybSB3cGJjX2NvbnRhaW5lcl9ib29raW5nX2Zvcm0nICk7XHJcblx0XHRcdHBhZ2VfZWwuc2V0QXR0cmlidXRlKCAnZGF0YS1wYWdlJywgKytiLnBhZ2VfY291bnRlciApO1xyXG5cclxuXHRcdFx0Ly8gXCJQYWdlIDEgfCBYXCIgLSBSZW5kZXIgcGFnZSBUaXRsZSB3aXRoIFJlbW92ZSBYIGJ1dHRvbi5cclxuXHRcdFx0Y29uc3QgY29udHJvbHNfaHRtbCA9IFVJLnJlbmRlcl93cF90ZW1wbGF0ZSggJ3dwYmMtYmZiLXRwbC1wYWdlLXJlbW92ZScsIHsgcGFnZV9udW1iZXI6IGIucGFnZV9jb3VudGVyIH0gKTtcclxuXHRcdFx0cGFnZV9lbC5pbm5lckhUTUwgICA9IGNvbnRyb2xzX2h0bWwgKyAnPGRpdiBjbGFzcz1cIndwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXIgd3BiY193aXphcmRfX2JvcmRlcl9jb250YWluZXJcIj48L2Rpdj4nO1xyXG5cclxuXHRcdFx0Yi5wYWdlc19jb250YWluZXIuYXBwZW5kQ2hpbGQoIHBhZ2VfZWwgKTtcclxuXHRcdFx0aWYgKCBzY3JvbGwgKSB7XHJcblx0XHRcdFx0cGFnZV9lbC5zY3JvbGxJbnRvVmlldyggeyBiZWhhdmlvcjogJ3Ntb290aCcsIGJsb2NrOiAnc3RhcnQnIH0gKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3Qgc2VjdGlvbl9jb250YWluZXIgICAgICAgICA9IHBhZ2VfZWwucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fZm9ybV9wcmV2aWV3X3NlY3Rpb25fY29udGFpbmVyJyApO1xyXG5cdFx0XHRjb25zdCBzZWN0aW9uX2NvdW50X29uX2FkZF9wYWdlID0gMjtcclxuXHRcdFx0dGhpcy5pbml0X3NlY3Rpb25fc29ydGFibGUoIHNlY3Rpb25fY29udGFpbmVyICk7XHJcblx0XHRcdHRoaXMuYWRkX3NlY3Rpb24oIHNlY3Rpb25fY29udGFpbmVyLCBzZWN0aW9uX2NvdW50X29uX2FkZF9wYWdlICk7XHJcblxyXG5cdFx0XHQvLyBEcm9wZG93biBjb250cm9sIGNsb25lZCBmcm9tIHRoZSBoaWRkZW4gdGVtcGxhdGUuXHJcblx0XHRcdGNvbnN0IGNvbnRyb2xzX2hvc3RfdG9wID0gcGFnZV9lbC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19jb250cm9scycgKTtcclxuXHRcdFx0Y29uc3QgY3RybF90b3AgICAgICAgICAgPSB0aGlzLl9tYWtlX2FkZF9jb2x1bW5zX2NvbnRyb2woIHBhZ2VfZWwsIHNlY3Rpb25fY29udGFpbmVyLCAndG9wJyApO1xyXG5cdFx0XHRpZiAoIGN0cmxfdG9wICkge1xyXG5cdFx0XHRcdGNvbnRyb2xzX2hvc3RfdG9wLmFwcGVuZENoaWxkKCBjdHJsX3RvcCApO1xyXG5cdFx0XHR9XHJcblx0XHRcdC8vIEJvdHRvbSBjb250cm9sIGJhciBhZnRlciB0aGUgc2VjdGlvbiBjb250YWluZXIuXHJcblx0XHRcdGNvbnN0IGNvbnRyb2xzX2hvc3RfYm90dG9tID0gQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuY3JlYXRlX2VsZW1lbnQoICdkaXYnLCAnd3BiY19iZmJfX2NvbnRyb2xzIHdwYmNfYmZiX19jb250cm9scy0tYm90dG9tJyApO1xyXG5cdFx0XHRzZWN0aW9uX2NvbnRhaW5lci5hZnRlciggY29udHJvbHNfaG9zdF9ib3R0b20gKTtcclxuXHRcdFx0Y29uc3QgY3RybF9ib3R0b20gPSB0aGlzLl9tYWtlX2FkZF9jb2x1bW5zX2NvbnRyb2woIHBhZ2VfZWwsIHNlY3Rpb25fY29udGFpbmVyLCAnYm90dG9tJyApO1xyXG5cdFx0XHRpZiAoIGN0cmxfYm90dG9tICkge1xyXG5cdFx0XHRcdGNvbnRyb2xzX2hvc3RfYm90dG9tLmFwcGVuZENoaWxkKCBjdHJsX2JvdHRvbSApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gcGFnZV9lbDtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IGNvbnRhaW5lclxyXG5cdFx0ICogQHBhcmFtIHtudW1iZXJ9ICAgICAgY29sc1xyXG5cdFx0ICogQHBhcmFtIHsndG9wJ3wnYm90dG9tJ30gW2luc2VydF9wb3M9J2JvdHRvbSddICAvLyBORVdcclxuXHRcdCAqL1xyXG5cdFx0YWRkX3NlY3Rpb24oY29udGFpbmVyLCBjb2xzLCBpbnNlcnRfcG9zID0gJ2JvdHRvbScpIHtcclxuXHRcdFx0Y29uc3QgYiA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0Y29scyAgICA9IE1hdGgubWF4KCAxLCBwYXJzZUludCggY29scywgMTAgKSB8fCAxICk7XHJcblxyXG5cdFx0XHRjb25zdCBzZWN0aW9uID0gQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuY3JlYXRlX2VsZW1lbnQoICdkaXYnLCAnd3BiY19iZmJfX3NlY3Rpb24nICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1pZCcsIGBzZWN0aW9uLSR7KytiLnNlY3Rpb25fY291bnRlcn0tJHtEYXRlLm5vdygpfWAgKTtcclxuXHRcdFx0c2VjdGlvbi5zZXRBdHRyaWJ1dGUoICdkYXRhLXVpZCcsIGBzLSR7KytiLl91aWRfY291bnRlcn0tJHtEYXRlLm5vdygpfS0ke01hdGgucmFuZG9tKCkudG9TdHJpbmcoIDM2ICkuc2xpY2UoIDIsIDcgKX1gICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS10eXBlJywgJ3NlY3Rpb24nICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1sYWJlbCcsICdTZWN0aW9uJyApO1xyXG5cdFx0XHRzZWN0aW9uLnNldEF0dHJpYnV0ZSggJ2RhdGEtY29sdW1ucycsIFN0cmluZyggY29scyApICk7XHJcblx0XHRcdC8vIERvIG5vdCBwZXJzaXN0IG9yIHNlZWQgcGVyLWNvbHVtbiBzdHlsZXMgYnkgZGVmYXVsdCAob3B0LWluIHZpYSBpbnNwZWN0b3IpLlxyXG5cclxuXHRcdFx0Y29uc3Qgcm93ID0gQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuY3JlYXRlX2VsZW1lbnQoICdkaXYnLCAnd3BiY19iZmJfX3JvdyB3cGJjX19yb3cnICk7XHJcblx0XHRcdGZvciAoIGxldCBpID0gMDsgaSA8IGNvbHM7IGkrKyApIHtcclxuXHRcdFx0XHRjb25zdCBjb2wgICAgICAgICAgID0gQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuY3JlYXRlX2VsZW1lbnQoICdkaXYnLCAnd3BiY19iZmJfX2NvbHVtbiB3cGJjX19maWVsZCcgKTtcclxuXHRcdFx0XHRjb2wuc3R5bGUuZmxleEJhc2lzID0gKDEwMCAvIGNvbHMpICsgJyUnO1xyXG5cdFx0XHRcdC8vIE5vIGRlZmF1bHQgQ1NTIHZhcnMgaGVyZTsgcmVhbCBjb2x1bW5zIHJlbWFpbiB1bmFmZmVjdGVkIHVudGlsIHVzZXIgYWN0aXZhdGVzIHN0eWxlcy5cclxuXHRcdFx0XHRiLmluaXRfc29ydGFibGU/LiggY29sICk7XHJcblx0XHRcdFx0cm93LmFwcGVuZENoaWxkKCBjb2wgKTtcclxuXHRcdFx0XHRpZiAoIGkgPCBjb2xzIC0gMSApIHtcclxuXHRcdFx0XHRcdGNvbnN0IHJlc2l6ZXIgPSBDb3JlLldQQkNfRm9ybV9CdWlsZGVyX0hlbHBlci5jcmVhdGVfZWxlbWVudCggJ2RpdicsICd3cGJjX2JmYl9fY29sdW1uLXJlc2l6ZXInICk7XHJcblx0XHRcdFx0XHRyZXNpemVyLmFkZEV2ZW50TGlzdGVuZXIoICdtb3VzZWRvd24nLCBiLmluaXRfcmVzaXplX2hhbmRsZXIgKTtcclxuXHRcdFx0XHRcdHJvdy5hcHBlbmRDaGlsZCggcmVzaXplciApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRzZWN0aW9uLmFwcGVuZENoaWxkKCByb3cgKTtcclxuXHRcdFx0Yi5sYXlvdXQuc2V0X2VxdWFsX2Jhc2VzKCByb3csIGIuY29sX2dhcF9wZXJjZW50ICk7XHJcblx0XHRcdGIuYWRkX292ZXJsYXlfdG9vbGJhciggc2VjdGlvbiApO1xyXG5cdFx0XHRzZWN0aW9uLnNldEF0dHJpYnV0ZSggJ3RhYmluZGV4JywgJzAnICk7XHJcblx0XHRcdHRoaXMuaW5pdF9hbGxfbmVzdGVkX3NvcnRhYmxlcyggc2VjdGlvbiApO1xyXG5cclxuXHRcdFx0Ly8gSW5zZXJ0aW9uIHBvbGljeTogdG9wIHwgYm90dG9tLlxyXG5cdFx0XHRpZiAoIGluc2VydF9wb3MgPT09ICd0b3AnICYmIGNvbnRhaW5lci5maXJzdEVsZW1lbnRDaGlsZCApIHtcclxuXHRcdFx0XHRjb250YWluZXIuaW5zZXJ0QmVmb3JlKCBzZWN0aW9uLCBjb250YWluZXIuZmlyc3RFbGVtZW50Q2hpbGQgKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRjb250YWluZXIuYXBwZW5kQ2hpbGQoIHNlY3Rpb24gKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IHNlY3Rpb25fZGF0YVxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gY29udGFpbmVyXHJcblx0XHQgKiBAcmV0dXJucyB7SFRNTEVsZW1lbnR9IFRoZSByZWJ1aWx0IHNlY3Rpb24gZWxlbWVudC5cclxuXHRcdCAqL1xyXG5cdFx0cmVidWlsZF9zZWN0aW9uKHNlY3Rpb25fZGF0YSwgY29udGFpbmVyKSB7XHJcblx0XHRcdGNvbnN0IGIgICAgICAgICA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0Y29uc3QgY29sc19kYXRhID0gQXJyYXkuaXNBcnJheSggc2VjdGlvbl9kYXRhPy5jb2x1bW5zICkgPyBzZWN0aW9uX2RhdGEuY29sdW1ucyA6IFtdO1xyXG5cdFx0XHR0aGlzLmFkZF9zZWN0aW9uKCBjb250YWluZXIsIGNvbHNfZGF0YS5sZW5ndGggfHwgMSApO1xyXG5cdFx0XHRjb25zdCBzZWN0aW9uID0gY29udGFpbmVyLmxhc3RFbGVtZW50Q2hpbGQ7XHJcblx0XHRcdGlmICggIXNlY3Rpb24uZGF0YXNldC51aWQgKSB7XHJcblx0XHRcdFx0c2VjdGlvbi5zZXRBdHRyaWJ1dGUoICdkYXRhLXVpZCcsIGBzLSR7KytiLl91aWRfY291bnRlcn0tJHtEYXRlLm5vdygpfS0ke01hdGgucmFuZG9tKCkudG9TdHJpbmcoIDM2ICkuc2xpY2UoIDIsIDcgKX1gICk7XHJcblx0XHRcdH1cclxuXHRcdFx0c2VjdGlvbi5zZXRBdHRyaWJ1dGUoICdkYXRhLWlkJywgc2VjdGlvbl9kYXRhPy5pZCB8fCBgc2VjdGlvbi0keysrYi5zZWN0aW9uX2NvdW50ZXJ9LSR7RGF0ZS5ub3coKX1gICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS10eXBlJywgJ3NlY3Rpb24nICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1sYWJlbCcsIHNlY3Rpb25fZGF0YT8ubGFiZWwgfHwgJ1NlY3Rpb24nICk7XHJcblx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1jb2x1bW5zJywgU3RyaW5nKCAoc2VjdGlvbl9kYXRhPy5jb2x1bW5zIHx8IFtdKS5sZW5ndGggfHwgMSApICk7XHJcblx0XHRcdC8vIFBlcnNpc3RlZCBhdHRyaWJ1dGVzXHJcblx0XHRcdGlmICggc2VjdGlvbl9kYXRhPy5odG1sX2lkICkge1xyXG5cdFx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1odG1sX2lkJywgU3RyaW5nKCBzZWN0aW9uX2RhdGEuaHRtbF9pZCApICk7XHJcblx0XHRcdFx0Ly8gZ2l2ZSB0aGUgY29udGFpbmVyIGEgcmVhbCBpZCBzbyBhbmNob3JzL0NTUyBjYW4gdGFyZ2V0IGl0XHJcblx0XHRcdFx0c2VjdGlvbi5pZCA9IFN0cmluZyggc2VjdGlvbl9kYXRhLmh0bWxfaWQgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gTkVXOiByZXN0b3JlIHBlcnNpc3RlZCBwZXItY29sdW1uIHN0eWxlcyAocmF3IEpTT04gc3RyaW5nKS5cclxuXHRcdFx0aWYgKCBzZWN0aW9uX2RhdGE/LmNvbF9zdHlsZXMgIT0gbnVsbCApIHtcclxuXHRcdFx0XHRjb25zdCBqc29uID0gU3RyaW5nKCBzZWN0aW9uX2RhdGEuY29sX3N0eWxlcyApO1xyXG5cdFx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1jb2xfc3R5bGVzJywganNvbiApO1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRzZWN0aW9uLmRhdGFzZXQuY29sX3N0eWxlcyA9IGpzb247XHJcblx0XHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHQvLyAoTm8gcmVuZGVyX3ByZXZpZXcoKSBjYWxsIGhlcmUgb24gcHVycG9zZTogc2VjdGlvbnPigJkgYnVpbGRlciBET00gdXNlcyAud3BiY19iZmJfX3Jvdy8ud3BiY19iZmJfX2NvbHVtbi4pXHJcblxyXG5cclxuXHRcdFx0aWYgKCBzZWN0aW9uX2RhdGE/LmNzc2NsYXNzICkge1xyXG5cdFx0XHRcdHNlY3Rpb24uc2V0QXR0cmlidXRlKCAnZGF0YS1jc3NjbGFzcycsIFN0cmluZyggc2VjdGlvbl9kYXRhLmNzc2NsYXNzICkgKTtcclxuXHRcdFx0XHQvLyBrZWVwIGNvcmUgY2xhc3NlcywgdGhlbiBhZGQgY3VzdG9tIGNsYXNzKGVzKVxyXG5cdFx0XHRcdFN0cmluZyggc2VjdGlvbl9kYXRhLmNzc2NsYXNzICkuc3BsaXQoIC9cXHMrLyApLmZpbHRlciggQm9vbGVhbiApLmZvckVhY2goIGNscyA9PiBzZWN0aW9uLmNsYXNzTGlzdC5hZGQoIGNscyApICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGNvbnN0IHJvdyA9IHNlY3Rpb24ucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fcm93JyApO1xyXG5cdFx0XHQvLyBEZWxlZ2F0ZSBwYXJzaW5nICsgYWN0aXZhdGlvbiArIGFwcGxpY2F0aW9uIHRvIHRoZSBDb2x1bW4gU3R5bGVzIHNlcnZpY2UuXHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0Y29uc3QganNvbiA9IHNlY3Rpb24uZ2V0QXR0cmlidXRlKCAnZGF0YS1jb2xfc3R5bGVzJyApXHJcblx0XHRcdFx0XHR8fCAoc2VjdGlvbi5kYXRhc2V0ID8gKHNlY3Rpb24uZGF0YXNldC5jb2xfc3R5bGVzIHx8ICcnKSA6ICcnKTtcclxuXHRcdFx0XHRjb25zdCBhcnIgID0gVUkuV1BCQ19CRkJfQ29sdW1uX1N0eWxlcy5wYXJzZV9jb2xfc3R5bGVzKCBqc29uICk7XHJcblx0XHRcdFx0VUkuV1BCQ19CRkJfQ29sdW1uX1N0eWxlcy5hcHBseSggc2VjdGlvbiwgYXJyICk7XHJcblx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29sc19kYXRhLmZvckVhY2goIChjb2xfZGF0YSwgaW5kZXgpID0+IHtcclxuXHRcdFx0XHRjb25zdCBjb2x1bW5zX29ubHkgID0gcm93LnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2NvbHVtbicgKTtcclxuXHRcdFx0XHRjb25zdCBjb2wgICAgICAgICAgID0gY29sdW1uc19vbmx5W2luZGV4XTtcclxuXHRcdFx0XHRjb2wuc3R5bGUuZmxleEJhc2lzID0gY29sX2RhdGEud2lkdGggfHwgJzEwMCUnO1xyXG5cdFx0XHRcdChjb2xfZGF0YS5pdGVtcyB8fCBbXSkuZm9yRWFjaCggKGl0ZW0pID0+IHtcclxuXHRcdFx0XHRcdGlmICggIWl0ZW0gfHwgIWl0ZW0udHlwZSApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCBpdGVtLnR5cGUgPT09ICdmaWVsZCcgKSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IGVsID0gYi5idWlsZF9maWVsZCggaXRlbS5kYXRhICk7XHJcblx0XHRcdFx0XHRcdGlmICggZWwgKSB7XHJcblx0XHRcdFx0XHRcdFx0Y29sLmFwcGVuZENoaWxkKCBlbCApO1xyXG5cdFx0XHRcdFx0XHRcdGIudHJpZ2dlcl9maWVsZF9kcm9wX2NhbGxiYWNrKCBlbCwgJ2xvYWQnICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCBpdGVtLnR5cGUgPT09ICdzZWN0aW9uJyApIHtcclxuXHRcdFx0XHRcdFx0dGhpcy5yZWJ1aWxkX3NlY3Rpb24oIGl0ZW0uZGF0YSwgY29sICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHR9ICk7XHJcblx0XHRcdGNvbnN0IGNvbXB1dGVkID0gYi5sYXlvdXQuY29tcHV0ZV9lZmZlY3RpdmVfYmFzZXNfZnJvbV9yb3coIHJvdywgYi5jb2xfZ2FwX3BlcmNlbnQgKTtcclxuXHRcdFx0Yi5sYXlvdXQuYXBwbHlfYmFzZXNfdG9fcm93KCByb3csIGNvbXB1dGVkLmJhc2VzICk7XHJcblx0XHRcdHRoaXMuaW5pdF9hbGxfbmVzdGVkX3NvcnRhYmxlcyggc2VjdGlvbiApO1xyXG5cclxuXHRcdFx0Ly8gTkVXOiByZXRhZyBVSURzIGZpcnN0IChzbyB1bmlxdWVuZXNzIGNoZWNrcyBkb24ndCBleGNsdWRlIG9yaWdpbmFscyksIHRoZW4gZGVkdXBlIGFsbCBrZXlzLlxyXG5cdFx0XHR0aGlzLl9yZXRhZ191aWRzX2luX3N1YnRyZWUoIHNlY3Rpb24gKTtcclxuXHRcdFx0dGhpcy5fZGVkdXBlX3N1YnRyZWVfc3RyaWN0KCBzZWN0aW9uICk7XHJcblx0XHRcdHJldHVybiBzZWN0aW9uO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBjb250YWluZXIgKi9cclxuXHRcdGluaXRfYWxsX25lc3RlZF9zb3J0YWJsZXMoY29udGFpbmVyKSB7XHJcblx0XHRcdGNvbnN0IGIgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdGlmICggY29udGFpbmVyLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXInICkgKSB7XHJcblx0XHRcdFx0dGhpcy5pbml0X3NlY3Rpb25fc29ydGFibGUoIGNvbnRhaW5lciApO1xyXG5cdFx0XHR9XHJcblx0XHRcdGNvbnRhaW5lci5xdWVyeVNlbGVjdG9yQWxsKCAnLndwYmNfYmZiX19zZWN0aW9uJyApLmZvckVhY2goIChzZWN0aW9uKSA9PiB7XHJcblx0XHRcdFx0c2VjdGlvbi5xdWVyeVNlbGVjdG9yQWxsKCAnLndwYmNfYmZiX19jb2x1bW4nICkuZm9yRWFjaCggKGNvbCkgPT4ge1xyXG5cdFx0XHRcdFx0dGhpcy5pbml0X3NlY3Rpb25fc29ydGFibGUoIGNvbCApO1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBjb250YWluZXIgKi9cclxuXHRcdGluaXRfc2VjdGlvbl9zb3J0YWJsZShjb250YWluZXIpIHtcclxuXHRcdFx0Y29uc3QgYiA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0aWYgKCAhY29udGFpbmVyICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb25zdCBpc19jb2x1bW4gICAgPSBjb250YWluZXIuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX2NvbHVtbicgKTtcclxuXHRcdFx0Y29uc3QgaXNfdG9wX2xldmVsID0gY29udGFpbmVyLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXInICk7XHJcblx0XHRcdGlmICggIWlzX2NvbHVtbiAmJiAhaXNfdG9wX2xldmVsICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRiLmluaXRfc29ydGFibGU/LiggY29udGFpbmVyICk7XHJcblx0XHR9XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogU2VyaWFsaXphdGlvbiBhbmQgZGVzZXJpYWxpemF0aW9uIG9mIHBhZ2VzL3NlY3Rpb25zL2ZpZWxkcy5cclxuXHQgKi9cclxuXHRVSS5XUEJDX0JGQl9TdHJ1Y3R1cmVfSU8gPSBjbGFzcyBleHRlbmRzIFVJLldQQkNfQkZCX01vZHVsZSB7XHJcblx0XHRpbml0KCkge1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIuZ2V0X3N0cnVjdHVyZSAgICAgICAgPSAoKSA9PiB0aGlzLnNlcmlhbGl6ZSgpO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXIubG9hZF9zYXZlZF9zdHJ1Y3R1cmUgPSAocywgb3B0cykgPT4gdGhpcy5kZXNlcmlhbGl6ZSggcywgb3B0cyApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBAcmV0dXJucyB7QXJyYXl9ICovXHJcblx0XHRzZXJpYWxpemUoKSB7XHJcblx0XHRcdGNvbnN0IGIgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdHRoaXMuX25vcm1hbGl6ZV9pZHMoKTtcclxuXHRcdFx0dGhpcy5fbm9ybWFsaXplX25hbWVzKCk7XHJcblx0XHRcdGNvbnN0IHBhZ2VzID0gW107XHJcblx0XHRcdGIucGFnZXNfY29udGFpbmVyLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3JyApLmZvckVhY2goIChwYWdlX2VsLCBwYWdlX2luZGV4KSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgY29udGFpbmVyID0gcGFnZV9lbC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXInICk7XHJcblx0XHRcdFx0Y29uc3QgY29udGVudCAgID0gW107XHJcblx0XHRcdFx0aWYgKCAhY29udGFpbmVyICkge1xyXG5cdFx0XHRcdFx0cGFnZXMucHVzaCggeyBwYWdlOiBwYWdlX2luZGV4ICsgMSwgY29udGVudCB9ICk7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGNvbnRhaW5lci5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gKicgKS5mb3JFYWNoKCAoY2hpbGQpID0+IHtcclxuXHRcdFx0XHRcdGlmICggY2hpbGQuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgKSB7XHJcblx0XHRcdFx0XHRcdGNvbnRlbnQucHVzaCggeyB0eXBlOiAnc2VjdGlvbicsIGRhdGE6IHRoaXMuc2VyaWFsaXplX3NlY3Rpb24oIGNoaWxkICkgfSApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIGNoaWxkLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19maWVsZCcgKSApIHtcclxuXHRcdFx0XHRcdFx0aWYgKCBjaGlsZC5jbGFzc0xpc3QuY29udGFpbnMoICdpcy1pbnZhbGlkJyApICkge1xyXG5cdFx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRjb25zdCBmX2RhdGEgPSBDb3JlLldQQkNfRm9ybV9CdWlsZGVyX0hlbHBlci5nZXRfYWxsX2RhdGFfYXR0cmlidXRlcyggY2hpbGQgKTtcclxuXHRcdFx0XHRcdFx0Ly8gRHJvcCBlcGhlbWVyYWwvZWRpdG9yLW9ubHkgZmxhZ3NcclxuXHRcdFx0XHRcdFx0WyAndWlkJywgJ2ZyZXNoJywgJ2F1dG9uYW1lJywgJ3dhc19sb2FkZWQnLCAnbmFtZV91c2VyX3RvdWNoZWQnIF1cclxuXHRcdFx0XHRcdFx0XHQuZm9yRWFjaCggayA9PiB7XHJcblx0XHRcdFx0XHRcdFx0XHRpZiAoIGsgaW4gZl9kYXRhICkgZGVsZXRlIGZfZGF0YVtrXTtcclxuXHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0XHRcdGNvbnRlbnQucHVzaCggeyB0eXBlOiAnZmllbGQnLCBkYXRhOiBmX2RhdGEgfSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRwYWdlcy5wdXNoKCB7IHBhZ2U6IHBhZ2VfaW5kZXggKyAxLCBjb250ZW50IH0gKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHRyZXR1cm4gcGFnZXM7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBzZWN0aW9uX2VsXHJcblx0XHQgKiBAcmV0dXJucyB7e2lkOnN0cmluZyxsYWJlbDpzdHJpbmcsaHRtbF9pZDpzdHJpbmcsY3NzY2xhc3M6c3RyaW5nLGNvbF9zdHlsZXM6c3RyaW5nLGNvbHVtbnM6QXJyYXl9fVxyXG5cdFx0ICovXHJcblx0XHRzZXJpYWxpemVfc2VjdGlvbihzZWN0aW9uX2VsKSB7XHJcblx0XHRcdGNvbnN0IHJvdyA9IHNlY3Rpb25fZWwucXVlcnlTZWxlY3RvciggJzpzY29wZSA+IC53cGJjX2JmYl9fcm93JyApO1xyXG5cclxuXHRcdFx0Ly8gTkVXOiByZWFkIHBlci1jb2x1bW4gc3R5bGVzIGZyb20gZGF0YXNldC9hdHRyaWJ1dGVzICh1bmRlcnNjb3JlICYgaHlwaGVuKVxyXG5cdFx0XHR2YXIgY29sX3N0eWxlc19yYXcgPVxyXG5cdFx0XHRcdFx0c2VjdGlvbl9lbC5nZXRBdHRyaWJ1dGUoICdkYXRhLWNvbF9zdHlsZXMnICkgfHxcclxuXHRcdFx0XHRcdChzZWN0aW9uX2VsLmRhdGFzZXQgPyAoc2VjdGlvbl9lbC5kYXRhc2V0LmNvbF9zdHlsZXMpIDogJycpIHx8XHJcblx0XHRcdFx0XHQnJztcclxuXHJcblx0XHRcdGNvbnN0IGJhc2UgPSB7XHJcblx0XHRcdFx0aWQgICAgICAgIDogc2VjdGlvbl9lbC5kYXRhc2V0LmlkLFxyXG5cdFx0XHRcdGxhYmVsICAgICA6IHNlY3Rpb25fZWwuZGF0YXNldC5sYWJlbCB8fCAnJyxcclxuXHRcdFx0XHRodG1sX2lkICAgOiBzZWN0aW9uX2VsLmRhdGFzZXQuaHRtbF9pZCB8fCAnJyxcclxuXHRcdFx0XHRjc3NjbGFzcyAgOiBzZWN0aW9uX2VsLmRhdGFzZXQuY3NzY2xhc3MgfHwgJycsXHJcblx0XHRcdFx0Y29sX3N0eWxlczogU3RyaW5nKCBjb2xfc3R5bGVzX3JhdyApICAgICAgICAvLyA8LS0gTkVXOiBrZWVwIGFzIHJhdyBKU09OIHN0cmluZ1xyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0aWYgKCAhcm93ICkge1xyXG5cdFx0XHRcdHJldHVybiBPYmplY3QuYXNzaWduKCB7fSwgYmFzZSwgeyBjb2x1bW5zOiBbXSB9ICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGNvbnN0IGNvbHVtbnMgPSBbXTtcclxuXHRcdFx0cm93LnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2NvbHVtbicgKS5mb3JFYWNoKCBmdW5jdGlvbiAoY29sKSB7XHJcblx0XHRcdFx0Y29uc3Qgd2lkdGggPSBjb2wuc3R5bGUuZmxleEJhc2lzIHx8ICcxMDAlJztcclxuXHRcdFx0XHRjb25zdCBpdGVtcyA9IFtdO1xyXG5cdFx0XHRcdEFycmF5LnByb3RvdHlwZS5mb3JFYWNoLmNhbGwoIGNvbC5jaGlsZHJlbiwgZnVuY3Rpb24gKGNoaWxkKSB7XHJcblx0XHRcdFx0XHRpZiAoIGNoaWxkLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19zZWN0aW9uJyApICkge1xyXG5cdFx0XHRcdFx0XHRpdGVtcy5wdXNoKCB7IHR5cGU6ICdzZWN0aW9uJywgZGF0YTogdGhpcy5zZXJpYWxpemVfc2VjdGlvbiggY2hpbGQgKSB9ICk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGlmICggY2hpbGQuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX2ZpZWxkJyApICkge1xyXG5cdFx0XHRcdFx0XHRpZiAoIGNoaWxkLmNsYXNzTGlzdC5jb250YWlucyggJ2lzLWludmFsaWQnICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdGNvbnN0IGZfZGF0YSA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLmdldF9hbGxfZGF0YV9hdHRyaWJ1dGVzKCBjaGlsZCApO1xyXG5cdFx0XHRcdFx0XHRbICd1aWQnLCAnZnJlc2gnLCAnYXV0b25hbWUnLCAnd2FzX2xvYWRlZCcsICduYW1lX3VzZXJfdG91Y2hlZCcgXS5mb3JFYWNoKCBmdW5jdGlvbiAoaykge1xyXG5cdFx0XHRcdFx0XHRcdGlmICggayBpbiBmX2RhdGEgKSB7XHJcblx0XHRcdFx0XHRcdFx0XHRkZWxldGUgZl9kYXRhW2tdO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0XHRpdGVtcy5wdXNoKCB7IHR5cGU6ICdmaWVsZCcsIGRhdGE6IGZfZGF0YSB9ICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fS5iaW5kKCB0aGlzICkgKTtcclxuXHRcdFx0XHRjb2x1bW5zLnB1c2goIHsgd2lkdGg6IHdpZHRoLCBpdGVtczogaXRlbXMgfSApO1xyXG5cdFx0XHR9LmJpbmQoIHRoaXMgKSApO1xyXG5cclxuXHRcdFx0Ly8gQ2xhbXAgcGVyc2lzdGVkIGNvbF9zdHlsZXMgdG8gdGhlIGFjdHVhbCBudW1iZXIgb2YgY29sdW1ucyBvbiBTYXZlLlxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGNvbnN0IGNvbENvdW50ID0gY29sdW1ucy5sZW5ndGg7XHJcblx0XHRcdFx0Y29uc3QgcmF3ICAgICAgPSBTdHJpbmcoIGNvbF9zdHlsZXNfcmF3IHx8ICcnICkudHJpbSgpO1xyXG5cclxuXHRcdFx0XHRpZiAoIHJhdyApIHtcclxuXHRcdFx0XHRcdGxldCBhcnIgPSBbXTtcclxuXHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IHBhcnNlZCA9IEpTT04ucGFyc2UoIHJhdyApO1xyXG5cdFx0XHRcdFx0XHRhcnIgICAgICAgICAgPSBBcnJheS5pc0FycmF5KCBwYXJzZWQgKSA/IHBhcnNlZCA6IChwYXJzZWQgJiYgQXJyYXkuaXNBcnJheSggcGFyc2VkLmNvbHVtbnMgKSA/IHBhcnNlZC5jb2x1bW5zIDogW10pO1xyXG5cdFx0XHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHRcdFx0XHRhcnIgPSBbXTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoIGNvbENvdW50IDw9IDAgKSB7XHJcblx0XHRcdFx0XHRcdGJhc2UuY29sX3N0eWxlcyA9ICdbXSc7XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRpZiAoIGFyci5sZW5ndGggPiBjb2xDb3VudCApIGFyci5sZW5ndGggPSBjb2xDb3VudDtcclxuXHRcdFx0XHRcdFx0d2hpbGUgKCBhcnIubGVuZ3RoIDwgY29sQ291bnQgKSBhcnIucHVzaCgge30gKTtcclxuXHRcdFx0XHRcdFx0YmFzZS5jb2xfc3R5bGVzID0gSlNPTi5zdHJpbmdpZnkoIGFyciApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRiYXNlLmNvbF9zdHlsZXMgPSAnJztcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0cmV0dXJuIE9iamVjdC5hc3NpZ24oIHt9LCBiYXNlLCB7IGNvbHVtbnM6IGNvbHVtbnMgfSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQHBhcmFtIHtBcnJheX0gc3RydWN0dXJlXHJcblx0XHQgKiBAcGFyYW0ge3tkZWZlcklmVHlwaW5nPzogYm9vbGVhbn19IFtvcHRzID0ge31dXHJcblx0XHQgKi9cclxuXHRcdGRlc2VyaWFsaXplKHN0cnVjdHVyZSwgeyBkZWZlcklmVHlwaW5nID0gdHJ1ZSB9ID0ge30pIHtcclxuXHRcdFx0Y29uc3QgYiA9IHRoaXMuYnVpbGRlcjtcclxuXHRcdFx0aWYgKCBkZWZlcklmVHlwaW5nICYmIHRoaXMuX2lzX3R5cGluZ19pbl9pbnNwZWN0b3IoKSApIHtcclxuXHRcdFx0XHRjbGVhclRpbWVvdXQoIHRoaXMuX2RlZmVyX3RpbWVyICk7XHJcblx0XHRcdFx0dGhpcy5fZGVmZXJfdGltZXIgPSBzZXRUaW1lb3V0KCAoKSA9PiB7XHJcblx0XHRcdFx0XHR0aGlzLmRlc2VyaWFsaXplKCBzdHJ1Y3R1cmUsIHsgZGVmZXJJZlR5cGluZzogZmFsc2UgfSApO1xyXG5cdFx0XHRcdH0sIDE1MCApO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRiLnBhZ2VzX2NvbnRhaW5lci5pbm5lckhUTUwgPSAnJztcclxuXHRcdFx0Yi5wYWdlX2NvdW50ZXIgICAgICAgICAgICAgID0gMDtcclxuXHRcdFx0KHN0cnVjdHVyZSB8fCBbXSkuZm9yRWFjaCggKHBhZ2VfZGF0YSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IHBhZ2VfZWwgICAgICAgICAgICAgICA9IGIucGFnZXNfc2VjdGlvbnMuYWRkX3BhZ2UoIHsgc2Nyb2xsOiBmYWxzZSB9ICk7XHJcblx0XHRcdFx0Y29uc3Qgc2VjdGlvbl9jb250YWluZXIgICAgID0gcGFnZV9lbC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19mb3JtX3ByZXZpZXdfc2VjdGlvbl9jb250YWluZXInICk7XHJcblx0XHRcdFx0c2VjdGlvbl9jb250YWluZXIuaW5uZXJIVE1MID0gJyc7XHJcblx0XHRcdFx0Yi5pbml0X3NlY3Rpb25fc29ydGFibGU/Liggc2VjdGlvbl9jb250YWluZXIgKTtcclxuXHRcdFx0XHQocGFnZV9kYXRhLmNvbnRlbnQgfHwgW10pLmZvckVhY2goIChpdGVtKSA9PiB7XHJcblx0XHRcdFx0XHRpZiAoIGl0ZW0udHlwZSA9PT0gJ3NlY3Rpb24nICkge1xyXG5cdFx0XHRcdFx0XHQvLyBOb3cgcmV0dXJucyB0aGUgZWxlbWVudDsgYXR0cmlidXRlcyAoaW5jbC4gY29sX3N0eWxlcykgYXJlIGFwcGxpZWQgaW5zaWRlIHJlYnVpbGQuXHJcblx0XHRcdFx0XHRcdGIucGFnZXNfc2VjdGlvbnMucmVidWlsZF9zZWN0aW9uKCBpdGVtLmRhdGEsIHNlY3Rpb25fY29udGFpbmVyICk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGlmICggaXRlbS50eXBlID09PSAnZmllbGQnICkge1xyXG5cdFx0XHRcdFx0XHRjb25zdCBlbCA9IGIuYnVpbGRfZmllbGQoIGl0ZW0uZGF0YSApO1xyXG5cdFx0XHRcdFx0XHRpZiAoIGVsICkge1xyXG5cdFx0XHRcdFx0XHRcdHNlY3Rpb25fY29udGFpbmVyLmFwcGVuZENoaWxkKCBlbCApO1xyXG5cdFx0XHRcdFx0XHRcdGIudHJpZ2dlcl9maWVsZF9kcm9wX2NhbGxiYWNrKCBlbCwgJ2xvYWQnICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0Yi51c2FnZT8udXBkYXRlX3BhbGV0dGVfdWk/LigpO1xyXG5cdFx0XHRiLmJ1cy5lbWl0KCBDb3JlLldQQkNfQkZCX0V2ZW50cy5TVFJVQ1RVUkVfTE9BREVELCB7IHN0cnVjdHVyZSB9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0X25vcm1hbGl6ZV9pZHMoKSB7XHJcblx0XHRcdGNvbnN0IGIgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdGIucGFnZXNfY29udGFpbmVyLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX3BhbmVsLS1wcmV2aWV3IC53cGJjX2JmYl9fZmllbGQ6bm90KC5pcy1pbnZhbGlkKScgKS5mb3JFYWNoKCAoZWwpID0+IHtcclxuXHRcdFx0XHRjb25zdCBkYXRhID0gQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuZ2V0X2FsbF9kYXRhX2F0dHJpYnV0ZXMoIGVsICk7XHJcblx0XHRcdFx0Y29uc3Qgd2FudCA9IENvcmUuV1BCQ19CRkJfU2FuaXRpemUuc2FuaXRpemVfaHRtbF9pZCggZGF0YS5pZCB8fCAnJyApIHx8ICdmaWVsZCc7XHJcblx0XHRcdFx0Y29uc3QgdW5pcSA9IGIuaWQuZW5zdXJlX3VuaXF1ZV9maWVsZF9pZCggd2FudCwgZWwgKTtcclxuXHRcdFx0XHRpZiAoIGRhdGEuaWQgIT09IHVuaXEgKSB7XHJcblx0XHRcdFx0XHRlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLWlkJywgdW5pcSApO1xyXG5cdFx0XHRcdFx0aWYgKCBiLnByZXZpZXdfbW9kZSApIHtcclxuXHRcdFx0XHRcdFx0Yi5yZW5kZXJfcHJldmlldyggZWwgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHRfbm9ybWFsaXplX25hbWVzKCkge1xyXG5cdFx0XHRjb25zdCBiID0gdGhpcy5idWlsZGVyO1xyXG5cdFx0XHRiLnBhZ2VzX2NvbnRhaW5lci5xdWVyeVNlbGVjdG9yQWxsKCAnLndwYmNfYmZiX19wYW5lbC0tcHJldmlldyAud3BiY19iZmJfX2ZpZWxkOm5vdCguaXMtaW52YWxpZCknICkuZm9yRWFjaCggKGVsKSA9PiB7XHJcblx0XHRcdFx0Y29uc3QgZGF0YSA9IENvcmUuV1BCQ19Gb3JtX0J1aWxkZXJfSGVscGVyLmdldF9hbGxfZGF0YV9hdHRyaWJ1dGVzKCBlbCApO1xyXG5cdFx0XHRcdGNvbnN0IGJhc2UgPSBDb3JlLldQQkNfQkZCX1Nhbml0aXplLnNhbml0aXplX2h0bWxfbmFtZSggKGRhdGEubmFtZSAhPSBudWxsKSA/IGRhdGEubmFtZSA6IGRhdGEuaWQgKSB8fCAnZmllbGQnO1xyXG5cdFx0XHRcdGNvbnN0IHVuaXEgPSBiLmlkLmVuc3VyZV91bmlxdWVfZmllbGRfbmFtZSggYmFzZSwgZWwgKTtcclxuXHRcdFx0XHRpZiAoIGRhdGEubmFtZSAhPT0gdW5pcSApIHtcclxuXHRcdFx0XHRcdGVsLnNldEF0dHJpYnV0ZSggJ2RhdGEtbmFtZScsIHVuaXEgKTtcclxuXHRcdFx0XHRcdGlmICggYi5wcmV2aWV3X21vZGUgKSB7XHJcblx0XHRcdFx0XHRcdGIucmVuZGVyX3ByZXZpZXcoIGVsICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqIEByZXR1cm5zIHtib29sZWFufSAqL1xyXG5cdFx0X2lzX3R5cGluZ19pbl9pbnNwZWN0b3IoKSB7XHJcblx0XHRcdGNvbnN0IGlucyA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0cmV0dXJuICEhKGlucyAmJiBkb2N1bWVudC5hY3RpdmVFbGVtZW50ICYmIGlucy5jb250YWlucyggZG9jdW1lbnQuYWN0aXZlRWxlbWVudCApKTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBNaW5pbWFsLCBzdGFuZGFsb25lIGd1YXJkIHRoYXQgZW5mb3JjZXMgcGVyLWNvbHVtbiBtaW4gd2lkdGhzIGJhc2VkIG9uIGZpZWxkcycgZGF0YS1taW5fd2lkdGguXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7VUkuV1BCQ19CRkJfTWluX1dpZHRoX0d1YXJkfVxyXG5cdCAqL1xyXG5cdFVJLldQQkNfQkZCX01pbl9XaWR0aF9HdWFyZCA9IGNsYXNzIGV4dGVuZHMgVUkuV1BCQ19CRkJfTW9kdWxlIHtcclxuXHJcblx0XHRjb25zdHJ1Y3RvcihidWlsZGVyKSB7XHJcblx0XHRcdHN1cGVyKCBidWlsZGVyICk7XHJcblx0XHRcdHRoaXMuX29uX2ZpZWxkX2FkZCAgICAgICAgPSB0aGlzLl9vbl9maWVsZF9hZGQuYmluZCggdGhpcyApO1xyXG5cdFx0XHR0aGlzLl9vbl9maWVsZF9yZW1vdmUgICAgID0gdGhpcy5fb25fZmllbGRfcmVtb3ZlLmJpbmQoIHRoaXMgKTtcclxuXHRcdFx0dGhpcy5fb25fc3RydWN0dXJlX2xvYWRlZCA9IHRoaXMuX29uX3N0cnVjdHVyZV9sb2FkZWQuYmluZCggdGhpcyApO1xyXG5cdFx0XHR0aGlzLl9vbl9zdHJ1Y3R1cmVfY2hhbmdlID0gdGhpcy5fb25fc3RydWN0dXJlX2NoYW5nZS5iaW5kKCB0aGlzICk7XHJcblx0XHRcdHRoaXMuX29uX3dpbmRvd19yZXNpemUgICAgPSB0aGlzLl9vbl93aW5kb3dfcmVzaXplLmJpbmQoIHRoaXMgKTtcclxuXHJcblx0XHRcdHRoaXMuX3BlbmRpbmdfcm93cyA9IG5ldyBTZXQoKTtcclxuXHRcdFx0dGhpcy5fcGVuZGluZ19hbGwgID0gZmFsc2U7XHJcblx0XHRcdHRoaXMuX3JhZl9pZCAgICAgICA9IDA7XHJcblx0XHR9XHJcblxyXG5cdFx0aW5pdCgpIHtcclxuXHRcdFx0Y29uc3QgRVYgPSBDb3JlLldQQkNfQkZCX0V2ZW50cztcclxuXHRcdFx0dGhpcy5idWlsZGVyPy5idXM/Lm9uPy4oIEVWLkZJRUxEX0FERCwgdGhpcy5fb25fZmllbGRfYWRkICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlcj8uYnVzPy5vbj8uKCBFVi5GSUVMRF9SRU1PVkUsIHRoaXMuX29uX2ZpZWxkX3JlbW92ZSApO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXI/LmJ1cz8ub24/LiggRVYuU1RSVUNUVVJFX0xPQURFRCwgdGhpcy5fb25fc3RydWN0dXJlX2xvYWRlZCApO1xyXG5cdFx0XHQvLyBSZWZyZXNoIHNlbGVjdGl2ZWx5IG9uIHN0cnVjdHVyZSBjaGFuZ2UgKE5PVCBvbiBldmVyeSBwcm9wIGlucHV0KS5cclxuXHRcdFx0dGhpcy5idWlsZGVyPy5idXM/Lm9uPy4oIEVWLlNUUlVDVFVSRV9DSEFOR0UsIHRoaXMuX29uX3N0cnVjdHVyZV9jaGFuZ2UgKTtcclxuXHJcblx0XHRcdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCAncmVzaXplJywgdGhpcy5fb25fd2luZG93X3Jlc2l6ZSwgeyBwYXNzaXZlOiB0cnVlIH0gKTtcclxuXHRcdFx0dGhpcy5fc2NoZWR1bGVfcmVmcmVzaF9hbGwoKTtcclxuXHRcdH1cclxuXHJcblx0XHRkZXN0cm95KCkge1xyXG5cdFx0XHRjb25zdCBFViA9IENvcmUuV1BCQ19CRkJfRXZlbnRzO1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXI/LmJ1cz8ub2ZmPy4oIEVWLkZJRUxEX0FERCwgdGhpcy5fb25fZmllbGRfYWRkICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlcj8uYnVzPy5vZmY/LiggRVYuRklFTERfUkVNT1ZFLCB0aGlzLl9vbl9maWVsZF9yZW1vdmUgKTtcclxuXHRcdFx0dGhpcy5idWlsZGVyPy5idXM/Lm9mZj8uKCBFVi5TVFJVQ1RVUkVfTE9BREVELCB0aGlzLl9vbl9zdHJ1Y3R1cmVfbG9hZGVkICk7XHJcblx0XHRcdHRoaXMuYnVpbGRlcj8uYnVzPy5vZmY/LiggRVYuU1RSVUNUVVJFX0NIQU5HRSwgdGhpcy5fb25fc3RydWN0dXJlX2NoYW5nZSApO1xyXG5cdFx0XHR3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ3Jlc2l6ZScsIHRoaXMuX29uX3dpbmRvd19yZXNpemUgKTtcclxuXHRcdH1cclxuXHJcblx0XHRfb25fZmllbGRfYWRkKGUpIHtcclxuXHRcdFx0dGhpcy5fc2NoZWR1bGVfcmVmcmVzaF9hbGwoKTtcclxuXHRcdFx0Ly8gaWYgeW91IHJlYWxseSB3YW50IHRvIGJlIG1pbmltYWwgd29yayBoZXJlLCBrZWVwIHlvdXIgcm93LW9ubHkgdmVyc2lvbi5cclxuXHRcdH1cclxuXHJcblx0XHRfb25fZmllbGRfcmVtb3ZlKGUpIHtcclxuXHRcdFx0Y29uc3Qgc3JjX2VsID0gZT8uZGV0YWlsPy5lbCB8fCBudWxsO1xyXG5cdFx0XHRjb25zdCByb3cgICAgPSAoc3JjX2VsICYmIHNyY19lbC5jbG9zZXN0KSA/IHNyY19lbC5jbG9zZXN0KCAnLndwYmNfYmZiX19yb3cnICkgOiBudWxsO1xyXG5cdFx0XHRpZiAoIHJvdyApIHtcclxuXHRcdFx0XHR0aGlzLl9zY2hlZHVsZV9yZWZyZXNoX3Jvdyggcm93ICk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0dGhpcy5fc2NoZWR1bGVfcmVmcmVzaF9hbGwoKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdF9vbl9zdHJ1Y3R1cmVfbG9hZGVkKCkge1xyXG5cdFx0XHR0aGlzLl9zY2hlZHVsZV9yZWZyZXNoX2FsbCgpO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9vbl9zdHJ1Y3R1cmVfY2hhbmdlKGUpIHtcclxuXHRcdFx0Y29uc3QgcmVhc29uID0gZT8uZGV0YWlsPy5yZWFzb24gfHwgJyc7XHJcblx0XHRcdGNvbnN0IGtleSAgICA9IGU/LmRldGFpbD8ua2V5IHx8ICcnO1xyXG5cclxuXHRcdFx0Ly8gSWdub3JlIG5vaXN5IHByb3AgY2hhbmdlcyB0aGF0IGRvbid0IGFmZmVjdCBtaW4gd2lkdGhzLlxyXG5cdFx0XHRpZiAoIHJlYXNvbiA9PT0gJ3Byb3AtY2hhbmdlJyAmJiBrZXkgIT09ICdtaW5fd2lkdGgnICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgZWwgID0gZT8uZGV0YWlsPy5lbCB8fCBudWxsO1xyXG5cdFx0XHRjb25zdCByb3cgPSBlbD8uY2xvc2VzdD8uKCAnLndwYmNfYmZiX19yb3cnICkgfHwgbnVsbDtcclxuXHRcdFx0aWYgKCByb3cgKSB7XHJcblx0XHRcdFx0dGhpcy5fc2NoZWR1bGVfcmVmcmVzaF9yb3coIHJvdyApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHRoaXMuX3NjaGVkdWxlX3JlZnJlc2hfYWxsKCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRfb25fd2luZG93X3Jlc2l6ZSgpIHtcclxuXHRcdFx0dGhpcy5fc2NoZWR1bGVfcmVmcmVzaF9hbGwoKTtcclxuXHRcdH1cclxuXHJcblx0XHRfc2NoZWR1bGVfcmVmcmVzaF9yb3cocm93X2VsKSB7XHJcblx0XHRcdGlmICggIXJvd19lbCApIHJldHVybjtcclxuXHRcdFx0dGhpcy5fcGVuZGluZ19yb3dzLmFkZCggcm93X2VsICk7XHJcblx0XHRcdHRoaXMuX2tpY2tfcmFmKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0X3NjaGVkdWxlX3JlZnJlc2hfYWxsKCkge1xyXG5cdFx0XHR0aGlzLl9wZW5kaW5nX2FsbCA9IHRydWU7XHJcblx0XHRcdHRoaXMuX3BlbmRpbmdfcm93cy5jbGVhcigpO1xyXG5cdFx0XHR0aGlzLl9raWNrX3JhZigpO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9raWNrX3JhZigpIHtcclxuXHRcdFx0aWYgKCB0aGlzLl9yYWZfaWQgKSByZXR1cm47XHJcblx0XHRcdHRoaXMuX3JhZl9pZCA9ICh3aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lIHx8IHNldFRpbWVvdXQpKCAoKSA9PiB7XHJcblx0XHRcdFx0dGhpcy5fcmFmX2lkID0gMDtcclxuXHRcdFx0XHRpZiAoIHRoaXMuX3BlbmRpbmdfYWxsICkge1xyXG5cdFx0XHRcdFx0dGhpcy5fcGVuZGluZ19hbGwgPSBmYWxzZTtcclxuXHRcdFx0XHRcdHRoaXMucmVmcmVzaF9hbGwoKTtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Y29uc3Qgcm93cyA9IEFycmF5LmZyb20oIHRoaXMuX3BlbmRpbmdfcm93cyApO1xyXG5cdFx0XHRcdHRoaXMuX3BlbmRpbmdfcm93cy5jbGVhcigpO1xyXG5cdFx0XHRcdHJvd3MuZm9yRWFjaCggKHIpID0+IHRoaXMucmVmcmVzaF9yb3coIHIgKSApO1xyXG5cdFx0XHR9LCAwICk7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdHJlZnJlc2hfYWxsKCkge1xyXG5cdFx0XHR0aGlzLmJ1aWxkZXI/LnBhZ2VzX2NvbnRhaW5lclxyXG5cdFx0XHRcdD8ucXVlcnlTZWxlY3RvckFsbD8uKCAnLndwYmNfYmZiX19yb3cnIClcclxuXHRcdFx0XHQ/LmZvckVhY2g/LiggKHJvdykgPT4gdGhpcy5yZWZyZXNoX3Jvdyggcm93ICkgKTtcclxuXHRcdH1cclxuXHJcblx0XHRyZWZyZXNoX3Jvdyhyb3dfZWwpIHtcclxuXHRcdFx0aWYgKCAhcm93X2VsICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgY29scyA9IHJvd19lbC5xdWVyeVNlbGVjdG9yQWxsKCAnOnNjb3BlID4gLndwYmNfYmZiX19jb2x1bW4nICk7XHJcblxyXG5cdFx0XHQvLyAxKSBSZWNhbGN1bGF0ZSBlYWNoIGNvbHVtbuKAmXMgcmVxdWlyZWQgbWluIHB4IGFuZCB3cml0ZSBpdCB0byB0aGUgQ1NTIHZhci5cclxuXHRcdFx0Y29scy5mb3JFYWNoKCAoY29sKSA9PiB0aGlzLmFwcGx5X2NvbF9taW4oIGNvbCApICk7XHJcblxyXG5cdFx0XHQvLyAyKSBFbmZvcmNlIGl0IGF0IHRoZSBDU1MgbGV2ZWwgcmlnaHQgYXdheSBzbyBsYXlvdXQgY2Fu4oCZdCByZW5kZXIgbmFycm93ZXIuXHJcblx0XHRcdGNvbHMuZm9yRWFjaCggKGNvbCkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IHB4ICAgICAgICAgICA9IHBhcnNlRmxvYXQoIGdldENvbXB1dGVkU3R5bGUoIGNvbCApLmdldFByb3BlcnR5VmFsdWUoICctLXdwYmMtY29sLW1pbicgKSB8fCAnMCcgKSB8fCAwO1xyXG5cdFx0XHRcdGNvbC5zdHlsZS5taW5XaWR0aCA9IHB4ID4gMCA/IE1hdGgucm91bmQoIHB4ICkgKyAncHgnIDogJyc7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIDMpIE5vcm1hbGl6ZSBjdXJyZW50IGJhc2VzIHNvIHRoZSByb3cgcmVzcGVjdHMgYWxsIG1pbnMgd2l0aG91dCBvdmVyZmxvdy5cclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRjb25zdCBiICAgPSB0aGlzLmJ1aWxkZXI7XHJcblx0XHRcdFx0Y29uc3QgZ3AgID0gYi5jb2xfZ2FwX3BlcmNlbnQ7XHJcblx0XHRcdFx0Y29uc3QgZWZmID0gYi5sYXlvdXQuY29tcHV0ZV9lZmZlY3RpdmVfYmFzZXNfZnJvbV9yb3coIHJvd19lbCwgZ3AgKTsgIC8vIHsgYmFzZXMsIGF2YWlsYWJsZSB9XHJcblx0XHRcdFx0Ly8gUmUtZml0ICpjdXJyZW50KiBiYXNlcyBhZ2FpbnN0IG1pbnMgKHNhbWUgYWxnb3JpdGhtIGxheW91dCBjaGlwcyB1c2UpLlxyXG5cdFx0XHRcdGNvbnN0IGZpdHRlZCA9IFVJLldQQkNfQkZCX0xheW91dF9DaGlwcy5fZml0X3dlaWdodHNfcmVzcGVjdGluZ19taW4oIGIsIHJvd19lbCwgZWZmLmJhc2VzICk7XHJcblx0XHRcdFx0aWYgKCBBcnJheS5pc0FycmF5KCBmaXR0ZWQgKSApIHtcclxuXHRcdFx0XHRcdGNvbnN0IGNoYW5nZWQgPSBmaXR0ZWQuc29tZSggKHYsIGkpID0+IE1hdGguYWJzKCB2IC0gZWZmLmJhc2VzW2ldICkgPiAwLjAxICk7XHJcblx0XHRcdFx0XHRpZiAoIGNoYW5nZWQgKSB7XHJcblx0XHRcdFx0XHRcdGIubGF5b3V0LmFwcGx5X2Jhc2VzX3RvX3Jvdyggcm93X2VsLCBmaXR0ZWQgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHRcdHcuX3dwYmM/LmRldj8uZXJyb3I/LiggJ1dQQkNfQkZCX01pbl9XaWR0aF9HdWFyZCAtIHJlZnJlc2hfcm93JywgZSApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0YXBwbHlfY29sX21pbihjb2xfZWwpIHtcclxuXHRcdFx0aWYgKCAhY29sX2VsICkgcmV0dXJuO1xyXG5cdFx0XHRsZXQgbWF4X3B4ICAgID0gMDtcclxuXHRcdFx0Y29uc3QgY29sUmVjdCA9IGNvbF9lbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcclxuXHRcdFx0Y29sX2VsLnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2ZpZWxkJyApLmZvckVhY2goIChmaWVsZCkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IHJhdyA9IGZpZWxkLmdldEF0dHJpYnV0ZSggJ2RhdGEtbWluX3dpZHRoJyApO1xyXG5cdFx0XHRcdGxldCBweCAgICA9IDA7XHJcblx0XHRcdFx0aWYgKCByYXcgKSB7XHJcblx0XHRcdFx0XHRjb25zdCBzID0gU3RyaW5nKCByYXcgKS50cmltKCkudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHRcdGlmICggcy5lbmRzV2l0aCggJyUnICkgKSB7XHJcblx0XHRcdFx0XHRcdGNvbnN0IG4gPSBwYXJzZUZsb2F0KCBzICk7XHJcblx0XHRcdFx0XHRcdGlmICggTnVtYmVyLmlzRmluaXRlKCBuICkgJiYgY29sUmVjdC53aWR0aCA+IDAgKSB7XHJcblx0XHRcdFx0XHRcdFx0cHggPSAobiAvIDEwMCkgKiBjb2xSZWN0LndpZHRoO1xyXG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRcdHB4ID0gMDtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0cHggPSB0aGlzLnBhcnNlX2xlbl9weCggcyApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRjb25zdCBjcyA9IGdldENvbXB1dGVkU3R5bGUoIGZpZWxkICk7XHJcblx0XHRcdFx0XHRweCAgICAgICA9IHBhcnNlRmxvYXQoIGNzLm1pbldpZHRoIHx8ICcwJyApIHx8IDA7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGlmICggcHggPiBtYXhfcHggKSBtYXhfcHggPSBweDtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHRjb2xfZWwuc3R5bGUuc2V0UHJvcGVydHkoICctLXdwYmMtY29sLW1pbicsIG1heF9weCA+IDAgPyBNYXRoLnJvdW5kKCBtYXhfcHggKSArICdweCcgOiAnMHB4JyApO1xyXG5cdFx0fVxyXG5cclxuXHRcdHBhcnNlX2xlbl9weCh2YWx1ZSkge1xyXG5cdFx0XHRpZiAoIHZhbHVlID09IG51bGwgKSByZXR1cm4gMDtcclxuXHRcdFx0Y29uc3QgcyA9IFN0cmluZyggdmFsdWUgKS50cmltKCkudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0aWYgKCBzID09PSAnJyApIHJldHVybiAwO1xyXG5cdFx0XHRpZiAoIHMuZW5kc1dpdGgoICdweCcgKSApIHtcclxuXHRcdFx0XHRjb25zdCBuID0gcGFyc2VGbG9hdCggcyApO1xyXG5cdFx0XHRcdHJldHVybiBOdW1iZXIuaXNGaW5pdGUoIG4gKSA/IG4gOiAwO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggcy5lbmRzV2l0aCggJ3JlbScgKSB8fCBzLmVuZHNXaXRoKCAnZW0nICkgKSB7XHJcblx0XHRcdFx0Y29uc3QgbiAgICA9IHBhcnNlRmxvYXQoIHMgKTtcclxuXHRcdFx0XHRjb25zdCBiYXNlID0gcGFyc2VGbG9hdCggZ2V0Q29tcHV0ZWRTdHlsZSggZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50ICkuZm9udFNpemUgKSB8fCAxNjtcclxuXHRcdFx0XHRyZXR1cm4gTnVtYmVyLmlzRmluaXRlKCBuICkgPyBuICogYmFzZSA6IDA7XHJcblx0XHRcdH1cclxuXHRcdFx0Y29uc3QgbiA9IHBhcnNlRmxvYXQoIHMgKTtcclxuXHRcdFx0cmV0dXJuIE51bWJlci5pc0Zpbml0ZSggbiApID8gbiA6IDA7XHJcblx0XHR9XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogV1BCQ19CRkJfVG9nZ2xlX05vcm1hbGl6ZXJcclxuXHQgKlxyXG5cdCAqIENvbnZlcnRzIHBsYWluIGNoZWNrYm94ZXMgaW50byB0b2dnbGUgVUk6XHJcblx0ICogPGRpdiBjbGFzcz1cImluc3BlY3Rvcl9fY29udHJvbCB3cGJjX3VpX190b2dnbGVcIj5cclxuXHQgKiAgIDxpbnB1dCB0eXBlPVwiY2hlY2tib3hcIiBpZD1cInt1bmlxdWV9XCIgZGF0YS1pbnNwZWN0b3Ita2V5PVwiLi4uXCIgY2xhc3M9XCJpbnNwZWN0b3JfX2lucHV0XCIgcm9sZT1cInN3aXRjaFwiIGFyaWEtY2hlY2tlZD1cInRydWV8ZmFsc2VcIj5cclxuXHQgKiAgIDxsYWJlbCBjbGFzcz1cIndwYmNfdWlfX3RvZ2dsZV9pY29uXCIgIGZvcj1cInt1bmlxdWV9XCI+PC9sYWJlbD5cclxuXHQgKiAgIDxsYWJlbCBjbGFzcz1cIndwYmNfdWlfX3RvZ2dsZV9sYWJlbFwiIGZvcj1cInt1bmlxdWV9XCI+TGFiZWw8L2xhYmVsPlxyXG5cdCAqIDwvZGl2PlxyXG5cdCAqXHJcblx0ICogLSBTa2lwcyBpbnB1dHMgYWxyZWFkeSBpbnNpZGUgYC53cGJjX3VpX190b2dnbGVgLlxyXG5cdCAqIC0gUmV1c2VzIGFuIGV4aXN0aW5nIDxsYWJlbCBmb3I9XCIuLi5cIj4gdGV4dCBpZiBwcmVzZW50OyBvdGhlcndpc2UgZmFsbHMgYmFjayB0byBuZWFyYnkgbGFiZWxzIG9yIGF0dHJpYnV0ZXMuXHJcblx0ICogLSBBdXRvLWdlbmVyYXRlcyBhIHVuaXF1ZSBpZCB3aGVuIGFic2VudC5cclxuXHQgKi9cclxuXHRVSS5XUEJDX0JGQl9Ub2dnbGVfTm9ybWFsaXplciA9IGNsYXNzIHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFVwZ3JhZGUgYWxsIHJhdyBjaGVja2JveGVzIGluIGEgY29udGFpbmVyIHRvIHRvZ2dsZXMuXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSByb290X2VsXHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyB1cGdyYWRlX2NoZWNrYm94ZXNfaW4ocm9vdF9lbCkge1xyXG5cclxuXHRcdFx0aWYgKCAhcm9vdF9lbCB8fCAhcm9vdF9lbC5xdWVyeVNlbGVjdG9yQWxsICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIGlucHV0cyA9IHJvb3RfZWwucXVlcnlTZWxlY3RvckFsbCggJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScgKTtcclxuXHRcdFx0aWYgKCAhaW5wdXRzLmxlbmd0aCApIHtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdEFycmF5LnByb3RvdHlwZS5mb3JFYWNoLmNhbGwoIGlucHV0cywgZnVuY3Rpb24gKGlucHV0KSB7XHJcblxyXG5cdFx0XHRcdC8vIDEpIFNraXAgaWYgYWxyZWFkeSBpbnNpZGUgdG9nZ2xlIHdyYXBwZXIuXHJcblx0XHRcdFx0aWYgKCBpbnB1dC5jbG9zZXN0KCAnLndwYmNfdWlfX3RvZ2dsZScgKSApIHtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Ly8gU2tpcCByb3dzIC8gd2hlcmUgaW5wdXQgY2hlY2tib3ggZXhwbGljaXRseSBtYXJrZWQgd2l0aCAgYXR0cmlidXRlICdkYXRhLXdwYmMtdWktbm8tdG9nZ2xlJy5cclxuXHRcdFx0XHRpZiAoIGlucHV0Lmhhc0F0dHJpYnV0ZSggJ2RhdGEtd3BiYy11aS1uby10b2dnbGUnICkgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyAyKSBFbnN1cmUgdW5pcXVlIGlkOyBwcmVmZXIgZXhpc3RpbmcuXHJcblx0XHRcdFx0dmFyIGlucHV0X2lkID0gaW5wdXQuZ2V0QXR0cmlidXRlKCAnaWQnICk7XHJcblx0XHRcdFx0aWYgKCAhaW5wdXRfaWQgKSB7XHJcblx0XHRcdFx0XHR2YXIga2V5ICA9IChpbnB1dC5kYXRhc2V0ICYmIGlucHV0LmRhdGFzZXQuaW5zcGVjdG9yS2V5KSA/IFN0cmluZyggaW5wdXQuZGF0YXNldC5pbnNwZWN0b3JLZXkgKSA6ICdvcHQnO1xyXG5cdFx0XHRcdFx0aW5wdXRfaWQgPSBVSS5XUEJDX0JGQl9Ub2dnbGVfTm9ybWFsaXplci5nZW5lcmF0ZV91bmlxdWVfaWQoICd3cGJjX2luc19hdXRvXycgKyBrZXkgKyAnXycgKTtcclxuXHRcdFx0XHRcdGlucHV0LnNldEF0dHJpYnV0ZSggJ2lkJywgaW5wdXRfaWQgKTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIDMpIEZpbmQgYmVzdCBsYWJlbCB0ZXh0LlxyXG5cdFx0XHRcdHZhciBsYWJlbF90ZXh0ID0gVUkuV1BCQ19CRkJfVG9nZ2xlX05vcm1hbGl6ZXIucmVzb2x2ZV9sYWJlbF90ZXh0KCByb290X2VsLCBpbnB1dCwgaW5wdXRfaWQgKTtcclxuXHJcblx0XHRcdFx0Ly8gNCkgQnVpbGQgdGhlIHRvZ2dsZSB3cmFwcGVyLlxyXG5cdFx0XHRcdHZhciB3cmFwcGVyICAgICAgID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2RpdicgKTtcclxuXHRcdFx0XHR3cmFwcGVyLmNsYXNzTmFtZSA9ICdpbnNwZWN0b3JfX2NvbnRyb2wgd3BiY191aV9fdG9nZ2xlJztcclxuXHJcblx0XHRcdFx0Ly8gS2VlcCBvcmlnaW5hbCBpbnB1dDsganVzdCBtb3ZlIGl0IGludG8gd3JhcHBlci5cclxuXHRcdFx0XHRpbnB1dC5jbGFzc0xpc3QuYWRkKCAnaW5zcGVjdG9yX19pbnB1dCcgKTtcclxuXHRcdFx0XHRpbnB1dC5zZXRBdHRyaWJ1dGUoICdyb2xlJywgJ3N3aXRjaCcgKTtcclxuXHRcdFx0XHRpbnB1dC5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCBpbnB1dC5jaGVja2VkID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cclxuXHRcdFx0XHR2YXIgaWNvbl9sYWJlbCAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsYWJlbCcgKTtcclxuXHRcdFx0XHRpY29uX2xhYmVsLmNsYXNzTmFtZSA9ICd3cGJjX3VpX190b2dnbGVfaWNvbic7XHJcblx0XHRcdFx0aWNvbl9sYWJlbC5zZXRBdHRyaWJ1dGUoICdmb3InLCBpbnB1dF9pZCApO1xyXG5cclxuXHRcdFx0XHR2YXIgdGV4dF9sYWJlbCAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsYWJlbCcgKTtcclxuXHRcdFx0XHR0ZXh0X2xhYmVsLmNsYXNzTmFtZSA9ICd3cGJjX3VpX190b2dnbGVfbGFiZWwnO1xyXG5cdFx0XHRcdHRleHRfbGFiZWwuc2V0QXR0cmlidXRlKCAnZm9yJywgaW5wdXRfaWQgKTtcclxuXHRcdFx0XHR0ZXh0X2xhYmVsLmFwcGVuZENoaWxkKCBkb2N1bWVudC5jcmVhdGVUZXh0Tm9kZSggbGFiZWxfdGV4dCApICk7XHJcblxyXG5cdFx0XHRcdC8vIDUpIEluc2VydCB3cmFwcGVyIGludG8gRE9NIG5lYXIgdGhlIGlucHV0LlxyXG5cdFx0XHRcdC8vICAgIFByZWZlcnJlZDogcmVwbGFjZSB0aGUgb3JpZ2luYWwgbGFiZWxlZCByb3cgaWYgaXQgbWF0Y2hlcyB0eXBpY2FsIGluc3BlY3RvciBsYXlvdXQuXHJcblx0XHRcdFx0dmFyIHJlcGxhY2VkID0gVUkuV1BCQ19CRkJfVG9nZ2xlX05vcm1hbGl6ZXIudHJ5X3JlcGxhY2Vfa25vd25fcm93KCBpbnB1dCwgd3JhcHBlciwgbGFiZWxfdGV4dCApO1xyXG5cclxuXHRcdFx0XHRpZiAoICFyZXBsYWNlZCApIHtcclxuXHRcdFx0XHRcdGlmICggIWlucHV0LnBhcmVudE5vZGUgKSByZXR1cm47IC8vIE5FVyBndWFyZFxyXG5cdFx0XHRcdFx0Ly8gRmFsbGJhY2s6IGp1c3Qgd3JhcCB0aGUgaW5wdXQgaW4gcGxhY2UgYW5kIGFwcGVuZCBsYWJlbHMuXHJcblx0XHRcdFx0XHRpbnB1dC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSggd3JhcHBlciwgaW5wdXQgKTtcclxuXHRcdFx0XHRcdHdyYXBwZXIuYXBwZW5kQ2hpbGQoIGlucHV0ICk7XHJcblx0XHRcdFx0XHR3cmFwcGVyLmFwcGVuZENoaWxkKCBpY29uX2xhYmVsICk7XHJcblx0XHRcdFx0XHR3cmFwcGVyLmFwcGVuZENoaWxkKCB0ZXh0X2xhYmVsICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyA2KSBBUklBIHN5bmMgb24gY2hhbmdlLlxyXG5cdFx0XHRcdGlucHV0LmFkZEV2ZW50TGlzdGVuZXIoICdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0XHRpbnB1dC5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCBpbnB1dC5jaGVja2VkID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogR2VuZXJhdGUgYSB1bmlxdWUgaWQgd2l0aCBhIGdpdmVuIHByZWZpeC5cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBwcmVmaXhcclxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XHJcblx0XHQgKi9cclxuXHRcdHN0YXRpYyBnZW5lcmF0ZV91bmlxdWVfaWQocHJlZml4KSB7XHJcblx0XHRcdHZhciBiYXNlID0gU3RyaW5nKCBwcmVmaXggfHwgJ3dwYmNfaW5zX2F1dG9fJyApO1xyXG5cdFx0XHR2YXIgdWlkICA9IE1hdGgucmFuZG9tKCkudG9TdHJpbmcoIDM2ICkuc2xpY2UoIDIsIDggKTtcclxuXHRcdFx0dmFyIGlkICAgPSBiYXNlICsgdWlkO1xyXG5cdFx0XHQvLyBNaW5pbWFsIGNvbGxpc2lvbiBndWFyZCBpbiB0aGUgY3VycmVudCBkb2N1bWVudCBzY29wZS5cclxuXHRcdFx0d2hpbGUgKCBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggaWQgKSApIHtcclxuXHRcdFx0XHR1aWQgPSBNYXRoLnJhbmRvbSgpLnRvU3RyaW5nKCAzNiApLnNsaWNlKCAyLCA4ICk7XHJcblx0XHRcdFx0aWQgID0gYmFzZSArIHVpZDtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gaWQ7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZXNvbHZlIHRoZSBiZXN0IGh1bWFuIGxhYmVsIGZvciBhbiBpbnB1dC5cclxuXHRcdCAqIFByaW9yaXR5OlxyXG5cdFx0ICogIDEpIDxsYWJlbCBmb3I9XCJ7aWR9XCI+dGV4dDwvbGFiZWw+XHJcblx0XHQgKiAgMikgbmVhcmVzdCBzaWJsaW5nL3BhcmVudCAuaW5zcGVjdG9yX19sYWJlbCB0ZXh0XHJcblx0XHQgKiAgMykgaW5wdXQuZ2V0QXR0cmlidXRlKCdhcmlhLWxhYmVsJykgfHwgZGF0YS1sYWJlbCB8fCBkYXRhLWluc3BlY3Rvci1rZXkgfHwgbmFtZSB8fCAnT3B0aW9uJ1xyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm9vdF9lbFxyXG5cdFx0ICogQHBhcmFtIHtIVE1MSW5wdXRFbGVtZW50fSBpbnB1dFxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGlucHV0X2lkXHJcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxyXG5cdFx0ICovXHJcblx0XHRzdGF0aWMgcmVzb2x2ZV9sYWJlbF90ZXh0KHJvb3RfZWwsIGlucHV0LCBpbnB1dF9pZCkge1xyXG5cdFx0XHQvLyBmb3I9IGFzc29jaWF0aW9uXHJcblx0XHRcdGlmICggaW5wdXRfaWQgKSB7XHJcblx0XHRcdFx0dmFyIGFzc29jID0gcm9vdF9lbC5xdWVyeVNlbGVjdG9yKCAnbGFiZWxbZm9yPVwiJyArIFVJLldQQkNfQkZCX1RvZ2dsZV9Ob3JtYWxpemVyLmNzc19lc2NhcGUoIGlucHV0X2lkICkgKyAnXCJdJyApO1xyXG5cdFx0XHRcdGlmICggYXNzb2MgJiYgYXNzb2MudGV4dENvbnRlbnQgKSB7XHJcblx0XHRcdFx0XHR2YXIgdHh0ID0gYXNzb2MudGV4dENvbnRlbnQudHJpbSgpO1xyXG5cdFx0XHRcdFx0Ly8gUmVtb3ZlIHRoZSBvbGQgbGFiZWwgZnJvbSBET007IGl0cyB0ZXh0IHdpbGwgYmUgdXNlZCBieSB0b2dnbGUuXHJcblx0XHRcdFx0XHRhc3NvYy5wYXJlbnROb2RlICYmIGFzc29jLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoIGFzc29jICk7XHJcblx0XHRcdFx0XHRpZiAoIHR4dCApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIHR4dDtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIG5lYXJieSBpbnNwZWN0b3IgbGFiZWxcclxuXHRcdFx0dmFyIG5lYXJfbGFiZWwgPSBpbnB1dC5jbG9zZXN0KCAnLmluc3BlY3Rvcl9fcm93JyApO1xyXG5cdFx0XHRpZiAoIG5lYXJfbGFiZWwgKSB7XHJcblx0XHRcdFx0dmFyIGlsID0gbmVhcl9sYWJlbC5xdWVyeVNlbGVjdG9yKCAnLmluc3BlY3Rvcl9fbGFiZWwnICk7XHJcblx0XHRcdFx0aWYgKCBpbCAmJiBpbC50ZXh0Q29udGVudCApIHtcclxuXHRcdFx0XHRcdHZhciB0MiA9IGlsLnRleHRDb250ZW50LnRyaW0oKTtcclxuXHRcdFx0XHRcdC8vIElmIHRoaXMgcm93IGhhZCB0aGUgc3RhbmRhcmQgbGFiZWwrY29udHJvbCwgZHJvcCB0aGUgb2xkIHRleHQgbGFiZWwgdG8gYXZvaWQgZHVwbGljYXRlcy5cclxuXHRcdFx0XHRcdGlsLnBhcmVudE5vZGUgJiYgaWwucGFyZW50Tm9kZS5yZW1vdmVDaGlsZCggaWwgKTtcclxuXHRcdFx0XHRcdGlmICggdDIgKSB7XHJcblx0XHRcdFx0XHRcdHJldHVybiB0MjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIGZhbGxiYWNrc1xyXG5cdFx0XHR2YXIgYXJpYSA9IGlucHV0LmdldEF0dHJpYnV0ZSggJ2FyaWEtbGFiZWwnICk7XHJcblx0XHRcdGlmICggYXJpYSApIHtcclxuXHRcdFx0XHRyZXR1cm4gYXJpYTtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZiAoIGlucHV0LmRhdGFzZXQgJiYgaW5wdXQuZGF0YXNldC5sYWJlbCApIHtcclxuXHRcdFx0XHRyZXR1cm4gU3RyaW5nKCBpbnB1dC5kYXRhc2V0LmxhYmVsICk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBpbnB1dC5kYXRhc2V0ICYmIGlucHV0LmRhdGFzZXQuaW5zcGVjdG9yS2V5ICkge1xyXG5cdFx0XHRcdHJldHVybiBTdHJpbmcoIGlucHV0LmRhdGFzZXQuaW5zcGVjdG9yS2V5ICk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBpbnB1dC5uYW1lICkge1xyXG5cdFx0XHRcdHJldHVybiBTdHJpbmcoIGlucHV0Lm5hbWUgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gJ09wdGlvbic7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBUcnkgdG8gcmVwbGFjZSBhIGtub3duIGluc3BlY3RvciByb3cgcGF0dGVybiB3aXRoIGEgdG9nZ2xlIHdyYXBwZXIuXHJcblx0XHQgKiBQYXR0ZXJuczpcclxuXHRcdCAqICA8ZGl2Lmluc3BlY3Rvcl9fcm93PlxyXG5cdFx0ICogICAgPGxhYmVsLmluc3BlY3Rvcl9fbGFiZWw+VGV4dDwvbGFiZWw+XHJcblx0XHQgKiAgICA8ZGl2Lmluc3BlY3Rvcl9fY29udHJvbD4gW2lucHV0W3R5cGU9Y2hlY2tib3hdXSA8L2Rpdj5cclxuXHRcdCAqICA8L2Rpdj5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge0hUTUxJbnB1dEVsZW1lbnR9IGlucHV0XHJcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSB3cmFwcGVyXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn0gcmVwbGFjZWRcclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIHRyeV9yZXBsYWNlX2tub3duX3JvdyhpbnB1dCwgd3JhcHBlciwgbGFiZWxfdGV4dCkge1xyXG5cdFx0XHR2YXIgcm93ICAgICAgID0gaW5wdXQuY2xvc2VzdCggJy5pbnNwZWN0b3JfX3JvdycgKTtcclxuXHRcdFx0dmFyIGN0cmxfd3JhcCA9IGlucHV0LnBhcmVudEVsZW1lbnQ7XHJcblxyXG5cdFx0XHRpZiAoIHJvdyAmJiBjdHJsX3dyYXAgJiYgY3RybF93cmFwLmNsYXNzTGlzdC5jb250YWlucyggJ2luc3BlY3Rvcl9fY29udHJvbCcgKSApIHtcclxuXHRcdFx0XHQvLyBDbGVhciBjb250cm9sIHdyYXAgYW5kIHJlaW5zZXJ0IHRvZ2dsZSBzdHJ1Y3R1cmUuXHJcblx0XHRcdFx0d2hpbGUgKCBjdHJsX3dyYXAuZmlyc3RDaGlsZCApIHtcclxuXHRcdFx0XHRcdGN0cmxfd3JhcC5yZW1vdmVDaGlsZCggY3RybF93cmFwLmZpcnN0Q2hpbGQgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0cm93LmNsYXNzTGlzdC5hZGQoICdpbnNwZWN0b3JfX3Jvdy0tdG9nZ2xlJyApO1xyXG5cclxuXHRcdFx0XHRjdHJsX3dyYXAuY2xhc3NMaXN0LmFkZCggJ3dwYmNfdWlfX3RvZ2dsZScgKTtcclxuXHRcdFx0XHRjdHJsX3dyYXAuYXBwZW5kQ2hpbGQoIGlucHV0ICk7XHJcblxyXG5cdFx0XHRcdHZhciBpbnB1dF9pZCAgICAgICA9IGlucHV0LmdldEF0dHJpYnV0ZSggJ2lkJyApO1xyXG5cdFx0XHRcdHZhciBpY29uX2xibCAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsYWJlbCcgKTtcclxuXHRcdFx0XHRpY29uX2xibC5jbGFzc05hbWUgPSAnd3BiY191aV9fdG9nZ2xlX2ljb24nO1xyXG5cdFx0XHRcdGljb25fbGJsLnNldEF0dHJpYnV0ZSggJ2ZvcicsIGlucHV0X2lkICk7XHJcblxyXG5cdFx0XHRcdHZhciB0ZXh0X2xibCAgICAgICA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsYWJlbCcgKTtcclxuXHRcdFx0XHR0ZXh0X2xibC5jbGFzc05hbWUgPSAnd3BiY191aV9fdG9nZ2xlX2xhYmVsJztcclxuXHRcdFx0XHR0ZXh0X2xibC5zZXRBdHRyaWJ1dGUoICdmb3InLCBpbnB1dF9pZCApO1xyXG5cdFx0XHRcdGlmICggbGFiZWxfdGV4dCApIHtcclxuXHRcdFx0XHRcdHRleHRfbGJsLmFwcGVuZENoaWxkKCBkb2N1bWVudC5jcmVhdGVUZXh0Tm9kZSggbGFiZWxfdGV4dCApICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdC8vIElmIHRoZSByb3cgcHJldmlvdXNseSBoYWQgYSAuaW5zcGVjdG9yX19sYWJlbCAod2UgcmVtb3ZlZCBpdCBpbiByZXNvbHZlX2xhYmVsX3RleHQpLFxyXG5cdFx0XHRcdC8vIHdlIGludGVudGlvbmFsbHkgZG8gTk9UIHJlY3JlYXRlIGl0OyB0aGUgdG9nZ2xlIHRleHQgbGFiZWwgYmVjb21lcyB0aGUgdmlzaWJsZSBvbmUuXHJcblx0XHRcdFx0Ly8gVGhlIHRleHQgY29udGVudCBpcyBhbHJlYWR5IHJlc29sdmVkIGluIHJlc29sdmVfbGFiZWxfdGV4dCgpIGFuZCBzZXQgYmVsb3cgYnkgY2FsbGVyLlxyXG5cclxuXHRcdFx0XHRjdHJsX3dyYXAuYXBwZW5kQ2hpbGQoIGljb25fbGJsICk7XHJcblx0XHRcdFx0Y3RybF93cmFwLmFwcGVuZENoaWxkKCB0ZXh0X2xibCApO1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBOb3QgYSBrbm93biBwYXR0ZXJuOyBjYWxsZXIgd2lsbCB3cmFwIGluIHBsYWNlLlxyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDU1MuZXNjYXBlIHBvbHlmaWxsIGZvciBzZWxlY3RvcnMuXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gc1xyXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cclxuXHRcdCAqL1xyXG5cdFx0c3RhdGljIGNzc19lc2NhcGUocykge1xyXG5cdFx0XHRzID0gU3RyaW5nKCBzICk7XHJcblx0XHRcdGlmICggd2luZG93LkNTUyAmJiB0eXBlb2Ygd2luZG93LkNTUy5lc2NhcGUgPT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHdpbmRvdy5DU1MuZXNjYXBlKCBzICk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIHMucmVwbGFjZSggLyhbXlxcdy1dKS9nLCAnXFxcXCQxJyApO1xyXG5cdFx0fVxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEFwcGx5IGFsbCBVSSBub3JtYWxpemVycy9lbmhhbmNlcnMgdG8gYSBjb250YWluZXIgKHBvc3QtcmVuZGVyKS5cclxuXHQgKiBLZWVwIHRoaXMgZmlsZSBzbWFsbCBhbmQgYWRkIG1vcmUgbm9ybWFsaXplcnMgbGF0ZXIgaW4gb25lIHBsYWNlLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm9vdFxyXG5cdCAqL1xyXG5cdFVJLmFwcGx5X3Bvc3RfcmVuZGVyID0gZnVuY3Rpb24gKHJvb3QpIHtcclxuXHRcdGlmICggIXJvb3QgKSB7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHRcdHRyeSB7XHJcblx0XHRcdFVJLldQQkNfQkZCX1ZhbHVlU2xpZGVyPy5pbml0X29uPy4oIHJvb3QgKTtcclxuXHRcdH0gY2F0Y2ggKCBlICkgeyAvKiBub29wICovXHJcblx0XHR9XHJcblx0XHR0cnkge1xyXG5cdFx0XHR2YXIgVCA9IFVJLldQQkNfQkZCX1RvZ2dsZV9Ob3JtYWxpemVyO1xyXG5cdFx0XHRpZiAoIFQgJiYgdHlwZW9mIFQudXBncmFkZV9jaGVja2JveGVzX2luID09PSAnZnVuY3Rpb24nICkge1xyXG5cdFx0XHRcdFQudXBncmFkZV9jaGVja2JveGVzX2luKCByb290ICk7XHJcblx0XHRcdH1cclxuXHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHR3Ll93cGJjPy5kZXY/LmVycm9yPy4oICdhcHBseV9wb3N0X3JlbmRlci50b2dnbGUnLCBlICk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gQWNjZXNzaWJpbGl0eToga2VlcCBhcmlhLWNoZWNrZWQgaW4gc3luYyBmb3IgYWxsIHRvZ2dsZXMgaW5zaWRlIHJvb3QuXHJcblx0XHR0cnkge1xyXG5cdFx0XHRyb290LnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY191aV9fdG9nZ2xlIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScgKS5mb3JFYWNoKCBmdW5jdGlvbiAoY2IpIHtcclxuXHRcdFx0XHRpZiAoIGNiLl9fd3BiY19hcmlhX2hvb2tlZCApIHtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Y2IuX193cGJjX2FyaWFfaG9va2VkID0gdHJ1ZTtcclxuXHRcdFx0XHRjYi5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCBjYi5jaGVja2VkID8gJ3RydWUnIDogJ2ZhbHNlJyApO1xyXG5cdFx0XHRcdC8vIERlbGVnYXRlIOKAmGNoYW5nZeKAmSBqdXN0IG9uY2UgcGVyIHJlbmRlciDigJMgbmF0aXZlIGRlbGVnYXRpb24gc3RpbGwgd29ya3MgZmluZSBmb3IgeW91ciBsb2dpYy5cclxuXHRcdFx0XHRjYi5hZGRFdmVudExpc3RlbmVyKCAnY2hhbmdlJywgKCkgPT4ge1xyXG5cdFx0XHRcdFx0Y2Iuc2V0QXR0cmlidXRlKCAnYXJpYS1jaGVja2VkJywgY2IuY2hlY2tlZCA/ICd0cnVlJyA6ICdmYWxzZScgKTtcclxuXHRcdFx0XHR9LCB7IHBhc3NpdmU6IHRydWUgfSApO1xyXG5cdFx0XHR9ICk7XHJcblx0XHR9IGNhdGNoICggZSApIHtcclxuXHRcdFx0dy5fd3BiYz8uZGV2Py5lcnJvcj8uKCAnYXBwbHlfcG9zdF9yZW5kZXIuYXJpYScsIGUgKTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHRVSS5JbnNwZWN0b3JFbmhhbmNlcnMgPSBVSS5JbnNwZWN0b3JFbmhhbmNlcnMgfHwgKGZ1bmN0aW9uICgpIHtcclxuXHRcdHZhciByZWdzID0gW107XHJcblxyXG5cdFx0ZnVuY3Rpb24gcmVnaXN0ZXIobmFtZSwgc2VsZWN0b3IsIGluaXQsIGRlc3Ryb3kpIHtcclxuXHRcdFx0cmVncy5wdXNoKCB7IG5hbWUsIHNlbGVjdG9yLCBpbml0LCBkZXN0cm95IH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHRmdW5jdGlvbiBzY2FuKHJvb3QpIHtcclxuXHRcdFx0aWYgKCAhcm9vdCApIHJldHVybjtcclxuXHRcdFx0cmVncy5mb3JFYWNoKCBmdW5jdGlvbiAocikge1xyXG5cdFx0XHRcdHJvb3QucXVlcnlTZWxlY3RvckFsbCggci5zZWxlY3RvciApLmZvckVhY2goIGZ1bmN0aW9uIChub2RlKSB7XHJcblx0XHRcdFx0XHRub2RlLl9fd3BiY19laCA9IG5vZGUuX193cGJjX2VoIHx8IHt9O1xyXG5cdFx0XHRcdFx0aWYgKCBub2RlLl9fd3BiY19laFtyLm5hbWVdICkgcmV0dXJuO1xyXG5cdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0ci5pbml0ICYmIHIuaW5pdCggbm9kZSwgcm9vdCApO1xyXG5cdFx0XHRcdFx0XHRub2RlLl9fd3BiY19laFtyLm5hbWVdID0gdHJ1ZTtcclxuXHRcdFx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHJcblx0XHRmdW5jdGlvbiBkZXN0cm95KHJvb3QpIHtcclxuXHRcdFx0aWYgKCAhcm9vdCApIHJldHVybjtcclxuXHRcdFx0cmVncy5mb3JFYWNoKCBmdW5jdGlvbiAocikge1xyXG5cdFx0XHRcdHJvb3QucXVlcnlTZWxlY3RvckFsbCggci5zZWxlY3RvciApLmZvckVhY2goIGZ1bmN0aW9uIChub2RlKSB7XHJcblx0XHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0XHRyLmRlc3Ryb3kgJiYgci5kZXN0cm95KCBub2RlLCByb290ICk7XHJcblx0XHRcdFx0XHR9IGNhdGNoICggX2UgKSB7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIG5vZGUuX193cGJjX2VoICkgZGVsZXRlIG5vZGUuX193cGJjX2VoW3IubmFtZV07XHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHR9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHsgcmVnaXN0ZXIsIHNjYW4sIGRlc3Ryb3kgfTtcclxuXHR9KSgpO1xyXG5cclxuXHRVSS5XUEJDX0JGQl9WYWx1ZVNsaWRlciA9IHtcclxuXHRcdGluaXRfb24ocm9vdCkge1xyXG5cdFx0XHR2YXIgZ3JvdXBzID0gKHJvb3Qubm9kZVR5cGUgPT09IDEgPyBbIHJvb3QgXSA6IFtdKS5jb25jYXQoIFtdLnNsaWNlLmNhbGwoIHJvb3QucXVlcnlTZWxlY3RvckFsbD8uKCAnW2RhdGEtbGVuLWdyb3VwXScgKSB8fCBbXSApICk7XHJcblx0XHRcdGdyb3Vwcy5mb3JFYWNoKCBmdW5jdGlvbiAoZykge1xyXG5cdFx0XHRcdGlmICggIWcubWF0Y2hlcyB8fCAhZy5tYXRjaGVzKCAnW2RhdGEtbGVuLWdyb3VwXScgKSApIHJldHVybjtcclxuXHRcdFx0XHRpZiAoIGcuX193cGJjX2xlbl93aXJlZCApIHJldHVybjtcclxuXHJcblx0XHRcdFx0dmFyIG51bWJlciA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWxlbi12YWx1ZV0nICk7XHJcblx0XHRcdFx0dmFyIHJhbmdlICA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWxlbi1yYW5nZV0nICk7XHJcblx0XHRcdFx0dmFyIHVuaXQgICA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWxlbi11bml0XScgKTtcclxuXHJcblx0XHRcdFx0aWYgKCAhbnVtYmVyIHx8ICFyYW5nZSApIHJldHVybjtcclxuXHJcblx0XHRcdFx0Ly8gTWlycm9yIGNvbnN0cmFpbnRzIGlmIG1pc3Npbmcgb24gdGhlIHJhbmdlLlxyXG5cdFx0XHRcdFsgJ21pbicsICdtYXgnLCAnc3RlcCcgXS5mb3JFYWNoKCBmdW5jdGlvbiAoYSkge1xyXG5cdFx0XHRcdFx0aWYgKCAhcmFuZ2UuaGFzQXR0cmlidXRlKCBhICkgJiYgbnVtYmVyLmhhc0F0dHJpYnV0ZSggYSApICkge1xyXG5cdFx0XHRcdFx0XHRyYW5nZS5zZXRBdHRyaWJ1dGUoIGEsIG51bWJlci5nZXRBdHRyaWJ1dGUoIGEgKSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gKTtcclxuXHJcblxyXG5cdFx0XHRcdGZ1bmN0aW9uIHN5bmNfcmFuZ2VfZnJvbV9udW1iZXIoKSB7XHJcblx0XHRcdFx0XHRpZiAoIHJhbmdlLnZhbHVlICE9PSBudW1iZXIudmFsdWUgKSB7XHJcblx0XHRcdFx0XHRcdHJhbmdlLnZhbHVlID0gbnVtYmVyLnZhbHVlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0ZnVuY3Rpb24gZGlzcGF0Y2hfaW5wdXQoZWwpIHtcclxuXHRcdFx0XHRcdHRyeSB7IGVsLmRpc3BhdGNoRXZlbnQoIG5ldyBFdmVudCggJ2lucHV0JywgeyBidWJibGVzOiB0cnVlIH0gKSApOyB9IGNhdGNoICggX2UgKSB7fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRmdW5jdGlvbiBkaXNwYXRjaF9jaGFuZ2UoZWwpIHtcclxuXHRcdFx0XHRcdHRyeSB7IGVsLmRpc3BhdGNoRXZlbnQoIG5ldyBFdmVudCggJ2NoYW5nZScsIHsgYnViYmxlczogdHJ1ZSB9ICkgKTsgfSBjYXRjaCAoIF9lICkge31cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIFRocm90dGxlIHJhbmdlLT5udW1iZXIgc3luY2luZyAodGltZS1iYXNlZCkuXHJcblx0XHRcdFx0dmFyIHRpbWVyX2lkICAgICAgID0gMDtcclxuXHRcdFx0XHR2YXIgcGVuZGluZ192YWwgICAgPSBudWxsO1xyXG5cdFx0XHRcdHZhciBwZW5kaW5nX2NoYW5nZSA9IGZhbHNlO1xyXG5cdFx0XHRcdHZhciBsYXN0X2ZsdXNoX3RzICA9IDA7XHJcblxyXG5cdFx0XHRcdC8vIENoYW5nZSB0aGlzIHRvIHR1bmUgc3BlZWQ6IDUwLi4xMjAgbXMgaXMgYSBnb29kIHJhbmdlLlxyXG5cdFx0XHRcdHZhciBtaW5faW50ZXJ2YWxfbXMgPSBwYXJzZUludCggZy5kYXRhc2V0LmxlblRocm90dGxlIHx8IFVJLlZBTFVFX1NMSURFUl9USFJPVFRMRV9NUywgMTAgKTtcclxuXHRcdFx0XHRtaW5faW50ZXJ2YWxfbXMgPSBOdW1iZXIuaXNGaW5pdGUoIG1pbl9pbnRlcnZhbF9tcyApID8gTWF0aC5tYXgoIDAsIG1pbl9pbnRlcnZhbF9tcyApIDogMTIwO1xyXG5cclxuXHRcdFx0XHRmdW5jdGlvbiBmbHVzaF9yYW5nZV90b19udW1iZXIoKSB7XHJcblx0XHRcdFx0XHR0aW1lcl9pZCA9IDA7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCBwZW5kaW5nX3ZhbCA9PSBudWxsICkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0dmFyIG5leHQgICAgPSBTdHJpbmcoIHBlbmRpbmdfdmFsICk7XHJcblx0XHRcdFx0XHRwZW5kaW5nX3ZhbCA9IG51bGw7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCBudW1iZXIudmFsdWUgIT09IG5leHQgKSB7XHJcblx0XHRcdFx0XHRcdG51bWJlci52YWx1ZSA9IG5leHQ7XHJcblx0XHRcdFx0XHRcdC8vIElNUE9SVEFOVDogb25seSAnaW5wdXQnIHdoaWxlIGRyYWdnaW5nLlxyXG5cdFx0XHRcdFx0XHRkaXNwYXRjaF9pbnB1dCggbnVtYmVyICk7XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0aWYgKCBwZW5kaW5nX2NoYW5nZSApIHtcclxuXHRcdFx0XHRcdFx0cGVuZGluZ19jaGFuZ2UgPSBmYWxzZTtcclxuXHRcdFx0XHRcdFx0ZGlzcGF0Y2hfY2hhbmdlKCBudW1iZXIgKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRsYXN0X2ZsdXNoX3RzID0gRGF0ZS5ub3coKTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGZ1bmN0aW9uIHNjaGVkdWxlX3JhbmdlX3RvX251bWJlcih2YWwsIGVtaXRfY2hhbmdlKSB7XHJcblx0XHRcdFx0XHRwZW5kaW5nX3ZhbCA9IHZhbDtcclxuXHRcdFx0XHRcdGlmICggZW1pdF9jaGFuZ2UgKSB7XHJcblx0XHRcdFx0XHRcdHBlbmRpbmdfY2hhbmdlID0gdHJ1ZTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBJZiBjb21taXQgcmVxdWVzdGVkLCBmbHVzaCBpbW1lZGlhdGVseS5cclxuXHRcdFx0XHRcdGlmICggcGVuZGluZ19jaGFuZ2UgKSB7XHJcblx0XHRcdFx0XHRcdGlmICggdGltZXJfaWQgKSB7XHJcblx0XHRcdFx0XHRcdFx0Y2xlYXJUaW1lb3V0KCB0aW1lcl9pZCApO1xyXG5cdFx0XHRcdFx0XHRcdHRpbWVyX2lkID0gMDtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRmbHVzaF9yYW5nZV90b19udW1iZXIoKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdHZhciBub3cgICA9IERhdGUubm93KCk7XHJcblx0XHRcdFx0XHR2YXIgZGVsdGEgPSBub3cgLSBsYXN0X2ZsdXNoX3RzO1xyXG5cclxuXHRcdFx0XHRcdC8vIElmIGVub3VnaCB0aW1lIHBhc3NlZCwgZmx1c2ggaW1tZWRpYXRlbHk7IGVsc2Ugc2NoZWR1bGUuXHJcblx0XHRcdFx0XHRpZiAoIGRlbHRhID49IG1pbl9pbnRlcnZhbF9tcyApIHtcclxuXHRcdFx0XHRcdFx0Zmx1c2hfcmFuZ2VfdG9fbnVtYmVyKCk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoIHRpbWVyX2lkICkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0dGltZXJfaWQgPSBzZXRUaW1lb3V0KCBmbHVzaF9yYW5nZV90b19udW1iZXIsIE1hdGgubWF4KCAwLCBtaW5faW50ZXJ2YWxfbXMgLSBkZWx0YSApICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRmdW5jdGlvbiBvbl9udW1iZXJfaW5wdXQoKSB7XHJcblx0XHRcdFx0XHRzeW5jX3JhbmdlX2Zyb21fbnVtYmVyKCk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRmdW5jdGlvbiBvbl9udW1iZXJfY2hhbmdlKCkge1xyXG5cdFx0XHRcdFx0c3luY19yYW5nZV9mcm9tX251bWJlcigpO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0ZnVuY3Rpb24gb25fcmFuZ2VfaW5wdXQoKSB7XHJcblx0XHRcdFx0XHRzY2hlZHVsZV9yYW5nZV90b19udW1iZXIoIHJhbmdlLnZhbHVlLCBmYWxzZSApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0ZnVuY3Rpb24gb25fcmFuZ2VfY2hhbmdlKCkge1xyXG5cdFx0XHRcdFx0c2NoZWR1bGVfcmFuZ2VfdG9fbnVtYmVyKCByYW5nZS52YWx1ZSwgdHJ1ZSApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0bnVtYmVyLmFkZEV2ZW50TGlzdGVuZXIoICdpbnB1dCcsICBvbl9udW1iZXJfaW5wdXQgKTtcclxuXHRcdFx0XHRudW1iZXIuYWRkRXZlbnRMaXN0ZW5lciggJ2NoYW5nZScsIG9uX251bWJlcl9jaGFuZ2UgKTtcclxuXHRcdFx0XHRyYW5nZS5hZGRFdmVudExpc3RlbmVyKCAnaW5wdXQnLCAgb25fcmFuZ2VfaW5wdXQgKTtcclxuXHRcdFx0XHRyYW5nZS5hZGRFdmVudExpc3RlbmVyKCAnY2hhbmdlJywgb25fcmFuZ2VfY2hhbmdlICk7XHJcblxyXG5cdFx0XHRcdGlmICggdW5pdCApIHtcclxuXHRcdFx0XHRcdHVuaXQuYWRkRXZlbnRMaXN0ZW5lciggJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0Ly8gV2UganVzdCBudWRnZSB0aGUgbnVtYmVyIHNvIHVwc3RyZWFtIGhhbmRsZXJzIHJlLXJ1bi5cclxuXHRcdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0XHRudW1iZXIuZGlzcGF0Y2hFdmVudCggbmV3IEV2ZW50KCAnaW5wdXQnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0XHRcdH0gY2F0Y2ggKCBfZSApIHtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gSW5pdGlhbCBzeW5jXHJcblx0XHRcdFx0c3luY19yYW5nZV9mcm9tX251bWJlcigpO1xyXG5cclxuXHRcdFx0XHRnLl9fd3BiY19sZW5fd2lyZWQgPSB7XHJcblx0XHRcdFx0XHRkZXN0cm95KCkge1xyXG5cdFx0XHRcdFx0XHRudW1iZXIucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ2lucHV0JywgIG9uX251bWJlcl9pbnB1dCApO1xyXG5cdFx0XHRcdFx0XHRudW1iZXIucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ2NoYW5nZScsIG9uX251bWJlcl9jaGFuZ2UgKTtcclxuXHRcdFx0XHRcdFx0cmFuZ2UucmVtb3ZlRXZlbnRMaXN0ZW5lciggJ2lucHV0JywgIG9uX3JhbmdlX2lucHV0ICk7XHJcblx0XHRcdFx0XHRcdHJhbmdlLnJlbW92ZUV2ZW50TGlzdGVuZXIoICdjaGFuZ2UnLCBvbl9yYW5nZV9jaGFuZ2UgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9O1xyXG5cdFx0XHR9ICk7XHJcblx0XHR9LFxyXG5cdFx0ZGVzdHJveV9vbihyb290KSB7XHJcblx0XHRcdHZhciBncm91cHMgPSAocm9vdCAmJiByb290Lm5vZGVUeXBlID09PSAxID8gWyByb290IF0gOiBbXSkuY29uY2F0KFxyXG5cdFx0XHRcdFtdLnNsaWNlLmNhbGwoIHJvb3QucXVlcnlTZWxlY3RvckFsbD8uKCAnW2RhdGEtbGVuLWdyb3VwXScgKSB8fCBbXSApXHJcblx0XHRcdCk7XHJcblx0XHRcdGdyb3Vwcy5mb3JFYWNoKCBmdW5jdGlvbiAoZykge1xyXG5cdFx0XHRcdGlmICggIWcubWF0Y2hlcyB8fCAhZy5tYXRjaGVzKCAnW2RhdGEtbGVuLWdyb3VwXScgKSApIHJldHVybjtcclxuXHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0Zy5fX3dwYmNfbGVuX3dpcmVkICYmIGcuX193cGJjX2xlbl93aXJlZC5kZXN0cm95ICYmIGcuX193cGJjX2xlbl93aXJlZC5kZXN0cm95KCk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRkZWxldGUgZy5fX3dwYmNfbGVuX3dpcmVkO1xyXG5cdFx0XHR9ICk7XHJcblx0XHR9XHJcblx0fTtcclxuXHJcblx0Ly8gUmVnaXN0ZXIgd2l0aCB0aGUgZ2xvYmFsIGVuaGFuY2VycyBodWIuXHJcblx0VUkuSW5zcGVjdG9yRW5oYW5jZXJzICYmIFVJLkluc3BlY3RvckVuaGFuY2Vycy5yZWdpc3RlcihcclxuXHRcdCd2YWx1ZS1zbGlkZXInLFxyXG5cdFx0J1tkYXRhLWxlbi1ncm91cF0nLFxyXG5cdFx0ZnVuY3Rpb24gKGVsLCBfcm9vdCkge1xyXG5cdFx0XHRVSS5XUEJDX0JGQl9WYWx1ZVNsaWRlci5pbml0X29uKCBlbCApO1xyXG5cdFx0fSxcclxuXHRcdGZ1bmN0aW9uIChlbCwgX3Jvb3QpIHtcclxuXHRcdFx0VUkuV1BCQ19CRkJfVmFsdWVTbGlkZXIuZGVzdHJveV9vbiggZWwgKTtcclxuXHRcdH1cclxuXHQpO1xyXG5cclxuXHQvLyBTaW5nbGUsIGxvYWQtb3JkZXItc2FmZSBwYXRjaCBzbyBlbmhhbmNlcnMgYXV0by1ydW4gb24gZXZlcnkgYmluZC5cclxuXHQoZnVuY3Rpb24gcGF0Y2hJbnNwZWN0b3JFbmhhbmNlcnMoKSB7XHJcblx0XHRmdW5jdGlvbiBhcHBseVBhdGNoKCkge1xyXG5cdFx0XHR2YXIgSW5zcGVjdG9yID0gdy5XUEJDX0JGQl9JbnNwZWN0b3I7XHJcblx0XHRcdGlmICggIUluc3BlY3RvciB8fCBJbnNwZWN0b3IuX193cGJjX2VuaGFuY2Vyc19wYXRjaGVkICkgcmV0dXJuIGZhbHNlO1xyXG5cdFx0XHRJbnNwZWN0b3IuX193cGJjX2VuaGFuY2Vyc19wYXRjaGVkID0gdHJ1ZTtcclxuXHRcdFx0dmFyIG9yaWcgICAgICAgICAgICAgICAgICAgICAgICAgICA9IEluc3BlY3Rvci5wcm90b3R5cGUuYmluZF90b19maWVsZDtcclxuXHRcdFx0SW5zcGVjdG9yLnByb3RvdHlwZS5iaW5kX3RvX2ZpZWxkICA9IGZ1bmN0aW9uIChlbCkge1xyXG5cdFx0XHRcdG9yaWcuY2FsbCggdGhpcywgZWwgKTtcclxuXHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0dmFyIGlucyA9IHRoaXMucGFuZWxcclxuXHRcdFx0XHRcdFx0fHwgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICd3cGJjX2JmYl9faW5zcGVjdG9yJyApXHJcblx0XHRcdFx0XHRcdHx8IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcud3BiY19iZmJfX2luc3BlY3RvcicgKTtcclxuXHRcdFx0XHRcdFVJLkluc3BlY3RvckVuaGFuY2VycyAmJiBVSS5JbnNwZWN0b3JFbmhhbmNlcnMuc2NhbiggaW5zICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fTtcclxuXHRcdFx0Ly8gSW5pdGlhbCBzY2FuIGlmIHRoZSBET00gaXMgYWxyZWFkeSBwcmVzZW50LlxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdHZhciBpbnNFbCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnd3BiY19iZmJfX2luc3BlY3RvcicgKVxyXG5cdFx0XHRcdFx0fHwgZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9faW5zcGVjdG9yJyApO1xyXG5cdFx0XHRcdFVJLkluc3BlY3RvckVuaGFuY2VycyAmJiBVSS5JbnNwZWN0b3JFbmhhbmNlcnMuc2NhbiggaW5zRWwgKTtcclxuXHRcdFx0fSBjYXRjaCAoIF9lICkge1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFRyeSBub3c7IGlmIEluc3BlY3RvciBpc27igJl0IGRlZmluZWQgeWV0LCBwYXRjaCB3aGVuIGl0IGJlY29tZXMgcmVhZHkuXHJcblx0XHRpZiAoICFhcHBseVBhdGNoKCkgKSB7XHJcblx0XHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXHJcblx0XHRcdFx0J3dwYmNfYmZiX2luc3BlY3Rvcl9yZWFkeScsXHJcblx0XHRcdFx0ZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0YXBwbHlQYXRjaCgpO1xyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0eyBvbmNlOiB0cnVlIH1cclxuXHRcdFx0KTtcclxuXHRcdH1cclxuXHR9KSgpO1xyXG5cclxufSggd2luZG93LCBkb2N1bWVudCApKTsiLCIvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gRmlsZSAgL2luY2x1ZGVzL3BhZ2UtZm9ybS1idWlsZGVyL19vdXQvY29yZS9iZmItaW5zcGVjdG9yLmpzID09IFRpbWUgcG9pbnQ6IDIwMjUtMDktMDYgMTQ6MDhcclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbihmdW5jdGlvbiAodykge1xyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0Ly8gMSkgQWN0aW9ucyByZWdpc3RyeS5cclxuXHJcblx0LyoqIEB0eXBlIHtSZWNvcmQ8c3RyaW5nLCAoY3R4OiBJbnNwZWN0b3JBY3Rpb25Db250ZXh0KSA9PiB2b2lkPn0gKi9cclxuXHRjb25zdCBfX0lOU1BFQ1RPUl9BQ1RJT05TX01BUF9fID0gT2JqZWN0LmNyZWF0ZSggbnVsbCApO1xyXG5cclxuXHQvLyBCdWlsdC1pbnMuXHJcblx0X19JTlNQRUNUT1JfQUNUSU9OU19NQVBfX1snZGVzZWxlY3QnXSA9ICh7IGJ1aWxkZXIgfSkgPT4ge1xyXG5cdFx0YnVpbGRlcj8uc2VsZWN0X2ZpZWxkPy4oIG51bGwgKTtcclxuXHR9O1xyXG5cclxuXHRfX0lOU1BFQ1RPUl9BQ1RJT05TX01BUF9fWydzY3JvbGx0byddID0gKHsgYnVpbGRlciwgZWwgfSkgPT4ge1xyXG5cdFx0aWYgKCAhZWwgfHwgIWRvY3VtZW50LmJvZHkuY29udGFpbnMoIGVsICkgKSByZXR1cm47XHJcblx0XHRidWlsZGVyPy5zZWxlY3RfZmllbGQ/LiggZWwsIHsgc2Nyb2xsSW50b1ZpZXc6IHRydWUgfSApO1xyXG5cdFx0ZWwuY2xhc3NMaXN0LmFkZCggJ3dwYmNfYmZiX19zY3JvbGwtcHVsc2UnICk7XHJcblx0XHRzZXRUaW1lb3V0KCAoKSA9PiBlbC5jbGFzc0xpc3QucmVtb3ZlKCAnd3BiY19iZmJfX3Njcm9sbC1wdWxzZScgKSwgNzAwICk7XHJcblx0fTtcclxuXHJcblx0X19JTlNQRUNUT1JfQUNUSU9OU19NQVBfX1snbW92ZS11cCddID0gKHsgYnVpbGRlciwgZWwgfSkgPT4ge1xyXG5cdFx0aWYgKCAhZWwgKSByZXR1cm47XHJcblx0XHRidWlsZGVyPy5tb3ZlX2l0ZW0/LiggZWwsICd1cCcgKTtcclxuXHRcdC8vIFNjcm9sbCBhZnRlciB0aGUgRE9NIGhhcyBzZXR0bGVkLlxyXG5cdFx0cmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IF9fSU5TUEVDVE9SX0FDVElPTlNfTUFQX19bJ3Njcm9sbHRvJ10oeyBidWlsZGVyLCBlbCB9KSk7XHJcblx0fTtcclxuXHJcblx0X19JTlNQRUNUT1JfQUNUSU9OU19NQVBfX1snbW92ZS1kb3duJ10gPSAoeyBidWlsZGVyLCBlbCB9KSA9PiB7XHJcblx0XHRpZiAoICFlbCApIHJldHVybjtcclxuXHRcdGJ1aWxkZXI/Lm1vdmVfaXRlbT8uKCBlbCwgJ2Rvd24nICk7XHJcblx0XHQvLyBTY3JvbGwgYWZ0ZXIgdGhlIERPTSBoYXMgc2V0dGxlZC5cclxuXHRcdHJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiBfX0lOU1BFQ1RPUl9BQ1RJT05TX01BUF9fWydzY3JvbGx0byddKHsgYnVpbGRlciwgZWwgfSkpO1xyXG5cdH07XHJcblxyXG5cdF9fSU5TUEVDVE9SX0FDVElPTlNfTUFQX19bJ2RlbGV0ZSddID0gKHsgYnVpbGRlciwgZWwsIGNvbmZpcm0gPSB3LmNvbmZpcm0gfSkgPT4ge1xyXG5cdFx0aWYgKCAhZWwgKSByZXR1cm47XHJcblx0XHRjb25zdCBpc19maWVsZCA9IGVsLmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX19maWVsZCcgKTtcclxuXHRcdGNvbnN0IGxhYmVsICAgID0gaXNfZmllbGRcclxuXHRcdFx0PyAoZWwucXVlcnlTZWxlY3RvciggJy53cGJjX2JmYl9fZmllbGQtbGFiZWwnICk/LnRleHRDb250ZW50IHx8IGVsLmRhdGFzZXQ/LmlkIHx8ICdmaWVsZCcpXHJcblx0XHRcdDogKGVsLmRhdGFzZXQ/LmlkIHx8ICdzZWN0aW9uJyk7XHJcblxyXG5cdFx0VUkuTW9kYWxfQ29uZmlybV9EZWxldGUub3BlbiggbGFiZWwsICgpID0+IHtcclxuXHRcdFx0Ly8gQ2VudHJhbCBjb21tYW5kIHdpbGwgcmVtb3ZlLCBlbWl0IGV2ZW50cywgYW5kIHJlc2VsZWN0IG5laWdoYm9yICh3aGljaCByZS1iaW5kcyBJbnNwZWN0b3IpLlxyXG5cdFx0XHRidWlsZGVyPy5kZWxldGVfaXRlbT8uKCBlbCApO1xyXG5cdFx0fSApO1xyXG5cclxuXHR9O1xyXG5cclxuXHRfX0lOU1BFQ1RPUl9BQ1RJT05TX01BUF9fWydkdXBsaWNhdGUnXSA9ICh7IGJ1aWxkZXIsIGVsIH0pID0+IHtcclxuXHRcdGlmICggIWVsICkgcmV0dXJuO1xyXG5cdFx0Y29uc3QgY2xvbmUgPSBidWlsZGVyPy5kdXBsaWNhdGVfaXRlbT8uKCBlbCApO1xyXG5cdFx0aWYgKCBjbG9uZSApIGJ1aWxkZXI/LnNlbGVjdF9maWVsZD8uKCBjbG9uZSwgeyBzY3JvbGxJbnRvVmlldzogdHJ1ZSB9ICk7XHJcblx0fTtcclxuXHJcblx0Ly8gUHVibGljIEFQSS5cclxuXHR3LldQQkNfQkZCX0luc3BlY3Rvcl9BY3Rpb25zID0ge1xyXG5cdFx0cnVuKG5hbWUsIGN0eCkge1xyXG5cdFx0XHRjb25zdCBmbiA9IF9fSU5TUEVDVE9SX0FDVElPTlNfTUFQX19bbmFtZV07XHJcblx0XHRcdGlmICggdHlwZW9mIGZuID09PSAnZnVuY3Rpb24nICkgZm4oIGN0eCApO1xyXG5cdFx0XHRlbHNlIGNvbnNvbGUud2FybiggJ1dQQkMuIEluc3BlY3RvciBhY3Rpb24gbm90IGZvdW5kOicsIG5hbWUgKTtcclxuXHRcdH0sXHJcblx0XHRyZWdpc3RlcihuYW1lLCBoYW5kbGVyKSB7XHJcblx0XHRcdGlmICggIW5hbWUgfHwgdHlwZW9mIGhhbmRsZXIgIT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCAncmVnaXN0ZXIobmFtZSwgaGFuZGxlcik6IGludmFsaWQgYXJndW1lbnRzJyApO1xyXG5cdFx0XHR9XHJcblx0XHRcdF9fSU5TUEVDVE9SX0FDVElPTlNfTUFQX19bbmFtZV0gPSBoYW5kbGVyO1xyXG5cdFx0fSxcclxuXHRcdGhhcyhuYW1lKSB7XHJcblx0XHRcdHJldHVybiB0eXBlb2YgX19JTlNQRUNUT1JfQUNUSU9OU19NQVBfX1tuYW1lXSA9PT0gJ2Z1bmN0aW9uJztcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvLyAyKSBJbnNwZWN0b3IgRmFjdG9yeS5cclxuXHJcblx0dmFyIFVJID0gKHcuV1BCQ19CRkJfQ29yZS5VSSA9IHcuV1BCQ19CRkJfQ29yZS5VSSB8fCB7fSk7XHJcblxyXG5cdC8vIEdsb2JhbCBIeWJyaWQrKyByZWdpc3RyaWVzIChrZWVwIHB1YmxpYykuXHJcblx0dy53cGJjX2JmYl9pbnNwZWN0b3JfZmFjdG9yeV9zbG90cyAgICAgID0gdy53cGJjX2JmYl9pbnNwZWN0b3JfZmFjdG9yeV9zbG90cyB8fCB7fTtcclxuXHR3LndwYmNfYmZiX2luc3BlY3Rvcl9mYWN0b3J5X3ZhbHVlX2Zyb20gPSB3LndwYmNfYmZiX2luc3BlY3Rvcl9mYWN0b3J5X3ZhbHVlX2Zyb20gfHwge307XHJcblxyXG5cdC8vIERlZmluZSBGYWN0b3J5IG9ubHkgaWYgbWlzc2luZyAobm8gZWFybHkgcmV0dXJuIGZvciB0aGUgd2hvbGUgYnVuZGxlKS5cclxuXHQvLyBhbHdheXMgZGVmaW5lL3JlcGxhY2UgRmFjdG9yeVxyXG5cdHtcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFV0aWxpdHk6IGNyZWF0ZSBlbGVtZW50IHdpdGggYXR0cmlidXRlcyBhbmQgY2hpbGRyZW4uXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHRhZ1xyXG5cdFx0ICogQHBhcmFtIHtPYmplY3Q9fSBhdHRyc1xyXG5cdFx0ICogQHBhcmFtIHsoTm9kZXxzdHJpbmd8QXJyYXk8Tm9kZXxzdHJpbmc+KT19IGNoaWxkcmVuXHJcblx0XHQgKiBAcmV0dXJucyB7SFRNTEVsZW1lbnR9XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIGVsKHRhZywgYXR0cnMsIGNoaWxkcmVuKSB7XHJcblx0XHRcdHZhciBub2RlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggdGFnICk7XHJcblx0XHRcdGlmICggYXR0cnMgKSB7XHJcblx0XHRcdFx0T2JqZWN0LmtleXMoIGF0dHJzICkuZm9yRWFjaCggZnVuY3Rpb24gKGspIHtcclxuXHRcdFx0XHRcdHZhciB2ID0gYXR0cnNba107XHJcblx0XHRcdFx0XHRpZiAoIHYgPT0gbnVsbCApIHJldHVybjtcclxuXHRcdFx0XHRcdGlmICggayA9PT0gJ2NsYXNzJyApIHtcclxuXHRcdFx0XHRcdFx0bm9kZS5jbGFzc05hbWUgPSB2O1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIGsgPT09ICdkYXRhc2V0JyApIHtcclxuXHRcdFx0XHRcdFx0T2JqZWN0LmtleXMoIHYgKS5mb3JFYWNoKCBmdW5jdGlvbiAoZGspIHtcclxuXHRcdFx0XHRcdFx0XHRub2RlLmRhdGFzZXRbZGtdID0gU3RyaW5nKCB2W2RrXSApO1xyXG5cdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGlmICggayA9PT0gJ2NoZWNrZWQnICYmIHR5cGVvZiB2ID09PSAnYm9vbGVhbicgKSB7XHJcblx0XHRcdFx0XHRcdGlmICggdiApIG5vZGUuc2V0QXR0cmlidXRlKCAnY2hlY2tlZCcsICdjaGVja2VkJyApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoIGsgPT09ICdkaXNhYmxlZCcgJiYgdHlwZW9mIHYgPT09ICdib29sZWFuJyApIHtcclxuXHRcdFx0XHRcdFx0aWYgKCB2ICkgbm9kZS5zZXRBdHRyaWJ1dGUoICdkaXNhYmxlZCcsICdkaXNhYmxlZCcgKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0Ly8gbm9ybWFsaXplIGJvb2xlYW4gYXR0cmlidXRlcyB0byBzdHJpbmdzLlxyXG5cdFx0XHRcdFx0aWYgKCB0eXBlb2YgdiA9PT0gJ2Jvb2xlYW4nICkge1xyXG5cdFx0XHRcdFx0XHRub2RlLnNldEF0dHJpYnV0ZSggaywgdiA/ICd0cnVlJyA6ICdmYWxzZScgKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0bm9kZS5zZXRBdHRyaWJ1dGUoIGssIFN0cmluZyggdiApICk7XHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggY2hpbGRyZW4gKSB7XHJcblx0XHRcdFx0KEFycmF5LmlzQXJyYXkoIGNoaWxkcmVuICkgPyBjaGlsZHJlbiA6IFsgY2hpbGRyZW4gXSkuZm9yRWFjaCggZnVuY3Rpb24gKGMpIHtcclxuXHRcdFx0XHRcdGlmICggYyA9PSBudWxsICkgcmV0dXJuO1xyXG5cdFx0XHRcdFx0bm9kZS5hcHBlbmRDaGlsZCggKHR5cGVvZiBjID09PSAnc3RyaW5nJykgPyBkb2N1bWVudC5jcmVhdGVUZXh0Tm9kZSggYyApIDogYyApO1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gbm9kZTtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEJ1aWxkIGEgdG9nZ2xlIGNvbnRyb2wgcm93IChjaGVja2JveCByZW5kZXJlZCBhcyB0b2dnbGUpLlxyXG5cdFx0ICpcclxuXHRcdCAqIFN0cnVjdHVyZTpcclxuXHRcdCAqIDxkaXYgY2xhc3M9XCJpbnNwZWN0b3JfX3JvdyBpbnNwZWN0b3JfX3Jvdy0tdG9nZ2xlXCI+XHJcblx0XHQgKiAgIDxkaXYgY2xhc3M9XCJpbnNwZWN0b3JfX2NvbnRyb2wgd3BiY191aV9fdG9nZ2xlXCI+XHJcblx0XHQgKiAgICAgPGlucHV0IHR5cGU9XCJjaGVja2JveFwiIGlkPVwiSURcIiBkYXRhLWluc3BlY3Rvci1rZXk9XCJLRVlcIiBjbGFzcz1cImluc3BlY3Rvcl9faW5wdXRcIiBjaGVja2VkPlxyXG5cdFx0ICogICAgIDxsYWJlbCBjbGFzcz1cIndwYmNfdWlfX3RvZ2dsZV9pY29uXCIgIGZvcj1cIklEXCI+PC9sYWJlbD5cclxuXHRcdCAqICAgICA8bGFiZWwgY2xhc3M9XCJ3cGJjX3VpX190b2dnbGVfbGFiZWxcIiBmb3I9XCJJRFwiPkxhYmVsIHRleHQ8L2xhYmVsPlxyXG5cdFx0ICogICA8L2Rpdj5cclxuXHRcdCAqIDwvZGl2PlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBpbnB1dF9pZFxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGtleVxyXG5cdFx0ICogQHBhcmFtIHtib29sZWFufSBjaGVja2VkXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gbGFiZWxfdGV4dFxyXG5cdFx0ICogQHJldHVybnMge0hUTUxFbGVtZW50fVxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBidWlsZF90b2dnbGVfcm93KCBpbnB1dF9pZCwga2V5LCBjaGVja2VkLCBsYWJlbF90ZXh0ICkge1xyXG5cclxuXHRcdFx0dmFyIHJvd19lbCAgICA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19yb3cgaW5zcGVjdG9yX19yb3ctLXRvZ2dsZScgfSApO1xyXG5cdFx0XHR2YXIgY3RybF93cmFwID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICdpbnNwZWN0b3JfX2NvbnRyb2wgd3BiY191aV9fdG9nZ2xlJyB9ICk7XHJcblxyXG5cdFx0XHR2YXIgaW5wdXRfZWwgPSBlbCggJ2lucHV0Jywge1xyXG5cdFx0XHRcdGlkICAgICAgICAgICAgICAgICAgOiBpbnB1dF9pZCxcclxuXHRcdFx0XHR0eXBlICAgICAgICAgICAgICAgIDogJ2NoZWNrYm94JyxcclxuXHRcdFx0XHQnZGF0YS1pbnNwZWN0b3Ita2V5Jzoga2V5LFxyXG5cdFx0XHRcdCdjbGFzcycgICAgICAgICAgICAgOiAnaW5zcGVjdG9yX19pbnB1dCcsXHJcblx0XHRcdFx0Y2hlY2tlZCAgICAgICAgICAgICA6ICEhY2hlY2tlZCxcclxuXHRcdFx0XHRyb2xlICAgICAgICAgICAgICAgIDogJ3N3aXRjaCcsXHJcblx0XHRcdFx0J2FyaWEtY2hlY2tlZCcgICAgICA6ICEhY2hlY2tlZFxyXG5cdFx0XHR9ICk7XHJcblx0XHRcdHZhciBpY29uX2xibCA9IGVsKCAnbGFiZWwnLCB7ICdjbGFzcyc6ICd3cGJjX3VpX190b2dnbGVfaWNvbicsICdmb3InOiBpbnB1dF9pZCB9ICk7XHJcblx0XHRcdHZhciB0ZXh0X2xibCA9IGVsKCAnbGFiZWwnLCB7ICdjbGFzcyc6ICd3cGJjX3VpX190b2dnbGVfbGFiZWwnLCAnZm9yJzogaW5wdXRfaWQgfSwgbGFiZWxfdGV4dCB8fCAnJyApO1xyXG5cclxuXHRcdFx0Y3RybF93cmFwLmFwcGVuZENoaWxkKCBpbnB1dF9lbCApO1xyXG5cdFx0XHRjdHJsX3dyYXAuYXBwZW5kQ2hpbGQoIGljb25fbGJsICk7XHJcblx0XHRcdGN0cmxfd3JhcC5hcHBlbmRDaGlsZCggdGV4dF9sYmwgKTtcclxuXHJcblx0XHRcdHJvd19lbC5hcHBlbmRDaGlsZCggY3RybF93cmFwICk7XHJcblx0XHRcdHJldHVybiByb3dfZWw7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0ICogVXRpbGl0eTogY2hvb3NlIGluaXRpYWwgdmFsdWUgZnJvbSBkYXRhIG9yIHNjaGVtYSBkZWZhdWx0LlxyXG5cdCAqL1xyXG5cdFx0ZnVuY3Rpb24gZ2V0X2luaXRpYWxfdmFsdWUoa2V5LCBkYXRhLCBwcm9wc19zY2hlbWEpIHtcclxuXHRcdFx0aWYgKCBkYXRhICYmIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggZGF0YSwga2V5ICkgKSByZXR1cm4gZGF0YVtrZXldO1xyXG5cdFx0XHR2YXIgbWV0YSA9IHByb3BzX3NjaGVtYSAmJiBwcm9wc19zY2hlbWFba2V5XTtcclxuXHRcdFx0cmV0dXJuIChtZXRhICYmIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggbWV0YSwgJ2RlZmF1bHQnICkpID8gbWV0YS5kZWZhdWx0IDogJyc7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0ICogVXRpbGl0eTogY29lcmNlIHZhbHVlIGJ5IHNjaGVtYSB0eXBlLlxyXG5cdCAqL1xyXG5cclxuXHJcblx0XHRmdW5jdGlvbiBjb2VyY2VfYnlfdHlwZSh2YWx1ZSwgdHlwZSkge1xyXG5cdFx0XHRzd2l0Y2ggKCB0eXBlICkge1xyXG5cdFx0XHRcdGNhc2UgJ251bWJlcic6XHJcblx0XHRcdFx0Y2FzZSAnaW50JzpcclxuXHRcdFx0XHRjYXNlICdmbG9hdCc6XHJcblx0XHRcdFx0XHRpZiAoIHZhbHVlID09PSAnJyB8fCB2YWx1ZSA9PSBudWxsICkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gJyc7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR2YXIgbiA9IE51bWJlciggdmFsdWUgKTtcclxuXHRcdFx0XHRcdHJldHVybiBpc05hTiggbiApID8gJycgOiBuO1xyXG5cdFx0XHRcdGNhc2UgJ2Jvb2xlYW4nOlxyXG5cdFx0XHRcdFx0cmV0dXJuICEhdmFsdWU7XHJcblx0XHRcdFx0Y2FzZSAnYXJyYXknOlxyXG5cdFx0XHRcdFx0cmV0dXJuIEFycmF5LmlzQXJyYXkoIHZhbHVlICkgPyB2YWx1ZSA6IFtdO1xyXG5cdFx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0XHRyZXR1cm4gKHZhbHVlID09IG51bGwpID8gJycgOiBTdHJpbmcoIHZhbHVlICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHQgKiBOb3JtYWxpemUgPHNlbGVjdD4gb3B0aW9ucyAoYXJyYXkgb2Yge3ZhbHVlLGxhYmVsfSBvciBtYXAge3ZhbHVlOmxhYmVsfSkuXHJcblx0ICovXHJcblx0XHRmdW5jdGlvbiBub3JtYWxpemVfc2VsZWN0X29wdGlvbnMob3B0aW9ucykge1xyXG5cdFx0XHRpZiAoIEFycmF5LmlzQXJyYXkoIG9wdGlvbnMgKSApIHtcclxuXHRcdFx0XHRyZXR1cm4gb3B0aW9ucy5tYXAoIGZ1bmN0aW9uIChvKSB7XHJcblx0XHRcdFx0XHRpZiAoIHR5cGVvZiBvID09PSAnb2JqZWN0JyAmJiBvICYmICd2YWx1ZScgaW4gbyApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIHsgdmFsdWU6IFN0cmluZyggby52YWx1ZSApLCBsYWJlbDogU3RyaW5nKCBvLmxhYmVsIHx8IG8udmFsdWUgKSB9O1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0cmV0dXJuIHsgdmFsdWU6IFN0cmluZyggbyApLCBsYWJlbDogU3RyaW5nKCBvICkgfTtcclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBvcHRpb25zICYmIHR5cGVvZiBvcHRpb25zID09PSAnb2JqZWN0JyApIHtcclxuXHRcdFx0XHRyZXR1cm4gT2JqZWN0LmtleXMoIG9wdGlvbnMgKS5tYXAoIGZ1bmN0aW9uIChrKSB7XHJcblx0XHRcdFx0XHRyZXR1cm4geyB2YWx1ZTogU3RyaW5nKCBrICksIGxhYmVsOiBTdHJpbmcoIG9wdGlvbnNba10gKSB9O1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gW107XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqIFBhcnNlIGEgQ1NTIGxlbmd0aCBsaWtlIFwiMTIwcHhcIiBvciBcIjgwJVwiIGludG8geyB2YWx1ZTpudW1iZXIsIHVuaXQ6c3RyaW5nIH0uICovXHJcblx0XHRmdW5jdGlvbiBwYXJzZV9sZW4odmFsdWUsIGZhbGxiYWNrX3VuaXQpIHtcclxuXHRcdFx0dmFsdWUgPSAodmFsdWUgPT0gbnVsbCkgPyAnJyA6IFN0cmluZyggdmFsdWUgKS50cmltKCk7XHJcblx0XHRcdHZhciBtID0gdmFsdWUubWF0Y2goIC9eKC0/XFxkKyg/OlxcLlxcZCspPykocHh8JXxyZW18ZW0pJC9pICk7XHJcblx0XHRcdGlmICggbSApIHtcclxuXHRcdFx0XHRyZXR1cm4geyB2YWx1ZTogcGFyc2VGbG9hdCggbVsxXSApLCB1bml0OiBtWzJdLnRvTG93ZXJDYXNlKCkgfTtcclxuXHRcdFx0fVxyXG5cdFx0XHQvLyBwbGFpbiBudW1iZXIgLT4gYXNzdW1lIGZhbGxiYWNrIHVuaXRcclxuXHRcdFx0aWYgKCB2YWx1ZSAhPT0gJycgJiYgIWlzTmFOKCBOdW1iZXIoIHZhbHVlICkgKSApIHtcclxuXHRcdFx0XHRyZXR1cm4geyB2YWx1ZTogTnVtYmVyKCB2YWx1ZSApLCB1bml0OiAoZmFsbGJhY2tfdW5pdCB8fCAncHgnKSB9O1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiB7IHZhbHVlOiAwLCB1bml0OiAoZmFsbGJhY2tfdW5pdCB8fCAncHgnKSB9O1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKiBDbGFtcCBoZWxwZXIuICovXHJcblx0XHRmdW5jdGlvbiBjbGFtcF9udW0odiwgbWluLCBtYXgpIHtcclxuXHRcdFx0aWYgKCB0eXBlb2YgdiAhPT0gJ251bWJlcicgfHwgaXNOYU4oIHYgKSApIHJldHVybiAobWluICE9IG51bGwgPyBtaW4gOiAwKTtcclxuXHRcdFx0aWYgKCBtaW4gIT0gbnVsbCAmJiB2IDwgbWluICkgdiA9IG1pbjtcclxuXHRcdFx0aWYgKCBtYXggIT0gbnVsbCAmJiB2ID4gbWF4ICkgdiA9IG1heDtcclxuXHRcdFx0cmV0dXJuIHY7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gSW5pdGlhbGl6ZSBDb2xvcmlzIHBpY2tlcnMgaW4gYSBnaXZlbiByb290LlxyXG5cdFx0Ly8gUmVsaWVzIG9uIENvbG9yaXMgYmVpbmcgZW5xdWV1ZWQgKHNlZSBiZmItYm9vdHN0cmFwLnBocCkuXHJcblx0XHRmdW5jdGlvbiBpbml0X2NvbG9yaXNfcGlja2Vycyhyb290KSB7XHJcblx0XHRcdGlmICggIXJvb3QgfHwgIXcuQ29sb3JpcyApIHJldHVybjtcclxuXHRcdFx0Ly8gTWFyayBpbnB1dHMgd2Ugd2FudCBDb2xvcmlzIHRvIGhhbmRsZS5cclxuXHRcdFx0dmFyIGlucHV0cyA9IHJvb3QucXVlcnlTZWxlY3RvckFsbCggJ2lucHV0W2RhdGEtaW5zcGVjdG9yLXR5cGU9XCJjb2xvclwiXScgKTtcclxuXHRcdFx0aWYgKCAhaW5wdXRzLmxlbmd0aCApIHJldHVybjtcclxuXHJcblx0XHRcdC8vIEFkZCBhIHN0YWJsZSBjbGFzcyBmb3IgQ29sb3JpcyB0YXJnZXRpbmc7IGF2b2lkIGRvdWJsZS1pbml0aWFsaXppbmcuXHJcblx0XHRcdGlucHV0cy5mb3JFYWNoKCBmdW5jdGlvbiAoaW5wdXQpIHtcclxuXHRcdFx0XHRpZiAoIGlucHV0LmNsYXNzTGlzdC5jb250YWlucyggJ3dwYmNfYmZiX2NvbG9yaXMnICkgKSByZXR1cm47XHJcblx0XHRcdFx0aW5wdXQuY2xhc3NMaXN0LmFkZCggJ3dwYmNfYmZiX2NvbG9yaXMnICk7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIENyZWF0ZS9yZWZyZXNoIGEgQ29sb3JpcyBpbnN0YW5jZSBib3VuZCB0byB0aGVzZSBpbnB1dHMuXHJcblx0XHRcdC8vIEtlZXAgSEVYIG91dHB1dCB0byBtYXRjaCBzY2hlbWEgZGVmYXVsdHMgKGUuZy4sIFwiI2UwZTBlMFwiKS5cclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHR3LkNvbG9yaXMoIHtcclxuXHRcdFx0XHRcdGVsICAgICAgIDogJy53cGJjX2JmYl9jb2xvcmlzJyxcclxuXHRcdFx0XHRcdGFscGhhICAgIDogZmFsc2UsXHJcblx0XHRcdFx0XHRmb3JtYXQgICA6ICdoZXgnLFxyXG5cdFx0XHRcdFx0dGhlbWVNb2RlOiAnYXV0bydcclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0Ly8gQ29sb3JpcyBhbHJlYWR5IGRpc3BhdGNoZXMgJ2lucHV0JyBldmVudHMgb24gdmFsdWUgY2hhbmdlcy5cclxuXHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0Ly8gTm9uLWZhdGFsOiBpZiBDb2xvcmlzIHRocm93cyAocmFyZSksIHRoZSB0ZXh0IGlucHV0IHN0aWxsIHdvcmtzLlxyXG5cdFx0XHRcdGNvbnNvbGUud2FybiggJ1dQQkMgSW5zcGVjdG9yOiBDb2xvcmlzIGluaXQgZmFpbGVkOicsIGUgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQnVpbGQ6IHNsaWRlciArIG51bWJlciBpbiBvbmUgcm93ICh3cml0ZXMgdG8gYSBzaW5nbGUgZGF0YSBrZXkpLlxyXG5cdFx0ICogQ29udHJvbCBtZXRhOiB7IHR5cGU6J3JhbmdlX251bWJlcicsIGtleSwgbGFiZWwsIG1pbiwgbWF4LCBzdGVwIH1cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gYnVpbGRfcmFuZ2VfbnVtYmVyX3JvdyhpbnB1dF9pZCwga2V5LCBsYWJlbF90ZXh0LCB2YWx1ZSwgbWV0YSkge1xyXG5cdFx0XHR2YXIgcm93X2VsICAgPSBlbCgnZGl2JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19yb3cnIH0pO1xyXG5cdFx0XHR2YXIgbGFiZWxfZWwgPSBlbCgnbGFiZWwnLCB7ICdmb3InOiBpbnB1dF9pZCwgJ2NsYXNzJzogJ2luc3BlY3Rvcl9fbGFiZWwnIH0sIGxhYmVsX3RleHQgfHwga2V5IHx8ICcnKTtcclxuXHRcdFx0dmFyIGN0cmwgICAgID0gZWwoJ2RpdicsIHsgJ2NsYXNzJzogJ2luc3BlY3Rvcl9fY29udHJvbCcgfSk7XHJcblxyXG5cdFx0XHR2YXIgbWluICA9IChtZXRhICYmIG1ldGEubWluICE9IG51bGwpICA/IG1ldGEubWluICA6IDA7XHJcblx0XHRcdHZhciBtYXggID0gKG1ldGEgJiYgbWV0YS5tYXggIT0gbnVsbCkgID8gbWV0YS5tYXggIDogMTAwO1xyXG5cdFx0XHR2YXIgc3RlcCA9IChtZXRhICYmIG1ldGEuc3RlcCAhPSBudWxsKSA/IG1ldGEuc3RlcCA6IDE7XHJcblxyXG5cdFx0XHR2YXIgZ3JvdXAgPSBlbCgnZGl2JywgeyAnY2xhc3MnOiAnd3BiY19sZW5fZ3JvdXAgd3BiY19pbmxpbmVfaW5wdXRzJywgJ2RhdGEtbGVuLWdyb3VwJzoga2V5IH0pO1xyXG5cclxuXHRcdFx0dmFyIHJhbmdlID0gZWwoJ2lucHV0Jywge1xyXG5cdFx0XHRcdHR5cGUgOiAncmFuZ2UnLFxyXG5cdFx0XHRcdCdjbGFzcyc6ICdpbnNwZWN0b3JfX2lucHV0JyxcclxuXHRcdFx0XHQnZGF0YS1sZW4tcmFuZ2UnOiAnJyxcclxuXHRcdFx0XHRtaW4gIDogU3RyaW5nKG1pbiksXHJcblx0XHRcdFx0bWF4ICA6IFN0cmluZyhtYXgpLFxyXG5cdFx0XHRcdHN0ZXAgOiBTdHJpbmcoc3RlcCksXHJcblx0XHRcdFx0dmFsdWU6IFN0cmluZyh2YWx1ZSA9PSBudWxsIHx8IHZhbHVlID09PSAnJyA/IG1pbiA6IHZhbHVlKVxyXG5cdFx0XHR9KTtcclxuXHJcblx0XHRcdHZhciBudW0gPSBlbCgnaW5wdXQnLCB7XHJcblx0XHRcdFx0aWQgICA6IGlucHV0X2lkLFxyXG5cdFx0XHRcdHR5cGUgOiAnbnVtYmVyJyxcclxuXHRcdFx0XHQnY2xhc3MnOiAnaW5zcGVjdG9yX19pbnB1dCBpbnNwZWN0b3JfX3dfMzAnLFxyXG5cdFx0XHRcdCdkYXRhLWxlbi12YWx1ZSc6ICcnLFxyXG5cdFx0XHRcdCdkYXRhLWluc3BlY3Rvci1rZXknOiBrZXksXHJcblx0XHRcdFx0bWluICA6IFN0cmluZyhtaW4pLFxyXG5cdFx0XHRcdG1heCAgOiBTdHJpbmcobWF4KSxcclxuXHRcdFx0XHRzdGVwIDogU3RyaW5nKHN0ZXApLFxyXG5cdFx0XHRcdHZhbHVlOiAodmFsdWUgPT0gbnVsbCB8fCB2YWx1ZSA9PT0gJycpID8gU3RyaW5nKG1pbikgOiBTdHJpbmcodmFsdWUpXHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdFx0Z3JvdXAuYXBwZW5kQ2hpbGQocmFuZ2UpO1xyXG5cdFx0XHRncm91cC5hcHBlbmRDaGlsZChudW0pO1xyXG5cdFx0XHRjdHJsLmFwcGVuZENoaWxkKGdyb3VwKTtcclxuXHRcdFx0cm93X2VsLmFwcGVuZENoaWxkKGxhYmVsX2VsKTtcclxuXHRcdFx0cm93X2VsLmFwcGVuZENoaWxkKGN0cmwpO1xyXG5cdFx0XHRyZXR1cm4gcm93X2VsO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQnVpbGQ6IChudW1iZXIgKyB1bml0KSArIHNsaWRlciwgd3JpdGluZyBhICpzaW5nbGUqIGNvbWJpbmVkIHN0cmluZyB0byBga2V5YC5cclxuXHRcdCAqIENvbnRyb2wgbWV0YTpcclxuXHRcdCAqIHtcclxuXHRcdCAqICAgdHlwZTonbGVuJywga2V5LCBsYWJlbCwgdW5pdHM6WydweCcsJyUnLCdyZW0nLCdlbSddLFxyXG5cdFx0ICogICBzbGlkZXI6IHsgcHg6e21pbjowLG1heDo1MTIsc3RlcDoxfSwgJyUnOnttaW46MCxtYXg6MTAwLHN0ZXA6MX0sIHJlbTp7bWluOjAsbWF4OjEwLHN0ZXA6MC4xfSwgZW06ey4uLn0gfSxcclxuXHRcdCAqICAgZmFsbGJhY2tfdW5pdDoncHgnXHJcblx0XHQgKiB9XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIGJ1aWxkX2xlbl9jb21wb3VuZF9yb3coY29udHJvbCwgcHJvcHNfc2NoZW1hLCBkYXRhLCB1aWQpIHtcclxuXHRcdFx0dmFyIGtleSAgICAgICAgPSBjb250cm9sLmtleTtcclxuXHRcdFx0dmFyIGxhYmVsX3RleHQgPSBjb250cm9sLmxhYmVsIHx8IGtleSB8fCAnJztcclxuXHRcdFx0dmFyIGRlZl9zdHIgICAgPSBnZXRfaW5pdGlhbF92YWx1ZSgga2V5LCBkYXRhLCBwcm9wc19zY2hlbWEgKTtcclxuXHRcdFx0dmFyIGZhbGxiYWNrX3UgPSBjb250cm9sLmZhbGxiYWNrX3VuaXQgfHwgJ3B4JztcclxuXHRcdFx0dmFyIHBhcnNlZCAgICAgPSBwYXJzZV9sZW4oIGRlZl9zdHIsIGZhbGxiYWNrX3UgKTtcclxuXHJcblx0XHRcdHZhciByb3cgICA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19yb3cnIH0gKTtcclxuXHRcdFx0dmFyIGxhYmVsID0gZWwoICdsYWJlbCcsIHsgJ2NsYXNzJzogJ2luc3BlY3Rvcl9fbGFiZWwnIH0sIGxhYmVsX3RleHQgKTtcclxuXHRcdFx0dmFyIGN0cmwgID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICdpbnNwZWN0b3JfX2NvbnRyb2wnIH0gKTtcclxuXHJcblx0XHRcdHZhciB1bml0cyAgICAgID0gQXJyYXkuaXNBcnJheSggY29udHJvbC51bml0cyApICYmIGNvbnRyb2wudW5pdHMubGVuZ3RoID8gY29udHJvbC51bml0cyA6IFsgJ3B4JywgJyUnLCAncmVtJywgJ2VtJyBdO1xyXG5cdFx0XHR2YXIgc2xpZGVyX21hcCA9IGNvbnRyb2wuc2xpZGVyIHx8IHtcclxuXHRcdFx0XHQncHgnIDogeyBtaW46IDAsIG1heDogNTEyLCBzdGVwOiAxIH0sXHJcblx0XHRcdFx0JyUnICA6IHsgbWluOiAwLCBtYXg6IDEwMCwgc3RlcDogMSB9LFxyXG5cdFx0XHRcdCdyZW0nOiB7IG1pbjogMCwgbWF4OiAxMCwgc3RlcDogMC4xIH0sXHJcblx0XHRcdFx0J2VtJyA6IHsgbWluOiAwLCBtYXg6IDEwLCBzdGVwOiAwLjEgfVxyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0Ly8gSG9zdCB3aXRoIGEgaGlkZGVuIGlucHV0IHRoYXQgY2FycmllcyBkYXRhLWluc3BlY3Rvci1rZXkgdG8gcmV1c2UgdGhlIHN0YW5kYXJkIGhhbmRsZXIuXHJcblx0XHRcdHZhciBncm91cCA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnd3BiY19sZW5fZ3JvdXAnLCAnZGF0YS1sZW4tZ3JvdXAnOiBrZXkgfSApO1xyXG5cclxuXHRcdFx0dmFyIGlubGluZSA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnd3BiY19pbmxpbmVfaW5wdXRzJyB9ICk7XHJcblxyXG5cdFx0XHR2YXIgbnVtID0gZWwoICdpbnB1dCcsIHtcclxuXHRcdFx0XHR0eXBlICAgICAgICAgICAgOiAnbnVtYmVyJyxcclxuXHRcdFx0XHQnY2xhc3MnICAgICAgICAgOiAnaW5zcGVjdG9yX19pbnB1dCcsXHJcblx0XHRcdFx0J2RhdGEtbGVuLXZhbHVlJzogJycsXHJcblx0XHRcdFx0bWluICAgICAgICAgICAgIDogJzAnLFxyXG5cdFx0XHRcdHN0ZXAgICAgICAgICAgICA6ICdhbnknLFxyXG5cdFx0XHRcdHZhbHVlICAgICAgICAgICA6IFN0cmluZyggcGFyc2VkLnZhbHVlIClcclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0dmFyIHNlbCA9IGVsKCAnc2VsZWN0JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19pbnB1dCcsICdkYXRhLWxlbi11bml0JzogJycgfSApO1xyXG5cdFx0XHR1bml0cy5mb3JFYWNoKCBmdW5jdGlvbiAodSkge1xyXG5cdFx0XHRcdHZhciBvcHQgPSBlbCggJ29wdGlvbicsIHsgdmFsdWU6IHUgfSwgdSApO1xyXG5cdFx0XHRcdGlmICggdSA9PT0gcGFyc2VkLnVuaXQgKSBvcHQuc2V0QXR0cmlidXRlKCAnc2VsZWN0ZWQnLCAnc2VsZWN0ZWQnICk7XHJcblx0XHRcdFx0c2VsLmFwcGVuZENoaWxkKCBvcHQgKTtcclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0aW5saW5lLmFwcGVuZENoaWxkKCBudW0gKTtcclxuXHRcdFx0aW5saW5lLmFwcGVuZENoaWxkKCBzZWwgKTtcclxuXHJcblx0XHRcdC8vIFNsaWRlciAodW5pdC1hd2FyZSlcclxuXHRcdFx0dmFyIGN1cnJlbnQgPSBzbGlkZXJfbWFwW3BhcnNlZC51bml0XSB8fCBzbGlkZXJfbWFwW3VuaXRzWzBdXTtcclxuXHRcdFx0dmFyIHJhbmdlICAgPSBlbCggJ2lucHV0Jywge1xyXG5cdFx0XHRcdHR5cGUgICAgICAgICAgICA6ICdyYW5nZScsXHJcblx0XHRcdFx0J2NsYXNzJyAgICAgICAgIDogJ2luc3BlY3Rvcl9faW5wdXQnLFxyXG5cdFx0XHRcdCdkYXRhLWxlbi1yYW5nZSc6ICcnLFxyXG5cdFx0XHRcdG1pbiAgICAgICAgICAgICA6IFN0cmluZyggY3VycmVudC5taW4gKSxcclxuXHRcdFx0XHRtYXggICAgICAgICAgICAgOiBTdHJpbmcoIGN1cnJlbnQubWF4ICksXHJcblx0XHRcdFx0c3RlcCAgICAgICAgICAgIDogU3RyaW5nKCBjdXJyZW50LnN0ZXAgKSxcclxuXHRcdFx0XHR2YWx1ZSAgICAgICAgICAgOiBTdHJpbmcoIGNsYW1wX251bSggcGFyc2VkLnZhbHVlLCBjdXJyZW50Lm1pbiwgY3VycmVudC5tYXggKSApXHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIEhpZGRlbiB3cml0ZXIgaW5wdXQgdGhhdCB0aGUgZGVmYXVsdCBJbnNwZWN0b3IgaGFuZGxlciB3aWxsIGNhdGNoLlxyXG5cdFx0XHR2YXIgaGlkZGVuID0gZWwoICdpbnB1dCcsIHtcclxuXHRcdFx0XHR0eXBlICAgICAgICAgICAgICAgIDogJ3RleHQnLFxyXG5cdFx0XHRcdCdjbGFzcycgICAgICAgICAgICAgOiAnaW5zcGVjdG9yX19pbnB1dCcsXHJcblx0XHRcdFx0c3R5bGUgICAgICAgICAgICAgICA6ICdkaXNwbGF5Om5vbmUnLFxyXG5cdFx0XHRcdCdhcmlhLWhpZGRlbicgICAgICAgOiAndHJ1ZScsXHJcblx0XHRcdFx0dGFiaW5kZXggICAgICAgICAgICA6ICctMScsXHJcblx0XHRcdFx0aWQgICAgICAgICAgICAgICAgICA6ICd3cGJjX2luc18nICsga2V5ICsgJ18nICsgdWlkICsgJ19sZW5faGlkZGVuJyxcclxuXHRcdFx0XHQnZGF0YS1pbnNwZWN0b3Ita2V5Jzoga2V5LFxyXG5cdFx0XHRcdHZhbHVlICAgICAgICAgICAgICAgOiAoU3RyaW5nKCBwYXJzZWQudmFsdWUgKSArIHBhcnNlZC51bml0KVxyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRncm91cC5hcHBlbmRDaGlsZCggaW5saW5lICk7XHJcblx0XHRcdGdyb3VwLmFwcGVuZENoaWxkKCByYW5nZSApO1xyXG5cdFx0XHRncm91cC5hcHBlbmRDaGlsZCggaGlkZGVuICk7XHJcblxyXG5cdFx0XHRjdHJsLmFwcGVuZENoaWxkKCBncm91cCApO1xyXG5cdFx0XHRyb3cuYXBwZW5kQ2hpbGQoIGxhYmVsICk7XHJcblx0XHRcdHJvdy5hcHBlbmRDaGlsZCggY3RybCApO1xyXG5cdFx0XHRyZXR1cm4gcm93O1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogV2lyZSBzeW5jaW5nIGZvciBhbnkgLndwYmNfbGVuX2dyb3VwIGluc2lkZSBhIGdpdmVuIHJvb3QgKHBhbmVsKS5cclxuXHRcdCAqIC0gcmFuZ2Ug4oeEIG51bWJlciBzeW5jXHJcblx0XHQgKiAtIHVuaXQgc3dpdGNoZXMgdXBkYXRlIHNsaWRlciBib3VuZHNcclxuXHRcdCAqIC0gaGlkZGVuIHdyaXRlciAoaWYgcHJlc2VudCkgZ2V0cyB1cGRhdGVkIGFuZCBlbWl0cyAnaW5wdXQnXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdpcmVfbGVuX2dyb3VwKHJvb3QpIHtcclxuXHRcdFx0aWYgKCAhcm9vdCApIHJldHVybjtcclxuXHJcblx0XHRcdGZ1bmN0aW9uIGZpbmRfZ3JvdXAoZWwpIHtcclxuXHRcdFx0XHRyZXR1cm4gZWwgJiYgZWwuY2xvc2VzdCAmJiBlbC5jbG9zZXN0KCAnLndwYmNfbGVuX2dyb3VwJyApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyb290LmFkZEV2ZW50TGlzdGVuZXIoICdpbnB1dCcsIGZ1bmN0aW9uIChlKSB7XHJcblx0XHRcdFx0dmFyIHQgPSBlLnRhcmdldDtcclxuXHRcdFx0XHQvLyBTbGlkZXIgbW92ZWQgLT4gdXBkYXRlIG51bWJlciAoYW5kIHdyaXRlci9oaWRkZW4pXHJcblx0XHRcdFx0aWYgKCB0ICYmIHQuaGFzQXR0cmlidXRlKCAnZGF0YS1sZW4tcmFuZ2UnICkgKSB7XHJcblx0XHRcdFx0XHR2YXIgZyA9IGZpbmRfZ3JvdXAoIHQgKTtcclxuXHRcdFx0XHRcdGlmICggIWcgKSByZXR1cm47XHJcblx0XHRcdFx0XHR2YXIgbnVtID0gZy5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtbGVuLXZhbHVlXScgKTtcclxuXHRcdFx0XHRcdGlmICggbnVtICkge1xyXG5cdFx0XHRcdFx0XHRudW0udmFsdWUgPSB0LnZhbHVlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0dmFyIHdyaXRlciA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWluc3BlY3Rvci1rZXldJyApO1xyXG5cdFx0XHRcdFx0aWYgKCB3cml0ZXIgJiYgd3JpdGVyLnR5cGUgPT09ICd0ZXh0JyApIHtcclxuXHRcdFx0XHRcdFx0dmFyIHVuaXQgICAgID0gZy5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtbGVuLXVuaXRdJyApO1xyXG5cdFx0XHRcdFx0XHR1bml0ICAgICAgICAgPSB1bml0ID8gdW5pdC52YWx1ZSA6ICdweCc7XHJcblx0XHRcdFx0XHRcdHdyaXRlci52YWx1ZSA9IFN0cmluZyggdC52YWx1ZSApICsgU3RyaW5nKCB1bml0ICk7XHJcblx0XHRcdFx0XHRcdC8vIHRyaWdnZXIgc3RhbmRhcmQgaW5zcGVjdG9yIGhhbmRsZXI6XHJcblx0XHRcdFx0XHRcdHdyaXRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdpbnB1dCcsIHsgYnViYmxlczogdHJ1ZSB9ICkgKTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdC8vIFBsYWluIHJhbmdlX251bWJlciBjYXNlIChudW1iZXIgaGFzIGRhdGEtaW5zcGVjdG9yLWtleSkgLT4gZmlyZSBpbnB1dCBvbiBudW1iZXJcclxuXHRcdFx0XHRcdFx0aWYgKCBudW0gJiYgbnVtLmhhc0F0dHJpYnV0ZSggJ2RhdGEtaW5zcGVjdG9yLWtleScgKSApIHtcclxuXHRcdFx0XHRcdFx0XHRudW0uZGlzcGF0Y2hFdmVudCggbmV3IEV2ZW50KCAnaW5wdXQnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIE51bWJlciB0eXBlZCAtPiB1cGRhdGUgc2xpZGVyIGFuZCB3cml0ZXIvaGlkZGVuXHJcblx0XHRcdFx0aWYgKCB0ICYmIHQuaGFzQXR0cmlidXRlKCAnZGF0YS1sZW4tdmFsdWUnICkgKSB7XHJcblx0XHRcdFx0XHR2YXIgZyA9IGZpbmRfZ3JvdXAoIHQgKTtcclxuXHRcdFx0XHRcdGlmICggIWcgKSByZXR1cm47XHJcblx0XHRcdFx0XHR2YXIgciA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWxlbi1yYW5nZV0nICk7XHJcblx0XHRcdFx0XHRpZiAoIHIgKSB7XHJcblx0XHRcdFx0XHRcdC8vIGNsYW1wIHdpdGhpbiBzbGlkZXIgYm91bmRzIGlmIHByZXNlbnRcclxuXHRcdFx0XHRcdFx0dmFyIG1pbiA9IE51bWJlciggci5taW4gKTtcclxuXHRcdFx0XHRcdFx0dmFyIG1heCA9IE51bWJlciggci5tYXggKTtcclxuXHRcdFx0XHRcdFx0dmFyIHYgICA9IE51bWJlciggdC52YWx1ZSApO1xyXG5cdFx0XHRcdFx0XHRpZiAoICFpc05hTiggdiApICkge1xyXG5cdFx0XHRcdFx0XHRcdHYgICAgICAgPSBjbGFtcF9udW0oIHYsIGlzTmFOKCBtaW4gKSA/IHVuZGVmaW5lZCA6IG1pbiwgaXNOYU4oIG1heCApID8gdW5kZWZpbmVkIDogbWF4ICk7XHJcblx0XHRcdFx0XHRcdFx0ci52YWx1ZSA9IFN0cmluZyggdiApO1xyXG5cdFx0XHRcdFx0XHRcdGlmICggU3RyaW5nKCB2ICkgIT09IHQudmFsdWUgKSB0LnZhbHVlID0gU3RyaW5nKCB2ICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdHZhciB3cml0ZXIgPSBnLnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1pbnNwZWN0b3Ita2V5XScgKTtcclxuXHRcdFx0XHRcdGlmICggd3JpdGVyICYmIHdyaXRlci50eXBlID09PSAndGV4dCcgKSB7XHJcblx0XHRcdFx0XHRcdHZhciB1bml0ICAgICA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWxlbi11bml0XScgKTtcclxuXHRcdFx0XHRcdFx0dW5pdCAgICAgICAgID0gdW5pdCA/IHVuaXQudmFsdWUgOiAncHgnO1xyXG5cdFx0XHRcdFx0XHR3cml0ZXIudmFsdWUgPSBTdHJpbmcoIHQudmFsdWUgfHwgMCApICsgU3RyaW5nKCB1bml0ICk7XHJcblx0XHRcdFx0XHRcdHdyaXRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdpbnB1dCcsIHsgYnViYmxlczogdHJ1ZSB9ICkgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdC8vIGVsc2U6IG51bWJlciBpdHNlbGYgbGlrZWx5IGNhcnJpZXMgZGF0YS1pbnNwZWN0b3Ita2V5IChyYW5nZV9udW1iZXIpOyBkZWZhdWx0IGhhbmRsZXIgd2lsbCBydW4uXHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9LCB0cnVlICk7XHJcblxyXG5cdFx0XHRyb290LmFkZEV2ZW50TGlzdGVuZXIoICdjaGFuZ2UnLCBmdW5jdGlvbiAoZSkge1xyXG5cdFx0XHRcdHZhciB0ID0gZS50YXJnZXQ7XHJcblx0XHRcdFx0Ly8gVW5pdCBjaGFuZ2VkIC0+IHVwZGF0ZSBzbGlkZXIgbGltaXRzIGFuZCB3cml0ZXIvaGlkZGVuXHJcblx0XHRcdFx0aWYgKCB0ICYmIHQuaGFzQXR0cmlidXRlKCAnZGF0YS1sZW4tdW5pdCcgKSApIHtcclxuXHRcdFx0XHRcdHZhciBnID0gZmluZF9ncm91cCggdCApO1xyXG5cdFx0XHRcdFx0aWYgKCAhZyApIHJldHVybjtcclxuXHJcblx0XHRcdFx0XHQvLyBGaW5kIHRoZSBjb250cm9sIG1ldGEgdmlhIGEgZGF0YSBhdHRyaWJ1dGUgb24gZ3JvdXAgaWYgcHJvdmlkZWRcclxuXHRcdFx0XHRcdC8vIChGYWN0b3J5IHBhdGggc2V0cyBub3RoaW5nIGhlcmU7IHdlIHJlLWRlcml2ZSBmcm9tIGN1cnJlbnQgc2xpZGVyIGJvdW5kcy4pXHJcblx0XHRcdFx0XHR2YXIgciAgICAgID0gZy5xdWVyeVNlbGVjdG9yKCAnW2RhdGEtbGVuLXJhbmdlXScgKTtcclxuXHRcdFx0XHRcdHZhciBudW0gICAgPSBnLnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1sZW4tdmFsdWVdJyApO1xyXG5cdFx0XHRcdFx0dmFyIHdyaXRlciA9IGcucXVlcnlTZWxlY3RvciggJ1tkYXRhLWluc3BlY3Rvci1rZXldJyApO1xyXG5cdFx0XHRcdFx0dmFyIHVuaXQgICA9IHQudmFsdWUgfHwgJ3B4JztcclxuXHJcblx0XHRcdFx0XHQvLyBBZGp1c3Qgc2xpZGVyIGJvdW5kcyBoZXVyaXN0aWNhbGx5IChtYXRjaCBGYWN0b3J5IGRlZmF1bHRzKVxyXG5cdFx0XHRcdFx0dmFyIGJvdW5kc19ieV91bml0ID0ge1xyXG5cdFx0XHRcdFx0XHQncHgnIDogeyBtaW46IDAsIG1heDogNTEyLCBzdGVwOiAxIH0sXHJcblx0XHRcdFx0XHRcdCclJyAgOiB7IG1pbjogMCwgbWF4OiAxMDAsIHN0ZXA6IDEgfSxcclxuXHRcdFx0XHRcdFx0J3JlbSc6IHsgbWluOiAwLCBtYXg6IDEwLCBzdGVwOiAwLjEgfSxcclxuXHRcdFx0XHRcdFx0J2VtJyA6IHsgbWluOiAwLCBtYXg6IDEwLCBzdGVwOiAwLjEgfVxyXG5cdFx0XHRcdFx0fTtcclxuXHRcdFx0XHRcdGlmICggciApIHtcclxuXHRcdFx0XHRcdFx0dmFyIGIgID0gYm91bmRzX2J5X3VuaXRbdW5pdF0gfHwgYm91bmRzX2J5X3VuaXRbJ3B4J107XHJcblx0XHRcdFx0XHRcdHIubWluICA9IFN0cmluZyggYi5taW4gKTtcclxuXHRcdFx0XHRcdFx0ci5tYXggID0gU3RyaW5nKCBiLm1heCApO1xyXG5cdFx0XHRcdFx0XHRyLnN0ZXAgPSBTdHJpbmcoIGIuc3RlcCApO1xyXG5cdFx0XHRcdFx0XHQvLyBjbGFtcCB0byBuZXcgYm91bmRzXHJcblx0XHRcdFx0XHRcdHZhciB2ICA9IE51bWJlciggbnVtICYmIG51bS52YWx1ZSA/IG51bS52YWx1ZSA6IHIudmFsdWUgKTtcclxuXHRcdFx0XHRcdFx0aWYgKCAhaXNOYU4oIHYgKSApIHtcclxuXHRcdFx0XHRcdFx0XHR2ICAgICAgID0gY2xhbXBfbnVtKCB2LCBiLm1pbiwgYi5tYXggKTtcclxuXHRcdFx0XHRcdFx0XHRyLnZhbHVlID0gU3RyaW5nKCB2ICk7XHJcblx0XHRcdFx0XHRcdFx0aWYgKCBudW0gKSBudW0udmFsdWUgPSBTdHJpbmcoIHYgKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCB3cml0ZXIgJiYgd3JpdGVyLnR5cGUgPT09ICd0ZXh0JyApIHtcclxuXHRcdFx0XHRcdFx0dmFyIHYgICAgICAgID0gbnVtICYmIG51bS52YWx1ZSA/IG51bS52YWx1ZSA6IChyID8gci52YWx1ZSA6ICcwJyk7XHJcblx0XHRcdFx0XHRcdHdyaXRlci52YWx1ZSA9IFN0cmluZyggdiApICsgU3RyaW5nKCB1bml0ICk7XHJcblx0XHRcdFx0XHRcdHdyaXRlci5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdpbnB1dCcsIHsgYnViYmxlczogdHJ1ZSB9ICkgKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0sIHRydWUgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcblx0XHQvLyA9PSAgQyBPIE4gVCBSIE8gTCAgPT1cclxuXHRcdC8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuXHJcblx0XHQvKipcclxuXHQgKiBTY2hlbWEgPiBJbnNwZWN0b3IgPiBDb250cm9sIEVsZW1lbnQsIGUuZy4gSW5wdXQhICBCdWlsZCBhIHNpbmdsZSBjb250cm9sIHJvdzpcclxuXHQgKiA8ZGl2IGNsYXNzPVwiaW5zcGVjdG9yX19yb3dcIj5cclxuXHQgKiAgIDxsYWJlbCBjbGFzcz1cImluc3BlY3Rvcl9fbGFiZWxcIiBmb3I9XCIuLi5cIj5MYWJlbDwvbGFiZWw+XHJcblx0ICogICA8ZGl2IGNsYXNzPVwiaW5zcGVjdG9yX19jb250cm9sXCI+PGlucHV0fHRleHRhcmVhfHNlbGVjdCBjbGFzcz1cImluc3BlY3Rvcl9faW5wdXRcIiAuLi4+PC9kaXY+XHJcblx0ICogPC9kaXY+XHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge09iamVjdH0gY29udHJvbCAgICAgICAgICAgLSBzY2hlbWEgY29udHJvbCBtZXRhICh7dHlwZSxrZXksbGFiZWwsLi4ufSlcclxuXHQgKiBAcGFyYW0ge09iamVjdH0gcHJvcHNfc2NoZW1hICAgICAgLSBzY2hlbWEucHJvcHNcclxuXHQgKiBAcGFyYW0ge09iamVjdH0gZGF0YSAgICAgICAgICAgICAgLSBjdXJyZW50IGVsZW1lbnQgZGF0YS0qIG1hcFxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB1aWQgICAgICAgICAgICAgICAtIHVuaXF1ZSBzdWZmaXggZm9yIGlucHV0IGlkc1xyXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBjdHggICAgICAgICAgICAgICAtIHsgZWwsIGJ1aWxkZXIsIHR5cGUsIGRhdGEgfVxyXG5cdCAqIEByZXR1cm5zIHtIVE1MRWxlbWVudH1cclxuXHQgKi9cclxuXHRcdGZ1bmN0aW9uIGJ1aWxkX2NvbnRyb2woY29udHJvbCwgcHJvcHNfc2NoZW1hLCBkYXRhLCB1aWQsIGN0eCkge1xyXG5cdFx0XHR2YXIgdHlwZSA9IGNvbnRyb2wudHlwZTtcclxuXHRcdFx0dmFyIGtleSAgPSBjb250cm9sLmtleTtcclxuXHJcblx0XHRcdHZhciBsYWJlbF90ZXh0ID0gY29udHJvbC5sYWJlbCB8fCBrZXkgfHwgJyc7XHJcblx0XHRcdHZhciBwcm9wX21ldGEgID0gKGtleSA/IChwcm9wc19zY2hlbWFba2V5XSB8fCB7IHR5cGU6ICdzdHJpbmcnIH0pIDogeyB0eXBlOiAnc3RyaW5nJyB9KTtcclxuXHRcdFx0dmFyIHZhbHVlICAgICAgPSBjb2VyY2VfYnlfdHlwZSggZ2V0X2luaXRpYWxfdmFsdWUoIGtleSwgZGF0YSwgcHJvcHNfc2NoZW1hICksIHByb3BfbWV0YS50eXBlICk7XHJcblx0XHQvLyBBbGxvdyB2YWx1ZV9mcm9tIG92ZXJyaWRlIChjb21wdXRlZCBhdCByZW5kZXItdGltZSkuXHJcblx0XHRpZiAoIGNvbnRyb2wgJiYgY29udHJvbC52YWx1ZV9mcm9tICYmIHcud3BiY19iZmJfaW5zcGVjdG9yX2ZhY3RvcnlfdmFsdWVfZnJvbVtjb250cm9sLnZhbHVlX2Zyb21dICkge1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHR2YXIgY29tcHV0ZWQgPSB3LndwYmNfYmZiX2luc3BlY3Rvcl9mYWN0b3J5X3ZhbHVlX2Zyb21bY29udHJvbC52YWx1ZV9mcm9tXSggY3R4IHx8IHt9ICk7XHJcblx0XHRcdFx0XHR2YWx1ZSAgICAgICAgPSBjb2VyY2VfYnlfdHlwZSggY29tcHV0ZWQsIHByb3BfbWV0YS50eXBlICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHRjb25zb2xlLndhcm4oICd2YWx1ZV9mcm9tIGZhaWxlZCBmb3InLCBjb250cm9sLnZhbHVlX2Zyb20sIGUgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBpbnB1dF9pZCA9ICd3cGJjX2luc18nICsga2V5ICsgJ18nICsgdWlkO1xyXG5cclxuXHRcdFx0dmFyIHJvd19lbCAgICA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19yb3cnIH0gKTtcclxuXHRcdFx0dmFyIGxhYmVsX2VsICA9IGVsKCAnbGFiZWwnLCB7ICdmb3InOiBpbnB1dF9pZCwgJ2NsYXNzJzogJ2luc3BlY3Rvcl9fbGFiZWwnIH0sIGxhYmVsX3RleHQgKTtcclxuXHRcdFx0dmFyIGN0cmxfd3JhcCA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnaW5zcGVjdG9yX19jb250cm9sJyB9ICk7XHJcblxyXG5cdFx0XHR2YXIgZmllbGRfZWw7XHJcblxyXG5cdFx0Ly8gLS0tIHNsb3QgaG9zdCAobmFtZWQgVUkgaW5qZWN0aW9uKSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0aWYgKCB0eXBlID09PSAnc2xvdCcgJiYgY29udHJvbC5zbG90ICkge1xyXG5cdFx0XHQvLyBhZGQgYSBtYXJrZXIgY2xhc3MgZm9yIHRoZSBsYXlvdXQgY2hpcHMgcm93XHJcblx0XHRcdHZhciBjbGFzc2VzID0gJ2luc3BlY3Rvcl9fcm93IGluc3BlY3Rvcl9fcm93LS1zbG90JztcclxuXHRcdFx0aWYgKCBjb250cm9sLnNsb3QgPT09ICdsYXlvdXRfY2hpcHMnICkgY2xhc3NlcyArPSAnIGluc3BlY3Rvcl9fcm93LS1sYXlvdXQtY2hpcHMnO1xyXG5cclxuXHRcdFx0dmFyIHNsb3Rfcm93ID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6IGNsYXNzZXMgfSApO1xyXG5cclxuXHRcdFx0aWYgKCBsYWJlbF90ZXh0ICkgc2xvdF9yb3cuYXBwZW5kQ2hpbGQoIGVsKCAnbGFiZWwnLCB7ICdjbGFzcyc6ICdpbnNwZWN0b3JfX2xhYmVsJyB9LCBsYWJlbF90ZXh0ICkgKTtcclxuXHJcblx0XHRcdC8vIGFkZCBhIGRhdGEgYXR0cmlidXRlIG9uIHRoZSBob3N0IHNvIGJvdGggQ1NTIGFuZCB0aGUgc2FmZXR5LW5ldCBjYW4gdGFyZ2V0IGl0XHJcblx0XHRcdHZhciBob3N0X2F0dHJzID0geyAnY2xhc3MnOiAnaW5zcGVjdG9yX19jb250cm9sJyB9O1xyXG5cdFx0XHRpZiAoIGNvbnRyb2wuc2xvdCA9PT0gJ2xheW91dF9jaGlwcycgKSBob3N0X2F0dHJzWydkYXRhLWJmYi1zbG90J10gPSAnbGF5b3V0X2NoaXBzJztcclxuXHJcblx0XHRcdHZhciBzbG90X2hvc3QgPSBlbCggJ2RpdicsIGhvc3RfYXR0cnMgKTtcclxuXHRcdFx0c2xvdF9yb3cuYXBwZW5kQ2hpbGQoIHNsb3RfaG9zdCApO1xyXG5cclxuXHRcdFx0dmFyIHNsb3RfZm4gPSB3LndwYmNfYmZiX2luc3BlY3Rvcl9mYWN0b3J5X3Nsb3RzW2NvbnRyb2wuc2xvdF07XHJcblx0XHRcdGlmICggdHlwZW9mIHNsb3RfZm4gPT09ICdmdW5jdGlvbicgKSB7XHJcblx0XHRcdFx0c2V0VGltZW91dCggZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0c2xvdF9mbiggc2xvdF9ob3N0LCBjdHggfHwge30gKTtcclxuXHRcdFx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHRcdFx0XHRjb25zb2xlLndhcm4oICdzbG90IFwiJyArIGNvbnRyb2wuc2xvdCArICdcIiBmYWlsZWQ6JywgZSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0sIDAgKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRzbG90X2hvc3QuYXBwZW5kQ2hpbGQoIGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnd3BiY19iZmJfX3Nsb3RfX21pc3NpbmcnIH0sICdbc2xvdDogJyArIGNvbnRyb2wuc2xvdCArICddJyApICk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIHNsb3Rfcm93O1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHRcdGlmICggdHlwZSA9PT0gJ3RleHRhcmVhJyApIHtcclxuXHRcdFx0XHRmaWVsZF9lbCA9IGVsKCAndGV4dGFyZWEnLCB7XHJcblx0XHRcdFx0XHRpZCAgICAgICAgICAgICAgICAgIDogaW5wdXRfaWQsXHJcblx0XHRcdFx0XHQnZGF0YS1pbnNwZWN0b3Ita2V5Jzoga2V5LFxyXG5cdFx0XHRcdFx0cm93cyAgICAgICAgICAgICAgICA6IGNvbnRyb2wucm93cyB8fCAzLFxyXG5cdFx0XHRcdFx0J2NsYXNzJyAgICAgICAgICAgICA6ICdpbnNwZWN0b3JfX2lucHV0J1xyXG5cdFx0XHRcdH0sICh2YWx1ZSA9PSBudWxsID8gJycgOiBTdHJpbmcoIHZhbHVlICkpICk7XHJcblx0XHRcdH0gZWxzZSBpZiAoIHR5cGUgPT09ICdzZWxlY3QnICkge1xyXG5cdFx0XHRcdGZpZWxkX2VsID0gZWwoICdzZWxlY3QnLCB7XHJcblx0XHRcdFx0XHRpZCAgICAgICAgICAgICAgICAgIDogaW5wdXRfaWQsXHJcblx0XHRcdFx0XHQnZGF0YS1pbnNwZWN0b3Ita2V5Jzoga2V5LFxyXG5cdFx0XHRcdFx0J2NsYXNzJyAgICAgICAgICAgICA6ICdpbnNwZWN0b3JfX2lucHV0J1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRub3JtYWxpemVfc2VsZWN0X29wdGlvbnMoIGNvbnRyb2wub3B0aW9ucyB8fCBbXSApLmZvckVhY2goIGZ1bmN0aW9uIChvcHQpIHtcclxuXHRcdFx0XHRcdHZhciBvcHRfZWwgPSBlbCggJ29wdGlvbicsIHsgdmFsdWU6IG9wdC52YWx1ZSB9LCBvcHQubGFiZWwgKTtcclxuXHRcdFx0XHRcdGlmICggU3RyaW5nKCB2YWx1ZSApID09PSBvcHQudmFsdWUgKSBvcHRfZWwuc2V0QXR0cmlidXRlKCAnc2VsZWN0ZWQnLCAnc2VsZWN0ZWQnICk7XHJcblx0XHRcdFx0XHRmaWVsZF9lbC5hcHBlbmRDaGlsZCggb3B0X2VsICk7XHJcblx0XHRcdFx0fSApO1xyXG5cdFx0XHR9IGVsc2UgaWYgKCB0eXBlID09PSAnY2hlY2tib3gnICkge1xyXG5cdFx0XHRcdC8vIGZpZWxkX2VsID0gZWwoICdpbnB1dCcsIHsgaWQ6IGlucHV0X2lkLCB0eXBlOiAnY2hlY2tib3gnLCAnZGF0YS1pbnNwZWN0b3Ita2V5Jzoga2V5LCBjaGVja2VkOiAhIXZhbHVlLCAnY2xhc3MnOiAnaW5zcGVjdG9yX19pbnB1dCcgfSApOyAvLy5cclxuXHJcblx0XHRcdFx0Ly8gUmVuZGVyIGFzIHRvZ2dsZSBVSSBpbnN0ZWFkIG9mIGxhYmVsLWxlZnQgKyBjaGVja2JveC4gIE5vdGU6IHdlIHJldHVybiB0aGUgZnVsbCB0b2dnbGUgcm93IGhlcmUgYW5kIHNraXAgdGhlIGRlZmF1bHQgcm93L2xhYmVsIGZsb3cgYmVsb3cuXHJcblx0XHRcdFx0cmV0dXJuIGJ1aWxkX3RvZ2dsZV9yb3coIGlucHV0X2lkLCBrZXksICEhdmFsdWUsIGxhYmVsX3RleHQgKTtcclxuXHJcblx0XHRcdH0gZWxzZSBpZiAoIHR5cGUgPT09ICdyYW5nZV9udW1iZXInICkge1xyXG5cdFx0XHRcdC8vIC0tLSBuZXc6IHNsaWRlciArIG51bWJlciAoc2luZ2xlIGtleSkuXHJcblx0XHRcdFx0dmFyIHJuX2lkICA9ICd3cGJjX2luc18nICsga2V5ICsgJ18nICsgdWlkO1xyXG5cdFx0XHRcdHZhciBybl92YWwgPSB2YWx1ZTsgLy8gZnJvbSBnZXRfaW5pdGlhbF92YWx1ZS9wcm9wX21ldGEgYWxyZWFkeS5cclxuXHRcdFx0XHRyZXR1cm4gYnVpbGRfcmFuZ2VfbnVtYmVyX3Jvdyggcm5faWQsIGtleSwgbGFiZWxfdGV4dCwgcm5fdmFsLCBjb250cm9sICk7XHJcblxyXG5cdFx0XHR9IGVsc2UgaWYgKCB0eXBlID09PSAnbGVuJyApIHtcclxuXHRcdFx0XHQvLyAtLS0gbmV3OiBsZW5ndGggY29tcG91bmQgKHZhbHVlK3VuaXQrc2xpZGVyIC0+IHdyaXRlcyBhIHNpbmdsZSBzdHJpbmcga2V5KS5cclxuXHRcdFx0XHRyZXR1cm4gYnVpbGRfbGVuX2NvbXBvdW5kX3JvdyggY29udHJvbCwgcHJvcHNfc2NoZW1hLCBkYXRhLCB1aWQgKTtcclxuXHJcblx0XHRcdH0gZWxzZSBpZiAoIHR5cGUgPT09ICdjb2xvcicgKSB7XHJcblx0XHRcdFx0Ly8gQ29sb3IgcGlja2VyIChDb2xvcmlzKS4gU3RvcmUgYXMgc3RyaW5nIChlLmcuLCBcIiNlMGUwZTBcIikuXHJcblx0XHRcdFx0ZmllbGRfZWwgPSBlbCggJ2lucHV0Jywge1xyXG5cdFx0XHRcdFx0aWQgICAgICAgICAgICAgICAgICAgOiBpbnB1dF9pZCxcclxuXHRcdFx0XHRcdHR5cGUgICAgICAgICAgICAgICAgIDogJ3RleHQnLFxyXG5cdFx0XHRcdFx0J2RhdGEtaW5zcGVjdG9yLWtleScgOiBrZXksXHJcblx0XHRcdFx0XHQnZGF0YS1pbnNwZWN0b3ItdHlwZSc6ICdjb2xvcicsXHJcblx0XHRcdFx0XHQnZGF0YS1jb2xvcmlzJyAgICAgICA6ICcnLFxyXG5cdFx0XHRcdFx0J2NsYXNzJyAgICAgICAgICAgICAgOiAnaW5zcGVjdG9yX19pbnB1dCcsXHJcblx0XHRcdFx0XHQnZGF0YS1kZWZhdWx0LWNvbG9yJyA6ICggdmFsdWUgIT0gbnVsbCAmJiB2YWx1ZSAhPT0gJycgPyBTdHJpbmcodmFsdWUpIDogKGNvbnRyb2wucGxhY2Vob2xkZXIgfHwgJycpIClcclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0aWYgKCB2YWx1ZSAhPT0gJycgKSB7XHJcblx0XHRcdFx0XHRmaWVsZF9lbC52YWx1ZSA9IFN0cmluZyggdmFsdWUgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0Ly8gdGV4dC9udW1iZXIgZGVmYXVsdC5cclxuXHRcdFx0XHR2YXIgYXR0cnMgPSB7XHJcblx0XHRcdFx0XHRpZCAgICAgICAgICAgICAgICAgIDogaW5wdXRfaWQsXHJcblx0XHRcdFx0XHR0eXBlICAgICAgICAgICAgICAgIDogKHR5cGUgPT09ICdudW1iZXInKSA/ICdudW1iZXInIDogJ3RleHQnLFxyXG5cdFx0XHRcdFx0J2RhdGEtaW5zcGVjdG9yLWtleSc6IGtleSxcclxuXHRcdFx0XHRcdCdjbGFzcycgICAgICAgICAgICAgOiAnaW5zcGVjdG9yX19pbnB1dCdcclxuXHRcdFx0XHR9O1xyXG5cdFx0XHQvLyBudW1iZXIgY29uc3RyYWludHMgKHNjaGVtYSBvciBjb250cm9sKVxyXG5cdFx0XHRcdGlmICggdHlwZSA9PT0gJ251bWJlcicgKSB7XHJcblx0XHRcdFx0XHRpZiAoIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggcHJvcF9tZXRhLCAnbWluJyApICkgYXR0cnMubWluID0gcHJvcF9tZXRhLm1pbjtcclxuXHRcdFx0XHRcdGlmICggT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKCBwcm9wX21ldGEsICdtYXgnICkgKSBhdHRycy5tYXggPSBwcm9wX21ldGEubWF4O1xyXG5cdFx0XHRcdFx0aWYgKCBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoIHByb3BfbWV0YSwgJ3N0ZXAnICkgKSBhdHRycy5zdGVwID0gcHJvcF9tZXRhLnN0ZXA7XHJcblx0XHRcdFx0XHRpZiAoIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCggY29udHJvbCwgJ21pbicgKSApIGF0dHJzLm1pbiA9IGNvbnRyb2wubWluO1xyXG5cdFx0XHRcdFx0aWYgKCBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoIGNvbnRyb2wsICdtYXgnICkgKSBhdHRycy5tYXggPSBjb250cm9sLm1heDtcclxuXHRcdFx0XHRcdGlmICggT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKCBjb250cm9sLCAnc3RlcCcgKSApIGF0dHJzLnN0ZXAgPSBjb250cm9sLnN0ZXA7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdGZpZWxkX2VsID0gZWwoICdpbnB1dCcsIGF0dHJzICk7XHJcblx0XHRcdFx0aWYgKCB2YWx1ZSAhPT0gJycgKSBmaWVsZF9lbC52YWx1ZSA9IFN0cmluZyggdmFsdWUgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y3RybF93cmFwLmFwcGVuZENoaWxkKCBmaWVsZF9lbCApO1xyXG5cdFx0XHRyb3dfZWwuYXBwZW5kQ2hpbGQoIGxhYmVsX2VsICk7XHJcblx0XHRcdHJvd19lbC5hcHBlbmRDaGlsZCggY3RybF93cmFwICk7XHJcblx0XHRcdHJldHVybiByb3dfZWw7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTY2hlbWEgPiBJbnNwZWN0b3IgPiBHcm91cHMhIEJ1aWxkIGFuIGluc3BlY3RvciBncm91cCAoY29sbGFwc2libGUpLlxyXG5cdFx0ICogU3RydWN0dXJlOlxyXG5cdFx0ICogPHNlY3Rpb24gY2xhc3M9XCJ3cGJjX2JmYl9faW5zcGVjdG9yX19ncm91cCB3cGJjX3VpX19jb2xsYXBzaWJsZV9ncm91cCBpcy1vcGVuXCIgZGF0YS1ncm91cD1cIi4uLlwiPlxyXG5cdFx0ICogICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImdyb3VwX19oZWFkZXJcIiByb2xlPVwiYnV0dG9uXCIgYXJpYS1leHBhbmRlZD1cInRydWVcIiBhcmlhLWNvbnRyb2xzPVwid3BiY19jb2xsYXBzaWJsZV9wYW5lbF9YXCI+XHJcblx0XHQgKiAgICAgPGgzPkdyb3VwIFRpdGxlPC9oMz5cclxuXHRcdCAqICAgICA8aSBjbGFzcz1cIndwYmNfdWlfZWxfX3ZlcnRfbWVudV9yb290X3NlY3Rpb25faWNvbiBtZW51X2ljb24gaWNvbi0xeCB3cGJjLWJpLWNoZXZyb24tcmlnaHRcIj48L2k+XHJcblx0XHQgKiAgIDwvYnV0dG9uPlxyXG5cdFx0ICogICA8ZGl2IGNsYXNzPVwiZ3JvdXBfX2ZpZWxkc1wiIGlkPVwid3BiY19jb2xsYXBzaWJsZV9wYW5lbF9YXCIgYXJpYS1oaWRkZW49XCJmYWxzZVwiPiDigKZyb3dz4oCmIDwvZGl2PlxyXG5cdFx0ICogPC9zZWN0aW9uPlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBncm91cFxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IHByb3BzX3NjaGVtYVxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IGRhdGFcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB1aWRcclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBjdHhcclxuXHRcdCAqIEByZXR1cm5zIHtIVE1MRWxlbWVudH1cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gYnVpbGRfZ3JvdXAoZ3JvdXAsIHByb3BzX3NjaGVtYSwgZGF0YSwgdWlkLCBjdHgpIHtcclxuXHRcdFx0dmFyIGlzX29wZW4gID0gISFncm91cC5vcGVuO1xyXG5cdFx0XHR2YXIgcGFuZWxfaWQgPSAnd3BiY19jb2xsYXBzaWJsZV9wYW5lbF8nICsgdWlkICsgJ18nICsgKGdyb3VwLmtleSB8fCAnZycpO1xyXG5cclxuXHRcdFx0dmFyIHNlY3Rpb24gPSBlbCggJ3NlY3Rpb24nLCB7XHJcblx0XHRcdFx0J2NsYXNzJyAgICAgOiAnd3BiY19iZmJfX2luc3BlY3Rvcl9fZ3JvdXAgd3BiY191aV9fY29sbGFwc2libGVfZ3JvdXAnICsgKGlzX29wZW4gPyAnIGlzLW9wZW4nIDogJycpLFxyXG5cdFx0XHRcdCdkYXRhLWdyb3VwJzogZ3JvdXAua2V5IHx8ICcnXHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdHZhciBoZWFkZXJfYnRuID0gZWwoICdidXR0b24nLCB7XHJcblx0XHRcdFx0dHlwZSAgICAgICAgICAgOiAnYnV0dG9uJyxcclxuXHRcdFx0XHQnY2xhc3MnICAgICAgICA6ICdncm91cF9faGVhZGVyJyxcclxuXHRcdFx0XHRyb2xlICAgICAgICAgICA6ICdidXR0b24nLFxyXG5cdFx0XHRcdCdhcmlhLWV4cGFuZGVkJzogaXNfb3BlbiA/ICd0cnVlJyA6ICdmYWxzZScsXHJcblx0XHRcdFx0J2FyaWEtY29udHJvbHMnOiBwYW5lbF9pZFxyXG5cdFx0XHR9LCBbXHJcblx0XHRcdFx0ZWwoICdoMycsIG51bGwsIGdyb3VwLnRpdGxlIHx8IGdyb3VwLmxhYmVsIHx8IGdyb3VwLmtleSB8fCAnJyApLFxyXG5cdFx0XHRcdGVsKCAnaScsIHsgJ2NsYXNzJzogJ3dwYmNfdWlfZWxfX3ZlcnRfbWVudV9yb290X3NlY3Rpb25faWNvbiBtZW51X2ljb24gaWNvbi0xeCB3cGJjLWJpLWNoZXZyb24tcmlnaHQnIH0gKVxyXG5cdFx0XHRdICk7XHJcblxyXG5cdFx0XHR2YXIgZmllbGRzID0gZWwoICdkaXYnLCB7XHJcblx0XHRcdFx0J2NsYXNzJyAgICAgIDogJ2dyb3VwX19maWVsZHMnLFxyXG5cdFx0XHRcdGlkICAgICAgICAgICA6IHBhbmVsX2lkLFxyXG5cdFx0XHRcdCdhcmlhLWhpZGRlbic6IGlzX29wZW4gPyAnZmFsc2UnIDogJ3RydWUnXHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdGZ1bmN0aW9uIGFzQXJyYXkoeCkge1xyXG5cdFx0XHRcdGlmICggQXJyYXkuaXNBcnJheSggeCApICkgcmV0dXJuIHg7XHJcblx0XHRcdFx0aWYgKCB4ICYmIHR5cGVvZiB4ID09PSAnb2JqZWN0JyApIHJldHVybiBPYmplY3QudmFsdWVzKCB4ICk7XHJcblx0XHRcdFx0cmV0dXJuIHggIT0gbnVsbCA/IFsgeCBdIDogW107XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGFzQXJyYXkoIGdyb3VwLmNvbnRyb2xzICkuZm9yRWFjaCggZnVuY3Rpb24gKGNvbnRyb2wpIHtcclxuXHRcdFx0XHRmaWVsZHMuYXBwZW5kQ2hpbGQoIGJ1aWxkX2NvbnRyb2woIGNvbnRyb2wsIHByb3BzX3NjaGVtYSwgZGF0YSwgdWlkLCBjdHggKSApO1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRzZWN0aW9uLmFwcGVuZENoaWxkKCBoZWFkZXJfYnRuICk7XHJcblx0XHRcdHNlY3Rpb24uYXBwZW5kQ2hpbGQoIGZpZWxkcyApO1xyXG5cdFx0XHRyZXR1cm4gc2VjdGlvbjtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNjaGVtYSA+IEluc3BlY3RvciA+IEhlYWRlciEgQnVpbGQgaW5zcGVjdG9yIGhlYWRlciB3aXRoIGFjdGlvbiBidXR0b25zIHdpcmVkIHRvIGV4aXN0aW5nIGRhdGEtYWN0aW9uIGhhbmRsZXJzLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7QXJyYXk8c3RyaW5nPn0gaGVhZGVyX2FjdGlvbnNcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSAgICAgICAgdGl0bGVfdGV4dFxyXG5cdFx0ICogQHJldHVybnMge0hUTUxFbGVtZW50fVxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBidWlsZF9oZWFkZXIoaW5zcGVjdG9yX3VpLCB0aXRsZV9mYWxsYmFjaywgc2NoZW1hX2Zvcl90eXBlKSB7XHJcblxyXG5cdFx0XHRpbnNwZWN0b3JfdWkgICAgICA9IGluc3BlY3Rvcl91aSB8fCB7fTtcclxuXHRcdFx0c2NoZW1hX2Zvcl90eXBlICAgPSBzY2hlbWFfZm9yX3R5cGUgfHwge307XHJcblx0XHRcdHZhciB2YXJpYW50ICAgICAgID0gaW5zcGVjdG9yX3VpLmhlYWRlcl92YXJpYW50IHx8ICdtaW5pbWFsJztcclxuXHRcdFx0dmFyIGhlYWRlckFjdGlvbnMgPSBpbnNwZWN0b3JfdWkuaGVhZGVyX2FjdGlvbnNcclxuXHRcdFx0XHR8fCBzY2hlbWFfZm9yX3R5cGUuaGVhZGVyX2FjdGlvbnNcclxuXHRcdFx0XHR8fCBbICdkZXNlbGVjdCcsICdzY3JvbGx0bycsICdtb3ZlLXVwJywgJ21vdmUtZG93bicsICdkdXBsaWNhdGUnLCAnZGVsZXRlJyBdO1xyXG5cclxuXHRcdFx0dmFyIHRpdGxlICAgICAgID0gaW5zcGVjdG9yX3VpLnRpdGxlIHx8IHRpdGxlX2ZhbGxiYWNrIHx8ICcnO1xyXG5cdFx0XHR2YXIgZGVzY3JpcHRpb24gPSBpbnNwZWN0b3JfdWkuZGVzY3JpcHRpb24gfHwgJyc7XHJcblxyXG5cdFx0XHQvLyBoZWxwZXIgdG8gY3JlYXRlIGEgYnV0dG9uIGZvciBlaXRoZXIgaGVhZGVyIHN0eWxlXHJcblx0XHRcdGZ1bmN0aW9uIGFjdGlvbkJ0bihhY3QsIG1pbmltYWwpIHtcclxuXHRcdFx0XHRpZiAoIG1pbmltYWwgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gZWwoICdidXR0b24nLCB7IHR5cGU6ICdidXR0b24nLCAnY2xhc3MnOiAnYnV0dG9uLWxpbmsnLCAnZGF0YS1hY3Rpb24nOiBhY3QgfSwgJycgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Ly8gdG9vbGJhciB2YXJpYW50IChyaWNoKVxyXG5cdFx0XHRcdHZhciBpY29uTWFwID0ge1xyXG5cdFx0XHRcdFx0J2Rlc2VsZWN0JyA6ICd3cGJjX2ljbl9yZW1vdmVfZG9uZScsXHJcblx0XHRcdFx0XHQnc2Nyb2xsdG8nIDogJ3dwYmNfaWNuX2Fkc19jbGljayBmaWx0ZXJfY2VudGVyX2ZvY3VzJyxcclxuXHRcdFx0XHRcdCdtb3ZlLXVwJyAgOiAnd3BiY19pY25fYXJyb3dfdXB3YXJkJyxcclxuXHRcdFx0XHRcdCdtb3ZlLWRvd24nOiAnd3BiY19pY25fYXJyb3dfZG93bndhcmQnLFxyXG5cdFx0XHRcdFx0J2R1cGxpY2F0ZSc6ICd3cGJjX2ljbl9jb250ZW50X2NvcHknLFxyXG5cdFx0XHRcdFx0J2RlbGV0ZScgICA6ICd3cGJjX2ljbl9kZWxldGVfb3V0bGluZSdcclxuXHRcdFx0XHR9O1xyXG5cdFx0XHRcdHZhciBjbGFzc2VzID0gJ2J1dHRvbiBidXR0b24tc2Vjb25kYXJ5IHdwYmNfdWlfY29udHJvbCB3cGJjX3VpX2J1dHRvbic7XHJcblx0XHRcdFx0aWYgKCBhY3QgPT09ICdkZWxldGUnICkgY2xhc3NlcyArPSAnIHdwYmNfdWlfYnV0dG9uX2RhbmdlciBidXR0b24tbGluay1kZWxldGUnO1xyXG5cclxuXHRcdFx0XHR2YXIgYnRuID0gZWwoICdidXR0b24nLCB7XHJcblx0XHRcdFx0XHR0eXBlICAgICAgICAgOiAnYnV0dG9uJyxcclxuXHRcdFx0XHRcdCdjbGFzcycgICAgICA6IGNsYXNzZXMsXHJcblx0XHRcdFx0XHQnZGF0YS1hY3Rpb24nOiBhY3QsXHJcblx0XHRcdFx0XHQnYXJpYS1sYWJlbCcgOiBhY3QucmVwbGFjZSggLy0vZywgJyAnIClcclxuXHRcdFx0XHR9ICk7XHJcblxyXG5cdFx0XHRcdGlmICggYWN0ID09PSAnZGVsZXRlJyApIHtcclxuXHRcdFx0XHRcdGJ0bi5hcHBlbmRDaGlsZCggZWwoICdzcGFuJywgeyAnY2xhc3MnOiAnaW4tYnV0dG9uLXRleHQnIH0sICdEZWxldGUnICkgKTtcclxuXHRcdFx0XHRcdGJ0bi5hcHBlbmRDaGlsZCggZG9jdW1lbnQuY3JlYXRlVGV4dE5vZGUoICcgJyApICk7IC8vIG1pbm9yIHNwYWNpbmcgYmVmb3JlIGljb25cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0YnRuLmFwcGVuZENoaWxkKCBlbCggJ2knLCB7ICdjbGFzcyc6ICdtZW51X2ljb24gaWNvbi0xeCAnICsgKGljb25NYXBbYWN0XSB8fCAnJykgfSApICk7XHJcblx0XHRcdFx0cmV0dXJuIGJ0bjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gPT09IG1pbmltYWwgaGVhZGVyIChleGlzdGluZyBsb29rOyBkZWZhdWx0KSA9PT1cclxuXHRcdFx0aWYgKCB2YXJpYW50ICE9PSAndG9vbGJhcicgKSB7XHJcblx0XHRcdFx0dmFyIGhlYWRlciA9IGVsKCAnaGVhZGVyJywgeyAnY2xhc3MnOiAnd3BiY19iZmJfX2luc3BlY3Rvcl9faGVhZGVyJyB9ICk7XHJcblx0XHRcdFx0aGVhZGVyLmFwcGVuZENoaWxkKCBlbCggJ2gzJywgbnVsbCwgdGl0bGUgfHwgJycgKSApO1xyXG5cclxuXHRcdFx0XHR2YXIgYWN0aW9ucyA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAnd3BiY19iZmJfX2luc3BlY3Rvcl9faGVhZGVyX2FjdGlvbnMnIH0gKTtcclxuXHRcdFx0XHRoZWFkZXJBY3Rpb25zLmZvckVhY2goIGZ1bmN0aW9uIChhY3QpIHtcclxuXHRcdFx0XHRcdGFjdGlvbnMuYXBwZW5kQ2hpbGQoIGFjdGlvbkJ0biggYWN0LCAvKm1pbmltYWwqL3RydWUgKSApO1xyXG5cdFx0XHRcdH0gKTtcclxuXHRcdFx0XHRoZWFkZXIuYXBwZW5kQ2hpbGQoIGFjdGlvbnMgKTtcclxuXHRcdFx0XHRyZXR1cm4gaGVhZGVyO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyA9PT0gdG9vbGJhciBoZWFkZXIgKHJpY2ggdGl0bGUvZGVzYyArIGdyb3VwZWQgYnV0dG9ucykgPT09XHJcblx0XHRcdHZhciByb290ID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICd3cGJjX2JmYl9faW5zcGVjdG9yX19oZWFkJyB9ICk7XHJcblx0XHRcdHZhciB3cmFwID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICdoZWFkZXJfY29udGFpbmVyJyB9ICk7XHJcblx0XHRcdHZhciBsZWZ0ID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICdoZWFkZXJfdGl0bGVfY29udGVudCcgfSApO1xyXG5cdFx0XHR2YXIgaDMgICA9IGVsKCAnaDMnLCB7ICdjbGFzcyc6ICd0aXRsZScgfSwgdGl0bGUgfHwgJycgKTtcclxuXHRcdFx0bGVmdC5hcHBlbmRDaGlsZCggaDMgKTtcclxuXHRcdFx0aWYgKCBkZXNjcmlwdGlvbiApIHtcclxuXHRcdFx0XHRsZWZ0LmFwcGVuZENoaWxkKCBlbCggJ2RpdicsIHsgJ2NsYXNzJzogJ2Rlc2MnIH0sIGRlc2NyaXB0aW9uICkgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIHJpZ2h0ID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICdhY3Rpb25zIHdwYmNfYWp4X3Rvb2xiYXIgd3BiY19ub19ib3JkZXJzJyB9ICk7XHJcblx0XHRcdHZhciB1aUMgICA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAndWlfY29udGFpbmVyIHVpX2NvbnRhaW5lcl9zbWFsbCcgfSApO1xyXG5cdFx0XHR2YXIgdWlHICAgPSBlbCggJ2RpdicsIHsgJ2NsYXNzJzogJ3VpX2dyb3VwJyB9ICk7XHJcblxyXG5cdFx0XHQvLyBTcGxpdCBpbnRvIHZpc3VhbCBncm91cHM6IGZpcnN0IDIsIG5leHQgMiwgdGhlbiB0aGUgcmVzdC5cclxuXHRcdFx0dmFyIGcxID0gZWwoICdkaXYnLCB7ICdjbGFzcyc6ICd1aV9lbGVtZW50JyB9ICk7XHJcblx0XHRcdHZhciBnMiA9IGVsKCAnZGl2JywgeyAnY2xhc3MnOiAndWlfZWxlbWVudCcgfSApO1xyXG5cdFx0XHR2YXIgZzMgPSBlbCggJ2RpdicsIHsgJ2NsYXNzJzogJ3VpX2VsZW1lbnQnIH0gKTtcclxuXHJcblx0XHRcdGhlYWRlckFjdGlvbnMuc2xpY2UoIDAsIDIgKS5mb3JFYWNoKCBmdW5jdGlvbiAoYWN0KSB7XHJcblx0XHRcdFx0ZzEuYXBwZW5kQ2hpbGQoIGFjdGlvbkJ0biggYWN0LCBmYWxzZSApICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0aGVhZGVyQWN0aW9ucy5zbGljZSggMiwgNCApLmZvckVhY2goIGZ1bmN0aW9uIChhY3QpIHtcclxuXHRcdFx0XHRnMi5hcHBlbmRDaGlsZCggYWN0aW9uQnRuKCBhY3QsIGZhbHNlICkgKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHRoZWFkZXJBY3Rpb25zLnNsaWNlKCA0ICkuZm9yRWFjaCggZnVuY3Rpb24gKGFjdCkge1xyXG5cdFx0XHRcdGczLmFwcGVuZENoaWxkKCBhY3Rpb25CdG4oIGFjdCwgZmFsc2UgKSApO1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHR1aUcuYXBwZW5kQ2hpbGQoIGcxICk7XHJcblx0XHRcdHVpRy5hcHBlbmRDaGlsZCggZzIgKTtcclxuXHRcdFx0dWlHLmFwcGVuZENoaWxkKCBnMyApO1xyXG5cdFx0XHR1aUMuYXBwZW5kQ2hpbGQoIHVpRyApO1xyXG5cdFx0XHRyaWdodC5hcHBlbmRDaGlsZCggdWlDICk7XHJcblxyXG5cdFx0XHR3cmFwLmFwcGVuZENoaWxkKCBsZWZ0ICk7XHJcblx0XHRcdHdyYXAuYXBwZW5kQ2hpbGQoIHJpZ2h0ICk7XHJcblx0XHRcdHJvb3QuYXBwZW5kQ2hpbGQoIHdyYXAgKTtcclxuXHJcblx0XHRcdHJldHVybiByb290O1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHRmdW5jdGlvbiBmYWN0b3J5X3JlbmRlcihwYW5lbF9lbCwgc2NoZW1hX2Zvcl90eXBlLCBkYXRhLCBvcHRzKSB7XHJcblx0XHRcdGlmICggIXBhbmVsX2VsICkgcmV0dXJuIHBhbmVsX2VsO1xyXG5cclxuXHRcdFx0c2NoZW1hX2Zvcl90eXBlICA9IHNjaGVtYV9mb3JfdHlwZSB8fCB7fTtcclxuXHRcdFx0dmFyIHByb3BzX3NjaGVtYSA9IChzY2hlbWFfZm9yX3R5cGUuc2NoZW1hICYmIHNjaGVtYV9mb3JfdHlwZS5zY2hlbWEucHJvcHMpID8gc2NoZW1hX2Zvcl90eXBlLnNjaGVtYS5wcm9wcyA6IHt9O1xyXG5cdFx0XHR2YXIgaW5zcGVjdG9yX3VpID0gKHNjaGVtYV9mb3JfdHlwZS5pbnNwZWN0b3JfdWkgfHwge30pO1xyXG5cdFx0XHR2YXIgZ3JvdXBzICAgICAgID0gaW5zcGVjdG9yX3VpLmdyb3VwcyB8fCBbXTtcclxuXHJcblx0XHRcdHZhciBoZWFkZXJfYWN0aW9ucyA9IGluc3BlY3Rvcl91aS5oZWFkZXJfYWN0aW9ucyB8fCBzY2hlbWFfZm9yX3R5cGUuaGVhZGVyX2FjdGlvbnMgfHwgW107XHJcblx0XHRcdHZhciB0aXRsZV90ZXh0ICAgICA9IChvcHRzICYmIG9wdHMudGl0bGUpIHx8IGluc3BlY3Rvcl91aS50aXRsZSB8fCBzY2hlbWFfZm9yX3R5cGUubGFiZWwgfHwgKGRhdGEgJiYgZGF0YS5sYWJlbCkgfHwgJyc7XHJcblxyXG5cdFx0Ly8gUHJlcGFyZSByZW5kZXJpbmcgY29udGV4dCBmb3Igc2xvdHMvdmFsdWVfZnJvbSwgZXRjLlxyXG5cdFx0XHR2YXIgY3R4ID0ge1xyXG5cdFx0XHRcdGVsICAgICA6IG9wdHMgJiYgb3B0cy5lbCB8fCBudWxsLFxyXG5cdFx0XHRcdGJ1aWxkZXI6IG9wdHMgJiYgb3B0cy5idWlsZGVyIHx8IG51bGwsXHJcblx0XHRcdFx0dHlwZSAgIDogb3B0cyAmJiBvcHRzLnR5cGUgfHwgbnVsbCxcclxuXHRcdFx0XHRkYXRhICAgOiBkYXRhIHx8IHt9XHJcblx0XHRcdH07XHJcblxyXG5cdFx0XHQvLyBjbGVhciBwYW5lbC5cclxuXHRcdFx0d2hpbGUgKCBwYW5lbF9lbC5maXJzdENoaWxkICkgcGFuZWxfZWwucmVtb3ZlQ2hpbGQoIHBhbmVsX2VsLmZpcnN0Q2hpbGQgKTtcclxuXHJcblx0XHRcdHZhciB1aWQgPSBNYXRoLnJhbmRvbSgpLnRvU3RyaW5nKCAzNiApLnNsaWNlKCAyLCA4ICk7XHJcblxyXG5cdFx0XHQvLyBoZWFkZXIuXHJcblx0XHRcdHBhbmVsX2VsLmFwcGVuZENoaWxkKCBidWlsZF9oZWFkZXIoIGluc3BlY3Rvcl91aSwgdGl0bGVfdGV4dCwgc2NoZW1hX2Zvcl90eXBlICkgKTtcclxuXHJcblxyXG5cdFx0XHQvLyBncm91cHMuXHJcblx0XHRcdGdyb3Vwcy5mb3JFYWNoKCBmdW5jdGlvbiAoZykge1xyXG5cdFx0XHRcdHBhbmVsX2VsLmFwcGVuZENoaWxkKCBidWlsZF9ncm91cCggZywgcHJvcHNfc2NoZW1hLCBkYXRhIHx8IHt9LCB1aWQsIGN0eCApICk7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIEFSSUEgc3luYyBmb3IgdG9nZ2xlcyBjcmVhdGVkIGhlcmUgKGVuc3VyZSBhcmlhLWNoZWNrZWQgbWF0Y2hlcyBzdGF0ZSkuXHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0Ly8gQ2VudHJhbGl6ZWQgVUkgbm9ybWFsaXplcnMgKHRvZ2dsZXMgKyBBMTF5KTogaGFuZGxlZCBpbiBDb3JlLlxyXG5cdFx0XHRcdFVJLmFwcGx5X3Bvc3RfcmVuZGVyKCBwYW5lbF9lbCApO1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHR3aXJlX2xlbl9ncm91cCggcGFuZWxfZWwgKTtcclxuXHRcdFx0XHRcdC8vIEluaXRpYWxpemUgQ29sb3JpcyBvbiBjb2xvciBpbnB1dHMgcmVuZGVyZWQgaW4gdGhpcyBwYW5lbC5cclxuXHRcdFx0XHRcdGluaXRfY29sb3Jpc19waWNrZXJzKCBwYW5lbF9lbCApO1xyXG5cdFx0XHRcdH0gY2F0Y2ggKCBfICkgeyB9XHJcblx0XHRcdH0gY2F0Y2ggKCBfICkgeyB9XHJcblxyXG5cdFx0XHRyZXR1cm4gcGFuZWxfZWw7XHJcblx0XHR9XHJcblxyXG5cdFx0VUkuV1BCQ19CRkJfSW5zcGVjdG9yX0ZhY3RvcnkgPSB7IHJlbmRlcjogZmFjdG9yeV9yZW5kZXIgfTsgICAvLyBvdmVyd3JpdGUvcmVmcmVzaFxyXG5cclxuXHRcdC8vIC0tLS0gQnVpbHQtaW4gc2xvdCArIHZhbHVlX2Zyb20gZm9yIFNlY3Rpb25zIC0tLS1cclxuXHJcblx0XHRmdW5jdGlvbiBzbG90X2xheW91dF9jaGlwcyhob3N0LCBjdHgpIHtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHR2YXIgTCA9IHcuV1BCQ19CRkJfQ29yZSAmJiAgdy5XUEJDX0JGQl9Db3JlLlVJICYmIHcuV1BCQ19CRkJfQ29yZS5VSS5XUEJDX0JGQl9MYXlvdXRfQ2hpcHM7XHJcblx0XHRcdFx0aWYgKCBMICYmIHR5cGVvZiBMLnJlbmRlcl9mb3Jfc2VjdGlvbiA9PT0gJ2Z1bmN0aW9uJyApIHtcclxuXHRcdFx0XHRcdEwucmVuZGVyX2Zvcl9zZWN0aW9uKCBjdHguYnVpbGRlciwgY3R4LmVsLCBob3N0ICk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGhvc3QuYXBwZW5kQ2hpbGQoIGRvY3VtZW50LmNyZWF0ZVRleHROb2RlKCAnW2xheW91dF9jaGlwcyBub3QgYXZhaWxhYmxlXScgKSApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0Y29uc29sZS53YXJuKCAnd3BiY19iZmJfc2xvdF9sYXlvdXRfY2hpcHMgZmFpbGVkOicsIGUgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHcud3BiY19iZmJfaW5zcGVjdG9yX2ZhY3Rvcnlfc2xvdHMubGF5b3V0X2NoaXBzID0gc2xvdF9sYXlvdXRfY2hpcHM7XHJcblxyXG5cdFx0ZnVuY3Rpb24gdmFsdWVfZnJvbV9jb21wdXRlX3NlY3Rpb25fY29sdW1ucyhjdHgpIHtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHR2YXIgcm93ID0gY3R4ICYmIGN0eC5lbCAmJiBjdHguZWwucXVlcnlTZWxlY3RvciAmJiBjdHguZWwucXVlcnlTZWxlY3RvciggJzpzY29wZSA+IC53cGJjX2JmYl9fcm93JyApO1xyXG5cdFx0XHRcdGlmICggIXJvdyApIHJldHVybiAxO1xyXG5cdFx0XHRcdHZhciBuID0gcm93LnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2NvbHVtbicgKS5sZW5ndGggfHwgMTtcclxuXHRcdFx0XHRpZiAoIG4gPCAxICkgbiA9IDE7XHJcblx0XHRcdFx0aWYgKCBuID4gNCApIG4gPSA0O1xyXG5cdFx0XHRcdHJldHVybiBuO1xyXG5cdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRyZXR1cm4gMTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHcud3BiY19iZmJfaW5zcGVjdG9yX2ZhY3RvcnlfdmFsdWVfZnJvbS5jb21wdXRlX3NlY3Rpb25fY29sdW1ucyA9IHZhbHVlX2Zyb21fY29tcHV0ZV9zZWN0aW9uX2NvbHVtbnM7XHJcblx0fVxyXG5cclxuXHQvLyAzKSBJbnNwZWN0b3IgY2xhc3MuXHJcblxyXG5cdGNsYXNzIFdQQkNfQkZCX0luc3BlY3RvciB7XHJcblxyXG5cdFx0Y29uc3RydWN0b3IocGFuZWxfZWwsIGJ1aWxkZXIpIHtcclxuXHRcdFx0dGhpcy5wYW5lbCAgICAgICAgID0gcGFuZWxfZWwgfHwgdGhpcy5fY3JlYXRlX2ZhbGxiYWNrX3BhbmVsKCk7XHJcblx0XHRcdHRoaXMuYnVpbGRlciAgICAgICA9IGJ1aWxkZXI7XHJcblx0XHRcdHRoaXMuc2VsZWN0ZWRfZWwgICA9IG51bGw7XHJcblx0XHRcdHRoaXMuX3JlbmRlcl90aW1lciA9IG51bGw7XHJcblxyXG5cdFx0XHR0aGlzLl9vbl9kZWxlZ2F0ZWRfaW5wdXQgID0gKGUpID0+IHRoaXMuX2FwcGx5X2NvbnRyb2xfZnJvbV9ldmVudCggZSApO1xyXG5cdFx0XHR0aGlzLl9vbl9kZWxlZ2F0ZWRfY2hhbmdlID0gKGUpID0+IHRoaXMuX2FwcGx5X2NvbnRyb2xfZnJvbV9ldmVudCggZSApO1xyXG5cdFx0XHR0aGlzLnBhbmVsLmFkZEV2ZW50TGlzdGVuZXIoICdpbnB1dCcsIHRoaXMuX29uX2RlbGVnYXRlZF9pbnB1dCwgdHJ1ZSApO1xyXG5cdFx0XHR0aGlzLnBhbmVsLmFkZEV2ZW50TGlzdGVuZXIoICdjaGFuZ2UnLCB0aGlzLl9vbl9kZWxlZ2F0ZWRfY2hhbmdlLCB0cnVlICk7XHJcblxyXG5cdFx0XHR0aGlzLl9vbl9kZWxlZ2F0ZWRfY2xpY2sgPSAoZSkgPT4ge1xyXG5cdFx0XHRcdGNvbnN0IGJ0biA9IGUudGFyZ2V0LmNsb3Nlc3QoICdbZGF0YS1hY3Rpb25dJyApO1xyXG5cdFx0XHRcdGlmICggIWJ0biB8fCAhdGhpcy5wYW5lbC5jb250YWlucyggYnRuICkgKSByZXR1cm47XHJcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblxyXG5cdFx0XHRcdGNvbnN0IGFjdGlvbiA9IGJ0bi5nZXRBdHRyaWJ1dGUoICdkYXRhLWFjdGlvbicgKTtcclxuXHRcdFx0XHRjb25zdCBlbCAgICAgPSB0aGlzLnNlbGVjdGVkX2VsO1xyXG5cdFx0XHRcdGlmICggIWVsICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0XHR3LldQQkNfQkZCX0luc3BlY3Rvcl9BY3Rpb25zPy5ydW4oIGFjdGlvbiwge1xyXG5cdFx0XHRcdFx0YnVpbGRlcjogdGhpcy5idWlsZGVyLFxyXG5cdFx0XHRcdFx0ZWwsXHJcblx0XHRcdFx0XHRwYW5lbCAgOiB0aGlzLnBhbmVsLFxyXG5cdFx0XHRcdFx0ZXZlbnQgIDogZVxyXG5cdFx0XHRcdH0gKTtcclxuXHJcblx0XHRcdFx0aWYgKCBhY3Rpb24gPT09ICdkZWxldGUnICkgdGhpcy5jbGVhcigpO1xyXG5cdFx0XHR9O1xyXG5cdFx0XHR0aGlzLnBhbmVsLmFkZEV2ZW50TGlzdGVuZXIoICdjbGljaycsIHRoaXMuX29uX2RlbGVnYXRlZF9jbGljayApO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9wb3N0X3JlbmRlcl91aSgpIHtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHR2YXIgVUkgPSB3LldQQkNfQkZCX0NvcmUgJiYgdy5XUEJDX0JGQl9Db3JlLlVJO1xyXG5cdFx0XHRcdGlmICggVUkgJiYgdHlwZW9mIFVJLmFwcGx5X3Bvc3RfcmVuZGVyID09PSAnZnVuY3Rpb24nICkge1xyXG5cdFx0XHRcdFx0VUkuYXBwbHlfcG9zdF9yZW5kZXIoIHRoaXMucGFuZWwgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0Ly8gTkVXOiB3aXJlIHNsaWRlci9udW1iZXIvdW5pdCBzeW5jaW5nIGZvciBsZW5ndGggJiByYW5nZV9udW1iZXIgZ3JvdXBzLlxyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHR3aXJlX2xlbl9ncm91cCggdGhpcy5wYW5lbCApO1xyXG5cdFx0XHRcdFx0aW5pdF9jb2xvcmlzX3BpY2tlcnMoIHRoaXMucGFuZWwgKTtcclxuXHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHRcdF93cGJjPy5kZXY/LmVycm9yPy4oICdpbnNwZWN0b3IuX3Bvc3RfcmVuZGVyX3VpJywgZSApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdF9hcHBseV9jb250cm9sX2Zyb21fZXZlbnQoZSkge1xyXG5cdFx0XHRpZiAoICF0aGlzLnBhbmVsLmNvbnRhaW5zKCBlLnRhcmdldCApICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgdCAgID0gLyoqIEB0eXBlIHtIVE1MSW5wdXRFbGVtZW50fEhUTUxUZXh0QXJlYUVsZW1lbnR8SFRNTFNlbGVjdEVsZW1lbnR9ICovIChlLnRhcmdldCk7XHJcblx0XHRcdGNvbnN0IGtleSA9IHQ/LmRhdGFzZXQ/Lmluc3BlY3RvcktleTtcclxuXHRcdFx0aWYgKCAha2V5ICkgcmV0dXJuO1xyXG5cclxuXHRcdFx0Y29uc3QgZWwgPSB0aGlzLnNlbGVjdGVkX2VsO1xyXG5cdFx0XHRpZiAoICFlbCB8fCAhZG9jdW1lbnQuYm9keS5jb250YWlucyggZWwgKSApIHJldHVybjtcclxuXHJcblx0XHRcdGxldCB2O1xyXG5cdFx0XHRpZiAoIHQgaW5zdGFuY2VvZiBIVE1MSW5wdXRFbGVtZW50ICYmIHQudHlwZSA9PT0gJ2NoZWNrYm94JyApIHtcclxuXHRcdFx0XHR2ID0gISF0LmNoZWNrZWQ7XHJcblx0XHRcdFx0dC5zZXRBdHRyaWJ1dGUoICdhcmlhLWNoZWNrZWQnLCB2ID8gJ3RydWUnIDogJ2ZhbHNlJyApOyAgICAgICAgIC8vIEtlZXAgQVJJQSBzdGF0ZSBpbiBzeW5jIGZvciB0b2dnbGVzIChzY2hlbWEgYW5kIHRlbXBsYXRlIHBhdGhzKS5cclxuXHRcdFx0fSBlbHNlIGlmICggdCBpbnN0YW5jZW9mIEhUTUxJbnB1dEVsZW1lbnQgJiYgdC50eXBlID09PSAnbnVtYmVyJyApIHtcclxuXHRcdFx0XHR2ID0gKHQudmFsdWUgPT09ICcnID8gJycgOiBOdW1iZXIoIHQudmFsdWUgKSk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0diA9IHQudmFsdWU7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICgga2V5ID09PSAnaWQnICkge1xyXG5cdFx0XHRcdGNvbnN0IHVuaXF1ZSA9IHRoaXMuYnVpbGRlcj8uaWQ/LnNldF9maWVsZF9pZD8uKCBlbCwgdiApO1xyXG5cdFx0XHRcdGlmICggdW5pcXVlICE9IG51bGwgJiYgdC52YWx1ZSAhPT0gdW5pcXVlICkgdC52YWx1ZSA9IHVuaXF1ZTtcclxuXHJcblx0XHRcdH0gZWxzZSBpZiAoIGtleSA9PT0gJ25hbWUnICkge1xyXG5cdFx0XHRcdGNvbnN0IHVuaXF1ZSA9IHRoaXMuYnVpbGRlcj8uaWQ/LnNldF9maWVsZF9uYW1lPy4oIGVsLCB2ICk7XHJcblx0XHRcdFx0aWYgKCB1bmlxdWUgIT0gbnVsbCAmJiB0LnZhbHVlICE9PSB1bmlxdWUgKSB0LnZhbHVlID0gdW5pcXVlO1xyXG5cclxuXHRcdFx0fSBlbHNlIGlmICgga2V5ID09PSAnaHRtbF9pZCcgKSB7XHJcblx0XHRcdFx0Y29uc3QgYXBwbGllZCA9IHRoaXMuYnVpbGRlcj8uaWQ/LnNldF9maWVsZF9odG1sX2lkPy4oIGVsLCB2ICk7XHJcblx0XHRcdFx0aWYgKCBhcHBsaWVkICE9IG51bGwgJiYgdC52YWx1ZSAhPT0gYXBwbGllZCApIHQudmFsdWUgPSBhcHBsaWVkO1xyXG5cclxuXHRcdFx0fSBlbHNlIGlmICgga2V5ID09PSAnY29sdW1ucycgJiYgZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgKSB7XHJcblx0XHRcdFx0Y29uc3Qgdl9pbnQgPSBwYXJzZUludCggU3RyaW5nKCB2ICksIDEwICk7XHJcblx0XHRcdFx0aWYgKCBOdW1iZXIuaXNGaW5pdGUoIHZfaW50ICkgKSB7XHJcblx0XHRcdFx0XHRjb25zdCBjbGFtcGVkID0gdy5XUEJDX0JGQl9Db3JlLldQQkNfQkZCX1Nhbml0aXplLmNsYW1wKCB2X2ludCwgMSwgNCApO1xyXG5cdFx0XHRcdFx0dGhpcy5idWlsZGVyPy5zZXRfc2VjdGlvbl9jb2x1bW5zPy4oIGVsLCBjbGFtcGVkICk7XHJcblx0XHRcdFx0XHRpZiAoIFN0cmluZyggY2xhbXBlZCApICE9PSB0LnZhbHVlICkgdC52YWx1ZSA9IFN0cmluZyggY2xhbXBlZCApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0aWYgKCB0IGluc3RhbmNlb2YgSFRNTElucHV0RWxlbWVudCAmJiB0LnR5cGUgPT09ICdjaGVja2JveCcgKSB7XHJcblx0XHRcdFx0XHRlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLScgKyBrZXksIFN0cmluZyggISF2ICkgKTtcclxuXHRcdFx0XHR9IGVsc2UgaWYgKCB0IGluc3RhbmNlb2YgSFRNTElucHV0RWxlbWVudCAmJiB0LnR5cGUgPT09ICdudW1iZXInICkge1xyXG5cdFx0XHRcdFx0aWYgKCB0LnZhbHVlID09PSAnJyB8fCAhTnVtYmVyLmlzRmluaXRlKCB2ICkgKSB7XHJcblx0XHRcdFx0XHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSggJ2RhdGEtJyArIGtleSApO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0ZWwuc2V0QXR0cmlidXRlKCAnZGF0YS0nICsga2V5LCBTdHJpbmcoIHYgKSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0gZWxzZSBpZiAoIHYgPT0gbnVsbCApIHtcclxuXHRcdFx0XHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSggJ2RhdGEtJyArIGtleSApO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRlbC5zZXRBdHRyaWJ1dGUoICdkYXRhLScgKyBrZXksICh0eXBlb2YgdiA9PT0gJ29iamVjdCcpID8gSlNPTi5zdHJpbmdpZnkoIHYgKSA6IFN0cmluZyggdiApICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBVcGRhdGUgcHJldmlldy9vdmVybGF5XHJcblx0XHRcdGlmICggZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX2ZpZWxkJyApICkge1xyXG5cdFx0XHRcdGlmICggdGhpcy5idWlsZGVyPy5wcmV2aWV3X21vZGUgKSB0aGlzLmJ1aWxkZXIucmVuZGVyX3ByZXZpZXcoIGVsICk7XHJcblx0XHRcdFx0ZWxzZSB0aGlzLmJ1aWxkZXIuYWRkX292ZXJsYXlfdG9vbGJhciggZWwgKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHR0aGlzLmJ1aWxkZXIuYWRkX292ZXJsYXlfdG9vbGJhciggZWwgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCB0aGlzLl9uZWVkc19yZXJlbmRlciggZWwsIGtleSwgZSApICkge1xyXG5cdFx0XHRcdHRoaXMuX3NjaGVkdWxlX3JlbmRlcl9wcmVzZXJ2aW5nX2ZvY3VzKCAwICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRfbmVlZHNfcmVyZW5kZXIoZWwsIGtleSwgX2UpIHtcclxuXHRcdFx0aWYgKCBlbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fc2VjdGlvbicgKSAmJiBrZXkgPT09ICdjb2x1bW5zJyApIHJldHVybiB0cnVlO1xyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0YmluZF90b19maWVsZChmaWVsZF9lbCkge1xyXG5cdFx0XHR0aGlzLnNlbGVjdGVkX2VsID0gZmllbGRfZWw7XHJcblx0XHRcdHRoaXMucmVuZGVyKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0Y2xlYXIoKSB7XHJcblx0XHRcdHRoaXMuc2VsZWN0ZWRfZWwgPSBudWxsO1xyXG5cdFx0XHRpZiAoIHRoaXMuX3JlbmRlcl90aW1lciApIHtcclxuXHRcdFx0XHRjbGVhclRpbWVvdXQoIHRoaXMuX3JlbmRlcl90aW1lciApO1xyXG5cdFx0XHRcdHRoaXMuX3JlbmRlcl90aW1lciA9IG51bGw7XHJcblx0XHRcdH1cclxuXHRcdFx0Ly8gQWxzbyBjbGVhciB0aGUgc2VjdGlvbi1jb2xzIGhpbnQgb24gZW1wdHkgc3RhdGUuXHJcblx0XHRcdHRoaXMucGFuZWwucmVtb3ZlQXR0cmlidXRlKCdkYXRhLWJmYi1zZWN0aW9uLWNvbHMnKTtcclxuXHRcdFx0dGhpcy5wYW5lbC5pbm5lckhUTUwgPSAnPGRpdiBjbGFzcz1cIndwYmNfYmZiX19pbnNwZWN0b3JfX2VtcHR5XCI+U2VsZWN0IGEgZmllbGQgdG8gZWRpdCBpdHMgb3B0aW9ucy48L2Rpdj4nO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9zY2hlZHVsZV9yZW5kZXJfcHJlc2VydmluZ19mb2N1cyhkZWxheSA9IDIwMCkge1xyXG5cdFx0XHRjb25zdCBhY3RpdmUgICAgPSAvKiogQHR5cGUge0hUTUxJbnB1dEVsZW1lbnR8SFRNTFRleHRBcmVhRWxlbWVudHxIVE1MRWxlbWVudHxudWxsfSAqLyAoZG9jdW1lbnQuYWN0aXZlRWxlbWVudCk7XHJcblx0XHRcdGNvbnN0IGFjdGl2ZUtleSA9IGFjdGl2ZT8uZGF0YXNldD8uaW5zcGVjdG9yS2V5IHx8IG51bGw7XHJcblx0XHRcdGxldCBzZWxTdGFydCAgICA9IG51bGwsIHNlbEVuZCA9IG51bGw7XHJcblxyXG5cdFx0XHRpZiAoIGFjdGl2ZSAmJiAnc2VsZWN0aW9uU3RhcnQnIGluIGFjdGl2ZSAmJiAnc2VsZWN0aW9uRW5kJyBpbiBhY3RpdmUgKSB7XHJcblx0XHRcdFx0Ly8gQHRzLWlnbm9yZVxyXG5cdFx0XHRcdHNlbFN0YXJ0ID0gYWN0aXZlLnNlbGVjdGlvblN0YXJ0O1xyXG5cdFx0XHRcdC8vIEB0cy1pZ25vcmVcclxuXHRcdFx0XHRzZWxFbmQgICA9IGFjdGl2ZS5zZWxlY3Rpb25FbmQ7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlmICggdGhpcy5fcmVuZGVyX3RpbWVyICkgY2xlYXJUaW1lb3V0KCB0aGlzLl9yZW5kZXJfdGltZXIgKTtcclxuXHRcdFx0dGhpcy5fcmVuZGVyX3RpbWVyID0gLyoqIEB0eXBlIHt1bmtub3dufSAqLyAoc2V0VGltZW91dCggKCkgPT4ge1xyXG5cdFx0XHRcdHRoaXMucmVuZGVyKCk7XHJcblx0XHRcdFx0aWYgKCBhY3RpdmVLZXkgKSB7XHJcblx0XHRcdFx0XHRjb25zdCBuZXh0ID0gLyoqIEB0eXBlIHtIVE1MSW5wdXRFbGVtZW50fEhUTUxUZXh0QXJlYUVsZW1lbnR8SFRNTEVsZW1lbnR8bnVsbH0gKi8gKFxyXG5cdFx0XHRcdFx0XHR0aGlzLnBhbmVsLnF1ZXJ5U2VsZWN0b3IoIGBbZGF0YS1pbnNwZWN0b3Ita2V5PVwiJHthY3RpdmVLZXl9XCJdYCApXHJcblx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0aWYgKCBuZXh0ICkge1xyXG5cdFx0XHRcdFx0XHRuZXh0LmZvY3VzKCk7XHJcblx0XHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdFx0aWYgKCBzZWxTdGFydCAhPSBudWxsICYmIHNlbEVuZCAhPSBudWxsICYmIHR5cGVvZiBuZXh0LnNldFNlbGVjdGlvblJhbmdlID09PSAnZnVuY3Rpb24nICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gQHRzLWlnbm9yZVxyXG5cdFx0XHRcdFx0XHRcdFx0bmV4dC5zZXRTZWxlY3Rpb25SYW5nZSggc2VsU3RhcnQsIHNlbEVuZCApO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0fSBjYXRjaCggZSApeyBfd3BiYz8uZGV2Py5lcnJvciggJ19yZW5kZXJfdGltZXInLCBlICk7IH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdH0sIGRlbGF5ICkpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJlbmRlcigpIHtcclxuXHJcblx0XHRcdGNvbnN0IGVsID0gdGhpcy5zZWxlY3RlZF9lbDtcclxuXHRcdFx0aWYgKCAhZWwgfHwgIWRvY3VtZW50LmJvZHkuY29udGFpbnMoIGVsICkgKSByZXR1cm4gdGhpcy5jbGVhcigpO1xyXG5cclxuXHRcdFx0Ly8gUmVzZXQgc2VjdGlvbi1jb2xzIGhpbnQgdW5sZXNzIHdlIHNldCBpdCBsYXRlciBmb3IgYSBzZWN0aW9uLlxyXG5cdFx0XHR0aGlzLnBhbmVsLnJlbW92ZUF0dHJpYnV0ZSggJ2RhdGEtYmZiLXNlY3Rpb24tY29scycgKTtcclxuXHJcblx0XHRcdGNvbnN0IHByZXZfc2Nyb2xsID0gdGhpcy5wYW5lbC5zY3JvbGxUb3A7XHJcblxyXG5cdFx0XHQvLyBTZWN0aW9uXHJcblx0XHRcdGlmICggZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCAnd3BiY19iZmJfX3NlY3Rpb24nICkgKSB7XHJcblx0XHRcdFx0bGV0IHRwbCA9IG51bGw7XHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdHRwbCA9ICh3LndwICYmIHdwLnRlbXBsYXRlICYmIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAndG1wbC13cGJjLWJmYi1pbnNwZWN0b3Itc2VjdGlvbicgKSkgPyB3cC50ZW1wbGF0ZSggJ3dwYmMtYmZiLWluc3BlY3Rvci1zZWN0aW9uJyApIDogbnVsbDtcclxuXHRcdFx0XHR9IGNhdGNoICggXyApIHtcclxuXHRcdFx0XHRcdHRwbCA9IG51bGw7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpZiAoIHRwbCApIHtcclxuXHRcdFx0XHRcdHRoaXMucGFuZWwuaW5uZXJIVE1MID0gdHBsKCB7fSApO1xyXG5cdFx0XHRcdFx0dGhpcy5fZW5mb3JjZV9kZWZhdWx0X2dyb3VwX29wZW4oKTtcclxuXHRcdFx0XHRcdHRoaXMuX3NldF9wYW5lbF9zZWN0aW9uX2NvbHMoIGVsICk7XHJcblx0XHRcdFx0XHR0aGlzLl9wb3N0X3JlbmRlcl91aSgpO1xyXG5cdFx0XHRcdFx0dGhpcy5wYW5lbC5zY3JvbGxUb3AgPSBwcmV2X3Njcm9sbDtcclxuXHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGNvbnN0IEZhY3RvcnkgPSB3LldQQkNfQkZCX0NvcmUuVUkgJiYgdy5XUEJDX0JGQl9Db3JlLlVJLldQQkNfQkZCX0luc3BlY3Rvcl9GYWN0b3J5O1xyXG5cdFx0XHRcdGNvbnN0IHNjaGVtYXMgPSB3LldQQkNfQkZCX1NjaGVtYXMgfHwge307XHJcblx0XHRcdFx0Y29uc3QgZW50cnkgICA9IHNjaGVtYXNbJ3NlY3Rpb24nXSB8fCBudWxsO1xyXG5cdFx0XHRcdGlmICggZW50cnkgJiYgRmFjdG9yeSApIHtcclxuXHRcdFx0XHRcdHRoaXMucGFuZWwuaW5uZXJIVE1MID0gJyc7XHJcblx0XHRcdFx0XHRGYWN0b3J5LnJlbmRlcihcclxuXHRcdFx0XHRcdFx0dGhpcy5wYW5lbCxcclxuXHRcdFx0XHRcdFx0ZW50cnksXHJcblx0XHRcdFx0XHRcdHt9LFxyXG5cdFx0XHRcdFx0XHR7IGVsLCBidWlsZGVyOiB0aGlzLmJ1aWxkZXIsIHR5cGU6ICdzZWN0aW9uJywgdGl0bGU6IGVudHJ5LmxhYmVsIHx8ICdTZWN0aW9uJyB9XHJcblx0XHRcdFx0XHQpO1xyXG5cdFx0XHRcdFx0dGhpcy5fZW5mb3JjZV9kZWZhdWx0X2dyb3VwX29wZW4oKTtcclxuXHJcblx0XHRcdFx0XHQvLyAtLS0gU2FmZXR5IG5ldDogaWYgZm9yIGFueSByZWFzb24gdGhlIHNsb3QgZGlkbuKAmXQgcmVuZGVyIGNoaXBzLCBpbmplY3QgdGhlbSBub3cuXHJcblx0XHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0XHRjb25zdCBoYXNTbG90SG9zdCA9XHJcblx0XHRcdFx0XHRcdFx0XHQgIHRoaXMucGFuZWwucXVlcnlTZWxlY3RvciggJ1tkYXRhLWJmYi1zbG90PVwibGF5b3V0X2NoaXBzXCJdJyApIHx8XHJcblx0XHRcdFx0XHRcdFx0XHQgIHRoaXMucGFuZWwucXVlcnlTZWxlY3RvciggJy5pbnNwZWN0b3JfX3Jvdy0tbGF5b3V0LWNoaXBzIC53cGJjX2JmYl9fbGF5b3V0X2NoaXBzJyApIHx8XHJcblx0XHRcdFx0XHRcdFx0XHQgIHRoaXMucGFuZWwucXVlcnlTZWxlY3RvciggJyN3cGJjX2JmYl9fbGF5b3V0X2NoaXBzX2hvc3QnICk7XHJcblxyXG5cdFx0XHRcdFx0XHRjb25zdCBoYXNDaGlwcyA9XHJcblx0XHRcdFx0XHRcdFx0XHQgICEhdGhpcy5wYW5lbC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19sYXlvdXRfY2hpcCcgKTtcclxuXHJcblx0XHRcdFx0XHRcdGlmICggIWhhc0NoaXBzICkge1xyXG5cdFx0XHRcdFx0XHRcdC8vIENyZWF0ZSBhIGhvc3QgaWYgbWlzc2luZyBhbmQgcmVuZGVyIGNoaXBzIGludG8gaXQuXHJcblx0XHRcdFx0XHRcdFx0Y29uc3QgaG9zdCA9IChmdW5jdGlvbiBlbnN1cmVIb3N0KHJvb3QpIHtcclxuXHRcdFx0XHRcdFx0XHRcdGxldCBoID1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRyb290LnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1iZmItc2xvdD1cImxheW91dF9jaGlwc1wiXScgKSB8fFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHJvb3QucXVlcnlTZWxlY3RvciggJy5pbnNwZWN0b3JfX3Jvdy0tbGF5b3V0LWNoaXBzIC53cGJjX2JmYl9fbGF5b3V0X2NoaXBzJyApIHx8XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0cm9vdC5xdWVyeVNlbGVjdG9yKCAnI3dwYmNfYmZiX19sYXlvdXRfY2hpcHNfaG9zdCcgKTtcclxuXHRcdFx0XHRcdFx0XHRcdGlmICggaCApIHJldHVybiBoO1xyXG5cdFx0XHRcdFx0XHRcdFx0Ly8gRmFsbGJhY2sgaG9zdCBpbnNpZGUgKG9yIGFmdGVyKSB0aGUg4oCcbGF5b3V04oCdIGdyb3VwXHJcblx0XHRcdFx0XHRcdFx0XHRjb25zdCBmaWVsZHMgICAgPVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCAgcm9vdC5xdWVyeVNlbGVjdG9yKCAnLndwYmNfYmZiX19pbnNwZWN0b3JfX2dyb3VwW2RhdGEtZ3JvdXA9XCJsYXlvdXRcIl0gLmdyb3VwX19maWVsZHMnICkgfHxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQgIHJvb3QucXVlcnlTZWxlY3RvciggJy5ncm91cF9fZmllbGRzJyApIHx8IHJvb3Q7XHJcblx0XHRcdFx0XHRcdFx0XHRjb25zdCByb3cgICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0cm93LmNsYXNzTmFtZSAgID0gJ2luc3BlY3Rvcl9fcm93IGluc3BlY3Rvcl9fcm93LS1sYXlvdXQtY2hpcHMnO1xyXG5cdFx0XHRcdFx0XHRcdFx0Y29uc3QgbGFiICAgICAgID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2xhYmVsJyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0bGFiLmNsYXNzTmFtZSAgID0gJ2luc3BlY3Rvcl9fbGFiZWwnO1xyXG5cdFx0XHRcdFx0XHRcdFx0bGFiLnRleHRDb250ZW50ID0gJ0xheW91dCc7XHJcblx0XHRcdFx0XHRcdFx0XHRjb25zdCBjdGwgICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0Y3RsLmNsYXNzTmFtZSAgID0gJ2luc3BlY3Rvcl9fY29udHJvbCc7XHJcblx0XHRcdFx0XHRcdFx0XHRoICAgICAgICAgICAgICAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0aC5jbGFzc05hbWUgICAgID0gJ3dwYmNfYmZiX19sYXlvdXRfY2hpcHMnO1xyXG5cdFx0XHRcdFx0XHRcdFx0aC5zZXRBdHRyaWJ1dGUoICdkYXRhLWJmYi1zbG90JywgJ2xheW91dF9jaGlwcycgKTtcclxuXHRcdFx0XHRcdFx0XHRcdGN0bC5hcHBlbmRDaGlsZCggaCApO1xyXG5cdFx0XHRcdFx0XHRcdFx0cm93LmFwcGVuZENoaWxkKCBsYWIgKTtcclxuXHRcdFx0XHRcdFx0XHRcdHJvdy5hcHBlbmRDaGlsZCggY3RsICk7XHJcblx0XHRcdFx0XHRcdFx0XHRmaWVsZHMuYXBwZW5kQ2hpbGQoIHJvdyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0cmV0dXJuIGg7XHJcblx0XHRcdFx0XHRcdFx0fSkoIHRoaXMucGFuZWwgKTtcclxuXHJcblx0XHRcdFx0XHRcdFx0Y29uc3QgTCA9ICh3LldQQkNfQkZCX0NvcmUgJiYgdy5XUEJDX0JGQl9Db3JlLlVJICYmIHcuV1BCQ19CRkJfQ29yZS5VSS5XUEJDX0JGQl9MYXlvdXRfQ2hpcHMpIDtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIEwgJiYgdHlwZW9mIEwucmVuZGVyX2Zvcl9zZWN0aW9uID09PSAnZnVuY3Rpb24nICkge1xyXG5cdFx0XHRcdFx0XHRcdFx0aG9zdC5pbm5lckhUTUwgPSAnJztcclxuXHRcdFx0XHRcdFx0XHRcdEwucmVuZGVyX2Zvcl9zZWN0aW9uKCB0aGlzLmJ1aWxkZXIsIGVsLCBob3N0ICk7XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9IGNhdGNoKCBlICl7IF93cGJjPy5kZXY/LmVycm9yKCAnV1BCQ19CRkJfSW5zcGVjdG9yIC0gcmVuZGVyJywgZSApOyB9XHJcblxyXG5cdFx0XHRcdFx0dGhpcy5fc2V0X3BhbmVsX3NlY3Rpb25fY29scyggZWwgKTtcclxuXHRcdFx0XHRcdHRoaXMucGFuZWwuc2Nyb2xsVG9wID0gcHJldl9zY3JvbGw7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHR0aGlzLnBhbmVsLmlubmVySFRNTCA9ICc8ZGl2IGNsYXNzPVwid3BiY19iZmJfX2luc3BlY3Rvcl9fZW1wdHlcIj5TZWxlY3QgYSBmaWVsZCB0byBlZGl0IGl0cyBvcHRpb25zLjwvZGl2Pic7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBGaWVsZFxyXG5cdFx0XHRpZiAoICFlbC5jbGFzc0xpc3QuY29udGFpbnMoICd3cGJjX2JmYl9fZmllbGQnICkgKSByZXR1cm4gdGhpcy5jbGVhcigpO1xyXG5cclxuXHRcdFx0Y29uc3QgZGF0YSA9IHcuV1BCQ19CRkJfQ29yZS5XUEJDX0Zvcm1fQnVpbGRlcl9IZWxwZXIuZ2V0X2FsbF9kYXRhX2F0dHJpYnV0ZXMoIGVsICk7XHJcblx0XHRcdGNvbnN0IHR5cGUgPSBkYXRhLnR5cGUgfHwgJ3RleHQnO1xyXG5cclxuXHRcdFx0ZnVuY3Rpb24gX2dldF90cGwoaWQpIHtcclxuXHRcdFx0XHRpZiAoICF3LndwIHx8ICF3cC50ZW1wbGF0ZSApIHJldHVybiBudWxsO1xyXG5cdFx0XHRcdGlmICggIWRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAndG1wbC0nICsgaWQgKSApIHJldHVybiBudWxsO1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gd3AudGVtcGxhdGUoIGlkICk7XHJcblx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gbnVsbDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGNvbnN0IHRwbF9pZCAgICAgID0gYHdwYmMtYmZiLWluc3BlY3Rvci0ke3R5cGV9YDtcclxuXHRcdFx0Y29uc3QgdHBsICAgICAgICAgPSBfZ2V0X3RwbCggdHBsX2lkICk7XHJcblx0XHRcdGNvbnN0IGdlbmVyaWNfdHBsID0gX2dldF90cGwoICd3cGJjLWJmYi1pbnNwZWN0b3ItZ2VuZXJpYycgKTtcclxuXHJcblx0XHRcdGNvbnN0IHNjaGVtYXMgICAgICAgICA9IHcuV1BCQ19CRkJfU2NoZW1hcyB8fCB7fTtcclxuXHRcdFx0Y29uc3Qgc2NoZW1hX2Zvcl90eXBlID0gc2NoZW1hc1t0eXBlXSB8fCBudWxsO1xyXG5cdFx0XHRjb25zdCBGYWN0b3J5ICAgICAgICAgPSB3LldQQkNfQkZCX0NvcmUuVUkgJiYgdy5XUEJDX0JGQl9Db3JlLlVJLldQQkNfQkZCX0luc3BlY3Rvcl9GYWN0b3J5O1xyXG5cclxuXHRcdFx0aWYgKCB0cGwgKSB7XHJcblx0XHRcdFx0Ly8gTkVXOiBtZXJnZSBzY2hlbWEgZGVmYXVsdHMgc28gbWlzc2luZyBrZXlzIChlc3AuIGJvb2xlYW5zKSBob25vciBkZWZhdWx0cyBvbiBmaXJzdCBwYWludFxyXG5cdFx0XHRcdGNvbnN0IGhhc093biA9IEZ1bmN0aW9uLmNhbGwuYmluZCggT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eSApO1xyXG5cdFx0XHRcdGNvbnN0IHByb3BzICA9IChzY2hlbWFfZm9yX3R5cGUgJiYgc2NoZW1hX2Zvcl90eXBlLnNjaGVtYSAmJiBzY2hlbWFfZm9yX3R5cGUuc2NoZW1hLnByb3BzKSA/IHNjaGVtYV9mb3JfdHlwZS5zY2hlbWEucHJvcHMgOiB7fTtcclxuXHRcdFx0XHRjb25zdCBtZXJnZWQgPSB7IC4uLmRhdGEgfTtcclxuXHRcdFx0XHRpZiAoIHByb3BzICkge1xyXG5cdFx0XHRcdFx0T2JqZWN0LmtleXMoIHByb3BzICkuZm9yRWFjaCggKGspID0+IHtcclxuXHRcdFx0XHRcdFx0Y29uc3QgbWV0YSA9IHByb3BzW2tdIHx8IHt9O1xyXG5cdFx0XHRcdFx0XHRpZiAoICFoYXNPd24oIGRhdGEsIGsgKSB8fCBkYXRhW2tdID09PSAnJyApIHtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGhhc093biggbWV0YSwgJ2RlZmF1bHQnICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0XHQvLyBDb2VyY2UgYm9vbGVhbnMgdG8gYSByZWFsIGJvb2xlYW47IGxlYXZlIG90aGVycyBhcy1pc1xyXG5cdFx0XHRcdFx0XHRcdFx0bWVyZ2VkW2tdID0gKG1ldGEudHlwZSA9PT0gJ2Jvb2xlYW4nKSA/ICEhbWV0YS5kZWZhdWx0IDogbWV0YS5kZWZhdWx0O1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0fSBlbHNlIGlmICggbWV0YS50eXBlID09PSAnYm9vbGVhbicgKSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gTm9ybWFsaXplIHRydXRoeSBzdHJpbmdzIGludG8gYm9vbGVhbnMgZm9yIHRlbXBsYXRlcyB0aGF0IGNoZWNrIG9uIHRydXRoaW5lc3NcclxuXHRcdFx0XHRcdFx0XHRjb25zdCB2ICAgPSBkYXRhW2tdO1xyXG5cdFx0XHRcdFx0XHRcdG1lcmdlZFtrXSA9ICh2ID09PSB0cnVlIHx8IHYgPT09ICd0cnVlJyB8fCB2ID09PSAxIHx8IHYgPT09ICcxJyk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0dGhpcy5wYW5lbC5pbm5lckhUTUwgPSB0cGwoIG1lcmdlZCApO1xyXG5cclxuXHRcdFx0XHR0aGlzLl9wb3N0X3JlbmRlcl91aSgpO1xyXG5cdFx0XHR9IGVsc2UgaWYgKCBzY2hlbWFfZm9yX3R5cGUgJiYgRmFjdG9yeSApIHtcclxuXHRcdFx0XHR0aGlzLnBhbmVsLmlubmVySFRNTCA9ICcnO1xyXG5cdFx0XHRcdEZhY3RvcnkucmVuZGVyKFxyXG5cdFx0XHRcdFx0dGhpcy5wYW5lbCxcclxuXHRcdFx0XHRcdHNjaGVtYV9mb3JfdHlwZSxcclxuXHRcdFx0XHRcdHsgLi4uZGF0YSB9LFxyXG5cdFx0XHRcdFx0eyBlbCwgYnVpbGRlcjogdGhpcy5idWlsZGVyLCB0eXBlLCB0aXRsZTogZGF0YS5sYWJlbCB8fCAnJyB9XHJcblx0XHRcdFx0KTtcclxuXHRcdFx0XHQvLyBFbnN1cmUgdG9nZ2xlIG5vcm1hbGl6ZXJzIGFuZCBzbGlkZXIvbnVtYmVyL3VuaXQgd2lyaW5nIGFyZSBhdHRhY2hlZC5cclxuXHRcdFx0XHR0aGlzLl9wb3N0X3JlbmRlcl91aSgpO1xyXG5cdFx0XHR9IGVsc2UgaWYgKCBnZW5lcmljX3RwbCApIHtcclxuXHRcdFx0XHR0aGlzLnBhbmVsLmlubmVySFRNTCA9IGdlbmVyaWNfdHBsKCB7IC4uLmRhdGEgfSApO1xyXG5cdFx0XHRcdHRoaXMuX3Bvc3RfcmVuZGVyX3VpKCk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHRcdGNvbnN0IG1zZyAgICAgICAgICAgID0gYFRoZXJlIGFyZSBubyBJbnNwZWN0b3Igd3AudGVtcGxhdGUgXCIke3RwbF9pZH1cIiBvciBTY2hlbWEgZm9yIHRoaXMgXCIke1N0cmluZyggdHlwZSB8fCAnJyApfVwiIGVsZW1lbnQuYDtcclxuXHRcdFx0XHR0aGlzLnBhbmVsLmlubmVySFRNTCA9ICcnO1xyXG5cdFx0XHRcdGNvbnN0IGRpdiAgICAgICAgICAgID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2RpdicgKTtcclxuXHRcdFx0XHRkaXYuY2xhc3NOYW1lICAgICAgICA9ICd3cGJjX2JmYl9faW5zcGVjdG9yX19lbXB0eSc7XHJcblx0XHRcdFx0ZGl2LnRleHRDb250ZW50ICAgICAgPSBtc2c7IC8vIHNhZmUuXHJcblx0XHRcdFx0dGhpcy5wYW5lbC5hcHBlbmRDaGlsZCggZGl2ICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHRoaXMuX2VuZm9yY2VfZGVmYXVsdF9ncm91cF9vcGVuKCk7XHJcblx0XHRcdHRoaXMucGFuZWwuc2Nyb2xsVG9wID0gcHJldl9zY3JvbGw7XHJcblx0XHR9XHJcblxyXG5cdFx0X2VuZm9yY2VfZGVmYXVsdF9ncm91cF9vcGVuKCkge1xyXG5cdFx0XHRjb25zdCBncm91cHMgPSBBcnJheS5mcm9tKCB0aGlzLnBhbmVsLnF1ZXJ5U2VsZWN0b3JBbGwoICcud3BiY19iZmJfX2luc3BlY3Rvcl9fZ3JvdXAnICkgKTtcclxuXHRcdFx0aWYgKCAhZ3JvdXBzLmxlbmd0aCApIHJldHVybjtcclxuXHJcblx0XHRcdGxldCBmb3VuZCA9IGZhbHNlO1xyXG5cdFx0XHRncm91cHMuZm9yRWFjaCggKGcpID0+IHtcclxuXHRcdFx0XHRpZiAoICFmb3VuZCAmJiBnLmNsYXNzTGlzdC5jb250YWlucyggJ2lzLW9wZW4nICkgKSB7XHJcblx0XHRcdFx0XHRmb3VuZCA9IHRydWU7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGlmICggZy5jbGFzc0xpc3QuY29udGFpbnMoICdpcy1vcGVuJyApICkge1xyXG5cdFx0XHRcdFx0XHRnLmNsYXNzTGlzdC5yZW1vdmUoICdpcy1vcGVuJyApO1xyXG5cdFx0XHRcdFx0XHRnLmRpc3BhdGNoRXZlbnQoIG5ldyBFdmVudCggJ3dwYmM6Y29sbGFwc2libGU6Y2xvc2UnLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRnLmNsYXNzTGlzdC5yZW1vdmUoICdpcy1vcGVuJyApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0aWYgKCAhZm91bmQgKSB7XHJcblx0XHRcdFx0Z3JvdXBzWzBdLmNsYXNzTGlzdC5hZGQoICdpcy1vcGVuJyApO1xyXG5cdFx0XHRcdGdyb3Vwc1swXS5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICd3cGJjOmNvbGxhcHNpYmxlOm9wZW4nLCB7IGJ1YmJsZXM6IHRydWUgfSApICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNldCBkYXRhLWJmYi1zZWN0aW9uLWNvbHMgb24gdGhlIGluc3BlY3RvciBwYW5lbCBiYXNlZCBvbiB0aGUgY3VycmVudCBzZWN0aW9uLlxyXG5cdFx0ICogVXNlcyB0aGUgcmVnaXN0ZXJlZCBjb21wdXRlIGZuIGlmIGF2YWlsYWJsZTsgZmFsbHMgYmFjayB0byBkaXJlY3QgRE9NLlxyXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gc2VjdGlvbkVsXHJcblx0XHQgKi9cclxuXHRcdF9zZXRfcGFuZWxfc2VjdGlvbl9jb2xzKHNlY3Rpb25FbCkge1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdC8vIFByZWZlciB0aGUgYWxyZWFkeS1yZWdpc3RlcmVkIHZhbHVlX2Zyb20gaGVscGVyIGlmIHByZXNlbnQuXHJcblx0XHRcdFx0dmFyIGNvbXB1dGUgPSB3LndwYmNfYmZiX2luc3BlY3Rvcl9mYWN0b3J5X3ZhbHVlX2Zyb20gJiYgdy53cGJjX2JmYl9pbnNwZWN0b3JfZmFjdG9yeV92YWx1ZV9mcm9tLmNvbXB1dGVfc2VjdGlvbl9jb2x1bW5zO1xyXG5cclxuXHRcdFx0XHR2YXIgY29scyA9IDE7XHJcblx0XHRcdFx0aWYgKCB0eXBlb2YgY29tcHV0ZSA9PT0gJ2Z1bmN0aW9uJyApIHtcclxuXHRcdFx0XHRcdGNvbHMgPSBjb21wdXRlKCB7IGVsOiBzZWN0aW9uRWwgfSApIHx8IDE7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdC8vIEZhbGxiYWNrOiBjb21wdXRlIGRpcmVjdGx5IGZyb20gdGhlIERPTS5cclxuXHRcdFx0XHRcdHZhciByb3cgPSBzZWN0aW9uRWwgJiYgc2VjdGlvbkVsLnF1ZXJ5U2VsZWN0b3IoICc6c2NvcGUgPiAud3BiY19iZmJfX3JvdycgKTtcclxuXHRcdFx0XHRcdGNvbHMgICAgPSByb3cgPyAocm93LnF1ZXJ5U2VsZWN0b3JBbGwoICc6c2NvcGUgPiAud3BiY19iZmJfX2NvbHVtbicgKS5sZW5ndGggfHwgMSkgOiAxO1xyXG5cdFx0XHRcdFx0aWYgKCBjb2xzIDwgMSApIGNvbHMgPSAxO1xyXG5cdFx0XHRcdFx0aWYgKCBjb2xzID4gNCApIGNvbHMgPSA0O1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHR0aGlzLnBhbmVsLnNldEF0dHJpYnV0ZSggJ2RhdGEtYmZiLXNlY3Rpb24tY29scycsIFN0cmluZyggY29scyApICk7XHJcblx0XHRcdH0gY2F0Y2ggKCBfICkge1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdF9jcmVhdGVfZmFsbGJhY2tfcGFuZWwoKSB7XHJcblx0XHRcdGNvbnN0IHAgICAgID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2RpdicgKTtcclxuXHRcdFx0cC5pZCAgICAgICAgPSAnd3BiY19iZmJfX2luc3BlY3Rvcic7XHJcblx0XHRcdHAuY2xhc3NOYW1lID0gJ3dwYmNfYmZiX19pbnNwZWN0b3InO1xyXG5cdFx0XHRkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKCBwICk7XHJcblx0XHRcdHJldHVybiAvKiogQHR5cGUge0hUTUxEaXZFbGVtZW50fSAqLyAocCk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyBFeHBvcnQgY2xhc3MgKyByZWFkeSBzaWduYWwuXHJcblx0dy5XUEJDX0JGQl9JbnNwZWN0b3IgPSBXUEJDX0JGQl9JbnNwZWN0b3I7XHJcblx0ZG9jdW1lbnQuZGlzcGF0Y2hFdmVudCggbmV3IEV2ZW50KCAnd3BiY19iZmJfaW5zcGVjdG9yX3JlYWR5JyApICk7XHJcblxyXG59KSggd2luZG93ICk7XHJcbiJdfQ==
