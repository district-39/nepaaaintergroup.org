<?php

namespace GoDaddy\WordPress\MWC\Core\Traits;

use GoDaddy\WordPress\MWC\Common\Platforms\Exceptions\PlatformRepositoryException;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformRepositoryFactory;

trait CanDetermineWhetherIsStagingSiteTrait
{
    /**
     * Determines whether the site is a staging site.
     *
     * Assumes the site is a staging site if an error occurs trying to find out.
     */
    protected static function isStagingSite() : bool
    {
        try {
            return PlatformRepositoryFactory::getNewInstance()->getPlatformRepository()->isStagingSite();
        } catch (PlatformRepositoryException $exception) {
            return true;
        }
    }
}
