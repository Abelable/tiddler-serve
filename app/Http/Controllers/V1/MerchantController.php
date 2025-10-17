<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ShopDepositPaymentLogService;
use App\Services\MerchantService;
use App\Services\ShopDepositService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantInput;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    public function settleIn()
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
                ShopDepositService::getInstance()->createShopDeposit($shop->id);
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        // todo 目前一个商家对应一个店铺，暂时可以用商家id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $shop = ShopService::getInstance()->getShopByUserId($this->userId());

        return $this->success($merchant ? [
            'id' => $merchant->id,
            'status' => $merchant->status,
            'failureReason' => $merchant->failure_reason,
            'deposit' => $shop->deposit,
            'shopId' => $shop->id
        ] : null);
    }

    public function info()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = ShopService::getInstance()->getShopByUserId($this->userId());
            $merchant['shopCategoryIds'] = array_map('intval', json_decode($shop->category_ids));
            $merchant['deposit'] = $shop->deposit;
            $merchant['shopBg'] = $shop->bg;
            $merchant['shopLogo'] = $shop->logo;
            $merchant['shopName'] = $shop->name;
        }
        return $this->success($merchant);
    }

    public function delete()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '商家信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
