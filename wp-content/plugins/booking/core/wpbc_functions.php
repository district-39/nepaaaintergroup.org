<?php 
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Support Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

	/**
	 * Get Logo for conversation.
	 *
	 * @param string $svg_color - main color.
	 * @param string $svg_color_alt - alt color.
	 *
	 * @return void
	 */
	function wpbc_get_svg_logo( $svg_color = '#000', $svg_color_alt = '#fff', $opacity = '0.1' ) {

//		$svg_icon_integarted = '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="150 150 850 850">'
//								. '<path style="fill:' . $svg_color . '" d="M224.3 117.81c0-7.62 6.18-13.8 13.8-13.8h104.13c7.62 0 13.8 6.18 13.8 13.8v239.06c-58.48 32.03-105.14 82.96-131.74 144.58V117.81z"/>'
//								. '<path style="fill:' . $svg_color . ';" d="M224.3 620.3c0-152.27 123.43-275.7 275.7-275.7 69.48 0 132.95 25.7 181.43 68.11l-93.55 93.55c-24.31-18.76-54.79-29.92-87.88-29.92-79.51 0-143.96 64.45-143.96 143.96S420.49 764.26 500 764.26c33.09 0 63.57-11.16 87.88-29.92l93.55 93.55C632.95 870.3 569.48 896 500 896c-152.27 0-275.7-123.44-275.7-275.7z"/>'
//								. '<path style="fill:' . $svg_color_alt . '" fill-opacity="' . $opacity . '" d="m681.43 827.89-93.55-93.55c34.11-26.33 56.08-67.62 56.08-114.04s-21.97-87.71-56.08-114.04l93.55-93.55c57.78 50.54 94.27 124.8 94.27 207.59s-36.49 157.05-94.27 207.59z"/>'
//							. '</svg>';
//
//
//		$svg_icon_integarted = '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1" viewBox="0 0 600 600">
//								  <path id="O_Letter" d="M298.22 197.55c-92.32 0-167.15 77.82-167.15 173.81s74.84 173.82 167.15 173.82 167.15-77.82 167.15-173.82-74.84-173.81-167.15-173.81Zm0 264.53c-50.1 0-90.71-40.61-90.71-90.71s40.61-90.71 90.71-90.71 90.71 40.61 90.71 90.71-40.61 90.71-90.71 90.71Z" style="fill:' . $svg_color_alt . ';" fill-opacity="' . $opacity . '" />
//								  <path id="C_Letter" d="M363.75 434.1c-14.85 15.51-35.16 25.76-57.84 27.66-54.87 4.6-101.64-41.14-98.23-96.1 2.94-47.44 42.35-85.01 90.54-85.01 24.84 0 47.34 9.98 63.72 26.16 5.86 5.78 15.29 5.73 21.11-.09l35.14-35.14c5.69-5.69 5.9-14.87.43-20.77-30.41-32.83-73.1-53.26-120.4-53.26-92.32 0-167.15 77.82-167.15 173.81s74.84 173.82 167.15 173.82c48.49 0 92.15-21.47 122.68-55.76 5.29-5.94 5.02-14.97-.6-20.59l-35.05-35.05c-5.98-5.98-15.67-5.79-21.51.32Z" style="fill:' . $svg_color . ';"/>
//								  <path d="M130.69 357.28v2.59c0 .27.06.53.17.76a123.1 123.1 0 0 1-.17-3.35Zm4.51-50.82c12.34-39.07 38.32-70.02 77.79-97.04.92-.63 1.48-1.67 1.48-2.79V59.95c0-4.28-3.68-7.76-8.22-7.76h-3.76c-18.56 0-73.17 40.3-71.79 60.62l-.17 192.91c0 2.74 3.86 3.34 4.68.73Z"  style="fill:' . $svg_color . ';"/>
//								</svg>';
//
//		$svg_icon_integarted = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600">
//								  <path d="M311.92 214.22c-92.32 0-167.15 77.82-167.15 173.81s74.84 173.82 167.15 173.82 167.15-77.82 167.15-173.82-74.84-173.81-167.15-173.81Zm0 264.53c-50.1 0-90.71-40.61-90.71-90.71s40.61-90.71 90.71-90.71 90.71 40.61 90.71 90.71-40.61 90.71-90.71 90.71Z" style="fill:' . $svg_color_alt . ';" fill-opacity="' . $opacity . '" />
//								  <path d="M377.45 450.77c-14.85 15.51-35.16 25.76-57.84 27.66-54.87 4.6-101.64-41.14-98.23-96.1 2.94-47.44 42.35-85.01 90.54-85.01 24.84 0 47.34 9.98 63.72 26.16 5.86 5.78 15.29 5.73 21.11-.09l35.14-35.14c5.69-5.69 5.9-14.87.43-20.77-30.41-32.83-73.1-53.26-120.4-53.26-92.32 0-167.15 77.82-167.15 173.81s74.84 173.82 167.15 173.82c48.49 0 92.15-21.47 122.68-55.76 5.29-5.94 5.02-14.97-.6-20.59l-35.05-35.05c-5.98-5.98-15.67-5.79-21.51.32Z"  style="fill:' . $svg_color . ';"/>
//								  <path d="M149.45 318.12c12.34-39.07 38.32-70.02 77.79-97.04.92-.63 1.48-1.67 1.48-2.79V71.62c0-4.28-3.68-7.76-8.22-7.76h-3.76c-18.56 0-73.17 40.3-71.79 60.62l-.17 192.91c0 2.74 3.86 3.34 4.68.73Z"  style="fill:' . $svg_color . ';"/>
//								</svg>';
//
//		$svg_icon_integarted = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 550 550">
//								  <path d="M275 176.36c-92.32 0-167.15 77.82-167.15 173.81S182.69 523.99 275 523.99s167.15-77.82 167.15-173.82S367.31 176.36 275 176.36Zm0 264.53c-50.1 0-90.71-40.61-90.71-90.71s40.61-90.71 90.71-90.71 90.71 40.61 90.71 90.71-40.61 90.71-90.71 90.71Z" style="fill:#ffd25b"/>
//								  <path d="M340.53 412.91c-14.85 15.51-35.16 25.76-57.84 27.66-54.87 4.6-101.64-41.14-98.23-96.1 2.94-47.44 42.35-85.01 90.54-85.01 24.84 0 47.34 9.98 63.72 26.16 5.86 5.78 15.29 5.73 21.11-.09l35.14-35.14c5.69-5.69 5.9-14.87.43-20.77-30.41-32.83-73.1-53.26-120.4-53.26-92.32 0-167.15 77.82-167.15 173.81S182.69 523.99 275 523.99c48.49 0 92.15-21.47 122.68-55.76 5.29-5.94 5.02-14.97-.6-20.59l-35.05-35.05c-5.98-5.98-15.67-5.79-21.51.32Z" style="fill:#193db2"/>
//								  <path d="M112.53 280.27c12.34-39.07 38.32-70.02 77.79-97.04.92-.63 1.48-1.67 1.48-2.79V33.76c0-4.28-3.68-7.76-8.22-7.76h-3.76c-18.56 0-73.17 40.3-71.79 60.62l-.17 192.91c0 2.74 3.86 3.34 4.68.73Z" style="fill:#451fff"/>
//								</svg>';

		// FixIn: 10.11.3.3.
		$svg_icon_integarted = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 550 550">'
								. '<path d="M275 162.36c-92.32 0-167.15 77.82-167.15 173.81S182.69 509.99 275 509.99s167.15-77.82 167.15-173.82S367.31 162.36 275 162.36Zm0 264.53c-50.1 0-90.71-40.61-90.71-90.71s40.61-90.71 90.71-90.71 90.71 40.61 90.71 90.71-40.61 90.71-90.71 90.71Z" style="fill:' . $svg_color_alt . ';" fill-opacity="' . $opacity . '" />'
								. '<path d="M340.53 398.91c-14.85 15.51-35.16 25.76-57.84 27.66-54.87 4.6-101.64-41.14-98.23-96.1 2.94-47.44 42.35-85.01 90.54-85.01 24.84 0 47.34 9.98 63.72 26.16 5.86 5.78 15.29 5.73 21.11-.09l35.14-35.14c5.69-5.69 5.9-14.87.43-20.77-30.41-32.83-73.1-53.26-120.4-53.26-92.32 0-167.15 77.82-167.15 173.81S182.69 509.99 275 509.99c48.49 0 92.15-21.47 122.68-55.76 5.29-5.94 5.02-14.97-.6-20.59l-35.05-35.05c-5.98-5.98-15.67-5.79-21.51.32Z"  style="fill:' . $svg_color . ';"/>'
								. '<path d="M112.53 261.27c12.34-39.07 38.32-70.02 77.79-97.04.92-.63 1.48-1.67 1.48-2.79V14.76c0-4.28-3.68-7.76-8.22-7.76h-3.76c-18.56 0-73.17 40.3-71.79 60.62l-.17 192.91c0 2.74 3.86 3.34 4.68.73Z"  style="fill:' . $svg_color . ';"/>'
							. '</svg>';


		return $svg_icon_integarted;
	}


	/**
	 * Get Logo for Background Image.
	 *
	 * @param string $svg_color - main color.
	 * @param string $svg_color_alt - alt color.
	 *
	 * @return void
	 */
	function wpbc_get_svg_logo_for_background( $svg_color = '#000', $svg_color_alt = '#fff', $opacity = '0.2' ) {

		$svg_icon_integarted = wpbc_get_svg_logo( $svg_color, $svg_color_alt, $opacity  );

		$svg_icon_integarted = sprintf( 'data:image/svg+xml;base64,%s', base64_encode( $svg_icon_integarted ) );

		return $svg_icon_integarted;
	}

