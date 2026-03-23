<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Site_Optimizer {

	use Helpers;

	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js_scripts' ), - PHP_INT_MAX );
		self::site_optimizer_notice();
	}

	/**
	 * @return void
	 */
	public static function site_optimizer_notice() {

		if ( Plugin::is_gd()) {
			new Admin\Notice(
				self::get_notice_text(),
				array( 'notice-success' ),
				'activate_plugins',
				true
			);
		}
	}

	/**
	 * Provide the correct wording for the notice
	 *
	 * @return string
	 */
	public static function get_notice_text() {

		if (!defined('GD_ACCOUNT_UID')) {
			return '';
		}

		/**
		 * For GoDaddy customer.
		 */
        $link = Plugin::get_env() === 'prod' ?
            "https://host.godaddy.com/mwp/site/" . GD_ACCOUNT_UID . '/site-optimizer?source=wpadmin_banner' :
            "https://host.test-godaddy.com/mwp/site/" . GD_ACCOUNT_UID . '/site-optimizer?source=wpadmin_banner';

		$title = __('Let GoDaddy Airo™ Site Optimizer Improve your site.');
		$message = __('There may be opportunities to improve your site\'s SEO rank. ');
		$message .= '<a href='. "{$link}". ' target="_blank" rel="noopener" id="optimize-my-site-airo-banner">
                                        ' . __("Optimize my site") . '
                                </a>';

		$format = '
                <div>
                    <div>
                        <div><strong>'.$title.'</strong></div>
                        ' . $message . '
                    </div>
                </div>
                ';
		return $format;
	}

    public function enqueue_js_scripts()
    {
        if ( Plugin::is_gd()) {
            wp_enqueue_script('airo-site-optimizer', Plugin::assets_url('js/airo-site-optimizer.js'), array('jquery'));
        }
    }
}