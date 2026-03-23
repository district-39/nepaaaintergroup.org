<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth;

abstract class AuthPayloadPass extends AuthPayload
{
    abstract public function getPassInfo(): PassInfo;
}
