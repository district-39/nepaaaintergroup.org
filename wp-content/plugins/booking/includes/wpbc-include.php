<?php
/**
 * Include Files of Booking Calendar.
 *
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Files Loading
 * @category Bookings
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

// =====================================================================================================================
// ==  L O A D   F I L E S  ==
// =====================================================================================================================

require_once WPBC_PLUGIN_DIR . '/core/wpbc-debug.php';                       // Debug                                            = Package: WPBC =.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-core.php';                        // Core.

require_once WPBC_PLUGIN_DIR . '/core/any/class-css-js.php';                 // Abstract. Loading CSS & JS files                 = Package: Any =.
require_once WPBC_PLUGIN_DIR . '/core/any/class-admin-settings-api.php';     // Abstract. Settings API.

require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/class-settings-page-parts.php';                                   // Settings Template - "Settins Parts", which used in the class-page-structure.php .
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/class-menu-structure.php';                                        // Abstract. Page Structure in Admin Panel    // 2025-02-09.
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/class-page-structure.php';                                        // Abstract. Page Structure in Admin Panel    // 2024-12-23.

// -----------------------------------------------------------------------------------------------------------------
// Booking Listing templates.
// -----------------------------------------------------------------------------------------------------------------
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_listing_row.php';
// Booking Actions.
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/booking_note.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/booking_cost.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/payment_status.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/payment_request.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/change_resource.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/duplicate_booking.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/booking_action/change_locale.php';
// Booking Listing - Filters - ...
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_filters/bo_listing_toolbar.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_filters/sort_by.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_filters/expand_colapse_all.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_filters/resource_choosen.php';
// Booking Listing - Actions - Bulk Actions.
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/bulk_actions_dropdown.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_approve.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_pending.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_trash.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_restore.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_delete.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_read.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/selected_bookings_unread.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/bulk_print.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/empty_trash.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/set_read_all.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/export_csv.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/listing_actions/import_google_calendar.php';


require_once WPBC_PLUGIN_DIR . '/core/any/class-admin-menu.php';                                                        // CLASS. Menus of plugin.
require_once WPBC_PLUGIN_DIR . '/core/any/admin-bs-ui.php';                                                             // Functions. Toolbar BS UI Elements.
if ( is_admin() ) {
	require_once WPBC_PLUGIN_DIR . '/core/class/wpbc-class-notices.php';        // Class - Showing different messages and alerts. Including some predefined static messages.
	require_once WPBC_PLUGIN_DIR . '/core/class/wpbc-class-welcome.php';        // Class - Welcome Page - info  about  new version.
}

// =====================================================================================================================
// Functions
// =====================================================================================================================
require_once WPBC_PLUGIN_DIR . '/includes/_functions/class-wpbc-action-scheduler-compatibility.php';                    // Class to Increase Memory and Time.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/nonce_func.php';                // Nonce functions - front-end excluding.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/str_regex.php';                 // String and Regex functions for shortcodes.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/is_table_exist.php';            // Is DB Tables Exists.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/is_dismissed.php';              // Is Dismissed.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/booking_data__parse.php';       // Booking form data parsing and replacement.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/booking_data__get.php';         // Booking details | replace / fields functions.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/simple_html_tags.php';          // Simple HTML Tags             -   Custom Shortcodes.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/admin_menu_url.php';            // Admin Menu Pages & URLs.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/admin_top_bar.php';             // Admin Top Bar.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/news_version.php';              // News, Version.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/versions.php';                  // Versions.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/sanitizing.php';                // Sanitizing.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/request.php';                   // Class for sanitizing $_REQUEST parameters and saving or getting it from  DB         // FixIn: 9.3.1.2.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/city_list.php';                 // City list.
require_once WPBC_PLUGIN_DIR . '/includes/save-user-meta/save-user-meta.php';        // Saving custom user data.
require_once WPBC_PLUGIN_DIR . '/includes/save-load-option/save-load-option.php';    // Ajax Options Saver / Loader.
require_once WPBC_PLUGIN_DIR . '/includes/_functions/calendar_scripts.php';    		 // Calendar Scripts.

require_once WPBC_PLUGIN_DIR . '/core/wpbc_functions.php';                               // Functions.
require_once WPBC_PLUGIN_DIR . '/core/wpbc_functions_dates.php';                         // Function Dates                       New in 9.8.
require_once WPBC_PLUGIN_DIR . '/core/form_parser.php';                                  // Parser for booking form              New in 9.8.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-dates.php';                                   // Dates.
require_once WPBC_PLUGIN_DIR . '/core/wpbc_welcome.php';                                 // Welcome Panel Functions.

// New Engine       // FixIn: 9.8.0.4.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/wpbc_cache.php';                     // Caching different requests to DB                                 // FixIn: 9.8.0.4.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/booking_date_class.php';             // Functions for booking dates.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/dates_times_support.php';            // Functions for booking DATES & TIME.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/resource_support.php';               // Functions for resources support.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/capacity.php';                       // Get Dates from DB  - Capacity, Availability and more ...
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/get_times_fields.php';               // Get Times Fields options from  Booking Form.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/aggregate.php';                      // Aggregate functions  for 'aggregate' parameter in shortcode.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/where_to_save.php';                  // Check  where to  save booking.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/create_booking.php';                 // Functions to create new bookings.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/confirmation.php';                   // Confirmation section - get data.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/confirmation_page.php';              // Confirmation Page.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/calendar_load.php';                  // Scripts to  load calendar.
require_once WPBC_PLUGIN_DIR . '/includes/_capacity/captcha_simple_text.php';            // Simple text captcha checking.

require_once WPBC_PLUGIN_DIR . '/core/wpbc-translation.php';                 // Translation,  must be loaded after '/core/wpbc-core.php',  because there defined  add_bk_filter(), etc...
// FixIn: 8.9.4.12.
if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations.php' ) ) {

	require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations.php';    // All Translation Terms.

	// FixIn: 8.7.3.6.
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations1.php' ) ) {
		require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations1.php';
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations2.php' ) ) {
		require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations2.php';
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations3.php' ) ) {
		require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations3.php';
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations4.php' ) ) {
		require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations4.php';
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations5.php' ) ) {
		require_once WPBC_PLUGIN_DIR . '/core/lang/wpbc_all_translations5.php';
	}
}

require_once WPBC_PLUGIN_DIR . '/includes/publish/wpbc-create-pages.php';                // Create pages for different purposes                  // FixIn: 9.6.2.10.
require_once WPBC_PLUGIN_DIR . '/includes/publish/wpbc-publish-shortcode.php';           // Publish  Booking Calendar shortcodes into the Pages   // FixIn: 9.8.15.5.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-emails.php';                                  // Emails.
// JS & CSS.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-css.php';                                     // Load CSS.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-js.php';                                      // Load JavaScript.
require_once WPBC_PLUGIN_DIR . '/core/wpbc-js-vars.php';                                 // Define JavaScript Vars.
// Admin UI.
require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-toolbars.php';                          // Toolbar - BS UI Elements.
require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-sql.php';                               // Data Engine for Booking Listing / Calendar Overview pages.
// FixIn: 9.6.3.5    // FixIn: 8.6.1.13.
require_once WPBC_PLUGIN_DIR . '/core/timeline/flex-timeline.php';                       // New. Flex. Timeline.

require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-dashboard.php';                          // Dashboard Widget.

// ---------------------------------------------------------------------------------------------------------------------
// Admin Pages
// ---------------------------------------------------------------------------------------------------------------------

// FixIn: 9.2.1 // FixIn: 9.6.3.5.
require_once WPBC_PLUGIN_DIR . '/includes/_booking_hash/booking_hash.php';                                       // FixIn: 9.2.3.3.

require_once WPBC_PLUGIN_DIR . '/includes/_news/wpbc_news.php';

// UI Elements.
require_once WPBC_PLUGIN_DIR . '/includes/_toolbar_ui/flex_ui_elements.php';
require_once WPBC_PLUGIN_DIR . '/includes/_toolbar_ui/ui__settings_panel.php';
require_once WPBC_PLUGIN_DIR . '/includes/_toolbar_ui/toolbar_ui.php';
require_once WPBC_PLUGIN_DIR . '/includes/_toolbar_ui/ui__form_steps_timeline.php';  // FixIn: 10.9.6.6.

// Booking Listing.
require_once WPBC_PLUGIN_DIR . '/includes/_pagination/pagination.php';
require_once WPBC_PLUGIN_DIR . '/includes/print/bookings_print.php';

require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/bookings__sql.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/bookings__actions.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/bookings__listing.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-bookings/bookings__page.php';

// Booking > Availability page.
require_once WPBC_PLUGIN_DIR . '/includes/page-availability/availability__activation.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-availability/availability__toolbar_ui.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-availability/availability__class.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-availability/availability__resource.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-availability/availability__page.php';

if ( WPBC_NEW_FORM_BUILDER ) {
	require_once WPBC_PLUGIN_DIR . '/includes/page-form-builder/bfb-include.php';                                       // Booking Form Builder - @since: 11.0.0.
	// New Shortcode Parsers and Form Render. 11.0.1.
	require_once WPBC_PLUGIN_DIR . '/includes/page-form-builder/form-render/class-wpbc-form-shortcode-engine.php';
	require_once WPBC_PLUGIN_DIR . '/includes/page-form-builder/form-render/class-wpbc-form-shortcode-engine-adapter.php';
	require_once WPBC_PLUGIN_DIR . '/includes/page-form-builder/form-render/bfb-form-render.php';
}


// Elementor Addons:   https://developers.elementor.com/docs/getting-started/first-addon/.
 require_once WPBC_PLUGIN_DIR . '/includes/elementor-booking-form/wpbc-elementor.php';

// ---------------------------------------------------------------------------------------------------------------------

// FixIn: 9.6.3.5.
require_once WPBC_PLUGIN_DIR . '/core/admin/page-timeline.php';                      // Timeline.
require_once WPBC_PLUGIN_DIR . '/core/admin/page-new.php';                           // Add New Booking page.

require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-settings-functions.php';    // Support functions for Booking > Settings General page.
require_once WPBC_PLUGIN_DIR . '/core/admin/page-settings.php';              // Settings page.
require_once WPBC_PLUGIN_DIR . '/core/admin/api-settings.php';               // Settings API.


require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-gutenberg.php';              // Settings page.

// ---------------------------------------------------------------------------------------------------------------------

// Functions from  Free form that can  be use in paid versions in Wizard Setup.

require_once WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_templates.php';                                         // Booking Form  Templates // FixIn: 10.6.2.1.

require_once WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__default.php';
require_once WPBC_PLUGIN_DIR . '/includes/page-form-simple/form_simple__get_data.php';

if ( file_exists( WPBC_PLUGIN_DIR . '/inc/_ps/personal.php' ) ) {
	require_once WPBC_PLUGIN_DIR . '/inc/_ps/personal.php';
} else {
	require_once WPBC_PLUGIN_DIR . '/includes/page-resource-free/page-resource-free.php';        // Resource page for Free version.
	require_once WPBC_PLUGIN_DIR . '/includes/page-form-simple/page-form-simple.php';            // Booking Form Simple.

	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-new-admin.php';   // Email - New admin.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-new-visitor.php'; // Email - New visitor.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-deny.php';        // Email - Deny - set  pending.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-approved.php';    // Email - Approved.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-trash.php';       // Email - Trash.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-email-deleted.php';     // Email - Deleted / completely erase.

	require_once WPBC_PLUGIN_DIR . '/core/admin/page-ics-general.php';        // General ICS Help Settings page.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-ics-import.php';         // Import ICS Help Settings page.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-ics-export.php';         // Export ICS Feeds Settings page.
	require_once WPBC_PLUGIN_DIR . '/core/admin/page-import-gcal.php';        // Import from  Google Calendar Settings page .
}

require_once WPBC_PLUGIN_DIR . '/includes/page-settings-form-options/page-settings-form-options.php';                                     // Booking Form Opyions Settings.
require_once WPBC_PLUGIN_DIR . '/includes/page-settings-color-themes/page-settings-color-themes.php';                                                 // Appearance / Color Themes / Skins Settings.

// Booking > Setup page                                                         // FixIn: 10.2.0.1.
// FixIn: 9.8.0.2.
require_once WPBC_PLUGIN_DIR . '/includes/page-setup/setup__page.php';
require_once WPBC_PLUGIN_DIR . '/includes/_tour/wpbc_tour.php';              // FixIn: 10.4.0.1.


require_once WPBC_PLUGIN_DIR . '/includes/_feedback/feedback.php';                                                      // FixIn: 9.2.3.6.
require_once WPBC_PLUGIN_DIR . '/includes/_feedback_deactivation/feedback.php';                                         // FixIn: 10.12.3.4.

// Old Working.
require_once WPBC_PLUGIN_DIR . '/core/lib/wpdev-booking-widget.php';            // W i d g e t s.
require_once WPBC_PLUGIN_DIR . '/js/captcha/captcha.php';                       // C A P T C H A.

require_once WPBC_PLUGIN_DIR . '/core/lib/wpbc-calendar-legend.php';            // Calendar Legend                      // FixIn: 9.4.3.6.

if ( 1 ) {
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-shortcode-params.php';
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-inline-css-js.php';
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-render.php';
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-shortcodes.php';
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-form-source-resolver.php';
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/hooks/class-fe-bfb-settings-hooks.php';   // Apply BFB settings to rendered booking form HTML.
	require_once WPBC_PLUGIN_DIR . '/includes/fontend/class-fe-render-form-body.php';
}
require_once WPBC_PLUGIN_DIR . '/core/lib/wpdev-booking-class.php';             // C L A S S    B o o k i n g.
require_once WPBC_PLUGIN_DIR . '/core/lib/wpbc-booking-new.php';                // N e w.
require_once WPBC_PLUGIN_DIR . '/core/lib/wpbc-cron.php';                       // CRON  @since: 5.2.0.


if ( is_admin() ) {
	// FixIn: 9.9.0.15.
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/shortcode_tpl_js_loader.php';                            // Templates JS Loader.
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking.php';                                     // Booking  -   Shortcode Config Content.
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_calendar.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_select.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_timeline.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_form.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_search.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_other.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_import.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/sh_tpl_booking_listing.php';
	require_once WPBC_PLUGIN_DIR . '/includes/ui_modal__shortcodes/tiny-button-popup.php';                                  // PopUp for shortcodes     and     Buttons in Edit toolbar.
}

require_once WPBC_PLUGIN_DIR . '/core/sync/wpbc-gcal-class.php';                // DONE: in 7.0     // Google Calendar Feeds Import @since: 5.2.0  - v.3.0 API support @since: 5.4.0.
require_once WPBC_PLUGIN_DIR . '/core/sync/wpbc-gcal.php';                      // DONE: in 7.0     // Sync Google Calendar Events with  WPBC @since: 5.2.0  - v.3.0 API support @since: 5.4.0.

require_once WPBC_PLUGIN_DIR . '/core/any/activation.php';
require_once WPBC_PLUGIN_DIR . '/core/wpbc-activation.php';

require_once WPBC_PLUGIN_DIR . '/core/wpbc-dev-api.php';                     // API for Booking Calendar integrations.

make_bk_action( 'wpbc_loaded_php_files' );
do_action( 'wpbc_loaded_php_files' );
