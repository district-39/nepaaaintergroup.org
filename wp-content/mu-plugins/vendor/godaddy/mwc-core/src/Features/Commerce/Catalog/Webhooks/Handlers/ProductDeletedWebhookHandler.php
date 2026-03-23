<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\DeleteLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Traits\ShouldHandleLocalIdTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.product.deleted` webhooks.
 */
class ProductDeletedWebhookHandler extends AbstractProductWebhookHandler
{
    use ShouldHandleLocalIdTrait;

    protected DeleteLocalProductService $deleteLocalProductService;

    public function __construct(
        DeleteLocalProductService $deleteLocalProductService,
        ProductMapRepository $productMapRepository,
        WebhooksRepository $webhooksRepository
    ) {
        $this->deleteLocalProductService = $deleteLocalProductService;

        parent::__construct($productMapRepository, $webhooksRepository);
    }

    /**
     * Handles `commerce.product.deleted` events by also deleting the corresponding local product.
     *
     * @param Webhook $webhook
     * @return void
     * @throws WebhookProcessingException
     */
    public function handle(Webhook $webhook) : void
    {
        if (! $this->shouldHandle($webhook)) {
            return;
        }

        $this->deleteLocalProductService->delete($this->localId);
    }
}
