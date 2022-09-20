<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\BaseService;
use App\Utils\Inputs\UserListInput;

class UserService extends BaseService
{
    public function getUserList(UserListInput $input, $columns = ['*'])
    {
        $query = User::query();
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', 'like', "%$input->nickname%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserById($id, $columns = ['*'])
    {
        return User::query()->find($id, $columns);
    }
}
