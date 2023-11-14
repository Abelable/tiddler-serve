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
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();
        $page = HotelService::getInstance()->search($input);
        $list = collect($page->items())->map(function (Hotel $hotel) {
            return [
                'id' => $hotel->id,
                'cover' => $hotel->cover,
                'name' => $hotel->name,
                'price' => $hotel->price,
                'grade' => $hotel->grade,
                'rate' => $hotel->rate,
                'longitude' => $hotel->longitude,
                'latitude' => $hotel->latitude,
                'address' => $hotel->address,
                'featureTagList' => json_decode($hotel->feature_tag_list),
            ];
        });
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $columns = ['id', 'cover', 'name', 'price', 'rate', 'grade', 'longitude', 'latitude', 'feature_tag_list'];
        $page = HotelService::getInstance()->getNearbyList($input, $columns);
        $list = collect($page->items())->map(function (Hotel $hotel) {
            $hotel->feature_tag_list = json_decode($hotel->feature_tag_list);
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
