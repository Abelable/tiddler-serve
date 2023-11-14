<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MealTicket;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\SetMeal;
use App\Services\MealTicketService;
use App\Services\ProviderRestaurantService;
use App\Services\RestaurantCategoryService;
use App\Services\RestaurantService;
use App\Services\SetMealService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\RestaurantInput;

class RestaurantController extends Controller
{
    protected $only = ['edit', 'delete'];

    public function categoryOptions()
    {
        $options = RestaurantCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();

        $page = RestaurantService::getInstance()->getAllList($input);
        $restaurantList = collect($page->items());

        $categoryIds = $restaurantList->pluck('category_id')->toArray();
        $categoryList = RestaurantCategoryService::getInstance()->getListByIds($categoryIds)->keyBy('id');

        $list = $restaurantList->map(function (Restaurant $restaurant) use ($categoryList) {
            /** @var RestaurantCategory $category */
            $category = $categoryList->get($restaurant->category_id);
            $restaurant['categoryName'] = $category->name;
            unset($restaurant->category_id);

            $mealTicketList = MealTicketService::getInstance()->getListByIds($restaurant->ticketIds(), ['price', 'original_price']);
            $restaurant['mealTicketList'] = $mealTicketList;

            $setMealList = SetMealService::getInstance()->getListByIds($restaurant->setMealIds(), ['name', 'price', 'original_price']);
            $restaurant['setMealList'] = $setMealList;

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

        $category = RestaurantCategoryService::getInstance()->getCategoryById($restaurant->category_id);
        $restaurant['categoryName'] = $category->name;

        $columns = ['id', 'price', 'original_price', 'sales_volume', 'buy_limit', 'per_table_usage_limit', 'overlay_usage_limit', 'use_time_list', 'inapplicable_products', 'box_available', 'need_pre_book'];
        $mealTicketList = MealTicketService::getInstance()->getListByIds($restaurant->ticketIds(), $columns);
        $mealTicketList = $mealTicketList->map(function (MealTicket $ticket) {
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            return $ticket;
        });
        $restaurant['mealTicketList'] = $mealTicketList;

        $columns = ['id', 'cover', 'name', 'price', 'original_price', 'sales_volume', 'buy_limit', 'per_table_usage_limit', 'use_time_list', 'need_pre_book'];
        $setMealList = SetMealService::getInstance()->getListByIds($restaurant->setMealIds(), $columns);
        $setMealList = $setMealList->map(function (SetMeal $setMeal) {
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            return $setMeal;
        });
        $restaurant['setMealList'] = $setMealList;

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
        $restaurantIds = ProviderRestaurantService::getInstance()->getOptions($this->userId())->pluck('restaurant_id')->toArray();
        $restaurantOptions = RestaurantService::getInstance()->getUserOptions($restaurantIds, ['id', 'name']);
        return $this->success($restaurantOptions);
    }
}
