/**
 * Booking Calendar — Generic UI Tabs Utility (JS)
 *
 * Purpose: Lightweight, dependency-free tabs controller for any small tab group in admin UIs.
 * - Auto-initializes groups marked with data-wpbc-tabs.
 * - Assigns ARIA roles and toggles aria-selected/aria-hidden/tabindex.
 * - Supports keyboard navigation (Left/Right/Home/End).
 * - Public API: window.wpbc_ui_tabs.{init_on, init_group, set_active}
 * - Emits 'wpbc:tabs:change' on the group root when the active tab changes.
 *
 * Markup contract:
 * - Root:   [data-wpbc-tabs]
 * - Tabs:   [data-wpbc-tab-key="K"]
 * - Panels: [data-wpbc-tab-panel="K"]
 *
 * @package   Booking Calendar
 * @subpackage Admin\UI
 * @since     11.0.0
 * @version   1.0.0
 * @see       /includes/__js/admin/ui_tabs/ui_tabs.js
 *
 *
 * How it works:
 * - Root node must have [data-wpbc-tabs] attribute (any value).
 * - Tab buttons must carry [data-wpbc-tab-key="..."] (unique per group).
 * - Panels must carry [data-wpbc-tab-panel="..."] with matching keys.
 * - Adds WAI-ARIA roles and aria-selected/hidden wiring.
 *
 * <div data-wpbc-tabs="column-styles" data-wpbc-tab-active="1"    class="wpbc_ui_tabs_root" >
 *    <!-- Top Tabs -->
 *    <div data-wpbc-tablist="" role="tablist"                    class=" wpbc_ui_el__horis_top_bar__wrapper" >
 *        <div class="wpbc_ui_el__horis_top_bar__content">
 *            <h2 class="wpbc_ui_el__horis_nav_label">Column:</h2>
 *
 *            <div class="wpbc_ui_el__horis_nav_item wpbc_ui_el__horis_nav_item__1">
 *                <a
 *                    data-wpbc-tab-key="1"
 *                    aria-selected="true" role="tab" tabindex="0" aria-controls="wpbc_tab_panel_col_1"
 *
 *                        href="javascript:void(0);"
 *                        class="wpbc_ui_el__horis_nav_item__a wpbc_ui_el__horis_nav_item__single"
 *                        id="wpbc_tab_col_1"
 *                        title="Column 1"
 *                ><span class="wpbc_ui_el__horis_nav_title">Title 1</span></a>
 *            </div>
 *            ...
 *        </div>
 *    </div>
 *    <!-- Tabs Content -->
 *    <div class="wpbc_tab__panel group__fields" data-wpbc-tab-panel="1" id="wpbc_tab_panel_col_1" role="tabpanel" aria-labelledby="wpbc_tab_col_1">
 *        ...
 *    </div>
 *    ...
 * </div>
 *
 * Public API:
 *   - wpbc_ui_tabs.init_on(root_or_selector)   // find and init groups within a container
 *   - wpbc_ui_tabs.init_group(root_el)         // init a single group root
 *   - wpbc_ui_tabs.set_active(root_el, key)    // programmatically change active tab
 *
 * Events:
 *   - Dispatches CustomEvent 'wpbc:tabs:change' on root when tab changes:
 *       detail: { active_key: '2', prev_key: '1' }
 *
 * Switch a local (generic) tabs group to tab 3:     var group = document.querySelector('[data-wpbc-tabs="column-styles"]'); if ( group ) { wpbc_ui_tabs.set_active(group, '3'); }
 */
