<?php
/**
 * @version 1.1
 * @package Booking Calendar 
 * @category Send Emails
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 15.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



////////////////////////////////////////////////////////////////////////////////
// Emails
////////////////////////////////////////////////////////////////////////////////

/**
 * Check email and format  it
 * 
 * @param string $emails
 * @return string
 */
function wpbc_validate_emails( $emails ) {

    $emails = str_replace(';', ',', $emails);

    if ( !is_array( $emails ) )
            $emails = explode( ',', $emails );

    $emails_list = array();
    foreach ( (array) $emails as $recipient ) {

        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
        $recipient_name = '';
        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
            if ( count( $matches ) == 3 ) {
                $recipient_name = $matches[1];
                $recipient = $matches[2];                 
            }
        } else {                
            // Check about correct  format  of email
            if( preg_match( '/([\w\.\-_]+)?\w+@[\w\-_]+(\.\w+){1,}/im', $recipient, $matches ) ) {                      // FixIn: 8.7.7.2.
                $recipient = $matches[0];
            }             
        }

        $recipient_name = str_replace('"', '', $recipient_name);
        $recipient_name = trim( wp_specialchars_decode( esc_html( stripslashes( $recipient_name ) ), ENT_QUOTES ) );

        $emails_list[] =   ( empty( $recipient_name ) ? '' : $recipient_name . ' '  )
                           . '<' . sanitize_email( $recipient ) . '>';		
    }

    $emails_list = implode( ',', $emails_list );

    return $emails_list;
}    


/**
 * Convert email  address to the correct  format  like  "Jony Smith" <smith@server.com> - tp  prevent "WordPress" title in email.
 * @param string $wpbc_mail - just  simple email
 * @param type $booking_form_show - array with  name and secondname of the person - title
 * @return string - formated email.
 */
function wpbc_email_prepand_person_name( $wpbc_mail, $booking_form_show = array() ) {

    //FixIn:5.4.4
    $wpbc_email_title =  ((isset($booking_form_show['firstname']))?$booking_form_show['firstname'].' ':'')
                        .((isset($booking_form_show['name']))?$booking_form_show['name'].' ':'')
                        .((isset($booking_form_show['lastname']))?$booking_form_show['lastname'].' ':'')
                        .((isset($booking_form_show['secondname']))?$booking_form_show['secondname'].' ':'');
    $wpbc_email_title = ( ( empty($wpbc_email_title ) ) ? __('Booking system' ,'booking') : substr( $wpbc_email_title, 0 , -1 ) );

    $wpbc_email_title = str_replace('"', '', $wpbc_email_title);
    $wpbc_email_title = trim( wp_specialchars_decode( esc_html( stripslashes( $wpbc_email_title ) ), ENT_QUOTES ) );

    $wpbc_mail = wpbc_validate_emails( $wpbc_email_title . ' <' .  $wpbc_mail . '> ' );

    return $wpbc_mail;
}

// Old, Replaced in api-emails
class wpbc_email_return_path {
    function __construct() {
            add_action( 'phpmailer_init', array( $this, 'fix' ) );    
    }

    function fix( $phpmailer ) {
            $phpmailer->Sender = $phpmailer->From;
    }
}


function wpbc_wp_mail( $mail_recipient, $mail_subject, $mail_body, $mail_headers ){

    $wpbc_email_return_path = new wpbc_email_return_path();

    // $mail_recipient = str_replace( '"', '', $mail_recipient );               //FixIn:5.4.3
	if ( wpbc_email_api_is_allow_send( true, '', '' ) ) {
		@wp_mail( $mail_recipient, $mail_subject, $mail_body, $mail_headers );
	}
    unset( $wpbc_email_return_path );
}





function wpbc_check_for_several_emails_in_form( $mail_recipients, $formdata, $bktype ) {  // FixIn: 6.0.1.9.

    $possible_other_emails = explode('~',$formdata);
    $possible_other_emails = array_map("explode", array_fill(0,count($possible_other_emails),'^'), $possible_other_emails);
    $other_emails = array();
    foreach ( $possible_other_emails as $possible_emails ) {
        if (       ( $possible_emails[0] == 'email' ) 
                //&& ( $possible_emails[1] != 'email' . $bktype ) 
                && ( ! empty($possible_emails[2]) ) 
            )
                $other_emails[]= trim( $possible_emails[2] );				// FixIn: 8.2.1.6.
    }
    $other_emails = array_unique( $other_emails );							// FixIn: 8.2.1.6.
    if ( count( $other_emails ) > 1 ) {
        $other_emails = implode(',',$other_emails);
        $mail_recipients =  $other_emails;
    }
    return $mail_recipients;
}


