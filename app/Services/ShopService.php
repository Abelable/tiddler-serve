<?php

namespace App\Services;

use App\Models\Shop;
use App\Utils\Inputs\Admin\ShopListInput;

class ShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, int $type, string $name, int $categoryId)
    {
        $shop = Shop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->type = $type;
        $shop->name = $name;
        $shop->category_id = $categoryId;
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

    public function getShopListByIds(array $ids, $columns = ['*'])
    {
        return Shop::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return Shop::query()->where('user_id', $userId)->first($columns);
    }
}
