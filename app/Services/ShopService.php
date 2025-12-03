<?php

namespace App\Services;

use App\Models\Shop;
use App\Utils\CodeResponse;
use App\Utils\Enums\MerchantType;
use App\Utils\Inputs\Admin\ShopPageInput;
use App\Utils\Inputs\MerchantInput;
use App\Utils\Inputs\ShopInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Log;

class ShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, MerchantInput $input)
    {
        $shop = Shop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->type = $input->type;
        $shop->deposit = $input->deposit;
        $shop->category_ids = json_encode($input->shopCategoryIds);
        $shop->logo = $input->shopLogo;
        $shop->name = $input->shopName;
        $shop->bg = $input->shopBg;
        $shop->save();
        return $shop;
    }

    public function updateShopInfo(Shop $shop, ShopInput $input)
    {
        $shop->bg = $input->bg ?? '';
        $shop->logo = $input->logo;
        $shop->name = $input->name;
        $shop->brief = $input->brief ?? '';
        $shop->owner_name = $input->ownerName ?? '';
        $shop->mobile = $input->mobile ?? '';
        $shop->address_detail = $input->addressDetail ?? '';
        $shop->longitude = $input->longitude ?? 0;
        $shop->latitude = $input->latitude ?? 0;
        $shop->open_time_list = $input->openTimeList ? json_decode($input->openTimeList, true) : '[]';
        $shop->save();
        return $shop;
    }

    public function getShopPage(ShopPageInput $input, $columns = ['*'])
    {
        $query = Shop::query();
        if (!empty($input->name)) {
            $query = $query->where('name', $input->name);
        }
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopById(int $id, $columns = ['*'])
    {
        return Shop::query()->find($id, $columns);
    }

    public function getShopListByMerchantId(int $merchantId, $columns = ['*'])
    {
        return Shop::query()->where('merchant_id', $merchantId)->get($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return Shop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserShopByShopId($userId, $shopId, $columns = ['*'])
    {
        return Shop::query()->where('user_id', $userId)->where('shop_id', $shopId)->first($columns);
    }

    public function getFirstShop(int $merchantId, $columns = ['*'])
    {
        return Shop::query()->where('merchant_id', $merchantId)->first($columns);
    }

    public function createWxPayOrder($shopId, $userId, string $openid)
    {
        $shop = $this->getUserShopByShopId($userId, $shopId);
        if (is_null($shop)) {
            $this->throwBadArgumentValue();
        }
        if ($shop->status != 0) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '店铺保证金已支付，请勿重复操作');
        }

        return [
            'out_trade_no' => time(),
            'body' => '店铺保证金',
            'attach' => 'shop_id:' . $shopId,
            'total_fee' => bcmul($shop->deposit, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $shopId = $data['attach'] ? str_replace('shop_id:', '', $data['attach']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $shop = $this->getShopById($shopId);
        if (is_null($shop)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '店铺不存在');
        }

        $shopDeposit = ShopDepositService::getInstance()->getShopDeposit($shopId);
        $pendingPaymentAmount = bcsub($shop->deposit, $shopDeposit->balance, 2);

        if (bccomp($actualPaymentAmount, $pendingPaymentAmount, 2) != 0) {
            $errMsg = "支付回调异常，店铺保证金支付金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，待支付金额：{$pendingPaymentAmount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        // 商家入驻场景
        if ($shop->status == 0) {
            // 更新店铺状态
            $shop->status = 1;
            $shop->save();

            // 更新商家状态
            MerchantService::getInstance()->settled($shop->merchant_id);

            // 邀请商家入驻活动
            $userTask = UserTaskService::getInstance()
                ->getByMerchantId(MerchantType::GOODS, $shop->merchant_id, 1);
            if (!is_null($userTask)) {
                $userTask->step = 2;
                $userTask->save();
            }
        }

        // 更新店铺保证金
        ShopDepositService::getInstance()->updateDeposit($shop->id, 1, $actualPaymentAmount, $payId);

        // 同步微信后台非物流订单
        sleep(10); // todo 延迟10s执行（改为延迟任务队列）
        $openid = UserService::getInstance()->getUserById($shop->user_id)->openid;
        WxMpServe::new()->notifyNoShipment($openid, $payId, '店铺保证金', 3);
    }

    public function getOptions($columns = ['*'])
    {
        return Shop::query()->where('status', 1)->get($columns);
    }
}
