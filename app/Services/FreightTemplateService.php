<?php

namespace App\Services;

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

    public function getOptions($columns = ['*'])
    {
        return FreightTemplate::query()->orderBy('id', 'asc')->get($columns);
    }
}
