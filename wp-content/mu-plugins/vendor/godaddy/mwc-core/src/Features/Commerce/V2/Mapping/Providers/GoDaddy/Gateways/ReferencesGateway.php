<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\GoDaddy\Gateways;

use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Gateways\AbstractGateway;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Gateways\Traits\CanDoAdaptedRequestWithExceptionHandlingTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters\ListReferencesRequestAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters\SkuReferencesRequestAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesInputContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesOutputContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\Contracts\ReferencesGatewayContract;

/**
 * Gateway for handling SKU and List references requests.
 */
class ReferencesGateway extends AbstractGateway implements ReferencesGatewayContract
{
    use CanGetNewInstanceTrait;
    use CanDoAdaptedRequestWithExceptionHandlingTrait;

    /**
     * Retrieves references for the given input.
     *
     * @param ReferencesInputContract $input
     * @return ReferencesOutputContract
     * @throws CommerceExceptionContract
     */
    public function getReferences(ReferencesInputContract $input) : ReferencesOutputContract
    {
        if ($input instanceof SkuReferencesInput) {
            return $this->getProductReferences($input);
        }

        if ($input instanceof ListReferencesInput) {
            return $this->getCategoriesReferences($input);
        }

        throw new GatewayRequestException('Unsupported input type for references request');
    }

    /**
     * Retrieves product references for the given SKU references input.
     *
     * @param SkuReferencesInput $input
     * @return SkuReferencesOutput
     * @throws CommerceExceptionContract
     */
    public function getProductReferences(SkuReferencesInput $input) : SkuReferencesOutput
    {
        /** @var SkuReferencesOutput $result */
        $result = $this->doAdaptedRequest(SkuReferencesRequestAdapter::getNewInstance($input));

        return $result;
    }

    /**
     * Retrieves categories references for the given categories references input.
     *
     * @param ListReferencesInput $input
     * @return ListReferencesOutput
     * @throws CommerceExceptionContract
     */
    public function getCategoriesReferences(ListReferencesInput $input) : ListReferencesOutput
    {
        /** @var ListReferencesOutput $result */
        $result = $this->doAdaptedRequest(ListReferencesRequestAdapter::getNewInstance($input));

        return $result;
    }
}
