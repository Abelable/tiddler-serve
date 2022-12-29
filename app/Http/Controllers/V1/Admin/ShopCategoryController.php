<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Services\ShopCategoryService;
use App\Utils\Inputs\PageInput;

class ShopCategoryController extends Controller
{
    protected $guard = 'admin';

    public function list()
    {
        $input = PageInput::new();
        $list = ShopCategoryService::getInstance()->getCategoryList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $category = ShopCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前店铺分类已存在');
        }

        $category = ShopCategory::new();
        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        $name = $this->verifyRequiredString('name');

        $category = ShopCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前店铺分类已存在');
        }

        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }

        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = ShopCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }
}
