<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\WriteProductService;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\QueueableJobTrait;
use WC_Product;

/**
 * Job to create or update a product in the remote platform.
 */
class CreateOrUpdateProductJob implements QueueableJobContract
{
    use QueueableJobTrait;

    /** @var string represents the key of this job */
    public const JOB_KEY = 'createOrUpdateProductJob';

    protected WriteProductService $writeProductService;

    public function __construct(WriteProductService $writeProductService)
    {
        $this->writeProductService = $writeProductService;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function handle() : void
    {
        if ($this->shouldHandle()) {
            if ($localId = TypeHelper::int(ArrayHelper::get($this->args, '0'), 0)) {
                $this->writeProduct($localId);
            }
        }

        $this->jobDone();
    }

    /**
     * Writes the local product to the remote platform.
     *
     * @param int $localId
     * @return void
     * @throws Exception
     */
    protected function writeProduct(int $localId) : void
    {
        $wooProduct = CatalogIntegration::withoutReads(function () use ($localId) {
            clean_post_cache($localId);
            $wooProduct = ProductsRepository::get($localId);
            clean_post_cache($localId);

            return $wooProduct;
        });

        if (! $wooProduct instanceof WC_Product) {
            throw new Exception('Failed to retrieve local WC_Product object.');
        }

        try {
            $this->writeProductService->write($wooProduct);
        } catch(Exception $e) {
            SentryException::getNewInstance('Failed to write product to the platform: '.$e->getMessage(), $e);
        }
    }

    /**
     * Should the job be handled?
     *
     * @return bool
     */
    protected function shouldHandle() : bool
    {
        return ! empty($this->args);
    }
}
