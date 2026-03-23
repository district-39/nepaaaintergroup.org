<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Versions Functions
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
// ==  Versions  ==
// =====================================================================================================================

/**
 * Check if this demo website
 *
 * @return bool
 */
function wpbc_is_this_demo() {

	 // return false;  //.

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		return false;        // If this is Booking Calendar Free version,  then it's not the demo.
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( ( isset( $_SERVER['SCRIPT_FILENAME'] ) ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'wpbookingcalendar.com' ) !== false ) ) || ( ( isset( $_SERVER['HTTP_HOST'] ) ) && ( strpos( $_SERVER['HTTP_HOST'], 'wpbookingcalendar.com' ) !== false ) ) ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Check if this Beta Demo website
 *
 * @return bool
 */
function wpbc_is_this_beta() {

	$is_beta = ( ( isset( $_SERVER['HTTP_HOST'] ) ) && ( 'beta' === $_SERVER['HTTP_HOST'] ) );

	return $is_beta;
}


/** Get Warning Text  for Demo websites */
function wpbc_get_warning_text_in_demo_mode() {
	// return '<div class="wpbc-error-message wpbc_demo_test_version_warning"><strong>Warning!</strong> Demo test version does not allow changes to these items.</div>'; //Old Style
	return '<div class="wpbc-settings-notice notice-warning"><strong>Warning!</strong> Demo test version does not allow changes to these items.</div>';
}


function wpbc_get_version_type__and_mu(){
	$version = 'free';
	if ( class_exists( 'wpdev_bk_personal' ) ) $version = 'personal';
	if ( class_exists( 'wpdev_bk_biz_s' ) )    $version = 'biz_s';
	if ( class_exists( 'wpdev_bk_biz_m' ) )    $version = 'biz_m';
	if ( class_exists( 'wpdev_bk_biz_l' ) )    $version = 'biz_l';
	if ( class_exists('wpdev_bk_multiuser') )  $version = 'multiuser';
	return $version;
}


function wpbc_get_plugin_version_type(){
	$version = 'free';
	if ( class_exists( 'wpdev_bk_personal' ) ) $version = 'personal';
	if ( class_exists( 'wpdev_bk_biz_s' ) )    $version = 'biz_s';
	if ( class_exists( 'wpdev_bk_biz_m' ) )    $version = 'biz_m';
	if ( class_exists( 'wpdev_bk_biz_l' ) )    $version = 'biz_l';
	return $version;
}


/**
 * Get Title of the version type.
 *
 * @return string
 */
function wpbc_get_plugin_version_title() {
	$title        = 'Free';
	$version_type = wpbc_get_version_type__and_mu();
	switch ( $version_type ) {
		case 'personal':
			$title = 'Personal';
			break;
		case 'biz_s':
			$title = 'Business Small';
			break;
		case 'biz_m':
			$title = 'Business Medium';
			break;
		case 'biz_l':
			$title = 'Business Large';
			break;
		case 'multiuser':
			$title = 'MultiUser';
			break;
		default:
			$title = 'Free';
	}

	return $title;
}


/**
 * Check if user accidentially update Booking Calendar Paid version to Free
 *
 * @return bool
 */
function wpbc_is_updated_paid_to_free() {

	if ( ( wpbc_is_table_exists('bookingtypes') ) && ( ! class_exists('wpdev_bk_personal') )  )
		return  true;
	else
		return false;
}


function wpbc_get_ver_sufix() {
	if ( strpos( strtolower( WPDEV_BK_VERSION ), 'multisite' ) !== false ) {
		$v_type = '-multi';
	} else if ( strpos( strtolower( WPDEV_BK_VERSION ), 'develop' ) !== false ) {
		$v_type = '-dev';
	} else {
		$v_type = '';
	}
	$v = '';
	if ( class_exists( 'wpdev_bk_personal' ) ) {
		$v = 'ps' . $v_type;
	}
	if ( class_exists( 'wpdev_bk_biz_s' ) ) {
		$v = 'bs' . $v_type;
	}
	if ( class_exists( 'wpdev_bk_biz_m' ) ) {
		$v = 'bm' . $v_type;
	}
	if ( class_exists( 'wpdev_bk_biz_l' ) ) {
		$v = 'bl' . $v_type;
	}
	if ( class_exists( 'wpdev_bk_multiuser' ) ) {
		$v = '';
	}

	return $v;
}

/**
 * Get Up link.
 *
 * @return string
 */
function wpbc_up_link() {

	if ( ! wpbc_is_this_demo() ) {
		$v = wpbc_get_ver_sufix();
	} else {
		$v = '';
	}

	if ( empty( $v ) ) {
		$v = 'features/';
	} else {
		$v = 'upgrade-' . $v . '/';
	}

	return 'https://wpbookingcalendar.com/' . $v;
}


