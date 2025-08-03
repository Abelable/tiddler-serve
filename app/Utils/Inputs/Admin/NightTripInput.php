<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class NightTripInput extends BaseInput
{
    public $scenicId;
    public $scenicCover;
    public $scenicName;
    public $featureTips;
    public $recommendTips;
    public $guideTips;

    public function rules()
    {
        return [
            'scenicId' => 'required|integer',
            'scenicCover' => 'required|string',
            'scenicName' => 'required|string',
            'featureTips' => 'required|string',
            'recommendTips' => 'required|string',
            'guideTips' => 'required|string',
        ];
    }
}
