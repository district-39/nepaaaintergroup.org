<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor/jean85/pretty-package-versions')
            ->name(['*.php', 'LICENSE']),
    ],
    'exclude-classes' => [
        Composer\InstalledVersions::class,
        Rector\Config\RectorConfig::class,
    ],
    'patchers' => [],
];
