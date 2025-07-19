<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SetMeal;
use App\Services\SetMealService;
use App\Services\SetMealRestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\SetMealInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class SetMealController extends Controller
{
    protected $except = ['listByScenicId'];

    public function listByRestaurantId()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');

        $setMealIds = SetMealRestaurantService::getInstance()->getListByRestaurantId($restaurantId)->pluck('set_meal_id')->toArray();
        $setMealList = SetMealService::getInstance()->getListByIds($setMealIds, [
            'cover',
            'name',
            'price',
            'original_price',
            'sales_volume',
            'package_details',
            'validity_days',
            'validity_start_time',
            'validity_end_time',
            'buy_limit',
            'per_table_usage_limit',
            'use_time_list',
            'need_pre_book',
            'use_rules'
        ]);

        $setMealList = $setMealList->map(function (SetMeal $setMeal) {
            $setMeal->package_details = json_decode($setMeal->package_details);
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];
            return $setMeal;
        });

        return $this->success($setMealList);
    }

    public function listTotals()
    {
        return $this->success([
            SetMealService::getInstance()->getListTotal($this->userId(), 1),
            SetMealService::getInstance()->getListTotal($this->userId(), 3),
            SetMealService::getInstance()->getListTotal($this->userId(), 0),
            SetMealService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function userList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = SetMealService::getInstance()->getSetMealListByStatus($this->userId(), $input);
        $setMealList = collect($page->items());
        $list = $setMealList->map(function (SetMeal $setMeal) {
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
        $setMeal->package_details = json_decode($setMeal->package_details);
        $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
        $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];

        return $this->success($setMeal);
    }

    public function add()
    {
        /** @var SetMealInput $input */
        $input = SetMealInput::new();

        $providerId = $this->user()->cateringProvider->id;
        if ($providerId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是服务商，无法上传餐饮套餐');
        }

        DB::transaction(function () use ($providerId, $input) {
            $setMeal = SetMealService::getInstance()->createSetMeal($this->userId(), $providerId, $input);
            SetMealRestaurantService::getInstance()->create($setMeal->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var SetMealInput $input */
        $input = SetMealInput::new();

        $setMeal = SetMealService::getInstance()->getUserSetMeal($this->userId(), $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }

        DB::transaction(function () use ($input, $setMeal) {
            SetMealService::getInstance()->updateSetMeal($setMeal, $input);
            SetMealRestaurantService::getInstance()->update($setMeal->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getUserSetMeal($this->userId(), $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        if ($setMeal->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架餐饮套餐，无法上架');
        }
        $setMeal->status = 1;
        $setMeal->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getUserSetMeal($this->userId(), $id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        if ($setMeal->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中餐饮套餐，无法下架');
        }
        $setMeal->status = 3;
        $setMeal->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        DB::transaction(function () use ($id) {
            SetMealService::getInstance()->deleteSetMeal($this->userId(), $id);
            SetMealRestaurantService::getInstance()->deleteBySetMealId($id);
        });

        return $this->success();
    }
}
