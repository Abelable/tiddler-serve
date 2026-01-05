<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FanService;
use App\Services\Mall\Catering\MealTicketOrderService;
use App\Services\Mall\Catering\SetMealOrderService;
use App\Services\Mall\Goods\OrderService;
use App\Services\Mall\Hotel\HotelOrderService;
use App\Services\Mall\Scenic\ScenicOrderService;
use App\Services\Media\MediaService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\HotelOrderStatus;
use App\Utils\Enums\MealTicketOrderStatus;
use App\Utils\Enums\OrderStatus;
use App\Utils\Enums\ScenicOrderStatus;
use App\Utils\Enums\SetMealOrderStatus;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\UserInfoInput;
use App\Utils\TimServe;

class UserController extends Controller
{
    protected $except = ['userBaseInfo', 'userInfo', 'authorInfo', 'search', 'options', 'addTempUser'];

    public function baseInfo()
    {
        $userInfo = $this->handleBaseInfo($this->user());
        return $this->success($userInfo);
    }

    public function userBaseInfo()
    {
        $userId = $this->verifyRequiredId('userId');

        $user = UserService::getInstance()->getUserById($userId);
        if (is_null($user)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户不存在');
        }

        $userInfo = $this->handleBaseInfo($user);

        return $this->success($userInfo);
    }

    private function handleBaseInfo(User $user)
    {
        $user->setAttribute('authInfoId', $user->authInfo->id ?? 0);

        $user->loadMissing('promoterInfo');
        if ($user->promoterInfo) {
            $user->promoterInfo->makeHidden(['user_id']);
        }

        $user->setAttribute('superiorId', $user->superiorId() ?? 0);

        return $user;
    }

    public function socialStats()
    {
        $userId = $this->user()->id;

        $beLikedTimes = MediaService::getInstance()->beLikedTimes($userId);
        $beCollectedTimes = MediaService::getInstance()->beCollectedTimes($userId);
        $followedAuthorNumbers = FanService::getInstance()->followedAuthorNumber($userId);
        $fansNumber = FanService::getInstance()->fansNumber($userId);

        return $this->success([
            'beLikedTimes' => $beLikedTimes,
            'beCollectedTimes' => $beCollectedTimes,
            'followedAuthorNumbers' => $followedAuthorNumbers,
            'fansNumber' => $fansNumber,
        ]);
    }

    public function merchantInfo()
    {
        $user = $this->user();

        if ($user->scenicMerchant) {
            $data['scenicMerchantId'] = $user->scenicMerchant->id;
            $data['scenicMerchantStatus'] = $user->scenicMerchant->status;
        }
        if ($user->hotelMerchant) {
            $data['hotelMerchantId'] = $user->hotelMerchant->id;
            $data['hotelMerchantStatus'] = $user->hotelMerchant->status;
        }
        if ($user->cateringMerchant) {
            $data['cateringMerchantId'] = $user->cateringMerchant->id;
            $data['cateringMerchantStatus'] = $user->cateringMerchant->status;
        }
        if ($user->merchant) {
            $data['goodsMerchantId'] = $user->merchant->id;
            $data['goodsMerchantStatus'] = $user->merchant->status;
        }

        $data['scenicShopOptions'] = UserService::getInstance()->scenicShopOptions($user);
        $data['hotelShopOptions'] = UserService::getInstance()->hotelShopOptions($user);
        $data['cateringShopOptions'] = UserService::getInstance()->cateringShopOptions($user);
        $data['goodsShopOptions'] = UserService::getInstance()->shopOptions($user);

        return $this->success($data);
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
        // todo 及时通讯
        $timServe = TimServe::new();
        $user = UserService::getInstance()->getUserById(1);
        $timServe->updateUserInfo(1, $user->nickname, $user->avatar);
        $loginInfo = $timServe->getLoginInfo(1);
        return $this->success($loginInfo);

//        $timServe = TimServe::new();
//        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
//        $loginInfo = $timServe->getLoginInfo($this->userId());
//        return $this->success($loginInfo);
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
        $keywords = $this->verifyString('keywords');
        $options = UserService::getInstance()->getOptions($keywords, ['id', 'avatar', 'nickname', 'mobile']);
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

    public function orderTotal()
    {
        $scenicStatusList = [
            ScenicOrderStatus::CREATED,
            ScenicOrderStatus::PAID,
            ScenicOrderStatus::MERCHANT_APPROVED,
            ScenicOrderStatus::CONFIRMED,
            ScenicOrderStatus::AUTO_CONFIRMED,
            ScenicOrderStatus::ADMIN_CONFIRMED,
            ScenicOrderStatus::REFUNDING
        ];
        $scenicOrderTotal = ScenicOrderService::getInstance()->getTotal($this->userId(), $scenicStatusList);

        $hotelStatusList = [
            HotelOrderStatus::CREATED,
            HotelOrderStatus::PAID,
            HotelOrderStatus::MERCHANT_APPROVED,
            HotelOrderStatus::CONFIRMED,
            HotelOrderStatus::AUTO_CONFIRMED,
            HotelOrderStatus::ADMIN_CONFIRMED,
            HotelOrderStatus::REFUNDING
        ];
        $hotelOrderTotal = HotelOrderService::getInstance()->getTotal($this->userId(), $hotelStatusList);

        $mealTicketStatusList = [
            MealTicketOrderStatus::CREATED,
            MealTicketOrderStatus::PAID,
            MealTicketOrderStatus::MERCHANT_APPROVED,
            MealTicketOrderStatus::CONFIRMED,
            MealTicketOrderStatus::AUTO_CONFIRMED,
            MealTicketOrderStatus::ADMIN_CONFIRMED,
            MealTicketOrderStatus::REFUNDING
        ];
        $mealTicketOrderTotal = MealTicketOrderService::getInstance()->getTotal($this->userId(), $mealTicketStatusList);

        $setMealStatusList = [
            SetMealOrderStatus::CREATED,
            SetMealOrderStatus::PAID,
            SetMealOrderStatus::MERCHANT_APPROVED,
            SetMealOrderStatus::CONFIRMED,
            SetMealOrderStatus::AUTO_CONFIRMED,
            SetMealOrderStatus::ADMIN_CONFIRMED,
            SetMealOrderStatus::REFUNDING
        ];
        $setMealOrderTotal = SetMealOrderService::getInstance()->getTotal($this->userId(), $setMealStatusList);

        $goodsStatusList = [
            OrderStatus::CREATED,
            OrderStatus::PAID,
            OrderStatus::EXPORTED,
            OrderStatus::SHIPPED,
            OrderStatus::PENDING_VERIFICATION,
            OrderStatus::CONFIRMED,
            OrderStatus::AUTO_CONFIRMED,
            OrderStatus::ADMIN_CONFIRMED,
            OrderStatus::REFUNDING
        ];
        $goodsOrderTotal = OrderService::getInstance()->getTotal($this->userId(), $goodsStatusList);

        return $this->success([
            $scenicOrderTotal,
            $hotelOrderTotal,
            $mealTicketOrderTotal,
            $setMealOrderTotal,
            $goodsOrderTotal
        ]);
    }
}
