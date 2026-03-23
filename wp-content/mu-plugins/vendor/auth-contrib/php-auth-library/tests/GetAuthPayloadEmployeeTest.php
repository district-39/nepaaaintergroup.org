<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthPayloadEmployee;

class GetAuthPayloadEmployeeTest extends AuthTestCase
{
    public function testGivenValidJomaxTokenShouldReturnBasicJomaxPayload()
    {
        $token = $this->getPayload(AuthPayloadEmployeeTest::VALID_JOMAX_TOKEN);
        $this->assertJomaxTokenValid($token);
    }

    public function testGivenMalformedTokenShouldReturnNull()
    {
        $token = $this->getPayload(AuthPayloadEmployeeTest::MALFORMED_TOKEN);
        $this->assertNull($token);
    }

    private function getPayload(string $token): ?AuthPayloadEmployee
    {
        return self::$authManager->getAuthPayloadEmployee(self::SSO_TEST_HOSTNAME, $token, self::EXPIRY_IMPACT_LEVEL_NONE);
    }
}
