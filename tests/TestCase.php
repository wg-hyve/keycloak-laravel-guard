<?php

namespace KeycloakGuard\Tests;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use KeycloakGuard\Tests\Traits\HasPayload;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    use HasPayload;

    protected function getToken(): string
    {
//        $config = array(
//            "private_key_bits" => 1024, // Länge des privaten Schlüssels
//            "private_key_type" => OPENSSL_KEYTYPE_RSA, // Algorithmus des privaten Schlüssels
//        );
//
//// Generieren Sie den privaten Schlüssel
//        $privateKey = openssl_pkey_new($config);
//
//// Extrahieren Sie den privaten Schlüssel aus dem Schlüsselpaar
//        openssl_pkey_export($privateKey, $privateKeyString);
//
//// Speichern Sie den privaten Schlüssel in einer Datei
//        file_put_contents('/Users/sine/Documents/src/keycloak-laravel-guard/private.key', $privateKeyString);
//
//        $privateKey = openssl_pkey_get_private(file_get_contents('/Users/sine/Documents/src/keycloak-laravel-guard/private.key'));
//
//// Extrahieren Sie den öffentlichen Schlüssel aus dem privaten Schlüssel
//        $publicKey = openssl_pkey_get_details($privateKey)['key'];
//
//// Speichern Sie den öffentlichen Schlüssel in einer Datei
//        file_put_contents('/Users/sine/Documents/src/keycloak-laravel-guard/public.key', $publicKey);
//
//        $payload = $this->loadJson('jwt.json');
//
//        unset($payload['exp']);
//
//        $jwt = JWT::encode(
//            $payload,
//            openssl_pkey_get_private($this->load('keys/private.key')),
//            'RS256'
//        );
//
//        file_put_contents(
//            '/Users/sine/Documents/src/keycloak-laravel-guard/jwt.json',
//            $jwt
//        );

//        JWT::decode($this->load('tokens/access_token'), new Key($this->load('keys/public.key'), 'RS256'));
        return $this->load('tokens/access_token');
    }
}