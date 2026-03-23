<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wpbc_welcome_section_10_14( $obj ){

	$section_param_arr = array( 'version_num' => '10.14', 'show_expand' => false );

	$obj->expand_section_start( $section_param_arr );


	//   $obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Control start and end dates for booking and availability' ) ); ?></h2>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.14/wp_booking_calendar_elementor_advanced_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Control which dates are shown!**: You can now control which dates are shown in the Booking Calendar by using two new shortcode parameters: **[booking resource_id=1 calendar_dates_start=\'2025-01-01\' calendar_dates_end=\'2025-12-31\']**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**:  Ability to **set the start and end dates for the calendar** using shortcode parameters. Example: [booking resource_id=1 calendar_dates_start=\'2025-01-01\' calendar_dates_end=\'2025-12-31\' startmonth=\'2025-3\']' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**:  If startmonth is omitted, the calendar will now automatically use the month from calendar_dates_start as the initial display month.  Example: [booking resource_id=1 calendar_dates_start=\'2025-01-01\' calendar_dates_end=\'2025-12-31\']' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**:  New: You can now **allow users to submit bookings for past dates**, as long as those dates are within the visible range set by calendar_dates_start and calendar_dates_end in the shortcode. Useful for administrative or backdated bookings. Shortcode Example: [booking resource_id=1 calendar_dates_start=\'2025-01-01\' calendar_dates_end=\'2025-12-31\']' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**:  Elementor native supports of these parameters calendar_dates_start=\'2025-01-01\' calendar_dates_end=\'2025-12-31\' in Advanced section.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improvement**: When calendar_dates_start and calendar_dates_end are defined in the shortcode, the system skips checking availability from today, and instead starts from the defined calendar_dates_start.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improvement**: To **enable the new Smart Phone Validation** feature introduced in the previous update, go to: WP Booking Calendar > Settings > Booking Form > Form Options and enable the "Smart Phone Validation" option. It is disabled by default.' ) ); ?></li>
				</ul>
			</div>
		</div>
	</div>
	<?php

	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_13( $obj ){

	$section_param_arr = array( 'version_num' => '10.13', 'show_expand' => false );

	$obj->expand_section_start( $section_param_arr );


	//   $obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Smarter Bookings, Elementor Integration and Cleaner Interface' ) ); ?></h2>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Booking Calendar meets Elementor!**' ) ); ?></h3>
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.13/wp_booking_calendar_elementor_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Full Elementor Support!**: Design and customize your booking forms directly inside the Elementor editor—with real-time form previews and a built-in skin selector. No more shortcode guesswork—just drag, configure, and save!' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Calendar Preloader:** We’ve added a smooth-loading preloader bar that prevents layout jumps on slower connections, ensuring a polished experience for your visitors.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New Booking Form Template – Service Selection Wizard (Pro):** Perfect for appointment-based bookings: this template features a left-side service selector with a streamlined date/time step for faster, easier bookings.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Cleaner & More Intuitive Interface**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Updated Sidebar Navigation:** All plugin menus now live in a single, collapsible sidebar—easier to navigate and quicker to access.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Visual Polish & UX Tweaks:** We’ve made subtle design upgrades across the board: improved calendar styling, better visual hierarchy, smarter warnings (e.g., duplicate calendar IDs), and more compact menu visuals in compact mode.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 50%;margin: 0px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.13/vertical_menu_01.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Smart Phone Validation**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Auto-Detect & Format Phone Fields:** Phone fields now automatically detect and format numbers based on the user’s input and locale. Start typing +1 and the field instantly formats to +1 000 000 0000 (USA); type +34 for Spain and get +34 000 000 0000.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 50%;margin: 0px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.13/phone_validattion_01.png' ) ); ?>" style="margin:10px auto;max-width:600px;" />
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull;  **Built-In Intelligence – No Setup Required** We intelligently detect phone fields by name (e.g., “phone”, “fone”, “tel”, “mobile”, “telefono”, etc.) and apply the right mask and placeholder automatically—no third-party APIs needed. Only valid digits are allowed; unsupported characters are blocked instantly.' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div>
	<?php

	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_12( $obj ){

	$section_param_arr = array( 'version_num' => '10.12', 'show_expand' => false );

	$obj->expand_section_start( $section_param_arr );


	//   $obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Skin & Improved Pro Vesions Structure' ) ); ?></h2>
			<span style="font-size: 1em;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'A complete redesign of the Booking Calendar admin panel brings a **modern, clean, and user-focused UI**, helping you configure settings faster and manage bookings more efficiently.' ) ); ?></span>
			<div class="wpbc_wn_col" style="flex: 1 1 70px;margin: 0px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.12/calendar_skin_green_25_6.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				<!--h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Brand New Admin Panel Experience!**' ) ); ?></h3-->
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New Green Calendar Skin**: to enhance your calendar appearance.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Plugin Structure:**: All Pro versions now require the Free version for better auto-updates and streamlined add-on management.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Smarter Stripe Integration:**: Prevents duplicate customer entries—payments now stay linked to the same user.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Fixes and Enhancements:**: Time slot blocking, cost display corrections, and test card links for Stripe, PayPal, and Redsys added.' ) ); ?></li>
				</ul>
		<div class="wpbc_wn_section" style="justify-content: center;margin-top:2em;">
			<?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '...and other small improvements and refinements!' ) ); ?>
		</div>
			</div>
		</div>

	</div>
	<?php

	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_11( $obj ){

	$section_param_arr = array( 'version_num' => '10.11', 'show_expand' => false );

	$obj->expand_section_start( $section_param_arr );


	  // $obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Major Admin Interface Overhaul!' ) ); ?></h2>
			<span style="font-size: 1em;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'A complete redesign of the Booking Calendar admin panel brings a **modern, clean, and user-focused UI**, helping you configure settings faster and manage bookings more efficiently.' ) ); ?></span>
			<div class="wpbc_wn_col" style="flex: 1 1 55%;margin: 20px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.11/booking_calendar_settings_dashboard_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Brand New Admin Panel Experience!**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Fresh New Logo**: We\'ve updated the Booking Calendar logo with a clean and modern design based on the initials \'b\' and \'c\'.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Full-Screen Mode**: Hide the WordPress interface and focus entirely on booking management. Just click the full-screen icon at the top right of any admin page.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Universal Left Sidebar Menu**: Easily access all settings from a new side menu that works in wide, compact, or minimized modes—fully optimized for mobile too.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Fixed Top Bar**: The new horizontal bar stays visible while scrolling and contains essential buttons like “Full Screen.”' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col"  style="flex: 1 1 45%;margin: 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Redesigned Booking Listing Page' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Modern UI**: Clean and intuitive design, focused on clear display of booking dates, statuses, and key details.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Smarter Date View**: See booking dates displayed based on their type (single day, multiple days, or time slots).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Expandable Details**: Hover or click to expand and view full booking information.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Dropdown Actions**: All booking actions are now in an easy-to-use dropdown next to each entry.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Bulk Actions**: Located in the top left, automatically expands when bookings are selected.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Enhanced Pagination**: Easily browse bookings with new navigation and items-per-page controls.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 100%;margin: 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.11/booking_calendar_booking_listing_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col" style="flex: 1 1 55%;margin: 20px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.11/booking_calendar_timeline_view_page__multi_resources.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Screenshot from  paid versions of Booking Calendar' ) ); ?></span></span>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Other Improvements**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Timeline View**: Refreshed layout and better booking resource selection in paid versions.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Settings Menu Structure**: Settings pages are now grouped more logically in the sidebar for quicker access.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Accessibility**: Improved keyboard navigation for calendars and time pickers to meet EAA standards.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Edit Past Bookings**: Add &allow_past to the URL to allow editing bookings in the past from the admin panel.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Reset Filters**: If no bookings are found, a new “Reset Filter” link will appear to reload all bookings.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section" style="justify-content: center;">
			<?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Check information about the other updates on ' ) ); ?>&nbsp;<a href="https://wpbookingcalendar.com/wn/"> this page</a>.
		</div>
	</div>
	<?php


	// </editor-fold>



	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_10( $obj ){

	$section_param_arr = array( 'version_num' => '10.10', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	 // $obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" >
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: **Improved Booking Form & Display**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New Form Template:** - "**Appointments Based on Service Duration**" <br>Ideal for scheduling services with fixed durations (Pro Versions).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Flexible Form Fields** - Save and display custom "Option Titles" and "Values" in select boxes, checkboxes, and radio buttons, providing flexibility for configuring services based on time and more...' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Shortcode Example for Booking Form**: <br><code>[selectbox durationtime "Service A (20 min)@@00:20" "Service B (30 min)@@00:30"]</code>. <br>**Shortcodes for Displaying Booking Field Data**:<br> - To display the selected title, e.g., "**Service A (20 min)**", use shortcode: <code>[durationtime_val]</code> <em>(e.g. field name + "_val" sufix)</em>. <br> - To show the value, e.g., "**00:20**", use the standard shortcode: <code>[durationtime]</code> <em>(e.g. field name)</em>.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/wp_booking_calendar_wizard_form_steps_03.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" >
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: **Steps Timeline Progress Indicator**' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Easily show a progress indicator for multi-step booking forms.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Shortcode:** <code>[steps_timeline steps_count="3" active_step="1"]</code>' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Parameters:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **steps_count="3"** - Defines total number of steps' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **active_step="1"** - Highlights the current step' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **color="#619d40"** - Optional parameter to customize step color ' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/wp_booking_calendar_wizard_form_steps_chnage_over_02.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></span>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Availability & Time Management' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free. ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Unavailable Past Times**: Automatically hides/locks past time slots when selecting today’s date.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Timezone-Based Availability**: The "Unavailable time from current time" option now follows your WordPress timezone, ensuring accurate availability settings.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Customizable Labels**: Removed default colons (:) from field labels. You can now manually add them while editing forms in Simple Mode.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col" style="flex: 1 1 45%;margin: 10px 0;">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/availability_from_today.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<span><span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Medium or higher versions' ) ); ?></span></span>
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements & Fixes' ) ); ?></h3>
				<ul>
					<?php // Pro. ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; New JS Event Hook: \'wpbc_before_booking_create\'  - Allows developers to trigger actions before a booking is created.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Booking Hash Updates: If enabled, booking hashes now change when moving bookings to Pending or Trash, ensuring better security & tracking.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed CSS Conflicts: Resolved calendar cell width issues affecting certain WordPress themes.' ) ); ?></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Calendar Color Themes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added 1 dark  and 4 light Calendar Skins' ) ); ?></li>
				</ul>
			</div>
		</div>
		<div class="wpbc_wn_section" style="margin-top: -3em;">
			<div class="wpbc_wn_col">
				 	<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
					<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/24_9_dark_3_availability_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				 	<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
					<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/24_9_green_3_availability_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
			<div class="wpbc_wn_col">
				 	<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
					<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/24_9_green_2_availability_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				 	<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
					<img src="<?php echo esc_attr( $obj->section_img_url( '10.10/24_9_green_4_availability_page.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	/*
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free. ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**: You can ...' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improvement** ...' ) ); ?></li>
				</ul>

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro. ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We\'ve addressed ...' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed a ....' ) ); ?></li>
				</ul>

			</div>

		</div>
	</div><?php
	*/
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_9( $obj ){

	$section_param_arr = array( 'version_num' => '10.9', 'show_expand' => !false );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<!--h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Multi-Step Wizard for Booking Forms (Free Version)!' ) ); ?></h3-->
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Set Unavailable Time Intervals!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; You can now define unavailable intervals for specific hours and minutes starting from the current time. This feature complements the ability to set unavailable dates starting from today\'s date, offering more flexibility in managing availability.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.9/wp-booking-calendar__set_unavailable_time_from_current_time.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: **Redsys Payment Gateway** Support!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added integration with the **Redsys payment gateway** using the **Redsys Redirection** method. This feature is ideal for Spanish users, enabling them to seamlessly receive payments for bookings.' ) ); ?></li>
					<li><span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in the  Booking Calendar Business Small or higher versions' ) ); ?></span></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.9/wp-booking-calendar__redsys_settings.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New**: You can now select the "Number of months in a row" value from the Options toolbar on the WP Booking Calendar > Availability > Days Availability page. This feature improves the clarity of calendar day displays in certain calendar skins. The default value is set to 3 months.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved**: Setup Wizard Troubleshooting for Wordfence Users! A warning message now appears at the 4th and 5th steps of the Setup Wizard if the Wordfence plugin is installed, along with a link to troubleshoot the issue. This ensures smoother configuration and guides users in resolving potential conflicts.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improvement** Now, when approving a booking that was previously in the Trash, the system will automatically restore it, streamlining the approval process.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improvement** The email fields now use the HTML input type email, ensuring better validation and user experience when entering email addresses.' ) ); ?></li>
				</ul>

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We\'ve addressed an issue where the "real-time" cost wasn\'t displayed correctly based on selected days and other options. This occurred occasionally due to multiple requests causing server response delays. (10.8.1.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed a warning issue that occurred on the WP Booking Calendar > Availability > Season Availability page.' ) ); ?></li>
				</ul>

			</div>

		</div>
		<?php /* ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Calendar Behavior**: We’ve removed the highlighting of days in the calendar when the mouse cursor moves outside the calendar container for a smoother user experience. (10.5.2.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Timeline Navigation**: We’ve fixed an issue where the dropdown list would auto-close after selecting the start date in the navigation panel on the Timeline view. (10.5.2.1)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Page Workflow**: Notice messages from other plugins are now hidden in the header on Booking Calendar pages. This prevents interruptions to the normal workflow caused by messages from other plugins. (10.5.2.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Start Time Validation**: We’ve added a fix to check the start time and prevent the selection of times that have already passed for today. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Submit Button Color Issue Resolved**: We’ve fixed an issue where the color of the "Send" button wasn’t saving correctly after a second click on dates, when the range dates selection mode was enabled. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Date/Time Hints Display**: Resolved an issue where date/time hints were not showing in the booking form if no date was selected. Previously, a "0" would display if only the time was selected. (10.5.2.7)' ) ); ?></li>
				</ul>
			</div>
		</div>
		<?php */ ?>
	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_8( $obj ){

	$section_param_arr = array( 'version_num' => '10.8', 'show_expand' => !false );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Multi-Step Wizard for Booking Forms (Free Version)!' ) ); ?></h3>
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<!--<h3>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Booking Form Setup as the Wizard (several steps)' ) ); ?><!--</h3>-->
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now you can set up your booking form as a multi-step wizard in the Booking Calendar Free version. This feature allows you to **guide users through the booking process step-by-step**, enhancing the booking experience.' ) ); ?></li>
				</ul>
			<!--/div>
			<div class="wpbc_wn_col"-->
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.8/wp_booking_calendar_booking_form_several_steps_wizard_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Multi-Column Layout for Booking Forms (Free Version)!' ) ); ?></h3>
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; You can now set the number of columns in your booking form in the Booking Calendar Free version. Easily adjust the column layout in the "Form Layout" section at WP Booking Calendar > Settings > Booking Form page for a more organized and customizable form appearance.' ) ); ?></li>

				</ul>
			</div>
			<div class="wpbc_wn_col">
				 <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.8/wp_booking_calendar_booking_form_setup_in_several_columns_02.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We\'ve added a 20-minute time-slot interval option when editing time slots on the WP Booking Calendar > Settings > Booking Form page. This allows for quick and easy configuration of time slots with a 20-minute duration.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Enhanced** Added new  skins: "Black-2", "Green-01", "Light-01", and "Traditional-times" to the legacy calendar skin group. (10.7.1.5)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Enhanced** Disabled auto-selection of legacy calendar skins. (10.7.1.5.1)' ) ); ?></li>
				</ul>

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We\'ve introduced a new "**Time-Based Appointments**" form template in a multi-step wizard format. To use this template, reset your booking form by selecting it from the dropdown list in the toolbar on the Settings > Booking Form page. (10.7.1.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added new shortcodes: [search_days_number] and [search_nights_number] to enhance search results customization. Configure these on the WP Booking Calendar > Settings > Search page. (10.7.1.1)   *(Business Large, MultiUser)*' ) ); ?></li>
				</ul>
				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under hood' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; You can now use the \'allow_past\' parameter in the URL (e.g., https://yourserver.com/wp-admin/admin.php?page=wpbc-new&allow_past) to display past bookings and enable scrolling to previous months on the Booking > Add Booking page in the admin panel.' ) ); ?></li>
				</ul>

			</div>

		</div>
		<?php /* ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Calendar Behavior**: We’ve removed the highlighting of days in the calendar when the mouse cursor moves outside the calendar container for a smoother user experience. (10.5.2.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Timeline Navigation**: We’ve fixed an issue where the dropdown list would auto-close after selecting the start date in the navigation panel on the Timeline view. (10.5.2.1)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Page Workflow**: Notice messages from other plugins are now hidden in the header on Booking Calendar pages. This prevents interruptions to the normal workflow caused by messages from other plugins. (10.5.2.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Start Time Validation**: We’ve added a fix to check the start time and prevent the selection of times that have already passed for today. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Submit Button Color Issue Resolved**: We’ve fixed an issue where the color of the "Send" button wasn’t saving correctly after a second click on dates, when the range dates selection mode was enabled. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Date/Time Hints Display**: Resolved an issue where date/time hints were not showing in the booking form if no date was selected. Previously, a "0" would display if only the time was selected. (10.5.2.7)' ) ); ?></li>
				</ul>
			</div>
		</div>
		<?php */ ?>
	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_7( $obj ){

	$section_param_arr = array( 'version_num' => '10.7', 'show_expand' => !false );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Setup Wizard for Quick & Easy Configuration!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Our new Setup Wizard makes it simple to get started with the Booking Calendar plugin! With an intuitive, step-by-step flow, this wizard guides you through essential settings, including booking type selection, calendar appearance, availability preferences, and more.' ) ); ?></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Time Appointments - Quick Initial Setup' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Configure your **appointment booking system** in under 2.5 minutes. The guided setup process makes it quick and easy to get your system ready for handling time-based appointments.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<div style="margin:0 auto;width: 560px;">
					<iframe width="560" height="298" src="https://www.youtube.com/embed/GYJWZJBFwXw?si=uBMpuv0fQfpjR9_O" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				</div>

			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Full Day Bookings - Setup Wizard' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Easily configure Booking Calendar for full-day bookings with the new step-by-step Setup Wizard. Get your booking system ready for full day bookings.' ) ); ?></li>

				</ul>
			</div>
			<div class="wpbc_wn_col">
				<div style="margin:0 auto;width: 560px;">
					<iframe width="560" height="315" src="https://www.youtube.com/embed/NJ88lGD5iJ0?si=nFzlfnsPKcZHmtMB" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				</div>

			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col" style="flex: 1 1 33%;">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Changeover Multi-Day Bookings - Setup Wizard' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Configure multi-day bookings with specific check-in and check-out days, clearly marked with diagonal or vertical lines. Perfect for bookings that require split days.' ) ); ?></li>
				</ul>
				<span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in Business Small or higher versions.') ); ?></span><br>
				<span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Cost hints available in Business Medium or higher versions.') ); ?></span>
			</div>
			<div class="wpbc_wn_col">
				<div style="margin:0 auto;width: 560px;">
					<iframe width="560" height="298" src="https://www.youtube.com/embed/uCQ9JmHR8w4?si=wVdguA1rXo6DwcII" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				</div>

			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Display Available Booking Slots in the Calendar!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now you can display the number of available booking resources - such as slots, rooms, or items - directly in each date cell within the calendar, providing a clear view of availability at a glance.' ) ); ?></li>
				</ul>
				<span style="font-size: 0.9em;font-style: italic;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Available in Business Large or higher version.') ); ?></span><br>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.7/wp_booking_calendar__availability_hint_in_day_cell_01.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	// </editor-fold>
