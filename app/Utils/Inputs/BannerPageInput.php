<?php

namespace App\Utils\Inputs;

class BannerPageInput extends PageInput
{
    public $status;
    public $scene;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer|in:1,2',
            'scene' => 'integer|in:1,2,3,4,5',
        ]);
    }
}
