<?php

namespace App\Services;

use App\Models\ScenicCategory;
use App\Models\ScenicProject;
use App\Utils\Inputs\PageInput;

class ScenicProjectService extends BaseService
{
    public function getProjectList(PageInput $input, $columns = ['*'])
    {
        return ScenicProject::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getProjectById($id, $columns = ['*'])
    {
        return ScenicProject::query()->find($id, $columns);
    }

    public function insert($list)
    {
        return ScenicProject::query()->insert($list);
    }
}
