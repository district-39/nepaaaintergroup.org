<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth;

class AuthPayloadSSLCert extends AuthPayload
{
    /** @var AuthCertSubject */
    public $sbj;

    public function __construct()
    {
        $this->sbj = new AuthCertSubject();
    }
}
