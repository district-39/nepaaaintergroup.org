<?php

namespace GoDaddy\WordPress\MWC\Core\Features\WebVitals;

use Exception;
use GoDaddy\WordPress\MWC\Common\Components\Contracts\ComponentContract;
use GoDaddy\WordPress\MWC\Common\Components\Traits\HasComponentsFromContainerTrait;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Features\AbstractFeature;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\WebVitals\Interceptors\Handlers\RenderWebVitalsInlineScriptInterceptor;

class WebVitals extends AbstractFeature
{
    use HasComponentsFromContainerTrait;

    /** @var class-string<ComponentContract>[] */
    protected array $componentClasses = [
        RenderWebVitalsInlineScriptInterceptor::class,
    ];

    /**
     * {@inheritDoc}
     */
    public static function getName() : string
    {
        return 'web_vitals';
    }

    public function load() : void
    {
        try {
            $this->loadComponents();
        } catch (Exception $exception) {
            SentryException::getNewInstance("An error occurred trying to load components for the WebVitals feature: {$exception->getMessage()}", $exception);
        }
    }
}
