<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Tests\Traits\HasPayload;

class RoutingTest extends TestCase
{
    use HasPayload;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_authentication_to_a_private_endpoint_with_token(): void
    {
        $response = $this->withKeycloakToken()->json('GET', '/acme/foo');

        $response->assertOk();

        $this->assertEquals(['client-role-test'], Auth::roles());
        $this->assertEquals(['read-test', 'write-test'], Auth::scopes());
    }

    public function test_requesting_a_non_protected_endpoint_without_token(): void
    {
        $response = $this->json('GET', '/acme/bar');

        $response->assertOk();
    }

    public function test_authentication_fails_with_expired_token(): void
    {
        $response = $this->withExpiredKeycloakToken()->json('GET', '/acme/foo');

        $response->assertStatus(401);
    }
}
