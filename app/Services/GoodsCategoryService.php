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

    public function getCategoryOptions($columns = ['*'])
    {
        return GoodsCategory::query()->orderBy('id', 'asc')->get($columns);
    }

    public function getOptionsByShopCategoryId($shopCategoryId, $columns = ['*'])
    {
        return GoodsCategory::query()->where('shop_category_id', $shopCategoryId)->get($columns);
    }
}
