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
use App\Utils\Inputs\ShopInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ShopController extends Controller
{
    protected $except = ['shopInfo'];

    public function categoryOptions()
    {
        $options = ShopCategoryService::getInstance()
            ->getCategoryOptions(['id', 'name', 'deposit', 'adapted_merchant_types']);
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
            if ($merchant->status == 3) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                MerchantService::getInstance()->updateMerchant($merchant, $input);
            } else {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }
        } else {
            DB::transaction(function () use ($input) {
                $merchant = MerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = ShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                ShopDepositPaymentLogService::getInstance()
                    ->createLog($this->userId(), $merchant->id, $shop->id, $shop->deposit);
            });
        }

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

    public function merchantInfo()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '暂无商家信息');
        }

        $shop = ShopService::getInstance()->getShopByUserId($this->userId());
        $merchant['shopCover'] = $shop->cover;
        $merchant['shopLogo'] = $shop->logo;
        $merchant['shopName'] = $shop->name;
        $merchant['shopCategoryIds'] = array_map('intval', json_decode($shop->category_ids));
        $merchant['deposit'] = $shop->deposit;

        return $this->success($merchant);
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
        $columns = ['id', 'category_ids', 'name', 'type', 'logo', 'bg'];
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
        $shop = ShopService::getInstance()->getShopByUserId($this->userId());
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您非商家，暂无店铺');
        }
        $shop->category_ids = json_decode($shop->category_ids);
        $shop->open_time_list = json_decode($shop->open_time_list);

        return $this->success($shop);
    }

    public function updateShopInfo()
    {
        /** @var ShopInput $input */
        $input = ShopInput::new();

        $shop = ShopService::getInstance()->getShopByUserId($this->userId());
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您非商家，暂无店铺');
        }

        ShopService::getInstance()->updateShopInfo($shop, $input);

        return $this->success();
    }

    public function expressOptions()
    {
        $options = ExpressService::getInstance()->getExpressOptions(['id', 'code', 'name']);
        return $this->success($options);
    }
}
