<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class HotScenicInput extends BaseInput
{
    public $scenicId;
    public $scenicCover;
    public $scenicName;
    public $recommendReason;
    public $interestedUserNumber;

    public function rules()
    {
        return [
            'scenicId' => 'required|integer',
            'scenicCover' => 'required|string',
            'scenicName' => 'required|string',
            'recommendReason' => 'required|string',
            'interestedUserNumber' => 'required|integer',
        ];
    }
}
