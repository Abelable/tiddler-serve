<?php

namespace App\Utils\Inputs;

class FreightTemplateAddInput extends BaseInput
{
    public $mode;
    public $name;
    public $title;
    public $computeMode;
    public $freeQuota;
    public $areaList;
    public $expressList;
    public $expressTemplateLists;

    public function rules()
    {
        return [
            'mode' => 'required|integer|in:1,2',
            'name' => 'required|string',
            'title' => 'required|string',
            'computeMode' => 'required|integer|in:1,2',
            'freeQuota' => 'numeric',
            'areaList' => 'required_if:mode,1',
            'expressList' => 'required_if:mode,1',
            'expressTemplateLists' => 'required_if:mode,1',
        ];
    }
}
