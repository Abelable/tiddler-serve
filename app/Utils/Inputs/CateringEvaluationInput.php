<?php

namespace App\Utils\Inputs;

class CateringEvaluationInput extends BaseInput
{
    public $restaurantId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'restaurantId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
