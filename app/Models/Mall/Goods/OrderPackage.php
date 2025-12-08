<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\OrderPackage
 *
 * @property int $id
 * @property int $status 包裹状态：0-待发货，1-已发货，2-运输中，3-已签收
 * @property int $order_id 订单id
 * @property string $ship_channel 快递公司名称
 * @property string $ship_code 快递公司编号
 * @property string $ship_sn 快递单号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mall\Goods\OrderPackageGoods[] $goodsList
 * @property-read int|null $goods_list_count
 * @property-read \App\Models\Mall\Goods\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderPackage extends BaseModel
{
    public function goodsList()
    {
        return $this->hasMany(OrderPackageGoods::class, 'package_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
