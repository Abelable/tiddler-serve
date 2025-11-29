<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsCategoryService;
use App\Services\GoodsPickupAddressService;
use App\Services\GoodsService;
use App\Services\ShopManagerService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ShopGoodsController extends Controller
{
    public function categoryOptions()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $shopInfo = ShopService::getInstance()->getShopById($shopId);
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '店铺不存在');
        }

        $shopCategoryIds = json_decode($shopInfo->category_ids);
        $options = GoodsCategoryService::getInstance()
            ->getOptionsByShopCategoryIds(
                $shopCategoryIds,
                ['id', 'shop_category_id', 'name', 'min_sales_commission_rate', 'max_sales_commission_rate']
            );

        return $this->success($options);
    }

    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');
        return $this->success([
            GoodsService::getInstance()->getListTotal($shopId, 1),
            GoodsService::getInstance()->getListTotal($shopId, 3),
            GoodsService::getInstance()->getListTotal($shopId, 0),
            GoodsService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = [
            'id',
            'status',
            'failure_reason',
            'category_id',
            'cover',
            'name',
            'price',
            'sales_commission_rate',
            'sales_volume',
            'stock',
            'created_at',
            'updated_at'
        ];
        $page = GoodsService::getInstance()->getShopGoodsPage($shopId, $input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods['pickupAddressIds'] = $goods->pickupAddressIds();
        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        return $this->success($goods);
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'cover', 'name'];
        $page = GoodsService::getInstance()->getShopGoodsList($shopId, [1], $columns);
        return $this->successPaginate($page);
    }

    public function add()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无权限上传商品');
        }

        DB::transaction(function () use ($input, $shopId) {
            $goods = GoodsService::getInstance()->createGoods($shopId, $input);
            GoodsPickupAddressService::getInstance()->createList($goods->id, $input->pickupAddressIds ?: []);
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无权限编辑商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        DB::transaction(function () use ($goods, $input, $shopId) {
            GoodsService::getInstance()->updateGoods($goods, $input);
            GoodsPickupAddressService::getInstance()->createList($goods->id, $input->pickupAddressIds ?: []);
        });

        return $this->success();
    }

    public function up()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无法上架商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
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
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无法下架商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
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

    public function editCommission()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $salesCommissionRate = $this->verifyRequiredNumeric('salesCommissionRate');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无法编辑商品佣金');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->sales_commission_rate = $salesCommissionRate;
        $goods->save();

        return $this->success();
    }

    public function editStock()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $stock = $this->verifyRequiredInteger('stock');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无法编辑商品库存');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->stock = $stock;
        $goods->save();

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = ShopManagerService::getInstance()->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->shop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无法删除商品');
        }

        $goods = GoodsService::getInstance()->getShopGoods($shopId, $id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();

        return $this->success();
    }
}
