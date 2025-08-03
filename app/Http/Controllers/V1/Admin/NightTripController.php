<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\NightTripService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\NightTripInput;
use App\Utils\Inputs\PageInput;

class NightTripController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = NightTripService::getInstance()->getNightTripPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $nightTrip = NightTripService::getInstance()->getNightTrip($id);
        if (is_null($nightTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前夜游景点不存在');
        }
        return $this->success($nightTrip);
    }

    public function add()
    {
        /** @var NightTripInput $input */
        $input = NightTripInput::new();

        NightTripService::getInstance()->createNightTrip($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var NightTripInput $input */
        $input = NightTripInput::new();
        $id = $this->verifyRequiredId('id');

        $nightTrip = NightTripService::getInstance()->getNightTrip($id);
        if (is_null($nightTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前夜游景点不存在');
        }

        NightTripService::getInstance()->updateNightTrip($nightTrip, $input);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $nightTrip = NightTripService::getInstance()->getNightTrip($id);
        if (is_null($nightTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前夜游景点不存在');
        }

        $nightTrip->sort = $sort;
        $nightTrip->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $nightTrip = NightTripService::getInstance()->getNightTrip($id);
        if (is_null($nightTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前夜游景点不存在');
        }
        $nightTrip->delete();
        return $this->success();
    }
}
