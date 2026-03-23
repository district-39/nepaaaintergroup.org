<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Integration;

use GoDaddy\WordPress\MWC\Common\Vendor\GuzzleHttp\Psr7\ServerRequest;
use GoDaddy\WordPress\MWC\Common\Vendor\Psr\Http\Message\ServerRequestInterface;
/**
 * Default implementation for RequestFetcherInterface. Creates a request object
 * from the PHP superglobals.
 */
final class RequestFetcher implements RequestFetcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchRequest(): ?ServerRequestInterface
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || \PHP_SAPI === 'cli') {
            return null;
        }
        return ServerRequest::fromGlobals();
    }
}
