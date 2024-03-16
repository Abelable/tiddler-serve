<?php

namespace App\Utils\Inputs;

class GoodsEvaluationInput extends BaseInput
{
    public $orderId;
    public $goodsIds;
    public $content;
    public $score;
    public $imageList;

    public function rules()
    {
        return [
            'orderId' => 'required|integer|digits_between:1,20',
            'goodsIds' => 'required|array|min:1',
            'content' => 'required|string',
            'score' => 'required|numeric',
            'imageList' => 'array',
        ];
    }
}
