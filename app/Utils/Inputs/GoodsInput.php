<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $categoryIds;
    public $cover;
    public $video;
    public $imageList;
    public $detailImageList;
    public $defaultSpecImage;
    public $name;
    public $introduction;
    public $price;
    public $marketPrice;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $superiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;
    public $stock;
    public $numberLimit;
    public $specList;
    public $skuList;
    public $deliveryMode;
    public $freightTemplateId;
    public $pickupAddressIds;
    public $refundStatus;
    public $refundAddressId;

    public function rules()
    {
        return [
            'categoryIds' => 'required|array',
            'cover' => 'required|string',
            'video' => 'string',
            'imageList' => 'required|array',
            'detailImageList' => 'required|array',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'introduction' => 'string',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'salesCommissionRate' => 'numeric',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'superiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
            'stock' => 'required|integer',
            'numberLimit' => 'integer|digits_between:1,20',
            'specList' => 'required|array',
            'skuList' => 'required|array',
            'deliveryMode' => 'required|integer|in:1,2,3',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'pickupAddressIds' => 'array',
            'refundStatus' => 'required|integer|in:0,1',
            'refundAddressId' => 'integer|digits_between:1,20',
        ];
    }
}
