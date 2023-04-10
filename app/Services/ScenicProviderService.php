<?php

namespace App\Services;

use App\Models\ScenicProvider;
use App\Utils\Inputs\ScenicProviderListInput;

class ScenicProviderService extends BaseService
{
    public function getProviderList(ScenicProviderListInput $input, $columns = ['*'])
    {
        $query = ScenicProvider::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getProviderByUserId($userId, $columns = ['*'])
    {
        return ScenicProvider::query()->where('user_id', $userId)->first($columns);
    }

    public function getProviderById($id, $columns = ['*'])
    {
        return ScenicProvider::query()->find($id, $columns);
    }

    public function paySuccess(int $merchantId)
    {
        $provider = $this->getProviderById($merchantId);
        if (is_null($provider)) {
            $this->throwBadArgumentValue();
        }
        $provider->status = 2;
        $provider->save();
        return $provider;
    }
}
