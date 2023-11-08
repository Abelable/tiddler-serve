<?php

namespace App\Utils\Inputs;

class TourismNotePageInput extends PageInput
{
    public $id;
    public $authorId;
    public $withComments;

    public function rules()
    {
        return array_merge([
            'id' => 'integer|digits_between:1,20',
            'authorId' => 'integer|digits_between:1,20',
            'withComments' => 'integer|in:0,1',
        ], parent::rules());
    }
}
