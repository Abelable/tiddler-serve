<?php

namespace App\Utils\Inputs;

class CreateScenicOrderInput extends BaseInput
{
    public $ticketId;
    public $categoryId;
    public $timeStamp;
    public $num;
    public $consignee;
    public $mobile;
    public $idCardNumber;

    public function rules()
    {
        return [
            'ticketId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'timeStamp' => 'required|integer',
            'num' => 'required|integer',
            'consignee' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'idCardNumber' => 'required|string',
        ];
    }
}
