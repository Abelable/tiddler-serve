<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Mall\CommissionService;
use App\Services\Mall\Goods\OrderService;
use App\Services\Promoter\PromoterChangeLogService;
use App\Services\Promoter\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\Inputs\SearchPageInput;

class PromoterController extends Controller
{
    public function achievement()
    {
        $promoterInfo = $this->user()->promoterInfo;

        $levelChangeTime = PromoterChangeLogService::getInstance()
            ->getLevelChangeLog($promoterInfo->id)
            ->created_at;
        $achievement = CommissionService::getInstance()->getUserAchievement($this->userId(), $levelChangeTime);

        return $this->success((float) $achievement);
    }

    public function customerData()
    {
        $todayNewCustomerCount = RelationService::getInstance()->getTodayCountBySuperiorId($this->userId());

        $customerIds = RelationService::getInstance()
            ->getListBySuperiorId($this->userId())
            ->pluck('user_id')
            ->toArray();
        $todayOrderingCustomerCount = OrderService::getInstance()->getTodayOrderingUserCountByUserIds($customerIds);

        $customerTotalCount = RelationService::getInstance()->getCountBySuperiorId($this->userId());

        return $this->success([
            'todayNewCount' => $todayNewCustomerCount,
            'todayOrderingCount' => $todayOrderingCustomerCount,
            'totalCount' => $customerTotalCount
        ]);
    }

    public function todayNewCustomerList()
    {
        $todayNewCustomerIds = RelationService::getInstance()
            ->getTodayListBySuperiorId($this->userId())
            ->pluck('user_id')
            ->toArray();
        $customerList = UserService::getInstance()->getListByIds($todayNewCustomerIds);
        $list = $this->handleCustomerList($todayNewCustomerIds, $customerList);
        return $this->success($list);
    }

    public function todayOrderingCustomerList()
    {
        $totalCustomerIds = RelationService::getInstance()
            ->getListBySuperiorId($this->userId())
            ->pluck('user_id')
            ->toArray();
        $todayOrderingCustomerIds = OrderService::getInstance()
            ->getTodayOrderListByUserIds($totalCustomerIds)->pluck('user_id')->toArray();
        $customerList = UserService::getInstance()->getListByIds($todayOrderingCustomerIds);
        $list = $this->handleCustomerList($todayOrderingCustomerIds, $customerList);
        return $this->success($list);
    }

    public function customerList()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        $totalCustomerIds = RelationService::getInstance()
            ->getListBySuperiorId($this->userId())->pluck('user_id')->toArray();
        if (!empty($input->keywords)) {
            $userList = UserService::getInstance()->searchListByUserIds($totalCustomerIds, $input->keywords);
            $totalCustomerIds = $userList->pluck('id')->toArray();
        }
        $page = UserService::getInstance()->getPageByUserIds($totalCustomerIds, $input);
        $customerList = collect($page->items());

        $list = $this->handleCustomerList($totalCustomerIds, $customerList);

        return $this->success($this->paginate($page, $list));
    }

    private function handleCustomerList($customerIds, $customerList)
    {
        $promoterList = PromoterService::getInstance()->getPromoterListByUserIds($customerIds)->keyBy('user_id');

        $userCommissionList = CommissionService::getInstance()->getSettledCommissionListByUserIds($customerIds)->groupBy('user_id');

        return $customerList->map(function (User $user) use ($promoterList, $userCommissionList) {
            $promoter = $promoterList->get($user->id);

            $userCommission = $userCommissionList->get($user->id);
            $GMV = $userCommission ? $userCommission->sum('achievement') : 0;

            return [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'promoterId' => $promoter ? $promoter->id : 0,
                'level' => $promoter ? $promoter->level : 0,
                'GMV' => $GMV,
                'createdAt' => $user->created_at
            ];
        });
    }
}
