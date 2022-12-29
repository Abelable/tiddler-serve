<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\AliOssServe;

class CommonController extends Controller
{
    protected $only = ['getOssConfig'];

    public function getOssConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }
}
