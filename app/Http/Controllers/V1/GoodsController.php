<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Goods;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\GoodsCategoryService;
use App\Services\GoodsPickupAddressService;
use App\Services\GoodsRefundAddressService;
use App\Services\GoodsService;
use App\Services\OrderGoodsService;
use App\Services\ProductHistoryService;
use App\Services\ShopManagerService;
use App\Services\ShopPickupAddressService;
use App\Services\ShopService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\GoodsInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\RecommendGoodsPageInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    protected $except = ['categoryOptions', 'list', 'search', 'detail', 'shopOnSaleGoodsList', 'getPickupAddressList'];

    public function categoryOptions()
    {
        $shopCategoryId = $this->verifyId('shopCategoryId');
        $options = GoodsCategoryService::getInstance()->getCategoryOptions($shopCategoryId, ['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->getAllList($input);
        $list = $this->supplementGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function getPurchasedList()
    {
        $goodsId = $this->verifyRequiredId('goodsId');
        $scene = $this->verifyRequiredInteger('scene');

        $columns = ['selected_sku_name', 'selected_sku_index', 'number'];
        $orderGoodsList = OrderGoodsService::getInstance()->getRecentlyUserListByGoodsIds($this->userId(), [$goodsId], $columns);
        $cartGoodsList = CartGoodsService::getInstance()->getListByGoodsId($this->userId(), $goodsId, $columns);
        $purchasedList = collect($orderGoodsList);
        if ($scene == 1) {
            $purchasedList = $purchasedList->merge(collect($cartGoodsList));
        }
        $list = $purchasedList->groupBy(function ($item) {
            return $item['selected_sku_name'] . '|' . $item['selected_sku_index'];
        })->map(function ($groupedItems) {
            return [
                'skuName' => $groupedItems->first()['selected_sku_name'],
                'skuIndex' => $groupedItems->first()['selected_sku_index'],
                'number' => $groupedItems->sum('number'),
            ];
        })->values()->toArray();

        return $this->success($list);
    }

    public function recommendList()
    {
        /** @var RecommendGoodsPageInput $input */
        $input = RecommendGoodsPageInput::new();
        $page = GoodsService::getInstance()->getRecommendGoodsList($input);
        $list = $this->supplementGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->search($keywords, $input);
        $list = $this->supplementGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    private function supplementGoodsList($page)
    {
        $goodsList = collect($page->items());
        $goodsIds = $goodsList->pluck('id')->toArray();
        $shopIds = $goodsList->pluck('shop_id')->toArray();

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'logo', 'name'])->keyBy('id');
        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');
        $giftGoodsIds = GiftGoodsService::getInstance()->getList()->pluck('goods_id')->toArray();

        return $goodsList->map(function (Goods $goods) use ($shopList, $groupedCouponList, $giftGoodsIds) {
            $shopInfo = $goods->shop_id != 0 ? $shopList->get($goods->shop_id) : null;
            $goods['shopInfo'] = $shopInfo;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;

            return $goods;
        });
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
        $addressId = $this->verifyId('addressId');

        $columns = [
            'id',
            'status',
            'shop_id',
            'shop_category_id',
            'freight_template_id',
            'cover',
            'video',
            'image_list',
            'default_spec_image',
            'detail_image_list',
            'name',
            'introduction',
            'price',
            'market_price',
            'sales_commission_rate',
            'promotion_commission_rate',
            'promotion_commission_upper_limit',
            'stock',
            'number_limit',
            'sales_volume',
            'spec_list',
            'sku_list',
            'refund_status',
            'delivery_mode',
            'views'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        if ($this->isLogin()) {
            $addressColumns = ['id', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
            if (is_null($addressId)) {
                /** @var Address $address */
                $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $addressColumns);
            } else {
                /** @var Address $address */
                $address = AddressService::getInstance()->getById($this->userId(), $addressId, $addressColumns);
            }
            $goods['addressInfo'] = $address;
        }

        $couponList = CouponService::getInstance()->getCouponListByGoodsId($goods->id);
        if ($this->isLogin()) {
            $receivedCouponIds = UserCouponService::getInstance()->getUserCouponList($this->userId())->pluck('coupon_id')->toArray();
            $usedCountList = UserCouponService::getInstance()
                ->getUsedCount($this->userId())
                ->keyBy('coupon_id')
                ->map(function($item) {
                    return $item->receive_count;
                });
            $couponList = $couponList->map(function (Coupon $coupon) use ($receivedCouponIds, $usedCountList) {
                if (in_array($coupon->id, $receivedCouponIds)) {
                    $coupon['isReceived'] = 1;
                } elseif ($coupon->receive_limit != 0 && $usedCountList->get($coupon->id) >= $coupon->receive_limit) {
                    $coupon['isUsed'] = 1;
                }
                return $coupon;
            });
        }
        $goods['couponList'] = $couponList;

        $giftGoods = GiftGoodsService::getInstance()->getGoodsByGoodsId($goods->id);
        if ($giftGoods) {
            $goods['isGift'] = 1;
            $goods['giftDuration'] = $giftGoods->duration;
        }

        if ($goods->shop_id != 0) {
            $shopInfo = ShopService::getInstance()->getShopById($goods->shop_id, ['id', 'type', 'logo', 'name']);
            if (is_null($shopInfo)) {
                return $this->fail(CodeResponse::NOT_FOUND, '店铺已下架，当前商品不存在');
            }
            $shopInfo['goods_list'] = GoodsService::getInstance()->getShopTopList($id, $goods->shop_id, 6, ['id', 'cover', 'name', 'price', 'sales_volume']);
            $goods['shop_info'] = $shopInfo;
        }
        unset($goods->shop_id);

        if ($goods->freight_template_id != 0) {
            $goods['freightTemplateInfo'] = $goods->freightTemplateInfo;
            unset($goods->freight_template_id);
        }

        // 购买用户列表
        $goods['customerList'] = OrderGoodsService::getInstance()->getLatestCustomerList($goods->id);

        if ($this->isLogin()) {
            DB::transaction(function () use ($goods) {
                $goods->increment('views');
                ProductHistoryService::getInstance()
                    ->createHistory($this->userId(), ProductType::GOODS, $goods->id);
            });
        }

        return $this->success($goods);
    }

    public function shopCategoryOptions()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $shopInfo = ShopService::getInstance()->getShopById($shopId);
        if (is_null($shopInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '店铺不存在');
        }

        $shopCategoryIds = json_decode($shopInfo->category_ids);
        $options = GoodsCategoryService::getInstance()->getOptionsByShopCategoryIds($shopCategoryIds);

        return $this->success($options);
    }

    public function shopGoodsListTotals()
    {
        $shopId = $this->verifyRequiredId('shopId');
        return $this->success([
            GoodsService::getInstance()->getListTotal($shopId, 1),
            GoodsService::getInstance()->getListTotal($shopId, 3),
            GoodsService::getInstance()->getListTotal($shopId, 0),
            GoodsService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function shopGoodsList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $columns = ['id', 'cover', 'name', 'price', 'sales_volume', 'failure_reason', 'created_at', 'updated_at'];
        $page = GoodsService::getInstance()->getShopGoodsPage($shopId, $input, $columns);
        return $this->successPaginate($page);
    }

    public function shopOnSaleGoodsList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];
        $list = GoodsService::getInstance()->getShopOnSaleGoodsList($shopId, $input, $columns);
        return $this->successPaginate($list);
    }

    public function goodsInfo()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->refundAddressIds = $goods->refundAddressIds();
        $goods->pickupAddressIds = $goods->pickupAddressIds();
        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        return $this->success($goods);
    }

    public function getPickupAddressList()
    {
        $cartGoodsId = $this->verifyRequiredId('cartGoodsId');
        $goodsId = CartGoodsService::getInstance()->getCartGoodsById($cartGoodsId)->goods_id;
        $pickupAddressIds = GoodsPickupAddressService::getInstance()->getListByGoodsId($goodsId)->pluck('pickup_address_id')->toArray();

        $columns = ['id', 'name', 'open_time_list', 'address_detail', 'longitude', 'latitude'];
        $pickupAddressList = ShopPickupAddressService::getInstance()->getListByIds($pickupAddressIds, $columns);

        return $this->success($pickupAddressList);
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
            GoodsRefundAddressService::getInstance()->createList($goods->id, $input->refundAddressIds ?: []);
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
        if ($goods->status == 0 || $goods->status == 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '当前状态下商品，无法编辑');
        }

        DB::transaction(function () use ($goods, $input, $shopId) {
            GoodsService::getInstance()->updateGoods($goods, $input);
            GoodsPickupAddressService::getInstance()->createList($goods->id, $input->pickupAddressIds ?: []);
            GoodsRefundAddressService::getInstance()->createList($goods->id, $input->refundAddressIds ?: []);
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
