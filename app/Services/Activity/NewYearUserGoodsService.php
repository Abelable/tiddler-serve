<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearGoods;
use App\Models\Activity\NewYearUserGoods;
use App\Models\Mall\Goods\Address;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class NewYearUserGoodsService extends BaseService
{
    public function createUserGoods($userId, NewYearGoods $goods, Address $address)
    {
        $userGoods = NewYearUserGoods::new();
        $userGoods->user_id = $userId;
        $userGoods->goods_id = $goods->id;
        $userGoods->cover = $goods->cover;
        $userGoods->luck_score = $goods->luck_score;
        $userGoods->name = $goods->name;
        $userGoods->consignee = $address->name;
        $userGoods->mobile = $address->mobile;
        $userGoods->address = $address->region_desc . ' ' . $address->address_detail;
        $userGoods->save();

        return $userGoods;
    }

    public function getUserGoodsPage($userId, PageInput $input, $statusList = [0, 1], $columns = ['*'])
    {
        return NewYearUserGoods::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserGoodsCount($userId, $goodsIds, $statusList = [0, 1])
    {
        return NewYearUserGoods::query()
            ->where('user_id', $userId)
            ->whereIn('goods_id', $goodsIds)
            ->whereIn('status', $statusList)
            ->count();
    }

    public function getUserGoodsCountMap($userId, array $goodsIds, array $statusList = [0, 1])
    {
        return NewYearUserGoods::query()
            ->selectRaw('goods_id, COUNT(*) as cnt')
            ->where('user_id', $userId)
            ->whereIn('goods_id', $goodsIds)
            ->whereIn('status', $statusList)
            ->groupBy('goods_id')
            ->pluck('cnt', 'goods_id')
            ->toArray();
    }
}
