<?php

namespace App\Utils\Inputs;

class TempTourismNoteInput extends TourismNoteInput
{
    public $userId;
    public $commodityId;
    public $commodityType;

    public function rules()
    {
        return [
            'userId' => 'required|string',
            'commodityId' => 'required|string',
            'commodityType' => 'required|string'
        ];
    }
}
