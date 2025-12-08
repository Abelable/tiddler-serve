<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\ScenicTicketCategory;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ScenicTicketCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return ScenicTicketCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return ScenicTicketCategory::query()->find($id, $columns);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return ScenicTicketCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return ScenicTicketCategory::query()->orderBy('id', 'asc')->get($columns);
    }
}
