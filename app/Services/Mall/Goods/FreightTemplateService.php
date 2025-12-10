<?php

namespace App\Services\Mall\Goods;

use App\Models\Mall\Goods\Address;
use App\Models\Mall\Goods\FreightTemplate;
use App\Services\BaseService;
use App\Utils\Inputs\FreightTemplateInput;
use App\Utils\Inputs\PageInput;
use App\Utils\MathTool;

class FreightTemplateService extends BaseService
{
    public function getSelfList(PageInput $input, $columns = ['*'])
    {
        return FreightTemplate::query()
            ->where('shop_id', 0)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getPageByShopId($shopId, PageInput $input, $columns = ['*'])
    {
        return FreightTemplate::query()
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByShopId($shopId, $columns = ['*'])
    {
        return FreightTemplate::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getFreightTemplateById($id, $columns = ['*'])
    {
        return FreightTemplate::query()->find($id, $columns);
    }

    public function getListByIds(array $Ids, $columns = ['*'])
    {
        return FreightTemplate::query()->whereIn('id', $Ids)->get($columns);
    }

    public function update($freightTemplate, FreightTemplateInput $input)
    {
        $freightTemplate->name = $input->name;
        $freightTemplate->title = $input->title;
        $freightTemplate->compute_mode = $input->computeMode;
        if (!is_null($input->freeQuota)) {
            $freightTemplate->free_quota = $input->freeQuota;
        }
        $freightTemplate->area_list = json_encode($input->areaList);
        $freightTemplate->save();

        return $freightTemplate;
    }

    public function getOptions($shopId, $columns = ['*'])
    {
        return FreightTemplate::query()->where('shop_id', $shopId)->orderBy('id', 'asc')->get($columns);
    }

    public function getSelfOptions($columns = ['*'])
    {
        return FreightTemplate::query()->where('shop_id', 0)->orderBy('id', 'asc')->get($columns);
    }

    public function calcFreightPrice(FreightTemplate $freightTemplate, Address $address, $totalPrice, $goodsNumber)
    {
        // 包邮额度：大于 free_quota 就免费
        if ($freightTemplate->free_quota != 0 && bccomp($totalPrice, $freightTemplate->free_quota, 2) === 1) {
            return '0.00';
        }

        // 区域匹配：取城市码前 4 位
        $regionCodes = json_decode($address->region_code_list, true) ?? [];
        $cityCode = isset($regionCodes[1]) ? substr($regionCodes[1], 0, 4) : null;
        if (is_null($cityCode)) {
            return '0.00';
        }

        // 找到匹配的区域规则
        $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
            $codes = array_map('trim', explode(',', $area->pickedCityCodes));
            return in_array($cityCode, $codes);
        });

        if (is_null($area)) {
            return '0.00';
        }

        // 固定运费
        if ($freightTemplate->compute_mode == 1) {
            return MathTool::bcRound($area->fee);
        }

        // 按件数计算
        return MathTool::bcRound(bcmul($area->fee, $goodsNumber, 4));
    }
}
