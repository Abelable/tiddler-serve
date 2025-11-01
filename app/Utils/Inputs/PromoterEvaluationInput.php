<?php

namespace App\Utils\Inputs;

class PromoterEvaluationInput extends BaseInput
{
    public $promoterId;
    public $score;
    public $tagIds;
    public $content;
    public $imageList;

    public function rules()
    {
        return [
            'promoterId' => 'required|integer|digits_between:1,20',
            'score' => 'required|numeric',
            'tagIds' => 'array',
            'content' => 'required|string',
            'imageList' => 'array',
        ];
    }
}
