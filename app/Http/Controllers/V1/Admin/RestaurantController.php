<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\RestaurantService;
use App\Utils\Inputs\Admin\RestaurantListInput;
use App\Utils\Inputs\RestaurantInput;

class RestaurantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var RestaurantListInput $input */
        $input = RestaurantListInput::new();
        $columns = [
            'id',
            'cover',
            'name',
            'category_id',
            'rate',
            'created_at',
            'updated_at'
        ];
        $list = RestaurantService::getInstance()->getRestaurantList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        return $this->success($restaurant);
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
        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        RestaurantService::getInstance()->updateRestaurant($restaurant, $input);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        $restaurant->delete();
        return $this->success();
    }

    public function options()
    {
        $options = RestaurantService::getInstance()->getOptions(['id', 'name']);
        return $this->success($options);
    }
}
