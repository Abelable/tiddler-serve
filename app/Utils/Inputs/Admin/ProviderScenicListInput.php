<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ProviderScenicListInput extends PageInput
{
    public $status;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|in:0,1,2',
        ], parent::rules());
    }
}
