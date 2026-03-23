<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthPayloadSSLCert;

class AuthPayloadSSLCertTest extends AuthTestCase
{
    public function testValidSSLCertToken()
    {
        $cert = $this->getPayload(self::VALID_SSL_TO_JWT_TOKEN);

        $this->assertNotNull($cert);
        $this->assertNotNull($cert->sbj);
        $this->assertEquals("api-shopper.dev.client.int.godaddy.com", $cert->sbj->cn);
        $this->assertEquals("GoDaddy.com, LLC.", $cert->sbj->o);
        $this->assertEquals("IT", $cert->sbj->ou);
    }

    public function testInvalidSSLCertToken()
    {
        $cert = $this->getPayload(self::INVALID_SSL_TO_JWT_TOKEN);
        $this->assertNull($cert);
    }

    public function testMalformedSSLCertToken()
    {
        $cert = $this->getPayload(self::MALFORMED_SSL_TO_JWT_TOKEN);
        $this->assertNull($cert);
    }

    private function getPayload(string $token): ?AuthPayloadSSLCert
    {
        return self::$authManager->getAuthPayloadSSLCert(self::SSO_TEST_HOSTNAME, $token);
    }
}