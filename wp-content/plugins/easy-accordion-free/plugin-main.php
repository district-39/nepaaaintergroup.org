<?php
/**
 * Plugin name: Easy Accordion
 * Plugin URI:  https://easyaccordion.io/?ref=1
 * Description: Easy Accordion is a responsive Accordion and FAQ builder plugin for WordPress. Create unlimited accordions, FAQ sections, and WooCommerce Product FAQs with a simple drag-and-drop interface—no coding required.
 * Author:      ShapedPlugin LLC
 * Author URI:  https://shapedplugin.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:     3.0.7
 * Text Domain: easy-accordion-free
 * Domain Path: /languages/
 *
 * @package easy-accordion-free
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Pro version check.
 *
 * @return boolean
 */
function is_easy_accordion_pro_active() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	if (
		is_plugin_active( 'easy-accordion-pro/easy-accordion-pro.php' ) ||
		is_plugin_active_for_network( 'easy-accordion-pro/easy-accordion-pro.php' )
	) {
		return true;
	}

	return false;
}

/**
 * The main class.
 */
class SP_EASY_ACCORDION_FREE {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Easy_Accordion_Free_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $loader;

	/**
	 * Currently plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $version = '3.0.7';

	/**
	 * The name of the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'easy-accordion-free';

	/**
	 * Plugin textdomain.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $domain = 'easy-accordion-free';

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file = __FILE__;

	/**
	 * Holds class object
	 *
	 * @var   object
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Initialize the SP_EASY_ACCORDION_FREE() class
	 *
	 * @since  2.0.0
	 * @return object
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SP_EASY_ACCORDION_FREE ) ) {
			self::$instance = new SP_EASY_ACCORDION_FREE();
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 2.0.0
	 */
	public function setup() {
		$this->define_constants();
		$this->includes();
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_common_hooks();
		$this->eap_wc_tab();
	}

