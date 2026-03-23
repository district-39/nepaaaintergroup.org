<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth;

abstract class AuthPayloadShopper extends AuthPayload
{
    abstract public function getShopper(): ShopperInfo;
}
