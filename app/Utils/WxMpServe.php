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
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';

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
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $accessToken = $result['access_token'];
        Cache::put(self::ACCESS_TOKEN_KEY, $accessToken, now()->addSeconds(7000));
        return $accessToken;
    }

    public function getUserPhoneNumber($code)
    {
        $result = $this->httpPost(sprintf(self::GET_PHONE_NUMBER_URL, $this->accessToken), ['code' => $code]);
        if ($result['errcode'] != 0) {
            // 目前有出现access_token在缓存有效期内失效的问题，暂未找到具体解决办法，在此作重置处理
            if ($result['errcode'] == 40001) {
                Cache::forget(self::ACCESS_TOKEN_KEY);
                $accessToken = $this->getAccessToken();
                $this->accessToken = $accessToken;
                return $this->getUserPhoneNumber($code);
            }
            throw new \Exception('获取微信小程序用户手机号异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result['phone_info']['purePhoneNumber'];
    }

    public function getUserOpenid($code)
    {
        $result = $this->httpGet(sprintf(self::GET_OPENID_URL, env('WX_MP_APPID'), env('WX_MP_SECRET'), $code));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序openid异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result;
    }
}
