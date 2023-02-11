<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Shop;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsAllListInput;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;

class GoodsService extends BaseService
{
    public function getAllList(GoodsAllListInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('sales_volume', 'desc')
                ->orderByRaw("CASE WHEN shop_id = 0 THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
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
                ->orderByRaw("CASE WHEN shop_id = 0 THEN 0 ELSE 1 END")
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

    public function getGoodsListByIds($ids, $columns=['*'])
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

    public function getRecommendGoodsList
    (
        $goodsIds,
        $categoryIds,
        $limit = 10,
        $columns=['id', 'shop_id', 'image', 'name', 'price', 'market_price', 'sales_volume']
    )
    {
        $goodsList = $this->getTopListByCategoryIds($goodsIds, $categoryIds, $limit, $columns);
        return $this->addShopInfoToGoodsList($goodsList);
    }

    public function addShopInfoToGoodsList($goodsList)
    {
        $shopIds = $goodsList->pluck('shop_id')->toArray();
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name'])->keyBy('id');
        $list = $goodsList->map(function (Goods $goods) use ($shopList) {
            if ($goods->shop_id != 0) {
                /** @var Shop $shop */
                $shop = $shopList->get($goods->shop_id);
                $goods['shop_info'] = $shop;
            }
            unset($goods->shop_id);
            return $goods;
        });
        return $list;
    }
}
