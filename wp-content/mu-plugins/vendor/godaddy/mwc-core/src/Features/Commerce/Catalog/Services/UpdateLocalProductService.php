<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\AdapterException;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\Exceptions\WordPressRepositoryException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters\ProductBaseAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters\ProductPostMetaSynchronizer;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters\ProductPostStatusAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingProductLocalIdForParentException;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters\ProductAdapter;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Models\Products\Product;
use WC_Post_Data;
use WC_Product;

class UpdateLocalProductService
{
    /** @var ProductBaseAdapter adapter for {@see ProductBase} objects */
    protected ProductBaseAdapter $productBaseAdapter;

    /** @var AttachmentsService service class to help insert any attachments we don't have locally */
    protected AttachmentsService $attachmentsService;

    protected ProductPostMetaSynchronizer $productPostMetaSynchronizer;

    private ProductPostStatusAdapter $productPostStatusAdapter;

    public function __construct(
        ProductBaseAdapter $productBaseAdapter,
        AttachmentsService $attachmentsService,
        ProductPostMetaSynchronizer $productPostMetaSynchronizer,
        ProductPostStatusAdapter $productPostStatusAdapter
    ) {
        $this->productBaseAdapter = $productBaseAdapter;
        $this->attachmentsService = $attachmentsService;
        $this->productPostMetaSynchronizer = $productPostMetaSynchronizer;
        $this->productPostStatusAdapter = $productPostStatusAdapter;
    }

    /**
     * Updates a local version {@see Product} of the remote resource {@see ProductBase} into the local database.
     *
     * @param ProductBase $productBase
     * @param int $localId
     * @return void
     */
    public function update(ProductBase $productBase, int $localId) : void
    {
        try {
            /*
             * Gets a WC_Product instance from the supplied local product ID. This involves:
             *  - Find the corresponding remote UUID that matches the local ID.
             *  - Fetch the full product data from the platform.
             *  - Adapt that ProductBase DTO into a core Product object.
             *  - Adapt that core Product object into a WC_Product object.
             *
             * Note: we specifically want to run this through all the above adapters in order to ensure we have the
             * full set of remote data. If we just did `wc_get_product($id)->save()` and relied on our reads to take effect,
             * when fetching the product, we wouldn't get category associations saved, as we don't have hooks in place
             * to headlessly read those at this time.
             */
            $wcProduct = $this->makeWooProduct($productBase, $localId);

            $this->unhookDeferredProductSync();

            /*
             * Calling WC_Product::save() below triggers `jetpack_sync_save_post`.
             * Something in this action causes what we believe to be an infinite loop inside Jetpack's code.
             * Removing all callbacks from that action resolves the issue. However, we should spend some time to
             * investigate the root cause, to come up with a more thorough solution. Done in MWC-15139
             * {agibson 2024-01-08}
             */
            remove_all_actions('jetpack_sync_save_post');

            CatalogIntegration::withoutWrites(fn () => $this->saveAndMaybeSyncProduct($wcProduct, $productBase));
        } catch(Exception $e) {
            SentryException::getNewInstance($e->getMessage(), $e);
        }
    }

    /**
     * Saves the local WooCommerce product, and maybe syncs the local parent product.
     */
    protected function saveAndMaybeSyncProduct(WC_Product $product, ProductBase $remoteProduct) : void
    {
        $this->productPostMetaSynchronizer->syncProductMeta($product, $remoteProduct);

        // we disable reads here to ensure that `UpdateProductMetaCacheHandler` does not run again while we're in the process of saving
        CatalogIntegration::withoutReads(function () use ($product) {
            $product->save();
        });

        /*
         * We disabled the deferred product sync a bit above, because we're unable to control "without writes" when it's
         * deferred. But if we run it immediately here we can control it and ensure it runs without writes being
         * enabled, in which case it will not trigger an infinite loop.
         */
        $this->maybeSyncParentProduct($product);
    }

    /**
     * Makes an instance of {@see WC_Product} from the supplied Product Base and local product ID.
     *
     * @param ProductBase $remoteProduct
     * @param int $localId
     * @return WC_Product
     * @throws Exception
     */
    protected function makeWooProduct(ProductBase $remoteProduct, int $localId) : WC_Product
    {
        $coreProduct = $this->makeCoreProduct($remoteProduct, $localId);
        $wcProduct = $this->getWcProductInstance($coreProduct);

        /*
         * Set properties on the $coreProduct object that are not supported by the Commerce Platform, this allows
         * ProductAdapter to convert the $wcProduct object into a $coreProduct object with all the necessary data.
         */
        $coreProduct = $this->setCoreProductProperties($coreProduct, $remoteProduct, $wcProduct);

        return ProductAdapter::getNewInstance($wcProduct)
            ->convertToSource($coreProduct, false); // getNewInstance must be false so that we use our fetched WC_Product instance above
    }

