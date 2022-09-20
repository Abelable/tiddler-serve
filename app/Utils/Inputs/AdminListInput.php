<?php

namespace App\Utils\Inputs;

use Illuminate\Validation\Rule;

class AdminListInput extends BaseInput
{
    public $nickname;
    public $account;
    public $roleId;
    public $page = 1;
    public $limit = 10;
    public $sort = 'created_at';
    public $order = 'desc';

    public function rules()
    {
        return [
            'nickname' => 'string',
            'account' => 'string',
            'roleId' => 'integer|digits_between:1,20',
            'page' => 'integer',
            'limit' => 'integer',
            'sort' => 'string',
            'order' => Rule::in(['desc', 'asc'])
        ];
    }
}