/*
	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Refactored Code**: We’ve refactored the code for improved efficiency and maintainability.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Email Deliverability**: The "From" email address is now automatically set to the website\'s "Administration Email Address" for all newly activated regular users. This change helps prevent emails from being marked as spam when the user\'s email is not from the website domain. *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Translation**. Local German translation update' ) ); ?></li>
				</ul>
				<!--
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>
				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>
				-->
			</div>

		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Calendar Behavior**: We’ve removed the highlighting of days in the calendar when the mouse cursor moves outside the calendar container for a smoother user experience. (10.5.2.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Timeline Navigation**: We’ve fixed an issue where the dropdown list would auto-close after selecting the start date in the navigation panel on the Timeline view. (10.5.2.1)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Page Workflow**: Notice messages from other plugins are now hidden in the header on Booking Calendar pages. This prevents interruptions to the normal workflow caused by messages from other plugins. (10.5.2.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Start Time Validation**: We’ve added a fix to check the start time and prevent the selection of times that have already passed for today. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Submit Button Color Issue Resolved**: We’ve fixed an issue where the color of the "Send" button wasn’t saving correctly after a second click on dates, when the range dates selection mode was enabled. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Date/Time Hints Display**: Resolved an issue where date/time hints were not showing in the booking form if no date was selected. Previously, a "0" would display if only the time was selected. (10.5.2.7)' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>

*/
	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_6( $obj ){

	$section_param_arr = array( 'version_num' => '10.6', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Structured Settings Dashboard!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Structured Settings Dashboard**: We’ve introduced a new, organized settings dashboard with brief descriptions for each feature. This update makes it easier to find and configure the settings you\'re looking for.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.6/wp_booking_calendar_settings_dashboard_04.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	// </editor-fold>

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Refactored Code**: We’ve refactored the code for improved efficiency and maintainability.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Email Deliverability**: The "From" email address is now automatically set to the website\'s "Administration Email Address" for all newly activated regular users. This change helps prevent emails from being marked as spam when the user\'s email is not from the website domain. *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Translation**. Local German translation update' ) ); ?></li>
				</ul>
				<!--
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>
				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>
				-->
			</div>

		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Calendar Behavior**: We’ve removed the highlighting of days in the calendar when the mouse cursor moves outside the calendar container for a smoother user experience. (10.5.2.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Timeline Navigation**: We’ve fixed an issue where the dropdown list would auto-close after selecting the start date in the navigation panel on the Timeline view. (10.5.2.1)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Page Workflow**: Notice messages from other plugins are now hidden in the header on Booking Calendar pages. This prevents interruptions to the normal workflow caused by messages from other plugins. (10.5.2.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Start Time Validation**: We’ve added a fix to check the start time and prevent the selection of times that have already passed for today. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Submit Button Color Issue Resolved**: We’ve fixed an issue where the color of the "Send" button wasn’t saving correctly after a second click on dates, when the range dates selection mode was enabled. (10.5.2.3)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Date/Time Hints Display**: Resolved an issue where date/time hints were not showing in the booking form if no date was selected. Previously, a "0" would display if only the time was selected. (10.5.2.7)' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_5( $obj ){

	$section_param_arr = array( 'version_num' => '10.5', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );

	 //$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this
	
	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
<!--			<h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Added New Modern Calendar Themes featuring rounded day cells' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing a Fresh Calendar Color Skin! We\'ve added the new \'24_9__light\' skin featuring rounded day cells for a modern and sleek look.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fresh Color Scheme! We\'ve added the "24_9__light_square_1" color scheme for the "24_9__light" Calendar Skin, giving your calendar a stylish new look with square rounded day cells.' ) ); ?></li>
					<!--li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fresh Color Scheme! We\'ve added the "24_9__light_violet_1" color scheme for the "24_9__light" Calendar Skin, giving your calendar a stylish new look.' ) ); ?></li-->
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__appointments__bookings_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<div style="font-size:10px;text-align: center">Image from  paid versions of Booking Calendar</div>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__changeover_days__bookings_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<div style="font-size:10px;text-align: center">Image from  paid versions of Booking Calendar</div>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Flexible Customization of Calendars' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Easy Configuration of Calendar Skin Parameters! Now you can easily customize your calendar skin using CSS parameter variables, including day cell radius, various colors, and other settings. This feature allows you to create your custom calendar skin quickly and efficiently.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__calendar_skins_setup_02.gif' ) ); ?>" style="margin:10px 0;width:auto;max-height: 280px;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Dark Calendar Theme' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing a Dark Calendar Theme! We\'ve added a new dark calendar theme based on the \'24_9__light\' skin, featuring rounded day cells for a sleek and modern look.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__dark_theme1.png' ) ); ?>" style="margin:10px 0;width:auto;max-height: 300px;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Updated Booking Form Fields Setup' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Booking Form Fields UI! We\'ve revamped the interface for setting up booking form fields, with refactored code and updated UI elements. The new design is cleaner and more intuitive, providing a smoother user experience.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We\'ve added new field blocks for the Calendar, Captcha, and Send button. You can now organize the order of these fields in the booking form and customize their labels or titles to suit your needs.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__simple_forms__setup_03.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Dashboard Statistic Options' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'We\'ve added new statistic options to the Dashboard Agenda, including:' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Check in - Today' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Check out - Today' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Check in - Tomorrow' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Check out - Tomorrow' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__dashboard_statistic.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Timeline View' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Multiple time slot bookings in the admin panel are now displayed with a small margin, offering a clearer and more organized layout.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__timeline_week_view_one_resource.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Create and Use Multiple Custom Booking Forms in **Simple Form Setup** mode' ) ); ?></h3>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.5/wp_booking_calendar__custom_forms_in_simple_form_setup_02.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Create **Multiple Custom Booking Forms**! You can now create and use multiple custom booking forms while in "**Simple Form Setup**" mode. This enhancement offers a quicker and easier way for users to configure their booking forms.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced UI for Advanced Form Mode! We\'ve updated the UI of booking forms while in "Advanced Form" mode. The code has been refactored, and toolbar UI elements have been refreshed for a smoother setup experience.' ) ); ?></li>
					<li style="font-size:0.9em;"><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Ths feature available in the Business Medium or higher versions.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php



	// </editor-fold>
