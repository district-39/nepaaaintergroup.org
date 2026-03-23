<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Gateways\Traits;

use Exception;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\CommerceException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Adapters\AbstractGatewayRequestAdapter;

/**
 * Trait for handling adapted requests with enhanced exception handling.
 *
 * Wraps parent::doAdaptedRequest() calls to provide consistent CommerceException handling
 * across gateway implementations.
 */
trait CanDoAdaptedRequestWithExceptionHandlingTrait
{
    /**
     * Performs the request and returns the adapted response.
     *
     * @param AbstractGatewayRequestAdapter $adapter
     * @return mixed
     * @throws CommerceExceptionContract
     */
    protected function doAdaptedRequest(AbstractGatewayRequestAdapter $adapter)
    {
        try {
            return parent::doAdaptedRequest($adapter);
        } catch (Exception $exception) {
            if ($exception instanceof CommerceExceptionContract) {
                throw $exception;
            }

            throw new CommerceException($exception->getMessage(), $exception);
        }
    }
}
