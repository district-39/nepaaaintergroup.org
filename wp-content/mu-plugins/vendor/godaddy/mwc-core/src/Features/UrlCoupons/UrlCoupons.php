<?php

namespace GoDaddy\WordPress\MWC\Core\Features\UrlCoupons;

use Exception;
use GoDaddy\WordPress\MWC\Common\Admin\Notices\Notice;
use GoDaddy\WordPress\MWC\Common\Admin\Notices\Notices;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Events\FeatureEnabledEvent;
use GoDaddy\WordPress\MWC\Common\Features\AbstractFeature;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Models\User;
use GoDaddy\WordPress\MWC\Common\Platforms\Exceptions\PlatformRepositoryException;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformRepositoryFactory;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Views\Components\GoDaddyBranding;
use WC_URL_Coupons_Loader;

/**
 * The URL Coupons feature loader.
 */
class UrlCoupons extends AbstractFeature
{
    /** @var string the plugin name */
    protected static $communityPluginName = 'woocommerce-url-coupons/woocommerce-url-coupons.php';

    /** @var string the community plugin slug */
    protected static $communityPluginSlug = 'woocommerce-url-coupons';

    /**
     * Gets the feature name, matching the key used in configuration.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'url_coupons';
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load()
    {
        if (static::isUrlCouponsPluginActive()) {
            // if the community plugin is active, we should NOT attempt to load the bundled plugin
            // doing so will result in fatal errors due to classes/functions being defined twice
            // our only goal at this point is to deactivate the community version
            $this->registerDeactivationHooks();
        } else {
            $this->loadBundledPlugin();
        }
    }

    /**
     * Registers hooks that should execute when the community plugin is already active.
     *
     * @return void
     * @throws Exception
     */
    protected function registerDeactivationHooks() : void
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'maybeDeactivateUrlCouponsPlugins'])
            ->execute();
    }

    /**
     * Loads the bundled version of the plugin.
     * @throws Exception
     */
    protected function loadBundledPlugin() : void
    {
        $rootVendorPath = StringHelper::trailingSlash(Configuration::get('system_plugin.project_root').'/vendor');

        // load plugin class file
        require_once $rootVendorPath.'godaddy/mwc-url-coupons/woocommerce-url-coupons.php';

        // load SV Framework from root vendor folder first
        require_once $rootVendorPath.'skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php';

        WC_URL_Coupons_Loader::instance()->init_plugin();

        $this->registerHooks();
        $this->addAdminNotices();
    }

    /**
     * Registers hooks.
     *
     * @throws Exception
     */
    protected function registerHooks()
    {
        Register::action()
            ->setGroup('wc_url_coupons_options_discount_links')
            ->setHandler([$this, 'addGoDaddyBrandingStyles'])
            ->setCondition([$this, 'shouldAddGoDaddyBranding'])
            ->execute();

        Register::action()
            ->setGroup('wc_url_coupons_options_discount_links')
            ->setHandler([GoDaddyBranding::getInstance(), 'render'])
            ->setCondition([$this, 'shouldAddGoDaddyBranding'])
            ->execute();

        Register::action()
            ->setGroup('load-plugins.php')
            ->setHandler([$this, 'removePluginUpdateNotice'])
            ->setPriority(PHP_INT_MAX)
            ->execute();

        Register::filter()
            ->setGroup('wc_url_coupons_documentation_url')
            ->setHandler([$this, 'modifyDocumentationUrl'])
            ->execute();

        $this->deregisterFeaturesCompatibility();
    }

    /**
     * Bundled MWC plugins should not declare Woo compatibility, as they are not standalone plugins.
     * @link https://godaddy-corp.atlassian.net/browse/MWC-16720
     * @throws Exception
     */
    protected function deregisterFeaturesCompatibility() : void
    {
        Register::action()
            ->setGroup('before_woocommerce_init')
            ->setHandler([wc_url_coupons(), 'handle_features_compatibility'])
            ->deregister();
    }

    /**
     * Removes the WP action that displays the plugin update notice below each plugin on the Plugins page.
     */
    public function removePluginUpdateNotice()
    {
        remove_action('after_plugin_row_'.static::$communityPluginName, 'wp_plugin_update_row');
    }

    /**
     * Modifies the documentation URL to point to GoDaddy instead of WooCommerce.com.
     *
     * @param mixed $docsUrl
     * @return string
     */
    public function modifyDocumentationUrl($docsUrl) : string
    {
        return 'https://godaddy.com/help/-40741';
    }

    /**
     * Checks if should add GoDaddy branding to module settings page.
     *
     * @return bool
     * @throws PlatformRepositoryException
     */
    public function shouldAddGoDaddyBranding() : bool
    {
        return ! PlatformRepositoryFactory::getNewInstance()->getPlatformRepository()->isReseller()
            // only add branding if another feature is not already adding it
            && ! has_action('wc_url_coupons_options_discount_links', [GoDaddyBranding::getInstance(), 'render']);
    }

    /**
     * Adds the style tag used by the GoDaddy branding.
     */
    public function addGoDaddyBrandingStyles()
    {
        ob_start(); ?>
        <style>
            .mwc-gd-branding {
                margin: 9px;
                line-height: 0;
                padding-top: 24px;
                width: 120px;
            }
        </style>
        <?php

        (GoDaddyBranding::getInstance())->addStyle(ob_get_clean());
    }

    /**
     * May deactivate the URL Coupons plugin.
     *
     * @throws Exception
     */
    public function maybeDeactivateUrlCouponsPlugins()
    {
        if (! static::isUrlCouponsPluginActive()) {
            return;
        }

        update_option('mwc_url_coupons_show_notice_plugin_users', 'yes');

        // we want to display the notice again even it was previously dismissed
        if ($user = User::getCurrent()) {
            Notices::restoreNotice($user, $this->getCommunityPluginDeactivatedNoticeId());
        }

        if (function_exists('deactivate_plugins')) {
            deactivate_plugins(static::$communityPluginName);

            // unset GET param so that the "Plugin activated." notice is not displayed
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            Events::broadcast(new FeatureEnabledEvent('url_coupons'));
        }
    }

    /**
     * Gets the ID of the "community plugin deactivated" notice.
     *
     * @return string
     */
    protected function getCommunityPluginDeactivatedNoticeId() : string
    {
        return wc_url_coupons()->get_id_dasherized().'-plugin-users';
    }

    /**
     * Displays admin notices.
     */
    public function addAdminNotices() : void
    {
        $this->maybeAddCommunityPluginDeactivationNotice();
    }

    /**
     * Adds a notice if the community plugin was deactivated.
     * {@see static::maybeDeactivateUrlCouponsPlugins()}.
     */
    protected function maybeAddCommunityPluginDeactivationNotice() : void
    {
        $notice = (new Notice())
            ->setId($this->getCommunityPluginDeactivatedNoticeId())
            ->setType(Notice::TYPE_INFO)
            ->setDismissible(true)
            ->setRenderCondition([$this, 'shouldShowCommunityPluginDeactivationNotice'])
            ->setButtonUrl(esc_url(admin_url('edit.php?post_type=shop_coupon')))
            ->setButtonText(__('View coupons', 'mwc-core'))
            ->setTitle(__('Share discount links', 'mwc-core'))
            ->setContent(
                __('The URL Coupons plugin is now included natively in your hosting plan! The plugin has been deactivated, and your existing settings and coupons have been migrated successfully.', 'mwc-core')
            );

        Notices::enqueueAdminNotice($notice);
    }

    /**
     * Determines whether we should show the notice about the community plugin having been deactivated.
     * We only want to show on certain pages, and if we actually did deactivate the plugin - {@see static::maybeDeactivateUrlCouponsPlugins()}.
     *
     * @return bool
     */
    public function shouldShowCommunityPluginDeactivationNotice() : bool
    {
        $currentScreen = WordPressRepository::getCurrentScreen();
        if (! $currentScreen) {
            return false;
        }

        return in_array($currentScreen->getPageId(), ['plugins', 'coupon_list', 'edit_coupon'], true) &&
            'yes' === get_option('mwc_url_coupons_show_notice_plugin_users');
    }

    /**
     * Checks if URL Coupons plugin is active.
     *
     * @return bool
     */
    public static function isUrlCouponsPluginActive() : bool
    {
        return function_exists('is_plugin_active') && is_plugin_active(static::$communityPluginName);
    }
}
