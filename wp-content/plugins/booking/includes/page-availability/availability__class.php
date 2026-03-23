<?php /**
 * @version 1.0
 * @description WPBC_AJX__Availability
 * @category  WPBC_AJX__Availability Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-10-24
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_AJX__Availability {

	// <editor-fold     defaultstate="collapsed"                        desc=" ///  JS | CSS files | Tpl loading  /// "  >

		/**
		 * Define HOOKs for loading CSS and  JavaScript files
		 */
		public function init_load_css_js_tpl() {

			$server_request_uri = ( ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
			// Load only  at  specific  Page
			if  ( strpos( $server_request_uri, 'page=wpbc-availability' ) !== false ) {

				add_action( 'wpbc_enqueue_js_files',  array( $this, 'js_load_files' ),     50 );
				add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ), 50 );

				add_action( 'wpbc_hook_settings_page_footer', array( $this, 'hook__page_footer_tmpl' ) );
			}
		}


		/** JS */
		public function js_load_files( $where_to_load ) {

			$in_footer = true;

			if ( wpbc_is_availability_page() )
			if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

				wp_enqueue_script(    'wpbc-ajx_availability_page'
									, trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/availability_page.js'         /* wpbc_plugin_url( '/_out/js/code_mirror.js' ) */
									, array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );                     //FixIn: 9.8.1

				wp_enqueue_script( 'wpbc_all',         wpbc_plugin_url( '/_dist/all/_out/wpbc_all.js' ),                 	array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );          // FixIn: 9.8.6.1.
				wp_enqueue_script( 'wpbc-main-client', wpbc_plugin_url( '/js/client.js' ),     array( 'wpbc-datepick' ),    WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
				wp_enqueue_script( 'wpbc-times',       wpbc_plugin_url( '/js/wpbc_times.js' ), array( 'wpbc-main-client' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
				/**
				 *
				 * wp_localize_script( 'wpbc_all', 'wpbc_live_request_obj'
				 * , array(
				 * 'ajx_booking'  => '',
				 * 'reminders' => ''
				 * )
				 * );
				 */
			}
		}


		/** CSS */
		public function enqueue_css_files( $where_to_load ) {

			if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

				wp_enqueue_style( 'wpbc-ajx_availability_page'
								, trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/availability_page.css'          //, wpbc_plugin_url( '/includes/listing_ajx_booking/o-ajx_booking-listing.css' )
								, array(), WP_BK_VERSION_NUM );
			}
		}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  R e q u e s t  /// "  >

	/**
	 * Get params names for escaping and/or default value of such  params
	 *
	 * @return array        array (  'resource_id'      => array( 'validate' => 'digit_or_csd',  	'default' => array( '1' ) )
	 *                             , ... )
	 */
	static public function request_rules_structure(){

		$default_resource_id = wpbc_get_default_resource();

		return  array(
			  'do_action'	    	=> array( 'validate' => array( 'none', 'set_availability', 'make_reset', 'erase_availability', 'change_month_number_in_row' ),     'default' => 'none' )			// FixIn: 10.8.1.5.

		    , 'resource_id' 		=> array( 'validate' => 'd',  	'default' => $default_resource_id )	    	// 'digit_or_csd' can check about 'digit_or_csd' in arrays, as well         // if ['0'] - All  booking resources
			, 'dates_selection' 	=> array( 'validate' => 's', 	'default' => '' )
			, 'dates_availability'	=> array( 'validate' => array( 'unavailable', 'available' ),     'default' => 'unavailable' )

			, 'ui_clicked_element_id'   				=> array( 'validate' => 's', 'default' => '' )
 			, 'ui_usr__availability_selected_toolbar'   => array( 'validate' => array( 'info', 'calendar_settings' ),     'default' => 'info' )

			// Calendar settings
			, 'calendar__start_week_day' 		=> array( 'validate' => array( '0','1','2','3','4','5','6' ),	'default' => intval(get_bk_option( 'booking_start_day_weeek' )) )
			, 'calendar__days_selection_mode' 	=> array( 'validate' => array( 'multiple', 'dynamic' ),     	'default' => 'dynamic' )
			, 'calendar__view__visible_months' 	=> array( 'validate' => 'd',  	'default' => 12 )
			, 'calendar__view__months_in_row' 	=> array( 'validate' => 'd',  	'default' => 3 )						// FixIn: 10.8.1.5.
			, 'calendar__view__width' 			=> array( 'validate' => 's',  	'default' => '100%' )
			, 'calendar__view__max_width' 		=> array( 'validate' => 's',  	'default' => '' )			// default ''   it's will be set  as 341px,
			, 'calendar__view__cell_height' 	=> array( 'validate' => 's',  	'default' => '38px' )			// '48px' || ''

//			, 'calendar__view__visible_months' 	=> array( 'validate' => 'd',  	'default' => 1 )
//			, 'calendar__view__months_in_row' 	=> array( 'validate' => 'd',  	'default' => 1 )
//			, 'calendar__view__width' 			=> array( 'validate' => 's',  	'default' => '300px' )
//			, 'calendar__view__max_width' 		=> array( 'validate' => 's',  	'default' => '300px' )			// default ''   it's will be set  as 341px,
//			, 'calendar__view__cell_height' 	=> array( 'validate' => 's',  	'default' => '48px' )		// '45px' || ''

			, 'calendar__timeslot_day_bg_as_available' 	=> array( 'validate' => 's'
															    , 'default' => ('On' === get_bk_option( 'booking_timeslot_day_bg_as_available' ) ) ? ' wpbc_timeslot_day_bg_as_available' : '' )

		);

	}


		/**
		 * Get default params
		 *
		 * @return array        array (  'ui_wh_modification_date_radio' => 0
		 *                             , ... )
		 */
		static public function get__request_values__default() {

			$request_rules_structure = self::request_rules_structure();

			$default_params_arr = array();

			$structure_type = 'default';

			foreach ( $request_rules_structure as $key => $value ) {
				$default_params_arr[ $key ] = $value[ $structure_type ];
			}

			return $default_params_arr;
		}

	// </editor-fold>



	// <editor-fold     defaultstate="collapsed"                        desc=" ///  Templates  /// "  >

		// Templates ===================================================================================================

		/**
		 * Templates at footer of page
		 *
		 * @param $page string
		 */
		public function hook__page_footer_tmpl( $page ){

			// page=wpbc&tab=vm_booking_listing
			if ( 'wpbc-ajx_booking_availability'  === $page ) {		// from >>	do_action( 'wpbc_hook_settings_page_footer', 'wpbc-ajx_booking_availability' ); as availability_page.php in bottom  of content method
				$this->template__main_page_content();

				$this->template_toolbar_select_booking_resource();
				$this->template_toolbar_select_month_number_in_row();        // FixIn: 10.8.1.5.
				$this->template__widget_available_unavailable();
				$this->template__widget_calendar_legend();
			}
		}


		/**
		 * Template
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
		private function template__main_page_content() {
			?><script type="text/html" id="tmpl-wpbc_ajx_availability_main_page_content">
				<div class="wpbc_ajx_avy__container">
					<div class="wpbc_ajx_avy__section_left">
						<?php
							$is_booking_change_over_days_triangles = get_bk_option( 'booking_change_over_days_triangles' );
							$is_booking_change_over_days_triangles = ( $is_booking_change_over_days_triangles !== 'Off' ) ? "wpbc_change_over_triangle" : '';
						?>
						<div class="wpbc_ajx_avy__calendar <?php echo esc_attr( $is_booking_change_over_days_triangles ); ?>"><?php esc_html_e('Calendar is loading...', 'booking'); ?></div>
					</div>
					<div class="wpbc_ajx_avy__section_right">
						<div class="wpbc_widgets">
						<# <?php if (0) { ?><script type="text/javascript"><?php } ?>

							var wpbc_ajx_select_booking_resource = wp.template( 'wpbc_ajx_select_booking_resource' );
							jQuery( '#wpbc_hidden_template__select_booking_resource').html( wpbc_ajx_select_booking_resource( data	) );

							var wpbc_widget__available_unavailable = wp.template( 'wpbc_ajx_widget_available_unavailable' );
							var wpbc_widget__calendar_legend       = wp.template( 'wpbc_ajx_widget_calendar_legend' );

							// FixIn: 10.8.1.5.
							var wpbc_ajx_select_month_number_in_row = wp.template( 'wpbc_ajx_select_month_number_in_row' );
							jQuery( '#wpbc_template__select_month_number_in_row').html( wpbc_ajx_select_month_number_in_row( data	) );

						<?php if (0) { ?></script><?php } ?>
						#>

						{{{   	wpbc_widget__available_unavailable( data.ajx_cleaned_params )	}}}
						{{{  	wpbc_widget__calendar_legend( { } )   		}}}

						</div>
					</div>
				</div>
			</script><?php
		}


		private function template__widget_available_unavailable(){

		    ?><script type="text/html" id="tmpl-wpbc_ajx_widget_available_unavailable">
				<div class="wpbc_widget wpbc_widget_available_unavailable">
					<div class="wpbc_widget_header">
						<span class="wpbc_widget_header_text"><?php esc_html_e('Apply availability', 'booking'); ?></span>
						<a href="/" class="wpbc_widget_header_settings_link"><i class="menu_icon icon-1x wpbc_icn_settings"></i></a>
					</div>
					<div class="wpbc_widget_content wpbc_ajx_toolbar" style="margin:0 0 20px;">
						<div class="ui_container" >
							<div class="ui_group    ui_group__available_unavailable"><?php

								//	Available 		Radio
								?><div class="ui_element ui_nowrap"><?php
									wpbc_ajx_avy__ui__available_radio();
								?></div><?php


								//	Unavailable 	Radio
								?><div class="ui_element ui_nowrap"><?php
									wpbc_ajx_avy__ui__unavailable_radio();
								?></div><?php

								// Set checked specific Radio button,  depends on  last action  from  user
								?><# <?php if (0) { ?><script type="text/javascript"><?php } ?>

									jQuery( document ).ready( function (){

										//Helper,  if we click on button  side,  and not at  radio button or label,  then  make radio checked.
										jQuery( '.ui_btn_avy__set_days_availability__available__outer_button' ).on( 'click', function (){
											jQuery( '#ui_btn_avy__set_days_availability__available' ).prop( "checked", true ).trigger( 'change' );
										} );
										jQuery( '.ui_btn_avy__set_days_availability__unavailable__outer_button' ).on( 'click', function (){
											jQuery( '#ui_btn_avy__set_days_availability__unavailable' ).prop( "checked", true ).trigger( 'change' );
										} );

										// Set checked or not, specific radio buttons
										if ( 'unavailable' == data.dates_availability ){
											jQuery( '#ui_btn_avy__set_days_availability__unavailable' ).prop( 'checked', true );//.trigger( 'change' );
										}
										if ( 'available' == data.dates_availability ){
											jQuery( '#ui_btn_avy__set_days_availability__available' ).prop( 'checked', true );//.trigger( 'change' );
										}
									} );

								<?php if (0) { ?></script><?php } ?> #><?php


								//	Apply 			Button
								?><div class="ui_element"><?php
									wpbc_ajx_avy__ui__availability_apply_btn();
								?></div>

							</div>
						</div>
					</div>
				</div>
			</script><?php
		}


		private function template__widget_calendar_legend(){

		    ?><script type="text/html" id="tmpl-wpbc_ajx_widget_calendar_legend">
				<div class="wpbc_widget wpbc_widget_calendar_legend">
					<div class="wpbc_widget_header">
						<span class="wpbc_widget_header_text"><?php esc_html_e('Calendar Legend', 'booking'); ?></span>
						<a href="/" class="wpbc_widget_header_settings_link"><i class="menu_icon icon-1x wpbc_icn_settings"></i></a>
					</div>
					<div class="wpbc_widget_content wpbc_ajx_toolbar" style="margin:0 0 20px;">
						<div class="ui_container" >
							<div class="ui_group    ui_group__available_unavailable"><?php

								?><div class="ui_element ui_nowrap"><?php

									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo wpbc_replace_shortcodes_in_booking_form__legend_items(
												'[legend_items'
												            . ' items="resource_unavailable"'
												            . ' titles="resource_unavailable={'
																								. htmlspecialchars( __( "Resource unavailable days", 'booking' ), ENT_QUOTES)
																						 . '}"'
															. ' text_for_day_cell="' . gmdate( 'd' ) . '"'
															. ' unavailable_day_cell_tag="a"'
												.']'
										);
								?></div><?php

								?><div style="border-top:1px solid #d3d3d3;width:100%;margin: 10px 0 -5px;"></div><?php

								?><div class="ui_element ui_nowrap"><?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo wpbc_replace_shortcodes_in_booking_form__legend_items(
												'[legend_items'
																. ' items="unavailable,available,pending,approved,partially"'
																. ' titles="'
																			//.' unavailable={' . htmlspecialchars(  __( "Unavailable", 'booking' ), ENT_QUOTES ) . '}'
																			//.' pending={' . htmlspecialchars(  __( "Pending", 'booking' ), ENT_QUOTES ) . '}'
																		 .'"'
																. ' text_for_day_cell="' . gmdate( 'd' ) . '"'
																. ' unavailable_day_cell_tag="span"'
												.']'
										);

								?></div><?php

							?>
							</div>
						</div>
					</div>
				</div>
				<style type="text/css">
					.wpbc_ajx_toolbar .ui_container .ui_group .ui_element > .block_hints.datepick {
						height: auto;
						margin: 8px 0 0 !important;
					}
				</style>
			</script><?php
		}


		private function template_toolbar_select_booking_resource(){

			// Template
			?><script type="text/html" id="tmpl-wpbc_ajx_select_booking_resource"><?php

				if ( ! class_exists('wpdev_bk_personal') ) {
					echo '</script>';
					return  false;
				}

				$booking_action = 'select_booking_resource';

				$el_id = 'ui_btn_' . $booking_action;

				if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
					echo '</script>';
					return false;
				}


						?><div class="ui_element"><?php

							wpbc_flex_label(
												array(
													  'id' 	  => $el_id
													, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Booking resource', 'booking' ) . ':</span>'
												)
										   );

							?><select class="wpbc_ui_control wpbc_ui_select change_booking_resource_selectbox"
									  id="<?php echo esc_attr( $el_id ); ?>" name="<?php echo esc_attr( $el_id ); ?>"

									  <?php /* ?>onfocus="javascript:console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"<?php /**/ ?>

									  onchange="javascript:wpbc_admin_show_message_processing( '' );wpbc_ajx_availability__send_request_with_params( {
																											  'resource_id': 	    jQuery( this ).val()
																											, 'dates_availability': jQuery( '.wpbc_radio__set_days_availability:checked' ).val()
																										    , 'dates_selection':    ''
																										    , 'do_action': 'change_booking_resource'
																										} );"
							  ><#
								_.each( data.ajx_data.ajx_booking_resources, function ( p_resource, p_resource_id, p_data ){
									#><option value="{{p_resource.booking_type_id}}"
											  <#
												if ( data.ajx_cleaned_params.resource_id == p_resource.booking_type_id ) {
													#> selected="SELECTED" <#
												}
											  #>
											  style="<#
														if( undefined != p_resource.parent ) {
															if( '0' == p_resource.parent ) {
																#>font-weight:600;<#
															} else {
																#>font-size:0.95em;padding-left:20px;<#
															}
														}
													#>"
									><#
										if( undefined != p_resource.parent ) {
											if( '0' != p_resource.parent ) {
												#>&nbsp;&nbsp;&nbsp;<#
											}
										}
									#>{{p_resource.title}}</option><#
								});
							#>
							</select><?php

						?></div>

						<div class="ui_element"><?php

							?><div class="wpbc_ui_separtor" style="margin-left: 8px;"></div><?php

						?></div><?php

			?></script><?php
		}



		private function template_toolbar_select_month_number_in_row(){                                                 // FixIn: 10.8.1.5.

			// Template
			?><script type="text/html" id="tmpl-wpbc_ajx_select_month_number_in_row"><?php

				/*
				?><# console.log( ' == TEMPLATE PARAMS "wpbc_ajx_change_month_number_in_row" == ', data ); #><?php
				*/
				$booking_action = 'select_month_number_in_row';

				$el_id = 'ui_btn_' . $booking_action;

				?><div class="ui_element"><?php
					?><div class="wpbc_ui_separtor" style="margin-left: 8px;"></div><?php
				?></div><?php

				?><div class="ui_element"><?php

					wpbc_flex_label(
										array(
											  'id' 	  => $el_id
											, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Number of months in a row', 'booking' ) . ':</span>'
										)
								   );

					?><select class="wpbc_ui_control wpbc_ui_select change_month_number_in_row_selectbox"
							  id="<?php echo esc_attr( $el_id ); ?>" name="<?php echo esc_attr( $el_id ); ?>"

							  <?php /* ?>onfocus="javascript:console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"<?php /**/ ?>

							  onchange="javascript:wpbc_admin_show_message_processing( '' );wpbc_ajx_availability__send_request_with_params( {
																									  'calendar__view__months_in_row': jQuery( '.change_month_number_in_row_selectbox option:selected' ).val()
																									, 'do_action': 'change_month_number_in_row'
																								} );"
					  ><#
						for (var month_index = 2; month_index < 7; month_index++ ){
						#><option value="{{month_index}}"
									  <#
										if ( data.ajx_cleaned_params.calendar__view__months_in_row == month_index ) {
											#> selected="SELECTED" <#
										}
									  #>
							>{{month_index}}</option><#
						}
						#>
					</select><?php

				?></div><?php

			?></script><?php
		}

	// </editor-fold>



	// <editor-fold     defaultstate="collapsed"                        desc=" ///  A J A X  /// "  >

		// A J A X =====================================================================================================

		/**
		 * Define HOOKs for start  loading Ajax
		 */
		public function define_ajax_hook(){

			// Ajax Handlers.		Note. "locale_for_ajax" rechecked in wpbc-ajax.php
			add_action( 'wp_ajax_'		     . 'WPBC_AJX_AVAILABILITY', array( $this, 'ajax_' . 'WPBC_AJX_AVAILABILITY' ) );	    // Admin & Client (logged in usres)

			// Ajax Handlers for actions
			//add_action( 'wp_ajax_'		     . 'WPBC_AJX_BOOKING_ACTIONS', 			'wpbc_ajax_' . 'WPBC_AJX_BOOKING_ACTIONS' );

			// add_action( 'wp_ajax_nopriv_' . 'WPBC_AJX_BOOKING_LISTING', array( $this, 'ajax_' . 'WPBC_AJX_BOOKING_LISTING' ) );	    // Client         (not logged in)
		}



		/**
		 * Ajax - Get Listing Data and Response to JS script
		 */
		public function ajax_WPBC_AJX_AVAILABILITY() {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['search_params'] ) || empty( $_POST['search_params'] ) ) { exit; }

			// Security  -----------------------------------------------------------------------------------------------    // in Ajax Post:   'nonce': wpbc_ajx_booking_listing.get_secure_param( 'nonce' ),
			$action_name    = 'wpbc_ajx_availability_ajx' . '_wpbcnonce';
			$nonce_post_key = 'nonce';
			$result_check   = check_ajax_referer( $action_name, $nonce_post_key );

			$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) )  ?  intval( $_REQUEST['wpbc_ajx_user_id'] )  :  wpbc_get_current_user_id();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

			/**
			 * SQL  ---------------------------------------------------------------------------
			 *
			 * in Ajax Post:  'search_params': wpbc_ajx_booking_listing.search_get_all_params()
			 *
			 * Use prefix "search_params", if Ajax sent -
			 *                 $_REQUEST['search_params']['page_num'], $_REQUEST['search_params']['page_items_count'],..
			 */

			$user_request = new WPBC_AJX__REQUEST( array(
													   'db_option_name'          => 'booking_availability_request_params',
													   'user_id'                 => $user_id,
													   'request_rules_structure' => WPBC_AJX__Availability::request_rules_structure()
													)
							);
			$request_prefix = 'search_params';
			$request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 		// NOT Direct: 	$_REQUEST['search_params']['resource_id']

			//----------------------------------------------------------------------------------------------------------
			// Get booking resources (sql)
			$resources_arr = wpbc_ajx_get_all_booking_resources_arr();          /**
																				 * Array (   [0] => Array (     [booking_type_id] => 1
																												[title] => Standard
																												[users] => 1
																												[import] =>
																												[export] =>
																												[cost] => 25
																												[default_form] => standard
																												[prioritet] => 0
																												[parent] => 0
																												[visitors] => 2
																					), ...                  */

			$resources_arr_sorted = wpbc_ajx_get_sorted_booking_resources_arr( $resources_arr );

			// FixIn: 10.3.0.7.
			// Check  if $request_params['resource_id'] exist in $resources_arr_sorted,  because it possible situation,  when  some  booking resources was deleted and we do not need to  load old data.
			$is_resource_exist = false;
			foreach ( $resources_arr_sorted as $resource_index => $resource_value_arr ) {
				if ( intval( $resource_value_arr['booking_type_id'] ) === intval( $request_params['resource_id'] ) ) {
					$is_resource_exist = true;
					break;  // All  good
				}
			}
			if ( ! $is_resource_exist ) {
				if ( count( $resources_arr_sorted ) > 0 ) {
					$request_params['resource_id'] = intval( $resources_arr_sorted[0]['booking_type_id'] );
				}
			}


			$data_arr = array();
			$data_arr['ajx_after_action_message'] = '';
			$data_arr['ajx_after_action_result']  = 1;

			if ( 'set_availability' == $request_params['do_action'] ) {

				//  Dates -- update status in DB
				$row_number = wpbc_availability__update_dates_status__sql(    array(
												  'resource_id'     => $request_params['resource_id'],            //  int
												  'prop_name'       => 'date_status',                             // 											  'rate', 'allow_start_day_selection', 'allow_days_number_to_select', 'availability_count', ...
												  'prop_value'      => $request_params['dates_availability'],     // 'available', 'unavailable'				, 'pending', 'approved'
												  'dates_selection' => $request_params['dates_selection']   	  // '2023-04-04 | 2023-04-07'
											) );

				if ( false !== $row_number ) {
					if ( 'available' == $request_params['dates_availability'] ) {
						/* translators: 1: ... */
						$data_arr['ajx_after_action_message'] = sprintf( _n( '%1$s date has been set as %2$s available %3$s', '%1$s dates has been set as %2$s available %3$s', $row_number, 'booking' )
																, '<strong>' . $row_number . '</strong>'
																, '<strong style="color:#11be4c;">', '</strong>' );
					} else {
						/* translators: 1: ... */
						$data_arr['ajx_after_action_message'] = sprintf( _n( '%1$s date has been set as %2$s unavailable %3$s', '%1$s dates has been set as %2$s unavailable %3$s', $row_number, 'booking' )
																, '<strong>' . $row_number . '</strong>'
																, '<strong style="color:#e43939;">', '</strong>' );
					}
					$data_arr['ajx_after_action_result'] = ( $row_number > 0 ) ? 1 : 0;
				}
			}

			if ( 'erase_availability' == $request_params['do_action'] ) {
//TODO: Make new fucntion here to  delete all  items from  DB relative to this booking resource		2023-02-06 14:42
				//  Dates -- update status in DB
				$row_number = wpbc_availability__delete_dates_status__sql(    array(
												  'resource_id'     => $request_params['resource_id'],            //  int
												  'prop_name'       => 'date_status',                             // 											  'rate', 'allow_start_day_selection', 'allow_days_number_to_select', 'availability_count', ...
												  //'prop_value'      => $request_params['dates_availability'],     // 'available', 'unavailable'				, 'pending', 'approved'
												  'dates_selection' => 'all'   	  // '2023-04-04 | 2023-04-07'
											) );
				$data_arr['ajx_after_action_message'] = ( $row_number > 0 )
														/* translators: 1: ... */
														? sprintf( __( 'All %s unavailable dates has been removed', 'booking' ), '<strong>' . $row_number . '</strong>' )
														: sprintf( __( 'No unavailable dates.', 'booking' ), $row_number );
				$data_arr['ajx_after_action_result'] = ( $row_number > 0 ) ? 1 : 0;

			}

			// FixIn: 10.8.1.5.
			if ( 'change_month_number_in_row' == $request_params['do_action'] ) {
				// $request_params['calendar__view__months_in_row'];		// Saving this parameter
				/* translators: 1: ... */
				$data_arr['ajx_after_action_message'] = sprintf( __( 'Set month number in a row as %s', 'booking' ), '<strong>' . $request_params['calendar__view__months_in_row'] . '</strong>' );
				$data_arr['ajx_after_action_result']  = 1;
			}

			$data_arr['booked_dates'] = wpbc__sql__get_booked_dates( array(
																			'resource_id' => $request_params['resource_id']
																		) );

			$data_arr['season_availability'] = wpbc__sql__get_season_availability( array(
																			'resource_id' => $request_params['resource_id']
																		) );

			$data_arr['resource_unavailable_dates'] =  wpbc_resource__get_unavailable_dates($request_params['resource_id']);


			//----------------------------------------------------------------------------------------------------------


			$data_arr['ajx_booking_resources'] = $resources_arr_sorted;

			//----------------------------------------------------------------------------------------------------------

			$data_arr['ajx_nonce_calendar'] = wp_nonce_field( 'CALCULATE_THE_COST', 'wpbc_nonce' . 'CALCULATE_THE_COST' . $request_params['resource_id'], true, false );

			$data_arr['popover_hints'] = array();

			$data_arr['popover_hints']['season_unavailable'] = '<strong>' . esc_html__( 'Season unavailable day', 'booking' ) . '</strong><hr>'
											 	/* translators: 1: ... */
											 	. sprintf( __( 'Change this date status at %1$sBooking Calendar %2$s Availability %3$s Season Availability page.', 'booking' ), '<br>', '&gt;', '&gt;' );
			$data_arr['popover_hints']['weekdays_unavailable'] = '<strong>' . esc_html__( 'Unavailable week day', 'booking' ) . '</strong><hr>'
											   /* translators: 1: ... */
											   . sprintf( __( 'Change this date status at %1$sBooking Calendar %2$s Settings General page %3$s in "Availability" section.', 'booking' ), '<br>', '&gt;', '<br>' );
			$data_arr['popover_hints']['before_after_unavailable'] = '<strong>' . esc_html__( 'Unavailable day, depends on today day', 'booking' ) . '</strong><hr>'
											   /* translators: 1: ... */
											   . sprintf( __( 'Change this date status at %1$sBooking Calendar %2$s Settings General page %3$s in "Availability" section.', 'booking' ), '<br>', '&gt;', '<br>' );



			$data_arr['popover_hints']['toolbar_text'] = '<span style="font-size: 1.05em;line-height: 1.8em;">'.
											   /* translators: 1: ... */
											   sprintf( __( '%1$sSelect days%2$s in calendar then select %3$sAvailable%4$s / %5$sUnavailable%6$s status and click %7$sApply%8$s availability button.', 'booking' )
														, '<strong>', '&nbsp;</strong>'
														, '<strong>&nbsp;', '&nbsp;</strong>'
														, '<strong>&nbsp;', '&nbsp;</strong>'
														, '<strong>&nbsp;', '&nbsp;</strong>'
											   )
											.'</span>';
			/* translators: 1: ... */
			$data_arr['popover_hints']['toolbar_text_available'] = sprintf( __( 'Set dates %1$s as %2$s available.', 'booking' )
																			, '_DATES_'
																			, '_HTML_'
																		);
			/* translators: 1: ... */
			$data_arr['popover_hints']['toolbar_text_unavailable'] = sprintf( __( 'Set dates %1$s as %2$s unavailable.', 'booking' )
																			, '_DATES_'
																			, '_HTML_'
																		);

			// Clear here DATES selection in $request_params['dates_selection'] to  not save such  selection

			if ( 'make_reset' === $request_params['do_action'] ) {

				$is_reseted = $user_request->user_request_params__db_delete();											// Delete from DB

				$request_params['do_action'] = $is_reseted ? 'reset_done' : 'reset_error';
			} else {

				$request_params_to_save = $request_params;

				// Do not safe such elements
				unset( $request_params_to_save['ui_clicked_element_id'] );
				unset( $request_params_to_save['do_action'] );
				unset( $request_params_to_save['dates_selection'] );
				// Do not save "Do not change background color for partially booked days" option ! it must reflect from Booking > Settings General page and not from User options
				unset( $request_params_to_save['calendar__timeslot_day_bg_as_available'] );                             // FixIn: 9.5.5.4.

				$is_success_update = $user_request->user_request_params__db_save( $request_params_to_save );					// Save to DB		// - $request_params - serialized here automatically
			}

			//----------------------------------------------------------------------------------------------------------
			// Send JSON. Its will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) )		Pass JS OBJ: response_data in "jQuery.post( " function on success.
			wp_send_json( array(
								'ajx_data'              => $data_arr,
								// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								'ajx_search_params'     => $_REQUEST[ $request_prefix ],								// $_REQUEST[ 'search_params' ]
								'ajx_cleaned_params'    => $request_params
							) );
		}

	// </editor-fold>

}

/**
 * Just for loading CSS and  JavaScript files
 */
if ( true ) {
	$wpbc_ajx_availability_loading = new WPBC_AJX__Availability;
	$wpbc_ajx_availability_loading->init_load_css_js_tpl();
	$wpbc_ajx_availability_loading->define_ajax_hook();
}