<?php
/**
 * Color Themes - Settings page.
 *
 * @version     1.0
 * @package     Booking > Settings > Color Themes
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com
 * @modified    2025-05-07
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit if accessed directly.
}


require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__preview.php' );                                // Preview.
require_once( WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__preview_templates.php' );                      // Templates - Simple Booking Form.


/**
 * Override VALIDATED fields BEFORE saving to DB
 *
 * @param array $validated_fields - array of form  fields.
 */
function wpbc_settings_color_themes_validate_fields_before_saving__all( $validated_fields ) {

	// Unset promote fields.
	foreach ( $validated_fields as $field_name => $field_value ) {
		if ( strpos( $field_name, '__promote_upgrade' ) > 0 ) {
			unset( $validated_fields[ $field_name ] );
		}
	}

	return $validated_fields;
}
add_filter( 'wpbc_settings_color_themes_validate_fields_before_saving', 'wpbc_settings_color_themes_validate_fields_before_saving__all', 10, 1 );


/** API  for  Settings_Color_Themes  */
class WPBC_API_Settings_Color_Themes extends WPBC_Settings_API {


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

        $this->fields['booking_form_theme'] = array(
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_form_theme']           //'Off'
                                , 'title'       => __('Color Theme' ,'booking')
                                , 'description' => __('Select a color theme for your booking form that matches the look of your website.' ,'booking')
												. '<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info">' .
														esc_html__( 'When you select a color theme, it also change the calendar and time-slot picker skins to match your choice. Customize these options separately as needed.', 'booking' ) .
												   '</div>'
                                , 'options' => array(
						                                ''                  => __( 'Light', 'booking' ),
						                                'wpbc_theme_dark_1' => __( 'Dark', 'booking' )
					                                )
                                , 'group'       => 'color_themes'
            );

//	    $this->fields['booking_form_theme__help_tip'] = array(
//                            'type'          => 'pure_html'
//                            , 'group'       => 'color_themes'
//	                        , 'html' => '<tr><td colspan="2">' .
//									    '<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info">' .
//										esc_html__( 'When you select a color theme, it also change the calendar and time-slot picker skins to match your choice. Customize these options separately as needed.', 'booking' ) .
//									   '</div>' .
//								    '</td> </tr>'
//                    );

        $this->fields['booking_skin'] = array(
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_skin']      // '/css/skins/traditional.css'         // Activation|Deactivation  of this options in wpbc-activation  file.  // Default value in wpbc_get_default_options('booking_skin')
                                    //, 'value' => '/css/skins/standard.css'    //This will override value loaded from DB
                                    , 'title'       => __('Calendar Skin', 'booking')
                                    , 'description' => __('Select the skin of the booking calendar' ,'booking')
                                    , 'options'     => wpbc_get_calendar_skin_options()
                                    , 'group'       => 'color_themes'
                            );



        //  Time Picker Skin  /////////////////////////////////////////////////////
        $timeslot_picker_skins_options  = array();

        // Skins in the Custom User folder (need to create it manually):    http://example.com/wp-content/uploads/wpbc_skins/ ( This folder do not owerwrited during update of plugin )
        $upload_dir = wp_upload_dir();
		// FixIn: 8.9.4.8.
        $files_in_folder = wpbc_dir_list( array(  WPBC_PLUGIN_DIR . '/css/time_picker_skins/', $upload_dir['basedir'].'/wpbc_time_picker_skins/' ) );  // Folders where to look about Time Picker skins

        foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';
			//FixIn: 8.9.4.8    // FixIn: 9.1.2.10.
            $skin_file[1] = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL,  $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for Time Picker skin
            $timeslot_picker_skins_options[ $skin_file[1] ] = $skin_file[2];
        }

        $this->fields['booking_timeslot_picker_skin'] = array(
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_timeslot_picker_skin']      // '/css/skins/traditional.css'         // Activation|Deactivation  of this options in wpbc-activation  file.  // Default value in wpbc_get_default_options('booking_skin')
                                    //, 'value' => '/css/time_picker_skins/grey.css'    //This will override value loaded from DB
                                    , 'title'       => __('Time Picker Skin', 'booking')
                                    , 'description' => __('Select the skin of the time picker' ,'booking')
                                    , 'options'     => $timeslot_picker_skins_options
                                    , 'group'       => 'color_themes'
                            );