//  N E W  /////////////////////////////////////////////////////////////////////


/** 
	 * Parse email and get Parts of Email - Name and Email
 * 
 * @param string $email
 * @return array [email] => beta@wpbookingcalendar.com
                 [title] => Booking system
                 [original] => "Booking system" 
                 [original_to_show] => "Booking system" <beta@wpbookingcalendar.com>
 */         
function wpbc_get_email_parts( $email ) {
        
    $email_to_parse =  html_entity_decode( $email );                                 // Convert &quot; to " etc...
    
    $pure_name  = '';
    $pure_email = '';
    if( preg_match( '/(.*)<(.+)>/', $email_to_parse, $matches ) ) {
        if ( count( $matches ) == 3 ) {
            $pure_name = $matches[1];
            $pure_email = $matches[2];                 
        }
    } else {                                                                    // Check about correct  format  of email
        if( preg_match( '/([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}/im', $email_to_parse, $matches ) ) {
            $pure_email = $matches[0];
        }             
    }
    
    $pure_name = trim( wp_specialchars_decode( esc_html( stripslashes( $pure_name ) ), ENT_QUOTES ) , ' "');
    
    $return_email = array(
                            'email' => sanitize_email( $pure_email )
                            , 'title' => $pure_name
                            , 'original' => $email_to_parse
                            , 'original_to_show' => htmlentities( $email_to_parse )         // Convert " to  &quot;  etc...
                    );
    
    return $return_email;
}


	function wpbc_tooltip_help__fix_quote( $field_val ) {

		$field_val = str_replace( '"', "'", $field_val );

		return $field_val;
	}

