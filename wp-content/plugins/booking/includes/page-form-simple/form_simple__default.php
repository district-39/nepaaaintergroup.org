<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Simple Booking Form Setup
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-08-16
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

/**
 * Get  'Blank Visual Structure'    of booking form
 *
 * It can be imported from very old Free version,  if such options exist in plugin DB,  otherwise it is blank template
 *
 * @return array
 */
function wpbc_simple_form__visual__get_default_form() {
	$visual_form_structure = array();
	// Calendar.
	$visual_form_structure[] = array(
		'type'              => 'calendar',
		'name'              => 'calendar',
		'obligatory'        => 'On',
		'if_exist_required' => 'On',
		'label'             => '',               // __('Select date', 'booking') //.
	);
	// Time - Slots.
	$time_slots_arr = array(
		'09:00 - 10:00',
		'10:00 - 11:00',
		'11:00 - 12:00',
		'12:00 - 13:00',
		'13:00 - 14:00',
		'14:00 - 15:00',
		'15:00 - 16:00',
		'16:00 - 17:00',
		'17:00 - 18:00',
		'18:00 - 19:00',
	);
	$time_slots_str = '';
	foreach ( $time_slots_arr as $time_slot_v ) {
		$time_slots_str .= wpbc_time_slot_in_format( $time_slot_v ) . '@@' . $time_slot_v . "\r\n";
	}
	$time_slots_str .= 'Full Day@@00:00 - 24:00';

	$visual_form_structure[] = array(
		'type'              => 'selectbox',
		'name'              => 'rangetime',
		'obligatory'        => 'Off',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Time Slots', 'booking' ),
		'value'             => $time_slots_str,
	);
	// First Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'name',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active1' ) ) ? 'On' : get_bk_option( 'booking_form_field_active1' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required1' ) ) ? 'On' : get_bk_option( 'booking_form_field_required1' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label1' ) ) ? __( 'First Name', 'booking' ) : get_bk_option( 'booking_form_field_label1' ),
	);
	// Second Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'secondname',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active2' ) ) ? 'On' : get_bk_option( 'booking_form_field_active2' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required2' ) ) ? 'On' : get_bk_option( 'booking_form_field_required2' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label2' ) ) ? __( 'Second Name', 'booking' ) : get_bk_option( 'booking_form_field_label2' ),
	);
	// Email.
	$visual_form_structure[] = array(
		'type'       => 'email',
		'name'       => 'email',
		'obligatory' => 'On',
		'active'     => 'On',
		'required'   => 'On',
		'label'      => empty( get_bk_option( 'booking_form_field_label3' ) ) ? __( 'Email', 'booking' ) : get_bk_option( 'booking_form_field_label3' ),
	);
	// Visitors.
	$visual_form_structure[] = array(
		'type'       => 'selectbox',
		'name'       => 'visitors',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active6' ) ) ? 'On' : get_bk_option( 'booking_form_field_active6' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required6' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required6' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label6' ) ) ? __( 'Visitors', 'booking' ) : get_bk_option( 'booking_form_field_label6' ),
		'value'      => ( empty( get_bk_option( 'booking_form_field_values6' ) ) ? ( '1' . "\r\n" . '2' . "\r\n" . '3' . "\r\n" . '4' ) : get_bk_option( 'booking_form_field_values6' ) ),
	);
	// Phone.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'phone',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active4' ) ) ? 'On' : get_bk_option( 'booking_form_field_active4' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required4' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required4' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label4' ) ) ? __( 'Phone', 'booking' ) : get_bk_option( 'booking_form_field_label4' ),
	);
	// Details  -  textarea.
	$visual_form_structure[] = array(
		'type'       => 'textarea',
		'name'       => 'details',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active5' ) ) ? 'On' : get_bk_option( 'booking_form_field_active5' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required5' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required5' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label5' ) ) ? __( 'Details', 'booking' ) : get_bk_option( 'booking_form_field_label5' ),
	);
	// CAPTCHA.
	$visual_form_structure[] = array(
		'type'              => 'captcha',
		'name'              => 'captcha',
		'obligatory'        => 'Off',
		'active'            => ( 'On' === get_bk_option( 'booking_is_use_captcha' ) ) ? 'On' : 'Off',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => '',
	);
	// Submit.
	$visual_form_structure[] = array(
		'type'              => 'submit',
		'name'              => 'submit',
		'obligatory'        => 'On',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => empty( get_bk_option( 'booking_send_button_title' ) ) ? __( 'Send', 'booking' )
			: get_bk_option( 'booking_send_button_title' ),
	);

	return $visual_form_structure;
}

