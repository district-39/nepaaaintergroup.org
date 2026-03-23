<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthPayloadSSLCert extends AuthPayload
{
    /** @var AuthCertSubject */
    public $sbj;

    public function __construct()
    {
        $this->sbj = new AuthCertSubject();
    }
}
