<?php

namespace App\Utils\Inputs;

class CreateOrderInput extends BaseInput
{
    public $addressId;
    public $cartIds;
    public $remarks;

    public function rules()
    {
        return [
            'addressId' => 'required|integer|in:1,2',
            'cartIds' => 'required|array|min:1',
            'remarks' => 'string'
        ];
    }
}
