<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoodsPickupAddressService;
use App\Services\GoodsRefundAddressService;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsApproveInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\GoodsInput;
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

            unset($shop->merchant_id);
            unset($goods->shop_id);
            $goods['shop_info'] = $shop;
            $goods['merchant_info'] = $merchant;
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->sku_list = json_decode($goods->sku_list);
        $goods->spec_list = json_decode($goods->spec_list);

        $goods['pickupAddressIds'] = $goods->pickupAddressIds();
        $goods['refundAddressIds'] = $goods->refundAddressIds();

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
            if (!empty($input->refundAddressIds)) {
                GoodsRefundAddressService::getInstance()->createList($goods->id, $input->refundAddressIds);
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
            GoodsRefundAddressService::getInstance()->createList($goods->id, $input->refundAddressIds);
        });

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

        $goods->views = $views;
        $goods->save();

        return $this->success();
    }

    public function approve()
    {
        /** @var GoodsApproveInput $input */
        $input = GoodsApproveInput::new();

        $goods = GoodsService::getInstance()->getGoodsById($input->id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->status = 1;
        $goods->promotion_commission_rate = $input->promotionCommissionRate;
        $goods->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        $goods->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        $goods->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
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
}