/**
 * Get Logo as HTML element.
 *
 * @param array $params - logo parameters.
 *
 * @return string
 */
function wpbc_get_svg_logo_for_html( $params = array() ) {

	// FixIn: 10.12.1.6.
	$defaults = array(
		'svg_color'     => '#ccc',
		'svg_color_alt' => '#aaa',
		'opacity'       => '0.4',
		'style_default' => 'background-repeat: no-repeat; background-position: center; display: inline-block; vertical-align: top;',
		'style_adjust'  => 'background-size: 18px auto; width: 20px; height: 20px; margin-top: 6px;',                   // This parameters, the adjust size of the logo and position.
		'css_class'     => '',
	);

	$params = wp_parse_args( $params, $defaults );

	$svg_logo_for_background = wpbc_get_svg_logo_for_background( $params['svg_color'], $params['svg_color_alt'], $params['opacity'] );

	$svg_icon_integarted  = '<i';
	$svg_icon_integarted .= ' class="wpbc_svg_logo ' . esc_attr( $params['css_class'] ) . '"';
	$svg_icon_integarted .= ' style="' . esc_attr( $params['style_default'] ) . esc_attr( $params['style_adjust'] );
	$svg_icon_integarted .= 'background-image: url(\'' . esc_attr( $svg_logo_for_background ) . '\') !important;"';
	$svg_icon_integarted .= '></i>';

	return $svg_icon_integarted;
}

	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Get html  preview of shortcode for Edit pages  ==  "  >

		/**
		 * Get html  preview of shortcode for Edit pages in Elementor and at Block editors
		 *
		 * @param $shortcode_type
		 * @param $attr
		 *
		 * @return string
		 */
		function wpbc_get_preview_for_shortcode( $shortcode_type, $attr ) {

			// FixIn: 9.9.0.39.

			return '<div style="border:2px dashed #ccc;text-align: center; padding:10px;display:flex;flex-flow:column wrap;justify-content: center;align-content: center;">
						<div>WP Booking Calendar Shortcode</div>
						<code>['.$shortcode_type.' ...]</code>
						<div style="font-size:0.8em;">This is not a real preview. Publish the page to see it in action.</div>
					</div>';
		}
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Calendar functions  ==  "  >
		
		/**
		 * Get maximum visible days in calendar
		 *
		 * @return int
		 */
		function wpbc_get_max_visible_days_in_calendar(){
		
			// Number of months to scroll
			$max_visible_days_in_calendar = get_bk_option( 'booking_max_monthes_in_calendar');
		
			if ( false !== strpos( $max_visible_days_in_calendar, 'm' ) ) {
				//$max_visible_days_in_calendar = intval( str_replace( 'm', '', $max_visible_days_in_calendar ) ) * 31 ;                    // FixIn: 9.6.1.1.
				// FixIn: 10.0.0.26.
				$diff_days = strtotime( '+' . intval( str_replace( 'm', '', $max_visible_days_in_calendar ) ) . ' months', strtotime( 'now' ) ) - strtotime( 'now' );
				$max_visible_days_in_calendar = round( $diff_days / ( 60 * 60 * 24 ) ) + 1;
			} else {
				$max_visible_days_in_calendar = intval( str_replace( 'y', '', $max_visible_days_in_calendar ) ) * 365 + 15;                  // FixIn: 9.6.1.1.
			}
		
			return $max_visible_days_in_calendar;
		}


		/**
		 * Parse {calendar ....}  option parameter  in Calendar | Booking form   shortcode
		 *
		 * @param $bk_options
		 *
		 * @return array|false
		 */
		function wpbc_parse_calendar_options( $bk_options ) {

			if ( empty( $bk_options ) ) {
				return false;
			}
				/* $matches    structure:
				 * Array
					(
						[0] => Array
							(
								[0] => {calendar months="6" months_num_in_row="2" width="341px" cell_height="48px"},
								[1] => calendar
								[2] => months="6" months_num_in_row="2" width="341px" cell_height="48px"
							)

						[1] => Array
							(
								[0] => {select-day condition="weekday" for="5" value="3"},
								[1] => select-day
								[2] => condition="weekday" for="5" value="3"
							)
						 .....
					)
				 */

			$pattern_to_search='%\s*{([^\s]+)\s*([^}]+)\s*}\s*[,]?\s*%';

			preg_match_all( $pattern_to_search, $bk_options, $matches, PREG_SET_ORDER );

			foreach ( $matches as $value ) {
				if ( $value[1] == 'calendar' ) {
					$paramas = $value[2];
					$paramas = trim( $paramas );
					$paramas = explode( ' ', $paramas );
					$options = array();
					foreach ( $paramas as $vv ) {
						if ( ! empty( $vv ) ) {
							$vv = trim( $vv );
							$vv = explode( '=', $vv );
							if ( ( isset( $vv[0] ) ) && ( isset( $vv[1] ) ) ) {
								$options[ $vv[0] ] = trim( $vv[1] );
							}
						}
					}
					if ( count( $options ) == 0 ) {
						return false;
					} else {
						return $options;
					}
				}
			}

			return false;			// We are not have the "calendar" options in the shortcode
		}

		

		/**
		 * Parse {calendar ....}  option parameter  in Calendar | Booking form   shortcode
		 *
		 * @param $bk_options
		 *
		 * @return array|false
		 */
		function wpbc_parse_calendar_options__aggregate_param( $bk_options ) {                    						// FixIn: 9.8.15.10.

			if ( empty( $bk_options ) ) {
				return false;
			}
				/* $matches    structure:
					$matches = [
									0 = [
											  0 = "{aggregate type=bookings_only}"
											  1 = "aggregate"
											  2 = "type=bookings_only"
										]
								 ]
				 */

			$pattern_to_search='%\s*{([^\s]+)\s*([^}]+)\s*}\s*[,]?\s*%';

			preg_match_all( $pattern_to_search, $bk_options, $matches, PREG_SET_ORDER );

			foreach ( $matches as $value ) {
				if ( $value[1] == 'aggregate' ) {
					$paramas = $value[2];
					$paramas = trim( $paramas );
					$paramas = explode( ' ', $paramas );
					$options = array();
					foreach ( $paramas as $vv ) {
						if ( ! empty( $vv ) ) {
							$vv = trim( $vv );
							$vv = explode( '=', $vv );
							if ( ( isset( $vv[0] ) ) && ( isset( $vv[1] ) ) ) {
								$options[ $vv[0] ] = trim( $vv[1] );
							}
						}
					}
					if ( count( $options ) == 0 ) {
						return false;
					} else {
						return $options;
					}
				}
			}

			return false;			// We are not have the "calendar" options in the shortcode
		}


		function wpbc_is_calendar_skin_legacy( $skin_name ){
			$legacy_skins = array(
					'black.css',
					'multidays.css',
					'premium-black.css',
					'premium-light-noborder.css',
					'premium-light.css',
					'premium-marine.css',
					'premium-steel-noborder.css',
					'premium-steel.css',
					'standard.css',
					'traditional-light.css',
					'traditional.css'
					// FixIn: 10.7.1.5.
					, 'traditional-times.css'
					, 'black-2.css'
					, 'green-01.css'
					, 'light-01.css'
			);

			return in_array( $skin_name, $legacy_skins );
		}


		/**
		 * Get Legacy calendar skin
		 *
		 * @param $skin_name		=	'Black-2'
		 * @param $skin_arr			=   ["24_9__dark_1.css", "/css/skins/blac...", "Black-2"]
		 *
		 * @return mixed
		 */
		function wpbc_get_legacy_calendar_skin_name($skin_name, $skin_arr){

			if (wpbc_is_calendar_skin_legacy($skin_arr[0]) ){
				return __( 'Legacy', 'booking' ) . ': ' . $skin_name;
			} else {
			return $skin_name;
			}
		}

		/**
		 * Get Calendar  skin  options for selectboxes
		 * @return void
		 */
		function wpbc_get_calendar_skin_options( $url_prefix = '' ){
			//  Calendar Skin  /////////////////////////////////////////////////////
			$calendar_skins_options        = array();
			$legacy_calendar_skins_options = array();
			// Skins in the Custom User folder (need to create it manually):    http://example.com/wp-content/uploads/wpbc_skins/ ( This folder do not owerwrited during update of plugin )
			$upload_dir = wp_upload_dir();
			// FixIn: 8.9.4.8.
			$files_in_folder = wpbc_dir_list( array(  WPBC_PLUGIN_DIR . '/css/skins/'  ) );  // Folders where to look about calendar skins
			foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';

				//FixIn: 8.9.4.8    // FixIn: 9.1.2.10.
				$skin_file[1] = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL , $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for calendar skin

				if ( ! wpbc_is_calendar_skin_legacy( $skin_file[0] ) ) {
					$calendar_skins_options[ $skin_file[1] ] =  $skin_file[2];
				} else {
					$legacy_calendar_skins_options[ $skin_file[1] ] = wpbc_get_legacy_calendar_skin_name( $skin_file[2], $skin_file );
				}
			}

			$calendar_skins_options_real   = array();
			$calendar_skins_options_real[] =  array( 'optgroup' => true, 'close'  => false, 'title'  => '&nbsp;' . __('Calendar Skins' ,'booking')  );
			foreach ( $calendar_skins_options as $s_key => $s_val ) {
				$calendar_skins_options_real [ $url_prefix . $s_key ] = $s_val;
			}
			$calendar_skins_options_real[] =  array( 'optgroup' => true, 'close'  => true );
			$calendar_skins_options_real[] = array( 'optgroup' => true, 'close'  => false, 'title'  => '&nbsp;' . __('Legacy Calendar Skins' ,'booking')  );
			foreach ( $legacy_calendar_skins_options as $s_key => $s_val ) {
				$calendar_skins_options_real [ $url_prefix . $s_key ] = array( 'title' => $s_val, 'attr'  => array( 'style' => 'color:#ccc;' ) );
			}
			$calendar_skins_options_real[] = array( 'optgroup' => true, 'close'  => true );
			$calendar_skins_options_real[] = array( 'optgroup' => true, 'close'  => false, 'title'  => '&nbsp;' . __('Custom Calendar Skins' ,'booking')  );
				$files_in_folder = wpbc_dir_list( array(  $upload_dir['basedir'].'/wpbc_skins/' ) );  // Folders where to look about calendar skins
				foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';

					//FixIn: 8.9.4.8    // FixIn: 9.1.2.10.
					$skin_file[1] = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL , $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for calendar skin

					$calendar_skins_options_real[ $url_prefix . $skin_file[1] ] = $skin_file[2];

				}

			$calendar_skins_options_real[] = array( 'optgroup' => true, 'close'  => true );

		    return $calendar_skins_options_real;
		}


		function wpbc_get_calendar_skin_options_with_legacy_sections(){

			// FixIn: 10.3.0.5.
			//  Calendar Skin  /////////////////////////////////////////////////////
			$cal_arr = wpbc_get_calendar_skin_options( WPBC_PLUGIN_URL );

			$upload_dir              = wp_upload_dir();                            // Check  if this skin exist  in the Custom User folder at  the http://example.com/wp-content/uploads/wpbc_skins/
			$custom_user_skin_folder = $upload_dir['basedir'];
			$custom_user_skin_url    = $upload_dir['baseurl'];

			$transformed_cal_arr = array();
			foreach ( $cal_arr as $calendar_skin_url => $calendar_name ) {
				if ( false !== strpos( $calendar_skin_url, WPBC_PLUGIN_URL ) ) {

					$relative_cal_skin_path = str_replace( WPBC_PLUGIN_URL, '', $calendar_skin_url );

					if ( file_exists( $custom_user_skin_folder . $relative_cal_skin_path ) ) {
						// Custom  Skin
						$transformed_cal_arr[ $custom_user_skin_url . $relative_cal_skin_path ] = $calendar_name;
					} else {
						// Plugin Usual Skins
						$transformed_cal_arr[ WPBC_PLUGIN_URL . $relative_cal_skin_path ] = $calendar_name;
					}
				} else {
					// OptGroups
					$transformed_cal_arr[ $calendar_skin_url ] = $calendar_name;
				}
			}

			return $transformed_cal_arr;
		}
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Files functions  ==  "  >

		// Get array of images - icons inside of this directory
		function wpbc_dir_list ($directories) {

			// create an array to hold directory list
			$results = array();

			if (is_string($directories)) $directories = array($directories);
			foreach ($directories as $dir) {
				if ( is_dir( $dir ) ) {
					$directory = $dir;
				} else {
					$directory = WPBC_PLUGIN_DIR . $dir;
				}

				if ( file_exists( $directory ) ) {                                  //FixIn: 5.4.5
					// create a handler for the directory
					$handler = @opendir($directory);
					if ($handler !== false) {
						// keep going until all files in directory have been read
						while ($file = readdir($handler)) {

							// if $file isn't this directory or its parent,
							// add it to the results array
							if ($file != '.' && $file != '..' && ( strpos($file, '.css' ) !== false ) )
								$results[] = array($file, /* WPBC_PLUGIN_URL .*/ $dir . $file,  ucfirst(strtolower( str_replace('.css', '', $file))) );
						}

						// tidy up: close the handler
						closedir($handler);
					}
				}
			}
			// done!
			return $results;
		}


		/**
		 * Check  if such file exist or not.
		 *
		 * @param string $path - relative path to  file (relative to plugin folder).
		 * @return boolean true | false
		 */
		function wpbc_is_file_exist( $path ) {

			if (  file_exists( trailingslashit( WPBC_PLUGIN_DIR ) . ltrim( $path, '/\\' ) )  )  // check if this file exist
				return true;
			else
				return false;
		}


		/**
		 * Count the number of bytes of a given string.
		 * Input string is expected to be ASCII or UTF-8 encoded.
		 * Warning: the function doesn't return the number of chars
		 * in the string, but the number of bytes.
		 * See http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
		 * for information on UTF-8.
		 *
		 * @param string $str The string to compute number of bytes
		 *
		 * @return The length in bytes of the given string.
		 */
		function wpbc_get_bytes_from_str( $str ) {
			// STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
			// Number of characters in string
			$strlen_var = strlen( $str );

			$d = 0;			// string bytes counter

			// Iterate over every character in the string, escaping with a slash or encoding to UTF-8 where necessary
			for ( $c = 0; $c < $strlen_var; ++ $c ) {
				$ord_var_c = ord( $str[$c] );        // FixIn: 2.0.17.1.
				switch ( true ) {
					case(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):		// characters U-00000000 - U-0000007F (same as ASCII)
						$d ++;
						break;
					case(($ord_var_c & 0xE0) == 0xC0):						// characters U-00000080 - U-000007FF, mask 110XXXXX
						$d += 2;
						break;
					case(($ord_var_c & 0xF0) == 0xE0):						// characters U-00000800 - U-0000FFFF, mask 1110XXXX
						$d += 3;
						break;
					case(($ord_var_c & 0xF8) == 0xF0):						// characters U-00010000 - U-001FFFFF, mask 11110XXX
						$d += 4;
						break;
					case(($ord_var_c & 0xFC) == 0xF8):						// characters U-00200000 - U-03FFFFFF, mask 111110XX
						$d += 5;
						break;
					case(($ord_var_c & 0xFE) == 0xFC):						// characters U-04000000 - U-7FFFFFFF, mask 1111110X
						$d += 6;
						break;
					default:
						$d ++;
				}
			}
			return $d;
		}


	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Can I Load  JavaScript Files  ==  "  >

		function wpbc_can_i_load_on__edit_new_post_page() {

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return false;
			}


			$pages_where_load = array( 'post-new.php', 'page-new.php', 'post.php', 'page.php', 'widgets.php', 'customize.php' );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( ( in_array( basename( $_SERVER['PHP_SELF'] ), $pages_where_load ) )
				 || ( wpbc_is_setup_wizard_page() )
			) {
				return true;
			}

			return false;
		}


		function wpbc_can_i_load_on__searchable_resources_page() {

			if (
				   ( isset( $_REQUEST['page'] ) ) && ( $_REQUEST['page'] === 'wpbc-resources' )              // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& ( isset( $_REQUEST['tab'] ) )  && ( $_REQUEST['tab']  === 'searchable_resources' )        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				return true;
			}

			return false;
		}


		function wpbc_can_i_load_on__resources_page() {

			if (
				   ( isset( $_REQUEST['page'] ) ) && ( $_REQUEST['page'] === 'wpbc-resources' )        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				return true;
			}

			return false;
		}


		/**
		 * Can I load scripts on this page for 'shortcode_config'
		 *
		 * @return bool
		 */
		function wpbc_can_i_load_on_this_page__shortcode_config() {

			if (
					 wpbc_can_i_load_on__edit_new_post_page()
				 || (
							   wpbc_can_i_load_on__resources_page()
						&& ( ! wpbc_can_i_load_on__searchable_resources_page() )
					)
				|| wpbc_is_setup_wizard_page()
				// || wpbc_is_bookings_page()		// FixIn: 10.6.6.2.
			){
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Can I load scripts on this page?     Loaded only  at ../admin.php?page=wpbc-resources&tab=searchable_resources
		 *
		 * @return bool
		 */
		function wpbc_can_i_load_on_this_page__modal_search_options() {

			if ( wpbc_can_i_load_on__searchable_resources_page() ){
				return true;
			} else {
				return false;
			}
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Footer  ==  "  >

		function wpbc_show_booking_footer(){

				$wpdev_copyright_adminpanel = get_bk_option( 'booking_wpdev_copyright_adminpanel' );             // check

				$message = '';



				if ( ( 'Off' !== $wpdev_copyright_adminpanel ) && ( ! wpbc_is_this_demo() ) ) {


					/* translators: 1: ... */
					$message .= sprintf( __( 'If you like %1$s please leave us a %2$s rating. A huge thank you in advance!', 'booking' )
										, '<strong>Booking Calendar</strong> ' . esc_attr( WP_BK_VERSION_NUM )
										, '<a href="https://wordpress.org/support/plugin/booking/reviews/#new-post" target="_blank" title="' . esc_attr__( 'Thanks :)', 'booking' ) . '">'
											  . '&#9733;&#9733;&#9733;&#9733;&#9733;'
											  . '</a>'
					);
				}


			if ( ! empty( $message ) ) {

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div id="wpbc-footer" style="position:absolute;bottom:40px;text-align:left;width:95%;font-size:0.9em;text-shadow:0 1px 0 #fff;margin:0;color:#888;">' . $message . '</div>';

				?>
				<script type="text/javascript">
					jQuery(document).ready(function () {
						jQuery('#wpfooter').append(jQuery('#wpbc-footer'));
					});
				</script>
				<?php
			}
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==   P a g i n a t i o n    o f    T a b l e    L  i s t i n g  ==  "  >

		/**
			 * Show    P a g i n a t i o n
		 *
		 * @param int $summ_number_of_items     - total  number of items
		 * @param int $active_page_num          - number of activated page
		 * @param int $num_items_per_page       - number of items per page
		 * @param array $only_these_parameters  - array of keys to exclude from links
		 * @param string $url_sufix             - usefule for anchor to  HTML section  with  specific ID,  Example: '#my_section'
		 */
		function wpbc_show_pagination( $summ_number_of_items, $active_page_num, $num_items_per_page , $only_these_parameters = false, $url_sufix = '' ) {

			if ( empty( $num_items_per_page ) ) {
				$num_items_per_page = '10';
			}

			$pages_number = ceil( $summ_number_of_items / $num_items_per_page );
			if ( $pages_number < 2 )
				return;

			//Fix: 5.1.4 - Just in case we are having tooo much  resources, then we need to show all resources - and its empty string
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ( isset( $_REQUEST['wh_booking_type'] ) ) && ( strlen( wp_unslash( $_REQUEST['wh_booking_type'] ) ) > 1000 ) ) {
				$_REQUEST['wh_booking_type'] = '';
			}

			// First  parameter  will overwriten by $_GET['page'] parameter
			$bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), array('page_num'), $only_these_parameters );


			?>
			<span class="wpdevelop wpbc-pagination">
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12 text-center control-group0">
							<nav class="btn-toolbar">
							  <div class="btn-group wpbc-no-margin" style="float:none;">

								<?php
								if ( $pages_number > 1 ) { ?>
									<a class="button button-secondary <?php
									echo ( $active_page_num == 1 ) ? ' disabled' : ''; ?>"
									   href="<?php
									   echo esc_url( $bk_admin_url ); ?>&page_num=<?php
									   if ( $active_page_num == 1 ) {
										   echo esc_attr( $active_page_num );
									   } else {
										   echo esc_attr( $active_page_num - 1 );
									   }
									   echo esc_attr( $url_sufix );
									   ?>">
											<?php
											esc_html_e( 'Prev', 'booking' ); ?>
										</a>
									<?php
								}

								/** Number visible pages (links) that linked to active page, other pages skipped by "..." */
								$num_closed_steps = 3;

								for ( $pg_num = 1; $pg_num <= $pages_number; $pg_num++ ) {

										if ( ! (
												   ( $pages_number > ( $num_closed_steps * 4) )
												&& ( $pg_num > $num_closed_steps )
												&& ( ( $pages_number - $pg_num + 1 ) > $num_closed_steps )
												&& (  abs( $active_page_num - $pg_num ) > $num_closed_steps )
										   ) ) {
											?> <a class="button button-secondary <?php if ($pg_num == $active_page_num ) echo ' active'; ?>"
												 href="<?php echo esc_url( $bk_admin_url ); ?>&page_num=<?php echo esc_attr( $pg_num );
												 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												 echo $url_sufix; ?>">
												<?php echo wp_kses_post( $pg_num ); ?>
											  </a><?php

											if ( ( $pages_number > ( $num_closed_steps * 4) )
													&& ( ($pg_num+1) > $num_closed_steps )
													&& ( ( $pages_number - ( $pg_num + 1 ) ) > $num_closed_steps )
													&&  ( abs($active_page_num - ( $pg_num + 1 ) ) > $num_closed_steps )
												) {
												echo ' <a class="button button-secondary disabled" href="javascript:void(0);">...</a> ';
											}
										}
								}

								if ( $pages_number > 1 ) { ?>
									<a class="button button-secondary <?php
									echo ( $active_page_num == $pages_number ) ? ' disabled' : ''; ?>"
									   href="<?php
									   echo esc_url( $bk_admin_url ); ?>&page_num=<?php
									   if ( $active_page_num == $pages_number ) {
										   echo esc_attr( $active_page_num );
									   } else {
										   echo esc_attr( $active_page_num + 1 );
									   }
									   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									   echo $url_sufix; ?>">
											<?php
											esc_html_e( 'Next', 'booking' ); ?>
										</a>
								<?php
								}
								?>

							  </div>
							</nav>
						</div>
					</div>
				</div>
			</span>
			<?php
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Number of New Bookings  ==  "  >

		/**
		 * Reset Cache for getting Number of new bookings. After this operation,  system  will  get  number of new bookings from  the DB.
		 *
		 * @return void
		 */
		function wpbc_booking_cache__new_bookings__reset(){
			update_bk_option( 'booking_cache__new_bookings__saved_date', '' );
		}
		add_action( 'wpbc_track_new_booking', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_set_booking_pending', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_set_booking_approved', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_move_booking_to_trash', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_restore_booking_from_trash', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_delete_booking_completely', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_set_booking_as_read', 'wpbc_booking_cache__new_bookings__reset' );
		add_action( 'wpbc_set_booking_as_unread', 'wpbc_booking_cache__new_bookings__reset' );


/**
 * Get number of new bookings
 * @return false|int|mixed|null
 */
function wpbc_db_get_number_new_bookings() {

	$new_bookings__number     = get_bk_option( 'booking_cache__new_bookings__number' );
	$new_bookings__saved_date = get_bk_option( 'booking_cache__new_bookings__saved_date' );

	$nowdate_str_ymdhis = gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) );

	if ( ! empty( $new_bookings__saved_date ) ) {

		$is_expired = ( strtotime( $new_bookings__saved_date ) <= strtotime( '-10 minutes', strtotime( $nowdate_str_ymdhis ) ) );

		if ( ! $is_expired ) {
			return $new_bookings__number;
		}
	}

	global $wpdb;

	$trash_bookings = ' AND bk.trash != 1 ';                                                                // FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings} .
	$sql_req        = "SELECT bk.booking_id FROM {$wpdb->prefix}booking as bk WHERE  bk.is_new = 1 {$trash_bookings} ";

	$sql_req = apply_bk_filter( 'get_sql_for_checking_new_bookings', $sql_req );
	$sql_req = apply_bk_filter( 'get_sql_for_checking_new_bookings_multiuser', $sql_req );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$bookings       = $wpdb->get_results( $sql_req );
	$bookings_count = count( $bookings );

	update_bk_option( 'booking_cache__new_bookings__number', $bookings_count );
	update_bk_option( 'booking_cache__new_bookings__saved_date', $nowdate_str_ymdhis );

	return $bookings_count;
}


