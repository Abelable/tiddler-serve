<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsAddInput;
use App\Utils\Inputs\GoodsEditInput;

class GoodsController extends Controller
{
    protected $guard = 'Admin';

    public function goodsList()
    {
        $input = GoodsListInput::new();
        $columns = ['id', 'image', 'name', 'category_id', 'price', 'stock', 'commission_rate', 'sales_volume', 'status', 'created_at', 'updated_at'];
        $list = GoodsService::getInstance()->getMerchantGoodsList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $shop = ShopService::getInstance()->getShopById($goods->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        $merchant = MerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        $goods['shop_info'] = $shop;
        $goods['merchant_info'] = $merchant;

        return $this->success($goods);
    }

    public function approved()
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

    public function ownerGoodsList()
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

    public function ownerGoodsDetail()
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
        /** @var GoodsAddInput $input */
        $input = GoodsAddInput::new();

        $goods = Goods::new();
        $goods->status = 1;
        $goods->image = $input->image;
        if (!empty($input->video)) {
            $goods->video = $input->video;
        }
        $goods->image_list = $input->imageList;
        $goods->detail_image_list = $input->detailImageList;
        $goods->default_spec_image = $input->defaultSpecImage;
        $goods->name = $input->name;
        $goods->freight_template_id = $input->freightTemplateId;
        $goods->category_id = $input->categoryId;
        $goods->return_address_id = $input->returnAddressId;
        $goods->price = $input->price;
        if (!empty($input->marketPrice)) {
            $goods->market_price = $input->marketPrice;
        }
        $goods->stock = $input->stock;
        $goods->commission_rate = $input->commissionRate;
        $goods->spec_list = $input->specList;
        $goods->sku_list = $input->skuList;
        $goods->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var GoodsEditInput $input */
        $input = GoodsEditInput::new();

        $goods = GoodsService::getInstance()->getGoodsById($input->id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image = $input->image;
        if (!empty($input->video)) {
            $goods->video = $input->video;
        }
        $goods->image_list = $input->imageList;
        $goods->detail_image_list = $input->detailImageList;
        $goods->default_spec_image = $input->defaultSpecImage;
        $goods->name = $input->name;
        $goods->freight_template_id = $input->freightTemplateId;
        $goods->category_id = $input->categoryId;
        $goods->return_address_id = $input->returnAddressId;
        $goods->price = $input->price;
        if (!empty($input->marketPrice)) {
            $goods->market_price = $input->marketPrice;
        }
        $goods->stock = $input->stock;
        $goods->commission_rate = $input->commissionRate;
        $goods->spec_list = $input->specList;
        $goods->sku_list = $input->skuList;
        $goods->save();

        return $this->success();
    }
}
