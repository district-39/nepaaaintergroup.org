<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Inventory\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;

/**
 * Intercepts the "Manage Stock?" global setting in Settings > Products > Inventory to ensure it's always enabled.
 * This can still be modified on a per-product basis.
 *
 * Will also handle Inventory settings descriptions.
 */
class StockManagementSettingInterceptor extends AbstractInterceptor
{
    protected RegisterFilter $manageStockFilter;

    /**
     * Ensures "Manage stock?" is always enabled globally and cannot be disabled.
     *
     * Also updates "Manage stock?" and "Stock display format" settings descriptions.
     *
     * @return void
     * @throws Exception
     */
    public function addHooks() : void
    {
        // unhook managed stock filter while saving products > inventory settings.
        Register::filter()
            ->setGroup('woocommerce_save_settings_products_inventory')
            ->setHandler([$this, 'unhookManageStockFilter'])
            ->setPriority(PHP_INT_MIN)
            ->setArgumentsCount(1)
            ->execute();

        $this->manageStockFilter = Register::filter()
            ->setGroup('pre_option_woocommerce_manage_stock')
            ->setHandler([$this, 'enableManageStock'])
            ->setPriority(PHP_INT_MAX);

        $this->manageStockFilter->execute();

        Register::filter()
            ->setGroup('woocommerce_inventory_settings')
            ->setHandler([$this, 'handleProductInventorySettings'])
            ->execute();

        Register::filter()
            ->setGroup('pre_update_option_woocommerce_manage_stock')
            ->setHandler([$this, 'enableManageStockOnSave'])
            ->setPriority(PHP_INT_MAX)
            ->setArgumentsCount(2)
            ->execute();
    }

    /**
     * Remove the "enable stock management" hook when saving Woo's inventory settings.
     *
     * Prevents `enableManageStockAgain()` from always returning "yes"
     * via WordPress' `pre_update_option` filter, allowing the correct value to save.
     *
     * @param bool $saving
     *
     * @return bool
     * @throws Exception
     */
    public function unhookManageStockFilter(bool $saving) : bool
    {
        if ($saving) {
            $this->manageStockFilter->deregister();
        }

        return $saving;
    }

    /**
     * Returns `'yes'` to indicate the setting is enabled.
     *
     * @internal
     *
     * @return string
     */
    public function enableManageStock() : string
    {
        return 'yes';
    }

    /**
     * Returns `'yes'` to indicate the setting is enabled when saving the setting.
     *
     * @param mixed $newValue
     * @param mixed $oldValue
     * @return string
     */
    public function enableManageStockOnSave($newValue, $oldValue) : string
    {
        return 'yes';
    }

    /**
     * Adds the `disabled` property to the "Manage stock?" setting so that the checkbox cannot be unchecked.
     *
     * Also updates:
     *  - The description of "Manage stock?" to indicate why it's disabled.
     *  - The description of "Stock display format" to mention that in frontend the reserved stock is not accounted for.
     *
     * @internal
     *
     * @param array<string, mixed>|mixed $settings
     * @return array<string, mixed>|mixed
     */
    public function handleProductInventorySettings($settings)
    {
        if (! ArrayHelper::accessible($settings)) {
            return $settings;
        }

        foreach ($settings as $key => $setting) {
            switch (ArrayHelper::get($setting, 'id')) {
                case 'woocommerce_manage_stock':
                    // Ensure desc key exists and get current value
                    $currentDesc = isset($setting['desc']) && is_string($setting['desc']) ? $setting['desc'] : '';

                    $settings[$key]['disabled'] = true;
                    $settings[$key]['desc'] = $currentDesc.sprintf(
                        /* translators: %1$s opening anchor tag; %2$s closing anchor tag */
                        '<p class="description">'.__('Required for storing Product data in Commerce Home. Stock management can be enabled or disabled per product. Learn more about %1$sproduct inventory settings%2$s.', 'mwc-core').'</p>',
                        '<a href="https://woocommerce.com/document/managing-products/#product-data" target="_blank">',
                        '</a>',
                    );
                    break;
                case 'woocommerce_stock_format':
                    if (! empty($setting['desc'])) {
                        $settings[$key]['desc_tip'] = $setting['desc'];
                    }

                    $settings[$key]['desc'] = '<p class="description">'.__('Inventory amounts displayed on the frontend reflect total available for purchase and exclude reserved stock.', 'mwc-core').'</p>';
                    break;
            }
        }

        return $settings;
    }
}