/**
 * Update 'is_new' status of bookings in Database
 *
 * @param $id_of_new_bookings     inr or comma seperated ID of bookings. Example:  '1'  |   '3,5,7,9'
 * @param $is_new                 '0' | '1'
 * @param $user_id                'user_id'
 *
 */
function wpbc_db_update_number_new_bookings( $id_of_new_bookings, $is_new = '0', $user_id = 1 ) {

	global $wpdb;

	if ( count( $id_of_new_bookings ) > 0 ) {

		$id_of_new_bookings = implode( ',', $id_of_new_bookings );
		$id_of_new_bookings = wpbc_clean_like_string_for_db( $id_of_new_bookings );

		// FixIn: 10.12.1.5.
		if ( 'all' === $id_of_new_bookings ) {
			$update_sql = "UPDATE {$wpdb->prefix}booking SET is_new = {$is_new}  WHERE is_new != {$is_new} ";    // FixIn: 8.2.1.18.

			$update_sql = apply_bk_filter( 'update_sql_for_checking_new_bookings', $update_sql, 0, $user_id );
		} else {
			$update_sql = "UPDATE {$wpdb->prefix}booking SET is_new = {$is_new} WHERE booking_id IN  ( {$id_of_new_bookings} ) ";
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		if ( false === $wpdb->query( $update_sql ) ) {
			debuge_error( 'Error during updating status of bookings at DB', __FILE__, __LINE__ );
			die();
		}
	}
}

	// </editor-fold>


    // <editor-fold     defaultstate="collapsed"                        desc="  ==  User ID | Role  - functions  ==  "  >
			
		/**
			 * Check  if Current User have specific Role
		 * 
		 * @return bool Whether the current user has the given capability. 
		 */
		function wpbc_is_current_user_have_this_role( $user_role ) {
			
		   if ( $user_role == 'administrator' )  $user_role = 'activate_plugins';
		   if ( $user_role == 'editor' )         $user_role = 'publish_pages';
		   if ( $user_role == 'author' )         $user_role = 'publish_posts';
		   if ( $user_role == 'contributor' )    $user_role = 'edit_posts';
		   if ( $user_role == 'subscriber')      $user_role = 'read';
		   
		   return current_user_can( $user_role );
		}
		

		/**
		 * Get Current ID of user or get user ID of Forced log in user in  Booking Calendar MultiUser version.
		 *
		 * @return int
		 */
		function wpbc_get_current_user_id() {																					// FixIn: 9.2.4.1.

			if ( function_exists( 'wpbc_mu__wp_get_current_user' ) ) {
				$user       = wpbc_mu__wp_get_current_user();
				$user_bk_id = $user->ID;
			} else {
				$user_bk_id = get_current_user_id();
			}

			return $user_bk_id;
		}
		
		
		/**
		 * Get Current User Object or get User object of Forced log in user in  Booking Calendar MultiUser version.
		 *
		 * @return stdClass|WP_User|null
		 */
		function wpbc_get_current_user() {																						// FixIn: 9.2.4.1.

			if ( function_exists( 'wpbc_mu__wp_get_current_user' ) ) {
				$user = wpbc_mu__wp_get_current_user();
			} else {
				$user = wp_get_current_user();
			}

			return $user;
		}
		
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Messages for Admin panel  ==  "  >

	
		/**
			 * Show "Saved Changes" message at  the top  of settings page.
		 *
		 */
		function wpbc_show_changes_saved_message() {
			wpbc_show_message ( __('Changes saved.','booking'), 5 );
		}
	
	
		/**
			 * Show Message at  Top  of Admin Pages
		 *
		 * @param type $message         - mesage to  show
		 * @param type $time_to_show    - number of seconds to  show, if 0 or skiped,  then unlimited time.
		 * @param type $message_type    - Default: updated   { updated | error | notice }
		 */
		function wpbc_show_message ( $message, $time_to_show , $message_type = 'updated') {
	
			// Generate unique HTML ID  for the message
			$inner_message_id =  intval( time() * wp_rand(10, 100) );
	
			// Get formated HTML message
			$notice = wpbc_get_formated_message( $message, $message_type, $inner_message_id );
	
			// Get the time of message showing
			$time_to_show = intval( $time_to_show ) * 1000;
	
			// Show this Message
			?> <script type="text/javascript">
				if ( jQuery('.wpbc_admin_message').length ) {
						jQuery('.wpbc_admin_message').append( '<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $notice; ?>' );
					<?php if ( $time_to_show > 0 ) { ?>
						jQuery('#wpbc_inner_message_<?php echo esc_attr( $inner_message_id ); ?>').animate({opacity: 1},<?php echo esc_attr( $time_to_show ); ?>).fadeOut( 2000 );
					<?php } ?>
				}
			</script> <?php
		}
	
	
		/**
			 * Escape and prepare message to  show it
		 *
		 * @param type $message                 - message
		 * @param type $message_type            - Default: updated   { updated | error | notice }
		 * @param string $inner_message_id      - ID of message DIV,  can  be skipped
		 * @return string
		 */
		function wpbc_get_formated_message ( $message, $message_type = 'updated', $inner_message_id = '') {
	
	
			// Recheck  for any "lang" shortcodes for replacing to correct language
			$message =  wpbc_lang( $message );
	
			// Escape any JavaScript from  message
			$notice =   html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;
	
			$notice .= '<a class="close tooltip_left" rel="tooltip" title="'. esc_js(__("Hide",'booking')). '" data-dismiss="alert" href="javascript:void(0)" onclick="javascript:jQuery(this).parent().hide();">&times;</a>';
	
			if (! empty( $inner_message_id ))
				$inner_message_id = 'id="wpbc_inner_message_'. $inner_message_id .'"';
	
			$notice = '<div '.$inner_message_id.' class="wpbc_inner_message '. $message_type . '">' . $notice . '</div>';
	
			return  $notice;
		}
	
	
		/**
			 * Show system info  in settings page
		 *
		 * @param string $message                     ...
		 * @param string $message_type                'info' | 'warning' | 'error'
		 * @param string $title                       __('Important!' ,'booking')  |  __('Note' ,'booking')
		 *
		 * Exmaple:     wpbc_show_message_in_settings( __( 'Nothing Found', 'booking' ), 'warning', __('Important!' ,'booking') );
		 */
		function wpbc_show_message_in_settings( $message, $message_type = 'info', $title = '' , $is_echo = true ) {
	
			$message_content = '';
	
			$message_content .= '<div class="clear"></div>';
	
			$message_content .= '<div class="wpbc-settings-notice notice-' . $message_type . '" style="text-align:left;">';
	
			if ( ! empty( $title ) )
				$message_content .=  '<strong>' . esc_js( $title ) . '</strong> ';
	
			$message_content .= html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;
	
			$message_content .= '</div>';
	
			$message_content .= '<div class="clear"></div>';

			if ( $is_echo ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $message_content;
			} else {
				return $message_content;
			}
	
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Meta Boxes - Open/Close  ==  "  >

		/**
		 * Meta box section open tag
		 *
		 * @param string $metabox_id		HTML  ID of section
  		 * @param string $title				Title in section
		 * @param array $params		[
										'is_section_visible_after_load' => true,			// Default true		-  is this section Visible,  after  page loading or hidden - useful  for Booking > Settings General page LEFT column sections
										'is_show_minimize'   			=> true 			// Default true		-  is show minimize button at  top right section
									]
		 *
		 * @return void
		 */
		function wpbc_open_meta_box_section( $metabox_id, $title, $params = array() ) {

			$defaults = array(
								'is_section_visible_after_load' => true,
								'is_show_minimize'              => true,
								'dismiss_button'                => '',
								'css_class'                     => 'postbox'
						);
			$params   = wp_parse_args( $params, $defaults );

			$my_close_open_win_id = $metabox_id . '_metabox';

			?>
			<div class='meta-box'>
				<div	id="<?php echo esc_attr( $my_close_open_win_id ); ?>"
						class="<?php echo esc_attr( $params['css_class'] ); ?> <?php
											if ( $params['is_show_minimize'] ) {
												if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) {
													echo 'closed';
												}
											}
						?>"
						style="<?php

						$force_visible = false;

						// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
						$do_expand = ( ( isset( $_REQUEST['do_expand'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['do_expand'] ) ) : '' );

						if ( $my_close_open_win_id === $do_expand ) {
							$force_visible = true;
						}

						if ( ( ! $params['is_section_visible_after_load'] ) && ( ! $force_visible ) ) {
							echo 'display:none';
						}
						?>"
					><div class="postbox-header" style="display: flex;flex-flow: row nowrap;border-bottom: 1px solid #ccd0d4;">
						<h3 class='hndle' style="flex: 1 1 auto;border: none;">
						  <span><?php
							  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							  echo $title;
						  ?></span>
						  <?php
							  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							  echo $params['dismiss_button'];
						  ?>
						</h3>
						<?php if ( $params['is_show_minimize'] ) { ?>
						<div  	title="<?php esc_attr_e('Click to toggle','booking'); ?>" class="handlediv"
								onclick="javascript:wpbc_verify_window_opening(<?php echo esc_attr( wpbc_get_current_user_id() ); ?>, '<?php echo esc_attr( $my_close_open_win_id ); ?>');"
						><br/></div>
						<?php } ?>
					</div>
					<div class="inside">
			<?php
		}


		function wpbc_close_meta_box_section() {
			?>
					  </div>
				</div>
			</div>
			<?php
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Inline JavaScript to Footer page  ==  "  >

/**
 * Queue  JavaScript for later output at  footer
 *
 * @param string $code
 */
function wpbc_enqueue_js( $code ) {
	global $wpbc_queued_js;

	if ( empty( $wpbc_queued_js ) ) {
		$wpbc_queued_js = '';
	}

	$wpbc_queued_js .= "\n" . $code . "\n";
}


/**
 * Output any queued javascript code in the footer.
 */
function wpbc_print_js() {

	global $wpbc_queued_js;

	if ( ! empty( $wpbc_queued_js ) ) {

		$wpbc_queued_js = wp_check_invalid_utf8( $wpbc_queued_js );

		$wpbc_queued_js = wp_specialchars_decode( $wpbc_queued_js, ENT_COMPAT );            // Converts double quotes  '&quot;' => '"'.

		$wpbc_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpbc_queued_js );
		$wpbc_queued_js = str_replace( "\r", '', $wpbc_queued_js );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "<!-- WPBC JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {" . $wpbc_queued_js . "});\n</script>\n<!-- End WPBC JavaScript -->\n";

		$wpbc_queued_js = '';
		unset( $wpbc_queued_js );
	}
}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Support functions for MU version  ==  "  >

		/**
			 * Set  active User Environment in MultiUser  version, depend from owner of booking resource
		 *
		 * @param int $previous_active_user (default=-1) - blank parameter
		 * @param int $bktype - booking resource ID for checking
		 * @return int - ID of Previous Active User
		 *
		 * Usage:
		   $previous_active_user = apply_bk_filter('wpbc_mu_set_environment_for_owner_of_resource', -1, $bktype );

		 */
		function wpbc_mu_set_environment_for_owner_of_resource( $previous_active_user = -1, $bktype = 1 ) {

			if ( class_exists( 'wpdev_bk_multiuser' ) ) {
				// Get  the owner of this booking resource
				$user_bk_id = apply_bk_filter( 'get_user_of_this_bk_resource', false, $bktype );

				$user = wpbc_get_current_user();

				// Get possible other active user settings
				$previous_active_user = apply_bk_filter( 'get_client_side_active_params_of_user' );

				// Set active user of that specific booking resource
				make_bk_action( 'check_multiuser_params_for_client_side_by_user_id', $user_bk_id );
			}

			return $previous_active_user;
		}
		add_bk_filter('wpbc_mu_set_environment_for_owner_of_resource', 'wpbc_mu_set_environment_for_owner_of_resource');


		/**
			 * Set environment for this user in MU version
		 *
		 * @param int $previous_active_user - ID of user
		 * Usage:
		   make_bk_action('wpbc_mu_set_environment_for_user', $previous_active_user );

		 */
		function wpbc_mu_set_environment_for_user( $previous_active_user ) {

			if ( $previous_active_user !== -1 ) {

				// Reactivate the previous active user
				make_bk_action('check_multiuser_params_for_client_side_by_user_id', $previous_active_user );
			}
		}
		add_bk_action('wpbc_mu_set_environment_for_user', 'wpbc_mu_set_environment_for_user');


		/**
		 * Check  if we have simulated user  login  in  Booking Calendar MultiUser version
		 * 					return user ID of regular simulated user or 0 if not simulated
		 * @return int
		 */
		function wpbc_mu__is_simulated_login_as_user(){
			if ( class_exists( 'wpdev_bk_multiuser' ) ) {

				$real_current_user_id = get_current_user_id();                                                                  // Is current user suer booking admin  and if this user was simulated log in
				$is_user_super_admin  = apply_bk_filter( 'is_user_super_admin', $real_current_user_id );
				if ( $is_user_super_admin ) {

					$simulate_user_id = intval( get_option( 'booking_simulate_login_as_user' ) );                               // Is user was simulated log in

					if ( ( ! empty( $simulate_user_id ) ) && ( $simulate_user_id > 0 ) ) {
						return $simulate_user_id;
					}
				}
			}
		    return 0;
		}
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Currency  functions (>=BS)  ==  "  >

		/**
			 * Format booking cost with a currency symbol.
		 *  In MultiUser  version also checking about specific currency  that  belong to  specific WordPress user.
		 *  This checking based on belonging specific booking resource to  specific user.
		 *
		 * @param float $cost
		 * @param int $booking_resource_id
		 * @return string                       - $cost_to_show_with_currency
		 */
		function wpbc_get_cost_with_currency_for_user( $cost, $booking_resource_id  = 0 ){

			if ( ( $cost === '' ) || ( ! class_exists( 'wpdev_bk_biz_s' ) ) ) {
				return '';
			}

			if ( ! empty( $booking_resource_id ) ) {
				$previous_active_user = apply_bk_filter( 'wpbc_mu_set_environment_for_owner_of_resource', - 1, $booking_resource_id );
			}       // MU

			$cost_to_show_with_currency = wpbc_cost_show( $cost, array( 'currency' => wpbc_get_currency() ) );

			if ( ! empty( $booking_resource_id ) ) {
				make_bk_action( 'wpbc_mu_set_environment_for_user', $previous_active_user );
			}                                                // MU

			return $cost_to_show_with_currency;
		}


		/**
			 * Get currency Symbol.
		 *  In MultiUser  version also checking about specific currency  that  belong to  specific WordPress user.
		 *  This checking based on belonging specific booking resource to  specific user.
		 *
		 * @param int $booking_resource_id  - ID of specific booking resource
		 * @return string                   - currency  symbol
		 */
		function wpbc_get_currency_symbol_for_user( $booking_resource_id  = 0 ){

			if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
				return '';
			}

			if ( ! empty( $booking_resource_id ) ) {
				$previous_active_user = apply_bk_filter( 'wpbc_mu_set_environment_for_owner_of_resource', - 1, $booking_resource_id );
			}       // MU

			$currency_symbol = wpbc_get_currency_symbol();

			if ( ! empty( $booking_resource_id ) ) {
				make_bk_action( 'wpbc_mu_set_environment_for_user', $previous_active_user );
			}                                                // MU

			return $currency_symbol;
		}

		/**
		 *  Get Cost  per period: DAY / NIGHT / FIXED / HOUR
		 *  In MultiUser  version  This checking based on belonging specific booking resource to  specific user.
		 *
		 * @param int $booking_resource_id  - ID of specific booking resource
		 * @return string                   - cost  per period
		 */
		function wpbc_get_cost_per_period_for_user( $booking_resource_id  = 0 ){										// FixIn: 10.0.0.14.

			if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
				return '';
			}

			if ( ! empty( $booking_resource_id ) ) {
				$previous_active_user = apply_bk_filter( 'wpbc_mu_set_environment_for_owner_of_resource', - 1, $booking_resource_id );
			}       // MU

			$cost_period = get_bk_option( 'booking_paypal_price_period' );

			if ( ! empty( $booking_resource_id ) ) {
				make_bk_action( 'wpbc_mu_set_environment_for_user', $previous_active_user );
			}                                                // MU

			return $cost_period;
		}
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Approve |  Pending  functions  ==  "  >

