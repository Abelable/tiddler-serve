<?php

namespace App\Utils\Inputs;

class FreightTemplateInput extends BaseInput
{
    public $name;
    public $title;
    public $computeMode;
    public $freeQuota;
    public $areaList;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'title' => 'required|string',
            'computeMode' => 'required|integer|in:1,2',
            'freeQuota' => 'numeric',
            'areaList' => 'required|array|min:1',
        ];
    }
}
