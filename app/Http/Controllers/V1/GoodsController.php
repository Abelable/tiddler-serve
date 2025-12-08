<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Coupon;
use App\Models\Mall\Goods\Address;
use App\Services\Mall\CouponService;
use App\Services\Mall\Goods\AddressService;
use App\Services\Mall\Goods\CartGoodsService;
use App\Services\Mall\Goods\GiftGoodsService;
use App\Services\Mall\Goods\GoodsCategoryService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\Goods\OrderGoodsService;
use App\Services\Mall\Goods\ShopManagerService;
use App\Services\Mall\Goods\ShopService;
use App\Services\Mall\ProductHistoryService;
use App\Services\Mall\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\RecommendGoodsPageInput;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    protected $only = [];

    public function categoryOptions()
    {
        $shopCategoryId = $this->verifyId('shopCategoryId');
        $options = GoodsCategoryService::getInstance()->getCategoryOptions([$shopCategoryId], ['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->getAllList($input);
        $list = GoodsService::getInstance()->handleList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->search($keywords, $input);
        $list = GoodsService::getInstance()->handleList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $addressId = $this->verifyId('addressId');

        $columns = [
            'id',
            'status',
            'shop_id',
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

        $goods = GoodsService::getInstance()->decodeGoodsInfo($goods);

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
            $shopInfo = ShopService::getInstance()
                ->getShopById($goods->shop_id, ['id', 'user_id', 'type', 'logo', 'name', 'owner_avatar', 'owner_name']);
            if (is_null($shopInfo)) {
                return $this->fail(CodeResponse::NOT_FOUND, '店铺已下架，当前商品不存在');
            }

            $shopInfo['managerList'] = ShopManagerService::getInstance()
                ->getManagerList($shopInfo->id, ['id', 'user_id', 'avatar', 'nickname', 'role_id']);

            $shopInfo['goodsList'] = GoodsService::getInstance()
                ->getShopTopList($id, $goods->shop_id, 6, ['id', 'cover', 'name', 'price', 'sales_volume']);

            $goods['shopInfo'] = $shopInfo;
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

    public function shopList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];

        $list = GoodsService::getInstance()->getShopOnSaleGoodsList($shopId, $input, $columns);

        return $this->successPaginate($list);
    }

    public function purchasedList()
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
        $list = GoodsService::getInstance()->handleList($page);
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
}
