<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ShopScenicSpotService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopScenicController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ShopScenicSpotService::getInstance()->getListTotal($shopId, 1),
            ShopScenicSpotService::getInstance()->getListTotal($shopId, 0),
            ShopScenicSpotService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $page = ShopScenicSpotService::getInstance()
            ->getScenicPage($shopId, $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $shopScenicSpotList = collect($page->items());

        $scenicIds = $shopScenicSpotList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()
            ->getScenicListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])
            ->keyBy('id');

        $list = $shopScenicSpotList->map(function (ShopScenicSpot $shopScenicSpot) use ($scenicList) {
            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($shopScenicSpot->scenic_id);
            $shopScenicSpot['scenic_image'] = json_decode($scenic->image_list)[0];
            $shopScenicSpot['scenic_name'] = $scenic->name;
            $shopScenicSpot['scenic_level'] = $scenic->level;
            $shopScenicSpot['scenic_address'] = $scenic->address;
            return $shopScenicSpot;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $scenicIds = $this->verifyArrayNotEmpty('scenicIds');

        $userShopId = $this->user()->scenicShop->id;
        $userShopManagerList = $this->user()->scenicShopManagerList;
        if ($userShopId != $shopId && $userShopManagerList->isEmpty()) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }

        ShopScenicSpotService::getInstance()->createScenicList($shopId, $scenicIds);
        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $spot = ShopScenicSpotService::getInstance()->getShopScenicById($shopId, $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $spot->delete();

        return $this->success();
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $scenicIds = ShopScenicSpotService::getInstance()
            ->getShopScenicOptions($shopId)
            ->pluck('scenic_id')
            ->toArray();
        $scenicOptions = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name']);

        return $this->success($scenicOptions);
    }
}