/**
 * Check  is this booking approved -- get booking dates in DB and check  status
 *
 * @param $booking_id
 *
 * @return bool
 */
function wpbc_is_booking_approved( $booking_id ) {
	// FixIn: 8.1.2.8.

	$is_booking_approved = false;

	global $wpdb;

	$sql = $wpdb->prepare( "SELECT DISTINCT approved FROM {$wpdb->prefix}bookingdates WHERE booking_id = %d ORDER BY booking_date", $booking_id );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	$dates_result = $wpdb->get_results( $sql );

	foreach ( $dates_result as $my_date ) {

		if ( 1 === intval( $my_date->approved ) ) {
			$is_booking_approved = true;        // FixIn: 8.3.1.2.
		}
	}

	return $is_booking_approved;
}


/**
 * Approve booking in DB (update booking dates in DB like approved = '1')
 *
 * @param $booking_id  int | CSD e.g.            100  |    '1,5,10'
 *
 * @return bool
 */
function wpbc_db__booking_approve( $booking_id ) {

	$booking_id = wpbc_clean_digit_or_csd( $booking_id );                                                    // Check  parameter  if it number or comma separated list  of numbers.

	global $wpdb;

	$update_sql = "UPDATE {$wpdb->prefix}bookingdates SET approved = '1' WHERE booking_id IN ({$booking_id});";

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	if ( false === $wpdb->query( $update_sql ) ) {
		return false;
	}

	// In case if the booking was in Trash,  then  restore it.
	// FixIn: 10.8.1.1.

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	if ( false === $wpdb->query( "UPDATE {$wpdb->prefix}booking SET trash = 0 WHERE booking_id IN ({$booking_id})" ) ) {
		return false;
	}

	return true;
}


