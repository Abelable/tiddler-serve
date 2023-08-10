<?php

namespace App\Utils\Inputs;

class HotelAddInput extends BaseInput
{
    public $video;
    public $imageList;
    public $name;
    public $level;
    public $categoryId;
    public $longitude;
    public $latitude;
    public $address;
    public $openingYear;
    public $lastDecorationYear;
    public $roomNum;
    public $featureTagList;
    public $tel;
    public $brief;
    public $facilityList;
    public $serviceList;
    public $remindList;
    public $checkInTipList;
    public $preorderTipList;

    public function rules()
    {
        return [
            'video' => 'string',
            'imageList' => 'required|array|min:1',
            'name' => 'required|string',
            'grade' => 'required|integer|in:1,2,3,4',
            'price' => 'required|numeric',
            'categoryId' => 'required|integer|digits_between:1,20',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'openingYear' => 'required|string',
            'lastDecorationYear' => 'string',
            'roomNum' => 'required|integer',
            'featureTagList'=> 'array',
            'tel' => 'required|string',
            'brief' => 'required|string',
            'facilityList' => 'array',
            'serviceList' => 'array',
            'remindList' => 'array',
            'checkInTipList' => 'array',
            'preorderTipList' => 'array',
        ];
    }
}
