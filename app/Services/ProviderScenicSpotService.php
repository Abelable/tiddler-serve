<?php

namespace App\Services;

use App\Models\ProviderScenicSpot;
use App\Utils\Inputs\ProviderScenicSpotListInput;

class ProviderScenicSpotService extends BaseService
{
    public function getSpotList($userId, ProviderScenicSpotListInput $input, $columns = ['*'])
    {
        return ProviderScenicSpot::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getSpotById($userId, $id, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->find($id, $columns);
    }
}
