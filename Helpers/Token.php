<?php

declare(strict_types=1);

namespace Helpers;

require_once './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use KeycloakGuard\Tests\Traits\HasPayload;

class Token
{
    use HasPayload;

    protected string $pathStore;

    private function __construct(protected string $source, protected string $target, protected bool $hasExpire)
    {
        $this->pathStore = realpath(sprintf('%s/../tests/Data/tokens', __DIR__));
    }

    public static function init(string $source, string $target, mixed $hasExpire): Token
    {
        return new self($source, $target, filter_var($hasExpire, FILTER_VALIDATE_BOOLEAN));
    }

    public function generate(): void
    {
        $payload = $this->loadJson($this->source);

        var_dump($this->hasExpire);

        if($this->hasExpire === false) {
            unset($payload['exp']);
        }
        $jwt = JWT::encode(
            $payload,
            openssl_pkey_get_private($this->load('keys/private.key')),
            'RS256'
        );

        file_put_contents(
            sprintf('%s/%s', $this->pathStore, $this->target),
            $jwt
        );
    }
}

$source = $argv[1];
$target = $argv[2];
$hasExpire = $argv[3] ?? false;

Token::init($source, $target, $hasExpire)->generate();