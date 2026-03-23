<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\DataStores;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\CurrencyAmountAdapter;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\DataStores\Contracts\CommerceProductDataStoreContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\DataStores\Traits\HasProductPlatformDataStoreCrudTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Helpers\MapAssetsHelper;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs\UpdateVariationNamesJob;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductAssociation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\BatchListProductsByLocalIdService;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\DataSources\Adapters\SimpleMoneyAdapter;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;
use WC_Product;
use WC_Product_Variable_Data_Store_CPT;

/**
 * Commerce Catalog products data store for variable products (products that contain variants).
 *
 * A WooCommerce data store for variable products to replace the default data store to enable read and write operations with the Commerce API.
 */
class VariableProductDataStore extends WC_Product_Variable_Data_Store_CPT implements CommerceProductDataStoreContract
{
    public const META_LOOKUP_TABLE = 'wc_product_meta_lookup';

    use HasProductPlatformDataStoreCrudTrait;

    protected BatchListProductsByLocalIdService $batchListProductsByLocalIdService;

    /**
     * Constructs the data store.
     *
     * @param ProductsServiceContract $productsService
     * @param MapAssetsHelper $mapAssetsHelper
     * @param BatchListProductsByLocalIdService $batchListProductsByLocalIdService
     */
    public function __construct(ProductsServiceContract $productsService, MapAssetsHelper $mapAssetsHelper, BatchListProductsByLocalIdService $batchListProductsByLocalIdService)
    {
        $this->productsService = $productsService;
        $this->mapAssetsHelper = $mapAssetsHelper;
        $this->batchListProductsByLocalIdService = $batchListProductsByLocalIdService;
    }

    /**
     * Loads variation child IDs.
     *
     * @param WC_Product $product Product object.
     * @param bool       $force_read True to bypass the transient.
     *
     * @return array<string, int[]|string>
     */
    public function read_children(&$product, $force_read = false)
    {
        $children = parent::read_children($product, $force_read);
        $childrenIds = TypeHelper::arrayOfIntegers($children['all'] ?? []);

        if (! empty($childrenIds)) {
            // pre-warm the cache for these products
            $this->batchListProductsByLocalIdService->batchListByLocalIds($childrenIds);
        }

        // @phpstan-ignore return.type
        return $children;
    }

    /**
     * Gets certain data for the lookup table from the API directly.
     *
     * This is primarily overridden, so we can modify the "price" data for
     * variable products. We fetch the prices of all variants from the API
     * instead of the local DB.
     *
     * @param int $id ID of object to update.
     * @param string $table Lookup table name.
     * @return array<string, mixed>
     */
    protected function get_data_for_lookup_table($id, $table)
    {
        $filter = $this->getFilterForVariationPrices();

        if (self::META_LOOKUP_TABLE === $table) {
            try {
                // override the call to `get_post_meta( $id, '_price', false );`
                $filter->execute();
            } catch (Exception $e) {
                // do nothing.
            }
        }

        $data = parent::get_data_for_lookup_table($id, $table);

        if (self::META_LOOKUP_TABLE === $table) {
            try {
                $filter->deregister();
            } catch (Exception $e) {
                // do nothing.
            }
        }

        // @phpstan-ignore return.type
        return $data;
    }

    /**
     * Gets the filter to override the call to `get_post_meta( $id, '_price', false );`.
     *
     * @return RegisterFilter
     */
    protected function getFilterForVariationPrices() : RegisterFilter
    {
        return Register::filter()
            ->setGroup('get_post_metadata')
            ->setHandler([$this, 'filterPriceMetaForVariations'])
            ->setPriority(10)
            ->setArgumentsCount(5);
    }

    /**
     * Filters the call to get variation price meta so it's read from the API
     * instead of the local DB.
     *
     * @param null|mixed $value
     * @param int|mixed $objectId
     * @param string|mixed $metaKey
     * @param bool|mixed $single
     * @param string|mixed $metaType
     * @return mixed
     */
    public function filterPriceMetaForVariations($value, $objectId, $metaKey, $single, $metaType)
    {
        // we only want to filter _price metadata when NOT retrieving a single value
        if ('_price' !== $metaKey || false !== $single || ! $objectId) {
            return $value;
        }

        $childrenIds = $this->getLocalProductVariationIds($objectId)['all'] ?? [];
        $childrenIds = TypeHelper::arrayOfIntegers($childrenIds);

        if (empty($childrenIds)) {
            return $value;
        }

        $productAssociations = $this->batchListProductsByLocalIdService->batchListByLocalIds($childrenIds);
        $prices = $this->maybeCompilePricesForRemoteVariations($productAssociations);

        return $prices ?: $value;
    }

    /**
     * Gets local product variation IDs for a given product ID.
     *
     * @param mixed $objectId
     * @return array<string, int[]|string>
     */
    protected function getLocalProductVariationIds($objectId) : array
    {
        $product = wc_get_product($objectId);

        return $product instanceof WC_Product ? $this->read_children($product) : [];
    }

    /**
     * Attempts to compile prices from remote product variations.
     *
     * @param ProductAssociation[] $productAssociations
     * @return array<float>
     */
    protected function maybeCompilePricesForRemoteVariations(array $productAssociations) : array
    {
        $prices = [];

        $wooCommerceCurrency = WooCommerceRepository::getCurrency();

        foreach ($productAssociations as $productAssociation) {
            $remotePrice = $productAssociation->remoteResource->salePrice ?: $productAssociation->remoteResource->price;
            if ($remotePrice) {
                $corePrice = SimpleMoneyAdapter::getNewInstance()->convertFromSimpleMoney($remotePrice);
                $prices[] = (new CurrencyAmountAdapter($corePrice->getAmount(), $wooCommerceCurrency))->convertToSource($corePrice);
            }
        }

        sort($prices, SORT_NUMERIC);

        return $prices;
    }

    /**
     * Syncs variation names with parent product attribute values.
     *
     * @param WC_Product $product
     * @param string $previous_name
     * @param string $new_name
     */
    public function sync_variation_names(&$product, $previous_name = '', $new_name = '') : void
    {
        // Only proceed if we have both old and new names to replace
        if ($previous_name !== $new_name) {
            // Get all variation IDs for batch processing
            $variationIds = TypeHelper::arrayOfIntegers($product->get_children());

            if (! empty($variationIds)) {
                // Dispatch background job to handle variation name updates in batches
                JobQueue::getNewInstance()
                    ->chain([UpdateVariationNamesJob::class])
                    ->dispatch([
                        $variationIds,      // Pass variation IDs instead of parent product ID
                        $previous_name,
                        $new_name,
                    ]);
            }
        }

        $this->callParentSyncVariationNames($product, $previous_name, $new_name);
    }

    /**
     * Calls the parent sync_variation_names method.
     *
     * @param WC_Product $product
     * @param string $previous_name
     * @param string $new_name
     */
    protected function callParentSyncVariationNames(&$product, $previous_name = '', $new_name = '') : void
    {
        parent::sync_variation_names($product, $previous_name, $new_name);
    }
}
