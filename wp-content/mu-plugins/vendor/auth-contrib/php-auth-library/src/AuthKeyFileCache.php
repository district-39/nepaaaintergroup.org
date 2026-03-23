<?php

namespace GoDaddy\Auth;

class AuthKeyFileCache implements AuthKeyCacheInterface
{
    private const MAX_RETRIES = 3;

    private $dir = '';
    private $maxTTL = 0;

    /**
     * Internal cache of key_name (string) => key (string)
     *
     * @var array
     */
    private $keys = [];

    /**
     * @param string $dir    Directory to store all the keys to. Will be created if it does not exist.
     *                       Make sure the directory is only dedicated for caching, ie. it doesn't contain any other non-cache files.
     * @param int    $maxTTL Maximum TTL in seconds for every stored key. Relies on file's modification time.
     *
     * @throws \Exception If the passed directory is not present on the disk and cannot be created.
     */
    public function __construct(string $dir, int $maxTTL = 0)
    {
        if (!is_dir($dir)) {
            error_clear_last();
            if (@mkdir($dir, 0777, true) === false) {
                $error = error_get_last();
                clearstatcache(false, $dir);
                // Race-condition check.
                if (!is_dir($dir)) {
                    throw new \Exception('Could not create key cache directory "%s": %s', $dir, $error['message'] ?? 'unknown error');
                }
            }
        }
        $this->dir    = $dir;
        $this->maxTTL = $maxTTL;
    }

    public function get(string $keyId): ?string
    {
        if (strlen($keyId) === 0) {
            return null;
        }

        if (isset($this->keys[$keyId])) {
            return $this->keys[$keyId];
        }

        $keyFile = $this->dir.'/'.$this->keyFileName($keyId);

        if (!is_file($keyFile)) {
            return null;
        }

        $retries = 0;
        again:
        try {
            error_clear_last();
            $stat = @stat($keyFile);
            if ($stat === false) {
                $error = error_get_last();
                throw new \Exception(sprintf('Could not stat key cache file "%s": %s', $keyFile, $error['message'] ?? 'unknown error'));
            }

            if ($stat['mtime'] && $this->maxTTL && ($stat['mtime'] + $this->maxTTL < time())) {
                // Key expired.
                @unlink($keyFile);
                return null;
            }

            error_clear_last();
            $key = @file_get_contents($keyFile);
            if ($key === false) {
                $error = error_get_last();
                throw new \Exception(sprintf('Could not read key cache file "%s": %s', $keyFile, $error['message'] ?? 'unknown error'));
            }
        } catch (\Exception $e) {
            if ($retries < self::MAX_RETRIES) {
                $retries++;
                clearstatcache(false, $keyFile);
                $this->sleep($retries);
                goto again;
            }
            throw $e;
        }

        if (!strlen($key)) {
            @unlink($keyFile);
            return null;
        }

        $this->keys[$keyId] = $key;

        return $key;
    }

    public function set(string $keyId, string $key): void
    {
        if (strlen($keyId) === 0) {
            return;
        }

        $this->keys[$keyId] = $key;

        $keyFile = $this->dir.'/'.$this->keyFileName($keyId);

        $retries = 0;
        again:
        try {
            error_clear_last();
            if (@file_put_contents($keyFile, $key) === false) {
                $error = error_get_last();
                throw new \Exception(sprintf('Could not write key cache file "%s": %s', $keyFile, $error['message'] ?? 'unknown error'));
            }
        } catch (\Exception $e) {
            if ($retries < self::MAX_RETRIES) {
                $retries++;
                $this->sleep($retries);
                goto again;
            }
            throw $e;
        }
    }

    private function sleep(int $retries): void
    {
        // Sleep for 200ms, 400ms, 900ms, 1.6s etc.
        usleep(pow($retries, 2) * 100000);
    }

    /**
     * @param string $keyId
     *
     * @return string Filename-safe base64-encoded key ID, so we never let unknown input into filesystem.
     */
    private function keyFileName(string $keyId): string
    {
        return strtr(rtrim(base64_encode($keyId), '='), '+/', '-_');
    }
}
