<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\ScenicCategory;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ScenicCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return ScenicCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return ScenicCategory::query()->find($id, $columns);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return ScenicCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return ScenicCategory::query()->orderBy('id', 'asc')->get($columns);
    }
}
