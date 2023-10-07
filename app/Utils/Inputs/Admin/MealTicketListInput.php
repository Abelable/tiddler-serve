<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class MealTicketListInput extends PageInput
{
    public $name;
    public $restaurantId;
    public $status;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'restaurantId' => 'integer|digits_between:1,20',
            'status' => 'integer|in:0,1,2,3',
        ], parent::rules());
    }
}
