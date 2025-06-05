<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class MediaPageInput extends PageInput
{
    public $title;
    public $userId;
    public $scenicId;
    public $hotelId;
    public $restaurantId;
    public $goodsId;

    public function rules()
    {
        return array_merge([
            'title' => 'string',
            'userId' => 'integer|digits_between:1,20',
            'scenicId' => 'integer|digits_between:1,20',
            'hotelId' => 'integer|digits_between:1,20',
            'restaurantId' => 'integer|digits_between:1,20',
            'goodsId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
