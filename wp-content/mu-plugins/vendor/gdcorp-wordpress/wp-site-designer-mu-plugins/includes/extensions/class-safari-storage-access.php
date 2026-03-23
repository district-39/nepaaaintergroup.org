<?php
/**
 * Safari Storage Access Plugin
 *
 * Handles Safari storage access for cross-origin iframe authentication in Airo Site Designer.
 * Intercepts requests with ?safari_popup_* and shows a page that requests storage access.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Config;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;
use WP_User;

/**
 * Handles Safari storage access handshake for cross-origin iframe authentication
 */
class Safari_Storage_Access {

	/**
	 * Cookie name for popup auth pending state
	 *
	 * @var string
	 */
	const AUTH_PENDING_COOKIE = 'safari_popup_auth_pending';

	/**
	 * Cookie name for storage granted state
	 *
	 * @var string
	 */
	const STORAGE_GRANTED_COOKIE = 'safari_storage_granted';

	/**
	 * Initialize the Safari storage access handler
	 */
	public static function init(): void {
		$instance = new self();

		// Set cookie when popup auth starts (on login page).
		add_action( 'login_init', array( $instance, 'maybe_set_popup_cookie' ) );

		// After successful login, redirect to completion page.
		add_action( 'wp_login', array( $instance, 'on_login_success' ), 10, 2 );

		// Handle iframe storage access request.
		add_action( 'init', array( $instance, 'maybe_request_storage_access' ), 1 );

		// Inject popup completion script on any page after login.
		add_action( 'wp_head', array( $instance, 'maybe_inject_popup_complete_script' ), 1 );
		add_action( 'admin_head', array( $instance, 'maybe_inject_popup_complete_script' ), 1 );
		add_action( 'login_head', array( $instance, 'maybe_inject_popup_complete_script' ), 1 );

		// Make sure this script has the last say on headers.
		add_action( 'send_headers', array( $instance, 'send_headers' ), PHP_INT_MAX );
	}

	/**
	 * Send headers for cross-origin iframe support
	 * Referrer policy to ensure referrer is sent on navigations
	 */
	public function send_headers(): void {
		header( 'Referrer-Policy: no-referrer-when-downgrade', true );
	}

	/**
	 * Set cookie when popup auth is initiated on login page
	 */
	public function maybe_set_popup_cookie(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_gdmu_site_designer_safari_popup =
			isset( $_REQUEST['safari_popup_auth'] ) && sanitize_key( $_REQUEST['safari_popup_auth'] ) ||
			isset( $_REQUEST['safari_popup_auth_alone'] ) && sanitize_key( $_REQUEST['safari_popup_auth_alone'] );

		if ( $is_gdmu_site_designer_safari_popup ) {
			setcookie( self::AUTH_PENDING_COOKIE, '1', time() + 300, '/', '', true, true );
		}
	}

	/**
	 * After login, redirect to a page that will close the popup
	 *
	 * @param string $user_login Username.
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function on_login_success( string $user_login, WP_User $user ): void { //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( isset( $_COOKIE[ self::AUTH_PENDING_COOKIE ] ) ) {
			wp_safe_redirect( home_url( '/?safari_popup_complete=1' ) );
			exit;
		}
	}

	/**
	 * Check if the current browser is Safari (not Chrome/Edge which also have Safari in UA)
	 *
	 * @return bool
	 */
	public function is_safari(): bool {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$ua = $_SERVER['HTTP_USER_AGENT'];

		// Safari has "Safari" in UA but Chrome/Edge also do - check for those.
		return strpos( $ua, 'Safari' ) !== false
		       && strpos( $ua, 'Chrome' ) === false
		       && strpos( $ua, 'Chromium' ) === false
		       && strpos( $ua, 'Edg' ) === false;
	}

	/**
	 * For iframe requests: Show storage access prompt if needed
	 */
	public function maybe_request_storage_access(): void {
		// Only run for Safari browser (ITP storage partitioning).
		if ( ! $this->is_safari() ) {
			return;
		}

		// Only run for iframe context (Site Designer embedding).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['wp_site_designer'] ) ) {
			return;
		}

