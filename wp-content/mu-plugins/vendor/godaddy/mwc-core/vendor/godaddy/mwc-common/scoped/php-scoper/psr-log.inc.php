<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor/psr/log')
            ->exclude([
                'Psr/Log/Test',
            ])
            ->name(['*.php', 'LICENSE']),
    ],
    'patchers' => [
        static function (string $path, string $prefix, string $content) : string {
            if (str_ends_with(dirname($path), 'scoped/vendor/psr/log/Psr/Log')) {
                return preg_replace(
                    '/@throws \\\\Psr\\\\Log\\\\InvalidArgumentException/',
                    '@throws InvalidArgumentException',
                    $content
                ) ?? $content;
            }

            return $content;
        },
    ],
];
