<?php

namespace App\Utils;

use App\Utils\Traits\HttpClient;
use Illuminate\Support\Facades\Cache;

class WxMpServe
{
    use HttpClient;

    const ACCESS_TOKEN_KEY = 'wx_mp_access_token';
    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    const GET_PHONE_NUMBER_URL = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=%s';

    private $accessToken;

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $accessToken = Cache::get(self::ACCESS_TOKEN_KEY);
        if (empty($accessToken)) {
            $accessToken = $this->getAccessToken();
        }
        $this->accessToken = $accessToken;
    }

    private function getAccessToken()
    {
        $result = $this->httpGet(sprintf(self::GET_ACCESS_TOKEN_URL, env('WX_MP_APPID'), env('WX_MP_SECRET')));
//        if ($result['errcode'] != 0) {
//        }
        $accessToken = $result['access_token'];
        Cache::put(self::ACCESS_TOKEN_KEY, $accessToken, 7200);
        return $accessToken;
    }

    public function getUserPhoneNumber($code)
    {
        $result = $this->httpPost(sprintf(self::GET_PHONE_NUMBER_URL, $this->accessToken), ['code' => $code]);
        return $result['phone_info']['purePhoneNumber'];
    }
}
