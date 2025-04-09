<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Shop;
use App\Models\ShopDepositPaymentLog;
use App\Services\MerchantService;
use App\Services\ShopDepositPaymentLogService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;
use App\Utils\Inputs\PageInput;

class ShopController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ShopPageInput $input */
        $input = ShopPageInput::new();
        $page = ShopService::getInstance()->getShopPage($input);
        $shopList = collect($page->items());
        $shopIds = $shopList->pluck('id')->toArray();

        $depositPaymentLogs = ShopDepositPaymentLogService::getInstance()
            ->getLogListByShopIds($shopIds)->keyBy('shop_id');
        $list = $shopList->map(function (Shop $shop) use ($depositPaymentLogs) {
            /** @var ShopDepositPaymentLog $depositPaymentLog */
            $depositPaymentLog = $depositPaymentLogs->get($shop->id);
            if (!is_null($depositPaymentLog)) {
                unset($depositPaymentLog->id);
                unset($depositPaymentLog->user_id);
                unset($depositPaymentLog->merchant_id);
            }
            $shop['depositPaymentLog'] = $depositPaymentLog;
            return $shop;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shop = ShopService::getInstance()->getShopById($id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function depositPaymentLogs()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = ShopDepositPaymentLogService::getInstance()->getLogPage($input);
        $logList = collect($page->items());

        $merchantIds = $logList->pluck('merchant_id')->toArray();
        $merchantList = MerchantService::getInstance()->getMerchantListByIds($merchantIds)->keyBy('id');

        $list = $logList->map(function (ShopDepositPaymentLog $log) use ($merchantList) {
            /** @var Merchant $merchant */
            $merchant = $merchantList->get($log->merchant_id);
            $log['merchant_type'] = $merchant->type;
            if ($merchant->type == 1) {
                $log['name'] = $merchant->name;
            } else {
                $log['company_name'] = $merchant->company_name;
            }
            return $log;
        });

        return $this->success($this->paginate($page, $list));
    }
}