/**
 * Approve specific booking and send email about this.
 *
 * @param int    $booking_id - ID of booking
 * @param string $email_reason
 */
function wpbc_auto_approve_booking( $booking_id, $email_reason = '' ) {

	$booking_id = wpbc_clean_digit_or_csd( $booking_id );                   // Check  paramter  if it number or comma separated list  of numbers.

	if ( is_numeric( $booking_id ) ) {                                                                                  // FixIn: 8.1.2.8.
		if ( ! wpbc_is_booking_approved( $booking_id ) ) {
			do_action( 'wpbc_booking_approved', $booking_id, 1 );                                                        // FixIn: 8.7.6.1.

			wpbc_send_email_approved( $booking_id, 1, $email_reason );
		}
	} else {
		$booking_id_arr = explode( ',', $booking_id );
		foreach ( $booking_id_arr as $bk_id ) {
			if ( ! wpbc_is_booking_approved( $bk_id ) ) {
				do_action( 'wpbc_booking_approved', $bk_id, 1 );                                                        // FixIn: 8.7.6.1.
				wpbc_send_email_approved( $bk_id, 1, $email_reason );
			}
		}
	}

	$db_result = wpbc_db__booking_approve( $booking_id );

	if ( false === $db_result ) {

		wpbc_redirect( site_url() );
	}
}


