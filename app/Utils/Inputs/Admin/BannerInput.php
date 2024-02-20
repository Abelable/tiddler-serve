<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class BannerInput extends BaseInput
{
    public $cover;
    public $desc;
    public $scene;
    public $value;

    public function rules()
    {
        return [
            'cover' => 'required|string',
            'desc' => 'string',
            'scene' => 'required|integer|in:1,2,3,4,5',
            'value' => 'required|string'
        ];
    }
}
