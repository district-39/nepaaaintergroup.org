/**
 * WPBC calendar loader bootstrap.
 * ==============================================================================================
 * - Finds every .calendar_loader_frame[data-wpbc-rid] on the page (now or later).
 * - For each loader element, waits a "grace" period (data-wpbc-grace, default 8000 ms):
 *     - If the real calendar appears: do nothing (loader naturally replaced).
 *     - If not: show a helpful message (missing jQuery/_wpbc/datepick) or a duplicate notice.
 * - Works with multiple calendars and even duplicate RIDs on the same page.
 * - No inline JS needed in the shortcode/template output.
 *
 * File:  ../includes/__js/client/cal/wpbc_cal_loader.js
 *
 * @since    10.14.5
 * @modified 2025-09-07 12:21
 * @version  1.0.0
 *
 */
/**
 * WPBC calendar loader bootstrap.
 * - Auto-detects .calendar_loader_frame[data-wpbc-rid] blocks.
 * - Waits a "grace" period per element before showing a helpful message
 *   if the real calendar hasn't replaced the loader.
 * - Multiple calendars and duplicate RIDs are handled.
 */
(function (w, d) {
	'use strict';

	/* ---------------------------------------------------------------------------
	 * Small utilities (snake_case)
	 * ------------------------------------------------------------------------ */

	/** Track processed loader elements; fallback to data flag if WeakSet missing. */
	var processed_set = typeof WeakSet === 'function' ? new WeakSet() : null;

	/** Return first match inside optional root. */
	function query_one(selector, root) {
		return (root || d).querySelector( selector );
	}

	/** Return NodeList of matches inside optional root. */
	function query_all(selector, root) {
		return (root || d).querySelectorAll( selector );
	}

	/** Run a callback when DOM is ready. */
	function on_ready(fn) {
		if ( d.readyState === 'loading' ) {
			d.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	/** Clear interval safely. */
	function safe_clear(interval_id) {
		try {
			w.clearInterval( interval_id );
		} catch ( e ) {
		}
	}

	/** Mark element processed (WeakSet or data attribute). */
	function mark_processed(el) {
		if ( processed_set ) {
			processed_set.add( el );
		} else {
			try {
				el.dataset.wpbcProcessed = '1';
			} catch ( e ) {
			}
		}
	}

	/** Check if element was processed. */
	function is_processed(el) {
		return processed_set ? processed_set.has( el ) : (el && el.dataset && el.dataset.wpbcProcessed === '1');
	}

	/* ---------------------------------------------------------------------------
	 * Messages (fixed English strings; no i18n)
	 * ------------------------------------------------------------------------ */

	/**
	 * Build fixed English messages for a resource.
	 * @param {string|number} rid
	 * @return {{duplicate:string,support:string,lib_jq:string,lib_dp:string,lib_wpbc:string}}
	 */
	function get_messages(rid) {
		var rid_int = parseInt( rid, 10 );
		return {
			duplicate  :
				'You have added the same calendar (ID = ' + rid_int + ') more than once on this page. ' +
				'Please keep only one calendar with the same ID on a page to avoid conflicts.',
			init_failed:
				'The calendar could not be initialized on this page.' + '\n' +
				'Please check your browser console for JavaScript errors and conflicts with other scripts/plugins.',
			support    : 'Contact support@wpbookingcalendar.com if you have any questions.',
			lib_jq     :
				'It appears that the "jQuery" library is not loading correctly.' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/',
			lib_dp     :
				'It appears that the "jQuery.datepick" library is not loading correctly.' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/',
			lib_wpbc   :
				'It appears that the "_wpbc" library is not loading correctly.' + '\n' +
				'Please enable the loading of JS/CSS files for this page on the "WP Booking Calendar" - "Settings General" - "Advanced" page' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/'
		};
	}

	/**
	 * Wrap plain text (with newlines) in a small HTML container.
	 * @param {string} msg
	 * @return {string}
	 */
	function wrap_html(msg) {
		return '<div style="font-size:13px;margin:10px;">' + String( msg || '' ).replace( /\n/g, '<br>' ) + '</div>';
	}

	/** Library presence checks (fast & cheap). */
	function has_jq() {
		return !!(w.jQuery && jQuery.fn && typeof jQuery.fn.on === 'function');
	}

	function has_dp() {
		return !!(w.jQuery && jQuery.datepick);
	}

	function has_wpbc() {
		return !!(w._wpbc && typeof w._wpbc.set_other_param === 'function');
	}

	function normalize_rid(rid) {
		var n = parseInt( rid, 10 );
		return (n > 0) ? String( n ) : '';
	}

	function get_rid_counts(rid) {
		var r = normalize_rid( rid );
		return {
			rid       : r,
			loaders   : r ? query_all( '.calendar_loader_frame[data-wpbc-rid="' + r + '"]' ).length : 0,
			containers: r ? query_all( '#calendar_booking' + r ).length : 0
		};
	}

	function is_duplicate_rid(rid) {
		var c = get_rid_counts( rid );
		return (c.loaders > 1) || (c.containers > 1);
	}

	/**
	 * Determine if the loader has been replaced by the real calendar.
	 *
	 * @param {Element} el       Loader element
	 * @param {string} rid       Resource ID
	 * @param {Element|null} container Optional #calendar_booking{rid} element
	 * @return {boolean}
	 */
	function is_replaced(el, rid, container) {
		var loader_still_in_dom = d.body.contains( el );
		var calendar_exists     = !!query_one( '.wpbc_calendar_id_' + rid, container || d );
		return (!loader_still_in_dom) || calendar_exists;
	}

	/**
	 * Start watcher for a single loader element.
	 * - Polls and observes the calendar container.
	 * - After grace, injects a suitable message if not replaced.
	 *
	 * @param {Element} el
	 */
	function start_for(el) {
		if ( ! el || is_processed( el ) ) {
			return;
		}
		mark_processed( el );

		var rid = el.dataset.wpbcRid;
		if ( ! rid ) {
			return;
		}

		var grace_ms = parseInt( el.dataset.wpbcGrace || '8000', 10 );
		if ( ! (grace_ms > 0) ) {
			grace_ms = 8000;
		}

		var container_id = 'calendar_booking' + rid;
		var container    = d.getElementById( container_id );
		var text_el      = query_one( '.calendar_loader_text', el );

		function replaced_now() {
			return is_replaced( el, rid, container );
		}

		// Already replaced -> nothing to do.
		if ( replaced_now() ) {
			return;
		}

		// 1) Cheap polling.
		var poll_id = w.setInterval( function () {
			if ( replaced_now() ) {
				safe_clear( poll_id );
				if ( observer ) {
					try {
						observer.disconnect();
					} catch ( e ) {
					}
				}
			}
		}, 250 );

		// 2) MutationObserver for faster reaction.
		var observer = null;
		if ( container && 'MutationObserver' in w ) {
			try {
				observer = new MutationObserver( function () {
					if ( replaced_now() ) {
						safe_clear( poll_id );
						try {
							observer.disconnect();
						} catch ( e ) {
						}
					}
				} );
				observer.observe( container, { childList: true, subtree: true } );
			} catch ( e ) {
			}
		}

		// 3) Final decision after grace period.
		w.setTimeout( function finalize_after_grace() {
			if ( replaced_now() ) {
				safe_clear( poll_id );
				if ( observer ) {
					try {
						observer.disconnect();
					} catch ( e ) {
					}
				}
				return;
			}

			var M = get_messages( rid );
			var msg;
			if ( ! has_jq() ) {
				msg = M.lib_jq;
			} else if ( ! has_wpbc() ) {
				msg = M.lib_wpbc;
			} else if ( ! has_dp() ) {
				msg = M.lib_dp;
			} else {
				// Libraries are present, but loader wasn't replaced -> decide what is most likely.
				if ( is_duplicate_rid( rid ) ) {
					msg = M.duplicate + '\n\n' + M.support;
				} else {
					msg = M.init_failed + '\n\n' + M.support;
				}
			}

			try {
				if ( text_el ) {
					text_el.innerHTML = wrap_html( msg );
				}
			} catch ( e ) {
			}

			safe_clear( poll_id );
			if ( observer ) {
				try {
					observer.disconnect();
				} catch ( e ) {
				}
			}
		}, grace_ms );
	}

	/**
	 * Initialize watchers for loader elements already in the DOM.
	 */
	function bootstrap_existing() {
		query_all( '.calendar_loader_frame[data-wpbc-rid]' ).forEach( start_for );
	}

	/**
	 * Observe the document for any new loader elements inserted later (AJAX, block render).
	 */
	function observe_new_loaders() {
		if ( ! ('MutationObserver' in w) ) {
			return;
		}
		try {
			var doc_observer = new MutationObserver( function (mutations) {
				for ( var i = 0; i < mutations.length; i++ ) {
					var nodes = mutations[i].addedNodes || [];
					for ( var j = 0; j < nodes.length; j++ ) {
						var node = nodes[j];
						if ( ! node || node.nodeType !== 1 ) {
							continue;
						}
						if ( node.matches && node.matches( '.calendar_loader_frame[data-wpbc-rid]' ) ) {
							start_for( node );
						}
						if ( node.querySelectorAll ) {
							var inner = node.querySelectorAll( '.calendar_loader_frame[data-wpbc-rid]' );
							if ( inner && inner.length ) {
								inner.forEach( start_for );
							}
						}
					}
				}
			} );
			doc_observer.observe( d.documentElement, { childList: true, subtree: true } );
		} catch ( e ) {
		}
	}

	/* ---------------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------------ */
	on_ready( function () {
		bootstrap_existing();
		observe_new_loaders();
	} );

})( window, document );
