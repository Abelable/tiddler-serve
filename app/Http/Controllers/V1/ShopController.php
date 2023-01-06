<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\ShopCategoryService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantSettleInInput;
use Yansongda\LaravelPay\Facades\Pay;

class ShopController extends Controller
{
    protected $except = ['getShopInfo'];

    public function categoryOptions()
    {
        $options = ShopCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function addMerchant()
    {
        /** @var MerchantSettleInInput $input */
        $input = MerchantSettleInInput::new();

        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请');
        }

        $merchant = Merchant::new();
        $merchant->user_id = $this->userId();
        $merchant->type = $input->type;
        if ($input->type == 2) {
            $merchant->company_name = $input->companyName;
            $merchant->business_license_photo = $input->businessLicensePhoto;
        }
        $merchant->region_desc = $input->regionDesc;
        $merchant->region_code_list = $input->regionCodeList;
        $merchant->address_detail = $input->addressDetail;
        $merchant->name = $input->name;
        $merchant->mobile = $input->mobile;
        $merchant->email = $input->email;
        $merchant->id_card_number = $input->idCardNumber;
        $merchant->id_card_front_photo = $input->idCardFrontPhoto;
        $merchant->id_card_back_photo = $input->idCardBackPhoto;
        $merchant->hold_id_card_photo = $input->holdIdCardPhoto;
        $merchant->bank_card_owner_name = $input->bankCardOwnerName;
        $merchant->bank_card_number = $input->bankCardNumber;
        $merchant->bank_name = $input->bankName;
        $merchant->shop_name = $input->shopName;
        $merchant->shop_category_id = $input->shopCategoryId;
        $merchant->save();

        return $this->success();
    }

    public function merchantStatusInfo()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId(), ['id', 'status', 'failure_reason', 'type']);
        return $this->success($merchant ?: '');
    }

    public function payDeposit()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order = MerchantOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteMerchant()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '商家信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }

    public function shopInfo()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'name', 'type', 'avatar', 'cover'];
        $shop = ShopService::getInstance()->getShopById($id, $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'type', 'avatar', 'cover'];
        $shop = ShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }
}
