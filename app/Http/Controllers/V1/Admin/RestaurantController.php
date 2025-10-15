<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catering\Restaurant;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\RestaurantPageInput;
use App\Utils\Inputs\RestaurantInput;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var RestaurantPageInput $input */
        $input = RestaurantPageInput::new();
        $list = RestaurantService::getInstance()->getAdminRestaurantList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐馆不存在');
        }

        $restaurant = RestaurantService::getInstance()->decodeRestaurantInfo($restaurant);

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
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐馆不存在');
        }

        RestaurantService::getInstance()->updateRestaurant($restaurant, $input);
        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        Cache::forget('product_list_cache');
        RestaurantService::getInstance()->updateViews($id, $views);

        return $this->success();
    }

    public function options()
    {
        $restaurantOptions = RestaurantService::getInstance()
            ->getOptions(['id', 'name', 'cover', 'tel_list', 'address', 'longitude', 'latitude']);
        $options = $restaurantOptions->map(function (Restaurant $restaurant) {
            $restaurant['tel'] = json_decode($restaurant['tel_list'], true)[0] ?? "";
            unset($restaurant['tel_list']);
            return $restaurant;
        });
        return $this->success($options);
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐馆不存在');
        }

        $restaurant->delete();
        return $this->success();
    }
}
