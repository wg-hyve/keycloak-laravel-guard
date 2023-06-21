<?php

declare(strict_types=1);

namespace KeycloakGuard\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\KeycloakGuard;
use KeycloakGuard\KeycloakGuardServiceProvider;
use KeycloakGuard\Tests\Models\User;
use KeycloakGuard\Tests\Traits\HasPayload;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use HasPayload;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->setBasePath(__DIR__);
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('keycloak.token_principal_attribute', 'azp');
        $app['config']->set('keycloak.realm_public_key', $this->load('keys/public_no_wrap.key'));
        $app['config']->set('keycloak.allowed_resources', 'client-role-test');
        $app['config']->set('keycloak.service_role', 'client-role-test');
        $app['config']->set('keycloak.ignore_resources_validation', true);

        $app['config']->set('auth.guards.api', [
            'driver' => 'keycloak',
            'provider' => 'users'
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [KeycloakGuardServiceProvider::class,];
    }

    protected function withKeycloakToken(): static
    {
        $this->withToken($this->load('tokens/access_token'));

        return $this;
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    protected function getGuard(): KeycloakGuard
    {
        $req = new Request();
        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token')));

        return new KeycloakGuard(new User(), $req);
    }
}