// Get Emails Help Shortcodes for Settings pages
function wpbc_get_email_help_shortcodes( $skip_shortcodes = array() , $email_example = '') {

	$icn = '<a 	href="https://wpbookingcalendar.com/faq/email-shortcodes/#available_shortcodes" 
				class="tooltip_top wpbc-bi-question-circle wpbc_help_tooltip_icon_left" 				 
				data-original-title="%2$s"></a> %1$s';

    $fields = array();
    
    if ( class_exists('wpdev_bk_personal') ) { 
        $fields[] = sprintf(__('You can use (in subject and content of email template) any shortcodes, which you used in the booking form. Use the shortcodes in the same way as you used them in the content form at Settings Fields page.' ,'booking'));
        $fields[] = '<hr/>';
    }
    $fields[] = '<strong>' . esc_html__('You can use following shortcodes in content of this template' ,'booking') . '</strong>';
    
    // [content]
	if ( class_exists( 'wpdev_bk_personal' ) ) {
		/* translators: 1: ... */
		$fields[] = sprintf( $icn, '<code>[content]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting data info about the booking, which you configured in the content form at Settings Fields page', 'booking' ), '[content]' ) ) );
	} else {
		/* translators: 1: ... */
		$fields[] = sprintf( $icn, '<code>[content]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting data info about the booking', 'booking' ), '[content]' ) ) );
	}


    // [dates]    
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[dates]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting the dates of booking' ,'booking'), '[dates]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[only_dates]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting only booking dates without times' ,'booking'), '[only_dates]' ) ) );

    // [check_in_date]
    if ( ! in_array( 'check_in_date', $skip_shortcodes ) ) {
	    /* translators: 1: ... */
	    $fields[] = sprintf( $icn, '<code>[check_in_date]</code>', wpbc_tooltip_help__fix_quote( sprintf(  __( '%s - inserting check-in date (first day of reservation),', 'booking' ), '[check_in_date]' ) ) );
	    /* translators: 1: ... */
	    $fields[] = sprintf( $icn, '<code>[check_in_only_date]</code>', wpbc_tooltip_help__fix_quote( sprintf(  __( '%s - inserting check-in date (only date without time) (first day of reservation),', 'booking' ), '[check_in_only_date]' ) ) );	// FixIn: 8.7.2.5.
    }
    // [check_out_date] [check_out_plus1day]
    if ( ! in_array( 'check_out_date', $skip_shortcodes ) ) {
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[check_out_date]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting check-out date (last day of reservation),' ,'booking'), '[check_out_date]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[check_out_only_date]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting check-out date (only date without time) (last day of reservation),' ,'booking'), '[check_out_only_date]' ) ) );		// FixIn: 8.7.2.5.
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[check_out_plus1day]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting check-out date (last day of reservation),' ,'booking'), '[check_out_plus1day]') .  ' + 1 ' . __('day', 'booking') ) );
    }
    
    // [dates_count]
    if ( ! in_array( 'dates_count', $skip_shortcodes ) ) {
		/* translators: 1: ... */
		$fields[] = sprintf( $icn, '<code>[dates_count]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting the number of booking dates ', 'booking' ), '[dates_count]' ) ) );
	}

    $fields[] = '<hr/>';

    
    // [id]
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[booking_id]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting ID of booking ' ,'booking'), '[id], [booking_id]' )  ) );
    
    // [resource_title]  [bookingtype]
	if ( class_exists( 'wpdev_bk_personal' ) ) {
		if ( ! in_array( 'bookingtype', $skip_shortcodes ) ) {
			/* translators: 1: ... */
			$fields[] = sprintf( $icn, '<code>[resource_title]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%1$s or %2$s - inserting the title of the booking resource ', 'booking' ), '[resource_title]', '[bookingtype]' ) ) );
		}
	}
	// FixIn: 10.9.2.4.
	if ( class_exists( 'wpdev_bk_biz_l' ) ) {
		if ( ! in_array( 'bookingtype', $skip_shortcodes ) ) {
			/* translators: 1: ... */
			$fields[] = sprintf( $icn, '<code>[parent_resource_title]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting the title of the parent booking resource ', 'booking' ), '[parent_resource_title]' ) ) );
		}
	}

        
    // [cost]    
	if ( class_exists( 'wpdev_bk_biz_s' ) ) {
		if ( ! in_array( 'cost', $skip_shortcodes ) ) {
			/* translators: 1: ... */
			$fields[] = sprintf( $icn, '<code>[cost]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting the cost of  booking ', 'booking' ), '[cost]' ) ) );
		}
	}
    
    $fields[] = '<hr/>';   

    // [siteurl]
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[siteurl]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting your site URL ' ,'booking'), '[siteurl]' ) ) );
    
    if ( class_exists('wpdev_bk_personal') ) {    
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[remote_ip]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting IP address of the user who made this action ' ,'booking'), '[remote_ip]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[user_agent]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting contents of the User-Agent: header from the current request, if there is one ' ,'booking'), '[user_agent]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[request_url]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting address of the page (if any), where visitor make this action ' ,'booking'), '[request_url]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[current_time]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting time of this action ' ,'booking'), '[current_time]' ) ) );
    }

    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[current_date]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting date of this action ' ,'booking'), '[current_date]' ) ) );

    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_date]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_date]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_year]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_year]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_month]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_month]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_day]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_day]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_hour]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_hour]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_minutes]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_minutes]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[modification_seconds]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting modification date of booking ' ,'booking'), '[modification_seconds]' ) ) );
	// FixIn: 10.0.0.34.
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_date]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_date]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_year]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_year]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_month]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_month]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_day]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_day]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_hour]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_hour]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_minutes]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_minutes]' ) ) );
    /* translators: 1: ... */
    $fields[] = sprintf( $icn, '<code>[creation_seconds]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting creation date of booking ' ,'booking'), '[creation_seconds]' ) ) );



    // [moderatelink]
    if ( ! in_array( 'moderatelink', $skip_shortcodes ) ) {
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[moderatelink]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting moderate link of new booking ' ,'booking'), '[moderatelink]' ) ) );

        // FixIn: 8.4.7.25.
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[click2approve]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting link to approve booking in 1 mouse click ' ,'booking'), '[click2approve]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[click2decline]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting link to set booking as pending in 1 mouse click ' ,'booking'), '[click2decline]' ) ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[click2trash]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting link for move booking to trash in 1 mouse click ' ,'booking'), '[click2trash]' ) ) );

	}

	// FixIn: 9.6.3.8.
    if ( ! in_array( 'add_to_google_cal_button', $skip_shortcodes ) ) {
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[add_to_google_cal_button]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting link for export booking to' ,'booking'), '[add_to_google_cal_button]') . ' Google Calendar' ) );
        /* translators: 1: ... */
        $fields[] = sprintf( $icn, '<code>[add_to_google_cal_url]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - inserting URL for export booking to' ,'booking'), '[add_to_google_cal_url]') . ' Google Calendar' ) );

	}


    if ( class_exists('wpdev_bk_personal') ) { 

    	//FixIn: 8.1.3.5.1
		if ( ! in_array( 'visitorbookingslisting', $skip_shortcodes ) ) {
			/* translators: 1: ... */
			$fields[] = sprintf( $icn, '<code>[visitorbookingslisting]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%1$s - inserting link to the page where visitor can see listing of own bookings,  (possible to use the %2$s parameter for setting different %3$s of this page. Example: %4$s )', 'booking' ), '[visitorbookingslisting]', '"url"', 'URL', '[visitorbookingslisting url="http://www.server.com/custom-page/"]' )
			/* translators: 1: ... */
			. ' ' . sprintf( __( 'Example of HTML a link usage: %s )', 'booking' ), '[visitorbookingslisting url="http://www.server.com/listing-custom-page/" type="link" title="Listing"]]' ) ) );
		}

        if ( ! in_array( 'visitorbookingediturl', $skip_shortcodes ) ) {
			/* translators: 1: ... */
			$fields[] = sprintf( $icn, '<code>[visitorbookingediturl]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%1$s - inserting link to the page where visitor can edit the reservation,  (possible to use the %2$s parameter for setting different %3$s of this page. Example: %4$s )', 'booking' ), '[visitorbookingediturl]', '"url"', 'URL', '[visitorbookingediturl url="http://www.server.com/custom-page/"]' )
			/* translators: 1: ... */
			. ' ' . sprintf( __( 'Example of HTML a link usage: %s )', 'booking' ), '[visitorbookingediturl url="http://www.server.com/edit-custom-page/" type="link" title="Edit Booking"]]' ) ) );
		}
        // [visitorbookingcancelurl]
	    if ( ! in_array( 'visitorbookingcancelurl', $skip_shortcodes ) ) {

			/* translators: 1: ... */
		    $fields[] = sprintf( $icn, '<code>[visitorbookingcancelurl]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%1$s - inserting link to the page where visitor can cancel the reservation, (possible to use the %2$s parameter for setting different %3$s of this page. Example: %4$s )', 'booking' ), '[visitorbookingcancelurl]', '"url"', 'URL', '[visitorbookingcancelurl url="http://www.server.com/custom-page/"]' )
			/* translators: 1: ... */
			. ' ' . sprintf( __( 'Example of HTML a link usage: %s )' ,'booking'), '[visitorbookingcancelurl url="http://www.server.com/cancel-custom-page/" type="link" title="Cancel Booking"]]' )
			) );
	    }
        
        if ( class_exists('wpdev_bk_biz_s') )  {
			// [visitorbookingpayurl]
			if ( ! in_array( 'visitorbookingpayurl', $skip_shortcodes ) ) {
				/* translators: 1: ... */
				$fields[] = sprintf( $icn, '<code>[visitorbookingpayurl]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%1$s - inserting link to payment page where visitor can pay for the reservation  (possible to use the %2$s parameter for setting different %3$s of this page. Example: %4$s )', 'booking' ), '[visitorbookingpayurl]', '"url"', 'URL', '[visitorbookingpayurl url="http://www.server.com/custom-page/"]' )
				/* translators: 1: ... */
				. ' ' . sprintf( __( 'Example of HTML a link usage: %s )', 'booking' ), '[visitorbookingpayurl url="http://www.server.com/payment-custom-page/" type="link" title="Pay Now"]]' ) ) );
			}

            // [paymentreason]
			if ( ! in_array( 'paymentreason', $skip_shortcodes ) ) {
				/* translators: 1: ... */
				$fields[] = sprintf( $icn, '<code>[paymentreason]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - add the reason for booking payment, you can enter it before sending email, ', 'booking' ), '[paymentreason]' ) ) );
			}
        }
    }
    
    // [denyreason]
	if ( ! in_array( 'denyreason', $skip_shortcodes ) ) {
		/* translators: 1: ... */
		$fields[] = sprintf( $icn, '<code>[denyreason]</code>', wpbc_tooltip_help__fix_quote( sprintf( __( '%s - add the reason booking was cancelled, you can enter it before sending email, ', 'booking' ), '[denyreason]' ) ) );
	}
    
    
    //$fields[] = __('HTML tags is accepted.' ,'booking');
    
    $fields[] = '<hr/>';
    
    // show_additional_translation_shortcode_help
    $fields[] = '<strong>' . sprintf(__('Configuration in several languages' ,'booking') ) . '.</strong>';
    /* translators: 1: ... */
    $fields[] = sprintf( __( '%1$s - start new translation section, where %2$s - locale of translation', 'booking' ),'<code>[lang=LOCALE]</code>','<code>LOCALE</code>');
    /* translators: 1: ... */
    $fields[] = sprintf(__( 'Example #1: %s - start French translation section' ,'booking'),'<code>[lang=fr_FR]</code>');
    /* translators: 1: ... */
    $fields[] = sprintf(__( 'Example #2: "%s" - English and French translation of some message' ,'booking'),'<code>Thank you for your booking.[lang=fr_FR]Je vous remercie de votre reservation.</code>');


    return $fields;
}


