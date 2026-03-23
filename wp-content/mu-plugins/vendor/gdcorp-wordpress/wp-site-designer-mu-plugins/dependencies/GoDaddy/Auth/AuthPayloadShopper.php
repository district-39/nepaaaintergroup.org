<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

abstract class AuthPayloadShopper extends AuthPayload
{
    abstract public function getShopper(): ShopperInfo;
}
