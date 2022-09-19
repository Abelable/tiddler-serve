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

    public function getRoleById($id)
    {
        return AdminRole::query()->find($id);
    }
}
