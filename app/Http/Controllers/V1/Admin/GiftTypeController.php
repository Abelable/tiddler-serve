<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftType;
use App\Services\GiftTypeService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class GiftTypeController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = GiftTypeService::getInstance()->getTypeList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = GiftTypeService::getInstance()->getTypeById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前礼包类型不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $category = GiftTypeService::getInstance()->getTypeByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前礼包类型已存在');
        }

        $category = GiftType::new();
        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $category = GiftTypeService::getInstance()->getTypeById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前礼包类型不存在');
        }

        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $category = GiftTypeService::getInstance()->getTypeById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前礼包类型不存在');
        }

        $category->sort = $sort;
        $category->save();

        return $this->success();
    }

    public function editStatus() {
        $id = $this->verifyRequiredId('id');
        $status = $this->verifyRequiredInteger('status');

        $category = GiftTypeService::getInstance()->getTypeById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前礼包类型不存在');
        }

        $category->status = $status;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = GiftTypeService::getInstance()->getTypeById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前礼包类型不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = GiftTypeService::getInstance()->getTypeOptions();
        return $this->success($options);
    }
}
