<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $guard = 'admin';
    protected $only = [];

    public function login()
    {

    }

    public function logout()
    {}
}
