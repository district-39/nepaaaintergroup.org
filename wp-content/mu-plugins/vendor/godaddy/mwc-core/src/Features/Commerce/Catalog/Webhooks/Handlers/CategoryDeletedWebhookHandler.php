<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Handlers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Helpers\RemoteCategoryNotFoundHelper;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Traits\ShouldHandleLocalIdTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Webhooks\DataObjects\Webhook;
use GoDaddy\WordPress\MWC\Core\Webhooks\Repositories\WebhooksRepository;

/**
 * Handles `commerce.category.deleted` webhooks.
 */
class CategoryDeletedWebhookHandler extends AbstractCategoryWebhookHandler
{
    use ShouldHandleLocalIdTrait;

    /** @var RemoteCategoryNotFoundHelper */
    protected RemoteCategoryNotFoundHelper $remoteCategoryNotFoundHelper;

    public function __construct(CategoryMapRepository $categoryMapRepository, WebhooksRepository $webhooksRepository, RemoteCategoryNotFoundHelper $remoteCategoryNotFoundHelper)
    {
        $this->remoteCategoryNotFoundHelper = $remoteCategoryNotFoundHelper;

        parent::__construct($categoryMapRepository, $webhooksRepository);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Webhook $webhook) : void
    {
        if (! $this->shouldHandle($webhook)) {
            return;
        }

        $this->remoteCategoryNotFoundHelper->handle($this->localId);
    }
}
