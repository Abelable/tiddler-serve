<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ScenicTicketListInput extends PageInput
{
    public $name;
    public $type;
    public $scenicId;
    public $status;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'type' => 'integer|in:1,2',
            'scenicId' => 'integer|digits_between:1,20',
            'status' => 'integer|in:0,1,2,3',
        ], parent::rules());
    }
}
