<?php 

return [
    'realm_public_key' => env('KEYCLOAK_REALM_PUBLIC_KEY', null),

    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'sub'),

    'ignore_resources_validation' => filter_var(env('KEYCLOAK_IGNORE_RESOURCE_VALIDATION', false), FILTER_VALIDATE_BOOLEAN),

    'append_decoded_token' => env('KEYCLOAK_APPEND_DECODED_TOKEN', false),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', null),

    'service_role' => env('KEYCLOAK_SERVICE_ROLE', ''),

    'realm_address' => env('KEYCLOAK_REALM_ADDRESS', null),

    'key_cache_seconds' => env('KEYCLOAK_KEY_CACHE_SECONDS', 86400),

    'auth_url' => env('KEYCLOAK_AUTH_URL', null),

    'client_id' => env('KEYCLOAK_CLIENT_ID', null),

    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', null),

    'scope' => env('KEYCLOAK_SCOPE', null),

    'grant_type' => env('KEYCLOAK_GRANT_TYPE', null),

    'leeway' => env('KEYCLOAK_LEEWAY', 0),
];
