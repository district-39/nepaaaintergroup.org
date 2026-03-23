<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Webhooks\Handlers\Contracts\WebhookEventTypeHandlerContract;
use GoDaddy\WordPress\MWC\Core\Repositories\AbstractResourceMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Abstract base for handlers that process webhooks.
 */
abstract class AbstractWebhookHandler implements WebhookEventTypeHandlerContract
{
    /** @var positive-int|null */
    protected ?int $localId;

    protected WebhooksRepository $webhooksRepository;

    public function __construct(WebhooksRepository $webhooksRepository)
    {
        $this->webhooksRepository = $webhooksRepository;
    }

    /**
     * Determine if an incoming webhook is stale based on previously received webhook data.
     *
     * @param Webhook $incomingWebhook
     * @return bool
     * @throws WebhookProcessingException
     */
    protected function incomingWebhookIsStale(Webhook $incomingWebhook) : bool
    {
        if (! $incomingWebhook->remoteResourceId) {
            throw new WebhookProcessingException('Remote Resource ID not found in the incoming webhook data.');
        }

        $existingWebhookRecord = $this->webhooksRepository->getLatestCompletedWebhookByResourceId($incomingWebhook->remoteResourceId);
        if (! $existingWebhookRecord) {
            return false;
        }

        // Incoming webhook is stale if a newer one has already been proceeded
        return $incomingWebhook->occurredAt <= $existingWebhookRecord->occurredAt;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldHandle(Webhook $webhook) : bool
    {
        if (! $webhook->remoteResourceId) {
            throw new WebhookProcessingException('Remote Resource ID not found in webhook data.');
        }

        if (! $webhook->occurredAt) {
            throw new WebhookProcessingException('Webhook timestamp not found in webhook data.');
        }

        if ($this->incomingWebhookIsStale($webhook)) {
            return false;
        }

        return true;
    }

    /**
     * Gets the local ID for the remoteResourceId in a Webhook.
     *
     * @param Webhook $webhook
     * @return positive-int|null
     * @throws WebhookProcessingException
     */
    abstract protected function getLocalId(Webhook $webhook) : ?int;

    /**
     * Uses the given {@see AbstractResourceMapRepository} instance to retrieve the local ID for the given remote ID.
     *
     * @return positive-int|null
     */
    protected function getLocalIdFromMapRepository(AbstractResourceMapRepository $mapRepository, string $remoteId) : ?int
    {
        $localId = $mapRepository->getLocalId($remoteId);

        return $localId > 0 ? $localId : null;
    }
}
