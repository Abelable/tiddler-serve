<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $image;
    public $video;
    public $imageList;
    public $detailImageList;
    public $defaultSpecImage;
    public $name;
    public $freightTemplateId;
    public $categoryId;
    public $returnAddressId;
    public $price;
    public $marketPrice;
    public $stock;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $specList;
    public $skuList;

    public function rules()
    {
        return [
            'image' => 'required|string',
            'video' => 'string',
            'imageList' => 'required|string',
            'detailImageList' => 'required|string',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'returnAddressId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'salesCommissionRate' => 'required|integer',
            'promotionCommissionRate' => 'required|integer',
            'specList' => 'required|string',
            'skuList' => 'required|string',
        ];
    }
}
