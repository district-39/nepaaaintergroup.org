<?php

class APVC_Salt {
	/*
	 * Used for salting visitor hashes.
	 */
	public static function visitor_token_salt() {
		return self::get_salt_option( 'apvc_salt' );
	}

	/*
	 * Primarily used for salting request payloads.
	 */
	public static function request_payload_salt() {
		return self::get_salt_option( 'apvc_request_rawdata_salt' );
	}

	private static function get_salt_option( $name ) {
		$salt = get_option( $name );

		if ( $salt == false ) {
			$salt = self::generate_salt();
			update_option( $name, $salt );
		}

		return $salt;
	}

	private static function generate_salt() {
		$length = 36;

		if ( function_exists( 'random_bytes' ) ) {
			$bytes = bin2hex( random_bytes( $length ) );
		}
		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$bytes = bin2hex( openssl_random_pseudo_bytes( $length ) );
		}

		return substr( strtr( base64_encode( hex2bin( $bytes ) ), '+', '.' ), 0, 44 );
	}
}
