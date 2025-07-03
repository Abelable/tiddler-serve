<?php

namespace App\Services;

use App\Models\CartGoods;
use App\Models\OrderGoods;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderGoodsService extends BaseService
{
    public function createList($cartGoodsList, $orderId, $userId, $userLevel)
    {
        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            $goods = OrderGoods::new();
            $goods->user_id = $userId;
            $goods->user_level = $userLevel;
            $goods->order_id = $orderId;
            $goods->goods_id = $cartGoods->goods_id;
            $goods->shop_id = $cartGoods->shop_id;
            $goods->is_gift = $cartGoods->is_gift;
            $goods->duration = $cartGoods->duration;
            $goods->refund_status = $cartGoods->refund_status;
            $goods->cover = $cartGoods->cover;
            $goods->name = $cartGoods->name;
            $goods->selected_sku_name = $cartGoods->selected_sku_name;
            $goods->selected_sku_index = $cartGoods->selected_sku_index;
            $goods->price = $cartGoods->price;
            $goods->sales_commission_rate = $cartGoods->sales_commission_rate;
            $goods->promotion_commission_rate = $cartGoods->promotion_commission_rate;
            $goods->promotion_commission_upper_limit = $cartGoods->promotion_commission_upper_limit;
            $goods->superior_promotion_commission_rate = $cartGoods->superior_promotion_commission_rate;
            $goods->superior_promotion_commission_upper_limit = $cartGoods->superior_promotion_commission_upper_limit;
            $goods->number = $cartGoods->number;
            $goods->save();
        }
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderGoods::query()->where('order_id', $orderId)->get($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return OrderGoods::query()->find($id, $columns);
    }

    public function getOrderGoods($orderId, $goodsId, $columns = ['*'])
    {
        return OrderGoods::query()->where('order_id', $orderId)->where('goods_id', $goodsId)->first($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return OrderGoods::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function getGiftOrderGoodsList(array $orderIds, $columns = ['*'])
    {
        return OrderGoods::query()
            ->whereIn('order_id', $orderIds)
            ->where('is_gift', 1)
            ->first($columns);
    }

    public function getListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return OrderGoods::query()->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getListByOrderIdsAndGoodsIds(array $orderIds, array $goodsIds, $columns = ['*'])
    {
        return OrderGoods::query()
            ->whereIn('order_id', $orderIds)
            ->whereIn('goods_id', $goodsIds)
            ->get($columns);
    }

    public function delete($orderId)
    {
        return OrderGoods::query()->where('order_id', $orderId)->delete();
    }

    public function batchDelete(array $orderIds)
    {
        return OrderGoods::query()->whereIn('order_id', $orderIds)->delete();
    }

    public function getUserListByGoodsIds($userId, array $goodsIds, $columns = ['*'])
    {
        return OrderGoods::query()->where('user_id', $userId)->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getRecentlyUserListByGoodsIds($userId, array $goodsIds, $columns = ['*'])
    {
        return OrderGoods::query()
            ->where('user_id', $userId)
            ->whereIn('goods_id', $goodsIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->get($columns);
    }

    public function getList($columns = ['*'])
    {
        return OrderGoods::query()->get($columns);
    }

    public function getLatestListByGoodsId($goodsId, $limit, $columns = ['*'])
    {
        return OrderGoods::query()
            ->where('goods_id', $goodsId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get($columns);
    }

    public function getLatestCustomerList($goodsId, $limit = 50)
    {
        $latestOrderGoodsList = $this->getLatestListByGoodsId($goodsId, $limit);
        $customerIds = $latestOrderGoodsList->pluck('user_id')->toArray();
        $customerList = UserService::getInstance()->getListByIds($customerIds)->keyBy('id');
        return $latestOrderGoodsList->map(function (OrderGoods $orderGoods) use ($customerList) {
            $customer = $customerList->get($orderGoods->user_id);
            return $customer ? [
                'id' => $customer->id,
                'avatar' => $customer->avatar,
                'nickname' => $customer->nickname,
                'createdAt' => $orderGoods->created_at,
            ] : null;
        })->filter(function ($customer) {
            return !is_null($customer);
        })->values();
    }

    public function getTopSalesGoodsList($startDate, $endDate)
    {
        $startDate = Carbon::createFromTimestamp($startDate);
        $endDate   = Carbon::createFromTimestamp($endDate);
        return OrderGoods::query()
            ->where('status', '1')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('goods_id')
            ->select('goods_id', DB::raw('SUM(price * number) as sum'))
            ->orderByDesc('sum')
            ->limit(7)
            ->get();
    }

    public function getTopOrderCountGoodsList($startDate, $endDate)
    {
        $startDate = Carbon::createFromTimestamp($startDate);
        $endDate   = Carbon::createFromTimestamp($endDate);
        return OrderGoods::query()
            ->where('status', '1')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('goods_id')
            ->select('goods_id', DB::raw('COUNT(*) as count'))
            ->orderByDesc('count')
            ->limit(7)
            ->get();
    }

    public function updateStatusByOrderIds(array $orderIds, $status)
    {
        return OrderGoods::query()->whereIn('order_id', $orderIds)->update(['status' => $status]);
    }

    public function searchList($userId, $keyword, $columns = ['*'])
    {
        return OrderGoods::query()
            ->where('user_id', $userId)
            ->where('name', 'like', "%{$keyword}%")
            ->get($columns);
    }
}
