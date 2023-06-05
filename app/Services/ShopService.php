<?php

namespace App\Services;

use App\Models\Shop;
use App\Utils\Inputs\Admin\ShopListInput;
use App\Utils\Inputs\MerchantSettleInInput;

class ShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, MerchantSettleInInput $input)
    {
        $shop = Shop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->type = $input->type;
        $shop->category_id = $input->shopCategoryId;
        $shop->avatar = $input->shopAvatar;
        $shop->name = $input->shopName;
        $shop->cover = $input->shopCover;
        $shop->save();
        return $shop;
    }

    public function getShopList(ShopListInput $input, $columns = ['*'])
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
        return Shop::query()->where('merchant_id', $merchantId)->first($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return Shop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return Shop::query()->where('user_id', $userId)->first($columns);
    }

    public function paySuccess(int $merchantId)
    {
        $shop = $this->getShopByMerchantId($merchantId);
        if (is_null($shop)) {
            $this->throwBadArgumentValue();
        }
        $shop->status = 1;
        $shop->save();
        return $shop;
    }
}