/**
 * Get  'Blank Visual Structure'    of booking form
 *
 * It can be imported from very old Free version,  if such options exist in plugin DB,  otherwise it is blank template
 *
 * @return array
 */
function wpbc_simple_form__visual__get_default_form__times_30min() {

	$visual_form_structure = array();

	// Calendar.
	$visual_form_structure[] = array(
		'type'              => 'calendar',
		'name'              => 'calendar',
		'obligatory'        => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Select date', 'booking' ),
	);
	// Time - Slots.
	$time_slots_arr     = array(
		'09:00 - 09:30',
		'09:30 - 10:00',
		'10:00 - 10:30',
		'10:30 - 11:00',
		'11:00 - 11:30',
		'11:30 - 12:00',
		'12:00 - 12:30',
		'12:30 - 13:00',
		'13:00 - 13:30',
		'13:30 - 14:00',
		'14:00 - 14:30',
		'14:30 - 15:00',
		'15:00 - 15:30',
		'15:30 - 16:00',
		'16:00 - 16:30',
		'16:30 - 17:00',
		'17:00 - 17:30',
		'17:30 - 18:00',
	);
	$formated_times_arr = array();
	foreach ( $time_slots_arr as $time_slot_v ) {
		$formated_times_arr[] = wpbc_time_slot_in_format( $time_slot_v ) . '@@' . $time_slot_v;    // . "\r\n";//.
	}
	$time_slots_str = implode( "\r\n", $formated_times_arr );
	// $time_slots_str .= 'Full Day@@00:00 - 24:00'; //.

	$visual_form_structure[] = array(
		'type'              => 'selectbox',
		'name'              => 'rangetime',
		'obligatory'        => 'Off',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Select Time', 'booking' ),
		'value'             => $time_slots_str,
	);

	// First Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'name',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active1' ) ) ? 'On' : get_bk_option( 'booking_form_field_active1' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required1' ) ) ? 'On' : get_bk_option( 'booking_form_field_required1' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label1' ) ) ? __( 'First Name', 'booking' ) : get_bk_option( 'booking_form_field_label1' ),
	);
	// Second Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'secondname',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active2' ) ) ? 'On' : get_bk_option( 'booking_form_field_active2' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required2' ) ) ? 'On' : get_bk_option( 'booking_form_field_required2' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label2' ) ) ? __( 'Second Name', 'booking' ) : get_bk_option( 'booking_form_field_label2' ),
	);
	// Email.
	$visual_form_structure[] = array(
		'type'       => 'email',
		'name'       => 'email',
		'obligatory' => 'On',
		'active'     => 'On',
		'required'   => 'On',
		'label'      => empty( get_bk_option( 'booking_form_field_label3' ) ) ? __( 'Email', 'booking' ) : get_bk_option( 'booking_form_field_label3' ),
	);
	// Visitors.
	$visual_form_structure[] = array(
		'type'       => 'selectbox',
		'name'       => 'visitors',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active6' ) ) ? 'On' : get_bk_option( 'booking_form_field_active6' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required6' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required6' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label6' ) ) ? __( 'Visitors', 'booking' ) : get_bk_option( 'booking_form_field_label6' ),
		'value'      => ( empty( get_bk_option( 'booking_form_field_values6' ) ) ? ( '1' . "\r\n" . '2' . "\r\n" . '3' . "\r\n" . '4' ) : get_bk_option( 'booking_form_field_values6' ) ),
	);
	// Phone.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'phone',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active4' ) ) ? 'On' : get_bk_option( 'booking_form_field_active4' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required4' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required4' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label4' ) ) ? __( 'Phone', 'booking' ) : get_bk_option( 'booking_form_field_label4' ),
	);
	// Details  -  textarea.
	$visual_form_structure[] = array(
		'type'       => 'textarea',
		'name'       => 'details',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active5' ) ) ? 'On' : get_bk_option( 'booking_form_field_active5' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required5' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required5' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label5' ) ) ? __( 'Details', 'booking' ) : get_bk_option( 'booking_form_field_label5' ),
	);
	// CAPTCHA.
	$visual_form_structure[] = array(
		'type'              => 'captcha',
		'name'              => 'captcha',
		'obligatory'        => 'Off',
		'active'            => ( 'On' === get_bk_option( 'booking_is_use_captcha' ) ) ? 'On' : 'Off',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => '',
	);
	// Submit.
	$visual_form_structure[] = array(
		'type'              => 'submit',
		'name'              => 'submit',
		'obligatory'        => 'On',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => empty( get_bk_option( 'booking_send_button_title' ) ) ? __( 'Send', 'booking' ) : get_bk_option( 'booking_send_button_title' ),
	);

	return $visual_form_structure;
}

