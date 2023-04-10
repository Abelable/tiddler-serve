<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\ScenicSpot;
use App\Models\Shop;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\Admin\ScenicListInput;
use App\Utils\Inputs\GoodsAllListInput;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ScenicAllListInput;

class ScenicService extends BaseService
{
    public function getScenicList(ScenicListInput $input, $columns=['*'])
    {
        $query = ScenicSpot::query();
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

    public function getScenicById($id, $columns=['*'])
    {
        return ScenicSpot::query()->find($id, $columns);
    }

    public function getAllList(ScenicAllListInput $input, $columns=['*'])
    {
        $query = ScenicSpot::query()->where('status', 1);
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
                ->orderBy('sales_commission_rate', 'desc')
                ->orderBy('promotion_commission_rate', 'desc')
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
                ->orderBy('sales_commission_rate', 'desc')
                ->orderBy('promotion_commission_rate', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get($columns);
    }

    public function getShopTopList($goodsId, $shopId, $limit, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $shopId)
            ->where('id', '!=', $goodsId)
            ->orderBy('sales_volume', 'desc')
            ->orderBy('sales_commission_rate', 'desc')
            ->orderBy('promotion_commission_rate', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get($columns);
    }

    public function getShopGoodsList($shopId, PageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $shopId)
            ->orderBy('sales_volume', 'desc')
            ->orderBy('sales_commission_rate', 'desc')
            ->orderBy('promotion_commission_rate', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserGoodsList($userId, PageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('user_id', $userId)
            ->where('status', 1)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLiveUnlistedGoodsList($userId, $goodsIds, $columns=['*'])
    {
        return Goods::query()
            ->where('user_id', $userId)
            ->where('status', 1)
            ->whereNotIn('id', $goodsIds)
            ->get($columns);
    }

    public function getListTotal($userId, $status)
    {
        return Goods::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getGoodsListByStatus($userId, MerchantGoodsListInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }



    public function getOnSaleGoods($id, $columns=['*'])
    {
        return Goods::query()->where('status', 1)->find($id, $columns);
    }

    public function getGoodsListByIds($ids, $columns=['*'])
    {
        return Goods::query()->whereIn('id', $ids)->get($columns);
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

    public function reduceStock($id, $number, $selectedSkuIndex = -1)
    {
        $goods = $this->getOnSaleGoods($id);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '商品不存在');
        }

        $skuList = json_decode($goods->sku_list);

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK, '所选规格库存不足');
            }
            // 减规格库存
            $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock - $number;
            $goods->sku_list = json_encode($skuList);
        } else {
            if ($goods->stock == 0 || $number > $goods->stock) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK, '商品库存不足');
            }
        }
        $goods->stock = $goods->stock - $number;

        return $goods->cas();
    }

    public function addStock($id, $number, $selectedSkuIndex = -1)
    {
        $goods = $this->getOnSaleGoods($id);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '商品不存在');
        }

        $skuList = json_decode($goods->sku_list);

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock + $number;
            $goods->sku_list = json_encode($skuList);
        }
        $goods->stock = $goods->stock + $number;

        return $goods->cas();
    }
}
