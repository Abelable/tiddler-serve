<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $guard = 'admin';

    public function list()
    {}

    public function add()
    {
        $account = $this->verifyRequiredString('account');
        $password = $this->verifyRequiredString('password');
        $roleId = $this->verifyRequiredId('role_id');

        $user = AdminService::getInstance()->getUserByAccount($account);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::REGISTERED_ACCOUNT);
        }

        $user = Admin::new();
        $user->account = $account;
        $user->password = Hash::make($password);
        $user->role_id = $roleId;
        $user->save();

        return $this->success();
    }

    public function edit()
    {}

    public function delete()
    {}
}
