<?php

namespace App\Services;

use App\Models\ShopCategory;

class ShopCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return ShopCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return ShopCategory::query()->find($id, $columns = ['*']);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return ShopCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return ShopCategory::query()->get($columns);
    }
}
