<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;

/**
 * An interceptor that forces GoDaddy branding to display on the WooCommerce admin pages.
 */
class GoDaddyBrandingInterceptor extends AbstractInterceptor
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function addHooks() : void
    {
        Register::action()
            ->setGroup('admin_footer')
            ->setHandler([$this, 'addGoDaddyBrandingStyles'])
            ->setPriority(PHP_INT_MAX)
            ->execute();
    }

    /**
     * Only force branding display on the WooCommerce settings 'checkout' settings page when viewing
     * specific sections.
     *
     * @return bool
     */
    public static function shouldLoad() : bool
    {
        // List of sections where we should force the GoDaddy branding
        $sections = [
            'poynt',
            'godaddy-payments-apple-pay',
            'godaddy-payments-google-pay',
            'godaddy-payments-payinperson',
            'stripe',
        ];

        return 'wc-settings' === ArrayHelper::get($_GET, 'page')
            && 'checkout' === ArrayHelper::get($_GET, 'tab')
            && in_array(ArrayHelper::get($_GET, 'section'), $sections, true);
    }

    /**
     * Overwrites hiding the wpfooter which is present in the base wooCommerce styles.
     *
     * @return void
     */
    public function addGoDaddyBrandingStyles() : void
    {
        echo '<style>#wpfooter { display: block ! important; }</style>';
    }
}
