<?php

namespace Tests\Unit;

use Illuminate\Support\Arr;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\Models\User;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Tests\Traits\HasPayload;

class TokenGuardTest extends TestCase
{
    use HasPayload;

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_load_bearer_token_from_request(): void
    {
        $token = $this
            ->getGuard()
            ->getTokenFromRequest();

        $this->assertIsString($token);

        $this->assertEquals($this->load('tokens/access_token'), $token);
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_not_load_bearer_token_from_request(): void
    {
        $token = $this
            ->getGuard(null)
            ->getTokenFromRequest();

        $this->assertNull($token);
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_load_jwt_successfully(): void
    {
        $token = $this
            ->getGuard()
            ->token();

        $this->assertNotNull($token);
        $this->assertEquals(json_decode($this->load('jwt_no_expire.json')), $token);
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
    public function test_guard_bearer_token_has_no_scopes(): void
    {
        $guard = $this->getGuard('access_token_no_scopes');

        $this->assertEquals([], $guard->scopes());
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

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_user(): void
    {
        $guard = $this->getGuard();

        $this->assertFalse($guard->hasUser());
        $this->assertNull($guard->user());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_has_no_guest(): void
    {
        $guard = $this->getGuard();

        $this->assertFalse($guard->guest());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_check_is_valid(): void
    {
        $guard = $this->getGuard();

        $this->assertTrue($guard->check());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_token_has_no_id(): void
    {
        $id = $this
            ->getGuard()
            ->id();

        $this->assertNull($id);
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_validate_roles(): void
    {
        app('config')->set('keycloak.ignore_resources_validation', false);

        $guard = $this->getGuard();

        $this->assertTrue($guard->validate());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_can_not_validate_roles(): void
    {
        app('config')->set('keycloak.ignore_resources_validation', false);
        app('config')->set('keycloak.allowed_resources', 'nope');

        $this->expectException(ResourceAccessNotAllowedException::class);
        $this->expectExceptionMessage('The decoded JWT token has not a valid `resource_access` allowed by API. Allowed resources by API: nope');

        $guard = $this->getGuard();

        $guard->validate();
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_ignores_validation(): void
    {
        app('config')->set('keycloak.allowed_resources', 'nope');

        $guard = $this->getGuard();

        $this->assertTrue($guard->validate());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_token_is_expired(): void
    {
        $this->expectException(InvalidTokenException::Class);
        $this->expectExceptionMessage('Expired token');

        $this->getGuard('access_token_has_expire');
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_guard_ignores_user_claims(): void
    {
        $this->assertNull($this->getGuard('access_token_with_user')->user());
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public function test_load_user_from_token(): void
    {
        app('config')->set('keycloak.provide_user', true);

        $user = $this->getGuard('access_token_with_user')->user();
        $jwt = $this->loadJson('jwt_with_user.json');

        $this->assertNotNull($jwt['sub']);
        $this->assertNotNull($jwt['email']);
        $this->assertNotNull($jwt['given_name']);
        $this->assertNotNull($jwt['family_name']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals($jwt['sub'], $user->uuid);
        $this->assertEquals($jwt['email'], $user->email);
        $this->assertEquals($jwt['given_name'], $user->firstname);
        $this->assertEquals($jwt['family_name'], $user->lastname);
    }
}
