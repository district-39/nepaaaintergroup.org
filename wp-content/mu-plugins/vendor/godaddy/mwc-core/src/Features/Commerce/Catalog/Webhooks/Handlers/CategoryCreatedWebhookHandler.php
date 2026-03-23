<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\InsertLocalCategoryService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits\CanReconcileCategoryRelationshipsTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers\Traits\CanInsertLocalCategoriesTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.category.created` webhooks.
 */
class CategoryCreatedWebhookHandler extends AbstractCategoryWebhookHandler
{
    use CanInsertLocalCategoriesTrait;
    use CanReconcileCategoryRelationshipsTrait;

    public function __construct(
        CategoryMapRepository $categoryMapRepository,
        ProductMapRepository $productMapRepository,
        WebhooksRepository $webhooksRepository,
        InsertLocalCategoryService $insertLocalCategoryService
    ) {
        $this->productMapRepository = $productMapRepository;
        $this->insertLocalCategoryService = $insertLocalCategoryService;

        parent::__construct($categoryMapRepository, $webhooksRepository);
    }

    /**
     * {@inheritDoc}
     */
    public function shouldHandle(Webhook $webhook) : bool
    {
        if ($this->getLocalId($webhook)) {
            // category already exists
            return false;
        }

        return parent::shouldHandle($webhook);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Webhook $webhook) : void
    {
        if (! $this->shouldHandle($webhook)) {
            return;
        }

        $category = $this->getCategory($webhook);

        $this->maybeReconcileRelationships($this->getRemoteProductIdsFromWebhook($webhook), $this->insertLocalCategory($category));
    }
}
