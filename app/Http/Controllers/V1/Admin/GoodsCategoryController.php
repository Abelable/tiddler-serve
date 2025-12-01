<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoodsCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsCategoryInput;
use App\Utils\Inputs\Admin\GoodsCategoryPageInput;

class GoodsCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GoodsCategoryPageInput $input */
        $input = GoodsCategoryPageInput::new();

        $list = GoodsCategoryService::getInstance()->getCategoryList($input);

        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        /** @var GoodsCategoryInput $input */
        $input = GoodsCategoryInput::new();

        $category = GoodsCategoryService::getInstance()->getCategoryByName($input->name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前商品分类已存在');
        }

        GoodsCategoryService::getInstance()->createGoodsCategory($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var GoodsCategoryInput $input */
        $input = GoodsCategoryInput::new();
        $id = $this->verifyId('id');

        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        GoodsCategoryService::getInstance()->updateGoodsCategory($category, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $shopCategoryIds = $this->verifyArray('shopCategoryIds');
        $options = GoodsCategoryService::getInstance()->getCategoryOptions($shopCategoryIds);
        return $this->success($options);
    }
}
