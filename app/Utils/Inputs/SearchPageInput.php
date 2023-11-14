<?php

namespace App\Utils\Inputs;

class SearchPageInput extends BaseInput
{
    public $keywords;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'keywords' => 'required|string',
        ]);
    }
}