if (1){
	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Under the Hood: New Hook Added! We\'ve introduced a new hook that triggers after showing cost/dates hints. You can bind this event using the following JavaScript:' ) ); ?></li>
					<li><code>jQuery(".booking_form_div").on('after_show_cost_hints', function(event, resource_id) {
    // Your code here
});</code></li>
				</ul>
				<!--
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>
				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>
				-->
			</div>

		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved Compatibility Issue! We\'ve fixed a potential issue that could occur when downgrading from the Paid to the Free version while using the \'Range Days\' selection mode, which is not supported in the Free version.' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>
}

	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_4( $obj ){

	$section_param_arr = array( 'version_num' => '10.4', 'show_expand' => !false );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<!-- <h2>--><?php //echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?><!--</h2>-->
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Modern Calendar Themes and More!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing a Modern Calendar Skin! Try the new \'Light 24_8\' skin for a fresh, contemporary look.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Discover 10 New Color Schemes for the \'Light 24_8\' Calendar Skin! Customize your calendar with a variety of fresh, modern color options.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing a Modern Time Picker Skin - \'Light 24_8\'.' ) ); //sleek and contemporary look.
						?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.4/wp_booking_calendar_modern_light_time_slots_booking_08.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Change Over Days in New Calendar Themes!' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Change Over Days in new calendar themes to better show split-day bookings.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Range Days Selection! When the range days selection mode is activated, the check-in and check-out dates now have a stronger color than the middle dates. This improvement makes it clearer for customers to identify their check-in and check-out dates.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<!--img src="<?php echo esc_attr( $obj->section_img_url( '10.4/wp_booking_calendar__change-over-days-bookings--split-days.png' ) ); ?>" style="margin:10px 0;width:98%;" /-->
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.4/wp_booking_calendar__change_over_days__split_days_02.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Updated Timeline Views and Booking Listing pages' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The Timeline Views and Booking Listing pages have been updated. They now feature a clearer and more sleek user interface.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.4/wp_booking_calendar_timeline_view_month.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Easy Configuration and Publishing Booking Calendar shortcodes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added a configuration shortcode button at WP Booking Calendar > Publish menu page. This makes it simple to configure and publish booking forms, availability calendars, or timelines into existing or new pages.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.4/wp_booking_calendar_publish_booking_form.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php



	// </editor-fold>

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Organized Calendar Skins! Defined groups for current and legacy calendar skins in the drop-down list on the Settings > General page in the "Calendar" section for easier navigation.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Simplified Booking Form Setup! During the initial installation of the plugin, the default structure of the booking form is now set to \'Form at right side of calendar\'.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Typography and Styles! Updated typography and styles for the booking resources select-box in the booking resources selection shortcode.' ) ); ?></li>
				</ul>
				<!--
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>
				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>
				-->
			</div>

		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Accurate Preview Updates! If a custom calendar skin is used in \'/wp-content/uploads/wpbc_skins/\', the preview now correctly updates in the \'Color Theme\' section on the Settings > Booking Form page.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull;  Correct Availability Settings! Resolved an issue with defining availability on the WP Booking Calendar > Availability > Days Availability page, especially when booking resources were previously deleted and only one resource remains.' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_3( $obj ){

	$section_param_arr = array( 'version_num' => '10.3', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Calendar Overview and Timeline Views, and more...' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<!--h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Updated Timeline View' ) ); ?></h3-->
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; We have updated the Calendar Overview and Timeline views to improve readability. Timelines now have a minimum width for day/time cells, making bookings easier to read. Additionally, you can scroll horizontally to search for specific date and time intervals.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Bookings View in Calendar Overview and Timeline! Booking bars now have a transparent background, making it easier to identify specific dates under the booking pipeline.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Typography in Bookings View! The typography in the Calendar Overview and Timeline has been updated for a cleaner and more modern look.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.3/wp_booking_timeline_scroll_06.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Change Over Days in Timeline Views!' ) ); ?></h3>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.3/wp_booking_calendar__timeline__change_over_01.png' ) ); ?>" style="margin:10px 0;width:98%;" />
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; In the pro versions, Timeline and Calendar Overview can now display change over days (triangles for check-in/out dates). This feature makes it easier to identify check-in/out dates and provides a better overview of bookings in the Timeline if your system uses the change over days functionality.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Option to Remove Booking Summary Details in Export! Now you can choose to exclude booking summary details when exporting bookings using the "Add to Google Calendar" button. Enable this option on the WP Booking Calendar > Settings > Sync > "General" page.' ) ); ?></li>
				</ul>
				<!--
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Form Times Templates! We\'ve improved the form times templates for a better user experience.  *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated the View of the "Back to Super Admin" Button! When a super booking admin user simulates a login as a "Regular User," the button\'s appearance has been enhanced for clarity. (10.1.5.2) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>

				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>
				-->
			</div>

		</div>
<!--
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Bootstrap Icons to the latest 1.11.3 Version for enhanced visual appeal and consistency.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed Deprecated BS Glyph Font for a cleaner and more modern design.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed Deprecated wpbc_vars.js for improved performance and maintainability.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The option \'Using BootStrap CSS for the form fields\' has been deprecated and moved to the Settings General page in the "Advanced" section for better organization.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added support for the WP Rocket plugin by excluding it from JavaScript Delay execution, ensuring smoother performance.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added a new hook \'wpbc_visitor_action__booking_trash\' for when visitors cancel their own booking. Example usage:: function your_cust_func_wpbc_visitor_action__booking_trash ( $booking_id, $resource_id ) { /* Your code here */ } add_action( \'wpbc_visitor_action__booking_trash\', \'your_cust_func_wpbc_visitor_action__booking_trash\', 100, 2 ) );  (10.2.0.3)' ) ); ?></li>
				</ul>

			</div>
		</div>
