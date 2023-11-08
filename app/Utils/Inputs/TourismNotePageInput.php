<?php

namespace App\Utils\Inputs;

class TourismNotePageInput extends PageInput
{
    public $id;
    public $authorId;
    public $withComments;

    public function rules()
    {
        return [
            'id' => 'integer|digits_between:1,20',
            'authorId' => 'integer|digits_between:1,20',
            'withComments' => 'boolean'
        ];
    }
}
