<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicShop;
use App\Models\ShopScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ScenicShopService;
use App\Services\ShopScenicSpotService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopScenicController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput  $input */
        $input = StatusPageInput::new();
        $page = ShopScenicSpotService::getInstance()->getAdminScenicPage($input, ['id', 'scenic_id', 'merchant_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $shopScenicList = collect($page->items());

        $shopIds = $shopScenicList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()
            ->getShopListByIds($shopIds, ['id', 'logo', 'name'])->keyBy('id');

        $scenicIds = $shopScenicList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list'])->keyBy('id');

        $list = $shopScenicList->map(function (ShopScenicSpot $shopScenic) use ($scenicList, $shopList) {
            /** @var ScenicShop $shop */
            $shop = $shopList->get($shopScenic->shop_id);
            $shopScenic['shop_logo'] = $shop->logo;
            $shopScenic['shop_name'] = $shop->name;

            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($shopScenic->scenic_id);
            $shopScenic['scenic_name'] = $scenic->name;
            $shopScenic['scenic_image'] = json_decode($scenic->image_list)[0];

            return $shopScenic;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approveScenicApply()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ShopScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->status = 1;
        $scenic->save();

        return $this->success();
    }

    public function rejectScenicApply()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $scenic = ShopScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->status = 2;
        $scenic->failure_reason = $reason;
        $scenic->save();

        return $this->success();
    }

    public function deleteScenicApply()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ShopScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }
}