-->
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fix: Displaying Greyed (Disabled) Options! Fixed an issue where greyed-out (disabled) options, such as booked time slots, were not showing correctly in select boxes. ' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fix: Resolved Conflict with \'MIXITUP - A CSS3 and JQuery Filter & Sort Plugin\'! Fixed the issue causing the error: "Uncaught TypeError: time_fields_obj.times_as_seconds is undefined. ' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved Start/End Time Invalid Issue! Fixed the issue where Start/End Time was invalid in some configurations with range days selection mode and change-over days.' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_2( $obj ){

	$section_param_arr = array( 'version_num' => '10.2', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Explore Advanced Configurable Booking Confirmation, Enhanced Booking Form Typography, and more...' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Customizable Booking Confirmation Section' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Customizable Confirmation Section! Now you can configure what to display in the "Confirmation Window" after a booking is created. Enable or disable content and configure it with a list of shortcodes that will be shown in the "Personal Information" and "Booking Details" sections of the Booking Confirmation window.' ) ); ?></li>
				</ul>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.2/wp_booking_calendar__booking_confirmation_setup_04.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Typography for Booking Forms' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Typography for Booking Forms! The typography for booking form elements has been updated to provide a cleaner interface and to prevent CSS conflicts with themes.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.2/wp-booking-calendar_typography-01.png' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced of Time Slots Bookings' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing the "Use Selected Times for Each Booking Date" Option! This feature is available when \'Multiple Days\' selection mode is activated. It allows you to use the **selected times as booked time slots for each selected date**. If not enabled, the selected times will be used as the start time for the first date and the end time for the last date, with all middle dates being fully booked. Enable this option on the Settings > General page in the "Calendar" section. (10.1.5.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing the \'Partially Booked\' Calendar Legend Item! You can now configure this item in the calendar legend. Enable and customize it on the Settings > General page in the "Calendar" section under the \'Show legend below calendar\' option. (10.1.5.5)' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.2/wp_booking_calendar__booking_times_in_several_days_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Shortcode' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing the [only_dates] Shortcode for Email Templates! This shortcode allows you to insert only booking dates without times in your email templates. (10.1.5.6)' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<?php // Free ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added compatibility for WordPress 6.6' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added Thin Scroll Bar to Popovers! In the Calendar Overview and Timeline, a thin scroll bar is now included in popovers to handle long booking details or details for multiple bookings.' ) ); ?></li>
				</ul>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Form Times Templates! We\'ve improved the form times templates for a better user experience.  *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated the View of the "Back to Super Admin" Button! When a super booking admin user simulates a login as a "Regular User," the button\'s appearance has been enhanced for clarity. (10.1.5.2) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Spinners for Cost and Date Hints! The spinners in the booking form for cost and date hints have been updated for a smoother and more intuitive user experience. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>

				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Korean Translation! The translation has been updated and is now 96% complete, courtesy of modelaid.' ) ); ?></li>
				</ul>

			</div>

		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Bootstrap Icons to the latest 1.11.3 Version for enhanced visual appeal and consistency.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed Deprecated BS Glyph Font for a cleaner and more modern design.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed Deprecated wpbc_vars.js for improved performance and maintainability.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The option \'Using BootStrap CSS for the form fields\' has been deprecated and moved to the Settings General page in the "Advanced" section for better organization.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added support for the WP Rocket plugin by excluding it from JavaScript Delay execution, ensuring smoother performance.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added a new hook \'wpbc_visitor_action__booking_trash\' for when visitors cancel their own booking. Example usage:: function your_cust_func_wpbc_visitor_action__booking_trash ( $booking_id, $resource_id ) { /* Your code here */ } add_action( \'wpbc_visitor_action__booking_trash\', \'your_cust_func_wpbc_visitor_action__booking_trash\', 100, 2 ) );  (10.2.0.3)' ) ); ?></li>
				</ul>

			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Adjusted the height of booking form fields to ensure compatibility with certain themes.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of search results not displaying on a separate page when custom search filter options are used in the search form. (10.2.0.2) *(Personal, Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added the ability to uncheck the exclusive checkbox in group checkbox options for greater flexibility. (10.1.5.1) *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of popovers not displaying in the timeline on the front-end during initial page loading when a cache plugin is activated. (10.2.0.4)' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_1( $obj ){

	$section_param_arr = array( 'version_num' => '10.1', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2024-06-01 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Explore Enhanced UI, Advanced Search <sup style="font-size:10px );top: -8px;position: relative;left: -4px;">Pro</sup>, and more...' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New: Booking Form Setup' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Experience an updated UI for booking form configurations. Define booking form layout, including centering, and placing the form next to the calendar. Adjust the size and alignment of the form, easily create or edit new fields, define color themes, enable CAPTCHA, and much more.' ) ); ?></li>
				</ul>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.1/wp-booking-calendar__simple_booking_form_setup_01.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Booking Form Preview' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now you can see all changes in real-time on the same page at WP Booking Calendar > Settings > Booking Form, immediately after making adjustments to your booking form.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.1/wp_booking_calendar__simple_booking_form_preview_02.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced Admin Panel Calendar Overview' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Experience an **updated UI on the Calendar Overview page**, optimizing space for clear viewing of bookings. The new design minimizes space for UI elements, maximizing the focus on bookings and enhancing the user experience for managing them. This redesign not only improves user experience but also ensures seamless usability across all devices, including mobile, allowing for increased visibility of bookings.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.1/wp_booking_calendar__calendar_overview_04.gif' ) ); ?>" style="margin:10px 0;width:98%;" />
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Display first and last names in the booking pipelines on the Calendar Overview page to simplify finding specific bookings.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Updated Interface for Email Settings' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'The Settings > Emails page now features an updated, user-friendly interface. The email template selection menu is conveniently located in the left navigation panel, clearly showing enabled and disabled templates. The configuration UI for email subjects and templates has been improved, with a revamped help section that highlights available shortcodes more effectively and clearly.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.1/wp-booking-calendar__email_settings_01.png' ) ); ?>" style="margin-top:14px;" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php


	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Changes in Premium Versions' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<ul>
					<li><br><h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Completely new Search Availability Engine' ) ); ?></h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; This powerful feature allows you to search for available Dates and Times based on existing bookings, unavailable dates, and other criteria defined in the search form. Enjoy a more efficient and precise search experience tailored to your needs.' ) ); ?></li>
				</ul>
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<li><h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Searchable Resource Setup' ) ); ?></h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; This feature allows you to easily customize search availability for booking resources. Personalize search visibility and attributes by customizing options like **summary text and thumbnail images** to enhance user engagement in search results. Additionally, configure specific **parameters for resource filtering in search forms**, providing users with targeted and relevant search options.' ) ); ?></li>
				</ul>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.1/wp_booking_calendar__search_availability_05.gif" style="margin:10px 0;width:98%;"/>
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<li><h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Search for Specific Time Slots' ) ); ?></h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now you can search for specific time slots on particular dates or even a range of dates. This is a fantastic **feature for appointment-based businesses**, allowing clients to **find available time slots for different service providers**. Imagine searching for an available hair stylist, doctor, or other service provider on a specific date and time. You can even set an approximate date range (+/- N days from the desired date) to find appointments within a specific time slot.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.1/wp_booking_calendar__search_availability_time_01.gif" style="margin:10px 0;width:98%;"/>
			</div>
			<div class="wpbc_wn_col">
				<ul>
					<li><h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Dark Theme' ) ); ?></h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; New **Dark Theme** for Search Form and Results! When you switch the "Color Theme" to "Dark" at WP Booking Calendar > Settings > General page in the "Form Options" section, the system will automatically update the colors to dark in both the Search Form and Search Results for a seamless fit with your website design.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.1/wp-booking-calendar__search_availability_dark_01.png" style="margin:10px 0;width:98%;"/>
			</div>
		</div>

		<div class="wpbc_wn_section">
			<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Search Availability' ) ); ?></h3>
			<div class="wpbc_wn_col">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Streamlined Date Selection! After selecting a date in the Check In field in the search availability form, the **Check Out calendar** now **opens automatically** for a smoother booking experience.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Search Interface! The Settings page now features an **updated search interface** with a streamlined help section, making it easier to find the shortcodes you need quickly and efficiently.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **6 New  Search Form Templates**! Now available at WP Booking Calendar > Settings > Search page, offering more customization options for your search forms.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Discover **4 New Search Result Templates**, to suit your website\'s style and user preferences.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now, the search form and search results **support "Simple HTML tags"** for easier configuration of the search form structure. Example: Create a row with 2 columns using <code>&lt;r&gt; &lt;c&gt;..&lt;/c&gt; &lt;c&gt;..&lt;/c&gt; &lt;/r&gt;</code>.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Integrate **search filter options directly into your search results layout**. If specific filters are set in the **Search Filter** section of the **Searchable Resource** page, such as <code>location = Spain</code>, you can now incorporate corresponding shortcodes like <code>[location]</code> into your Search Results Layout. Tailor your search results to match your defined criteria effortlessly.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New Shortcodes for Search Forms**. Easily search for the **number of items to book**, **extend search dates** by a range, and **search for available times** using new shortcodes: <code>[search_quantity "1" "2" "3"]</code>, <code>[search_extend "2"]</code>, and <code>[search_time "Full Day@@" "10:00 - 14:00" "15:00 - 16:00"]</code>.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Configure \'**Search Filter Options**\' shortcodes. Now you can **filter search results by various criteria and parameters**. Example shortcodes include filtering by amenity, maximum visitors, and location: <code>[selectbox amenity "Any@@" "Parking" "WiFi"]</code>, <code>[selectbox max_visitors "Any@@" "1" "2" "3"]</code>, and <code>[selectbox location "Any@@" "Spain" "France"]</code>.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **New Search Results Shortcodes**. Customize your search results with a range of new shortcodes: <code>[search_time]</code>, <code>[search_time_check_in]</code>, <code>[search_time_check_out]</code>, <code>[search_result_title]</code>, <code>[search_result_info]</code>, <code>[search_result_image]</code>, <code>[search_result_image_url]</code>, <code>[search_result_url]</code>, <code>[search_result_button "Book Now"]</code>, <code>[resource_title]</code>, <code>[resource_id]</code>, <code>[resource_capacity]</code>, <code>[available_count]</code>, <code>[resource_cost]</code>, <code>[search_check_out_plus1day]</code>.' ) ); ?></li>
				</ul>
			</div>

		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhanced UI for Daily Costs Page' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The Daily Costs page has been redesigned to provide users with a clearer and more intuitive interface. This update introduces standardized resource lists, action sections, labels, and enhanced search and sorting capabilities for booking resources by various parameters.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now, users can easily identify daily cost parameters such as "per 1 day" or "per 1 night" next to each booking resource. This feature is particularly useful in the MultiUser version, as different regular users may have different configurations for these parameters for their own booking resources (calendars).' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.1/wp_booking_calendar__daily_costs_ui_01.gif" style="margin:10px 0;width:98%;"/>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Updated Interface for Payment Setup Settings' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'The Settings > Payment Setup page now features an updated, user-friendly interface. The payment gateways menu located in the left navigation panel, clearly showing enabled and disabled payment systems. The configuration UI for payment systems has been improved.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.1/wp-booking-calendar__payment_setup_settings_01.png' ) ); ?>" style="margin-top:14px;" />
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Other New Features**' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Pre-Check-in Date Hint Shortcode:** Introduced the <code>[pre_checkin_date_hint]</code> shortcode, which shows the date that is N days before the selected check-in date.  You can select the number of days for the [pre_checkin_date_hint] shortcode at the WP Booking Calendar > Settings General page in "Form Options" section". (10.0.0.31) *(Business Medium/Large, MultiUser)*' )  ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added the ability to **use attributes in "Simple HTML"** tags. Now you can use attributes like **style** and **class** in "Simple HTML tags" for greater customization. Example: <code>&lt;r class=\'my_css_class\'&gt; &lt;c style=\'align-self:last baseline;\'&gt;..&lt;/c&gt; &lt;/r&gt;</code>' ) ) . '</li>'
				 . '</ul>';
				?>
			</div>
		</div>

	</div><?php

	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>
					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; By default, the "Aggregate only bookings" option is disabled in the shortcode configuration dialog. (10.0.0.6)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now, from the WP Booking Calendar > Resources page, you can easily simulate login to a regular user. The "Simulate login" button is located next to each booking resource belonging to "regular users" in the "Owner" section. (10.0.0.15) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Renamed the WP Booking Calendar > Settings > Payment Gateways page to **Payment Setup** page. *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Eliminated parameters "Title of Search Results" and "Nothing Found Message" from the search form shortcode. You can now configure this text at the WP Booking Calendar > Settings > Search page. (10.0.0.41)' ) ); ?></li>
				</ul>

				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Dutch Translation Update**. Translation has been updated, reaching 98% completion, courtesy of Han van de Graaf.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Hungarian Translation Update**. Translation has been updated, reaching 99% completion, courtesy of VinczeI.' ) ); ?></li>
				</ul>

			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Free version' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added support of new shortcodes of booking creation for emails on WP Booking Calendar > Settings > Emails page: [creation_date], [creation_year], [creation_month], [creation_day], [creation_hour], [creation_minutes], [creation_seconds] (10.0.0.34)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Smart Days Selection during booking editing. The system now selects dates in the calendar after all dates are loaded. If multiple non-consecutive dates are selected but a different selection mode is defined later, the system sets Multiple Days selection mode to ensure correct selection. If Single Day selection mode is enabled but the booking has multiple dates, the system switches to Multiple Days selection mode during the edit. (10.0.0.50)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; When adjusting the number of months in \'Setup Size & Structure,\' the overall \'Visible months\' count will automatically update in the Shortcode Configuration dialog. (10.0.0.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now, you can define to scroll through the calendar for a period of 1.5 years (18 months). (10.0.0.11)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced parameter sanitization for improved security and stability. (10.0.0.12)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced the view of submenu items on settings pages for improved navigation and usability.' ) ); ?></li>
				</ul>
			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introduced helper JavaScript for selecting calendar dates via keyboard (tab selection). Please note, additional CSS adjustments may be needed in specific calendars to display focus elements correctly. (10.0.0.19)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; New JS event generated after creation of booking: \'wpbc_booking_created\'. To catch this event use code: jQuery( \'body\' ).on( \'wpbc_booking_created\', function( event, resource_id, params ) { ... } ) ); (10.0.0.30)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Inline JavaScript variables needed for all calendars are now defined after loading the wpbc_all.js script. (10.0.0.43)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added an action hook that triggers when all booking data is loaded in the calendar: jQuery( \'body\' ).on( \'wpbc_calendar_ajx__loaded_data\', function( event, resource_id ) { ... } ) ); (10.0.0.44)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Replaced the translation function apply_bk_filter( \'wpdev_check_for_active_language\', $text ) with wpbc_lang( $text ) function. (10.0.0.46)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added a new internal function wpbc_auto_select_dates_in_calendar() for automatic date selection. This function simulates clicks on the calendar and, based on check-in/out dates and various conditions for range day selection, it can automatically select or not select dates in the calendar. (10.0.0.47)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added a new internal function to automatically fill booking form fields from URL parameters. For example, \'?wpbc_auto_fill=visitors1^2~children1^1\' will auto-fill the specified fields. (10.0.0.47)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added the ability to auto-select dates in the calendar based on URL parameters. For example, ?wpbc_select_check_in=2024-05-16&wpbc_select_check_out=2024-05-19&wpbc_select_calendar_id=1 will automatically select the specified dates. (10.0.0.48)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed most global JavaScript variables in the plugin and defined them under the _wpbc JS variable. Redefined the loading of JavaScript variables and some JS files.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; By default, when saving a booking to the system, the plugin rechecks availability for specific dates. During this checking process, if you used the aggregate parameter, the system utilizes the "Aggregate only bookings" parameter value to prevent potential issues related to errors such as \'These dates and times in this calendar are already booked or unavailable. ... Booking cannot be saved on this date ...\' (10.0.0.7)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introduced a hook that executes upon the deletion of booking resources: do_action( \'wpbc_deleted_booking_resources\', $bulk_action_arr_id ) ); (10.0.0.35)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Shortcode  [bookinglooking] for help with search availability feature become deprecated and removed from Booking Calendar. *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Deprecated the following shortcodes in the Search Form: [search_visitors], [additional_search "3"], [search_category], [search_tag]' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Deprecated the following shortcodes in the Search Results: [booking_resource_title], [link_to_booking_resource "Book now"], [book_now_link], [num_available_resources], [booking_featured_image], [booking_info], [booking_resource_id], [standard_cost], [max_visitors]' ) ); ?></li>
				</ul>

			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Correctly display a white background for the header of admin pages on small resolutions where the top menu shifts to the second row. (10.0.0.13)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; If you set a number of months to scroll from today on the WP Booking Calendar > Settings General page, the system will show the last scrolled month\'s exact date as available, based on today\'s date. (10.0.0.26)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Addressed an issue with exporting booking links to Google Calendar when the start time is 00:00 and the end time is a specific time. In this scenario, the system incorrectly set the date one day lower than it should have. (10.0.0.28)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Improved top menu border and background colors for a sleeker look. This update ensures a more cohesive appearance, especially when other plugins or themes display warning messages at the top of the page. The background gradient of the top menu will now appear more organic and clear. (10.0.0.32)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Display the green bar correctly based on the WordPress timezone rather than GMT time on the Calendar Overview page in Day view mode. (10.0.0.40)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Compatibility with jQuery 3.0 replaced deprecated .unbind() to .off() and .andSelf() to .addBack() methods. (10.0.0.45)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved an issue where the end time in the booking form was set to 24:00 (midnight), causing a booking date issue like "30 Nov -0001." The system now adjusts the end time to 23:59 if it is set to 24:00.  (10.0.0.49)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved an issue where the rangetime field was incorrectly selected during booking editing in the free version when using the AM/PM time format. (10.0.0.52)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Pro Versions:**' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated styles for the message "There is a new version of Booking Calendar available" in the WordPress Plugins menu. (10.0.0.20)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the error message: \'These dates and times in this calendar are already booked or unavailable...\' that appeared when using a custom booking form with time slots different from the standard booking form. (10.0.0.10) *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of incorrectly displaying status for change-over dates when there were check-in/out dates with pending and approved statuses on the same date in booking resources with specific capacities. (10.0.0.2) *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of incorrectly parsing "simple HTML tags" in custom booking forms on the Booking Listing page. (10.0.0.3) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Corrected the display of booking resource ID = 1 in WordPress Blocks when configuring the booking resource differently from the default resource. (10.0.0.16) *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved PHP Fatal error that occurred in PHP 8.1 or 8.2 when using complex arithmetic operations at the WP Booking Calendar > Prices > Form Options Costs page: Uncaught TypeError: Unsupported operand types: null + string in ../wpbc-calc-string.php on line 68. (10.0.0.23) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Addressed the warning message about an undefined array key "cost" on line 624 of the page-resources.php file. *(Personal)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of the "bank" selection table not displaying correctly for the iDeal payment system on mobile devices in the payment form. (10.0.0.27) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of the search field for Seasons being hidden on the WP Booking Calendar > Prices > Seasons page. (10.0.0.29) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of the search field for Coupons being hidden on the WP Booking Calendar > Prices > Discount Coupons page. (10.0.0.29) *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of the search field for Users being hidden on the WP Booking Calendar > Settings > Users page. (10.0.0.29) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Improved the styles for date descriptions in season filters on the WP Booking Calendar > Availability > Seasons page. (10.0.0.33) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed the selection filter for [visitors] in the old legacy search engine to accurately display search results. (10.0.0.36) *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Corrected the email notification sent when a booking was duplicated at the WP Booking Calendar > Add Booking page. It now sends a New booking email instead of a Modification email. (10.0.0.42)' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_10_0( $obj ){

	$section_param_arr = array( 'version_num' => '10.0', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2023-11-06 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Explore Simplified Integration and Enhanced Management' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Easy Shortcode Integration' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Discover the simplicity of integrating Booking Calendar shortcodes into your pages. Our updated UI for the configuration popup dialog simplifies the process of inserting booking forms, availability calendars, and other Booking Calendar shortcodes. Configuring all parameters is now super easy with our new structured wizard-style configuration dialog. Add the Booking Calendar to your page by using Booking Calendar Blocks in the WP Block Editor or by clicking on the Booking Calendar icon in the WP Classic Editor.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.0/wp_booking_calendar_insert_shortcode_01.png' ) ); ?>" style="margin-top:55px" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Unavailable Dates legend item' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Easily configure **Unavailable Dates legend item** under the calendar at WP Booking Calendar > Settings General page in the "Calendar" section.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '10.0/wp_booking_calendar_unavailable_legend_01.png' ) ); ?>" style="margin-top:0px" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Changes in Premium Versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Discover an enhanced user interface for the Resources page' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**New** **UI Design of Resource Page**. Easily toggle between table header tabs to view specific information, such as the shortcode for embedding into pages, costs, default form, or parent/child relation for defining booking resource capacity. The interface is now clearer and more straightforward for a seamless experience *(Personal, Business Small/Medium/Large, MultiUser)*.' ) . '</li>'
					. '</ul>' );
				?>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.0/wp_booking_calendar_resources_page_03.gif" />
				<?php
				echo '<ul>'
					  	. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Added **search booking resources** field to Options toolbar for easy searching by ID or Title keywords (9.9.0.11) *(Personal, Business Small/Medium/Large, MultiUser)*.' ) ) . '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; You now have the ability to **save the number of booking resources per page** directly from the Booking > Resources page (at pagination section). (9.9.0.22) *(Personal, Business Small/Medium/Large, MultiUser)*' ) ) . '</li>'
					. '</ul>';
				?>
			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Explore the super ease of configuring "Conditional Days Selection"' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Set the **number of days** for selection based **on a specific weekday**.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Determine the **number of days** for selection **in a specific season**.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Define the **number of days** for selection **starting from a specific date**.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Choose **weekdays as start days** for selection **during a specific season**.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '*(Business Medium/Large, MultiUser)*' ) ). '</li>'
					. '</ul>';
				?>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>10.0/wp_booking_calendar_days_selection_04_2000px.gif" />
			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**New shortcode** for **displaying taxes or additional fees based on the total booking cost**.' ) ); ?></h3>
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'This **[total_cost]** shortcode  is used for displaying additional cost hints without affecting cost calculations. Since the total cost has already been calculated at this stage.' ) . ' '
						 		 . wpbc_replace_to_strong_symbols( 'For example, if you want to **show the tax cost as 20% inclusive part of the total booking cost**, set up your booking form fields like this: ' ) //. '</li>'
						  . '<code>&lt;div style="display:none;"&gt; [checkbox tax_fee default:on ""]&lt;/div&gt; Tax: [tax_fee_hint]</code></li>'
						//. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'The first shortcode, [checkbox tax_fee default:on ""], is crucial for cost calculation, while the second shortcode, [tax_fee_hint], reveals the cost hint.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'Now, define the additional cost for tax_fee at WP Booking Calendar > Prices > Form Options Costs page: ' ) . '<code>tax_fee = ( [total_cost] * 0.2 )</code></li>'
						. '<li>' . wpbc_replace_to_strong_symbols( ' *(Business Medium/Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Track Changes: **Booking Edit Notification**' ) ); ?></h3>
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( '&bull; Whenever a booking is edited, a note stating **\'The booking has been edited\'** will be generated for the booking. This note also contains the URL of the page where the user made the modifications. With this feature, you gain control over tracking changes to booking details.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( '&bull; Now, booking notes such as "Imported from Google Calendar," "Payment section displayed," "Total cost manually entered," "Automatically calculated cost," etc., will be **saved only if the "Logging actions for booking" option is activated** in the WP Booking Calendar > Settings General page in the "Admin Panel" section.' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>


	</div><?php



	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?><hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Pro Versions' ) ); ?></h3>
				<ul>

					<?php // Pro ?>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Sage/Opayo gateway becoming a payment product under the Elavon brand. Update name of payment gateway from SagePay to **Opayo - Elavon**. For more information, visit: https://www.elavon.co.uk/resource-center/news-and-insights/opayo-migration-faqs.html  (9.9.0.34) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Booking Management** - When editing or duplicating a booking in the admin panel, you will now be redirected to the Booking Listing page, where the specific booking will be displayed. (9.9.0.3) (9.9.0.38) *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Shortcode Functionality** - Resolve the "403 permission error" issue encountered when modifying booking forms on the WP Booking Calendar > Settings > Booking Form page by utilizing the **new shortcode [selectbox name "Option 1" "Option 2"]** instead of [select name "Option 1" "Option 2"]. This update addresses the problem faced on certain Apache servers with ModSecurity firewall, which affects specific POST requests when saving booking form configurations. *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Deposit Management** - Now, when a booking is made in the admin panel at WP Booking Calendar > Add booking page, the deposit amount in the cost field and notes section, as well as the total cost for the booking, will be updated accordingly. This feature requires displaying payment forms at the Booking > Add booking page in such cases. (9.9.0.21) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Import Functionality** - You can now utilize the option \'Trash / delete all imported bookings before new import\' in situations where imports are initiated via the Google Calendar API instead of .ics feeds. This feature provides greater control over managing imported bookings and ensures a smoother import process. Please note that this feature requires the installation of the **Booking Manager** plugin.  (9.9.0.25)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Payment Confirmation** - You now have the ability to **use the [booking_confirm] shortcode on the "Successful payment" page**, where users are redirected after making a payment via the Stripe or PayPal payment system. This allows you to configure the page to display a message such as \'Your payment for the booking has been successfully received. [booking_confirm]\', providing users with confirmation of their payment along with booking details. (9.9.0.37) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>

				</ul>
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements in Free version' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Ability to dismiss the booking statistic section at Booking > Settings General page in "Info / News" for improved admin panel loading speed. (9.9.0.8).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced **Performance** - To improve the speed of page loading in the admin panel, statistic functions will now run only on the Dashboard and Booking Calendar settings pages. This optimization ensures a smoother user experience by focusing resource-intensive tasks only where necessary. (9.9.0.40)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Prevent showing real preview in Elementor to reduce potential conflicts ); instead, show \'WP Booking Calendar Shortcode block\' (9.9.0.39).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Prevent execution of Booking Calendar shortcodes while editing posts/pages, reducing potential conflicts (9.9.0.39).' ) ); ?></li>
				</ul>

				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Customize showing booked time slots as inactive slots with a red background after selecting specific dates. Configure this in the ../{Booking Calendar Folder}/js/wpbc_time-selector.js file  by searching //FixIn: 9.9.0.2 (9.9.0.2).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Update the URLs for send transactions for Opayo - Elavon (former SagePay) to an Elavon domain on 31st March 2024. For more details, please refer to the following link: https://developer.elavon.com/products/opayo-forms/v1/test-and-live-urls-4633 (9.9.0.34) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Option to **Hide Payment Form After Visitor Edits Own Booking** - You now have the ability to define not to show the payment form after a visitor edits their own booking. This can be achieved by using the shortcode **[visitorbookingediturl]&is_show_payment_form=Off** in the New (visitor) email template at the WP Booking Calendar > Settings > Emails page. (9.9.0.35) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>

				<div style="clear:both;height:20px;"></div>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Hungarian Translation Update**. Translation has been updated, reaching 96% completion, courtesy of VinczeI.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **German Translation Update**. Translation has been updated, reaching 96% completion, courtesy of Wibias.' ) ); ?></li>
				</ul>

			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved possible PHP 8 incompatibility issue (9.9.0.4).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved issue of showing top messages (9.9.0.10).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Corrected the issue of incorrectly showing today\'s date as an unavailable date in the calendar on servers with a timezone different from UTC, especially in American timezones (9.9.0.17).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Addressed the issue of incorrectly showing bookings in Calendar Overview and Timeline when the timezone was incorrectly defined by themes (in functions.php file) or other plugins via incorrectly defined the timezone via this PHP functions: like date_default_timezone_set(\'America/Chicago\') ); Even it is not correct way of defining timezone, relative to https://make.wordpress.org/core/2019/09/23/date-time-improvements-wp-5-3/ . Now plugin correctly resolve this issue. (9.9.0.18).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Re-created the "Thank you" confirmation page (such  as https://server.com/wpbc-booking-received/) if the confirmation booking page was previously defined as follow: https://server.com/thank-you/ (9.9.0.27).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved fatal error:  Uncaught Error: Cannot access offset of type string on string in ../wpbc-class-timeline_v2.php on line 3100 (9.9.0.30).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Search Availability** Functionality Issue - An issue has been resolved in the Search Availability functionality. Previously, it was not possible to find pages with booking form shortcodes when the booking resource was defined using the \'resource_id\' parameter instead of \'type\'. This fix ensures that the search functionality now works as expected, allowing users to locate pages with booking forms accurately. (9.9.0.26) *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Correct Sorting Order for Booking Resources** - An issue has been addressed where the sorting order for booking resources in select-boxes was not defined correctly during the change of booking resource for a specific booking. This fix ensures that booking resources are displayed in the correct order. (9.9.0.23) *(Personal, Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Correction of Start Day Definition** - An issue has been **resolved where Sunday was incorrectly defined as the start day for selection** in specific seasons when using conditions in the shortcode. For example, when using a shortcode like [booking resource_id=1 options=\'{start-day condition="season" for="myHighSeason" value="0"},{select-day condition="season" for="myHighSeason" value="8,15,22,29"}\'], Sunday was inaccurately identified as the start day. This fix ensures that the start day for selection in specific seasons is correctly determined. (9.9.0.9)  *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Corrected Saving of Booking Dates** - An issue has been resolved where booking dates were incorrectly saved if the option "Set check out date as available" was activated at the WP Booking Calendar > Settings General page in the "Calendar" section. (9.9.0.19) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Corrected Redirect Issue for Failed Payments** - An issue has been addressed where users were incorrectly redirected to the home page instead of the failed URL when **payments failed via iDeal (Buckaroo)**.  (9.9.0.24) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved an issue causing a possible PHP Fatal error has been resolved. This error, specifically the Uncaught TypeError: Unsupported operand types: null + string, occurred in the wpbc-calc-string.php file when non-standard shortcodes with arithmetic operations were used on the WP Booking Calendar > Prices > Form Options Costs page. (9.9.0.28) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Corrected Minimum Date Selection Issue** - An issue has been addressed where the minimum number of dates in the calendar was incorrectly selected if a user clicked twice on the same date, and the system had a minimum number defined higher than 1 day. (9.9.0.29) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Corrected End Time Selection Issue** - An issue has been resolved where, if the option "Use selected times for each booking date" was deactivated, the system incorrectly blocked end times when a user selected multiple dates. For example, if the start time was booked from 00:00 to 15:00, the system would block the end time until 15:00, even if the end date was fully available. (9.9.0.31) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Corrected Display Issue with Check-In/Out Dates** - An issue has been resolved where the check-in/out dates (triangles) were incorrectly shown as fully available dates in booking resources with specific capacity. This occurred when one of the child booking resources had a change-over dates situation rather than being fully booked.  (9.9.0.33) *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_9_9( $obj ){

	$section_param_arr = array( 'version_num' => '9.9', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2023-11-06 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Explore Enhanced User Experience' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhancements in Booking Admin Panel UI' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Redesigned "Top Tabs" for page selection in the Booking Calendar Admin UI. This update enhances space utilization, resulting in a clearer and smoother interface for users.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '9.9/wp_booking_calendar_admin_panel_01.png' ) ); ?>" />
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Easy Booking Form Integration' ) ); ?></h3>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '9.9/wp_booking_calendar_publish_02.gif' ) ); ?>" />
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Now it\'s super easy to put your booking form into  the pages of your website. Explore the new  "Resource" menu in the free version,  where you can find \'Publish\' button or use the new \'Publish\' button on the Booking > Resources page in paid versions. Add the form to any page with a few clicks. Simple as that!' ) ); ?></li>
				</ul>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	</div>
	<?php

	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Changes in Premium Versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New PayPal Standard Checkout integration.' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'Integrated **PayPal Standard Checkout** payment gateway. Enjoy various payment methods, including **card payments and PayPal**. Choose from different designs for PayPal buttons. The system now automatically responds from  the PayPal, updating the booking status and payment status. *(Available in Business Small/Medium/Large, MultiUser versions).*' ) . '</li>'
					. '</ul>' );
				?>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.9/wp_booking_calendar_paypal_setup_03.gif" />
			</div>
		</div>
	</div>
	<?php



	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Improvements' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The Prices menu now includes options such as "Seasonal Rates" (formerly "Rates"), "Duration-Based Cost" (formerly "Valuation days"), "Partial Payments" (formerly "Deposit"), "Form Options Costs" (formerly "Advanced costs"), and "Payment Gateways." This centralized location allows you to manage all prices for bookings in one place.  *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The "Availability" section has a new structure and titles. Now, the configuration of all "Calendars Availability" is located in the "Availability" menu page and its sub-pages, including "Days Availability," "Season Availability" (formerly "Availability"), and "General Availability." This provides a centralized location to manage all availability settings for calendars. *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; The Resources menu now features an enhanced toolbar UI for creating new booking resources and managing options. This improvement provides a more user-friendly experience for resource management. *(Paid versions)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Implemented the addition of the "autocomplete" attribute to the booking form fields. This enhancement ensures correct autofill functionality in the Chrome browser when using autofill addons. *(Paid versions)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Show super booking admin  currency  at  front-end,  instead of regular user defined currency,  if was activated "Receive all payments only to Super Booking Admin account" option  at the Booking > Settings General page in "Multiuser Options" section.  (9.8.9.3) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Updated Top WordPress Bar menu for Booking Calendar (9.8.15.9)' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Features' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Easy weekdays selection in "Season Dates Filter". Now, you can easily  select specific weekday(s) in specific year and append or remove additional dates by using range "Dates Filter" at  Booking > Resources > Filters page. (9.8.6.3) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing the **Server Balancer**, a feature designed for low-level hosting systems with limited server resources. Useful for scenarios where multiple calendars need to load bookings simultaneously on the same page or when using resource selection shortcode for many calendars. Now, you have the ability to define the number of parallel requests sent to the server. A lower number of parallel requests minimizes the impact on the server but extends the time required to load all calendars. Calendars will send requests only after the previous request is finished. By customizing the number of parallel requests, you can find the optimal balance for your specific server, especially when using numerous calendars on the same page. Simply set the value of parallel requests at Booking > Settings General page in the Advanced section. (9.8.6.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Introducing General Import Conditions options for importing events into the Booking Calendar for all booking resources. You can now enable the option **Import if Dates Available** to import events only if dates are available in the source calendar. Additionally, the option **Import Only New Events** allows you to import only if the event has not been imported before. This last option replaces the deprecated "Force import" option and can be configured at Booking > Settings > Sync > "General" page in the "Import advanced" section.  (9.8.15.8)' ) ); ?></li>
				</ul>
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Now you can aggregate only \'**bookings**\' without including unavailable dates from the \'Booking > Availability > Days Availability\' page. If you use the \'**aggregate**\' parameter in the Booking Calendar shortcode and wish to include only bookings, utilize the new parameter: **options="{aggregate type=bookings_only}"**. For instance, in the shortcode example below, we aggregate only bookings: <code>[bookingselect type=\'3,4\' aggregate=\'3 );4\' options="{aggregate type=bookings_only}"]</code>  (9.8.15.10)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Ability to define inline checkboxes and radio buttons using <code>&lt;r&gt;&lt;c&gt;...&lt;/c&gt;&lt;/r&gt;</code> constructions. For this, use the new element: <code>&lt;r&gt;&lt;c&gt;&lt;div class="wpbc_row_inline"&gt;...&lt;/div&gt;&lt;/c&gt;&lt;/r&gt;</code>. Example #1:  <code>&lt;r&gt;&lt;c&gt;&lt;p class="wpbc_row_inline"&gt;&lt;l&gt;[checkbox terms "I accept"]&lt;a href="#"&gt;terms and conditions&lt;/a&gt;&lt;/l&gt;&lt;/p&gt;&lt;/c&gt;&lt;/r&gt;</code> Example #2:  <code>&lt;r&gt;&lt;c&gt;&lt;p class="wpbc_row_inline"&gt;&lt;l&gt;Fee: [checkbox somefee default:on ""]&lt;/l&gt;&lt;/p&gt;&lt;/c&gt;&lt;/r&gt;</code> Example #3:  <code>&lt;r&gt;&lt;c&gt;&lt;p class="wpbc_row_inline"&gt;&lt;l&gt;Club: [radio* clubanlass default:No "No" "Yes"]&lt;/l&gt;&lt;/p&gt;&lt;/c&gt;&lt;/r&gt;</code> (9.8.8.1)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Improved dates selection mode. Introducing a new JavaScript functions for defining simple customization of different date selections for various calendars. Find more information on <a href="https://wpbookingcalendar.com/faq/advanced-javascript-for-the-booking-shortcodes/">this page</a>.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; In case, if you want to wrap checkbox with text in label use parameter "label_wrap" in the shortcode construction: <code>[checkbox* terms label_wrap use_label_element "I Accept"]</code>. System make DOM construction such  as: <code>&lt;label for="cid"&gt;&lt;input class="wpdev-validates-as-required wpdev-checkbox" id="cid" type="checkbox" name="terms" value="I Accept"&gt;&nbsp;&nbsp;I Accept&lt;/label&gt;</code> (9.8.13.2)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; In case, if you want to use labels and checkbox separately,  please use shortcode construction: <code>[checkbox* terms use_label_element "I Accept"]</code>. System make DOM construction such  as: <code>&lt;input class="wpdev-validates-as-required wpdev-checkbox" id="cid" type="checkbox" name="terms" value="I Accept"&gt;&nbsp;&nbsp;&lt;label for="cid"&gt;I Accept&lt;/label&gt;</code>  (9.8.13.2)' ) ); ?></li>
				</ul>
			</div>
		</div>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved an issue where bookings with zero cost were not automatically approved when the option "Auto approve booking if booking cost is zero" was activated in the Booking > Settings General page under the "Auto Cancellation / Auto Approval" section.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved a fatal error: Uncaught TypeError: Unsupported operand types: string * int in ../core/sync/wpbc-gcal-class.php' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **German Translation Update**. Translation has been updated, reaching 94% completion, courtesy of Res Rickli' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **French Translation Update**. Translation has been updated, reaching 93% completion, courtesy of Res Rickli' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Italian Translation Update**. Translation has been updated, reaching 85% completion, courtesy of Res Rickli' ) ); ?></li>
				</ul>
			</div>
		</div>

	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}

