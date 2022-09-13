<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Services\BaseService;

class AdminService extends BaseService
{
    public function getUserByAccount($account)
    {
        return Admin::query()->where('account', $account)->first();
    }

}