//	    $this->fields['booking_timeslot_picker__help_tip'] = array(
//                            'type'          => 'pure_html'
//                            , 'group'       => 'color_themes'
//	                        , 'html' => '<tr><td colspan="2">'
//	                                    .'<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info">'
//											. '<strong class="alert-heading">' . esc_html__( 'Note', 'booking' ) . '!</strong> '
//											/* translators: 1: ... */
//											. sprintf( __( 'You can enable or disable, as well as configure %1$sTime Slots%2$s for your booking form on the Settings > %3$sBooking Form%4$s page.', 'booking' ),
//													'<strong>', '</strong>',
//												    '<strong><a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=form' ) . '">', '</a></strong>'
//												)
//	                                    . '</div>'
//
//								    .'</td> </tr>'
//                    );
		// FixIn: 8.7.11.10.
	    $this->fields['booking_timeslot_picker'] = array(
	                            'type'          => 'checkbox'
	                            , 'default'     => $default_options_values['booking_timeslot_picker']   //'Off'
	                            , 'title'       => __('Time picker for time slots' ,'booking')
	                            , 'label'       => __('Show time slots as a time picker instead of a select box.' ,'booking')
								, 'description' =>  ''
													.'<div class="wpbc-general-settings-notice wpbc-settings-notice notice-info">'
														. '<strong class="alert-heading">' . esc_html__( 'Note', 'booking' ) . '!</strong> '
														/* translators: 1: ... */
														. sprintf( __( 'You can enable or disable, as well as configure %1$sTime Slots%2$s for your booking form on the Settings > %3$sBooking Form%4$s page.', 'booking' ),
																'<strong>', '</strong>',
																'<strong><a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=form' ) . '">', '</a></strong>'
															)
													. '</div>'
	                            , 'group'       => 'color_themes'
	                            , 'tr_class'    => 'wpbc_timeslot_picker'
	        );

		// FixIn: 8.2.1.27.
	    $this->fields['booking_timeslot_day_bg_as_available'] = array(
	                            'type'          => 'checkbox'
	                            , 'default'     => $default_options_values['booking_timeslot_day_bg_as_available']   //'Off'
	                            , 'title'       => __('Do not change background color for partially booked days' ,'booking')
	                            , 'label'       => __('Show partially booked days with same background as in legend item' ,'booking')
	                            , 'description' => '<span class="description0" style="line-height: 1.7em;margin: 0 0 0 -10px;"><strong>' . esc_html__('Note' ,'booking') .':</strong> '
                                                        . sprintf(__('Partially booked item - day, which is booked for the specific time-slot(s).' ,'booking'),'<b>','</b>')
                                                   . '</span>'

	                            , 'group'       => 'color_themes'
	                            , 'tr_class'    => 'wpbc_timeslot_day_bg_as_available'
	        );


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

        // Select  specific Time Picker skin,  depending from  selection  of Calendar skin      // FixIn: 8.7.11.10.
        $js_script .= " jQuery('#set_gen_booking_skin').on( 'change', function(){    
        
                            var wpbc_selected_skin = jQuery('select[name=\"set_gen_booking_skin\"] option:selected').val(); 
                            var wpbc_cal_skin_arr = [      
													'/css/skins/black-2.css',
													'/css/skins/black.css',
													'/css/skins/multidays.css',
													'/css/skins/premium-black.css',
													'/css/skins/premium-light.css',
													'/css/skins/premium-marine.css',
													'/css/skins/premium-steel.css',
													'/css/skins/standard.css',
													'/css/skins/traditional-light.css',
													'/css/skins/traditional.css',
													'/css/skins/green-01.css'
                                                ];
                            var wpbc_time_skin_arr = [      
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/green.css',
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/light__24_8.css',
													'/css/time_picker_skins/marine.css',
													'/css/time_picker_skins/light__24_8.css',
													'/css/time_picker_skins/blue.css',
													'/css/time_picker_skins/orange.css',
													'/css/time_picker_skins/light__24_8.css',
													'/css/time_picker_skins/light__24_8.css'
                                                ];  
                            if ( wpbc_cal_skin_arr.indexOf( wpbc_selected_skin ) >= 0 ) {
								jQuery( '#set_gen_booking_timeslot_picker_skin' ).find( 'option' ).prop( 'selected', false );								
								jQuery( '#set_gen_booking_timeslot_picker_skin' ).find( 'option[value=\"'+ wpbc_time_skin_arr[ wpbc_cal_skin_arr.indexOf( wpbc_selected_skin ) ]  +'\"]' ).prop( 'selected', true );																
                            }
        
						} ); ";

		// If selected Dark  theme then  select  Dark  calendar  skin,  as well.
		$js_script .= " jQuery('#set_gen_booking_form_theme').on( 'change', function(){
                            var wpbc_selected_theme = jQuery('select[name=\"set_gen_booking_form_theme\"] option:selected').val(); 
                            var wpbc_cal_dark_skin_path = '/css/skins/24_9__dark_1.css';		                            							  
                            if ( 'wpbc_theme_dark_1' === wpbc_selected_theme ) {
								jQuery( '#set_gen_booking_skin' ).find( 'option' ).prop( 'selected', false );								
								jQuery( '#set_gen_booking_skin' ).find( 'option[value=\"'+ wpbc_cal_dark_skin_path  +'\"]' ).prop( 'selected', true ).trigger('change');																
                            }
                            var wpbc_cal_light_skin_path = '/css/skins/25_5__square_1.css';		
                            if ( '' === wpbc_selected_theme ) {
								jQuery( '#set_gen_booking_skin' ).find( 'option' ).prop( 'selected', false );								
								jQuery( '#set_gen_booking_skin' ).find( 'option[value=\"'+ wpbc_cal_light_skin_path  +'\"]' ).prop( 'selected', true ).trigger('change');																
                            }        
						} ); ";

		if ( ! empty( $js_script ) ) {
			wpbc_enqueue_js( $js_script );
		}
	}
}


