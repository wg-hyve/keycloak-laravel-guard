<?php

declare(strict_types=1);

namespace KeycloakGuard\Tests;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\KeycloakGuard;
use KeycloakGuard\KeycloakGuardServiceProvider;
use KeycloakGuard\Tests\Controllers\AcmeController;
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

        $app['config']->set('auth.defaults.guard', 'api');
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('auth.guards.cloak', [
            'driver' => 'keycloak',
            'provider' => 'users'
        ]);

        Http::fake(['keycloak.dev/auth/realms/testing' => Http::response(['public_key' => $this->load('keys/public_no_wrap.key')]),]);
        Http::fake(['keycloak.dev/auth/realms/nope' => Http::response(['public_key' => null]), 404]);
    }

    protected function getPackageProviders($app): array
    {
        Route::any('/acme/foo', [AcmeController::class, 'foo'])->middleware(['auth:cloak']);
        Route::any('/acme/bar', [AcmeController::class, 'bar']);

        return [KeycloakGuardServiceProvider::class,];
    }

    protected function withKeycloakToken(): static
    {
        $this->withToken($this->load('tokens/access_token'));

        return $this;
    }

    protected function withExpiredKeycloakToken(): static
    {
        $this->withToken($this->load('tokens/access_token_has_expire'));

        return $this;
    }

    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    protected function getGuard(?string $tokenName = 'access_token'): KeycloakGuard
    {
        $req = new Request();

        if($tokenName) {
            $req->headers->set('Authorization', sprintf('Bearer %s', $this->load(sprintf('tokens/%s', $tokenName))));
        }

        return new KeycloakGuard(new User(), $req);
    }
}