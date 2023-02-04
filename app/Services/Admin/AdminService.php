<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\AdminListInput;

class AdminService extends BaseService
{
    public function getAdminById($id, $columns = ['*'])
    {
        return Admin::query()->find($id, $columns);
    }

    public function getAdminByAccount($account, $columns = ['*'])
    {
        return Admin::query()->where('account', $account)->first($columns);
    }

    public function getAdminList(AdminListInput $input, $columns = ['*'])
    {
        $query = Admin::query();
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', 'like', "%$input->nickname%");
        }
        if (!empty($input->account)) {
            $query = $query->where('account', $input->account);
        }
        if (!empty($input->roleId)) {
            $query = $query->where('role_id', $input->roleId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }
}