function wpbc_welcome_section_9_8( $obj ){

	$section_param_arr = array( 'version_num' => '9.8', 'show_expand' => true );

	$obj->expand_section_start( $section_param_arr );


	//$obj->asset_path = 'http://beta/assets/';	// TODO: 2023-11-06 comment this


	// <editor-fold     defaultstate="collapsed"                        desc=" = F R E E = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = F R E E =
	// -----------------------------------------------------------------------------------------------------------------
	?><div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Experience a smoother and more efficient booking process' ) ); ?></h2>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Completely New  Availability / Capacity engine' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'The calendar now dynamically loads bookings with smooth animations, enhancing the overall speed and user experience.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'The system is now highly proactive in preventing double bookings. It checks availability at multiple stages, whether it\'s during calendar loading or when users submit bookings. This ensures that double bookings are effectively eliminated. Even if multiple users attempt bookings for the same date/time from different locations at the same time.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '9.8/wp_booking_calendar_form_01.gif' ) ); ?>" style="margin-top:3.5em;"/>
			</div>
		</div>
		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_form_dark_02.gif" />
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Introducing the Dark Theme for Booking Form' ) ); ?></h3>
				<?php
				echo '<ul>'
						//. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'In our latest update you have the option to switch to a "Dark" theme for your booking form.' ) . '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'With this new option, you can seamlessly integrate your booking form into your website\'s design. This Theme automatically selects the appropriate calendar and time picker skins, adjusts the colors of your booking form fields, labels, text, and other UI elements.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'If you prefer Dark design, now you can select  it' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'To enable the Dark Theme, head to the Settings General Page, find the "Form Options" section, and select "Dark" from the "Color Theme" dropdown.' ) ). '</li>'
					. '</ul>' ;
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Performance improvements.' ) ); ?></h3>
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'We\'ve supercharged Booking Calendar with significant speed improvements. Compared to previous updates.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'Page loading now happens **34% to 78% faster**. The speed boost varies depending on the number of bookings and number of calendars on your page (multiple calendars in paid versions of Booking Calendar).' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'Server request execution speed has **improved from 63% to 94%** when you load your initial calendar.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'We\'ve streamlined the system to **reduce the number of SQL requests** for your initial page loading by **49%** to a staggering **89%**.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'All these speed enhancements have been tested with 500 active bookings.' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_booking_confirmation_01.gif" />
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Booking Confirmation Section' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'Introduced a new Booking Confirmation section that provides users with a **summary of their booking details**, making it easy for users to confirm their reservations after completing the booking process. This feature allows users to quickly review essential booking information. \'Booking confirmation\' section located on the Booking > Settings General page. Previously, it was located in the \'Form\' section as \'Thank you\' message/page.' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Enhancements in Settings Interface' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Structured General Settings Page:** Redesigned the General Settings page to enhance user experience. The new layout includes a clear navigation column that displays the specific section you click on, making it easier to understand settings, quickly find specific options, and simplify the configuration of the plugin.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**Toggle Boxes:** Replaced checkboxes in the Booking Calendar User Interface with toggle boxes. This change provides a clearer view of enabling specific options and features, particularly for enabling/disabling Rates and Availability in paid versions.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->section_img_url( '9.8/wp_booking_calendar_settings_02.gif' ) ); ?>" style="margin-top:3.5em;"/>
			</div>
		</div>
	</div>
	<?php

	// </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------
	//  = P A I D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Changes in Premium Versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_time_capacity_09.gif" />
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'New Capacity Engine for time-slots' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'With the new capacity engine, you can define booking capacities for your calendar, allowing you to receive a **specific number of bookings per time slots or full dates**. This enhances your control over bookings compared to the previous version, which only supported specific booking limits for full dates. *(Available in Business Large, MultiUser versions).*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'The the capacity feature ensures that the **total number of bookings for specific dates or times does not exceed the specified capacity**. This functionality is versatile and can be applied to various scenarios where you need to manage and limit bookings for specific resources to maintain efficient operations.' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( '**Booking Quantity Control:** Enabled the ability to enable and define a field for \'Booking Quantity Control,\' allowing visitors to define the number of items they can book for specific dates or times within a single reservation. Find this option in  **New  Capacity** section on Booking > Settings General page. *(Business Large, MultiUser)*' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( '**Capacity Shortcode:** Added the Capacity shortcode for showing available (remained) slots per selected dates and times: <code>[capacity_hint]</code>. You can use it in the booking form at the Booking > Settings > Form page. *(Business Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_payments_01.gif" />
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Redesigned Payment Buttons' ) ); ?></h3>
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( '**Redesigned Payment Buttons:** Payment buttons in the new Booking Confirmation window have been redesigned for a more user-friendly experience. *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( '**Payment System Responses:** Now, responses from payment systems after visitors\' payments are recorded in the Note section of the booking, provided "Logging actions for booking" is activated at the Booking > Settings General page in the "Booking Admin Panel" section. Also the booking log now keeps track of booking details, such as cost calculations, actions related to payment request pages via email links, and other important events. *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Additional Notes:** Added the ability to add extended notes about "Total Cost | Discounts applied | Subtotal cost | Deposit Due | Balance Remaining" after creating the booking. Also, added notes about the approval of the booking by the payment system after a response from the Payment gateway. Notes are now added for bookings that were imported from Google Calendar. *(Business Medium/Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_extended_notes.png" />
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Showing Customizable Booking Details in Tooltip' ) ); ?></h3>
				<?php
				echo wp_kses_post( '<ul>'
						. '<li>' . wpbc_replace_to_strong_symbols( '**Booking Details Tooltip:** Added the ability to show booking details in a mouse-over tooltip for specific booked dates or times during a day, significantly improving the speed of this functionality. <br>This can now **show booking details even for fully booked dates**. ' ) . '</li>'
						. '<li>' . wpbc_replace_to_strong_symbols( 'It\'s a helpful feature for businesses with booking workflows that require displaying information about the users who\'ve made the bookings or for similar neighborhood-based use cases.<br>*(Business Medium/Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_display_booking_details_05.gif" />
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_display_unavailable_before_after_02.gif" />
			</div>
			<div class="wpbc_wn_col">

				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Set Unavailable Time Before or After Bookings' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'This feature improves the engine for defining a specific period of unavailability before or after bookings. It now works in all scenarios, even for fully booked dates. When you select multiple dates and specific times for a booking, the system extends the unavailable time interval for those time slots on each day.' ) . '</li>'

					. '</ul>' );
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'It\'s a great feature for preparing your property or service before or after a specific booking, such as allowing time for cleaning.<br>*(Business Medium/Large, MultiUser)*' ) . '</li>'
					. '</ul>' );
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Simple HTML Configuration' ) ); ?></h3>
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'Now, you can organize your booking form fields in rows and columns using the new Simple HTML shortcodes. This makes configuring your booking form even easier.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Here\'s how it works:**' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( 'Example of how to create a single row with 2 columns:' ) ). '</li>'
						. '<li><pre>&lt;r&gt;<br>  &lt;c&gt;...&lt;/c&gt;<br>  &lt;c&gt;...&lt;/c&gt;  <br>&lt;/r&gt;</pre></li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**With this feature, you have the flexibility to design your booking form exactly as you need it.**' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( ' *(Available in all paid versions).*' ) ) . '</li>'
					. '</ul>' ;
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				<img src="<?php echo esc_attr( $obj->asset_path ); ?>9.8/wp_booking_calendar_simple_html_09.gif" />
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Create Rows**: Use <code>&lt;r&gt;...&lt;/r&gt;</code> tags to defines a row in your form.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Add Columns**: Inside each row, you can specify columns using <code>&lt;c&gt;...&lt;/c&gt;</code> tags.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Label Fields**: Use the <code>&lt;l&gt;...&lt;/l&gt;</code> tags.' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Spacer**: <code>&lt;spacer&gt;&lt;/spacer&gt;</code> or <code>&lt;spacer&gt;width:1em;&lt;/spacer&gt;</code>.' ) ). '</li>'
					. '</ul>';
				?>
			</div>
			<div class="wpbc_wn_col">
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Simplified field data tag**: <code>&lt;f&gt;...&lt;/f&gt;</code>. Easily highlight field data by enclosing it within <code>&lt;f&gt;...&lt;/f&gt;</code> tags in the \'Content of booking fields data\' section on the Booking &gt; Settings &gt; Form page. For example: <code>&lt;f&gt;[secondname]&lt;/f</code>. This will highlight the background of the field on the Booking Listing page. *(All Pro Versions)*' ) ). '</li>'
					. '</ul>';
				?>
			</div>
		</div>

		<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<?php
				echo '<ul>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Different Rates for options in select-boxes:** Now, different rates are supported, depending on the selection of options in select-boxes. Example of rate configuration at Booking > Resources > Cost and rates > Rate page: <code>[visitors=1:270 );2:300;3:380;4:450]</code>  (9.8.0.5) *(Business Medium/Large, MultiUser)*' ) ) . '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Date Selection Condition:** Added a condition for defining a specific number of selected dates if started from a specific date. Condition format: <code>\'{select-day condition="date" for="2023-10-01" value="20,25,30-35"}\'</code>.  Example of shortcode: <code>[booking type=3 options=\'{select-day condition="date" for="2023-10-01" value="20,25,30-35"}\']</code> *(Business Medium/Large, MultiUser)*' ) ). '</li>'
						. '<li>' . wp_kses_post( wpbc_replace_to_strong_symbols( '**Cancellation Date Hint Shortcode:** Introduced the <code>[cancel_date_hint]</code> shortcode, which shows the date that is 14 days before the selected check-in date. (9.7.3.16) *(Business Medium/Large, MultiUser)*' ) ). '</li>'
					. '</ul>' ;
				?>
			</div>
		</div>
	</div>
	<?php



	// <editor-fold     defaultstate="collapsed"                        desc="  = M I X E D = "  >
	// -----------------------------------------------------------------------------------------------------------------
	//  = M I X E D =
	// -----------------------------------------------------------------------------------------------------------------
	?>
	<hr class="wpbc_hr_dots"><?php // ---------------------------------------------------------------------- ?>
	<div class="wpbc_wn_container">
		<div class="wpbc_wn_section">
			<h2><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Additional Improvements in Free and Pro versions' ) ); ?></h2>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Toolbar Enhancement** Added a \'Reset\' button at Booking > Add booking page for the toolbar of configuring calendar size.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Date : Time** section on the Booking > Settings General page, making it easier to configure date and time options. Now, the \'Time format\' option is also available in the Booking Calendar Free version.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Daylight Saving Time Fix:** Resolved the \'Daylight Saving Time\' issue that existed on some servers (possibly due to themes or other plugins defining different timezones than those in WordPress via date_default_timezone_set(...) )), ensuring localized dates and times work correctly for all booking dates/times without the need to activate any options in the settings.' ) ); ?></li>

					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved Google Calendar Import:** Improved the actual cron system for importing Google Calendar events, allowing you to set import time intervals starting from 15 minutes. The system now shows the last and next time of importing at the Booking > Settings > Sync > "Import Google Calendar Events" page.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Pseudo Cron** System updated  for google calendar imports.' ) ); ?></li>

					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Scrolling Enhancement:** Enhanced scrolling to specific elements in the booking form, ensuring that the system will not create a new scroll if the previous one was not finished.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Legend Position:** Moved \'Show legend below calendar\' to the \'Calendar\' section on the Booking > Settings General page. Previously, it was located in the \'Form\' section.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Deprecated Options Removal:** Removed deprecated options such as "Use localized time format", \'Time for showing "Thank you" message\',  \'Checking to prevent double booking during submitting booking,\' \'Set capacity based on the number of visitors,\' \'Disable booked time slots in multiple days selection mode\' from Booking > Settings General page  and  option: "for setting maximum  number of visitors per resource" at  the Booking > Resources page in paid versions.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **JS Calendar Scripts:** Updated to version 9.8.0.3.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Caching Improvement:** Introduced new caching for frequently used SQL requests (9.7.3.14).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **\'Reply-To visitor email\' Option:** Added the \'Reply-To visitor email\' option for "Admin emails" at the Booking > Settings > Emails page. By default, this option is disabled to prevent spam detection at some servers in the outbound SMTP relay, which could lead to email rejection (9.7.3.17).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Updated Styles:** Improved the styles of warning messages in the booking form for a better user experience.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Calendar Dimensions:** Increased the width of the calendar from 284px to 341px and the height of calendar cells from 40px to 48px (9.7.3.2). Improved internal logic for calendar months\' size. The width of the calendar is now based on the maximum width, ensuring great responsiveness at any resolution.  No need to use "strong_width" parameter in options of Booking Calendar shortcode. (9.7.3.4)' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Mobile Optimization:** For small mobile devices (width smaller than 400px), the height cell is now 40px by default (9.7.3.2). You can specify the same height for all devices using the \'strong_cell_height\' parameter in the shortcode. For example: [booking type=1 nummonths=2 options=\'{calendar months_num_in_row=2 width=682px strong_cell_height=55px}\'] (9.7.3.3)' ) ); ?></li>
