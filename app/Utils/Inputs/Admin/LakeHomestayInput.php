<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class LakeHomestayInput extends BaseInput
{
    public $hotelId;
    public $cover;
    public $name;

    public function rules()
    {
        return [
            'hotelId' => 'required|integer',
            'cover' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
