<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\ShopCategory;
use App\Services\Mall\Goods\ShopCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopCategoryInput;
use App\Utils\Inputs\PageInput;

class ShopCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ShopCategoryService::getInstance()->getCategoryList($input);
        $list = collect($page->items())->map(function (ShopCategory $category) {
            $category->adapted_merchant_types = json_decode($category->adapted_merchant_types);
            return $category;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }
        $category->adapted_merchant_types = json_decode($category->adapted_merchant_types);
        return $this->success($category);
    }

    public function add()
    {
        /** @var ShopCategoryInput $input */
        $input = ShopCategoryInput::new();

        $category = ShopCategoryService::getInstance()->getCategoryByName($input->name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前店铺分类已存在');
        }

        $category = ShopCategory::new();
        $category->name = $input->name;
        $category->deposit = $input->deposit;
        $category->adapted_merchant_types = json_encode($input->adaptedMerchantTypes);
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        /** @var ShopCategoryInput $input */
        $input = ShopCategoryInput::new();

        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }

        $category->name = $input->name;
        $category->deposit = $input->deposit;
        $category->adapted_merchant_types = json_encode($input->adaptedMerchantTypes);
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

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }

        $category->sort = $sort;
        $category->save();

        return $this->success();
    }

    public function editVisible() {
        $id = $this->verifyRequiredId('id');
        $visible = $this->verifyRequiredInteger('visible');

        $category = ShopCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺分类不存在');
        }

        $category->visible = $visible;
        $category->save();

        return $this->success();
    }
}
