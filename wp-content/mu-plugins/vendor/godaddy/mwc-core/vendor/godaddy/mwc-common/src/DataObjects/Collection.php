<?php

namespace GoDaddy\WordPress\MWC\Common\DataObjects;

use GoDaddy\WordPress\MWC\Common\Contracts\CanConvertToArrayContract;

/**
 * @template T of CanConvertToArrayContract|mixed
 */
class Collection
{
    /**
     * @var T[]
     */
    protected array $items = [];

    /**
     * @param T[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return T[]
     */
    public function all() : array
    {
        return $this->items;
    }

    /**
     * @return array<int, mixed>
     */
    public function toArray() : array
    {
        $result = [];
        foreach ($this->items as $item) {
            if ($item instanceof CanConvertToArrayContract) {
                $result[] = $item->toArray();
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }
}