/**
 * Get  'Blank Visual Structure'    of booking form
 *
 * It can be imported from very old Free version,  if such options exist in plugin DB,  otherwise it is blank template
 *
 * @return array
 */
function wpbc_simple_form__visual__get_default_form__without_times() {
	$visual_form_structure = array();
	// Calendar.
	$visual_form_structure[] = array(
		'type'              => 'calendar',
		'name'              => 'calendar',
		'obligatory'        => 'On',
		'if_exist_required' => 'On',
		'label'             => '',               //__('Select date', 'booking') //.
	);
	// First Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'name',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active1' ) ) ? 'On'
			: get_bk_option( 'booking_form_field_active1' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required1' ) ) ? 'On'
			: get_bk_option( 'booking_form_field_required1' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label1' ) ) ? __( 'First Name', 'booking' )
			: get_bk_option( 'booking_form_field_label1' ),
	);
	// Second Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'secondname',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active2' ) ) ? 'On' : get_bk_option( 'booking_form_field_active2' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required2' ) ) ? 'On' : get_bk_option( 'booking_form_field_required2' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label2' ) ) ? __( 'Second Name', 'booking' ) : get_bk_option( 'booking_form_field_label2' ),
	);
	// Email.
	$visual_form_structure[] = array(
		'type'       => 'email',
		'name'       => 'email',
		'obligatory' => 'On',
		'active'     => 'On',
		'required'   => 'On',
		'label'      => empty( get_bk_option( 'booking_form_field_label3' ) ) ? __( 'Email', 'booking' ) : get_bk_option( 'booking_form_field_label3' ),
	);
	// Visitors.
	$visual_form_structure[] = array(
		'type'       => 'selectbox',
		'name'       => 'visitors',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active6' ) ) ? 'On' : get_bk_option( 'booking_form_field_active6' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required6' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required6' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label6' ) ) ? __( 'Visitors', 'booking' ) : get_bk_option( 'booking_form_field_label6' ),
		'value'      => ( empty( get_bk_option( 'booking_form_field_values6' ) ) ? ( '1' . "\r\n" . '2' . "\r\n" . '3' . "\r\n" . '4' ) : get_bk_option( 'booking_form_field_values6' ) ),
	);
	// Phone.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'phone',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active4' ) ) ? 'On' : get_bk_option( 'booking_form_field_active4' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required4' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required4' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label4' ) ) ? __( 'Phone', 'booking' ) : get_bk_option( 'booking_form_field_label4' ),
	);
	// Details  -  textarea.
	$visual_form_structure[] = array(
		'type'       => 'textarea',
		'name'       => 'details',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active5' ) ) ? 'On' : get_bk_option( 'booking_form_field_active5' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required5' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required5' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label5' ) ) ? __( 'Details', 'booking' ) : get_bk_option( 'booking_form_field_label5' ),
	);
	// CAPTCHA.
	$visual_form_structure[] = array(
		'type'              => 'captcha',
		'name'              => 'captcha',
		'obligatory'        => 'Off',
		'active'            => ( 'On' === get_bk_option( 'booking_is_use_captcha' ) ) ? 'On' : 'Off',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => '',
	);
	// Submit.
	$visual_form_structure[] = array(
		'type'              => 'submit',
		'name'              => 'submit',
		'obligatory'        => 'On',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => empty( get_bk_option( 'booking_send_button_title' ) ) ? __( 'Send', 'booking' ) : get_bk_option( 'booking_send_button_title' ),
	);

	return $visual_form_structure;
}


/**
 * Get  'Visual Structure'    of booking form  for "Service duraton" (time) and Start time   -  Wizard form
 *
 * @return array
 */
