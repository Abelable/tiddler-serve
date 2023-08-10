<?php

namespace App\Utils\Inputs;

class HotelEditInput extends HotelAddInput
{
    public $id;

    public function rules()
    {
        return array_merge([
            'id' => 'required|integer|digits_between:1,20',
        ], parent::rules());
    }
}
