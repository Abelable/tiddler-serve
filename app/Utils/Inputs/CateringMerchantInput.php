<?php

namespace App\Utils\Inputs;

class CateringMerchantInput extends BaseInput
{
    public $type;
    public $companyName;
    public $regionDesc;
    public $regionCodeList;
    public $addressDetail;
    public $businessLicensePhoto;
    public $hygienicLicensePhoto;
    public $name;
    public $mobile;
    public $email;
    public $idCardNumber;
    public $idCardFrontPhoto;
    public $idCardBackPhoto;
    public $holdIdCardPhoto;
    public $bankCardOwnerName;
    public $bankCardNumber;
    public $bankName;
    public $shopType;
    public $deposit;
    public $shopBg;
    public $shopLogo;
    public $shopName;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'companyName' => 'required_if:type,2',
            'regionDesc' => 'required|string',
            'regionCodeList' => 'required|string',
            'addressDetail' => 'required|string',
            'businessLicensePhoto' => 'required|string',
            'hygienicLicensePhoto' => 'required|string',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'email' => 'required|email',
            'idCardNumber' => 'required|string',
            'idCardFrontPhoto' => 'required|string',
            'idCardBackPhoto' => 'required|string',
            'holdIdCardPhoto' => 'required|string',
            'bankCardOwnerName' => 'required|string',
            'bankCardNumber' => 'required|string',
            'bankName' => 'required|string',
            'shopType' => 'required|integer|in:1,2,3',
            'deposit' => 'required|numeric',
            'shopBg' => 'string',
            'shopLogo' => 'required|string',
            'shopName' => 'required|string',
        ];
    }
}
