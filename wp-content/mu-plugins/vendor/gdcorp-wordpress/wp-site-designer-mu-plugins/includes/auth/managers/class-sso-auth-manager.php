<?php
/**
 * Extended Sso Auth Manager for Site Designer
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Managers;

use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthException;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthManager;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadShopper;

/**
 * Extended AuthManager that provides access to additional JWT claims like 'cid'.
 */
class Sso_Auth_Manager extends AuthManager {

	use Has_Customer_Id;

	/**
	 * Stores the last decoded raw payload data for accessing additional claims.
	 *
	 * @var array
	 */
	protected array $payload = array();

	/**
	 * Get authenticated shopper payload with access to additional claims.
	 *
	 * @param string     $host SSO host (empty string for offline/local validation).
	 * @param string     $rawToken JWT token.
	 * @param array|null $auths Allowed auth types.
	 * @param int        $level Security level.
	 * @param bool       $forcedHeartbeat Force heartbeat check.
	 *
	 * @return AuthPayloadShopper|null
	 *
	 * @throws AuthException If JWT token is invalid, expired, signature verification fails, or SSO server is unreachable.
	 */
	public function getAuthPayloadShopper( string $host, string $rawToken, ?array $auths, int $level, bool $forcedHeartbeat = false ): ?AuthPayloadShopper {
		// Store the raw payload for later access to additional claims.
		$this->payload = $this->decodePayload( $rawToken );

		// Call parent to do the actual validation and mapping.
		return parent::getAuthPayloadShopper( $host, $rawToken, $auths, $level, $forcedHeartbeat );
	}
}
