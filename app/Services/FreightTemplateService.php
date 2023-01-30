<?php

namespace App\Services;

use App\Models\FreightTemplate;
use App\Utils\Inputs\PageInput;

class FreightTemplateService extends BaseService
{
    public function getListByUserId($userId, PageInput $input, $columns = ['*'])
    {
        return FreightTemplate::query()->where('user_id', $userId)->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getFreightTemplateById($id, $columns = ['*'])
    {
        return FreightTemplate::query()->find($id, $columns);
    }
}
