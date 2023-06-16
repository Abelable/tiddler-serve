<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsInput;

class GoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $input = GoodsListInput::new();
        $columns = [
            'id',
            'image',
            'name',
            'category_id',
            'price',
            'stock',
            'sales_commission_rate',
            'promotion_commission_rate',
            'sales_volume',
            'status',
            'failure_reason',
            'created_at',
            'updated_at'
        ];
        $list = GoodsService::getInstance()->getMerchantGoodsList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $goodsColumns = [
            'id',
            'shop_id',
            'image',
            'name',
            'category_id',
            'price',
            'stock',
            'promotion_commission_rate',
            'sales_volume',
            'status',
            'failure_reason',
            'created_at',
            'updated_at'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $goodsColumns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $shopColumns = [
            'id',
            'merchant_id',
            'avatar',
            'name',
            'category_id',
            'created_at',
            'updated_at'
        ];
        $shop = ShopService::getInstance()->getShopById($goods->shop_id, $shopColumns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        $merchantColumns = [
            'id',
            'type',
            'name',
            'mobile',
            'created_at',
            'updated_at'
        ];
        $merchant = MerchantService::getInstance()->getMerchantById($shop->merchant_id, $merchantColumns);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        unset($shop->merchant_id);
        unset($goods->shop_id);
        $goods['shop_info'] = $shop;
        $goods['merchant_info'] = $merchant;

        return $this->success($goods);
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->status = 1;
        $goods->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->status = 2;
        $goods->failure_reason = $reason;
        $goods->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();

        return $this->success();
    }

    public function ownerList()
    {
        $input = GoodsListInput::new();
        $columns = ['id', 'image', 'name', 'category_id', 'price', 'stock', 'commission_rate', 'sales_volume', 'status', 'created_at', 'updated_at'];
        $list = GoodsService::getInstance()->getOwnerGoodsList($input, $columns);
        return $this->successPaginate($list);
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->status = 3;
        $goods->save();

        return $this->success();
    }

    public function ownerDetail()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        return $this->success($goods);
    }

    public function add()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();
        GoodsService::getInstance()->createGoods(0, 0, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var GoodsInput $input */
        $input = GoodsInput::new();

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        GoodsService::getInstance()->updateGoods($goods, $input);

        return $this->success();
    }
}
