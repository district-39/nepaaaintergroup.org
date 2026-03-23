<?php

namespace WPaaS\Admin;

use WP_Admin_Bar;
use WPaaS\Helpers;
use WPaaS\Storage_Limit_Checker;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Admin_Notices {

	use Helpers;

	/**
	 * Admin bar object.
	 *
	 * @var WP_Admin_Bar
	 */
	private $admin_bar;

	/**
	 * Storage limit checker object.
	 *
	 * @var Storage_Limit_Checker
	 */
	private $storage_limit_checker;

	/**
	 * Class constructor.
	 */
	public function __construct( Storage_Limit_Checker $storage_limit_checker ) {
		$this->storage_limit_checker = $storage_limit_checker;
		add_action( 'init', [ $this, 'init' ] );
	}


	/**
	 * Initialize the script.
	 *
	 * @action init
	 */
	public function init() {
		/**
		 * Update php version admin notice.
		 *
		 * @since php version is less than 8.0
		 */
		if ( version_compare( PHP_VERSION, 8.0 ) < 0 ) {
			$update_php_version_alert_text = self::get_update_php_alert_text();
			new Notice( $update_php_version_alert_text, [ 'error' ] );
		}

		/**
		 * Staging site admin notice.
		 *
		 * @since 2.0.11
		 */
		if ( self::is_staging_site() ) {
			new Notice( __( 'Note: This is a staging site.', 'gd-system-plugin' ), [ 'error' ] );
		}

		$cdn_full_page = defined( 'GD_CDN_FULLPAGE' ) ? GD_CDN_FULLPAGE : false;

		if ( is_admin() && false === $cdn_full_page && $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( 'cdn_cohort_2',
				false ) ) {
			new Notice(
				__( 'Within the next 14 days, we are updating our Content Delivery Network (CDN). You can expect to see improved performance, with faster load times and a more secure environment for your data. We are committed to providing you with the best possible experience when using WordPress.',
					'gd-system-plugin' ),
				[ 'info' ], 'activate_plugins', true
			);
		}

		$cdn_static = defined( 'GD_CDN_ENABLED' ) ? GD_CDN_ENABLED : false;
		if ( is_admin() && false === $cdn_static && $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( 'cdn_static_enable',
				false ) ) {
			new Notice(
				__( 'Within the next 30 days, we are enabling our improved Content Delivery Network (CDN) on your site. You can expect to see improved performance, specifically with the image assets on your site. We are committed to providing you with the best possible experience when using WordPress.',
					'gd-system-plugin' ),
				[ 'info' ], 'activate_plugins', true
			);
		}

		$should_check_storage = $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( 'admin_storage_notice_gd', false );

		/**
		 * Site storage usage admin notice.
		 *
		 */
		if ( is_admin()
		     && self::is_wpaas_v2()
		     && ! self::is_mwcs()
		     && ( $should_check_storage ) ) {

			$storage_bucket = $this->storage_limit_checker->get_storage_bucket();

			if ( $storage_bucket === Storage_Limit_Checker::STORAGE_BUCKET_BTW90_100 ||
			     $storage_bucket === Storage_Limit_Checker::STORAGE_BUCKET_GT100 ) {

				$myh_url = self::get_myh_add_storage_url();

				$notice_text  = $storage_bucket === Storage_Limit_Checker::STORAGE_BUCKET_GT100 ?
					__( 'You won’t be able to add media or install plugins and themes until you clear up space or add more storage.', 'gd-system-plugin' ) :
					__( 'Once you hit 100%, you won’t be able to add media or install plugins and themes until you clear up space or add more storage.', 'gd-system-plugin' );
				$notice_class = $storage_bucket === Storage_Limit_Checker::STORAGE_BUCKET_GT100 ? 'notice-error' : 'notice-warning';

				new Notice(
					'<strong>' . sprintf( /* translators: 1. Storage bucket description. */ __( 'Your storage is %1$s', 'gd-system-plugin' ), Storage_Limit_Checker::get_storage_bucket_message( $storage_bucket ) ) . '</strong>' .
					'<p>' . $notice_text . '</p>' .
					'<a href="' . $myh_url . '" target="_blank">' . __( 'Add More Storage', 'gd-system-plugin' ) . '</a>',
					[ $notice_class ], 'activate_plugins', $storage_bucket === Storage_Limit_Checker::STORAGE_BUCKET_BTW90_100
				);
			}

		}


	}

}
