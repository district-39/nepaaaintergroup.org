<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Adapters\Traits;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\Categories\Category;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters\ExternalIdsAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingCategoryRemoteIdException;

/**
 * Trait used by adapters that can convert a Commerce Category response to a {@see Category} object.
 */
trait CanConvertCategoryResponseTrait
{
    use CanParseNullableStringPropertyTrait;
    use CanConvertDateTimeFromTimestampTrait;

    /**
     * Converts a Commerce Category response to a {@see Category} object.
     *
     * @param array<string, mixed> $responseData
     *
     * @return Category
     * @throws MissingCategoryRemoteIdException
     */
    public function convertCategoryResponse(array $responseData) : Category
    {
        /** @var array<array<string, string>> $externalIdsData */
        $externalIdsData = TypeHelper::array(ArrayHelper::get($responseData, 'externalIds'), []);

        return new Category([
            'altId' => $this->parseNullableStringProperty($responseData, 'altId'),
            // @TODO assets will be converted when we start reading assets from the service {agibson 2023-09-06}
            'categoryId'  => $this->getValidCategoryId($responseData, 'categoryId'),
            'createdAt'   => $this->convertDateTimeFromTimestamp($responseData, 'createdAt'),
            'depth'       => TypeHelper::int(ArrayHelper::get($responseData, 'depth'), 0),
            'description' => $this->parseNullableStringProperty($responseData, 'description'),
            'externalIds' => ExternalIdsAdapter::getNewInstance()->convertToSourceFromArray($externalIdsData),
            'name'        => TypeHelper::string(ArrayHelper::get($responseData, 'name'), ''),
            'parentId'    => $this->parseNullableStringProperty($responseData, 'parentId'),
            'sequence'    => TypeHelper::int(ArrayHelper::get($responseData, 'sequence'), 1),
            'updatedAt'   => $this->convertDateTimeFromTimestamp($responseData, 'updatedAt'),
        ]);
    }

    /**
     * Get the valid category ID from the response data.
     *
     * @param array<string, mixed> $responseData
     * @param string $key
     * @return string|null
     * @throws MissingCategoryRemoteIdException
     */
    protected function getValidCategoryId(array $responseData, string $key) : ?string
    {
        if ($value = TypeHelper::string(ArrayHelper::get($responseData, $key), '')) {
            return $value;
        }

        throw new MissingCategoryRemoteIdException('Category ID is missing from response.');
    }
}
