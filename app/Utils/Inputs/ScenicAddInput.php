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
    public $facility_list;
    public $tips_list;

    public function rules()
    {
        return [
            'video' => 'string',
            'imageList' => 'required|string',
            'name' => 'required|string',
            'categoryId' => 'required|integer|digits_between:1,20',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'brief' => 'required|string',
            'policy_list' => 'required|string',
            'hotline_list' => 'required|string',
            'facility_list' => 'required|string',
            'tips_list' => 'required|string',
        ];
    }
}
