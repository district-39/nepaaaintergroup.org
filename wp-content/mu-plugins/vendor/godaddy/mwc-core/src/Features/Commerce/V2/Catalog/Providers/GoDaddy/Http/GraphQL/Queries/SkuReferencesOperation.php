<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\GoDaddy\Http\GraphQL\Queries;

use GoDaddy\WordPress\MWC\Common\Http\GraphQL\AbstractGraphQLOperation;

/**
 * Query retrieves all v1 references required for mapping v1 <-> v2 product data.
 */
class SkuReferencesOperation extends AbstractGraphQLOperation
{
    protected $operation = 'query GetSkusDetailsAndReferences(
        $first: Int!
        $after: String
        $referenceValues: [String!]!
        $status: SKUStatusFilter = {in: ["ACTIVE", "ARCHIVED", "DRAFT"]}
    ) {
  skus(
    first: $first
    after: $after
    referenceValue: {
      in: $referenceValues
    },
    status: $status
  ) {
    edges {
      node {
        id
        code
        references {
          edges {
            node {
              id
              origin
              value
            }
          }
        }
        skuGroup {
          id
          name
          label
          references {
            edges {
              node {
                id
                origin
                value
              }
            }
          }
        }
        mediaObjects {
          edges {
            node {
              id
              label
              name
              url
            }
          }
        }
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}';
}
