<?php

namespace App\Services;

use App\Models\Catering\Restaurant;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\RestaurantPageInput;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\RestaurantInput;
use Illuminate\Support\Facades\DB;

class RestaurantService extends BaseService
{
    public function getAdminRestaurantList(RestaurantPageInput $input, $columns=['*'])
    {
        $query = Restaurant::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRestaurantPage(CommonPageInput $input, $columns=['*'])
    {
        $query = Restaurant::query();
        if (!empty($input->productIds)) {
            $query = $query->orderByRaw(DB::raw("FIELD(id, " . implode(',', $input->productIds) . ") DESC"));
        }
        if (!empty($input->keywords)) {
            $query = $query->where('name', 'like', "%$input->keywords%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('views', 'desc')
                ->orderBy('sales_volume', 'desc')
                ->orderBy('score', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(CommonPageInput $input)
    {
        return Restaurant::search($input->keywords)
            ->orderBy('score', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function getRestaurantById($id, $columns=['*'])
    {
        $restaurant = Restaurant::query()->find($id, $columns);
        if (is_null($restaurant)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '餐馆不存在');
        }
        return $this->decodeRestaurantInfo($restaurant);
    }

    public function getRestaurantByProviderId($providerId, $columns=['*'])
    {
        $restaurant = Restaurant::query()->where('provider_id', $providerId)->first($columns);
        if (is_null($restaurant)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '餐馆不存在');
        }
        return $this->decodeRestaurantInfo($restaurant);
    }

    private function decodeRestaurantInfo(Restaurant $restaurant) {
        $restaurant->longitude = (float) $restaurant->longitude;
        $restaurant->latitude = (float) $restaurant->latitude;
        $restaurant->food_image_list = json_decode($restaurant->food_image_list);
        $restaurant->environment_image_list = json_decode($restaurant->environment_image_list);
        $restaurant->price_image_list = json_decode($restaurant->price_image_list);
        $restaurant->tel_list = json_decode($restaurant->tel_list);
        $restaurant->facility_list = json_decode($restaurant->facility_list);
        $restaurant->open_time_list = json_decode($restaurant->open_time_list);
        return $restaurant;
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return Restaurant::query()->whereIn('id', $ids)->get($columns);
    }

    public function getOptions($columns = ['*'])
    {
        return Restaurant::query()->orderBy('id', 'asc')->get($columns);
    }

    public function getList($columns = ['*'])
    {
        return Restaurant::query()->get($columns);
    }

    public function getUserOptions(array $ids, $columns = ['*'])
    {
        return Restaurant::query()->whereNotIn('id', $ids)->orderBy('id', 'asc')->get($columns);
    }

    public function getSelectableOptions($hotelIds, $keywords = '', $columns = ['*'])
    {
        $query = Restaurant::query()->whereNotIn('id', $hotelIds);
        if (!empty($keywords)) {
            $query = $query->where('name', 'like', '%' . $keywords . '%');
        }
        return $query->get($columns);
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
        $restaurant->tel_list = json_encode($input->telList);
        $restaurant->open_time_list = json_encode($input->openTimeList);
        $restaurant->facility_list = json_encode($input->facilityList);
        $restaurant->save();

        return $restaurant;
    }

    public function updateRestaurantAvgScore($restaurantId, $avgScore)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $restaurant->score = $avgScore;
        $restaurant->save();
        return $restaurant;
    }

    public function increaseSalesVolume($restaurantId, $num)
    {
        $restaurant = $this->getRestaurantById($restaurantId);
        $restaurant->sales_volume = $restaurant->sales_volume + $num;
        $restaurant->save();
        return $restaurant;
    }

    public function updateViews($id, $views)
    {
        return Restaurant::query()->where('id', $id)->update(['views' => $views]);
    }
}
