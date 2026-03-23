<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers\Traits;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\Categories\Category;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\InsertLocalCategoryService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;

trait CanInsertLocalCategoriesTrait
{
    protected InsertLocalCategoryService $insertLocalCategoryService;

    /**
     * Creates a local category using the information from the given {@see Category} data object.
     *
     * @throws WebhookProcessingException
     */
    protected function insertLocalCategory(Category $category) : int
    {
        try {
            return $this->insertLocalCategoryService->insert($category);
        } catch (CommerceExceptionContract $e) {
            throw new WebhookProcessingException('Failed to insert category.', $e);
        }
    }
}
