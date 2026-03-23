<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Traits;

use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;

/**
 * Trait for handling webhooks that should only be handled if the remote resource associated with the webhook has a known local ID.
 */
trait ShouldHandleLocalIdTrait
{
    /**
     * {@inheritDoc}
     *
     * @phpstan-assert-if-true positive-int $this->localId
     */
    public function shouldHandle(Webhook $webhook) : bool
    {
        if (! $this->localId = $this->getLocalId($webhook)) {
            // nothing to delete
            return false;
        }

        return parent::shouldHandle($webhook);
    }
}
