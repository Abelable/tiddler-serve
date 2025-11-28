<?php

namespace App\Utils\Inputs;

class OrderPageInput extends PageInput
{
    public $status;
    public $orderSn;
    public $shopId;
    public $goodsId;
    public $userId;
    public $deliveryMode;
    public $consignee;
    public $mobile;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer',
            'orderSn' => 'string',
            'shopId' => 'integer',
            'goodsId' => 'integer',
            'userId' => 'integer',
            'deliveryMode' => 'integer|in:1,2',
            'consignee' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
        ]);
    }
}
