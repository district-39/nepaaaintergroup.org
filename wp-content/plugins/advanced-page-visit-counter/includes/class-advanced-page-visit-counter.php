<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://iamankitpanchal.com
 * @since      1.0.0
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 * @author     Page Visit Counter <developer@pagevisitcounter.com>
 */
class Advanced_Page_Visit_Counter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Advanced_Page_Visit_Counter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ADVANCED_PAGE_VISIT_COUNTER_VERSION' ) ) {
			$this->version = ADVANCED_PAGE_VISIT_COUNTER_VERSION;
		} else {
			$this->version = '8.0.4';
		}
		$this->plugin_name = 'advanced-page-visit-counter';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Advanced_Page_Visit_Counter_Loader. Orchestrates the hooks of the plugin.
	 * - Advanced_Page_Visit_Counter_i18n. Defines internationalization functionality.
	 * - Advanced_Page_Visit_Counter_Admin. Defines all hooks for the admin area.
	 * - Advanced_Page_Visit_Counter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-page-visit-counter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-page-visit-counter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-advanced-page-visit-counter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-advanced-page-visit-counter-public.php';

		$this->loader = new Advanced_Page_Visit_Counter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Page_Visit_Counter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Advanced_Page_Visit_Counter_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Advanced_Page_Visit_Counter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'apvc_download_geo_files', $plugin_admin, 'apvc_download_geo_files' );
		$this->loader->add_action( 'cron_schedules', $plugin_admin, 'custom_cron_schedule' );
		$this->loader->add_action( 'init', $plugin_admin, 'apvc_schedule_internal_data_refresh' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'apvc_admin_bar_items', 100 );
		$this->loader->add_action( 'apvc_two_weeks_geo_data_update', $plugin_admin, 'apvc_two_weeks_geo_data_update' );
		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'apvc_after_plugin_update_function', 10, 2 );

		$saved_option = get_option( 'apvc_basic_settings', true );
		if( !empty($saved_option) && isset( $saved_option['apvc_post_types'] ) ){
			$post_types = $saved_option['apvc_post_types'];

			foreach( $post_types as $post_type ) {
				add_filter( 'manage_'.$post_type.'_posts_columns', function ( $columns ) {
					return array_merge( $columns, [ 'views' => __( 'Views', 'apvc' ) ] );
				} );

				add_action( 'manage_'.$post_type.'_posts_custom_column', function ( $column_key, $post_id ) {
					if ( $column_key == 'views' ) {
						echo '<span style="color:green;">';
						echo APVC_Query::get_view_count( $post_id );
						echo '</span>';
					}
				}, 10, 2 );
			}

		}

		$this->loader->add_filter('connect_message_on_update', $plugin_admin, 'apvc_filter_connect_message_on_update', 10, 6 );
		$this->loader->add_filter('connect_message', $plugin_admin, 'apvc_filter_connect_message_on_update', 10, 6 );
		$this->loader->add_filter('plugin_action_links_advanced-page-visit-counter/advanced-page-visit-counter.php', $plugin_admin, 'plugin_action_links' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Advanced_Page_Visit_Counter_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'add_tracking_scripts_to_footer' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'register_rest_api_for_tracking' );
		$this->loader->add_filter( 'post_starting_count', $plugin_public, 'filter_starting_count_for_the_post', 1, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Advanced_Page_Visit_Counter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
