<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\MealTicket;
use App\Models\Mall\Catering\Restaurant;
use App\Models\Mall\Catering\RestaurantCategory;
use App\Models\Mall\Catering\SetMeal;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\RestaurantPageInput;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
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
        if ($input->sort != 'created_at') {
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

    public function getNearbyPage(NearbyPageInput $input, $columns = ['*'])
    {
        $query = Restaurant::query();
        if (!empty($input->id)) {
            $query = $query->where('id', '!=', $input->id);
        }
        $query = $query
            ->select(
                '*',
                DB::raw(
                    '(6371 * acos(cos(radians(' . $input->latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $input->longitude . ')) + sin(radians(' . $input->latitude . ')) * sin(radians(latitude)))) AS distance'
                )
            );
        if ($input->radius != 0) {
            $query = $query->having('distance', '<=', $input->radius);
        }
        return $query
            ->orderBy('distance')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTopList($count, $columns = ['*'])
    {
        return Restaurant::query()
            ->orderBy('views', 'desc')
            ->orderBy('sales_volume', 'desc')
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get($columns);
    }

    public function handleList($restaurantList)
    {
        $categoryIds = $restaurantList->pluck('category_id')->toArray();
        $categoryList = RestaurantCategoryService::getInstance()->getListByIds($categoryIds)->keyBy('id');

        return $restaurantList->map(function (Restaurant $restaurant) use ($categoryList) {
            /** @var RestaurantCategory $category */
            $category = $categoryList->get($restaurant->category_id);
            $restaurant['categoryName'] = $category->name;
            unset($restaurant->category_id);

            $mealTicketList = MealTicketService::getInstance()
                ->getListByIds($restaurant->mealTicketIds(), ['price', 'original_price']);
            $restaurant['mealTicketList'] = $mealTicketList;

            $setMealList = SetMealService::getInstance()
                ->getListByIds($restaurant->setMealIds(), ['name', 'price', 'original_price']);
            $restaurant['setMealList'] = $setMealList;

            $restaurant->longitude = (float) $restaurant->longitude;
            $restaurant->latitude = (float) $restaurant->latitude;
            $restaurant->food_image_list = json_decode($restaurant->food_image_list);
            $restaurant->environment_image_list = json_decode($restaurant->environment_image_list);
            $restaurant->price_image_list = json_decode($restaurant->price_image_list);
            $restaurant->tel_list = json_decode($restaurant->tel_list);
            $restaurant->facility_list = json_decode($restaurant->facility_list);
            $restaurant->open_time_list = json_decode($restaurant->open_time_list);

            return $restaurant;
        });
    }

    public function getRestaurantById($id, $columns=['*'])
    {
        return Restaurant::query()->find($id, $columns);
    }

    public function getRestaurantByName($name, $columns = ['*'])
    {
        return Restaurant::search($name)->first();
    }

    public function decodeRestaurantInfo(Restaurant $restaurant) {
        $restaurant->longitude = (float) $restaurant->longitude;
        $restaurant->latitude = (float) $restaurant->latitude;
        $restaurant->food_image_list = json_decode($restaurant->food_image_list);
        $restaurant->environment_image_list = json_decode($restaurant->environment_image_list);
        $restaurant->price_image_list = json_decode($restaurant->price_image_list);
        $restaurant->tel_list = json_decode($restaurant->tel_list);
        $restaurant->facility_list = json_decode($restaurant->facility_list);
        $restaurant->open_time_list = json_decode($restaurant->open_time_list);

        $category = RestaurantCategoryService::getInstance()->getCategoryById($restaurant->category_id);
        $restaurant['categoryName'] = $category->name;

        $mealTicketList = MealTicketService::getInstance()->getListByIds($restaurant->mealTicketIds());
        $mealTicketList = $mealTicketList->map(function (MealTicket $ticket) {
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            return $ticket;
        });
        $restaurant['mealTicketList'] = $mealTicketList;

        $setMealList = SetMealService::getInstance()->getListByIds($restaurant->setMealIds());
        $setMealList = $setMealList->map(function (SetMeal $setMeal) {
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            return $setMeal;
        });
        $restaurant['setMealList'] = $setMealList;

        $restaurant['evaluationSummary'] = CateringEvaluationService::getInstance()
            ->evaluationSummary($restaurant->id, 2);
        $restaurant['qaSummary'] = CateringQuestionService::getInstance()->qaSummary($restaurant->id, 3);

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
