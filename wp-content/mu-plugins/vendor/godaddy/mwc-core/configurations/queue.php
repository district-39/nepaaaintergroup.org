<?php

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Backfill\Jobs\BackfillProductCategoriesJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Backfill\Jobs\BackfillProductsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchAssociateProductCategoryRelationshipsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchCreateOrUpdateProductsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\BatchRemoveProductCategoryRelationshipsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\CreateOrUpdateProductJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\PatchProductCategoryAssociationsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\PatchProductListOptionsJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\UpdateVariationNamesJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Jobs\CategoryMappingJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Jobs\ProductMappingJob;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobSettings;

return [
    /*
     * Registry of `QueueableJobContract` implementations.
     * Each job must have a registered, unique string key so that class names can be "serialized" upon insertion into
     * the Action Scheduler database.
     */
    'jobs' => [
        BackfillProductCategoriesJob::JOB_KEY => [
            'job'      => BackfillProductCategoriesJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                ],
            ],
        ],
        BackfillProductsJob::JOB_KEY => [
            'job'      => BackfillProductsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    // this is lower, as we're backfilling products and inventory in one job (double the API requests)
                    'maxPerBatch' => 30,
                ],
            ],
        ],
        BatchCreateOrUpdateProductsJob::JOB_KEY => [
            'job'      => BatchCreateOrUpdateProductsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => defined('MWC_MAX_PRODUCTS_PER_WRITE_BATCH') ? MWC_MAX_PRODUCTS_PER_WRITE_BATCH : 50,
                ],
            ],
        ],
        BatchRemoveProductCategoryRelationshipsJob::JOB_KEY => [
            'job'      => BatchRemoveProductCategoryRelationshipsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => defined('MWC_MAX_PRODUCTS_CATEGORIES_PER_RECONCILE_BATCH') ? MWC_MAX_PRODUCTS_CATEGORIES_PER_RECONCILE_BATCH : 50,
                ],
            ],
        ],
        BatchAssociateProductCategoryRelationshipsJob::JOB_KEY => [
            'job'      => BatchAssociateProductCategoryRelationshipsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => defined('MWC_MAX_PRODUCTS_CATEGORIES_PER_RECONCILE_BATCH') ? MWC_MAX_PRODUCTS_CATEGORIES_PER_RECONCILE_BATCH : 50,
                ],
            ],
        ],
        CreateOrUpdateProductJob::JOB_KEY => [
            'job' => CreateOrUpdateProductJob::class,
        ],
        UpdateVariationNamesJob::JOB_KEY => [
            'job'      => UpdateVariationNamesJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => 15,
                ],
            ],
        ],
        PatchProductCategoryAssociationsJob::JOB_KEY => [
            'job'      => PatchProductCategoryAssociationsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => 50,
                ],
            ],
        ],
        PatchProductListOptionsJob::JOB_KEY => [
            'job'      => PatchProductListOptionsJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => 50,
                ],
            ],
        ],
        CategoryMappingJob::JOB_KEY => [
            'job'      => CategoryMappingJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => 50,
                ],
            ],
        ],
        ProductMappingJob::JOB_KEY => [
            'job'      => ProductMappingJob::class,
            'settings' => [
                'class'  => BatchJobSettings::class,
                'values' => [
                    'maxPerBatch' => 50,
                ],
            ],
        ],
    ],
];
