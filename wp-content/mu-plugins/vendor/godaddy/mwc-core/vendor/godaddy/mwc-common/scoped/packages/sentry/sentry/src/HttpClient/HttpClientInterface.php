<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\HttpClient;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Options;
interface HttpClientInterface
{
    public function sendRequest(Request $request, Options $options): Response;
}
