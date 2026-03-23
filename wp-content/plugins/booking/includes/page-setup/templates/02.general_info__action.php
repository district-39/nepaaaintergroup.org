<?php /**
 * @version 1.0
 * @description Action  for  Template Setup pages
 * @category    Setup Action
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-29
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Action - General Info ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - General Info - Step 01
 *
 * 	Help Tips:
 *
 *		<script type="text/html" id="tmpl-template_name_a">
 * 			Escaped:  	 {{data.test_key}}
 * 			HTML:  		{{{data.test_key}}}
 * 			JS: 	  	<# if (true) { alert( 1 ); } #>
 * 		</script>
 *
 * 		var template__var = wp.template( 'template_name_a' );
 *
 * 		jQuery( '.content' ).html( template__var( { 'test_key' => '<strong>Data</strong>' } ) );
 *
 * @return void
 */


function wpbc_template__general_info__action_validate_data( $post_data ){

	$escaped_data = array(
		'wpbc_swp_industry'          => '',
		'type'                       => '',         // FixIn: 10.7.1.3.
		'days'                       => '',
		'version'                    => 'V: ' .wpbc_feedback_01_get_version(),
		'wpbc_swp_booking_who_setup' => '',
		'wpbc_swp_business_name'     => '',
		'wpbc_swp_email'             => '',
		'wpbc_swp_accept_send'       => 'Off'
	);

	$how_old_info_arr = wpbc_get_info__about_how_old();
	if ( ! empty( $how_old_info_arr ) ) {
		$escaped_data['days'] = 'Days: ' . $how_old_info_arr['days'];
	}

	$key = 'wpbc_swp_industry';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = 'Industry: ' . wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_booking_who_setup';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = 'Who_setup: ' . wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_business_name';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = 'Business_name: ' . wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_email';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = 'Email: ' . wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_accept_send';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}

	return $escaped_data;
}


/**
 * Send email with  Setup Feedback
 *
 * @param $feedback_description
 *
 * @return void
 */
function wpbc_setup_feedback__send_email( $feedback_description_arr ) {
	$feedback_description = implode( "\n", $feedback_description_arr);

 	$us_data = wp_get_current_user();

	$fields_values = array();
	$fields_values['from_email'] = get_option( 'admin_email' );
	$fields_values['from_name'] = $us_data->display_name;
	$fields_values['from_name'] = wp_specialchars_decode( esc_html( stripslashes( $fields_values['from_name'] ) ), ENT_QUOTES );
	$fields_values['from_email'] = sanitize_email( $fields_values['from_email'] );

	$subject = 'WPBC - Initial Setup';


	$message  = '';// '=============================================' . "\n";
	$message .= $feedback_description . "\n";
	$message .= '=============================================' . "\n";
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


	$to = 'setup_feedback@wpbookingcalendar.com';

// debuge('In email', htmlentities($to), $subject, htmlentities($message), $headers, $attachments)  ;
// debuge( '$to, $subject, $message, $headers, $attachments',htmlspecialchars($to), htmlspecialchars($subject), htmlspecialchars($message), htmlspecialchars($headers), htmlspecialchars($attachments));
	if ( wpbc_email_api_is_allow_send( true, '', '' ) ) {
		$return = @wp_mail( $to, $subject, $message, $headers, $attachments );
	}
}


/**
 *  Update "General Data" like "Email" and "Title"
 *
 * @param $cleaned_data     array(
 *		'wpbc_swp_business_name'     => '',
 *		'wpbc_swp_booking_who_setup' => '',
 *		'wpbc_swp_industry'          => '',
 *		'wpbc_swp_email'             => '',
 *		'wpbc_swp_accept_send'       => 'Off'
 *
 * )
 *
 * @return void
 */
function wpbc_setup__update__general_info( $cleaned_data ){

	if ( ( ! empty( $cleaned_data['wpbc_swp_email'] ) ) && ( false !== is_email( $cleaned_data['wpbc_swp_email'] ) ) ) {

		//update_bk_option( 'wpbc_swp_email', $cleaned_data['wpbc_swp_email'] );

		// -------------------------------------------------------------------------------------------------------------
        // Update Emails
		// -------------------------------------------------------------------------------------------------------------
        $email_option_names = array(  WPBC_EMAIL_NEW_ADMIN_PREFIX       . WPBC_EMAIL_NEW_ADMIN_ID
                                    , WPBC_EMAIL_NEW_VISITOR_PREFIX     . WPBC_EMAIL_NEW_VISITOR_ID
                                    , WPBC_EMAIL_APPROVED_PREFIX        . WPBC_EMAIL_APPROVED_ID
                                    , WPBC_EMAIL_DENY_PREFIX            . WPBC_EMAIL_DENY_ID
                                    , WPBC_EMAIL_TRASH_PREFIX           . WPBC_EMAIL_TRASH_ID
                                    , WPBC_EMAIL_DELETED_PREFIX         . WPBC_EMAIL_DELETED_ID
                              );
		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$email_option_names[] = WPBC_EMAIL_MODIFICATION_PREFIX    . WPBC_EMAIL_MODIFICATION_ID;
		}
		if ( class_exists('wpdev_bk_biz_s')) {
			$email_option_names[] = WPBC_EMAIL_PAYMENT_REQUEST_PREFIX . WPBC_EMAIL_PAYMENT_REQUEST_ID;
		}
        foreach ( $email_option_names as $email_option_name ) {

            // Get Email Data
            $email_data = get_bk_option( $email_option_name );

            $email_data['from'] =  $cleaned_data['wpbc_swp_email'];

	        if ( $email_option_name === WPBC_EMAIL_NEW_ADMIN_PREFIX . WPBC_EMAIL_NEW_ADMIN_ID ) {
		        $email_data['to'] = $cleaned_data['wpbc_swp_email'];
	        } else {
		        $email_data['to'] = $cleaned_data['wpbc_swp_email'];
	        }

            // Update Email Data
            update_bk_option( $email_option_name, $email_data );
        }
		// -------------------------------------------------------------------------------------------------------------
	}
}