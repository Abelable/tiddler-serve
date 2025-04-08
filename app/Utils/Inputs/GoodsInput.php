<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $cover;
    public $video;
    public $imageList;
    public $detailImageList;
    public $defaultSpecImage;
    public $name;
    public $introduction;
    public $freightTemplateId;
    public $shopCategoryId;
    public $categoryId;
    public $returnAddressId;
    public $price;
    public $marketPrice;
    public $stock;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $specList;
    public $skuList;
    public $refundStatus;

    public function rules()
    {
        return [
            'cover' => 'required|string',
            'video' => 'string',
            'imageList' => 'required|array',
            'detailImageList' => 'required|array',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'introduction' => 'string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'shopCategoryId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'returnAddressId' => 'integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'salesCommissionRate' => 'numeric',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'specList' => 'required|array',
            'skuList' => 'required|array',
            'refundStatus' => 'required|integer|in:0,1',
        ];
    }
}
