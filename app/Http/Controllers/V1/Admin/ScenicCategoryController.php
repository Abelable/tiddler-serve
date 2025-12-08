<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Scenic\ScenicCategory;
use App\Services\Mall\Scenic\ScenicCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class ScenicCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = ScenicCategoryService::getInstance()->getCategoryList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = ScenicCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $category = ScenicCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前景点分类已存在');
        }

        $category = ScenicCategory::new();
        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        $name = $this->verifyRequiredString('name');

        $category = ScenicCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前景点分类已存在');
        }

        $category = ScenicCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点分类不存在');
        }

        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = ScenicCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = ScenicCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }
}
