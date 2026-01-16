<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Activity\NewYearTaskService;
use Illuminate\Support\Facades\Cache;

class NewYearController extends Controller
{
    public function taskList()
    {
        $list = Cache::remember('new_year_task_list', 10080, function () {
            return NewYearTaskService::getInstance()->getList();
        });
        return $this->success($list);
    }

    public function goodsList()
    {
        $list = Cache::remember('new_year_goods_list', 10080, function () {
            return NewYearGoodsService::getInstance()->getList();
        });
        return $this->success($list);
    }
}
