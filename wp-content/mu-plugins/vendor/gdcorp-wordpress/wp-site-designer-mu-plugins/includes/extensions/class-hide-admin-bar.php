<?php
/**
 * Hide Admin Bar Plugin
 *
 * @package wp-site-designer-mu-plugins
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use function add_action;
use function show_admin_bar;

/**
 * Hides the WordPress admin bar when Site Designer requests are detected
 */
class Hide_Admin_Bar {

	/**
	 * Initialize the class and register hooks
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();
		add_action( 'init', array( $instance, 'remove_admin_bar' ), 1 );
		add_action( 'admin_init', array( $instance, 'remove_admin_bar' ), 1 );
	}

	/**
	 * Hide the admin bar for Site Designer requests
	 *
	 * @return void
	 */
	public function remove_admin_bar(): void {
		// Check if we have logged in user.
		if ( ! is_user_logged_in() ) {
			return;
		}

		show_admin_bar( false );
	}
}
