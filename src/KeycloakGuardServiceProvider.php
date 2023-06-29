<?php
namespace KeycloakGuard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class KeycloakGuardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMigrations();
        $this->registerPublishing();

        $this->mergeConfigFrom(__DIR__ . '/../config/keycloak.php', 'keycloak');
    }

    public function register()
    {
        Auth::extend('keycloak', function ($app, $name, array $config) {
            return new KeycloakGuard(Auth::createUserProvider($config['provider']), $app->request);
        });
    }


    protected function registerMigrations()
    {
        if ($this->app->runningInConsole() && Keycloak::$ignoreMigration === false) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes(
                [
                    __DIR__ . '/../config/keycloak.php' => app()->configPath('keycloak.php'),
                ],
                'keycloak-config'
            );

            $this->publishes(
                [
                    __DIR__.'/../database/migrations' => database_path('migrations'),
                ],
                'keycloak-migrations'
            );
        }
    }
}