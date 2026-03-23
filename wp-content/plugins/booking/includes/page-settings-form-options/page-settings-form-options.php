<?php
/**
 * Form Options Settings page.
 *
 * @version     1.0
 * @package     Booking > Settings > Booking Form > Form Options
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com
 * @modified    2025-05-06
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit if accessed directly.
}


/** API  for  Settings_Form_Options  */
class WPBC_API_Settings_Form_Options extends WPBC_Settings_API {


	/**
	 * Override Settings API Constructor
	 * During creation,  system try to load values from DB, if exist.
	 *
	 * @param string $id - of Settings.
	 */
	public function __construct( $id = '' ) {

		$options = array(
			'db_prefix_option' => '',                                // 'booking_'
			'db_saving_type'   => 'separate',
			'id'               => 'set_gen',
		);

		$id = empty( $id ) ? $options['id'] : $id;

		parent::__construct( $id, $options );                                                                           // Define ID of Setting page and options.

		add_action( 'wpbc_after_settings_content', array( $this, 'enqueue_js' ), 10, 3 );
	}


	/**
	 * Init all fields rows for settings page
	 */
	public function init_settings_fields() {

		$this->fields = array();

		$default_options_values = wpbc_get_default_options();

		$is_use_code_mirror = ( function_exists( 'wpbc_codemirror' ) ) && ( is_user_logged_in() && 'false' !== wp_get_current_user()->syntax_highlighting );

		$this->fields = array();

		if ( defined( 'WPBC_NEW_FORM_BUILDER' ) && WPBC_NEW_FORM_BUILDER ) {
			$this->fields['booking_use_bfb_form'] = array(
									'type'          => 'checkbox'
									, 'default'     => $default_options_values['booking_use_bfb_form']         // 'Off'
									, 'title'       =>  __('Drag & Drop Booking Form Builder' ,'booking') .
														'<span class="wpbc_new_label" style="padding: 3px 4px;margin: 0 10px;">' . esc_html__( 'New', 'booking' ) . '</span>' .
									 					'<span class="wpbc_new_label" style="padding: 3px 4px;background: #e18900;text-transform: none;">Beta</span> '
									, 'label'       => __('Enable the new drag & drop booking form builder.' ,'booking')
									, 'description' => __( 'Enables the new drag & drop form builder. Disable this option to use the classic booking form.', 'booking' )
									, 'group'       => 'form'
				);
		}

		// FixIn: 8.7.11.10.
	    $this->fields['booking_timeslot_picker'] = array(
	                            'type'          => 'checkbox'
	                            , 'default'     => $default_options_values['booking_timeslot_picker']   //'Off'
	                            , 'title'       => __('Time picker for time slots' ,'booking')
	                            , 'label'       => __('Show time slots as a time picker instead of a select box.' ,'booking')
								, 'description' =>  '<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info" style="margin-top:-10px;">'
														. '<strong class="alert-heading">' . esc_html__( 'Note', 'booking' ) . '!</strong> '
														/* translators: 1: ... */
														. sprintf( __( 'You can enable or disable, as well as configure %1$sTime Slots%2$s for your booking form on the Settings > %3$sBooking Form%4$s page.', 'booking' ),
																'<strong>', '</strong>',
																'<strong><a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=form' ) . '">', '</a></strong>'
															)
												   .'</div>'

	                            , 'group'       => 'form'
	                            , 'tr_class'    => 'wpbc_timeslot_picker'
	        );

