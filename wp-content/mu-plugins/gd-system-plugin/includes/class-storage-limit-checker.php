<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Storage_Limit_Checker {
	use Helpers;

	const STORAGE_BUCKET_LT90 = 'LT90';
	const STORAGE_BUCKET_BTW90_100 = 'BTW90_100';
	const STORAGE_BUCKET_GT100 = 'GT100';

	public function __construct() {
	}

	public function is_storage_limit_exceeded(): bool {
		
		if ( ! defined( 'GD_STORAGE_USED_BUCKET' ) ) {
			return false;
		}

		return GD_STORAGE_USED_BUCKET === self::STORAGE_BUCKET_GT100;
	}

	public function is_storage_limit_warnable(): bool {

		if ( ! defined( 'GD_STORAGE_USED_BUCKET' ) ) {
			return false;
		}

		return GD_STORAGE_USED_BUCKET === self::STORAGE_BUCKET_BTW90_100;
	}

	/**
	 * Get the percentage of storage used.
	 *
	 * @return string
	 */
	public function get_storage_bucket(): string {
		if ( ! defined( 'GD_STORAGE_USED_BUCKET' ) ) {
			return self::STORAGE_BUCKET_LT90;
		}

		return GD_STORAGE_USED_BUCKET;
	}

	public static function get_storage_bucket_message( $bucket ) {
		$bucket_messages = [
			self::STORAGE_BUCKET_LT90      => __( 'less than 90% used', 'gd-system-plugin' ),
			self::STORAGE_BUCKET_BTW90_100 => __( 'more than 90% used', 'gd-system-plugin' ),
			self::STORAGE_BUCKET_GT100     => __( 'full', 'gd-system-plugin' ),
		];;

		return $bucket_messages[ $bucket ] ?? $bucket;
	}

}
