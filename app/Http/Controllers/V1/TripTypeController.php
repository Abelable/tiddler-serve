<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotScenicService;
use App\Services\LakeCycleMediaService;
use App\Services\LakeCycleService;
use App\Services\LakeTripService;
use App\Utils\Inputs\PageInput;

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

    public function lakeCycleList()
    {
        $routeId = $this->verifyRequiredId('routeId');
        $list = LakeCycleService::getInstance()->getLakeCycleList($routeId);
        return $this->success($list);
    }

    public function lakeCycleMediaList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LakeCycleMediaService::getInstance()->getPage($input);
        return $this->successPaginate($page);
    }
}
