<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SetMeal;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\SetMealService;
use App\Services\SetMealRestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\SetMealInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ShopSetMealController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            SetMealService::getInstance()->getListTotal($shopId, 1),
            SetMealService::getInstance()->getListTotal($shopId, 3),
            SetMealService::getInstance()->getListTotal($shopId, 0),
            SetMealService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $page = SetMealService::getInstance()->getSetMealListByStatus($shopId, $input);
        $setMealList = collect($page->items());
        $list = $setMealList->map(function (SetMeal $setMeal) {
            $setMeal['restaurantIds'] = $setMeal->restaurantIds();
            return $setMeal;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var SetMealInput $input */
        $input = SetMealInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shopManagerIds = CateringShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->cateringShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this
                ->fail(CodeResponse::FORBIDDEN, '您不是当前餐饮门店商家或管理员，无权限添加套餐');
        }

        DB::transaction(function () use ($shopId, $input) {
            $setMeal = SetMealService::getInstance()->createSetMeal($shopId, $input);
            SetMealRestaurantService::getInstance()->create($setMeal->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var SetMealInput $input */
        $input = SetMealInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getShopSetMeal($shopId, $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前套餐不存在');
        }

        DB::transaction(function () use ($input, $setMeal) {
            SetMealService::getInstance()->updateSetMeal($setMeal, $input);
            SetMealRestaurantService::getInstance()->update($setMeal->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function up()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getShopSetMeal($shopId, $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前套餐不存在');
        }

        if ($setMeal->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架套餐，无法上架');
        }
        $setMeal->status = 1;
        $setMeal->save();

        return $this->success();
    }

    public function down()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getShopSetMeal($shopId, $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前套餐不存在');
        }

        if ($setMeal->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中套餐，无法下架');
        }
        $setMeal->status = 3;
        $setMeal->save();

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getShopSetMeal($shopId, $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前套餐不存在');
        }

        DB::transaction(function () use ($shopId, $id) {
            SetMealService::getInstance()->deleteSetMeal($shopId, $id);
            SetMealRestaurantService::getInstance()->deleteBySetMealId($id);
        });

        return $this->success();
    }
}
