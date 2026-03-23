<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use GoDaddy\WordPress\MWC\Common\Repositories\Exceptions\WordPressRepositoryException;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\TermsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;

/**
 * Job to associate categories and products. This job will repeat until all batches have been processed.
 */
class BatchAssociateProductCategoryRelationshipsJob extends AbstractProductCategoryRelationshipsJob
{
    /** @var string Represents this jobs key. */
    public const JOB_KEY = 'batchAssociateProductCategoryRelationshipsJob';

    /**
     * {@inheritDoc}
     *
     * @throws WordPressRepositoryException
     */
    protected function handleChunk(array $productIds, int $categoryId) : void
    {
        $this->associateCategoriesToProducts($productIds, $categoryId);
    }

    /**
     * Associates products with a category.
     *
     * @param int[] $localIds local product ids
     * @param int $categoryId
     * @return void
     * @throws WordPressRepositoryException
     */
    protected function associateCategoriesToProducts(array $localIds, int $categoryId) : void
    {
        foreach ($localIds as $productId) {
            TermsRepository::addTermsToObject($productId, [$categoryId], CatalogIntegration::PRODUCT_CATEGORY_TAXONOMY);
        }
    }
}
