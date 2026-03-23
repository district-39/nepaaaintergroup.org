<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Booking Form Settings
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-03-23
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__toolbar_free.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__submit.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__timeslots.php' );         						// Timeslots Generator
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__preview.php' );         						// Preview
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__preview_templates.php' );  	 				// Templates - Simple Booking Form
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__auto_show_timeslots.php' );  	 				// Auto Load Time Slots -- Editing or Adding form

/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsFormFieldsFree extends WPBC_Page_Structure {
    
    /** Need define some filters */
    public function __construct() {


        

        /**
	 	 * We need to  update these fields after  usual update process :
         * 'booking_form'
         * 'booking_form_show'
         * 'booking_form_visual'
        */
        add_bk_action( 'wpbc_other_versions_activation',   array( $this, 'activate'   ) );      // Activate
        add_bk_action( 'wpbc_other_versions_deactivation', array( $this, 'deactivate' ) );      // Deactivate

        parent::__construct();
    }

    public function in_page() {

		if ( WPBC_Frontend_Settings::is_bfb_enabled( null ) ) {
			return 'test' . (string) wp_rand( 100000, 1000000 );
		}

		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$is_use_simgple_form = get_bk_option( 'booking_is_use_simple_booking_form' );											// FixIn: 8.1.1.12.
			if ( 'On' !== $is_use_simgple_form ) {
				return 'test' . (string) wp_rand( 100000, 1000000 );
			}
		}
        return 'wpbc-settings';
    }

    public function tabs() {


		// Init vars.
		$separator_i    = 0;
		$tabs           = array();
		$subtabs        = array();
		$subtab_default = array(
			'type'            => 'subtab',                        // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'.
			'title'           => '',                              // Example: __( 'Dashboard'                                                                   // Title of TAB.
			'page_title'      => '',                              // __( 'Search Availability'                                                                  // Title of Page.
			'hint'            => '',                              // __( 'Configure the layout and functionality of both the search form and search results.'   // Hint.
			'link'            => '',                              // wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_dashboard_tab',        // Link.
			'css_classes'     => '',                              // cls CSS .
			'font_icon'       => 'wpbc_icn_dashboard',
			'font_icon_right' => 'wpbc-bi-question-circle',                              // 'wpbc-bi-question-circle' .
			'default'         => false,                           // Is this sub tab activated by default or not: true || false.
		);

        $tabs[ 'form' ] = array(
			'is_show_top_path'                   => false,                                // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => 'compact',                           // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'Booking Form', 'booking'),                               // Header - Title.  If false, than hidden.
			'page_description'                   => __('Add, remove, or customize fields in your booking form.','booking'),                               // Header - Title Description.  If false, than hidden.
                              'title'     => __( 'Booking Form', 'booking')             // Title of TAB
                            , 'page_title'=> __( 'Booking Form', 'booking')      // Title of Page
                            , 'hint'      => __( 'Add, remove, or customize fields in your booking form.', 'booking')               // Hint
                            //, 'css_classes'=> ''                                // CSS class(es)
                            , 'font_icon' => 'wpbc_icn_dashboard _customize dashboard rtt draw'         // CSS definition  of forn Icon
                            , 'default'   => false                               // Is this tab activated by default or not: true || false.
                            , 'subtabs'   => array()
							, 'folder_style'     => 'order:15;',
                    );

		$section_id              = 'wpbc_general_settings_booking_form_fields_metabox';
		$subtabs['booking_form_fields'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Booking Form Fields', 'booking' ),
				'page_title'      => __( 'Booking Form Fields', 'booking' ),
				'hint'            => __( 'Add, remove, or customize fields in your booking form.', 'booking' ),
				'font_icon'       => 'wpbc-bi-ui-radios',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
				//'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'link'            => wpbc_get_settings_url() . '&tab=form',
				'default'         => true,
			)
		);
		$tabs[ 'form' ]['subtabs'] = $subtabs;


