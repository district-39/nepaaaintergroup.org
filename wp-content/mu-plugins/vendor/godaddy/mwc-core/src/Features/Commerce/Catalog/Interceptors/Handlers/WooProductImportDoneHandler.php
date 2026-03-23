<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchCreateOrUpdateProductsJob;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;

/**
 * Handles the WooCommerce product import done.
 *
 * This handler finds products modified during the import period and schedules sync jobs
 * to sync them to the platform.
 */
class WooProductImportDoneHandler extends AbstractInterceptorHandler
{
    /**
     * {@inheritDoc}
     */
    public function run(...$args)
    {
        if (! $this->shouldHandle()) {
            return;
        }

        $this->scheduleImportedProductsSync();
    }

    /**
     * The import is "done" when we are on the "Import Complete!" page.
     *
     * This method provides more robust checking than just URL parameters by verifying:
     * - User is in admin and has import capability
     * - We're on the correct importer page with 'done' step
     * - The nonce is valid for CSRF protection
     *
     * @return bool
     */
    protected function shouldHandle() : bool
    {
        if (! is_admin() || ! current_user_can('import')) {
            return false;
        }

        return ArrayHelper::get($_GET, 'page', '') === 'product_importer'
            && ArrayHelper::get($_GET, 'step', '') === 'done'
            && wp_verify_nonce(
                TypeHelper::string(ArrayHelper::get($_GET, '_wpnonce', ''), ''),
                'woocommerce-csv-importer'
            );
    }

    /**
     * Schedule sync jobs for products modified during the import period.
     *
     * @return void
     */
    protected function scheduleImportedProductsSync() : void
    {
        $importStartedAt = TypeHelper::string(get_option('mwc_product_import_started_at'), '');
        $importRunning = get_option('mwc_product_import_running');

        // Clean up the options
        delete_option('mwc_product_import_started_at');
        delete_option('mwc_product_import_running');

        if (! $importStartedAt || ! $importRunning) {
            return;
        }

        try {
            $modifiedProductIds = $this->getProductsModifiedSince($importStartedAt);

            if (! empty($modifiedProductIds)) {
                // Dispatch batch job to sync all imported products
                JobQueue::getNewInstance()
                    ->chain([BatchCreateOrUpdateProductsJob::class])
                    ->dispatch($modifiedProductIds);
            }
        } catch (Exception $e) {
            SentryException::getNewInstance('Could not schedule sync jobs for imported products', $e);
        }
    }

    /**
     * Get product IDs that were modified since the given timestamp.
     *
     * @param string $since
     * @return int[]
     */
    protected function getProductsModifiedSince(string $since) : array
    {
        // Subtract 1 second to ensure we don't miss products modified at the exact start time
        $adjustedSince = date('Y-m-d H:i:s', strtotime($since) - 1);

        /** @var int[] $productIds */
        $productIds = CatalogIntegration::withoutReads(function () use ($adjustedSince) {
            return ProductsRepository::query([
                'type'           => ['simple', 'variable', 'variation'],
                'posts_per_page' => -1,
                'date_query'     => [
                    [
                        'column' => 'post_modified_gmt',
                        'after'  => $adjustedSince,
                    ],
                    'inclusive' => true,
                ],
                'return' => 'ids',
            ]);
        });

        // Extract IDs from WC_Product objects
        return array_map('intval', $productIds);
    }
}
