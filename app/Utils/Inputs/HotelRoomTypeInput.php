<?php

namespace App\Utils\Inputs;

class HotelRoomTypeInput extends BaseInput
{
    public $hotelId;
    public $name;
    public $imageList;
    public $bedDesc;
    public $areaSize;
    public $floorDesc;
    public $facilityList;

    public function rules()
    {
        return [
            'hotelId' => 'integer|digits_between:1,20',
            'name' => 'required|string',
            'imageList' => 'required|array',
            'bedDesc' => 'required|string',
            'areaSize' => 'required|numeric',
            'floorDesc' => 'required|string',
            'facilityList' => 'array',
        ];
    }
}
