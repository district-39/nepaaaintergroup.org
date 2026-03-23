<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\InsertLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers\Traits\CanInsertLocalProductsTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.product.created` webhooks.
 */
class ProductCreatedWebhookHandler extends AbstractProductWebhookHandler
{
    use CanInsertLocalProductsTrait;

    public function __construct(ProductMapRepository $productMapRepository, WebhooksRepository $webhooksRepository, InsertLocalProductService $insertLocalProductService)
    {
        $this->insertLocalProductService = $insertLocalProductService;

        parent::__construct($productMapRepository, $webhooksRepository);
    }

    /**
     * {@inheritDoc}
     *
     * @throws WebhookProcessingException
     *
     * @phpstan-assert-if-true null $this->localId
     */
    public function shouldHandle(Webhook $webhook) : bool
    {
        if ($this->localId = $this->getLocalId($webhook)) {
            // product already exists
            return false;
        }

        return parent::shouldHandle($webhook);
    }

    /**
     * {@inheritDoc}
     *
     * @throws WebhookProcessingException
     */
    public function handle(Webhook $webhook) : void
    {
        if (! $this->shouldHandle($webhook)) {
            return;
        }

        $productBase = $this->getProductBase($webhook);

        // This is a child product.
        if ($productBase->parentId) {
            return;
        }

        $this->insertLocalProduct($webhook, $productBase);
    }
}
