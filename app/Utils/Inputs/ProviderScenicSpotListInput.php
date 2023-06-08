<?php

namespace App\Utils\Inputs;

class ProviderScenicSpotListInput extends PageInput
{
    public $status;

    public function rules()
    {
        return array_merge([
            'status' => 'required|integer|in:1,2,3',
        ], parent::rules());
    }
}
