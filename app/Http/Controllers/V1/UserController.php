<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FanService;
use App\Services\HotelShopManagerService;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Media\MediaService;
use App\Services\ScenicShopManagerService;
use App\Services\ShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['userInfo', 'authorInfo', 'search', 'options', 'addTempUser'];

    public function myInfo()
    {
        $userInfo = $this->handelUserInfo($this->user());
        return $this->success($userInfo);
    }

    public function userInfo()
    {
        $userId = $this->verifyRequiredId('userId');

        $user = UserService::getInstance()->getUserById($userId);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户不存在');
        }

        $userInfo = $this->handelUserInfo($user);

        return $this->success($userInfo);
    }

    private function handelUserInfo(User $user)
    {
        $beLikedTimes = MediaService::getInstance()->beLikedTimes($user->id);
        $beCollectedTimes = MediaService::getInstance()->beCollectedTimes($user->id);
        $followedAuthorNumbers = FanService::getInstance()->followedAuthorNumber($user->id);
        $fansNumber = FanService::getInstance()->fansNumber($user->id);
        $user['beLikedTimes'] = $beLikedTimes;
        $user['beCollectedTimes'] = $beCollectedTimes;
        $user['followedAuthorNumber'] = $followedAuthorNumbers;
        $user['fansNumber'] = $fansNumber;

        $promoterInfo = $user->promoterInfo;
        $user['promoterInfo'] = $promoterInfo ? [
            'id' => $promoterInfo->id,
            'status' => $promoterInfo->status,
            'level' => $promoterInfo->level,
            'subUserCount' => $promoterInfo->sub_user_number,
            'subPromoterCount' => $promoterInfo->sub_promoter_number,
            'selfCommissionSum' => $promoterInfo->self_commission_sum,
            'shareCommissionSum' => $promoterInfo->share_commission_sum,
            'teamCommissionSum' => $promoterInfo->team_commission_sum,
            'expirationTime' => $promoterInfo->expiration_time,
        ] : null;

        $user['superiorId'] = $user->superiorId() ?? 0;

        $user['authInfoId'] = $user->authInfo->id ?? 0;

        $scenicShopId = $user->scenicShop->id ?? 0;
        $user['scenicShopId'] = $scenicShopId;
        if ($scenicShopId != 0) {
            $scenicShopManagerList = ScenicShopManagerService::getInstance()
                ->getManagerList($scenicShopId, ['id', 'user_id', 'role_id']);
            $user['scenicShopManagerList'] = $scenicShopManagerList;
        }

        $hotelShopId = $user->hotelShop->id ?? 0;
        $user['hotelShopId'] = $hotelShopId;
        if ($hotelShopId != 0) {
            $hotelShopManagerList = HotelShopManagerService::getInstance()
                ->getManagerList($hotelShopId, ['id', 'user_id', 'role_id']);
            $user['hotelShopManagerList'] = $hotelShopManagerList;
        }

        $cateringShopId = $user->cateringShop->id ?? 0;
        $user['cateringShopId'] = $cateringShopId;
        if ($cateringShopId != 0) {
            $cateringShopManagerList = CateringShopManagerService::getInstance()
                ->getManagerList($cateringShopId, ['id', 'user_id', 'role_id']);
            $user['cateringShopManagerList'] = $cateringShopManagerList;
        }

        $shopId = $user->shop->id ?? 0;
        $user['shopId'] = $shopId;
        if ($shopId != 0) {
            $shopManagerList = ShopManagerService::getInstance()
                ->getManagerList($shopId, ['id', 'user_id', 'role_id']);
            $user['shopManagerList'] = $shopManagerList;
        }

        unset($user->openid);
        unset($user->authInfo);

        unset($user->scenicMerchant);
        unset($user->scenicShop);
        unset($user->hotelMerchant);
        unset($user->hotelShop);
        unset($user->cateringMerchant);
        unset($user->cateringShop);
        unset($user->merchant);
        unset($user->shop);

        unset($user->created_at);
        unset($user->updated_at);

        return $user;
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

    public function options()
    {
        $nickname = $this->verifyString('nickname');
        $options = UserService::getInstance()->getOptions($nickname);
        return $this->success($options);
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
            $user->mobile = $user::generateMobile();
            $user->save();
        }
        return $this->success($user->id);
    }

    public function supplyUserMobile()
    {
        $list = User::query()->get();
        $list->map(function (User $user) {
            if (!$user->mobile) {
                $user->mobile = $user::generateMobile();
                $user->save();
            }
        });
        return $this->success();
    }
}
