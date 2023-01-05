<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\AliOssServe;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $only = ['getOssConfig'];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }

    public function wxOrderNotify()
    {
        $data = Pay::wechat()->verify()->toArray();
        Log::info('wx_pay_notify', $data);
    }
}