/**
 * Set as Pending specific booking and send email about this.
 *
 * @param int    $booking_id - ID of booking
 * @param string $denyreason
 */
function wpbc_auto_pending_booking( $booking_id, $denyreason = '' ) {
	// FixIn: 8.4.7.25.

	global $wpdb;

	$booking_id = wpbc_clean_digit_or_csd( $booking_id );                   // Check  paramter  if it number or comma separated list  of numbers.

	if ( is_numeric( $booking_id ) ) {                                                                                  // FixIn: 8.1.2.8.
		if ( wpbc_is_booking_approved( $booking_id ) ) {
			wpbc_send_email_deny( $booking_id, 1, $denyreason );
		}
	} else {
		$booking_id_arr = explode( ',', $booking_id );
		foreach ( $booking_id_arr as $bk_id ) {
			if ( wpbc_is_booking_approved( $bk_id ) ) {
				wpbc_send_email_deny( $bk_id, 1, $denyreason );
			}
		}
	}

	$update_sql = "UPDATE {$wpdb->prefix}bookingdates SET approved = '0' WHERE booking_id IN ({$booking_id});";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	if ( false === $wpdb->query( $update_sql ) ) {

		wpbc_redirect( site_url() );
	}
}


/**
 * Cancel (move to  Trash) specific booking.
 *
 * @param int    $booking_id   - ID of booking
 * @param string $email_reason -    reason  of cancellation
 */
