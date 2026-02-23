<?php

namespace App\Services;

use App\Models\WxMp;
use App\Utils\Inputs\Admin\WxMpInput;
use App\Utils\Inputs\NamePageInput;

class WxMpService extends BaseService
{
    public function update($mp, WxMpInput $input)
    {
        $mp->app_id = $input->appId;
        $mp->secret = $input->secret;
        $mp->name = $input->name;
        $mp->save();
        return $mp;
    }

    public function getById($id, $columns = ['*'])
    {
        return WxMp::query()->find($id, $columns);
    }

    public function getSecret($appId)
    {
        $mp = WxMp::query()->where('app_id', $appId)->firstOrFail();
        return $mp->secret;
    }

    public function getPage(NamePageInput $input, $columns = ['*'])
    {
        $query = WxMp::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', '%' . $input->name . '%');
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