    /**
     * Gets a new instance of {@see WC_Product}.
     *
     * We need to get the instance using `wc_get_product()` so that existing database details are pre-populated.
     * If we manually instantiate an empty WC_Product object then Woo will not properly save empty values, because it
     * will not recognize that they've been changed.
     *
     * @param Product $product
     * @return WC_Product
     * @throws Exception
     */
    protected function getWcProductInstance(Product $product) : WC_Product
    {
        if (! $localId = $product->getId()) {
            throw new Exception('Missing local product ID.');
        }

        /** @var WC_Product|null|false $wooProduct */
        $wooProduct = CatalogIntegration::withoutReads(fn () => wc_get_product($localId));
        if (! $wooProduct instanceof WC_Product) {
            throw new Exception('Failed to retrieve local WC_Product object.');
        }

        return $wooProduct;
    }

    /**
     * Syncs the parent, if one exists for the supplied product.
     *
     * @param WC_Product $product
     * @return void
     */
    protected function maybeSyncParentProduct(WC_Product $product) : void
    {
        if ($parentId = $product->get_parent_id()) {
            $parentProduct = wc_get_product($parentId);
            if (is_callable([$parentProduct, 'sync'])) {
                $parentProduct->sync($parentProduct);
            }
        }
    }

    /**
     * Unhooks the WooCommerce {@see WC_Post_Data::deferred_product_sync} action, because if we save a variant, WooCommerce
     * then queues up the parent product for a deferred sync. This causes the parent product to be saved locally and in
     * the platform. Saving the parent in the platform causes the variants' updatedAt values to change. This results in
     * a remote change being detected for the variants, which causes us to save them again... ultimately ending in an
     * infinite loop.
     *
     * @throws Exception
     * @see WC_Product::maybe_defer_product_sync()
     * @see WC_Post_Data::do_deferred_product_sync()
     */
    protected function unhookDeferredProductSync() : void
    {
        if (! class_exists('WC_Post_Data')) {
            return;
        }

        Register::action()
            ->setGroup('shutdown')
            ->setHandler([WC_Post_Data::class, 'do_deferred_product_sync'])
            ->setPriority(10)
            ->deregister();
    }

    /**
     * Makes a core {@see Product} object from the supplied local product ID.
     *
     * @param ProductBase $remoteProduct
     * @param int $localId
     * @return Product
     * @throws AdapterException
     * @throws MissingProductLocalIdForParentException
     * @throws WordPressRepositoryException
     */
    protected function makeCoreProduct(ProductBase $remoteProduct, int $localId) : Product
    {
        if ($remoteProduct->assets) {
            // pre-flight check to ensure all remote assets exist in the local database as attachments
            // this needs to be called prior to the adapter
            $this->attachmentsService->handle($remoteProduct->assets, $localId);
        }

        return $this->productBaseAdapter
            ->convertFromSource($remoteProduct)
            ->setId($localId); // set the local ID, as the adapter won't have set it; this ensure we end up updating the _existing_ product
    }

    /**
     * Set core Product properties.
     *
     * This method is responsible for setting properties not supported by the Commerce Platform.
     *
     * @param Product $coreProduct
     * @param ProductBase $remoteProduct
     * @param WC_Product $wcProduct
     * @return Product
     */
    public function setCoreProductProperties(Product $coreProduct, ProductBase $remoteProduct, WC_Product $wcProduct) : Product
    {
        /*
         * The status set in `makeCoreProduct` is unaware of the local post status, so it needs to be overwritten here,
         * to account for logic that determines the correct status based on the local post status.
         */
        $coreProduct->setStatus($this->productPostStatusAdapter->convertToSource($remoteProduct->active, $wcProduct->get_status()));

        $coreProduct->setShortDescription($wcProduct->get_short_description());

        /** @var string $password type hint here b/c the WC return type is incorrect, the password will be a string. */
        $password = $wcProduct->get_post_password();
        $coreProduct->setPassword($password);

        return $coreProduct;
    }
}
