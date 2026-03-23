<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Jean85\Exception;

interface VersionMissingExceptionInterface extends \Throwable
{
    public static function create(string $packageName): self;
}
