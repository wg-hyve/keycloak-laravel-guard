<?php

namespace KeycloakGuard\Models;

use Illuminate\Foundation\Auth\User as Auth;

class User extends Auth
{
    protected $fillable = [
        'uuid',
        'email',
        'name',
        'firstname',
        'lastname',
    ];
}