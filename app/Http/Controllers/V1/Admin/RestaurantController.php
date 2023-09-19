<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
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
            'status',
            'failure_reason',
            'name',
            'level',
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
        $restaurant->status = 1;

        $this->update($restaurant, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var RestaurantInput $input */
        $input = RestaurantInput::new();

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '门店不存在');
        }

        $this->update($restaurant, $input);

        return $this->success();
    }

    private function update($restaurant, RestaurantInput $input)
    {
        $restaurant->name = $input->name;
        $restaurant->level = $input->level;
        $restaurant->category_id = $input->categoryId;
        if (!empty($input->video)) {
            $restaurant->video = $input->video;
        }
        $restaurant->image_list = json_encode($input->imageList);
        $restaurant->latitude = $input->latitude;
        $restaurant->longitude = $input->longitude;
        $restaurant->address = $input->address;
        $restaurant->brief = $input->brief;
        $restaurant->open_time_list = json_encode($input->openTimeList);
        $restaurant->policy_list = json_encode($input->policyList);
        $restaurant->hotline_list = json_encode($input->hotlineList);
        $restaurant->project_list = json_encode($input->projectList);
        $restaurant->facility_list = json_encode($input->facilityList);
        $restaurant->tips_list = json_encode($input->tipsList);
        $restaurant->save();

        return $restaurant;
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '门店不存在');
        }
        $restaurant->delete();

        return $this->success();
    }

    public function options()
    {
        $options = RestaurantService::getInstance()->getRestaurantOptions(['id', 'name']);
        return $this->success($options);
    }
}