function wpbc_simple_form__visual__get_default_form__service_duration_a() {

	$visual_form_structure = array();

	// Calendar.
	$visual_form_structure[] = array(
		'type'              => 'calendar',
		'name'              => 'calendar',
		'obligatory'        => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Select date', 'booking' ),
	);

	// Duration Time - Service Duration.
	$duration_time_slots_arr = array(
		'Service A (15 min)@@00:15',
		'Service B (20 min)@@00:20',
		'Service C (30 min)@@00:30',
		'Service D (1 hour)@@01:00',
		'Service E (2 hours)@@02:00',
	);
	$duration_time_slots_str = implode( "\r\n", $duration_time_slots_arr );
	$visual_form_structure[] = array(
		'type'              => 'selectbox',
		'name'              => 'durationtime',
		'obligatory'        => 'Off',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Select Service', 'booking' ),
		'value'             => $duration_time_slots_str,
	);
	// Start Time.
	$starttime_slots_arr = array(
		'10:00',
		'10:30',
		'11:00',
		'11:30',
		'12:00',
		'12:30',
		'13:00',
		'13:30',
		'14:00',
		'14:30',
		'15:00',
		'15:30',
		'16:00',
		'16:30',
		'17:00',
		'17:30',
		'18:00',
	);

	$formated_times_arr = array();
	foreach ( $starttime_slots_arr as $time_slot_v ) {
		$formated_times_arr[] = wpbc_time_in_format( $time_slot_v ) . '@@' . $time_slot_v;
	}
	$time_slots_str = implode( "\r\n", $formated_times_arr );

	$visual_form_structure[] = array(
		'type'              => 'selectbox',
		'name'              => 'starttime',
		'obligatory'        => 'Off',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => __( 'Select Start Time', 'booking' ),
		'value'             => $time_slots_str,
	);

	// First Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'name',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active1' ) ) ? 'On' : get_bk_option( 'booking_form_field_active1' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required1' ) ) ? 'On' : get_bk_option( 'booking_form_field_required1' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label1' ) ) ? __( 'First Name', 'booking' ) : get_bk_option( 'booking_form_field_label1' ),
	);
	// Second Name.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'secondname',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active2' ) ) ? 'On' : get_bk_option( 'booking_form_field_active2' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required2' ) ) ? 'On' : get_bk_option( 'booking_form_field_required2' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label2' ) ) ? __( 'Second Name', 'booking' ) : get_bk_option( 'booking_form_field_label2' ),
	);
	// Email.
	$visual_form_structure[] = array(
		'type'       => 'email',
		'name'       => 'email',
		'obligatory' => 'On',
		'active'     => 'On',
		'required'   => 'On',
		'label'      => empty( get_bk_option( 'booking_form_field_label3' ) ) ? __( 'Email', 'booking' ) : get_bk_option( 'booking_form_field_label3' ),
	);
	// Visitors.
	$visual_form_structure[] = array(
		'type'       => 'selectbox',
		'name'       => 'visitors',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active6' ) ) ? 'On' : get_bk_option( 'booking_form_field_active6' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required6' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required6' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label6' ) ) ? __( 'Visitors', 'booking' ) : get_bk_option( 'booking_form_field_label6' ),
		'value'      => ( empty( get_bk_option( 'booking_form_field_values6' ) ) ? ( '1' . "\r\n" . '2' . "\r\n" . '3' . "\r\n" . '4' ) : get_bk_option( 'booking_form_field_values6' ) ),
	);
	// Phone.
	$visual_form_structure[] = array(
		'type'       => 'text',
		'name'       => 'phone',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active4' ) ) ? 'On' : get_bk_option( 'booking_form_field_active4' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required4' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required4' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label4' ) ) ? __( 'Phone', 'booking' ) : get_bk_option( 'booking_form_field_label4' ),
	);
	// Details  -  textarea.
	$visual_form_structure[] = array(
		'type'       => 'textarea',
		'name'       => 'details',
		'obligatory' => 'Off',
		'active'     => empty( get_bk_option( 'booking_form_field_active5' ) ) ? 'On' : get_bk_option( 'booking_form_field_active5' ),
		'required'   => empty( get_bk_option( 'booking_form_field_required5' ) ) ? 'Off' : get_bk_option( 'booking_form_field_required5' ),
		'label'      => empty( get_bk_option( 'booking_form_field_label5' ) ) ? __( 'Details', 'booking' ) : get_bk_option( 'booking_form_field_label5' ),
	);
	// CAPTCHA.
	$visual_form_structure[] = array(
		'type'              => 'captcha',
		'name'              => 'captcha',
		'obligatory'        => 'Off',
		'active'            => ( 'On' === get_bk_option( 'booking_is_use_captcha' ) ) ? 'On' : 'Off',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => '',
	);
	// Submit.
	$visual_form_structure[] = array(
		'type'              => 'submit',
		'name'              => 'submit',
		'obligatory'        => 'On',
		'active'            => 'On',
		'required'          => 'On',
		'if_exist_required' => 'On',
		'label'             => empty( get_bk_option( 'booking_send_button_title' ) ) ? __( 'Send', 'booking' ) : get_bk_option( 'booking_send_button_title' ),
	);

	return $visual_form_structure;
}
