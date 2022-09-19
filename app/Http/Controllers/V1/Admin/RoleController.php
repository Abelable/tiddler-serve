<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Services\Admin\RoleService;
use App\Utils\Inputs\PageInput;

class RoleController extends Controller
{
    protected $guard = 'admin';

    public function list()
    {
        $input = PageInput::new();
        $list = RoleService::getInstance()->getRoleList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $role = RoleService::getInstance()->getRoleById($id);

    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');
        $desc = $this->verifyString('desc');

        $role = AdminRole::new();
        $role->name = $name;
        $role->desc = $desc;
        $role->save();

        return $this->success();
    }

    public function edit()
    {}

    public function delete()
    {}
}
