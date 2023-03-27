<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\FanService;
use App\Services\Media\MediaService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['authorInfo'];

    public function userInfo()
    {
        $user = $this->user();
        return $this->success([
            'id' => $user->id,
            'avatar' => $user->avatar,
            'nickname' => $user->nickname,
            'gender' => $user->gender,
            'mobile' => $user->mobile,
            'shopId' => $user->shop_id,
        ]);
    }

    public function timLoginInfo()
    {
        $timServe = TimServe::new();
        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
        $loginInfo = $timServe->getLoginInfo($this->userId());
        return $this->success($loginInfo);
    }

    public function authorInfo()
    {
        $authorId = $this->verifyRequiredId('authorId');

        $authorInfo = UserService::getInstance()->getUserById($authorId, ['id', 'avatar', 'nickname', 'gender', 'shop_id']);
        if (is_null($authorInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前用户不存在');
        }

        $beLikedTimes = MediaService::getInstance()->beLikedTimes($authorId);
        $beCollectedTimes = MediaService::getInstance()->beCollectedTimes($authorId);
        $followedAuthorNumbers = FanService::getInstance()->followedAuthorNumber($authorId);
        $fansNumber = FanService::getInstance()->fansNumber($authorId);

        $authorInfo['be_liked_times'] = $beLikedTimes;
        $authorInfo['be_collected_times'] = $beCollectedTimes;
        $authorInfo['followed_author_number'] = $followedAuthorNumbers;
        $authorInfo['fans_number'] = $fansNumber;

        return $this->success($authorInfo);
    }
}
