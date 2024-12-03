<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsCategory;
use App\Services\GoodsCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsCategoryInput;
use App\Utils\Inputs\Admin\GoodsCategoryPageInput;
use App\Utils\Inputs\PageInput;

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

        $category = GoodsCategory::new();
        $category->shop_category_id = $input->shopCategoryId;
        $category->name = $input->name;
        $category->min_sales_commission_rate = $input->minSalesCommissionRate;
        $category->max_sales_commission_rate = $input->maxSalesCommissionRate;
        $category->min_promotion_commission_rate = $input->minPromotionCommissionRate;
        $category->max_promotion_commission_rate = $input->maxPromotionCommissionRate;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        /** @var GoodsCategoryInput $input */
        $input = GoodsCategoryInput::new();

        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        $category->shop_category_id = $input->shopCategoryId;
        $category->name = $input->name;
        $category->min_sales_commission_rate = $input->minSalesCommissionRate;
        $category->max_sales_commission_rate = $input->maxSalesCommissionRate;
        $category->min_promotion_commission_rate = $input->minPromotionCommissionRate;
        $category->max_promotion_commission_rate = $input->maxPromotionCommissionRate;
        $category->save();

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
        $shopCategoryId = $this->verifyId('shopCategoryId');
        $options = GoodsCategoryService::getInstance()->getCategoryOptions($shopCategoryId || null);
        return $this->success($options);
    }
}
