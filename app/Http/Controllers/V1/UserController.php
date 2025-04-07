<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FanService;
use App\Services\Media\MediaService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['superiorInfo', 'authorInfo', 'search', 'addTempUser'];

    public function userInfo()
    {
        $user = $this->user();
        $beLikedTimes = MediaService::getInstance()->beLikedTimes($this->userId());
        $beCollectedTimes = MediaService::getInstance()->beCollectedTimes($this->userId());
        $followedAuthorNumbers = FanService::getInstance()->followedAuthorNumber($this->userId());
        $fansNumber = FanService::getInstance()->fansNumber($this->userId());

        $user['promoterId'] = $user->promoterInfo->id ?? 0;
        $user['level'] = $user->promoterInfo->level ?? 0;
        $user['superiorId'] = $user->superiorId() ?? 0;

        $user['authInfoId'] = $user->authInfo->id ?? 0;

        $user['merchantId'] = $user->merchant->id ?? 0;
        $user['scenicProviderId'] = $user->scenicProvider->id ?? 0;
        $user['hotelProviderId'] = $user->hotelProvider->id ?? 0;
        $user['cateringProviderId'] = $user->cateringProvider->id ?? 0;

        $user['be_liked_times'] = $beLikedTimes;
        $user['be_collected_times'] = $beCollectedTimes;
        $user['followed_author_number'] = $followedAuthorNumbers;
        $user['fans_number'] = $fansNumber;

        unset($user->openid);
        unset($user->promoterInfo);
        unset($user->authInfo);
        unset($user->merchant);
        unset($user->scenicProvider);
        unset($user->hotelProvider);
        unset($user->cateringProvider);
        unset($user->created_at);
        unset($user->updated_at);

        return $this->success($user);
    }

    public function updateUserInfo()
    {
        /** @var UserInfoInput $input */
        $input = UserInfoInput::new();
        $user = $this->user();

        if (!empty($input->bg)) {
            $user->bg = $input->bg;
        }
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        if (!empty($input->birthday)) {
            $user->birthday = $input->birthday;
        }
        if (!empty($input->constellation)) {
            $user->constellation = $input->constellation;
        }
        if (!empty($input->signature)) {
            $user->signature = $input->signature;
        }

        $user->save();

        return $this->success();
    }

    public function timLoginInfo()
    {
        $timServe = TimServe::new();
        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
        $loginInfo = $timServe->getLoginInfo($this->userId());
        return $this->success($loginInfo);
    }

    public function superiorInfo()
    {
        $superiorId = $this->verifyRequiredId('superiorId');

        $superiorInfo = UserService::getInstance()->getUserById($superiorId, ['id', 'mobile', 'avatar', 'nickname', 'gender', 'wx_qrcode', 'signature']);
        if (is_null($superiorInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户上级不存在');
        }

        return $this->success($superiorInfo);
    }

    public function authorInfo()
    {
        $authorId = $this->verifyRequiredId('authorId');

        $authorInfo = UserService::getInstance()->getUserById($authorId, ['id', 'avatar', 'nickname', 'bg', 'gender', 'signature']);
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

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        $followUserIds = $this->isLogin() ? FanService::getInstance()->followAuthorIds($this->userId()) : [];

        $page = UserService::getInstance()->searchPage($input);
        $list = collect($page->items())->map(function (User $user) use ($followUserIds) {
            $user['isFollow'] = $this->isLogin() && in_array($user->id, $followUserIds);
            $user['followedUsersNumber'] = $user->followedUsersNumber();
            $user['fansNumber'] = $user->fansNumber();
            return $user;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function addTempUser()
    {
        $avatar = $this->verifyRequiredString('avatar');
        $nickname = $this->verifyRequiredString('nickname');

        $user = UserService::getInstance()->getUserByNickname($nickname);
        if (is_null($user)) {
            $user = User::new();
            $user->avatar = $avatar;
            $user->nickname = $nickname;
            $user->save();
        }
        return $this->success($user->id);
    }
}
