<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services;

use Exception;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesInputContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesOutputContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\Contracts\ReferencesGatewayContract;

/**
 * Abstract base class for reference services.
 */
abstract class AbstractReferencesService
{
    /** @var ReferencesGatewayContract */
    protected ReferencesGatewayContract $gateway;

    /** @var CommerceContextContract */
    protected CommerceContextContract $commerceContext;

    /**
     * AbstractReferencesService constructor.
     */
    public function __construct(ReferencesGatewayContract $gateway, CommerceContextContract $commerceContext)
    {
        $this->gateway = $gateway;
        $this->commerceContext = $commerceContext;
    }

    /**
     * Gets the store ID for the current context.
     */
    protected function getStoreId() : string
    {
        return $this->commerceContext->getStoreId();
    }

    /**
     * @template T of ReferencesOutputContract
     * @param ReferencesInputContract $input
     * @param class-string<T> $referencesOutputClass expected class name for the output
     * @return T
     * @throws CommerceExceptionContract
     * @throws GatewayRequestException
     */
    protected function getReferences(ReferencesInputContract $input, string $referencesOutputClass) : ReferencesOutputContract
    {
        try {
            // Execute the GraphQL operation using the gateway
            $result = $this->gateway->getReferences($input);

            // Return all product references from the response
            if (! $result instanceof $referencesOutputClass) {
                throw new GatewayRequestException('Invalid response type from gateway');
            }

            return $result;
        } catch (Exception $e) {
            $this->handleException($e, 'Failed to retrieve product references');
        }
    }

    /**
     * Handles exceptions by wrapping them in a GatewayRequestException.
     *
     * @param Exception $exception
     * @param string $message
     * @return never-return
     * @throws GatewayRequestException
     */
    protected function handleException(Exception $exception, string $message) : void
    {
        throw new GatewayRequestException(sprintf(
            '%s: %s',
            $message,
            $exception->getMessage()
        ), $exception);
    }
}
