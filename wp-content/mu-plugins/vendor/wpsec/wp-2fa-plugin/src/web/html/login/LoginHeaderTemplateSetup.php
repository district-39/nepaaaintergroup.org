<?php

namespace Wpsec\twofa\web\html\login;


/**
 * Login App Template.
 *
 * @package Wpsec
 * @subpackage Wpsec/web/html/login
 */
class LoginHeaderTemplateSetup {

	/**
	 * Render login yubikey template.
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		?>
		<p class="wpsec_2fa_login_text" id="wpsec_2fa_login_check_title">
				<strong><?php echo __( 'Set Up 2-Step Verification', 'wpsec-wp-2fa' ); ?></strong>
		</p>
		<p class="wpsec_2fa_login_text_bottom" id="wpsec_2fa_login_check_title_bottom">
			<?php echo __( 'Choose the option you prefer.', 'wpsec-wp-2fa' ); ?>
		</p>
		<?php
	}
}
