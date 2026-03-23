<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers\Traits;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\ListRemoteVariantsJobHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\InsertLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;

trait CanInsertLocalProductsTrait
{
    protected InsertLocalProductService $insertLocalProductService;

    /**
     * Creates a local product using the information from the given {@see ProductBase} data object.
     *
     * @throws WebhookProcessingException
     */
    protected function insertLocalProduct(Webhook $webhook, ProductBase $productBase) : void
    {
        try {
            $this->insertLocalProductService->insert($productBase);
        } catch (CommerceExceptionContract $e) {
            throw new WebhookProcessingException('Failed to insert remote product: '.$webhook->remoteResourceId);
        }

        $this->maybeCreateVariantProduct($productBase);
    }

    /**
     * Maybe create a variant product.
     *
     * If the parent product already exists, we can create it.
     */
    protected function maybeCreateVariantProduct(ProductBase $productBase) : void
    {
        if (! $productBase->variants) {
            return;
        }

        ListRemoteVariantsJobHandler::scheduleListVariantsJob(
            ListRemoteVariantsJobHandler::getChunkedIds($productBase->variants)
        );
    }
}
