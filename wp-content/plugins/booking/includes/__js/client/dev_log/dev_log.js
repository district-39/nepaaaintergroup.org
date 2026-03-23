window.__WPBC_DEV = true;

/**
 * Extend _wpbc with  new methods
 *
 * @type {*|{}}
 * @private
 */
_wpbc = (function (obj, $) {

	/**
	 * Dev logger (no-op unless window.__WPBC_DEV = true)
	 *
	 * @type {*|{warn: (function(*, *, *): void), error: (function(*, *, *): void), once: obj.dev.once, try: ((function(*, *, *): (*|undefined))|*)}}
	 */
	obj.dev = obj.dev || (() => {
		const seen    = new Set();
		const enabled = () => !!window.__WPBC_DEV;

		function out(level, code, msg, extra) {
			if ( !enabled() ) return;
			try {
				(console[level] || console.warn)( `[WPBC][${code}] ${msg}`, extra ?? '' );
			} catch {
			}
		}

		return {
			log  : (code, msg, extra) => out('log',   code, msg, extra),
			debug: (code, msg, extra) => out('debug', code, msg, extra),
			warn : (code, msg, extra) => out( 'warn', code, msg, extra ),
			error: (code, errOrMsg, extra) =>
				out( 'error', code,
					errOrMsg instanceof Error ? errOrMsg.message : String( errOrMsg ),
					errOrMsg instanceof Error ? errOrMsg : extra ),
			once : (code, msg, extra) => {
				if ( !enabled() ) return;
				const key = `${code}|${msg}`;
				if ( seen.has( key ) ) return;
				seen.add( key );
				out( 'error', code, msg, extra );
			},
			try  : (code, fn, extra) => {
				try {
					return fn();
				} catch ( e ) {
					out( 'error', code, e, extra );
				}
			}
		};
	})();

	// Optional: global traps in dev.
	if ( window.__WPBC_DEV ) {
		window.addEventListener( 'error', (e) => {
			try { _wpbc?.dev?.error( 'GLOBAL-ERROR', e?.error || e?.message, e ); } catch ( _ ) {}
		} );
		window.addEventListener( 'unhandledrejection', (e) => {
			try { _wpbc?.dev?.error( 'GLOBAL-REJECTION', e?.reason ); } catch ( _ ) {}
		} );
	}

	return obj;
	}( _wpbc || {}, jQuery ));
