<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    protected $only = [];

    public function pop()
    {
        $pop = BannerService::getInstance()->getPop();
        return $this->success($pop);
    }

    public function list()
    {
        $position = $this->verifyRequiredInteger('position');
        $list = Cache::remember('banner_list_' . $position, 10080, function () use ($position) {
            return BannerService::getInstance()->getBannerList($position);
        });
        return $this->success($list);
    }
}
