<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelCategoryService;
use App\Services\HotelService;
use App\Services\ProductHistoryService;
use App\Services\ProviderHotelService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\HotelInput;
use App\Utils\Inputs\NearbyPageInput;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    protected $only = ['mediaRelativeList', 'add', 'edit', 'providerOptions'];

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
        $list = $this->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = HotelService::getInstance()->search($input);
        $list = $this->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = HotelService::getInstance()->getNearbyList($input);
        $list = $this->handelList(collect($page->items()));
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
        $list = $this->handelList(collect($page->items()));

        return $this->success($this->paginate($page, $list));
    }

    private function handelList($hotelList)
    {
        return $hotelList->map(function (Hotel $hotel) {
            return [
                'id' => $hotel->id,
                'cover' => $hotel->cover,
                'name' => $hotel->name,
                'englishName' => $hotel->english_name,
                'price' => $hotel->price,
                'score' => $hotel->score,
                'grade' => $hotel->grade,
                'longitude' => $hotel->longitude,
                'latitude' => $hotel->latitude,
                'address' => $hotel->address,
                'featureTagList' => json_decode($hotel->feature_tag_list),
            ];
        });
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }

        if ($this->isLogin()) {
            DB::transaction(function () use ($hotel) {
                HotelService::getInstance()->updateViews($hotel);
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

        $hotel = Hotel::new();
        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var HotelInput $input */
        $input = HotelInput::new();

        $providerHotel = ProviderHotelService::getInstance()->getHotelByHotelId($this->userId(), $id);
        if (is_null($providerHotel)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂未改酒店编辑权限');
        }

        $hotel = HotelService::getInstance()->getHotelById($id);
        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function providerOptions()
    {
        $providerHotelIds = ProviderHotelService::getInstance()
            ->getUserHotelOptions($this->userId())
            ->pluck('hotel_id')
            ->toArray();
        $options = HotelService::getInstance()->getProviderHotelOptions($providerHotelIds, ['id', 'name']);
        return $this->success($options);
    }

    public function addSales()
    {
        $list = HotelService::getInstance()->getList();
        /** @var Hotel $hotel */
        foreach ($list as $hotel) {
            if ($hotel->sales_volume == 0) {
                $hotel->sales_volume = mt_rand(20, 100);
                $hotel->save();
            }
        }
        return $this->success();
    }
}
