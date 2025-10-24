<?php

namespace App\Utils\Inputs;

class ScenicMerchantInput extends BaseInput
{
    public $companyName;
    public $regionDesc;
    public $regionCodeList;
    public $addressDetail;
    public $businessLicensePhoto;
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
    public $categoryId;
    public $deposit;
    public $shopBg;
    public $shopLogo;
    public $shopName;
    public function rules()
    {
        return [
            'companyName' => 'required|string',
            'regionDesc' => 'required|string',
            'regionCodeList' => 'required|string',
            'addressDetail' => 'required|string',
            'businessLicensePhoto' => 'required|string',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
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
