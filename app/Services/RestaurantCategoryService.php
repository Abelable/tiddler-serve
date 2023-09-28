<?php

namespace App\Services;

use App\Models\RestaurantCategory;
use App\Utils\Inputs\PageInput;

class RestaurantCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return RestaurantCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return RestaurantCategory::query()->find($id, $columns);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return RestaurantCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return RestaurantCategory::query()->orderBy('id', 'asc')->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return RestaurantCategory::query()->whereIn('id', $ids)->get($columns);
    }
}