/**
	 * Check  if "Booking Manager" installed/activated and return version number
 *
 * @return string - 0 if not installed,  otherwise version num
 */
function wpbc_get_wpbm_version() {

	if ( ! defined( 'WPBM_VERSION_NUM' ) ) {
		return 0;
	} else {
		return WPBM_VERSION_NUM;
	}
}


/**
 * Get header info from this file, just for compatibility with WordPress 2.8 and older versions
 *
 * @param $file					= WPBC_FILE
 * @param $default_headers		= array( 'Name' => 'Plugin Name', 'PluginURI' => 'Plugin URI', 'Version' => 'Version', 'Description' => 'Description', 'Author' => 'Author', 'AuthorURI' => 'Author URI', 'TextDomain' => 'Text Domain', 'DomainPath' => 'Domain Path' )
 * @param $context				= 'plugin'
 *
 * @return array
 *
 *  Example:
 *          $plugin_data = wpbc_file__read_header_info(  WPBC_FILE , array( 'Name' => 'Plugin Name', 'PluginURI' => 'Plugin URI', 'Version' => 'Version', 'Description' => 'Description', 'Author' => 'Author', 'AuthorURI' => 'Author URI', 'TextDomain' => 'Text Domain', 'DomainPath' => 'Domain Path' ) , 'plugin' );
 */
function wpbc_file__read_header_info( $file, $default_headers, $context = '' ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	$fp = fopen( $file, 'r' );		// We don't need to write to the file, so just open for reading.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
	$file_data = fread( $fp, 8192 );		// Pull only the first 8kiB of the file in.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	fclose( $fp );					// PHP will close file handle, but we are good citizens.

	if ( $context != '' ) {
		$extra_headers = array();			//apply_filters( "extra_$context".'_headers', array() );

		$extra_headers = array_flip( $extra_headers );
		foreach ( $extra_headers as $key => $value ) {
			$extra_headers[ $key ] = $key;
		}
		$all_headers = array_merge( $extra_headers, $default_headers );
	} else {
		$all_headers = $default_headers;
	}

	foreach ( $all_headers as $field => $regex ) {
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
		if ( !empty( ${$field} ) )
			${$field} =  trim(preg_replace("/\s*(?:\*\/|\?>).*/", '',  ${$field}[1] ));
		else
			${$field} = '';
	}

	$file_data = compact( array_keys( $all_headers ) );

	return $file_data;
}


function wpbc_get_json_property_from_meta( $property_key ) {

	if ( ! defined( 'WPBC_PRO_FILE' ) ) {
		return null;
	}

	$meta_file_path = plugin_dir_path( WPBC_PRO_FILE ) . 'meta.json';

	if ( ! file_exists( $meta_file_path ) ) {
		return null;
	}

	global $wp_filesystem;

	if ( ! function_exists( 'request_filesystem_credentials' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	if ( empty( $wp_filesystem ) ) {
		WP_Filesystem();
	}

	$json_contents = $wp_filesystem->get_contents( $meta_file_path );

	if ( empty( $json_contents ) ) {
		return null;
	}

	$data = json_decode( $json_contents, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return null;
	}

	return isset( $data[ $property_key ] ) ? $data[ $property_key ] : null;
}

/**
 * Check  if we need BLUR this section -- add CSS Class for specific versions
 *
 *  Example:   $is_blured = wpbc_is_blured( array( 'free', 'ps' ) ); 		// true in Free and  Personal versions.
 *
 * @param $versions_arr
 *
 * @return void
 */
function wpbc_is_blured( $versions_arr ){

	$ver = wpbc_get_version_type__and_mu();

	$is_blured = false;

	switch ( $ver ) {
		case 'free':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'f', $versions_arr ) ) ) ? true : $is_blured;
			break;
		case 'personal':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'ps', $versions_arr ) ) ) ? true : $is_blured;
			break;
		case 'biz_s':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'bs', $versions_arr ) ) ) ? true : $is_blured;
			break;
		case 'biz_m':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'bm', $versions_arr ) ) ) ? true : $is_blured;
			break;
		case 'biz_l':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'bl', $versions_arr ) ) ) ? true : $is_blured;
			break;
		case 'multiuser':
			$is_blured = ( ( in_array( $ver, $versions_arr ) ) || ( in_array( 'mu', $versions_arr ) ) ) ? true : $is_blured;
			break;
		default:
			// Default
	}
    return $is_blured;
}


/**
 * Echo Blur CSS Class for specific versions
 *
 *  Example: wpbc_echo_blur(array('free','ps')); 		// Echo  blur in Free and  Personal versions.
 *
 * @param $versions_arr
 *
 * @return void
 */
