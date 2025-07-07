<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ShopScenicSpotService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicMerchantService;
use App\Services\ScenicService;
use App\Services\ScenicShopDepositPaymentLogService;
use App\Services\ScenicShopDepositService;
use App\Services\ScenicShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicMerchantInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicMerchantController extends Controller
{
    public function settleIn()
    {
        /** @var ScenicMerchantInput $input */
        $input = ScenicMerchantInput::new();

        $provider = ScenicMerchantService::getInstance()->getProviderByUserId($this->userId());
        if (!is_null($provider)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交申请，请勿重复操作');
        }

        DB::transaction(function () use ($input) {
            $provider = ScenicMerchantService::getInstance()->createProvider($input, $this->userId());
            $shop = ScenicShopService::getInstance()->createShop($this->userId(), $provider->id, $input);
            // todo 暂时设置保证金金额为10000
            ScenicShopDepositPaymentLogService::getInstance()
                ->createLog($this->userId(), $provider->id, $shop->id, 10000);
            ScenicShopDepositService::getInstance()->createShopDeposit($shop->id);
        });

        return $this->success();
    }

    public function statusInfo()
    {
        $provider = ScenicMerchantService::getInstance()->getProviderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        $providerOrder = ScenicProviderOrderService::getInstance()->getOrderByUserId($this->userId(), ['id']);

        return $this->success($provider ? [
            'id' => $provider->id,
            'status' => $provider->status,
            'failureReason' => $provider->failure_reason,
            'orderId' => $providerOrder ? $providerOrder->id : 0
        ] : null);
    }

    public function payDeposit()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order = ScenicProviderOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteProvider()
    {
        $provider = ScenicMerchantService::getInstance()->getProviderByUserId($this->userId());
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $provider->delete();
        return $this->success();
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'type', 'logo', 'cover'];
        $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function scenicListTotals()
    {
        return $this->success([
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 1),
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 0),
            ShopScenicSpotService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function providerScenicSpotList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ShopScenicSpotService::getInstance()->getUserSpotList($this->userId(), $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerScenicSpotList = collect($page->items());
        $scenicIds = $providerScenicSpotList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerScenicSpotList->map(function (ShopScenicSpot $providerScenicSpot) use ($scenicList) {
            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($providerScenicSpot->scenic_id);
            $providerScenicSpot['scenic_image'] = json_decode($scenic->image_list)[0];
            $providerScenicSpot['scenic_name'] = $scenic->name;
            $providerScenicSpot['scenic_level'] = $scenic->level;
            $providerScenicSpot['scenic_address'] = $scenic->address;
            return $providerScenicSpot;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function applyScenicSpot()
    {
        $scenicIds = $this->verifyArrayNotEmpty('scenicIds');
        $scenicMerchant = $this->user()->scenicMerchant;
        if (is_null($scenicMerchant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }
        ShopScenicSpotService::getInstance()->createScenicList($this->userId(), $scenicMerchant->id, $scenicIds);
        return $this->success();
    }

    public function deleteShopScenicSpot()
    {
        $id = $this->verifyRequiredId('id');
        $spot = ShopScenicSpotService::getInstance()->getUserSpotById($this->userId(), $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $spot->delete();
        return $this->success();
    }

    public function providerScenicOptions()
    {
        $scenicIds = ShopScenicSpotService::getInstance()->getUserScenicOptions($this->userId())->pluck('scenic_id')->toArray();
        $scenicOptions = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name']);
        return $this->success($scenicOptions);
    }
}