	/**
	 * Define constants
	 *
	 * @since 2.0.0
	 */
	public function define_constants() {
		define( 'SP_EA_VERSION', $this->version );
		define( 'SP_PLUGIN_NAME', $this->plugin_name );
		define( 'SP_EA_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SP_EA_URL', plugin_dir_url( __FILE__ ) );
		define( 'SP_EA_BASENAME', plugin_basename( __FILE__ ) );
		define( 'SP_EA_INCLUDES', SP_EA_PATH . '/includes' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Easy_Accordion_Free_Admin( SP_PLUGIN_NAME, SP_EA_VERSION );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_styles' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'eap_updated_messages', 10, 2 );
		$this->loader->add_filter( 'manage_sp_easy_accordion_posts_columns', $plugin_admin, 'filter_accordion_admin_column' );

		$this->loader->add_action( 'manage_sp_easy_accordion_posts_custom_column', $plugin_admin, 'display_accordion_admin_fields', 10, 2 );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'sp_eap_review_text', 10, 2 );
		$this->loader->add_filter( 'update_footer', $plugin_admin, 'sp_eap_version_text', 11 );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'after_easy_accodion_row_meta', 10, 4 );
		$this->loader->add_action( 'activated_plugin', $plugin_admin, 'sp_ea_redirect_after_activation', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_action_links', 10, 2 );
		// import export tools.
		$import_export = new Easy_Accordion_Import_Export( SP_PLUGIN_NAME, SP_EA_VERSION );
		$this->loader->add_action( 'wp_ajax_eap_export_accordions', $import_export, 'export_accordions' );
		$this->loader->add_action( 'wp_ajax_eap_import_accordions', $import_export, 'import_accordions' );
		if ( version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ) {
			// Gutenberg block.
			new Easy_Accordion_Free_Gutenberg_Block();
		}

		// Elementor shortcode addons.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ( is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active_for_network( 'elementor/elementor.php' ) ) ) {
			require_once SP_EA_PATH . 'admin/class-easy-accordion-free-element-shortcode-addons.php';
		}
		add_filter( 'body_class', array( $this, 'sp_easy_accordion_body_class' ) );
		register_activation_hook( 'easy-accordion-free/easy-accordion-free.php', array( $this, 'eap_post_type_reset_flush_flag' ) );
	}

	/**
	 * Reset the flag if needed (e.g., during theme or plugin updates).
	 *
	 * @return void
	 */
	public function eap_post_type_reset_flush_flag() {
		delete_option( 'sp_eap_flush_rewrite_rules' );
	}

	/**
	 * Adds a custom body class for SP Easy Accordion to the body tag.
	 *
	 * @param array $classes An array of body classes.
	 * @return array An updated array of body classes.
	 */
	public function sp_easy_accordion_body_class( $classes ) {
		// Add the custom body class for SP Easy Accordion.
		$classes[] = 'sp-easy-accordion-enabled';

		return $classes;
	}

	/**
	 * Register common hooks.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function define_common_hooks() {
		$plugin_cpt           = new Easy_Accordion_Free_Post_Type( $this->plugin_name, $this->version );
		$plugin_review_notice = new Easy_Accordion_Free_Review( SP_PLUGIN_NAME, SP_EA_VERSION );
		$this->loader->add_action( 'init', $plugin_cpt, 'easy_accordion_post_type', 10 );
		$this->loader->add_action( 'init', $plugin_cpt, 'register_faq_post_type' );
		$this->loader->add_action( 'admin_menu', $plugin_cpt, 'add_faq_submenu' );
		$this->loader->add_action( 'admin_notices', $plugin_review_notice, 'display_admin_notice' );
		$this->loader->add_action( 'wp_ajax_sp-eafree-never-show-review-notice', $plugin_review_notice, 'dismiss_review_notice' );
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @since 2.0.2
	 * @access private
	 */
	private function eap_wc_tab() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$settings    = get_option( 'sp_eap_settings' );
		$eap_woo_faq = isset( $settings['eap_woo_faq'] ) ? $settings['eap_woo_faq'] : '';
		if ( ( $eap_woo_faq ) && ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) ) {
			// Product tab.
			$product_tab = new Easy_Accordion_Free_Product_Tab( SP_PLUGIN_NAME, SP_EA_VERSION );
			$this->loader->add_filter( 'woocommerce_product_tabs', $product_tab, 'eap_woo_faq_tab', 10, 2 );
		}
	}

	/**
	 * Included required files.
	 *
	 * @return void
	 */
	public function includes() {
		require_once SP_EA_INCLUDES . '/class-easy-accordion-free-updates.php';
		require_once SP_EA_INCLUDES . '/class-easy-accordion-free-loader.php';
		require_once SP_EA_INCLUDES . '/class-easy-accordion-free-post-types.php';
		require_once SP_EA_INCLUDES . '/class-easy-accordion-free-product-tab.php';
		require_once SP_EA_PATH . '/admin/class-easy-accordion-free-admin.php';
		require_once SP_EA_PATH . '/admin/views/models/classes/setup.class.php';
		require_once SP_EA_PATH . '/admin/help-page/help-page.php';
		require_once SP_EA_PATH . '/admin/views/notices/review.php';
		require_once SP_EA_PATH . '/public/scripts.php';
		require_once SP_EA_PATH . '/public/eap-frontend.php';
		require_once SP_EA_PATH . '/includes/class-easy-accordion-import-export.php';
		require_once SP_EA_PATH . '/admin/preview/class-easy-accordion-free-preview.php';
		require_once SP_EA_PATH . '/admin/class-easy-accordion-free-gutenberg-block.php';
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Easy_Accordion_Free_Loader. Orchestrates the hooks of the plugin.
	 * - Easy_Accordion_Free_Admin. Defines all hooks for the admin area.
	 * - Easy_Accordion_Free_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new Easy_Accordion_Free_Loader();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}
}

/**
 * Main instance of Easy Accordion
 *
 * Returns the main instance of the Easy Accordion.
 *
 * @since 2.0.0
 */
function sp_easy_accordion() {
	$plugin = SP_EASY_ACCORDION_FREE::init();
	$plugin->loader->run();

	if ( ! defined( 'SHAPEDPLIUGIN_OFFER_BANNER_LOADED' ) ) {
		define( 'SHAPEDPLIUGIN_OFFER_BANNER_LOADED', true );

		/**
		 * The file is responsible for generating admin offer banner.
		 */
		include_once plugin_dir_path( __FILE__ ) . 'admin/views/notices/offer-banner.php';
	}
}

if ( ! is_easy_accordion_pro_active() ) {
	sp_easy_accordion();
}
