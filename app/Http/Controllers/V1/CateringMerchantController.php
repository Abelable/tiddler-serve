<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopDepositPaymentLogService;
use App\Services\Mall\Catering\CateringShopDepositService;
use App\Services\Mall\Catering\CateringShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CateringMerchantInput;
use Illuminate\Support\Facades\DB;

class CateringMerchantController extends Controller
{
    public function settleIn()
    {
        /** @var CateringMerchantInput $input */
        $input = CateringMerchantInput::new();

        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());

        if (!is_null($merchant)) {
            if ($merchant->status == 3) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                CateringMerchantService::getInstance()->updateMerchant($merchant, $input);
            } else {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }
        } else {
            DB::transaction(function () use ($input) {
                $merchant = CateringMerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = CateringShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                CateringShopDepositPaymentLogService::getInstance()
                    ->createLog($this->userId(), $merchant->id, $shop->id, $shop->deposit);
                CateringShopDepositService::getInstance()->createShopDeposit($shop->id);
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        // todo 目前一个商家对应一个店铺，暂时可以用商家id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $shop = CateringShopService::getInstance()->getShopByUserId($this->userId());

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
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = CateringShopService::getInstance()->getShopByUserId($this->userId());
            $merchant['shopType'] = $shop->type;
            $merchant['deposit'] = $shop->deposit;
            $merchant['shopBg'] = $shop->bg;
            $merchant['shopLogo'] = $shop->logo;
            $merchant['shopName'] = $shop->name;
        }
        return $this->success($merchant);
    }

    public function delete()
    {
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮服务商信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
