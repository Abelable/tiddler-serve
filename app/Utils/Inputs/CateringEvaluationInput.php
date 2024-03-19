<?php

namespace App\Utils\Inputs;

class CateringEvaluationInput extends BaseInput
{
    public $orderId;
    public $restaurantId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'orderId' => 'required|integer|digits_between:1,20',
            'restaurantId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
