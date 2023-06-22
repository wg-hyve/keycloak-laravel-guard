<?php

namespace KeycloakGuard\Tests\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AcmeController extends BaseController
{
    use AuthorizesRequests;

    public function foo(Request $request): string
    {
        return 'auth';
    }

    public function bar(Request $request): string
    {
        return 'no_auth';
    }
}