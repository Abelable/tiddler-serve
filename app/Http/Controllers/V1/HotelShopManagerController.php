<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Hotel\HotelManagerService;
use App\Services\Mall\Hotel\HotelShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;
use Illuminate\Support\Facades\DB;

class HotelShopManagerController extends Controller
{
    public function list()
    {
        /** @var ManagerPageInput $input */
        $input = ManagerPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $page = HotelShopManagerService::getInstance()->getManagerPage($shopId, $input);
        return $this->successPaginate($page);
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
        /** @var ManagerInput $input */
        $input = ManagerInput::new();
        $hotelIds = $this->verifyArray('hotelIds');

        $manager = HotelShopManagerService::getInstance()->getManagerByUserId($input->shopId, $input->userId);
        if (!is_null($manager)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理人员已存在，请勿重复添加');
        }

        $userInfo = UserService::getInstance()->getUserById($input->userId);

        DB::transaction(function () use ($input, $hotelIds, $userInfo) {
            $manager = HotelShopManagerService::getInstance()->createManager($input, $userInfo);
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
