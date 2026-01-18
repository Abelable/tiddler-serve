<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearTaskInput extends BaseInput
{
    public $type;
    public $icon;
    public $name;
    public $desc;
    public $btnContent;
    public $luckScore;
    public $scene;
    public $param;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2,3',
            'icon' => 'required|string',
            'name' => 'required|string',
            'desc' => 'required|string',
            'btnContent' => 'required|string',
            'luckScore' => 'required|integer',
            'scene' => 'required|integer|in:1,2',
            'param' => 'string',
        ];
    }
}
