<?php

namespace App\Utils\Inputs;

class MerchantSettleInInput extends BaseInput
{
    public $user_id;
    public $status;
    public $failure_reason;
    public $type;
    public $company_name;
    public $region_list;
    public $address_detail;
    public $business_license_photo;
    public $name;
    public $mobile;
    public $email;
    public $id_card_number;
    public $id_card_front_photo;
    public $id_card_back_photo;
    public $hold_id_card_photo;
    public $bank_card_number;
    public $bank_name;
    public $shop_name;
    public $shop_category_id;

    public function rules()
    {
        return [
            'user_id' => 'required|integer|digits_between:1,20',
            'type' => 'required|integer|in:1,2',
            'company_name' => 'string',
            'region_list' => 'required|string',
            'address_detail' => 'required|string',
            'business_license_photo' => 'string',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'email' => 'required|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
            'id_card_number' => 'required|regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/',
            'id_card_front_photo' => 'required|string',
            'id_card_back_photo' => 'required|string',
            'hold_id_card_photo' => 'required|string',
            'bank_card_number' => 'required|regex:/^([1-9]{1})(\d{15}|\d{16}|\d{18})$/',
            'bank_name' => 'required|string',
            'shop_name' => 'required|string',
            'shop_category_id' => 'required|integer|digits_between:1,20',
        ];
    }
}
