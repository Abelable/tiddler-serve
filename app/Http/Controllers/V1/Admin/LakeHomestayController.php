<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\LakeHomestayService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\LakeHomestayInput;
use App\Utils\Inputs\PageInput;

class LakeHomestayController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LakeHomestayService::getInstance()->getLakeHomestayPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $lakeHomestay = LakeHomestayService::getInstance()->getLakeHomestay($id);
        if (is_null($lakeHomestay)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前民宿不存在');
        }
        return $this->success($lakeHomestay);
    }

    public function add()
    {
        /** @var LakeHomestayInput $input */
        $input = LakeHomestayInput::new();

        LakeHomestayService::getInstance()->createLakeHomestay($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var LakeHomestayInput $input */
        $input = LakeHomestayInput::new();
        $id = $this->verifyRequiredId('id');

        $lakeHomestay = LakeHomestayService::getInstance()->getLakeHomestay($id);
        if (is_null($lakeHomestay)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前民宿不存在');
        }

        LakeHomestayService::getInstance()->updateLakeHomestay($lakeHomestay, $input);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $lakeHomestay = LakeHomestayService::getInstance()->getLakeHomestay($id);
        if (is_null($lakeHomestay)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前民宿不存在');
        }

        $lakeHomestay->sort = $sort;
        $lakeHomestay->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $lakeHomestay = LakeHomestayService::getInstance()->getLakeHomestay($id);
        if (is_null($lakeHomestay)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前民宿不存在');
        }
        $lakeHomestay->delete();
        return $this->success();
    }
}
