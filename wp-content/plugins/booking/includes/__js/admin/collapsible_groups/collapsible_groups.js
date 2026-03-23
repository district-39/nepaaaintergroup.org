/**
 * WPBC Collapsible Groups
 *
 * Universal, dependency-free controller for expanding/collapsing grouped sections in right-side panels (Inspector/Library/Form Settings, or any other WPBC page).
 *
 * 		=== How to use it (quick) ? ===
 *
 *		-- 1. Markup (independent mode: multiple open allowed) --
 *			<div class="wpbc_collapsible">
 *			  <section class="wpbc_ui__collapsible_group is-open">
 *				<button type="button" class="group__header"><h3>General</h3></button>
 *				<div class="group__fields">…</div>
 *			  </section>
 *			  <section class="wpbc_ui__collapsible_group">
 *				<button type="button" class="group__header"><h3>Advanced</h3></button>
 *				<div class="group__fields">…</div>
 *			  </section>
 *			</div>
 *
 *		-- 2. Exclusive/accordion mode (one open at a time) --
 *			<div class="wpbc_collapsible wpbc_collapsible--exclusive">…</div>
 *
 *		-- 3. Auto-init --
 *			The script auto-initializes on DOMContentLoaded. No extra code needed.
 *
 *		-- 4. Programmatic control (optional)
 *			const root = document.querySelector('#wpbc_bfb__inspector');
 *			const api  = root.__wpbc_collapsible_instance; // set by auto-init
 *
 *			api.open_by_heading('Validation'); // open by heading text
 *			api.open_by_index(0);              // open the first group
 *
 *		-- 5.Listen to events (e.g., to persist “open group” state) --
 *			root.addEventListener('wpbc:collapsible:open',  (e) => { console.log(  e.detail.group ); });
 *			root.addEventListener('wpbc:collapsible:close', (e) => { console.log(  e.detail.group ); });
 *
 *
 *
 * Markup expectations (minimal):
 *  <div class="wpbc_collapsible [wpbc_collapsible--exclusive]">
 *    <section class="wpbc_ui__collapsible_group [is-open]">
 *      <button type="button" class="group__header"> ... </button>
 *      <div class="group__fields"> ... </div>
 *    </section>
 *    ... more <section> ...
 *  </div>
 *
 * Notes:
 *  - Add `is-open` to any section you want initially expanded.
 *  - Add `wpbc_collapsible--exclusive` to the container for "open one at a time" behavior.
 *  - Works with your existing BFB markup (classes used there are the defaults).
 *
 * Accessibility:
 *  - Sets aria-expanded on .group__header
 *  - Sets aria-hidden + [hidden] on .group__fields
 *  - ArrowUp/ArrowDown move focus between headers; Enter/Space toggles
 *
 * Events (bubbles from the <section>):
 *  - 'wpbc:collapsible:open'  (detail: { group, root, instance })
 *  - 'wpbc:collapsible:close' (detail: { group, root, instance })
 *
 * Public API (instance methods):
 *  - init(), destroy(), refresh()
 *  - expand(group, [exclusive]), collapse(group), toggle(group)
 *  - open_by_index(index), open_by_heading(text)
 *  - is_exclusive(), is_open(group)
 *
 * @version 2025-08-26
 * @since 2025-08-26
 */
