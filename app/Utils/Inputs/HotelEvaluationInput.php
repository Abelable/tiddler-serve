<?php

namespace App\Utils\Inputs;

class HotelEvaluationInput extends BaseInput
{
    public $orderId;
    public $hotelId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'orderId' => 'required|integer|digits_between:1,20',
            'hotelId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'array',
        ];
    }
}
