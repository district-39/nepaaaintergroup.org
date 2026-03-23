<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Check  News, Version
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-03
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// =====================================================================================================================
// ==  Check  News, Version  ==
// =====================================================================================================================

define ('OBC_CHECK_URL', 'https://wpbookingcalendar.com/');


function wpbc_old_ajax_check_bk_news( $sub_url = '' ){

	$v=array();
	if (class_exists('wpdev_bk_personal'))          $v[] = 'wpdev_bk_personal';
	if (class_exists('wpdev_bk_biz_s'))             $v[] = 'wpdev_bk_biz_s';
	if (class_exists('wpdev_bk_biz_m'))             $v[] = 'wpdev_bk_biz_m';
	if (class_exists('wpdev_bk_biz_l'))             $v[] = 'wpdev_bk_biz_l';
	if (class_exists('wpdev_bk_multiuser'))         $v[] = 'wpdev_bk_multiuser';

	$obc_settings = array();
	$ver = get_bk_option('bk_version_data');
	if ( $ver !== false ) { $obc_settings = array( 'subscription_key'=>wp_json_encode($ver) ); }

	$params = array(
				'action' => 'get_news',
				'subscription_email' => isset($obc_settings['subscription_email'])?$obc_settings['subscription_email']:false,
				'subscription_key'   => isset($obc_settings['subscription_key'])?$obc_settings['subscription_key']:false,
				'bk' => array('bk_ver'=>WPDEV_BK_VERSION, 'bk_url'=>WPBC_PLUGIN_URL,'bk_dir'=>WPBC_PLUGIN_DIR, 'bk_clss'=>$v),
				'siteurl'            => get_option('siteurl'),
				'siteip' 			 => wpbc_get_server_ip(),													// FixIn: 9.8.14.3.
				'admin_email'        => get_option('admin_email')
	);

	$request = new WP_Http();
	if (empty($sub_url)) $sub_url = 'info/';
	$result  = $request->request( OBC_CHECK_URL . $sub_url, array(
		'method' => 'POST',
		'timeout' => 15,
		'body' => $params
		));

	if (!is_wp_error($result) && ($result['response']['code']=='200') && (true) ) {

	   $string = ($result['body']);                                         //$string = str_replace( "'", '&#039;', $string );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $string;
	   echo ' <script type="text/javascript"> ';
	   echo '    jQuery("#ajax_bk_respond").after( jQuery("#ajax_bk_respond #bk_news_loaded") );';
	   echo '    jQuery("#bk_news_loaded").slideUp(1).slideDown(1500);';
	   echo ' </script> ';

	} else  /**/
		{ // Some error appear
		echo '<div id="bk_errror_loading">';
			if ( is_wp_error( $result ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $result->get_error_message();
			} else {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $result['response']['message'];
			}
		echo '</div>';
		echo ' <script type="text/javascript"> ';
		echo '    document.getElementById("bk_news").style.display="none";';
		echo '    jQuery("#ajax_bk_respond").after( jQuery("#ajax_bk_respond #bk_errror_loading") );';
		echo '    jQuery("#bk_errror_loading").slideUp(1).slideDown(1500);';
		echo '    jQuery("#bk_news_section").animate({opacity:1},3000).slideUp(1500);';
		echo ' </script> ';
	}

}


/**
 * Check  if user defined to  not show up_news section.
 *
 */
function wpbc_is_show_up_news(){

	$is_show = get_bk_option( 'booking_wpdev_copyright_adminpanel' );

	if ( ( 'Off' === $is_show ) && ( ! wpbc_is_this_demo() ) && ( class_exists( 'wpdev_bk_personal' ) ) ) {
		return false;
	} else {
		return true;
	}
}


function wpbc_old_ajax_check_bk_version(){
	$v=array();
	if (class_exists('wpdev_bk_personal'))            $v[] = 'wpdev_bk_personal';
	if (class_exists('wpdev_bk_biz_s'))        $v[] = 'wpdev_bk_biz_s';
	if (class_exists('wpdev_bk_biz_m'))   $v[] = 'wpdev_bk_biz_m';
	if (class_exists('wpdev_bk_biz_l'))          $v[] = 'wpdev_bk_biz_l';
	if (class_exists('wpdev_bk_multiuser'))      $v[] = 'wpdev_bk_multiuser';

	$obc_settings = array();
	$params = array(
		'action'       => 'set_register',
		'order_number' => isset( $_POST['order_num'] ) ? sanitize_text_field( wp_unslash( $_POST['order_num'] ) ) : false,  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		'bk'           => array( 'bk_ver'  => WPDEV_BK_VERSION,
								 'bk_url'  => WPBC_PLUGIN_URL,
								 'bk_dir'  => WPBC_PLUGIN_DIR,
								 'bk_clss' => $v,
		),
		'siteurl'      => get_option( 'siteurl' ),
		'siteip'       => wpbc_get_server_ip(),                // FixIn: 9.8.14.3.
		'admin_email'  => get_option( 'admin_email' ),
	);

	update_bk_option( 'bk_version_data', serialize( $params ) );

	$request = new WP_Http();
	$result  = $request->request( OBC_CHECK_URL . 'register/', array(
		'method' => 'POST',
		'timeout' => 15,
		'body' => $params
		));

	if ( ! is_wp_error($result)
		&& ( $result['response']['code']=='200' )
		&& ( true ) ) {

	   $string = ($result['body']);                                         //$string = str_replace( "'", '&#039;', $string );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $string;
	   echo ' <script type="text/javascript"> ';
	   echo '    jQuery("#ajax_message").append( jQuery("#ajax_respond #bk_registration_info") );';
	   echo '    jQuery("#ajax_message").append( "<div id=\'bk_registration_info_reload\'>If page will not reload automatically,  please refresh page after 60 seconds...</div>" );';
	   echo ' </script> ';

	} else { // Some error appear
		echo '<div id="bk_errror_loading" class="warning_message" >';
		echo '<div class="info_message">'; esc_html_e('Warning! Some error occur, during sending registration request.' ,'booking'); echo '</div>';

		if ( is_wp_error( $result ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $result->get_error_message();
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $result['response']['message'];
		}
		echo '<br /><br />';
		esc_html_e('Please refresh this page and if the same error appear again contact support by email (with  info about order number and website) for finishing the registrations' ,'booking'); echo ' <a href="mailto:activate@wpbookingcalendar.com">activate@wpbookingcalendar.com</a>';
		echo '</strong></div>';
		echo ' <script type="text/javascript"> ';
		echo '    jQuery( "#ajax_message" ).html( "" );';

		echo '    jQuery("#ajax_message").append( jQuery("#ajax_respond #bk_errror_loading") );';
		echo '    jQuery("#bk_errror_loading").slideUp(1).slideDown(1500);';

		echo '    jQuery("#recheck_version").animate({opacity:1},3000).slideUp(1500);';
		echo ' </script> ';
	}
}