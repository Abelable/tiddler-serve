<?php

namespace App\Utils\Inputs;

class ScenicInput extends BaseInput
{
    public $video;
    public $imageList;
    public $name;
    public $level;
    public $categoryId;
    public $price;
    public $longitude;
    public $latitude;
    public $address;
    public $brief;
    public $openTimeList;
    public $policyList;
    public $hotlineList;
    public $projectList;
    public $facilityList;
    public $tipsList;

    public function rules()
    {
        return [
            'video' => 'string',
            'imageList' => 'required|array|min:1',
            'name' => 'required|string',
            'level' => 'string',
            'categoryId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'brief' => 'required|string',
            'openTimeList'=> 'array',
            'policyList' => 'array',
            'hotlineList' => 'array',
            'projectList' => 'array',
            'facilityList' => 'array',
            'tipsList' => 'array',
        ];
    }
}
