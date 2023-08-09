<?php

namespace App\Services;

use App\Models\HotelCategory;
use App\Utils\Inputs\PageInput;

class HotelCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return HotelCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return HotelCategory::query()->find($id, $columns);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return HotelCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return HotelCategory::query()->orderBy('id', 'asc')->get($columns);
    }
}
