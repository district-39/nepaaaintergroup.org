<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog;

use GoDaddy\WordPress\MWC\Common\Components\Exceptions\ComponentClassesNotDefinedException;
use GoDaddy\WordPress\MWC\Common\Components\Exceptions\ComponentLoadFailedException;
use GoDaddy\WordPress\MWC\Common\Components\Traits\HasComponentsFromContainerTrait;
use GoDaddy\WordPress\MWC\Common\Features\AbstractFeature;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\InitiateRemoteProductOptionsListPatch;

/**
 * Feature that initiates the remote product list options patch.
 *
 * These products updates are required to correct remote products that have been with non-variant product attributes
 * mapped to the remote `options` array as `LIST` options.
 */
class CommerceRemoteProductListOptionsUpdate extends AbstractFeature
{
    use HasComponentsFromContainerTrait;

    /** @var class-string[] alphabetically ordered list of components to load */
    protected array $componentClasses = [
        InitiateRemoteProductOptionsListPatch::class,
    ];

    /**
     * {@inheritDoc}
     */
    public static function getName() : string
    {
        return 'commerce_remote_product_list_options';
    }

    /**
     * {@inheritDoc}
     *
     * @throws ComponentClassesNotDefinedException|ComponentLoadFailedException
     */
    public function load() : void
    {
        $this->loadComponents();
    }
}
