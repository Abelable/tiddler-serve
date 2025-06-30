<?php

namespace App\Services;

use App\Models\OrderPackageGoods;

class OrderPackageGoodsService extends BaseService
{

    public function create($orderId, $packageId, $goodsId, $goodsCover, $goodsName, $selectedSkuName, $goodsNumber)
    {
        $goods = OrderPackageGoods::new();
        $goods->order_id = $orderId;
        $goods->package_id = $packageId;
        $goods->goods_id = $goodsId;
        $goods->cover = $goodsCover;
        $goods->name = $goodsName;
        $goods->selected_sku_name = $selectedSkuName;
        $goods->number = $goodsNumber;
        $goods->save();
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderPackageGoods::query()->where('order_id', $orderId)->get($columns);
    }
}
