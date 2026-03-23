<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use GoDaddy\WordPress\MWC\Common\Exceptions\AdapterException;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\TermsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\Categories\Category;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters\CategoryWpTermAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;

class UpdateLocalCategoryService
{
    protected CategoryMapRepository $categoryMapRepository;

    protected CategoryWpTermAdapter $categoryWpTermAdapter;

    public function __construct(CategoryMapRepository $categoryMapRepository, CategoryWpTermAdapter $categoryWpTermAdapter)
    {
        $this->categoryMapRepository = $categoryMapRepository;
        $this->categoryWpTermAdapter = $categoryWpTermAdapter;
    }

    /**
     * Updates local database with the given {@see Category} data.
     *
     * @param Category $category
     * @param int $localId
     * @throws AdapterException
     */
    public function update(Category $category, int $localId) : void
    {
        $wpTerm = $this->categoryWpTermAdapter->convertToSource($category);

        $data = [
            'name'        => $wpTerm->name,
            'description' => TypeHelper::string($wpTerm->description, ''),
            'parent'      => $wpTerm->parent,
        ];

        CatalogIntegration::withoutWrites(fn () => TermsRepository::updateTerm($localId, CatalogIntegration::PRODUCT_CATEGORY_TAXONOMY, $data));
    }
}
