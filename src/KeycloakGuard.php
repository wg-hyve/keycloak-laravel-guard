<?php

namespace KeycloakGuard;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use stdClass;

class KeycloakGuard implements Guard
{
    private array $config;
    private UserProvider $provider;
    private ?stdClass $decodedToken = null;
    private Request $request;

    /**
     * @throws InvalidTokenException
     * @throws ResourceAccessNotAllowedException
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->config = config('keycloak');
        $this->provider = $provider;
        $this->request = $request;

        $this->authenticate();
    }

    /**
     * Decode token, validate and authenticate user
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    private function authenticate(): void
    {
        try {
            $this->decodedToken = Token::decode(
                $this->getTokenFromRequest(),
                $this->config['realm_public_key'] ?? '',
                $this->config['realm_address'] ?? '',
                $this->config['leeway'] ?? 0,
            );
        } catch (Exception $e) {
            throw new InvalidTokenException($e->getMessage());
        }

        if ($this->decodedToken) {
            $this->validate();
        }
    }

    /**
     * Get the token for the current request.
     *
     * @return ?string
     */
    public function getTokenFromRequest(): ?string
    {
        $inputKey = $this->config['input_key'] ?? '';
        $token = $this->request->bearerToken() ?? $this->request->input($inputKey) ?? Arr::get(getallheaders(), 'Authorization');

        if($token) {
            return str_replace('Bearer ', '', $token);
        }

        return null;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }

    /**
     * Determine if the guard has a user instance.
     */
    public function hasUser(): bool
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Set the current user.
     */
    public function setUser(Authenticatable $user): self
    {
        return $this;
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?Authenticatable
    {
        return null;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): ?string
    {
        return $this?->decodedToken?->jti;
    }

    /**
     * Returns full decoded JWT token from authenticated user
     */
    public function token(): ?stdClass
    {
        return $this->decodedToken;
    }

    /**
     * Validate a user's credentials.
     * @throws ResourceAccessNotAllowedException
     */
    public function validate(array $credentials = []): bool
    {
        $this->validateResources();

        return $this->decodedToken !== null;
    }

    /**
     * Validate if authenticated user has a valid resource
     * @throws ResourceAccessNotAllowedException
     */
    private function validateResources(): void
    {
        if ($this->config['ignore_resources_validation']) {
            return;
        }

        $token_resource_access = Arr::get(
            (array) ($this->decodedToken->resource_access->{$this->getClientName()} ?? []),
            'roles',
            []
        );

        $allowed_resources = explode(',', $this->config['allowed_resources']);

        if (count(array_intersect($token_resource_access, $allowed_resources)) == 0) {

            throw new ResourceAccessNotAllowedException(
                sprintf(
                    'The decoded JWT token has not a valid `resource_access` allowed by API. Allowed resources by API: %s',
                    $this->config['allowed_resources']
                )
            );
        }
    }

    public function roles(bool $useGlobal = true): array
    {
        $global_roles = [];
        $client_roles = $this->decodedToken?->resource_access?->{$this->getClientName()}?->roles ?? [];

        if($useGlobal) {
            $global_roles = $this->decodedToken?->realm_access?->roles ?? [];
        }

//        $global_roles = $useGlobal === true ? $this->decodedToken?->realm_access?->roles ?? [] : [];

        return array_unique(
            array_merge(
                $global_roles,
                $client_roles
            )
        );
    }

    /**
     * Check if authenticated user has a especific role into resource
     * @param array|string $roles
     * @return bool
     */
    public function hasRole(array|string $roles): bool
    {
        return count(
                array_intersect(
                    $this->roles(),
                    is_string($roles) ? [$roles] : $roles
                )
            ) > 0;
    }

    public function scopes(): array
    {
        $scopes = $this->decodedToken->scope ?? null;

        if($scopes) {
            return explode(' ', $scopes);
        }

        return [];
    }

    public function hasScope(string|array $scope): bool
    {
        return count(array_intersect(
                         $this->scopes(),
                         is_string($scope) ? [$scope] : $scope
                     )) > 0;
    }

    public function getRoles(): array
    {
        return $this->roles(false);
    }

    private function getClientName(): string|null
    {
        return $this->decodedToken->{$this->config['token_principal_attribute']};
    }
}
