<?php

namespace App\Utils\Inputs;

class ScenicAddInput extends BaseInput
{
    public $video;
    public $imageList;
    public $name;
    public $categoryId;
    public $longitude;
    public $latitude;
    public $address;
    public $brief;
    public $policy_list;
    public $hotline_list;
    public $project_list;
    public $facility_list;
    public $tips_list;

    public function rules()
    {
        return [
            'video' => 'string',
            'imageList' => 'required|array|min:1',
            'name' => 'required|string',
            'categoryId' => 'required|integer|digits_between:1,20',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'brief' => 'required|string',
            'policy_list' => 'array',
            'hotline_list' => 'array',
            'project_list' => 'array',
            'facility_list' => 'array',
            'tips_list' => 'array',
        ];
    }
}
