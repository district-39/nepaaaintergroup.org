<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

interface HttpClientInterface
{
    public function get(string $url): string;

    public function post(string $url, array $data): string;
}
