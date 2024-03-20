<?php

namespace App\Utils\Inputs;

class CateringEvaluationInput extends BaseInput
{
    public $type;
    public $orderId;
    public $restaurantId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'orderId' => 'required|integer|digits_between:1,20',
            'restaurantId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
