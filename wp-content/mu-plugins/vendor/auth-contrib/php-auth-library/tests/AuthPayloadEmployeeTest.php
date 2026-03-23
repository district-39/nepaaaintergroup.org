<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthManager;
use GoDaddy\Auth\AuthPayloadEmployee;

class AuthPayloadEmployeeTest extends AuthTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$authManager = new AuthManager(null, null, "MyApp");
    }

    public function testValidJomaxToken()
    {
        $token = $this->getPayload(self::VALID_JOMAX_TOKEN);

        $this->assertJomaxTokenValid($token);
    }

    public function testMalformedJomaxToken()
    {
        $token = $this->getPayload(self::MALFORMED_TOKEN);
        $this->assertNull($token);
    }

    private function getPayload(string $token): ?AuthPayloadEmployee
    {
        return self::$authManager->getAuthPayloadEmployee(self::SSO_TEST_HOSTNAME, $token, self::EXPIRY_IMPACT_LEVEL_NONE);
    }
}
