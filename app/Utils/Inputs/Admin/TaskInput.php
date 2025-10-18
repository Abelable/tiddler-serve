<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class TaskInput extends BaseInput
{
    public $merchantType;
    public $productId;
    public $merchantName;
    public $tel;
    public $address;
    public $longitude;
    public $latitude;
    public $rewardTotal;
    public $rewardList;

    public function rules()
    {
        return [
            'merchantType' => 'required|integer|in:1,2,3,4',
            'productId' => 'integer|digits_between:1,20',
            'merchantName' => 'required|string',
            'tel' => 'string',
            'address' => 'string',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'rewardTotal' => 'numeric',
            'rewardList' => 'array',
        ];
    }
}
