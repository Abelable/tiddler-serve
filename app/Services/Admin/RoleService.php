<?php

namespace App\Services\Admin;

use App\Models\AdminRole;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class RoleService extends BaseService
{
    public function getRoleList(PageInput $input, $columns = ['*'])
    {
        return AdminRole::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRoleById($id, $columns = ['*'])
    {
        return AdminRole::query()->find($id, $columns);
    }

    public function getRoleByName($name, $columns = ['*'])
    {
        return AdminRole::query()->where('name', $name)->first($columns);
    }

    public function getRoleOptions($columns = ['*'])
    {
        return AdminRole::query()->get($columns);
    }
}
