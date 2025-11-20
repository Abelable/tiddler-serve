<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\RestaurantManagerService;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;
use Illuminate\Support\Facades\DB;

class CateringShopManagerController extends Controller
{
    public function list()
    {
        /** @var ManagerPageInput $input */
        $input = ManagerPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $page = CateringShopManagerService::getInstance()->getManagerPage($shopId, $input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'user_id', 'role_id'];

        $manager = CateringShopManagerService::getInstance()->getShopManager($shopId, $id, $columns);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $restaurantIds = RestaurantManagerService::getInstance()
            ->getListByManagerId($manager->id)->pluck('restaurant_id')->toArray();
        $manager['restaurantIds'] = $restaurantIds;

        return $this->success($manager);
    }

    public function add()
    {
        /** @var ManagerInput $input */
        $input = ManagerInput::new();
        $restaurantIds = $this->verifyArray('restaurantIds');

        $manager = CateringShopManagerService::getInstance()->getManagerByUserId($input->shopId, $input->userId);
        if (!is_null($manager)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理人员已存在，请勿重复添加');
        }

        $userInfo = UserService::getInstance()->getUserById($input->userId);

        DB::transaction(function () use ($input, $restaurantIds, $userInfo) {
            $manager = CateringShopManagerService::getInstance()->createManager($input, $userInfo);
            foreach ($restaurantIds as $restaurantId) {
                RestaurantManagerService::getInstance()->createManager($restaurantId, $manager->id);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $roleId = $this->verifyRequiredId('roleId');
        $restaurantIds = $this->verifyArrayNotEmpty('restaurantIds');

        $manager = CateringShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        DB::transaction(function () use ($manager, $roleId, $restaurantIds) {
            CateringShopManagerService::getInstance()->updateManager($manager, $roleId);

            RestaurantManagerService::getInstance()->deleteManager($manager->id);
            foreach ($restaurantIds as $restaurantId) {
                RestaurantManagerService::getInstance()->createManager($restaurantId, $manager->id);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $manager = CateringShopManagerService::getInstance()->getShopManager($shopId, $id);
        if (is_null($manager)) {
            return $this->fail(CodeResponse::NOT_FOUND, '管理员不存在');
        }

        $manager->delete();
        return $this->success();
    }
}