/**
 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_Settings_Color_Themes extends WPBC_Page_Structure {

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
			$this->settings_api = new WPBC_API_Settings_Color_Themes();
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
		$this_settings_url = wpbc_get_settings_url() . '&tab=color_themes';

		// =============================================================================================================
		// ==  Color Themes  ==
		// =============================================================================================================

        $tabs[ 'color_themes' ] = array(
			'is_show_top_path'                   => false,                                // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                           		  // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'Appearance', 'booking' ) . ' / ' . __( 'Color Theme', 'booking' ),                               // Header - Title.  If false, than hidden.
			'page_description'                   => __('Select a color theme, adjust the calendar\'s display by choosing a calendar skin, choose how time slots are shown in the form: as a dropdown list or a time picker.','booking'),                               // Header - Title Description.  If false, than hidden.
                              'title'     => __( 'Appearance', 'booking' ) . ' / ' . __( 'Color Theme', 'booking' )     // Title of TAB
                            , 'page_title'=> __( 'Appearance', 'booking' ) . ' / ' . __( 'Color Theme', 'booking' )      // Title of Page
                            , 'hint'      => __( 'Add, remove, or customize fields in your booking form.', 'booking')               // Hint
                            //, 'css_classes'=> ''                                // CSS class(es)
                            , 'font_icon' => 'wpbc-bi-palette-fill 0wpbc_icn_format_color_fill 0wpbc_icn_format_paint 0wpbc-bi-palette2'         // CSS definition  of forn Icon
                            , 'default'   => false                               // Is this tab activated by default or not: true || false.
                            , 'subtabs'   => array()
							, 'folder_style'     => 'order:30;',
                    );

//		$section_id              = 'wpbc_general_settings_booking_form_fields_metabox';
//		$subtabs['booking_form_fields'] = array_merge(
//			$subtab_default,
//			array(
//				'title'           => __( 'Booking Form Fields', 'booking' ),
//				'page_title'      => __( 'General Settings', 'booking' ),
//				'hint'            => __( 'Add, remove, or customize fields in your booking form.', 'booking' ),
//				'font_icon'       => 'wpbc_icn_format_color_fill',
//				'font_icon_right' => 'wpbc-bi-question-circle',
//				'css_classes'     => 'do_expand__' . $section_id . '_link sub_bold',
//				//'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
//				'link'            => wpbc_get_settings_url() . '&tab=form',
//				'default'         => true,
//			)
//		);
//
//
//		$section_id              = 'wpbc_general_settings_form_metabox';
//		$subtabs['color_themes'] = array_merge(
//			$subtab_default,
//			array(
//				'title'           => __( 'Form Options', 'booking' ),
//				'page_title'      => __( 'Form Options', 'booking' ),
//				'hint'            => __( 'Enable CAPTCHA, auto-fill fields, and customize the form\'s color theme.', 'booking' ),
//				'font_icon'       => 'wpbc_icn_format_paint',
//				'font_icon_right' => 'wpbc-bi-question-circle',
//				'css_classes'     => 'do_expand__' . $section_id . '_link sub_bold',
//				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . $this_settings_url . "', '" . $section_id . "' );",
//				'default'         => false,
//			)
//		);
//
//		$section_id                       = 'wpbc_general_settings_time_slots_metabox';
//		$subtabs['time_slots_appearance'] = array_merge(
//			$subtab_default,
//			array(
//				'title'           => __( 'Time Slot Appearance', 'booking' ),
//				'page_title'      => __( 'General Settings', 'booking' ),
//				'hint'            => __( 'Choose how time slots are shown in the form: as a dropdown list or a time picker, and set a color theme.', 'booking' ),
//				'font_icon'       => 'wpbc-bi-palette2',
//				'font_icon_right' => 'wpbc-bi-question-circle',
//				'css_classes'     => 'do_expand__' . $section_id . '_link',
//				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . $this_settings_url  . "', '" . $section_id . "' );",
//				'default'         => false,
//			)
//		);
//
//		// We return  it as subtabs of Form tab!
//		$tabs[ 'color_themes' ]['subtabs'] = $subtabs;

		// FixIn: 10.12.4.7.  $tabs[ 'color_themes_settings' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:30;' ) );

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

							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_form', __('Appearance / Color Themes', 'booking'), array( 'is_section_visible_after_load' => true, 'is_show_minimize' => false ) ); ?>

							<?php

							// $params = array ('is_use_js_for_templates' => false);
							// wpbc_stp_wiz__widget__color_theme__skins( $params );

							?>

							<?php $this->settings_api()->show( 'color_themes' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


						</div>
						<div class="clear"></div>
						<div class="container_for_save_buttons">
							<input type="submit" value="<?php esc_attr_e( 'Save Changes and Update Preview', 'booking' ); ?>" class="button button-primary wpbc_submit_button wpbc_submit_button_trigger"/>
							<span class="sub_right" style="display:none;">
								<a style="margin:0;" class="button button"
									onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_all_tab a' ,'.postbox', 'Show All Settings' );"
									href="javascript:void(0);">
									<?php
										esc_html_e( 'Show All Settings', 'booking' )
									?>
								</a>
								<?php

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

		wpbc_show_preview__form();

		// $this->js_preview_calendar_skin_change();

		do_action( 'wpbc_hook_settings_page_footer', 'color_themes_settings' );
    }


	public function js_preview_calendar_skin_change(){

		$upload_dir              = wp_upload_dir();
		$custom_user_skin_folder = $upload_dir['basedir'];

	    ?>
		<script type="text/javascript">
			jQuery( document ).ready( function (){
				/**
				 * Change calendar skin view
				 */
				jQuery( '#ui_btn_cstm__set_calendar_skins' ).on( 'change', function (event) {
					wpbc__calendar__change_skin( jQuery( this ).val() );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Save Chanages.
	 */
	public function update() {

		$validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.

		$validated_fields = apply_filters( 'wpbc_settings_color_themes_validate_fields_before_saving', $validated_fields );   // Hook for validated fields.

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
		?>
		<script type="text/javascript">
			(function() { var a = setInterval( function() {  if ( ( 'undefined' === typeof jQuery ) || ! window.jQuery ) { return; } clearInterval( a ); jQuery( document ).ready( function () {
				location.reload();
			} ); }, 500 ); })();
		</script>
		<?php
	}
}
add_action('wpbc_menu_created', array( new WPBC_Page_Settings_Color_Themes(), '__construct') );    // Executed after creation of Menu.
