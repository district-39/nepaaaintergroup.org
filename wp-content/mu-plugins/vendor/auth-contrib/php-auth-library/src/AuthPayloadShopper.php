<?php

namespace GoDaddy\Auth;

abstract class AuthPayloadShopper extends AuthPayload
{
    abstract public function getShopper(): ShopperInfo;
}
