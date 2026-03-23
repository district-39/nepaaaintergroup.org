<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\WebVitals\Interceptors\Handlers;

use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Common\Platforms\Contracts\PlatformRepositoryContract;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformEnvironment;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\ThemeRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use WP_Post;

class RenderWebVitalsInlineScriptHandler extends AbstractInterceptorHandler
{
    protected PlatformRepositoryContract $platformRepository;

    public function __construct(PlatformRepositoryContract $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * Renders the inline script to capture web vitals and additional request information.
     */
    public function run(...$args) : void
    {
        $this->render();
    }

    /**
     * The RUM class in the MWP system plugin also renders a Full Story inline script:
     *
     * https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/class-rum.php#L120-L151
     *
     * We are not rendering that script here because it is already being rendered in {@see Client::maybeEnqueueFullStoryAssets}.
     */
    protected function render() : void
    {
        $environment = ManagedWooCommerceRepository::getEnvironment();

        ?><script>
        'undefined'=== typeof _trfq || (window._trfq = []);
        'undefined'=== typeof _trfd && (window._trfd=[]), _trfd.push.apply(_trfd, <?php echo json_encode($this->getTrafficVariables($environment)); ?>);
        var trafficScript = document.createElement('script');
        trafficScript.src = '<?php echo esc_js($this->getSignalsCaptureClientUrl($environment)); ?>'; window.document.head.appendChild(trafficScript);
    </script>
        <script>window.addEventListener('click', function (elem) { var _elem$target, _elem$target$dataset, _window, _window$_trfq; return (elem === null || elem === void 0 ? void 0 : (_elem$target = elem.target) === null || _elem$target === void 0 ? void 0 : (_elem$target$dataset = _elem$target.dataset) === null || _elem$target$dataset === void 0 ? void 0 : _elem$target$dataset.eid) && ((_window = window) === null || _window === void 0 ? void 0 : (_window$_trfq = _window._trfq) === null || _window$_trfq === void 0 ? void 0 : _window$_trfq.push(["cmdLogEvent", "click", elem.target.dataset.eid]));});</script>
        <script src='https://img1.wsimg.com/traffic-assets/js/tccl-tti.min.js' onload="window.tti.calculateTTI()"></script>
        <?php
    }

    /**
     * Gets a list of variables to send with the web vitals in the format expected by the web vitals script.
     *
     * The script expects each variable as an object with the variable name as the only
     * property and the variable value as the property value.
     *
     * @return list<array<string, string>>
     */
    protected function getTrafficVariables(string $environment) : array
    {
        $variables = [];

        foreach ($this->getTrafficData($environment) as $key => $value) {
            $variables[] = [$key => $value];
        }

        return $variables;
    }

    /**
     * Gets an associative array of variables to send with the web vitals.
     *
     * @return array{
     *     "tccl.baseHost": string,
     *     ap: 'mwcs',
     *     server: string,
     *     pod: non-empty-string | 'null',
     *     wp: string,
     *     php: string,
     *     loggedin: '1' | '0',
     *     cdn: '1' | '0',
     *     builder: string,
     *     theme: string,
     *     wds: '0',
     *     wp_alloptions_count: numeric-string,
     *     wp_alloptions_bytes: numeric-string,
     *     gdl_coming_soon_page: '1' | '0',
     *     appid: string
     * }
     */
    protected function getTrafficData(string $environment) : array
    {
        $options = wp_load_alloptions();

        return [
            'tccl.baseHost'        => $this->getTrafficCaptureClientLiteHost($environment),
            'ap'                   => 'mwcs',
            'server'               => (string) gethostname(),
            'pod'                  => $this->getPodName(),
            'wp'                   => (string) WordPressRepository::getVersion(),
            'php'                  => PHP_VERSION,
            'loggedin'             => (function_exists('is_user_logged_in') && is_user_logged_in()) ? '1' : '0',
            'cdn'                  => $this->isCdnEnabled() ? '1' : '0',
            'builder'              => $this->getPageBuilderName($this->getPost()) ?: '',
            'theme'                => $this->getActiveThemeName(),
            'wds'                  => '0',
            'wp_alloptions_count'  => (string) count($options),
            'wp_alloptions_bytes'  => (string) strlen(serialize($options)),
            'gdl_coming_soon_page' => $this->isComingSoonPage() ? '1' : '0',
            'appid'                => $this->platformRepository->getPlatformSiteId(),
        ];
    }

    /**
     * TCCL stands for Traffic Capture Client Lite.
     *
     * Host values extracted from https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/class-rum.php#L86-L87
     *
     * @link https://godaddy-corp.atlassian.net/wiki/spaces/WEBANLYTCS/pages/487656958/Traffic+Lite+TCCL
     * @link https://godaddy-corp.atlassian.net/wiki/spaces/CKPT/pages/3176671567/Traffic+Migration+Roadmap
     *
     * @return string
     */
    protected function getTrafficCaptureClientLiteHost(string $environment) : string
    {
        switch ($environment) {
            case PlatformEnvironment::PRODUCTION:
                return 'secureserver.net';
            case PlatformEnvironment::TEST:
                return 'test-secureserver.net';
            default:
                return 'dev-secureserver.net';
        }
    }

    /**
     * @return non-empty-string | 'null'
     */
    protected function getPodName() : string
    {
        return TypeHelper::string(getenv('WPAAS_POD'), '') ?: 'null';
    }

    protected function isCdnEnabled() : bool
    {
        return method_exists($this->platformRepository, 'isCdnEnabled') && $this->platformRepository->isCdnEnabled();
    }

    protected function getPost() : ?WP_Post
    {
        return $GLOBALS['post'] ?? null;
    }

    /**
     * Determine which builder platform was used to create the current page.
     *
     * Copied and adapted from {@link https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/trait-helpers.php#L1267-L1359}
     */
    protected function getPageBuilderName(?WP_Post $post = null) : ?string
    {
        if (is_null($post)) {
            return null;
        }

        if (class_exists('FLBuilderLoader') && 1 === TypeHelper::int(get_post_meta($post->ID, '_fl_builder_enabled', true), 0)) {
            return 'beaver-builder';
        }

        if (defined('BRIZY_VERSION') && get_post_meta($post->ID, 'brizy_post_uid', true)) {
            return 'brizy';
        }

        if (defined('ET_BUILDER_VERSION') && 'on' === get_post_meta($post->ID, '_et_pb_use_builder', true)) {
            return 'divi';
        }

        if (defined('ELEMENTOR_VERSION') && 'builder' === get_post_meta($post->ID, '_elementor_edit_mode', true)) {
            return 'elementor';
        }

        if (defined('CT_VERSION') && get_post_meta($post->ID, 'ct_builder_shortcodes', true)) {
            return 'oxygen';
        }
        if (defined('THEMIFY_VERSION') && get_post_meta($post->ID, '_themify_builder_settings_json', true)) {
            return 'themify-builder';
        }
        if (defined('VCV_VERSION') && 'vc' === get_post_meta($post->ID, '_vcv-page-template-type', true)) {
            return 'visual-composer';
        }

        if (class_exists('Classic_Editor')) {
            // Normalize old options: https://plugins.trac.wordpress.org/browser/classic-editor/trunk/classic-editor.php?rev=2084072#L254
            $default = in_array(get_option('classic-editor-replace'), ['block', 'no-replace'], true) ? 'block-editor' : 'classic-editor';
            $builder = ('allow' === get_option('classic-editor-allow-users')) ? get_post_meta($post->ID, 'classic-editor-remember', true) : $default;
            $builder = in_array($builder, ['block-editor', 'classic-editor'], true) ? $builder : $default;

            return 'wp-'.$builder;
        }

        return (false !== strpos($post->post_content, '<!-- wp:')) ? 'wp-block-editor' : null;
    }

    protected function getActiveThemeName() : string
    {
        return sanitize_title(ThemeRepository::getActiveThemeName());
    }

    protected function isComingSoonPage() : bool
    {
        return (bool) apply_filters('gdl_coming_soon_page', false);
    }

    /**
     * URLs extracted from https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/class-rum.php#L90-L92.
     */
    protected function getSignalsCaptureClientUrl(string $environment) : string
    {
        switch ($environment) {
            case PlatformEnvironment::PRODUCTION:
                $host = 'img1.wsimg.com';
                $asset = 'scc-c2.min.js';
                break;
            case PlatformEnvironment::TEST:
                $host = 'img1.test-wsimg.com';
                $asset = 'scc-c2.min.js';
                break;
            default:
                $host = 'img1.dev-wsimg.com';
                $asset = 'scc-c2.js';
                break;
        }

        return "https://{$host}/signals/js/clients/scc-c2/{$asset}";
    }
}
