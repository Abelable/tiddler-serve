<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\HotScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotScenicInput;
use App\Utils\Inputs\PageInput;

class HotScenicController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = HotScenicService::getInstance()->getHotScenicPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $hotScenic = HotScenicService::getInstance()->getHotScenic($id);
        if (is_null($hotScenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前网红打卡地不存在');
        }
        return $this->success($hotScenic);
    }

    public function add()
    {
        /** @var HotScenicInput $input */
        $input = HotScenicInput::new();

        HotScenicService::getInstance()->createHotScenic($input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $recommendReason = $this->verifyRequiredString('recommendReason');
        $interestedUserNumber = $this->verifyRequiredInteger('interestedUserNumber');

        $hotScenic = HotScenicService::getInstance()->getHotScenic($id);
        if (is_null($hotScenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前网红打卡地不存在');
        }

        HotScenicService::getInstance()->updateHotScenic($hotScenic, $recommendReason, $interestedUserNumber);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $hotScenic = HotScenicService::getInstance()->getHotScenic($id);
        if (is_null($hotScenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前网红打卡地不存在');
        }

        $hotScenic->sort = $sort;
        $hotScenic->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $hotScenic = HotScenicService::getInstance()->getHotScenic($id);
        if (is_null($hotScenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前网红打卡地不存在');
        }
        $hotScenic->delete();
        return $this->success();
    }
}
