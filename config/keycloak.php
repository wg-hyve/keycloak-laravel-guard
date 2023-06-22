<?php 

return [
    'realm_public_key' => env('KEYCLOAK_REALM_PUBLIC_KEY', null),

    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'sub'),

    'ignore_resources_validation' => filter_var(env('KEYCLOAK_IGNORE_RESOURCE_VALIDATION', false), FILTER_VALIDATE_BOOLEAN),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', null),

    'realm_address' => env('KEYCLOAK_REALM_ADDRESS', null),

    'key_cache_seconds' => env('KEYCLOAK_KEY_CACHE_SECONDS', 86400),

    'leeway' => env('KEYCLOAK_LEEWAY', 0),
];
