<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\GoodsCategoryService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsAddInput;
use App\Utils\Inputs\GoodsEditInput;
use App\Utils\Inputs\MerchantGoodsListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopGoodsListInput;

class GoodsController extends Controller
{
    protected $except = ['list', 'info'];

    public function list()
    {
        $input = PageInput::new();
        $columns = ['id', 'shop_id', 'image', 'name', 'price', 'market_price', 'sales_volume'];
        $list = GoodsService::getInstance()->getList($input, $columns);
        return $this->successPaginate($list);
    }

    public function shopGoodsList()
    {
        /** @var ShopGoodsListInput $input */
        $input = ShopGoodsListInput::new();
        $columns = ['id', 'image', 'name', 'price', 'market_price', 'sales_volume'];
        $list = GoodsService::getInstance()->getShopGoodsList($input, $columns);
        return $this->successPaginate($list);
    }

    public function merchantGoodsList()
    {
        /** @var MerchantGoodsListInput $input */
        $input = MerchantGoodsListInput::new();

        $columns = ['id', 'image', 'name', 'price', 'sales_volume', 'failure_reason', 'created_at', 'updated_at'];
        $list = GoodsService::getInstance()->getGoodsListByStatus($this->userId(), $input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $columns = [
            'id',
            'image',
            'video',
            'image_list',
            'detail_image_list',
            'default_spec_image',
            'name',
            'freight_template_id',
            'category_id',
            'return_address_id',
            'price',
            'market_price',
            'stock',
            'commission_rate',
            'spec_list',
            'sku_list'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        return $this->success($goods);
    }

    public function info()
    {
        $id = $this->verifyRequiredId('id');

        $columns = [
            'id',
            'shop_id',
            'video',
            'image_list',
            'default_spec_image',
            'name',
            'price',
            'market_price',
            'stock',
            'detail_image_list',
            'spec_list',
            'sku_list'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
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
        /** @var GoodsAddInput $input */
        $input = GoodsAddInput::new();

        $goods = Goods::new();
        $shopId = $this->user()->shop_id;
        if ($shopId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法上传商品');
        }
        $goods->shop_id = $shopId;
        $goods->user_id = $this->userId();
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
        if ($goods->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家商品，无法编辑');
        }
        if ($goods->status != 2) {
            return $this->fail(CodeResponse::FORBIDDEN, '非审核未通过商品，无法编辑');
        }

        $goods->status = 0;
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

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($goods->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家商品，无法上架该商品');
        }
        if ($goods->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架商品，无法上架');
        }
        $goods->status = 1;
        $goods->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($goods->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家商品，无法下架该商品');
        }
        if ($goods->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中商品，无法下架');
        }
        $goods->status = 2;
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
        if ($goods->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家商品，无法删除');
        }
        $goods->delete();

        return $this->success();
    }

    public function categoryOptions()
    {
        $options = GoodsCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }
}
