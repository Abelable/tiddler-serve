<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ExpressService;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\ShopCategoryService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantSettleInInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ShopController extends Controller
{
    protected $except = ['shopInfo'];

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

        DB::transaction(function () use ($input) {
            $merchant = MerchantService::getInstance()->createMerchant($input, $this->userId());
            ShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
        });

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

    public function expressOptions()
    {
        $options = ExpressService::getInstance()->getExpressOptions(['id', 'code', 'name']);
        return $this->success($options);
    }
}
