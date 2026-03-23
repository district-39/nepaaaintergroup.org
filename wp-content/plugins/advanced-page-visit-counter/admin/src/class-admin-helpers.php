<?php

class APVC_Admin_Helpers {
	public function __construct() {}

	public static function is_var_set( $var ): bool {
		if ( isset( $var ) ) {
			return true;
		}
		return false;
	}

	public static function is_dashboard_section_set( $page, $section, $current_section ): bool {
		if ( APVC_DASHBOARD_PAGE_SLUG === $page && $section === $current_section ) {
			return true;
		}
		return false;
	}

	public static function is_value_set( $var ): string {
		if ( ! empty( $var ) ) {
			return sanitize_text_field( $var );
		}

		return '';
	}

	public static function is_active_menu( $slug ): string {
		if ( isset( $_GET['section'] ) && $slug === $_GET['section'] ) {
			return 'active';
		}
		return '-';
	}

	public static function sanitize_text_or_array_field( $array_or_string ) {
		if ( is_string( $array_or_string ) ) {
			$array_or_string = sanitize_text_field( $array_or_string );
		} elseif ( is_array( $array_or_string ) ) {
			foreach ( $array_or_string as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = self::sanitize_text_or_array_field( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}
		}

		return $array_or_string;
	}

	public static function is_selected_array( $var, $array ) : string {
		if ( in_array( $var, $array ) ) {
			$selected = 'selected';
		} else {
			$selected = '';
		}

		return $selected;
	}

	public static function is_selected_string( $var, $string ) : string {
		if ( isset( $var ) && $var === $string ) {
			$selected = 'selected';
		} else {
			$selected = '';
		}

		return $selected;
	}

	public static function is_selected_checkbox( $var, $string ) : string {
		if ( ! empty( $var ) && $var === $string ) {
			return 'checked';
		}
		return '-';
	}

	public static function is_isset_value( $value, $default ) : string {
		if ( isset( $value ) && ! empty( $value ) ) {
			return stripslashes( $value );
		}
		return stripslashes( $default );
	}

	public static function check_section( $section ) {
		if ( isset( $_GET['page'] ) && isset( $_GET['section'] ) ) {
			if ( ! empty( $_GET['section'] ) && $section === $_GET['section'] ) {
				return true;
			}
		}
		return false;
	}

	public static function check_page( $page ) {
		if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] && ! isset( $_GET['section'] ) ) ) {
			if ( $page === $_GET['page'] ) {
				return true;
			}
		}
		return false;
	}

	public static function get_post_types() {
		return get_post_types( array( 'public' => true ) );
	}

	public static function get_authors() {
		$args          = array( 'orderby' => 'display_name' );
		$wp_user_query = new WP_User_Query( $args );
		return $wp_user_query->get_results();
	}

	public static function get_all_users() {
		return get_users( array( 'fields' => array( 'display_name', 'ID' ) ) );
	}

	public static function get_all_user_roles() {
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		return apply_filters( 'editable_roles', $all_roles );
	}

	public static function get_all_posts() : array {
		return get_posts(
			array(
				'numberposts' => -1,
				'post_status' => 'publish',
				'post_type'   => get_post_types( '', 'names' ),
				'fields'      => array( 'ID', 'title' ),
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);
	}

	public static function validate( $s ) {
		# returns true if the $s is valid ip address
		return (int) ( ! filter_var( $s, FILTER_VALIDATE_IP ) === false );
	}

	# extracts potential ips from a string into an array
	public static function extract_legal_chars( $s ) {
		preg_match_all(
			'/([a-fA-F0-9\.\:])+/',
			$s,
			$strings_with_legal_chars
		);
		return $strings_with_legal_chars[0];
	}

	# returns an array of valid IP addresses extracted from
	# a string or array of strings
	public static function extract( $s ) {
		try {
			$targets = self::extract_legal_chars( is_array( $s ) ? implode( ' ', $s ) : $s );
			$output  = array();
			foreach ( $targets as $key => $value ) {
				if ( self::validate( $value ) ) {
					array_push( $output, $value );
				}
			}
			$output = array_unique( $output );
			return $output;
		} catch ( Exception $e ) {
		}
	}

	public static function get_device_type_list() {
		return APVC_Query::get_device_list();
	}

	public static function get_device_os_list() {
		return APVC_Query::get_device_os_list();
	}

	public static function get_device_br_list() {
		return APVC_Query::get_device_br_list();
	}

	public static function get_parameter( $parameter ) {
		if ( isset( $_GET[ $parameter ] ) ) {
			return sanitize_text_field( $_GET[ $parameter ] );
		} else {
			return '';
		}
	}

	public static function get_shortcodes() {

		$shortcodes = array();

		$shortcodes['template_3']['name'] = __( 'Template 3', 'apvc' );
		$shortcodes['template_3']['key']  = 'template_3';
		$shortcodes['template_3']['icon'] = 'yes';
		$shortcodes['template_3']['css']  = '.template_3{background:#1c8394;padding:15px;margin:15px;border-radius:50px;border:2px solid #1c8394;-webkit-box-shadow:3px 4px 12px -2px rgba(0,0,0,.68);-moz-box-shadow:3px 4px 12px -2px rgba(0,0,0,.68);box-shadow:3px 4px 12px -2px rgba(0,0,0,.68);font-family:calibri;font-size:13pt;text-align:center}.template_3>div{color:#fff;display:inline-block;margin:0 30px}.template_3>div>span{font-weight:700;margin-left:10px}.template_3 .icons{color:#fff;margin-right:5px;font-weight:700}@media (max-width:644px){.template_3>div{margin:0 10px}}@media (max-width:525px){.template_3>div{color:#fff;display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_3>div:last-child{border-bottom:none}}';

		$shortcodes['template_6']['name']  = __( 'Template 6', 'apvc' );
		$shortcodes['template_6']['key']   = 'template_6';
		$shortcodes['template_6']['icon']  = 'yes';
		$shortcodes['template_6']['class'] = 'effect2';
		$shortcodes['template_6']['css']   = '.template_6{background:#764ba2;background:linear-gradient(90deg,#667eea 0,#764ba2 100%);padding:15px;margin:15px;border-radius:40px;border:2px solid #764ba2;font-family:calibri;font-size:13pt;text-align:center}.effect2{position:relative}.effect2:after{z-index:-1;position:absolute;content:"";bottom:15px;right:10px;left:auto;width:50%;top:50%;max-width:300px;background:#777;-webkit-box-shadow:0 15px 10px #777;-moz-box-shadow:0 15px 10px #777;box-shadow:0 15px 10px #777;-webkit-transform:rotate(4deg);-moz-transform:rotate(4deg);-o-transform:rotate(4deg);-ms-transform:rotate(4deg);transform:rotate(4deg)}.template_6>div{color:#fff;display:inline-block;margin:0 30px}.template_6>div>span{font-weight:700;margin-left:10px}.template_6 .icons{color:#fff;margin-right:5px;font-weight:700}@media (max-width:644px){.template_6>div{margin:0 10px}}@media (max-width:525px){.template_6>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fcb8a1}.template_6>div:last-child{border-bottom:none}}';

		$shortcodes['template_7']['name']  = __( 'Template 7', 'apvc' );
		$shortcodes['template_7']['key']   = 'template_7';
		$shortcodes['template_7']['icon']  = 'yes';
		$shortcodes['template_7']['class'] = 'effect2';
		$shortcodes['template_7']['css']   = '.template_7{background:#dfa579;background:linear-gradient(90deg,#c79081 0,#dfa579 100%);padding:15px;margin:15px;border-radius:40px;border:2px solid #dfa579;font-family:calibri;font-size:13pt;text-align:center}.effect2{position:relative}.effect2:after,.effect2:before{z-index:-1;position:absolute;content:"";bottom:25px;left:10px;width:50%;top:35%;max-width:300px;background:#000;-webkit-box-shadow:0 35px 20px #000;-moz-box-shadow:0 35px 20px #000;box-shadow:0 35px 20px #000;-webkit-transform:rotate(-7deg);-moz-transform:rotate(-7deg);-o-transform:rotate(-7deg);-ms-transform:rotate(-7deg);transform:rotate(-7deg)}.effect2:after{-webkit-transform:rotate(7deg);-moz-transform:rotate(7deg);-o-transform:rotate(7deg);-ms-transform:rotate(7deg);transform:rotate(7deg);right:10px;left:auto}.template_7>div{color:#fff;display:inline-block;margin:0 30px}.template_7>div>span{font-weight:700;margin-left:10px}.template_7 .icons{color:#fff;margin-right:5px;font-weight:700}@media (max-width:644px){.template_7>div{margin:0 10px}}@media (max-width:525px){.template_7>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fcb8a1}.template_7>div:last-child{border-bottom:none}}';

		$shortcodes['template_8']['name']  = __( 'Template 8', 'apvc' );
		$shortcodes['template_8']['key']   = 'template_8';
		$shortcodes['template_8']['icon']  = 'yes';
		$shortcodes['template_8']['class'] = 'effect2';
		$shortcodes['template_8']['css']   = '.template_8{background:#5fc3e4;background:linear-gradient(90deg,#e55d87 0,#5fc3e4 100%);padding:15px;margin:15px;border:2px solid #5fc3e4;font-family:calibri;font-size:13pt;text-align:center}.effect2{position:relative;-webkit-box-shadow:0 1px 4px rgba(0,0,0,.3),0 0 40px rgba(0,0,0,.1) inset;-moz-box-shadow:0 1px 4px rgba(0,0,0,.3),0 0 40px rgba(0,0,0,.1) inset;box-shadow:0 1px 4px rgba(0,0,0,.3),0 0 40px rgba(0,0,0,.1) inset}.effect2:after,.effect2:before{content:"";position:absolute;z-index:-1;-webkit-box-shadow:0 0 20px rgba(0,0,0,.8);-moz-box-shadow:0 0 20px rgba(0,0,0,.8);box-shadow:0 0 20px rgba(0,0,0,.8);top:0;bottom:0;left:10px;right:10px;-moz-border-radius:100px/10px;border-radius:100px/10px}.effect2:after{right:10px;left:auto;-webkit-transform:skew(8deg) rotate(3deg);-moz-transform:skew(8deg) rotate(3deg);-ms-transform:skew(8deg) rotate(3deg);-o-transform:skew(8deg) rotate(3deg);transform:skew(8deg) rotate(3deg)}.template_8>div{color:#fff;display:inline-block;margin:0 30px}.template_8>div>span{font-weight:700;margin-left:10px}.template_8 .icons{color:#fff;margin-right:5px;font-weight:700}@media (max-width:644px){.template_8>div{margin:0 10px}}@media (max-width:525px){.template_8>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_8>div:last-child{border-bottom:none}}';

		$shortcodes['template_11']['name'] = __( 'Template 11', 'apvc' );
		$shortcodes['template_11']['key']  = 'template_11';
		$shortcodes['template_11']['icon'] = 'yes';
		$shortcodes['template_11']['css']  = '.template_11{background:#2980b9;background:linear-gradient(225deg,#2980b9 0,#6dd5fa 50%,#fff 100%);padding:15px;margin:15px;border-radius:40px;border:2px solid #2980b9;font-family:calibri;font-size:13pt;text-align:center}.template_11>div{color:#1a1a1a;display:inline-block;margin:0 30px}.template_11>div>span{font-weight:700;margin-left:10px}.template_11 .icons{color:#1a1a1a;margin-right:5px;font-weight:700}@media (max-width:644px){.template_11>div{margin:0 10px}}@media (max-width:525px){.template_11>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #2980b9}.template_11>div:last-child{border-bottom:none}}';

		$shortcodes['template_22']['name'] = __( 'Template 22', 'apvc' );
		$shortcodes['template_22']['key']  = 'template_22';
		$shortcodes['template_22']['icon'] = 'no';
		$shortcodes['template_22']['css']  = '.template_22{background:#355c7d;background:linear-gradient(90deg,#355c7d 0,#6c5b7b 50%,#c06c84 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);box-shadow:0 10px 14px 0 rgba(0,0,0,.1)}.template_22>div{color:#fff;display:inline-block;margin:0 30px}.template_22>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_22>div{margin:0 10px}}@media (max-width:525px){.template_22>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #c06c84}.template_22>div:last-child{border-bottom:none}}';

		$shortcodes['template_23']['name'] = __( 'Template 23', 'apvc' );
		$shortcodes['template_23']['key']  = 'template_23';
		$shortcodes['template_23']['icon'] = 'no';
		$shortcodes['template_23']['css']  = '.template_23{background:#fc5c7d;background:linear-gradient(90deg,#fc5c7d 0,#6c5b7b 50%,#6a82fb 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);box-shadow:0 10px 14px 0 rgba(0,0,0,.1)}.template_23>div{color:#fff;display:inline-block;margin:0 30px}.template_23>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_23>div{margin:0 10px}}@media (max-width:525px){.template_23>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #c06c84}.template_23>div:last-child{border-bottom:none}}';

		$shortcodes['template_24']['name'] = __( 'Template 24', 'apvc' );
		$shortcodes['template_24']['key']  = 'template_24';
		$shortcodes['template_24']['icon'] = 'no';
		$shortcodes['template_24']['css']  = '.template_24{background:#fffbd5;background:linear-gradient(90deg,#fffbd5 0,#b20a2c 50%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);box-shadow:0 10px 14px 0 rgba(0,0,0,.1)}.template_24>div{color:#fff;display:inline-block;margin:0 30px}.template_24>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_24>div{margin:0 10px}}@media (max-width:525px){.template_24>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fffbd5}.template_24>div:last-child{border-bottom:none}}';

		$shortcodes['template_25']['name'] = __( 'Template 25', 'apvc' );
		$shortcodes['template_25']['key']  = 'template_25';
		$shortcodes['template_25']['icon'] = 'no';
		$shortcodes['template_25']['css']  = '.template_25{background:#302b63;background:linear-gradient(90deg,#0f0c29 0,#7365ff 50%,#24243e 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);box-shadow:0 10px 14px 0 rgba(0,0,0,.1)}.template_25>div{color:#fff;display:inline-block;margin:0 30px}.template_25>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_25>div{margin:0 10px}}@media (max-width:525px){.template_25>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #0f0c29}.template_25>div:last-child{border-bottom:none}}';

		$shortcodes['template_26']['name'] = __( 'Template 26', 'apvc' );
		$shortcodes['template_26']['key']  = 'template_26';
		$shortcodes['template_26']['icon'] = 'no';
		$shortcodes['template_26']['css']  = '.template_26{background:#d3cce3;background:linear-gradient(90deg,#d3cce3 0,#e9e4f0 50%,#d3cce3 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.1);box-shadow:0 10px 14px 0 rgba(0,0,0,.1)}.template_26>div{color:#6a6279;display:inline-block;margin:0 30px}.template_26>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_26>div{margin:0 10px}}@media (max-width:525px){.template_26>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #7f7a8a}.template_26>div:last-child{border-bottom:none}}';

		$shortcodes['template_29']['name'] = __( 'Template 29', 'apvc' );
		$shortcodes['template_29']['key']  = 'template_29';
		$shortcodes['template_29']['icon'] = 'no';
		$shortcodes['template_29']['css']  = '.template_29{background:#6d6027;background:linear-gradient(90deg,#6d6027 0,#d3cbb8 80%,#3c3b3f 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);box-shadow:0 10px 14px 0 rgba(0,0,0,.2)}.template_29>div{color:#fff;display:inline-block;margin:0 30px}.template_29>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_29>div{margin:0 10px}}@media (max-width:525px){.template_29>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #00f260}.template_29>div:last-child{border-bottom:none}}';

		$shortcodes['template_31']['name'] = __( 'Template 31', 'apvc' );
		$shortcodes['template_31']['key']  = 'template_31';
		$shortcodes['template_31']['icon'] = 'no';
		$shortcodes['template_31']['css']  = '.template_31{background:#3a1c71;background:linear-gradient(90deg,#3a1c71 0,#d76d77 25%,#ffaf7b 50%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);box-shadow:0 10px 14px 0 rgba(0,0,0,.2)}.template_31>div{color:#1a1a1a;display:inline-block;margin:0 30px}.template_31>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_31>div{margin:0 10px}}@media (max-width:525px){.template_31>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_31>div:last-child{border-bottom:none}}';

		$shortcodes['template_34']['name'] = __( 'Template 34', 'apvc' );
		$shortcodes['template_34']['key']  = 'template_34';
		$shortcodes['template_34']['icon'] = 'no';
		$shortcodes['template_34']['css']  = '.template_34{background:#f7971e;background:linear-gradient(90deg,#f7971e 0,#ffd200 50%,#f7971e 1%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);box-shadow:0 10px 14px 0 rgba(0,0,0,.2)}.template_34>div{color:#1a1a1a;display:inline-block;margin:0 30px}.template_34>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_34>div{margin:0 10px}}@media (max-width:525px){.template_34>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_34>div:last-child{border-bottom:none}}';

		$shortcodes['template_39']['name'] = __( 'Template 39', 'apvc' );
		$shortcodes['template_39']['key']  = 'template_39';
		$shortcodes['template_39']['icon'] = 'no';
		$shortcodes['template_39']['css']  = '.template_39{background:#000;background:linear-gradient(90deg,#000 0,#b3cc2c 50%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);box-shadow:0 10px 14px 0 rgba(0,0,0,.2)}.template_39>div{color:#fff;display:inline-block;margin:0 30px}.template_39>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_39>div{margin:0 10px}}@media (max-width:525px){.template_39>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_39>div:last-child{border-bottom:none}}';

		$shortcodes['template_40']['name'] = __( 'Template 40', 'apvc' );
		$shortcodes['template_40']['key']  = 'template_40';
		$shortcodes['template_40']['icon'] = 'no';
		$shortcodes['template_40']['css']  = '.template_40{background:#ba8b02;background:linear-gradient(90deg,#ba8b02 0,#ffd65d 80%,#ba8b02 100%);padding:15px;margin:15px;font-family:calibri;font-size:13pt;text-align:center;-webkit-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);-moz-box-shadow:0 10px 14px 0 rgba(0,0,0,.2);box-shadow:0 10px 14px 0 rgba(0,0,0,.2)}.template_40>div{color:#1a1a1a;display:inline-block;margin:0 30px}.template_40>div>span{font-weight:700;margin-left:10px}@media (max-width:644px){.template_40>div{margin:0 10px}}@media (max-width:525px){.template_40>div{display:block;margin:0;padding:10px 0;border-bottom:1px solid #fff}.template_40>div:last-child{border-bottom:none}}';

		if ( ! empty( $shortcode ) ) {
			return $shortcodes[ $shortcode ];
		} else {
			return apply_filters( 'apvc_premium_templates', $shortcodes );
		}
	}

	public static function filter_count_format( $count ) {

		$saved_option = get_option( 'apvc_advanced_settings', true );

		if ( isset( $saved_option['apvc_show_shorthand_counter'] ) && 'on' === $saved_option['apvc_show_shorthand_counter'] ) {

			$neg = false;
			if( $count <= -1 ){
				$neg = true;
			}
			if ( absint($count) > 1000 ) {

				$x               = round( absint( $count ) );
				$x_number_format = number_format( $x );
				$x_array         = explode( ',', $x_number_format );
				$x_parts         = array( 'k', 'm', 'b', 't' );
				$x_count_parts   = count( $x_array ) - 1;
				$x_display = $x_array[0] . ( absint( $x_array[1][0] ) !== 0 ? '.' . $x_array[1][0] : '' );
				if( isset($x_parts[ $x_count_parts - 1 ]) ){
					$x_display .= $x_parts[ $x_count_parts - 1 ];
				}
				if( true === $neg ){
					return '-'.$x_display;
				}
				return $x_display;

			}
		}

		return $count;
	}

}
