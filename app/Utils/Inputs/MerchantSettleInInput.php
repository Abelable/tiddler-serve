<?php

namespace App\Utils\Inputs;

class MerchantSettleInInput extends BaseInput
{
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
    public $bankCardOwnerName;
    public $bankCardNumber;
    public $bankName;
    public $shopName;
    public $shopCategoryId;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'companyName' => 'required_if:type,2,string',
            'regionList' => 'required|string',
            'addressDetail' => 'required|string',
            'businessLicensePhoto' => 'required_if:type,2,string',
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
            'shopName' => 'required|string',
            'shopCategoryId' => 'required|integer|digits_between:1,20',
        ];
    }
}
