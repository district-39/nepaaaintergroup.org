<?php
/**
 * Booking Calendar Admin Deactivation Feedback Class
 *
 * @package Booking Calendar Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPBC_Admin_Deactivation_Feedback', false ) ) :

	/**
	 * WPBC_Admin_Deactivation_Feedback Class
	 */
	class WPBC_Admin_Deactivation_Feedback {

		const FEEDBACK_URL = 'https://wpbookingcalendar.com/wp-json/tgreporting/v1/deactivation/';

		/**
		 * Class constructor.
		 * Attaches the necessary actions and filters for the deactivate feedback feature.
		 * Adds an action to enqueue scripts on the plugins screen.
		 * Adds an action to handle the AJAX request for sending deactivate feedback.
		 */
		public function __construct() {
			add_action(
				'current_screen',
				function () {
					if ( ! $this->is_plugins_screen() ) {
						return;
					}

					add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
				}
			);

			// Ajax.
			add_action( 'wp_ajax_wpbc_deactivate_feedback', array( $this, 'send' ) );
		}

		/**
		 * Enqueue scripts.
		 */
		public function scripts() {
			add_action( 'admin_footer', array( $this, 'feedback_html' ) );

			// $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$suffix = '';

			wp_enqueue_script( 'wpbc-admin-deactivation-feedback',
								wpbc_plugin_url( '/includes/_feedback_deactivation/_out/feedback.js' ),
								array( 'jquery' ),
								WP_BK_VERSION_NUM,
								array( 'in_footer' => WPBC_JS_IN_FOOTER )
			);
			wp_localize_script( 'wpbc-admin-deactivation-feedback', 'wpbc_plugins_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			wp_enqueue_style( 'wpbc-admin-deactivation-feedback', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/feedback.css', array(), WP_BK_VERSION_NUM );
		}


		/**
		 * Deactivation Feedback HTML.
		 *
		 * @return void
		 */
		public function feedback_html() {
			$wpbc_deactivate_reasons = array(
				'feature_unavailable'    => array(
					'title'             => esc_html__( 'Missing important features', 'booking' ),
				),
				'complex_to_use'         => array(
					'title'             => esc_html__( 'I found the plugin complex to use.', 'booking' ),
				),
				'temporary_deactivation' => array(
					'title'             => esc_html__( 'Temporary deactivation', 'booking' ),
				),
				'found_a_better_plugin'  => array(
					'title'             => esc_html__( 'I found a better alternative', 'booking' ),
				),
				'no_longer_needed'       => array(
					'title'             => esc_html__( 'I no longer need the plugin', 'booking' ),
				),
				'setup_confusing'       => array(
					'title'             => esc_html__( 'Plugin setup process is confusing', 'booking' ),
				),
				'no_documentation'       => array(
					'title'             => esc_html__( 'Lack of documentation or tutorials', 'booking' ),
				),
				'other'                  => array(
					'title'             => esc_html__( 'Technical issues / Bugs', 'booking' ),
				),
			);

			require_once WPBC_PLUGIN_DIR . '/includes/_feedback_deactivation/feedback_view.php';           // API
		}

		/**
		 * Send API Request.
		 *
		 * @return void
		 */
		public function send() {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), '_wpbc_deactivate_feedback_nonce' ) ) {
				wp_send_json_error();
			}

			$reason_text = '---';
			$reason_slug = '';

			if ( ! empty( $_POST['reason_slug'] ) ) {
				$reason_slug = sanitize_text_field( wp_unslash( $_POST['reason_slug'] ) );
			}

			if ( ! empty( $_POST[ "wpbc_deactivate-feedback-more-details" ] ) ) {
				$reason_text = sanitize_textarea_field( wp_unslash( $_POST[ "wpbc_deactivate-feedback-more-details" ] ) );
			}

			$deactivation_data = array(
				'reason_text'  => $reason_text,
				'reason_slug'  => $reason_slug,
				'admin_email'  => get_bloginfo( 'admin_email' ),
				'website_url'  => esc_url_raw( get_bloginfo( 'url' ) ),
				'base_product' => is_plugin_active( 'booking-calendar-com/booking-calendar-com.php' ) ? 'booking-calendar-com/booking-calendar-com.php' : 'booking/wpdev-booking.php',
			);

			// TODO: Make in future API request.
			// $this->send_api_request( $deactivation_data );

			wpbc_feedback_deactivation__send_email( $reason_slug, implode( " \n\n", $deactivation_data ) );

			wp_send_json_success();
		}

		/**
		 * Sends an API request with deactivation data.
		 *
		 * @param array $deactivation_data Deactivation Data.
		 * @return string The response body from the API request.
		 */
		private function send_api_request( $deactivation_data ) {
			$headers = array(
				'user-agent' => 'BookingCalendar/' . WP_BK_VERSION_NUM . '; ' . get_bloginfo( 'url' ),
			);

			$response = wp_remote_post(
				self::FEEDBACK_URL,
				array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => $headers,
					'body'        => array( 'deactivation_data' => $deactivation_data ),
				)
			);
			return wp_remote_retrieve_body( $response );
		}

		/**
		 * Check if the current screen is the plugins screen and returns a boolean.
		 *
		 * @return boolean
		 */
		private function is_plugins_screen() {
			return in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true );
		}
	}

	new WPBC_Admin_Deactivation_Feedback();
