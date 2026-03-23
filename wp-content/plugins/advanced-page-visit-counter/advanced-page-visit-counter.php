<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://pagevisitcounter.com
 * @since             7.0.0
 * @package           Advanced_Page_Visit_Counter
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Page Visit Counter
 * Plugin URI:        https://pagevisitcounter.com/pricing/?utm_source=Website&utm_medium=Personal&utm_campaign=Get+visit+from+website&utm_term=Get+website+traffic&utm_content=Bio+link
 * Description:       Google Analytics alternative for WordPress by Advanced Page Visit Counter is the must-have Plugin! Now Enhanced eCommerce Tracking is available for WooCommerce
 * Version:           9.1.1
 * Author:            Page Visit Counter
 * Author URI:        https://iamankitpanchal.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-page-visit-counter
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'apvc_fs' ) ) {
    apvc_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'apvc_fs' ) ) {
        // Create a helper function for easy SDK access.
        function apvc_fs()
        {
            global  $apvc_fs ;
            
            if ( !isset( $apvc_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
                $apvc_fs = fs_dynamic_init( array(
                    'id'              => '5937',
                    'slug'            => 'advanced-page-visit-counter',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_6ffe7478cb9ec6a6bfcf3496b571b',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Premium',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                    'days'               => 3,
                    'is_require_payment' => true,
                ),
                    'has_affiliation' => 'all',
                    'menu'            => array(
                    'slug'       => 'advanced-page-visit-counter-dashboard',
                    'first-path' => 'admin.php?page=advanced-page-visit-counter-dashboard',
                    'network'    => true,
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $apvc_fs;
        }
        
        // Init Freemius.
        apvc_fs();
        // Signal that SDK was initiated.
        do_action( 'apvc_fs_loaded' );
    }
    
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    if ( !defined( 'ADVANCED_PAGE_VISIT_COUNTER_VERSION' ) ) {
        define( 'ADVANCED_PAGE_VISIT_COUNTER_VERSION', '9.1.1' );
    }
    // Plugin Folder Path.
    if ( !defined( 'APVC_PLUGIN_DIR' ) ) {
        define( 'APVC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    }
    // Plugin Folder Path.
    
    if ( !defined( 'APVC_UPLOAD_DIR' ) ) {
        $upload_dir = wp_upload_dir();
        define( 'APVC_UPLOAD_DIR', $upload_dir['basedir'] );
    }
    
    
    if ( !defined( 'APVC_EXPORT_DIR' ) ) {
        $upload_dir = wp_upload_dir();
        define( 'APVC_EXPORT_DIR', $upload_dir['basedir'] . '/apvc_export' );
    }
    
    
    if ( !defined( 'APVC_GEO_DB_DIR' ) ) {
        $upload_dir = wp_upload_dir();
        define( 'APVC_GEO_DB_DIR', $upload_dir['basedir'] . '/apvc_geo_db' );
    }
    
    if ( !defined( 'APVC_GEO_DB_FILE' ) ) {
        define( 'APVC_GEO_DB_FILE', APVC_GEO_DB_DIR . '/apvc-location-database.mmdb' );
    }
    // Plugin Folder URL.
    if ( !defined( 'APVC_UPLOAD_DIR_URL' ) ) {
        define( 'APVC_UPLOAD_DIR_URL', plugin_dir_url( __FILE__ ) );
    }
    
    if ( !defined( 'APVC_EXPORT_DIR_URL' ) ) {
        $upload_dir = wp_upload_dir();
        define( 'APVC_EXPORT_DIR_URL', $upload_dir['baseurl'] . '/apvc_export' );
    }
    
    // Date format.
    if ( !defined( 'APVC_DATE_FORMAT' ) ) {
        define( 'APVC_DATE_FORMAT', 'Y-m-d H:i:s' );
    }
    // Page slug.
    if ( !defined( 'APVC_DASHBOARD_PAGE_SLUG' ) ) {
        define( 'APVC_DASHBOARD_PAGE_SLUG', 'advanced-page-visit-counter-dashboard' );
    }
    // Dashboard page link.
    if ( !defined( 'APVC_DASHBOARD_PAGE_LINK' ) ) {
        define( 'APVC_DASHBOARD_PAGE_LINK', get_admin_url() . 'admin.php?page=advanced-page-visit-counter-dashboard' );
    }
    // Dashboard page link.
    if ( !defined( 'APVC_DASHBOARD_SETTINGS_PAGE_LINK' ) ) {
        define( 'APVC_DASHBOARD_SETTINGS_PAGE_LINK', get_admin_url() . 'admin.php?page=advanced-page-visit-counter-dashboard&section=apvc-settings' );
    }
    if ( !defined( 'APVC_ASSETS_PREFIX' ) ) {
        define( 'APVC_ASSETS_PREFIX', 'apvc-' );
    }
    if ( !defined( 'APVC_DATE_FORMAT' ) ) {
        define( 'APVC_DATE_FORMAT', 'Y-m-d H:i:s' );
    }
    if ( !defined( 'APVC_REST_VERSION' ) ) {
        define( 'APVC_REST_VERSION', 'v1' );
    }
    if ( !defined( 'APVC_VIEW_ICON' ) ) {
        define( 'APVC_VIEW_ICON', APVC_UPLOAD_DIR_URL . 'public/images/eye-icon.png' );
    }
    if ( !defined( 'APVC_REALTIME_VIEW_ICON' ) ) {
        define( 'APVC_REALTIME_VIEW_ICON', APVC_UPLOAD_DIR_URL . 'admin/assets/img/realtime-loader.svg' );
    }
    if ( !defined( 'APVC_SITE_URL' ) ) {
        define( 'APVC_SITE_URL', 'https://www.pagevisitcounter.com' );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-advanced-page-visit-counter-activator.php
     */
    function activate_advanced_page_visit_counter()
    {
        
        if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) {
            deactivate_plugins( basename( __FILE__ ) );
            wp_die( '<p>The <strong>Advanced Page Visit Counter</strong> plugin requires PHP version 7.4.0 or greater.</p>', 'Plugin Activation Error', array(
                'response'  => 200,
                'back_link' => TRUE,
            ) );
        }
        
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-page-visit-counter-activator.php';
        Advanced_Page_Visit_Counter_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-advanced-page-visit-counter-deactivator.php
     */
    function deactivate_advanced_page_visit_counter()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-page-visit-counter-deactivator.php';
        Advanced_Page_Visit_Counter_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_advanced_page_visit_counter' );
    register_deactivation_hook( __FILE__, 'deactivate_advanced_page_visit_counter' );
    require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-page-visit-counter.php';
    // Autoload Composer packages.
    require_once APVC_PLUGIN_DIR . 'vendor/autoload.php';
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/action-scheduler/action-scheduler.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-helper-functions.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-query.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-geo-database.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-salt.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-visitor.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-register-view.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-url-support.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-views-cache.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-rest-api.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-shortcodes.php';
    require plugin_dir_path( __FILE__ ) . 'src/class-migration.php';
    /*
     * Admin files
     */
    require plugin_dir_path( __FILE__ ) . 'admin/src/class-admin-dashboard.php';
    require plugin_dir_path( __FILE__ ) . 'admin/src/class-admin-helpers.php';
    require plugin_dir_path( __FILE__ ) . 'admin/src/admin-templates/class-admin-html.php';
    require plugin_dir_path( __FILE__ ) . 'admin/src/admin-templates/class-shortcode-templates.php';
    require plugin_dir_path( __FILE__ ) . 'admin/src/class-timezone-helpers.php';
    /*
     * Public files
     */
    require plugin_dir_path( __FILE__ ) . 'public/class-shortcode-api.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_advanced_page_visit_counter()
    {
        $plugin = new Advanced_Page_Visit_Counter();
        $plugin->run();
        new APVC_Admin_Menu_Dashboard();
        new APVC_Admin_Html();
        new APVC_Admin_Rest_API();
        new APVC_view_cache();
    }
    
    run_advanced_page_visit_counter();
}
