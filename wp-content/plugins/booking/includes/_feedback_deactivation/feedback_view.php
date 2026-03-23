<?php
/**
 * Deactivation popup admin
 *
 * Link to Booking Calendar contact form page.
 *
 * @package     Booking Calendar/Admin
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $status, $page, $s;

if ( is_plugin_active( 'booking-calendar-com/booking-calendar-com.php' ) ) {
	$wpbc_deactivate_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . WPBC_PRO_PLUGIN_DIRNAME . htmlentities('/', ENT_COMPAT) . WPBC_PRO_PLUGIN_FILENAME . '&amp;plugin_status=' . $status . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . WPBC_PRO_PLUGIN_DIRNAME . htmlentities('/', ENT_COMPAT) . WPBC_PRO_PLUGIN_FILENAME  );
} else {
	$wpbc_deactivate_url = wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . WPBC_PLUGIN_DIRNAME . htmlentities('/', ENT_COMPAT) . WPBC_PLUGIN_FILENAME . '&amp;plugin_status=' . $status . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . WPBC_PLUGIN_DIRNAME . htmlentities('/', ENT_COMPAT) . WPBC_PLUGIN_FILENAME  );
}

?>
<div id="wpbc_deactivate-feedback-popup-wrapper">
	<div class="wpbc_deactivate-feedback-popup-inner">
		<div class="wpbc_deactivate-feedback-popup-header">
			<div class="wpbc_deactivate-feedback-popup-header__logo-wrap">
				<div class="wpbc_deactivate-feedback-popup-header__logo-icon">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_get_svg_logo_for_html(
						array(
							'svg_color'     => '#444',
							'svg_color_alt' => '#bbb',
							'opacity'       => '0.35',
							'style_default' => 'background-repeat: no-repeat; background-position: center; display: inline-block; vertical-align: middle;',
							'style_adjust'  => 'background-size: 30px auto; width: 30px; height: 30px; margin-top: 0px;',  // This parameters, the adjust size of the logo and position.
							'css_class'     => '',
						)
					);
					?>
				</div>
				<span class="wpbc_deactivate-feedback-popup-header-title"><?php echo esc_html__( 'Help Us Improve!', 'booking' ); ?></span>
			</div>
			<a class="close-deactivate-feedback-popup"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<form class="wpbc_deactivate-feedback-form" method="POST">
			<?php
			wp_nonce_field( '_wpbc_deactivate_feedback_nonce' );
			?>
			<input type="hidden" name="action" value="wpbc_deactivate_feedback"/>

			<div class="wpbc_deactivate-feedback-popup-form-caption">
				<?php
				echo esc_html__( 'We\'re sorry to see you leave. Could you tell us why?', 'booking' );
				?>
			</div>
			<div class="wpbc_deactivate-feedback-popup-form-body">
				<div class="wpbc_deactivate-feedback-popup-form-questions">
				<?php foreach ( $wpbc_deactivate_reasons as $wpbc_reason_slug => $wpbc_reason ) : ?>
					<div class="wpbc_deactivate-feedback-popup-input-wrapper">
						<input id="wpbc_deactivate-feedback-<?php echo esc_attr( $wpbc_reason_slug ); ?>"
							class="wpbc_deactivate-feedback-input" type="checkbox" name="reason_slug"
							value="<?php echo esc_attr( $wpbc_reason_slug ); ?>"/>
						<label for="wpbc_deactivate-feedback-<?php echo esc_attr( $wpbc_reason_slug ); ?>"
							class="wpbc_deactivate-feedback-label"><?php echo wp_kses_post( $wpbc_reason['title'] ); ?></label>
					</div>
				<?php endforeach; ?>
				</div>
				<span class="consent">* <?php esc_html_e( 'By submitting this form, you will also be sending us your email address & website URL.', 'booking' ); ?></span>
			</div>
			<div class="wpbc_deactivate-feedback-popup-form-footer">
				<a href="<?php echo esc_url( $wpbc_deactivate_url ); ?>" class="skip"><?php esc_html_e( 'Skip &amp; Deactivate', 'booking' ); ?></a>
				<div class="wpbc_deactivate-feedback-popup-form-more-details">
					<label for="wpbc_deactivate-feedback-more-details"
						class="wpbc_deactivate-feedback-label"><?php echo wp_kses_post( __( 'Could you share more details? We\'d love to fix them!', 'booking' ) ); ?></label>
					<textarea name="wpbc_deactivate-feedback-more-details" placeholder="<?php echo esc_attr( __( 'Please share your feedback', 'booking' ) ); ?>"
							class="feedback-textarea" rows="4"></textarea>
				</div>
				<button class="submit" type="submit"><?php esc_html_e( 'Submit &amp; Deactivate', 'booking' ); ?></button>
			</div>
		</form>
	</div>
</div>
