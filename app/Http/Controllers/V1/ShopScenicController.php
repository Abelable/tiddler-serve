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
    public function listTotals()
    {
        return $this->success([
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 1),
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 0),
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ShopScenicSpotService::getInstance()->getUserSpotList($this->userId(), $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerScenicSpotList = collect($page->items());
        $scenicIds = $providerScenicSpotList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerScenicSpotList->map(function (ShopScenicSpot $providerScenicSpot) use ($scenicList) {
            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($providerScenicSpot->scenic_id);
            $providerScenicSpot['scenic_image'] = json_decode($scenic->image_list)[0];
            $providerScenicSpot['scenic_name'] = $scenic->name;
            $providerScenicSpot['scenic_level'] = $scenic->level;
            $providerScenicSpot['scenic_address'] = $scenic->address;
            return $providerScenicSpot;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $scenicIds = $this->verifyArrayNotEmpty('scenicIds');
        $scenicMerchant = $this->user()->scenicMerchant;
        if (is_null($scenicMerchant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }
        ShopScenicSpotService::getInstance()->createScenicList($this->userId(), $scenicMerchant->id, $scenicIds);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $spot = ShopScenicSpotService::getInstance()->getUserSpotById($this->userId(), $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $spot->delete();
        return $this->success();
    }

    public function options()
    {
        $scenicIds = ShopScenicSpotService::getInstance()->getUserScenicOptions($this->userId())->pluck('scenic_id')->toArray();
        $scenicOptions = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name']);
        return $this->success($scenicOptions);
    }
}
