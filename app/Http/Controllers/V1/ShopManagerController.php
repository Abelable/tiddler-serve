<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopManager;
use App\Models\User;
use App\Services\ShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;

class ShopManagerController extends Controller
{
    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'user_id', 'role_id'];

        $managerList = ShopManagerService::getInstance()->getManagerList($shopId, $columns);

        $userIds = $managerList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');

        $list = $managerList->map(function (ShopManager $manager) use ($userList) {
            /** @var User $user */
            $user = $userList->get($manager->user_id);

            $manager['avatar'] = $user->avatar;
            $manager['nickname'] = $user->nickname;

            return $manager;
        });


        return $this->success($list);
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'user_id', 'role_id'];

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id, $columns);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '店铺管理员不存在');
        }

        return $this->success($manager);
    }

    public function add()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $userId = $this->verifyRequiredId('userId');
        $roleId = $this->verifyRequiredId('roleId');

        ShopManagerService::getInstance()->createManager($userId, $roleId, $shopId);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $shopId = $this->verifyRequiredId('shopId');
        $userId = $this->verifyRequiredId('userId');
        $roleId = $this->verifyRequiredId('roleId');

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '店铺管理员不存在');
        }

        ShopManagerService::getInstance()->createManager($manager, $userId, $roleId);

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '店铺管理员不存在');
        }

        $manager->delete();
        return $this->success();
    }
}
