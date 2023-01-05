<?php

namespace App\Services;

use App\Models\Merchant;
use App\Utils\Inputs\MerchantListInput;

class MerchantService extends BaseService
{
    public function getMerchantList(MerchantListInput $input, $columns = ['*'])
    {
        $query = Merchant::query();
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getMerchantByUserId($userId, $columns = ['*'])
    {
        return Merchant::query()->where('user_id', $userId)->first($columns);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return Merchant::query()->find($id, $columns);
    }

    public function paySuccess(int $merchantId)
    {
        $merchant = $this->getMerchantById($merchantId);
        if (is_null($merchant)) {
            $this->throwBadArgumentValue();
        }
        $merchant->status = 2;
        $merchant->save();
        return $merchant;
    }
}
