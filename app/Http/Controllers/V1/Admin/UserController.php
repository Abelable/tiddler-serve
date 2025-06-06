<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\UserPageInput;

class UserController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var UserPageInput $input */
        $input = UserPageInput::new();
        $list = UserService::getInstance()->getUserPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $user = UserService::getInstance()->getUserById($id);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }
        return $this->success($user);
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $avatar = $this->verifyString('avatar');
        $nickname = $this->verifyString('nickname');

        $user = UserService::getInstance()->getUserById($id);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }

        if (!is_null($avatar)) {
            $user->avatar = $avatar;
        }
        if (!is_null($nickname)) {
            $user->nickname = $nickname;
        }

        $user->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $user = UserService::getInstance()->getUserById($id);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }
        $user->delete();
        return $this->success();
    }

    public function options()
    {
        $options = UserService::getInstance()->getUserList(['id', 'avatar', 'nickname']);
        return $this->success($options);
    }
}
