<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class LakeTripInput extends BaseInput
{
    public $lakeId;
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
            'lakeId' => 'required|integer',
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
