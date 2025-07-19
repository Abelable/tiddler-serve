<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catering\SetMeal;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\SetMealService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\SetMealListInput;

class SetMealController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var SetMealListInput $input */
        $input = SetMealListInput::new();
        $page = SetMealService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (SetMeal $setMeal) {
            $setMeal['restaurantIds'] = $setMeal->restaurantIds();
            return $setMeal;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal['restaurantIds'] = $setMeal->restaurantIds();

        $shop = CateringShopService::getInstance()->getShopById($setMeal->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家店铺不存在');
        }
        $merchant = CateringMerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }
        $setMeal['shop_info'] = $shop;
        $setMeal['merchant_info'] = $merchant;
        unset($shop->merchant_id);
        unset($setMeal->shop_id);

        return $this->success($setMeal);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal->status = 1;
        $setMeal->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal->status = 2;
        $setMeal->failure_reason = $reason;
        $setMeal->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal->delete();

        return $this->success();
    }
}
