<?php

namespace App\Utils\Inputs;

class HotelEvaluationInput extends BaseInput
{
    public $hotelId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'hotelId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
