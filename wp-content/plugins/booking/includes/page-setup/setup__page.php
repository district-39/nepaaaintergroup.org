<?php
/*
* @package: AJX_Setup_Wizard Page
* @category: Initial  setup  and plugin  customization
* Author: wpdevelop, oplugins
* Version: 1.0
* @modified 2024-06-28
*/
// FixIn: 10.2.0.1.
if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/**
 *  ==  How to add a new step ?  ==
 *
 *  1. Create and include (bellow) a new files:
 * 							require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/form_structure__tpl.php' );
 * 							require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/form_structure__action.php' );
 *
 *  2) ../includes/page-setup/setup_steps.php
 *  	- Add a new step  structure    in 	init_steps_data()
 *
 *  3)../includes/page-setup/setup_ajax.php
 *  	- Add validation  option 'save_and_continue__form_structure'   in 	wpbc_setup_wizard_page__request_rules_structure()
 *  	- Add save action in ajax_WPBC_AJX_SETUP_WIZARD_PAGE :
 *                                                            case 'save_and_continue__form_structure': $
 *                                                                if ( isset( $_POST['all_ajx_params']['step_data'] ) && ( ! empty( $_POST['all_ajx_params']['step_data'] ) ) ) {
 *                                                                    $cleaned_data = wpbc_template__form_structure__action_validate_data( $_POST['all_ajx_params']['step_data'] );
 *                                                                    wpbc_setup__update__form_structure( $cleaned_data );
 *                                                                }
 *                                                                $setup_steps->db__set_step_as_completed( 'form_structure' );
 *                                                                break;
 * 		- Optional. Add loading booking form:
 * 																switch ( $cleaned_request_params['current_step'] ) {
 *																	case 'form_structure':
 *  4) ../includes/page-setup/setup_templates.php
 *  - Add loading template in function wpbc_template__stp_wiz__main_content() {
 * 									 									case 'form_structure':
 * 																			template__main_section = wp.template( 'wpbc_stp_wiz__template__form_structure' );
 * 																			break;
 *  - include template in function hook__load_templates_at_footer( $page ):
 * 																		wpbc_stp_wiz__template__form_structure();
 */


require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/setup_templates.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/01.welcome__tpl.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/02.general_info__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/02.general_info__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/03.date_time_formats__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/03.date_time_formats__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/04.bookings_types__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/04.bookings_types__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/05.form_structure__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/05.form_structure__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/06.cal_availability__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/06.cal_availability__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/07.color_theme__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/07.color_theme__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/08.optional_other_settings__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/08.optional_other_settings__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/09.wizard_publish__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/09.wizard_publish__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/10.get_started__tpl.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/templates/10.get_started__action.php' );

require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/setup_steps.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/setup_ajax.php' );
require_once( WPBC_PLUGIN_DIR . '/includes/page-setup/setup_support.php' );

