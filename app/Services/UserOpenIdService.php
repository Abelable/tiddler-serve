<?php

namespace App\Services;

use App\Models\UserOpenId;

class UserOpenIdService extends BaseService
{
    public function create($userId, $openid, $appId)
    {
        return UserOpenId::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'app_id' => $appId,
            ],
            [
                'openid' => $openid,
            ]
        );
    }

    public function getByOpenId($openid, $appId)
    {
        return UserOpenId::query()->where('openid', $openid)->where('app_id', $appId)->first();
    }

    public function getOpenid($userId, $appId)
    {
        return UserOpenId::query()->where('user_id', $userId)->where('app_id', $appId)->first()->openid;
    }
}
