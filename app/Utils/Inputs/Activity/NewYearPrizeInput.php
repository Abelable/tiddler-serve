<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearPrizeInput extends BaseInput
{
    public $status;
    public $type;
    public $couponId;
    public $goodsId;
    public $isBig;

    public $cover;
    public $name;
    public $sort;

    // 抽奖 & 成本核心字段
    public $rate;
    public $stock;
    public $luckScore;
    public $cost;

    // 风控字段
    public $limitPerUser;
    public $startAt;
    public $endAt;
    public $fallbackPrizeId;

    public function rules()
    {
        return [
            // 基础字段
            'status' => 'integer|in:1,2',
            'type'   => 'required|integer|in:1,2,3',
            'isBig'  => 'integer|in:0,1',

            'couponId' => 'integer|min:0',
            'goodsId'  => 'integer|min:0',

            'cover' => 'required|string|max:500',
            'name'  => 'required|string|max:100',
            'sort'  => 'integer|min:0',

            // 抽奖核心
            'rate' => 'required|numeric|min:0|max:1',
            'stock' => 'required|integer|min:-1',
            'luckScore' => 'integer|min:0',
            'cost' => 'numeric|min:0',

            // 风控
            'limitPerUser' => 'integer|min:0',
            'fallbackPrizeId' => 'integer|min:0',

            // 时间控制
            'startAt' => 'nullable|date',
            'endAt'   => 'nullable|date|after:startAt',
        ];
    }

    /**
     * 字段级联校验
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 福气值类型，必须有 luckValue
            if ($this->type == 1 && $this->luckValue <= 0) {
                $validator->errors()->add('luckValue', '福气值奖品必须设置福气值数量');
            }

            // 优惠券类型
            if ($this->type == 2 && empty($this->couponId)) {
                $validator->errors()->add('couponId', '优惠券奖品必须绑定 couponId');
            }

            // 商品类型
            if ($this->type == 3 && empty($this->goodsId)) {
                $validator->errors()->add('goodsId', '商品奖品必须绑定 goodsId');
            }

            // 大奖必须限量
            if ($this->isBig == 1 && $this->stock == -1) {
                $validator->errors()->add('stock', '大奖不允许设置为无限库存');
            }
        });
    }
}
