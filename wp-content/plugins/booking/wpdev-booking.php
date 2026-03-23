<?php
/*
Plugin Name: Booking Calendar
Plugin URI: https://wpbookingcalendar.com/demo/
Description: Booking Calendar is the original WordPress booking plugin — trusted since 2009. Easily add a calendar to your site, display availability, and accept bookings for appointments, events, time slots, or full-day reservations.
Author: wpdevelop, oplugins
Author URI: https://wpbookingcalendar.com/
Text Domain: booking
Domain Path: /languages/
Version: 10.14.16
License: GPLv2 or later
*/

/*
	Copyright 2009 - 2026  www.wpbookingcalendar.com  (email: info@wpbookingcalendar.com),

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '<h3>Direct access to this file do not allow!</h3>' );                                                         // Exit if accessed directly.
}


if ( ! defined( 'WP_BK_VERSION_NUM' ) ) {
	define( 'WP_BK_VERSION_NUM', '10.14.16' );
}
if ( ! defined( 'WP_BK_MINOR_UPDATE' ) ) {
	define( 'WP_BK_MINOR_UPDATE', true );
}


// ---------------------------------------------------------------------------------------------------------------------
// PRIMARY URL CONSTANTS
// ---------------------------------------------------------------------------------------------------------------------

if ( ! defined( 'WPBC_FILE' ) ) {
	define( 'WPBC_FILE', __FILE__ );                                                                                    // ..\home\siteurl\www\wp-content\plugins\plugin-name\wpdev-booking.php
}

if ( ! defined( 'WPBC_PLUGIN_FILENAME' ) ) {
	define( 'WPBC_PLUGIN_FILENAME', basename( __FILE__ ) );                                                             // wpdev-booking.php .
}

if ( ! defined( 'WPBC_PLUGIN_DIRNAME' ) ) {
	define( 'WPBC_PLUGIN_DIRNAME', plugin_basename( __DIR__ ) );                                                        // plugin-name .
}

if ( ! defined( 'WPBC_PLUGIN_DIR' ) ) {
	define( 'WPBC_PLUGIN_DIR', untrailingslashit( plugin_dir_path( WPBC_FILE ) ) );                                     // ..\home\siteurl\www\wp-content\plugins\plugin-name
}

if ( ! defined( 'WPBC_PLUGIN_URL' ) ) {
	define( 'WPBC_PLUGIN_URL', untrailingslashit( plugins_url( '', WPBC_FILE ) ) );                                     // https: //website.com/wp-content/plugins/plugin-name .
}

if ( ! defined( 'WP_BK_MIN_WP_VERSION' ) ) {
	define( 'WP_BK_MIN_WP_VERSION', '4.0' );                                                                            // Minimum required WP version.
}

if ( ! defined( 'WPBC_JS_IN_FOOTER' ) ) {
	define( 'WPBC_JS_IN_FOOTER', true );                                                                                // Load all  JavaScript files of plugin  at  footer or in header.
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  SYSTEM  CONSTANTS  ==
// ---------------------------------------------------------------------------------------------------------------------
if ( ! defined( 'WP_BK_RESPONSE' ) ) {
	define( 'WP_BK_RESPONSE', false );
}
if ( ! defined( 'WPBC_IS_PLAYGROUND' ) ) {
	define( 'WPBC_IS_PLAYGROUND', ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ( 'PHP.wasm' === $_SERVER['SERVER_SOFTWARE'] ) ) );
}

// Intentionally completely disable showing booking deatils in Timeline view on Front-End side.							// FixIn: 10.14.11.1.
if ( ! defined( 'WPBC_DISABLE_POPOVER_IN_TIMELINE' ) ) {
	define( 'WPBC_DISABLE_POPOVER_IN_TIMELINE', true );
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  DEBUG  CONSTANTS  ==
// ---------------------------------------------------------------------------------------------------------------------
if ( true ) {
	// :: LIVE
	if ( ! defined( 'WP_BK_BETA_DATA_FILL' ) ) {
		define( 'WP_BK_BETA_DATA_FILL', 0 );
	}                                                                                                                   // Set 0 for no filling or 2 for 241 bookings or more for more.
} else {
	// :: DEBUG
	define( 'WP_BK_BETA_DATA_FILL', 1 );                                                                                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'WP_BK_BETA_DATA_FILL_AS', 'BL' );                                                                          // BL - Dates   ,   MU - Times.
}


if ( ! defined( 'WPBC_NEW_FORM_BUILDER' ) ) {
	define( 'WPBC_NEW_FORM_BUILDER', ! true );
}

// ---------------------------------------------------------------------------------------------------------------------
// ==  Go  ==
// ---------------------------------------------------------------------------------------------------------------------
require_once WPBC_PLUGIN_DIR . '/core/wpbc.php';
