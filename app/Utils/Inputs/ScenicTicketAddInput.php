<?php

namespace App\Utils\Inputs;

class ScenicTicketAddInput extends BaseInput
{
    public $type;
    public $scenicIds;
    public $name;
    public $price;
    public $marketPrice;
    public $stock;
    public $priceList;
    public $salesCommissionRate;
    public $promotionCommissionRate;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'scenicIds' => 'required|array|min:1',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'priceList' => 'required|string',
            'salesCommissionRate' => 'required|numeric',
            'promotionCommissionRate' => 'required|numeric',
        ];
    }
}
