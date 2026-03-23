<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthPayload;
use PHPUnit\Framework\TestCase;

class AuthPayloadTest extends TestCase
{
    public function testLevel0()
    {
        $auth = new AuthPayload();
        $this->assertFalse($auth->isExpired(0));
    }

    public function testLevelGt3()
    {
        $auth = new AuthPayload();
        $this->assertTrue($auth->isExpired(4));
        $this->assertTrue($auth->isExpired(5));
    }

    public function testLevel1()
    {
        $auth      = new AuthPayload();
        $auth->vat = $auth->iat = time() - 9 * 60;
        $auth->per = true;
        $this->assertFalse($auth->isExpired(1));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 24 * 180 + 60;
        $auth->per = true;
        $this->assertFalse($auth->isExpired(1));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 24 * 180 - 60;
        $auth->per = true;
        $this->assertTrue($auth->isExpired(1));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 24 * 180 + 60;
        $auth->vat = time() - 60 * 10 + 60;
        $this->assertFalse($auth->isExpired(1));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 24 * 180 - 60;
        $auth->vat = time() - 60 * 10 - 60;
        $this->assertTrue($auth->isExpired(1));
    }

    public function testLevel2()
    {
        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 30 + 60;
        $auth->vat = time() - 60 * 9;
        $this->assertFalse($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 30 - 60;
        $auth->vat = time() - 60 * 9;
        $this->assertFalse($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 7 + 60;
        $this->assertFalse($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 7 - 60;
        $this->assertTrue($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 7 + 60;
        $this->assertFalse($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->per = true;
        $auth->iat = time() - 60 * 60 * 24 * 7 - 60;
        $this->assertTrue($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 12 + 60;
        $this->assertFalse($auth->isExpired(2));

        $auth      = new AuthPayload();
        $auth->iat = time() - 60 * 60 * 12 - 60;
        $this->assertTrue($auth->isExpired(2));
    }

    public function testLevel3()
    {
        $auth = new AuthPayload();
        $this->assertTrue($auth->isExpired(3));

        $auth->iat = time() - 30 * 60;
        $this->assertTrue($auth->isExpired(3));

        $auth->hbi = time() - 90 * 60;
        $this->assertTrue($auth->isExpired(3));

        $auth->hbi = time() - 30 * 60;
        $this->assertFalse($auth->isExpired(3));
    }
}
