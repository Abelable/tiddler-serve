<?php

namespace App\Utils\Inputs;

class CommentInput extends BaseInput
{
    public $mediaId;
    public $commentId;
    public $content;

    public function rules()
    {
        return [
            'mediaId' => 'required|integer|digits_between:1,20',
            'commentId' => 'integer|digits_between:1,20',
            'content' => 'required|string'
        ];
    }
}
