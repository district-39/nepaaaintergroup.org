<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Payloads;

use GoDaddy\WordPress\MWC\Common\Contracts\CanConvertToArrayContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\HasPayloadFieldNameContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\PayloadBuilderContract;

/**
 * Describes a payload builder that uses the field name associated with the payload that it’s trying to build.
 *
 * TODO: Why do this class and {@see HasPayloadFieldNameContract} define generic parameters? {wvega 2024-12-26}
 *       They may be needed once all the pieces are in place, but right now we don't have access to the discovery
 *       document to double check.
 *
 * @template TResource of CanConvertToArrayContract
 * @template TModel of object
 * @implements HasPayloadFieldNameContract<TResource, TModel>
 */
abstract class AbstractFieldPayloadBuilder implements PayloadBuilderContract, HasPayloadFieldNameContract
{
    /** @var non-empty-string|null The name of the field associated with this payload builder */
    protected ?string $fieldName = null;

    /**
     * {@inheritDoc}
     */
    public function getFieldName() : ?string
    {
        return $this->fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldName(?string $value)
    {
        $this->fieldName = $value;

        return $this;
    }
}
