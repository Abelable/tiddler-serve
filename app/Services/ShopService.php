<?php

namespace App\Services;

use App\Models\Shop;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;
use App\Utils\Inputs\MerchantInput;

class ShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, MerchantInput $input)
    {
        $shop = Shop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->deposit = $input->deposit;
        $shop->type = $input->type;
        $shop->category_ids = json_encode($input->shopCategoryIds);
        $shop->logo = $input->shopLogo;
        $shop->name = $input->shopName;
        $shop->cover = $input->shopCover;
        $shop->save();
        return $shop;
    }

    public function getShopPage(ShopPageInput $input, $columns = ['*'])
    {
        $query = Shop::query();
        if (!empty($input->name)) {
            $query = $query->where('name', $input->name);
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopById(int $id, $columns = ['*'])
    {
        return Shop::query()->find($id, $columns);
    }

    public function getShopByMerchantId(int $merchantId, $columns = ['*'])
    {
        // todo 目前一个用户对应一个商家，一个商家对应一个店铺，可以暂时用商户id获取店铺，之后一个商家有多个店铺，该方法需要删除
        return Shop::query()->where('merchant_id', $merchantId)->first($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return Shop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserShopByShopId($userId, $shopId, $columns = ['*'])
    {
        return Shop::query()->where('user_id', $userId)->find($shopId, $columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        // todo 目前一个用户对应一个商家，一个商家对应一个店铺，可以暂时用用户id获取店铺，之后一个商家有多个店铺，该方法需要删除
        return Shop::query()->where('user_id', $userId)->first($columns);
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
            'body' => 'shop_id:' . $shopId,
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

    public function getOptions($columns = ['*'])
    {
        return Shop::query()->where('status', 1)->get($columns);
    }
}
