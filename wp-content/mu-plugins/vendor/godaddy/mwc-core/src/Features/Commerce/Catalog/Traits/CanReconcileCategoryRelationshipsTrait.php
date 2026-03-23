<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchAssociateProductCategoryRelationshipsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchRemoveProductCategoryRelationshipsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;

/**
 * Trait for reconciling the relationships between products and a local category.
 */
trait CanReconcileCategoryRelationshipsTrait
{
    protected ProductMapRepository $productMapRepository;

    /**
     * Gets the local mapping for products within the given category and reconciles the relationships locally if they exist.
     *
     * @param string[] $remoteProductIds
     * @param int $localCategoryId
     */
    protected function maybeReconcileRelationships(?array $remoteProductIds, int $localCategoryId) : void
    {
        // a category with no product IDs is a valid state, and can indicate that local products need to be removed from it
        $localProductIds = [];

        // if there are product IDs to process, try to get their local IDs
        if (! empty($remoteProductIds)) {
            $localProductMappings = $this->productMapRepository->getMappingsByRemoteIds($remoteProductIds);

            // we don't need a job if none of the category products have local mappings
            if (! $localProductIds = $localProductMappings->getLocalIds()) {
                return;
            }
        }

        $this->reconcileCategoryRelationships($localProductIds, $localCategoryId);
    }

    /**
     * Dispatches jobs to reconcile the relationships between local products and a local category.
     *
     * @param int[] $localIds local product IDs
     * @param int $categoryId local category ID
     * @return void
     */
    protected function reconcileCategoryRelationships(array $localIds, int $categoryId) : void
    {
        $associatedProducts = TypeHelper::arrayOfIntegers(CatalogIntegration::withoutReads(function () use ($categoryId) {
            return ProductsRepository::query([
                'type'                => ['variation', 'simple', 'variable'],
                'product_category_id' => [$categoryId],
                'return'              => 'ids',
            ]);
        }));

        if (! empty($addProducts = TypeHelper::arrayOfIntegers(array_diff($localIds, $associatedProducts)))) {
            $this->associateCategoryRelationships($addProducts, $categoryId);
        }

        if (! empty($removeProducts = TypeHelper::arrayOfIntegers(array_diff($associatedProducts, $localIds)))) {
            $this->removeCategoryRelationships($removeProducts, $categoryId);
        }
    }

    /**
     * Dispatches jobs to associate the relationships between products and a category.
     *
     * @param int[] $productIds product IDs
     * @param int $categoryId category ID
     * @return void
     */
    protected function associateCategoryRelationships(array $productIds, int $categoryId) : void
    {
        JobQueue::getNewInstance()->chain([
            BatchAssociateProductCategoryRelationshipsJob::class,
        ])->dispatch([
            'productIds' => $productIds,
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Dispatches jobs to remove the relationships between products and a category.
     *
     * @param int[] $productIds product IDs
     * @param int $categoryId category ID
     * @return void
     */
    protected function removeCategoryRelationships(array $productIds, int $categoryId) : void
    {
        JobQueue::getNewInstance()->chain([
            BatchRemoveProductCategoryRelationshipsJob::class,
        ])->dispatch([
            'productIds' => $productIds,
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Gets the remote product ids from the webhook payload.
     *
     * @param Webhook $webhook
     * @return string[]
     */
    protected function getRemoteProductIdsFromWebhook(Webhook $webhook) : array
    {
        return TypeHelper::arrayOfStrings(ArrayHelper::get(json_decode($webhook->payload, true), 'data.productIds'));
    }
}