        $this->fields['booking_is_use_captcha'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_captcha']           //'Off'
                                , 'title'       => __('CAPTCHA' ,'booking')
                                , 'label'       => __('Check the box to activate CAPTCHA inside the booking form.' ,'booking')
								, 'description' =>  '<div class="wpbc-general-settings-notice wpbc-settings-notice notice-warning" style="margin-top:-10px;">'
												   .  '<strong>' . esc_html__('Note' ,'booking') . '!</strong> ' .
								                    __( 'If your website uses a cache plugin or system, exclude pages with booking forms from caching to ensure CAPTCHA functions correctly.', 'booking' )
												   .'</div>'
                                , 'group'       => 'form'
            );
        $this->fields['booking_is_use_autofill_4_logged_user'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_autofill_4_logged_user']         // 'Off'
                                , 'title'       => __('Auto-fill fields' ,'booking')
                                , 'label'       => __('Check the box to activate auto-fill form fields for logged in users.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );
		// FixIn: 10.13.1.5.
        $this->fields['booking_is_use_phone_validation'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_phone_validation']         // 'Off'
                                , 'title'       => __('Smart Phone Validation' ,'booking')
								 /* translators: 1: Phone number format. */
                                , 'label'       => sprintf( __('Enable option to ensure phone number fields are validated according to the user\'s country format %1s' ,'booking'), '+1 000 000 0000' )
                                , 'description' => ''
                                , 'group'       => 'form'
            );

		$this->fields['hr_calendar_after_autofill'] = array( 'type' => 'hr', 'group' => 'form' );

		if ( class_exists( 'wpdev_bk_personal' ) )                                                                      // FixIn: 8.1.1.12.
            $this->fields['booking_is_use_simple_booking_form'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_simple_booking_form']        //'Off'
                                , 'title'       => __('Simple' ,'booking') . ' ' . __('Booking Form', 'booking')
                                , 'label'       => __('Check the box, if you want to use simple booking form customization from Free plugin version at Settings - Form page.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );
		if ( class_exists( 'wpdev_bk_personal' ) )                                                                      // FixIn: 8.1.1.12.
            $this->fields['booking_is_use_codehighlighter_booking_form'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_codehighlighter_booking_form']        //'Off'
                                , 'title'       => __('Syntax highlighter' ,'booking')
                                , 'label'       => __('Check the box, if you want to use syntax highlighter during customization booking form.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );

		// FixIn: 10.0.0.31.
	    if ( class_exists( 'wpdev_bk_biz_m' ) ){
		   $this->fields['booking_number_for_pre_checkin_date_hint'] = array(
		                                    'type'          => 'select'
		                                    , 'default'     => $default_options_values['booking_number_for_pre_checkin_date_hint']                                  //'0'
		                                    , 'title'       => __( 'Pre-Check-in Display Duration', 'booking' )
		                                    /* translators: 1: ... */
		                                    , 'description' => sprintf( __( 'Select the number of days for the %s shortcode.', 'booking' ), '<strong>[pre_checkin_date_hint]</strong>' )
		                                                     . ' '
		                                                     /* translators: 1: ... */
		                                                     . sprintf( __( 'This shortcode is used in the booking form to display the date %1$sN days before%2$s the selected check-in date.', 'booking' )
 					                                                    , '<a href="'. esc_url( wpbc_get_settings_url() ) . '&tab=form">', '</a>' )
					                                                  //, '<a href="'.wpbc_get_settings_url( true, false ) . '&scroll_to_section=wpbc_general_settings_form_tab">', '</a>' )
		                                    , 'options'     => array_combine( range( 1, 91 ), range( 1, 91 ) )
		                                    , 'group'       => 'form'
		                            );
		}

		/*
        $this->fields['booking_search_form_show'] = array(
                                      'type'        => ( $is_use_code_mirror ? 'textarea' : 'wp_textarea' )				// FixIn: 8.4.7.18.
                                    , 'default'     => ''
                                    , 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => ''
                                    , 'description_tag' => ''
                                    , 'css'         => ''
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'rows'        => 20
                                    , 'show_in_2_cols' => true
                                    // Default options:
                                    , 'class'           => ''                   // Any extra CSS Classes to append to the Editor textarea
                                    , 'default_editor'  => 'html'               // 'tinymce' | 'html'       // 'html' is used for the "Text" editor tab.
                                    , 'show_visual_tabs'=> true                 // Remove Visual Mode from the Editor
                                    , 'teeny'           => true                 // Whether to output the minimal editor configuration used in PressThis
                                    , 'drag_drop_upload'=> false                // Enable Drag & Drop Upload Support (since WordPress 3.9)
                            );
		*/

    }


	/**
	 * Add Custon JavaScript - for some specific settings options
	 *      Need to executes after showing of entire settings page (on hook: wpbc_after_settings_content).
	 *      After initial definition of settings,  and possible definition after POST request.
	 *
	 * @param string $menu_slug
	 * @param string $active_page_tab
	 * @param string $active_page_subtab
	 *
	 * @return void
	 */
	public function enqueue_js( $menu_slug, $active_page_tab, $active_page_subtab ) {

		$js_script = '';

		if ( ! empty( $js_script ) ) {
			wpbc_enqueue_js( $js_script );
		}
	}
}


/**
 * Override VALIDATED fields BEFORE saving to DB
 *
 * @param array $validated_fields - array of form  fields.
 */
function wpbc_settings_form_options_validate_fields_before_saving__all( $validated_fields ) {

	// Unset promote fields.
	foreach ( $validated_fields as $field_name => $field_value ) {
		if ( strpos( $field_name, '__promote_upgrade' ) > 0 ) {
			unset( $validated_fields[ $field_name ] );
		}
	}

	return $validated_fields;
}
add_filter( 'wpbc_settings_form_options_validate_fields_before_saving', 'wpbc_settings_form_options_validate_fields_before_saving__all', 10, 1 );


/**
 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_Settings_Form_Options extends WPBC_Page_Structure {

	/**
	 * Link to Settings API obj
	 *
	 * @var bool
	 */
	private $settings_api = false;

	public function __construct() {

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all
			// If tab  was not selected or selected default,  then  redirect  it to the "form" tab.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( ( isset( $_REQUEST['page'] ) && ( 'wpbc-settings' === $_REQUEST['page'] ) ) && ( ( ! isset( $_REQUEST['tab'] ) ) || ( 'general' === $_REQUEST['tab'] ) ) ) {
				$_REQUEST['tab'] = 'form';
			}
		} else {
			parent::__construct();
		}
	}

	public function in_page() {

		if ( WPBC_Frontend_Settings::is_bfb_enabled( null ) ) {
			return 'test' . (string) wp_rand( 100000, 1000000 );
		}

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all.
			return (string) wp_rand( 100000, 1000000 );
		}

		return 'wpbc-settings';
	}

	/**
	 * Get Settings API class - define, show, update "Fields".
	 *
	 * @return object Settings API
	 */
	public function settings_api() {

		if ( false === $this->settings_api ) {
			$this->settings_api = new WPBC_API_Settings_Form_Options();
		}

		return $this->settings_api;
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
		$this_settings_url = wpbc_get_settings_url() . '&tab=form&subtab=form_options';

		// =============================================================================================================
		// ==  B O O K I N G _ F O R M _ O P T I O N S  ==
		// =============================================================================================================

		$section_id              = 'wpbc_general_settings_form_metabox';
		$subtabs['form_options'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Form Options', 'booking' ),
				'page_title'      => __( 'Booking Form Options', 'booking' ),
				'hint'            => __( 'Enable CAPTCHA, auto-fill fields, and customize the form\'s color theme.', 'booking' ),
				'font_icon'       => 'wpbc-bi-toggle2-off',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'style'           => 'order:110',
				'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
				// 'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . $this_settings_url . "', '" . $section_id . "' );",//.
				'default'         => false,
			)
		);

		$section_id              = 'wpbc_general_time_slots_configuration_metabox';
		$subtabs['time_slots_configuration'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Time Slot Configuration', 'booking' ). '<span class="wpbc-bi-arrow-up-short" style="margin-left: auto;"></span>',
				'page_title'      => __( 'Time Slot Configuration', 'booking' ),
				'hint'            => __( 'Enable and configure time selection fields in your booking form.', 'booking' ),
				'font_icon'       => 'wpbc_icn_schedule',
				'font_icon_right' => '', // 'wpbc-bi-question-circle', //.
				'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
				'link'            => wpbc_get_settings_url() . '&tab=form&field_type=timeslots',
				// 'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . $this_settings_url . "', '" . $section_id . "' );",//.
				'default'         => false,
				'style'           => 'order:109',
			)
		);

