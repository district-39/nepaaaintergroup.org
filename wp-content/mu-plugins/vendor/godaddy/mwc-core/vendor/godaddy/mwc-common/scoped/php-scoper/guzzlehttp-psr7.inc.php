<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor/guzzlehttp/psr7')
            ->name(['*.php', 'LICENSE']),
    ],
    'patchers' => [],
];
