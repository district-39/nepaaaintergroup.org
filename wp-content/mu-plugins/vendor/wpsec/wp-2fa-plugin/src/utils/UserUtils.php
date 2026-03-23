<?php

namespace Wpsec\twofa\utils;

use WP_User;

/**
 * User utils.
 *
 * @package Wpsec
 * @subpackage Wpsec/utils
 */
class UserUtils {
	/**
	 * Gets current user
	 *
	 * @return WP_User|null
	 * @since 1.0.0
	 */
	public static function get_current_user() {
		$current_user = wp_get_current_user();
		if ( ! $current_user ) {
			return null;
		}

		return $current_user;
	}

	public static function obfuscate_email( $email ) {
		list( $local_part, $domain_part ) = explode( '@', $email );

		if ( strlen( $local_part ) > 2 ) {
			$first_char = $local_part[0];
			$las_char   = $local_part[ strlen( $local_part ) - 1 ];

			$obfuscated_local_part = $first_char . '...' . $las_char;
		} else {
			$obfuscated_local_part = $local_part;
		}

		// Reconstruct the email
		return $obfuscated_local_part . '@' . $domain_part;
	}
}
