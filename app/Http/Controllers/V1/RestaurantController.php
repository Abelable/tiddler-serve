<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\Restaurant;
use App\Services\ProductHistoryService;
use App\Services\RestaurantCategoryService;
use App\Services\RestaurantService;
use App\Services\ShopRestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\RestaurantInput;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    protected $only = ['add', 'edit', 'delete', 'shopOptions'];

    public function categoryOptions()
    {
        $options = RestaurantCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = RestaurantService::getInstance()->getRestaurantPage($input);
        $list = RestaurantService::getInstance()->handleList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = RestaurantService::getInstance()->search($input);
        $list = RestaurantService::getInstance()->handleList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = RestaurantService::getInstance()->getNearbyPage($input);
        $list = RestaurantService::getInstance()->handleList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function mediaRelativeList()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();

        if ($input->keywords) {
            $page = RestaurantService::getInstance()->search($input);
        } else {
            $page = RestaurantService::getInstance()->getRestaurantPage($input);
        }

        $list = collect($page->items())->map(function (Restaurant $restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'cover' => $restaurant->cover,
                'price' => $restaurant->price,
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮门店不存在');
        }

        $restaurant = RestaurantService::getInstance()->decodeRestaurantInfo($restaurant);

        if ($this->isLogin()) {
            DB::transaction(function () use ($restaurant) {
                $restaurant->increment('views');
                ProductHistoryService::getInstance()
                    ->createHistory($this->userId(), ProductType::RESTAURANT, $restaurant->id);
            });
        }

        return $this->success($restaurant);
    }

    public function options()
    {
        $restaurantOptions = RestaurantService::getInstance()->getOptions(['id', 'name', 'cover']);
        return $this->success($restaurantOptions);
    }

    public function add()
    {
        /** @var RestaurantInput $input */
        $input = RestaurantInput::new();

        RestaurantService::getInstance()->createRestaurant($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var RestaurantInput $input */
        $input = RestaurantInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopRestaurant = ShopRestaurantService::getInstance()
            ->getByRestaurantId($shopId, $id);
        if (is_null($shopRestaurant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家餐饮门店，不可编辑');
        }

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        RestaurantService::getInstance()->updateRestaurant($restaurant, $input);

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopRestaurant = ShopRestaurantService::getInstance()
            ->getByRestaurantId($shopId, $id);
        if (is_null($shopRestaurant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家餐饮门店，不可删除');
        }

        $restaurant = RestaurantService::getInstance()->getRestaurantById($id);
        $restaurant->delete();

        return $this->success();
    }

    public function shopOptions()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $keywords = $this->verifyString('keywords');

        $restaurantIds = ShopRestaurantService::getInstance()
            ->getShopRestaurantOptions($shopId)
            ->pluck('restaurant_id')
            ->toArray();
        $restaurantOptions = RestaurantService::getInstance()
            ->getSelectableOptions($restaurantIds, $keywords, ['id', 'name']);

        return $this->success($restaurantOptions);
    }
}
