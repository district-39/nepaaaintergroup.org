<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\Categories\Category;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Adapters\CategoryWebhookPayloadAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Exceptions\WebhookProcessingException;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

abstract class AbstractCategoryWebhookHandler extends AbstractWebhookHandler
{
    protected CategoryMapRepository $categoryMapRepository;

    public function __construct(CategoryMapRepository $categoryMapRepository, WebhooksRepository $webhooksRepository)
    {
        $this->categoryMapRepository = $categoryMapRepository;

        parent::__construct($webhooksRepository);
    }

    /**
     * {@inheritDoc}
     */
    protected function getLocalId(Webhook $webhook) : ?int
    {
        if (! $webhook->remoteResourceId) {
            throw new WebhookProcessingException('Category ID not found in webhook data.');
        }

        return $this->getLocalIdFromMapRepository($this->categoryMapRepository, $webhook->remoteResourceId);
    }

    /**
     * Retrieves the Category object from the webhook.
     * @param Webhook $webhook
     * @return Category
     * @throws WebhookProcessingException
     */
    protected function getCategory(Webhook $webhook) : Category
    {
        try {
            $data = TypeHelper::arrayOfStringsAsKeys(ArrayHelper::get(json_decode($webhook->payload, true), 'data'));

            return CategoryWebhookPayloadAdapter::getNewInstance()->convertResponse($data);
        } catch (Exception $e) {
            throw new WebhookProcessingException('Failed to convert webhook payload to Category.', $e);
        }
    }
}