/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_AJX_Setup_Wizard extends WPBC_Page_Structure {

	private $is_full_screen = true;

   	public function __construct() {

	    parent::__construct();

		add_filter( 'admin_body_class', array( $this, 'add_loading_classes' ) );
    }


    public function in_page() {
        return 'wpbc-setup';
    }


    public function tabs() {

        $tabs = array();
        $tabs[ 'step_01' ] = array(
			'left_navigation__default_view_mode' => 'none',                               // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
                              'title'		=> __( 'Setup', 'booking' )						// Title of TAB
                            , 'hint'		=> false//__( 'Setup', 'booking' ) . ' - '	 . 'Booking Calendar'					// Hint
                            , 'page_title'	=> false//__( 'Setup', 'booking' ) . ' - '	 . 'Booking Calendar'					// Title of Page
                            , 'link'		=> ''								// Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position'	=> ''                               // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon'		=> ''                               // Icon - link to the real PNG img
                            , 'font_icon'	=> 'wpbc_icn_donut_large'	//'wpbc_icn_free_cancellation'		// CSS definition  of forn Icon
                            , 'default'		=> true								// Is this tab activated by default or not: true || false.
                            , 'disabled'	=> false                            // Is this tab disbaled: true || false.
                            , 'hided'		=> false                            // Is this tab hided: true || false.
                            , 'subtabs'		=> array()
        );

        $subtabs = array();
        $subtabs['path_setup'] = array(
                              'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title'     => __( 'Setup', 'booking' )						// Title of TAB
                            , 'page_title'=> false//__( 'Setup', 'booking' ) . ' - '	 . 'Booking Calendar'					// Title of Page
                            , 'hint'      => false//__( 'Setup', 'booking' ) . ' - '	 . 'Booking Calendar'					// Hint
                            , 'link' => '#path_setup'                                      								// link
                            , 'position' => ''                                  	// 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               	// CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 	// Icon - link to the real PNG img
                            //, 'font_icon' => 'wpbc_icn_mail_outline'   			// CSS definition of Font Icon
                                                        , 'default' 	=> true                                	// Is this sub tab activated by default or not: true || false.
                            , 'disabled' 	=> false                               	// Is this sub tab deactivated: true || false.
                            , 'checkbox'  	=> false                              	// or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' 	=> 'content'                            // Function to load as content of this TAB
							, 'is_use_navigation_path' 	=> array(
										'path' => array(
											'go_back_to_dashboard' => array(
																'title'  => __('Go to Dashboard','booking'),
																'hint'   => __('Go back to the Dashboard','booking'),
																'icon'   => 'wpbc_icn_navigate_before',
																'url'    => wpbc_get_bookings_url(),	//wpbc_get_settings_url(),
																'attr'   => array( 'style' => 'border-right: 1px solid #0000001a; margin-right: 10px;padding-right: 25px;' .
																							  ( !$this->is_full_screen ? 'display:none;' : '' ) ),
																'class'  => 'wpbc_full_screen_mode_buttons'
															),
											// 'go_back_separtor' => array( 'tag' => '|' ),   //array( 'tag' => '>' ),
											'setup' => array(
																'title'  => __('Setup','booking'),
																'hint'   => __('Initial Setup','booking'),
																'icon'   => 'wpbc_icn_donut_large wpbc_spin wpbc_animation_pause',		// -> wpbc_setup_wizard_page_reload_button__spin_start( ... )
																'attr'   => array( 'style' => 'pointer-events: none;', 'id' => 'wpbc_initial_setup_top_menu_item' ),
																'url'    => '',
																'tag' => 'div',
																'class'  => 'nav-tab-active'
															),
											//		'next_all' => array( 'tag' => '>' ),
											//		'intro' => array(
											//							'title'  => __('Step','booking') . ' ' . wpbc_setup_wizard_page__get_active_step() . ' / ' .wpbc_setup_wizard_page__get_total_steps(),
											//							'hint'   => __('General Settings','booking'),
											//							'icon'   => 'wpbc_icn_layers outlined_flag',//'wpbc_icn_adjust',
											//							'url'    => wpbc_get_setup_wizard_page_url() . '&tab=step_01',
											//							//'attr'   => array()
											//							//'tag'    => 'a',
											//							'class'  => 'nav-tab-active'
											//						),
											'wpbc__container_place__steps_for_timeline' => array(
																'title'  => '',
																'hint'   => '',
																'icon'   => '',
																'action' => '',
																'attr'   => array( 'style' => '' ),
																'tag'    => 'div',
																'class'  => 'wpbc__container_place__steps_for_timeline'
															),
											'full_screen' => array(
																'title'  => '',//__('Full Screen','booking'),
																'hint'   => __('Full Screen','booking'),
																'icon'   => 'wpbc-bi-arrows-fullscreen 0wpbc_icn_zoom_out_map  0wpbc_icn_open_in_full',
																'action' => "jQuery('body').toggleClass('wpbc_admin_full_screen');wpbc_check_full_screen_mode();jQuery('.wpbc_full_screen_mode_buttons').toggle();",
																'attr'   => array( 'style' => 'margin-left:auto;' . ( $this->is_full_screen ? 'display:none;' : '' ) ),
																//'tag'    => 'a',
																'class'  => 'wpbc_full_screen_mode_buttons'
															),
											'full_screen_exit' => array(
																'title'  => '',
																'hint'   => __('Exit Full Screen','booking'),
																'icon'   => 'wpbc-bi-arrows-angle-contract 0wpbc_icn_zoom_in_map  0wpbc_icn_close_fullscreen',
																'action' => "jQuery('body').toggleClass('wpbc_admin_full_screen');wpbc_check_full_screen_mode();jQuery('.wpbc_full_screen_mode_buttons').toggle();",
																'attr' => array( 'style' => 'margin-left:auto;' . ( !$this->is_full_screen ? 'display:none;' : '' ) ),
																//'tag'    => 'a',
																'class'  => 'wpbc_full_screen_mode_buttons'
															)
										)
									)
                        );
         $tabs[ 'step_01' ][ 'subtabs' ] = $subtabs;

        return $tabs;
    }


    public function content() {

        do_action( 'wpbc_hook_settings_page_header', 'page_booking_setup_wizard');										// Define Notices Section and show some static messages, if needed.

		// -------------------------------------------------------------------------------------------------------------
		// Check MultiUser params
		// -------------------------------------------------------------------------------------------------------------
	    if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) {  return false;  }  									// Check if MU user activated, otherwise show Warning message.
 		// if ( ! wpbc_set_default_resource_to__get() ) return false;                  									// Define default booking resources for $_GET  and  check if booking resource belong to user.


		// -------------------------------------------------------------------------------------------------------------
		// Get and escape request parameters
		// -------------------------------------------------------------------------------------------------------------
       	$escaped_request_params_arr =  wpbc_setup_wizard_page__get_cleaned_params__saved_request_default();


		// -------------------------------------------------------------------------------------------------------------
		// Main Submit Form  (if needed ?)
		// -------------------------------------------------------------------------------------------------------------
		$submit_form_name = 'wpbc_setup_wizard_page_form';                             									// Define form name
		?><form  name="<?php echo esc_attr( $submit_form_name ); ?>" id="<?php echo esc_attr( $submit_form_name ); ?>" action="" method="post" >
			<?php
			   // N o n c e   field, and key for checking   S u b m i t
			   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
			?><input type="hidden" name="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" id="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" value="1" /><?php

		?></form><?php

		// -------------------------------------------------------------------------------------------------------------
		// JS :: Tooltips, Popover, Datepicker
		// -------------------------------------------------------------------------------------------------------------
		wpbc_js_for_bookings_page();

		?><div id="wpbc_log_screen" class="wpbc_log_screen"></div><?php

		?><div class="wpbc_page_publish_notice_section"><?php
			// This is required for submit of Embed shortcodes into  New or Existing pages !
			do_action( 'wpbc_hook_settings_page_before_content_table', 'resources' );      									// Define Notices Section and show some static messages, if needed
		?></div><?php
		// -------------------------------------------------------------------------------------------------------------
        // ==  Content  ==
		// -------------------------------------------------------------------------------------------------------------
		$this->show__main_container( $escaped_request_params_arr );

		//wpbc_show_wpbc_footer();																						// Rating

        do_action( 'wpbc_hook_settings_page_footer', 'wpbc-ajx_booking_setup_wizard' );
    }


	private function show__main_container( $escaped_request_params_arr ) {

		wpbc_clear_div();
		?>
		<span class="metabox-holder">
			<div id="ajx_nonce_calendar_section"></div>
			<div class="wpbc_setup_wizard_page_container" wpbc_loaded="first_time">
				<div class="wpbc_calendar_loading"><span class="wpbc_icn_autorenew wpbc_spin"></span>&nbsp;&nbsp;<span><?php esc_html_e( 'Loading', 'booking' ); ?>...</span></div>
			</div>
		</span>
		<?php

		wpbc_clear_div();

		?><script type="text/javascript"><?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpbc_jq_ready_start();
			if( $this->is_full_screen ) {
			?>
				jQuery( '.wpbc_ui__top_nav__btn_full_screen,.wpbc_ui__top_nav__btn_normal_screen' ).toggleClass( 'wpbc_ui__hide' );
				wpbc_check_full_screen_mode();
			<?php
			}
			?>

			// Set Security - Nonce for Ajax  - Listing
			_wpbc_settings.set_param__secure( 'nonce',   '<?php echo esc_js( wp_create_nonce( 'wpbc_setup_wizard_page_ajx' . '_wpbcnonce' ) ); ?>' );
			_wpbc_settings.set_param__secure( 'user_id', '<?php echo esc_js( wpbc_get_current_user_id() ); ?>' );
			_wpbc_settings.set_param__secure( 'locale',  '<?php echo esc_js( get_user_locale() ); ?>' );

			// Set other parameters
			_wpbc_settings.set_param__other( 'container__main_content', '.wpbc_setup_wizard_page_container' );

			// Send Ajax and then show content
			wpbc_ajx__setup_wizard_page__send_request_with_params( <?php echo wp_json_encode( $escaped_request_params_arr ); ?> );
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpbc_jq_ready_end();
		?></script><?php

		/**
		 *   JS Examples of showing specific Step:
		 * 												wpbc_ajx__setup_wizard_page__send_request_with_params( { 'current_step':'optional_other_settings' } );
		 *
		 * 												wpbc_ajx__setup_wizard_page__send_request_with_params( { 'current_step':'general_info' } );
		 */
	}


	/**
	 * Set the admin full screen class
	 *
	 * @param bool $classes Body classes.
	 * @return array
	 */
	public function add_loading_classes( $classes ) {

		if ( ( wpbc_is_setup_wizard_page() ) && ( $this->is_full_screen ) ) {
			$classes .= ' wpbc_admin_full_screen';
		}

		return $classes;
	}
}
add_action('wpbc_menu_created', array( new WPBC_Page_AJX_Setup_Wizard() , '__construct') );    // Executed after creation of Menu