endif;




/**
 * Send email with  feedback
 *
 * @param $uninstall_slug
 * @param $feedback_description
 *
 * @return void
 */
function wpbc_feedback_deactivation__send_email( $uninstall_slug, $feedback_description ) {

 	$us_data = wp_get_current_user();

	$fields_values = array();
	$fields_values['from_email'] = get_option( 'admin_email' );
	$fields_values['from_name']  = $us_data->display_name;
	$fields_values['from_name']  = wp_specialchars_decode( esc_html( stripslashes( $fields_values['from_name'] ) ), ENT_QUOTES );
	$fields_values['from_email'] = sanitize_email( $fields_values['from_email'] );

	$subject = 'WPBC Uninstall: ' . $uninstall_slug . '';


	$message = '';
	$message .= $feedback_description . "\n";
	$message .= '=====' . "\n";

	$cleaned_data_booking_feedback_arr = get_bk_option( 'booking_feedback__after_send' );
	if ( ( ! empty( $cleaned_data_booking_feedback_arr ) ) && ( is_array( $cleaned_data_booking_feedback_arr ) ) ) {
		$feedback_description_setup = implode( "\n", $cleaned_data_booking_feedback_arr );
		$message .= $feedback_description_setup . "\n";
		$message .= '=====' . "\n";
	}


	$message .="\n";

	$message .= $fields_values['from_name'] . "\n";
	$message .="\n";
	$message .= '---------------------------------------------' . "\n";

	$message .= 'Booking Calendar ' . wpbc_feedback_01_get_version()  . "\n";

	$how_old_info_arr = wpbc_get_info__about_how_old();
	if ( ! empty( $how_old_info_arr ) ) {
		$message .= "\n";
		$message .= 'From: ' . $how_old_info_arr['date_echo'];
		$message .= ' - ' . $how_old_info_arr['days'] . ' days ago.';
	}

	$message .="\n";
	$message .= '---------------------------------------------' . "\n";
	$message .= '[' .  date_i18n( get_bk_option( 'booking_date_format' ) ) . ' ' .  date_i18n( get_bk_option( 'booking_time_format' ) ) . ']'. "\n";
	$message .= home_url() ;


	$headers = '';

	if ( ! empty( $fields_values['from_email'] ) ) {

		$headers .= 'From: ' . $fields_values['from_name'] . ' <' . $fields_values['from_email'] . '> ' . "\r\n";
	} else {
            /* If we don't have an email from the input headers default to wordpress@$sitename
             * Some hosts will block outgoing mail from this address if it doesn't exist but
             * there's no easy alternative. Defaulting to admin_email might appear to be another
             * option but some hosts may refuse to relay mail from an unknown domain. See
             * https://core.trac.wordpress.org/ticket/5007.
             */
	}
	$headers .= 'Content-Type: ' . 'text/plain' . "\r\n" ;			// 'text/html'

	$attachments = '';


	$to = 'feedback_uninstall@wpbookingcalendar.com';

// debuge('In email', htmlentities($to), $subject, htmlentities($message), $headers, $attachments)  ;
// debuge( '$to, $subject, $message, $headers, $attachments',htmlspecialchars($to), htmlspecialchars($subject), htmlspecialchars($message), htmlspecialchars($headers), htmlspecialchars($attachments));
	if ( wpbc_email_api_is_allow_send( true, '', '' ) ) {
		$return = wp_mail( $to, $subject, $message, $headers, $attachments );
	}
}
