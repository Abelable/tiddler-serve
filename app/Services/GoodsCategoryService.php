<?php

namespace App\Services;

use App\Models\GoodsCategory;
use App\Utils\Inputs\Admin\GoodsCategoryPageInput;

class GoodsCategoryService extends BaseService
{
    public function getCategoryList(GoodsCategoryPageInput $input, $columns = ['*'])
    {
        $query = GoodsCategory::query();
        if (!empty($input->shopCategoryId)) {
            $query = $query->where('shop_category_id', $input->shopCategoryId);
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

    public function getCategoryOptions($shopCategoryId = null, $columns = ['*'])
    {
        $query = GoodsCategory::query();
        if (!is_null($shopCategoryId)) {
            $query = $query->where('shop_category_id', $shopCategoryId);
        }
        return $query->orderBy('id', 'asc')->get($columns);
    }

    public function getOptionsByShopCategoryIds(array $shopCategoryIds, $columns = ['*'])
    {
        return GoodsCategory::query()->whereIn('shop_category_id', $shopCategoryIds)->get($columns);
    }
}
