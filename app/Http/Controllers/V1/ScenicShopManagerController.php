<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicShopManager;
use App\Models\User;
use App\Services\ScenicManagerService;
use App\Services\ScenicShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\DB;

class ScenicShopManagerController extends Controller
{
    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'user_id', 'role_id'];

        $managerList = ScenicShopManagerService::getInstance()->getManagerList($shopId, $columns);

        $userIds = $managerList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');

        $list = $managerList->map(function (ScenicShopManager $manager) use ($userList) {
            /** @var User $user */
            $user = $userList->get($manager->user_id);

            $manager['avatar'] = $user->avatar;
            $manager['nickname'] = $user->nickname;
            $manager['mobile'] = $user->mobile;

            return $manager;
        });

        return $this->success($list);
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'user_id', 'role_id'];

        $manager = ScenicShopManagerService::getInstance()->getShopManager($shopId, $id, $columns);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $scenicIds = ScenicManagerService::getInstance()
            ->getListByManagerId($manager->id)->pluck('scenic_id')->toArray();
        $manager['scenicIds'] = $scenicIds;

        return $this->success($manager);
    }

    public function add()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $userId = $this->verifyRequiredId('userId');
        $roleId = $this->verifyRequiredId('roleId');
        $scenicIds = $this->verifyArray('scenicIds');

        $manager = ScenicShopManagerService::getInstance()->getManagerByUserId($shopId, $userId);
        if (!is_null($manager)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理人员已存在，请勿重复添加');
        }

        DB::transaction(function () use ($shopId, $userId, $roleId, $scenicIds) {
            $manager = ScenicShopManagerService::getInstance()->createManager($shopId, $userId, $roleId);
            foreach ($scenicIds as $scenicId) {
                ScenicManagerService::getInstance()->createManager($scenicId, $manager->id);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $roleId = $this->verifyRequiredId('roleId');
        $scenicIds = $this->verifyArrayNotEmpty('scenicIds');

        $manager = ScenicShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        DB::transaction(function () use ($manager, $roleId, $scenicIds) {
            ScenicShopManagerService::getInstance()->updateManager($manager, $roleId);

            ScenicManagerService::getInstance()->deleteManager($manager->id);
            foreach ($scenicIds as $scenicId) {
                ScenicManagerService::getInstance()->createManager($scenicId, $manager->id);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $manager = ScenicShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $manager->delete();
        return $this->success();
    }
}
