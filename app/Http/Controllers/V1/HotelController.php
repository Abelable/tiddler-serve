<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelCategoryService;
use App\Services\HotelService;
use App\Utils\Inputs\HotelPageInput;
use App\Utils\Inputs\PageInput;

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
        /** @var HotelPageInput $input */
        $input = HotelPageInput::new();

        $columns = ['id', 'cover', 'name', 'english_name', 'grade', 'rate', 'longitude', 'latitude', 'address', 'feature_tag_list', 'price'];
        $page = HotelService::getInstance()->getHotelPage($input, $columns);
        $list = collect($page->items())->map(function (Hotel $hotel) {
            $hotel->feature_tag_list= json_decode($hotel->feature_tag_list);
            return $hotel;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = HotelService::getInstance()->search($keywords, $input);
        $list = collect($page->items())->map(function (Hotel $hotel) {
            return [
                'id' => $hotel->id,
                'cover' => $hotel->cover,
                'name' => $hotel->name,
                'price' => $hotel->price,
                'grade' => $hotel->grade,
                'rate' => $hotel->rate,
                'address' => $hotel->address,
                'featureTagList' => $hotel->feature_tag_list,
            ];
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
