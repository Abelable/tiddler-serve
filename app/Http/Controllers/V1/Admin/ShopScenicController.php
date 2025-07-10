<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicMerchant;
use App\Models\ScenicShop;
use App\Models\ShopScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ScenicMerchantService;
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
        $page = ShopScenicSpotService::getInstance()->getAdminScenicPage($input);
        $shopScenicList = collect($page->items());

        $shopIds = $shopScenicList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $merchantIds = $shopList->pluck('merchant_id')->toArray();
        $merchantList = ScenicMerchantService::getInstance()->getMerchantListByIds($merchantIds)->keyBy('id');

        $scenicIds = $shopScenicList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()
            ->getScenicListByIds($scenicIds, ['id', 'name', 'image_list'])->keyBy('id');

        $list = $shopScenicList->map(function (ShopScenicSpot $shopScenic) use ($scenicList, $shopList, $merchantList) {
            /** @var ScenicShop $shop */
            $shop = $shopList->get($shopScenic->shop_id);
            $shopScenic['shop_logo'] = $shop->logo;
            $shopScenic['shop_name'] = $shop->name;

            /** @var ScenicMerchant $merchant */
            $merchant = $merchantList->get($shop->merchant_id);
            $shopScenic['merchant_name'] = $merchant->company_name;
            $shopScenic['business_license'] = $merchant->business_license_photo;

            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($shopScenic->scenic_id);
            $shopScenic['scenic_name'] = $scenic->name;
            $shopScenic['scenic_image'] = json_decode($scenic->image_list)[0];

            return $shopScenic;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approve()
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

    public function reject()
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

    public function delete()
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
