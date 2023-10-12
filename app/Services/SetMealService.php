<?php

namespace App\Services;

use App\Models\SetMeal;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\SetMealListInput;
use App\Utils\Inputs\SetMealInput;
use App\Utils\Inputs\StatusPageInput;

class SetMealService extends BaseService
{
    public function getList(SetMealListInput $input, $columns=['*'])
    {
        $query = SetMeal::query()->whereIn('status', [0, 1, 2]);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->restaurantId)) {
            $query = $query->whereIn('id', function ($subQuery) use ($input) {
                $subQuery->select('set_meal_id')
                    ->from('restaurant_set_meal')
                    ->where('restaurant_id', $input->restaurantId);
            });
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds(array $ids, $columns=['*'])
    {
        return SetMeal::query()->where('status', 1)->whereIn('id', $ids)->get($columns);
    }

    public function getListTotal($userId, $status)
    {
        return SetMeal::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getSetMealListByStatus($userId, StatusPageInput $input, $columns=['*'])
    {
        return SetMeal::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getSetMealById($id, $columns=['*'])
    {
        return SetMeal::query()->find($id, $columns);
    }

    public function getUserSetMeal($userId, $id, $columns=['*'])
    {
        return SetMeal::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createSetMeal($userId, $providerId, SetMealInput $input)
    {
        $setMeal = SetMeal::new();
        $setMeal->user_id = $userId;
        $setMeal->provider_id = $providerId;

        return $this->updateSetMeal($setMeal, $input);
    }

    public function updateSetMeal(SetMeal $setMeal, SetMealInput $input)
    {
        if ($setMeal->status == 2) {
            $setMeal->status = 0;
            $setMeal->failure_reason = '';
        }
        $setMeal->price = $input->price;
        $setMeal->original_price = $input->originalPrice;
        $setMeal->sales_commission_rate = $input->salesCommissionRate;
        $setMeal->promotion_commission_rate = $input->promotionCommissionRate;
        $setMeal->package_details = json_encode($input->packageDetails);
        $setMeal->validity_days = $input->validityDays ?: 0;
        $setMeal->validity_start_time = $input->validityStartTime ?: '';
        $setMeal->validity_end_time = $input->validityEndTime ?: '';
        $setMeal->buy_limit = $input->buyLimit ?: 0;
        $setMeal->per_table_usage_limit = $input->perTableUsageLimit ?: 0;
        $setMeal->use_time_list = json_encode($input->useTimeList);
        $setMeal->need_pre_book = $input->needPreBook;
        $setMeal->use_rules = json_encode($input->useRules);
        $setMeal->save();

        return $setMeal;
    }

    public function deleteSetMeal($userId, $id)
    {
        $setMeal = $this->getUserSetMeal($userId, $id);
        if (is_null($setMeal)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '套餐不存在');
        }
        $setMeal->delete();
    }
}
