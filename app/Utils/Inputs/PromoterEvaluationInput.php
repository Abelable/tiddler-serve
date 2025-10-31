<?php

namespace App\Utils\Inputs;

class PromoterEvaluationInput extends BaseInput
{
    public $promoterId;
    public $tagIds;
    public $score;
    public $content;
    public $imageList;

    public function rules()
    {
        return [
            'promoterId' => 'required|integer|digits_between:1,20',
            'tagIds' => 'array',
            'score' => 'required|numeric',
            'content' => 'required|string',
            'imageList' => 'array',
        ];
    }
}
