<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopManager;
use App\Models\User;
use App\Services\ShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;

class ShopManagerController extends Controller
{
    public function list()
    {
        /** @var ManagerPageInput $input */
        $input = ManagerPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'user_id', 'role_id', 'created_at', 'updated_at'];

        $page = ShopManagerService::getInstance()->getManagerPage($shopId, $input, $columns);
        $managerList = collect($page->items());

        $userIds = $managerList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');

        $list = $managerList->map(function (ShopManager $manager) use ($userList) {
            /** @var User $user */
            $user = $userList->get($manager->user_id);

            $manager['avatar'] = $user->avatar;
            $manager['nickname'] = $user->nickname;
            $manager['mobile'] = $user->mobile;

            return $manager;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'user_id', 'role_id'];

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id, $columns);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        return $this->success($manager);
    }

    public function add()
    {
        /** @var ManagerInput $input */
        $input = ManagerInput::new();

        $manager = ShopManagerService::getInstance()->getManagerByUserId($input->shopId, $input->userId);
        if (!is_null($manager)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理人员已存在，请勿重复添加');
        }

        $userInfo = UserService::getInstance()->getUserById($input->userId);
        ShopManagerService::getInstance()->createManager($input, $userInfo);

        return $this->success();
    }

    public function edit()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $roleId = $this->verifyRequiredId('roleId');

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        ShopManagerService::getInstance()->updateManager($manager, $roleId);

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $manager = ShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $manager->delete();
        return $this->success();
    }
}
