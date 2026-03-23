<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Helpers;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\PatchProductCategoryAssociationsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits\HasJobRunTrait;

/**
 * Helper class to check the status of the {@see PatchProductCategoryAssociationsJob}.
 */
class PatchProductCategoryIdsJobStatusHelper
{
    use HasJobRunTrait;

    /** @var string name for whether the job has completed */
    protected const JOB_HAS_RUN_OPTION_NAME = 'mwc_patch_product_category_ids_job_has_run';
}
