<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\ProviderRestaurantService;
use App\Services\RestaurantCategoryService;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\AllListInput;
use App\Utils\Inputs\RestaurantInput;

class RestaurantController extends Controller
{
    protected $only = ['edit', 'delete', ];

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
        $restaurantOptions = RestaurantService::getInstance()->getOptions(['id', 'name']);
        return $this->success($restaurantOptions);
    }

    public function add()
    {
        /** @var RestaurantInput $input */
        $input = RestaurantInput::new();
        $restaurant = Restaurant::new();
        RestaurantService::getInstance()->updateRestaurant($restaurant, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var RestaurantInput $input */
        $input = RestaurantInput::new();

        $providerRestaurant = ProviderRestaurantService::getInstance()->getUserRestaurantByRestaurantId($this->userId(), $id);
        if (is_null($providerRestaurant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家门店，不可编辑');
        }

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        RestaurantService::getInstance()->updateRestaurant($restaurant, $input);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $providerRestaurant = ProviderRestaurantService::getInstance()->getUserRestaurantByRestaurantId($this->userId(), $id);
        if (is_null($providerRestaurant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家门店，不可删除');
        }

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        $restaurant->delete();

        return $this->success();
    }

    public function userOptions()
    {
        $restaurantIds = ProviderRestaurantService::getInstance()->getAllUserList($this->userId())->pluck('restaurant_id')->toArray();
        $restaurantOptions = RestaurantService::getInstance()->getUserOptions($restaurantIds, ['id', 'name']);
        return $this->success($restaurantOptions);
    }
}
