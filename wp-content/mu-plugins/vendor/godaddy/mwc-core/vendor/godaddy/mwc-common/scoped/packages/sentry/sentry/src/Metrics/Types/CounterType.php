<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Metrics\Types;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Metrics\MetricsUnit;
/**
 * @internal
 */
final class CounterType extends AbstractType
{
    /**
     * @var string
     */
    public const TYPE = 'c';
    /**
     * @var int|float
     */
    private $value;
    /**
     * @param int|float $value
     */
    public function __construct(string $key, $value, MetricsUnit $unit, array $tags, int $timestamp)
    {
        parent::__construct($key, $unit, $tags, $timestamp);
        $this->value = (float) $value;
    }
    /**
     * @param int|float $value
     */
    public function add($value): void
    {
        $this->value += (float) $value;
    }
    public function serialize(): array
    {
        return [$this->value];
    }
    public function getType(): string
    {
        return self::TYPE;
    }
}
