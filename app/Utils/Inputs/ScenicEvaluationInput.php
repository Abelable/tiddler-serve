<?php

namespace App\Utils\Inputs;

class ScenicEvaluationInput extends BaseInput
{
    public $scenicId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'scenicId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