/**
 * Make Setup Status Reset or Complete from  URL
 *
 * @return bool
 */
function wpbc_setup_wizard_page__force_in_get() {

	// Se it as DONE
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_REQUEST['wpbc_setup_wizard'] ) && 'completed' === $_REQUEST['wpbc_setup_wizard'] ) {

		$setup_steps = new WPBC_SETUP_WIZARD_STEPS();
		$setup_steps->db__set_all_steps_as( true );

		return true;
	}

	// Reset
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_REQUEST['wpbc_setup_wizard'] ) && 'reset' === $_REQUEST['wpbc_setup_wizard'] ) {

		// -------------------------------------------------------------------------------------------------------------
		// ==  Request  ==          ->  $_REQUEST['all_ajx_params']['page_num'],   $_REQUEST['all_ajx_params']['page_items_count'], ...
		// -------------------------------------------------------------------------------------------------------------
		$user_request = new WPBC_AJX__REQUEST( array(
												   'db_option_name'          => 'booking_setup_wizard_page_request_params',
												   'user_id'                 => wpbc_get_current_user_id(),
												   'request_rules_structure' => wpbc_setup_wizard_page__request_rules_structure()
												)
						);
		// Delete from DB
		$is_reseted = $user_request->user_request_params__db_delete();

		// Clear All Steps      Mark as Undone
		$setup_steps = new WPBC_SETUP_WIZARD_STEPS();
		$setup_steps->db__set_all_steps_as( false );

		return true;
	}

	return false;
}
add_action( 'init', 'wpbc_setup_wizard_page__force_in_get' );