<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\GuzzleHttp\Psr7;

use GoDaddy\WordPress\MWC\Common\Vendor\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that prevents a stream from being seeked.
 */
final class NoSeekStream implements StreamInterface
{
    use StreamDecoratorTrait;
    /** @var StreamInterface */
    private $stream;
    public function seek($offset, $whence = \SEEK_SET): void
    {
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable(): bool
    {
        return \false;
    }
}
