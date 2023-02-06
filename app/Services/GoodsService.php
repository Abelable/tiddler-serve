<?php

namespace App\Services;

use App\Models\Goods;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopGoodsListInput;

class GoodsService extends BaseService
{
    public function getList(PageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListTotal($userId, $status)
    {
        return Goods::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getShopGoodsList(ShopGoodsListInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $input->shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsListByStatus($userId, MerchantGoodsListInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsById($id, $columns=['*'])
    {
        return Goods::query()->find($id, $columns);
    }

    public function getMerchantGoodsList(GoodsListInput $input, $columns=['*'])
    {
        $query = Goods::query()
            ->where('shop_id', '!=', 0)
            ->whereIn('status', [0, 1, 2]);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOwnerGoodsList(GoodsListInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('shop_id', 0);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }
}
