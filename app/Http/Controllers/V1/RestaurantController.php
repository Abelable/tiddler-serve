<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\RestaurantCategoryService;
use App\Services\RestaurantService;
use App\Utils\Inputs\AllListInput;

class RestaurantController extends Controller
{
    protected $only = [];

    public function categoryOptions()
    {
        $options = RestaurantCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var AllListInput $input */
        $input = AllListInput::new();

        $page = RestaurantService::getInstance()->getAllList($input);
        $restaurantList = collect($page->items());
        $list = $restaurantList->map(function (Restaurant $restaurant) {
            $restaurant->food_image_list= json_decode($restaurant->food_image_list);
            $restaurant->environment_image_list= json_decode($restaurant->environment_image_list);
            $restaurant->price_image_list= json_decode($restaurant->price_image_list);
            $restaurant->facility_list= json_decode($restaurant->facility_list);
            $restaurant->open_time_list= json_decode($restaurant->open_time_list);

            return $restaurant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        return $this->success($restaurant);
    }

    public function options()
    {
        $restaurantOptions = RestaurantService::getInstance()->getRestaurantOptions(['id', 'name']);
        return $this->success($restaurantOptions);
    }
}
