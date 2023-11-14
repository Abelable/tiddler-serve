<?php

namespace App\Utils\Inputs;

class NearbyPageInput extends PageInput
{
    public $id;
    public $longitude;
    public $latitude;
    public $radius = 10;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'id' => 'integer|digits_between:1,20',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'radius' => 'integer',
        ]);
    }
}
