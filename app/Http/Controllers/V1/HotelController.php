<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelCategoryService;
use App\Services\HotelEvaluationService;
use App\Services\HotelQuestionService;
use App\Services\HotelService;
use App\Services\ProductHistoryService;
use App\Services\ShopHotelService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\HotelInput;
use App\Utils\Inputs\NearbyPageInput;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    protected $only = ['add', 'edit', 'delete', 'shopOptions'];

    public function categoryOptions()
    {
        $options = HotelCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = HotelService::getInstance()->getHotelPage($input);
        $list = HotelService::getInstance()->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = HotelService::getInstance()->search($input);
        $list = HotelService::getInstance()->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = HotelService::getInstance()->getNearbyPage($input);
        $list = HotelService::getInstance()->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function mediaRelativeList()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();

        if ($input->keywords) {
            $page = HotelService::getInstance()->search($input);
        } else {
            $page = HotelService::getInstance()->getHotelPage($input);
        }
        $list = HotelService::getInstance()->handelList(collect($page->items()));

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }

        $hotel['evaluationSummary'] = HotelEvaluationService::getInstance()->evaluationSummary($id, 2);
        $hotel['qaSummary'] = HotelQuestionService::getInstance()->qaSummary($id, 3);
        $hotel['nearbyScenicSummary'] = ScenicService::getInstance()
            ->nearbySummary($hotel->longitude, $hotel->latitude, 10, $id);
        $hotel['nearbyHotelSummary'] = HotelService::getInstance()
            ->nearbySummary($hotel->longitude, $hotel->latitude, 10);

        if ($this->isLogin()) {
            DB::transaction(function () use ($hotel) {
                $hotel->increment('views');
                ProductHistoryService::getInstance()
                    ->createHistory($this->userId(), ProductType::HOTEL, $hotel->id);
            });
        }

        return $this->success($hotel);
    }

    public function options()
    {
        $hotelOptions = HotelService::getInstance()->getHotelOptions(['id', 'name', 'cover']);
        return $this->success($hotelOptions);
    }

    public function add()
    {
        /** @var HotelInput $input */
        $input = HotelInput::new();

        $hotel = HotelService::getInstance()->getHotelByName($input->name);
        if (!is_null($hotel)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已存在相同名称酒店');
        }

        HotelService::getInstance()->createHotel($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var HotelInput $input */
        $input = HotelInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopHotel = ShopHotelService::getInstance()->getByHotelId($shopId, $id);
        if (is_null($shopHotel)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂未改酒店编辑权限');
        }

        $hotel = HotelService::getInstance()->getHotelById($id);
        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopHotel = ShopHotelService::getInstance()->getByHotelId($shopId, $id);
        if (is_null($shopHotel)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家酒店，不可删除');
        }

        $restaurant = HotelService::getInstance()->getHotelById($id);
        $restaurant->delete();

        return $this->success();
    }

    public function shopOptions()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $keywords = $this->verifyString('keywords');

        $hotelIds = ShopHotelService::getInstance()
            ->getShopHotelOptions($shopId)
            ->pluck('hotel_id')
            ->toArray();
        $options = HotelService::getInstance()
            ->getSelectableOptions($hotelIds, $keywords, ['id', 'name']);

        return $this->success($options);
    }
}
