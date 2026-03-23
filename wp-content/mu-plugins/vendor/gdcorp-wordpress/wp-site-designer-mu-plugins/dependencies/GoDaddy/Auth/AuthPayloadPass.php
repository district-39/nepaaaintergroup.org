<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

abstract class AuthPayloadPass extends AuthPayload
{
    abstract public function getPassInfo(): PassInfo;
}
