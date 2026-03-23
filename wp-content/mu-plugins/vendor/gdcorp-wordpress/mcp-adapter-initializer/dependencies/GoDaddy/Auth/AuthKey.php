<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth;

class AuthKey
{
    public $type = '';
    public $id = '';
    public $code = 0;
    public $message = '';
    /** @var AuthKeyData */
    public $data;

    public function __construct()
    {
        $this->data = new AuthKeyData();
    }
}
