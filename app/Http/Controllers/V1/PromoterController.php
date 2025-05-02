<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\RelationService;

class PromoterController extends Controller
{
    public function customerData()
    {
        $todayNewCustomerCount = RelationService::getInstance()->getTodayCountBySuperiorId($this->userId());

        $customerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $todayOrderingCustomerCount = OrderService::getInstance()->getTodayOrderingUserCountByUserIds($customerIds);

        $customerTotalCount = RelationService::getInstance()->getCountBySuperiorId($this->userId());

        return $this->success([
            'todayNewCount' => $todayNewCustomerCount,
            'todayOrderingCount' => $todayOrderingCustomerCount,
            'totalCount' => $customerTotalCount
        ]);
    }
}
