<?php

namespace GoDaddy\WordPress\MWC\Common\Helpers;

class GraphQLHelper
{
    /**
     * Extracts children from GraphQL edges structure, ignoring the edges and nodes wrapper.
     *
     * This method handles the common GraphQL pattern of:
     * {
     *   "key": {
     *     "edges": [
     *       {
     *         "node": { actual data }
     *       }
     *     ]
     *   }
     * }
     *
     * @param array<string, mixed> $source The source data array
     * @param string $key The key to extract from (e.g., 'references', 'mediaObjects')
     * @return array<mixed> Array of node data with edges/nodes structure removed
     */
    public static function extractGraphQLEdges(array $source, string $key) : array
    {
        $nodes = [];
        $edgesPath = $key.'.edges';
        $edgesData = ArrayHelper::get($source, $edgesPath, []);

        if (is_array($edgesData)) {
            foreach ($edgesData as $edge) {
                $node = ArrayHelper::get($edge, 'node', []);
                if (is_array($node) && ! empty($node)) {
                    $nodes[] = $node;
                }
            }
        }

        return $nodes;
    }
}
