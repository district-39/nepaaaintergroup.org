<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits;

/**
 * Trait for managing the patch job run status.
 */
trait HasJobRunTrait
{
    /**
     * Has the patch job ever run?
     *
     * @return bool
     */
    public static function hasRun() : bool
    {
        return ! empty(get_option(static::JOB_HAS_RUN_OPTION_NAME, false));
    }

    /**
     * Sets the patch job has run status.
     *
     * @param bool $value
     * @return void
     */
    public static function setHasRun(bool $value = true) : void
    {
        update_option(static::JOB_HAS_RUN_OPTION_NAME, $value);
    }
}