(function ( w ) {
	'use strict';

	if ( w.wpbc_ui_tabs ) {
		return;
	}

	/**
	 * Internal: toggle active state.
	 *
	 * @param {HTMLElement} root_el
	 * @param {string}      key
	 * @param {boolean}     should_emit
	 */
	function set_active_internal( root_el, key, should_emit ) {
		var tab_btns = root_el.querySelectorAll( '[data-wpbc-tab-key]' );
		var panels   = root_el.querySelectorAll( '[data-wpbc-tab-panel]' );

		var prev_key = root_el.getAttribute( 'data-wpbc-tab-active' ) || null;
		if ( String( prev_key ) === String( key ) ) {
			return;
		}

		// Buttons: aria + class
		for ( var i = 0; i < tab_btns.length; i++ ) {
			var btn   = tab_btns[i];
			var b_key = btn.getAttribute( 'data-wpbc-tab-key' );
			var is_on = String( b_key ) === String( key );

			btn.setAttribute( 'role', 'tab' );
			btn.setAttribute( 'aria-selected', is_on ? 'true' : 'false' );
			btn.setAttribute( 'tabindex', is_on ? '0' : '-1' );

			if ( is_on ) {
				btn.classList.add( 'active' );
			} else {
				btn.classList.remove( 'active' );
			}
		}

		// Panels: aria + visibility
		for ( var j = 0; j < panels.length; j++ ) {
			var pn   = panels[j];
			var pkey = pn.getAttribute( 'data-wpbc-tab-panel' );
			var show = String( pkey ) === String( key );

			pn.setAttribute( 'role', 'tabpanel' );
			pn.setAttribute( 'aria-hidden', show ? 'false' : 'true' );
			if ( show ) {
				pn.removeAttribute( 'hidden' );
			} else {
				pn.setAttribute( 'hidden', '' );
			}
		}

		root_el.setAttribute( 'data-wpbc-tab-active', String( key ) );

		if ( should_emit ) {
			try {
				var ev = new w.CustomEvent( 'wpbc:tabs:change', {
					bubbles : true,
					detail  : { active_key : String( key ), prev_key : prev_key }
				} );
				root_el.dispatchEvent( ev );
			} catch ( _e ) {}
		}
	}

	/**
	 * Internal: get ordered keys from buttons.
	 *
	 * @param {HTMLElement} root_el
	 * @returns {string[]}
	 */
	function get_keys( root_el ) {
		var list = [];
		var btns = root_el.querySelectorAll( '[data-wpbc-tab-key]' );
		for ( var i = 0; i < btns.length; i++ ) {
			var k = btns[i].getAttribute( 'data-wpbc-tab-key' );
			if ( k != null && k !== '' ) {
				list.push( String( k ) );
			}
		}
		return list;
	}

	/**
	 * Internal: move focus between tabs using keyboard.
	 *
	 * @param {HTMLElement} root_el
	 * @param {number}      dir  +1 (next) / -1 (prev)
	 */
	function focus_relative( root_el, dir ) {
		var keys    = get_keys( root_el );
		var current = root_el.getAttribute( 'data-wpbc-tab-active' ) || keys[0] || null;
		var idx     = Math.max( 0, keys.indexOf( String( current ) ) );
		var next    = keys[ ( idx + ( dir > 0 ? 1 : keys.length - 1 ) ) % keys.length ];

		var next_btn = root_el.querySelector( '[data-wpbc-tab-key="' + next + '"]' );
		if ( next_btn ) {
			next_btn.focus();
			set_active_internal( root_el, next, true );
		}
	}

	/**
	 * Initialize a single tabs group root.
	 *
	 * @param {HTMLElement} root_el
	 */
	function init_group( root_el ) {
		if ( ! root_el || root_el.__wpbc_tabs_inited ) {
			return;
		}
		root_el.__wpbc_tabs_inited = true;

		// Roles
		var tablist = root_el.querySelector( '[data-wpbc-tablist]' ) || root_el;
		tablist.setAttribute( 'role', 'tablist' );

		// Default active: from attribute or first button
		var keys = get_keys( root_el );
		var def  = root_el.getAttribute( 'data-wpbc-tab-active' ) || ( keys[0] || '1' );
		set_active_internal( root_el, def, false );

		// Clicks
		root_el.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest ? e.target.closest( '[data-wpbc-tab-key]' ) : null;
			if ( ! btn || ! root_el.contains( btn ) ) {
				return;
			}
			e.preventDefault();
			var key = btn.getAttribute( 'data-wpbc-tab-key' );
			if ( key != null ) {
				set_active_internal( root_el, key, true );
			}
		}, true );

		// Keyboard (Left/Right/Home/End)
		root_el.addEventListener( 'keydown', function ( e ) {
			var tgt = e.target;
			if ( ! tgt || ! tgt.hasAttribute || ! tgt.hasAttribute( 'data-wpbc-tab-key' ) ) {
				return;
			}
			switch ( e.key ) {
			case 'ArrowLeft':
				e.preventDefault(); focus_relative( root_el, -1 ); break;
			case 'ArrowRight':
				e.preventDefault(); focus_relative( root_el, +1 ); break;
			case 'Home':
				e.preventDefault(); set_active_internal( root_el, ( get_keys( root_el )[0] || '1' ), true ); break;
			case 'End':
				e.preventDefault(); var ks = get_keys( root_el ); set_active_internal( root_el, ( ks[ ks.length - 1 ] || '1' ), true ); break;
			}
		}, true );
	}

	/**
	 * Initialize all groups within a container (or document).
	 *
	 * @param {HTMLElement|string|null} container
	 */
	function init_on( container ) {
		var ctx = container ? ( typeof container === 'string' ? document.querySelector( container ) : container ) : document;
		if ( ! ctx ) {
			return;
		}
		var groups = ctx.querySelectorAll( '[data-wpbc-tabs]' );
		for ( var i = 0; i < groups.length; i++ ) {
			init_group( groups[i] );
		}
	}

	/**
	 * Programmatically set active tab by key.
	 *
	 * @param {HTMLElement} root_el
	 * @param {string|number} key
	 */
	function set_active( root_el, key ) {
		if ( root_el && root_el.hasAttribute && root_el.hasAttribute( 'data-wpbc-tabs' ) ) {
			set_active_internal( root_el, String( key ), true );
		}
	}

	// Public API (snake_case)
	w.wpbc_ui_tabs = {
		init_on    : init_on,
		init_group : init_group,
		set_active : set_active
	};

	// Auto-init on DOM ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { init_on( document ); } );
	} else {
		init_on( document );
	}

})( window );
