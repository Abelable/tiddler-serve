<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Hotel\HotelCategory;
use App\Services\Mall\Hotel\HotelCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class HotelCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = HotelCategoryService::getInstance()->getCategoryList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = HotelCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $category = HotelCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前酒店分类已存在');
        }

        $category = HotelCategory::new();
        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        $name = $this->verifyRequiredString('name');

        $category = HotelCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前酒店分类已存在');
        }

        $category = HotelCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店分类不存在');
        }

        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = HotelCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = HotelCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }
}
