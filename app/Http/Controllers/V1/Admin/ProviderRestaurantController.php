<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringProvider;
use App\Models\ProviderRestaurant;
use App\Models\Restaurant;
use App\Services\CateringProviderService;
use App\Services\ProviderRestaurantService;
use App\Services\RestaurantService;
use App\Utils\Inputs\StatusPageInput;

class ProviderRestaurantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $page = ProviderRestaurantService::getInstance()->getList($input);
        $providerRestaurantList = collect($page->items());

        $providerIds = $providerRestaurantList->pluck('provider_id')->toArray();
        $providerList = CateringProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'company_name', 'business_license_photo'])->keyBy('id');

        $restaurantIds = $providerRestaurantList->pluck('restaurant_id')->toArray();
        $restaurantList = RestaurantService::getInstance()->getRestaurantListByIds($restaurantIds, ['id', 'name', 'cover'])->keyBy('id');

        $list = $providerRestaurantList->map(function (ProviderRestaurant $providerRestaurant) use ($restaurantList, $providerList) {
            /** @var CateringProvider $provider */
            $provider = $providerList->get($providerRestaurant->provider_id);
            $providerRestaurant['provider_company_name'] = $provider->company_name;
            $providerRestaurant['provider_business_license_photo'] = $provider->business_license_photo;

            /** @var Restaurant $restaurant */
            $restaurant = $restaurantList->get($providerRestaurant->restaurant_id);
            $providerRestaurant['restaurant_name'] = $restaurant->name;
            $providerRestaurant['restaurant_image'] = $restaurant->cover;

            return $providerRestaurant;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approvedApply()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = ProviderRestaurantService::getInstance()->getRestaurant($id);
        $restaurant->status = 1;
        $restaurant->save();
        return $this->success();
    }

    public function rejectApply()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');
        $restaurant = ProviderRestaurantService::getInstance()->getRestaurant($id);
        $restaurant->status = 2;
        $restaurant->failure_reason = $reason;
        $restaurant->save();
        return $this->success();
    }

    public function deleteApply()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = ProviderRestaurantService::getInstance()->getRestaurant($id);
        $restaurant->delete();
        return $this->success();
    }
}
