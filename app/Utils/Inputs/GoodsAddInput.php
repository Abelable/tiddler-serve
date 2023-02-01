<?php

namespace App\Utils\Inputs;

class GoodsAddInput extends BaseInput
{
    public $video;
    public $imageList;
    public $name;
    public $freightTemplateId;
    public $categoryId;
    public $returnAddressId;
    public $price;
    public $marketPrice;
    public $stock;
    public $commissionRate;
    public $detailImageList;
    public $specList;
    public $skuList;

    public function rules()
    {
        return [
            'video' => 'string',
            'imageList' => 'required|string',
            'name' => 'required|string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'returnAddressId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'commissionRate' => 'required|numeric',
            'detailImageList' => 'required|string',
            'specList' => 'required|string',
            'skuList' => 'required|string',
        ];
    }
}
