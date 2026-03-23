<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Metrics\Types;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Metrics\MetricsUnit;
/**
 * @internal
 */
final class DistributionType extends AbstractType
{
    /**
     * @var string
     */
    public const TYPE = 'd';
    /**
     * @var array<array-key, float>
     */
    private $values;
    /**
     * @param int|float $value
     */
    public function __construct(string $key, $value, MetricsUnit $unit, array $tags, int $timestamp)
    {
        parent::__construct($key, $unit, $tags, $timestamp);
        $this->add($value);
    }
    /**
     * @param int|float $value
     */
    public function add($value): void
    {
        $this->values[] = (float) $value;
    }
    public function serialize(): array
    {
        return $this->values;
    }
    public function getType(): string
    {
        return self::TYPE;
    }
}
