<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\Exceptions\TokenException;
use KeycloakGuard\KeycloakGuardServiceProvider;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Tests\Traits\HasPayload;
use KeycloakGuard\Token;
use stdClass;

class TokenGuardTest extends TestCase
{
    use HasPayload;

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_loads_roles_from_bearer_token(): void
    {
        $guard = $this->getGuard();

        $this->assertEquals(['client-role-test'], $guard->getRoles());
    }
}
