<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class LakeCycleInput extends BaseInput
{
    public $routeId;
    public $scenicId;
    public $scenicCover;
    public $scenicName;
    public $desc;
    public $distance;
    public $duration;
    public $time;

    public function rules()
    {
        return [
            'routeId' => 'required|integer',
            'scenicId' => 'required|integer',
            'scenicCover' => 'required|string',
            'scenicName' => 'required|string',
            'desc' => 'required|string',
            'distance' => 'required|string',
            'duration' => 'required|string',
            'time' => 'required|string',
        ];
    }
}
