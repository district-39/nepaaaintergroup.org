<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Common\Exceptions\AdapterException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\UpdateLocalCategoryService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits\CanReconcileCategoryRelationshipsTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Traits\ShouldHandleLocalIdTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.category.updated` webhooks.
 */
class CategoryUpdatedWebhookHandler extends AbstractCategoryWebhookHandler
{
    use ShouldHandleLocalIdTrait;
    use CanReconcileCategoryRelationshipsTrait;

    protected UpdateLocalCategoryService $updateLocalCategoryService;

    public function __construct(
        UpdateLocalCategoryService $updateLocalCategoryService,
        CategoryMapRepository $categoryMapRepository,
        ProductMapRepository $productMapRepository,
        WebhooksRepository $webhooksRepository
    ) {
        $this->productMapRepository = $productMapRepository;
        $this->updateLocalCategoryService = $updateLocalCategoryService;

        parent::__construct($categoryMapRepository, $webhooksRepository);
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterException|WebhookProcessingException
     */
    public function handle(Webhook $webhook) : void
    {
        if (! $this->shouldHandle($webhook)) {
            return;
        }

        $category = $this->getCategory($webhook);

        $this->updateLocalCategoryService->update($category, $this->localId);

        $this->maybeReconcileRelationships($this->getRemoteProductIdsFromWebhook($webhook), $this->localId);
    }
}
