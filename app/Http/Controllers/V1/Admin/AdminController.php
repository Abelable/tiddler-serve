<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    protected $guard = 'admin';
    protected $except = ['login'];

    public function login()
    {

    }

    public function list()
    {}

    public function add()
    {}
}
