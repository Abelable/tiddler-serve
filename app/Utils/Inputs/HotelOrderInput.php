<?php

namespace App\Utils\Inputs;

class HotelOrderInput extends BaseInput
{
    public $roomId;
    public $checkInDate;
    public $checkOutDate;
    public $num;
    public $consignee;
    public $mobile;
    public $useBalance;

    public function rules()
    {
        return [
            'roomId' => 'required|integer|digits_between:1,20',
            'checkInDate' => 'required|integer',
            'checkOutDate' => 'required|integer',
            'num' => 'required|integer',
            'consignee' => 'required|string',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'useBalance' => 'integer|in:0,1',
        ];
    }
}
