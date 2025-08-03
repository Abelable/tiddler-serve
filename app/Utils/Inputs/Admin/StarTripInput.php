<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class StarTripInput extends BaseInput
{
    public $productType;
    public $productId;
    public $cover;
    public $name;
    public $desc;

    public function rules()
    {
        return [
            'productType' => 'required|integer',
            'productId' => 'required|integer',
            'cover' => 'required|string',
            'name' => 'required|string',
            'desc' => 'required|string',
        ];
    }
}
