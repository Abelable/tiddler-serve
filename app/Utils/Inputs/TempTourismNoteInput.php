<?php

namespace App\Utils\Inputs;

class TempTourismNoteInput extends TourismNoteInput
{
    public $userId;
    public $productId;
    public $productType;

    public function rules()
    {
        return [
            'userId' => 'required|string',
            'productId' => 'required|string',
            'productType' => 'required|string'
        ];
    }
}
