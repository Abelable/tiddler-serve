<?php

namespace App\Utils\Inputs;

class CreateHotelOrderInput extends BaseInput
{
    public $roomId;
    public $checkInDate;
    public $checkOutDate;
    public $num;
    public $consignee;
    public $mobile;

    public function rules()
    {
        return [
            'roomId' => 'required|integer|digits_between:1,20',
            'checkInDate' => 'required|integer',
            'checkOutDate' => 'required|integer',
            'num' => 'required|integer',
            'consignee' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
        ];
    }
}
