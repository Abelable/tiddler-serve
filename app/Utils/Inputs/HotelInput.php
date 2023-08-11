<?php

namespace App\Utils\Inputs;

class HotelInput extends BaseInput
{
    public $video;
    public $cover;
    public $appearanceImageList;
    public $interiorImageList;
    public $roomImageList;
    public $environmentImageList;
    public $restaurantImageList;
    public $name;
    public $englishName;
    public $grade;
    public $categoryId;
    public $price;
    public $longitude;
    public $latitude;
    public $address;
    public $openingYear;
    public $lastDecorationYear;
    public $roomNum;
    public $featureTagList;
    public $tel;
    public $brief;
    public $recreationFacility;
    public $healthFacility;
    public $childrenFacility;
    public $commonFacility;
    public $publicAreaFacility;
    public $trafficService;
    public $cateringService;
    public $receptionService;
    public $cleanService;
    public $businessService;
    public $otherService;
    public $remindList;
    public $checkInTipList;
    public $preorderTipList;

    public function rules()
    {
        return [
            'video' => 'string',
            'cover' => 'required|string',
            'appearanceImageList' => 'required|array',
            'interiorImageList' => 'required|array',
            'roomImageList' => 'required|array',
            'environmentImageList' => 'required|array',
            'restaurantImageList' => 'required|array',
            'name' => 'required|string',
            'englishName' => 'required|string',
            'grade' => 'required|integer|in:1,2,3,4',
            'categoryId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'openingYear' => 'required|string',
            'lastDecorationYear' => 'string',
            'roomNum' => 'required|integer',
            'featureTagList'=> 'array',
            'tel' => 'required|string',
            'brief' => 'required|string',
            'recreationFacility' => 'array',
            'healthFacility' => 'array',
            'childrenFacility' => 'array',
            'commonFacility' => 'array',
            'publicAreaFacility' => 'array',
            'trafficService' => 'array',
            'cateringService' => 'array',
            'receptionService' => 'array',
            'cleanService' => 'array',
            'businessService' => 'array',
            'otherService' => 'array',
            'remindList' => 'array',
            'checkInTipList' => 'array',
            'preorderTipList' => 'array',
        ];
    }
}
