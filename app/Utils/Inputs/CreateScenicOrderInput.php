<?php

namespace App\Utils\Inputs;

class CreateScenicOrderInput extends BaseInput
{
    public $addressId;
    public $cartIds;
    public $remarks;

    public function rules()
    {
        return [
            'addressId' => 'required|integer|digits_between:1,20',
            'cartIds' => 'required|array|min:1',
            'remarks' => 'string'
        ];
    }
}
