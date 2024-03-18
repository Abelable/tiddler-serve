<?php

namespace App\Utils\Inputs;

class ScenicEvaluationInput extends BaseInput
{
    public $orderId;
    public $ticketId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'orderId' => 'required|integer|digits_between:1,20',
            'ticketId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'array',
        ];
    }
}
