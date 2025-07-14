<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelShopManager;
use App\Models\User;
use App\Services\HotelManagerService;
use App\Services\HotelShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\DB;

class HotelShopManagerController extends Controller
{
    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'user_id', 'role_id'];

        $managerList = HotelShopManagerService::getInstance()->getManagerList($shopId, $columns);

        $userIds = $managerList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');

        $list = $managerList->map(function (HotelShopManager $manager) use ($userList) {
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

        $manager = HotelShopManagerService::getInstance()->getShopManager($shopId, $id, $columns);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $hotelIds = HotelManagerService::getInstance()
            ->getListByManagerId($manager->id)->pluck('hotel_id')->toArray();
        $manager['hotelIds'] = $hotelIds;

        return $this->success($manager);
    }

    public function add()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $userId = $this->verifyRequiredId('userId');
        $roleId = $this->verifyRequiredId('roleId');
        $hotelIds = $this->verifyArray('hotelIds');

        DB::transaction(function () use ($shopId, $userId, $roleId, $hotelIds) {
            $manager = HotelShopManagerService::getInstance()->createManager($userId, $roleId, $shopId);
            foreach ($hotelIds as $hotelId) {
                HotelManagerService::getInstance()->createManager($hotelId, $manager->id);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $roleId = $this->verifyRequiredId('roleId');
        $hotelIds = $this->verifyArrayNotEmpty('hotelIds');

        $manager = HotelShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        DB::transaction(function () use ($manager, $roleId, $hotelIds) {
            HotelShopManagerService::getInstance()->updateManager($manager, $roleId);

            HotelManagerService::getInstance()->deleteManager($manager->id);
            foreach ($hotelIds as $hotelId) {
                HotelManagerService::getInstance()->createManager($hotelId, $manager->id);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $manager = HotelShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $manager->delete();
        return $this->success();
    }
}
