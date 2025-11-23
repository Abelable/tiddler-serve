<?php

namespace App\Utils\Inputs;

class RefundInput extends BaseInput
{
    public $shopId;
    public $orderId;
    public $orderSn;
    public $couponId;
    public $orderGoodsId;
    public $goodsId;
    public $type;
    public $reason;
    public $imageList;

    public function rules()
    {
        return [
            'shopId' => 'required|integer|digits_between:1,20',
            'orderId' => 'required|integer|digits_between:1,20',
            'orderSn' => 'required|string',
            'couponId' => 'integer|digits_between:1,20',
            'orderGoodsId' => 'required|integer|digits_between:1,20',
            'goodsId' => 'required|integer|digits_between:1,20',
            'type' => 'required|integer|in:1,2',
            'reason' => 'required|string',
            'imageList' => 'array',
        ];
    }
}