<!-- PRO -->
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Shortcodes Enhancement:** All shortcodes can now use the parameter \'resource_id\' instead of the previously deprecated \'type\' parameter. *(All Pro Versions)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Showing the **full URL .ics feed** at the Booking > Settings > Sync > **"Export - .ics" page** for easier copying of URLs (9.8.0.6). *(All Pro Versions)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; When creating new fast blank bookings, all fields are autofilled with \'---\' instead of \'admin\' values, and the email address is filled with \'blank@wpbookingmanager.com,\' which is skipped during sending emails by Booking Calendar. *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Improved \'Aggregate\' Parameter**: Now, when using \'aggregate,\' if you mark specific dates as unavailable in **aggregation resources on the Booking > Availability page**, the system will automatically make those dates unavailable in the source resource. *(All Pro Versions)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Enhanced Availability Management: If you mark dates as unavailable in the aggregate booking resources on the Booking > Availability page, the system treats these dates as unavailable for all booking resources, including parent and child resources. This ensures consistent availability management. *(All Pro Versions. Capacity in Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; For booking resources with specific capacities, when you use \'aggregate\' for a \'parent booking resource\' with a set capacity, the system adds bookings from \'aggregate booking resources\' to the \'parent resource\' and its child resources. Any \'unavailable dates\' marked on the Booking > Availability page will affect both the parent and its child resources, making those times or dates unavailable for booking. *(Business Large, MultiUser)*' ) ); ?></li>
				<ul>
			</div>
		</div>

		<div class="wpbc_wn_section">
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Under Hood Changes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Removed the JavaScript wpbc_settings variable. Instead of wpbc_settings.set_option( \'pending_days_selectable\', true ) ); use: _wpbc.calendar__set_param_value( resource_id , \'pending_days_selectable\' , true  );  It\'s give ability to define this parameter separately per each calendar in paid versions.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Replaced deprecated functions related to the new "Capacity and Availability" engine (9.7.3.13), and updated the cron system for Google Calendar imports.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Replaced  JavaScript  function showErrorMessage( element , errorMessage , isScrollStop ) to 	wpbc_front_end__show_message__warning( jq_node, message ).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Test Dates Functions:** Added the [wpbc_test_dates_functions] shortcode for testing different dates functions on the server, relative to the possible \'Daylight Saving Time\' issue.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '**CSS Class:** Added a new \'wpbc_calendar\' CSS class to the calendar HTML table, making it easier to manage CSS conflicts with theme styles). You can use CSS in the theme like this: table:not(.wpbc_calendar){...} instead of table{...} (9.7.3.7)' ) ); ?></li>
<!-- PRO -->
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Stripe**: Updated Stripe PHP library from version 9.0.0 to 12.6.0. *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
				    <li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Max. visitors field at the Booking > Resources page is deprecated and removed. For defining capacity, use child booking resources. For defining max visitors selection, use a new custom booking form with a different number of users/visitors selection. *(Business Medium/Large, MultiUser)*' ) ); ?></li>

				</ul>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Support' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; **Support for WordPress 6.4:** Added support for WordPress version 6.4.' ) ); ?></li>
				</ul>
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Translations' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Translation: Improved German (94% completed) by Reinhard Kappen and French (93% completed) by Roby.' ) ); ?></li>
				</ul>
			</div>
			<div class="wpbc_wn_col">
				<h3><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( 'Bug Fixes' ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed an issue with not correctly showing creation and modification booking times on some servers.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Corrected the problem of showing the calendar with an incorrectly defined Start week date at Booking > Availability page.' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the issue of not translating some terms in the plugin (9.7.3.9).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed a color issue of daily cost in calendar date cells for the "Light-01" calendar skin (9.7.3.10).' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed an Uncaught TypeError related to the wpbc-gcal-class.php file (9.7.3.15).' ) ); ?></li>
<!-- PRO -->
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed an issue of redirection to the "Unknown-Stripe-Payment" page after Stripe payment in Booking Calendar MultiUser version, if the option "Receive all payments only to Super Booking Admin account" was activated. (9.7.3.5) *(MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Corrected the issue of not showing conditional time slots, which depend on Seasons.  Uncaught Error: Syntax error, unrecognized expression: # jQuery 10(9.7.3.6) *(Business Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Resolved the problem of removing duplicate days\' selections at the "Specific days" selection option under range days selection mode using 2 mouse clicks. *(Business Small/Medium/Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Fixed the issue of showing available dates in the search form, while such dates were defined as unavailable at Booking > Availability page  (9.7.3.11). *(Business Large, MultiUser)*' ) ); ?></li>
					<li><?php echo wp_kses_post( wpbc_replace_to_strong_symbols( '&bull; Eliminated the issue when used conditional days selection logic, and some weekdays were not defined in seasons. In this case, the system will use the default days selection settings.  *(Business Medium/Large, MultiUser)*' ) ); ?></li>
				<ul>
			</div>
		</div>
	</div><?php
	// </editor-fold>


	$obj->expand_section_end( $section_param_arr );
}
