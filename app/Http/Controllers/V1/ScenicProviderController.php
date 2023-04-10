<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\ScenicProvider;
use App\Services\ExpressService;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicProviderService;
use App\Services\ShopCategoryService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantSettleInInput;
use App\Utils\Inputs\ScenicProviderInput;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicProviderController extends Controller
{
    public function settleIn()
    {
        /** @var ScenicProviderInput $input */
        $input = ScenicProviderInput::new();

        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId());
        if (!is_null($provider)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交申请，请勿重复操作');
        }

        $provider = ScenicProvider::new();
        $provider->user_id = $this->userId();
        $provider->company_name = $input->companyName;
        $provider->business_license_photo = $input->businessLicensePhoto;
        $provider->region_desc = $input->regionDesc;
        $provider->region_code_list = $input->regionCodeList;
        $provider->address_detail = $input->addressDetail;
        $provider->name = $input->name;
        $provider->mobile = $input->mobile;
        $provider->email = $input->email;
        $provider->id_card_number = $input->idCardNumber;
        $provider->id_card_front_photo = $input->idCardFrontPhoto;
        $provider->id_card_back_photo = $input->idCardBackPhoto;
        $provider->hold_id_card_photo = $input->holdIdCardPhoto;
        $provider->bank_card_owner_name = $input->bankCardOwnerName;
        $provider->bank_card_number = $input->bankCardNumber;
        $provider->bank_name = $input->bankName;
        $provider->shop_name = $input->shopName;
        $provider->save();

        return $this->success();
    }

    public function statusInfo()
    {
        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        return $this->success($provider ?: '');
    }

    public function payDeposit()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order =  ScenicProviderOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteProvider()
    {
        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId());
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $provider->delete();
        return $this->success();
    }
}
