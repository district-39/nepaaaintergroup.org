<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://iamankitpanchal.com
 * @since      1.0.0
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/public
 */
use  GeoIp2\Database\Reader ;
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/public
 * @author     Page Visit Counter <developer@pagevisitcounter.com>
 */
class Advanced_Page_Visit_Counter_Public
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
    private  $migration ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        new Shortcode_API();
        $this->migration = new APVC_Migration();
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/advanced-page-visit-counter-public.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/advanced-page-visit-counter-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    public function filter_starting_count_for_the_post( $count, $post_id )
    {
        return absint( $count ) + absint( ( get_post_meta( $post_id, 'apvc_starting_count', true ) ? get_post_meta( $post_id, 'apvc_starting_count', true ) : 0 ) );
    }
    
    public function register_rest_api_for_tracking()
    {
        register_rest_route( 'apvc', '/track_view', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'track_view' ),
            'permission_callback' => function () {
            return true;
        },
        ) );
    }
    
    public function track_view( $request )
    {
        $helpers = new APVC_Helper_Functions();
        /* check migration process is running or not		*/
        if ( $this->migration->is_migrating() ) {
            return;
        }
        if ( !file_exists( APVC_GEO_DB_FILE ) ) {
            return;
        }
        $ip_address = APVC_Helper_Functions::get_ip_address();
        $user_agent = $helpers::get_user_agent();
        $user_device = $helpers::get_user_device();
        $singular_id = ( isset( $request['rawData']['singular_id'] ) ? absint( $request['rawData']['singular_id'] ) : 0 );
        $user_id = ( isset( $request['rawData']['user_id'] ) ? absint( $request['rawData']['user_id'] ) : 0 );
        if ( 0 === $singular_id ) {
            return;
        }
        if ( $helpers::blocked_post_type( $singular_id ) ) {
            return;
        }
        if ( $helpers::blocked_users( $user_id ) ) {
            return;
        }
        if ( $helpers::blocked_posts( $singular_id ) ) {
            return;
        }
        if ( $helpers::blocked_ip( $ip_address ) ) {
            return;
        }
        if ( $helpers::track_loggedin_users_only( absint( $user_id ) ) ) {
            return;
        }
        $visitor = new APVC_Visitor( $ip_address, $user_agent );
        $signature = md5( APVC_Salt::request_payload_salt() . json_encode( $request['rawData'] ) );
        $campaign = array();
        $referrer_url = wp_get_referer() ?? '';
        
        if ( $signature === $request['signature'] ) {
            new APVC_Register_View(
                $request['rawData'],
                $referrer_url,
                $visitor,
                $campaign,
                null,
                $user_device
            );
            return new \WP_REST_Response( array(
                'success' => true,
            ), 200, array(
                'X-APVC' => 'APVC',
            ) );
        } else {
            return new \WP_REST_Response( array(
                'success' => false,
            ), 200, array(
                'X-APVC' => 'APVC',
            ) );
        }
    
    }
    
    public function add_tracking_scripts_to_footer()
    {
        
        if ( isset( $_GET['a_camp'] ) && !empty($_GET['a_camp']) ) {
            $full_url = APVC_Query::get_campaign_full_url( sanitize_text_field( $_GET['a_camp'] ) );
            echo  '<script>window.location.replace("' . $full_url . '")</script>' ;
        }
        
        $saved_option = get_option( 'apvc_basic_settings', true );
        if ( empty($saved_option['apvc_enable_counter']) ) {
            return;
        }
        $rawData = array();
        $current_page = APVC_Helper_Functions::get_page_type();
        if ( is_null( $current_page ) && !is_array( $current_page ) ) {
            return;
        }
        $rawData['type'] = ( !empty($current_page['page_type']) ? $current_page['page_type'] : null );
        if ( !is_null( $current_page['page_meta_key'] ) && !is_null( $current_page['page_meta_value'] ) ) {
            $rawData[$current_page['page_meta_key']] = $current_page['page_meta_value'];
        }
        
        if ( is_user_logged_in() ) {
            $rawData['author_id'] = get_current_user_id();
        } else {
            $rawData['author_id'] = 0;
        }
        
        $rawData['page'] = max( 1, get_query_var( 'paged' ) );
        $validation_of_data = !empty($rawData);
        
        if ( $validation_of_data ) {
            $data = array(
                'rawData' => $rawData,
            );
            $data['signature'] = md5( APVC_Salt::request_payload_salt() . json_encode( $data['rawData'] ) );
            $url = get_rest_url() . 'apvc/track_view';
            ?>

            <script>
                (function () {
                    document.addEventListener("DOMContentLoaded", function (e) {
                        if (document.hasOwnProperty("visibilityState") && document.visibilityState === "prerender") {
                            return;
                        }

                        if (navigator.webdriver || /bot|crawler|spider|crawling/i.test(navigator.userAgent)) {
                            return;
                        }

                        const params = location.search.slice(1).split('&').reduce((acc, s) => {
                            const [k, v] = s.split('=')
                            return Object.assign(acc, {[k]: v})
                        }, {})

                        const url = "<?php 
            echo  $url ;
            ?>"
                        const body = {
                            utm_source: params.utm_source,
                            utm_medium: params.utm_medium,
                            utm_campaign: params.utm_campaign,
                            utm_term: params.utm_term,
                            utm_content: params.utm_content,
                            ...<?php 
            echo  json_encode( $data ) ;
            ?>
                        }
                        const xhr = new XMLHttpRequest()
                        xhr.open("POST", url, true)
                        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8")
                        xhr.send(JSON.stringify(body))
                    })
                })();
            </script>
			<?php 
        }
    
    }

}