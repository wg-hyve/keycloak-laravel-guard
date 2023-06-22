<?php

namespace KeycloakGuard\Tests\Models;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserProvider
{
    public function retrieveById($identifier)
    {
    }

    public function retrieveByToken($identifier, $token)
    {
    }

    public function updateRememberToken(\Illuminate\Contracts\Auth\Authenticatable $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials)
    {
    }

    public function validateCredentials(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials)
    {
    }
}