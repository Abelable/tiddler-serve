<?php

namespace App\Utils\Inputs;

class CommentListInput extends PageInput
{
    public $mediaId;
    public $commentId;

    public function rules()
    {
        return array_merge([
            'mediaId' => 'required|integer|digits_between:1,20',
            'commentId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
