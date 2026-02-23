<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class WxMpInput extends BaseInput
{
    public $appId;
    public $secret;
    public $name;

    public function rules()
    {
        return [
            'appId' => 'required|string',
            'secret' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