function wpbc_email_troubleshooting_help_notice(){

	?><div class="wpbc-settings-notice notice-warning notice-helpful-info">
		<?php
	/* translators: 1: ... */
	echo wp_kses_post( sprintf( __( 'To ensure your notifications arrive in your and your customers inboxes, we recommend connecting your email address to your domain and setting up a dedicated SMTP server. If something does not seem to be sending correctly, install the %1$s or check the %2$sEmail FAQ%3$s page.', 'booking' ),
			'<a href="https://wordpress.org/plugins/wp-mail-logging/">WP Mail Logging Plugin</a>',
			'<a href="https://wpbookingcalendar.com/faq/no-emails/">', '</a>' ) ); ?>
	</div><?php
}


/** 
	 * Check  Email  subject  about Language sections
 * 
 * @param string $subject
 * @param string $email_id
 * @return string
 */
function wpbc_email_api_get_subject_before( $subject, $email_id ) {
            
    $subject =  wpbc_lang( $subject );

    return  $subject;
}
add_filter( 'wpbc_email_api_get_subject_before', 'wpbc_email_api_get_subject_before', 10, 2 );    // Hook fire in api-email.php


/** 
	 * Check  Email  sections content  about Language sections
 * 
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbc_email_api_get_content_before( $fields_values, $email_id , $email_type ) {

	if ( isset( $fields_values['content'] ) ) {
		$fields_values['content'] = wpbc_lang( $fields_values['content'] );
		if ( $email_type == 'html' ) {
			$fields_values['content'] = make_clickable( $fields_values['content'] );
		}
	}

	if ( isset( $fields_values['header_content'] ) ) {
		$fields_values['header_content'] = wpbc_lang( $fields_values['header_content'] );
	}

	if ( isset( $fields_values['footer_content'] ) ) {
		$fields_values['footer_content'] = wpbc_lang( $fields_values['footer_content'] );
	}

	return $fields_values;
}
add_filter( 'wpbc_email_api_get_content_before', 'wpbc_email_api_get_content_before', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Modify email  content,  if needed. - In HTML mail content,  make links clickable.
 * 
 * @param array $email_content - content of Email
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbc_email_api_get_content_after( $email_content, $email_id , $email_type ) {
    
    if (  ( $email_type == 'html' ) || ( $email_type == 'multipart' )  )
       $email_content = make_clickable( $email_content );

    return $email_content;
}
add_filter( 'wpbc_email_api_get_content_after', 'wpbc_email_api_get_content_after', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Check  Email  Headers  -  in New Booking Email (to admin) set Reply-To header to visitor email.
 * 
 * @param string $headers
 * @param string $email_id - Email ID
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param array $replace_array - list  of relpaced shortcodes
 * @return string
 */
