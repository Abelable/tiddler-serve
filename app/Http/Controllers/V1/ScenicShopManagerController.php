<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicManagerService;
use App\Services\ScenicShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;
use Illuminate\Support\Facades\DB;

class ScenicShopManagerController extends Controller
{
    public function list()
    {
        /** @var ManagerPageInput $input */
        $input = ManagerPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $page = ScenicShopManagerService::getInstance()->getManagerPage($shopId, $input);
        return $this->successPaginate($page);
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
        /** @var ManagerInput $input */
        $input = ManagerInput::new();
        $scenicIds = $this->verifyArray('scenicIds');

        $manager = ScenicShopManagerService::getInstance()->getManagerByUserId($input->shopId, $input->userId);
        if (!is_null($manager)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '管理人员已存在，请勿重复添加');
        }

        $userInfo = UserService::getInstance()->getUserById($input->userId);

        DB::transaction(function () use ($input, $scenicIds, $userInfo) {
            $manager = ScenicShopManagerService::getInstance()->createManager($input, $userInfo);
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
