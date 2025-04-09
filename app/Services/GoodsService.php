<?php

namespace App\Services;

use App\Models\Goods;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class GoodsService extends BaseService
{
    public function getAllList(GoodsPageInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (!empty($input->goodsIds)) {
            $query = $query->orderByRaw(DB::raw("FIELD(id, " . implode(',', $input->goodsIds) . ") DESC"));
        }
        if (!empty($input->shopCategoryId)) {
            $query = $query->where('shop_category_id', $input->shopCategoryId);
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

    public function search($keywords, GoodsPageInput $input)
    {
        $query = Goods::search($keywords)->where('status', 1);
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy('sales_commission_rate', 'desc')
                ->orderBy('promotion_commission_rate', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit,'page', $input->page);
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

    public function getShopOnSaleGoodsList($shopId, PageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('status', 1)
            ->where('shop_id', $shopId)
            ->orderBy('sales_commission_rate', 'desc')
            ->orderBy('sales_volume', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopGoodsList($shopId, StatusPageInput $input, $columns=['*'])
    {
        return Goods::query()
            ->where('shop_id', $shopId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getSelfGoodsList($columns = ['*'])
    {
        return Goods::query()->where('shop_id', 0)->get($columns);
    }

    public function getLiveUnlistedGoodsList($shopId, $goodsIds, $columns=['*'])
    {
        return Goods::query()
            ->where('shop_id', $shopId)
            ->where('status', 1)
            ->whereNotIn('id', $goodsIds)
            ->get($columns);
    }

    public function getListTotal($shopId, $status)
    {
        return Goods::query()->where('shop_id', $shopId)->where('status', $status)->count();
    }

    public function getGoodsById($id, $columns=['*'])
    {
        return Goods::query()->find($id, $columns);
    }

    public function getShopGoods($shopId, $id, $columns = ['*'])
    {
        return Goods::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getOnSaleGoods($id, $columns=['*'])
    {
        return Goods::query()->where('status', 1)->find($id, $columns);
    }

    public function getGoodsListByIds($ids, $columns=['*'])
    {
        return Goods::query()->whereIn('id', $ids)->get($columns);
    }

    public function getAdminGoodsList(GoodsListInput $input, $columns=['*'])
    {
        $query = Goods::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->shopCategoryId)) {
            $query = $query->where('shop_category_id', $input->shopCategoryId);
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->shopId)) {
            $query = $query->where('shop_id', $input->shopId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecommendGoodsList
    (
        $goodsIds,
        $categoryIds,
        $limit = 10,
        $columns=['id', 'shop_id', 'cover', 'name', 'price', 'market_price', 'sales_volume']
    )
    {
        $goodsList = $this->getTopListByCategoryIds($goodsIds, $categoryIds, $limit, $columns);
        return $this->addShopInfoToGoodsList($goodsList);
    }

    public function addShopInfoToGoodsList($goodsList)
    {
        $shopIds = $goodsList->pluck('shop_id')->toArray();
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name'])->keyBy('id');
        return $goodsList->map(function (Goods $goods) use ($shopList) {
            return [
                'id' => $goods->id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'price' => $goods->price,
                'marketPrice' => $goods->market_price,
                'salesVolume' => $goods->sales_volume,
                'shopInfo' => $shopList->get($goods->shop_id) ?: null,
            ];
        });
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

    public function createGoods($shopId, GoodsInput $input)
    {
        $goods = Goods::new();
        $goods->shop_id = $shopId;
        return $this->updateGoods($goods, $input);
    }

    public function updateGoods(Goods $goods, GoodsInput $input)
    {
        if ($goods->status == 2) {
            $goods->status = 0;
            $goods->failure_reason = '';
        }
        if ($goods->shop_id == 0 && $goods->status == 0) {
            $goods->status = 1;
        }
        $goods->cover = $input->cover;
        $goods->video = $input->video ?: '';
        $goods->image_list = json_encode($input->imageList);
        $goods->detail_image_list = json_encode($input->detailImageList);
        $goods->default_spec_image = $input->defaultSpecImage;
        $goods->name = $input->name;
        $goods->introduction = $input->introduction ?: '';
        $goods->freight_template_id = $input->freightTemplateId;
        $goods->shop_category_id = $input->shopCategoryId;
        $goods->category_id = $input->categoryId;
        $goods->return_address_id = $input->returnAddressId ?: 0;
        $goods->price = $input->price;
        $goods->market_price = $input->marketPrice ?: 0;
        $goods->stock = $input->stock;
        $goods->sales_commission_rate = $input->salesCommissionRate ?: 0;
        $goods->promotion_commission_rate = $input->promotionCommissionRate ?: 0;
        $goods->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit ?: 0;
        $goods->spec_list = json_encode($input->specList);
        $goods->sku_list = json_encode($input->skuList);
        $goods->refund_status = $input->refundStatus;
        $goods->save();

        return $goods;
    }
}
