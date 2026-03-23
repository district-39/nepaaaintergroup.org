<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\ListRemoteVariantsJobHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\InsertLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\UpdateLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers\Traits\CanInsertLocalProductsTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.product.updated` webhooks.
 */
class ProductUpdatedWebhookHandler extends AbstractProductWebhookHandler
{
    use CanInsertLocalProductsTrait;

    protected ProductMapRepository $productMapRepository;
    protected UpdateLocalProductService $updateLocalProductService;

    public function __construct(
        ProductMapRepository $productMapRepository,
        InsertLocalProductService $insertLocalProductService,
        UpdateLocalProductService $updateLocalProductService,
        WebhooksRepository $webhooksRepository
    ) {
        $this->productMapRepository = $productMapRepository;
        $this->insertLocalProductService = $insertLocalProductService;
        $this->updateLocalProductService = $updateLocalProductService;

        parent::__construct($productMapRepository, $webhooksRepository);
    }

    /**
     * {@inheritDoc}
     *
     * @phpstan-assert-if-true ProductBase $this->productBase
     */
    public function shouldHandle(Webhook $webhook) : bool
    {
        if (($this->productBase = $this->getProductBase($webhook))->parentId) {
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

        if ($this->localId = $this->getLocalId($webhook)) {
            $this->updateLocalProduct($this->productBase, $this->localId);
        } else {
            $this->insertLocalProduct($webhook, $this->productBase);
        }
    }

    /**
     * Updates a local product using the information from the given {@see ProductBase} data object.
     *
     * @param ProductBase $productBase
     * @param positive-int $localId
     */
    protected function updateLocalProduct(ProductBase $productBase, int $localId) : void
    {
        $this->updateLocalProductService->update($productBase, $localId);

        $this->maybeScheduleVariantJobs($productBase->variants);
    }

    /**
     * Schedules variant jobs if necessary.
     *
     * Scheduling the jobs avoids any possible race conditions where parents may be updated after their children.
     * When WooCommerce updates children it depends on database content from the parent, so we need to ensure that
     * the parent is updated first.
     *
     * @param ?string[] $variantIds
     * @return void
     */
    protected function maybeScheduleVariantJobs(?array $variantIds) : void
    {
        if (empty($variantIds)) {
            return;
        }

        ListRemoteVariantsJobHandler::scheduleListVariantsJob(
            ListRemoteVariantsJobHandler::getChunkedIds($variantIds)
        );
    }
}