// ---------------------------------------------------------------------------------------------------------------------
// == File  /collapsible_groups.js == Time point: 2025-08-26 14:13
// ---------------------------------------------------------------------------------------------------------------------
(function (w, d) {
	'use strict';

	class WPBC_Collapsible_Groups {

		/**
		 * Create a collapsible controller for a container.
		 *
		 * @param {HTMLElement|string} root_el
		 *        The container element (or CSS selector) that wraps collapsible groups.
		 *        The container usually has the class `.wpbc_collapsible`.
		 * @param {Object} [opts={}]
		 * @param {string}  [opts.group_selector='.wpbc_ui__collapsible_group']
		 *        Selector for each collapsible group inside the container.
		 * @param {string}  [opts.header_selector='.group__header']
		 *        Selector for the clickable header inside a group.
		 * @param {string}  [opts.fields_selector='.group__fields']
		 *        Selector for the content/panel element inside a group.
		 * @param {string}  [opts.open_class='is-open']
		 *        Class name that indicates the group is open.
		 * @param {boolean} [opts.exclusive=false]
		 *        If true, only one group can be open at a time in this container.
		 *
		 * @constructor
		 * @since 2025-08-26
		 */
		constructor(root_el, opts = {}) {
			this.root = (typeof root_el === 'string') ? d.querySelector( root_el ) : root_el;
			this.opts = Object.assign( {
				group_selector : '.wpbc_ui__collapsible_group',
				header_selector: '.group__header',
				fields_selector: '.group__fields,.group__content',
				open_class     : 'is-open',
				exclusive      : false
			}, opts );

			// Bound handlers (for add/removeEventListener symmetry).
			/** @private */
			this._on_click = this._on_click.bind( this );
			/** @private */
			this._on_keydown = this._on_keydown.bind( this );

			/** @type {HTMLElement[]} @private */
			this._groups = [];
			/** @type {MutationObserver|null} @private */
			this._observer = null;
		}

		/**
		 * Initialize the controller: cache groups, attach listeners, set ARIA,
		 * and start observing DOM changes inside the container.
		 *
		 * @returns {WPBC_Collapsible_Groups} The instance (chainable).
		 * @listens click
		 * @listens keydown
		 * @since 2025-08-26
		 */
		init() {
			if ( !this.root ) {
				return this;
			}
			this._groups = Array.prototype.slice.call(
				this.root.querySelectorAll( this.opts.group_selector )
			);
			this.root.addEventListener( 'click', this._on_click, false );
			this.root.addEventListener( 'keydown', this._on_keydown, false );

			// Observe dynamic inserts/removals (Inspector re-renders).
			this._observer = new MutationObserver( () => {
				this.refresh();
			} );
			this._observer.observe( this.root, { childList: true, subtree: true } );

			this._sync_all_aria();
			return this;
		}

		/**
		 * Tear down the controller: detach listeners, stop the observer,
		 * and drop internal references.
		 *
		 * @returns {void}
		 * @since 2025-08-26
		 */
		destroy() {
			if ( !this.root ) {
				return;
			}
			this.root.removeEventListener( 'click', this._on_click, false );
			this.root.removeEventListener( 'keydown', this._on_keydown, false );
			if ( this._observer ) {
				this._observer.disconnect();
				this._observer = null;
			}
			this._groups = [];
		}

		/**
		 * Re-scan the DOM for current groups and re-apply ARIA to all of them.
		 * Useful after dynamic (re)renders.
		 *
		 * @returns {void}
		 * @since 2025-08-26
		 */
		refresh() {
			if ( !this.root ) {
				return;
			}
			this._groups = Array.prototype.slice.call(
				this.root.querySelectorAll( this.opts.group_selector )
			);
			this._sync_all_aria();
		}

		/**
		 * Check whether the container is in exclusive (accordion) mode.
		 *
		 * Order of precedence:
		 *  1) Explicit option `opts.exclusive`
		 *  2) Container has class `.wpbc_collapsible--exclusive`
		 *  3) Container matches `[data-wpbc-accordion="exclusive"]`
		 *
		 * @returns {boolean} True if exclusive mode is active.
		 * @since 2025-08-26
		 */
		is_exclusive() {
			return !!(
				this.opts.exclusive ||
				this.root.classList.contains( 'wpbc_collapsible--exclusive' ) ||
				this.root.matches( '[data-wpbc-accordion="exclusive"]' )
			);
		}

		/**
		 * Determine whether a specific group is open.
		 *
		 * @param {HTMLElement} group The group element to test.
		 * @returns {boolean} True if the group is currently open.
		 * @since 2025-08-26
		 */
		is_open(group) {
			return group.classList.contains( this.opts.open_class );
		}

		/**
		 * Open a group. Honors exclusive mode by collapsing all sibling groups
		 * (queried from the live DOM at call-time).
		 *
		 * @param {HTMLElement} group The group element to open.
		 * @param {boolean} [exclusive]
		 *        If provided, overrides container mode for this action only.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:open
		 * @since 2025-08-26
		 */
		expand(group, exclusive) {
			if ( !group ) {
				return;
			}
			const do_exclusive = (typeof exclusive === 'boolean') ? exclusive : this.is_exclusive();
			if ( do_exclusive ) {
				// Always use the live DOM, not the cached list.
				Array.prototype.forEach.call(
					this.root.querySelectorAll( this.opts.group_selector ),
					(g) => {
						if ( g !== group ) {
							this._set_open( g, false );
						}
					}
				);
			}
			this._set_open( group, true );
		}

		/**
		 * Close a group.
		 *
		 * @param {HTMLElement} group The group element to close.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:close
		 * @since 2025-08-26
		 */
		collapse(group) {
			if ( !group ) {
				return;
			}
			this._set_open( group, false );
		}

		/**
		 * Toggle a group's open/closed state.
		 *
		 * @param {HTMLElement} group The group element to toggle.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		toggle(group) {
			if ( !group ) {
				return;
			}
			this[this.is_open( group ) ? 'collapse' : 'expand']( group );
		}

		/**
		 * Open a group by its index within the container (0-based).
		 *
		 * @param {number} index Zero-based index of the group.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		open_by_index(index) {
			const group = this._groups[index];
			if ( group ) {
				this.expand( group );
			}
		}

		/**
		 * Open a group by matching text contained within the <h3> inside the header.
		 * The comparison is case-insensitive and substring-based.
		 *
		 * @param {string} text Text to match against the heading contents.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		open_by_heading(text) {
			if ( !text ) {
				return;
			}
			const t     = String( text ).toLowerCase();
			const match = this._groups.find( (g) => {
				const h = g.querySelector( this.opts.header_selector + ' h3' );
				return h && h.textContent.toLowerCase().indexOf( t ) !== -1;
			} );
			if ( match ) {
				this.expand( match );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Internal
		// -------------------------------------------------------------------------------------------------------------

		/**
		 * Delegated click handler for headers.
		 *
		 * @private
		 * @param {MouseEvent} ev The click event.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_on_click(ev) {
			const btn = ev.target.closest( this.opts.header_selector );
			if ( !btn || !this.root.contains( btn ) ) {
				return;
			}
			ev.preventDefault();
			ev.stopPropagation();
			const group = btn.closest( this.opts.group_selector );
			if ( group ) {
				this.toggle( group );
			}
		}

		/**
		 * Keyboard handler for header interactions and roving focus:
		 *  - Enter/Space toggles the active group.
		 *  - ArrowUp/ArrowDown moves focus between group headers.
		 *
		 * @private
		 * @param {KeyboardEvent} ev The keyboard event.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_on_keydown(ev) {
			const btn = ev.target.closest( this.opts.header_selector );
			if ( !btn ) {
				return;
			}

			const key = ev.key;

			// Toggle on Enter / Space.
			if ( key === 'Enter' || key === ' ' ) {
				ev.preventDefault();
				const group = btn.closest( this.opts.group_selector );
				if ( group ) {
					this.toggle( group );
				}
				return;
			}

			// Move focus with ArrowUp/ArrowDown between headers in this container.
			if ( key === 'ArrowUp' || key === 'ArrowDown' ) {
				ev.preventDefault();
				const headers = Array.prototype.map.call(
					this.root.querySelectorAll( this.opts.group_selector ),
					(g) => g.querySelector( this.opts.header_selector )
				).filter( Boolean );
				const idx     = headers.indexOf( btn );
				if ( idx !== -1 ) {
					const next_idx = (key === 'ArrowDown')
						? Math.min( headers.length - 1, idx + 1 )
						: Math.max( 0, idx - 1 );
					headers[next_idx].focus();
				}
			}
		}

		/**
		 * Apply ARIA synchronization to all known groups based on their open state.
		 *
		 * @private
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_sync_all_aria() {
			this._groups.forEach( (g) => this._sync_group_aria( g ) );
		}

		/**
		 * Sync ARIA attributes and visibility on a single group.
		 *
		 * @private
		 * @param {HTMLElement} group The group element to sync.
		 * @returns {void}
		 * @since 2025-08-26
		 */
		_sync_group_aria(group) {
			const is_open = this.is_open( group );
			const header  = group.querySelector( this.opts.header_selector );
			// Only direct children that match.
			const panels = Array.prototype.filter.call( group.children, (el) => el.matches( this.opts.fields_selector ) );

			// Header ARIA.
			if ( header ) {
				header.setAttribute( 'role', 'button' );
				header.setAttribute( 'aria-expanded', is_open ? 'true' : 'false' );

				if ( panels.length ) {
					// Ensure each panel has an id; then wire aria-controls with space-separated ids.
					const ids = panels.map( (p) => {
						if ( !p.id ) p.id = this._generate_id( 'wpbc_collapsible_panel' );
						return p.id;
					} );
					header.setAttribute( 'aria-controls', ids.join( ' ' ) );
				}
			}

			// (3) Panels ARIA + visibility.
			panels.forEach( (p) => {
				p.hidden = !is_open;                            // actual visibility.
				p.setAttribute( 'aria-hidden', is_open ? 'false' : 'true' ); // ARIA.
			} );
		}

		/**
		 * Internal state change: set a group's open/closed state, sync ARIA,
		 * manage focus on collapse, and emit a custom event.
		 *
		 * @private
		 * @param {HTMLElement} group The group element to mutate.
		 * @param {boolean} open Whether the group should be open.
		 * @returns {void}
		 * @fires CustomEvent#wpbc:collapsible:open
		 * @fires CustomEvent#wpbc:collapsible:close
		 * @since 2025-08-26
		 */
		_set_open(group, open) {
			if ( !open && group.contains( document.activeElement ) ) {
				const header = group.querySelector( this.opts.header_selector );
				header && header.focus();
			}
			group.classList.toggle( this.opts.open_class, open );
			this._sync_group_aria( group );
			const ev_name = open ? 'wpbc:collapsible:open' : 'wpbc:collapsible:close';
			group.dispatchEvent( new CustomEvent( ev_name, {
				bubbles: true,
				detail : { group, root: this.root, instance: this }
			} ) );
		}

		/**
		 * Generate a unique DOM id with the specified prefix.
		 *
		 * @private
		 * @param {string} prefix The id prefix to use.
		 * @returns {string} A unique element id not present in the document.
		 * @since 2025-08-26
		 */
		_generate_id(prefix) {
			let i = 1;
			let id;
			do {
				id = prefix + '_' + (i++);
			}
			while ( d.getElementById( id ) );
			return id;
		}
	}

	/**
	 * Auto-initialize collapsible controllers on the page.
	 * Finds top-level `.wpbc_collapsible` containers (ignoring nested ones),
	 * and instantiates {@link WPBC_Collapsible_Groups} on each.
	 *
	 * @function WPBC_Collapsible_AutoInit
	 * @returns {void}
	 * @since 2025-08-26
	 * @example
	 * // Runs automatically on DOMContentLoaded; can also be called manually:
	 * WPBC_Collapsible_AutoInit();
	 */
	function wpbc_collapsible__auto_init() {
		var ROOT  = '.wpbc_collapsible';
		var nodes = Array.prototype.slice.call( d.querySelectorAll( ROOT ) )
			.filter( function (n) {
				return !n.parentElement || !n.parentElement.closest( ROOT );
			} );

		nodes.forEach( function (node) {
			if ( node.__wpbc_collapsible_instance ) {
				return;
			}
			var exclusive = node.classList.contains( 'wpbc_collapsible--exclusive' ) || node.matches( '[data-wpbc-accordion="exclusive"]' );

			node.__wpbc_collapsible_instance = new WPBC_Collapsible_Groups( node, { exclusive } ).init();
		} );
	}

	// Export to global for manual control if needed.
	w.WPBC_Collapsible_Groups   = WPBC_Collapsible_Groups;
	w.WPBC_Collapsible_AutoInit = wpbc_collapsible__auto_init;

	// DOM-ready auto init.
	if ( d.readyState === 'loading' ) {
		d.addEventListener( 'DOMContentLoaded', wpbc_collapsible__auto_init, { once: true } );
	} else {
		wpbc_collapsible__auto_init();
	}
})( window, document );