function wpbc_echo_blur( $versions_arr ){

	$is_blured = wpbc_is_blured( $versions_arr );

	if ( $is_blured ) {
		echo 'wpbc_blur';
	}
}


/**
 * Show Upgrade Widget
 *
 * @param $id
 * @param $params
 *
 * @return string		if upgrade panel hided than  returned ''
 *
 *
 * Example:
 *                     wpbc_get_up_notice('booking_weekdays_conditions', array(
 * 																						'feature_link' => array( 'title' => 'feature',        'relative_url' => 'overview/#capacity' ),
 * 																						'upgrade_link' => array( 'title' => 'Upgrade to Pro', 'relative_url' => 'features/#bk_news_section' ),
 * 																						'versions'     => 'Business Large, MultiUser versions',
 * 																						'css'          => 'transform: translate(0) translateY(120px);'
 *                                                                    ));
 */
function wpbc_get__upgrade_notice__html_content( $id, $params ){

	$defaults = array(
					'feature_link' => array( 'title' => 'feature', 		  'relative_url' => 'overview/#capacity' ),
					'upgrade_link' => array( 'title' => 'Upgrade to Pro', 'relative_url' => 'features/#bk_news_section' ),
					'versions' 	   => 'Business Large, MultiUser versions',
					'css'		   => 'transform: translate(0) translateY(120px);',
					'dismiss_css_class' => '',
					'html_dismiss_btn'  => ''
				);
	$params   = wp_parse_args( $params, $defaults );

	ob_start();

	?><div id="upgrade_notice_<?php echo esc_attr( $id ); ?>"
		   class="wpbc_widget_content wpbc_upgrade_widget <?php echo esc_attr( str_replace( array('.','#'), '', $params['dismiss_css_class'] ) ); ?>"
		   style="<?php echo esc_attr( $params['css'] ); ?>">
		<div class="ui_container    ui_container_toolbar		ui_container_small       wpbc_upgrade_widget_container">
			<div class="ui_group    ui_group__upgrade">
				<div class="wpbc_upgrade_note wpbc_upgrade_theme_green">
					<div>
					<?php
						echo wp_kses_post( sprintf( 'This %s is available in the %s. %s'
							, '<a target="_blank" href="https://wpbookingcalendar.com/' . $params['feature_link']['relative_url'] . '">' . $params['feature_link']['title'] . '</a>'
							, '<strong>' . $params['versions'] . '</strong>'
							, '<a target="_blank" href="https://wpbookingcalendar.com/' . $params['upgrade_link']['relative_url'] . '">' . $params['upgrade_link']['title'] . '</a>'
						) );
					?>
					</div>
					<?php
					// Dismiss button
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $params['html_dismiss_btn'];
					?>
				</div>
			</div>
		</div>
	</div><?php

	$html = ob_get_clean();

	return  $html ;
}


/**
 * Get for showing Upgrade Widget Content and CSS class if needed to  blur real  content.  If
 *
 * @param $params = array(
 *																	  'id'                 => $id . '_' . 'weekdays_conditions',
 *														  			  'dismiss_css_class'  => '.wpbc_dismiss_weekdays_conditions',
 *														  			  'blured_in_versions' => array( 'free', 'ps', 'bs', 'mu' ),
 *														  			  'feature_link'       => array( 'title' => 'feature', 'relative_url' => 'overview/#advanced-days-selection' ),
 *														  			  'upgrade_link'       => array( 'title' => 'Upgrade to Pro', 'relative_url' => 'features/#bk_news_section' ),
 *														  			  'versions'           => 'Business Medium / Large, MultiUser versions',
 *														  			  'css'                => 'transform: translate(0) translateY(120px);'
 * 					)
 *
 * @return array(
 *     				'content' 				=> $upgrade_panel_html,
 *     				'maybe_blur_css_class' 	=> $blur_css_class
 * 				)
 *
 * Example of usage:
 *
 *		  		$upgrade_content_arr = wpbc_get_upgrade_widget( array(
 *																	  'id'                 => $id . '_' . 'weekdays_conditions',
 *														  			  'dismiss_css_class'  => '.wpbc_dismiss_weekdays_conditions',
 *														  			  'blured_in_versions' => array( 'free', 'ps', 'bs', 'mu' ),
 *														  			  'feature_link'       => array( 'title' => 'feature', 'relative_url' => 'overview/#advanced-days-selection' ),
 *														  			  'upgrade_link'       => array( 'title' => 'Upgrade to Pro', 'relative_url' => 'features/#bk_news_section' ),
 *														  			  'versions'           => 'Business Medium / Large, MultiUser versions',
 *														  			  'css'                => 'transform: translate(0) translateY(120px);'
 *														   ) );
 *
 * 			echo $upgrade_content_arr['content'];
 *
 *			// ... In real  content ...
 *          <div class=" wpbc_dismiss_weekdays_conditions <?php echo esc_attr( $upgrade_content_arr['maybe_blur_css_class'] ); ?>">
 *              ...
 *			</div>
 */
