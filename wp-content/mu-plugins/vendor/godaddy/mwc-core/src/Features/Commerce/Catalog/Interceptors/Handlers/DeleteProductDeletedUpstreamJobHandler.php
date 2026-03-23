<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Operations\ListProductsOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\DeleteLocalProductService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Responses\Contracts\ListProductsResponseContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;

/**
 * Handle deleting local products that are marked as deleted upstream.
 */
class DeleteProductDeletedUpstreamJobHandler extends AbstractInterceptorHandler
{
    private ProductsServiceContract $productsService;
    private DeleteLocalProductService $deleteLocalProductService;

    public function __construct(
        ProductsServiceContract $productsService,
        DeleteLocalProductService $deleteLocalProductService
    ) {
        $this->productsService = $productsService;
        $this->deleteLocalProductService = $deleteLocalProductService;
    }

    /**
     * {@inheritDoc}
     */
    public function run(...$args)
    {
        $localId = $this->getLocalId($args);

        if ($localId && $this->isLocalProductDeletedUpstream($localId)) {
            $this->deleteLocalProductService->delete($localId);
        }
    }

    /**
     * @param mixed[] $args
     */
    protected function getLocalId(array $args) : ?int
    {
        $value = TypeHelper::int($args[0] ?? 0, 0);

        return $value > 0 ? $value : null;
    }

    protected function isLocalProductDeletedUpstream(int $localId) : bool
    {
        $remoteProduct = $this->getRemoteProductIncludingDeleted($localId);

        return ! is_null($remoteProduct) && ! is_null($remoteProduct->deletedAt);
    }

    protected function getRemoteProductIncludingDeleted(int $localId) : ?ProductBase
    {
        try {
            $response = $this->productsService->listProducts(
                (new ListProductsOperation())->setLocalIds([$localId])->setIncludeDeleted(true)
            );
        } catch (CommerceExceptionContract|Exception $e) {
            SentryException::getNewInstance('Failed to get remote product deleted upstream: '.$e->getMessage(), $e);

            return null;
        }

        return $this->getRemoteProductFromResponse($response, $localId);
    }

    protected function getRemoteProductFromResponse(ListProductsResponseContract $response, int $localId) : ?ProductBase
    {
        foreach ($response->getProducts() as $productAssociation) {
            if ($productAssociation->localId === $localId) {
                return $productAssociation->remoteResource;
            }
        }

        return null;
    }
}
