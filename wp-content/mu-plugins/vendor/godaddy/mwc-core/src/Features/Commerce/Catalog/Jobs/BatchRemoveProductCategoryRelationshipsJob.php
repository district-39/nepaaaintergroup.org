<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Repositories\Exceptions\WordPressRepositoryException;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\TermsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;

class BatchRemoveProductCategoryRelationshipsJob extends AbstractProductCategoryRelationshipsJob
{
    /** @var string JOB_KEY */
    public const JOB_KEY = 'batchRemoveProductCategoryRelationshipsJob';

    /**
     * {@inheritDoc}
     */
    protected function handleChunk(array $productIds, int $categoryId) : void
    {
        $this->removeCategoriesFromProducts($productIds, $categoryId);
    }

    /**
     * Removes the given product IDs from the given category ID.
     *
     * @param int[] $productIds
     * @param int $categoryId
     */
    protected function removeCategoriesFromProducts(array $productIds, int $categoryId) : void
    {
        foreach ($productIds as $productId) {
            try {
                TermsRepository::removeTermsFromObject($productId, [$categoryId], CatalogIntegration::PRODUCT_CATEGORY_TAXONOMY);
            } catch (WordPressRepositoryException $exception) {
                SentryException::getNewInstance("Failed to remove category {$categoryId} from product {$productId}", $exception);
            }
        }
    }
}
