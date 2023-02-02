<?php

namespace App\Services;

use App\Models\Goods;
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
}
