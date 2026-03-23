<?php

namespace Wpsec\twofa\Services;

use WP_Error;
use Wpsec\twofa\API\TwoFactorApiClient;
use Wpsec\twofa\Constants\ToggleStatus;
use Wpsec\twofa\Constants\TwoFactorConstants;
use Wpsec\twofa\utils\SiteUtils;

class TwoFactorAuthService {

	const TWOFA_VENTURE_HOME_FF = '2fa_venture_home';

	/**
	 * TwoFactorApiClient instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      TwoFactorApiClient $tfa_api_client.
	 */
	private $tfa_api_client;
	public function __construct( $tfa_api_client ) {
		$this->tfa_api_client = $tfa_api_client;
	}

	/**
	 *  Enable 2-Factor Auth for site
	 * @return bool
	 */
	public function toggle_2fa() {

		$two_fa = get_option( 'wpsec_two_fa_status' );
		$status = ToggleStatus::ENABLED;

		if ( ToggleStatus::ENABLED === $two_fa ) {
			$status = ToggleStatus::DISABLED;
		}

		update_option( 'wpsec_two_fa_status', $status );
		$status_boolean = ToggleStatus::ENABLED === $status;

		if ( $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( self::TWOFA_VENTURE_HOME_FF, false ) ) {
			try {
				$this->tfa_api_client->toggle_2fa_status( SiteUtils::get_site_origin(), $status_boolean );
			} catch ( WP_Error $e ) {
				return $status_boolean;
			}
		}

		return $status_boolean;
	}

	/**
	 * Check if 2-Factor Auth enabled
	 * If not set, we will assume it's disabled
	 * @return bool
	 */
	public function is_2fa_enabled() {
		return get_option( 'wpsec_two_fa_status', ToggleStatus::DISABLED ) === ToggleStatus::ENABLED;
	}

	/**
	 * Activates given 2fa method for given user.
	 *
	 * @param int $user_id user ID.
	 * @param string $method_to_activate Method to activate.
	 * @return boolean Is new method enabled.
	 * @since 1.0.0
	 */
	public function add_new_2fa_auth_method( $user_id, $method_to_activate ) {
		if ( ! $this->is_2fa_enabled() ) {
			return false;
		}

		$available_2fa_methods = get_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, true );
		if ( empty( $available_2fa_methods ) ) {
			$available_2fa_methods = array();
		} else {
			$available_2fa_methods = json_decode( $available_2fa_methods, true );
		}

		if ( ! in_array( $method_to_activate, $available_2fa_methods, true ) ) {
			$available_2fa_methods[] = $method_to_activate;
			update_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, json_encode( $available_2fa_methods ) );

			return true;
		}

		return false;
	}

	/**
	 * Gets array of active 2fa methods.
	 *
	 * @param int $user_id user ID.
	 * @return array
	 * @since 1.0.0
	 */
	public function get_active_2fa_methods( $user_id ) {
		$provider = get_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, true );
		if ( empty( $provider ) ) {
			return array();
		}

		return json_decode( $provider, true );
	}

	/**
	 * Activates given 2fa method for given user.
	 *
	 * @param int $user_id Current user.
	 * @param string $method_to_deactivate Method to deactivate.
	 * @return bool
	 * @since 1.0.0
	 */
	public function deactivate_2fa_method( $user_id, $method_to_deactivate ) {
		$available_2fa_methods = get_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, true );
		$available_2fa_methods = json_decode( $available_2fa_methods, true );

		if ( in_array( $method_to_deactivate, $available_2fa_methods, true ) ) {
			$index = array_search( $method_to_deactivate, $available_2fa_methods, true );
			unset( $available_2fa_methods[ $index ] );

			update_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, json_encode( $available_2fa_methods ) );
			return true;
		}

		return false;
	}

	public function deactivate_all_methods( $user_id ) {
		update_user_meta( $user_id, TwoFactorConstants::WPSEC_2FA_ACTIVE, json_encode( array() ) );
	}
}