		// Skip popup auth flow - that's handled separately.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['safari_popup_auth'] ) || isset( $_GET['safari_popup_complete'] ) ) {
			return;
		}

		// Skip if already processing grant (must check before cookie to avoid loop).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['_storage_access_granted'] ) ) {
			setcookie( self::STORAGE_GRANTED_COOKIE, '1', 0, '/', '', true, true );

			return;
		}

		// If storage was granted before, show loader to verify access is still valid.
		if ( isset( $_COOKIE[ self::STORAGE_GRANTED_COOKIE ] ) ) {
            // even if cookie is set, it needs to confirm in case of URL change.
			$this->continuous_storage_access();
			exit;
		}

		$this->initial_storage_access_page();
		exit;
	}

	/**
     * Called on every subsequent request. Every path/document needs to receive separate grant access.
     *
	 * @return void
	 */
    public function continuous_storage_access(): void {
        $current_url = self::get_current_url();
	    $final_url   = add_query_arg( '_storage_access_granted', '1', $current_url );

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Loading...</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    background: #f0f0f1;
                }
                .loader {
                    width: 48px;
                    height: 48px;
                    border: 4px solid #e5e7eb;
                    border-top-color: #5865F2;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin { to { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
        <div class="loader"></div>
        <script>
            (async function() {
                var finalUrl = <?php echo wp_json_encode( $final_url, JSON_UNESCAPED_SLASHES ); ?>;
                try {
                    // Check if we still have storage access.
                    if (document.hasStorageAccess && !await document.hasStorageAccess()) {
                        // Lost access, request it again.
                        if (document.requestStorageAccess) {
                            await document.requestStorageAccess();
                        }
                    }
                } catch (e) {
                    // Access request failed, continue anyway.
                }
                window.location.href = finalUrl;
            })();
        </script>
        </body>
        </html>
    <?php }

	/**
	 * Output the storage access prompt page
	 */
	public function initial_storage_access_page(): void {
		$current_url = self::get_current_url();
		$final_url   = add_query_arg( '_storage_access_granted', '1', $current_url );

		?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="referrer" content="origin">
            <title>Allow Safari to show your site</title>
            <style>
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }

                body {
                    font-family: 'GD Sherpa', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    background: rgba(0, 0, 0, 0.5);
                }

                .modal {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end;
                    padding: 40px;
                    gap: 40px;
                    width: 600px;
                    max-width: calc(100% - 32px);
                    background: #FFFFFF;
                    box-shadow: 0px 0px 1px rgba(0, 0, 0, 0.2), 0px 4px 18px rgba(0, 0, 0, 0.1), 0px 6px 8px rgba(0, 0, 0, 0.07);
                    border-radius: 16px;
                    transform: scale(1.25);
                    transform-origin: center center;
                }

                .text-lockup {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 0;
                    gap: 8px;
                    width: 100%;
                }

                .title {
                    font-weight: 700;
                    font-size: 22px;
                    line-height: 28px;
                    color: #111111;
                    width: 100%;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }

                .subtitle {
                    font-weight: 400;
                    font-size: 16px;
                    line-height: 24px;
                    color: #111111;
                    width: 100%;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }

                .footer {
                    display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0;
                    gap: 16px;
                    width: 100%;
                }

                .step-label {
                    font-weight: 500;
                    font-size: 16px;
                    line-height: 22px;
                    color: #111111;
                }

                .btn-primary {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    align-items: center;
                    padding: 16px 24px;
                    gap: 8px;
                    min-height: 37px;
                    background: #111111;
                    border: 1px solid #111111;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background 0.15s ease, border-color 0.15s ease;
                }

                .btn-primary:hover {
                    background: #333333;
                    border-color: #333333;
                }

                .btn-primary:focus {
                    outline: 2px solid #111111;
                    outline-offset: 2px;
                }

                .btn-primary:disabled {
                    background: #9ca3af;
                    border-color: #9ca3af;
                    cursor: not-allowed;
                }

                .btn-label {
                    font-family: -apple-system, BlinkMacSystemFont, "SF Pro", "Segoe UI", Roboto, sans-serif;
                    font-weight: 500;
                    font-size: 13px;
                    line-height: 20px;
                    color: #FFFFFF;
                    white-space: nowrap;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }
            </style>
        </head>
        <body>
        <div class="modal">
            <div class="text-lockup">
                <h1 class="title">Allow Safari to show your site</h1>
                <p class="subtitle">When you click on the button below, Safari will ask for permission. Tap "Allow" to
                    view your WordPress site.</p>
            </div>
            <div class="footer">
                <span class="step-label">Step 2/2</span>
                <button id="continueBtn" class="btn-primary" type="button">
                    <span class="btn-label">Allow in Safari</span>
                </button>
            </div>
        </div>
        <script>
            (function() {
                const btn = document.getElementById('continueBtn');
                const btnLabel = btn.querySelector('.btn-label');
                const finalUrl = <?php echo wp_json_encode( $final_url, JSON_UNESCAPED_SLASHES ); ?>;
                btn.addEventListener('click', async function() {
                    btn.disabled = true;
                    btnLabel.textContent = 'Allowing...';
                    try {
                        if (document.requestStorageAccess) {
                            await document.requestStorageAccess();
                        }
                    } catch (e) {
                        // Storage access request failed, continue anyway.
                    }
                    window.location.href = finalUrl;
                });
            })();
        </script>
        </body>
        </html>
		<?php
	}

	/**
	 * Inject script to signal popup completion
	 */
	public function maybe_inject_popup_complete_script(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$should_complete = isset( $_GET['safari_popup_complete'] ) ||
		                   ( isset( $_COOKIE[ self::AUTH_PENDING_COOKIE ] ) && is_user_logged_in() );

		if ( ! $should_complete ) {
			return;
		}

        $allowed_origins = Iframe_Context_Detector::get_allowed_parent_origins();

		// Clear the popup cookie (flags must match those used when setting).
		setcookie( self::AUTH_PENDING_COOKIE, '', time() - 3600, '/', '', true, true );

		?>
        <script>
            (function () {
                const ALLOWED_ORIGINS = <?php echo wp_json_encode( $allowed_origins ); ?>;

                if (window.opener) {
                    try {
                        ALLOWED_ORIGINS.forEach(function (origin) {
                            window.opener.postMessage({
                                type: 'safari_sso_complete',
                                success: true
                            }, origin);
                        });
                    } catch (e) {
                        setTimeout(function () {
                            window.close();
                        }, 3000);
                    }
                } else {
                    setTimeout(function () {
                        window.close();
                    }, 3000);
                }
            })();
        </script>
		<?php
	}

	/**
	 * Get the current URL
	 *
	 * @return string The current URL.
	 */
	private static function get_current_url(): string {
		$protocol = is_ssl() ? 'https://' : 'http://';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';

		return $protocol . $host . $uri;
	}
}
