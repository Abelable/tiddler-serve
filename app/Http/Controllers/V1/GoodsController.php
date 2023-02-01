<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;

class GoodsController extends Controller
{
    protected $except = ['list', 'info'];

    public function list()
    {
        $input = PageInput::new();
        $columns = ['id', 'name', 'video', 'image_list', 'price', 'market_price', 'sales_volume'];
        $list = GoodsService::getInstance()->getList($input, $columns);
        return $this->successPaginate($list);
    }

    public function merchantGoodsList()
    {
        /** @var MerchantGoodsListInput $input */
        $input = MerchantGoodsListInput::new();

        $columns = ['id', 'status', 'name', 'failure_reason', 'created_at'];
        $list = GoodsService::getInstance()->getGoodsListByStatus($this->userId(), $input, $columns);

        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'video', 'image_list', 'name', 'freight_template_id', 'category_id', 'return_address_id', 'price', 'market_price', 'stock', 'commission_rate', 'detail_image_list', 'spec_list'];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);

        return $this->success($goods);
    }

    public function info()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'shop_id', 'video', 'image_list', 'name', 'price', 'market_price', 'stock', 'detail_image_list', 'sku_list'];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->sku_list = json_decode($goods->sku_list);

        if ($goods->shop_id != 0) {
            $shopInfo = ShopService::getInstance()->getShopById($goods->shop_id, ['id', 'avatar', 'name']);
            if (is_null($shopInfo)) {
                return $this->fail(CodeResponse::NOT_FOUND, '店铺已下架，当前商品不存在');
            }
            $goods['shop_info'] = $shopInfo;
        }

        unset($goods->shop_id);

        return $this->success($goods);
    }

    public function add()
    {

    }
}
