<?php

namespace App\Utils;

use App\Utils\Libs\Tim\TimRestInterface;
use App\Utils\Libs\Tim\TLSSigAPI;
use Exception;

class TimServe extends TimRestInterface
{
    #app基本信息
    protected $sdkappid = 0;
    protected $usersig = '';
    public $identifier = '';

    #开放IM https接口参数, 一般不需要修改
    protected $http_type = 'https://';
    protected $method = 'post';
    protected $im_yun_url = 'console.tim.qq.com';
    protected $version = 'v4';
    protected $contenttype = 'json';
    protected $apn = '0';

    public $sigApi;

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

    function openim_send_msg($account_id, $receiver, $text_content)
    {
        // TODO: Implement openim_send_msg() method.
    }

    function openim_send_msg_pic($account_id, $receiver, $pic_path)
    {
        // TODO: Implement openim_send_msg_pic() method.
    }

    function openim_send_msg2($account_id, $receiver, $msg_content)
    {
        // TODO: Implement openim_send_msg2() method.
    }

    function openim_batch_sendmsg($account_list, $text_content)
    {
        // TODO: Implement openim_batch_sendmsg() method.
    }

    function openim_batch_sendmsg_pic($account_list, $pic_path)
    {
        // TODO: Implement openim_batch_sendmsg_pic() method.
    }

    function openim_batch_sendmsg2($account_list, $msg_content)
    {
        // TODO: Implement openim_batch_sendmsg2() method.
    }

    function account_import($identifier, $nick, $face_url)
    {
        // TODO: Implement account_import() method.
    }

    public function register_account($identifier, $identifierType, $password)
    {
        // TODO: Implement register_account() method.
    }

    function profile_portrait_get($account_id)
    {
        // TODO: Implement profile_portrait_get() method.
    }

    function profile_portrait_get2($account_list, $tag_list)
    {
        // TODO: Implement profile_portrait_get2() method.
    }

    function profile_portrait_set($account_id, $new_name)
    {
        // TODO: Implement profile_portrait_set() method.
    }

    function profile_portrait_set2($account_id, $profile_list)
    {
        // TODO: Implement profile_portrait_set2() method.
    }

    function sns_friend_import($accout_id, $receiver)
    {
        // TODO: Implement sns_friend_import() method.
    }

    function sns_friend_delete($account_id, $frd_id)
    {
        // TODO: Implement sns_friend_delete() method.
    }

    function sns_friend_delete_all($account_id)
    {
        // TODO: Implement sns_friend_delete_all() method.
    }

    function sns_friend_check($account_id, $to_account)
    {
        // TODO: Implement sns_friend_check() method.
    }

    function sns_friend_check2($account_id, $to_account_list, $check_type)
    {
        // TODO: Implement sns_friend_check2() method.
    }

    function sns_friend_get_all($account_id)
    {
        // TODO: Implement sns_friend_get_all() method.
    }

    function sns_friend_get_all2($account_id, $tag_list)
    {
        // TODO: Implement sns_friend_get_all2() method.
    }

    function sns_friend_get_list($account_id, $frd_id)
    {
        // TODO: Implement sns_friend_get_list() method.
    }

    function sns_friend_get_list2($account_id, $frd_list, $tag_list)
    {
        // TODO: Implement sns_friend_get_list2() method.
    }

    function group_get_appid_group_list()
    {
        // TODO: Implement group_get_appid_group_list() method.
    }

    function group_get_appid_group_list2($limit, $offset, $group_type)
    {
        // TODO: Implement group_get_appid_group_list2() method.
    }

    function group_create_group($group_type, $group_name, $owner_id)
    {
        // TODO: Implement group_create_group() method.
    }

    function group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list)
    {
        // TODO: Implement group_create_group2() method.
    }

    function group_create_group3($group_type, $group_name, $owner_id, $group_id)
    {
        // TODO: Implement group_create_group3() method.
    }

    function group_change_group_owner($group_id, $new_owner)
    {
        // TODO: Implement group_change_group_owner() method.
    }

    function group_get_group_info($group_id)
    {
        // TODO: Implement group_get_group_info() method.
    }

    function group_get_group_info2($group_list, $base_info_filter, $member_info_filter, $app_define_filter)
    {
        // TODO: Implement group_get_group_info2() method.
    }

    function group_get_group_member_info($group_id, $limit, $offset)
    {
        // TODO: Implement group_get_group_member_info() method.
    }

    function group_modify_group_base_info($group_id, $group_name)
    {
        // TODO: Implement group_modify_group_base_info() method.
    }

    function group_modify_group_base_info2($group_id, $group_name, $info_set, $app_define_list)
    {
        // TODO: Implement group_modify_group_base_info2() method.
    }

    function group_add_group_member($group_id, $member_id, $silence)
    {
        // TODO: Implement group_add_group_member() method.
    }

    function group_delete_group_member($group_id, $member_id, $silence)
    {
        // TODO: Implement group_delete_group_member() method.
    }

    function group_modify_group_member_info($group_id, $account_id, $role)
    {
        // TODO: Implement group_modify_group_member_info() method.
    }

    function group_modify_group_member_info2($group_id, $account_id, $role, $msg_flag, $shutup_time)
    {
        // TODO: Implement group_modify_group_member_info2() method.
    }

    function group_destroy_group($group_id)
    {
        // TODO: Implement group_destroy_group() method.
    }

    function group_get_joined_group_list($account_id)
    {
        // TODO: Implement group_get_joined_group_list() method.
    }

    function group_get_joined_group_list2($account_id, $group_type, $base_info_filter, $self_info_filter)
    {
        // TODO: Implement group_get_joined_group_list2() method.
    }

    function group_get_role_in_group($group_id, $member_id)
    {
        // TODO: Implement group_get_role_in_group() method.
    }

    function group_forbid_send_msg($group_id, $member_id, $second)
    {
        // TODO: Implement group_forbid_send_msg() method.
    }

    function group_send_group_msg($account_id, $group_id, $text_content)
    {
        // TODO: Implement group_send_group_msg() method.
    }

    function group_send_group_msg_pic($account_id, $group_id, $pic_path)
    {
        // TODO: Implement group_send_group_msg_pic() method.
    }

    function group_send_group_msg2($account_id, $group_id, $msg_content)
    {
        // TODO: Implement group_send_group_msg2() method.
    }

    function group_send_group_system_notification($group_id, $content, $receiver_id)
    {
        // TODO: Implement group_send_group_system_notification() method.
    }

    function group_send_group_system_notification2($group_id, $content, $receiver_list)
    {
        // TODO: Implement group_send_group_system_notification2() method.
    }

    function group_import_group_member($group_id, $member_id, $role)
    {
        // TODO: Implement group_import_group_member() method.
    }

    function group_import_group_member2($group_id, $member_list)
    {
        // TODO: Implement group_import_group_member2() method.
    }

    function group_import_group_msg($group_id, $from_account, $text)
    {
        // TODO: Implement group_import_group_msg() method.
    }

    function group_import_group_msg2($group_id, $msg_list)
    {
        // TODO: Implement group_import_group_msg2() method.
    }

    function group_set_unread_msg_num($group_id, $member_account, $unread_msg_num)
    {
        // TODO: Implement group_set_unread_msg_num() method.
    }

    function comm_rest($server_name, $command, $req_body)
    {
        // TODO: Implement comm_rest() method.
    }

    function query_user_state($user_ids)
    {
        // TODO: Implement query_user_state() method.
    }
}
