<?php

namespace App\Utils;

use App\Utils\Libs\Tim\TimRestAPI;

class TimServe
{
    const SystemSendPraise = 100;
    const SystemSendEnter = 101;
    const SystemSendExit = 102;
    const SystemSendOwnerEnter = 103;
    const SystemSendOwnerExit = 104;
    const SystemSendOwnerLinkClose = 105;
    const SystemSendOwnerChangeLiveProducts = 106;

    public TimRestAPI $api;

    /**
     * 响应码信息
     */
    public static $code = [
        '0' => ['verify' => true, 'msg' => '成功'],
        '200' => ['verify' => true, 'msg' => '成功'],
        '10002' => ['verify' => false, 'msg' => '系统错误'],
        '10004' => ['verify' => false, 'msg' => '参数非法'],
        '10007' => ['verify' => false, 'msg' => '无权限请求'],
        '10010' => ['verify' => false, 'msg' => '群不存在或已解散'],
        '10015' => ['verify' => false, 'msg' => '群组id非法'],
        '10017' => ['verify' => false, 'msg' => '用户被禁言'],
        '80001' => ['verify' => false, 'msg' => '文本含有敏感数据'],
        '80002' => ['verify' => false, 'msg' => '消息内容过长']
    ];

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $sdkappid = env('TIM_APPID');
        $identifier = env('TIM_ADMIN');
        $sigPrivateKey = storage_path('app/tim/private_key.pem');
        $sigPublicKey = storage_path('app/tim/public_key.pem');

        $api = new TimRestAPI();
        $api->init($sdkappid, $identifier, $sigPrivateKey, $sigPublicKey);
        $api->generate_user_sig($identifier, 604800);

        $this->api = $api;
    }

    public function updateUserInfo($userId, $nickname, $avatar)
    {
        $profileList = [
            'profile_nick' => [
                "Tag" => "Tag_Profile_IM_Nick",
                "Value" => $nickname,
            ],
            'profile_img' => [
                "Tag" => "Tag_Profile_IM_Image",
                "Value" => $avatar,
            ]
        ];
        $ret = $this->api->profile_portrait_set2($userId, $profileList);
        if ($ret['ErrorCode'] != 0) {
            $ret = $this->api->account_import($userId, $nickname, $avatar);
            if ($ret['ErrorCode'] != 0) {
                throw new \Exception('云通讯用户信息更新失败', $ret, 312);
            }
        }
    }

    public function getLoginInfo($userId)
    {
        $ret = $this->api->generate_user_sig($userId, 31536000);
        if ($ret['ActionStatus'] != 'OK') {
            throw new \Exception('云通讯用户签名生成失败', $ret, 312);
        }
        return [
            'sdkAppId' => env('TIM_APPID'),
            'userSig' => $ret['userSig']
        ];
    }

    public function createChatGroup($roomId)
    {
        $ret = $this->api->group_create_group3('AVChatRoom', '' . $roomId, env('TIM_ADMIN'), $roomId);
        if ($ret['ActionStatus'] != 'OK') {
            throw new \Exception('云通讯群组创建失败', $ret, 312);
        }
        return $ret['GroupId'];
    }

    public function destroyChatGroup($groupId)
    {
        $ret = $this->api->group_destroy_group($groupId);
        if ($ret['ActionStatus'] != 'OK') {
            throw new \Exception('云通讯群组删除失败', $ret, 312);
        }
    }

    public function sendGroupSystemNotification($groupId, $content)
    {
        $ret = $this->api->group_send_group_system_notification($groupId, $content, null);
        if ($ret['ActionStatus'] != 'OK') {
            throw new \Exception('云通讯群消息发送失败', $ret, 312);
        }
        return $ret;
    }
}