		// We return  it as subtabs of Form tab!
		$tabs[ 'form' ]['subtabs'] = $subtabs;

		return $tabs;
	}


	/**
	 * Show Content of Settings page.
	 */
    public function content() {

		// Checking.
		do_action( 'wpbc_hook_settings_page_header', 'general_settings' );       // Define Notices Section and show some static messages, if needed.

		if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) {
			return false;
		}    // Check if MU user activated, otherwise show Warning message.

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {
			return false;
		}  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.

		// Init Settings API & Get Data from DB.
		$this->settings_api();                                                  // Define all fields and get values from DB.

		// Submit .

		$submit_form_name = 'wpbc_general_settings_form';                       // Define form name.

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ 'is_form_sbmitted_' . $submit_form_name ] ) ) {

			// Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }.
			$nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page.

			// Save Changes .
			$this->update();
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_GET['scroll_to_section'] ) ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					jQuery('#<?php echo esc_js( sanitize_text_field( wp_unslash( $_GET['scroll_to_section'] ) ) ); ?> a').trigger('click');
				});
			</script>
			<?php
		}


		// JavaScript: Tooltips, Popover, Datepick (js & css) .
		echo '<span class="wpdevelop">';
		wpbc_js_for_bookings_page();
		echo '</span>';


		// Content .
		?>
		<div class="clear"></div>
		<div class="wpbc_settings_flex_container">
			<div class="wpbc_settings_flex_container_right">

					<span class="metabox-holder">
					<form name="<?php echo esc_attr( $submit_form_name ); ?>" id="<?php echo esc_attr( $submit_form_name ); ?>" action="" method="post">
						<?php
						// N o n c e   field, and key for checking   S u b m i t.
						wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
						?>
						<input type="hidden" name="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" id="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" value="1"/>
						<input type="hidden" name="form_visible_section" id="form_visible_section" value=""/>
						<div class="wpbc_settings_row wpbc_settings_row_full_width" >

							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_form', __('Form Options', 'booking'), array( 'is_section_visible_after_load' => true, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'form' ); ?>

							<?php wpbc_close_meta_box_section(); ?>

						</div>
						<div class="clear"></div>
						<div class="container_for_save_buttons">
							<input type="submit" value="<?php esc_attr_e( 'Save Changes', 'booking' ); ?>" class="button button-primary wpbc_submit_button"/>
							<span class="sub_right">
								<a style="margin:0;" class="button button"
									onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_all_tab a' ,'.postbox', 'Show All Settings' );"
									href="javascript:void(0);">
									<?php
										esc_html_e( 'Show All Settings', 'booking' )
									?>
								</a>
								<?php
								if ( 'translations_updated_from_wpbc_and_wp' !== get_bk_option( 'booking_translation_update_status' ) ) {

									$current_locale = wpbc_get_maybe_reloaded_booking_locale();

									if ( ! in_array( $current_locale, array( 'en_US', 'en_CA', 'en_GB', 'en_AU' ), true ) ) {

										echo '<a class="button button" href="'
											. esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) . '&update_translations=1#wpbc_general_settings_system_info_metabox' )
											. '">'
											. esc_html__( 'Update Translations', 'booking' )
											. '</a>';
									}
								}

								if ( ! wpbc_is_this_demo() ) {

									echo '<a style="margin:0 2em;" class="button button" href="'
										. esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) . '&restore_dismissed=On#wpbc_general_settings_restore_dismissed_metabox' )
										. '">'
										. esc_html__( 'Restore all dismissed windows', 'booking' )
										. '</a>';
								}
								?>
							</span>
						</div>

					</form>
					</span>
			</div>
		</div>
		<?php
        do_action( 'wpbc_hook_settings_page_footer', 'form_options_settings' );
    }


	/**
	 * Save Chanages.
	 */
	public function update() {

		$validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.

		$validated_fields = apply_filters( 'wpbc_settings_form_options_validate_fields_before_saving', $validated_fields );   // Hook for validated fields.

		/**
		 * Skip saving specific option, for example in Demo mode.
		 * // unset( $validated_fields['booking_start_day_weeek'] );
		 */

		// FixIn: 9.8.6.1.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['form_visible_section'] ) ) {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function () {
					jQuery( '<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						echo esc_js( $_POST['form_visible_section'] );
						?> a' ).trigger( 'click' );
				} );
			</script>
			<?php
		}

		$this->settings_api()->save_to_db( $validated_fields );                 // Save fields to DB.

		wpbc_show_changes_saved_message();
	}
}
add_action('wpbc_menu_created', array( new WPBC_Page_Settings_Form_Options() , '__construct') );    // Executed after creation of Menu