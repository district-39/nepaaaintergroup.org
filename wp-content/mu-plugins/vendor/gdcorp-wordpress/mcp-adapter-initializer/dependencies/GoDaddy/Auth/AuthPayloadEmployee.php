<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth;

class AuthPayloadEmployee extends AuthPayload
{
    public $accountName = '';
    public $firstname = '';
    public $lastname = '';
    /** @var string[] */
    public $groups = [];
}
