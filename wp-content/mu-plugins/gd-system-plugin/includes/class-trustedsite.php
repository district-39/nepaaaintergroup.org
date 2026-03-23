<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class TrustedSite {

    use Helpers;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		if ( $this->should_embed() ) {
            wp_enqueue_script(
                'trustedsite-badge',
                'https://cdn.trustedsite.com/js/1.js',
                [],
                null,
                [
                    'in_footer' => true,
                    'strategy'  => 'async',
                ]
            );
		}
	}

	private function should_embed() {
		return !is_admin()
            && defined( 'GD_TRUSTEDSITE_ACTIVE' )
            && get_option( 'gd_trustedsite_show_badge', '1' ) === '1';
	}

}