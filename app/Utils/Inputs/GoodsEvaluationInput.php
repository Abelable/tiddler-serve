<?php

namespace App\Utils\Inputs;

class GoodsEvaluationInput extends BaseInput
{
    public $goodsId;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'goodsId' => 'required|integer|digits_between:1,20',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'required|array',
        ];
    }
}
