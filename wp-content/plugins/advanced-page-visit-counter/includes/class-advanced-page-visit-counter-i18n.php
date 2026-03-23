<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://iamankitpanchal.com
 * @since      1.0.0
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 * @author     Page Visit Counter <developer@pagevisitcounter.com>
 */
class Advanced_Page_Visit_Counter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'advanced-page-visit-counter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
