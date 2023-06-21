<?php

namespace Tests\Unit;

use KeycloakGuard\Exceptions\TokenException;
use KeycloakGuard\Tests\TestCase;
use KeycloakGuard\Token;
use stdClass;

class TokenTest extends TestCase
{
    /**
     * @throws TokenException
     */
    public function test_token_can_be_loaded_from_public_key(): void
    {
        $token = Token::decode($this->load('tokens/access_token'), $this->load('keys/public.key'));

        $this->assertInstanceOf(stdClass::class, $token);
    }

    /**
     * @throws TokenException
     */
    public function test_token_can_be_loaded_from_unwrapped_public_key(): void
    {
        $token = Token::decode($this->load('tokens/access_token'), $this->load('keys/public_no_wrap.key'));

        $this->assertInstanceOf(stdClass::class, $token);
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
}
