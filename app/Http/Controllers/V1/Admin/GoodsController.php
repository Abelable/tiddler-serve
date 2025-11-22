<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\GiftGoodsService;
use App\Services\GoodsPickupAddressService;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Services\ShopService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\GoodsInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $list = GoodsService::getInstance()->getAdminGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods = GoodsService::getInstance()->decodeGoodsInfo($goods);

        if ($goods->shop_id != 0) {
            $shopColumns = [
                'id',
                'merchant_id',
                'logo',
                'name',
                'category_ids',
                'created_at',
                'updated_at'
            ];
            $shop = ShopService::getInstance()->getShopById($goods->shop_id, $shopColumns);
            if (is_null($shop)) {
                return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
            }
            $shop->category_ids = json_decode($shop->category_ids);

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

            $goods['shop_info'] = $shop;
            $goods['merchant_info'] = $merchant;
            unset($shop->merchant_id);
            unset($goods->shop_id);
        }

        $goods['pickupAddressIds'] = $goods->pickupAddressIds();

        return $this->success($goods);
    }

    public function add()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();

        DB::transaction(function () use ($input) {
            $goods = GoodsService::getInstance()->createGoods(0, $input);

            if (!empty($input->pickupAddressIds)) {
                GoodsPickupAddressService::getInstance()->createList($goods->id, $input->pickupAddressIds);
            }
        });

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

        DB::transaction(function () use ($goods, $input) {
            GoodsService::getInstance()->updateGoods($goods, $input);
            GoodsPickupAddressService::getInstance()->createList($goods->id, $input->pickupAddressIds);
        });

        return $this->success();
    }

    public function editCommission()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        if ($input->promotionCommissionRate) {
            $goods->promotion_commission_rate = $input->promotionCommissionRate;
        }
        if ($input->promotionCommissionUpperLimit) {
            $goods->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        }
        if ($input->superiorPromotionCommissionRate) {
            $goods->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        }
        if ($input->superiorPromotionCommissionUpperLimit) {
            $goods->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        }
        $goods->save();

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        Cache::forget('product_list_cache');

        $goods->views = $views;
        $goods->save();

        return $this->success();
    }

    public function approve()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        DB::transaction(function () use ($goods, $input) {
            $goods->status = 1;
            $goods->promotion_commission_rate = $input->promotionCommissionRate;
            $goods->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
            $goods->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
            $goods->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
            $goods->save();

            // 邀请商家入驻活动
            $userTask = UserTaskService::getInstance()
                ->getByMerchantId(4, $goods->shopInfo->merchant_id, 2);
            if (!is_null($userTask)) {
                $userTask->step = 3;
                $userTask->save();
            }
        });

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

    public function options()
    {
        $options = GoodsService::getInstance()->getGoodsList(['id', 'name', 'cover']);
        return $this->success($options);
    }

    public function selfSupportGoodsOptions()
    {
        $options = GoodsService::getInstance()->getSelfGoodsList(['id', 'name', 'cover']);
        return $this->success($options);
    }

    public function normalGoodsOptions()
    {
        $giftGoodsIds = GiftGoodsService::getInstance()->getList()->pluck('goods_id')->toArray();
        $options = GoodsService::getInstance()->getFilterGoodsList($giftGoodsIds, ['id', 'name', 'cover']);
        return $this->success($options);
    }
}
