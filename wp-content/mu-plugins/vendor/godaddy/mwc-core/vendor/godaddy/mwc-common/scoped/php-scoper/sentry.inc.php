<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor/sentry/sentry')
            ->name(['*.php', 'LICENSE']),
    ],
    'exclude-classes' => [
        ExcimerProfiler::class,
        ExcimerLogEntry::class,
        GuzzleHttp\Exception\RequestException::class,
    ],
    'exclude-namespaces' => [
        '\\Monolog\\',
    ],
    'patchers' => [],
];
