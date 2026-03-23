/**
 * Compatibility Bridge
 *
 * Sends compatibility status (incompatible plugins/themes) to Site Designer parent window.
 *
 * @package wp-site-designer-mu-plugins
 */

(function() {
	'use strict';

	const config = window.siteDesignerCompatibility || {};
	const PARENT_ORIGIN = config.parentOrigin || null;
	const ALLOWED_ORIGINS = config.allowedOrigins || [];

	/**
	 * Convert PHP's localized boolean (string '1' or '') to actual boolean
	 */
	function toBool(value) {
		return value === true || value === '1' || value === 1;
	}

	/**
	 * Build and send compatibility status to parent window
	 */
	function sendCompatibilityStatus() {
		if (!window.parent || window.parent === window) {
			return;
		}

		const status = {
			type: 'wordpress-compatibility',
			timestamp: Date.now(),
			isCompatible: toBool(config.isCompatible),
			theme: {
				isBlockTheme: toBool(config.theme?.isBlockTheme),
				name: config.theme?.name || null,
				recommendedTheme: config.theme?.recommendedTheme || null,
				recommendedThemeName: config.theme?.recommendedThemeName || null
			},
			plugins: {
				blocked: config.plugins?.blocked || [],
				review: config.plugins?.review || []
			}
		};

		try {
			if (PARENT_ORIGIN) {
				window.parent.postMessage(status, PARENT_ORIGIN);
			} else {
				ALLOWED_ORIGINS.forEach(function (origin) {
					window.parent.postMessage(status, origin);
				});
			}

		} catch (e) {
			console.error('[CompatibilityBridge] Error sending message:', e);
		}
	}

	/**
	 * Handle messages from parent window
	 */
	function handleMessage(event) {
		// Validate origin
		if (event.origin !== PARENT_ORIGIN) {
			return;
		}

		// Handle compatibility status request
		if (event.data && event.data.type === 'request-compatibility-status') {
			sendCompatibilityStatus();
		}
	}

	/**
	 * Initialize
	 */
	function init() {
		// Listen for requests from parent
		window.addEventListener('message', handleMessage);

		// Send status on load
		sendCompatibilityStatus();
	}

	// Start when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