function wpbc_email_api_get_headers_after( $mail_headers, $email_id , $fields_values , $replace_array, $additional_params = array() ) {
       
/*
// Default in api-emails.php:
//        $mail_headers  = 'From: ' . $this->get_from__name() . ' <' .  $this->get_from__email_address() . '> ' . "\r\n" ;
//        $mail_headers .= 'Content-Type: ' . $this->get_content_type() . "\r\n" ;
//        
//            $mail_headers = "From: $mail_sender\n";
//            preg_match('/<(.*)>/', $mail_sender, $simple_email_matches );
//            $reply_to_email = ( count( $simple_email_matches ) > 1 ) ? $simple_email_matches[1] : $mail_sender;
//            $mail_headers .= 'Reply-To: ' . $reply_to_email . "\n";        
//            $mail_headers .= 'X-Sender: ' . $reply_to_email . "\n";
//            $mail_headers .= 'Return-Path: ' . $reply_to_email . "\n";
*/

//debuge($mail_headers, $email_id , $fields_values , $replace_array);    
    if (
          ( $email_id == 'new_admin' )                                            // Only  for email: "New Booking to Admin"
       || ( isset( $additional_params['reply'] ) )  
    ){
	    // FixIn: 9.7.3.17.
        if (
			   ( ! empty( $replace_array['email'] ) )
			&& ( ! empty( $fields_values['enable_replyto'] ) )
			&& (  'On' == $fields_values['enable_replyto'] )
        ){                                // Get email from  the booking form.
           
            $reply_to_email = sanitize_email( $replace_array['email'] );
	        if ( ! empty( $reply_to_email ) ) {
		        $mail_headers .= 'Reply-To: ' . $reply_to_email . "\r\n";
	        }
           // $mail_headers .= 'X-Sender: '    . $reply_to_email  . "\r\n" ;
           // $mail_headers .= 'Return-Path: ' . $reply_to_email  . "\r\n" ;           
        }
    }

    return  $mail_headers;
}
add_filter( 'wpbc_email_api_get_headers_after', 'wpbc_email_api_get_headers_after', 10, 5 );    // Hook fire in api-email.php


