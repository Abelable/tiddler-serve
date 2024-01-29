<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ProviderScenicSpotService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ProviderScenicController extends Controller
{
    public function listTotals()
    {
        return $this->success([
            ProviderScenicSpotService::getInstance()->getListTotal($this->userId(), 1),
            ProviderScenicSpotService::getInstance()->getListTotal($this->userId(), 0),
            ProviderScenicSpotService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderScenicSpotService::getInstance()->getUserSpotList($this->userId(), $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerScenicSpotList = collect($page->items());
        $scenicIds = $providerScenicSpotList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerScenicSpotList->map(function (ProviderScenicSpot $providerScenicSpot) use ($scenicList) {
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
        $scenicProvider = $this->user()->scenicProvider;
        if (is_null($scenicProvider)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }
        ProviderScenicSpotService::getInstance()->createScenicList($this->userId(), $scenicProvider->id, $scenicIds);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $spot = ProviderScenicSpotService::getInstance()->getUserSpotById($this->userId(), $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $spot->delete();
        return $this->success();
    }

    public function options()
    {
        $scenicIds = ProviderScenicSpotService::getInstance()->getUserScenicOptions($this->userId())->pluck('scenic_id')->toArray();
        $scenicOptions = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name']);
        return $this->success($scenicOptions);
    }
}
