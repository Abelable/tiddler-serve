<?php

namespace App\Utils;

use App\Models\Mall\Goods\Order;
use App\Models\Mall\Goods\OrderPackage;
use App\Utils\Traits\HttpClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WxMpServe
{
    use HttpClient;

    const ACCESS_TOKEN_KEY = 'wx_mp_access_token';
    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    const STABLE_ACCESS_TOKEN_KEY = 'wx_mp_stable_access_token';
    const GET_STABLE_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/stable_token';
    const TRACE_WAYBILL_URL = 'https://api.weixin.qq.com/cgi-bin/express/delivery/open_msg/trace_waybill?access_token=%s';
    const GET_PHONE_NUMBER_URL = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=%s';
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    const GET_QRCODE_URL = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s';
    const GET_LINK_URL = 'https://api.weixin.qq.com/wxa/generate_urllink?access_token=%s';
    const UPLOAD_SHIPPING_INFO_URL = 'https://api.weixin.qq.com/wxa/sec/order/upload_shipping_info?access_token=%s';

    // ========== 企业微信（WeCom） ==========
    const WECOM_ACCESS_TOKEN_KEY = 'wecom_access_token';
    const GET_WECOM_ACCESS_TOKEN_URL = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s';

    // 客户群
    const ADD_GROUP_JOIN_WAY_URL = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/add_join_way?access_token=%s';
    const GET_GROUP_CHAT_URL = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/get?access_token=%s';
    const GET_GROUP_CHAT_LIST_URL = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/list?access_token=%s';

    protected $accessToken;
    protected $stableAccessToken;
    protected $wecomAccessToken;

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $this->accessToken = Cache::has(self::ACCESS_TOKEN_KEY)
            ? Cache::get(self::ACCESS_TOKEN_KEY)
            : $this->getAccessToken();

        $this->stableAccessToken = Cache::has(self::STABLE_ACCESS_TOKEN_KEY)
            ? Cache::get(self::STABLE_ACCESS_TOKEN_KEY)
            : $this->getStableAccessToken();

//        $this->wecomAccessToken = Cache::has(self::WECOM_ACCESS_TOKEN_KEY)
//            ? Cache::get(self::WECOM_ACCESS_TOKEN_KEY)
//            : $this->getWeComAccessToken();
    }

    private function getAccessToken()
    {
        $result = $this->httpGet(sprintf(self::GET_ACCESS_TOKEN_URL, env('WX_MP_APPID'), env('WX_MP_SECRET')));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $accessToken = $result['access_token'];
        Cache::put(self::ACCESS_TOKEN_KEY, $accessToken, now()->addSeconds($result['expires_in'] - 300));
        return $accessToken;
    }

    private function getStableAccessToken()
    {
        $result = $this->httpPost(self::GET_STABLE_ACCESS_TOKEN_URL, [
            'grant_type' => 'client_credential',
            'appid' => env('WX_MP_APPID'),
            'secret' => env('WX_MP_SECRET')
        ]);
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序stable_access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $stableAccessToken = $result['access_token'];
        Cache::put(self::STABLE_ACCESS_TOKEN_KEY, $stableAccessToken, now()->addSeconds($result['expires_in'] - 300));
        return $stableAccessToken;
    }

    private function getWeComAccessToken()
    {
        $result = $this->httpGet(sprintf(
            self::GET_WECOM_ACCESS_TOKEN_URL,
            env('WECOM_CORP_ID'),
            env('WECOM_CORP_SECRET')
        ));

        if (!empty($result['errcode'])) {
            throw new \Exception('获取企业微信access_token异常：' . $result['errcode'] . $result['errmsg']);
        }

        Cache::put(
            self::WECOM_ACCESS_TOKEN_KEY,
            $result['access_token'],
            now()->addSeconds($result['expires_in'] - 300)
        );

        return $result['access_token'];
    }

    public function getWaybillToken($openid, $shipCode, $shipSn, $packageGoodsList, Order $order)
    {
        $goodsList = [];
        foreach ($packageGoodsList as $goods) {
            $goodsList[] = [
                'goods_img_url' => $goods->cover ?: '',
                'goods_name' => $goods->name,
            ];
        }
        $result = $this->httpPost(
            sprintf(self::TRACE_WAYBILL_URL, $this->stableAccessToken),
            [
                'openid' => $openid,
                'delivery_id' => $shipCode,
                'waybill_id' => $shipSn,
                'receiver_phone' => $order->mobile,
                'goods_info' => [
                    'detail_list' => $goodsList
                ],
                'trans_id' => $order->pay_id,
                'order_detail_path' => 'pages/mine/subpages/order-center/subpages/order-detail/index?id=' . $order->id,
            ],
            3
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('获取微信小程序waybillToken异常：' . $result['errcode'] . $result['errmsg']);
        }

        return $result['waybill_token'];
    }

    public function getUserPhoneNumber($code)
    {
        $result = $this->httpPost(sprintf(self::GET_PHONE_NUMBER_URL, $this->accessToken), ['code' => $code]);
        if ($result['errcode'] != 0) {
            throw new \Exception('获取微信小程序用户手机号异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result['phone_info']['purePhoneNumber'];
    }

    public function getUserSession($code)
    {
        $result = $this->httpGet(sprintf(self::GET_OPENID_URL, env('WX_MP_APPID'), env('WX_MP_SECRET'), $code));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序openid异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result;
    }

    public function getQrCode($scene, $page)
    {
        return $this->httpPost(sprintf(self::GET_QRCODE_URL, $this->accessToken), ['scene' => $scene, 'page' => $page], 1, false);
    }

    public function getURLLink($path = '', $query = '', $expireType = 1, $expireTime = 30, $expireInterval = 30, $envVersion = 'release')
    {
        return $this->httpPost(sprintf(self::GET_LINK_URL, $this->accessToken), [
            'path' => $path,
            'query' => $query,
            'expire_type' => $expireType,
            'expire_time' => $expireTime,
            'expire_interval' => $expireInterval,
            'env_version' => $envVersion
        ]);
    }

    public function uploadShippingInfo($openid, Order $order, array $orderPackageList, $isAllDelivered)
    {
        $shippingList = [];
        /** @var OrderPackage $orderPackage */
        foreach ($orderPackageList as $orderPackage) {
            $shippingList[] = [
                'tracking_no' => $orderPackage->ship_sn,
                'express_company' => $orderPackage->ship_code,
                'item_desc' => $orderPackage->goodsList()->pluck('name')->implode('，'),
                'contact' => [
                    'receiver_contact' => substr($order->mobile,0, 3) . '****' .substr($order->mobile,-4)
                ]
            ];
        }

        $result =  $this->httpPost(
            sprintf(self::UPLOAD_SHIPPING_INFO_URL, $this->accessToken),
            [
                'order_key' => [
                    'order_number_type' => 2,
                    'transaction_id' => $order->pay_id
                ],
                'logistics_type' => 1,
                'delivery_mode' => 2,
                'is_all_delivered' => $isAllDelivered,
                'shipping_list' => $shippingList,
                'upload_time' => Carbon::now()->format('Y-m-d\TH:i:s.uP'),
                'payer' => [
                    'openid' => $openid
                ]
            ],
            3
        );
        if ($result['errcode'] != 0) {
            Log::error('同步微信后台发货信息异常：' . $result['errcode'] . $result['errmsg']);
            throw new \Exception('同步微信后台发货信息异常：' . $result['errcode'] . $result['errmsg']);
        }
    }

    public function notifyNoShipment($openid, $payId, $productName, $logisticsType = 4)
    {
        $result = $this->httpPost(
            sprintf(self::UPLOAD_SHIPPING_INFO_URL, $this->accessToken),
            [
                'order_key' => [
                    'order_number_type' => 2,
                    'transaction_id' => $payId
                ],
                'logistics_type' => $logisticsType,
                'delivery_mode' => 1,
                'shipping_list' => [[
                    'item_desc' => $productName,
                ]],
                'upload_time' => Carbon::now()->format('Y-m-d\TH:i:s.uP'),
                'payer' => [
                    'openid' => $openid
                ]
            ],
            3
        );
        if ($result['errcode'] != 0) {
            Log::error('同步微信后台发货信息异常：' . $result['errcode'] . $result['errmsg']);
            throw new \Exception('同步微信后台发货信息异常：' . $result['errcode'] . $result['errmsg']);
        }
    }

    /**
     * 获取企业微信群列表
     *
     * @param int $statusFilter 0-所有群 1-离职待继承 2-离职继承完成
     * @param int $limit        每次拉取数量，最大100
     * @return array
     */
    public function getWeComGroupChatList(int $statusFilter = 0, int $limit = 100): array
    {
        $chatIdList = [];
        $cursor = '';

        do {
            $payload = [
                'status_filter' => $statusFilter,
                'limit' => $limit,
            ];

            if ($cursor) {
                $payload['cursor'] = $cursor;
            }

            $result = $this->httpPost(
                sprintf(self::GET_GROUP_CHAT_LIST_URL, $this->wecomAccessToken),
                $payload
            );

            if ($result['errcode'] != 0) {
                throw new \Exception(
                    '获取企业微信群列表失败：' . $result['errcode'] . ' ' . $result['errmsg']
                );
            }

            foreach ($result['group_chat_list'] as $item) {
                $chatIdList[] = $item['chat_id'];
            }

            $cursor = $result['next_cursor'] ?? '';
        } while (!empty($cursor));

        return $chatIdList;
    }

    /**
     * 创建企业微信群活码
     */
    public function createWeComGroupJoinWay(array $chatIdList, $scene, $remark = '小程序进群')
    {
        $result = $this->httpPost(
            sprintf(self::ADD_GROUP_JOIN_WAY_URL, $this->wecomAccessToken),
            [
                'scene' => $scene,
                'remark' => $remark,
                'chat_id_list' => $chatIdList,
            ]
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('创建企业微信群活码失败：' . $result['errcode'] . $result['errmsg']);
        }

        return [
            'config_id' => $result['config_id'],
            'qr_code'   => $result['qr_code'],
        ];
    }

    /**
     * 判断 external_userid 是否在指定企业微信群
     */
    public function isUserInWeComGroup(string $chatId, string $externalUserId): bool
    {
        $result = $this->httpPost(
            sprintf(self::GET_GROUP_CHAT_URL, $this->wecomAccessToken),
            [
                'chat_id' => $chatId
            ]
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('获取企业微信群信息失败：' . $result['errcode'] . $result['errmsg']);
        }

        foreach ($result['group_chat']['member_list'] as $member) {
            if (
                $member['type'] == 2 &&
                isset($member['external_userid']) &&
                $member['external_userid'] === $externalUserId
            ) {
                return true;
            }
        }

        return false;
    }
}
