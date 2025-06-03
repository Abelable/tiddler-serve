<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Utils\AliOssServe;

class CommonController extends Controller
{
    protected $guard = 'Admin';

    protected $only = [];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }
}
