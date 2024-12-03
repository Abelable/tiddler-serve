<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsCategoryService;
use App\Services\GoodsService;
use App\Services\KeywordService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\StatusPageInput;

class GoodsController extends Controller
{
    protected $except = ['categoryOptions', 'list', 'search', 'detail', 'shopGoodsList'];

    public function categoryOptions()
    {
        $shopCategoryId = $this->verifyRequiredId('shopCategoryId');
        $options = GoodsCategoryService::getInstance()->getOptionsByShopCategoryId($shopCategoryId, ['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();

        $page = GoodsService::getInstance()->getAllList($input);
        $goodsList = collect($page->items());
        $list = GoodsService::getInstance()->addShopInfoToGoodsList($goodsList);

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->search($keywords, $input);
        $goodsList = collect($page->items());
        $list = GoodsService::getInstance()->addShopInfoToGoodsList($goodsList);

        return $this->success($this->paginate($page, $list));
    }

    public function mediaRelativeList()
    {
        $keywords = $this->verifyString('keywords');

        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();

        if (!empty($keywords)) {
            $page = GoodsService::getInstance()->search($keywords, $input);
        } else {
            $page = GoodsService::getInstance()->getAllList($input);
        }

        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $columns = [
            'id',
            'shop_id',
            'category_id',
            'cover',
            'video',
            'image_list',
            'default_spec_image',
            'name',
            'price',
            'market_price',
            'stock',
            'sales_volume',
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

        $goods['recommend_goods_list'] = GoodsService::getInstance()->getRecommendGoodsList([$id], [$goods->category_id]);
        unset($goods->category_id);

        if ($goods->shop_id != 0) {
            $shopInfo = ShopService::getInstance()->getShopById($goods->shop_id, ['id', 'type', 'avatar', 'name']);
            if (is_null($shopInfo)) {
                return $this->fail(CodeResponse::NOT_FOUND, '店铺已下架，当前商品不存在');
            }
            $shopInfo['goods_list'] = GoodsService::getInstance()->getShopTopList($id, $goods->shop_id, 6, ['id', 'cover', 'name', 'price']);
            $goods['shop_info'] = $shopInfo;
        }
        unset($goods->shop_id);

        return $this->success($goods);
    }

    public function shopCategoryOptions()
    {
        $shopCategoryIds = json_decode($this->user()->shopInfo->category_ids);
        $options = GoodsCategoryService::getInstance()->getOptionsByShopCategoryIds($shopCategoryIds);
        return $this->success($options);
    }

    public function goodsListTotals()
    {
        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法查看商家商品');
        }

        return $this->success([
            GoodsService::getInstance()->getListTotal($shopInfo->id, 1),
            GoodsService::getInstance()->getListTotal($shopInfo->id, 3),
            GoodsService::getInstance()->getListTotal($shopInfo->id, 0),
            GoodsService::getInstance()->getListTotal($shopInfo->id, 2),
        ]);
    }

    public function ownerGoodsList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $columns = ['id', 'cover', 'name', 'price', 'sales_volume', 'failure_reason', 'created_at', 'updated_at'];

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法查看商家商品');
        }

        $list = GoodsService::getInstance()->getOwnerGoodsList($shopInfo->id, $input, $columns);
        return $this->successPaginate($list);
    }

    public function shopGoodsList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];
        $list = GoodsService::getInstance()->getShopGoodsList($shopId, $input, $columns);
        return $this->successPaginate($list);
    }

    public function goodsInfo()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        return $this->success($goods);
    }

    public function add()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法上传商品');
        }

        GoodsService::getInstance()->createGoods($this->userId(), $shopInfo->id, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var GoodsInput $input */
        $input = GoodsInput::new();

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法编辑商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopInfo->id, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($goods->status == 0 || $goods->status == 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '当前状态下商品，无法编辑');
        }

        GoodsService::getInstance()->updateGoods($goods, $input);

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法上架商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopInfo->id, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
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

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法下架商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopInfo->id, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($goods->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中商品，无法下架');
        }
        $goods->status = 3;
        $goods->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $shopInfo = $this->user()->shopInfo;
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法删除商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopInfo->id, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();

        return $this->success();
    }
}
