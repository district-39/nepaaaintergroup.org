<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Factories;

use GoDaddy\WordPress\MWC\Common\Models\AbstractAttachment;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\MediaMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Strategies\Contracts\MediaMappingStrategyContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Strategies\MediaMappingStrategy;

class MediaMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    protected MediaMapRepository $mediaMapRepository;

    public function __construct(CommerceContextContract $commerceContext, MediaMapRepository $mediaMapRepository)
    {
        parent::__construct($commerceContext);

        $this->mediaMapRepository = $mediaMapRepository;
    }

    /**
     * Gets the main mapping strategy for media.
     *
     * @param AbstractAttachment|object $model
     * @return MediaMappingStrategyContract|null
     */
    public function getPrimaryMappingStrategyFor(object $model) : ?MediaMappingStrategyContract
    {
        return $model instanceof AbstractAttachment && $model->getId()
            ? $this->getMediaMappingStrategy()
            : null;
    }

    /**
     * Gets the media mapping strategy.
     */
    protected function getMediaMappingStrategy() : MediaMappingStrategyContract
    {
        return new MediaMappingStrategy($this->mediaMapRepository);
    }

    /**
     * Gets the fallback mapping strategy.
     */
    public function getSecondaryMappingStrategy() : MediaMappingStrategyContract
    {
        // we do not have a secondary mapping strategy at this time
        return new class implements MediaMappingStrategyContract {
            public function saveRemoteId(object $model, string $remoteId) : void
            {
                // no-op
            }

            public function getRemoteId(object $model) : ?string
            {
                // no-op
                return null;
            }
        };
    }
}
