<?php

namespace KeycloakGuard\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Sinemah\JsonApi\Error\Error;
use Sinemah\JsonApi\Error\Laravel\Exceptions\StatusUnavailableException;
use Sinemah\JsonApi\Error\Laravel\Responses\Laravel;

class InvalidTokenException extends Exception
{
    protected $message = 'Unauthorized';

    /**
     * @throws StatusUnavailableException
     */
    public function render(): JsonResponse
    {
        return Laravel::response()
            ->add(Error::fromArray(['status' => 401, 'code' => 'e-token-00', 'title' => $this->message]))
            ->json();
    }
}
