<?php

namespace App\Services;

use App\Models\GiftType;
use App\Utils\Inputs\PageInput;

class GiftTypeService extends BaseService
{

    public function getTypeList(PageInput $input, $columns = ['*'])
    {
        return GiftType::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTypeById($id, $columns = ['*'])
    {
        return GiftType::query()->find($id, $columns);
    }

    public function getTypeByName($name, $columns = ['*'])
    {
        return GiftType::query()->where('name', $name)->first($columns);
    }

    public function getTypeOptions($columns = ['*'])
    {
        return GiftType::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
