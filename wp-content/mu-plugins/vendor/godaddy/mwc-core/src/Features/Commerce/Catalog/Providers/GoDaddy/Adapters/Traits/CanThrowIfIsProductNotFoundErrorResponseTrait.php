<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Adapters\Traits;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\ResponseContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\ProductNotFoundException;

trait CanThrowIfIsProductNotFoundErrorResponseTrait
{
    /**
     * Throws an exception on error responses.
     *
     * @param ResponseContract $response
     * @throws CommerceExceptionContract
     */
    protected function throwIfIsErrorResponse(ResponseContract $response) : void
    {
        $this->throwIfIsProductNotFoundErrorResponse($response);

        parent::throwIfIsErrorResponse($response);
    }

    /**
     * @throws ProductNotFoundException
     */
    protected function throwIfIsProductNotFoundErrorResponse(ResponseContract $response) : void
    {
        if ($this->isProductNotFoundError($response)) {
            throw new ProductNotFoundException($this->getErrorMessageFromResponse($response));
        }
    }

    /**
     * Determines whether the given response indicates that the requested product is not available.
     *
     * We expect the Catalog Service to respond with one of the following responses
     * when we try to access a product that was deleted or is no longer available:
     *
     * - `{"code":"NOT_FOUND","message":"Not found"}`
     * - `{"code":"NOT_FOUND","message":"Product not found"}`
     */
    protected function isProductNotFoundError(ResponseContract $response) : bool
    {
        if (! $response->isError() || $response->getStatus() !== 404) {
            return false;
        }

        if (! $data = $response->getBody()) {
            return false;
        }

        $message = TypeHelper::string(ArrayHelper::get($data, 'message'), '');

        if (strcasecmp('Product not found', $message) !== 0 && strcasecmp('Not found', $message) !== 0) {
            return false;
        }

        $code = TypeHelper::string(ArrayHelper::get($data, 'code'), '');

        return strcasecmp('NOT_FOUND', $code) === 0;
    }
}
