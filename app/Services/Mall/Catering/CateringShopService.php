<?php

namespace App\Services\Mall\Catering;

use App\Models\CateringShop;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;
use App\Utils\Inputs\CateringMerchantInput;
use App\Utils\Inputs\ShopInput;

class CateringShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, CateringMerchantInput $input)
    {
        $shop = CateringShop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->type = $input->shopType;
        $shop->deposit = $input->deposit;
        $shop->logo = $input->shopLogo;
        $shop->name = $input->shopName;
        if (!empty($input->shopBg)) {
            $shop->bg = $input->shopBg;
        }
        $shop->save();
        return $shop;
    }

    public function updateShop(CateringShop $shop, ShopInput $input)
    {
        $shop->bg = $input->bg ?? '';
        $shop->logo = $input->logo;
        $shop->name = $input->name;
        $shop->save();
        return $shop;
    }

    public function getShopList(ShopPageInput $input, $columns = ['*'])
    {
        $query = CateringShop::query();
        if (!empty($input->name)) {
            $query = $query->where('name', $input->name);
        }
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopById(int $id, $columns = ['*'])
    {
        return CateringShop::query()->find($id, $columns);
    }

    public function getShopByMerchantId(int $merchantId, $columns = ['*'])
    {
        return CateringShop::query()->where('merchant_id', $merchantId)->first($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return CateringShop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserShopByShopId($userId, $shopId, $columns = ['*'])
    {
        return CateringShop::query()->where('user_id', $userId)->find($shopId, $columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return CateringShop::query()->where('user_id', $userId)->first($columns);
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
            'attach' => 'catering_shop_id:' . $shopId,
            'total_fee' => bcmul($shop->deposit, 100),
            'openid' => $openid
        ];
    }

    public function paySuccess(int $shopId)
    {
        $shop = $this->getShopById($shopId);
        if (is_null($shop)) {
            $this->throwBadArgumentValue();
        }
        if ($shop->status != 0) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '店铺保证金已支付，请勿重复操作');
        }
        $shop->status = 1;
        $shop->save();
        return $shop;
    }
}
