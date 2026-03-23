<?php
/**
 * Extended local-parser-based Auth Manager for Site Designer
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Managers;

use Exception;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthException;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthManager;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadCert2S;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadE2S;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadE2S2S;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadS2S;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadShopper;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthPayloadShopperBasic;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthResult;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\ObjectMapper;

/**
 * Local AuthManager that parses JWT without SSO verification.
 * For local development only - does not verify signatures.
 */
class Local_Auth_Manager extends AuthManager {

	use Has_Customer_Id;

	/**
	 * Object mapper for mapping decoded JWT data to payload objects.
	 *
	 * @var ?ObjectMapper
	 */
	private ?ObjectMapper $mapper = null;

	/**
	 * Get authenticated shopper payload with access to additional claims.
	 * For local environments, this skips SSO verification.
	 *
	 * @param string     $host SSO host (ignored for local validation).
	 * @param string     $rawToken JWT token.
	 * @param array|null $auths Allowed auth types (ignored for local validation).
	 * @param int        $level Security level (ignored for local validation).
	 * @param bool       $forcedHeartbeat Force heartbeat check (ignored for local validation).
	 *
	 * @return AuthPayloadShopper|null
	 *
	 * @throws AuthException|Exception If JWT token is invalid, expired, or cannot be decoded.
	 */
	public function getAuthPayloadShopper( string $host, string $rawToken, ?array $auths, int $level, bool $forcedHeartbeat = false ): ?AuthPayloadShopper {
		$this->mapper = new ObjectMapper();
		$authResult   = $this->getPayloadJson( $host, $rawToken );

		if ( ! strlen( $authResult->getResult() ) ) {
			return null;
		}
		$decoded = json_decode( $authResult->getResult(), true );
		if ( empty( $decoded['typ'] ) || 'idp' !== $decoded['typ'] ) {
			return null;
		}

		$auth = $decoded['auth'] ?? 'basic';
		switch ( $auth ) {
			case 's2s':
				$result = new AuthPayloadS2S();
				break;
			case 'e2s':
				$result = new AuthPayloadE2S();
				break;
			case 'e2s2s':
				$result = new AuthPayloadE2S2S();
				break;
			case 'cert2s':
				$result = new AuthPayloadCert2S();
				break;
			case 'basic':
				$result = new AuthPayloadShopperBasic();
				break;
			default:
				return null;
		}
		$this->mapper->mapDataToObject( $decoded, $result );

		if ( is_array( $auths ) && count( $auths ) && ! in_array( $auth, $auths ) ) {
			return null;
		}

		if ( $result->isExpired( $level, $forcedHeartbeat, $reason ) ) {
			$this->reauthReason = $reason;

			return null;
		}

		return $result;
	}

	/**
	 * Override getPayloadJson to handle offline validation for local environments.
	 * Does not contact SSO server - parses JWT locally without signature verification.
	 *
	 * @param string     $host SSO host (ignored for offline validation).
	 * @param string     $rawToken JWT token.
	 * @param array|null $auths Allowed auth types (ignored for offline validation).
	 * @param int        $level Security level (ignored for offline validation).
	 *
	 * @return AuthResult
	 */
	protected function getPayloadJson( string $host, string $rawToken, ?array $auths = null, int $level = 0 ): AuthResult { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed,Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed -- Parameter required by WordPress REST API callback signature.
		// Decode the JWT payload without verification.
		$this->payload = $this->decodePayload( $rawToken );

		return new AuthResult( json_encode( $this->payload ) );
	}
}
