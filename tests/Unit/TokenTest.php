<?php

namespace Tests\Unit;

use KeycloakGuard\Exceptions\TokenException;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Tests\Traits\HasPayload;
use KeycloakGuard\Token;
use stdClass;

class TokenTest extends TestCase
{
    use HasPayload;

    /**
     * @throws TokenException
     */
    public function test_empty_token_returns_null(): void
    {
        $token = Token::decode(null, $this->load('keys/public.key'));

        $this->assertNull($token);
    }

    /**
     * @throws TokenException
     */
    public function test_token_can_be_loaded_from_public_key(): void
    {
        $token = Token::decode($this->load('tokens/access_token'), $this->load('keys/public.key'));

        $this->assertInstanceOf(stdClass::class, $token);
        $this->assertEquals(json_decode($this->load('jwt_no_expire.json')), $token);
    }

    /**
     * @throws TokenException
     */
    public function test_token_can_be_loaded_from_unwrapped_public_key(): void
    {
        $token = Token::decode($this->load('tokens/access_token'), $this->load('keys/public_no_wrap.key'));

        $this->assertInstanceOf(stdClass::class, $token);
        $this->assertEquals(json_decode($this->load('jwt_no_expire.json')), $token);
    }

    /**
     * @throws TokenException
     */
    public function test_token_can_not_be_loaded_without_public_key(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('No public key found.');

        Token::decode($this->load('tokens/access_token'), $this->load('keys/nope.key'));
    }

    /**
     * @throws TokenException
     */
    public function test_load_token_from_server(): void
    {
        $token = Token::decode($this->load('tokens/access_token'), '', 'keycloak.dev/auth/realms/testing');

        $this->assertInstanceOf(stdClass::class, $token);
        $this->assertEquals(json_decode($this->load('jwt_no_expire.json')), $token);
    }

    /**
     * @throws TokenException
     */
    public function test_can_not_load_token_from_server(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('Can\'t get public key from keycloak server.');

        $token = Token::decode($this->load('tokens/access_token'), '', 'keycloak.dev/auth/realms/nope');

        $this->assertInstanceOf(stdClass::class, $token);
    }
}