//        if ( ! class_exists( 'wpdev_bk_personal' ) )																	// FixIn: 8.1.1.12.
//			// FixIn: 10.12.4.7.  $tabs[ 'settings-upgrade' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:1000;' ) );
//        	$tabs[ 'upgrade-link' ] = array(
//                              'title' => __('Get Pro','booking')                // Title of TAB
//                            , 'hint'  => __('Upgrade to higher versions', 'booking')              // Hint
//                            //, 'page_title' => __('Upgrade', 'booking')        // Title of Page
//                            , 'link' => 'https://wpbookingcalendar.com/features/'                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
//                            , 'position' => 'right'                             // 'left'  ||  'right'  ||  ''
//                            //, 'css_classes' => ''                             // CSS class(es)
//                            //, 'icon' => ''                                    // Icon - link to the real PNG img
//                            , 'font_icon' => 'wpbc_icn_redeem'// CSS definition  of forn Icon
//                            //, 'default' => false                              // Is this tab activated by default or not: true || false.
//                            //, 'subtabs' => array()
//							, 'folder_style' => 'order:9999;'
//        );
        
        return $tabs;
    }


    public function content() {
		$this->css();

		// Define Notices Section and show some static messages, if needed
        do_action( 'wpbc_hook_settings_page_header', 'form_field_free_settings');
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    	// Check if MU user activated, otherwise show Warning message.
        //if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  	// User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.


	    // -------------------------------------------------------------------------------------------------------------
	    //  Submit
	    // -------------------------------------------------------------------------------------------------------------
	    $submit_form_name = 'wpbc_form_field_free';                             // Define form name
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	    if ( isset( $_POST[ 'is_form_sbmitted_' . $submit_form_name ] ) ) {
		    // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
		    $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submitting, if it's not refear to the original page

		    wpbc_simple_form__page_save_submit();            // Save Changes
	    }

        echo '<span class="wpdevelop">';
        	wpbc_js_for_bookings_page();		// JavaScript: Tooltips, Popover, Datepick (js & css) //////////////////
        echo '</span>';


		// -------------------------------------------------------------------------------------------------------------
        //  Content
		// -------------------------------------------------------------------------------------------------------------
        ?><div class="clear"></div>
		<div class="wpbc_settings_flex_container"><?php

			$this->left_navigation_menu_template();

			?><div class="wpbc_settings_flex_container_right"><?php

				// This div required for hiding toolbar,  if clicked items in Left Nav Bar.
				?><div id="wpbc_settings__custom_booking_forms_fields__toolbar" class="wpbc_container_hide__on_left_nav_click" style="margin:0 0 25px;"><?php

					wpbc_simple_form__toolbar_free();

				?></div><?php


				?><form  name="<?php echo esc_attr( $submit_form_name ); ?>" id="<?php echo esc_attr( $submit_form_name ); ?>" action="" method="post" style="width:100%;"><?php
					?><span class="metabox-holder"><?php
					   // N o n c e   field, and key for checking   S u b m i t
					   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );

						?><input type="hidden" name="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" id="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" value="1" /><?php
						?><input type="hidden" name="form_visible_section" id="form_visible_section" value="" /><?php
						?><input type="hidden" name="reset_to_default_form" id="reset_to_default_form" value="" /><?php
						?><input type="hidden" name="booking_form_structure_type" id="booking_form_structure_type" value="<?php echo esc_attr( get_bk_option( 'booking_form_structure_type' ) ); ?>" /><?php

						?><div id="wpbc_settings__form_fields_metabox" class="wpbc_container_hide__on_left_nav_click"><?php

							$this->content_section__form_fields();

						?></div><?php

						wpbc_open_meta_box_section( 'wpbc_settings__form_layout', __('Form Layout', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false, 'css_class'=>'postbox wpbc_container_hide__on_left_nav_click' ) );
							$this->content_section__form_layout();
						wpbc_close_meta_box_section();

						$is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
						if ( $is_can ) {
							wpbc_open_meta_box_section( 'wpbc_settings__form_theme', __('Color Theme', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false, 'css_class'=>'postbox wpbc_container_hide__on_left_nav_click' ) );

								$this->content_section__form_color_theme();

								?><table class="form-table"><tbody><tr valign="top" class="wpbc_tr_booking_form_skins"><th scope="row"></th><td><fieldset><?php

									?><div class="wpbc_widget wpbc_widget_color_skins">
										<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_background" style="margin:0 0 20px;">
											<div class="ui_container" ><?php

												$this->content_section__calendar_skin();

												$this->content_section__time_picker_skin();

									?></div></div></div><?php

								?></fieldset></td></tr></tbody></table><?php

							wpbc_close_meta_box_section();


							wpbc_open_meta_box_section( 'wpbc_settings__form_options', __('Form Options', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false, 'css_class'=>'postbox wpbc_container_hide__on_left_nav_click' ) );
								$this->content_section__form_options();
							wpbc_close_meta_box_section();
						}
					?></span><?php
					// -----------------------------------------------------------------------------------------------------
					// Save button
					// -----------------------------------------------------------------------------------------------------
					?><div class="clear" style="height:5px;"></div>
					<input type="submit" value="<?php esc_attr_e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button wpbc_submit_button_trigger" />
				</form>
				<?php
					//wpbc_show_preview__form();
				?>
			</div>
		</div><?php


		wpbc_show_preview__form();

		// Define templates and write  JavaScript for Timeslots in ../generator-timeslots.php
        do_action( 'wpbc_hook_settings_page_footer', 'form_field_free_settings' );

    }


		public function left_navigation_menu_template(){

			?>
			<div class="wpbc_settings_flex_container_left wpbc_settings_flex_container_can_collapse">

				<div class="wpbc_settings_navigation_column">
					<div id="wpbc_settings__form_fields_tab" class="wpbc_settings_navigation_item  wpbc_settings_navigation_item_active">
						<a onclick="javascript:wpbc_navigation_click_show_section(this,'#wpbc_settings__form_fields_metabox,#wpbc_settings__custom_booking_forms_fields__toolbar', '.wpbc_container_hide__on_left_nav_click' );" href="javascript:void(0);">
							<span><?php esc_html_e( 'Form Fields', 'booking' ); ?></span>
							<i class="tooltip_right wpbc_set_nav__right_icon menu_icon icon-1x wpbc_icn_format_align_left" data-original-title="<?php echo esc_attr( __( 'Form Fields', 'booking' ) ); ?>"></i>
						</a>
					</div>
					<div id="wpbc_settings__form_layout_tab" class="wpbc_settings_navigation_item">
						<a onclick="javascript:wpbc_navigation_click_show_section(this,'#wpbc_settings__form_layout_metabox', '.wpbc_container_hide__on_left_nav_click' );" href="javascript:void(0);">
							<span><?php esc_html_e( 'Form Layout', 'booking' ); ?></span>
							<i class="tooltip_right wpbc_set_nav__right_icon menu_icon icon-1x wpbc_icn_dashboard" data-original-title="<?php echo esc_attr( __( 'Form Layout', 'booking' ) ); ?>"></i>
						</a>
					</div><?php

					$is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
					if ( $is_can ) {

					?><div id="wpbc_settings__form_theme_tab" class="wpbc_settings_navigation_item">
						<a onclick="javascript:wpbc_navigation_click_show_section(this,'#wpbc_settings__form_theme_metabox', '.wpbc_container_hide__on_left_nav_click' );" href="javascript:void(0);">
							<span><?php esc_html_e( 'Color Theme', 'booking' ); ?></span>
							<i class="tooltip_right wpbc_set_nav__right_icon menu_icon icon-1x wpbc_icn_format_color_text 00wpbc-bi-pencil" data-original-title="<?php echo esc_attr( __( 'Color Theme', 'booking' ) ); ?>"></i>
						</a>
					</div>
					<div id="wpbc_settings__form_options_tab" class="wpbc_settings_navigation_item">
						<a onclick="javascript:wpbc_navigation_click_show_section(this,'#wpbc_settings__form_options_metabox', '.wpbc_container_hide__on_left_nav_click' );" href="javascript:void(0);">
							<span><?php esc_html_e( 'Options', 'booking' ); ?></span>
							<i class="tooltip_right wpbc_set_nav__right_icon menu_icon icon-1x wpbc_icn_tune" data-original-title="<?php echo esc_attr( __( 'Options', 'booking' ) ); ?>"></i>
						</a>
					</div><?php

					}
					?><div id="wpbc_settings__form_options_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border0">
						<a onclick="javascript:wpbc_navigation_click_show_section(this,'#wpbc_settings__form_preview_metabox', '.wpbc_container_hide__on_left_nav_click000' );" href="javascript:void(0);">
							<span><?php esc_html_e( 'Preview', 'booking' ); ?></span>
							<i class="tooltip_right wpbc_set_nav__right_icon menu_icon icon-1x wpbc-bi-eye" data-original-title="<?php echo esc_attr( __( 'Preview', 'booking' ) ); ?>"></i>
						</a>
					</div>
					<div id="wpbc_settings__form_options_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item0 wpbc_navigation_top_border">
						<a onclick="javascript:jQuery(this).parents('.wpbc_settings_flex_container_can_collapse').toggleClass('wpbc_container_collapsed');
									jQuery( this ).find('.wpbc_icon_for_collapse').removeClass('wpbc_icn_chevron_left wpbc_icn_chevron_right');
									if (jQuery(this).parents('.wpbc_settings_flex_container_can_collapse').hasClass('wpbc_container_collapsed')){
										jQuery( this ).find('.wpbc_icon_for_collapse').addClass('wpbc_icn_chevron_right');
									} else {
										jQuery( this ).find('.wpbc_icon_for_collapse').addClass('wpbc_icn_chevron_left');
									}
									jQuery( this ).blur();
									"
						   href="javascript:void(0);" style="font-weight: 400;font-size:0.94em;">
							<span><?php esc_html_e( 'Collapse menu', 'booking' ); ?></span>
							<i class="wpbc_set_nav__right_icon menu_icon icon-1x wpbc_icn_chevron_left tooltip_right wpbc_icon_for_collapse"
							   data-original-title="<?php echo esc_attr( __( 'Collapse menu', 'booking' ) ); ?>"
							   style="margin-top:0;"></i>
						</a>
					</div>
				</div>
			</div>
			<?php
		}


		public function content_section__form_fields(){

			// Fields Generator
			?>
			<span class="metabox_wpbc_form_field_free_generator" style="display:none;">
				<div class="clear"></div>
				<span class="metabox-holder">
					<div class="wpbc_settings_row " >
						<?php
						wpbc_open_meta_box_section( 'wpbc_form_field_free_generator', __('Form Field Configuration', 'booking') );

						$this->fields_generator_section();

						wpbc_close_meta_box_section();
						?>
					</div>
				</span>
			</span>
			<?php

			// Content
			?>
			<div class="clear" style="margin-bottom:10px;"></div>
			<div class="metabox-holder">
				<?php

						// Get Form  Fields
						$booking_form_structure = wpbc_simple_form__db__get_visual_form_structure();   // Get saved or Import form  fields from  OLD Free version

						$this->show_booking_form_fields_table( $booking_form_structure );
				?>
			</div>
			<div class="clear" style="height:10px;"></div>
			<?php

		}


		public function content_section__form_layout(){

			$default_options_values = wpbc_get_default_options();
			?><table class="form-table"><?php

			$field_value_or_default = ( empty( get_bk_option( 'booking_form_structure_type' ) ) ? $default_options_values['booking_form_structure_type'] : get_bk_option( 'booking_form_structure_type' ) );

			$field_options = array(                       // Associated array  of titles and values
                                                          'optgroup_sf_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Standard Forms' ,'booking')
                                                                    )
														// FixIn: 10.7.1.7.
                                                        , 'wizard_2columns' => array(
                                                                          'title' => __('Wizard (several steps)', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'wizard_services_a' => array(
                                                                          'title' => __( 'Wizard (Steps Timline)', 'booking' )
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'vertical' => array(
                                                                        'title' => __('Form under calendar', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'form_right' => array(
                                                                        'title' => __('Form at right side of calendar', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'form_center' => array(
                                                                        'title' => __('Form and calendar are centered', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )

                                                        , 'optgroup_sf_e' => array( 'optgroup' => true, 'close'  => true )
//														, 'optgroup_help_tips_s' => array(
//                                                                        'optgroup' => true
//                                                                        , 'close'  => false
//                                                                        , 'title'  => '&nbsp;' . __('Advanced Forms' ,'booking')
//                                                                    )
//                                                        , 'form_2_columns' => array(
//                                                                        'title' => __('2 Columns', 'booking')
//                                                                        , 'id' => ''
//                                                                        , 'name' => ''
//                                                                        , 'style' => ''
//                                                                        , 'class' => ''
//                                                                        , 'disabled' => true
//                                                                        , 'selected' => false
//                                                                        , 'attr' => array()
//                                                                    )
//														, 'optgroup_help_tips_e' => array( 'optgroup' => true, 'close'  => true )

                                                    );

			WPBC_Settings_API::field_select_row_static( 'booking_form_structure_type__select'
									, array(
											  'type'              => 'select'
											, 'title'             =>  __('Form Layout', 'booking')
											, 'label'             => ''
											, 'disabled'          => false
											, 'disabled_options'  => array()
											, 'multiple'          => false

											, 'description'       => ''
											, 'description_tag'   => 'span'

											, 'group'             => 'form_layout'
											, 'tr_class'          => ''
											, 'class'             => ''
											, 'css'               => 'width:auto;max-width:100%;'
											, 'only_field'        => false
											, 'attr'              => array()

											, 'value'             => $field_value_or_default
											, 'options'           => $field_options
											, 'attr' => array( 'onchange' => "javascript: if( 'wizard_2columns' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_width' ).val( '100' ); jQuery( '#booking_form_layout_width_px_pr' ).val( '%' ); jQuery( '#booking_form_layout_max_cols' ).val( '2' ); } "
																			 		  . " if( 'wizard_services_a' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_width' ).val( '100' ); jQuery( '#booking_form_layout_width_px_pr' ).val( '%' ); jQuery( '#booking_form_layout_max_cols' ).val( '2' ); } "
																			 		  . " if( 'vertical' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_max_cols' ).val( '2' ); jQuery( '#booking_form_layout_width' ).val( '100' ); jQuery( '#booking_form_layout_width_px_pr' ).val( '%' ); } "
																			 		  . " if( 'form_right' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_max_cols' ).val( '1' ); jQuery( '#booking_form_layout_width' ).val( '440' ); jQuery( '#booking_form_layout_width_px_pr' ).val( 'px' ); } "
																			 		  . " if( 'form_center' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_max_cols' ).val( '1' ); jQuery( '#booking_form_layout_width' ).val( '440' ); jQuery( '#booking_form_layout_width_px_pr' ).val( 'px' ); } "
															  )
										)
							);


			$field_value_or_default = ( empty( get_bk_option( 'booking_form_layout_max_cols' ) ) ? $default_options_values['booking_form_layout_max_cols'] : get_bk_option( 'booking_form_layout_max_cols' ) );
			WPBC_Settings_API::field_select_row_static( 'booking_form_layout_max_cols'
									, array(
											  'type'              => 'select'
											, 'title'             => __( 'Columns', 'booking' )
											, 'label'             => ''
											, 'disabled'          => false
											, 'disabled_options'  => array()
											, 'multiple'          => false

											, 'description'       => __('Set number of columns in booking form','booking')
											, 'description_tag'   => 'span'

											, 'group'             => 'form_layout'
											, 'tr_class'          => ''
											, 'class'             => ''
											, 'css'               => 'width:auto;max-width:100%;'
											, 'only_field'        => false
											, 'attr'              => array()

											, 'value'             => $field_value_or_default
											, 'options'           => array( 1 => 1, 2 => 2, 3 => 3 )
											//, 'attr' => array( 'onchange' => "javascript: if( 'wizard_2columns' === jQuery( this ).val() ) { jQuery( '#booking_form_layout_width' ).val( '100' ); jQuery( '#booking_form_layout_width_px_pr' ).find( 'option' ).prop( 'selected', false );	jQuery( '#booking_form_layout_width_px_pr' ).val( '%' ); } " )
										)
							);


				$id = 'booking_form_layout';
				?><tr class="wpbc_tr_<?php echo esc_attr( $id ); ?>_size__select">
					<th scope="row" style="vertical-align: middle;">
						<label for="wpbc_booking_width" class="wpbc-form-text"><?php esc_html_e('Form Width:', 'booking'); ?></label>
					</th>
					<td class=""><fieldset style="display: flex;flex-flow: row wrap;justify-content: flex-start;align-items: center;"><?php

						$field_name = 	$id . '_width';			// 100
						WPBC_Settings_API::field_text_row_static( $field_name
															, array(
																	  'type'              => 'text'
																	, 'placeholder'       => '100'
																	, 'class'             => ''
																	, 'css'               => 'width:5em;'
																	, 'only_field'        => true
																	, 'attr'              => array()
																	, 'value' => ( empty( get_bk_option( $field_name ) ) ? $default_options_values[ $field_name ] : get_bk_option( $field_name ) )
																)
											);
						$field_name = 	$id . '_width_px_pr';	// %
						WPBC_Settings_API::field_select_row_static(   $field_name
																	, array(
																			  'type'              => 'select'
																			, 'multiple'          => false
																			, 'class'             => ''
																			, 'css'               => 'width:4em;'
																			, 'only_field'        => true
																			, 'attr'              => array()
																			, 'options'           => array( 'px' => 'px', '%'  => '%' )
																			, 'value' => ( empty( get_bk_option( $field_name ) ) ? $default_options_values[ $field_name ] : get_bk_option( $field_name ) )
																		)
										);
						?><span class="description"> <?php esc_html_e('Set width of calendar' ,'booking'); ?></span></fieldset></td>
				</tr><?php


			?></table>
			<script type="text/javascript">
					jQuery('select[name=\"booking_form_structure_type__select\"]').on( 'change', function(){
						var selected_val = jQuery('#booking_form_structure_type__select').val();
						jQuery('#booking_form_structure_type').val( selected_val );
					} );
			</script><?php
		}


		public function content_section__form_color_theme(){

			$default_options_values = wpbc_get_default_options();

			?><table class="form-table"><?php

				$field_name = 'booking_form_theme';
				$field_params = array(
										  'type'          => 'select'
										, 'default'     => $default_options_values[ $field_name ]   					//'Off'
										, 'title'       => __('Color Theme' ,'booking')
										, 'description' => __('Select a color theme for your booking form that matches the look of your website.' ,'booking')
//														   . '<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info">'
//														   .    __('When you select a color theme, it also change the calendar and time-slot picker skins to match your choice. Customize these options separately as needed.' ,'booking')
//														   .'</div>'
										, 'options' => array(
																''                  => __( 'Light', 'booking' ),
																'wpbc_theme_dark_1' => __( 'Dark', 'booking' )
															)
										, 'group'       => 'form'
										, 'value'       => ( empty( get_bk_option( $field_name ) ) ? $default_options_values[ $field_name ] : get_bk_option( $field_name ) ),
										'attr' => array( 'onchange' => "javascript: wpbc_on_change__form_color_theme( this );" )

				);

				WPBC_Settings_API::field_select_row_static( $field_name, $field_params );

 			?></table><?php
			?><script type="text/javascript">
				function wpbc_on_change__form_color_theme( _this ){
					var wpbc_cal_dark_skin_path;
					if ( 'wpbc_theme_dark_1' == jQuery( _this ).val() ){
						jQuery( '.wpbc_center_preview,.wpbc_container.wpbc_container_booking_form' ).addClass( 'wpbc_theme_dark_1' );
						wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/skins/24_9__dark_1.css';
						jQuery( '#ui_btn_cstm__set_calendar_skin' ).find( 'option' ).prop( 'selected', false );
						jQuery( '#ui_btn_cstm__set_calendar_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
						wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/time_picker_skins/black.css';
						jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option' ).prop( 'selected', false );
						jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
					} else {
						jQuery( '.wpbc_center_preview,.wpbc_container.wpbc_container_booking_form' ).removeClass( 'wpbc_theme_dark_1' );
						wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/skins/25_5__square_1.css';
						jQuery( '#ui_btn_cstm__set_calendar_skin' ).find( 'option' ).prop( 'selected', false );
						jQuery( '#ui_btn_cstm__set_calendar_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
						wpbc_cal_dark_skin_path = '<?php echo esc_url( WPBC_PLUGIN_URL ); ?>/css/time_picker_skins/light__24_8.css';
						jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option' ).prop( 'selected', false );
						jQuery( '#ui_btn_cstm__set_time_picker_skin' ).find( 'option[value="' + wpbc_cal_dark_skin_path + '"]' ).prop( 'selected', true ).trigger( 'change' );
					}
				}
			</script><?php
		}


		public function content_section__calendar_skin(){

			?><script type="text/javascript">
				jQuery( document ).ready( function (){

					// Calendar skin
					var template__var = wp.template( 'wpbc_ajx_widget_change_calendar_skin' );

					jQuery( '.wpbc_widget_color_skins .ui_container' ).append(

								template__var({ 'ajx_cleaned_params': {
																	   'customize_plugin__booking_skin':     '<?php echo esc_js( get_bk_option( 'booking_skin' ) ); ?>',
																	 }
								              })
																	);
				} );
			</script><?php
		}


		public function content_section__time_picker_skin(){

			?><script type="text/javascript">
				jQuery( document ).ready( function (){
					// Time Picker
					var template__var = wp.template( 'wpbc_ajx_widget_change_time_picker' );

					jQuery( '.wpbc_widget_color_skins .ui_container' ).append(

								template__var({ 'ajx_cleaned_params': {
																	   'customize_plugin__time_picker_skin': '<?php echo esc_js( get_bk_option( 'booking_timeslot_picker_skin' ) ); ?>'
																	 }
								              })
																	);
				} );
			</script><?php
		}


		public function content_section__form_options(){

			$default_options_values = wpbc_get_default_options();

			?><table class="form-table"><?php

				$field_name = 'booking_is_use_autofill_4_logged_user';
				$field_params = array(
										'type'          => 'checkbox'
										, 'default'     => $default_options_values[ $field_name ]   					//'Off'
										, 'title'       => __('Auto-fill fields' ,'booking')
										, 'label'       => __('Check the box to activate auto-fill form fields for logged in users.' ,'booking')
										, 'description' => ''
										, 'group'       => 'form'
										, 'value'       => ( empty( get_bk_option( $field_name ) ) ? $default_options_values[ $field_name ] : get_bk_option( $field_name ) )
				);
				WPBC_Settings_API::field_checkbox_row_static( $field_name, $field_params );


				$field_name = 'booking_timeslot_picker';
				$field_params = array(
										  'type'        => 'checkbox'
										, 'default'     => $default_options_values[ $field_name ]   					//'Off'
										, 'title'       => __('Time picker for time slots' ,'booking')
										, 'label'       => __('Show time slots as a time picker instead of a select box.' ,'booking')
										, 'description' => ''
										, 'group'       => 'time_slots'
										, 'tr_class'    => 'wpbc_timeslot_picker'
										, 'value'       => ( empty( get_bk_option( $field_name ) ) ? $default_options_values[ $field_name ] : get_bk_option( $field_name ) )
				);
				WPBC_Settings_API::field_checkbox_row_static( $field_name, $field_params );

 			?></table><?php

			?><div style="display: flex;flex-flow: row wrap;justify-content: flex-end;align-items: center;font-size: 10px;">
				<div style="margin-right: 1em;font-weight: 600;"><?php esc_html_e('Global Settings','booking'); ?>: </div>
				<i style="margin-right: 5px;<?php echo ( get_bk_option( 'booking_is_use_captcha' ) === 'On' ) ? 'color: #036aab' : ''; ?>"
				   class="wpbc_set_nav__right_icon menu_icon icon-1x <?php echo ( get_bk_option( 'booking_is_use_captcha' ) === 'On' ) ? 'wpbc-bi-toggle2-on wpbc_set_nav__icon_on' : 'wpbc-bi-toggle2-off'; ?>"
				></i><a href="<?php echo esc_url( wpbc_get_settings_url() ) . '&tab=form&subtab=form_options#do_expand__wpbc_general_settings_form_metabox#do_other_actions__blink_captcha'; ?>">
					<span ><?php
						esc_html_e( 'CAPTCHA', 'booking' );
						echo ' ';
						echo esc_html( get_bk_option( 'booking_is_use_captcha' ) );
					?></span>
				</a>
			</div><?php
		}


	private function help_notice__show_time_fields_once() {

		?>
		<div class="wpbc-settings-notice notice-error notice-helpful-info0" style="margin: 0 0 30px;">
		<?php
		echo '<strong>';
		esc_html_e( 'Error', 'booking' );
		echo '!</strong>&nbsp;&nbsp;';

			/* translators: 1: ... */
		echo wp_kses_post( __( 'You can either use the time slots field or the start time and duration time fields, but not both. If you choose to use the start time and duration time fields, make sure to use them together.', 'booking' ) );
		?>
		</div>
		<?php
	}

	private function help_notice__show_missed_time_fields() {

		?>
		<div class="wpbc-settings-notice notice-error notice-helpful-info0" style="margin: 0 0 30px;">
		<?php
		echo '<strong>';
		esc_html_e( 'Error', 'booking' );
		echo '!</strong>&nbsp;&nbsp;';

			/* translators: 1: ... */
		echo wp_kses_post( __( 'You must use both the start time field and the duration time field together.', 'booking' ) );
		?>
		</div>
		<?php
	}

	private function is_exist_duplicated_time_fields( $visual_form_structure ) {

		$time_fields_arr = array();
		foreach ( $visual_form_structure as $field_arr ) {
			if (
				( isset( $field_arr['active'] ) ) &&
				( isset( $field_arr['name'] ) ) &&
				( 'on' === strtolower( $field_arr['active'] ) ) &&
				( in_array( $field_arr['name'], array(
					'rangetime',
					'durationtime',
					'starttime',
					'endtime',
				), true ) ) ) {
				$time_fields_arr[] = $field_arr['name'];
			}
		}

		if ( in_array( 'rangetime', $time_fields_arr, true ) ) {

			if ( count( $time_fields_arr ) > 1 ) {
				return 'duplicated_time_fields';
			}

		} else {

			if ( count( $time_fields_arr ) === 1 ) {
				return 'missed_time_fields';
			}
		}

		return 'ok';
	}

    /**
	 * Show Fields Table */
    private function show_booking_form_fields_table( $booking_form_structure ) {
       
        $booking_form_structure = maybe_unserialize( $booking_form_structure );  

        $skip_obligatory_field_types =array();// array( 'calendar', 'submit', 'captcha' );

		$obligatory_field_types = array( 'calendar', 'submit', 'email' );
	    if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {
		    $obligatory_field_types[] = 'captcha';
	    }


		if ( 'duplicated_time_fields' === $this->is_exist_duplicated_time_fields( $booking_form_structure ) ) {
			$this->help_notice__show_time_fields_once();
		}
		if ( 'missed_time_fields' === $this->is_exist_duplicated_time_fields( $booking_form_structure ) ) {
			$this->help_notice__show_missed_time_fields();
		}

        ?><table class="widefat wpbc_input_table sortable wpdevelop wpbc_table_form_free" cellspacing="0" cellpadding="0"> <?php //FixIn: 10.1.2.2 style="flex:0 1 49.9%;"> ?>
            <thead>
                <tr>
                    <th class="sort"><span class="wpbc_icn_swap_vert" aria-hidden="true"></span></th>
                    <th class="field_active"><?php      echo esc_js( __('Active', 'booking') ); ?></th>
                    <th class="field_label"><?php       echo esc_js( __('Field Label', 'booking') ); ?></th>
                    <th class="field_required"><?php    echo esc_js( __('Required', 'booking') ); ?></th>                    
                    <!--th class="field_options"><?php     echo esc_js( __('Type', 'booking') ) . ' | ' . esc_js( __('Name', 'booking') ); ?></th-->
                    <th class="field_actions"><?php     echo esc_js( __('Actions', 'booking') ); ?></th>
                </tr>
            </thead>
            <tbody class="wpbc_form_fields_body">
            <?php 

            $i=0;
            
            foreach ( $booking_form_structure as $form_field ) {
                
                $defaults = array(
                                    'type'     => 'text'
                                  , 'name'     => 'unique_name'
                                  , 'obligatory' => 'Off'
                                  , 'active'   => 'On'
                                  , 'required' => 'Off'
                                  , 'label'    => ''
                                  , 'value'    => ''
                );        
                $form_field = wp_parse_args( $form_field, $defaults );
                                
                if( ! in_array( $form_field['type'], $skip_obligatory_field_types  ) ) {
                    
                    $i++;
                
                    $row = '<tr class="account">';
                    
                    $row .= '<td class="sort"><span class="wpbc_icn_drag_indicator" aria-hidden="true"></span></td>';

					// Flex Toggle
	                if ( 1 ) {
						ob_start();
						$is_checked = ( $form_field['active'] === 'On' );
						$field_id    = 'wpbc_on_off_' . 'active_' . intval( microtime( true ) ) . '_' . wp_rand( 1, 1000 );
						$field_name  = 'form_field_active[' . $i . ']';
						$field_value = esc_attr( $form_field['active'] );
						$params_checkbox = array(
												  'id'       => $field_id 													// HTML ID  of element
												, 'name'     => $field_name
												, 'label'    => array( 'title' => '', 'position' => 'right' )
												, 'toggle_style' => '' 														// CSS of select element
												, 'class'    => 'wpbc_visible_but_out_screen '  							// CSS Class of select element
												, 'disabled' => ''
												, 'attr' => array( 'autocomplete' => 'off' ) 								// Any  additional attributes, if this radio | checkbox element
												, 'legend'   => ''															//wp_kses_post( $field_title )			// aria-label parameter
												, 'value'    => $field_value 												// Some Value from options array that selected by default
												, 'selected' => $is_checked													// Selected or not
												//, 'onchange' 	=> "jQuery( this ).parents('.wpbc_searchable_on_off').find('.wpbc_label_on_off').hide();jQuery( this ).parents('.wpbc_searchable_on_off').find( jQuery( this ).is(':checked') ? '.wpbc_label_on' : '.wpbc_label_off' ).show();"					// JavaScript code
												//, 'onfocus' 	=>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
												//, 'hint' 		=> array( 'title' => __('Send email notification to customer about this operation' ,'booking') , 'position' => 'top' )
											);
						wpbc_flex_toggle( $params_checkbox );
						$flex_toggle = ob_get_clean();
					}

                    $row .= '<td class="field_active"><div class="wpbc_align_vertically">'
                                . ( ( ( $form_field['obligatory'] != 'On' ) && ( ! in_array( $form_field['type'], $obligatory_field_types ) ) )
									? $flex_toggle
								 // ? '<input type="checkbox" name="form_field_active[' . $i . ']" value="' . esc_attr( $form_field['active'] ) . '" ' . checked(  $form_field['active'], 'On' , false ) . ' autocomplete="off" />'
									: '' )
                            
                            .'</div></td>';
                    $row .= '<td class="field_label"><div class="wpbc_align_vertically">'
                                . '<legend class="screen-reader-text"><span>' . esc_attr( $form_field['label'] ) . '</span></legend>'
                                  .'<input  type="text" 
                                        name="form_field_label[' . $i . ']"
                                        value="' . esc_attr( $form_field['label'] ) . '" 
                                        class="regular-text"                                 
                                        placeholder="' . ( ( $form_field['name'] == 'calendar' )  ? __( 'Select date(s)', 'booking' ) : esc_attr( $form_field['label'] ) ) . '" 
                                        autocomplete="off"
                                    /> ';
	                if (
						   ( in_array( $form_field['name'], array( 'calendar', 'submit', 'captcha', 'rangetime', 'durationtime', 'starttime', 'endtime' ), true ) )
						|| ( in_array( $form_field['type'], array( 'calendar' ) ) )
	                ){
						$css_label = 'wpbc_label_approved';
						$css_label = (( $form_field['name'] == 'calendar' ) || ( $form_field['type'] == 'calendar' ))  ? 'wpbc_label_deleted_resource' : $css_label;
						$css_label = ( $form_field['name'] == 'submit' )    ? 'wpbc_label_resource' : $css_label;
						$css_label = ( $form_field['name'] == 'captcha' )   ? 'wpbc_label_pending' : $css_label;
						$css_label = ( in_array( $form_field['name'], array( 'rangetime', 'durationtime', 'starttime', 'endtime' ), true ) ) ? 'wpbc_label_approved' : $css_label;

						$row .= 		'<div class="field_type_name_description"><div class="field_type_name_value wpbc_label '.$css_label.'" style="text-transform: uppercase;padding: 0 1em;">';

		                if ( ( $form_field['name'] == 'calendar' ) ||
							 ( $form_field['type'] == 'calendar' ) ) {			$row .= __( 'Calendar', 'booking' );
		                } else if ( $form_field['name'] == 'rangetime' ) {		$row .= __( 'Time Slots', 'booking' );
		                } else if ( $form_field['name'] == 'durationtime' ) {	$row .= __( 'Duration Time', 'booking' ) . ' / ' . __( 'Service Duration', 'booking' );
		                } else if ( $form_field['name'] == 'starttime' ) {		$row .= __( 'Start Time', 'booking' );
		                } else if ( $form_field['name'] == 'endtime' ) {		$row .= __( 'End Time', 'booking' );
		                } else if ( $form_field['name'] == 'submit' ) {			$row .= $form_field['name'] . ' ' . __( 'Button', 'booking' );
		                } else {												$row .= $form_field['name'];
		                }
						$row .= 		'</div></div>';
	                } else {
						$row .= 		'<div class="field_type_name_description">                                    
											' . __( 'Type', 'booking' ) . ': <div class="field_type_name_value">' . $form_field['type'] . '</div>  
											<span class="field_type_name_separator">|</span>  
											' . __( 'Name', 'booking' ) . ': <div class="field_type_name_value">' . $form_field['name'] . '</div>
										</div>';
	                }
					$row .= 		'<input type="hidden"  value="'. esc_attr( ( 'select' == $form_field['type'] ) ? 'selectbox' : $form_field['type'] ) 	. '"  name="form_field_type[' . $i . ']" autocomplete="off" />'
                                . '<input type="hidden"  value="'. esc_attr( $form_field['name'] ) 	. '"  name="form_field_name[' . $i . ']" autocomplete="off" />'
                                . '<input type="hidden"  value="'. esc_attr( $form_field['value'] ) . '"  name="form_field_value[' . $i . ']" autocomplete="off" />'
                            .'</div></td>';

                    																									//FixIn:  TimeFreeGenerator
                    $is_show_required_checkbox = true;
	                if ( ( $form_field['obligatory'] == 'On' ) || ( in_array( $form_field['type'], $obligatory_field_types ) ) ) {
                    	$is_show_required_checkbox = false;
					}
                    if (  isset( $form_field['if_exist_required'] ) &&  ( $form_field['if_exist_required'] == 'On' )  ) {
                    	$is_show_required_checkbox = false;
					}

					// Flex Toggle
	                if ( 1 ) {
						ob_start();
						$is_checked = ( $form_field['required'] === 'On' );
						$field_id    = 'wpbc_on_off_' . 'required_' . intval( microtime( true ) ) . '_' . wp_rand( 1, 1000 );
						$field_name  = 'form_field_required[' . $i . ']';
						$field_value = esc_attr( $form_field['required'] );
						$params_checkbox = array(
												  'id'       => $field_id 													// HTML ID  of element
												, 'name'     => $field_name
												, 'label'    => array( 'title' => '', 'position' => 'right' )
												, 'toggle_style' => '' 														// CSS of select element
												, 'class'    => 'wpbc_visible_but_out_screen '  							// CSS Class of select element
												, 'disabled' => ''
												, 'attr' => array( 'autocomplete' => 'off' ) 								// Any  additional attributes, if this radio | checkbox element
												, 'legend'   => ''															//wp_kses_post( $field_title )			// aria-label parameter
												, 'value'    => $field_value 												// Some Value from options array that selected by default
												, 'selected' => $is_checked													// Selected or not
												//, 'onchange' 	=> "jQuery( this ).parents('.wpbc_searchable_on_off').find('.wpbc_label_on_off').hide();jQuery( this ).parents('.wpbc_searchable_on_off').find( jQuery( this ).is(':checked') ? '.wpbc_label_on' : '.wpbc_label_off' ).show();"					// JavaScript code
												//, 'onfocus' 	=>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
												//, 'hint' 		=> array( 'title' => __('Send email notification to customer about this operation' ,'booking') , 'position' => 'top' )
											);
						wpbc_flex_toggle( $params_checkbox );
						$flex_toggle = ob_get_clean();
					}
                    $row .= '<td class="field_required"><div class="wpbc_align_vertically">'
                            	. ( $is_show_required_checkbox ? $flex_toggle : '' )
								// ( $is_show_required_checkbox ? '<input    type="checkbox" name="form_field_required[' . $i . ']" value="' . esc_attr( $form_field['required'] ) . '" ' . checked(  $form_field['required'], 'On'  , false ) . ' autocomplete="off" />' : '' )
                            .'</div></td>';
//                    $row .= '<td class="field_options"><div class="wpbc_align_vertically">'
//                                . '<input type="text" disabled="DISABLED" value="'. '' . $form_field['type']. ' | ' . $form_field['name'] . '"  autocomplete="off" />'
//                            .'</div></td>';
                    $row .= '<td class="field_actions">'; 
                    if ( ( $form_field['obligatory'] != 'On' ) && ( ! in_array( $form_field['type'], array( 'captcha' ) ) ) ) {
//TODO: refactor here: 2024-05-31 23:15
						$row .= '<a href="javascript:void(0)" onclick="javascript:wpbc_start_edit_form_field(' . $i . ');" class="tooltip_top button-secondary button" title="'.__('Edit' ,'booking').'"><i class="wpbc_icn_draw"></i></a>';
						$row .= '<a href="javascript:void(0)" class="tooltip_top button-secondary button delete_bk_link" title="'.__('Remove' ,'booking').'"><i class="wpbc_icn_close"></i></a>';
//TODO: refactor here: 2024-05-31 23:15
/*
ob_start();
?>
<div class="wpbc_ajx_toolbar wpbc_no_background" style="margin:0;">
	<div class="ui_container ui_container_small">
		<div class="ui_group ui_group__search_url">
<?php
wpbc_ajx__ui__all_or_new( array(), array('wh_what_bookings'=>'new') );

echo '<div class="ui_element" style="margin:0 5px 0 -10px"><a href="javascript:void(0)" onclick="javascript:wpbc_start_edit_form_field(' . $i . ');" class="wpbc_ui_control wpbc_ui_button wpbc_ui_button tooltip_top " title="'.__('Edit' ,'booking').'"><i class="wpbc_icn_draw"></i></a></div>';
echo '<div class="ui_element"><a href="javascript:void(0)" class="wpbc_ui_control wpbc_ui_button wpbc_ui_button tooltip_top  button delete_bk_link" title="'.__('Remove' ,'booking').'"><i class="wpbc_icn_close"></i></a></div>';

?></div></div></div><?php
$elemnt = ob_get_clean();
$row .= $elemnt;
*/
                    }
                    $row .= '</td>';

					$row .= '</tr>';
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $row;
                }
            }            

            ?>
            </tbody>
            <?php /* ?>
            <tfoot>
                <tr>
                    <th colspan="6">
                        <a href="#" class="remove_rows button"><?php esc_html_e( 'Remove selected field' ,'booking'); ?></a>
                    </th>
                </tr>
            </tfoot>
            <?php  /**/ ?>
        </table><?php  
        
        
    } 

    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css"> 
            /* toolbar fix */
            .wpdevelop .visibility_container .control-group {
                margin: 2px 8px 3px 0;  /* margin: 0 8px 5px 0; */ /* FixIn:  9.5.4.8	*/
            }
            /* Selectbox element in toolbar */
            .visibility_container select optgroup{                            
                color:#999;
                vertical-align: middle;
                font-style: italic;
                font-weight: 400;
            }
            .visibility_container select option {
                padding:5px;
                font-weight: 600;
            }
            .visibility_container select optgroup option{
                padding: 5px 20px;       
                color:#555;
                font-weight: 600;
            }
            #wpbc_create_new_custom_form_name_fields {
                width: 360px;
                display:none;
            }
            @media (max-width: 399px) {
                #wpbc_create_new_custom_form_name_fields {
                    width: 100%;
                }
            }
        </style>
        <?php
		wpbc_timeslots_free_css();																						//FixIn: TimeFreeGenerator
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Generators
    // -----------------------------------------------------------------------------------------------------------------
    /** Sections with Add New Fields forms */
    private function fields_generator_section() {
        ?>
        <div class="wpbc_field_generator wpbc_field_generator_info">
        <?php 
            
            echo
                '<p><strong>' . esc_html__('Shortcodes' ,'booking') . '.</strong> '
                           . wp_kses_post( sprintf(__('You can generate the form fields for your form (at the left side) by selection specific field in the above selectbox.' ,'booking'),'<code><strong>[email* email]</strong></code>'))
                /* translators: 1: ... */
                .'<br/>'   . wp_kses_post( sprintf( __( 'Please read more about the booking form fields configuration %1$shere%2$s.', 'booking' ),'<a href="https://wpbookingcalendar.com/faq/booking-form-fields/" target="_blank">', '</a>') )

                . '</p><p><strong>' . esc_html__('Default Form Templates' ,'booking') . '.</strong> ' .
                             /* translators: 1: ... */
                             wp_kses_post( sprintf( __( 'You can reset your active form template by selecting default %1$sform template%2$s at the top toolbar. Please select the form template and click on %3$sReset%4$s button for resetting only active form (Booking Form or Content of Booking Fields form). Click  on %5$sBoth%6$s button if you want to reset both forms: Booking Form and Content of Booking Fields form.', 'booking' )
                                        ,'<strong>','</strong>'
                                        ,'<strong>','</strong>'
                                        ,'<strong>','</strong>'
                                     ) )
                .'</p>';
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_text">
        <?php 
        
            $this->generate_field(  
                                    'text_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'text' 
                                    )  
                                );            
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_textarea">
        <?php  
        
            $this->generate_field(  
                                    'textarea_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'textarea' 
                                    )  
                                );        
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_select wpbc_field_generator_selectbox">
        <?php 
            $this->generate_field(  
                                    'selectbox_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => true 
                                        , 'type' => 'selectbox'
                                    )  
                                );        
        ?>    
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_checkbox">
        <?php 
        
            $this->generate_field(  
                                    'checkbox_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'checkbox' 
                                    )  
                                );        
        ?>
        </div>
		<?php
																														//FixIn: TimeFreeGenerator
		?>
        <div class="wpbc_field_generator wpbc_field_generator_rangetime">
        <?php

            $this->generate_field(
                                    'rangetime_field_generator'
                                    , array(
                                          'active' 	 => true
                                        , 'required' => true
                                        , 'label' 	 => true
                                        , 'name' 	 => true
                                        , 'value' 	 => true
                                        , 'type' 	 => 'selectbox'

										, 'required_attr' 	=> array( 'disabled' => true
																	, 'value' => 'On'
																)
										, 'label_attr' 		=> array( 'placeholder' => __( 'Time Slots', 'booking' )
																	, 'value' 		=> __( 'Time Slots', 'booking' )
																)
										, 'name_attr' 		=> array( 'disabled' 	=> true
																	, 'placeholder' => 'rangetime'
																	, 'value' 		=> 'rangetime'
																)
										, 'value_attr' 		=> array( 'value' => "10:00 AM - 12:00 PM@@10:00 - 12:00\n12:00 PM - 02:00 PM@@12:00 - 14:00\n13:00 - 14:00\n11:00 - 15:00\n14:00 - 16:00\n16:00 - 18:00\n18:00 - 20:00"
																	, 'attr' => array(
																						'placeholder' => "10:00 AM - 12:00 PM@@10:00 - 12:00\n12:00 PM - 02:00 PM@@12:00 - 14:00\n13:00 - 14:00\n11:00 - 15:00\n14:00 - 16:00\n16:00 - 18:00\n18:00 - 20:00"
																					)
																	, 'rows' => 8
																	, 'cols' => 37
																)
                                    )
                                );
        ?>
        </div>
		<?php

		// FixIn: 10.10.1.4.
		?>
		<div class="wpbc_field_generator wpbc_field_generator_durationtime">
			<?php
			// __( 'Start Time', 'booking' ) . ' / ' . __( 'Duration Time', 'booking' ), .
			$this->generate_field(
				'durationtime_field_generator',
				array(
					'active'        => true,
					'required'      => true,
					'label'         => true,
					'name'          => true,
					'value'         => true,
					'type'          => 'selectbox',
					'required_attr' => array(
						'disabled' => true,
						'value'    => 'On',
					),
					'label_attr'    => array(
						'placeholder' => __( 'Duration Time', 'booking' ),
						'value'       => __( 'Duration Time', 'booking' ),
					),
					'name_attr'     => array(
						'disabled'    => true,
						'placeholder' => 'durationtime',
						'value'       => 'durationtime',
					),
					'value_attr'    => array(
						'value' => "Service A (15 min)@@00:15\nService B (20 min)@@00:20\nService C (30 min)@@00:30\nService D (1 hour)@@01:00\nService E (2 hours)@@02:00",
						'attr'  => array(
							'placeholder' => "Service A (15 min)@@00:15\nService B (20 min)@@00:20\nService C (30 min)@@00:30\nService D (1 hour)@@01:00\nService E (2 hours)@@02:00",
						),
						'rows'  => 8,
						'cols'  => 37,
					),
				)
			);
			?>
		</div>
		<?php


		// FixIn: 10.10.1.5.
		?>
		<div class="wpbc_field_generator wpbc_field_generator_starttime">
			<?php
			// __( 'Start Time', 'booking' ) . ' / ' . __( 'Duration Time', 'booking' ), .
			$this->generate_field(
				'starttime_field_generator',
				array(
					'active'        => true,
					'required'      => true,
					'label'         => true,
					'name'          => true,
					'value'         => true,
					'type'          => 'selectbox',
					'required_attr' => array(
						'disabled' => true,
						'value'    => 'On',
					),
					'label_attr'    => array(
						'placeholder' => __( 'Start Time', 'booking' ),
						'value'       => __( 'Start Time', 'booking' ),
					),
					'name_attr'     => array(
						'disabled'    => true,
						'placeholder' => 'starttime',
						'value'       => 'starttime',
					),
					'value_attr'    => array(
						'value' => "10:00 AM@@10:00\n10:30 AM@@10:30\n11:00 AM@@11:00\n11:30 AM@@11:30\n12:00 PM@@12:00\n12:30 PM@@12:30\n1:00 PM@@13:00\n1:30 PM@@13:30\n2:00 PM@@14:00\n2:30 PM@@14:30\n3:00 PM@@15:00\n3:30 PM@@15:30\n4:00 PM@@16:00\n4:30 PM@@16:30\n5:00 PM@@17:00\n5:30 PM@@17:30\n6:00 PM@@18:00",
						'attr'  => array(
							'placeholder' => "10:00 AM@@10:00\n10:30 AM@@10:30\n11:00 AM@@11:00\n11:30 AM@@11:30\n12:00 PM@@12:00\n12:30 PM@@12:30\n1:00 PM@@13:00\n1:30 PM@@13:30\n2:00 PM@@14:00\n2:30 PM@@14:30\n3:00 PM@@15:00\n3:30 PM@@15:30\n4:00 PM@@16:00\n4:30 PM@@16:30\n5:00 PM@@17:00\n5:30 PM@@17:30\n6:00 PM@@18:00",
						),
						'rows'  => 8,
						'cols'  => 37,
					),
				)
			);
			?>
		</div>
		<?php


		// FixIn: 10.10.1.5.
		?>
		<div class="wpbc_field_generator wpbc_field_generator_endtime">
			<?php
			// __( 'Start Time', 'booking' ) . ' / ' . __( 'Duration Time', 'booking' ), .
			$this->generate_field(
				'endtime_field_generator',
				array(
					'active'        => true,
					'required'      => true,
					'label'         => true,
					'name'          => true,
					'value'         => true,
					'type'          => 'selectbox',
					'required_attr' => array(
						'disabled' => true,
						'value'    => 'On',
					),
					'label_attr'    => array(
						'placeholder' => __( 'End Time', 'booking' ),
						'value'       => __( 'End Time', 'booking' ),
					),
					'name_attr'     => array(
						'disabled'    => true,
						'placeholder' => 'endtime',
						'value'       => 'endtime',
					),
					'value_attr'    => array(
						'value' => "10:00 AM@@10:00\n10:30 AM@@10:30\n11:00 AM@@11:00\n11:30 AM@@11:30\n12:00 PM@@12:00\n12:30 PM@@12:30\n1:00 PM@@13:00\n1:30 PM@@13:30\n2:00 PM@@14:00\n2:30 PM@@14:30\n3:00 PM@@15:00\n3:30 PM@@15:30\n4:00 PM@@16:00\n4:30 PM@@16:30\n5:00 PM@@17:00\n5:30 PM@@17:30\n6:00 PM@@18:00",
						'attr'  => array(
							'placeholder' => "10:00 AM@@10:00\n10:30 AM@@10:30\n11:00 AM@@11:00\n11:30 AM@@11:30\n12:00 PM@@12:00\n12:30 PM@@12:30\n1:00 PM@@13:00\n1:30 PM@@13:30\n2:00 PM@@14:00\n2:30 PM@@14:30\n3:00 PM@@15:00\n3:30 PM@@15:30\n4:00 PM@@16:00\n4:30 PM@@16:30\n5:00 PM@@17:00\n5:30 PM@@17:30\n6:00 PM@@18:00",
						),
						'rows'  => 8,
						'cols'  => 37,
					),
				)
			);
			?>
		</div>
		<?php


		// Close button.
		?>
		<div class="wpbc_field_generator wpbc_field_generator_info_advanced">
			<div class="clear" style="margin-top:20px;"></div>
			<a onclick="javascript:wpbc_hide_fields_generators();" href="javascript:void(0)" style="margin: 0 15px;" class="button button"><i class="menu_icon icon-1x wpbc_icn_visibility_off"></i>&nbsp;
			<?php
				esc_html_e( 'Close', 'booking' );
			?>
			</a>
		</div>
		<?php
	}



	/**
	 * General Fields Generator
	 *
	 * @param $field_name
	 * @param $field_options
	 *
	 * @return void
	 */
	private function generate_field( $field_name = 'some_field_name', $field_options = array()  ) {

			$defaults = array(
						'active'   => true
					  , 'required' => true
					  , 'label'    => true
					  , 'name'     => true
					  , 'value'    => true
																															//FixIn: TimeFreeGenerator 	(inside of form fields edited,  as well)
					  , 'required_attr' => array( 	  'disabled' => false
													, 'value' => 'Off'
											)
					  , 'label_attr' 	=> array( 	  'placeholder' => __('First Name', 'booking')
													, 'value' => ''
											)
					  , 'name_attr' 	=> array( 	  'disabled' => false
													, 'placeholder' => 'first_name'
													, 'value' => ''
											)
					  , 'value_attr' 	=> array( 	  'value' => ''
													, 'attr' => array( 'placeholder' => "1\n2\n3\n4" )
													, 'rows' => 8
													, 'cols' => 37
											)
					  );
			$field_options = wp_parse_args( $field_options, $defaults );

			?><table class="form-table"><?php

			if ( $field_options['active'] )
				WPBC_Settings_API::field_checkbox_row_static(   $field_name . '_active'
															, array(
																	'type'              => 'checkbox'
																	, 'title'             => __('Active', 'booking')
																	, 'label'             => __('Show / hide field in booking form', 'booking')
																	, 'disabled'          => false
																	, 'class'             => ''
																	, 'css'               => ''
																	, 'type'              => 'checkbox'
																	, 'description'       => ''
																	, 'attr'              => array()
																	, 'group'             => 'general'
																	, 'tr_class'          => ''
																	, 'only_field'        => false
																	, 'is_new_line'       => true
																	, 'description_tag'   => 'span'
																	, 'value' => 'On'
															)
															, true
														);
			if ( $field_options['required'] )
				WPBC_Settings_API::field_checkbox_row_static(   $field_name . '_required'
															, array(
																	'type'              => 'checkbox'
																	, 'title'             => __('Required', 'booking')
																	, 'label'             => __('Set field as required', 'booking')
																	, 'disabled'          => $field_options[ 'required_attr' ][ 'disabled' ]				//false
																	, 'class'             => ''
																	, 'css'               => ''
																	, 'type'              => 'checkbox'
																	, 'description'       => ''
																	, 'attr'              => array()
																	, 'group'             => 'general'
																	, 'tr_class'          => ''
																	, 'only_field'        => false
																	, 'is_new_line'       => true
																	, 'description_tag'   => 'span'
																	, 'value' 			  => $field_options[ 'required_attr' ][ 'value' ]				//'Off'
															)
															, true
														);
			if ( $field_options['label'] )
				WPBC_Settings_API::field_text_row_static(   $field_name . '_label'
															, array(
																	'type'                => 'text'
																	, 'title'             => __('Label', 'booking')
																	, 'disabled'          => false
																	, 'class'             => ''
																	, 'css'               => ''
																	, 'placeholder'       => $field_options[ 'label_attr' ][ 'placeholder' ]				//'First Name'
																	, 'description'       => ''//__('Enter field label', 'booking')
																	, 'group'             => 'general'
																	, 'tr_class'          => ''
																	, 'only_field'        => false
																	, 'description_tag'   => 'p'
																	, 'value' 			  => $field_options[ 'label_attr' ][ 'value' ]				//''
																	, 'attr'              => array(
																		  'oninput'   => "javascript:this.onchange();"
																		, 'onpaste'   => "javascript:this.onchange();"
																		, 'onkeypress'=> "javascript:this.onchange();"
																		, 'onchange'  => "javascript:if ( ! jQuery('#".$field_name . '_name'."').is(':disabled') ) { jQuery('#".$field_name . '_name'."').val(jQuery(this).val() );} wpbc_check_typed_name('".$field_name."');"
																	)
															)
															, true
														);
			if ( $field_options['name'] )
				WPBC_Settings_API::field_text_row_static(   $field_name . '_name'
															, array(
																	'type'              => 'text'
																	, 'title'             => __('Name', 'booking') . '  *'
																	, 'disabled'          => $field_options[ 'name_attr' ][ 'disabled' ]				//false
																	, 'class'             => ''
																	, 'css'               => ''
																	, 'placeholder'       => $field_options[ 'name_attr' ][ 'placeholder' ]				//'first_name'
																	/* translators: 1: ... */
																	, 'description'       => sprintf( __( 'Type only %1$sunique field name%2$s, that is not using in form', 'booking' ), '<strong>', '</strong>' )
																	, 'group'             => 'general'
																	, 'tr_class'          => ''
																	, 'only_field'        => false
																	, 'description_tag'   => 'p'
																	, 'value' 			  => $field_options[ 'name_attr' ][ 'value' ]					//''
																	, 'attr'              => array(
																		  'oninput'   => "javascript:this.onchange();"
																		, 'onpaste'   => "javascript:this.onchange();"
																		, 'onkeypress'=> "javascript:this.onchange();"
																		, 'onchange'  => "javascript:wpbc_check_typed_name('".$field_name."');"

																	)

															)
															, true
														);
			if ( $field_options['value'] )
				WPBC_Settings_API::field_textarea_row_static(   $field_name . '_value'
															, array(

																	 'title'             => __('Values', 'booking')
																	, 'disabled'          => false
																	, 'class'             => ''
																	, 'css'               => ''
																	, 'placeholder'       => ''
																	, 'description'       => __('Enter dropdown options. One option per line.', 'booking') //. '<br>'
																			 //. (( 'durationtime_field_generator' !== $field_options['name'] ) ? '' : __( 'The option format is "Title@@Time Duration," where "Title" is usually the service name and "Time Duration" is defined in the format HH:MM (HH = hours from 00 to 23, MM = minutes from 00 to 59).', 'booking' ))
																			 //. (( 'starttime_field_generator' !== $field_options['name'] ) ? '' : __( 'The option format is "Title@@Time," where "Title" is any text (typically the time in AM/PM format) and "Time" is defined in the 24-hour format (HH:MM), where HH = hours (00 to 23) and MM = minutes (00 to 59).', 'booking' ))
																	, 'group'             => 'general'
																	, 'tr_class'          => ''
																	, 'only_field'        => false
																	, 'description_tag'   => 'p'
																	, 'value' 			  => $field_options[ 'value_attr' ][ 'value' ]					// ''
																	, 'attr'              => $field_options[ 'value_attr' ][ 'attr' ]					//array( 'placeholder' => "1\n2\n3\n4" )   //Override Placeholder value, because of escaping \n symbols
																	, 'rows'              => $field_options[ 'value_attr' ][ 'rows' ]					//2
																	, 'cols'              => $field_options[ 'value_attr' ][ 'cols' ]					//37
																	, 'show_in_2_cols'    => false
																	, 'attr'              => array(
																		  'oninput'   => "javascript:this.onchange();"
																		, 'onpaste'   => "javascript:this.onchange();"
																		, 'onkeypress'=> "javascript:this.onchange();"
																		, 'onchange'  => "javascript:wpbc_check_typed_values('".$field_name."');"
																	)
															)
															, true
														);
				do_action( 'wpbc_settings_form_page_after_values', $field_name, $field_options );                            //FixIn: TimeFreeGenerator

				?>
				<tr><th colspan="2" style="border-bottom:1px solid #eee;padding:10px 0 0;"></th></tr>

				<tr class="wpbc_add_field_row">
					<th colspan="2" class="wpdevelop">
						<a onclick="javascript:wpbc_add_field ( '<?php echo esc_attr( $field_name ); ?>', '<?php echo esc_attr( $field_options['type'] ); ?>' );"
						   href="javascript:void(0)"
						   style=""
						   class="button button-primary"><i class="menu_icon icon-1x wpbc_icn_add _circle_outline"></i>&nbsp;&nbsp;<?php esc_html_e( 'Add New Field' ,'booking'); ?></a>
						&nbsp;&nbsp;
						<a onclick="javascript:wpbc_hide_fields_generators();"
						   href="javascript:void(0)"
						   style=""
						   class="button button"><i class="menu_icon icon-1x wpbc_icn_close"></i>&nbsp;&nbsp;<?php esc_html_e( 'Cancel' ,'booking'); ?></a>
					</th>
				</tr>

				<tr class="wpbc_edit_field_row">
					<th colspan="2" class="wpdevelop">
						<a onclick="javascript:wpbc_finish_edit_form_field ( '<?php echo esc_attr( $field_name ); ?>', '<?php echo esc_attr( $field_options['type'] ); ?>' );"
						   href="javascript:void(0)"
						   style=""
						   class="button button-primary"><i class="menu_icon icon-1x wpbc_icn_draw"></i>&nbsp;&nbsp;<?php esc_html_e( 'Save Changes' ,'booking'); ?></a>
						&nbsp;&nbsp;
						<a onclick="javascript:wpbc_hide_fields_generators();"
						   href="javascript:void(0)"
						   style=""
						   class="button button"><i class="menu_icon icon-1x wpbc_icn_close"></i>&nbsp;&nbsp;<?php esc_html_e( 'Cancel' ,'booking'); ?></a>
					</th>
				</tr>

			</table><?php
		}


    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Activate | Deactivate " >    
    
    public function activate() {
        
        add_bk_option( 'booking_form',          wpbc_simple_form__get_booking_form__as_shortcodes() );
        add_bk_option( 'booking_form_show',     wpbc_simple_form__get_form_show__as_shortcodes() );
        add_bk_option( 'booking_form_visual',   wpbc_simple_form__visual__get_default_form() );
    }
    
    public function deactivate() {
        
        delete_bk_option( 'booking_form' );
        delete_bk_option( 'booking_form_show' );
        delete_bk_option( 'booking_form_visual');
    }

    //                                                                              </editor-fold>
}

add_action('wpbc_menu_created', array( new WPBC_Page_SettingsFormFieldsFree() , '__construct') );    // Executed after creation of Menu



// =====================================================================================================================
//  Load JS files at  specific pages only
// =====================================================================================================================

/**
 * Load JS files.
 *
 * @param $hook        'post.php' | 'wp-booking-calendar3_page_wpbc-resources'
 *
 * @return void
 */
function wpbc_register_js__page_form_simple( $hook ) {

	if (
		( isset( $_REQUEST['page'] ) ) && ( 'wpbc-settings' === $_REQUEST['page'] )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		&& ( isset( $_REQUEST['tab'] ) ) && ( 'form' === $_REQUEST['tab'] )          // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	) {
		// wpbc_load_js__required_for_modals();
		// wpbc_load_js__required_for_media_upload();
		wp_enqueue_script( 'wpbc_simple_form', wpbc_plugin_url( '/includes/page-form-simple/_src/wpbc_simple_form.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
	}
}

add_action( 'admin_enqueue_scripts', 'wpbc_register_js__page_form_simple' );
