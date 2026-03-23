<?php

namespace WPaaS\Admin;

use WPaaS\API_Interface;
use \WPaaS\Helpers;
use \WPaaS\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Feedback_Form
{
    use \WPaaS\Helpers;

    const CACHING_INTERVAL = 21600; // 6 hours

    const OPTION_NAME = 'wpaas_nps_form_%s';

    /**
     * Instance of the API.
     *
     * @var API_Interface
     */
    private API_Interface $api;

    private string $npsFormId = 'wptool#nps-system-plugin-dashboard';

    private string $feedbackFormId = 'wptool#feedback';

    /**
     * @var bool
     */
    private bool $has_api_failed = false;

    public function __construct(API_Interface $api)
    {
        $this->api = $api;
        add_action( 'init', [ $this, 'init' ] );
    }

    public function init() {
        // Always enqueue for admins in the dashboard (non-AJAX)
        $should_enqueue = is_admin() &&
            current_user_can( 'administrator' ) &&
            ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX );

        if ( ! $should_enqueue ) {
            return;
        }

        // Always enqueue scripts with default feedback object
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

        // Conditionally localize NPS object
        $should_show_nps = ! Plugin::is_staging_site() &&
            defined( 'GD_RUM_ENABLED' ) &&
            GD_RUM_ENABLED &&
            Plugin::is_gd() &&
            $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( 'nps_90_days', false ) &&
            $this->should_show_nps_form();

        if ( $should_show_nps ) {
            add_action('admin_enqueue_scripts', [$this, 'localize_nps'], 20);
        }
    }

    /**
     * Enqueue feedback widget assets and default localization.
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('wpaas-feedback-style', self::get_cdn_url() . 'standalone/feedback-widget-vanilla.css');
        wp_enqueue_script('wpaas-feedback', self::get_cdn_url() . 'standalone/feedback-widget-vanilla.js');

        // Default feedback object - always available
        wp_localize_script('wpaas-feedback', 'wpaas_feedback_object', $this->get_form_config( $this->feedbackFormId, 'FEEDBACK' ));
    }

    /**
     * Localize NPS-specific configuration.
     */
    public function localize_nps()
    {
        wp_localize_script('wpaas-feedback', 'wpaas_nps_object', $this->get_form_config( $this->npsFormId, 'NPS' ));
    }

    /**
     * Build form configuration array for a given form ID and type.
     *
     * @param string $formId       The form identifier.
     * @param string $feedbackType The type of feedback (e.g., 'NPS', 'FEEDBACK').
     *
     * @return array
     */
    private function get_form_config( string $formId, string $feedbackType ): array
    {

        if ($feedbackType === 'NPS') {
            $submitUrl = add_query_arg( 'nps_form_id', rawurlencode( $formId ), rest_url( 'wpaas/v1/nps-form-submit' ) );
            $dismissUrl = add_query_arg( 'nps_form_id', rawurlencode( $formId ), rest_url( 'wpaas/v1/nps-form-dismiss' ) );
        } else {
            $submitUrl = add_query_arg( 'feedback_form_id', rawurlencode( $formId ), rest_url( 'wpaas/v1/form-submit' ) );
            $dismissUrl = ''; // No dismiss URL for general feedback
        }

        return [
            'getConfigUrl'  => add_query_arg(
                'nps_form_id',
                rawurlencode( $formId ),
                rest_url( 'wpaas/v1/nps-form-configuration' )
            ),
            'dismissUrl'    => $dismissUrl,
            'submitUrl'     => $submitUrl,
            'localeBaseUrl' => self::get_cdn_url() . 'assets/locales',
            'locale'        => get_locale(),
            'nonce'         => wp_create_nonce( 'wp_rest' ),
            'metaData'      => $this->get_metadata(),
            'feedbackType'  => $feedbackType,
        ];
    }

    /**
     * Get common metadata for feedback forms.
     *
     * @return string JSON-encoded metadata.
     */
    private function get_metadata(): string
    {
        $siteCreationDate = null;
        if ( defined( 'GD_SITE_CREATED' ) ) {
            $siteCreationDate = ( new \DateTime() )->setTimestamp( GD_SITE_CREATED );
        }

        return json_encode( [
            'customerId'             => defined( 'GD_CUSTOMER_ID' ) ? GD_CUSTOMER_ID : null,
            'guid'                   => defined( 'GD_ACCOUNT_UID' ) ? GD_ACCOUNT_UID : null,
            'productId'              => defined( 'GD_ACCOUNT_UID' ) ? GD_ACCOUNT_UID : null,
            'product_name'           => 'MWP',
            'mwpSystemPluginVersion' => Plugin::version(),
            'wpUserId'               => get_current_user_id(),
            'wpVersion'              => get_bloginfo( 'version' ),
            'mwpPlanName'            => defined( 'GD_PLAN_NAME' ) ? GD_PLAN_NAME : null,
            'wpLocale'               => get_locale(),
            'woocommerceVersion'     => defined( 'WC_VERSION' ) ? WC_VERSION : null,
            'siteCreatedAt'          => $siteCreationDate ? $siteCreationDate->format( \DateTime::ATOM ) : null,
            'siteAgeDays'            => defined( 'GD_SITE_CREATED' ) ? floor( ( time() - GD_SITE_CREATED ) / 86400 ) : 0,
            'isV2App'                => self::is_wpaas_v2(),
            'isAiroSiteDesigner'     => (bool) get_option( 'wp_site_designer_activated', false ),
        ]);
    }

    /**
     * Check if NPS form should be shown with caching.
     *
     * @return bool
     */
    private function should_show_nps_form(): bool {
        if ( !defined('GD_ACCOUNT_UID') ) {
            return false;
        }

        $option_name = sprintf( self::OPTION_NAME, GD_ACCOUNT_UID );
        $cached_data = get_option( $option_name );

        // No cache exists - fetch from API
        if ( false === $cached_data ) {
            return $this->fetch_and_cache_nps_form_status( $option_name, true );
        }

        // Cache expired - refresh from API
        if ( empty( $cached_data['timestamp'] ) || time() - $cached_data['timestamp'] > self::CACHING_INTERVAL ) {
            $result = $this->fetch_and_cache_nps_form_status( $option_name, false );

            // If API failed, reuse cached value and update timestamp to avoid hammering
            if ( $this->has_api_failed && isset( $cached_data['show_form'] ) ) {
                $cached_data['timestamp'] = time();
                update_option( $option_name, $cached_data );
                return $cached_data['show_form'];
            }

            return $result;
        }

        // Return cached value
        return isset( $cached_data['show_form'] ) ? $cached_data['show_form'] : false;
    }

    /**
     * Fetch NPS form status from API and cache the result.
     *
     * @param string $option_name
     * @param bool   $first_fetch
     *
     * @return bool
     */
    private function fetch_and_cache_nps_form_status( string $option_name, bool $first_fetch ): bool {
        $result = $this->api->show_nps_form( $this->npsFormId );

        if ( null === $result ) {
            $this->has_api_failed = true;

            // On first fetch with API failure, cache false as default
            if ( $first_fetch ) {
                $cached_data = [
                    'show_form' => false,
                    'timestamp' => time(),
                ];
                update_option( $option_name, $cached_data );
            }

            return false;
        }

        $cached_data = [
            'show_form' => (bool) $result,
            'timestamp' => time(),
        ];
        update_option( $option_name, $cached_data );

        return (bool) $result;
    }

    public function get_cdn_url(): string
    {
        $env = Plugin::get_env();

        if ( in_array( $env, [ 'dev', 'test' ], true ) ) {
            return "https://d2mt2uivr8ua2g.cloudfront.net/";
        }

        return 'https://ds9ywulh7jrls.cloudfront.net/';
    }
}
