<?php

namespace GoDaddy\Auth;

interface AuthKeyCacheInterface
{
    /**
     * @param string $keyId
     *
     * @return string|null
     */
    public function get(string $keyId): ?string;

    /**
     * @param string $keyId
     * @param string $key
     */
    public function set(string $keyId, string $key): void;
}