/**
 * Check if we can send Email - block  sending in live demos
 *
 * @param bool   $is_send_email  - is send emails?.
 * @param string $email_id      - email  id.
 * @param array  $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 *
 * @return bool
 */
function wpbc_email_api_is_allow_send( $is_send_email, $email_id, $fields_values ) {

	if ( wpbc_is_this_demo() ) {
		$is_send_email = false;
	}
	// FixIn: 10.12.1.5.
	if ( WPBC_IS_PLAYGROUND ) {
		$is_send_email = false;
	}

	return $is_send_email;
}

add_filter( 'wpbc_email_api_is_allow_send', 'wpbc_email_api_is_allow_send', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Show warning about not sending emails,  and reason about this.
 * 
 * @param object $wp_error_object     - WP Error object
 * @param string $error_description   - Description
 */
function wpbc_email_sending_error( $wp_error_object, $error_description = '' ) {

	if ( ( defined( 'WPBC_AJAX_ERROR_CATCH' ) ) && (  WPBC_AJAX_ERROR_CATCH ) ) { return  false; }                      // FixIn: 9.2.1.10.

    if ( empty( $error_description ) ) {
//        $error_description = __( 'Unknown exception', 'booking' ) . '.';        // Overwrite to  show error, if no description ???    
    }
    
    if ( ! empty( $error_description ) ) {

        $error_description = '' . __('Error', 'booking')  . '! ' . __('Email was not sent. An error occurred.', 'booking') .  ' ' . $error_description;
        
        // Admin side
        if (  function_exists( 'wpbc_show_message' ) ) {
            wpbc_show_message ( $error_description , 15 , 'error');     

        }
        
        // Front-end
        ?>   
        <script type="text/javascript">  
            if (typeof( wpbc_front_end__show_message__warning_under_element ) == 'function') {
                wpbc_front_end__show_message__warning_under_element( '.booking_form' , '<?php echo esc_js( $error_description ) ; ?>' );
            }
        </script>    
        <?php    
    } else {
        
        // Error that have no description. Its can be Empty Object like this: WP_Error Object(  'errors' => array(), 'error_data' => array() ),  or NOT
        // debuge( $wp_error_object );        
    }
}
add_action('wpbc_email_sending_error', 'wpbc_email_sending_error', 10, 2);