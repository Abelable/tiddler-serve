<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearTaskInput extends BaseInput
{
    public $icon;
    public $name;
    public $desc;
    public $btnContent;
    public $luckScore;
    public $type;
    public $param;

    public function rules()
    {
        return [
            'icon' => 'required|string',
            'name' => 'required|string',
            'desc' => 'required|string',
            'btnContent' => 'required|string',
            'luckScore' => 'required|integer',
            'type' => 'required|integer|in:1,2',
            'param' => 'string',
        ];
    }
}
