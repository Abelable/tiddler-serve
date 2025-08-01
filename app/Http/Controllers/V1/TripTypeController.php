<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotScenicService;
use App\Services\LakeTripService;

class TripTypeController extends Controller
{
    protected $only = [];

    public function hotScenicList()
    {
        $list = HotScenicService::getInstance()->getHotScenicList();
        return $this->success($list);
    }

    public function lakeTripList()
    {
        $lakeId = $this->verifyRequiredId('lakeId');
        $list = LakeTripService::getInstance()->getLakeTripList($lakeId);
        return $this->success($list);
    }
}
