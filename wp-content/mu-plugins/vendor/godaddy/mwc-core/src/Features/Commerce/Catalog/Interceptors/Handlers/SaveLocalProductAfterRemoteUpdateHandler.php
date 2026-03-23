<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Events\Subscribers\DispatchJobToSaveLocalProductSubscriber;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\SaveLocalProductAfterRemoteUpdateInterceptor;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Operations\ReadProductOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\UpdateLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequest404Exception;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingProductRemoteIdException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\ProductMappingNotFoundException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\ProductNotFoundException;

/**
 * Handler for {@see SaveLocalProductAfterRemoteUpdateInterceptor} async job callback.
 */
class SaveLocalProductAfterRemoteUpdateHandler extends AbstractInterceptorHandler
{
    protected ProductsServiceContract $productsService;
    protected UpdateLocalProductService $updateLocalProductService;

    public function __construct(
        ProductsServiceContract $productsService,
        UpdateLocalProductService $updateLocalProductService
    ) {
        $this->productsService = $productsService;
        $this->updateLocalProductService = $updateLocalProductService;
    }

    /**
     * When a product is updated remotely, we re-save that product locally.
     * {@see DispatchJobToSaveLocalProductSubscriber}.
     *
     * Saving the local product when it's been updated remotely has these benefits:
     *
     * - Local caches will be purged. This means we won't be using outdated caches to serve product data; we'll get the latest
     *   changes from upstream. @link https://godaddy-corp.atlassian.net/browse/MWC-12725
     * - `woocommerce_update_product` hooks will fire when a product has been changed upstream. This creates a more expected
     *   and standard WooCommerce experience.
     *
     * @param ...$args
     * @return void
     */
    public function run(...$args) : void
    {
        try {
            $localId = $this->getLocalId($args);
            $this->updateLocalProductService->update($this->getRemoteProduct($localId), $localId);
        } catch(Exception $e) {
            SentryException::getNewInstance($e->getMessage(), $e);
        }
    }

    /**
     * Gets the local ID from arguments passed to the handler.
     *
     * @param array<mixed> $args
     * @return int
     * @throws Exception
     */
    protected function getLocalId(array $args) : int
    {
        $localId = TypeHelper::int(ArrayHelper::get($args, 0), 0);

        if (empty($localId)) {
            throw new Exception('Missing local product ID in job arguments.');
        }

        return $localId;
    }

    /**
     * Fetches the remote {@see ProductBase} DTO from the platform.
     *
     * @param int $localProductId
     * @return ProductBase
     * @throws GatewayRequest404Exception|GatewayRequestException|MissingProductRemoteIdException|ProductMappingNotFoundException
     * @throws ProductNotFoundException
     */
    protected function getRemoteProduct(int $localProductId) : ProductBase
    {
        return $this->productsService->readProduct(
            ReadProductOperation::getNewInstance()->setLocalId($localProductId)
        )->getProduct();
    }
}
