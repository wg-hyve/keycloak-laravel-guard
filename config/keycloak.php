<?php 

return [
    'realm_public_key' => env('KEYCLOAK_REALM_PUBLIC_KEY', null),

    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'sub'),

    'ignore_resources_validation' => filter_var(env('KEYCLOAK_IGNORE_RESOURCE_VALIDATION', false), FILTER_VALIDATE_BOOLEAN),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', null),

    'realm_address' => env('KEYCLOAK_REALM_ADDRESS', null),

    'key_cache_seconds' => env('KEYCLOAK_KEY_CACHE_SECONDS', 86400),

    'leeway' => env('KEYCLOAK_LEEWAY', 0),

    'provide_user' => filter_var(env('KEYCLOAK_PROVIDE_LOCAL_USER', false), FILTER_VALIDATE_BOOLEAN),

    'user_id_claim' => env('KEYCLOAK_USER_ID_CLAIM', 'sub'),

    'user_mail_claim' => env('KEYCLOAK_USER_MAIL_CLAIM', 'email'),

    'user_firstname_claim' => env('KEYCLOAK_USER_FIRSTNAME_CLAIM', 'given_name'),

    'user_lastname_claim' => env('KEYCLOAK_USER_LASTNAME_CLAIM', 'family_name'),
  
    'debug' => env('KEYCLOAK_DEBUG', false),
];