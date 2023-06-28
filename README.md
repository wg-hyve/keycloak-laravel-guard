# Keycloak Laravel Implementation
This Keycloak lib does not load users from database.
Keycloak user UUIDs are usually stored in the sub claim.

## Installation
```bash
composer require wg-hyve/keycloak-laravel-guard
```

## Configuration
Add your keycloak to your guards and use it in your routes as middleware. That's it for a normal usage.
### config/auth.php
```php
return [
    'guards' => [
        // ...
        'cloak' => [
            'driver' => 'keycloak',
            'provider' => 'users',
        ],
        // ...
    ],
];
```

### routes/api.php
```php
Route::any('/acme', [AcmeController::class, 'index'])->middleware(['auth:cloak']);
```

## Extended usage
The KeycloakGuard comes with some methods to keep it easy.

### Example usage in requests
```php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::hasScope('can-delete');
    }

    public function rules(): array
    {
        return [];
    }
}
```

### Other Methods

#### getTokenFromRequest
Get raw Token from request
#### token(): ?stdClass
Get encoded Token from request
#### hasRole(array|string $roles): bool
Proofs if the role or one of the roles is in your client or global roles
#### scopes(): array
Delivers all scopes
#### hasScope(string|array $scope): bool
Proofs if the scope or one of the scopes is in your JWT
#### getRoles(): array
Delivers all roles from your client. Client is delivered in azp claim.
#### roles(bool $useGlobal = true): array
Delivers all roles (global & client)

## User
Saving users in a local database is disabled per default. Execute following steps to enable it.
Make sure you read the instructions for the environment variables in your `.env`.

### Configuration
Enable `KEYCLOAK_PROVIDE_LOCAL_USER` in your environment.
```
KEYCLOAK_PROVIDE_LOCAL_USER = true
```
The guard will save user objects from JWTs.

### Migrations
Publish und execute migrations.
```bash
php artisan vendor:publish --tag=keycloak-migrations
```
Make adjustments and migrate.
```bash
php artisan migrate
```

### User Model
Update your user model in `config/auth.php`. You can also extend it and add your own user model.
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \KeycloakGuard\Models\User::class,
    ],
],
```
You are free to use your user model to extend and define custom relations. Make sure it is compatible with the migration above.

## Environment Variables
```
KEYCLOAK_REALM_PUBLIC_KEY
```
Local Public key of your Keycloak instance. You can find it in `https://your-keycloak.dev.com/auth/realms/your_realm/`

```
KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE
```
Should be `azp` or `client_id` depending on your Keycloak configuration

```
KEYCLOAK_IGNORE_RESOURCE_VALIDATION
```
true or false, auto check if role given with ```KEYCLOAK_ALLOWED_RESOURCES``` is in your token

```
KEYCLOAK_ALLOWED_RESOURCES
```
role to auto check

```
KEYCLOAK_REALM_ADDRESS
```
URL to load public key. Usually `https://your-keycloak.dev.com/auth/realms/your_realm/`

```
KEYCLOAK_KEY_CACHE_SECONDS
```

Cache duration for downloaded public key of your realm.

```
KEYCLOAK_LEEWAY
```
You want to set the time offset in seconds if you get `Cannot handle token prior to (time) error`. Use this if your app and Keycloak timings differ. 

```
KEYCLOAK_PROVIDE_LOCAL_USER
```

Enable local user processing. The guard will try to store the user in a local database. Default is false.

```
KEYCLOAK_USER_ID_CLAIM
```

The user UUID in your JWT. Default is sub claim.

```
KEYCLOAK_USER_MAIL_CLAIM
```

The email claim in your JWT. Default is email.

```
KEYCLOAK_USER_FIRSTNAME_CLAIM
```

The firstname claim in your JWT. Default is given_name.

```
KEYCLOAK_USER_LASTNAME_CLAIM
```

The lastname claim in your JWT. Default is family_name.


## Testing

### Commands
```bash
# run tests with testdox
composer test

# run tests with coverage
composer test:coverage
```

### Docker
Test in docker with coverage after you extended to lib. Don't install xdebug locally 🐌

### Create a new source JSON file
Source files are stored in `tests/Data`
Generated tokens are stored in `tests/Data/tokens`

```bash
composer jwt:generate jwt.json access_token
composer jwt:generate jwt_realm_access.json access_token_realm_access
composer jwt:generate jwt.json access_token_has_expire true
```