<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Theme\StarTripService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\StarTripInput;
use App\Utils\Inputs\PageInput;

class StarTripController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = StarTripService::getInstance()->getStarTripPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $starTrip = StarTripService::getInstance()->getStarTrip($id);
        if (is_null($starTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前明星同游地不存在');
        }
        return $this->success($starTrip);
    }

    public function add()
    {
        /** @var StarTripInput $input */
        $input = StarTripInput::new();

        StarTripService::getInstance()->createStarTrip($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var StarTripInput $input */
        $input = StarTripInput::new();
        $id = $this->verifyRequiredId('id');

        $starTrip = StarTripService::getInstance()->getStarTrip($id);
        if (is_null($starTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前明星同游地不存在');
        }

        StarTripService::getInstance()->updateStarTrip($starTrip, $input);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $starTrip = StarTripService::getInstance()->getStarTrip($id);
        if (is_null($starTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前明星同游地不存在');
        }

        $starTrip->sort = $sort;
        $starTrip->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $starTrip = StarTripService::getInstance()->getStarTrip($id);
        if (is_null($starTrip)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前明星同游地不存在');
        }
        $starTrip->delete();
        return $this->success();
    }
}
