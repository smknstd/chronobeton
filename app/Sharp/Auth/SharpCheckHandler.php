<?php

namespace App\Sharp\Auth;

use Code16\Sharp\Auth\SharpAuthenticationCheckHandler;
use Illuminate\Contracts\Auth\Authenticatable;

class SharpCheckHandler implements SharpAuthenticationCheckHandler
{
    public function check(Authenticatable $user): bool
    {
        return true;
    }
}
