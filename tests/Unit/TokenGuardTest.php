<?php

namespace Tests\Unit;

use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Tests\Traits\HasPayload;

class TokenGuardTest extends TestCase
{
    use HasPayload;

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_load_bearer_token(): void
    {
        $token = $this
            ->getGuard()
            ->getTokenForRequest();

        $this->assertIsString($token);

        $this->assertEquals($this->load('tokens/access_token'), $token);
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_not_load_bearer_token(): void
    {
        $token = $this
            ->getGuard(null)
            ->getTokenForRequest();

        $this->assertNull($token);
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_loads_roles_from_bearer_token(): void
    {
        $guard = $this->getGuard();

        $this->assertEquals(['client-role-test'], $guard->getRoles());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_loads_client_roles_from_bearer_token_with_global_roles(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertEquals(['client-role-test'], $guard->getRoles());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_loads_all_roles_from_bearer_token_with_global_roles(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertEquals(['client-role-realm-test', 'client-role-test'], $guard->roles());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_role_from_bearer_token_as_string(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertTrue($guard->hasRole('client-role-test'));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_role_from_bearer_token_as_array(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertTrue($guard->hasRole(['client-role-test']));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_one_roles_in_bearer_token_from_array(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertTrue($guard->hasRole(['client-role-realm-test', 'client-role-test-nope']));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_role_from_bearer_token_as_string(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertFalse($guard->hasRole('client-role-test-nope'));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_role_from_bearer_token_as_array(): void
    {
        $guard = $this->getGuard('access_token_realm_access');

        $this->assertFalse($guard->hasRole(['client-role-test-nope']));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_loads_scopes_from_bearer_token(): void
    {
        $guard = $this->getGuard();

        $this->assertEquals(['read-test', 'write-test'], $guard->scopes());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_scope_from_bearer_token_as_string(): void
    {
        $guard = $this->getGuard();

        $this->assertTrue($guard->hasScope('write-test'));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_scope_from_bearer_token_as_array(): void
    {
        $guard = $this->getGuard();

        $this->assertTrue($guard->hasScope(['write-test']));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_scopes_from_bearer_token_as_array(): void
    {
        $guard = $this->getGuard();

        $this->assertTrue($guard->hasScope(['write-test', 'read-test']));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_scope_from_bearer_token_as_string(): void
    {
        $guard = $this->getGuard();

        $this->assertFalse($guard->hasScope('write-test-nope'));
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_scope_from_bearer_token_as_array(): void
    {
        $guard = $this->getGuard();

        $this->assertFalse($guard->hasScope(['write-test-nope']));
    }
}
