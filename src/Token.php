<?php
namespace KeycloakGuard;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use KeycloakGuard\Exceptions\TokenException;
use stdClass;

class Token
{
    /**
     * Decode a JWT token
     * @throws TokenException
     */
    public static function decode(?string $token = null, string $publicKey = '', string $keyCloakServer = '', int $leeway = 0): ?stdClass
    {
        JWT::$leeway = $leeway;

        return $token ? JWT::decode(
            $token,
            new Key(self::loadPublicKey($publicKey, $keyCloakServer), 'RS256')
        ) : null;
    }

    /**
     * @param string $publicKey
     * @param string $keyCloakServer
     * @return string
     * @throws TokenException
     */
    private static function loadPublicKey(string $publicKey = '', string $keyCloakServer = ''): string
    {
        return match (true) {
            strlen($keyCloakServer) > 0 => self::buildPublicKey(self::getPublicFromKeyCloak($keyCloakServer)),
            strlen($publicKey) > 0 => self::buildPublicKey($publicKey),
            default => throw new TokenException('No public key found.'),
        };
    }

    /**
     * Build a valid public key from a string
     */
    private static function buildPublicKey(string $key): string
    {
        if(str_starts_with($key, '-----BEGIN PUBLIC KEY-----')) {
            return $key;
        }

        return "-----BEGIN PUBLIC KEY-----\n" . wordwrap($key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
    }

    private static function getPublicFromKeyCloak(string $keyCloakServer): mixed
    {
        return Cache::remember('keycloak.public_key', config('keycloak.key_cache_seconds'), function () use($keyCloakServer) {
            $response = Http::get($keyCloakServer);

            if (!$response->successful()) {
                throw new TokenException('Can\'t get public key from keycloak server.');
            }

            return $response->json('public_key');
        });
    }
}
