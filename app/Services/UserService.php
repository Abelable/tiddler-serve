<?php

namespace App\Services;

use App\Models\Catering\CateringShop;
use App\Models\Catering\CateringShopManager;
use App\Models\HotelShop;
use App\Models\HotelShopManager;
use App\Models\ScenicShop;
use App\Models\ScenicShopManager;
use App\Models\Shop;
use App\Models\ShopManager;
use App\Models\User;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\CateringShopService;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\WxMpRegisterInput;

class UserService extends BaseService
{
    public function register($openid, WxMpRegisterInput $input)
    {
        $user = User::new();
        $user->openid = $openid;
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        $user->mobile = $input->mobile;
        $user->save();
        return $user;
    }

    public function getByOpenid($openid)
    {
        return User::query()->where('openid', $openid)->first();
    }

    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->first();
    }

    public function getUserPage(UserPageInput $input, $userIds = null, $columns = ['*'])
    {
        $query = User::query();
        if (!is_null($userIds)) {
            $query = $query->whereIn('id', $userIds);
        }
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', 'like', "%$input->nickname%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserList($columns = ['*'])
    {
        return User::query()->get($columns);
    }

    public function getNormalList($promoterIds, $columns = ['*'])
    {
        return User::query()->whereNotIn('id', $promoterIds)->get($columns);
    }

    public function getUserById($id, $columns = ['*'])
    {
        return User::query()->find($id, $columns);
    }

    public function getUserByNickname($nickname, $columns = ['*'])
    {
        return User::query()->where('nickname', $nickname)->first($columns);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return User::query()->whereIn('id', $ids)->get($columns);
    }

    public function getPageByUserIds(array $userIds, SearchPageInput $input, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function searchPage(SearchPageInput $input)
    {
        return User::search($input->keywords)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function searchList($keywords)
    {
        return User::search($keywords)->get();
    }

    public function getOptions($keywords, $columns = ['*'])
    {
        $query = User::query();
        if (!empty($keywords)) {
            $query = $query->where(function($query) use ($keywords) {
                $query->where('nickname', 'like', "%$keywords%")
                    ->orWhere('mobile', $keywords);
            });
        }
        return $query->get($columns);
    }

    public function searchUserIds($keywords)
    {
        $list = $this->searchList($keywords);
        return $list->pluck('id')->toArray();
    }

    public function searchListByUserIds(array $userIds, $keywords, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->where(function($query) use ($keywords) {
                $query->where('nickname', 'like', "%$keywords%")
                    ->orWhere('mobile', $keywords);
            })->get($columns);
    }

    public function scenicShopOptions(User $user) {
        $scenicShopId = $user->scenicShop->id ?? 0;

        $scenicShopManagerList = ScenicShopManagerService::getInstance()->getManagerListByUserId($user->id);
        $shopIds = $scenicShopManagerList->pluck('shop_id')->toArray();

        if ($scenicShopId != 0 && !in_array($scenicShopId, $shopIds)) {
            $shopIds[] = $scenicShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $scenicShopList = ScenicShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $scenicShopOptions = $scenicShopManagerList->map(function (ScenicShopManager $scenicShopManager) use ($scenicShopList) {
            /** @var ScenicShop $shopInfo */
            $shopInfo = $scenicShopList->get($scenicShopManager->shop_id);
            return [
                'id' => $scenicShopManager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $scenicShopManager->role_id,
            ];
        });

        if ($scenicShopId != 0) {
            $shopInfo = $scenicShopList->get($scenicShopId);
            $scenicShopOptions->prepend([
                'id' => $scenicShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 0,
            ]);
        }

        return $scenicShopOptions;
    }

    public function hotelShopOptions(User $user) {
        $hotelShopId = $user->hotelShop->id ?? 0;

        $hotelShopManagerList = HotelShopManagerService::getInstance()->getManagerListByUserId($user->id);
        $shopIds = $hotelShopManagerList->pluck('shop_id')->toArray();

        if ($hotelShopId != 0 && !in_array($hotelShopId, $shopIds)) {
            $shopIds[] = $hotelShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $hotelShopList = HotelShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $hotelShopOptions = $hotelShopManagerList->map(function (HotelShopManager $manager) use ($hotelShopList) {
            /** @var HotelShop $shopInfo */
            $shopInfo = $hotelShopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($hotelShopId != 0) {
            $shopInfo = $hotelShopList->get($hotelShopId);
            $hotelShopOptions->prepend([
                'id' => $hotelShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 0,
            ]);
        }

        return $hotelShopOptions;
    }

    public function cateringShopOptions(User $user) {
        $cateringShopId = $user->cateringShop->id ?? 0;

        $cateringShopManagerList = CateringShopManagerService::getInstance()->getManagerListByUserId($user->id);
        $shopIds = $cateringShopManagerList->pluck('shop_id')->toArray();

        if ($cateringShopId != 0 && !in_array($cateringShopId, $shopIds)) {
            $shopIds[] = $cateringShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $cateringShopList = CateringShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $cateringShopOptions = $cateringShopManagerList->map(function (CateringShopManager $manager) use ($cateringShopList) {
            /** @var CateringShop $shopInfo */
            $shopInfo = $cateringShopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($cateringShopId != 0) {
            $shopInfo = $cateringShopList->get($cateringShopId);
            $cateringShopOptions->prepend([
                'id' => $cateringShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 0,
            ]);
        }

        return $cateringShopOptions;
    }

    public function shopOptions(User $user) {
        $shopId = $user->shop->id ?? 0;

        $shopManagerList = ShopManagerService::getInstance()->getManagerListByUserId($user->id);
        $shopIds = $shopManagerList->pluck('shop_id')->toArray();

        if ($shopId != 0 && !in_array($shopId, $shopIds)) {
            $shopIds[] = $shopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $shopOptions = $shopManagerList->map(function (ShopManager $manager) use ($shopList) {
            /** @var Shop $shopInfo */
            $shopInfo = $shopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo'   => $shopInfo->logo ?? '',
                'name'   => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($shopId != 0) {
            $shopInfo = $shopList->get($shopId);
            $shopOptions->prepend([
                'id' => $shopId,
                'logo'   => $shopInfo->logo ?? '',
                'name'   => $shopInfo->name ?? '',
                'roleId' => 0,
            ]);
        }

        return $shopOptions;
    }
}
