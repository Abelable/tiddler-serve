<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelCategoryService;
use App\Services\HotelService;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\SearchPageInput;
use PhpParser\Node\Expr\New_;

class HotelController extends Controller
{
    protected $except = ['categoryOptions', 'list', 'search', 'detail', 'options'];

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
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();
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

    private function handelList($hotelList)
    {
        return $hotelList->map(function (Hotel $hotel) {
            return [
                'id' => $hotel->id,
                'cover' => $hotel->cover,
                'name' => $hotel->name,
                'english_name' => $hotel->english_name,
                'price' => $hotel->price,
                'rate' => $hotel->rate,
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
        return $this->success($hotel);
    }

    public function options()
    {
        $hotelOptions = HotelService::getInstance()->getHotelOptions(['id', 'name']);
        return $this->success($hotelOptions);
    }
}
