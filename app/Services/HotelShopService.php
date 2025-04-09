<?php

namespace App\Services;

use App\Models\HotelShop;
use App\Utils\Inputs\Admin\ShopPageInput;
use App\Utils\Inputs\HotelProviderInput;

class HotelShopService extends BaseService
{
    public function createShop(int $userId, int $providerId, HotelProviderInput $input)
    {
        $shop = HotelShop::new();
        $shop->user_id = $userId;
        $shop->provider_id = $providerId;
        $shop->type = $input->shopType;
        $shop->avatar = $input->shopAvatar;
        $shop->name = $input->shopName;
        if (!empty($input->shopCover)) {
            $shop->cover = $input->shopCover;
        }
        $shop->save();
        return $shop;
    }

    public function getShopList(ShopPageInput $input, $columns = ['*'])
    {
        $query = HotelShop::query();
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
        return HotelShop::query()->find($id, $columns);
    }

    public function getShopByProviderId(int $providerId, $columns = ['*'])
    {
        return HotelShop::query()->where('provider_id', $providerId)->first($columns);
    }

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return HotelShop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return HotelShop::query()->where('user_id', $userId)->first($columns);
    }

    public function paySuccess(int $providerId)
    {
        $shop = $this->getShopByProviderId($providerId);
        if (is_null($shop)) {
            $this->throwBadArgumentValue();
        }
        $shop->status = 1;
        $shop->save();
        return $shop;
    }
}
