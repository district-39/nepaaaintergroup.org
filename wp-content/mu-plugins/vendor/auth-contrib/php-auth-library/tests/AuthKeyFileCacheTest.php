<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthKeyFileCache;
use PHPUnit\Framework\TestCase;

class AuthKeyFileCacheTest extends TestCase
{
    private $dir = '';

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/godaddy-key-cache_tests';
    }

    public function testDeletesExpiredKeys()
    {
        $keyId      = 'expired';
        $keyPath    = $this->dir.'/'.$this->keyFileName($keyId);
        $keyContent = 'content';

        $cache = new AuthKeyFileCache($this->dir, 60);
        file_put_contents($keyPath, $keyContent);
        $this->assertFileExists($keyPath);
        $this->assertTrue(touch($keyPath, time() - 30), "could not touch file");
        $this->assertEquals($keyContent, $cache->get($keyId));

        $this->assertTrue(touch($keyPath, time() - 90), "could not touch file");
        $cache = new AuthKeyFileCache($this->dir, 0);
        $this->assertEquals($keyContent, $cache->get($keyId));
        $cache = new AuthKeyFileCache($this->dir, 60);
        $this->assertNull($cache->get($keyId));
        $this->assertFileDoesNotExist($keyPath);
    }

    public function testWritesKeysToDisk()
    {
        $keyId      = 'key';
        $keyPath    = $this->dir.'/'.$this->keyFileName($keyId);
        $keyContent = 'content';

        $cache = new AuthKeyFileCache($this->dir);
        $cache->set($keyId, $keyContent);

        $this->assertStringEqualsFile($keyPath, $keyContent);
    }

    private function keyFileName($keyId)
    {
        // URL-safe base64_encode.
        return strtr(rtrim(base64_encode($keyId), '='), '+/', '-_');
    }
}
