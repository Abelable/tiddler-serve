<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Services\ExpressService;
use App\Services\ShopDepositPaymentLogService;
use App\Services\MerchantService;
use App\Services\ShopCategoryService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ShopController extends Controller
{
    protected $except = ['shopInfo'];

    public function categoryOptions()
    {
        $options = ShopCategoryService::getInstance()->getCategoryOptions(['id', 'name', 'deposit', 'adapted_merchant_types']);
        $options = $options->map(function (ShopCategory $category) {
            $category->adapted_merchant_types = json_decode($category->adapted_merchant_types);
            return $category;
        });
        return $this->success($options);
    }

    public function addMerchant()
    {
        /** @var MerchantInput $input */
        $input = MerchantInput::new();

        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请');
        }

        DB::transaction(function () use ($input) {
            $merchant = MerchantService::getInstance()->createMerchant($input, $this->userId());
            $shop = ShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
            ShopDepositPaymentLogService::getInstance()
                ->createLog($this->userId(), $merchant->id, $shop->id, $shop->deposit);
        });

        return $this->success();
    }

    public function merchantStatusInfo()
    {
        // todo 目前一个商家对应一个店铺，暂时可以用商家id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        $shop = ShopService::getInstance()->getShopByUserId($this->userId());

        return $this->success($merchant ? [
            'id' => $merchant->id,
            'status' => $merchant->status,
            'failureReason' => $merchant->failure_reason,
            'deposit' => $shop->deposit,
            'shopId' => $shop->id
        ] : null);
    }

    public function payDeposit()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $wxPayOrder = ShopService::getInstance()->createWxPayOrder($shopId, $this->userId(), $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($wxPayOrder);
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
        $columns = ['id', 'category_ids', 'name', 'type', 'logo', 'cover'];
        $shop = ShopService::getInstance()->getShopById($id, $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        $shop->category_ids = json_decode($shop->category_ids);
        return $this->success($shop);
    }

    public function myShopInfo()
    {
        // todo 目前一个用户对应一个商家，一个商家对应一个店铺，可以暂时用用户id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $columns = ['id', 'category_ids', 'name', 'type', 'logo', 'cover'];
        $shop = ShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        $shop->category_ids = json_decode($shop->category_ids);
        return $this->success($shop);
    }

    public function expressOptions()
    {
        $options = ExpressService::getInstance()->getExpressOptions(['id', 'code', 'name']);
        return $this->success($options);
    }
}
