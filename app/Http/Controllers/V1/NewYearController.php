<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Activity\NewYearTaskService;

class NewYearController extends Controller
{
    public function taskList()
    {
        $list = NewYearTaskService::getInstance()->getList();
        return $this->success($list);
    }
}
