<?php

namespace Wpsec\twofa\web\html\login;


/**
 * Login App Template.
 *
 * @package Wpsec
 * @subpackage Wpsec/web/html/login
 */
class LoginHeaderTemplateCode {

	/**
	 * Render login yubikey template.
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		?>
		<p class="wpsec_2fa_login_text" id="wpsec_2fa_login_check_title">
			<strong><?php echo __( 'Choose the 2-Step Verification option you prefer', 'wpsec-wp-2fa' ); ?></strong>
		</p>
		<?php
	}
}