function wpbc_get_upgrade_widget( $params ) {

	$defaults = array(
						'id' 				 => 'wpbc_random_' . round( microtime( true ) * 1000 ), 			//$id . '_' . 'weekdays_conditions',
						'blured_in_versions' => array( 'free', 'ps', 'bs', 'bm', 'bl', 'mu' ),
						'feature_link' 		 => array( 'title' => 'feature', 'relative_url' => 'overview/#capacity' ),
						'upgrade_link' 		 => array( 'title' => 'Upgrade to Pro', 'relative_url' => 'features/#bk_news_section' ),
						'versions'     		 => 'Business Large, MultiUser versions',
						'css'          		 => 'transform: translate(0) translateY(120px);',
						'dismiss_css_class'  => '',																//'.wpbc_random_' . round( microtime( true ) * 1000 ), //'.'.$id . '_' . 'weekdays_conditions'
						'no_dismiss'         => false
				);
	$params = wp_parse_args( $params, $defaults );
	$up_id = $params['id'];


	$is_blured = wpbc_is_blured( $params['blured_in_versions'] );

	$upgrade_panel_html = '';
	$blur_css_class     = '';

	if ( $is_blured ) {

		if ( $params['no_dismiss'] ) {
			$is_upgrade_panel_visible = true;
			$html_dismiss_btn         = '';
		} else {
			// ---------------------------------------------------------------------------------------------------------
			// Is dismissed ?
			// ---------------------------------------------------------------------------------------------------------
			ob_start();
			$is_upgrade_panel_visible = wpbc_is_dismissed( $up_id , array(
																		'title' => '<span aria-hidden="true" style="font-size: 28px;">&times;</span>',
																		'hint'  => __( 'Dismiss', 'booking' ),
																		'class' => 'wpbc_panel_get_started_dismiss',
																		'css'   => '',
																		'dismiss_css_class' => $params['dismiss_css_class']
																) );
			$html_dismiss_btn = ob_get_clean();
		}

		// ---------------------------------------------------------------------------------------------------------
		// Upgrade Widget
		// ---------------------------------------------------------------------------------------------------------
		if ( $is_upgrade_panel_visible ) {

			$upgrade_panel_html =  wpbc_get__upgrade_notice__html_content( $up_id, array(
														'feature_link' => $params['feature_link'],
														'upgrade_link' => $params['upgrade_link'],
														'versions'     => $params['versions'],
														'css'          => $params['css'],
														'dismiss_css_class' => $params['dismiss_css_class'],
														'html_dismiss_btn'=> $html_dismiss_btn
												) );
			$blur_css_class = 'wpbc_blur';
		} else {

			ob_start();

			?><script type="text/javascript">
				jQuery(document).ready(function(){
					setTimeout(function(){
						jQuery( '<?php echo esc_attr( $params['dismiss_css_class'] ); ?>' ).hide() ;
					}, 100);
				});
			</script><?php

			$upgrade_panel_html = ob_get_clean();
		}
	}

	$upgrade_content_arr = array(
					'content' 				=> $upgrade_panel_html,
					'maybe_blur_css_class' 	=> $blur_css_class
				);

	return $upgrade_content_arr;

}


/**
 * How old ago was installed plugin,  based on first booking
 *
 * @return int			if -1,  then  no bookings!
 */
function wpbc_how_old_in_days() {

	$how_old = wpbc_get_info__about_how_old();

	if ( ! empty( $how_old ) ) {
		return intval( $how_old['days'] );
	} else {
		// Unknown. Maybe no bookings
		return -1;
	}
}

/**
 * Get date info about first booking.
 * @return array|false
 */
function wpbc_get_info__about_how_old() {
	global $wpdb;

	$sql = "SELECT modification_date FROM  {$wpdb->prefix}booking as bk ORDER by booking_id  LIMIT 0,1";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$res = $wpdb->get_results( $sql );

	if ( ! empty( $res ) ) {

		$first_booking_date = wpbc_datetime_localized( gmdate( 'Y-m-d H:i:s', strtotime( $res[0]->modification_date ) ), 'Y-m-d H:i:s' );

		$dif_days = wpbc_get_difference_in_days( gmdate( 'Y-m-d 00:00:00', strtotime( 'now' ) ), gmdate( 'Y-m-d 00:00:00', strtotime( $res[0]->modification_date ) ) );

		return array(
			'date_ymd_his' => $res[0]->modification_date,
			'date_echo'    => $first_booking_date,
			'days'         => $dif_days
		);
	}

	return false;
}
