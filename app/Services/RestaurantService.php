<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\RestaurantListInput;
use App\Utils\Inputs\AllListInput;
use App\Utils\Inputs\RestaurantInput;

class RestaurantService extends BaseService
{
    public function getRestaurantList(RestaurantListInput $input, $columns=['*'])
    {
        $query = Restaurant::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->grade)) {
            $query = $query->where('grade', $input->grade);
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRestaurantById($id, $columns=['*'])
    {
        $restaurant = Restaurant::query()->find($id, $columns);
        if (is_null($restaurant)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '餐馆不存在');
        }
        $restaurant->food_image_list = json_decode($restaurant->food_image_list);
        $restaurant->environment_image_list = json_decode($restaurant->environment_image_list);
        $restaurant->price_image_list = json_decode($restaurant->price_image_list);
        $restaurant->facility_list = json_decode($restaurant->facility_list);
        $restaurant->open_time_list = json_decode($restaurant->open_time_list);
        return $restaurant;
    }

    public function getRestaurantByProviderId($providerId, $columns=['*'])
    {
        $restaurant = Restaurant::query()->where('provider_id', $providerId)->get($columns);
        if (is_null($restaurant)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '餐馆不存在');
        }
        $restaurant->food_image_list = json_decode($restaurant->food_image_list);
        $restaurant->environment_image_list = json_decode($restaurant->environment_image_list);
        $restaurant->price_image_list = json_decode($restaurant->price_image_list);
        $restaurant->facility_list = json_decode($restaurant->facility_list);
        $restaurant->open_time_list = json_decode($restaurant->open_time_list);
        return $restaurant;
    }

    public function getRestaurantListByIds(array $ids, $columns = ['*'])
    {
        return Restaurant::query()->whereIn('id', $ids)->get($columns);
    }

    public function getAllList(AllListInput $input, $columns=['*'])
    {
        $query = Restaurant::query()->where('status', 1);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('rate', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOptions($columns = ['*'])
    {
        return Restaurant::query()->orderBy('id', 'asc')->get($columns);
    }

    public function getUserOptions(array $ids, $columns = ['*'])
    {
        return Restaurant::query()->whereNotIn('id', $ids)->orderBy('id', 'asc')->get($columns);
    }

    public function createRestaurant(RestaurantInput $input) {
        $restaurant = Restaurant::new();
        return $this->updateRestaurant($restaurant, $input);
    }

    public function updateRestaurant(Restaurant $restaurant, RestaurantInput $input) {
        $restaurant->category_id = $input->categoryId;
        $restaurant->name = $input->name;
        $restaurant->price = $input->price;
        if (!empty($input->video)) {
            $restaurant->video = $input->video;
        }
        $restaurant->cover = $input->cover;
        $restaurant->food_image_list = json_encode($input->foodImageList);
        $restaurant->environment_image_list = json_encode($input->environmentImageList);
        $restaurant->price_image_list = json_encode($input->priceImageList);
        $restaurant->latitude = $input->latitude;
        $restaurant->longitude = $input->longitude;
        $restaurant->address = $input->address;
        $restaurant->tel_list = $input->telList;
        $restaurant->open_time_list = json_encode($input->openTimeList);
        $restaurant->facility_list = json_encode($input->facilityList);
        $restaurant->save();

        return $restaurant;
    }

    public function paySuccess(int $providerId)
    {
        $restaurant = $this->getRestaurantByProviderId($providerId);
        if (is_null($restaurant)) {
            $this->throwBadArgumentValue();
        }
        $restaurant->status = 1;
        $restaurant->save();
        return $restaurant;
    }
}
