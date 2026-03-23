<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Payloads;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\PayloadBuilderContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\PayloadContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\PayloadInputContract;

abstract class AbstractResourcePayloadBuilder implements PayloadBuilderContract
{
    /**
     * {@inheritDoc}
     */
    public function build(PayloadInputContract $input) : PayloadContract
    {
        // TODO: Implement method in MWC-13834
        return new EmptyPayload();
    }

    /**
     * Gets a list of field names that will be built by this builder.
     *
     * @param PayloadInputContract $input
     * @return non-empty-string[]
     */
    abstract protected function getFieldNames(PayloadInputContract $input) : array;

    /**
     * Gets a payload input instance to be used as input for the payload builders that this builder uses.
     *
     * @param PayloadInputContract $input
     * @return PayloadInputContract
     */
    protected function prepareInputForFields(PayloadInputContract $input) : PayloadInputContract
    {
        return $input;
    }

    /**
     * Attempts to build the payload for each one of the specified fields using the given input object.
     *
     * @param non-empty-string[] $fields
     * @param PayloadInputContract $input
     * @return array<string, mixed>
     */
    protected function getCombinedDataForFields(array $fields, PayloadInputContract $input) : array
    {
        // TODO: Implement method in MWC-13835
        return [];
    }

    /**
     * Gets a list of payload builders indexed by the name of the field that each of them builds.
     *
     * @return array<string, PayloadBuilderContract>
     */
    abstract protected function getConfig() : array;

    /**
     * Attempts to build the payload for the specified field using the given input object.
     *
     * @param non-empty-string $field
     * @param PayloadInputContract $input
     * @param array<string, PayloadBuilderContract> $config
     * @return PayloadContract
     */
    protected function getPayloadForField(string $field, PayloadInputContract $input, array $config) : PayloadContract
    {
        // TODO: Implement method in MWC-13836
        return new EmptyPayload();
    }

    /**
     * Attempts to get a payload builder.
     *
     * @param non-empty-string $field
     * @param array<string, PayloadBuilderContract> $config
     * @return PayloadBuilderContract|null
     */
    protected function getBuilderForField(string $field, array $config) : ?PayloadBuilderContract
    {
        // TODO: Implement method in MWC-13837
        return null;
    }

    /**
     * Gets the default payload builder for the given field that not included in the config.
     *
     * @param non-empty-string $field
     * @return PayloadBuilderContract|null
     */
    protected function getDefaultPayloadBuilder(string $field) : ?PayloadBuilderContract
    {
        return null;
    }

    /**
     * Creates an instance of PayloadContract using the data provided.
     *
     * @param array<string, mixed> $data
     * @return PayloadContract
     */
    protected function makePayloadFromValue(array $data) : PayloadContract
    {
        // TODO: Implement method in MWC-13838
        return new EmptyPayload();
    }
}
