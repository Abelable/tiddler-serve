<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelCategoryService;
use App\Services\HotelService;
use App\Utils\Inputs\AllListInput;

class HotelController extends Controller
{
    protected $except = ['categoryOptions', 'list', 'detail', 'options'];

    public function categoryOptions()
    {
        $options = HotelCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var AllListInput $input */
        $input = AllListInput::new();

        $columns = ['id', 'cover', 'name', 'english_name', 'grade', 'rate', 'longitude', 'latitude', 'address', 'feature_tag_list', 'price'];
        $page = HotelService::getInstance()->getAllList($input, $columns);
        $hotelList = collect($page->items());
        $list = $hotelList->map(function (Hotel $hotel) {
            $hotel->feature_tag_list= json_decode($hotel->feature_tag_list);
            return $hotel;
        });

        return $this->success($this->paginate($page, $list));
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
