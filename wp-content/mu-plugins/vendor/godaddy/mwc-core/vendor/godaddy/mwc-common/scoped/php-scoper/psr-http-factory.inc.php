<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor/psr/http-factory')
            ->name(['*.php', 'LICENSE']),
    ],
    'patchers' => [],
];
