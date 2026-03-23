<?php

namespace GoDaddy\Auth;

class AuthPayloadSSLCert extends AuthPayload
{
    /** @var AuthCertSubject */
    public $sbj;

    public function __construct()
    {
        $this->sbj = new AuthCertSubject();
    }
}
