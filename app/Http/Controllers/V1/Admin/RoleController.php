<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;

class RoleController extends Controller
{
    protected $guard = 'admin';

    public function list()
    {}

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
