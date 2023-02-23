<?php

namespace App\Utils;

use App\Utils\Libs\Tim\TimRestInterface;
use App\Utils\Libs\Tim\TLSSigAPI;
use App\Utils\Traits\HttpClient;
use Exception;
use PHPUnit\Util\Filter;

class TimServe extends TimRestInterface
{
    use HttpClient;

    const REQUEST_URL = 'https://console.tim.qq.com/v4/%s/%s?usersig=%s&identifier=%s&sdkappid=%s&contenttype=json';

    const SystemSendPraise = 100;
    const SystemSendEnter = 101;
    const SystemSendExit = 102;
    const SystemSendOwnerEnter = 103;
    const SystemSendOwnerExit = 104;
    const SystemSendOwnerLinkClose = 105;
    const SystemSendOwnerChangeLiveProducts = 106;

    protected $sdkappid = 0;
    protected $usersig = '';
    public $identifier = '';
    public $sigApi;

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
        $this->init($sdkappid, $identifier, $sigPrivateKey, $sigPublicKey);
    }

    /**
     * 初始化函数
     * @param int $sdkappid 应用的appid
     * @param string $identifier 访问接口的用户
     * @throws Exception
     */
    function init($sdkappid, $identifier, $sigPrivateKey, $sigPublicKey)
    {
        $this->sdkappid = $sdkappid;
        $this->identifier = $identifier;

        $this->sigApi = new TLSSigAPI();
        $this->sigApi->setAppid($sdkappid);
        $this->sigApi->setPrivateKey($sigPrivateKey);
        $this->sigApi->setPublicKey($sigPublicKey);
    }

    private function request($service_name, $cmd_name, $req_data)
    {
        $url = sprintf(self::REQUEST_URL, $service_name, $cmd_name, $this->usersig, $this->identifier, $this->sdkappid);
        $ret = $this->httpPost($url, $req_data);
        if ($ret['ActionStatus'] == 'OK') {
            return $ret;
        } else {
            throw new Exception('云通讯请求失败', $ret);
        }
    }

    /**
     * 独立模式根据Identifier生成UserSig的方法
     * @param int $identifier 用户账号
     * @param int $expiry_after 过期时间
     * @return string $out 返回的签名字符串
     */
    public function generate_user_sig($identifier, $expiry_after)
    {
        try {
            $sig = $this->sigApi->genSig($identifier, $expiry_after);
            $this->usersig = $sig;
            return $sig;
        } catch (Exception $e) {
            return null;
        }
    }

    public function set_user_sig($usr_sig)
    {
        $this->usersig = $usr_sig;
        return true;
    }

    public function openim_send_msg($account_id, $receiver, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->openim_send_msg2($account_id, $receiver, $msg_content);
    }

    public function openim_send_msg_pic($account_id, $receiver, $pic_path)
    {
        // TODO: Implement openim_send_msg_pic() method.
    }

    public function openim_send_msg2($account_id, $receiver, $msg_content)
    {
        $msg = [
            'To_Account' => $receiver,
            'MsgSeq' => rand(1, 65535),
            'MsgRandom' => rand(1, 65535),
            'MsgTimeStamp' => time(),
            'MsgBody' => $msg_content,
            'From_Account' => $account_id
        ];
        return $this->request('openim', 'sendmsg', json_encode($msg));
    }

    public function openim_batch_sendmsg($account_list, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->openim_batch_sendmsg2($account_list, $msg_content);
    }

    public function openim_batch_sendmsg_pic($account_list, $pic_path)
    {
        // TODO: Implement openim_batch_sendmsg_pic() method.
    }

    public function openim_batch_sendmsg2($account_list, $msg_content)
    {
        $msg = [
            'To_Account' => $account_list,
            'MsgRandom' => rand(1, 65535),
            'MsgBody' => $msg_content,
        ];
        return $this->request('openim', 'batchsendmsg', json_encode($msg));
    }

    public function account_import($identifier, $nick, $face_url)
    {
        $msg = [
            'Identifier' => (string)$identifier,
            'Nick' => $nick,
            'FaceUrl' => $face_url,
        ];
        return $this->request('im_open_login_svc', 'account_import', json_encode($msg));
    }

    public function register_account($identifier, $identifierType, $password)
    {
        $msg = [
            'Identifier' => $identifier,
            'IdentifierType' => $identifierType,
            'Password' => $password,
        ];
        return $this->request('registration_service', 'register_account_v1', json_encode($msg));
    }

    public function profile_portrait_get($account_id)
    {
        $account_list = [];
        $account_list[] = $account_id;
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_Profile_IM_AllowType"
        ];
        return $this->profile_portrait_get2($account_list, $tag_list);
    }

    public function profile_portrait_get2($account_list, $tag_list)
    {
        $msg = [
            'From_Account' => $this->identifier,
            'To_Account' => $account_list,
            'TagList' => $tag_list,
            'LastStandardSequence' => 0
        ];
        return $this->request('profile', 'portrait_get', json_encode($msg));
    }

    public function profile_portrait_set($account_id, $new_name)
    {
        $profile_list = [];
        $profile_nick = [
            "Tag" => "Tag_Profile_IM_Nick",
            "Value" => $new_name
        ];
        $profile_list[] = $profile_nick;
        return $this->profile_portrait_set2($account_id, $profile_list);
    }

    public function profile_portrait_set2($account_id, $profile_list)
    {
        $msg = [
            'From_Account' => (string)$account_id,
            'ProfileItem' => $profile_list
        ];
        return $this->request('profile', 'portrait_set', json_encode($msg));
    }

    public function sns_friend_import($account_id, $receiver)
    {
        $msg = [
            'From_Account' => $account_id,
            'AddFriendItem' => []
        ];
        $receiver_arr = [
            'To_Account' => $receiver,
            'Remark' => "",
            'AddSource' => "AddSource_Type_Unknow",
            'AddWording' => ""
        ];
        $msg['AddFriendItem'][] = $receiver_arr;
        return $this->request('sns', 'friend_import', json_encode($msg));
    }

    public function sns_friend_delete($account_id, $frd_id)
    {
        $frd_list = [];
        $frd_list[] = $frd_id;
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $frd_list,
            'DeleteType' => "Delete_Type_Both"
        ];
        return $this->request('sns', 'friend_delete', json_encode($msg));
    }

    public function sns_friend_delete_all($account_id)
    {
        $msg = [
            'From_Account' => $account_id,
        ];
        return $this->request('sns', 'friend_delete_all', json_encode($msg));
    }

    public function sns_friend_check($account_id, $to_account)
    {
        $to_account_list = [];
        $to_account_list[] = $to_account;
        return $this->sns_friend_check2($account_id, $to_account_list, "CheckResult_Type_Both");
    }

    public function sns_friend_check2($account_id, $to_account_list, $check_type)
    {
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $to_account_list,
            'CheckType' => $check_type
        ];
        return $this->request('sns', 'friend_check', json_encode($msg));
    }

    public function sns_friend_get_all($account_id)
    {
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_SNS_IM_Remark"
        ];
        return $this->sns_friend_get_all2($account_id, $tag_list);
    }

    public function sns_friend_get_all2($account_id, $tag_list)
    {
        $msg = [
            'From_Account' => $account_id,
            'TimeStamp' => 0,
            'TagList' => $tag_list,
            'LastStandardSequence' => 1,
        ];
        return $this->request('sns', 'friend_get_all', json_encode($msg));
    }

    public function sns_friend_get_list($account_id, $frd_id)
    {
        $frd_list = [];
        $frd_list[] = $frd_id;
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_SNS_IM_Remark"
        ];
        return $this->sns_friend_get_list2($account_id, $frd_list, $tag_list);
    }

    public function sns_friend_get_list2($account_id, $frd_list, $tag_list)
    {
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $frd_list,
            'TagList' => $tag_list,
        ];
        return $this->request('sns', 'friend_get_list', json_encode($msg));
    }

    public function group_get_appid_group_list()
    {
        return $this->group_get_appid_group_list2(50, null, null);
    }

    public function group_get_appid_group_list2($limit, $offset, $group_type)
    {
        $msg = array(
            'Limit' => $limit,
            'Offset' => $offset,
            'GroupType' => $group_type
        );
        return $this->request('group_open_http_svc', 'get_appid_group_list', json_encode($msg));
    }

    public function group_create_group($group_type, $group_name, $owner_id)
    {
        $info_set = [
            'group_id' => null,
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => 500,
        ];
        $mem_list = [];
        return $this->group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list);
    }

    public function group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list)
    {
        $msg = [
            'Type' => $group_type,
            'Name' => $group_name,
            'Owner_Account' => $owner_id,
            'GroupId' => $info_set['group_id'],
            'Introduction' => $info_set['introduction'],
            'Notification' => $info_set['notification'],
            'FaceUrl' => $info_set['face_url'],
            'MaxMemberCount' => $info_set['max_member_num'],
            'MemberList' => $mem_list
        ];
        return $this->request('group_open_http_svc', 'create_group', json_encode($msg));
    }

    public function group_create_group3($group_type, $group_name, $owner_id, $group_id)
    {
        $info_set = [
            'group_id' => $group_id,
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => 500,
        ];
        $mem_list = [];
        return $this->group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list);
    }

    public function group_change_group_owner($group_id, $new_owner)
    {
        $msg = [
            'GroupId' => $group_id,
            'NewOwner_Account' => $new_owner
        ];
        return $this->request('group_open_http_svc', 'change_group_owner', json_encode($msg));
    }

    public function group_get_group_info($group_id)
    {
        $group_list = [];
        $group_list[] = $group_id;

        $base_info_filter = [
            "Type",               //群类型(包括Public(公开群), Private(私密群), ChatRoom(聊天室))
            "Name",               //群名称
            "Introduction",       //群简介
            "Notification",       //群公告
            "FaceUrl",            //群头像url地址
            "CreateTime",         //群组创建时间
            "Owner_Account",      //群主id
            "LastInfoTime",       //最后一次系统通知时间
            "LastMsgTime",        //最后一次消息发送时间
            "MemberNum",          //群组当前成员数目
            "MaxMemberNum",       //群组内最大成员数目
            "ApplyJoinOption"     //加群处理方式(比如FreeAccess 自由加入)
        ];
        $member_info_filter = [
            "Account",         // 成员ID
            "Role",            // 成员身份
            "JoinTime",        // 成员加入时间
            "LastSendMsgTime", // 该成员最后一次发送消息时间
            "ShutUpUntil"      // 该成员被禁言直到某时间
        ];
        $app_define_filter = [
            "GroupTestData1",  //自定义数据
        ];

        return $this->group_get_group_info2($group_list, $base_info_filter, $member_info_filter, $app_define_filter);
    }

    public function group_get_group_info2($group_list, $base_info_filter, $member_info_filter, $app_define_filter)
    {
        $filter = new Filter();
        // $filter->GroupBaseInfoFilter = $base_info_filter;
        // $filter->MemberInfoFilter = $member_info_filter;
        // $filter->AppDefinedDataFilter_Group = $app_define_filter;
        $msg = [
            'GroupIdList' => $group_list,
            'ResponseFilter' => $filter
        ];
        return $this->request('group_open_http_svc', 'get_group_info', json_encode($msg));
    }

    public function group_get_group_member_info($group_id, $limit, $offset)
    {
        $msg = [
            "GroupId" => $group_id,
            "Limit" => $limit,
            "Offset" => $offset
        ];
        #将消息序列化为json串
        $req_data = json_encode($msg);
        return $this->request('group_open_http_svc', 'get_group_member_info', json_encode($msg));
    }

    public function group_modify_group_base_info($group_id, $group_name)
    {
        $info_set = [
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => null,
            //	'apply_join' => "NeedPermission"
        ];
        $app_define_list = [];
        return $this->group_modify_group_base_info2($group_id, $group_name, $info_set, $app_define_list);
    }

    public function group_modify_group_base_info2($group_id, $group_name, $info_set, $app_define_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "Name" => $group_name,
            "Introduction" => $info_set['introduction'],
            "Notification" => $info_set['notification'],
            "FaceUrl" => $info_set['face_url'],
            "MaxMemberNum" => $info_set['max_member_num'],
            //	"ApplyJoinOption" => $info_set['apply_join'],
            "AppDefinedData" => $app_define_list
        ];
        return $this->request('group_open_http_svc', 'modify_group_base_info', json_encode($msg));
    }

    public function group_add_group_member($group_id, $member_id, $silence)
    {
        $mem_list = [];
        $mem_elem = [
            "Member_Account" => $member_id
        ];
        $mem_list[] = $mem_elem;
        $msg = [
            "GroupId" => $group_id,
            "MemberList" => $mem_list,
            "Silence" => $silence
        ];
        return $this->request('group_open_http_svc', 'add_group_member', json_encode($msg));
    }

    public function group_delete_group_member($group_id, $member_id, $silence)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "MemberToDel_Account" => $mem_list,
            "Silence" => $silence
        ];
        return $this->request('group_open_http_svc', 'delete_group_member', json_encode($msg));
    }

    public function group_modify_group_member_info($group_id, $account_id, $role)
    {
        return $this->group_modify_group_member_info2($group_id, $account_id, $role, "AcceptAndNotify", 0);
    }

    public function group_modify_group_member_info2($group_id, $account_id, $role, $msg_flag, $shutup_time)
    {
        $msg = [
            "GroupId" => $group_id,
            "Member_Account" => $account_id,
            "Role" => $role
        ];
        return $this->request('group_open_http_svc', 'modify_group_member_info', json_encode($msg));
    }

    public function group_destroy_group($group_id)
    {
        $msg = [
            "GroupId" => $group_id,
        ];
        return $this->request('group_open_http_svc', 'destroy_group', json_encode($msg));
    }

    public function group_get_joined_group_list($account_id)
    {
        $base_info_filter = [
            "Type",               //群类型(包括Public(公开群), Private(私密群), ChatRoom(聊天室))
            "Name",               //群名称
            "Introduction",       //群简介
            "Notification",       //群公告
            "FaceUrl",            //群头像url地址
            "CreateTime",         //群组创建时间
            "Owner_Account",      //群主id
            "LastInfoTime",       //最后一次系统通知时间
            "LastMsgTime",        //最后一次消息发送时间
            "MemberNum",          //群组当前成员数目
            "MaxMemberNum",       //群组内最大成员数目
            "ApplyJoinOption"     //申请加群处理方式(比如FreeAccess 自由加入, NeedPermission 需要同意)
        ];

        $self_info_filter = [
            "Role",            //群内身份(Amin/Member)
            "JoinTime",        //入群时间
            "MsgFlag",         //消息屏蔽类型
            "UnreadMsgNum"     //未读消息数量
        ];

        return $this->group_get_joined_group_list2($account_id, null, $base_info_filter, $self_info_filter);
    }

    public function group_get_joined_group_list2($account_id, $group_type, $base_info_filter, $self_info_filter)
    {
        $filter = new Filter();
        // $filter->GroupBaseInfoFilter = $base_info_filter;
        // $filter->SelfInfoFilter = $self_info_filter;
        $msg = array(
            "Member_Account" => $account_id,
            "ResponseFilter" => $filter
        );
        return $this->request('group_open_http_svc', 'get_joined_group_list', json_encode($msg));
    }

    public function group_get_role_in_group($group_id, $member_id)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "User_Account" => $mem_list,
        ];
        return $this->request('group_open_http_svc', 'get_role_in_group', json_encode($msg));
    }

    public function group_forbid_send_msg($group_id, $member_id, $second)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "Members_Account" => $mem_list,
            "ShutUpTime" => $second
        ];
        return $this->request('group_open_http_svc', 'forbid_send_msg', json_encode($msg));
    }

    public function group_send_group_msg($account_id, $group_id, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->group_send_group_msg2($account_id, $group_id, $msg_content);
    }

    public function group_send_group_msg_pic($account_id, $group_id, $pic_path)
    {
        // TODO: Implement group_send_group_msg_pic() method.
    }

    public function group_send_group_msg2($account_id, $group_id, $msg_content)
    {
        $msg = [
            "GroupId" => $group_id,
            "From_Account" => $account_id,
            "Random" => rand(1, 65535),
            "MsgBody" => $msg_content
        ];
        return $this->request('group_open_http_svc', 'send_group_msg', json_encode($msg));
    }

    public function group_send_group_system_notification($group_id, $content, $receiver_id)
    {
        $receiver_list = [];
        if($receiver_id != null){
            $receiver_list[] = $receiver_id;
        }
        return $this->group_send_group_system_notification2($group_id, $content, $receiver_list);
    }

    public function group_send_group_system_notification2($group_id, $content, $receiver_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "ToMembers_Account" => $receiver_list,
            "Content" => $content
        ];
        return $this->request('group_open_http_svc', 'send_group_system_notification', json_encode($msg));
    }

    public function group_import_group_member($group_id, $member_id, $role)
    {
        $member_list = [];
        $member_elem = [
            "Member_Account" => $member_id,
            "Role" => $role
        ];
        $member_list[] = $member_elem;
        return $this->group_import_group_member2($group_id, $member_list);
    }

    public function group_import_group_member2($group_id, $member_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "MemberList" => $member_list,
        ];
        return $this->request('group_open_http_svc', 'import_group_member', json_encode($msg));
    }

    public function group_import_group_msg($group_id, $from_account, $text)
    {
        $msg_content = [
            "Text" => $text
        ];
        $msg_body_elem = [
            "MsgType" => "TIMTextElem",
            "MsgContent" => $msg_content,
        ];
        $msg_body_list = [];
        $msg_body_list[] = $msg_body_elem;

        //构造MsgList的一个元素
        $msg_list_elem = [
            "From_Account" => $from_account,
            "SendTime" => time(),
            "Random" => rand(1, 65535),
            "MsgBody" => $msg_body_list
        ];

        //构造MsgList
        $msg_list = [];
        $msg_list[] = $msg_list_elem;

        return $this->group_import_group_msg2($group_id, $msg_list);
    }

    public function group_import_group_msg2($group_id, $msg_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "MsgList" => $msg_list,
        ];
        return $this->request('group_open_http_svc', 'import_group_msg', json_encode($msg));
    }

    public function group_set_unread_msg_num($group_id, $member_account, $unread_msg_num)
    {
        $msg = [
            "GroupId" => $group_id,
            "Member_Account" => $member_account,
            "UnreadMsgNum" => (int)$unread_msg_num
        ];
        return $this->request('group_open_http_svc', 'set_unread_msg_num', json_encode($msg));
    }

    public function comm_rest($server_name, $command, $req_body)
    {
        return $this->request($server_name, $command, json_encode($req_body));
    }

    public function query_user_state($user_ids)
    {
        $msg = [
            'To_Account' => $user_ids,
        ];
        return $this->request('openim', 'querystate', json_encode($msg));
    }
}
