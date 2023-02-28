<?php

namespace App\Utils\Inputs;

class CommentListInput extends PageInput
{
    public $commentId;

    public function rules()
    {
        return array_merge([
            'commentId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
