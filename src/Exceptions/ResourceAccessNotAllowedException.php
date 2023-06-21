<?php
namespace KeycloakGuard\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Sinemah\JsonApi\Error\Error;
use Sinemah\JsonApi\Error\Laravel\Exceptions\StatusUnavailableException;
use Sinemah\JsonApi\Error\Laravel\Responses\Laravel;

class ResourceAccessNotAllowedException extends Exception
{
    protected $message = 'The decoded JWT token has not a valid `resource_access` allowed by API.';

    /**
     * @throws StatusUnavailableException
     */
    public function render(): JsonResponse
    {
        return Laravel::response()
            ->add(Error::fromArray(['status' => 401, 'code' => 'e-token-10', 'title' => $this->message]))
            ->json();
    }
}