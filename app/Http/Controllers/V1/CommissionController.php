<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Services\CommissionService;
use App\Services\HotelOrderRoomService;
use App\Services\OrderGoodsService;
use App\Services\OrderMealTicketService;
use App\Services\OrderSetMealService;
use App\Services\PromoterChangeLogService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\ScenicOrderTicketService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
{
    public function achievement()
    {
        $promoterInfo = $this->user()->promoterInfo;

        if (is_null($promoterInfo)) {
            return $this->fail(CodeResponse::FAIL, '非代言人无法查看数据');
        }

        $levelChangeTime = PromoterChangeLogService::getInstance()
            ->getLevelChangeLog($promoterInfo->id)
            ->created_at;

        $monthDifference = 2;
        if ($levelChangeTime) {
            $currentMonth = date('n');
            $levelChangeMonth = (int)date('n', strtotime($levelChangeTime));
            $monthDifference = $currentMonth - $levelChangeMonth;
            if ($monthDifference < 0) {
                $monthDifference += 12;
            }
        }

        if ($monthDifference == 0) {
            $beforeLastMonthGMV = 0;
            $lastMonthGMV = 0;
            $curMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 7, $levelChangeTime);
        } elseif ($monthDifference == 1) {
            $beforeLastMonthGMV = 0;
            $lastMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 8, $levelChangeTime);
            $curMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 3);
        } else {
            $beforeLastMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 9, $levelChangeTime);
            $lastMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 4);
            $curMonthGMV = CommissionService::getInstance()
                ->getUserGMVByTimeType($this->userId(), 3);
        }

        $totalGMV = bcadd($beforeLastMonthGMV, $lastMonthGMV, 2);
        $totalGMV = bcadd($totalGMV, $curMonthGMV, 2);

        // 升Lv.2：3个月累计超3w
        // 升Lv.3：3个月累计超10w
        // 升Lv.4：3个月累计超50w
        $level = $promoterInfo->level;
        $targets = [1 => 30000, 2 => 100000, 3 => 500000];
        $target = $targets[$level] ?? 0;
        $percent = ($totalGMV >= $target) ? 100 : round(($totalGMV / $target) * 100, 2);

        return $this->success([
            'monthDifference' => $monthDifference,
            'beforeLastMonthGMV' => $beforeLastMonthGMV,
            'lastMonthGMV' => $lastMonthGMV,
            'curMonthGMV' => $curMonthGMV,
            'totalGMV' => $totalGMV,
            'percent' => $percent
        ]);
    }

    public function commissionOrderList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $scene = $this->verifyInteger('scene');
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');

        $page = CommissionService::getInstance()->getUserCommissionPageByTimeType(
            $this->userId(),
            $timeType,
            $statusList,
            $input,
            $scene ?: null
        );
        $commissionList = collect($page->items());

        $orderIdsByType = $commissionList->groupBy('product_type')->map(function ($group) {
            return $group->pluck('order_id')->unique()->toArray();
        });
        $productIdsByType = $commissionList->groupBy('product_type')->map(function ($group) {
            return $group->pluck('product_id')->unique()->toArray();
        });

        $scenicTicketMap = [];
        $scenicTicketList = ScenicOrderTicketService::getInstance()->getListByOrderIdsAndTicketIds(
            $orderIdsByType[ProductType::SCENIC] ?? [],
            $productIdsByType[ProductType::SCENIC] ?? []
        );
        foreach ($scenicTicketList as $scenicTicket) {
            $scenicTicketMap[$scenicTicket->order_id][$scenicTicket->ticket_id] = [
                'id' => $scenicTicket->ticket_id,
                'name' => $scenicTicket->name,
                'categoryName' => $scenicTicket->category_name,
                'price' => $scenicTicket->price,
                'number' => $scenicTicket->number,
                'scenicList' => json_decode($scenicTicket->scenic_list),
                'validityTime' => $scenicTicket->validity_time,
                'selectedDateTimestamp' => $scenicTicket->selected_date_timestamp,
                'effectiveTime' => $scenicTicket->effective_time,
                'refundStatus' => $scenicTicket->refund_status,
                'needExchange' => $scenicTicket->need_exchange
            ];
        }

        $hotelRoomMap = [];
        $hotelRoomList = HotelOrderRoomService::getInstance()->getListByOrderIdsAndRoomIds(
            $orderIdsByType[ProductType::HOTEL] ?? [],
            $productIdsByType[ProductType::HOTEL] ?? []
        );
        foreach ($hotelRoomList as $hotelRoom) {
            $hotelRoom->image_list = json_decode($hotelRoom->image_list);
            $hotelRoom->facility_list = json_decode($hotelRoom->facility_list);
            $hotelRoomMap[$hotelRoom->order_id][$hotelRoom->room_id] = $hotelRoom;
        }

        $mealTicketMap = [];
        $mealTicketList = OrderMealTicketService::getInstance()->getListByOrderIdsAndMealTicketIds(
            $orderIdsByType[ProductType::MEAL_TICKET] ?? [],
            $productIdsByType[ProductType::MEAL_TICKET] ?? []
        );
        foreach ($mealTicketList as $mealTicket) {
            $mealTicketMap[$mealTicket->order_id][$mealTicket->ticket_id] = $mealTicket;
        }

        $setMealMap = [];
        $setMealList = OrderSetMealService::getInstance()->getListByOrderIdsAndSetMealIds(
            $orderIdsByType[ProductType::SET_MEAL] ?? [],
            $productIdsByType[ProductType::SET_MEAL] ?? []
        );
        foreach ($setMealList as $setMeal) {
            $setMealMap[$setMeal->order_id][$setMeal->set_meal_id] = $setMeal;
        }

        $goodsMap = [];
        $goodsList = OrderGoodsService::getInstance()->getListByOrderIdsAndGoodsIds(
            $orderIdsByType[ProductType::GOODS] ?? [],
            $productIdsByType[ProductType::GOODS] ?? []
        );
        foreach ($goodsList as $goods) {
            $goodsMap[$goods->order_id][$goods->goods_id] = [
                'id' => $goods->goods_id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'selectedSkuName' => $goods->selected_sku_name,
                'price' => $goods->price,
                'number' => $goods->number,
            ];
        }

        $list = $commissionList->map(function (Commission $commission) use (
            $scenicTicketMap,
            $hotelRoomMap,
            $mealTicketMap,
            $setMealMap,
            $goodsMap
        ) {
            $product = null;
            $orderId = $commission->order_id;
            $productId = $commission->product_id;

            switch ($commission->product_type) {
                case ProductType::SCENIC:
                    $product = $scenicTicketMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::HOTEL:
                    $product = $hotelRoomMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::MEAL_TICKET:
                    $product = $mealTicketMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::SET_MEAL:
                    $product = $setMealMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::GOODS:
                    $product = $goodsMap[$orderId][$productId] ?? null;
                    break;
            }

            $commission['product'] = $product;
            unset($commission['product_id']);

            return $commission;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function sum()
    {
        $cashAmount = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('commission_amount');
        $pendingAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [1]);
        $settledAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [2, 3, 4]);
        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        $scene = $this->verifyInteger('scene');

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType([$this->userId()], $timeType);
        if (!is_null($scene)) {
            switch ($scene) {
                case 1:
                    $query = $query->where('scene', 1);
                    break;
                case 2:
                    $query = $query->whereIn('scene', [2, 3]);
                    break;
                case 3:
                    $query = $query->whereIn('scene', [4, 5]);
                    break;
            }
        }

        $orderCount = (clone $query)->whereIn('status', [1, 2, 3, 4])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('payment_amount');
        $pendingAmount = (clone $query)->where('status', 1)->sum('commission_amount');
        $settledAmount = (clone $query)->whereIn('status', [2, 3, 4])->sum('commission_amount');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function teamTimeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');

        $customerIds = RelationService::getInstance()
            ->getListBySuperiorId($this->userId())
            ->pluck('user_id')
            ->toArray();
        $promoterIds = PromoterService::getInstance()
            ->getPromoterListByUserIds($customerIds)
            ->pluck('user_id')
            ->toArray();

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($promoterIds, $timeType);
        $orderCount = (clone $query)
            ->whereIn('status', [1, 2, 3, 4])
            ->distinct('order_id')
            ->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('payment_amount');

        $pendingAmount = 0;
        $settledAmount = 0;
        if (!is_null($this->user()->promoterInfo)) {
            $pendingGMV = (clone $query)->where('status', 1)->sum('commission_base');
            $settledGMV = (clone $query)->whereIn('status', [2, 3, 4])->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 2:
                    $pendingAmount = bcmul($pendingGMV, 0.01, 2);
                    $settledAmount = bcmul($settledGMV, 0.01, 2);
                    break;
                case 3:
                    $pendingAmount = bcmul($pendingGMV, 0.02, 2);
                    $settledAmount = bcmul($settledGMV, 0.02, 2);
                    break;
                case 4:
                    $pendingAmount = bcmul($pendingGMV, 0.03, 2);
                    $settledAmount = bcmul($settledGMV, 0.03, 2);
                    break;
            }
        }

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function cash()
    {
        $commissionQuery = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month);
        $selfPurchase = (clone $commissionQuery)->where('scene', 1)->sum('commission_amount');
        $share = (clone $commissionQuery)->whereIn('scene', [2, 3])->sum('commission_amount');
        $team = (clone $commissionQuery)->whereIn('scene', [4, 5])->sum('commission_amount');
        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share,
            'team' => $team
        ]);
    }
}
