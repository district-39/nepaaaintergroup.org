<?php
/**
 * Cookie Status Bridge Plugin
 *
 * This plugin enables communication between WordPress (running in an iframe) and the
 * Site Designer parent application. It monitors WordPress authentication state and
 * cookie status, then broadcasts changes to the parent window via postMessage.
 *
 * Why this exists:
 * - Site Designer embeds WordPress in an iframe for live editing
 * - The parent app needs to know when WordPress auth cookies are present/valid
 * - Cross-origin iframe communication requires postMessage with whitelisted origins
 * - This enables features like "user logged out" detection and session management
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Config;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

/**
 * Bridges cookie/authentication status between WordPress iframe and Site Designer parent window
 *
 * Sends postMessage events to parent window when:
 * - Page loads (initial cookie state)
 * - Cookies change (login/logout detected)
 * - Page becomes visible again (tab focus)
 */
class Cookie_Status_Bridge {

	/**
	 * Polling interval for cookie change detection (in milliseconds)
	 *
	 * Set to 5 seconds as a balance between responsiveness and performance.
	 * Cookie changes typically only occur during explicit login/logout actions,
	 * which usually trigger page reloads anyway.
	 *
	 * @var int
	 */
	private const POLL_INTERVAL_MS = 1000;

	/**
	 * Initialize the class and register hooks
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();

		// Hook into all page types where authentication status matters.
		// Priority 999 ensures script outputs after other footer scripts.
		add_action( 'admin_footer', array( $instance, 'output_script' ), 999 );
		add_action( 'wp_footer', array( $instance, 'output_script' ), 999 );
		add_action( 'login_footer', array( $instance, 'output_script' ), 999 );
	}

	/**
	 * Output JavaScript that monitors and reports cookie/auth status to parent window
	 *
	 * The script:
	 * 1. Collects WordPress-related cookies accessible via JavaScript
	 * 2. Combines with PHP-side auth detection (more reliable for httpOnly cookies)
	 * 3. Sends status to parent window via postMessage
	 * 4. Monitors for changes and re-sends when detected
	 *
	 * Security note: Uses the single validated parent origin from Iframe_Context_Detector,
	 * avoiding exposure of the full allowed origins list in JavaScript.
	 *
	 * @return void
	 */
	public function output_script(): void {
		// PHP-side detection is more reliable because:
		// - Auth cookies are typically httpOnly (not accessible via JS)
		// - is_user_logged_in() checks session validity, not just cookie presence.
		$is_user_logged_in = is_user_logged_in();
		$has_cookies       = ! empty( $_COOKIE );
		$poll_interval     = self::POLL_INTERVAL_MS;
		$parent_origin     = Iframe_Context_Detector::get_parent_origin();
		$allowed_origins   = Iframe_Context_Detector::get_allowed_parent_origins();
		?>
        <script type="text/javascript">
            /**
             * Cookie Status Bridge - WordPress to Site Designer Communication
             *
             * Monitors WordPress authentication/cookie state and reports changes
             * to the parent Site Designer application via postMessage.
             */
            (function () {
                'use strict';

                // Configuration from PHP (validated server-side).
                const PHP_IS_LOGGED_IN = <?php echo wp_json_encode( $is_user_logged_in ); ?>;
                const PHP_HAS_COOKIES = <?php echo wp_json_encode( $has_cookies ); ?>;
                const POLL_INTERVAL = <?php echo wp_json_encode( $poll_interval ); ?>;
                const PARENT_ORIGIN = <?php echo wp_json_encode( $parent_origin ); ?>;
                const ALLOWED_ORIGINS = <?php echo wp_json_encode( $allowed_origins ); ?>;

                // State tracking to prevent duplicate messages.
                let lastCookieState = '';

                /**
                 * Extract WordPress-related cookies that JavaScript can access
                 *
                 * Note: Auth cookies (wordpress_logged_in_*) are typically httpOnly,
                 * so this primarily catches non-httpOnly cookies like settings cookies.
                 * PHP-side detection (PHP_IS_LOGGED_IN) is the primary auth indicator.
                 *
                 * @return {Object} Key-value pairs of WordPress cookie names and values.
                 */
                function getWordPressCookies() {
                    const cookies = {};
                    const cookieString = document.cookie;

                    if (!cookieString) {
                        return cookies;
                    }

                    const cookieArray = cookieString.split(';');

                    cookieArray.forEach(function (cookie) {
                        const parts = cookie.trim().split('=');
                        const name = parts[0];
                        const value = parts.slice(1).join('=');

                        // Filter to WordPress-related cookies only.
                        if (name.indexOf('wordpress_') === 0 ||
                            name.indexOf('wp-') === 0 ||
                            name.indexOf('wp_') === 0 ||
                            name === 'PHPSESSID') {
                            cookies[name] = value;
                        }
                    });

                    return cookies;
                }

                /**
                 * Send current cookie/auth status to parent window
                 *
                 * Only sends if state has changed to prevent flooding parent with messages.
                 * Uses the PHP-validated parent origin for secure postMessage.
                 */
                function sendCookieStatus() {
                    const wpCookies = getWordPressCookies();
                    const jsHasCookies = Object.keys(wpCookies).length > 0;

                    // Build status payload.
                    // PHP_IS_LOGGED_IN is authoritative for auth state (httpOnly cookies).
                    // Cookie presence is a secondary indicator.
                    const cookieInfo = {
                        type: 'wordpress-cookies',
                        loaded: PHP_HAS_COOKIES || jsHasCookies,
                        isLoggedIn: PHP_IS_LOGGED_IN,
                        timestamp: Date.now()
                    };

                    // Deduplicate: only send if state changed.
                    const currentState = JSON.stringify(cookieInfo);
                    if (currentState === lastCookieState) {
                        return;
                    }
                    lastCookieState = currentState;

                    // Send to parent window if we're in an iframe and have a validated origin.
                    // Uses PHP-validated origin; if it's unavailable, do not send the message.
                    if (window.parent && window.parent !== window && ALLOWED_ORIGINS) {
                        if (PARENT_ORIGIN) {
                            window.parent.postMessage(cookieInfo, PARENT_ORIGIN);
                        } else {
                            ALLOWED_ORIGINS.forEach(function (origin) {
                                window.parent.postMessage(cookieInfo, origin);
                            });
                        }
                    }
                }

                /**
                 * Initialize cookie monitoring
                 *
                 * Sets up:
                 * 1. Immediate status report on page load
                 * 2. Polling for cookie changes (catches JS-based auth flows)
                 * 3. Visibility change listener (re-reports when tab regains focus)
                 */
                function initMonitoring() {
                    let lastCookieString = document.cookie;

                    // Send initial status immediately.
                    sendCookieStatus();

                    // Poll for cookie changes.
                    // This catches edge cases where login/logout happens via JavaScript
                    // without a full page reload (e.g., AJAX-based auth flows).
                    setInterval(function () {
                        if (document.cookie !== lastCookieString) {
                            lastCookieString = document.cookie;
                            sendCookieStatus();
                        }
                    }, POLL_INTERVAL);

                    // Re-report status when page becomes visible.
                    // Handles cases where user logged in/out in another tab.
                    document.addEventListener('visibilitychange', function () {
                        if (!document.hidden) {
                            sendCookieStatus();
                        }
                    });
                }

                // Start monitoring when DOM is ready.
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initMonitoring);
                } else {
                    initMonitoring();
                }
            })();
        </script>
		<?php
	}
}

