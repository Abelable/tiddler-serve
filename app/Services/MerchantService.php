<?php

namespace App\Services;

use App\Models\Merchant;

class MerchantService extends BaseService
{
    public function getMerchantByUserId($userId, $columns = ['*'])
    {
        return Merchant::query()->where('user_id', $userId)->first($columns);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return Merchant::query()->find($id, $columns);
    }
}
