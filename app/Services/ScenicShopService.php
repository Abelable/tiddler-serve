<?php

namespace App\Services;

use App\Models\ScenicShop;
use App\Utils\Inputs\Admin\ShopListInput;
use App\Utils\Inputs\ScenicProviderInput;

class ScenicShopService extends BaseService
{
    public function createShop(int $userId, int $providerId, ScenicProviderInput $input)
    {
        $shop = ScenicShop::new();
        $shop->user_id = $userId;
        $shop->provider_id = $providerId;
        $shop->type = $input->shopType;
        $shop->avatar = $input->shopAvatar;
        $shop->name = $input->shopName;
        $shop->cover = $input->shopCover;
        $shop->save();
        return $shop;
    }

    public function getShopList(ShopListInput $input, $columns = ['*'])
    {
        $query = ScenicShop::query();
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
        return ScenicShop::query()->find($id, $columns);
    }

    public function getShopByMerchantId(int $merchantId, $columns = ['*'])
    {
        return ScenicShop::query()->where('merchant_id', $merchantId)->first($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return ScenicShop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return ScenicShop::query()->where('user_id', $userId)->first($columns);
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
