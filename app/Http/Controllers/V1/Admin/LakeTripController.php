<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\LakeTripService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\LakeTripInput;
use App\Utils\Inputs\PageInput;

class LakeTripController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LakeTripService::getInstance()->getLakeTripPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $lakeTrip = LakeTripService::getInstance()->getLakeTrip($id);
        if (is_null($lakeTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }
        return $this->success($lakeTrip);
    }

    public function add()
    {
        /** @var LakeTripInput $input */
        $input = LakeTripInput::new();

        LakeTripService::getInstance()->createLakeTrip($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var LakeTripInput $input */
        $input = LakeTripInput::new();
        $id = $this->verifyRequiredId('id');

        $lakeTrip = LakeTripService::getInstance()->getLakeTrip($id);
        if (is_null($lakeTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }

        LakeTripService::getInstance()->updateLakeTrip($lakeTrip, $input);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $lakeTrip = LakeTripService::getInstance()->getLakeTrip($id);
        if (is_null($lakeTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }

        $lakeTrip->sort = $sort;
        $lakeTrip->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $lakeTrip = LakeTripService::getInstance()->getLakeTrip($id);
        if (is_null($lakeTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }
        $lakeTrip->delete();
        return $this->success();
    }
}
