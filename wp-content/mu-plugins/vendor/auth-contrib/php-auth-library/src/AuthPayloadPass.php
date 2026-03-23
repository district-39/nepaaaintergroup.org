<?php

namespace GoDaddy\Auth;

abstract class AuthPayloadPass extends AuthPayload
{
    abstract public function getPassInfo(): PassInfo;
}
