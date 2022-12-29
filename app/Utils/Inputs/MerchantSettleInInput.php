<?php

namespace App\Utils\Inputs;

class MerchantSettleInInput extends BaseInput
{
    public $userId;
    public $type;
    public $companyName;
    public $regionList;
    public $addressDetail;
    public $businessLicensePhoto;
    public $name;
    public $mobile;
    public $email;
    public $idCardNumber;
    public $idCardFrontPhoto;
    public $idCardBackPhoto;
    public $holdIdCardPhoto;
    public $bankCardNumber;
    public $bankName;
    public $shopName;
    public $shopCategoryId;

    public function rules()
    {
        return [
            'userId' => 'required|integer|digits_between:1,20',
            'type' => 'required|integer|in:1,2',
            'companyName' => 'string',
            'regionList' => 'required|string',
            'addressDetail' => 'required|string',
            'businessLicensePhoto' => 'string',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'email' => 'required|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
            'idCardNumber' => 'required|regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/',
            'idCardFrontPhoto' => 'required|string',
            'idCardBackPhoto' => 'required|string',
            'holdIdCardPhoto' => 'required|string',
            'bankCardNumber' => 'required|regex:/^([1-9]{1})(\d{15}|\d{16}|\d{18})$/',
            'bankName' => 'required|string',
            'shopName' => 'required|string',
            'shopCategoryId' => 'required|integer|digits_between:1,20',
        ];
    }
}
