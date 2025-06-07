<?php

namespace App\Utils\Inputs;

class TempShortVideoInput extends ShortVideoInput
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
