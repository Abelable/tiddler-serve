<?php

namespace App\Services;

use App\Models\Goods;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;

class GoodsService extends BaseService
{
    public function getListByCategoryId($categoryId, PageInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (!empty($categoryId)) {
            $query = $query->where('category_id', $categoryId);
        }
        return $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy($input->sort, $input->order)
                ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTopListByCategoryIds(array $goodsIds, array $categoryIds, $limit, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);

        if (!empty($categoryIds)) {
            $query = $query->whereIn('category_id', $categoryIds);
        }
        if (!empty($goodsIds)) {
            $query = $query->whereNotIn('id', $goodsIds);
        }
        return $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get($columns);
    }

    public function getTopListByShopId($goodsId, $shopId, $limit, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $shopId)
            ->where('id', '!=', $goodsId)
            ->orderBy('sales_volume', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get($columns);
    }

    public function getListTotal($userId, $status)
    {
        return Goods::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getGoodsListByShopId($shopId, PageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $shopId)
            ->orderBy('sales_volume', 'desc')
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

    public function getGoodsListByIds(arr $ids, $columns=['*'])
    {
        return Goods::query()->whereIn('id', $ids)->get($columns);
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
        if (!is_null($input->status)) {
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