function wpbc_auto_cancel_booking( $booking_id, $email_reason = '' ) {
	// FixIn: 8.4.7.25.

	global $wpdb;

	$booking_id = wpbc_clean_digit_or_csd( $booking_id );                   // Check  paramter  if it number or comma separated list  of numbers.

	if ( empty( $email_reason ) ) {    // FixIn: 8.4.7.25.
		// Get the reason of cancellation.
		$email_reason                                = __( 'Payment rejected', 'booking' );
		$auto_cancel_pending_unpaid_bk_is_send_email = get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_is_send_email' );
		if ( 'On' === $auto_cancel_pending_unpaid_bk_is_send_email ) {
			$email_reason = get_bk_option( 'booking_auto_cancel_pending_unpaid_bk_email_reason' );
		}
	}
	// Send decline emails.
	wpbc_send_email_trash( $booking_id, 1, $email_reason );
	// FixIn: 10.12.1.5.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
	if ( false === $wpdb->query( "UPDATE {$wpdb->prefix}booking SET trash = 1 WHERE booking_id IN ({$booking_id})" ) ) {

		wpbc_redirect( site_url() );
	}
}


			// FixIn: 9.9.0.43.

		/**
		 * Auto  approve booking and send email, after successful  payment process
		 *
		 * It resolves issue in  Booking Calendar MultiUser version  for sending "regular email" to  visitors,  if was made booking for booking resource,  that  belong to  regular  user,
		 * and was activated this option "Receive all payments only to Super Booking Admin account" at  the WP Booking Calendar > Settings General page in "Multiuser Options" section
		 *
		 * @param $booking_id
		 *
		 * @return void
		 */
		function wpbc_auto_approve_booking__after_payment( $booking_id ) {
			//--------------------------------------------------------------------------------------------------		// FixIn: 9.9.0.43.
			$is_force_again = false;
			if ( class_exists( 'wpdev_bk_multiuser' ) ) {

				list( $booking_hash, $booking_resource_id ) = wpbc_hash__get_booking_hash__resource_id( $booking_id );

				$user_id = apply_bk_filter( 'get_user_of_this_bk_resource', false, $booking_resource_id );

				$is_booking_resource_user_super_admin = apply_bk_filter( 'is_user_super_admin', $user_id );

				if (
					( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) )
					&& ( ! $is_booking_resource_user_super_admin )
				) {
					// Finish "Super-User" forcing
					make_bk_action( 'finish_force_using_this_user' );
					//Reactivate data for "regular  user
					make_bk_action( 'check_multiuser_params_for_client_side_by_user_id', $user_id );

					$is_force_again = true;
				}
			}
			// -------------------------------------------------------------------------------------------------

			wpbc_auto_approve_booking( $booking_id );

			if ( $is_force_again ) {                                                                                    // FixIn: 9.9.0.43.
				make_bk_action( 'make_force_using_this_user', - 999 );                                                  // '-999' - This ID "by default" is the ID of super booking admin user
			}
		}


		/**
		 * Auto cancel booking and send email, after successful  payment process
		 *
		 * It resolves issue in  Booking Calendar MultiUser version  for sending "regular email" to  visitors,  if was made booking for booking resource,  that  belong to  regular  user,
		 * and was activated this option "Receive all payments only to Super Booking Admin account" at  the WP Booking Calendar > Settings General page in "Multiuser Options" section
		 *
		 * @param $booking_id
		 *
		 * @return void
		 */
		function wpbc_auto_cancel_booking__after_payment( $booking_id ) {
			//--------------------------------------------------------------------------------------------------		// FixIn: 9.9.0.43.
			$is_force_again = false;
			if ( class_exists( 'wpdev_bk_multiuser' ) ) {

				list( $booking_hash, $booking_resource_id ) = wpbc_hash__get_booking_hash__resource_id( $booking_id );

				$user_id = apply_bk_filter( 'get_user_of_this_bk_resource', false, $booking_resource_id );

				$is_booking_resource_user_super_admin = apply_bk_filter( 'is_user_super_admin', $user_id );

				if (
					( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) )
					&& ( ! $is_booking_resource_user_super_admin )
				) {
					// Finish "Super-User" forcing
					make_bk_action( 'finish_force_using_this_user' );
					//Reactivate data for "regular  user
					make_bk_action( 'check_multiuser_params_for_client_side_by_user_id', $user_id );

					$is_force_again = true;
				}
			}
			// -------------------------------------------------------------------------------------------------

			wpbc_auto_cancel_booking( $booking_id );

			if ( $is_force_again ) {                                                                                    // FixIn: 9.9.0.43.
				make_bk_action( 'make_force_using_this_user', - 999 );                                                  // '-999' - This ID "by default" is the ID of super booking admin user
			}
		}
	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  Logs for Notes / remarks  ==  "  >

		/**
		 * Add Log info  to  Notes of bookings
		 *
		 * @param array | int $booking_id_arr
		 * @param string $message
		 */
		function wpbc_db__add_log_info( $booking_id_arr, $message ) {

			if ( get_bk_option( 'booking_log_booking_actions' ) !== 'On' ) {
				return;
			}

			$booking_id_arr = (array) $booking_id_arr;

			$is_append = true;
			foreach ( $booking_id_arr as $booking_id ) {
				$date_time = date_i18n( '[Y-m-d H:i] ' );
				make_bk_action('wpdev_make_update_of_remark' , $booking_id , $date_time . $message , $is_append );                // FixIn: 9.1.2.14.
			}
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  CSV  export support function  ==  "  >

		/**
		 * Get Path from request URL.  E.g.  'my-category/file.csv'  from  url,  like  https://server.com/my-category/file.csv
		 *
		 * It can detect internal sub folder or WordPress,  like http://server.com/my-website/
		 *
		 * @return false|string
		 */
		function wpbc_get_request_url_path(){

			$server_request_uri = ( ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
			$my_parsed_url = wp_parse_url( $server_request_uri );

			if ( false === $my_parsed_url ) {        // seriously malformed URLs, wp_parse_url() may return FALSE.
				return false;
			}

			$my_parsed_url_path = trim( $my_parsed_url['path'] );
			$my_parsed_url_path = trim( $my_parsed_url_path, '/' );


			// Check internal sub folder of WP,  like http://server.com/my-website/[ ... LINK ...]                              // FixIn: 2.0.5.4.
			if ( function_exists( 'wp_parse_url' ) ) {
				$wp_home_server_url = wp_parse_url( home_url() );
			} else {
				$wp_home_server_url = @wp_parse_url( home_url() );
			}

			if ( ( false !== $wp_home_server_url ) && ( ! empty( $wp_home_server_url['path'] ) ) ) {		                    // seriously malformed URLs, wp_parse_url() may return FALSE.

				$server_url_sufix	 = trim( $wp_home_server_url[ 'path' ] );       // [path] => /my-website
				$server_url_sufix	 = trim( $server_url_sufix, '/' );              // my-website

				if ( ! empty( $server_url_sufix ) ) {

					$check_sufix = substr( $my_parsed_url_path, 0, strlen( $server_url_sufix ) );

					if ( $check_sufix === $server_url_sufix ) {

						$my_parsed_url_path = substr( $my_parsed_url_path, strlen( $server_url_sufix ) );

						$my_parsed_url_path = trim( $my_parsed_url_path, '/' );
					}
				}
			}                                                                                                                   //End FixIn: 2.0.5.4

			return $my_parsed_url_path;
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  SKIP BLANK EMAIL  ==  "  >


		/**
		 * Check if this email  is NOT  blank email to  SKIP email  sending -- from autofill fast booking creation at Booking > Add booking page.
		 *
		 * Currently,  plugin  use 'blank@wpbookingmanager.com' for all  blank  bookings, previously  it was used 'admin@blank.com'
		 *
		 * @param $email_address
		 * @param $email_content
		 *
		 * @return bool
		 */
		function wpbc_is_not_blank_email( $email_address, $email_content ) {

			// Previously: 		if ( ( strpos( $to, '@blank.com' ) === false ) && ( strpos( $replace['content'], 'admin@blank.com' ) === false ) ) {  ->send(...) }

			if (
				   ( false === strpos( $email_address, '@blank.com' ) )
				&& ( false === strpos( $email_content, 'admin@blank.com' ) )
				&& ( false === strpos( $email_address, '@wpbookingmanager.com' ) )
				&& ( false === strpos( $email_content, 'blank@wpbookingmanager.com' ) )
			) {
				return true;
			}

			return false;
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc="  ==  PHP PERFORMANCE  ==  "  >


		/**
		 * Start calculation  of time for execution  of specific part  of CODE
		 *
		 * @param string $name              name of section
		 * @param array $php_performance
		 *
		 * @return array
		 *
		 *  Example:
		 *
		 *  	$php_performance = wpbc_php_performance_START( 'emails_sending' , $php_performance );
		 *
		 *      ... some code here ...
		 *
		 *  	$php_performance = wpbc_php_performance_END(   'emails_sending' , $php_performance );
		 *
		 *      echo 'Time of execution: ' . $php_performancep['emails_sending']
		 *
		 */
		function wpbc_php_performance_START( $name, $php_performance ) {

			if ( empty( $php_performance ) ) {
				$php_performance = array();
			}

			$php_performance[ $name ] = microtime( true );

			return $php_performance;
		}

		/**
		 * End calculation  of time for execution  of specific part  of CODE
		 *
		 * @param string $name              name of section
		 * @param array $php_performance
		 *
		 * @return array
		 *
		 *  Example:
		 *
		 *  	$php_performance = wpbc_php_performance_START( 'emails_sending' , $php_performance );
		 *
		 *      ... some code here ...
		 *
		 *  	$php_performance = wpbc_php_performance_END(   'emails_sending' , $php_performance );
		 *
		 *      echo 'Time of execution: ' . $php_performancep['emails_sending']
		 *
		 */
		function wpbc_php_performance_END( $name, $php_performance ) {

			if ( empty( $php_performance ) ) {
				$php_performance = array();
			}
			if ( empty( $php_performance[ $name ] ) ) {
				$php_performance[ $name ] = microtime( true );
			}

			$php_performance[ $name ] = microtime( true ) - $php_performance[ $name ];

			return $php_performance;
		}

		/**
		 * Set maximum number of seconds for execution.
		 *
		 * @param $limit_in_seconds_int
		 *
		 * @return void
		 */
		function wpbc_set_limit_php( $limit_in_seconds_int = 300 ) {

			WPBC_Action_Scheduler_Compatibility::raise_memory_limit();
			WPBC_Action_Scheduler_Compatibility::raise_time_limit( $limit_in_seconds_int );
		}


	// </editor-fold>



/**
 * Temp  fix of styles for edit specific season filter or rate.
 *
 * @return void
 */
function wpbc_add_css_to_hide_seasons() {

	// TODO: 2025-05-03 temp  fix of view edit filter
	?>
	<style tye="text/css">
		.wpdvlp-sub-tabs,
		#wpbc_dcoupons_table, #wpbc_discountcoupons,
		#bulk-action-selector-bottom,
		#toolbar_booking_resources,
		#toolbar_seasonfilters,
		#wpbc_seasonfilters,
		#wpbc_booking_resource_table{
			display: none !important;
		}
		.wpbc_round_white_panel {
			background: #fff;
			padding: 1px 20px 20px;
			border-radius: 10px
		}
		.header_for_cost_object_edit {
			font-size: 20px;
			padding: 0 0 15px;
			border-bottom: 2px solid #f2f2f2;
			margin: 15px 0;
			line-height: 1.5;
			display: flex;
			flex-flow: row nowrap;
			justify-content: space-between;
			align-items: flex-start;
		}
			.in_header_container_for_resource_title {
				color: #5379aa;
				margin-left: 0.3em;
			}
			.header_for_cost_object_edit__close i.wpbc_icn_close::before {
				font-size: 22px;
			}
	</style>
	<?php
}
