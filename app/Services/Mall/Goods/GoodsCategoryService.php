<?php

namespace App\Services\Mall\Goods;

use App\Models\Mall\Goods\GoodsCategory;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\GoodsCategoryInput;
use App\Utils\Inputs\Admin\GoodsCategoryPageInput;

class GoodsCategoryService extends BaseService
{
    public function createGoodsCategory(GoodsCategoryInput $input)
    {
        $category = GoodsCategory::new();
        return $this->updateGoodsCategory($category, $input);
    }

    public function updateGoodsCategory(GoodsCategory $category, GoodsCategoryInput $input)
    {
        $category->logo = $input->logo ?? '';
        $category->name = $input->name;
        $category->description = $input->description ?? '';
        $category->min_sales_commission_rate = $input->minSalesCommissionRate;
        $category->max_sales_commission_rate = $input->maxSalesCommissionRate;
        $category->min_promotion_commission_rate = $input->minPromotionCommissionRate;
        $category->max_promotion_commission_rate = $input->maxPromotionCommissionRate;
        $category->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        $category->min_superior_promotion_commission_rate = $input->minSuperiorPromotionCommissionRate;
        $category->max_superior_promotion_commission_rate = $input->maxSuperiorPromotionCommissionRate;
        $category->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        $category->save();

        $category->shopCategories()->sync($input->shopCategoryIds);

        return $category;
    }

    public function getCategoryList(GoodsCategoryPageInput $input, $columns = ['*'])
    {
        $query = GoodsCategory::query()->with('shopCategories:id');
        if (!empty($input->shopCategoryId)) {
            $query->whereHas('shopCategories', function ($q) use ($input) {
                $q->where('shop_categories.id', $input->shopCategoryId);
            });
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return GoodsCategory::query()->find($id, $columns = ['*']);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return GoodsCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions(array $shopCategoryIds = [], $columns = ['*'])
    {
        $query = GoodsCategory::query();
        if (count($shopCategoryIds) != 0) {
            $query->whereHas('shopCategories', function ($q) use ($shopCategoryIds) {
                $q->whereIn('shop_categories.id', $shopCategoryIds);
            });
        }
        return $query->orderBy('id', 'asc')->get($columns);
    }
}
