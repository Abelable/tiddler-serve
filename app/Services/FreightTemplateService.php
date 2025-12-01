<?php

namespace App\Services;

use App\Models\Address;
use App\Models\FreightTemplate;
use App\Utils\Inputs\FreightTemplateInput;
use App\Utils\Inputs\PageInput;

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
        if ($freightTemplate->free_quota != 0 && $totalPrice > $freightTemplate->free_quota) {
            $freightPrice = 0;
        } else {
            $cityCode = substr(json_decode($address->region_code_list)[1], 0, 4);
            $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                return in_array($cityCode, explode(',', $area->pickedCityCodes));
            });
            if (is_null($area)) {
                $freightPrice = 0;
            } else {
                if ($freightTemplate->compute_mode == 1) {
                    $freightPrice = $area->fee;
                } else {
                    $freightPrice = bcmul($area->fee, $goodsNumber, 2);
                }
            }
        }
        return $freightPrice;
    }
}
