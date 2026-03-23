<?php /**
 * @version 1.0
 * @description  Templates for Setup pages
 * @category  Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-08-27
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_AJX__Setup_Wizard__Templates {

	// <editor-fold     defaultstate="collapsed"                        desc=" ///  JS | CSS files | Tpl loading  /// "  >

	/**
	 * Define HOOKs for loading CSS and  JavaScript files
	 */
	public function init_load_css_js_tpl() {

		// Load only  at  specific  Page
		if ( wpbc_is_setup_wizard_page() ) {

			add_action( 'wpbc_enqueue_js_files',  array( $this, 'js_load_files' ),     50 );
			add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ), 50 );

			add_action( 'wpbc_hook_settings_page_footer', array( $this, 'hook__load_templates_at_footer' ) );
		}
	}


	/** JS */
	public function js_load_files( $where_to_load ) {

		$in_footer = true;

		if ( wpbc_is_setup_wizard_page() ){

			wp_enqueue_script( 'wpbc_all', 			wpbc_plugin_url( '/_dist/all/_out/wpbc_all.js' ), 	array( 'jquery' ), 			 WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
			wp_enqueue_script( 'wpbc-main-client', 	wpbc_plugin_url( '/js/client.js' ), 				array( 'wpbc-datepick' ), 	 WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
			wp_enqueue_script( 'wpbc-times', 		wpbc_plugin_url( '/js/wpbc_times.js' ), 			array( 'wpbc-main-client' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );

			wp_enqueue_script( 'wpbc-settings_obj',  	 trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/settings_obj.js',	array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-setup_wizard_obj',  trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/setup_obj.js', 		array( 'wpbc-settings_obj' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-setup_wizard_show', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/setup_show.js',		array( 'wpbc-setup_wizard_obj' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-setup_wizard_ajax', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/setup_ajax.js',		array( 'wpbc-setup_wizard_obj' ), WP_BK_VERSION_NUM, $in_footer );
		}
	}


	/** CSS */
	public function enqueue_css_files( $where_to_load ) {

		if ( wpbc_is_setup_wizard_page() ){

			wp_enqueue_style( 'wpbc-setup_wizard_page', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/setup_page.css', array(), WP_BK_VERSION_NUM );
		}
	}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  Templates  /// "  >


		/**
		 * Load Templates at footer of page
		 *
		 * @param $page string
		 */
		public function hook__load_templates_at_footer( $page ){

			// Hook  from ../includes/page-setup/setup__page.php
			if ( 'wpbc-ajx_booking_setup_wizard' === $page ) {

				$this->wpbc_template__stp_wiz__main_content();

				wpbc_stp_wiz__template__welcome();
				wpbc_stp_wiz__template__general_info();
				wpbc_stp_wiz__template__date_time_formats();
				wpbc_stp_wiz__template__bookings_types();
				wpbc_stp_wiz__template__form_structure();
				wpbc_stp_wiz__template__cal_availability();
				wpbc_stp_wiz__template__color_theme();
				wpbc_stp_wiz__template__optional_other_settings();
				wpbc_stp_wiz__template__wizard_publish();
				wpbc_stp_wiz__template__get_started();

				$this->wpbc_template__stp_wiz__left_navigation();
				$this->wpbc_template__stp_wiz__left_navigation_item();

				$this->wpbc_template__timeline_steps();
				$this->wpbc_template__timeline_steps_icons();

				$this->wpbc_template__stp_wiz__footer_buttons();
			}
		}


		// =============================================================================================================
		// == Templates ==
		// =============================================================================================================
		/**
		 * Template - Page Container
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
		private function wpbc_template__stp_wiz__main_content() {
			?><script type="text/html" id="tmpl-wpbc_template__stp_wiz__main_content">
				<#
					var wpbc_template__timeline_steps = wp.template( 'wpbc_template__timeline_steps' );

					jQuery( '.wpbc__container_place__steps_for_timeline' ).html(  wpbc_template__timeline_steps( data.steps_is_done ) );
				#>
				<div class="wpbc__container_place__steps_for_timeline">{{{  wpbc_template__timeline_steps( data.steps_is_done )  }}}</div>
				<div class="wpbc_setup_wizard_page__container 		wpbc_ajx_page__container">

				<# if ( false !== data.steps[ data.current_step ][ 'show_section_left' ] ) {  #>

					<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_left 	wpbc_ajx_page__section_left">
						<# var wpbc_template__stp_wiz__left_navigation = wp.template( 'wpbc_template__stp_wiz__left_navigation' ); #>{{{

							wpbc_template__stp_wiz__left_navigation({
																			'left_navigation'   : data.left_navigation,
																			'ajx_cleaned_params': data.ajx_cleaned_params
																		})
						}}}
					</div>

				<# } #>

					<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_main 	wpbc_ajx_page__section_main">
							<?php if(0){ ?><script><?php } echo '<#'; ?>

								var template__main_section;

								switch ( data.current_step ) {

									case 'welcome':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__welcome' );
										break;

									case 'general_info':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__general_info' );
										break;

									case 'date_time_formats':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__date_time_formats' );
										break;

									case 'bookings_types':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__bookings_types' );
										break;

									case 'form_structure':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__form_structure' );
										break;

									case 'cal_availability':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__cal_availability' );
										break;

									case 'color_theme':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__color_theme' );
										break;

									case 'optional_other_settings':

										template__main_section = wp.template( 'wpbc_stp_wiz__template__optional_other_settings' );
										break;

									case 'wizard_publish':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__wizard_publish' );
										break;

									case 'get_started':
										template__main_section = wp.template( 'wpbc_stp_wiz__template__get_started' );
										break;

									default:
									   // Default
									   template__main_section = wp.template( 'wpbc_stp_wiz__template__general_info' );
								}

							<?php if(0){ ?></script><?php } echo '#>'; ?>
							{{{
									template__main_section( data )
							}}}
					</div>

					<# if ( false !== data.steps[ data.current_step ][ 'show_section_right' ] ) { #>

						<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_right 	wpbc_ajx_page__section_right">
							<?php $this->test_right_widget(); ?>
						</div>
					<# } #>

					<#
						var wpbc_template__stp_wiz__footer_buttons = wp.template( 'wpbc_template__stp_wiz__footer_buttons' );
					#>
					<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_footer		wpbc_ajx_page__section_footer">
						<div class="wpbc__container_place__footer_buttons 		wpbc_container    wpbc_form    wpbc_container_booking_form">{{{
							wpbc_template__stp_wiz__footer_buttons( data )
						}}}</div>
					</div>
				</div>
			</script><?php
		}


		// -------------------------------------------------------------------------------------------------------------
		// == Timeline Steps  -  Line with Checked Dots  ==
		// -------------------------------------------------------------------------------------------------------------
		private function wpbc_template__timeline_steps(){

			?><script type="text/html" id="tmpl-wpbc_template__timeline_steps">
			<# var wpbc_template__timeline_steps_icons = wp.template( 'wpbc_template__timeline_steps_icons' ); #>
			<div class="wpbc_steps_for_timeline_container">
				<div class="wpbc_steps_for_timeline">
					<#

						var steps_count 	   = wpbc_setup_wizard_page__get_steps_count();
						var actual_step_number = wpbc_setup_wizard_page__get_actual_step_number();

						var css_class_for_step = '';
						var css_class_for_line = '';
						var is_line_exist 	   = false;

						for ( var i = 1; i <= steps_count ; i++ ) {

							is_line_exist = ( i > 1 );

							if ( actual_step_number > i ) {
								css_class_for_step = ' wpbc_steps_for_timeline_step_completed';
								css_class_for_line = 'wpbc_steps_for_timeline_line_active';
							} else if ( actual_step_number == i ) {
								css_class_for_step = ' wpbc_steps_for_timeline_step_active';
								css_class_for_line = 'wpbc_steps_for_timeline_line_active';
							} else {
								css_class_for_step = '';
								css_class_for_line = '';
							}


					if ( is_line_exist ) { #>
						<div class="wpbc_steps_for_timeline_step_line {{css_class_for_line}}"></div>
					<# } #>
					<div class="wpbc_steps_for_timeline_step {{css_class_for_step}}">
						{{{ wpbc_template__timeline_steps_icons({ }) }}}
					</div>
					<# } #>
				</div>
			</div>
			</script><?php
		}

	/**
	 * Show template for steps timeline icons
	 *
	 * @return void
	 */
	private function wpbc_template__timeline_steps_icons() {

		?>
		<script type="text/html" id="tmpl-wpbc_template__timeline_steps_icons">
		<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpbc_ui__steps_timeline__get_icons();
		?>
		</script>
		<?php
	}


		// -------------------------------------------------------------------------------------------------------------
		// == Footer Action Buttons ==
		// -------------------------------------------------------------------------------------------------------------
		private function wpbc_template__stp_wiz__footer_buttons(){

			?><script type="text/html" id="tmpl-wpbc_template__stp_wiz__footer_buttons">
				<div class="wpbc__form__div">
					<hr>
					<div class="wpbc__row wpbc__row__btn_prior_next">
						<?php /* ?>
						<div class="wpbc__field">
							<input type="button" value="<?php esc_attr_e('Reset Wizard and Start from Beginning','booking'); ?>"
								   class="wpbc_button_light wpbc_button_danger tooltip_top "  style=""
								   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params(  { 'do_action': 'make_reset' }  ); ">
						</div>
 						<?php */ ?>
						<div class="wpbc__field">
							<#  if ( '' != data['steps'][ data['current_step'] ]['prior'] ) { #>
							<a     class="wpbc_button_light"  style="margin-left:auto;margin-right:10px;" tabindex="0"
								   id="btn__toolbar__buttons_prior"
								   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
								   																		'current_step': '{{data.steps[ data.current_step ].prior}}',
								   																		'do_action': 'none',
								   																		'ui_clicked_element_id': 'btn__toolbar__buttons_prior'
								   																	} );
								   			wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );" ><i class="menu_icon icon-1x wpbc_icn_arrow_back_ios"></i><span>&nbsp;&nbsp;&nbsp;{{data.steps[ data.current_step ].prior_title}}</span></a>

							<# } else { #>
								<span style="margin-left:auto;"></span>
							<# } #>
							<a	   class="wpbc_button_light button-primary" tabindex="0"
								   id="btn__toolbar__buttons_next"
								   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
								   																		'current_step': '{{data.steps[ data.current_step ].next}}',
								   																		   'do_action': '{{data.steps[ data.current_step ].do_action}}',
								   																		'ui_clicked_element_id': 'btn__toolbar__buttons_next'
								   																	} );
								   			wpbc_button_enable_loading_icon( this );
											wpbc_admin_show_message_processing( '' );" ><span>{{data.steps[ data.current_step ].next_title}}&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
						</div>
					</div>
					<div class="wpbc__row wpbc__row__btn_skip_exist">
						<div class="wpbc__field">
							<p class="wpbc_exit_link_small">
								<a href="javascript:void(0)" tabindex="-1"
								   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
								   title="<?php esc_attr_e('Exit and skip the setup wizard','booking'); ?>"
								><?php
									esc_html_e('Exit and skip the setup wizard','booking');
								?></a>
								<?php  ?>
								<a href="javascript:void(0)" class="wpbc_button_danger" style="margin: 25px 0 0;  font-size: 12px;" tabindex="-1"
								   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'make_reset' } ); "
								   title="<?php esc_attr_e('Start Setup from Beginning','booking'); ?>"
								><?php
									esc_html_e('Reset Wizard','booking');
								?></a>
 								<?php /**/ ?>
							</p>
						</div>
					</div>
				</div>
			</script><?php
		}



		// -------------------------------------------------------------------------------------------------------------
		// == Left Navigation ==
		// -------------------------------------------------------------------------------------------------------------
		/**
		 * Template - Left Navigation
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
		private function wpbc_template__stp_wiz__left_navigation() {
			?><script type="text/html" id="tmpl-wpbc_template__stp_wiz__left_navigation">
				<div class="wpbc_navigation_menu_left">
					<#
						var wpbc_template__stp_wiz__left_navigation_item = wp.template( 'wpbc_template__stp_wiz__left_navigation_item' );
						_.each( data.left_navigation, function ( p_val, p_id, p_data_arr ){
							if ( undefined === p_val.a_style){ p_val.a_style = ''; }
							if ( undefined === p_val.style){   p_val.style = ''; }
					#>{{{
							wpbc_template__stp_wiz__left_navigation_item({
																			'id'      : p_id,
																			'data_arr': p_val
																		})
						}}}
					<# } ); #>
				</div>
			</script><?php
		}

		
		/**
		 * Template - Left Navigation Item
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
		private function wpbc_template__stp_wiz__left_navigation_item() {
			?><script type="text/html" id="tmpl-wpbc_template__stp_wiz__left_navigation_item">
				<div id="{{data.id}}"
					 class="wpbc_navigation_menu_left_item {{data.data_arr.class}}" style="{{data.data_arr.style}}">
					<div class="wpbc_navigation_menu_left_item_container">
						<a class="wpbc_navigation_menu_left_item_a" style="{{data.data_arr.a_style}}" onclick="javascript:{{{data.data_arr.action}}}" href="javascript:void(0);">
							<i class="wpbc_navigation_menu_left_item_icon	menu_icon icon-1x {{data.data_arr.icon}}"></i>
							<div class="wpbc_navigation_menu_left_item_text">{{{data.data_arr.title}}}</div>
						</a>
						<# if ( undefined != data.data_arr.right_icon ) { #>
						<a class="wpbc_navigation_menu_left_item_icon_right" onclick="javascript:{{{data.data_arr.right_icon.action}}}" href="javascript:void(0);"
							><i class="menu_icon icon-1x {{data.data_arr.right_icon.icon}}"></i>
							<# if ( undefined != data.data_arr.right_icon.text ) { #>
								<div class="wpbc_navigation_menu_left_small_text_right">{{{data.data_arr.right_icon.text}}}</div>
							<# } #>
						</a>
						<# } #>
					</div>
				</div>
			</script><?php
		}



		// TODO: Update this
		private function test_right_widget(){
			?>
					<div class="wpbc_widgets">
						<div class="wpbc_widget wpbc_widget_change_calendar_skin">
							<div class="wpbc_widget_header">
								<span class="wpbc_widget_header_text">Calendar Skin</span>
								<a href="/" class="wpbc_widget_header_settings_link"><i class="menu_icon icon-1x wpbc_icn_settings"></i></a>
							</div>
							<div class="wpbc_widget_content wpbc_ajx_toolbar" style="margin:0 0 20px;">
								<div class="ui_container">
									<div class="ui_group    ui_group__change_calendar_skin">
									</div>
								</div>
							</div>
						</div>
					</div>
			<?php
		}

	// </editor-fold>

}


/**
 * Just for loading CSS and  JavaScript files
 */
if ( true ) {
	$wpbc_setup_wizard_page_loading = new WPBC_AJX__Setup_Wizard__Templates;
	$wpbc_setup_wizard_page_loading->init_load_css_js_tpl();
}
