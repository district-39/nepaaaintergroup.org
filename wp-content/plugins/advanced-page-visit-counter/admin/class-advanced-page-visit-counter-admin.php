<?php

use  GeoIp2\Database\Reader ;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://iamankitpanchal.com
 * @since      1.0.0
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/admin
 * @author     Page Visit Counter <developer@pagevisitcounter.com>
 */
class Advanced_Page_Visit_Counter_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Page_Visit_Counter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Page_Visit_Counter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        
        if ( isset( $_GET['page'] ) && $_GET['page'] === APVC_DASHBOARD_PAGE_SLUG ) {
            /* Core CSS */
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'core',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/css/core.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'default',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/css/rtl/theme-default.css',
                array(),
                $this->version,
                'all'
            );
            /* Icons. Uncomment required icon fonts */
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'boxicons',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/fonts/boxicons.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'sweetalert2',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/sweetalert2/sweetalert2.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'spinkit',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/spinkit/spinkit.css',
                array(),
                $this->version,
                'all'
            );
            
            if ( APVC_Admin_Helpers::check_section( 'apvc-settings' ) || APVC_Admin_Helpers::check_section( 'apvc-shortcode-generator' ) || APVC_Admin_Helpers::check_section( 'apvc-export-wizard' ) ) {
                /* Vendors CSS */
                wp_enqueue_style(
                    APVC_ASSETS_PREFIX . 'typeahead',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/typeahead-js/typeahead.css',
                    array(),
                    $this->version,
                    'all'
                );
                wp_enqueue_style(
                    APVC_ASSETS_PREFIX . 'tagify',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/tagify/tagify.css',
                    array(),
                    $this->version,
                    'all'
                );
                wp_enqueue_style(
                    APVC_ASSETS_PREFIX . 'pickr',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/pickr/pickr-themes.css',
                    array(),
                    $this->version,
                    'all'
                );
                wp_enqueue_style(
                    APVC_ASSETS_PREFIX . 'select2',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/select2/select2.css',
                    array(),
                    $this->version,
                    'all'
                );
            }
            
            wp_enqueue_style(
                APVC_ASSETS_PREFIX . 'bootstrap-daterangepicker',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'assets/css/advanced-page-visit-counter-admin.css',
                array(),
                $this->version,
                'all'
            );
        }
    
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Page_Visit_Counter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Page_Visit_Counter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        
        if ( isset( $_GET['page'] ) && $_GET['page'] === APVC_DASHBOARD_PAGE_SLUG ) {
            wp_enqueue_script( 'jquery' );
            /* Helpers */
            //          wp_enqueue_script( APVC_ASSETS_PREFIX . 'helpers', plugin_dir_url( __FILE__ ) . 'assets/vendor/js/helpers.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'config',
                plugin_dir_url( __FILE__ ) . 'assets/js/config.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'popper',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/popper/popper.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'bootstrap',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/js/bootstrap.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'hammer',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/hammer/hammer.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'typeahead',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/typeahead-js/typeahead.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'chartjs',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/chartjs/chartjs.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'sweetalert2',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/sweetalert2/sweetalert2.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            
            if ( APVC_Admin_Helpers::check_section( 'apvc-settings' ) || APVC_Admin_Helpers::check_section( 'apvc-shortcode-generator' ) || APVC_Admin_Helpers::check_section( 'apvc-export-wizard' ) ) {
                wp_enqueue_script(
                    APVC_ASSETS_PREFIX . 'select2',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/select2/select2.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
                wp_enqueue_script(
                    APVC_ASSETS_PREFIX . 'tagify',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/tagify/tagify.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
                wp_enqueue_script(
                    APVC_ASSETS_PREFIX . 'pickr',
                    plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/pickr/pickr.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
                wp_enqueue_script(
                    APVC_ASSETS_PREFIX . 'apvc-pickr',
                    plugin_dir_url( __FILE__ ) . 'assets/js/apvc-color-picker.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
                wp_enqueue_script(
                    APVC_ASSETS_PREFIX . 'apvc-settings',
                    plugin_dir_url( __FILE__ ) . 'assets/js/apvc-settings.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
            }
            
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'moment',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/moment/moment.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'bootstrap-daterangepicker',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            wp_enqueue_script(
                APVC_ASSETS_PREFIX . 'block-ui',
                plugin_dir_url( __FILE__ ) . 'assets/vendor/libs/block-ui/block-ui.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            $saved_option = get_option( 'apvc_advanced_settings', true );
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'assets/js/advanced-page-visit-counter-admin.js',
                array( 'jquery' ),
                $this->version,
                true
            );
            $localised_vars = array(
                'apvcWPnonce'                    => wp_create_nonce( 'apvc_nonce' ),
                'dashboard_rest_url'             => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/dashboard_stats',
                'get_reports_stats_with_range'   => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/get_reports_stats_with_range',
                'reports_page_rest_url'          => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/reports_stats',
                'reports_det_page_rest_url'      => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/reports_det_stats',
                'refer_refresh_url'              => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/refer_refresh_url',
                'generate_shortcode'             => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/generate_shortcode',
                'save_shortcode'                 => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/save_shortcode',
                'delete_shortcode'               => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/delete_shortcode',
                'get_starting_count'             => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/get_starting_count',
                'set_starting_count'             => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/set_starting_count',
                'reset_everything'               => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/reset_everything',
                'start_migration'                => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/start_migration',
                'migrate_data'                   => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/migrate_data',
                'migrate_records'                => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/migrate_records',
                'update_migration'               => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/update_migration',
                'start_fresh'                    => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/start_fresh',
                'clean_old_stats'                => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/clean_old_stats',
                'dash_get_yearly_stats'          => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/dash_get_yearly_stats',
                'dash_get_monthly_stats'         => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/dash_get_monthly_stats',
                'dash_get_weekly_stats'          => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/dash_get_weekly_stats',
                'dash_get_daily_stats'           => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/dash_get_daily_stats',
                'trend_get_top_articles'         => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_articles',
                'trend_get_top_posts'            => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_posts',
                'trend_get_top_countries'        => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_countries',
                'trend_get_top_states'           => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_states',
                'trend_get_top_cities'           => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_cities',
                'trend_get_top_devices'          => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_devices',
                'trend_get_top_devices_os'       => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_devices_os',
                'trend_get_top_browsers'         => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trend_get_top_browsers',
                'trending_get_referrer'          => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trending_get_referrer',
                'trending_refferer_list'         => get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/trending_refferer_list',
                'view_more_data'                 => __( 'View more details', 'apvc' ),
                'dashboard_link'                 => APVC_DASHBOARD_PAGE_LINK,
                'migration_done'                 => APVC_DASHBOARD_PAGE_LINK . '&migration=success',
                'lb_visitors'                    => __( 'Visitors', 'apvc' ),
                'lb_views'                       => __( 'Views', 'apvc' ),
                'lb_sessions'                    => __( 'User Sessions', 'apvc' ),
                'lb_city'                        => __( 'City:', 'apvc' ),
                'lb_state'                       => __( 'State:', 'apvc' ),
                'lb_country'                     => __( 'Country:', 'apvc' ),
                'sh_saved'                       => __( 'Shortcode saved successfully.', 'apvc' ),
                'set_starting_count_str'         => __( 'Set Initial Count Value.', 'apvc' ),
                'reset_delete_warning_title'     => __( 'Delete all data?', 'apvc' ),
                'reset_delete_warning_text'      => __( 'You are about to delete all stats associated with Advanced Page Visit Counter. This includes all views, referrers, users, and user sessions.', 'apvc' ),
                'reset_delete_warning_conf_text' => __( 'Yes, delete all data!', 'apvc' ),
                'reset_delete_success_title'     => __( 'Deleted!', 'apvc' ),
                'reset_delete_success_text'      => __( 'Your all data(This includes all views, referrers, users, and user sessions) has been deleted.', 'apvc' ),
                'mig_process_started'            => __( 'The migration process has begun.', 'apvc' ),
                'mig_process_records'            => __( 'The migration is in progress', 'apvc' ),
                'mig_process_completed'          => __( 'The migration has been successfully completed.', 'apvc' ),
                'mig_process_started_btn_lbl'    => __( 'In Progress', 'apvc' ),
                'mig_are_you_sure'               => __( 'Are you sure about deleting all the old data(old plugin) from the database?', 'apvc' ),
                'mig_cnf_msg_for_del'            => __( "You won't be able to revert this! \n Old statistics will be deleted from the database as your data has been successfully migrated to new analytics.", 'apvc' ),
                'mig_cncl_msg_for_del'           => __( 'Rest assured, your old statistics (from the previous plugin) are securely stored in the database.', 'apvc' ),
                'mig_success_msg'                => __( "Your previous plugin's old statistics data has been cleared and successfully migrated to the newer version of this plugin.", 'apvc' ),
                'mig_success_lbl'                => __( 'Deleted!', 'apvc' ),
                'mig_warning_lbl'                => __( 'Cancelled', 'apvc' ),
                'mig_confirm_lbl'                => __( 'Yes, delete it!', 'apvc' ),
                'no_data_found'                  => __( 'No data found!', 'apvc' ),
                'starting_count_label'           => __( 'Initial Count Value:', 'apvc' ),
                'page_views_label'               => __( 'Page Views:', 'apvc' ),
                'user_sessions_label'            => __( 'User Sessions:', 'apvc' ),
                'device_type_label'              => __( 'Type:', 'apvc' ),
                'device_os_label'                => __( 'OS:', 'apvc' ),
                'device_br_label'                => __( 'Browser:', 'apvc' ),
                'device_br_ver_label'            => __( 'Browser Ver.:', 'apvc' ),
                'apvc_convert_num_to_k'          => ( isset( $saved_option['apvc_show_shorthand_counter'] ) && 'on' === $saved_option['apvc_show_shorthand_counter'] ? 'yes' : 'no' ),
            );
            
            if ( APVC_Helper_Functions::apvc_is_pro() ) {
                $localised_vars['str_thirty_min_chart'] = array(
                    __( 'now', 'apvc' ),
                    __( '-1 min', 'apvc' ),
                    __( '-2 min', 'apvc' ),
                    __( '-3 min', 'apvc' ),
                    __( '-4 min', 'apvc' ),
                    __( '-5 min', 'apvc' ),
                    __( '-6 min', 'apvc' ),
                    __( '-7 min', 'apvc' ),
                    __( '-8 min', 'apvc' ),
                    __( '-9 min', 'apvc' ),
                    __( '-10 min', 'apvc' ),
                    __( '-11 min', 'apvc' ),
                    __( '-12 min', 'apvc' ),
                    __( '-13 min', 'apvc' ),
                    __( '-14 min', 'apvc' ),
                    __( '-15 min', 'apvc' ),
                    __( '-16 min', 'apvc' ),
                    __( '-17 min', 'apvc' ),
                    __( '-18 min', 'apvc' ),
                    __( '-19 min', 'apvc' ),
                    __( '-20 min', 'apvc' ),
                    __( '-21 min', 'apvc' ),
                    __( '-22 min', 'apvc' ),
                    __( '-23 min', 'apvc' ),
                    __( '-24 min', 'apvc' ),
                    __( '-25 min', 'apvc' ),
                    __( '-26 min', 'apvc' ),
                    __( '-27 min', 'apvc' ),
                    __( '-28 min', 'apvc' ),
                    __( '-29 min', 'apvc' )
                );
                $localised_vars['str_no_views_five_mins'] = __( 'No page views in last 5 minutes.', 'apvc' );
                $localised_vars['get_realtime_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/realtime_stats_refresh';
                $localised_vars['get_thirty_minutes_states'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/thirty_minutes_states_refresh';
                $localised_vars['get_realtime_pages_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/realtime_pages_list_stats';
                $localised_vars['get_realtime_referrer_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/realtime_referrer_list_stats';
                $localised_vars['get_realtime_countries_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/realtime_countries_list_stats';
                $localised_vars['get_realtime_campaigns_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/realtime_campaigns_list_stats';
                $localised_vars['create_campaign'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/create_campaign';
                $localised_vars['delete_campaign'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/delete_campaign';
                $localised_vars['refresh_wc_stats'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/refresh_wc_stats';
                $localised_vars['export_create'] = get_rest_url() . 'apvc/' . APVC_REST_VERSION . '/export_create';
            }
            
            wp_localize_script( $this->plugin_name, 'APVC_ADMIN', $localised_vars );
            wp_localize_script( APVC_ASSETS_PREFIX . 'premium', 'APVC_ADMIN_PRE', $localised_vars );
        }
    
    }
    
    public function apvc_download_geo_files()
    {
        $geo_database = new APVC_Geo_Database();
        $geo_database->maybe_dispatch_download_job();
    }
    
    public function apvc_filter_connect_message_on_update(
        $message,
        $user_first_name,
        $product_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        // Add the heading HTML.
        $plugin_name = 'Advanced Page Visit Counter';
        $title = '<h3>' . \sprintf( \esc_html__( 'We hope you love %1$s', 'apvc' ), $plugin_name ) . '</h3>';
        $html = '';
        // Add the introduction HTML.
        $html .= '<p>';
        $html .= \sprintf( \esc_html__( 'Hi, %1$s! This is an invitation to help the %2$s community.', 'apvc' ), $user_first_name, $plugin_name );
        $html .= '<strong>';
        $html .= \sprintf( \esc_html__( 'If you opt-in, some data about your usage of %2$s will be shared with us', 'apvc' ), $user_first_name, $plugin_name );
        $html .= '</strong>';
        $html .= \sprintf( \esc_html__( ' so we can improve %2$s. We will also share some helpful info on using the plugin so you can get the most out of your sites analytics.', 'apvc' ), $user_first_name, $plugin_name );
        $html .= '</p>';
        $html .= '<p>';
        $html .= \sprintf( \esc_html__( 'And if you skip this, that\'s okay! %1$s will still work just fine.', 'apvc' ), $plugin_name );
        $html .= '</p>';
        return $title . $html;
    }
    
    public function plugin_action_links( $plugin_links )
    {
        $settings_link = '<a class="calendar-link" href="' . APVC_DASHBOARD_PAGE_LINK . '">' . \esc_html__( 'Dashboard', 'apvc' ) . '</a>';
        // Add the link to the start of the array
        \array_unshift( $plugin_links, $settings_link );
        return $plugin_links;
    }
    
    public function apvc_schedule_internal_data_refresh()
    {
        $time = '+5 seconds';
        $interval = 3600;
        if ( false === as_has_scheduled_action( 'apvc_refresh_pages_widget' ) ) {
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_pages_widget',
                array(),
                'apvc',
                true
            );
        }
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_posts_widget' ) ) {
            $time = '+10 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_posts_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_countries_widget' ) ) {
            $time = '+15 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_countries_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_states_widget' ) ) {
            $time = '+20 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_states_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_cities_widget' ) ) {
            $time = '+25 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_cities_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_devices_widget' ) ) {
            $time = '+30 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_devices_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_devices_os_widget' ) ) {
            $time = '+35 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_devices_os_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_browsers_widget' ) ) {
            $time = '+40 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_browsers_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_refer_trending_ref_widget' ) ) {
            $time = '+45 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_refer_trending_ref_widget',
                array(),
                'apvc',
                true
            );
        }
        
        
        if ( false === as_has_scheduled_action( 'apvc_refresh_refer_trending_ref_list_widget' ) ) {
            $time = '+55 seconds';
            as_schedule_recurring_action(
                strtotime( $time ),
                $interval,
                'apvc_refresh_refer_trending_ref_list_widget',
                array(),
                'apvc',
                true
            );
        }
        
        // Schedule the cron event if not already scheduled
        if ( !wp_next_scheduled( 'apvc_two_weeks_geo_data_update' ) ) {
            wp_schedule_event( time(), 'every_two_weeks', 'apvc_two_weeks_geo_data_update' );
        }
        //		$cr_version = get_option('apvc_version',true);
        //		if( 7 <= absint( $cr_version ) && !empty( $cr_version ) ){
        //			update_option('apvc_migration_process_bulk_90x','not_started');
        //		}
    }
    
    public function apvc_admin_bar_items( WP_Admin_Bar $wp_admin_bar )
    {
        $menu_id = 'advanced-page-visit-counter-admin-bar';
        $page_id = get_the_ID();
        $today = APVC_Query::get_current_page_today_counts( $page_id );
        $pageTotal = APVC_Query::get_current_page_counts( $page_id );
        $allTime = APVC_Query::get_total_counts();
        $wp_admin_bar->add_menu( array(
            'id'     => $menu_id,
            'parent' => null,
            'href'   => admin_url( 'admin.php?page=advanced-page-visit-counter-dashboard' ),
            'title'  => __( 'APVC: ', 'apvc' ) . $today . __( ' Views', 'apvc' ),
        ) );
        $node = '<span class="ad_left">' . __( 'Page Today: ', 'apvc' ) . '</span><span style="float: right; font-weight: bold;">' . $today . '</span><br />';
        $node .= '<span class="ad_left">' . __( 'Page Total: ', 'apvc' ) . '</span><span style="float: right; font-weight: bold;">' . $pageTotal . '</span><br />';
        $node .= '<span class="ad_left">' . __( 'Total Views ', 'apvc' ) . '</span><span style="float: right; font-weight: bold;">' . $allTime . '</span>';
        $wp_admin_bar->add_menu( array(
            'parent' => $menu_id,
            'title'  => $node,
            'id'     => 'new-order-notification-disable',
        ) );
    }
    
    public function custom_cron_schedule( $schedules )
    {
        $schedules['every_two_weeks'] = array(
            'interval' => 2 * WEEK_IN_SECONDS,
            'display'  => __( 'Every Two Weeks' ),
        );
        return $schedules;
    }
    
    public function apvc_two_weeks_geo_data_update()
    {
        $geo_database = new APVC_Geo_Database();
        $geo_database->maybe_dispatch_download_job();
    }
    
    public function apvc_after_plugin_update_function()
    {
    }

}